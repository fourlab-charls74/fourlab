<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal40Controller extends Controller
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
		return view(Config::get('shop.store.view') . '/sale/sal40', $values);
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
				sc.store_channel as store_channel_nm, 
				sc2.store_kind as store_kind_nm,
				a.store_nm, 
				concat(round(a.u_qty / a.qty * 100), ':', round((a.n_off_qty + n_on_qty) / a.qty * 100)) as ratio1,
				ifnull(concat(round(a.u_qty / (a.qty - a.n_on_qty) * 100), ':', round(a.n_off_qty / (a.qty - a.n_on_qty) * 100)), '0:0') as ratio2,
				a.qty, a.recv_amt,  
				a.u_qty, a.u_recv_amt, ifnull(b.ord_cnt, 0) as ord_cnt, ifnull(b.avg_qty, 0) as avg_qty, ifnull(c.join_cnt, 0) as join_cnt,
				a.n_off_qty, a.n_off_recv_amt, a.n_on_qty, a.n_on_recv_amt
			from (
				select
					s.store_channel, s.store_channel_kind,
					oo.store_cd, s.store_nm,
					sum(oow.qty * if(oow.ord_state = '30', 1, -1)) as qty,
					sum(oow.recv_amt * if(oow.ord_state = '30', 1, -1)) as recv_amt,
					sum(if(ifnull(om.user_id, '') <> '', oow.qty * if(oow.ord_state = '30', 1, -1), 0)) as u_qty,
					sum(if(ifnull(om.user_id, '') <> '', oow.recv_amt * if(oow.ord_state = '30', 1, -1), 0)) as u_recv_amt,
					sum(if(ifnull(om.user_id, '') = '' and oo.sale_kind <> '81', oow.qty * if(oow.ord_state = '30', 1, -1), 0)) as n_off_qty,
					sum(if(ifnull(om.user_id, '') = '' and oo.sale_kind <> '81', oow.recv_amt * if(oow.ord_state = '30', 1, -1), 0)) as n_off_recv_amt,
					sum(if(ifnull(om.user_id, '') = '' and ifnull(oo.sale_kind, '81') = '81', oow.qty * if(oow.ord_state = '30', 1, -1), 0)) as n_on_qty,
					sum(if(ifnull(om.user_id, '') = '' and ifnull(oo.sale_kind, '81') = '81', oow.recv_amt * if(oow.ord_state = '30', 1, -1), 0)) as n_on_recv_amt
				from order_opt_wonga oow
				inner join order_opt oo on oow.ord_opt_no = oo.ord_opt_no and oo.ord_state = '30'
				inner join order_mst om on oo.ord_no = om.ord_no
				inner join store s on oo.store_cd = s.store_cd
				where
					oow.ord_state_date >= '20240401'
					and oow.ord_state_date <= '20240430'
					and oow.ord_state in (30, 60, 61)
					and if( oow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
				group by s.store_channel, s.store_channel_kind, oo.store_cd
			) a
			left outer join (
				select
					a.store_cd, count(*) as ord_cnt, round(avg(qty), 1) as avg_qty
				from (
					select
						oo.store_cd, om.user_id, sum(oo.qty) as qty
					from order_opt_wonga oow
					inner join order_opt oo on oow.ord_opt_no = oo.ord_opt_no and oo.ord_state = '30'
					inner join order_mst om on oo.ord_no = om.ord_no
					inner join store s on oo.store_cd = s.store_cd
					where
						oow.ord_state_date >= '20240401'
						and oow.ord_state_date <= '20240430'
						and oow.ord_state in (30, 60, 61)
						and if( oow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
						and om.user_id <> ''
					group by oo.store_cd, om.user_id
				) a 
				group by a.store_cd
			) b on a.store_cd = b.store_cd
			left outer join (
				select
					ifnull(m.store_cd, 'F0001') as store_cd, count(*) as join_cnt
				from member m
				where
					m.regdate >= '2024-04-01 00:00:00'
					and m.regdate <= '2024-04-30 23:59:59'
				group by m.store_cd
			) c on a.store_cd = c.store_cd
			left outer join store_channel sc on a.store_channel = sc.store_channel_cd and sc.dep = 1 and sc.use_yn = 'Y'
			left outer join store_channel sc2 on a.store_channel = sc2.store_channel_cd and a.store_channel_kind = sc2.store_kind_cd and sc2.dep = 2 and sc2.use_yn = 'Y'
			where
				a.qty <> 0
			order by sc.store_channel, a.store_channel_kind, a.store_nm
		";
		$rows = DB::select($sql);

		$total_amt	= [];
		foreach($rows as $row){
			//if(!isset($total_amt[$row->ord_month])){
			//	$total_amt[$row->ord_month]	= 0;
			//}

			//$total_amt[$row->ord_month]	+= $row->recv_amt;
		}

		foreach($rows as $row){
			//$row->sg_ratio	= round(($row->recv_amt/$total_amt[$row->ord_month] * 100), 2);
		}

		$sql	= "
			select
				sum(a.qty) as qty, 
				sum(a.recv_amt) as recv_amt,  
				sum(a.u_qty) as u_qty, 
				sum(a.u_recv_amt) as u_recv_amt, 
				sum(ifnull(b.ord_cnt, 0)) as ord_cnt, 
				avg(ifnull(b.avg_qty, 0)) as avg_qty, 
				sum(ifnull(c.join_cnt, 0)) as join_cnt,
				sum(a.n_off_qty) as n_off_qty, 
				sum(a.n_off_recv_amt) as n_off_recv_amt, 
				sum(a.n_on_qty) as n_on_qty, 
				sum(a.n_on_recv_amt) as n_on_recv_amt
			from (
				select
					s.store_channel, s.store_channel_kind,
					oo.store_cd, s.store_nm,
					sum(oow.qty * if(oow.ord_state = '30', 1, -1)) as qty,
					sum(oow.recv_amt * if(oow.ord_state = '30', 1, -1)) as recv_amt,
					sum(if(ifnull(om.user_id, '') <> '', oow.qty * if(oow.ord_state = '30', 1, -1), 0)) as u_qty,
					sum(if(ifnull(om.user_id, '') <> '', oow.recv_amt * if(oow.ord_state = '30', 1, -1), 0)) as u_recv_amt,
					sum(if(ifnull(om.user_id, '') = '' and oo.sale_kind <> '81', oow.qty * if(oow.ord_state = '30', 1, -1), 0)) as n_off_qty,
					sum(if(ifnull(om.user_id, '') = '' and oo.sale_kind <> '81', oow.recv_amt * if(oow.ord_state = '30', 1, -1), 0)) as n_off_recv_amt,
					sum(if(ifnull(om.user_id, '') = '' and ifnull(oo.sale_kind, '81') = '81', oow.qty * if(oow.ord_state = '30', 1, -1), 0)) as n_on_qty,
					sum(if(ifnull(om.user_id, '') = '' and ifnull(oo.sale_kind, '81') = '81', oow.recv_amt * if(oow.ord_state = '30', 1, -1), 0)) as n_on_recv_amt
				from order_opt_wonga oow
				inner join order_opt oo on oow.ord_opt_no = oo.ord_opt_no and oo.ord_state = '30'
				inner join order_mst om on oo.ord_no = om.ord_no
				inner join store s on oo.store_cd = s.store_cd
				where
					oow.ord_state_date >= '20240401'
					and oow.ord_state_date <= '20240430'
					and oow.ord_state in (30, 60, 61)
					and if( oow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
				group by s.store_channel, s.store_channel_kind, oo.store_cd
			) a
			left outer join (
				select
					a.store_cd, count(*) as ord_cnt, round(avg(qty), 1) as avg_qty
				from (
					select
						oo.store_cd, om.user_id, sum(oo.qty) as qty
					from order_opt_wonga oow
					inner join order_opt oo on oow.ord_opt_no = oo.ord_opt_no and oo.ord_state = '30'
					inner join order_mst om on oo.ord_no = om.ord_no
					inner join store s on oo.store_cd = s.store_cd
					where
						oow.ord_state_date >= '20240401'
						and oow.ord_state_date <= '20240430'
						and oow.ord_state in (30, 60, 61)
						and if( oow.ord_state_date <= '20231109', oo.sale_kind is not null, 1=1)
						and om.user_id <> ''
					group by oo.store_cd, om.user_id
				) a 
				group by a.store_cd
			) b on a.store_cd = b.store_cd
			left outer join (
				select
					ifnull(m.store_cd, 'F0001') as store_cd, count(*) as join_cnt
				from member m
				where
					m.regdate >= '2024-04-01 00:00:00'
					and m.regdate <= '2024-04-30 23:59:59'
				group by m.store_cd
			) c on a.store_cd = c.store_cd
			where
				a.qty <> 0
		";

		$total_data = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total"			=> count($rows),
				"total_data"	=> $total_data[0]
			),
			"body" => $rows
		]);
	}
}
