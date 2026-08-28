<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>
<div class="login-card">
    <div class="logo">
        <i class="fas fa-building"></i>
        <h1>UCC PMP</h1>
        <p>Property Management Portal</p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => [
            'template' => "{input}{error}",
        ],
    ]); ?>

        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <?= $form->field($model, 'username', ['options' => ['class' => '']])->label(false)->textInput([
                'autofocus' => true,
                'placeholder' => 'Email address',
                'class' => 'form-control',
            ]) ?>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <?= $form->field($model, 'password', ['options' => ['class' => '']])->label(false)->passwordInput([
                'placeholder' => 'Password',
                'class' => 'form-control',
                'id' => 'password-input',
            ]) ?>
            <i class="fas fa-eye password-toggle" id="togglePassword" style="left:auto; right:18px; cursor:pointer;"></i>
        </div>

        <div class="remember-forgot">
            <div class="remember">
                <?= Html::activeCheckbox($model, 'rememberMe', ['label' => false, 'id' => 'remember-me']) ?>
                <label for="remember-me">Remember me</label>
            </div>
        </div>

        <?= Html::submitButton('Sign In', ['class' => 'login-btn', 'name' => 'login-button']) ?>

    <?php ActiveForm::end(); ?>

    <div class="help-note">
        Forgot your password? Contact your system administrator.
    </div>
</div>

<script>
    (function () {
        var toggle = document.getElementById('togglePassword');
        var pwd = document.getElementById('password-input');
        if (toggle && pwd) {
            toggle.addEventListener('click', function () {
                var isHidden = pwd.type === 'password';
                pwd.type = isHidden ? 'text' : 'password';
                toggle.classList.toggle('fa-eye');
                toggle.classList.toggle('fa-eye-slash');
            });
        }
    })();
</script>
