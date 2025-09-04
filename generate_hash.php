<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';
(new yii\web\Application($config));

// Password mpya
$password = 'admin123';

// Generate hash
$hash = Yii::$app->security->generatePasswordHash($password);

// Generate random auth_key
$authKey = Yii::$app->security->generateRandomString(32);

echo "Password Hash: $hash\n";
echo "Auth Key: $authKey\n";
