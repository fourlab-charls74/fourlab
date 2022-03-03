<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

// 테스트관리
class std52Controller extends Controller
{
    public function index()
    {
        $values = [];
        return view(Config::get("shop.head.view") . "/standard/std52", $values);
    }
}