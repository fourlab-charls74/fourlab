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
		$sdate = str_replace("-", "", $sdate);
		$edate = str_replace("-", "", $edate);

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

        $type = $request->input("type");
        $goods_type = $request->input("goods_type");

        $page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		if ($com_type != "") $where .= " and g.com_type = '$com_type' ";
		if ($store_type != "")	$where .= " and s.store_type = '" . $store_type . "' ";
		if ($store_cd != "")	$where .= " and m.store_cd = '" . $store_cd . "' ";
		if ($prd_cd != "")	$where .= " and o.prd_cd = '" . $prd_cd . "' ";
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

		$total = 0;
		$page_cnt = 0;

		if ( $page == 1 ) {
			$query = /** @lang text */
            	"
				select
					count(a.cnt) as total
				from
				(
					select 
						o.prd_cd, count(*) as cnt
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no
						left outer join store s on m.store_cd = s.store_cd
						left outer join brand b on g.brand = b.brand
						left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
						left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
					where w.`ord_state_date` >= '$sdate' and w.ord_state_date <= '$edate' and w.`ord_state` in ( '30','60','61') 
						and o.prd_cd <> ''
						$where
					group by o.prd_cd
				) as a inner join product_stock ps on a.prd_cd = ps.prd_cd
			";

			$row = DB::selectOne($query);

			if ($row) $total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}
		
		$sql = /** @lang text */
            "
			select 
				a.*, 
				b.in_sum_qty, b.in_sum_amt,
				ifnull(ps.qty, '0') as stock_qty, ifnull(ps.wqty, '0') as stock_wqty,
				round((a.ord_qty / ifnull(b.in_sum_qty, '0') * 100), 2) as in_sale_rate,
				round((a.ord_qty / ifnull(ps.wqty, '0') * 100), 2) as sale_rate
			from ( 
					select 
						o.prd_cd,
						sum(w.qty) as ord_qty,
						sum(w.recv_amt + w.point_apply_amt) as ord_amt,
						avg(w.wonga) as wonga,
						g.goods_type, c.code_val as sale_stat_cl_val, c2.code_val as goods_type_nm,
						o.goods_no, g.brand, b.brand_nm, g.style_no, o.goods_opt, g.img, g.goods_nm, g.goods_nm_eng
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no 
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no
						left outer join store s on m.store_cd = s.store_cd
						left outer join brand b on g.brand = b.brand
						left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
						left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
					where w.`ord_state_date` >= '$sdate' and w.ord_state_date <= '$edate' and w.`ord_state` in ( '30','60','61') 
						and o.prd_cd <> '' $where
					group by o.prd_cd
				) as a inner join product_stock ps on a.prd_cd = ps.prd_cd left outer join 
				( 
					select 
						sp.prd_cd as prd_cd, 
						sum(ps.in_qty) as in_sum_qty, sum(sp.cost) as in_sum_amt
					from stock_product sp
						inner join product_stock ps on sp.prd_cd = ps.prd_cd
					group by sp.prd_cd
				) as b on a.prd_cd = b.prd_cd
			$orderby 
			$limit
		";

		$result = DB::select($sql);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body" => $result
		]);
	}
}
