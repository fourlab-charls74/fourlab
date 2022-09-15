<?php

namespace App\Models;

use App\Components\SLib;
use Exception;
use Illuminate\Support\Facades\DB;

/*
  ORD_TYPE
  */
const ORD_KIND_WEB_ORDER = 10;	// 정상주문
const ORD_KIND_SUGI_PAIED = 20;	// 수기.입금 -> 출고 가능
const ORD_KIND_SUGI_DELAYED = 30;	// 수기.보류 -> 출고 보류
/*
  ORD_STATE
  */
const ORD_STATE_CANCELED = -10;		// 주문취소
const ORD_STATE_PG_FAIL = -20;		// 결제실패
const ORD_STATE_PG_EXPECTED = 1;		// 입금예정
const ORD_STATE_PG_OK = 5;			// 입금완료

const ORD_STATE_OS_CHECK = 6;			// 현지주문접수
const ORD_STATE_OS_ORDER_FINISH = 7;	// 현지주문완료
const ORD_STATE_OS_STOCK_IN = 8;		// 현지입고완료
const ORD_STATE_OS_STOCK_OUT = 9;		// 현지출고완료


//const ORD_STATE_OUT_OF_STOCK = 9;	// 입금완료-품절

const ORD_STATE_DLV_START = 10;		// 출고요청
const ORD_STATE_DLV_PROCESS = 20;		// 출고처리중
const ORD_STATE_DLV_FINISH = 30;		// 출고완료

const ORD_STATE_CUST_RECEIPT = 40;	// 배송완료

/*
  COM_TYPE
*/
//const COM_TYPE_SUPPLY = 1;			// 공급업체
//const COM_TYPE_IPJUM = 2;			// 입점업체

/*
  CLAIM_STATE
*/
const CLAIM_STATE_CHANGE = 60;		// 교환
const CLAIM_STATE_REFUND = 61;		// 환불
const CLAIM_STATE_CANCEL_ORDER = 62;	// 주문취소
const CLAIM_STATE_JAEGO_MODIFY = -30;	// 클레임무효

class Order
{
    public $user;
    public $ord_opt_no;
    public $ord_no;

    function __construct($user = [], $create_ord_no = false) {
        $this->user = $user;

        if ($create_ord_no) {
			$ord_no = $this->GetNextOrdNo();
			$this->SetOrdNo( $ord_no );
        }
    }

    public function SetOrdNo( $ord_no ){
        $this->ord_no = $ord_no;
    }

    public function SetOrdOptNo( $ord_opt_no ,$ord_no = "" ){
        $this->ord_opt_no = $ord_opt_no;
        if($ord_no == ""){
            $row = DB::table('order_opt')
                    ->select("ord_no")
                    ->where("ord_opt_no","=",$ord_opt_no)
                    ->first();

            $ord_no = $row->ord_no;
            $this->SetOrdNo( $ord_no );
        }else{
            $this->SetOrdNo( $ord_no );
        }
    }

    public function CheckState($ord_state)
    {
        if($ord_state == "") return false;

        $check_state = true;

        $cnt = DB::table('order_opt_wonga')
            ->where("ord_opt_no", "=", $this->ord_opt_no)
            ->where("ord_state", ">=", $ord_state)
            ->count();

        if($cnt > 0){
            $check_state = false;
        }
        return $check_state;
    }

    public function SetOrderState( $ord_state ){

        $order = [
            'ord_state' => $ord_state,
            'upd_date' => DB::raw('now()')
        ];
        if($ord_state == 30){
            $order["dlv_end_date"] = DB::raw('now()');
        }
        DB::table('order_mst')
            ->where("ord_no", "=", $this->ord_no)
            ->update($order);
    }

    public function GetNextOrdNo($date = ""){
        $goLoop = false;
        $cnt = 0;
        $ord_no = "";
        do {
            try {
                $goLoop = false;
                list($usec, $sec) = explode(" ",microtime());
                $ord_no = sprintf("%s%04d",$date != "" ? $date : date("YmdHis"), round($usec * 10000,0));
                $sql = "insert into order_no ( ord_no ) values ( '$ord_no' )";
                DB::insert($sql);
            }catch(Exception $e) {
                $goLoop = true;
                $cnt++;
            }
        } while($goLoop && $cnt < 3);

        return $ord_no;
    }

    public function CompleteOrderSugi($ord_opt_no = "", $ord_state = "", $is_store_order = false)
	{
		if ( $ord_state != ORD_STATE_PG_EXPECTED ) {	// 출고요청, 출고완료 상태만 재고 차감 처리

			if ($is_store_order == true) {
				$result = $this->ProcStoreOrder($ord_opt_no, $point_flag = false, $sms_flag = false);
			} else {
				$result = $this->ProcOrder($ord_opt_no, $point_flag = false, $sms_flag = false);
			}

			if ( $result == 1 ) {
				// 입금확인
				$this->PaymentPaid("", "", "", array());

				// order_mst 상태/처리일 변경
				$this->__MasterOrdState( $ord_state );
			}
		} else {
			$result = 1;
		}

		return $result;
		
    }

	function CheckStockQty($ord_opt_no = "0")
	{

		$ret = false;

		$where = "";
		if( $ord_opt_no != 0 )	$where .= " and a.ord_opt_no = '$ord_opt_no' ";

		$sql	= "
			select count(*) as cnt
			from order_opt a
				inner join goods_summary s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
				inner join goods g on g.goods_no = a.goods_no and g.goods_sub = a.goods_sub and g.goods_type = 'S'
			where 
				a.ord_no = :ord_no $where
				and s.wqty < a.qty
		";
		$row = DB::selectOne($sql, ['ord_no' => $this->ord_no]);

		if( $row->cnt == 0 ) $ret	= true;

		return $ret;

	}

/*
	function CheckStockQty($check_all = "N")
	{
        $cnt = DB::table('order_opt')
            ->where("ord_no", "=", $this->ord_no)
            ->where(function($query) use ($check_all){
                if($check_all == "N"){
                    $query->where("ord_opt_no","=",$this->ord_opt_no);
                }
            })
            ->where("ord_state", ">", 0)
            ->where("ord_state", "<", 10)
            ->count();

        if($cnt == 0){

            $where = "";
            if($check_all == "N"){
                $where .= " and a.ord_opt_no = '$this->ord_opt_no' ";
            }

            $query ="
                select count(*) as cnt
                from order_opt a
                    inner join goods_summary s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
                    inner join goods g on g.goods_no = a.goods_no and g.goods_sub = a.goods_sub
                where a.ord_no = :ord_no $where
                    and if(g.goods_type = 'P',0,s.wqty < a.qty) = 1
            ";
            $rows = DB::selectOne($query, ['ord_no' => $this->ord_no]);

            return $rows->cnt == 0;
        }  else {
            return false;
        }

    }
*/
    public function DlvProc($dlv_series_no, $ord_state = 10){
        DB::table('order_opt')
            ->where("ord_opt_no", "=", $this->ord_opt_no)
            ->update([
                'ord_state' => $ord_state,
                'dlv_series_no' => $dlv_series_no,
                'dlv_proc_date' => DB::raw('now()')
            ]);

        $this->SetOrderState($ord_state);
    }

