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

class stk31Controller extends Controller
{
        public function index() {

            $mutable = Carbon::now();
            $sdate	= $mutable->sub(1, 'week')->format('Y-m-d');
    
            $values = [
                'store_types'	=> SLib::getCodes("STORE_TYPE"),
                'sdate' => $sdate,
                'edate' => date("Y-m-d")
            ];
            return view(Config::get('shop.store.view') . '/stock/stk31', $values);
        }

        // 검색
        public function search(Request $request){

            $r = $request->all();
            
            $sdate = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
            $edate = $request->input('edate',date("Ymd"));
            $subject = $request->input('subject');
            $content = $request->input('content');
            $store_no = $request->input('store_no');
            $store_nm = $request->input('store_nm');
            $store_type	= $request->input("store_type");


    
            $where = "";
            $orderby = "";
            if ($subject != "") $where .= " and s.subject like '%" . Lib::quote($subject) . "%' ";
            if ($content != "") $where .= " and s.content like '%" . Lib::quote($content) . "%' ";
            if ($store_no != "") $where .= " and d.store_cd like '%" . Lib::quote($store_no) . "%'  or s.all_store_yn = 'Y'" ;
            if( $store_type != "" )	$where .= " and a.store_type = '$store_type' or s.all_store_yn = 'Y'";

            // ordreby
            $ord = $r['ord'] ?? 'desc';
            $ord_field = $r['ord_field'] ?? "s.rt";
            if($ord_field == 'subject') $ord_field = 's.' . $ord_field;
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
                    s.ut
                from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store a on a.store_cd = d.store_cd
                left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
                where s.rt >= :sdate and s.rt < date_add(:edate,interval 1 day) $where
                group by s.ns_cd
                $orderby
                $limit
                ";

            $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);
    
            return response()->json([
                "code" => 200,
                "head" => array(
                    "total" => count($result)
                ),
                "body" => $result
            ]);

        }

        // 추가
        public function create(Request $request){

            $name =  Auth('head')->user()->name;
            $no = $request->input('ns_cd');
    
            $user = new \stdClass();
            $user->name = $name;
            $user->subject = '';
            $user->content = '';
            $user->ns_cd = $no;
            $user->store_cd = '';
            $user->store_nm = '';
    
            return view( Config::get('shop.store.view') . '/stock/stk31_show',
                ['no' => $no,'user' => $user]
            );
        }

        public function show($no){
            $user = DB::table('notice_store')->where('ns_cd',"=",$no)->first();
            $user->name = $user->admin_nm;

            $storeCode = 
                    "
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
            $storeCodes = DB::select($storeCode);
            
            return view( Config::get('shop.store.view') . '/stock/stk31_show',
                ['no' => $no, 'user' => $user, 'storeCode' => $storeCodes]
            );
        }

        public function store(Request $request){

            $id =  Auth('head')->user()->id;
            $email = Auth('head')->user()->email;

            $ns_cd = $request->input('ns_cd');            
            $subject = $request->input('subject');
            $content = $request->input('content');
            $admin_id = $id;
            $admin_nm = $request->input('name');
            $store_cd = $request->input('store_no','');
            $rt = DB::raw('now()');
            $store_nm = $request->input('store_nm');
            $rt2 = DB::raw('now()');

            if($store_cd == null){
                $all_store_yn = "Y";
            }else{
                $all_store_yn = "N";
            }
  
            try {
                DB::beginTransaction();

                $res = DB::table('notice_store')
                    ->insertGetId([
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
                
                
                if($store_cd !=''){
                    foreach($store_cd as $sc){
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
    
        public function update($no, Request $request){
    
            $id =  Auth('head')->user()->id;
    
            $subject = $request->input('subject');
            $content = $request->input('content');
    
            $notice_store = [
                'subject' => $subject,
                'content' => $content,
                'ut' => DB::raw('now()')
            ];
    
            try {
                DB::transaction(function () use (&$result,$no,$notice_store) {
                    DB::table('notice_store')
                        ->where('ns_cd','=',$no)
                        ->update($notice_store);
                });
                $code = 200;
                $msg = "";
            } catch(Exception $e){
                $code = 500;
                $msg = $e->getMessage();
            }
    
            return response()->json([
                "code" => $code,
                "msg" => $msg
            ]);
        }

        public function del_store($no,$store_cd, Request $request){
            
            $subject = $request->input('subject');
            $content = $request->input('content');
    
            $notice_store = [
                'subject' => $subject,
                'content' => $content,
                'ut' => DB::raw('now()')
            ];

            try {
                DB::transaction(function () use (&$result,$no,$store_cd) {
                    
                    $res = DB::table('notice_store')
                    ->insertGetId([
                        
                    ]);
                
                
                if($store_cd !=''){
                    foreach($store_cd as $sc){
                        DB::table('notice_store_detail')
                            ->insert([
                               
                            ]);
                        }
                }
                });
                $code = 200;
                $msg = "";
            } catch(Exception $e){
                $code = 500;
                $msg = $e->getMessage();
            }
    
            return response()->json([
                "code" => $code,
                "msg" => $msg
            ]);
        
        }

}