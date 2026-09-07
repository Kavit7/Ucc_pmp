<?php

use yii\db\Migration;

/**
 * A lightweight general ledger sitting on top of the existing Bill/payment
 * flow, which stays exactly as it is. Three tables: a small chart of
 * accounts, journal entries (one per business event), and their debit/
 * credit lines. CustomController posts to it when a bill is created or
 * paid; ExpenseController posts to it when a property expense is logged -
 * see Ledger::post() for the actual double-entry mechanics.
 */
class m260907_020000_create_general_ledger extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%gl_account}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(10)->notNull(),
            'name' => $this->string(100)->notNull(),
            'type' => "ENUM('asset','liability','equity','revenue','expense') NOT NULL",
            'normal_balance' => "ENUM('debit','credit') NOT NULL",
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
        ]);
        $this->createIndex('idx-gl_account-code', '{{%gl_account}}', 'code', true);

        $this->createTable('{{%gl_journal_entry}}', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(100)->notNull(),
            'entry_date' => $this->date()->notNull(),
            'description' => $this->string(255)->notNull(),
            'reference_type' => $this->string(50)->null(),
            'reference_id' => $this->integer()->null(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx-gl_journal_entry-uuid', '{{%gl_journal_entry}}', 'uuid', true);
        $this->createIndex('idx-gl_journal_entry-reference', '{{%gl_journal_entry}}', ['reference_type', 'reference_id']);
        $this->addForeignKey('fk-gl_journal_entry-created_by', '{{%gl_journal_entry}}', 'created_by', '{{%users}}', 'user_id', 'SET NULL');

        $this->createTable('{{%gl_journal_line}}', [
            'id' => $this->primaryKey(),
            'journal_entry_id' => $this->integer()->notNull(),
            'account_id' => $this->integer()->notNull(),
            'debit' => $this->decimal(15, 2)->notNull()->defaultValue(0),
            'credit' => $this->decimal(15, 2)->notNull()->defaultValue(0),
            'memo' => $this->string(255)->null(),
        ]);
        $this->addForeignKey('fk-gl_journal_line-entry', '{{%gl_journal_line}}', 'journal_entry_id', '{{%gl_journal_entry}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-gl_journal_line-account', '{{%gl_journal_line}}', 'account_id', '{{%gl_account}}', 'id', 'RESTRICT');

        // Minimal chart of accounts - just the accounts the app actually
        // posts to today (rent billing, payment collection, property
        // expenses). Add more only when something will actually post to them.
        $this->batchInsert('{{%gl_account}}', ['code', 'name', 'type', 'normal_balance'], [
            ['1000', 'Cash & Bank', 'asset', 'debit'],
            ['1100', 'Rent Receivable', 'asset', 'debit'],
            ['4000', 'Rental Income', 'revenue', 'credit'],
            ['5000', 'Property Expenses', 'expense', 'debit'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%gl_journal_line}}');
        $this->dropTable('{{%gl_journal_entry}}');
        $this->dropTable('{{%gl_account}}');
    }
}