	public function GetOrderState( $ord_opt_no ){

		$p_ord_state	= "";

		// 이전 주문상태 얻기
		$sql	= "
			select ord_state
			from order_opt
			where ord_opt_no = '$ord_opt_no'
		";
		$row	= DB::selectOne($sql);

		if( !$row ) return 0;

		$p_ord_state	= $row->ord_state;
  
		return $p_ord_state;
	}

    public function AddStateLog($ord_state, $comment = ""){
        $row = DB::table('order_opt')
                  ->select("ord_state", "ord_opt_no");

		$ord_opt_no = $this->ord_opt_no;

		if (empty($this->ord_opt_no)) {
			$ord_opt_no = @$ord_state['ord_opt_no'];
		}

		if(!empty($ord_opt_no)) {
			$row->where("ord_opt_no", $ord_opt_no);
		}else{
			$ord_no	= $ord_state['ord_no'];
			$row->where("ord_no", $ord_no);
		}

        if (empty($comment)) {
            $comment = empty($ord_state['comment']) ? "" : $ord_state['comment'];
		}

		$rows = $row->get();

		foreach($rows as $row) {
			DB::table('order_state')->insert([
				"ord_opt_no"	=> $row->ord_opt_no,
				"p_ord_state"	=> $row->ord_state,
				"ord_state"		=> $ord_state['ord_state'],
				"state_date"	=> now(),
				"comment"		=> $comment,
				"admin_id"		=> $this->user['id'],
				"admin_nm"		=> $this->user['name']
			]);
		}
    }

    /*
      Function: DlvReqWait
      주문 상품의 주문상태를 출고처리중에서 출고요청 상태로 변경

      Params:
        $ord_kind - 출고구분
        $ord_state = 10 - 출고요청
    */
    public function DlvReqWait($ord_kind, $ord_state = 10) {
      DB::table('order_opt')
        ->where('ord_opt_no', $this->ord_opt_no)
        ->update([
          'ord_state' => $ord_state,
          'ord_kind' => $ord_kind,
          'dlv_proc_date' => now()
        ]);

      $this->SetOrderState($ord_state);
    }



    /*
      Function: DlvEnd
      주문 상품의 주문상태를 출고완료 상태로 변경

      Params:
        $dlv_cd - 택배사 코드
        $dlv_no = 택배 송장 번호
        $ord_state = ORD_STATE_DLV_FINISH - 출고완료 상태

      See Also:
        <__MasterOrdState>
    */
    public function DlvEnd($dlv_cd, $dlv_no, $ord_state = ORD_STATE_DLV_FINISH ){
        DB::table("order_opt")->where('ord_opt_no', $this->ord_opt_no)->update([
            'ord_state' => $ord_state,
            'dlv_cd' => $dlv_cd,
            'dlv_no' => $dlv_no,
            'dlv_end_date' => now()
        ]);

        $this->__MasterOrdState($ord_state);
    }

    /*
      Function: __MasterOrdState
      주문 마스터의 주문상태를 변경

      Params:
        $ord_state - 주문 상태

      Comment:
        - order_opt 주문 상태 변경 시 변경

      See Also:
        <__MasterOrdState>
    */
    private function __MasterOrdState( $ord_state ){
      $setValue = [
        'ord_state' => $ord_state,
        'upd_date' => now()
      ];

      if ( ORD_STATE_DLV_FINISH == $ord_state ) {
        $setValue['dlv_end_date'] = now();
      }

      DB::table('order_mst')
        ->where('ord_no', $this->ord_no)
        ->update($setValue);
    }

    /*
      Function: DlvLog
      주문 상품의 주문상태를 기록

      Params:
        $ord_state - 주문 상태 ( 출고완료 : 30 )
    */
    public function DlvLog( $ord_state ){

        $log_flag = true;

        //
        //	order_opt_wonga 테이블에 ord_state : 30 상태인 건 체크하여 원가 로그 작성
        //	이희천, 2009-02-12
        //
        if( $ord_state == ORD_STATE_DLV_FINISH )
        {
            $sql = "select qty from order_opt where ord_opt_no = '$this->ord_opt_no'";

            $row = DB::selectOne($sql);

            $order_qty = $row->qty;

            $sql = "
                select sum(qty) as qty from order_opt_wonga where ord_opt_no = '$this->ord_opt_no' and ord_state = '$ord_state'
            ";

            $row = DB::selectOne($sql);
            $wonga_qty = $row->qty;

            if($order_qty > $wonga_qty)
            {
                $log_flag = true;
            }
            else if($order_qty <= $wonga_qty)
            {
                $log_flag = false;
            }
        }

        // 이전 상태 설정
        $pre_state = ORD_STATE_DLV_START;

        if(!$log_flag) return false;

        $sql = "
            insert into order_opt_wonga (
            goods_no, goods_sub, ord_opt_no, goods_opt, qty, wonga, price
            , dlv_amt, dlv_ret_amt, dlv_add_amt, dlv_enc_amt, dlv_pay_amt, recv_amt, point_apply_amt, coupon_apply_amt, dc_apply_amt
            , com_id, com_rate, ord_state, ord_kind, ord_type, invoice_no
            , ord_state_date, coupon_no, com_coupon_ratio, sales_com_fee
            )
            select
            goods_no, goods_sub, ord_opt_no, goods_opt, qty, wonga, price
            , dlv_amt, dlv_ret_amt, dlv_add_amt, dlv_enc_amt, dlv_pay_amt, recv_amt, point_apply_amt, coupon_apply_amt, dc_apply_amt
            , com_id, com_rate, '$ord_state' ord_state, ord_kind, ord_type, invoice_no
            , date_format(now(),'%Y%m%d') as ord_state_date, coupon_no, com_coupon_ratio, sales_com_fee
            from order_opt_wonga
            where ord_opt_no = '$this->ord_opt_no' and ord_state = '$pre_state'
        ";

        $result = DB::insert($sql);

        return ($result) ? true : false;
    }


