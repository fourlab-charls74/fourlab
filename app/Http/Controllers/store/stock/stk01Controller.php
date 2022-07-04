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
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'			=> SLib::getItems(),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),

		];

		return view( Config::get('shop.store.view') . '/stock/stk01',$values);
	}

	public function search(Request $request)
	{
		$store_type = $request->input('store_type');
		$store_cd = $request->input('store_cd');
		$store_nm = $request->input('store_nm');
		$prd_cd = $request->input('prd_cd');

        $goods_stat = $request->input("goods_stat");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
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
		if ( $store_type != "" )	$where .= " and s.store_type = '" . $store_type . "' ";
		if ( $store_cd != "" )	$where .= " and s.store_cd = '" . $store_cd . "' ";
		if ( $store_nm != "" )	$where .= " and s.store_nm like '%" . Lib::quote($store_nm) . "%' ";
		if ( $prd_cd != "" )	$where .= " and p.prd_cd = '" . $prd_cd . "' ";
        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

        if( is_array($goods_stat)) {
            if (count($goods_stat) == 1 && $goods_stat[0] != "") {
                $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
            } else if (count($goods_stat) > 1) {
                $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
            }
        } else if($goods_stat != ""){
            $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        }

        if($goods_nos        != ""){
            $goods_no = $goods_nos;
        }
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

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

        if ($type != "") $where .= " and g.type = '" . Lib::quote($type) . "' ";
        if ($goods_type != "") $where .= " and g.goods_type = '" . Lib::quote($goods_type) . "' ";

        $page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ( $page == 1 ) {
			$query = /** @lang text */
                "
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

		$sql = /** @lang text */
            "
			select 
				p.goods_no, goods_type, prd_cd, g.opt_kind_cd, opt_kind_nm, brand_nm, style_no, 
				c.code_val as sale_stat_cl_val, ifnull( c2.code_val, 'N/A') as goods_type_nm,
				img, goods_nm, goods_nm_eng, goods_opt, p.store_cd, store_nm, store_type, wqty, p.rt, p.ut
			from product_stock_store p 
				left join goods g on p.goods_no = g.goods_no
				left join store s on p.store_cd = s.store_cd
				left join opt o on g.opt_kind_cd = o.opt_kind_cd
				left join `code` c on c.code_kind_cd = 'G_GOODS_STAT' and sale_stat_cl = c.code_id
				left join `code` c2 on c2.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = c2.code_id 
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
