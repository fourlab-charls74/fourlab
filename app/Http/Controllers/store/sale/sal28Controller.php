<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class sal28Controller extends Controller
{
	public function index()
	{
		$sdate		= date('Y-m-d');
		$edate		= date("Y-m-d");
		$storages	= SLib::getStorage();

		$values = [
			"edate"		=> $edate,
			"sdate"		=> $sdate,
			"storages"	=> $storages
		];
		return view(Config::get('shop.store.view') . '/sale/sal28', $values);
	}
	
	public function search(Request $request)
	{
		$sdate	= str_replace("-", "", $request->input("sdate", date('Ymd')));
		$edate	= str_replace("-", "", $request->input("edate", date("Ymd")));

		$where	= "";

		$limit	= 100;

		$page	= $request->input("page", 1);
		
		if ($page < 1 or $page == "") $page = 1;

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;

		$total		= 0;
		$page_cnt	= 0;

		if ($page == 1) {
			// 갯수 얻기
			$sql =
				" 
				select
				    count(*) as total
				    /*
					m.d,
					ifnull(( hst_gr.storage_in_qty + hst_gr.rt_in_qty ), 0) as in_qty,
					ifnull(( hst_gr.storage_return_qty + hst_gr.storage_out_qty + hst_gr.rt_out_qty ), 0) as out_qty,
					ifnull(hst_gr.store_return_qty, 0) as return_qty,
					ifnull(hst_gr.loss_qty, 0) as loss_qty
					*/
				from mdate m
				where
					d >= :sdate and d <= :edate
				";
			$row = DB::selectOne($sql, ['sdate' => $sdate, 'edate' => $edate]);
			$total = $row->total;
			if ($total > 0) {
				$page_cnt = (int)(($total - 1) / $page_size) + 1;
			}
		}

		$sql =
			"
			select
				m.d,
				ifnull(( hst_gr.storage_in_qty + hst_gr.rt_in_qty ), 0) as in_qty,
				ifnull(( hst_gr.storage_return_qty + hst_gr.storage_out_qty + hst_gr.rt_out_qty ), 0) as out_qty,
				ifnull(hst_gr.store_return_qty, 0) as return_qty,
				ifnull(hst_gr.loss_qty, 0) as loss_qty
			from mdate m
			left outer join 
			(
				select
					hst.stock_state_date,
					sum(if(hst.type = 1, hst.qty, 0)) as storage_in_qty,				-- 상품입고
					sum(if(hst.type = 16 and hst.qty > 0, hst.qty, 0)) as rt_in_qty,	-- 이동입고
			
					sum(if(hst.type = 9, hst.qty, 0)) as storage_return_qty,			-- 생산반품
					sum(if(hst.type = 17, hst.qty * -1, 0)) as storage_out_qty,			-- 매장출고
					sum(if(hst.type = 16 and hst.qty < 0, hst.qty * -1, 0)) as rt_out_qty,	-- 이동출고
			
					sum(if(hst.type = 11, hst.qty, 0)) as store_return_qty,				-- 매장반품
			
					sum(if(hst.type = 14, hst.qty * -1, 0)) as loss_qty					-- LOSS
				from product_stock_hst hst
				where
					hst.location_type = 'STORAGE'
					and hst.location_cd = 'A0009'
					and hst.stock_state_date >= :sdate1 and hst.stock_state_date <= :edate1
				group by hst.stock_state_date
			) as hst_gr on m.d = hst_gr.stock_state_date
			where
				d >= :sdate2 and d <= :edate2
			order by d
            limit $startno,$page_size
            ";

		$rows = DB::select($sql, ['sdate1' => $sdate, 'edate1' => $edate, 'sdate2' => $sdate, 'edate2' => $edate]);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($rows)
			),
			"body" => $rows
		]);
		
	}
}
