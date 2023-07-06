<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal09Controller extends Controller
{
	public function index(Request $request)
	{
		// $sdate = Carbon::now()->startOfMonth()->format("Y-m"); // 이번 달 기준
		// $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 1달전 기준 (테스트 용)

		$mutable = Carbon::now();
        $sdate = $request->input('sdate', now()->startOfMonth()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));

		// 매장구분
		$sql = " 
			select *
			from code
			where 
				code_kind_cd = 'store_type' and use_yn = 'Y' order by code_seq 
		";
		$store_types = DB::select($sql);

		// 행사구분 - 추후 논의사항
		$sql = "
			select *
			from __tmp_code
			where
				code_kind_cd = 'event_cd' and use_yn = 'Y' order by code_seq
		";
		$event_cds = DB::select($sql);

		// 판매유형 - 추후 논의사항
		$sql = "
			select *
			from __tmp_code
			where
				code_kind_cd = 'sell_type' and use_yn = 'Y' order by code_seq
		";
		$sell_types	= DB::select($sql);

		$months = [];
		$sd = Carbon::parse($sdate);
        while($sd <= Carbon::parse($edate)){
            $months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y-m") ];
            $sd->addMonth();
        }

		$values = [
			'sdate' 		=> $sdate,
            'edate' 		=> $edate,
			'months'		=> $months,
			'store_types'	=> $store_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
        return view( Config::get('shop.store.view') . '/sale/sal09',$values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->startOfMonth()->subMonth()->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));

		$months_count = Carbon::parse($sdate)->diffInMonths(Carbon::parse($edate)->lastOfMonth());

		$list_type = $request->input('list_type', "qty");
		$store_cd = $request->input('store_cd', "");
		$goods_no = $request->input('goods_no', "");
		$goods_nm = $request->input('goods_nm', "");
		$brand_cd = $request->input('brand_cd', "");
		$style_no = $request->input('style_no', "");
		$sale_yn = $request->input('sale_yn','Y');
		// 판매 유형은 추후 반영 예정
		$sell_type = $request->input('sell_type');
		$store_channel	= $request->input("store_channel", '');
		$store_channel_kind	= $request->input("store_channel_kind", '');

		// 검색조건 필터링
		$where = "";
		if ($brand_cd != "") $where .= " and b.brand = '" . Lib::quote($brand_cd) . "' ";

		$goods_no = preg_replace("/\s/", ",", $goods_no);
		$goods_no = preg_replace("/\t/", ",", $goods_no);
		$goods_no = preg_replace("/\n/", ",", $goods_no);
		$goods_no = preg_replace("/,,/", ",", $goods_no);
		if ($goods_no != "") {
			$goods_nos = explode(",", $goods_no);
			if (count($goods_nos) > 1) {
				if (count($goods_nos) > 500) array_splice($goods_nos, 500);
				$in_goods_nos = join(",", $goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
			}
		}

		if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%'";
		if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%'";

		if ($sell_type != "") $where .= "and o.sale_kind = '$sell_type'";

        $where2 = "";
        // if ($sale_yn == "Y") $where2 .= " and o.qty is not null";
		if ($store_cd != "") $where2 .= " and s.store_cd like '" . Lib::quote($store_cd) . "%'";
		if ($store_channel != "") $where2 .= " and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where2 .= " and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";


		// 전달 받은 리스트 타입에 따라 합계 쿼리 구분
		$sum = "";
		$sum_true = "";
		$sum_false = 0;
		switch ($list_type) {
			case 'qty':
				$sum_true = "o.qty";
				break;
			case 'ord_amt':
				$sum_true = "o.price*o.qty";
				break;
			case 'recv_amt':
				$sum_true = "o.recv_amt";
				break;
			default:
				break;
		}

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
			$sum_month_prev .= "
				sum(if(date_format(m.ord_date,'%Y%m') = '${prev_sdate}', o.price*o.qty,0)) as prev_ord_amt_${Ym}, 
				sum(if(date_format(m.ord_date,'%Y%m') = '${prev_sdate}', o.recv_amt,0)) as prev_recv_amt_${Ym},
			";

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
			select s.store_nm,c.code_val as store_type_nm,a.*, b.*, p.*, ifnull(p.amt,0) as proj_amt
			from store s 
			left outer join (
				select m.store_cd, sum(o.price*o.qty) as ord_amt, sum(o.recv_amt) as recv_amt,
						${sum_month_prev}
						${sum_month_others}
					from order_mst m 
						inner join order_opt o on m.ord_no = o.ord_no 
					where m.ord_date >= '${prev_sdate}' and m.ord_date < '${next_edate}' and m.store_cd <> ''
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
			inner join 
			(
				select 
					ssp.store_cd, sum(amt) as proj_amt, ssp.amt,
					${sum_proj_amt}
				from store_sales_projection ssp
				where ym >= '${ym_s}' and ym <= '${ym_e}'
				group by ssp.store_cd
			) p on s.store_cd = p.store_cd 		
			left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
            where 1=1 $where2
		";

		$rows = DB::select($sql, ['sdate' => $sdate, 'edate' => $edate]);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows),
				'col_keys' => $col_keys
            ),
            "body" => $rows
        ]);
	}
}
