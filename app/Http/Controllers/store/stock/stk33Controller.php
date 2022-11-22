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

class stk33Controller extends Controller
{
    public function index()
    {
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'week')->format('Y-m');

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'competitors' => SLib::getCodes("COMPETITOR"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view(Config::get('shop.store.view') . '/stock/stk33', $values);
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
        // if ($store_type != "") $where .= " and store_type = '$store_type'";

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "s.rt";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sql = "
            select code_id from code where code_kind_cd = 'competitor'
        ";

        $code_ids = array_map(function($row) {return $row->code_id;}, DB::select($sql));


       
        $com = "";
        foreach($code_ids as $code_id) {
            // $com .= ", ifnull((select sale_amt from competitor_sale where competitor_cd = '$code_id' group by store_cd), 0 ) as '$code_id'";
            $com .= ", ifnull(sum(case when cs.competitor_cd = '$code_id' then cs.sale_amt end), 0) as 'amt_$code_id'";
        }

            $query =
                "
                select 
                    s.store_nm
                    , cs.sale_date
                    , sum(cs.sale_amt) as total_amt
                    , cs.store_cd
                    , cs.competitor_cd
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

            $result = DB::select($query);
            
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
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

        $sql = "
            select 
                cd.code_id as competitor_cd
                , cd.code_val as competitor_nm
                , com.store_cd
                , com.use_yn
            from code cd
                left outer join competitor com on cd.code_id = com.competitor_cd and com.store_cd = '$store_no'
            where cd.code_kind_cd = 'COMPETITOR' and cd.use_yn = 'Y'and com.use_yn = 'Y'
        
        ";

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
                DB::table('competitor_sale')
                    ->insert([
                        'store_cd' => $rows['store_cd'],
                        'competitor_cd' => $rows['competitor_cd'],
                        'sale_date' => $date,
                        'sale_amt' => $rows['sale_amt'] ?? 0,
                        'admin_id' => $admin_id,
                        'rt' => now()
                    ]);
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

    public function update($no, Request $request)
    {

        $id =  Auth('head')->user()->id;

        $subject = $request->input('subject');
        $content = $request->input('content');
        $store_cd = $request->input('store_no', '');
        $ns_cd = $no;
        $ut = DB::raw('now()');
        $rt2 = DB::raw('now()');

        if ($store_cd == null) {
            $all_store_yn = "Y";
        } else {
            $all_store_yn = "N";
        }

        $notice_store = [
            'subject' => $subject,
            'content' => $content,
            'all_store_yn' => $all_store_yn,
            'ut' => $ut
        ];

        try {
            DB::beginTransaction();

            DB::table('notice_store')
                ->where('ns_cd', '=', $ns_cd)
                ->update($notice_store);

            if ($store_cd != '') {
                foreach ($store_cd as $sc) {
                    DB::table('notice_store_detail')
                        ->insert([
                            'ns_cd' => $ns_cd,
                            'store_cd' => $sc,
                            'check_yn' => 'N',
                            'rt' => $rt2
                        ]);
                }
            }
            DB::commit();
            $code = 200;
            $msg = "";
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

    public function del_store(Request $request)
    {
        $store_cd = $request->input('data_store');
        $ns_cd = $request->input('ns_cd');

        try {
            DB::beginTransaction();

            $sql = "
                delete 
                from notice_store_detail
                where ns_cd = '$ns_cd' and store_cd = '$store_cd'
            ";

            DB::delete($sql);

            DB::commit();
            $code = '200';
            $msg = "";
        } catch (Exception $e) {
            DB::rollBack();
            $code = 500;
            $msg = "실패!";
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }
}
