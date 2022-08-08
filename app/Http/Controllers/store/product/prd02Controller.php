<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class prd02Controller extends Controller
{

	//
	public function index() {

		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		$com_types	= [];
		$event_cds	= [];
		//판매유형
		$sell_types	= [];
		$code_kinds	= [];

		$conf = new Conf();

		$domain		= $conf->getConfigValue("shop", "domain");


		$values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
			'domain'		=> $domain,
			'style_no'		=> "",
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
		];

		return view( Config::get('shop.store.view') . '/product/prd02',$values);
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
		$cat_type	= $request->input("cat_type");
		$cat_cd		= $request->input("cat_cd");
		$is_unlimited	= $request->input("is_unlimited");

		$prd_cd		= $request->input("prd_cd");
		$com_id		= $request->input("com_cd");

		$head_desc	= $request->input("head_desc");
		$ad_desc	= $request->input("ad_desc");

		$is_unlimited	= $request->input("is_unlimited");
		$limit		= $request->input("limit",100);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','g.goods_no');
		$type		= $request->input("type");
		$goods_type	= $request->input("goods_type");

		$sale_yn	= $request->input("sale_yn");
		$coupon_yn	= $request->input("coupon_yn");
		$sale_type	= $request->input("sale_type");

		$orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where		= "";
		
		if($prd_cd != "")		$where .= " and s.prd_cd = '" . Lib::quote($prd_cd) . "' ";
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

		if($cat_cd != ""){
			if($cat_type === "DISPLAY"){
				$where .= " and g.rep_cat_cd = '". Lib::quote($cat_cd) . "' ";
			} else if($cat_type === "ITEM"){
				$where .= " and ( select count(*) from category_goods where cat_type = 'ITEM' and d_cat_cd = '". Lib::quote($cat_cd) . "' and goods_no = g.goods_no ) > 0 ";
			}
		}

		if($head_desc != "")	$where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
		if($ad_desc != "")		$where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

		if($is_unlimited != "")	$where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";

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

		if($type != "")			$where .= " and g.type = '" . Lib::quote($type) . "' ";
		if($goods_type != "")	$where .= " and g.goods_type = '" . Lib::quote($goods_type) . "' ";

		if( $sale_yn != "" )	$where .= " and g.sale_yn = '$sale_yn' ";
		if( $coupon_yn != "" )	$where .= " and gc.price > 0 ";
		if( $sale_type != "" )	$where .= " and g.sale_type = '" . Lib::quote($sale_type) . "' ";

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
				$row["img"] = sprintf("%s%s",config("shop.image_svr"),$row["img"]);
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

	public function create(Request $request)
	{
		$sql	= " select brand_nm, br_cd from brand where use_yn = 'Y' and br_cd <> '' ";
		$brands	= DB::select($sql);
		
		$values = [
			'brands'	=> $brands,
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons'	=> SLib::getCodes("PRD_CD_SEASON"),
			'genders'	=> SLib::getCodes("PRD_CD_GENDER"),
			'items'		=> SLib::getCodes("PRD_CD_ITEM"),
			'opts'		=> SLib::getCodes("PRD_CD_OPT"),
		];

		return view( Config::get('shop.store.view') . '/product/prd02_create',$values);
	}

	public function prd_search(Request $request){
		$brand		= $request->input('brand');
		$year		= $request->input('year');
		$season		= $request->input('season');
		$gender		= $request->input('gender');
		$item		= $request->input('item');
		$opt		= $request->input('opt');
		$goods_no	= $request->input('goods_no');
		$goods_sub	= 0;
		$prd_cd1	= "";
		$seq		= "01";
		$prd_yn		= "N";
		$chk_prd_cd	= "";

		$sql	= "
			select
				a.goods_no, b.style_no, b.goods_nm, a.goods_opt, '' as prd_cd1, '' as color, '' as size, '' as match_yn,
				'$brand' as brand, '$year' as year, '$season' as season, '$gender' as gender, '$item' as item, '$opt' as opt,
				'' as seq
			from goods_summary a
			inner join goods b on a.goods_no = b.goods_no and b.goods_sub = 0
			where
				a.goods_no = :goods_no1 and a.goods_sub = :goods_sub

			union all

			select
				a.goods_no, b.style_no, b.goods_nm, c.goods_opt, concat(a.brand, a.year, a.season, a.gender, a.item, a.seq, a.opt) as prd_cd1, a.color, a.size, 'Y' as match_yn,
				a.brand, a.year, a.season, a.gender, a.item, a.opt, a.seq
			from product_code a
			inner join goods b on a.goods_no = b.goods_no and b.goods_sub = 0
			inner join product_stock c on a.prd_cd = c.prd_cd
			where
				a.goods_no = :goods_no2
		";

		$result = DB::select($sql,['goods_no1' => $goods_no, 'goods_sub' => $goods_sub, 'goods_no2' => $goods_no]);

		foreach($result as $row){

			if( $row->match_yn == "Y" ){
			}else{
				$sql_sub	= " 
					select ifnull(max(seq),'00') as seq
					from product_code 
					where 
						brand	= :brand
						and year	= :year
						and season	= :season
						and item	= :item
						and opt		= :opt
				";
				$result_sub	= DB::select($sql_sub,['brand' => $brand, 'year' => $year, 'season' => $season, 'item' => $item, 'opt' => $opt]);
				$seq = $result_sub[0]->seq + 1;
	
				if(strlen($seq) == "1")	$seq = "0" . $seq;
		
				$row->seq	= $seq;

				$goods_opt	= explode('^', $row->goods_opt);
				$color		= strtolower(str_replace(" ", "", $goods_opt[0]));
				$size		= isset($goods_opt[1]) ? $goods_opt[1] : "";

				$sql		= " select code_id as color_cd from code where	code_kind_cd = 'PRD_CD_COLOR' and LOWER(replace(code_val,' ','')) = :color limit 1 ";
				$color_cd	= DB::selectOne($sql, ["color" => $color])->color_cd;

				if( $size != "" ){
					$size		= strtolower(str_replace(" ", "", $goods_opt[1]));

					$sql		= " select code_val as size_cd from code where	code_kind_cd = 'PRD_CD_SIZE_MATCH' and LOWER(replace(code_val2,' ','')) = :size limit 1 ";
					$size_cd	= DB::selectOne($sql, ["size" => $size])->size_cd;
				}
				
				$prd_cd1		= $brand . $year . $season . $gender . $item . $seq . $opt;

				$row->prd_cd1	= $prd_cd1;
				$row->color		= isset($color_cd) ? $color_cd : "";
				$row->size		= isset($size_cd) ? $size_cd : "";
			}
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body" => $result
		]);

	}

	public function add_product_code(Request $request){
		$admin_id	= Auth('head')->user()->id;
        $datas		= $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($datas as $data) {

				$prd_cd1	= $data['prd_cd1'];
				$goods_no	= $data['goods_no'];
				$brand		= $data['brand'];
				$year		= $data['year'];
				$season		= $data['season'];
				$gender		= $data['gender'];
				$item		= $data['item'];
				$opt		= $data['opt'];
				$seq		= $data['seq'];
				$color		= $data['color'];
				$size		= $data['size'];
				$goods_opt	= $data['goods_opt'];

				$prd_cd		= $prd_cd1 . $color . $size;

				DB::table('product_code')
					->insert([
						'prd_cd'	=> $prd_cd,
						'goods_no'	=> $goods_no,
						'brand'		=> $brand,
						'year'		=> $year,
						'season'	=> $season,
						'gender'	=> $gender,
						'item'		=> $item,
						'opt'		=> $opt,
						'seq'		=> $seq,
						'color'		=> $color,
						'size'		=> $size,
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $admin_id
					]);

				DB::table('product_stock')
					->insert([
						'goods_no'	=> $goods_no,
						'prd_cd'	=> $prd_cd,
						'in_qty'	=> 0,
						'out_qty'	=> 0,
						'qty'		=> 0,
						'wqty'		=> 0,
						'goods_opt'	=> $goods_opt,
						'barcode'	=> $prd_cd,
						'use_yn'	=> 'Y',
						'rt'		=> now(),
						'ut'		=> now()
					]);
            }

			DB::commit();
			$code = 200;
			$msg = "상품코드 등록이 완료되었습니다.";

		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function edit_goods_no($product_code, $goods_no, Request $request){

		$sql	= "
			select
				brand, year, season, gender, item, opt, seq, color, size
			from product_code
			where
				prd_cd = :prd_cd and goods_no = :goods_no
		";
		$product	= DB::selectOne($sql,['prd_cd' => $product_code, 'goods_no' => $goods_no]);

		$sql	= " select brand_nm, br_cd from brand where use_yn = 'Y' and br_cd <> '' ";
		$brands	= DB::select($sql);
		
		$values = [
			'brands'	=> $brands,
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons'	=> SLib::getCodes("PRD_CD_SEASON"),
			'genders'	=> SLib::getCodes("PRD_CD_GENDER"),
			'items'		=> SLib::getCodes("PRD_CD_ITEM"),
			'opts'		=> SLib::getCodes("PRD_CD_OPT"),
			'product_code'	=> $product_code,
			'goods_no'	=> $goods_no,
			'product'	=> $product
		];

		return view( Config::get('shop.store.view') . '/product/prd02_edit',$values);

	}

}
