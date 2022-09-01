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

		$values = [
			'code_kinds'	=> [],
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'store_types'	=> SLib::getStoreTypes(),
			// 'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'			=> SLib::getItems(),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE')
		];

		return view( Config::get('shop.store.view') . '/stock/stk01',$values);
	}

	public function search(Request $request)
	{
		$store_type = $request->input('store_type');
		$prd_cd = $request->input('prd_cd', '');

        $goods_stat = $request->input("goods_stat");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $goods_nm_eng = $request->input("goods_nm_eng");

		$com_id		= $request->input("com_cd");

        $page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$store_cds = $request->input('store_no') ?? [];
		$store_where = "";
		foreach ($store_cds as $key => $cd) {
			if ($key === 0) {
				$store_where .= "p.store_cd = '$cd'";
			} else {
				$store_where .= " or p.store_cd = '$cd'";
			}
		}
		if (count($store_cds) < 1) {
			$store_where = "1=1";
		}

		$where	= "";
		if ($store_type != "")	$where .= " and s.store_type = '" . $store_type . "' ";
		
		if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd = '" . $cd . "' ";
			}
			$where .= ")";
		}
        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

		if ($com_id != "") $where .= " and g.com_id = '" . Lib::quote($com_id) . "'";

        if (is_array($goods_stat)) {
            if (count($goods_stat) == 1 && $goods_stat[0] != "") {
                $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
            } else if (count($goods_stat) > 1) {
                $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
            }
        } else if($goods_stat != "") {
            $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        }

        if ($goods_nos != "") {
            $goods_no = $goods_nos;
        }
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if ( $goods_no != "" ) {
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
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
				select count(*) as total
				from product_stock_store p
					left outer join goods g on p.goods_no = g.goods_no
					left outer join store s on p.store_cd = s.store_cd
				where 1=1 $where
			";
			$row = DB::selectOne($query);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$sql = /** @lang text */
            "
			select 
				p.goods_no, goods_type, prd_cd, g.opt_kind_cd, opt_kind_nm, b.brand_nm, style_no, 
				c.code_val as sale_stat_cl, ifnull( c2.code_val, 'N/A') as goods_type_nm,
				if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				)) as img, goods_nm, goods_nm_eng, goods_opt, p.store_cd, store_nm, store_type, qty, wqty, p.rt, p.ut,
				g.goods_sh, g.price
			from product_stock_store p 
				left outer join goods g on p.goods_no = g.goods_no
				left outer join store s on p.store_cd = s.store_cd
				left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
				left outer join `code` c on c.code_kind_cd = 'G_GOODS_STAT' and sale_stat_cl = c.code_id
				left outer join `code` c2 on c2.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = c2.code_id 
				left outer join brand b on b.brand = g.brand
			where 1=1 $where and ($store_where)
			$orderby 
			$limit
		";

		$rows = DB::select($sql);
		$rows = collect($rows)->map(function ($row) { // shop image_svr 적용
			if ($row->img != "") {
				$row->img = sprintf("%s%s",config("shop.image_svr"), $row->img);
			}
			return $row;
		})->all();

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($rows)
			),
			"body" => $rows
		]);

	}
}
