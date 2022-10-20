<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class prd04Controller extends Controller
{

	public function index() 
	{

		$values = [
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
		];

		return view( Config::get('shop.store.view') . '/product/prd04',$values);
	}

}