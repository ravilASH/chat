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
     * Тип(ы) моделек встречающихся в конкретном списке моделек (только на этой странице) или тип одной модели
     * @var array|string
     */
    public $dataTypesOrType = [];

    /**
     * название поля, добавляемого в корень ответа  с указанием всех встречающихся типов либо единственного типа
     * если не указано, то тогда не добавляется
     * оно же используется для поиска типов моделек в ответе
     * @var string | null
     */
    public $typeField = 'type';

    protected $linkAndPaginationData = [];

    /**
     * @inheritdoc
     */
    protected function serializeModel($model)
    {
        $data = null;
        if ($this->request->getIsHead()) {
            $data = null;
        }elseif($model instanceof RestSerializable){
            $data =  $model->toRestArray($this->configuratedFields);
        }else {
            list ($fields, $expand) = $this->getRequestedFields();
            $data =  $model->toArray($fields, $expand);
        }

        // наполняем тип модели
        if (
            isset ($model[$this->typeField])
        ){
            $this->dataTypesOrType = $model[$this->typeField];
        }

        return $data;
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

            // наполняем массив типами
            if (
                isset ($models[$i][$this->typeField])
                && !in_array($models[$i][$this->typeField], $this->dataTypesOrType)
            ){
                $this->dataTypesOrType[] = $models[$i][$this->typeField];
            }
        }

        return $models;
    }

    /**
     * Нам нужна только часть родительского без добавления мета информации,
     * посколькуу добавлять мета инфоррмацию мы будем не только в дата-провайдеры
     * @inheritdoc
     */
    protected function serializeDataProvider($dataProvider)
    {
        if ($this->preserveKeys) {
            $models = $dataProvider->getModels();
        } else {
            $models = array_values($dataProvider->getModels());
        }
        $models = $this->serializeModels($models);

        if (($pagination = $dataProvider->getPagination()) !== false) {
            $this->addPaginationHeaders($pagination);
            $this->paginationData = $this->serializePagination($pagination);
        }

        return $models;
    }

    /**
     * Перенес сюда добавление метадаты (не только при сериализации провайдера)
     * @inheritdoc
     */
    public function serialize($data)
    {
        $result = parent::serialize($data);

        if ($this->request->getIsHead()) {
            return null;
        } elseif ($this->collectionEnvelope === null) {
            return $result;
        } else {
            $result = [
                $this->collectionEnvelope => $result,
            ];

            if ($meta = $this->collectMetaData()) {
                $result = array_merge($result, $meta);
            }
            return $result;
        }

    }

    protected function collectMetaData () {
        $meta = [];
        if ($this->linkAndPaginationData) {
            $meta = array_merge($meta, $this->linkAndPaginationData);
        }

        if ($this->typeField) {
            $meta["_".$this->typeField] = $this->dataTypesOrType;
        }

        return $meta;
    }

}