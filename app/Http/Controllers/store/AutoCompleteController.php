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
                where 1=1 and use_yn = 'Y' and store_nm like '%$keyword%' 
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

	public function color(Request $req)
	{

		$type = $req->input('type', 'ac');
		$keyword = $req->input('keyword', '');

		if ($keyword == '') return '';

		$where = '';

		if ($type == "select2") {
			$sql = /** @lang text */
				"
                select
                	code_id as id, code_val as text
                from code
                where code_kind_cd = 'PRD_CD_COLOR' and (code_id like :code_id or code_val like :code_val)
                order by code_id
                limit 0, 10
            ";
			$results =  DB::select($sql, [
				"code_id" => sprintf("%s%%", $keyword),
				"code_val" => sprintf("%s%%", $keyword)
			]);

			return response()->json([
				"results" => $results
			]);

		} else {
			$sql = /** @lang text */
				"
                select
                	code_id as id, code_val as label
                from code
                where code_kind_cd = 'PRD_CD_COLOR' and code_val like :keyword
                limit 0, 5
            ";

			return DB::select($sql,["keyword" => sprintf("%s%%",$keyword)]);

		}

	}
}
