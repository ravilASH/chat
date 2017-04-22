<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 17.04.17
 * Time: 18:51
 */

namespace app\components;


use phpDocumentor\Reflection\DocBlock\Tags\Link;
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class Serializer extends \yii\rest\Serializer
{
    public $collectionEnvelope = 'data';

    public $fieldsParam = 'only_fields';

    /**
     * Serializes a model object.
     * @param Arrayable $model
     * @return array the array representation of the model
     */
    protected function serializeModel($model)
    {
        if ($this->request->getIsHead()) {
            return null;
        } else {
            list ($fields, $expand) = $this->getRequestedFields();
            return $model->toArray($fields, $expand);
        }
    }

    /**
     * Serializes a set of models.
     * @param array $models
     * @return array the array representation of the models
     */
    protected function serializeModels(array $models)
    {
        foreach ($models as $i => $model) {
            if ($model instanceof RestSerializable) {
                $models[$i] = $model->toRestArray();
            }elseif ($model instanceof Arrayable) {
                list ($fields, $expand) = $this->getRequestedFields();
                $models[$i] = $model->toArray($fields, $expand);
            } elseif (is_array($model)) {
                $models[$i] = ArrayHelper::toArray($model);
            }
        }

        return $models;
    }

}