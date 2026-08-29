<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// Loaded globally so every page can show flash messages consistently -
// previously several pages set a flash (e.g. a blocked delete, a rejected
// file upload) with nowhere to display it, so the message was silently lost.
$this->registerCssFile(Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.css'));
$this->registerJsFile(Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.js'));

$userProfilePic = Yii::$app->user->identity->profile_pic ?? null;
$userName = Yii::$app->user->identity->name ?? 'Guest';
$userRole = Yii::$app->user->identity->role ?? 'User';
$userInitials = strtoupper(substr($userName,0,1) . substr(strrchr($userName,' '),0,1));
if (Yii::$app->user->isGuest && Yii::$app->controller->id != 'login') {
    Yii::$app->response->redirect(Url::to(['login/login']))->send();
    Yii::$app->end(); // Hii inahakikisha rest ya page haifanyi render
}

// Get current route to set active class
$currentRoute = Yii::$app->controller->getRoute();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/lib/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/lib/bootstrap-icons/bootstrap-icons.css') ?>">

    <?php $this->head() ?>
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #120912ff;
            --sidebar-color: #ffffffff;
            --sidebar-hover-bg: #4f46e5;
            --content-bg: #f1f5f9;
            --header-bg: #ffffff;
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --secondary-color: #64748b;
            --accent-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #dc2626;
            --warning-color: #f59e0b;
        }

        /* System-wide button colors: override Bootstrap's default blue so every
           .btn-primary / .btn-outline-primary across the app uses the same brand
           indigo instead of Bootstrap's stock #0d6efd (or the various ad-hoc blues
           that had crept into individual pages). */
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
        }
        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
        .btn-success {
            background-color: var(--success-color) !important;
            border-color: var(--success-color) !important;
        }
        .btn-success:hover, .btn-success:focus, .btn-success:active {
            background-color: #059669 !important;
            border-color: #059669 !important;
        }
        .btn-danger {
            background-color: var(--danger-color) !important;
            border-color: var(--danger-color) !important;
        }
        .btn-danger:hover, .btn-danger:focus, .btn-danger:active {
            background-color: #b91c1c !important;
            border-color: #b91c1c !important;
        }
        .btn-warning {
            background-color: var(--warning-color) !important;
            border-color: var(--warning-color) !important;
            color: #fff !important;
        }
        .btn-warning:hover, .btn-warning:focus, .btn-warning:active {
            background-color: #d97706 !important;
            border-color: #d97706 !important;
            color: #fff !important;
        }
        .btn-link { color: var(--primary-color) !important; }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
            

        body {
        font-family: 'Inter', 'Roboto', sans-serif !important;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        flex-direction: column;
        overflow-x: hidden;
        display: flex;
        background-color: var(--content-bg);
       
    }
             .main-wrapper {
            display: flex;
            flex: 1;
            position: relative;
        }

        .portal-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex;
            justify-content: center;
            margin: 20px 0 30px;
            padding: 0 1.5rem;
        }

        .sidebar-logo img {
            height: 50px;
            width: auto;
            border-radius: 10px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 0 0 30px;
            
        }

                .nav-link {
                color: var(--sidebar-color);
                padding: 1rem 1.5rem;   /* Increased top/bottom padding */
                display: flex;
                align-items: center;
                margin: 0.2rem 1rem;    /* Increased vertical spacing between links */
                border-radius: 12px;
                transition: all 0.3s ease;
                text-decoration: none;
                cursor: pointer;
            }


        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--sidebar-hover-bg);
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

       

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
        }

        header {
            background: var(--header-bg);
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .header-search {
            flex: 1;
            max-width: 500px;
            position: relative;
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .header-search:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .header-search .search-icon {
            color: #94a3b8;
            margin-right: 12px;
            font-size: 1rem;
        }

        .header-search input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 0.95rem;
            background: transparent;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            color: var(--secondary-color);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .icon-button:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Profile Dropdown Styles */
        .profile-dropdown {
            position: relative;
        }
        
        .profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8fafc;
        }

        .profile:hover {
            background: #e2e8f0;
        }

        .profile-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .profile-name {
            font-size: 0.9rem;
            color: #1e293b;
            font-weight: 600;
        }

        .profile-role {
            font-size: 0.8rem;
            color: #64748b;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 250px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            margin-top: 10px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 15px 20px 10px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 5px;
        }

        .dropdown-header .profile-name {
            font-size: 1rem;
            margin-bottom: 3px;
        }

        .dropdown-header .profile-role {
            font-size: 0.85rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 12px;
            color: #94a3b8;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }

        .dropdown-item:hover i {
            color: var(--primary-color);
        }

        .dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 8px 0;
        }

        .portal-content {
            padding: 30px;
            flex: 1;
            
        }

        .content-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            min-height: 300px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #64748b;
            margin-bottom: 25px;
        }

        .footer {
            background: var(--header-bg);
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
            margin-top: auto;
            width: 100%;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        /* Mobile Responsiveness */
        .menu-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 1200px) {
            .footer-content {
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 992px) {
            .portal-sidebar {
                width: 80px;
            }
            
            .content-wrapper {
                margin-left: 80px;
                width: calc(100% - 80px);
            }
            
            .nav-link span {
                display: none;
            }
            
            .profile-info {
                display: none;
            }

            .sidebar-logo img {
                height: 40px;
            }
            
            .logout-section {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }
            
            .overlay.active {
                display: block;
            }
            
            .portal-sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                z-index: 1000;
            }
            
            .portal-sidebar.mobile-open {
                left: 0;
            }
            
            .content-wrapper {
                margin-left: 0;
                width: 100%;
            }
            
            .header-search {
                max-width: 100%;
                order: 3;
                width: 100%;
                margin-top: 15px;
            }

            .nav-link span {
                display: inline;
            }

            .profile-info {
                display: flex;
            }
            
            header {
                padding: 15px 20px;
            }
            
            .dropdown-menu {
                right: 10px;
                width: calc(100vw - 40px);
            }
        }

        @media (max-width: 576px) {
            header {
                padding: 15px;
            }
            
            .portal-content {
                padding: 15px;
            }
            
            .content-card {
                padding: 20px;
            }
            
            .header-right {
                gap: 10px;
            }
            
            .footer {
                padding: 15px;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 10px;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }
            
            .profile-info {
                display: none;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="overlay" id="overlay"></div>

<div class="main-wrapper">
    <div class="portal-sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="<?= Yii::getAlias('@web/image/logo.jpg') ?>" alt="Logo">
    </div>
            
      <nav class="sidebar-nav">
       <a class="nav-link <?= $currentRoute == 'dashboard/admin-dash' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['dashboard/admin-dash']) ?>">
        <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
      </a>



    <?php if (Yii::$app->user->identity->role !== 'tenant'): ?>
        <a class="nav-link <?= $currentRoute == 'property/index' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['property/index']) ?>">
            <i class="fas fa-building"></i>
            <span>Properties</span>
        </a>
    <?php endif; ?>

     <?php if(Yii::$app->user->identity->role==='admin'): ?>
       <a class="nav-link <?= strpos($currentRoute, 'property-price') !== false ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['property-price/index']) ?>">
            <i class="fas fa-dollar-sign"></i>
            <span>Properties Prices</span>
        </a>
     <?php endif; ?>
      
        <a class="nav-link <?= strpos($currentRoute, '/leases') !== false || strpos($currentRoute, 'lease') !== false ? 'active' : '' ?>"  href="<?= \yii\helpers\Url::to(['custom/leases']) ?>">
            <i class="fas fa-file-contract"></i>
            <span><?= Yii::$app->user->identity->role === 'tenant' ? 'My Leases' : 'Lease management' ?></span>
        </a>

    <?php if(Yii::$app->user->identity->role==='admin'): ?>
        <a class="nav-link <?= $currentRoute == 'list-source/create' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['list-source/create']) ?>">
            <i class="fas fa-building"></i>
            <span>Configuration</span>
        </a>
       

        <a class="nav-link <?= $currentRoute == 'property-attribute/create' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['property-attribute/create']) ?>">
            <i class="fas fa-building"></i>
            <span>Property Extra data</span>
        </a>

 <?php endif; ?>
        <!-- Bills -->
        <a class="nav-link <?= strpos($currentRoute, 'custom/bill') !== false ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['custom/bill']) ?>">
            <i class="fas fa-file-invoice"></i>
            <span><?= Yii::$app->user->identity->role === 'tenant' ? 'My Bills' : 'Bills' ?></span>
        </a>

        <!-- Maintenance -->
        <a class="nav-link <?= strpos($currentRoute, 'maintenance') !== false ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['maintenance/index']) ?>">
            <i class="fas fa-tools"></i>
            <span>Maintenance</span>
        </a>

        <!-- Payments -->
        <a class="nav-link <?= strpos($currentRoute, 'custom/payment') !== false ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['custom/payment']) ?>">
            <i class="fas fa-credit-card"></i>
            <span><?= Yii::$app->user->identity->role === 'tenant' ? 'My Payments' : 'Payments' ?></span>
        </a>

       
       <?php if (Yii::$app->user->identity->role === 'admin'||Yii::$app->user->identity->role === 'manager' ): ?>
       <a class="nav-link <?= Yii::$app->controller->id === 'users' ? 'active' : '' ?>"
                href="<?= \yii\helpers\Url::to(['users/index']) ?>">
                    <i class="fas fa-users"></i><span>User management</span>
            </a>
           <?php endif; ?>

    <?php if (Yii::$app->user->identity->role !== 'tenant'): ?>
        <a class="nav-link <?= strpos($currentRoute, 'report') !== false ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['report/index']) ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    <?php endif; ?>

        <a class="nav-link <?= strpos($currentRoute, 'custom/profile') !== false ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['custom/profile']) ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profile</span>
        </a>

    </nav>
</div>


    <div class="content-wrapper">
   <?php
$user = Yii::$app->user->identity;

\app\models\Notification::syncOverdueBillNotifications();
$recentNotifications = \app\models\Notification::find()
    ->where(['user_id' => $user->user_id])
    ->orderBy(['created_at' => SORT_DESC])
    ->limit(6)
    ->all();
$unreadNotifCount = \app\models\Notification::find()
    ->where(['user_id' => $user->user_id, 'is_read' => 0])
    ->count();

$userProfilePic = $user->profile_picture ?? null;
$userInitials = strtoupper(substr($user->full_name ?? 'U', 0, 1));

// Hapa tunahakiki kama file ipo kwenye server
$profileUrl = ($userProfilePic && file_exists(Yii::getAlias('@webroot/' . $userProfilePic)))
    ? Yii::getAlias('@web/' . $userProfilePic)
    : null; // hakuna picha, tutaonyesha initials
?>

<header style="display:flex;align-items:center;justify-content:space-between; padding:0 15px;">
    <div class="header-left" style="display:flex;align-items:center;gap:15px;">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <?php if ($currentRoute === 'dashboard/admin-dash'): ?>
            <div class="header-search">
                <form action="<?= \yii\helpers\Url::to(['property/index']) ?>" method="get" style="display:flex;align-items:center;width:250px;max-width:100%;">
                    <i class="fa fa-search search-icon"></i>
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search properties..." 
                        value="<?= Yii::$app->request->get('q') ?>" 
                        style="border:none;outline:none;flex:1;background:transparent;">
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="header-right" style="display:flex;align-items:center;gap:15px;">
        <div class="icon-button" id="notifToggle">
            <i class="fa fa-bell"></i>
            <?php if ($unreadNotifCount > 0): ?>
                <span class="notification-badge"><?= $unreadNotifCount > 9 ? '9+' : $unreadNotifCount ?></span>
            <?php endif; ?>

            <div class="dropdown-menu" id="notifMenu" style="width:320px; right:-10px;">
                <div class="dropdown-header d-flex justify-content-between align-items-center" style="cursor:default;">
                    <span class="profile-name" style="font-weight:600;">Notifications</span>
                    <?php if ($unreadNotifCount > 0): ?>
                        <?= \yii\helpers\Html::a('Mark all read', ['custom/mark-all-notifications-read'], [
                            'class' => 'text-decoration-none',
                            'style' => 'font-size:0.8rem;',
                            'data-method' => 'post',
                        ]) ?>
                    <?php endif; ?>
                </div>

                <?php if (empty($recentNotifications)): ?>
                    <div class="dropdown-item" style="cursor:default;">No notifications yet.</div>
                <?php else: ?>
                    <?php foreach ($recentNotifications as $notif): ?>
                        <a href="<?= \yii\helpers\Url::to(['custom/read-notification', 'id' => $notif->id]) ?>"
                           data-method="post"
                           class="dropdown-item" style="align-items:flex-start; white-space:normal; <?= $notif->is_read ? '' : 'background:#f5f6ff;' ?>">
                            <i class="fas fa-circle" style="font-size:6px; margin-top:6px; <?= $notif->is_read ? 'visibility:hidden;' : 'color:#4f46e5;' ?>"></i>
                            <div>
                                <div style="font-weight:600; color:#334155; font-size:0.88rem;"><?= \yii\helpers\Html::encode($notif->title) ?></div>
                                <div style="font-size:0.8rem; color:#94a3b8;"><?= \yii\helpers\Html::encode($notif->message) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="dropdown-divider"></div>
                <?= \yii\helpers\Html::a('View all notifications', ['custom/notifications'], ['class' => 'dropdown-item justify-content-center']) ?>
            </div>
        </div>

        <?= \yii\helpers\Html::a('<i class="fa fa-cog"></i>', ['custom/settings'], ['class' => 'icon-button', 'title' => 'Settings']) ?>

        <!-- Profile Dropdown -->
        <div class="profile-dropdown">
            <div class="profile" id="profileToggle" style="display:flex;align-items:center;gap:8px;">
                <?php if ($profileUrl): ?>
                    <img src="<?= $profileUrl ?>" alt="Profile" class="profile-icon" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                    <div class="profile-icon" style="width:40px;height:40px;border-radius:50%;background:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                        <?= $userInitials ?>
                    </div>
                <?php endif; ?>

                <div class="profile-info" style="display:flex;flex-direction:column;">
                    <span class="profile-name"><?= $user->full_name ?></span>
                    <span class="profile-role"><?= ucfirst($user->role) ?></span>
                </div>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <div class="dropdown-header">
                    <div class="profile-name"><?= $user->full_name ?></div>
                    <div class="profile-role"><?= ucfirst($user->role) ?></div>
                </div>

                <div class="dropdown-divider"></div>
               
               <a href="<?= \yii\helpers\Url::to(['custom/change-password']) ?>" class="dropdown-item">
                    <i class="fas fa-key"></i>
                    <span>Change Password</span>
                </a>

                <div class="dropdown-divider"></div>
                
                <?= \yii\helpers\Html::a(
                    '<i class="fas fa-sign-out-alt"></i><span>Logout</span>',
                    ['custom/logout'],
                    ['class' => 'dropdown-item']
                ) ?>
            </div>
        </div>
    </div>
</header>




        <div class="portal-content">
    <div class="mt-4">
        <?= $content ?>
    </div>


        </div>

        <footer class="footer">
            <div class="footer-content">
                <div class="copyright">
                    &copy; <?= date('Y') ?> Property Management System. All rights reserved.
                </div>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Contact Us</a>
                    <a href="#">Help Center</a>
                </div>
            </div>
        </footer>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link:not(.logout-link)');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
    // Set active class on click
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default for links with actual href
            if(this.getAttribute('href') === '#') {
                e.preventDefault();
            }
            
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Store active state in localStorage
            const linkText = this.querySelector('span').textContent;
            localStorage.setItem('activeNavItem', linkText);
            
            // Close sidebar on mobile after clicking a link
            if (window.innerWidth < 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });
    });
    
    // Restore active state from localStorage on page load
    const activeNavText = localStorage.getItem('activeNavItem');
    if (activeNavText) {
        navLinks.forEach(link => {
            if (link.querySelector('span').textContent === activeNavText) {
                link.classList.add('active');
            }
        });
    }
    
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking on overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }
    
    // Close menu when window is resized to desktop size
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }
    });
    
    // Profile dropdown functionality
    const profileToggle = document.getElementById('profileToggle');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (profileToggle && dropdownMenu) {
        profileToggle.addEventListener('click', function(e) {
            // Only handle the toggle click itself. If the click is on
            // something inside the already-open menu (e.g. Logout, which
            // needs a real POST), let it bubble normally instead of
            // stopping propagation - otherwise Yii's data-method="post"
            // handler (bound on document) never sees the click.
            if (dropdownMenu.contains(e.target)) {
                return;
            }
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Close dropdown when clicking on a dropdown item
        const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });
    }

    // Notifications dropdown functionality
    const notifToggle = document.getElementById('notifToggle');
    const notifMenu = document.getElementById('notifMenu');

    if (notifToggle && notifMenu) {
        notifToggle.addEventListener('click', function(e) {
            // Same reasoning as the profile dropdown above: don't swallow
            // clicks on links inside the menu (Mark all read, individual
            // notifications) - they're POST-only and need to reach Yii's
            // document-level data-method handler.
            if (notifMenu.contains(e.target)) {
                return;
            }
            e.stopPropagation();
            notifMenu.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!notifToggle.contains(e.target)) {
                notifMenu.classList.remove('show');
            }
        });
    }
});
</script>

<?php
$flashJs = '';
if (Yii::$app->session->hasFlash('success')) {
    $flashJs .= "Swal.fire({icon:'success', title:'Success!', text:" . json_encode(Yii::$app->session->getFlash('success')) . ", confirmButtonColor:'#4f46e5'});\n";
}
if (Yii::$app->session->hasFlash('error')) {
    $flashJs .= "Swal.fire({icon:'error', title:'Error!', text:" . json_encode(Yii::$app->session->getFlash('error')) . ", confirmButtonColor:'#dc2626'});\n";
}
if ($flashJs) {
    echo "<script>document.addEventListener('DOMContentLoaded', function () {\n{$flashJs}});</script>\n";
}
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>