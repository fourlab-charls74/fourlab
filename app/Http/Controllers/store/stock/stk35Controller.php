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
use Exception;
use App\Models\Conf;
use App\Exports\ExcelViewExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

const PRODUCT_STOCK_TYPE_RETURN = 11; // 창고반품 (매장->창고)

class stk35Controller extends Controller
{
	public function index()
	{
		$storages = DB::table("storage")->where('use_yn', 'Y')
			->select('storage_cd', 'storage_nm as storage_nm', 'default_yn')
			->orderByDesc('default_yn')->get();

		$values = [
			'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
			'edate'         => date("Y-m-d"),
			'storages'      => $storages,
			'sr_states'	    => SLib::getCodes("SR_CODE"),	// 반품상태
			'sr_reasons'	=> SLib::getCodes("SR_REASON"),	// 반품사유
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/stock/stk35', $values);
	}

	public function search(Request $request)
	{
		$date_yn	= $request->input('date_yn');
		$sdate      = $request->input("sdate", now()->sub(1, 'week')->format('Ymd'));
		$edate      = $request->input("edate", date("Ymd"));
		$exp_date_yn	= $request->input('exp_date_yn');
		$exp_sdate      = $request->input("exp_sdate", now()->sub(1, 'week')->format('Ymd'));
		$exp_edate      = $request->input("exp_edate", date("Ymd"));
		$sr_state   = $request->input("sr_state", "");
		$sr_reason  = $request->input("sr_reason", "");
		$storage_cd = $request->input("storage_cd", "");
		$store_no   = $request->input("store_no", "");

		$store_channel		= $request->input("store_channel", "");
		$store_channel_kind	= $request->input("store_channel_kind", "");

		// where
		$where = "";

		if( $date_yn == "Y" ) {
			$sdate = str_replace("-", "", $sdate);
			$edate = str_replace("-", "", $edate);
			$where .= "
				and cast(sr.sr_fin_date as date) >= '$sdate' 
				and cast(sr.sr_fin_date as date) <= '$edate'
			";
		}
		if( $exp_date_yn == "Y" ) {
			$exp_sdate = str_replace("-", "", $exp_sdate);
			$exp_edate = str_replace("-", "", $exp_edate);
			$where .= "
				and cast(sr.sr_date as date) >= '$exp_sdate' 
				and cast(sr.sr_date as date) <= '$exp_edate'
        	";
		}

		if($sr_state != "")     $where .= " and sr.sr_state = '" . Lib::quote($sr_state) . "'";
		if($sr_reason != "")    $where .= " and sr.sr_reason = '" . Lib::quote($sr_reason) . "'";
		if($storage_cd != "")   $where .= " and sr.storage_cd = '" . Lib::quote($storage_cd) . "'";
		if($store_no != "")     $where .= " and sr.store_cd = '" . Lib::quote($store_no) . "'";

		if ($store_channel != "") 		$where .= "and store.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") 	$where .= "and store.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		// ordreby
		$ord        = $request->input("ord", "desc");
		$ord_field  = $request->input("ord_field", "sr.sr_cd");
		if($ord_field == 'sr_cd') $ord_field = 'sr.' . $ord_field;
		$orderby    = sprintf("order by %s %s", $ord_field, $ord);

		// pagination
		$page       = $request->input("page", 1);
		$page_size  = $request->input("limit", 100);
		if ($page < 1 or $page == "") $page = 1;
		$startno    = ($page - 1) * $page_size;
		$limit      = " limit $startno, $page_size ";

		// search
		$sql = "
            select
                sr.sr_cd,
                sr.storage_cd,
                storage.storage_nm,
                sr.store_cd,
                store.store_nm,
                store.store_type,
                sc.store_kind as store_type_nm,
                sr.sr_date,
                sr.sr_fin_date,
                sr.sr_kind,
                sr.sr_state,
                c.code_val as sr_state_nm,
                sum(pss.wqty) as store_qty,
                sum(srp.return_qty) as sr_qty,
                sum(srp.return_qty * srp.return_price) as sr_price,
                sum(srp.return_p_qty) as return_p_qty,
                sum(srp.return_p_qty * srp.return_price) as return_p_price,
				sum(srp.fixed_return_qty) as fixed_return_qty,
                sum(srp.fixed_return_price) as fixed_return_price,
                sr.sr_reason,
                co.code_val as sr_reason_nm,
                sr.comment
            from store_return sr
                inner join store_return_product srp on srp.sr_cd = sr.sr_cd
				left outer join product_stock_store pss on pss.prd_cd = srp.prd_cd and pss.store_cd = sr.store_cd
                inner join storage on storage.storage_cd = sr.storage_cd
                inner join store on store.store_cd = sr.store_cd
                inner join store_channel sc on sc.store_channel_cd = store.store_channel and sc.store_kind_cd = store.store_channel_kind
                inner join code c on c.code_kind_cd = 'SR_CODE' and c.code_id = sr.sr_state
                inner join code co on co.code_kind_cd = 'SR_REASON' and co.code_id = sr.sr_reason
            where 1=1 $where
			group by sr.sr_cd
            $orderby
            $limit
		";
		$result = DB::select($sql);

		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
				select count(sr_cd) as total
				from (
					select sr.sr_cd
					from store_return sr
						inner join store_return_product srp on srp.sr_cd = sr.sr_cd
						left outer join product_stock_store pss on pss.prd_cd = srp.prd_cd and pss.store_cd = sr.store_cd
						inner join storage on storage.storage_cd = sr.storage_cd
						inner join store on store.store_cd = sr.store_cd
						inner join store_channel sc on sc.store_channel_cd = store.store_channel and sc.store_kind_cd = store.store_channel_kind
						inner join code c on c.code_kind_cd = 'SR_CODE' and c.code_id = sr.sr_state
						inner join code co on co.code_kind_cd = 'SR_REASON' and co.code_id = sr.sr_reason
					where 1=1 $where
					group by sr.sr_cd
				) a
            ";

			$row = DB::selectOne($sql);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			],
			"body" => $result
		]);
	}

	// 매장반품 등록 & 상세팝업 오픈
	public function show($sr_cd = '')
	{
		$sr = '';
		$storages = DB::table("storage")
			->where('use_yn', '=', 'Y')
			->whereIn('storage_cd', ['S0006', 'C0005', 'A0009'])
			->select('storage_cd', 'storage_nm as storage_nm', 'default_yn')
			->orderByDesc('default_yn')
			->get();

		if($sr_cd != '') {
			$sql = "
                select
                    sr.sr_cd,
					concat(sr.store_cd, '_', REPLACE(sr.sr_date, '-', '') , '_' , LPAD(sr.sr_cd, 3, '0')) as sr_code,
                    sr.storage_cd,
                    sr.store_cd,
                    s.store_nm,
                    sr.sr_date,
                    sr.sr_kind,
                    sr.sr_state,
                    sr.sr_reason,
                    sr.comment,
                    sr.rt,
                    sr.ut
                from store_return sr
                    inner join store s on s.store_cd = sr.store_cd
                where sr_cd = :sr_cd
            ";
			$sr = DB::selectOne($sql, [ 'sr_cd' => $sr_cd ]);
		}

		$values = [
			'cmd' 			=> $sr == '' ? "add" : "update",
			'sdate'         => $sr == '' ? date("Y-m-d") : $sr->sr_date,
			'storages'      => $storages,
			'sr_reasons'    => SLib::getCodes("SR_REASON"),
			'sr'            => $sr,
			'sr_cd'         => $sr_cd,
			'sr_state'      => $sr->sr_state ?? '',
			'reject_reasons' => SLib::getCodes('SR_REJECT_REASON'),
			'return_storage_cd' => 'S0006', // 반품창고
		];
		return view(Config::get('shop.store.view') . '/stock/stk35_show', $values);
	}

	// 기존에 반품등록된 상품목록 조회
	public function search_return_products(Request $request)
	{
		$sr_cd = $request->input('sr_cd', '');
		$sql = "
            select 
                @rownum := @rownum + 1 as count,
                srp.sr_prd_cd, 
                srp.sr_cd, 
                srp.prd_cd,
                pc.goods_no,
                g.goods_type,
                ifnull(type.code_val, 'N/A') as goods_type_nm,
                op.opt_kind_nm,
                b.brand_nm as brand, 
                g.style_no, 
                stat.code_val as sale_stat_cl, 
                g.goods_nm,
                g.goods_nm_eng,
                if(pc.prd_cd_p = '', concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt), pc.prd_cd_p) as prd_cd_p,
                pc.color,
				pc.size,
                pc.goods_opt,
                g.goods_sh,
                srp.price,
                srp.return_price, 
                ifnull(pss.qty, 0) as store_qty,
                ifnull(pss.wqty, 0) as store_wqty, 
                srp.return_qty as qty,
                (srp.return_qty * srp.return_price) as return_amt,
                srp.return_p_qty as return_p_qty,
                (srp.return_p_qty * srp.return_price) as return_p_amt,
                true as isEditable,
                srp.fixed_return_price as fixed_return_price,
                srp.fixed_return_qty as fixed_return_qty,
                srp.fixed_comment as fixed_comment,
                srp.reject_reason as reject_reason,
                reason.code_val as reject_reason_val,
                srp.reject_comment as reject_comment
            from store_return_product srp
                inner join product_code pc on pc.prd_cd = srp.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join store_return sr on sr.sr_cd = srp.sr_cd
                left outer join product_stock_store pss on pss.store_cd = sr.store_cd and pss.prd_cd = srp.prd_cd
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join code reason on reason.code_kind_cd = 'SR_REJECT_REASON' and reason.code_id = srp.reject_reason,
                (select @rownum :=0) as r
            where srp.sr_cd = :sr_cd
        ";
		$products = DB::select($sql, [ 'sr_cd' => $sr_cd ]);

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($products),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
			],
			"body" => $products
		]);
	}

	// 매장반품 등록
	public function save(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$sr_kind = $request->input("sr_kind", "G"); // 일반(G) / 일괄(B)
		$sr_state = 10; // 반품등록 시 요청 상태로 등록
		$sr_date = $request->input("sr_date", date("Y-m-d"));
		$storage_cd = $request->input("storage_cd", "");
		$sr_reason = $request->input("sr_reason", "");
		$comment = $request->input("comment", "");
		$products = $request->input("products", []);
		$store_cd = $request->input("store_cd", []);
		
		try {
			DB::beginTransaction();

			if(count($products) < 1) {
				throw new Exception("반품등록할 상품을 선택해주세요.");
			}
			
			$group_store_cd = collect($products)->groupBy('store_cd');
			
			foreach ($group_store_cd as $store_cd => $store_product) {
				if(count($store_product) < 1) {
					continue;
				}
				
				$store = DB::table('store')->where('store_cd', $store_cd)->select('store_cd', 'store_nm')->first();
				
				$sr_cd = DB::table('store_return')
					->insertGetId([
						'storage_cd' => $storage_cd,
						'store_cd' => $store_cd,
						'sr_date' => $sr_date,
						'sr_kind' => $sr_kind,
						'sr_state' => $sr_state,
						'sr_reason' => $sr_reason,
						'comment' => $comment,
						'rt' => now(),
						'admin_id' => $admin_id,
					]);

				foreach($store_product as $product) {

					if ($product['store_wqty'] < $product['return_qty']) {
						$code = 501;
						throw new Exception('매장보유재고보다 많은 수량을 반품요청할 수 없습니다.');
					}

					DB::table('store_return_product')
						->insert([
							'sr_cd' => $sr_cd,
							'prd_cd' => $product['prd_cd'],
							'price' => $product['price'], // 판매가
							'return_price' => $product['return_price'], // 반품단가
							'return_qty' => $product['return_qty'], // 요청수량
							'rt' => now(),
							'admin_id' => $admin_id,
						]);

				}
				//상품반품 등록 시 매장에 내역 확인 알림 표시
				$res = DB::table('msg_store')
					->insertGetId([
						'msg_kind' => 'STORE_RETURN',
						'sender_type' => 'H',
						'sender_cd' => $admin_id ?? '',
						'reservation_yn' => 'N',
						'content' => '본사에서 ' . $store->store_nm . '에 매장반품 요청하였습니다.',
						'rt' => now()
					]);

				DB::table('msg_store_detail')
					->insert([
						'msg_cd' => $res,
						'receiver_type' => 'S',
						'receiver_cd' => $store_cd ?? '',
						'check_yn' => 'N',
						'rt' => now()
					]);
				
			}

			DB::commit();
			$msg = "매장반품이 정상적으로 요청되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = $code === 200 ? 500 : $code;
			$msg = $e->getMessage();
		}

		return response()->json([ "code" => $code, "msg" => $msg ]);
	}

	// 창고반품 수정 (+상태변경)
	public function update(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$sr_cd = $request->input("sr_cd", "");
		$sr_reason = $request->input("sr_reason", "");
		$comment = $request->input("comment", "");
		$new_state = $request->input("new_state", "");
		$products = $request->input("products", []);

		try {
			DB::beginTransaction();

			$sr_update = [
				'sr_reason' => $sr_reason,
				'comment' => $comment,
				'ut' => now(),
				'admin_id' => $admin_id,
			];

			if ($new_state != '') {
				$sr_update = array_merge($sr_update, [ 'sr_state' => $new_state ]);
			}

			if ($new_state == '40') {
				$sr_update = array_merge($sr_update, [ 'sr_fin_date' => date('Y-m-d') ]);
			}

			DB::table('store_return')
				->where('sr_cd', $sr_cd)
				->update($sr_update);

			$now_state = DB::table('store_return')->where('sr_cd', $sr_cd)->value('sr_state');

			foreach($products as $product) {
				if (($product['store_wqty'] < $product['return_qty']) && $now_state < 30) {
					$code = 501;
					throw new Exception('매장보유재고보다 많은 수량을 반품요청할 수 없습니다.');
				}

				$sr_prd_cd = $product['sr_prd_cd'] ?? '';
				if ($sr_prd_cd !== '') {
					DB::table('store_return_product')
						->where('sr_prd_cd', '=', $sr_prd_cd)
						->update([
							'return_price' => $product['return_price'], // 반품단가
							'return_qty' => $product['return_qty'], // 요청수량
							'return_p_qty' => $product['return_p_qty'], // 처리수량
							'reject_reason' => $product['reject_reason'] ?? '', // 반품거부사유
							'reject_comment' => $product['reject_comment'] ?? '', // 반품거부메모
							'fixed_return_price' => $product['fixed_return_price'], // 확정금액
							'fixed_return_qty' => $product['fixed_return_qty'], // 확정수량
							'fixed_comment' => $product['fixed_comment'] ?? '',
							'ut' => now(),
							'admin_id' => $admin_id,
						]);
				} else {
					$sr_prd_cd = DB::table('store_return_product')
						->insertGetId([
							'sr_cd' => $sr_cd,
							'prd_cd' => $product['prd_cd'],
							'price' => $product['price'], // 판매가
							'return_price' => $product['return_price'], // 반품단가
							'return_qty' => $product['return_qty'], // 요청수량
							'return_p_qty' => $product['return_p_qty'], // 처리수량
							'reject_reason' => $product['reject_reason'] ?? '', // 반품거부사유
							'reject_comment' => $product['reject_comment'] ?? '', // 반품거부메모
							'fixed_return_price' => $product['fixed_return_price'], // 확정금액
							'fixed_return_qty' => $product['fixed_return_qty'], // 확정수량
							'fixed_comment' => $product['fixed_comment'] ?? '',
							'rt' => now(),
							'admin_id' => $admin_id,
						]);
				}

				if ($new_state == 30) {
					// 반품처리중 재고처리
					$this->_set_return_p_stock($sr_prd_cd, $admin_id, $admin_nm);
				} else if ($new_state == 40) {
					// 반품완료 재고처리
					$this->_set_return_stock($sr_prd_cd, $admin_id, $admin_nm);
				}
			}

			DB::commit();
			$msg = "매장반품내역이 정상적으로 저장되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = $code === 200 ? 500 : $code;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	/** 매장반품 처리중 시, 재고처리 */
	private function _set_return_p_stock($sr_prd_cd, $admin_id, $admin_nm)
	{
		$sql = "
			select srp.prd_cd
			     , pc.goods_no
			     , pc.goods_opt
			     , srp.return_price as price
			     , g.wonga
			     , srp.return_p_qty as return_qty
				 , sr.store_cd
				 , sr.storage_cd
			from store_return_product srp
			    inner join store_return sr on sr.sr_cd = srp.sr_cd
				inner join product_code pc on pc.prd_cd = srp.prd_cd
				inner join goods g on g.goods_no = pc.goods_no
			where srp.sr_prd_cd = :sr_prd_cd
		";
		$row = DB::selectOne($sql, [ 'sr_prd_cd' => $sr_prd_cd ]);
		if ($row === null) throw new Exception("반품정보가 존재하지 않습니다.");

		$qty = $row->return_qty ?? 0;

		// 매장보유재고 차감 (+ history)
		DB::table('product_stock_store')
			->where('prd_cd', '=', $row->prd_cd)
			->where('store_cd', '=', $row->store_cd)
			->update([
				'wqty' => DB::raw('wqty - ' . $qty),
				'ut' => now(),
			]);
		if ($qty > 0 || $qty < 0) {
			DB::table('product_stock_hst')
				->insert([
					'goods_no' => $row->goods_no,
					'prd_cd' => $row->prd_cd,
					'goods_opt' => $row->goods_opt,
					'location_cd' => $row->store_cd,
					'location_type' => 'STORE',
					'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(출고)
					'price' => $row->price,
					'wonga' => $row->wonga,
					'qty' => $qty * -1,
					'stock_state_date' => date('Ymd'),
					'r_stock_state_date' => date('Ymd'),
					'ord_opt_no' => '',
					'store_return_no'	=> $sr_prd_cd,
					'comment' => '매장반품처리',
					'rt' => now(),
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
				]);
		}
		//해당 창고에 재고있는지 확인하는 부분
		$storage_stock_cnt =
			DB::table('product_stock_storage')
				->where('storage_cd', '=', $row->storage_cd)
				->where('prd_cd', '=', $row->prd_cd)
				->count();

		if($storage_stock_cnt < 1) {
			// 해당 창고에 상품 기존재고가 없을 경우
			DB::table('product_stock_storage')
				->insert([
					'goods_no' => $row->goods_no,
					'prd_cd' => $row->prd_cd,
					'storage_cd' => $row->storage_cd,
					'qty' => 0,
					//'wqty' => $qty, 231110 ceduce
					'wqty' => 0,
					'goods_opt' => $row->goods_opt,
					'use_yn' => 'Y',
					'rt' => now()
				]);
		} else {
			// 해당 창고에 상품 기존재고가 이미 존재할 경우
			// 창고보유재고 증가
			// 231110 ceduce
			//DB::table('product_stock_storage')
			//	->where('prd_cd', '=', $row->prd_cd)
			//	->where('storage_cd', '=', $row->storage_cd)
			//	->update([
			//		'wqty' => DB::raw('wqty + ' . ($qty)),
			//		'ut' => now(),
			//	]);
		}

		if ($qty > 0 || $qty < 0) {
			// 231110 ceduce
			//DB::table('product_stock_hst')
			//	->insert([
			//		'goods_no' => $row->goods_no,
			//		'prd_cd' => $row->prd_cd,
			//		'goods_opt' => $row->goods_opt,
			//		'location_cd' => $row->storage_cd,
			//		'location_type' => 'STORAGE',
			//		'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(입고)
			//		'price' => $row->price,
			//		'wonga' => $row->wonga,
			//		'qty' => $qty,
			//		'stock_state_date' => date('Ymd'),
			//		'ord_opt_no' => '',
			//		'comment' => '매장반품처리',
			//		'rt' => now(),
			//		'admin_id' => $admin_id,
			//		'admin_nm' => $admin_nm,
			//	]);
		}

		// 전체재고 중 창고재고 업데이트
		// 231110 ceduce
		//DB::table('product_stock')
		//	->where('prd_cd', '=', $row->prd_cd)
		//	->update([
		//		'wqty' => DB::raw('wqty + ' . $qty),
		//		'ut' => now(),
		//	]);

		return 1;
	}

	/** 매장반품 완료처리 시, 재고처리 */
	private function _set_return_stock($sr_prd_cd, $admin_id, $admin_nm)
	{
		$sql = "
			select srp.prd_cd
			     , pc.goods_no
			     , pc.goods_opt
			     , srp.return_price as price
			     , g.wonga
			     , srp.return_p_qty
			     , srp.fixed_return_qty
				 , sr.store_cd
				 , sr.storage_cd
			from store_return_product srp
			    inner join store_return sr on sr.sr_cd = srp.sr_cd
				inner join product_code pc on pc.prd_cd = srp.prd_cd
				inner join goods g on g.goods_no = pc.goods_no
			where srp.sr_prd_cd = :sr_prd_cd
		";
		$row = DB::selectOne($sql, [ 'sr_prd_cd' => $sr_prd_cd ]);
		if ($row === null) throw new Exception("반품정보가 존재하지 않습니다.");

		$f_qty = $row->fixed_return_qty ?? 0; // 확정수량
		$p_qty = $row->return_p_qty ?? 0; // 처리수량

		// 매장보유재고 재설정 (+ history) 및 실재고 차감
		DB::table('product_stock_store')
			->where('prd_cd', '=', $row->prd_cd)
			->where('store_cd', '=', $row->store_cd)
			->update([
				'qty' => DB::raw('qty - ' . $f_qty),
				'wqty' => DB::raw('wqty + ' . ($p_qty - $f_qty)),
				'ut' => now(),
			]);
		if (($p_qty - $f_qty) > 0 || ($p_qty - $f_qty) < 0) {
			DB::table('product_stock_hst')
				->insert([
					'goods_no' => $row->goods_no,
					'prd_cd' => $row->prd_cd,
					'goods_opt' => $row->goods_opt,
					'location_cd' => $row->store_cd,
					'location_type' => 'STORE',
					'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(출고)
					'price' => $row->price,
					'wonga' => $row->wonga,
					'qty' => ($p_qty - $f_qty),
					'stock_state_date' => date('Ymd'),
					'r_stock_state_date' => date('Ymd'),
					'ord_opt_no' => '',
					'store_return_no'	=> $sr_prd_cd,
					'comment' => '매장반품확정',
					'rt' => now(),
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
				]);
		}

		//
		DB::table('product_stock_hst')
			->where('store_return_no', '=', $sr_prd_cd)
			->where('location_type', '=', $row->store_cd)
			->where('type', '=', '11')
			->update([
				'r_stock_state_date' => date('Ymd'),
				'ut'	=> now()
			]);

		//해당 창고에 재고있는지 확인하는 부분
		$storage_stock_cnt =
			DB::table('product_stock_storage')
				->where('storage_cd', '=', $row->storage_cd)
				->where('prd_cd', '=', $row->prd_cd)
				->count();

		if($storage_stock_cnt < 1) {
			// 해당 창고에 상품 기존재고가 없을 경우
			DB::table('product_stock_storage')
				->insert([
					'goods_no' => $row->goods_no,
					'prd_cd' => $row->prd_cd,
					'storage_cd' => $row->storage_cd,
					'qty' => $f_qty,
					'wqty' => $f_qty,
					'goods_opt' => $row->goods_opt,
					'use_yn' => 'Y',
					'rt' => now()
				]);
		} else {
			// 창고보유재고 재설정 (+ history) 및 실재고 증가
			DB::table('product_stock_storage')
				->where('prd_cd', '=', $row->prd_cd)
				->where('storage_cd', '=', $row->storage_cd)
				->update([
					'qty' => DB::raw('qty + ' . $f_qty),
					// 231110 ceduce
					//'wqty' => DB::raw('wqty - ' . ($p_qty - $f_qty)),
					'wqty' => DB::raw('wqty + ' . $f_qty),
					'ut' => now(),
				]);
		}

		//if (($p_qty - $f_qty) > 0 || ($p_qty - $f_qty) < 0) {
		if ($f_qty > 0 || $f_qty < 0) {
			DB::table('product_stock_hst')
				->insert([
					'goods_no' => $row->goods_no,
					'prd_cd' => $row->prd_cd,
					'goods_opt' => $row->goods_opt,
					'location_cd' => $row->storage_cd,
					'location_type' => 'STORAGE',
					'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(입고)
					'price' => $row->price,
					'wonga' => $row->wonga,
					// 231110 ceduce
					//'qty' => ($p_qty - $f_qty) * -1,
					'qty' => $f_qty,
					'stock_state_date' => date('Ymd'),
					'r_stock_state_date' => date('Ymd'),
					'ord_opt_no' => '',
					'store_return_no'	=> $sr_prd_cd,
					'comment' => '매장반품확정',
					'rt' => now(),
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
				]);
		}

		// 전체재고 중 창고재고 업데이트
		DB::table('product_stock')
			->where('prd_cd', '=', $row->prd_cd)
			->update([
				// 231110 ceduce
				//'wqty' => DB::raw('wqty - ' . ($p_qty - $f_qty)),
				'wqty' => DB::raw('wqty + ' . $f_qty),
				'ut' => now(),
			]);

		return 1;
	}

	/** 창고반품 삭제 */
	public function del_return(Request $request)
	{
		$sr_cds = $request->input("sr_cds", []);
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		try {
			DB::beginTransaction();

			foreach($sr_cds as $sr_cd) {
				$sql = "
					select srp.prd_cd
						 , pc.goods_no
						 , pc.goods_opt
						 , srp.return_price as price
						 , g.wonga
						 , srp.return_p_qty
						 , srp.fixed_return_qty
						 , sr.store_cd
						 , sr.storage_cd
						 , sr.sr_state
						 , srp.sr_prd_cd
					from store_return_product srp
						inner join store_return sr on sr.sr_cd = srp.sr_cd
						inner join product_code pc on pc.prd_cd = srp.prd_cd
						inner join goods g on g.goods_no = pc.goods_no
                    where srp.sr_cd = :sr_cd
                ";
				$products = DB::select($sql, [ 'sr_cd' => $sr_cd ]);

				// 반품정보 삭제 (재고처리)
				foreach ($products as $prd) {
					if ($prd->sr_state < 30) continue;

					// 원복수량: 반품완료상태일 때는 확정수량, 반품처리중상태일 때는 처리수량
					$return_qty = $prd->sr_state >= 40 ? ($prd->fixed_return_qty ?? 0) : 0; // 실재고
					$return_wqty = $prd->sr_state >= 40 ? ($prd->fixed_return_qty ?? 0) : ($prd->return_p_qty ?? 0); // 보유재고

					// 매장보유재고 환원 (+ history)
					DB::table('product_stock_store')
						->where('prd_cd', $prd->prd_cd)
						->where('store_cd', $prd->store_cd)
						->update([
							'qty' => DB::raw('qty + ' . $return_qty),
							'wqty' => DB::raw('wqty + ' . $return_wqty),
							'ut' => now(),
						]);
					if ($return_wqty > 0 || $return_wqty < 0) {
						DB::table('product_stock_hst')
							->insert([
								'goods_no' => $prd->goods_no,
								'prd_cd' => $prd->prd_cd,
								'goods_opt' => $prd->goods_opt,
								'location_cd' => $prd->store_cd,
								'location_type' => 'STORE',
								'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품
								'price' => $prd->price,
								'wonga' => $prd->wonga,
								'qty' => $return_wqty * 1,
								'stock_state_date' => date('Ymd'),
								'r_stock_state_date' => date('Ymd'),
								'ord_opt_no' => '',
								'store_return_no'	=> $prd->sr_prd_cd,
								'comment' => '매장반품삭제',
								'rt' => now(),
								'admin_id' => $admin_id,
								'admin_nm' => $admin_nm,
							]);
					}

					if($prd->sr_state >= "40"){

						// 창고보유재고 차감 (+ history)
						DB::table('product_stock_storage')
							->where('prd_cd', '=', $prd->prd_cd)
							->where('storage_cd', '=', $prd->storage_cd)
							->update([
								'qty' => DB::raw('qty - ' . $return_qty),
								'wqty' => DB::raw('wqty - ' . $return_wqty),
								'ut' => now(),
							]);
						if ($return_wqty > 0 || $return_wqty < 0) {
							DB::table('product_stock_hst')
								->insert([
									'goods_no' => $prd->goods_no,
									'prd_cd' => $prd->prd_cd,
									'goods_opt' => $prd->goods_opt,
									'location_cd' => $prd->storage_cd,
									'location_type' => 'STORAGE',
									'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품
									'price' => $prd->price,
									'wonga' => $prd->wonga,
									'qty' => $return_wqty * -1,
									'stock_state_date' => date('Ymd'),
									'r_stock_state_date' => date('Ymd'),
									'ord_opt_no' => '',
									'store_return_no'	=> $prd->sr_prd_cd,
									'comment' => '매장반품삭제',
									'rt' => now(),
									'admin_id' => $admin_id,
									'admin_nm' => $admin_nm,
								]);
						}

						// 전체재고 중 창고재고 업데이트
						DB::table('product_stock')
							->where('prd_cd', '=', $prd->prd_cd)
							->update([
								'wqty' => DB::raw('wqty - ' . $return_wqty),
								'ut' => now(),
							]);

					}
				}

				// 반품정보 삭제
				DB::table('store_return')
					->where('sr_cd', '=', $sr_cd)
					->delete();

				DB::table('store_return_product')
					->where('sr_cd', '=', $sr_cd)
					->delete();
			}

			DB::commit();
			$code = 200;
			$msg = "정상적으로 삭제되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	public function show_batch()
	{
		$storages = DB::table("storage")
			->where('use_yn', '=', 'Y')
			->whereIn('storage_cd', ['S0006', 'C0005', 'A0009'])
			->select('storage_cd', 'storage_nm as storage_nm', 'default_yn')
			->orderByDesc('default_yn')
			->get();

		$values = [
			'sdate'         => date("Y-m-d"),
			'storages'      => $storages,
			'sr_reasons'    => SLib::getCodes("SR_REASON"),
			'return_storage_cd' => 'S0006', // 반품창고
		];
		return view(Config::get('shop.store.view') . '/stock/stk35_batch', $values);
	}

	/** 일괄등록 시 Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환 */
	public function import_excel(Request $request) {
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();

				$save_path = "data/store/stk30/";
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

	/** 일괄등록 상품 개별 조회 */
	public function get_goods(Request $request) {
		$storage_cd = $request->input('storage_cd', '');

		$data = $request->input('data', []);
		$result = [];

		$storage = DB::table('storage')->where('storage_cd', $storage_cd)->select('storage_cd', 'storage_nm')->first();

		foreach ($data as $key => $d) {
			$prd_cd = $d['prd_cd'];
			$qty = $d['qty'] ?? 0;
			$count = $d['count'] ?? '';
			$store_cd = $d['store_cd'] ?? '';

			$store = DB::table('store')->where('store_cd', $store_cd)->select('store_cd', 'store_nm')->first();

			if ($store == null || $storage== null) {
				return response()->json(['code' => 404, 'msg' => '매장반품 기본정보가 올바르지 않습니다. 반품창고와 보내는매장 항목을 확인해주세요.']);
			}

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
                    , pc.size
                    , pc.goods_opt
                    , if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh
                    , if(g.goods_no <> '0', g.price, p.price) as price
                    , p.price as return_price
                    , '$store_cd' as store_cd
                    , '$store->store_nm' as store_nm
                    , pss.qty as store_qty
                    , pss.wqty as store_wqty
                    , '$qty' as qty
                    , (if(g.goods_no <> '0', g.price, p.price) * $qty) as return_amt
                    , true as isEditable
                    , '$count' as count
                from product_code pc
                    inner join product p on p.prd_cd = pc.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd and pss.store_cd = '$store_cd'
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
	
	public function add_row(Request $request) {
		$prd_cd = $request->input('prd_cd', []);
		$store_cd = $request->input('store_cd', []);

		$prd_cds = explode(',', $prd_cd);
		$result = [];

		foreach ($prd_cds as $key => $pc) {
			
			foreach($store_cd as $sc) {
				$store = DB::table('store')->where('store_cd', $sc)->select('store_cd', 'store_nm')->first();

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
						, pc.size
						, pc.goods_opt
						, if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh
						, if(g.goods_no <> '0', g.price, p.price) as price
						, p.price as return_price
						, '$sc' as store_cd
						, '$store->store_nm' as store_nm
						, ifnull(pss.qty,0) as store_qty
						, ifnull(pss.wqty,0) as store_wqty
						, '0' as qty
						, true as isEditable
					from product_code pc
						inner join product p on p.prd_cd = pc.prd_cd
						left outer join goods g on g.goods_no = pc.goods_no
						left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd and pss.store_cd = '$sc'
						left outer join opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
						left outer join brand b on b.br_cd = pc.brand
					where pc.prd_cd = '$pc'
					limit 1
				";

				$row = DB::selectOne($sql);
				array_push($result, $row);
			}
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

	// 창고반품 거래명세서 출력
	public function download(Request $request)
	{
		$sr_cd = $request->input('sr_cd');

		$sql = "
            select srp.prd_cd
				, g.goods_nm
                , pc.color
                , pc.size
                , if(sr.sr_state = 10, srp.return_qty, if(sr.sr_state = 30, srp.return_p_qty, srp.fixed_return_qty)) * -1 as qty
                , g.price
                , (g.price * if(sr.sr_state = 10, srp.return_qty, if(sr.sr_state = 30, srp.return_p_qty, srp.fixed_return_qty)) * -1) as total_price
			 	, round(g.price / 1.1) as return_price
			 	, round(g.price / 1.1 * if(sr.sr_state = 10, srp.return_qty, if(sr.sr_state = 30, srp.return_p_qty, srp.fixed_return_qty)) * -1) as total_return_price
				, s.store_nm
				, s.addr1
				, s.addr2
				, s.phone
				, s.fax
				, s.biz_no
				, s.biz_ceo
				, s.biz_uptae
				, s.biz_upjong
				, (select concat(addr1, ifnull(addr2, '')) from storage where storage_cd = sr.storage_cd) as storage_addr
				, (select concat(ifnull(ceo, ''), ' ', phone) from storage where storage_cd = sr.storage_cd) as storage_manager
            from store_return_product srp
                inner join product_code pc on pc.prd_cd = srp.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join store_return sr on sr.sr_cd = srp.sr_cd
				inner join store s on s.store_cd = sr.store_cd
            where srp.sr_cd = :sr_cd
        ";
		$rows = DB::select($sql, ['sr_cd' => $sr_cd]);

		$data = [
			'one_sheet_count' => 36,
			'document_number' => sprintf('%04d', $sr_cd),
			'products' => $rows
		];

		if (count($rows) > 0) {
			$data['receipt_date']		= date('Y-m-d'); // 반품요청일자? 반품처리일자? 반품완료일자? 논의 후 수정필요
			$data['store_nm'] 			= $rows[0]->store_nm ?? '';
			$data['store_addr'] 		= ($rows[0]->addr1 ?? '') . ($rows[0]->addr2);
			$data['store_phone'] 		= $rows[0]->phone ?? '';
			$data['store_fax'] 			= $rows[0]->fax ?? '';
			$data['biz_no'] 			= $rows[0]->biz_no ?? '';
			$data['biz_ceo'] 			= $rows[0]->biz_ceo ?? '';
			$data['biz_uptae'] 			= $rows[0]->biz_uptae ?? '';
			$data['biz_upjong'] 		= $rows[0]->biz_upjong ?? '';
			$data['storage_addr'] 		= $rows[0]->storage_addr ?? '';
			$data['storage_manager'] 	= $rows[0]->storage_manager ?? '';

			$conf = new Conf();
			$company = $conf->getConfig('shop');
			$data['business_registration_number'] = $company['business_registration_number'];
			$data['company_name'] = $company['company_name'];
			$data['company_ceo_name'] = $company['company_ceo_name'];
			$data['company_address'] = $company['company_address'];

			/* 하단 정보는 값등록 후 수정이 필요합니다. */
			$data['company_uptae'] = '도소매';
			$data['company_upjong'] = '의류,신발,악세서리';
			$data['company_office_phone'] = '02) 332-0018';
			$data['company_fax'] = '';
			$data['company_bank_number'] = '국민은행 / 730637-04-005212 / (주) 알펜인터내셔널';
			/* 상단 정보는 값등록 후 수정이 필요합니다. */
		}

		$style = [
			'A1:AH50' => [
				'alignment' => [
					'vertical' => Alignment::VERTICAL_CENTER,
					'horizontal' => Alignment::HORIZONTAL_CENTER
				],
				'font' => [ 'size' => 22, 'name' => '굴림' ]
			],
			'A3:AH3' => [
				'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ],
				'font' => [ 'size' => 25 ]
			],
			'A4' => [ 'alignment' => [ 'textRotation' => true ] ],
			'R4' => [ 'alignment' => [ 'textRotation' => true ] ],
			'A4:AH50' => [
				'borders' => [
					'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ],
					'outline' => [ 'borderStyle' => Border::BORDER_THICK ],
				],
			],
			'M5:Q5' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'AD5:AH5' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'AC47:AH50' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'A9:AH9' => [ 'borders' => [ 'top' => [ 'borderStyle' => Border::BORDER_THICK ] ] ],
			'E5:E6' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'V5:V6' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'F10:F45' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'W10:AH46' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_RIGHT ] ],
			'B5:B8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'S5:S8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'J5:J8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'AA5:AA8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'A46' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 70 ] ],
			'A47:A49' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 10 ] ],
			'V5' => [ 'font' => [ 'size' => 18 ] ],
			'V6' => [ 'font' => [ 'size' => 18 ] ],
			'Q5' => [ 'font' => [ 'size' => 16 ] ],
			'AH5' => [ 'font' => [ 'size' => 16 ] ],
			'B10:Q45' => [ 'font' => [ 'size' => 18 ] ],
			'Y10:AH45' => [ 'font' => [ 'size' => 19 ] ],
			'M2:V2' => [ 'borders' => [ 'bottom' => [ 'borderStyle' => Border::BORDER_THIN ] ] ],
			'K1' => [ 'font' => [ 'size' => 50, 'bold' => true ] ],
		];

		$view_url = Config::get('shop.store.view') . '/stock/stk30_document';
		$keys = [ 'list_key' => 'products', 'one_sheet_count' => $data['one_sheet_count'], 'cell_width' => 8, 'cell_height' => 48 ];
		$images = [[ 'title' => '인감도장', 'public_path' => '/img/stamp.png', 'cell' => 'P4', 'height' => 150 ]];

		return Excel::download(new ExcelViewExport($view_url, $data, $style, $images, $keys), '반품거래명세서.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
}
