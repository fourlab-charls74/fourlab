<?php

namespace App\Http\Controllers\head\order;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ord51Controller extends Controller
{
	public function index()
	{

		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$ad_types = SLib::getCodes("G_AD_TYPE");
		$com_types = SLib::getCodes("G_COM_TYPE");
		$query = "select com_id as id, com_nm as val from company where com_type = '4' order by com_nm";
		$sale_place_list = DB::select($query);

		$today = date("Y-m-d");
		$edate = $today;
		$sdate = date('Y-m-d', strtotime(-7 . 'days'));

		$values = [
			"admin_id" => $id,
			"admin_nm" => $name,
			"edate" => $edate,
			"sdate" => $sdate,
			"ad_types" => $ad_types,
			"com_types" => $com_types,
			"sale_place_list" => $sale_place_list
		];
		return view(Config::get('shop.head.view') . '/order/ord51', $values);
	}

	public function search(Request $request)
	{
		$sdate			= str_replace("-", "", $request->input("sdate"));
		$edate			= str_replace("-", "", $request->input("edate"));
		$ad_type		= $request->input("ad_type");
		$ad				= $request->input("ad");
		$goods_nm		= $request->input("goods_nm");
		$brand_cd		= $request->input("brand_cd");
		$brand_nm		= $request->input("brand_nm");
		$com_type		= $request->input("com_type");
		$com_nm			= $request->input("com_nm");
		$com_id			= $request->input("com_id");
		$ord_field		= $request->input("ord_field");
		$ord			= $request->input("ord");
		$limit			= $request->input("limit");
		$referer		= $request->input("referer");
		$keyword		= $request->input("keyword");
		$sale_place		= $request->input("sale_place");

		$page			= $request->input("page", 1);
		$page_size		= $limit;

		if ($page < 1 or $page == "") $page = 1;

        $total		= 0;
        $page_cnt	= 0;

		$where = "";
		if ($sdate != "") $where .= "and o.ord_date >= '$sdate' ";
		if ($edate != "") $where .= "and o.ord_date < date_add('$edate',interval 1 day) ";
		if ($ad_type != "") $where .= "and a.type = '$ad_type'";
		if ($ad != "") $where .= "and t.ad = '$ad'";
		if ($goods_nm != "") $where .= "and o.goods_nm like '%$goods_nm%' ";
		if ($com_id != "") $where .= "and o.com_id = '$com_id'";
		if ($brand_cd != "") $where .= "and g.brand = '$brand_cd'";
		if ($com_type != "") $where .= "and g.com_type = '$com_type'";
		if ($com_id != "") $where .= "and g.com_id = '$com_id'";
		if ($referer != "") $where .= "and t.referer like '%$referer%'";
		if ($keyword != "") $where .= "and t.kw like '%$keyword%'";
		if ($sale_place != "") $where .= "and o.sale_place = '$sale_place' ";

		$page_size = $limit;

		if( $page == 1 ) 
		{
			$sql	= "
				select
					count(*) as total
				from order_opt o
					inner join order_mst m on m.ord_no = o.ord_no
					inner join order_track t on o.ord_no = t.ord_no
					inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
				where 1=1 $where
			";
			$row = DB::select($sql);
			$total = $row[0]->total;

			// 페이지 얻기
			$page_cnt=(int)(($total - 1)/$page_size) + 1;
            $startno = ($page - 1) * $page_size;
			//$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page, "page_total"=>count);
		} else {
			$startno = ($page - 1) * $page_size;
			//$arr_header = null;
		}

		if ($limit == -1) {
			$sql_limit = "";
		} else {
			$sql_limit = " limit $startno, $page_size ";
		}


		$sql = "
			select
				date_format(m.ord_date, '%Y.%m.%d %H:%i:%s') ord_date, m.ord_no, o.ord_opt_no,
				o.goods_nm, o.goods_opt, o.qty, o.price, c.code_val as ord_state, d.code_val as clm_state,
				e.code_val as ad_type,a.name,t.se,ifnull(f.code_val,t.type) as site_type,t.kw,
				o.referrer as track,t.vt as vt, t.vc as vc,datediff(t.rt,t.lvd) as diff,
				t.pageview, t.referer, o.goods_no, o.goods_sub, a.ad
			from order_track t
				left outer join ad a on a.ad= t.ad
				inner join order_mst m on m.ord_no = t.ord_no
				inner join order_opt o on o.ord_no = t.ord_no
				inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
				left outer join code c on c.code_id = o.ord_state and c.code_kind_cd = 'G_ORD_STATE'
				left outer join code d on d.code_id = o.clm_state and d.code_kind_cd = 'G_CLM_STATE'
				left outer join code e on e.code_id = a.type and e.code_kind_cd = 'G_AD_TYPE'
				left outer join code f on f.code_id = t.type and f.code_kind_cd = 'G_SITE_TYPE'
			where 1=1 $where
			order by $ord_field $ord
			$sql_limit
		";

		//echo $sql;



		$result = DB::select($sql);
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => $page_size
			),
			"body" => $result
		]);
	}
}
