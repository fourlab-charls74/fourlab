<?php

namespace App\Http\Controllers\head;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    //
    public function index() {

//        $id = Auth::guard('head')->user()->id;
//
//        echo $id;
//        return;
//
//        $sql = /** @lang text */
//            "select menu_nm as url
//            from mgr_log
//            where id = :id and  order by log_time desc limit 0,1 ";
//        $row = DB::selectOne($sql,array("id" => $id));
//        $url = $row->url;
//        return redirect($url);
        return view(Config::get('shop.head.view'). '/index');
    }
}

