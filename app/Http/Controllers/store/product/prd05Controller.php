<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class prd05Controller extends Controller
{

	public function index() 
	{
		
		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		

		$values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
		];

		return view( Config::get('shop.store.view') . '/product/prd05',$values);
	}

    public function show() {

        $mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
		];

        return view( Config::get('shop.store.view') . '/product/prd05_show',$values);
    }

	// public function search(Request $request)
	// {
		

	// 	return response()->json([
	// 		"code"	=> 200,
	// 		"head"	=> array(
	// 			"total"		=> $total,
	// 			"page"		=> $page,
	// 			"page_cnt"	=> $page_cnt,
	// 			"page_total"=> count($result),
	// 		),
	// 		"body"	=> $result
	// 	]);
	// }

	
}