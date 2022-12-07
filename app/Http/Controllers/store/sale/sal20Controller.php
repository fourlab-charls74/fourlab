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
                sum(sp.store_qty - sp.qty) as loss_qty,
                sum(sp.price * (sp.store_qty - sp.qty)) as loss_price,
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
        $data = $request->input('data', []);
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        try {
            DB::beginTransaction();

            foreach($data as $d)
            {
                DB::table('stock_check')
                    ->where('sc_cd', '=', $d['sc_cd'])
                    ->update([
                        'sc_state' => 'Y',
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);
                
                $sql = "
                    select 
                        s.prd_cd,
                        s.qty,
                        sum(s.store_qty - s.qty) as loss_qty,
                        pc.goods_no,
                        pc.goods_opt,
                        g.price,
                        g.wonga
                    from stock_check_product s
                        inner join product_code pc on pc.prd_cd = s.prd_cd
                        inner join goods g on g.goods_no = pc.goods_no
                    where sc_cd = :sc_cd
                    group by sc_prd_cd
                ";
                $products = DB::select($sql, ['sc_cd' => $d['sc_cd']]);
                foreach($products as $prd)
                {
                    $original_wqty = DB::table('product_stock_store')->where('store_cd', '=', $d['store_cd'])->where('prd_cd', '=', $prd->prd_cd)->first()->wqty;
                    $minus_qty = ($original_wqty ?? 0) - ($prd->qty ?? 0);

                    DB::table('product_stock_store')
                        ->where('store_cd', '=', $d['store_cd'])
                        ->where('prd_cd', '=', $prd->prd_cd)
                        ->update([
                            'qty' => $prd->qty,
                            'wqty' => $prd->qty,
                            'ut' => now(),
                        ]);

                    DB::table('product_stock')
                        ->where('prd_cd', '=', $prd->prd_cd)
                        ->update([
                            'qty_wonga'	=> DB::raw('qty_wonga - ' . ($minus_qty * ($prd->wonga))),
							'out_qty' => DB::raw('out_qty + ' . $minus_qty),
                            'qty' => DB::raw('qty - ' . $minus_qty),
                            'ut' => now(),
                        ]);

                    // 재고이력 등록
                    DB::table('product_stock_hst')
                        ->insert([
                            'goods_no' => $prd->goods_no,
                            'prd_cd' => $prd->prd_cd,
                            'goods_opt' => $prd->goods_opt,
                            'location_cd' => $d['store_cd'],
                            'location_type' => 'STORE',
                            'type' => PRODUCT_STOCK_TYPE_LOSS, // 재고분류 : LOSS
                            'price' => $prd->price,
                            'wonga' => $prd->wonga,
                            'qty' => ($prd->loss_qty) * -1,
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
