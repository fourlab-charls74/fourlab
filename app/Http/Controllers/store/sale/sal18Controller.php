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

class sal18Controller extends Controller
{
	public function index()
	{
		$values = [
			'sdate' => Carbon::now()->startOfMonth()->format('Y-m'),
			'sale_kinds' => SLib::getCodes('SALE_KIND'), // 판매구분
			'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
		];
        return view(Config::get('shop.store.view') . '/sale/sal18', $values);
	}

	// 판매유형 조회
	public function search(Request $request)
	{	
		$sale_month = $request->input('sdate', '');
		$sale_kind = $request->input('sale_kind', '');
		$sale_type_nm = $request->input('sale_type_nm', '');
		$where = "";

		if($sale_month != '') $where .= "";
		if($sale_kind != '') $where .= " and s.sale_kind = '$sale_kind'";
		if($sale_type_nm != '') $where .= " and s.sale_type_nm like '%$sale_type_nm%'";
		
		$sql = "
			select
				s.idx as sale_type_cd,
				s.sale_kind,
				s.sale_type_nm
			from sale_type s
			where s.use_yn = 'Y' $where
		";
		$result = DB::select($sql);
		
		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($result)
			],
			'body' => $result
		]);
	}
	
	// 매장목록 조회
	public function search_store(Request $request)
	{
		$sale_month = $request->input('sdate', '');
		$store_type = $request->input('store_type', '');
		$where = "";
		
		if($sale_month != '') $where .= "";
		if($store_type != '') $where .= " and s.store_type = '$store_type'";

		$sql = "
			select
				s.store_cd,
				s.store_nm,
				s.store_type,
				c.code_val as store_type_nm
			from store s
				left outer join code c on c.code_kind_cd = 'STORE_TYPE' and s.store_type = c.code_id
			where 1=1 $where
		";
		$result = DB::select($sql);

		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($result)
			],
			'body' => $result
		]);
	}
}
