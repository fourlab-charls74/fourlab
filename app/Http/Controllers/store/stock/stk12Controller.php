<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk12Controller extends Controller
{
    public function index()
	{
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderBy('default_yn')->get();

		$values = [
            'today'         => date("Y-m-d"),
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
            'rel_orders'    => SLib::getCodes("REL_ORDER"), // 출고차수
            'storages'      => $storages, // 창고리스트
		];

        return view(Config::get('shop.store.view') . '/stock/stk12', $values);
	}

	public function search(Request $request)
	{
		$r = $request->all();

		$code = 200;
		$where = "";
        $orderby = "";

        // where
		if($r['prd_cd'] != null) 
			$where .= " and p.prd_cd = '" . $r['prd_cd'] . "'";
		if($r['type'] != null) 
			$where .= " and g.type = '" . $r['type'] . "'";
		if($r['goods_type'] != null) 
			$where .= " and g.goods_type = '" . $r['goods_type'] . "'";
        if(isset($r['goods_stat'])) {
            $goods_stat = $r['goods_stat'];
            if(is_array($goods_stat)) {
                if (count($goods_stat) == 1 && $goods_stat[0] != "") {
                    $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
                } else if (count($goods_stat) > 1) {
                    $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
                }
            } else if($goods_stat != ""){
                $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
            }
        } 
        if($r['style_no'] != null) 
            $where .= " and g.style_no = '" . $r['style_no'] . "'";

        $goods_no = $r['goods_no'];
        $goods_nos = $request->input('goods_nos', '');
        if($goods_nos != '') $goods_no = $goods_nos;
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if($goods_no != ""){
            $goods_nos = explode(",", $goods_no);
            if(count($goods_nos) > 1) {
                if(count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(",", $goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        if($r['com_type'] != null) 
            $where .= " and g.com_type = '" . $r['com_type'] . "'";
        if($r['com_cd'] != null) 
            $where .= " and g.com_id = '" . $r['com_cd'] . "'";
        if($r['item'] != null) 
            $where .= " and g.opt_kind_cd = '" . $r['item'] . "'";
        if(isset($r['brand_cd']))
            $where .= " and g.brand = '" . $r['brand_cd'] . "'";
        if($r['goods_nm'] != null) 
            $where .= " and g.goods_nm like '%" . $r['goods_nm'] . "%'";
        if($r['goods_nm_eng'] != null) 
            $where .= " and g.goods_nm_eng like '%" . $r['goods_nm_eng'] . "%'";

        // orderby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "g.goods_no";
        if($ord_field == 'goods_no') $ord_field = 'g.' . $ord_field;
        else $ord_field = 'psr.' . $ord_field;
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        // search
		$sql = "
            select
                g.goods_no, 
                g.goods_type,
                ifnull(type.code_val, 'N/A') as goods_type_nm, 
                p.prd_cd, 
                op.opt_kind_nm,
                b.brand_nm, 
                g.style_no, 
                stat.code_val as sale_stat_cl, 
                g.goods_nm, 
                p.goods_opt,
                pss.qty as storage_qty,
                pss.wqty as storage_wqty
            from product_stock p
                inner join goods g on p.goods_no = g.goods_no
                left outer join product_stock_storage pss on pss.prd_cd = p.prd_cd and pss.storage_cd = (select storage_cd from storage where default_yn = 'Y')
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
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
                from product_stock p
                    inner join goods g on p.goods_no = g.goods_no
                    left outer join product_stock_storage pss on pss.prd_cd = p.prd_cd and pss.storage_cd = (select storage_cd from storage where default_yn = 'Y')
                    left outer join brand b on b.brand = g.brand
                    left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                    left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                    left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                where 1=1 $where
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $store_cds = explode(',', $r['store_nos']);

        foreach($result as $re) {
            $prd_cd = $re->prd_cd;
            foreach($store_cds as $cd) {
                $qty = $cd . '_qty';
                $wqty = $cd . '_wqty';
                $rel_qty = $cd . '_rel_qty';
                $sql = "
                    select qty as $qty, wqty as $wqty
                    from product_stock_store
                    where 1=1
                        and prd_cd = '$prd_cd'
                        and store_cd = '$cd'
                ";
                $row = DB::selectOne($sql);
                $re->$cd = $row;
            }
        }
        // dd($result);
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

    // 초도출고 요청 (요청과 동시에 접수완료 처리됩니다.)
    public function request_release(Request $request) {
        $r = $request->all();

        $release_type = 'F';
        $state = 20;
        $admin_id = Auth('head')->user()->id;
        $stores = $request->input("stores", '');
        $exp_dlv_day = $request->input("exp_dlv_day", '');
        $rel_order = $request->input("rel_order", '');
        $data = $request->input("products", []);
        
        try {
            DB::beginTransaction();

            $storage_cd = DB::table('storage')->where('default_yn', '=', 'Y')->select('storage_cd')->get();
            $storage_cd = $storage_cd[0]->storage_cd;

            foreach($data as $d) {
                $cnt = 0;
                foreach($stores as $store_cd) {
                    $rel_qty = $d[$store_cd . '_rel_qty'] ?? 0;

                    DB::table('product_stock_release')
                        ->insert([
                            'type' => $release_type,
                            'goods_no' => $d['goods_no'],
                            'prd_cd' => $d['prd_cd'],
                            'goods_opt' => $d['goods_opt'] ?? '',
                            'qty' => $rel_qty,
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

                    // product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
                    $store_stock_cnt = 
                        DB::table('product_stock_store')
                            ->where('store_cd', '=', $store_cd)
                            ->where('prd_cd', '=', $d['prd_cd'])
                            ->count();
                    if($store_stock_cnt < 1) {
                        // 해당 매장에 상품 기존재고가 없을 경우
                        DB::table('product_stock_store')
                            ->insert([
                                'goods_no' => $d['goods_no'],
                                'prd_cd' => $d['prd_cd'],
                                'store_cd' => $store_cd,
                                'qty' => 0,
                                'wqty' => $rel_qty,
                                'goods_opt' => $d['goods_opt'] ?? '',
                                'use_yn' => 'Y',
                                'rt' => now(),
                            ]);
                    } else {
                        // 해당 매장에 상품 기존재고가 이미 존재할 경우
                        DB::table('product_stock_store')
                            ->where('prd_cd', '=', $d['prd_cd'])
                            ->where('store_cd', '=', $store_cd) 
                            ->update([
                                'wqty' => DB::raw('wqty + ' . ($rel_qty)),
                                'ut' => now(),
                            ]);
                    }

                    $cnt += $rel_qty;
                }
    
                // product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', '=', $d['prd_cd'])
                    ->update([
                        'wqty' => DB::raw("wqty - $cnt"),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $d['prd_cd'])
                    ->where('storage_cd', '=', $storage_cd)
                    ->update([
                        'wqty' => DB::raw("wqty - $cnt"),
                        'ut' => now(),
                    ]);

            }

            DB::commit();
            $code = 200;
            $msg = "초도출고 요청 및 접수가 정상적으로 등록되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
