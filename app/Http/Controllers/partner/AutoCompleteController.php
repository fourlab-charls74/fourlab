<?php

namespace App\Http\Controllers\partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoCompleteController extends Controller
{
    public function template(Request $req) {
        $keyword = $req->input('keyword', '');
        $com_id = Auth('partner')->user()->com_id;

        if ($keyword == '') return '';

        $sql = /** @lang text */
            "
          select qna_no as no ,subject as label from (
            select q.type,q.qna_no,ca.code_val as tplkind,
              if(ifnull(q.subject,'') = '',b.type_nm,subject) as subject
            from qna_ans_type q
              left outer join qna_type b on q.qna_no = b.qna_no
              left outer join code ca on ca.code_kind_cd = 'G_TYPE_CD' and ca.code_id = q.tplkind
            where ( q.type = 'C' or (  q.type = 'P' and q.admin_id = '$com_id'  ))  and q.use_yn = 'Y'
            limit 0, 5
          ) a where subject like :keyword order by type desc,subject
        ";

        return DB::select($sql,["keyword" => sprintf("%%%s%%",$keyword)]);
    }

    public function goods_no(Request $req)
    {
        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2"){
            $sql = /** @lang text */
                "
                select 
                    goods_no as id,goods_no as text,
                    goods_nm,REPLACE(img,'_a_500','_s_50') AS img
                from goods 
                where ( goods_no = :goods_no or style_no like :style_no or goods_nm like :goods_nm ) 
                limit 0, 10
              ";
            $results =  DB::select($sql, [
                "goods_no" => sprintf("%s", $keyword),
                "style_no" => sprintf("%s%%", $keyword),
                "goods_nm" => sprintf("%s%%", $keyword)
            ]);


            return response()->json([
                "results" => $results
            ]);
        } else {
            $sql = /** @lang text */
                "
            select style_no as label,goods_no,REPLACE(img,'_a_500','_s_50') AS img
            from goods 
            where style_no like :keyword
            limit 0, 10
          ";
            $results = DB::select($sql, ["keyword" => sprintf("%s%%", $keyword)]);
            return response()->json($results);
        }

    }


    public function style_no(Request $req)
    {
        $com_id = Auth('partner')->user()->com_id;

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2"){
            $sql = /** @lang text */
                        "
                select goods_no,concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img,style_no as id,style_no as text
                from goods 
                where com_id = :com_id and style_no like :keyword
                limit 0, 10
              ";
            $results =  DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "com_id" => $com_id,
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
            where com_id = :com_id and style_no like :keyword
            limit 0, 10
          ";
            $results =  DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "com_id" => $com_id,
                "keyword" => sprintf("%s%%", $keyword)
            ]);
            return response()->json($results);
        }

    }

    public function brand(Request $req) {

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2"){

            $sql = /** @lang text */
                "
                select 
                    brand as id,brand_nm as text, concat(:image_svr,brand_logo) as img
                from brand 
                where use_yn = 'Y' and ( brand like :brand or brand_nm like :brand_nm )
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
                select brand_nm as label
                from brand 
                where brand_nm like :keyword
                limit 0, 5
            ";
            return DB::select($sql,["keyword" => sprintf("%s%%",$keyword)]);

        }

    }

    public function goods_nm(Request $req) {

        $com_id = Auth('partner')->user()->com_id;

        $type = $req->input('type', 'ac');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2") {
        } else {

            $sql = /** @lang text */
                "
                select 
                    goods_nm as label,concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img
                from goods 
                where com_id = :com_id and goods_nm like :keyword
                limit 0, 10
          ";
            return DB::select($sql, [
                "image_svr" => config('shop.image_svr'),
                "com_id" => $com_id,
                "keyword" => sprintf("%s%%", $keyword)]
            );
        }
    }

    public function category(Request $req) {

        $com_id = Auth('partner')->user()->com_id;

        $type = $req->input('type', 'ac');
        $cat_type = $req->input('cat_type', 'DISPLAY');
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        if($type == "select2") {

            $query = /** @lang text */
                "
                select 
                    a.d_cat_cd as id, a.d_cat_nm as text,a.full_nm
                from p_partner_category	p 
                    inner join category a on p.cat_type = a.cat_type and p.cat_cd = a.d_cat_cd
                where p.com_id = :com_id
                    and p.cat_type = :cat_type and full_nm like :keyword
                order by a.d_cat_cd
                limit 0,10                
            ";
            $results = DB::select($query,[
                'com_id' => $com_id,
                'cat_type' => $cat_type,
                "keyword" => sprintf("%%%s%%", $keyword)
            ]);

            return response()->json([
                "results" => $results
            ]);

        }
    }
}
