<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 22.04.17
 * Time: 11:30
 */

namespace app\components;


use app\helpers\RestArrayHelper;
use app\views\ViewModel;
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
    public function toRestArray($configFields = [], $recursive = true){
        if ($viewModelName = $this->viewModelName($configFields)){
            $viewModel = new $viewModelName(['model' => $this]);
            if ($viewModel instanceof ViewModel) {
                $key = $this->configKey($configFields);
                $fields = isset($configFields[$key])? $configFields[$key] : [];
                // todo добавить обязательный type
                $data = $viewModel->toRestArray($fields);
                return $recursive ? RestArrayHelper::toArray($data, $configFields) : $data;
            }
        }

        return $this->innerToRestArray($configFields, $recursive );
    }

    /**
     * Если надо сериализовать саму модельку
     * @param array $configFields
     * @param bool $recursive
     * @return array
     */
    public function innerToRestArray($configFields, $recursive)
    {
        $data = [];
        foreach ($this->resolveRestFields($configFields) as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $field);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        if ($this->hasErrors()){
            $data['_errors'] = RestArrayHelper::serializeModelErrors($this);
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
        // ключ из мессива конфигураций
        $key = $this->configKey($configFields);

        if ( !empty($this->configuratedRestFields ) ) {
            // либо конфигурация в модели
            // todo принудительно добавить type
            $listOfFields = $this->configuratedRestFields;
        }elseif (!empty($key)){
            // либо в сериализаторе
            // todo принудительно добавить type
            $listOfFields = $configFields[$key];
        }else{
            // или дефолтная конфигурация
            // todo принудительно добавить type
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

    /**
     * @param array $configFields
     * @return string|false получаем здесь название модели или логическое false
     */
    public function viewModelName ($configFields) {
        $keyText = $this->configKey($configFields);
        $type = isset($this->type) ? $this->type : '';
        // todo написать шаблон для неймспейсов
        $pattern = '/'.$type.':(.*)/';
        $matches = [];
        if (
            preg_match($pattern, $keyText, $matches)
            && isset ($matches[1])
        ){
            return $matches[1];
        }

        return false;
    }

    /**
     * @param array $configFields
     * @return string|false получаем здесь подходящий ключ массива конфигурации или логическое false
     */
    public function configKey ($configFields) {
        $type = isset($this->type) ? $this->type : '';

        foreach ($configFields as $sugestType => $fields) {
            // здесь можно добавить логику выбора наиболее подходящего конфига
            // todo написать шаблон для неймспейсов
            $pattern = '/'.$type.':.*/';
            if  ($sugestType === $type || preg_match($pattern, $sugestType)) {
                return $sugestType;
            }
        }

        return '';
    }
}