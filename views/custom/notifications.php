<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Notifications';
?>

<div class="container-fluid mt-4 body">
    <div class="d-flex justify-content-between align-items-center mb-3 p-3"
        style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
        <h3 class="mb-0">Notifications</h3>
        <?= Html::a('<i class="fas fa-check-double me-1"></i> Mark all as read', ['custom/mark-all-notifications-read'], [
            'class' => 'btn btn-sm btn-outline-secondary',
            'data-method' => 'post',
        ]) ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="list-group list-group-flush">
            <?php foreach ($dataProvider->getModels() as $notif): ?>
                <a href="<?= \yii\helpers\Url::to(['custom/read-notification', 'id' => $notif->id]) ?>"
                   data-method="post"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
                   style="<?= $notif->is_read ? '' : 'background:#eef2ff;' ?>">
                    <div>
                        <div class="fw-bold" style="color:#1f2937;">
                            <?php if (!$notif->is_read): ?><span class="dot-unread"></span><?php endif; ?>
                            <?= Html::encode($notif->title) ?>
                        </div>
                        <div class="text-muted small mt-1"><?= Html::encode($notif->message) ?></div>
                    </div>
                    <div class="text-muted small text-nowrap ms-3"><?= Yii::$app->formatter->asRelativeTime($notif->created_at) ?></div>
                </a>
            <?php endforeach; ?>
            <?php if ($dataProvider->getCount() === 0): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-bell-slash mb-2" style="font-size:2rem;"></i>
                    <div>No notifications yet.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-3">
        <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
    </div>
</div>

<style>
    .dot-unread {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4f46e5;
        margin-right: 6px;
    }
</style>
