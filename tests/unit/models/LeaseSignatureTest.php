<?php

namespace tests\unit\models;

use app\models\Lease;
use app\models\ListSource;
use app\models\Property;
use app\models\Users;
use Yii;

/**
 * Covers Lease::saveTenantSignature(), the e-signature capture added this
 * session: a valid base64 PNG (as a canvas signature pad produces) must
 * save and timestamp the lease, while anything that isn't a real,
 * reasonably-sized PNG must be rejected without touching the lease.
 */
class LeaseSignatureTest extends \Codeception\Test\Unit
{
    private $createdFiles = [];

    protected function _after()
    {
        foreach ($this->createdFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function makeLease()
    {
        $tenant = new Users([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => 'Signature Test Tenant',
            'email' => 'sig-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
        ]);
        $tenant->save(false);

        $property = Property::find()->one();
        verify($property)->notNull();
        $price = $property->propertyPrice[0] ?? null;
        verify($price)->notNull();

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

        return $lease;
    }

    private function pngDataUrl($paddingBytes = 60)
    {
        $minimalPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $padded = $minimalPng . str_repeat('X', $paddingBytes);
        return 'data:image/png;base64,' . base64_encode($padded);
    }

    public function testValidSignatureIsSavedAndLeaseIsTimestamped()
    {
        $lease = $this->makeLease();

        $result = $lease->saveTenantSignature($this->pngDataUrl());

        verify($result)->true();
        verify($lease->tenant_signature_url)->notNull();
        verify($lease->tenant_signed_at)->notNull();

        $path = Yii::getAlias('@webroot/' . $lease->tenant_signature_url);
        $this->createdFiles[] = $path;
        verify(is_file($path))->true();
    }

    public function testTooSmallSignatureIsRejected()
    {
        $lease = $this->makeLease();

        $result = $lease->saveTenantSignature($this->pngDataUrl(0));

        verify($result)->false();
        verify($lease->tenant_signature_url)->null();
        verify($lease->getFirstError('tenant_signature_url'))->notNull();
    }

    public function testNonImageDataIsRejected()
    {
        $lease = $this->makeLease();

        $result = $lease->saveTenantSignature('not a data url at all');

        verify($result)->false();
        verify($lease->tenant_signature_url)->null();
    }
}
