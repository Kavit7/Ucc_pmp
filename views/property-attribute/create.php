<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin(); ?>

<div class="form-container">
    <div>
        <h5>Configure Extra Data</h5>

        <!-- Display all validation errors -->
        <?= $form->errorSummary($model) ?>
    </div>

    <div class="form-row">
        <div class="form-group">

            <?= $form->field($model, 'property_type_id')->dropDownList(
                $childProperty,
                [
                    'prompt' => 'Select The Property Type',
                    'class' => 'form-control',
                    'id'=>'property_id'
                ]
            ) ?>

            <?= $form->field($model, 'attribute_name')->textInput([
                'placeholder' => 'Enter Label',
                'class' => 'form-control'
            ]) ?>

            <?= $form->field($model, 'attribute_name_dataType_id')->dropDownList(
                $childDataType,
                [
                    'prompt' => 'Select Data type',
                    'class' => 'form-control'
                ]
            ) ?>

            <?= Html::submitButton('Save', ['class' => 'btn btn-success mt-3']) ?>

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
