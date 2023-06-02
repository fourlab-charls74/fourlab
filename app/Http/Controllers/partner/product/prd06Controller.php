<?php

namespace App\Http\Controllers\partner\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\Jaego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Components\SLib;
use App\Models\Conf;

class prd06Controller extends Controller
{
    public function index() {
		
		$conf = new Conf();
        $com_id = Auth('partner')->user()->com_id;


        $opt_cd_list = $this->get_opt_cd_list();
        $com_info = $this->get_com_info($com_id);
        $point_info = $this->get_point_info();
		$cfg_point_ratio = $conf->getConfigValue("point", "ratio", "0");
		
        $values = [
            'com_id' => $com_id,
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
            'alter_reasons' => SLib::getCodes('G_JAEGO_REASON'),
            'opt_cd_list' => $opt_cd_list,
            'com_info' => $com_info,
            'point_info' => $point_info,
			'order_point_ratio'	=> $cfg_point_ratio,
        ];
        return view( Config::get('shop.partner.view') . '/product/prd06',$values);
    }

    public function store(Request $request){

//        $subject = $request->input('subject');
//        $content = $request->input('content');
//        $use_yn = $request->input('use_yn');
//        $main_yn = $request->input('main_yn','N');
//        $notice_yn = $request->input('notice_yn','N');
//        $popup_yn = $request->input('popup_yn','N');
//        $popup_type = $request->input('popup_type');
//
//        $validator = $request->validate([
//            'subject' => 'required',
//            'content' => 'required',
//        ]);

//        if ($validator->fails()) {
//        } else {
//            $notice_shop = [
//                'subject' => $subject,
//                'content' => $content,
//                'admin_id' => 'smson',
//                'admin_nm' => '손상모',
//                'admin_email' => 'steve92son@gmail.com',
//                'use_yn' => $use_yn,
//                'main_yn' => $main_yn,
//                'notice_yn' => $notice_yn,
//                'popup_yn' => $popup_yn,
//                'popup_type' => $popup_type,
//                'd_cat_cds' => '',
//                'disp_prd_yn' => '',
//                'disp_prd_type' => '',
//                'cnt' => 0,
//                'regi_date' => DB::raw('now()'),
//                'ut' => DB::raw('now()')
//            ];
//
//            try {
//                DB::transaction(function () use (&$result,$notice_shop) {
//                    DB::table('notice_shop')->insert($notice_shop);
//                });
//                $code = 200;
//            } catch(Exception $e){
//                $code = 500;
//            }
//            echo json_encode(array(
//                "code" => $code
//            ));
//        }


        $row = (array)DB::table('goods')->where("goods_no","=",123439)->first();
        $product = array(
            "goods_sub"		=> 0,
            "com_id"		=> $row["com_id"],
            "com_type"		=> $row["com_type"],
            "opt_kind_cd"	=> $row["opt_kind_cd"],
            "brand"			=> $row["brand"],
            "rep_cat_cd"	=> $row["rep_cat_cd"],
            "style_no"		=> $row["style_no"],
            "goods_nm"		=> '상모'.$row["goods_nm"],
            "goods_nm_eng"	=> $row["goods_nm_eng"],
            "price"			=> $row["price"],
            "goods_sh"		=> $row["goods_sh"],
            "wonga"			=> $row["wonga"],
            "head_desc"		=> $row["head_desc"],
            "ad_desc"		=> $row["ad_desc"],
            "baesong_info"	=> $row["baesong_info"],
            "baesong_kind"	=> $row["baesong_kind"],
            "dlv_pay_type"	=> $row["dlv_pay_type"],
            "dlv_fee_cfg"	=> $row["dlv_fee_cfg"],
            "bae_yn"		=> $row["bae_yn"],
            "baesong_price"	=> $row["baesong_price"],
            "point_cfg"		=> $row["point_cfg"],
            "point_yn"		=> $row["point_yn"],
            "point_unit"	=> $row["point_unit"],
            "point"			=> $row["point"],
            "org_nm"		=> $row["org_nm"],
            "md_id"			=> $row["md_id"],
            "md_nm"			=> $row["md_nm"],
            "make"			=> $row["make"],
            "goods_cont"	=> $row["goods_cont"],
            "spec_desc"		=> $row["spec_desc"],
            "baesong_desc"	=> $row["baesong_desc"],
            "opinion"		=> $row["opinion"],
            "is_option_use"	=> $row["is_option_use"],
            "option_kind"	=> $row["option_kind"],
            "is_unlimited"	=> $row["is_unlimited"],
            "tax_yn"		=> $row["tax_yn"],
            "sale_stat_cl"	=> $row["sale_stat_cl"],
            "goods_type"	=> $row["goods_type"],
            "special_yn"	=> $row["special_yn"],
            "delv_area"		=> $row["delv_area"],
            "related_cfg"	=> $row["related_cfg"],
            "restock_yn"	=> $row["restock_yn"],
            "admin_id"		=> 'smson',
            "admin_nm"		=> '손상모',
            "reg_dm"		=> date("Y-m-d H:i:s"),
            "upd_dm"		=> date("Y-m-d H:i:s"),
            "n_goods_yn"	=> "N",
            "b_goods_yn"	=> "N",
            "goods_location"=> '',
        );


        try {
            DB::transaction(function () use (&$result,$product) {

                $user = array(
                    "id" => 'smson',
                    "name" => '손상모'
                );
                $prd = new Product($user);
                $goods_no = $prd->GetNextGoodsNo();
                $product["goods_no"] = $goods_no;
                $prd->Add($product);

                $d_cat_cd = $product["rep_cat_cd"];
                if(strlen($d_cat_cd) >=3){
                    $dcat = new Category($user,"DISPLAY");
                    for($i = 1; $i <= strlen($d_cat_cd)/3; $i++) {
                        $code = substr($d_cat_cd, 0, $i*3);
                        $dcat->SetCode( $code );
                        $dcat->AddProduct($goods_no);
                    }
                }

                $u_cat_cd = isset($product["u_cat_cd"])? $product["u_cat_cd"]:"";
                if(strlen($u_cat_cd) >=3){
                    $ucat = new Category($user,"ITEM");
                    for($i = 1; $i <= strlen($u_cat_cd)/3; $i++) {
                        $code = substr($u_cat_cd, 0, $i*3);
                        $ucat->SetCode( $code );
                        $ucat->AddProduct($goods_no);
                    }
                }

                $option_kind = $product["option_kind"];
                $multi_pos = strpos($option_kind, "^");

                // 옵션명 등록
                $a_opt_name = explode("^", $option_kind);

                for( $i = 0; $i < count($a_opt_name); $i++){
                    if($i > 2){
                        break;
                    }
                    $prd->AddOption("basic",trim($a_opt_name[$i]));
                }

                $opt1 = "";
                $opt2 = "";
                $opt_qty = "";
                $opt_price = "";
                $wonga = 0;


                // 옵션 등록
                $a_opt1 = ( $opt1 != "" ) ?  explode(",", $opt1) : array();
                $a_opt2 = ( $opt2 != "" ) ?  explode(",", $opt2) : array();

                // 옵션 수량
                $a_opt_qty = ( $opt_qty != "" ) ?  explode(",", $opt_qty) : array();

                // 옵션 가격
                $a_opt_price = ( $opt_price != "" ) ?  explode(",", $opt_price) : array();

                $jaego = new Jaego($user);
                //$jaego->Plus();
                //옵션

                if($multi_pos !== false){
                    for( $i = 0; $i < count($a_opt1); $i++)
                    {
                        $_opt1 = $a_opt1[$i];

                        $_opt_qty = (isset($a_opt_qty[$i]) && trim($a_opt_qty[$i]) != "") ? trim($a_opt_qty[$i]) : 0;

                        for( $j=0; $j < count($a_opt2); $j++ ){
                            $_opt2 = $a_opt2[$j];
                            $goods_opt = sprintf("%s^%s", $_opt1, $_opt2);

                            $_opt_price = isset($a_opt_price[$i]) ? trim($a_opt_price[$i]) : 0;

                            $prd->AddOptionQty($goods_opt,$_opt_qty,$_opt_price,$i);

                            $jaego->Plus($goods_no,$goods_opt,$_opt_qty,[
                                "type" => 9,
                                "etc" => "재고수정",
                                "wonga" => $wonga,
                                "invoice_no" => date("Ymd"),
                                "opt_seq" => $i+$j
                            ]);

                        }
                    }

                } else {
                    // 단일옵션
                    for( $i = 0; $i < count($a_opt1); $i++) {

                        $goods_opt = $a_opt1[$i];
                        $_opt_qty = (isset($a_opt_qty[$i]) && trim($a_opt_qty[$i]) != "") ? trim($a_opt_qty[$i]) : 0;
                        $_opt_price = isset($a_opt_price[$i]) ? trim($a_opt_price[$i]) : 0;

                        $prd->AddOptionQty($goods_opt,$_opt_qty,$_opt_price,$i);

                        $jaego->Plus($goods_no,$goods_opt,$_opt_qty,[
                            "type" => 9,
                            "etc" => "재고수정",
                            "wonga" => $wonga,
                            "invoice_no" => date("Ymd"),
                            "opt_seq" => $i
                        ]);
                    }
                }

            });
            $code = 200;
        } catch(\Exception $e){
            $code = 500;
        }



    }

