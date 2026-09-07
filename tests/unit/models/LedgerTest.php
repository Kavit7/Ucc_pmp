<?php

namespace tests\unit\models;

use app\components\Ledger;
use app\models\GlAccount;
use app\models\GlJournalEntry;

/**
 * Covers Ledger::post() - the general ledger's one entry point, and the
 * only place the "debits always equal credits" invariant can be broken.
 * An unbalanced entry, or one referencing an account code that doesn't
 * exist, must never make it into gl_journal_line.
 */
class LedgerTest extends \Codeception\Test\Unit
{
    private $createdEntryIds = [];

    protected function _after()
    {
        foreach ($this->createdEntryIds as $id) {
            $entry = GlJournalEntry::findOne($id);
            if ($entry) {
                $entry->delete(); // cascades to gl_journal_line
            }
        }
    }

    public function testBalancedEntryPostsBothLinesAndUpdatesAccountBalances()
    {
        $cash = GlAccount::find()->where(['code' => '1000'])->one();
        $income = GlAccount::find()->where(['code' => '4000'])->one();
        verify($cash)->notNull();
        verify($income)->notNull();

        $cashBefore = $cash->balance();
        $incomeBefore = $income->balance();

        $entry = Ledger::post(date('Y-m-d'), 'Unit test entry', [
            ['account' => '1000', 'debit' => 12345],
            ['account' => '4000', 'credit' => 12345],
        ], 'unit_test', 999);
        $this->createdEntryIds[] = $entry->id;

        verify($entry->lines)->arrayCount(2);
        verify($cash->balance() - $cashBefore)->equals(12345.0);
        verify($income->balance() - $incomeBefore)->equals(12345.0);
    }

    public function testUnbalancedEntryIsRejectedAndNothingIsSaved()
    {
        $countBefore = GlJournalEntry::find()->count();

        $threw = false;
        try {
            Ledger::post(date('Y-m-d'), 'Unbalanced entry', [
                ['account' => '1000', 'debit' => 500],
                ['account' => '4000', 'credit' => 400],
            ]);
        } catch (\RuntimeException $e) {
            $threw = true;
        }

        verify($threw)->true();
        verify(GlJournalEntry::find()->count())->equals($countBefore);
    }

    public function testUnknownAccountCodeIsRejectedAndRollsBackTheWholeEntry()
    {
        $countBefore = GlJournalEntry::find()->count();

        $threw = false;
        try {
            Ledger::post(date('Y-m-d'), 'Bad account code', [
                ['account' => '1000', 'debit' => 500],
                ['account' => '9999', 'credit' => 500],
            ]);
        } catch (\RuntimeException $e) {
            $threw = true;
        }

        verify($threw)->true();
        // The 1000 line must not have been left behind even though it was valid.
        verify(GlJournalEntry::find()->count())->equals($countBefore);
    }

    public function testPostSafelyNeverThrowsAndReturnsNullOnFailure()
    {
        $result = Ledger::postSafely(date('Y-m-d'), 'Unbalanced but safe', [
            ['account' => '1000', 'debit' => 500],
            ['account' => '4000', 'credit' => 1],
        ]);

        verify($result)->null();
    }
}
