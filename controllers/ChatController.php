<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:04
 */

namespace app\controllers;


class ChatController extends BaseActiveController
{
    public $modelClass = 'app\models\Chat';

    public function actions() {
        $actions = parent::actions();
        return $actions;
    }

    public function beforeAction($action){
        if (parent::beforeAction($action)) {
            if ($action->id == 'index') {
                $this->configuratedFields = [
                    'chat' => ['id', 'type', 'users'],
                    'user:app\views\RestUser' => ['id', 'displayname', 'type']
                ];

            }

            return true;
        }

        return false;
    }
}