<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:04
 */

namespace app\controllers;



class MessageController extends BaseActiveController
{
    public $modelClass = 'app\models\Message';

    public function actions() {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);
        return $actions;
    }
}