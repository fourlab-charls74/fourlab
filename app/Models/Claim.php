<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;

const CLAIM_STATE_REFUND = 61; // 환불완료
const CLAIM_CS_FORM = 01; // 클레임 cs
const REFUND_PRODUCT_STOCK_TYPE = 6; // 환불 (code > PRODUCT_STOCK_TYPE)

class Claim
{
    private $user;
    private $clm_no;
    private $ord_opt_no;
    private $clm_det_no;
    public function __construct($user=[]){
        $this->user = $user;
    }

    public function SetClmNo( $clm_no ){
        $this->clm_no = $clm_no;
    }

    public function SetOrdOptNo( $ord_opt_no){
        $this->ord_opt_no = $ord_opt_no;
    }

	function SetClmDetNo( $clm_det_no ){
		$this->clm_det_no = $clm_det_no;
	}

    public function AddComments($memo,$who = 'head',$cs_form = '01',$req_claim_gubun = "")
    {
        $ord_opt = DB::table('order_opt')
            ->where("ord_opt_no", "=", $this->ord_opt_no)
            ->select('ord_no','ord_state', 'clm_state')->first();

        $claim_memo = DB::table('claim_memo')->insert([
            'ord_opt_no' => $this->ord_opt_no,
            'clm_no' => $this->clm_no,
            'ord_state' => $ord_opt->ord_state,
            'clm_state' => $ord_opt->ord_state,
            'cs_form' => $cs_form,
            'memo' => $memo,
            'admin_id' => $this->user["id"],
            'admin_nm' => $this->user["name"],
            'regi_date' => DB::raw('now()'),
        ]);
        $memo_no = DB::getPdo()->lastInsertId();

        if($who !== "head"){
            DB::table('claim_memo_client')->insert([
                'ord_no' => $ord_opt->ord_no,
                'ord_opt_no' => $this->ord_opt_no,
                'memo_no' => $memo_no,
                'req_claim_gubun' => $req_claim_gubun,
                'req_client_gubun' => $who,
                'id' => $this->user["id"],
                'name' => $this->user["name"],
                'regi_date' => DB::raw('now()'),
            ]);
        }
    }

	/*
 		Function: InsertMessage
 		클레임 메모 등록 및 최신 클레임 메모 변경

 		Parameters:
 			$ord_opt_no - 주문 일련 번호
 			$ord_state - 주문 상태
 			$clm_state - 클레임 상태
 			$cs_form - CS 유형
 			$memo - 메모 내용

 		Returns:
 			$clm_memo_no : last insert id
	*/
    public function InsertMessage($param) {
		$memo_no = $this->InsertMemo($param);

		if($memo_no > 0){
            DB::table('claim')
              ->where('clm_no', $this->clm_no)
              ->update([
                'memo' => $param['memo'],
                'last_up_date' => now()
            ]);
        }

		return $memo_no;
    }

	/*
 		Function: InsertMemo
 		클레임 메모 등록

 		Parameters:
 			$ord_opt_no - 주문 일련 번호
 			$ord_state - 주문 상태
 			$clm_state - 클레임 상태
 			$cs_form - CS 유형
 			$memo - 메모 내용

 		Returns:
 			$clm_memo_no : last insert id
	*/
	public function InsertMemo( $param ){
		if(empty($this->ord_opt_no)) trigger_error("Use SetOrdOptNo(ord_opt_no) method first !!", E_USER_ERROR);
		//if(empty($this->clm_no)) trigger_error("Use SetClmNo(clm_no) method first !!", E_USER_ERROR);

        return DB::table('claim_memo')->insertGetId([
            'ord_opt_no' => $this->ord_opt_no,
            'clm_no' => $this->clm_no,
            'ord_state' => $param['ord_state'],
            'clm_state' => $param['clm_state'],
            'cs_form' => $param['cs_form'],
            'memo' => $param['memo'],
            'admin_id' => $this->user["id"],
            'admin_nm' => $this->user["name"],
            'regi_date' => now()
        ]);
    }

