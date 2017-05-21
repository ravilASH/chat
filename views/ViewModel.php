<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 21.05.17
 * Time: 18:37
 */

namespace app\views;


use yii\base\Model;

abstract class ViewModel extends Model
{
    public $model = null;

    abstract public function toRestArray($configFields = []);
}