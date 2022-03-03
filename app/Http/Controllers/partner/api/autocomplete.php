<?php

namespace App\Http\Controllers\partner\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class autocomplete extends Controller
{

    public function get_style_no($search_str){

        $query = "  select 
                        style_no 
                    from 
                        goods 
                    where 
                        style_no like '".$search_str."%'
                ";


        //echo "<pre>$query</pre>";
        //dd($query);
        $result = DB::select($query);

        $total = count($result);

        echo json_encode(array(
            "code" => 200,
            "head" => array(
                "total" => $total,
            ),
            "body" => $result
        ));
    }

    public function get_goods_nm($search_str){

        $query = "  select 
                        goods_nm
                    from 
                        goods 
                    where 
                        goods_nm like '".$search_str."%'
                ";


        //echo "<pre>$query</pre>";
        //dd($query);
        $result = DB::select($query);

        $total = count($result);

        echo json_encode(array(
            "code" => 200,
            "head" => array(
                "total" => $total,
            ),
            "body" => $result
        ));
    }

}