    public function store_bundle(request $request){
        //dd($request);

        $com_id = Auth('partner')->user()->com_id;
        $com_info = $this->get_com_info($com_id);


        $form_str = $request->input('form_str');
        $form_rows = explode('<br />',nl2br($form_str));
        //dd($form_rows);
        $i = 0;
        $values = array();
        $keys = array();
        for($i;$i < count($form_rows); $i++){
            $rows = explode(',',$form_rows[$i]);

            if($i == 0){
                $y =0;
                for($y;$y<count($rows);$y++){
                    $keys[$y] = $rows[$y];
                }
            }else{
                $y =0;
                for($y;$y<count($rows);$y++){
                    $values[($i-1)][$y] = $rows[$y];
                }
            }
        }


        $product = array();
        foreach($values as $value){
            $c =0;
            for($c; $c < count($keys); $c++){
                $product_kr[$keys[$c]] = $value[$c];
            }

            $product['goods_nm'] = $product_kr['상품명'];
            $product['goods_sub'] = 0;
            $product['goods_nm_eng'] = $product_kr['상품 영문명'];
            $product['com_id'] = $com_id;
            $product['com_type'] = $com_info->com_type;
            $product['opt_kind_cd'] = $product_kr['품목'];
            $product['brand'] = $product_kr['브랜드'];
            $product['rep_cat_cd'] = $product_kr['대표카테고리코드'];
            //$product['u_cat_cd'] = $product_kr['용도카테고리코드'];
            $product['style_no'] = $product_kr['스타일넘버'];
            $product['price'] = $product_kr['판매가'];
            $product['wonga'] = $product_kr['원가'];
            //$product['chk_option_kind1'] = $product_kr['옵션구분'];
            $product['option_kind'] = $product_kr['옵션1'];
            //$product['chk_option_kind2'] = $product_kr['옵션구분'];
            //$product['option_kind2'] = $product_kr['옵션2'];
            //$product['qty'] = $product_kr['수량'];
            //$product['opt_price'] = $product_kr['옵션가격'];
            $product['head_desc'] = $product_kr['상단홍보글'];
            $product['ad_desc'] = $product_kr['하단홍보글'];
            $product['baesong_info'] = $product_kr['배송방법'];
            $product['baesong_kind'] = $product_kr['배송처리'];
            $product['dlv_pay_type'] = $product_kr['배송비지불'];
            $product['dlv_fee_cfg'] = $product_kr['배송비설정'];
            $product['bae_yn'] = $product_kr['배송비여부'];
            $product['baesong_price'] = $product_kr['배송비'];
            $product['point_cfg'] = $product_kr['적립금설정'];
            $product['point_yn'] = $product_kr['적립금여부'];
            //$product['point_rate'] = $product_kr['적립율'];
            $product['point'] = $product_kr['적립금'];
            $product['org_nm'] = $product_kr['원산지'];
            $product['md_nm'] = $product_kr['md_nm'];
            $product['md_id'] = $product_kr['md_id'];
            $product['make'] = $product_kr['제조사'];
            $product['goods_cont'] = $product_kr['상품상세'];
            $product['spec_desc'] = $product_kr['제품사양'];
            $product['baesong_desc'] = $product_kr['예약/배송'];
            $product['opinion'] = $product_kr['MD상품평'];
            $product['restock_yn'] = $product_kr['재입고알림'];
            $product['admin_id'] = 'smson';
            $product['admin_nm'] = '손상모';
            $product['reg_dm'] = date("Y-m-d H:i:s");
            $product['upd_dm'] = date("Y-m-d H:i:s");
            $product['n_goods_yn'] = 'N';
            $product['b_goods_yn'] = 'N';
            $product['goods_location'] = '';
            $product['tax_yn'] = $product_kr["과세구분"];

            $user = array(
                "id" => 'smson',
                "name" => '손상모'
            );
            //$prd = new Product($user);
            //$goods_no = $prd->GetNextGoodsNo();
            //$product["goods_no"] = $goods_no;
            //$result = $prd->Add($product);
            $result = $this->add($product);
            echo($result);
        }

    }

