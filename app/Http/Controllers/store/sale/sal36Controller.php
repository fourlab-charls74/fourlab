<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal36Controller extends Controller
{
	public function index(Request $req)
	{
		$mutable	= Carbon::now();
		$sdate		= sprintf("%s",$mutable->sub(1, 'month')->format('Y-m'));

		$values = [
			'sdate'			=> $sdate,
			'edate'			=> date("Y-m"),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/sale/sal36', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate',Carbon::now()->sub(1, 'month')->format('Y-m'));
		$edate = $request->input('edate',date("Y-m"));

		$store_channel		= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$store_cd       	= $request->input('store_no');
		$prd_cd_range_text 	= $request->input("prd_cd_range", '');

		$sdate	= str_replace("-","", $sdate) . "00";
		$edate	= str_replace("-","", $edate) . "24";

		$where	= "";
		//$in_where	= "";

		// 판매채널/매장구분 검색
		if($store_channel != "")		$where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if($store_channel_kind != "")	$where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		// 매장검색
		if ( $store_cd != "" ) {
			$where	.= " and s.store_cd = '$store_cd' ";
		}

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

		$sql	= "
			select
				w.ord_month, w.sale_kind, st.sale_type_nm
				, sum(w.tag_amt) as tag_amt
				, sum(w.price_amt) as price_amt
				, sum(w.recv_amt) as recv_amt
				, round((( sum(w.tag_amt) - sum(w.recv_amt) )/sum(w.tag_amt) * 100), 2) as sale_ratio
				, '' as sg_ratio
			from
			(
				select
					substr(oow.ord_state_date, 1, 6) as ord_month
					, ifnull(oo.sale_kind,'81') as sale_kind	-- 온라인 매장은 온라인 판매유형으로 등록
					, (p.tag_price * oow.qty) * if(oow.ord_state = '30', 1, -1) as tag_amt
					, (oow.price * oow.qty) as price_amt
					, if(oow.qty > 0, 1, -1) * (oow.recv_amt * oow.qty) * if(oow.ord_state = '30', 1, -1) as recv_amt
				from order_opt_wonga oow
				inner join order_opt oo on oo.ord_opt_no = oow.ord_opt_no and oo.ord_state = '30'
				inner join product p on oo.prd_cd = p.prd_cd
				inner join product_code pc on oo.prd_cd = pc.prd_cd
				left outer join store s on s.store_cd = oo.store_cd
				where
					oow.ord_state_date >= '$sdate' and oow.ord_state_date <= '$edate'
					and oow.ord_state in (30, 60, 61)
					and if( oow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
					$where
			) w
			left outer join sale_type st on w.sale_kind = st.sale_kind
			group by w.ord_month, w.sale_kind
			order by ord_month desc, sale_kind asc
		";
		$rows = DB::select($sql);

		$total_amt	= [];
		foreach($rows as $row){
			if(!isset($total_amt[$row->ord_month])){
				$total_amt[$row->ord_month]	= 0;
			}

			$total_amt[$row->ord_month]	+= $row->recv_amt;
		}

		foreach($rows as $row){
			$row->sg_ratio	= round(($row->recv_amt/$total_amt[$row->ord_month] * 100), 2);
		}
		
		$sql	= "
			select
				t.tag_amt as total_tag_amt
				, t.price_amt as total_price_amt
				, t.recv_amt as total_recv_amt
			from (
				select
					w.ord_month, w.sale_kind, st.sale_type_nm
					, sum(w.tag_amt) as tag_amt
					, sum(w.price_amt) as price_amt
					, sum(w.recv_amt) as recv_amt
				from
				(
					select
						substr(oow.ord_state_date, 1, 6) as ord_month
						, ifnull(oo.sale_kind,'81') as sale_kind	-- 온라인 매장은 온라인 판매유형으로 등록
						, (p.tag_price * oow.qty) * if(oow.ord_state = '30', 1, -1) as tag_amt
						, (oow.price * oow.qty) as price_amt
						, (oow.recv_amt * oow.qty) * if(oow.ord_state = '30', 1, -1) as recv_amt
					from order_opt_wonga oow
					inner join order_opt oo on oo.ord_opt_no = oow.ord_opt_no and oo.ord_state = '30'
					inner join product p on oo.prd_cd = p.prd_cd
					inner join product_code pc on oo.prd_cd = pc.prd_cd
					left outer join store s on s.store_cd = oo.store_cd
					where
						oow.ord_state_date >= '$sdate' and oow.ord_state_date <= '$edate'
						and oow.ord_state in (30, 60, 61)
						and if( oow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
						$where
				) w
				left outer join sale_type st on w.sale_kind = st.sale_kind
				order by ord_month desc, sale_kind asc
			) t
		";
		
		$total_data = DB::select($sql);
		
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($rows),
				"total_data" => $total_data[0]
			),
			"body" => $rows
		]);
	}
}
