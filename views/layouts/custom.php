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
            --sidebar-width: 280px;
            --sidebar-bg: #1e293b;
            --sidebar-color: #e2e8f0;
            --sidebar-hover-bg: #3b82f6;
            --content-bg: #f1f5f9;
            --header-bg: #ffffff;
            --primary-color: #3b82f6;
            --secondary-color: #64748b;
            --accent-color: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: var(--content-bg);
            overflow-x: hidden;
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            padding: 0 0 20px;
        }

        .nav-link {
            color: var(--sidebar-color);
            padding: 0.9rem 1.5rem;
            display: flex;
            align-items: center;
            margin: 0.3rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
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

        .logout-section {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 1.5rem;
        }

        .logout-link {
            color: #f87171;
            background: rgba(248, 113, 113, 0.1);
        }

        .logout-link:hover {
            background: rgba(248, 113, 113, 0.2);
            color: #fca5a5;
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

        .profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .profile:hover {
            background: #f8fafc;
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
            <a class="nav-link " href="#"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a class="nav-link" href="#"><i class="fas fa-building"></i><span>Properties</span></a>
            <a class="nav-link <?= Yii::$app->controller->id === 'users' ? 'active' : '' ?>"
                href="<?= \yii\helpers\Url::to(['users/index']) ?>">
                    <i class="fas fa-users"></i><span>User management</span>
            </a>


           <a class="nav-link" href="<?= \yii\helpers\Url::to(['custom/leases']) ?>">
                <i class="fas fa-file-contract"></i>
                <span>Lease management</span>
            </a>

             <a class="nav-link" href="#"><i class="fas fa-credit-card"></i><span>Payments</span></a>
            
            
               <a class="nav-link" href="<?= \yii\helpers\Url::to(['property-price/index']) ?>">
                  <i class="fas fa-dollar-sign"></i>
                  <span>Properties Prices</span>
               </a>

            <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a class="nav-link" href="#"><i class="fas fa-user-circle"></i><span>Profile</span></a>
            
         <div class="logout-section">
    <?= \yii\helpers\Html::a(
        '<i class="fas fa-sign-out-alt"></i><span>Logout</span>',
        ['custom/logout'], // controller/action
        ['class' => 'nav-link logout-link']
    ) ?>
</div>

    </div>

    <div class="content-wrapper">
        <header>
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="header-search">
                <i class="fa fa-search search-icon"></i>
                <input type="text" placeholder="Search here..." id="search-input">
            </div>
            
            <div class="header-right">
                <div class="icon-button">
                    <i class="fa fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="icon-button">
                    <i class="fa fa-cog"></i>
                </div>
                
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
            <div class="content-card">
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome back, <?= $userName ?>! Here's what's happening with your properties today.</p>
                
                <div class="mt-4">
                    <?= $content ?>
                </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Close sidebar on mobile after clicking a link
            if (window.innerWidth < 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
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
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>