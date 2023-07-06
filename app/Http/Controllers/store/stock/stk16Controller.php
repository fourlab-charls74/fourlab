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

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // 출고
const PRODUCT_STOCK_TYPE_STORE_OUT = 11;
const PRODUCT_STOCK_TYPE_STORAGE_IN = 11;

class stk16Controller extends Controller
{
    private $rel_states = [
        '10' => '요청',
        '20' => '접수',
        '30' => '출고',
        '40' => '매장입고',
        '-10' => '거부',
    ];

    public function index()
    {
        $values = [
            'sdate' => now()->sub(1, 'week')->format('Y-m-d'),
            'edate' => date("Y-m-d"),
            'rel_orders' => SLib::getCodes("REL_ORDER"), // 출고차수
            'rel_types' => SLib::getCodes("REL_TYPE"), // 출고구분
            'rel_states' => $this->rel_states, // 출고상태
            'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
            'types' => SLib::getCodes("PRD_MATERIAL_TYPE"), // 원부자재 구분
            'opts' => SLib::getCodes("PRD_MATERIAL_OPT"), // 원부자재 품목
            'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
        ];

        return view(Config::get('shop.store.view') . '/stock/stk16', $values);
    }

    public function search(Request $request)
    {
        $req = $request->all();

        $code = 200;
        $where = "";
        $orderby = "";

        // where
        $sdate = str_replace("-", "", Lib::quote($req['sdate']) ?? now()->sub(1, 'week')->format('Ymd'));
        $edate = str_replace("-", "", Lib::quote($req['edate']) ?? date("Ymd"));

        if ($req['date_type'] != "") {
            if ($req['date_type'] == "req_rt") {
                $where .= " 
                    and cast(psr.req_rt as date) >= '$sdate'
                    and cast(psr.req_rt as date) <= '$edate'
                ";
            } else if ($req['date_type'] == "dlv_day") {
                $where .= " 
                    and cast(psr.exp_dlv_day as date) >= '$sdate'
                    and cast(psr.exp_dlv_day as date) <= '$edate'
                ";
            }
        }

        if ($req['type'] != "") $where .= " and pc.brand = '" . Lib::quote($req['type']) . "'";
        if ($req['opt'] != "") $where .= " and pc.opt = '" . Lib::quote($req['opt']) . "'";
        if ($req['prd_nm'] != "") $where .= " and p.prd_nm like '________%" . Lib::quote($req['prd_nm']) . "%' ";

        if ($req['rel_order'] != null)
            $where .= " and psr.rel_order like '%" . Lib::quote($req['rel_order']) . "'";
        if ($req['rel_type'] != null)
            $where .= " and psr.type = '" . Lib::quote($req['rel_type']) . "'";
        if ($req['state'] != null)
            $where .= " and psr.state = '" . Lib::quote($req['state']) . "'";
        if ($req['ext_done_state'] ?? '' != '')
            $where .= " and psr.state != '40'";
        if (isset($req['store_no']))
            $where .= " and s.store_cd = '" . Lib::quote($req['store_no']) . "'";
        if ($req['prd_cd_sub'] != null) {
            $prd_cd = explode(',', $req['prd_cd_sub']);
            $where .= " and (1!=1";
            foreach ($prd_cd as $cd) {
                $where .= " or psr.prd_cd like '" . Lib::quote($cd) . "%' ";
            }
            $where .= ")";
        }
        if ($req['store_channel'] != '') $where .= "and s.store_channel ='" . Lib::quote($req['store_channel']). "'";
        if ($req['store_channel_kind'] ?? '' != '') $where .= "and s.store_channel_kind ='" . Lib::quote($req['store_channel_kind']). "'";

        // ordreby
        $ord = $req['ord'] ?? 'desc';
        $ord_field = $req['ord_field'] ?? "psr.req_rt";
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
                psr.idx,
                cast(if(psr.state < 30, psr.exp_dlv_day, psr.prc_rt) as date) as dlv_day,
                psr.prd_cd, 
                psr.prd_cd,
                i.img_url as img,
                p.prd_cd as prd_cd,
                p.prd_nm as prd_nm,
                c.code_val as type_nm,
                c2.code_val as opt,
                c3.code_val as color,
                c4.code_val as size,
                c5.code_val as unit,
                c6.code_val as rel_type,
                ifnull(p.price, 0) as price,
                ifnull(p.wonga, 0) as wonga,
                psr.qty,
                psr.store_cd,
                s.store_nm, 
                psr.storage_cd,
                sg.storage_nm, 
                psr.state, 
                cast(psr.exp_dlv_day as date) as exp_dlv_day, 
                -- psr.rel_order,
                c7.code_val3 as rel_order, 
                psr.comment,
                psr.req_comment,
                psr.req_id, 
                psr.req_rt, 
                psr.rec_id, 
                psr.rec_rt, 
                psr.prc_id, 
                psr.prc_rt, 
                psr.fin_id, 
                psr.fin_rt
            from sproduct_stock_release psr
                inner join product p on psr.prd_cd = p.prd_cd
                inner join product_code pc on p.prd_cd = pc.prd_cd
                left outer join product_image i on i.prd_cd = pc.prd_cd
                left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
                left outer join `code` c4 on c4.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c4.code_id = pc.size
                left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
                left outer join `code` c6 on c6.code_kind_cd = 'REL_TYPE' and c6.code_id = psr.type
                left outer join `code` c7 on c7.code_kind_cd = 'REL_ORDER' and c7.code_id = psr.rel_order
                left outer join store s on s.store_cd = psr.store_cd
                left outer join storage sg on sg.storage_cd = psr.storage_cd
            where 1=1 $where
            $orderby
            $limit
		";
        $result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if ($page == 1) {
            $sql = "
                select count(*) as total
                from sproduct_stock_release psr
                    inner join product p on psr.prd_cd = p.prd_cd
                    inner join product_code pc on p.prd_cd = pc.prd_cd
                    left outer join product_image i on i.prd_cd = pc.prd_cd
                    left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                    left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                    left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
                    left outer join `code` c4 on c4.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c4.code_id = pc.size
                    left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
                    left outer join `code` c6 on c6.code_kind_cd = 'REL_TYPE' and c6.code_id = psr.type
                    left outer join store s on s.store_cd = psr.store_cd
                    left outer join storage sg on sg.storage_cd = psr.storage_cd
                where 1=1 $where
                order by psr.rt
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
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

    // 접수 (10 -> 20)
    public function receipt(Request $request)
    {
        $ori_state = 10;
        $new_state = 20;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        $data = $request->input("data", []);
        $exp_dlv_day = $request->input("exp_dlv_day", '');
        $rel_order = $request->input("rel_order", '');

        try {

            DB::beginTransaction();

            foreach ($data as $row) {

                if ($row['state'] != $ori_state) continue;

                $prd_cd = $row['prd_cd'];
                $price = $row['price'];
                $wonga = $row['wonga'];

                $result = DB::table('product_stock_storage')
                ->where('prd_cd', '=', $prd_cd)
                ->where('storage_cd', '=', DB::raw("(select storage_cd from storage where default_yn = 'Y')"))
                ->get()[0];

                $storage_qty = $result->wqty;

                if ((int)$row['qty'] > $storage_qty) { // 창고수량보다 접수 수량이 많은 경우 에러처리
                    DB::rollback();
                    return response()->json(["code" => -1, "prd_cd" => $prd_cd]);
                }
                
                DB::table('sproduct_stock_release')
                    ->where('idx', '=', $row['idx'])
                    ->update([
                        'qty' => $row['qty'] ?? 0,
                        'exp_dlv_day' => str_replace("-", "", $exp_dlv_day),
                        'rel_order' => $rel_order,
                        'state' => $new_state,
                        'comment' => $row['comment'],
                        'rec_id' => $admin_id,
                        'rec_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', '=', $prd_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($row['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $prd_cd)
                    ->where('storage_cd', '=', $row['storage_cd'])
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($row['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                //재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'prd_cd' => $prd_cd,
                        'location_cd' => $row['storage_cd'],
                        'location_type' => 'STORAGE',
                        'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
                        'price' => $price,
                        'wonga' => $wonga,
                        'qty' => ($row['qty'] ?? 0) * -1,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '창고출고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);

                // product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
                $store_stock_cnt =
                    DB::table('product_stock_store')
                    ->where('store_cd', '=', $row['store_cd'])
                    ->where('prd_cd', '=', $prd_cd)
                    ->count();
                if ($store_stock_cnt < 1) {
                    // 해당 매장에 상품 기존재고가 없을 경우
                    DB::table('product_stock_store')
                        ->insert([
                            'prd_cd' => $prd_cd,
                            'store_cd' => $row['store_cd'],
                            'qty' => 0,
                            'wqty' => $row['qty'] ?? 0,
                            'use_yn' => 'Y',
                            'rt' => now(),
                        ]);
                } else {
                    // 해당 매장에 상품 기존재고가 이미 존재할 경우
                    DB::table('product_stock_store')
                        ->where('prd_cd', '=', $prd_cd)
                        ->where('store_cd', '=', $row['store_cd'])
                        ->update([
                            'wqty' => DB::raw('wqty + ' . ($row['qty'] ?? 0)),
                            'ut' => now(),
                        ]);
                }

                // 재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'prd_cd' => $prd_cd,
                        'location_cd' => $row['store_cd'],
                        'location_type' => 'STORE',
                        'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
                        'price' => $price,
                        'wonga' => $wonga,
                        'qty' => $row['qty'] ?? 0,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '매장입고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);

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

    // 출고 (20 -> 30)
    public function release(Request $request)
    {
        $ori_state = 20;
        $new_state = 30;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
        $data = $request->input("data", []);
		$date = Carbon::now()->timezone('Asia/Seoul')->format('Y-m-d');
		$stock_state_date = str_replace("-", "", $date); 
		$now = now();

        try {
            DB::beginTransaction();
            foreach ($data as $row) {
                $prd_cd = $row['prd_cd'];
                $qty = $row['qty'];

                if ($row['state'] != $ori_state) continue;

                DB::table('sproduct_stock_release')
                    ->where('idx', '=', $row['idx'])
                    ->update([
                        'state' => $new_state,
                        'prc_id' => $admin_id,
                        'prc_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock_storage 창고 실재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $prd_cd)
                    ->where('storage_cd', '=', $row['storage_cd'])
                    ->update([
                        'qty' => DB::raw('qty - ' . ($qty ?? 0)),
                        'ut' => now(),
                    ]);

                    $res = "
                    select 
                        a.goods_no as goods_no,
                        a.goods_opt as goods_opt,
                        b.price as price,
                        b.wonga as wonga
                    from product_code a
                        inner join product b on a.prd_cd = b.prd_cd
                    where a.prd_cd = '$prd_cd'
                ";
                $r = DB::selectOne($res);

                $query = "
                    insert into 
                    product_stock_hst(goods_no, prd_cd, goods_opt, location_type, type, price, wonga, qty, stock_state_date, comment, rt, admin_id, admin_nm) 
                    values('$r->goods_no', '$prd_cd', '$r->goods_opt', 'STORAGE', '17', '$r->price', '$r->wonga', '$qty', '$stock_state_date', '창고출고(원부자재)', '$now', '$admin_id', '$admin_nm')
                ";

                DB::insert($query);

            }
            $code = 200;
            DB::commit();
        } catch (Exception $e) {
            $code = 500;
            DB::rollback();
        }
        return response()->json(["code" => $code]);
    }

    // 매장입고 (30 -> 40)
    public function receive(Request $request)
    {
        $ori_state = 30;
        $new_state = 40;
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);
        try {
            DB::beginTransaction();
            foreach ($data as $row) {
                if ($row['state'] != $ori_state) continue;
                DB::table('sproduct_stock_release')
                    ->where('idx', '=', $row['idx'])
                    ->update([
                        'state' => $new_state,
                        'fin_id' => $admin_id,
                        'fin_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock_store 매장 실재고 플러스
                DB::table('product_stock_store')
                    ->where('prd_cd', '=', $row['prd_cd'])
                    ->where('store_cd', '=', $row['store_cd'])
                    ->update([
                        'qty' => DB::raw('qty + ' . ($row['qty'] ?? 0)),
                        'ut' => now(),
                    ]);
            }
            $code = 200;
            DB::commit();
        } catch (Exception $e) {
            $code = 500;
            DB::rollback();
            // $msg = $e->getMessage();
        }
        return response()->json(["code" => $code]);
    }

    // 거부 (10 -> -10)
    public function reject(Request $request)
    {
        $ori_state = 10;
        $new_state = -10;
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);
        try {
            DB::beginTransaction();
            foreach ($data as $row) {
                if ($row['state'] != $ori_state) continue;
                DB::table('sproduct_stock_release')
                    ->where('idx', '=', $row['idx'])
                    ->update([
                        'state' => $new_state,
                        'comment' => $row['comment'] ?? '',
                        'fin_id' => $admin_id,
                        'fin_rt' => now(),
                        'ut' => now(),
                    ]);
            }
            $code = 200;
            DB::commit();
        } catch (Exception $e) {
            $code = 500;
            DB::rollback();
            // $msg = $e->getMessage();
        }
        return response()->json(["code" => $code]);
    }

    //삭제
    public function del_release(Request $request) 
    {
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach ($data as $row) {

                $prd_cd = $row['prd_cd'];
                $price = $row['price'];
                $wonga = $row['wonga'];

                DB::table('sproduct_stock_release')
                    ->where('idx', '=', $row['idx'])
                    ->delete();

                // product_stock 보유재고 플러스
                DB::table('product_stock')
                    ->where('prd_cd', '=', $prd_cd)
                    ->update([
                        'wqty' => DB::raw('wqty + ' . ($row['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 플러스
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $prd_cd)
                    ->where('storage_cd', '=', $row['storage_cd'])
                    ->update([
                        'wqty' => DB::raw('wqty + ' . ($row['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // 재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'prd_cd' => $prd_cd,
                        'location_cd' => $row['storage_cd'],
                        'location_type' => 'STORAGE',
                        'type' => PRODUCT_STOCK_TYPE_STORAGE_IN, 
                        'price' => $price,
                        'wonga' => $wonga,
                        'qty' => ($row['qty'] ?? 0),
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '창고입고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);

                // 매장재고 원복
                DB::table('product_stock_store')
                    ->where('prd_cd', '=', $prd_cd)
                    ->where('store_cd', '=', $row['store_cd'])
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($row['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // // 재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'prd_cd' => $prd_cd,
                        'location_cd' => $row['store_cd'],
                        'location_type' => 'STORE',
                        'type' => PRODUCT_STOCK_TYPE_STORE_OUT,
                        'price' => $price,
                        'wonga' => $wonga,
                        'qty' => ($row['qty'] ?? 0) * -1,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '매장출고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);
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
