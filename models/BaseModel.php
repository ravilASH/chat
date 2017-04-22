<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 20.04.17
 * Time: 8:27
 */

namespace app\models;


use app\components\RestSerializable;
use app\components\ToRestArrayTrait;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord implements RestSerializable
{
    use ToRestArrayTrait;
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'type';
        return $fields;
    }

    public function getType (){
        return static::tableName();
    }
}