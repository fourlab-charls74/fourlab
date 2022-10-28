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

class prd02Controller extends Controller
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
			// 'com_types'     => SLib::getCodes('G_COM_TYPE'),
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

		// $color_size = substr($prd_cd, 0, 11);
		
		
		// $color = substr($color_size, 0, 2);// 컬러
		// $size = substr($color_size, 2, strlen($color_size));//사이즈

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


	public function prd_search_code(Request $request){
		$prd_cd 	= $request->input('prd_cd');
		$goods_no 	= $request->input('goods_no2');
		
		
		$query = "
			select
				seq,color,size
			from product_code
			where prd_cd = '$prd_cd'
		";
		$res = DB::selectOne($query);

		$color = $res->color;
		$size = $res->size;

		$seq = $res->seq;


		$color_sql = "
			select * from code where code_kind_cd = 'PRD_CD_COLOR' and code_id = '$color'
		";
		$color_val = DB::selectOne($color_sql);

		$size_sql = "
			select * from code where code_kind_cd = 'PRD_CD_SIZE_MATCH' and code_id = '$size'
		";
		$size_val = DB::selectOne($size_sql);


		$goods_opt = $color_val->code_val.'^'.$size_val->code_val2;


		$sql = "
			select 
				distinct(p.prd_cd), g.goods_nm as prd_nm, g.style_no, p.goods_no, p.goods_opt, p.color, p.size, '' as seq, '' as yn, 'N' as is_product
			from product_code p
				inner join goods_summary gs on p.goods_no = gs.goods_no
				inner join goods g on g.goods_no = p.goods_no 
			where p.goods_no = '$goods_no'

			union all

			select 
				prd_cd, prd_nm , style_no ,'$goods_no' as goods_no, '$goods_opt' as goods_opt, '$color' as color, '$size' as size, '$seq' as seq, '' as yn, 'Y' as is_product
			from product
			where prd_cd like '$prd_cd%'
			order by seq desc

		";

	// 	$sql = "
	// 	select 
	// 		distinct(p.prd_cd), g.goods_nm as prd_nm, g.style_no, p.goods_no, p.goods_opt, p.color, p.size, '' as seq, '' as yn
	// 	from product_code p
	// 		inner join goods_summary gs on p.goods_no = gs.goods_no
	// 		inner join goods g on g.goods_no = p.goods_no 
	// 	where p.goods_no = '$goods_no'

	// 	union all

	// 	select 
	// 		p.prd_cd, p.prd_nm , p.style_no ,'$goods_no' as goods_no, '$goods_opt' as goods_opt, '$color' as color, '$size' as size, '$seq' as seq, '' as yn
	// 	from product p 
	// 		inner join product_code pc on pc.prd_cd = p.prd_cd
	// 		inner join goods_summary g on g.goods_opt = pc.goods_opt
	// 	where p.prd_cd like '$prd_cd%'
	// 	order by seq desc

	// ";

		$result = DB::select($sql);


		$goods_opt_counts = collect($result)->groupBy('goods_opt')->map(function($row) {
            return $row->count();
        })->all();
        $result = collect($result)->map(function($row) use ($goods_opt_counts) {
            $goods_opt = $row->goods_opt;
            $count = $goods_opt_counts[$goods_opt];
            $row->checkbox = $count > 1 ? true : false;
            return $row;
        })->all();


		// $goods_opts = [];
		// foreach ($result as $row) {
		// 	array_push($goods_opts, ['goods_opt' =>$row->goods_opt]);
		// }
		// $cnt = 0;
		// foreach ($goods_opts as $g_opt) {
		// 	$g = $g_opt['goods_opt'];

		// 	if ($goods_opt == $g) {
		// 		$cnt++;
		// 	}
		// }
		// $match = "";
		// if ($cnt > 1) {
		// 	$match =  "is_match";
		// } else{
		// 	$match = "not_match";
		// }

		// array_push($result, ['match' =>$match]);

		// dd($result);




		// dd(collect($goods_opts)->groupBy('goods_opt');

		// // $result = collect($result)->map(function ($row, $idx) use ($goods_opts) {

		// // 	$duplicated = collect($goods_opts)->duplicates('goods_opt')->all();
		// // 	$row->duplicated = $duplicated;
		// // 	// $row->checked = count($duplicated) > 0 ? true : false;
		// // 	return $row;
		// // });

		// // dd($result);


		// // $i = 0;
		// // foreach ($result as $row) {
		// // 	// index가 같지 않고 이름이 같으면 중복

		// // 	$goods_opt = $row->goods_opt;

		// // 	$idx = array_search($goods_opt, $goods_opts);

		// // 	dd($idx);

		// 	// while (true) {
		// 	// 	if ($idx == array_search($goods_opt, $goods_opts)) {
		// 	// 		continue;
		// 	// 	} else {
		// 	// 		$idx = $i;
		// 	// 		break;
		// 	// 	}
		// 	// }

		// 	// 0번째  goods_opt가 opts 안에 2개 이상 있으면 중복됨. 따라서
		// 	// checked = true
		// 	$i++;
		// }

		// dd($result);
		// // 상품 옵션이 중복되는 경우 체크박스 표시 true 아닌 경우 false
		// // 



		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total" => count($result),
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
				$goods_nm	= $data['goods_nm'];
				$style_no	= $data['style_no'];
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
						'goods_opt'	=> $goods_opt,
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
						'qty_wonga'	=> 0,
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
				
				DB::table('product')
					->insert([
						'prd_cd' 	=> $prd_cd,
						'prd_nm' 	=> $goods_nm,
						'style_no'	=> $style_no,
						'tag_price'	=> 0,
						'price'		=> 0,
						'wonga'		=> 0,
						'match_yn' 	=> 'Y',
						'use_yn' 	=> 'Y',
						'rt' 		=> now(),
						'ut' 		=> now(),
						'admin_id' 	=> $admin_id
					]);
            }
				
			DB::commit();
			$code = 200;
			$msg = "상품코드 등록이 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}


	public function add_product_product(Request $request){
		$admin_id	= Auth('head')->user()->id;
        $datas		= $request->input("data", []);
		$now		= now();

        try {
            DB::beginTransaction();

			foreach($datas as $data) {

				$prd_cd	= $data['prd_cd'];
				$prd_nm = $data['prd_nm'];
				$style_no = $data['style_no'];
				$goods_no = $data['goods_no'];


				$color = $data['color'];
				$size = $data['size'];

				$color_sql = "
					select * from code where code_kind_cd = 'PRD_CD_COLOR' and code_id = '$color'
				";
				$color_val = DB::selectOne($color_sql);

				$size_sql = "
					select * from code where code_kind_cd = 'PRD_CD_SIZE_MATCH' and code_id = '$size'
				";
				$size_val = DB::selectOne($size_sql);


				$goods_opt = $color_val->code_val.'^'.$size_val->code_val2;


				$product_sql = "
					update product 
					set match_yn = 'Y', ut = 'now()'
					where prd_cd = '$prd_cd'
				";
				
				DB::update($product_sql);

				$product_code_sql = "
					update product_code
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = '$now'
					where prd_cd = '$prd_cd'
				";
				
				DB::update($product_code_sql);
				
				$product_stock_sql = "
					update product_stock 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = '$now'
					where prd_cd = '$prd_cd'
				";
				
				DB::update($product_stock_sql);

            }
				
			DB::commit();
			$code = 200;
			$msg = "상품코드 매칭이 완료되었습니다.";

		} catch (\Exception $e) {
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

	public function prd_edit_search(Request $request){
		$product_code	= $request->input('product_code');

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
		$chk_prd_cd	= "";

		$sql	= "
			select
				a.goods_no, b.style_no, b.goods_nm, a.goods_opt, '' as prd_cd1, '' as color, '' as size, if(ifnull(c.prd_cd,'') = '', '', 'Y') as match_yn,
				'$brand' as brand, '$year' as year, '$season' as season, '$gender' as gender, '$item' as item, '$opt' as opt,
				c.seq, c.prd_cd, if(ifnull(c.prd_cd,'') = '', '', '삭제') as del
			from goods_summary a
			inner join goods b on a.goods_no = b.goods_no and b.goods_sub = 0
			left outer join product_code c on c.goods_no = a.goods_no and c.goods_opt = a.goods_opt
			where
				a.goods_no = :goods_no and a.goods_sub = :goods_sub
			order by a.seq
		";

		$result = DB::select($sql,[
			'goods_no'	=> $goods_no, 
			'goods_sub'	=> $goods_sub
		]);

		foreach($result as $row){

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

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body" => $result
		]);

	}

	public function del_product_code(Request $request){

		$prd_cd		= $request->input('prd_cd');
		$goods_no	= $request->input('goods_no');

		try {
			DB::beginTransaction();

			DB::table('product_code')
				->where('prd_cd', '=', $prd_cd)
				->where('goods_no', '=', $goods_no) 
				->delete();

			DB::table('product_stock')
				->where('prd_cd', '=', $prd_cd)
				->where('goods_no', '=', $goods_no) 
				->delete();

			DB::commit();
			$code = 200;
			$msg = "상품코드 삭제가 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function batch_create(Request $request){
		
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd02_batch_create',$values);

	}

	public function upload(Request $request)
	{

        if ( 0 < $_FILES['file']['error'] ) {
            echo json_encode(array(
                "code" => 500,
                "errmsg" => 'Error: ' . $_FILES['file']['error']
            ));
        }
        else {
			$file = sprintf("data/store/prd02/%s", $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
            echo json_encode(array(
                "code" => 200,
                "file" => $file
            ));
        }

	}

	public function update(Request $request)
	{
		$admin_id		= Auth('head')->user()->id;
		$error_code		= "200";
		$result_code	= "";

        $datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" ){
			$error_code	= "400";
		}

		DB::beginTransaction();

		for( $i = 0; $i < count($datas); $i++ ){
			$data	= (array)$datas[$i];

			$cd			= $data["xmd_code"];
			$goods_no	= $data["goods_no"];
			$goods_opt	= $data["goods_opt"];

			$query	= " select count(*) as cnt from goods_xmd_imp2 where cd = :cd ";
			$rows	= DB::selectOne($query, ['cd' => $cd]);

			if( $rows->cnt == 0 ){
				$sql	= "
					insert into goods_xmd_imp2( cd,goods_no,goods_opt )
					values (  '$cd','$goods_no','$goods_opt' )
				";
				DB::insert($sql);
			}
		}

		$sql	= " update goods_xmd_imp2 set goods_opt = replace(goods_opt,' ^ ','^') ";
		DB::update( $sql);

		$sql	= " update goods_xmd_imp2 set goods_opt = replace(goods_opt,'\r','') where goods_opt like '%\r%'; ";
		DB::update( $sql);

		$sql	= "
			delete from goods_summary 
			where goods_no in (
				select goods_no from goods_xmd_imp2 group by goods_no
			)
		";
		DB::delete($sql);

		$sql	= "
			insert into goods_summary ( goods_no,goods_sub,opt_name,goods_opt,opt_price,opt_memo,good_qty,wqty,soldout_yn,use_yn, seq,rt,ut,bad_qty,last_date )
			select 
				a.goods_no, 0 as goods_sub,
				if( instr(goods_opt,'^') > 0,'컬러^사이즈','사이즈') as opt_name, 
				a.goods_opt,0 as opt_price,
				REVERSE(SUBSTR(REVERSE(a.cd),3,2)) AS opt_memo,
				0 as good_qty, 0 as wqty,'N' AS soldout_yn,'Y' AS use_yn, 0 AS seq,
				NOW() AS rt, NOW() AS ut, 0 AS bad_qty, DATE_FORMAT(NOW(),'%Y-%m-%d') AS last_date
			from ( select goods_no,goods_opt,max(cd) as cd from goods_xmd_imp2 group by goods_no,goods_opt )  a inner join goods g on a.goods_no = g.goods_no
		";
		DB::insert($sql);

		$sql	= "
			delete o.* FROM goods_option o 
			INNER JOIN (SELECT goods_no FROM goods_xmd_imp2 GROUP BY goods_no ) b ON o.goods_no = b.goods_no 
			WHERE o.type = 'basic' AND NAME IN ('사이즈','컬러^사이즈','컬러')
		";
		DB::delete($sql);

		$sql	= "
			insert into goods_option ( goods_no,goods_sub,type,name,required_yn,use_yn,seq,option_no,rt,ut )
			select 
				goods_no, goods_sub,'basic' as type,'사이즈' as name, 'Y' as required_yn, 
				'Y' as use_yn, 0 as seq, 0 as option_no, now() as rt, now() as ut
			from goods_summary 
			where goods_no in ( select goods_no from goods_xmd_imp2 group by goods_no ) and opt_name = '사이즈'
			group by goods_no, goods_sub
		";
		DB::insert($sql);

		$sql	= "
			insert into goods_option ( goods_no,goods_sub,type,name,required_yn,use_yn,seq,option_no,rt,ut )
			select 
				s.goods_no, s.goods_sub,'basic' as type,'사이즈' as name, 'Y' as required_yn, 
				'Y' as use_yn, 0 as seq, 0 as option_no, now() as rt, now() as ut
			from (
				select goods_no from goods_xmd_imp2 group by goods_no
			) a inner join goods_summary s on a.goods_no = s.goods_no
			where s.goods_no in ( select goods_no from goods_xmd_imp2 group by goods_no )
				and opt_name = '사이즈' and ( select count(*) from goods_option where goods_no = a.goods_no ) = 0
			group by s.goods_no,s.opt_name
		";
		DB::insert($sql);

		$sql	= "
			insert into goods_option ( goods_no,goods_sub,type,name,required_yn,use_yn,seq,option_no,rt,ut )
			select * from (
				select 
					s.goods_no, s.goods_sub,'basic' as type,'컬러' as name, 'Y' as required_yn, 
					'Y' as use_yn, 0 as seq, 0 as option_no, now() as rt, now() as ut
				from (
					select goods_no from goods_xmd_imp2 group by goods_no
				) a inner join goods_summary s on a.goods_no = s.goods_no
				where opt_name = '컬러^사이즈' and ( select count(*) from goods_option where goods_no = a.goods_no ) = 0
				group by s.goods_no,s.opt_name
				union 
				select 
					s.goods_no, s.goods_sub,'basic' as type,'사이즈' as name, 'Y' as required_yn, 
					'Y' as use_yn, 0 as seq, 1 as option_no, now() as rt, now() as ut
				from (
					select goods_no from goods_xmd_imp2 group by goods_no
				) a inner join goods_summary s on a.goods_no = s.goods_no
				where opt_name = '컬러^사이즈' and ( select count(*) from goods_option where goods_no = a.goods_no ) = 0
				group by s.goods_no,s.opt_name
			) a order by goods_no, goods_sub, name desc
		";
		DB::insert($sql);

		$sql	= " delete from goods_xmd where cd in ( select cd from goods_xmd_imp2 ) ";
		DB::delete($sql);

		//추후 삭제 예정
		$sql	= " insert into goods_xmd select cd,goods_no,0 as goods_sub,goods_opt,now() as rt, now() as ut from goods_xmd_imp2 ";
		DB::insert($sql);

		//#####상풍코드용 추가 작업 시작
		////
		for( $i = 0; $i < count($datas); $i++ ){
			$data	= (array)$datas[$i];

			$prd_cd		= $data["xmd_code"];
			$goods_no	= $data["goods_no"];
			$goods_opt	= $data["goods_opt"];

			$sql	= " delete from product_code where prd_cd = :prd_cd ";
			DB::delete($sql,['prd_cd' => $prd_cd]);
	
			$sql	= " delete from product_stock where prd_cd = :prd_cd ";
			DB::delete($sql,['prd_cd' => $prd_cd]);
	
			if( substr($prd_cd, 0 , 2) != "HR" ){
				$cd_cut_cnt	= "1";
	
				if( strlen($prd_cd) == 13 )	$size_cnt = 3;
				else						$size_cnt = 2;
		
				$size	= substr($prd_cd, ($cd_cut_cnt+12), $size_cnt); 
			}else{
				$cd_cut_cnt	= "2";
				$size	= substr($prd_cd, ($cd_cut_cnt+12), 2); 
			}
	
			$brand	= substr($prd_cd, 0, 1);
			$year	= substr($prd_cd, $cd_cut_cnt, 2);
			$season	= substr($prd_cd, ($cd_cut_cnt+2), 1);
			$gender	= substr($prd_cd, ($cd_cut_cnt+3), 1); 
			$item	= substr($prd_cd, ($cd_cut_cnt+4), 2); 
			$opt	= substr($prd_cd, ($cd_cut_cnt+8), 2); 
			$seq	= substr($prd_cd, ($cd_cut_cnt+6), 2); 
			$color	= substr($prd_cd, ($cd_cut_cnt+10), 2); 
	
			DB::table('product_code')
				->insert([
					'prd_cd'	=> $prd_cd,
					'goods_no'	=> $goods_no,
					'goods_opt'	=> $goods_opt,
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
					'qty_wonga'	=> 0,
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
		////	
		//#####상풍코드용 추가 작업 종료

		DB::commit();

		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);
	}

	public function product_upload(Request $request)
	{
		$sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')
			->select('com_id', 'com_nm')->get()->all(); // 공급업체 리스트

		$sql	= " select brand_nm, br_cd from brand where use_yn = 'Y' and br_cd <> '' ";
		$brands	= DB::select($sql);
		$values = [
			'brands' 	=> $brands,
			'brand' 	=> SLib::getCodes("PRD_CD_BRAND"),
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons' 	=> SLib::getCodes("PRD_CD_SEASON"),
			'genders' 	=> SLib::getCodes("PRD_CD_GENDER"),
			'items'		=> SLib::getCodes("PRD_CD_ITEM"),
			'opts' 		=> SLib::getCodes("PRD_CD_OPT"),
			'colors' 	=> SLib::getCodes("PRD_CD_COLOR"),
			'sizes'		=> SLib::getCodes("PRD_CD_SIZE_MATCH"),
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'sup_coms' 	=> $sup_coms,
			'units' 	=> SLib::getCodes("PRD_CD_UNIT"),
			'images' 	=> []
		];

		return view( Config::get('shop.store.view') . '/product/prd02_product_upload',$values);
	}

	public function save_product(Request $request){
		$admin_id = Auth('head')->user()->id;
        $data = $request->input("data");
		$sel_data = $request->input("sel_data");

		try {

			DB::beginTransaction();

			foreach($sel_data as $row) {

				$brand 		= $row['brand'];
				$year 		= $row['year'];
				$season		= $row['season'];
				$gender		= $row['gender'];
				$item 		= $row['item'];
				$opt 		= $row['opt'];
				$color 		= $row['color'];
				$size 		= $row['size'];
				$prd_nm		= $row['prd_nm'];
				$style_no 	= $row['style_no'];
				$sup_com 	= $row['sup_com'];
				
				$seq 		= $row['seq'];
				$price 		= $row['price'];
				$wonga 		= $row['wonga'];
				$tag_price 	= $row['tag_price'];

				$brand 		= explode(' : ', $brand);
				$year 		= explode(' : ', $year);
				$season 	= explode(' : ', $season);
				$gender 	= explode(' : ', $gender);
				$item 		= explode(' : ', $item);
				$opt 		= explode(' : ', $opt);
				$color 		= explode(' : ', $color);
				$size 		= explode(' : ', $size);
				$sup_com 	= explode(' : ', $sup_com);

				$unit = "";

				$prd_cd	= $row['prd_cd'].$color[0].$size[0];
				$goods_no = "";

				$sql = "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				$size_sql = "select * from code where code_kind_cd = 'PRD_CD_SIZE_MATCH' and code_id = '$size[0]'";
				$size_cd = DB::selectOne($size_sql)->code_val2;

				// $goods_opt = $color[1]."^".$size_cd;
				$goods_opt = "";
				if ($result->count == 0) {

					DB::table('product')->insert([
						'prd_cd' => $prd_cd,
						'prd_nm' => $prd_nm,
						'style_no' => $style_no,
						'price' => $price,
						'wonga' => $wonga,
						'tag_price' => $tag_price,
						'com_id' => $sup_com[0],
						'unit' => $unit,
						'rt' => now(),
						'ut' => now(),
						'admin_id' => $admin_id
					]);

					/**
					 *  상품 이미지 저장 (단일 이미지)
					 */
					$base64_src = $row['image'];
					$save_path = "/images/prd02";

					$unique_img_name = $prd_cd . $seq;

					$img_url = ULib::uploadBase64img($save_path, $base64_src, $unique_img_name);
		
					DB::table('product_code')->insert([
						'prd_cd' => $prd_cd,
						'seq' => $seq,
						'goods_no' => $goods_no,
						'goods_opt'	=> $goods_opt,
						'brand' => $brand[0],
						'year' => $year[0],
						'season' => $season[0],
						'gender' => $gender[0],
						'item' => $item[0],
						'opt' => $opt[0],
						'color' => $color[0],
						'size' => $size[0],
						'rt' => now(),
						'ut' => now(),
						'admin_id'	=> $admin_id
					]);
					
					DB::table('product_image')->insert([
						'prd_cd' => $prd_cd,
						'seq' => $seq,
						'img_url' => $img_url,
						'rt' => now(),
						'ut' => now(),
						'admin_id'	=> $admin_id
					]);

					DB::table('product_stock')->insert([
						'goods_no' => $goods_no,
						'prd_cd' => $prd_cd,
						'qty_wonga'	=> 0,
						'in_qty' => 0,
						'out_qty' => 0,
						'qty' => 0,
						'wqty' => 0,
						'goods_opt' => $goods_opt,
						'barcode' => $prd_cd,
						'use_yn' => 'Y',
						'rt' => now(),
						'ut' => now()
					]);

				} else {
					DB::rollback();
					return response()->json(["code" => -1, "prd_cd" => $prd_cd]);
				}
			}
			DB::commit();
			$code = 200;
			$msg = "성공";
		} catch (\Exception $e) {
			DB::rollback();
			$msg = $e->getMessage();
			$code = 500;
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function getSeq(Request $request) {
		$brand = $request->input('brand');
		$year = $request->input('year');
		$season = $request->input('season');
		$item = $request->input('item');
		$opt = $request->input('opt');

		$sql = " 
			select ifnull(max(seq),'00') as seq 
			from product_code 
			where 
				brand = :brand
				and year = :year
				and season = :season
				and item = :item
				and opt = :opt
		";
		$result	= DB::selectOne($sql, ['brand' => $brand, 'year' => $year, 'season' => $season, 'item' => $item, 'opt' => $opt]);
		$seq = $result->seq + 1;
		if (strlen($seq) == "1") $seq = "0" . $seq;

		return response()->json(['seq' => $seq , 'code' => 200]);
	}

	public function delImg(Request $request)
	{
		$admin_id = Auth('head')->user()->id;
		$prd_cd = $request->input('prd_cd');
		$seq = $request->input('seq');

		try {
			DB::beginTransaction();

			DB::table('product')->where('prd_cd', '=', $prd_cd)->update([
				'ut' => now(),
				'admin_id' => $admin_id
			]);

			DB::table('product_code')->where('prd_cd', '=', $prd_cd)->update([
				'ut' => now(),
				'admin_id'	=> $admin_id
			]);

			$result = DB::table('product_image')->where([['prd_cd', '=', $prd_cd], ['seq', '=', $seq]])->first();
			$idx = $result->idx;
			$img_url = $result->img_url;

			ULib::deleteFile($img_url);
			DB::table('product_image')->where('idx', '=', $idx)->delete();

            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code" => $code]);
	}

}