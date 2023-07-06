<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal06Controller extends Controller
{
	public function index(Request $request) 
	{
		$sdate = Carbon::now()->startOfMonth()->format("Y-m-d"); // 이번 달 기준
		$edate = Carbon::now()->format("Y-m-d");

		$store_types = SLib::getStoreTypes();
		$sale_kind_id = $request->input('sale_kind_id', "");

		$sale_kinds = SLib::getUsedSaleKinds();

		// 행사구분 - 추후 논의사항
		$sql = "
			select *
			from __tmp_code
			where
				code_kind_cd = 'event_cd' and use_yn = 'Y' order by code_seq
		";
		$event_cds = DB::select($sql);

		$values = [
            'sdate'         => $sdate,
			'edate'         => $edate,
			'store_types'	=> $store_types,
			'event_cds'		=> $event_cds,
			'sale_kinds' 	=> $sale_kinds,
			'items'			=> SLib::getItems(), // 품목
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
        return view( Config::get('shop.store.view') . '/sale/sal06', $values );
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->format("Ymd"));
		$edate = $request->input('edate', date("Ymd"));

		$sdate = str_replace("-", "", $sdate);
		$edate = str_replace("-", "", $edate);

		$store_type = $request->input('store_type', "");
		$store_cd = $request->input('store_cd', "");
		$goods_no = $request->input('goods_no', "");
		$goods_nm = $request->input("goods_nm", "");
		$goods_nm_eng = $request->input("goods_nm_eng", "");
        $brand_cd = $request->input("brand_cd");
		$style_no = $request->input('style_no', "");

		$sale_yn = $request->input('sale_yn','Y');
		$sale_kind = $request->input('sale_kind', "");
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");


		/**
		 * 검색조건 필터링
		 */
		$where = "";
		if ($brand_cd != "") $where .= " and b.brand = '" . Lib::quote($brand_cd) . "' ";

		$goods_no = preg_replace("/\s/", ",", $goods_no);
		$goods_no = preg_replace("/\t/", ",", $goods_no);
		$goods_no = preg_replace("/\n/", ",", $goods_no);
		$goods_no = preg_replace("/,,/", ",", $goods_no);
		if ($goods_no != "") {
			$goods_nos = explode(",", $goods_no);
			if (count($goods_nos) > 1) {
				if (count($goods_nos) > 500) array_splice($goods_nos, 500);
				$in_goods_nos = join(",", $goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
			}
		}
		
		if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%'";
		if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%'";
		if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%'";
		if ($sale_kind != "") $where .= " and o.sale_kind = '" . Lib::quote($sale_kind) . "' ";

        $where2 = "";
        if ($sale_yn == "Y") $where2 .= " and qty is not null";
		if ($store_channel != "") $where2 .= " and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where2 .= " and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
		if ($store_cd != "") $where2 .= " and s.store_cd like '" . Lib::quote($store_cd) . "%'";
	
		// 판매유형별 쿼리 추가
		$sale_kinds = SLib::getUsedSaleKinds();
		$sale_kinds_query = "";
		foreach ($sale_kinds as $item) {
			$id = $item->code_id;
			$sale_kinds_query .= "sum(if(o.sale_kind = '$id', w.qty, 0)) as sale_kind_$id, ";
		}

		$sql = /** @lang text */
		"
			select s.store_nm, c.code_val as store_type_nm, a.*
			from store s left outer join ( 
				select 
					m.store_cd,count(*) as cnt,
					$sale_kinds_query
					sum(w.qty) as qty,
					sum(w.qty * w.price) as amt,
					sum(w.recv_amt + w.point_apply_amt) as recv_amt,
					sum(w.qty * w.price - w.recv_amt) as discount,
					avg(w.price) as avg_price,
					avg(w.wonga) as wonga,
					sum(w.wonga * w.qty) as sum_wonga,
					sum(w.qty * w.price - w.wonga * w.qty) as sales_profit,
					(sum(w.qty * w.price) / sum(w.qty * w.price - w.wonga * w.qty)) * 100 as profit_rate,
					g.goods_type, c.code_val as sale_stat_cl_val, c2.code_val as goods_type_nm,
					o.goods_no, g.brand, b.brand_nm, g.style_no, o.goods_opt, g.img, g.goods_nm, g.goods_nm_eng
				from order_mst m 
					inner join order_opt o on m.ord_no = o.ord_no 
					inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no
					left outer join store s on m.store_cd = s.store_cd
					left outer join brand b on g.brand = b.brand
					left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
					left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
				where w.`ord_state_date` >= '$sdate' and w.ord_state_date <= '$edate' and w.`ord_state` in ( '30','60','61') 
					and m.store_cd <> '' $where
				group by m.store_cd
			) as a on s.store_cd = a.store_cd
				left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
			where 1=1 $where2
		";

		$result = DB::select($sql);

		return response()->json([
			'code'	=> 200,
			'head'	=> array(
				'total'	=> count($result)
			),
			'body' => $result
		]);

	}
}
