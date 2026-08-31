<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Users $user */

$resetLink = Url::to(['login/reset-password', 'token' => $user->password_reset_token], true);
?>
<p>Hello <?= Html::encode($user->full_name) ?>,</p>

<p>You (or someone else) requested a password reset for your account on <?= Html::encode(Yii::$app->name) ?>.</p>

<p>Click the link below to choose a new password. This link expires in 1 hour and can only be used once:</p>

<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

<p>If you did not request this, you can safely ignore this email — your password will not be changed.</p>
