<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

class sal17Controller extends Controller
{
	//
	public function index(Request $request)
	{
        $sdate = $request->input('sdate', now()->startOfMonth()->subMonth()->format("Y-m"));
        $edate = $request->input('edate', now()->format("Y-m"));
        $store_type = $request->input('store_type', "");
        $store_cd = $request->input('store_cd', "");

		$sql = "
			select 
				store_nm
			from store
			where store_cd = '$store_cd'
		";
		$store_nm = DB::selectOne($sql);


		// 매장구분
		$sql = /** @lang text */
            " 
			select *
			from code
			where 
				code_kind_cd = 'store_type' and use_yn = 'Y' order by code_seq 
		";
		$store_types = DB::select($sql);

		$months = [];
		$sd = Carbon::parse($sdate);
        while($sd <= Carbon::parse($edate)){
            $months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y-m") ];
            $sd->addMonth();
        }

        // // 행사구분 - 추후 논의사항
		// $sql = "
		// 	select *
		// 	from __tmp_code
		// 	where
		// 		code_kind_cd = 'event_cd' and use_yn = 'Y' order by code_seq
		// ";
		// $event_cds = DB::select($sql);

		// // 판매유형 - 추후 논의사항
		// $sql = "
		// 	select *
		// 	from __tmp_code
		// 	where
		// 		code_kind_cd = 'sell_type' and use_yn = 'Y' order by code_seq
		// ";
		// $sell_types	= DB::select($sql);

		$values = [
            'sdate'         => $sdate,
            'edate'         => $edate,
			'months'	    => $months,
			'store_types'	=> $store_types,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
			'store'         => DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', '=', $store_cd)->first(),   
		];
        return view( Config::get('shop.store.view') . '/sale/sal17', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->startOfMonth()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));

		$months_count = Carbon::parse($sdate)->diffInMonths(Carbon::parse($edate)->lastOfMonth());

		$store_cd = $request->input('store_cd', "");
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$store_yn = $request->input('store_yn');

		$where = "";
		if ($store_cd != "") $where .= " and s.store_cd like '" . Lib::quote($store_cd) . "%'";
		if ($store_channel != "") $where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
		if ($store_yn != "") $where .= "and s.use_yn = '" . Lib::quote($store_yn). "'";

		/**
		 * 합계 쿼리 작성
		 */
		$sum_month_prev = "";
		$sum_month_others = "";
		$sum_last_year = "";
		$sum_proj_amt = "";

		$col_keys = [];
		$prev_sdate = Carbon::parse($sdate)->subMonth()->format("Ym");
		$Ym = (int)str_replace("-", "", $sdate);
		for ( $i = 0; $i <= $months_count; $i++ ) {
			$comma = ($i == $months_count) ? "" : ",";

			// sdate 기준 저번 달 주문금액, 결제금액 가져오기
//			$sum_month_prev .= "
//				sum(if(date_format(m.ord_date,'%Y%m') = '${prev_sdate}', o.price*o.qty,0)) as prev_ord_amt_${Ym}, 
//				sum(if(date_format(m.ord_date,'%Y%m') = '${prev_sdate}', o.recv_amt,0)) as prev_recv_amt_${Ym},
//			";

			// 기간 이내의 주문금액, 결제금액 가져오기
			$sum_month_others .= " 
				sum(if(date_format(m.ord_date,'%Y%m') = '${Ym}', o.price*o.qty,0)) as ord_amt_${Ym}, 
				sum(if(date_format(m.ord_date,'%Y%m') = '${Ym}', o.recv_amt,0)) as recv_amt_${Ym}${comma}
			";

			// 기간 이내의 매장목표 가져오기
			$sum_proj_amt .= "
				sum(if(ym = '${Ym}',amt,0)) as proj_amt_${Ym}${comma}
			";

			// 기간 이내의 작년 주문금액, 결제금액 가져오기
			$last_year = (int)substr($Ym, 0, 4) - 1;
			$month = substr($Ym, 4, 2);
			$last_Ym = $last_year . $month;
			$sum_last_year .= " 
				sum(if(date_format(m.ord_date,'%Y%m') = '${last_Ym}', o.price*o.qty,0)) as last_ord_amt_${Ym}, 
				sum(if(date_format(m.ord_date,'%Y%m') = '${last_Ym}', o.recv_amt,0)) as last_recv_amt_${Ym}${comma}
			";

			// 12월 넘어가는 경우 01월로 변경, 아닌 경우 한달을 더해줌
			array_push($col_keys, (int)$Ym);
			$year = substr($Ym, 0, 4);
			$month = substr($Ym, 4, 2);
			if ((int)$month >= 12) {
				$year = (int)$year + 1;
				$month = sprintf("%02d", 1);
				$Ym = $year . $month;
			} else {
				$Ym = $year . sprintf("%02d", (int)$month + 1);
			}

			// 다음달의 전월 구하기
			$year = substr($Ym, 0, 4);
			$month = substr($Ym, 4, 2);
			$f_ym = $year . "-" . $month;
			$prev_sdate = Carbon::parse($f_ym)->subMonth()->format("Ym");
		}

