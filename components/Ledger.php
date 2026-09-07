<?php

namespace app\components;

use Yii;
use Throwable;
use RuntimeException;
use app\models\GlAccount;
use app\models\GlJournalEntry;
use app\models\GlJournalLine;

/**
 * Posts balanced double-entry journal entries. This is the only place a
 * gl_journal_entry/gl_journal_line pair should ever be created - callers
 * describe the business event, Ledger::post() enforces that debits equal
 * credits and that every account code is real, in one DB transaction.
 *
 * Usage:
 *   Ledger::post(date('Y-m-d'), 'Rent billed: lease #12', [
 *       ['account' => '1100', 'debit' => 500000],
 *       ['account' => '4000', 'credit' => 500000],
 *   ], 'bill', $bill->id);
 */
class Ledger
{
    /**
     * @param string $entryDate Y-m-d
     * @param string $description
     * @param array $lines each ['account' => code, 'debit' => n] or ['account' => code, 'credit' => n, 'memo' => optional]
     * @param string|null $referenceType e.g. 'bill', 'payment', 'expense'
     * @param int|null $referenceId
     * @return GlJournalEntry
     * @throws RuntimeException if the entry doesn't balance or an account code is unknown
     */
    public static function post($entryDate, $description, array $lines, $referenceType = null, $referenceId = null)
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }
        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new RuntimeException(sprintf(
                'Ledger::post(): unbalanced entry (%.2f debit vs %.2f credit) for "%s".',
                $totalDebit,
                $totalCredit,
                $description
            ));
        }

        $accountsByCode = GlAccount::find()->indexBy('code')->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $entry = new GlJournalEntry([
                'entry_date' => $entryDate,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_by' => Yii::$app->has('user') ? (Yii::$app->user->id ?? null) : null,
            ]);
            if (!$entry->save()) {
                throw new RuntimeException('Ledger::post(): could not save journal entry: ' . implode(' ', $entry->getFirstErrors()));
            }

            foreach ($lines as $line) {
                $code = $line['account'];
                if (!isset($accountsByCode[$code])) {
                    throw new RuntimeException("Ledger::post(): unknown account code \"{$code}\".");
                }

                $glLine = new GlJournalLine([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $accountsByCode[$code]->id,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'memo' => $line['memo'] ?? null,
                ]);
                if (!$glLine->save()) {
                    throw new RuntimeException('Ledger::post(): could not save journal line: ' . implode(' ', $glLine->getFirstErrors()));
                }
            }

            $transaction->commit();
            return $entry;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Same as post(), but never throws - logs and returns null instead.
     * The ledger is a record of what happened elsewhere in the app; a
     * posting failure should never be the reason a bill or payment fails
     * to save.
     */
    public static function postSafely($entryDate, $description, array $lines, $referenceType = null, $referenceId = null)
    {
        try {
            return self::post($entryDate, $description, $lines, $referenceType, $referenceId);
        } catch (Throwable $e) {
            Yii::error('Ledger posting failed: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }
}
