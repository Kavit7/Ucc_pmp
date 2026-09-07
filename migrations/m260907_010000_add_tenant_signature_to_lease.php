<?php

use yii\db\Migration;

/**
 * In-app e-signature capture for leases: the tenant draws a signature
 * (canvas, no external e-sign service) which is saved as a PNG and
 * timestamped, marking the lease as signed.
 */
class m260907_010000_add_tenant_signature_to_lease extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%lease}}', 'tenant_signature_url', $this->string(255)->null()->after('lease_doc_url'));
        $this->addColumn('{{%lease}}', 'tenant_signed_at', $this->dateTime()->null()->after('tenant_signature_url'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%lease}}', 'tenant_signed_at');
        $this->dropColumn('{{%lease}}', 'tenant_signature_url');
    }
}
