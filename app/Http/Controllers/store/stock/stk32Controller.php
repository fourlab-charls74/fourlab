<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Auth;
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
        $read_yn = $request->input('read_yn');

        $where = "";
		$having = "";
        $orderby = "";
		
		if ($msg_type == 'send') {
        	if ($sender != "") $having .= "having receiver_nm like '%" . Lib::quote($sender) . "%'  ";
		} else {
			if ($sender != "") $having .= "having sender_nm like '%" . Lib::quote($sender) . "%'  ";			
		}
        if ($content != "") $where .= " and m.content like '%" . Lib::quote($content) . "%' ";
        if ($read_yn != "") $where .= "and md.check_yn = '$read_yn'";
     

        // 로그인한 계정 // 추후 수정
        $admin_type = 'H';
        $admin_id = Auth('head')->user()->id;

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
                    , md.receiver_type
                   	, group_concat(if(md.receiver_type = 'S', s.store_nm, if(md.receiver_type = 'U', mu.name, if(md.receiver_type = 'H','본사',''))) separator ', ') as receiver_nm
                    , if(md.receiver_type = 'S', s.store_nm, if(md.receiver_type = 'U', mu.name,if(md.receiver_type = 'G', s.store_nm, ''))) as first_receiver
                    , s.store_nm
                    , md.receiver_type
                    , m.reservation_yn
                    , m.reservation_date
                    , m.content
                    , m.rt
                    , md.check_yn
                from msg_store m 
                    left outer join msg_store_detail md on md.msg_cd = m.msg_cd
                    left outer join store s on s.store_cd = md.receiver_cd
                	left outer join mgr_user mu on mu.id = md.receiver_cd
                where 1=1 and m.sender_cd = '$admin_id'
                and m.rt >= :sdate and m.rt < date_add(:edate, interval 1 day)
                $where
                group by m.rt
            	$having
                $orderby
                $limit
            ";
			
        } else if ($msg_type == 'receive') {
            $sql = "
                select 
                    m.msg_cd,
                    m.sender_cd,
                    -- if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'U', mu.name, if(m.sender_type = 'H','본사',''))) as sender_nm,
                    if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'H', mu.name,'')) as sender_nm,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                    md.check_yn
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                	left outer join mgr_user mu on mu.id = m.sender_cd
                where 1=1 and md.receiver_cd = '$admin_id'
                   and (m.reservation_yn <> 'Y' or m.reservation_date <= now())
                and m.rt >= :sdate and m.rt < date_add(:edate, interval 1 day)
                $where
                group by md.msg_cd
                $having
                $orderby
                $limit
            ";
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
            'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_show', $values);
    }

    public function search_store(Request $request)
    {
        $store_nm = $request->input('store_nm');
        $div_store = $request->input('store');
        $store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;

        $where = "";
        if($store_nm != "" && $div_store == 'S') $where .= " and store_nm like '%" . $store_nm . "%' ";
       
        if ($store_channel != "") $where .= " and store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= " and store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
          
            $sql = 
                "
                select 
                    store_cd,
                    store_nm,
                	phone,
                    '$div_store' as store,
                    store_type
                from store
                where 1=1 $where and use_yn = 'Y'
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
                    left outer join store s on s.store_cd = mgs.store_cd and s.use_yn = 'Y'
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


	public function search_hq_user_id(Request $request)
	{
		$user_id = $request->input('user_id');
		$user_name = $request->input('user_name');

		// pagination
		$page = $r['page'] ?? 1;
		if ($page < 1 or $page == "") $page = 1;

		$where = "";
		if ($user_id != '') $where .= " and id like '%$user_id%'";
		if ($user_name != '') $where .= "and name like '%$user_name%'";
		

		$sql =
			"
               select
               		id
               		, name
               		, grade
               		, store_cd
               		, store_nm
               		, part
               		, posi
               from mgr_user
               where 1=1 and use_yn = 'Y' $where
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
		$user_ids = $request->input('user_id');
		$user_id = explode(',', $user_ids);
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
                left outer join msg_group_store mgs on mgs.group_cd = mg.group_cd
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
		
		$ids = [];
		foreach ($user_id as $ui) {
			$sql = "
				select
					id
					, name
				from mgr_user
				where id = '$ui'
			";
			$id = DB::selectOne($sql);
			if ($id != null) {
				array_push($ids, $id);
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
			'user_ids' => $user_ids,
			'ids' => $ids,
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
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

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
                    group_concat(if(md.receiver_type = 'S', s.store_nm, if(md.receiver_type = 'U', mu.name, if(md.receiver_type = 'H','본사',''))) separator ', ') as receiver_nm,
                    if(md.receiver_type = 'S', s.store_nm, if(md.receiver_type = 'U', concat(mu.name,'(본사)'),if(md.receiver_type = 'G', s.store_nm, ''))) as first_receiver,
                    count(md.receiver_cd) as receiver_cnt,
                    m.reservation_yn,
                    m.reservation_date,
                    m.content,
                    m.rt,
                    md.check_yn,
                    s.store_nm
                from msg_store m
                    inner join msg_store_detail md on md.msg_cd = m.msg_cd
                    left outer join store s on s.store_cd = md.receiver_cd
                	left outer join mgr_user mu on mu.id = md.receiver_cd
                where m.msg_cd = '$msg_cd'
                group by m.msg_cd
                ";
			
        } else if ($msg_type == 'receive') {
            $sql = "
                select 
                    m.msg_cd,
                    m.sender_cd,
                    if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'H', concat(mu.name,'(본사)'),'')) as sender_nm,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                    md.check_yn,
                    m.reservation_yn,
                    m.reservation_date
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                	left outer join mgr_user mu on mu.id = m.sender_cd
                where m.msg_cd = '$msg_cd'
                group by md.msg_cd
            ";
        } else if ($msg_type == 'pop') {
            $sql = "
                select 
                    m.msg_cd,
                    m.sender_cd,
                   	if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'H', concat(mu.name,'(본사)'),'')) as sender_nm,
                    m.reservation_yn,
                    m.reservation_date,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                    md.check_yn,
                    m.msg_kind
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                	left outer join mgr_user mu on mu.id = m.sender_cd
                where m.msg_cd = '$msg_cd'
                group by md.msg_cd
            ";
        }
       
        $result = DB::selectOne($sql);

        $receiver_nm = $result->receiver_nm??'';
        $receiver_nm = explode(', ',$receiver_nm);


        $sql = "
            select 
                m.msg_cd,
                md.receiver_type,
                md.check_yn,
            	if(md.receiver_type = 'S', s.store_nm, if(md.receiver_type = 'U', mu.name, if(md.receiver_type = 'G', s.store_nm, ''))) as stores
            from msg_store m
                inner join msg_store_detail md on md.msg_cd = m.msg_cd
                left outer join store s on s.store_cd = md.receiver_cd
            	left outer join mgr_user mu on mu.id = md.receiver_cd
            where  m.msg_cd = '$msg_cd'
        ";

        $store = DB::select($sql);

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
                'receiver_nm' => $receiver_nm,
                'store_nm' => $result->store_nm,
                'check_yn' => $result->check_yn,
                'store' => $store,
				'admin_id' => $admin_id,
				'admin_nm' => $admin_nm
            ];
        } else if ($msg_type == 'receive') {
            $values = [
                'store_types' => SLib::getCodes("STORE_TYPE"),
                'msg_type' => $msg_type,
                'sdate' => $sdate,
                'edate' => date("Y-m-d"),
                'msg_cd' => $msg_cd,
                'content' => $res->content,
                'sender_nm' => $result->sender_nm,
				'reservation_yn' => $result->reservation_yn,
				'reservation_date' => $result->reservation_date,
				'rt' => $result->rt,
                'store' => $store,
				'admin_id' => $admin_id,
				'admin_nm' => $admin_nm
            ];
			
        } else if ($msg_type == 'pop') {
            $values = [
                'msg_type' => $msg_type,
                'msg_cd' => $msg_cd,
                'content' => $result->content,
                'sender_nm' => $result->sender_nm,
                'msg_kind' => $result->msg_kind,
                'reservation_yn' => $result->reservation_yn,
                'reservation_date' => $result->reservation_date,
                'rt' => $result->rt,
				'store' => $store,
				'admin_id' => $admin_id,
				'admin_nm' => $admin_nm
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
		$user_ids = $request->input('user_ids');
		$user_ids = explode(',', $user_ids);
        $check = $request->input('check');
		$admin_id = Auth('head')->user()->id;
        $rm_date = $request->input('rm_date');
        $rm_hour = $request->input('rm_hour');
        $rm_min = $request->input('rm_min');
		$msg_cd = $request->input('msg_cd','');
		
        if ($reservation_msg == 'true') {
            $reservation_yn = "Y";
            $reservation_date = "$rm_date"." $rm_hour:"."$rm_min:00";
        } elseif ($reservation_msg == 'false') {
            $reservation_yn = "N";
            $reservation_date = "";
        }

        try {
            DB::beginTransaction();

			if ($msg_cd != '') {
				$sql = "
					select
						msg_cd
						, msg_cd_p
					from msg_store
					where msg_cd = $msg_cd
				";
				$res = DB::selectOne($sql);

				if ($res->msg_cd_p == '') {
					$msg_cd_p = $res->msg_cd;
				} else {
					$msg_cd_p = $res->msg_cd_p;
				}

			} else {
				$msg_cd_p = '';
			}

            if ($reservation_msg == 'true'){
                if($reservation_date > date("Y-m-d H:i:s")){
                    $res = DB::table('msg_store')
                    ->insertGetId([
						'msg_cd_p' => $msg_cd_p,
                        'sender_type' => 'H', //본사 : H , 매장 : S
                        'sender_cd' => $admin_id,
                        'reservation_yn' => $reservation_yn,
                        'reservation_date' => $reservation_date,
                        'content' => $content,
                        'rt' => now()
                    ]);

					DB::table('msg_store')
						->insert([
							'msg_cd_p' => $res,
						]);
    
                    if ($check == "S") {
						foreach ($store_cds as $sc) {
							DB::table('msg_store_detail')
								->insert([
									'msg_cd' => $res,
									'receiver_type' => 'S',
									'receiver_cd' => $sc,
									'check_yn' => 'N',
									'rt' => now()
								]);
						}
					} elseif ($check == "U") {
						foreach ($user_ids as $id) {
							DB::table('msg_store_detail')
								->insert([
									'msg_cd' => $res,
									'receiver_type' => 'U',
									'receiver_cd' => $id,
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
                                    'receiver_type' => 'G',
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
						'msg_cd_p' => $msg_cd_p,
                        'sender_type' => 'H', // 본사 : H 매장 : S
                        'sender_cd' => $admin_id,
                        'reservation_yn' => $reservation_yn,
                        'reservation_date' => $reservation_date,
                        'content' => $content,
                        'rt' => now()
                    ]);
    
                    if ($check == "S") {
						foreach ($store_cds as $sc) {
							DB::table('msg_store_detail')
								->insert([
									'msg_cd' => $res,
									'receiver_type' => 'S',
									'receiver_cd' => $sc,
									'check_yn' => 'N',
									'rt' => now()
								]);
							
						}
					} elseif ($check == "U" || $check == 'H') {
						foreach ($user_ids as $id) {
							DB::table('msg_store_detail')
								->insert([
									'msg_cd' => $res,
									'receiver_type' => 'U',
									'receiver_cd' => $id,
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
                                    'receiver_type' => 'G',
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

            if(is_array($msg_cd)) {
                foreach ($msg_cd as $mc) {
                    DB::table('msg_store_detail')
                        ->where('msg_cd', '=', $mc)
                        ->update($msg_store_detail);
                }
            } else {
                DB::table('msg_store_detail')
                        ->where([
                            ['msg_cd', '=', $msg_cd],
                        ])
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

    public function popup_chk(Request $request)
    {
		$admin_id = Auth('head')->user()->id;
        $sql = "
            select 
                md.msg_cd as msg_cd
            from msg_store_detail md
                left outer join msg_store m on m.msg_cd = md.msg_cd
                left outer join store s on s.store_cd = m.sender_cd
            where md.receiver_cd = :admin_id and md.check_yn = 'N' and (m.reservation_yn = 'N' 
                and (md.rt >= date_add(now(), interval -1 month)) or (m.reservation_yn = 'Y' and (m.reservation_date < now() and m.reservation_date >= date_add(now(), interval -1 month))))
            order by md.rt desc
        ";

        $msgs = DB::select($sql, ['admin_id' => $admin_id]);

        return response()->json([
			"code" => 200,
            "msgs"  => $msgs,
		]);
    }

	public function reply_msg(Request $request)
	{
		$msg_cd = $request->input('msg_cd');
		$mutable = Carbon::now();
		$sdate	= $mutable->sub(1, 'month')->format('Y-m-d');
		$sender_cd = Auth('head')->user()->id;
		//$sender_type = 'H';
		$store_cds = "";
		$group_cds = "";
		$user_ids = "";
		
		$sql = "
			 select 
                    m.msg_cd,
                    if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'H', concat(mu.name,'(본사)'),'')) as user_nm,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                	m.sender_type as receiver_type,
                	m.sender_cd as receiver_cd,
                    md.check_yn,
                    m.reservation_yn,
                    m.reservation_date
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                	left outer join mgr_user mu on mu.id = m.sender_cd
                where m.msg_cd = '$msg_cd'
                group by md.msg_cd
		";
		$msg = DB::selectOne($sql, ['msg_cd' => $msg_cd]);
		
		if ($msg->receiver_type == 'H') {
			$user_ids = $msg->receiver_cd;
		} else {
			$store_cds = $msg->receiver_cd;
		}

		$values = [
			'sender_cd' => $sender_cd,
			'content' 	=> $msg->content,
			'msg_cd' 	=> $msg_cd,
			'sdate' 	=> $sdate,
			'edate'		=> date("Y-m-d"),
			'store_cds' => $store_cds,
			'group_cds' => $group_cds,
			'user_ids'	=> $user_ids,
			'user_nm'	=> $msg->user_nm,
			'check' 	=> $msg->receiver_type,
			'content'	=> $msg->content
			
		];
		return view(Config::get('shop.store.view') . '/stock/stk32_reply', $values);
	}

	// 알리미 재전송을 했을 때 check_yn값이 Y로 업데이트되는 부분
	public function update_check_yn(Request $request)
	{
		$msg_cd = $request->input('msg_cd');
		$user_store = Auth('head')->user()->store_nm;
		$user_id = Auth('head')->user()->id;

		try {
			DB::beginTransaction();

			//그룹명 변경 쿼리문
			DB::table('msg_store_detail')
				->where('msg_cd', '=', $msg_cd)
				->where('receiver_cd', '=', $user_store)
				->orWhere('receiver_cd', '=', $user_id)
				->update([
					'check_yn' => 'Y'
				]);

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
