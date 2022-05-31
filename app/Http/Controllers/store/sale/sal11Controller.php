<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal11Controller extends Controller
{
	//
	public function index() {
        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		$com_types	= [];
		$event_cds	= [];
		//판매유형
		$sell_types	= [];
		$code_kinds	= [];

		$values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			'com_types'		=> [],
			'event_cds'		=> [],
			'sell_types'	=> [],
			'code_kinds'	=> [],
		];
        return view( Config::get('shop.store.view') . '/sale/sal11',$values);
	}

	public function search(Request $request)
	{
	}
}