    /*
      Function: IsEscrowOrder()
      에스크로 결제 여부 확인

      Returns:
        $ret - boolean
    */
    public function IsEscrowOrder(){
      $ret = 0;
      $sql = "
        select
          ifnull(escw_use, '') as escw_use
        from payment
        where ord_no = '$this->ord_no'
      ";

      $row = DB::selectOne($sql);

      if ( !$row ) return 0;

      if( $row->escw_use == "Y" ){
        $ret = 1;
      }

      return $ret;
    }

	/*
		Function: __InsertOptWonga
		매출 정보 등록

		Parameters:
			$order_opt_wonga - object

		Returns:
			$ord_wonga_no - 매출 일련 번호
	*/
	function __InsertOptWonga( $value ){

		if( empty($this->ord_opt_no) )	trigger_error("Do SetOrdOptNo() method!!", E_USER_ERROR);

		$ord_wonga_no		= "";

		$goods_no			= isset($value["goods_no"]) ? $value["goods_no"] : '';
		$goods_sub			= isset($value["goods_sub"]) ? $value["goods_sub"] : '';
		$goods_opt			= isset($value["goods_opt"]) ? $value["goods_opt"] : '';
		$qty				= isset($value["qty"]) ? $value["qty"] : '';
		$wonga				= isset($value["wonga"]) ? $value["wonga"] : '' ;
		$price				= isset($value["price"]) ? $value["price"] : '';
		$dlv_amt			= isset($value["dlv_amt"]) ? $value["dlv_amt"] : '';
		$dlv_ret_amt		= isset($value["dlv_ret_amt"]) ? $value["dlv_ret_amt"] : '';
		$dlv_add_amt		= isset($value["dlv_add_amt"]) ? $value["dlv_add_amt"] : '';
		$dlv_enc_amt		= isset($value["dlv_enc_amt"]) ? $value["dlv_enc_amt"] : '';
		$recv_amt			= isset($value["recv_amt"]) ? $value["recv_amt"] : '';
		$point_apply_amt	= isset($value["point_apply_amt"]) ? $value["point_apply_amt"] : '';
		$coupon_apply_amt	= isset($value["coupon_apply_amt"]) ? $value["coupon_apply_amt"] : '';
		$dc_apply_amt		= isset($value["dc_apply_amt"]) ? $value["dc_apply_amt"] : '';
		$pay_fee			= isset($value["pay_fee"]) ? $value["pay_fee"] : '';
		$com_id				= isset($value["com_id"]) ? $value["com_id"] : '';
		$com_rate			= isset($value["com_rate"]) ? $value["com_rate"] : '';
		$ord_state			= isset($value["ord_state"]) ? $value["ord_state"] : '';
		$ord_kind			= isset($value["ord_kind"]) ? $value["ord_kind"] : '';
		$ord_type			= isset($value["ord_type"]) ? $value["ord_type"] : '';
		$invoice_no			= isset($value["invoice_no"]) ? $value["invoice_no"] : '';
		$coupon_no			= isset($value["coupon_no"]) ? $value["coupon_no"] : '';
		$com_coupon_ratio	= isset($value["com_coupon_ratio"]) ? $value["com_coupon_ratio"] : '';
		$sales_com_fee		= isset($value["sales_com_fee"]) ? $value["sales_com_fee"] : '';
		$ord_state_date		= isset($value["ord_state_date"]) ? $value["ord_state_date"] : '';
		$prd_cd				= isset($value["prd_cd"]) ? $value["prd_cd"] : '';
		$store_cd			= isset($value["store_cd"]) ? $value["store_cd"] : '';

		//if($ord_state_date == "") $ord_state_date = now();
		if( $ord_state_date == "" )	$ord_state_date = date("Ymd");

		//
		// 중복 체크
		// 2012-07-26 knight : 주문일련번호, 주문상태 값으로 등록 여부 확인하여 최초 등록인 경우만 허용
		// 2021-06-15 ceduce : pay_fee  필드가 존재하지 않음 임시 주석처리
		//
		$sql = "
			select count(*) as cnt
			from order_opt_wonga
			where ord_opt_no = '$this->ord_opt_no'
				and ord_state = '$ord_state'
		";
		$rs = DB::selectOne($sql);
		$cnt = $rs->cnt;

		if( $cnt == 0 ) {
			$ord_wonga_no	= DB::table('order_opt_wonga')->insertGetId([
				'goods_no'			=> $goods_no,
				'goods_sub'			=> $goods_sub,
				'ord_opt_no'		=> $this->ord_opt_no,
				'goods_opt'			=> $goods_opt,
				'qty'				=> $qty,
				'wonga'				=> $wonga,
				'price'				=> $price,
				'dlv_amt'			=> $dlv_amt,
				'dlv_ret_amt'		=> $dlv_ret_amt,
				'dlv_add_amt'		=> $dlv_add_amt,
				'dlv_enc_amt'		=> $dlv_enc_amt,
				'recv_amt'			=> $recv_amt,
				'point_apply_amt'	=> $point_apply_amt,
				'coupon_apply_amt'	=> $coupon_apply_amt,
				'dc_apply_amt'		=> $dc_apply_amt,
				//'pay_fee'			=> $pay_fee,
				'com_id'			=> $com_id,
				'com_rate'			=> $com_rate,
				'ord_state'			=> $ord_state,
				'ord_kind'			=> $ord_kind,
				'ord_type'			=> $ord_type,
				'invoice_no'		=> $invoice_no,
				'ord_state_date'	=> $ord_state_date,
				'coupon_no'			=> $coupon_no,
				'com_coupon_ratio'	=> $com_coupon_ratio,
				'sales_com_fee'		=> $sales_com_fee,
				'prd_cd'			=> $prd_cd,
				'store_cd'			=> $store_cd,
			]);
		}

		return $ord_wonga_no;
	}

