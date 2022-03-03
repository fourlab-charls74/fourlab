<?php

namespace App\Http\Controllers\head\order;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;
use App\Models\Order;
use App\Models\SMS;

class ord06Controller extends Controller
{
	public function index() {
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$today = date("Y-m-d");
        $edate = $today;

		$day_w = (date("w")-1);
		$sdate_tiem = strtotime("-$day_w days");
		$monday = date("Y-m-d", $sdate_tiem);
		$sdate = date("Y-m-d", strtotime($monday . "-1 week"));

		$is_yn = SLib::getCodes("G_YN");
		$sql = "select number id, concat(bkname, ' (계좌번호:', number,')') val from bankda_account where use_yn = 'Y'";
		$accounts = DB::select($sql);

		$values = [
			"admin_id" =>$id,
            "admin_nm" => $name,
			"edate" => $edate,
            "sdate" => $sdate,
			"accounts" => $accounts,
			"is_yn_item" => $is_yn

		];
		return view( Config::get('shop.head.view') . '/order/ord06',$values);
	}

	public function search(Request $request){
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$page = $request->input("page",1);
		if ($page < 1 or $page == "") $page = 1;

		$sdate 			= $request->input("sdate");
		$edate 			= $request->input("edate",date("Ymd"));
		$account 		= $request->input("account");	// 입금은행
		$bank_inpnm		= $request->input("bank_inpnm");	// 입금자
		$is_hold		= $request->input("is_hold");	// 보류여부
		$is_matched		= $request->input("is_matched");	// 입금확인여부
		$bank_input		= $request->input("bank_input");	// 입금액
		$ord_no			= $request->input("ord_no");	// 주문번호
		$user_nm		= $request->input("user_nm");	// 주문자
		$r_nm			= $request->input("r_nm");	// 수령자

		$limit			= $request->input("limit",100);

		$where = "";


		$sdate = str_replace("-", "", $sdate);


		
		if($sdate != "" && $edate != ""){
			$where .= " and a.bkdate >= '$sdate' ";
			$where .= " and a.bkdate < DATE_FORMAT(DATE_ADD('$edate', INTERVAL 1 DAY), '%Y%m%d') ";
		}

		if($account != "")	$where .= " and d.number = '$account' ";
		if($bank_inpnm != "")	$where .= " and a.bkjukyo = '$bank_inpnm' ";
		if($is_hold != "")	$where .= " and a.is_hold = '$is_hold' ";
		if($is_matched != "")	$where .= " and a.is_matched = '$is_matched' ";
		if($bank_input != "")	$where .= " and a.bkinput = '$bank_input' ";
		if($ord_no != "")		$where .= " and a.ord_no = '$ord_no' ";
		if($user_nm != "")	$where .= " and b.user_nm = '$user_nm' ";
		if($r_nm != "")		$where .= " and b.r_nm = '$r_nm' ";

		$ip = $_SERVER["REMOTE_ADDR"];

		$page_size = $limit;


		// 갯수 얻기
		$sql = "
			select
				count(*) total
			from bankda_record a
				left outer join order_mst b on a.ord_no = b.ord_no
				left outer join payment c on a.ord_no = c.ord_no
				left outer join bankda_log d on a.log_no = d.no
				left outer join company e on b.sale_place = e.com_id and e.com_type = '4'
			where 1=1 $where
			order by a.no
		";
		$row = DB::selectOne($sql);
		$total = $row->total;

		$page_cnt=(int)(($total-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}
		$arr_header = array("data_cnt"=>$total, "page_cnt"=>$page_cnt);

		if($limit == -1){
			$where_limit = "";
		} else {
			$where_limit = " limit $startno, $page_size ";
		}

		$sql = "
			select
				'' as chk, date_format(a.bkdate, '%Y.%m.%d') as bkdate, d.bkname, d.number, a.bkjukyo, a.bkinput, concat(a.bkcontent,'/',a.bketc) as bkinfo, a.memo
				, a.is_matched, a.is_hold, d.rt, a.matched_dt
				, ifnull(a.ord_no, '선택') as ord_no
				, ifnull(a.ord_nos, '') as ord_nos
				, if(a.ord_no = '' || a.is_matched = 'Y' || a.is_hold = 'Y', '', ifnull(a.expect_ord_no, '')) as expect_ord_no
				, ord_state.code_val as ord_state, pay_type.code_val as pay_type, if(a.is_hold = 'Y', '입금보류', pay_stat.code_val) as pay_stat
				, b.ord_amt, b.point_amt, b.coupon_amt, b.dc_amt
				, b.phone, b.mobile, CONCAT(b.user_nm,'(',b.user_id,')') as user_nm, b.r_nm
				, e.com_nm as sale_price, a.admin_name
				, a.bkdate as h_bkdate, a.no
			from bankda_record a
				left outer join order_mst b on a.ord_no = b.ord_no
				left outer join payment c on a.ord_no = c.ord_no
				left outer join bankda_log d on a.log_no = d.no
				left outer join company e on b.sale_place = e.com_id and e.com_type = '4'
				left outer join code ord_state on (b.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
				left outer join code pay_type on (c.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
				left outer join code pay_stat on (c.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
			where 1=1 $where
			order by a.no
			$where_limit
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
	
	public function account(){

		$values = [
		];

		return view( Config::get('shop.head.view') . '/order/ord06_account',$values);
	}

	public function account_list(){
		$sql = "select '', number, bkname, bankda_id, bankda_pwd, use_yn, no, rt, ut
		from bankda_account
		order by no
		";
		$result = DB::select($sql);
	
		return response()->json([
			"code" => 200,
			"head" => array(
			"total" => count($result),
			"page" => -1,
			"page_cnt" => -1,
			"page_total" => -1
			),
			"body" => $result
		]);

	}

	public function save_account(Request $request){
		$data = $request->input("data");
		
		$result_code = 0;
		//echo count($data);

		for($i=0; $i<count($data); $i++){
			$account_arr = $data[$i];
			$number = $account_arr['number'];
			$bkname = $account_arr['bkname'];
			$bankda_id = $account_arr['bankda_id'];
			$bankda_pwd = $account_arr['bankda_pwd'];
			$use_yn = $account_arr['use_yn'];
			$no = $account_arr['no'];
			/*
			echo "number";
			echo $account_arr['number'];
			echo "<br>";
			*/

			if($no != ""){
				$account_items = [
					'number'=> $number,
					'bkname' => $bkname, 
					'bankda_id' => $bankda_id,
					'bankda_pwd' => $bankda_pwd,
					'use_yn' => $use_yn,
					'ut' => "now()",
				];

				try {
					DB::table('bankda_account')
					->where('no','=',$no)
					->update($account_items);
					//$code = 200;
					$result_code = 1;
				} catch(Exception $e){
					//$code = 500;
					$result_code = 0;
				}

			}else{
				$insert_act = "insert into bankda_account(
                    number, bkname, bankda_id, bankda_pwd, use_yn, rt, ut
                )values(
                    '$number', '$bkname', '$bankda_id ', '$bankda_pwd', '$use_yn', now(), now()
                )";
				/*
				echo $insert_act;
				echo "<br>";
				*/
                try {
                    DB::insert($insert_act);
                    $result_code = 1;
                } catch(Exception $e){
                    $result_code = 0;
                };
				

				if($result_code == 0) break;

			}
		}
		
		//echo "result_code : ". $result_code;

		return response()->json([
            "code" => 200,
            "result_code" => $result_code
        ]);
	}

	public function delete_account(Request $request){
		$data = $request->input("data");
		$result_code = 0;

		for($i=0; $i<count($data); $i++){
			$account_arr = $data[$i];
			$no = $account_arr['no'];
			if($no != ""){
				try {
					 DB::table('bankda_account')->where([
						'no' => $no
					])->delete();

					$result_code = 1;
				} catch(Exception $e){
					$result_code = 0;
				}

				if($result_code == 0) break;

			}else{
				$result_code = 1;
			}
		}
		
		return response()->json([
            "code" => 200,
            "result_code" => $result_code
        ]);

	}


	public function account_log($bkdate='', Request $request){
		$today = date("Y-m-d");
        $edate = $today;

		$day_w = (date("w")-1);
		$sdate_tiem = strtotime("-$day_w days");
		$monday = date("Y-m-d", $sdate_tiem);
		$sdate = date("Y-m-d", strtotime($monday . "-1 week"));

		$is_yn = SLib::getCodes("G_YN");

		$values = [
			"sdate" => $sdate,
			"edate" => $edate,
			"is_yn_item" => $is_yn

		];

		return view( Config::get('shop.head.view') . '/order/ord06_account_log',$values);
	}

	/*
		Function: SearchAccountLog
		입금수집내역 조회
	*/
	function account_log_search(Request $request){
		$sdate 			= $request->input("sdate");
		$edate 			= $request->input("edate");
		$success_yn		= $request->input("success_yn");

		$where = "";
		if($sdate != "" && $edate != ""){
			$where .= " and date_format(rt, '%Y%m%d') >= '$sdate' ";
			$where .= " and date_format(rt, '%Y%m%d') < DATE_FORMAT(DATE_ADD('$edate', INTERVAL 1 DAY), '%Y%m%d') ";
		}

		if($success_yn != "")	$where .= " and success = '$success_yn' ";

		$sql = " 
			select
				number, bkname, date_format(bkdate, '%Y.%m.%d') as bkdate, record, description, rt, admin_id, admin_name, no, xml
			from bankda_log
			where 1=1 $where
			order by no desc
		";
		
		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
			"total" => count($result),
			"page" => -1,
			"page_cnt" => -1,
			"page_total" => -1
			),
			"body" => $result
		]);

	}

	public function pop_log($log_no = ''){

		$log = "";
		$sql = "
			select xml
			from bankda_log
			where no = '$log_no'
		";
		$row = DB::selectOne($sql);
		$log = $row->xml;

		$values = [
			"log"				=> $log
		];
		return view( Config::get('shop.head.view') . '/order/ord06_pop_log',$values);
	}

	public function save_memo(Request $request){
		$data = $request->input("data");
		$data_len = count($data);
		$return_code = 0;

		//print_r($data);


		for($i=0; $i<$data_len; $i++){
			$no = $data[$i]['no'];
			$memo = $data[$i]['memo'];

			$memo_update_items = [
				"memo" => $memo
			];
			try {
                DB::table('bankda_record')
                ->where('no','=', $no)
                ->update($memo_update_items);
                $return_code = 1;
            } catch(Exception $e){
                $return_code = 0;
            }
			
			if($return_code == 0) break;
		}
		
		return response()->json([
			"code" => 200,
			"return_code" => $return_code
		]);

	}

	public function pay(Request $request){
		$conf = new Conf();

		$cfg_sms				= $conf->getConfig("sms");
        $cfg_sms_yn				= $conf->getValue($cfg_sms,"sms_yn");
		$cfg_out_of_stock_msg	= $conf->getValue($cfg_sms,"out_of_stock_msg");
		$cfg_payment_yn			= $conf->getValue($cfg_sms,"payment_yn");
		$cfg_out_of_stock_yn	= $conf->getValue($cfg_sms,"out_of_stock_yn");
		$cfg_cash_use_yn		= $conf->getConfig("shop","cash_use_yn"); // 현금영수증 사용여부

		$pay_result = 0;

		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$user_arr = [
			'id'=>$id,
			'name' => $name 
		];


		$ord_no		= $request->input("ord_no");
		$a_ord_no	= explode(",", $ord_no);

		$bankda_no	= $request->input("bankda_no");
		$memo		= $request->input("memo");

		$update_items = [
			"is_matched" => 'Y',
			"is_hold" => 'N',
			"ord_no" => $a_ord_no[0],
			"ord_nos" => $ord_no,
			"memo" => $memo,
			"admin_id" => $id,
			"admin_name" => $name,
			"matched_dt" => date('Y-m-d H:i:s')
		];

		
		try {
			DB::table('bankda_record')
			->where('no','=', $bankda_no)
			->update($update_items);
			$pay_result = 1;
		} catch(Exception $e){
			$pay_result = -1;
		}
		

		// 입금 확인 처리

		$ret = "";
		for($i=0; $i<count($a_ord_no); $i++){
			if(isset($a_ord_no[$i]) && $a_ord_no[$i] != ""){

				$ord_no2 = $a_ord_no[$i];


				$ordclass = new Order();

				$ordclass->SetOrdNo($ord_no);

				$ordclass->__construct($user_arr);


				// 입금상태 확인
				$ret_pay_stat = $ordclass->CheckPayment();

				if($ret_pay_stat != 0 ){ // 입금 확인 상태
					$pay_result = 0;

				} else {	// 입금 정보 정상
					//==============================================================
					// 입금확인 처리 로그 START
					//==============================================================
					$inputarr = array(
						"confirm_id" => $id,
						"ord_no" => $ord_no2
					);


					$pay_update_items = [
					"confirm_amt" => "pay_amt",
					"confirm_id" => $id
					];

					
					try {
						DB::table('payment')
						->where('ord_no','=', $ord_no2)
						->update($pay_update_items);
						$pay_result = 1;
					} catch(Exception $e){
						$pay_result = -2;
					}

					/*
					* ord_opt_no 값 검색
					*/
					$sql = "select ord_opt_no from order_opt where ord_no='$ord_no2'";
					$ord_opt_row = DB::selectOne($sql);
					$ord_opt_no = $ord_opt_row->ord_opt_no;
					/*
					* ord_opt_no 값 검색
					*/
					$ret_jaego = $ordclass->CheckJaego($ord_opt_no); // 재고 체크
					//$ret_jaego
					$ordclass->SetOrdOptNo($ord_opt_no);	// ord_opt_no 
					
					if($ret_jaego){
						/**
						 * 주문상태 로그
						 */

						$state_log = array("ord_no" => $ord_no2, "ord_state" => "10", "comment" => "입금확인");
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
								where a.ord_no = '$ord_no2'
								limit 0,1
							";
							//$rs = $conn->Execute($sql);
							$row = DB::selectOne($sql);
							if($row->ord_no){
								//$row = $rs->fields;
			
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
								$id_info    = str_replace("-", "", $row->r_mobile);		// 신분확인 ID(핸드폰번호 또는 주민등록번호 -> 현재는 수령자 핸드폰번호로 처리함.)
								$amt_tot    = $row->recv_amt;								// 거래금액 총 합
								$amt_sup    = round(($amt_tot)/1.1);						// 공급가액
								$amt_tax    = $amt_tot - $amt_sup;							// 부가가치세
								$amt_svc    = "0";											// 봉사료

								$mod_type   = "";											// 변경 타입
								$mod_value  = "";											// 변경 요청 거래번호
								$mod_gubn   = "";											// 변경 요청 거래번호 구분
								$mod_mny    = "";											// 변경 요청 금액
								$rem_mny    = "";											// 변경처리 이전 금액

								$admin_id = $id;											// 관리자 아이디
								$admin_nm = $name;											// 관리자 이름
		
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
									//echo "test21";
									/*
									$pg_cash = new cash();
									list($res_cd, $res_msg) = $pg_cash->mod($conn, $param);
									*/
								}
							}
						}

						$pay_result = 1;

					}else{
						/**
						 * 주문상태 로그
						 */
						$state_log = array("ord_no" => $ord_no2, "ord_state" => "5", "comment" => "입금확인(품절)");
						$ordclass->AddStateLog($state_log);

						// 재고 없는 경우 주문상태 변경
						$ordclass->OutOfScockAfterPaid();
						
						if($cfg_sms_yn == "Y" && $cfg_out_of_stock_yn == "Y"){

							// 품절 알림 SMS
							$sql = "
								select user_nm, mobile
								from order_mst
								where ord_no = '$ord_no2'
							";
							$row = DB::selectOne($sql);
							if ($row) {
								$user_nm = $row->user_nm;		// 주문자이름
								$mobile = $row->mobile;		// 핸드폰번호
							}

							/******************************************************
							* 테스트 위해 아래로 휴대폰 번호 임시 지정 시작
							******************************************************/
							//$mobile = "010-9877-2675";
							//$user_nm = "테스트";
							/******************************************************
							* 테스트 위해 아래로 휴대폰 번호 임시 지정 시작
							******************************************************/

							$msgarr = array(
								"SHOP_NAME" => $cfg_shop_name,
							);
							$sms = new SMS([
								'admin_id' => $id,
								'admin_nm' => $name,
							]);

							$sms_msg = $sms->MsgReplace($cfg_out_of_stock_msg, $msgarr);
							if($mobile != ""){
								$sms->Send($sms_msg, $mobile, $user_nm);
							}
						}

						$pay_result = 2;


					}
					//==============================================================
					// 입금확인 처리 로그 END
					//==============================================================
				}
			}
		}
		return response()->json([
			"code" => 200,
			"return_code" => $pay_result
		]);

	}

	public function pay_hold(Request $request){
		$data		= $request->input('data');
		/*
		$bankda_no	= $request->input("bankda_no");
		$memo	= $request->input("memo");
		*/
		$pay_result = 0;

		for($i=0; $i<count($data); $i++){
			$row = $data[$i];
			$bankda_no = $row['no'];
			$update_items = [
				"is_hold" => 'Y',
				"memo" => $row['memo']
			];

			try {
				DB::table('bankda_record')
				->where('no','=', $bankda_no)
				->update($update_items);
				$pay_result = 1;
			} catch(Exception $e){
				$pay_result = -1;
			}

			if($pay_result!=1) break;
		}

		return response()->json([
			"code" => 200,
			"return_code" => $pay_result,
			"num" => $i
		]);
	}
}
