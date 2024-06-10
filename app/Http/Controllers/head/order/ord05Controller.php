<?php

namespace App\Http\Controllers\head\order;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use App\Models\Conf;
use App\Models\Order;
use App\Models\SMS;
use Carbon\Carbon;

class ord05Controller extends Controller
{
	public function index() {
	  	$conf = new Conf();

		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$ord_states = SLib::getCodes("G_ORD_STATE");
		$clm_states = SLib::getCodes("G_CLM_STATE");
		$stat_pay_types = SLib::getCodes("G_STAT_PAY_TYPE");
		$pay_stats = SLib::getCodes("G_PAY_STAT");
		// Lib::dd($ord_states, $clm_states, $stat_pay_types, $pay_stats);
		
		$bank_info = $conf->getConfigValue("bank", "info"); // getConfigValues로 전부 가져와야하는거 하는지
		// Lib::dd($bank_info);

		$banks = array();
		list($_bank,$_account,$_name) = explode("|",$bank_info);

		$banks = array();
		$banks["key"]	= $_bank . "|" . $_account;
		$banks["value"]	= sprintf("%s [%s]",$_bank,$_account);

		$today = date("Y-m-d");
        $edate = $today;
        $sdate = date('Y-m-d', strtotime(-1 .'month'));

		$values = [
			"admin_id" =>$id,
            "admin_nm" => $name,
			"edate" => $edate,
            "sdate" => $sdate,
			"ord_states" => $ord_states,
			"clm_states" => $clm_states,
			"stat_pay_types" => $stat_pay_types,
			"pay_stats" => $pay_stats,
            'sale_places'   => SLib::getSalePlaces(),
			"banks" => $banks
		];

		return view( Config::get('shop.head.view') . '/order/ord05',$values);
	}

