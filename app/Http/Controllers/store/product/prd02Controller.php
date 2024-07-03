<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Conf;
use Exception;
use PDO;

class prd02Controller extends Controller
{

	public function index(Request $request) 
	{
		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		$event_cds	= [];
		//판매유형
		$sell_types	= [];
		$code_kinds	= [];

		$conf = new Conf();
		$domain		= $conf->getConfigValue("shop", "domain");

		$size_kind_sql = "select * from size_kind where use_yn = 'Y' order by seq";
		$size_kind = DB::select($size_kind_sql);

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
			'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
			'size_kind'		=> $size_kind,
			'pr_codes'		=> SLib::getCodes("PR_CODE")
		];

		return view( Config::get('shop.store.view') . '/product/prd02',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 1000);

		$goods_stat	= $request->input("goods_stat");
		$style_no	= $request->input("style_no");
		$goods_no	= $request->input("goods_no");
		$goods_nos	= $request->input('goods_nos', '');       // 상품번호 textarea
		$item		= $request->input("item");
		$brand_nm	= $request->input("brand_nm");
		$brand_cd	= $request->input("brand_cd");
		$goods_nm	= $request->input("goods_nm");
		$goods_nm_eng	= $request->input("goods_nm_eng");
		$ext_storage_qty = $request->input('ext_storage_qty');
		$store_no	= $request->input("store_no", "");
		$size_kind	= $request->input("size_kind", "");

		$prd_cd		= $request->input("prd_cd", "");
		$com_id		= $request->input("com_cd");
		$prd_cd_range_text = $request->input("prd_cd_range", '');

		$head_desc	= $request->input("head_desc");
		$ad_desc	= $request->input("ad_desc");

		$is_unlimited	= $request->input("is_unlimited");
		$limit		= $request->input("limit", 1000);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','prd_cd1');
		if ($ord_field == 'prd_cd1') $ord_field = 'pc.rt';

		if ($ord_field == 'pc.prd_cd_p') $orderby = sprintf("order by p.match_yn desc, %s %s, pc.rt desc, pc.prd_cd ", $ord_field, $ord);
		else $orderby = sprintf("order by p.match_yn desc, %s %s, pc.prd_cd ", $ord_field, $ord);

		$in_store_sql = "";
		$match_yn	= $request->input('match_yn1');
		$pr_code	= $request->input('pr_code');

