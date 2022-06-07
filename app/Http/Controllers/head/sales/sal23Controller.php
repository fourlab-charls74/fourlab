<?php

namespace App\Http\Controllers\head\sales;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;
use Carbon\Carbon;

class sal23Controller extends Controller
{
    // 일별 매출 통계
    public function index() {

        //return '일별매출통계';
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'sale_places'   => SLib::getSalePlaces(),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            "ad_types"      => SLib::getCodes("G_AD_TYPE")

        ];
        echo Config::get('shop.head.view');
        return view( Config::get('shop.head.view') . '/sales/sal23',$values);
    }

    public function search(Request $request){

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $item	= $request->input("item");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $sale_place = $request->input("sale_place", "");
        $com_type = $request->input("com_type", "");
        $com_id = $request->input("com_id", "");
        $ord_type = $request->input("ord_type", "");
        $ad_type = $request->input("ad_type");
        $ad = $request->input("ad");

        $where = "";
        $inner_join = "";

        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        //if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";

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

        if ($goods_nm != "") $where .= " and g.goods_nm like '%$goods_nm%' ";
        if ($brand_cd != "") $where .= " and g.brand ='$brand_cd'";
        if ($item != "") $where .= " and g.opt_kind_cd = '$item' ";

        if ($sale_place != "")	$where .= " and o.sale_place = '$sale_place' ";
        if ($com_type != "")	$where .= " and g.com_type   = '$com_type' ";
        if ($com_id != "")		$where .= " and g.com_id     = '$com_id' ";
        if ($ord_type != "") 	$where .= " and o.ord_type   = '$ord_type' ";

        if ($ad_type != ""){
            $inner_join = "
				inner join order_track t on t.ord_no = o.ord_no inner join ad ad on ad.ad = t.ad            
            ";
        }

        if ($ad_type != "") $where .= "and ad.type = '$ad_type'";
        if ($ad != "") $where .= "and ad.ad = '$ad'";

        $sql = /** @lang text */
            "
			select 
			    cb.brand_nm, opt.opt_kind_nm as item,
			    o.*, w.* 
			from (
				select
					g.goods_no, g.goods_sub, g.style_no, g.goods_nm, g.brand, g.opt_kind_cd
					, ifnull(count(distinct(o.ord_no)), 0) as qty_cnt
					, ifnull(sum(o.qty), 0) as qty_all
					, ifnull(sum(o.qty * o.price), 0) as price_all
					, sum(if(ord_state = -20, o.qty, 0)) as qty_20_err
					, sum(if(ord_state = -20, o.qty * o.price, 0)) as price_20_err
					, sum(if(ord_state = -10, o.qty, 0)) as qty_10_cancel
					, sum(if(ord_state = -10, o.qty * o.price, 0)) as price_10_cancel
				from order_opt o
					inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
					$inner_join
				where o.ord_date >= '$sdate'  and o.ord_date < date_add('$edate',interval 1 day)
				$where
				group by g.goods_no, g.goods_sub, g.style_no, g.goods_nm, g.brand, g.opt_kind_cd
			) o left outer join (
				select
					g.goods_no as ggn, g.goods_sub as ggs
					, sum(if(w.ord_state = 10, ifnull(w.qty, 0), 0)) as qty_10
					, sum(if(w.ord_state = 10, ifnull(w.price * w.qty, 0), 0)) as price_10
					, sum(if(w.ord_state = 61, ifnull(w.qty, 0), 0)) * -1 as qty_61
					, sum(if(w.ord_state = 61, ifnull(w.price * w.qty, 0), 0)) as price_61
					, sum(if(w.ord_state = 60, ifnull(w.qty, 0), 0)) * -1 as qty_60
					, sum(if(w.ord_state = 60, ifnull((w.price * w.qty), 0), 0)) as price_60
				from order_opt_wonga w
					inner join order_opt o on w.ord_opt_no=o.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
					$inner_join
				where
					o.ord_date >= '$sdate'  and o.ord_date < date_add('$edate',interval 1 day)
					$where
				group by ggn, ggs
			) w on o.goods_no = ggn and o.goods_sub = ggs
			left outer join brand cb on cb.brand = o.brand
			left outer join opt opt on opt.opt_kind_cd = o.opt_kind_cd and opt.opt_id = 'K'     
			order by item,brand_nm       
        ";

        $rows = DB::select($sql);
        $result = [];

        foreach($rows as $row) {
            $row->qty_sale = $row->qty_10 + $row->qty_61 - $row->qty_60;
            $row->price_sale = $row->price_10 + $row->price_61 - $row->price_60;
            $result[] = $row;
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

}
