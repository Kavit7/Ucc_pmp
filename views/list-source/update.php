<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin();
?>

<!-- User input fields -->
<div class="d-flex gap-2">
<?= $form->field($model, 'list_Name')->textInput(['placeholder' => 'Enter List Name','style'=>'width:450px'],) ?>
<?= $form->field($model, 'category')->textInput(['placeholder' => 'Enter Category','style'=>'width:450px']) ?>
</div>
<?= $form->field($model, 'description')->textarea(['placeholder' => 'Enter Description']) ?>

<!-- System-generated fields (hidden) -->

<div>
    <!-- Submit button -->
    <?= Html::submitButton('Update', ['class' => 'btn mt-4']) ?>

    <!-- Cancel button -->
    <?= Html::a('Cancel', ['create'], ['class' => 'btn mt-4','style'=>'background-color:red !important; color:white;']) ?>
</div>

<?php ActiveForm::end(); ?>
