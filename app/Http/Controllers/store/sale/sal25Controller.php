<?php

namespace App\Http\Controllers\store\sale;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;
use Carbon\Carbon;

class sal25Controller extends Controller
{
    // 월별 매출 통계
    public function index() {

        $mutable = Carbon::now();
        $sdate	= sprintf("%s",$mutable->sub(6, 'month')->format('Y-m-d'));

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
			'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
			'sale_kinds'	=> SLib::getCodes('SALE_KIND'),
			'pr_codes'		=> SLib::getCodes('PR_CODE'),
        ];
        return view( Config::get('shop.store.view') . '/sale/sal25',$values);
    }

    public function search(Request $request){

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(12, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd = $request->input("brand_cd", "");
        $goods_nm = $request->input("goods_nm");
        $item	= $request->input("item");
		$ord_type = $request->input("ord_type", "");
        $ord_state	= $request->input("ord_state");
        $stat_pay_type	= $request->input("stat_pay_type");
        $store_cd       = $request->input('store_no');
        $sell_type      = $request->input('sell_type');
        $pr_code        = $request->input('pr_code');
        $on_off_yn      = $request->input('on_off_yn');

        $inner_where = "";
		$inner_where2	= "";	//매출
        $where = "";

        // 매장검색
		if ( $store_cd != "" ) {
			$where	.= " and (1!=1";
			foreach($store_cd as $store_cd) {
				$where .= " or o.store_cd = '$store_cd' ";

			}
			$where	.= ")";
		}

		//판매유형 검색
		if ( $sell_type != "" ) {
			$where	.= " and (1!=1";
			foreach($sell_type as $sell_types) {
				$where .= " or o.sale_kind = '$sell_types' ";

			}
			$where	.= ")";
		}

		//행사코드 검색
		if ( $pr_code != "" ) {
			$where	.= " and (1!=1";
			foreach($pr_code as $pr_codes) {
				$where .= " or o.pr_code = '$pr_codes' ";

			}
			$where	.= ")";
		}

        //온/오프라인
		if ($on_off_yn == 'ON') {
			$where .= "and o.store_cd = ''";
		} else if ($on_off_yn == 'OFF') {
			$where .= "and o.store_cd != ''";
		}


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

		// 결제조건
		if( $stat_pay_type != "" ){
			$stat_pay_type_where	= "";
			for( $i = 0; $i < 7; $i++ ){
				if( !empty($stat_pay_type[$i]) ){
					if($stat_pay_type_where != "" )	$stat_pay_type_where	.= " or ";
					$stat_pay_type_where	.= " ( o.pay_type & " . $stat_pay_type[$i] . " ) = " . $stat_pay_type[$i];
				}
			}

			if( $stat_pay_type_where != "" ){
				$inner_where2	.= " and ( $stat_pay_type_where ) ";
			}
		}

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
                        , o.store_cd
						, o.sale_kind
						, o.pr_code
                        , g.brand
					from order_opt o
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
					where
						w.ord_state_date >= '$sdate' 
						and w.ord_state_date <= '$edate' 
						and w.ord_state in ('$ord_state',60,61)
						and o.ord_state >= '$ord_state'
						$inner_where2 $inner_where $where
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
            $row["margin"] = $row["sum_amt"]? round((1 - $row["sum_wonga"]/$row["sum_amt"])*100, 2):0;
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