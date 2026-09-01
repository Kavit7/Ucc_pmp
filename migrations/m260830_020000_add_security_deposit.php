<?php

use yii\db\Migration;

/**
 * The lease.security_deposit_* columns and their FK are now created
 * directly by m260827_000000_create_base_schema. This migration now only seeds the
 * "Security Deposit Status" list_source taxonomy those columns rely on -
 * kept under its original name so the `migration` table's existing
 * "applied" record for this class stays valid on databases that already
 * ran it.
 */
class m260830_020000_add_security_deposit extends Migration
{
    public function safeUp()
    {
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
        $this->delete('list_source', ['category' => 'Security Deposit Status']);
    }
}
