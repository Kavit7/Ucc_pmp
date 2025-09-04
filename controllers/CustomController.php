<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\models\Lease;
use app\models\Bill;

class CustomController extends Controller
{
    public $layout = 'custom';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'logout', 'leases', 'create-lease', 'delete-lease', 'bill', 'payment'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    return Yii::$app->response->redirect(['login/index']);
                },
            ],
        ];
    }

    // Dashboard
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        return $this->render('index', ['user' => $user]);
    }

    // Logout
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login/index']);
    }

    // List leases
    public function actionLeases()
    {
        $leases = Lease::find()->with(['property', 'tenant', 'propertyPrice'])->all();
        return $this->render('leases', ['leases' => $leases]);
    }

    // Create lease
    public function actionCreateLease()
    {
        $lease = new Lease();

        if ($lease->load(Yii::$app->request->post())) {

            // handle file upload
            $lease->lease_doc_file = UploadedFile::getInstance($lease, 'lease_doc_file');
            if ($lease->uploadDocument()) {
                // file path is saved in lease_doc_url
            }

            if ($lease->save()) {
                // Create bill automatically
                $bill = new Bill();
                $bill->uuid = Yii::$app->security->generateRandomString(12);
                $bill->lease_id = $lease->id;
                $bill->amount = $lease->propertyPrice->unit_amount ?? 0;
                $bill->due_date = $lease->lease_start_date;
                $bill->bill_status = 'pending';
                $bill->created_by = Yii::$app->user->id ?? null;
                $bill->save();

                Yii::$app->session->setFlash('success', 'Lease na Bill zimeundwa!');
                return $this->redirect(['bill']); // view ya Bill
            }
        }

        return $this->render('create-lease', ['lease' => $lease]);
    }

    // Delete lease
    public function actionDeleteLease($id)
    {
        $lease = Lease::findOne($id);
        if ($lease) {
            $lease->delete();
            Yii::$app->session->setFlash('success', 'Lease deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Lease not found.');
        }
        return $this->redirect(['leases']);
    }

    // Bills view
    public function actionBill()
    {
        $bills = Bill::find()->with('lease')->all();
        return $this->render('bill', [
            'bills' => $bills,
        ]);
    }


            // CustomController.php
        public function actionProfile()
        {
            $user = Yii::$app->user->identity; // inatoa user aliye login
            return $this->render('profile', [
                'user' => $user,
            ]);
            
        }
     // UserController.php

// Inline check for current password
// Check current password inline via AJAX
public function actionCheckCurrentPassword()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $data = json_decode(Yii::$app->request->getRawBody(), true);
    $password = $data['currentPassword'] ?? '';

    $user = Yii::$app->user->identity;
    $isValid = $user && $user->validatePassword($password);

    return ['valid' => $isValid];
}

// Change password action
public function actionChangePassword()
{
    $model = new \app\models\ChangePasswordForm();

    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $user = Yii::$app->user->identity;

        // Validate old password
        if (!$user->validatePassword($model->currentPassword)) {
            Yii::$app->session->setFlash('error', 'Current password is incorrect.');
            return $this->refresh();
        }

        // Set new password
        $user->setPassword($model->newPassword);
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->save(false);

        Yii::$app->session->setFlash('success', 'Password has been changed. Please login again.');
        Yii::$app->user->logout();

        return $this->redirect(['login/index']);
    }

    return $this->render('change-password', ['model' => $model]);
}



    // Payments view (example: only paid bills)
    public function actionPayment()
    {
        $payments = Bill::find()->where(['not', ['paid_date' => null]])->with('lease')->all();
        return $this->render('payment', [
            'payments' => $payments,
        ]);
    }
}
