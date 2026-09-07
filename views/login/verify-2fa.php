<?php

/** @var yii\web\View $this */
/** @var string|null $error */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Verify your identity';
?>
<div class="login-card">
    <div class="logo">
        <i class="fas fa-shield-halved"></i>
        <h1>Two-Factor Login</h1>
        <p>Enter the 6-digit code from your authenticator app</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 px-3 small mb-3"><?= Html::encode($error) ?></div>
    <?php endif; ?>

    <?= Html::beginForm(['login/verify2fa'], 'post') ?>
        <div class="input-group">
            <i class="fas fa-key"></i>
            <?= Html::textInput('code', '', [
                'class' => 'form-control',
                'placeholder' => '000000',
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'maxlength' => 6,
                'autocomplete' => 'one-time-code',
                'autofocus' => true,
            ]) ?>
        </div>

        <?= Html::submitButton('Verify', ['class' => 'login-btn']) ?>
    <?= Html::endForm() ?>

    <div class="help-note">
        <?= Html::a('Back to login', ['login/login']) ?>
    </div>
</div>
