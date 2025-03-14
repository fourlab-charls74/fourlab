<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // 출고

class stk18Controller extends Controller
{
    public function index()
	{
        $stores = DB::table('store')->where('use_yn', '=', 'Y')->select('store_cd', 'store_nm')->get();//전체매장
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderBy('default_yn')->get();

		$values = [
            'today' => date("Y-m-d"),
            'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
            'types' => SLib::getCodes("PRD_MATERIAL_TYPE"), // 원부자재 구분
            'opts' => SLib::getCodes("PRD_MATERIAL_OPT"),
            'rel_orders' => SLib::getCodes("REL_ORDER"), // 출고차수
            'stores' => $stores, // 전체 매장리스트
            'storages' => $storages, // 창고리스트
		];

        return view(Config::get('shop.shop.view') . '/stock/stk18', $values);
	}

    public function search(Request $request)
    {
        $req = $request->all();

	    $code = 200;
		$where = "";
        $orderby = "";

        // where
        if ($req['prd_cd_sub'] != null) {
            $prd_cd = explode(',', $req['prd_cd_sub']);
            $where .= " and (1!=1";
            foreach ($prd_cd as $cd) {
                $where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
            }
            $where .= ")";
        }

        if ($req['type'] != "") $where .= " and pc.brand = '" . Lib::quote($req['type']) . "'";
        if ($req['opt'] != "") $where .= " and pc.opt = '" . Lib::quote($req['opt']) . "'";
		if ($req['prd_nm'] != "") $where .= " and p.prd_nm like '%" . Lib::quote($req['prd_nm']) . "%' ";

        // having
        $having = "";
        if (($req['ext_storage_qty'] ?? 'false') == 'true') $having .= " and sum(pss.wqty) > '0'";

        // orderby
        $ord = $req['ord'] ?? 'desc';
        $ord_field = $req['ord_field'] ?? "p.prd_cd";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $req['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $req['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        // search
		$sql = "
            select
                c.code_val as type_nm,
                c2.code_val as opt,
                i.img_url as img,
                p.prd_cd as prd_cd,
                p.prd_nm as prd_nm,
                c3.code_val as color,
                c4.code_val as size,
                c5.code_val as unit,
                ifnull(p.price, 0) as price,
                ifnull(p.wonga, 0) as wonga,
                ifnull(pss.qty, 0) as storage_qty,
                ifnull(pss.wqty, 0) as storage_wqty,
                '0' as rel_qty,
                '0' as amount
            from product p
                inner join product_stock_storage pss on pss.prd_cd = p.prd_cd
                inner join product_code pc on p.prd_cd = pc.prd_cd and pc.type <> 'N'
                left outer join product_image i on p.prd_cd = i.prd_cd
                inner join company cp on p.com_id = cp.com_id
                left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
                left outer join `code` c4 on c4.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c4.code_id = pc.size
                left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
            where 1=1 $where
            group by p.prd_cd
            having 1=1 $having
            $orderby
            $limit
		";

		$result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if ($page == 1) {
            $sql = "
                select count(c.prd_cd) as total
                from (
                    select pss.prd_cd, count(pss.prd_cd)
                    from product p
                        inner join product_stock_storage pss on p.prd_cd = pss.prd_cd
                        inner join product_code pc on p.prd_cd = pc.prd_cd and pc.type <> 'N'
                        left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                        left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                    where 1=1 $where
                    group by p.prd_cd
                    having 1=1 $having
                ) as c
            ";
            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }
        
        foreach ($result as $item) {
            $prd_cd = $item->prd_cd;
            $sql = "
                select s.storage_cd, p.prd_cd, p.wqty
                from storage s
                    left outer join product_stock_storage p on p.storage_cd = s.storage_cd and p.prd_cd = '$prd_cd'
                where s.use_yn = 'Y' and p.use_yn = 'Y'
            ";
            $row = DB::select($sql);
            $item->storage_qty = $row;
        }

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			],
			"body" => $result
		]);
    }

    // 일반출고 요청 (요청과 동시에 접수완료 처리됩니다.)
    public function request_release(Request $request) {

        $release_type = 'G';
        $state = 20;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        $storage_cd = $request->input("storage_cd", '');
        $store_cd = $request->input("store_cd", '');
        $exp_dlv_day = $request->input("exp_dlv_day", '');
        $rel_order = $request->input("rel_order", '');

        $data = $request->input("products", []);

        try {
            
            DB::beginTransaction();

			foreach($data as $row) {

                DB::table('sproduct_stock_release')
                    ->insert([
                        'type' => $release_type,
                        'prd_cd' => $row['prd_cd'],
                        'price' => $row['price'],
                        'wonga' => $row['wonga'],
                        'qty' => $row['rel_qty'] ?? 0,
                        'store_cd' => $store_cd,
                        'storage_cd' => $storage_cd,
                        'state' => $state,
                        'exp_dlv_day' => str_replace("-", "", $exp_dlv_day),
                        'rel_order' => $rel_order,
                        'req_id' => $admin_id,
                        'req_rt' => now(),
                        'rec_id' => $admin_id,
                        'rec_rt' => now(),
                        'rt' => now(),
                    ]);
    
                // product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', '=', $row['prd_cd'])
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($row['rel_qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $row['prd_cd'])
                    ->where('storage_cd', '=', $storage_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($row['rel_qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // // 재고이력 등록
                // DB::table('product_stock_hst')
                //     ->insert([
                //         'prd_cd' => $row['prd_cd'],
                //         'location_cd' => $storage_cd,
                //         'location_type' => 'STORAGE',
                //         'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
                //         'price' => $row['price'],
                //         'wonga' => $row['wonga'],
                //         'qty' => ($row['rel_qty'] ?? 0) * -1,
                //         'stock_state_date' => date('Ymd'),
                //         'ord_opt_no' => '',
                //         'comment' => '창고출고',
                //         'rt' => now(),
                //         'admin_id' => $admin_id,
                //         'admin_nm' => $admin_nm,
                //     ]);

                // product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
                $store_stock_cnt = 
                    DB::table('product_stock_store')
                        ->where('store_cd', '=', $store_cd)
                        ->where('prd_cd', '=', $row['prd_cd'])
                        ->count();
                if ($store_stock_cnt < 1) {
                    // 해당 매장에 상품 기존재고가 없을 경우
                    DB::table('product_stock_store')
                        ->insert([
                            'prd_cd' => $row['prd_cd'],
                            'store_cd' => $store_cd,
                            'qty' => 0,
                            'wqty' => $row['rel_qty'] ?? 0,
                            'use_yn' => 'Y',
                            'rt' => now(),
                        ]);
                } else {
                    // 해당 매장에 상품 기존재고가 이미 존재할 경우
                    DB::table('product_stock_store')
                        ->where('prd_cd', '=', $row['prd_cd'])
                        ->where('store_cd', '=', $store_cd) 
                        ->update([
                            'wqty' => DB::raw('wqty + ' . ($row['rel_qty'] ?? 0)),
                            'ut' => now(),
                        ]);
                }

                // // 재고이력 등록
				// DB::table('product_stock_hst')
                //     ->insert([
                //         'prd_cd' => $row['prd_cd'],
                //         'location_cd' => $store_cd,
                //         'location_type' => 'STORE',
                //         'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
                //         'price' => $row['price'],
                //         'wonga' => $row['wonga'],
                //         'qty' => $d['rel_qty'] ?? 0,
                //         'stock_state_date' => date('Ymd'),
                //         'ord_opt_no' => '',
                //         'comment' => '매장입고',
                //         'rt' => now(),
                //         'admin_id' => $admin_id,
                //         'admin_nm' => $admin_nm,
                //     ]);

            }
            $code = 200;
			DB::commit();
		} catch (Exception $e) {
            // $msg = $e->getMessage();
            $code = 500;
			DB::rollback();
		}
        return response()->json(["code" => $code]);
    }

    function change_store_type(Request $request) {
        $store_type = $request->input('store_type');
        try {
            DB::beginTransaction();

            if ($store_type == null) {
                $sql = "
                    select 
                        store_cd, store_nm
                    from store
                    where use_yn = 'Y'
                ";
            } else {
                $sql = "
                    select 
                        store_cd, store_nm
                    from store
                    where use_yn = 'Y' and store_type = '$store_type'
                ";
            }
           
            $result = DB::select($sql);


            $code = 200;
			DB::commit();
		} catch (Exception $e) {
            // $msg = $e->getMessage();
            $code = 500;
			DB::rollback();
		}
        return response()->json(["code" => $code , "result" => $result]);
    
    }

}
