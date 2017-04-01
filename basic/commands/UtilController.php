<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

class UtilController extends Controller
{
    /**
     *  Первичная загрузка пользователей в базу
     * @param $password
     * @param string $login
     */
    public function actionLoadUsers($password, $login = 'ravil.shamenov')
    {
        echo "Первичная загрузка пользователей в базу\n";
    }
}
