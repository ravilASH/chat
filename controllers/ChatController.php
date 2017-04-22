<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:04
 */

namespace app\controllers;



use app\models\Chat;
use yii\data\ActiveDataProvider;

class ChatController extends BaseActiveController
{
    public $modelClass = 'app\models\Chat';

    public function actions() {
        $actions = parent::actions();
        return $actions;
    }

    // todo дописать получние только своих чатов

    public function afterAction($action, $result)
    {
        if ($action->id == 'index') {
            /** @var ActiveDataProvider $result  */
            if ($result instanceof Chat){
                foreach ($result->models as $model){
                    $model->configuratedRestFields = ['id'];
                }
            }
        }
        $resultSerylyzed = parent::afterAction($action, $result);
        // your custom code here
        return $resultSerylyzed;
    }
}