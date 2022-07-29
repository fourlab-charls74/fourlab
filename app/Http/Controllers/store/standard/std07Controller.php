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

class std07Controller extends Controller
{
	public function index()
	{
		$values = [
			"store_types" => SLib::getCodes("STORE_TYPE"),
		];
		return view(Config::get('shop.store.view') . '/standard/std07', $values);
	}

	public function search(Request $request)
	{
		$store_type = $request->input("store_type");
		$store_cd = $request->input("store_cd");
		$store_nm = $request->input("store_nm");
		$use_yn = $request->input("use_yn");

		$code = 200;
		$where = "";

		if($store_type != null) 
			$where .= " and s.store_type = '$store_type'";
		if($store_cd != null) 
			$where .= " and s.store_cd = '$store_cd'";
		if($store_nm != null) 
			$where .= " and (s.store_nm like '%$store_nm%' or s.store_nm_s like '%$store_nm%')";
		if($use_yn != null) 
			$where .= " and s.use_yn = '$use_yn'";

		$sql = "
			select s.store_cd, s.store_nm as store_nm, s.use_yn, c.code_val as store_type
			from store s
				inner join code c on c.code_kind_cd = 'STORE_TYPE' and c.code_id = s.store_type
			where 1=1 $where
			order by store_cd
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

	public function search_store_fee($store_cd)
	{
		$code = 200;

		$rows = $this->_get_store_fee($store_cd);

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

	public function _get_store_fee($store_cd) 
	{
		$sql = "
			select 
				sf.idx, 
				cd.code_id as pr_code_cd, 
				cd.code_val as pr_code_nm, 
				sf.store_cd, 
				sf.store_fee, 
				sf.manager_fee, 
				sf.sdate, 
				sf.edate, 
				sf.comment, 
				sf.use_yn
			from code cd
				left outer join store_fee sf 
					on cd.code_id = sf.pr_code and sf.store_cd = :store_cd
			where cd.code_kind_cd = 'PR_CODE' and cd.use_yn = 'Y'
			order by cd.code_seq
		";

		$rows = DB::select($sql, ["store_cd" => $store_cd]);
		return $rows;
	}

	public function update_store_fee(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$store_cd = $request->input("store_cd");
		$data = $request->input("data");
		dd($store_cd, $data);
		try {
			// DB::beginTransaction();
			// foreach($data as $i => $d) {
			// 	$ori = DB::table('store_fee')
			// 		->where("store_cd", "=", $store_cd)
			// 		->where("competitor_cd", "=", $d['competitor_cd'])
			// 		->get();
			// 	$is_exist = count($ori) > 0;

			// 	if(!$is_exist) {
			// 		// 등록
			// 		DB::table('competitor')->insert([
			// 			'store_cd' => $store_cd,
			// 			'competitor_cd' => $d['competitor_cd'],
			// 			'item' => $d['item'] ?? null,
			// 			'manager' => $d['manager'] ?? null,
			// 			'sdate' => $d['sdate'] ?? null,
			// 			'edate' => $d['edate'] ?? null,
			// 			'use_yn' => $d['use_yn'] ?? 'N',
			// 			'reg_date' => now(),
			// 			'admin_id' => $admin_id,
			// 		]);
			// 	} else {
			// 		// 수정
			// 		DB::table('competitor')
			// 			->where("store_cd", "=", $store_cd)
			// 			->where("competitor_cd", "=", $d['competitor_cd'])
			// 			->update([
			// 				'item' => $d['item'] ?? null,
			// 				'manager' => $d['manager'] ?? null,
			// 				'sdate' => $d['sdate'] ?? null,
			// 				'edate' => $d['edate'] ?? null,
			// 				'use_yn' => $d['use_yn'] ?? 'N',
			// 				'mod_date' => now(),
			// 				'admin_id' => $admin_id,
			// 			]);
			// 	}
			// }
			// $msg = "정상적으로 저장되었습니다.";
			// DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
}

