<?php

/**
 * Класс для взаимодействия с системой ABCP через публичный API
 *
 * @link http://docs.abcp.ru/wiki/API
 */
class PublicApiClient extends RestJsonClient
{
    /**
     * Конструктор клиента
     *
     * @param array $serviceUrls Адрес веб-службы, к которой делается запрос
     */
    public function __construct(array $serviceUrls = array())
    {
        $this->serviceUrls = $serviceUrls;
        $this->requestSender = new RestRequestSender();
    }

    /**
     * Поиск брендов по номеру
     *
     * @param string $number Искомый номер детали
     * @param int $useOnlineStocks Флаг "использовать online-склады" [0,1]
     * @return array|null
     * @link http://docs.abcp.ru/wiki/API#.D0.9F.D0.BE.D0.B8.D1.81.D0.BA_.D0.B1.D1.80.D0.B5.D0.BD.D0.B4.D0.BE.D0.B2_.D0.BF.D0.BE_.D0.BD.D0.BE.D0.BC.D0.B5.D1.80.D1.83
     */
    public function searchBrands($number = NULL, $useOnlineStocks = NULL)
    {
        $requestVars = array(
            'number' => $number,
            'useOnlineStocks' => $useOnlineStocks,
        );
        $result = $this->getResultsByGet($requestVars, '/search/brands');

        return $this->getTwoDimensionalArrayResult($result);
    }

    /**
     * Поиск детали по номеру и бренду
     *
     * @param string $number Искомый номер детали
     * @param string $brand Фильтр по имени производителя
     * @param int $useOnlineStocks Флаг "использовать online-склады" [0,1]
     * @return array
     * @link http://docs.abcp.ru/wiki/API#.D0.9F.D0.BE.D0.B8.D1.81.D0.BA_.D0.B4.D0.B5.D1.82.D0.B0.D0.BB.D0.B8_.D0.BF.D0.BE_.D0.BD.D0.BE.D0.BC.D0.B5.D1.80.D1.83_.D0.B8_.D0.B1.D1.80.D0.B5.D0.BD.D0.B4.D1.83
     */
    public function searchArticles($number = NULL, $brand = NULL, $useOnlineStocks = NULL)
    {
        $requestVars = array(
            'number' => $number,
            'brand' => $brand,
            'useOnlineStocks' => $useOnlineStocks,
        );
        $result = $this->getResultsByGet($requestVars, '/search/articles');

        return $this->getAssociativeArrayInArrayResult($result);
    }

    /**
     * Подсказки по поиску
     *
     * @param string $number Номер или часть номера детали
     * @return array
     * @link http://docs.abcp.ru/wiki/API#.D0.9F.D0.BE.D0.B4.D1.81.D0.BA.D0.B0.D0.B7.D0.BA.D0.B8_.D0.BF.D0.BE_.D0.BF.D0.BE.D0.B8.D1.81.D0.BA.D1.83
     */
    public function searchTips($number = NULL)
    {
        $requestVars = array(
            'number' => $number
        );
        $result = $this->getResultsByGet($requestVars, '/search/tips');

        return $this->getAssociativeArrayInArrayResult($result);
    }
}