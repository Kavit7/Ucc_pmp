<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Property;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\PropertyPrice */

$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$indexUrl = Url::to(['property-price/index']); // correct redirect URL
?>

<div class="property-price-form box box-primary p-3">

    <?php $form = ActiveForm::begin([
        'id' => 'property-price-form',
        'action' => $model->isNewRecord 
            ? ['property-price/save'] 
            : ['property-price/save', 'id' => $model->id],
        'method' => 'post',
        'options' => ['class' => 'ajax-form'],
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
    ]); ?>

    <?= $form->field($model, 'property_id')->dropDownList(
        ArrayHelper::map(Property::find()->all(), 'id', 'property_name'),
        ['prompt' => 'Select Property']
    ) ?>
    
    <?= $form->field($model, 'price_type')->dropDownList([
        'Rent' => 'Rent',
        'Sale' => 'Sale',
        'Deposit' => 'Deposit',
    ], ['prompt' => 'Select Price Type']) ?>

    <?= $form->field($model, 'unit_amount')->textInput([
        'type' => 'number',
        'step' => '0.01',
        'placeholder' => 'Amount e.g. 1000.00'
    ]) ?>

    <?= $form->field($model, 'period')->dropDownList([
        'Monthly' => 'Monthly',
        'Yearly' => 'Yearly',
    ], ['prompt' => 'Select Period']) ?>

    <?= $form->field($model, 'min_monthly_rent')->textInput([
        'type' => 'number',
        'step' => '0.01'
    ]) ?>

    <?= $form->field($model, 'max_monthly_rent')->textInput([
        'type' => 'number',
        'step' => '0.01'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Add Price' : 'Update Price', [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$('#property-price-form').on('beforeSubmit', function(e){
    e.preventDefault();
    var form = $(this);
    
    // skip if validation errors
    if(form.find('.has-error').length) {
        return false;
    }

    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title: response.message.includes('updated') ? 'Updated!' : 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '$indexUrl'; // redirect safely
                });
            } else if(response.status === 'error'){
                var errors = '';
                $.each(response.errors, function(field, messages){
                    errors += field + ': ' + messages.join(', ') + '\\n';
                });
                Swal.fire('Validation Error', errors, 'error');
            }
        },
        error: function(){
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        }
    });

    return false;
});
JS;

$this->registerJs(new JsExpression($js));
?>
