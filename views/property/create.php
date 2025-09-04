<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Property */
/* @var $childProperty array */
/* @var $childUsage array */
/* @var $childOwner array */
/* @var $childStatus array */

$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css");
?>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
    
    body {
        font-family: 'Inter', 'Roboto', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
    }
    
    .form-card {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 3rem;
        background-color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }
    
    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary), #3b82f6, var(--secondary));
    }
    
    .form-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }
    
    .form-header h5 {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--dark-text);
        margin-bottom: 0.5rem;
    }
    
    .form-header p {
        color: var(--light-text);
        font-size: 1.125rem;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--mid-text);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
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
    
    .styled-textarea {
        min-height: 120px;
        resize: vertical;
    }
    
    .form-group {
        margin-bottom: 1.75rem;
    }
    
    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 2.5rem;
        font-size: 1.125rem;
        font-weight: 600;
        color: white;
        background: linear-gradient(90deg, var(--primary), #6366f1);
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.25);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(79, 70, 229, 0.3);
        background: linear-gradient(90deg, var(--primary-dark), var(--primary));
    }
    
    .btn-submit:active {
        transform: translateY(0);
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark-text);
        margin: 2.5rem 0 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border-color);
        position: relative;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary), #3b82f6);
    }
    
    .dynamic-placeholder {
        text-align: center;
        padding: 3rem 2rem;
        background-color: var(--light-bg);
        border-radius: 12px;
        border: 2px dashed var(--border-color);
        color: var(--light-text);
    }
    
    .dynamic-placeholder i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #9ca3af;
        display: block;
    }
    
    .field-error {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }
    
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }
        
        .form-header h5 {
            font-size: 1.75rem;
        }
        
        .section-title {
            font-size: 1.25rem;
        }
    }
</style>

<div class="form-card">
    <div class="form-header">
        <h5>ADD NEW PROPERTY</h5>
        <p>Please fill out the form below with the property details</p>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <h3 class="section-title">Basic Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="form-group">
            <label class="form-label">Property Name</label>
            <?= $form->field($model, 'property_name')->textInput([
                'placeholder' => 'Enter Property name',
                'class' => 'styled-input',
            ])->label(false) ?>
        </div>
        <div class="form-group">
            <label class="form-label">Property Type</label>
            <?= $form->field($model, 'property_type_id')->dropDownList($childProperty, [
                'prompt' => 'Select property type',
                'id' => 'property_type_id',
                'class' => 'styled-select'
            ])->label(false) ?>
        </div>
        <div class="form-group">
            <label class="form-label">Identifier Code</label>
            <?= $form->field($model, 'identifier_code')->textInput([
                'placeholder' => 'Identifier Code',
                'class' => 'styled-input'
            ])->label(false) ?>
        </div>
        <div class="form-group">
            <label class="form-label">Usage Type</label>
            <?= $form->field($model, 'usage_type_id')->dropDownList($childUsage, [
                'prompt' => 'Select Usage Type',
                'class' => 'styled-select'
            ])->label(false) ?>
        </div>
        <div class="form-group">
            <label class="form-label">Ownership Type</label>
            <?= $form->field($model, 'ownership_type_id')->dropDownList($childOwner, [
                'prompt' => 'Select Ownership Type',
                'class' => 'styled-select'
            ])->label(false) ?>
        </div>
        <div class="form-group">
            <label class="form-label">Document URL</label>
            <?= $form->field($model, 'document_url')->fileInput([
                'class' => 'styled-input'
            ])->label(false) ?>
        </div>
    </div>

    <h3 class="section-title">Description</h3>
    <div class="form-group">
        <?= $form->field($model, 'description')->textArea([
            'placeholder' => 'Enter Description here',
            'class' => 'styled-textarea'
        ])->label(false) ?>
    </div>

    <h3 class="section-title">Additional Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="form-group">
            <label class="form-label">Property Status</label>
            <?= $form->field($model, 'property_status_id')->dropDownList($childStatus, [
                'prompt' => 'Select Property status',
                'class' => 'styled-select'
            ])->label(false) ?>
        </div>
        <div class="form-group">
            <label class="form-label">Street</label>
            <?= $form->field($model, 'street_id')->textInput([
                'placeholder' => 'Select street',
                'class' => 'styled-input'
            ])->label(false) ?>
        </div>
    </div>

    <h3 class="section-title">Property Attributes</h3>
    <div id="dynamic-form-container" class="mt-2">
        <div class="dynamic-placeholder">
            <i class="fas fa-clipboard-list"></i>
            <p>Select a Property Type to view additional attributes</p>
        </div>
    </div>

    <div class="mt-12 flex justify-end">
        <?= Html::submitButton('Next Step <i class="fas fa-arrow-right ml-2"></i>', ['class' => 'btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$attributeUrl = Url::to(['property-attribute/get-attributes']);
$js = <<<JS
$(document).ready(function () {
    $('#property_type_id').on('change', function () {
        let propertyId = $(this).val();
        if (!propertyId) return;

        $.ajax({
            url: '{$attributeUrl}',
            type: 'GET',
            data: {id: propertyId},
            success: function(response) {
                let container = $('#dynamic-form-container');
                container.empty();

                if (response.length === 0) {
                    container.html('<div class="dynamic-placeholder"><i class="fas fa-times-circle"></i><p>Hakuna sifa zilizopatikana kwa aina hii ya mali.</p></div>');
                    return;
                }

                // Create grid container for attributes
                let gridContainer = $('<div>').addClass('grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6');
                container.append(gridContainer);

                response.forEach(function(attr) {
                    let fieldHtml = '';
                    let attrId = attr.id;
                    let attrName = attr.attribute_name;

                    switch(attr.attribute_datatype.toLowerCase()) {
                        case 'text':
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<input type="text" class="styled-input" name="attributes['+attr.id+']" placeholder="Enter '+attr.attribute_name+'">' +
                                        '</div>';
                            break;
                        case 'number':
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<input type="number" class="styled-input" name="attributes['+attr.id+']" placeholder="Enter '+attr.attribute_name+'">' +
                                        '</div>';
                            break;
                        case 'boolean':
                        case 'select':
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<select class="styled-select" name="answers['+attr.id+']">' +
                                        '<option value="">Chagua Data</option>';

                            if (Array.isArray(attr.list_source) && attr.list_source.length > 0) {
                                attr.list_source.forEach(function(item) {
                                    fieldHtml += '<option value="'+item.id+'">'+item.list_Name+'</option>';
                                });
                            }
                            fieldHtml += '</select></div>';
                            break;
                        default:
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<input type="text" class="styled-input" name="attributes['+attr.id+']" placeholder="Enter '+attr.attribute_name+'">' +
                                        '</div>';
                    }
                    gridContainer.append(fieldHtml);
                });
            },
            error: function(err) {
                console.log('Error:', err);
                $('#dynamic-form-container').html('<div class="text-red-500 p-4"><i class="fas fa-exclamation-circle mr-2"></i>Kuna hitilafu. Tafadhali jaribu tena.</div>');
            }
        });
    });
});
JS;

$this->registerJs($js);
?>