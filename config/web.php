<?php
$params = require(__DIR__ . '/params.php');
$local = require(__DIR__ . '/local.php');

if (!$local || !$local['db'] || !$local['mailer'] || !isset($local['developEnv'])) {
    echo 'Не найден или не заполнен файл локальных настроке config/local.php';
    die();
}

$local = require(__DIR__ . '/local.php');

if (!$local || !$local['db'] || !$local['mailer'] || !isset($local['developEnv'])) {
    echo 'Не найден или не заполнен файл локальных настроке config/local.php';
    die();
}

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerMap' => [
        'build-rest-doc' => [
            'sourceDirs' => [
                '@app/controllers',   // <-- path to your API controllers
            ],
            'template' => '//restdoc/restdoc.twig',
            'class' => 'pahanini\restdoc\controllers\BuildController',
            'sortProperty' => 'shortDescription', // <-- default value (how controllers will be sorted)
            'targetFile' => 'path/to/nice-documentation.html'
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Z1VZEsWXq-MII311-z3sM45f64Pb0XGG',
            'enableCsrfValidation'=>false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]

        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => $local['mailer'],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $local['db'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'OPTIONS <url:(.*)>' => 'base-rest/options',
                '<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
                [
                   'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => [
                        'user',
                        'message',
                        'chat'
                    ]
                 ],
            ],
        ],
        'push' => [
            'class' => 'app\components\PushCollection',
        ]
    ],
    'params' => $params,
];

if ($local['developEnv']) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}
return $config;
