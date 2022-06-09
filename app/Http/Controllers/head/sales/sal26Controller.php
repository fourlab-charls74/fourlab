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

class sal26Controller extends Controller
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
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            "ad_types"      => SLib::getCodes("G_AD_TYPE")

        ];
        return view( Config::get('shop.head.view') . '/sales/sal26',$values);
    }

    public function search(Request $request){

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd = $request->input("brand_cd");
        $item	= $request->input("item");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $com_type = $request->input("com_type", "");
        $com_cd = $request->input("com_cd", "");
        $ad_type = $request->input("ad_type");
        $ad = $request->input("ad");

        $where = "";
        $inner_where = "";
        $pv_where = "";

        if ($style_no != "") $inner_where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        //if ($goods_no != "") $inner_where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";

        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ) {
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $inner_where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $inner_where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        if ($brand_cd != "") $inner_where .= " and g.brand ='$brand_cd'";
        if ($item != "") $inner_where .= " and g.opt_kind_cd = '$item' ";

        if ($com_type != "")	$inner_where .= " and g.com_type   = '$com_type' ";
        if ($com_cd != "")		$inner_where .= " and g.com_id     = '$com_cd' ";

        if ($ad_type != "") {
            $inner_where .= " and ad.type = '$ad_type'";
            $where .= " and a.type = '$ad_type'";
        }
        if ($ad != "") {
            $inner_where .= " and ad.ad = '$ad'";
            $where .= " and a.ad = '$ad'";
        }

        $sql = /** @lang text */
            "
			select
				if(a.ad = '','None',a.ad) as code,
				a.name as ad_name,
				a.type as ad_type,
                ifnull(p.pageview,0) as pageview,
				o.*, w.*
			from (
				select ad,type,name from ad
			) a left outer join (
				select ad,sum(pageview) as pageview
				from goods_track t inner join goods g
					on t.goods_no = g.goods_no
				where day >= '$sdate' and day <= '$edate' $pv_where
				group by ad
			) p on a.ad = p.ad left outer join (
				select
					ad.ad
					, ifnull(count(distinct(t.ord_no)), 0) as qty_cnt
					, ifnull(sum(o.qty), 0) as qty_all
					, ifnull(sum(o.qty * o.price), 0) as price_all
					, sum(if(ord_state = -20, o.qty, 0)) as qty_20_err
					, sum(if(ord_state = -20, o.qty * o.price, 0)) as price_20_err
					, sum(if(ord_state = -10, o.qty, 0)) as qty_10_cancel
					, sum(if(ord_state = -10, o.qty * o.price, 0)) as price_10_cancel
				from ad ad
					inner join order_track t on t.ad = ad.ad
					inner join order_opt o on o.ord_no = t.ord_no
					inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				where o.ord_date >= '$sdate'  and o.ord_date < date_add('$edate',interval 1 day)
				$inner_where
				group by ad.ad
			) o on a.ad = o.ad left outer join (
				select
					ad.ad
					, sum(if(w.ord_state = 10, ifnull(w.qty, 0), 0)) as qty_10
					, sum(if(w.ord_state = 10, ifnull(w.price * w.qty, 0), 0)) as price_10
					, sum(if(w.ord_state = 61, ifnull(w.qty, 0), 0)) * -1 as qty_61
					, sum(if(w.ord_state = 61, ifnull(w.price * w.qty, 0), 0)) as price_61
					, sum(if(w.ord_state = 60, ifnull(w.qty, 0), 0)) * -1 as qty_60
					, sum(if(w.ord_state = 60, ifnull((w.price * w.qty), 0), 0)) as price_60
				from ad ad
					inner join order_track t on ad.ad = t.ad
					inner join order_opt o on t.ord_no = o.ord_no
					inner join order_opt_wonga w on w.ord_opt_no=o.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				where
					o.ord_date >= '$sdate'  and o.ord_date < date_add('$edate',interval 1 day)
					$inner_where
				group by ad.ad
			) w on a.ad = w.ad
			where 1=1 $where
			order by o.qty_all desc, a.name asc   
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