	#############################################################
	#		Order 관련 테이블 상태 변경							#
	#############################################################

	/*
 		Function: UpdateClaimStateOrder
 		주문 내역의 클레임 상태 변경

 		Parameters:
 			$clm_state - 클레임 상태
 			$ord_opt_no - 주문 일련 번호
	*/
	public function ChangeClaimStateOrder( $clm_state ){
		if(empty($this->ord_opt_no)) throw new Exception("Use SetOrdOptNo(ord_opt_no) method first !!");

		if( $clm_state == -10 ){ // 주문 취소
			$sql = "
				update order_opt set
					ord_state = -10,
					clm_state = -10,
					dlv_cancel_date = now()
				where ord_opt_no = '$this->ord_opt_no'
					and ord_state in (1,9,-10,-20)
            ";
		}else{
			$sql = "
				update order_opt set
					clm_state = '$clm_state'
				where ord_opt_no = '$this->ord_opt_no'
			";
        }

        DB::update($sql);
    }

	/*
		Function: MinusSales
		클레임에 의한 마이너스 매출 등록 및 재고 처리

		Parameters:
			$next_claim_state - 진행할 클레임 상태
			$ord_no - 주문 번호
			$ord_opt_no - 주문 일련 번호

		Returns:
			true - success
			false - failure

		See Also:
			- <Jaego::Jaego>
			- <Jaego::Plus>
			- <Order::Order>
			- <Order::__InsertOptWonga>


		Applied:
			- /webapps/csm/csm33.php
			- /webapps/csm/csm30.php
			- /webapps/order/ord06.php
			- /webapps/order/ord01_detail.php

		Comment:
			- Order::AddOrdOptWonga > Order::ProcWongaJaego > <Claim::MinusSales>
	*/
	public function MinusSales( $next_clm_state, $ord_no, $ord_opt_no ) {

		if( ! ($next_clm_state == 60 || $next_clm_state == 61) ){
			return false;
		}

		if(empty($this->clm_no)) trigger_error("Use SetClmNo(clm_no) method first !!", E_USER_ERROR);

		#########################################
		#	Order Class Object Create			#
		#########################################
		$order = new Order($this->user);
		$order->SetOrdNo($ord_no);
		
		#########################################
		#	Jaego Class Object Create			#
		#########################################
		$jaego = new Jaego($this->user);

		if( $next_clm_state == 60 ) {	// 교환
			$type = "5";
		} else if ($next_clm_state == 61 ) {	// 환불
			$type = "6";
		}

		// 클레임 정보 얻기
		$sql = "
			select
				o.ord_state,c.dlv_amt, c.dlv_add_amt, c.dlv_ret_amt, c.dlv_enc_amt, 'N' as stocked_yn,
				d.stock_state,d.clm_qty,d.jaego_yn,d.jaego_reason
			from claim c
				inner join order_opt o on c.ord_opt_no = o.ord_opt_no
				inner join claim_detail d on c.clm_no = d.clm_no
			where c.clm_no = '$this->clm_no'
			order by c.clm_no desc
        ";

        $row = DB::selectOne($sql);

		if(!empty($row->ord_state)){
			$dlv_amt = $row->dlv_amt;
			$dlv_add_amt = $row->dlv_add_amt;
			$dlv_ret_amt = $row->dlv_ret_amt;
			$dlv_enc_amt = $row->dlv_enc_amt;
			$clm_qty = $row->clm_qty;
			$jaego_yn = $row->jaego_yn;
			$jaego_reason = $row->jaego_reason;
			$stocked_yn = $row->stocked_yn;	// 재고처리 여부
			$ord_state = $row->ord_state;

			//
			// 매출 정보 얻기
			// 2012-07-26 knight : and w.ord_state = 10 > and w.ord_state = 5 로 변경
			//
			$sql = " /* admin : order/ord01_cmd.php (37) */
				select
					goods_no, goods_sub, goods_opt, w.ord_opt_no,price,
					ifnull(ws.qty,w.qty) as qty,
					ifnull(ws.wonga,w.wonga) as wonga,
					ifnull(ws.ord_state,w.ord_state) as ord_state,
					recv_amt, point_apply_amt, coupon_apply_amt, dc_apply_amt, pay_fee,
					com_id, com_rate, ord_kind, ord_type, invoice_no, sales_com_fee,
					coupon_no, com_coupon_ratio
				from order_opt_wonga w left outer join (
						select ord_opt_no,ord_state,qty,wonga
						from order_opt_wonga where ord_opt_no = '$ord_opt_no' and ord_state = 30
					) ws on w.ord_opt_no = ws.ord_opt_no
				where w.ord_opt_no = '$ord_opt_no' and w.ord_state = 5
				order by w.ord_wonga_no desc
            ";

            $rs_wonga = DB::select($sql);

            if ($clm_qty > 0) {
                foreach ($rs_wonga as $row ) {
                    $goods_no = $row->goods_no;
                    $goods_sub = $row->goods_sub;
                    $goods_opt = $row->goods_opt;
                    $ord_opt_no = $row->ord_opt_no;
                    $qty = $row->qty ? $row->qty : 0;
                    $wonga = $row->wonga ? $row->wonga : 0;
                    $price = $row->price ? $row->price : 0;
                    $recv_amt = $row->recv_amt ? $row->recv_amt : 0;
                    $point_apply_amt = $row->point_apply_amt ? $row->point_apply_amt : 0;
                    $coupon_apply_amt = $row->coupon_apply_amt ? $row->coupon_apply_amt : 0;
                    $dc_apply_amt = $row->dc_apply_amt ? $row->dc_apply_amt : 0;
                    $pay_fee = $row->pay_fee ? $row->pay_fee : 0;
                    $com_id = $row->com_id;
                    $com_rate = $row->com_rate;
                    $ord_kind = $row->ord_kind;
                    $ord_type = $row->ord_type;	//  2008-05-30 : knight 추가
                    $invoice_no = $row->invoice_no;
                    $sales_com_fee = $row->sales_com_fee;

                    $coupon_no = $row->coupon_no;	 // 2008-05-30 : knight 추가
                    $com_coupon_ratio = $row->com_coupon_ratio;	// 2008-05-30 : knight 추가

                    // 마이너스 매출로... (recv_amt 를 -로 변경)
                    $jaego_wonga = $wonga;
                    $wonga = $wonga * (-1);
                    $price = $price * (-1);
                    $sales_com_fee = $sales_com_fee * (-1);

                    if($qty > $clm_qty){
                        $qty = $clm_qty;
                    }
                    $clm_qty -= $qty;

                    // 마이너스 매출 저장
                    $order->SetOrdOptNo($ord_opt_no);
                    $order->__InsertOptWonga(
                        array(
                            "goods_no" => $goods_no,
                            "goods_sub" => $goods_sub,
                            "goods_opt" => $goods_opt,
                            "ord_opt_no" => $ord_opt_no,
                            "qty" => $qty,
                            "wonga" => $wonga,
                            "price" => $price,
                            "dlv_amt" => $dlv_amt,
                            "dlv_ret_amt" => $dlv_ret_amt,
                            "dlv_add_amt" => $dlv_add_amt,
                            "dlv_enc_amt" => $dlv_enc_amt,
                            "recv_amt" => $recv_amt,
                            "point_apply_amt" => $point_apply_amt,
                            "coupon_apply_amt" => $coupon_apply_amt,
                            "dc_apply_amt" => $dc_apply_amt,
                            "pay_fee" => $pay_fee,
                            "com_id" => $com_id,
                            "com_rate" => $com_rate,
                            "ord_state" => $next_clm_state,
                            "ord_kind" => $ord_kind,
                            "ord_type" => $ord_type,
                            "invoice_no" => $invoice_no,
                            "ord_state_date" => date("Ymd"),
                            "coupon_no" => $coupon_no,
                            "com_coupon_ratio" => $com_coupon_ratio,
                            "sales_com_fee" => $sales_com_fee
                        )
                    );

                    if($stocked_yn == "N" && $ord_state >= 10)
                    {
                        // 재고 처리
                        $jaego->Plus( array(
                            "type" => $type,
                            "etc" => $jaego_reason,
                            "qty" => $qty,
                            "goods_no" => $goods_no,
                            "goods_sub" => $goods_sub,
                            "goods_opt" => $goods_opt,
                            "wonga" => $jaego_wonga,
                            "invoice_no" => $invoice_no,
                            "ord_no" => $ord_no,
                            "ord_opt_no" => $ord_opt_no,
                            "ord_state" => $ord_state
                        ));

                        // 재고처리 안 할 경우 강제 재고조정
                        if( $jaego_yn == "n") {
                            $jaego->MinusQty($goods_no,$goods_sub, $goods_opt, $qty);
                            if($ord_state >= 30) {
                                $jaego->MinusStockQty($goods_no,$goods_sub,$goods_opt,
                                                    $qty,9,$invoice_no,"재고 미처리 (".$jaego_reason.")",$com_id,$ord_no,$ord_opt_no);
                            }
                        }
                    }
                }
            }
		}
		return true;
    }

