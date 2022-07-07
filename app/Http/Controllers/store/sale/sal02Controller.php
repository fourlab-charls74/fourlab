<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal02Controller extends Controller
{
	public function index() {

		$sdate = Carbon::now()->subMonth()->startOfMonth()->format("Y-m");
        $m = Carbon::now()->subMonth()->startOfMonth();
        $lastofMonth = $m->lastOfMonth()->format("d");

        $months = [];
        for($i = 1;$i <= $lastofMonth;$i++){
            $m->addDays();
            $months[] = array( "day" => $i, "week" => $m->format("D"));
        }

		// 매장구분
		$sql = " 
			select *
			from __tmp_code
			where 
				code_kind_cd = 'com_type' and use_yn = 'Y' order by code_seq 
		";
		$com_types	= DB::select($sql);

		// 행사구분
		$sql = "
			select *
			from __tmp_code
			where
				code_kind_cd = 'event_cd' and use_yn = 'Y' order by code_seq
		";
		$event_cds	= DB::select($sql);

		// 판매유형
		$sql = "
			select *
			from __tmp_code
			where
				code_kind_cd = 'sell_type' and use_yn = 'Y' order by code_seq
		";
		$sell_types	= DB::select($sql);

		$values = [
            'sdate'         => $sdate,
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
            'months'        => $months
		];
        return view( Config::get('shop.store.view') . '/sale/sal02',$values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->format("Y-m"));
		$ym = str_replace("-","-",$sdate);
		$next_month = $sdate[-1] + 1;
		$edate = substr($sdate, 0, -1) . $next_month;

		// 검색 필드는 sql에 추후 추가 예정
		$com_type = $request->input('com_type');
		$sell_type = $request->input('sell_type');
        $sale_yn = $request->input('sale_yn','Y');

        $where = "";
        if($sale_yn == "Y"){
            $where = " and qty is not null";
        }

		$max_day = 31;

		$sum_qty = "";
		$sum_price = "";
		$sum_recv_amt = "";


		$yoil_codes = [];
		for ($i = 0; $i < $max_day; $i++) {
			$day = $i + 1;
			$comma = ($day == 31) ? "" : ",";
			$sum_qty .= "sum(if(day(m.ord_date) = ${day}, o.qty, 0)) as ${day}_qty${comma}";
			$sum_price .= "sum(if(day(m.ord_date) = ${day}, o.price*o.qty, 0)) as ${day}_price${comma}";
			$sum_recv_amt .= "sum(if(day(m.ord_date) = ${day}, o.recv_amt, 0)) as ${day}_recv_amt${comma}";

			// 해당 월의 모든 요일 구하기
			$day = sprintf("%02d", $day);
			$day = $sdate . "-${day}";
			$yoil_codes[$i] = date('w', strtotime($day));
		}

		$sql = /** @lang text */
            "
			select s.store_nm,c.code_val as store_type_nm,a.*,ifnull(p.amt,0) as proj_amt
			from store s left outer join (
				select 
					store_cd, sum(o.qty) as qty, sum(o.price*o.qty) as ord_amt, sum(o.recv_amt) as recv_amt,
					${sum_qty},
					${sum_price},
					${sum_recv_amt}
				from order_mst m inner join order_opt o on m.ord_no = o.ord_no 
				where m.ord_date >= :sdate and m.ord_date < :edate and m.store_cd <> ''
				group by store_cd
			) a on s.store_cd = a.store_cd
                left outer join store_sales_projection p on p.ym = :ym and s.`store_cd` = p.`store_cd`			
                left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
            where 1=1 $where                
		";
		$rows = DB::select($sql,[
		    'sdate' => $sdate,'edate' => $edate,"ym" => $ym
        ]);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows),
				"yoil_codes" => $yoil_codes
            ),
            "body" => $rows
        ]);
	}
}
