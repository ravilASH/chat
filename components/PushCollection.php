<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 30.04.17
 * Time: 14:34
 */

namespace app\components;


use app\models\Push;
use GuzzleHttp\Client;
use yii\base\Component;
use yii\helpers\Json;

class PushCollection extends Component
{
    /**
     * Конфигурируемое поле сериализатора
     * @var string
     */
    public $serializer = 'app\components\PushSerializer';

    /**
     * Массив пушей этого запроса
     * @var Push[]
     */
    protected $pushs = [];

    public function add(Push $push){
        $this->pushs[] = $push;
    }

    /**
     * Отправлояет все пуши клиентам
     */
    public function sendAll() {
        foreach ($this->pushs as $push){
            if ($push->data !== null && !empty($push->pushTo)){
                $data = $this->serializeData($push->data, $push->pushTo, $push->configuratedFields);
                $formattedData = Json::encode($data);
                $this->send($formattedData);
            }else{
                // если некому или нечего не оправляем ничего
            }
        }
    }

    /**
     * Превращаем модели в массивы рекурсивно с учетом конфига и еще добавляем в него кому отдаем пуши
     */
    protected function serializeData($data, $users = [], $fieldsConfig = [])
    {
        $serializer = \Yii::createObject($this->serializer);
        /** @var PushSerializer $serializer  */
        $serializer->pushTo = $users;
        $serializer->configuratedFields = $fieldsConfig;
        return $serializer->serialize($data);
    }

    /**
     * Отправляет один пуш клиенту
     * @param $requestString
     */
    protected function send($requestString) {
        try {
            // todo асинхронную отппавку здесь или в предыдущем вызове
            $client = new Client();
            $resp = $client->post(\Yii::$app->params['nodePushUrl'], ['body' => $requestString]);
            \Yii::trace($resp);
        }catch (\Exception $ex){
            \Yii::error($ex->getMessage());
        }
    }
}