	/*
 		Function: CompleteChange
 		교환완료 시 자식 주문건의 입금상태를 완료로 변경

		See Also:
			- <Order::SetOrdOptNo>
			- <Order::DlvStart>
			- <Order::__MasterOrdState>

 		Applied:
 			- /webapps/order/ord01_detail.php
	*/
	public function CompleteChange()
	{
		if(empty($this->ord_opt_no)) trigger_error("Use SetOrdOptNo(ord_opt_no) method first !!", E_USER_ERROR);

		$c_ord_no = "";

		// 자식 주문건의 주문번호 정보
		$sql = "
			select ord_no
			from order_opt
			where p_ord_opt_no = '$this->ord_opt_no'
        ";

        $row = DB::selectOne($sql);

		if(!empty($row->ord_no)) $c_ord_no = $row->ord_no;

        if( $c_ord_no == "" ) return;

        // 자식주문건의 결제 - 입금완료 처리
        $sql = "
            select
                pay_type, pay_nm, pay_amt, pay_stat, bank_inpnm, bank_code,
                bank_number, card_code, card_isscode, card_quota, card_appr_no, card_appr_dm,
                card_tid, card_msg, ord_dm, upd_dm, pay_ypoint, pay_point, pay_baesong, card_name, nointf
            from payment
            where ord_no = '$c_ord_no'
        ";

        $row = DB::selectOne($sql);

        if (!empty($row->pay_type))
        {
            //$row			= $rs->fields;
            $pay_type		= $row->pay_type;
            $pay_nm			= $row->pay_nm;
            $pay_amt		= $row->pay_amt;
            $bank_inpnm		= $row->bank_inpnm;
            $bank_code		= $row->bank_code;
            $bank_number	= $row->bank_number;
            $card_code		= $row->card_code;
            $card_isscode	= $row->card_isscode;
            $card_quota		= $row->card_quota;
            $card_appr_no	= $row->card_appr_no;
            $card_appr_dm	= $row->card_appr_dm;
            $card_tid		= $row->card_tid;
            $card_msg		= $row->card_msg;
            $pay_ypoint		= $row->pay_ypoint;
            $pay_point		= $row->pay_point;
            $pay_baesong	= $row->pay_baesong;
            $card_name		= $row->card_name;
            $nointf			= $row->nointf;

            // 자식 주문건의 결제정보 완료처리
            $sql = "
                /* admin : order/ord01.php (32) */
                update payment set
                    pay_type		= '$pay_type'
                    ,pay_nm			= '$pay_nm'
                    ,pay_amt		= '$pay_amt'
                    ,pay_stat		= 1
                    ,bank_inpnm		= '$bank_inpnm'
                    ,bank_code		= '$bank_code'
                    ,bank_number	= '$bank_number'
                    ,card_code		= '$card_code'
                    ,card_isscode	= '$card_isscode'
                    ,card_quota		= '$card_quota'
                    ,card_appr_no	= '$card_appr_no'
                    ,card_appr_dm	= '$card_appr_dm'
                    ,card_tid		= '$card_tid'
                    ,card_msg		= '$card_msg'
                    ,upd_dm			= now()
                    ,pay_ypoint		= '$pay_ypoint'
                    ,pay_point		= '$pay_point'
                    ,pay_baesong	= '$pay_baesong'
                    ,card_name		= '$card_name'
                    ,nointf			= '$nointf'
                where ord_no		= '$c_ord_no'
            ";
            DB::update($sql);
        }
    }

