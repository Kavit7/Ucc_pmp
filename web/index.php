<?php

// Driven by the APP_ENV environment variable so production deployments
// don't depend on remembering to edit this file. Defaults to dev (current
// behavior) when APP_ENV isn't set, e.g. on this local XAMPP setup.
$appEnv = getenv('APP_ENV') ?: 'dev';
defined('YII_DEBUG') or define('YII_DEBUG', $appEnv !== 'prod');
defined('YII_ENV') or define('YII_ENV', $appEnv);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