    private function add($product){
        $code = 0;
        try {
            DB::transaction(function () use (&$result,$product) {

                $user = array(
                    "id" => 'smson',
                    "name" => '손상모'
                );
                $prd = new Product($user);
                $goods_no = $prd->GetNextGoodsNo();
                $product["goods_no"] = $goods_no;
                $prd->Add($product);

                $d_cat_cd = $product["rep_cat_cd"];
                if(strlen($d_cat_cd) >=3){
                    $dcat = new Category($user,"DISPLAY");
                    for($i = 1; $i <= strlen($d_cat_cd)/3; $i++) {
                        $code = substr($d_cat_cd, 0, $i*3);
                        $dcat->SetCode( $code );
                        $dcat->AddProduct($goods_no);
                    }
                }

                $u_cat_cd = isset($product["u_cat_cd"])? $product["u_cat_cd"]:"";
                if(strlen($u_cat_cd) >=3){
                    $ucat = new Category($user,"ITEM");
                    for($i = 1; $i <= strlen($u_cat_cd)/3; $i++) {
                        $code = substr($u_cat_cd, 0, $i*3);
                        $ucat->SetCode( $code );
                        $ucat->AddProduct($goods_no);
                    }
                }

                $option_kind = $product["option_kind"];
                $multi_pos = strpos($option_kind, "^");

                // 옵션명 등록
                $a_opt_name = explode("^", $option_kind);

                for( $i = 0; $i < count($a_opt_name); $i++){
                    if($i > 2){
                        break;
                    }
                    $prd->AddOption("basic",trim($a_opt_name[$i]));
                }

                $opt1 = "";
                $opt2 = "";
                $opt_qty = "";
                $opt_price = "";
                $wonga = 0;


                // 옵션 등록
                $a_opt1 = ( $opt1 != "" ) ?  explode(",", $opt1) : array();
                $a_opt2 = ( $opt2 != "" ) ?  explode(",", $opt2) : array();

                // 옵션 수량
                $a_opt_qty = ( $opt_qty != "" ) ?  explode(",", $opt_qty) : array();

                // 옵션 가격
                $a_opt_price = ( $opt_price != "" ) ?  explode(",", $opt_price) : array();

                $jaego = new Jaego($user);
                //$jaego->Plus();
                //옵션

                if($multi_pos !== false){
                    for( $i = 0; $i < count($a_opt1); $i++)
                    {
                        $_opt1 = $a_opt1[$i];

                        $_opt_qty = (isset($a_opt_qty[$i]) && trim($a_opt_qty[$i]) != "") ? trim($a_opt_qty[$i]) : 0;

                        for( $j=0; $j < count($a_opt2); $j++ ){
                            $_opt2 = $a_opt2[$j];
                            $goods_opt = sprintf("%s^%s", $_opt1, $_opt2);

                            $_opt_price = isset($a_opt_price[$i]) ? trim($a_opt_price[$i]) : 0;

                            $prd->AddOptionQty($goods_opt,$_opt_qty,$_opt_price,$i);

                            $jaego->Plus($goods_no,$goods_opt,$_opt_qty,[
                                "type" => 9,
                                "etc" => "재고수정",
                                "wonga" => $wonga,
                                "invoice_no" => date("Ymd"),
                                "opt_seq" => $i+$j
                            ]);

                        }
                    }

                } else {
                    // 단일옵션
                    for( $i = 0; $i < count($a_opt1); $i++) {

                        $goods_opt = $a_opt1[$i];
                        $_opt_qty = (isset($a_opt_qty[$i]) && trim($a_opt_qty[$i]) != "") ? trim($a_opt_qty[$i]) : 0;
                        $_opt_price = isset($a_opt_price[$i]) ? trim($a_opt_price[$i]) : 0;

                        $prd->AddOptionQty($goods_opt,$_opt_qty,$_opt_price,$i);

                        $jaego->Plus($goods_no,$goods_opt,$_opt_qty,[
                            "type" => 9,
                            "etc" => "재고수정",
                            "wonga" => $wonga,
                            "invoice_no" => date("Ymd"),
                            "opt_seq" => $i
                        ]);
                    }
                }

            });
            $code = 200;
        } catch(\Exception $e){
            $code = 500;
        }

        return $code;

    }

