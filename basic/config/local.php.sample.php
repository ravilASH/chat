<?php

return [
    'developEnv' => true,
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=127.0.0.1;port=5432;dbname=tko_dev2',
        'username' => 'tko_dev',
        'password' => '1wgxxch3',
        'charset' => 'utf8',
    ],
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => true,
    ],
    'param' => [

    ],
];