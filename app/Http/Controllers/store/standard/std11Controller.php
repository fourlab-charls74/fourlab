<?php

namespace App\Http\Controllers\store\standard;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class std11Controller extends Controller
{
	public function index()
	{
        $mutable	= now();
        $sdate		= $mutable->sub(1, 'day')->format('Y-m-d');
        $com_types = SLib::getCodes("G_COM_TYPE");
		$values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
		    "com_types" => $com_types
        ];
		return view(Config::get('shop.store.view') . '/standard/std11', $values);
	}

	public function search(Request $request)
	{
		// 설정 값 얻기
		$where = "";

		$query = /** @lang text */
            "select * from after_service
			where 1=1 $where
        ";

		$result = DB::select($query);
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			),
			"body" => $result
		]);
	}

	public function createIndex(Request $request)
	{
		$request->input('data');
		$mutable = now();
        $sdate = $mutable->sub(1, 'day')->format('Y-m-d');
        $com_types = SLib::getCodes("G_COM_TYPE");
		$values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
		    "com_types" => $com_types
        ];
		return view(Config::get('shop.store.view') . '/standard/std11_create', $values);
	}

	public function create(Request $request)
	{
		$data = $request->input('data');
		dd($data);
	}

	public function detail(Request $request)
	{
		$request->input('data');
		$mutable = now();
        $sdate = $mutable->sub(1, 'day')->format('Y-m-d');
        $com_types = SLib::getCodes("G_COM_TYPE");
		$values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
		    "com_types" => $com_types
        ];
		return view(Config::get('shop.store.view') . '/standard/std11_detail', $values);
	}

	public function edit(Request $request)
	{

	}

	public function remove(Request $request)
	{

	}


}
