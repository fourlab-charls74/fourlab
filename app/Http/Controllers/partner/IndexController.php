<?php

namespace App\Http\Controllers\partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class IndexController extends Controller
{
    //
    public function index() {
        return view( Config::get('shop.partner.view') . '/index');
    }
}
