<?php

$params = require __DIR__ . '/params.php';
$local = require __DIR__ . '/local.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'name' => 'PHPKitchen Sample App',
    'aliases' => [
        '@vendor' => dirname(dirname(dirname(__DIR__))) . '/vendor',
        '@runtime' => dirname(__DIR__) . '/Runtime',
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@dev-app' => dirname(dirname(__DIR__)),
        '@views' => dirname(dirname(__DIR__)) . '/View/Templates',
    ],
    'controllerNamespace' => 'PHPKitchen\\Domain\\Dev\\App\\Controllers',
    'layout' => '@views/layouts/main',
    'viewPath' => '@views',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'r736478923yrbfu97278fb78',
        ],
        'cache' => [
            'class' => 'yii\caching\ArrayCache',
        ],
        'user' => [
            'identityClass' => 'App\Models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'defaultRoute' => 'site/index',
            ],
        ],

    ],
    'params' => $params,
];

$config['bootstrap'][] = 'debug';
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
];

$config['bootstrap'][] = 'gii';
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'generators' => [
        \PHPKitchen\Domain\Generator\Domain\ModelGenerator::class,
    ],
];

\yii\helpers\ArrayHelper::merge($config, $local);

return $config;
