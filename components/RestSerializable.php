<?php
/**
 * Created by PhpStorm.
 * User: ravil
 * Date: 22.04.17
 * Time: 10:27
 */

namespace app\components;


interface RestSerializable
{
    /**
     * Возвращает дефолтный список полей для клиента, который может быть изменен содержанием переменной $restFields
     * @return array
     */
    public function defaultRestFields();

    /**
     * Возвращает массив из текущей модели с учетом полей для реста и
     * @return array
     */
    public function toRestArray();
}