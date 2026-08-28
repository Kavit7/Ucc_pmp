<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var float $totalAmount */
/** @var array $statusOptions */
/** @var string|null $from */
/** @var string|null $to */
/** @var string|null $statusId */

$this->title = 'Revenue Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <h3 class="mb-0">Revenue Report — Total: TZS <?= number_format($totalAmount, 2) ?></h3>
        <div class="d-flex align-items-center gap-2">
            <div class="position-relative" style="max-width: 220px;">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color:#939292;"></i>
                <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search...">
            </div>
            <button id="exportBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                <i class="fas fa-file-export"></i> Export
            </button>
            <button id="printBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <?php $form = \yii\bootstrap5\ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['report/revenue']),
        'options' => ['class' => 'row g-2 align-items-end mb-3'],
    ]); ?>
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="from" value="<?= Html::encode($from) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="to" value="<?= Html::encode($to) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <?php foreach ($statusOptions as $id => $name): ?>
                    <option value="<?= $id ?>" <?= (string) $statusId === (string) $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?= Html::a('Reset', ['report/revenue'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    <?php \yii\bootstrap5\ActiveForm::end(); ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="reportTable">
            <thead>
                <tr>
                    <th>Bill UUID</th>
                    <th>Tenant</th>
                    <th>Property</th>
                    <th>Amount (TZS)</th>
                    <th>Due Date</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $bill): ?>
                    <tr>
                        <td><?= Html::encode($bill->uuid) ?></td>
                        <td><?= Html::encode($bill->lease->tenant->full_name ?? '-') ?></td>
                        <td><?= Html::encode($bill->lease->property->property_name ?? '-') ?></td>
                        <td><?= number_format($bill->amount, 2) ?></td>
                        <td><?= Html::encode($bill->due_date) ?></td>
                        <td><?= Html::encode($bill->paid_date ?? '-') ?></td>
                        <td><?= Html::encode($bill->billStatus->list_Name ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($dataProvider->getCount() === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No bills match this filter.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>

<?php
$this->registerJs(<<<JS
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#reportTable tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.getElementById('printBtn').addEventListener('click', function () {
    window.print();
});

document.getElementById('exportBtn').addEventListener('click', function () {
    const rows = [...document.querySelectorAll('#reportTable tr')].filter(r => r.style.display !== 'none');
    const csv = rows.map(row => [...row.querySelectorAll('th,td')].map(cell => '"' + cell.textContent.trim().replace(/"/g, '""') + '"').join(',')).join('\\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'revenue-report.csv';
    link.click();
});
JS
);
?>
