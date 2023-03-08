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
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준
        $extra_types = collect($this->_get_extra_type_columns())->whereIn('entry_cd', ['P', 'S', 'O'])->groupBy('entry_cd');

        $values = [
            'sdate'         => $sdate,
            'pr_codes'		=> SLib::getCodes('PR_CODE'),
            'store_types'	=> SLib::getStoreTypes(),
            'store_kinds'	=> SLib::getCodes("STORE_KIND"),
            'extra_types'   => $extra_types,
        ];
        return view( Config::get('shop.store.view') . '/account/acc04', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Y-m"));

        $f_month = Carbon::parse($sdate)->format("Ym");
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");
		$nowdate = now()->format("Ymd");

        $store_type = $request->input('store_type', '');
        $store_kind = $request->input('store_kind', '');
		$store_no = $request->input('store_no', []);
        $closed_yn = $request->input('closed_yn', ''); // 마감상태
        $sale_yn = $request->input('sale_yn', 'Y'); // 매출여부

        // 검색조건 필터링
        $where = "";
        if ($store_type != '') $where .= " and s.store_type = '" . Lib::quote($store_type) . "' ";
        if (count($store_no) > 0) $where .= " and s.store_cd in (" . join(",", array_map(function($cd) { return "'$cd'"; }, $store_no)) . ") ";
        if ($store_kind != '') $where .= " and s.store_kind = '" . Lib::quote($store_kind) . "' ";
        if ($closed_yn != '') {
			if ($closed_yn == 'Z') $where .= " and c.closed_yn is null ";
			else $where .= " and c.closed_yn = '" . Lib::quote($closed_yn) . "'";
		}
        if ($sale_yn == 'Y') $where .= " and w.sales_amt > 0 ";
        else if ($sale_yn == 'N') $where .= " and w.sales_amt <= 0 ";

        $pr_codes = SLib::getCodes('PR_CODE'); // 행사코드목록
        $pr_code_query1 = "";
        $pr_code_query2 = "";
        $pr_code_query3 = "";
        $pr_code_query4 = "";
        foreach ($pr_codes->toArray() as $cd) {
            $pr_code_query1 .= ", sum(if(ww.online_yn = 'N' and ww.pr_code = '" . $cd->code_id . "', ww.recv_amt, 0)) as sales_" . $cd->code_id . "_amt";
            $pr_code_query2 .= ", max(if(f.pr_code = '" . $cd->code_id . "', ifnull(f.store_fee, 0), 0)) as sales_" . $cd->code_id . "_fee_rate";
            $pr_code_query3 .= "
                , round(w.sales_" . $cd->code_id . "_amt / 1.1) as sales_" . $cd->code_id . "_amt_except_vat
                , round(w.sales_" . $cd->code_id . "_amt / 1.1 * ifnull(sf.sales_" . $cd->code_id . "_fee_rate, 0) / 100) as sales_" . $cd->code_id . "_fee
            ";
            $pr_code_query4 .= "+ (w.sales_" . $cd->code_id . "_amt / 1.1 * ifnull(sf.sales_" . $cd->code_id . "_fee_rate, 0) / 100)";
        }

        $extra_types = $this->_get_extra_type_columns(); // 기타재반자료타입목록
        $extra_type_query = array_reduce($extra_types, function($a, $c) { 
                return $a . ", sum(if(et.type_cd = '" . $c->type_cd . "', if(et.except_vat_yn = 'Y', ifnull(el.extra_amt, 0) / 1.1, ifnull(el.extra_amt, 0)), 0)) as extra_" . $c->type_cd . "_amt"; 
            }, "");

        $sql = "
            select b.*
                , round(b.sales_profit) as sales_profit
                , round(b.sales_fee) as sales_fee
                , round(b.fee_amt) as fee_amt
                , round(b.sales_profit - b.sales_fee - b.fee_amt - ifnull(b.extra_P_sum, 0) - ifnull(b.extra_O_sum, 0)) as real_profit -- 영업이익
                , round((b.sales_profit - b.sales_fee - b.fee_amt - ifnull(b.extra_P_sum, 0) - ifnull(b.extra_O_sum, 0)) / b.sales_profit_except_M1 * 100, 2) as real_profit_rate -- 영업이익율
            from (
                select a.*
                    , round(a.ord_JS1_amt_except_vat_s) as ord_JS1_amt_except_vat
                    , round(a.ord_JS2_amt_except_vat_s) as ord_JS2_amt_except_vat
                    , round(a.ord_JS3_amt_except_vat_s) as ord_JS3_amt_except_vat
                    , round(a.ord_TG_amt_except_vat_s) as ord_TG_amt_except_vat
                    , round(a.ord_YP_amt_except_vat_s) as ord_YP_amt_except_vat
                    , round(a.ord_OL_amt_except_vat_s) as ord_OL_amt_except_vat
                    , round(a.ord_JS1_amt_except_vat_s * a.fee1 / 100) as fee_amt_JS1
                    , round(a.ord_JS2_amt_except_vat_s * a.fee2 / 100) as fee_amt_JS2
                    , round(a.ord_JS3_amt_except_vat_s * a.fee3 / 100) as fee_amt_JS3
                    , round(a.ord_TG_amt_except_vat_s * a.fee_10 / 100) as fee_amt_TG
                    , round(a.ord_YP_amt_except_vat_s * a.fee_11 / 100) as fee_amt_YP
                    , round(a.ord_OL_amt_except_vat_s * a.fee_12 / 100) as fee_amt_OL
                    , (a.ord_JS1_amt_except_vat_s * a.fee1 / 100
                        + a.ord_JS2_amt_except_vat_s * a.fee2 / 100
                        + a.ord_JS3_amt_except_vat_s * a.fee3 / 100
                        + a.ord_TG_amt_except_vat_s * a.fee_10 / 100
                        + a.ord_YP_amt_except_vat_s * a.fee_11 / 100
                        + a.ord_OL_amt_except_vat_s * a.fee_12 / 100
                    ) as fee_amt
                from (
                    select s.store_cd, s.store_nm, s.store_type, st.code_val as store_type_nm, s.manager_nm, c.closed_yn
                        , w.*, ae.*, sf.*, s.grade_cd, sg.grade_nm, sg.fee1, sg.fee2, sg.fee3, sg.fee_10, sg.fee_11, sg.fee_12
                        , round(w.sales_amt / 1.1) as sales_amt_except_vat
                        , ((w.sales_amt / 1.1) - ifnull(ae.extra_M1_amt, 0)) as sales_profit_except_M1 -- 매출이익(원가제외)
                        , ((w.sales_amt / 1.1) - ifnull(ae.extra_M1_amt, 0) - w.wonga_amt) as sales_profit -- 매출이익
                        -- 판매수수료
                        $pr_code_query3
                        , (0 $pr_code_query4) as sales_fee
                        -- 중간관리자수수료
                        , if(w.ord_JS_amt > sg.amt1, sg.amt1, w.ord_JS_amt) as ord_JS1_amt
                        , if(w.ord_JS_amt > sg.amt2, sg.amt2 - sg.amt1, if(w.ord_JS_amt > sg.amt1, w.ord_JS_amt - sg.amt1, 0)) as ord_JS2_amt
                        , if(w.ord_JS_amt > sg.amt2, w.ord_JS_amt - sg.amt2, 0) as ord_JS3_amt
                        , if(w.ord_JS_amt > sg.amt1, sg.amt1, w.ord_JS_amt) / 1.1 as ord_JS1_amt_except_vat_s
                        , if(w.ord_JS_amt > sg.amt2, sg.amt2 - sg.amt1, if(w.ord_JS_amt > sg.amt1, w.ord_JS_amt - sg.amt1, 0)) / 1.1 as ord_JS2_amt_except_vat_s
                        , if(w.ord_JS_amt > sg.amt2, w.ord_JS_amt - sg.amt2, 0) / 1.1 as ord_JS3_amt_except_vat_s
                        , w.ord_TG_amt / 1.1 as ord_TG_amt_except_vat_s
                        , w.ord_YP_amt / 1.1 as ord_YP_amt_except_vat_s
                        , w.ord_OL_amt / 1.1 as ord_OL_amt_except_vat_s
                    from store s
                        left outer join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = s.store_type
                        left outer join store_account_closed c on sday = '$f_sdate' and eday = '$f_edate' and c.store_cd = s.store_cd
                        left outer join (
                            select grade_cd, name as grade_nm
                                , fee1, round(amt1 * 1.1) as amt1, fee2, round(amt2 * 1.1) as amt2, fee3
                                , fee_10, fee_11, fee_12, fee_10_info, fee_10_info_over_yn
                            from store_grade
                            where concat(replace(sdate, '-', ''), '01') <= '$nowdate' and concat(replace(edate, '-', ''), '31') >= '$nowdate'
                        ) sg on sg.grade_cd = s.grade_cd
                        left outer join (
                            select f.store_cd as sf_store_cd
                                $pr_code_query2
                            from store_fee f
                            where f.use_yn = 'Y' and f.idx in (select max(idx) from store_fee where store_cd = f.store_cd group by pr_code)
                            group by f.store_cd
                        ) sf on sf.sf_store_cd = s.store_cd
                        left outer join (
                            select ww.store_cd as ww_store_cd
                                $pr_code_query1
                                , sum(if(ww.online_yn = 'N', ww.recv_amt, 0)) as sales_amt -- 매출합계
                                , sum(if(ww.online_yn = 'N', ww.wonga, 0)) as wonga_amt -- 원가합계
                                , sum(if(
                                    ww.online_yn = 'N' and g.brand not in (select code_id from code where code_kind_cd = 'YP_BRAND') 
                                    and if(sg.fee_10_info_over_yn = 'Y', ((1 - (ww.price / g.goods_sh)) * 100) <= sg.fee_10_info, ((1 - (ww.price / g.goods_sh)) * 100) < sg.fee_10_info)
                                    , ww.recv_amt, 0
                                )) as ord_JS_amt -- 정상매출(중간관리자)
                                , sum(if(
                                    ww.online_yn = 'N' and g.brand not in (select code_id from code where code_kind_cd = 'YP_BRAND') 
                                    and if(sg.fee_10_info_over_yn = 'Y', ((1 - (ww.price / g.goods_sh)) * 100) > sg.fee_10_info, ((1 - (ww.price / g.goods_sh)) * 100) >= sg.fee_10_info)
                                    , ww.recv_amt, 0
                                )) as ord_TG_amt -- 특가매출(중간관리자)
                                , sum(if(ww.online_yn = 'N' and g.brand in (select code_id from code where code_kind_cd = 'YP_BRAND'), ww.recv_amt, 0)) as ord_YP_amt -- 용품매출(중간관리자)
                                , sum(if(ww.online_yn = 'Y', ww.recv_amt, 0)) as ord_OL_amt -- 특가(온라인)매출(중간관리자)
                            from (
                                (
                                    select w.store_cd, w.goods_no, w.prd_cd, o.pr_code, 'N' as online_yn
                                        , w.qty, w.price, (w.qty * w.wonga * if(w.ord_state = 30, 1, -1)) as wonga, (w.recv_amt * if(w.ord_state = 30, 1, -1)) as recv_amt
                                    from order_opt_wonga w
                                        inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                                    where w.ord_state_date >= '$f_sdate' and w.ord_state_date <= '$f_edate'
                                        and w.ord_state in (30,60,61)
                                )
                                union all
                                (
                                    select o.dlv_place_cd as store_cd, w.goods_no, w.prd_cd, '' as pr_code, 'Y' as online_yn
                                        , w.qty, w.price, (w.qty * w.wonga * if(w.ord_state = 30, 1, -1)) as wonga, (w.recv_amt * if(w.ord_state = 30, 1, -1)) as recv_amt
                                    from order_opt_wonga w
                                        inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                                    where w.ord_state_date >= '$f_sdate' and w.ord_state_date <= '$f_edate'
                                        and w.ord_state in (30,60,61)
                                        and o.dlv_place_type = 'STORE' and o.dlv_place_cd <> ''
                                )
                            ) ww
                                inner join goods g on g.goods_no = ww.goods_no
                                inner join store s on s.store_cd = ww.store_cd
                                inner join store_grade sg on sg.grade_cd = s.grade_cd
                            group by ww.store_cd
                        ) w on w.ww_store_cd = s.store_cd
                        left outer join (
                            select e.store_cd as ae_store_cd
                                $extra_type_query
                                , sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_sum
                                , (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
                                    + sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
                                    + sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
                                ) as extra_S_sum
                                , sum(if(et.entry_cd = 'O' and el.type <> 'O2', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_O_sum
                            from store_account_extra e
                                inner join store_account_extra_list el on el.ext_idx = e.idx
                                inner join store_account_extra_type et on et.type_cd = el.type
                            where e.ymonth = '$f_month'
                            group by e.store_cd
                        ) ae on ae.ae_store_cd = s.store_cd
                    where s.account_yn = 'Y' $where
                    order by s.store_cd asc
                ) a
            ) b
        ";
        $result = DB::select($sql);

        return response()->json([
            'code'	=> 200,
            'head'	=> array(
                'total'	=> count($result),
                'date' => Carbon::parse($sdate)->format("Y년 m월"),
            ),
            'body' => $result
        ]);
    }

    /** 기타재반자료 컬럼적용 리스트 반환 */
    private function _get_extra_type_columns()
    {
        $extra_type_sql = "
            select t.type_cd, t.type_nm, ifnull(t.entry_cd, 'S') as entry_cd, tt.type_nm as entry_nm, t.payer
                , t.except_vat_yn, t.total_include_yn, t.has_child_yn, t.use_yn, t.seq, t.rt
            from store_account_extra_type t
                left outer join store_account_extra_type tt on tt.type_cd = t.entry_cd
            where t.use_yn = 'Y' and t.has_child_yn = 'N' and (t.total_include_yn = 'Y' or t.type_cd = 'O1') and t.type_cd <> 'O2'
            order by t.payer is null desc, t.payer desc, t.entry_cd is null asc, t.seq
        ";
        return DB::select($extra_type_sql);
    }

}
