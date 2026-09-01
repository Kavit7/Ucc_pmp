<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

// Real key lives in secret-local.php, gitignored and never committed. Falls
// back to a placeholder so the app still boots on a fresh clone, but you
// MUST set a real one before relying on sessions/CSRF/signed cookies.
$secrets = ['cookieValidationKey' => 'CHANGE-ME-see-config/secret-local.php.example'];
$secretsFile = __DIR__ . '/secret-local.php';
if (is_file($secretsFile)) {
    $secrets = array_merge($secrets, require $secretsFile);
}

$config = [
    'id' => 'basic',
    'name' => 'UCC Property Management Portal',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Africa/Nairobi',
    'defaultRoute'=>'dashboard/admin-dash',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => $secrets['cookieValidationKey'],
        ],
        'authManager' => [
        'class' => 'yii\rbac\DbManager', // stores RBAC data in database
    ],
         'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'currencyCode' => 'TZS', // or 'TZS' for Tanzanian Shilling
    ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,


            'loginUrl' => ['login/login'], // login page
        ],
        'errorHandler' => [
            // ensure site/error exists

        ],

        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',

            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,


        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // ensure property-price routes work
                'property-price' => 'property-price/index',
                'property-price/<action:\w+>' => 'property-price/<action>',
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
