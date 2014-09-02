<?php

/**
 * Классы исключеинй
 */

class ServiceException extends Exception {
	protected $code;
	protected $httpCode;

	public function  __construct($message, $defaultMessage, $code, $httpCode = NULL) {
		$msg = isset($message) ? $message : $defaultMessage;
		$this->code = $code;
		if($httpCode){
			$this->setHttpCode($httpCode);
		}
		parent::__construct($msg, $this->code);
	}

	public function getHttpCode(){
		return $this->httpCode;
	}

	public function setHttpCode($httpCode){
		$this->httpCode = $httpCode;
	}
}

class ServiceRequestSyntaxException extends ServiceException {
	protected $httpCode = 400;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::REQUEST_SYNTAX_ERROR, ServiceErrors::REQUEST_SYNTAX_ERROR);
	}
}

class ServiceRequestParameterNotFoundException extends ServiceException {
	protected $httpCode = 400;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::REQUEST_PARAMETER_NOT_FOUND_ERROR, ServiceErrors::REQUEST_PARAMETER_NOT_FOUND_ERROR);
	}
}

class ServiceRequestParameterErrorException extends ServiceException {
	protected $httpCode = 400;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::REQUEST_PARAMETER_ERROR, ServiceErrors::REQUEST_PARAMETER_ERROR);
	}
}

class ServiceUnknownOperationException extends ServiceException {
	protected $httpCode = 400;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::UNKNOWN_OPERATION, ServiceErrors::UNKNOWN_OPERATION);
	}
}

class ServiceUnknownErrorException extends ServiceException {
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::UNKNOWN_ERROR, ServiceErrors::UNKNOWN_ERROR);
	}
}

class ServiceUserAuthenticationException extends ServiceException {
	protected $httpCode = 403;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::USER_AUTHENTICATION_ERROR, ServiceErrors::USER_AUTHENTICATION_ERROR);
	}
}

class ServiceSiteAuthenticationException extends ServiceException {
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::SITE_AUTHENTICATION_ERROR, ServiceErrors::SITE_AUTHENTICATION_ERROR);
	}
}

class ServiceAccessDeniedException extends ServiceException {
	protected $httpCode = 403;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::ACCESS_DENIED, ServiceErrors::ACCESS_DENIED);
	}
}

class ServiceDBException extends ServiceException {
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::DB_ERROR, ServiceErrors::DB_ERROR);
	}
}
class ServiceDBUniqueException extends ServiceException {
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::DB_UNIQUE_ERROR, ServiceErrors::DB_UNIQUE_ERROR);
	}
}

class ServiceObjectNotFoundException extends ServiceException {
	protected $httpCode = 404;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::OBJECT_NOT_FOUND, ServiceErrors::OBJECT_NOT_FOUND);
	}
}

class ServiceCacheNotInitializedException extends ServiceException {
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::CACHE_NOT_INITIALIZED, ServiceErrors::CACHE_NOT_INITIALIZED);
	}
}

class ServiceLockedException extends ServiceException {
	protected $httpCode = 423;
	public function  __construct($message = NULL) {
		parent::__construct($message, ServiceErrorsMessages::LOCKED, ServiceErrors::LOCKED);
	}
}

