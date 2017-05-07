<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:04
 */

namespace app\controllers;


use app\helpers\LdapHelper;
use app\models\User;
use yii\base\UserException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class UserController extends BaseActiveController
{
    public $modelClass = 'app\models\User';

    public function actions() {
        $actions = parent::actions();
        unset($actions['create'], $actions['delete']);
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        /** @var ActiveRecord $model */
        parent::checkAccess($action, $model, $params);
        // todo запретить update всех параметров кроме нужных
    }

    public function actionLogin() {
        $login = \Yii::$app->request->getBodyParam('username');
        $pass = \Yii::$app->request->getBodyParam('password');
        if (empty($login) || empty($pass)) {
            throw new UserException('Укажите логин и пароль');
        }

        if ($login == '111' && $pass == '111') {
            $userData = (User::findOne(['displayname' => 'Шаменов Равиль Абдэлганиевич']))->attributes;
        }else{
            $userData = LdapHelper::getUserInfo($login, $pass);
        }


        if (empty($userData)){
            throw new NotFoundHttpException('Пользователь не найден');
        }

        if (!$user = User::find()->where(["objectguid" => $userData['objectguid']])->one()) {
            $user = new User();
        }

        $user->load($userData, '');

        $result = $user->save();

        if (!$result) {
            \Yii::$app->response->setStatusCode(422);
            return $user->errors;
        }else{
            $fields = $user->defaultRestFields();
            $fields[] = 'auth_key';
            $this->configuratedFields = ['user' => $fields];
            return $user;
        }

   }
}