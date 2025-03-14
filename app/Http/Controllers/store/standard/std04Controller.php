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

class std04Controller extends Controller
{
	public function index()
	{
		$values = [
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view(Config::get('shop.store.view') . '/standard/std04', $values);
	}

	public function search(Request $request)
	{
		$store_type = $request->input("store_type");
		$store_cd = $request->input("store_cd");
		$store_nm = $request->input("store_nm");
		$use_yn = $request->input("use_yn");
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		$code = 200;
		$where = " and s.competitor_yn = 'Y' "; // 동종업계정보입력을 사용하는 항목만 조회

		if($store_type != null) 
			$where .= " and s.store_type = '$store_type'";
		if($store_cd != null) 
			$where .= " and s.store_cd like '%$store_cd%'";
		if($store_nm != null) 
			$where .= " and (s.store_nm like '%$store_nm%' or s.store_nm_s like '%$store_nm%')";
		if($use_yn != null) 
			$where .= " and s.use_yn = '$use_yn'";

		if ($store_channel != "") $where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		$sql = "
			select 
				s.store_cd
				, s.store_nm as store_nm
				, sc.store_channel as store_channel
				, sc2.store_kind as store_channel_kind
				, (select count(*) from competitor where store_cd = s.store_cd and use_yn = 'Y') as competitor_cnt
			from store s
				left outer join store_channel sc on sc.store_channel_cd = s.store_channel and dep = 1 and sc.use_yn = 'Y'
				left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind and sc2.dep = 2 and sc2.use_yn = 'Y'
			where 1=1
				$where
			order by s.store_cd
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

	public function search_competitor($store_cd)
	{
		$code = 200;

		$rows = $this->_get_competitor($store_cd);

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

	public function _get_competitor($store_cd) 
	{
		$sql = "
			select 
				cd.code_id as competitor_cd, cd.code_val as competitor_nm, com.store_cd,
				com.concept, com.item, com.manager, com.sdate, com.edate, com.use_yn
			from code cd
				left outer join competitor com 
					on cd.code_id = com.competitor_cd and com.store_cd = :store_cd
			where cd.code_kind_cd = 'COMPETITOR' and cd.use_yn = 'Y'
			order by cd.code_id
		";

		$rows = DB::select($sql, ["store_cd" => $store_cd]);
		return $rows;
	}

	public function update_competitor(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$store_cd = $request->input("store_cd");
		$data = $request->input("data");

		try {
			DB::beginTransaction();
			foreach($data as $i => $d) {
				$ori = DB::table('competitor')
					->where("store_cd", "=", $store_cd)
					->where("competitor_cd", "=", $d['competitor_cd'])
					->get();
				$is_exist = count($ori) > 0;

				if(!$is_exist) {
					// 등록
					DB::table('competitor')->insert([
						'store_cd' => $store_cd,
						'competitor_cd' => $d['competitor_cd'],
						'item' => $d['item'] ?? null,
						'manager' => $d['manager'] ?? null,
						'sdate' => $d['sdate'] ?? null,
						'edate' => $d['edate'] ?? null,
						'use_yn' => $d['use_yn'] ?? 'N',
						'reg_date' => now(),
						'admin_id' => $admin_id,
					]);
				} else {
					// 수정
					DB::table('competitor')
						->where("store_cd", "=", $store_cd)
						->where("competitor_cd", "=", $d['competitor_cd'])
						->update([
							'item' => $d['item'] ?? null,
							'manager' => $d['manager'] ?? null,
							'sdate' => $d['sdate'] ?? null,
							'edate' => $d['edate'] ?? null,
							'use_yn' => $d['use_yn'] ?? 'N',
							'mod_date' => now(),
							'admin_id' => $admin_id,
						]);
				}
			}
			$msg = "정상적으로 저장되었습니다.";
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
}

