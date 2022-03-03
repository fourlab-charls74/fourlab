<?php

namespace App\Http\Controllers\partner\order;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ord72Controller extends Controller
{
    // 월별 매출 통계 컨트롤러
    public function index(Request $request) {
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(6, 'month')->format('Y-m-d');
        $style_no	= $request->input('style_no');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'style_no' => $style_no,
            'items' => SLib::getItems(),
            'sale_places' => SLib::getSalePlaces(),
            'md_ids' => SLib::getMDs(),
        ];
        return view( Config::get('shop.partner.view') . '/order/ord72',$values);
    }

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;

        $sdate = $request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $sdate = str_replace("-","",$sdate);
        $edate = str_replace("-","",$edate);

        if(strlen($sdate) == 6){
            $sdate = $sdate . "01";
        }
        if(strlen($edate) == 6){
            $edate = $edate . "31";
        }

        $m_cat_cd		= $request->input("m_cat_cd");
        $brand_cd		= $request->input("brand_cd");
        $goods_nm		= $request->input("goods_nm");
        $goods_no		= $request->input("goods_no");
        $goods_sub	= $request->input("goods_sub");
        $opt_kind_cd	= $request->input("opt_kind_cd");
        $md_id		= $request->input("md_id");
        $style_no		= $request->input("style_no");
        $sale_place	= $request->input("sale_place");

        $where = "";
        if ($style_no != "") $where .= " and ~~~~~~~ like '%" . Lib::quote($style_no) . "%' ";

        //
        // 다른페이지에서 값이 넘어왔을경우
        //
//        if($S_GOODS_NO != "" && $S_GOODS_SUB != ""){
//            $sql = "select goods_nm from goods where goods_no = '$S_GOODS_NO' and goods_sub = '$S_GOODS_SUB' ";
//            $rs = &$conn->Execute($sql);
//            if(!$rs->EOF) {
//                $row = $rs->fields;
//                $S_GOODS_NM = trim($row["goods_nm"]);
//            }
//        }

        $inner_where = "";
        $inner_where .= " and o.com_id='$com_id' ";

        if($style_no != ""){
            $inner_where .= " and g.style_no like '". Lib::quote($style_no)."%' ";
        }

        if($goods_nm != ""){
            if($goods_no != "" && $goods_sub != ""){
                $inner_where .= " and g.goods_no= '" . Lib::quote($goods_no, '\'') . "' and g.goods_sub='" . addCslashes($goods_sub) . "' ";
            } else{
                $inner_where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
            }
        }

        //옵션(품목)
        if ($opt_kind_cd != ""){
            $inner_where .= " and g.opt_kind_cd = '" . Lib::quote($opt_kind_cd) . "' ";
        }

        if($brand_cd != ""){
            $inner_where .= " and g.brand ='" . Lib::quote($brand_cd) . "'";
        }

        if($sale_place != ""){
            $inner_where .= " and o.sale_place = '" . Lib::quote($sale_place) . "' ";
        }

        if($md_id != ""){
            $inner_where .= " and o.md_id = '" . Lib::quote($md_id) . "' ";
        }

        if($m_cat_cd != ""){
            $inner_where .= " and g.rep_cat_cd like '" . Lib::quote($m_cat_cd) . "%' ";
        }

		$query = "
            select
                ord_date,o.*,w.*
            from (
                select date_format(d,'%Y%m') as ord_date
                from mdate where  d >= '$sdate'  and d <= '$edate'
                group by ord_date
                order by ord_date desc
            ) a left outer join (
                select
                    date_format(o.ord_date, '%Y%m') as ord_state_date
                    , ifnull(count(distinct(o.ord_no)), 0) as qty_cnt
                    , ifnull(sum(o.qty), 0) as qty_all
                    , ifnull(sum(o.qty * o.price), 0) as price_all
                    , sum(if(ord_state = -20, o.qty, 0)) as qty_20_err
                    , sum(if(ord_state = -20, o.qty * o.price, 0)) as price_20_err
                    , sum(if(ord_state = -10, o.qty, 0)) as qty_10_cancel
                    , sum(if(ord_state = -10, o.qty * o.price, 0)) as price_10_cancel
                from order_opt o
                    inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                where o.ord_date >= '$sdate'  and o.ord_date <= date_add('$edate',interval 1 day)
                $inner_where
                group by ord_state_date
            ) o on a.ord_date = o.ord_state_date left outer join (
                select
                    date_format(o.ord_date,'%Y%m') as w_ord_date
                    , sum(if(w.ord_state = 10, ifnull(w.qty, 0), 0)) as qty_10
                    , sum(if(w.ord_state = 10, ifnull(w.price * w.qty, 0), 0)) as price_10
                    , sum(if(w.ord_state = 61, ifnull(w.qty, 0), 0)) * -1 as qty_61
                    , sum(if(w.ord_state = 61, ifnull(w.price * w.qty, 0), 0)) as price_61
                    , sum(if(w.ord_state = 60, ifnull(w.qty, 0), 0)) * -1 as qty_60
                    , sum(if(w.ord_state = 60, ifnull(w.price * w.qty, 0), 0)) as price_60
                from order_opt_wonga w
                    inner join order_opt o on w.ord_opt_no=o.ord_opt_no
                    inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                where
                    o.ord_date >= '$sdate'  and o.ord_date < date_add('$edate',interval 1 day)
                    $inner_where
                group by w_ord_date
            ) w on a.ord_date = w.w_ord_date
        ";

		//dd($query);
        //echo "<pre>$query</pre>";exit;

		$result = DB::select($query,['com_id1' => $com_id,'com_id2' => $com_id]);



		foreach($result as $row){
			$row->qty_sale = $row->qty_10 + $row->qty_61 + $row->qty_60;
			$row->price_sale = $row->price_10 + $row->price_61 + $row->price_60;
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
