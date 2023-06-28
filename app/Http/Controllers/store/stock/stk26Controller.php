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

const PRODUCT_STOCK_TYPE_LOSS = 14;		// 재고분류 : LOSS
class stk26Controller extends Controller
{
	public function index()
	{
        $sdate = now()->sub(1, 'week')->format('Y-m-d');
        $edate = date('Y-m-d');

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
			'loss_reasons'	=> SLib::getCodes('LOSS_REASON'),
		];
        return view(Config::get('shop.store.view') . '/stock/stk26', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'week')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $sc_cd = $request->input('sc_cd', '');
        $store_cd = $request->input('store_no', '');
        $sc_state = $request->input('sc_state', '');
		$loss_reason = $request->input('loss_reason', '');

        // where
        $where = "";
        $where .= " and s.sc_date >= '$sdate' ";
        $where .= " and s.sc_date <= '$edate' ";
        if($sc_cd != '') $where .= " and s.sc_cd = '$sc_cd' ";
        if($store_cd != '') $where .= " and s.store_cd = '$store_cd' ";
        if($sc_state != '') $where .= " and s.sc_state = '$sc_state' ";
        if($loss_reason != '') $where .= " and sp.loss_reason = '$loss_reason' ";

        $sql = "
            select
                s.sc_date,
                s.sc_type,
                s.sc_cd,
                s.store_cd,
                store.store_nm,
                sum(sp.store_qty) as store_qty,
                sum(sp.qty) as qty,
                sum(sp.loss_rec_qty) as loss_qty,
                sum(sp.loss_price) as loss_price,
                s.sc_state,
                s.md_id,
                m.name as md_nm,
                s.comment
            from stock_check s
                inner join store on store.store_cd = s.store_cd
                inner join mgr_user m on m.id = s.md_id
                inner join stock_check_product sp on sp.sc_cd = s.sc_cd
            where 1=1 $where
            group by s.sc_cd
            order by s.sc_cd desc
        ";

