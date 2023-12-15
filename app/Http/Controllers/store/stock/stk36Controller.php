<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Models\Conf;
use Mockery\Undefined;
use mysql_xdevapi\XSession;

class stk36Controller extends Controller
{
    public function index()
    {
        $values = [
            'competitors' => SLib::getCodes("COMPETITOR"),
            'sdate' => date("Y-m"),
            'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
        ];
        return view(Config::get('shop.store.view') . '/stock/stk36', $values);
    }

    // 검색
    public function search(Request $request)
    {
        $r = $request->all();
        $sdate = $request->input('sdate');
        $store_no = $request->input('store_no', '');
        $store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$stores = [];

        $where = "";
        // $orderby = "";
        if ($store_no != "") $where .= " and s.store_cd ='" . Lib::quote($store_no). "'";
        if ($store_channel != "") $where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

        // ordreby
        // $ord = $r['ord'] ?? 'desc';
        // $ord_field = $r['ord_field'] ?? "s.rt";
        // $orderby = sprintf("order by %s %s ", $ord_field, $ord);

        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $request->input('limit', 500);
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

     	$sql = "
     		select
     			s.store_cd
     			, s.store_nm
     		from store s
     			inner join competitor_sale c on s.store_cd = c.store_cd
     		where 1=1 and c.sale_date >= :sdate and c.sale_date <= :edate 
     		$where
     		group by c.store_cd
     		order by s.store_nm
     	";
		 
		 $rows = DB::select($sql, ['sdate' => $sdate.'-01', 'edate' => $sdate.'-31']);
		 
		 $stores[] = $rows;
		
		 $res = [];
		 foreach ($rows as $row) {
			 $store_cd = $row->store_cd;
			 $competitor_nm = $row->store_cd . '_brand';
			 $competitor_amt = $row->store_cd . '_amt';

			 $sql = "
				select 
					cs.store_cd
					, c.code_val as $competitor_nm
					, sum(cs.sale_amt) as $competitor_amt
					, sum(cs.sale_amt) as sale_amt
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where c.use_yn = 'Y' and cs.sale_date >= :sdate and cs.sale_date <= :edate and cs.store_cd = '$store_cd'
				group by cs.store_cd, cs.competitor_cd
				order by sum(cs.sale_amt) desc, $competitor_nm asc
			 ";

			 $result = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);
			 
			 $sql = "
				select 
					'$store_cd' as store_cd
					, '피엘라벤' as $competitor_nm
					, ifnull(sum(o.recv_amt * if(w.ord_state > 30, -1, 1)),0) as $competitor_amt
					, ifnull(sum(o.recv_amt * if(w.ord_state > 30, -1, 1)),0) as sale_amt
				from order_opt_wonga w 
					inner join order_opt o on w.ord_opt_no = o.ord_opt_no
				where w.ord_state in (30, 60, 61) and o.store_cd = '$store_cd' and o.ord_state = '30'
					and w.ord_state_date >= replace(concat('$sdate','-01'), '-', '')
					and w.ord_state_date <= replace(concat('$sdate','-31'), '-', '') 
			 ";
			 
			 $recv_amt = DB::select($sql);
			  array_push($result,$recv_amt[0]);
			 $res[] = $result;
		 }
		 
		 // 배열을 amt기준으로 내림차순으로 정렬하는 부분
		 for ($i = 0; $i < count($res); $i++) {
			 usort($res[$i], function ($a, $b) {
				 return $b->sale_amt - $a->sale_amt;
			 });
		 }

		$targetBrand = '피엘라벤';

		$rank = -1; // 초기 순위를 -1로 설정
		$rank_data = [];

		for ($i = 0; $i < count($res); $i++) {
			$row = $res[$i];
			for ($j = 0; $j < count($row); $j++) {
				if ($row[$j]->{$row[$j]->store_cd . '_brand'} == $targetBrand) {
					$cnt = count($row);
					$rank = $j + 1;
					$rank_data[$row[$j]->store_cd] = $rank.'/'.$cnt;
					break;
				}
			}
		}
		
