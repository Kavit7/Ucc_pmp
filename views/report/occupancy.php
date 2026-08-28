<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var array $byStatus */
/** @var array $byUsage */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Occupancy Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid mt-4 body">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">By Status</h5>
                    <table class="table table-sm">
                        <?php foreach ($byStatus as $row): ?>
                            <tr>
                                <td><?= Html::encode($row['label'] ?? 'Unset') ?></td>
                                <td class="text-end fw-bold"><?= $row['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">By Usage Type</h5>
                    <table class="table table-sm">
                        <?php foreach ($byUsage as $row): ?>
                            <tr>
                                <td><?= Html::encode($row['label'] ?? 'Unset') ?></td>
                                <td class="text-end fw-bold"><?= $row['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 mt-4 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <h3 class="mb-0">Properties</h3>
        <div class="position-relative" style="max-width: 220px;">
            <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color:#939292;"></i>
            <input type="text" id="searchInput" class="form-control ps-5" placeholder="Search...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="reportTable">
            <thead>
                <tr>
                    <th>Property</th>
                    <th>Identifier</th>
                    <th>Status</th>
                    <th>Usage Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $property): ?>
                    <tr>
                        <td><?= Html::encode($property->property_name) ?></td>
                        <td><?= Html::encode($property->identifier_code) ?></td>
                        <td><?= Html::encode($property->propertyStatus->list_Name ?? '-') ?></td>
                        <td><?= Html::encode($property->usageType->list_Name ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($dataProvider->getCount() === 0): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">No properties found.</td></tr>
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
JS
);
?>
