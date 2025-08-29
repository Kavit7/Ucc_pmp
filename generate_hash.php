<?php
// Include Yii bootstrap
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

// Use same config as web app
$config = require __DIR__ . '/config/web.php';
(new yii\web\Application($config));

// Generate password hash and auth key
$hash = Yii::$app->getSecurity()->generatePasswordHash('admin123');
$authKey = Yii::$app->getSecurity()->generateRandomString(32);

echo "Password Hash: $hash\n";
echo "Auth Key: $authKey\n";
