<?php

namespace app\controllers;

use Yii;
use app\models\Property;
use app\models\PropertyInquiry;
use app\models\Notification;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * Public-facing "available properties" listing and inquiry form. No
 * authentication required - this is the one part of the app meant to be
 * reachable by prospective tenants who don't have an account yet.
 */
class PublicListingController extends Controller
{
    public $layout = 'public';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'inquire' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $occupiedLeaseSubquery = \app\models\Lease::find()
            ->select('property_id')
            ->where(['status' => \app\models\ListSource::find()
                ->select('id')
                ->where(['list_Name' => 'Active', 'category' => 'Lease Status']),
            ]);

        $query = Property::find()
            ->with(['propertyType', 'photos', 'propertyPrice'])
            ->andWhere(['not in', 'id', $occupiedLeaseSubquery])
            ->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 12],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionInquire()
    {
        $model = new PropertyInquiry();
        $model->load(Yii::$app->request->post());

        // Simple honeypot: a hidden field real visitors never fill in.
        if (Yii::$app->request->post('website')) {
            return $this->redirect(['index']);
        }

        if ($model->validate() && $model->save()) {
            $propertyName = $model->property->property_name ?? 'a property';
            Notification::notifyRoles(
                ['admin', 'manager'],
                'New property inquiry',
                "{$model->name} is interested in {$propertyName} (" . $model->email . ($model->phone ? ', ' . $model->phone : '') . ").",
                ['property/document', 'id' => $model->property_id]
            );

            Yii::$app->session->setFlash('success', "Thanks {$model->name} - we've received your inquiry and will contact you soon.");
        } else {
            Yii::$app->session->setFlash('error', 'Please check your details and try again.');
        }

        return $this->redirect(['index']);
    }
}
