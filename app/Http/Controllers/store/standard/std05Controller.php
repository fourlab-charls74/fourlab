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
use Illuminate\Support\Facades\Date;

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

		$date = Carbon::now()->timezone('Asia/Seoul')->format('Y-m-d');

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
			select 
				s.sale_kind
				, c.code_val as sale_kind_nm
				, s.idx as sale_type_cd
				, s.sale_type_nm
				, s.sale_apply
				, s.amt_kind, s.sale_amt
				, s.sale_per, s.use_yn
				, (select count(ss.idx) from sale_type_store ss where ss.sale_type_cd = s.idx and ss.use_yn = 'Y' and ss.sdate >= '$date') as store_cnt
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
				select s.idx, s.sale_kind, c.code_val as sale_kind_nm, c.code_id as sale_type_cd, s.sale_type_nm, s.sale_apply, s.amt_kind, s.sale_amt, s.sale_per, s.use_yn
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

		return view(Config::get('shop.store.view') . '/standard/std05_show', $values);
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

	// 판매유형별 브랜드 조회
	public function search_brand(Request $request, $sale_type_cd = '')
	{
		$code = 200;

		$sql = "
			select brand.brand, brand.brand_nm, b.use_yn, b.sdate, b.edate
			from brand
				left outer join sale_type_brand b
					on brand.brand = b.brand and b.sale_type_cd = :sale_type_cd
			where 1 = 1
			order by brand.brand_type,brand.brand_nm
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
		$admin_nm = Auth('head')->user()->name;
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
				'mod_date' => now(),
				'admin_id' => $admin_id,
			]);

			$cnt = DB::table('code')
					->where('code_kind_cd', '=', 'SALE_KIND')
					->count();

			DB::table('code')
				->insert([
					'code_kind_cd' => 'SALE_KIND',
					'code_id' => $r['sale_kind'],
					'code_val' => $r['sale_type_nm'],
					'use_yn' => $r['use_yn'],
					'code_seq' => $cnt + 1,
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
					'rt' => now(),
					'ut' => now()
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

			foreach($r['brand_datas'] as $b) {
				DB::table('sale_type_brand')->insert([
					'sale_type_cd' => $idx,
					'brand' => $b['brand'],
					'brand_nm' => $b['brand_nm'],
					'use_yn' => $b['use_yn'] ?? "N",
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

			DB::table('code')
				->where('code_kind_cd','=', 'SALE_KIND')
				->where('code_id', '=', $r['sale_kind'])
				->update([
					'code_val' => $r['sale_type_nm'],
					'use_yn' => $r['use_yn'],
					'ut' => now()
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

			foreach($r['brand_datas'] as $b) {
				$cnt = DB::table('sale_type_brand')
					->where("sale_type_cd", "=", $idx)
					->where("brand", "=", $b['brand'])
					->count();
				if($cnt < 1) {
					DB::table('sale_type_brand')->insert([
						'sale_type_cd' => $idx,
						'brand' => $b['brand'],
						'brand_nm' => $b['brand_nm'],
						'use_yn' => $b['use_yn'] ?? "N",
						'reg_date' => now(),
					]);
				} else {
					DB::table('sale_type_brand')
						->where("sale_type_cd", "=", $idx)
						->where("brand", "=", $b['brand'])
						->update([
							'use_yn' => $b['use_yn'] ?? "N",
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

	// 판매구분코드 중복체크
	public function check_code($sale_type_cd = '') 
	{
		$code	= 200;
		$msg	= "사용가능한 코드입니다.";

		$sql	= " select count(code_id) as cnt from code where code_kind_cd = 'SALE_KIND' and code_id = :sale_type_cd ";

		$cnt	= DB::selectOne($sql, ["sale_type_cd" => $sale_type_cd])->cnt;

		if( $cnt > 0 ){
			$code	= 409;
			$msg	= "이미 사용중인 코드입니다.";
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
}

