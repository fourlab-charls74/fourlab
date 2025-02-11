<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class sal33Controller extends Controller
{
	public function index(Request $req)
	{
		$date = new DateTime($req->input('sdate', now()->startOfMonth()->sub(0, 'month')->format("Ym"). '01'));
		$sdate = $date->format('Y-m-d');
		$edate = $date->format('Y-m-t');

		$values = [
			'sdate'			=> $sdate,
			'edate'			=> $edate,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/sale/sal33', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd'));
		$edate = $request->input('edate',date("Ymd"));

		$store_channel		= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$store_cd       	= $request->input('store_no');
		$rt_type	       	= $request->input('rt_type');
		
		$sdate	= $sdate . " 00:00:00";
		$edate	= $edate . " 23:59:59";

		$where	= "";
		$in_where	= "";

		// 판매채널/매장구분 검색
		if($store_channel != "")		$where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if($store_channel_kind != "")	$where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		// 매장검색
		if ( $store_cd != "" ) {
			$where	.= " and s.store_cd = '$store_cd' ";
		}

		// RT 타입
		if ( $rt_type != "" ) {
			$in_where	.= " and psr.type = '$rt_type' ";
		}

		$sql	= "
			select
					rt.store_cd, sc.store_channel, sc.store_kind, s.store_nm
					, sum(out_rt_cnt) as out_rt_cnt
					, sum(out_req_cnt) as out_req_cnt
					, ifnull(round(sum(out_req_cnt)/sum(out_rt_cnt)*100),'-') as out_req_ratio
					, (sum(out_rt_cnt) - sum(out_req_cnt)) as out_ing_cnt
					, (sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt)) as out_end_cnt
					, ifnull(round((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt))/(sum(out_rt_cnt) - sum(out_req_cnt))*100),'-') as out_end_ratio
					, sum(out_rej_cnt) as out_rej_cnt
					, ifnull(round(sum(out_rej_cnt)/sum(out_rt_cnt)*100),'-') as out_rej_ratio
			
					, sum(in_rt_cnt) as in_rt_cnt
					, sum(in_req_cnt) as in_req_cnt
					, ifnull(round(sum(in_req_cnt)/sum(in_rt_cnt)*100),'-') as in_req_ratio
					, (sum(in_rt_cnt) - sum(in_req_cnt)) as in_ing_cnt
					-- , (sum(in_rec_cnt) + sum(in_prc_cnt)) as in_ing_cnt
					, (sum(in_rec_cnt) + sum(in_prc_cnt) + sum(in_fin_cnt)) as in_end_cnt
					, sum(in_fin_cnt) as in_fin_cnt
					, ifnull(round((sum(in_rec_cnt) + sum(in_prc_cnt) + sum(in_fin_cnt))/(sum(in_rt_cnt) - sum(in_req_cnt))*100),'-') as in_end_ratio
					-- , ifnull(round(sum(in_fin_cnt)/sum(in_rt_cnt)*100), '-') as in_end_ratio
					, sum(in_rej_cnt) as in_rej_cnt
					, ifnull(round(sum(in_rej_cnt)/sum(in_rt_cnt)*100),'-') as in_rej_ratio
					
					, concat(
						' ',
						ifnull(round((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt))/((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt)) + sum(in_fin_cnt)) * 100), '-'), 
						' : ',
						ifnull(round(sum(in_fin_cnt)/((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt)) + sum(in_fin_cnt)) * 100), '-')
					) as rt_ratio
			from
			(
				select
					psr.dep_store_cd as store_cd
					, 1 as out_rt_cnt
					, if(psr.state = '10', 1, 0) as out_req_cnt
					, if(psr.state = '20', 1, 0) as out_rec_cnt
					, if(psr.state = '30', 1, 0) as out_prc_cnt
					, if(psr.state = '40', 1, 0) as out_fin_cnt
					, if(psr.state = '-10', 1, 0) as out_rej_cnt
					, 0 as in_rt_cnt
					, 0 as in_req_cnt
					, 0 as in_rec_cnt
					, 0 as in_prc_cnt
					, 0 as in_fin_cnt
					, 0 as in_rej_cnt
				from product_stock_rotation psr
				where
					psr.rt >= '$sdate' and psr.rt <= '$edate' and psr.del_yn = 'N'
					$in_where
				
				union all
			
				select
					psr.store_cd as store_cd
					, 0 as out_rt_cnt
					, 0 as out_req_cnt
					, 0 as out_rec_cnt
					, 0 as out_prc_cnt
					, 0 as out_fin_cnt
					, 0 as out_rej_cnt
					, 1 as in_rt_cnt
					, if(psr.state = '10', 1, 0) as in_req_cnt
					, if(psr.state = '20', 1, 0) as in_rec_cnt
					, if(psr.state = '30', 1, 0) as in_prc_cnt
					, if(psr.state = '40', 1, 0) as in_fin_cnt
					, if(psr.state = '-10', 1, 0) as in_rej_cnt
				from product_stock_rotation psr
				where
					psr.rt >= '$sdate' and psr.rt <= '$edate' and psr.del_yn = 'N'
					$in_where
			) rt
			inner join store s on s.store_cd = rt.store_cd
			inner join store_channel sc on sc.store_channel_cd = s.store_channel and sc.store_kind_cd = s.store_channel_kind
			where
				s.use_yn = 'Y'
				$where
			group by rt.store_cd
			order by sc.store_channel, sc.store_kind
		";
		$result = DB::select($sql);

		/*
		$result = collect($rows)->map(function ($row) {
			
			if($row->out_req_ratio != "-")	$out_req_ratio	= $row->out_req_ratio . "%";
			else							$out_req_ratio	= $row->out_req_ratio;

			if($row->out_end_ratio != "-")	$out_end_ratio	= $row->out_end_ratio . "%";
			else							$out_end_ratio	= $row->out_end_ratio;

			if($row->out_rej_ratio != "-")	$out_rej_ratio	= $row->out_rej_ratio . "%";
			else							$out_rej_ratio	= $row->out_rej_ratio;
			
			$array = array(
				"store_channel"	=> $row->store_channel,
				"store_kind"	=> $row->store_kind,
				"store_cd"		=> $row->store_cd,
				"store_nm"		=> $row->store_nm,
				"out_rt_cnt"	=> $row->out_rt_cnt,
				"out_req_cnt"	=> $row->out_req_cnt,
				"out_req_ratio"	=> $out_req_ratio,
				"out_ing_cnt"	=> $row->out_ing_cnt,
				"out_end_cnt"	=> $row->out_end_cnt,
				"out_end_ratio"	=> $out_end_ratio,
				"out_rej_cnt"	=> $row->out_rej_cnt,
				"out_rej_ratio"	=> $row->out_rej_ratio,
			);
			
			return $array;

		})->all();
		*/

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result)
			),
			"body" => $result
		]);
	}
}
