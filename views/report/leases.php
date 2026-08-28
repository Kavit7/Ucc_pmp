<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $statusOptions */
/** @var string|null $statusId */

$this->title = 'Lease Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <h3 class="mb-0">Lease Report</h3>
        <div class="d-flex align-items-center gap-2">
            <div class="position-relative" style="max-width: 220px;">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color:#939292;"></i>
                <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search...">
            </div>
            <form method="get" action="<?= Url::to(['report/leases']) ?>" class="d-flex gap-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    <?php foreach ($statusOptions as $id => $name): ?>
                        <option value="<?= $id ?>" <?= (string) $statusId === (string) $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button id="printBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="reportTable">
            <thead>
                <tr>
                    <th>Lease UUID</th>
                    <th>Tenant</th>
                    <th>Property</th>
                    <th>Rent (TZS)</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $lease): ?>
                    <tr>
                        <td><?= Html::encode($lease->uuid) ?></td>
                        <td><?= Html::encode($lease->tenant->full_name ?? '-') ?></td>
                        <td><?= Html::encode($lease->property->property_name ?? '-') ?></td>
                        <td><?= number_format($lease->propertyPrice->unit_amount ?? 0, 2) ?></td>
                        <td><?= Html::encode($lease->lease_start_date) ?></td>
                        <td><?= Html::encode($lease->lease_end_date) ?></td>
                        <td><?= Html::encode($lease->statusLabel->list_Name ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($dataProvider->getCount() === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No leases match this filter.</td></tr>
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
JS
);
?>
