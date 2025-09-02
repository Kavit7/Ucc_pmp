<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin(
);
?>

<?php
$topLists = \app\models\ListSource::find()->where(['parent_id' => null])->all();
echo $form->errorSummary($model);
echo $form->field($model, 'parent_id')->dropDownList(
    \yii\helpers\ArrayHelper::map($topLists, 'id', function($item) {
        return (string)$item->list_Name;
    }),
    ['prompt' => 'Select Parent List']
);
?>




<?= $form->field($model, 'list_Name')->textInput() ?>
<?= $form->field($model, 'category')->textInput() ?>
<?= $form->field($model, 'description')->textInput() ?>

<!-- We no longer show code field; it will be auto-generated in the model -->
<?php // <?= $form->field($model, 'code')->textInput(['readonly' => true]) ?> 

<div class="mt-3">
    <?= Html::submitButton('Add Child',['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
