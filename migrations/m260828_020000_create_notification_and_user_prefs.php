<?php

use yii\db\Migration;

/**
 * Adds a real notification system: a `notification` table (one row per
 * recipient) and a per-user `notifications_enabled` preference that the
 * Settings page controls and Notification::notify() respects.
 */
class m260828_020000_create_notification_and_user_prefs extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'message' => $this->text()->null(),
            'link' => $this->string(255)->null(),
            'related_type' => $this->string(50)->null(),
            'related_id' => $this->integer()->null(),
            'is_read' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-notification-user_unread', '{{%notification}}', ['user_id', 'is_read']);
        $this->createIndex('idx-notification-uuid', '{{%notification}}', 'uuid', true);
        $this->createIndex(
            'idx-notification-dedupe',
            '{{%notification}}',
            ['user_id', 'related_type', 'related_id'],
            true
        );

        $this->addForeignKey(
            'fk-notification-user',
            '{{%notification}}',
            'user_id',
            '{{%users}}',
            'user_id',
            'CASCADE'
        );

        $this->addColumn('{{%users}}', 'notifications_enabled', $this->boolean()->notNull()->defaultValue(1)->after('profile_picture'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'notifications_enabled');
        $this->dropForeignKey('fk-notification-user', '{{%notification}}');
        $this->dropTable('{{%notification}}');
    }
}
