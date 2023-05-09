<?php

namespace App\Http\Controllers\shop\community;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class comm01Controller extends Controller
{
    public function index($notice_id, Request $request)
    {
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'week')->format('Y-m-d');

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'store_notice_type' => strval($notice_id),
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view(Config::get('shop.shop.view') . '/community/comm01', $values);
    }

    // 검색
    public function search($notice_id, Request $request)
    {

        $r = $request->all();

        $sdate = $request->input('sdate', Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate = $request->input('edate', date("Ymd"));
        $subject = $request->input('subject', '');
        $content = $request->input('content', '');
        $store_no = Auth('head')->user()->store_cd;
        $store_nm = $request->input('store_nm', '');
        $store_type    = $request->input("store_type", '');

        $where = "";
        $orderby = "";
        if ($subject != "") $where .= " and s.subject like '%" . Lib::quote($subject) . "%' ";
        if ($content != "") $where .= " and s.content like '%" . Lib::quote($content) . "%' ";

        if ($store_no != "") {
            if($notice_id === 'notice') {
                $where .= " and d.store_cd like '%" . Lib::quote($store_no) . "%' ";
            }
        } else {
            $where .= " and s.all_store_yn = 'Y'";
        }
        

        if ($store_type != "") $where .= " and a.store_type = '$store_type' or s.all_store_yn = 'Y'";

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "s.rt";
        if ($ord_field == 'subject') $ord_field = 's.' . $ord_field;
        else $ord_field = 's.' . $ord_field;
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $query = /** @lang text */
            "
            select 
                (select code_val from code where code_kind_cd  = 'STORE_NOTICE_TYPE' and code_id = store_notice_type) as store_notice_type,
                s.ns_cd,
                s.subject,
                s.content,
                s.admin_id,
                s.admin_nm,
                s.admin_email,
                s.cnt,
                s.all_store_yn,
                group_concat(a.store_nm separator ', ') as stores,
                s.rt,
                c.code_val as store_type_nm,
                s.ut,
                (case when ifnull(char_length(s.attach_file_url), 0) > 0 then 'Y' else 'N' end ) as attach_file_yn
            from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store a on a.store_cd = d.store_cd
                left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
            where s.rt >= :sdate and s.rt < date_add(:edate, interval 1 day) 
                and store_notice_type in (
                    select code_id from code c2 where c2.code_kind_cd  = 'STORE_NOTICE_TYPE' and c2.code_val = '$notice_id'
                )
                $where
            group by s.ns_cd
            $orderby
            $limit
        ";

        $result = DB::select($query, ['sdate' => $sdate, 'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show2($no)
    {
        $user = DB::table('notice_store')->where('ns_cd', "=", $no)->first();
        $user->name = $user->admin_nm;
        
        $sql = "
            update notice_store set
                cnt = cnt + 1
            where ns_cd = $no
        ";
        DB::update($sql);

        $sql = "
            select
                d.check_yn,
                d.ns_cd,
                s.ns_cd,
                d.store_cd,
                store.store_nm
            from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store on store.store_cd = d.store_cd
            where s.ns_cd = $no
        ";
        $storeCodes = DB::select($sql);

        $values = [
            'no' => $no,
            'user' => $user,
            'storeCode' => $storeCodes,
        ];

        return view(Config::get('shop.shop.view') . '/community/comm01_show', $values);
    }

    public function show($notice_id, $no)
    {
        $user = DB::table('notice_store')->where('ns_cd', "=", $no)->first();
        $user->name = $user->admin_nm;

        $sql = "
            select
                d.check_yn,
                d.ns_cd,
                s.ns_cd,
                d.store_cd,
                store.store_nm
            from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store on store.store_cd = d.store_cd
            where s.ns_cd = $no
        ";
        $storeCodes = DB::select($sql);

        $values = [
            'no' => $no,
            'user' => $user,
            'storeCode' => $storeCodes,
            'store_notice_type' => $notice_id
        ];

        //읽음 처리
        $store_cd = Auth('head')->user()->store_cd;
        $sql = "select count(*) as cnt from notice_store_detail where ns_cd = '$no' and store_cd = '$store_cd'";
        $cnt = DB::selectOne($sql)->cnt;
        if($cnt > 0) {
            $sql = "
                update notice_store_detail set 
                    check_yn = 'Y', 
                    check_date = now() 
                where ns_cd = '$no' and store_cd = '$store_cd'
            ";
            DB::update($sql);
        } else if($cnt == 0) {
            $sql = "
                select 
                    rt
                from notice_store
                where ns_cd = '$no'
            ";
            $rt = DB::selectOne($sql)->rt;

            $sql = "insert into notice_store_detail (
                      ns_cd, store_cd, check_yn, check_date, rt  
                    ) values (
                        '$no', '$store_cd', 'Y', now(), '$rt'
                    )
            ";
            DB::insert($sql);
        }

        return view(Config::get('shop.shop.view') . '/community/comm01_show', $values);
    }

    public function popup_chk(Request $request)
    {
        $store_cd	= $request->input("store_cd");

        $sql = "
            select
                aa.ns_cd, aa.subject, aa.content
            from
            (
                select
                    ns.ns_cd, ns.subject, ns.content
                from notice_store ns
                inner join notice_store_detail nsd on ns.ns_cd = nsd.ns_cd and nsd.check_yn = 'N'
                where
                    ns.all_store_yn = 'N'
                    and ns.store_notice_type = '01'
                    and ns.rt >= date_add(now(), interval -1 month)
                    and nsd.store_cd = '$store_cd'
                    
                union all
                select
                    ns.ns_cd, ns.subject, ns.content
                from notice_store ns
                left outer join notice_store_detail nsd on ns.ns_cd = nsd.ns_cd and nsd.check_yn = 'Y' and nsd.store_cd = '$store_cd'
                where
                    ns.all_store_yn = 'Y'
                    and ns.store_notice_type = '01'
                    and ns.rt >= date_add(now(), interval -1 month)
                    and nsd.ns_cd is null
            )aa
            order by aa.ns_cd
        ";

        $nos = DB::select($sql);

        return response()->json([
			"code" => 200,
            "nos"  => $nos,
            "cnt"  => count($nos),
		]);
    }

    public function show_notice($no)
    {
        $user = DB::table('notice_store')->where('ns_cd', "=", $no)->first();
        $user->name = $user->admin_nm;
        $store_cd = Auth('head')->user()->store_cd;

        $sql = "
            update notice_store set
                cnt = cnt + 1
            where ns_cd = $no
        ";
        DB::update($sql);

        $sql = "
            select
                count(*) as cnt,
                s.ns_cd as ns_cd,
                s.all_store_yn,
                d.ns_cd as ns_cd2,
                d.check_yn,
                d.store_cd,
                store.store_nm
            from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store on store.store_cd = d.store_cd
            where s.ns_cd = '$no' 
                and ( d.store_cd = '$store_cd' or d.store_cd is null )
        ";
        
        $storeCodes = DB::selectOne($sql);

        if($storeCodes->cnt == 0) {
            $sql = "
                select
                    s.ns_cd as ns_cd,
                    if( d.store_cd = '$store_cd', d.store_cd, '' ) as store_cd
                from notice_store s 
                    left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                    left outer join store on store.store_cd = d.store_cd
                where s.ns_cd = '$no' 
                    and ( d.store_cd <> '$store_cd' or s.all_store_yn = 'Y' )
            ";
            
            $storeCodes = DB::selectOne($sql);
        }

        $values = [
            'no' => $no,
            'user' => $user,
            'storeCode' => $storeCodes,
        ];  

        return view(Config::get('shop.shop.view') . '/community/comm01_show_pop', $values);
    }

    public function notice_read(Request $request)
    {
        $ns_cd = $request->input('ns_cd');
        $store_cd = $request->input('store_cd', '');

        if($store_cd == '') {
            $sql = "
                select 
                    rt
                from notice_store
                where ns_cd = :ns_cd
            ";
            $rt = DB::selectOne($sql, ['ns_cd' => $ns_cd]);

            $notice_store_detail = [
                'ns_cd' => $ns_cd,
                'store_cd' => Auth('head')->user()->store_cd,
                'check_yn' => 'Y',
                'check_date' => now(),
                'rt' => $rt->rt
            ];
        } else {
            $notice_store_detail = [
                'check_yn' => 'Y',
                'check_date' => now()
            ];
        }

        try {
            DB::beginTransaction();
            if($store_cd == '') {
                DB::table('notice_store_detail')
                    ->insert($notice_store_detail);
            } else {
                DB::table('notice_store_detail')
                    ->where([
                        ['ns_cd', '=', $ns_cd],
                        ['store_cd', '=', $store_cd]
                    ])
                    ->update($notice_store_detail);
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