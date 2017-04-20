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
        \Yii::trace(\GuzzleHttp\json_encode($responseObject->statusCode));
        if ($responseObject->statusCode < 400){
            $client = new Client();
            $resp = $client->post(\Yii::$app->params['nodePushUrl'], ['body' => $responseObject->content]);
            \Yii::trace($resp);
        }
    }
}