    private function get_opt_cd_list(){
        $query = "select opt_kind_cd as 'name', opt_kind_nm as 'value' from opt where opt_id = 'K' and use_yn = 'Y' order by opt_seq";

        $result = DB::select($query);

        return $result;
    }

    private function get_com_info($com_id){

        $query = "
			select a.com_nm, a.com_id, a.com_type, a.margin_type, a.pay_fee, a.baesong_kind, a.baesong_info, a.md_nm, b.id as md_id,
				ifnull(a.dlv_policy,'S') as dlv_policy, a.dlv_amt, a.free_dlv_amt_limit
			from company a
				left outer join mgr_user b on a.md_nm = b.name and b.md_yn = 'Y'
			where a.com_id = '$com_id'
        ";


        $result = DB::select($query);

        if($result[0]->dlv_policy == "S"){
            $fee_query = "
				select value, mvalue from conf where type = 'delivery' and name = 'base_delivery_fee'
            ";

            $fee = DB::select($fee_query);

            $amt_query = "
				select value, mvalue from conf where type = 'delivery' and name = 'free_delivery_amt'
            ";

            $amt = DB::select($amt_query);


            $result[0]->dlv_amt = $fee[0]->value;
            $result[0]->free_dlv_amt_limit = $amt[0]->value;
        }

        return $result[0];
    }

