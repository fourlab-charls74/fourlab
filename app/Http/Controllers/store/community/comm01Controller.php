<?php

namespace App\Http\Controllers\store\community;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use GuzzleHttp;
use App\Models\Conf;
use Illuminate\Support\Facades\Storage;

class comm01Controller extends Controller
{
    public function index($notice_id, Request $request)
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

        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'week')->format('Y-m-d');

        $values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'store_notice_type' => strval($notice_id),
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'store_channel'	=> $store_channel,
			'store_kind'	=> $store_kind
        ];
        return view(Config::get('shop.store.view') . '/community/comm01', $values);
    }

    // 검색
    public function search($notice_id, Request $request)
    {

        $r = $request->all();

        $sdate = $request->input('sdate', Carbon::now()->sub(3, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $subject = $request->input('subject', '');
        $content = $request->input('content', '');
        $store_no = $request->input('store_no', '');
        $store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

        $where = "";
        $orderby = "";
        if ($subject != "") $where .= " and s.subject like '%" . Lib::quote($subject) . "%' ";
        if ($content != "") $where .= " and s.content like '%" . Lib::quote($content) . "%' ";
        if ($store_no != "") $where .= " and d.store_cd like '%" . Lib::quote($store_no) . "%' ";
        if ($store_channel != "") $where .= " and a.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= " and a.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "s.rt";
        if ($ord_field == 'subject') $ord_field = $ord_field;
        else $ord_field = $ord_field;
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
                    select code_id from code c2 where  c2.code_kind_cd  = 'STORE_NOTICE_TYPE' and c2.code_val = '$notice_id'
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

    // 추가
    public function create($notice_id, Request $request)
    {

        $name =  Auth('head')->user()->name;
        $no = $request->input('ns_cd');

        $user = new \stdClass();
        $user->name = $name;
        $user->subject = '';
        $user->content = '';
        $user->ns_cd = $no;
        $user->store_cd = '';
        $user->store_nm = '';
        $user->attach_file_url = '';

        $values = ['no' => $no, 'user' => $user, 'store_notice_type' => $notice_id];

        return view(Config::get('shop.store.view') . '/community/comm01_show', $values);
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

        return view(Config::get('shop.store.view') . '/community/comm01_show', $values);
    }

    public function store(Request $request)
    {
        $now = date('YmdHis');
        $excel_extensions = config::get('file.excel_extensions');
        $ppt_extionsions = config::get('file.ppt_extensions');
        $image_extionsions = config::get('file.image_extensions');
        
        $this->validate($request, [
            'files.*' => 'required|mimes:'.strtolower(implode(',', $excel_extensions)).strtolower(implode(',', $ppt_extionsions).strtolower(implode(',', $image_extionsions)))
        ]);

        $id =  Auth('head')->user()->id;
        $email = Auth('head')->user()->email;
        $ns_cd = $request->input('ns_cd');
        $subject = $request->input('subject');
        $content = $request->input('content');
        $admin_id = $id;
        $admin_nm = $request->input('name');
        $store_cd = explode(',', $request->input('store_no', ''));
        $store_notice_type = $request->input('store_notice_type', '') === 'vmd' ? '02' : '01';
        $rt = DB::raw('now()');
        $store_nm = $request->input('store_nm');
        $rt2 = DB::raw('now()');

        $files = $request->file('files');
        $file_url = null;

        if ($store_cd == null) {
            $all_store_yn = "Y";
        } else {
            $all_store_yn = "N";
        }

        try {

            //엑셀 및 ppt, image 업로드 
            if (count($_FILES) > 0) {
                $url_array = [];
                foreach($files as $file) {
                    $file_name = "$now"."$id"."_".uniqid().".".$file->extension();
                    $save_path = config::get('file.store_notice_path');
                    $url_array[] = ULib::uploadFile($save_path, $file_name, $file);
                }
                $file_url = implode(',', $url_array);
            }

            DB::beginTransaction();

            $res = DB::table('notice_store')
                ->insertGetId([
                    'store_notice_type' => $store_notice_type,
                    'attach_file_url' => $file_url,
                    'ns_cd' => $ns_cd,
                    'subject' => $subject,
                    'content' => $content,
                    'admin_id' => $admin_id,
                    'admin_nm' => $admin_nm,
                    'admin_email' => $email,
                    'all_store_yn' => $all_store_yn,
                    'cnt' => 0,
                    'rt' => $rt
                ]);

            if (count($store_cd) > 0) {
                foreach ($store_cd as $sc) {
                    DB::table('notice_store_detail')
                        ->insert([
                            'ns_cd' => $res,
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

    public function update($no, Request $request)
    {

        $id =  Auth('head')->user()->id;
        $now = date('YmdHis');

        $subject = $request->input('subject');
        $content = $request->input('content');
        $store_cd = explode(',', $request->input('store_no', ''));
        $files = $request->file('files');
        $file_url = null;

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
            //엑셀 및 ppt, image 업로드 
            if (count($_FILES) > 0) {
                $url_array = [];
                foreach($files as $file) {
                    $file_name = "$now"."$id"."_".uniqid().".".$file->extension();
                    $save_path = config::get('file.store_notice_path');
                    $url_array[] = ULib::uploadFile($save_path, $file_name, $file);
                }
                $file_url = implode(',', $url_array);
            }

            DB::beginTransaction();
            
            $sql = "
                update notice_store
                set 
                    subject = '$subject', 
                    content = '$content',
                    all_store_yn =  '$all_store_yn',
                    ut = $ut,
                    attach_file_url = (case when '$file_url' != '' then CONCAT(attach_file_url, ',' , '$file_url') else attach_file_url end)
                where 
                    ns_cd = $ns_cd
            ";

            DB::update($sql);
            
            if (count($store_cd) > 0) {
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

    public function download_file($path) {

        if (file_exists(storage_path('\\app\\public\\data\\community\\comm01\\'.$path))) {

            try{
                return response()->download(public_path('\\data\\community\\comm01\\'.$path));
            } catch(Exception $e){
                return response()->json([
                    "code" => '500',
                    "msg" => $e->getMessage()
                ]);        
            }
        }

        return response()->json([
            "code" => '400',
            "msg" => 'file not found'
        ]);
    }

    public function delete_file($no, $path) {

        $file_path = storage_path('\\app\\public\\data\\community\\comm01\\'.$path);
        $delete_file_url = 'data/community/comm01/'.$path;

        if (file_exists($file_path)) {

            try{
                DB::beginTransaction();
                storage::delete($file_path);
                
                $sql = "
                    update notice_store s
                    set s.attach_file_url = (
                        select 
                            GROUP_CONCAT(files)
                        from (
                            select 
                                substring_index(substring_index(ns.attach_file_url, ',', fileCnt.n), ',' , -1) as files
                            from (
                                select 1 as n union all 
                                select 2 union all
                                select 3 union all 
                                select 4 union all
                                select 5
                            ) as fileCnt
                            inner join notice_store ns on char_length(ns.attach_file_url) - char_length(replace(ns.attach_file_url, ',' , '')) >= fileCnt.n - 1
                            where 
                                ns.ns_cd  = $no
                        ) list
                        where 
                            list.files not in (
                                '$delete_file_url'
                            )
                    )
                    where 
                        s.ns_cd = $no
                ";

                DB::update($sql);
                DB::commit();

                return response()->json([
                    "code" => '200',
                    "msg" => 'file success deleted'
                ]);

            } catch(Exception $e){
                DB::rollBack();

                return response()->json([
                    "code" => '500',
                    "msg" => $e->getMessage()
                ]);        
            }
        }

        return response()->json([
            "code" => '400',
            "msg" => 'file not found'
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
