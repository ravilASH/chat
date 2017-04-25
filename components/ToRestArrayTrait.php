<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 22.04.17
 * Time: 11:30
 */

namespace app\components;


use app\helpers\RestArrayHelper;
use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

trait ToRestArrayTrait
{

    /**
     * Поля для рест-сериализатора, приоритетны перед дефолтными и конфигурацией сериалайзера,
     * не могут содержать поля вложенных сущностей
     *
     * формат
     *    [
     *        'field1',
     *        'field2' => 'type'
     *        'field3' => function ($model) {
     *             $model->getType();
     *         }
     *    ]
     *
     * можно предусмотреть не только предусмотренные в дефолтный но ои другие поля, в тч со значениями
     *
     * @var array
     */
    public $configuratedRestFields = [];

    /**
     * Дефолтный список для полей
     * @return array
     */
    public function defaultRestFields()
    {
        return self::fields();
    }

    /**
     * Преобразует модели для клиента по особым правилам без участия клинета
     *
     * @param bool $recursive whether to recursively return array representation of embedded objects.
     * @return array the array representation of the object
     */
    public function toRestArray($configFields = [], $recursive = true)
    {
        $data = [];
        foreach ($this->resolveRestFields($configFields) as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $field);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        if ($this->hasErrors()){
            $data['errors'] = RestArrayHelper::serializeModelErrors($this);
        }

        return $recursive ? RestArrayHelper::toArray($data, $configFields) : $data;
    }

    /**
     * Определяет какие поля должны быть представлены клиенту (конфигурирование)
     * @return array the list of fields to be exported.
     */
    protected function resolveRestFields($configFields = [])
    {
        $result = [];
        $type = isset($this->type) ? $this->type : null;

        if ( !empty($this->configuratedRestFields ) ) {
            // либо конфигурация в модели
            $listOfFields = $this->configuratedRestFields;
        }elseif (
            !empty ($configFields)
            && is_string($type)
            && isset($configFields[$type])
        ){
            // либо в сериализаторе
            $listOfFields = $configFields[$type];
        }else{
            // или дефолтная конфигурация
            $listOfFields =  $this->defaultRestFields();
        }
        foreach ($listOfFields as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }

            $result[$field] = $definition;
        }

        return $result;
    }
}