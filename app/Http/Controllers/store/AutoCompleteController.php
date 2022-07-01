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
                where $where ( store_cd like :store_cd or store_nm like :store_nm ) 
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
}
