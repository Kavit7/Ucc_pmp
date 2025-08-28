<?php
use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$userProfilePic = Yii::$app->user->identity->profile_pic ?? null;
$userName = Yii::$app->user->identity->name ?? 'Guest';
$userRole = Yii::$app->user->identity->role ?? 'User';
$userInitials = strtoupper(substr($userName,0,1) . substr(strrchr($userName,' '),0,1));
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php $this->head() ?>
    <style>
        :root {
            --sidebar-width: 270px;
            --sidebar-bg: #374151;
            --sidebar-color: #ffffff;
            --sidebar-hover-bg: #2563EB;
            --content-bg: #EAEBEB;
        }

        body {
            font-family: 'SF Pro Display', sans-serif;
            background-color: var(--content-bg);
            overflow-x: hidden;
        }

        .portal-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            color: var(--sidebar-color);
            z-index: 1000;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            padding: 0 1rem;
        }

        .nav-link {
            color: var(--sidebar-color);
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--sidebar-hover-bg);
            color: #fff;
            border-radius: 1rem;
            transform: scale(0.93);
        }

        .logout-item {
            margin-top: auto;
            border-top: 1px solid rgba(223, 221, 221, 0.1);
            padding-top: 1rem;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        .header-search {
            flex: 1;
            max-width: 400px;
            position: relative;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #EAEBEB;
            border-radius: 20px;
            padding: 5px 10px;
        }

        .header-search .search-icon {
            color: #999;
            margin-right: 8px;
            cursor: pointer;
            font-size: 1rem;
        }

        .header-search input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 0.95rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #EAEBEB;
            color: #000;
            font-weight: 600;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .profile-name {
            font-size: 0.85rem;
            color: #000;
        }

        .profile-role {
            font-size: 0.85rem;
            color: #666;
        }

        .portal-content {
            margin-left: calc(var(--sidebar-width) + 15px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        @media (max-width:768px){
            .portal-sidebar { width: 70px; }
            .portal-content { margin-left: 85px; }
            .nav-link span { display: none; }
            header { margin-left: 70px; width: calc(100% - 70px); }
        }

        @media (max-width:576px){
            .portal-sidebar { position: absolute; height:auto; }
            .portal-content { margin-left:0; width:100%; }
            .header-search { max-width: 100%; }
            .profile-name { display: none; }
            header { margin-left:0; width:100%; }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="portal-sidebar">
    <div class="sidebar-logo">
        <img src="<?= Yii::getAlias('@web/images/logo.png') ?>" alt="Logo" style="height:50px; width:auto;">
    </div>
    
    <nav class="nav flex-column">
        <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a class="nav-link" href="#"><i class="fas fa-building"></i><span>Properties</span></a>
        <a class="nav-link" href="#"><i class="fas fa-users"></i><span>User management</span></a>
        <a class="nav-link" href="#"><i class="fas fa-file-contract"></i><span>Lease management</span></a>
        <a class="nav-link" href="#"><i class="fas fa-credit-card"></i><span>Payments</span></a>
        <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
        <a class="nav-link" href="#"><i class="fas fa-user-circle"></i><span>Profile</span></a>
        <a class="nav-link logout-item" href="#"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </nav>
</div>

<header>
    <div class="header-search">
        <i class="fa fa-search search-icon"></i>
        <input type="text" placeholder="Search here..." id="search-input">
    </div>
    <div class="header-right">
        <i class="fa fa-bell notification-icon"></i>
        <i class="fa fa-cog settings-icon"></i>
        <div class="profile">
            <?php if($userProfilePic): ?>
                <img src="<?= Yii::getAlias('@web/uploads/' . $userProfilePic) ?>" alt="Profile" class="profile-icon">
            <?php else: ?>
                <div class="profile-icon"><?= $userInitials ?></div>
            <?php endif; ?>
            <div class="profile-info">
                <span class="profile-name"><?= $userName ?></span>
                <span class="profile-role"><?= $userRole ?></span>
            </div>
        </div>
    </div>
</header>

<div class="portal-content">
    <div class="mt-4 flex-grow-1">
        <?= $content ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>