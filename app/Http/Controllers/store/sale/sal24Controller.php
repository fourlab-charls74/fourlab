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
use DateTime;

class sal24Controller extends Controller
{
    // 일별 매출 통계
    public function index(Request $req) 
	{
		$date = new DateTime($req->input('sdate', now()->startOfMonth()->sub(0, 'month')->format("Ym"). '01'));
		$sdate = $date->format('Y-m-d');
		$edate = $date->format('Y-m-t');

		$pr_code 			= $req->input('pr_code', []);
		$sell_type 			= $req->input('sell_type', []);
		$stat_pay_type 		= $req->input('stat_pay_type', []);
		$ord_type 			= $req->input('ord_type', []);
		$ord_state 			= $req->input('ord_state', '');
		$item 				= $req->input('item', '');
		$brand_cd 			= $req->input('brand_cd', '');
		$goods_nm 			= $req->input('goods_nm', '');
		$store_cd 			= $req->input('store_cd', '');
		$on_off_yn 			= $req->input('on_off_yn', '');
		$store_channel 		= $req->input('store_channel', '');
		$store_channel_kind = $req->input('store_channel_kind', '');
		$prd_cd_range_text 	= $req->query("prd_cd_range", '');
		$prd_cd_range_nm 	= $req->query("prd_cd_range_nm", '');
		
		parse_str($prd_cd_range_text, $prd_cd_range);
		$pr_code_ids = [];
		$sell_type_ids = [];
		
		if(!empty($pr_code)) {
			$pr_code_ids = DB::table('code')->select('code_id')->where('code_kind_cd', 'PR_CODE')->whereIn('code_id', $pr_code)->get();
			$pr_code_ids = array_map(function ($p) { return $p->code_id; }, $pr_code_ids->toArray());	
		}
		
		if(!empty($sell_type)) {
			$sell_type_ids = DB::table('code')->select('code_id')->where('code_kind_cd', 'SALE_KIND')->whereIn('code_id', $sell_type)->get();
			$sell_type_ids = array_map(function ($p) { return $p->code_id; }, $sell_type_ids->toArray());	
		}
		
		$store = DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', $store_cd)->first();
		//$brand = DB::table('brand')->select('brand', 'brand_nm')->where('brand', $brand_cd)->first();

		$values = [
			'sdate' 		=> $sdate,
			'edate' 		=> $edate,
			'items' 		=> SLib::getItems(),
			'sale_places'   => SLib::getSalePlaces(),
			'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
			'sale_kinds'	=> SLib::getCodes('SALE_KIND'),
			'pr_codes'		=> SLib::getCodes('PR_CODE'),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
			'stat_pay_type'	=> $stat_pay_type,
			'ord_type'		=> $ord_type,
			'ord_state'		=> $ord_state,
			'item'			=> $item,
			//'brand'			=> $brand,
			'goods_nm'		=> $goods_nm,
			'on_off_yn'		=> $on_off_yn,
			'pr_code_ids'	=> $pr_code_ids,
			'sell_type_ids'	=> $sell_type_ids,
			'p_store_channel' => $store_channel,
			'p_store_kind' 	=> $store_channel_kind,
			'store'			=> $store,
			'prd_cd_range'	=> $prd_cd_range,
			'prd_cd_range_nm' => $prd_cd_range_nm,
		];
		return view(Config::get('shop.store.view') . '/sale/sal24', $values);
    }