        $result = DB::select($sql);

		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($result),
                'page' => 1,
			],
			'body' => $result
		]);
    }

    public function show($sc_cd = '', Request $request)
    {
        $sc = '';

        if($sc_cd != '') {
            $sql = "
                select
                    s.sc_date,
                    concat(s.store_cd, '_', REPLACE(s.sc_date, '-', '') , '_' , LPAD(s.sc_cd, 3, '0')) as sc_code,
                    s.sc_type,
                    if(s.sc_type = 'G', '일반등록', if(s.sc_type = 'B', '일괄등록', if(s.sc_type = 'C', '바코드등록', '-'))) as sc_type_nm,
                    s.sc_cd,
                    s.store_cd,
                    store.store_nm,
                    s.sc_state,
                    s.md_id,
                    m.name as md_nm,
                    s.comment
                from stock_check s
                    inner join store on store.store_cd = s.store_cd
                    inner join mgr_user m on m.id = s.md_id
                where sc_cd = :sc_cd
            ";
            $sc = DB::selectOne($sql, ['sc_cd' => $sc_cd]);
        }

        $values = [
            "cmd"           => $sc == '' ? "add" : "update",
            'sdate'         => $sc == '' ? date("Y-m-d") : $sc->sc_date,
            'sc'            => $sc,
			'loss_reasons'	=> SLib::getCodes('LOSS_REASON'),
		];
        return view(Config::get('shop.store.view') . '/stock/stk26_show', $values);
    }

    // 기존 실사등록상품정보 불러오기
    public function search_check_products(Request $request)
    {
        $sc_cd = $request->input('sc_cd', '');
        $sql = "
            select 
                @rownum := @rownum + 1 as count,
                s.sc_prd_cd, 
                s.sc_cd, 
                s.prd_cd,
                pc.goods_no,
                g.goods_type,
                op.opt_kind_nm,
                b.brand_nm as brand, 
                if(g.goods_no <> '0', g.style_no, p.style_no) as style_no,
                if(g.goods_no <> '0', g.goods_nm, p.prd_nm) as goods_nm,
                if(g.goods_no <> '0', g.goods_nm_eng, p.prd_nm) as goods_nm_eng,
                pc.goods_opt,
                if(pc.prd_cd_p <> '', pc.prd_cd_p, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt)) as prd_cd_p,
                pc.color,
                pc.size,
                if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh,
                s.price,
                s.qty,
                s.store_qty as store_wqty, 
                s.loss_qty,
                s.loss_rec_qty,
                s.loss_price,
                s.loss_price2,
                s.loss_tag_price,
                s.loss_reason,
                r.code_val as loss_reason_val,
                s.comment
            from stock_check_product s
                inner join product_code pc on pc.prd_cd = s.prd_cd
                inner join product p on p.prd_cd = s.prd_cd
                left outer join goods g on g.goods_no = pc.goods_no
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
               	left outer join code r on r.code_kind_cd = 'LOSS_REASON' and r.code_id = s.loss_reason
                , (select @rownum :=0) as r
            where s.sc_cd = :sc_cd
        ";
        $products = DB::select($sql, ['sc_cd' => $sc_cd]);

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

    // 실사등록
    public function save(Request $request)
    {
        $sc_type = $request->input("sc_type", "G"); // 일반(G)/일괄(B)
        $sc_date = $request->input("sc_date", date("Y-m-d"));
        $store_cd = $request->input("store_cd", "");
        $md_id = $request->input("md_id", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);
        $admin_id = Auth('head')->user()->id;

        try {
            DB::beginTransaction();

            $sc_cd = DB::table('stock_check')
                ->insertGetId([
                    'store_cd' => $store_cd,
                    'md_id' => $md_id,
                    'sc_date' => $sc_date,
                    'sc_type' => $sc_type,
                    'comment' => $comment,
                    'rt' => now(),
                    'admin_id' => $admin_id,
                ]);

            foreach($products as $product) {
                DB::table('stock_check_product')
                    ->insert([
                        'sc_cd' => $sc_cd,
                        'prd_cd' => $product['prd_cd'],
                        'price' => $product['price'],
                        'qty' => $product['qty'],
                        'store_qty' => $product['store_qty'],
                        'loss_qty' => $product['store_qty'] - $product['qty'],
                        'loss_rec_qty' => $product['store_qty'] - $product['qty'],
                        'loss_price' => $product['price'] * ($product['store_qty'] - $product['qty']),
                        'loss_price2' => $product['price'] * $product['store_qty'],
                        'loss_tag_price' => $product['goods_sh'] * $product['store_qty'],
						'loss_reason' => $product['loss_reason'] ?? null,
						'comment' => $product['comment'] ?? null,
                        'rt' => now(),
                        'admin_id' => $admin_id,
                    ]);
            }

			DB::commit();
            $code = '200';
            $msg = '';
		} catch (Exception $e) {
			DB::rollback();
			$code = '500';
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 실사정보 수정
    public function update(Request $request)
    {
        $sc_cd = $request->input("sc_cd", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);
        $admin_id = Auth('head')->user()->id;

        try {
            DB::beginTransaction();

            DB::table('stock_check')
                ->where('sc_cd', '=', $sc_cd)
                ->update([
                    'comment' => $comment,
                    'ut' => now(),
                    'admin_id' => $admin_id,
                ]);

			foreach($products as $product) {
                DB::table('stock_check_product')
                    ->where('sc_prd_cd', '=', $product['sc_prd_cd'])
                    ->update([
                        'qty' => $product['qty'],
						'loss_qty' => DB::raw("store_qty - qty"),
						'loss_rec_qty' => $product['loss_rec_qty'],
						'loss_price' => DB::raw("loss_rec_qty * price"),
						'loss_reason' => $product['loss_reason'] ?? null,
						'comment' => $product['comment'] ?? null,
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);
            }

			DB::commit();
            $code = '200';
            $msg = '';
		} catch (Exception $e) {
			DB::rollback();
			$code = '500';
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 실사정보 삭제 (LOSS미등록시에만)
    public function delete(Request $request)
    {
        $code = '';
        $msg = '';
        $sc_cds = $request->input('sc_cds', []);
        
        try {
            DB::beginTransaction();

            foreach ($sc_cds as $sc_cd) {
                if ($sc_cd == '') throw new Exception("삭제할 실사정보가 존재하지 않는 항목이 있습니다.");
    
                $sc_state = DB::table('stock_check')->where('sc_cd', $sc_cd)->value('sc_state');
                if ($sc_state != 'N') throw new Exception("LOSS등록된 실사정보는 삭제할 수 없습니다.");
    
                // 삭제
                DB::table('stock_check')->where('sc_cd', $sc_cd)->delete();
                DB::table('stock_check_product')->where('sc_cd', $sc_cd)->delete();
            }

			DB::commit();
            $code = '200';
		} catch (Exception $e) {
			DB::rollback();
			$code = '500';
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

	// LOSS 등록
	public function save_loss(Request $request)
	{
		$code 		= 200;
		$msg		= "";
		$sc_cd 		= $request->input('sc_cd');
		$store_cd   = $request->input('store_cd');
		$comment    = $request->input('comment');
		$products 	= $request->input("products", []);
		$admin_id   = Auth('head')->user()->id;
		$admin_nm   = Auth('head')->user()->name;

		try {
			DB::beginTransaction();

			DB::table('stock_check')
				->where('sc_cd', '=', $sc_cd)
				->update([
					'sc_state' => 'Y',
					'comment' => $comment,
					'ut' => now(),
					'admin_id' => $admin_id,
				]);

			foreach ($products as $product) {
				
				DB::table('stock_check_product')
					->where('sc_prd_cd', '=', $product['sc_prd_cd'])
					->update([
						'loss_rec_qty' => $product['loss_rec_qty'],
						'qty' => DB::raw("store_qty - loss_rec_qty"),
						'loss_qty' => DB::raw("store_qty - " . $product['qty']),
						'loss_price' => DB::raw("loss_rec_qty * price"),
						'loss_reason' => $product['loss_reason'] ?? null,
						'comment' => $product['comment'] ?? null,
						'ut' => now(),
						'admin_id' => $admin_id,
					]);
				
				$qty = DB::table('stock_check_product')->where('sc_prd_cd', $product['sc_prd_cd'])->value('qty');

				$original_wqty = DB::table('product_stock_store')->where('store_cd', $store_cd)->where('prd_cd', $product['prd_cd'])->value('wqty');
				$minus_qty = ($original_wqty ?? 0) - ($qty ?? 0);

				DB::table('product_stock_store')
					->where('store_cd', $store_cd)
					->where('prd_cd', $product['prd_cd'])
					->update([
						'qty' => $qty,
						'wqty' => $qty,
						'ut' => now(),
					]);

				DB::table('product_stock')
					->where('prd_cd', $product['prd_cd'])
					->update([
						'qty_wonga'	=> DB::raw('qty_wonga - (' . $minus_qty . ' * wonga)'),
						'out_qty' => DB::raw('out_qty + ' . $minus_qty),
						'qty' => DB::raw('qty - ' . $minus_qty),
						'ut' => now(),
					]);
				
				$wonga = DB::table('product_stock')->where('prd_cd', $product['prd_cd'])->value('wonga');

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $product['goods_no'],
						'prd_cd' => $product['prd_cd'],
						'goods_opt' => $product['goods_opt'],
						'location_cd' => $store_cd,
						'location_type' => 'STORE',
						'type' => PRODUCT_STOCK_TYPE_LOSS, // 재고분류 : LOSS
						'price' => $product['price'],
						'wonga' => $wonga ?? 0,
						'qty' => $product['loss_rec_qty'] * -1,
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'comment' => 'LOSS등록',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);
			}

			DB::commit();
			$msg = "LOSS등록이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([ "code" => $code, "msg" => $msg ], $code);
	}

    /** 실사일괄등록 팝업오픈 */
    public function show_batch()
    {
        return view(Config::get('shop.store.view') . '/stock/stk26_batch');
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
	
				$save_path = "data/store/stk26/";
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
        $sc_date = $request->input('sc_date', '');
        $store_cd = $request->input('store_cd', '');
        $md_id = $request->input('md_id', '');
        $comment = $request->input('comment', '');
        
        $data = $request->input('data', []);
        $result = [];
        
        $store = DB::table('store')->where('store_cd', $store_cd)->select('store_cd', 'store_nm')->first();
        $md = DB::table('mgr_user')->where('id', $md_id)->select('id', 'name')->first();
        if ($store == null || $md == null || $sc_date == null) {
            return response()->json(['code' => 404, 'msg' => '실사 기본정보가 올바르지 않습니다. 실사일자/매장코드/담당자아이디 항목을 확인해주세요.']);
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
                    , ifnull(pss.wqty, 0) as store_wqty
                    , '$qty' as qty
                    , (ifnull(pss.wqty, 0) - ifnull('$qty', 0)) as loss_qty
                    , (ifnull(pss.wqty, 0) - ifnull('$qty', 0)) * g.price as loss_price
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

        $new_sc_cd = 1;
        $sql = "
            select sc_cd
            from stock_check
            order by sc_cd desc
            limit 1
        ";
        $row = DB::selectOne($sql);
        if($row != null) $new_sc_cd = $row->sc_cd + 1;

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
                "new_sc_cd" => $new_sc_cd,
                "sc_date" => $sc_date,
                "store" => $store,
                "md" => $md,
                "comment" => $comment,
            ],
            "body" => $result
        ]);
    }

      /**
       * 
       * 매장 실사 바코드 등록 부분
       * 
       */

      public function barcode_batch()
      {
          return view(Config::get('shop.store.view') . '/stock/stk26_barcode_batch');
      }

      /** 바코드 등록 시 Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환 */
	public function import_excel2(Request $request) {
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();
	
				$save_path = "data/store/stk26/";
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

    /** 바코드 등록 상품 개별 조회 */
    public function get_goods2(Request $request) {
        $sc_date = $request->input('sc_date', '');
        $store_cd = $request->input('store_cd', '');
        $md_id = $request->input('md_id', '');
        $comment = $request->input('comment', '');
        
        $data = $request->input('data', []);
        $result = [];
        
        $store = DB::table('store')->where('store_cd', $store_cd)->select('store_cd', 'store_nm')->first();
        $md = DB::table('mgr_user')->where('id', $md_id)->select('id', 'name')->first();
        if ($store == null || $md == null || $sc_date == null) {
            return response()->json(['code' => 404, 'msg' => '실사 기본정보가 올바르지 않습니다. 실사일자/매장코드/담당자아이디 항목을 확인해주세요.']);
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
                    , ifnull(pss.wqty, 0) as store_wqty
                    , $qty as qty
                    , (ifnull(pss.wqty, 0) - ifnull($qty, 0)) as loss_qty
                    , (ifnull(pss.wqty, 0) - ifnull($qty, 0)) * g.price as loss_price
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

        

        $new_sc_cd = 1;
        $sql = "
            select sc_cd
            from stock_check
            order by sc_cd desc
            limit 1
        ";
        $row = DB::selectOne($sql);
        if($row != null) $new_sc_cd = $row->sc_cd + 1;

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
                "new_sc_cd" => $new_sc_cd,
                "sc_date" => $sc_date,
                "store" => $store,
                "md" => $md,
                "comment" => $comment,
            ],
            "body" => $result
        ]);
    }



}
