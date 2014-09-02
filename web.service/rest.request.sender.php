<?php

/**
 * Класс отправки запроса веб-службе.
 *  - формирует POST и GET запросов к веб-службе из данных более высокого уровня,
 *  - декодирует ответ в зависимости от используемого формата данных,
 *  - пробрасывает исключения со стороны веб-службы в клиентский код
 */
class RestRequestSender {
    private $port = NULL;

    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    public function getPort() {
        return $this->port;
    }

    /**
     * Отправляет запрос веб-службе
     *
     * @param array $requests
     * @return array|null
     */
    public function send(array $requests) {
        $requestsCount = count($requests);
        if (0 === $requestsCount) {
            return NULL;
        } elseif (1 === $requestsCount) {
            $response = $this->singleRequest($requests[0]);
        } else {
            $response = $this->multiRequest($requests);
        }

        $this->processHttpErrors($response);

        $responseBodies = array();
        foreach ($response as $oneResponse) {
            $responseBodies[] = $this->responseDecode($oneResponse['body']);
        }

        return $responseBodies;
    }

    /**
     * Отправляет одиночный запрос веб-службе
     *
     * @param RestRequest $request
     * @return array
     */
    private function singleRequest(RestRequest $request) {
        $response = array();

        $curlHandle = curl_init();
        $this->setCurlOptions($curlHandle, $request);
        $res = curl_exec($curlHandle);

        $response[0]['info'] = curl_getinfo($curlHandle);

        if (FALSE !== $res) {
            $response[0]['body'] = $res;
        }

        curl_close($curlHandle);

        return $response;
    }

