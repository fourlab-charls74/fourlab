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

    public function show($code='', $type = '') {

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
            where dep = 2
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

        if ($code != '') {
            $sql = "
                select
                    store_channel
                    , store_channel_cd
                    , use_yn
                from store_channel
                where store_channel_cd = '$code'
            ";

            $edit = DB::selectOne($sql);

            $sql = "
                select
                    store_kind_cd
                    , store_channel_cd
                    , store_kind
                    , use_yn
                from store_channel
                where store_kind_cd = '$code' and dep = 2
            ";

            $edit2  = DB::selectOne($sql);
        }

        $values = [
            'sc_seq' => $sc_seq,
            'sk_seq' => $sk_seq,
            'channels' => $channels,
            'code' => $code == '' ? "" : "update",
            'type' => $type,
            'store_channel' => $edit??'',
            'store_kind' => $edit2??'',
        ];

        return view( Config::get('shop.store.view') . '/standard/std09_show',$values);
    }

    public function search(Request $request) {

        $store_channel  = $request->input('store_channel', '');
        $use_yn		    = $request->input('use_yn', '');

        $where = "";

        if ($store_channel != "")  	$where .= " and store_channel like '%" . Lib::quote($store_channel) . "%' ";
        if ($use_yn != "")  		$where .= " and use_yn = '" . Lib::quote($use_yn) . "' ";

        $sql = /** @lang text */
            "
            select 
                 store_type
                 , store_channel_cd
                 , store_channel
                 , dep
                 , use_yn
            from store_channel
            where 1=1 and store_type = 'C' and dep = 1 $where
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

    public function search_store_type($store_channel_cd, Request $request) {

        $code = 200;
        $store_kind     = $request->input('store_kind', '');

		$rows = $this->_get_store_channel($store_channel_cd, $store_kind);

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			],
			"body" => $rows
		]);

    }

    public function _get_store_channel($store_channel_cd) 
	{
		$sql = "
            select 
                store_type
                , store_channel_cd
                , store_channel
                , store_kind_cd
                , store_kind
                , seq
                , dep
                , use_yn
            from store_channel
            where 1=1 and store_type = 'T' and dep = 2 and store_channel_cd = '$store_channel_cd'

		";

		$rows = DB::select($sql, ["store_channel_cd" => $store_channel_cd]);
		return $rows;
	}

    //저장
    public function save(Request $request) {

        $add_type = $request->input('add_type');
        $store_channel_cd = $request->input('store_channel_cd');
        $store_channel = $request->input('store_channel');
        $sel_channel = $request->input('sel_channel');
        $store_kind_cd = $request->input('store_kind_cd');
        $store_kind = $request->input('store_kind');
        $use_yn = $request->input('use_yn');


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

                $sql = "
                    select
                        store_channel
                    from store_channel
                    where store_channel_cd = '$sel_channel'
                ";

                $select_store_channel = DB::selectOne($sql);

                $sql = "
                    select
                        count(*) as cnt
                    from store_channel
                    where store_channel_cd = '$sel_channel' and dep = 2
                ";

                $cnt = DB::selectOne($sql);


                if ($cnt->cnt == 0) {
                    $seq = 1;
                } else {
                    $seq = $cnt->cnt + 1;
                }

                DB::table('store_channel')->insert([
                    'store_type' => $add_type,
                    'store_channel_cd' => $sel_channel,
                    'store_channel' => $select_store_channel->store_channel,
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

    public function edit(Request $request) {

        $add_type = $request->input('add_type');
        $store_channel_cd = $request->input('store_channel_cd');
        $store_channel = $request->input('store_channel');
        $sel_channel = $request->input('sel_channel');
        $store_kind_cd = $request->input('store_kind_cd');
        $store_kind = $request->input('store_kind');
        $use_yn = $request->input('use_yn');

        try {
            DB::beginTransaction();

            if ($add_type == 'C') {
                DB::table('store_channel')
                    ->where('store_channel_cd','=',$store_channel_cd)
                    ->update([
                        'store_channel' => $store_channel,
                        'use_yn' => $use_yn
                    ]);
            }

            if ($add_type == 'T') {

                DB::table('store_channel')
                    ->where('store_kind_cd','=',$store_kind_cd)
                    ->where('dep','=',2)
                    ->update([
                        'store_channel_cd' => $sel_channel,
                        'store_kind' => $store_kind,
                        'use_yn' => $use_yn
                    ]);
            }
           

            DB::commit();
			$code = 200;
			$msg = "판매채널 수정이 완료되었습니다.";

		} catch (\Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);

    }

    //삭제
    public function delete(Request $request) {

        $data = $request->input('data');

        try {
            DB::beginTransaction();

            foreach ($data as $d) {

                DB::table('store_channel')
                    ->where('store_channel_cd', '=', $d['store_channel_cd'])
                    ->where('store_kind_cd', '=', $d['store_kind_cd'])
                    ->where('dep', '=', 2)
                    ->delete();
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);

    }

    //판매채널 삭제
    public function delete_channel(Request $request) {

        $data = $request->input('data');

        try {
            DB::beginTransaction();

            foreach ($data as $d) {

                DB::table('store_channel')
                    ->where('store_channel_cd', '=', $d['store_channel_cd'])
                    ->delete();
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);

    }
}
