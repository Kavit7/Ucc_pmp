<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PropertyLocation $model */

$this->title = 'Update Property Location: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Property Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="property-location-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
