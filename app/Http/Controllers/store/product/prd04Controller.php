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

class prd04Controller extends Controller
{

	public function index()
	{
		$values = [
			'sdate'			=> date('Y-m-d'),
			'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view( Config::get('shop.store.view') . '/product/prd04',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$sdate 		= $request->input("sdate", date("Y-m-d"));
		$next_edate = date("Y-m-d", strtotime("+1 day", strtotime($sdate)));
		$prd_cd		= $request->input("prd_cd", "");
		$goods_no	= $request->input("goods_no", "");
		$style_no	= $request->input("style_no");
		$goods_nm	= $request->input("goods_nm");
		$store_type	= $request->input("store_type", "");
		$store_no	= $request->input("store_no", "");
		$ext_store_qty	= $request->input("ext_store_qty", ""); //매장재고 0 제외
		$ext_storage_qty	= $request->input("ext_storage_qty", ""); // 창고재고 0 제외
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$goods_nm_eng	= $request->input("goods_nm_eng");
		$storage_cd = $request->input('storage_no');
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','prd_cd_p');
		//if ($ord_field == 'prd_cd_p') $ord_field = 'pc.rt';
		//$orderby	= sprintf("order by p.match_yn desc, %s %s, pc.prd_cd", $ord_field, $ord);	//22-12-08 매칭된 상품을 상단으로
		$orderby	= sprintf("order by %s %s, concat(pc.prd_cd_p,pc.color), ob.seq", $ord_field, $ord);	//사이즈 정렬 기준으로 변경
		$plan_category	= $request->input('plan_category');
		$match_yn = $request->input('match_yn1');

		$where		= "";
		$store_where1	= "";
		$store_where2	= "";
		$having = "";
		$in_store_sql	= "";
		//$store_qty_sql	= "(ps.qty - ps.wqty)";
		$store_qty_sql	= "
			(
				select sum(pss.qty) 
				from product_stock_store pss 
				inner join store s on pss.store_cd = s.store_cd and s.use_yn = 'Y'
				where pss.prd_cd = pc.prd_cd
			)
		";
		$next_store_qty_sql = "";

		if($plan_category != '')	$where .= " and pc.plan_category = '" . Lib::quote($plan_category) . "' ";

		if($match_yn == 'Y') 	$where .= " and p.match_yn = 'Y'";
		if($match_yn == 'N') 	$where .= " and p.match_yn = 'N'";

		if( $prd_cd != "" ){
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}

		// 창고검색
		if ( $storage_cd != "" ) {
			$where	.= " and (1!=1";
			foreach($storage_cd as $storage_cd) {
				$where .= " or pss2.storage_cd = '$storage_cd' ";
			}
			$where	.= ")";
		}

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
				$where .= " and g.style_no in ( $in_style_nos ) ";
			} else {
				if ($style_no != "") $where .= " and g.style_no = '" . Lib::quote($style_no) . "' ";
			}
		}

//		if( $style_no != "" )	$where .= " and ( g.style_no like '" . Lib::quote($style_no) . "%' or p.style_no like '" . Lib::quote($style_no) . "%' ) ";
		if( $goods_nm != "" ){
			$where .= " and ( g.goods_nm like '%" . Lib::quote($goods_nm) . "%' or p.prd_nm like '%" . Lib::quote($goods_nm) . "%' ) ";
		}
		if( $store_no != "" ){
			//$in_store_sql	= " left outer join product_stock_store pss on pc.prd_cd = pss.prd_cd ";
			$in_store_sql	= "";

			$store_where1	.= " and ( 1<>1";
			$store_where2	.= " and ( 1<>1";
			foreach($store_no as $store_cd) {
				$store_where1 .= " or location_cd = '" . Lib::quote($store_cd) . "' ";
				$store_where2 .= " or store_cd = '" . Lib::quote($store_cd) . "' ";
			}
			$store_where1	.= ")";
			$store_where2	.= ")";

			//$next_store_qty_sql = " and location_cd = pss.store_cd ";
			//$store_qty_sql	= "pss.wqty";
			$store_qty_sql	= " select sum(wqty) from product_stock_store where prd_cd = pc.prd_cd " . $store_where2;
		}
		if($goods_nm_eng != "")	$where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