	/*
		Function: CheckPayment
		입금 정보 상태 확인 - 주문에 의해 생성된 입금정보의 상태 확인

		Returns:
			$pay_stat - 0 : 미입금, 1: 입금, -1: 입금정보 없음

		Comment:
			- 입금정보 없는 경우는 시스템 오류로 간주
	*/
	public function CheckPayment(){
		$pay_stat = 0;

		// 입금확인 된건인지 검사
		$sql = "
			select pay_stat
			from payment
			where ord_no = '$this->ord_no'
        ";

        $row = DB::selectOne($sql);

		//return empty($row->pay_stat) ? -1 : $row->pay_stat;
		return $row->pay_stat;
    }

	/*
		Function: CheckJaego
		상품 재고 수량 확인 (재고 수량 및 재고 수량 집계 테이블 검사)

		Returns:
			$ret_val - boolean
	*/
	public function CheckJaego( $ord_opt_no = 0 ){
		$ret = true;

		$jaego = new Jaego( $this->user );

		$where = "";
		if($ord_opt_no != 0) $where .= " and ord_opt_no = '$ord_opt_no' ";

		// 주문 상품에 대한 재고 수량 확인
		$sql = "
			select a.goods_no, a.goods_sub, a.goods_opt, a.qty, a.ord_kind, a.ord_state, a.ord_opt_no, b.is_unlimited
			from order_opt a
				inner join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
			where ord_no = '$this->ord_no'
				$where
        ";

        $rows = DB::select($sql);

		foreach($rows as $row) {
			$goods_no		= $row->goods_no;
			$goods_sub		= $row->goods_sub;
			$goods_opt		= $row->goods_opt;
			$ord_qty		= $row->qty;
			$ord_kind		= $row->ord_kind;
			$is_unlimited	= $row->is_unlimited;

			//if( $ord_kind == "10" ){ // 정상 주문인 경우에만 해당. 예약/교환/환불등은 이미 재고 잡혀있기때문에 상관없음.
                $qty = $jaego->GetQty($goods_no,$goods_sub,$goods_opt);

				if($ord_qty > $qty) {
                    $ret = false;
                    break;
                }
			//}
		}

		return $ret;
    }


	/*
		Function: CompleteOrder
		입금확인 후 재고 처리 및 출고요청 상태로 변경

		Parameters:
			$point_flag = true - 포인트 지급 설정
			$sms_flag = true - SMS 발송 설정

		Applied:
			common_return.php, ord02.php, ord12.php, ord27.php

		Comment:
			$order->regiOrder > <Order::CompleteOrder>

		See Also:
			- <Jaego::__SetStockNo>
			- <Jaego::__GetPartnerGoodsWonga>
			- <Jaego::__DecreaseGoodQtyAll>
			- <Jaego::DecreaseGoodQty>
			- <Jaego::DecreaseSummaryQty>
			- <Jaego::InsertHistory>
			- <Point::Order>
			- <SetOrdOptNo>
			- <__SetOrderGoods>
			- <__InsertOptWonga>
			- <DlvStart>
			- <PaymentPaid>
			- <__MasterOrdState>
	*/

