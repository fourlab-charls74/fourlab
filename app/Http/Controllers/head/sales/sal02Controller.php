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

class sal02Controller extends Controller
{
    // 일별 매출 통계
    public function index(Request $req) {

        //return '일별매출통계';
        $mutable = Carbon::now();
        $sdate	 = $mutable->sub(1, 'month')->format('Y-m-d');

		$ord_state	= $req->input('ord_state');
		$ord_type	= $req->input('ord_type');

		if( $ord_state != "" || $ord_type != "" )
			$pop_search	= "Y";
		else
			$pop_search = "N";

        $values = [
            'sdate' 		=> $sdate,
            'edate' 		=> date("Y-m-d"),
            'items' 		=> SLib::getItems(),
			'sale_places'   => SLib::getSalePlaces(),
			'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
			'ord_state'		=> $ord_state,
			'ord_type'		=> $ord_type,
			'pop_search'	=> $pop_search
        ];
        echo Config::get('shop.head.view');
        return view( Config::get('shop.head.view') . '/sales/sal02',$values);
    }

    public function search(Request $request){

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd 		= $request->input("brand_cd");
        $goods_nm 		= $request->input("goods_nm");
        $item			= $request->input("item");
        $ord_state		= $request->input("ord_state");
		$ord_type 		= $request->input("ord_type", "");
		$sale_place 	= $request->input("sale_place", "");
		$stay_pay_type 	= $request->input("stat_pay_type");

        $inner_where = "";
		$inner_where2	= "";	//매출

        if($goods_nm != ""){
            $inner_where .= " and g.goods_nm like '%$goods_nm%' ";
        }

        if($brand_cd != ""){
            $inner_where .= " and g.brand ='$brand_cd'";
        }

        //옵션(품목)
        if ($item != ""){
            $inner_where .= " and g.opt_kind_cd = '$item' ";
        }

		if ($sale_place != "")	$inner_where .= " and o.sale_place = '$sale_place' ";
		// if ($ord_type != "") 	$inner_where .= " and o.ord_type   = '$ord_type' ";

		if( $ord_type != "" ){
			$ord_type_where	= "";
			for( $i = 0; $i < 9; $i++ ){
				if( !empty($ord_type[$i]) ){
					if( $ord_type_where != "" )	$ord_type_where	.= " or ";
					$ord_type_where	.= " o.ord_type = '" . $ord_type[$i] . "' ";
					if($ord_type[$i] == '15') $ord_type_where .= " or o.ord_type = '0' ";
				}
			}

			if( $ord_type_where != "" ){
				$inner_where2	.= " and ( $ord_type_where ) ";
			}
		} else {
			$inner_where2	.= " and ( o.ord_type < 0 ) ";
		}

        $sql = /** @lang text */
            "
			select
				date_format(a.sale_date,'%Y%m%d') as date
				, date_format(a.sale_date,'%m') as month
				, date_format(a.sale_date,'%d') as day
				, date_format(a.sale_date,'%a') as yoil_nm
				, DAYOFWEEK(a.sale_date) as yoil
				, t.*
				, (qty_30 + qty_60 + qty_61) as sum_qty
				, (t.recv_amt_30 + t.recv_amt_60 + t.recv_amt_61) as sum_recv_amt
				, (t.wonga_30 + t.wonga_60 + t.wonga_61) as sum_wonga
				, (t.point_amt_30 + t.point_amt_60 + t.point_amt_61) as sum_point_amt
				, (t.fee_amt_30 + t.fee_amt_60 + t.fee_amt_61) as sum_fee_amt
				, (t.coupon_amt_30 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt
				, (t.dc_amt_30 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt
				, (t.taxation_amt_30 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt
				, (t.tax_amt_30 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt

			from (
				select d as sale_date from mdate where d >='$sdate' and d <= '$edate' order by sale_date desc
			) a left outer join (
				select
					b.sale_date
                    , sum(if(ord_state = '$ord_state', ifnull(b.qty, 0), 0)) as qty_30
					, sum(if(ord_state = '$ord_state', ifnull(b.recv_amt, 0), 0)) as recv_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.point_amt, 0), 0)) as point_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.coupon_amt, 0), 0)) as coupon_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.dc_amt, 0), 0)) as dc_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.fee_amt,0), 0)) as fee_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.wonga,0), 0)) as wonga_30
					, sum(if(ord_state = '$ord_state', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_30					
					, sum(if(ord_state = '$ord_state', ifnull(b.tax_amt, 0), 0)) as tax_amt_30
					
					, sum(if(ord_state = 60, ifnull(b.qty,0), 0)) * -1 as qty_60
					, sum(if(ord_state = 60, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_60
					, sum(if(ord_state = 60, ifnull(b.point_amt,0), 0)) * -1 as point_amt_60
					, sum(if(ord_state = 60, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_60
					, sum(if(ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_60					
					, sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1  as fee_amt_60
					, sum(if(ord_state = 60, ifnull(b.wonga,0), 0)) as wonga_60
					, sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60					
					, sum(if(ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60	
					
					, sum(if(ord_state = 61, ifnull(b.qty,0), 0)) * -1 as qty_61
					, sum(if(ord_state = 61, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_61
					, sum(if(ord_state = 61, ifnull(b.point_amt,0), 0)) * -1 as point_amt_61
					, sum(if(ord_state = 61, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_61
					, sum(if(ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_61					
					, sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
					, sum(if(ord_state = 61, ifnull(b.wonga,0), 0))  as wonga_61
					, sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61					
					, sum(if(ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61						
				from (
					select
						w.ord_state_date as sale_date, w.ord_state
						, sum(ifnull(w.qty,0))as qty
						, sum(ifnull(w.recv_amt,0)) as recv_amt
						, sum(ifnull(w.point_apply_amt,0)) as point_amt
						, sum(ifnull(w.wonga * w.qty,0)) as wonga
						, sum(ifnull(w.coupon_apply_amt,0)) as coupon_amt
						, sum(ifnull(w.dc_apply_amt,0)) as dc_amt
						, sum(ifnull(w.sales_com_fee,0)) as fee_amt
						, sum(if( ifnull(g.tax_yn,'Y') = 'Y',w.recv_amt + w.point_apply_amt - w.sales_com_fee,0)) as taxation_amt
						, sum(if( ifnull(g.tax_yn,'Y') = 'Y',floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11),0)) as tax_amt
					from order_opt o
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
					where
						w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate'
						and w.ord_state in ('$ord_state',60,61)
						and o.ord_state >= '$ord_state'
						$inner_where2 $inner_where
					group by w.ord_state_date, w.ord_state
				) b group by b.sale_date
			) t on a.sale_date = t.sale_date
			order by a.sale_date desc
        ";
			//echo "<pre>$sql</pre>";

        $rows = DB::select($sql);
        $result = [];

        foreach($rows as $row) {
            $row->sum_amt = $row->sum_recv_amt + $row->sum_point_amt - $row->sum_fee_amt;
            $row->sum_taxfree	= $row->sum_amt -  $row->sum_taxation_amt;
            $row->sum_taxation_no_vat	= round($row->sum_taxation_amt/1.1);		// 과세 부가세 별도
            $row->vat = $row->sum_taxation_amt - $row->sum_taxation_no_vat;
            $row->margin = $row->sum_amt?round((1 - $row->sum_wonga/$row->sum_amt)*100, 2):0;
            $row->margin1 = $row->wonga_30 - $row->wonga_60;
            $row->margin2 = $row->wonga_30 - $row->wonga_60 - $row->vat;

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