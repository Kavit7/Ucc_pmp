<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Street $model */

$this->title = 'Update Street: ' . $model->street_id;
$this->params['breadcrumbs'][] = ['label' => 'Streets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->street_id, 'url' => ['view', 'street_id' => $model->street_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="street-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
