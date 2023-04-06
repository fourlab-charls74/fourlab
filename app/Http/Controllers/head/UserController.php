<?php

namespace App\Http\Controllers\head;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Head;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PDO;

class UserController extends Controller
{
    //
    public function index() {
        $user =  Auth::guard('head')->user();
        $id = $user->id;

        //echo $id;
        //exit;
        return view( Config::get('shop.head.view') . '/auth/user',
            ['id' => $id, 'user' => $user]
        );

    }

    public function store(Request $request){

        $id =  Auth::guard('head')->user()->id;

        $passwd = $request->input('passwd');
        $passwd_chg = $request->input('passwd_chg');
        $name = $request->input('name');
        $part = $request->input('part');
        $posi = $request->input('posi');
        $email = $request->input('email');
        $tel = $request->input('tel', '');
        $exttel = $request->input('exttel', '');

        $user = [
            'name' => $name,
            'part' => $part,
            'posi' => $posi,
            'email' => $email,
            'tel'   => $tel,
            'exttel'   => $exttel,
        ];

        if($passwd_chg == "Y"){
            $user["passwd"] = DB::raw("password('$passwd')");
            $user["pwchgdate"] = DB::raw('now()');
        }

        $profile_url = $request->input("file_url",'');
        if($profile_url != ""){

            $image = preg_replace('/data:image\/(.*?);base64,/', '', $profile_url);
            preg_match('/data:image\/(.*?);base64,/', $profile_url, $matches, PREG_OFFSET_CAPTURE);
            //print_r($matches);
            $ext = $matches[1][0];

            $base_path = "/images/profile_img";

            /* 이미지를 저장할 경로 폴더가 없다면 생성 */
            if(!Storage::disk('public')->exists($base_path)){
                //Storage::disk('public')->makeDirectory($save_path);
                Storage::disk('public')->makeDirectory($base_path);
            }

            $file_name = sprintf("%s.%s", $id,$ext);
            //$save_file = sprintf("%s/%s", $save_path, $file_name);
            $save_file = sprintf("%s/%s", $base_path, $file_name);
            //$logo_img_url = sprintf("%s/%s", $brand, $file_name);
            $profile_img_url = sprintf("%s/%s", $base_path, $file_name);

            //$profile_img_info =  file_get_contents($profile_img);
            //Storage::disk('public')->putFileAs($base_path, $profile_img, $file_name);
            Storage::disk('public')->put($save_file, base64_decode($image));
            $user["profile_img"] = $profile_img_url;
        }

//        if($request->hasFile("file")){
//
//            $profile_img = $request->file("file");
//            print_r($profile_img);
//
//            $base_path = "/images/profile_img/";
//
//            /* 이미지를 저장할 경로 폴더가 없다면 생성 */
//            if(!Storage::disk('public')->exists($base_path)){
//                //Storage::disk('public')->makeDirectory($save_path);
//                Storage::disk('public')->makeDirectory($base_path);
//            }
//
//            $ext = $profile_img->extension();
//            $file_name = sprintf("%s.%s", $id,$ext);
//            //$save_file = sprintf("%s/%s", $save_path, $file_name);
//            $save_file = sprintf("%s/%s", $base_path, $file_name);
//            //$logo_img_url = sprintf("%s/%s", $brand, $file_name);
//            $profile_img_url = sprintf("%s/%s", $base_path, $file_name);
//
//            //$profile_img_info =  file_get_contents($profile_img);
//            Storage::disk('public')->putFileAs($base_path, $profile_img, $file_name);
//
//            $user["profile_img"] = $profile_img_url;
//        }


        try {
            DB::transaction(function () use (&$result, $id, $user) {
                DB::table('mgr_user')
                    ->where('id','=', $id)
                    ->update($user);
            });
            $code = 200;
            $msg = '';
        } catch(Exception $e){
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code,'msg' => $msg]);

    }

    public function log(){

        $sdate	= now()->sub(1, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];
        return view( Config::get('shop.head.view') . '/auth/log',$values);
    }

    public function searchlog(Request $request){

        $id = Auth('head')->user()->id;

        $sd	= now()->sub(1, 'month')->format('Y-m-d');
        $ed	= date("Y-m-d");

        $menu_nm = $request->input('menu_nm');
        $page       = $request->input("page",1);
        $page_size  = $request->input("limit", 100);
        $sdate  = $request->input("sdate",$sd );
        $edate  = $request->input("edate",$ed );

        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($menu_nm != "") $where .= " and menu_nm like '%" . Lib::quote($menu_nm) . "%' ";

        $total      = 0;
        $page_cnt   = 0;

        if ($page == 1)
        {
            // 갯수 얻기
            $sql = /** @lang text */
                "
                select count(*) as total from mgr_log 
                where id = :id and log_time >= :sdate and log_time < date_add(:edate,INTERVAL 1 day) $where
			";
            $row = DB::selectOne($sql, array(
                "id" => $id,
                "sdate" => $sdate,
                "edate" => $edate,

            ));
            $total = $row->total;

            $page_cnt   = (int)(($total-1)/$page_size) + 1;
            $startno    = ($page-1) * $page_size;

        } else {
            $startno = ($page-1) * $page_size;
        }

        $limit = " limit $startno, $page_size ";

        $sql =
            /** @lang text */
            "
            select * from mgr_log 
			where id = :id and log_time >= :sdate and log_time < date_add(:edate,INTERVAL 1 day) $where
			order by log_time desc $limit
        ";

        $rows = DB::select($sql,array(
            "id" => $id,
            "sdate" => $sdate,
            "edate" => $edate,
        ));
        // print_r ($rows[1]);
        //echo ($total/10);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt
            ),
            "body" => $rows
        ]);
    }

    public function menu(){
        $id =  Auth::guard('head')->user()->id;
        $mgr_group_sql = "
            select group_no from mgr_user_group mug where id = '$id';
        ";

        $group_id = DB::selectOne($mgr_group_sql);
        $group_no = $group_id->group_no;

        $query = /** @lang text */
        "
            select * from mgr_controller 
            where is_del = 0 and state >= 0
        ";

        
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $menu = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $menu[strtolower($row["pid"])] = $row["action"];
        }
        return response()->json(['code' => 200,'menu' => $menu]);
    }

}

