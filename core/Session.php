<?php

namespace PHPFramework;

class Session
{
    public function __construct()
    {
        session_start();
    }

    /**
     * Помещает значение в сессию по ключу
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Получаем значение из сессии по ключу
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public function get($key, $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Проверяет наличие ключа в сессии
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Удаляет указанный ключ из сессии
     * @param $key
     * @return void
     */
    public function remove($key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Помещает в сессию разовое сообщение по ключу
     * @param $key
     * @param $value
     * @return void
     */
    public function setFlash($key, $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * Получает разовое сообщение из сессии по ключу
     * @param $key
     * @param $default
     * @return mixed
     */
    public function getFlash($key, $default = null): mixed
    {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
        }
        return $value ?? $default;
    }
}