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
    public $serializer = 'app\components\Serializer';

    /**
     * Массив полей для проброса в сериализатор
     * @var array
     */
    public $configuratedFields = [];

    /**
     * @inheritdoc
     */
    protected function serializeData($data)
    {
        $serializer = \Yii::createObject($this->serializer);
        $serializer->configuratedFields = $this->configuratedFields;
        return $serializer->serialize($data);
    }

    public function init()
    {
        parent::init();
        \Yii::$app->response
            ->headers->add("Access-Control-Allow-Origin", "*")
            ->add("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            ->add('Access-Control-Allow-Headers', 'Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent');
    }
}