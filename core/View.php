<?php

namespace PHPFramework;

class View
{
    /**
     * Используемый шаблон
     * @var string
     */
    public string $layout;
    /**
     * Изменяемое содержимое страницы
     * @var string
     */
    public string $content = '';

    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $data = [], $layout = ''): string
    {
        extract($data);
        $view_file = VIEW . "/$view.php";
        if (is_file($view_file)) {
            ob_start();
            require $view_file;
            $this->content = ob_get_clean();
        } else {
            abort("Не найден вид $view_file", 500);
        }
        if (false === $layout) {
            return $this->content;
        }
        $layout_filename = $layout ?: $this->layout;
        $layout_file = VIEW . "/layouts/$layout_filename.php";
        if (is_file($layout_file)) {
            ob_start();
            require_once $layout_file;
            return ob_get_clean();
        } else {
            abort("Не найден шаблон $layout_file", 500);
        }
        return '';
    }

    public function renderPartial($view, $data = []): string
    {
        extract($data);
        $view_file = VIEW . "/$view.php";
        if (is_file($view_file)) {
            ob_start();
            require $view_file;
            return ob_get_clean();
        } else {
            return "Не найден файл $view_file";
        }
    }
}