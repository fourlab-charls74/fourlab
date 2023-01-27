<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class acc04Controller extends Controller
{
    public function index(Request $request) 
    {
        $sdate = Carbon::now()->startOfMonth()->format("Y-m-d"); // 이번 달 기준
        $edate = Carbon::now()->format("Y-m-d");
        $sale_kind_id = $request->input('sale_kind', '');

        $values = [
            'sdate'         => $sdate,
            'edate'         => $edate,
            'pr_codes'		=> SLib::getCodes('PR_CODE'),
            'store_types'	=> SLib::getStoreTypes(),
            'sale_kinds' 	=> SLib::getUsedSaleKinds(),
            'sale_kind_id' 	=> $sale_kind_id
        ];
        return view( Config::get('shop.store.view') . '/account/acc04', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->startOfMonth()->format("Ymd"));
        $edate = $request->input('edate', date("Ymd"));

        $sdate = str_replace('-', '', $sdate);
        $edate = str_replace('-', '', $edate);

        $store_type = $request->input('store_type', ''); // 매장구분
		$store_no = $request->input('store_no', []); // 매장명 리스트
        $sale_kind = $request->input('sale_kind', []); // 판매유형
        $brand_cd = $request->input('brand_cd', ''); // 브랜드
        $sale_yn = $request->input('sale_yn', 'Y'); // 매출여부

        // 검색조건 필터링
        $where = "";
        // 매장구분/매장명/판매유형/브랜드 필터링 필요
        if ($sale_yn == 'Y') $where .= " and w.sales_total > 0 ";

        $sql = "
            select
                w.*
                , (w.sales_total - w.wonga_total) as sales_profit
                , (w.sales_total / (w.sales_total - w.wonga_total)) as profit_rate
                , (((w.sales_total - w.wonga_total) / w.sales_total) * 100) as profit_rate
                , s.store_cd
                , s.store_nm
                , st.code_val as store_type_nm
                , 0 as fee -- 작업중입니다
            from store s 
                inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = s.store_type
                left outer join (
                    select oo.ord_opt_no, oo.pr_code, oo.store_cd, ww.qty, ww.wonga, ww.price
                        , sum(ww.qty) as sale_qty
                        , sum(ww.qty * ww.wonga) as wonga_total
                        , sum(ww.qty * ww.price) as sales_total
                        , sum(if(oo.pr_code = 'JS', (ww.qty * ww.price), 0)) as sales_JS
                        , sum(if(oo.pr_code = 'GL', (ww.qty * ww.price), 0)) as sales_GL
                        , sum(if(oo.pr_code = 'J1', (ww.qty * ww.price), 0)) as sales_J1
                        , sum(if(oo.pr_code = 'J2', (ww.qty * ww.price), 0)) as sales_J2
                        , sum(if(oo.pr_code not in ('JS', 'GL', 'J1', 'J2'), (ww.qty * ww.price), 0)) as sales_etc
                    from order_opt_wonga ww
                        inner join order_opt oo on oo.ord_opt_no = ww.ord_opt_no
                    where ww.ord_state in (30,60,61) 
                        and ww.ord_state_date >= '$sdate'
                        and ww.ord_state_date <= '$edate'
                    group by oo.store_cd
                ) w on w.store_cd = s.store_cd
            where 1=1 $where
            order by s.store_cd
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
