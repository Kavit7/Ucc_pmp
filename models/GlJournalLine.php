<?php

namespace app\models;

use yii\db\ActiveRecord;

class GlJournalLine extends ActiveRecord
{
    public static function tableName()
    {
        return 'gl_journal_line';
    }

    public function rules()
    {
        return [
            [['journal_entry_id', 'account_id'], 'required'],
            [['journal_entry_id', 'account_id'], 'integer'],
            [['debit', 'credit'], 'number', 'min' => 0],
            [['memo'], 'string', 'max' => 255],
        ];
    }

    public function getEntry()
    {
        return $this->hasOne(GlJournalEntry::class, ['id' => 'journal_entry_id']);
    }

    public function getAccount()
    {
        return $this->hasOne(GlAccount::class, ['id' => 'account_id']);
    }
}
