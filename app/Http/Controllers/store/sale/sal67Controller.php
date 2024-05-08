<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal67Controller extends Controller
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
			'store_types'   => $store_types,
			'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'items'			=> SLib::getItems(),
			// 'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			// 'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view( Config::get('shop.store.view') . '/sale/sal67', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->sub(1, 'month')->format('Ymd'));
		$edate = $request->input('edate', date("Ymd"));
		$sdate = str_replace("-", "", $sdate);
		$edate = str_replace("-", "", $edate);

		$store_cd = $request->input('store_cd');
		$prd_cd = $request->input('prd_cd', '');
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$com_id = $request->input("com_cd");
		$com_nm = $request->input("com_nm");
		$com_type = $request->input("com_type");
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		$style_no = $request->input("style_no");
		$goods_no = $request->input("goods_no");
		$goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
		$item = $request->input("item");
		$brand_nm = $request->input("brand_nm");
		$brand_cd = $request->input("brand_cd");
		$goods_nm = $request->input("goods_nm");
		$goods_nm_eng = $request->input("goods_nm_eng");

		$page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		if ($com_type != "") $where .= " and g.com_type = '$com_type' ";
		if ($store_cd != "")	$where .= " and o.store_cd = '" . $store_cd . "' ";
		if ($store_channel != "") $where .= " and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= " and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		if ($prd_cd != '') {
			$prd_cd = preg_replace("/\s/", ",", $prd_cd);
			$prd_cd = preg_replace("/\t/", ",", $prd_cd);
			$prd_cd = preg_replace("/\n/", ",", $prd_cd);
			$prd_cd = preg_replace("/,,/", ",", $prd_cd);
			$prd_cds = explode(',', $prd_cd);
			if (count($prd_cds) > 1) {
				$prd_cds_str = "";
				if (count($prd_cds) > 500) array_splice($prd_cds, 500);
				for($i =0; $i < count($prd_cds); $i++) {
					$prd_cds_str.= "'".$prd_cds[$i]."'";

					if($i !== count($prd_cds) -1) {
						$prd_cds_str .= ",";
					}
				}
				$where .= " and o.prd_cd in ($prd_cds_str) ";
			} else {
				$where .= " and o.prd_cd like '" . Lib::quote($prd_cd) . "%' ";
			}
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

		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ( $page == 1 ) {
			$query = /** @lang text */
				"
				select
				count(cnt) as total
				from
				(
					select 
						count(*) as cnt
					from order_opt_wonga w
						inner join order_opt o on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no
						inner join product_code pc on pc.prd_cd = o.prd_cd
						left outer join store s on o.store_cd = s.store_cd
					where w.`ord_state_date` >= '$sdate' and w.ord_state_date <= '$edate' and w.`ord_state` in ( '30','60','61') 
						and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						and o.prd_cd <> '' $where
					group by o.prd_cd
				) a
			";
			$row = DB::selectOne($query);

			if ($row) $total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$sql = /** @lang text */
			"
			select 
				o.prd_cd,count(*) as cnt,
				sum(if(w.ord_state = '30', w.qty, w.qty * -1)) as qty,
				sum(w.qty * w.price) as amt,
				-- sum(w.recv_amt + w.point_apply_amt) as recv_amt,
				sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1)) as recv_amt,
				sum(w.qty * w.price - if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1)) as discount,
				avg(w.price) as avg_price,
				avg(w.wonga) as wonga,
				-- sum(w.wonga * w.qty) as sum_wonga,
				sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) as sum_wonga,

				( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) as recv_amt_novat,
				
				( 
					sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1
					-- sum(w.wonga * w.qty)
					- sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1))
				) as sales_profit,
				
				-- sum(w.qty * w.price - w.wonga * w.qty) as sales_profit,
	
				if( (sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) > 0 or ( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 - sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) ) > 0,
					(( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 - sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) ) / ( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) * 100),
					(( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 - sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) ) / ( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) * -100)
				)
				 as profit_rate,

				-- (sum(w.qty * w.price) / sum(w.qty * w.price - w.wonga * w.qty)) * 100 as profit_rate,
				o.goods_no, g.brand, b.brand_nm, g.style_no, o.goods_opt, g.img, g.goods_nm, g.goods_nm_eng,
				item.code_val as opt_kind_nm,
				pc.color, pc.prd_cd_p, pc.size
			from order_opt_wonga w 
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no 
				inner join goods g on o.goods_no = g.goods_no
				inner join product_code pc on pc.prd_cd = o.prd_cd
				inner join product_wonga pw on pc.prd_cd_p = pw.prd_cd_p
				inner join code item on item.code_kind_cd = 'PRD_CD_ITEM' and item.code_id = pc.item
				left outer join store s on o.store_cd = s.store_cd
				left outer join brand b on pc.brand = b.br_cd
			where w.`ord_state_date` >= '$sdate' and w.ord_state_date <= '$edate' and w.`ord_state` in ( '30','60','61') 
				and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
				and o.prd_cd <> '' $where
			group by o.prd_cd
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
