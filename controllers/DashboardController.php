<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use app\models\ListSource;
use app\models\Bill;
use app\models\Lease;
use app\models\Notification;

class DashboardController extends Controller
{
    public $layout = 'custom'; // Layout yako ya sidebar + content

    public function actionAdminDash()
    {
        $user = Yii::$app->user->identity;

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
        ]);
    }
}
