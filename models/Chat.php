<?php

namespace app\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "chat".
 *
 * @property integer $id
 * @property string $name
 * @property integer $creator
 * @property string $date_create
 * @property string $date_update
 *
 * @property User[] $Users
 * @property Message[] $lastMessages
 * @property array $usersIds
 */
class Chat extends BaseModel
{
    /**
     * Получаемый с фронта перечень айдишников  пользователей при его создании
     * todo переделать на получение моделек пользователей? согласовать с фронтом
     * @var null|array
     */
    public $_userIds = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['creator'], 'integer'],
            [['date_create', 'date_update'], 'safe'],
            [['userIds'], 'safe'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[]= 'userIds';
        $fields[] = 'users';
        $fields[] = 'lastMessages';
        return $fields;
    }

    public function defaultRestFields()
    {
        $restFields = parent::defaultRestFields();
        $restFields[] = 'users';
        return $restFields;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'creator' => 'Creator',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
        ];
    }

    /**
     * @param $usersIds
     */
    public function setUserIds($usersIds)
    {
        $this->_userIds = $usersIds;
    }

    /**
     * @return array
     */
    public function getUserIds ()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('chat_to_user', ['chat_id' => 'id'])
            ->select('user.id')
            ->column();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('chat_to_user', ['chat_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastMessages()
    {
        return $this->hasMany(Message::className(), ['chat_id' => 'id'])->orderBy('date_create DESC');
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert){
                $this->date_create = (new \DateTime())->format('c');

                // todo айди пользователя определять по аутентификации
                $headers = Yii::$app->request->getHeaders();
                $xToken = $headers->get('X-Token');
                $user = User::findOne(['auth_key' => $xToken]);
                if (!$user) {
                    throw new Exception("Не получилось определить пользователя");
                }
                $this->creator = $user->id;
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        // после создания чата связываем его с пользователями
        $otherUserIds = $this->_userIds;
        if (!$otherUsers = User::findAll(['id' => $otherUserIds])) {
            throw new Exception("Не получилось определить других пользователей");
        }
        // todo айди пользователя определять по аутентификации
        $headers = Yii::$app->request->getHeaders();
        $xToken = $headers->get('X-Token');
        $user = User::findOne(['auth_key' => $xToken]);
        if (!$user) {
            throw new Exception("Не получилось определить пользователя");
        }

        // уникальных пользователей вместе с самим пользователем должно быть больше двух
        // todo придумать валидацию чтобы модель не полученная с фронта тоже валидировалась хорошо
        $users = [$user->id];
        $usersMap[$user->id] = $user;
        foreach ($otherUsers as $otherUser){
            $users[] = $otherUser->id;
            $usersMap[$otherUser->id] = $otherUser;
        }
        $unicUsers = array_unique($users);
        if (count($unicUsers)>1) {
            foreach ($unicUsers as $unicUser){
                // забыли что это будет и при updatre todo исправить
                $this->link('users', $usersMap[$unicUser]);
            }
        } else {
            throw new Exception("Участников должно быть два и более");
        }
        parent::afterSave($insert, $changedAttributes);
    }

    //todo продумать удаление чата
}
