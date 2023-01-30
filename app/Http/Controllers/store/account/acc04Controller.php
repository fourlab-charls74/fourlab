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
        // $brand_cd = $request->input('brand_cd', ''); // 브랜드
        $sale_yn = $request->input('sale_yn', 'Y'); // 매출여부
        $pr_codes = SLib::getCodes('PR_CODE'); // 행사코드목록
        $pr_codes = array_map(function($c) { return $c->code_id; }, $pr_codes->toArray());

        // 검색조건 필터링
        $where = "";
        // 브랜드 필터링 필요
        if ($store_type != '') $where .= " and s.store_type = '" . Lib::quote($store_type) . "' ";
        if (count($store_no) > 0) $where .= " and s.store_cd in (" . join(",", array_map(function($cd) { return "'$cd'"; }, $store_no)) . ") ";
        if (count($sale_kind) > 0) $where .= " and w.sale_kind in (" . join(",", array_map(function($cd) { return "'$cd'"; }, $sale_kind)) . ") ";
        if ($sale_yn == 'Y') $where .= " and w.sales_total > 0 ";

        $sql = "
            select b.*
                , (ifnull(b.sales_profit, 0) - ifnull(b.total_fee, 0)) as sales_real_profit
                , (((ifnull(b.sales_profit, 0) - ifnull(b.total_fee, 0)) / ifnull(b.sales_total, 0)) * 100) as real_profit_rate
            from (
                select a.*
                    , (" . join(' + ', array_map(function($cd) { return "a." . $cd . "_fee"; }, $pr_codes)) . ") as total_fee
                from (
                    select
                        w.*
                        , (w.sales_total - w.wonga_total) as sales_profit
                        , (((w.sales_total - w.wonga_total) / w.sales_total) * 100) as profit_rate
                        , s.store_cd
                        , s.store_nm
                        , st.code_val as store_type_nm
                        , " . join(",", array_map(function($cd) { return "sf." . $cd . "_fee_rate"; }, $pr_codes)) . "
                        , sf.etc_fee_rate
                        , " . join(",", array_map(function($cd) { return "(ifnull(w.sales_" . $cd . ", 0) * (ifnull(sf." . $cd . "_fee_rate, 0) / 100)) as " . $cd . "_fee"; }, $pr_codes)) . "
                        , (ifnull(w.sales_etc, 0) * (ifnull(sf.etc_fee_rate, 0) / 100)) as etc_fee
                    from store s 
                        inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = s.store_type
                        left outer join (
                            select oo.ord_opt_no, oo.pr_code, oo.sale_kind, oo.store_cd as ord_store_cd, ww.qty, ww.wonga, ww.price
                                , sum(ww.qty) as sale_qty
                                , sum(ww.qty * ww.wonga) as wonga_total
                                , sum(ww.recv_amt) as sales_total
                                , " . join(",", array_map(function($cd) { return "sum(if(oo.pr_code = '" . $cd . "', ww.recv_amt, 0)) as sales_" . $cd . ""; }, $pr_codes)) . "
                                , sum(if(oo.pr_code not in (" . join(',', array_map(function($cd) { return "'$cd'"; }, $pr_codes)) . "), (ww.qty * ww.price), 0)) as sales_etc
                            from order_opt_wonga ww
                                inner join order_opt oo on oo.ord_opt_no = ww.ord_opt_no
                            where ww.ord_state in (30,60,61) 
                                and ww.ord_state_date >= '$sdate'
                                and ww.ord_state_date <= '$edate'
                            group by oo.store_cd
                        ) w on w.ord_store_cd = s.store_cd
                        left outer join (
                            select 
                                idx, store_cd, pr_code
                                , " . join(",", array_map(function($cd) { return "sum(if(pr_code = '" . $cd . "', store_fee, 0)) as " . $cd . "_fee_rate"; }, $pr_codes)) . "
                                , sum(if(pr_code not in (" . join(',', array_map(function($cd) { return "'$cd'"; }, $pr_codes)) . "), store_fee, 0)) as etc_fee_rate
                            from store_fee 
                            where idx in (select max(idx) from store_fee group by store_cd, pr_code)
                            group by store_cd
                        ) sf on sf.store_cd = s.store_cd
                    where 1=1 $where
                    order by w.sales_total desc
                ) a
            ) b
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