		$ym_s = str_replace("-", "", $sdate);
		$ym_e = str_replace("-", "", $edate);
		$prev_sdate = Carbon::parse($sdate)->subMonth()->format("Y-m");
		
		$next_edate = Carbon::parse($edate)->addMonth()->format("Y-m");

		$last_year_sdate = Carbon::parse($sdate)->subYear()->format("Y-m");
		$last_year_next_edate = Carbon::parse($edate)->subYear()->addMonth()->format("Y-m");

		$sql = /** @lang text */
            "
			select s.store_cd as scd,s.store_nm, sc.store_channel as store_channel, sc2.store_kind as store_channel_kind,a.*,b.*,p.*
				from store s 
				left outer join
				( 
					select m.store_cd, sum(o.price*o.qty) as ord_amt, sum(o.recv_amt) as recv_amt,
						${sum_month_others}
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no 
					where m.ord_date > '${sdate}' and m.ord_date < '${next_edate}' and m.store_cd <> ''
					group by m.store_cd
				) a on s.store_cd = a.store_cd 
				left outer join 
				(
					select 
						m.store_cd, sum(o.recv_amt) as last_recv_amt,
						${sum_last_year}
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no 
					where m.ord_date >= '${last_year_sdate}' and m.ord_date < '${last_year_next_edate}' and m.store_cd <> ''
					group by m.store_cd
				) b on s.store_cd = b.store_cd 
				left outer join 
				(
					select 
						ssp.store_cd, sum(amt) as proj_amt,
						${sum_proj_amt}
					from store_sales_projection ssp
					where ym >= '${ym_s}' and ym <= '${ym_e}'
					group by ssp.store_cd
				) p on s.`store_cd` = p.`store_cd` 
				-- left outer join `code` c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
				left outer join store_channel sc on sc.store_channel_cd = s.store_channel and sc.dep = '1'
				left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind
			where 1=1 ${where}
			order by scd
		";

		$rows = DB::select($sql, ['sdate' => $sdate, 'edate' => $edate]);

		return response()->json([
			'code' => 200,
			'head' => array(
				'total' => count($rows),
				'col_keys' => $col_keys
			),
			'body' => $rows
		]);

	}

	// 자동저장 기능 삭제 후 수동저장기능으로 변경
	public function update(Request $request)
	{
		$data = $request->input("data");
		$sdate = $request->input("sdate");
		$edate = $request->input("edate");
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$start_date = new DateTime($sdate);
		$end_date = new DateTime($edate);
		$interval = new DateInterval('P1M'); // P1M은 1달을 의미합니다.
		$period = new DatePeriod($start_date, $interval, $end_date);

		$date_arr = [];
		foreach ($period as $date) {
			array_push($date_arr, $date->format('Ym'));
		}
		$date_arr[] = $end_date->format('Ym');

		try {
			DB::beginTransaction();

			$upsertData = [];

			foreach ($date_arr as $ym) {
				foreach ($data as $d) {

					$proj_amt = str_replace(",","",$d['proj_amt_' . $ym])??0;
					$store_cd = $d['scd'];
	
					$upsertData[] = [
						'store_cd' => $store_cd,
						'ym' => $ym,
						'amt' => $proj_amt,
						'uid' => $admin_id,
						'unm' => $admin_nm,
						'rt' => now(),
						'ut' => now(),
					];
				}
			}

			DB::table('store_sales_projection')->upsert(
				$upsertData,
				['store_cd', 'ym'],
				['amt', 'uid', 'unm', 'rt', 'ut']
			);

			DB::commit();
			$code = 200;
			$msg = "매장별 목표가 저장되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([
			"code" => $code,
			"msg" => $msg
		]);
	}
}
