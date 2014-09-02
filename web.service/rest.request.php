<?php

/**
 * Класс REST-запроса
 * Содержит все параметры запроса, необходимые для его корректной обработки веб-службой
 */
class RestRequest {

	private $serviceUrl = '';
    private $httpAccept = 'application/xml';
    private $method = 'get';
    private $requestVars = array();
	private $uri = '';
	
	/**
	 * Конструктор REST-запроса
     *
	 * @param string $serviceUrl Адрес веб-службы, к которой делается запрос
	 * @param string $httpAccept Формат данных обмена: 'application/xml' или 'application/json'
	 * @param string $method Метод отправки запроса: 'post' или 'get'
	 * @param array $requestVars Параметры запроса в виде массива
	 * @param string $uri Uri запроса без параметров. Например, 'search/brands'
	 */
	public function __construct($serviceUrl, $httpAccept, $method, array $requestVars, $uri = '') {
		$this->serviceUrl = $serviceUrl;
		$this->httpAccept = $httpAccept;
		$this->method = $method;
		$this->requestVars = $requestVars;
		$this->uri = $uri;
	}

	public function setServiceUrl($serviceUrl) {
		$this->serviceUrl = $serviceUrl;
	}

	public function getServiceUrl() {
		return $this->serviceUrl;
	}

    public function setHttpAccept($httpAccept) {
        $this->httpAccept = $httpAccept;
    }

    public function getHttpAccept() {
        return $this->httpAccept;
    }

	public function setMethod($method) {
    	$this->method = $method;
	}

    public function getMethod() {
        return $this->method;
    }

	public function setRequestVars($requestVars) {
		$this->requestVars = $requestVars;
    }

    public function getRequestVars() {
        return $this->requestVars;
    }

	public function setUri($uri) {
		$this->uri = $uri;
    }

    public function getUri() {
        return $this->uri;
    }
}
