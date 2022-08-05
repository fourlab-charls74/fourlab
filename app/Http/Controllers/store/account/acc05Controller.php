<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class acc05Controller extends Controller
{
    public function index(Request $request) {

        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 이번 달 기준

        $store_types = SLib::getStoreTypes();
        $sale_kinds = SLib::getUsedSaleKinds();
        $dynamic_cols = SLib::getCodes('G_ACC_EXTRA_TYPE')->groupBy('code_val2');
        
        $values = [
            'sdate' => $sdate,
            'store_types' => $store_types,
            'sale_kinds' => $sale_kinds,
            'dynamic_cols' => $dynamic_cols,
        ];

        return view( Config::get('shop.store.view') . '/account/acc05', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->format("Y-m"));
        $sdate = Lib::quote(str_replace("-", "", $sdate));

        $store_type = $request->input('store_type', "");
        $store_cd = $request->input('store_cd', "");

        /**
         * 검색조건 필터링
         */
        $where = "";
        if ($store_cd != "") $where .= " and s.store_cd like '" . Lib::quote($store_cd) . "%'";

        $where2 = "";
        if ($store_type) $where2 .= " and c.code_id = " . Lib::quote($store_type);

        /**
         * 기타재반자료구분 쿼리 추가
         */
        $extra_types = SLib::getCodes('G_ACC_EXTRA_TYPE');

        $extra_types_query = $extra_types->reduce((function ($carry, $item) 
        {
            $query = $carry[0];
            $group_nm = $carry[1];
            
            $type = $item->code_id;
            if ($group_nm != $item->code_val2) {
                $group_nm = $item->code_val2;
                $query = $query . "sum(if(c2.code_val2 = '$group_nm', e.extra_amt, 0)) as ${group_nm}_sum, ";
            }

            $query = $query . "sum(if(e.type = '$type', e.extra_amt, 0)) as ${type}_code, ";
            return [$query, $group_nm];
        }), ["", ""]);
        $extra_types_query = $extra_types_query[0];

        $sql = /** @lang text */
        "
			select s.store_cd, s.store_nm, c.code_val as store_type_nm, a.* 
            from store s
                left outer join `code` c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
                left outer join 
                (
                    select 
                        e.ymonth as ymonth,
                        $extra_types_query
                        e.store_cd as scd
                    from store_account_extra as e
                        left outer join `code` c2 on c2.code_kind_cd = 'g_acc_extra_type' and c2.code_id = e.type
                    where ymonth = '$sdate' $where
                    group by e.store_cd
                ) as a on s.store_cd = a.scd
            $where2
		";

        $result = DB::select($sql);

        return response()->json([
            'code'	=> 200,
            'head'	=> array(
                'total'	=> count($result)
            ),
            'body' => $result
        ]);

    }

	public function save(Request $request)
	{
		$data = $request->input('data');
		try {
			DB::transaction(function () use ($data) {
				foreach ($data as $row) {
					/**
					 * 데이터 가공, 초기 값 설정 및 불필요한 프로퍼티 제거
					 */
                    unset($row['editable']);
                    if (array_key_exists('ymonth', $row) === false) $row['ymonth'] = now()->format("Ym");
                    $store_cd = Lib::quote($row['store_cd']);
                    $ymonth = Lib::quote($row['ymonth']);
					/**
					 * 등급이 있는 경우 업데이트 / 없는 경우 추가
					 */
					
					$sql = /** @lang text */
                    "
                        select store_cd, ymonth, count(*) as cnt 
                        from store_account_extra s
                        where s.store_cd = '$store_cd'
                        and s.ymonth = '$ymonth'
                    ";
					$result = DB::selectOne($sql);
					if ($result->cnt > 0) {
                        echo "업데이트";
						dd($result);
						// DB::table('store_grade')->where('idx', "=", $result->idx)->update($row);
					} else {
                        echo "삽입";
						dd($result);
						DB::table('store_account_extra')->insert($row);
					}
				}
			});
			return response()->json(['code'	=> '200']);
		} catch (\Exception $e) {
            dd($e);
			return response()->json(['code' => '500']);
		}
		return response()->json([]);
	}

}
