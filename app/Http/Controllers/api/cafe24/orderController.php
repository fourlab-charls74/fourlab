<?php

namespace App\Http\Controllers\api\cafe24;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use App\Models\Auth;
use App\Models\Gift;
use App\Models\Product;
use App\Models\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Conf;
use PDO;

class orderController extends Controller
{
    private $user = null;

    public function __construct(Request $request)
    {
        $auth = new Auth($request->bearerToken());
        if(!$auth->isAuth()){
            echo json_encode(array(
                "error" => [
                    "code" => 401,
                    "message" => "Authorization is required.",
                    "more_info" => ""
                ]
            ));
            exit;
        } else {
            $this->user = $auth->getUser();
        }
    }

    function getOrderList(Request $req){

        try {
            $com_id = $this->user["id"];

            $cafe_paymethod = [
                "1" => "cash",
                "2" => "card",
                "4" => "point",
                "16" => "tcash",
                "32" => "cell",
            ];

            $cafe_order_state = [
                "10" => "2",
                "20" => "4",
                "30" => "5",
                "40" => "11",
                "50" => "11",
                "60" => "7",
                "61" => "10",
            ];
            $cafe_order_state2 = array_flip($cafe_order_state);

            $goods_img_url = '';
            $cfg_img_size_real = SLib::getCodesValue("G_IMG_SIZE","real");
            $cfg_img_size_list = SLib::getCodesValue("G_IMG_SIZE","list");

            $sdate          = $req->input("start_date", date("Ymd"));
            $edate          = $req->input("end_date", now()->sub(3, 'month')->format('Ymd'));
            $ord_state      = $req->input("item_status", "");

            $where = "";
            $where .= " and a.ord_date >= cast('$sdate' as date) ";
            $where .= " and a.ord_date < DATE_ADD('$edate', INTERVAL 1 DAY) ";
            if($ord_state != "" && isset($cafe_order_state2[$ord_state])){
                $where .= " and a.ord_state = '" . $cafe_order_state2[$ord_state] . "' ";
            }

            $query = /** @lang text */
                "
            select
                '' as chkbox, a.ord_no, a.ord_opt_no, ord_state, clm_state,
                a.pay_type,pay_stat.code_val as pay_stat,
                ifnull(gt.code_val,'N/A') as goods_type_nm, a.style_no, '' as img_view, a.goods_nm,
                a.goods_opt as goods_opt,a.qty, a.user_nm,a.email,a.phone,a.mobile,
                a.r_nm,a.r_phone,a.r_mobile,a.r_zipcode,a.r_addr1,a.r_addr2,
                a.recv_amt,a.dc_amt,a.coupon_amt,a.price,
                a.sale_amt, a.gift, a.dlv_amt, a.pay_fee, fintech,
                a.cash_apply_yn,
                a.cash_yn,
                ord_type.code_val as ord_type,
                ord_kind.code_val as ord_kind,
                a.sale_place, a.out_ord_no, a.com_nm,
                baesong_kind.code_val as baesong_kind,
                dlv_type.code_val as dlv_type,
                dlv_cd, a.dlv_no,a.dlv_msg,
                a.state, a.memo,
                a.coupon_nm,
                a.mobile_yn, a.app_yn, a.browser,
                a.ord_date, a.pay_date, a.dlv_end_date,
                a.last_up_date, a.goods_no, a.goods_sub,
                a.sms_name, a.sms_mobile
            from (
                select
                    b.ord_no, a.ord_opt_no, a.ord_state, d.pay_stat, c.goods_type, c.style_no, a.goods_nm,
                    a.goods_opt, a.qty, concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm,b.email,b.phone,b.mobile,
                    b.r_nm,b.r_phone,b.r_mobile,b.r_zipcode,b.r_addr1,b.r_addr2,
                    b.recv_amt,b.dlv_amt,b.dc_amt,b.coupon_amt,a.price, (a.coupon_amt+a.dc_amt) as sale_amt,
                    (
                        select group_concat(gf.name)
                        from order_gift og
                            inner join gift gf on og.gift_no = gf.no
                        where og.ord_no = a.ord_no and og.ord_opt_no = a.ord_opt_no
                    ) as gift,
                    0 as pay_fee, d.pay_type,'' as fintech,
                    a.ord_type, a.ord_kind, f.coupon_nm, a.dlv_cd, a.dlv_no,b.dlv_msg,
                    a.clm_state, e.com_nm as sale_place, b.out_ord_no, i.com_nm,
                    c.baesong_kind as dlv_baesong_kind, b.ord_date, d.pay_date,
                    a.dlv_end_date, g.last_up_date, c.goods_no, c.goods_sub, c.img, c.com_type,
                    h.state, h.memo, b.user_nm as sms_name, b.mobile as sms_mobile,
                    b.mobile_yn, '' as app_yn, ifnull(ot.browser, '') as browser,
                    if(d.cash_apply_yn = 'Y', '신청', '') as cash_apply_yn,
                    if(d.cash_yn = 'Y', '발행', '') as cash_yn,
                    b.dlv_type
                from order_opt a
                    inner join order_mst b on a.ord_no = b.ord_no
                    inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                    left outer join payment d on b.ord_no = d.ord_no
                    left outer join coupon f on ( a.coupon_no = f.coupon_no )
                    left outer join company e on a.sale_place = e.com_id and e.com_type = '4'
                    left outer join company i on a.com_id = i.com_id
                    left outer join claim g on g.ord_opt_no = a.ord_opt_no
                    left outer join order_opt_memo h on a.ord_opt_no = h.ord_opt_no
                    left outer join order_track ot on a.ord_no = ot.ord_no
                where 1=1
                and a.com_id = :com_id and b.ord_state >= 10
                $where
                order by a.ord_opt_no
            ) a
            left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
            left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
            left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
            left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
            left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
            left outer join code com_type on (a.com_type = com_type.code_id and com_type.code_kind_cd = 'G_COM_TYPE')
            left outer join code baesong_kind on (a.dlv_baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
            left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
            left outer join code dlv_cd on (a.dlv_cd = dlv_cd.code_id and dlv_cd.code_kind_cd = 'DELIVERY')
            left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
            left outer join code dlv_type on (a.dlv_type = dlv_type.code_id and dlv_type.code_kind_cd = 'G_DLV_TYPE')
        ";
            //echo "<pre>$query</pre>";
            //dd($query);
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($query);
            $stmt->execute(["com_id" => $com_id]);
            $orders = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $orders[$row["ord_no"]][] = $row;
            }
            $cafe_rows = array();
            foreach($orders as $ord_no => $rows){

                $pay_type =$rows[0]["pay_type"];
                if(($pay_type & 1) === 1){
                    $pay_type = 1;
                } else if(($pay_type & 2) === 2){
                    $pay_type = 2;
                } else if(($pay_type & 4) === 4){
                    $pay_type = 4;
                } else if(($pay_type & 16) === 16){
                    $pay_type = 16;
                } else if(($pay_type & 32) === 32){
                    $pay_type = 32;
                } else {
                    $pay_type = 1;
                }


                $cafe_row = [
                    "market_order_no" => $ord_no,
                    "payment_amount" => $rows[0]["recv_amt"],
                    "order_date" =>  $rows[0]["ord_date"],
                    "payment_date" => $rows[0]["pay_date"],
                    "place_datetime" => $rows[0]["dlv_end_date"],
                    "buyer_name" => $rows[0]["user_nm"],
                    "buyer_email" =>  $rows[0]["email"],
                    "buyer_phone" => $rows[0]["phone"],
                    "buyer_cellphone" => $rows[0]["mobile"],
                    "buyer_zipcode" => $rows[0]["r_zipcode"],
                    "buyer_address1" => $rows[0]["r_addr1"],
                    "buyer_address2" => $rows[0]["r_addr2"],
                    "shipping_message" => $rows[0]["dlv_msg"],
                    "receiver_name" => $rows[0]["r_nm"],
                    "receiver_phone" => $rows[0]["r_phone"],
                    "receiver_cellphone" => $rows[0]["r_mobile"],
                    "receiver_zipcode" => $rows[0]["r_zipcode"],
                    "receiver_address1" => $rows[0]["r_addr1"],
                    "receiver_address2" => $rows[0]["r_addr2"],
                    "shipping_fee" => $rows[0]["dlv_amt"],
                    "payment_method" => $cafe_paymethod[$pay_type],
                    "additional_discount_price" => $rows[0]["dc_amt"],
                    "shipping_type" => "A",
                    "country_code" => "KR",
                    "clearance_information" => "",
                    "items" => []
                ];
                for($i=0;$i < count($rows);$i++){

                    $sql	= /** @lang text */
                        "
                        select opt_name
                        from goods_summary
                        where goods_no = :goods_no and goods_opt = :goods_opt
                    ";
                    $optRow	= (array)DB::selectone($sql,[
                        'goods_no' => $rows[$i]["goods_no"],
                        'goods_opt' => $rows[$i]["goods_opt"],
                    ]);
                    if(isset($optRow["opt_name"])){
                        $opt_name = $optRow["opt_name"];
                    } else {
                        $sql	= /** @lang text */
                            "
                            select group_concat(name separator '^') as opt_name
                            from goods_option
                            where goods_no = :goods_no
                        ";
                        $optRow	= (array)DB::selectone($sql,[
                            'goods_no' => $rows[$i]["goods_no"]
                        ]);
                        $opt_name = $optRow["opt_name"];
                    }

                    if($rows[$i]["clm_state"] == 60) {
                        $rows[$i]["ord_state"] = 60;
                    } else if($rows[$i]["clm_state"] == 61){
                        $rows[$i]["ord_state"] = 61;
                    }

                    if(strtoupper($rows[$i]["goods_opt"]) === "NONE"){
                        $options = "";
                    } else {
                        $opt_names = explode("^",$opt_name);
                        $goods_opts = explode("^",$rows[$i]["goods_opt"]);
                        $options =  [$opt_names,$goods_opts];
                    }

                    $cafe_row["items"][] =  [
                        "items_status" => $cafe_order_state[$rows[$i]["ord_state"]],
                        "market_item_no" => $rows[$i]["ord_opt_no"],
                        "quantity" => $rows[$i]["qty"],
                        "market_product_code" => $rows[$i]["goods_no"],
                        "product_name" => $rows[$i]["goods_nm"],
                        "options" => $options,
                        "product_price" => $rows[$i]["price"],
                        "discount_price" => 0,
                        "tracking_no" => $rows[$i]["dlv_no"],
                        "shipping_company_code" => $rows[$i]["dlv_cd"],
                    ];
                }
                $cafe_rows[] = $cafe_row;
            }

            return response()->json([
                "data" => $cafe_rows
            ]);

        } catch(\Exception $e){
            $errmsg = sprintf("%s %s line - %s",$e->getFile(),$e->getLine(),$e->getMessage());
            return response()->json([
                "error" => [
                    "code" => 500,
                    "message" => "Interal Error",
                    "more_info" => [$errmsg],
                ]
            ]);
        }
    }

