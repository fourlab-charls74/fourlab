<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // 출고

class stk12Controller extends Controller
{
    public function index()
	{
        $sql = "
            select
                *
            from code
            where code_kind_cd = 'rel_order' and code_id like 'F_%'
        ";
        $rel_order_res = DB::select($sql);

        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderBy('default_yn')->get();

		$values = [
            'today'         => date("Y-m-d"),
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            // 'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
            // 'rel_orders'    => SLib::getCodes("REL_ORDER"), // 출고차수
            'storages'      => $storages, // 창고리스트
            'rel_order_res' => $rel_order_res //초도출고차수
		];

        return view(Config::get('shop.store.view') . '/stock/stk12', $values);
	}

	public function search(Request $request)
	{
		$r = $request->all();
        
		$code = 200;
		$where = "";
        $orderby = "";

        $prd_cd_range_text = $request->input("prd_cd_range", '');

        // where
		if($r['prd_cd'] != null) {
            $prd_cd = explode(',', $r['prd_cd']);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
        }
        if($r['invoice_no'] != null) {
            $invoice_no = $r['invoice_no'];
            $where .= " and p.prd_cd in (select prd_cd from stock_product where invoice_no = '$invoice_no')";
        }
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

        // 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
			}
		}

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
        if(($r['ext_storage_qty'] ?? 'false') == 'true')
            $where .= " and (p.wqty != '' and p.wqty != '0')";

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
        $store_cds = $r['store_no'] ?? [];
        $store_type = $r['store_type'] ?? '';
        $stores = [];
        $store_select_sql = "";
        foreach($store_cds as $store_cd) {
            $row = DB::table('store')->select('store_cd', 'store_nm', 'store_type')->where('store_cd', '=', $store_cd)->first();
            if($store_type == '' or ($store_type != '' and $row->store_type == $store_type)) {
                array_push($stores, $row);
                $store_select_sql .= "(select qty from product_stock_store where store_cd = '$store_cd' and prd_cd = p.prd_cd) as $store_cd" . "_qty,";
                $store_select_sql .= "(select wqty from product_stock_store where store_cd = '$store_cd' and prd_cd = p.prd_cd) as $store_cd" . "_wqty,";
            }
        }
        if(count($store_cds) < 1) {
            $stores = DB::table('store')->select('store_cd', 'store_nm')->where('store_type', '=', $store_type)->get();
            foreach($stores as $s) {
                $store_cd = $s->store_cd;
                $store_select_sql .= "(select qty from product_stock_store where store_cd = '$store_cd' and prd_cd = p.prd_cd) as $store_cd" . "_qty,";
                $store_select_sql .= "(select wqty from product_stock_store where store_cd = '$store_cd' and prd_cd = p.prd_cd) as $store_cd" . "_wqty,";
            }
        }

		$sql = "
            select
                p.goods_no,
                g.goods_type,
                ifnull(type.code_val, 'N/A') as goods_type_nm,
                p.prd_cd,
                op.opt_kind_nm,
                b.brand_nm,
                g.style_no,
                stat.code_val as sale_stat_cl,
                g.goods_nm,
                g.goods_nm_eng,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                pc.color,
                pc.size,
                p.goods_opt,
                p.qty as storage_qty,
                p.wqty as storage_wqty,
                $store_select_sql
                '' as blank
            from product_stock_storage p
                left outer join product_code pc on pc.prd_cd = p.prd_cd
                inner join goods g on g.goods_no = p.goods_no
                inner join brand b on b.brand = g.brand
                inner join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                inner join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                inner join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
            where p.storage_cd = (select storage_cd from storage where default_yn = 'Y') $where
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
                from product_stock_storage p
                    left outer join product_code pc on pc.prd_cd = p.prd_cd
                    inner join goods g on g.goods_no = p.goods_no
                    inner join brand b on b.brand = g.brand
                    inner join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                    inner join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                    inner join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                where p.storage_cd = (select storage_cd from storage where default_yn = 'Y') $where
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
				"page_total" => count($result),
                "stores" => $stores,
			],
			"body" => $result
		]);
	}

    // 초도출고 요청 (요청과 동시에 접수완료 처리됩니다.)
    public function request_release(Request $request) {
        $release_type = 'F';
        $state = 20;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
        $stores = $request->input("stores", '');
        $exp_dlv_day = $request->input("exp_dlv_day", '');
        $rel_order = $request->input("rel_order", '');
        $data = $request->input("products", []);
        $exp_day = str_replace("-", "", $exp_dlv_day);
        $exp_dlv_day_data = substr($exp_day,2,6);
        
        try {
            DB::beginTransaction();

            $storage_cd = DB::table('storage')->where('default_yn', '=', 'Y')->select('storage_cd')->get();
            $storage_cd = $storage_cd[0]->storage_cd;

            foreach($data as $d) {
                $cnt = 0;

                $sql = "
                    select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
                    from product_code pc
                        inner join goods g on g.goods_no = pc.goods_no
                    where prd_cd = :prd_cd
                ";
                $prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
                if($prd == null) continue;

                foreach($stores as $store_cd) {
                    $rel_qty = $d[$store_cd . '_rel_qty'] ?? 0;

                    DB::table('product_stock_release')
                        ->insert([
                            'type' => $release_type,
                            'goods_no' => $prd->goods_no,
                            'prd_cd' => $prd->prd_cd,
                            'goods_opt' => $prd->goods_opt,
                            'qty' => $rel_qty,
                            'store_cd' => $store_cd,
                            'storage_cd' => $storage_cd,
                            'state' => $state,
                            'exp_dlv_day' => $exp_dlv_day_data,
                            'rel_order' =>  $exp_dlv_day_data . '-' . $rel_order,
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
                            ->where('prd_cd', '=', $prd->prd_cd)
                            ->count();
                    if($store_stock_cnt < 1) {
                        // 해당 매장에 상품 기존재고가 없을 경우
                        DB::table('product_stock_store')
                            ->insert([
                                'goods_no' => $prd->goods_no,
                                'prd_cd' => $prd->prd_cd,
                                'store_cd' => $store_cd,
                                'qty' => 0,
                                'wqty' => $rel_qty,
                                'goods_opt' => $prd->goods_opt,
                                'use_yn' => 'Y',
                                'rt' => now(),
                            ]);
                    } else {
                        // 해당 매장에 상품 기존재고가 이미 존재할 경우
                        DB::table('product_stock_store')
                            ->where('prd_cd', '=', $prd->prd_cd)
                            ->where('store_cd', '=', $store_cd) 
                            ->update([
                                'wqty' => DB::raw('wqty + ' . ($rel_qty)),
                                'ut' => now(),
                            ]);
                    }

                    // 재고이력 등록
                    DB::table('product_stock_hst')
                        ->insert([
                            'goods_no' => $prd->goods_no,
                            'prd_cd' => $prd->prd_cd,
                            'goods_opt' => $prd->goods_opt,
                            'location_cd' => $store_cd,
                            'location_type' => 'STORE',
                            'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
                            'price' => $prd->price,
                            'wonga' => $prd->wonga,
                            'qty' => $rel_qty,
                            'stock_state_date' => date('Ymd'),
                            'ord_opt_no' => '',
                            'comment' => '매장입고',
                            'rt' => now(),
                            'admin_id' => $admin_id,
                            'admin_nm' => $admin_nm,
                        ]);

                    $cnt += $rel_qty;
                }
    
                // product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->update([
                        'wqty' => DB::raw("wqty - $cnt"),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->where('storage_cd', '=', $storage_cd)
                    ->update([
                        'wqty' => DB::raw("wqty - $cnt"),
                        'ut' => now(),
                    ]);

                // 재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'goods_no' => $prd->goods_no,
                        'prd_cd' => $prd->prd_cd,
                        'goods_opt' => $prd->goods_opt,
                        'location_cd' => $storage_cd,
                        'location_type' => 'STORAGE',
                        'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
                        'price' => $prd->price,
                        'wonga' => $prd->wonga,
                        'qty' => $cnt * -1,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '창고출고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
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
