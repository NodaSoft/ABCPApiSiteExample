<?php

namespace Ajax;

/**
 * Базовый класс для ajax методов
 * @package Ajax
 */
class Base {

    /**
     * @var array|null Входные данные
     */
    protected $requestVars;

    /**
     * @var array Список ошибок
     */
    protected $errors = array();

    public function __construct($requestVars) {
        $this->requestVars = $requestVars;
    }

    public function run() {
        return NULL;
    }

    /**
     * Получить список ошибок
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
}