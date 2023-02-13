<?php

namespace App\Http\Controllers\shop\standard;

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
		// $values = [
		// 	"sale_kinds" => SLib::getCodes("SALE_KIND"),
		// ];
		// return view(Config::get('shop.shop.view') . '/standard/std05', $values);

		/* shop 미사용 메뉴 메인페이지로 리다이렉트 */
        return redirect('/shop');
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
			$where .= " and s.sale_kind = '$sale_kind'";
		if($sale_type_nm != null) 
			$where .= " and s.sale_type_nm like '%$sale_type_nm%'";
		if($sale_apply != null) 
			$where .= " and s.sale_apply = '$sale_apply'";
		if($use_yn != null) 
			$where .= " and s.use_yn = '$use_yn'";

		$sql = "
			select s.sale_kind, c.code_val as sale_kind_nm, s.idx as sale_type_cd, s.sale_type_nm, s.sale_apply, s.amt_kind, s.sale_amt, s.sale_per, s.use_yn, (select count(ss.idx) from sale_type_store ss where ss.sale_type_cd = s.idx and ss.use_yn = 'Y') as store_cnt
			from sale_type s
				inner join code c on c.code_kind_cd = 'SALE_KIND' and c.code_id = s.sale_kind
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
				select s.idx, s.sale_kind, c.code_val as sale_kind_nm, s.sale_type_nm, s.sale_apply, s.amt_kind, s.sale_amt, s.sale_per, s.use_yn
				from sale_type s
					inner join code c
						on c.code_kind_cd = 'SALE_KIND' and c.code_id = s.sale_kind
				where s.idx = :sale_type_cd
			";

			$sale_type = DB::selectOne($sql, ["sale_type_cd" => $sale_type_cd]);
		}

		$sql = "
			select c.code_id, c.code_val, c.code_id in(select sale_kind from sale_type) as use_yn
			from code c
				left outer join sale_type s
					on s.sale_kind = c.code_id
			where
				c.code_kind_cd = 'SALE_KIND' 
				and c.use_yn = 'Y' 
			order by c.code_id
		";
		$sale_kinds = DB::select($sql);
			
		$values = [
			"cmd" => $sale_type_cd == '' ? "add" : "update",
			"sale_type" => $sale_type,
			"sale_kinds" => $sale_kinds,
			"store_types" => SLib::getCodes("STORE_TYPE"),
		];

		return view(Config::get('shop.shop.view') . '/standard/std05_show', $values);
	}

	// 판매유형별 매장정보 조회
	public function search_store(Request $request, $sale_type_cd = '')
	{
		$store_type_cd = $request->input("store_type_cd");
		$code = 200;

		$where = "";
		if($store_type_cd != '') $where .= " and store.store_type = '$store_type_cd'";
		
		$sql = "
			select store.store_cd, store.store_nm, s.use_yn, s.sdate, s.edate
			from store
				left outer join sale_type_store s
					on store.store_cd = s.store_cd and s.sale_type_cd = :sale_type_cd
			where 1=1 $where
			order by store.store_cd
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

	// 판매유형정보 등록
	public function add_sale_type(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$r = $request->all();

		try {
			DB::beginTransaction();

			$idx = DB::table('sale_type')->insertGetId([
				'sale_kind' => $r['sale_kind'],
				'sale_type_nm' => $r['sale_type_nm'],
				'sale_apply' => $r['sale_apply'],
				'amt_kind' => $r['amt_kind'],
				'sale_amt' => $r['sale_amt'] ?? null,
				'sale_per' => $r['sale_per'] ?? null,
				'use_yn' => $r['use_yn'],
				'reg_date' => now(),
				'admin_id' => $admin_id,
			]);

			foreach($r['store_datas'] as $s) {
				DB::table('sale_type_store')->insert([
					'sale_type_cd' => $idx,
					'store_cd' => $s['store_cd'],
					'store_nm' => $s['store_nm'],
					'sdate' => $s['sdate'] ?? ($s['use_yn'] == 'Y' ? date("Y-m-d") : null),
					'edate' => $s['edate'] ?? ($s['use_yn'] == 'Y' ? '9999-12-31' : null),
					'use_yn' => $s['use_yn'] ?? "N",
					'reg_date' => now(),
				]);
			}

			$msg = "정상적으로 저장되었습니다.";
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, "data" => ["sale_type_cd" => $idx]]);
	}

	// 판매유형정보 수정
	public function update_sale_type(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$r = $request->all();
		$idx = $r['sale_kind_cd'];

		try {
			DB::beginTransaction();

			DB::table('sale_type')
				->where("idx", "=", $idx)
				->update([
					'sale_type_nm' => $r['sale_type_nm'],
					'sale_apply' => $r['sale_apply'],
					'amt_kind' => $r['amt_kind'],
					'sale_amt' => $r['sale_amt'] ?? null,
					'sale_per' => $r['sale_per'] ?? null,
					'use_yn' => $r['use_yn'],
					'mod_date' => now(),
					'admin_id' => $admin_id,
				]);
				
			foreach($r['store_datas'] as $s) {
				$cnt = DB::table('sale_type_store')
					->where("sale_type_cd", "=", $idx)
					->where("store_cd", "=", $s['store_cd'])
					->count();
				if($cnt < 1) {
					DB::table('sale_type_store')->insert([
						'sale_type_cd' => $idx,
						'store_cd' => $s['store_cd'],
						'store_nm' => $s['store_nm'],
						'sdate' => $s['sdate'] ?? ($s['use_yn'] == 'Y' ? date("Y-m-d") : null),
						'edate' => $s['edate'] ?? ($s['use_yn'] == 'Y' ? '9999-12-31' : null),
						'use_yn' => $s['use_yn'] ?? "N",
						'reg_date' => now(),
					]);
				} else {
					DB::table('sale_type_store')
						->where("sale_type_cd", "=", $idx)
						->where("store_cd", "=", $s['store_cd'])
						->update([
							'sdate' => $s['sdate'] ?? ($s['use_yn'] == 'Y' ? date("Y-m-d") : null),
							'edate' => $s['edate'] ?? ($s['use_yn'] == 'Y' ? '9999-12-31' : null),
							'use_yn' => $s['use_yn'] ?? "N",
							'mod_date' => now(),
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

		return response()->json(["code" => $code, "msg" => $msg, "data" => ["sale_type_cd" => $idx]]);

	}
}

