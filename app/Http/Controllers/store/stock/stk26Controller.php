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

class stk26Controller extends Controller
{
	public function index()
	{
        $sdate = now()->sub(1, 'week')->format('Y-m-d');
        $edate = date('Y-m-d');

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
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
            order by s.sc_date desc
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
        $editable = $request->input("editable", 'Y'); // 매장LOSS등록에서 실사상세팝업에 접근할 경우, 정보를 수정할 수 없습니다.
        $sc = '';
        $new_sc_cd = '';

        if($sc_cd != '') {
            $sql = "
                select
                    s.sc_date,
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
                select sc_cd
                from stock_check
                order by sc_cd desc
                limit 1
            ";
            $row = DB::selectOne($sql);
            if($row == null) $new_sc_cd = 1;
            else $new_sc_cd = $row->sc_cd + 1;
        }
        if($editable == 'N') $sc->sc_state = 'Y';

        $values = [
            "cmd"           => $sc == '' ? "add" : "update",
            'sdate'         => $sc == '' ? date("Y-m-d") : $sc->sc_date,
            'sc'            => $sc,
            'new_sc_cd'     => $new_sc_cd,
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
                ifnull(type.code_val, 'N/A') as goods_type_nm,
                op.opt_kind_nm,
                b.brand_nm as brand, 
                g.style_no, 
                stat.code_val as sale_stat_cl, 
                g.goods_nm,
                pc.goods_opt,
                g.goods_sh,
                s.price,
                s.qty,
                s.store_qty as store_wqty, 
                (s.store_qty - s.qty) as loss_qty,
                (s.price * (s.store_qty - s.qty)) as loss_price,
                true as isEditable
            from stock_check_product s
                left outer join product_code pc on pc.prd_cd = s.prd_cd
                left outer join goods g on g.goods_no = pc.goods_no
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id,
                (select @rownum :=0) as r
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
                    'comment' => $comment,
                    'rt' => now(),
                    'admin_id' => $admin_id,
                ]);

            foreach($products as $product) {
                DB::table('stock_check_product')
                    ->insert([
                        'sc_cd' => $sc_cd,
                        'prd_cd' => $product['prd_cd'],
                        'price' => $product['price'], // 판매가
                        'qty' => $product['qty'], // 실사수량
                        'store_qty' => $product['store_qty'], // 매장수량
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
                        'qty' => $product['qty'], // 실사수량
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
