<?php

namespace App\Http\Controllers\shop\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
//use Carbon\Carbon;
use Exception;

class acc02Controller extends Controller
{
	public function index() {
		$sdate	= date("Y-m-d", strtotime("first day of -1 month"));
		$edate	= date("Y-m-d", strtotime("last day of -1 month"));

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
		];

		return view( Config::get('shop.shop.view') . '/account/acc02',$values);
	}

	public function search(Request $request)
	{
		$sdate	= $request->input('sdate',strtotime("first day of -1 month"));
		$sdate	= str_replace("-", "", $sdate);
		$edate	= $request->input('edate',strtotime("last day of -1 month"));
		$edate	= str_replace("-", "", $edate);
		$store_cd	= $request->input('store_cd');

		$where	= "";
		$inner_where	= "";
		
		if( $store_cd != "" ){
			$where .= " and s.store_cd = '$store_cd' ";
			$inner_where .= " and w.store_cd = '$store_cd' ";
		}

		$query	= "
			select
				'$sdate ~ $edate' as day,
				s.store_nm,
				a.sale_amt, a.clm_amt, a.dc_apply_amt, a.coupon_com_amt, a.dlv_amt,
				a.fee_etc_amt, a.sale_net_taxation_amt, a.sale_net_taxfree_amt, a.sale_net_amt, a.tax_amt,
				a.fee, a.fee_dc_amt,
				a.fee_net_amt,
				a.acc_amt,
				a.fee_allot_amt,
				s.store_cd,
				c.closed_yn as closed
			from
			(
				select
					o.store_cd,
					sum(w.sale_amt) as sale_amt,
					sum(w.clm_amt) as clm_amt,
					sum(w.dc_apply_amt) as dc_apply_amt,
					sum(w.coupon_apply_amt) as coupon_apply_amt,
					sum(w.coupon_apply_amt - w.allot_amt) as coupon_com_amt,
					sum(w.dlv_amt) as dlv_amt,
					sum(w.sale_net_amt + w.dlv_amt + w.etc_amt) as sale_net_taxation_amt,
					'0' as sale_net_taxfree_amt,
					sum(w.sale_net_amt + w.dlv_amt + w.etc_amt) as sale_net_amt,
					sum(floor(( w.sale_net_amt + w.dlv_amt + w.etc_amt )/11)) as tax_amt,
					sum(w.fee) as fee,
					sum(dc_apply_amt) as fee_dc_amt,
					/*sum(w.coupon_apply_amt - w.allot_amt) as fee_allot_amt,*/
					sum(w.allot_amt) as fee_allot_amt,
					sum(w.etc_amt) as fee_etc_amt,
					sum(w.fee - w.dc_apply_amt) as fee_net_amt,
					/* 정산 금액 */
					sum( (w.sale_net_amt + w.dlv_amt + w.etc_amt) - (w.fee - w.dc_apply_amt) ) as acc_amt
				from 
				(
					select
						ord_opt_no
						, round(sum(type)) as `type`
						, max(state_date) as state_date
						, sum(qty) as qty, sum(sale_qty) as sale_qty
						, sum(sale_amt) as sale_amt, sum(clm_amt) as clm_amt
						, sum(coupon_apply_amt) as coupon_apply_amt
						, /*sum(dc_apply_amt)*/ 0 as dc_apply_amt
						, sum(sale_amt + clm_amt - /*dc_apply_amt*/ 0 - ( coupon_apply_amt - allot_amt ) ) as sale_net_amt
						, sum(dlv_amt) as dlv_amt
						, sum(wonga) as wonga, sum(fee_ratio) as fee_ratio, sum(fee) as fee
						, sum(etc_amt) as etc_amt
						, sum(allot_amt) as allot_amt
						, sum(com_coupon_apply_amt) as com_coupon_apply_amt
					from
					(
						/*
							기타정산액 (account_etc 추후 store_account_etc로 변경)
						*/
						select
							e.ord_opt_no,0 as `type`, max(e.etc_day) as state_date, 0 as qty, 0 as sale_qty,
							0 as sale_amt, 0 as clm_amt, 0 as coupon_apply_amt, 0 as dc_apply_amt,
							0 as dlv_amt, 0 as wonga, 0 as fee_ratio, 0 as fee, 0 as allot_amt, sum(e.etc_amt) as etc_amt,
							0 as com_coupon_apply_amt
						from
							account_etc e inner join order_opt o on e.ord_opt_no = o.ord_opt_no
						where
							e.etc_day >= '$sdate' and e.etc_day <= '$edate'
						group by
							e.ord_opt_no
						union all

						select
							ord_opt_no, `type`, state_date, qty, sale_qty,
							sale_amt, clm_amt, coupon_apply_amt, 0 as dc_apply_amt,
							( a.dlv_amt + dlv_ref_amt ) as dlv_amt, wonga, fee_ratio,
							round(( sale_amt + clm_amt ) * a.fee_ratio / 100) as fee,
							( coupon_apply_amt - coupon_allot_amt ) as allot_amt,
							com_coupon_apply_amt, 0 as etc_amt
						from
						(
							/*
								주문번호로 매출합을 구하기
							*/
							select
								ord_opt_no, store_cd
								, sum(distinct(if(ord_state = 30, 30, ord_state))) as `type`
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
								, round(wonga*qty) as wonga
								, round(100*(1 - max(wonga)/max(price)),2) as fee_ratio
								, sum(com_coupon_apply_amt) as com_coupon_apply_amt
							from
								(
								/* 30 에 대한 매출내역 */
								select
									s.store_cd, w.ord_opt_no, o.ord_no, w.ord_state_date, w.ord_state,
									if(w.ord_state = 30, w.qty, w.qty * -1) as qty,
									abs(w.price) as price, abs(w.wonga) as wonga, w.coupon_apply_amt,
									ifnull( /*w.dc_apply_amt*/ 0,0) as dc_apply_amt,
									round(if(ifnull(w.com_coupon_ratio, 0) > 1,ifnull(w.com_coupon_ratio, 0) / 100,ifnull(w.com_coupon_ratio, 0))	* ifnull(w.coupon_apply_amt, 0)) as coupon_allot_amt,
									ifnull(w.dlv_amt, 0) as dlv_amt,
									ifnull(w.dlv_ret_amt, 0) + ifnull(w.dlv_add_amt, 0) - ifnull(w.dlv_enc_amt, 0) as dlv_ref_amt,
									/* 업체 쿠폰 부담 금액 ( 2010.10.07 추가 ) */
									round(
										if( ifnull(w.com_coupon_ratio, 0) > 1, ifnull(w.com_coupon_ratio, 0) / 100, ifnull(w.com_coupon_ratio, 0) ) * w.coupon_apply_amt
									) as com_coupon_apply_amt
								from
									order_opt o inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
									inner join store s on o.store_cd = s.store_cd
									where
										w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate'
										and w.ord_state in (30,60,61) $inner_where
										and o.ord_state >= 30 /* 결제 오류 발생시 order_opt_wonga 가 자료가 입력 되는 경우 처리 */
								)
								a group by ord_opt_no, store_cd
							) a
						) a group by ord_opt_no
					) w inner join order_opt o on o.ord_opt_no = w.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				where
					1 = 1
				group by
					o.store_cd
				) a inner join store s on a.store_cd = s.`store_cd`
				left outer join store_account_closed c on s.store_cd = c.store_cd
			where
				1 = 1 $where
			order by
				a.acc_amt desc
		";

		$result = DB::select($query);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body" => $result
		]);

	}

	public function show($store_cd, $sdate, $edate, Request $request)
	{
		$store_type   = "";
		$store_nm	= "";
		$acc_idx	= "";
		$closed_yn	= "";

		if( $store_cd != "" ){
			$sql	= " select store_type, store_nm from store where store_cd = :store_cd ";
			$row	= DB::selectOne($sql, ['store_cd' => $store_cd]);

			if(!empty($row)){
				$store_type	= $row->store_type;
				$store_nm = $row->store_nm;
			}
		}

		$sql = "
			select idx, closed_yn from store_account_closed
			where store_cd = :store_cd and sday = :sday and eday = :eday
		";
		$row = DB::selectOne($sql, ['store_cd' => $store_cd, 'sday' => str_replace("-","",$sdate), 'eday' => str_replace("-","",$edate)]);

		if(!empty($row)){
			$acc_idx	= $row->idx;
			$closed_yn	= $row->closed_yn;
		}

		$values = [
			'sdate'			=> $sdate,
			'edate'			=> $edate,
			'store_cd'		=> $store_cd,
			'store_nm'		=> $store_nm,
			'ord_states'	=> SLib::getOrdStates(),
			'clm_states'	=> SLib::getCodes('G_CLM_STATE'),
			'stat_pay_types'	=> SLib::getCodes('G_STAT_PAY_TYPE'),
			'ord_types'		=> SLib::getCodes('G_ORD_TYPE'),
			'acc_idx'		=> $acc_idx
		];

		return view( Config::get('shop.shop.view') . '/account/acc02_show', $values);
	}

	public function show_search(Request $request)
	{
		$sdate = $request->input('sdate',strtotime("first day of -1 month"));
		$sdate = str_replace("-", "", $sdate);
		$edate = $request->input('edate',strtotime("last day of -1 month"));
		$edate = str_replace("-", "", $edate);
		$store_cd = $request->input('store_no');

		$ord_state	= $request->input('ord_state');
		$clm_state	= $request->input('clm_state');
		$stat_pay_type	= $request->input('stat_pay_type');
		$not_complex	= $request->input('not_complex');
		$ord_type	= $request->input('ord_type');

		$where	= "";
		if( $ord_state != "" )	$where .= " and o.ord_state = '$ord_state' ";
		if( $clm_state != "" )	$where .= " and o.clm_state = '$clm_state' ";
		if( $ord_type != "" )	$where .= " and o.ord_type = '$ord_type' ";

		if($stat_pay_type != ""){
			if($not_complex == "Y"){
				$where .= " and p.pay_type = '$stat_pay_type' ";
			}else{
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
						account_etc e inner join order_opt o on e.ord_opt_no = o.ord_opt_no
					where
						e.etc_day >= '$sdate' and e.etc_day <= '$edate' and o.com_id = '$store_cd'
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
								w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate'
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
				inner join store s on o.store_cd = s.store_cd
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
		$sdate	= $request->input('sdate');
		$edate	= $request->input('edate');
		$store_cd	= $request->input('store_cd');

		$sdate	= str_replace("-", "", $sdate);
		$edate	= str_replace("-", "", $edate);

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

		if(strlen($sdate) != 8 && strlen($edate) != 8 && $store_cd != ""){
			return response()->json(["code"	=> "100", "msg"	=> "부정확한 요청입니다."]);
		}

		$sql = "
			select count(*) as cnt from store_account_closed
			where store_cd = :store_cd and sday = :sday and eday = :eday limit 0,1
		";
		$row	= DB::selectOne($sql, ['store_cd' => $store_cd, 'sday' => $sdate, 'eday' => $edate]);
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
			DB::delete($sql, ['store_cd' => $store_cd, 'sday' => $sdate, 'eday' => $edate]);

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
					0 as acc_idx, '$store_cd' as store_cd, '$sdate' as sday, '$edate' as eday,
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
							e.etc_day >= '$sdate' and e.etc_day <= '$edate' and o.store_cd = '$store_cd'
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
									w.ord_state_date >= '$sdate'
									and w.ord_state_date <= '$edate'
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
					'n', :id, :name, now() as reg_date, now() as upd_date
				from
					store_account_closed_list
				where
					store_cd = :store_cd and sday = :sday and eday = :eday
				group by
					store_cd,sday,eday
			";
			DB::insert($sql, ['id' => $id, 'name' => $name, 'store_cd' => $store_cd, 'sday' => $sdate, 'eday' => $edate]);

			$acc_idx = db::table('account_closed')->latest('idx')->first()->idx;

			$sql	= "
				update store_account_closed_list set
					acc_idx = :acc_idx
				where
					store_cd = :store_cd and sday = :sday and eday = :eday
			";
			DB::update($sql, ['acc_idx' => $acc_idx, 'store_cd' => $store_cd, 'sday' => $sdate, 'eday' => $edate]);

			DB::commit();

		} catch(exception $e) {

			dd($e);

			db::rollback();
			$code	= "999";
			$msg	= $e->getmessage();

		}

		return response()->json([
			"code"	=> $code,
			"msg"	=> $msg
		]);
	}
}