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

class Serializer extends \yii\rest\Serializer
{
    public $collectionEnvelope = 'data';

    public $fieldsParam = 'only_fields';

    /**
     * конфигурация полей для каждой  сущности запроса (применяется если нет в модели своей конфигурации)
     * при пустом параметре и пустом конфигураторе полей в модели применяются дефолтные поля
     *
     * формат
     * [   'type' =>
     *    [
     *        'field1',
     *        'field2' => 'type'
     *        'field3' => function ($model) {
     *             $model->getType();
     *         }
     *    ],
     *    'type2' => ...
     * }
     *
     * можно предусмотреть не только предусмотренные в дефолтный но ои другие поля, в тч со значениями
     * @see fields
     *
     * @var array
     */
    public $configuratedFields = [];

    /**
     * @inheritdoc
     */
    protected function serializeModel($model)
    {
        if ($this->request->getIsHead()) {
            return null;
        }elseif($model instanceof RestSerializable){
            return $model->toRestArray($this->configuratedFields);
        }else {
            list ($fields, $expand) = $this->getRequestedFields();
            return $model->toArray($fields, $expand);
        }
    }

    /**
     * @inheritdoc
     */
    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        $errors =  RestArrayHelper::serializeModelErrors($model);

        $modelData = $this->serializeModel($model);
        $modelData['errors'] = $errors;
        return $modelData;
    }

    /**
     * Обработка  так же и особых сериализуемых моделек
     * @inheritdoc
     */
    protected function serializeModels(array $models)
    {
        foreach ($models as $i => $model) {
            if ($model instanceof RestSerializable) {
                // здесь в сериализатор отправляем массив настроек, который будет пробрасываться по рекурсии
                $models[$i] = $model->toRestArray($this->configuratedFields);
            }elseif ($model instanceof Arrayable) {
                // todo подумать нужно ли рест-сериализовать модели лежащие внутри обычных моделей
                list ($fields, $expand) = $this->getRequestedFields();
                $models[$i] = $model->toArray($fields, $expand);
            } elseif (is_array($model)) {
                $models[$i] = ArrayHelper::toArray($model);
            }
        }

        return $models;
    }

}