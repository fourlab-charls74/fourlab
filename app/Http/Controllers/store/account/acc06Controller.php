<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class acc06Controller extends Controller
{
    public function index(Request $request) {
        // $sdate = Carbon::now()->startOfMonth()->format("Y-m-d"); // 이번 달 기준
        // $edate = Carbon::now()->format("Y-m-d"); // 현재
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준 - 테스트용

        $store_types = SLib::getStoreTypes();

        $sql = "select 
            code_id, code_val 
            from `code` 
            where code_kind_cd = 'pr_code'
            order by code_seq asc
        ";
        $pr_codes = DB::select($sql);

        $values = [
            'sdate'         => $sdate,
            'store_types'	=> $store_types,
            'store_kinds'	=> SLib::getCodes("STORE_KIND"),
            'pr_codes'      => $pr_codes
        ];

        return view( Config::get('shop.store.view') . '/account/acc06', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->format("Y-m"));

        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

        $sdate = str_replace("-", "", $sdate);

        $store_type = $request->input('store_type', "");
        $store_kind = $request->input('store_kind', "");
        $store_cd = $request->input('store_cd', "");

        /**
         * 검색조건 필터링
         */
        $where = "";
        if ($store_type) $where .= " and c.code_id = " . Lib::quote($store_type);
        if ($store_kind != "") $where .= " and s.store_kind = '". Lib::quote($store_kind) . "'";
        if ($store_cd != "") $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";

        $sql = "select 
            code_id, code_val 
            from `code` 
            where code_kind_cd = 'pr_code'
            order by code_seq asc
        ";
        $pr_codes = DB::select($sql);

        // 행사코드별 매출구분
        $pr_codes_query = "";
        foreach ($pr_codes as $item) {
            $key = $item->code_id;
            $pr_codes_query .= "sum(if(m.pr_code = '$key', o.price * o.qty, 0)) as amt_$key,";
        }

        /**
         * 특가 -> 행사, 특가(온라인) -> 균일로 우선 적용해놓았음 
         * 미구현된 두 항목은 추후 반영이 필요함
         */
        $sql = /** @lang text */
            "
			select 
                s.store_nm, c.code_val as store_type_nm, 
                round(if(amt_js > sg.amt1,sg.amt1 * fee1/100,amt_js * fee1/100 )) as fee_amt_js1,
                round(if(amt_js > sg.amt1,if((amt_js - sg.amt1) > sg.amt2,sg.amt2 * fee2/100,(amt_js - sg.amt1) * fee2/100 ),0)) as fee_amt_js2,
                round(if((amt_js - sg.amt1) > sg.amt2,(amt_js - sg.amt1 - sg.`amt2`) * fee3/100,0)) as fee_amt_js3,
                round(amt_gl * sg.fee_10/100) as fee_amt_gl,
                round(amt_j1 * sg.fee_10/100) as fee_amt_j1,
                round(amt_j2 * sg.fee_11/100) as fee_amt_j2,
                sg.*, a.*, b.extra_total
			from store s 
                left outer join (
                    select
                        m.store_cd,count(*) as cnt,
                        $pr_codes_query
                        sum(o.price*o.qty) as ord_amt
                    from order_mst m
                        inner join order_opt o on m.ord_no = o.ord_no
                        inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                        inner join goods g on o.goods_no = g.goods_no
                        left outer join store s on m.store_cd = s.store_cd
                        left outer join brand b on g.brand = b.brand
                        left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
                        left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
                    where w.ord_state_date >= '$f_sdate' and w.ord_state_date <= '$f_edate'
                        and m.store_cd <> ''
                    group by m.store_cd
			    ) as a on s.store_cd = a.store_cd
				left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
                left outer join store_grade sg on sg.grade_cd = s.grade_cd
                left outer join (
                    select 
                        e.store_cd as scd,
                        sum(e.extra_amt) as extra_total
                    from store_account_extra as e
                        left outer join `code` c3 on c3.code_kind_cd = 'g_acc_extra_type' and c3.code_id = e.type
                    where ymonth = '$sdate'
                    group by e.store_cd
                ) b on s.store_cd = b.scd
			where 1=1 and sg.use_yn = 'Y' $where
            order by a.ord_amt desc
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

}
