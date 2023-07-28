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
		$sdate		= str_replace("-", "", $request->input("sdate", date('Ymd')));
		$edate		= str_replace("-", "", $request->input("edate", date("Ymd")));
		$storage_cd	= $request->input('storage_cd');

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
					from
					(
						select
							hst.stock_state_date
						from product_stock_hst hst
						where
							hst.location_type = 'STORAGE'
							and hst.location_cd = :storage_cd
							and hst.stock_state_date >= :sdate and hst.stock_state_date <= :edate
						group by hst.stock_state_date
					) a
				";
			$row = DB::selectOne($sql, ['storage_cd' => $storage_cd, 'sdate' => $sdate, 'edate' => $edate]);
			$total = $row->total;
			if ($total > 0) {
				$page_cnt = (int)(($total - 1) / $page_size) + 1;
			}
		}
		
		//창고 총재고 정보
		$sql	= " select sum(qty) as period_out_qty from product_stock_storage where storage_cd = :storage_cd ";
		$row	= DB::selectOne($sql, ['storage_cd' => $storage_cd]);
		$total_qty	= $row->period_out_qty;
		
		//기간 후 재고 정보
		$sql	= "
			select
				ifnull(sum(hst.qty), 0) as period_out_qty
			from product_stock_hst hst
			where
				hst.location_type = 'STORAGE'
				and hst.location_cd = :storage_cd
				and hst.stock_state_date >= :sdate
		";
		$row	= DB::selectOne($sql, ['storage_cd' => $storage_cd, 'sdate' => $sdate]);
		$period_out_qty	= $row->period_out_qty;

		$sql	=
			"
				select
					a.stock_state_date,
					( a.storage_in_qty + a.rt_in_qty ) as in_qty,
					( a.storage_return_qty + a.storage_out_qty + a.rt_out_qty ) as out_qty,
					a.store_return_qty as return_qty,
					a.loss_qty as loss_qty,
					a.qty as period_in_qty,
					0 as term_qty
				from
				(
					select
						hst.stock_state_date,
						sum(if(hst.type = 1, hst.qty, 0)) as storage_in_qty,					-- 상품입고
						sum(if(hst.type = 16 and hst.qty > 0, hst.qty, 0)) as rt_in_qty,		-- 이동입고
						sum(if(hst.type = 9, hst.qty, 0)) as storage_return_qty,				-- 생산반품
						sum(if(hst.type = 17, hst.qty * -1, 0)) as storage_out_qty,				-- 매장출고
						sum(if(hst.type = 16 and hst.qty < 0, hst.qty * -1, 0)) as rt_out_qty,	-- 이동출고
						sum(if(hst.type = 11, hst.qty, 0)) as store_return_qty,					-- 매장반품
						sum(if(hst.type = 14, hst.qty * -1, 0)) as loss_qty,					-- LOSS
						sum(hst.qty) as qty														-- 기간재고
					from product_stock_hst hst
					where
						hst.location_type = 'STORAGE'
						and hst.location_cd = :storage_cd
						and hst.stock_state_date >= :sdate and hst.stock_state_date <= :edate
					group by hst.stock_state_date
				) a
	            limit $startno,$page_size
            ";
		
		$term_qty	= $total_qty - $period_out_qty;
		
		$rows = DB::select($sql, ['storage_cd' => $storage_cd, 'sdate' => $sdate, 'edate' => $edate]);
		foreach ($rows as $row) {
			$term_qty		= $term_qty + $row->period_in_qty;
			$row->term_qty	= $term_qty;
		}		

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
