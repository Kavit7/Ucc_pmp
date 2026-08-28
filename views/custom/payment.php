<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $payments app\models\Bill[] */

$this->title = 'Payments';

$totalCollected = array_sum(array_map(fn($p) => (float) $p->amount, $payments));
?>

<div class="container-fluid mt-4 payment-index">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <div>
            <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>
            <p class="text-muted mb-0 small"><?= count($payments) ?> recorded payment<?= count($payments) === 1 ? '' : 's' ?> &middot; TZS <?= number_format($totalCollected, 2) ?> total</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="position-relative" style="max-width: 220px;">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color:#939292;"></i>
                <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search...">
            </div>
            <button id="printBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                <i class="fas fa-print"></i> Print
            </button>
            <button id="exportBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                <i class="fas fa-file-export"></i> Export
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="paymentTable">
            <thead>
                <tr>
                    <th>Bill UUID</th>
                    <th>Tenant</th>
                    <th>Property</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Paid Date</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= Html::encode($payment->uuid) ?></td>
                        <td><?= Html::encode($payment->lease->tenant->full_name ?? '-') ?></td>
                        <td><?= Html::encode($payment->lease->property->property_name ?? '-') ?></td>
                        <td>TZS <?= number_format($payment->amount, 2) ?></td>
                        <td><?= Yii::$app->formatter->asDate($payment->due_date) ?></td>
                        <td><?= $payment->paid_date ? Yii::$app->formatter->asDate($payment->paid_date) : '-' ?></td>
                        <td>
                            <?php if ($payment->receipt_url): ?>
                                <?= Html::a('<i class="fas fa-receipt"></i> View', Yii::getAlias('@web/' . $payment->receipt_url), [
                                    'class' => 'btn btn-sm btn-outline-secondary',
                                    'target' => '_blank',
                                ]) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5">No payments have been recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .payment-index { font-family: 'Inter', 'Roboto', sans-serif; }
</style>

<?php
$js = <<<JS
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#paymentTable tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.getElementById('printBtn').addEventListener('click', function () {
    window.print();
});

document.getElementById('exportBtn').addEventListener('click', function () {
    const rows = [...document.querySelectorAll('#paymentTable tr')].filter(r => r.style.display !== 'none');
    const csv = rows.map(row => [...row.querySelectorAll('th,td')].map(cell => '"' + cell.textContent.trim().replace(/"/g, '""') + '"').join(',')).join('\\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'payments.csv';
    link.click();
});
JS;
$this->registerJs($js);
?>
