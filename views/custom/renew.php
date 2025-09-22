<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ListSource;
use app\models\PropertyPrice;

/* @var $this yii\web\View */
/* @var $lease app\models\Lease */
/* @var $oldLease app\models\Lease */

$this->title = 'Renew Lease';
$indexUrl = Url::to(['custom/leases']);

$propertyPrices = PropertyPrice::find()->all();
$pricesData = [];
foreach ($propertyPrices as $price) {
    $pricesData[$price->property_id][] = [
        'id' => $price->id,
        'unit_amount' => $price->unit_amount,
    ];
}
$pricesJson = json_encode($pricesData);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-10 col-12 mx-auto">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-header bg-transparent border-0 pb-2">
                   <p class="mb-3">
                    <?= Html::a('&larr; Back to Leases', ['custom/leases'], ['class' => 'text-decoration-none fw-bold']) ?>
                </p>
                    <hr class="mt-1">
                </div>

                <div class="card-body pt-3">
                    <?php $form = ActiveForm::begin([
                        'options' => ['class' => 'needs-validation', 'enctype' => 'multipart/form-data'],
                    ]); ?>
                
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <?= $form->field($lease, 'property_id')->textInput([
                                    'value' => $oldLease->property->property_name,
                                    'disabled' => true,
                                    'class' => 'form-control styled-input'
                                ]) ?>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <?= $form->field($lease, 'tenant_id')->textInput([
                                    'value' => $oldLease->tenant->full_name,
                                    'disabled' => true,
                                    'class' => 'form-control styled-input'
                                ]) ?>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <?= $form->field($lease, 'property_price_id')->dropDownList(
                                    ['' => 'Select Price'],
                                    ['class' => 'form-select w-100 styled-select']
                                ) ?>
                            </div>
                        </div>

                        <?= $form->field($lease, 'lease_doc_file')->fileInput(['class' => 'form-control styled-input']) ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($lease, 'lease_start_date')->input('date', ['class' => 'form-control styled-input']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($lease, 'lease_end_date')->input('date', ['class' => 'form-control styled-input']) ?>
                            </div>
                        </div>

                        <?= $form->field($lease, 'status')->dropDownList(
                            ArrayHelper::map(
                                ListSource::find()
                                    ->where(['category' => 'Lease Status'])
                                    ->andWhere(['IS NOT', 'parent_id', null])
                                    ->all(),
                                'id',
                                'list_Name'
                            ),
                            ['prompt' => 'Select Status', 'class' => 'form-select w-100 styled-select']
                        ) ?>

                        <div class="d-grid mt-4">
                            <?= Html::submitButton('Renew Lease', ['class' => 'btn btn-primary btn-lg']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const propertyPrices = <?= $pricesJson ?>;
    const priceSelect = document.getElementById('lease-property_price_id');
    const propertyId = "<?= $oldLease->property_id ?>";

    priceSelect.innerHTML = '<option value="">Select Price</option>';

    if (propertyId && propertyPrices[propertyId] !== undefined) {
        propertyPrices[propertyId].forEach(price => {
            const option = document.createElement('option');
            option.value = price.id;
            option.text = price.unit_amount;
            priceSelect.appendChild(option);
        });
    }
});
</script>
<style>
/* Card and spacing */
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
         body {
        font-family: 'Inter', 'Roboto', sans-serif !important;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        
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
    
</style>
