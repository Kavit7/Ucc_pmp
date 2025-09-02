<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Add New User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid d-flex justify-content-center">
    <div class="user-create py-4 px-4" style="background:#fff; border-radius:8px; max-width:1050px; width:100%;">

        <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

        <?php $form = ActiveForm::begin([
            'options' => ['novalidate' => true], // Disable default HTML5 validation
        ]); ?>

        <div class="row g-3 mb-3">
            <div class="col-md-6"><?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6"><?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'national_id')->textInput(['maxlength' => true]) ?></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6"><?= $form->field($model, 'nationality')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'occupation')->textInput(['maxlength' => true]) ?></div>
        </div>

        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <?= $form->field($model, 'role')->dropDownList([
                    'Tenant' => 'Tenant',
                    'Manager' => 'Manager',
                    'Admin' => 'Admin'
                ], [
                    'prompt' => 'Select Role',
                    'id' => 'user-role',
                    'class' => 'form-select',
                ]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'blocked' => 'Blocked'
                ], [
                    'prompt' => 'Select Status',
                    'class' => 'form-select',
                ]) ?>
            </div>

            <div class="col-md-6 password-section" style="display:none;">
                <?= Html::label('Password', 'password-input') ?>
                <div style="position: relative;">
                    <?= Html::passwordInput('User[password]', null, [
                        'id' => 'password-input',
                        'class' => 'form-control pe-5' // padding for icon
                    ]) ?>
                    <span id="toggle-password" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>

        </div>


        <div id="privileges-section" style="display:none; margin-top:20px; padding:10px;">
            <label><strong>Privileges</strong></label>
            <div class="row g-2">
                <?php 
                $privs = [
                    'create' => 'Create Property',
                    'edit' => 'Edit Property',
                    'delete' => 'Delete Property',
                    'assign' => 'Assign Tenant',
                    'view' => 'View Reports',
                    'manage' => 'Manage Users'
                ];
                $i = 0;
                foreach ($privs as $key => $label): 
                    if ($i % 3 === 0 && $i != 0) echo '</div><div class="row g-2 mt-2">';
                ?>
                    <div class="col-md-4">
                        <div class="form-check">
                            <?= Html::checkbox("User[privileges][]", in_array($key, $model->privileges ?? []), [
                                'value' => $key, 
                                'id' => 'priv-'.$key,
                                'class' => 'form-check-input'
                            ]) ?>
                            <?= Html::label($label, 'priv-'.$key, ['class' => 'form-check-label']) ?>
                        </div>
                    </div>
                <?php 
                $i++; 
                endforeach; 
                ?>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-3">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$script = <<<JS
$('#user-role').on('change', function() {
    var selected = $(this).val();
    if (selected === 'Manager' || selected === 'Admin') {
        $('#privileges-section').slideDown();
        $('.password-section').slideDown();
    } else {
        $('#privileges-section').slideUp();
        $('.password-section').slideUp();
    }
});

document.getElementById('toggle-password').addEventListener('click', function () {
    var passwordInput = document.getElementById('password-input');
    var icon = this.querySelector('i');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

$('form').on('submit', function(event) {
    $(this).find('input[required], select[required], textarea[required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Required</div>');
            }
            event.preventDefault();
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
});
JS;
$this->registerJs($script);
?>
