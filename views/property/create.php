<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Property */
?>

<?php $form = ActiveForm::begin(); ?>
<html>

<head>
</head>

<body>
    <div class="form-container ">
        <div class="form-header">
            <h5>ADD NEW PROPERTY</h5>
        </div>
        <div class="form-row">
            <div class="form-group">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'property_name')->textInput([
                                'placeholder' => 'Enter Property name'
                            ]) ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'property_type_id')->dropDownList($childProperty, [
                                'prompt' => 'Select property type',
                                'id' => 'property_type_id'
                            ]) ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'identifier_code')->textInput([
                                'placeholder' => 'Identifier Code'
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'usage_type_id')->dropDownList($childUsage, [
                                'prompt' => 'Select Usage Type'
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'ownership_type_id')->dropDownList($childOwner, [
                                'prompt' => 'Select Ownership Type',


                            ]) ?>
                        </div>

                    </div>

                </div>
            </div>
            <div class="form-group">

                <?= $form->field($model, 'document_url')->fileInput(['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'description')->textArea([
                    'placeholder' => 'Enter Description here'
                ]) ?>
            </div>
        </div>
        <div class="form-group">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'property_status_id')->dropDownList($childStatus, [
                            'prompt' => 'Select Property status',


                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'street_id')->textInput([
                            'placeholder' => 'Select street'
                        ]) ?>
                    </div>
                </div>
            </div>
            <div id="dynamic-form-container"></div>
        </div>
        <div class="form-row">
            <?= Html::submitButton('Next', ['class' => 'btn mt-5 float-end']) ?>
        </div>
    </div>
</body>

</html>
<?php ActiveForm::end(); ?>
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
                    container.html('<div class="text-center text-muted p-4">No attributes found for this property type</div>');
                    return;
                }

                response.forEach(function(attr) {
                    let fieldHtml = '';
                    let attrId = attr.id;
                    let attrName = attr.attribute_name;

                    switch(attr.attribute_datatype.toLowerCase()) {
                        case 'text':
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<input type="text" class="form-control styled-input" name="attributes['+attr.id+']">' +
                                        '</div>';
                            break;
                        case 'number':
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<input type="number" class="form-control  styled-input" name="attributes['+attr.id+']" > ' +
                                        '</div>';
                            break;
                        case 'boolean':
                        case 'select':
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<select class="form-control styled-select" name="answers['+attr.id+']">' +
                                        '<option value="">Select Data</option>';

                            // FIXED hapa: sasa inasoma kama array ya objects {id, list_Name}
                            if (Array.isArray(attr.list_source) && attr.list_source.length > 0) {
                                attr.list_source.forEach(function(item) {
                                    fieldHtml += '<option value="'+item.id+'">'+item.list_Name+'</option>';
                                });
                            }

                            // Default options for boolean

                            fieldHtml += '</select></div>';
                            break;
                        default:
                            fieldHtml = '<div class="form-group">' +
                                        '<label class="form-label">'+attr.attribute_name+'</label>' +
                                        '<input type="text" class="form-control styled-input" name="attributes['+attr.id+']">' +
                                        '</div>';
                    }

                    container.append(fieldHtml);
                });
            },
            error: function(err) {
                console.log('Error:', err);
                $('#dynamic-form-container').html('<div class="alert alert-danger">Error loading attributes. Please try again.</div>');
            }
        });
    });
});
JS;

$this->registerJs($js);
?>

