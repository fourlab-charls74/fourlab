<?php

namespace App\Http\Controllers\head;

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

    public function template_q(Request $req) {
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        $sql = /** @lang text */
            "
            select qna_no as no ,subject as label from (
                select q.type,q.qna_no,ca.code_val as tplkind,
                if(ifnull(q.subject,'') = '',b.type_nm,subject) as subject
                from qna_ans_type q
                left outer join qna_type b on q.qna_no = b.qna_no
                left outer join code ca on ca.code_kind_cd = 'G_TYPE_CD' and ca.code_id = q.tplkind
                where q.use_yn = 'Y' 
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
                    goods_nm,concat(:image_svr,REPLACE(img,'_a_500','_s_50')) AS img
                from goods 
                where ( goods_no = :goods_no or style_no like :style_no or goods_nm like :goods_nm ) 
                limit 0, 10
              ";
            $results =  DB::select($sql, [
                "image_svr" => config("shop.image_svr"),
                "goods_no" => sprintf("%s", $keyword),
                "style_no" => sprintf("%s%%", $keyword),
                "goods_nm" => sprintf("%s%%", $keyword)
            ]);


            return  response()->json([
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
            $results = DB::select($sql, [
                "image_svr" => config("shop.image_svr"),
                "keyword" => sprintf("%s%%", $keyword)
            ]);
            return response()->json($results);
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

    public function category(Request $req) {
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
                where p.cat_type = :cat_type and full_nm like :keyword
                group by id
                order by a.d_cat_cd
                limit 0,10                
            ";
            $results = DB::select($query,[
                'cat_type' => $cat_type,
                "keyword" => sprintf("%%%s%%", $keyword)
            ]);

            return response()->json([
                "results" => $results
            ]);
        }
    }

    public function company(Request $req) {
        $type = $req->input('type', 'ac');
		// $com_id = $req->input('com_id', '');
		$keyword = $req->input('keyword', '');
		// $use_yn = $req->input('use_yn', '');
		// $com_type = $req->input('com_type', '');

        if ($keyword == '') return '';

        if($type == "select2"){
            $sql = /** @lang text */
            "
                select a.com_id as id, ifnull(com_nm,'-') as text
                from company a 
                inner join code cd on cd.code_kind_cd = 'G_COM_TYPE' and cd.code_id = a.com_type
                where a.use_yn = 'Y'
                    and a.com_nm like '%$keyword%' 
                order by a.com_type, a.com_nm
            ";
            $results =  DB::select($sql);

            return response()->json([
                "results" => $results
            ]);

        } else {
            $where = "";
            // if ($S_COM_TYPE != "")	$where .= "and a.com_type = '$com_type' ";
            // if ($S_COM_ID != "")	$where .= "and a.com_id = '$com_id' ";
            if ($keyword != "")	$where .= "and a.com_nm like '%$keyword%' ";
            // if ($S_USE_YN != "")	$where .= "and a.use_yn = '$use_yn' ";

            $sql = "
                select a.com_id as id, ifnull(com_nm,'-') as label
                  from company a 
                 inner join code cd on cd.code_kind_cd = 'G_COM_TYPE' and cd.code_id = a.com_type
                 where a.use_yn = 'Y'
                       $where
                 order by a.com_type, a.com_nm
            ";

            return DB::select($sql);
        }

    }

	public function ad_type(Request $req){
		$ad_type = $req->input('ad_type', '');

        $sql = "
            select ad id, name val
            from ad
            where type = '$ad_type' 
            order by name 
        ";

		$result = DB::select($sql);
		$total = count($result);

        return response()->json([
			"results" => $result
		]);
    }

    public function style_no2(Request $req)
    {
        $keyword = $req->input('keyword', '');

        if ($keyword == '') return '';

        $sql = /** @lang text */
        "
            select 
                style_no as id,
                style_no as label
            from goods 
            where style_no like :keyword
            group by style_no order by style_no
            limit 0, 30
        ";
        $results = DB::select($sql, ["keyword" => sprintf("%s%%", $keyword)]);
        return $results;

    }

}
