<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\ListSource;

$this->title = 'Configure List Source';
$form = ActiveForm::begin();
$this->registerJs("
    $(document).ready(function () {
        $('.form-select').on('change', function() {
            let select = $(this).val();
            if (select === 'New-List') {
                $('.List').show();
            } else {
                $('.List').hide();
            }
        });
    });
");
?>
<select class="form-select">
    <option value="">-------Select Configuration Task---------</option>
    <option value="New-List">Add New List Name Configuration</option>
    <option value="Existing-List">Add Configuration To existing List Name</option>
</select>
<div class="List" style="display:none;">
    <?= $this->render('_formview', ['model' => $model]) ?>
</div>
