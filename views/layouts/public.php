<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - <?= Html::encode(Yii::$app->name) ?></title>

    <link href="<?= Yii::getAlias('@web/lib/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= Yii::getAlias('@web/lib/fontawesome/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">
    <?php $this->head() ?>

    <style>
        :root {
            --brand: #4f46e5;
            --brand-dark: #3730a3;
            --brand-light: #eef2ff;
            --ink: #0f172a;
            --muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            font-family: -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #f6f7fb;
            color: var(--ink);
            margin: 0;
        }

        /* Header */
        .public-header {
            background: linear-gradient(135deg, #14091c 0%, #1e1030 100%);
            color: #fff;
            padding: 0.9rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .public-header .brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.01em;
        }

        .public-header .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand), #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .public-header .login-btn {
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            padding: 0.5rem 1.15rem;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .public-header .login-btn:hover {
            background: #fff;
            color: var(--brand-dark);
        }

        .public-nav {
            display: flex;
            align-items: center;
            gap: 1.75rem;
        }
        .public-nav a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.15s ease;
        }
        .public-nav a:hover { color: #fff; }

        .public-header .account-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .public-header .account-icon:hover {
            background: #fff;
            color: var(--brand-dark);
        }

        /* Breadcrumb */
        .public-breadcrumb {
            background: #fff;
            border-bottom: 1px solid #eef0f4;
        }
        .public-breadcrumb .breadcrumb-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0.65rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 0.82rem;
            color: #64748b;
        }
        .public-breadcrumb a { color: #64748b; text-decoration: none; }
        .public-breadcrumb a:hover { color: var(--brand); }
        .public-breadcrumb i { font-size: 0.65rem; color: #cbd5e1; }
        .public-breadcrumb span { color: #0f172a; font-weight: 600; }

        /* Footer */
        .public-footer {
            background: #14091c;
            color: #a1a1aa;
            text-align: center;
            padding: 2.25rem 1rem 1.75rem;
            margin-top: 3.5rem;
        }

        .public-footer .footer-brand {
            color: #fff;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .public-footer small {
            display: block;
            margin-top: 0.75rem;
            color: #71717a;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="public-header">
    <a href="<?= Url::to(['public-listing/index']) ?>" class="brand" style="text-decoration:none;">
        <span class="brand-icon"><i class="fas fa-building"></i></span>
        <span><?= Html::encode(Yii::$app->name) ?></span>
    </a>
    <nav class="public-nav d-none d-md-flex">
        <a href="<?= Url::to(['public-listing/index']) ?>">Home</a>
        <a href="<?= Url::to(['public-listing/index']) ?>#available">Browse Properties</a>
    </nav>
    <div class="d-flex align-items-center gap-3">
        <a href="<?= Url::to(['login/login']) ?>" class="account-icon d-none d-sm-flex" title="Staff / Tenant Login"><i class="fas fa-user"></i></a>
        <a href="<?= Url::to(['login/login']) ?>" class="login-btn"><i class="fas fa-user me-1 d-sm-none"></i> Staff / Tenant Login</a>
    </div>
</div>

<div class="public-breadcrumb">
    <div class="breadcrumb-inner">
        <a href="<?= Url::to(['public-listing/index']) ?>">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span><?= Html::encode($this->title) ?></span>
    </div>
</div>

<?= $content ?>

<div class="public-footer">
    <div class="footer-brand"><?= Html::encode(Yii::$app->name) ?></div>
    <div>Helping you find a place, made simple.</div>
    <small>&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. All rights reserved.</small>
</div>

<script src="<?= Yii::getAlias('@web/lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.js') ?>"></script>
<?php
$flashJs = '';
if (Yii::$app->session->hasFlash('success')) {
    $flashJs .= "Swal.fire({icon:'success', title:'Thanks!', text:" . json_encode(Yii::$app->session->getFlash('success')) . ", confirmButtonColor:'#4f46e5'});\n";
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
