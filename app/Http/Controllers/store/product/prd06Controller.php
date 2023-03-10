<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Conf;
use App\Models\Jaego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class prd06Controller extends Controller
{
    public function index() {
        $edate = date("Y-m-d");
        $sdate = date('Y-m-d', strtotime(-7 . 'days'));

        $values = [
            "edate" => $edate,
            "sdate" => $sdate,
        ];
        return view( Config::get('shop.store.view') . '/product/prd06',$values);
    }

    public function search(Request $req)
    {
        $sdate              = $req->input("sdate", date('Y-m-d', strtotime(-7 . 'days')));
        $edate              = $req->input("edate", date("Y-m-d"));
        $price_apply_yn     = $req->input('price_apply_yn');
        $store_buffer       = $req->input('store_buffer');
        
        $where = "";
        if ($price_apply_yn != "") $where .= " and price_apply_yn = '$price_apply_yn' ";
        if ($store_buffer != "") $where .= " and store_buffer = '$store_buffer' ";

        $limit   = $req->input("limit", 100);

        $page = $req->input("page", 1);
        if ($page < 1 or $page == "") $page = 1;

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            // 갯수 얻기
            $sql =
                /** @lang text */
                " 
                select count(*) as total
                from bizest_stock_log
                where rt >= :sdate and rt < date_add(:edate,interval 1 day) $where
			";
            $row = DB::selectOne($sql, ['sdate' => $sdate, 'edate' => $edate]);
            $total = $row->total;
            if ($total > 0) {
                $page_cnt = (int)(($total - 1) / $page_size) + 1;
            }
        }

        $sql =
            /** @lang text */
            "
            select
				rt, prd_stock_cnt, store_cnt, price_apply_yn, store_buffer_kind, id
            from bizest_stock_log
            where rt >= :sdate and rt < date_add(:edate,interval 1 day) $where
            order by rt desc
            limit $startno,$page_size
            ";

        $rows = DB::select($sql, ['sdate' => $sdate, 'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function create()
    {
        $id = Auth('head')->user()->id;
        $default = DB::table('storage')
                    ->where('default_yn', '=', 'Y')
                    ->select('storage_cd', 'storage_nm')
                    ->first();

        $online = DB::table('storage')
                    ->where('online_yn', '=', 'Y')
                    ->select('storage_cd', 'storage_nm')
                    ->first();

        $values = [
            'id' => $id,
            'default' => $default,
            'online' => $online,
        ];

        // dd($values);

        return view(Config::get('shop.store.view') . '/product/prd06_show', $values);
    }

    public function search_store()
    {

        return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
				'total_row'  => $total_row,
			),
			"body"	=> $result
		]);
    }

    public function search_product()
    {
        
        return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
				'total_row'  => $total_row,
			),
			"body"	=> $result
		]);
    }
}
