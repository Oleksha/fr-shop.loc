<?php

namespace App\Controllers;

use PHPFramework\View;

class HomeController extends BaseController
{
    public function index(): View|string
    {
//        session()->setFlash('success', 'Все прошло хорошо!');
//        session()->setFlash('error', 'Все прошло очень плохо!');
        return view('home/index', [
            'title' => 'Главная',
            ]);
    }
}