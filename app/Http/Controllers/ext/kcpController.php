<?php

namespace App\Http\Controllers\ext;

use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Jaego;
use App\Models\Order;
use App\Models\Point;
use App\Models\SMS;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class kcpController extends Controller
{
    //


    public function index()
    {
    }

    public function common_return(Request $req)
    {

        $C_ADMIN_ID = "admin";
        $C_ADMIN_NAME = "관리자";

        $user["id"] = $C_ADMIN_ID;
        $user["name"] = $C_ADMIN_NAME;

        print_r($_POST);

        $C_REMOTE_ADDR = $req->ip();

        $white_ips = [
            "203.238.36.58",
            "203.238.36.160",
            "203.238.36.161",
            "203.238.36.173",
            "203.238.36.178",
            "211.238.131.119",
            "211.238.131.10",
            "211.197.29.109",
            "211.197.29.109",
            "203.238.36.173",
            "203.238.36.178",
            "103.215.144.173",
            "103.215.144.174",
            "127.0.0.1"
        ];

        // 아이피 검사
        if (in_array($C_REMOTE_ADDR, $white_ips)) {

            //$conn->LogSQL(); // turn on logging
            //$conn->debug=true;

            $common_insert_idx = 0;

            /* ============================================================================== */
            /* =   PAGE : 공통 통보 PAGE                                                    = */
            /* = -------------------------------------------------------------------------- = */
            /* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.                   = */
            /* ============================================================================== */


            /* ============================================================================== */
            /* =   01. 공통 통보 페이지 설명(필독!!)                                        = */
            /* = -------------------------------------------------------------------------- = */
            /* =   에스크로 서비스의 경우, 가상계좌 입금 통보 데이터와 가상계좌 환불        = */
            /* =   통보 데이터, 구매확인/구매취소 통보 데이터, 배송시작 통보 데이터 등을    = */
            /* =   KCP 를 통해 별도로 통보 받을 수 있습니다. 이러한 통보 데이터를 받기      = */
            /* =   위해 가맹점측은 결과를 전송받는 페이지를 마련해 놓아야 합니다.           = */
            /* =   현재의 페이지를 업체에 맞게 수정하신 후, KCP 관리자 페이지에 등록해      = */
            /* =   주시기 바랍니다. 등록 방법은 연동 매뉴얼을 참고하시기 바랍니다.          = */
            /* ============================================================================== */


            /* ============================================================================== */
            /* =   02. 공통 통보 데이터 받기                                                = */
            /* = -------------------------------------------------------------------------- = */
            $site_cd = $_POST ["site_cd"];                    // 사이트 코드
            $tno = $_POST ["tno"];                    // KCP 거래번호
            $order_no = $_POST ["order_no"];                    // 주문번호
            $tx_cd = $_POST ["tx_cd"];                    // 업무처리 구분 코드
            $tx_tm = $_POST ["tx_tm"];                 // 업무처리 완료 시간


            /* = -------------------------------------------------------------------------- = */
            $ipgm_name = "";                                    // 주문자명
            $remitter = "";                                    // 입금자명
            $ipgm_mnyx = "";                                    // 입금 금액
            $bank_code = "";                                    // 은행코드
            $account = "";                                    // 가상계좌 입금계좌번호
            $op_cd = "";                                    // 처리구분 코드
            $noti_id = "";                                    // 통보 아이디
            /* = -------------------------------------------------------------------------- = */
            $refund_nm = "";                                    // 환불계좌주명
            $refund_mny = "";                                    // 환불금액
            $bank_code = "";                                    // 은행코드
            /* = -------------------------------------------------------------------------- = */
            $st_cd = "";                                    // 구매확인 코드
            $can_msg = "";                                    // 구매취소 사유
            /* = -------------------------------------------------------------------------- = */
            $waybill_no = "";                                    // 운송장 번호
            $waybill_corp = "";                                    // 택배 업체명
            $cash_a_no = "";

            /* = -------------------------------------------------------------------------- = */
            /* =   02-1. 가상계좌 입금 통보 데이터 받기                                     = */
            /* = -------------------------------------------------------------------------- = */
            if ($tx_cd == "TX00") {
                $ipgm_name = $_POST["ipgm_name"];                // 주문자명
                $remitter = $_POST["remitter"];                // 입금자명
                $ipgm_mnyx = $_POST["ipgm_mnyx"];                // 입금 금액
                $ipgm_time = $req->input("ipgm_time", "");                // 입금 시각
                $bank_code = $_POST["bank_code"];                // 은행코드
                $account = $_POST["account"];                // 가상계좌 입금계좌번호
                $op_cd = $_POST["op_cd"];                // 처리구분 코드
                $noti_id = $req->input("noti_id", "");                // 통보 아이디
                $cash_a_no = $req->input("cash_a_no", "");         // 현금영수증 승인번호
            }

            /* = -------------------------------------------------------------------------- = */
            /* =   02-2. 가상계좌 환불 통보 데이터 받기                                     = */
            /* = -------------------------------------------------------------------------- = */
            else if ($tx_cd == "TX01") {
                $refund_nm = $_POST["refund_nm"];              // 환불계좌주명
                $refund_mny = $_POST["refund_mny"];              // 환불금액
                $bank_code = $_POST["bank_code"];              // 은행코드
            }

            /* = -------------------------------------------------------------------------- = */
            /* =   02-3. 구매확인/구매취소 통보 데이터 받기                                 = */
            /* = -------------------------------------------------------------------------- = */
            else if ($tx_cd == "TX02") {
                $st_cd = $_POST["st_cd"];                        // 구매확인 코드

                if ($st_cd == "N")                               // 구매확인 상태가 구매취소인 경우
                {
                    $can_msg = $_POST["can_msg"];                // 구매취소 사유
                }
            }

            /* = -------------------------------------------------------------------------- = */
            /* =   02-4. 배송시작 통보 데이터 받기                                          = */
            /* = -------------------------------------------------------------------------- = */
            else if ($tx_cd == "TX03") {
                $waybill_no = $_POST["waybill_no"];          // 운송장 번호
                $waybill_corp = $_POST["waybill_corp"];          // 택배 업체명
            }

            /* = -------------------------------------------------------------------------- = */
            /* =   02-5. 모바일안심결제 통보 데이터 받기                                    = */
            /* = -------------------------------------------------------------------------- = */
            else if ($tx_cd == "TX08") {
                $ipgm_mnyx = $_POST["ipgm_mnyx"];                // 입금 금액
                $bank_code = $_POST["bank_code"];                // 은행코드
            }
            /* ============================================================================== */


            /* ============================================================================== */
            /* =   03. 공통 통보 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.      = */
            /* = -------------------------------------------------------------------------- = */
            /* =   통보 결과를 DB 작업 하는 과정에서 정상적으로 통보된 건에 대해 DB 작업을  = */
            /* =   실패하여 DB update 가 완료되지 않은 경우, 결과를 재통보 받을 수 있는     = */
            /* =   프로세스가 구성되어 있습니다. 소스에서 result 라는 Form 값을 생성 하신   = */
            /* =   후, DB 작업이 성공 한 경우, result 의 값을 "0000" 로 세팅해 주시고,      = */
            /* =   DB 작업이 실패 한 경우, result 의 값을 "0000" 이외의 값으로 세팅해 주시  = */
            /* =   기 바랍니다. result 값이 "0000" 이 아닌 경우에는 재통보를 받게 됩니다.   = */
            /* = -------------------------------------------------------------------------- = */

            /* = -------------------------------------------------------------------------- = */
            /* =   03-1. 가상계좌 입금 통보 데이터 DB 처리 작업 부분                        = */
            /* = -------------------------------------------------------------------------- = */


            /* 테스트 용 */
            /*
            $conn->debug = true;

            // 입금확인
            $site_cd	= "T0000";			// 사이트 코드
            $tno		= "20120726908184";	// KCP 거래번호
            $order_no	= "201207261411561241";	// 주문번호
            $tx_cd		= "TX00";				// 업무처리 구분 코드
            $tx_tm		= "20120726133505";		// 업무처리 완료 시간
            $ipgm_name	= "이희천";
            $remitter	= "이희천";
            $ipgm_mnyx	= "1000000";
            $bank_code	= "신한은행";
            $account	= "T0400000040177";
            $op_cd		= 1;

            // 입금취소
            $site_cd	= "W7019";				// 사이트 코드
            $tno		= "20080804342354";		// KCP 거래번호
            $order_no	= "2008080410599ec6a5";	// 주문번호
            $tx_cd		= "TX00";				// 업무처리 구분 코드
            $tx_tm		= "20080429104422";		// 업무처리 완료 시간
            $account	= "12301230123";
            $ipgm_name	= "손상모";
            $remitter	= "손상모";
            $ipgm_mnyx	= "50000";
            $bank_code	= "국민";
            $account	= "12345";
            $op_cd		= 13;
            */

            DB::beginTransaction();

            try {

                DB::table("common_return")->insert([
                    "site_cd" => $site_cd,
                    "tno" => $tno,
                    "order_no" => $order_no,
                    "tx_cd" => $tx_cd,
                    "tx_tm" => $tx_tm,
                    "ip" => $C_REMOTE_ADDR,
                    "waybill_no" => $waybill_no,
                    "waybill_corp" => $waybill_corp,
                    "ipgm_name" => $ipgm_name,
                    "remitter" => $remitter,
                    "ipgm_mnyx" => $ipgm_mnyx,
                    "bank_code" => $bank_code,
                    "account" => $account,
                    "op_cd" => $op_cd,
                    "noti_id" => $noti_id,
                    "flag" => "N",
                    "msg" => "",
                    "admin_flag" => "N",
                    "regi_date" => DB::raw("now()")
                ]);
                $common_insert_idx = DB::getPdo()->lastInsertId();

                if ($tx_cd == "TX00") {

                    $ordclass = new Order($user);
                    $ordclass->SetOrdNo($order_no);

                    if ($op_cd == "13") {    // 입금 무효

                        /**
                         * 주문상태 로그
                         */
                        $ordclass->AddStateLog([
                            "ord_no" => $order_no, "ord_state" => "1", "comment" => "PG(Cancel)"
                        ]);

                        // KCP 자동 입금 무효 처리
                        $this->bank_cancel($order_no, $user);

                        DB::table("common_return")
                            ->where("order_no", $order_no)
                            ->where("idx", $common_insert_idx)
                            ->update([
                                'msg' => 'KCP 입금 무효 처리 성공',
                                'flag' => 'Y',
                                'admin_flag' => 'Y'
                            ]);

                        $this->printResult("0000");

                    } else {    // 입금 정상

                        $ret_pay_stat = $ordclass->CheckPayment();

                        if ($ret_pay_stat != 0) { // 입금 정보 오류

                            if ($ret_pay_stat < 0) {
                                $tmp_msg = "입금데이터(payment)가 존재하지 않습니다.";
                            } else {
                                $tmp_msg = "입금상태가 미입금 상태가 아닙니다.";
                            }

                            DB::table("common_return")
                                ->where("order_no", $order_no)
                                ->where("idx", $common_insert_idx)
                                ->update([
                                    'msg' => $tmp_msg
                                ]);

                            $this->printResult("0000");

                        } else {    // 입금 정보 정상

                            $ret_jaego = $ordclass->CheckJaego(); // 재고 체크
                            $conf = new Conf();

                            if ($ret_jaego) {

                                /**
                                 * 주문상태 로그
                                 */
                                $ordclass->AddStateLog(["ord_no" => $order_no, "ord_state" => "10", "comment" => "PG"]);

                                // 매출정보 저장/재고차감/입금완료 처리
                                $ordclass->CompleteOrder();

                                DB::table("common_return")
                                    ->where("order_no", $order_no)
                                    ->where("idx", $common_insert_idx)
                                    ->update([
                                        'msg' => 'KCP 자동 입금완료 처리 성공',
                                        'flag' => 'Y',
                                        'admin_flag' => 'Y'
                                    ]);

                            } else {

                                /**
                                 * 주문상태 로그
                                 */
                                $ordclass->AddStateLog(["ord_no" => $order_no, "ord_state" => "5", "comment" => "PG(Sold Out)"]);

                                DB::table("common_return")
                                    ->where("order_no", $order_no)
                                    ->where("idx", $common_insert_idx)
                                    ->update([
                                        'msg' => '재고가 부족하여 입금 처리할 수 없습니다.',
                                    ]);

                                $ordclass->OutOfScockAfterPaid();

                                //$conf = new Conf();
                                $cfg_shop_name = $conf->getConfigValue("shop", "name");
                                $cfg_sms_yn = $conf->getConfigValue("sms", "sms_yn");
                                $cfg_out_of_stock_yn = $conf->getConfigValue("sms", "out_of_stock_yn");
                                $cfg_out_of_stock_msg = $conf->getConfigValue("sms", "out_of_stock_msg");

                                if ($cfg_sms_yn == "Y") {
                                    if ($cfg_out_of_stock_yn == "Y") {
                                        // 품절 알림 SMS
                                        $sql = /** @lang text */
                                            "
                                            select user_nm, mobile
                                            from order_mst
                                            where ord_no = '$order_no'
                                    ";
                                        $row = (array)DB::selectone($sql);
                                        if ($row) {

                                            $user_nm = $row["user_nm"];    // 주문자이름
                                            $mobile = $row["mobile"];        // 핸드폰번호

                                            $sms = new SMS();
                                            $msgarr = array(
                                                "SHOP_NAME" => $cfg_shop_name,
                                            );
                                            $sms_msg = $sms->MsgReplace($cfg_out_of_stock_msg, $msgarr);
                                            /*
                                            if($cfg_kakao_yn == "Y" && $template_code != ""){
                                                $sms->SendKakao($template_code, $user_mobile, $user_name, $sms_msg, $msgarr, '', $btnarr);
                                            } else {
                                                $sms->Send($sms_msg, $user_mobile, $user_name);
                                            }
                                            */
                                            //$sms->Send($sms_msg, $mobile, $user_nm);
                                            $sms->SendAligoSMS( $mobile, $sms_msg, $user_nm );

                                        }
                                    }
                                }

                                $this->printResult("0000");
                            }

                            $cfg_cash_use_yn = $conf->getConfigValue("shop", "cash_use_yn", "N");

                            // 현금영수증 발행
                            if ($cash_a_no != "" && $cfg_cash_use_yn == "Y") {
                                // 주문 정보 얻기
                                $user_id = "";
                                $user_nm = "";
                                $pay_amy = 0;
                                $sql = /** @lang text */
                                    "
                                select
                                    if((a.user_id = ''), 'guest', a.user_id) as user_id, a.user_nm, b.pay_amt
                                from order_mst a
                                    left outer join payment b on a.ord_no = b.ord_no
                                where a.ord_no = '$order_no'
                            ";
                                $row = (array)DB::selectone($sql);
                                if ($row) {
                                    $user_id = $row["user_id"];
                                    $user_nm = $row["user_nm"];
                                    $pay_amy = $row["pay_amt"];
                                }

                                // 현금영수증 발행내역 등록
                                $sql = /** @lang text */
                                    "
                                    insert into cash_history (
                                        `cash_no`, `ord_no`, `receipt_no`, `app_time`, `reg_stat`, `reg_desc`, `id_info`, `amt_tot`, `user_id`, `user_nm`, `admin_id`, `admin_nm`, `rt`, `ut`
                                    ) values (
                                        '$tno', '$order_no', '$cash_a_no', '$ipgm_time', '', '', '', '$pay_amy', '$user_id', '$user_nm', 'system', '시스템', now(), now()
                                    )
                                ";
                                DB::insert($sql);

                                // 현금영수증 발행여부 등록
                                $sql = /** @lang text */
                                    "
                                update payment set
                                    cash_yn = 'Y', cash_date = now()
                                where ord_no = '$order_no'
                            ";
                                DB::update($sql);
                            }
                        }
                    }
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-2. 가상계좌 환불 통보 데이터 DB 처리 작업 부분									  = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX01") {
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-3. 구매확인/구매취소 통보 데이터 DB 처리 작업 부분                    = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX02") {
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-4. 배송시작 통보 데이터 DB 처리 작업 부분                             = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX03") {
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-5. 정산보류 통보 데이터 DB 처리 작업 부분                             = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX04") {
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-6. 즉시취소 통보 데이터 DB 처리 작업 부분                             = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX05") {
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-7. 취소 통보 데이터 DB 처리 작업 부분                                 = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX06") {
                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-8. 발급계좌해지 통보 데이터 DB 처리 작업 부분                         = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX07") {

                }

                /* = -------------------------------------------------------------------------- = */
                /* =   03-9. 모바일안심결제 통보 데이터 DB 처리 작업 부분                       = */
                /* = -------------------------------------------------------------------------- = */
                else if ($tx_cd == "TX08") {

                }

                /* ============================================================================== */
                /* =   04. result 값 세팅 하기                                                  = */
                /* ============================================================================== */

                $this->printResult("0000");

                DB::commit();

            } catch(Exception $e){
                DB::rollBack();
                printf("%s",$e->getMessage());
            }


        } else {
            echo "허용된 IP[$C_REMOTE_ADDR]가 아님니다.";
            exit;
        }
    }

    private function bank_cancel($order_no, $user)
    {

        $jaego = new Jaego($user);

        // 적립 포인트 무효화
        $sql = /** @lang text */
            "
		select user_id, point
		from point_list
		where ord_no = '$order_no' and point > 0
	";
        $rows = DB::select($sql);

        for ($i = 0; $i < count($rows); $i++) {
            $row = (array)$rows[$i];
            $point = $row["point"];

            $classPoint = new Point($user);
            $classPoint->SetOrdNo($order_no);
            $classPoint->Admin($point, "REFUND", "REFUND", "사용");
        }


        // 재고 처리 무효화
        // 주문 옵션별 (판매가*수량) 및 정보 얻기
        $sql = /** @lang text */
            "
		select
			a.ord_opt_no, a.qty*a.price opt_tot_price, a.qty, a.price, a.goods_no, a.goods_sub, a.goods_opt, a.point_amt,
            -- a.partner_id, b.pay_fee partner_rate, 
            a.com_id, c.pay_fee com_rate, a.ord_kind, a.add_point
		from order_opt a
			-- left outer join company b on a.partner_id = b.com_id
			left outer join company c on a.com_id = c.com_id
		where a.ord_no = '$order_no'
	";
        $rows = DB::select($sql);
        //echo $sql;
        for ($i = 0; $i < count($rows); $i++) {
            $row = (array)$rows[$i];

            $ord_opt_no = $row['ord_opt_no'];
            $goods_no = $row['goods_no'];
            $goods_sub = $row['goods_sub'];
            $goods_opt = $row['goods_opt'];
            $ord_qty = $row['qty'];

            // 주문 수량
            $cal_qty = $ord_qty;
            $com_id = $row['com_id'];

            $sql2 = " select wonga,invoice_no from order_opt_wonga where ord_opt_no = '$ord_opt_no' ";
            $row2 = (array)DB::selectone($sql2);
            if ($row2) {
                $wonga = $row2['wonga'];
                $invoice_no = $row2['invoice_no'];

                $jaego->Plus(array(
                    "type" => 6,
                    "etc" => "",
                    "qty" => $cal_qty,
                    "goods_no" => $goods_no,
                    "goods_sub" => $goods_sub,
                    "goods_opt" => $goods_opt,
                    "wonga" => $wonga,
                    "invoice_no" => $invoice_no,
                    "ord_no" => $order_no,
                    "ord_opt_no" => $ord_opt_no
                ));
                // 원가 삭제
                $sql1 = " delete from order_opt_wonga where ord_opt_no = '$ord_opt_no' ";
                DB::delete($sql1);
            }
        }

        $sql = " update order_opt set ord_state = '1' where ord_no = '$order_no' ";
        DB::update($sql);

        $sql = " update order_mst set ord_state = '1' where ord_no = '$order_no' ";
        DB::update($sql);

        $sql = " update payment set pay_stat = '0' where ord_no = '$order_no' ";
        DB::update($sql);
    }

    private function printResult($code)
    {
        echo "<html><body><form><input type='hidden' name='result' value='$code'></form></body></html>";
        return;
    }

}

