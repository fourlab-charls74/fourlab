<?php

namespace App\Http\Controllers\head;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Head;
use Illuminate\Validation\ValidationException;

class SignUpController extends Controller
{
    //
    public function index()
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

        return view( Config::get('shop.head.view') . '/auth/sign_up_show', $values);
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
		$store_wonga_yn	= $request->input('store_wonga_yn');

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
				'store_wonga_yn' => $store_wonga_yn,
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
}
