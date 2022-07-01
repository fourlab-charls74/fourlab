<?php

namespace App\Http\Controllers\store\api;

use App\Http\Controllers\Controller;
use App\Components\Lib;
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
        $store_cd = $request->input('store_cd');
        $store_nm = $request->input('store_nm');
        $sql = "
            select store_cd, store_nm 
            from store 
            where store_nm like '%" . Lib::quote($store_nm) . "%'
                and store_cd like '%" . Lib::quote($store_cd) . "%'
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

}