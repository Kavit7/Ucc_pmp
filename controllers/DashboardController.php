<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use app\models\ListSource;
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
$forSale = null;
$forRent = null;
$stored = null;

foreach ($childRecords as $child) {
    $childName = strtolower($child->list_Name); // convert to lowercase

    if ($childName === 'sale') {
        $forSale = (new Query())
            ->from('property p')
            ->leftJoin('list_source ls', 'ls.id = p.usage_type_id')
            ->where(['ls.list_Name' => $child->list_Name]) // or strtolower in SQL
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

        // Translation report
        $translationReport = (new Query())
            ->select([
                'l.id AS lease_id',
                't.full_name AS tenant_name',
                'p.property_name',
                'pp.unit_amount AS price',
                'l.lease_start_date AS start_date',
                'l.lease_end_date AS end_date',
            ])
            ->from('lease l')
            ->leftJoin('users t', 't.user_id = l.tenant_id')
            ->leftJoin('property p', 'p.id = l.property_id')
            ->leftJoin('property_price pp', 'pp.id = l.property_price_id')
            ->all();

        return $this->render('@app/views/dashboard/admin_dash', [
            'user' => $user,
            'totalProperties' => $totalProperties,
            'forSale' => $forSale,
            'forRent' => $forRent,
            'stored' => $stored,
            'analytics' => $analytics,
            'translationReport' => $translationReport
        ]);
    }
}