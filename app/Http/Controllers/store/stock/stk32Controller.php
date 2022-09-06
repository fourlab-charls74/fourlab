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

class stk32Controller extends Controller
{
    public function index(Request $request)
    {
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');
        $msg_type = $request->input("is_send_msg", "false");
        $msg_type = $msg_type == 'true' ? 'send' : 'receive';

        $values = [
            'cmd' => $msg_type,
            'store_types'	=> SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view(Config::get('shop.store.view') . '/stock/stk32', $values);
    }

    public function search(Request $request)
    {
        $r = $request->all();
        $sdate = $request->input('sdate', Carbon::now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $sender = $request->input('sender');
        $sender_cd = $request->input('sender_cd');
        $content = $request->input('content');
        $msg_type = $request->input("is_send_msg", "false");
        $msg_types = $msg_type == 'true' ? 'send' : 'receive';

        $where = "";
        $orderby = "";
        if ($sender != "") $where .= "and sender_cd like '%" . $sender . "%'  ";
        if ($content != "") $where .= " and content like '%" . Lib::quote($content) . "%' ";
     
        // ordreby
        // $ord = $r['ord'] ?? 'desc';
        // $ord_field = $r['ord_field'] ?? "ms.rt";
        // if($ord_field == '') $ord_field = 'ms.' . $ord_field;
        // else $ord_field = 'ms.' . $ord_field;
        // $orderby = sprintf("order by %s %s", $ord_field, $ord);

         // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        if($msg_types == 'send'){
            $sql = 
                "
                select 
                    s.mobile,
                    ms.content,
                    ms.rt
                from msg_store ms
                    left outer join store s on s.store_nm = ms.sender_cd'
                where 1=1 $where          
                $limit
    
                ";
        }else{
            $sql = 
                "
                select
                    md.msg_cd,
                    group_concat(md.receiver_cd separator ', ') as receiver_cds,
                    ms.content,
                    md.rt,
                    md.check_yn
                from msg_store_detail md
                    left outer join msg_store ms on ms.msg_cd = md.msg_cd
                where 1=1 $where
                group by md.msg_cd
            ";

        }

       
        $result = DB::select($sql , ['sdate' => $sdate, 'edate' => $edate] );
        
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function search2(Request $request)
    {
        $store_nm = $request->input('store_nm');
        

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $where = "";
        if($store_nm != "") $where .= " and store_nm like '%" . $store_nm . "%' ";

        $sql = 
            "
            select 
                store_cd,
                store_nm,
                mobile
            from store
            where 1=1 $where
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


    public function create()
    {
        return view( Config::get('shop.store.view') . '/stock/stk32_show');
    }

    
    
    public function sendMsg(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');
        $store_cds = $request->input('store_cd','');
        $store_cd = explode(',',$store_cds);
        // dd($store_cd);
        
        $stores = [];
        foreach($store_cd as $store){
            $store_nm = 
                "
                select 
                    store_cd,
                    store_nm
                from store
                where store_cd in('$store');
                ";
                $sc = DB::selectOne($store_nm);
                if($sc != null ){
                    array_push($stores, $sc);
                }
        }
          
        $values = [
            'store_types'	=> SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'stores' => $stores,
            'store_cds' => $store_cds
        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_sendMsg', $values);
    }


    public function store(Request $request)
    {
       
        $content = $request->input('content');
        $store_cds = $request->input('store_cds');
        $store_cds = explode(',',$store_cds);
        $sender_type = "H";
        $sender_cd = "HEAD";
        // $reservation_yn = "Y";
        // $reservation_date = $request->input('rm_date');
        // $hour = $request->input('rm_hour');
        // $min = $request->input('rm_minute');
        
        // if($reservation_yn == "Y"){
        //     $reservation_time = $reservation_date;
        // }else{
        //     $reservation_time = "";
        // }

        try {
            DB::beginTransaction();

                $res = DB::table('msg_store')
                    ->insertGetId([
                        'sender_type' => $sender_type,
                        'sender_cd' => $sender_cd,
                        'reservation_yn' => 'N',
                        'reservation_date' => '',
                        'content' => $content,
                        'rt' => now()
                    ]);
                    foreach($store_cds as $sc){
                        DB::table('msg_store_detail')
                            ->insert([
                                'msg_cd' => $res,
                                'receiver_type' => 'S',
                                'receiver_cd' => $sc ,
                                'check_yn' => 'N',
                                'rt' => now()
                            ]);
                        }

            DB::commit();
            $code = '200';
            $msg = "";
        } catch (Exception $e) {
            DB::rollBack();
            $code = '500';
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }


    // public function show($no) 
    // {
    //     $sql = /** @lang text */
    //         "
    //         select 
    //             receiver_cd,
    //             store_nm,
    //             mobile,
    //         from msg_store_detail
	// 		where msg_cd = $no
    //     ";
    //     $result = DB::selectOne($sql,array("msg_cd" => $no));

    //     $values = [
    //         'msg_cd' => $no,
    //         'result' => $result,
    //     ];

    //     return view( Config::get('shop.store.view') . '/stock/stk32_show', $values);
    // }

    
}