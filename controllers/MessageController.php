<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:04
 */

namespace app\controllers;

use app\components\behaviors\PushBehavior;

class MessageController extends BaseActiveController
{
    public $modelClass = 'app\models\Message';

    public function actions() {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);
        return $actions;
    }

    public function beforeAction($action){
        if (parent::beforeAction($action)) {
            if ($action->id == 'create') {
                $this->configuratedFields = [
                    'message' => ['id', 'text', 'chat', 'date_create', 'type'],
                    'chat' => ['id', 'users', 'type', 'userIds'],
                    'user' => ['id', 'displayname', 'type']
                ];

                // todo унести в общий конфиг и там его привязать ко всем ответам
                $component = \Yii::$app->response;
                $component->attachBehavior('PushBehavior', [
                    'class' => PushBehavior::className(),
                ]);
            }

            return true;
        }

        return false;
    }
}