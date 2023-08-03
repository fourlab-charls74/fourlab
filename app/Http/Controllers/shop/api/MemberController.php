<?php

namespace App\Http\Controllers\shop\api;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
/**
 * 고객명 조회
 */
class MemberController extends Controller {

    /**
     * 고객명 선택 화면 랜더링
     */
    public function show() {
        return view(Config::get('shop.shop.view') . "/common/member");
    }

    /**
     * 검색
     */
    public function search(Request $request) {
        $name = $request->input('name');
        $sql = "
            select user_id, name, phone, zip, addr, addr2 from member where name like '%" . Lib::quote($name) . "%'
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