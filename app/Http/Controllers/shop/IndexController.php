<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    //
    public function index() {
        return view(Config::get('shop.shop.view'). '/index');
    }
}