	function CompleteOrder($point_flag = true, $sms_flag = true){

		$result = $this->ProcOrder("",$point_flag,$sms_flag);

		if($result == 1){

            // 설정 값 얻기
            $conf = new Conf();
			$cfg_name			= $conf->getConfigValue("shop","name");
			$cfg_kakao_yn		= $conf->getConfigValue("kakao","kakao_yn");
			$cfg_sms			= $conf->getConfig("sms");
			$cfg_sms_yn			= $conf->getValue($cfg_sms,"sms_yn");
			$cfg_payment_yn		= $conf->getValue($cfg_sms,"payment_yn");
			$cfg_payment_msg	= $conf->getValue($cfg_sms,"payment_msg_aligo");
			$g_bank_code 		= SLib::getCodes('G_BANK_CODE');

			/// 입금처리 완료 //////////////////////
			// payment 상태/처리일 변경
			global $bank_code, $remitter, $account;	// 가상계좌 수동 입금처리 시 사용되는 변수

			$this->PaymentPaid($remitter, $bank_code, $account, $g_bank_code);

			// order_mst 상태/처리일 변경
			$this->__MasterOrdState( ORD_STATE_DLV_START );

			/*************************************/
			/** SMS 발송 설정된 경우 	 		**/
			/*************************************/
			if( $sms_flag ) {

				if($cfg_sms_yn == "Y"){
					if($cfg_payment_yn == "Y"){

						$sql = "
							select user_nm, mobile, recv_amt from order_mst where ord_no = '$this->ord_no'
						";
						$row = DB::selectOne($sql);
						$user_nm = $row->user_nm;	// 주문자이름
						$mobile = $row->mobile;	// 핸드폰번호
						$recv_amt = $row->recv_amt;	// 입금금액

						$sms = new SMS( $this->user );
						$template_code = 'Ordercode10';

						$msgarr = array(
							"SHOP_NAME" => $cfg_name,
							"USER_NAME" => $user_nm,
							"ORDER_NO" => $this->ord_no,
							"ORDER_AMT" => number_format($recv_amt),
							"SHOP_URL" => 'http://www.netpx.co.kr/app/mypage/order_list'
						);
						$btnarr = array(
							"BUTTON_TYPE" => '1',
							"BUTTON_INFO" => '주문내역조회^WL^http://www.netpx.co.kr/app/mypage/order_list'
						);
						$sms_msg = $sms->MsgReplace($cfg_payment_msg, $msgarr);
						
						if($cfg_kakao_yn == "Y" && $template_code != ""){
							// 문자 서비스
							$sms->SendKakao( $template_code, $mobile, $user_nm, $sms_msg, $msgarr, '', $btnarr);
						} else {
							$sms->Send($sms_msg, $mobile, $user_nm);
						}
					}
				}
			}
		}

		return $result;
	}
	/*
		Function: ProcOrder
		재고 처리 및 출고요청

		Parameters:
			$ord_opt_no	- 주문옵션번호
			$point_flag	- 포인트 지급 여부
			$sms_flag		-  SMS 발송여부

		Returns:
			 1	- 성공
			-1	- 재고 출고건
			-2 - 재고 없음

	*/
	public function ProcOrder($ord_opt_no = "", $point_flag = true, $sms_flag = true) {
		$jaego = new Jaego( $this->user );

		if( $point_flag ){
			$point = new Point( $this->user,"");
		}

        $where = "";

		if($ord_opt_no != ""){
			$where .= " and a.ord_opt_no = '$ord_opt_no' ";
		}

		// 주문 옵션별 (판매가*수량) 및 정보 얻기
		// ceduce - 210615 - a.pay_fee 삭제
		$sql = "
			select
				a.ord_opt_no, a.qty, a.price, ifnull(a.wonga, g.wonga) as wonga, a.goods_no, a.goods_sub, a.goods_opt,
				a.recv_amt, a.point_amt, a.coupon_amt, a.dc_amt, '' as pay_fee, a.com_id, c.pay_fee com_rate,
				a.ord_kind, a.ord_type, a.add_point, a.com_coupon_ratio, a.coupon_no,
				c.com_type, date_format(a.ord_date,'%Y%m%d') as ord_date, a.sales_com_fee, a.dlv_amt,
				b.user_id, a.ord_state, g.is_unlimited, g.sale_stat_cl
			from order_opt a
				inner join order_mst b on a.ord_no = b.ord_no
				left outer join company c on a.com_id = c.com_id
				left outer join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
			where a.ord_no = '$this->ord_no' $where
        ";

        $rows = DB::select($sql);

		foreach($rows as $row){
			$ord_opt_no			= $row->ord_opt_no;
			$goods_no			= $row->goods_no;
			$goods_sub			= $row->goods_sub;
			$goods_opt			= $row->goods_opt;
			$ord_qty			= $row->qty;
			$ord_price			= $row->price;
			$wonga				= $row->wonga;
			$recv_amt			= $row->recv_amt;
			$point_amt			= $row->point_amt;
			$coupon_amt			= $row->coupon_amt;
			$dc_amt				= $row->dc_amt;
			$pay_fee			= $row->pay_fee;
			$com_id				= $row->com_id;
			$com_rate			= $row->com_rate;
			$ord_kind			= $row->ord_kind;
			$ord_type			= $row->ord_type;
			$add_point			= $row->add_point;
			$coupon_no			= $row->coupon_no;
			$com_coupon_ratio	= $row->com_coupon_ratio;
			$com_type			= $row->com_type;
			$ord_date			= $row->ord_date;
			$dlv_amt			= $row->dlv_amt;		// 2008-05-30 : knight 추가
			$sales_com_fee		= $row->sales_com_fee;
			$user_id			= $row->user_id;
			$ord_state			= $row->ord_state;
			$is_unlimited		= $row->is_unlimited;	// 무한재고 여부
			$sale_stat_cl		= $row->sale_stat_cl;	// 상품상태

			$this->SetOrdOptNo($ord_opt_no);

			/*
				재고 처리된 주문건인지 확인
			*/
			if($this->__IsFirstMinus() == false){
				return -1;
			}

			// 입금예정 상태의 주문 처리 : 넷텔러, 가상계좌
			if( $ord_state == ORD_STATE_PG_EXPECTED || $ord_state == ORD_STATE_PG_OK ){

				if($is_unlimited == "N"){	// 무한재고 상품이 아닐 경우에만 재고차감.

					// 재고 차감 처리 : 온라인재고
					$ret = $jaego->MinusQty($goods_no,$goods_sub,$goods_opt,$ord_qty);

					if( $ret == 0 ) {
						return -2;
					} else if( $ret > 0 ){
						// 상품 총 수량 확인
						if($jaego->IsTotalQty($goods_no, $goods_sub) == 0 && $sale_stat_cl == "40"){
							// 상품 클래스 로드
							$goods = new Product( $this->user, $goods_no, $goods_sub );

							// 상품상태 품절 처리.
							$param["sale_stat_cl"] = 30;
							$param["goods_memo"] = "재고차감으로 인한 자동 품절 처리";
							$goods->Edit($goods_no,$param);
						}
					}
				}

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_PG_OK,
					"ord_kind" => $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_START,
					"ord_kind" => $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);
				unset($order_opt_wonga);

				// 주문건 입금 완료 상태로 변경
				$this->PayOk(ORD_STATE_PG_OK);

				/**
				 * 주문상태 : 배송출고 요청 상태로 변경
				 */
				$this->DlvStart(ORD_STATE_DLV_START, $ord_kind);

			}

			// 출고요청 주문 처리
			elseif( $ord_state == ORD_STATE_DLV_START ) {

				if($is_unlimited == "N"){	// 무한재고 상품이 아닐 경우에만 재고차감.
					// 재고 차감 처리 : 온라인재고
					$ret = $jaego->MinusQty($goods_no,$goods_sub,$goods_opt,$ord_qty);
					if( $ret == 0 ) {
						return -2;
					} else if( $ret > 0 ){

						// 상품 총 수량 확인
						if($jaego->IsTotalQty($goods_no, $goods_sub) == 0 && $sale_stat_cl == "40"){
							// 상품 클래스 로드
							$goods = new Product( $this->user, $goods_no, $goods_sub );

							// 상품상태 품절 처리.
							$param["sale_stat_cl"] = 30;
							$param["goods_memo"] = "재고차감으로 인한 자동 품절 처리";
							$goods->Edit($goods_no,$param);
						}
					}
				}

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_PG_OK,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_START,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);
				unset($order_opt_wonga);

				// 주문건 입금 완료 상태로 변경
				$this->PayOk(ORD_STATE_PG_OK);

				#
				#	주문상태 : 배송출고 요청 상태로 변경
				#
				$this->DlvStart(ORD_STATE_DLV_START, $ord_kind);
			}

			//  출고완료 주문처리
			elseif( $ord_state == ORD_STATE_DLV_FINISH ) {

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_PG_OK,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);

				if($is_unlimited == "N"){	// 무한재고 상품이 아닐 경우에만 재고차감.
					// 재고 차감 처리 : 온라인재고
					$ret = $jaego->MinusQty($goods_no,$goods_sub,$goods_opt,$ord_qty);
					if( $ret == 0 ) {
						return -2;
					} else if( $ret > 0 ){

						// 상품 총 수량 확인
						if($jaego->IsTotalQty($goods_no, $goods_sub) == 0 && $sale_stat_cl == "40"){
							// 상품 클래스 로드
							$goods = new Product( $this->conn, $this->user, $goods_no, $goods_sub );

							// 상품상태 품절 처리.
							$param["sale_stat_cl"] = 30;
							$param["goods_memo"] = "재고차감으로 인한 자동 품절 처리";
							$goods->Edit($goods_no,$param);
						}
					}
				}

