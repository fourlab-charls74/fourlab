<?php

namespace App\Http\Controllers\head\product;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;

class prd09Controller extends Controller
{
  //
	public function index() {
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'com_types' => SLib::getCodes('G_COM_TYPE'),
            'ord_kinds' => SLib::getCodes('G_ORD_KIND'),
            'sale_placies' => SLib::getSalePlaces(),
            'goods_stats' => SLib::getCodes('G_GOODS_STAT')
        ];
        // dd($values);
		return view( Config::get('shop.head.view') . '/product/prd09',$values);
	}

	public function search(){
		$edate			= Request("edate",date("Ymd"));
		$sdate			= Request("sdate");
		$goods_stat		= Request("goods_stat");
		$goods_no		= Request("goods_no");
		$style_no		= Request("style_no");
		$com_type		= Request("com_type");
		$com_id			= Request("com_cd");
		$com_nm			= Request("com_nm");
		$brand_cd		= Request("brand_cd");
		$brand_nm		= Request("brand_nm");
		$goods_nm		= Request("goods_nm");
		$opt_kind_cd	= Request("opt_kind_cd");
		$order			= Request("order");
		$cart			= Request("cart");
		$wish			= Request("wish");
        
		$where = "";
		if($goods_stat != "")		$where .= " and g.sale_stat_cl = '$goods_stat' ";
		if($com_type != "")		$where .= " and c.com_type = '$com_type' ";
		if($com_id != "")			$where .= " and c.com_id = '$com_id' ";
		if($com_nm != "")			$where .= " and c.com_nm = '$com_nm' ";
		if($opt_kind_cd != "")	$where .= " and g.opt_kind_cd = '$opt_kind_cd' ";

		if($brand_cd != ""){
			$where .= " and b.brand ='$brand_cd'";
		} else if ($brand_cd == "" && $brand_nm != ""){
			$where .= " and b.brand ='$brand_cd'";
		}

		if($goods_nm != "")		$where .= " and g.goods_nm like '%$goods_nm%'";
		if($style_no != "")		$where .= " and g.style_no like '$style_no%'";
		if($goods_no != "")		$where .= " and g.goods_no = '$goods_no' ";

		if($order == "Y" )		$where .= " and s.qty_o > 0 ";
		if($cart == "Y" )			$where .= " and s.qty_c > 0 ";
		if($wish == "Y" )			$where .= " and s.qty_w > 0 ";

		$sql = "
			select
				g.goods_no, g.goods_sub, type.code_val goods_type_nm, c.com_nm, opt.opt_kind_nm,
				b.brand_nm, g.style_no, g.goods_nm, stat.code_val as goods_stat,
				(select sum(good_qty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub) as good_qty,
				(select sum(wqty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub ) as wqty,
				s.qty_o, s.qty_c, s.qty_w, g.goods_type, c.com_type
			from goods g
				inner join (
					select
						goods_no, goods_sub,
						sum(if(type = 'o', ifnull(a.qty, 0), 0)) as qty_o,
						sum(if(type = 'w', ifnull(a.qty, 0), 0)) as qty_w,
						sum(if(type = 'c', ifnull(a.qty, 0), 0)) as qty_c
					from (
						select 'c' as type, goods_no, goods_sub, goods_cnt as qty from cart
						union all
						select 'w' as type, goods_no, goods_sub, 1 as qty from wishlist
						where regi_date >= '$sdate' and regi_date < DATE_ADD($edate, INTERVAL 1 DAY) and goods_opt <> ''
						union all
						select 'o' as type, goods_no, goods_sub, qty from order_opt
						where ord_date >= '$sdate' and ord_date < DATE_ADD($edate, INTERVAL 1 DAY) and ord_state >= '10'
					) a
					group by goods_no, goods_sub
				) s on s.goods_no = g.goods_no and s.goods_sub = g.goods_sub
				inner join company c on c.com_id = g.com_id
				inner join brand b on b.brand = g.brand and b.brand_type = 'S'
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
			where 1=1
				$where
			order by ( s.qty_c + s.qty_w ) desc, s.qty_o desc
        ";

        $result = DB::select($sql);

        return response()->json([
          "code" => 200,
          "head" => [
            'total' => count($result)
          ],
          "body" => $result
        ]);
	}
}
