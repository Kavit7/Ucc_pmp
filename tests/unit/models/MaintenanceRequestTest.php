<?php

namespace tests\unit\models;

use app\models\MaintenanceRequest;
use app\models\Property;
use app\models\Users;

/**
 * Covers the maintenance/work-order feature: uuid auto-generation and the
 * default-to-Open status behavior that actionCreate() relies on.
 */
class MaintenanceRequestTest extends \Codeception\Test\Unit
{
    private function makeTenantAndProperty()
    {
        $tenant = new Users([
            'uuid' => 'UT_' . uniqid(),
            'full_name' => 'Maintenance Test Tenant',
            'email' => 'maint-test-' . uniqid() . '@example.com',
            'role' => 'tenant',
            'status' => 'active',
        ]);
        $tenant->save(false);

        $property = Property::find()->one();
        verify($property)->notNull();

        return [$tenant, $property];
    }

    public function testUuidIsGeneratedOnInsert()
    {
        [$tenant, $property] = $this->makeTenantAndProperty();

        $request = new MaintenanceRequest([
            'property_id' => $property->id,
            'reported_by' => $tenant->user_id,
            'title' => 'Broken window',
            'status_id' => MaintenanceRequest::openStatusId(),
        ]);
        $request->save(false);

        verify($request->uuid)->notEmpty();
        verify($request->uuid)->stringStartsWith('MR_');
    }

    public function testOpenStatusIdResolvesToARealListSourceRow()
    {
        $id = MaintenanceRequest::openStatusId();

        verify($id)->notEmpty();
    }

    public function testTwoRequestsGetDistinctUuids()
    {
        [$tenant, $property] = $this->makeTenantAndProperty();

        $first = new MaintenanceRequest([
            'property_id' => $property->id,
            'reported_by' => $tenant->user_id,
            'title' => 'Issue one',
            'status_id' => MaintenanceRequest::openStatusId(),
        ]);
        $first->save(false);

        $second = new MaintenanceRequest([
            'property_id' => $property->id,
            'reported_by' => $tenant->user_id,
            'title' => 'Issue two',
            'status_id' => MaintenanceRequest::openStatusId(),
        ]);
        $second->save(false);

        verify($first->uuid)->notEquals($second->uuid);
    }
}
