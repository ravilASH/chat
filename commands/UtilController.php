<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

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
        $ds=ldap_connect("172.29.134.240");
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if ($ds) {

            $r=ldap_bind($ds,"alt\\".$login,$password);

            $dn="OU=HQ,DC=alt,DC=altarix,DC=ru";
            $sr = ldap_search($ds, $dn, "(objectClass=user)", array('distinguishedname','department', 'displayname', 'cn', 'sn','title','company','mail','jpegphoto','thumbnailphoto','objectguid','objectsid'));

            //toDo поправить импорт контактов
            $info = ldap_get_entries($ds, $sr);

            $out =array();
            for ($i=0; $i<$info["count"]; $i++) {
                $out[] = array(
                    "cn" => (isset($info[$i]['cn']))? iconv ( "Windows-1251" , "UTF-8", $info[$i]['cn']["0"]):null,
                    "distinguishedname" => (isset($info[$i]['distinguishedname']))?iconv ( "Windows-1251" , "UTF-8",$info[$i]['distinguishedname']["0"]):null,
                    "department" => (isset($info[$i]['department']))? iconv ( "Windows-1251" , "UTF-8",$info[$i]['department']["0"]):null,
                    "displayname" => (isset($info[$i]['displayname']))? iconv ( "Windows-1251" , "UTF-8",$info[$i]['displayname']["0"]):null,
                    "dn" => (isset($info[$i]['dn']))? iconv ( "Windows-1251" , "UTF-8", $info[$i]['dn']):null,
                    "sn" => (isset($info[$i]['sn']))? iconv ( "Windows-1251" , "UTF-8", $info[$i]['sn']["0"]):null,
                    "title" => (isset($info[$i]['title']))? iconv ( "Windows-1251" , "UTF-8", $info[$i]['title']["0"]):null,
                    "mail" => (isset($info[$i]['mail']))? iconv ( "Windows-1251" , "UTF-8", $info[$i]['mail']["0"]):null,
                    "thumbnailphoto" => (isset($info[$i]['thumbnailphoto']))? base64_encode($info[$i]['thumbnailphoto']["0"] ):null,
                    "jpegphoto" => (isset($info[$i]['jpegphoto']))? base64_encode($info[$i]['jpegphoto']["0"] ):null,
                    "objectsid" => (isset($info[$i]['objectsid']))? base64_encode($info[$i]['objectsid']["0"] ):null,
                    "objectguid" => (isset($info[$i]['objectguid']))? base64_encode($info[$i]['objectguid']["0"] ):null,
                );
            }

            ldap_close($ds);

            foreach ($out as $userData) {
                $user = new User();
                $user->cn = $userData['cn'];
                $user->objectsid = $userData['objectsid'];
                $user->mail = $userData['mail'];
                $user->dn = $userData['dn'];
                $user->sn = $userData['sn'];
                $user->displayname = $userData['displayname'];
                $user->title = $userData['title'];
                $user->objectguid = $userData['objectguid'];
                $user->auth_key = \Yii::$app->security->generateRandomString();
                $user->isadmin = false;
                $user->thumbnailphoto = $userData['thumbnailphoto'];
                $user->inactive = (strpos( $userData['dn'] , "Отключенные_пользователи") === false)? false : true;

                $result = $user->save();
                if (!$result){
                    throw new UserException(array_shift($user->getFirstErrors()));
                }
            }

            return $this->render('info', [
                'info' => "Ок",
            ]);

        } else {
            throw new UserException ("Невозможно подключиться к серверу LDAP");
        }
    }
}
