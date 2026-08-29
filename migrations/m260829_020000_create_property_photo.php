<?php

use yii\db\Migration;

/**
 * Properties previously had exactly one file total (document_url),
 * shared between "property photo" and "legal document". This adds a
 * proper multi-photo gallery, leaving document_url as-is for the
 * single legal document (title deed, etc.) so existing data is
 * untouched.
 */
class m260829_020000_create_property_photo extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%property_photo}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull(),
            'property_id' => $this->integer()->notNull(),
            'photo_url' => $this->string(255)->notNull(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-property_photo-uuid', '{{%property_photo}}', 'uuid', true);
        $this->createIndex('idx-property_photo-property', '{{%property_photo}}', 'property_id');
        $this->addForeignKey('fk-property_photo-property', '{{%property_photo}}', 'property_id', '{{%property}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-property_photo-property', '{{%property_photo}}');
        $this->dropTable('{{%property_photo}}');
    }
}
