<?php

namespace app\models;

use Faker\Provider\cs_CZ\DateTime;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $text
 * @property integer $chat_id
 * @property string $date_create
 *
 * @property Chat $chat
 */
class Message extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id'], 'integer'],
            [['date_create'], 'safe'],
            [['text'], 'string', 'max' => 2048],
            [['chat_id'], 'exist', 'skipOnEmpty' => false, 'skipOnError' => true, 'targetClass' => Chat::className(), 'targetAttribute' => ['chat_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'chat_id' => 'Chat ID',
            'date_create' => 'Date Create',
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['chat'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chat::className(), ['id' => 'chat_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->date_create = (new \DateTime())->format('c');
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $push = new Push();
            $push->configuratedFields = [
                'message' => ['id', 'text', 'chat', 'date_create', 'type'],
                'chat' => ['id', 'users', 'type', 'userIds'],
                'user' => ['id', 'displayname', 'type']
            ];
            $this->refresh();
            $push->pushTo = $this->chat->userIds;
            $push->data = $this;
            Yii::$app->push->add($push);
        }
    }
}
