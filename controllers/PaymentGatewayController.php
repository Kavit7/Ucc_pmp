<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Bill;
use app\models\PaymentTransaction;
use app\models\Notification;
use app\components\FlutterwaveClient;
use app\components\Ledger;
use app\models\ListSource;
use yii\helpers\Html;
use RuntimeException;
use Throwable;

/**
 * Online rent payment via Flutterwave Standard Checkout. Never touches
 * bill.paid_date/bill_status directly except through confirmTransaction()
 * below, and only after independently verifying the transaction with
 * Flutterwave's API - a redirect query string alone is never trusted,
 * since anyone could craft one by hand.
 */
class PaymentGatewayController extends Controller
{
    public $layout = 'custom';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['pay'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $bill = Bill::findOne(Yii::$app->request->get('id'));
                            return $bill && (int) ($bill->lease->tenant_id ?? 0) === (int) Yii::$app->user->id;
                        },
                    ],
                    [
                        // Flutterwave's redirect back (browser, may or may not
                        // still be logged in) and their server-to-server
                        // webhook both land here - the transaction lookup
                        // itself is what's authoritative, not the session.
                        'allow' => true,
                        'actions' => ['callback', 'webhook'],
                        'roles' => ['?', '@'],
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['login/login']);
                    }
                    throw new \yii\web\ForbiddenHttpException('You do not have permission to do that.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'pay' => ['get'],
                    'callback' => ['get'],
                    'webhook' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id === 'webhook') {
            // Flutterwave's server has no session/CSRF token to send.
            Yii::$app->request->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Starts a Flutterwave Standard Checkout session for a pending bill
     * and redirects the tenant to Flutterwave's hosted payment page.
     */
    public function actionPay($id)
    {
        $bill = Bill::findOne($id);
        if (!$bill) {
            throw new NotFoundHttpException('Bill not found.');
        }

        if (!FlutterwaveClient::isConfigured()) {
            Yii::$app->session->setFlash('error', 'Online payment isn\'t set up yet. Please contact your property manager to pay this bill.');
            return $this->redirect(['custom/bill']);
        }

        if ($bill->paid_date) {
            Yii::$app->session->setFlash('success', 'This bill is already paid.');
            return $this->redirect(['custom/bill']);
        }

        $tenant = Yii::$app->user->identity;
        $txRef = 'UCCPMP-' . $bill->uuid . '-' . time();

        $transaction = new PaymentTransaction([
            'bill_id' => $bill->id,
            'tenant_id' => $tenant->user_id,
            'tx_ref' => $txRef,
            'amount' => $bill->amount,
            'currency' => 'TZS',
            'status' => 'pending',
        ]);
        if (!$transaction->save()) {
            Yii::$app->session->setFlash('error', 'Could not start the payment. Please try again.');
            return $this->redirect(['custom/bill']);
        }

        try {
            $client = FlutterwaveClient::fromParams();
            $link = $client->initiatePayment(
                $txRef,
                (float) $bill->amount,
                'TZS',
                Yii::$app->urlManager->createAbsoluteUrl(['payment-gateway/callback']),
                [
                    'email' => $tenant->email,
                    'name' => $tenant->full_name,
                    'phonenumber' => $tenant->phone ?: null,
                ],
                'Rent payment - ' . ($bill->lease->property->property_name ?? 'UCC PMP')
            );
        } catch (Throwable $e) {
            Yii::error('Flutterwave initiatePayment failed: ' . $e->getMessage(), __METHOD__);
            $transaction->status = 'failed';
            $transaction->gateway_response = $e->getMessage();
            $transaction->save(false);

            Yii::$app->session->setFlash('error', 'Could not connect to the payment provider. Please try again shortly.');
            return $this->redirect(['custom/bill']);
        }

        return $this->redirect($link);
    }

    /**
     * Flutterwave redirects the tenant's browser here after checkout.
     * This is a convenience for the UI only - the actual confirmation
     * comes from verifying with Flutterwave's API below, never from these
     * query params on their own.
     */
    public function actionCallback()
    {
        $flwTransactionId = Yii::$app->request->get('transaction_id');
        $txRef = Yii::$app->request->get('tx_ref');

        if (!$flwTransactionId) {
            Yii::$app->session->setFlash('error', 'Payment was not completed.');
            return $this->redirect(['custom/bill']);
        }

        try {
            $result = $this->confirmTransaction($flwTransactionId, $txRef);
            Yii::$app->session->setFlash($result ? 'success' : 'error', $result
                ? 'Payment received. Thank you.'
                : 'We could not confirm this payment. If you were charged, contact your property manager with reference ' . Html::encode($txRef));
        } catch (Throwable $e) {
            Yii::error('Flutterwave callback confirmation failed: ' . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'We could not confirm this payment right now. If you were charged, it will still be reconciled automatically shortly.');
        }

        return $this->redirect(['custom/bill']);
    }

    /**
     * Flutterwave's server-to-server webhook - the reliable confirmation
     * path (a tenant closing their browser after paying would otherwise
     * mean the redirect callback never fires). Verifies the signature
     * before touching anything.
     */
    public function actionWebhook()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $config = Yii::$app->params['flutterwave'] ?? [];
        $expectedHash = $config['webhook_hash'] ?? null;
        $sentHash = Yii::$app->request->headers->get('verif-hash');

        if (!$expectedHash || !$sentHash || !hash_equals($expectedHash, $sentHash)) {
            Yii::$app->response->statusCode = 401;
            return ['status' => 'error', 'message' => 'Invalid signature.'];
        }

        $payload = json_decode(Yii::$app->request->getRawBody(), true);
        $flwTransactionId = $payload['data']['id'] ?? null;
        if (!$flwTransactionId) {
            Yii::$app->response->statusCode = 400;
            return ['status' => 'error', 'message' => 'Missing transaction id.'];
        }

        try {
            $this->confirmTransaction($flwTransactionId);
        } catch (Throwable $e) {
            Yii::error('Flutterwave webhook confirmation failed: ' . $e->getMessage(), __METHOD__);
        }

        return ['status' => 'success'];
    }

    /**
     * Verifies a transaction directly with Flutterwave and, only if it
     * genuinely succeeded and the amount/currency match what we expected,
     * marks the payment_transaction and the underlying bill paid. Safe to
     * call twice for the same transaction (idempotent) since the redirect
     * and the webhook can both arrive for the same payment.
     */
    private function confirmTransaction($flwTransactionId, $expectedTxRef = null)
    {
        $client = FlutterwaveClient::fromParams();
        $data = $client->verifyTransaction($flwTransactionId);

        $txRef = $data['tx_ref'] ?? $expectedTxRef;
        $transaction = PaymentTransaction::findOne(['tx_ref' => $txRef]);
        if (!$transaction) {
            throw new RuntimeException("No local payment_transaction found for tx_ref \"{$txRef}\".");
        }

        if ($transaction->status === 'successful') {
            return true; // already processed - webhook and redirect both fired
        }

        $transaction->flw_transaction_id = (string) $flwTransactionId;
        $transaction->gateway_response = json_encode($data);

        $isGenuinelySuccessful = ($data['status'] ?? null) === 'successful'
            && ($data['currency'] ?? null) === $transaction->currency
            && (float) ($data['amount'] ?? 0) >= (float) $transaction->amount;

        if (!$isGenuinelySuccessful) {
            $transaction->status = 'failed';
            $transaction->save(false);
            return false;
        }

        $transaction->status = 'successful';
        $transaction->save(false);

        $bill = $transaction->bill;
        if ($bill && !$bill->paid_date) {
            $bill->paid_date = date('Y-m-d');
            $bill->bill_status = $this->billStatusId('Paid');

            if ($bill->save(false)) {
                $propertyName = $bill->lease->property->property_name ?? 'a property';
                $amountFmt = number_format($bill->amount, 2);

                Ledger::postSafely(
                    date('Y-m-d'),
                    "Rent collected online: {$propertyName}",
                    [
                        ['account' => '1000', 'debit' => $bill->amount],
                        ['account' => '1100', 'credit' => $bill->amount],
                    ],
                    'payment',
                    $bill->id
                );

                Notification::notify(
                    $bill->lease->tenant_id ?? null,
                    'Payment received',
                    "Your online payment of TZS {$amountFmt} for {$propertyName} was received.",
                    ['custom/payment']
                );
                Notification::notifyRoles(
                    ['admin', 'manager'],
                    'Online payment received',
                    "TZS {$amountFmt} paid online for {$propertyName}.",
                    ['custom/payment']
                );
            }
        }

        return true;
    }

    private function billStatusId($name)
    {
        $parentId = ListSource::find()->select('id')->where(['list_Name' => 'Bill Status'])->scalar();
        return ListSource::find()->where(['list_Name' => $name, 'parent_id' => $parentId])->select('id')->scalar();
    }
}
