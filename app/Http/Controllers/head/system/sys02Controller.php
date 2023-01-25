<?php

namespace App\Http\Controllers\head\system;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use PDO;

class sys02Controller extends Controller
{
    public function index()
    {
        $sql =
            /** @lang text */
            "
            select * from mgr_controller a
			where entry = 1 and is_del = 0 and state >= 0
			order by seq
            ";

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pmenu = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $pmenu[] = $row;
        }

        $values = [
            "pmenus" => $pmenu
        ];

        return view(Config::get('shop.head.view') . '/system/sys02', $values);
    }

    public function create()
    {
        $menu = new \stdClass();
        $menu->kind = "P";
        $menu->state = "0";
        $menu->sys_menu = "N";

        $sql =
            /** @lang text */
            "
            select * from mgr_controller a
			where entry = 1 and is_del = 0 and state >= 0
			order by seq
            ";

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $pmenu = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $pmenu[] = $row;

            $sql = /** @lang text */
                "
                select *
                from mgr_controller a
                where entry = :entry and kind = 'M' order by seq            
            ";
            $stmt2 = $pdo->prepare($sql);
            $stmt2->execute(["entry" => $row["menu_no"]]);
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
            {
                $pmenu[] = $row2;
            }
        }

        $values = [
            'code' => '',
            'menu' => $menu,
            'pmenus' => $pmenu
        ];
        return view(Config::get('shop.head.view') . '/system/sys02_show', $values);
    }

    public function show($code)
    {

        $sql =
            /** @lang text */
            "
            select * from mgr_controller 
			where menu_no = :code
            ";

        $menu = DB::selectOne($sql, array("code" => $code));

        $sql =
            /** @lang text */
            "
            select * from mgr_controller a
			where entry = ( select entry from mgr_controller where menu_no = :entry ) and is_del = 0 and state >= 0
			order by seq
            ";

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["entry" => $menu->entry]);
        $pmenu = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $pmenu[] = $row;

            $sql = /** @lang text */
                "
                select *
                from mgr_controller a
                where entry = :entry and kind = 'M' order by seq            
            ";
            $stmt2 = $pdo->prepare($sql);
            $stmt2->execute(["entry" => $row["menu_no"]]);
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
            {
                $pmenu[] = $row2;
            }
        }

        $values = [
            'code' => $code,
            'menu' => $menu,
            'pmenus' => $pmenu
        ];

        return view(Config::get('shop.head.view') . '/system/sys02_show', $values);
    }

    public function search(Request $req)
    {

        $kor_nm        = $req->input('kor_nm', '');
        $eng_nm        = $req->input('eng_nm', '');
        $is_del        = $req->input('is_del', '');
        $state        = $req->input('state', '');
        $menu_no        = $req->input('menu_no', '');

        $menu_no_where = "";
        $where = "";

        if ($menu_no != "")    $menu_no_where .= " and menu_no     = '" . Lib::quote($menu_no) . "' ";

        if ($kor_nm != "")        $where .= " and kor_nm like '%" . Lib::quote($kor_nm) . "%' ";
        if ($eng_nm != "")        $where .= " and eng_nm like '%" . Lib::quote($eng_nm) . "%' ";
        if ($is_del != "")    $where .= " and is_del     = '" . Lib::quote($is_del) . "' ";
        if ($state != ""){
            if($state === "+0"){
                $where .= " and state >= 0 ";
            } else {
                $where .= " and state = '" . Lib::quote($state) . "' ";
            }
        }

        $sql =
            /** @lang text */
            "
            select * from mgr_controller 
			where entry = 1 $menu_no_where $where order by seq
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $menu = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $menu[] = $row;

            $sql = /** @lang text */
                "
                select a.*, ( select count(*) from mgr_controller where entry = a.menu_no ) as cnt
                from mgr_controller a
                where entry = :entry $where order by seq            
            ";
            $stmt2 = $pdo->prepare($sql);
            $stmt2->execute(["entry" => $row["menu_no"]]);
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
            {
                $menu[] = $row2;

                if($row2["cnt"] > 0){
                    $sql = /** @lang text */
                            "
                        select * from mgr_controller 
                        where entry = :entry $where order by seq            
                    ";
                    $stmt3 = $pdo->prepare($sql);
                    $stmt3->execute(["entry" => $row2["menu_no"]]);
                    while($row3 = $stmt3->fetch(PDO::FETCH_ASSOC))
                    {
                        $menu[] = $row3;
                    }
                }
            }
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($menu)
            ),
            "body" => $menu
        ]);
    }

    public function store(Request $request)
    {

        $entry = $request->input('entry');
        $pid = $request->input('pid');
        $kor_nm = $request->input('kor_nm');
        $eng_nm = $request->input('eng_nm');
        $kind = $request->input('kind');
        $action = $request->input('action');
        $sys_menu = $request->input('sys_menu');
        $state = $request->input('state');

        $id = Auth::guard('head')->user()->id;

        if ($entry != null) {
            if ($entry == 'entry_menu') {
                $entry = 1;
                $lev = 1;
            } else {
                $sql = "
                    select
                        *
                    from mgr_controller
                    where menu_no = '$entry'
                ";
                $result = DB::selectOne($sql);
                $lev = $result->lev + 1;
            }
           
        } else {
            $entry = 1;
            $lev = 0;
        }


        $user_cnt = DB::table('mgr_controller')
            ->where('pid', $pid)->count();
        if ($user_cnt == 0) {

            $mgr_controller = [
                'entry' => $entry,
                'pid' => $pid,
                'kor_nm' => $kor_nm,
                'eng_nm' => $eng_nm,
                'kind' => $kind,
                'action' => $action,
                'sys_menu' => $sys_menu,
                'lev' => $lev,
                'state' => $state,
                'id' => $id,
                'regi_date' => DB::raw('now()'),
                'ut' => DB::raw('now()'),
                'is_del' => 'Y',
            ];

            try {
                DB::transaction(function () use (&$result, $mgr_controller) {
                    DB::table('mgr_controller')->insert($mgr_controller);
                });
                $code = 200;
                $msg = "";
            } catch (Exception $e) {
                $code = 500;
                $msg = $e->getMessage();
            }
        } else {
            $code = 501;
            $msg = 'id dup..';
        }

        return response()->json(['code' => $code, 'msg' => $msg]);
    }

    public function update($code, Request $request)
    {

        $entry = $request->input('entry');
        $pid = $request->input('pid');
        $kor_nm = $request->input('kor_nm');
        $eng_nm = $request->input('eng_nm');
        $kind = $request->input('kind');
        $action = $request->input('action');
        $sys_menu = $request->input('sys_menu');
        $state = $request->input('state');
        $roles = (object)json_decode($request->input('roles'));

        $id = Auth::guard('head')->user()->id;

        if ($entry == 'entry_menu') {
            $entry = 1;
            $lev = 1;
        } else {
            $sql = "
                select
                    *
                from mgr_controller
                where menu_no = '$entry'
            ";
    
            $result = DB::selectOne($sql);
    
            $lev = $result->lev + 1;
        }


        $mgr_controller = [
            'entry' => $entry,
            'pid' => $pid,
            'kor_nm' => $kor_nm,
            'eng_nm' => $eng_nm,
            'kind' => $kind,
            'action' => $action,
            'sys_menu' => $sys_menu,
            'lev' => $lev,
            'state' => $state,
            'id' => $id,
            'regi_date' => DB::raw('now()'),
            'ut' => DB::raw('now()'),
            'is_del' => 'Y',
        ];

        try {
            DB::transaction(function () use (&$result, $code, $mgr_controller, $roles) {
                DB::table('mgr_controller')
                    ->where('menu_no', '=', $code)
                    ->update($mgr_controller);

                foreach ($roles as $group_no => $group_role) {
                    DB::table('mgr_group_menu_role')
                        ->where('menu_no', '=', $code)
                        ->where('group_no', '=', $group_no)
                        ->delete();

                    if ($group_role == "1") {
                        DB::table('mgr_group_menu_role')
                            ->insert([
                                'menu_no' => $code,
                                'group_no' => $group_no,
                                'role' => 1,
                            ]);
                    }
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code, 'msg' => $msg]);
    }

    public function delete($code, Request $req)
    {
        try {
            DB::transaction(function () use (&$result, $code) {
                DB::table('mgr_controller')->where('menu_no', $code)->delete();
            });

            DB::transaction(function () use (&$result, $code) {
                DB::table('mgr_group_menu_role')->where('menu_no', $code)->delete();
            });

            $code = 200;
        } catch (Exception $e) {
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function role_search($code)
    {

        $sql =
            /** @lang text */
            "
            select 
            g.`group_no`,g.`group_nm`,ifnull(r.`role`,0) as role
            from mgr_group g left outer join mgr_group_menu_role r on g.group_no = r.group_no and r.`menu_no` = :code    
        ";

        $rows = DB::select($sql, array("code" => $code));

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }
}