	/*
 		Function: UpdateClaim
 		클레임 수정

 		Parameters:
 			$clm_state - 클레임 상태
 			$clm_reason - 클레임 사유
 			$refund_yn - 환불 여부
 			$refund_amt - 환불 금액
 			$refund_bank - 환불 은행
 			$refund_account - 환불 계좌 번호
 			$refund_nm - 환불 계좌 예금주 명
 			$dlv_deduct - 배송료 차감
 			$update_field - 상태에 따른 수정 필드 쿼리
	*/
	public function UpdateClaim( $param ){

		if(empty($this->ord_opt_no)) trigger_error("Use SetOrdOptNo() method first !!", E_USER_ERROR);
		if(empty($this->clm_no)) trigger_error("Use SetClmNo() method first !!", E_USER_ERROR);

		$clm_state = $param["clm_state"];
		$clm_reason = $param["clm_reason"];
		$refund_yn = $param["refund_yn"];
		$refund_amt = $param["refund_amt"];
		$refund_bank = $param["refund_bank"];
		$refund_account = $param["refund_account"];
		$refund_nm = $param["refund_nm"];
		$dlv_deduct = $param["dlv_deduct"];
		$update_field = $param["update_field"];

		$sql = " -- [".$this->user["id"]."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
			update claim set
				ord_opt_no = '$this->ord_opt_no'
				,clm_state = '$clm_state'
				,clm_reason = '$clm_reason'
				,refund_yn = '$refund_yn'
				,last_up_date = now()
				,dlv_deduct = '$dlv_deduct'
				$update_field
			where
				clm_no = '$this->clm_no'
        ";

        DB::update($sql);
    }


