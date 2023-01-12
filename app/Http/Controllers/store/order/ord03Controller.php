<?php

namespace App\Http\Controllers\store\order;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/** 온라인 배송처리 */
class ord03Controller extends Controller
{
	public function index(Request $request) {

		$sdate = Carbon::now()->sub(3, 'day')->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");

		$sale_places_sql = "select com_id as id, com_nm as val from company where com_type = '4' and use_yn = 'Y' order by com_nm";
		$sale_places = DB::select($sale_places_sql);

		$dlv_storages_sql = "select storage_cd, storage_nm, if(online_yn = 'Y', 0, 1) as seq from storage where online_yn = 'Y' or default_yn = 'Y' order by seq";
		$dlv_storages = DB::select($dlv_storages_sql);

		$values = [
            'sdate'         	=> $sdate,
			'edate'         	=> $edate,
			'dlv_storages'		=> $dlv_storages, // 창고목록
            'ord_states'        => SLib::getordStates(), // 주문상태
			'dlv_types'			=> SLib::getCodes('G_DLV_TYPE'), // 배송방식
			'sale_places'		=> $sale_places, // 판매처
            'stat_pay_types'    => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
			'items'			    => SLib::getItems(), // 품목
            'sale_kinds'        => SLib::getUsedSaleKinds(), // 판매유형
		];
        return view( Config::get('shop.store.view') . '/order/ord03', $values );
	}

	public function search(Request $request)
	{
		$receipt_sdate = $request->input('receipt_sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$receipt_edate = $request->input('receipt_edate', Carbon::now()->format("Y-m-d"));
		$rel_order = $request->input('rel_order', '');
		$dlv_place_type = $request->input('dlv_place_type', 'storage');
		$storage_cd = $request->input('storage_cd', '');
		$store_cd = $request->input('store_no', '');
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));
		$ord_no = $request->input('ord_no', '');
		$ord_state = $request->input('ord_state', ''); // 주문상태
		$dlv_type = $request->input('dlv_type', ''); // 배송방식
		$sale_place = $request->input('sale_place', ''); // 판매처
		$ord_info_key = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$sale_kind = $request->input('sale_kind', ''); // 판매유형
		$stat_pay_type = $request->input('stat_pay_type', ''); // 결제방법
		$not_complex = $request->input('not_complex', 'N'); // 복합결제 제외여부
		$com_id = $request->input('com_cd', '');
		$prd_cd = $request->input('prd_cd', '');
		$style_no = $request->input('style_no', '');
		$goods_no = $request->input('goods_no', '');
		$item = $request->input('item', '');
		$brand_cd = $request->input('brand_cd', '');
		$prd_cd_range_text = $request->input('prd_cd_range', '');
		$goods_nm = $request->input('goods_nm', '');
		$goods_nm_eng = $request->input('goods_nm_eng', '');

		$ord_field = $request->input('ord_field', 'o.ord_date');
		$ord = $request->input('ord', 'desc');
		$page = $request->input('page', 1);
		$limit = $request->input('limit', 100);

		/** 검색조건 필터링 */
		$where = "";
		$prd_where = "";

		// order by
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		// pagination
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		// get list
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
				, os.code_val as ord_state_nm
				, round((1 - (a.price * (1 - if(st.amt_kind = 'per', st.sale_per, 0) / 100)) / a.goods_sh) * 100) as dc_rate
				, sk.code_val as sale_kind_nm, pr.code_val as pr_code_nm
				, ot.code_val as ord_type_nm, ok.code_val as ord_kind_nm
				, bk.code_val as baesong_kind, com.com_nm as sale_place_nm
				, pt.code_val as pay_type_nm, ps.code_val as pay_stat_nm
			from (
				select 
					rcp.or_cd, rcp.or_prd_cd, rc.rel_order, rc.req_id
					, rcp.state, rcp.dlv_location_type, rcp.dlv_location_cd, rcp.rt as receipt_date
					, if(rcp.dlv_location_type = 'STORAGE', (select storage_nm from storage where storage_cd = rcp.dlv_location_cd), (select store_cd from store where store_cd = rcp.dlv_location_cd)) as dlv_location_nm
					, o.ord_no, o.ord_opt_no, o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, o.goods_opt
					, pc.prd_cd, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
					, o.wonga, o.price, g.price as goods_price, g.goods_sh, o.qty
					, o.pay_type, o.dlv_amt, o.point_amt, o.coupon_amt, o.dc_amt, o.recv_amt
					, o.sale_place, o.store_cd, o.ord_state, o.clm_state, o.com_id, o.baesong_kind as dlv_baesong_kind, o.ord_date
					, o.sale_kind, o.pr_code, o.sales_com_fee, o.ord_type, o.ord_kind, p.pay_stat
					, concat(ifnull(om.user_nm, ''), '(', ifnull(om.user_id, ''), ')') as user_nm, om.r_nm
					$qty_sql
				from order_receipt_product rcp
					inner join order_receipt rc on rc.or_cd = rcp.or_cd
					inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
					inner join order_mst om on om.ord_no = o.ord_no
					inner join product_code pc on pc.prd_cd = rcp.prd_cd
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
				where rcp.state < 30
					and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					$where
				$orderby
				$limit
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

		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
				select count(*) as total
				from order_receipt_product rcp
					inner join order_receipt rc on rc.or_cd = rcp.or_cd
					inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
					inner join order_mst om on om.ord_no = o.ord_no
					inner join product_code pc on pc.prd_cd = rcp.prd_cd
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
				where rcp.state < 30
					and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					$where
			";
			
			$row = DB::selectOne($sql);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		return response()->json([
            "code" => 200,
            "head" => [
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result),
				"dlv_locations" => $dlv_locations
            ],
            "body" => $result,
        ]);
	}
}
