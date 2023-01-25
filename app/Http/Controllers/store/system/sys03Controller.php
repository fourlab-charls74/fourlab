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

        $query = "
            select 
                group_no, wonga_yn, other_store_yn, release_price_yn, pos_use_yn
            from store_group_authority
            where group_no = $code
        ";

        $store_group_authority = DB::selectOne($query);

        $values = [
            'code' => $code,
            'mgr_group' => $user,
            'store_group_authority' => $store_group_authority,
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
        $wonga_yn = $request->input('wonga_yn');
        $other_store_yn = $request->input('other_store_yn');
        $release_price_yn = $request->input('release_price_yn');
        $pos_use_yn = $request->input('pos_use_yn');

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
                DB::beginTransaction();

                $groupno = DB::table('mgr_group')->insertGetId($mgr_group);

                DB::table('store_group_authority')->insert([
                    'group_no' => $groupno,
                    'wonga_yn' => $wonga_yn,
                    'other_store_yn' => $other_store_yn,
                    'release_price_yn' => $release_price_yn,
                    'pos_use_yn' => $pos_use_yn
                ]);

                DB::commit();
                $code = 200;
                $msg = "";
            } catch (Exception $e) {
                DB::rollBack();
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
        $wonga_yn = $request->input('wonga_yn');
        $other_store_yn = $request->input('other_store_yn');
        $release_price_yn = $request->input('release_price_yn');
        $pos_use_yn = $request->input('pos_use_yn');


        $admin_id = Auth::guard('head')->user()->id;

        $store_group_authority = [
            'group_no' => $code,
            'wonga_yn' => $wonga_yn,
            'other_store_yn' => $other_store_yn,
            'release_price_yn' => $release_price_yn,
            'pos_use_yn' => $pos_use_yn
        ];

        $store_group_authority_update = [
            'wonga_yn' => $wonga_yn,
            'other_store_yn' => $other_store_yn,
            'release_price_yn' => $release_price_yn,
            'pos_use_yn' => $pos_use_yn
        ];


        $mgr_group = [
            'group_nm' => $group_nm,
            'group_nm_eng' => $group_nm_eng,
            'id' => $admin_id,
            'regi_date' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $code, $mgr_group, $roles, $store_group_authority,$store_group_authority_update) {
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

                $sql = "
                    select
                        group_no
                    from store_group_authority
                    where group_no = $code
                ";

                $res = DB::select($sql);

                // dd($res);

                if ($res == null) {
                    
                    DB::table('store_group_authority')->insert($store_group_authority);
                } else {
                    DB::table('store_group_authority')
                        ->where('group_no', '=', $code)
                        ->update($store_group_authority_update);
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

}
