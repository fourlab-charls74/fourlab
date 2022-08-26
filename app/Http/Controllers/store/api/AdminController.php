<?php

namespace App\Http\Controllers\store\api;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
/**
 * 관리자 (담당자MD) 조회
 */
class AdminController extends Controller {

    /**
     * 검색
     */
    public function search(Request $request)
    {
        $md_nm = $request->input('md_nm');
        $where = "";

        $sql = "
            select id as md_id, name as md_nm 
            from mgr_user 
            where name like '%" . Lib::quote($md_nm) . "%'
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