<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Region $model */

$this->title = 'Configure Region';
$this->params['breadcrumbs'][] = ['label' => 'Regions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region-create bg-white p-4 rounded">

    <h1 class="text-center text-primary"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
