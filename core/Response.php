<?php

namespace PHPFramework;

class Response
{
    public function setResponseCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect($url = ''): void
    {
        if ($url) {
            $redirect = $url;
        } else {
            $redirect = $_SERVER['HTTP_REFERER'] ?? base_url('/');
        }
        header("Location: $redirect");
        die;
    }

    public function json($data, $code = 200)
    {
        $this->setResponseCode($code);
        header("Content-type: application/json; charset=UTF-8");
        exit(json_encode($data));
    }
}