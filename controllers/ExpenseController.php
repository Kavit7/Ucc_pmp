<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Expense;
use app\models\Property;

/**
 * Property expense entry - the debit side of the general ledger. Every
 * saved expense posts an automatic journal entry (see Expense::afterSave()
 * / app\components\Ledger); this controller is just the form around it.
 */
class ExpenseController extends Controller
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
                        'actions' => ['index', 'create', 'delete'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return in_array(Yii::$app->user->identity->role ?? null, ['admin', 'manager'], true);
                        },
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Expense::find()->with('property')->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 25],
        ]);

        $totalExpenses = (float) Expense::find()->sum('amount');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'totalExpenses' => $totalExpenses,
        ]);
    }

    public function actionCreate()
    {
        $model = new Expense();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Expense logged.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'properties' => \yii\helpers\ArrayHelper::map(Property::find()->orderBy('property_name')->all(), 'id', 'property_name'),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Expense removed. Note: its ledger entry is kept as a historical record.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested expense does not exist.');
    }
}
