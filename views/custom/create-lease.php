<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Create Lease';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'options' => ['class' => 'needs-validation'],
                    ]); ?>

                    <?= $form->field($lease, 'property_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(\app\models\Property::find()->all(), 'id', 'property_name'),
                        ['prompt'=>'Select Property', 'class'=>'form-select']
                    ) ?>

                    <?= $form->field($lease, 'tenant_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(\app\models\Tenant::find()->all(), 'id', 'name'),
                        ['prompt'=>'Select Tenant', 'class'=>'form-select']
                    ) ?>

                    <?= $form->field($lease, 'property_price_id')->dropDownList(
                        \yii\helpers\ArrayHelper::map(\app\models\PropertyPrice::find()->all(), 'id', 'unit_amount'),
                        ['prompt'=>'Select Price', 'class'=>'form-select']
                    ) ?>

                    <?= $form->field($lease, 'lease_doc_url')->textInput(['placeholder'=>'Document URL','class'=>'form-control']) ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($lease, 'lease_start_date')->input('date', ['class'=>'form-control']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($lease, 'lease_end_date')->input('date', ['class'=>'form-control']) ?>
                        </div>
                    </div>

                    <?= $form->field($lease, 'status')->dropDownList(
                        [0=>'Pending', 1=>'Active', 2=>'Terminated'],
                        ['class'=>'form-select']
                    ) ?>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Create Lease', ['class'=>'btn btn-success btn-lg']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 576px) {
    .card {
        margin: 0 10px;
    }
}
</style>
