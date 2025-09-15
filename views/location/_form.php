<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Country;
use app\models\Region;
use app\models\District;
use app\models\Street;

/** @var yii\web\View $this */
/** @var app\models\Location $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($model, 'uuid')->textInput() ?>

    <?= $form->field($model, 'country_id')->dropDownList(
        ArrayHelper::map(Country::find()->all(), 'country_id', 'country_name'),
        ['prompt' => 'Select Country']
    ) ?>

    <?= $form->field($model, 'region_id')->dropDownList(
        ArrayHelper::map(Region::find()->all(), 'region_id', 'name'),
        ['prompt' => 'Select Region']
    ) ?>

    <?= $form->field($model, 'district_id')->dropDownList(
        ArrayHelper::map(District::find()->all(), 'district_id', 'district_name'),
        ['prompt' => 'Select District']
    ) ?>

    <?= $form->field($model, 'street_id')->dropDownList(
        ArrayHelper::map(Street::find()->all(), 'street_id', 'street_name'),
        ['prompt' => 'Select Street']
    ) ?>

    <?= $form->field($model, 'created_at')->hiddenInput(['value' => date('Y-m-d H:i:s')])->label(false) ?>
    <?= $form->field($model, 'created_by')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
    <?= $form->field($model, 'updated_at')->hiddenInput(['value' => date('Y-m-d H:i:s')])->label(false) ?>
    <?= $form->field($model, 'updated_by')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
