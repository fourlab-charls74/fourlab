<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm31Controller extends Controller
{
  //
    public function index() {
        $values = [
            'sdate'         => date("Y-m-d"),
            'edate'         => date("Y-m-d"),
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm31',$values);
    }

    public function search() {

        $sdate	= Request("sdate"). " 00:00:00";
        $edate	= Request("edate"). " 23:59:59";
        $kwd	= Request("kwd");
        $ip		= Request("ip");
        $user_id	= Request("user_id");
        $sch_cnt_fr	= Request("sch_cnt_fr");
        $sch_cnt_to	= Request("sch_cnt_to");

        $limit			= Request("limit",100);
        $ord_field		= Request("ord_field","s.idx");
        $ord				= Request("ord","desc");

        $orderby  = sprintf("order by %s %s",$ord_field,$ord);

        $page = Request("page",1);
        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($kwd != "") $where .= " and s.kwd like '$kwd%' ";
        if ($ip != "") $where .= " and s.ip = '$ip' ";
        if ($user_id != "") $where .= " and s.user_id = '$user_id' ";
        if ($sch_cnt_fr != "") $where .= " and s.sch_cnt >= '$sch_cnt_fr' ";
        if ($sch_cnt_to != "") $where .= " and s.sch_cnt <= '$sch_cnt_to' ";

        $sql = "
            select code_id as name,code_val as value from code where code_kind_cd = 'G_PRODUCTS_COLOR'
            order by code_id asc
        ";
        $colors = array();
        $rows = DB::select($sql);

        foreach($rows as $row) {
            $colors[$row->name] = $row->value;
        }

        $page_size = $limit;

        if ($page == 1) {
            // 갯수 얻기
            $sql = "
                select
                    count(*) as total
                from search_log s
                    left outer join search a on s.kwd = a.kwd
                    left outer join category c on cat_type = 'DISPLAY' and s.d_cat_cd = c.d_cat_cd
                where CAST(DAY AS DATE) between '$sdate' and '$edate' $where
            ";
            $data_cnt = DB::selectOne($sql)->total;

            $page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
            if($page == 1){
                $startno = ($page-1) * $page_size;
            } else {
                $startno = ($page-1) * $page_size;
            }

            $arr_header = array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt
            );
        } else {
            $startno = ($page-1) * $page_size;
            $arr_header = [];
        }

        if($limit == -1){
			if ($page > 1) $limit = "limit 0";
			else $limit = "";
        } else {
            $limit = " limit $startno, $page_size ";
        }

        $sql = "
            select
                s.rt, 
                s.kwd,
                s.qry, 
                a.synonym,
                ifnull(a.pv_1m,0) as pv_1m, 
                s.sch_cnt, 
                s.ip, 
                s.vid, 
                s.user_id, 
                s.d_cat_cd, 
                c.full_nm,
                s.price_from, 
                s.price_to,
                s.item,
                s.brand,
                s.color,
                s.sort
            from search_log s
                left outer join search a on s.kwd = a.kwd
                left outer join category c on cat_type = 'DISPLAY' and s.d_cat_cd = c.d_cat_cd
            where CAST(DAY AS DATE) between '$sdate' and '$edate' $where
            $orderby $limit
        ";

        $results = DB::select($sql);
        
		foreach ($results as $row) {
			$color = "";
			if($row->color > 0){
				$cs = array();
				foreach($colors as $idx => $value){
					if(($idx & $row->color) == $row->color){
						array_push($cs,$value);
					}
				}
                $color = join(",",$cs);
			}
			$row->color = $color;
        }
        
        $arr_header['page_total'] = count($results);
        
        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $results,
            "sql" => $sql
        ]);
    }
}
