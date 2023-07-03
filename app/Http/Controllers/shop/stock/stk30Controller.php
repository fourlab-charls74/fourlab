<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Conf;
use App\Exports\ExcelViewExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

const PRODUCT_STOCK_TYPE_RETURN = 11; // 창고반품 (매장->창고)

class stk30Controller extends Controller
{
    public function index()
	{
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')
			->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')
			->orderByDesc('default_yn')->get();

		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'storages'      => $storages,
            'sr_states'	    => SLib::getCodes("SR_CODE"),	// 반품상태
            'sr_reasons'	=> SLib::getCodes("SR_REASON"),	// 반품사유
		];
        return view(Config::get('shop.shop.view') . '/stock/stk30', $values);
	}

    public function search(Request $request)
    {
        $sdate      = $request->input("sdate", now()->sub(1, 'week')->format('Ymd'));
        $edate      = $request->input("edate", date("Ymd"));
        $sr_state   = $request->input("sr_state", "");
        $sr_reason  = $request->input("sr_reason", "");
        $storage_cd = $request->input("storage_cd", "");
        $store_cd   = Auth('head')->user()->store_cd;
        
        // where
		$where = "";

        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);
        $where .= "
            and cast(sr.sr_date as date) >= '$sdate' 
            and cast(sr.sr_date as date) <= '$edate'
        ";
        if($sr_state != "")     $where .= " and sr.sr_state = '" . Lib::quote($sr_state) . "'";
        if($sr_reason != "")    $where .= " and sr.sr_reason = '" . Lib::quote($sr_reason) . "'";
        if($storage_cd != "")   $where .= " and sr.storage_cd = '" . Lib::quote($storage_cd) . "'";
        if($store_cd != "")     $where .= " and sr.store_cd = '" . Lib::quote($store_cd) . "'";

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
                sr.sr_date,
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

    // 창고반품 등록 & 상세팝업 오픈
    public function show($sr_cd) 
    {
        $sr = '';
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')
			->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')
			->orderByDesc('default_yn')->get();

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
            $sr = DB::selectOne($sql, ['sr_cd' => $sr_cd]);
        }

        $values = [
            'sdate'         => $sr->sr_date ?? '',
            'storages'      => $storages,
            'sr_reasons'    => SLib::getCodes("SR_REASON"),
            'sr'            => $sr,
            'sr_state'      => $sr->sr_state ?? '',
			'reject_reasons' => SLib::getCodes('SR_REJECT_REASON'),
        ];
        return view(Config::get('shop.shop.view') . '/stock/stk30_show', $values);
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
                left outer join store_return sr on sr.sr_cd = srp.sr_cd
                left outer join product_stock_store pss on pss.store_cd = sr.store_cd and pss.prd_cd = srp.prd_cd
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join code reason on reason.code_kind_cd = 'SR_REJECT_REASON' and reason.code_id = srp.reject_reason,
                (select @rownum :=0) as r
            where srp.sr_cd = :sr_cd
        ";
        $products = DB::select($sql, ['sr_cd' => $sr_cd]);

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

	// 창고반품 수정 (+상태변경)
	public function update(Request $request)
	{
		$code = 200;
		$msg = "";

		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$sr_cd = $request->input("sr_cd", "");
		$new_state = $request->input("new_state", "");
		$products = $request->input("products", []);

		try {
			DB::beginTransaction();

			if ($new_state != '') {
				DB::table('store_return')->where('sr_cd', $sr_cd)->update([
					'sr_state' => $new_state,
					'ut' => now(),
					'admin_id' => $admin_id,
				]);
			}

			foreach($products as $product) {
				if ($product['store_wqty'] < $product['return_p_qty']) {
					$code = 501;
					throw new Exception('매장보유재고보다 많은 수량을 반품처리할 수 없습니다.');
				}

				DB::table('store_return_product')
					->where('sr_prd_cd', '=', $product['sr_prd_cd'])
					->update([
						'return_p_qty' => $product['return_p_qty'], // 처리수량
						'reject_reason' => $product['reject_reason'] ?? '', // 반품거부사유
						'reject_comment' => $product['reject_comment'] ?? '', // 반품거부메모
						'ut' => now(),
						'admin_id' => $admin_id,
					]);
				
				// 재고처리
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

	// // 삭제예정최유현
    // public function update_state(Request $request)
    // {
    //     $new_state = $request->input("new_state", "");
    //     $admin_id = Auth('head')->user()->id;
    //     $admin_nm = Auth('head')->user()->name;
    //     $data = $request->input("data", []);
    //     $store_cd = $request->input('store_cd');
    //     $storage_cd = $request->input('storage_cd');
	//
    //     try {
    //         DB::beginTransaction();
	//
    //         if($new_state != "") {
    //             foreach($data as $d) {
    //                 DB::table('store_return')
    //                     ->where('sr_cd', '=', $d['sr_cd'])
    //                     ->update([
    //                         'sr_state' => $new_state,
    //                         'ut' => now(),
    //                         'admin_id' => $admin_id,
    //                     ]);
    //                
    //                 $sql = "
    //                     select
    //                         sr.sr_cd, 
    //                         sr.sr_prd_cd,
    //                         sr.prd_cd,
    //                         sr.return_qty,
    //                         sr.fixed_return_qty,
    //                         pc.goods_opt,
    //                         g.goods_no,
    //                         g.price,
    //                         g.wonga
    //                     from store_return_product sr
    //                         inner join product_code pc on pc.prd_cd = sr.prd_cd
    //                         inner join goods g on g.goods_no = pc.goods_no
    //                     where sr.sr_cd = :sr_cd
    //                 ";
    //                 $rows = DB::select($sql, ["sr_cd" => $d['sr_cd']]);
	//
    //                if($new_state == 40) {
    //                     // 완료처리
    //                     foreach($rows as $row) {
    //                         // 창고 실재고 플러스
    //                         DB::table('product_stock_storage')
    //                             ->where('prd_cd', '=', $row->prd_cd)
    //                             ->where('storage_cd', '=', $storage_cd) 
    //                             ->update([
    //                                 'qty' => DB::raw('qty + ' . ($row->fixed_return_qty ?? 0)),
    //                                 'ut' => now(),
    //                             ]);
    //                     }
    //                 }
    //             }
    //         }
	//
	// 		DB::commit();
    //         $code = 200;
    //         $msg = "창고반품 상태변경이 정상적으로 완료되었습니다.";
	// 	} catch (Exception $e) {
	// 		DB::rollback();
	// 		$code = 500;
	// 		$msg = $e->getMessage();
	// 	}
	//
    //     return response()->json(["code" => $code, "msg" => $msg]);
    // }
	//
    // // 창고반품 상태변경 	// 삭제예정최유현
    // public function update_return_state(Request $request)
    // {
    //     $new_state = $request->input("new_state", "");
    //     $admin_id = Auth('head')->user()->id;
    //     $admin_nm = Auth('head')->user()->name;
    //     $data = $request->input("data", []);
    //     $storage_cd = $request->input('storage_cd');
    //     $sr_cd = $request->input('sr_cd');
    //     $user_store = Auth('head')->user()->store_cd;
	//
    //     try {
    //         DB::beginTransaction();
	//
    //         if ($new_state != "") {
    //             DB::table('store_return')
    //                 ->where('sr_cd', '=', $sr_cd )
    //                 ->update([
    //                     'sr_state' => $new_state,
    //                     'ut' => now(),
    //                     'admin_id' => $admin_id
    //                 ]);
	//
    //             foreach ($data as $d) {
    //                 DB::table('store_return_product')
    //                     ->where('sr_prd_cd', '=', $d['sr_prd_cd'])
    //                     ->update([
    //                         'return_price' => $d['return_price'], // 반품단가
    //                         'return_qty' => $d['qty'], // 반품수량
    //                         'fixed_return_price' => $d['fixed_return_price'],
    //                         'fixed_return_qty' => $d['fixed_return_qty'],
    //                         'fixed_comment' => $d['fixed_comment']??'',
    //                         'ut' => now(),
    //                         'admin_id' => $admin_id,
    //                     ]);
    //             }
	//
    //             $sql = "
    //                     select
    //                         sr.sr_cd,
    //                         sr.sr_prd_cd,
    //                         sr.prd_cd,
    //                         sr.return_qty,
    //                         sr.fixed_return_qty,
    //                         pc.goods_opt,
    //                         g.goods_no,
    //                         g.price,
    //                         g.wonga
    //                     from store_return_product sr
    //                         inner join product_code pc on pc.prd_cd = sr.prd_cd
    //                         inner join goods g on g.goods_no = pc.goods_no
    //                     where sr.sr_cd = :sr_cd
    //                 ";
    //             $rows = DB::select($sql, ["sr_cd" => $sr_cd]);
	//
    //             foreach($rows as $row) {
	//
    //                 // 매장 재고,실재고 차감
    //                 DB::table('product_stock_store')
    //                     ->where('prd_cd', '=', $row->prd_cd)
    //                     ->where('store_cd', '=', $user_store) 
    //                     ->update([
    //                         'qty' => DB::raw('qty - ' . ($row->return_qty ?? 0)),
    //                         'wqty' => DB::raw('wqty - ' . ($row->return_qty ?? 0)),
    //                         'ut' => now(),
    //                     ]);
	//
    //                 // 재고이력 등록
    //                 DB::table('product_stock_hst')
    //                     ->insert([
    //                         'goods_no' => $row->goods_no,
    //                         'prd_cd' => $row->prd_cd,
    //                         'goods_opt' => $row->goods_opt,
    //                         'location_cd' => $user_store,
    //                         'location_type' => 'STORE',
    //                         'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(출고)
    //                         'price' => $row->price,
    //                         'wonga' => $row->wonga,
    //                         'qty' => ($row->return_qty ?? 0) * -1,
    //                         'stock_state_date' => date('Ymd'),
    //                         'ord_opt_no' => '',
    //                         'comment' => '창고반품',
    //                         'rt' => now(),
    //                         'admin_id' => $admin_id,
    //                         'admin_nm' => $admin_nm,
    //                     ]);
	//
    //                 // 창고 재고 플러스
    //                 DB::table('product_stock_storage')
    //                     ->where('prd_cd', '=', $row->prd_cd)
    //                     ->where('storage_cd', '=', $storage_cd) 
    //                     ->update([
    //                         'wqty' => DB::raw('wqty + ' . ($row->return_qty ?? 0)),
    //                         'ut' => now(),
    //                     ]);
    //                
    //                 // 재고이력 등록
    //                 DB::table('product_stock_hst')
    //                     ->insert([
    //                         'goods_no' => $row->goods_no,
    //                         'prd_cd' => $row->prd_cd,
    //                         'goods_opt' => $row->goods_opt,
    //                         'location_cd' => $storage_cd,
    //                         'location_type' => 'STORAGE',
    //                         'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(입고)
    //                         'price' => $row->price,
    //                         'wonga' => $row->wonga,
    //                         'qty' => $row->return_qty ?? 0,
    //                         'stock_state_date' => date('Ymd'),
    //                         'ord_opt_no' => '',
    //                         'comment' => '매장반품',
    //                         'rt' => now(),
    //                         'admin_id' => $admin_id,
    //                         'admin_nm' => $admin_nm,
    //                     ]);
	//
    //                 // product_stock -> 창고보유재고 플러스
    //                 DB::table('product_stock')
    //                     ->where('prd_cd', '=', $row->prd_cd)
    //                     ->update([
    //                         'wqty' => DB::raw('wqty + ' . ($row->return_qty ?? 0)),
    //                         'ut' => now(),
    //                     ]);
    //             }
	//
    //         }
	// 		DB::commit();
    //         $code = 200;
    //         $msg = "창고반품 상태변경이 정상적으로 완료되었습니다.";
	// 	} catch (Exception $e) {
	// 		DB::rollback();
	// 		$code = 500;
	// 		$msg = $e->getMessage();
	// 	}
	//
    //     return response()->json(["code" => $code, "msg" => $msg]);
    // }
	//
	// // 삭제예정최유현
    // public function update_store_return(Request $request)
    // {
    //     $admin_id = Auth('head')->user()->id;
    //     $sr_cd = $request->input("sr_cd", "");
    //     $sr_reason = $request->input("sr_reason", "");
    //     $comment = $request->input("comment", "");
    //     $products = $request->input("products", []);
    //   
    //     try {
    //         DB::beginTransaction();
	//
    //         DB::table('store_return')
    //             ->where('sr_cd', '=', $sr_cd)
    //             ->update([
    //                 'sr_reason' => $sr_reason,
    //                 'comment' => $comment,
    //                 'ut' => now(),
    //                 'admin_id' => $admin_id,
    //             ]);
	//
	// 		foreach($products as $product) {
    //             DB::table('store_return_product')
    //                 ->where('sr_prd_cd', '=', $product['sr_prd_cd'])
    //                 ->update([
    //                     'return_price' => $product['return_price'], // 반품단가
    //                     'return_qty' => $product['return_qty'], // 반품수량
    //                     'ut' => now(),
    //                     'admin_id' => $admin_id,
    //                 ]);
    //         }
	//
	// 		DB::commit();
    //         $code = 200;
    //         $msg = "창고반품 저장이 정상적으로 완료되었습니다.";
	// 	} catch (Exception $e) {
	// 		DB::rollback();
	// 		$code = 500;
	// 		$msg = $e->getMessage();
	// 	}
	//
    //     return response()->json(["code" => $code, "msg" => $msg]);
    // }

	// 창고반품 거래명세서 출력
	public function download(Request $request)
	{
		$sr_cd = $request->input('sr_cd');

		$sql = "
            select srp.prd_cd
				, g.goods_nm
                , pc.color
                , pc.size
                 -- 확정수량? 예정수량? 논의 후 수정필요
				, srp.return_qty * -1 as qty
                , g.price
                , (g.price * srp.return_qty * -1) as total_price
			 	, round(g.price / 1.1) as return_price
			 	, round(g.price / 1.1 * srp.return_qty * -1) as total_return_price
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
