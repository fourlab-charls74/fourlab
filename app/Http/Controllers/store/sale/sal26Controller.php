<?php

namespace App\Http\Controllers\store\sale;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal26Controller extends Controller
{
    // 업체별 매출 통계
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'store_types'   => SLib::getCodes('STORE_TYPE'),
			'sale_kinds'	=> SLib::getCodes('SALE_KIND'),
			'pr_codes'		=> SLib::getCodes('PR_CODE'),
        ];
        return view( Config::get('shop.store.view') . '/sale/sal26',$values);
    }

    public function search(Request $request){

		$sdate		= str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
		$edate		= str_replace("-","",$request->input('edate',date("Ymd")));

		$com_cd		= $request->input("com_cd");
		$com_type	= $request->input("com_type");

		$item		= $request->input("item");
		$brand_cd	= $request->input("brand_cd");
		$goods_nm	= $request->input("goods_nm");
		$ord_state	= $request->input("ord_state");
		$ord_type	= $request->input("ord_type");
        $sell_type  = $request->input('sell_type');
        $pr_code    = $request->input('pr_code');
        $store_cd   = $request->input('store_no');
        $store_type = $request->input('store_type');


		$inner_where	= "";
		$inner_where2	= "";	//매출

        if($com_cd != ""){
            $inner_where .= " and c.com_id = '". Lib::quote($com_cd). "'";
        }

        if($com_type != ""){
            $inner_where .= " and c.com_type = '". Lib::quote($com_type). "'";
        }

        if($item != ""){
            $inner_where .= " and g.opt_kind_cd = '". Lib::quote($item). "'";
        }

        if($brand_cd != ""){
            $inner_where .= " and g.brand = '". Lib::quote($brand_cd). "'";
        }

        if($goods_nm != ""){
            $inner_where .= " and g.goods_nm like '%". Lib::quote($goods_nm)."%' ";
        }

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

        $where = "";

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

        //매장구분 검색
        if($store_type != '') {
            $where .= " and s.store_type = $store_type";
        }

        // 매장검색
        if ( $store_cd != "" ) {
            $where	.= " and (1!=1";
            foreach($store_cd as $store_cd) {
                $where .= " or o.store_cd = '$store_cd' ";

            }
            $where	.= ")";
        }


        $sql = /** @lang text */
            "
			select
                a.store_cd as store_cd, a.store_nm as store_nm
				, t.*
				, (qty_10 + qty_60 + qty_61) as sum_qty
				, (t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) as sum_recv_amt
				, (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) as sum_point_amt
				, (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_fee_amt
                ,(t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) + (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) - (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_amt
				, (t.wonga_10 + t.wonga_60 + t.wonga_61) as sum_wonga
				, (t.coupon_amt_10 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt
				, (t.dc_amt_10 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt
				, (t.taxation_amt_10 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt
				, (t.tax_amt_10 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt				
			from ( select store_cd, store_nm from store where 1=1 ) a inner join (
				select
                    b.store_cd
					, sum(if(b.ord_state = $ord_state, ifnull(b.qty,0), 0)) as qty_10
					, sum(if(b.ord_state = $ord_state, ifnull(b.point_amt,0), 0)) as point_amt_10
					, sum(if(b.ord_state = $ord_state, ifnull(b.coupon_amt,0), 0)) as coupon_amt_10
					, sum(if(b.ord_state = $ord_state, ifnull(b.dc_amt, 0), 0)) as dc_amt_10
					, sum(if(b.ord_state = $ord_state, ifnull(b.fee_amt,0), 0)) as fee_amt_10
					, sum(if(b.ord_state = $ord_state, ifnull(b.recv_amt,0), 0)) as recv_amt_10
					, sum(if(b.ord_state = $ord_state, ifnull(b.wonga,0), 0)) as wonga_10					
					, sum(if(b.ord_state = $ord_state, ifnull(b.taxation_amt, 0), 0)) as taxation_amt_10					
					, sum(if(b.ord_state = $ord_state, ifnull(b.tax_amt, 0), 0)) as tax_amt_10							

					, sum(if(b.ord_state = 60, ifnull(b.qty,0), 0)) * -1 as qty_60
					, sum(if(b.ord_state = 60, ifnull(b.point_amt,0), 0)) * -1 as point_amt_60
					, sum(if(b.ord_state = 60, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_60
					, sum(if(b.ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_60		
					, sum(if(b.ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
					, sum(if(b.ord_state = 60, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_60
					, sum(if(b.ord_state = 60, ifnull(b.wonga,0), 0)) as wonga_60
					, sum(if(b.ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60					
					, sum(if(b.ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60	
					
					, sum(if(b.ord_state = 61, ifnull(b.qty,0), 0)) * -1 as qty_61
					, sum(if(b.ord_state = 61, ifnull(b.point_amt,0), 0)) * -1 as point_amt_61
					, sum(if(b.ord_state = 61, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_61
					, sum(if(b.ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_61
					, sum(if(b.ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
					, sum(if(b.ord_state = 61, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_61
					, sum(if(b.ord_state = 61, ifnull(b.wonga,0), 0))  as wonga_61
					, sum(if(b.ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61					
					, sum(if(b.ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61							
				from  (
						select
                            s.store_cd
                            , w.ord_state
							, sum(w.qty)as qty
							, sum(w.recv_amt) as recv_amt
							, sum(w.point_apply_amt) as point_amt
							, sum(w.wonga * w.qty) as wonga
							, sum(w.coupon_apply_amt) as coupon_amt
							, sum(w.sales_com_fee) as fee_amt
							, sum(w.dc_apply_amt) as dc_amt
							, sum(if( ifnull(g.tax_yn,'Y') = 'Y',w.recv_amt + w.point_apply_amt - w.sales_com_fee,0)) as taxation_amt
							, sum(if( ifnull(g.tax_yn,'Y') = 'Y',floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11),0)) as tax_amt
                            , o.sale_kind
						    , o.pr_code
                            , s.store_type
                            , s.store_nm	
						from
							order_opt o
							inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
							inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
							inner join company c on w.com_id = c.com_id
                            inner join store s on s.store_cd = o.store_cd
						where
							w.ord_state_date >= '$sdate' 
							and w.ord_state_date <= '$edate' and w.ord_state in ($ord_state,60,61)
							and o.ord_state >= $ord_state
							$inner_where2 $inner_where $where
						group by store_cd,ord_state
				) b group by b.store_cd
			) t on a.store_cd = t.store_cd 
            order by sum_amt desc
        ";

        $result = DB::select($sql);

        foreach($result as $row){
            // $row->sum_amt = $row->sum_recv_amt + $row->sum_point_amt - $row->sum_fee_amt;
            $row->sum_taxfree	= $row->sum_amt -  $row->sum_taxation_amt;
            $row->sum_taxation_no_vat	= round($row->sum_taxation_amt/1.1);		// 과세 부가세 별도
            $row->vat = $row->sum_taxation_amt - $row->sum_taxation_no_vat;
            $row->margin = $row->sum_amt? round((1 - $row->sum_wonga/$row->sum_amt)*100, 2):0;
            $row->margin1 = $row->wonga_10 - $row->wonga_60;
            $row->margin2 = $row->wonga_10 - $row->wonga_60 - $row->vat;
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
