<?php

namespace App\Http\Controllers\head\stock;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;

class stk20Controller extends Controller
{
    public function index(Request $request) {

        $mutable = Carbon::now();
        $sdate = $request->input("sdate", now()->sub(3, 'day')->format('Ymd'));
        $edate = $request->input("edate", date("Ymd"));

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
            'alter_reasons' => SLib::getCodes('G_JAEGO_REASON'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'month3' => (int)date("m"),
            'month2' => (int)$mutable->sub(1, 'month')->format('m'),
            'month1' => (int)$mutable->sub(2, 'month')->format('m'),
        ];
        return view( Config::get('shop.head.view') . '/stock/stk20',$values);
    }

    public function search(Request $request){

        $page = $request->input('page',1);
        $total = $request->input('total',0);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit',100);
        $ord = $request->input('ord','desc');
        $ord_field = $request->input('ord_field','g.goods_no');
        $str_order_by = $ord_field." ".$ord;

        $edate          = $request->input("edate", date("Ymd"));
        $sdate          = $request->input("sdate", now()->sub(1, 'month')->format('Ymd'));
        $goods_stat	    = $request->input("goods_stat");
        $goods_stat_ex	= $request->input("goods_stat_ex");
        $style_no		= $request->input("style_no");
        $goods_no		= $request->input("goods_no");
        $item       	= $request->input("item");
        $com_type       = $request->input("com_type");
        $com_id         = $request->input("com_cd");
        $brand_nm		= $request->input("brand_nm");
        $brand_cd		= $request->input("brand_cd");
        $goods_nm		= $request->input("goods_nm");
        $is_unlimited	= $request->input("is_unlimited");
        $wqty_h	        = $request->input("wqty_h");
        $wqty_l	    	= $request->input("wqty_l");
        $qty_h	    	= $request->input("qty_h");
        $qty_l	    	= $request->input("qty_l");
        $ex_trash       = $request->input("ex_trash");
        $ex_soldout     = $request->input("ex_soldout");

        $where = "";
        if( $style_no != "" )	    $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if( $com_id != "" )	    $where .= " and g.com_id = '" . Lib::quote($com_id) . "' ";
        //if( $goods_no != "" )		$where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
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


        if( $item != "" )	$where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if( $com_type != "" )	$where .= " and g.com_type = '" . Lib::quote($com_type) . "' ";
        if( $brand_cd != "" ){
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != ""){
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if( $goods_nm != "" )		$where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if( $is_unlimited != "" )	$where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";
        if( $wqty_l != "" )		    $where .= " and s.wqty >= '" . Lib::quote($wqty_l) . "' ";
        if( $wqty_h != "" )	        $where .= " and s.wqty <= '" . Lib::quote($wqty_h) . "' ";
        if( $qty_l != "" )		    $where .= " and s.good_qty >= '" . Lib::quote($qty_l) . "' ";
        if( $qty_h != "" )		    $where .= " and s.good_qty <= '" . Lib::quote($qty_h) . "' ";
        if( $ex_trash == "1")       $where .= " and g.sale_stat_cl <> -90";
		if( $ex_soldout == "1")     $where .= " and g.sale_stat_cl <> 30";
        if( is_array($goods_stat)) {
            if (count($goods_stat) == 1 && $goods_stat[0] != "") {
                $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
            } else if (count($goods_stat) > 1) {
                $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
            }
        } else if($goods_stat != ""){
            $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
        }
        if( $goods_stat_ex != "" )	    $where .= " and g.sale_stat_cl > 0 ";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $page_cnt = 0;
		$total_good_qty_cnt = 0;
		$total_wonga = 0;
		
        if($page == 1){
			$query = /** @lang text */
				"
                select 
                    count(g.goods_no) as total,
                    sum(ifnull(if(g.is_unlimited = 'Y', '∞', s.good_qty), 0)) as good_qty,
                    sum(gs2.totalWonga) as total_wonga
                from 
                    goods g 
                    inner join goods_summary s ON s.goods_no = g.goods_no AND s.goods_sub = g.goods_sub
                    left outer join goods_stock gs2 ON  gs2.goods_no = s.goods_no AND gs2.goods_sub = s.goods_sub and gs2.goods_opt = s.goods_opt 
                where 1=1 $where
			";
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt=(int)(($total-1)/$page_size) + 1;
			$total_good_qty_cnt = $row[0]->good_qty;
			$total_wonga = $row[0]->total_wonga;
        }

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = /** @lang text */
            "
          select
            a.com_nm, o.opt_kind_nm, b.brand_nm, g.style_no, g.com_type,
            cd.code_val as goods_type_nm, cd2.code_val as is_unlimited_nm,
            g.goods_no, g.goods_sub, g.goods_nm,g.head_desc,g.goods_nm_eng,
            cd3.code_val as sale_stat_cl_nm,
            g.goods_sh,g.price,g.wonga,(g.price-g.wonga) as margin_amt,if(g.price > 0,((g.price-g.wonga)/g.price)*100,0) as margin_rate,
            g.price * a.wqty as tot_sales, ( g.price * a.wqty - a.totalwonga ) as tot_margin,
            a.goods_opt, '1' as level,
            ifnull(if(g.is_unlimited = 'Y', '∞', a.good_qty), 0) as good_qty,
            ifnull(a.wqty,0) as wqty,
            a.sale_qty1,a.sale_qty2,a.sale_qty3,a.sale_qty,round(a.sale_qty/30,2) as avg_qty,
            a.expect_day,g.new_product_day,
            a.maxinputdate,a.stock_qty,a.req_date,a.totalwonga,
            0 as sale_sum_qty, 0 as sale_sum_amt,
            '' as dc_sale_qty,'' as top20p_sale_qty,'' as top20p_sale_amt,
            '' as sale_ord_type_12_qty,
            '' as sale_ord_type_13_qty,
            '' as sale_ord_type_16_qty,
            '' as sale_ord_type_17_qty,
            '' as sale_ord_type_roket_qty,
            '' as clm_qty,'' as clm_4_qty,'' as clm_5_qty,
            '' as max_ord_date,
            '' as img2,
            if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
            )) as img
          from (
            select
                g.goods_no,g.goods_sub,c.com_nm, s.goods_opt,s.good_qty,s.wqty,
                gsr.sale_qty1,gsr.sale_qty2,gsr.sale_qty3,gsr.sale_qty,
                if(s.wqty = 0,0,ifnull(round(s.wqty/round(gsr.sale_qty/30,2),2),999999.00)) as expect_day,
                gs.maxinputdate,gs.stock_qty,gs.req_date,gs.totalwonga
            from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
                inner join company c on c.com_id = g.com_id
                left outer join goods_sale_recent gsr on ( s.goods_no = gsr.goods_no AND s.goods_sub = gsr.goods_sub AND s.goods_opt = gsr.goods_opt )     
                inner join goods_stock gs ON ( gs.goods_no = s.goods_no AND gs.goods_sub = s.goods_sub and gs.goods_opt = s.goods_opt)                          
            where 1=1 $where
            order by $str_order_by
            $limit
          ) a inner join goods g on g.goods_no = a.goods_no and g.goods_sub = a.goods_sub
          left outer join brand b on g.brand = b.brand
          inner join opt o on g.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
          inner join code cd on cd.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = cd.code_id
          inner join code cd2 on cd2.code_kind_cd = 'G_IS_UNLIMITED' and g.is_unlimited = cd2.code_id
          inner join code cd3 on cd3.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = cd3.code_id
        ";
        //echo "<pre>$query</pre>";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = [];
        $goods = [];
        $goods_query = "";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $key = sprintf("%s_%s",$row["goods_no"],$row["goods_opt"]);
            $goods[$key] = count($result);
            $result[] = $row;
            if($goods_query === ""){
            } else {
                $goods_query .= "union";
            }
            $goods_query .= sprintf(" SELECT %s AS goods_no, 0 AS goods_sub,'%s' AS goods_opt\n",$row["goods_no"],$row["goods_opt"]);

        }

        if($goods_query != ""){
            $query = /** @lang text */
                "
                SELECT 
                    g.goods_no,g.goods_sub,g.goods_opt,
                    SUM(IF(o.ord_type = 12,qty,0)) AS sale_ord_type_12_qty,
                    SUM(IF(o.ord_type = 13,qty,0)) AS sale_ord_type_13_qty,
                    SUM(IF(o.ord_type = 16,qty,0)) AS sale_ord_type_16_qty,
                    SUM(IF(o.ord_type = 17,qty,0)) AS sale_ord_type_17_qty,
                    SUM(IF(o.ord_type = 16 AND m.`sale_place` = 'roket',qty,0)) AS sale_ord_type_roket_qty,
                    SUM(IF((o.dc_amt + o.coupon_amt) > 0,qty,0)) AS dc_sale_qty,
                    SUM(o.qty) AS sale_sum_qty,
                    '' as top20p_sale_amt,
                    SUM((o.recv_amt + o.point_amt) * o.qty) AS sale_sum_amt,                    
                    SUM(IF(c.`clm_no` > 0,1,0)) AS clm_qty,	
                    SUM(IF(c.`clm_reason` = 4,1,0)) AS clm_4_qty,	
                    SUM(IF(c.`clm_reason` = 5,1,0)) AS clm_5_qty,	
                    MAX(o.ord_date) AS max_ord_date
                FROM (
                    $goods_query
                ) g INNER JOIN order_opt o ON g.goods_no = o.goods_no AND g.goods_sub = o.goods_sub AND g.goods_opt = o.goods_opt
                 INNER JOIN order_mst m ON m.ord_no = o.ord_no
                 LEFT OUTER JOIN claim c ON o.ord_opt_no = c.`ord_opt_no`
                WHERE o.ord_date >= '$sdate' AND o.ord_date < DATE_ADD('$edate', INTERVAL 1 DAY)
                GROUP BY o.goods_no,o.goods_sub,o.goods_opt    
                order by sale_sum_amt desc
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $cnt = 1;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if($total > 0 && ($cnt / $total * 100 < 20)) $row["top20p_sale_amt"] = '○';
                $key = sprintf("%s_%s",$row["goods_no"],$row["goods_opt"]);
                $index = $goods[$key];
                $result[$index] = array_merge($result[$index],$row);

            }
        }

        if($goods_query != ""){
            $sd = str_replace("-","",$sdate);
            $ed = str_replace("-","",$edate);
            $query = /** @lang text */
                "
                SELECT 
                    g.goods_no,g.goods_sub,g.goods_opt,
                    SUM(s.qty) AS stock_qty
                FROM (
                    $goods_query
                ) g INNER JOIN stock_product s ON g.goods_no = s.goods_no AND g.goods_sub = s.goods_sub AND g.goods_opt = s.opt_kor
                WHERE stock_date >= '$sd' AND s.stock_date <= '$ed'
                GROUP BY g.goods_no,g.goods_sub,g.goods_opt    
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $cnt = 1;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $key = sprintf("%s_%s",$row["goods_no"],$row["goods_opt"]);
                $index = $goods[$key];
                $result[$index] = array_merge($result[$index],$row);

            }
        }



        /*        -- explain
        SELECT
            o.goods_no,o.goods_sub,o.goods_opt,
            SUM(o.qty) AS sale_qty,
            SUM(IF(o.ord_type = 12,qty,0)) AS sale_ord_type_12_qty,
            SUM(IF(o.ord_type = 13,qty,0)) AS sale_ord_type_13_qty,
            SUM(IF(o.ord_type = 16,qty,0)) AS sale_ord_type_16_qty,
            SUM(IF(o.ord_type = 17,qty,0)) AS sale_ord_type_17_qty,
            SUM(IF(o.ord_type = 16 AND m.`sale_place` = 'roket',qty,0)) AS sale_ord_type_roket_qty,
            MAX(o.ord_date) AS max_ord_date
        FROM (
            SELECT 124836 AS goods_no, 0 AS goods_sub,'NONE' AS goods_opt
            UNION SELECT -1 AS goods_no, 0 AS goods_sub,'NONE' AS goods_opt
        ) g INNER JOIN order_opt o ON g.goods_no = o.goods_no AND g.goods_sub = o.goods_sub AND g.goods_opt = o.goods_opt
         INNER JOIN order_mst m ON m.ord_no = o.ord_no
        WHERE o.ord_date >= '20210601' AND o.ord_date <= DATE_FORMAT(NOW(),'%Y%m%d')
        GROUP BY o.goods_no,o.goods_sub,o.goods_opt*/


        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
				"total_good_qty_cnt" => $total_good_qty_cnt,
				"total_wonga" =>  $total_wonga,
                "page_cnt" => $page_cnt,
                "page_total" => count($result),
            ),
            "body" => $result
        ]);
    }
}
