<?php

namespace App\Http\Controllers\head\product;

use App\Components\Lib;
use App\Components\SLib;

use App\Http\Controllers\Controller;
use App\Models\Conf;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Option;
use App\Models\Jaego;

class prd07Controller extends Controller
{
    public function index(Request $request)
	{
		/**
		 * post 형식으로 window.open 하여 전달받은 goods_nos 있으면 뷰에 전달
		 */
		$goods_nos = $request->input('goods_nos', '');

		/**
		 * 설정 값 얻기
		 */
		$conf = new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery", "base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery", "free_delivery_amt");
		$cfg_point_ratio		= $conf->getConfigValue("point", "ratio", "0");

		$sql = "select id id, name val from mgr_user where md_yn = 'Y' and use_yn = 'Y' order by name";
		$md_names = DB::select($sql);

		$values = [
			'goods_nos' => $goods_nos,
			'items' => SLib::getItems(),
			'com_types' => SLib::getCodes('G_COM_TYPE'),
			'goods_stats' => SLib::getCodes("G_GOODS_STAT"),
			'md_names' => $md_names,
			'baesong_infos' => SLib::getCodes("G_BAESONG_INFO"),
			'baesong_kinds' => SLib::getCodes("G_BAESONG_KIND"),
			'dlv_pay_types' => SLib::getCodes("G_DLV_PAY_TYPE"),
			'dlv_fee_yn' => ['' => "==유료/무료==", 'Y' => "유료", 'N' => "무료"],
			'point_yn' => ['' => "==지급여부==", 'Y' => "지급함", 'N' => "지급안함"],
			'point_unit' => [''=> "단위", 'W' => "원", 'P' => "%"],
			'dlv_due_types' => SLib::getCodes("G_DLV_DUE_TYPE"),
			'dlv_fee' => Lib::cm($cfg_dlv_fee),
			'free_dlv_fee_limit' => Lib::cm($cfg_free_dlv_fee_limit),
			'order_point_ratio'	=> $cfg_point_ratio,
			'tax_yn' => ['' => "==과세 구분==", 'Y' => "과세", 'N' => "면세"]
		];
		return view(Config::get('shop.head.view') . '/product/prd07', $values);
	}

	/*
		Function: CheckInt
			지정한 정수얻기

		Parameters:
			p_name - 변수
			default - 값이 없을 경우 기본값

		Returns:
			int
	*/
	public function checkInt($p_name, $default = 0)
	{
		$p_name = str_replace(",", "", $p_name);
		return is_numeric($p_name) ? (int)$p_name : $default;
	}

	/**
	 * 상품 일괄 등록
	 */
	public function enroll(Request $request) 
	{
		$row = $request->input('row');

		$row["goods_sh"] = array_key_exists('goods_sh', $row) ? $this->checkInt(Lib::Rq($row["goods_sh"])) : null;
		$row["price"] = array_key_exists('price', $row) ? $this->checkInt(Lib::Rq($row["price"])) : null;

		// 데이터의 각각 키 이름들로 변수 할당
		foreach ($row as $key => $value) {
			$$key = is_string($value) ? Lib::Rq($value) : $value;
		}
		
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

		// company, style_no 중복 검사
		$sql = "
			select count(*) as cnt from goods where com_id = '$com_id' and style_no = '" . @$style_no . "'
		";
		$result = DB::selectOne($sql);
		$cnt = $result->cnt;

		if ($cnt > 0) {
			return response()->json([
                "result" => 100, // 중복 : style_no, com_id
				"msg" => "중복스타일넘버"
            ]);
		}

		// 상품 클래스
		$user = [
			'id' => Auth('head')->user()->id,
			'name' => Auth('head')->user()->name
		];
		$goods = new Product($user);

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
			"admin_id"		=> $id,
			"admin_nm"		=> $name,
			"reg_dm"		=> $today_his,
			"upd_dm"		=> $today_his,
			"n_goods_yn"	=> "N",
			"b_goods_yn"	=> "N",
			"goods_location"=> $goods_location
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
						WHERE goods_no = :goods_no AND goods_sub = :goods_sub AND tag = :tag
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
								:goods_no, :goods_sub, :tag, :admin_id, :admin_nm, NOW()
							)
						";
						$inputarr = array(
							"goods_no" => $goods_no,
							"goods_sub" => $goods_sub,
							"tag" => $_tag,
							"id" => $id,
							"name" => $name
						);
						DB::insert($sql, $inputarr);
					}

				}
			}

			DB::commit();
			return response()->json([
				"result" => 1,
                "msg" => $goods_no."-".$goods_sub
            ]);

		} catch (Exception $e) {
			DB::rollback();
			dd($e);
			return response()->json([
                "result" => 0,
				"msg" => "시스템에러"
            ]);
		}

	}



}