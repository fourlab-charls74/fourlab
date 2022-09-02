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

use App\Models\Conf;

class stk32Controller extends Controller
{
    public function index()
    {
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');

        $values = [
            'store_types'	=> SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view(Config::get('shop.store.view') . '/stock/stk32', $values);
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate = $request->input('edate', date("Ymd"));
        $sender = $request->input('');
        $content = $request->input('');

        $where = "";
        $orderby = "";
        if ($sender != "") $where .= "";
        if ($content != "") $where .= "";

        $sql = "";

        $result = DB::select($sql , ['sdate' => $sdate, 'edate' => $edate]);
        
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function create()
    {

        return view( Config::get('shop.store.view') . '/stock/stk32_show');
    }

    public function sendMsg()
    {
        
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');

        $values = [
            'store_types'	=> SLib::getCodes("STORE_TYPE"),
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_sendMsg', $values);
    }

    public function show($no) 
    {

        $sql = /** @lang text */
            "
            select * 
            from msg_store_detail
			where msg_cd = $no
        ";
        $result = DB::selectOne($sql,array("msg_cd" => $no));

        $values = [
            'msg_cd' => $no,
            'result' => $result,
        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_show', $values);
    }

    public function msg($no) 
    {

        $sql = /** @lang text */
            "
            select * from msg_store
			where msg_cd = $no
         ";
        $result = DB::selectOne($sql,array("msg_cd" => $no));

        $values = [
            'msg_cd' => $no,
            'result' => $result,
        ];

        return view( Config::get('shop.store.view') . '/stock/stk32_msg', $values);
    }

}