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
	public function index() 
	{
		$sdate = Carbon::now()->startOfMonth()->format("Y-m"); // 이번 달 기준
		//$sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 1달전 기준 (테스트 용)

		$values = [
            'sdate'         => $sdate,
			'sell_types'	=> SLib::getCodes('SALE_KIND'),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
        return view( Config::get('shop.store.view') . '/sale/sal02',$values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->format("Ym"));
		$list_type = $request->input('list_type', "qty");
		$store_cd = $request->input('store_cd', "");
		$goods_no = $request->input('goods_no', "");
		$goods_nm = $request->input('goods_nm', "");
		$brand_cd = $request->input('brand_cd', "");
		$style_no = $request->input('style_no', "");
		$prd_cd = $request->input('prd_cd', "");
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		
		// 판매 유형은 추후 반영 예정
		$sell_type = $request->input('sell_type', '');
		$store_channel	= $request->input("store_channel", '');
		$store_channel_kind	= $request->input("store_channel_kind", '');

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

		if ($sell_type != "") $where .= " and o.sale_kind = '$sell_type'";

        $where2 = "";
		if ($store_cd != "") $where2 .= " and s.store_cd = '" . Lib::quote($store_cd)."'";
		if ($store_channel != "") $where2 .= " and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where2 .= " and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		// 상품코드
		if ($prd_cd != '') {
			$prd_cd = preg_replace("/\s/", ",", $prd_cd);
			$prd_cd = preg_replace("/\t/", ",", $prd_cd);
			$prd_cd = preg_replace("/\n/", ",", $prd_cd);
			$prd_cd = preg_replace("/,,/", ",", $prd_cd);
			$prd_cds = explode(',', $prd_cd);
			if (count($prd_cds) > 1) {
				$prd_cds_str = "";
				if (count($prd_cds) > 500) array_splice($prd_cds, 500);
				for($i =0; $i < count($prd_cds); $i++) {
					$prd_cds_str.= "'".$prd_cds[$i]."'";
					
					if($i !== count($prd_cds) -1) {
						$prd_cds_str .= ",";
					}
				}
				$where .= " and o.prd_cd in ($prd_cds_str) ";
			} else {
				$where .= " and o.prd_cd like '" . Lib::quote($prd_cd) . "%' ";
			}
		}

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}
		
		// 전달 받은 리스트 타입에 따라 합계 쿼리 구분
		$sum = "";
		$sum_true = "";
		$sum_false = 0;
		$total_sum = "";
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
			$sum .= "sum(if(day(w.ord_state_date) = ${day} ,if(w.ord_state > 30, ${sum_true} * -1, ${sum_true}),0)) as ${day}_val${comma}";
			$total_sum .= ", ifnull(sum(t.${day}_val),0) as ${day}_val";

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
					  o.store_cd, sum(if(w.ord_state > 30, o.qty * -1, o.qty)) as qty,
					  sum(o.price*o.qty) as ord_amt,
					  sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as recv_amt,
					  w.ord_state,
					  if (s.online_only_yn = 'Y', sum(case when o.sale_kind <> 81 or o.sale_kind is null then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end), sum(case when o.sale_kind = 81 then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end)) as online,
    				  if (s.online_only_yn = 'Y', sum(case when o.sale_kind = 81 then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end), sum(case when o.sale_kind <> 81 or o.sale_kind is null then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end))  as offline,
					  ${sum}
				   from order_opt_wonga w
					  inner join order_opt o on w.ord_opt_no = o.ord_opt_no and o.ord_state = '30'
					  inner join store s on s.store_cd = o.store_cd
					  inner join order_mst m on m.ord_no = o.ord_no
					  inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
					  left outer join product_code pc on pc.prd_cd = o.prd_cd
					  left outer join code c on c.code_id = o.sale_kind and c.code_kind_cd = 'SALE_KIND'
				   where 
						w.ord_state in(30, 60, 61) 
						and w.ord_state_date >= :sdate 
						and w.ord_state_date < :edate 
						and o.store_cd <> '' 
						and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
						$where
				   group by o.store_cd
				) a on s.store_cd = a.store_cd
				left outer join store_sales_projection p on p.ym = :ym and s.`store_cd` = p.`store_cd`
				left outer join store_channel sc on sc.store_channel_cd = s.store_channel and dep = 1
				left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind and sc2.dep = 2
			where 1=1 and s.use_yn = 'Y' and (p.amt <> '' or qty is not null) $where2
		";
			
		$rows = DB::select($sql, ['sdate' => $startdate.'01', 'edate' => $edate, 'ym' => $ym]);

		$sql = "

			select
				   count(t.store_nm) as total
				   , sum(t.proj_amt) as proj_amt
				   , sum(t.recv_amt) as recv_amt
				   , round(sum(t.recv_amt) / sum(t.proj_amt) * 100, 0) as progress_proj_amt
				   , sum(t.qty) as qty
				   , sum(t.ord_amt) as ord_amt
				   , sum(t.online) as online
				   , sum(t.offline) as offline
				   , concat(round((sum(t.offline) / sum(t.recv_amt)) * 100,0), ' : ', round((sum(t.online) / sum(t.recv_amt)) * 100,0)) as offline_online_rate
				   ${total_sum}
			from (
				select
					s.store_nm
					, a.*
					, ifnull(p.amt,0) as proj_amt
					, sc.store_channel as store_channel
					, sc2.store_kind as store_channel_kind
				from store s
				   left outer join (
					  select
					     o.store_cd,
						 sum(if(w.ord_state > 30, o.qty * -1, o.qty)) as qty, 
						 sum(o.price*o.qty) as ord_amt,
						 sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as recv_amt,  
						 if (s.online_only_yn = 'Y', sum(case when o.sale_kind <> 81 or o.sale_kind is null then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end), sum(case when o.sale_kind = 81 then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end)) as online,
						 if (s.online_only_yn = 'Y', sum(case when o.sale_kind = 81 then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end), sum(case when o.sale_kind <> 81 or o.sale_kind is null then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end))  as offline,
						 ${sum}
					  from order_opt_wonga w
						 inner join order_opt o on w.ord_opt_no = o.ord_opt_no and o.ord_state  ='30'
					     inner join store s on s.store_cd = o.store_cd
						 inner join order_mst m on m.ord_no = o.ord_no
						 inner join goods g on o.goods_no = g.goods_no
						 left outer join brand b on g.brand = b.brand
						 left outer join product_code pc on pc.prd_cd = o.prd_cd
					  	 left outer join code c on c.code_id = o.sale_kind and c.code_kind_cd = 'SALE_KIND'
					  where 
							w.ord_state in(30, 60, 61) 
					    	and w.ord_state_date >= :sdate 
					    	and w.ord_state_date < :edate 
					    	and o.store_cd <> ''
					    	and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1) 
					    	$where
					  group by o.store_cd
				   ) a on s.store_cd = a.store_cd
				   left outer join store_sales_projection p on p.ym = :ym and s.`store_cd` = p.`store_cd`
				   left outer join store_channel sc on sc.store_channel_cd = s.store_channel and dep = 1
				   left outer join store_channel sc2 on sc2.store_kind_cd = s.store_channel_kind and sc2.dep = 2
				where 1=1 and s.use_yn = 'Y' and (p.amt <> '' or qty is not null) $where2
			) t
			
		";
		
		$res = DB::selectOne($sql, ['sdate' => $startdate.'01', 'edate' => $edate, 'ym' => $ym]);

		$total_data = $res;
		
		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows),
				"yoil_codes" => $yoil_codes,
				"total_data" => $total_data
            ),
            "body" => $rows
        ]);
	}
}
