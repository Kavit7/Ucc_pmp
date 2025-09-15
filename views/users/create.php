<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Add New User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$currentRole = Yii::$app->user->identity->role ?? null;
?>

<div class="container-fluid d-flex justify-content-center">
    <div class="user-create py-4 px-4" style="background:#fff; border-radius:8px; max-width:1050px; width:100%;">
        <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

        <?php $form = ActiveForm::begin([
            'id' => 'user-create-form',
            'options' => ['novalidate' => true], 
        ]); ?>

        <div class="row g-3 mb-3">
            <div class="col-md-6 "><?= $form->field($model, 'full_name')->textInput(['maxlength' => true,'class'=>'styled-input']) ?></div>
            <div class="col-md-6 "><?= $form->field($model, 'email')->textInput(['maxlength' => true,'class'=>'styled-input']) ?></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 "><?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class'=>'styled-input']) ?></div>
            <div class="col-md-6 "><?= $form->field($model, 'national_id')->textInput(['maxlength' => true,'class'=>'styled-input']) ?></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 "><?= $form->field($model, 'nationality')->textInput(['maxlength' => true, 'class'=>'styled-input']) ?></div>
            <div class="col-md-6 "><?= $form->field($model, 'occupation')->textInput(['maxlength' => true, 'class'=>'styled-input']) ?></div>
        </div>

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <?php
                if($currentRole === 'manager') {
                    // Manager sees only tenant option
                    echo $form->field($model, 'role')->dropDownList(['tenant'=>'tenant'], [
                        'prompt'=>'Select Role',
                        'id'=>'user-role',
                        'class'=>'form-select styled-select',
                    ]);
                } else {
                    // Admin sees all roles
                    echo $form->field($model, 'role')->dropDownList([
                        'tenant'=>'tenant',
                        'manager'=>'manager',
                        'admin'=>'admin'
                    ], [
                        'prompt'=>'Select Role',
                        'id'=>'user-role',
                        'class'=>'form-select styled-select',
                    ]);
                }
                ?>
            </div>

            <?php if($currentRole !== 'manager'): ?>
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList([
                    'active'=>'active',
                    'inactive'=>'inactive',
                    'blocked'=>'blocked'
                ], ['prompt'=>'Select Status', 'class'=>'form-select styled-select']) ?>
            </div>

            <div class="col-md-6 password-section">
                <?= $form->field($model, 'password')->passwordInput(['maxlength'=>true, 'placeholder'=>'Enter initial password', 'class'=>'styled-select']) ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if($currentRole !== 'manager'): ?>
        <div id="privileges-section" style="margin-top:20px; padding:10px;">
            <label><strong>Privileges</strong></label>
            <div class="row g-2">
                <?php 
                $privs = [
                    'create'=>'Create Property',
                    'edit'=>'Edit Property',
                    'delete'=>'Delete Property',
                    'assign'=>'Assign Tenant',
                    'view'=>'View Reports',
                    'manage'=>'Manage Users'
                ];
                $i=0;
                foreach($privs as $key=>$label):
                    if($i%3===0 && $i!=0) echo '</div><div class="row g-2 mt-2">';
                ?>
                    <div class="col-md-4">
                        <div class="form-check">
                            <?= Html::checkbox("User[privileges][]", in_array($key,$model->privileges??[]), [
                                'value'=>$key,
                                'id'=>'priv-'.$key,
                                'class'=>'form-check-input'
                            ]) ?>
                            <?= Html::label($label,'priv-'.$key,['class'=>'form-check-label']) ?>
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-4 d-flex justify-content-end gap-3">
            <?= Html::a('Cancel',['index'],['class'=>'btn btn-outline-secondary']) ?>
            <?= Html::submitButton('Save',['class'=>'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<style>
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
        color: red;
    }
    

    
 
</style>