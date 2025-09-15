<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Street $model */

$this->title = 'Create Street';
$this->params['breadcrumbs'][] = ['label' => 'Streets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="street-create bg-white p-4 rounded">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
