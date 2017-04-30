<?php
namespace app\components\behaviors;

use GuzzleHttp\Client;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\web\Response;

class PushBehavior extends Behavior
{

    public function events()
    {
        return [
            Response::EVENT_AFTER_SEND => 'sendPush',
        ];
    }

    public function sendPush($event)
    {
        /** @var Response $responseObject */
        $responseObject = $event->sender;

        if ($responseObject->statusCode < 400){
            \Yii::$app->push->sendAll();
        }
    }
}
