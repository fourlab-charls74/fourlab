<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // 출고

class stk19Controller extends Controller
{
	public function index()
	{
		$sql = "
			select
				code_id, code_val, code_val3
			from code
			where code_kind_cd = 'rel_order' and code_id not like 'R_%'
		";
		$rel_order_res = DB::select($sql);

		$stores = DB::table('store')->where('use_yn', '=', 'Y')->select('store_cd', 'store_nm')->get();
		$storages = DB::table("storage")
			->where('use_yn', '=', 'Y')
			->select('storage_cd', 'storage_nm as storage_nm', 'default_yn')
			->orderByRaw('CASE WHEN default_yn = "Y" THEN 0 ELSE 1 END')
			->orderByRaw('CASE WHEN online_yn = "Y" THEN 0 ELSE 1 END')
			->get();

		$storage_arr = [];
		foreach ($storages as $index => $storage) {
			$storage_arr[] = [
				'storage_cd' => $storage->storage_cd,
				'storage_nm' => $storage->storage_nm,
				'seq' => $index,
			];
		}
		
		$values = [
			'today'         => date("Y-m-d"),
			'items'			=> SLib::getItems(), // 품목
			'stores'        => $stores, // 매장리스트
			'storages'      => $storages, // 창고리스트
			'rel_order_res' => $rel_order_res, //창고출고 차수
			'storage_arr'	=> $storage_arr,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view(Config::get('shop.store.view') . '/stock/stk19', $values);
	}

	public function search(Request $request)
	{
		$r = $request->all();

		$code = 200;
		$where = "";
		$orderby = "";
		$prd_cd_range_text = $request->input("prd_cd_range", '');

		// where
		if($r['prd_cd'] != null) {
			$prd_cd = explode(',', $r['prd_cd']);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}
		if(isset($r['goods_stat'])) {
			$goods_stat = $r['goods_stat'];
			if(is_array($goods_stat)) {
				if (count($goods_stat) == 1 && $goods_stat[0] != "") {
					$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
				} else if (count($goods_stat) > 1) {
					$where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
				}
			} else if($goods_stat != ""){
				$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
			}
		}
		if($r['style_no'] != null)
			$where .= " and g.style_no = '" . $r['style_no'] . "'";

		$goods_no = $r['goods_no'];
		$goods_nos = $request->input('goods_nos', '');
		if($goods_nos != '') $goods_no = $goods_nos;
		$goods_no = preg_replace("/\s/",",",$goods_no);
		$goods_no = preg_replace("/\t/",",",$goods_no);
		$goods_no = preg_replace("/\n/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);

		if($goods_no != ""){
			$goods_nos = explode(",", $goods_no);
			if(count($goods_nos) > 1) {
				if(count($goods_nos) > 500) array_splice($goods_nos, 500);
				$in_goods_nos = join(",", $goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
			}
		}

		if($r['com_cd'] != null)
			$where .= " and g.com_id = '" . $r['com_cd'] . "'";
		if($r['item'] != null)
			$where .= " and g.opt_kind_cd = '" . $r['item'] . "'";
		if(isset($r['brand_cd']))
			$where .= " and g.brand = '" . $r['brand_cd'] . "'";
		if($r['goods_nm'] != null)
			$where .= " and g.goods_nm like '%" . $r['goods_nm'] . "%'";
		if($r['goods_nm_eng'] != null)
			$where .= " and g.goods_nm_eng like '%" . $r['goods_nm_eng'] . "%'";

		$having = "";
		if(($r['ext_storage_qty'] ?? 'false') == 'true')
			$having .= " and sum(p.wqty) > '0'";

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// orderby
		$ord = $r['ord'] ?? 'desc';
		$ord_field = $r['ord_field'] ?? "p.prd_cd";
		$orderby = sprintf("order by %s %s", "p.".$ord_field, $ord);

		// pagination
		$page = $r['page'] ?? 1;
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $r['limit'] ?? 100;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		// search
		$sql = "
            select
                g.goods_no, 
                g.goods_type,
                ifnull(type.code_val, 'N/A') as goods_type_nm, 
                p.prd_cd, 
                op.opt_kind_nm,
                b.brand_nm, 
                g.style_no, 
                stat.code_val as sale_stat_cl, 
                g.goods_nm, 
                g.goods_nm_eng,
                pc.prd_cd_p as prd_cd_p,
                pc.color,
                c.code_val as color_nm,
               	pc.size,
                '' as rel_qty
            from product_stock_storage p
                inner join goods g on p.goods_no = g.goods_no
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join product_code pc on pc.prd_cd = p.prd_cd
                left outer join code c on c.code_id = pc.color and c.code_kind_cd = 'PRD_CD_COLOR'
            where 1=1 $where
            group by p.prd_cd
            having 1=1 $having
            $orderby
            $limit
		";

		$result = DB::select($sql);

		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
                select count(c.prd_cd) as total
                from (
                    select p.prd_cd, count(p.prd_cd)
                    from product_stock_storage p
                        inner join goods g on p.goods_no = g.goods_no
                        left outer join brand b on b.brand = g.brand
                        left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                        left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                        left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                        left outer join product_code pc on pc.prd_cd = p.prd_cd
                    where 1=1 $where
                    group by p.prd_cd
                    having 1=1 $having
                ) as c
            ";

			$row = DB::selectOne($sql);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		foreach($result as $re) {
			$prd_cd = $re->prd_cd;
			$sql = "
                select s.storage_cd, p.prd_cd, ifnull(p.wqty,'0') as wqty , ifnull(p.wqty,'0') as wqty2
                from storage s
                    left outer join product_stock_storage p on p.storage_cd = s.storage_cd and p.prd_cd = '$prd_cd'
                where s.use_yn = 'Y'
                order by case when default_yn ='Y' then 0 else 1 end,
                         case when online_yn = 'Y' then 0 else 1 end
            ";
			$row = DB::select($sql);

			$re->storage_qty = $row;
		}

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			],
			"body" => $result
		]);
	}

