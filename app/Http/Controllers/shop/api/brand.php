<?php

namespace App\Http\Controllers\shop\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class brand extends Controller
{

    function get_brand_nm(Request $request){

        $brand_nm	= $request->input("brand_nm");
        $use_yn	= $request->input("use_yn");

        $where = "";
        if ($brand_nm != "")	$where .= "and brand_nm like '$brand_nm%' ";
        if ($use_yn != "")	$where .= "and use_yn = '$use_yn' ";


        $sql = "    select 
                        brand
                        , brand_nm
                        , use_yn
                        , '선택'
                    from 
                        brand
                    where 1=1
                    $where
                    order by brand_nm asc
        ";

        $result = DB::select($sql);
        //echo "<pre>$sql</pre>";exit;


        echo json_encode(array(
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ));








        }

    function getlist(Request $request){

        $brand	= $request->input("brand");
        $brand_nm	= $request->input("brand_nm");
        $brand_type	= $request->input("brand_type");
        $use_yn	= $request->input("use_yn");


        $where = [];

//        $where = "";
//        if ($brand != "")	$where .= "and brand like '$brand%' ";
        if ($use_yn != ""){
            $where[] = ['use_yn','=',$use_yn];
        }

        if ($brand_type != ""){
            $where[] = ['brand_type','=',$brand_type];
        }

        if ($brand != ""){
            $where[] = ['brand','like',sprintf("%s%%",$brand)];
        }

        if ($brand_nm != ""){
            $where[] = ['brand_nm','like',sprintf("%%%s%%",$brand_nm)];
        }


        $result = DB::table("brand")
            ->where($where)->orderBy('brand_nm')->select('brand', 'brand_nm', 'brand_type', 'use_yn')->get();

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result

            ]);
    }

}





