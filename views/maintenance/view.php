<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MaintenanceRequest $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Maintenance Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusName = strtolower($model->status->list_Name ?? '');
$statusClass = match ($statusName) {
    'open' => 'text-danger border-danger',
    'in progress' => 'text-warning border-warning',
    'resolved', 'closed' => 'text-success border-success',
    default => 'text-secondary border-secondary',
};
?>

<div class="container mt-5" style="max-width:600px;">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <span class="badge <?= $statusClass ?> mb-3"><?= Html::encode($model->status->list_Name ?? '-') ?></span>

            <p><?= nl2br(Html::encode($model->description)) ?></p>

            <div class="row small text-muted mt-3">
                <div class="col-6 mb-2">Property: <strong><?= Html::encode($model->property->property_name ?? '-') ?></strong></div>
                <div class="col-6 mb-2">Priority: <strong><?= Html::encode($model->priority->list_Name ?? '-') ?></strong></div>
                <div class="col-6 mb-2">Reported: <strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></strong></div>
                <div class="col-6 mb-2">Assigned To: <strong><?= Html::encode($model->assignedTo->full_name ?? 'Unassigned') ?></strong></div>
                <?php if ($model->resolved_at): ?>
                    <div class="col-6 mb-2">Resolved: <strong><?= Yii::$app->formatter->asDatetime($model->resolved_at) ?></strong></div>
                <?php endif; ?>
            </div>

            <?php if ($model->photo_url): ?>
                <img src="<?= Yii::getAlias('@web/' . $model->photo_url) ?>" alt="Issue photo" class="img-fluid rounded mt-3" style="max-height:300px;">
            <?php endif; ?>

            <div class="mt-4">
                <?= Html::a('&larr; Back to Requests', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    </div>
</div>
