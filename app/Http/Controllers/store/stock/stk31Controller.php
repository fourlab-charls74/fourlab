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
                'sdate' => $sdate,
                'edate' => date("Y-m-d")
            ];
            return view(Config::get('shop.store.view') . '/stock/stk31', $values);
        }

        // 검색
        public function search(Request $request){

            $sdate = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
            $edate = $request->input('edate',date("Ymd"));
            $subject = $request->input('subject');
            $content = $request->input('content');
    
            $where = "";
            if ($subject != "") $where .= " and subject like '%" . Lib::quote($subject) . "%' ";
            if ($content != "") $where .= " and content like '%" . Lib::quote($content) . "%' ";
    
            $query = /** @lang text */
                "
                select *
                from notice_store
                where rt >= :sdate and rt < date_add(:edate,interval 1 day) $where
                order by rt desc
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
    
            return view( Config::get('shop.store.view') . '/stock/stk31_show',
                ['no' => $no,'user' => $user]
            );
        }

        public function show($no){
            $user = DB::table('notice_store')->where('ns_cd',"=",$no)->first();
            $user->name = $user->admin_nm;
            return view( Config::get('shop.store.view') . '/stock/stk31_show',
                ['no' => $no, 'user' => $user]
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
    
            $notice_store = [
                'ns_cd' => $ns_cd,
                'subject' => $subject,
                'content' => $content,
                'admin_id' => $admin_id,
                'admin_nm' => $admin_nm,
                'admin_email' => $email,
                'sc_state' => 'Y',
                'cnt' => 0,
                'rt' => DB::raw('now()')
            ];
    
            try {
                DB::transaction(function () use (&$result,$notice_store) {
                    DB::table('notice_store')->insert($notice_store);
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
}