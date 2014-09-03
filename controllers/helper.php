<?php

/**
 * Класс вспомогательных методов
 */
class Helper
{

    /**
     * Массив номеров-исключений, для которых очистка номера не производится
     */
    private static $numberExceptions = array('LA37/1', 'LA42/1');

    /**
     * Очищаем номер запчасти от спецсимволов, оставляя только буквы и цифры,
     * учитываем номера-исключения, для которых очистка не производится
     *
     * @param string $number Номер запчасти
     * @return string Номер запчасти, очищенный от спецсимволов
     */
    public static function cleanNumber($number)
    {
        $numberUp = mb_strtoupper($number, 'UTF-8');
        if (in_array($numberUp, self::$numberExceptions)) {
            $numberFix = $numberUp;
        } else {
            $numberFix = preg_replace('%[^\p{N}\p{L}]%u', '', $numberUp);
        }

        return $numberFix;
    }

    /**
     * Деплоим шаблон
     *
     * @param string $templateName
     * @param array $templateData
     * @return string
     */
    public static function deployTemplate($templateName, $templateData = NULL)
    {
        list($templateDir, $compiledPath, $cachedPath) = self::getSmartyDirectories();

        $smarty = new Smarty();
        $smarty->setTemplateDir($templateDir);
        $smarty->setCompileDir($compiledPath);
        $smarty->setCacheDir($cachedPath);
        $smarty->assign("tpl_data", $templateData);
        $smarty->assign("tpl_debug", '<pre>' . var_export($smarty->getTemplateVars(), TRUE) . '</pre>');

        return $smarty->fetch($templateName);
    }

    /**
     * Получаем директории для smarty
     *
     * @return array
     */
    private static function getSmartyDirectories()
    {
        $templateDir = __DIR__ . '/../views/';

        $tempPath = __DIR__ . '/../tmp/smarty/';
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, TRUE);
        }

        $compiledPath = $tempPath . '/compiled';
        if (!file_exists($compiledPath)) {
            mkdir($compiledPath, 0777, TRUE);
        }

        $cachedPath = $tempPath . '/cached';
        if (!file_exists($cachedPath)) {
            mkdir($cachedPath, 0777, TRUE);
        }

        return array($templateDir, $compiledPath, $cachedPath);
    }
}