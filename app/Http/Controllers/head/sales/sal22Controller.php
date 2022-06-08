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

class sal22Controller extends Controller
{
    // 일별 매출 통계
    public function index(Request $req) {

        //return '일별매출통계';
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');

        $ad_type = $req->input("ad_type");
        $ad = $req->input("ad");

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'sale_places'   => SLib::getSalePlaces(),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            "ad_types"      => SLib::getCodes("G_AD_TYPE"),
            "ad_type"       => $ad_type,
            "ad"            => $ad
        ];
        echo Config::get('shop.head.view');
        return view( Config::get('shop.head.view') . '/sales/sal22',$values);
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
			    date_format(ord_date,'%Y%m%d') as date,
				date_format(ord_date,'%m') as month,
				date_format(ord_date,'%d') as day,
				date_format(ord_date,'%a') as yoil_nm,
				DAYOFWEEK(ord_date) as yoil,
			    o.*, w.* 
			from (
				select d as ord_date from mdate where  d >= '$sdate'  and d <= '$edate' order by d desc 
			) a left outer join (
				select
					date_format(o.ord_date, '%Y%m%d') as ord_state_date
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
				group by ord_state_date
			) o on a.ord_date = o.ord_state_date left outer join (
				select
					date_format(o.ord_date, '%Y%m%d') as w_ord_date
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
				group by w_ord_date
			) w on a.ord_date = w.w_ord_date            
        ";
            //echo "<pre>$sql</pre>";

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
