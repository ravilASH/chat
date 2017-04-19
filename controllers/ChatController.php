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
        unset($actions['update'], $actions['delete']);
        return $actions;
    }

    // todo дописать получние только своих чатов
}