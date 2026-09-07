<?php

use yii\db\Migration;

/**
 * Tracks online rent payment attempts through Flutterwave, kept entirely
 * separate from bill/paid_date so the existing manual "staff records a
 * payment" flow (bill.paid_date, bill.bill_status, bill.receipt_url)
 * stays untouched. A row here that ends up 'successful' is what drives
 * PaymentGatewayController to mark the underlying bill paid.
 */
class m260907_030000_create_payment_transaction extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%payment_transaction}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull(),
            'bill_id' => $this->integer()->notNull(),
            'tenant_id' => $this->integer()->notNull(),
            'tx_ref' => $this->string(100)->notNull(),
            'flw_transaction_id' => $this->string(100)->null(),
            'amount' => $this->decimal(15, 2)->notNull(),
            'currency' => $this->string(10)->notNull()->defaultValue('TZS'),
            'status' => "ENUM('pending','successful','failed') NOT NULL DEFAULT 'pending'",
            'gateway_response' => $this->text()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-payment_transaction-uuid', '{{%payment_transaction}}', 'uuid', true);
        $this->createIndex('idx-payment_transaction-tx_ref', '{{%payment_transaction}}', 'tx_ref', true);
        $this->createIndex('idx-payment_transaction-bill_id', '{{%payment_transaction}}', 'bill_id');

        $this->addForeignKey('fk-payment_transaction-bill', '{{%payment_transaction}}', 'bill_id', '{{%bill}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-payment_transaction-tenant', '{{%payment_transaction}}', 'tenant_id', '{{%users}}', 'user_id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%payment_transaction}}');
    }
}
