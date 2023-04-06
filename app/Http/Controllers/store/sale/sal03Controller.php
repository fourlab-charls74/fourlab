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

		// 매장구분
		$sql = " 
			select *
			from code
			where 
				code_kind_cd = 'store_type' and use_yn = 'Y' order by code_seq 
		";
		$store_types = DB::select($sql);

		$values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'store_types'     => $store_types,
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'			=> SLib::getItems(),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
		];
        return view( Config::get('shop.store.view') . '/sale/sal03', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->sub(1, 'month')->format('Ymd'));
		$edate = $request->input('edate', date("Ymd"));
		$sdate2 = str_replace("-", "", $sdate);
		$edate2 = str_replace("-", "", $edate);

		$store_type = $request->input('store_type');
		$store_cd = $request->input('store_cd');
		$prd_cd = $request->input('prd_cd');
		$com_id = $request->input("com_cd");
		$com_nm = $request->input("com_nm");
		$com_type = $request->input("com_type");

        $goods_stat = $request->input("goods_stat");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $goods_nm_eng = $request->input("goods_nm_eng");
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$best_worst = $request->input('best_worst');

        $type = $request->input("type");
        $goods_type = $request->input("goods_type");
		$group_type_condition = $request->input("group_type_condition");

        $page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);
		$list_total = $request->input('limit', 100);

		$orderby = '';
		$in_orderby = '';
		$rank_column = '';

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','final.ord_qty');

		if ($best_worst == 'B') {
			$orderby = sprintf("order by %s %s, total_sale_rate asc", 'final.'.$ord_field, "desc");
			$in_field = $ord_field == 'ord_qty' ? ' sum(oo.qty)' : ' sum(oo.qty * oo.price)';
			$in_orderby = sprintf("order by %s %s", $in_field, "desc");
			$rank_column = $ord_field == 'ord_qty' ? ' sum(oo.qty)' : ' sum(oo.qty * oo.price)';
		} else if ($best_worst == 'W') {
			$orderby = sprintf("order by %s %s, total_sale_rate asc" , 'final.'.$ord_field, "asc");
			$in_field = $ord_field == 'ord_qty' ? ' sum(oo.qty)' : ' sum(oo.qty * oo.price)';
			$in_orderby = sprintf("order by %s %s", $in_field, "asc");
			$rank_column = $ord_field == 'ord_qty' ? ' sum(oo.qty)' : ' sum(oo.qty * oo.price)';
		} else {
			$orderby = sprintf("order by %s %s, total_sale_rate asc", 'final.'.$ord_field, $ord);
			$in_field = $ord_field == 'ord_qty' ? ' sum(oo.qty)' : ' sum(oo.qty * oo.price)';
			$in_orderby = sprintf("order by %s %s", $in_field, "asc");
			$rank_column = $ord_field == 'ord_qty' ? ' sum(oo.qty)' : ' sum(oo.qty * oo.price)';
		}

		$where	= "";
		$in_where	= "";

		if ($com_type != "") $where .= " and final.com_type = '$com_type' ";
		if ($store_type != "")	$in_where .= " and s.store_type = '" . $store_type . "' ";
		if ($store_cd != "")	$in_where .= " and oo.store_cd = '" . $store_cd . "' ";
		if ($prd_cd != "")	$in_where .= " and oo.prd_cd like '" . $prd_cd . "%' ";
		if ($com_id != "") $where .= " and final.com_id = '" . Lib::quote($com_id) . "'";
		if ($com_nm != "") $where .= " and final.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if ($style_no != "") $where .= " and final.style_no like '" . Lib::quote($style_no) . "%' ";
		if ($item != "") $where .= " and final.opt_kind_cd = '" . Lib::quote($item) . "' ";
		if ($brand_cd != "") {
			$where .= " and final.brand = '" . Lib::quote($brand_cd) . "' ";
		} else if ($brand_cd == "" && $brand_nm != "") {
			$where .= " and final.brand = '" . Lib::quote($brand_cd) . "' ";
		}
		if ($goods_nm != "") $where .= " and final.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if ($goods_nm_eng != "") $where .= " and final.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

		if ($goods_nos != "") {
			$goods_no = $goods_nos;
		}
		$goods_no = preg_replace("/\s/", ",", $goods_no);
		$goods_no = preg_replace("/\t/", ",", $goods_no);
		$goods_no = preg_replace("/\n/", ",", $goods_no);
		$goods_no = preg_replace("/,,/", ",", $goods_no);

		//보기 그룹 조건 설정
		$group_by = null;
		$total_sale_rate = null;
		$sale_rate = null;
		$prd_cd_p = null;
		$size = null;
		$color = null;
		$stock_qty = null;
		$stock_wqty = null;
		$group_column = null;
		$max_column = null;
		$prd_cd = null;
		$goods_opt = null;

		if($group_type_condition == 'color_and_size') {
			$group_by = "group by pc2.prd_cd";

			$total_sale_rate = "(select ifnull(round((final.total_ord_qty / ps.qty) * 100),0) from product_stock ps where ps.prd_cd = final.prd_cd) as total_sale_rate,";

			$sale_rate = "(select if(ps.qty = 0, 0, round((final.ord_qty / ps.qty) * 100)) from product_stock ps where ps.prd_cd = final.prd_cd) as sale_rate,";

			$prd_cd_p = "(select concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) from product_code pc where pc.prd_cd = final.prd_cd) as prd_cd_p,";
			
			$color =  "(select pc.color from product_code pc where pc.prd_cd = final.prd_cd) as color,";

			$size =  "(select pc.size from product_code pc where pc.prd_cd = final.prd_cd) as size,";

			$stock_qty = "(select sum(ifnull(ps.qty, '0')) from product_stock ps where ps.prd_cd = final.prd_cd group by ps.prd_cd) as stock_qty,";

			$partial_stock_qty = "(select sum(ifnull(ps.qty, '0')) from product_stock ps where ps.prd_cd = oo.prd_cd group by ps.prd_cd) as partial_stock_qty,";
			
			$prd_cd = "final.prd_cd,";

			$goods_opt = "final.goods_opt,";

			$stock_wqty = "(select sum(ifnull(ps.wqty, '0')) from product_stock ps where ps.prd_cd = final.prd_cd group by ps.prd_cd) as stock_wqty";

			$partial_stock_wqty = "(select sum(ifnull(ps.wqty, '0')) from product_stock ps where ps.prd_cd = oo.prd_cd group by ps.prd_cd) as partial_stock_wqty";

			$group_column = "prd_cd";

			$max_column = "max(oo.goods_no) as goods_no";

		} else {
			$group_by = "group by oo.goods_no";
			
			$total_sale_rate = "(select ifnull(round((final.total_ord_qty / sum(ps.qty)) * 100),0) from product_stock ps where ps.goods_no = final.goods_no group by ps.goods_no) as total_sale_rate,";

			$sale_rate = "(select if(ps.qty = 0, 0, round((final.ord_qty / sum(ps.qty)) * 100)) from product_stock ps where ps.goods_no = final.goods_no group by ps.goods_no) as sale_rate,";

			$prd_cd_p = "'' as prd_cd_p, ";
			$color = "'' as color,";
			$size = "'' as size,";

			$stock_qty = "(select sum(ifnull(ps.qty, '0')) from product_stock ps where ps.goods_no = final.goods_no group by ps.goods_no) as stock_qty,";

			$partial_stock_qty = "(select sum(ifnull(ps.qty, '0')) from product_stock ps where ps.goods_no = oo.goods_no group by ps.goods_no) as partial_stock_qty,";

			$prd_cd = "'' as prd_cd,";

			$goods_opt = "'' as goods_opt,";

			$stock_wqty = "(select sum(ifnull(ps.wqty, '0')) from product_stock ps where ps.goods_no = final.goods_no group by ps.goods_no) as stock_wqty";

			$partial_stock_wqty = "(select sum(ifnull(ps.wqty, '0')) from product_stock ps where ps.goods_no = oo.goods_no group by ps.goods_no) as partial_stock_wqty";

			$group_column = "goods_no";

			$max_column = "max(oo.prd_cd) as prd_cd";
		} 

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$in_where .= " and pc2.$opt $in_query ($opt_join) ";
			}
		}

		if ($goods_no != "") {
			$goods_nos = explode(",", $goods_no);
			if (count($goods_nos) > 1) {
				if (count($goods_nos) > 500) array_splice($goods_nos, 500);
				$in_goods_nos = join(",", $goods_nos);
				$where .= " and final.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where .= " and final.goods_no = '" . Lib::quote($goods_no) . "' ";
			}
		}

		if ($type != "") $where .= " and final.type = '" . Lib::quote($type) . "' ";
		if ($goods_type != "") $where .= " and final.goods_type = '" . Lib::quote($goods_type) . "' ";
		if (is_array($goods_stat)) {
			if (count($goods_stat) == 1 && $goods_stat[0] != "") {
				$where .= " and final.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
			} else if (count($goods_stat) > 1) {
				$where .= " and final.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
			}
		} else if ($goods_stat != "") {
			$where .= " and final.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
		} 

		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";
		
		$sql	= "
			select 
				final.goods_no,
				final.brand_nm,
				final.style_no,
				final.img,
				final.goods_nm,
				final.goods_nm_eng,
				final.in_sum_qty,
				final.in_sum_amt,
				final.in_sale_rate,
				final.ex_sum_qty,
				final.total_ord_qty,
				final.total_ord_amt,
				final.ord_qty,
				final.ord_amt,
				$prd_cd
				$goods_opt
				$total_sale_rate
				$sale_rate
				$prd_cd_p
				$color
				$size
				final.partial_stock_qty as stock_qty,
				final.partial_stock_wqty as stock_wqty
			from (
				select 
					a.prd_cd,
					a.goods_no,
					g.brand,
					brd.brand_nm,
					g.style_no,
					a.goods_opt,
					if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
						select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
						from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
					)) as img,
					'' as img_view,
					g.goods_nm,
					g.goods_nm_eng,
					g.type,
					g.goods_type,
					g.sale_stat_cl,
					g.com_type,
					g.com_id,
					g.com_nm,
					g.opt_kind_cd,

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
					
					-- 기간판매
					a.per_qty as ord_qty,
					a.t_price as ord_amt,

					a.partial_stock_qty,
					a.partial_stock_wqty
				from ( 
					select 
						d.*
					from (
						select
							sum(oo.qty) as per_qty,
							sum(oo.recv_amt) as t_price,
							oo.$group_column,
							0 as qty,
							0 as total_ord_amt,
							sum(oo.price * oo.qty) as ord_amt,
							$max_column,
							oo.goods_opt,
							( @rank := @rank + 1 ) AS rank,
							( @real_rank := IF ( @last > $rank_column, @real_rank:=@real_rank+1, @real_rank ) ) AS real_rank,
							( @last := $rank_column) ,
							$partial_stock_qty
							$partial_stock_wqty
						from order_opt oo
							left outer join store s on oo.store_cd = s.store_cd
							inner join product_code pc2 on oo.prd_cd = pc2.prd_cd 
							, ( SELECT @rank := 0, @last := 0, @real_rank := 1 ) AS c
						where
							oo.ord_state = '30'
							and ( oo.clm_state = 0 or oo.clm_state = -30 or oo.clm_state = 90)
							and oo.ord_date >= '$sdate2'
							and oo.ord_date <= '$edate2'
							$in_where
						$group_by
						$in_orderby
					) d
					where 
						d.rank <= $page_size
				) as a
				inner join goods g on a.goods_no = g.goods_no and g.goods_sub = 0
				left outer join brand brd on g.brand = brd.brand
			)final
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
			
			$prd_cd		= $row['prd_cd'];
			$goods_no	= $row['goods_no'];
			
			$sql_tot_ord = null;
			$sql_in = null;
			$sql_out = null;

			//전체주문데이터
			if($group_type_condition == 'color_and_size') {
				$sql_tot_ord	= "
					select 
						ifnull(sum(qty), 0) as qty,  ifnull(sum(recv_amt),0) as total_ord_amt
					from order_opt 
					where 
						prd_cd = '$prd_cd' 
						and ord_state = '30' 
						and ( clm_state = 0 or clm_state = -30 or clm_state = 90)
					group by prd_cd
				";
			} else {
				$sql_tot_ord	= "
					select 
						ifnull(sum(qty), 0) as qty, ifnull(sum(recv_amt),0) as total_ord_amt
					from order_opt 
					where 
						goods_no = '$goods_no' 
						and ord_state = '30' 
						and ( clm_state = 0 or clm_state = -30 or clm_state = 90)
					group by goods_no
				";
			}
			
			$tot_ord = DB::selectOne($sql_tot_ord);

			$row['total_ord_qty']	= $tot_ord->qty;
			$row['total_ord_amt']	= $tot_ord->total_ord_amt;

			$tot_ord_sum_qty	+= $row['total_ord_qty'];
			$tot_ord_sum_amt	+= $row['total_ord_amt'];

			//입고 데이터
			if($group_type_condition == 'color_and_size') {
				$sql_in	= "
					select 
						ifnull(sum(p.qty), 0) as in_sum_qty, ifnull(sum(p.cost * p.qty), 0) as in_sum_amt
					from product_stock_order_product p
					inner join product_stock ps on p.prd_cd = ps.prd_cd
					where 
						p.state >= '30'
						and p.prd_cd = '$prd_cd'
					group by p.prd_cd
				";
			} else {
				$sql_in	= "
					select 
						ifnull(sum(p.qty), 0) as in_sum_qty, ifnull(sum(p.cost * p.qty), 0) as in_sum_amt
					from product_stock_order_product p
					inner join product_stock ps on p.prd_cd = ps.prd_cd
					where 
						p.state >= '30'
						and ps.goods_no = '$goods_no'
					group by ps.goods_no
				";
			}

			$tot_in = DB::selectOne($sql_in);

			$row['in_sum_qty']	=  $tot_in !== null ? $tot_in->in_sum_qty : 0;
			$row['in_sum_amt']	= $tot_in !== null ? $tot_in->in_sum_amt : 0;
			$row['in_sale_rate']	= ($row['in_sum_qty'] == 0)?0:round($row['total_ord_qty'] / $row['in_sum_qty'] * 100);

			$tot_in_sum_qty	+=  $tot_in !== null ?  $tot_in->in_sum_qty : 0;
			$tot_in_sum_amt	+=  $tot_in !== null ?  $tot_in->in_sum_amt : 0;

			//출고 데이터
			if($group_type_condition == 'color_and_size') {
				$sql_out	= "
					select 
						ifnull(sum(psr.qty), 0) as qty,
						left(min(psr.prc_rt), 10) as prc_rt
					from product_stock_release psr
					where 
						psr.state = '40'
						and psr.prd_cd = '$prd_cd'
					group by psr.prd_cd
				";
			} else {
				$sql_out	= "
					select 
						ifnull(sum(psr.qty), 0) as qty,
						left(min(psr.prc_rt), 10) as prc_rt
					from product_stock_release psr
					where 
						psr.state = '40'
						and psr.goods_no = '$goods_no'
					group by psr.goods_no
				";
			}

			$tot_out = DB::selectOne($sql_out);

			$row['ex_sum_qty']	= $tot_out !== null ? $tot_out->qty : 0;
			$row['ex_date']		= $tot_out !== null ? $tot_out->prc_rt: '';

			$tot_ex_sum_qty	+= $tot_out !== null ? $tot_out->qty : 0;

			$row['total_sale_rate']	= ($row['in_sum_qty'] == 0)?0:round($row['total_ord_qty'] / $row['in_sum_qty'] * 100);

			$result[] = $row;
		}

		// pagination
		$total = 0;
		$total_data = '';
		$page_cnt = 0;

		if ( $page == 1 ) {

			$query	= "
				select 
					$total_sale_rate
					final.in_sum_qty,
					final.in_sum_amt,
					final.ex_sum_qty,
					final.total_ord_qty,
					final.total_ord_amt,
					final.ord_qty,
					final.ord_amt,
					final.stock_qty,
					final.stock_wqty
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

						sum(a.partial_stock_qty) as stock_qty,
						sum(a.partial_stock_wqty) as stock_wqty,

						a.prd_cd,
						a.goods_no,
						g.brand,
						g.style_no,
						g.goods_nm,
						g.goods_nm_eng,
						g.type,
						g.goods_type,
						g.sale_stat_cl,
						g.com_type,
						g.com_id,
						g.com_nm,
						g.opt_kind_cd
					from ( 
						select 
							d.*
						from (
							select
								sum(oo.qty) as per_qty,
								sum(oo.recv_amt) as t_price,
								oo.$group_column,
								0 as qty,
								0 as total_ord_amt,
								sum(oo.price * oo.qty) as ord_amt,
								$max_column,
								oo.goods_opt,
								( @rank := @rank + 1 ) AS rank,
								( @real_rank := IF ( @last > $rank_column, @real_rank:=@real_rank+1, @real_rank ) ) AS real_rank,
								( @last := $rank_column) ,
								$partial_stock_qty
								$partial_stock_wqty
							from order_opt oo
								left outer join store s on oo.store_cd = s.store_cd
								inner join product_code pc2 on oo.prd_cd = pc2.prd_cd 
								, ( SELECT @rank := 0, @last := 0, @real_rank := 1 ) AS c
							where
								oo.ord_state = '30'
								and ( oo.clm_state = 0 or oo.clm_state = -30 or oo.clm_state = 90)
								and oo.ord_date >= '$sdate2'
								and oo.ord_date <= '$edate2'
								$in_where
							$group_by
							$in_orderby
						) d
						where 
							d.rank <= $page_size
					) as a
					inner join goods g on a.goods_no = g.goods_no and g.goods_sub = 0
					left outer join brand brd on g.brand = brd.brand
				) final
				where
					1=1
					$where
				$orderby
			";
		
			$row = DB::selectOne($query);

			$row !== null ? $row->in_sum_qty = $tot_in_sum_qty : 0;	// 총입고수량
			$row !== null ? $row->in_sum_amt = $tot_in_sum_amt : 0;	// 총입고금액
			$row !== null ? $row->ex_sum_qty = $tot_ex_sum_qty : 0;	// 총출고수량
			$row !== null ? $row->total_ord_qty = $tot_ord_sum_qty: 0;	// 총판매수량
			$row !== null ? $row->total_ord_amt = $tot_ord_sum_amt: 0;	// 총판매금액

			$total_data = $row;

			// if ($row) $total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $list_total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
				"total_data" => $total_data
			),
			"body" => $result
		]);
	}
}
