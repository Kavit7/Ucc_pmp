<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
<<<<<<< HEAD
=======
    'defaultRoute'=>'property/index',
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
<<<<<<< HEAD
            // insert a secret key
            'cookieValidationKey' => 'yxpNPHeFhhnsaYh4bJRl8P4JMZK61Xh8',
=======
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '220sE_-yG6ktrNmPk6TAlvQm2C6GBrsa',
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
<<<<<<< HEAD
            'loginUrl' => ['login/index'], // login page
        ],
        'errorHandler' => [
            // ensure site/error exists
=======
        ],
        'errorHandler' => [
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
<<<<<<< HEAD
=======
            // send all mails to a file by default.
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
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
<<<<<<< HEAD
=======
        
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
<<<<<<< HEAD
                // ensure property-price routes work
                'property-price' => 'property-price/index',
                'property-price/<action:\w+>' => 'property-price/<action>',
            ],
        ],
=======
            ],
        ],
        
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
<<<<<<< HEAD
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
=======
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
<<<<<<< HEAD
=======
        // uncomment the following to add your IP if you are not connecting from localhost.
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
