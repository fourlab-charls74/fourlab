<?php

namespace App\Http\Controllers\store\cs;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class cs02Controller extends Controller
{
    public function index()
	{
		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'rel_orders'     => SLib::getCodes("REL_ORDER"), // 출고차수
            'rel_types'     => SLib::getCodes("REL_TYPE"), // 출고구분
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            // 'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'), // 상품구분(2)
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/cs/cs02', $values);
	}

    public function search(Request $request)
    {
        $r = $request->all();

		$code = 200;
		$where = "";
        $orderby = "";
        
        // where
        $sdate = str_replace("-", "", $r['sdate'] ?? now()->sub(1, 'week')->format('Ymd'));
        $edate = str_replace("-", "", $r['edate'] ?? date("Ymd"));
        $where .= "
            and cast(if(psr.state > 20, psr.prc_rt, if(psr.state > 10, psr.exp_dlv_day, psr.req_rt)) as date) >= '$sdate' 
            and cast(if(psr.state > 20, psr.prc_rt, if(psr.state > 10, psr.exp_dlv_day, psr.req_rt)) as date) <= '$edate'
        ";
		if($r['rel_order'] != null)
			$where .= " and psr.rel_order = '" . $r['rel_order'] . "'";
		if($r['rel_type'] != null) 
			$where .= " and psr.type = '" . $r['rel_type'] . "'";
		if($r['state'] != null) 
			$where .= " and psr.state = '" . $r['state'] . "'";
		if($r['store_type'] != null) 
			$where .= " and s.store_type = '" . $r['store_type'] . "'";
		if(isset($r['store_no'])) 
			$where .= " and s.store_cd = '" . $r['store_no'] . "'";
		if($r['prd_cd'] != null) 
			$where .= " and psr.prd_cd = '" . $r['prd_cd'] . "'";
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

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "psr.req_rt";
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
                psr.idx,
                cast(if(psr.state < 30, psr.exp_dlv_day, psr.prc_rt) as date) as dlv_day,
                c.code_val as rel_type, 
                psr.goods_no, 
                g.style_no, 
                g.goods_nm, 
                psr.prd_cd, 
                psr.goods_opt, 
                psr.qty,
                psr.store_cd,
                s.store_nm, 
                psr.storage_cd,
                sg.storage_nm, 
                psr.state, 
                cast(psr.exp_dlv_day as date) as exp_dlv_day, 
                psr.rel_order, 
                psr.comment,
                psr.req_id, 
                psr.req_rt, 
                psr.rec_id, 
                psr.rec_rt, 
                psr.prc_id, 
                psr.prc_rt, 
                psr.fin_id, 
                psr.fin_rt
            from product_stock_release psr
                inner join goods g on g.goods_no = psr.goods_no
                left outer join code c on c.code_kind_cd = 'REL_TYPE' and c.code_id = psr.type
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
        if($page == 1) {
            $sql = "
                select count(*) as total
                from product_stock_release psr
                    inner join goods g on g.goods_no = psr.goods_no
                    left outer join code c on c.code_kind_cd = 'REL_TYPE' and c.code_id = psr.type
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
}
