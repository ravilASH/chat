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
     * Поля для рест-сериализатора, приоритетны перед дефолтными, могут содержать поля вложенных сущностей
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
    public function toRestArray($recursive = true)
    {
        $data = [];
        foreach ($this->resolveRestFields() as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $field);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        return $recursive ? RestArrayHelper::toArray($data) : $data;
    }

    /**
     * Определяет какие поля должны быть представлены клиенту (конфигурирование)
     * @return array the list of fields to be exported.
     */
    protected function resolveRestFields()
    {
        $result = [];

        if ( empty($this->configuratedRestFields ) ) {
            $listOfFields =  $this->defaultRestFields();
        }else{
            $listOfFields = $this->configuratedRestFields;
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