<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\GlAccount;
use app\models\GlJournalEntry;

/**
 * The accounting side of the app: a trial balance derived live from the
 * journal (see GlAccount::balance()), and the journal itself for
 * drilling into what's behind a number. Nothing here is ever hand-edited -
 * every entry comes from Ledger::post(), called by Bill/Expense/payment
 * recording elsewhere in the app.
 */
class GlController extends Controller
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
                        'actions' => ['index', 'journal'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return in_array(Yii::$app->user->identity->role ?? null, ['admin', 'manager', 'accountant'], true);
                        },
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['login/login']);
                    }
                    throw new \yii\web\ForbiddenHttpException('You do not have permission to view the ledger.');
                },
            ],
        ];
    }

    /**
     * Trial balance: every account and its current balance. In a
     * correctly-posted ledger, total debits equal total credits.
     */
    public function actionIndex()
    {
        $accounts = GlAccount::find()->where(['is_active' => 1])->orderBy('code')->all();

        $rows = [];
        $totalDebitBalances = 0.0;
        $totalCreditBalances = 0.0;
        foreach ($accounts as $account) {
            $balance = $account->balance();
            $rows[] = ['account' => $account, 'balance' => $balance];
            if ($balance >= 0) {
                $totalDebitBalances += $account->normal_balance === 'debit' ? $balance : 0;
                $totalCreditBalances += $account->normal_balance === 'credit' ? $balance : 0;
            }
        }

        return $this->render('index', [
            'rows' => $rows,
            'totalDebitBalances' => $totalDebitBalances,
            'totalCreditBalances' => $totalCreditBalances,
        ]);
    }

    /**
     * The journal: every posted entry, most recent first, with its lines.
     */
    public function actionJournal()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => GlJournalEntry::find()->with(['lines.account', 'creator'])->orderBy(['entry_date' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => ['pageSize' => 25],
        ]);

        return $this->render('journal', ['dataProvider' => $dataProvider]);
    }
}