	/*
 		Function: InsertClaim
 		클레임 등록

 		Parameters:
			$claim - claim object (array)

		Returns:
			$clm_no - 클레임 번호

		Values:
 			$clm_state - 클레임 상태
 			$clm_reason - 클레임 사유
 			$refund_yn - 환불 여부
 			$refund_amt - 환불 금액
 			$refund_bank - 환불 은행
 			$refund_account - 환불 계좌 번호
 			$refund_nm - 환불 계좌 예금주 명
 			$req_date - 클레임 요청일
 			$req_nm - 클레임 요청자 명
 			$end_date - 클레임 종료일
 			$end_nm - 클레임 종료자 명
 			$goods_no - 상품 번호
 			$goods_sub - 상품 번호 서브

		Comment:
			- 최초 등록 시 클레임 요청 또는 주문 취소
	*/
	public function InsertClaim( $claim ){

		if(empty($this->ord_opt_no)) trigger_error("Use SetOrdOptNo() method first !!", E_USER_ERROR);

		$clm_state = $claim["clm_state"];
		$clm_reason = $claim["clm_reason"];
		$refund_yn = $claim["refund_yn"];
		$refund_amt = $claim["refund_amt"];
		$refund_bank = $claim["refund_bank"];
		$refund_account = $claim["refund_account"];
		$refund_nm = $claim["refund_nm"];
		$req_date = $claim["req_date"];
		$req_nm = $claim["req_nm"];
		$end_date = $claim["end_date"];
		$end_nm = $claim["end_nm"];
		$goods_no = $claim["goods_no"];
		$goods_sub = $claim["goods_sub"];
		$memo = $claim["memo"];

        return DB::table('claim')->insertGetId([
            'ord_opt_no' => $this->ord_opt_no,
            'clm_state' => $clm_state,
            'clm_reason' => $clm_reason,
            'refund_yn' => $refund_yn,
            'req_date' =>$req_date,
            'req_nm' => $req_nm,
            'end_date' => $end_date,
            'end_nm' => $end_nm,
            'goods_no' => $goods_no,
            'goods_sub' => $goods_sub,
            'last_up_date' => now(),
            'memo' => $memo
        ]);
    }

