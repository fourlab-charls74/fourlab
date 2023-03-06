<?php

namespace App\Http\Controllers\store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoCompleteController extends Controller
{
    public function store(Request $req)
    {

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        $where = '';

        if ($type == "select2") {
            $sql = /** @lang text */
                "
                select 
                    store_cd as id, store_nm as text
                from store
                where store_nm like '%$keyword%' 
                order by store_cd
                limit 0, 10
            ";
            $results =  DB::select($sql, [
                "store_cd" => sprintf("%s%%", $keyword),
                "store_nm" => sprintf("%s%%", $keyword)
            ]);

            return response()->json([
                "results" => $results
            ]);

        } else {
            $sql = /** @lang text */
            "
                select store_cd as id, store_nm as label
                from store 
                where store_nm like :keyword
                limit 0, 5
            ";

            return DB::select($sql,["keyword" => sprintf("%s%%",$keyword)]);

        }

    }

    public function storage(Request $req)
    {

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        $where = '';

        if ($type == "select2") {
            $sql = /** @lang text */
                "
                select 
                    storage_cd as id, storage_nm as text
                from storage
                where $where ( storage_cd like :storage_cd or storage_nm like :storage_nm ) 
                order by storage_cd
                limit 0, 10
            ";
            $results =  DB::select($sql, [
                "storage_cd" => sprintf("%s%%", $keyword),
                "storage_nm" => sprintf("%s%%", $keyword)
            ]);

            return response()->json([
                "results" => $results
            ]);

        } else {
            $sql = /** @lang text */
            "
                select storage_cd as id, storage_nm as label
                from storage 
                where storage_nm like :keyword
                limit 0, 5
            ";

            return DB::select($sql,["keyword" => sprintf("%s%%",$keyword)]);

        }

    }
}
