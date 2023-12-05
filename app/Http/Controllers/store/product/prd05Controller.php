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
			'edate'         => date("Y-m-d", strtotime('3 day')),
		];

		return view( Config::get('shop.store.view') . '/product/prd05',$values);
	}

    public function show($code = '')
	{
        $mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');
		$cmd = 'add';
		$res = '';

		if($code != '') {
			$cmd = 'update';

			$sql = "
				select
					*
				from product_price
				where idx = '$code'
			";

			$product_cnt = DB::selectOne($sql);

			if ($product_cnt->change_cnt == 0) {
				$sql = "
					select
						change_date
						, change_type
						, change_kind
						, change_val
						, apply_yn
					from product_price
					where idx = '$code'
				";

				$res = DB::selectOne($sql);

			} else {
				$sql = "
					select
						ppl.prd_cd as prd_cd
						, g.goods_no as goods_no
						, g.goods_nm as goods_nm
						, g.goods_nm_eng as goods_nm_eng
						, pc.prd_cd_p as prd_cd_p
						, pc.color as color
						, ifnull((
							select s.size_cd from size s
							where s.size_kind_cd = pc.size_kind
							   and s.size_cd = pc.size
							   and use_yn = 'Y'
						),'') as size
						, pc.goods_opt as goods_opt
						, p.tag_price as goods_sh
						, p.price as price
						, ppl.change_price as change_price
						, pp.change_date as change_date
						, ppl.product_price_cd as product_price_cd
						, pp.apply_yn as apply_yn
						, pp.change_kind as change_kind
						, pp.change_val as change_val
						, pp.change_type as change_type
						, pp.price_kind as price_kind
					from product_price_list ppl
						inner join product p on p.prd_cd = ppl.prd_cd
						left outer join product_code pc on pc.prd_cd = ppl.prd_cd
						inner join goods g on g.goods_no = pc.goods_no
						left outer join product_price pp on pp.idx = ppl.product_price_cd
					where 1=1 and ppl.product_price_cd = '$code'
				";
	
				$res = DB::selectOne($sql);
			}
		}
	
        $values = [
			'code'	=> $code,
			'res'	=> $res,
			'cmd'	=> $cmd,
			'sdate'	=> $sdate,
			'edate'	=> date("Y-m-d"),
			'rdate'	=> date("Y-m-d", strtotime('1 day'))
		];

        return view( Config::get('shop.store.view') . '/product/prd05_show',$values);
    }

	public function view($code = '')
	{

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
					, ifnull((
						select s.size_cd from size s
						where s.size_kind_cd = pc.size_kind
						   and s.size_cd = pc.size
						   and use_yn = 'Y'
					),'') as size
					, pc.goods_opt as goods_opt
					, p.tag_price as goods_sh
					, p.price as price
					, ppl.change_price as change_price
					, pp.change_date as change_date
					, ppl.product_price_cd as product_price_cd
					, pp.apply_yn as apply_yn
					, pp.change_kind as change_kind
					, pp.change_val as change_val
					, pp.change_type as change_type
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

        return view( Config::get('shop.store.view') . '/product/prd05_view',$values);
    }


	public function search(Request $request)
	{
		$sdate = $request->input('sdate');
		$edate = $request->input('edate');
		$prd_cd = $request->input('prd_cd');
		$nud = $request->input("s_nud", "N");
		$goods_nm = $request->input("goods_nm");
		$goods_nm_eng = $request->input("goods_nm_eng");

		$where = "";
		if( $prd_cd != "" ) $where .= " and pc.prd_cd like '$prd_cd%'";
		if($nud == 'Y') $where .= " and ( ppl.change_date >= '$sdate' and ppl.change_date < date_add('$edate',interval 1 day)) ";
		if ($goods_nm != "") $where .= " and g.goods_nm like '%". Lib::quote($goods_nm)."%' ";
		if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%". Lib::quote($goods_nm_eng)."%' ";

		// ordreby
        $ord_field  = $request->input("ord_field", "ppl.change_date");
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
				ppl.idx
				, ppl.change_date
			    , 
			    case
			        when ppl.price_kind = 'T' then '정상가'
			        when ppl.price_kind = 'P' then '현재가'
			    end as price_kind
				, ppl.change_kind
				, ppl.change_val
				, ppl.apply_yn
				, pp.change_cnt
				, ppl.change_type
			    , ppl.org_price
			    , ppl.change_price
				, ppl.rt
				, ppl.ut
				, pc.prd_cd
			    , 
			    case
			        when ppl.plan_category = '00' then '변경없음'
			        when pc.plan_category = '01' then '정상매장'
			        when pc.plan_category = '02' then '전매장'
			        when pc.plan_category = '03' then '이월취급점'
			        when pc.plan_category = '04' then '아울렛전용'
			    end as plan_category
				, opt.opt_kind_nm as opt_kind_nm
				, ifnull(pc.goods_no,0) as goods_no
				, g.style_no
				, b.brand_nm as brand
				, g.goods_nm
				, g.goods_nm_eng
				, c.code_val as color
				, ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
				, g.price
				, g.goods_sh
			from product_price_list ppl
			inner join product_price pp on pp.idx = ppl.product_price_cd
			inner join product_code pc on ppl.prd_cd = pc.prd_cd
			left outer join goods g on g.goods_no = pc.goods_no
			left outer join code c on c.code_id = pc.color and c.code_kind_cd = 'PRD_CD_COLOR'
			left outer join brand b on b.br_cd = pc.brand
			left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
			where 1=1 
			$where
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
				from product_price_list ppl
				inner join product_price pp on pp.idx = ppl.product_price_cd
				inner join product_code pc on ppl.prd_cd = pc.prd_cd
				left outer join goods g on g.goods_no = pc.goods_no
				where 1=1
				$where
			";

			$row = DB::selectOne($sql, ['sdate' => $sdate,'edate' => $edate]);
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

	//상품가격변경 예약
	public function show_search(Request $request)
	{
		$product_price_cd = $request->input('product_price_cd');

		 // pagination
		 $page       = $request->input("page", 1);
		 $page_size  = $request->input("limit", 100);
		 if ($page < 1 or $page == "") $page = 1;
		 $startno    = ($page - 1) * $page_size;

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


		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
				select
					count(*) as total
				from product_price_list ppl
					inner join product p on p.prd_cd = ppl.prd_cd
					left outer join product_code pc on pc.prd_cd = ppl.prd_cd
					inner join goods g on g.goods_no = pc.goods_no
					left outer join brand b on b.br_cd = pc.brand
					left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				where 1=1 and ppl.product_price_cd = '$product_price_cd'
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

	
	//상품가격변경
	public function change_price(Request $request)
	{

		$data				= $request->input('data');
		$change_date_res	= $request->input('change_date_res');
		$change_date_now	= $request->input('change_date_now');
		$change_cnt			= $request->input('change_cnt');
		$type				= $request->input('type');
		$admin_id			= Auth('head')->user()->id;
		$plan_category		= $request->input('plan_category', '00');
		
		$change_date	= '';
		$change_type	= '';
		$apply_yn		= '';

		if ($type == 'reservation') {
			$change_date = $change_date_res;
			$change_type = 'R';
			$apply_yn = 'N';
		} else {
			$change_date = $change_date_now;
			$change_type = 'A';
			$apply_yn = 'Y';
		}

		try {
            DB::beginTransaction();

				$product_price_cd = DB::table('product_price')
					->insertGetId([
						'change_cnt'	=> $change_cnt,
						'apply_kind'	=> 'N',
						'admin_id'		=> $admin_id,
						'rt' => now(),
						'ut' => now()
					]);

				foreach ($data as $d) {
					$change_val = (int)$d['change_val'];
					
					DB::table('product_price_list')
						->insert([
							'change_date'		=> $change_date,
							'apply_yn'			=> $apply_yn,
							'change_type'		=> $change_type,
							'plan_category'		=> $plan_category,
							'product_price_cd'	=> $product_price_cd,
							'prd_cd'			=> $d['prd_cd'],
							'org_price'			=> $d['price'],
							'change_price'		=> $change_val,
							'admin_id'			=> $admin_id,
							'rt' => now(),
							'ut' => now()
						]);

					if ($type == 'now') {
						if( $d['goods_no'] != 0 ){
							//goods 테이블 price 가격변경
							DB::table('goods')
								->where('goods_no', '=', $d['goods_no'])
								->update(['price' => $change_val]);
						}

						$sql = " select prd_cd, prd_cd_p from product_code where prd_cd = :prd_cd ";
						$product_result = DB::select($sql,['prd_cd' => $d['prd_cd']]);

						foreach ($product_result as $pr) {
							DB::table('product')
								->where('prd_cd', 'like', $pr->prd_cd_p . '%')
								->update([
									"price" 	=> $change_val,
									"admin_id"	=> $admin_id,
									"ut"		=> now()
								]);

							if($plan_category != '00'){
								DB::table('product_code')
									->where('prd_cd', 'like', $pr->prd_cd_p . '%')
									->update(['plan_category' => $plan_category]);
							}
						}
					}
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

	//상품가격변경 즉시
	// 현재 사용하지 않음 (추후 사용하지 않을시 삭제예정 )
	public function change_price_now(Request $request)
	{

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
						'apply_yn' => 'Y',
						'change_type' => 'A',
						'admin_id' => $admin_id,
						'rt' => now(),
						'ut' => now()
					]);
				
				foreach ($data as $d) {
					DB::table('product_price_list')
						->insert([
							'product_price_cd' => $product_price_cd,
							'prd_cd' => $d['prd_cd'],
							'org_price' => $d['goods_sh'],
							'change_price' => $d['change_val'],
							'admin_id' => $admin_id,
							'rt' => now(),
							'ut' => now()
						]);
					
					//goods 테이블 price 가격변경
					DB::table('goods')
						->where('goods_no', '=', $d['goods_no'])
						->update(['price' => $d['change_val']]);

					//product 테이블 price 가격변경
					DB::table('product')
						->where('prd_cd', '=', $d['prd_cd'])
						->update(['price'=> $d['change_val']]);
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

	//상품가격변경 수정 예약
	public function update_price(Request $request) 
	{

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

	public function del_product_price(Request $request) 
	{
		$data = $request->input('data');

		try {
            DB::beginTransaction();
			foreach ($data as $d) {
				DB::table('product_price_list')
					->where('idx', '=' , $d['idx'])
					->delete();
			}
				
			DB::commit();
            $code = 200;
            $msg = "상품가격 변경 정보가 삭제되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}


	public function del_product(Request $request)
	{
		$data = $request->input('data');
		$idx = (int)$request->input('idx');
		$row_cnt = $request->input('cnt');

		try {
            DB::beginTransaction();
			foreach ($data as $d) {
				DB::table('product_price_list')
					->where('product_price_cd', '=' , $d['product_price_cd'])
					->where('prd_cd', '=', $d['prd_cd'])
					->delete();

			}

			DB::table('product_price')
				->where('idx','=', $idx)
				->update([
					'change_cnt' => $row_cnt - count($data)
				]);
				
			DB::commit();
            $code = 200;
            $msg = "상품가격 변경 정보가 삭제되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function import_excel(Request $request)
	{
		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		$values = [
			'sdate'	=> $sdate,
			'edate'	=> date("Y-m-d"),
			'rdate'	=> date("Y-m-d", strtotime('1 day'))
		];

		return view( Config::get('shop.store.view') . '/product/prd05_batch_show',$values);
	}

	// 엑셀업로드 
	public function upload(Request $request)
	{
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();

				$save_path = "data/store/prd05/";
				$file_name = "${now}_${user_id}.${extension}";

				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}

				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);

				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}

	public function batch_update(Request $request)
	{
		$admin_id = Auth('head')->user()->id;
		$code	= "200";
		$msg	= "";

		$change_date_res	= $request->input('change_date_res');
		$change_date_now	= $request->input('change_date_now');
		$change_cnt			= $request->input('change_cnt');
		$type				= $request->input('type');
		$plan_category		= $request->input('plan_category', '00');
		$data				= $request->input('data', []);
		
		if ($type == 'reservation') {
			$change_date = $change_date_res;
			$change_type = 'R';
			$apply_yn = 'N';
		} else {
			$change_date = $change_date_now;
			$change_type = 'A';
			$apply_yn = 'Y';
		}

		try {
			DB::beginTransaction();

			$product_price_cd = DB::table('product_price')
				->insertGetId([
					'change_cnt'	=> $change_cnt,
					'apply_kind'	=> 'N',
					'admin_id'		=> $admin_id,
					'rt' => now(),
					'ut' => now()
				]);

			foreach ($data as $d) {
				$change_val = (int)$d['change_val'];
				
				DB::table('product_price_list')
					->insert([
						'change_date'		=> $change_date,
						'apply_yn'			=> $apply_yn,
						'change_type'		=> $change_type,
						'plan_category'		=> $plan_category,
						'product_price_cd'	=> $product_price_cd,
						'prd_cd'			=> $d['prd_cd'],
						'org_price'			=> $d['price'],
						'change_price'		=> $change_val,
						'admin_id'			=> $admin_id,
						'rt' => now(),
						'ut' => now()
					]);

				if ($type == 'now') {
					
					if ($d['goods_no'] != 0) {
						//goods 테이블 price 가격변경
						DB::table('goods')
							->where('goods_no', '=', $d['goods_no'])
							->update(['price' => $change_val]);
					}
					
					$sql = " select prd_cd, prd_cd_p from product_code where prd_cd = :prd_cd ";
					$product_result = DB::select($sql,['prd_cd' => $d['prd_cd']]);
					
					foreach ($product_result as $pr) {
						//product 테이블 price 가격변경
						DB::table('product')
							->where('prd_cd', 'like', $pr->prd_cd_p . '%')
							->update([
								'price'		=> $change_val,
								'admin_id'	=> $admin_id,
								'ut'		=> now()
							]);

						if($plan_category != '00'){
							DB::table('product_code')
								->where('prd_cd', 'like', $pr->prd_cd_p . '%' )
								->update(['plan_category' => $plan_category]);
						}
					}
				}
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

	/** 일괄등록 상품 개별 조회 */
	public function get_goods(Request $request) {
		$data = $request->input('data', []);

		$result = [];

		foreach ($data as $key => $d) {
			$prd_cd 			= $d['prd_cd'];
			$change_val 	= preg_replace('/,/','', $d['change_val']);
			
			$sql = "
                select
                    pc.prd_cd
                    , pc.goods_no
                    , opt.opt_kind_nm
                    , b.brand_nm as brand
                    , if(g.goods_no <> '0', g.style_no, p.style_no) as style_no
                    , if(g.goods_no <> '0', g.goods_nm, p.prd_nm) as goods_nm
                    , if(g.goods_no <> '0', g.goods_nm_eng, p.prd_nm) as goods_nm_eng
                    , pc.prd_cd_p as prd_cd_p
                    , pc.color
                    , pc.size as size
                    , pc.goods_opt
                    , if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh
                    , if(g.goods_no <> '0', g.price, p.price) as price
                	, $change_val as change_val
                from product_code pc
                    inner join product p on p.prd_cd = pc.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                    left outer join brand b on b.br_cd = pc.brand
                where pc.prd_cd = '$prd_cd'
                limit 1
            ";

			$row = DB::selectOne($sql);
			array_push($result, $row);
		}

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
			],
			"body" => $result,
		]);
	}
}