    function orderSetComfirmreceiving(Request $req){

        $ord_state   = $req->input("ord_state", "20");
        $ord_no      = $req->input("market_order_id", "");
        $ord_opt_nos   = $req->input("item_no", "");

        $com_id = "";
        $com_nm = "";

        $where = "";
        if(count($ord_opt_nos) > 0)	$where .= " and ord_opt_no in ( " . Lib::quote(join(",",$ord_opt_nos)) . " ) ";

        $query = /** @lang text */
            "
            select
              ord_opt_no
            from order_opt
            where ord_no = :ord_no $where
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            "ord_no" => $ord_no
        ]);
        $ord_opt_nos = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $ord_opt_nos[] = [$ord_no,$row["ord_opt_no"]];
        }

        if(count($ord_opt_nos) ===0){
            return response()->json([
                "error" => [
                    "code" => 501,
                    "message" => "No orders were found."
                ]
            ]);
        }

        $dlv_series_no = date("Ymd");

        $sql = /** @lang text */
            "
        select
          dlv_series_no
        from order_dlv_series
        where dlv_day >= date_format(date_sub(now(),interval 1 day),'%Y%m%d')
            and dlv_series_nm = '$dlv_series_no'
        order by dlv_series_no desc limit 0,1
      ";

        $row = DB::selectOne($sql);

        if ($row) {
            $dlv_series_no = $row->dlv_series_no;
        } else {
            $dlv_series_no = DB::table('order_dlv_series')->insertGetId([
                'dlv_series_nm' => $dlv_series_no,
                'dlv_day' => date('Ymd'),
                'com_id' => $com_id,
                'regi_date' => now()
            ]);
        }

        $user = [
            'id'	=> $com_id,
            'name'	=> $com_nm
        ];

        //수정시작 ceduce 21-07-20
        $order = new Order($user);
        $is_soldout = false;

        //for( $i = 0; $i < count($ord_opt_nos); $i++ )
        foreach ($ord_opt_nos as $datas) {
            if (!is_array($datas)) continue;

            list($ord_no, $ord_opt_no) = $datas;

            $order->SetOrdOptNo($ord_opt_no, $ord_no);

            if ($order->CheckStockQty(0)) {
                $state_log = array("ord_no" => $ord_no, "ord_state" => $ord_state, "comment" => "배송 출고요청", "admin_id" => $com_id, "admin_nm" => $com_nm);
                $order->AddStateLog($state_log);
                $order->DlvProc($dlv_series_no, $ord_state);
            } else {
                $is_soldout = true;
            }
        }

        if ($is_soldout == true) {
            return response()->json([
                "error" => [
                    "code" => 502,
                    "message" => "It's out of stock."
                ]
            ]);
        } else {
            $cafe_rows = [
                "market_order_id" => $ord_no
            ];
            return response()->json([
                "data" => $cafe_rows
            ]);
        }
    }

    function orderSetShippinggeneral(Request $req){

        $ord_state   = $req->input("ord_state", "30");
        $ord_no      = $req->input("market_order_id", "");
        $ord_opt_nos   = $req->input("item_no", "");
        $dlv_no   = $req->input("tracking_no", "");
        $dlv_cd   = $req->input("delivery_code", "");
        $send_sms_yn	=  Request("send_sms_yn", 'N');

        $com_id = "";
        $com_nm = "";

        $where = "";
        if(count($ord_opt_nos) > 0)	$where .= " and ord_opt_no in ( " . Lib::quote(join(",",$ord_opt_nos)) . " ) ";

        $query = /** @lang text */
            "
            select
              ord_opt_no
            from order_opt
            where ord_no = :ord_no $where
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            "ord_no" => $ord_no
        ]);
        $order_nos = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $order_nos[] = [$ord_no,$row["ord_opt_no"],$dlv_no];
        }

        if(count($order_nos) ===0){
            return response()->json([
                "error" => [
                    "code" => 501,
                    "message" => "No orders were found."
                ]
            ]);
        }

        $user = [
            'id'	=> $com_id,
            'name'	=> $com_nm
        ];
        // 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_name		= $conf->getConfigValue("shop","name");
        $cfg_kakao_yn		= $conf->getConfigValue("kakao","kakao_yn");
        $cfg_sms			= $conf->getConfig("sms");
        $cfg_sms_yn			= $conf->getValue($cfg_sms,"sms_yn");
        $cfg_delivery_yn	= $conf->getValue($cfg_sms,"delivery_yn");
        $cfg_delivery_msg	= $conf->getValue($cfg_sms,"delivery_msg");
        $shop_phone =       $conf->getConfigValue("shop","phone");

        try {
            // Start transaction
            DB::beginTransaction();
            foreach($order_nos as $order_data)
            {
                list($ord_no, $ord_opt_no, $dlv_no)	= $order_data;
                $dlv_nm = SLib::getCodesValue('DELIVERY',$dlv_cd);
                if($dlv_nm === "") $dlv_nm = $dlv_cd;

                $order	= new Order($user);
                $order->SetOrdOptNo($ord_opt_no, $ord_no);

                // 중복 방지 상태 점검
                $check_state	= $order->CheckState("30");

                if( !$check_state ) {
                    DB::rollBack();
                    return response()->json([
                        "error" => [
                            "code" => 501,
                            "message" => "Order has already been shipped. "
                        ]
                    ]);
                }

                /*******************************************************
                 * 주문상태 로그
                 *******************************************************/
                $state_log	= [
                    "ord_no"		=> $ord_no,
                    "ord_opt_no"	=> $ord_opt_no,
                    "ord_state"		=> "30",
                    "comment"		=> "배송 출고처리",
                    "admin_id"		=> $user['id'],
                    "admin_nm"		=> $user['name']
                ];

                $order->AddStateLog($state_log);

                $order->DlvEnd($dlv_cd, $dlv_no);
                $order->DlvLog($ord_state = 30);

                ################################################################
                // 보유재고 차감 로직 추가

                $sql	= /** @lang text */
                    "
					select qty, goods_no, goods_sub, goods_opt
					from order_opt
					where ord_opt_no = '$ord_opt_no'
				";
                $opt	= DB::selectOne($sql);
                $_qty	= $opt->qty;

                $_goods_no	= $opt->goods_no;
                $_goods_sub	= $opt->goods_sub;
                $_goods_opt	= $opt->goods_opt;

                $prd = new Product($user);

                // 재고 차감 처리
                $stocks = $ret = $prd->Minus( array(
                    "type"			=> $type=2,
                    "etc" 			=> $etc="",
                    "qty" 			=> $_qty,
                    "goods_no"		=> $_goods_no,
                    "goods_sub"		=> $_goods_sub,
                    "goods_opt"		=> $_goods_opt,
                    "ord_no"		=> $ord_no,
                    "ord_opt_no"	=> $ord_opt_no
                ));

                if( count($stocks) > 0 )
                {
                    // 추가옵션에 대한 재고 차감
                    $sql	= /** @lang text */
                        "
						select addopt_idx, addopt_qty
						from order_opt_addopt
						where ord_opt_no = '$ord_opt_no'
					";
                    $rows = DB::select($sql);

                    foreach($rows as $row)
                    {
                        $_addopt_idx	= $row->addopt_idx;
                        $_addopt_qty	= $row->addopt_qty;

                        $sql2	= /** @lang text */
                            "
						update options set
							wqty = wqty - $_addopt_qty
						where no = '$_addopt_idx'
						";
                        DB::update($sql2);
                    }
                    ################################################################

                    // 에스크로 결제 여부 검사
                    $is_escrow = $order->IsEscrowOrder();

                    if( $is_escrow )
                    {
                        // 거래번호 얻기
                        $sql	= "select tno from payment where ord_no = '$ord_no' ";
                        $row	= DB::selectOne($sql);
                        $tno	= $row->tno;

                        // Parameters
                        $ip		= $_SERVER["REMOTE_ADDR"];
                        $memo	= "배송 시작 요청";
                        $a_param	= array( "deli_numb" => $dlv_no, "deli_corp" => $dlv_nm );

                        // 배송요청 시작
                        $pg	= new pay();
                        list( $res_cd, $res_msg ) = $pg->mod_escrow("STE1", $tno, $ord_no, $ip, $memo, $a_param);

                        // 클레임 메모 등록
                        $param = array(
                            "ord_state"	=> 30,
                            "clm_state"	=> 30,
                            "cs_form"	=> 10,
                            "memo"		=> $msg = "[에스크로] 배송시작[ $dlv_nm ($dlv_no) ] - $res_msg [$res_cd]",
                        );
                        $claim	= new Claim($user);
                        $claim->SetOrdOptNo( $ord_opt_no );
                        $claim->SetClmNo("");
                        $memo_no = $claim->InsertMessage( $param );
                    }

                    /*******************************************************
                     * 사은품 지급 :
                     *******************************************************/

                    //Gift Class
                    //$gift = new Gift($user);
                    $gift = new Gift();

                    $sql = "
						select no
						from order_gift
						where ord_no = '$ord_no' and ord_opt_no = '$ord_opt_no'
					";
                    $gifts = DB::select($sql);

                    foreach( $gifts as $g_row )
                    {
                        $order_gift_no	= $g_row->no;
                        if( $order_gift_no != "" )
                        {
                            $gift->GiveGift($order_gift_no);
                        }
                    }

                    $msg_yn  = "N";

                    if( $send_sms_yn != "N" ){
                        if( $cfg_sms_yn == "Y" && $cfg_delivery_yn == "Y" ){

                            $sql = /** @lang text */
                                "
								select
									b.user_nm, b.mobile, a.goods_nm,
									( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) as msg_cnt
								from order_opt a
									 inner join order_mst b on a.ord_no = b.ord_no
								where ord_opt_no = '$ord_opt_no'
								      and ( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) = 0
							";
                            $opt = DB::selectone($sql);
                            if ( !empty($opt->user_nm) )
                            {
                                $user_nm	= $opt->user_nm;
                                $mobile		= $opt->mobile;
                                $goods_nm	= mb_substr($opt->goods_nm, 0, 10);

                                $sms = new SMS( $user );
                                $sms_msg = sprintf("[%s]%s..발송완료 %s(%s)",$cfg_shop_name, $goods_nm, $dlv_nm, $dlv_no);

                                if($cfg_kakao_yn == "Y"){
                                    $template_code = "OrderCode6";
                                    $msgarr = array(
                                        "SHOP_NAME" => $cfg_shop_name,
                                        "GOODS_NAME" => $goods_nm,
                                        "DELIVERY_NAME" => $dlv_nm,
                                        "DELIVERY_NO" => $dlv_no,
                                        "USER_NAME"	=> $user_nm,
                                        "ORDER_NO"	=> $ord_no,
                                        "SHOP_URL"	=> 'http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo='.$dlv_no
                                    );
                                    $btnarr = array(
                                        "BUTTON_TYPE" => '1',
                                        "BUTTON_INFO" => '배송 조회하기^DS^http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo='.$dlv_no
                                    );
                                    $sms->SendKakao( $template_code, $mobile, $user_nm, $sms_msg, $msgarr, '', $btnarr);
                                } else {
                                    if($mobile != ""){
                                        $sms->Send( $sms_msg, $mobile, $user_nm,$shop_phone);
                                        $msg_yn  = "Y";
                                    }
                                }
                            }
                        }
                    }

                    DB::table("delivery_import")
                        ->where("com_id",$com_id)
                        ->where("admin_id",$user['id'])
                        ->where("ord_opt_no",'$ord_opt_no')
                        ->update([
                            'dlv_yn' =>'Y',
                            'msg_yn' => $msg_yn,
                            'rt' => DB::raw("now()")
                        ]);
                }
                else
                {
                    throw new Exception("선택하신 주문 중 이미 출고된 주문건이 있습니다. 검색 후 다시 처리하여 주십시오.");
                }
            }

            // Finish transaction
            DB::commit();

            $code = "200";
            $msg = "";

        } catch(Exception $e) {
            DB::rollBack();
            $code = 500;
            $msg = $e->getMessage();
        }

        if ($code != "200") {
            return response()->json([
                "error" => [
                    "code" => $code,
                    "message" => $msg
                ]
            ]);
        } else {
            $cafe_rows = [
                "market_order_id" => $ord_no
            ];
            return response()->json([
                "data" => $cafe_rows
            ]);
        }
    }

    function getCanceled(Request $req){
    }

    function getClaimList(Request $req){

        //$com_id = Auth('partner')->user()->com_id;
        $com_id = $this->user["id"];

        $cafe_ord_status = [
            "40" => "8",
            "50" => "8",
            "60" => "10",
            "41" => "7",
            "51" => "7",
            "61" => "9",
        ];
        $cafe_ord_status2 = [
            "7" => array(41,51),
            "8" => array(40,50),
            "10" => "60",
            "9" => "61",
        ];

        $sdate          = $req->input("start_date", date("Ymd"));
        $edate          = $req->input("end_date", now()->sub(3, 'month')->format('Ymd'));
        $clm_state      = $req->input("item_status", "");

        $where = "";
        $where .= " and a.ord_date >= cast('$sdate' as date) ";
        $where .= " and a.ord_date < DATE_ADD('$edate', INTERVAL 1 DAY) ";
        if($clm_state != ""){
            if(isset($cafe_ord_status2[$clm_state])) {
                if(is_array($cafe_ord_status2[$clm_state])){
                    $where .= " and a.clm_state in (" . join(",",$cafe_ord_status2[$clm_state]) . ") ";
                } else {
                    $where .= " and a.clm_state = '" . $cafe_ord_status2[$clm_state] . "' ";
                }
            } else {
                $where .= " and a.clm_state = '00' ";
            }
        }

        $query = /** @lang text */
            "
            select
                '' as chkbox, a.ord_no, a.ord_opt_no, ord_state.code_val ord_state, a.clm_state,clm_state.code_val clm_state_nm,
                ifnull(gt.code_val,'N/A') as goods_type_nm, a.style_no, a.goods_nm,
                a.goods_no,
                a.clm_reason,a.clm_qty,a.clm_date
            from (
                select
                    b.ord_no, a.ord_opt_no, a.ord_state,a.clm_state,c.goods_type, a.goods_no,c.style_no, a.goods_nm,
                    a.goods_opt, a.qty, concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm, b.r_nm,
                    cd.code_val as clm_reason,clm_dt.clm_qty,g.last_up_date as clm_date
                from order_opt a
                    inner join order_mst b on a.ord_no = b.ord_no
                    inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                    inner join claim g on g.ord_opt_no = a.ord_opt_no
                    inner join claim_detail clm_dt on g.clm_no = clm_dt.clm_no
                    left outer join code cd on cd.code_kind_cd = 'G_CLM_REASON' and g.clm_reason = cd.code_id
                where 1=1
                and a.com_id = :com_id and a.clm_state >= 40
                $where
                order by a.ord_opt_no
            ) a
            left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
            left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
            left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
        ";

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute(["com_id" => $com_id]);
        $orders = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $orders[$row["ord_no"]][] = $row;
        }
        $cafe_rows = array();
        foreach($orders as $ord_no => $rows){
            $cafe_row = [
                "order_id" => $ord_no,
                "items" => []
            ];
            for($i=0;$i < count($rows);$i++){
                $row = $rows[$i];
                $cafe_row["items"][] =  [
                    "items_status" => $cafe_ord_status[$row["clm_state"]],
                    "market_item_no" => $row["ord_opt_no"],
                    "market_product_code" => $row["goods_no"],
                    "market_product_name" => $row["goods_nm"],
                    "claim_no" => $row["ord_opt_no"],
                    "claim_quantity" => $row["clm_qty"],
                    "claim_reason" => $row["clm_reason"],
                    "claim_reason_detail" => "",
                    "claim_datetime" => $row["clm_date"]
                ];
            }
            $cafe_rows[] = $cafe_row;
        }

        //echo "<pre>$query</pre>";
        //dd($query);
/*        $cafe_rows = array();
        $rows = DB::select($query,["com_id" => $com_id]);
        $cafe_rows = array();
        for($i=0;$i<count($rows);$i++){
            $row = (array)$rows[$i];
            $cafe_rows[] = [
                "order_id" => $row["ord_no"],
                "claim_reason" => $row["clm_reason"],
                "claim_reason_detail" => "",
                "items" => [
                    [
                        "items_status" => array_flip($cafe_ord_status)[$row["clm_state"]],
                        "market_item_no" => $row["ord_opt_no"],
                        "market_product_code" => $row["goods_no"],
                        "market_product_name" => $row["goods_nm"],
                        "claim_quantity" => $row["clm_qty"],
                        "claim_no" => $row["ord_opt_no"],
                        "claim_datetime" => $row["clm_date"]
                    ]
                ]
            ];
        }*/

        return response()->json([
            "data" => $cafe_rows
        ]);
    }


}