	/*
 		Function: UpdateClaimDetail
 		클레임 상세내역(claim_detail) 내용 변경

 		Parameters:
 			$clm_qty - 클레임 수량
 			$jaego_yn - 재고 미처리 여부
 			$jaego_reason - 재고 미처리 사유
 			$stock_state - 재고 상태
	*/
	public function UpdateClaimDetail( $claim_detail ){

		if(empty($this->clm_det_no)) trigger_error("Use SetClmDetNo(clm_det_no) method first !!", E_USER_ERROR);

		$clm_qty = $claim_detail["clm_qty"];
		$jaego_yn = $claim_detail["jaego_yn"];
		$jaego_reason = $claim_detail["jaego_reason"];
		$stock_state = $claim_detail["stock_state"];

		$sql = "
			update claim_detail set
				clm_qty = '$clm_qty',
				jaego_yn = '$jaego_yn',
				jaego_reason = '$jaego_reason',
				stock_state = '$stock_state'
			where
				clm_det_no = '$this->clm_det_no'
        ";

        DB::update($sql);
    }

	/*
 		Function: InsertClaimDetail
 		클레임 상세내역(claim_detail) 등록

 		Parameters:
 			$ord_wonga_no - 주문 매출 번호
 			$clm_qty - 클레임 수량
 			$jaego_yn - 재고 미처리 여부
 			$jaego_reason - 재고 미처리 사유
 			$stock_state - 재고 상태
	*/
	public function InsertClaimDetail( $claim_detail ){
		if(empty($this->clm_no)) trigger_error("Use SetClmNo(clm_no) method first !!", E_USER_ERROR);

		$ord_wonga_no = $claim_detail["ord_wonga_no"];
		$clm_qty = $claim_detail["clm_qty"];
		$jaego_yn = $claim_detail["jaego_yn"];
		$jaego_reason = $claim_detail["jaego_reason"];
		$stock_state = $claim_detail["stock_state"];

		if(! $ord_wonga_no) $ord_wonga_no = 0;

		$sql = "
			insert into claim_detail (
				clm_no, ord_wonga_no, clm_qty, jaego_yn, jaego_reason, stock_state
			) values (
				'$this->clm_no', '$ord_wonga_no', '$clm_qty', '$jaego_yn','$jaego_reason', '$stock_state'
			)
        ";

        DB::insert($sql);
	}

	/*
 		Function: CompleteRefund
 		클레임 환불 완료 처리 시 클레임 테이블의 클레임 상태를 '환불완료' 로 변경

 		Parameters:
 			$memo - 클레임 메모
	*/
	public function CompleteRefund($refund_type, $memo = ""){

		if(empty($this->ord_opt_no)) trigger_error("Use SetOrdOptNo(ord_opt_no) method first !!", E_USER_ERROR);

		$name = $this->user["name"];

		// 클레임 마스터
		$sql = "
			update claim set
				clm_state = 61
				,last_up_date = now()
				,end_date = now()
				,end_nm = '$name'
				,refund_type = '$refund_type'
				,memo = '$memo'
			where ord_opt_no = '$this->ord_opt_no'
        ";

        DB::update($sql);
	}

