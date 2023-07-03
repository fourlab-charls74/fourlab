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

    public function show($code='', $type = '', $idx = '') {
        //셀렉트박스 부분
        $sql = "
            select
                store_channel
                , store_channel_cd
            from store_channel
            where use_yn = 'Y' and dep = '1'
        ";

        $channels = DB::select($sql);

        // dd($code, $type);

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

            if($type == 'C') {
                $sql = "
                    select
                        idx
                        , store_kind_cd
                        , store_channel_cd
                        , store_kind
                        , use_yn
                    from store_channel
                    where store_channel_cd = '$code' and dep = 1
                ";
    
                $edit2  = DB::selectOne($sql);
            } else {
                $sql = "
                    select
                        idx
                        , store_kind_cd
                        , store_channel_cd
                        , store_kind
                        , use_yn
                    from store_channel
                    where store_kind_cd = '$code' and dep = 2
                ";
    
                $edit2  = DB::selectOne($sql);
            }
        }

        $values = [
            'channels' => $channels,
            'code' => $code == '' ? "" : "update",
            'store_channel_cd' => $code,
            'type' => $type,
            'idx' => $idx,
            'store_channel' => $edit??'',
            'store_kind' => $edit2??'',
        ];

        return view( Config::get('shop.store.view') . '/standard/std09_show',$values);
    }

    public function add($code='', $type = '', $idx = '') {

        //셀렉트박스 부분
        $sql = "
            select
                store_channel
                , store_channel_cd
            from store_channel
            where use_yn = 'Y' and dep = '1'
        ";

        $channels = DB::select($sql);

        // dd($code, $type);

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

            if($type == 'C') {
                $sql = "
                    select
                        idx
                        , store_kind_cd
                        , store_channel_cd
                        , store_kind
                        , use_yn
                    from store_channel
                    where store_channel_cd = '$code' and dep = 1
                ";
    
                $edit2  = DB::selectOne($sql);
            } else {
                $sql = "
                    select
                        idx
                        , store_kind_cd
                        , store_channel_cd
                        , store_kind
                        , use_yn
                    from store_channel
                    where store_kind_cd = '$code' and dep = 2
                ";
    
                $edit2  = DB::selectOne($sql);
            }
        }

        $values = [
            'channels' => $channels,
            'code' => $code == '' ? "" : "update",
            'store_channel_cd' => $code,
            'type' => $type,
            'idx' => $idx,
            'store_channel' => $edit??'',
            'store_kind' => $edit2??'',
        ];

        return view( Config::get('shop.store.view') . '/standard/std09_add',$values);
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
                idx
                 , store_type
                 , store_channel_cd
                 , store_channel
                 , dep
                 , use_yn
            from store_channel
            where 1=1 and store_type = 'C' and dep = 1 $where
            order by seq asc
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
                idx
                , store_type
                , store_channel_cd
                , store_channel
                , store_kind_cd
                , store_kind
                , seq
                , dep
                , use_yn
            from store_channel
            where 1=1 and store_type = 'T' and dep = 2 and store_channel_cd = '$store_channel_cd'
            order by seq asc

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

    public function store_type_save(Request $request) {

        $add_type = $request->input('add_type');
        $sel_channel = $request->input('sel_channel');
        $store_kind_cd = $request->input('store_kind_cd');
        $store_kind = $request->input('store_kind');
        $use_yn = $request->input('use_yn');


        try {
            DB::beginTransaction();

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
			$msg = "매장구분 등록이 완료되었습니다.";

		} catch (\Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);

    }

    public function edit(Request $request) {

        $idx = $request->input('idx');
        $add_type = $request->input('add_type');
        $store_channel_cd = $request->input('store_channel_cd');
        $store_channel = $request->input('store_channel');
        $sel_channel = $request->input('sel_channel');
        $store_kind_cd = $request->input('store_kind_cd');
        $store_kind = $request->input('store_kind');
        $use_yn = $request->input('use_yn');
        $code = $request->input('code');

        try {
            DB::beginTransaction();

            if ($add_type == 'C') {
                DB::table('store_channel')
                    ->where('store_channel_cd', '=', $code)
                    ->update([
                        'store_channel' => $store_channel,
                        'store_channel_cd' => $store_channel_cd,
                        'use_yn' => $use_yn
                    ]);
            }

            if ($add_type == 'T') {

                DB::table('store_channel')
                    ->where('idx','=',$idx)
                    ->where('dep','=',2)
                    ->update([
                        'store_channel_cd' => $sel_channel,
                        'store_kind' => $store_kind,
                        'store_kind_cd' => $store_kind_cd,
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

    // //삭제 주석처리 추후 필요할 수 있음
    // public function delete(Request $request) {

    //     $data = $request->input('data');
    //     $store_channel_cd = $data[0]['store_channel_cd'];

    //     try {
    //         DB::beginTransaction();

    //         foreach ($data as $d) {

    //             DB::table('store_channel')
    //                 ->where('store_channel_cd', '=', $d['store_channel_cd'])
    //                 ->where('store_kind_cd', '=', $d['store_kind_cd'])
    //                 ->where('dep', '=', 2)
    //                 ->delete();
    //         }

    //         $sql = "
    //             select 
    //                 store_channel
    //             from store_channel
    //             where store_channel_cd = '$store_channel_cd' and dep = 1
    //         ";

    //         $store_channel = DB::selectOne($sql);

    //         DB::commit();
    //         $code = 200;
    //         $msg = "";

    //     } catch(Exception $e){
    //         DB::rollback();
    //         $code = 500;
    //         $msg = $e->getMessage();
    //     }

    //     return response()->json([
    //         "code" => $code,
    //         "msg" => $msg,
    //         "store_channel_cd" => $store_channel_cd,
    //         "store_channel" => $store_channel->store_channel
    //     ]);

    // }

    // //판매채널 삭제
    // public function delete_channel(Request $request) {

    //     $data = $request->input('data');

    //     try {
    //         DB::beginTransaction();

    //         foreach ($data as $d) {

    //             DB::table('store_channel')
    //                 ->where('store_channel_cd', '=', $d['store_channel_cd'])
    //                 ->delete();
    //         }

    //         DB::commit();
    //         $code = 200;
    //         $msg = "";

    //     } catch(Exception $e){
    //         DB::rollback();
    //         $code = 500;
    //         $msg = $e->getMessage();
    //     }

    //     return response()->json([
    //         "code" => $code,
    //         "msg" => $msg
    //     ]);

    // }

    // 매장코드 중복체크
	public function check_code($channel = '', $add_type = '') 
	{

		$code	= 200;
		$msg	= "사용가능한 코드입니다.";

        if ($add_type === 'C') {
            $sql	= " select count(store_channel_cd) as cnt from store_channel where store_channel_cd = :store_channel_cd and dep = 1 ";
            $cnt	= DB::selectOne($sql, ["store_channel_cd" => $channel])->cnt;
        } else {
            $sql	= " select count(store_kind_cd) as cnt from store_channel where store_kind_cd = :store_kind_cd and dep = 2 ";
            $cnt	= DB::selectOne($sql, ["store_kind_cd" => $channel])->cnt;
        }

		if( $cnt > 0 ){
			$code	= 409;
			$msg	= "이미 사용중인 코드입니다.";
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

    //판매채널 순서변경
    public function change_seq_store_channel(Request $request) {

        $store_channel_cds = $request->input('store_channel_cds');

        try {
			DB::beginTransaction();
                for($i=0;$i<count($store_channel_cds);$i++) {
                    DB::table('store_channel')
                        ->where('store_channel_cd','=',$store_channel_cds[$i])
                        ->where('dep','=', 1)
                        ->update(['seq' => $i+1]);
                }
	
			$msg = "판매채널의 순서가 변경되었습니다.";
            $code = 200;
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    //매장구분 순서변경
    public function change_seq_store_type(Request $request) {

        $store_types = $request->input('store_types');

        $sql = "
            select
                store_channel_cd
                , store_channel
            from store_channel
            where store_kind_cd = '$store_types[0]'
        ";

        $store_channel = DB::selectOne($sql);

        try {
			DB::beginTransaction();
                for($i=0;$i<count($store_types);$i++) {
                    DB::table('store_channel')
                        ->where('store_kind_cd','=',$store_types[$i])
                        ->where('dep','=', 2)
                        ->update(['seq' => $i+1]);
                }
	
			$msg = "매장구분의 순서가 변경되었습니다.";
            $code = 200;
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
        return response()->json(['code' => $code,"msg" => $msg, "store_channel" => $store_channel->store_channel, "store_channel_cd" => $store_channel-> store_channel_cd]);
    }
   
}
