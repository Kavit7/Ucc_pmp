<?php

use yii\db\Migration;

class m260830_030000_create_property_inquiry extends Migration
{
    public function safeUp()
    {
        $this->createTable('property_inquiry', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull()->unique(),
            'property_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'phone' => $this->string(50)->null(),
            'message' => $this->text()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-property_inquiry-property_id',
            'property_inquiry',
            'property_id',
            'property',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-property_inquiry-property_id', 'property_inquiry');
        $this->dropTable('property_inquiry');
    }
}
