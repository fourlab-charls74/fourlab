<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

const PRODUCT_STOCK_TYPE_LOSS = 14; // 재고분류 : LOSS

class stk27Controller extends Controller
{
	public function index()
	{
        $sdate = now()->sub(1, 'week')->format('Y-m-d');
        $edate = date('Y-m-d');

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
			'loss_reasons'	=> SLib::getCodes('STORAGE_LOSS_REASON'),
		];
        return view(Config::get('shop.store.view') . '/stock/stk27', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'week')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $storage_cd = $request->input('storage_no', '');
		$loss_reason = $request->input('loss_reason', '');

        // where
        $where = "";
        $where .= " and s.ssc_date >= '$sdate' ";
        $where .= " and s.ssc_date <= '$edate' ";
        if($storage_cd != '') $where .= " and s.storage_cd = '$storage_cd' ";
        if($loss_reason != '') $where .= " and sp.loss_reason = '$loss_reason' ";

        $sql = "
            select
                s.ssc_date,
                s.ssc_type,
                s.ssc_cd,
                s.storage_cd,
                storage.storage_nm,
                sum(sp.storage_qty) as storage_qty,
                sum(sp.qty) as qty,
                sum(sp.loss_qty) as loss_qty,
                sum(sp.loss_price) as loss_price,
                s.md_id,
                m.name as md_nm,
                s.comment
            from storage_stock_check s
                inner join storage on storage.storage_cd = s.storage_cd
                inner join mgr_user m on m.id = s.md_id
                inner join storage_stock_check_product sp on sp.ssc_cd = s.ssc_cd
            where 1=1 $where
            group by s.ssc_cd
            order by s.ssc_cd desc
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

    public function show($ssc_cd = '', Request $request)
    {
        $ssc = '';

        if($ssc_cd != '') {
            $sql = "
                select
                    s.ssc_date,
                    concat(s.storage_cd, '_', REPLACE(s.ssc_date, '-', '') , '_' , LPAD(s.ssc_cd, 3, '0')) as ssc_code,
                    s.ssc_type,
                    if(s.ssc_type = 'G', '일반등록', if(s.ssc_type = 'B', '일괄등록', if(s.ssc_type = 'C', '바코드등록', '-'))) as ssc_type_nm,
                    s.ssc_cd,
                    s.storage_cd,
                    storage.storage_nm,
                    s.md_id,
                    m.name as md_nm,
                    s.comment
                from storage_stock_check s
                    inner join storage on storage.storage_cd = s.storage_cd
                    inner join mgr_user m on m.id = s.md_id
                where s.ssc_cd = :ssc_cd
            ";
            $ssc = DB::selectOne($sql, [ 'ssc_cd' => $ssc_cd ]);
        }

        $values = [
            "cmd"           => $ssc == '' ? "add" : "get",
            'sdate'         => $ssc == '' ? date("Y-m-d") : $ssc->ssc_date,
            'ssc'           => $ssc,
			'loss_reasons'	=> SLib::getCodes('STORAGE_LOSS_REASON'),
		];
        return view(Config::get('shop.store.view') . '/stock/stk27_show', $values);
    }

