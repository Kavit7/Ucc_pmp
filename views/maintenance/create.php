<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MaintenanceRequest $model */
/** @var array $properties */
/** @var array $priorityOptions */

$this->title = 'Report an Issue';
$this->params['breadcrumbs'][] = ['label' => 'Maintenance Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container mt-5" style="max-width:600px;">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <?php if (empty($properties)): ?>
                <p class="text-muted">You don't have any active lease to report an issue against.</p>
            <?php else: ?>
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <?= $form->field($model, 'property_id')->dropDownList($properties, ['prompt' => 'Select Property']) ?>
                <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => 'e.g. Leaking kitchen tap']) ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 4, 'placeholder' => 'Describe the issue...']) ?>
                <?= $form->field($model, 'priority_id')->dropDownList($priorityOptions, ['prompt' => 'Select Priority']) ?>
                <?= $form->field($model, 'photoFile')->fileInput(['accept' => '.png,.jpg,.jpeg']) ?>

                <div class="d-flex gap-2 justify-content-end mt-3">
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    <?= Html::submitButton('Submit Request', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
