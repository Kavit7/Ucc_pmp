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
                $('.ChildList').hide();
            } else if (select === 'Existing-List') {
                $('.List').hide();
                $('.ChildList').show();
            } else {
                $('.List, .ChildList').hide();
            }
        });
    });
");
?>
<div class="d-flex flex-column  align-items-center">
<select class="form-select" >
    <option value="">-------Select Configuration Task---------</option>
    <option value="New-List">Add New List Name Configuration</option>
    <option value="Existing-List">Add Configuration To existing List Name</option>
</select>
<div class="List " style="display:none; width: 1000px; padding:30px; ">
    <?= $this->render('_formview', ['model' => $model]) ?>
</div>
<div class="ChildList" style="display:none;">
    <?= $this->render('_formChild', ['model' => new ListSource(), 'topListOptions' => $sources]) ?> <!-- Child list -->
</div>
</div>
<div class="container">
  <?= $this->render('source', ['sources' => $sources]) ?>
</div>