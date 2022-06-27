<?php

namespace App\Http\Controllers\store\standard;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class std05Controller extends Controller
{
	public function index()
	{
		$values = [
			"sale_kinds" => SLib::getCodes("SALE_KIND"),
		];
		return view(Config::get('shop.store.view') . '/standard/std05', $values);
	}

	public function search(Request $request)
	{
		$sale_kind = $request->input("sale_kind");
		$sale_type_nm = $request->input("sale_type_nm");
		$sale_apply = $request->input("sale_apply");
		$use_yn = $request->input("use_yn");

		$code = 200;
		$where = "";

		if($sale_kind != null) 
			$where .= " and sale_kind = '$sale_kind'";
		if($sale_type_nm != null) 
			$where .= " and sale_type_nm like '%$sale_type_nm%'";
		if($sale_apply != null) 
			$where .= " and sale_apply = '$sale_apply'";
		if($use_yn != null) 
			$where .= " and use_yn = '$use_yn'";

		$sql = "
			select idx as sale_type_cd, sale_kind, sale_type_nm, sale_apply, amt_kind, sale_amt, sale_per, use_yn
			from sale_type
			where 1=1 $where
			order by sale_kind
		";

		$rows = DB::select($sql);

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			],
			"body" => $rows
		]);
	}

	public function show($sale_type_cd = '') 
	{
		$sale_type = "";

		if($sale_type_cd != '') {
			$sql = "
				select *
				from sale_type
				where idx = :sale_type_cd
			";

			$sale_type = DB::selectOne($sql, ["sale_type_cd" => $sale_type_cd]);
		}

		$values = [
			"cmd" => $sale_type_cd == '' ? "add" : "update",
			"sale_type" => $sale_type,
			"sale_kinds" => SLib::getCodes("SALE_KIND"),
		];

		return view(Config::get('shop.store.view') . '/standard/std05_show', $values);
	}

	// 판매유형별 매장정보 조회
	public function search_store($sale_type_cd = '')
	{
		$code = 200;
		
		$sql = "
			select store.store_cd, store.store_nm, s.use_yn, s.sdate, s.edate
			from store
				left outer join sale_type_store s
					on store.store_cd = s.store_cd and s.idx = :sale_type_cd
		";

		$rows = DB::select($sql, ["sale_type_cd" => $sale_type_cd]);

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			],
			"body" => $rows
		]);
	}
}

