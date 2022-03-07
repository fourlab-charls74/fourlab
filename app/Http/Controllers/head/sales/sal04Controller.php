<?php

namespace App\Http\Controllers\head\sales;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal04Controller extends Controller
{
    // 상품별별 매출 통계
    public function index()
    {

        $mutable = Carbon::now();
        $sdate    = $mutable->sub(3, 'month')->format('Y-m-d');
        $goods_stats  = SLib::getCodes("G_GOODS_STAT");

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'sale_places'   => SLib::getSalePlaces(),
            "goods_stats"	=> $goods_stats
        ];
        return view(Config::get('shop.head.view') . '/sales/sal04', $values);
    }

    public function search(Request $request)
    {

        $sdate = str_replace("-", "", $request->input('sdate', Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-", "", $request->input('edate', date("Ymd")));

		$m_cat_all		= $request->input("m_cat_all");
		$stat_pay_type	= $request->input("stat_pay_type");
		$goods_nm		= $request->input("goods_nm");
		$ord_state		= $request->input("ord_state");
		$not_sale		= $request->input("not_sale");
		$cat_type		= $request->input("cat_type");
		$cat_cd			= $request->input("cat_cd");
		$item			= $request->input("item");
		$sale_place		= $request->input("sale_place", "");
		$goods_stat		= $request->input("goods_stat");
		$ord_type		= $request->input("ord_type");

        $inner_where = "";
        $inner_where1 = "";    //재고조정
        $inner_where2 = "";    //매출

        if ($m_cat_all != "") {
            $inner_where .= " and g.rep_cat_cd like '" . Lib::quote($m_cat_all) . "%' ";
        }

        if($goods_nm != ""){
            $inner_where .= " and g.goods_nm like '%$goods_nm%' ";
        }

        if ($cat_cd != "") {
            if ($cat_type === "DISPLAY") {
                $inner_where .= " and g.rep_cat_cd = '" . Lib::quote($cat_cd) . "' ";
            } else if ($cat_type === "ITEM") {
                $inner_where .= " and g.item_cat_cd = '" . Lib::quote($cat_cd) . "' ";
            }
        }

        if ($not_sale != "") {
            $join = " left outer join ";
        }

        if ($item != ""){
            $inner_where .= " and g.opt_kind_cd = '$item' ";
        }

        if ($sale_place != "")	$inner_where .= " and o.sale_place = '$sale_place' ";
        if ($goods_stat != "")	$inner_where .= " and a.goods_stat = '$goods_stat' ";

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
        $join = " inner join ";

        $sql =
            /** @lang text */
            "
			select
				g.goods_no as no, g.goods_sub as sub, gs.code_val as goods_state
				, cb.brand_nm, opt.opt_kind_nm, g.style_no, g.goods_nm
				, ifnull((select sum(good_qty) from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub),0) as jaego_qty
				, t.*
				, (qty_10 + qty_60 + qty_61) as sum_qty
				, (t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) as sum_recv_amt
				, (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) as sum_point_amt
				, (t.wonga_10 + t.wonga_60 + t.wonga_61) as sum_wonga
				, (t.coupon_amt_10 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt
				, (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_fee_amt
				, (t.dc_amt_10 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt
				, (t.taxation_amt_10 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt
				, (t.tax_amt_10 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt				
			from goods g $join (
				select
					b.goods_no,b.goods_sub
					, sum(if(ord_state = '$ord_state', ifnull(b.qty,0), 0)) as qty_10
					, sum(if(ord_state = '$ord_state', ifnull(b.point_amt,0), 0)) as point_amt_10
					, sum(if(ord_state = '$ord_state', ifnull(b.coupon_amt,0), 0)) as coupon_amt_10
					, sum(if(ord_state = '$ord_state', ifnull(b.dc_amt, 0), 0)) as dc_amt_10
					, sum(if(ord_state = '$ord_state', ifnull(b.fee_amt,0), 0)) as fee_amt_10
					, sum(if(ord_state = '$ord_state', ifnull(b.recv_amt,0), 0)) as recv_amt_10
					, sum(if(ord_state = '$ord_state', ifnull(b.wonga,0), 0)) as wonga_10					
					, sum(if(ord_state = '$ord_state', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_10					
					, sum(if(ord_state = '$ord_state', ifnull(b.tax_amt, 0), 0)) as tax_amt_10							

					, sum(if(ord_state = 60, ifnull(b.qty,0), 0)) * -1 as qty_60
					, sum(if(ord_state = 60, ifnull(b.point_amt,0), 0)) * -1 as point_amt_60
					, sum(if(ord_state = 60, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_60
					, sum(if(ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_60		
					, sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
					, sum(if(ord_state = 60, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_60
					, sum(if(ord_state = 60, ifnull(b.wonga,0), 0)) as wonga_60
					, sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60					
					, sum(if(ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60	
					
					, sum(if(ord_state = 61, ifnull(b.qty,0), 0)) * -1 as qty_61
					, sum(if(ord_state = 61, ifnull(b.point_amt,0), 0)) * -1 as point_amt_61
					, sum(if(ord_state = 61, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_61
					, sum(if(ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_61
					, sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
					, sum(if(ord_state = 61, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_61
					, sum(if(ord_state = 61, ifnull(b.wonga,0), 0))  as wonga_61
					, sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61					
					, sum(if(ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61							
				from  (
						select
							g.goods_no,g.goods_sub,w.ord_state
							, sum(w.qty)as qty
							, sum(w.recv_amt) as recv_amt
							, sum(w.point_apply_amt) as point_amt
							, sum(w.wonga * w.qty) as wonga
							, sum(w.coupon_apply_amt) as coupon_amt
							, sum(w.sales_com_fee) as fee_amt
							, sum(w.dc_apply_amt) as dc_amt
							, sum(if( ifnull(g.tax_yn,'Y') = 'Y',w.recv_amt + w.point_apply_amt - w.sales_com_fee,0)) as taxation_amt
							, sum(if( ifnull(g.tax_yn,'Y') = 'Y',floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11),0)) as tax_amt							
						from
							order_opt o
							inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
							inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
						where
							w.ord_state_date >= '$sdate' 
							and w.ord_state_date <= '$edate' and w.ord_state in ('$ord_state',60,61)
							and o.ord_state >= '$ord_state'
							$inner_where2 $inner_where
						group by g.goods_no,g.goods_sub,ord_state
				) b group by b.goods_no,b.goods_sub
			) t on g.goods_no = t.goods_no and g.goods_sub = t.goods_sub 
			left outer join brand cb on cb.brand = g.brand
			left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
			left outer join code gs on gs.code_kind_cd = 'G_GOODS_STAT'  and g.sale_stat_cl = gs.code_id
			where 1 = 1 $inner_where
			order by opt.opt_kind_nm, gs.code_val,
						ifnull(( ( t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) +
							(t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) +
							(t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61)
						),0) desc
        ";

        //echo "<pre>$sql</pre>";exit;

        $result = DB::select($sql);

        foreach ($result as $row) {
            $row->sum_amt = $row->sum_recv_amt + $row->sum_point_amt - $row->sum_fee_amt;
            $row->sum_taxfree    = $row->sum_amt -  $row->sum_taxation_amt;
            $row->sum_taxation_no_vat    = round($row->sum_taxation_amt / 1.1);        // 과세 부가세 별도
            $row->vat = $row->sum_taxation_amt - $row->sum_taxation_no_vat;
            $row->margin = ($row->sum_amt > 0 && $row->sum_wonga) ? round((1 - $row->sum_wonga / $row->sum_amt) * 100, 2) : 0;
            $row->margin1 = $row->wonga_10 - $row->wonga_60;
            $row->margin2 = $row->wonga_10 - $row->wonga_60 - $row->vat;
        }
        //dd($result);

        return response()->json(
            [
                "code" => 200,
                "head" => array(
                    "total" => count($result)
                ),
                "body" => $result
            ]
        );
    }
}
