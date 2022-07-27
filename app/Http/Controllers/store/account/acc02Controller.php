<?php

namespace App\Http\Controllers\store\account;

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
	//
	public function index() {
		$sdate	= date("Y-m-d", strtotime("first day of -1 month"));
		$edate	= date("Y-m-d", strtotime("last day of -1 month"));



		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
		];

		return view( Config::get('shop.store.view') . '/account/acc02',$values);
	}

	public function search(Request $request)
	{
		$sdate	= $request->input('sdate',strtotime("first day of -1 month"));
		$sdate	= str_replace("-", "", $sdate);
		$edate	= $request->input('edate',strtotime("last day of -1 month"));
		$edate	= str_replace("-", "", $edate);
		$com_cd	= $request->input('com_cd');

		$where	= "";
		$inner_where	= "";
		
		if( $com_cd != "" ){
			$where	.= " and c.com_id = '$com_cd' ";
			$inner_where	.= " and w.com_id = '$com_cd' ";
		}

		$total		= 0;
		$page_cnt	= 0;

		$query	= "
			SELECT
				ifnull(
					( select
						case
							when sday = '$sdate' and eday = '$edate' then closed_yn
						else concat_ws(' ~ ',sday,eday)
						end as chk
						from account_closed
						where com_id = a.com_id and sday <= '$edate' and eday >= '$sdate'
						order by idx desc limit 0,1
					),'-'
				) as closed,
				'$sdate ~ $edate' as day,
				c.com_nm,
				cd.code_val as margin_type,
				a.sale_amt, a.clm_amt, a.dc_apply_amt, a.coupon_com_amt, a.dlv_amt,
				a.fee_etc_amt, a.sale_net_taxation_amt, a.sale_net_taxfree_amt, a.sale_net_amt, a.tax_amt,
				a.fee, a.fee_dc_amt,
				a.fee_net_amt,
				a.acc_amt,
				a.fee_allot_amt,
				a.com_id,
				ifnull(( select idx from account_closed where com_id = a.com_id and sday = '$sdate' and eday = '$edate' ),'-') as acc_idx
			FROM
			(
				SELECT
					o.com_id,
					SUM(w.sale_amt) AS sale_amt,
					SUM(w.clm_amt) AS clm_amt,
					SUM(w.dc_apply_amt) AS dc_apply_amt,
					SUM(w.coupon_apply_amt) AS coupon_apply_amt,
					SUM(w.coupon_apply_amt - w.allot_amt) AS coupon_com_amt,
					SUM(w.dlv_amt) AS dlv_amt,
					SUM(w.sale_net_amt + w.dlv_amt + w.etc_amt) AS sale_net_taxation_amt,
					'0' AS sale_net_taxfree_amt,
					SUM(w.sale_net_amt + w.dlv_amt + w.etc_amt) AS sale_net_amt,
					SUM(FLOOR(( w.sale_net_amt + w.dlv_amt + w.etc_amt )/11)) AS tax_amt,
					SUM(w.fee) AS fee,
					SUM(dc_apply_amt) AS fee_dc_amt,
					/*SUM(w.coupon_apply_amt - w.allot_amt) AS fee_allot_amt,*/
					sum(w.allot_amt) AS fee_allot_amt,
					SUM(w.etc_amt) AS fee_etc_amt,
					SUM(w.fee - w.dc_apply_amt) AS fee_net_amt,
					/* 정산 금액 */
					SUM( (w.sale_net_amt + w.dlv_amt + w.etc_amt) - (w.fee - w.dc_apply_amt) ) as acc_amt
				FROM
				(
					SELECT
						ord_opt_no
						, ROUND(SUM(type)) AS type
						, MAX(state_date) AS state_date
						, SUM(qty) AS qty, SUM(sale_qty) AS sale_qty
						, SUM(sale_amt) AS sale_amt, SUM(clm_amt) AS clm_amt
						, SUM(coupon_apply_amt) AS coupon_apply_amt
						, /*SUM(dc_apply_amt)*/ 0 AS dc_apply_amt
						, SUM(sale_amt + clm_amt - /*dc_apply_amt*/ 0 - ( coupon_apply_amt - allot_amt ) ) AS sale_net_amt
						, SUM(dlv_amt) AS dlv_amt
						, SUM(wonga) AS wonga, SUM(fee_ratio) AS fee_ratio, SUM(fee) AS fee
						, SUM(etc_amt) AS etc_amt
						, SUM(allot_amt) AS allot_amt
						, SUM(com_coupon_apply_amt) as com_coupon_apply_amt
					FROM
					(
						SELECT
							e.ord_opt_no,0 AS type, MAX(e.etc_day) AS state_date, 0 AS qty, 0 AS sale_qty,
							0 AS sale_amt, 0 AS clm_amt, 0 AS coupon_apply_amt, 0 AS dc_apply_amt,
							0 AS dlv_amt, 0 AS wonga, 0 AS fee_ratio, 0 AS fee, 0 AS allot_amt, SUM(e.etc_amt) AS etc_amt,
							0 as com_coupon_apply_amt
						FROM
							account_etc e INNER JOIN order_opt o ON e.ord_opt_no = o.ord_opt_no
						WHERE
							e.etc_day >= '$sdate' AND e.etc_day <= '$edate'
						GROUP BY
							e.ord_opt_no

						UNION ALL

						SELECT
							ord_opt_no, type, state_date, qty, sale_qty,
							sale_amt, clm_amt, coupon_apply_amt, /*dc_apply_amt*/ 0 as dc_apply_amt,
							( a.dlv_amt + dlv_ref_amt ) AS dlv_amt, wonga, fee_ratio,
							IF(c.margin_type = 'WONGA',
							IF(qty = 0, 0, (sale_amt + clm_amt - wonga)),
							ROUND(( sale_amt + clm_amt ) * fee_ratio / 100)) AS fee,
							( coupon_apply_amt - coupon_allot_amt ) AS allot_amt,

							/* 클레임 발생으로 쿠폰적용 금액이 (-) 인 경우 0 으로..
							IF (
								coupon_apply_amt <= 0,
								0,
								( coupon_apply_amt - com_coupon_apply_amt )
							) AS allot_amt,*/

							0 as etc_amt,
							com_coupon_apply_amt
						FROM
						(
							SELECT
								ord_opt_no,com_id
								, SUM(distinct(if(ord_state = 30,30,ord_state))) as type
								, MAX(ord_state_date) AS state_date
								, SUM(qty) AS qty
								, SUM(IF(ord_state = 30, qty,0)) AS sale_qty
								, SUM(IF(ord_state = 30, price*qty, 0)) AS sale_amt
								, SUM(IF(ord_state IN (60, 61), price*qty, 0)) AS clm_amt
								, SUM(IF(ord_state = 30, coupon_apply_amt, -1 * coupon_apply_amt)) AS coupon_apply_amt
								, SUM(IF(ord_state = 30, coupon_allot_amt, -1 * coupon_allot_amt)) AS coupon_allot_amt
								, SUM(IF(ord_state = 30, /*dc_apply_amt*/ 0, -1 * /*dc_apply_amt*/ 0)) AS dc_apply_amt
								, SUM(IF(ord_state = 30, dlv_amt, -1 * dlv_amt)) AS dlv_amt
								, SUM(dlv_ref_amt) AS dlv_ref_amt

								, ROUND(wonga*qty) as wonga
								, ROUND(100*(1 - MAX(wonga)/MAX(price)),2) as fee_ratio
								, SUM(com_coupon_apply_amt) AS com_coupon_apply_amt
							FROM
							(
								SELECT
									w.ord_opt_no, w.com_id, o.ord_no, w.ord_state_date, w.ord_state,
									IF(w.ord_state = 30, w.qty, w.qty * -1) AS qty,
									ABS(w.price) AS price, ABS(w.wonga) AS wonga, w.coupon_apply_amt,
									IFNULL( /*w.dc_apply_amt*/ 0,0) as dc_apply_amt,
									ROUND(IF(IFNULL(w.com_coupon_ratio, 0) > 1,IFNULL(w.com_coupon_ratio, 0) / 100,IFNULL(w.com_coupon_ratio, 0))	* IFNULL(w.coupon_apply_amt, 0)) AS coupon_allot_amt,
									IFNULL(w.dlv_amt, 0) AS dlv_amt,
									IFNULL(w.dlv_ret_amt, 0) + IFNULL(w.dlv_add_amt, 0) - IFNULL(w.dlv_enc_amt, 0) AS dlv_ref_amt,
									/* 업체 쿠폰 부담 금액 ( 2010.10.07 추가 ) */
									ROUND(
										IF( IFNULL(w.com_coupon_ratio, 0) > 1, IFNULL(w.com_coupon_ratio, 0) / 100, IFNULL(w.com_coupon_ratio, 0) ) * w.coupon_apply_amt
									) AS com_coupon_apply_amt
								FROM
									order_opt o INNER JOIN order_opt_wonga w ON o.ord_opt_no = w.ord_opt_no
									INNER JOIN company c on w.com_id = c.com_id and c.com_type = '2'
								WHERE
									w.ord_state_date >= '$sdate' AND w.ord_state_date <= '$edate'
									AND w.ord_state IN (30,60,61) $inner_where
									AND o.ord_state >= 30 /* 결제 오류 발생시 order_opt_wonga 가 자료가 입력 되는 경우 처리 */
							)
							a GROUP BY ord_opt_no,com_id
						)
						a INNER JOIN company c ON a.com_id = c.com_id
					)
					a GROUP BY ord_opt_no
				)
					w INNER JOIN order_opt o ON o.ord_opt_no = w.ord_opt_no
					INNER JOIN goods g ON o.goods_no = g.goods_no AND o.goods_sub = g.goods_sub
				WHERE
					1 = 1
				GROUP BY
					o.com_id
			)
				a inner join company c on a.com_id = c.com_id
				inner join code cd on cd.code_kind_cd = 'G_MARGIN_TYPE' and c.margin_type = cd.code_id
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

	public function show($com_id, $sdate, $edate, Request $request)
	{
		$com_type	= "";
		$com_nm		= "";
		$acc_idx	= "";
		$closed_yn	= "";

		if( $com_id != "" ){
			$sql	= " select com_type, com_nm from company where com_id = :com_id ";
			$row	= DB::selectOne($sql, ['com_id' => $com_id]);

			if(!empty($row)){
				$com_type	= $row->com_type;
				$com_nm		= $row->com_nm;
			}
		}

		$sql	= "
			select idx,closed_yn from account_closed
			where com_id = :com_id and sday = :sday and eday = :eday
		";
		$row	= DB::selectOne($sql, ['com_id' => $com_id, 'sday' => str_replace("-","",$sdate), 'eday' => str_replace("-","",$edate)]);

		if(!empty($row)){
			$acc_idx	= $row->idx;
			$closed_yn	= $row->closed_yn;
		}

		$values = [
			'sdate'			=> $sdate,
			'edate'			=> $edate,
			'com_id'		=> $com_id,
			'com_nm'		=> $com_nm,
			'ord_states'	=> SLib::getOrdStates(),
			'clm_states'	=> SLib::getCodes('G_CLM_STATE'),
			'stat_pay_types'	=> SLib::getCodes('G_STAT_PAY_TYPE'),
			'ord_types'		=> SLib::getCodes('G_ORD_TYPE'),
			'acc_idx'		=> $acc_idx
		];

		return view( Config::get('shop.store.view') . '/account/acc02_show',$values);
	}

	public function show_search(Request $request)
	{
		$sdate	= $request->input('sdate',strtotime("first day of -1 month"));
		$sdate	= str_replace("-", "", $sdate);
		$edate	= $request->input('edate',strtotime("last day of -1 month"));
		$edate	= str_replace("-", "", $edate);
		$com_cd	= $request->input('com_cd');

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
			SELECT
				acc_type.code_val AS type,w.state_date,o.ord_no,o.ord_opt_no,
				IF((SELECT COUNT(*) FROM order_opt WHERE ord_no = o.ord_no) > 1, 'Y','') AS multi_order,
				IF(o.coupon_no <>0,(SELECT coupon_nm FROM coupon WHERE coupon_no = o.coupon_no),'') AS coupon_nm,
				o.goods_nm, REPLACE(o.goods_opt,'^',':') AS opt_nm, g.style_no,
				opt_type.code_val AS opt_type, cp.com_nm, m.user_nm, pay_type.code_val AS pay_type,
				'Y' as tax_yn,
				w.qty AS qty, w.sale_amt, w.clm_amt,w.dc_apply_amt,
				w.coupon_com_amt,
				w.dlv_amt,  w.etc_amt as fee_etc_amt,
				( sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_taxation_amt,
				'0' as sale_net_taxfree_amt,
				( w.sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_amt,
				floor(( w.sale_net_amt + w.dlv_amt )/11) as tax_amt,

				/* 본사 수수료 */
				ROUND(w.fee_ratio, 2) as fee_ratio,
				w.fee,
				w.dc_apply_amt as fee_dc_amt,
				/*( w.coupon_apply_amt - w.allot_amt ) as fee_allot_amt,*/
				/*( w.coupon_apply_amt - w.com_coupon_apply_amt - w.allot_amt ) AS fee_allot_amt,*/
				( w.fee - w.dc_apply_amt ) as fee_net_amt,

				/* 정산 금액 */
				(( w.sale_net_amt + w.dlv_amt + w.etc_amt ) - ( w.fee - w.dc_apply_amt ) ) as acc_amt,

				( w.allot_amt ) AS fee_allot_amt,
				/* 기타 정보 */
				cd.code_val AS ord_state, cd2.code_val AS clm_state,
				DATE_FORMAT(o.ord_date,'%Y%m%d') AS ord_date, DATE_FORMAT(o.dlv_end_date,'%Y%m%d') AS dlv_end_date,
				IF(o.clm_state IN (60,61), (
					SELECT
						DATE_FORMAT(MAX(end_date),'%Y%m%d') as clm_end_date
					FROM
						claim
					WHERE
						ord_opt_no = o.ord_opt_no
				), '') AS clm_end_date,'' AS bigo,
				g.goods_no ,g.goods_sub ,acc_type.code_id AS acc_type,
				CASE
					WHEN
						w.type IN ( 30,90,91 ) AND o.qty <> w.sale_qty THEN 'qtyerror'
					ELSE ''
				END AS err_notice
			FROM
			(
				SELECT
					ord_opt_no
					, ROUND(SUM(type)) AS type
					, MAX(state_date) AS state_date
					, SUM(qty) AS qty, SUM(sale_qty) AS sale_qty
					, SUM(sale_amt) AS sale_amt, SUM(clm_amt) AS clm_amt
					, SUM(coupon_apply_amt) AS coupon_apply_amt
					, SUM(coupon_apply_amt - allot_amt) AS coupon_com_amt,
					/*SUM(dc_apply_amt)*/ 0 AS dc_apply_amt
					, SUM(sale_amt + clm_amt - /*dc_apply_amt*/ 0 - ( coupon_apply_amt - allot_amt )) AS sale_net_amt
					, SUM(dlv_amt) AS dlv_amt
					, SUM(wonga) AS wonga, SUM(fee_ratio) AS fee_ratio, SUM(fee) AS fee
					, SUM(etc_amt) AS etc_amt
					, SUM(allot_amt) AS allot_amt
					, SUM(com_coupon_apply_amt) as com_coupon_apply_amt
				FROM
				(
					SELECT
						e.ord_opt_no,0 AS type, MAX(e.etc_day) AS state_date,0 AS qty, 0 AS sale_qty,
						0 AS sale_amt, 0 AS clm_amt, 0 AS coupon_apply_amt, 0 AS dc_apply_amt,
						0 AS dlv_amt, 0 AS wonga, 0 AS fee_ratio, 0 AS fee, 0 AS allot_amt, SUM(e.etc_amt) AS etc_amt,
						0 as com_coupon_apply_amt
					FROM
						account_etc e INNER JOIN order_opt o ON e.ord_opt_no = o.ord_opt_no
					WHERE
						e.etc_day >= '$sdate' AND e.etc_day <= '$edate' AND o.com_id = '$com_cd'
					GROUP BY
						e.ord_opt_no

					UNION ALL

					SELECT
						ord_opt_no, type, state_date, qty, sale_qty,
						sale_amt, clm_amt, coupon_apply_amt, dc_apply_amt,
						( a.dlv_amt + dlv_ref_amt ) AS dlv_amt, wonga, fee_ratio,
						IF(c.margin_type = 'WONGA',
						IF(qty = 0, 0, (sale_amt + clm_amt - wonga)),
						round(( sale_amt + clm_amt ) * fee_ratio / 100)) AS fee,
						( coupon_apply_amt - coupon_allot_amt ) AS allot_amt,

						/* 클레임 발생으로 쿠폰적용 금액이 (-) 인 경우 0 으로..
						IF (
							coupon_apply_amt <= 0,
							0,
							( coupon_apply_amt - com_coupon_apply_amt )
						) AS allot_amt,*/

						0 as etc_amt,

						com_coupon_apply_amt
					FROM
					(
						SELECT
							ord_opt_no,com_id
							, SUM(distinct(if(ord_state = 30,30,ord_state))) as type
							, MAX(ord_state_date) AS state_date
							, SUM(qty) AS qty
							, SUM(IF(ord_state = 30, qty,0)) AS sale_qty
							, SUM(IF(ord_state = 30, price*qty, 0)) AS sale_amt
							, SUM(IF(ord_state IN (60, 61), price*qty, 0)) AS clm_amt
							, SUM(IF(ord_state = 30, coupon_apply_amt, -1 * coupon_apply_amt)) AS coupon_apply_amt
							, SUM(IF(ord_state = 30, coupon_allot_amt, -1 * coupon_allot_amt)) AS coupon_allot_amt
							, SUM(IF(ord_state = 30, /*dc_apply_amt*/ 0, -1 * /*dc_apply_amt*/ 0)) AS dc_apply_amt
							, SUM(IF(ord_state = 30, dlv_amt, -1 * dlv_amt)) AS dlv_amt
							, SUM(dlv_ref_amt) AS dlv_ref_amt

							, ROUND(wonga * qty) AS wonga
							, ROUND(100 * (1 - MAX(wonga) / MAX(price)), 2) AS fee_ratio
							, SUM(com_coupon_apply_amt) AS com_coupon_apply_amt
						FROM
						(
							SELECT
								w.ord_opt_no, w.com_id, o.ord_no, w.ord_state_date, w.ord_state,
								IF(w.ord_state = 30, w.qty, w.qty * -1) AS qty,
								ABS(w.price) AS price, ABS(w.wonga) AS wonga,
								w.coupon_apply_amt,
								IFNULL( /*w.dc_apply_amt*/ 0,0) as dc_apply_amt,
								ROUND(IF(IFNULL(w.com_coupon_ratio, 0) > 1,IFNULL(w.com_coupon_ratio, 0) / 100,IFNULL(w.com_coupon_ratio, 0)) * IFNULL(w.coupon_apply_amt, 0)) AS coupon_allot_amt,
								IFNULL(w.dlv_amt, 0) AS dlv_amt,
								IFNULL(w.dlv_ret_amt, 0) + IFNULL(w.dlv_add_amt, 0) - IFNULL(w.dlv_enc_amt, 0) AS dlv_ref_amt,
								/* 업체 쿠폰 부담 금액 ( 2010.10.07 추가 ) */
								ROUND(
									IF( IFNULL(w.com_coupon_ratio, 0) > 1, IFNULL(w.com_coupon_ratio, 0) / 100, IFNULL(w.com_coupon_ratio, 0) ) * w.coupon_apply_amt
								) AS com_coupon_apply_amt
							FROM
								order_opt o INNER JOIN order_opt_wonga w ON o.ord_opt_no = w.ord_opt_no
							WHERE
								w.ord_state_date >= '$sdate' AND w.ord_state_date <= '$edate'
								AND w.ord_state IN (30,60,61)
								AND w.com_id = '$com_cd'
								AND o.ord_state >= 30 /* 결제 오류 발생시 order_opt_wonga 가 자료가 입력 되는 경우 처리 */
						)
						a GROUP BY ord_opt_no,com_id
					)
					a INNER JOIN company c ON a.com_id = c.com_id
				)
				a GROUP BY ord_opt_no
			)
				w INNER JOIN order_opt o ON o.ord_opt_no = w.ord_opt_no
				INNER JOIN order_mst m ON o.ord_no = m.ord_no
				INNER JOIN goods g ON o.goods_no = g.goods_no AND o.goods_sub = g.goods_sub
				INNER JOIN payment p ON m.ord_no = p.ord_no
				LEFT OUTER JOIN company cp ON o.sale_place = cp.com_id AND cp.com_type = '4'
				LEFT OUTER JOIN  code cd ON cd.code_kind_cd = 'G_ORD_STATE' AND cd.code_id = o.ord_state
				LEFT OUTER JOIN  code cd2 ON cd2.code_kind_cd = 'G_CLM_STATE' AND cd2.code_id = o.clm_state
				LEFT OUTER JOIN  code opt_type ON opt_type.code_kind_cd = 'G_ORD_TYPE' AND o.ord_type = opt_type.code_id
				LEFT OUTER JOIN  code acc_type ON acc_type.code_kind_cd = 'G_ACC_TYPE' AND w.type = acc_type.code_id
				LEFT OUTER JOIN  code pay_type ON pay_type.code_kind_cd = 'G_PAY_TYPE' AND p.pay_type = pay_type.code_id
			WHERE
				1 = 1 $where
			ORDER BY
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
		$com_id	= $request->input('com_id');

		$sdate	= str_replace("-", "", $sdate);
		$edate	= str_replace("-", "", $edate);

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;
        $code	= "000";
        $msg	= "";

		/*
			999 : 알수 없는 에러
			000 : 성공
			100 : 부정확한 요청입니다.
			110 : 마감처리된 내역
			200 : 자료등록시 오류
		*/

		if(strlen($sdate) != 8 && strlen($edate) != 8 && $com_id != ""){
			return response()->json(["code"	=> "100", "msg"	=> "부정확한 요청입니다."]);
		}

		$sql = "
			select count(*) as cnt from account_closed
			where com_id = :com_id and sday = :sday and eday = :eday limit 0,1
		";
		$row	= DB::selectOne($sql, ['com_id' => $com_id, 'sday' => $sdate, 'eday' => $edate]);
		$cnt	= $row->cnt;

		if( $cnt > 0 ){
			return response()->json(["code"	=> "110", "msg"	=> "이미 마감처리된 내역이 존재합니다."]);
		}

		try {

			// Start transaction
			DB::beginTransaction();

			$sql	= "
				delete from account_closed_list
				where com_id = :com_id and sday = :sday and eday = :eday
			";
			DB::delete($sql, ['com_id' => $com_id, 'sday' => $sdate, 'eday' => $edate]);

			$sql	= "
				INSERT INTO account_closed_list
				(
					acc_idx, com_id, sday, eday,
					type, ord_opt_no, state_date,

					qty, sale_amt, clm_amt, sale_fee,
					sale_clm_amt, dc_amt, coupon_amt, dlv_amt,

					sale_net_taxation_amt, sale_net_taxfree_amt, sale_net_amt, tax_amt,

					fee_ratio, fee, fee_dc_amt, allot_amt, etc_amt, fee_net, acc_amt, bigo
				)
				SELECT
					0 as acc_idx, '$com_id' as com_id, '$sdate' as sday, '$edate' as eday,
					w.type, w.ord_opt_no, w.state_date,

					w.qty AS qty, w.sale_amt, w.clm_amt, w.fee as sale_fee,
					0 as sale_clm_amt, w.dc_apply_amt, w.coupon_apply_amt, w.dlv_amt,

					( w.sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_taxation_amt,
					'0' as sale_net_taxfree_amt,
					( w.sale_net_amt + w.dlv_amt + w.etc_amt ) as sale_net_amt,
					floor(( w.sale_net_amt + w.dlv_amt ) / 11) as tax_amt,

					/* 본사 수수료 */
					ROUND(w.fee_ratio, 2) AS fee_ratio,
					w.fee,
					w.dc_apply_amt as fee_dc_amt,
					/*( w.coupon_apply_amt - w.allot_amt ) as fee_allot_amt, */
					( w.allot_amt ) AS fee_allot_amt,
					w.etc_amt as fee_etc_amt,
					( w.fee - w.dc_apply_amt ) as fee_net_amt,

					/* 정산 금액 */
					(( w.sale_net_amt + w.dlv_amt + w.etc_amt ) - ( w.fee - w.dc_apply_amt ) ) as acc_amt,

					'' AS bigo
				FROM
				(
					SELECT
						ord_opt_no
						, ROUND(SUM(type)) AS type
						, MAX(state_date) AS state_date
						, SUM(qty) AS qty, SUM(sale_qty) AS sale_qty
						, SUM(sale_amt) AS sale_amt, SUM(clm_amt) AS clm_amt
						, SUM(coupon_apply_amt) AS coupon_apply_amt
						, SUM(/*dc_apply_amt*/ 0) AS dc_apply_amt
						, SUM(sale_amt + clm_amt - dc_apply_amt - ( coupon_apply_amt - allot_amt) ) AS sale_net_amt
						, SUM(dlv_amt) AS dlv_amt
						, SUM(wonga) AS wonga, SUM(fee_ratio) AS fee_ratio, SUM(fee) AS fee
						, SUM(etc_amt) AS etc_amt
						, SUM(allot_amt) AS allot_amt
						, SUM(com_coupon_apply_amt) as com_coupon_apply_amt
					FROM
					(
						SELECT
							e.ord_opt_no,0 AS type, MAX(e.etc_day) AS state_date,0 AS qty, 0 AS sale_qty,
							0 AS sale_amt, 0 AS clm_amt, 0 AS coupon_apply_amt, 0 AS dc_apply_amt,
							0 AS dlv_amt, 0 AS wonga, 0 AS fee_ratio, 0 AS fee, 0 AS allot_amt, SUM(e.etc_amt) AS etc_amt,
							0 as com_coupon_apply_amt
						FROM
							account_etc e INNER JOIN order_opt o ON e.ord_opt_no = o.ord_opt_no
						WHERE
							e.etc_day >= '$sdate' AND e.etc_day <= '$edate' AND o.com_id = '$com_id'
						GROUP BY
							e.ord_opt_no

						UNION ALL

						SELECT
							ord_opt_no, type, state_date, qty, sale_qty,
							sale_amt, clm_amt, coupon_apply_amt, dc_apply_amt,
							( a.dlv_amt + dlv_ref_amt ) AS dlv_amt, wonga, fee_ratio,
							IF(c.margin_type = 'WONGA',
							IF(qty = 0, 0, (sale_amt + clm_amt - wonga)),
							round(( sale_amt + clm_amt ) * fee_ratio / 100)) AS fee,
							( coupon_apply_amt - coupon_allot_amt ) AS allot_amt,

							/* 클레임 발생으로 쿠폰적용 금액이 (-) 인 경우 0 으로..
							IF (
								coupon_apply_amt <= 0,
								0,
								( coupon_apply_amt - com_coupon_apply_amt )
							) AS allot_amt, */

							0 as etc_amt,
							com_coupon_apply_amt
						FROM
						(
							SELECT
								ord_opt_no,com_id
								, SUM(distinct(if(ord_state = 30,30,ord_state))) as type
								, MAX(ord_state_date) AS state_date
								, SUM(qty) AS qty
								, SUM(IF(ord_state = 30, qty,0)) AS sale_qty
								, SUM(IF(ord_state = 30, price*qty, 0)) AS sale_amt
								, SUM(IF(ord_state IN (60, 61), price*qty, 0)) AS clm_amt
								, SUM(IF(ord_state = 30, coupon_apply_amt, -1 * coupon_apply_amt)) AS coupon_apply_amt
								, SUM(IF(ord_state = 30, coupon_allot_amt, -1 * coupon_allot_amt)) AS coupon_allot_amt
								, SUM(IF(ord_state = 30, /*dc_apply_amt*/ 0, -1 * /*dc_apply_amt*/ 0)) AS dc_apply_amt
								, SUM(IF(ord_state = 30, dlv_amt, -1 * dlv_amt)) AS dlv_amt
								, SUM(dlv_ref_amt) AS dlv_ref_amt

								, ROUND(wonga * qty) AS wonga
								, ROUND(100 * (1 - MAX(wonga) / MAX(price)), 2) AS fee_ratio
								, SUM(com_coupon_apply_amt) AS com_coupon_apply_amt
							FROM
							(
								SELECT
									w.ord_opt_no, w.com_id, o.ord_no, w.ord_state_date, w.ord_state,
									IF(w.ord_state = 30, w.qty, w.qty * -1) AS qty,
									ABS(w.price) AS price, ABS(w.wonga) AS wonga, w.coupon_apply_amt,IFNULL( /*w.dc_apply_amt*/ 0,0) as dc_apply_amt,
									ROUND(IF(IFNULL(w.com_coupon_ratio, 0) > 1,IFNULL(w.com_coupon_ratio, 0) / 100,IFNULL(w.com_coupon_ratio, 0))
										* IFNULL(w.coupon_apply_amt, 0)) AS coupon_allot_amt,
									IFNULL(w.dlv_amt, 0) AS dlv_amt,
									IFNULL(w.dlv_ret_amt, 0) + IFNULL(w.dlv_add_amt, 0) - IFNULL(w.dlv_enc_amt, 0) AS dlv_ref_amt,
									/* 업체 쿠폰 부담 금액 ( 2010.10.07 추가 ) */
									ROUND(
										IF( IFNULL(w.com_coupon_ratio, 0) > 1, IFNULL(w.com_coupon_ratio, 0) / 100, IFNULL(w.com_coupon_ratio, 0) ) * w.coupon_apply_amt
									) AS com_coupon_apply_amt
								FROM
									order_opt o INNER JOIN order_opt_wonga w ON o.ord_opt_no = w.ord_opt_no
								WHERE
									w.ord_state_date >= '$sdate'
									AND w.ord_state_date <= '$edate'
									AND w.ord_state IN (30,60,61)
									AND w.com_id = '$com_id'
									AND o.ord_state >= 30 /* 결제 오류 발생시 order_opt_wonga 가 자료가 입력 되는 경우 처리 */
							)
							a GROUP BY ord_opt_no,com_id
						)
						a INNER JOIN company c ON a.com_id = c.com_id
					)
					a GROUP BY ord_opt_no
				)
					w INNER JOIN order_opt o ON o.ord_opt_no = w.ord_opt_no
					INNER JOIN goods g ON o.goods_no = g.goods_no AND o.goods_sub = g.goods_sub
			";
			DB::insert($sql);

			$sql	= "
				INSERT into account_closed
				(
					com_id,sday,eday,
					sale_amt, clm_amt, sale_fee, dc_amt, coupon_amt, dlv_amt,
					sale_net_taxation_amt, sale_net_taxfree_amt, sale_net_amt, tax_amt,
					fee, fee_dc_amt, allot_amt, etc_amt, fee_net, acc_amt ,
					closed_yn, admin_id, admin_nm, reg_date, upd_date
				)
				SELECT
					com_id,sday,eday,
					sum(sale_amt) as sale_amt, sum(clm_amt), sum(sale_fee), sum(dc_amt), sum(coupon_amt),sum(dlv_amt),
					sum(sale_net_taxation_amt), sum(sale_net_taxfree_amt), sum(sale_net_amt),sum(tax_amt),
					sum(fee), sum(fee_dc_amt), sum(allot_amt), sum(etc_amt), sum(fee_net), sum(acc_amt),
					'N', :id, :name, now() as reg_date, now() as upd_date
				FROM
					account_closed_list
				WHERE
					com_id = :com_id and sday = :sday and eday = :eday
				GROUP BY
					com_id,sday,eday
			";
			DB::insert($sql, ['id' => $id, 'name' => $name, 'com_id' => $com_id, 'sday' => $sdate, 'eday' => $edate]);

			$acc_idx = DB::table('account_closed')->latest('idx')->first()->idx;

			$sql	= "
				UPDATE account_closed_list set
					acc_idx = :acc_idx
				WHERE
					com_id = :com_id and sday = :sday and eday = :eday
			";
			DB::update($sql, ['acc_idx' => $acc_idx, 'com_id' => $com_id, 'sday' => $sdate, 'eday' => $edate]);

			DB::commit();

		} catch(Exception $e) {

			DB::rollBack();
			$code	= "999";
			$msg	= $e->getMessage();

		}

		return response()->json([
			"code"	=> $code,
			"msg"	=> $msg
		]);
	}
}