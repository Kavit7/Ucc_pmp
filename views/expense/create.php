<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Expense $model */
/** @var array $properties */

$this->title = 'Log Expense';
?>

<div class="container mt-5" style="max-width: 560px;">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'property_id')->dropDownList($properties, ['prompt' => 'Select a property']) ?>
            <?= $form->field($model, 'expense_type')->textInput(['maxlength' => true, 'placeholder' => 'e.g. Plumbing repair, Utility bill, Property tax']) ?>
            <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

            <div class="d-flex gap-2 mt-3">
                <?= Html::submitButton('Save Expense', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