		$where		= "";
		if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or ps.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}

		if($match_yn == 'Y' || $match_yn == 'N') $where .= " and p.match_yn = '$match_yn'";

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		if($item != "")			$where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
		if($brand_cd != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		} else if ($brand_cd == "" && $brand_nm != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		}
		if($goods_nm != "")		$where .= " and if(pc.goods_no = 0, p.prd_nm, g.goods_nm) like '%" . Lib::quote($goods_nm) . "%' ";
		if($goods_nm_eng != "")	$where .= " and if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) like '%" . Lib::quote($goods_nm_eng) . "%' ";
		if($is_unlimited != "")	$where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";

		if($com_id != "")		$where .= " and g.com_id = '" . Lib::quote($com_id) . "'";

		if($head_desc != "")	$where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
		if($ad_desc != "")		$where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";
		if($pr_code != "")		$where .= " and p.pr_code = '" . Lib::quote($pr_code) . "' ";

		if( $store_no != "" ){
			$in_store_sql	= " left outer join product_stock_store pss on pc.prd_cd = pss.prd_cd ";

			$where	.= " and (1!=1";
			foreach($store_no as $store_cd) {
				$where .= " or pss.store_cd = '" . Lib::quote($store_cd) . "' ";
			}
			$where	.= ")";
		}
		
		if ($size_kind != "") $where .= " and pc.size_kind = '" . Lib::quote($size_kind) . "' ";

		if( is_array($goods_stat)) {
			if (count($goods_stat) == 1 && $goods_stat[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
			} else if (count($goods_stat) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
			}
		} else if($goods_stat != ""){
			$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
		}

		if($ext_storage_qty == 'true') {
			$where .= "and ps.wqty > 0";
		}

		// 스타일넘버 다중검색
		$style_no	= preg_replace("/\s/",",",$style_no);
		$style_no	= preg_replace("/\t/",",",$style_no);
		$style_no	= preg_replace("/\n/",",",$style_no);
		$style_no	= preg_replace("/,,/",",",$style_no);

		if( $style_no != "" ){
			$style_nos = explode(",",$style_no);
			if(count($style_nos) > 1){
				if(count($style_nos) > 500) array_splice($style_nos,500);
				$in_style_nos = join(",",$style_nos);
				$where .= " and p.style_no in ( $in_style_nos ) ";
			} else {
				if ($style_no != "") $where .= " and p.style_no = '" . Lib::quote($style_no) . "' ";
			}
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
			$query = "
				select 
					count(a.prd_cd) as total
				from (
					select 
						pc.prd_cd
						, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd1
						, if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
						, if(pc.goods_no = 0, p.price, g.price) as price
						-- , if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
					    , ps.wonga
						, (if(pc.goods_no = 0, p.price, g.price) - ps.wonga) as margin_amt
						, ps.wqty
						, (ps.qty - ps.wqty) as sqty
					from product_code pc
						inner join product_stock ps on ps.prd_cd = pc.prd_cd
						inner join product p on p.prd_cd = pc.prd_cd
						left outer join goods g on g.goods_no = pc.goods_no
						$in_store_sql
						inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
						inner join brand b on b.br_cd = pc.brand
					where 1 = 1
					$where
					group by pc.prd_cd
				) a
			";
			$row	= DB::select($query);
			$total	= $row[0]->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$query = "
			select 
				pc.prd_cd
				-- , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd1
				, pc.prd_cd_p as prd_cd1
			    , if(pc.goods_no = 0, '' , ps.goods_no) as goods_no
				, b.brand_nm
				, if(pc.goods_no = 0, p.style_no, g.style_no) as style_no
				, '' as img_view
				, opt.opt_kind_nm
				, pc.color
				-- , ifnull((
				-- 	select s.size_cd from size s 
				-- 	where s.size_kind_cd = pc.size_kind 
				-- 		and s.size_cd = pc.size
				-- 		and use_yn = 'Y'
				-- ),'') as size
			    , pc.size
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
							select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
							from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
						  )) as img
				, if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
				, if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng
				, pc.goods_opt
				, c.code_val as color_nm
				, ps.wqty
				, (ps.qty - ps.wqty) as sqty
				, p.tag_price as goods_sh
				, p.price as price
				-- , pw.wonga as wonga -- 평균원가
				, p.wonga as wonga
				, (100 / (if(pc.goods_no = 0, p.price, g.price) / (if(pc.goods_no = 0, p.price, g.price) - (p.wonga * 1.1)))) as margin_rate
				, (if(pc.goods_no = 0, p.price, g.price) - (p.wonga * 1.1)) as margin_amt
				, g.org_nm
				, com.com_nm
				, g.reg_dm
				, g.upd_dm
				, p.match_yn
				, p.origin as org_nm
				, pc.size_kind as size_kind,
				  prc.code_val as pr_code_nm
			from product_code pc
				inner join product_stock ps on ps.prd_cd = pc.prd_cd
				inner join product p on p.prd_cd = pc.prd_cd
				-- inner join product_wonga pw on pc.prd_cd_p = pw.prd_cd_p -- 평균원가
				left outer join goods g on g.goods_no = pc.goods_no
				$in_store_sql
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = g.com_id
				left outer join brand brand on brand.brand = g.brand
				left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
				left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
				left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
				left outer join code prc on prc.code_kind_cd = 'PR_CODE' and prc.code_id = p.pr_code
				inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
				inner join brand b on b.br_cd = pc.brand
			where 1 = 1
			$where
			group by pc.prd_cd
			$orderby
			$limit
		";

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

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
			),
			"body"	=> $result
		]);
	}

	public function create(Request $request)
	{
		$prd_cd	= $request->input('prd_cd');
		
		$sql	= " select brand_nm, br_cd from brand where use_yn = 'Y' and br_cd <> '' order by field(brand_nm, '헤스트라', '프리머스', '한바그', '피엘라벤') desc, brand_nm asc";
		$brands	= DB::select($sql);

		$item_sql	= " select code_id, code_val from code where code_kind_cd = 'prd_cd_item' order by code_val asc";
		$items	= DB::select($item_sql);

		$opt_sql	= " select code_id, code_val from code where code_kind_cd = 'prd_cd_opt' order by code_val asc";
		$opts	= DB::select($opt_sql);

		//상품정보관리에서 바코드맵핑추가 버튼을 클릭시 넘어오는 정보
		$mapping = $request->query("mapping","");
		$goods_no = $request->query("goods_no","");

		$values = [
			'prd_cd'	=> $prd_cd,
			'brands'	=> $brands,
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons'	=> SLib::getCodes("PRD_CD_SEASON"),
			'genders'	=> SLib::getCodes("PRD_CD_GENDER"),
			'items'		=> $items,
			'opts'		=> $opts,
			'mapping'	=> $mapping,
			'goods_no'	=> $goods_no,
			
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

		$sql	= "
			select
				a.goods_no, b.style_no, b.goods_nm, a.goods_opt, '' as prd_cd1, '' as color, '' as size, 'N' as match_yn, '' as size_kind,
				'$brand' as brand, '$year' as year, '$season' as season, '$gender' as gender, '$item' as item, '$opt' as opt,
				'' as seq
			from goods_summary a
			inner join goods b on a.goods_no = b.goods_no and b.goods_sub = 0
			where
				a.goods_no = :goods_no1 and a.goods_sub = :goods_sub

			union all

			select
				a.goods_no, b.style_no, b.goods_nm, c.goods_opt, a.prd_cd_p as prd_cd1, a.color
			    ,(
					select s.size_cd from size s
					where s.size_kind_cd = a.size_kind
					   and s.size_cd = a.size
					   and use_yn = 'Y'
				) as size
			    , 'Y' as match_yn,
				a.brand, a.year, a.season, a.gender, a.item, a.opt, a.seq, a.size_kind
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
				$size		= $goods_opt[1];
				
				
				$sql		= " select code_id as color_cd from code where	code_kind_cd = 'PRD_CD_COLOR' and LOWER(replace(code_val,' ','')) = :color limit 1 ";
				$color_cd	= DB::selectOne($sql, ["color" => $color])->color_cd;
				
				$sql = "
					select size_cd, size_kind_cd from size where size_nm = '$size';
				";
				
				$size = DB::selectOne($sql);
				
				$row->size = $size->size_cd;
				$row->size_kind = $size->size_kind_cd;
				
				$style_no 		= str_pad($row->style_no,8,"0",STR_PAD_LEFT);
				$prd_cd1		= $brand . $year . $season . $gender . $item . $style_no ;
				
				$row->prd_cd1	= $prd_cd1;
				$row->color		= isset($color_cd) ? $color_cd : "";
				
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

		$sql = "
			select 
				pc.prd_cd, p.prd_nm, p.style_no, pc.goods_no, concat(c.code_val, '^',s.size_nm) as goods_opt, pc.color, s.size_cd as size,
				p.match_yn, '0' as err_code
			from product_code pc 
			inner join product p on pc.prd_cd = p.prd_cd
			inner join code c on pc.color = c.code_id and c.code_kind_cd = 'PRD_CD_COLOR'
			inner join size s on s.size_kind_cd = pc.size_kind and s.size_cd = pc.size and s.use_yn = 'Y'
			where 
				pc.prd_cd like '$prd_cd%'
		";
		$result = DB::select($sql);

		foreach ($result as $row) {
			if( $row->match_yn == 'Y'){
				if( $row->goods_no == $goods_no ){
					$row->err_code	= '3';	// 해당 상품정보와 매칭
				}else{
					$row->err_code	= '2';	// 다른 상품정보와 매칭 
				}
			}else{
				$row->goods_no	= $goods_no;

				$sql_goods	= " select count(*) as tot from goods_summary where goods_no = :goods_no and replace(goods_opt, ' ', '') = replace(:goods_opt, ' ', '')";
				$goods_opt_cnt	= DB::selectOne($sql_goods,['goods_no' => $goods_no, 'goods_opt' => $row->goods_opt])->tot;
				
				if( $goods_opt_cnt == 0 ){
					$row->err_code	= '1';	// 매칭가능한 옵션이 없음
				}
			}
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total" => count($result),
			),
			"body" => $result,
		]);

	}

	public function prd_search_code_bak(Request $request){
		$prd_cd 	= $request->input('prd_cd');
		$goods_no 	= $request->input('goods_no2');
		
		$sql = "
			select 
				distinct(a.prd_cd),e.goods_nm as prd_nm, e.style_no, a.goods_no, 
				concat(c.code_val, '^',d.code_val2) as goods_opt, 
				a.color, '' as seq , 'N' as is_product
				, ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = a.size_kind
					   and s.size_cd = a.size
					   and use_yn = 'Y'
				),'') as size
			from product_code a
				left outer join goods_summary b on a.goods_no = b.goods_no
				inner join code c on a.color = c.code_id
				inner join code d on a.size = d.code_id
				inner join goods e on e.goods_no = a.goods_no 
			where c.code_kind_cd = 'PRD_CD_COLOR' and a.goods_no = '$goods_no'

			union all

			select
				p.prd_cd, p.prd_nm, p.style_no, '$goods_no' as goods_no, concat(c.code_val, '^',
					ifnull((
						select s.size_nm from size s
						where s.size_kind_cd = pc.size_kind
						   and s.size_cd = pc.size
						   and use_yn = 'Y'
					),'')					
			    ) as goods_opt
				, pc.color, pc.seq, 'Y' as is_product
				, ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
			from product p
				inner join product_code pc on pc.prd_cd = p.prd_cd
				inner join code c on pc.color = c.code_id and c.code_kind_cd = 'PRD_CD_COLOR'
			where p.prd_cd like '$prd_cd%'
			order by seq desc
		";
			
		$result = DB::select($sql);

		$goods_opt_counts = collect($result)->groupBy('goods_opt')->map(function($row) {
            return $row->count();
        })->all();
        $result = collect($result)->map(function($row) use ($goods_opt_counts) {
            $goods_opt = $row->goods_opt;
            $count = $goods_opt_counts[$goods_opt];
            $row->checkbox = $count > 1 ? false : true;
            return $row;
        })->all();


		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total" => count($result),
			),
			"body" => $result,
		]);

	}

	public function add_product_code(Request $request)
	{
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
				$size_kind  = $data['size_kind'];
				$prd_cd		= $prd_cd1 . $color . $size;

				DB::table('product_code')
					->insert([
						'prd_cd'	=> $prd_cd,
						'prd_cd_p'	=> $prd_cd1,
						'goods_no'	=> $goods_no,
						'style_no'  => $style_no,
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
						'size_kind'	=> $size_kind,
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
			$msg = "바코드 등록이 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}


	public function add_product_product(Request $request)
	{
		$datas = $request->input("data", []);
		$now = now();

		try {
			DB::beginTransaction();

			foreach ($datas as $data) {

				$prd_cd = $data['prd_cd'];
				$goods_no = $data['goods_no'];
				$goods_opt = $data['goods_opt'];

				$product_sql = "
					update product 
					set match_yn = 'Y', ut = 'now()'
					where prd_cd = '$prd_cd'
				";
				DB::update($product_sql);

				$product_code_sql = "
					update product_code
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_code_sql);

				$product_stock_sql = "
					update product_stock 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_stock_sql);

				$product_stock_sql = "
					update product_stock_storage 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_stock_sql);

				$product_stock_sql = "
					update product_stock_store 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_stock_sql);
				
				//상품정보 업데이트
				$sql_product	= " 
					select 
						p.tag_price, p.price, p.wonga 
					from product p
					inner join product_code pc on p.prd_cd = pc.prd_cd
					where 
						pc.prd_cd = :prd_cd 
				";
				$product_info	= DB::selectOne($sql_product, ['prd_cd' => $prd_cd]);
				
				$sql_goods	= " update goods set goods_sh = :goods_sh, price = :price, wonga = :wonga where goods_no = :goods_no ";
				DB::update($sql_goods, ['goods_sh' => $product_info->tag_price, 'price' => $product_info->price, 'wonga' => $product_info->wonga, 'goods_no' => $goods_no]);

				//기존 상품 매핑 정보 테이블 정보 추가
				$sql	= " select count(*) as tot from goods_xmd where cd = :prd_cd ";
				$tot	= DB::selectOne($sql,['prd_cd' => $prd_cd])->tot;
				
				if( $tot == 0 ){
					$sql	= " insert into goods_xmd(cd, goods_no, goods_sub, goods_opt, rt, ut) values ( :prd_cd, :goods_no, '0', :goods_opt, now(), now() ) ";
					DB::insert($sql, ['prd_cd' => $prd_cd, 'goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				}else{
					$sql	= " update goods_xmd set goods_no = :goods_no, goods_opt = :goods_opt, ut = now() where cd = :prd_cd ";
					DB::update($sql, ['prd_cd' => $prd_cd, 'goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				}
				
			}

			DB::commit();
			$code = 200;
			$msg = "바코드 매칭이 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
	
	public function create_save_online_product_options(Request $request) {
		$data = $request->input("data", []);
		$code = 200;
		$msg = "";

		try {
			DB::beginTransaction();
			
			foreach ($data as $d) {
				$prd_cd = $d['prd_cd'];
				$goods_no = $d['goods_no'];
				$goods_opt = $d['goods_opt'];
				
				/*
				 * goods_good 테이블에 해당되는 상품이 있는지 확인
				 * goods_summary 테이블에 해당되는 상품이 있는지 확인
				 * 둘 다 없다면 goods_good, goods_summary 테이블에 해당 goods_opt값을 insert
				 * 있다면 에러처리
				 * 
				 * */
				$goods_good_sql = "
					select
						count(*) as cnt
					from goods_good
					where goods_no = :goods_no and goods_opt = :goods_opt
				";
				
				$select_goods_good = DB::selectOne($goods_good_sql, ['goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				
				$goods_summary_sql = "
					select
						count(*) as cnt
					from goods_summary
					where goods_no = :goods_no and goods_opt = :goods_opt
				";
				
				$select_goods_summary = DB::selectOne($goods_summary_sql, ['goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				
				if ($select_goods_good->cnt == 0 && $select_goods_summary->cnt == 0) {
					$goods_good_insert_sql = "
						insert into goods_good (goods_no, goods_opt, regi_date)
						values(:goods_no, :goods_opt, now())
					";
					DB::insert($goods_good_insert_sql, ['goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
					
					$goods_summary_insert_sql = "
						insert into goods_summary (goods_no, opt_name, goods_opt, soldout_yn, use_yn, rt, ut)
						values (:goods_no, '컬러^사이즈', :goods_opt, 'N', 'Y', now(), now())
					";
					DB::insert($goods_summary_insert_sql, ['goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				} else {
					$code = 500;
					throw new Exception("이미 매칭이 되어있거나 이미 등록되어있는 옵션입니다.");
				}
				
				/*
				 * 매칭관련 테이블 update 및 insert
				 * 
				 * */

				$product_sql = "
					update product 
					set match_yn = 'Y', ut = 'now()'
					where prd_cd = '$prd_cd'
				";
				DB::update($product_sql);

				$product_code_sql = "
					update product_code
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_code_sql);

				$product_stock_sql = "
					update product_stock 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_stock_sql);

				$product_stock_sql = "
					update product_stock_storage 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_stock_sql);

				$product_stock_sql = "
					update product_stock_store 
					set goods_no = '$goods_no', goods_opt = '$goods_opt', ut = now()
					where prd_cd = '$prd_cd'
				";
				DB::update($product_stock_sql);

				//기존 상품 매핑 정보 테이블 정보 추가
				$sql	= " select count(*) as tot from goods_xmd where cd = :prd_cd ";
				$tot	= DB::selectOne($sql,['prd_cd' => $prd_cd])->tot;

				if( $tot == 0 ){
					$sql	= " insert into goods_xmd(cd, goods_no, goods_sub, goods_opt, rt, ut) values ( :prd_cd, :goods_no, '0', :goods_opt, now(), now() ) ";
					DB::insert($sql, ['prd_cd' => $prd_cd, 'goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				}else{
					$sql	= " update goods_xmd set goods_no = :goods_no, goods_opt = :goods_opt, ut = now() where cd = :prd_cd ";
					DB::update($sql, ['prd_cd' => $prd_cd, 'goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				}
			}

			DB::commit();
			$code = 200;
			$msg = "온라인상품 옵션 생성 후 바코드 매칭이 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = $code === 200 ? 500 : $code;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
	
	// 추후 삭제 해야함
	public function add_product_product_bak(Request $request)
	{
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
			$msg = "바코드 매칭이 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function edit_goods_no($product_code, $goods_no, Request $request)
	{
		$sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')
			->select('com_id', 'com_nm')->get()->all(); // 공급업체 리스트
		
		$product = array(); 

		if( $goods_no != "" ){
			$sql	= "
				select
					pc.prd_cd
				    , pc.prd_cd_p
				    , pc.goods_no
				    , ifnull(g.goods_nm,p.prd_nm) as goods_nm
					, ifnull(g.style_no,p.style_no) as style_no
				    , pc.color
				    , c1.code_val as color_nm
				    , pc.goods_opt
				    , pc.size
					, p.tag_price as goods_sh
					, p.price as price
				    , p.wonga
				    , p.com_id
					, pc.size_kind
					, p.origin as origin
					, p.pr_code
				from product_code pc
				inner join product p on p.prd_cd = pc.prd_cd
				left outer join goods g on g.goods_no = pc.goods_no
				left outer join code c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and c1.code_id = pc.color and c1.use_yn = 'Y'
				where
					pc.prd_cd = :prd_cd
			";
			$product	= DB::selectOne($sql,['prd_cd' => $product_code]);
		}

		$size_kind_sql	= " select * from size_kind where use_yn = 'Y' order by seq ";
		$size_kind		= DB::select($size_kind_sql);

		$values = [
			'sup_coms' 	=> $sup_coms,
			'prd_cd'	=> $product_code,
			'goods_no'	=> $goods_no,
			'product'	=> $product,
			'size_kind' => $size_kind,
			'pr_codes'	=> SLib::getCodes("PR_CODE")
		];

		return view( Config::get('shop.store.view') . '/product/prd02_edit',$values);

	}

	public function prd_edit_search(Request $request)
	{
		$goods_no	= $request->input('goods_no');

		$sql	= "
			select
				a.goods_no, b.style_no, b.goods_nm, a.goods_opt, c.prd_cd
				, concat(c.brand, c.year, c.season, c.gender, c.item, c.seq, c.opt) as prd_cd_p, c.color
				, c.size, if(ifnull(c.prd_cd,'') = '', '', 'Y') as match_yn, if(ifnull(c.prd_cd,'') = '', '', '삭제') as del
				, if(ifnull(c.prd_cd,'') = '', '', '삭제') as del_mapping
			from goods_summary a
			inner join goods b on a.goods_no = b.goods_no and b.goods_sub = 0
			left outer join product_code c on c.goods_no = a.goods_no and c.goods_opt = a.goods_opt
			where
				a.goods_no = :goods_no and a.use_yn = 'Y'
			order by c.prd_cd
		";

		$result = DB::select($sql,['goods_no'	=> $goods_no]);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body" => $result
		]);

	}

	public function del_product_code(Request $request)
	{

		$prd_cd		= $request->input('prd_cd');

		try {
			DB::beginTransaction();

			$sql = "
				select
					qty
					, wqty
				from product_stock
				where prd_cd = :prd_cd
			";
			$product_stock_cnt = DB::selectOne($sql,[ 'prd_cd' => $prd_cd ]);

			if ($product_stock_cnt->qty == 0 && $product_stock_cnt->wqty == 0) {
				DB::table('product')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_code')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_stock')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_stock_store')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_stock_storage')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::commit();
				$code = 200;
				$msg = "바코드 삭제가 완료되었습니다.";
		
			} else {
				$code = 201;
				$msg = "해당 상품의 재고가 남아있습니다.";
			}
			
		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	//현재 사용하지 않음(확인후 삭제 예정)
	public function match_goods_no($product_code, Request $request)
	{

		$sql	= "
			select
				pc.prd_cd, pc.goods_no, p.prd_nm, p.style_no, pc.color, c1.code_val as color_nm,  pc.size, pc.goods_opt
			from product_code pc
			left outer join product p on p.prd_cd = pc.prd_cd
			left outer join code c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and c1.code_id = pc.color and c1.use_yn = 'Y'
			where
				pc.prd_cd = :prd_cd
		";
		$product	= DB::selectOne($sql,['prd_cd' => $product_code]);

		$values = [
			'prd_cd'	=> $product_code,
			'product'	=> $product,
			'pr_codes'	=> SLib::getCodes("PR_CODE")
		];

		return view( Config::get('shop.store.view') . '/product/prd02_edit_match',$values);

	}

	public function prd_edit_match_search(Request $request)
	{
		$prd_cd		= $request->input('prd_cd');
		$goods_no	= $request->input('goods_no');

		$sql	= "
			select
				d.goods_no, g.style_no, g.goods_nm, d.goods_opt, a.prd_cd,
				concat(a.brand, a.year, a.season, a.gender, a.item, a.seq, a.opt) as prd_cd1, a.color, 
				a.size, '' as match_yn
			from product_code a
			inner join code b on a.color = b.code_id and b.code_kind_cd = 'PRD_CD_COLOR'
			inner join code c on a.size = c.code_val and c.code_kind_cd = 'PRD_CD_SIZE_MATCH'
			inner join (
				select goods_no, goods_opt from goods_summary 
			) d on d.goods_no = :goods_no2 and replace(d.goods_opt, ' ','') = replace(concat(b.code_val,'^',c.code_val2), ' ','') 
			inner join goods g on g.goods_no = d.goods_no
			where 
				a.goods_no = 0 and a.prd_cd = :prd_cd

			union all

			select
				gs.goods_no, b.style_no, b.goods_nm, gs.goods_opt, c.prd_cd,
				concat(c.brand, c.year, c.season, c.gender, c.item, c.seq, c.opt) as prd_cd1, c.color, 
				c.size, if(ifnull(c.prd_cd,'') = '', '', 'Y') as match_yn
			from goods_summary gs
			inner join goods b on gs.goods_no = b.goods_no and b.goods_sub = 0
			left outer join product_code c on c.goods_no = gs.goods_no and c.goods_opt = gs.goods_opt
			where
				gs.goods_no = :goods_no1
		";

		$result = DB::select($sql,['goods_no1' => $goods_no, 'goods_no2' => $goods_no, 'prd_cd' => $prd_cd]);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body" => $result
		]);

	}

	public function edit_match_product_code(Request $request)
	{
        $datas		= $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($datas as $data) {

				$prd_cd		= $data['prd_cd'];
				$goods_no	= $data['goods_no'];
				$goods_opt	= $data['goods_opt'];

				// 상품 정보 수정
				$sql	= " 
					update product 
						set match_yn = 'Y', ut = now()
					where prd_cd = :prd_cd
				";
				DB::update($sql, ['prd_cd' => $prd_cd]);

				$sql	= "
					update product_code
						set goods_no = :goods_no, goods_opt = :goods_opt, ut = now()
					where prd_cd = :prd_cd
				";
				DB::update($sql, ['prd_cd' => $prd_cd, 'goods_no' => $goods_no, 'goods_opt' => $goods_opt]);
				
				$sql	= "
					update product_stock 
						set goods_no = :goods_no, goods_opt = :goods_opt, ut = now()
					where prd_cd = :prd_cd
				";
				DB::update($sql, ['prd_cd' => $prd_cd, 'goods_no' => $goods_no, 'goods_opt' => $goods_opt]);

            }
				
			DB::commit();
			$code = 200;
			$msg = "바코드 매칭이 완료되었습니다.";

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

	//바코드 일괄매칭 자료 업로드
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
	
	//바코드 일괄매칭 저장
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

			$cd			= $data["prd_cd"];
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
		for ( $i = 0; $i < count($datas); $i++ ) {
			$data	= (array)$datas[$i];

			$prd_cd		= $data["prd_cd"];
			$goods_no	= $data["goods_no"];
			$goods_opt	= $data["goods_opt"];

			$sql	= " delete from product_code where prd_cd = :prd_cd ";
			DB::delete($sql,['prd_cd' => $prd_cd]);
	
			$sql	= " delete from product_stock where prd_cd = :prd_cd ";
			DB::delete($sql,['prd_cd' => $prd_cd]);

			if( substr($prd_cd, 0 , 2) != "HR" || substr($prd_cd, 0 , 2) != "TR" ){
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

			$prd_cd_p	= $brand . $year . $season . $gender . $item . $seq . $opt;
	
			DB::table('product_code')
				->insert([
					'prd_cd'	=> $prd_cd,
					'prd_cd_p'	=> $prd_cd_p,
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

		$sql	= " select brand_nm, br_cd from brand where use_yn = 'Y' and br_cd <> '' order by field(brand_nm, '헤스트라', '프리머스', '한바그', '피엘라벤') desc, brand_nm asc";
		$brands	= DB::select($sql);

		$item_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_item' order by code_val asc ";
		$items = DB::select($item_sql);

		$opt_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_opt' order by code_val asc ";
		$opts = DB::select($opt_sql);

		$color_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_color' order by code_id asc ";
		$colors = DB::select($color_sql);

		$size_kind_sql = "select * from size_kind where use_yn = 'Y' order by seq";
		$size_kind = DB::select($size_kind_sql);

		$values = [
			'brands' 	=> $brands,
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons' 	=> SLib::getCodes("PRD_CD_SEASON"),
			'genders' 	=> SLib::getCodes("PRD_CD_GENDER"),
			'items'		=> $items,
			'opts' 		=> $opts,
			'colors' 	=> $colors,
			'sizes'		=> SLib::getCodes("PRD_CD_SIZE_MATCH"),
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'sup_coms' 	=> $sup_coms,
			'units' 	=> SLib::getCodes("PRD_CD_UNIT"),
			'images' 	=> [],
			'size_kind' => $size_kind,
			'pr_codes'	=> SLib::getCodes("PR_CODE")
		];

		return view( Config::get('shop.store.view') . '/product/prd02_product_upload',$values);
	}

	public function save_product(Request $request)
	{
		$admin_id = Auth('head')->user()->id;
		$sel_data = $request->input("sel_data");

		try {

			DB::beginTransaction();

			foreach($sel_data as $row) {

				$brand 		= $row['brand'];
				$year 		= $row['year'];
				$season		= $row['season'];
				$gender		= $row['gender'];
				$opt 		= $row['opt'];
				$item 		= $row['item'];
				$color 		= $row['color'];
				$size_kind	= $row['size_kind'];
				$size 		= $row['size'];
				$prd_nm		= $row['prd_nm'];
				$prd_nm_eng	= $row['prd_nm_eng']??'';
				$style_no 	= $row['style_no'];
				$sup_com 	= $row['sup_com'];
				$price 		= $row['price'];
				$wonga 		= $row['wonga'];
				$tag_price 	= $row['tag_price'];
				$origin 	= $row['origin'];
				$plan_category 	= $row['plan_category'];


				$brand 		= explode(' : ', $brand);
				$year 		= explode(' : ', $year);
				$season 	= explode(' : ', $season);
				$gender 	= explode(' : ', $gender);
				$opt 		= explode(' : ', $opt);
				$item 		= explode(' : ', $item);
				$size_kind  = explode(' : ', $size_kind);
				$sup_com 	= explode(' : ', $sup_com);
				$plan_category 	= explode(' : ', $plan_category);

				$unit = "";

				$prd_cd	= $row['prd_cd'].$color.$size;
				$goods_no = "";

				$sql = "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				// $size_sql = "select * from code where code_kind_cd = 'PRD_CD_SIZE_MATCH' and code_id = '$size[0]'";
				// $size_cd = DB::selectOne($size_sql)->code_val2;

				$goods_opt = "";
				if ($result->count == 0) {

					DB::table('product')->insert([
						'prd_cd' => $prd_cd,
						'prd_nm' => $prd_nm,
						'prd_nm_eng' => $prd_nm_eng,
						'style_no' => $style_no,
						'price' => $price,
						'wonga' => $wonga,
						'tag_price' => $tag_price,
						'com_id' => $sup_com[0],
						'origin' => $origin,
						'unit' => $unit,
						'rt' => now(),
						'ut' => now(),
						'admin_id' => $admin_id
					]);

					

					$prd_cd_p	= $brand[0] . $year[0] . $season[0] . $gender[0] . $item[0] . $style_no;
		
					DB::table('product_code')->insert([
						'prd_cd'	=> $prd_cd,
						'prd_cd_p'	=> $prd_cd_p,
						'goods_no'	=> $goods_no,
						'style_no'	=> $style_no,
						'goods_opt'	=> $goods_opt,
						'brand'		=> $brand[0],
						'year'		=> $year[0],
						'season'	=> $season[0],
						'gender'	=> $gender[0],
						'opt'		=> $opt[0],
						'item'		=> $item[0],
						'color'		=> $color,
						'size_kind' => $size_kind[0],
						'size'		=> $size,
						'plan_category' => $plan_category[0],
						'rt'		=> now(),
						'ut'		=> now(),
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

					/**
					 *  상품 이미지 저장 (단일 이미지)
					 */
					if ($row['image'] != null) {
						$base64_src = $row['image'];
						$save_path = "/images/prd02";

						$unique_img_name = $prd_cd . $style_no;

						$img_url = ULib::uploadBase64img($save_path, $base64_src, $unique_img_name);

						DB::table('product_image')->insert([
							'prd_cd' => $prd_cd,
							'img_url' => $img_url,
							'rt' => now(),
							'ut' => now(),
							'admin_id'	=> $admin_id
						]);
					}
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

	public function update_product(Request $request)
	{
		$admin_id = Auth('head')->user()->id;
		$admin_name = Auth('head')->user()->name;

		$prd_cd 	= $request->input('prd_cd');
		$goods_no	= $request->input('goods_no');
		$style_no	= $request->input('style_no');
		$com_id		= $request->input('sup_com');
		$tag_price	= $request->input('tag_price');
		$price		= $request->input('price');
		$size_kind	= $request->input('size_kind');
		$origin 	= $request->input('origin', '');
		$pr_code	= $request->input('pr_code');
	
		// $sql = "
		// 	select
		// 		c.prd_cd
		// 		, c.prd_cd_p
		// 	from goods_summary a
		// 	left outer join product_code c on c.goods_no = a.goods_no and c.goods_opt = a.goods_opt
		// 	where
		// 		a.goods_no = :goods_no
		// ";
		// $result = DB::select($sql, ['goods_no' => $goods_no]);

		try {
			DB::beginTransaction();
			
			if ($goods_no != '0') {
				
				$sql		= " select com_nm from company where com_id = :com_id ";
				$com_info	= DB::selectOne($sql, ['com_id' => $com_id]);
				
				DB::table('goods')
					->where('goods_no', '=', $goods_no)
					->update([
						"style_no"	=> $style_no,
						"price"		=> $price,
						"goods_sh"	=> $tag_price,
						"org_nm"	=> $origin, // goods 테이블의 원산지 변경
						"com_id"	=> $com_id,
						"com_nm"	=> $com_info->com_nm,
						"admin_id"	=> $admin_id,
						"admin_nm"	=> $admin_name,
						"upd_dm"	=> now(),
					]);
			}
			
			// foreach($result as $row){
			// 	DB::table('product')
			// 		->where('prd_cd', 'like', $row->prd_cd_p . '%')
			// 		->update([
			// 			"price" 	=> $price,
			// 			"tag_price" => $tag_price,
			// 			"origin"	=> $origin,	//product 테이블의 원산지 변경
			// 			"admin_id"	=> $admin_id,
			// 			"ut" => now(),
			// 		]);
				
				//사이즈구분 변경하는 부분
			// 	DB::table('product_code')
			// 		->where('prd_cd', 'like', $row->prd_cd_p . '%')
			// 		->update([
			// 			"size_kind" => $size_kind,
			// 			"ut"		=> now(),
			// 			"admin_id"	=> $admin_id
			// 		]);
			// }
			
			// 바코드가 매칭되지않았을 때 
			//if ($goods_no == '0') {
				$sql = "
					select 
						prd_cd, prd_cd_p
					from product_code
					where prd_cd = :prd_cd
				";
				
				$product_result = DB::select($sql,['prd_cd' => $prd_cd]);
				
				foreach ($product_result as $pr) {
					DB::table('product_code')
						->where('prd_cd', 'like', $pr->prd_cd_p . '%')
						->update([
							"size_kind" => $size_kind,
							"ut"		=> now(),
							"admin_id"	=> $admin_id
						]);

					DB::table('product')
						->where('prd_cd', 'like', $pr->prd_cd_p . '%')
						->update([
							"style_no"	=> $style_no,
							"price" 	=> $price,
							"tag_price" => $tag_price,
							"com_id"	=> $com_id,
							"origin"	=> $origin,
							"pr_code"	=> $pr_code,
							"admin_id"	=> $admin_id,
							"ut"		=> now(),
						]);
				}
			//}

			DB::commit();
			$code = 200;
			$msg = "바코드 정보 수정이 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function getSeq(Request $request)
	{
		$brand = $request->input('brand');
		$year = $request->input('year');
		$season = $request->input('season');
		$gender = $request->input('gender');
		$item = $request->input('item');

		$sql = " 
			select *
			from product_code 
			where 
				brand = :brand
				and year = :year
				and season = :season
				and item = :item
				and gender = :gender
		";
		$result	= DB::selectOne($sql, ['brand' => $brand, 'year' => $year, 'season' => $season, 'item' => $item, 'gender' => $gender]);

		return response()->json(['result' => $result , 'code' => 200]);
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

	//상품코드등록 순서 출력하는 부분
	public function selSeq(Request $request)
	{

		$prd_cd	= $request->input('prd_cd', '');
		$opt	= $request->input('opt', '');

		try {
			DB::beginTransaction();

			$sql = "
				select 
					p.prd_cd, g.goods_nm as prd_nm, g.goods_nm_eng as goods_nm_eng , pc.seq
				from product p 
					inner join product_code pc on p.prd_cd = pc.prd_cd
					inner join goods g on g.goods_no = pc.goods_no
				where p.prd_cd like '$prd_cd%' and pc.opt = '$opt'
				group by p.prd_nm
				order by p.prd_cd asc
			";

			$result = DB::select($sql);
            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code" => $code, "result" => $result]);
	}

	//순서 선택시 자동완성
	public function changeSeq(Request $request)
	{

		$prd_nm = $request->input('prd_nm', '');
		$prd_seq = $request->input('prd_seq', '');

		try {
			DB::beginTransaction();

			$sql = "
				select 
					g.goods_nm as prd_nm, g.goods_nm_eng as prd_nm_eng, p.style_no, p.tag_price, p.price, p.wonga, p.com_id
				from product p
					inner join product_code pc on p.prd_cd = pc.prd_cd
					inner join goods g on g.goods_no = pc.goods_no
				where g.goods_nm = '$prd_nm'
				limit 1
			";

			$result = DB::select($sql);
            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code" => $code, "result" => $result]);
	}

	//성별 변경 시 해당 성별의 사이즈 값 출력
	public function change_size(Request $request)
	{
		$size_kind = $request->input('size_kind');

		try {
			DB::beginTransaction();

			$sql = "
				select
					size_kind_cd
					, size_cd
					, size_nm
					, use_yn
				from size
				where size_kind_cd = '$size_kind'
				and use_yn = 'Y'
				order by size_seq asc
			";

			$result = DB::select($sql);

            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code" => $code, "result" => $result]);
	}

	public function batch_show()
    {
		$values = [];

        return view(Config::get('shop.store.view') . '/product/prd02_batch', $values);
    }

	public function import_excel(Request $request)
	{
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();
	
				$save_path = "data/product/prd02/";
				$file_name = "${now}_${user_id}.${extension}";
				
				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}
	
				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);
	
				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}

	public function get_products(Request $request)
	{
        $data = $request->input('data', []);
        $result = [];
		
        foreach($data as $key => $d)
        {
			$brand = $d['brand'];
			$year = $d['year'];
			$season = $d['season'];
			$gender = $d['gender'];
			$item = $d['item'];
			$opt = $d['opt'];
			$style_no = $d['style_no'];
			$color = $d['color'];
			$size_kind = $d['size_kind'];
			$size = $d['size'];
			$plan_category = $d['plan_category'];
			$goods_nm = $d['goods_nm'];
			$goods_nm_eng = $d['goods_nm_eng'];
			$sup_com = $d['sup_com'];
			$wonga = $d['wonga'];
			$tag_price = $d['tag_price'];
			$price = $d['price'];
			$origin = $d['origin'];
			
			$prd_cd = $brand . $year . $season . $gender . $opt . $style_no . $color . $size;
			$prd_cd_p = $brand . $year . $season . $gender . $opt . $style_no;

            $sql = "
				select
				    '$prd_cd' as prd_cd
				    , '$prd_cd_p' as prd_cd_p
				 	, '$brand' as brand
					, '$year' as year
					, '$season' as season
					, '$gender' as gender
					, '$item' as item
					, '$opt' as opt
					, '$style_no' as style_no
					, '$color' as color
					, '$size_kind' as size_kind
					, '$size' as size
					, '$plan_category' as plan_category
					, '$goods_nm' as goods_nm
					, '$goods_nm_eng' as goods_nm_eng
					, '$sup_com' as sup_com
					, '$wonga' as wonga
					, '$tag_price' as tag_price
					, '$price' as price
					, '$origin' as origin
				from product_code
				limit 1

            ";
            $row = DB::selectOne($sql);
            array_push($result, $row);
        }

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
            ],
            "body" => $result
        ]);
    }

	public function batch_products(Request $request) 
	{
		$admin_id = Auth('head')->user()->id;
		$data = $request->input("products", []);
		

		try {
			DB::beginTransaction();

			foreach ($data as $row) {
				$prd_cd = $row['prd_cd'];
				$prd_cd_p = $row['prd_cd_p'];
				$brand = $row['brand'];
				$year = $row['year'];
				$season = $row['season'];
				$gender = $row['gender'];
				$item = $row['item'];
				$opt = $row['opt'];
				$style_no = $row['style_no'];
				$color = $row['color'];
				$size_kind = $row['size_kind'];
				$size = $row['size'];
				$plan_category = $row['plan_category'];
				$goods_nm = $row['goods_nm'];
				$goods_nm_eng = $row['goods_nm_eng'];
				$sup_com = $row['sup_com'];
				$wonga = $row['wonga'];
				$tag_price = $row['tag_price'];
				$price = $row['price'];
				$origin = $row['origin'];

				$unit = "";
				$goods_no = "";
				$goods_opt = "";

				$sql = "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				if ($result->count == 0) {

					DB::table('product')->insert([
						'prd_cd'		=> $prd_cd,
						'prd_nm'		=> $goods_nm,
						'prd_nm_eng'	=> $goods_nm_eng,
						'style_no'		=> $style_no,
						'price'			=> $price,
						'wonga'			=> $wonga,
						'tag_price'		=> $tag_price,
						'com_id'		=> $sup_com,
						'unit'			=> $unit,
						'origin'		=> $origin,
						'rt'			=> now(),
						'ut'			=> now(),
						'admin_id'		=> $admin_id
					]);

					DB::table('product_code')->insert([
						'prd_cd'		=> $prd_cd,
						'prd_cd_p'		=> $prd_cd_p,
						'style_no'		=> $style_no,
						'seq'			=> '',
						'goods_no'		=> $goods_no,
						'goods_opt'		=> $goods_opt,
						'brand'			=> $brand,
						'year'			=> $year,
						'season'		=> $season,
						'gender'		=> $gender,
						'item'			=> $item,
						'opt'			=> $opt,
						'color'			=> $color,
						'size_kind' 	=> $size_kind,
						'size'			=> $size,
						'plan_category' => $plan_category,
						'rt'			=> now(),
						'ut'			=> now(),
						'admin_id'		=> $admin_id
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
				}else {
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

	///////////////////////////////////
	///////////신규바코드생성///////////
	///////////////////////////////////

	public function create_barcode(Request $request)
	{
		// $sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')->where('com_type', '=', '10')
		// 	->select('com_id', 'com_nm')->get()->all(); // 공급업체 리스트

		$sql = "select com_id, com_nm from company where use_yn = 'Y' and com_type in ('1','10')";
		$sup_coms = DB::select($sql);

		$sql	= " select brand_nm, br_cd from brand where use_yn = 'Y' and br_cd <> '' order by field(brand_nm, '헤스트라', '프리머스', '한바그', '피엘라벤') desc, brand_nm asc";
		$brands	= DB::select($sql);

		$item_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_item' order by code_val asc ";
		$items = DB::select($item_sql);

		$opt_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_opt' order by code_val asc ";
		$opts = DB::select($opt_sql);

		$color_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_color' order by code_id asc ";
		$colors = DB::select($color_sql);

		$size_kind_sql = "select * from size_kind where use_yn = 'Y' order by seq ";
		$size_kind = DB::select($size_kind_sql);

		$values = [
			'brands' 	=> $brands,
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons' 	=> SLib::getCodes("PRD_CD_SEASON"),
			'genders' 	=> SLib::getCodes("PRD_CD_GENDER"),
			'items'		=> $items,
			'opts' 		=> $opts,
			'colors' 	=> $colors,
			'sizes'		=> SLib::getCodes("PRD_CD_SIZE_MATCH"),
			'years'		=> SLib::getCodes("PRD_CD_YEAR"),
			'sup_coms' 	=> $sup_coms,
			'units' 	=> SLib::getCodes("PRD_CD_UNIT"),
			'size_kind' => $size_kind,
			'images' 	=> []
		];

		return view( Config::get('shop.store.view') . '/product/prd02_create_barcode',$values);
	}

	public function dup_style_no(Request $request) 
	{
		$request->input('prd_cd_p');


	}

	/*
	***
	상품슬라이더
	***
	*/

	public function index_slider(Request $request)
	{

        $where = '';
        foreach(explode(",", $request->goods_nos) as $goods_no) {
            $where .= " or goods_no = $goods_no";
        }

		$sql = "
            select goods_no, goods_sub, goods_nm, date_format(reg_dm,'%Y%m%d') as reg_dm, img, goods_cont
            from goods
            where 1!=1 $where
		";
        $goods_list = DB::select($sql);

        foreach($goods_list as $goods) {
            $reg_dm = $goods->reg_dm;
            $dir = sprintf("/images/goods_img/%s/%s", $reg_dm, $goods->goods_no);
            $file_list = Storage::disk('public')->files($dir);

            $files = [];
            $sortArr = [];

            foreach ($file_list as $file) {
                $names = explode('/', $file);
                $name = array_pop($names);
                $file_size = floor(Storage::disk('public')->size($file) / 1000);
                $img_size = explode('.', explode('_', $name)[2])[0];
        
                $files[] = [
                    'src' => "/" . $file,
                    'filesize' => $file_size,
                    'size' => $img_size
                ];
        
                $sortArr[] = $img_size;
            }
            array_multisort($sortArr, SORT_ASC, $files);
            $goods->files = $files;
        }
        // error_log(var_export($goods_list, 1));

		$values = [
			'goods_list' => $goods_list,
		];
		return view( Config::get('shop.store.view') . '/product/prd02_slider', $values);
	}

	// 바코드 맵핑 정보 맵핑 삭제
	public function del_mapping(Request $request)
	{
		$prd_cd	= $request->input('prd_cd');

		try {
			DB::beginTransaction();

			DB::table('product_code')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'goods_no'	=> 0,
					'goods_opt'	=> ''
				]);

			DB::table('product')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'match_yn' => 'N'
				]);
			
			DB::table('product_stock')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'goods_no'	=> 0,
					'goods_opt'	=> ''
				]);

			DB::table('product_stock_store')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'goods_no'	=> 0,
					'goods_opt'	=> ''
				]);

			DB::table('product_stock_storage')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'goods_no'	=> 0,
					'goods_opt'	=> ''
				]);

			// 기존 상품 코드 매핑 테이블 삭제
			DB::table('goods_xmd')->where('cd', $prd_cd)->delete();

			DB::commit();
			$code = 200;
			$msg = "맵핑 삭제가 완료되었습니다.";

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	/*
	 * 
	 * 바코드등록(구) 일괄등록 부분
	 * 
	 * */
	public function barcode_batch()
	{
		$values = [];

		return view(Config::get('shop.store.view') . '/product/prd02_barcode_batch', $values);
	}

	public function import_excel2(Request $request)
	{
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();

				$save_path = "data/product/prd02/";
				$file_name = "${now}_${user_id}.${extension}";

				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}

				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);

				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}

	public function get_products2(Request $request)
	{
		$data	= $request->input('data', []);
		$result	= [];

		foreach($data as $key => $d)
		{
			$brand			= $d['brand'];
			$opt_kind_nm	= $d['opt_kind_nm'];
			$prd_cd_p		= $d['prd_cd_p'];
			$color			= $d['color'];
			$size_kind		= $d['size_kind'];
			$size			= $d['size'];
			$goods_nm		= $d['goods_nm'];
			$goods_nm_eng	= $d['goods_nm_eng'];
			$style_no		= $d['style_no'];
			$seq			= $d['seq'];
			$price			= $d['price'];
			$wonga			= $d['wonga'];
			$tag_price		= $d['tag_price'];
			$year			= $d['year'];
			$season			= $d['season'];
			$gender			= $d['gender'];
			$item			= $d['item'];
			$sup_com		= $d['sup_com'];
			$origin			= $d['origin'];
			$pr_code		= $d['pr_code'];

			$sql = "
				select
				 	'$brand' as brand
					, '$opt_kind_nm' as opt_kind_nm
				 	, '$prd_cd_p' as prd_cd_p
					, '$color' as color
				    , '$size_kind' as size_kind
					, '$size' as size
					, '$goods_nm' as goods_nm
					, '$goods_nm_eng' as goods_nm_eng
					, '$style_no' as style_no
					, '$seq' as seq
					, '$price' as price
					, '$wonga' as wonga
					, '$tag_price' as tag_price
					, '$year' as year
					, '$season' as season
					, '$gender' as gender
					, '$item' as item
					, '$sup_com' as sup_com
					, '$origin' as origin
					, '$pr_code' as pr_code
				from product_code
				limit 1
            ";
			$row = DB::selectOne($sql);
			array_push($result, $row);
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> [
				"total"		=> count($result),
				"page"		=> 1,
				"page_cnt"	=> 1,
				"page_total"=> 1,
			],
			"body"	=> $result
		]);
	}

	public function batch_products2(Request $request)
	{
		$admin_id	= Auth('head')->user()->id;
		$data		= $request->input("products", []);
		
		try {
			DB::beginTransaction();

			foreach ($data as $row) {
				$prd_cd_p	= $row['prd_cd_p'];
				$brand		= $row['brand'];
				$year		= $row['year'];
				$season		= $row['season'];
				$gender		= $row['gender'];
				$item		= $row['item'];
				$opt		= $row['opt_kind_nm'];
				$style_no	= $row['style_no'];
				$seq		= $row['seq'];
				$color		= $row['color'];
				$size_kind	= $row['size_kind'];
				$size		= $row['size'];
				$goods_nm	= $row['goods_nm'];
				$goods_nm_eng	= $row['goods_nm_eng'];
				$sup_com	= $row['sup_com'];
				$wonga		= $row['wonga'];
				$tag_price	= $row['tag_price'];
				$price		= $row['price'];
				$origin		= $row['origin'];
				$pr_code	= $row['pr_code'];

				$unit		= "";
				$goods_no	= "";
				$goods_opt	= "";
				
				$prd_cd		= $prd_cd_p . $color . $size;

				// 행사코드 오류 검토
				$pr_code_err	= "";
				if(($pr_code == 'JS') && ($tag_price != $price))	$pr_code_err = "Y";
				if(($pr_code == 'J2') && ($tag_price <= $price))	$pr_code_err = "Y";
				if(($pr_code == 'J1') && ($brand == 'F'))			$pr_code_err = "Y";
				
				if($pr_code_err == 'Y'){
					return response()->json(["code" => -2, "prd_cd" => $prd_cd]);
				}

				$sql	= "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);
				
				if ($result->count == 0) {

					DB::table('product')->insert([
						'prd_cd'		=> $prd_cd,
						'prd_nm'		=> $goods_nm,
						'prd_nm_eng'	=> $goods_nm_eng,
						'style_no'		=> $style_no,
						'price'			=> $price,
						'wonga'			=> $wonga,
						'tag_price'		=> $tag_price,
						'com_id'		=> $sup_com,
						'unit'			=> $unit,
						'origin'		=> $origin,
						'pr_code'		=> $pr_code,
						'rt'			=> now(),
						'ut'			=> now(),
						'admin_id'		=> $admin_id
					]);

					DB::table('product_code')->insert([
						'prd_cd'		=> $prd_cd,
						'prd_cd_p'		=> $prd_cd_p,
						'style_no'		=> $style_no,
						'seq'			=> $seq,
						'goods_no'		=> $goods_no,
						'goods_opt'		=> $goods_opt,
						'brand'			=> $brand,
						'year'			=> $year,
						'season'		=> $season,
						'gender'		=> $gender,
						'item'			=> $item,
						'opt'			=> $opt,
						'color'			=> $color,
						'size_kind' 	=> $size_kind,
						'size'			=> $size,
						'rt'			=> now(),
						'ut'			=> now(),
						'admin_id'		=> $admin_id
					]);

					DB::table('product_stock')->insert([
						'goods_no'	=> $goods_no,
						'prd_cd'	=> $prd_cd,
						'wonga'		=> $wonga,
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
				}else {
					DB::rollback();
					return response()->json(["code" => -1, "prd_cd" => $prd_cd]);
				}
			}

			DB::commit();
			$code	= 200;
			$msg	= "성공";
		} catch (\Exception $e) {
			DB::rollback();
			$msg	= $e->getMessage();
			$code	= 500;
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function save_product2(Request $request)
	{
		$admin_id	= Auth('head')->user()->id;
		$sel_data	= $request->input("sel_data");
		
		try {

			DB::beginTransaction();

			foreach($sel_data as $row) {

				$brand 		= $row['brand'];
				$year 		= $row['year'];
				$season		= $row['season'];
				$gender		= $row['gender'];
				$opt 		= $row['opt'];
				$seq		= $row['seq'];
				$item 		= $row['item'];
				$color 		= $row['color'];
				$size_kind	= $row['size_kind'];
				$size 		= $row['size'];
				$prd_nm		= $row['prd_nm'];
				$prd_nm_eng	= $row['prd_nm_eng']??'';
				$style_no 	= $row['style_no']??'';
				$sup_com 	= $row['sup_com'];
				$price 		= $row['price'];
				$wonga 		= $row['wonga'];
				$tag_price 	= $row['tag_price'];
				$origin 	= $row['origin'];
				$pr_code	= $row['pr_code'];
//				$plan_category 	= $row['plan_category'];
				
				$brand 		= explode(' : ', $brand);
				$year 		= explode(' : ', $year);
				$season 	= explode(' : ', $season);
				$gender 	= explode(' : ', $gender);
				$opt 		= explode(' : ', $opt);
				$item 		= explode(' : ', $item);
				$sup_com 	= explode(' : ', $sup_com);
				$pr_code	= explode(' : ', $pr_code);
//				$plan_category 	= explode(' : ', $plan_category);

				$unit = "";

				$prd_cd	= $row['prd_cd'].$color.$size;
				$goods_no = "";

				$sql = "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				// $size_sql = "select * from code where code_kind_cd = 'PRD_CD_SIZE_MATCH' and code_id = '$size[0]'";
				// $size_cd = DB::selectOne($size_sql)->code_val2;

				$prd_cd_p	= $brand[0] . $year[0] . $season[0] . $gender[0] . $item[0] . $seq . $opt[0];
				

				$goods_opt = "";
				if ($result->count == 0) {

					DB::table('product')->insert([
						'prd_cd'		=> $prd_cd,
						'prd_nm'		=> $prd_nm,
						'prd_nm_eng'	=> $prd_nm_eng,
						'style_no'		=> $style_no,
						'price'			=> $price,
						'wonga'			=> $wonga,
						'tag_price'		=> $tag_price,
						'com_id'		=> $sup_com[0],
						'origin'		=> $origin,
						'pr_code'		=> $pr_code[0],
						'unit'			=> $unit,
						'rt'			=> now(),
						'ut'			=> now(),
						'admin_id'		=> $admin_id
					]);

					DB::table('product_code')->insert([
						'prd_cd'	=> $prd_cd,
						'prd_cd_p'	=> $prd_cd_p,
						'goods_no'	=> $goods_no,
						'style_no'	=> $style_no,
						'goods_opt'	=> $goods_opt,
						'brand'		=> $brand[0],
						'year'		=> $year[0],
						'season'	=> $season[0],
						'gender'	=> $gender[0],
						'seq'		=> $seq,
						'opt'		=> $opt[0],
						'item'		=> $item[0],
						'color'		=> $color,
						'size_kind' => $size_kind,
						'size'		=> $size,
//						'plan_category' => $plan_category[0],
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $admin_id
					]);

					DB::table('product_stock')->insert([
						'goods_no'	=> $goods_no,
						'prd_cd'	=> $prd_cd,
						'wonga'		=> $wonga,
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

					/**
					 *  상품 이미지 저장 (단일 이미지)
					 */
					if ($row['image'] != null) {
						$base64_src	= $row['image'];
						$save_path	= "/images/prd02";

						$unique_img_name = $prd_cd . $style_no;

						$img_url = ULib::uploadBase64img($save_path, $base64_src, $unique_img_name);

						DB::table('product_image')->insert([
							'prd_cd'	=> $prd_cd,
							'img_url'	=> $img_url,
							'rt'		=> now(),
							'ut'		=> now(),
							'admin_id'	=> $admin_id
						]);
					}
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
	
	// 상품코드 일괄 매핑 페이지
	public function show_batch_mapping()
	{

		$values = [
		];

		return view(Config::get('shop.store.view') . '/product/prd02_show_batch_mapping', $values);
	}

	// 상품코드 일괄 매핑 엑셀 업로드
	public function mapping_import_excel(Request $request)
	{
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();

				$save_path = "data/store/product/prd02/";
				$file_name = "${now}_${user_id}.${extension}";

				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}

				$file = sprintf("${save_path}%s", $file_name);

				move_uploaded_file($_FILES['file']['tmp_name'], $file);

				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}
	
	// 상품코드 일괄 매핑 검토 및 데이터 생성
	public function mapping_data(Request $request)
	{

		$data	= $request->input('data', []);
		$result	= [];
		$msg	= '';
		$code	= 200;
		
		$not_prd_cd		= [];
		$not_goods_opt	= [];
		$mapping_y		= [];

		foreach ($data as $row) {
			$prd_cd		= $row['prd_cd'];
			$goods_no	= $row['goods_no'];
			$goods_opt	= $row['goods_opt'];

			//바코드, 온라인코드 검색
			$sql	= " select prd_cd, goods_no from product_code where prd_cd = :prd_cd ";
			$search_true_prd_cd = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

			if(empty($search_true_prd_cd)) {
				array_push($not_prd_cd, $prd_cd);
			}

			//매핑 여부 검색
			$sql	= " select prd_cd from product where prd_cd = :prd_cd and match_yn = 'N' ";
			$search_match_n = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

			if(empty($search_match_n)) {
				array_push($mapping_y, $prd_cd);
			}
			
			//상품정보, 옵션 검색
			$sql	= " select goods_no, goods_opt from goods_summary where goods_no = :goods_no and goods_opt = :goods_opt";
			$search_true_goods = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_opt' => $goods_opt]);

			if(empty($search_true_goods)) {
				array_push($not_goods_opt, $goods_no . "||" . $goods_opt);
			}
		}

		if (count($not_prd_cd) > 0) {
			$code = 400;
			$msg .= "존재하지않는 상품이 있습니다." . "\n". implode(', ', $not_prd_cd) . "\n";
		}

		if (count($mapping_y) > 0) {
			$code = 400;
			$msg .= "이미 매핑된 상품이 있습니다." . "\n". implode(', ', $mapping_y) . "\n";
		}

		if (count($not_goods_opt) > 0) {
			$code = 400;
			$msg .= "존재하지않는 온라인코드 혹은 옵션이 있습니다." . "\n". implode(', ', $not_goods_opt) . "\n";
		}

		return response()->json([
			"code"	=> $code,
			"msg"	=> $msg,
			"head"	=> [
				"total"		=> count($data),
				"page"		=> 1,
				"page_cnt"	=> 1,
				"page_total"=> 1,
			],
			"body"	=> $data
		]);
	}

	// 비매칭 바코드 삭제
	public function del_prd_cd_not_matching(Request $request)
	{

		$prd_cd	= $request->input('prd_cd');

		try {
			DB::beginTransaction();

			$sql = "
				select
					qty
					, wqty
				from product_stock
				where prd_cd = :prd_cd
			";
			$product_stock_cnt = DB::selectOne($sql,[ 'prd_cd' => $prd_cd ]);

			if ($product_stock_cnt->qty == 0 && $product_stock_cnt->wqty == 0) {
				DB::table('product')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_code')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_stock')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_stock_store')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::table('product_stock_storage')
					->where('prd_cd', '=', $prd_cd)
					->delete();

				DB::commit();
				$code = 200;
				$msg = "바코드 삭제가 완료되었습니다.";

			} else {
				$code = 201;
				$msg = "해당 상품의 재고가 남아있습니다.";
			}

		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
}
