<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class stk30Controller extends Controller
{
    public function index()
	{
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();

		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'storages'      => $storages,
            'store_types'	=> SLib::getCodes("STORE_TYPE"),	// 매장구분
            'sr_states'	    => SLib::getCodes("SR_CODE"),	// 반품상태
            'sr_reasons'	=> SLib::getCodes("SR_REASON"),	// 반품사유
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/stock/stk30', $values);
	}

    public function search(Request $request)
    {
		$where = "";
        $orderby = "";

        $sdate      = $request->input("sdate", now()->sub(1, 'week')->format('Ymd'));
        $edate      = $request->input("edate", date("Ymd"));
        $sr_state   = $request->input("sr_state", "");
        $sr_reason  = $request->input("sr_reason", "");
        $storage_cd = $request->input("storage_cd", "");
        $store_type = $request->input("store_type", "");
        $store_nm   = $request->input("store_nm", "");
        $store_no   = $request->input("store_no", "");
        
        // where
        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);
        $where .= "
            and cast(sr.sr_date as date) >= '$sdate' 
            and cast(sr.sr_date as date) <= '$edate'
        ";
        if($sr_state != "")     $where .= " and sr.sr_state = '" . $sr_state . "'";
        if($sr_reason != "")    $where .= " and sr.sr_reason = '" . $sr_reason . "'";
        if($storage_cd != "")   $where .= " and sr.storage_cd = '" . $storage_cd . "'";
        if($store_type != "")   $where .= " and store.store_type = '" . $store_type . "'";
        if($store_no != "")     $where .= " and sr.store_cd = '" . $store_no . "'";

        // ordreby
        $ord_field  = $request->input("ord_field", "sr.sr_cd");
        if($ord_field == 'sr_cd') $ord_field = 'sr.' . $ord_field;
        $ord        = $request->input("ord", "desc");
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
                sc.code_val as store_type_nm,
                sr.sr_date,
                sr.sr_kind,
                sr.sr_state,
                c.code_val as sr_state_nm,
                (select sum(return_price * return_qty) from store_return_product where sr_cd = sr.sr_cd) as sr_price,
                (select sum(return_qty) from store_return_product where sr_cd = sr.sr_cd) as sr_qty,
                sr.sr_reason,
                co.code_val as sr_reason_nm,
                sr.comment
            from store_return sr
                inner join storage on storage.storage_cd = sr.storage_cd
                inner join store on store.store_cd = sr.store_cd
                inner join code c on c.code_kind_cd = 'SR_CODE' and c.code_id = sr.sr_state
                inner join code co on co.code_kind_cd = 'SR_REASON' and co.code_id = sr.sr_reason
                inner join code sc on sc.code_kind_cd = 'STORE_TYPE' and sc.code_id = store.store_type
            where 1=1 $where
            $orderby
            $limit
		";
		$result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                from store_return sr
                    inner join storage on storage.storage_cd = sr.storage_cd
                    inner join store on store.store_cd = sr.store_cd
                    inner join code c on c.code_kind_cd = 'SR_CODE' and c.code_id = sr.sr_state
                    inner join code co on co.code_kind_cd = 'SR_REASON' and co.code_id = sr.sr_reason
                where 1=1 $where
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
    public function show($sr_cd = '') 
    {
        $sr = '';
        $new_sr_cd = '';
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();

        if($sr_cd != '') {
            $sql = "
                select
                    sr.sr_cd,
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
        } else {
            $sql = "
                select sr_cd
                from store_return
                order by sr_cd desc
                limit 1
            ";
            $row = DB::selectOne($sql);
            if($row == null) $new_sr_cd = 1;
            else $new_sr_cd = $row->sr_cd + 1;
        }

        $values = [
            "cmd" => $sr == '' ? "add" : "update",
            'sdate'         => $sr == '' ? date("Y-m-d") : $sr->sr_date,
            'storages'      => $storages,
            'sr_reasons'    => SLib::getCodes("SR_REASON"),
            'sr'            => $sr,
            'new_sr_cd'     => $new_sr_cd,
        ];
        return view(Config::get('shop.store.view') . '/stock/stk30_show', $values);
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
                pc.goods_opt,
                g.goods_sh,
                srp.price,
                srp.return_price, 
                ifnull(pss.wqty, 0) as store_wqty, 
                srp.return_qty as qty,
                (srp.return_qty * srp.return_price) as total_return_price,
                true as isEditable
            from store_return_product srp
                inner join product_code pc on pc.prd_cd = srp.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                left outer join store_return sr on sr.sr_cd = srp.sr_cd
                left outer join product_stock_store pss on pss.store_cd = sr.store_cd and pss.prd_cd = srp.prd_cd
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id,
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

    // 창고반품 등록
    public function add_store_return(Request $request)
    {
        $admin_id = Auth('head')->user()->id;
        $sr_kind = "S";
        $sr_state = 10; // 반품등록 시 요청 상태로 등록
        $sr_date = $request->input("sr_date", date("Y-m-d"));
        $storage_cd = $request->input("storage_cd", "");
        $store_cd = $request->input("store_cd", "");
        $sr_reason = $request->input("sr_reason", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);

        try {
            DB::beginTransaction();

            if(count($products) < 1) {
                throw new Exception("반품등록할 상품을 선택해주세요.");
            }

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

			foreach($products as $product) {
                DB::table('store_return_product')
                    ->insert([
                        'sr_cd' => $sr_cd,
                        'prd_cd' => $product['prd_cd'],
                        'price' => $product['price'], // 판매가
                        'return_price' => $product['return_price'], // 반품단가
                        'return_qty' => $product['return_qty'], // 반품수량
                        'rt' => now(),
                        'admin_id' => $admin_id,
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "창고반품등록이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 창고반품 수정
    public function update_store_return(Request $request)
    {
        $admin_id = Auth('head')->user()->id;
        $sr_cd = $request->input("sr_cd", "");
        $sr_reason = $request->input("sr_reason", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);

        try {
            DB::beginTransaction();

            DB::table('store_return')
                ->where('sr_cd', '=', $sr_cd)
                ->update([
                    'sr_reason' => $sr_reason,
                    'comment' => $comment,
                    'ut' => now(),
                    'admin_id' => $admin_id,
                ]);

			foreach($products as $product) {
                DB::table('store_return_product')
                    ->where('sr_prd_cd', '=', $product['sr_prd_cd'])
                    ->update([
                        'return_price' => $product['return_price'], // 반품단가
                        'return_qty' => $product['return_qty'], // 반품수량
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "창고반품수정이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 창고반품 상태변경
    public function update_return_state(Request $request)
    {
        $new_state = $request->input("new_state", "");
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

            if($new_state != "") {
                foreach($data as $d) {
                    DB::table('store_return')
                        ->where('sr_cd', '=', $d['sr_cd'])
                        ->update([
                            'sr_state' => $new_state,
                            'ut' => now(),
                            'admin_id' => $admin_id,
                        ]);
                    
                    $sql = "
                        select
                            sr_cd, 
                            sr_prd_cd,
                            prd_cd,
                            return_qty
                        from store_return_product
                        where sr_cd = :sr_cd
                    ";
                    $rows = DB::select($sql, ["sr_cd" => $d['sr_cd']]);

                    if($new_state == 30) {
                        // 이동처리
                        foreach($rows as $row) {
                            // 매장 재고,실재고 차감
                            DB::table('product_stock_store')
                                ->where('prd_cd', '=', $row->prd_cd)
                                ->where('store_cd', '=', $d['store_cd']) 
                                ->update([
                                    'qty' => DB::raw('qty - ' . ($row->return_qty ?? 0)),
                                    'wqty' => DB::raw('wqty - ' . ($row->return_qty ?? 0)),
                                    'ut' => now(),
                                ]);

                            // 창고 재고 플러스
                            DB::table('product_stock_storage')
                                ->where('prd_cd', '=', $row->prd_cd)
                                ->where('storage_cd', '=', $d['storage_cd']) 
                                ->update([
                                    'wqty' => DB::raw('wqty + ' . ($row->return_qty ?? 0)),
                                    'ut' => now(),
                                ]);
                        }
                    } else if($new_state == 40) {
                        // 완료처리
                        foreach($rows as $row) {
                            // 창고 실재고 플러스
                            DB::table('product_stock_storage')
                                ->where('prd_cd', '=', $row->prd_cd)
                                ->where('storage_cd', '=', $d['storage_cd']) 
                                ->update([
                                    'qty' => DB::raw('qty + ' . ($row->return_qty ?? 0)),
                                    'ut' => now(),
                                ]);
                        }
                    }
                }
            }

			DB::commit();
            $code = 200;
            $msg = "창고반품 상태변경이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 창고반품 삭제
    public function del_return(Request $request)
    {
        $sr_cds = $request->input("sr_cds", []);

        try {
            DB::beginTransaction();

            foreach($sr_cds as $sr_cd) {
                DB::table('store_return')
                    ->where('sr_cd', '=', $sr_cd)
                    ->delete();

                DB::table('store_return_product')
                    ->where('sr_cd', '=', $sr_cd)
                    ->delete();
            }

			DB::commit();
            $code = 200;
            $msg = "삭제가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
