<?php

namespace Ajax;

/**
 * Получение подсказки поиска по номеру запчасти более трех символов
 * Например, /ajax.php?action=getSearchTips&params[number]=01089
 * Class getSearchTips
 * @package Ajax
 */
class getSearchTips extends Base {

    /**
     * @var \PublicApiClient
     */
    protected $client;

    public function __construct($requestVars) {
        parent::__construct($requestVars);
        $this->client = new \PublicApiClient(array(\PublicApiConfig::SERVICE_URL));
    }

    public function run() {
        return $this->client->searchTips($this->requestVars['number']);
    }

    public function checkRequestVars() {
        if (!isset($this->requestVars['number'])) {
            $this->errors[] = 'Отсутствует обязательный параметр number';
        }
        if (mb_strlen($this->requestVars['number'], 'UTF-8') < 3) {
            $this->errors[] = 'Параметр number должен быть не менее трех символов';
        }
        return (empty($this->errors));
    }
}