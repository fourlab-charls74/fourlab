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

class sys01Controller extends Controller
{
    public function index()
    {

        $values = [
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
            'types' => SLib::getCodes('G_AD_TYPE'),
            'states' => SLib::getCodes('IS_SHOW')
        ];
        return view(Config::get('shop.head.view') . '/system/sys01', $values);
    }

    public function create()
    {

        $user = (object)[
            'id' => '',
            'passwd' => '',
            'grade' => 'U',
            'iptype' => 'A'
        ];

        $values = [
            'code' => '',
            'user' => $user
        ];
        return view(Config::get('shop.head.view') . '/system/sys01_show', $values);
    }

    public function show($code)
    {

        $sql =
            /** @lang text */
            "
            select * from mgr_user 
			where id = :code
            ";
        $user = DB::selectOne($sql, array("code" => $code));

        $values = [
            'code' => $code,
            'user' => $user,
        ];

        return view(Config::get('shop.head.view') . '/system/sys01_show', $values);
    }

    public function search(Request $req)
    {

        $name        = $req->input('name', '');
        $part        = $req->input('part', '');

        $where = "";

        if ($name != "")        $where .= " and name like '%" . Lib::quote($name) . "%' ";
        if ($part != "")        $where .= " and part like '%" . Lib::quote($part) . "%' ";

        $sql =
			/** @lang text */
            "
            select * from mgr_user 
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

		$grade			= $request->input('grade');
		$id				= $request->input('id');
		$passwd			= $request->input('passwd');
		$pwchgperiod	= $request->input('pwchgperiod');
		$name			= $request->input('name');
		$ipfrom			= $request->input('ipfrom');
		$ipto			= $request->input('ipto');
		$md_yn			= $request->input('md_yn');
		$use_yn			= $request->input('use_yn');
		$part			= $request->input('part');
		$posi			= $request->input('posi');
		$tel			= $request->input('tel');
		$exttel			= $request->input('exttel');
		$messenger		= $request->input('messenger');
		$email			= $request->input('email');

		$user_cnt	= DB::table('mgr_user')
						->where('id', $id)->count();

		if( $user_cnt == 0 ){

			$mgr_user = [
				'grade' => $grade,
				'id' => $id,
				'passwd' => DB::raw("CONCAT('*', UPPER(SHA1(UNHEX(SHA1('$passwd')))))"),
				'pwchgperiod' => $pwchgperiod,
				'name' => $name,
				'ipfrom' => $ipfrom,
				'ipto' => $ipto,
				'md_yn' => $md_yn,
				'use_yn' => $use_yn,
				'part' => $part,
				'posi' => $posi,
				'tel' => $tel,
				'exttel' => $exttel,
				'messenger' => $messenger,
				'email' => $email,
				'pwchgdate' => now(),
			];

			try {
				DB::transaction(function () use (&$result, $mgr_user) {
					DB::table('mgr_user')->insert($mgr_user);
				});
				$code = 200;
				$msg = "";
			} catch (Exception $e) {
				$code = 500;
				$msg = $e->getMessage();
			}
		} else {
			$code = 501;
			$msg = '중복된 아이디가 존재합니다.';
		}

		return response()->json(['code' => $code, 'msg' => $msg]);
	}

	public function update($code, Request $request)
	{
		$grade		= $request->input('grade');
		$id			= $request->input('id');
		$passwd		= $request->input('passwd');
		$pwchgperiod	= $request->input('pwchgperiod');
		$name		= $request->input('name');
		$ipfrom		= $request->input('ipfrom');
		$ipto		= $request->input('ipto');
		$md_yn		= $request->input('md_yn');
		$use_yn		= $request->input('use_yn');
		$part		= $request->input('part');
		$posi		= $request->input('posi');
		$tel		= $request->input('tel');
		$exttel		= $request->input('exttel');
		$messenger	= $request->input('messenger');
		$email		= $request->input('email');
		$roles		= (object)json_decode($request->input('roles'));
		$passwd_chg	= $request->input('passwd_chg');

		$mgr_user = [
			'grade'			=> $grade,
			'id'			=> $id,
			'pwchgperiod'	=> $pwchgperiod,
			'name'			=> $name,
			'ipfrom'		=> $ipfrom,
			'ipto'			=> $ipto,
			'md_yn'			=> $md_yn,
			'use_yn'		=> $use_yn,
			'part'			=> $part,
			'posi'			=> $posi,
			'tel'			=> $tel,
			'exttel'		=> $exttel,
			'messenger'		=> $messenger,
			'email'			=> $email,
			'pwchgdate'		=> DB::raw('now()')
		];

		if( $passwd_chg == "Y" ){
			$mgr_user	= array_merge($mgr_user,[
				'passwd'	=> DB::raw("CONCAT('*', UPPER(SHA1(UNHEX(SHA1('$passwd')))))")
			]);
		}

		try {
			DB::transaction(function () use (&$result, $code, $mgr_user, $roles) {
				DB::table('mgr_user')
					->where('id', '=', $code)
					->update($mgr_user);

				foreach( $roles as $group_no => $group_role ) {
					DB::table('mgr_user_group')
						->where('id', '=', $code)
						->where('group_no', '=', $group_no)
						->delete();

					if ($group_role == "1") {
						DB::table('mgr_user_group')
							->insert([
								'id'		=> $code,
								'group_no'	=> $group_no
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
                DB::table('mgr_user')->where('id', $code)->delete();
            });

            DB::transaction(function () use (&$result, $code) {
                DB::table('mgr_user_group')->where('id', $code)->delete();
            });

            $code = 200;
        } catch (Exception $e) {
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function group_search($code)
    {
		$where = "";
		$role = "0";
		$code = $code == '-' ? '' : $code;

		if($code != '') {
			$where .= "and r.`id` = :code";
			$role = "if(ifnull(r.group_no ,-1) >= 0,1,0)";
		}
		
        $sql = "
            select
                g.group_no,g.group_nm, $role as role
            from mgr_group g left outer join mgr_user_group r on g.`group_no` = r.`group_no` $where    
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
