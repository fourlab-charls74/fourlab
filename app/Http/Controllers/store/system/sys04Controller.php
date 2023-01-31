<?php

namespace App\Http\Controllers\store\system;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sys04Controller extends Controller
{

    public function index(){

        $sdate	= now()->sub(1, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];
        return view(Config::get('shop.store.view') . '/system/sys04', $values);
    }

    public function search(Request $request){

        $id = Auth('head')->user()->id;

        $sd	= now()->sub(1, 'month')->format('Y-m-d');
        $ed	= date("Y-m-d");

        $menu_nm = $request->input('menu_nm');
        $page       = $request->input("page",1);
        $page_size  = $request->input("limit", 100);
        $sdate  = $request->input("sdate",$sd );
        $edate  = $request->input("edate",$ed );
        $search_id = $request->input('id');
        
        $page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','log_time');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);


        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($menu_nm != "") $where .= " and menu_nm like '%" . Lib::quote($menu_nm) . "%' ";
        if ($search_id != "") $where .= "and id like '%" . $search_id . "%'";

        $page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

        $total		= 0;
		$page_cnt	= 0;

        if ($page == 1)
        {
            // 갯수 얻기
            $sql = /** @lang text */
                "
                select 
                    count(*) as total 
                from store_log 
                where log_time >= '$sdate' and log_time < date_add('$edate',INTERVAL 1 day) 
                $where
			";

            $row		= DB::select($sql);
			$total		= $row[0]->total;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;

        }

        $sql =
            /** @lang text */
            "
            select 
                * 
            from store_log 
			where log_time >= :sdate and log_time < date_add(:edate,INTERVAL 1 day)
            $where
            $orderby            
            $limit
        ";

        $result = DB::select($sql,array(
            // "id" => $id,
            "sdate" => $sdate,
            "edate" => $edate,
        ));
        // print_r ($rows[1]);
        //echo ($total/10);

        return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body" => $result
		]);
    }

}

