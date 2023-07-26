<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class sal28Controller extends Controller
{
	public function index()
	{
		$sdate		= date('Y-m-d');
		$edate		= date("Y-m-d");
		$storages	= SLib::getStorage();

		$values = [
			"edate"		=> $edate,
			"sdate"		=> $sdate,
			"storages"	=> $storages
		];
		return view(Config::get('shop.store.view') . '/sale/sal28', $values);
	}
}
