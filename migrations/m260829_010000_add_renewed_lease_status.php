<?php

use yii\db\Migration;

/**
 * Adds a "Renewed" Lease Status, distinct from "Terminated" (which implies
 * an early/forced ending). actionRenew() will set the old lease to this
 * status instead of leaving it "Active" forever or misusing "Terminated".
 */
class m260829_010000_add_renewed_lease_status extends Migration
{
    public function safeUp()
    {
        $parentId = (new \yii\db\Query())
            ->select('id')
            ->from('list_source')
            ->where(['list_Name' => 'Lease Status', 'parent_id' => null])
            ->scalar();

        if (!$parentId) {
            echo "  > 'Lease Status' parent not found, skipping.\n";
            return;
        }

        $existing = (new \yii\db\Query())
            ->select('id')
            ->from('list_source')
            ->where(['list_Name' => 'Renewed', 'parent_id' => $parentId])
            ->scalar();

        if ($existing) {
            return;
        }

        $last = (new \yii\db\Query())
            ->select('uuid')
            ->from('list_source')
            ->where(['like', 'uuid', 'List_%', false])
            ->orderBy(['id' => SORT_DESC])
            ->scalar();
        $nextNum = $last ? ((int) str_replace('List_', '', $last)) + 1 : 1;

        do {
            $code = 'LIST' . rand(100, 999);
        } while ((new \yii\db\Query())->from('list_source')->where(['code' => $code])->exists());

        $this->insert('list_source', [
            'uuid' => 'List_' . $nextNum,
            'list_Name' => 'Renewed',
            'code' => $code,
            'category' => 'Lease Status',
            'sort_by' => '1',
            'parent_id' => $parentId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function safeDown()
    {
        $this->delete('list_source', ['list_Name' => 'Renewed']);
    }
}
