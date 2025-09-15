<?php
use app\models\Street;
use yii\helpers\ArrayHelper;
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
    
 
    
    .form-card {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem 2rem 3rem;
        background-color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }
    
    .form-header {
        margin-bottom: 2rem;
        position: relative;
    }
    
    .form-header h5 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark-text);
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .form-header .back-icon {
        margin-right: 15px;
        cursor: pointer;
        color: var(--primary);
        font-size: 1.2rem;
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
        background: #2563EB;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.25);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    
    
    .btn-submit:active {
        transform: translateY(0);
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
    
    /* Custom grid layout for form rows */
    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
        margin-bottom: 1.5rem;
    }
    
    .form-row .form-group {
        flex: 1;
        padding: 0 10px;
        margin-bottom: 0;
    }
    
    .form-row.cols-2 .form-group {
        flex: 0 0 50%;
    }
    
    .form-row.cols-3 .form-group {
        flex: 0 0 33.333333%;
    }
    
    .form-row.cols-1 .form-group {
        flex: 0 0 100%;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }
      .help-block{
  color:red;
  font-weight:bold;
} 
    
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }
        
        .form-header h5 {
            font-size: 1.25rem;
        }
        
        .form-row.cols-2 .form-group,
        .form-row.cols-3 .form-group {
            flex: 0 0 100%;
        }
    }
</style>
<div class="form-card">
    <div class="form-header">
        <h5>
            <i class="fas fa-arrow-left back-icon"></i>
            Add new property
        </h5>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    
    <!-- First row: Two fields -->
    <div class="form-row cols-2">
        <div class="form-group">
           
            <?= $form->field($model, 'property_name')->textInput([
                'placeholder' => 'Property Name',
                'class' => 'styled-input',
            ]) ?>
        </div>
        <div class="form-group">
           
            <?= $form->field($model, 'property_type_id')->dropDownList($childProperty, [
                'prompt' => 'Property Type',
                'id' => 'property_type_id',
                'class' => 'styled-select'
            ]) ?>
        </div>
    </div>
    
    <!-- Second row: Three fields -->
    <div class="form-row cols-2">
        <div class="form-group">
            
            <?= $form->field($model, 'ownership_type_id')->dropDownList($childOwner, [
                'prompt' => 'Ownership Type',
                'class' => 'styled-select'
            ]) ?>
        </div>
        <div class="form-group">
          
            <?= $form->field($model, 'property_status_id')->dropDownList($childStatus, [
                'prompt' => 'Enter Status Type',
                'class' => 'styled-select'
            ]) ?>
        </div>
        <div class="form-group">
            
            <?= $form->field($model, 'usage_type_id')->dropDownList($childUsage, [
                'prompt' => 'Usage Type',
                'class' => 'styled-select'
            ]) ?>
        </div>
        <div class="form-group">
            
            <?= $form->field($model, 'street_id')->dropDownList(ArrayHelper::map(Street::find()->all(),'street_id','street_name'),[
                'prompt' => 'Location',
                'class' => 'styled-select'
            ])?>
        </div>
    </div>
    
    <!-- Third row: Two fields -->
    <div class="form-row cols-2">
        <div class="form-group">
           
            <?= $form->field($model, 'documentFile')->fileInput([
                'class' => 'styled-input'
            ]) ?>
        </div>
        <div class="form-group">
            
            <?= $form->field($model, 'identifier_code')->textInput([
                'placeholder' => 'Unique Property Identity',
                'class' => 'styled-input'
            ]) ?>
        </div>
    </div>
    
    <!-- Fourth row: One field (full width) -->
    <div class="form-row cols-1">
        <div class="form-group">
            
            <?= $form->field($model, 'description')->textArea([
                'placeholder' => 'Description',
                'class' => 'styled-textarea'
            ]) ?>
        </div>
    </div>
    
    <div id="dynamic-form-container" class="mt-2">
        <div class="dynamic-placeholder">
            <i class="fas fa-clipboard-list"></i>
            <p>Select a Property Type to view additional attributes</p>
        </div>
    </div>
    
    <div class="mt-12 flex justify-end">
         <?= Html::submitButton('Save', ['class' => 'btn btn-submit me-3', ])?>

    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$attributeUrl = Url::to(['property-attribute/get-attributes']);
$js = <<<JS
$(document).ready(function () {
    // Back button functionality
    $('.back-icon').on('click', function() {
        window.history.back();
    });
    
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
                    container.html('<div class="dynamic-placeholder"><i class="fas fa-times-circle"></i><p>No attributes found for this property type.</p></div>');
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
                                        '<option value="">Select</option>';
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
                $('#dynamic-form-container').html('<div class="text-red-500 p-4"><i class="fas fa-exclamation-circle mr-2"></i>Error occurred. Please try again.</div>');
            }
        });
    });
});
JS;
$this->registerJs($js);
?>