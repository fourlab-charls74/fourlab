<?php

namespace App\Http\Controllers\head\cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;
use App\Models\Claim;
use App\Models\Point;
use App\Models\Gift;
use App\Models\SMS;

class cs05Controller extends Controller
{
	public function index()
	{
		$today = date("Y-m-d");
		$edate = $today;
		$sdate = date('Y-m-d', strtotime(-1 . 'month'));
		$clm_series_no = date('YmdH');
		$today = date("Ymd");
		$sql = "
			select clm_series_no id , clm_series_nm val
			from claim_series
			where regi_date >  '$today'
			order by regi_date desc
		";

		$clm_series_no_list = DB::select($sql);

		$values = [
			'sdate'	=> $sdate,
			'edate'	=> $edate,
			'today'	=> $today,
			'clm_series_no'	=> $clm_series_no,
			'clm_series_no_list'	=> $clm_series_no_list
		];

		return view(Config::get('shop.head.view') . '/cs/cs05', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input("sdate");
		$edate = $request->input("edate");
		$clm_state = $request->input("clm_state");
		$pay_type = $request->input("pay_type");
		$ord_no = $request->input("ord_no");
		$user_nm = $request->input("user_nm");
		$clm_series_no = $request->input("clm_series_no");

		$inner_where = '';
		$where = "";

		if ($ord_no != "") {
			$where .= " and b.ord_no = '$ord_no' ";
		}

		if ($user_nm != "") {
			$where .= " and f.user_nm = '$user_nm' ";
		}

		if ($clm_series_no != "") {
			$where .= " and  a.clm_series_no = '$clm_series_no' ";
		}

		// 결제조건
		$sumpaytype = 0;
		if ($pay_type != "") {
			//$as_pay_type = explode(",",$pay_type);
			$sumpaytype = array_sum($pay_type);
			if ($sumpaytype > 0) {
				$where .= " and ( e.pay_type & $sumpaytype ) > 0 ";
			}
		}

		$sql = "
				select
					'' as chk,
					a.clm_no,
					b.ord_no,
					b.ord_opt_no,
					f.user_nm,
					f.user_id,
					a.refund_bank,
					a.refund_account,
					a.refund_nm,
					pay_type.code_val pay_type,
					e.escw_use,
					e.st_cd,
					if(a.refund_yn = 'Y', e.pay_amt,'') as pay_amt,
					a.refund_amt,
					if(a.refund_yn = 'y','Y','N') as refund_yn,ord_state.code_val as ord_state_nm,
					cs.code_val as clm_state_nm,
					cd.code_val as clm_reason,a.memo,
					a.clm_state,
					ce.clm_series_nm,
					a.req_nm,
					a.req_date,
					a.end_nm,
					a.end_date,
					( select count(*) from claim where refund_no = b.ord_opt_no and clm_state <> 51 ) as not_ref_cnt,
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
					left outer join claim_series ce on a.clm_series_no = ce.clm_series_no
				where a.proc_date >= '$sdate' and a.proc_date < date_add('$edate',interval 1 day)
					and a.clm_state = 51 and a.refund_yn = 'y' and b.ord_opt_no = a.refund_no
					$where $inner_where
				order by a.proc_date desc
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

	function SeriesCommand(Request $request)
	{
		$data			= $request->input('data');
		$clm_series_nm	= $request->input('clm_series_nm');

		$clm_seties_result = 0;
		$claim_result = 0;

		$clm_series_no = DB::table('claim_series')->insertGetId([
			'clm_series_nm' => $clm_series_nm,
			'regi_date' => now()
		]);

		if ($clm_series_no) {
			$clm_seties_result = 1;
			$sql = "
				update claim set
					clm_series_no = '$clm_series_no'
				where clm_no in ($data)
			";
			try {
				DB::update($sql);
				//$code = 200;
				$clm_seties_result = 1;
			} catch (Exception $e) {
				//$code = 500;
				$clm_seties_result = -1;
			}
		} else {
			$clm_seties_result = 0;
		}

		return response()->json([
			"code" => 200,
			"return_code" => $clm_seties_result
		]);
	}

	public function Refunds(Request $request)
	{
		$return_code = 0;

		// 설정 값 얻기
		$conf = new Conf();
		$cfg_refund_msg	= $conf->getConfigValues("sms", "refund_msg");

		$cfg_shop_name	= $conf->getConfigValue("shop", "name");
		$cfg_kakao_yn	= $conf->getConfigValue("kakao", "kakao_yn");
		$cfg_sms		= $conf->getConfig("sms");
		$cfg_sms_yn		= $conf->getValue($cfg_sms, "sms_yn");
		$cfg_refund_yn	= $conf->getValue($cfg_sms, "refund_yn");

		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$data = $request->input("data");
		$S_CLM_STATE = $request->input("S_CLM_STATE");
		$ord_cnt = count(explode(",", $data));
		$memo = "환불완료 (일괄처리)";

		$user_arr = [
			'id' => $id,
			'name' => $name
		];

		$claim = new Claim();
		$claim->__construct($user_arr);

		$point = new Point();
		$point->__construct($user_arr);

		$gift = new Gift();

		// 주문 정보 얻기
		$sql = "
			select
				o.ord_opt_no, o.ord_state, o.qty, o.ord_no, o.goods_no, o.goods_sub, o.goods_opt, o.add_point, o.pay_type,
				c.clm_no, c.clm_state, c.clm_type, c.clm_reason, c.refund_yn, c.refund_amt
			from
				order_opt o
				inner join claim c on o.ord_opt_no = c.ord_opt_no
			where o.ord_opt_no in ($data) and o.ord_state >= 5 and o.clm_state = 51 and c.refund_yn = 'y'
		";
		//echo $sql;
		$rows = DB::select($sql);

		if ($rows) {
			//foreach($rows as $row){
			for ($i = 0; $i < count($rows); $i++) {
				$row = $rows[$i];
				$ord_opt_no		= $row->ord_opt_no;
				$prev_ord_state = $row->ord_state;
				$prev_clm_state = $row->clm_state;
				$ord_qty		= $row->qty;
				$ord_no			= $row->ord_no;
				$goods_no		= $row->goods_no;
				$goods_sub		= $row->goods_sub;
				$goods_opt		= $row->goods_opt;
				$add_point		= $row->add_point;
				$clm_no			= $row->clm_no;
				$clm_type		= $row->clm_type;
				$clm_reason		= $row->clm_reason;
				$refund_yn		= $row->refund_yn;
				$refund_amt		= $row->refund_amt;
				$r_pay_type		= $row->pay_type;

				$sql = "
					select count(*) as cnt
					from claim c inner join order_opt o on c.ord_opt_no = o.ord_opt_no
					where o.ord_no = '$ord_no' and c.refund_no = '$ord_opt_no' and o.clm_state <> 51
				";
				$rowRefund = DB::selectOne($sql);

				echo "rowRefund cnt : " . $rowRefund->cnt;
				echo "<br>";

				if ($rowRefund->cnt > 0) {
					$return_code = "101";
					//break;
				} else {
					$return_code = "1";
					$sql = "
						select
							o.ord_no,c.clm_no,c.ord_opt_no,c.refund_no,o.clm_state,o.add_point
						from claim c inner join order_opt o on c.ord_opt_no = o.ord_opt_no
						where o.ord_no = '$ord_no' and c.refund_no = '$ord_opt_no' and o.clm_state = 51
					";
					$rsRefund = DB::select($sql);

					foreach ($rsRefund as $rowRefund) {
						$claim->SetClmNo($rowRefund->clm_no);
						$claim->SetOrdOptNo($rowRefund->ord_opt_no);

						//////////////////////////////////////////////////////////////////////////////
						$claim->MinusSales($next_clm_state = 61, $ord_no, $rowRefund->ord_opt_no);
						//////////////////////////////////////////////////////////////////////////////

						/*
						 * 추후 로직을 하나의 함수로 처리
						 */
						$claim->CompleteRefund("MN", $memo);
						$claim->ChangeClaimStateOrder("61");

						// 지급포인트 뺏어오기
						//$conn->debug=true;
						$point->SetOrdNo($ord_no);
						$point->Refund($rowRefund->ord_opt_no, $rowRefund->add_point, 61);
						//debugSQL();exit;

						// 지급된 사은품 환불처리
						$gift->Refund($ord_no, $rowRefund->ord_opt_no);
					}

					$param = array(
						"ord_state" => $prev_ord_state,
						"clm_state" => 61,
						"cs_form" => "01",
						"memo" => $memo
					);
					$claim->InsertMemo($param);

					if ($cfg_sms_yn == "Y") {
						if ($cfg_refund_yn == "Y") {
							////////// 환불완료시 문자보내기 시작 //////////
							$sql05 = "
										select
												b.user_nm, b.mobile
										from order_opt a
										inner join order_mst b on a.ord_no = b.ord_no
										where
												a.ord_opt_no = '$ord_opt_no'
								";
							//$rs05 = &$conn->Execute($sql05);
							$row05 = DB::selectOne($sql05);
							if ($row05) {
								//$row05 = $rs05->fields;
								$user_name = $row05->user_nm;
								$user_mobile = $row05->mobile;

								if ($r_pay_type == "2" || $r_pay_type == "6" || $r_pay_type == "10" || $r_pay_type == "14") {
									$msg = $cfg_refund_msg[0]->value;
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
									if($cfg_kakao_yn == "Y"){
										$sms->SendKakao( $template_code, $user_mobile, $user_name, $sms_msg, $msgarr, "", $btnarr);	
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
				}
			}
		}


		return response()->json([
			"code" => 200,
			"return_code" => $return_code
		]);
	}
}
