<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 22.04.17
 * Time: 13:24
 */

namespace app\helpers;


use app\components\RestSerializable;
use yii\base\Arrayable;
use yii\helpers\BaseArrayHelper;

class RestArrayHelper extends BaseArrayHelper
{
    /**
     * Преобразует модели в массивы с учетом RestSerializable интерфейса
     *
     * @inheritdoc
     * @param array $properties массив конфигов полей для каждого типа моделек
     */
    public static function toArray($object, $properties = [], $recursive = true)
    {
        if (is_array($object)) {
            if ($recursive) {
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $object[$key] = static::toArray($value, $properties, true);
                    }
                }
            }

            return $object;
        } elseif (is_object($object)) {
            if (!empty($properties)) {
                $className = get_class($object);
                if (!empty($properties[$className])) {
                    $result = [];
                    foreach ($properties[$className] as $key => $name) {
                        if (is_int($key)) {
                            $result[$name] = $object->$name;
                        } else {
                            $result[$key] = static::getValue($object, $name);
                        }
                    }

                    return $recursive ? static::toArray($result, $properties) : $result;
                }
            }
            if ($object instanceof RestSerializable) {
                $result = $object->toRestArray($properties);
            }elseif ($object instanceof Arrayable) {
                // todo если поля определены то отдать только эти поля
                $result = $object->toArray([], [], $recursive);
            } else {
                $result = [];
                foreach ($object as $key => $value) {
                    // todo  если поля определены то отдать только эти поля
                    $result[$key] = $value;
                }
            }

            return $recursive ? static::toArray($result, $properties) : $result;
        } else {
            return [$object];
        }
    }

    public static function serializeModelErrors ($model) {
        $errors = [];
        foreach ($model->getFirstErrors() as $name => $message) {
            $errors[] = [
                'field' => $name,
                'message' => $message,
            ];
        }

        return $errors;
    }

}