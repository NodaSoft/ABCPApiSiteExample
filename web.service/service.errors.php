<?php

/**
 * Класс кодов ошибок
 */
class ServiceErrors
{
    const REQUEST_SYNTAX_ERROR = 1;
    const REQUEST_PARAMETER_NOT_FOUND_ERROR = 2;
    const UNKNOWN_OPERATION = 3;
    const REQUEST_PARAMETER_ERROR = 4;
    const UNKNOWN_ERROR = 13;

    const USER_AUTHENTICATION_ERROR = 102;
    const ACCESS_DENIED = 103;
    const SITE_AUTHENTICATION_ERROR = 104;

    const DB_ERROR = 201;
    const DB_UNIQUE_ERROR = 202;

    const OBJECT_NOT_FOUND = 301;
    const CACHE_NOT_INITIALIZED = 302;
    const LOCKED = 303;
}

/**
 * Класс сообщений ошибок
 */
class ServiceErrorsMessages
{
    const REQUEST_SYNTAX_ERROR = 'Некорректный запрос';
    const REQUEST_PARAMETER_NOT_FOUND_ERROR = 'Недостаточно параметров';
    const UNKNOWN_OPERATION = 'Неизвестная операция';
    const REQUEST_PARAMETER_ERROR = 'Некорректные параметры';
    const UNKNOWN_ERROR = 'Неизвестная ошибка';

    const USER_AUTHENTICATION_ERROR = 'Ошибка авторизации пользователя';
    const ACCESS_DENIED = 'Недостаточно прав для выполнения данной операции';
    const SITE_AUTHENTICATION_ERROR = 'Ошибка авторизации сайта';

    const DB_ERROR = 'Ошибка базы данных';
    const DB_UNIQUE_ERROR = 'Нарушение уникальности полей';

    const OBJECT_NOT_FOUND = 'Объект не найден';
    const CACHE_NOT_INITIALIZED = 'Ошибка инициализации кэша';
    const LOCKED = 'Ресурс заблокирован';
}
