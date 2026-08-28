<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'My Profile';

$this->registerCssFile(Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.css'));
$this->registerJsFile(Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.js'));

$avatarUrl = $user->profile_picture
    ? Yii::getAlias('@web/' . $user->profile_picture)
    : Yii::getAlias('@web/images/default-avatar.png');

$fields = [
    ['icon' => 'fa-envelope', 'label' => 'Email', 'value' => $user->email],
    ['icon' => 'fa-phone-alt', 'label' => 'Phone Number', 'value' => $user->phone ?? 'N/A'],
    ['icon' => 'fa-id-card', 'label' => 'National ID', 'value' => $user->national_id ?? 'N/A'],
    ['icon' => 'fa-flag', 'label' => 'Nationality', 'value' => $user->nationality ?? 'N/A'],
    ['icon' => 'fa-briefcase', 'label' => 'Occupation', 'value' => $user->occupation ?? 'N/A'],
    ['icon' => 'fa-user-tag', 'label' => 'Role', 'value' => ucfirst($user->role)],
    ['icon' => 'fa-circle', 'label' => 'Status', 'value' => ucfirst($user->status)],
];
?>

<style>
    .profile-card {
        font-family: 'Inter', 'Roboto', sans-serif;
    }
    .profile-card .card-body {
        padding: 2.5rem;
    }
    .avatar-wrap {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto;
        cursor: pointer;
    }
    .avatar-img {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #e5e7eb, 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .avatar-edit-badge {
        position: absolute;
        bottom: 6px;
        right: 6px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #4f46e5;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        font-size: 14px;
        transition: background 0.2s ease;
    }
    .avatar-wrap:hover .avatar-edit-badge {
        background: #3730a3;
    }
    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin: 1.25rem 0 0.15rem;
    }
    .profile-role-badge {
        display: inline-block;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #4f46e5;
        background: #eef2ff;
        padding: 0.3rem 0.8rem;
        border-radius: 999px;
    }
    .profile-divider {
        border-left: 1px solid #e5e7eb;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.75rem 2.5rem;
    }
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.9rem;
    }
    .info-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }
    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 0.2rem;
    }
    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1f2937;
        word-break: break-word;
    }
    @media (max-width: 767.98px) {
        .info-grid { grid-template-columns: 1fr; }
        .profile-divider { border-left: none; border-top: 1px solid #e5e7eb; padding-top: 2rem; margin-top: 2rem; }
    }
</style>

<div class="container mt-5 profile-card">
    <h1 class="mb-4 font-weight-bold" style="color:#111827;"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-0">
                <!-- Left column: Profile picture and name -->
                <div class="col-md-4 text-center">
                    <?= Html::beginForm(['custom/upload-profile-picture'], 'post', [
                        'enctype' => 'multipart/form-data',
                        'id' => 'profile-picture-form',
                    ]) ?>
                        <div class="avatar-wrap" id="avatarWrap" title="Click to change profile picture">
                            <img id="avatarPreview" src="<?= Html::encode($avatarUrl) ?>" alt="Profile Picture" class="avatar-img">
                            <span class="avatar-edit-badge"><i class="fas fa-camera"></i></span>
                        </div>
                        <input type="file" name="profilePictureFile" id="profilePictureFile"
                               accept=".png,.jpg,.jpeg" class="d-none">
                    <?= Html::endForm() ?>

                    <div class="profile-name"><?= Html::encode($user->full_name) ?></div>
                    <span class="profile-role-badge"><?= Html::encode($user->role) ?></span>
                </div>

                <!-- Right column: Details -->
                <div class="col-md-8 profile-divider ps-md-5 mt-5 mt-md-0">
                    <div class="info-grid">
                        <?php foreach ($fields as $field): ?>
                            <div class="info-item">
                                <div class="info-icon"><i class="fas <?= $field['icon'] ?>"></i></div>
                                <div>
                                    <div class="info-label"><?= Html::encode($field['label']) ?></div>
                                    <div class="info-value"><?= Html::encode($field['value']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
document.getElementById('avatarWrap').addEventListener('click', function () {
    document.getElementById('profilePictureFile').click();
});
document.getElementById('profilePictureFile').addEventListener('change', function () {
    if (this.files && this.files[0]) {
        document.getElementById('avatarPreview').src = URL.createObjectURL(this.files[0]);
        document.getElementById('profile-picture-form').submit();
    }
});
JS;

if (Yii::$app->session->hasFlash('success')) {
    $msg = Yii::$app->session->getFlash('success');
    $js .= "\nSwal.fire({icon:'success', title:'Success!', text:" . json_encode($msg) . ", confirmButtonColor:'#4a90e2'});";
}
if (Yii::$app->session->hasFlash('error')) {
    $msg = Yii::$app->session->getFlash('error');
    $js .= "\nSwal.fire({icon:'error', title:'Error!', text:" . json_encode($msg) . ", confirmButtonColor:'#e74c3c'});";
}
$this->registerJs($js);
?>
