<?php
<<<<<<< HEAD
/* @var $this yii\web\View */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = 'Error';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= nl2br(Html::encode($exception->getMessage())) ?></p>
=======

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
