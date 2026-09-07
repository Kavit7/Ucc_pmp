<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string $secret */
/** @var string $qrSvg */

$this->title = 'Enable Two-Factor Authentication';
?>

<div class="container mt-5" style="max-width: 620px;">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <ol class="ps-3">
                <li class="mb-3">Install an authenticator app if you don't already have one (Google Authenticator, Microsoft Authenticator, Authy).</li>
                <li class="mb-3">
                    Scan this code with the app:
                    <div class="text-center my-3"><?= $qrSvg ?></div>
                    <div class="text-muted small">Can't scan it? Enter this key manually: <code style="user-select: all;"><?= Html::encode($secret) ?></code></div>
                </li>
                <li>Enter the 6-digit code the app shows to confirm setup:</li>
            </ol>

            <?= Html::beginForm(['custom/enable-2fa'], 'post', ['class' => 'mt-2']) ?>
                <div class="d-flex gap-2">
                    <?= Html::textInput('code', '', [
                        'class' => 'form-control',
                        'placeholder' => '000000',
                        'inputmode' => 'numeric',
                        'pattern' => '[0-9]*',
                        'maxlength' => 6,
                        'autofocus' => true,
                        'style' => 'max-width: 160px;',
                    ]) ?>
                    <?= Html::submitButton('Confirm & Enable', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Cancel', ['custom/settings'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
