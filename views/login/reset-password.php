<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ResetPasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Choose a new password';
?>
<div class="login-card">
    <div class="logo">
        <i class="fas fa-lock"></i>
        <h1>New password</h1>
        <p>Choose a new password for your account.</p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'reset-password-form',
        'fieldConfig' => [
            'template' => "{input}{error}",
        ],
    ]); ?>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <?= $form->field($model, 'password', ['options' => ['class' => '']])->label(false)->passwordInput([
                'autofocus' => true,
                'placeholder' => 'New password',
                'class' => 'form-control',
            ]) ?>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <?= $form->field($model, 'confirmPassword', ['options' => ['class' => '']])->label(false)->passwordInput([
                'placeholder' => 'Confirm new password',
                'class' => 'form-control',
            ]) ?>
        </div>

        <?= Html::submitButton('Reset password', ['class' => 'login-btn']) ?>

    <?php ActiveForm::end(); ?>
</div>
