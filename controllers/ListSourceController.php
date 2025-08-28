<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\ListSource;

class ListSourceController extends Controller
{
    public $layout = 'custom';

    public function actionCreate()
    {
        $model = new ListSource();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionTest()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ListSource::find()->orderBy(['sort_by' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $model = new ListSource();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        $parents = ArrayHelper::map(
            ListSource::find()->all(),
            'id',
            function($m) { return $m->list_Name . ' - ' . $m->code; }
        );

        return $this->render('test', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'parents' => $parents
        ]);
    }
}
