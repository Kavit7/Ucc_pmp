<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\ListSource;

/** @var yii\web\View $this */
/** @var app\models\Lease $lease */

$this->title = 'Security Deposit';

$statusOptions = ArrayHelper::map(
    ListSource::find()->where(['category' => 'Security Deposit Status'])->andWhere(['is not', 'parent_id', null])->all(),
    'id',
    'list_Name'
);
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="mb-1"><?= Html::encode($this->title) ?></h4>
                    <p class="text-muted mb-4">
                        <?= Html::encode($lease->property->property_name ?? 'Property') ?> &middot;
                        <?= Html::encode($lease->tenant->full_name ?? 'Tenant') ?>
                    </p>

                    <?= Html::beginForm(['custom/manage-deposit', 'id' => $lease->id], 'post') ?>

                        <div class="mb-3">
                            <label class="form-label">Deposit amount (TZS)</label>
                            <?= Html::textInput('security_deposit_amount', $lease->security_deposit_amount, [
                                'class' => 'form-control',
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => '0',
                            ]) ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <?= Html::dropDownList('security_deposit_status', $lease->security_deposit_status, $statusOptions, [
                                'class' => 'form-select',
                                'prompt' => 'Not set',
                            ]) ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Returned date</label>
                            <?= Html::textInput('security_deposit_returned_at', $lease->security_deposit_returned_at, [
                                'class' => 'form-control',
                                'type' => 'date',
                            ]) ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes</label>
                            <?= Html::textarea('security_deposit_notes', $lease->security_deposit_notes, [
                                'class' => 'form-control',
                                'rows' => 3,
                                'placeholder' => 'e.g. TZS 50,000 deducted for cleaning',
                            ]) ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <?= Html::a('Cancel', ['leases'], ['class' => 'btn btn-outline-secondary']) ?>
                            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                        </div>

                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
</div>
