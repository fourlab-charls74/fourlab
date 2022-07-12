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
                pss.wqty as storage_wqty,
                '' as rel_qty
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

        // foreach($result as $re) {
        //     $prd_cd = $re->prd_cd;
        //     $store_cd = $r['store_no'];
        //     $sql = "
        //         select qty, wqty
        //         from product_stock_store
        //         where store_cd ='$store_cd' and prd_cd = '$prd_cd'
        //     ";
        //     $row = DB::selectOne($sql);
        //     $re->store_qty = $row;
        // }
        dd($result);
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

		////////////////////////////////////
		
		// $page	= $request->input('page', 1);
		// if( $page < 1 or $page == "" )	$page = 1;
		// $limit	= $request->input('limit', 100);

		// $sdate		= $request->input('sdate',Carbon::now()->sub(2, 'year')->format('Ymd'));
		// $edate		= $request->input('edate',date("Ymd"));
		// $com_type	= $request->input('com_type');
		// $com_nm		= $request->input('com_nm');
		// $goods_code	= $request->input('goods_code');
		// $event_cd	= $request->input('event_cd');
		// $user_id	= $request->input('user_id');
		// $sell_type	= $request->input('sell_type');

		// $limit		= $request->input("limit",100);
		// $ord		= $request->input('ord','desc');
		// $ord_field	= $request->input('ord_field','g.goods_no');
		// $orderby	= sprintf("order by %s %s", $ord_field, $ord);

		// $where	= "";
		// if( $com_type != "" )	$where .= " and a.com_type = '" . $com_type . "' ";
		// if( $com_nm != "" )		$where .= " and a.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		// if( $goods_code != "" )	$where .= " and a.goods_code like '" . Lib::quote($goods_code) . "%' ";
		// if( $event_cd != "" )	$where .= " and a.event_cd = '" . $event_cd . "' ";
		// if( $user_id != "" )	$where .= " and a.user_id = '" . Lib::quote($user_id) . "%' ";
		// if( $sell_type != "" )	$where .= " and a.sell_type = '" . $sell_type . "' ";

		// $page_size	= $limit;
		// $startno	= ($page - 1) * $page_size;
		// $limit		= " limit $startno, $page_size ";

		// $total		= 0;
		// $page_cnt	= 0;

		// if( $page == 1 ){
		// 	$query	= "
		// 		select count(*) as total
		// 		from __tmp_order a
		// 		where 1=1 
		// 			and ( a.ord_date >= :sdate and a.ord_date < date_add(:edate,interval 1 day))
		// 			$where
		// 	";
		// 	//$row = DB::select($query,['com_id' => $com_id]);
		// 	$row		= DB::select($query, ['sdate' => $sdate,'edate' => $edate]);
		// 	$total		= $row[0]->total;
		// 	$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		// }

		// $query	= "
		// 	select
		// 		a.*, 
		// 		(100 - round(a.price/a.goods_sh * 100)) as sale_rate,
		// 		(100 - round(a.ord_amt/a.goods_sh * 100)) as ord_sale_rate,
		// 		( (100 - round(a.ord_amt/a.goods_sh * 100)) - (100 - round(a.price/a.goods_sh * 100)) ) as sale_gap,
		// 		b.code_val as com_type_nm,
		// 		c.code_val as opt_kind_nm,
		// 		d.code_val as brand_nm,
		// 		e.code_val as stat_pay_type_nm,
		// 		f.code_val as sell_type_nm,
		// 		g.code_val as event_kind_nm
		// 	from __tmp_order a
		// 	left outer join __tmp_code b on b.code_kind_cd = 'com_type' and b.code_id = a.com_type
		// 	left outer join __tmp_code c on c.code_kind_cd = 'opt_kind_cd' and c.code_id = a.opt_kind
		// 	left outer join __tmp_code d on d.code_kind_cd = 'brand' and d.code_id = a.brand
		// 	left outer join __tmp_code e on e.code_kind_cd = 'stat_pay_type' and e.code_id = a.stat_pay_type
		// 	left outer join __tmp_code f on f.code_kind_cd = 'sell_type' and f.code_id = a.sell_type
		// 	left outer join __tmp_code g on g.code_kind_cd = 'event_cd' and g.code_id = a.event_cd
		// 	where 1=1 
		// 		and ( a.ord_date >= :sdate and a.ord_date < date_add(:edate,interval 1 day))
		// 		$where
		// 	$orderby
		// 	$limit
		// ";

		// $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

		// return response()->json([
		// 	"code"	=> 200,
		// 	"head"	=> array(
		// 		"total"		=> $total,
		// 		"page"		=> $page,
		// 		"page_cnt"	=> $page_cnt,
		// 		"page_total"=> count($result)
		// 	),
		// 	"body" => $result
		// ]);

	}
}
