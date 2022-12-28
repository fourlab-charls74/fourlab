<?php

namespace App\Http\Controllers\store\order;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/** 온라인 주문접수 */
class ord02Controller extends Controller
{
	public function index(Request $request) {

		$sdate = Carbon::now()->sub(4, 'week')->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");

		$sale_places_sql = "select com_id as id, com_nm as val from company where com_type = '4' and use_yn = 'Y' order by com_nm";
		$sale_places = DB::select($sale_places_sql);

		$values = [
            'sdate'         	=> $sdate,
			'edate'         	=> $edate,
            'ord_states'        => SLib::getordStates(), // 주문상태
			'sale_places'		=> $sale_places, // 판매처
            'stat_pay_types'    => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
			'items'			    => SLib::getItems(), // 품목
            'sale_kinds'        => SLib::getUsedSaleKinds(),
		];
        return view( Config::get('shop.store.view') . '/order/ord02', $values );
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', Carbon::now()->sub(1, 'week')->format("Ymd"));
		$edate = $request->input('edate', Carbon::now()->format("Ymd"));
		$sdate = str_replace('-', '', $sdate);
		$edate = str_replace('-', '', $edate);

		$store_type = $request->input('store_type', ''); // 매장구분
		$store_no = $request->input('store_no', []); // 매장명 리스트
        $brand_cd = $request->input('brand_cd', ''); // 브랜드
		$goods_nm = $request->input('goods_nm', ''); // 상품명
		$goods_nm_eng = $request->input('goods_nm_eng', ''); // 상품명(영문)
		$prd_cd	= $request->input('prd_cd', ''); // 상품코드
		$prd_cd_range_text = $request->input('prd_cd_range', ''); // 상품옵션범위
		$item = $request->input('item', ''); // 품목

		/** 검색조건 필터링 */
		$where = "";
		$where2 = "";
		
		$where .= " and w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate' ";
		if ($store_type != '') $where .= " and s.store_type = '$store_type' ";
		if (count($store_no) > 0) {
			$where .= " and (1<>1 ";
			foreach ($store_no as $store_cd) {
				$where .= " or o.store_cd = '$store_cd' ";
			}
			$where .= " ) ";
		}
		if ($brand_cd != '') $where2 .= " and b.brand = '$brand_cd' ";
		if ($goods_nm != '') $where2 .= " and g.goods_nm like '%$goods_nm%' ";
		if ($goods_nm_eng != '') $where2 .= " and g.goods_nm_eng like '%$goods_nm_eng%' ";
		if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or o.prd_cd like '$cd%' ";
			}
			$where .= " ) ";
		}
		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.`$opt $in_query` ($opt_join) ";
			}
		}
		if ($item != '') $where2 .= " and g.opt_kind_cd = '$item' ";

		$sql = "
			select *
			from (
				select
					o.ord_no, o.ord_opt_no, o.ord_state
					, o.prd_cd, o.qty, o.wonga, o.price, o.dc_amt, o.coupon_amt, o.recv_amt
					, g.goods_no, g.style_no, g.goods_nm, g.goods_nm_eng, o.goods_opt
					, concat(ifnull(m.user_nm, ''), '(', ifnull(m.user_id, ''), ')') as user_nm, m.r_nm
					, o.store_cd, o.pr_code, p.pay_stat
				from order_opt o
					inner join order_mst m on m.ord_no = o.ord_no
					inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
					left outer join payment p on p.ord_no = o.ord_no
			) a
				left outer join product_stock_store ps on ps.prd_cd = a.prd_cd and ps.store_cd in (select code_id as store_cd from code where code_kind_cd = 'ONLINE_ORDER_STORE')
				left outer join product_stock_storage pss on pss.prd_cd = a.prd_cd and pss.storage_cd in ('A0009', 'C0006')
		";
		$result = DB::select($sql);

		return response()->json([
			'code'	=> 200,
			'head'	=> [
				'total'	=> count($result)
			],
			'body' => $result
		]);
	}
}
