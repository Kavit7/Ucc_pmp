<?php
use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
<?php $this->beginBody() ?>

<header class="d-flex justify-content-between align-items-center p-2 shadow-sm sticky-top bg-white">
     <div class="">
          <h2 style="color:#7543D1;"><span class="" style="color:#278BF5;">Premier</span>Property</h2>
     </div>
        
<div style="color:#7543D1; " class="d-flex gap-3 me-3 ">
<i class="bi bi-heart fs-4"></i>
<i class="bi bi-question-circle fs-4"></i>
<i class="bi bi-bell fs-4"></i>
<button class="lbtn btn text-white " style="background:#5A09DE;">Login</button>
     </div>  
</header>
<main class="flex-grow-1 container g-0 ms-0 me-0" >
    <?= $content ?>
</main>
<footer class="mt-0 bg-light text-center p-3">
    <p>© <?= date('Y') ?> My App</p>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
