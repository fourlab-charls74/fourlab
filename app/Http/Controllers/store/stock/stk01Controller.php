<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk01Controller extends Controller
{
	public function index() {

        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $com_types	= [];
        $event_cds	= [];
        //판매유형
        $sell_types	= [];
        $code_kinds	= [];

		$values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
		];

		return view( Config::get('shop.store.view') . '/stock/stk01',$values);
	}

	public function search(Request $request)
	{
		$store_type = $request->input('store_type');
		$store_cd = $request->input('store_cd');
		$store_nm = $request->input('store_nm');
		$prd_cd = $request->input('prd_cd');

		$page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		if ( $store_type != "" )	$where .= " and s.store_type = '" . $store_type . "' ";
		if ( $store_nm != "" )	$where .= " and s.store_nm like '%" . Lib::quote($store_nm) . "%' ";
		if ( $prd_cd != "" )	$where .= " and p.prd_cd = '" . $prd_cd . "' ";

		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ( $page == 1 ) {
			$query = "
				select count(*) as total
				from product_stock_store p
				left join goods g on p.goods_no = g.goods_no
				left join store s on p.store_cd = s.store_cd
				where 1=1 $where
			";
			$row = DB::selectOne($query);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$sql = "
			select 
				p.goods_no, goods_type, prd_cd, opt_kind_cd, brand_nm, style_no, sale_stat_cl, 
				img, goods_nm, goods_nm_eng, goods_opt, p.store_cd, store_nm, store_type, wqty, rt, ut
			from product_stock_store p 
			left join goods g on p.goods_no = g.goods_no
			left join store s on p.store_cd = s.store_cd
			where 1=1
			$where
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
