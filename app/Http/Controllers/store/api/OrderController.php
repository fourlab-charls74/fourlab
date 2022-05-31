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

class OrderController extends Controller
{
    public function index(Request $req){
        $mutable = now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'ord_no'        => $req->input('ord_no', ''),
            'isld'          => $req->input('isld', 'N'),
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'ord_states'    => SLib::getOrdStates(),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            'ord_kinds'     => SLib::getCodes('G_ORD_KIND'),
            'dlv_types'     => SLib::getCodes('G_DLV_TYPE'),
            'sale_places'   => SLib::getSalePlaces(),
            'items'         => SLib::getItems(),
            'goods_types'   => SLib::getCodes('G_GOODS_TYPE'),
            'clm_states'     => SLib::getCodes('G_CLM_STATE'),
            'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE')
        ];

        return view( Config::get('shop.head.view') . "/common/order", $values);
    }
}
