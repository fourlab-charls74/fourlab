<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;

class sal66Controller extends Controller
{
	public function index(Request $request)
	{
		$sdate = Carbon::now()->startOfMonth()->format("Y-m-d"); // 이번 달 기준
		$edate = Carbon::now()->format("Y-m-d");

		$sale_kind_id = $request->input('sale_kind_id', "");

		$values = [
			'sdate'         => $sdate,
			'edate'         => $edate,
			'sale_kinds' 	=> SLib::getCodes('SALE_KIND'),
			'items'			=> SLib::getItems(), // 품목
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view( Config::get('shop.store.view') . '/sale/sal66', $values );
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
		$prd_cd = $request->input('prd_cd', "");
		$sale_yn = $request->input('sale_yn','Y');
		$sale_kind = $request->input('sale_kind', "");
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$prd_cd_range_text 	= $request->input("prd_cd_range", '');

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

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// 상품코드
		if ($prd_cd != '') {
			$prd_cd = preg_replace("/\s/", ",", $prd_cd);
			$prd_cd = preg_replace("/\t/", ",", $prd_cd);
			$prd_cd = preg_replace("/\n/", ",", $prd_cd);
			$prd_cd = preg_replace("/,,/", ",", $prd_cd);
			$prd_cds = explode(',', $prd_cd);
			if (count($prd_cds) > 1) {
				$prd_cds_str = "";
				if (count($prd_cds) > 500) array_splice($prd_cds, 500);
				for($i =0; $i < count($prd_cds); $i++) {
					$prd_cds_str.= "'".$prd_cds[$i]."'";

					if($i !== count($prd_cds) -1) {
						$prd_cds_str .= ",";
					}
				}
				$where .= " and o.prd_cd in ($prd_cds_str) ";
			} else {
				$where .= " and o.prd_cd like '" . Lib::quote($prd_cd) . "%' ";
			}
		}

		// 판매유형별 쿼리 추가
		$sale_kinds = SLib::getUsedSaleKinds('','Y');
		$sale_kinds_query = "";
		foreach ($sale_kinds as $item) {
			$id = $item->code_id;
			$sale_kinds_query .= "sum(if(ifnull(o.sale_kind, '00') = '$id', if(w.ord_state = '30', w.qty, w.qty * -1), 0)) as sale_kind_$id, ";
		}

		$sql = /** @lang text */
			"
			select s.store_nm, c.store_channel as store_type_nm, a.*
			from store s left outer join ( 
				select 
					o.store_cd,count(*) as cnt,
					$sale_kinds_query
					sum(if(w.ord_state = '30', w.qty, w.qty * -1)) as qty,
					sum(w.qty * w.price) as amt,
					-- sum(w.recv_amt + w.point_apply_amt) as recv_amt,
					sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1)) as recv_amt,
					( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) as recv_amt_novat,
					-- sum(w.qty * w.price - w.recv_amt) as discount,
					sum(w.qty * w.price - if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1)) as discount,
					avg(w.price) as avg_price,
					avg(w.wonga) as wonga,
					-- sum(w.wonga * w.qty) as sum_wonga,
					sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) as sum_wonga,
					-- sum(w.qty * w.price - w.wonga * w.qty) as sales_profit,
					( 
						sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1
						- sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1))
					) as sales_profit,
					-- (sum(w.qty * w.price) / sum(w.qty * w.price - w.wonga * w.qty)) * 100 as profit_rate,
					if( (sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) > 0 or ( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 - sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) ) > 0,
						(( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 - sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) ) / ( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) * 100),
						(( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 - sum(pw.wonga * w.qty * if(w.ord_state = 30, 1, -1)) ) / ( sum(if(w.ord_state = '30', w.recv_amt, w.recv_amt * -1))/1.1 ) * -100)
					)
					 as profit_rate,
					g.goods_type, c.code_val as sale_stat_cl_val, c2.code_val as goods_type_nm,
					o.goods_no, g.brand, b.brand_nm, g.style_no, o.goods_opt, g.img, g.goods_nm, g.goods_nm_eng
				from order_opt o 
					inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no
					inner join product_code pc on pc.prd_cd = o.prd_cd
					inner join product_wonga pw on pc.prd_cd_p = pw.prd_cd_p
					left outer join store s on o.store_cd = s.store_cd
					left outer join brand b on g.brand = b.brand
					left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
					left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
				where w.`ord_state_date` >= '$sdate' and w.ord_state_date <= '$edate' and w.`ord_state` in ( '30','60','61')
					and o.ord_state = '30' 
					and o.store_cd <> '' 
					and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
					$where
				group by o.store_cd
			) as a on s.store_cd = a.store_cd
					inner join store_channel c on c.store_channel_cd = s.store_channel and c.store_kind_cd = s.store_channel_kind
				-- left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
			where 1=1 $where2
		";

		$result = DB::select($sql);

		$array = [];

		foreach($sale_kinds as $item) {
			$id = $item->code_id;
			$array["sale_kind_$id"] = 0;
		}

		$array['store_nm'] = '합계';

		collect($result)->map(function ($row) use (&$sale_kinds, &$array) {

			foreach($sale_kinds as $item) {
				$id = $item->code_id;

				if(array_key_exists("sale_kind_$id", $array)) {
					$array_row = (array) $row;
					$array["sale_kind_$id"] += (int)($array_row["sale_kind_$id"]);
				}
			}

		})->all();

		return response()->json([
			'code'	=> 200,
			'head'	=> array(
				'total'	=> count($result)
			),
			'body' => $result,
			"sum" => $array
		]);

	}
}
