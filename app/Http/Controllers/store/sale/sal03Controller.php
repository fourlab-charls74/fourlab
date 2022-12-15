<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        $page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$orderby = '';
		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');

		if ($best_worst == 'B') {
			$orderby = sprintf("order by %s %s, total_sale_rate asc", $ord_field, "desc");
		} else if ($best_worst == 'W') {
			$orderby = sprintf("order by %s %s, total_sale_rate asc" , $ord_field, "asc");
		}else {
			$orderby = sprintf("order by %s %s, total_sale_rate asc", $ord_field, $ord);
		}

		$where	= "";
		if ($com_type != "") $where .= " and g.com_type = '$com_type' ";
		if ($store_type != "")	$where .= " and s.store_type = '" . $store_type . "' ";
		if ($store_cd != "")	$where .= " and m.store_cd = '" . $store_cd . "' ";
		if ($prd_cd != "")	$where .= " and o.prd_cd like '" . $prd_cd . "%' ";
		if ($com_id != "") $where .= " and g.com_id = '" . Lib::quote($com_id) . "'";
		if ($com_nm != "") $where .= " and g.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
		if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
		if ($brand_cd != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		} else if ($brand_cd == "" && $brand_nm != "") {
			$where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
		}
		if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

		if ($goods_nos != "") {
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
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
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
		
		$sql = /** @lang text */
            "
			select 
				a.goods_no,
				a.prd_cd,
				a.brand,
				a.brand_nm,
				a.style_no,
				a.goods_opt,
				a.img,
				a.goods_nm,
				a.goods_nm_eng,
				a.prd_cd_p,
				a.color,
				a.size,
				
				-- 입고
				b.qty as in_sum_qty,
				b.cost as in_sum_amt,
				round((b.qty / ps.qty) * 100) as in_sale_rate,

				-- 출고
				rel.qty as ex_sum_qty,
				rel.prc_rt as ex_date,
				
				-- 총판매
				ifnull(a.qty,'0') as total_ord_qty,
				ifnull(a.total_ord_amt ,'0') as total_ord_amt,
				ifnull(round((a.qty / ps.qty) * 100),0) as total_sale_rate,
				
				-- 기간판매
				a.per_qty as ord_qty,
				a.t_price as ord_amt,
				ifnull(round((a.per_qty / ps.qty) * 100),0) as sale_rate,

				-- 매장재고, 창고재고
				ifnull(ps.qty, '0') as stock_qty, 
				ifnull(ps.wqty, '0') as stock_wqty
			from ( 
					select 
						sum(c.qty) as per_qty,
						sum(c.price) as t_price,
						o.prd_cd,
						sum(w.qty) as qty,
						sum(o.recv_amt) as total_ord_amt,
						sum(w.recv_amt + w.point_apply_amt) as ord_amt,
						avg(w.wonga) as wonga,
						g.goods_type,
						o.goods_no, g.brand, b.brand_nm, g.style_no, o.goods_opt, g.img, g.goods_nm, g.goods_nm_eng,
						concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no 
						left outer join product_code pc on pc.prd_cd = o.prd_cd
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no 
						inner join goods g on o.goods_no = g.goods_no
						left outer join store s on m.store_cd = s.store_cd
						left outer join brand b on g.brand = b.brand
						left outer join (
							select 
								w.qty as qty,
								w.recv_amt as price,
								m.ord_no as ord_no
							from order_mst m
								inner join order_opt o on m.ord_no = o.ord_no 
								inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no 
							where m.ord_date >= '$sdate2' and m.ord_date <= '$edate2'
						) c on c.ord_no = o.ord_no
					where 
						w.`ord_state` in ( '30','60','61') 
						and o.prd_cd <> '' and o.ord_date >= '$sdate2' and o.ord_date <= '$edate2'
						$where
					group by o.prd_cd
				) as a 
				inner join product_stock ps on a.prd_cd = ps.prd_cd 
				left outer join ( 
					select 
						p.prd_cd
						, p.qty
						, p.cost
					from product_stock_order_product p
						inner join product_stock ps on p.prd_cd = ps.prd_cd
					where p.state = '30'
				) as b on a.prd_cd = b.prd_cd
				left outer join ( 
					select 
						psr.prd_cd,
						psr.qty as qty,
						left(psr.prc_rt, 10) as prc_rt
					from product_stock_release psr
				) as rel on a.prd_cd = rel.prd_cd
			$orderby 
			$limit
		";
		

		$result = DB::select($sql);


		// pagination
		$total = 0;
		$total_data = '';
		$page_cnt = 0;

		if ( $page == 1 ) {

			$query = /** @lang text */
            	"
				select
					count(a.cnt) as total
					-- 입고
					, sum(b.cost) as in_sum_amt
					, sum(b.qty) as in_sum_qty
					, sum(ifnull(round((b.qty / ps.qty) * 100), 0)) as in_sale_rate
					
					-- 출고
					, sum(rel.qty) as ex_sum_qty

					-- 총판매
					, sum(a.qty) as total_ord_qty
					, sum(a.total_ord_amt) as total_ord_amt
					, sum(ifnull(round((a.qty / ps.qty) * 100),0)) as total_sale_rate

					-- 기간판매
					, sum(a.ord_qty) as ord_qty
					, sum(a.sum_ord_amt) as ord_amt
					, sum(ifnull(round((a.sum_c_qty / ps.qty) * 100),0)) as sale_rate

					-- 매장재고, 창고재고
					, sum(ifnull(ps.qty, '0')) as stock_qty 
					, sum(ifnull(ps.wqty, '0')) as stock_wqty
				from
				(
					select 
						o.prd_cd,
						count(*) as cnt,
						sum(c.qty) as ord_qty,
						sum(c.price) as sum_ord_amt,
						sum(w.qty) as qty,
						sum(o.recv_amt) as total_ord_amt,
						sum(ifnull(round((w.qty / ps.qty) * 100),0)) as total_sale_rate, 
						sum(c.qty) as sum_c_qty
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						left outer join product_code pc on pc.prd_cd = o.prd_cd
						inner join product_stock ps on ps.prd_cd = pc.prd_cd 
						inner join goods g on o.goods_no = g.goods_no
						left outer join stock_product sp on sp.prd_cd = pc.prd_cd
						left outer join store s on m.store_cd = s.store_cd
						left outer join brand b on g.brand = b.brand
						left outer join (
							select 
								w.qty as qty,
								w.recv_amt as price,
								m.ord_no as ord_no
							from order_mst m
								inner join order_opt o on m.ord_no = o.ord_no 
								inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no 
							where m.ord_date >= '$sdate2' and m.ord_date <= '$edate2'
						) c on c.ord_no = o.ord_no
					where 
						w.`ord_state` in ( '30','60','61') 
						and o.prd_cd <> ''
						$where and o.ord_date >= '$sdate2' and o.ord_date <= '$edate2'
						group by o.prd_cd
						$orderby
						$limit
				) as a 
				inner join product_stock ps on a.prd_cd = ps.prd_cd
				left outer join ( 
					select 
						p.prd_cd
						, p.qty
						, p.cost
					from product_stock_order_product p
						inner join product_stock ps on p.prd_cd = ps.prd_cd
					where p.state = '30'
				) as b on a.prd_cd = b.prd_cd
				left outer join ( 
					select 
						psr.prd_cd,
						psr.qty as qty,
						left(psr.prc_rt, 10) as prc_rt
					from product_stock_release psr
				) as rel on a.prd_cd = rel.prd_cd
				$orderby
				$limit
			";

			$row = DB::selectOne($query);
			$total_data = $row;
            $total = $row->total;

			// if ($row) $total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
				"total_data" => $total_data
			),
			"body" => $result
		]);
	}
}
