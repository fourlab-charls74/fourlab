<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
class sal35Controller extends Controller
{
	public function index()
	{
		$values = [
			'sdate' => date("Y-m"),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/sale/sal35', $values);
	}
	// 검색
	public function search(Request $request)
	{
		$sdate = $request->input("sdate");
		$store_channel = $request->input("store_channel");
		$store_kind = $request->input("store_kind");
		$store_cd = $request->input("store_cd");

		$ym = str_replace("-", "", $sdate);
		$ord_state_sdate = str_replace("-","", $sdate)."01";
		$ord_state_edate = str_replace("-","", $sdate)."31";
		$last_year_sdate = Carbon::parse($sdate)->subYear()->format('Ym')."01";
		$last_year_edate = Carbon::parse($sdate)->subYear()->format('Ym')."31";

		/*  
		* 달성율 = 판매금액 / 목표금액 * 100
		* 신장율 = 판매금액 / 전년판매 * 100
		* */

		$sql = "
			select
				s.store_nm_eng as stores
				, ifnull(ssp.amt,0) as proj_amt 
				, sum(ifnull(w.recv_amt,0)) as recv_amt
				, sum(ifnull(w.last_recv_amt,0)) as last_recv_amt
				, ifnull(round(sum(w.recv_amt) / ssp.amt * 100, 2), 0.00) as progress_proj_rate
				, ifnull(round(sum(w.recv_amt) / sum(w.last_recv_amt) * 100, 2), 0.00) as elongation_rate
			from store s
				left outer join store_sales_projection ssp on s.store_cd = ssp.store_cd and ssp.ym = '$ym'
				left outer join (
				    select
				         if(o.ord_state > 30, ow.recv_amt * -1, ow.recv_amt) as recv_amt
				    	, 0 as last_recv_amt
				    	, o.store_cd
				    	, ow.ord_state_date
				    	, o.ord_state
				    from order_opt_wonga ow
				    inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
				    where 1=1 
				      	and ow.ord_state_date between '$ord_state_sdate' and '$ord_state_edate' 
				      	and ow.ord_state in (30, 60, 61) 
				      	and o.ord_state = '30'
				    	and if( ow.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
				    
				    union all
				    
				     select
				         0 as recv_amt
				        , if(o.ord_state > 30, ow.recv_amt * -1, ow.recv_amt) as last_recv_amt
				    	, o.store_cd
				    	, ow.ord_state_date
				    	, o.ord_state
				    from order_opt_wonga ow
				    inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
				    where 1=1 
				      	and ow.ord_state_date between '$last_year_sdate' and '$last_year_edate' 
				      	and ow.ord_state in (30, 60, 61) 
				      	and o.ord_state = '30'
				    	and if( ow.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
				) w on w.store_cd = s.store_cd
				left outer join store_channel sc on sc.store_channel_cd = s.store_channel and sc.dep = '1'
				left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind
			where 1=1 and s.retail_store_sales_yn = 'Y' 
			group by  s.store_cd, s.store_nm_eng, ssp.amt
		";

		$rows = DB::select($sql);

		$sql = "
			select
			    sum(ifnull(a.proj_amt,0)) as total_proj_amt
			    , sum(ifnull(a.recv_amt,0)) as total_recv_amt
				, sum(ifnull(a.last_recv_amt,0)) as total_last_recv_amt
				, ifnull(round(sum(a.recv_amt) / sum(a.proj_amt) * 100, 2), 0.00) as total_progress_proj_rate
				, ifnull(round(sum(a.recv_amt) / sum(a.last_recv_amt) * 100, 2), 0.00) as total_elongation_rate
			from (    
				select
					s.store_nm_eng as stores
					, ssp.amt as proj_amt
					, sum(w.recv_amt) as recv_amt
					, sum(w.last_recv_amt) as last_recv_amt
					, round(sum(w.recv_amt) / ssp.amt * 100, 2) as progress_proj_rate
					, round(sum(w.recv_amt) / sum(w.last_recv_amt) * 100, 2) as elongation_rate
				from store s
					left outer join store_sales_projection ssp on s.store_cd = ssp.store_cd and ssp.ym = '$ym'
					left outer join (
						select
							 if(o.ord_state > 30, ow.recv_amt * -1, ow.recv_amt) as recv_amt
							, 0 as last_recv_amt
							, o.store_cd
							, ow.ord_state_date
							, o.ord_state
						from order_opt_wonga ow
						inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
						where 1=1 
							and ow.ord_state_date between '$ord_state_sdate' and '$ord_state_edate' 
							and ow.ord_state in (30, 60, 61) 
							and o.ord_state = '30'
							and if( ow.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						
						union all
						
						 select
							 0 as recv_amt
							, if(o.ord_state > 30, ow.recv_amt * -1, ow.recv_amt) as last_recv_amt
							, o.store_cd
							, ow.ord_state_date
							, o.ord_state
						from order_opt_wonga ow
						inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
						where 1=1 
							and ow.ord_state_date between '$last_year_sdate' and '$last_year_edate' 
							and ow.ord_state in (30, 60, 61) 
							and o.ord_state = '30'
							and if( ow.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
					) w on w.store_cd = s.store_cd
					left outer join store_channel sc on sc.store_channel_cd = s.store_channel and sc.dep = '1'
					left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind
				where 1=1 and s.retail_store_sales_yn = 'Y' 
				group by  s.store_cd, s.store_nm_eng, ssp.amt
			) a
		";

		$result = DB::select($sql);

		return response()->json([
			'code' => 200,
			'head' => array(
				'total' => count($rows),
				"total_data" => $result[0]??''
			),
			'body' => $rows
		]);


	}

	public function search2(Request $request)
	{
		$sdate = $request->input("sdate");
		$store_channel = $request->input("store_channel");
		$store_kind = $request->input("store_kind");
		$store_cd = $request->input("store_cd");

		$ym = str_replace("-", "", $sdate);
		$ord_state_sdate = str_replace("-","", $sdate)."01";
		$ord_state_edate = str_replace("-","", $sdate)."31";
		$last_year_sdate = Carbon::parse($sdate)->subYear()->format('Ym')."01";
		$last_year_edate = Carbon::parse($sdate)->subYear()->format('Ym')."31";

		/*  
		* 달성율 = 판매금액 / 목표금액 * 100
		* 신장율 = 판매금액 / 전년판매 * 100
		* */

		$sql = "
			select
				code_id
				, code_val
				, code_val2
			from code
			where code_kind_cd = 'CHANNELS'
			order by code_seq asc
		";

		$channels = DB::select($sql);

		$channel_data = [];

		foreach ($channels as $channel) {
			$where = "";
			$groupby  = "";
			if ($channel->code_id == 'shop') {
				$where = "s.shop_yn = 'Y' ";
				$groupby = "s.shop_yn";
			} elseif ($channel->code_id == 'consignment') {
				$where = "s.consignment_yn = 'Y' ";
				$groupby = "s.consignment_yn";
			} elseif ($channel->code_id == 'online') {
				$where = "s.online_yn = 'Y' ";
				$groupby = "s.online_yn";
			} else {
				$where = "s.wholesale_yn = 'Y' ";
				$groupby = "s.wholesale_yn";
			}

			$sql = "
				select
					'$channel->code_val' as channels
					, sum(a.proj_amt) as channel_proj_amt
					, sum(a.recv_amt) as channel_recv_amt
				    , round(sum(a.recv_amt) / sum(a.proj_amt) * 100, 2) as channel_progress_proj_rate
				    , round(sum(a.recv_amt) / sum(a.last_recv_amt) * 100, 2) as channel_elongation_rate
					, sum(a.last_recv_amt) as channel_last_recv_amt
				from (    
					select
						s.store_nm_eng as stores
						, ssp.amt as proj_amt
						, sum(w.recv_amt) as recv_amt
						, sum(w.last_recv_amt) as last_recv_amt
						, round(sum(w.recv_amt) / ssp.amt * 100, 2) as progress_proj_rate
						, round(sum(w.recv_amt) / sum(w.last_recv_amt) * 100, 2) as elongation_rate
					from store s
						left outer join store_sales_projection ssp on s.store_cd = ssp.store_cd and ssp.ym = '$ym'
						left outer join (
							select
								 if(o.ord_state > 30, ow.recv_amt * -1, ow.recv_amt) as recv_amt
								, 0 as last_recv_amt
								, o.store_cd
								, ow.ord_state_date
								, o.ord_state
							from order_opt_wonga ow
							inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
							where 1=1 
								and ow.ord_state_date between '$ord_state_sdate' and '$ord_state_edate' 
								and ow.ord_state in (30, 60, 61) 
								and o.ord_state = '30'
								and if( ow.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
							
							union all
							
							 select
								 0 as recv_amt
								, if(o.ord_state > 30, ow.recv_amt * -1, ow.recv_amt) as last_recv_amt
								, o.store_cd
								, ow.ord_state_date
								, o.ord_state
							from order_opt_wonga ow
							inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
							where 1=1 
								and ow.ord_state_date between '$last_year_sdate' and '$last_year_edate' 
								and ow.ord_state in (30, 60, 61) 
								and o.ord_state = '30'
								and if( ow.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						) w on w.store_cd = s.store_cd
						left outer join store_channel sc on sc.store_channel_cd = s.store_channel and sc.dep = '1'
						left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind
					where 1=1 and  $where
					group by  s.store_cd
				) a
			";

			$rows = DB::select($sql);
			$channel_data[] = $rows[0]??[];
		}



		return response()->json([
			'code' => 200,
			'head' => array(
				'total' => count($channel_data),
			),
			'body' => $channel_data
		]);


	}
}
