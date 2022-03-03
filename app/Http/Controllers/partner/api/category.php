<?php

namespace App\Http\Controllers\partner\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class category extends Controller
{

    function getlist($cat_type,Request $request){

        $com_id = Auth('partner')->user()->com_id;
        $cat_nm	= $request->input("cat_nm");

        $where = "";
        if ($cat_nm != "") $where .= " and a.d_cat_nm like '" . Lib::quote($cat_nm) . "%' ";

        $query = /** @lang text */
            "
                select 
                    a.d_cat_cd, a.d_cat_nm,a.full_nm,(
						select if(count(*) > 0, length(a.d_cat_cd) + 1, length(a.d_cat_cd))
						from category
						where cat_type = :cat_type1 and p_d_cat_cd = a.d_cat_cd 
						limit 0,1                        
					) as mx_len
                from p_partner_category	p 
                    inner join category a on p.cat_type = a.cat_type and p.cat_cd = a.d_cat_cd
                where p.com_id = :com_id
                    and p.cat_type = :cat_type $where
                order by a.d_cat_cd
            ";

        $result = DB::select($query,[
            'com_id' => $com_id,
            'cat_type1' => $cat_type,
            'cat_type' => $cat_type
        ]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    function get_category_list($cat_type){

        $com_id = Auth('partner')->user()->com_id;
        $cat_type	= $cat_type;


        $query = /** @lang text */
            "
                    select a.d_cat_cd, a.full_nm,
                        case
                            when length(a.d_cat_cd) = 3 then 1
                            when length(a.d_cat_cd) = 6 then 2
                            when length(a.d_cat_cd) = 9 then 3
                            when length(a.d_cat_cd) = 12 then 4
                        end as level,
                        ( select if(count(*) >0,length(a.d_cat_cd)+1,length(a.d_cat_cd))
                            from category where cat_type = '$cat_type' and p_d_cat_cd = a.d_cat_cd limit 0,1 ) as mx_len
                    from p_partner_category	p inner join category a on p.cat_cd = a.d_cat_cd and a.cat_type = '$cat_type'
                    where p.com_id = '$com_id'
                        and p.cat_type = '$cat_type'
                    order by a.d_cat_cd
                ";

        $result = DB::select($query);

        foreach($result as $rows){
            $d_cat_cd = $rows->d_cat_cd;
            $full_nm = $rows->full_nm;
            $mx_len = $rows->mx_len;
            $rows->str = "'".$cat_type."','".$d_cat_cd."','".$full_nm."','".$mx_len."'";
        }

        //echo "<pre>$sql</pre>";exit;

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);

    }

        function get_category_by_goods_no($cat_type, $goods_no, $goods_sub ) {

            $query = "
                select a.d_cat_cd, a.seq, a.disp_yn, b.full_nm
                from category_goods a
                    inner join category b on b.cat_type = a.cat_type and b.d_cat_cd = a.d_cat_cd
                where a.goods_no = '$goods_no' and a.goods_sub = '$goods_sub'
                    and a.cat_type = '$cat_type'
                order by a.d_cat_cd
            ";

            $result = DB::select($query);

            foreach($result as $rows){
                $d_cat_cd = $rows->d_cat_cd;
                $seq = $rows->seq;
                $disp_yn = $rows->disp_yn;
                $location = $rows->full_nm;
                $rows->display_str = $location." ($seq) [$disp_yn] ".$d_cat_cd;
            }

            echo json_encode(array(
                "code" => 200,
                "head" => array(
                    "total" => count($result)
                ),
                "body" => $result
            ));
        }

}





