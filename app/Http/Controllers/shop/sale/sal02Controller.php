<?php

namespace App\Http\Controllers\shop\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal02Controller extends Controller
{
	public function index() 
	{

		 $sdate = Carbon::now()->startOfMonth()->format("Y-m"); // 이번 달 기준
//		 $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 1달전 기준 (테스트 용)

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

		$values = [
            'sdate'         => $sdate,
			'sell_types'	=> $sell_types
		];
        return view( Config::get('shop.shop.view') . '/sale/sal02',$values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->format("Ym"));
		$list_type = $request->input('list_type', "qty");
		$store_cd = Auth('head')->user()->store_cd;
		$goods_no = $request->input('goods_no', "");
		$goods_nm = $request->input('goods_nm', "");
		$brand_cd = $request->input('brand_cd', "");
		$style_no = $request->input('style_no', "");

		// 판매 유형은 추후 반영 예정
		$sell_type = $request->input('sell_type');

		$ym = str_replace("-", "", $sdate);
		$edate = Carbon::parse($sdate)->firstOfMonth()->addMonth()->format("Ymd");

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
		if ($store_cd != "") $where2 .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";

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

		// 요일코드 추가 및 날짜별 합계 쿼리 적용
		$last_day = Carbon::parse($sdate)->endOfMonth()->toDateString();
		$max_day = substr($last_day, 8, 2);
		$yoil_codes = [];
		for ($i = 0; $i < $max_day; $i++) {
			$day = $i + 1;
			$comma = ($day == $max_day) ? "" : ",";
//			$sum .= "sum(if(day(m.ord_date) = ${day}, ${sum_true}, ${sum_false})) as ${day}_val${comma}";
			$sum .= "sum(if(day(w.ord_state_date) = ${day} ,if(w.ord_state > 30, ${sum_true} * -1, ${sum_true}),0)) as ${day}_val${comma}";

			// 해당 월의 모든 요일 구하기
			$day = sprintf("%02d", $day);
			$day = $sdate . "-${day}";
			$yoil_codes[$i] = date('w', strtotime($day));
		}

		$startdate = str_replace("-","",$sdate);

		$sql = /** @lang text */
            "
			select
				a.*
				, s.store_cd
				, s.store_nm
				, ifnull(p.amt,0) as proj_amt
				, sc.store_channel as store_channel
				, sc2.store_kind as store_channel_kind
			from store s 
				left outer join (
					select 
						m.store_cd, sum(if(w.ord_state > 30, o.qty * -1, o.qty)) as qty,
						sum(o.price*o.qty) as ord_amt,
						sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as recv_amt,
						w.ord_state,
						sum(case when o.sale_kind = 81 then o.qty else 0 end) as online,
						sum(case when o.sale_kind <> 81 then o.qty else 0 end) as offline,
						${sum}
					from order_opt_wonga w 
						inner join order_opt o on w.ord_opt_no = o.ord_opt_no 
						inner join order_mst m on m.ord_no = o.ord_no
						inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
						left outer join product_code pc on pc.prd_cd = o.prd_cd
						left outer join code c on c.code_id = o.sale_kind and c.code_kind_cd = 'SALE_KIND'
					where w.ord_state in(30, 60, 61) and w.ord_state_date >= :sdate and w.ord_state_date < :edate and m.store_cd <> '' $where
					group by m.store_cd
				) a on s.store_cd = a.store_cd
                left outer join store_sales_projection p on p.ym = :ym and s.`store_cd` = p.`store_cd`			
                left outer join store_channel sc on sc.store_channel_cd = s.store_channel and dep = 1
				left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind and sc2.dep = 2
            where 1=1 and s.use_yn = 'Y' and (p.amt <> '' or qty is not null) $where2
		";

		$rows = DB::select($sql, ['sdate' => $startdate.'01', 'edate' => $edate, 'ym' => $ym]);

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
