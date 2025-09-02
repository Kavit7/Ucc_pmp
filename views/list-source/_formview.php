<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'options' => ['class' => 'styled-form']
]);
?>

<div class="form-container">
    <div class="form-header">
        <h3><i class="bi bi-list-check"></i> List Source Configuration</h3>
        <p>Create a new list source for your application</p>
    </div>

    <div class="form-row">
        <div class="form-group">
            <?= $form->field($model, 'list_Name')->textInput([
                'placeholder' => 'Enter List Name',
                'class' => 'form-control styled-input'
            ])->label('List Name <span class="required">*</span>', ['class' => 'form-label']) ?>
        </div>
        
        <div class="form-group">
            <?= $form->field($model, 'category')->textInput([
                'placeholder' => 'Enter Category',
                'class' => 'form-control styled-input'
            ])->label('Category <span class="required">*</span>', ['class' => 'form-label']) ?>
        </div>
    </div>

    <div class="form-group full-width">
        <?= $form->field($model, 'description')->textarea([
            'placeholder' => 'Enter Description',
            'class' => 'form-control styled-textarea',
            'rows' => 4
        ])->label('Description', ['class' => 'form-label']) ?>
    </div>

    <!-- System-generated fields (hidden) -->
    <?= $form->field($model, 'code')->hiddenInput(['value' => $model->generateCode()])->label(false) ?>
    <?= $form->field($model, 'sort_by')->hiddenInput(['value' => $model->generateSort()])->label(false) ?>
    <?= $form->field($model, 'parent_id')->hiddenInput(['value' => $model->generateParentId()])->label(false) ?>

    <div class="form-actions">
        <?= Html::submitButton('<i class="bi bi-check-circle"></i> Save', ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i> Cancel', ['create'], ['class' => 'btn btn-cancel btn-lg']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<style>
.styled-form {
    max-width: 900px;
    margin: 0 auto;
}

.form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 2rem;
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.form-header h3 {
    color: #0c0a7e;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-header p {
    color: #718096;
    margin-bottom: 0;
}

.form-row {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    flex: 1;
}

.full-width {
    width: 100%;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: block;
}

.required {
    color: #e53e3e;
}

.styled-input, .styled-textarea {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.styled-input:focus, .styled-textarea:focus {
    border-color: #3f51b5;
    box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.2);
    outline: none;
}

.styled-textarea {
    resize: vertical;
    min-height: 120px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #0c0a7e, #3f51b5);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(63, 81, 181, 0.3);
}

.btn-cancel {
    background: #e53e3e !important;
    color: white;
}

.btn-cancel:hover {
    background: #c53030;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
}

/* Responsive design */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>