		if( $store_no == "" && $store_channel != "" ){
			//$in_store_sql	= " left outer join product_stock_store pss on pc.prd_cd = pss.prd_cd ";
			$in_store_sql	= "";

			$sql	= " select store_cd from store where store_channel = :store_channel and use_yn = 'Y' ";
			$result = DB::select($sql,['store_channel' => $store_channel]);

			$store_where1	.= " and ( 1<>1";
			$store_where2	.= " and ( 1<>1";
			foreach($result as $row){
				$store_where1 .= " or location_cd = '" . Lib::quote($row->store_cd) . "' ";
				$store_where2 .= " or store_cd = '" . Lib::quote($row->store_cd) . "' ";
			}
			$store_where1	.= ")";
			$store_where2	.= ")";

			//$next_store_qty_sql = " and location_cd = pss.store_cd ";
			//$store_qty_sql	= "sum(pss.wqty)";
			$store_qty_sql	= " select sum(wqty) from product_stock_store where prd_cd = pc.prd_cd " . $store_where2;
		}

		if( $store_no == "" && $store_channel != "" && $store_channel_kind ){
			//$in_store_sql	= " left outer join product_stock_store pss on pc.prd_cd = pss.prd_cd ";
			$in_store_sql	= "";

			$sql	= " select store_cd from store where store_channel = :store_channel and store_channel_kind = :store_channel_kind and use_yn = 'Y' ";
			$result = DB::select($sql,['store_channel' => $store_channel, 'store_channel_kind' => $store_channel_kind]);

			$store_where1	.= " and ( 1<>1";
			$store_where2	.= " and ( 1<>1";
			foreach($result as $row){
				$store_where1 .= " or location_cd = '" . Lib::quote($row->store_cd) . "' ";
				$store_where2 .= " or store_cd = '" . Lib::quote($row->store_cd) . "' ";
			}
			$store_where1	.= ")";
			$store_where2	.= ")";

			//$next_store_qty_sql = " and location_cd = pss.store_cd ";
			//$store_qty_sql	= "sum(pss.wqty)";
			$store_qty_sql	= " select sum(wqty) from product_stock_store where prd_cd = pc.prd_cd " . $store_where2;
		}

		if($ext_store_qty == 'true' && $ext_storage_qty == 'true') {
			$having .= "having (hqty - hwqty) <> 0 and (wqty) <> 0";
		} else if ($ext_store_qty == 'true' && $ext_storage_qty != 'true') {
			$having .= "having (hqty - hwqty) <> 0";
		} else if ($ext_store_qty != 'true' && $ext_storage_qty == 'true') {
			$having .= "having (wqty) <> 0";
		}

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;
		$total_row  = [];

