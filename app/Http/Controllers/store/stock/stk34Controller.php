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

class stk34Controller extends Controller
{
    public function index()
    {
        $sql = "
			select
				store_channel
				, store_channel_cd
				, use_yn
			from store_channel
			where dep = 1 and use_yn = 'Y'
		";

		$store_channel = DB::select($sql);

		$sql = "
			select
				store_kind
				, store_kind_cd
				, use_yn
			from store_channel
			where dep = 2 and use_yn = 'Y'
		";

		$store_kind = DB::select($sql);

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'competitors' => SLib::getCodes("COMPETITOR"),
            'sdate' => date("Y-m"),
            'edate' => date("Y-m"),
            'store_channel'	=> $store_channel,
			'store_kind'	=> $store_kind
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
        $store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

        $where = "";
        $orderby = "";
        if ($store_no != "") $where .= " and cs.store_cd like '%" . Lib::quote($store_no) . "%'";
        if ($store_channel != "") $where .= "and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

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
                    , cs.sale_memo
                from competitor_sale cs
                    left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor'
                    left outer join store s on s.store_cd = cs.store_cd
                where 1=1 and cs.sale_date >= '$sdate-01' and cs.sale_date <= '$edate-31' and cs.sale_amt > 0
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
                        where 1=1 and cs.sale_date >= '$sdate-01' and cs.sale_date <= '$edate-31' and cs.sale_amt > 0
                        $where
                        group by date_format(cs.sale_date, '%Y-%m'), cs.store_cd, cs.competitor_cd
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
                    , cs.sale_memo
                    $sale_amt
                from competitor_sale cs
                    left outer join code c on c.code_id = cs.competitor_cd and code_kind_cd = 'competitor' and c.use_yn = 'Y'
                where cs.store_cd = '$store_no' and cs.sale_date >= '$date-01' and cs.sale_date <= '$date-31'
                group by cs.competitor_cd
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