	/*
 		Function: UpdateStoreOrder
 		매장환불 시 order_opt 수정 및 order_opt_wonga 등록

		Returns:
			$ord_wonga_no
	*/
	public function UpdateStoreOrder($ord)
	{
		// order_opt > clm_state 변경
		DB::table('order_opt')
			->where('ord_opt_no', '=', $this->ord_opt_no)
			->update(['clm_state' => CLAIM_STATE_REFUND]);

		// order_opt_wonga 등록 전 중복체크
		$sql = "
			select count(*) as cnt
			from order_opt_wonga
			where ord_opt_no = :ord_opt_no and ord_state = :ord_state
		";
		$rows = DB::selectOne($sql, ['ord_opt_no' => $this->ord_opt_no, 'ord_state' => CLAIM_STATE_REFUND]);
		$ord_wonga_no = 0;

		if ($rows->cnt < 1) {
			// order_opt_wonga 등록
			$ord_wonga_no = DB::table('order_opt_wonga')->insertGetId([
				'goods_no'			=> $ord->goods_no,
				'goods_sub'			=> $ord->goods_sub,
				'ord_opt_no'		=> $ord->ord_opt_no,
				'goods_opt'			=> $ord->goods_opt,
				'qty'				=> $ord->qty,
				'wonga'				=> $ord->wonga,
				'price'				=> $ord->price,
				'dlv_amt'			=> $ord->dlv_amt,
				'dlv_ret_amt'		=> '',
				'dlv_add_amt'		=> '',
				'dlv_enc_amt'		=> '',
				'recv_amt'			=> $ord->recv_amt,
				'point_apply_amt'	=> $ord->point_amt,
				'coupon_apply_amt'	=> $ord->coupon_amt,
				'dc_apply_amt'		=> $ord->dc_amt,
				'com_id'			=> $ord->com_id,
				'com_rate'			=> $ord->com_rate,
				'ord_state'			=> CLAIM_STATE_REFUND,
				'ord_kind'			=> $ord->ord_kind,
				'ord_type'			=> $ord->ord_type,
				'invoice_no'		=> '',
				'ord_state_date'	=> date('Ymd'),
				'coupon_no'			=> $ord->coupon_no,
				'com_coupon_ratio'	=> $ord->com_coupon_ratio,
				'sales_com_fee'		=> $ord->sales_com_fee,
				'prd_cd'			=> $ord->prd_cd,
				'store_cd'			=> $ord->store_cd,
			]);
		}

		return $ord_wonga_no;
	}

