<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Bill $bill */

$this->title = 'Record Payment';
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['custom/bill']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container mt-5" style="max-width:520px;">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="mb-3 pb-3" style="border-bottom:1px solid #e5e7eb;">
                <div class="text-muted small">Property</div>
                <div class="fw-bold"><?= Html::encode($bill->lease->property->property_name ?? '-') ?></div>

                <div class="text-muted small mt-2">Tenant</div>
                <div class="fw-bold"><?= Html::encode($bill->lease->tenant->full_name ?? '-') ?></div>

                <div class="text-muted small mt-2">Amount Due</div>
                <div class="fw-bold" style="font-size:1.3rem; color:#4f46e5;">TZS <?= number_format($bill->amount, 2) ?></div>
            </div>

            <?= Html::beginForm(['custom/record-payment', 'id' => $bill->id], 'post', ['enctype' => 'multipart/form-data']) ?>
                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="paid_date" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Receipt (optional)</label>
                    <input type="file" name="receiptFile" class="form-control" accept=".png,.jpg,.jpeg,.pdf">
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <?= Html::a('Cancel', ['custom/bill'], ['class' => 'btn btn-outline-secondary']) ?>
                    <?= Html::submitButton('<i class="fas fa-check me-1"></i> Record Payment', ['class' => 'btn btn-primary']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
