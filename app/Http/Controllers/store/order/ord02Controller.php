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

		$dlv_locations_sql = "
			(select 'storage' as location_type, storage_cd as location_cd, storage_nm as location_nm, if(online_yn = 'Y', 0, 1) as seq from storage where online_yn = 'Y' or default_yn = 'Y')
			union all
			(select 'store' as location_type, store_cd as location_cd, store_nm as location_nm, 2 as seq from store where store_cd in (select code_id from code where code_kind_cd = 'ONLINE_ORDER_STORE'))
			order by seq, location_cd
		";
		$dlv_locations = DB::select($dlv_locations_sql);

		$rel_orders = DB::table('code')->where('code_kind_cd', 'REL_ORDER')->where('code_id', 'like', 'O_%')->get();

		$values = [
            'sdate'         	=> $sdate,
			'edate'         	=> $edate,
            'ord_states'        => SLib::getordStates(), // 주문상태
			'sale_places'		=> $sale_places, // 판매처
            'stat_pay_types'    => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
			'items'			    => SLib::getItems(), // 품목
            'sale_kinds'        => SLib::getUsedSaleKinds(),
			'rel_orders'		=> $rel_orders, // 온라인출고차수
			'dlv_locations'		=> $dlv_locations, // 배송처
		];
        return view( Config::get('shop.store.view') . '/order/ord02', $values );
	}

	public function search(Request $request)
	{
		// $sdate = $request->input('sdate', Carbon::now()->sub(1, 'week')->format("Ymd"));
		// $edate = $request->input('edate', Carbon::now()->format("Ymd"));
		// $sdate = str_replace('-', '', $sdate);
		// $edate = str_replace('-', '', $edate);

		// $store_type = $request->input('store_type', ''); // 매장구분
		// $store_no = $request->input('store_no', []); // 매장명 리스트
        // $brand_cd = $request->input('brand_cd', ''); // 브랜드
		// $goods_nm = $request->input('goods_nm', ''); // 상품명
		// $goods_nm_eng = $request->input('goods_nm_eng', ''); // 상품명(영문)
		// $prd_cd	= $request->input('prd_cd', ''); // 상품코드
		// $prd_cd_range_text = $request->input('prd_cd_range', ''); // 상품옵션범위
		// $item = $request->input('item', ''); // 품목

		// /** 검색조건 필터링 */
		// $where = "";
		// $where2 = "";
		
		// $where .= " and w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate' ";
		// if ($store_type != '') $where .= " and s.store_type = '$store_type' ";
		// if (count($store_no) > 0) {
		// 	$where .= " and (1<>1 ";
		// 	foreach ($store_no as $store_cd) {
		// 		$where .= " or o.store_cd = '$store_cd' ";
		// 	}
		// 	$where .= " ) ";
		// }
		// if ($brand_cd != '') $where2 .= " and b.brand = '$brand_cd' ";
		// if ($goods_nm != '') $where2 .= " and g.goods_nm like '%$goods_nm%' ";
		// if ($goods_nm_eng != '') $where2 .= " and g.goods_nm_eng like '%$goods_nm_eng%' ";
		// if ($prd_cd != '') {
		// 	$prd_cd = explode(',', $prd_cd);
		// 	$where .= " and (1<>1 ";
		// 	foreach ($prd_cd as $cd) {
		// 		$where .= " or o.prd_cd like '$cd%' ";
		// 	}
		// 	$where .= " ) ";
		// }
		// // 상품옵션 범위검색
		// parse_str($prd_cd_range_text, $prd_cd_range);
		// $range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		// foreach ($range_opts as $opt) {
		// 	$rows = $prd_cd_range[$opt] ?? [];
		// 	if (count($rows) > 0) {
		// 		$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
		// 		$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
		// 		$where .= " and pc.`$opt $in_query` ($opt_join) ";
		// 	}
		// }
		// if ($item != '') $where2 .= " and g.opt_kind_cd = '$item' ";


		$dlv_locations_sql = "
			(select 'storage' as location_type, storage_cd as location_cd, storage_nm as location_nm, if(online_yn = 'Y', 0, 1) as seq from storage where online_yn = 'Y' or default_yn = 'Y')
			union all
			(select 'store' as location_type, store_cd as location_cd, store_nm as location_nm, 2 as seq from store where store_cd in (select code_id from code where code_kind_cd = 'ONLINE_ORDER_STORE'))
			order by seq, location_cd
		";
		$dlv_locations = DB::select($dlv_locations_sql);
		$qty_sql = "";
		foreach ($dlv_locations as $loc) {
			$qty_sql .= ", (select wqty from product_stock_$loc->location_type where " . $loc->location_type . "_cd = '$loc->location_cd' and prd_cd = pc.prd_cd) as "  . $loc->seq . "_" . $loc->location_type . "_" . $loc->location_cd . "_qty ";
		}

		$sql = "
			select a.*
				, if(a.goods_no_group < 2, null, a.goods_no) as goods_no_group
				, os.code_val as ord_state_nm
				, round((1 - ((a.price * a.qty) * (1 - if(st.amt_kind = 'per', st.sale_per, 0) / 100)) / a.goods_sh) * 100) as dc_rate
				, sk.code_val as sale_kind_nm, pr.code_val as pr_code_nm
				, ot.code_val as ord_type_nm, ok.code_val as ord_kind_nm
				, bk.code_val as baesong_kind, com.com_nm as sale_place_nm
				, pt.code_val as pay_type_nm, ps.code_val as pay_stat_nm
			from (
				select 
					o.ord_no, o.ord_opt_no, o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, o.goods_opt
					, pc.prd_cd, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
					, o.wonga, o.price, g.price as goods_price, g.goods_sh, o.qty
					, o.pay_type, o.dlv_amt, o.point_amt, o.coupon_amt, o.dc_amt, o.recv_amt
					, o.sale_place, o.store_cd, o.ord_state, o.clm_state, o.com_id, o.baesong_kind as dlv_baesong_kind, o.ord_date
					, o.sale_kind, o.pr_code, o.sales_com_fee, o.ord_type, o.ord_kind, p.pay_stat
					, concat(ifnull(m.user_nm, ''), '(', ifnull(m.user_id, ''), ')') as user_nm, m.r_nm
					, (
						select count(*)
                        from product_code inpc
							inner join code inc on inc.code_kind_cd = 'PRD_CD_COLOR' and inc.code_id = color
							inner join code incs on incs.code_kind_cd = 'PRD_CD_SIZE_MEN' and incs.code_id = size
                        where inpc.goods_no = o.goods_no
							and inc.code_val = SUBSTRING_INDEX(o.goods_opt, '^', 1) and replace(incs.code_val, ' ', '') = replace(substring_index(o.goods_opt, '^', -1), ' ', '')
					) as goods_no_group
					$qty_sql
				from order_opt o
					inner join order_mst m on m.ord_no = o.ord_no
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
					left outer join (
						select prd_cd, goods_no, brand, year, season, gender, item, seq, opt, color, size, c.code_val as color_nm, cs.code_val as size_nm
						from product_code
							inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = color
							inner join code cs on cs.code_kind_cd = 'PRD_CD_SIZE_MEN' and cs.code_id = size
					) pc on pc.goods_no = o.goods_no and pc.color_nm = SUBSTRING_INDEX(o.goods_opt, '^', 1) and replace(pc.size_nm, ' ', '') = replace(substring_index(o.goods_opt, '^', -1), ' ', '')
				where (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					and o.ord_date >= '2022-01-01 00:00:00' and o.ord_date <= now()
					-- and o.ord_no like '202201%'
					and o.ord_state = 10
					-- 입금상태
					-- and o.sale_place = ''
					-- 주문정보
					-- 결제방법
					-- and o.sale_kind = ''
					-- 상품 공급업체
					-- 상품코드 / 스타일넘버 / 상품번호 / 품목 / 브랜드 / 상품옵션 / 상품명 / 상품명영문
					-- and (o.goods_no = '130658' or o.goods_no = '130976')
				order by o.ord_date desc, pc.prd_cd asc
				limit 0, 100
			) a
				left outer join code sk on sk.code_kind_cd = 'SALE_KIND' and sk.code_id = a.sale_kind
				left outer join code pr on pr.code_kind_cd = 'PR_CODE' and pr.code_id = a.pr_code
				left outer join code os on os.code_kind_cd = 'G_ORD_STATE' and os.code_id = a.ord_state
				left outer join code ot on ot.code_kind_cd = 'G_ORD_TYPE' and ot.code_id = a.ord_type
				left outer join code ok on ok.code_kind_cd = 'G_ORD_KIND' and ok.code_id = a.ord_kind
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = a.dlv_baesong_kind
				left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = a.pay_type
				left outer join code ps on ps.code_kind_cd = 'G_PAY_STAT' and ps.code_id = a.pay_stat
				left outer join sale_type st on st.sale_kind = a.sale_kind and st.use_yn = 'Y'
				left outer join company com on com.com_type = '4' and com.use_yn = 'Y' and com.com_id = a.sale_place
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
