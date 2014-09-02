<?php

/**
 * Класс отправки запроса веб-службе в формате JSON
 */
class RestJsonRequest extends RestRequest {

	public function __construct($serviceUrl, $method, array $requestVars) {
		parent::__construct($serviceUrl, 'application/json', $method, $requestVars);
	}
}