	public function search(Request $request){
		$page = $request->input("page",1);
		if ($page < 1 or $page == "") $page = 1;

		$edate			= $request->input("edate",date("ymd"));
		$sdate			= $request->input("sdate");
		$ord_no			= $request->input("ord_no");
		$user_nm		= $request->input("user_nm");
		$user_id		= $request->input("user_id");
		$r_nm			= $request->input("r_nm");
		$bank_inpnm		= $request->input("bank_inpnm");
		$pay_stat		= $request->input("pay_stat");
		$ord_state		= $request->input("ord_state");
		$clm_state		= $request->input("clm_state");
		$stat_pay_type	= $request->input("stat_pay_type");
		$sale_place		= $request->input("sale_place");
		$cols			= $request->input("cols");
		$limit			= $request->input("limit", 100);
		$not_complex	= $request->input("not_complex");
		$key			= $request->input("key");
		$receipt		= $request->input("receipt");			// 현금영수증
		$diff_amt		= $request->input("diff_amt");		// 입금액 불일치
		$debt_order		= $request->input("debt_order");	// 외상주문
		$confirm		= $request->input("confirm");			// 수동 입금확인
		//$pay_fee		= $request->input("pay_fee");			// 결제수수료 주문

		$where = "";

		$where .= " and m.ord_date >= '$sdate' ";
		$where .= " and m.ord_date < date_add('$edate', interval 1 day) ";

		if($ord_no != "")		$where .= " and m.ord_no = '$ord_no' ";
		if($user_nm != "")		$where .= " and m.user_nm like '$user_nm%' ";
		if($r_nm != "")			$where .= " and m.r_nm like '$r_nm%' ";
		if($user_id != "")		$where .= " and m.user_id like '$user_id%' ";
		if($bank_inpnm != "")	$where .= " and d.bank_inpnm like '$bank_inpnm%' ";


		if($cols != "" && $key != ""){
			if(in_array($cols,array("m.mobile","m.phone","m.r_phone","m.r_mobile"))){
				//$key = replacetel($s_key);
				if($cols == "m.mobile" || $cols == "m.phone" || $cols == "m.r_mobile"){
					$where .= " and $cols = '$key' ";
				} else {
					$where .= " and $cols like '$key%' ";
				}
			} else {
				$where .= " and $cols like '$key%' ";
			}
		}

		if($sale_place != "")	$where .= " and o.sale_place = '$sale_place' ";
		if($ord_state != "")	$where .= " and m.ord_state = '$ord_state' ";
		if($clm_state == "90")$where .= " and o.clm_state = 0 ";
		else{
			if($clm_state != ""){
				$where .= " and o.clm_state = '$clm_state' ";
			}
		}

		// 결제조건
		if($stat_pay_type != ""){
			if($not_complex == "Y"){
				$where .= " and d.pay_type = '$stat_pay_type' ";
			}else{
				$where .= " and (( d.pay_type & $stat_pay_type ) = $stat_pay_type) ";
			}
		}
		if($pay_stat != "")		$where .= " and d.pay_stat = '$pay_stat' ";

		//현금영수증 발행조건
		//현금영수증 발행조건
		if($receipt == "R"){
			$where .= " and d.cash_apply_yn = 'Y' ";
		} elseif($receipt == "Y"){
			$where .= " and d.cash_yn = '$receipt' ";
		}

		if($diff_amt != ""){
			$where .= " and d.pay_amt <> d.confirm_amt ";
		}

		if($debt_order != ""){
			$where .= " and o.ord_state >= 10 and d.pay_stat = '0' ";
		}

		if($confirm != ""){
			$where .= " and d.confirm_id is not null and d.confirm_id <> 'bankda' ";
		}

		//if($pay_fee == "Y") $where .= " and m.pay_fee > 0 ";

		$page_size = $limit;

		$sql = "
			select
				count(distinct(m.ord_no)) as total
			from order_mst m
				inner join order_opt o on m.ord_no = o.ord_no
				left outer join payment d on m.ord_no = d.ord_no
			where 1=1 $where
		";

		$row = DB::select($sql);
		$total = $row[0]->total;

		$page_cnt=(int)(($total-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}
		$arr_header = array("data_cnt"=>$total, "page_cnt"=>$page_cnt);

		if($limit == -1){
			if ($page > 1) $limit = "limit 0";
			else $limit = "";
		} else {
			$limit = " limit $startno, $page_size ";
		}


		$sql = "
			select  SQL_BUFFER_RESULT
				m.ord_no, a.ord_opt_no,
				ord_state.code_val as ord_state_nm,
				ifnull(clm_state.code_val,'') as clm_state_nm,
				m.ord_amt,
				( m.dc_amt + m.coupon_amt ) as sale_amt,
				m.point_amt,
				-- ifnull(m.pay_fee, 0) as pay_fee,
				'' as pay_fee,
				m.recv_amt,
				pay_type.code_val pay_type,
				pay_stat.code_val as pay_stat,
				concat(ifnull(m.user_nm, ''),'(',ifnull(m.user_id, ''),')') as user_nm,
				d.bank_code,
				d.bank_number,
				d.bank_inpnm,
				d.confirm_amt as confirm_amt,
				d.card_msg,
				m.r_nm,
				if(d.cash_apply_yn = 'Y', '신청', '') as cash_apply_yn,
				if(d.cash_yn = 'Y', '발행', '') as cash_yn,
				ord_type.code_val ord_type,
				ord_kind.code_val ord_kind,
				a.com_nm,
				m.ord_date,
				d.pay_date,
				m.dlv_end_date,
				m.user_id
			from order_mst m inner join (
				select
					m.ord_no,
					max(o.ord_opt_no) as ord_opt_no,
					max(o.clm_state) as clm_state,
					group_concat(distinct(c.com_nm) separator ',') as com_nm
				from order_mst m inner join order_opt o on m.ord_no = o.ord_no
					left outer join payment d on m.ord_no = d.ord_no
					left outer join company c on c.com_type = '4' and c.com_id = o.sale_place
				where 1=1 $where
				group by m.ord_no
				order by o.ord_opt_no desc $limit
			) a on m.ord_no = a.ord_no
			left outer join payment d on m.ord_no = d.ord_no
			left outer join code ord_state on (m.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
			left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
			left outer join code pay_type on (d.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
			left outer join code pay_stat on (d.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
			left outer join code ord_type on (m.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
			left outer join code ord_kind on (m.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
		";
		/*
		echo $sql;
		echo "<br>";
		*/

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			),
			"body" => $result
		]);

	}


	public function pay(Request $request){
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$conf = new Conf();
		$user_arr = [
			'id'=>$id,
			'name' => $name
		];

		$cfg_sms				= $conf->getConfig("sms");
        $cfg_sms_yn				= $conf->getValue($cfg_sms,"sms_yn");
		$cfg_out_of_stock_msg	= $conf->getValue($cfg_sms,"out_of_stock_msg");
		$cfg_payment_yn			= $conf->getValue($cfg_sms,"payment_yn");
		$cfg_out_of_stock_yn	= $conf->getValue($cfg_sms,"out_of_stock_yn");
		$cfg_cash_use_yn		= $conf->getConfig("shop","cash_use_yn"); // 현금영수증 사용여부

		$cfg_shop_name = "";
		$cfg_shop_name	= $conf->getConfigValue("shop","name");
		$cfg_kakao_yn	= $conf->getConfigValue("kakao","kakao_yn");
		$shop_tel		= $conf->getConfigValue("shop","phone");

		$pay_result = 1;

		$nodeid			= $request->input("nodeid");

		$ord_no			= $request->input("ord_no");
		$bank			= $request->input("bank");
		$bank_inpnm		= $request->input("bank_inpnm");
		$confirm_amt	= $request->input("confirm_amt");
		$ord_opt_no		= $request->input("ord_opt_no");

		$card_msg		= $request->input("card_msg");

		list($bank_code,$bank_number) = explode("|", $bank);

		$ordclass = new Order();

		$ordclass->SetOrdNo($ord_no);
		$ordclass->__construct($user_arr);

		try {
			DB::beginTransaction();
			
			// 입금상태 확인
			$ret_pay_stat = $ordclass->CheckPayment();

			if($ret_pay_stat != 0 ){ // 입금 확인 상태
				$pay_result = 0;
			} else {	// 입금 정보 정상
				//==============================================================
				// 입금확인 처리 로그 START
				//==============================================================
				$pay_type = $this->GetPayType($ord_no);

				$pay_update_items = [
					"pay_type" => $pay_type,
					"bank_inpnm" => $bank_inpnm,
					"bank_code" => $bank_code,
					"bank_number" => $bank_number,
					"ghost_use" => null,
					"escw_use" => null,
					"tno" => null,
					"card_msg" => $card_msg,
					"confirm_amt" => $confirm_amt,
					"confirm_id" => $id
				];

				DB::table('payment')
					->where('ord_no','=', $ord_no)
					->update($pay_update_items);
				
				$opt_update_items = [
					"pay_type" => $pay_type
				];
				
				DB::table('order_opt')
					->where('ord_no','=', $ord_no)
					->update($opt_update_items);
			

				//==============================================================
				// 입금확인 처리 로그 END
				//==============================================================

				$ret_jaego = $ordclass->CheckJaego($ord_opt_no); // 재고 체크
				$ordclass->SetOrdOptNo($ord_opt_no);	// ord_opt_no

				if($ret_jaego){
					/**
					 * 주문상태 로그
					 */
					$state_log = array("ord_no" => $ord_no, "ord_state" => "10", "comment" => "입금확인", "admin_id" => $id, "admin_nm" => $name);
					$ordclass->AddStateLog($state_log);

					// 매출정보 저장/재고차감/입금완료 처리
					$ret = $ordclass->CompleteOrder();

					//////////////////////////////////////////////////////////////////////////////////////////////////////
					//
					// PG 사 현금영수증 발행

					if($cfg_cash_use_yn == "Y" && $ret == "1")
					{

						// 주문 및 결제 정보 얻기
						$sql = "
							select
								a.ord_no, a.user_nm, a.phone, a.email, a.r_mobile, a.recv_amt
								, b.pay_type, b.cash_apply_yn
								, c.goods_nm
							from order_mst a
								inner join payment b on a.ord_no = b.ord_no
								inner join order_opt c on a.ord_no = c.ord_no
								-- inner join goods d on c.goods_no = d.goods_no and c.goods_sub = d.goods_sub
							where a.ord_no = '$ord_no'
							limit 0,1
						";
						
						$row = DB::selectOne($sql);
						if(!$row){

							$pay_type = $row->pay_type;								// 결제 방식
							$cash_apply_yn = $row->cash_apply_yn;							// 현금영수증 신청

							$req_tx     = "pay";										// 발행
							$trad_time  = date("Ymdhis");								// 원거래 시각

							$ordr_idxx  = $row->ord_no;								// 주문 번호
							$buyr_name  = $row->user_nm;							// 주문자 이름
							$buyr_tel1  = $row->phone;								// 주문자 전화번호
							$buyr_mail  = $row->email;								// 주문자 E-Mail
							$good_name  = substr(trim($row->goods_nm), 0, 30);	// 상품 정보

							// 상품명의 특수문자 제한
							$patten = "/[\^\&\%\'\!\@\#\"]+/";
							$good_name  = preg_replace($patten, "", $good_name);

							$comment		= "";										// 비고
							$corp_type     = "0";										// 사업장 구분(0:직접 판매, 1:입점몰 판매)
							$corp_tax_type = "TG01";									// 과세/면세 구분(TG01:과세, TG02:면세)
							$corp_tax_no   = "";										// 발행 사업자 번호
							$corp_nm       = "";										// 상호
							$corp_owner_nm = "";										// 대표자명
							$corp_addr     = "";										// 사업장 주소
							$corp_telno    = "";										// 사업장 대표 연락처

							$tr_code    = "0";											// 발행용도(0:소득공제용, 1:지출증빙용)
							$id_info    = str_replace("-", "", $row["r_mobile"]);		// 신분확인 ID(핸드폰번호 또는 주민등록번호 -> 현재는 수령자 핸드폰번호로 처리함.)
							$amt_tot    = $row->recv_amt;								// 거래금액 총 합
							$amt_sup    = round(($amt_tot)/1.1);						// 공급가액
							$amt_tax    = $amt_tot - $amt_sup;							// 부가가치세
							$amt_svc    = "0";											// 봉사료

							$mod_type   = "";											// 변경 타입
							$mod_value  = "";											// 변경 요청 거래번호
							$mod_gubn   = "";											// 변경 요청 거래번호 구분
							$mod_mny    = "";											// 변경 요청 금액
							$rem_mny    = "";											// 변경처리 이전 금액

							$admin_id = $id;								// 관리자 아이디
							$admin_nm = $name;							// 관리자 이름

							$param = array(
								"req_tx"		=> $req_tx,
								"trad_time"		=> $trad_time,
								"ordr_idxx"		=> $ordr_idxx,
								"buyr_name"		=> $buyr_name,
								"buyr_tel1"		=> $buyr_tel1,
								"buyr_mail"		=> $buyr_mail,
								"good_name"		=> $good_name,
								"comment"		=> $comment,
								"corp_type"		=> $corp_type,
								"corp_tax_type"	=> $corp_tax_type,
								"corp_tax_no"	=> $corp_tax_no,
								"corp_nm"		=> $corp_nm,
								"corp_owner_nm"	=> $corp_owner_nm,
								"corp_addr"		=> $corp_addr,
								"corp_telno"	=> $corp_telno,
								"tr_code"		=> $tr_code,
								"id_info"		=> $id_info,
								"amt_tot"		=> $amt_tot,
								"amt_sup"		=> $amt_sup,
								"amt_tax"		=> $amt_tax,
								"amt_svc"		=> $amt_svc,
								"mod_type"		=> $mod_type,
								"mod_value"		=> $mod_value,
								"mod_gubn"		=> $mod_gubn,
								"mod_mny"		=> $mod_mny,
								"rem_mny"		=> $rem_mny,
								"admin_id"		=> $admin_id,
								"admin_nm"		=> $admin_nm
							);

							// PG사를 통해 현금영수증 발행
							$res_cd = "";
							$res_msg = "";
							if($pay_type & 1 && $cash_apply_yn == "Y"){
								/*
								$pg_cash = new cash();
								list($res_cd, $res_msg) = $pg_cash->mod($conn, $param);
								*/
							}
						}
					}

				}else{
					/**
					 * 주문상태 로그
					 */
					$state_log = array("ord_no" => $ord_no, "ord_state" => "5", "comment" => "입금확인(품절)");
					$ordclass->AddStateLog($state_log);

					// 재고 없는 경우 주문상태 변경
					$ordclass->OutOfScockAfterPaid();

					if($cfg_sms_yn == "Y" && $cfg_out_of_stock_yn == "Y"){

						// 품절 알림 SMS
						$sql = "
						select user_nm, mobile
						from order_mst
						where ord_no = '$ord_no'
					";
						//$result = $conn->Execute($sql);
						$row = DB::selectOne($sql);
						if ($row) {
							$user_nm = $row->user_nm;		// 주문자이름
							$mobile = $row->mobile;		// 핸드폰번호
						}

						$msgarr = array(
							"SHOP_NAME" => $cfg_shop_name,
						);
						$sms = new SMS([
							'admin_id' => $id,
							'admin_nm' => $name,
						]);
						$sms_msg = $sms->MsgReplace($cfg_out_of_stock_msg, $msgarr);
						if($mobile != ""){
							//$sms->Send($sms_msg, $mobile, $user_nm);
							$sms->SendAligoSMS( $mobile, $sms_msg, $user_nm );
						}

						//$sms->Send($sms_msg, $mobile, $user_nm);
					}

					$pay_result = 2;

				}
				DB::commit();
				//$pay_result = 1;
			}

		} catch(Exception $e) {
			DB::rollback();
			
			$pay_result = 0;

			return response()->json([
				"code"			=> 500,
				"pay_result"	=> $pay_result,
				"msg"			=> $e->getMessage(),
				"nodeid"		=> $nodeid
			]);
		}
		
        return response()->json([
            "code"			=> 200,
            "pay_result"	=> $pay_result,
			"nodeid"		=> $nodeid
        ]);

	}

	// 결제 수단 확인
	function GetPayType($ord_no)
	{
		$pay_type = 1;

		$sql = "
			select pay_point, coupon_amt
			from payment
			where ord_no = '$ord_no'
		";
		$rs = DB::selectOne($sql);

		if(! $rs ){
			$point = $rs->pay_point;
			if($point > 0) $pay_type += 4;

			$coupon = $rs->coupon_amt;
			if($coupon > 0) $pay_type += 8;
		}

		return $pay_type;
	}

}
