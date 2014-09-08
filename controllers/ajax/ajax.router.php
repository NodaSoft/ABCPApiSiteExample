<?php

/**
 * Class AjaxRouter
 * Точка входа для ajax запросов. Где обязательным параметром является:
 * #) $requestVars['action'] - название ajax метода
 * И необязательный
 * #) $requestVars['params'] - массив необходимых параметров для каждого метода
 * Например, /ajax.php?action=getSearchTips&params[number]=123
 */
class AjaxRouter {

    /**
     * @var array|null Входные данные
     */
    protected $requestVars;

    public function __construct($requestVars) {
        $this->requestVars = $requestVars;
    }

    /**
     * Запуск работы роутера
     * @return string
     */
    public function run() {
        $methodName = $this->requestVars['action'];
        if (empty($methodName)) {
            return $this->sendError('Действие не найдено');
        }
        if (!$this->loadModule($methodName)) {
            return $this->sendError('Файл метода не найден');
        }
        $className = 'Ajax\\' . $methodName;

        if (class_exists($className)) {
            $params = NULL;
            if (isset($this->requestVars['params'])) {
                $params = $this->requestVars['params'];
            }
            $class = new $className($params);
            if (method_exists($className, 'checkRequestVars') && !$class->checkRequestVars()) {
                return $this->sendError($class->getErrors());
            }
            $data = $class->run();
            if (is_array($data) || is_object($data)) {
                return $this->sendResponse($data);
            } else {
                return $data;
            }
        } else {
            return $this->sendError("Класс {$className} не найден");
        }
    }

    /**
     * Проверяет на существования модуля обработки ajax запроса и в случае если модуль существует загружает его
     * @param string $methodName
     * @return boolean
     */
    public function loadModule($methodName) {
        $fileName = $this->getClassFileName($methodName);
        if (is_file(__DIR__ . '/methods/' . $fileName . '.php')) {
            require_once __DIR__ . '/methods/' . $fileName . '.php';
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Получение имени файла для класса.
     * Например, метод getSearchTips -> имя файла get.search.tips
     * @param string $methodName
     * @return string
     */
    protected function getClassFileName($methodName) {
        return mb_strtolower(preg_replace('/([A-Z])/', '.$1', $methodName), 'UTF-8');
    }

    /**
     * Получение json успешного результата
     * @param array $data
     * @return string json
     */
    protected function sendResponse($data) {
        header('Content-Type: application/json; charset=UTF-8');
        $response = array(
            'success' => TRUE,
            'result' => $data
        );
        return json_encode($response);
    }

    /**
     * Получение json ошибки
     * @param string|array $errors
     * @return string json
     */
    protected function sendError($errors) {
        header('Content-Type: application/json; charset=UTF-8');
        header('HTTP/1.1 500 Internal Server Error');
        if (!is_array($errors)) {
            $errors = array($errors);
        }
        $response = array(
            'success' => FALSE,
            'errors' => $errors
        );
        return json_encode($response);
    }
}