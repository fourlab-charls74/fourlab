<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk17Controller extends Controller
{
    public function index()
	{
		$values = [
            'today'         => date("Y-m-d"),
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
            'rel_orders'    => SLib::getCodes("REL_ORDER"), // 출고차수
		];

        return view(Config::get('shop.store.view') . '/stock/stk17', $values);
	}

    public function search(Request $request)
    {      
        $r = $request->all();

		$code = 200;
		$where = "";
        $orderby = "";

        // where
        if($r['prd_cd'] != null) {
            $prd_cd = explode(',', $r['prd_cd']);
            $where .= " and (1!=1";
            foreach($prd_cd as $cd) {
                $where .= " or p.prd_cd = '" . Lib::quote($cd) . "' ";
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

        // if($r['com_cd'] != null) 
        //     $where .= " and g.com_id = '" . $r['com_cd'] . "'";
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
        $store_cd = $r['store_no'];
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
                p.goods_opt,
                p.qty as storage_qty,
                p.wqty as storage_wqty,
                (select qty from product_stock_store where store_cd = '$store_cd' and prd_cd = p.prd_cd) as store_qty,
                (select wqty from product_stock_store where store_cd = '$store_cd' and prd_cd = p.prd_cd) as store_wqty,
                '' as rel_qty
            from product_stock_storage p
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
			],
			"body" => $result
		]);
    }

    // 요청분출고 요청
    public function request_release(Request $request) {
        $r = $request->all();

        $release_type = 'R';
        $state = 10;
        $admin_id = Auth('head')->user()->id;
        $store_cd = $request->input("store_cd", '');
        // $exp_dlv_day = $request->input("exp_dlv_day", '');
        // $rel_order = $request->input("rel_order", '');
        $data = $request->input("products", []);
        
        $sql = "select storage_cd from storage where default_yn = 'Y'";
        $storage_cd = DB::selectOne($sql)->storage_cd;

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                DB::table('product_stock_release')
                    ->insert([
                        'type' => $release_type,
                        'goods_no' => $d['goods_no'],
                        'prd_cd' => $d['prd_cd'],
                        'goods_opt' => $d['goods_opt'] ?? '',
                        'qty' => $d['rel_qty'] ?? 0,
                        'store_cd' => $store_cd,
                        'storage_cd' => $storage_cd,
                        'state' => $state,
                        // 'exp_dlv_day' => str_replace("-", "", $exp_dlv_day),
                        // 'rel_order' => $rel_order,
                        'req_id' => $admin_id,
                        'req_rt' => now(),
                        'rt' => now(),
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "요청분출고 요청이 정상적으로 등록되었습니다.";
		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
