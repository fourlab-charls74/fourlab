<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

const PRODUCT_STOCK_TYPE_LOSS = 14;		// 재고분류 : LOSS

class sal20Controller extends Controller
{

	public function index(Request $request)
	{
        $store_cd = $request->input('store_cd', '');
        $values = [
            'sdate' => $request->input('sdate', now()->sub(1, 'week')->format('Y-m-d')),
            'edate' => $request->input('edate', date('Y-m-d')),
            'sc_cd' => $request->input('sc_cd', ''),
            'store' => DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', '=', $store_cd)->first(),
            'sc_state' => $request->input('sc_state', ''),
		];
        return view(Config::get('shop.store.view') . '/sale/sal20', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'week')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $sc_cd = $request->input('sc_cd', '');
        $store_cd = $request->input('store_no', '');
        $sc_state = $request->input('sc_state', '');

        // where
        $where = "";
        $where .= " and s.sc_date >= '$sdate 00:00:00' ";
        $where .= " and s.sc_date <= '$edate 23:59:59' ";
        if($sc_cd != '') $where .= " and s.sc_cd = '$sc_cd' ";
        if($store_cd != '') $where .= " and s.store_cd = '$store_cd' ";
        if($sc_state != '') $where .= " and s.sc_state = '$sc_state' ";

        $sql = "
            select
                s.sc_date,
                s.sc_cd,
                s.store_cd,
                store.store_nm,
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

    public function save_loss(Request $request)
    {
        $data       = $request->input('data', []);
        $store_cd   = $request->input('store_cd');
        $sc_cd      = $request->input('sc_cd');
        $comment    = $request->input('comment');
        $admin_id   = Auth('head')->user()->id;
        $admin_nm   = Auth('head')->user()->name;

        try {
            DB::beginTransaction();

            foreach($data as $d)
            {
                DB::table('stock_check')
                    ->where('sc_cd', '=', $sc_cd)
                    ->update([
                        'sc_state' => 'Y',
                        'comment' => $comment,
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);

                // 실사재고, LOSS수량, LOSS인정수량, LOSS금액, 현재가 금액, TAG가 금액 업데이트
                DB::table('stock_check_product')
                    ->where('sc_prd_cd', '=', $d['sc_prd_cd'])
                    ->update([
                        'qty' => $d['qty'], //실사재고
                        'loss_qty' => $d['loss_qty'], //LOSS수량
                        'loss_rec_qty' => $d['loss_rec_qty'], //LOSS인정수량
                        'loss_price' => $d['loss_price'], // LOSS금액
                        'loss_price2' => $d['loss_price2'], //현재가 금액
                        'loss_tag_price' => $d['loss_tag_price'], // TAG가 금액
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);

                    $original_wqty = DB::table('product_stock_store')->where('store_cd', '=', $store_cd)->where('prd_cd', '=', $d['prd_cd'])->first()->wqty;

                    $minus_qty = ($original_wqty ?? 0) - ($d['qty'] ?? 0);
                    
                    DB::table('product_stock_store')
                        ->where('store_cd', '=', $store_cd)
                        ->where('prd_cd', '=', $d['prd_cd'])
                        ->update([
                            'qty' => $d['qty'],
                            'wqty' => $d['qty'],
                            'ut' => now(),
                        ]);

                    DB::table('product_stock')
                        ->where('prd_cd', '=', $d['prd_cd'])
                        ->update([
                            'qty_wonga'	=> DB::raw('qty_wonga - ' . ($minus_qty * ($d['wonga']))),
							'out_qty' => DB::raw('out_qty + ' . $minus_qty),
                            'qty' => DB::raw('qty - ' . $minus_qty),
                            'ut' => now(),
                        ]);

                    // 재고이력 등록
                    DB::table('product_stock_hst')
                        ->insert([
                            'goods_no' => $d['goods_no'],
                            'prd_cd' => $d['prd_cd'],
                            'goods_opt' => $d['goods_opt'],
                            'location_cd' => $store_cd,
                            'location_type' => 'STORE',
                            'type' => PRODUCT_STOCK_TYPE_LOSS, // 재고분류 : LOSS
                            'price' => $d['price'],
                            'wonga' => $d['wonga'],
                            'qty' => ($d['loss_qty']) * -1,
                            'stock_state_date' => date('Ymd'),
                            'ord_opt_no' => '',
                            'comment' => 'LOSS등록',
                            'rt' => now(),
                            'admin_id' => $admin_id,
                            'admin_nm' => $admin_nm,
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

    public function show($sc_cd = '', Request $request)
    {
        // $editable = $request->input("editable", 'Y'); // 매장LOSS등록에서 실사상세팝업에 접근할 경우, 정보를 수정할 수 없습니다.
        $sc = '';
        $new_sc_cd = '';

        if($sc_cd != '') {
            $sql = "
                select
                    s.sc_date,
                    s.sc_type,
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
        } else {
            $sql = "
                select 
                    sc_cd
                    , store_cd 
                from stock_check
                order by sc_cd desc
                limit 1
            ";
            $row = DB::selectOne($sql);
            if($row == null) $new_sc_cd = 1;
            else $new_sc_cd = $row->sc_cd + 1;
        }
        // if($editable == 'N') $sc->sc_state = 'Y';

        $values = [
            "cmd"           => $sc == '' ? "add" : "update",
            'sdate'         => $sc == '' ? date("Y-m-d") : $sc->sc_date,
            'sc'            => $sc,
            'new_sc_cd'     => $new_sc_cd,
            'store_cd'      => $sc->store_cd
		];
        return view(Config::get('shop.store.view') . '/sale/sal20_show', $values);
    }

    // 기존 실사 정보 가져오기
    public function search_check_products(Request $request)
    {
        $sc_cd = $request->input('sc_cd', '');
        $cmd = $request->input('cmd', '');
        $sc_state = $request->input('sc_state', '');

        if($sc_state== 'Y') {
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
                    concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                    pc.color,
                    pc.size,
                    if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh,
                    s.price,
                    g.wonga,
                    s.qty,
                    s.store_qty as store_wqty, 
                    (s.store_qty - s.qty) as loss_qty,
                    s.loss_rec_qty,
                    s.loss_price,
                    s.loss_tag_price,
                    s.loss_price2,
                    true as isEditable
                from stock_check_product s
                    inner join product_code pc on pc.prd_cd = s.prd_cd
                    inner join product p on p.prd_cd = s.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                    , (select @rownum :=0) as r
                where s.sc_cd = :sc_cd
            ";
        } else {
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
                    concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                    pc.color,
                    pc.size,
                    if(g.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh,
                    s.price,
                    g.wonga,
                    s.qty,
                    s.store_qty as store_wqty, 
                    (s.store_qty - s.qty) as loss_qty,
                    (s.store_qty - s.qty) as loss_rec_qty,
                    (s.price * (s.store_qty - s.qty)) as loss_price,
                    if(g.goods_no <> '0', g.goods_sh * (s.store_qty - s.qty), p.tag_price * (s.store_qty - s.qty)) as loss_tag_price,
                    (s.price * (s.store_qty - s.qty)) as loss_price2,
                    true as isEditable
                from stock_check_product s
                    inner join product_code pc on pc.prd_cd = s.prd_cd
                    inner join product p on p.prd_cd = s.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                    , (select @rownum :=0) as r
                where s.sc_cd = :sc_cd
            ";
        }

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

}
