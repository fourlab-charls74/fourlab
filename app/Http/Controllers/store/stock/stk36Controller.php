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

class stk36Controller extends Controller
{
    public function index()
    {
        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'competitors' => SLib::getCodes("COMPETITOR"),
            'sdate' => date("Y-m"),
            'edate' => date("Y-m"),
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
        $edate = $request->input('edate');
        $store_no = $request->input('store_no', '');
        $store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$stores = [];

        $where = "";
        // $orderby = "";
        if ($store_no != "") $where .= " and cs.store_cd like '%" . Lib::quote($store_no) . "%'";
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
     		group by c.store_cd
     	";
		 
		 $rows = DB::select($sql, ['sdate' => $sdate.'-01', 'edate' => $edate.'-31']);
		 
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
				from competitor_sale cs
					inner join code c on c.code_id = cs.competitor_cd and c.code_kind_cd = 'COMPETITOR'
				where c.use_yn = 'Y' and cs.sale_date >= :sdate and cs.sale_date <= :edate and cs.store_cd = '$store_cd'
				group by cs.store_cd, cs.competitor_cd
				order by sum(cs.sale_amt) desc
			 ";

				 $result = DB::select($sql, ['sdate' => $sdate . '-01', 'edate' => $edate . '-31']);

				 $res[] = $result;
			 }

			 

//		foreach ($res as $data) {
////			for ($i = 0; $i < count($res); $i++) {
//				$row = $data[3];
//
//				$store_cd = $row->store_cd;
//				$brand = $store_cd . '_brand';
//				$amt = $store_cd . '_amt';
//
//				$arr[$brand] = $row->{$store_cd. '_brand'};
//				$arr[$amt] = $row->{$store_cd. '_amt'};
////			}
//			
//		}
//		
//		$array[] = $arr;
		
		$arr = [];
		for ($i = 0; $i < count($res); $i++) {
			$row = $res[$i];
			for ($j = 0; $j < count($row); $j++) {
				$store_cd = $row[$j]->store_cd;
				$brand = $store_cd . '_brand';
				$amt = $store_cd . '_amt';

				$arr[$j][] = $row[$j]->{$store_cd. '_brand'};
				$arr[$j][] = $row[$j]->{$store_cd. '_amt'};
			}
		}
		
		dd($arr);
		
		$array[] = $arr;


	
//		
//		$res = [
//			[
//				'G0003_brand' => '노스페이스',
//				'G0003_amt' => 213777140,
//				'H0017_brand' => '노스페이스',
//				'H0017_amt' => 361915520,
//				'H0021_brand' => '노스페이스',
//				'H0021_amt' => 40221700,
//			],
//			[
//				'G0003_brand' => 'K2',
//				'G0003_amt' => 84122600,
//				'H0017_brand' => '아이더',
//				'H0017_amt' => 161545200,
//				'H0021_brand' => '아크테릭스',
//				'H0021_amt' => 38108000,
//			]
//		];

		 
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => 0,
                "page" => $page,
                "stores" => $stores
            ),
            "body" => $array
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
