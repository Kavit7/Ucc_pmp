<?php

use yii\db\Migration;

class m260830_010000_create_audit_log extends Migration
{
    public function safeUp()
    {
        $this->createTable('audit_log', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull()->unique(),
            'user_id' => $this->integer()->null(),
            'action' => $this->string(20)->notNull(),
            'model_name' => $this->string(100)->notNull(),
            'model_id' => $this->string(50)->notNull(),
            'changes' => $this->text()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-audit_log-model', 'audit_log', ['model_name', 'model_id']);
        $this->createIndex('idx-audit_log-user_id', 'audit_log', 'user_id');

        $this->addForeignKey(
            'fk-audit_log-user_id',
            'audit_log',
            'user_id',
            'users',
            'user_id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-audit_log-user_id', 'audit_log');
        $this->dropTable('audit_log');
    }
}
