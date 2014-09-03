<?php

/**
 * Класс для поиска по номеру детали
 *
 */
class Search
{
    /**
     * @var array
     */
    private static $templateData = array();

    /**
     * @var PublicApiClient
     */
    private static $apiClient;

    /**
     * Отрисовываем страницу поиска в зависимости от переданных параметров
     */
    public static function deploy()
    {
        self::$apiClient = new PublicApiClient(array(PublicApiConfig::SERVICE_URL));

        $number = $_REQUEST['number'];
        $brand = $_REQUEST['brand'];

        self::$templateData['number'] = htmlspecialchars($number, ENT_QUOTES);

        $numberFix = Helper::cleanNumber($number);
        if ($numberFix && $brand) {
            self::deploySecondStep($numberFix, $brand);
        } else if ($numberFix) {
            self::deployFirstStep($numberFix);
        } else {
            self::deployOnlyForm();
        }
    }

    /**
     * Отрисовываем только форму поиска
     */
    private static function deployOnlyForm()
    {
        echo Helper::deployTemplate('search/form.tpl', self::$templateData);
    }

    /**
     * Отрисовываем первый этап поиска
     *
     * Если для введенного номера найден только один бренд, выполняем переадресацию на страницу с указанием этого бренда и введенного номера
     * Иначе отображаем страницу со списком брендов и ссылками на каждом элементе списка, ведущими на второй этап поиска (с указанием бренда)
     *
     * @param $numberFix
     * @internal param string $number
     */
    private static function deployFirstStep($numberFix)
    {
        $searchBrands = self::$apiClient->searchBrands($numberFix);

        $isOnlyOneBrand = TRUE;
        $firstBrand = current($searchBrands);
        foreach ($searchBrands as $item) {
            if (strtolower($firstBrand['brand']) != strtolower($item['brand'])) {
                $isOnlyOneBrand = FALSE;
                break;
            }
        }

        if ($searchBrands && $isOnlyOneBrand) {
            self::deploySecondStep($numberFix, $firstBrand['brand']);
        } else {
            self::$templateData['searchBrands'] = $searchBrands;
            echo Helper::deployTemplate('search/first.step.tpl', self::$templateData);
        }
    }

    /**
     * Отрисовываем второй этоп поиска
     *
     * @param $numberFix
     * @param $brand
     */
    private static function deploySecondStep($numberFix, $brand)
    {
        self::$templateData['searchArticles'] = self::$apiClient->searchArticles($numberFix, $brand);
        echo Helper::deployTemplate('search/second.step.tpl', self::$templateData);
    }
}