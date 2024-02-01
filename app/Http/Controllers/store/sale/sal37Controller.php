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

class sal37Controller extends Controller
{
	public function index(Request $request)
	{
		$sdate = $request->input('sdate', now()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));
		$brand = $request->input('brand', 'F');
		
		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
			'brand' => $brand,
		];
		return view(Config::get('shop.store.view') . '/sale/sal37', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));
		$brand = $request->input('brand', 'F');

		$months = [];
		$sd = Carbon::parse($sdate);
		while($sd <= Carbon::parse($edate)){
			$months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y-m") ];
			$sd->addMonth();
		}
		
		$where = "";
		if ($brand == 'F') $where .= " and pc.brand = 'F'";
		if ($brand == 'W') $where .= " and pc.brand = 'W'";
		if ($brand == 'P') $where .= " and pc.brand = 'P'";
		if ($brand == 'TR') $where .= " and pc.brand = 'TR'";
		
		$page = $request->input('page', 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $request->input('limit', 500);
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";
		
		$total_qty = "";
		$total_recv_amt = "";
		foreach ($months as $month) {
			$total_qty .= ", if(o.ord_state > 30 and o.ord_state_date <= '$month[val]31', sum(o.qty) * -1, sum(o.qty)) as $month[val]_total_qty";
			$total_recv_amt .= ", if(o.ord_state > 30 and o.ord_state_date <= '$month[val]31', sum(o.recv_amt) * -1, sum(o.recv_amt)) as $month[val]_total_recv_amt";
		}
		
		
		$sql = "
			select
				concat(pc.brand, pc.year) as brand_year
				, concat(pc.brand, pc.year, pc.season) as season_brand
				$total_qty
				$total_recv_amt
			from product_code pc
				inner join (
					select
						w.qty
						, o.recv_amt
						, o.prd_cd
						, w.ord_state
						, w.ord_state_date
					from order_opt_wonga w
						inner join order_opt o on o.ord_opt_no = w.ord_opt_no
					where o.ord_date <= '2023-09-31'
						and w.ord_state in (30, 60, 61) and o.ord_state = '30'
						and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1) 
				) o on o.prd_cd = pc.prd_cd
			where 1=1 $where and pc.year >= 18
			group by concat(pc.brand, pc.year, pc.season)
			order by pc.brand, pc.year, pc.season asc
		";
		
		$rows = DB::select($sql);

		

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => 0,
				"page" => $page,
				"months" => $months,
			),
			"body" => $rows
		]);
	}
	

}
