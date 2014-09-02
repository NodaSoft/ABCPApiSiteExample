<?php

/**
 * Класс обмена данными с REST-службой
 */
class RestJsonClient
{
    /**
     * Отправитель запроса веб-службе
     * @var RestRequestSender
     */
    protected $requestSender = NULL;

    /**
     * Массив адресов веб-службы, к которой делается запрос
     * @var array
     */
    protected $serviceUrls = array();

    protected function __construct(array $serviceUrls = array())
    {
        $this->serviceUrls = $serviceUrls;
    }

    /**
     * Выполняет GET-запрос
     *
     * @param array $requestVars Массив с параметрами запроса
     * @param string $operation Название операции для формирования URL запроса (необязательный)
     * @return array
     */
    protected function getResultsByGet($requestVars, $operation = '')
    {
        return $this->getResults('get', $requestVars, $operation);
    }

    /**
     * Выполняет POST-запрос
     *
     * @param array $requestVars Массив с параметрами запроса
     * @param string $operation Название операции для формирования URL запроса (необязательный)
     * @return array
     */
    protected function getResultsByPost($requestVars, $operation = '')
    {
        return $this->getResults('post', $requestVars, $operation);
    }

    /**
     * Выполняет запрос и возвращает результаты
     *
     * @param string $requestMethod Метод [post,get]
     * @param array $requestVars Массив с параметрами запроса
     * @param string $operation Название операции для формирования URL запроса (необязательный)
     * @return array
     */
    private function getResults($requestMethod, $requestVars, $operation = '')
    {
        $serviceUrls = $this->getServiceUrlsForOperation($operation);
        return $this->requestSender->send(
            $this->buildRequests($serviceUrls, $requestMethod, $requestVars));
    }

    /**
     * Формирует список адресов для запроса
     *
     * @param string $operation
     * @return array
     */
    private function getServiceUrlsForOperation($operation)
    {
        $serviceUrls = $this->serviceUrls;
        foreach ($serviceUrls as $key => $serviceUrl) {
            $serviceUrls[$key] .= $operation;
        }
        return $serviceUrls;
    }

    /**
     * Формирует запросы
     *
     * @param array $serviceUrls Список адресов для запроса
     * @param string $method Метод [post,get]
     * @param array $requestVars Параметры запроса
     * @return RestJsonRequest
     */
    private function buildRequests($serviceUrls, $method, $requestVars)
    {
        return
            array_map(
                function ($url) use ($method, $requestVars) {
                    return new RestJsonRequest($url, $method, $requestVars);
                },
                $serviceUrls);
    }

    /**
     * Приводит результат выполнения запросов к логической переменной
     *
     * @param array $results
     * @return bool
     */
    protected function getBoolResult(array $results)
    {
        return
            array_reduce(
                $results,
                function ($reduced, $oneResult) {
                    return $reduced && $oneResult;
                },
                TRUE);
    }

    /**
     * Приводит результат выполнения запросов к одной переменной
     *
     * @param array $results
     * @param int $defaultValue
     * @return mixed
     */
    protected function getOneResult($results, $defaultValue = 0)
    {
        return
            array_reduce(
                $results,
                function ($reduced, $oneResult) {
                    return !empty($oneResult) ? $oneResult : $reduced;
                },
                $defaultValue);
    }

    /**
     * Приводит результат выполнения запросов к одномерному массиву
     *
     * @param array $results
     * @return array
     */
    protected function getArrayResult($results)
    {
        return
            array_reduce(
                $results,
                function ($reduced, $oneResult) {
                    if (is_null($oneResult) || !is_object($oneResult)) return $reduced;
                    $oneResult = get_object_vars($oneResult);
                    return is_array($oneResult)
                        ? $reduced + $oneResult : $reduced;
                },
                array());
    }

    /**
     * Приводит результат выполнения запросов к двумерному массиву
     *
     * @param array $results
     * @return array
     */
    protected function getTwoDimensionalArrayResult($results)
    {
        return
            array_reduce(
                $results,
                function ($reduced, $oneResult) {
                    if (is_null($oneResult) || !is_object($oneResult)) return $reduced;
                    $oneResult = get_object_vars($oneResult);
                    return is_array($oneResult)
                        ? $reduced + array_map('get_object_vars', $oneResult) : $reduced;
                },
                array());
    }

    /**
     * Приводит результат выполнения запросов к ассоциативному массиву внутри индексированного массива
     *
     * @param array $results
     * @return array
     */
    protected function getAssociativeArrayInArrayResult($results)
    {
        return
            array_reduce(
                $results,
                function ($reduced, $oneResult) {
                    return is_array($oneResult)
                        ? $reduced + array_map('get_object_vars', $oneResult) : $reduced;
                },
                array());
    }

}