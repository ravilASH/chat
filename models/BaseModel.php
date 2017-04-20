<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 20.04.17
 * Time: 8:27
 */

namespace app\models;


use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
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