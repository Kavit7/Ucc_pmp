<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
$form = ActiveForm::begin(
);
$indexUrl= Url::to(['list-source/create']);
?>
<div class="bg-white rounded p-4">
<?php
$topLists = \app\models\ListSource::find()->where(['parent_id' => null])->all();
echo $form->errorSummary($model);
echo '<div class="container">';
 echo '<div class="row">';
 echo '<div class="col-lg-4">';
echo $form->field($model, 'parent_id')->dropDownList(
    \yii\helpers\ArrayHelper::map($topLists, 'id', function($item) {
        return (string)$item->list_Name;
    }),
    ['prompt' => 'Select Parent List',
    'class'=>'styled-select'
    ]
);
echo '</div>'
?>



<div class="col-lg-4">
<?= $form->field($model, 'list_Name')->textInput([
    'class'=>'styled-input',
    'placeholder'=>'Enter Child Name',
]) ?>
</div>
<div class="col-lg-4">
<?= $form->field($model, 'category')->textInput([
    'class'=>'styled-input',
    'placeholder'=>'Enter the name as that of Parent Selected',
]) ?>
</div>
</div>
</div>
<?= $form->field($model, 'description')->textArea([
    'class'=>'styled-textarea'
]) ?>

<!-- We no longer show code field; it will be auto-generated in the model -->
<?php // <?= $form->field($model, 'code')->textInput(['readonly' => true]) ?> 

<div class="mt-3 d-flex justify-content-between">
    <?= Html::submitButton('Add Child',['class' => 'btn btn-primary']) ?>
    <?= Html::a('&larr; Back', $indexUrl, ['class' => 'text-decoration-none fw-bold']) ?>
</div>

<?php ActiveForm::end(); ?>
</div>
<?php
$this->registerCss(
    "
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
         .body {
        font-family: 'Inter', 'Roboto', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
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
    
    "
);

?>