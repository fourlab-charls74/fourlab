<?php

namespace App\Http\Controllers\head\standard;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class std02Controller extends Controller
{
	public function index()
	{
		$com_types = SLib::getCodes("G_COM_TYPE");
		$values = [ "com_types" => $com_types ];
		return view(Config::get('shop.head.view') . '/standard/std02', $values);
	}

	public function search(Request $request)
	{
		// 설정 값 얻기
		//$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		//$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_dlv_fee = "2500";
		$cfg_free_dlv_fee_limit = "20000";

		$com_nm	= $request->input("com_nm");
		$com_type	= $request->input("com_type");
		$md_nm	= $request->input("md_nm");
		$use_yn	= $request->input("use_yn");
		$margin_type = $request->input("margin_type");
		$api_yn	= $request->input("api_yn");
		$site_yn	= $request->input("site_yn");
		$dlv_policy	= $request->input("dlv_policy");
		$settle_nm = $request->input("settle_nm");

		$where = "";
		if ($com_nm != "") $where .= " and a.com_nm like '%$com_nm%' ";
		if ($com_type != "") $where .= " and a.com_type = '$com_type' ";
		if ($md_nm != "") $where .= " and a.md_nm like '$md_nm%' ";
		if ($use_yn != "") $where .= " and a.use_yn = '$use_yn' ";
		if ($margin_type != "") $where .= " and a.margin_type = '$margin_type' ";
		if ($api_yn != "") $where .= " and a.api_yn = '$api_yn' ";
		if ($site_yn != "") $where .= " and a.site_yn = '$site_yn' ";
		if ($dlv_policy != "") $where .= " and a.dlv_policy = '$dlv_policy' ";
		if ($settle_nm != "") $where .= " and a.settle_nm like '$settle_nm%' ";

		$query = "
			select
				com_type.code_val as com_type, a.com_id, a.com_nm, concat(baesong_kind.code_val, ' / ' ,baesong_info.code_val) as baesong,
				case a.dlv_policy
					when 'S' then '쇼핑몰 정책'
					when 'C' then '업체 정책'
				end as dlv_policy,
				if(a.dlv_policy='S', concat(format($cfg_dlv_fee,0), '원 [ ' , format($cfg_free_dlv_fee_limit,0), '원 이상 무료배송 ]'), concat(format(a.dlv_amt,0), '원 [ ' , format(a.free_dlv_amt_limit,0), '원 이상 무료배송 ]')) as baesong_price,
				a.md_nm, a.settle_nm, a.pay_fee, margin_type.code_val as margin_type, a.site_yn, a.api_yn, a.use_yn,
				case a.biz_type
					when 'P' then '개인'
					when 'C' then '법인'
				end as biz_type,
				a.cs_nm, a.cs_email,  a.cs_phone, a.cs_hp,
				a.staff_nm1, a.staff_email1, a.staff_phone1, a.staff_hp1, a.staff_nm2, a.staff_email2, a.staff_phone2, a.staff_hp2
			from company a
				inner join `code` com_type on a.com_type = com_type.code_id and com_type.code_kind_cd = 'G_COM_TYPE'
				inner join `code` margin_type on a.margin_type = margin_type.code_id and margin_type.code_kind_cd = 'G_MARGIN_TYPE'
				left join `code` baesong_kind on a.baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND'
				left join `code` baesong_info on a.baesong_info = baesong_info.code_id and baesong_info.code_kind_cd = 'G_BAESONG_INFO'
			where 1=1 $where
			order by a.com_nm
        ";
		
		//echo $query;
		$result = DB::select($query);
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			),
			"body" => $result
		]);
	}

	public function show($com_id = '')
	{
		$conf = new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery", "base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery", "free_delivery_amt");
		$cmd = "addcmd";
		$com_type = "";
		$baesong_kind = "";
		$baesong_info = "";
		$pay_day = "";
		$margin_type = "";
		$com_sale_type = "";

		//업체 정보 배열 초기화
		$a_company = array(
			"com_id"			=> "",			"pwd"					=> "",			"com_nm"			=> "",			"com_type"			=> "",
			"site_yn"			=> "",			"com_sale_type"			=> "",			"sale_type"			=> "D",			"homepage"			=> "",
			"store_type"		=> "",			"store_nm"				=> "",			"store_branch"		=> "",			"store_area"		=> "",
			"store_kind"		=> "",			"sell_type"				=> "",			"dp_yn"				=> "Y",			"use_yn"			=> "Y",
			"cs_yn"				=> "N",			"price_yn"				=> "N",			"baesong_kind"		=> "",			"baesong_info"		=> "",
			"dlv_policy"		=> "S",			"dlv_day"				=> "",			"api_yn"			=> "N",			"api_key"			=> "",
			"md_nm"				=> "",			"settle_nm"				=> "",			"pay_fee"			=> "",			"margin_type"		=> "",

			"memo"			 	=> "",			"staff_nm1"				=> "",			"staff_email1"		=> "",			"staff_phone1"		=> "",
			"staff_hp1"			=> "",			"staff_nm2"				=> "",			"staff_email2"		=> "",			"staff_phone2"		=> "",
			"staff_hp2"			=> "",			"name"					=> "",			"biz_num"			=> "",			"ceo"				=> "",

			"jumin_num"			=> "",			"uptae"					=> "",			"upjong"			=> "",			"bank"				=> "",
			"account"			=> "",			"dipositor"				=> "",			"pay_day"			=> "",			"zip_code"			=> "",
			"addr1"				=> "",			"addr2"					=> "",			"r_zip_code"		=> "",			"r_addr1"			=> "",
			"r_addr2"			=> "",			"biz_type"				=> "",			"mail_order_nm"		=> "",			"cs_nm"				=> "",
			"cs_email"			=> "",			"cs_phone"				=> "",			"cs_hp"				=> "",			"dlv_amt"			=> "",
			"free_dlv_amt_limit" => ""
		);
		if ($com_id != "") {
			$cmd = "editcmd";
			$sql = "
			select a.*
			from company a
			where a.com_id = '$com_id'
			";
			$rs = DB::select($sql);
			$a_company = $rs[0];

			//echo "a_company : ". $a_company->com_type;

			$com_type			= isset($a_company->com_type) ? $a_company->com_type : "";
			$baesong_kind		= isset($a_company->baesong_kind) ? $a_company->baesong_kind : "";
			$baesong_info		= isset($a_company->baesong_info) ? $a_company->baesong_info : "";
			$pay_day			= isset($a_company->pay_day) ? $a_company->pay_day : "";
			$margin_type		= isset($a_company->margin_type) ? $a_company->margin_type : "";
			$com_sale_type		= isset($a_company->com_sale_type) ? $a_company->com_sale_type : "";
		}
		$com_type_items				= SLib::getCodes("G_COM_TYPE");
		$baesong_kind_items			= SLib::getCodes("G_BAESONG_KIND");
		$baesong_info_items			= SLib::getCodes("G_BAESONG_INFO");
		$com_site_items				= SLib::getCodes("G_COM_SITE");
		$pay_day_items				= SLib::getCodes("G_PAY_DAY");
		$margin_type_items			= SLib::getCodes("G_MARGIN_TYPE");
		$com_sale_type_items		= SLib::getCodes("G_COM_SALE_TYPE");
		/*
		echo "a_company";
		echo "<br>";
		
		print_r($a_company);
		*/

		$values = [
			"host"						=> $_SERVER["HTTP_HOST"],
			"cmd"						=> $cmd,

			"com_types"					=> $com_type_items,
			"baesong_kinds"				=> $baesong_kind_items,
			"baesong_infos"				=> $baesong_info_items,
			"com_sites"					=> $com_site_items,

			"pay_days"					=> $pay_day_items,
			"pay_day"					=> $pay_day,
			"margin_types"				=> $margin_type_items,
			"margin_type"				=> $margin_type,
			"shop_dlv_fee"				=> $cfg_dlv_fee,
			"shop_free_dlv_fee_limit"	=> $cfg_free_dlv_fee_limit,
			"com_sale_types"			=> $com_sale_type_items,
			"com_sale_type"				=> $com_sale_type,
			"company"					=> (array)$a_company
		];
		return view(Config::get('shop.head.view') . '/standard/std02_show', $values);
	}

	/*
		Function: ViewCategory
		업체 카테고리 검색
	*/

	public function getdisplaycategory($com_id = '')
	{

		$query = "
			select '0' as chk,a.d_cat_cd,a.full_nm
			from p_partner_category	p inner join category a on p.cat_cd = a.d_cat_cd and a.cat_type = 'DISPLAY'
			where p.cat_type = 'DISPLAY' and a.use_yn = 'Y' and p.com_id = :com_id 
			order by a.d_cat_cd
		";

		$result = DB::select($query, [
			'com_id' => $com_id
		]);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
				"page" => 0,
				"page_cnt" => 0,
				"page_total" => 0
			),
			"body" => $result
		]);
	}

	/*
		Function: ViewCategory
		업체 용도카테고리 검색
	*/

	public function getitemcategory($com_id = '')
	{

		$query = "
			select '0' as chk,a.d_cat_cd,a.full_nm
			from p_partner_category	p inner join category a on p.cat_cd = a.d_cat_cd and a.cat_type = 'ITEM'
			where p.cat_type = 'ITEM' and a.use_yn = 'Y' and p.com_id = :com_id
			order by a.d_cat_cd
		";

		$result = DB::select($query, [
			'com_id' => $com_id
		]);


		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
				"page" => 0,
				"page_cnt" => 0,
				"page_total" => 0
			),
			"body" => $result
		]);
	}

	public function checkcomid($com_id = '')
	{
		$code = 0;

		$query = "
		select count(com_id) cnt from company where com_id = :com_id ";
		$com_rs = DB::select($query, [
			'com_id' => $com_id
		]);

		$com_cnt = $com_rs[0]->cnt;


		if ($com_cnt == 0) {
			$code = 1;
		} else {
			$code = 0;
		}

		return response()->json([
			"code" => 200,
			"com_code" => $code
		]);
	}

	public function addcategory($com_id = '', Request $request)
	{
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$cat_cd	= $request->input("cat_cd");
		$cat_type = $request->input("cat_type");

		$code = 0;

		if ($cat_cd != "") {
			$query = "
				select * from category where cat_type = :cat_type and d_cat_cd like :d_cat_cd
			";
			$rows = DB::select($query, ['cat_type' => $cat_type, 'd_cat_cd' => $cat_cd . '%']);

			if(count($rows) > 1) {
				$code = 2;
			} else {
				$query = "
					insert into p_partner_category ( com_id, cat_type, cat_cd, admin_id, admin_nm,regi_date,upd_date )
					select
					'$com_id' as com_id,c.cat_type,c.d_cat_cd,'$id' as admin_id,'$name' as admin_nm,now() as regi_date, now() as upd_date
					from category c
					where cat_type = '$cat_type' and d_cat_cd like '$cat_cd%'
					and ( select count(*) from category where cat_type = c.cat_type and p_d_cat_cd = c.d_cat_cd ) = 0
					and ( select count(*) from p_partner_category where com_id = '$com_id' and cat_type = c.cat_type and cat_cd = c.d_cat_cd ) = 0
				";
				
				try {
					DB::insert($query);
					$code = 1;
				} catch (Exception $e) {
					$code = 0;
				};
			}
		}

		return response()->json([
			"code" => 200,
			"cat_code" => $code
		]);
	}

	public function DelCategory($com_id = '', Request $request)
	{
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$cat_cd	= $request->input("cat_cd");
		$cat_type = $request->input("cat_type");

		$cat_code = 0;
		$cat_code_arr = array();

		if ($cat_cd != "") {
			$cat_cds = explode(",", $cat_cd);

			//print_r($cat_cds);

			for ($i = 0; $i < count($cat_cds); $i++) {
				$code = $cat_cds[$i];
				$sql = "
					delete from p_partner_category
						where com_id='$com_id' and cat_type = '$cat_type' and cat_cd = '$code'
				";
				try {
					DB::delete($sql);
					$cat_code_arr[$i] = 200;
				} catch (Exception $e) {
					$cat_code_arr[$i] = 500;
				};
			}
		}

		if (in_array(500, $cat_code_arr)) {
			$cat_code = 0;
		} else {
			$cat_code = 1;
		}


		return response()->json([
			"code" => 200,
			"cat_code" => $cat_code
		]);
	}

	public function Command(Request $request)
	{
		$cmd				= $request->input("cmd");
		$com_id				= $request->input("com_id");
		$pwd				= $request->input("pwd");
		$change_pwd			= $request->input("change_pwd");
		$com_nm				= $request->input("com_nm");
		$use_yn				= strtoupper($request->input("use_yn", ""));
		$com_type			= $request->input("com_type");
		$md_nm				= $request->input("md_nm");
		$settle_nm			= $request->input("settle_nm");
		$pay_fee			= $request->input("pay_fee");
		$mall_fee			= $request->input("mall_fee");
		$ceo				= $request->input("ceo");
		$name				= $request->input("name");
		$jumin_num			= $request->input("jumin_num");
		$biz_num			= $request->input("biz_num");
		$homepage			= $request->input("homepage");
		$bank				= $request->input("bank");
		$account			= $request->input("account");
		$uptae				= $request->input("uptae");
		$upjong				= $request->input("upjong");
		$dipositor			= $request->input("dipositor");
		$pay_day			= $request->input("pay_day");
		$zip_code			= $request->input("zip_code");
		$addr1				= $request->input("addr1");
		$addr2				= $request->input("addr2");
		$staff_nm1			= $request->input("staff_nm1");
		$staff_email1		= $request->input("staff_email1");
		$staff_phone1		= $request->input("staff_phone1");
		$staff_hp1			= $request->input("staff_hp1");
		$staff_nm2			= $request->input("staff_nm2");
		$staff_email2		= $request->input("staff_email2");
		$staff_phone2		= $request->input("staff_phone2");
		$staff_hp2			= $request->input("staff_hp2");
		$baesong_kind		= $request->input("baesong_kind");
		$baesong_info		= $request->input("baesong_info");
		$coupon_ratio		= $request->input("coupon_ratio");
		$margin_type		= $request->input("margin_type");
		$r_zip_code			= $request->input("r_zip_code");
		$r_addr1			= $request->input("r_addr1");
		$r_addr2			= $request->input("r_addr2");
		$memo				= $request->input("memo");
		$dlv_policy			= $request->input("dlv_policy");

		$dlv_amt			= $request->input("dlv_amt");
		$free_dlv_amt_limit	= $request->input("free_dlv_amt_limit");

		$dlv_day			= $request->input("dlv_day");
		$biz_type			= $request->input("biz_type");
		$mail_order_nm		= $request->input("mail_order_nm");
		$cs_nm				= $request->input("cs_nm");
		$cs_email			= $request->input("cs_email");
		$cs_phone			= $request->input("cs_phone");
		$cs_hp				= $request->input("cs_hp");
		$cs_yn				= strtoupper($request->input("cs_yn", ""));
		$price_yn			= strtoupper($request->input("price_yn", ""));
		$api_yn				= strtoupper($request->input("api_yn", ""));
		$api_key			= "";

		$sell_type			= $request->input("sell_type", 0);
		$dp_yn				= strtoupper($request->input("dp_yn", ""));
		$store_type			= $request->input("store_type");
		$store_nm			= $request->input("store_nm");
		$store_branch		= $request->input("store_branch");
		$store_area			= $request->input("store_area");
		$store_kind			= $request->input("store_kind");
		$com_site			= $request->input("com_site");
		$sale_type			= $request->input("sale_type");
		// $com_sale_type		= $request->input("com_sale_type");
		$site_yn			= strtoupper($request->input("site_yn", "N"));
		$result_code		= 0;

		if (!is_numeric($dlv_amt)) {
			$dlv_amt = 0;
		}
		if (!is_numeric($free_dlv_amt_limit)) {
			$free_dlv_amt_limit = 0;
		}

		//판매구분값 처리
		$sum_sell_type = 0;
		if ($sell_type != "0") {
			$a_sell_type = explode(",", $sell_type);
			for ($i = 0; $i < count($a_sell_type); $i++) {
				$sum_sell_type += $a_sell_type[$i];
			}
		}

		if ($cmd == "addcmd") {

			$query = "
			select count(com_id) cnt from company where com_id = :com_id ";
			$com_rs = DB::select($query, [
				'com_id' => $com_id
			]);

			$com_cnt = $com_rs[0]->cnt;
			if ($com_cnt == 0) {

				if ($api_yn == "Y" && $com_type == "2") {
					$api_key = $this->GetNewAPIKey($com_id);
				}


				$query = "
					insert into company (
						com_id, com_type, sale_type, site_yn, com_nm, pwd,ceo, name, biz_num, jumin_num, uptae, upjong, pay_day,
						zip_code, addr1, addr2, bank, account, dipositor, staff_nm1, staff_email1, staff_phone1, staff_hp1,
						staff_nm2, staff_email2, staff_phone2, staff_hp2, homepage, md_nm, settle_nm, pay_fee, mall_fee, use_yn,
						regi_date, update_date, last_login_date, baesong_kind, baesong_info, coupon_ratio, margin_type, r_zip_code, r_addr1, r_addr2, memo,
						dlv_policy, dlv_amt, free_dlv_amt_limit,
						dlv_day, biz_type, mail_order_nm, cs_nm, cs_email, cs_phone, cs_hp, cs_yn, price_yn, api_yn, api_key,
						sell_type, dp_yn, store_type, store_nm, store_branch, store_area, store_kind
					) values (
						'$com_id','$com_type','$sale_type','$site_yn','$com_nm','$pwd','$ceo','$name','$biz_num','$jumin_num','$uptae','$upjong','$pay_day',
						'$zip_code','$addr1','$addr2','$bank','$account','$dipositor','$staff_nm1','$staff_email1','$staff_phone1','$staff_hp1',
						'$staff_nm2','$staff_email2','$staff_phone2','$staff_hp2','$homepage','$md_nm','$settle_nm','$pay_fee','$mall_fee','$use_yn',
						now(),now(),null,'$baesong_kind','$baesong_info','$coupon_ratio','$margin_type', '$r_zip_code', '$r_addr1', '$r_addr2','$memo',
						'$dlv_policy','$dlv_amt','$free_dlv_amt_limit',
						'$dlv_day','$biz_type','$mail_order_nm','$cs_nm','$cs_email','$cs_phone','$cs_hp','$cs_yn','$price_yn','$api_yn','$api_key',
						'$sum_sell_type', '$dp_yn', '$store_type', '$store_nm', '$store_branch', '$store_area','$store_kind'
					)
				";

				try {
					DB::insert($query);
					$result_code = 1;
				} catch (Exception $e) {
					$result_code = 0;
				};

				//업체 사이트 처리
				$this->AddComSite($com_id, $com_site);
			} else {
				$result_code = -1;
			}
		} else if ($cmd == "editcmd") {
			$sql_pwd = "";
			if ($change_pwd == "Y") {
				$sql_pwd = " pwd = '$pwd',  ";
			}

			$sql = "
				update company set
					com_type = '$com_type',
					sale_type = '$sale_type',
					site_yn = '$site_yn',
					com_nm = '$com_nm',
					$sql_pwd
					ceo = '$ceo',
					name = '$name',
					biz_num = '$biz_num',
					jumin_num = '$jumin_num',
					uptae = '$uptae',
					upjong = '$upjong',
					pay_day = '$pay_day',
					zip_code = '$zip_code',
					addr1 = '$addr1',
					addr2 = '$addr2',
					bank = '$bank',
					account = '$account',
					dipositor = '$dipositor',
					staff_nm1 = '$staff_nm1',
					staff_email1 = '$staff_email1',
					staff_phone1 = '$staff_phone1',
					staff_hp1 = '$staff_hp1',
					staff_nm2 = '$staff_nm2',
					staff_email2 = '$staff_email2',
					staff_phone2 = '$staff_phone2',
					staff_hp2 = '$staff_hp2',
					homepage = '$homepage',
					md_nm = '$md_nm',
					settle_nm = '$settle_nm',
					pay_fee = '$pay_fee',
					mall_fee = '$mall_fee',
					use_yn = '$use_yn',
					update_date= now(),
					baesong_kind = '$baesong_kind',
					baesong_info = '$baesong_info',
					coupon_ratio = '$coupon_ratio',
					margin_type = '$margin_type',
					r_zip_code = '$r_zip_code',
					r_addr1 = '$r_addr1',
					r_addr2 = '$r_addr2',
					memo = '$memo',
					dlv_policy = '$dlv_policy',
					dlv_amt = '$dlv_amt',
					free_dlv_amt_limit = '$free_dlv_amt_limit',
					dlv_day = '$dlv_day',
					biz_type = '$biz_type',
					mail_order_nm = '$mail_order_nm',
					cs_nm = '$cs_nm',
					cs_email = '$cs_email',
					cs_phone = '$cs_phone',
					cs_hp = '$cs_hp',
					cs_yn = '$cs_yn',
					price_yn = '$price_yn',
					api_yn = '$api_yn',
					sell_type = '$sum_sell_type',
					dp_yn = '$dp_yn',
					store_type = '$store_type',
					store_nm = '$store_nm',
					store_branch = '$store_branch',
					store_area = '$store_area',
					store_kind = '$store_kind'
				where com_id='$com_id'
			";

			try {
				DB::update($sql);
				$result_code = 1;
			} catch (Exception $e) {
				$result_code = 0;
			}

			//업체 사이트 처리
			$this->AddComSite($com_id, $com_site);
		} else if ($cmd == "delcmd") {
			$sql = "
				update company set
					use_yn = 'N',
					update_date= now()
				where com_id='$com_id'
			";

			try {
				DB::update($sql);
				$result_code = 1;
			} catch (Exception $e) {
				$result_code = 0;
			}
		}


		//return response()->json(null, 204);
		return response()->json([
			"code" => 200,
			"result_code" => $result_code
		]);
	}


	public function GetNewAPIKey($id)
	{

		global $SITE_PKEY;

		$api_key = md5($id . time() . $SITE_PKEY);

		return $api_key;
	}


	/*
		Function: AddComSite
		업체 사이트 추가
	*/

	function AddComSite($com_id, $com_site)
	{
		$cs_result = 500;

		if ($com_id != "") {
			//기존에 등록되어 있던 사이트값 삭제
			$sql = "
				delete from company_site where com_id = '$com_id'
			";
			try {
				DB::delete($sql);
				$cs_result = 200;
			} catch (Exception $e) {
				$cs_result = 500;
			}

			if ($com_site != "") {
				$a_com_site = explode(',', $com_site);

				for ($i = 0; $i < count($a_com_site); $i++) {
					if (isset($a_com_site[$i]) && $a_com_site[$i] != "") {
						$site = $a_com_site[$i];

						$sql = "
							insert into company_site (
								com_id, site, rt, ut
							) values(
								'$com_id', '$site', now(), now()
							)
						";
						try {
							DB::insert($sql);
							$cs_result = 200;
						} catch (Exception $e) {
							$cs_result = 500;
						}
					}
				}
			}
		}
	}
}
