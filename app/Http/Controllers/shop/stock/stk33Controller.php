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
                left outer join competitor com on com.competitor_cd = c.code_id and com.store_cd = 'L0025'
            where c.code_kind_cd = 'COMPETITOR' and c.use_yn = 'Y' and com.use_yn = 'Y'
        ";

        $competitors = DB::select($sql);

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

        // $sdate = $request->input('sdate', Carbon::now()->sub(3, 'month'));
        // $edate = $request->input('edate', date("Ymd"));
        $sdate = $request->input('sdate');
        $store_no = $request->input('store_no', '');
        $store_nm = $request->input('store_nm', '');
        $store_type    = $request->input("store_type", '');


        $where = "";
        $orderby = "";
        if ($store_no != "") $where .= " and cs.store_cd like '%" . Lib::quote($store_no) . "%'";
        if ($store_type != "") $where .= " and s.store_type = '$store_type'";

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "s.rt";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        // $page = $r['page'] ?? 1;
        // if ($page < 1 or $page == "") $page = 1;
        // $page_size = $r['limit'] ?? 100;
        // $startno = ($page - 1) * $page_size;
        // $limit = " limit $startno, $page_size ";

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
            // $com .= ", ifnull((select sale_amt from competitor_sale where competitor_cd = '$code_id'), 0 ) as 'amt_$code_id'";
            $com .= ", ifnull(sum(case when cs.competitor_cd = '$code_id' then cs.sale_amt end), 0) as 'amt_$code_id'";
            $t_amt .= ", sum(a.amt_$code_id) as amt_$code_id";
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
                where 1=1 and sale_date like '$sdate%'
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
                        where 1=1 and sale_date like '$sdate%'
                        $where
                        group by cs.sale_date, cs.store_cd
                        $orderby
                        $limit
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

        if($store_no != '') $where .= "and cs.store_cd = '$store_no'";
        if($date != '') $where .= "and cs.sale_date like '$date%'";

        for ($i = 1; $i<=$day; $i++) {
            if ($i < 10) {
                $sale_amt .= ",sum(if(right(cs.sale_date,2) = '0$i',cs.sale_amt,0)) as sale_amt_0$i ";
            } else {
                $sale_amt .= ",sum(if(right(cs.sale_date,2) = '$i',cs.sale_amt,0)) as sale_amt_$i ";
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
                    c.code_id as competitor_cd
                    , cs.store_cd
                    , c.code_val as competitor_nm
                    $sale_amt
                from competitor_sale cs
                    left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor' and c.use_yn = 'Y'
                where cs.store_cd = '$store_no' and cs.sale_date >= '$date-01' and cs.sale_date <= '$date-31'
                group by cs.competitor_cd
                
            ";

            // $sql = "
            // select cs.*
            // from competitor c
            //     inner join (
            //         select csa.store_cd, csa.competitor_cd, cd.code_val as competitor_nm
            //             , sum(if(sale_date = '2023-02-01', sale_amt, 0)) as sale_amt_01
            //             , sum(if(sale_date = '2023-02-02', sale_amt, 0)) as sale_amt_02
            //             , sum(if(sale_date = '2023-02-03', sale_amt, 0)) as sale_amt_03
            //         from competitor_sale csa
            //             inner join store s on s.store_cd = csa.store_cd
            //             inner join code cd on cd.code_id = csa.competitor_cd and cd.code_kind_cd = 'COMPETITOR'
            //         where csa.store_cd = 'H0021' and csa.sale_date >= '2023-02-01' and csa.sale_date <= '2023-02-31'
            //         group by csa.store_cd, csa.competitor_cd
            //     ) cs on cs.store_cd = c.store_cd and cs.competitor_cd = c.competitor_cd
            // where c.use_yn = 'Y'
            // ";

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
        $day = $request->input('day');


        try {
            DB::beginTransaction();

            $day_arr = [ '00','01','02','03','04','05','06','07','08','09','10'
                        ,'11','12','13','14','15','16','17','18','19','20'
                        ,'21','22','23','24','25','26','27','28','29','30','31'];


            foreach($data as $rows) {
                $store_cd = $rows['store_cd'];
                $competitor_cd = $rows['competitor_cd'];

                $size = sizeof($rows);

                // if ($size > 3) {
                    for ($i = 1; $i <= $day; $i++) {

                        $where	= [
                            'store_cd' => $store_cd, 
                            'competitor_cd' => $competitor_cd,
                            'sale_date' => $date.'-'.$day_arr[$i]
                        ];

                        $values = [
                            'store_cd' => $store_cd,
                            'competitor_cd' => $competitor_cd,
                            'sale_date' => $date.'-'.$day_arr[$i],
                            'sale_amt' => $rows['sale_amt_'.$day_arr[$i]]??'',
                            'admin_id' => $admin_id,
                            'rt' => now(),
                            'ut' => now()
                        ];
                        
                        DB::table('competitor_sale')->updateOrInsert($where, $values);;
                    }
                // }
            }
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
