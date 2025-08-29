<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Login to Property Portal';
?>

<div class="login-card">
    <div class="logo">
        <i class="fas fa-building"></i>
        <h1>Property Management</h1>
        <p>Sign in to your account</p>
    </div>
    
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['autocomplete' => 'off'], // 🚀 disable autocomplete
        'fieldConfig' => [
            'template' => "{input}\n{error}",
            'errorOptions' => ['class' => 'invalid-feedback']
        ],
    ]); ?>

        <div class="input-group">
            <i class="fas fa-user"></i>
            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true, 
                'placeholder' => 'Username',
                'required' => true,
                'autocomplete' => 'off', // 🚀 prevent autofill
            ])->label(false) ?>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => 'Password',
                'required' => true,
                'autocomplete' => 'new-password', // 🚀 trick browser not to autofill
            ])->label(false) ?>
        </div>

        <div class="remember-forgot">
            <div class="remember">
                <?= $form->field($model, 'rememberMe')->checkbox([
                    'template' => "{input} <label for='remember'>Remember me</label>\n{error}"
                ]) ?>
            </div>
            
            <div class="forgot-password">
                <?= Html::a('Forgot Password?', ['site/request-password-reset'], ['class' => 'forgot']) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Login', [
                'class' => 'login-btn', 
                'name' => 'login-button'
            ]) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <div class="register-link">
        <p>Don't have an account? <?= Html::a('Register here', ['site/signup'], ['class' => 'register']) ?></p>
    </div>
</div>

