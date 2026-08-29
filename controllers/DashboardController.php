<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use app\models\ListSource;
use app\models\Bill;
use app\models\Lease;
use app\models\Notification;
use app\models\MaintenanceRequest;
use yii\filters\AccessControl;

class DashboardController extends Controller
{
    public $layout = 'custom'; // Layout yako ya sidebar + content

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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

    public function actionAdminDash()
    {
        $user = Yii::$app->user->identity;

        if (($user->role ?? null) === 'tenant') {
            return $this->tenantDashboard($user);
        }

        // Summary counts
        $totalProperties = (new Query())->from('property')->count();
        $parent = ListSource::find()
            ->select('id')
            ->where(['parent_id' => null, 'category' => 'Usage Type'])
            ->all();

        // Convert child names to lowercase
        $childRecords = ListSource::find()
            ->where(['parent_id' => array_column($parent, 'id')])
            ->all();

        // Initialize variables
        $forSale = 0;
        $forRent = 0;
        $stored = 0;

        foreach ($childRecords as $child) {
            $childName = strtolower($child->list_Name); // convert to lowercase

            if ($childName === 'sale') {
                $forSale = (new Query())
                    ->from('property p')
                    ->leftJoin('list_source ls', 'ls.id = p.usage_type_id')
                    ->where(['ls.list_Name' => $child->list_Name])
                    ->count();
            } elseif ($childName === 'rented') {
                $forRent = (new Query())
                    ->from('property p')
                    ->leftJoin('list_source ls', 'ls.id = p.usage_type_id')
                    ->where(['ls.list_Name' => $child->list_Name])
                    ->count();
            } elseif ($childName === 'storage') {
                $stored = (new Query())
                    ->from('property p')
                    ->leftJoin('list_source ls', 'ls.id = p.usage_type_id')
                    ->where(['ls.list_Name' => $child->list_Name])
                    ->count();
            }
        }

        // Property analytics by type (using list_source)
        $analytics = (new Query())
            ->select(['ls.list_Name AS type_name', 'COUNT(p.id) as total'])
            ->from('property p')
            ->leftJoin('list_source ls', 'ls.id = p.property_type_id')
            ->groupBy('ls.list_Name')
            ->all();

        // Property analytics by status (Active / Under Maintenance / etc.)
        $statusAnalytics = (new Query())
            ->select(['ls.list_Name AS status_name', 'COUNT(p.id) as total'])
            ->from('property p')
            ->leftJoin('list_source ls', 'ls.id = p.property_status_id')
            ->groupBy('ls.list_Name')
            ->all();

        // --- Revenue figures (mirrors ReportController's logic) ---
        $billStatusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Bill Status'])->scalar();
        $paidId = ListSource::find()->where(['list_Name' => 'Paid', 'parent_id' => $billStatusParentId])->select('id')->scalar();
        $pendingId = ListSource::find()->where(['list_Name' => 'Pending', 'parent_id' => $billStatusParentId])->select('id')->scalar();

        $totalCollected = (float) Bill::find()->where(['bill_status' => $paidId])->sum('amount');
        $totalPending = (float) Bill::find()->where(['bill_status' => $pendingId])->sum('amount');
        $overdueCount = (int) Bill::find()
            ->where(['bill_status' => $pendingId])
            ->andWhere(['<', 'due_date', date('Y-m-d')])
            ->count();

        // --- Lease figures ---
        $leaseStatusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Lease Status'])->scalar();
        $activeLeaseId = ListSource::find()->where(['list_Name' => 'Active', 'parent_id' => $leaseStatusParentId])->select('id')->scalar();
        $activeLeases = (int) Lease::find()->where(['status' => $activeLeaseId])->count();
        $totalTenants = (int) (new Query())->from('users')->where(['role' => 'tenant'])->count();

        // --- Occupancy rate: properties with an active lease / total properties ---
        $occupiedProperties = (int) Lease::find()->where(['status' => $activeLeaseId])->select('property_id')->distinct()->count();
        $occupancyRate = $totalProperties > 0 ? round(($occupiedProperties / $totalProperties) * 100) : 0;

        // --- Leases expiring in the next 30 days ---
        $expiringLeases = (new Query())
            ->select([
                'l.id', 'p.property_name', 't.full_name AS tenant_name', 'l.lease_end_date',
            ])
            ->from('lease l')
            ->leftJoin('property p', 'p.id = l.property_id')
            ->leftJoin('users t', 't.user_id = l.tenant_id')
            ->where(['l.status' => $activeLeaseId])
            ->andWhere(['between', 'l.lease_end_date', date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))])
            ->orderBy(['l.lease_end_date' => SORT_ASC])
            ->all();

        // --- Revenue trend: this month vs last month (collected) ---
        $collectedThisMonth = (float) Bill::find()
            ->where(['bill_status' => $paidId])
            ->andWhere(['>=', 'paid_date', date('Y-m-01')])
            ->sum('amount') ?: 0;
        $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
        $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));
        $collectedLastMonth = (float) Bill::find()
            ->where(['bill_status' => $paidId])
            ->andWhere(['between', 'paid_date', $lastMonthStart, $lastMonthEnd])
            ->sum('amount') ?: 0;
        $revenueChangePct = $collectedLastMonth > 0
            ? round((($collectedThisMonth - $collectedLastMonth) / $collectedLastMonth) * 100)
            : ($collectedThisMonth > 0 ? 100 : 0);

        // --- Revenue over the last 6 months, for the trend chart ---
        $revenueTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $revenueTrend[] = [
                'label' => date('M', strtotime($monthStart)),
                'collected' => (float) Bill::find()
                    ->where(['bill_status' => $paidId])
                    ->andWhere(['between', 'paid_date', $monthStart, $monthEnd])
                    ->sum('amount') ?: 0,
                'pending' => (float) Bill::find()
                    ->where(['bill_status' => $pendingId])
                    ->andWhere(['between', 'due_date', $monthStart, $monthEnd])
                    ->sum('amount') ?: 0,
            ];
        }

        // --- Maintenance summary ---
        $maintStatusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Maintenance Status'])->scalar();
        $maintOpenId = ListSource::find()->where(['list_Name' => 'Open', 'parent_id' => $maintStatusParentId])->select('id')->scalar();
        $maintResolvedId = ListSource::find()->where(['list_Name' => 'Resolved', 'parent_id' => $maintStatusParentId])->select('id')->scalar();
        $maintClosedId = ListSource::find()->where(['list_Name' => 'Closed', 'parent_id' => $maintStatusParentId])->select('id')->scalar();
        $maintPriorityParentId = ListSource::find()->select('id')->where(['list_Name' => 'Maintenance Priority'])->scalar();
        $maintUrgentId = ListSource::find()->where(['list_Name' => 'Urgent', 'parent_id' => $maintPriorityParentId])->select('id')->scalar();

        $openMaintenanceCount = (int) MaintenanceRequest::find()->where(['status_id' => $maintOpenId])->count();
        $urgentMaintenanceCount = (int) MaintenanceRequest::find()
            ->where(['priority_id' => $maintUrgentId])
            ->andWhere(['not in', 'status_id', array_filter([$maintResolvedId, $maintClosedId])])
            ->count();
        $myMaintenanceCount = $user->role === 'technician'
            ? (int) MaintenanceRequest::find()
                ->where(['assigned_to' => $user->user_id])
                ->andWhere(['not in', 'status_id', array_filter([$maintResolvedId, $maintClosedId])])
                ->count()
            : null;

        // Recent leases (replaces the old "Translation Report")
        $recentLeases = (new Query())
            ->select([
                'l.id AS lease_id',
                't.full_name AS tenant_name',
                'p.property_name',
                'pp.unit_amount AS price',
                'l.lease_start_date AS start_date',
                'l.lease_end_date AS end_date',
                'ls.list_Name AS status_name',
            ])
            ->from('lease l')
            ->leftJoin('users t', 't.user_id = l.tenant_id')
            ->leftJoin('property p', 'p.id = l.property_id')
            ->leftJoin('property_price pp', 'pp.id = l.property_price_id')
            ->leftJoin('list_source ls', 'ls.id = l.status')
            ->orderBy(['l.created_at' => SORT_DESC])
            ->limit(8)
            ->all();

        // Recent activity for this user (their own notification feed)
        Notification::syncOverdueBillNotifications();
        $recentActivity = Notification::find()
            ->where(['user_id' => $user->user_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        return $this->render('@app/views/dashboard/admin_dash', [
            'user' => $user,
            'totalProperties' => $totalProperties,
            'forSale' => $forSale,
            'forRent' => $forRent,
            'stored' => $stored,
            'analytics' => $analytics,
            'statusAnalytics' => $statusAnalytics,
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
            'overdueCount' => $overdueCount,
            'activeLeases' => $activeLeases,
            'totalTenants' => $totalTenants,
            'recentLeases' => $recentLeases,
            'recentActivity' => $recentActivity,
            'occupancyRate' => $occupancyRate,
            'occupiedProperties' => $occupiedProperties,
            'expiringLeases' => $expiringLeases,
            'collectedThisMonth' => $collectedThisMonth,
            'revenueChangePct' => $revenueChangePct,
            'revenueTrend' => $revenueTrend,
            'openMaintenanceCount' => $openMaintenanceCount,
            'urgentMaintenanceCount' => $urgentMaintenanceCount,
            'myMaintenanceCount' => $myMaintenanceCount,
        ]);
    }

    /**
     * Tenant-scoped dashboard: only this tenant's own leases, bills, and
     * maintenance requests - never other tenants' data or staff-level
     * property/revenue aggregates.
     */
    private function tenantDashboard($user)
    {
        $leases = Lease::find()
            ->with(['property', 'propertyPrice', 'statusLabel'])
            ->where(['tenant_id' => $user->user_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $leaseIds = array_map(fn($l) => $l->id, $leases);

        $bills = empty($leaseIds) ? [] : Bill::find()
            ->with(['lease.property', 'billStatus'])
            ->where(['lease_id' => $leaseIds])
            ->orderBy(['due_date' => SORT_DESC])
            ->all();

        $billStatusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Bill Status'])->scalar();
        $pendingId = ListSource::find()->where(['list_Name' => 'Pending', 'parent_id' => $billStatusParentId])->select('id')->scalar();

        $totalDue = 0.0;
        $overdueCount = 0;
        foreach ($bills as $bill) {
            if ($bill->bill_status == $pendingId) {
                $totalDue += (float) $bill->amount;
                if ($bill->due_date < date('Y-m-d')) {
                    $overdueCount++;
                }
            }
        }

        $leaseStatusParentId = ListSource::find()->select('id')->where(['list_Name' => 'Lease Status'])->scalar();
        $activeLeaseId = ListSource::find()->where(['list_Name' => 'Active', 'parent_id' => $leaseStatusParentId])->select('id')->scalar();
        $activeLeases = count(array_filter($leases, fn($l) => $l->status == $activeLeaseId));

        $maintenanceRequests = MaintenanceRequest::find()
            ->with(['property', 'status'])
            ->where(['reported_by' => $user->user_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        Notification::syncOverdueBillNotifications();
        $recentActivity = Notification::find()
            ->where(['user_id' => $user->user_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        return $this->render('@app/views/dashboard/tenant_dash', [
            'user' => $user,
            'leases' => $leases,
            'bills' => $bills,
            'totalDue' => $totalDue,
            'overdueCount' => $overdueCount,
            'activeLeases' => $activeLeases,
            'maintenanceRequests' => $maintenanceRequests,
            'recentActivity' => $recentActivity,
        ]);
    }
}
