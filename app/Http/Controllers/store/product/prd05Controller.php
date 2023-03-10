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

    public function show($code = '') {

        $mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');
		$cmd = 'add';
		$res = '';


		if($code != '') {
			$cmd = 'update';
			$sql = "
				select
					ppl.prd_cd as prd_cd
					, g.goods_no as goods_no
					, g.goods_nm as goods_nm
					, g.goods_nm_eng as goods_nm_eng
					, pc.prd_cd_p as prd_cd_p
					, pc.color as color
					, pc.size as size
					, pc.goods_opt as goods_opt
					, p.tag_price as goods_sh
					, p.price as price
					, ppl.change_price as change_price
					, pp.change_date as change_date
					, ppl.product_price_cd as product_price_cd
				from product_price_list ppl
					inner join product p on p.prd_cd = ppl.prd_cd
					left outer join product_code pc on pc.prd_cd = ppl.prd_cd
					inner join goods g on g.goods_no = pc.goods_no
					left outer join product_price pp on pp.idx = ppl.product_price_cd
				where 1=1 and ppl.product_price_cd = '$code'
			";

			$res = DB::selectOne($sql);

		}
	
        $values = [
			'code'			=> $code,
			'res'			=> $res,
			'cmd'           => $cmd,
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
		];

        return view( Config::get('shop.store.view') . '/product/prd05_show',$values);
    }

	public function search(Request $request)
	{

		// ordreby
        $ord_field  = $request->input("ord_field", "change_date");
        $ord        = $request->input("ord", "desc");
        $orderby    = sprintf("order by %s %s", $ord_field, $ord);
        
        // pagination
        $page       = $request->input("page", 1);
        $page_size  = $request->input("limit", 100);
        if ($page < 1 or $page == "") $page = 1;
        $startno    = ($page - 1) * $page_size;
        $limit      = " limit $startno, $page_size ";

		$sql = "
			select
				idx
				, change_date
				, change_kind
				, change_val
				, use_yn
				, change_cnt
				, rt
				, ut
			from product_price
			where 1=1
			$orderby
			$limit
		";

		$result = DB::select($sql);

		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
			select
				count(*) as total
			from product_price
			where 1=1
			";

			$row = DB::selectOne($sql);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}
	
		

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total"=> count($result)
			),
			"body"	=> $result
		]);
	}

	public function show_search(Request $request) {

		$product_price_cd = $request->input('product_price_cd');

		$sql = "
			select
				ppl.prd_cd as prd_cd
				, g.goods_no as goods_no
				, opt.opt_kind_nm as opt_kind_nm
				, b.brand_nm as brand
				, g.style_no as style_no
				, g.goods_nm as goods_nm
				, g.goods_nm_eng as goods_nm_eng
				, pc.prd_cd_p as prd_cd_p
				, pc.color as color
				, pc.size as size
				, pc.goods_opt as goods_opt
				, p.tag_price as goods_sh
				, p.price as price
				, ppl.change_price as change_val
				, ppl.product_price_cd as product_price_cd
			from product_price_list ppl
				inner join product p on p.prd_cd = ppl.prd_cd
				left outer join product_code pc on pc.prd_cd = ppl.prd_cd
				inner join goods g on g.goods_no = pc.goods_no
				left outer join brand b on b.br_cd = pc.brand
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
			where 1=1 and ppl.product_price_cd = '$product_price_cd'
		";

		$result = DB::select($sql);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
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

	public function update_price (Request $request) {

		$data = $request->input('data');
		$change_date = $request->input('change_date');
		$change_kind = $request->input('change_kind');
		$change_price = $request->input('change_price');
		$change_cnt = $request->input('change_cnt');
		$product_price_cd = $request->input('product_price_cd');
		$admin_id = Auth('head')->user()->id;

		try {
            DB::beginTransaction();

				DB::table('product_price_list')
					->where('product_price_cd', '=', $product_price_cd)
					->delete();

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

				DB::table('product_price')
					->where('idx', '=', $product_price_cd)
					->update([
						'change_date' => $change_date,
						'change_kind' => $change_kind,
						'change_val' => $change_price,
						'change_cnt' => $change_cnt,
						'ut' => now()
					]);
				
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