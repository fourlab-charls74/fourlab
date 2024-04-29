<?php

namespace App\Http\Controllers\shop;

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

    public function brand(Request $req) {

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');
        $isAll = $req->input("is_all", "false");

        if ($keyword == '') return '';

        $where = '';
        if($isAll == "false") $where .= ' use_yn = "Y" and ';

        if($type == "select2"){
            $sql = /** @lang text */
                "
                select 
                    brand as id,brand_nm as text, concat(:image_svr,brand_logo) as img
                from brand 
                where $where ( brand like :brand or brand_nm like :brand_nm ) 
                order by brand
                limit 0, 10
            ";
            $results =  DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "brand" => sprintf("%s%%", $keyword),
                "brand_nm" => sprintf("%%%s%%", $keyword)
            ]);

            return response()->json([
                "results" => $results
            ]);

        } else {
            $sql = /** @lang text */
            "
                select brand as id, brand_nm as label
                from brand 
                where brand_nm like :keyword
                limit 0, 5
            ";

            return DB::select($sql,["keyword" => sprintf("%s%%",$keyword)]);

        }

    }


    public function style_no(Request $req)
    {
        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2"){
            $sql = /** @lang text */
                        "
                select goods_no,
                    concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img,style_no as id,style_no as text
                from goods 
                where style_no like :keyword
                limit 0, 10
              ";
            $results =  DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "keyword" => sprintf("%s%%", $keyword)
            ]);

            return response()->json([
                "results" => $results
            ]);
        } else {
            $sql = /** @lang text */
                "
            select style_no as label,goods_no,concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img
            from goods 
            where style_no like :keyword
            limit 0, 10
          ";
            $results =  DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "keyword" => sprintf("%s%%", $keyword)
            ]);
            return response()->json($results);
        }

    }

    public function goods_nm(Request $req) {

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2") {
        } else {

            $sql = /** @lang text */
                "
                select 
                    goods_nm as label,
                    concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img
                from goods 
                where goods_nm like :keyword
                limit 0, 10
          ";
            return DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "keyword" => sprintf("%%%s%%", $keyword)]
            );
        }
    }

    public function goods_nm_eng(Request $req) {

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2") {
        } else {

            $sql = /** @lang text */
                "
                select 
                    goods_nm_eng as label,
                    concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img
                from goods 
                where goods_nm_eng like :keyword
                limit 0, 10
          ";
            return DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "keyword" => sprintf("%%%s%%", $keyword)]
            );
        }
    }
}
