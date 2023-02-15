<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

// 기타재반자료
class acc05Controller extends Controller
{
    public function index(Request $request) {

        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준
        // $sdate = Carbon::now()->startOfMonth()->format("Y-m"); // 테스트용
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Y-m-d H:i:s");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Y-m-d H:i:s");

        $extra_cols = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2'); // code_val2를 상위 카테고리로 사용

        $sql = "
            select r.prd_cd, p.prd_nm, p.type
            from sproduct_stock_release r
                inner join store s on s.store_cd = r.store_cd and s.account_yn = 'Y'
                inner join product p on p.prd_cd = r.prd_cd and p.type = :type
            where r.fin_rt >= '$f_sdate' and r.fin_rt <= '$f_edate'
            group by r.prd_cd
        ";
        $gifts = DB::select($sql, ['type' => 'G']); // 사은품
        $expandables = DB::select($sql, ['type' => 'S']); // 부자재(소모품)

        $values = [
            'sdate' => $sdate,
            'store_types' => SLib::getStoreTypes(),
			'store_kinds' => SLib::getCodes("STORE_KIND"),
            'sale_kinds' => SLib::getUsedSaleKinds(),
            'extra_cols' => $extra_cols,
            'gifts' => $gifts,
            'expandables' => $expandables,
        ];

        return view( Config::get('shop.store.view') . '/account/acc05', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Y-m-d H:i:s");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Y-m-d H:i:s");
        $sdate = Lib::quote(str_replace('-', '', $sdate));

        $store_type = $request->input('store_type', '');
        $store_kind = $request->input('store_kind', '');
        $store_cd = $request->input('store_cd', '');

        $sql = "
            select r.prd_cd, p.prd_nm, p.type
            from sproduct_stock_release r
                inner join store s on s.store_cd = r.store_cd and s.account_yn = 'Y'
                inner join product p on p.prd_cd = r.prd_cd and p.type = :type
            where r.fin_rt >= '$f_sdate' and r.fin_rt <= '$f_edate'
            group by r.prd_cd
        ";
        $gifts = DB::select($sql, ['type' => 'G']); // 사은품
        $expandables = DB::select($sql, ['type' => 'S']); // 부자재(소모품)
        $extra_types = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2')->toArray(); // 사은품/부자재 외 기타재반

        // 검색조건 필터링
        $where = "";
        if ($store_type != '') $where .= " and s.store_type = " . Lib::quote($store_type);
        if ($store_kind != '') $where .= " and s.store_kind = '". Lib::quote($store_kind) . "'";
        if ($store_cd != '') $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";

        // 기타재반타입별 쿼리문 생성
        $extra_sql = join('', array_map(function($key, $value) {
            $query = join('', array_map(function($v) {
                return ", sum(if(e.type = '" . $v->code_id . "', e.extra_amt, null)) as " . $v->code_id . "_amt";
            }, $value));
            $query .= ", sum(if(e.type in (" . join(',', array_map(function($val) { return "'" . $val->code_id . "'"; }, $value)) . "), e.extra_amt, null)) as " . $key . "_sum";
            return $query;
        }, array_keys($extra_types), $extra_types));

        // 사은품 쿼리문 생성
        $extra_sql .= join('', array_map(function($value) {
            return ", sum(if(e.type = '" . $value->type . "' and e.prd_cd = '" . $value->prd_cd . "', e.extra_amt, null)) as " . $value->prd_cd . "_amt";
        }, $gifts));
        $extra_sql .= ", sum(if(e.type = 'G', e.extra_amt, null)) as gifts_sum";
        
        // 부자재(소모품) 쿼리문 생성
        $extra_sql .= join('', array_map(function($value) {
            return ", sum(if(e.type = '" . $value->type . "' and e.prd_cd = '" . $value->prd_cd . "', e.extra_amt, null)) as " . $value->prd_cd . "_amt";
        }, $expandables));
        $extra_sql .= ", sum(if(e.type = 'S', e.extra_amt, null)) as expandables_sum";

        $sql = "
            select s.store_cd, s.store_nm, s.store_type, c.code_val as store_type_nm, e.*
            from store s
                left outer join (
                    select e.store_cd as store
                        $extra_sql
                    from store_account_extra e
                    where e.ymonth = :ymonth
                    group by e.store_cd
                ) e on e.store = s.store_cd
                left outer join code c on c.code_kind_cd = 'STORE_TYPE' and c.code_id = s.store_type
            where s.account_yn = 'Y' $where
        ";
        $result = DB::select($sql, ['ymonth' => $sdate]);

        return response()->json([
            'code'	=> 200,
            'head'	=> [
                'total'	=> count($result),
                'gifts' => $gifts,
                'expandables' => $expandables,
            ],
            'body' => $result
        ]);
    }

	public function save(Request $request)
	{
		$data = $request->input('selected_data');
		try {
			DB::transaction(function () use ($data) {
				foreach ($data as $row) {
					/**
					 * 데이터 가공, 초기 값 설정
					 */
                    $codes = $row['codes'];
                    $amts = $row['amts'];
                    $store_cd = Lib::quote($row['store_cd']);
                    $ymonth = Lib::quote($row['ymonth']);

					/**
					 * 등급이 있는 경우 업데이트 / 없는 경우 추가
					 */
                    for ($i=0; $i < count($codes); $i++) { 
                        $code = Lib::quote($codes[$i]);
                        $amt = Lib::quote($amts[$i]);
                        $sql = /** @lang text */
                        "
                            select idx, count(*) as cnt 
                            from store_account_extra s
                            where s.store_cd = '$store_cd'
                                and s.ymonth = '$ymonth'
                                and s.type = '$code'
                        ";
                        $result = DB::selectOne($sql);
                        if ($result->cnt > 0) {
                            DB::table('store_account_extra')->where('idx', "=", $result->idx)
                            ->update(['extra_amt' => $amt]);
                        } else {
                            DB::table('store_account_extra')->insert([
                                'store_cd' => $store_cd,
                                'ymonth' => $ymonth,
                                'type' => $code,
                                'extra_amt' => $amt
                            ]);
                        }
                    }
				}
			});
			return response()->json(['code'	=> '200']);
		} catch (\Exception $e) {
            // dd($e);
			return response()->json(['code' => '500']);
		}
		return response()->json([]);
	}
}
