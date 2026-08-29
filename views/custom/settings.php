<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Users $user */

$this->title = 'Settings';

$this->registerCssFile(Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.css'));
$this->registerJsFile(Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.js'));
?>

<div class="container mt-5">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Notifications</h5>
            <?= Html::beginForm(['custom/update-settings'], 'post') ?>
                <div class="form-check form-switch">
                    <?= Html::checkbox('notifications_enabled', $user->notifications_enabled, [
                        'class' => 'form-check-input',
                        'id' => 'notifPref',
                        'value' => 1,
                    ]) ?>
                    <label class="form-check-label" for="notifPref">
                        Receive in-app notifications (overdue bills, new leases, new users)
                    </label>
                </div>
                <p class="text-muted small mt-2 mb-3">
                    When off, no new notifications will be created for your account. You can still view past ones.
                </p>
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary btn-sm']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="mb-3">Account</h5>
            <div class="list-group list-group-flush">
                <?= Html::a('<i class="fas fa-key me-2"></i> Change Password', ['custom/change-password'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?= Html::a('<i class="fas fa-user-circle me-2"></i> View Profile', ['custom/profile'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?php if (in_array($user->role, ['admin', 'manager'])): ?>
                    <?= Html::a('<i class="fas fa-users me-2"></i> User Management', ['users/index'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?php endif; ?>
                <?php if ($user->role === 'admin'): ?>
                    <?= Html::a('<i class="fas fa-building me-2"></i> System Configuration', ['list-source/create'], ['class' => 'list-group-item list-group-item-action']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php // Flash messages are now rendered globally by the layout. ?>
