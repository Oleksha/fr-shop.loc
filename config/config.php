<?php

define("ROOT", dirname(__DIR__)); // Корневая папка фреймворка
const DEBUG = 1; // 1 - включена отладка, 0 - ошибки не показываются
const WWW = ROOT . '/public';
const CONFIG = ROOT . '/config';
const HELPERS = ROOT . '/helpers';
const APP = ROOT . '/app';
const CORE = ROOT . '/core';
const VIEW = APP . '/Views';
const ERROR_LOGS = ROOT . '/tmp/error.log';
const CACHE = ROOT . '/tmp/cache';
const LAYOUT = 'default';
const UPLOADS = WWW . '/uploads';
const PATH = 'https://fr-shop.loc';
const DB_SETTINGS = [
    'driver' => 'mysql',
    'host' => 'MariaDB-11.2',
    'database' => 'fr_shop_loc',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'port' => 3306,
    'prefix' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];
const MAIL_SETTINGS = [
    'host' => 'sandbox.smtp.mailtrap.io',
    'auth' => true,
    'username' => 'ce7c9ca775b921',
    'password' => '40596ac3cb16f9',
    'secure' => 'tls', //ssl
    'port' => '587',
    'from_email' => 'b27e258a53-83033a@inbox.mailtrap.io',
    'from_name' => 'My Framework',
    'is_html' => true,
    'charset' => 'UTF-8',
    'debug' => 0,
];
const PAGINATION_SETTINGS = [
    'perPage' => 3, // Сколько записей на страницу
    'midSize' => 2, // Сколько страниц показывать дополнительно
    'maxPages' => 7, // После скольки страниц начать убирать
    'tpl' => 'pagination/base', // Базовый шаблон вывода
];
const MULTILANGS = 0;
const LANGS = [
    'ru' => [
        'id' => 1,
        'code' => 'ru',
        'title' => 'Русский',
        'base' => 1,
    ],
    'en' => [
        'id' => 2,
        'code' => 'en',
        'title' => 'English',
        'base' => 0,
    ],
    'fr' => [
        'id' => 3,
        'code' => 'fr',
        'title' => 'France',
        'base' => 0,
    ],
];
