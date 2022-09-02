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
                
                $products = DB::table('stock_check_product')->select('prd_cd', 'qty')->where('sc_cd', '=', $d['sc_cd'])->get();

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
                            'qty' => DB::raw('qty - ' . $minus_qty),
                            'ut' => now(),
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