		$arr = [];
		for ($i = 0; $i < count($res); $i++) {
			$row = $res[$i];
			for ($j = 0; $j < count($row); $j++) {
				$store_cd = $row[$j]->store_cd;
				$brand = $store_cd . '_brand';
				$amt = $store_cd . '_amt';

				$arr[$j][$amt] = $row[$j]->{$store_cd. '_amt'};
				$arr[$j][$brand] = $row[$j]->{$store_cd. '_brand'};
			}
		}
		
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => 0,
                "page" => $page,
                "stores" => $stores,
				"rank_data" => $rank_data,
            ),
            "body" => $arr
        ]);
    }
	
	public function total_search(Request $request)
	{
		$sdate = $request->input('sdate');
		$store_no = $request->input('store_no', '');
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		
		$where = "";
		if ($store_no != "") $where .= " and s.store_cd ='" . Lib::quote($store_no). "'";
		if ($store_channel != "") $where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		//피엘라벤의 매장의 개수를 구하는 부분
		$sql = "
     		select
     			s.store_cd
     			, s.store_nm
     		from store s
     			inner join competitor_sale c on s.store_cd = c.store_cd
     		where 1=1 and c.sale_date >= :sdate and c.sale_date <= :edate
     		$where
     		group by c.store_cd
     	";

		$store_rows = DB::select($sql, ['sdate' => $sdate.'-01', 'edate' => $sdate.'-31']);
		$store_cnt = count($store_rows);

		$stores[] = $store_rows;

		// 아크테릭스와 파타고니아의 매장의 개수를 구하는 부분
		$res = [];
		$res2 = [];
		$res3 = [];
		foreach ($store_rows as $row) {
			$store_cd = $row->store_cd;
			$competitor_nm = $row->store_cd . '_brand';
			$competitor_amt = $row->store_cd . '_amt';

			$sql = "
				select 
					cs.store_cd
					, c.code_val as $competitor_nm
					, sum(cs.sale_amt) as $competitor_amt
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where c.use_yn = 'Y' and cs.sale_date >= :sdate and cs.sale_date <= :edate and cs.store_cd = '$store_cd' and c.code_val = '아크테릭스'
				group by cs.store_cd, cs.competitor_cd
				order by sum(cs.sale_amt) desc, $competitor_nm asc
			 ";

			$result = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);
			
			$res[] = $result;

			$sql = "
				select 
					cs.store_cd
					, c.code_val as $competitor_nm
					, sum(cs.sale_amt) as $competitor_amt
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where c.use_yn = 'Y' and cs.sale_date >= :sdate and cs.sale_date <= :edate and cs.store_cd = '$store_cd' and c.code_val = '파타고니아'
				group by cs.store_cd, cs.competitor_cd
				order by sum(cs.sale_amt) desc, $competitor_nm asc
			 ";

			$result2 = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);
			$res2[] = $result2;
			
		}
		function countNoEmptyArray($array) {
			return count(array_filter($array, function ($innerArray) {
				return !empty($innerArray);
			}));
		}
		$arcteryx_cnt = countNoEmptyArray($res);
		$patagonia_cnt = countNoEmptyArray($res2);
		
		// 전체 매출 데이터를 구하는 부분
		$res3 = [];
		$res4 = [];
		foreach ($store_rows as $row) {
			$store_cd = $row->store_cd;
			$competitor_nm = $row->store_cd . '_brand';
			$competitor_amt = $row->store_cd . '_amt';

			$sql = "
				select 
					cs.store_cd
					, c.code_val as $competitor_nm
					, sum(cs.sale_amt) as $competitor_amt
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where c.use_yn = 'Y' and cs.sale_date >= :sdate and cs.sale_date <= :edate and cs.store_cd = '$store_cd' and c.code_val = '아크테릭스'
				group by cs.store_cd, cs.competitor_cd
				order by sum(cs.sale_amt) desc, $competitor_nm asc
			 ";
			$result = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);
			$res3[] = $result;

			$sql = "
				select 
					cs.store_cd
					, c.code_val as $competitor_nm
					, sum(cs.sale_amt) as $competitor_amt
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where c.use_yn = 'Y' and cs.sale_date >= :sdate and cs.sale_date <= :edate and cs.store_cd = '$store_cd' and c.code_val = '파타고니아'
				group by cs.store_cd, cs.competitor_cd
				order by sum(cs.sale_amt) desc, $competitor_nm asc
			 ";
			$result = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);
			$res4[] = $result;
		}

		$arr = [];
		for ($i = 0; $i < count($res3); $i++) {
			$row = $res3[$i];
			for ($j = 0; $j < count($row); $j++) {
				$store_cd = $row[$j]->store_cd;
				$amt = $store_cd . '_amt';

				$arr[$j][$amt] = (int)$row[$j]->{$store_cd. '_amt'};
			}
		}

		$arr2 = [];
		for ($i = 0; $i < count($res4); $i++) {
			$row = $res4[$i];
			for ($j = 0; $j < count($row); $j++) {
				$store_cd = $row[$j]->store_cd;
				$amt = $store_cd . '_amt';

				$arr2[$j][$amt] = (int)$row[$j]->{$store_cd. '_amt'};
			}
		}
		
		if (!empty($arr)) {
			$arcteryx_total_amt = array_sum($arr[0]);
		} else {
			$arcteryx_total_amt = 0;
		}
		
		if(!empty($arr2)) {
			$patagonia_total_amt = array_sum($arr2[0]);
		} else {
			$patagonia_total_amt = 0;
		}

		//아크테릭스 최저매출매장
		$arcteryx_flatArr = call_user_func_array('array_merge', $arr);
		if (!empty($arcteryx_flatArr)) {
			$arcteryx_worst_amt = min($arcteryx_flatArr);
			$arcteryx_min_key = array_search($arcteryx_worst_amt, $arcteryx_flatArr);
			$worst_store_cd = str_replace("_amt", "", $arcteryx_min_key);
			$worst_store_nm = DB::table('store')->select( 'store_nm')->where('store_cd', $worst_store_cd)->first();
			$worst_store_nm = $worst_store_nm->store_nm;
		} else {
			$arcteryx_worst_amt = 0;
			$worst_store_nm = "매장없음";
		}

		//파타고니아 최저매출매장
		$patagonia_flatArr = call_user_func_array('array_merge', $arr2);
		if (!empty($patagonia_flatArr)) {
			$patagonia_worst_amt = min($patagonia_flatArr);
			$patagonia_min_key = array_search($patagonia_worst_amt, $patagonia_flatArr);
			$worst_store_cd2 = str_replace("_amt", "", $patagonia_min_key);
			$worst_store_nm2 = DB::table('store')->select( 'store_nm')->where('store_cd', $worst_store_cd2)->first();
			$worst_store_nm2 = $worst_store_nm2->store_nm;
		} else {
			$patagonia_worst_amt = 0;
			$worst_store_nm2 = "매장없음";
		}

		//아크테릭스 최고매출매장
		if (!empty($arcteryx_flatArr)) {
			$arcteryx_best_amt = max($arcteryx_flatArr);
			$arcteryx_best_key = array_search($arcteryx_best_amt, $arcteryx_flatArr);
			$best_store_cd = str_replace("_amt", "", $arcteryx_best_key);
			$best_store_nm = DB::table('store')->select( 'store_nm')->where('store_cd', $best_store_cd)->first();
			$best_store_nm = $best_store_nm->store_nm;
		} else {
			$arcteryx_best_amt = 0;
			$best_store_nm = "매장없음";
		}

		//파타고니아 최고매출매장
		if (!empty($patagonia_flatArr)) {
			$patagonia_best_amt = max($patagonia_flatArr);
			$patagonia_best_key = array_search($patagonia_best_amt, $patagonia_flatArr);
			$best_store_cd2 = str_replace("_amt", "", $patagonia_best_key);
			$best_store_nm2 = DB::table('store')->select( 'store_nm')->where('store_cd', $best_store_cd2)->first();
			$best_store_nm2 = $best_store_nm2->store_nm;
		} else {
			$patagonia_best_amt = 0;
			$best_store_nm2 = "매장없음";
		}
		
		// 피엘라벤의 전체매출데이터 부분
		$store_cds = array_map(function ($store_rows) {
			return "'".$store_rows->store_cd."'";
		}, $store_rows);

		if (!empty($store_cds)) {
			$storecds = implode(', ', $store_cds);
		} else {
			$storecds = "''";
		}
		
		$sql = "
			select
				 ifnull(sum(o.recv_amt * if(w.ord_state > 30, -1, 1)),0) as total_amt
			from order_opt_wonga w
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst om on o.ord_no = om.ord_no
				inner join goods g on o.goods_no = g.goods_no
			where 
				w.ord_state in (30,60,61) and o.ord_state = '30' and o.store_cd in ($storecds)
				and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
				and w.ord_state_date >= replace(:sdate, '-', '')
				and w.ord_state_date <= replace(:edate, '-', '') 
		";
		$total_amt = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);
		$fjallraven_total_amt = $total_amt[0]->total_amt??0;


		$sql = "
			select
			    o.store_cd,
				 ifnull(sum(o.recv_amt * if(w.ord_state > 30, -1, 1)),0) as total_amt
			from order_opt_wonga w
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst om on o.ord_no = om.ord_no
				inner join goods g on o.goods_no = g.goods_no
			where 
				w.ord_state in (30,60,61) and o.ord_state = '30' and o.store_cd in ($storecds)
				and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
				and w.ord_state_date >= replace(:sdate, '-', '')
				and w.ord_state_date <= replace(:edate, '-', '') 
			group by o.store_cd
		";

		$fjallraven_best_worst_amt = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $sdate . '-31']);

		$min_total_amt = PHP_INT_MAX;
		$min_total_store_cd = null;
		$max_total_amt = 0;
		$max_total_store_cd = null;
		
		if (!empty($fjallraven_best_worst_amt)) {
			foreach ($fjallraven_best_worst_amt as $result) {
				$store_cd = $result->store_cd;
				$total_amt = $result->total_amt;

				if ($total_amt < $min_total_amt) {
					$min_total_amt = $total_amt;
					$min_total_store_cd = $store_cd;
				}

				if ($total_amt > $max_total_amt) {
					$max_total_amt = $total_amt;
					$max_total_store_cd = $store_cd;
				}
			}
		}
		
		if ($min_total_store_cd === null) {
			$min_total_amt = 0;
			if (!empty($store_rows)) {
				$min_total_store_cd = $store_rows[0]->store_cd;
			} else {
				$min_total_store_cd = "";
			}
		}

		if ($max_total_store_cd === null) {
			$max_total_amt = 0;
			if (!empty($store_rows)) {
				$max_total_store_cd = $store_rows[0]->store_cd;
			} else {
				$max_total_store_cd = "";
			}
		}
		
		if ($min_total_store_cd == "") {
			$worst_store_nm3 = "매장없음";
		} else {
			$worst_store_nm3 = DB::table('store')->select( 'store_nm')->where('store_cd', $min_total_store_cd)->first();
			$worst_store_nm3 = $worst_store_nm3->store_nm;
		}
		
		if ($max_total_store_cd == "") {
			$best_store_nm3 = "매장없음";
		} else {
			$best_store_nm3 = DB::table('store')->select( 'store_nm')->where('store_cd', $max_total_store_cd)->first();
			$best_store_nm3 = $best_store_nm3->store_nm;
		}
	
		
		// 종합 데이터 출력부분
		$sql = "
			select
			    brand,
			    store_cnt,
			    total_amt,
			    worst_amt_store,
			    worst_amt,
			    best_amt_store,
			    best_amt
			from (
				select
					c.code_val as brand
					, if(c.code_val = '아크테릭스', '$arcteryx_cnt', if(c.code_val = '파타고니아', '$patagonia_cnt' ,0)) as store_cnt
					, if(c.code_val = '아크테릭스', '$arcteryx_total_amt', if(c.code_val = '파타고니아', '$patagonia_total_amt' ,0)) as total_amt
					, if(c.code_val = '아크테릭스', '$worst_store_nm', if(c.code_val = '파타고니아', '$worst_store_nm2' ,0)) as worst_amt_store
					, if(c.code_val = '아크테릭스', '$arcteryx_worst_amt', if(c.code_val = '파타고니아', '$patagonia_worst_amt' ,0)) as worst_amt
					, if(c.code_val = '아크테릭스', '$best_store_nm', if(c.code_val = '파타고니아', '$best_store_nm2' ,0)) as best_amt_store
					, if(c.code_val = '아크테릭스', '$arcteryx_best_amt', if(c.code_val = '파타고니아', '$patagonia_best_amt' ,0)) as best_amt
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where 1=1 and c.code_val in ('아크테릭스', '파타고니아') and cs.sale_date >= :sdate and cs.sale_date <= :edate
				group by c.code_val
				
				union 
				select
					'피엘라벤' as brand
					, ifnull('$store_cnt', 0) as store_cnt
					, ifnull('$fjallraven_total_amt', 0) as total_amt
					, ifnull('$worst_store_nm3', '롯데명동본점') as worst_amt_store
					, ifnull('$min_total_amt', 0) as worst_amt
					, ifnull('$best_store_nm3', '롯데명동본점') as best_amt_store
					, ifnull('$max_total_amt', 0) as best_amt
					
			) a
			order by case brand when '피엘라벤' then 1 when '아크테릭스' then 2 when '파타고니아' then 3 else 4 end
		";
		
		$row = DB::select($sql, ['sdate' => $sdate.'-01', 'edate' => $sdate.'-31']);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => 0,
			),
			"body" => $row
		]);
		
	}

    public function create()
    {
        $mutable = Carbon::now();
        $date = $mutable->now()->format('Y-m');

        $values = [
            'date' => $date,
        ];

        return view(Config::get('shop.store.view') . '/stock/stk36_show', $values);
    }


    public function com_search(Request $request)
    {
        $store_no = $request->input('store_no', '');
        $date = $request->input('date');
        $day = (int)$request->input('day');

        $where = "";
        $sale_amt = "";
		$null_sale_amt = "";
		$sum_sale_amt = "";

        if($store_no != '') $where .= "and cs.store_cd = '$store_no'";
        if($date != '') $where .= "and cs.sale_date like '$date%'";

        for ($i = 1; $i<=$day; $i++) {
            if ($i < 10) {
                $sale_amt .= ",sum(if(right(cs.sale_date,2) = '0$i',cs.sale_amt,0)) as sale_amt_0$i ";
				$null_sale_amt .= ", '0' as sale_amt_0$i";
				$sum_sale_amt .= ", sum(sale_amt_0$i) as sale_amt_0$i";
            } else {
                $sale_amt .= ",sum(if(right(cs.sale_date,2) = '$i',cs.sale_amt,0)) as sale_amt_$i ";
				$null_sale_amt .= ", '0' as sale_amt_$i";
				$sum_sale_amt .= ", sum(sale_amt_$i) as sale_amt_$i";
            }
        }

        $sql = "
            select 
                store_cd
                , competitor_cd
                , sale_date
                , sale_amt
            from competitor_sale
            where sale_date like '$date%' and store_cd = '$store_no' and sale_amt > 0
        ";

        $result = DB::select($sql);
       
        if(count($result) > 0 ) {
            $sql = "
                select
					competitor_cd,
					max(store_cd) as store_cd,
					max(competitor_nm) as competitor_nm,
					max(sale_memo) as sale_memo
					$sum_sale_amt
				from (
					select
						distinct(c.code_id) as competitor_cd,
						cs.store_cd,
						c.code_val as competitor_nm,
						cs.sale_memo
						$sale_amt
					from competitor_sale cs
						left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor' and c.use_yn = 'Y'
					where cs.store_cd = '$store_no' and cs.sale_date >= '$date-01' and cs.sale_date <= '$date-30'
					group by cs.competitor_cd
				
					union
					
					select
						distinct(cd.code_id) as competitor_cd,
						'$store_no' as store_cd,
						cd.code_val as competitor_nm,
						null as sale_memo
						$null_sale_amt
					from code cd
						left outer join competitor com on cd.code_id = com.competitor_cd
					where cd.code_kind_cd = 'COMPETITOR' and cd.use_yn = 'Y' and com.use_yn = 'Y' and com.store_cd = '$store_no'
				) a
				group by competitor_cd;
            ";
        } else {
            $sql = "
                select 
                    cd.code_id as competitor_cd
                    , cd.code_val as competitor_nm
                    , com.store_cd
                from code cd
                    left outer join competitor com on cd.code_id = com.competitor_cd
                where cd.code_kind_cd = 'COMPETITOR' and cd.use_yn = 'Y' and com.use_yn = 'Y' and com.store_cd = '$store_no'
                
            ";
        }
        
        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
                "day" => $day
            ),
            "body" => $result
        ]);

    }

    public function save_amt(Request $request)
    {
        $admin_id = Auth('head')->user()->id;
        $data = $request->input('data');
        $date = $request->input('date');

        try {
            DB::beginTransaction();

            $day_arr = ['01','02','03','04','05','06','07','08','09','10'
                        ,'11','12','13','14','15','16','17','18','19','20'
                        ,'21','22','23','24','25','26','27','28','29','30','31'];

            $upsert_array = [];
            foreach($data as $rows) {
                foreach($day_arr as $day_value) {
                    $key = $rows['competitor_cd'].$date.$day_value;
                    $upsert_array[$key]['store_cd']      = $rows['store_cd'];
                    $upsert_array[$key]['sale_memo']      = $rows['sale_memo']??'';
                    $upsert_array[$key]['competitor_cd'] = $rows['competitor_cd'];
                    $upsert_array[$key]['sale_date']     = $date . '-' .$day_value;
                    $upsert_array[$key]['admin_id']      = $admin_id;
                    $upsert_array[$key]['rt']            = date("Y-m-d H:i:s");
                    $upsert_array[$key]['ut']            = date("Y-m-d H:i:s");
                    $upsert_array[$key]['sale_amt']      = isset($rows['sale_amt_'.$day_value]) ? $rows['sale_amt_'.$day_value] : 0;
                }
            }

            DB::table('competitor_sale')->upsert(
                $upsert_array,
                ['store_cd', 'competitor_cd', 'sale_date'],
                ['sale_amt','sale_memo', 'rt', 'ut']
            );

            DB::commit();
            $code = 200;
            $msg = "매출액이 저장되었습니다.";
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
