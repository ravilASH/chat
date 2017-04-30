<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 17.04.17
 * Time: 18:51
 */

namespace app\components;


use app\helpers\RestArrayHelper;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class PushSerializer extends Serializer
{

    /**
     * Массив айдишников пользователей кому надо отправить пуши
     * @var array
     */
    public $pushTo = [];

    /**
     * Добавляем еще ключ пушту
     * @inheritdoc
     */
    protected function collectMetaData () {
        $meta = parent::collectMetaData();

        $meta['_pushTo'] = $this->pushTo;
        return $meta;
    }

}