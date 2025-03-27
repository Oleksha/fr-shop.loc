<?php

namespace PHPFramework\Middleware;

class Auth
{
    public function handle(): void
    {
        if (!check_auth()) {
            session()->setFlash('error', 'Необходима аутентификация!');
            response()->redirect(base_url('/login'));
        }
    }
}