    /**
     * Отправляет серию запросов
     *
     * @param array $requests
     * @return array
     */
    private function multiRequest(array $requests) {
        $curlMultiHandle = curl_multi_init();
        $curls = array();

        $i = 0;
        foreach ($requests as $oneRequest) {
            $curls[$i] = curl_init();
            $this->setCurlOptions($curls[$i], $oneRequest);
            curl_multi_add_handle($curlMultiHandle, $curls[$i]);
            $i++;
        }

        $active = FALSE;
        do {
            $status = curl_multi_exec($curlMultiHandle, $active);
            if ($status === CURLM_CALL_MULTI_PERFORM || $active) {
                usleep(10000);
            }
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        $response = array();
        foreach ($curls as $id => $content) {
            $response[$id]['body'] = curl_multi_getcontent($content);
            $response[$id]['info'] = curl_getinfo($content);
            curl_multi_remove_handle($curlMultiHandle, $content);
            curl_close($content);
        }

        curl_multi_close($curlMultiHandle);

        return $response;
    }

    /**
     * Проверяет ответ на содержание ошибок
     * Ошибка генерируется только в том случае если все запросы завершились неудачей,
     * исключение генерируется только по первой ошибке в списке
     *
     * @param array $responses
     */
    private function processHttpErrors(array $responses) {

        if (0 == count($responses)) {
            return;
        }

        $hasError = array_reduce($responses, function ($reduces, $oneResponse) {
            return $reduces & (500 == $oneResponse['info']['http_code']);
        }, TRUE);

        if ($hasError) {
            $this->throwExceptionFromResponse($responses[0]['body']);
        }
    }

    protected function throwExceptionFromResponse($responseBody) {
        if (!empty($responseBody)) {
            $object = $this->responseDecode($responseBody);
            $this->throwServiceException($object->errorCode, $object->errorMessage);
        } else {
            $this->throwServiceException(ServiceErrors::UNKNOWN_ERROR);
        }
    }

    protected function throwServiceException($errorCode, $errorMessage = '') {

        if (empty($errorCode)) {
            $this->throwServiceUnknownException($errorMessage);
        }

        switch ($errorCode) {
            case ServiceErrors::REQUEST_SYNTAX_ERROR:
                throw new ServiceRequestSyntaxException($errorMessage);
                break;
            case ServiceErrors::REQUEST_PARAMETER_NOT_FOUND_ERROR:
                throw new ServiceRequestParameterNotFoundException($errorMessage);
                break;
            case ServiceErrors::REQUEST_PARAMETER_ERROR:
                throw new ServiceRequestParameterErrorException($errorMessage);
                break;
            case ServiceErrors::UNKNOWN_OPERATION:
                throw new ServiceUnknownOperationException($errorMessage);
                break;
            case ServiceErrors::USER_AUTHENTICATION_ERROR:
                throw new ServiceUserAuthenticationException($errorMessage);
                break;
            case ServiceErrors::ACCESS_DENIED:
                throw new ServiceAccessDeniedException($errorMessage);
                break;
            case ServiceErrors::DB_ERROR:
                throw new ServiceDBException($errorMessage);
                break;
            case ServiceErrors::DB_UNIQUE_ERROR:
                throw new ServiceDBUniqueException($errorMessage);
                break;
            case ServiceErrors::OBJECT_NOT_FOUND:
                throw new ServiceObjectNotFoundException($errorMessage);
                break;
            case ServiceErrors::CACHE_NOT_INITIALIZED:
                throw new ServiceCacheNotInitializedException($errorMessage);
                break;
            default:
                $this->throwServiceUnknownException($errorMessage);
                break;
        }
    }

    protected function throwServiceUnknownException($errorMessage) {
        throw new ServiceUnknownErrorException($errorMessage);
    }

    /**
     * Устанавливает опции для curl
     *
     * @param $curlHandle
     * @param RestRequest $request
     * @throws InvalidArgumentException
     */
    protected function setCurlOptions($curlHandle, RestRequest $request) {

        if (!is_null($this->getPort())) {
            curl_setopt($curlHandle, CURLOPT_PORT, $this->getPort());
        }

        $requestMethod = $request->getMethod();
        switch (strtoupper($requestMethod)) {
            case 'GET':
                curl_setopt($curlHandle, CURLOPT_URL, $this->buildGetUrl($request->getServiceUrl(), $request->getRequestVars()));
                break;
            case 'POST':
                curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl());
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                $requestVars = $request->getRequestVars();
                if (!empty($requestVars['isMultipartData'])) {
                    $flatRequestVars = array();
                    $this->expandToStringArray('', $requestVars, $flatRequestVars);
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $flatRequestVars);
                } else {
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->buildPostBody($requestVars));
                }
                break;
            case 'PUT':
                curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl());
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
                $requestVars = $request->getRequestVars();
                if (!empty($requestVars['isMultipartData'])) {
                    $flatRequestVars = array();
                    $this->expandToStringArray('', $requestVars, $flatRequestVars);
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $flatRequestVars);
                } else {
                    curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->buildPostBody($requestVars));
                }
                break;
            case 'DELETE':
                curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl());
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                $requestVars = $request->getRequestVars();
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->buildPostBody($requestVars));
                break;
            default:
                throw new InvalidArgumentException('Current verb (' . $requestMethod . ') is an invalid REST verb.');
                break;
        }
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        $recursionCount = isset($_SERVER['HTTP_X_ABCP_RECURSION_COUNT']) ? $_SERVER['HTTP_X_ABCP_RECURSION_COUNT'] : 0;
        $timeOut = isset($_SERVER['HTTP_X_ABCP_TIMEOUT']) ? $_SERVER['HTTP_X_ABCP_TIMEOUT'] : 0;
        $headers = array('Accept: ' . $request->getHttpAccept());
        $headers[] = 'X-ABCP-Timeout: ' . $timeOut;
        $headers[] = 'X-ABCP-Recursion-Count: ' . ++$recursionCount;

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 60);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curlHandle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }

    private function expandToStringArray($inputKey, $inputArray, &$resultArray) {
        foreach ($inputArray as $key => $value) {
            $tmpKey = (bool )$inputKey ? $inputKey . "[" . $key . "]" : $key;
            if (is_array($value)) {
                $this->expandToStringArray($tmpKey, $value, $resultArray);
            } else {
                $resultArray[$tmpKey] = $value;
            }
        }
    }

    private function buildPostBody($data = NULL) {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
        }
        return http_build_query($data, '', '&');
    }

    private function buildGetUrl($url, $parameters = NULL) {
        $parametersString = '';
        if (!empty($parameters)) {
            $parametersString = http_build_query($parameters, '', '&');
        }

        return $url . '?' . $parametersString;
    }

    protected function responseDecode($responseBody) {
        return json_decode($responseBody);
    }
}
