<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PropertyLocation $model */

$this->title = 'Create Property Location';
$this->params['breadcrumbs'][] = ['label' => 'Property Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="property-location-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
