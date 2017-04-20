<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 20.04.17
 * Time: 8:51
 */

namespace app\components;


use app\models\Chat;
use yii\db\ActiveRecord;
use yii\rest\CreateAction;

class CreateAndReturnParentAction extends CreateAction
{
    public $parentRelationName = null;

    public function run() {
        $model = parent::run();

        $activeRecordClassname = ActiveRecord::className();
        if (
            $this->parentRelationName
            && $model instanceof $activeRecordClassname
            && $parent = $model->getRelation($this->parentRelationName, false)
        ) {
            //return $model->{$this->parentRelationName};
            /** @var Chat $parent */
            $parent = $model->{$this->parentRelationName};
            $parent->getLastMessages($model->id);

            return $parent;
        }else{
            return $model;
        }
    }
}