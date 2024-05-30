<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use DateTime;

use App\Models\Conf;

class stk33Controller extends Controller
{
    public function index(Request $request)
    {
        $store_cd = Auth::guard('head')->user()->store_cd;
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'week')->format('Y-m-d');
        $edate = date("Y-m-d");
        $req_date = $request->query('date');
        $lastDay = DATE('t', strtotime($req_date));

        if ($req_date != '') {
            $sdate = $req_date.'-01';
            $edate = $req_date.'-'.$lastDay;
        }

        $sql = "
            select 
                competitor_yn
            from store
            where store_cd = '$store_cd' 
        ";

        $competitor_yn = DB::selectOne($sql);

        $sql = "
            select 
                c.code_id
                ,c.code_val
            from code c
                left outer join competitor com on com.competitor_cd = c.code_id and com.store_cd = :store_cd
            where c.code_kind_cd = 'COMPETITOR' and c.use_yn = 'Y' and com.use_yn = 'Y'
        ";

        $competitors = DB::select($sql, [ 'store_cd' => $store_cd ]);

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'competitors' => $competitors,
            'sdate' => $sdate,
            'edate' => $edate,
            'store_cd' => $store_cd,
            'competitor_yn' => $competitor_yn->competitor_yn
        ];
        return view(Config::get('shop.shop.view') . '/stock/stk33', $values);
    }

    // 검색
    public function search(Request $request)
    {
        $r = $request->all();
        $sdate = $request->input('sdate');
        $edate = $request->input('edate', date("Y-m-d"));
        $store_no = $request->input('store_no', '');

        $where = "";
        $orderby = "";
        if ($store_no != "") $where .= " and cs.store_cd like '%" . Lib::quote($store_no) . "%'";

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "s.rt";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $request->input('limit', 500);
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sql = "
            select code_id from code where code_kind_cd = 'competitor'
        ";

        $code_ids = array_map(function($row) {return $row->code_id;}, DB::select($sql));

        $com = "";
        $t_amt = "";

        foreach($code_ids as $code_id) {
            $com .= "
            	, ifnull(sum(case when cs.competitor_cd = '$code_id' then cs.sale_amt_off end), 0) as 'amt_off_$code_id'
            	, ifnull(sum(case when cs.competitor_cd = '$code_id' then cs.sale_amt_on end), 0) as 'amt_on_$code_id'
            	, ifnull(sum(case when cs.competitor_cd = '$code_id' then cs.sale_amt end), 0) as 'amt_$code_id'
            ";
            $t_amt .= "
            	, sum(a.amt_off_$code_id) as amt_off_$code_id
            	, sum(a.amt_on_$code_id) as amt_on_$code_id
            	, sum(a.amt_$code_id) as amt_$code_id
            ";
        }

        $sql = "
            select 
                s.store_nm
                , cs.sale_date
                , sum(cs.sale_amt) as total_amt
                , cs.store_cd
                , cs.competitor_cd
                , s.store_type
                $com
            from competitor_sale cs
                inner join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                inner join store s on s.store_cd = cs.store_cd
            where 1=1 and sale_date >= '$sdate' and sale_date <= '$edate'
            $where
            group by cs.sale_date, cs.store_cd
            $orderby
            $limit
        ";
        
        $rows = DB::select($sql);
        
        // pagination
        $total = 0;
        $total_data = '';
        $page_cnt = 0;
        if($page == 1) {
            $query =
                "
                select
                    count(a.store_nm) as total,
                    sum(a.total_amt) as total_amt
                    $t_amt
                from (
                    select 
                        s.store_nm
                        , cs.sale_date
                        , sum(cs.sale_amt) as total_amt
                        , cs.store_cd
                        , cs.competitor_cd
                        , s.store_type
                        $com
                    from competitor_sale cs
                        inner join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                        inner join store s on s.store_cd = cs.store_cd
                    where 1=1 and sale_date >= '$sdate' and sale_date <= '$edate'
                    $where
                    group by cs.sale_date, cs.store_cd
                ) a
            ";
        }

        $row = DB::selectOne($query);
        $total_data = $row;
        $total = $row->total;
        $page_cnt = (int)(($total - 1) / $page_size) + 1;
            
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows),
                "total_data" => $total_data
            ),
            "body" => $rows
        ]);
    }

    public function create()
    {
        $store_cd = Auth::guard('head')->user()->store_cd;
        $mutable = Carbon::now();
        $date = $mutable->now()->format('Y-m');

        $values = [
            'date' => $date,
            'store_cd' => $store_cd
        ];

        return view(Config::get('shop.shop.view') . '/stock/stk33_show', $values);
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
				//$sale_amt .= ",sum(if(right(cs.sale_date,2) = '0$i',cs.sale_amt,0)) as sale_amt_0$i ";
				//$null_sale_amt .= ", '0' as sale_amt_0$i";
				//$sum_sale_amt .= ", sum(sale_amt_0$i) as sale_amt_0$i";
				$sale_amt		.= " ,sum(if(right(cs.sale_date,2) = '0$i',cs.sale_amt_off,0)) as sale_amt_off_0$i ,sum(if(right(cs.sale_date,2) = '0$i',cs.sale_amt_on,0)) as sale_amt_on_0$i ,sum(if(right(cs.sale_date,2) = '0$i',cs.sale_amt,0)) as sale_amt_0$i ";
				$null_sale_amt	.= " , '0' as sale_amt_off_0$i , '0' as sale_amt_on_0$i , '0' as sale_amt_0$i ";
				$sum_sale_amt	.= " , sum(sale_amt_off_0$i) as sale_amt_off_0$i , sum(sale_amt_on_0$i) as sale_amt_on_0$i , sum(sale_amt_0$i) as sale_amt_0$i ";
            } else {
				//$sale_amt .= ",sum(if(right(cs.sale_date,2) = '$i',cs.sale_amt,0)) as sale_amt_$i ";
				//$null_sale_amt .= ", '0' as sale_amt_$i";
				//$sum_sale_amt .= ", sum(sale_amt_$i) as sale_amt_$i";
				$sale_amt		.= " ,sum(if(right(cs.sale_date,2) = '$i',cs.sale_amt_off,0)) as sale_amt_off_$i ,sum(if(right(cs.sale_date,2) = '$i',cs.sale_amt_on,0)) as sale_amt_on_$i ,sum(if(right(cs.sale_date,2) = '$i',cs.sale_amt,0)) as sale_amt_$i ";
				$null_sale_amt	.= " , '0' as sale_amt_off_$i , '0' as sale_amt_on_$i , '0' as sale_amt_$i ";
				$sum_sale_amt	.= " , sum(sale_amt_off_$i) as sale_amt_off_$i , sum(sale_amt_on_$i) as sale_amt_on_$i , sum(sale_amt_$i) as sale_amt_$i ";
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
					store_cd as store_cd,
					competitor_nm as competitor_nm,
					sale_memo as sale_memo
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
					where cs.store_cd = '$store_no' and cs.sale_date >= '$date-01' and cs.sale_date <= '$date-31'
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
					$key	= $rows['competitor_cd'].$date.$day_value;
					$upsert_array[$key]['store_cd']			= $rows['store_cd'];
					$upsert_array[$key]['sale_memo']		= $rows['sale_memo']??'';
					$upsert_array[$key]['competitor_cd']	= $rows['competitor_cd'];
					$upsert_array[$key]['sale_date']		= $date . '-' .$day_value;
					$upsert_array[$key]['admin_id']			= $admin_id;
					$upsert_array[$key]['rt']				= date("Y-m-d H:i:s");
					$upsert_array[$key]['ut']				= date("Y-m-d H:i:s");
					$upsert_array[$key]['sale_amt_off']		= isset($rows['sale_amt_off_'.$day_value]) ? $rows['sale_amt_off_'.$day_value] : 0;
					$upsert_array[$key]['sale_amt_on']		= isset($rows['sale_amt_on_'.$day_value]) ? $rows['sale_amt_on_'.$day_value] : 0;
					$upsert_array[$key]['sale_amt']			= $upsert_array[$key]['sale_amt_off'] + $upsert_array[$key]['sale_amt_on'];
                }
            }

            DB::table('competitor_sale')->upsert(
                $upsert_array,
                ['store_cd', 'competitor_cd', 'sale_date'],
                ['sale_amt', 'sale_amt_off', 'sale_amt_on', 'sale_memo', 'rt', 'ut']
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