		if( $page == 1 ){
			$query	= /** @lang text */
				"
				select
					count(prd_cd) as total,
					ifnull(sum(a.goods_sh * a.wqty + a.goods_sh * a.sqty),0) as total_goods_sh,
					ifnull(sum(a.price * a.wqty + a.price * a.sqty),0) as total_price,
					ifnull(sum(a.wonga * a.wqty + a.wonga * a.sqty),0) as total_wonga,
					ifnull(sum(a.wqty),0) as total_wqty,
					ifnull(sum(a.sqty),0) as total_sqty,
					ifnull(sum(a.qty),0) as total_qty
				from (
					select
						pc.prd_cd
						, sum(pss2.qty) as qty
						, (sum(pss2.wqty) - ifnull((
							select sum(qty) as qty
							from product_stock_hst
							where location_type = 'STORAGE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
							and prd_cd = ps.prd_cd
							group by prd_cd
						), 0)) as wqty
						, (ifnull(($store_qty_sql),0) - ifnull((
							select sum(qty) as qty
							from product_stock_hst
							where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
							and prd_cd = ps.prd_cd $store_where1
							group by prd_cd
						), 0)) as sqty
						-- , if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
						-- , if(pc.goods_no = 0, p.price, g.price) as price
						-- , if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
						, p.tag_price as goods_sh
						, p.price
						, p.wonga
						-- , (sum(pss2.wqty) - ifnull(_next_storage.qty, 0)) as wqty
						, ps.qty as hqty
						, ps.wqty as hwqty
						, pss2.storage_cd as storage_cd
					from product_code pc
						inner join product_stock ps on pc.prd_cd = ps.prd_cd
						$in_store_sql
						inner join product p on p.prd_cd = pc.prd_cd
						left outer join goods g on pc.goods_no = g.goods_no
						inner join code c on pc.color = c.code_id and c.code_kind_cd = 'PRD_CD_COLOR'
						inner join brand b on b.br_cd = pc.brand
						left outer join product_stock_storage pss2 on pss2.prd_cd = pc.prd_cd
					where
						pc.type = 'N'
						$where
					group by pc.prd_cd
					$having
				) a
			";

			$row	= DB::select($query);
			$total	= $row[0]->total;
			$total_row = $row[0];
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$goods_img_url		= '';
		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$query	= /** @lang text */ 
			"
			select
				pc.prd_cd
				, pc.prd_cd_p as prd_cd_p
				, if(pc.goods_no = 0, '', ps.goods_no) as goods_no
				, b.brand_nm
				, if(pc.goods_no = 0, p.style_no, g.style_no) as style_no
				, '' as img_view
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				)) as img
				, if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
				, g.goods_nm_eng
				, pc.color, c.code_val as color_nm
				, pc.size
				, pc.goods_opt
				, ifnull(sum(pss2.qty),0) as qty
				, (ifnull(sum(pss2.wqty),0) - ifnull((
					select sum(qty) as qty
					from product_stock_hst
					where location_type = 'STORAGE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
					and prd_cd = ps.prd_cd
					group by prd_cd
				), 0)) as wqty
				, (ifnull(($store_qty_sql),0) - ifnull((
					select sum(qty) as qty
					from product_stock_hst
					where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
					and prd_cd = ps.prd_cd $store_where1
					group by prd_cd
				), 0)) as sqty
				-- , if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
				-- , if(pc.goods_no = 0, p.price, g.price) as price
				-- , if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
				, p.tag_price as goods_sh
				, p.price
				, p.wonga
				, round(((g.goods_sh - g.price) / g.goods_sh) * 100, 2) as sale_rate
				, p.match_yn
				, ps.qty as hqty
				, ps.wqty as hwqty
				, pss2.storage_cd as storage_cd
				-- , pc.plan_category as plan_category
				, case pc.plan_category
					when '01' then '정상매장'
					when '02' then '전매장'
					when '03' then '이월취급점'
					when '04' then '아울렛전용'
					else ''
				end as 'plan_category'
			from product_code pc
				inner join product_stock ps on pc.prd_cd = ps.prd_cd
				$in_store_sql
				inner join product p on p.prd_cd = pc.prd_cd
				left outer join goods g on pc.goods_no = g.goods_no
				inner join code c on pc.color = c.code_id and c.code_kind_cd = 'PRD_CD_COLOR'
				inner join brand b on b.br_cd = pc.brand
				left outer join product_stock_storage pss2 on pss2.prd_cd = pc.prd_cd
				left outer join product_orderby ob on pc.size = ob.size_cd
			where
				pc.type = 'N'
				$where
			group by pc.prd_cd
			$having
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

			// $chk_len	= strlen($row['prd_cd']) - strlen($row['color']) - strlen($row['size']);
			// $row['prd_cd_p']	= substr($row['prd_cd'], 0, $chk_len);

			$result[] = $row;
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
				'total_row'  => $total_row,
			),
			"body"	=> $result
		]);

	}

	/** show_stock: 옵션별 재고현황 팝업 */
	public function show_stock(Request $request)
	{
		$prd_cd_p = $request->input('prd_cd_p', '');
		$sdate = $request->input('date', date('Y-m-d'));
		if($sdate == '') $sdate = date('Y-m-d');
		$color = $request->input('color', '');
		$size = $request->input('size', '');

		// $sql = "
		// 	select
		// 		p.prd_cd
		// 		, concat(p.brand, p.year, p.season, p.gender, p.item, p.seq, p.opt) as prd_cd_p
		// 		, p.goods_no
		// 		, p.color as color_cd
		// 		, c.code_val as color
		// 		, p.size
		// 		, g.goods_nm
		// 		, g.goods_nm_eng
		// 		, g.style_no
		// 		, g.com_id
		// 		, g.com_nm
		// 		, g.opt_kind_cd
		// 		, o.opt_kind_nm
		// 		, g.brand
		// 		, b.brand_nm
		// 		, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
		// 			select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
		// 			from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
		// 		)) as img
		// 	from product_code p
		// 		left outer join goods g on g.goods_no = p.goods_no
		// 		left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
		// 		left outer join brand b on b.brand = g.brand
		// 		left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = p.color
		// 	having prd_cd_p = :prd_cd_p
		// ";

		// $colors = array_unique(array_map(function($row) {
		// 	return (object)['code' => $row->color_cd, 'name' => $row->color];
		// }, $rows), SORT_REGULAR);

		// $sizes = array_unique(array_map(function($row) {
		// 	return (object)['code' => $row->size];
		// }, $rows), SORT_REGULAR);

		$values = [
			'prd_cd_p' => $prd_cd_p,
			'sdate' => $sdate,
			'color' => $color ?? '',
			'size' => $size ?? '',
			// 'prd' => $rows[0] ?? '',
			'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view(Config::get('shop.store.view') . '/product/prd04_show', $values);
	}

	/** search_stock: 옵션별 재고현황 검색 */
	public function search_stock(Request $request)
	{
		$sdate = $request->input('sdate', date('Y-m-d'));
		$next_edate = date("Ymd", strtotime("+1 day", strtotime($sdate)));
		$now_date = date("Ymd");
		$prd_cd_p = $request->input('prd_cd_p', '');
		$o_prd_cd_p = $prd_cd_p;
		$color = $request->input('color', '');
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		if ($color != '') $prd_cd_p .= $color;

		$values = [];

		if ($prd_cd_p != '') {

			// get sizes
			$sql = "
				select
				    	s.size_cd
				from size s
					inner join product_code pc on pc.size = s.size_cd
				where s.size_kind_cd = pc.size_kind and s.size_cd = pc.size and use_yn = 'Y' and pc.prd_cd like '$prd_cd_p%'
				group by s.size_cd
				order by s.size_seq asc
			";
			$sizes = array_map(function($row) {return $row->size_cd;}, DB::select($sql));

			// get goods info
			$cfg_img_size_real = "a_500";
			$cfg_img_size_list = "a_500";

			$sql = "
				select
					pc.prd_cd_p
					, pc.prd_cd
					, pc.goods_no
					, g.goods_nm
					, g.goods_nm_eng
					, g.style_no
					, g.com_id
					, g.com_nm
					, g.brand
					, b.brand_nm
					, g.opt_kind_cd
					, o.opt_kind_nm
					, ifnull(g.style_no, (select style_no from product p where p.prd_cd = pc.prd_cd)) as style_no
					, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
						select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
						from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
					)) as img
				from product_code pc
					left outer join goods g on g.goods_no = pc.goods_no
					left outer join brand b on b.brand = g.brand
					left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
				group by prd_cd_p
				having prd_cd_p = :prd_cd_p
			";
			$prd = DB::selectOne($sql, ['prd_cd_p' => $o_prd_cd_p]);

			if (!isset($prd) || $prd->goods_no == '0') {
				$sql = "
					select
						p.prd_cd, p.prd_nm as goods_nm, p.style_no, p.type, p.com_id, c.com_nm
						, p.match_yn, p.use_yn, pc.brand, b.brand_nm
						, pc.prd_cd_p as prd_cd_p
					from product p
						inner join product_code pc on pc.prd_cd = p.prd_cd
						left outer join company c on c.com_id = p.com_id
						left outer join brand b on b.br_cd = pc.brand
					group by prd_cd_p
					having prd_cd_p = :prd_cd_p
				";
				$prd = DB::selectOne($sql, ['prd_cd_p' => $o_prd_cd_p]);
			}

			// get store stock
			$where = "";
			if ($store_channel != '') $where .= "and store_channel ='" . Lib::quote($store_channel). "'";
			if ($store_channel_kind ?? '' != '') $where .= "and store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

			$case_sql = "";
			$case_sum_sql = "";
			foreach ($sizes as $size) {
				$case_sql .= "
					, if(pc.size = '$size', (ps.qty
						- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
						- ifnull(w.qty, 0)
					), 0) as '" . str_replace('.', '', $size) . "_qty'
					, if(pc.size = '$size', (ps.wqty
						- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
						- ifnull(w.qty, 0)
					), 0) as '" . str_replace('.', '', $size) . "_wqty'
				";
				$case_sum_sql .= "
					, sum(a.`" . str_replace('.', '', $size) . "_qty`) as '" . str_replace('.', '', $size) . "_qty'
					, sum(a.`" . str_replace('.', '', $size) . "_wqty`) as '" . str_replace('.', '', $size) . "_wqty'
				";
			}

			$sql = "
				select a.store_cd, a.store_nm, a.prd_cd, a.color, c.code_val as color_nm
					$case_sum_sql
					, sum(a.qty) as qty
					, sum(a.wqty) as wqty
				from (
					select pc.color, ps.store_cd, s.store_nm, ps.prd_cd
						$case_sql
						, (ps.qty
							- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
							- ifnull(w.qty, 0)
						) as qty
						, (ps.wqty
							- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
							- ifnull(w.qty, 0)
						) as wqty
					from product_stock_store ps
						inner join product_code pc on pc.prd_cd = ps.prd_cd
						inner join store s on s.store_cd = ps.store_cd
						left outer join (
							select prd_cd, location_cd, type, qty
							from product_stock_hst
							where stock_state_date >= '$next_edate' and stock_state_date <= '$now_date' and location_type = 'STORE'
						) hst on hst.prd_cd = ps.prd_cd and hst.location_cd = ps.store_cd
						left outer join (
							select prd_cd, store_cd, sum(qty * if(ord_state = 30, -1, 1)) as qty
							from order_opt_wonga
							where ord_state_date >= '$next_edate' and ord_state_date <= '$now_date' and ord_state in (30,60,61)
							group by prd_cd, store_cd
						) w on w.prd_cd = ps.prd_cd and w.store_cd = ps.store_cd
						left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = pc.color
					where ps.prd_cd like '$prd_cd_p%' $where
					group by ps.store_cd, pc.prd_cd
					order by pc.color, s.store_nm
				) a
					left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = a.color
				group by a.store_cd, a.color
			";

			$store_rows = DB::select($sql);

			$case_sql = "";
			$case_sum_sql = "";
			foreach ($sizes as $size) {
				$case_sql .= "
					, if(pc.size = '$size', (ps.qty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))), 0) as '" . str_replace('.', '', $size) . "_qty'
					, if(pc.size = '$size', (ps.wqty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))), 0) as '" . str_replace('.', '', $size) . "_wqty'
				";
				$case_sum_sql .= "
					, sum(a.`" . str_replace('.', '', $size) . "_qty`) as '" . str_replace('.', '', $size) . "_qty'
					, sum(a.`" . str_replace('.', '', $size) . "_wqty`) as '" . str_replace('.', '', $size) . "_wqty'
				";
			}

			$sql = "
				select a.storage_cd, a.storage_nm, a.prd_cd, a.color, c.code_val as color_nm
					$case_sum_sql
					, sum(a.qty) as qty
					, sum(a.wqty) as wqty
				from (
					select pc.color, ps.storage_cd, s.storage_nm, ps.prd_cd
						$case_sql
						, (ps.qty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))) as qty
						, (ps.wqty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))) as wqty
					from product_stock_storage ps
						inner join product_code pc on pc.prd_cd = ps.prd_cd
						inner join storage s on s.storage_cd = ps.storage_cd and s.use_yn = 'Y'
						left outer join (
							select prd_cd, location_cd, type, qty
							from product_stock_hst
							where stock_state_date >= '$next_edate' and stock_state_date <= '$now_date' and location_type = 'STORAGE'
						) hst on hst.prd_cd = ps.prd_cd and hst.location_cd = ps.storage_cd
						left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = pc.color
					where ps.prd_cd like '$prd_cd_p%' and ps.qty != 0 and ps.wqty != 0
					group by ps.storage_cd, pc.prd_cd
					order by pc.color, s.storage_nm
				) a
					left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = a.color
				group by a.storage_cd, a.color
			";

			$storage_rows = DB::select($sql);

			$values = [
				'sizes' => $sizes,
				'prd' => $prd,
				'stores' => $store_rows,
				'storages' => $storage_rows,
			];
		}

		return response()->json([ 'data' => $values ], 200);
	}

	public function batch(){
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd04_batch', $values);
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
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/prd04/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update(Request $request)
	{


		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

		//try
		//{
		//    DB::beginTransaction();

		$sql	= " delete from product_stock_storage ";
		DB::delete($sql);

		for( $i = 0; $i < count($datas); $i++ )
		{
			$data		= (array)$datas[$i];

			$storage_cd	= trim($data['storage_cd']);
			$prd_cd_p	= trim($data['prd_cd_p']);
			$prd_cd		= trim($data['prd_cd']);
			$prd_nm		= trim($data['prd_nm']);
			$brand_nm	= trim($data['brand_nm']);
			$style_no	= trim($data['style_no']);
			$color		= trim($data['color']);
			$size		= trim($data['size']);
			$qty		= Lib::uncm(trim($data['qty']));
			$wonga		= Lib::uncm(trim($data['wonga']));
			$tag_price	= Lib::uncm(trim($data['tag_price']));
			$price		= Lib::uncm(trim($data['price']));

			//창고 존재 유무 검토
			$sql		= " select count(*) as tot from storage where storage_cd = :storage_cd ";
			$storage	= DB::selectOne($sql, ['storage_cd' => $storage_cd]);

			if( $storage->tot == 0 ){
				$error_code		= "501";
				$result_code	= "창고정보가 존재하지 않습니다. [" . $storage_cd . "]";

				break;
			}

			//브랜드 존재 유무 검토
			$brand	= "";
			$sql	= " select br_cd from brand where brand_nm = :brand_nm ";
			$result = DB::select($sql,['brand_nm' => $brand_nm]);
			foreach($result as $row){
				$brand	= $row->br_cd;
			}
			if( $brand == "" ){
				$error_code		= "502";
				$result_code	= "브랜드정보가 존재하지 않습니다. [" . $prd_cd . "]";

				break;
			}

			//상품코드 존재 유무
			$sql		= " select count(*) as tot from product_code where prd_cd = :prd_cd ";
			$obj_prd_code	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

			if( $obj_prd_code->tot == 0 ){

				$where	= ['prd_cd'	=> $prd_cd];

				//product 등록/수정
				$values	= [
					'prd_nm'	=> $prd_nm,
					'style_no'	=> $style_no,
					'tag_price'	=> $tag_price,
					'price'		=> $price,
					'wonga'		=> $wonga,
					'type'		=> 'N',			//일반상품
					'com_id'	=> 'alpen',		//
					'unit'		=> '',
					'match_yn'	=> 'N',
					'rt'		=> now(),
					'ut'		=> now(),
					'admin_id'	=> $id
				];
				DB::table('product')->updateOrInsert($where, $values);

				$year	= substr(str_replace($brand, "", $prd_cd), 0 ,2);
				$season	= substr(str_replace($brand, "", $prd_cd), 2 ,1);
				$gender	= substr(str_replace($brand, "", $prd_cd), 3 ,1);
				$item	= substr(str_replace($brand, "", $prd_cd), 4 ,2);
				$seq	= substr(str_replace($brand, "", $prd_cd), 6 ,2);
				$opt	= substr(str_replace($brand, "", $prd_cd), 8 ,2);

				$goods_no	= "";
				$goods_opt	= "";

				//product_code 등록/수정
				$values	= [
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
					'type'		=> 'N',			//일반상품
					'rt'		=> now(),
					'ut'		=> now(),
					'admin_id'	=> $id
				];
				DB::table('product_code')->Insert($values);

			}else{
				$sql		= " select goods_no, goods_opt from product_code where prd_cd = :prd_cd ";
				$obj_prd_code	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				$goods_no	= $obj_prd_code->goods_no;
				$goods_opt	= $obj_prd_code->goods_opt;
			}

			//재고정보 처리
			$where	= ['prd_cd'	=> $prd_cd];

			//창고별 재고등록 오류 수정 시작
			$sql_ps		= " select count(*) as tot from product_stock where prd_cd = :prd_cd ";
			$obj_ps	= DB::selectOne($sql_ps, $where);

			if($obj_ps->tot == 0){

				$values	= [
					//'goods_no'	=> '',
					'wonga'		=> $wonga,
					'qty_wonga'	=> $qty * $wonga,
					'in_qty'	=> $qty,
					'out_qty'	=> '0',
					'qty'		=> $qty,
					'wqty'		=> $qty,
					//'goods_opt'	=> '',
					'barcode'	=> $prd_cd,
					'use_yn'	=> 'Y',
					'rt'		=> now(),
					'ut'		=> now()
				];
				DB::table('product_stock')->Insert($where, $values);

			}else{
				$sql_ps = "
                        update product_stock set
                            wonga       = $wonga ,
                            qty_wonga   = '" . $qty * $wonga . "',
                            in_qty      = in_qty + $qty ,
                            qty         = qty + $qty ,
                            wqty        = wqty + $qty ,
                            barcode     = '$prd_cd',
                            ut          = now()
                        where
                            prd_cd = :prd_cd
                    ";
				DB::update($sql_ps, $where);
			}
			//창고별 재고등록 오류 수정 종료

			//창고재고 정보 처리
			$where	= ['prd_cd'	=> $prd_cd, 'storage_cd' => $storage_cd];

			$values	= [
				'goods_no'	=> $goods_no,
				'qty'		=> $qty,
				'wqty'		=> $qty,
				'goods_opt'	=> $goods_opt,
				'use_yn'	=> 'Y',
				'rt'		=> now(),
				'ut'		=> now()
			];
			DB::table('product_stock_storage')->updateOrInsert($where, $values);

		}

		//	DB::commit();
		//}
		//catch(Exception $e)
		//{
		//    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

	public function batch_wonga(){
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd04_batch_wonga', $values);
	}

	public function upload_wonga(Request $request)
	{

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/prd04/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update_wonga(Request $request)
	{


		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

		//try
		//{
		//    DB::beginTransaction();

		for( $i = 0; $i < count($datas); $i++ )
		{
			$data		= (array)$datas[$i];

			$prd_cd_p	= trim($data['prd_cd_p']);
			$style_no	= trim($data['style_no']);
			$prd_nm		= trim($data['prd_nm']);
			$color		= trim($data['color']);
			$size		= trim($data['size']);
			$tag_price	= Lib::uncm(trim($data['tag_price']));
			$price		= Lib::uncm(trim($data['price']));
			$wonga		= Lib::uncm(trim($data['wonga']));
			$store_qty	= Lib::uncm(trim($data['store_qty']));
			$storage_qty	= Lib::uncm(trim($data['storage_qty']));
			$tot_qty	= Lib::uncm(trim($data['tot_qty']));

			$prd_cd		= $prd_cd_p . $color . $size;

			if( $store_qty != 0 || $storage_qty != 0 ){
				$sql	= " select count(*) as cnt from product_code where prd_cd = :prd_cd";
				$product_code	= DB::selectOne($sql,['prd_cd' => $prd_cd]);

				if( $product_code->cnt == 0 ){
					// 상품코드 정보가 없을시

					$brand	= "";
					$sql	= " select br_cd, length(br_cd) as chk_len from brand where use_yn = 'Y' and br_cd <>'' order by length(br_cd) asc ";
					$result = DB::select($sql);
					foreach($result as $row){
						if( substr($prd_cd, 0, $row->chk_len) == $row->br_cd ){
							$brand	= $row->br_cd;
						}
					}

					if( $brand == "" ){
						$error_code		= "501";
						$result_code	= "브랜드정보가 존재하지 않습니다. [" . $prd_cd . "]";

						break;
					}

					$year	= substr(str_replace($brand, "", $prd_cd), 0 ,2);
					$season	= substr(str_replace($brand, "", $prd_cd), 2 ,1);
					$gender	= substr(str_replace($brand, "", $prd_cd), 3 ,1);
					$item	= substr(str_replace($brand, "", $prd_cd), 4 ,2);
					$seq	= substr(str_replace($brand, "", $prd_cd), 6 ,2);
					$opt	= substr(str_replace($brand, "", $prd_cd), 8 ,2);

					//product_code 등록/수정
					$values	= [
						'prd_cd'	=> $prd_cd,
						'prd_cd_p'	=> $prd_cd_p,
						'goods_no'	=> '',
						'goods_opt'	=> '',
						'brand'		=> $brand,
						'year'		=> $year,
						'season'	=> $season,
						'gender'	=> $gender,
						'item'		=> $item,
						'opt'		=> $opt,
						'seq'		=> $seq,
						'color'		=> $color,
						'size'		=> $size,
						'type'		=> 'N',			//일반상품
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $id
					];
					DB::table('product_code')->Insert($values);

				}

				//product 등록/수정
				$where	= ['prd_cd'	=> $prd_cd];
				$values	= [
					'prd_nm'	=> $prd_nm,
					'style_no'	=> $style_no,
					'tag_price'	=> $tag_price,
					'price'		=> $price,
					'wonga'		=> $wonga,
					'type'		=> 'N',			//일반상품
					'com_id'	=> 'alpen',		//
					'unit'		=> '',
					//'match_yn'	=> 'N',
					'rt'		=> now(),
					'ut'		=> now(),
					'admin_id'	=> $id
				];
				DB::table('product')->updateOrInsert($where, $values);

				//재고정보 처리
				$values	= [
					//'goods_no'	=> '',
					'wonga'		=> $wonga,
					'qty_wonga'	=> '0',
					'in_qty'	=> '0',
					'out_qty'	=> '0',
					'qty'		=> '0',
					'wqty'		=> '0',
					//'goods_opt'	=> '',
					'barcode'	=> $prd_cd,
					'use_yn'	=> 'Y',
					'rt'		=> now(),
					'ut'		=> now()
				];
				DB::table('product_stock')->updateOrInsert($where, $values);

			}else{
				//재고정보 초기화
				$where	= ['prd_cd'	=> $prd_cd];

				$values	= [
					'wonga'		=> $wonga,
					'qty_wonga'	=> '0',
					'in_qty'	=> '0',
					'out_qty'	=> '0',
					'qty'		=> '0',
					'wqty'		=> '0',
					'ut'		=> now()
				];
				DB::table('product_stock')
					->where($where)
					->update($values);
			}

		}

		//	DB::commit();
		//}
		//catch(Exception $e)
		//{
		//    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

	public function batch_store(){
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd04_batch_store', $values);
	}

	public function upload_store(Request $request)
	{

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/prd04/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update_store(Request $request)
	{


		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$init_chk	= $request->input('init_chk');
		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

		//try
		//{
		//    DB::beginTransaction();

		if( $init_chk == "Y"){
			$sql	= " delete from product_stock_store ";
			DB::delete($sql);
		}

		for( $i = 0; $i < count($datas); $i++ )
		{
			$data		= (array)$datas[$i];

			$store_cd	= trim($data['store_cd']);
			$prd_cd_p	= trim($data['prd_cd_p']);
			$prd_nm		= trim($data['prd_nm']);
			$style_no	= trim($data['style_no']);
			$color		= trim($data['color']);
			$size		= trim($data['size']);
			$qty		= Lib::uncm(trim($data['qty']));
			$tag_price	= Lib::uncm(trim($data['tag_price']));
			$price		= Lib::uncm(trim($data['price']));
			$qty_wonga	= 0;
			$wonga		= 0;
			$in_qty		= 0;
			$org_qty	= 0;

			$prd_cd		= $prd_cd_p . $color . $size;

			//매장 존재 유무 검토
			$sql		= " select count(*) as tot from store where store_cd = :store_cd ";
			$store	= DB::selectOne($sql, ['store_cd' => $store_cd]);

			if( $store->tot == 0 ){
				$error_code		= "501";
				$result_code	= "매장정보가 존재하지 않습니다. [" . $store_cd . "]";

				break;
			}

			//상품코드 존재 유무
			$sql		= " select goods_no, goods_opt, wonga, qty_wonga, in_qty, qty from product_stock where prd_cd = :prd_cd ";
			$result = DB::select($sql, ['prd_cd' => $prd_cd]);

			foreach($result as $row){
				$qty_wonga	= $row->qty_wonga;
				$wonga		= $row->wonga;

				if( $qty_wonga > 0 )	$wonga = $qty_wonga / $row->qty;

				$in_qty		= $row->in_qty;
				$org_qty	= $row->qty;

				$goods_no	= $row->goods_no;
				$goods_opt	= $row->goods_opt;
			}

			if( $wonga == 0 ){
				//$error_code		= "502";
				//$result_code	= "상품정보 혹은 원가정보가 존재하지 않습니다. [" . $prd_cd . "]";

				//break;
				$result_code	.= "|". $prd_cd;
			}else{

				//재고정보 처리
				$where	= ['prd_cd'	=> $prd_cd];

				$values	= [
					'wonga'		=> $wonga,
					'qty_wonga'	=> $qty_wonga + $qty * $wonga,
					'in_qty'	=> $in_qty + $qty,
					'qty'		=> $org_qty + $qty,
					'ut'		=> now()
				];
				DB::table('product_stock')
					->where($where)
					->update($values);
				//DB::table('product_stock')->update($where, $values);

				//매장재고 정보 처리
				$where	= ['prd_cd'	=> $prd_cd, 'store_cd' => $store_cd];

				$values	= [
					'goods_no'	=> $goods_no,
					'qty'		=> $qty,
					'wqty'		=> $qty,
					'goods_opt'	=> $goods_opt,
					'use_yn'	=> 'Y',
					'rt'		=> now(),
					'ut'		=> now()
				];
				DB::table('product_stock_store')->updateOrInsert($where, $values);

			}

		}

		//	DB::commit();
		//}
		//catch(Exception $e)
		//{
		//   DB::rollback();

		//	$result_code	= "500";
		//$result_msg		= "데이터 등록/수정 오류";
		//	$result_msg = $e->getMessage();
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}
