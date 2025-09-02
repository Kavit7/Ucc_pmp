<?php
/* @var $this yii\web\View */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = 'Error';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= nl2br(Html::encode($exception->getMessage())) ?></p>
