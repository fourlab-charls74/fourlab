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

class stk34Controller extends Controller
{
    public function index()
    {
        $mutable = Carbon::now();
        $sdate = $mutable->sub(5, 'month')->format('Y-m');

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'competitors' => SLib::getCodes("COMPETITOR"),
            'sdate' => $sdate,
            'edate' => date("Y-m")
        ];
        return view(Config::get('shop.store.view') . '/stock/stk34', $values);
    }

    // 검색
    public function search(Request $request)
    {
        $r = $request->all();
        $sdate = $request->input('sdate');
        $edate = $request->input('edate');
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

        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $request->input('limit', 500);
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

            $sql = "
                select 
                    s.store_nm
                    , date_format(cs.sale_date, '%Y-%m') as sale_date
                    , sum(cs.sale_amt) as total_amt
                    , cs.store_cd
                    , cs.competitor_cd
                    , s.store_type
                    , c.code_val as competitor
                    , sum(cs.sale_amt) as sale_amt
                from competitor_sale cs
                    left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                    left outer join store s on s.store_cd = cs.store_cd
                where 1=1 and sale_date >= '$sdate' and sale_date <= '$edate' and cs.sale_amt > 0
                $where
                group by date_format(cs.sale_date, '%Y-%m'), cs.store_cd, cs.competitor_cd
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
                        sum(a.sale_amt) as sal_amt
                    from (
                        select 
                            s.store_nm
                            , cs.sale_date
                            , sum(cs.sale_amt) as total_amt
                            , cs.store_cd
                            , cs.competitor_cd
                            , s.store_type
                            , c.code_val as competitor
                            , cs.sale_amt as sale_amt
                        from competitor_sale cs
                            left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                            left outer join store s on s.store_cd = cs.store_cd
                        where 1=1 and sale_date >= '$sdate' and sale_date <= '$edate' and cs.sale_amt > 0
                        $where
                        group by date_format(cs.sale_date, '%Y-%m'), cs.store_cd, cs.competitor_cd
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

        $mutable = Carbon::now();
        $date = $mutable->now()->format('Y-m');

        $values = [
            'date' => $date,
        ];


        return view(Config::get('shop.store.view') . '/stock/stk34_show', $values);
    }


    public function com_search(Request $request)
    {
        $store_no = $request->input('store_no', '');
        $date = $request->input('date');
        $day = (int)$request->input('day');

        $where = "";

        if($store_no != '') $where .= "and cs.store_cd = '$store_no'";
        if($date != '') $where .= "and cs.sale_date like '$date%'";

        $query = "
            select count(*) as cnt from competitor_sale where sale_date like '$date%' and store_cd = '$store_no'
        ";

        $res = DB::selectOne($query);

        if($res->cnt > 0 ) {
            $sql = "
                select 
                    cd.code_id as competitor_cd
                    , cd.code_val as competitor_nm
                    , com.store_cd
                    , cs.sale_amt
                    , cs.sale_date
                from code cd
                    left outer join competitor com on cd.code_id = com.competitor_cd 
                    left outer join competitor_sale cs on cs.competitor_cd = com.competitor_cd
                where cd.code_kind_cd = 'COMPETITOR' and cd.use_yn = 'Y'and com.use_yn = 'Y' and com.store_cd = '$store_no'
                $where
            
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
        $day = $request->input('day');


        dd($data);
        
        try {
            DB::beginTransaction();
            
            foreach($data as $rows) {
                
                for($i = 1; $i <= $day; $i++){


                    $where	= [
                        'store_cd' => $rows['store_cd'], 
                        'competitor_cd' => $rows['competitor_cd'],
                        'sale_date' => $date
                    ];


                    $values	= [
                        'store_cd' => $rows['store_cd'],
                        'competitor_cd' => $rows['competitor_cd'],
                        'sale_date' => $date,
                        'sale_amt_'.$i => $rows['sale_amt_'.$i],
                        'admin_id' => $admin_id,
                        'rt' => now(),
                        'ut' => now()
                    ];

                }

                DB::table('competitor_sale')
                        ->updateOrInsert($where, $values);
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
