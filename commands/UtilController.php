<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\helpers\LdapHelper;
use yii\base\UserException;
use yii\console\Controller;
use app\models\User;

class UtilController extends Controller
{
    /**
     *  Первичная загрузка пользователей в базу
     * @param $password
     * @param string $login
     */
    public function actionLoadUsers($password, $login = 'ravil.shamenov')
    {
        $out = LdapHelper::getAllUserInfo($login, $password);

            $allUsersUids = User::find()->select('objectsid')->asArray()->column();
            foreach ($out as $userData) {
                if (in_array( $userData['objectsid'] , $allUsersUids)) {
                    $user = User::findOne(['objectsid' => $userData['objectsid']]);
                }else{
                    $user = new User();
                }

                $user->load($userData, '');

                $result = $user->save();
                if (!$result){
                    print_r($user->errors);
                }
            }

            return  "Ок";
    }
}