    public function search(Request $request)
	{

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd 			= $request->input("brand_cd");
        $goods_nm 			= $request->input("goods_nm");
        $item				= $request->input("item");
        $ord_state			= $request->input("ord_state");
		$ord_type 			= $request->input("ord_type", "");
		$sale_place 		= $request->input("sale_place", "");
		$stat_pay_type 		= $request->input("stat_pay_type");
        $store_cd       	= $request->input('store_no');
        $sell_type      	= $request->input('sell_type');
        $pr_code        	= $request->input('pr_code');
        $on_off_yn      	= $request->input('on_off_yn');
		$store_channel		= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$prd_cd_range_text 	= $request->input("prd_cd_range", '');

        $inner_where = "";
		$inner_where2	= "";	//매출
		$where = "";

		// 판매채널/매장구분 검색
		if ($store_channel != "") $where .= "and store.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and store.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// 매장검색
		if ( $store_cd != "" ) {
			$where	.= " and o.store_cd = '$store_cd' ";
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
				date_format(a.sale_date,'%Y%m%d') as date,
				date_format(a.sale_date,'%m') as month,
				date_format(a.sale_date,'%d') as day,
				date_format(a.sale_date,'%a') as yoil_nm,
				DAYOFWEEK(a.sale_date) as yoil,
				t.*,
				(qty_30 + qty_60 + qty_61) as sum_qty,
				(t.recv_amt_30 + t.recv_amt_60 + t.recv_amt_61) as sum_recv_amt,
				(t.wonga_30 + t.wonga_60 + t.wonga_61) as sum_wonga,
				(t.point_amt_30 + t.point_amt_60 + t.point_amt_61) as sum_point_amt,
				(t.coupon_amt_30 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt,
				(t.fee_amt_30 + t.fee_amt_60 + t.fee_amt_61 ) as sum_fee_amt,
				(t.dc_amt_30 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt,
				(t.taxation_amt_30 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt,
				(t.tax_amt_30 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt,
				ifnull(p.pg_fee,0) as exp_pg_fee
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
					
					, sum(if(ord_state = 60, ifnull(b.qty, 0), 0)) * -1 as qty_60
					, sum(if(ord_state = 60, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_60
					, sum(if(ord_state = 60, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_60
					, sum(if(ord_state = 60, ifnull(b.coupon_amt, 0), 0)) * -1 as coupon_amt_60
					, sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) as fee_amt_60
					, sum(if(ord_state = 60, ifnull(b.wonga, 0), 0)) as wonga_60
					, sum(if(ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_60
					, sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60
					, sum(if(ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60

					, sum(if(ord_state = 61, ifnull(b.qty, 0), 0)) * -1 as qty_61
					, sum(if(ord_state = 61, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_61
					, sum(if(ord_state = 61, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_61
					, sum(if(ord_state = 61, ifnull(b.coupon_amt, 0), 0)) * -1  as coupon_amt_61
					, sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
					, sum(if(ord_state = 61, ifnull(b.wonga, 0), 0))  as wonga_61
					, sum(if(ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_61
					, sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61
					, sum(if(ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61					
				from (
					select
						w.ord_state_date as sale_date, w.ord_state
						, sum(w.qty)as qty
						, sum(w.recv_amt) as recv_amt
						, sum(w.point_apply_amt) as point_amt
						, sum(w.wonga * w.qty) as wonga
						, sum(w.coupon_apply_amt) as coupon_amt
						, sum(w.sales_com_fee) as fee_amt
						, sum(w.dc_apply_amt) as dc_amt
						, sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', w.recv_amt + w.point_apply_amt - w.sales_com_fee, 0)) as taxation_amt
						-- , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11), 0)) as tax_amt
						-- , sum((w.recv_amt + w.point_apply_amt) - (w.recv_amt + w.point_apply_amt)/1.1) as tax_amt
						, sum(w.recv_amt - w.recv_amt/1.1) as tax_amt
						, o.store_cd
						, o.sale_kind
						, o.pr_code
						, g.brand
					from order_opt o
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
						left outer join company c on o.sale_place = c.com_id
						left outer join store store on store.store_cd = o.store_cd
						left outer join product_code pc on pc.prd_cd = o.prd_cd
					where
						w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate'
						and w.ord_state in ('$ord_state',60,61)
						and o.ord_state >= '$ord_state'
						and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						$inner_where2 $inner_where
						$where
					group by w.ord_state_date, w.ord_state
				) b group by b.sale_date
			) t on a.sale_date = t.sale_date 
			left outer join (
				select
					ord_state_date,
					sum(cal_pg_fee(a.ord_state,a.ord_state_date,p.pay_type,p.pay_amt,p.pay_date,a.refund_amt)) as pg_fee
				from (
					select
						o.ord_no,ord_state_date,
						w.ord_state,
						sum(if(clm.refund_yn = 'y',refund_amt,0)) as refund_amt
					from order_opt o
						inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
						inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
						left outer join company c on o.sale_place = c.com_id
						left outer join claim clm on w.ord_opt_no = clm.ord_opt_no
						left outer join store store on store.store_cd = o.store_cd
						inner join product_code pc on pc.prd_cd = o.prd_cd
					where
						w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate'
						and w.ord_state in ('$ord_state',60,61)
						and o.ord_state >= '$ord_state'
						$inner_where2 $inner_where
					group by o.ord_no,w.ord_state,ord_state_date
				) a inner join order_mst m on a.ord_no = m.ord_no
					inner join payment p on m.ord_no = p.ord_no
				where m.ord_type = 0 && m.sale_place = 'HEAD_OFFICE'  && p.tno <> ''
				group by a.ord_state_date
			) p on a.sale_date = p.ord_state_date
		";
			
		$rows = DB::select($sql);

		$result = collect($rows)->map(function ($row) {

			$sale_date		= $row->date;
			
			$wonga_30		= $row->wonga_30;			//매출원가
			$wonga_60		= $row->wonga_60;			//교환원가
			$wonga_61		= $row->wonga_61;			//환불원가
			$qty_30			= $row->qty_30;			//판매
			$recv_amt_30	= $row->recv_amt_30/1.1;
			$qty_60			= $row->qty_60;			//교환
			$recv_amt_60	= $row->recv_amt_60/1.1 ;
			$qty_61			= $row->qty_61;			//환불
			$recv_amt_61	= $row->recv_amt_61/1.1 ;
			$point_amt_30	= $row->point_amt_30;		//판매 포인트
			$point_amt_60	= $row->point_amt_60;		//교환 포인트
			$point_amt_61	= $row->point_amt_61;		//환불 포인트
			$coupon_amt_30	= $row->coupon_amt_30;	//판매 쿠폰
			$coupon_amt_60	= $row->coupon_amt_60;	//교환 쿠폰
			$coupon_amt_61	= $row->coupon_amt_61;	//환불 쿠폰

			$dc_amt_30		= $row->dc_amt_30;			//판매 할인
			$dc_amt_60		= $row->dc_amt_60;			//교환 할인
			$dc_amt_61		= $row->dc_amt_61;			//환불 할인

			$fee_amt_30		= $row->fee_amt_30;		//판매 수수료
			$fee_amt_60		= $row->fee_amt_60;		//교환 수수료
			$fee_amt_61		= $row->fee_amt_61;		//환불 수수료

			$sum_point		= $row->sum_point_amt;	//포인트 합계
			$sum_coupon		= $row->sum_coupon_amt;	//쿠폰 합계
			$sum_dc			= $row->sum_dc_amt;		//할인 합계
			$sum_fee		= $row->sum_fee_amt;		//수수료 합계
			$sum_recv		= $row->sum_recv_amt;		//무통장 또는 카드 합계
			$sum_qty		= $row->sum_qty;			//수량 합계
			$sum_wonga		= $row->sum_wonga;		//원가 합계

			$sum_taxation	= $row->sum_taxation_amt;		//과세
			$sum_tax		= $row->sum_tax_amt;			//세금


			$sum_taxation_no_vat	= round($sum_taxation/1.1);		// 과세 부가세 별도
			//$vat = $sum_taxation - $sum_taxation_no_vat;			// 부가세
			$vat = $sum_tax;			// 부가세

			//$sum_amt		= $sum_recv + $sum_point - $sum_fee - $vat;
			//$sum_amt		= $sum_recv + $sum_point - $vat;
			$sum_amt		= $sum_recv - $vat;
			$sum_taxfree	= $sum_amt -  $sum_taxation;

			$exp_pg_fee		= $row->exp_pg_fee;
			$exp_point		= $sum_point;
			$exp_sum		= $exp_point + $exp_pg_fee;
			$biz_profit		= $sum_amt - $sum_wonga - $exp_sum;
			$biz_profit_after	= $biz_profit - $sum_tax;
			$biz_margin		= ($sum_amt > 0)? $biz_profit / $sum_amt * 100:0;

			$array = array(
				"date"			=> $sale_date,
				"month"			=> $row->month,
				"day"			=> $row->day,
				"yoil_nm"		=> $row->yoil_nm,
				"yoil"			=> $row->yoil,

				"sum_qty"		=> ($sum_qty) ? $sum_qty:0,
				"sum_point"		=> ($sum_point) ? $sum_point:0,
				"sum_dc"		=> ($sum_dc) ? $sum_dc:0,
				"sum_coupon"	=> ($sum_coupon) ? $sum_coupon:0,
				"sum_fee"	=> ($sum_fee) ? $sum_fee:0,
				"sum_recv"		=> ($sum_recv) ? $sum_recv:0,
				"sum_taxation"	=> ($sum_taxation) ? $sum_taxation:0,
				"sum_taxfree"	=> ($sum_taxfree) ? $sum_taxfree:0,

				"vat"			=> ($sum_tax) ? $sum_tax:0,
				"sum_amt"		=> ($sum_amt) ? $sum_amt:0,
				"sum_wonga"		=> ($sum_wonga) ? $sum_wonga*1:0,
//				"margin"		=> ($sum_amt) ? round((1 - $sum_wonga/$sum_amt)*100,2) : 0,
				"margin"		=> ($sum_amt) ? round(($sum_amt - $sum_wonga) / $sum_amt * 100,2) : 0,
				"margin1"		=> ($sum_amt - $sum_wonga) ? ($sum_amt - $sum_wonga):0,
				"margin2"		=> ($sum_amt - $sum_wonga - $sum_tax) ? ($sum_amt - $sum_wonga - $sum_tax):0,

				"exp_pg_fee"	=> $exp_pg_fee,
				"exp_point"		=> ($exp_point) ? $exp_point:0,
				"exp_ad"		=> 0,
				"exp_sum"		=> ($exp_sum) ? $exp_sum:0,
				"biz_margin"	=> ($biz_margin) ? $biz_margin:0,
				"biz_profit"	=> ($biz_profit) ? $biz_profit:0,
				"biz_profit_after"	=> ($biz_profit_after) ? $biz_profit_after:0,

				"qty_30"		=> ($qty_30) ? $qty_30:0,
				"point_amt_30"	=> ($point_amt_30) ? $point_amt_30:0,
				"dc_amt_30"		=> ($dc_amt_30) ? $dc_amt_30:0,
				"coupon_amt_30"	=> ($coupon_amt_30) ? $coupon_amt_30:0,
				"fee_amt_30"	=> ($fee_amt_30) ? $fee_amt_30:0,
				//"recv_amt_30"	=> ($recv_amt_30) ? $recv_amt_30 - $fee_amt_30:0,
				"recv_amt_30"	=> ($recv_amt_30) ? $recv_amt_30:0,

				"qty_60"		=> ($qty_60) ? $qty_60:0,
				"point_amt_60"	=> ($point_amt_60) ? $point_amt_60:0,
				"dc_amt_60"		=> ($dc_amt_60) ? $dc_amt_60:0,
				"coupon_amt_60"	=> ($coupon_amt_60) ? $coupon_amt_60:0,
				"fee_amt_60"	=> ($fee_amt_60) ? $fee_amt_60:0,
				//"recv_amt_60"	=> ($recv_amt_60) ? $recv_amt_60 - $fee_amt_60 :0,
				"recv_amt_60"	=> ($recv_amt_60) ? $recv_amt_60:0,

				"qty_61"		=> ($qty_61) ? $qty_61:0,
				"point_amt_61"	=> ($point_amt_61) ? $point_amt_61:0,
				"dc_amt_61"		=> ($dc_amt_61) ? $dc_amt_61:0,
				"coupon_amt_61"	=> ($coupon_amt_61) ? $coupon_amt_61:0,
				"fee_amt_61"	=> ($fee_amt_61) ? $fee_amt_61:0,
				//"recv_amt_61"	=> ($recv_amt_61) ? $recv_amt_61 + $fee_amt_61:0,
				"recv_amt_61"	=> ($recv_amt_61) ? $recv_amt_61:0,
			);

			return $array;

		})->all();

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

}
