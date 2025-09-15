<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Region;
use app\models\District;
/** @var yii\web\View $this */
/** @var app\models\Street $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="street-form">

    <?php $form = ActiveForm::begin(); ?>

    

    <?= $form->field($model, 'street_name')->textInput(['maxlength' => true,
    'class'=>'styled-input']) ?>

    <?= $form->field($model, 'region_id')->dropDownList( ArrayHelper::map(Region::find()->all(),'region_id','name'),[
        'prompt'=>'Select Region',
        'class'=>'styled-select'
    ]) ?>

    <?= $form->field($model, 'district_id')->dropDownList( 
        ArrayHelper::map(District::find()->all(),'district_id','district_name'),[
            'prompt'=>'Select District',
            'class'=>'styled-select'
        ]
    ) ?>



    <div class="form-group d-flex align-items-center justify-content-between">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary mt-2']) ?>
         <?= Html::a('Cancel',['index'],['class'=>'btn btn-outline-secondary  mt-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerCss("
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
    
    .styled-textarea {
        min-height: 120px;
        resize: vertical;
    }
    .has-error{
     color:red;
}

"


);

?>
