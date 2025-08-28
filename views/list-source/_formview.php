<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin();
?>

<!-- User input fields -->
<?= $form->field($model, 'list_Name')->textInput(['placeholder' => 'Enter List Name']) ?>
<?= $form->field($model, 'category')->textInput(['placeholder' => 'Enter Category']) ?>
<?= $form->field($model, 'description')->textarea(['placeholder' => 'Enter Description']) ?>

<!-- System-generated fields (hidden) -->
<?= $form->field($model, 'code')->hiddenInput(['value' => $model->generateCode()])->label(false) ?>
<?= $form->field($model, 'sort_by')->hiddenInput(['value' => $model->generateSort()])->label(false) ?>
<?= $form->field($model, 'parent_id')->hiddenInput(['value' => $model->generateParentId()])->label(false) ?>

<!-- Submit button -->
<?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
