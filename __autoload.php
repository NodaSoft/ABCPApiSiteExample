<?php

/**
 * Автозагрузка классов для работы abcpApiSiteExample
 * @param string $class Имя класса
 */
function abcpApiSiteExampleAutoload($class = NULL)
{
    static $classFiles = NULL;
    static $sourcesPath = NULL;

    if ($classFiles === NULL) {
        $classFiles = array(
            'restjsonclient' => 'web.service/rest.json.client.php',
            'restjsonrequest' => 'web.service/rest.json.request.php',
            'restrequest' => 'web.service/rest.request.php',
            'restrequestsender' => 'web.service/rest.request.sender.php',
            'serviceerrors' => 'web.service/service.errors.php',
            'serviceexceptions' => 'web.service/service.exceptions.php',

            'publicapiclient' => 'web.service/public.api/public.api.client.php',
            'publicapiconfig' => 'web.service/public.api/public.api.config.php',
        );
        $sourcesPath = dirname(__FILE__);
    }

    $className = strtolower($class);

    if (isset($classFiles[$className])) {
        require_once $sourcesPath . '/' . $classFiles[$className];
    }
}

spl_autoload_register('abcpApiSiteExampleAutoload');