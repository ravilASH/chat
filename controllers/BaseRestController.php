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

    /**
     * Serializes the specified data.
     * The default implementation will create a serializer based on the configuration given by [[serializer]].
     * It then uses the serializer to serialize the given data.
     * @param mixed $data the data to be serialized
     * @return mixed the serialized data.
     */
    protected function serializeData($data)
    {
        return \Yii::createObject($this->serializer)->serialize($data);
    }

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