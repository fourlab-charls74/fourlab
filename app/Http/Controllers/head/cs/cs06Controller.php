<?php

namespace App\Http\Controllers\head\cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;

class cs06Controller extends Controller
{
	public function index() {
		$today = date("Y-m-d");
        $edate = $today;
        $sdate = date('Y-m-d', strtotime(-1 .'month'));

		$values = [
			'sdate'	=> $sdate,
			'edate'	=> $edate,
			'today'	=> $today
		];
		return view( Config::get('shop.head.view') . '/cs/cs06',$values);
	}

	public function search(Request $request){
		$sdate = $request->input("sdate");
		$edate = $request->input("edate");
		$pay_type = $request->input("pay_type");
		$ord_no = $request->input("ord_no");
		$clm_series_no = $request->input("clm_series_no");

		$where = "";
		$inner_where = '';

		if( $ord_no != ""){
			$where .= " and b.ord_no = '$ord_no' ";
		}

		$sumpaytype = 0;
		if($pay_type != ""){
			//$as_pay_type = explode(",",$pay_type);
			$sumpaytype = array_sum($pay_type);
			if($sumpaytype > 0){
				$where .= " and ( e.pay_type & $sumpaytype ) > 0 ";
			}
		}

		if( $clm_series_no != ""){
			$where .= " and a.clm_series_no = '$clm_series_no' ";
		}

		$sql = "
			select
				a.clm_no,b.ord_no,b.ord_opt_no,f.user_nm,f.user_id,
                                pay_type.code_val pay_type,e.escw_use, e.st_cd,if(a.refund_yn = 'Y', e.pay_amt,'') as pay_amt,
                                a.refund_bank,
                                a.refund_amt,ifnull(e.tno,'') as tno,
                                if(a.refund_yn = 'y','Y','N') as refund_yn,ord_state.code_val as ord_state_nm,cs.code_val as clm_state_nm,
                                cd.code_val as clm_reason,a.memo,
                                a.clm_state,
                                a.req_nm,a.req_date,a.end_nm,a.end_date,
                                e.confirm_id
			from claim a
				inner join order_opt b on a.ord_opt_no = b.ord_opt_no
				inner join order_mst f on b.ord_no = f.ord_no
				inner join goods c on b.goods_no = c.goods_no and b.goods_sub = c.goods_sub
				left outer join payment e on b.ord_no = e.ord_no and e.pay_stat = '1'
                                left outer join code ord_state on ord_state.code_kind_cd = 'G_ORD_STATE' and b.ord_state = ord_state.code_id
                                left outer join code cs on cs.code_kind_cd = 'G_CLM_STATE' and cs.code_id = a.clm_state
                                left outer join code pay_type on pay_type.code_kind_cd = 'G_PAY_TYPE'  and e.pay_type = pay_type.code_id
				left outer join code cd on cd.code_kind_cd = 'G_CLM_REASON' and cd.code_id = a.clm_reason
			where a.proc_date >= '$sdate' and a.proc_date < date_add('$edate',interval 1 day)
                                $where $inner_where
				and a.clm_state = 51 and a.refund_yn = 'y' and b.ord_opt_no = a.refund_no
				and if((e.pay_type & 16) = 16 and substr(tno,1,8) = date_format(now(),'%Y%m%d'),0,1) = 1
			order by ord_no desc
		";
		

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
				"page" => -1,
				"page_cnt" => 1,
				"page_total" => -1
			),
			"body" => $result
		]);
	}

	public function Refund($res_cd = "", $res_msg = "", Request $request){
		$conf = new Conf();
		$cfg_shop_name				= $conf->getConfigValue("shop","name");

		$ord_no		= $request->input("ord_no");
		$ord_opt_no	= $request->input("ord_opt_no");

		$IsGroupDlv = true;

		$ord_amt = "";
		$pay_amt = "";
		$pay_point = 0;
		$pay_baesong = "";
		$coupon_amt = "";
		$dc_amt = "";
		$pay_fee = "";
		$pay_type = "";
		$pay_nm = "";
		$refunded_amt = 0;
		$tno =""; 

		// 주문 건수
		$sql = "
			select count(*) as cnt from order_opt where ord_no = '$ord_no'
		";
		$row = DB::selectOne($sql);
		$ord_cnt = $row->cnt;

		// 주문 & 입금정보
		$sql = "
			select
				a.ord_state,a.ord_amt, a.add_dlv_fee,
				b.pay_type, b.pay_nm, b.pay_amt, b.pay_point, b.pay_baesong, b.coupon_amt, b.dc_amt, 0 as pay_fee,
				b.bank_inpnm,bank_code,bank_number,tno,card_name,c.code_val as pay_name
			from order_mst a
				inner join payment b on a.ord_no = b.ord_no
				left outer join code c on c.code_kind_cd = 'G_PAY_TYPE' and b.pay_type = c.code_id
			where a.ord_no = '$ord_no'
		";
		$row_mst = DB::selectOne($sql);
		if($row_mst){

			$ord_state		= $row_mst->ord_state;
			$ord_amt		= $row_mst->ord_amt;
			$add_dlv_fee	= $row_mst->add_dlv_fee;

			$pay_amt		= $row_mst->pay_amt;
			$pay_point		= $row_mst->pay_point;
			$pay_baesong	= $row_mst->pay_baesong;
			$coupon_amt		= $row_mst->coupon_amt;
			$dc_amt			= $row_mst->dc_amt;
			$pay_fee		= $row_mst->pay_fee;

			$pay_type		= $row_mst->pay_type;
			$pay_name		= $row_mst->pay_name;
			$pay_nm			= $row_mst->pay_nm;
			$card_name		= $row_mst->card_name;
			$tno			= $row_mst->tno;

		}else{
			// 주문 정보 또는 입금 정보가 없는 경우는 에러처리!!!
		}


		//
		//	환불정보는 공유
		//

		$refund_no = "";

		$sql = "
			select refund_no
			from claim
			where ord_opt_no = '$ord_opt_no'
		";
		$row_claim = DB::selectOne($sql);
		if($row_claim->refund_no){
			$refund_no = $row_claim->refund_no;
		}
		
		// 환불정보
		$sql = "
			select
				clm_state, cs.code_val as clm_state_nm, refund_price, refund_dlv_amt, refund_dlv_ret_amt, refund_dlv_enc_amt,
				refund_point_amt, refund_coupon_amt, 0 as refund_pay_fee, refund_etc_amt, refund_amt,
				refund_bank, refund_account, refund_nm,memo
			from claim a
				left outer join code cs on cs.code_kind_cd = 'G_CLM_STATE' and cs.code_id = a.clm_state
			where a.ord_opt_no = '$refund_no'
		";
		$row = DB::selectOne($sql);

		$refund_clm_state		= "";
		$refund_clm_state_nm	= "";
		$refund_price			= "";
		$refund_dlv_amt			= "";
		$refund_dlv_ret_amt		= "";
		$refund_dlv_pay_amt		= "";
		$refund_dlv_enc_amt		= "";
		$refund_point_amt		= $pay_point;
		$refund_coupon_amt		= "";
		$refund_pay_fee			= 0;
		$refund_etc_amt			= 0;
		$refund_amt				= "";
		$refund_bank			= "";
		$refund_account			= "";
		$refund_nm				= "";
		$refund_memo			= "";
		
		if($row->clm_state){
			$refund_clm_state		= $row->clm_state;
			$refund_clm_state_nm	= $row->clm_state_nm;
			$refund_price			= $row->refund_price;
			$refund_dlv_amt			= $row->refund_dlv_amt;
			$refund_dlv_ret_amt		= $row->refund_dlv_ret_amt;
			$refund_dlv_enc_amt		= $row->refund_dlv_enc_amt;
			$refund_point_amt		= $row->refund_point_amt;
			$refund_coupon_amt		= $row->refund_coupon_amt;
			$refund_pay_fee			= $row->refund_pay_fee;
			$refund_amt				= $row->refund_amt;
			$refund_etc_amt			= $row->refund_etc_amt;
			$refund_bank			= $row->refund_bank;
			$refund_account			= $row->refund_account;
			$refund_nm				= $row->refund_nm;
			$refund_memo			= $row->memo;

		} else {
		}

		// 환불금액
		if($tno != ""){
			$sql = "
				select sum(ifnull(c.refund_amt,0)) as refunded_amt
				from (
					select ord_no
					from payment where tno = '$tno' and ord_no <> ''
				) a inner join order_opt o on a.ord_no = o.ord_no
					inner join claim c on o.ord_opt_no = c.ord_opt_no
				where c.clm_state = 61
			";
		} else {
			$sql = "
				select
					sum(ifnull(d.refund_amt,0)) as refunded_amt
				from order_opt a inner join claim d on a.ord_opt_no = d.ord_opt_no
				where a.ord_no = '$ord_no'
			";
		}
		$row_opt = DB::selectOne($sql);
		if($row_opt->refunded_amt){
			$refunded_amt = $row_opt->refunded_amt;
		}



		// 그룹 주문
		if($IsGroupDlv){
			$sql = "
				select
					if(g.com_type = 1, g.com_type,a.com_id) as com_id,
					count(*) as cnt,
					sum(a.dlv_amt) as dlv_amt,
					sum(ifnull(c.dlv_add_amt,0)) as dlv_add_amt
				from order_opt a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
					left outer join claim c on a.ord_opt_no = c.ord_opt_no
				where a.ord_no = '$ord_no'
				group by if(g.com_type = 1, g.com_type,a.com_id)
			";
			$rs_ord = DB::select($sql);

			$group_dlv = array();

			foreach($rs_ord as $rs){
				$group_dlv[$rs->com_id]["cnt"] = $rs->cnt;
				$group_dlv[$rs->com_id]["dlv_amt"] = $rs->dlv_amt;
				$group_dlv[$rs->com_id]["dlv_add_amt"] = $rs->dlv_add_amt;

			}

		} else {

			$sql = "
				select
					count(*) as cnt,
					sum(a.dlv_amt) as dlv_amt,
					sum(ifnull(c.dlv_add_amt,0)) as dlv_add_amt
				from order_opt a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
					left outer join claim c on a.ord_opt_no = c.ord_opt_no
				where a.ord_no = '$ord_no'
			";
			$rs = $conn->Execute($sql);
			$row = $rs->fields;

			$group_dlv = array();
			$group_dlv["1"]["cnt"] = $ord_cnt;
			$group_dlv["1"]["dlv_amt"] = $pay_baesong;
			$group_dlv["1"]["dlv_add_amt"] = $rs->dlv_add_amt;

		}

		
		// 주문 상품
		$sql = "
			select
				a.ord_opt_no, a.p_ord_opt_no, a.ord_state,a.clm_state,if(ifnull(a.clm_state,0) = 0,
					(select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = a.ord_state),
					(select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = a.clm_state)
				) as state,
				if(g.com_type = 1, g.com_type,a.com_id) as com_id,
				if(g.com_type = 1, '$cfg_shop_name',e.com_nm) as com_nm,
				a.goods_nm, replace(a.goods_opt,'^',' : ') as opt_nm, a.price, a.qty, a.price * a.qty as amt, a.coupon_amt, a.dc_amt, 0 as pay_fee,
				ifnull(a.dlv_amt,0) as dlv_amt,
				ifnull(d.dlv_type,'') as clm_dlv_type,
				ifnull(d.dlv_cm,'') as clm_dlv_cm,
				ifnull(d.dlv_amt,'') as clm_dlv_amt,
				ifnull(d.dlv_ret_amt,'') as clm_dlv_ret_amt,
				ifnull(d.dlv_add_amt,'') as clm_dlv_add_amt,
				ifnull(d.dlv_enc_amt,'') as clm_dlv_enc_amt,
				ifnull(d.ref_amt,'') as ref_amt,
				ifnull(d.refund_no,0) as refund_no,
				ifnull(d.refund_amt,'') as refund_amt
			from order_opt a
				inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				inner join company e on a.com_id = e.com_id
				left outer join coupon b on a.coupon_no = b.coupon_no
				left outer join claim d on a.ord_opt_no = d.ord_opt_no
			where a.ord_no = '$ord_no'
			order by com_id,ord_opt_no desc
		";
		$result = DB::select($sql);

		$prds = array();
		$pre_com_id = "";
		
		foreach($result as $row){

			$class = "";
			if($row->ord_opt_no == $ord_opt_no){
				//$class ="choice";
				$p_ord_opt_no = $row->p_ord_opt_no;
			}

			// 배송비 및 열수
			if($IsGroupDlv){
				$com_id = $row->com_id;
				$dlv_amt = $row->dlv_amt;
				$dlv_grp_amt = $group_dlv[$com_id]["dlv_amt"];
				$dlv_grp_add_amt = $group_dlv[$com_id]["dlv_add_amt"];
				$dlv_grp_cnt = ($pre_com_id != $row->com_id)? $group_dlv[$com_id]["cnt"]:"";
			} else {
				$com_id = "1";
				$dlv_amt = $pay_baesong;
				$dlv_grp_amt = $group_dlv[$com_id]["dlv_amt"];
				$dlv_grp_add_amt = $group_dlv[$com_id]["dlv_add_amt"];
				$dlv_grp_cnt = $ord_cnt;
			}

			array_push($prds,
				array(
					"class"				=> "",
					"refund_no"			=> $row->refund_no,
					"ord_opt_no"		=> $row->ord_opt_no,
					"ord_state"			=> $row->ord_state,
					"clm_state"	 		=> $row->clm_state,
					"state"				=> $row->state,
					"com_id"			=> $com_id,
					"com_nm"			=> $row->com_nm,
					"goods_nm"			=> $row->goods_nm,
					"goods_snm"			=> $row->goods_nm,
					"opt_nm"			=> $row->opt_nm,
					"price"				=> $row->price,
					"qty"				=> $row->qty,
					"amt"				=> $row->amt,
					"dc_amt"			=> $row->dc_amt,
					"pay_fee"			=> $row->pay_fee,
					"coupon_amt"		=> $row->coupon_amt + $row->dc_amt,
					"dlv_amt"			=> $dlv_amt,
					"dlv_grp_cnt"		=> $dlv_grp_cnt,
					"dlv_grp_amt"		=> $dlv_grp_amt,
					"dlv_grp_add_amt"	=> $dlv_grp_add_amt,
					"clm_dlv_type"		=> $row->clm_dlv_type,
					"clm_dlv_cm"		=> $row->clm_dlv_cm,
					"clm_dlv_amt"		=> $row->clm_dlv_amt,
					"clm_dlv_ret_amt"	=> $row->clm_dlv_ret_amt,
					"clm_dlv_add_amt"	=> $row->clm_dlv_add_amt,
					"clm_dlv_enc_amt"	=> $row->clm_dlv_enc_amt,
					"ref_amt"			=> $row->ref_amt
				)
			);

			$pre_com_id = $com_id;

		}



		$values = [
			"res_cd" => $res_cd,
			"res_msg" => $res_msg,

			"ord_no" => $ord_no,
			"ord_opt_no" => $ord_opt_no,
			"p_ord_opt_no" => $p_ord_opt_no,
			"refund_no" => $refund_no,

			"ord_state" => $ord_state,
			"ord_cnt" => $ord_cnt,
			"ord_amt" => $ord_amt,
			"add_dlv_fee" => $add_dlv_fee,
			"pay_amt" => $pay_amt,
			"pay_point" => $pay_point,
			"pay_baesong" => $pay_baesong,
			"coupon_amt" => $coupon_amt,
			"dc_amt" => $dc_amt+$coupon_amt,
			"pay_fee" => $pay_fee,
			"refunded_amt" => $refunded_amt,
			"bal_amt" => $pay_amt - $refunded_amt,

			"pay_type" => $pay_type,
			"pay_name" => $pay_name,
			"pay_nm" => $pay_nm,
			"tno" => $tno,

			"is_dlv_add" => ($add_dlv_fee > 0)? "Y":"N",

			"refund_clm_state" => $refund_clm_state,
			"refund_clm_state_nm" => $refund_clm_state_nm,
			"refund_price" => $refund_price,
			"refund_dlv_amt" => $refund_dlv_amt,
			"refund_dlv_ret_amt" => $refund_dlv_ret_amt,
			"refund_dlv_pay_amt" => $refund_dlv_pay_amt,
			"refund_dlv_enc_amt" => $refund_dlv_enc_amt,
			"refund_point_amt" => $refund_point_amt,
			"refund_coupon_amt" => $refund_coupon_amt,
			"refund_pay_fee" => $refund_pay_fee,
			"refund_etc_amt" => abs($refund_etc_amt),
			"refund_etc_gubun" => ($refund_etc_amt >= 0)? "m":"p",
			"refund_amt" => $refund_amt,

			"refund_bank" => $refund_bank,
			"refund_account" => $refund_account,
			"refund_nm" => $refund_nm,

			"memo" => $refund_memo,
			"prds"	=> $prds
		];

		
		return view( Config::get('shop.head.view') . '/cs/cs06_refund',$values);
	}



	/*
		Function: CheckRealBalanceAmt
		묶음주문 부분 취소시 환불 금액 체크
	*/

	function CheckRealBalanceAmt(Request $request){

		$ord_no		= Request("ord_no");
		$tno		= Request("tno");
		$bal_amt	= str_replace(",","",Request("bal_amt"));
		$pay_amt	= str_replace(",","",Request("pay_amt"));

		$result_code = 0;

		// 묶음주문 부분 취소시 처리
		if($tno != ""){
			$sql = "
				select sum(ifnull(c.refund_amt,0)) as refunded_amt
				from (
					select ord_no
					from payment where tno = '$tno' and ord_no <> ''
				) a inner join order_opt o on a.ord_no = o.ord_no
					inner join claim c on o.ord_opt_no = c.ord_opt_no
				where c.clm_state = 61
			";
		} else {
			$sql = "
				select
					sum(ifnull(d.refund_amt,0)) as refunded_amt
				from order_opt a inner join claim d on a.ord_opt_no = d.ord_opt_no
				where a.ord_no = '$ord_no'
			";
		}

		$row = DB::selectOne($sql);
		if($row){
			$refunded_amt = $row->refunded_amt;
		}

		$real_bal_amt = $pay_amt - $refunded_amt;

		if($bal_amt != $real_bal_amt){
			$result_code = "1";
		} else{
			$result_code = "0";
		}

		return response()->json([
			"code" => 200,
			"result_code" => $result_code
		]);
	}

	/*
		Function: RefundCommand
		환불 처리
	*/
	public function RefundCommand(Request $request){
		$return_code= 0;

		// 설정 값 얻기
		$conf = new Conf();
		$cfg_refund_msg	= $conf->getConfigValues("sms","refund_msg");

		$cfg_shop_name	= $conf->getConfigValue("shop","name");
		$cfg_kakao_yn	= $conf->getConfigValue("kakao","kakao_yn");
		$cfg_sms		= $conf->getConfig("sms");
		$cfg_sms_yn		= $conf->getValue($cfg_sms,"sms_yn");
		$cfg_refund_yn	= $conf->getValue($cfg_sms,"refund_yn");
		
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$user_arr = [
			'id'=>$id,
			'name' => $name 
		];

		$ord_no			= $request->input("ord_no");
		$ord_opt_no		= $request->input("ord_opt_no");
		$tno			= $request->input("tno");

		$bal_amt		= str_replace(",","",$request->input("bal_amt"));
		$refund_amt		= str_replace(",","",$request->input("refund_amt"));
		$pay_amt		= str_replace(",","",$request->input("pay_amt"));
		$pay_type		= $request->input("pay_type");
		$pay_type_nm	= $request->input("pay_type_nm");

		$res_cd			= "";
		$res_msg		= "";

		$sql = "
			select count(*) as cnt
			from claim c inner join order_opt o on c.ord_opt_no = o.ord_opt_no
			where o.ord_no = '$ORD_NO' and c.refund_no = '$ORD_OPT_NO' and o.clm_state <> 51
		";

		$rowRefund = DB::selectOne($sql);
		if($rowRefund->cnt > 0){
			$return_code= 0;
			$res_cd = "ERROR";
			$res_msg = "클레임 상태가 '환불처리중' 이 아닌 주문상품이 있습니다. '환불처리중' 으로 변경 후 처리하여 주십시오.";
		}else{
			// 중복사용제한
			$sql = "
				select count(*) as cnt from locked where type = 'refund' and idx = '$ORD_OPT_NO'
			";

			$row = DB::selectOne($sql);

			if($row->cnt > 0){
				$return_code= -1;
				$res_cd = "LOCK";
				$res_msg = "환불처리중";
			}else{
			
				$sql = "
					insert into locked ( type,idx, id, ut ) values ( 'refund', '$ORD_OPT_NO', '$id', now())
				";
				$ret = DB::insert($sql);
				/*
				try {
					DB::selectOne($sql);
					$ret = 1;
				} catch(Exception $e){
					$ret = 0;
				}
				*/
				//$ret = &$conn->Execute($sql);

				//if($ret == 1){
				if($ret){
					$return_code= 1;
					// 주문 정보 얻기
					$sql = "
						select
							o.ord_opt_no, o.ord_state, o.qty, o.ord_no, o.goods_no, o.goods_sub, o.goods_opt, o.add_point, o.pay_type,
							c.clm_no, c.clm_state, c.clm_type, c.clm_reason, c.refund_yn, c.refund_amt, p.fintech
						from order_opt o
							inner join payment p on o.ord_no = p.ord_no
							inner join claim c on o.ord_opt_no = c.ord_opt_no
						where o.ord_opt_no = '$ORD_OPT_NO' and c.clm_state = 51 and c.refund_yn = 'y' and o.ord_state >= 10
					";
					$row = DB::selectOne($sql);

					if($row){
						$ord_opt_no = $row->ord_opt_no;
						$ord_state = $row->ord_state;
						$ord_qty = $row->qty;
						$fintech = $row->fintech;
						$ord_no = $row->ord_no;
						$refund_amt = $row->refund_amt;
						$r_pay_type = $row->pay_type;

						//////////////////////////////////////////////////////////////////////////////////////////////////////
						//
						// PG 사 환불처리

						// 테스트를 위한 코드
						//

						// 취소 : CL, 부분취소 : RN

						if($pay_amt != $refund_amt){
							if($fintech == "kakaopay"){
								$refund_type = "SC";
							} else {
								if(($pay_type & 2) == 2){
									$refund_type = "RN";
								} else if(($pay_type & 16) == 16){
									$refund_type = "ST";
								}
							}
						} else {
							$refund_type = "CL";
						}

						$ip = $_SERVER["REMOTE_ADDR"];
						$memo = sprintf("%s (%s%s)","환불완료", $pay_type_nm, ($refund_type == "CL")? "취소":"부분취소");
						/*
						$pg = new pay();
						list($res_cd, $res_msg ) = $pg->mod($refund_type,$tno,$ord_no,$ip,$memo,$refund_amt,$bal_amt);
						*/

						// 테스트 코드
						$res_cd = "0000";
						//printf("%s %s",$res_cd,$res_msg);

						//////////////////////////////////////////////////////////////////////////////////////////////////////
						// PG 환불처리

						if($res_cd == "0000"){


							$claim = new Claim();
							$claim->__construct($user_arr);

							$point = new Point();
							$point->__construct($user_arr);

							$gift = new Gift();

							$sql = "
								select
									o.ord_no,c.clm_no,c.ord_opt_no,c.refund_no,o.clm_state,o.add_point
								from claim c inner join order_opt o on c.ord_opt_no = o.ord_opt_no
								where o.ord_no = '$ord_no' and c.refund_no = '$ord_opt_no' and o.clm_state = 51
							";
							//$rsRefund = &$conn->Execute($sql);
							$rsRefund = DB::select($sql);
							//while(!$rsRefund->EOF){
							foreach($rsRefund as $rowRefund ){

								$claim->SetClmNo( $rowRefund->clm_no );
								$claim->SetOrdOptNo($rowRefund->ord_opt_no);
								$claim->MinusSales( $next_clm_state = 61, $ord_no, $rowRefund->ord_opt_no );

								$claim->CompleteRefund($refund_type,$memo);
								$claim->ChangeClaimStateOrder("61");

								// 지급포인트 뺏어오기
								//$conn->debug=true;
								$point->SetOrdNo( $ord_no );
								$point->Refund( $rowRefund->ord_opt_no, $rowRefund->add_point,61 );
								//debugSQL();exit;

								// 지급된 사은품 환불처리
								$gift->Refund( $ord_no, $rowRefund->ord_opt_no );

								//$rsRefund->MoveNext();
							}

							$param = array(
								"ord_state"=> $ord_state,
								"clm_state"=> 61,
								"cs_form"=> "01",
								"memo"=> $memo
							);
							$claim->InsertMemo($param);

							if($cfg_sms_yn == "Y"){
								if($cfg_refund_yn == "Y"){

									////////// 환불완료시 문자보내기 시작 //////////
									$sql05 = "
										select
											b.user_nm, b.mobile
										from order_opt a
										inner join order_mst b on a.ord_no = b.ord_no
										where a.ord_opt_no = '$ord_opt_no'
									";

									//$rs05 = &$conn->Execute($sql05);
									$row05 = DB::selectOne($sql05);
									if ($row05) {

										$user_name = $row05->user_nm;
										$user_mobile = $row05->mobile;

										// 부분환불 일 경우 메세지 처리

										if(($r_pay_type & 2) == 2){
											$msg = $cfg_refund_msg[0]->value;
										} else if(($r_pay_type & 16) == 16){
											$msg = $cfg_refund_msg[1]->value;
										} else {
											$msg = $cfg_refund_msg[1]->value;
										}
										$template_code = "OrderCode4";
										$msgarr = array(
											"SHOP_NAME" => $cfg_shop_name,
											"USER_NAME" => $user_name,
											"ORDER_NO" => $ord_no,
											"ORDER_AMT" => $refund_amt,
											"SHOP_URL" => 'http://www.netpx.co.kr/app/mypage/order_list'
										);
										$btnarr = array(
											"BUTTON_TYPE" => '1',
											"BUTTON_INFO" => '주문내역 보기^WL^http://www.netpx.co.kr/app/mypage/order_list'
										);
										//$sms = new SMS( $conn, $this->user );

										$sms = new SMS([
											'admin_id' => $id,
											'admin_nm' => $name,
										]);


										$sms_msg = $sms->MsgReplace($msg, $msgarr);
										/*
										if($cfg_kakao_yn == "Y" && $template_code != ""){
											$sms->SendKakao($template_code, $user_mobile, $user_name, $sms_msg, $msgarr, '', $btnarr);
										} else {
											$sms->Send($sms_msg, $user_mobile, $user_name);
										}
										*/

										/******************************************************
										* 테스트 위해 아래로 휴대폰 번호 임시 지정
										******************************************************/
										$user_mobile = "010-9877-2675";
										$user_name = "테스트";

										$sms->Send($sms_msg, $user_mobile, $user_name);

									}
									////////// 환불완료시 문자보내기 끝 //////////
								}
							}

						} else {		// PG 승인취소 및 부분 취소 오류
							$return_code= -4;
							$res_cd = "LOCK";
							$res_msg = "승인취소 및 부분 취소 오류";
						}
					} else {
						$return_code= -3;
						$res_cd = "LOCK";
						$res_msg = "주문정보 조회오류";
					}





				} else {
					$return_code= -2;
					$res_cd = "LOCK";
					$res_msg = "환불처리중";
				}
			}
			
		}

		return response()->json([
			"code" => 200,
			"result_code" => $return_code,
			"res_cd" => $res_cd,
			"res_msg"	=> $res_msg
		]);
	
	}

}
