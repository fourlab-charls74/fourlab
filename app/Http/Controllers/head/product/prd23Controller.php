<?php

namespace App\Http\Controllers\head\product;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class prd23Controller extends Controller
{
    public function index(Request $req)
    {
        $values = [];
		return view(Config::get('shop.head.view') . '/product/prd23', $values);
    }
}