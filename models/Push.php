<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 30.04.17
 * Time: 14:38
 */

namespace app\models;


use app\components\Serializer;
use yii\base\Model;

class Push extends Model
{
    /**
     * поля всех типов моделек и массивов для сериализации
     * @see Serializer::$configuratedFields
     * @var array | null
     */
    public $configuratedFields = [];

    /**
     * Массив айдишников кому надо отправлять пуши
     * @var array
     */
    public $pushTo = [];

    /**
     * Модели  для сериализации пуша
     * @var mixed
     */
    public $data = null;
}