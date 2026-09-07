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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Two-Factor Authentication</h5>
            <?php if ($user->totp_enabled): ?>
                <p class="text-success mb-3"><i class="fas fa-circle-check me-2"></i>Enabled. You'll be asked for a code from your authenticator app each time you log in.</p>
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#disable2faModal">Disable</button>
            <?php else: ?>
                <p class="text-muted mb-3">Not enabled. Add a second step to your login using an authenticator app (Google Authenticator, Authy, etc.).</p>
                <?= Html::a('Enable Two-Factor Authentication', ['custom/enable-2fa'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($user->totp_enabled): ?>
    <div class="modal fade" id="disable2faModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?= Html::beginForm(['custom/disable-2fa'], 'post') ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Confirm your current password</label>
                        <?= Html::passwordInput('currentPassword', '', ['class' => 'form-control', 'required' => true]) ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Disable</button>
                    </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