	// 창고출고
	public function request_release(Request $request) {
		$release_type = 'SG';
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$storage_cd = $request->input("storage_cd", '');
		$store_cd = $request->input("store_cd", '');
		$exp_dlv_day = $request->input("exp_dlv_day", '');
		$rel_order = $request->input("rel_order", '');
		$rel_type = $request->input("rel_type", '');
		$data = $request->input("products", []);
		
		$exp_day = str_replace("-", "", $exp_dlv_day);
		$exp_dlv_day_data = substr($exp_day,2,6);
		
		if($rel_type == 'rel_process') {
			$state = 20;
		} else {
			$state = 30;
		}

		try {
			DB::beginTransaction();

			$sql = "select ifnull(document_number, 0) + 1 as document_number from product_stock_release order by document_number desc limit 1";
			$document_number = DB::selectOne($sql);
			if ($document_number === null) $document_number = 1;
			else $document_number = $document_number->document_number;

			foreach($data as $d) {

				$sql = "
                    select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
                    from product_code pc
                        inner join goods g on g.goods_no = pc.goods_no
                    where prd_cd = :prd_cd
                ";
				$prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
				if($prd == null) continue;

				if ($rel_type == 'rel_process') {
					DB::table('product_stock_release')
						->insert([
							'document_number' => $document_number,
							'type' => $release_type,
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'goods_opt' => $prd->goods_opt,
							'qty' => $d['rel_qty'] ?? 0,
							'store_cd' => $store_cd,
							'storage_cd' => $storage_cd,
							'state' => $state,
							'exp_dlv_day' => $exp_dlv_day_data,
							'rel_order' => $rel_order,
							'comment' => $d['storage_memo'] ?? '',
							'req_id' => $admin_id,
							'req_rt' => now(),
							'rec_id' => $admin_id,
							'rec_rt' => now(),
							'rt' => now(),
						]);

					$release_no	= DB::getPdo()->lastInsertId();

				} else {
					DB::table('product_stock_release')
						->insert([
							'document_number' => $document_number,
							'type' => $release_type,
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'goods_opt' => $prd->goods_opt,
							'qty' => $d['rel_qty'] ?? 0,
							'store_cd' => $store_cd,
							'storage_cd' => $storage_cd,
							'state' => $state,
							'exp_dlv_day' => $exp_dlv_day_data,
							'rel_order' => $rel_order,
							'comment' => $d['storage_memo'] ?? '',
							'req_id' => $admin_id,
							'req_rt' => now(),
							'rec_id' => $admin_id,
							'rec_rt' => now(),
							'prc_id' => $admin_id,
							'prc_rt' => now(),
							'rt' => now(),
						]);

					$release_no	= DB::getPdo()->lastInsertId();
				}

				// product_stock -> 창고보유재고 차감
				DB::table('product_stock')
					->where('prd_cd', '=', $prd->prd_cd)
					->update([
						'wqty' => DB::raw('wqty - ' . ($d['rel_qty'] ?? 0)),
						'ut' => now(),
					]);

				// product_stock_storage -> 보유재고 차감
				DB::table('product_stock_storage')
					->where('prd_cd', '=', $prd->prd_cd)
					->where('storage_cd', '=', $storage_cd)
					->update([
						'wqty' => DB::raw('wqty - ' . ($d['rel_qty'] ?? 0)),
						'ut' => now(),
					]);

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'location_cd' => $storage_cd,
						'location_type' => 'STORAGE',
						'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
						'price' => $prd->price,
						'wonga' => $prd->wonga,
						'qty' => ($d['rel_qty'] ?? 0) * -1,
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'release_no'	=> $release_no,
						'comment' => '창고출고',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);

				// product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
				$store_stock_cnt =
					DB::table('product_stock_store')
						->where('store_cd', '=', $store_cd)
						->where('prd_cd', '=', $prd->prd_cd)
						->count();
				if($store_stock_cnt < 1) {
					// 해당 매장에 상품 기존재고가 없을 경우
					DB::table('product_stock_store')
						->insert([
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'store_cd' => $store_cd,
							'qty' => 0,
							'wqty' => $d['rel_qty'] ?? 0,
							'goods_opt' => $prd->goods_opt,
							'use_yn' => 'Y',
							'rt' => now(),
						]);
				} else {
					// 해당 매장에 상품 기존재고가 이미 존재할 경우
					DB::table('product_stock_store')
						->where('prd_cd', '=', $prd->prd_cd)
						->where('store_cd', '=', $store_cd)
						->update([
							'wqty' => DB::raw('wqty + ' . ($d['rel_qty'] ?? 0)),
							'ut' => now(),
						]);
				}

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'location_cd' => $store_cd,
						'location_type' => 'STORE',
						'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
						'price' => $prd->price,
						'wonga' => $prd->wonga,
						'qty' => $d['rel_qty'] ?? 0,
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'release_no'	=> $release_no,
						'comment' => '매장입고',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);

				//출고완료를 선택하고 출고 시 창고 실재고 차감
				if ($rel_type == 'rel_success') {
					// product_stock_storage 창고 실재고 차감
					DB::table('product_stock_storage')
						->where('prd_cd', '=', $prd->prd_cd)
						->where('storage_cd', '=', $storage_cd)
						->update([
							'qty' => DB::raw('qty - ' . ($d['rel_qty'] ?? 0)),
							'ut' => now(),
						]);
					
					DB::table('product_stock_hst')
						->where('release_no', '=', $release_no)
						->where('location_type', '=', 'STORAGE')
						->update([
							//'r_stock_state_date' => DB::raw('stock_state_date'),
							'r_stock_state_date' => date('Ymd'),
							'ut'	=> now()
						]);
				}
				
			}

