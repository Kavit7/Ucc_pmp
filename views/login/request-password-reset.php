<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\PasswordResetRequestForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Forgot password';
?>
<div class="login-card">
    <div class="logo">
        <i class="fas fa-key"></i>
        <h1>Reset password</h1>
        <p>Enter your account email and we'll send you a reset link.</p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'request-password-reset-form',
        'fieldConfig' => [
            'template' => "{input}{error}",
        ],
    ]); ?>

        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <?= $form->field($model, 'email', ['options' => ['class' => '']])->label(false)->textInput([
                'autofocus' => true,
                'placeholder' => 'Email address',
                'class' => 'form-control',
            ]) ?>
        </div>

        <?= Html::submitButton('Send reset link', ['class' => 'login-btn']) ?>

    <?php ActiveForm::end(); ?>

    <div class="help-note">
        <?= Html::a('Back to login', ['login/login']) ?>
    </div>
</div>
