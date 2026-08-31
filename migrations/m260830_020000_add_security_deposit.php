<?php

use yii\db\Migration;

class m260830_020000_add_security_deposit extends Migration
{
    public function safeUp()
    {
        $this->addColumn('lease', 'security_deposit_amount', $this->decimal(15, 2)->null()->after('duration_months'));
        $this->addColumn('lease', 'security_deposit_status', $this->integer()->null()->after('security_deposit_amount'));
        $this->addColumn('lease', 'security_deposit_returned_at', $this->date()->null()->after('security_deposit_status'));
        $this->addColumn('lease', 'security_deposit_notes', $this->text()->null()->after('security_deposit_returned_at'));

        $this->addForeignKey(
            'fk-lease-security_deposit_status',
            'lease',
            'security_deposit_status',
            'list_source',
            'id',
            'SET NULL'
        );

        // Seed the "Security Deposit Status" taxonomy the same way every
        // other status field in this app is driven off list_source.
        $this->insert('list_source', [
            'uuid' => 'LS_SEC_DEP_PARENT',
            'list_Name' => 'Security Deposit Status',
            'code' => 'security_deposit_status',
            'category' => 'Security Deposit Status',
            'parent_id' => null,
        ]);
        $parentId = $this->db->getLastInsertID();

        foreach ([
            ['Held', 'security_deposit_held'],
            ['Partially Returned', 'security_deposit_partial'],
            ['Returned', 'security_deposit_returned'],
            ['Deducted', 'security_deposit_deducted'],
        ] as $i => [$name, $codeBase]) {
            $this->insert('list_source', [
                'uuid' => 'LS_SEC_DEP_' . ($i + 1),
                'list_Name' => $name,
                'code' => $codeBase,
                'category' => 'Security Deposit Status',
                'parent_id' => $parentId,
            ]);
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-lease-security_deposit_status', 'lease');
        $this->delete('list_source', ['category' => 'Security Deposit Status']);
        $this->dropColumn('lease', 'security_deposit_notes');
        $this->dropColumn('lease', 'security_deposit_returned_at');
        $this->dropColumn('lease', 'security_deposit_status');
        $this->dropColumn('lease', 'security_deposit_amount');
    }
}
