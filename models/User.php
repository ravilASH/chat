<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $objectsid
 * @property string $cn
 * @property string $mail
 * @property string $dn
 * @property string $sn
 * @property string $displayname
 * @property string $title
 * @property string $objectguid
 * @property string $password
 * @property string $auth_key
 * @property string $thumbnailphoto
 * @property boolean $inactive
 * @property boolean $veryfied
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['objectsid', 'auth_key'], 'required'],
            [['thumbnailphoto'], 'string'],
            [['inactive', 'veryfied'], 'boolean'],
            [['objectsid', 'mail'], 'string', 'max' => 100],
            [['cn', 'dn', 'sn', 'displayname', 'title', 'objectguid'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['objectsid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'objectsid' => 'Objectsid',
            'cn' => 'Cn',
            'mail' => 'Mail',
            'dn' => 'Dn',
            'sn' => 'Sn',
            'displayname' => 'Displayname',
            'title' => 'Title',
            'objectguid' => 'Objectguid',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'thumbnailphoto' => 'Thumbnailphoto',
            'inactive' => 'Inactive',
            'veryfied' => 'Veryfied',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if ($insert){
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }else{
            return false;
        }
    }
}
