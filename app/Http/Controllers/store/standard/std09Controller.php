<?php

namespace App\Http\Controllers\store\standard;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class std09Controller extends Controller
{
    public function index() {
        $values = [


        ];
        return view( Config::get('shop.store.view') . '/standard/std09',$values);
    }

    public function create(){

        $values = [
        ];
        return view( Config::get('shop.store.view') . '/standard/std09_show',$values);
    }

    public function show($code) {

        //판매채널코드 자동으로 만들어지는 부분
        $sql = "
            select
                store_channel_cd
            from store_channel
            order by idx desc
            limit 1
        ";

        $store_channel_cd = DB::selectOne($sql);

        if ($store_channel_cd == '') {
            $sc_seq = '01';
        } else {
            $sc_seq = (int)$store_channel_cd->store_channel_cd + 1;
        }

        if ((int)$sc_seq < 10) {
            $sc_seq = '0'.$sc_seq;
        } else {
            $sc_seq = strval($sc_seq);
        }

        //매장구분 매장구분 코드 자동으로 만들어지는 부분
        $sql = "
            select
                store_kind_cd
            from store_channel
            order by idx desc
            limit 1
        ";

        $store_kind_cd = DB::selectOne($sql);

        if ($store_kind_cd == '') {
            $sk_seq = '01';
        } else {
            $sk_seq = (int)$store_kind_cd->store_kind_cd + 1;
        }

        if ((int)$sk_seq < 10) {
            $sk_seq = '0'.$sk_seq;
        } else {
            $sk_seq = strval($sk_seq);
        }

        //셀렉트박스 부분
        $sql = "
            select
                store_channel
                , store_channel_cd
            from store_channel
            where use_yn = 'Y' and dep = '1'
        ";

        $channels = DB::select($sql);


        $values = [
            'sc_seq' => $sc_seq,
            'sk_seq' => $sk_seq,
            'channels' => $channels
        ];

        return view( Config::get('shop.store.view') . '/standard/std09_show',$values);
    }

    public function search(Request $request) {

        $use_yn		= $request->input('use_yn', '');

        $where = "";

        if ($use_yn != "")  		$where .= " and use_yn = '" . Lib::quote($use_yn) . "' ";

        $sql = /** @lang text */
            "
            select 
                * 
            from store_channel
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

    public function save(Request $request) {

        $add_type = $request->input('add_type');
        $store_channel_cd = $request->input('store_channel_cd');
        $store_channel = $request->input('store_channel');
        $sel_channel = $request->input('sel_channel');
        $store_kind_cd = $request->input('store_kind_cd');
        $store_kind = $request->input('store_kind');
        $use_yn = $request->input('use_yn');

        $seq = 1;


        try {
            DB::beginTransaction();

            if ($add_type == 'C') {
                DB::table('store_channel')->insert([
                    'store_type' => $add_type,
                    'store_channel_cd' => $store_channel_cd,
                    'store_channel' => $store_channel,
                    'dep' => 1,
                    'use_yn' => $use_yn
                ]);
            }

            if ($add_type == 'T') {
                DB::table('store_channel')->insert([
                    'store_channel_cd' => $sel_channel,
                    'store_kind_cd' => $store_kind_cd,
                    'store_kind' => $store_kind,
                    'dep' => 2,
                    'seq' => $seq,
                    'use_yn' => $use_yn
                ]);
            }
           

            DB::commit();
			$code = 200;
			$msg = "판매채널 등록이 완료되었습니다.";

		} catch (\Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);

    }
}
