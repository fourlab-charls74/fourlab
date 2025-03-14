<?php

namespace App\Http\Controllers\partner\sales;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;
use Carbon\Carbon;

class sal03Controller extends Controller
{
    // 일별 매출 통계
    public function index() {

        $mutable = Carbon::now();
        $sdate	= sprintf("%s",$mutable->sub(3, 'month')->format('Y-m-d'));

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
        ];
        return view( Config::get('shop.partner.view') . '/sales/sal03',$values);
    }

    public function search(Request $request) {

        $com_id = Auth('partner')->user()->com_id;

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $item	= $request->input("item");
        $ord_state	= $request->input("ord_state");

        $inner_where = "";

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
        $inner_where .= " and w.com_id='$com_id' ";

        $sql = /** @lang text */
            "
			select
				a.sale_date  as date
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
				select date_format(d,'%Y%m') as sale_date
				from mdate
				where d >='$sdate' and d <= '$edate'
				group by sale_date
				order by sale_date desc
			) a left outer join (
				select
					b.sale_date
					, sum(if(ord_state = '$ord_state', ifnull(b.qty,0), 0)) as qty_30
					, sum(if(ord_state = '$ord_state', ifnull(b.recv_amt,0), 0)) as recv_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.point_amt,0), 0)) as point_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.coupon_amt,0), 0)) as coupon_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.dc_amt,0), 0)) as dc_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.fee_amt,0), 0)) as fee_amt_30
					, sum(if(ord_state = '$ord_state', ifnull(b.wonga,0), 0)) as wonga_30
					, sum(if(ord_state = '$ord_state', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_30					
					, sum(if(ord_state = '$ord_state', ifnull(b.tax_amt, 0), 0)) as tax_amt_30						

					, sum(if(ord_state = 60, ifnull(b.qty,0), 0)) * -1 as qty_60
					, sum(if(ord_state = 60, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_60
					, sum(if(ord_state = 60, ifnull(b.point_amt,0), 0)) * -1 as point_amt_60
					, sum(if(ord_state = 60, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_60
					, sum(if(ord_state = 60, ifnull(b.dc_amt,0), 0)) * -1 as dc_amt_60
					, sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
					, sum(if(ord_state = 60, ifnull(b.wonga,0), 0)) as wonga_60
					, sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60					
					, sum(if(ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60						

					, sum(if(ord_state = 61, ifnull(b.qty,0), 0)) * -1 as qty_61
					, sum(if(ord_state = 61, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_61
					, sum(if(ord_state = 61, ifnull(b.point_amt,0), 0)) * -1 as point_amt_61
					, sum(if(ord_state = 61, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_61
					, sum(if(ord_state = 61, ifnull(b.dc_amt,0), 0)) * -1 as dc_amt_61
					, sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
					, sum(if(ord_state = 61, ifnull(b.wonga,0), 0)) as wonga_61
					, sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61					
					, sum(if(ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61						
				from (
					select
						date_format(w.ord_state_date,'%Y%m') as sale_date, w.ord_state
						, sum(w.qty)as qty
						, sum(w.recv_amt) as recv_amt
						, sum(w.point_apply_amt) as point_amt
						, sum(w.wonga * w.qty) as wonga
						, sum(w.coupon_apply_amt) as coupon_amt
						, sum(w.dc_apply_amt) as dc_amt						
						, sum(w.sales_com_fee) as fee_amt
						, sum(if( ifnull(g.tax_yn,'Y') = 'Y',w.recv_amt + w.point_apply_amt - w.sales_com_fee,0)) as taxation_amt
						, sum(if( ifnull(g.tax_yn,'Y') = 'Y',floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11),0)) as tax_amt
					from order_opt o
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
					where
						w.ord_state_date >= '$sdate' 
						and w.ord_state_date <= '$edate' 
						and w.ord_state in ('$ord_state',60,61)
						and o.ord_state >= '$ord_state'
						$inner_where
					group by sale_date, w.ord_state
				) b group by b.sale_date
			) t on a.sale_date = t.sale_date
        ";
        //echo "<pre>$sql</pre>";exit;

        //$result = DB::select($sql);
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $row["sum_amt"] = $row["sum_recv_amt"] + $row["sum_point_amt"] - $row["sum_fee_amt"];
            $row["sum_taxfree"]	= $row["sum_amt"] -  $row["sum_taxation_amt"];
            $row["sum_taxation_no_vat"]	= round($row["sum_taxation_amt"]/1.1);		// 과세 부가세 별도
            $row["vat"] = $row["sum_taxation_amt"] - $row["sum_taxation_no_vat"];
            $row["margin"] = $row["sum_wonga"]?round((1 - $row["sum_wonga"]/$row["sum_amt"])*100, 2):0;
            $row["margin1"] = $row["wonga_30"] - $row["wonga_60"];
            $row["margin2"] = $row["wonga_30"] - $row["wonga_60"] - $row["vat"];

            $result[] = $row;
        }

        return response()->json([
                "code" => 200,
                "head" => array(
                    "total" => count($result)
                ),
                "body" => $result
            ]
        );
    }

}
