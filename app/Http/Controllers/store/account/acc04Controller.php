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
        // $sdate = Carbon::now()->startOfMonth()->format("Y-m"); // 이번 달 기준
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준

        $values = [
            'sdate'         => $sdate,
            'pr_codes'		=> SLib::getCodes('PR_CODE'),
            'store_types'	=> SLib::getStoreTypes(),
            'store_kinds'	=> SLib::getCodes("STORE_KIND"),
        ];
        return view( Config::get('shop.store.view') . '/account/acc04', $values );
    }

    public function search(Request $request)
    {
        /*********************************** 아래 쿼리문 작업중입니다. - 최유현 ************************************/
        
        // $sdate = $request->input('sdate', now()->startOfMonth()->format("Ymd"));
        // $edate = $request->input('edate', date("Ymd"));

        // $sdate = str_replace('-', '', $sdate);
        // $edate = str_replace('-', '', $edate);

        // $store_type = $request->input('store_type', ''); // 매장구분
		// $store_no = $request->input('store_no', []); // 매장명 리스트
        // $sale_kind = $request->input('sale_kind', []); // 판매유형
        // // $brand_cd = $request->input('brand_cd', ''); // 브랜드
        // $sale_yn = $request->input('sale_yn', 'Y'); // 매출여부
        // $pr_codes = SLib::getCodes('PR_CODE'); // 행사코드목록
        // $pr_codes = array_map(function($c) { return $c->code_id; }, $pr_codes->toArray());

        // // 검색조건 필터링
        // $where = "";
        // // 브랜드 필터링 필요
        // if ($store_type != '') $where .= " and s.store_type = '" . Lib::quote($store_type) . "' ";
        // if (count($store_no) > 0) $where .= " and s.store_cd in (" . join(",", array_map(function($cd) { return "'$cd'"; }, $store_no)) . ") ";
        // if (count($sale_kind) > 0) $where .= " and w.sale_kind in (" . join(",", array_map(function($cd) { return "'$cd'"; }, $sale_kind)) . ") ";
        // if ($sale_yn == 'Y') $where .= " and w.sales_total > 0 ";

        // $sql = "
        //     select b.*
        //         , (ifnull(b.sales_profit, 0) - ifnull(b.total_fee, 0)) as sales_real_profit
        //         , (((ifnull(b.sales_profit, 0) - ifnull(b.total_fee, 0)) / ifnull(b.sales_total, 0)) * 100) as real_profit_rate
        //     from (
        //         select a.*
        //             , (" . join(' + ', array_map(function($cd) { return "a." . $cd . "_fee"; }, $pr_codes)) . ") as total_fee
        //         from (
        //             select
        //                 w.*
        //                 , (w.sales_total - w.wonga_total) as sales_profit
        //                 , (((w.sales_total - w.wonga_total) / w.sales_total) * 100) as profit_rate
        //                 , s.store_cd
        //                 , s.store_nm
        //                 , st.code_val as store_type_nm
        //                 , " . join(",", array_map(function($cd) { return "sf." . $cd . "_fee_rate"; }, $pr_codes)) . "
        //                 , sf.etc_fee_rate
        //                 , " . join(",", array_map(function($cd) { return "(ifnull(w.sales_" . $cd . ", 0) * (ifnull(sf." . $cd . "_fee_rate, 0) / 100)) as " . $cd . "_fee"; }, $pr_codes)) . "
        //                 , (ifnull(w.sales_etc, 0) * (ifnull(sf.etc_fee_rate, 0) / 100)) as etc_fee
        //             from store s 
        //                 inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = s.store_type
        //                 left outer join (
        //                     select oo.ord_opt_no, oo.pr_code, oo.sale_kind, oo.store_cd as ord_store_cd, ww.qty, ww.wonga, ww.price
        //                         , sum(ww.qty) as sale_qty
        //                         , sum(ww.qty * ww.wonga) as wonga_total
        //                         , sum(ww.recv_amt) as sales_total
        //                         , " . join(",", array_map(function($cd) { return "sum(if(oo.pr_code = '" . $cd . "', ww.recv_amt, 0)) as sales_" . $cd . ""; }, $pr_codes)) . "
        //                         , sum(if(oo.pr_code not in (" . join(',', array_map(function($cd) { return "'$cd'"; }, $pr_codes)) . "), (ww.qty * ww.price), 0)) as sales_etc
        //                     from order_opt_wonga ww
        //                         inner join order_opt oo on oo.ord_opt_no = ww.ord_opt_no
        //                     where ww.ord_state in (30,60,61) 
        //                         and ww.ord_state_date >= '$sdate'
        //                         and ww.ord_state_date <= '$edate'
        //                     group by oo.store_cd
        //                 ) w on w.ord_store_cd = s.store_cd
        //                 left outer join (
        //                     select 
        //                         idx, store_cd, pr_code
        //                         , " . join(",", array_map(function($cd) { return "sum(if(pr_code = '" . $cd . "', store_fee, 0)) as " . $cd . "_fee_rate"; }, $pr_codes)) . "
        //                         , sum(if(pr_code not in (" . join(',', array_map(function($cd) { return "'$cd'"; }, $pr_codes)) . "), store_fee, 0)) as etc_fee_rate
        //                     from store_fee 
        //                     where idx in (select max(idx) from store_fee group by store_cd, pr_code)
        //                     group by store_cd
        //                 ) sf on sf.store_cd = s.store_cd
        //             where 1=1 $where
        //             order by w.sales_total desc
        //         ) a
        //     ) b
        // ";

        $sql = "
            select ord.*, s.*
                , (ord.sales_JS_amt_except_vat * s.sales_JS_fee_rate / 100) as sales_JS_fee
                , (ord.sales_GL_amt_except_vat * s.sales_GL_fee_rate / 100) as sales_GL_fee
                , (ord.sales_J1_amt_except_vat * s.sales_J1_fee_rate / 100) as sales_J1_fee
                , (ord.sales_J2_amt_except_vat * s.sales_J2_fee_rate / 100) as sales_J2_fee
                
                , (if(ord.ord_JS_amt > s.amt1, s.amt1, ord.ord_JS_amt) / 1.1) as ord_JS1_amt_except_vat
                , (if(ord.ord_JS_amt > s.amt2, s.amt2 - s.amt1, if(ord.ord_JS_amt > s.amt1, ord.ord_JS_amt - s.amt1, 0)) / 1.1) as ord_JS2_amt_except_vat
                , (if(ord.ord_JS_amt > s.amt2, ord.ord_JS_amt - s.amt2, 0) / 1.1) as ord_JS3_amt_except_vat
                , (ord.ord_TG_amt / 1.1) as ord_TG_amt_except_vat
                , (ord.ord_YP_amt / 1.1) as ord_YP_amt_except_vat
                , (ord.ord_OL_amt / 1.1) as ord_OL_amt_except_vat
                
                , (if(ord.ord_JS_amt > s.amt1, s.amt1, ord.ord_JS_amt) / 1.1 * s.fee1 / 100) as fee_amt_JS1
                , (if(ord.ord_JS_amt > s.amt2, s.amt2 - s.amt1, if(ord.ord_JS_amt > s.amt1, ord.ord_JS_amt - s.amt1, 0)) / 1.1 * s.fee2 / 100) as fee_amt_JS2
                , (if(ord.ord_JS_amt > s.amt2, ord.ord_JS_amt - s.amt2, 0) / 1.1 * s.fee3 / 100) as fee_amt_JS3
                , (ord.ord_TG_amt / 1.1 * s.fee_10 / 100) as fee_amt_TG
                , (ord.ord_YP_amt / 1.1 * s.fee_11 / 100) as fee_amt_YP
                , (ord.ord_OL_amt / 1.1 * s.fee_12 / 100) as fee_amt_OL
                , ifnull(ae.extra_amt, 0) as extra_amt
            from (
                select y.ymonth, s.*
                from (
                    select date_format(ww.ord_state_date, '%Y%m') as ymonth
                    from order_opt_wonga ww
                    where ww.ord_state_date >= '20230101'
                        and ww.ord_state_date <= '20230331'
                    group by ymonth
                ) y
                    left outer join (
                        select ss.store_cd, ss.store_nm, ss.manager_nm, ss.store_type, ss.store_kind, ss.account_yn
                            , ss.grade_cd, sg.name as grade_nm
                            , sg.fee1, round(sg.amt1 * 1.1) as amt1, sg.fee2, round(sg.amt2 * 1.1) as amt2, sg.fee3
                            , sg.fee_10, sg.fee_11, sg.fee_12, sg.fee_10_info, sg.fee_10_info_over_yn
                            , sf.*
                        from store ss
                            inner join store_grade sg on sg.grade_cd = ss.grade_cd and concat(replace(sg.sdate, '-', ''), '01') <= '20230302' and concat(replace(sg.edate, '-', ''), '31') >= '20230302'
                            left outer join (
                                select f.store_cd as f_store_cd
                                    , max(if(f.pr_code = 'JS', f.store_fee, 0)) as sales_JS_fee_rate
                                    , max(if(f.pr_code = 'GL', f.store_fee, 0)) as sales_GL_fee_rate
                                    , max(if(f.pr_code = 'J1', f.store_fee, 0)) as sales_J1_fee_rate
                                    , max(if(f.pr_code = 'J2', f.store_fee, 0)) as sales_J2_fee_rate
                                from store_fee f
                                where f.use_yn = 'Y' and f.idx in (select max(idx) from store_fee where store_cd = f.store_cd group by pr_code)
                                group by f.store_cd
                            ) sf on sf.f_store_cd = ss.store_cd
                    ) s on s.account_yn = 'Y'
                group by y.ymonth desc, s.store_cd
            ) s
                left outer join (
                    select w.ymonth, w.store_cd
                        , sum(if(w.online_yn = 'N', w.recv_amt, 0)) as sales_amt -- 매출합계
                        , (sum(if(w.online_yn = 'N', w.recv_amt, 0)) / 1.1) as sales_amt_except_vat -- 매출합계(-vat)
                        , (sum(if(w.online_yn = 'N' and w.pr_code = 'JS', w.recv_amt, 0)) / 1.1) as sales_JS_amt_except_vat
                        , (sum(if(w.online_yn = 'N' and w.pr_code = 'GL', w.recv_amt, 0)) / 1.1) as sales_GL_amt_except_vat
                        , (sum(if(w.online_yn = 'N' and w.pr_code = 'J1', w.recv_amt, 0)) / 1.1) as sales_J1_amt_except_vat
                        , (sum(if(w.online_yn = 'N' and w.pr_code = 'J2', w.recv_amt, 0)) / 1.1) as sales_J2_amt_except_vat
                        , sum(if(
                            w.online_yn = 'N'
                                and g.brand not in (select code_id from code where code_kind_cd = 'YP_BRAND') 
                                and if(ss.fee_10_info_over_yn = 'Y', ((1 - (w.price / g.goods_sh)) * 100) <= ss.fee_10_info, ((1 - (w.price / g.goods_sh)) * 100) < ss.fee_10_info)
                            , w.recv_amt
                            , 0
                        )) as ord_JS_amt -- 정상
                        , sum(if(
                            w.online_yn = 'N'
                                and g.brand not in (select code_id from code where code_kind_cd = 'YP_BRAND') 
                                and if(ss.fee_10_info_over_yn = 'Y', ((1 - (w.price / g.goods_sh)) * 100) > ss.fee_10_info, ((1 - (w.price / g.goods_sh)) * 100) >= ss.fee_10_info)
                            , w.recv_amt
                            , 0
                        )) as ord_TG_amt -- 특가
                        , sum(if(w.online_yn = 'N' and g.brand in (select code_id from code where code_kind_cd = 'YP_BRAND'), w.recv_amt, 0)) as ord_YP_amt -- 용품
                        , sum(if(w.online_yn = 'Y', w.recv_amt, 0)) as ord_OL_amt -- 온라인
                    from (
                        (
                            select ww.ord_opt_no, ww.ord_state, ww.ord_state_date, date_format(ww.ord_state_date, '%Y%m') as ymonth
                                , ww.ord_kind, ww.ord_type, oo.pr_code, ww.goods_no, ww.prd_cd, ww.store_cd, 'N' as online_yn
                                , ww.qty, ww.price, (ww.recv_amt * if(ww.ord_state = 30, 1, -1)) as recv_amt
                            from order_opt_wonga ww
                                inner join order_opt oo on oo.ord_opt_no = ww.ord_opt_no
                            where ww.store_cd <> ''
                                and ww.ord_state >= 30
                                and ww.ord_state in (30,60,61)
                                and ww.ord_state_date >= '20230101'
                                and ww.ord_state_date <= '20230331'
                        )
                        union all
                        (
                            select ww.ord_opt_no, ww.ord_state, ww.ord_state_date, date_format(ww.ord_state_date, '%Y%m') as ymonth
                                , ww.ord_kind, ww.ord_type, '' as pr_code, ww.goods_no, ww.prd_cd, ww.store_cd, 'Y' as online_yn
                                , ww.qty, ww.price, (ww.recv_amt * if(ww.ord_state = 30, 1, -1)) as recv_amt
                            from order_opt_wonga ww
                                inner join order_opt oo on oo.ord_opt_no = ww.ord_opt_no
                            where oo.dlv_place_type = 'STORE'
                                and oo.dlv_place_cd <> ''
                                and ww.ord_state >= 30
                                and ww.ord_state in (30,60,61)
                                and ww.ord_state_date >= '20230101'
                                and ww.ord_state_date <= '20230331'
                        )
                    ) w
                        inner join goods g on g.goods_no = w.goods_no
                        inner join (
                            select sss.store_cd, ssg.fee_10_info, ssg.fee_10_info_over_yn
                            from store sss
                                inner join store_grade ssg on ssg.grade_cd = sss.grade_cd
                        ) ss on ss.store_cd = w.store_cd
                    group by w.ymonth, w.store_cd
                ) ord on ord.ymonth = s.ymonth and ord.store_cd = s.store_cd
                left outer join (
                    select ymonth, store_cd, sum(extra_amt) as extra_amt
                    from store_account_extra
                    group by ymonth, store_cd
               ) ae on ae.ymonth = s.ymonth and ae.store_cd = s.store_cd
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
