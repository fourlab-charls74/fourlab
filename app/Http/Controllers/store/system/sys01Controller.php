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

class sys01Controller extends Controller
{
    public function index()
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

        $values = [
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
            'types' => SLib::getCodes('G_AD_TYPE'),
            'states' => SLib::getCodes('IS_SHOW'),
            "store_types" => SLib::getCodes("STORE_TYPE"),
            'store_channel'	=> $store_channel,
			'store_kind'	=> $store_kind
        ];
        return view(Config::get('shop.store.view') . '/system/sys01', $values);
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
        return view(Config::get('shop.store.view') . '/system/sys01_show', $values);
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
            'store'=> DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', '=', $user->store_cd)->first(),
        ];

        return view(Config::get('shop.store.view') . '/system/sys01_show', $values);
    }

    public function search(Request $req)
    {

        $name        = $req->input('name', '');
        $part        = $req->input('part', '');
        $grade	     = $req->input('grade', '');
		$store_no    = $req->input("store_no", '');
        $store_channel	= $req->input("store_channel");
		$store_channel_kind	= $req->input("store_channel_kind");

        $where = "";

        if ($name != "")        $where .= " and mu.name like '%" . Lib::quote($name) . "%' ";
        if ($part != "")        $where .= " and mu.part like '%" . Lib::quote($part) . "%' ";
        if ($grade != "")  		$where .= " and mu.grade = '" . Lib::quote($grade) . "' ";
        if ($store_channel != "") $where .= " and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= " and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
		if ($store_no != "") {
            $where .= " and ( 1<>1";
            foreach($store_no as $store_cd) {
                $where .= " or mu.store_cd = '" . Lib::quote($store_cd) . "' ";
            }
            $where	.= ")";
        }
        
        $sql =
			/** @lang text */
            "
            select mu.*, s.store_nm as store_nm, c.code_val as store_type
            from mgr_user mu
                left outer join store s on s.store_cd = mu.store_cd
                left outer join code c on c.code_kind_cd = 'STORE_TYPE' and c.code_id = s.store_type
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
        $store_cd       = $request->input('store_no');
        $account_yn     = $request->input('account_yn');
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
		$store_wonga_yn	= $request->input('store_wonga_yn');

		$user_cnt	    = DB::table('mgr_user')
						    ->where('id', $id)->count();
        
        $store_nm       = DB::table('store')
                            ->where('store_cd', $store_cd)->value('store_nm');
    
		if( $user_cnt == 0 ){

            if ($grade == 'P') {
                $mgr_user = [
                    'grade' => $grade,
                    'id' => $id,
                    'store_cd' => $store_cd,
                    'store_nm' => $store_nm,
                    'account_yn' => $account_yn,
                    'passwd' => DB::raw("CONCAT('*', UPPER(SHA1(UNHEX(SHA1('$passwd')))))"),
                    'pwchgperiod' => $pwchgperiod,
                    'name' => $name,
                    'ipfrom' => $ipfrom,
                    'ipto' => $ipto,
                    'md_yn' => $md_yn,
                    'use_yn' => $use_yn,
                    'store_wonga_yn' => $store_wonga_yn,
                    'part' => $part,
                    'posi' => $posi,
                    'tel' => $tel,
                    'exttel' => $exttel,
                    'messenger' => $messenger,
                    'email' => $email,
                    'pwchgdate' => now(),
                ];
            } else {
                $mgr_user = [
                    'grade' => $grade,
                    'id' => $id,
                    'store_cd' => '',
                    'passwd' => DB::raw("CONCAT('*', UPPER(SHA1(UNHEX(SHA1('$passwd')))))"),
                    'pwchgperiod' => $pwchgperiod,
                    'name' => $name,
                    'ipfrom' => $ipfrom,
                    'ipto' => $ipto,
                    'md_yn' => $md_yn,
                    'use_yn' => $use_yn,
                    'store_wonga_yn' => $store_wonga_yn,
                    'part' => $part,
                    'posi' => $posi,
                    'tel' => $tel,
                    'exttel' => $exttel,
                    'messenger' => $messenger,
                    'email' => $email,
                    'pwchgdate' => now(),
                ];
            }
			

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
		$grade		    = $request->input('grade');
        $store_cd       = $request->input('store_no');
        $account_yn     = $request->input('account_yn');
		$id			    = $request->input('id');
		$passwd		    = $request->input('passwd');
		$pwchgperiod	= $request->input('pwchgperiod');
		$name		    = $request->input('name');
		$ipfrom		    = $request->input('ipfrom');
		$ipto		    = $request->input('ipto');
		$md_yn		    = $request->input('md_yn');
		$use_yn		    = $request->input('use_yn');
		$part		    = $request->input('part');
		$posi		    = $request->input('posi');
		$tel		    = $request->input('tel');
		$exttel		    = $request->input('exttel');
		$messenger	    = $request->input('messenger');
		$email		    = $request->input('email');
		$roles		    = (object)json_decode($request->input('roles'));
		$passwd_chg	    = $request->input('passwd_chg');
		$store_wonga_yn	= $request->input('store_wonga_yn');

        $store_nm       = DB::table('store')
                            ->where('store_cd', $store_cd)->value('store_nm');   

        if ($grade == 'P') {
            $mgr_user = [
                'grade'			=> $grade,
                'id'			=> $id,
                'store_cd'      => $store_cd,
                'store_nm'      => $store_nm,
                'account_yn'    => $account_yn,
                'pwchgperiod'	=> $pwchgperiod,
                'name'			=> $name,
                'ipfrom'		=> $ipfrom,
                'ipto'			=> $ipto,
                'md_yn'			=> $md_yn,
                'use_yn'		=> $use_yn,
                'store_wonga_yn'=> $store_wonga_yn,
                'part'			=> $part,
                'posi'			=> $posi,
                'tel'			=> $tel,
                'exttel'		=> $exttel,
                'messenger'		=> $messenger,
                'email'			=> $email,
                'pwchgdate'		=> DB::raw('now()')
            ];
    
        } else {
            $mgr_user = [
                'grade'			=> $grade,
                'id'			=> $id,
                'store_cd'      => '',
                'store_nm'      => '',
                'account_yn'    => 'Y',
                'pwchgperiod'	=> $pwchgperiod,
                'name'			=> $name,
                'ipfrom'		=> $ipfrom,
                'ipto'			=> $ipto,
                'md_yn'			=> $md_yn,
                'use_yn'		=> $use_yn,
                'store_wonga_yn'=> $store_wonga_yn,
                'part'			=> $part,
                'posi'			=> $posi,
                'tel'			=> $tel,
                'exttel'		=> $exttel,
                'messenger'		=> $messenger,
                'email'			=> $email,
                'pwchgdate'		=> DB::raw('now()')
            ];
    
        }
		
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
