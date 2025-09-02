<?php

namespace app\controllers;

use Yii;
use app\models\PropertyPrice;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

class PropertyPriceController extends Controller
{
    public $layout = 'custom'; // optional, kama una layout yako

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // List all prices
    public function actionIndex()
    {
        $prices = PropertyPrice::find()->with('property')->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('@app/views/custom/property_price_index', [
            'prices' => $prices,
        ]);
    }

    // Create or update
    public function actionSave($id = null)
    {
        $model = $id ? $this->findModel($id) : new PropertyPrice();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                $message = $id ? 'Property price updated successfully.' : 'Property price saved successfully.';
                return ['status' => 'success', 'message' => $message];
            } else {
                return ['status' => 'error', 'errors' => $model->errors];
            }
        }

        return $this->render('@app/views/custom/property_price_form', [
            'model' => $model,
        ]);
    }

    // Delete with JSON response
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        if($model->delete()){
            return ['status' => 'success', 'message' => 'Property price deleted successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete property price.'];
        }
    }

    protected function findModel($id)
    {
        if (($model = PropertyPrice::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested price does not exist.');
    }
}
