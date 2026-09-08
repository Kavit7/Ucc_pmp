<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
            'messageClass' => 'yii\symfonymailer\Message'
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // Unit tests run outside a real HTTP request, so
            // yii\web\Application::bootstrap() can't derive @webroot from
            // $_SERVER['SCRIPT_FILENAME'] the way it does for a real
            // request - it falls back to the current working directory.
            // Any test saving a file via Yii::getAlias('@webroot/...') then
            // writes to the project root instead of web/, leaving stray
            // folders behind. Setting scriptFile explicitly makes Yii
            // derive @webroot from it correctly, same as a live request.
            'scriptFile' => __DIR__ . '/../web/index.php',
            'scriptUrl' => '/index.php',
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
