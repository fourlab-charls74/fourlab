<?php

namespace App\Http\Controllers\store\pos;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class PosController extends Controller
{
    public function index() {
        return view(Config::get('shop.store.view') . '/pos/pos');
    }
}

