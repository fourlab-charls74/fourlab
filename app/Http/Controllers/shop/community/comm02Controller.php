<?php

namespace App\Http\Controllers\shop\community;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class comm02Controller extends Controller
{
    public function index(Request $request)
    {
		$mutable 	= Carbon::now();
        $sdate		= $mutable->sub(1, 'month')->format('Y-m-d');
        $msg_type 	= $request->input("is_send_msg", "false");
        $msg_types 	= $msg_type == 'true' ? 'send' : 'receive';

        $values = [
            'cmd' 			=> $msg_types,
            'store_types' 	=> SLib::getCodes("STORE_TYPE"),
            'sdate' 		=> $sdate,
            'edate' 		=> date("Y-m-d")
        ];
        return view(Config::get('shop.shop.view') . '/community/comm02', $values);
    }

    public function search(Request $request)
    {
        $r 			= $request->all();
        $sdate 		= $request->input('sdate', Carbon::now()->sub(1, 'month')->format('Y-m-d'));
        $edate 		= $request->input('edate', date("Y-m-d"));
        $sender 	= $request->input('sender');
        $content	= $request->input('content');
        $msg_type 	= $request->input("msg_type", "");

        $where = "";
        $orderby = "";
        if ($sender != "") $where .= "and store_nm like '%" . Lib::quote($sender) . "%'  ";
        if ($content != "") $where .= " and m.content like '%" . Lib::quote($content) . "%' ";
     

        // 로그인한 계정 // 추후 수정
        $user_store = Auth('head')->user()->store_cd;
        $user_id = Auth('head')->user()->id;

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
                    , msd.receiver_cd
                    , if(msd.receiver_type = 'S', s.store_nm, if(msd.receiver_type = 'U', mu.name, if(msd.receiver_type = 'H','본사',''))) as receiver_nm
                    , s.store_nm
                    , msd.receiver_type
                    , msd.check_yn
                    , msd.check_date
                    , m.reservation_yn
                    , m.reservation_date
                    , m.content
                    , m.rt
                from msg_store m 
                    left outer join msg_store_detail msd on msd.msg_cd = m.msg_cd
                    left outer join store s on s.store_cd = msd.receiver_cd
                	left outer join mgr_user mu on mu.id = msd.receiver_cd
                where (m.sender_cd = '$user_id' or m.sender_cd = '$user_store')
                and m.rt >= :sdate and m.rt < date_add(:edate, interval 1 day) and m.del_yn = 'N'
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
                    -- ifnull(if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'U', mu.name,if(m.sender_type = 'H', '본사', if(m.sender_type = 'G', s.store_nm, '')))), mu.name) as sender_nm,
                   	if(m.sender_type = 'S', mu.name, if(m.sender_type = 'U', mu.name, if(m.sender_type = 'H', mu.name,''))) as sender_nm,
                    s.phone as mobile,
                    m.content,
                    md.rt,
                    md.check_yn
                from msg_store_detail md
                    left outer join msg_store m on m.msg_cd = md.msg_cd
                    left outer join store s on s.store_cd = m.sender_cd
                	left outer join mgr_user mu on mu.id = m.sender_cd
                where (md.receiver_cd = '$user_store' or md.receiver_cd = '$user_id')
                   and (m.reservation_yn <> 'Y' or m.reservation_date <= now())
                and m.rt >= :sdate and m.rt < date_add(:edate, interval 1 day) and m.del_yn = 'N'
                $where
                group by md.msg_cd
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
        ];

        return view( Config::get('shop.shop.view') . '/community/comm02_show', $values);
    }

    public function search_store(Request $request)
    {
        $store_nm 	= $request->input('store_nm');
        $div_store 	= $request->input('store');
        $store_type = $request->input('store_type', "");

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;

        $where = "";
        if($store_nm != "" && $div_store == 'S') $where .= " and store_nm like '%" . $store_nm . "%' ";
       
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
                where 1=1 and store_type = '08' and use_yn = 'Y' $where
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

	public function search_hq_user_id(Request $request)
	{
		$user_id 	= $request->input('user_id');
		$user_name 	= $request->input('user_name');

		// pagination
		$page 		= $r['page'] ?? 1;
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
        
        $mutable 	= Carbon::now();
        $sdate		= $mutable->sub(1, 'month')->format('Y-m-d');
        $store_cds 	= $request->input('store_cd','');
        $store_cd 	= explode(',',$store_cds);
		$user_ids 	= $request->input('user_id');
		$user_id 	= explode(',', $user_ids);
        $check 		= $request->input('check');
        
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
            'sdate' 		=> $sdate,
            'edate' 		=> date("Y-m-d"),
            'stores' 		=> $stores,
            'store_cds' 	=> $store_cds,
			'user_ids' 		=> $user_ids,
			'ids' 			=> $ids,
            'check' 		=> $check

        ];

        return view( Config::get('shop.shop.view') . '/community/comm02_sendMsg', $values);
    }


    public function showContent(Request $request)
    {
        
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');
        $msg_cd = $request->input('msg_cd');
        $msg_type = $request->input('msg_type');
        // 로그인한 계정 // 추후 수정
        $user_store = Auth('head')->user()->store_cd;
        $user_store_nm = Auth('head')->user()->store_nm;
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
        if ($user_store == 'L0025') {
            $admin_type = 'H';
        } else {
            $admin_type = 'S';
        }
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
                    md.receiver_cd as receiver_cd,
                    if(md.receiver_type = 'S', s.store_nm, if(md.receiver_type = 'U', concat(mu.name, '(본사)'), if(md.receiver_type = 'H','본사',''))) as receiver_nm,
                    m.reservation_yn,
                    m.reservation_date,
                    m.content,
                    m.rt
                from msg_store m
                    inner join msg_store_detail md on md.msg_cd = m.msg_cd
                    left outer join store s on s.store_cd = md.receiver_cd
                	left outer join mgr_user mu on mu.id = md.receiver_cd
                group by m.msg_cd
                ";
			
        } else if ($msg_type == 'receive') {
            $sql = "
                select
                    m.msg_cd,
                    m.sender_cd,
                    if(m.sender_type = 'S', mu.name,if(m.sender_type = 'H', concat(mu.name, '(본사)'),'')) as sender_nm,
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
                where m.msg_cd = '$msg_cd' and (md.receiver_cd = '$admin_id' or md.receiver_cd = '$user_store')
                group by md.msg_cd
            ";
        } else if ($msg_type == 'pop') {
            $sql = "
                select 
                    m.msg_cd,
                    m.sender_cd,
                    if(m.sender_type = 'S', mu.name,if(m.sender_type = 'H', concat(mu.name,'(본사)'),'')) as sender_nm,
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
                where m.msg_cd = '$msg_cd' and (md.receiver_cd = '$admin_id' or md.receiver_cd = '$user_store')
                group by md.msg_cd
            ";
        }
       
        $result = DB::selectOne($sql);
       
        if ($msg_type == 'send') {
            $values = [
                'store_types' 	=> SLib::getCodes("STORE_TYPE"),
                'msg_type' 		=> $msg_type,
                'sdate' 		=> $sdate,
                'edate' 		=> date("Y-m-d"),
                'msg_cd' 		=> $msg_cd,
                'content' 		=> $res->content,
				'receiver_nm' 	=> $result->receiver_nm,
				'admin_id' 		=> $admin_id,
				'admin_nm' 		=> $admin_nm,
				'user_store_nm' => $user_store_nm

            ];
        } else if ($msg_type == 'receive') {
            $values = [
                'store_types' 		=> SLib::getCodes("STORE_TYPE"),
                'msg_type' 			=> $msg_type,
                'sdate' 			=> $sdate,
                'edate' 			=> date("Y-m-d"),
                'msg_cd' 			=> $msg_cd,
                'content' 			=> $res->content,
                'sender_nm' 		=> $result->sender_nm,
				'reservation_yn'	=> $result->reservation_yn,
				'reservation_date' 	=> $result->reservation_date,
				'rt' 				=> $result->rt,
				'admin_id' 			=> $admin_id,
				'admin_nm' 			=> $admin_nm,
				'user_store_nm' 	=> $user_store_nm
            ];
        } else if ($msg_type == 'pop') {
            $values = [
                'msg_type' 			=> $msg_type,
                'msg_cd' 			=> $msg_cd,
                'content' 			=> $result->content,
                'sender_nm' 		=> $result->sender_nm,
                'msg_kind' 			=> $result->msg_kind,
                'reservation_yn'	=> $result->reservation_yn,
                'reservation_date' 	=> $result->reservation_date,
				'rt' 				=> $result->rt,
				'admin_id' 			=> $admin_id,
				'admin_nm' 			=> $admin_nm,
				'user_store_nm' 	=> $user_store_nm
            ];
        }

        return view( Config::get('shop.shop.view') . '/community/comm02_showContent', $values);
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
        $sender_cd = Auth('head')->user()->store_cd;
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
                        'sender_type' => 'S', //본사 : H , 매장 : S
                        'sender_cd' => $sender_cd??$admin_id,
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
					}elseif ($check == "U" || $check == "H") {
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
                        'sender_type' => 'S', //본사 : H, 매장 : S
                        'sender_cd' => $sender_cd,
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
                                    'receiver_type' => "S",
                                    'receiver_cd' => $sc ,
                                    'check_yn' => 'N',
                                    'rt' => now()
                                ]);
                            }
					}elseif ($check == "U" || $check == "H") {
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

        try {
            DB::beginTransaction();

            foreach ($msg_cd as $mc) {
                DB::table('msg_store')
                    ->where('msg_cd','=', $mc)
                    ->update(['del_yn' => 'Y']);
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

    public function update(Request $request)
    {
        $group_nm = $request->input('group_nm');
        $group_cd = $request->input('group_cd');
        $store_cd = $request->input('store_cd');
        $store_cd = explode(',',$store_cd);
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

    public function popup_chk(Request $request)
    {
        $store_cd	= $request->input("store_cd");
		$user_id = Auth('head')->user()->id;

        $sql = "
            select 
                md.msg_cd as msg_cd
            from msg_store_detail md
                left outer join msg_store m on m.msg_cd = md.msg_cd
                left outer join store s on s.store_cd = m.sender_cd
            where (md.receiver_cd = :store_cd or md.receiver_cd = '$user_id' )and md.check_yn = 'N' and (m.reservation_yn = 'N' 
                and (md.rt >= date_add(now(), interval -1 month)) or (m.reservation_yn = 'Y' and (m.reservation_date < now() and m.reservation_date >= date_add(now(), interval -1 month))))
            order by md.rt desc
        ";

        $msgs = DB::select($sql, ['store_cd'=>$store_cd]);

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
		//$sender_type = 'S';
		$store_cds = "";
		$group_cds = "";
		$user_ids = "";

		$sql = "
			select
				m.sender_cd as receiver_cd
			    , concat(mu.name, '(본사)') as user_nm
			    , m.sender_type as receiver_type
			    , m.content
			from msg_store m
			    inner join msg_store_detail md on md.msg_cd = m.msg_cd
				left outer join store s on s.store_cd = md.receiver_cd
				left outer join mgr_user mu on mu.id = m.sender_cd
			where m.msg_cd = :msg_cd
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
		return view(Config::get('shop.shop.view') . '/community/comm02_reply', $values);
	}
	
	// 알리미 재전송을 했을 때 check_yn값이 Y로 업데이트되는 부분
	public function update_check_yn(Request $request)
	{
		$msg_cd = $request->input('msg_cd');
		$user_store = Auth('head')->user()->store_nm;
		$user_id = Auth('head')->user()->id;
		
		try {
			DB::beginTransaction();
			
			//확인여부 변경 부분
			DB::table('msg_store_detail')
				->where('msg_cd', '=', $msg_cd)
				->where('receiver_cd', '=', $user_store)
				->orWhere('receiver_cd', '=', $user_id)
				->update([
					'check_yn' => 'Y'
				]);

			
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
