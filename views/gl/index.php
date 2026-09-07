<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $rows each ['account' => GlAccount, 'balance' => float] */
/** @var float $totalDebitBalances */
/** @var float $totalCreditBalances */

$this->title = 'Trial Balance';

$typeLabels = [
    'asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity',
    'revenue' => 'Revenue', 'expense' => 'Expense',
];
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3 flex-wrap gap-2"
        style="background-color:#ffffff; border-radius:8px;">
        <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>
        <?= Html::a('<i class="fas fa-book me-1"></i> View Journal', ['journal'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th class="text-end">Debit Balance</th>
                    <th class="text-end">Credit Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): $account = $row['account']; $balance = $row['balance']; ?>
                    <tr>
                        <td><?= Html::encode($account->code) ?></td>
                        <td><?= Html::encode($account->name) ?></td>
                        <td><?= Html::encode($typeLabels[$account->type] ?? $account->type) ?></td>
                        <td class="text-end"><?= $account->normal_balance === 'debit' ? number_format($balance, 2) : '-' ?></td>
                        <td class="text-end"><?= $account->normal_balance === 'credit' ? number_format($balance, 2) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3">Total</td>
                    <td class="text-end">TZS <?= number_format($totalDebitBalances, 2) ?></td>
                    <td class="text-end">TZS <?= number_format($totalCreditBalances, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if (abs($totalDebitBalances - $totalCreditBalances) > 0.01): ?>
        <div class="alert alert-danger">
            Debits and credits don't match (TZS <?= number_format($totalDebitBalances, 2) ?> vs TZS <?= number_format($totalCreditBalances, 2) ?>). Something posted outside <code>Ledger::post()</code> - check the <?= Html::a('journal', ['journal']) ?> for the entry that broke the balance.
        </div>
    <?php else: ?>
        <p class="text-muted">Debits and credits match. The ledger is balanced.</p>
    <?php endif; ?>
</div>