				// 주문건 입금 완료 상태로 변경
				$this->PayOk(ORD_STATE_PG_OK);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_START,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);

				if($is_unlimited == "N"){	// 무한재고 상품이 아닐 경우에만 재고차감.
					// 재고 차감 처리 : 보유재고
					$jaego->MinusStockQty($goods_no, $goods_sub, $goods_opt, $ord_qty, 2, "", "", $com_id, $this->ord_no, $this->ord_opt_no);
				}

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_FINISH,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);
				unset($order_opt_wonga);

				#
				#	주문상태 : 배송출고 요청 > 처리중 > 완료 상태로 변경
				#
				$this->DlvStart(ORD_STATE_DLV_START, $ord_kind);
				$this->DlvProc(0, ORD_STATE_DLV_PROCESS);
				//$this->DlvEnd($dlv_cd = "HANJIN", $dlv_no = "수기판매", ORD_STATE_DLV_FINISH);
			}
		}

		/*************************************/
		/** 포인트 지급 설정된 경우 		**/
		/*************************************/
		if($point_flag){
			// 포인트 지급
			$point->SetOrdNo($this->ord_no);
			$point->Order($add_point);
		}

		return 1;
    }

	/*
		Function: __IsFirstMinus()
		재고 차감 전 재고 차감 기록을 검사

		Returns:
			$ret - boolean
	*/
	private function __IsFirstMinus(){
		$ret = true;
		$ord_state = ORD_STATE_DLV_START;

		$sql = "
			select count(*) as cnt
			from order_opt_wonga
			where ord_opt_no = '$this->ord_opt_no' and ord_state = '$ord_state'
		";
		$row = DB::selectOne($sql);
		$cnt = $row->cnt;

		if($cnt > 0) {
			$ret = false;
        }

		return $ret;
    }

	/*
		Function: PayOk
		주문상태를 입금완료 상태로 변경

		Parameter:
			$ord_state = ORD_STATE_PG_OK - 입금완료 상태
	 */
	public function PayOk($ord_state = ORD_STATE_PG_OK){
		$sql = "
			update order_opt set
				ord_state = '$ord_state'
			where ord_opt_no = '$this->ord_opt_no'
        ";

        DB::update($sql);
	}

	/*
		Function: DlvStart
		주문 상품의 주문상태를 출고요청 상태로 변경

		Parameter:
			$ord_state = ORD_STATE_DLV_START - 출고요청 상태
	 */
	public function DlvStart($ord_state = ORD_STATE_DLV_START, $ord_kind = ""){
        $ord_kind_sql = "";

		if($ord_kind == ORD_KIND_SUGI_PAIED){
			$ord_kind_sql = " ,ord_kind = '$ord_kind' ";
		}

		$sql = "
			update order_opt set
				ord_state = '$ord_state'
				,dlv_start_date = now()
				$ord_kind_sql
			where ord_opt_no = '$this->ord_opt_no'
        ";

        DB::update($sql);
	}

	/*
		Function: PaymentPaid
		주문 결제 정보를 입금 확인 상태로 변경
	*/
	public function PaymentPaid($bank_inpnm, $bank_code, $account, $arr_bank_codes){

		$sql_upd = ""; // KCP 입금통보에서 넘어 옴.

		if( $bank_inpnm != "" )	$sql_upd .= " , bank_inpnm = '$bank_inpnm' ";
		//if( $bank_code != "" )	$sql_upd .= " , bank_code = '$arr_bank_codes[$bank_code]' ";
		//if( $account != "" )	$sql_upd .= " , bank_number = '$account' ";

		$sql = "
			update payment set
				pay_stat = '1'
				, upd_dm = date_format(now(),'%Y%m%d%H%i%s')
				, pay_date = now()
				$sql_upd
			where ord_no = '$this->ord_no'
        ";

        DB::update($sql);
    }

	/*
		Function: OutOfScockAfterPaid
		입금 후 품절된 상태 처리

		Comment:
			- 주문 상품 테이블 상태 변경 -> 입금완료 ($ord_state : 5)
			- 주문 마스터 테이블 상태 변경 -> 입금완료 ($ord_state : 5)
			- 입금 테이블 상태 변경 - 입금 확인 ($pay_stat :1)
	*/
	public function OutOfScockAfterPaid(){

		$ord_state = ORD_STATE_PG_OK;

		//
		// 입금완료 상태 매출 등록
		//

		// 주문 옵션별 (판매가*수량) 및 정보 얻기
		$sql = "
			select
				a.ord_opt_no, a.qty, a.price, g.wonga, a.goods_no, a.goods_sub, a.goods_opt
				, a.recv_amt, a.point_amt, a.coupon_amt, a.dc_amt, a.com_id, c.pay_fee com_rate
				, a.ord_kind, a.ord_type, a.add_point, a.com_coupon_ratio, a.coupon_no
				, c.com_type, date_format(a.ord_date,'%Y%m%d') as ord_date, a.sales_com_fee, a.dlv_amt
				, b.user_id, a.ord_state, g.is_unlimited, g.sale_stat_cl
			from order_opt a
				inner join order_mst b on a.ord_no = b.ord_no
				left outer join company c on a.com_id = c.com_id
				left outer join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
			where a.ord_no = '$this->ord_no'
        ";

        $rows = DB::select($sql);

		foreach($rows as $row){
			$ord_opt_no			= $row->ord_opt_no;
			$goods_no			= $row->goods_no;
			$goods_sub			= $row->goods_sub;
			$goods_opt			= $row->goods_opt;
			$ord_qty			= $row->qty;
			$ord_price			= $row->price;
			$wonga				= $row->wonga;
			$recv_amt			= $row->recv_amt;
			$point_amt			= $row->point_amt;
			$coupon_amt			= $row->coupon_amt;
			$dc_amt				= $row->dc_amt;
			$com_id				= $row->com_id;
			$com_rate			= $row->com_rate;
			$ord_kind			= $row->ord_kind;
			$ord_type			= $row->ord_type;
			$add_point			= $row->add_point;
			$coupon_no			= $row->coupon_no;
			$com_coupon_ratio	= $row->com_coupon_ratio;
			$com_type			= $row->com_type;
			$ord_date			= $row->ord_date;
			$dlv_amt			= $row->dlv_amt;		// 2008-05-30 : knight 추가
			$sales_com_fee		= $row->sales_com_fee;
			$user_id			= $row->user_id;
			$ord_state			= $row->ord_state;
			$is_unlimited		= $row->is_unlimited;	// 무한재고 여부
			$sale_stat_cl		= $row->sale_stat_cl;	// 상품상태

			$this->SetOrdOptNo($ord_opt_no);

			// 입금예정 상태의 주문 처리 : 넷텔러, 가상계좌
			if( $ord_state == ORD_STATE_PG_EXPECTED || $ord_state == ORD_STATE_PG_OK ){

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no"				=> $goods_no
					, "goods_sub"			=> $goods_sub
					, "goods_opt"			=> $goods_opt
					, "qty"					=> $ord_qty
					, "wonga"				=> $wonga
					, "price"				=> $ord_price
					, "dlv_amt"				=> $dlv_amt
					, "recv_amt"			=> $recv_amt
					, "point_apply_amt"		=> $point_amt
					, "coupon_apply_amt"	=> $coupon_amt
					, "dc_apply_amt"		=> $dc_amt
					, "com_id"				=> $com_id
					, "com_rate"			=> $com_rate
					, "ord_state"			=> $ord_state = ORD_STATE_PG_OK
					, "ord_kind"			=> $ord_kind
					, "ord_type"			=> $ord_type
					, "coupon_no"			=> $coupon_no
					, "com_coupon_ratio"	=> $com_coupon_ratio
					, "sales_com_fee"		=> $sales_com_fee
				);
				$this->__InsertOptWonga($order_opt_wonga);

				unset($order_opt_wonga);

				$sql = "
					update order_opt set
						ord_state = '$ord_state'
					where ord_opt_no = '$this->ord_opt_no'
                ";

                DB::update($sql);
			}
		}

		//
		// 주문 상태 변경
		//
		$sql = "
			update order_mst set
				ord_state = '$ord_state'
			where ord_no = '$this->ord_no'
        ";
        DB::update($sql);

		$sql = "
			update payment set
				pay_stat = '1',
				card_msg = '입금 지연으로인한 품절, 환불처리 대상 건',
				pay_date = now(),
				upd_dm = date_format(now(),'%Y%m%d%H%i%s')
			where ord_no = '$this->ord_no'
        ";

        DB::update($sql);
	}

    function Delete($ord_no)
    {
        DB::beginTransaction();
        try {

            $user_id = "";
            $pay_point = 0;

            // 주문 정보 얻기
            $sql = /** @lang text */
                "
                select 
                    a.ord_no, a.ord_state, a.out_ord_no, a.sale_place, a.user_id
                    , b.ord_opt_no, b.goods_no, b.goods_sub, b.goods_opt, b.qty, c.pay_point
                from order_mst a
                    inner join order_opt b on a.ord_no = b.ord_no
                    inner join payment c on a.ord_no = c.ord_no
                where a.ord_no = :ord_no
            ";

            $rows = DB::select($sql, array("ord_no" => $ord_no));

            foreach ($rows as $row) {
                $ord_state = $row->ord_state;
                $out_ord_no = $row->out_ord_no;
                $sale_place = $row->sale_place;
                $user_id = $row->user_id;

                $ord_opt_no = $row->ord_opt_no;
                $goods_no = $row->goods_no;
                $goods_sub = $row->goods_sub;
                $goods_opt = $row->goods_opt;
                $qty = $row->qty;
                $pay_point = $row->pay_point;

                if ($ord_state >= 10) {  // 출고요청 상태

                    // 재고 환원
                    $jaego = new Jaego($this->user);
                    $jaego->PlusQty($goods_no, $goods_sub, $goods_opt, $qty);
                }

                DB::table("order_opt")
                    ->where("ord_opt_no", $ord_opt_no)
                    ->delete();

                DB::table("order_opt_wonga")
                    ->where("ord_opt_no", $ord_opt_no)
                    ->delete();

                if ($out_ord_no != "") {
                    DB::table("outbound_order")
                        ->where("sale_place", $sale_place)
                        ->where("out_ord_no", $out_ord_no)
                        ->where("goods_no", $goods_no)
                        ->where("ord_opt_no", $ord_opt_no)
                        ->delete();
                }

                DB::table("order_state")
                    ->where("ord_opt_no", $ord_opt_no)
                    ->delete();

                DB::table("order_opt_addopt")
                    ->where("ord_opt_no", $ord_opt_no)
                    ->delete();

            }

            if ($user_id != "") {

                DB::table("point_list")
                    ->where("user_id", $user_id)
                    ->where("ord_no", $ord_no)
                    ->delete();

                if ($pay_point > 0) {
                    DB::table("member")
                        ->where("user_id", $user_id)
                        ->update([
                            'point' => DB::raw("point + $pay_point")
                        ]);

                }
            }

            DB::table("payment")
                ->where("ord_no", $ord_no)
                ->delete();

            DB::table("order_mst")
                ->where("ord_no", $ord_no)
                ->delete();
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            //$msg = $e->getMessage();
            throw $e;
        }

    }

	//////////////////////////////////////////////////////
	/////////////////////// 매장주문 //////////////////////
	//////////////////////////////////////////////////////

	public function ProcStoreOrder($ord_opt_no = "", $point_flag = true, $sms_flag = true)
	{
		$ord_no = $this->ord_no;
		$where = " a.ord_no = '$ord_no' ";

		if ($ord_opt_no != "") $where .= " and a.ord_opt_no = '$ord_opt_no' ";

		// 주문 옵션별 (판매가*수량) 및 정보 얻기
		$sql = "
			select
				a.ord_opt_no, a.qty, a.price, ifnull(a.wonga, g.wonga) as wonga, a.goods_no, a.goods_sub, a.goods_opt,
				a.recv_amt, a.point_amt, a.coupon_amt, a.dc_amt, '' as pay_fee, a.com_id, c.pay_fee com_rate,
				a.ord_kind, a.ord_type, a.add_point, a.com_coupon_ratio, a.coupon_no,
				c.com_type, date_format(a.ord_date,'%Y%m%d') as ord_date, a.sales_com_fee, a.dlv_amt,
				b.user_id, a.ord_state, g.is_unlimited, g.sale_stat_cl, a.prd_cd, a.store_cd
			from order_opt a
				inner join order_mst b on a.ord_no = b.ord_no
				left outer join company c on a.com_id = c.com_id
				left outer join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
			where $where
        ";
        $rows = DB::select($sql);
		foreach ($rows as $row) {
			$ord_opt_no			= $row->ord_opt_no;
			$goods_no			= $row->goods_no;
			$goods_sub			= $row->goods_sub;
			$goods_opt			= $row->goods_opt;
			$ord_qty			= $row->qty;
			$ord_price			= $row->price;
			$wonga				= $row->wonga;
			$recv_amt			= $row->recv_amt;
			$point_amt			= $row->point_amt;
			$coupon_amt			= $row->coupon_amt;
			$dc_amt				= $row->dc_amt;
			$pay_fee			= $row->pay_fee;
			$com_id				= $row->com_id;
			$com_rate			= $row->com_rate;
			$ord_kind			= $row->ord_kind;
			$ord_type			= $row->ord_type;
			$add_point			= $row->add_point;
			$coupon_no			= $row->coupon_no;
			$com_coupon_ratio	= $row->com_coupon_ratio;
			$com_type			= $row->com_type;
			$ord_date			= $row->ord_date;
			$dlv_amt			= $row->dlv_amt;
			$sales_com_fee		= $row->sales_com_fee;
			$user_id			= $row->user_id;
			$ord_state			= $row->ord_state;
			$is_unlimited		= $row->is_unlimited;	// 무한재고 여부
			$sale_stat_cl		= $row->sale_stat_cl;	// 상품상태
			$prd_cd				= $row->prd_cd;
			$store_cd			= $row->store_cd;

			$this->SetOrdOptNo($ord_opt_no);

			// 재고 처리된 주문건인지 확인
			if($this->__IsFirstMinus() == false) return -1;

			// 입금예정 상태의 주문 처리 : 넷텔러, 가상계좌
			if ( $ord_state == ORD_STATE_PG_EXPECTED || $ord_state == ORD_STATE_PG_OK ) {

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_PG_OK,
					"ord_kind" => $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_START,
					"ord_kind" => $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);
				unset($order_opt_wonga);

				// 주문건 입금 완료 상태로 변경
				$this->PayOk(ORD_STATE_PG_OK);

				/**
				 * 주문상태 : 배송출고 요청 상태로 변경
				 */
				$this->DlvStart(ORD_STATE_DLV_START, $ord_kind);

			} else if ( $ord_state == ORD_STATE_DLV_START ) {
				// 출고요청 주문 처리

				// 재고차감
				if ($store_cd != '') {
					DB::table('product_stock_store')
						->where('prd_cd', '=', $prd_cd)
						->where('store_cd', '=', $store_cd) 
						->update([
							'wqty' => DB::raw('wqty - ' . $ord_qty),
							'ut' => now(),
						]);
					DB::table('product_stock')
						->where('prd_cd', '=', $prd_cd)
						->update([
							'qty' => DB::raw('qty - ' . $ord_qty),
							'ut' => now(),
						]);
				} else {
					DB::table('product_stock_storage')
						->where('prd_cd', '=', $prd_cd)
						->where('storage_cd', '=', DB::raw("(select storage_cd from storage where default_yn = 'Y')"))
						->update([
							'wqty' => DB::raw('wqty - ' . $ord_qty),
							'ut' => now(),
						]);
					DB::table('product_stock')
						->where('prd_cd', '=', $prd_cd)
						->update([
							'qty' => DB::raw('qty - ' . $ord_qty),
							'wqty' => DB::raw('wqty - ' . $ord_qty),
							'ut' => now(),
						]);
				}

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_PG_OK,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_START,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);
				unset($order_opt_wonga);

				// 주문건 입금 완료 상태로 변경
				$this->PayOk(ORD_STATE_PG_OK);

				/**
				 * 주문상태 : 배송출고 요청 상태로 변경
				 */
				$this->DlvStart(ORD_STATE_DLV_START, $ord_kind);
			} else if ( $ord_state == ORD_STATE_DLV_FINISH ) {
				//  출고완료 주문처리

				// 재고차감
				if ($store_cd != '') {
					DB::table('product_stock_store')
						->where('prd_cd', '=', $prd_cd)
						->where('store_cd', '=', $store_cd) 
						->update([
							'qty' => DB::raw('qty - ' . $ord_qty),
							'ut' => now(),
						]);
				} else {
					DB::table('product_stock_storage')
						->where('prd_cd', '=', $prd_cd)
						->where('storage_cd', '=', DB::raw("(select storage_cd from storage where default_yn = 'Y')"))
						->update([
							'qty' => DB::raw('qty - ' . $ord_qty),
							'ut' => now(),
						]);
				}

				// 모든 입금확인은 5 상태를 기록한다.
				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_PG_OK,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);

				// 주문건 입금 완료 상태로 변경
				$this->PayOk(ORD_STATE_PG_OK);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_START,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);

				$order_opt_wonga = array(
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"qty" => $ord_qty,
					"wonga" => $wonga,
					"price" => $ord_price,
					"dlv_amt" => $dlv_amt,
					"recv_amt" => $recv_amt,
					"point_apply_amt" => $point_amt,
					"coupon_apply_amt" => $coupon_amt,
					"dc_apply_amt" => $dc_amt,
					"pay_fee" => $pay_fee,
					"com_id" => $com_id,
					"com_rate" => $com_rate,
					"ord_state" => $ord_state = ORD_STATE_DLV_FINISH,
					"ord_kind"	=> $ord_kind,
					"ord_type" => $ord_type,
					"coupon_no" => $coupon_no,
					"com_coupon_ratio" => $com_coupon_ratio,
					"sales_com_fee" => $sales_com_fee,
					"prd_cd" => $prd_cd,
					"store_cd" => $store_cd
				);
				$this->__InsertOptWonga($order_opt_wonga);
				unset($order_opt_wonga);

				/**
				 * 주문상태 : 배송출고 요청 > 처리중 > 완료 상태로 변경
				 */
				$this->DlvStart(ORD_STATE_DLV_START, $ord_kind);
				$this->DlvProc(0, ORD_STATE_DLV_PROCESS);
			}
		}

		/*************************************/
		/******** 포인트 지급 설정된 경우 ******/
		/*************************************/
		if ($point_flag) {
			// 포인트 지급
			$point = new Point($this->user, "");
			$point->SetOrdNo($this->ord_no);
			$point->Order($add_point);
		}

		return 1;
	}
}
