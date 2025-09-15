<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
$indexUrl= Url::to(['list-source/create']);
?>

<?php $form = ActiveForm::begin(); ?>

<div class="form-container bg-white p-4 rounded">
    <div>
        <h5 class="text-primary text-center">Configure Extra Data</h5>

        <!-- Display all validation errors -->
        <?= $form->errorSummary($model) ?>
    </div>

    <div class="form-row">
        <div class="form-group">

            <?= $form->field($model, 'property_type_id')->dropDownList(
                $childProperty,
                [
                    'prompt' => 'Select The Property Type',
                    'class' => 'form-control styled-select',
                    'id'=>'property_id'
                ]
            ) ?>

            <?= $form->field($model, 'attribute_name')->textInput([
                'placeholder' => 'Enter Label',
                'class' => 'form-control styled-input'
            ]) ?>

            <?= $form->field($model, 'attribute_name_dataType_id')->dropDownList(
                $childDataType,
                [
                    'prompt' => 'Select Data type',
                    'class' => 'form-control styled-select'
                ]
            ) ?>
            <div class="d-flex align-items-center justify-content-between">
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary mt-3']) ?>
             <?= Html::a('&larr; Back', $indexUrl, ['class' => 'text-decoration-none fw-bold']) ?>
             </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<style>
       :root {
        --primary: #4f46e5;
        --primary-dark: #4338ca;
        --secondary: #10b981;
        --light-bg: #f9fafb;
        --dark-text: #1f2937;
        --mid-text: #4b5563;
        --light-text: #6b7280;
        --border-color: #e5e7eb;
        --success: #10b981;
    }
.styled-input, .styled-select, .styled-textarea {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--dark-text);
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: var(--light-bg);
    }
    
    .styled-input:focus, .styled-select:focus, .styled-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        background-color: #ffffff;
    }
    .has-error{
        color: red;
    }
    

</style>
