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
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/standard/std07', $values);
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
		$where = "";

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
				, s.use_yn
				, sc.store_channel as store_channel
				, sc2.store_kind as store_channel_kind
			from store s
				left outer join store_channel sc on sc.store_channel_cd = s.store_channel
				left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind
			where 1=1 $where
			group by store_cd
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

	public function search_store_fee(Request $request)
	{
		$store_cd = $request->input('store_cd');
		$use_yn = $request->input('use_yn','Y');

		$code = 200;

		$rows = $this->_get_store_fee($store_cd, $use_yn);

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

	public function _get_store_fee($store_cd, $use_yn) 
	{
		$where="";
		$store_fee_sql = "";
		if($use_yn != '') {
			if($use_yn == 'Y') {
				$where .= "and sf.use_yn = 'Y'";
				$store_fee_sql = "inner join store_fee sf
					on cd.code_id = sf.pr_code and sf.store_cd = s.store_cd and sf.idx in (select max(idx) from store_fee where store_cd = '$store_cd' group by pr_code)";
			} else if($use_yn == 'N') {
				$where .= "and sf.use_yn is null";
				$store_fee_sql = "left outer join store_fee sf
					on cd.code_id = sf.pr_code and sf.store_cd = s.store_cd and sf.idx in (select max(idx) from store_fee where store_cd = '$store_cd' group by pr_code)";
			} else {
				$store_fee_sql = "left outer join store_fee sf
					on cd.code_id = sf.pr_code and sf.store_cd = s.store_cd and sf.idx in (select max(idx) from store_fee where store_cd = '$store_cd' group by pr_code)";
			}
		}

		$sql = "
			select 
				sf.idx, 
				cd.code_id as pr_code_cd, 
				cd.code_val as pr_code_nm, 
				s.store_cd, 
				sf.store_fee,
				s.grade_cd,
				sg.idx as grade_idx,
				sg.name as grade_nm,
				sf.sdate, 
				sf.edate, 
				sf.comment, 
				sf.use_yn
			from code cd
				inner join store s on s.store_cd = '$store_cd'
				$store_fee_sql
				left outer join store_grade sg 
					on sg.grade_cd = s.grade_cd 
					and concat(sg.sdate, '-01 00:00:00') <= date_format(now(), '%Y-%m-%d 00:00:00') 
					and concat(sg.edate, '-31 23:59:59') >= date_format(now(), '%Y-%m-%d 00:00:00')			
			where cd.code_kind_cd = 'PR_CODE' and cd.use_yn = 'Y' $where
			order by cd.code_seq
		";

		$rows = DB::select($sql);

		return $rows;
	}

	public function show($store_cd, $pr_code_cd)
	{
		$sql = "select store_cd, store_nm from store where store_cd = :store_cd";
		$store = DB::selectOne($sql, ["store_cd" => $store_cd]);

		$sql = "select code_id as pr_code_cd, code_val as pr_code_nm from code where code_kind_cd = 'PR_CODE' and code_id = :pr_code_cd";
		$pr_code = DB::selectOne($sql, ["pr_code_cd" => $pr_code_cd]);

		$values = [
			"store" => $store,
			"pr_code" => $pr_code,
		];

		return view(Config::get('shop.store.view') . '/standard/std07_show', $values);
	}

	public function search_store_fee_history(Request $request)
	{
		$store_cd = $request->input("store_cd");
		$pr_code_cd = $request->input("pr_code_cd");

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
				if(sf.idx in (select max(idx) from store_fee group by pr_code), 'Y', 'N') as use_yn
			from code cd
				inner join store_fee sf 
					on cd.code_id = sf.pr_code and sf.store_cd = :store_cd
			where cd.code_kind_cd = 'PR_CODE' and cd.use_yn = 'Y' and cd.code_id = :pr_code_cd
			order by sf.idx desc
		";

		$rows = DB::select($sql, ["store_cd" => $store_cd, "pr_code_cd" => $pr_code_cd]);

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			],
			"body" => $rows
		]);
	}

	// 마진정보 추가 & 수정
	public function update_store_fee(Request $request)
	{
		$admin_id = Auth('head')->user()->id;
		$data = $request->all();

		$new_data = '';

		try {
			DB::beginTransaction();

			foreach($data as $i => $d) {
				if($d['use_yn'] == 'A') {
					$new_data = $d;

					// 등록
					DB::table('store_fee')->insert([
						'store_cd' => $d['store_cd'],
						'pr_code' => $d['pr_code_cd'],
						'store_fee' => $d['store_fee'] ?? 0,
						// 'manager_fee' => $d['manager_fee'] ?? 0,
						'manager_fee' => null,
						'sdate' => $d['sdate'] ?? '0000-00-00',
						'edate' => $d['edate'] ?? '9999-12-31',
						'comment' => $d['comment'] ?? null,
						'use_yn' => 'Y',
						'reg_date' => now(),
						'admin_id' => $admin_id,
					]);
				} else if($d['use_yn'] == 'Y' || $d['use_yn'] == 'N') {
					$edate = $d['edate'] ?? '9999-12-31';
					
					if($new_data != '') {
						if ($new_data['sdate'] == '0000-00-00') {
							$edate = '0000-00-00';
						} else {
							$edate = date('Y-m-d', strtotime($new_data['sdate'] . '-1 day'));
						}
					}
					// 수정
					DB::table('store_fee')
						->where('idx', '=', $d['idx'] ?? 0)
						->update([
							'store_fee' => $d['store_fee'] ?? 0,
							'manager_fee' => $d['manager_fee'] ?? 0,
							'sdate' => $d['sdate'] ?? null,
							'edate' => $edate,
							'comment' => $d['comment'] ?? null,
							'mod_date' => now(),
							'admin_id' => $admin_id,
						]);
				}
			}

			$code = 200;
			$msg = "정상적으로 저장되었습니다.";
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	// 마진정보 삭제
	public function remove_store_fee($fee_idx)
	{
		$admin_id = Auth('head')->user()->id;

		try {
			DB::beginTransaction();

			DB::table('store_fee')
				->where('idx', '=', $fee_idx)
				->delete();

			$code = 200;
			$msg = "정상적으로 삭제되었습니다.";
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function change_use_yn(Request $request) 
	{
		$charge_yn = $request->input('charge_yn','');
		$store_cd = $request->input('store_cd');
		$store_nm = $request->input('store_nm');

		$where="";
		if($charge_yn != '') {
			if($charge_yn == 'Y') {
				$where .= "and sf.use_yn != ''";
			} else if($charge_yn == 'N') {
				$where .= "and sf.use_yn = ''";
			}
		}


		$code = 200;

		$sql = "
			select 
				sf.idx, 
				cd.code_id as pr_code_cd, 
				cd.code_val as pr_code_nm, 
				s.store_cd, 
				sf.store_fee,
				s.grade_cd,
				sg.idx as grade_idx,
				sg.name as grade_nm,
				sf.sdate, 
				sf.edate, 
				sf.comment, 
				sf.use_yn
			from code cd
				inner join store s on s.store_cd = '$store_cd'
				inner join store_fee sf
					on cd.code_id = sf.pr_code and sf.store_cd = s.store_cd and sf.idx in (select max(idx) from store_fee where store_cd = '$store_cd' group by pr_code) $where
				left outer join store_grade sg 
					on sg.grade_cd = s.grade_cd 
					and concat(sg.sdate, '-01 00:00:00') <= date_format(now(), '%Y-%m-%d 00:00:00') 
					and concat(sg.edate, '-31 23:59:59') >= date_format(now(), '%Y-%m-%d 00:00:00')			
			where cd.code_kind_cd = 'PR_CODE' and cd.use_yn = 'Y'
			order by cd.code_seq

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
			"body" => $rows,
			"store_nm" => $store_nm
		]);

		

	}
}

