<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

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
        return view(Config::get('shop.shop.view') . '/stock/stk26', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->sub(1, 'week')->format('Y-m-d'));
		$edate = $request->input('edate', date('Y-m-d'));
		$sc_cd = $request->input('sc_cd', '');
		$store_cd = Auth('head')->user()->store_cd;
		$sc_state = $request->input('sc_state', '');
		$loss_reason = $request->input('loss_reason', '');

		// where
		$where = "";
		$where .= " and s.sc_date >= '$sdate' ";
		$where .= " and s.sc_date <= '$edate' ";
		if($sc_cd != '') $where .= " and s.sc_cd = '$sc_cd' ";
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
            where s.store_cd = :store_cd $where
            group by s.sc_cd
            order by s.sc_cd desc
        ";

		$result = DB::select($sql, [ 'store_cd' => $store_cd ]);

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
		$store_cd = Auth('head')->user()->store_cd;

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
                where s.sc_cd = :sc_cd and s.store_cd = :store_cd
            ";
			$sc = DB::selectOne($sql, [ 'sc_cd' => $sc_cd, 'store_cd' => $store_cd ]);
		}

		$values = [
			'sc'            => $sc,
			'loss_reasons'	=> SLib::getCodes('LOSS_REASON'),
		];
		return view(Config::get('shop.shop.view') . '/stock/stk26_show', $values);
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
                (
                    select s.size_cd from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size,
                if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh,
                s.price,
                s.qty,
                s.store_qty as store_wqty, 
                s.loss_qty,
                s.loss_rec_qty,
                s.loss_price,
                s.loss_price2,
                s.loss_tag_price,
                if(r.code_val is null, '', s.loss_reason) as loss_reason,
                ifnull(r.code_val, if(sc.sc_state = 'Y', s.loss_reason, null)) as loss_reason_val,
                s.comment
            from stock_check_product s
                inner join stock_check sc on sc.sc_cd = s.sc_cd
                inner join product_code pc on pc.prd_cd = s.prd_cd
                inner join product p on p.prd_cd = s.prd_cd
                left outer join goods g on g.goods_no = pc.goods_no
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
               	left outer join code r on r.code_kind_cd = 'LOSS_REASON' and r.code_id = s.loss_reason
                , (select @rownum :=0) as r
            where s.sc_cd = :sc_cd
        ";
		$products = DB::select($sql, [ 'sc_cd' => $sc_cd ]);

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

	// 실사정보 수정
	public function update(Request $request)
	{
		$products = $request->input("products", []);
		$admin_id = Auth('head')->user()->id;

		try {
			DB::beginTransaction();

			foreach($products as $product) {
				DB::table('stock_check_product')
					->where('sc_prd_cd', '=', $product['sc_prd_cd'])
					->update([
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
}
