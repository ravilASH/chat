<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 17:35
 */

namespace app\controllers;


use yii\rest\Controller;

class BaseRestController extends Controller
{
    public $serializer = 'app\components\Serializer';

    public function init()
    {
        parent::init();
        \Yii::$app->response
            ->headers->add("Access-Control-Allow-Origin", "*")
            ->add("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            ->add('Access-Control-Allow-Headers', 'Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent');
    }

    public function actionOptions()
    {
        return 'ok';
    }
}