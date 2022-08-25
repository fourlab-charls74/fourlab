<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class stk26Controller extends Controller
{
	public function index()
	{
        $sdate = now()->sub(1, 'week')->format('Y-m-d');
        $edate = date('Y-m-d');

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
		];
        return view(Config::get('shop.store.view') . '/stock/stk26', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'week')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $sc_cd = $request->input('sc_cd', '');
        $store_cd = $request->input('store_no', '');
        $sc_state = $request->input('sc_state', '');
        $where = "";

        $where .= " and s.sc_date >= '$sdate 00:00:00' ";
        $where .= " and s.sc_date <= '$edate 23:59:59' ";
        if($sc_cd != '') $where .= " and s.sc_cd = '$sc_cd' ";
        if($store_cd != '') $where .= " and s.store_cd = '$store_cd' ";
        if($sc_state != '') $where .= " and s.sc_state = '$sc_state' ";

        $sql = "
            select
                s.sc_date,
                s.sc_cd,
                s.store_cd,
                store.store_nm,
                s.sc_state,
                s.md_id,
                m.name as md_nm,
                s.comment
            from stock_check s
                inner join store on store.store_cd = s.store_cd
                inner join mgr_user m on m.id = s.md_id
            where 1=1 $where
        ";

        $result = DB::select($sql);

		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($result),
                'page' => 1,
				'page_cnt' => 1,
				'page_total' => 1
			],
			'body' => $result
		]);
    }

    public function show($sc_cd = '')
    {
        $sc = '';
        $new_sc_cd = '';

        if($sc_cd != '') {
            $sql = "
                select
                    s.sc_date,
                    s.sc_cd,
                    s.store_cd,
                    store.store_nm,
                    s.sc_state,
                    s.md_id,
                    m.name as md_nm,
                    s.comment
                from stock_check s
                    inner join store on store.store_cd = s.store_cd
                    inner join mgr_user m on m.id = s.md_id
                where sc_cd = :sc_cd
            ";
            $sc = DB::selectOne($sql, ['sc_cd' => $sc_cd]);
        } else {
            $sql = "
                select sc_cd
                from stock_check
                order by sc_cd desc
                limit 1
            ";
            $row = DB::selectOne($sql);
            if($row == null) $new_sc_cd = 1;
            else $new_sc_cd = $row->sc_cd + 1;
        }

        $values = [
            "cmd"           => $sc == '' ? "add" : "update",
            'sdate'         => $sc == '' ? date("Y-m-d") : $sc->sc_date,
            'sc'            => $sc,
            'new_sc_cd'     => $new_sc_cd,
		];
        return view(Config::get('shop.store.view') . '/stock/stk26_show', $values);
    }
}
