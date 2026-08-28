<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\Bill;
use app\models\Lease;
use app\models\Property;
use app\models\ListSource;

class ReportController extends Controller
{
    public $layout = 'custom';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    return Yii::$app->response->redirect(['login/login']);
                },
            ],
        ];
    }

    /**
     * Reports hub: high-level totals plus links to the detailed reports.
     */
    public function actionIndex()
    {
        $paidId = $this->billStatusId('Paid');
        $pendingId = $this->billStatusId('Pending');

        $totalCollected = (float) Bill::find()->where(['bill_status' => $paidId])->sum('amount');
        $totalPending = (float) Bill::find()->where(['bill_status' => $pendingId])->sum('amount');
        $overdueCount = (int) Bill::find()
            ->where(['bill_status' => $pendingId])
            ->andWhere(['<', 'due_date', date('Y-m-d')])
            ->count();

        $totalProperties = (int) Property::find()->count();
        $activeLeases = (int) Lease::find()->where(['status' => $this->leaseStatusId('Active')])->count();

        return $this->render('index', [
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
            'overdueCount' => $overdueCount,
            'totalProperties' => $totalProperties,
            'activeLeases' => $activeLeases,
        ]);
    }

    /**
     * Revenue report: bills with amount/status, filterable by date range and status.
     */
    public function actionRevenue()
    {
        $from = Yii::$app->request->get('from');
        $to = Yii::$app->request->get('to');
        $statusId = Yii::$app->request->get('status');

        $query = Bill::find()->joinWith(['lease.property', 'lease.tenant', 'billStatus']);

        if ($from) {
            $query->andWhere(['>=', 'bill.due_date', $from]);
        }
        if ($to) {
            $query->andWhere(['<=', 'bill.due_date', $to]);
        }
        if ($statusId) {
            $query->andWhere(['bill.bill_status' => $statusId]);
        }

        $totalAmount = (clone $query)->sum('bill.amount');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['due_date' => SORT_DESC]],
        ]);

        $statusParentId = $this->billStatusParentId();
        $statusOptions = ListSource::find()
            ->where(['parent_id' => $statusParentId])
            ->select(['list_Name', 'id'])
            ->indexBy('id')
            ->column();

        return $this->render('revenue', [
            'dataProvider' => $dataProvider,
            'totalAmount' => (float) $totalAmount,
            'statusOptions' => $statusOptions,
            'from' => $from,
            'to' => $to,
            'statusId' => $statusId,
        ]);
    }

    /**
     * Occupancy report: properties grouped by status and usage type.
     */
    public function actionOccupancy()
    {
        $byStatus = (new Query())
            ->select(['ls.list_Name AS label', 'COUNT(p.id) AS total'])
            ->from('property p')
            ->leftJoin('list_source ls', 'ls.id = p.property_status_id')
            ->groupBy('ls.list_Name')
            ->all();

        $byUsage = (new Query())
            ->select(['ls.list_Name AS label', 'COUNT(p.id) AS total'])
            ->from('property p')
            ->leftJoin('list_source ls', 'ls.id = p.usage_type_id')
            ->groupBy('ls.list_Name')
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => Property::find()->joinWith(['propertyStatus', 'usageType']),
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['property_name' => SORT_ASC]],
        ]);

        return $this->render('occupancy', [
            'byStatus' => $byStatus,
            'byUsage' => $byUsage,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lease report: all leases with tenant, property, rent and status, filterable by status.
     */
    public function actionLeases()
    {
        $statusId = Yii::$app->request->get('status');

        $query = Lease::find()->joinWith(['property', 'tenant', 'propertyPrice', 'statusLabel']);
        if ($statusId) {
            $query->andWhere(['lease.status' => $statusId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['lease_start_date' => SORT_DESC]],
        ]);

        $statusParentId = $this->listSourceParentId('Lease Status');
        $statusOptions = ListSource::find()
            ->where(['parent_id' => $statusParentId])
            ->select(['list_Name', 'id'])
            ->indexBy('id')
            ->column();

        return $this->render('leases', [
            'dataProvider' => $dataProvider,
            'statusOptions' => $statusOptions,
            'statusId' => $statusId,
        ]);
    }

    private function listSourceParentId($name)
    {
        return ListSource::find()->select('id')->where(['list_Name' => $name])->scalar();
    }

    private function billStatusParentId()
    {
        return $this->listSourceParentId('Bill Status');
    }

    private function billStatusId($name)
    {
        return ListSource::find()
            ->where(['list_Name' => $name, 'parent_id' => $this->billStatusParentId()])
            ->select('id')
            ->scalar();
    }

    private function leaseStatusId($name)
    {
        return ListSource::find()
            ->where(['list_Name' => $name, 'parent_id' => $this->listSourceParentId('Lease Status')])
            ->select('id')
            ->scalar();
    }
}
