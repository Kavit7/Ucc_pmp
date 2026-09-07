<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * A line in the chart of accounts. Balances aren't stored here - they're
 * derived by summing gl_journal_line.debit/credit for the account, so the
 * ledger can never drift out of sync with itself.
 */
class GlAccount extends ActiveRecord
{
    public static function tableName()
    {
        return 'gl_account';
    }

    public function rules()
    {
        return [
            [['code', 'name', 'type', 'normal_balance'], 'required'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
            [['type'], 'in', 'range' => ['asset', 'liability', 'equity', 'revenue', 'expense']],
            [['normal_balance'], 'in', 'range' => ['debit', 'credit']],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * The account's balance as of today (or a given date), signed
     * according to its normal balance - e.g. a debit-normal asset account
     * with more debits than credits shows as positive.
     */
    public function balance($asOfDate = null)
    {
        $query = GlJournalLine::find()
            ->joinWith('entry')
            ->where(['gl_journal_line.account_id' => $this->id]);
        if ($asOfDate) {
            $query->andWhere(['<=', 'gl_journal_entry.entry_date', $asOfDate]);
        }

        $sumDebit = (float) (clone $query)->sum('gl_journal_line.debit');
        $sumCredit = (float) (clone $query)->sum('gl_journal_line.credit');

        return $this->normal_balance === 'debit' ? $sumDebit - $sumCredit : $sumCredit - $sumDebit;
    }

    public function getLines()
    {
        return $this->hasMany(GlJournalLine::class, ['account_id' => 'id']);
    }
}
