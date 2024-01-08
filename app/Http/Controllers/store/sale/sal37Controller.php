<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sal37Controller extends Controller
{
	public function index(Request $request)
	{
		$sdate = $request->input('sdate', now()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));
		$brand = $request->input('brand', 'F');
		
		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
			'brand' => $brand,
		];
		return view(Config::get('shop.store.view') . '/sale/sal37', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));
		$brand = $request->input('brand', 'F');

		$months = [];
		$sd = Carbon::parse($sdate);
		while($sd <= Carbon::parse($edate)){
			$months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y-m") ];
			$sd->addMonth();
		}
		
		$where = "";
		if ($brand == 'F') $where .= " and brand = 'F'";
		if ($brand == 'W') $where .= " and brand = 'W'";
		if ($brand == 'P') $where .= " and brand = 'P'";
		if ($brand == 'TR') $where .= " and brand = 'TR'";
		
		$page = $request->input('page', 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $request->input('limit', 500);
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";
		
		$sql = "
			select
				concat(brand, year, season) as season_brand
			from product_code
			where 1=1 $where and year >= 18
			group by concat(brand, year, season)
			order by brand, year, season asc
		";
		
		$rows = DB::select($sql);

		

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => 0,
				"page" => $page,
				"months" => $months,
			),
			"body" => $rows
		]);
	}
	

}
