<?php

use yii\db\Migration;

/**
 * Adds maintenance/work-order tracking: tenants report issues against
 * their leased property, staff triage/assign/resolve them. Status and
 * priority are list_source-driven, matching the Bill/Lease Status
 * pattern already used elsewhere in the app.
 */
class m260829_000000_create_maintenance_request extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%maintenance_request}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull(),
            'property_id' => $this->integer()->notNull(),
            'lease_id' => $this->integer()->null(),
            'reported_by' => $this->integer()->notNull(),
            'assigned_to' => $this->integer()->null(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
            'priority_id' => $this->integer()->null(),
            'status_id' => $this->integer()->notNull(),
            'photo_url' => $this->string(255)->null(),
            'resolved_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-maintenance-uuid', '{{%maintenance_request}}', 'uuid', true);
        $this->createIndex('idx-maintenance-property', '{{%maintenance_request}}', 'property_id');
        $this->createIndex('idx-maintenance-reported_by', '{{%maintenance_request}}', 'reported_by');
        $this->createIndex('idx-maintenance-status', '{{%maintenance_request}}', 'status_id');

        $this->addForeignKey('fk-maintenance-property', '{{%maintenance_request}}', 'property_id', '{{%property}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-maintenance-reported_by', '{{%maintenance_request}}', 'reported_by', '{{%users}}', 'user_id', 'CASCADE');
        $this->addForeignKey('fk-maintenance-assigned_to', '{{%maintenance_request}}', 'assigned_to', '{{%users}}', 'user_id', 'SET NULL');

        // --- Seed status list (Open -> In Progress -> Resolved -> Closed) ---
        $statusParentId = $this->seedListParent('Maintenance Status');
        $this->seedListChild($statusParentId, 'Open');
        $this->seedListChild($statusParentId, 'In Progress');
        $this->seedListChild($statusParentId, 'Resolved');
        $this->seedListChild($statusParentId, 'Closed');

        // --- Seed priority list ---
        $priorityParentId = $this->seedListParent('Maintenance Priority');
        $this->seedListChild($priorityParentId, 'Low');
        $this->seedListChild($priorityParentId, 'Medium');
        $this->seedListChild($priorityParentId, 'High');
        $this->seedListChild($priorityParentId, 'Urgent');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-maintenance-assigned_to', '{{%maintenance_request}}');
        $this->dropForeignKey('fk-maintenance-reported_by', '{{%maintenance_request}}');
        $this->dropForeignKey('fk-maintenance-property', '{{%maintenance_request}}');
        $this->dropTable('{{%maintenance_request}}');
    }

    private $nextListUuidNumber;

    private function nextListUuid()
    {
        if ($this->nextListUuidNumber === null) {
            // The highest *numeric suffix* in use, not the row with the
            // highest auto-increment id - seed data inserted out of
            // numeric order (e.g. reconstructed historical uuids with
            // gaps) would otherwise make this pick a stale/lower number
            // and collide with an existing uuid.
            $max = 0;
            foreach ((new \yii\db\Query())->select('uuid')->from('list_source')->where(['like', 'uuid', 'List_%', false])->column() as $uuid) {
                $max = max($max, (int) str_replace('List_', '', $uuid));
            }
            $this->nextListUuidNumber = $max + 1;
        }
        return 'List_' . $this->nextListUuidNumber++;
    }

    /**
     * A random 'LIST'+3-digits code has a real chance of colliding with
     * one already in use (code is unique) - loop until a free one is found,
     * matching the safer pattern already used elsewhere in this app's
     * migrations (see m260829_010000_add_renewed_lease_status).
     */
    private function nextListCode()
    {
        do {
            $code = 'LIST' . rand(100, 999);
        } while ((new \yii\db\Query())->from('list_source')->where(['code' => $code])->exists());
        return $code;
    }

    private function seedListParent($name)
    {
        $existing = (new \yii\db\Query())->select('id')->from('list_source')->where(['list_Name' => $name, 'parent_id' => null])->scalar();
        if ($existing) {
            return $existing;
        }

        $this->insert('list_source', [
            'uuid' => $this->nextListUuid(),
            'list_Name' => $name,
            'code' => $this->nextListCode(),
            'category' => $name,
            'sort_by' => '1',
            'parent_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return (int) Yii::$app->db->getLastInsertID();
    }

    private function seedListChild($parentId, $name)
    {
        $existing = (new \yii\db\Query())->select('id')->from('list_source')->where(['list_Name' => $name, 'parent_id' => $parentId])->scalar();
        if ($existing) {
            return $existing;
        }

        $this->insert('list_source', [
            'uuid' => $this->nextListUuid(),
            'list_Name' => $name,
            'code' => $this->nextListCode(),
            'category' => $name,
            'sort_by' => '1',
            'parent_id' => $parentId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return (int) Yii::$app->db->getLastInsertID();
    }
}
