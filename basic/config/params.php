<?php
$local = require(__DIR__ . '/local.php');
$param = [
    'adminEmail' => 'admin@example.com',
];

return \yii\helpers\ArrayHelper::merge($param, $local['param']);
