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

        // Portfolio summary for the welcome slide - describes everything we
        // own, not just what's currently vacant. Computed before filters are
        // applied so the counts always reflect the whole portfolio.
        $totalOwned = Property::find()->count();
        $typeBreakdown = Property::find()
            ->select(['list_source.list_Name AS type_name', 'COUNT(*) AS total'])
            ->joinWith('propertyType', false)
            ->groupBy('list_source.list_Name')
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();
        $regionNames = Property::find()
            ->select('region.name')
            ->joinWith('street.region', false)
            ->distinct()
            ->column();
        $regionNames = array_values(array_filter($regionNames));

        // Filters - all optional, applied on top of the "currently vacant" base query.
        $q = trim((string) Yii::$app->request->get('q'));
        $typeId = Yii::$app->request->get('type');
        $region = Yii::$app->request->get('region');
        $minPrice = Yii::$app->request->get('min_price');
        $maxPrice = Yii::$app->request->get('max_price');

        $query = Property::find()
            ->with(['propertyType', 'photos', 'propertyPrice', 'usageType', 'street'])
            ->andWhere(['not in', 'id', $occupiedLeaseSubquery])
            ->orderBy(['id' => SORT_DESC]);

        if ($q !== '') {
            $query->andWhere(['like', 'property.property_name', $q]);
        }
        if ($typeId) {
            $query->andWhere(['property_type_id' => $typeId]);
        }
        if ($region) {
            $query->joinWith('street.region', false)
                ->andWhere(['region.name' => $region]);
        }
        if (($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== '')) {
            $priceQuery = \app\models\PropertyPrice::find()->select('property_id');
            if ($minPrice !== null && $minPrice !== '') {
                $priceQuery->andWhere(['>=', 'unit_amount', $minPrice]);
            }
            if ($maxPrice !== null && $maxPrice !== '') {
                $priceQuery->andWhere(['<=', 'unit_amount', $maxPrice]);
            }
            $query->andWhere(['in', 'id', $priceQuery]);
        }

        $totalAvailable = (clone $query)->count();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 12],
        ]);

        $typeOptions = \yii\helpers\ArrayHelper::map(
            \app\models\ListSource::find()
                ->where(['id' => Property::find()->select('property_type_id')->distinct()])
                ->all(),
            'id',
            'list_Name'
        );

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'totalAvailable' => $totalAvailable,
            'totalOwned' => $totalOwned,
            'typeBreakdown' => $typeBreakdown,
            'regionNames' => $regionNames,
            'typeOptions' => $typeOptions,
            'q' => $q,
            'selectedType' => $typeId,
            'selectedRegion' => $region,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
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
