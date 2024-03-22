<?php

namespace App\Http\Controllers\head\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class category extends Controller
{
	public function index($cat_type, Request $req){
		$site = "";
		$category_nm = "";

		$msg= "";
		
		if($cat_type == ""){
			$msg = "처리할 수 없는 요청입니다.\\n관리자에게 문의해주세요. ";
		}
		$categorys = DB::select("select cat_type, cat_type_nm from category_type where cat_type = '$cat_type' and use_yn = 'Y'");
		$category_nm = $categorys[0]->cat_type_nm;

		if($category_nm == ""){
			$msg = "처리할 수 없는 요청입니다.\\n관리자에게 문의해주세요.";
		}

		$sites = DB::select("select com_id,com_nm from company where com_type = '4' and site_yn = 'Y'");
		//echo $cat_type;
		$values=[
			"msg" => $msg,
			"sites" => $sites,
			"category_nm" => $category_nm,
			"charset" => 'kor',
			"cat_type" => $cat_type,
			"site"	=> $site

		];

        return view( Config::get('shop.head.view') . "/common/category", $values);
    }


    function getlist($cat_type,Request $request){
        $cat_nm	= $request->input("cat_nm");
		$site	= $request->input("site");

        $where = "";
        if ($cat_nm != "") $where .= " and a.full_nm like '%" . Lib::quote($cat_nm) . "%' ";
		//if($site != "") $where .= " and site='$site' ";

        $query = /** @lang text */
            "
				select 
					a.d_cat_cd, a.d_cat_nm, a.full_nm, a.p_d_cat_cd, SUBSTRING_INDEX(a.full_nm, ' > ', 1) as p_d_cat_nm,
					(
						select if(count(*) > 0, length(a.d_cat_cd) + 1, length(a.d_cat_cd))
						from category
						where cat_type = :cat_type1 and p_d_cat_cd = a.d_cat_cd 
						limit 0,1                        
					) as mx_len
				from category a
				where a.cat_type = :cat_type2 and a.use_yn = 'Y'
				$where
				order by a.seq, d_cat_cd
            ";

        $result = DB::select($query,[
            'cat_type1'	=> $cat_type,
            'cat_type2'	=> $cat_type
        ]);

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
                "total"			=> count($result),
				"page"			=> 0,
                "page_cnt"		=> 0,
                "page_total"	=> 0
            ),
            "body"	=> $result
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

        function get_category_by_goods_no($cat_type, $goods_no, $goods_sub ) 
		{
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





