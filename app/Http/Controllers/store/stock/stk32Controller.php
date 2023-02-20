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
        $msg_types = $msg_type == 'true' ? 'send' : 'receive';

        $values = [
            'cmd' => $msg_types,
            'store_types' => SLib::getCodes("STORE_TYPE"),
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
        $content = $request->input('content');
        $msg_type = $request->input("msg_type", "");

        $where = "";
        $orderby = "";
        if ($sender != "") $where .= "and store_nm like '%" . Lib::quote($sender) . "%'  ";
        if ($content != "") $where .= " and m.content like '%" . Lib::quote($content) . "%' ";
     

        // 로그인한 계정 // 추후 수정
        $admin_type = 'H';
        $admin_cd = 'HEAD';

        //ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "m.rt";
        if($ord_field == '') $ord_field = 'm.' . $ord_field;
        else $ord_field = 'm.' . $ord_field;
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

         // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        if ($msg_type == 'send') {
            $sql = "
                select 
                    m.msg_cd
                    , msd.receiver_type
                    , group_concat(msd.receiver_cd separator ', ') as receiver_cd
                    , group_concat(if(msd.receiver_type = 'S', s.store_nm, '본사') separator ', ') as receiver_nm
                    -- , if(msd.receiver_type = 'S', s.store_nm, '본사') as receiver
                    -- , count(msd.receiver_cd) as receiver_cnt
                    , s.store_nm
                    , msd.receiver_type
                    , m.reservation_yn
                    , m.reservation_date
                    , m.content
                    , m.rt
                from msg_store m 
                    left outer join msg_store_detail msd on msd.msg_cd = m.msg_cd
                    left outer join store s on s.store_cd = msd.receiver_cd
                where m.sender_type = '$admin_type' and m.sender_cd = '$admin_cd'
                and m.rt >= :sdate and m.rt < date_add(:edate, interval 1 day)
                $where
                group by m.rt
                $orderby
                $limit
            ";
        } else if ($msg_type == 'receive') {
            $sql = "
                select 
                    m.msg_cd,
                    m.sender_cd,
                    if(m.sender_type = 'S', s.store_nm, '본사') as sender_nm,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                    md.check_yn
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                where md.receiver_type = '$admin_type' and md.receiver_cd = '$admin_cd' $where
                group by md.msg_cd
            ";

            dd($sql);
        }
       
        $result = DB::select($sql , ['sdate' => $sdate, 'edate' => $edate]);

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
            'store_types' => SLib::getCodes("STORE_TYPE"),
        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_show', $values);
    }

    public function search_store(Request $request)
    {
        $store_nm = $request->input('store_nm');
        $div_store = $request->input('store');
        $store_type = $request->input('store_type', "");

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;

        $where = "";
        if($store_nm != "" && $div_store == 'O') $where .= " and store_nm like '%" . $store_nm . "%' ";
       
        $sql	= " select store_cd from store where store_type = :store_type and use_yn = 'Y' ";
        $result = DB::select($sql,['store_type' => $store_type]);
        if($store_type != "") {
            $where	.= " and (1!=1";
            foreach($result as $row){
                $where .= " or store_cd = '" . Lib::quote($row->store_cd) . "' ";
            }
            $where	.= ")";
        }
          
            $sql = 
                "
                select 
                    store_cd,
                    store_nm,
                    mobile,
                    '$div_store' as store,
                    store_type
                from store
                where 1=1 $where
                ";
       
        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result,
        ]);

    }


    public function search_groupStore(Request $request)
    {
        $group_nm = $request->input('group_nm');
        $div_store = $request->input('div_store');

       
        $where = "";
        if($group_nm != "") $where .= " and mg.group_nm like '%" . $group_nm . "%'";

            $sql = 
                "
                select 
                    mg.group_nm,
                    mg.group_cd,
                    group_concat(s.store_nm) as group_store_nm,
                    '$div_store' as store
                from msg_group mg
                    left outer join msg_group_store mgs on mgs.group_cd = mg.group_cd
                    left outer join store s on s.store_cd = mgs.store_cd
                where 1=1 $where
                group by mg.group_cd
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

    public function sendMsg(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');
        $store_cds = $request->input('store_cd','');
        $store_cd = explode(',',$store_cds);
        $group_cds = $request->input('group_cd', '');
        $group_cd = explode(',',$group_cds);
        $group_nms = $request->input('group_nm', '');
        $group_nm = explode(',',$group_nms);
        $check = $request->input('check');
        
        $stores = [];
        foreach($store_cd as $store) {
            $store_nm = 
                "
                select 
                    store_cd,
                    store_nm
                from store
                where store_cd in('$store');
            ";
            $sc = DB::selectOne($store_nm);
            if ($sc != null) {
                array_push($stores, $sc);
            }
        }

        $groups = [];
        foreach ($group_cd as $gc) {
            $group_cd_data = 
                "
                select
                    mg.group_cd,
                    group_concat(mgs.store_cd separator ', ') as stores
                from msg_group mg
                left outer join msg_group_store mgs ON mgs.group_cd = mg.group_cd
                where mgs.group_cd = '$gc'
            ";
            $sc2 = DB::selectOne($group_cd_data);
            if ($sc2 != null) {
                array_push($groups, $sc2);
            }
        }

        $groupName = [];
        foreach ($group_nm as $gn) {
            $group_nm_data = 
                "
                select 
                    group_nm,
                    group_cd
                    
                from msg_group
                where group_nm = '$gn'

            ";
            $sc3 = DB::selectOne($group_nm_data);
            if ($sc3 != null) {
                array_push($groupName, $sc3);
            }
        }
          
        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'stores' => $stores,
            'store_cds' => $store_cds,
            'group_cds' => $group_cds,
            'group_nms' => $group_nms,
            'groups' => $groups,
            'groupName' => $groupName,
            'check' => $check

        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_sendMsg', $values);
    }


    public function showContent(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');
        $msg_cd = $request->input('msg_cd');
        $msg_type = $request->input('msg_type');
        // 로그인한 계정 // 추후 수정
        $admin_type = 'H';
        $admin_cd = 'HEAD';

        $query = "
            select content
            from msg_store
            where msg_cd = '$msg_cd'
        ";

        $res = DB::selectOne($query);

        if ($msg_type == 'send') {
            $sql = 
                "
                select 
                    m.msg_cd,
                    md.receiver_type,
                    group_concat(md.receiver_cd separator ', ') as receiver_cd,
                    group_concat(if(md.receiver_type = 'S', s.store_nm, '본사') separator ', ') as receiver_nm,
                    if(md.receiver_type = 'S', s.store_nm, '본사') as first_receiver,
                    count(md.receiver_cd) as receiver_cnt,
                    m.reservation_yn,
                    m.reservation_date,
                    m.content,
                    m.rt
                from msg_store m
                    inner join msg_store_detail md on md.msg_cd = m.msg_cd
                    left outer join store s on s.store_cd = md.receiver_cd
                where m.sender_type = '$admin_type' and m.sender_cd = '$admin_cd' and m.msg_cd = '$msg_cd'
                group by m.msg_cd
                ";
        } else if ($msg_type == 'receive') {
            $sql = "
                select 
                    m.msg_cd,
                    m.sender_cd,
                    if(m.sender_type = 'S', s.store_nm, '본사') as sender_nm,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                    md.check_yn
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                where md.receiver_type = '$admin_type' and md.receiver_cd = '$admin_cd' and m.msg_cd = '$msg_cd'
                group by md.msg_cd
            ";
        }
       
        $result = DB::selectOne($sql);
       
        if ($msg_type == 'send') {
            $values = [
                'store_types' => SLib::getCodes("STORE_TYPE"),
                'msg_type' => $msg_type,
                'sdate' => $sdate,
                'edate' => date("Y-m-d"),
                'msg_cd' => $msg_cd,
                'content' => $res->content,
                'first_receiver' => $result->first_receiver,
                'receiver_cnt' => $result->receiver_cnt,
            ];
        } else if ($msg_type == 'receive') {
            $values = [
                'store_types' => SLib::getCodes("STORE_TYPE"),
                'msg_type' => $msg_type,
                'sdate' => $sdate,
                'edate' => date("Y-m-d"),
                'msg_cd' => $msg_cd,
                'content' => $res->content,
                'sender_nm' => $result->sender_nm
            ];
        }

        return view( Config::get('shop.store.view') . '/stock/stk32_showContent', $values);
    }


    public function store(Request $request)
    {
       
        $content = $request->input('content');
        $reservation_yn = $request->input('reservation_yn');
        $store_cds = $request->input('store_cds');
        $store_cds = explode(',',$store_cds);

        $reservation_msg = $request->input('reservation_msg');
        $group_nms = $request->input('group_nms');
        $group_nms = explode(',',$group_nms);
        $group_cds = $request->input('group_cds');
        $group_cds = explode(',',$group_cds);
        $check = $request->input('check');

        $sender_type = "H";
        $sender_cd = "HEAD";
        $rm_date = $request->input('rm_date');
        $rm_hour = $request->input('rm_hour');
        $rm_min = $request->input('rm_min');
        
        if ($reservation_msg == 'true') {
            $reservation_yn = "Y";
            $reservation_date = "$rm_date"." $rm_hour:"."$rm_min:00";
        } elseif ($reservation_msg == 'false') {
            $reservation_yn = "N";
            $reservation_date = "";
        }

        try {
            DB::beginTransaction();

            if ($reservation_msg == 'true'){
                if($reservation_date > date("Y-m-d H:i:s")){
                    $res = DB::table('msg_store')
                    ->insertGetId([
                        'sender_type' => $sender_type,
                        'sender_cd' => $sender_cd,
                        'reservation_yn' => $reservation_yn,
                        'reservation_date' => $reservation_date,
                        'content' => $content,
                        'rt' => now()
                    ]);
    
                    if ($check == "O") {
                        foreach ($store_cds as $sc) {
                            DB::table('msg_store_detail')
                                ->insert([
                                    'msg_cd' => $res,
                                    'receiver_type' => 'S',
                                    'receiver_cd' => $sc ,
                                    'check_yn' => 'N',
                                    'rt' => now()
                                ]);
                            }
                    } else {
                        $send = [];
                        foreach ($group_cds as $gc) {
                            $result = 
                                "
                                    select 
                                        store_cd
                                    from msg_group_store
                                    where group_cd = '$gc'
                            ";
                            $rs = DB::select($result);
                            if ($rs != null) {
                                array_push($send, $rs);
                            }
                        }
                        $arr = array_merge(...array_values($send));
                        foreach ($arr as $r) {
                            DB::table('msg_store_detail')
                                ->insert([
                                    'msg_cd' => $res,
                                    'receiver_type' => 'S',
                                    'receiver_cd' => $r->store_cd ,
                                    'check_yn' => 'N',
                                    'rt' => now()
                                ]);
                        }
                    }
                    $code = 200;
                    $msg = "알림 전송에 성공하였습니다.";
                } else {
                    $code = 100;
                    $msg = "예약발송시간이 현재시간보다 이전입니다. 예약발송 시간을 변경해주세요.";
                }
            } else {
                $res = DB::table('msg_store')
                    ->insertGetId([
                        'sender_type' => $sender_type,
                        'sender_cd' => $sender_cd,
                        'reservation_yn' => $reservation_yn,
                        'reservation_date' => $reservation_date,
                        'content' => $content,
                        'rt' => now()
                    ]);
    
                    if ($check == "O") {
                        foreach ($store_cds as $sc) {
                            DB::table('msg_store_detail')
                                ->insert([
                                    'msg_cd' => $res,
                                    'receiver_type' => 'S',
                                    'receiver_cd' => $sc ,
                                    'check_yn' => 'N',
                                    'rt' => now()
                                ]);
                            }
                    } else {
                        $send = [];
                        foreach ($group_cds as $gc) {
                            $result = 
                                "
                                    select 
                                        store_cd
                                    from msg_group_store
                                    where group_cd = '$gc'
                            ";
                            $rs = DB::select($result);
                            if ($rs != null) {
                                array_push($send, $rs);
                            }
                        }
                        $arr = array_merge(...array_values($send));
                        foreach ($arr as $r) {
                            DB::table('msg_store_detail')
                                ->insert([
                                    'msg_cd' => $res,
                                    'receiver_type' => 'S',
                                    'receiver_cd' => $r->store_cd ,
                                    'check_yn' => 'N',
                                    'rt' => now()
                                ]);
                        }
                    }
                    $code = 200;
                    $msg = "알림 전송에 성공하였습니다.";
            }
            DB::commit();
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


    public function msg_read(Request $request)
    {
        $msg_cd = $request->input('msg_cd');

        $msg_store_detail = [
            'check_yn' => 'Y',
            'check_date' => now()
        ];

        try {
            DB::beginTransaction();

            foreach ($msg_cd as $mc) {
                DB::table('msg_store_detail')
                    ->where('msg_cd', '=', $mc)
                    ->update($msg_store_detail);
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


    public function msg_del(Request $request)
    {
        $msg_cd = $request->input('msg_cd');
        
        // dd($msg_cd);

        try {
            DB::beginTransaction();

            foreach ($msg_cd as $mc) {
                DB::table('msg_store')
                    ->where('msg_cd', '=', $mc)
                    ->delete();
            }

            DB::commit();
            $code = 200;
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


    public function group(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'month')->format('Y-m-d');
       
        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];

        return view(Config::get('shop.store.view') . '/stock/stk32_group', $values);
    }

    public function group_show(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'month')->format('Y-m-d');
       
        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];

        return view(Config::get('shop.store.view') . '/stock/stk32_group_show', $values);
    }


    public function search_group(Request $request)
    {
        $sql = 
        "
            select 
                group_cd,
                group_nm
            from msg_group
            
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


    public function search_group2(Request $request)
    {
        $group_cd = $request->input('group_cd');

        $sql = 
        "
            select 
                m.group_cd,
                m.store_cd,
                s.store_nm
            from msg_group_store m 
                left outer join store s on s.store_cd = m.store_cd
                inner join msg_group mg on m.group_cd = mg.group_cd
            where m.group_cd = '$group_cd'
            
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


    public function add_group(Request $request)
    {
        $group_nm = $request->input('group_nm');
        $store_cd = $request->input('store_cd');
        $store_cd = explode(',',$store_cd);
        $code = "";

        try {
            DB::beginTransaction();

            $sql = "
                select 
                    * 
                from msg_group
                where group_nm = '$group_nm'
            ";
            $r = DB::select($sql);

            if (count($r) > 0) {
                $code = 100;

            } else {
                $res = DB::table('msg_group')
                ->insertGetId([
                    'group_nm' => $group_nm,
                    'account_cd' => 'HEAD',
                    'rt' => now()
                ]);

                foreach ($store_cd as $sc) {
                    DB::table('msg_group_store')
                        ->insert([
                            'group_cd' => $res,
                            'store_cd' => $sc,
                            'rt' => now()
                        ]);
                }
                $code = 200;
            }

           
            DB::commit();
            $msg = '';
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

    public function addGroup_show(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'month')->format('Y-m-d');
       
        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];

        return view(Config::get('shop.store.view') . '/stock/stk32_addGroup', $values);
    }

    public function addGroup(Request $request)
    {
        $group_nm = $request->input('group_nm');
        $store_cd = $request->input('store_cd');
        $store_cd = explode(',',$store_cd);
        $code = "";

        try {
            DB::beginTransaction();

            $sql = "
                select 
                    * 
                from msg_group
                where group_nm = '$group_nm'
            ";
            $r = DB::select($sql);

            if (count($r) > 0) {
                $code = 100;

            } else {
                $res = DB::table('msg_group')
                ->insertGetId([
                    'group_nm' => $group_nm,
                    'account_cd' => 'HEAD',
                    'rt' => now()
                ]);

                foreach ($store_cd as $sc) {
                    DB::table('msg_group_store')
                        ->insert([
                            'group_cd' => $res,
                            'store_cd' => $sc,
                            'rt' => now()
                        ]);
                }
                $code = 200;
            }

           
            DB::commit();
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

    public function update(Request $request)
    {
        $group_nm = $request->input('group_nm');
        $group_cd = $request->input('group_cd');
        $store_cd = $request->input('store_cd');
        $store_cd = explode(',',$store_cd);

        // $del_group_cd = $request->input('del_group_cd');
        // $del_group_cd = explode(',',$del_group_cd);

        $add_store = $request->input('add_store');

        if($add_store != null) {
            $add_store = explode(',',$add_store);
        }

        $del_store = $request->input('del_data');
        if ($del_store != null) {
            $del_store = explode(',',$del_store);
        }
        
        try {
            DB::beginTransaction();

            //그룹명 변경 쿼리문
            DB::table('msg_group')
                ->where('group_cd', '=', $group_cd)
                ->update([
                    'group_nm' => $group_nm
                ]);

            //매장 추가할 때 
            if ($add_store != null ) {
                foreach ($add_store as $as) {
                    DB::table('msg_group_store')
                        ->insert([
                            'group_cd' => $group_cd,
                            'store_cd' => $as,
                            'rt' => now()
                        ]);
                }
            }

            //삭제한 매장 저장
            if ($del_store != null) {
                foreach ($del_store as $ds) {
                    DB::table('msg_group_store')
                        ->where('group_cd', '=', $group_cd)
                        ->where('store_cd', '=', $ds)
                        ->delete();
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


    //그룹 관리 그룹삭제
    public function del_group(Request $request)
    {
        $rows = $request->input('rows');

        try {
            DB::beginTransaction();

            foreach($rows as $r) {
                
                DB::table('msg_group')
                ->where('group_cd', '=', $r['group_cd'])
                ->delete();
                
                DB::table('msg_group_store')
                ->where('group_cd', '=', $r['group_cd'])
                ->delete();
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

    //그룹관리 그룹에 속한 매장 삭제
    public function del_store(Request $request)
    {
        $rows2 = $request->input('rows2');

        try {
            DB::beginTransaction();

            foreach($rows2 as $r) {
                
                DB::table('msg_group_store')
                ->where('group_cd', '=', $r['group_cd'])
                ->where('store_cd', '=', $r['store_cd'])
                ->delete();
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

}