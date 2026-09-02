<?php

namespace tests\unit\commands;

use app\commands\BillingController;
use app\models\Bill;
use app\models\Lease;
use app\models\ListSource;
use app\models\Property;
use app\models\PropertyPrice;
use app\models\Users;

/**
 * Regression coverage for the recurring-billing coverage-math bug found and
 * fixed this session: a lease's first bill already covers its whole
 * duration_months upfront, so actionGenerateRecurring() must not generate a
 * second bill just because that first bill's due_date has passed. The old
 * due_date-only check did exactly that and produced a real duplicate bill on
 * a live 2-month lease.
 */
class BillingTest extends \Codeception\Test\Unit
{
    private function activeLeaseStatusId()
    {
        return ListSource::find()
            ->where(['list_Name' => 'Active', 'category' => 'Lease Status'])
            ->select('id')->scalar();
    }

    private function makeTenant()
    {
        $tenant = new Users([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => 'Billing Test Tenant',
            'email' => 'billing-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
        ]);
        $tenant->save(false);
        return $tenant;
    }

    private function makePrice($amount)
    {
        $property = Property::find()->one();
        verify($property)->notNull();

        $price = new PropertyPrice([
            'property_id' => $property->id,
            'unit_amount' => $amount,
            'period' => 'Monthly',
            'price_type' => 59,
        ]);
        $price->save(false);
        return $price;
    }

    /**
     * Builds a lease whose only bill (bill count 1) was created
     * `monthsAgoStart` months ago and is meant to cover `durationMonths`
     * months from lease_start_date.
     */
    private function makeLeaseWithFirstBill($durationMonths, $monthsAgoStart, $price, $tenant)
    {
        $start = date('Y-m-d', strtotime("-{$monthsAgoStart} months"));
        $end = date('Y-m-d', strtotime($start . " +{$durationMonths} months +6 months"));

        $lease = new Lease([
            'property_id' => $price->property_id,
            'tenant_id' => $tenant->user_id,
            'property_price_id' => $price->id,
            'status' => $this->activeLeaseStatusId(),
            'lease_start_date' => $start,
            'lease_end_date' => $end,
            'duration_months' => $durationMonths,
        ]);
        $lease->save(false);

        Bill::createPending($lease->id, $price->unit_amount, $start, null);

        return $lease;
    }

    private function runGenerateRecurring()
    {
        // BillingController only needs a module for the base Controller
        // constructor and never touches console-specific behavior beyond
        // stdout(), so the already-booted test app satisfies it fine.
        // Yii::$app->controller must be set manually here because we're
        // calling the action directly rather than through
        // Application::runAction() (which is what sets it in production,
        // and is what Notification::notify()'s relative Url::to() needs).
        $controller = new BillingController('billing', \Yii::$app);
        $previousController = \Yii::$app->controller;
        \Yii::$app->controller = $controller;
        try {
            $controller->actionGenerateRecurring();
        } finally {
            \Yii::$app->controller = $previousController;
        }
    }

    public function testFullyCoveredMultiMonthLeaseGetsNoExtraBill()
    {
        $tenant = $this->makeTenant();
        $price = $this->makePrice(2000000);
        // 2-month lease that started 1 month ago: its single bill already
        // covers month 1 and month 2, so today (1 month in) nothing new is due.
        $lease = $this->makeLeaseWithFirstBill(2, 1, $price, $tenant);

        $this->runGenerateRecurring();

        $count = Bill::find()->where(['lease_id' => $lease->id])->count();
        verify($count)->equals(1);
    }

    public function testExpiredCoverageGeneratesExactlyOneNewBill()
    {
        $tenant = $this->makeTenant();
        $price = $this->makePrice(1500000);
        // 1-month lease that started 2 months ago: its single bill covered
        // only month 1, so a second bill for month 2 is now due.
        $lease = $this->makeLeaseWithFirstBill(1, 2, $price, $tenant);

        $this->runGenerateRecurring();

        $count = Bill::find()->where(['lease_id' => $lease->id])->count();
        verify($count)->equals(2);
    }

    public function testLeaseWithNoBillsYetIsSkipped()
    {
        $tenant = $this->makeTenant();
        $price = $this->makePrice(1000000);

        $lease = new Lease([
            'property_id' => $price->property_id,
            'tenant_id' => $tenant->user_id,
            'property_price_id' => $price->id,
            'status' => $this->activeLeaseStatusId(),
            'lease_start_date' => date('Y-m-d', strtotime('-1 month')),
            'lease_end_date' => date('Y-m-d', strtotime('+11 months')),
            'duration_months' => 1,
        ]);
        $lease->save(false);

        $this->runGenerateRecurring();

        $count = Bill::find()->where(['lease_id' => $lease->id])->count();
        verify($count)->equals(0);
    }
}
