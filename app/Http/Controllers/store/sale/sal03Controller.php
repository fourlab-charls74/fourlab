<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class sal03Controller extends Controller
{
	public function index()
	{
		$mutable	= now();
		$sdate		= $mutable->sub(1, 'month')->format('Y-m-d');


		$values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
			'style_no'		=> "",
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view( Config::get('shop.store.view') . '/sale/sal03', $values);
	}

	public function search(Request $request)
	{
		$sdate			= $request->input('sdate', now()->sub(1, 'month')->format('Ymd'));
		$edate			= $request->input('edate', date("Ymd"));
		$sdate2			= str_replace("-", "", $sdate);
		$edate2			= str_replace("-", "", $edate);

		$store_type		= $request->input('store_type');
		$store_cd		= $request->input('store_cd');
		$prd_cd			= $request->input('prd_cd');
		$com_id			= $request->input("com_cd");
		$com_nm			= $request->input("com_nm");
		$com_type		= $request->input("com_type");

		$goods_stat		= $request->input("goods_stat");
		$style_no		= $request->input("style_no");
		$goods_no		= $request->input("goods_no");
		$goods_nos		= $request->input('goods_nos', '');
		$item			= $request->input("item");
		$brand_nm		= $request->input("brand_nm");
		$brand_cd		= $request->input("brand_cd");
		$goods_nm		= $request->input("goods_nm");
		$goods_nm_eng	= $request->input("goods_nm_eng");
		$prd_cd_range_text	= $request->input("prd_cd_range", '');
		$best_worst		= $request->input('best_worst');
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		$type			= $request->input("type");
		$goods_type		= $request->input("goods_type");
		$group_type_condition	= $request->input("group_type_condition");

		$page	= $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);
		$list_total	= $request->input('limit', 100);

		$orderby	= '';
		$ord	= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','p.goods_no');
		

		if ($best_worst == 'B') {
			$orderby	= sprintf("order by %s %s, a.prd_cd_p desc", $ord_field, "desc");
		} else if ($best_worst == 'W') {
			$orderby	= sprintf("order by %s %s, a.prd_cd_p desc" , $ord_field, "asc");
		}else {
			$orderby	= sprintf("order by %s %s", $ord_field, $ord);
		}

		$where	= "";
		$in_where	= "";
		if($com_type != "")		$where .= " and g.com_type = '$com_type' ";
		if($store_type != "")	$in_where .= " and s.store_type = '" . $store_type . "' ";
		if($store_cd != "")		$in_where .= " and oo.store_cd = '" . $store_cd . "' ";
		if($prd_cd != "")		$in_where .= " and oo.prd_cd like '" . $prd_cd . "%' ";
		if($com_id != "")		$where .= " and g.com_id = '" . Lib::quote($com_id) . "'";
		if($com_nm != "")		$where .= " and g.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if($style_no != "")		$where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
		if($item != "")			$where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
		if($store_channel != "")	$in_where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if($store_channel_kind != "")	$in_where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
		if($brand_cd != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		}else if ($brand_cd == "" && $brand_nm != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		}
		if($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

		if($goods_nos != "") {
			$goods_no = $goods_nos;
		}
		$goods_no = preg_replace("/\s/", ",", $goods_no);
		$goods_no = preg_replace("/\t/", ",", $goods_no);
		$goods_no = preg_replace("/\n/", ",", $goods_no);
		$goods_no = preg_replace("/,,/", ",", $goods_no);

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$in_where .= " and pc.$opt in ($opt_join) ";
			}
		}

		if ($goods_no != "") {
			$goods_nos = explode(",", $goods_no);
			if (count($goods_nos) > 1) {
				if (count($goods_nos) > 500) array_splice($goods_nos, 500);
				$in_goods_nos = join(",", $goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
			}
		}

		if ($type != "") $where .= " and g.type = '" . Lib::quote($type) . "' ";
		if ($goods_type != "") $where .= " and g.goods_type = '" . Lib::quote($goods_type) . "' ";
		if (is_array($goods_stat)) {
			if (count($goods_stat) == 1 && $goods_stat[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
			} else if (count($goods_stat) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
			}
		} else if ($goods_stat != "") {
			$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
		}

		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		
		
		// 컬러, 사이즈별
		if($group_type_condition == 'color_and_size') {


			$sql	= "
				select 
					a.goods_no,
					a.prd_cd,
					pc.brand,
					brd.brand_nm,
					p.style_no,
					a.goods_opt,
					if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
						select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
						from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
					)) as img,
					'' as img_view,
					if(p.prd_nm <> '', p.prd_nm, g.goods_nm) as goods_nm,
					if(p.prd_nm_eng <> '', p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng,
					pc.prd_cd_p as prd_cd_p, 
					pc.color, 
					pc.size,
					
					-- 입고
					0 as in_sum_qty,
					0 as in_sum_amt,
					0 as in_sale_rate,
				
					-- 출고
					0 as ex_sum_qty,
					'' as ex_date,
					
					-- 총판매
					a.qty as total_ord_qty,
					0 as total_ord_amt,
					ifnull(round((a.qty / ps.qty) * 100),0) as total_sale_rate,
					
					-- 기간판매
					a.per_qty as ord_qty,
					a.t_price as ord_amt,
					if(ps.qty = 0, 0, round((a.per_qty / ps.qty) * 100)) as sale_rate,
				
					-- 매장재고, 창고재고
					a.storage_wqty,
					a.store_wqty
				from ( 
					select
						sum(if(ow.ord_state = '30', ow.qty, ow.qty * -1)) as per_qty
						, sum(if(ow.ord_state = '30', ow.recv_amt, ow.recv_amt * -1)) as t_price
						, oo.prd_cd
					    , pc.prd_cd_p
						,0 as qty
						,0 as total_ord_amt
						, sum(oo.price * oo.qty) as ord_amt
						, oo.goods_no, oo.goods_opt
						, (select sum(wqty) from product_stock_storage where prd_cd = pc.prd_cd ) as storage_wqty
						, (select sum(wqty) from product_stock_store where prd_cd = pc.prd_cd) as store_wqty
					from order_opt_wonga ow 
					inner join order_opt oo on ow.ord_opt_no = oo.ord_opt_no
					left outer join store s on oo.store_cd = s.store_cd
					inner join product_code pc on pc.prd_cd = oo.prd_cd
					where
						ow.ord_state in (30, 60, 61)
						and ow.ord_state_date >= '$sdate2'
						and ow.ord_state_date <= '$edate2'
						and if( ow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
						$in_where
					group by oo.prd_cd
				) as a
				inner join product p on p.prd_cd = a.prd_cd
				inner join product_code pc on pc.prd_cd = a.prd_cd
				inner join product_stock ps on a.prd_cd = ps.prd_cd 
				left outer join goods g on a.goods_no = g.goods_no
				left outer join brand brd on pc.brand = brd.br_cd
				where
					1=1
					$where
				$orderby 
				$limit
			";

			$pdo	= DB::connection()->getPdo();
			$stmt	= $pdo->prepare($sql);
			$stmt->execute();
			$result	= [];

			$tot_in_sum_qty		= 0;
			$tot_in_sum_amt		= 0;
			$tot_ex_sum_qty		= 0;
			$tot_ord_sum_qty	= 0;
			$tot_ord_sum_amt	= 0;

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				if ($row["img"] != "") {
					$row["img"] = sprintf("%s%s", config("shop.image_svr"), $row["img"]);
				}

				$prd_cd	= $row['prd_cd'];

				//전체주문데이터
				$sql_tot_ord	= "
					select 
						sum(if(w.ord_state = '30', w.qty, w.qty * -1)) as qty, 
						sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1)) as total_ord_amt
					from order_opt_wonga w
					inner join order_opt o on w.ord_opt_no = o.ord_opt_no
					where 
						o.prd_cd = '$prd_cd' 
						and w.ord_state in (30, 60, 61)
						and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						-- and ( oclm_state = 0 or clm_state = -30 or clm_state = 90)
				";
				$tot_ord = DB::selectOne($sql_tot_ord);

				$row['total_ord_qty']	= $tot_ord->qty;
				$row['total_ord_amt']	= $tot_ord->total_ord_amt;

				$tot_ord_sum_qty	+= $row['total_ord_qty'];
				$tot_ord_sum_amt	+= $row['total_ord_amt'];

				//입고 데이터
				$sql_in	= "
					select 
						sum(p.qty) as in_sum_qty, sum(p.cost * p.qty) as in_sum_amt
					from product_stock_order_product p
					inner join product_stock ps on p.prd_cd = ps.prd_cd
					where 
						p.state >= '30'
						and p.prd_cd = '$prd_cd'
				";
				$tot_in = DB::selectOne($sql_in);

				$row['in_sum_qty']		= $tot_in->in_sum_qty;
				$row['in_sum_amt']		= $tot_in->in_sum_amt;
				$row['in_sale_rate']	= ($row['in_sum_qty'] == 0)?0:round($row['total_ord_qty'] / $row['in_sum_qty'] * 100);

				$tot_in_sum_qty	+= $tot_in->in_sum_qty;
				$tot_in_sum_amt	+= $tot_in->in_sum_amt;

				//출고 데이터
				$sql_out	= "
					select 
						sum(psr.qty) as qty,
						left(min(psr.prc_rt), 10) as prc_rt
					from product_stock_release psr
					where 
						psr.state = '40'
						and psr.prd_cd = '$prd_cd'
				";
				$tot_out = DB::selectOne($sql_out);

				$row['ex_sum_qty']	= $tot_out->qty;
				$row['ex_date']		= $tot_out->prc_rt;

				$tot_ex_sum_qty	+= $tot_out->qty;

				$row['total_sale_rate']	= ($row['in_sum_qty'] == 0)?0:round($row['total_ord_qty'] / $row['in_sum_qty'] * 100);

				$result[] = $row;
			}

			// pagination
			$total		= 0;
			$total_data	= '';
			$page_cnt	= 0;

			if( $page == 1 ){
				$query	= "
					select
						sum(t.ord_qty) as ord_qty
						, sum(t.ord_amt) as ord_amt
						, sum(t.storage_wqty) as storage_wqty
						, sum(t.store_wqty) as store_wqty
					from (
						select 
							-- 입고
							0 as in_sum_qty,
							0 as in_sum_amt,
						
							-- 출고
							0 as ex_sum_qty,
							
							-- 총판매
							0 as total_ord_qty,
							0 as total_ord_amt,
							
							-- 기간판매
							sum(a.per_qty) as ord_qty,
							sum(a.t_price) as ord_amt,
						
							-- 매장재고, 창고재고
							sum(a.storage_wqty) as storage_wqty, 
							sum(a.store_wqty) as store_wqty
						from ( 
							select
								sum(if(ow.ord_state = '30', ow.qty, ow.qty * -1)) as per_qty
								, sum(if(ow.ord_state = '30', ow.recv_amt, ow.recv_amt * -1)) as t_price
								, oo.prd_cd
							    , pc.prd_cd_p
								, 0 as qty
								, 0 as total_ord_amt
								, sum(oo.price * oo.qty) as ord_amt
								, oo.goods_no, oo.goods_opt
								, (select sum(wqty) from product_stock_storage where prd_cd = pc.prd_cd ) as storage_wqty
								, (select sum(wqty) from product_stock_store where prd_cd = pc.prd_cd) as store_wqty
							from order_opt_wonga ow
							inner join order_opt oo on ow.ord_opt_no = oo.ord_opt_no
							left outer join store s on oo.store_cd = s.store_cd
							inner join product_code pc on pc.prd_cd = oo.prd_cd
							where
								ow.ord_state in (30, 60, 61)
								-- and ( oo.clm_state = 0 or oo.clm_state = -30 or oo.clm_state = 90)
								and ow.ord_state_date >= '$sdate2'
								and ow.ord_state_date <= '$edate2'
								and if( ow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
								$in_where
								group by oo.prd_cd
						) as a
						inner join goods g on a.goods_no = g.goods_no
						left outer join brand brd on g.brand = brd.brand
						inner join product_code pc on pc.prd_cd = a.prd_cd
						inner join product_stock ps on a.prd_cd = ps.prd_cd 
						where
							1=1
							$where
						group by a.prd_cd
						$orderby
						$limit
					) t
				";

				$row = DB::selectOne($query);

				$row->in_sum_qty	= $tot_in_sum_qty;	// 총입고수량
				$row->in_sum_amt	= $tot_in_sum_amt;	// 총입고금액
				$row->ex_sum_qty	= $tot_ex_sum_qty;	// 총출고수량
				$row->total_ord_qty	= $tot_ord_sum_qty;	// 총판매수량
				$row->total_ord_amt	= $tot_ord_sum_amt;	// 총판매금액

				$total_data = $row;

				// if ($row) $total = $row->total;
				$page_cnt = (int)(($total - 1) / $page_size) + 1;
			}


		// 품번별
		}else{


			
			$sql	= "
				select 
					a.goods_no,
					a.prd_cd,
					a.prd_cd_p,
					a.brand,
					brd.brand_nm,
					p.style_no,
					a.goods_opt,
					if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
						select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
						from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
					)) as img,
					'' as img_view,
					if(p.prd_nm <> '', p.prd_nm, g.goods_nm) as goods_nm,
					if(p.prd_nm_eng <> '', p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng,
					'' as color, 
					'' as size,
					
					-- 입고
					0 as in_sum_qty,
					0 as in_sum_amt,
					0 as in_sale_rate,
				
					-- 출고
					0 as ex_sum_qty,
					'' as ex_date,
					
					-- 총판매
					a.qty as total_ord_qty,
					0 as total_ord_amt,
					-- ifnull(round((a.qty / ps.qty) * 100),0) as total_sale_rate,
					0 as total_sale_rate,
					
					-- 기간판매
					a.per_qty as ord_qty,
					a.t_price as ord_amt,
					-- if(ps.qty = 0, 0, round((a.per_qty / ps.qty) * 100)) as sale_rate,
					round((a.per_qty/(a.storage_wqty + a.store_wqty) * 100)) as sale_rate,
				
					-- 매장재고, 창고재고
					a.storage_wqty,
					a.store_wqty
				from ( 
					select
						sum(if(ow.ord_state = '30', ow.qty, ow.qty * -1)) as per_qty
						, sum(if(ow.ord_state = '30', ow.recv_amt, ow.recv_amt * -1)) as t_price
						, oo.prd_cd
					    , pc.prd_cd_p
					    , pc.brand
						,0 as qty
						,0 as total_ord_amt
						, sum(oo.price * oo.qty) as ord_amt
						, oo.goods_no, oo.goods_opt
						, (
							select sum(a_.wqty) 
							from product_stock_storage a_
							inner join product_code b_ on a_.prd_cd = b_.prd_cd
							where b_.prd_cd_p = pc.prd_cd_p 
						) as storage_wqty
						, 
						(
							select sum(a_.wqty) 
							from product_stock_store a_
							inner join product_code b_ on a_.prd_cd = b_.prd_cd
							where b_.prd_cd_p = pc.prd_cd_p
						) as store_wqty
					from order_opt_wonga ow 
					inner join order_opt oo on ow.ord_opt_no = oo.ord_opt_no
					left outer join store s on oo.store_cd = s.store_cd
					inner join product_code pc on pc.prd_cd = oo.prd_cd
					where
						ow.ord_state in (30, 60, 61)
						and ow.ord_state_date >= '$sdate2'
						and ow.ord_state_date <= '$edate2'
						and if( ow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
						$in_where
					group by pc.prd_cd_p
				) as a
				inner join product p on p.prd_cd = a.prd_cd
				left outer join goods g on a.goods_no = g.goods_no
				left outer join brand brd on a.brand = brd.br_cd
				where
					1=1
					$where
				$orderby 
				$limit
			";

			$pdo	= DB::connection()->getPdo();
			$stmt	= $pdo->prepare($sql);
			$stmt->execute();
			$result	= [];

			$tot_in_sum_qty		= 0;
			$tot_in_sum_amt		= 0;
			$tot_ex_sum_qty		= 0;
			$tot_ord_sum_qty	= 0;
			$tot_ord_sum_amt	= 0;

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				if ($row["img"] != "") {
					$row["img"] = sprintf("%s%s", config("shop.image_svr"), $row["img"]);
				}

				$prd_cd_p	= $row['prd_cd_p'];

				//전체주문데이터
				$sql_tot_ord	= "
					select 
						sum(if(w.ord_state = '30', w.qty, w.qty * -1)) as qty, 
						sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1)) as total_ord_amt
					from order_opt_wonga w
					inner join order_opt o on w.ord_opt_no = o.ord_opt_no
					where 
						o.prd_cd like '$prd_cd_p%' 
						and w.ord_state in (30, 60, 61)
						and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						-- and ( oclm_state = 0 or clm_state = -30 or clm_state = 90)
				";
				$tot_ord = DB::selectOne($sql_tot_ord);

				$row['total_ord_qty']	= $tot_ord->qty;
				$row['total_ord_amt']	= $tot_ord->total_ord_amt;

				$tot_ord_sum_qty	+= $row['total_ord_qty'];
				$tot_ord_sum_amt	+= $row['total_ord_amt'];

				//입고 데이터
				$sql_in	= "
					select 
						sum(p.qty) as in_sum_qty, sum(p.cost * p.qty) as in_sum_amt
					from product_stock_order_product p
					inner join product_stock ps on p.prd_cd = ps.prd_cd
					where 
						p.state >= '30'
						and p.prd_cd like '$prd_cd_p%'
				";
				$tot_in = DB::selectOne($sql_in);

				$row['in_sum_qty']		= $tot_in->in_sum_qty;
				$row['in_sum_amt']		= $tot_in->in_sum_amt;
				$row['in_sale_rate']	= ($row['in_sum_qty'] == 0)?0:round($row['total_ord_qty'] / $row['in_sum_qty'] * 100);

				$tot_in_sum_qty	+= $tot_in->in_sum_qty;
				$tot_in_sum_amt	+= $tot_in->in_sum_amt;

				//출고 데이터
				$sql_out	= "
					select 
						sum(psr.qty) as qty,
						left(min(psr.prc_rt), 10) as prc_rt
					from product_stock_release psr
					where 
						psr.state = '40'
						and psr.prd_cd like '$prd_cd_p%'
				";
				$tot_out = DB::selectOne($sql_out);

				$row['ex_sum_qty']	= $tot_out->qty;
				$row['ex_date']		= $tot_out->prc_rt;

				$tot_ex_sum_qty	+= $tot_out->qty;

				$row['total_sale_rate']	= ($row['in_sum_qty'] == 0)?0:round($row['total_ord_qty'] / $row['in_sum_qty'] * 100);

				$result[] = $row;
			}

			// pagination
			$total		= 0;
			$total_data	= '';
			$page_cnt	= 0;

			if( $page == 1 ){
				$query	= "
					select
						sum(t.ord_qty) as ord_qty
						, sum(t.ord_amt) as ord_amt
						, sum(t.storage_wqty) as storage_wqty
						, sum(t.store_wqty) as store_wqty
					from (
						select 
							-- 입고
							0 as in_sum_qty,
							0 as in_sum_amt,
						
							-- 출고
							0 as ex_sum_qty,
							
							-- 총판매
							0 as total_ord_qty,
							0 as total_ord_amt,
							
							-- 기간판매
							sum(a.per_qty) as ord_qty,
							sum(a.t_price) as ord_amt,
						
							-- 매장재고, 창고재고
							sum(a.storage_wqty) as storage_wqty, 
							sum(a.store_wqty) as store_wqty
						from ( 
							select
								sum(if(ow.ord_state = '30', ow.qty, ow.qty * -1)) as per_qty
								, sum(if(ow.ord_state = '30', ow.recv_amt, ow.recv_amt * -1)) as t_price
								, oo.prd_cd
							    , pc.prd_cd_p
								, 0 as qty
								, 0 as total_ord_amt
								, sum(oo.price * oo.qty) as ord_amt
								, oo.goods_no, oo.goods_opt
								, (
									select sum(a_.wqty) 
									from product_stock_storage a_
									inner join product_code b_ on a_.prd_cd = b_.prd_cd
									where b_.prd_cd_p = pc.prd_cd_p 
								) as storage_wqty
								, 
								(
									select sum(a_.wqty) 
									from product_stock_store a_
									inner join product_code b_ on a_.prd_cd = b_.prd_cd
									where b_.prd_cd_p = pc.prd_cd_p
								) as store_wqty
							from order_opt_wonga ow
							inner join order_opt oo on ow.ord_opt_no = oo.ord_opt_no
							left outer join store s on oo.store_cd = s.store_cd
							inner join product_code pc on pc.prd_cd = oo.prd_cd
							where
								ow.ord_state in (30, 60, 61)
								-- and ( oo.clm_state = 0 or oo.clm_state = -30 or oo.clm_state = 90)
								and ow.ord_state_date >= '$sdate2'
								and ow.ord_state_date <= '$edate2'
								and if( ow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
								$in_where
								group by pc.prd_cd_p
						) as a
						inner join goods g on a.goods_no = g.goods_no
						left outer join brand brd on g.brand = brd.brand
						where
							1=1
							$where
						group by a.prd_cd_p
						$orderby
						$limit
					) t
				";

				$row = DB::selectOne($query);

				$row->in_sum_qty	= $tot_in_sum_qty;	// 총입고수량
				$row->in_sum_amt	= $tot_in_sum_amt;	// 총입고금액
				$row->ex_sum_qty	= $tot_ex_sum_qty;	// 총출고수량
				$row->total_ord_qty	= $tot_ord_sum_qty;	// 총판매수량
				$row->total_ord_amt	= $tot_ord_sum_amt;	// 총판매금액

				$total_data = $row;

				// if ($row) $total = $row->total;
				$page_cnt = (int)(($total - 1) / $page_size) + 1;
			}
			
			
		}		
		
		
		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"			=> $list_total,
				"page"			=> $page,
				"page_cnt"		=> $page_cnt,
				"page_total"	=> count($result),
				"total_data"	=> $total_data
			),
			"body"	=> $result
		]);
	}
}
