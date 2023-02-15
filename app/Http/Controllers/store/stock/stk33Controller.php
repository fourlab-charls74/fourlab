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
use DateTime;

use App\Models\Conf;

class stk33Controller extends Controller
{
    public function index(Request $request)
    {

        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'week')->format('Y-m-d');


        $req_sdate = $request->query("date");
		$req_edate = $request->query("edate");

		if($req_sdate != '') {
			if($req_edate != '') {
				$sdate = $req_sdate;
				$edate = $req_edate;
			} else {
				$sdate = new DateTime($req_sdate . "-01");
				$edate = $sdate->format('Y-m-t');
				$sdate = $sdate->format('Y-m-d');
			}
		}

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'competitors' => SLib::getCodes("COMPETITOR"),
            'sdate' => $sdate,
            'edate' => $edate,
        ];
        return view(Config::get('shop.store.view') . '/stock/stk33', $values);
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
                    left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                    left outer join store s on s.store_cd = cs.store_cd
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
                            left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                            left outer join store s on s.store_cd = cs.store_cd
                        where 1=1 and sale_date >= '$sdate' and sale_date <= '$edate'
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
       

        $values = [
            
        ];
        

        return view(Config::get('shop.store.view') . '/stock/stk33_show', $values);
    }


    public function com_search(Request $request)
    {
        $store_nm = $request->input('store_nm', '');
        $store_no = $request->input('store_no', '');
        $year = $request->input('year', '');
        $month = $request->input('month', '');
        $day = $request->input('day', '');

        $amt_date = $year.'-'.$month.'-'.$day;

        $where = "";

        if($year != '' || $month != '' || $day != '') $where .= "and cs.sale_date = '$amt_date'";
        if($store_no != '') $where .= "and cs.store_cd = '$store_no'";

        $query = "
            select count(*) as cnt from competitor_sale where sale_date = '$amt_date' and store_cd = '$store_no'
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
                "total" => count($result)
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
            
            foreach($data as $rows) {
                
                $where	= [
                    'store_cd' => $rows['store_cd'], 
                    'competitor_cd' => $rows['competitor_cd'],
                    'sale_date' => $date
                ];

                $values	= [
                    'store_cd' => $rows['store_cd'],
                    'competitor_cd' => $rows['competitor_cd'],
                    'sale_date' => $date,
                    'sale_amt' => $rows['sale_amt'] ?? 0,
                    'admin_id' => $admin_id,
                    'rt' => now(),
                    'ut' => now()
                ];

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
