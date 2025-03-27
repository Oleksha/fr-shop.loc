<?php

namespace PHPFramework;

class Request
{
    /**
     * Строка запроса
     * @var string
     */
    public string $rawUri;
    public string $uri;
    public array $post;
    public array $get;
    public array $files;

    public function __construct($uri)
    {
        $this->rawUri = $uri;
        $this->uri = trim(urldecode($uri), '/');
        $this->post = $_POST;
        $this->get = $_GET;
        $this->files = $_FILES;
    }

    /**
     * Возвращает метод запроса в верхнем регистре
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Пришёл ли запрос методом GET
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() == 'GET';
    }

    /**
     * Пришёл ли запрос методом POST
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() == 'POST';
    }

    /**
     * Пришёл ли запрос методом AJAX
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Возвращает значение указанного параметра из массива GET,
     * если параметр существует или значение по умолчанию.
     * @param $name
     * @param $default
     * @return string|null
     */
    public function get($name, $default = null): ?string
    {
        return $this->get[$name] ?? $default;
    }

    /**
     * Возвращает значение указанного параметра из массива POST,
     * если параметр существует или значение по умолчанию.
     * @param $name
     * @param $default
     * @return string|null
     */
    public function post($name, $default = null): ?string
    {
        return $this->post[$name] ?? $default;
    }

    /**
     * Получает строку запроса без параметров
     * @return string
     */
    public function getPath(): string
    {
        return $this->removeQueryString();
    }

    /**
     * Убирает параметры из строки запроса
     * @return string
     */
    protected function removeQueryString(): string
    {
        if ($this->uri) {   // Если запрос не пуст.
            $params = explode('?', $this->uri);
            // Возвращаем строку запроса без параметров
            return trim($params[0], '/');
        }
        return "";
    }

    public function getData(): array
    {
        $data = [];
        $request_data = $this->isPost() ? $_POST : $_GET;
        foreach ($request_data as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
            }
            $data[$key] = $value;
        }
        return $data;
    }
}