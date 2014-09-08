<?php

/**
 * Автозагрузка классов для работы abcpApiSiteExample
 * @param string $class Имя класса
 */
function abcpApiSiteExampleAutoload($class = NULL)
{
    static $classFiles = NULL;

    if ($classFiles === NULL) {
        $classFiles = array(
            'search' => 'controllers/search.php',
            'helper' => 'controllers/helper.php',
            'ajaxrouter' => 'controllers/ajax/ajax.router.php',
            'ajax\base' => 'controllers/ajax/ajax.base.php',

            'smarty' => 'vendor/smarty/smarty/distribution/libs/Smarty.class.php',

            'publicapiclient' => 'web.service/public.api/public.api.client.php',
            'publicapiconfig' => 'web.service/public.api/public.api.config.php',

            'restjsonclient' => 'web.service/rest.json.client.php',
            'restjsonrequest' => 'web.service/rest.json.request.php',
            'restrequest' => 'web.service/rest.request.php',
            'restrequestsender' => 'web.service/rest.request.sender.php',
            'serviceerrors' => 'web.service/service.errors.php',
            'serviceexceptions' => 'web.service/service.exceptions.php',
        );
    }

    $className = strtolower($class);
    $classPath = $classFiles[$className];

    if (isset($classPath)) {
        require_once __DIR__ . '/' . $classPath;
    }
}

spl_autoload_register('abcpApiSiteExampleAutoload');