    private function get_point_info(){
        $query = "select value, mvalue from conf where type = 'point' and name = 'ratio'";

        $result = DB::select($query);

        return $result[0];
    }

	public function checkInt($p_name, $default = 0)
	{
		$p_name = str_replace(",", "", $p_name);
		return is_numeric($p_name) ? (int)$p_name : $default;
	}
	
	public function enroll2(Request $request)
	{
		$row = $request->input('row');
		$row["goods_sh"] = array_key_exists('goods_sh', $row) ? $this->checkInt(Lib::Rq($row["goods_sh"])) : null;
		$row["price"] = array_key_exists('price', $row) ? $this->checkInt(Lib::Rq($row["price"])) : null;

		// 데이터의 각각 키 이름들로 변수 할당
		foreach ($row as $key => $value) {
			$$key = is_string($value) ? Lib::Rq($value) : $value;
		}

		$sql = "
			select 
				com_type
			from company 
			where dp_yn = 'Y' and com_id = '$com_id'
		";

		$res = DB::selectOne($sql);
		$com_type = $res->com_type;

		// 상수 값 할당
		$conf = new Conf();
		$cfg_free_delivery_amt = $conf->getConfigValue("delivery", "free_delivery_amt");
		$cfg_base_delivery_fee = $conf->getConfigValue("delivery", "base_delivery_fee");

		$sale_stat_cl	= "5";
		$goods_type		= ($com_type == "1") ? "S" : "P";
		$special_yn		= "N";
		$related_cfg	= "A";

		// 쇼핑몰 정책에 의한 배송비
		if ($dlv_fee_cfg == "S") {
			$baesong_price = ($price < $cfg_free_delivery_amt) ? $cfg_base_delivery_fee : "0";
		}

		// 적립금 단위
		$point_unit = ($point_unit == "%") ? "W" : "P";

		// option varidation
		$patten = "/[#\&%@=\\\:;'\"\^`\_|\!\?\*$#<>\{\}]/i";
		$opt1 = preg_replace($patten, "", @$opt1);
		$opt2 = preg_replace($patten, "", @$opt2);


		// 상품 클래스
		$user = [
			'id' => Auth('head')->user()->id,
			'name' => Auth('head')->user()->name
		];
		$goods = new Product($user);

		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		// 상품번호 생성
		$goods_no = $goods->GetNextGoodsNo();
		$goods_sub = 0;

		// MD 이름, 아이디 얻기
		$sql = "
			select id from mgr_user where name = '" . @$md_nm . "'
		";
		$result = DB::selectOne($sql);
		$md_id = isset($result->id) ? $result->id : "";



		$today_his = date("Y-m-d H:i:s");
		$param = @array(
			"goods_no"		=> $goods_no,
			"goods_sub"		=> $goods_sub,
			"com_id"		=> $com_id,
			"com_type"		=> $com_type,
			"opt_kind_cd"	=> $opt_kind_cd,
			"brand"			=> $brand,
			"rep_cat_cd"	=> $rep_cat_cd,
			"style_no"		=> $style_no,
			"goods_nm"		=> $goods_nm,
			"goods_nm_eng"	=> $goods_nm_eng,
			"price"			=> $price,
			"goods_sh"		=> $goods_sh,
			"wonga"			=> $wonga,
			"head_desc"		=> $head_desc,
			"ad_desc"		=> $ad_desc,
			"baesong_info"	=> $baesong_info,
			"baesong_kind"	=> $baesong_kind,
			"dlv_pay_type"	=> $dlv_pay_type,
			"dlv_fee_cfg"	=> $dlv_fee_cfg,
			"bae_yn"		=> $bae_yn,
			"baesong_price"	=> $baesong_price,
			"point_cfg"		=> $point_cfg,
			"point_yn"		=> $point_yn,
			"point_unit"	=> $point_unit,
			"point"			=> $point,
			"org_nm"		=> $org_nm,
			"md_id"			=> $md_id,
			"md_nm"			=> $md_nm,
			"make"			=> $make,
			"goods_cont"	=> $goods_cont,
			"spec_desc"		=> $spec_desc,
			"baesong_desc"	=> $baesong_desc,
			"opinion"		=> $opinion,
			"is_option_use"	=> $is_option_use,
			"option_kind"	=> $option_kind,
			"is_unlimited"	=> $is_unlimited,
			"tax_yn"		=> $tax_yn,
			"sale_stat_cl"	=> $sale_stat_cl,
			"goods_type"	=> $goods_type,
			"special_yn"	=> $special_yn,
			"delv_area"		=> $delv_area,
			"related_cfg"	=> $related_cfg,
			"restock_yn"	=> $restock_yn,
			"admin_id"		=> $admin_id,
			"admin_nm"		=> $admin_nm,
			"reg_dm"		=> $today_his,
			"upd_dm"		=> $today_his,
			"n_goods_yn"	=> "N",
			"b_goods_yn"	=> "N",
			"goods_location"=> $goods_location,
		);

		DB::beginTransaction();
		try {
			$goods->Add( $param ); // 상품 추가 클래스

			// 전시카테고리 등록
			if ($rep_cat_cd) {
				$int = strlen($rep_cat_cd)/3;
				for ($i = 1; $i <= $int; $i++)
				{
					$cd = substr($rep_cat_cd, 0, $i*3);
					$category = new Category($user, "DISPLAY"); // 카테고리 클래스 호출
					$category->SetCode( $cd ); //카테고리 번호 설정
					$category->SetGoodsNoSub( $goods_no, $goods_sub ); //상품일련번호 설정
					$category->AddGoods( "" ); //카테고리에 상품 등록
				}
			}

			// 용도별 카테고리 등록
			if ($u_cat_cd)
			{
				$cat_depth = strlen($u_cat_cd)/3;
				for($j = 1; $j <= $cat_depth; $j++)
				{
					$cat = substr($u_cat_cd, 0, $j*3);
					$category = new Category($user, "ITEM");
					$category->SetCode( $cat );
					$category->SetGoodsNoSub( $goods_no, $goods_sub );
					$category->AddGoods( "" );
				}
			}

			///////////////////////////////////////////////////////
			//
			//		옵션 등록 ( 매입 상품은 제외)
			//
			///////////////////////////////////////////////////////

			// 멀티옵션 여부
			$multi_pos = strpos($option_kind, "^");

			// 옵션명 등록
			$a_opt_name = explode("^", $option_kind);
			$options = new Option($user, $goods_no, $goods_sub);
			$options->setGoods($goods_no, $goods_sub);

			for ( $i = 0; $i < count($a_opt_name); $i++ ) {
				if($i > 2){
					break;
				}
				$options->addOptionName(
					array(
						"type"			=> "basic",
						"name"			=> trim(Lib::Rq($a_opt_name[$i])),
						"required_yn"	=> "Y",
						"use_yn"		=> "Y",
						"option_no"		=> "",
						"seq"			=> "0"
					)
				);
			}

			// 옵션 등록
			$a_opt1 = ( $opt1 != "" ) ?  explode(",", $opt1) : array();
			$a_opt2 = ( $opt2 != "" ) ?  explode(",", $opt2) : array();

			// 옵션 수량
			$a_opt_qty = ( @$opt_qty != "" ) ?  explode(",", $opt_qty) : array();

			// 옵션 가격
			$a_opt_price = ( @$opt_price != "" ) ?  explode(",", $opt_price) : array();

			$jaego = new Jaego( $user );  //재고 클래스 호출

			// 멀티옵션
			if ( $multi_pos != false ) {

				for( $i = 0; $i < count($a_opt1); $i++) {

					$_opt1 = Lib::Rq($a_opt1[$i]);

					// 수량 얻기
					$_opt_qty = (isset($a_opt_qty[$i]) && trim($a_opt_qty[$i]) != "") ? trim($a_opt_qty[$i]) : 0;

					for ( $j=0; $j < count($a_opt2); $j++ ) {

						$_opt2 = Lib::Rq($a_opt2[$j]);
						$goods_opt = sprintf("%s^%s", $_opt1, $_opt2);

						// 옵션 재고 등록
						$jaego->Plus( array(
							"type" => 9,
							"etc" => "재고수정",
							"qty" => $_opt_qty,
							"goods_no" => $goods_no,
							"goods_sub" => $goods_sub,
							"goods_opt" => $goods_opt,
							"wonga" => $wonga,
							"invoice_no" => date("Ymd"),
							"opt_seq" => ($i+$j)
						));

						// 옵션가 등록
						$_opt_price = isset($a_opt_price[$i]) ? trim($a_opt_price[$i]) : 0;
						$options->modBasicOption(
							array(
								"opt1" => $_opt1,
								"goods_opt" => $goods_opt,
								"opt_price" => $_opt_price,
								"opt_qty" => $_opt_qty,
								"opt_seq" => ($i+$j)
							)
						);
					}
				}

			} else {

				// 단일옵션
				for ( $i = 0; $i < count($a_opt1); $i++ ) {

					$goods_opt = Lib::Rq($a_opt1[$i]);

					// 옵션별 수량
					$_opt_qty = (isset($a_opt_qty[$i]) && trim($a_opt_qty[$i]) != "") ? trim($a_opt_qty[$i]) : 0;

					// 옵션 재고 등록
					$jaego->Plus( array(
						"type" => 9,
						"etc" => "재고수정",
						"qty" => $_opt_qty,
						"goods_no" => $goods_no,
						"goods_sub" => $goods_sub,
						"goods_opt" => $goods_opt,
						"wonga" => $wonga,
						"invoice_no" => date("Ymd"),
						"opt_seq" => $i
					));

					// 옵션가 등록
					$_opt_price = isset($a_opt_price[$i]) ? trim($a_opt_price[$i]) : 0;
					$options->modBasicOption(
						array(
							"opt1" => $goods_opt,
							"goods_opt" => $goods_opt,
							"opt_price" => $_opt_price,
							"opt_qty" => $_opt_qty,
							"opt_seq" => $i
						)
					);
				}
			}

			///////////////////////////////////////////////////////
			//
			//		옵션등록 끝
			//
			///////////////////////////////////////////////////////

			// 태그 등록
			if ( @$tags != "" ) {

				$a_tags = explode(",", $tags);

				for ( $i = 0; $i < count($a_tags); $i++) {

					$_tag = Lib::Rq(trim($a_tags[$i]));

					if ( $_tag == "" ) continue;

					// 태그 검색
					$sql = "SELECT count(*) AS cnt
						FROM goods_tags
						WHERE goods_no = $goods_no AND goods_sub = $goods_sub AND tag = '$_tag'
					";

					$selectarr = array(
						"goods_no" => $goods_no,
						"goods_sub" => $goods_sub,
						"tag"=> $_tag
					);

					$result = DB::selectOne($sql, $selectarr);
					$cnt = $result->cnt;

					if ($cnt == 0) {
						$sql = "INSERT INTO goods_tags (
								goods_no, goods_sub, tag, admin_id, admin_nm, rt
							) VALUES (
								$goods_no, $goods_sub, '$_tag', '$admin_id', '$admin_nm', NOW()
							)
						";
						$inputarr = array(
							"goods_no" => $goods_no,
							"goods_sub" => $goods_sub,
							"tag" => $_tag,
							"id" => $admin_id,
							"name" => $admin_nm
						);
						DB::insert($sql, $inputarr);
					}

				}
			}

			DB::commit();
			return response()->json([
				"result" => 1,
				"msg" => $goods_no."-".$goods_sub,
				"code" => 200
			]);
		} catch (Exception $e) {
			DB::rollback();
			return response()->json([
				"result" => 0,
				"msg" => "시스템에러",
				"code" => 500

				// "msg" => $e->getMessage()
			]);
		}

	}


}
