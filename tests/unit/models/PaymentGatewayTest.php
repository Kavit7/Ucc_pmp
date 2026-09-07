<?php

namespace tests\unit\models;

use app\components\FlutterwaveClient;
use app\models\PaymentTransaction;
use app\models\Bill;
use app\models\Lease;
use app\models\Property;
use app\models\ListSource;
use app\models\Users;

/**
 * Covers the parts of the online-payment scaffold that don't require a
 * live Flutterwave account: isConfigured() correctly reporting "not set
 * up" (the real state until real keys are added to secret-local.php),
 * and PaymentTransaction's own save/uuid behavior.
 */
class PaymentGatewayTest extends \Codeception\Test\Unit
{
    public function testIsConfiguredIsFalseWithoutRealKeys()
    {
        // No secret-local.php flutterwave keys in this test environment,
        // and config/test.php doesn't even load config/web.php's params -
        // both should degrade to "not configured", never an error.
        verify(FlutterwaveClient::isConfigured())->false();
    }

    public function testPaymentTransactionGetsAUuidOnSave()
    {
        $tenant = new Users([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => 'Payment Test Tenant',
            'email' => 'pay-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
        ]);
        $tenant->save(false);

        $property = Property::find()->one();
        $price = $property->propertyPrice[0] ?? null;
        $activeStatusId = ListSource::find()->where(['list_Name' => 'Active', 'category' => 'Lease Status'])->select('id')->scalar();

        $lease = new Lease([
            'property_id' => $property->id,
            'tenant_id' => $tenant->user_id,
            'property_price_id' => $price->id,
            'status' => $activeStatusId,
            'lease_start_date' => date('Y-m-d'),
            'lease_end_date' => date('Y-m-d', strtotime('+6 months')),
            'duration_months' => 6,
        ]);
        $lease->save(false);

        $bill = \app\models\Bill::createPending($lease->id, 50000, date('Y-m-d'));

        $transaction = new PaymentTransaction([
            'bill_id' => $bill->id,
            'tenant_id' => $tenant->user_id,
            'tx_ref' => 'TEST-' . uniqid(),
            'amount' => 50000,
            'currency' => 'TZS',
            'status' => 'pending',
        ]);
        $transaction->save(false);

        verify($transaction->uuid)->notEmpty();
        verify($transaction->status)->equals('pending');
    }
}
