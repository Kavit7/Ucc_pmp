<?php
use yii\helpers\Html;
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
    <link href="<?= Yii::getAlias('@web/lib/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= Yii::getAlias('@web/lib/fontawesome/css/all.min.css') ?>" rel="stylesheet">
    <link href="<?= Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('<?= Yii::getAlias('@web/images/login-bg.jpg') ?>') no-repeat center 20% fixed;
            background-size: cover;
            position: relative;
            padding: 20px;
        }

        /* Gradient overlay - keeps the image visible while still giving the card contrast */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.55) 0%, rgba(255, 255, 255, 0.12) 100%);
            z-index: 0;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            z-index: 1;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.4s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 42px;
            color: #4f46e5;
            background: #fff;
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .logo p {
            color: #555;
            font-size: 15px;
            margin-top: 8px;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 20px;
            
           
        }
        
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #4f46e5;
            font-size: 18px;
            z-index: 1;
        }
        
        .input-group input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0,0,0,0.2);
            border-radius: 12px;
            color: #333;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .input-group input::placeholder {
            color: #666;
        }
        
        .input-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(79, 70, 229, 0.7);
            box-shadow: 0 0 10px rgba(79, 70, 229, 0.2);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember {
            display: flex;
            align-items: center;
        }
        
        .remember input {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            accent-color: #4f46e5;
        }
        
        .remember label {
            color: #333;
            font-size: 14px;
            cursor: pointer;
        }
        
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4f46e5, #818cf8);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .login-btn:hover {
            background: linear-gradient(135deg, #818cf8, #4f46e5);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .help-note {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 13px;
        }

        footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            color: black;
            font-size: 14px;
            z-index: 1;
        }
        
        @media (max-width: 500px) {
            .login-card {
                padding: 30px 25px;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="login-container">
        <?= $content ?>
    </div>
    
    <footer>
        &copy; <?= date('Y') ?> Property Management Portal. All rights reserved.
    </footer>

    <script src="<?= Yii::getAlias('@web/lib/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= Yii::getAlias('@web/lib/sweetalert2/sweetalert2.min.js') ?>"></script>
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
    <script>
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
