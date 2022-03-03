<?php

namespace App\Http\Controllers\head\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Jaego;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ord03Controller extends Controller
{
    public function index(Request $req)
    {

        $mutable = now();
        $sdate = $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sale_places'   => SLib::getSalePlaces(),
            'banks' => SLib::getBanks(),
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
        ];

        return view(Config::get('shop.head.view') . '/order/ord03', $values);
    }

    public function upload($prefix = 'ord03',Request $request){

        $src_file = "";

        try {
            if ($request->file('file')->isValid()) {

                $id = Auth('head')->user()->id;
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();

                $save_path = "/data/tmp";
                $file_name = sprintf("%s_%s.%s", $prefix,$id, $ext);
                $save_file = sprintf("%s/%s", $save_path, $file_name);

                if (!Storage::disk('public')->exists($save_path)) {
                    Storage::disk('public')->makeDirectory($save_path);
                }
                $request->file('file')->storeAs('public'.$save_path,$file_name);

                $src_file = $save_file;
                $code = 200;
                $msg = "";
            } else {
                $code = 500;
                $msg = "file is not invalid";
            }
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg,
            "file" => $src_file
        ]);
    }

    public function save(Request $request)
    {
		$order = (array)json_decode($request->input('order'));
        $sale_place = $request->input('sale_place');
        $bank_code = $request->input('bank_code');
        $ord_type = $request->input('ord_type',14);

        $order["ord_type"] = $ord_type;
        $order["sale_place"] = $sale_place;
        $order["qty"] = str_replace(",","",trim($order["qty"]));
        $order["ord_amt"] = str_replace(",","",trim($order["ord_amt"]));
        $order["dlv_amt"] = str_replace(",","",$order["dlv_amt"]);			// 배송비
        $order["dlv_add_amt"] = str_replace(",","",$order["dlv_add_amt"]);	// 추가배송비

        $order["r_addr1"] = $order["r_addr"];
        $order["r_addr2"] = "";

        if($bank_code != ""){
            list($order["bank_code"],$order["bank_number"]) = explode("_",$bank_code);
        }

        $order["user_id"]	= ($sale_place == "HEAD_OFFICE")? $order["user_id"]:"";
        $order["price"]		= round($order["ord_amt"]/$order["qty"]);
        $order["recv_amt"]	= $order["ord_amt"];
        $order["sales_com_fee"] = round($order["ord_amt"] * $order["fee_rate"] /100);

        if(preg_match("/^(\d+)-(\d+)$/i",$order["goods_no"],$match)){
            $order["goods_no"] = $match[1];
            $order["goods_sub"] = $match[2];
        } else {
            $order["goods_sub"] = 0;
        }

        if(!isset($order["phone"])) $order["phone"] = Lib::getValue($order,"r_phone","");
        if(!isset($order["mobile"])) $order["mobile"] = Lib::getValue($order,"r_mobile","");;
        if(!isset($order["goods_opt"])) $order["goods_opt"] = "none";
        if(!isset($order["goods_opt"])) $order["goods_opt"] = "none";
        if(!isset($order["dlv_pay_type"])) $order["dlv_pay_type"] = "P";
        if(!isset($order["ord_state"])) $order["ord_state"] = "10";
        if(!isset($order["pay_type"])) $order["pay_type"] = "1";
        if(!isset($order["pay_stat"])) $order["pay_stat"] = "1";
        if(!isset($order["fee_rate"])) $order["fee_rate"] = "0";
        if(!isset($order["sales_com_fee"])) $order["sales_com_fee"] = "0";
        if(!isset($order["ord_kind"])) $order["ord_kind"] = "20";
        if(!isset($order["dlv_comment"]) && isset($order["dlv_msg"])) $order["dlv_comment"] = $order["dlv_msg"];

        //print_r($order);
        $result = $this->save_order($order);

        //echo $sale_place;
        //echo "~~~~";
        //echo $order["out_ord_no"];

        return response()->json([
            "code" => $result["code"],
            "msg" => Lib::getValue($result,"msg",""),
            "ord_no" => isset($result["ord_no"])? $result["ord_no"]:""
        ]);

    }

    private function save_order($order){

        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        $ord_no = "";
        $code = 0;
        $msg = "";
        $out_ord_no = $order["out_ord_no"];

        if( $order["out_ord_no"] == "" ){
            $code = "-100";
        } else if( $order["goods_no"] == "" ){
            $code = "-101";
        } else if( $order["goods_opt"] == "" ){
            $code = "-102";
        } else if($order["qty"] == ""){
            $code = "-106";
        } else if($order["ord_amt"] == ""){
            $code = "-107";
        } else if($order["user_nm"] == ""){
            $code = "-108";
        } else if($order["r_nm"] == ""){
            $code = "-110";
        } else if($order["r_zipcode"] == ""){
            $code = "-111";
        } else if($order["r_addr"] == ""){
            $code = "-112";
        }

        if($code === 0){
            $stock = new Jaego();
            if($stock->IsOption($order["goods_no"],0,$order["goods_opt"]) == false){
                $code = "-220";
                return ["code" => $code];
            }

            $sql = /** @lang text */
                "
				select goods_no, opt_id, ord_no, user_nm
				from outbound_order
				where sale_place = :sale_place and out_ord_no = :out_ord_no
			";
            $rows = DB::select($sql,array("sale_place" => $order["sale_place"],"out_ord_no" => $out_ord_no));
            $ord_seq = 0;

            if(count($rows) > 0){
                for($i=0;$i<count($rows);$i++){
                    $out_order_row = (array)$rows[$i];
                    if( trim($out_order_row["goods_no"]) == $order["goods_no"] && trim($out_order_row["opt_id"]) == $order["goods_opt"] ) {
                        return ["code" => "-310"];
                    } else {
                        $ord_no = $out_order_row["ord_no"];
                    }
                }

                $sql = /** @lang text */
                    "
					select user_nm from order_mst
					where ord_no = :ord_no
				";
                $row = (array)DB::selectone($sql,array("ord_no" => $ord_no));
                if( $row  ) {
                    if(trim($row["user_nm"]) != $order["user_nm"]){	// 묶음주문인데 주문자명이 다른 경우 처리
                        return ["code" => "-320"];
                    }
                    $ord_seq++;
                } else {
                    return ["code" => "-330"];
                }
            }

            $sql = /** @lang text */
                "
					select
						a.head_desc, a.goods_nm, a.style_no, a.md_id, a.md_nm, b.com_nm, a.com_id,
						a.baesong_kind, a.baesong_price, b.pay_fee/100 as com_rate, a.price, a.com_type,
						a.is_unlimited
					from goods a left outer join company b on a.com_id  = b.com_id
					where a.goods_no = :goods_no 
				";
            $row = (array)DB::selectone($sql,array("goods_no" => $order["goods_no"]));
            if( $row  ) {
                if(isset($order["goods_nm"]) || $order["goods_nm"] == ""){
                    $order["goods_nm"]	= $row["goods_nm"];
                }
                $order["md_id"]		= $row["md_id"];
                $order["md_nm"]		= $row["md_nm"];
                $order["com_type"] 	= $row["com_type"];
                $order["com_id"]	= $row["com_id"];
                $order["com_nm"] 	= $row["com_nm"];
                $order["baesong_kind"] =  $row["baesong_kind"];
                $is_unlimited = $row["is_unlimited"];

            } else {
                return ["code" => "-210"];
            }

            /**
            재고 확인
             */
            $is_stock = true;
            $good_qty = $stock->GetQty($order["goods_no"],$order["goods_sub"],$order["goods_opt"]);

            if( $is_unlimited == "Y" ){
                if( $good_qty == 0 ){
                    $is_stock = false;
                }
            } else {
                if($order["qty"] > $good_qty){
                    $is_stock = false;
                }
            }

            // 주문 상태
            $order["ord_state"] = ($is_stock == true) ? "10" : "5";
            $order["clm_state"] = ($is_stock == true) ? "0" : "0";	// 클레임 : 주문취소 상태

            try {

                $orderClass = new Order([
                    "id" => $admin_id,
                    "name" => $admin_nm
                ]);
                if($ord_no === ""){
                    $ord_no =$orderClass->GetNextOrdNo();
                }
                $orderClass->SetOrdNo( $ord_no );

                if($ord_seq == 0){
                    $order_mst = [
                        "ord_no"		=> $ord_no,
                        "ord_date"      => DB::raw('now()'),
                        "user_id" 		=> $order["user_id"],
                        "user_nm" 		=> $order["user_nm"],
                        "phone" 		=> Lib::getValue($order,"phone",""),
                        "mobile" 		=> Lib::getValue($order,"mobile",""),
                        "email" 	    => Lib::getValue($order,"email",""),
                        "ord_amt" 		=> $order["ord_amt"],
                        "recv_amt"		=> $order["recv_amt"],
                        "point_amt" 	=> 0,
                        "coupon_amt"	=> 0,
                        "dlv_amt" 		=> $order["dlv_amt"],
                        "r_nm" 			=> $order["r_nm"],
                        "r_zipcode" 	=> $order["r_zipcode"],
                        "r_addr1" 		=> $order["r_addr1"],
                        "r_addr2" 		=> $order["r_addr2"],
                        "r_phone" 		=> $order["r_phone"],
                        "r_mobile" 		=> $order["r_mobile"],
                        "dlv_msg" 		=> $order["dlv_msg"],
                        "ord_state" 	=> $order["ord_state"],
                        "ord_type" 		=> $order["ord_type"],
                        "ord_kind" 		=> $order["ord_kind"],
                        "sale_place" 	=> $order["sale_place"],
                        "out_ord_no" 	=> $order["out_ord_no"],
                        "upd_date"      => DB::raw('now()'),
                        "dlv_end_date"  => DB::raw('now()'),
                    ];
                    DB::table('order_mst')->insert($order_mst);

                    $payment = [
                        "ord_no"		=> $ord_no,
                        "pay_type" 		=> $order["pay_type"],
                        "pay_nm" 		=> $order["user_nm"],
                        "pay_amt" 		=> $order["ord_amt"],
                        "pay_stat" 		=> $order["pay_stat"],
                        "bank_inpnm" 	=> Lib::getValue($order,"bank_inpnm",""),
                        "bank_code" 	=> Lib::getValue($order,"bank_code",""),
                        "bank_number" 	=> Lib::getValue($order,"bank_number",""),
                        "ord_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
                        "upd_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
                    ];
                    DB::table('payment')->insert($payment);

                } else {

                    DB::table('order_mst')
                        ->where('ord_no','=',$ord_no)
                        ->update([
                            'ord_amt' => DB::raw(sprintf("ord_amt + %d",$order["ord_amt"]) ),
                            'recv_amt' => DB::raw(sprintf("recv_amt + %d",$order["ord_amt"]) ),
                            'dlv_amt' => DB::raw(sprintf("dlv_amt + %d",$order["ord_amt"]) ),
                        ]);

                    DB::table('payment')
                        ->where('ord_no','=',$ord_no)
                        ->update([
                            'pay_amt' => $order["ord_amt"]
                        ]);
                }


                $order_opt = [
                    "goods_no"		=> $order["goods_no"],
                    "goods_sub" 	=> $order["goods_sub"],
                    "ord_no" 		=> $ord_no,
                    "ord_seq" 		=> $ord_seq,
                    "head_desc" 	=> Lib::getValue($order,"head_desc",""),
                    "goods_nm" 		=> $order["goods_nm"],
                    "goods_opt" 	=> $order["goods_opt"],
                    "qty"			=> $order["qty"],
                    "price" 		=> $order["price"],
                    "pay_type"		=> $order["pay_type"],
                    "dlv_pay_type" 	=> $order["dlv_pay_type"],
                    "dlv_amt" 		=> $order["dlv_amt"],
                    "point_amt" 	=> 0,
                    "coupon_amt" 	=> 0,
                    "recv_amt" 		=> $order["ord_amt"],
                    "md_id" 		=> $order["md_id"],
                    "md_nm" 		=> $order["md_nm"],

                    "sale_place" 	=> $order["sale_place"],
                    "ord_state" 	=> $order["ord_state"],
                    "clm_state" 	=> $order["clm_state"],
                    "com_id" 		=> $order["com_id"],
                    "ord_kind" 		=> $order["ord_kind"],
                    "ord_type" 		=> $order["ord_type"],
                    "baesong_kind" 	=> $order["baesong_kind"],

                    //"dlv_state_date"=> ($order["ord_state"] == "10" ) ? DB::raw('now()') : DB::raw('NULL'),
                    "dlv_comment" 	=> $order["dlv_comment"],
                    "admin_id" 		=> $admin_id,
                    "sales_com_fee" => $order["sales_com_fee"],
                    "ord_date"      => DB::raw('now()'),
                ];
                DB::table('order_opt')->insert($order_opt);
                $ord_opt_no = DB::getPdo()->lastInsertId();

                // ORDER_OPT_WONGA vs CLAIM //////////////////////////////
                if( $is_stock === true ) {

                    /**
                     * 주문상태 로그
                     */
                    $state_log = array(
                        "ord_no"		=> $ord_no,
                        "ord_opt_no"	=> $ord_opt_no,
                        "ord_state"		=> $order["ord_state"],
                        "comment" 		=> "수기판매일괄",
                        "admin_id" => $admin_id,
                        "admin_nm"=> $admin_nm
                    );
                    $orderClass->AddStateLog($state_log);

                    // 재고 차감
                    $orderClass->CompleteOrderSugi($ord_opt_no, $order["ord_state"]);

                } else {

                    /**
                     * 주문상태 로그
                     */
                    $state_log = array(
                        "ord_no" => $ord_no,
                        "ord_opt_no" => $ord_opt_no,
                        "ord_state" => $order["ord_state"],
                        "comment" => "수기판매일괄(품절)",
                        "admin_id" => $admin_id,
                        "admin_nm"=> $admin_nm
                    );
                    $orderClass->AddStateLog($state_log);

                    // 재고 없는 경우 주문상태 변경
                    $orderClass->OutOfScockAfterPaid();
                }

                // outbound_order 저장 /////////////////////////////////////////////


                $out_order = array(
                    "sale_place"	=> $order["sale_place"],
                    "out_ord_no" 	=> $order["out_ord_no"],

                    "pay_date" 		=> $order["pay_date"],
                    "goods_no" 		=> $order["goods_no"],
                    "goods_nm" 		=> $order["goods_nm"],
                    "opt1" 			=> $order["goods_opt"],
                    "qty" 			=> $order["qty"],
                    "price" 		=> $order["ord_amt"],

                    "r_nm" 			=> $order["r_nm"],
                    "r_zipcode" 	=> $order["r_zipcode"],
                    "r_addr1" 		=> $order["r_addr1"],
                    "r_addr2" 		=> $order["r_addr2"],
                    "r_phone" 		=> $order["r_phone"],
                    "r_mobile" 		=> $order["r_mobile"],
                    "dlv_msg" 		=> $order["dlv_msg"],

                    "user_nm" 		=> $order["user_nm"],
                    "user_phone" 	=> Lib::getValue($order,"phone",""),
                    "user_mobile" 	=> Lib::getValue($order,"email",""),

                    "opt_id" 		=> $order["goods_opt"],
                    "ord_no" 		=> $ord_no,
                    "ord_opt_no" 	=> $ord_opt_no,
                    "sales_com_fee" => $order["sales_com_fee"],
                    "dlv_amt" 		=> $order["dlv_amt"],
                );
                DB::table('outbound_order')->insert($out_order);

                $code = ($is_stock)? 200:110;

            } catch (Exception $e) {
                $code = 500;
                $msg = $e->getMessage();
            }

        } else {
            return ["code" => $code];
        }
        return [
            "code" => $code,
            "msg" => $msg,
            "ord_no" => $ord_no
        ];

    }

    public function Save2($conn,$x2gate){

        $SALE_PLACE = Request("SALE_PLACE");
        $BANK_CODE = Request("BANK_CODE");
        $ORD_TYPE = Request("ORD_TYPE");			// 출고형태 : 5 교환, 4 예약, 3 특별주문, 13 도매주문, 12 서비스

        $order = MagicStripslashes(Request("DATA"));
        $order = str_replace("\n","",$order);
        $HEAD = Request("HEAD","");
        $heads = explode("\t",$HEAD);

        $REMOTE_ADDR = $_SERVER["REMOTE_ADDR"];
        $C_ADMIN_ID = $this->user["id"];
        $C_ADMIN_NAME = $this->user["name"];


        $conn->StartTrans();

        $init_order = array(
            "out_ord_no"	=> "",			// 판매업체 주문번호
            "ord_date"		=> "",			// 주문일
            "style_no"		=> "",			// 스타일넘부
            "goods_no"		=> "",			// 상품번호
            "goods_sub"		=> "",			// 상품번호
            "goods_opt"		=> "none",		// 옵션
            "head_desc"		=> "",			// 홍보글
            "goods_nm"		=> "",			// 상품명
            "qty"			=> 0,			// 수량
            "price"			=> 0,			// 가격
            "ord_amt"		=> 0,			// 주문금액
            "dlv_pay_type"	=> "P",			// 배송비지불시점 ( 선불 : P , 후불 : F )
            "dlv_amt"		=> 0,			// 배송비
            "dlv_add_amt"	=> 0,			// 추가배송비
            "recv_amt"		=> 0,			// 결제금액
            "ord_state"		=> "10",		// 주문상태
            "clm_state"		=> "",			// 클레임상태
            "ord_type"		=> $ORD_TYPE,	// 주문구분 : 5 교환, 4 예약, 3 특별주문, 13 도매주문, 12 서비스
            "ord_kind"		=> "20",		// 출고구분 : 20 입금, 30 보류
            "pay_type"		=> "1",			// 결제방법 - 1:현금,2:카드
            "pay_stat"		=> "1",			// 입금 - 1:입금,0:미입금
            "pay_date"		=> "",			// 입금일
            "bank_inpnm"	=> "",			// 입금자명
            "bank_code"		=> "",			// 은행코드
            "bank_number"	=> "",			// 은행계좌번호
            "user_id"		=> "",			// 회원아이디
            "user_nm"		=> "",			// 주문자
            "email"			=> "",			// 이메일
            "phone"			=> "",			// 주문자연락처
            "mobile"		=> "",			// 주문자핸드폰번호
            "r_nm"			=> "",			// 수령자
            "r_zipcode"		=> "",			// 수령자 우편번호
            "r_addr"		=> "",			// 수령자 주소
            "r_phone"		=> "",			// 수령자 연락처
            "r_mobile"		=> "",			// 수령자 핸드폰번호
            "dlv_msg"		=> "",			// 배송정보
            "dlv_nm"		=> "",			// 택배사
            "dlv_cd"		=> "",			// 송장정보
            "dlv_comment"	=> "",			// 출고메세지
            "baesong_kind"	=> "",			// 배송구분(본사/업체)
            "fee_rate"		=> 0,			// 수수료율
            "sales_com_fee"	=> 0,			// 수수료
            "md_id"			=> "",			// MD(아이디)
            "md_nm"			=> "",			// MD(이름)
            "com_id"		=> "",			// 업체아이디
            "com_type"		=> "",			// 업체구분
            "sale_place"	=> $SALE_PLACE
        );

        if(!empty($order)){

            $cols = explode("\t", $order);
            $data = $init_order;

            for($i=0;$i<count($heads);$i++){
                $head = $heads[$i];
                if(isset($data[$head])){
                    if(isset($cols[$i]) && $cols[$i] != ""){
                        $data[$head] = trim($cols[$i]);
                    }
                    $data[$head] = str_replace("'","''",$data[$head]);
                } else {
                }
            }

            $data["qty"] = str_replace(",","",$data["qty"]);					// 수량
            $data["ord_amt"] = str_replace(",","",$data["ord_amt"]);			// 주문가격
            $data["dlv_amt"] = str_replace(",","",$data["dlv_amt"]);			// 배송비
            $data["dlv_add_amt"] = str_replace(",","",$data["dlv_add_amt"]);	// 추가배송비

            $is_dup = 0;

            // 입력값

            $ord_no			= "";
            $ord_opt_no		= "";
            $p_ord_opt_no	= "";
            $out_ord_no		= $data["out_ord_no"];

            if($data["ord_date"] == ""){
                $data["ord_date"] = date("Ymdhis");
            }

            if($data["pay_date"] == ""){
                $data["pay_date"] = $data["ord_date"];
            }

            $data["r_addr1"] = $data["r_addr"];
            $data["r_addr2"] = "";

            if($BANK_CODE != ""){
                list($data["bank_code"],$data["bank_number"]) = split("_",$BANK_CODE);
            }

            $data["user_id"]	= ($SALE_PLACE == "HEAD_OFFICE")? $data["user_id"]:"";
            $data["price"]		= round($data["ord_amt"]/$data["qty"]);
            $data["recv_amt"]	= $data["ord_amt"];
            $data["sales_com_fee"] = round($data["ord_amt"] * $data["fee_rate"] /100);

            if(preg_match("/^(\d+)-(\d+)$/i",$data["goods_no"],$match)){
                $data["goods_no"] = $match[1];
                $data["goods_sub"] = $match[2];
            } else {
                $data["goods_sub"] = 0;
            }

            if( $out_ord_no == "" ){
                echo "-100";
                return;
            } else if( $data["goods_no"] == "" ){
                echo "-101";
                return;
            } else if( $data["goods_opt"] == "" ){
                echo "-102";
                return;
            } else if($data["qty"] == ""){
                echo "-106";
                return;
            } else if($data["ord_amt"] == ""){
                echo "-107";
                return;
            } else if($data["user_nm"] == ""){
                echo "-108";
                return;
            } else if($data["r_nm"] == ""){
                echo "-110";
                return;
            } else if($data["r_zipcode"] == ""){
                echo "-111";
                return;
            } else if($data["r_addr"] == ""){
                echo "-112";
                return;
            }

            $jaego = new Jaego($conn,$this->user);
            if($jaego->IsOption($data["goods_no"],$data["goods_sub"],$data["goods_opt"]) == false){
                echo "-220";
                return;
            }

            $ord_seq = 0;
            $ret = 200;

            $sql = "
				select goods_no, opt_id, ord_no, user_nm
				from outbound_order
				where sale_place = '$SALE_PLACE' and out_ord_no = '$out_ord_no'
			";
            $rs = &$conn->Execute($sql);

            while(!$rs->EOF){

                $row = $rs->fields;
                $ord_no = $row["ord_no"];		// 기존 존재 주문건이면 주문번호 가져옴

                if( trim($row["goods_no"]) == $data["goods_no"] && trim($row["opt_id"]) == $data["goods_opt"] ) {
                    $is_dup = 1;
                    echo "-310:$ord_no";
                    return;
                }
                $ord_seq++;
                $rs->MoveNext();
            }

            // 묶음주문에 대한 유효성 검사

            if($ord_seq > 0){

                $sql = "
					select user_nm from order_mst
					where ord_no = '$ord_no'
				";
                $rs = &$conn->Execute($sql);
                if( $row = $rs->fields ) {
                    if(trim($row["user_nm"]) != $data["user_nm"]){	// 묶음주문인데 주문자명이 다른 경우 처리
                        echo "-320";
                        return;
                    }
                } else {
                    echo "-330";
                    return;
                }
            }

            if( $is_dup != 1 ) {

                // 주문 객체 생성
                $order = new Order($conn, $this->user);

                if( $ord_no == "" ) {
                    $ord_no = $order->ord_no;
                }

                // 상품 기본정보 가져오기 /////////////////////////////////////////

                $sql = "
					select
						a.head_desc, a.goods_nm, a.style_no, a.md_id, a.md_nm, b.com_nm, a.com_id,
						a.baesong_kind, a.baesong_price, b.pay_fee/100 as com_rate, a.price, a.com_type,
						a.is_unlimited
					from goods a left outer join company b on a.com_id  = b.com_id
					where a.goods_no = ? and a.goods_sub = ?
				";
                //debugSQL($sql2);exit;
                $rs = &$conn->Execute($sql,array(
                    "goods_no"	=> $data["goods_no"],
                    "goods_sub" => $data["goods_sub"],
                ));

                if( $row = $rs->fields ) {

                    $data["goods_nm"]	= Rq($row["goods_nm"]);
                    $data["md_id"]		= $row["md_id"];
                    $data["md_nm"]		= $row["md_nm"];
                    $data["com_type"] 	= $row["com_type"];
                    $data["com_id"]		= $row["com_id"];
                    $data["com_nm"] 	= $row["com_nm"];
                    $data["baesong_kind"] =  $row["baesong_kind"];
                    $is_unlimited = $row["is_unlimited"];

                } else {
                    echo -210;
                    return;
                }

                /**
                재고 확인
                 */
                $is_jaego = true;
                $good_qty = $jaego->GetQty($data["goods_no"],$data["goods_sub"],$data["goods_opt"]);

                if( $is_unlimited == "Y" ){
                    if( $good_qty == 0 ){
                        $is_jaego = false;
                        $ret = 110;
                    }
                } else {
                    if($data["qty"] > $good_qty){
                        $is_jaego = false;
                        $ret = 110;
                    }
                }

                // 주문 상태
                $data["ord_state"] = ($is_jaego == true) ? "10" : "5";
                $data["clm_state"] = ($is_jaego == true) ? "0" : "0";	// 클레임 : 주문취소 상태

                if( $ord_seq == 0 ) {
                    $sql = "
						insert into order_mst (
							ord_no,ord_date,user_id,user_nm,phone,mobile,email,ord_amt,recv_amt,point_amt,coupon_amt,dlv_amt,
							r_nm,r_zipcode,r_addr1,r_addr2,r_phone,r_mobile,dlv_msg,url,c_link,
							ord_state,upd_date,dlv_end_date,ord_type,ord_kind, sale_place, out_ord_no
						) values (
							?,now(),?,?,?,?,?,?,?,?,?,?,
							?,?,?,?,?,?,?,null,null,
							?,now(),null,?,?,?,?
						)
					";

                    $conn->Execute($sql,array(
                        "ord_no"		=> $ord_no,
                        "user_id" 		=> $data["user_id"],
                        "user_nm" 		=> $data["user_nm"],
                        "phone" 		=> $data["phone"],
                        "mobile" 		=> $data["mobile"],
                        "email" 		=> $data["email"],
                        "ord_amt" 		=> $data["ord_amt"],
                        "recv_amt"		=> $data["recv_amt"],
                        "point_amt" 	=> 0,
                        "coupon_amt"	=> 0,
                        "dlv_amt" 		=> $data["dlv_amt"],
                        "r_nm" 			=> $data["r_nm"],
                        "r_zipcode" 	=> $data["r_zipcode"],
                        "r_addr1" 		=> $data["r_addr1"],
                        "r_addr2" 		=> $data["r_addr2"],
                        "r_phone" 		=> $data["r_phone"],
                        "r_mobile" 		=> $data["r_mobile"],
                        "dlv_msg" 		=> $data["dlv_msg"],
                        "ord_state" 	=> $data["ord_state"],
                        "ord_type" 		=> $data["ord_type"],
                        "ord_kind" 		=> $data["ord_kind"],
                        "sale_place" 	=> $data["sale_place"],
                        "out_ord_no" 	=> $data["out_ord_no"]
                    ));

                    $sql = "
						insert into payment (
							ord_no,pay_type,pay_nm,pay_amt,pay_stat,bank_inpnm,bank_code,bank_number,
							card_code,card_isscode,card_quota,card_appr_no,card_appr_dm,card_tid,card_msg,
							ord_dm,upd_dm,pay_ypoint,pay_point,pay_baesong,card_name,nointf
						) values (
							?,?,?,?,?,?,?,?,
							null,null,null,null,null,null,null,
							date_format(?,'%Y%m%d%H%i%s'),date_format(now(),'%Y%m%d%H%i%s'),0,0,0,null,null
						)
					";

                    $conn->Execute($sql,array(
                        "ord_no"		=> $ord_no,
                        "pay_type" 		=> $data["pay_type"],
                        "pay_nm" 		=> $data["user_nm"],
                        "pay_amt" 		=> $data["ord_amt"],
                        "pay_stat" 		=> $data["pay_stat"],
                        "bank_inpnm" 	=> $data["bank_inpnm"],
                        "bank_code" 	=> $data["bank_code"],
                        "bank_number" 	=> $data["bank_number"],
                        "ord_dm" 		=> $data["ord_date"],
                    ));

                } else {
                    if($data["dlv_amt"] > 0){

                        $sql = "
							update order_mst set
								ord_amt = ord_amt + ?,
								recv_amt = recv_amt + ?,
								dlv_amt = dlv_amt + ?
							where ord_no = ?
						";
                        $conn->Execute($sql,array(
                            "ord_amt"	=> $data["ord_amt"],
                            "recv_amt"	=> $data["recv_amt"],
                            "dlv_amt"	=> $data["dlv_amt"],
                            "ord_no"	=> $ord_no
                        ));

                    }else{

                        $sql = "
							update order_mst set
								ord_amt = ord_amt + ?,
								recv_amt = recv_amt + ?
							where ord_no = ?
						";
                        $conn->Execute($sql,array(
                            "ord_amt"	=> $data["ord_amt"],
                            "recv_amt"	=> $data["recv_amt"],
                            "ord_no"	=> $ord_no
                        ));
                    }

                    $sql = "
						update payment set pay_amt = pay_amt + ? where ord_no = '$ord_no'
					";
                    $conn->Execute($sql,array(
                        "pay_amt" => $data["ord_amt"]
                    ));

                }

                $sql = "
					insert into order_opt (
						goods_no, goods_sub, ord_no, ord_seq, head_desc, goods_nm, goods_opt, qty, price, pay_type, dlv_pay_type, dlv_amt,
						point_amt, coupon_amt, recv_amt, bundle_ord_opt_no, p_ord_opt_no, dlv_no, dlv_cd, md_id, md_nm,
						sale_place, ord_state, clm_state, com_id, add_point, ord_kind, ord_type, baesong_kind,
						dlv_start_date, dlv_proc_date, dlv_end_date, dlv_cancel_date, ord_date, dlv_comment, admin_id, sales_com_fee
					) values (
						?,?,?,?,?,?,?,?,?,?,?,?,
						0,0,?,null,null,null,null,?,?,
						?,?,?,?,0,?,?,?,
						?,null,null,null,now(),?,?,?
					)
				";
                $conn->Execute($sql,array(
                    "goods_no"		=> $data["goods_no"],
                    "goods_sub" 	=> $data["goods_sub"],
                    "ord_no" 		=> $ord_no,
                    "ord_seq" 		=> $ord_seq,
                    "head_desc" 	=> $data["head_desc"],
                    "goods_nm" 		=> $data["goods_nm"],
                    "goods_opt" 	=> $data["goods_opt"],
                    "qty"			=> $data["qty"],
                    "price" 		=> $data["price"],
                    "pay_type"		=> $data["pay_type"],
                    "dlv_pay_type" 	=> $data["dlv_pay_type"],
                    "dlv_amt" 		=> $data["dlv_amt"],

                    "recv_amt" 		=> $data["ord_amt"],
                    "md_id" 		=> $data["md_id"],
                    "md_nm" 		=> $data["md_nm"],

                    "sale_place" 	=> $data["sale_place"],
                    "ord_state" 	=> $data["ord_state"],
                    "clm_state" 	=> $data["clm_state"],
                    "com_id" 		=> $data["com_id"],
                    "ord_kind" 		=> $data["ord_kind"],
                    "ord_type" 		=> $data["ord_type"],
                    "baesong_kind" 	=> $data["baesong_kind"],

                    "dlv_state_date"=> ($data["ord_state"] == "10" ) ? "now()" : "null",
                    "dlv_comment" 	=> $data["dlv_comment"],
                    "admin_id" 		=> $C_ADMIN_ID,
                    "sales_com_fee" => $data["sales_com_fee"],
                ));

                $ord_opt_no = $conn->Insert_ID();

                // ORDER_OPT_WONGA vs CLAIM //////////////////////////////
                if( $is_jaego ) {

                    $order->SetOrdNo( $ord_no );

                    /**
                     * 주문상태 로그
                     */
                    $state_log = array(
                        "ord_no"		=> $ord_no,
                        "ord_opt_no"	=> $ord_opt_no,
                        "ord_state"		=> $data["ord_state"],
                        "comment" 		=> "수기판매일괄",
                        "admin_id" 		=> $this->user["id"],
                        "admin_nm"		=> $this->user["name"]
                    );
                    $order->AddStateLog($state_log);

                    // 재고 차감
                    $order->CompleteOrderSugi($ord_opt_no, $data["ord_state"]);

                } else {

                    $order->SetOrdNo( $ord_no );

                    /**
                     * 주문상태 로그
                     */
                    $state_log = array(
                        "ord_no" => $ord_no,
                        "ord_opt_no" => $ord_opt_no,
                        "ord_state" => $data["ord_state"],
                        "comment" => "수기판매일괄(품절)",
                        "admin_id" => $this->user["id"],
                        "admin_nm"=> $this->user["name"]
                    );
                    $order->AddStateLog($state_log);

                    // 재고 없는 경우 주문상태 변경
                    $order->OutOfScockAfterPaid();
                }

                // outbound_order 저장 /////////////////////////////////////////////
                $sql = "
					insert into outbound_order (
						sale_place,out_ord_no,
						dlv_no,songjang_no,cp_no,cp_nm,org_cp_nm,shop_no,
						pay_date,goods_no,goods_nm,opt1,opt2,opt3,qty,price,
						pay_type,r_nm,r_zipcode,r_addr1,r_addr2,r_phone,r_mobile,dlv_msg,
						gift_msg,user_id,user_nm,user_phone,user_mobile,
						shop_nm,c_zipcode,c_addr1,c_addr2,md_phone1,md_phone2,
						opt_id, ord_no, ord_opt_no, sales_com_fee, dlv_amt
					) values (
						?, ?,
						'','','','','','',
						?,?,?,?,'','',?,?,
						'',?,?,?,?,?,?,?,
						'','',?,?,?,
						'','','','','','',
						?,?,?,?,?
					)
				";
                $conn->Execute($sql,array(
                    "sale_place"	=> $data["sale_place"],
                    "out_ord_no" 	=> $data["out_ord_no"],

                    "pay_date" 		=> $data["pay_date"],
                    "goods_no" 		=> $data["goods_no"],
                    "goods_nm" 		=> $data["goods_nm"],
                    "opt1" 			=> $data["goods_opt"],
                    "qty" 			=> $data["qty"],
                    "price" 		=> $data["ord_amt"],

                    "r_nm" 			=> $data["r_nm"],
                    "r_zipcode" 	=> $data["r_zipcode"],
                    "r_addr1" 		=> $data["r_addr1"],
                    "r_addr2" 		=> $data["r_addr2"],
                    "r_phone" 		=> $data["r_phone"],
                    "r_mobile" 		=> $data["r_mobile"],
                    "dlv_msg" 		=> $data["dlv_msg"],

                    "user_nm" 		=> $data["user_nm"],
                    "user_phone" 	=> $data["phone"],
                    "user_mobile" 	=> $data["mobile"],

                    "opt_id" 		=> $data["goods_opt"],
                    "ord_no" 		=> $ord_no,
                    "ord_opt_no" 	=> $ord_opt_no,
                    "sales_com_fee" => $data["sales_com_fee"],
                    "dlv_amt" 		=> $data["dlv_amt"],
                ));
            }

        } // end if (!empty($DATA))

        if($conn->CompleteTrans()){
            printf("%s:%s",$ret,$ord_no);
        } else{			// 실패
            echo "-500";
        }
    }

    public function import_show(Request $req)
    {
        $sale_place = $req->input('sale_place');

        $fee = 0;

        $conf = new Conf();
        $cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
        $cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");

		$values = [
			'p_sale_place'    => $sale_place,
			'sale_places'   => SLib::getSalePlaces(),
			"dlv_amt_limit" => $cfg_free_dlv_fee_limit,
			"dlv_amt" => $cfg_dlv_fee,
			"fee" => $fee
		];

		return view(Config::get('shop.head.view') . '/order/ord03_import', $values);
    }

    public function format_search(Request $req)
    {
        $sale_place = $req->input("sale_place");

        $sql = /** @lang text */
            "
			select idx, mat_idx, mat_pattern, mat_param, mat_value
			from company_match where com_id = :sale_place
			order by idx        
        ";
        $rows = DB::select($sql,array("sale_place" => $sale_place));

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows),
            ),
            "body" => $rows
        ]);
    }

    public function format_save(Request $req)
    {
        $sale_place = $req->input("sale_place");
        $data = $req->input("data");

        for($i=0;$i<count($data);$i++){
            $data[$i]["com_id"] = $sale_place;
            unset($data[$i]["name"]);
        }
        try {
            DB::transaction(function () use (&$result, $sale_place,$data) {

                DB::table('company_match')
                    ->where('com_id','=',$sale_place)
                    ->delete();

                DB::table('company_match')->insert($data);
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();

        }

        return response()->json([
            'code' => $code,
            'msg' => $msg
        ]);

    }

    public function format_show(Request $req)
    {
        $values = [
            'sale_places'   => SLib::getSalePlaces(),
        ];
        return view(Config::get('shop.head.view') . '/order/ord03_format', $values);
    }

    public function get_fee(Request $req){
        $sale_place = $req->input('sale_place');
        $fee        = 0;

		$sql    = "
			select pay_fee from company
			where com_type = '4' and com_id = :sale_place
		";
		$row	= DB::selectOne($sql,['sale_place' => $sale_place]);

        if( !empty($row) ){
            $fee    = $row->pay_fee;
        }

        return response()->json($fee, 200);
    }

}
