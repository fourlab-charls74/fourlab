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

        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

        $store_type = $request->input('store_type', "");
        $store_kind = $request->input('store_kind', "");
        $store_cd = $request->input('store_cd', "");
		
		$pr_codes = $this->_get_prcodes();
		$pr_codes = array_map(function($c) { return $c->code_id; }, $pr_codes);

		$sql = "
			select a.*
				, (a.fee_amt_JS1 + a.fee_amt_JS2 + a.fee_amt_JS3 + a.fee_amt_TG + a.fee_amt_YP + a.fee_amt_OL) as fee_amt
			from (
				select w.*, sg.*
					, s.store_cd, s.store_nm, s.manager_nm
					, s.store_type, st.code_val as store_type_nm
					, round(
						if(w.ord_JS_amt > sg.amt1
							, sg.amt1 * (sg.fee1 / 100)
							, w.ord_JS_amt * (sg.fee1 / 100)
						)
					) as fee_amt_JS1
					, round(
						if(w.ord_JS_amt > sg.amt2
							, (sg.amt2 - sg.amt1) * (sg.fee2 / 100)
							, if(w.ord_JS_amt > sg.amt1
								, (w.ord_JS_amt - sg.amt1) * (sg.fee2 / 100)
								, 0
							)
						)
					) as fee_amt_JS2
					, round(
						if(w.ord_JS_amt > sg.amt2
							, (w.ord_JS_amt - sg.amt2) * (sg.fee3 / 100)
							, 0
						)
					) as fee_amt_JS3
					, round(w.ord_TG_amt * (sg.fee_10 / 100)) as fee_amt_TG
					, round(w.ord_YP_amt * (sg.fee_11 / 100)) as fee_amt_YP
					, 0 as fee_amt_OL -- 온라인 작업중
				from store s
					inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = s.store_type
					inner join (
						select grade_cd, name as grade_nm, fee1, amt1, fee2, amt2, fee3, fee_10, fee_11, fee_12, fee_10_info, fee_10_info_over_yn
						from store_grade
						where concat(sdate, '-01 00:00:00') <= date_format(now(), '%Y-%m-%d 00:00:00') 
							and concat(edate, '-31 23:59:59') >= date_format(now(), '%Y-%m-%d 00:00:00') 
					) sg on sg.grade_cd = s.grade_cd
					left outer join (
						select oo.store_cd as ord_store_cd
							, sum(ww.qty) as sale_qty
							, sum(ww.qty * ww.wonga) as wonga_amt
							, sum(ww.recv_amt) as sales_amt
							, sum(if(oo.pr_code = 'JS', ww.recv_amt, 0)) as sales_JS_amt
							, sum(if(oo.pr_code = 'GL', ww.recv_amt, 0)) as sales_GL_amt
							, sum(if(oo.pr_code = 'J1', ww.recv_amt, 0)) as sales_J1_amt
							, sum(if(oo.pr_code = 'J2', ww.recv_amt, 0)) as sales_J2_amt
							, sum(if(
								g.brand not in (select code_id from code where code_kind_cd = 'YP_BRAND') 
									and if(ss.fee_10_info_over_yn = 'Y', ((1 - (oo.price / g.goods_sh)) * 100) <= ss.fee_10_info, ((1 - (oo.price / g.goods_sh)) * 100) < ss.fee_10_info)
								, ww.recv_amt
								, 0
							)) as ord_JS_amt -- 정상
							, sum(if(
								g.brand not in (select code_id from code where code_kind_cd = 'YP_BRAND') 
									and if(ss.fee_10_info_over_yn = 'Y', ((1 - (oo.price / g.goods_sh)) * 100) > ss.fee_10_info, ((1 - (oo.price / g.goods_sh)) * 100) >= ss.fee_10_info)
								, ww.recv_amt
								, 0
							)) as ord_TG_amt -- 특가
							, sum(if(g.brand in (select code_id from code where code_kind_cd = 'YP_BRAND'), ww.recv_amt, 0)) as ord_YP_amt -- 용품
							, sum(0) as ord_OL_amt -- 온라인
						from order_opt_wonga ww
							inner join order_opt oo on oo.ord_opt_no = ww.ord_opt_no
							inner join goods g on g.goods_no = oo.goods_no
							inner join (
								select sss.store_cd, ssg.fee_10_info, ssg.fee_10_info_over_yn
								from store sss
									inner join store_grade ssg on ssg.grade_cd = sss.grade_cd
							) ss on ss.store_cd = oo.store_cd
						where ww.ord_state in (30,60,61) 
							and ww.ord_state_date >= '$f_sdate'
							and ww.ord_state_date <= '$f_edate'
						group by oo.store_cd
					) w on w.ord_store_cd = s.store_cd
				where s.account_yn = 'Y'
				order by w.sales_amt desc
			) a
		";
		$result = DB::select($sql);

		// 아래 작업중입니다. - 최유현
        // /**
        //  * 검색조건 필터링
        //  */
        // $where = "";
        // if ($store_type) $where .= " and c.code_id = " . Lib::quote($store_type);
        // if ($store_kind != "") $where .= " and s.store_kind = '". Lib::quote($store_kind) . "'";
        // if ($store_cd != "") $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";
		
        // // 행사코드별 매출구분
        // $pr_codes = $this->_get_prcodes();
        // $pr_codes_query = "";
        // foreach ($pr_codes as $item) {
        //     $key = $item->code_id;
        //     $pr_codes_query .= "sum(if(o.pr_code = '$key', o.price * o.qty, 0)) as amt_$key,";
        // }

        // /**
        //  * 특가 -> 행사, 특가(온라인) -> 균일로 우선 적용해놓았음 
        //  * 미구현된 두 항목은 추후 반영이 필요함
        //  */
        // $sql = /** @lang text */
        //     "
		// 	select 
        //         s.store_nm, c.code_val as store_type_nm, 
        //         round(if(amt_js > sg.amt1, sg.amt1 * fee1/100, amt_js * fee1/100 )) as fee_amt_js1,
        //         round(if(amt_js > sg.amt1 and amt_js > sg.amt2, (sg.amt2 - sg.amt1) * fee2/100, 0)) as fee_amt_js2,
        //         round(if(amt_js > sg.amt1 and amt_js > sg.amt2 and amt_js > sg.amt3, (amt_js - sg.amt2) * fee3/100, 0)) as fee_amt_js3,
        //         round(amt_gl * sg.fee_10/100) as fee_amt_gl,
        //         round(amt_j1 * sg.fee_10/100) as fee_amt_j1,
        //         round(amt_j2 * sg.fee_11/100) as fee_amt_j2,
        //         sg.*, a.*, b.extra_total
		// 	from store s 
        //         left outer join (
        //             select
        //                 m.store_cd,count(*) as cnt,
        //                 $pr_codes_query
        //                 sum(o.price*o.qty) as ord_amt
        //             from order_mst m
        //                 inner join order_opt o on m.ord_no = o.ord_no
        //                 inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
        //                 inner join goods g on o.goods_no = g.goods_no
        //                 left outer join store s on m.store_cd = s.store_cd
        //                 left outer join brand b on g.brand = b.brand
        //                 left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
        //                 left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
        //             where w.ord_state_date >= '$f_sdate' and w.ord_state_date <= '$f_edate'
        //                 and m.store_cd <> ''
        //             group by m.store_cd
		// 	    ) as a on s.store_cd = a.store_cd
		// 		left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
        //         left outer join store_grade sg on sg.grade_cd = s.grade_cd
        //         left outer join (
        //             select 
        //                 e.store_cd as scd,
        //                 sum(e.extra_amt) as extra_total
        //             from store_account_extra as e
        //                 left outer join `code` c3 on c3.code_kind_cd = 'g_acc_extra_type' and c3.code_id = e.type
        //             where ymonth = date_format('$sdate', '%Y%m')
        //             group by e.store_cd
        //         ) b on s.store_cd = b.scd
		// 	where 1=1 $where and sg.sdate <= '$sdate' and sg.edate >= '$sdate'
        //     order by a.ord_amt desc
		// ";

        // $result = DB::select($sql);

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

    public function show($store_cd, $sdate)
	{
		$store_type   = "";
		$store_nm	= "";
		$acc_idx	= "";
		$closed_yn	= "";

        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->subMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->subMonth()->format("Ymd");

		if( $store_cd != "" ) {
			$sql	= " select store_type, store_nm from store where store_cd = :store_cd ";
			$row	= DB::selectOne($sql, ['store_cd' => $store_cd]);

			if (!empty($row)) {
				$store_type	= $row->store_type;
				$store_nm = $row->store_nm;
			}
		}

		$sql = "
			select idx, closed_yn from store_account_closed
			where store_cd = :store_cd and sday = :sday and eday = :eday
		";
		$row = DB::selectOne($sql, ['store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);

		if (!empty($row)) {
			$acc_idx	= $row->idx;
			$closed_yn	= $row->closed_yn;
		}

		$values = [
			'sdate'			=> $sdate,
			'store_cd'		=> $store_cd,
			'store_nm'		=> $store_nm,
			'ord_states'	=> SLib::getOrdStates(),
			'clm_states'	=> SLib::getCodes('G_CLM_STATE'),
			'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'),
			'ord_types'		=> SLib::getCodes('G_ORD_TYPE'),
			'acc_idx'		=> $acc_idx
		];

		return view( Config::get('shop.store.view') . '/account/acc06_show', $values);
	}

	public function show_search(Request $request)
	{
        $sdate = $request->input('sdate', now()->format("Y-m"));
        // $f_sdate = Carbon::parse($sdate)->firstOfMonth()->subMonth()->format("Ymd");
        // $f_edate = Carbon::parse($sdate)->lastOfMonth()->subMonth()->format("Ymd");

        // 테스트용
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

        $store_cd = $request->input('store_no');

        $ord_state = $request->input('ord_state');
        $clm_state = $request->input('clm_state');
        $stat_pay_type = $request->input('stat_pay_type');
        $not_complex = $request->input('not_complex');
        $ord_type = $request->input('ord_type');

        $where = "";
        if ($ord_state != "") $where .= " and o.ord_state = '$ord_state' ";
        if ($clm_state != "") $where .= " and o.clm_state = '$clm_state' ";
        if ($ord_type != "") $where .= " and o.ord_type = '$ord_type' ";

        if ($stat_pay_type != "") {
            if ($not_complex == "Y") {
                $where .= " and p.pay_type = '$stat_pay_type' ";
            } else {
                $where .= " and (( p.pay_type & $stat_pay_type ) = $stat_pay_type) ";
            }
        }

		$sql	= "
			select
				acc_type.code_val as `type`, w.state_date,o.ord_no, o.ord_opt_no,
				if((select count(*) from order_opt where ord_no = o.ord_no) > 1, 'Y','') as multi_order,
				if(o.coupon_no <>0,(select coupon_nm from coupon where coupon_no = o.coupon_no),'') as coupon_nm,
				o.goods_nm, replace(o.goods_opt,'^',':') as opt_nm, g.style_no,
				opt_type.code_val as opt_type, s.store_nm, m.user_nm, pay_type.code_val as pay_type,
				'Y' as tax_yn,
				w.qty as qty, w.sale_amt, w.clm_amt,w.dc_apply_amt,
				w.coupon_com_amt,
				w.dlv_amt,  w.etc_amt as fee_etc_amt,
				( sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_taxation_amt,
				'0' as sale_net_taxfree_amt,
				( w.sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_amt,
				floor(( w.sale_net_amt + w.dlv_amt )/11) as tax_amt,

				/* 본사 수수료 */
				round(w.fee_ratio, 2) as fee_ratio,
				w.fee,
				w.dc_apply_amt as fee_dc_amt,
				/*( w.coupon_apply_amt - w.allot_amt ) as fee_allot_amt,*/
				/*( w.coupon_apply_amt - w.com_coupon_apply_amt - w.allot_amt ) as fee_allot_amt,*/
				( w.fee - w.dc_apply_amt ) as fee_net_amt,

				/* 정산 금액 */
				(( w.sale_net_amt + w.dlv_amt + w.etc_amt ) - ( w.fee - w.dc_apply_amt ) ) as acc_amt,

				( w.allot_amt ) as fee_allot_amt,
				/* 기타 정보 */
				cd.code_val as ord_state, cd2.code_val as clm_state,
				date_format(o.ord_date,'%y%m%d') as ord_date, date_format(o.dlv_end_date,'%y%m%d') as dlv_end_date,
				if(o.clm_state in (60,61), (
					select
						date_format(max(end_date),'%y%m%d') as clm_end_date
					from
						claim
					where
						ord_opt_no = o.ord_opt_no
				), '') as clm_end_date,'' as bigo,
				g.goods_no ,g.goods_sub ,acc_type.code_id as acc_type,
				case
					when
						w.type in ( 30,90,91 ) and o.qty <> w.sale_qty then 'qtyerror'
					else ''
				end as err_notice
			from
			(
				select
					ord_opt_no
					, round(sum(type)) as `type`
					, max(state_date) as state_date
					, sum(qty) as qty, sum(sale_qty) as sale_qty
					, sum(sale_amt) as sale_amt, sum(clm_amt) as clm_amt
					, sum(coupon_apply_amt) as coupon_apply_amt
					, sum(coupon_apply_amt - allot_amt) as coupon_com_amt,
					/*sum(dc_apply_amt)*/ 0 as dc_apply_amt
					, sum(sale_amt + clm_amt - /*dc_apply_amt*/ 0 - ( coupon_apply_amt - allot_amt )) as sale_net_amt
					, sum(dlv_amt) as dlv_amt
					, sum(wonga) as wonga, sum(fee_ratio) as fee_ratio, sum(fee) as fee
					, sum(etc_amt) as etc_amt
					, sum(allot_amt) as allot_amt
					, sum(com_coupon_apply_amt) as com_coupon_apply_amt
				from
				(
					select
						e.ord_opt_no,0 as `type`, max(e.etc_day) as state_date,0 as qty, 0 as sale_qty,
						0 as sale_amt, 0 as clm_amt, 0 as coupon_apply_amt, 0 as dc_apply_amt,
						0 as dlv_amt, 0 as wonga, 0 as fee_ratio, 0 as fee, 0 as allot_amt, sum(e.etc_amt) as etc_amt,
						0 as com_coupon_apply_amt
					from
						store_account_etc e inner join order_opt o on e.ord_opt_no = o.ord_opt_no
					where
						e.etc_day >= '$f_sdate' and e.etc_day <= '$f_edate' and o.store_cd = '$store_cd'
					group by
						e.ord_opt_no

					union all

					select
						ord_opt_no, `type`, state_date, qty, sale_qty,
						sale_amt, clm_amt, coupon_apply_amt, dc_apply_amt,
						( a.dlv_amt + dlv_ref_amt ) as dlv_amt, wonga, fee_ratio,
						round(( sale_amt + clm_amt ) * fee_ratio / 100) as fee,
						( coupon_apply_amt - coupon_allot_amt ) as allot_amt,

						/* 클레임 발생으로 쿠폰적용 금액이 (-) 인 경우 0 으로..
						if (
							coupon_apply_amt <= 0,
							0,
							( coupon_apply_amt - com_coupon_apply_amt )
						) as allot_amt,*/

						0 as etc_amt,
						com_coupon_apply_amt
					from
					(
						select
							ord_opt_no, store_cd
							, sum(distinct(if(ord_state = 30,30,ord_state))) as type
							, max(ord_state_date) as state_date
							, sum(qty) as qty
							, sum(if(ord_state = 30, qty,0)) as sale_qty
							, sum(if(ord_state = 30, price*qty, 0)) as sale_amt
							, sum(if(ord_state in (60, 61), price*qty, 0)) as clm_amt
							, sum(if(ord_state = 30, coupon_apply_amt, -1 * coupon_apply_amt)) as coupon_apply_amt
							, sum(if(ord_state = 30, coupon_allot_amt, -1 * coupon_allot_amt)) as coupon_allot_amt
							, sum(if(ord_state = 30, /*dc_apply_amt*/ 0, -1 * /*dc_apply_amt*/ 0)) as dc_apply_amt
							, sum(if(ord_state = 30, dlv_amt, -1 * dlv_amt)) as dlv_amt
							, sum(dlv_ref_amt) as dlv_ref_amt

							, round(wonga * qty) as wonga
							, round(100 * (1 - max(wonga) / max(price)), 2) as fee_ratio
							, sum(com_coupon_apply_amt) as com_coupon_apply_amt
						from
						(
							select
								w.ord_opt_no, w.store_cd, o.ord_no, w.ord_state_date, w.ord_state,
								if(w.ord_state = 30, w.qty, w.qty * -1) as qty,
								abs(w.price) as price, abs(w.wonga) as wonga,
								w.coupon_apply_amt,
								ifnull( /*w.dc_apply_amt*/ 0,0) as dc_apply_amt,
								round(if(ifnull(w.com_coupon_ratio, 0) > 1,ifnull(w.com_coupon_ratio, 0) / 100,ifnull(w.com_coupon_ratio, 0)) * ifnull(w.coupon_apply_amt, 0)) as coupon_allot_amt,
								ifnull(w.dlv_amt, 0) as dlv_amt,
								ifnull(w.dlv_ret_amt, 0) + ifnull(w.dlv_add_amt, 0) - ifnull(w.dlv_enc_amt, 0) as dlv_ref_amt,
								/* 업체 쿠폰 부담 금액 ( 2010.10.07 추가 ) */
								round(
									if( ifnull(w.com_coupon_ratio, 0) > 1, ifnull(w.com_coupon_ratio, 0) / 100, ifnull(w.com_coupon_ratio, 0) ) * w.coupon_apply_amt
								) as com_coupon_apply_amt
							from
								order_opt o inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
							where
								w.ord_state_date >= '$f_sdate' and w.ord_state_date <= '$f_edate'
								and w.ord_state in (30,60,61)
								and w.store_cd = '$store_cd'
								and o.ord_state >= 30 /* 결제 오류 발생시 order_opt_wonga 가 자료가 입력 되는 경우 처리 */
						)
						a group by ord_opt_no, store_cd
					)
					a
				)
				a group by ord_opt_no
			)
				w inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst m on o.ord_no = m.ord_no
				inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				inner join payment p on m.ord_no = p.ord_no
				left outer join store s on o.store_cd = s.store_cd
				left outer join  code cd on cd.code_kind_cd = 'g_ord_state' and cd.code_id = o.ord_state
				left outer join  code cd2 on cd2.code_kind_cd = 'g_clm_state' and cd2.code_id = o.clm_state
				left outer join  code opt_type on opt_type.code_kind_cd = 'g_ord_type' and o.ord_type = opt_type.code_id
				left outer join  code acc_type on acc_type.code_kind_cd = 'g_acc_type' and w.type = acc_type.code_id
				left outer join  code pay_type on pay_type.code_kind_cd = 'g_pay_type' and p.pay_type = pay_type.code_id
			where
				1 = 1 $where
			order by
				w.state_date, o.ord_opt_no
		";

		$result = DB::select($sql);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body" => $result
		]);

	}

	public function closed(Request $request)
	{
		$store_cd = $request->input('store_cd');

		$sdate = $request->input('sdate', now()->format("Y-m"));
        // $f_sdate = Carbon::parse($sdate)->firstOfMonth()->subMonth()->format("Ymd");
        // $f_edate = Carbon::parse($sdate)->lastOfMonth()->subMonth()->format("Ymd");

        // 테스트용
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

		$id		= auth('head')->user()->id;
		$name	= auth('head')->user()->name;
        $code	= "000";
        $msg	= "";

		/*
			999 : 알수 없는 에러
			000 : 성공
			100 : 부정확한 요청입니다.
			110 : 마감처리된 내역
			200 : 자료등록시 오류
		*/

		if(strlen($f_sdate) != 8 && strlen($f_edate) != 8 && $store_cd != ""){
			return response()->json(["code"	=> "100", "msg"	=> "부정확한 요청입니다."]);
		}

		$sql = "
			select count(*) as cnt from store_account_closed
			where store_cd = :store_cd and sday = :sday and eday = :eday limit 0,1
		";
		$row	= DB::selectOne($sql, ['store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);
		$cnt	= $row->cnt;

		if( $cnt > 0 ){
			return response()->json(["code"	=> "110", "msg"	=> "이미 마감처리된 내역이 존재합니다."]);
		}

		try {
			// start transaction
			DB::beginTransaction();

			$sql	= "
				delete from store_account_closed_list
				where store_cd = :store_cd and sday = :sday and eday = :eday
			";
			DB::delete($sql, ['store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);

			$sql	= "
				insert into store_account_closed_list
				(
					acc_idx, store_cd, sday, eday,
					type, ord_opt_no, state_date,

					qty, sale_amt, clm_amt, sale_fee,
					sale_clm_amt, dc_amt, coupon_amt, dlv_amt,

					sale_net_taxation_amt, sale_net_taxfree_amt, sale_net_amt, tax_amt,

					fee_ratio, fee, fee_dc_amt, allot_amt, etc_amt, fee_net, acc_amt, bigo
				)
				select
					0 as acc_idx, '$store_cd' as store_cd, '$f_sdate' as sday, '$f_edate' as eday,
					w.type, w.ord_opt_no, w.state_date,

					w.qty as qty, w.sale_amt, w.clm_amt, w.fee as sale_fee,
					0 as sale_clm_amt, w.dc_apply_amt, w.coupon_apply_amt, w.dlv_amt,

					( w.sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_taxation_amt,
					'0' as sale_net_taxfree_amt,
					( w.sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_amt,
					floor(( w.sale_net_amt + w.dlv_amt ) / 11) as tax_amt,

					/* 본사 수수료 */
					round(w.fee_ratio, 2) as fee_ratio,
					w.fee,
					w.dc_apply_amt as fee_dc_amt,
					/*( w.coupon_apply_amt - w.allot_amt ) as fee_allot_amt, */
					( w.allot_amt ) as fee_allot_amt,
					w.etc_amt as fee_etc_amt,
					( w.fee - w.dc_apply_amt ) as fee_net_amt,

					/* 정산 금액 */
					(( w.sale_net_amt + w.dlv_amt + w.etc_amt ) - ( w.fee - w.dc_apply_amt ) ) as acc_amt,

					'' as bigo
				from
				(
					select
						ord_opt_no
						, round(sum(type)) as type
						, max(state_date) as state_date
						, sum(qty) as qty, sum(sale_qty) as sale_qty
						, sum(sale_amt) as sale_amt, sum(clm_amt) as clm_amt
						, sum(coupon_apply_amt) as coupon_apply_amt
						, sum(/*dc_apply_amt*/ 0) as dc_apply_amt
						, sum(sale_amt + clm_amt - dc_apply_amt - ( coupon_apply_amt - allot_amt) ) as sale_net_amt
						, sum(dlv_amt) as dlv_amt
						, sum(wonga) as wonga, sum(fee_ratio) as fee_ratio, sum(fee) as fee
						, sum(etc_amt) as etc_amt
						, sum(allot_amt) as allot_amt
						, sum(com_coupon_apply_amt) as com_coupon_apply_amt
					from
					(
						select
							e.ord_opt_no,0 as type, max(e.etc_day) as state_date,0 as qty, 0 as sale_qty,
							0 as sale_amt, 0 as clm_amt, 0 as coupon_apply_amt, 0 as dc_apply_amt,
							0 as dlv_amt, 0 as wonga, 0 as fee_ratio, 0 as fee, 0 as allot_amt, sum(e.etc_amt) as etc_amt,
							0 as com_coupon_apply_amt
						from
							store_account_etc e inner join order_opt o on e.ord_opt_no = o.ord_opt_no
						where
							e.etc_day >= '$f_sdate' and e.etc_day <= '$f_edate' and o.store_cd = '$store_cd'
						group by
							e.ord_opt_no

						union all

						select
							ord_opt_no, type, state_date, qty, sale_qty,
							sale_amt, clm_amt, coupon_apply_amt, dc_apply_amt,
							( a.dlv_amt + dlv_ref_amt ) as dlv_amt, wonga, fee_ratio,
							round(( sale_amt + clm_amt ) * fee_ratio / 100) as fee,
							( coupon_apply_amt - coupon_allot_amt ) as allot_amt,

							/* 클레임 발생으로 쿠폰적용 금액이 (-) 인 경우 0 으로..
							if (
								coupon_apply_amt <= 0,
								0,
								( coupon_apply_amt - com_coupon_apply_amt )
							) as allot_amt, */

							0 as etc_amt,
							com_coupon_apply_amt
						from
						(
							select
								ord_opt_no, store_cd
								, sum(distinct(if(ord_state = 30,30,ord_state))) as type
								, max(ord_state_date) as state_date
								, sum(qty) as qty
								, sum(if(ord_state = 30, qty,0)) as sale_qty
								, sum(if(ord_state = 30, price*qty, 0)) as sale_amt
								, sum(if(ord_state in (60, 61), price*qty, 0)) as clm_amt
								, sum(if(ord_state = 30, coupon_apply_amt, -1 * coupon_apply_amt)) as coupon_apply_amt
								, sum(if(ord_state = 30, coupon_allot_amt, -1 * coupon_allot_amt)) as coupon_allot_amt
								, sum(if(ord_state = 30, /*dc_apply_amt*/ 0, -1 * /*dc_apply_amt*/ 0)) as dc_apply_amt
								, sum(if(ord_state = 30, dlv_amt, -1 * dlv_amt)) as dlv_amt
								, sum(dlv_ref_amt) as dlv_ref_amt
								, round(wonga * qty) as wonga
								, round(100 * (1 - max(wonga) / max(price)), 2) as fee_ratio
								, sum(com_coupon_apply_amt) as com_coupon_apply_amt
							from
							(
								select
									w.ord_opt_no, w.store_cd, o.ord_no, w.ord_state_date, w.ord_state,
									if(w.ord_state = 30, w.qty, w.qty * -1) as qty,
									abs(w.price) as price, abs(w.wonga) as wonga, w.coupon_apply_amt,ifnull( /*w.dc_apply_amt*/ 0,0) as dc_apply_amt,
									round(if(ifnull(w.com_coupon_ratio, 0) > 1,ifnull(w.com_coupon_ratio, 0) / 100,ifnull(w.com_coupon_ratio, 0))
										* ifnull(w.coupon_apply_amt, 0)) as coupon_allot_amt,
									ifnull(w.dlv_amt, 0) as dlv_amt,
									ifnull(w.dlv_ret_amt, 0) + ifnull(w.dlv_add_amt, 0) - ifnull(w.dlv_enc_amt, 0) as dlv_ref_amt,
									/* 업체 쿠폰 부담 금액 ( 2010.10.07 추가 ) */
									round(
										if( ifnull(w.com_coupon_ratio, 0) > 1, ifnull(w.com_coupon_ratio, 0) / 100, ifnull(w.com_coupon_ratio, 0) ) * w.coupon_apply_amt
									) as com_coupon_apply_amt
								from
									order_opt o inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
								where
									w.ord_state_date >= '$f_sdate'
									and w.ord_state_date <= '$f_edate'
									and w.ord_state in (30,60,61)
									and w.store_cd = '$store_cd'
									and o.ord_state >= 30 /* 결제 오류 발생시 order_opt_wonga 가 자료가 입력 되는 경우 처리 */
							)
							a group by ord_opt_no, store_cd
						)
						a inner join store s on a.store_cd = s.store_cd
					)
					a group by ord_opt_no
				)
					w inner join order_opt o on o.ord_opt_no = w.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
			";

			DB::insert($sql);

			$sql	= "
				insert into store_account_closed
				(
					store_cd,sday,eday,
					sale_amt, clm_amt, sale_fee, dc_amt, coupon_amt, dlv_amt,
					sale_net_taxation_amt, sale_net_taxfree_amt, sale_net_amt, tax_amt,
					fee, fee_dc_amt, allot_amt, etc_amt, fee_net, acc_amt,
					closed_yn, admin_id, admin_nm, reg_date, upd_date
				)
				select
					store_cd,sday,eday,
					sum(sale_amt) as sale_amt, sum(clm_amt), sum(sale_fee), sum(dc_amt), sum(coupon_amt),sum(dlv_amt),
					sum(sale_net_taxation_amt), sum(sale_net_taxfree_amt), sum(sale_net_amt),sum(tax_amt),
					sum(fee), sum(fee_dc_amt), sum(allot_amt), sum(etc_amt), sum(fee_net), sum(acc_amt),
					'N', :id, :name, now() as reg_date, now() as upd_date
				from
					store_account_closed_list
				where
					store_cd = :store_cd and sday = :sday and eday = :eday
				group by
					store_cd,sday,eday
			";
			DB::insert($sql, ['id' => $id, 'name' => $name, 'store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);

			$acc_idx = @DB::table('store_account_closed')->latest('idx')->first()->idx;

			$sql	= "
				update store_account_closed_list set
					acc_idx = :acc_idx
				where
					store_cd = :store_cd and sday = :sday and eday = :eday
			";
			DB::update($sql, ['acc_idx' => $acc_idx, 'store_cd' => $store_cd, 'sday' => $f_sdate, 'eday' => $f_edate]);

			DB::commit();

		} catch(\exception $e) {

			// dd($e);

			DB::rollback();
			$code	= "999";
			$msg	= $e->getmessage();

		}

		return response()->json([
			"code"	=> $code,
			"msg"	=> $msg
		]);
	}

}
