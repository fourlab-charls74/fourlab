<?php

namespace App\Http\Controllers\store\standard;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class std03Controller extends Controller
{
	public function index()
	{
		$com_types = SLib::getCodes("G_COM_TYPE");
		$values = [ "com_types" => $com_types ];
		return view(Config::get('shop.store.view') . '/standard/std03', $values);
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

		$query = /** @lang text */
            "
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

}