    // 기존 재고조정등록상품정보 불러오기
    public function search_check_products(Request $request)
    {
        $ssc_cd = $request->input('ssc_cd', '');
        $sql = "
            select 
                @rownum := @rownum + 1 as count,
                s.ssc_prd_cd, 
                s.ssc_cd, 
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
                (
                    select s.size_cd from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size,
                s.tag_price as goods_sh,
                s.price,
                s.qty,
                s.storage_qty as storage_wqty, 
                s.loss_qty,
                s.loss_price,
                (s.storage_qty * s.price) as loss_price2,
                (s.storage_qty * s.tag_price) as loss_tag_price,
                if(r.code_val is null, '', s.loss_reason) as loss_reason,
                ifnull(r.code_val, s.loss_reason) as loss_reason_val,
                s.comment
            from storage_stock_check_product s
                inner join product_code pc on pc.prd_cd = s.prd_cd
                inner join product p on p.prd_cd = s.prd_cd
                left outer join goods g on g.goods_no = pc.goods_no
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
               	left outer join code r on r.code_kind_cd = 'STORAGE_LOSS_REASON' and r.code_id = s.loss_reason
                , (select @rownum :=0) as r
            where s.ssc_cd = :ssc_cd
        ";
        $products = DB::select($sql, ['ssc_cd' => $ssc_cd]);

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

    /** 재고조정 등록 */
    public function save(Request $request)
    {
		$code = '200';
		$msg = '';
        $ssc_type = $request->input("ssc_type", "G"); // 일반(G)/일괄(B)/바코드(C)
        $ssc_date = $request->input("ssc_date", date("Y-m-d"));
        $storage_cd = $request->input("storage_cd", "");
        $md_id = $request->input("md_id", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);
        $admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

        try {
            DB::beginTransaction();

			// 재고조정 마스터 등록
            $ssc_cd = DB::table('storage_stock_check')
                ->insertGetId([
                    'storage_cd' => $storage_cd,
                    'md_id' => $md_id,
                    'ssc_date' => $ssc_date,
                    'ssc_type' => $ssc_type,
                    'comment' => $comment,
                    'rt' => now(),
                    'admin_id' => $admin_id,
                ]);

            foreach($products as $product) {
				$loss_qty = $product['storage_qty'] - $product['qty'];
				
				// 재고조정 상품 상세내역 등록
                DB::table('storage_stock_check_product')
                    ->insert([
                        'ssc_cd' => $ssc_cd,
                        'prd_cd' => $product['prd_cd'],
                        'price' => $product['price'],
                        'tag_price' => $product['goods_sh'],
                        'qty' => $product['qty'],
                        'storage_qty' => $product['storage_qty'],
                        'loss_qty' => $loss_qty,
                        'loss_price' => $product['price'] * $loss_qty,
						'loss_reason' => $product['loss_reason'] ?? null,
						'comment' => $product['comment'] ?? null,
                        'rt' => now(),
                        'admin_id' => $admin_id,
                    ]);

				// 창고재고처리
				DB::table('product_stock_storage')
					->where('storage_cd', $storage_cd)
					->where('prd_cd', $product['prd_cd'])
					->update([
						'qty' => DB::raw("qty - " . $loss_qty),
						'wqty' => $product['qty'],
						'ut' => now(),
					]);

				// 전체재고처리
				DB::table('product_stock')
					->where('prd_cd', $product['prd_cd'])
					->update([
						'out_qty' => DB::raw('out_qty + ' . $loss_qty),
						'qty' => DB::raw('qty - ' . $loss_qty),
						'wqty' => DB::raw('wqty - ' . $loss_qty),
						'qty_wonga'	=> DB::raw('qty * wonga'),
						'ut' => now(),
					]);

				$wonga = DB::table('product_stock')->where('prd_cd', $product['prd_cd'])->value('wonga');

				if ($loss_qty > 0 || $loss_qty < 0) {
					// 재고이력 등록
					DB::table('product_stock_hst')
						->insert([
							'goods_no' => $product['goods_no'],
							'prd_cd' => $product['prd_cd'],
							'goods_opt' => $product['goods_opt'],
							'location_cd' => $storage_cd,
							'location_type' => 'STORAGE',
							'type' => PRODUCT_STOCK_TYPE_LOSS, // 재고분류 : LOSS
							'price' => $product['price'],
							'wonga' => $wonga ?? 0,
							'qty' => $loss_qty * -1,
							'stock_state_date' => date('Ymd'),
							'ord_opt_no' => '',
							'comment' => 'LOSS등록',
							'rt' => now(),
							'admin_id' => $admin_id,
							'admin_nm' => $admin_nm,
						]);
				}
            }

			DB::commit();
            $msg = '창고재고조정이 정상적으로 완료되었습니다.';
		} catch (Exception $e) {
			DB::rollback();
			$code = '500';
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    /** 재고조정일괄등록 팝업오픈 */
    public function show_batch()
    {
		$values = [ 
			'sdate' => date("Y-m-d"),
			'loss_reasons'	=> SLib::getCodes('STORAGE_LOSS_REASON'),
		];
        return view(Config::get('shop.store.view') . '/stock/stk27_batch', $values);
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
	
				$save_path = "data/store/stk27/";
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
		$ssc_type = $request->input('ssc_type', 'B');
        $result = [];

        foreach ($data as $key => $d) {
            $prd_cd = $d['prd_cd'];
            $qty = $d['qty'] ?? 0;
            $count = $d['count'] ?? '';
			$loss_reason_val = $d['loss_reason_val'] ?? '';
			$comment = $d['comment'] ?? '';
			
			$batch_sql = "";
			if ($ssc_type !== 'C') {
				$batch_sql = "
					, ifnull((select code_val from code where code_kind_cd = 'STORAGE_LOSS_REASON' and code_val = '$loss_reason_val'), '') as loss_reason_val
                	, ifnull((select code_id from code where code_kind_cd = 'STORAGE_LOSS_REASON' and code_val = '$loss_reason_val'), '') as loss_reason
                	, '$comment' as comment
				";
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
                    , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                    , pc.color
                    , (
                        select s.size_cd from size s
                        where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                            and s.size_cd = pc.size
                            and use_yn = 'Y'
                    ) as size
                    , pc.goods_opt
                    , if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh
                    , if(g.goods_no <> '0', g.price, p.price) as price
                    , ifnull(pss.wqty, 0) as storage_wqty
                    , '$qty' as qty
                    , (ifnull(pss.wqty, 0) - ifnull('$qty', 0)) as loss_qty
                    , (ifnull(pss.wqty, 0) - ifnull('$qty', 0)) * g.price as loss_price
                    , '$count' as count
                	$batch_sql
                from product_code pc
                    inner join product p on p.prd_cd = pc.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join product_stock_storage pss on pss.prd_cd = pc.prd_cd and pss.storage_cd = '$storage_cd'
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

	/** 창고재고조정 바코드등록 팝업오픈 */
	public function barcode_batch()
	{
		$values = [
			'sdate' => date("Y-m-d"),
			'loss_reasons'	=> SLib::getCodes('STORAGE_LOSS_REASON'),
		];
		return view(Config::get('shop.store.view') . '/stock/stk27_barcode_batch', $values);
	}
}
