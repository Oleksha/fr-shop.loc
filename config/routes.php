<?php

use App\Controllers\HomeController;

const MIDDLEWARE = [
    'auth' => \PHPFramework\Middleware\Auth::class,
    'guest' => \PHPFramework\Middleware\Guest::class,
];

router()->get('/', [HomeController::class, 'index']);