			DB::commit();
			$code = 200;
			$msg = "창고출고가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function batch_show()
	{
		$sql = "
            select
                *
            from code
            where code_kind_cd = 'rel_order' and code_id not like 'R_%'
        ";
		$rel_order_res = DB::select($sql);

		$storages = DB::table("storage")
			->where('use_yn', '=', 'Y')
			->select('storage_cd', 'storage_nm as storage_nm', 'default_yn')
			->orderByRaw('CASE WHEN default_yn = "Y" THEN 0 ELSE 1 END')
			->orderByRaw('CASE WHEN online_yn = "Y" THEN 0 ELSE 1 END')
			->get();
		
		$values = [
			'today'         => date("Y-m-d"),
			'style_no'		=> "", // 스타일넘버
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
			'items'			=> SLib::getItems(), // 품목
			'storages'      => $storages, // 창고리스트
			'rel_order_res' => $rel_order_res 
		];

		return view(Config::get('shop.store.view') . '/stock/stk19_batch', $values);
	}

	public function import_excel(Request $request)
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

				$save_path = "data/store/stk19/";
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

	public function get_goods(Request $request)
	{
		$data = $request->input('data', []);
		$result = [];

		foreach($data as $key => $d)
		{
			$prd_cd = $d['prd_cd'];
			$store_cd = $d['store_cd'];
			$storage_cd = $d['storage_cd'];
			$qty = $d['qty'];

			$sql = "
                select
                    g.goods_no
                    , ifnull( type.code_val, 'N/A') as goods_type
                    , com.com_nm
                    , opt.opt_kind_nm
                    , brand.brand_nm as brand
                    , g.style_no
                    , g.goods_nm
                    , g.goods_nm_eng
                    , g.goods_type as goods_type_cd
                    , com.com_type as com_type_d
                    , s.prd_cd
                    , pc.prd_cd_p as prd_cd_p
                    , pc.color
                    , pc.size
                    , s.goods_opt
                    , ps.wqty as storage_qty
                    , (select wqty from product_stock_store where store_cd = '$store_cd' and prd_cd = s.prd_cd) as store_qty
                    , '$qty' as qty
                    , '$store_cd' as store_cd
                    , '$storage_cd' as storage_cd
                    , (select store_nm from store where store_cd = '$store_cd') as store_nm
                	, (select storage_nm from storage where storage_cd = '$storage_cd') as storage_nm
                from goods g inner join product_stock s on g.goods_no = s.goods_no
                    left outer join product_stock_storage ps on s.prd_cd = ps.prd_cd and ps.storage_cd = '$storage_cd'
                    left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
                    left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                    left outer join product_code pc on pc.prd_cd = s.prd_cd
                    left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                    left outer join company com on com.com_id = g.com_id
                    left outer join brand brand on brand.brand = g.brand
                where s.prd_cd = '$prd_cd'
                limit 1
            ";

			$row = DB::selectOne($sql);
			array_push($result, $row);
		}

		foreach($result as $re) {
			$prd_cd = $re->prd_cd;
			$sql = "
                select s.storage_cd, p.prd_cd, ifnull(p.wqty,'0') as wqty , ifnull(p.wqty,'0') as wqty2
                from storage s
                    left outer join product_stock_storage p on p.storage_cd = s.storage_cd and p.prd_cd = '$prd_cd'
                where s.use_yn = 'Y'
                order by case when default_yn ='Y' then 0 else 1 end,
                         case when online_yn = 'Y' then 0 else 1 end
            ";
			$row = DB::select($sql);

			$re->storage_qty = $row;
		}


		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
			],
			"body" => $result
		]);
	}


	// 창고출고 요청 - 엑셀 (바로 출고처리중상태로 변경됩니다.)
	public function request_release_excel(Request $request)
	{
		$release_type = 'SG';
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$exp_dlv_day = $request->input("exp_dlv_day", '');
		$rel_order = $request->input("rel_order", '');
		$rel_type = $request->input("rel_type","");
		$data = $request->input("products", []);
		$exp_day = str_replace("-", "", $exp_dlv_day);
		$exp_dlv_day_data = substr($exp_day,2,6);
		
		if($rel_type == 'rel_process') {
			$state = 20;
		} else {
			$state = 30;
		}

		try {
			DB::beginTransaction();

			$sql = "select ifnull(document_number, 0) + 1 as document_number from product_stock_release order by document_number desc limit 1";
			$document_number = DB::selectOne($sql);
			if ($document_number === null) $document_number = 1;
			else $document_number = $document_number->document_number;
			
			foreach($data as $d) {
				$cnt = 0;

				$sql = "
                    select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
                    from product_code pc
                        inner join goods g on g.goods_no = pc.goods_no
                    where prd_cd = :prd_cd
                ";
				$prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
				if($prd == null) continue;

				if ($rel_type == 'process') {
					DB::table('product_stock_release')
						->insert([
							'document_number' => $document_number,
							'type' => $release_type,
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'goods_opt' => $prd->goods_opt,
							'qty' => $d['qty'],
							'store_cd' => $d['store_cd'],
							'storage_cd' => $d['storage_cd'],
							'state' => $state,
							'exp_dlv_day' => $exp_dlv_day_data,
							'rel_order' => $rel_order,
							'comment' => $d['storage_memo']??'',
							'req_id' => $admin_id,
							'req_rt' => now(),
							'rec_id' => $admin_id,
							'rec_rt' => now(),
							'rt' => now(),
						]);

					$release_no	= DB::getPdo()->lastInsertId();

				} else {
					DB::table('product_stock_release')
						->insert([
							'document_number' => $document_number,
							'type' => $release_type,
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'goods_opt' => $prd->goods_opt,
							'qty' => $d['qty'],
							'store_cd' => $d['store_cd'],
							'storage_cd' => $d['storage_cd'],
							'state' => $state,
							'exp_dlv_day' => $exp_dlv_day_data,
							'rel_order' => $rel_order,
							'comment' => $d['storage_memo']??'',
							'req_id' => $admin_id,
							'req_rt' => now(),
							'rec_id' => $admin_id,
							'rec_rt' => now(),
							'prc_id' => $admin_id,
							'prc_rt' => now(),
							'rt' => now(),
						]);

					$release_no	= DB::getPdo()->lastInsertId();

				}
				
				// product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
				$store_stock_cnt =
					DB::table('product_stock_store')
						->where('store_cd', '=', $d['store_cd'])
						->where('prd_cd', '=', $prd->prd_cd)
						->count();
				if($store_stock_cnt < 1) {
					// 해당 매장에 상품 기존재고가 없을 경우
					DB::table('product_stock_store')
						->insert([
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'store_cd' => $d['store_cd'],
							'qty' => 0,
							'wqty' => $d['qty'],
							'goods_opt' => $prd->goods_opt,
							'use_yn' => 'Y',
							'rt' => now(),
						]);
				} else {
					// 해당 매장에 상품 기존재고가 이미 존재할 경우
					DB::table('product_stock_store')
						->where('prd_cd', '=', $prd->prd_cd)
						->where('store_cd', '=', $d['store_cd'])
						->update([
							'wqty' => DB::raw('wqty + ' . ($d['qty'])),
							'ut' => now(),
						]);
				}

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'location_cd' => $d['store_cd'],
						'location_type' => 'STORE',
						'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
						'price' => $prd->price,
						'wonga' => $prd->wonga,
						'qty' => $d['qty'],
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'release_no'	=> $release_no,
						'comment' => '매장입고',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);

				$cnt += $d['qty'];

				// product_stock -> 창고보유재고 차감
				DB::table('product_stock')
					->where('prd_cd', '=', $prd->prd_cd)
					->update([
						'wqty' => DB::raw("wqty - $cnt"),
						'ut' => now(),
					]);

				// product_stock_storage -> 보유재고 차감
				DB::table('product_stock_storage')
					->where('prd_cd', '=', $prd->prd_cd)
					->where('storage_cd', '=', $d['storage_cd'])
					->update([
						'wqty' => DB::raw("wqty - $cnt"),
						'ut' => now(),
					]);

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'location_cd' => $d['storage_cd'],
						'location_type' => 'STORAGE',
						'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
						'price' => $prd->price,
						'wonga' => $prd->wonga,
						'qty' => $cnt * -1,
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'release_no'	=> $release_no,
						'comment' => '창고출고',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);

				//출고완료를 선택하고 출고 시 창고 실재고 차감
				if ($rel_type == 'rel_success') {
					// product_stock_storage 창고 실재고 차감
					DB::table('product_stock_storage')
						->where('prd_cd', '=', $prd->prd_cd)
						->where('storage_cd', '=', $d['storage_cd'])
						->update([
							'qty' => DB::raw('qty - ' . ($d['qty'] ?? 0)),
							'ut' => now(),
						]);

					DB::table('product_stock_hst')
						->where('release_no', '=', $release_no)
						->where('location_type', '=', 'STORAGE')
						->update([
							//'r_stock_state_date' => DB::raw('stock_state_date'),
							'r_stock_state_date' => date('Ymd'),
							'ut'	=> now()
						]);

				}
			}
			DB::commit();
			$code = 200;
			$msg = "창고출고가 정상적으로 등록되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
}
