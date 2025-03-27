<?php

namespace PHPFramework;

class Language
{
    /**
     * Данные для языка.
     * @var array
     */
    public static array $lang_data = [];
    /**
     * Массив общих переводных фраз.
     * @var array
     */
    protected static array $lang_layout = [];
    /**
     * Массив переводных фраз для вида.
     * @var array
     */
    protected static array $lang_view = [];

    public static function load($route)
    {
        // Получаем код языка.
        $code = app()->get('lang')['code'];
        $lang_layout = APP . "/Languages/$code.php";
        $lang_view = '';
        if (is_array($route)) {
            $controller_segments = explode('\\', $route[0]);
            $controller_name = array_pop($controller_segments);
            $lang_folder = strtolower(str_replace('Controller', '', $controller_name));
            $lang_file = $route[1];
            $lang_view = APP . "/Languages/$code/$lang_folder/$lang_file.php";
        }
        if (file_exists($lang_layout)) {
            self::$lang_layout = require_once $lang_layout;
        }
        if ($lang_view && file_exists($lang_view)) {
            self::$lang_view = require_once $lang_view;
        }
        self::$lang_data = array_merge(self::$lang_layout, self::$lang_view);
    }

    public static function get($key)
    {
        return self::$lang_data[$key] ?? $key;
    }
}