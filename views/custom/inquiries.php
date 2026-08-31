<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Property Inquiries';
?>
<div class="container mt-4 custom-container">
    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($model) {
            return '
                <div class="card mb-2 border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <strong>' . Html::encode($model->name) . '</strong>
                                <span class="text-muted">interested in</span>
                                <strong>' . Html::encode($model->property->property_name ?? 'a property') . '</strong>
                            </div>
                            <span class="text-muted small">' . Html::encode($model->created_at) . '</span>
                        </div>
                        <div class="small mt-1">
                            <i class="fas fa-envelope me-1"></i>' . Html::encode($model->email)
                            . ($model->phone ? ' &middot; <i class="fas fa-phone me-1"></i>' . Html::encode($model->phone) : '') . '
                        </div>
                        ' . ($model->message ? '<div class="mt-2 text-muted">' . nl2br(Html::encode($model->message)) . '</div>' : '') . '
                    </div>
                </div>';
        },
        'emptyText' => 'No inquiries yet.',
        'layout' => "{items}\n<div class='mt-3'>{pager}</div>",
        'options' => ['tag' => 'div'],
    ]) ?>
</div>
