<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use app\models\Property;
use app\models\PropertyPrice;
use app\models\ListSource;

/* @var $this yii\web\View */
/* @var $model app\models\PropertyPrice */

$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$indexUrl = Url::to(['property-price/index']); // redirect URL after success

// Get price type list from list_source table (Usage Type)
$parent= ListSource::find()->where(['list_Name'=>'Usage Type','category'=>'Usage Type'])->one();
$priceTypes = ArrayHelper::map(
    ListSource::find()->where(['parent_id' => $parent->id])->all(),
    'id',
    'list_Name'
);
?>

<div class="property-price-form container my-4 p-4 bg-white rounded shadow body">

    <!-- Back link -->
    <div class="mb-4">
        <?= Html::a('&larr; Back to Property Prices', $indexUrl, ['class' => 'text-decoration-none fw-bold']) ?>
        <hr>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'property-price-form',
        'action' => $model->isNewRecord 
            ? ['property-price/save'] 
            : ['property-price/save', 'id' => $model->id],
        'method' => 'post',
        'enableClientValidation' => true,
    ]); ?>
  <div class="container">
    <div class="row">
        
            <div class="col-lg-6 col-sm-12">
    <?= $form->field($model, 'property_id')->dropDownList(
        ArrayHelper::map(Property::find()->where(['not in','id',PropertyPrice::find()->select('property_id')])->all(), 'id', 'property_name'),
        ['prompt' => 'Select Property', 'class'=>'styled-select']
    ) ?>
              </div>
              <div class="col-lg-6 col-sm-12">
    <?= $form->field($model, 'price_type')->dropDownList(
        $priceTypes,
        ['prompt' => 'Select Price Type', 'class'=>'styled-select']
    ) ?>
           </div>
        
    </div>
</div>
<div class='container'>
    <div class="row">
        <div class="col-lg-6">
    <?= $form->field($model, 'unit_amount')->textInput([
        'type' => 'number',
        'step' => '0.01',
        'placeholder' => 'Amount e.g. 1000.00',
        'class'=>'styled-input'
    ]) ?>
         </div>
         <div class="col-lg-6">
    <?= $form->field($model, 'period')->dropDownList([
        'Monthly' => 'Monthly',
        'Yearly' => 'Yearly',
    ], ['prompt' => 'Select Period', 'class'=>'styled-textarea']) ?>
       </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-6">
    <?= $form->field($model, 'min_monthly_rent')->textInput([
        'type' => 'number',
        'step' => '0.01',
        'class'=>'styled-input'
    ]) ?>
        </div>
        <div class="col-lg-6">
    <?= $form->field($model, 'max_monthly_rent')->textInput([
        'type' => 'number',
        'step' => '0.01',
        'class'=>'styled-input'
    ]) ?>
        </div>
    </div>
</div>
    <div class="form-group mt-4">
        <?= Html::submitButton($model->isNewRecord ? 'Add Price' : 'Update Price', [
            'class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$('#property-price-form').on('beforeSubmit', function(e){
    e.preventDefault();
    var form = $(this);

    if(form.find('.has-error').length) return false;

    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        dataType: 'json',
        success: function(response){
            if(response.status === 'success'){
                Swal.fire({
                    title: response.message.includes('updated') ? 'Updated!' : 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '$indexUrl';
                });
            } else if(response.status === 'error'){
                let errors = '';
                if(response.errors){
                    $.each(response.errors, function(field, msgs){
                        errors += msgs.join(', ') + '\\n';
                    });
                } else {
                    errors = response.message || 'Unknown error';
                }
                Swal.fire('Validation Error', errors, 'error');
            }
        },
        error: function(xhr){
            Swal.fire('Error', 'Something went wrong. Try again.', 'error');
            console.error(xhr.responseText);
        }
    });
    return false;
});
JS;

$this->registerJs(new JsExpression($js));
?>
<?php 
$this->registerCss(
    "
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
     .form-group {
        margin-bottom: 1.75rem;
    }
  .help-block{
  color:red;
  font-weight:bold;
}  
   .form-label {
        font-weight: 500;
        color: var(--mid-text);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    } 
         .body {
        font-family: 'Inter', 'Roboto', sans-serif;
        background: white;
        min-height: 100vh;
        padding: 20px;
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
    
    "
);
?>