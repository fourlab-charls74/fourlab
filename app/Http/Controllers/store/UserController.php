<?php

namespace App\Http\Controllers\store;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Head;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PDO;

class UserController extends Controller
{

    public function log(){

        $sdate	= now()->sub(1, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];
        return view( Config::get('shop.store.view') . '/auth/log',$values);
    }

    public function searchlog(Request $request){

        $id = Auth('head')->user()->id;

        $sd	= now()->sub(1, 'month')->format('Y-m-d');
        $ed	= date("Y-m-d");

        $menu_nm = $request->input('menu_nm');
        $page       = $request->input("page",1);
        $page_size  = $request->input("limit", 100);
        $sdate  = $request->input("sdate",$sd );
        $edate  = $request->input("edate",$ed );

        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($menu_nm != "") $where .= " and menu_nm like '%" . Lib::quote($menu_nm) . "%' ";

        $total      = 0;
        $page_cnt   = 0;

        if ($page == 1)
        {
            // 갯수 얻기
            $sql = /** @lang text */
                "
                select count(*) as total from store_log 
                where id = :id and log_time >= :sdate and log_time < date_add(:edate,INTERVAL 1 day) $where
			";
            $row = DB::selectOne($sql, array(
                "id" => $id,
                "sdate" => $sdate,
                "edate" => $edate,

            ));
            $total = $row->total;

            $page_cnt   = (int)(($total-1)/$page_size) + 1;
            $startno    = ($page-1) * $page_size;

        } else {
            $startno = ($page-1) * $page_size;
        }

        $limit = " limit $startno, $page_size ";

        $sql =
            /** @lang text */
            "
            select * from store_log 
			where id = :id and log_time >= :sdate and log_time < date_add(:edate,INTERVAL 1 day) $where
			order by log_time desc $limit
        ";

        $rows = DB::select($sql,array(
            "id" => $id,
            "sdate" => $sdate,
            "edate" => $edate,
        ));
        // print_r ($rows[1]);
        //echo ($total/10);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt
            ),
            "body" => $rows
        ]);
    }

}

