<?php

namespace App\Http\Controllers\head\sales;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;
use Carbon\Carbon;

class sal27Controller extends Controller
{
    // 일별 매출 통계
    public function index() {

        //return '일별매출통계';
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
        ];
        echo Config::get('shop.head.view');
        return view( Config::get('shop.head.view') . '/sales/sal26',$values);
    }

    public function search(Request $request){
    }

}
