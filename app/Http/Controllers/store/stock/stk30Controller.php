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

const PRODUCT_STOCK_TYPE_RETURN = 11; // 창고반품 (매장->창고)

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
                g.goods_nm_eng,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                pc.color,
                pc.size,
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
        $admin_nm = Auth('head')->user()->name;
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
                            sr.sr_cd, 
                            sr.sr_prd_cd,
                            sr.prd_cd,
                            sr.return_qty,
                            pc.goods_opt,
                            g.goods_no,
                            g.price,
                            g.wonga
                        from store_return_product sr
                            inner join product_code pc on pc.prd_cd = sr.prd_cd
                            inner join goods g on g.goods_no = pc.goods_no
                        where sr.sr_cd = :sr_cd
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

                            // 재고이력 등록
                            DB::table('product_stock_hst')
                                ->insert([
                                    'goods_no' => $row->goods_no,
                                    'prd_cd' => $row->prd_cd,
                                    'goods_opt' => $row->goods_opt,
                                    'location_cd' => $d['store_cd'],
                                    'location_type' => 'STORE',
                                    'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(출고)
                                    'price' => $row->price,
                                    'wonga' => $row->wonga,
                                    'qty' => ($row->return_qty ?? 0) * -1,
                                    'stock_state_date' => date('Ymd'),
                                    'ord_opt_no' => '',
                                    'comment' => '창고반품',
                                    'rt' => now(),
                                    'admin_id' => $admin_id,
                                    'admin_nm' => $admin_nm,
                                ]);

                            // 창고 재고 플러스
                            DB::table('product_stock_storage')
                                ->where('prd_cd', '=', $row->prd_cd)
                                ->where('storage_cd', '=', $d['storage_cd']) 
                                ->update([
                                    'wqty' => DB::raw('wqty + ' . ($row->return_qty ?? 0)),
                                    'ut' => now(),
                                ]);
                            
                            // 재고이력 등록
                            DB::table('product_stock_hst')
                                ->insert([
                                    'goods_no' => $row->goods_no,
                                    'prd_cd' => $row->prd_cd,
                                    'goods_opt' => $row->goods_opt,
                                    'location_cd' => $d['storage_cd'],
                                    'location_type' => 'STORAGE',
                                    'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품(입고)
                                    'price' => $row->price,
                                    'wonga' => $row->wonga,
                                    'qty' => $row->return_qty ?? 0,
                                    'stock_state_date' => date('Ymd'),
                                    'ord_opt_no' => '',
                                    'comment' => '매장반품',
                                    'rt' => now(),
                                    'admin_id' => $admin_id,
                                    'admin_nm' => $admin_nm,
                                ]);

                            // product_stock -> 창고보유재고 플러스
                            DB::table('product_stock')
                                ->where('prd_cd', '=', $row->prd_cd)
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
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        try {
            DB::beginTransaction();

            foreach($sr_cds as $sr_cd) {
                $sr = DB::table('store_return')->where('sr_cd', $sr_cd)->first();

                $sql = "
                    select srp.prd_cd, pc.goods_no, pc.goods_opt, srp.price, g.wonga, srp.return_qty
                    from store_return_product srp
                        inner join product_code pc on pc.prd_cd = srp.prd_cd
                        left outer join goods g on g.goods_no = pc.goods_no
                    where srp.sr_cd = :sr_cd
                ";
                $products = DB::select($sql, ['sr_cd' => $sr_cd]);

                if ($sr->sr_state >= 30) {
                    // 반품정보 삭제 (재고처리)
                    foreach ($products as $prd) {
                        // 매장에 재고환원
                        DB::table('product_stock_store')
                            ->where('prd_cd', $prd->prd_cd)
                            ->where('store_cd', $sr->store_cd)
                            ->update([
                                'qty' => DB::raw('qty + ' . ($prd->return_qty ?? 0)),
                                'wqty' => DB::raw('wqty + ' . ($prd->return_qty ?? 0)),
                                'ut' => now(),
                            ]);
                        // 매장 재고이력 등록
                        DB::table('product_stock_hst')
                            ->insert([
                                'goods_no' => $prd->goods_no,
                                'prd_cd' => $prd->prd_cd,
                                'goods_opt' => $prd->goods_opt,
                                'location_cd' => $sr->store_cd,
                                'location_type' => 'STORE',
                                'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품
                                'price' => $prd->price,
                                'wonga' => $prd->wonga,
                                'qty' => ($prd->return_qty ?? 0) * 1,
                                'stock_state_date' => date('Ymd'),
                                'ord_opt_no' => '',
                                'comment' => '창고반품삭제',
                                'rt' => now(),
                                'admin_id' => $admin_id,
                                'admin_nm' => $admin_nm,
                            ]);
                        // 창고 재고차감
                        $update_values = [
                            'wqty' => DB::raw('wqty - ' . ($prd->return_qty ?? 0)),
                            'ut' => now(),
                        ];
                        if ($sr->sr_state == 40) {
                            $update_values = array_merge($update_values, [
                                'qty' => DB::raw('qty - ' . ($prd->return_qty ?? 0)),
                            ]);
                        }
                        DB::table('product_stock_storage')
                            ->where('prd_cd', '=', $prd->prd_cd)
                            ->where('storage_cd', '=', $sr->storage_cd) 
                            ->update($update_values);
                        // 창고 재고이력 등록
                        DB::table('product_stock_hst')
                            ->insert([
                                'goods_no' => $prd->goods_no,
                                'prd_cd' => $prd->prd_cd,
                                'goods_opt' => $prd->goods_opt,
                                'location_cd' => $sr->store_cd,
                                'location_type' => 'STORAGE',
                                'type' => PRODUCT_STOCK_TYPE_RETURN, // 재고분류 : 반품
                                'price' => $prd->price,
                                'wonga' => $prd->wonga,
                                'qty' => ($prd->return_qty ?? 0) * -1,
                                'stock_state_date' => date('Ymd'),
                                'ord_opt_no' => '',
                                'comment' => '매장반품삭제',
                                'rt' => now(),
                                'admin_id' => $admin_id,
                                'admin_nm' => $admin_nm,
                            ]);
                        // 창고재고 업데이트
                        DB::table('product_stock')
                            ->where('prd_cd', '=', $prd->prd_cd)
                            ->update([
                                'wqty' => DB::raw('wqty - ' . ($prd->return_qty ?? 0)),
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
            $msg = "삭제가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    public function show_batch()
    {
        return view(Config::get('shop.store.view') . '/stock/stk30_batch');
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
        $sr_date = $request->input('sr_date', '');
        $storage_cd = $request->input('storage_cd', '');
        $store_cd = $request->input('store_cd', '');
        $sr_reason = $request->input('sr_reason', '');
        $comment = $request->input('comment', '');

        $data = $request->input('data', []);
        $result = [];
        
        $store = DB::table('store')->where('store_cd', $store_cd)->select('store_cd', 'store_nm')->first();
        $storage = DB::table('storage')->where('storage_cd', $storage_cd)->select('storage_cd', 'storage_nm')->first();

        if ($store == null || $storage== null || $sr_reason == null || $sr_date == null) {
            return response()->json(['code' => 404, 'msg' => '창고반품 기본정보가 올바르지 않습니다. 반품일자/반품창고코드/매장코드/반품사유 항목을 확인해주세요.']);
        }

        foreach ($data as $key => $d) {
            $prd_cd = $d['prd_cd'];
            $qty = $d['qty'] ?? 0;
            $count = $d['count'] ?? '';

            $sql = "
                select
                    pc.prd_cd
                    , pc.goods_no
                    , opt.opt_kind_nm
                    , b.brand_nm as brand
                    , if(g.goods_no <> '0', g.style_no, p.style_no) as style_no
                    , if(g.goods_no <> '0', g.goods_nm, p.prd_nm) as goods_nm
                    , if(g.goods_no <> '0', g.goods_nm_eng, p.prd_nm) as goods_nm_eng
                    , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                    , pc.color
                    , pc.size
                    , pc.goods_opt
                    , if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh
                    , if(g.goods_no <> '0', g.price, p.price) as price
                    , p.price as return_price
                    , pss.wqty as store_wqty
                    , '$qty' as qty
                    , (if(g.goods_no <> '0', g.price, p.price) * $qty) as total_return_price
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
            "body" => $result
        ]);
    }

    // 창고일괄반품 등록
    public function save(Request $request)
    {
        $sr_kind = "G"; // 일반(G)/일괄(B)
        $sr_date = $request->input("sr_date", date("Y-m-d"));
        $store_cd = $request->input("store_cd", "");
        $storage_cd = $request->input("storage_cd", "");
        $sr_reason = $request->input("sr_reason", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);
        $admin_id = Auth('head')->user()->id;
        $sr_state = 10;

        try {
            DB::beginTransaction();

            foreach($products as $product) {
                if ($product['store_wqty'] < $product['return_qty']){
                    $code = 501;
                    $msg = '반품수량이 보유재고보다 많음';
                }

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
                if ($product['store_wqty'] < $product['return_qty']) {
                    $code = 501;
                    $msg = '';
                } else {
                    DB::table('store_return_product')
                        ->insert([
                            'sr_cd' => $sr_cd,
                            'prd_cd' => $product['prd_cd'],
                            'price' => $product['price'],
                            'return_price' => $product['return_price'],
                            'return_qty' => $product['return_qty'],
                            'rt' => now(),
                            'admin_id' => $admin_id
                        ]);

                    DB::commit();
                    $code = 200;
                    $msg = '';
                }
            }
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
