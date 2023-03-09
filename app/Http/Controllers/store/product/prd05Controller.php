<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;
use Exception;

class prd05Controller extends Controller
{

	public function index() 
	{
		
		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		

		$values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
		];

		return view( Config::get('shop.store.view') . '/product/prd05',$values);
	}

    public function show() {

        $mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
		];

        return view( Config::get('shop.store.view') . '/product/prd05_show',$values);
    }

	public function search(Request $request)
	{

		$sql = "
			select
				change_date
				, change_kind
				, change_val
				, use_yn
				, change_cnt
				, rt
				, ut
			from product_price
		";

		$result = DB::select($sql);
		
		

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				// "total"		=> $total,
				// "page"		=> $page,
				// "page_cnt"	=> $page_cnt,
				"page_total"=> count($result),
			),
			"body"	=> $result
		]);
	}

	public function change_price (Request $request) {

		$data = $request->input('data');
		$change_date = $request->input('change_date');
		$change_kind = $request->input('change_kind');
		$change_price = $request->input('change_price');
		$change_cnt = $request->input('change_cnt');
		$admin_id = Auth('head')->user()->id;

		try {
            DB::beginTransaction();

				$product_price_cd = DB::table('product_price')
					->insertGetId([
						'change_date' => $change_date,
						'change_kind' => $change_kind,
						'change_val' => $change_price,
						'change_cnt' => $change_cnt,
						'admin_id' => $admin_id,
						'rt' => now(),
						'ut' => now()
					]);
				
				foreach ($data as $d) {
					DB::table('product_price_list')
						->insert([
							'product_price_cd' => $product_price_cd,
							'prd_cd' => $d['prd_cd'],
							'org_price' => $d['price'],
							'change_price' => $d['change_val'],
							'admin_id' => $admin_id,
							'rt' => now(),
							'ut' => now()
						]);
				}
				
			DB::commit();
            $code = 200;
            $msg = "변경한 상품 가격이 저장되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);

	}

	
}