	/*
 		Function: InsertStoreClaim
 		매장환불 시 claim, claim_detail, claim_memo 등록

		Returns:
			$clm_no - 클레임 번호
	*/
	public function InsertStoreClaim($claim, $ord)
	{
		// claim 등록
		$clm_no = DB::table('claim')->insertGetId([
			'ord_opt_no' 		=> $this->ord_opt_no,
			'clm_type' 			=> 2, // 환불 (code - G_CLM_TYPE)
			'clm_state' 		=> CLAIM_STATE_REFUND, // 매장환불 시 클레임 완료처리
			'clm_reason' 		=> $claim['clm_reason'],
			'refund_no' 		=> $this->ord_opt_no,
			'ref_amt' 			=> $ord->ref_amt,
			'refund_yn' 		=> 'y',
			'refund_price' 		=> $ord->refund_price,
			'refund_point_amt' 	=> $ord->refund_point_amt,
			'refund_coupon_amt' => 0, // 추후 쿠폰기능 추가 시 수정
			'refund_pay_fee' 	=> 0,
			'refund_pay_fee_yn' => null,
			'refund_tax_fee' 	=> 0,
			'refund_etc_amt' 	=> 0,
			'refund_gift_amt' 	=> 0,
			'refund_amt' 		=> $ord->refund_amt,
			'refund_bank' 		=> $claim['refund_bank'],
			'refund_account' 	=> $claim['refund_account'],
			'refund_nm' 		=> $claim['refund_nm'],
			'memo' 				=> $claim['memo'],
			'req_date' 			=> now(),
			'req_nm' 			=> $this->user['name'],
			'proc_date' 		=> now(),
			'proc_nm' 			=> $this->user['name'],
			'end_date' 			=> now(),
			'end_nm' 			=> $this->user['name'],
			'goods_no' 			=> $ord->goods_no,
			'goods_sub' 		=> $ord->goods_sub,
			'last_up_date' 		=> now(),
		]);

		// claim_detail 등록
        DB::table('claim_detail')->insert([
			'clm_no' 		=> $clm_no,
			'ord_wonga_no' 	=> 0,
			'clm_qty' 		=> $ord->qty,
			'jaego_yn' 		=> 'Y',
			'jaego_reason' 	=> '',
			'stock_state' 	=> 1,
        ]);

		// claim_memo 등록
        DB::table('claim_memo')->insert([
			'ord_opt_no' 	=> $this->ord_opt_no,
			'clm_no' 		=> $clm_no,
			'ord_state' 	=> $ord->ord_state,
			'clm_state' 	=> CLAIM_STATE_REFUND,
			'cs_form' 		=> CLAIM_CS_FORM,
			'memo' 			=> $claim['memo'],
			'admin_id' 		=> $this->user['id'],
			'admin_nm' 		=> $this->user['name'],
			'regi_date' 	=> now(),
        ]);

		return $clm_no;
	}

	/*
 		Function: UpdateStoreStockToRefund
 		매장환불 시 재고 업데이트 - product_stock, product_stock_store 수정 및 product_stock_hst 등록

		Returns:
			$success_code - 재고 업데이트 성공여부
	*/
	public function UpdateStoreStockToRefund($ord)
	{
		$store_cd = $ord->store_cd;
		$prd_cd = $ord->prd_cd;
		$qty = $ord->qty;

		if ($store_cd != '' && $store_cd != null && $prd_cd != '' && $prd_cd != null) {

			// 매장재고 업데이트
			$success_code = DB::table('product_stock_store')
				->where('prd_cd', '=', $prd_cd)
				->where('store_cd', '=', $store_cd) 
				->update([
					'qty' => DB::raw('qty + ' . $qty),
					'wqty' => DB::raw('wqty + ' . $qty),
					'ut' => now(),
				]);
			
			if ($success_code < 1) return $success_code;

			// 전체재고 업데이트
			$success_code = DB::table('product_stock')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'qty_wonga'	=> DB::raw('qty_wonga + ' . ($qty * ($ord->wonga = 0))),
					'out_qty' => DB::raw('out_qty - ' . $qty),
					'qty' => DB::raw('qty + ' . $qty),
					'ut' => now(),
				]);

			if ($success_code < 1) return $success_code;

			// 재고이력 등록
			$success_code = DB::table('product_stock_hst')
				->insert([
					'goods_no' => $ord->goods_no,
					'prd_cd' => $prd_cd,
					'goods_opt' => $ord->goods_opt,
					'location_cd' => $store_cd,
					'location_type' => 'STORE',
					'type' => REFUND_PRODUCT_STOCK_TYPE,
					'price' => $ord->price,
					'wonga' => $ord->wonga,
					'qty' => $qty,
					'stock_state_date' => date('Ymd'),
					'ord_opt_no' => $ord->ord_opt_no,
					'comment' => '매장환불',
					'rt' => now(),
					'admin_id' => $this->user['id'],
					'admin_nm' => $this->user['name'],
				]);
		}

		return $success_code;
	}
}
