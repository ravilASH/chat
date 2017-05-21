<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 21.05.17
 * Time: 18:44
 */

namespace app\views;


use app\models\User;
use yii\base\Exception;

class RestUser extends ViewModel
{

    public function init(){
        if (!( $this->model instanceof User)) {
            throw new Exception('Данная вьюха работает только с юзерами');
        }
    }

    public function toRestArray($fields = [])
    {
        /** @var User $user */
        $user = $this->model;
        if (!\Yii::$app->user->isGuest){
            $data = [];
            foreach ($fields as $field => $definition) {
                $data[$field] = is_string($definition) ? $user->$definition : call_user_func($definition, $user, $field);
            }
        }else{
            $data = ['id' => $user->id, 'cn' => $user->cn];
        }

        return $data;
    }
}