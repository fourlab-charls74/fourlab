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

class acc06Controller extends Controller
{
    public function index(Request $request) 
	{
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준

        $values = [
            'sdate'         => $sdate,
            'store_types'	=> SLib::getStoreTypes(),
            'store_kinds'	=> SLib::getCodes("STORE_KIND"),
            'pr_codes'      => $this->_get_prcodes()
        ];

        return view( Config::get('shop.store.view') . '/account/acc06', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->format("Y-m"));

		$f_month = Carbon::parse($sdate)->format("Ym");
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");
		$nowdate = now()->format("Ymd");

        $store_type = $request->input('store_type', '');
        $store_kind = $request->input('store_kind', '');
        $store_cd = $request->input('store_cd', '');
        $closed_yn = $request->input('closed_yn', '');

		// 검색조건 필터링
		$where = "";
		if ($store_type != '') $where .= " and s.store_type = '" . Lib::quote($store_type) . "'";
        if ($store_kind != '') $where .= " and s.store_kind = '". Lib::quote($store_kind) . "'";
        if ($store_cd != '') $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";
		if ($closed_yn != '') {
			if ($closed_yn == 'Z') $where .= " and c.closed_yn is null ";
			else $where .= " and c.closed_yn = '" . Lib::quote($closed_yn) . "'";
		}
		
        // 행사코드별 매출구분
        $pr_codes = $this->_get_prcodes();
        $pr_codes_query = "";
        foreach ($pr_codes as $item) {
            $key = $item->code_id;
            $pr_codes_query .= ", sum(if(ww.online_yn = 'N' and ww.pr_code = '$key', ww.recv_amt, 0)) as sales_" . $key . "_amt";
        }

		$sql = "
			select a.*
				, round(a.ord_JS1_amt_except_vat) as ord_JS1_amt_except_vat
				, round(a.ord_JS2_amt_except_vat) as ord_JS2_amt_except_vat
				, round(a.ord_JS3_amt_except_vat) as ord_JS3_amt_except_vat
				, round(a.ord_TG_amt_except_vat) as ord_TG_amt_except_vat
				, round(a.ord_YP_amt_except_vat) as ord_YP_amt_except_vat
				, round(a.ord_OL_amt_except_vat) as ord_OL_amt_except_vat
				, round(a.ord_JS1_amt_except_vat * a.fee1 / 100) as fee_amt_JS1
				, round(a.ord_JS2_amt_except_vat * a.fee2 / 100) as fee_amt_JS2
				, round(a.ord_JS3_amt_except_vat * a.fee3 / 100) as fee_amt_JS3
				, round(a.ord_TG_amt_except_vat * a.fee_10 / 100) as fee_amt_TG
				, round(a.ord_YP_amt_except_vat * a.fee_11 / 100) as fee_amt_YP
				, round(a.ord_OL_amt_except_vat * a.fee_12 / 100) as fee_amt_OL
				, round(a.ord_JS1_amt_except_vat * a.fee1 / 100
					+ a.ord_JS2_amt_except_vat * a.fee2 / 100
					+ a.ord_JS3_amt_except_vat * a.fee3 / 100
					+ a.ord_TG_amt_except_vat * a.fee_10 / 100
					+ a.ord_YP_amt_except_vat * a.fee_11 / 100
					+ a.ord_OL_amt_except_vat * a.fee_12 / 100
				) as fee_amt
				, round(a.ord_JS1_amt_except_vat * a.fee1 / 100
					+ a.ord_JS2_amt_except_vat * a.fee2 / 100
					+ a.ord_JS3_amt_except_vat * a.fee3 / 100
					+ a.ord_TG_amt_except_vat * a.fee_10 / 100
					+ a.ord_YP_amt_except_vat * a.fee_11 / 100
					+ a.ord_OL_amt_except_vat * a.fee_12 / 100
					+ a.extra_amt
				) as total_fee_amt
			from (
				select s.store_cd, s.store_nm, s.store_type, st.code_val as store_type_nm, s.manager_nm, c.closed_yn
					, w.*, ae.*, s.grade_cd, sg.grade_nm, sg.fee1, sg.fee2, sg.fee3, sg.fee_10, sg.fee_11, sg.fee_12
					, round(w.sales_amt / 1.1) as sales_amt_except_vat
					, if(w.ord_JS_amt > sg.amt1, sg.amt1, w.ord_JS_amt) as ord_JS1_amt
					, if(w.ord_JS_amt > sg.amt2, sg.amt2 - sg.amt1, if(w.ord_JS_amt > sg.amt1, w.ord_JS_amt - sg.amt1, 0)) as ord_JS2_amt
					, if(w.ord_JS_amt > sg.amt2, w.ord_JS_amt - sg.amt2, 0) as ord_JS3_amt
					, if(w.ord_JS_amt > sg.amt1, sg.amt1, w.ord_JS_amt) / 1.1 as ord_JS1_amt_except_vat
					, if(w.ord_JS_amt > sg.amt2, sg.amt2 - sg.amt1, if(w.ord_JS_amt > sg.amt1, w.ord_JS_amt - sg.amt1, 0)) / 1.1 as ord_JS2_amt_except_vat
					, if(w.ord_JS_amt > sg.amt2, w.ord_JS_amt - sg.amt2, 0) / 1.1 as ord_JS3_amt_except_vat
					, w.ord_TG_amt / 1.1 as ord_TG_amt_except_vat
					, w.ord_YP_amt / 1.1 as ord_YP_amt_except_vat
					, w.ord_OL_amt / 1.1 as ord_OL_amt_except_vat
					, (ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as extra_amt
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
						select ww.store_cd as ww_store_cd
							$pr_codes_query
							, sum(if(ww.online_yn = 'N', ww.recv_amt, 0)) as sales_amt -- 매출합계
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
									, w.qty, w.price, (w.recv_amt * if(w.ord_state = 30, 1, -1)) as recv_amt
								from order_opt_wonga w
									inner join order_opt o on o.ord_opt_no = w.ord_opt_no
								where w.ord_state_date >= '$f_sdate' and w.ord_state_date <= '$f_edate'
									and w.ord_state in (30,60,61)
							)
							union all
							(
								select o.dlv_place_cd as store_cd, w.goods_no, w.prd_cd, '' as pr_code, 'Y' as online_yn
									, w.qty, w.price, (w.recv_amt * if(w.ord_state = 30, 1, -1)) as recv_amt
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
							, sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_amt
							, (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
								+ sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
								+ sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
							) * -1 as extra_S_amt
							, sum(if(et.entry_cd = 'O' and el.type <> 'O1' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_C_amt
						from store_account_extra e
							inner join store_account_extra_list el on el.ext_idx = e.idx
							inner join store_account_extra_type et on et.type_cd = el.type
						where e.ymonth = '$f_month'
						group by e.store_cd
					) ae on ae.ae_store_cd = s.store_cd
				where s.account_yn = 'Y' $where
				order by s.store_cd asc
			) a
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

	private function _get_prcodes()
	{
		$sql = "
			select code_id, code_val 
            from `code` 
            where code_kind_cd = 'PR_CODE'
            order by code_seq asc
        ";
        return DB::select($sql);
	}

	/** 상세판매내역 팝업 */
    public function show($store_cd, $sdate)
	{
		$store_nm	= "";
		$acc_idx	= "";
		$closed_yn	= "";

        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

		if( $store_cd != "" ) $store_nm = DB::table('store')->where('store_cd', $store_cd)->value('store_nm');

		$sql = "
			select idx, closed_yn
			from store_account_closed
			where store_cd = :store_cd and sday = :sday and eday = :eday
		";
		$row = DB::selectOne($sql, ['store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);

		if (!empty($row)) {
			$acc_idx = $row->idx;
			$closed_yn = $row->closed_yn;
		}
		
		$values = [
			'sdate'			 => $sdate,
			'store_cd'		 => $store_cd,
			'store_nm'		 => $store_nm,
			'ord_states'	 => SLib::getOrdStates(),
			'clm_states'	 => SLib::getCodes('G_CLM_STATE'),
			'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'),
			'ord_types'		 => SLib::getCodes('G_ORD_TYPE'),
			'acc_idx'		 => $acc_idx,
			'closed_yn'		 => $closed_yn,
		];

		return view( Config::get('shop.store.view') . '/account/acc06_show', $values );
	}

	/** 상세판매내역 조회 */
	public function show_search(Request $request)
	{
        $sdate = $request->input('sdate', now()->format("Y-m"));
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

        $store_cd = $request->input('store_no');
        $ord_state = $request->input('ord_state', '');
        $clm_state = $request->input('clm_state', '');
        $stat_pay_type = $request->input('stat_pay_type', '');
        $not_complex = $request->input('not_complex', '');
        $ord_type = $request->input('ord_type', '');

        $where = "";
        if ($ord_state != "") $where .= " and w.ord_state = '$ord_state' ";
        if ($clm_state != "") {
			if ($clm_state == "90") $where .= " and o.clm_state in ('$clm_state', '') ";
			else $where .= " and o.clm_state = '$clm_state' ";
		}
        if ($ord_type != "") $where .= " and o.ord_type = '$ord_type' ";
        if ($stat_pay_type != "") {
            if ($not_complex == "Y") {
                $where .= " and p.pay_type = '$stat_pay_type' ";
            } else {
                $where .= " and (( p.pay_type & $stat_pay_type ) = $stat_pay_type) ";
            }
        }

		$sql = "
			select a.*
				, act.code_val as sale_type
				, odt.code_val as ord_type_nm
				, prc.code_val as pr_code_nm
				, pyt.code_val as pay_type_nm
				, ods.code_val as ord_state_nm
				, cls.code_val as clm_state_nm
			from (
				select o.ord_no, w.ord_opt_no
					, w.ord_state_date, date_format(w.ord_state_date, '%Y-%m-%d') as state_date, if(w.ord_state = 30, date_format(o.ord_date, '%Y-%m-%d'), '') as ord_date
					, w.ord_state, w.ord_state as clm_state, date_format(o.dlv_end_date, '%Y-%m-%d') as dlv_end_date
					, if(w.ord_state in (60,61), (
						select date_format(max(end_date),'%Y-%m-%d') as clm_end_date 
						from claim
						where ord_opt_no = w.ord_opt_no
					), '') as clm_end_date
					, w.store_cd, w.prd_cd, w.goods_no, w.goods_opt, w.qty, w.price
					, w.ord_kind, w.ord_type
					, if(w.ord_state = 30, (w.qty * w.price), 0) as sale_amt
					, if(w.ord_state = 30, 0, (w.qty * w.price * -1)) as clm_amt
					-- , ((g.goods_sh - abs(w.price)) * w.qty * -1) as dc_amt
					, (w.dc_apply_amt * if(w.ord_state = 30 and w.recv_amt > 0, -1, 1)) as dc_amt
					, (w.coupon_apply_amt * if(w.ord_state = 30, -1, 1)) as coupon_amt
					, (w.recv_amt * if(w.ord_state = 30, 1, -1)) as recv_amt
					, o.pr_code, s.store_nm, p.pay_type, m.user_nm
					, g.style_no, g.goods_nm, g.tax_yn, g.goods_sh
					, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
					, if((select count(*) from order_opt where ord_no = o.ord_no) > 1, 'Y', '') as multi_order -- 복수주문여부
				from order_opt_wonga w
					inner join order_opt o on o.ord_opt_no = w.ord_opt_no
					inner join order_mst m on m.ord_no = o.ord_no
					inner join payment p on p.ord_no = o.ord_no
					inner join store s on s.store_cd = w.store_cd
					inner join goods g on g.goods_no = w.goods_no
					left outer join product_code pc on pc.prd_cd = w.prd_cd
				where w.ord_state_date >= :sdate and w.ord_state_date <= :edate
					and w.ord_state in (30,60,61) and w.store_cd = :store_cd
					$where
				order by w.ord_state_date, w.ord_opt_no, w.ord_state
			) a
				left outer join code act on act.code_kind_cd = 'G_ACC_TYPE' and act.code_id = a.ord_state
				left outer join code odt on odt.code_kind_cd = 'G_ORD_TYPE' and odt.code_id = a.ord_type
				left outer join code pyt on pyt.code_kind_cd = 'G_PAY_TYPE' and pyt.code_id = a.pay_type
				left outer join code ods on ods.code_kind_cd = 'G_ORD_STATE' and ods.code_id = a.ord_state
				left outer join code cls on cls.code_kind_cd = 'G_CLM_STATE' and cls.code_id = a.clm_state
				left outer join code prc on prc.code_kind_cd = 'PR_CODE' and prc.code_id = a.pr_code
		";
		
		$result = DB::select($sql, ['store_cd' => $store_cd, 'sdate' => $f_sdate, 'edate' => $f_edate]);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total"	=> count($result),
			),
			"body" => $result
		]);
	}

	/** 마감추가 */
	public function closed(Request $request)
	{
		$store_cd = $request->input('store_cd');

		$sdate = $request->input('sdate', now()->format("Y-m"));
		$nowdate = now()->format("Ymd");
        $ymonth = Carbon::parse($sdate)->format("Ym");
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

		$admin_id	= auth('head')->user()->id;
		$admin_nm = auth('head')->user()->name;
        $code = "000";
        $msg = "";

		/** 
			<에러코드 구분>
			000 : 성공
			100 : 부정확한 요청
			110 : 마감처리된 내역
			999 : 자료등록 시 오류
		*/

		if (strlen($f_sdate) != 8 || strlen($f_edate) != 8 || $store_cd == "") {
			return response()->json(["code"	=> "100", "msg"	=> "판매일자/매장정보가 부정확한 값입니다."]);
		}

		$account_yn = DB::table('store')->where('store_cd', $store_cd)->value('account_yn');
		if ($account_yn != 'Y') {
			return response()->json(["code"	=> "100", "msg"	=> "해당 매장은 정산관리가 허용되지 않은 매장입니다."]);
		}

		$sql = "
			select count(*) as cnt 
			from store_account_closed
			where store_cd = :store_cd and sday = :sday and eday = :eday 
			limit 0,1
		";
		$row = DB::selectOne($sql, ['store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);
		$cnt = $row->cnt;

		if ($cnt > 0) {
			return response()->json(["code"	=> "110", "msg"	=> "이미 마감처리된 내역이 존재합니다."]);
		}

		try {
			// start transaction
			DB::beginTransaction();

			// 1. store_account_closed 에 새로운 마감 추가
			$acc_idx = DB::table('store_account_closed')->insertGetId([
				'store_cd' => $store_cd,
				'sday' => $f_sdate,
				'eday' => $f_edate,
				'closed_yn' => 'N',
				'admin_id' => $admin_id,
				'admin_nm' => $admin_nm,
				'rt' => now(),
			]);

			// 2. store_account_closed_list 에 1에서 추가한 마감의 상세판매내역정보를 추가
			$sql = "
				insert into store_account_closed_list
				(
					acc_idx, type, sale_type, ord_opt_no, state_date, qty
					, sale_amt, clm_amt, dc_amt, coupon_amt, allot_amt, dlv_amt
					, sale_net_taxation_amt, sale_net_taxfree_amt, sale_net_amt
					, tax_amt, fee_ratio, fee, memo
					, sale_fee, sale_clm_amt, etc_amt, fee_dc_amt, fee_net, acc_amt
				)
				select :acc_idx as acc_idx
					, b.type, b.sale_type, b.ord_opt_no, b.state_date, b.qty
					, b.sale_amt, b.clm_amt, b.dc_amt, b.coupon_amt, b.allot_amt, b.dlv_amt
					, if(b.tax_yn = 'Y', b.recv_amt, 0) as sale_net_taxation_amt
					, if(b.tax_yn = 'N', b.recv_amt, 0) as sale_net_taxfree_amt
					, b.recv_amt as sale_net_amt
					, (b.recv_amt - (b.recv_amt / 1.1)) as tax_amt
					, b.fee_ratio
					, (b.recv_amt / 1.1 * b.fee_ratio / 100) as fee
					, '' as memo
					, 0 as sale_fee
					, 0 as sale_clm_amt
					, 0 as etc_amt
					, 0 as fee_dc_amt
					, 0 as fee_net
					, 0 as acc_amt
				from (
					select a.ord_state as type
						, a.sale_type as sale_type
						, a.ord_opt_no as ord_opt_no
						, a.ord_state_date as state_date
						, a.qty as qty
						, a.sale_price as sale_price
						, if(a.ord_state = 30, a.sale_price, 0) as sale_amt
						, if(a.ord_state = 30, 0, a.sale_price) as clm_amt
						-- , ((a.goods_sh - abs(a.price)) * a.qty * -1) as dc_amt
						, (a.dc_apply_amt * if(a.ord_state = 30 and a.recv_amt > 0, -1, 1)) as dc_amt
						, (a.coupon_apply_amt * if(a.ord_state = 30, -1, 1)) as coupon_amt
						, (a.recv_amt * if(a.ord_state = 30, 1, -1)) as recv_amt
						, 0 as allot_amt -- (본사부담)쿠폰금액 추후 수정필요
						, a.dlv_amt as dlv_amt
						, a.tax_yn
						, if(a.sale_type = 'JS', 0,
							if(a.sale_type = 'TG', a.fee_10,
								if(a.sale_type = 'YP', a.fee_11,
									if(a.sale_type = 'OL', a.fee_12, 0)
								)
							)
						) as fee_ratio
					from (
						select w.ord_opt_no, w.goods_no, w.qty, w.price, w.recv_amt, w.sale_price, w.ord_state
							, w.ord_state_date, w.prd_cd, w.store_cd
							, w.dc_apply_amt, w.coupon_apply_amt, w.dlv_amt
							, if(w.online_yn = 'Y', 'OL'
								, if(g.brand in (select code_id from code where code_kind_cd = 'YP_BRAND'), 'YP'
									, if(if(s.fee_10_info_over_yn = 'Y', ((1 - (w.price / g.goods_sh)) * 100) > s.fee_10_info, ((1 - (w.price / g.goods_sh)) * 100) >= s.fee_10_info), 'TG'
										, 'JS'
									)
								)
							) as sale_type
							, g.goods_sh, g.tax_yn, s.fee_10, s.fee_11, s.fee_12
						from (
							(
								select ww.ord_opt_no, ww.goods_no, ww.qty, ww.price, ww.recv_amt, ww.ord_state
									, (ww.qty * ww.price * if(ww.ord_state = 30, 1, -1)) as sale_price
									, ww.ord_state_date, ww.prd_cd, ww.store_cd, 'N' as online_yn
									, ww.dc_apply_amt, ww.coupon_apply_amt, ww.dlv_amt
								from order_opt_wonga ww
								where ww.ord_state_date >= :sdate1 and ww.ord_state_date <= :edate1
									and ww.ord_state in (30,60,61) and ww.store_cd = :store_cd1
							)
							union all
							(
								select ww.ord_opt_no, ww.goods_no, ww.qty, ww.price, ww.recv_amt, ww.ord_state
									, (ww.qty * ww.price * if(ww.ord_state = 30, 1, -1)) as sale_price
									, ww.ord_state_date, ww.prd_cd, ooo.dlv_place_cd as store_cd, 'Y' as online_yn
									, 0 as dc_apply_amt, 0 as coupon_apply_amt, ww.dlv_amt
								from order_opt_wonga ww
									inner join order_opt ooo on ooo.ord_opt_no = ww.ord_opt_no
								where ww.ord_state_date >= :sdate2 and ww.ord_state_date <= :edate2
									and ww.ord_state = 30
									and ooo.dlv_place_type = 'STORE' and ooo.dlv_place_cd = :store_cd2
							)
						) w
							inner join goods g on g.goods_no = w.goods_no
							inner join (
								select ss.store_cd, sg.*
								from store ss
									inner join (
										select grade_cd, name as grade_nm, fee_10, fee_11, fee_12, fee_10_info, fee_10_info_over_yn
										from store_grade
										where concat(replace(sdate, '-', ''), '01') <= :nowdate1
											and concat(replace(edate, '-', ''), '31') >= :nowdate2
									) sg on sg.grade_cd = ss.grade_cd
							) s on s.store_cd = w.store_cd
						order by w.ord_state_date, w.ord_opt_no, w.ord_state
					) a
				) b
			";
			DB::insert($sql
				, [
					'acc_idx' => $acc_idx, 'nowdate1' => $nowdate, 'nowdate2' => $nowdate
					, 'store_cd1' => $store_cd, 'sdate1' => $f_sdate, 'edate1' => $f_edate
					, 'store_cd2' => $store_cd, 'sdate2' => $f_sdate, 'edate2' => $f_edate
				]);

			// 3. store_account_closed 에 상세판매내역의 총합계정보 업데이트
			$sql = "
				update store_account_closed as c
					, (
						select b.*
							, (b.fee_JS1 + b.fee_JS2 + b.fee_JS3 + b.fee_TG + b.fee_YP + b.fee_OL) as fee
							, (b.fee_JS1 + b.fee_JS2 + b.fee_JS3 + b.fee_TG + b.fee_YP + b.fee_OL + b.extra_amt) as fee_net
						from (
							select
								c.acc_idx
								, sum(if(c.sale_type = 'OL', 0, c.sale_amt)) as sale_amt
								, sum(if(c.sale_type = 'OL', 0, c.clm_amt)) as clm_amt
								, sum(if(c.sale_type = 'OL', 0, c.dc_amt)) as dc_amt
								, sum(if(c.sale_type = 'OL', 0, c.coupon_amt)) as coupon_amt
								, sum(if(c.sale_type = 'OL', 0, c.allot_amt)) as allot_amt
								, sum(c.dlv_amt) as dlv_amt
								, sum(if(c.sale_type = 'OL', 0, c.sale_net_taxation_amt)) as sale_net_taxation_amt
								, sum(if(c.sale_type = 'OL', 0, c.sale_net_taxfree_amt)) as sale_net_taxfree_amt
								, sum(if(c.sale_type = 'OL', 0, c.sale_net_amt)) as sale_net_amt
								, round(sum(if(c.sale_type = 'OL', 0, c.sale_net_amt)) / 11) as tax_amt
								, c.fee1 as fee_rate_JS1
								, round(
									if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > c.amt1
										, c.amt1
										, sum(if(c.sale_type = 'JS', c.sale_net_amt, 0))
									) * c.fee1 / 100 / 1.1
								) as fee_JS1
								, c.fee2 as fee_rate_JS2
								, round(
									if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > c.amt2
										, c.amt2 - c.amt1
										, if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > c.amt1
											, sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) - c.amt1
											, 0
										)
									) * c.fee2 / 100 / 1.1
								) as fee_JS2
								, c.fee3 as fee_rate_JS3
								, round(
									if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > c.amt2
										, sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) - c.amt2
										, 0
									) * c.fee3 / 100 / 1.1
								) as fee_JS3
								, c.fee_10 as fee_rate_TG
								, round(sum(if(c.sale_type = 'TG', c.sale_net_amt, 0)) * c.fee_10 / 100 / 1.1) as fee_TG
								, c.fee_11 as fee_rate_YP
								, round(sum(if(c.sale_type = 'YP', c.sale_net_amt, 0)) * c.fee_11 / 100 / 1.1) as fee_YP
								, c.fee_12 as fee_rate_OL
								, round(sum(if(c.sale_type = 'OL', c.sale_net_amt, 0)) * c.fee_12 / 100 / 1.1) as fee_OL
								, (ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as extra_amt
							from (
								select cl.acc_idx, cl.type, cl.sale_type
									, cl.qty, cl.sale_amt, cl.clm_amt, cl.dc_amt, cl.coupon_amt, cl.allot_amt, cl.dlv_amt
									, cl.sale_net_taxation_amt, cl.sale_net_taxfree_amt, cl.sale_net_amt, cl.fee
									, s.store_cd, sg.*
								from store_account_closed_list cl
									inner join store_account_closed cc on cc.idx = cl.acc_idx
									inner join store s on s.store_cd = cc.store_cd
									inner join (
										select grade_cd, round(amt1 * 1.1) as amt1, fee1, round(amt2 * 1.1) as amt2, fee2, fee3, fee_10, fee_11, fee_12, fee_10_info, fee_10_info_over_yn 
										from store_grade
										where concat(replace(sdate, '-', ''), '01') <= :nowdate1
											and concat(replace(edate, '-', ''), '31') >= :nowdate2
									) sg on sg.grade_cd = s.grade_cd
								where cl.acc_idx = :acc_idx
							) c
								left outer join (
									select e.store_cd as ae_store_cd
										, sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_amt
										, (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
											+ sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
											+ sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
										) * -1 as extra_S_amt
										, sum(if(et.entry_cd = 'O' and el.type <> 'O1' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_C_amt
									from store_account_extra e
										inner join store_account_extra_list el on el.ext_idx = e.idx
										inner join store_account_extra_type et on et.type_cd = el.type
									where e.ymonth = :ymonth
									group by e.store_cd
								) ae on ae.ae_store_cd = c.store_cd
						) b
					) as a
				set c.sale_amt = a.sale_amt
					, c.clm_amt = a.clm_amt
					, c.dc_amt = a.dc_amt
					, c.coupon_amt = a.coupon_amt
					, c.allot_amt = a.allot_amt
					, c.dlv_amt = a.dlv_amt
					, c.sale_net_taxation_amt = a.sale_net_taxation_amt
					, c.sale_net_taxfree_amt = a.sale_net_taxfree_amt
					, c.sale_net_amt = a.sale_net_amt
					, c.tax_amt = a.tax_amt
					, c.fee_rate_JS1 = a.fee_rate_JS1
					, c.fee_JS1 = a.fee_JS1
					, c.fee_rate_JS2 = a.fee_rate_JS2
					, c.fee_JS2 = a.fee_JS2
					, c.fee_rate_JS3 = a.fee_rate_JS3
					, c.fee_JS3 = a.fee_JS3
					, c.fee_rate_TG = a.fee_rate_TG
					, c.fee_TG = a.fee_TG
					, c.fee_rate_YP = a.fee_rate_YP
					, c.fee_YP = a.fee_YP
					, c.fee_rate_OL = a.fee_rate_OL
					, c.fee_OL = a.fee_OL
					, c.fee = a.fee
					, c.extra_amt = a.extra_amt
					, c.fee_net = a.fee_net
					, c.ut = now()
				where c.idx = a.acc_idx
			";
			DB::update($sql, ['acc_idx' => $acc_idx, 'ymonth' => $ymonth, 'nowdate1' => $nowdate, 'nowdate2' => $nowdate]);

			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			$code = "999";
			$msg = $e->getmessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	/** 특약(온라인) 상세판매내역 팝업 */
	public function show_online(Request $request)
	{
		$sdate = $request->input('sdate', now()->format('Y-m'));
		$store_cd = $request->input('store_cd', '');
		$store_nm = '';

		if ($store_cd != '') $store_nm = DB::table('store')->where('store_cd', $store_cd)->value('store_nm');

		$values = [
			'sdate' => $sdate,
			'store_cd' => $store_cd,
			'store_nm' => $store_nm,
		];
		return view( Config::get('shop.store.view') . '/account/acc06_online', $values );
	}

	/** 특약(온라인) 상세판매내역 조회 */
	public function search_online(Request $request)
	{
		$sdate = $request->input('sdate', now()->format("Y-m"));
        $store_cd = $request->input('store_no', '');

        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

		$sql = "
			select a.*
				, act.code_val as sale_type
				, odt.code_val as ord_type_nm
				, prc.code_val as pr_code_nm
				, pyt.code_val as pay_type_nm
				, ods.code_val as ord_state_nm
				, com.com_nm as sale_place_nm
			from (
				select o.ord_no, w.ord_opt_no
					, w.ord_state_date, date_format(w.ord_state_date, '%Y-%m-%d') as state_date, if(w.ord_state = 30, date_format(o.ord_date, '%Y-%m-%d'), '') as ord_date
					, w.ord_state, date_format(o.dlv_end_date, '%Y-%m-%d') as dlv_end_date
					, s.store_cd, w.prd_cd, w.goods_no, w.goods_opt, w.qty, w.price
					, w.ord_kind, w.ord_type
					, if(w.ord_state = 30, (w.qty * w.price), 0) as sale_amt
					-- , ((g.goods_sh - abs(w.price)) * w.qty * -1) as dc_amt
					, (w.dc_apply_amt * if(w.ord_state = 30 and w.recv_amt > 0, -1, 1)) as dc_amt
					, (w.coupon_apply_amt * if(w.ord_state = 30, -1, 1)) as coupon_amt
					, (w.recv_amt * if(w.ord_state = 30, 1, -1)) as recv_amt
					, o.pr_code, o.sale_place, s.store_nm, p.pay_type, m.user_nm
					, g.style_no, g.goods_nm, g.tax_yn, g.goods_sh
					, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
					, if((select count(*) from order_opt where ord_no = o.ord_no) > 1, 'Y', '') as multi_order -- 복수주문여부
				from order_opt_wonga w
					inner join order_opt o on o.ord_opt_no = w.ord_opt_no
					inner join order_mst m on m.ord_no = o.ord_no
					inner join payment p on p.ord_no = o.ord_no
					inner join store s on s.store_cd = o.dlv_place_cd
					inner join goods g on g.goods_no = w.goods_no
					left outer join product_code pc on pc.prd_cd = w.prd_cd
				where w.ord_state_date >= :sdate and w.ord_state_date <= :edate
					and w.ord_state = 30 and o.dlv_place_type = 'STORE' and o.dlv_place_cd = :store_cd
				order by w.ord_state_date, w.ord_opt_no, w.ord_state
			) a
				left outer join code act on act.code_kind_cd = 'G_ACC_TYPE' and act.code_id = a.ord_state
				left outer join code odt on odt.code_kind_cd = 'G_ORD_TYPE' and odt.code_id = a.ord_type
				left outer join code pyt on pyt.code_kind_cd = 'G_PAY_TYPE' and pyt.code_id = a.pay_type
				left outer join code ods on ods.code_kind_cd = 'G_ORD_STATE' and ods.code_id = a.ord_state
				left outer join code prc on prc.code_kind_cd = 'PR_CODE' and prc.code_id = a.pr_code
				left outer join company com on com.com_id = a.sale_place
		";

		if ($store_cd != '') {
			$result = DB::select($sql, ['store_cd' => $store_cd, 'sdate' => $f_sdate, 'edate' => $f_edate]);
		} else {
			$result = [];
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"	=> count($result),
			),
			"body" => $result
		]);
	}
}