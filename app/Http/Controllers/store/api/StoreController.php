<?php

namespace App\Http\Controllers\store\api;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
/**
 * 매장명 조회
 */
class StoreController extends Controller {

    /**
     * 매장명 선택 화면 랜더링
     */
    public function show()
    {
        return view(Config::get('shop.store.view') . "/common/store");
    }

    /**
     * 검색
     */
    public function search(Request $request)
    {
        $store_nm = $request->input('store_nm');
        $store_cd = $request->input('store_cd');
        $store_channel = $request->input('store_channel');
        $store_channel_kind = $request->input('store_channel_kind');
        $where = "";

        if($store_channel != '') {
            $where .= " and store_channel = $store_channel";
        }

        if($store_channel_kind != '') {
            $where .= " and store_channel_kind = $store_channel_kind";
        }

        $sql = "
            select store_cd, store_nm 
            from store 
            where store_nm like '%" . Lib::quote($store_nm) . "%'
                and store_cd like '%" . Lib::quote($store_cd) . "%'
                $where
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

    /**
     * 판매채널 목록조회
     */
    public function search_storeChannel(Request $request)
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

        return response()->json(['code' => 200, 'body' => $store_channel]);
    }

    /**
     * 매장구분 목록조회
     */
    public function search_storeChannelKind(Request $request)
    {
        
        $sql = "
            select
                store_kind
                , store_kind_cd
                , use_yn
            from store_channel
            where dep = 2 and use_yn = 'Y'
        ";

        $store_kind = DB::select($sql);

        return response()->json(['code' => 200, 'body' => $store_kind]);
    }

    /**
     * 매장코드로 매장명 조회
     */
    public function search_storenm(Request $request)
    {
        $store_cds = $request->input("store_cds", []);
        $result = [];

        foreach($store_cds as $store_cd) {
            $store = DB::table("store")->where('store_cd', '=', $store_cd)->select('store_cd', 'store_nm')->get();
            array_push($result, $store[0]);
        }

        return response()->json(['code' => 200, 'head' => ['total' => count($result)], 'body' => $result]);
    }

    /**
     * 매장구분으로 매장명 조회
     */
    public function search_storenm_from_type(Request $request)
    {
        $store_type = $request->input("store_type", '');
        $result = DB::table('store')->where('store_type', '=', $store_type)->select('store_cd', 'store_nm')->get();
        
        return response()->json(['code' => 200, 'head' => ['total' => count($result)], 'body' => $result]);
    }

    /**
     * 매장코드로 해당 매장 주문정보 조회
     */
    public function search_store_info($store_cd = '')
    {
        $sql = "
            select *
            from store
            where store_cd = :store_cd
        ";
        $store = DB::selectOne($sql, ['store_cd' => $store_cd]);
        
        return response()->json([
            'code' => 200,
            'head' => [
                'total' => 1
            ],
            'body' => $store
        ]);
    }

    /**
     * 창고명 선택 화면 랜더링
     */
    public function storage_show()
    {
        return view(Config::get('shop.store.view') . "/common/storage");
    }

    /**
     * 검색
     */
    public function storage_search(Request $request)
    {
        $storage_cd = $request->input('storage_cd');
        $storage_nm = $request->input('storage_nm');
        $where = "";

        $sql = "
            select storage_cd, storage_nm 
            from storage 
            where storage_nm like '%" . Lib::quote($storage_nm) . "%'
                and storage_cd like '%" . Lib::quote($storage_cd) . "%'
                $where
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

    /**
     * 매장구분 목록조회
     */
    // public function search_storetype(Request $request)
    // {
    //     $store_types = SLib::getCodes("STORE_TYPE");
    //     return response()->json(['code' => 200, 'body' => $store_types]);
    // }

    /**
     * 매장코드로 매장명 조회
     */
    public function search_storagenm(Request $request)
    {
        $storage_cds = $request->input("storage_cds", []);
        $result = [];

        foreach($storage_cds as $storage_cd) {
            $storage = DB::table("storage")->where('store_cd', '=', $storage_cd)->select('storage_cd', 'storage_nm')->get();
            array_push($result, $storage[0]);
        }

        return response()->json(['code' => 200, 'head' => ['total' => count($result)], 'body' => $result]);
    }

    /**
     * 매장구분으로 매장명 조회
     */
    // public function search_storagenm_from_type(Request $request)
    // {
    //     $store_type = $request->input("store_type", '');
    //     $result = DB::table('store')->where('store_type', '=', $store_type)->select('store_cd', 'store_nm')->get();
        
    //     return response()->json(['code' => 200, 'head' => ['total' => count($result)], 'body' => $result]);
    // }

    /**
     * 매장코드로 해당 매장 주문정보 조회
     */
    public function search_storage_info($storage_cd = '')
    {
        $sql = "
            select *
            from storage
            where storage_cd = :storage_cd
        ";
        $store = DB::selectOne($sql, ['store_cd' => $storage_cd]);
        
        return response()->json([
            'code' => 200,
            'head' => [
                'total' => 1
            ],
            'body' => $store
        ]);
    }
}