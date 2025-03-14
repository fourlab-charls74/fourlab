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

class stk15Controller extends Controller
{
    public function index()
	{
        $sql = "
			select
				*
			from code
			where code_kind_cd = 'rel_order' and code_id like 'G_%'
		";
		$rel_order_res = DB::select($sql);

        $stores = DB::table('store')->where('use_yn', '=', 'Y')->select('store_cd', 'store_nm')->get();
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderBy('default_yn')->get();

		$values = [
            'today'         => date("Y-m-d"),
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
            // 'rel_orders'    => SLib::getCodes("REL_ORDER"), // 출고차수
            'stores'        => $stores, // 매장리스트
            'storages'      => $storages, // 창고리스트
            'rel_order_res' => $rel_order_res //일반출고 차수
		];

        return view(Config::get('shop.shop.view') . '/stock/stk15', $values);
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

        $having = "";
        if(($r['ext_storage_qty'] ?? 'false') == 'true')
            $having .= " and sum(p.wqty) > '0'";

        // 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

        // orderby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "prd_cd";
        $orderby = sprintf("order by %s %s", "p.".$ord_field, $ord);

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
                g.goods_nm_eng,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                pc.color,
                pc.size,
                p.goods_opt, 
                '' as rel_qty
            from product_stock_storage p
                inner join goods g on p.goods_no = g.goods_no
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join product_code pc on pc.prd_cd = p.prd_cd
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
        if($page == 1) {
            $sql = "
                select count(c.prd_cd) as total
                from (
                    select p.prd_cd, count(p.prd_cd)
                    from product_stock_storage p
                        inner join goods g on p.goods_no = g.goods_no
                        left outer join brand b on b.brand = g.brand
                        left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                        left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                        left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                        left outer join product_code pc on pc.prd_cd = p.prd_cd
                    where 1=1 $where
                    group by p.prd_cd
                    having 1=1 $having
                ) as c
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        foreach($result as $re) {
            $prd_cd = $re->prd_cd;
            $sql = "
                select s.storage_cd, p.prd_cd, p.wqty
                from storage s
                    left outer join product_stock_storage p on p.storage_cd = s.storage_cd and p.prd_cd = '$prd_cd'
                where s.use_yn = 'Y' and p.use_yn = 'Y'
            ";
            $row = DB::select($sql);
            $re->storage_qty = $row;
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
        $exp_day = str_replace("-", "", $exp_dlv_day);
        $exp_dlv_day_data = substr($exp_day,2,6);

        try {
            DB::beginTransaction();

			foreach($data as $d) {

                $sql = "
                    select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
                    from product_code pc
                        inner join goods g on g.goods_no = pc.goods_no
                    where prd_cd = :prd_cd
                ";
                $prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
                if($prd == null) continue;

                DB::table('product_stock_release')
                    ->insert([
                        'type' => $release_type,
                        'goods_no' => $prd->goods_no,
                        'prd_cd' => $prd->prd_cd,
                        'goods_opt' => $prd->goods_opt,
                        'qty' => $d['rel_qty'] ?? 0,
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
    
                // product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($d['rel_qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->where('storage_cd', '=', $storage_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($d['rel_qty'] ?? 0)),
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
                        'qty' => ($d['rel_qty'] ?? 0) * -1,
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
                            'wqty' => $d['rel_qty'] ?? 0,
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
                            'wqty' => DB::raw('wqty + ' . ($d['rel_qty'] ?? 0)),
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
                        'qty' => $d['rel_qty'] ?? 0,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '매장입고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "일반출고 요청 및 접수가 정상적으로 등록되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    public function chg_store_type(Request $request)
    {
        $store_type = $request->input('store_type');

        try {
            DB::beginTransaction();

            if ($store_type == '') {
                $sql = "
                    select 
                        store_nm,
                        store_cd
                    from store
                ";
            } else {
                $sql = "
                    select 
                        store_nm,
                        store_cd
                    from store
                    where store_type = '$store_type'
                ";
            }
			
            $stores = DB::select($sql);

			DB::commit();
            $code = 200;
            $msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg, 'stores' => $stores]);
    }
}
