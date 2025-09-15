<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Assign Property Location';
?>

<h2><?= Html::encode($this->title) ?></h2>

<?php $form = ActiveForm::begin(); ?>

<!-- Dropdown to select a property that has no location yet -->
<?= Html::dropDownList('property_id', null,
    \yii\helpers\ArrayHelper::map($properties, 'id', 'name'), // $properties = all properties without location
    ['prompt' => 'Select Property']
) ?>

<div style="margin-top:10px;">
    <?= Html::dropDownList('country', null,
        \yii\helpers\ArrayHelper::map($countries, 'id', 'name'),
        ['prompt' => 'Select Country', 'id' => 'country']
    ) ?>

    <?= Html::dropDownList('region', null, [], [
        'prompt' => 'Select Region',
        'id' => 'region'
    ]) ?>

    <?= Html::dropDownList('district', null, [], [
        'prompt' => 'Select District',
        'id' => 'district'
    ]) ?>

    <?= Html::dropDownList('street', null, [], [
        'prompt' => 'Select Street',
        'id' => 'street'
    ]) ?>
</div>

<div style="margin-top:10px;">
    <?= Html::submitButton('Assign Location', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
