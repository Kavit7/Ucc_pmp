<?php

use app\models\ListSource;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Property;
use app\models\Location;


/** @var yii\web\View $this */
/** @var app\models\PropertyLocation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="property-location-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'property_id')->dropDownList(
        ArrayHelper::map(Property::find()->where(['street_id'=>null])->all(), 'id', 'property_name'),
        ['prompt' => 'Select Property']
    ) ?>

    <?= $form->field($model, 'location_id')->dropDownList(
        ArrayHelper::map(Location::find()->all(), 'id', 'uuid'),
        ['prompt' => 'Select Location']
    ) ?>

    <?= $form->field($model, 'status_id')->dropDownList(
        ArrayHelper::map(ListSource::find()->all(), 'id', 'list_Name'),
        ['prompt' => 'Select Status']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
