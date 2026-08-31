<?php

namespace app\controllers;

use Yii;
use app\models\AuditLog;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class AuditLogController extends Controller
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
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return (Yii::$app->user->identity->role ?? null) === 'admin';
                        },
                    ],
                ],
                'denyCallback' => function () {
                    return Yii::$app->response->redirect(['login/login']);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $query = AuditLog::find()->with('user')->orderBy(['created_at' => SORT_DESC]);

        $modelName = Yii::$app->request->get('model_name');
        if ($modelName) {
            $query->andWhere(['model_name' => $modelName]);
        }

        $action = Yii::$app->request->get('action');
        if ($action) {
            $query->andWhere(['action' => $action]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 30],
        ]);

        $modelOptions = AuditLog::find()->select('model_name')->distinct()->column();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'modelOptions' => $modelOptions,
            'selectedModel' => $modelName,
            'selectedAction' => $action,
        ]);
    }
}
