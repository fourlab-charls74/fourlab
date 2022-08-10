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
			'sdate' => Carbon::now()->format('Y-m'),
			'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
		];
        return view(Config::get('shop.store.view') . '/sale/sal18', $values);
	}

	// 판매유형 조회
	public function search(Request $request)
	{	
		$sale_month = str_replace('-', '', $request->input('sdate', ''));
		
		$sql = "
			select
				s.idx as sale_type_cd,
				s.sale_kind,
				s.sale_type_nm,
				'$sale_month' as apply_date,
				ifnull(sta.apply_yn, 'N') as apply_yn
			from sale_type s
				left outer join sale_type_apply sta on sta.sale_type_cd = s.idx and sta.apply_date = '$sale_month'
			where s.use_yn = 'Y'
			order by s.sale_kind
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
		$sale_month = str_replace('-', '', $request->input('sdate', ''));
		$store_type = $request->input('store_type', '');
		$store_cd = $request->input('store_no', '');
		$where = "";
		
		if($store_type != '') $where .= " and s.store_type = '$store_type'";
		if($store_cd != '') $where .= " and s.store_cd = '$store_cd'";

		$last_month = date('Ym', strtotime('-1 month', strtotime($sale_month . '01')));
		$last_year = date('Ym', strtotime('-1 year', strtotime($sale_month . '01')));

		$sql = "
			select
				s.store_cd,
				s.store_nm,
				s.store_type,
				c.code_val as store_type_nm,
				stas.apply_rate as this_month_rate,
				(select apply_rate from sale_type_apply_store where apply_date = '$last_month' and store_cd = s.store_cd) as last_month_rate,
				(select apply_rate from sale_type_apply_store where apply_date = '$last_year' and store_cd = s.store_cd) as last_year_rate,
				stas.comment
			from store s
				left outer join code c on c.code_kind_cd = 'STORE_TYPE' and s.store_type = c.code_id
				left outer join sale_type_apply_store stas on stas.apply_date = '$sale_month' and stas.store_cd = s.store_cd
			where 1=1 $where
			order by s.store_cd
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

	// 월별할인유형적용 저장
	public function save(Request $request)
	{
		$sale_types = $request->input('sale_types', []);
		$sale_type_stores = $request->input('sale_type_stores', []);
        $admin_id = Auth('head')->user()->id;

		try {
            DB::beginTransaction();

			// 판매유형별 월별할인유형정보 업데이트
			foreach($sale_types as $sale_type)
			{
                $is_exist =
                    DB::table('sale_type_apply')
                        ->where('apply_date', '=', $sale_type['apply_date'])
                        ->where('sale_type_cd', '=', $sale_type['sale_type_cd'])
                        ->count();
                if($is_exist < 1) {
                    DB::table('sale_type_apply')
                        ->insert([
                            'apply_date' => $sale_type['apply_date'],
                            'sale_type_cd' => $sale_type['sale_type_cd'],
                            'apply_yn' => $sale_type['apply_yn'],
                            'rt' => now(),
                            'admin_id' => $admin_id,
                        ]);
                } else {
                    DB::table('sale_type_apply')
						->where('apply_date', '=', $sale_type['apply_date'])
                        ->where('sale_type_cd', '=', $sale_type['sale_type_cd'])
                        ->update([
                            'apply_yn' => $sale_type['apply_yn'],
                            'ut' => now(),
                            'admin_id' => $admin_id,
                        ]);
                }
			}

			// 매장별 월별할인유형정보 업데이트
			foreach($sale_type_stores as $sale_type_store)
			{
				// 작업예정
				dd($sale_type_store);
			}

			DB::commit();
            $code = 200;
			$msg = '저장이 정상적으로 완료되었습니다.';
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(['code' => $code, 'msg' => $msg]);
	}
}
