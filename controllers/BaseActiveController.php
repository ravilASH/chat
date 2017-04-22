<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:50
 */

namespace app\controllers;


use yii\rest\ActiveController;

class BaseActiveController extends ActiveController
{

    public function init()
    {
        parent::init();
        \Yii::$app->response
            ->headers->add("Access-Control-Allow-Origin", "*")
            ->add("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            ->add('Access-Control-Allow-Headers', 'X-Token,Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent');
    }
}