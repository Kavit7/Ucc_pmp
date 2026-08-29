<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MaintenanceRequest $model */
/** @var array $statusOptions */
/** @var array $technicians */

$this->title = 'Manage Request';
$this->params['breadcrumbs'][] = ['label' => 'Maintenance Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container mt-5" style="max-width:640px;">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h5><?= Html::encode($model->title) ?></h5>
            <p class="text-muted mb-2"><?= Html::encode($model->description) ?></p>
            <div class="row small text-muted">
                <div class="col-6">Property: <strong><?= Html::encode($model->property->property_name ?? '-') ?></strong></div>
                <div class="col-6">Reported by: <strong><?= Html::encode($model->reportedBy->full_name ?? '-') ?></strong></div>
                <div class="col-6">Priority: <strong><?= Html::encode($model->priority->list_Name ?? '-') ?></strong></div>
                <div class="col-6">Reported: <strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></strong></div>
            </div>
            <?php if ($model->photo_url): ?>
                <img src="<?= Yii::getAlias('@web/' . $model->photo_url) ?>" alt="Issue photo" class="img-fluid rounded mt-3" style="max-height:250px;">
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'status_id')->dropDownList($statusOptions) ?>
            <?= $form->field($model, 'assigned_to')->dropDownList($technicians, ['prompt' => 'Unassigned']) ?>

            <div class="d-flex gap-2 justify-content-end mt-3">
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
