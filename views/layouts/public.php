<?php
use yii\helpers\Html;
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
        body { font-family: 'Inter', 'Roboto', sans-serif; background: #f8fafc; }
        .public-header {
            background: #120912;
            color: #fff;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .public-header h1 { font-size: 1.25rem; margin: 0; }
        .public-footer {
            text-align: center;
            padding: 2rem 1rem;
            color: #6b7280;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="public-header">
    <h1><i class="fas fa-building me-2"></i><?= Html::encode(Yii::$app->name) ?></h1>
    <a href="<?= \yii\helpers\Url::to(['login/login']) ?>" class="btn btn-outline-light btn-sm">Staff / Tenant Login</a>
</div>

<?= $content ?>

<div class="public-footer">
    &copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. All rights reserved.
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
