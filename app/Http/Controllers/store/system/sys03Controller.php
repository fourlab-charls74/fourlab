<?php

namespace App\Http\Controllers\store\system;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sys03Controller extends Controller
{
    public function index()
    {

        $values = [
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
            'types' => SLib::getCodes('G_AD_TYPE'),
            'states' => SLib::getCodes('IS_SHOW')
        ];
        return view(Config::get('shop.store.view') . '/system/sys03', $values);
    }

    public function create()
    {

        $values = [
            'code' => '',
        ];
        return view(Config::get('shop.store.view') . '/system/sys03_show', $values);
    }

    public function show($code)
    {

        $sql =
            /** @lang text */
            "
            select * from mgr_group 
			where group_no = :code
            ";
        $user = DB::selectOne($sql, array("code" => $code));

        $values = [
            'code' => $code,
            'mgr_group' => $user,
        ];

        return view(Config::get('shop.store.view') . '/system/sys03_show', $values);
    }

    public function search(Request $req)
    {

        $group_no        = $req->input('group_no', '');
        $group_nm        = $req->input('group_nm', '');
        $group_nm_eng        = $req->input('group_nm_eng', '');

        $where = "";

        if ($group_no != "")        $where .= " and group_no like '%" . Lib::quote($group_no) . "%' ";
        if ($group_nm != "")        $where .= " and group_nm like '%" . Lib::quote($group_nm) . "%' ";
        if ($group_nm_eng != "")        $where .= " and group_nm_eng like '%" . Lib::quote($group_nm_eng) . "%' ";

        $sql =
            /** @lang text */
            "
            select * from mgr_group 
			where 1=1 $where
        ";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function store(Request $request)
    {
        $group_no = $request->input('group_no');
        $group_nm = $request->input('group_nm');
        $group_nm_eng = $request->input('group_nm_eng');

        $id = Auth::guard('head')->user()->id;

        $user_cnt = DB::table('mgr_group')
            ->where('group_nm', $group_nm)->count();
        if ($user_cnt == 0) {

            $mgr_group = [
                'group_no' => $group_no,
                'group_nm' => $group_nm,
                'group_nm_eng' => $group_nm_eng,
                'id' => $id,
                'regi_date' => DB::raw('now()'),
            ];

            try {
                DB::transaction(function () use (&$result, $mgr_group) {
                    DB::table('mgr_group')->insert($mgr_group);
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
        $group_nm = $request->input('group_nm');
        $group_nm_eng = $request->input('group_nm_eng');
        $roles = (object)json_decode($request->input('roles'));

        $admin_id = Auth::guard('head')->user()->id;

        $mgr_group = [
            'group_nm' => $group_nm,
            'group_nm_eng' => $group_nm_eng,
            'id' => $admin_id,
            'regi_date' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $code, $mgr_group, $roles) {
                DB::table('mgr_group')
                    ->where('group_no', '=', $code)
                    ->update($mgr_group);

                foreach ($roles as $id => $group_role) {
                    DB::table('mgr_user_group')
                        ->where('group_no', '=', $code)
                        ->where('id', '=', $id)
                        ->delete();

                    if ($group_role == "1") {
                        DB::table('mgr_user_group')
                            ->insert([
                                'group_no' => $code,
                                'id' => $id
                            ]);
                    }
                }
            });
            $code = 200;
            $msg = '';
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
                DB::table('mgr_group')->where('group_no', $code)->delete();
            });

            DB::transaction(function () use (&$result, $code) {
                DB::table('mgr_user_group')->where('group_no', $code)->delete();
            });

            $code = 200;
        } catch (Exception $e) {
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function user_search($code, Request $req)
    {

        $sql =
            /** @lang text */
            "
            select 
                g.id, g.`name`, g.`part`, g.`posi`,
                if(ifnull(r.id,'') <> '',1,0) as role
            from mgr_user g left outer join mgr_user_group r on g.`id` = r.`id` and r.`group_no` = :code
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


    public function menu_search($code, Request $req)
    {

        $sql =
            /** @lang text */
            "
            select
                pid, kor_nm
            from mgr_controller
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
