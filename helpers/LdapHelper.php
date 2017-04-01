<?php

namespace app\helpers;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 01.04.17
 * Time: 16:20
 */

class LdapHelper
{
    public static function getUserInfo ($username, $pass) {
        $search = "mailnickname=". $username;
        $out = self::getLdapInfo($username, $pass, $search);
        if (!is_array($out) || count($out) > 1) {
            throw new Exception("Нашлось более одного пользователя");
        }
        return $out[0];
    }

    public static function getAllUserInfo ($username, $pass) {
        $out = self::getLdapInfo($username, $pass);
        return $out;
    }

    /**
     *
     * @param $login
     * @param $password
     * @param bool $onlyMy запрос по одному или всем пользователям
     * @return array
     * @throws \yii\base\Exception
     */
    protected static function getLdapInfo ($login,$password, $searchPhrase = '(objectClass=user)') {
        $ds=ldap_connect("172.29.134.240");
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if ($ds) {

            $r = ldap_bind($ds, "alt\\" . $login, $password);

            $dn = "OU=HQ,DC=alt,DC=altarix,DC=ru";
            $sr = ldap_search($ds, $dn, $searchPhrase, array('distinguishedname', 'department', 'displayname', 'cn', 'sn', 'title', 'company', 'mail', 'jpegphoto', 'thumbnailphoto', 'objectguid', 'objectsid'));

            $info = ldap_get_entries($ds, $sr);

            $out = array();
            for ($i = 0; $i < $info["count"]; $i++) {
                $out[] = array(
                    "cn" => isset($info[$i]['cn']) ? $info[$i]['cn']["0"] : null,
                    "distinguishedname" => (isset($info[$i]['distinguishedname'])) ? $info[$i]['distinguishedname']["0"] : null,
                    "department" => (isset($info[$i]['department'])) ? $info[$i]['department']["0"] : null,
                    "displayname" => (isset($info[$i]['displayname'])) ? $info[$i]['displayname']["0"] : null,
                    "dn" => (isset($info[$i]['dn'])) ? $info[$i]['dn'] : null,
                    "sn" => (isset($info[$i]['sn'])) ? $info[$i]['sn']["0"] : null,
                    "title" => (isset($info[$i]['title'])) ? $info[$i]['title']["0"] : null,
                    "mail" => (isset($info[$i]['mail'])) ? $info[$i]['mail']["0"] : null,
                    "thumbnailphoto" => (isset($info[$i]['thumbnailphoto'])) ? base64_encode($info[$i]['thumbnailphoto']["0"]) : null,
                    "jpegphoto" => (isset($info[$i]['jpegphoto'])) ? base64_encode($info[$i]['jpegphoto']["0"]) : null,
                    "objectsid" => (isset($info[$i]['objectsid'])) ? base64_encode($info[$i]['objectsid']["0"]) : null,
                    "objectguid" => (isset($info[$i]['objectguid'])) ? base64_encode($info[$i]['objectguid']["0"]) : null,
                );
            }

            ldap_close($ds);

            return $out;
        }else{
            throw new \yii\base\Exception("Не получилось поключиться к серверу LDAP");
        }
    }
}