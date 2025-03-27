<?php

namespace App\Controllers;

use PHPFramework\View;

class HomeController extends BaseController
{
    public function index(): View|string
    {
        return view('home/index', [
            'title' => 'Главная',
            ]);
    }
}