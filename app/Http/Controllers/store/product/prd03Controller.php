<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class prd03Controller extends Controller
{

	public function index() 
	{

		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		$event_cds	= [];
		//판매유형
		$sell_types	= [];
		$code_kinds	= [];

		$conf = new Conf();
		$domain		= $conf->getConfigValue("shop", "domain");

		$values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
			'domain'		=> $domain,
			'style_no'		=> "",
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'types' => SLib::getCodes("PRD_MATERIAL_TYPE"),
			// 'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
		];

		return view( Config::get('shop.store.view') . '/product/prd03',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$goods_stat	= $request->input("goods_stat");
		$style_no	= $request->input("style_no");
		$goods_no	= $request->input("goods_no");
		$goods_nos	= $request->input('goods_nos', '');       // 상품번호 textarea
		$item		= $request->input("item");
		$brand_nm	= $request->input("brand_nm");
		$brand_cd	= $request->input("brand_cd");
		$goods_nm	= $request->input("goods_nm");
		$goods_nm_eng	= $request->input("goods_nm_eng");

		$prd_cd		= $request->input("prd_cd", "");
		$com_id		= $request->input("com_cd");

		$head_desc	= $request->input("head_desc");
		$ad_desc	= $request->input("ad_desc");

		$is_unlimited	= $request->input("is_unlimited");
		$limit		= $request->input("limit",100);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','g.goods_no');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where		= "";
		if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or s.prd_cd = '" . Lib::quote($cd) . "' ";
			}
			$where .= ")";
		}
		if($style_no != "")		$where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
		if($item != "")			$where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
		if($brand_cd != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		} else if ($brand_cd == "" && $brand_nm != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		}
		if($goods_nm != "")		$where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if($goods_nm_eng != "")	$where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";
		if($is_unlimited != "")	$where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";

		if($com_id != "")		$where .= " and g.com_id = '" . Lib::quote($com_id) . "'";

		if($head_desc != "")	$where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
		if($ad_desc != "")		$where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

		if( is_array($goods_stat)) {
			if (count($goods_stat) == 1 && $goods_stat[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
			} else if (count($goods_stat) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
			}
		} else if($goods_stat != ""){
			$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
		}

		if($goods_nos != ""){
			$goods_no	= $goods_nos;
		}
		$goods_no	= preg_replace("/\s/",",",$goods_no);
		$goods_no	= preg_replace("/\t/",",",$goods_no);
		$goods_no	= preg_replace("/\n/",",",$goods_no);
		$goods_no	= preg_replace("/,,/",",",$goods_no);

		if( $goods_no != "" ){
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
			}
		}

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

		if($page == 1) {
			$query	= /** @lang text */
				"
				select count(*) as total
				from goods g inner join product_stock s on g.goods_no = s.goods_no 
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
				where 1=1 
					-- g.com_id = :com_id 
					$where
			";
			//$row = DB::select($query,['com_id' => $com_id]);
			$row	= DB::select($query);
			$total	= $row[0]->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$goods_img_url		= '';
		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$query = /** @lang text */
		"
			select
				'' as blank
				, g.goods_no , g.goods_sub
				, ifnull( type.code_val, 'N/A') as goods_type
				, com.com_nm
				, opt.opt_kind_nm
				, brand.brand_nm
				, cat.full_nm
				, g.style_no
				, g.head_desc
				, '' as img_view
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				  )) as img
				, g.goods_nm
				, g.goods_nm_eng
				, g.ad_desc
				, stat.code_val as sale_stat_cl
				-- , g.normal_price
				,g.goods_sh
				, g.price
				-- , ifnull(
				--	(select sum(wqty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				--  ) as wqty
				, s.wqty
				, (s.qty - s.wqty) as sqty
				, g.wonga
				, (100/(g.price/(g.price-g.wonga))) as margin_rate
				, (g.price-g.wonga) as margin_amt
				, g.md_nm
				, bi.code_val as baesong_info
				, bk.code_val as baesong_kind
				, dpt.code_val as dlv_pay_type
				, g.baesong_price
				, g.point
				, g.org_nm
				, g.make
				, g.type
				, g.reg_dm
				, g.upd_dm
				, g.goods_location
				, g.sale_price
				, g.goods_type as goods_type_cd
				, com.com_type as com_type_d
				, s.prd_cd , s.goods_opt
			from goods g inner join product_stock s on g.goods_no = s.goods_no
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = g.com_id
				left outer join brand brand on brand.brand = g.brand
				left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
				left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
				left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
			where 1 = 1
				$where
			$orderby
			$limit
		";
		// dd($query);
		//$result = DB::select($query,['com_id' => $com_id]);
		$pdo	= DB::connection()->getPdo();
		$stmt	= $pdo->prepare($query);
		$stmt->execute();
		$result	= [];
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row["img"] != ""){
				$row["img"] = sprintf("%s%s",config("shop.image_svr"), $row["img"]);
			}

			$result[] = $row;
		}

		//echo "<pre>$query</pre>";
		//dd(array_keys ((array)$result[0]));
		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body"	=> $result
		]);
	}

	public function showCreate(Request $request)
	{
		$sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')
			->select('com_id', 'com_nm')->get()->all(); // 공급업체 리스트
		$values = [
			'types' => SLib::getCodes("PRD_MATERIAL_TYPE"),
			'brand' => SLib::getCodes("PRD_CD_BRAND"),
			'years'	=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons' => SLib::getCodes("PRD_CD_SEASON"),
			'genders' => SLib::getCodes("PRD_CD_GENDER"),
			'items'	=> SLib::getCodes("PRD_CD_ITEM"),
			'opts' => SLib::getCodes("PRD_CD_OPT"),
			'colors' => SLib::getCodes("PRD_CD_COLOR"),
			'sizes'	=> SLib::getCodes("PRD_CD_SIZE_MATCH"),
			'years'	=> SLib::getCodes("PRD_CD_YEAR"),
			'sup_coms' => $sup_coms,
			'units' => SLib::getCodes("PRD_CD_UNIT"),
			'images' => []
		];
		return view( Config::get('shop.store.view') . '/product/prd03_create',$values);
	}

	public function create(Request $request){
		$admin_id = Auth('head')->user()->id;
        $data = $request->input("data");

		try {

			DB::beginTransaction();

			foreach($data as $row) {

				$type = $row['type'];

				$brand = $row['brand'];
				$year = $row['year'];
				$season	= $row['season'];
				$gender	= $row['gender'];
				$item = $row['item'];
				$seq = $row['seq'];
				$opt = $row['opt'];
				$color = $row['color'];
				$size = $row['size'];

				$sup_com = $row['sup_com'];
				$unit = $row['unit'];
				$year = $row['year'];
				
				$prd_nm	= $row['prd_nm'];
				$prd_cd	= $brand . $year . $season . $gender . $item . $seq . $opt;

				$goods_no = 0; // 마지막 goods 에서 1 더해주면 되는지
				$goods_opt = ""; // 만들면 됨..
				
				DB::table('product')->insert(
					[
						'prd_cd' => $prd_cd,
						'prd_nm' => $prd_nm,
						'type' => $type,
						'com_id' => $sup_com,
						'unit' => $unit,
						'rt' => now(),
						'ut' => now(),
						'admin_id' => $admin_id
					]
				);

				/**
				 * 원부자재 상품 이미지 저장 (단일 이미지)
				 */
				$base64_src = $row['image'];
				$save_path = "/images/prd03";
				$img_url = ULib::uploadBase64img($save_path, $base64_src);
	
				DB::table('product_code')->insert(
					[
						'prd_cd' => $prd_cd,
						'seq' => $seq,
						'img_url' => $img_url,
						'goods_no' => $goods_no,
						'goods_opt'	=> $goods_opt,
						'brand' => $brand,
						'year' => $year,
						'season' => $season,
						'gender' => $gender,
						'item' => $item,
						'opt' => $opt,
						'color' => $color,
						'size' => $size,
						'type' => $type,
						'rt' => now(),
						'ut' => now(),
						'admin_id'	=> $admin_id
					]
				);
				
				DB::table('product_image')->insert(
					[
						'prd_cd' => $prd_cd,
						'seq' => $seq,
						'img_url' => $img_url,
						'rt' => now(),
						'ut' => now(),
						'admin_id'	=> $admin_id
					]
				);
			}

			DB::commit();
			$code = 200;

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			// $msg = $e->getMessage();
		}

		return response()->json(["code" => $code]);
	}
	
}
