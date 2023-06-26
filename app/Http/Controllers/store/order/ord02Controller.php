<?php

namespace App\Http\Controllers\store\order;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // (창고)출고
const PRODUCT_STOCK_TYPE_STORE_RT = 15; // (매장)RT출고

/** 온라인 주문접수 */
class ord02Controller extends Controller
{
	public function index(Request $request) {

		$sdate = Carbon::now()->sub(3, 'day')->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");

		$sale_places_sql = "select com_id as id, com_nm as val from company where com_type = '4' and use_yn = 'Y' order by com_nm";
		$sale_places = DB::select($sale_places_sql);

		$dlv_locations_sql = "
			(
				select 'storage' as location_type, storage_cd as location_cd, storage_nm as location_nm, if(online_yn = 'Y', 0, 1) as seq, 0 as location_seq
				from storage
				where online_yn = 'Y' or default_yn = 'Y'
			)
			union all
			(
				select 'store' as location_type, s.store_cd as location_cd, c.code_val as location_nm, 2 as seq, c.code_seq as location_seq
				from store s
					inner join code c on c.code_kind_cd = 'ONLINE_ORDER_STORE' and c.code_id = s.store_cd
			)
			order by seq, location_seq
		";
		$dlv_locations = DB::select($dlv_locations_sql);

		$rel_orders = $this->_get_rel_orders();

		$values = [
            'sdate'         	=> $sdate,
			'edate'         	=> $edate,
            'ord_states'        => SLib::getordStates(), // 주문상태
			'sale_places'		=> $sale_places, // 판매처
            'stat_pay_types'    => SLib::getCodes('G_PAY_TYPE'), // 결제방법
			'items'			    => SLib::getItems(), // 품목
            'sale_kinds'        => SLib::getUsedSaleKinds(),
			'rel_orders'		=> $rel_orders, // 온라인출고차수
			'ord_kinds'			=> SLib::getCodes('G_ORD_KIND'), // 출고구분
			'dlv_locations'		=> $dlv_locations, // 배송처
		];
        return view( Config::get('shop.store.view') . '/order/ord02', $values );
	}

	// 출고 가능한 출고차수 리스트 조회
	private function _get_rel_orders()
	{
		$rel_orders = DB::table('code')->where('code_kind_cd', 'REL_ORDER')->where('code_id', 'like', 'O_%')->select('code_val')->get()->toArray();
		$rel_orders = array_map(function($r) { return $r->code_val; }, $rel_orders);

		$today = date('ymd');
		$sql = "
			select substring_index(rel_order, '-', -1) as code_val
			from order_receipt
			where instr(rel_order, '$today') > 0
		";
		$used_orders = DB::select($sql);
		$used_orders = array_map(function($r) { return $r->code_val; }, $used_orders);

		return array_diff($rel_orders, $used_orders);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));
		$ord_no = $request->input('ord_no', '');
		$ord_state = $request->input('ord_state', ''); // 주문상태
		$pay_stat = $request->input('pay_stat', ''); // 입금상태
		$sale_place = $request->input('sale_place', ''); // 판매처
		$ord_info_key = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$stat_pay_type = $request->input('stat_pay_type', ''); // 결제방법
		$not_complex = $request->input('not_complex', 'N'); // 복합결제 제외여부
		$sale_kind = $request->input('sale_kind', []); // 판매유형
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

		$where .= " and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59' ";
		if ($ord_no != '') $where .= " and o.ord_no like '" . $ord_no . "%' ";
		if ($ord_state != '') $where .= " and o.ord_state = '" . $ord_state . "' ";
		if ($pay_stat != '') $where .= " and p.pay_stat = '" . $pay_stat . "' ";
		if ($sale_place != '') $where .= " and o.sale_place = '" . $sale_place . "' ";

		// 주문정보검색
		if ($ord_info_value != '') {
			if (in_array($ord_info_key, ['om.mobile', 'om.phone', 'om.r_mobile', 'om.r_phone'])) {
				$val = $this->__replaceTel($ord_info_value);
				if (in_array($ord_info_key, ['om.mobile', 'om.phone', 'om.r_mobile'])) {
					$where .= " and $ord_info_key = '$val' ";
				} else {
					$where .= " and $ord_info_key like '$val%' ";
				}
			} else {
				if (in_array($ord_info_key, ['om.user_nm', 'om.user_id', 'om.r_nm'])) {
					$where .= " and $ord_info_key = '$ord_info_value' ";
				} else {
					$where .= " and $ord_info_key like '$ord_info_value%' ";
				}
			}
		}

		// 결제방법
		if ($stat_pay_type != '') {
			if ($not_complex == 'Y') {
				$where .= " and o.pay_type = '$stat_pay_type' ";
			} else {
				$where .= " and ((o.pay_type & $stat_pay_type) = $stat_pay_type) ";
			}
		}

		if (count($sale_kind) > 0) {
			$sale_kind_join = join(',', array_map(function($s) { return "'$s'"; }, $sale_kind));
			$where .= " and o.sale_kind in ($sale_kind_join) ";
		}
		if ($com_id != '') $where .= " and g.com_id = '" . $com_id . "' ";

		// 상품코드 검색
		if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			$prd_where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '$cd%' ";
				$prd_where .= " or inpc.prd_cd like '$cd%' ";
			}
			$where .= ") ";
			$prd_where .= ") ";
		}

		if ($style_no != '') $where .= " and g.style_no like '" . $style_no . "%' ";
		if ($item != '') $where .= " and g.opt_kind_cd = '" . $item . "' ";
		if ($brand_cd != '') $where .= " and g.brand = '" . $brand_cd . "' ";
		if ($goods_nm != '') $where .= " and g.goods_nm like '%" . $goods_nm . "%' ";
		if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%" . $goods_nm_eng . "%' ";

		// 상품번호 검색
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
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
				$prd_where .= " and inpc.$opt $in_query ($opt_join) ";
			}
		}

		// order by
        $orderby = sprintf("order by %s %s, pc.prd_cd asc", $ord_field, $ord);

		// pagination
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		// get list
		$dlv_locations_sql = "
            (
				select 'storage' as location_type, storage_cd as location_cd, storage_nm as location_nm, if(online_yn = 'Y', 0, 1) as seq, 0 as location_seq
				from storage
				where online_yn = 'Y' or default_yn = 'Y'
			)
			union all
			(
				select 'store' as location_type, s.store_cd as location_cd, c.code_val as location_nm, 2 as seq, c.code_seq as location_seq
				from store s
					inner join code c on c.code_kind_cd = 'ONLINE_ORDER_STORE' and c.code_id = s.store_cd
			)
			order by seq, location_seq
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
				, null as goods_no_group
				, if(a.goods_no_group < 2, null, a.ord_opt_no) as ord_opt_no_group
			from (
				select
					o.ord_no, o.ord_opt_no, o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, pc.goods_opt
					, pc.prd_cd, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
					, o.wonga, o.price, g.price as goods_price, g.goods_sh, o.qty
					, o.pay_type, o.dlv_amt, o.point_amt, o.coupon_amt, o.dc_amt, o.recv_amt
					, o.sale_place, o.store_cd, o.ord_state, o.clm_state, o.com_id, o.baesong_kind as dlv_baesong_kind, o.ord_date
					, o.sale_kind, o.pr_code, o.sales_com_fee, o.ord_type, o.ord_kind, p.pay_stat, p.pay_date
					, concat(ifnull(om.user_nm, ''), '(', ifnull(om.user_id, ''), ')') as user_nm, om.r_nm
					, (
						select count(*)
                        from product_code inpc
							inner join code inc on inc.code_kind_cd = 'PRD_CD_COLOR' and inc.code_id = inpc.color
							inner join code incs on if(inpc.gender = 'M', incs.code_kind_cd = 'PRD_CD_SIZE_MEN', if(inpc.gender = 'W', incs.code_kind_cd = 'PRD_CD_SIZE_WOMEN', if(inpc.gender = 'U', incs.code_kind_cd = 'PRD_CD_SIZE_UNISEX', incs.code_kind_cd = 'PRD_CD_SIZE_MATCH' ))) and incs.code_id = inpc.size
                        where inpc.goods_no = o.goods_no
							and inc.code_val = substring_index(o.goods_opt, '^', 1)
							and replace(incs.code_val, ' ', '') = replace(substring_index(o.goods_opt, '^', -1), ' ', '')
							$prd_where
					) as goods_no_group
					$qty_sql
				from order_opt o
					inner join order_mst om on om.ord_no = o.ord_no
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
					left outer join (
						select prd_cd, goods_no, goods_opt, brand, year, season, gender, item, seq, opt, color, size, c.code_val as color_nm, cs.code_val as size_nm
						from product_code
							inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = color
							inner join code cs on if(gender = 'M', cs.code_kind_cd = 'PRD_CD_SIZE_MEN', if(gender = 'W', cs.code_kind_cd = 'PRD_CD_SIZE_WOMEN', if(gender = 'U', cs.code_kind_cd = 'PRD_CD_SIZE_UNISEX', cs.code_kind_cd = 'PRD_CD_SIZE_MATCH' ))) and cs.code_id = size
					) pc on pc.goods_no = o.goods_no and pc.color_nm = substring_index(o.goods_opt, '^', 1) and replace(pc.size_nm, ' ', '') = replace(substring_index(o.goods_opt, '^', -1), ' ', '')
				where (o.store_cd is null or o.store_cd = 'HEAD_OFFICE')
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

		$result = array_reduce($result, function($a, $c) use ($qty_sql) {
			if (!isset($c->prd_cd)) {
				$sql = "
						select
							pc.prd_cd, if(pc.prd_cd_p = '', concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt), pc.prd_cd_p) as prd_cd_p
							, pc.color, pc.size, o.ord_opt_no as ord_opt_no_group, pc.match_yn as prd_match
							$qty_sql
						from order_opt o
							inner join order_mst om on om.ord_no = o.ord_no
							left outer join (
							    select pc.prd_cd, pc.prd_cd_p, pc.goods_no, pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt, pc.color, pc.size, p.match_yn
							    from product_code pc
							    	inner join product p on p.prd_cd = pc.prd_cd
							) pc on pc.goods_no = o.goods_no
						where o.ord_opt_no = " . $c->ord_opt_no . "
							and o.goods_no = " . $c->goods_no
				;
				$rows = DB::select($sql);
				$rows = array_map(function($row) use ($c) { return array_merge((array) $c, (array) $row); }, $rows);
				return array_merge($a, $rows);
			}
			return array_merge($a, [$c]);
		}, []);

		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
				select count(*) as total
				from (
					select o.ord_opt_no
					from order_opt o
						inner join order_mst om on om.ord_no = o.ord_no
						inner join goods g on g.goods_no = o.goods_no
						left outer join payment p on p.ord_no = o.ord_no
						left outer join (
							select prd_cd, goods_no, brand, year, season, gender, item, seq, opt, color, size, c.code_val as color_nm, cs.code_val as size_nm
							from product_code
								inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = color
								inner join code cs on if(gender = 'M', cs.code_kind_cd = 'PRD_CD_SIZE_MEN', if(gender = 'W', cs.code_kind_cd = 'PRD_CD_SIZE_WOMEN', if(gender = 'U', cs.code_kind_cd = 'PRD_CD_SIZE_UNISEX', cs.code_kind_cd = 'PRD_CD_SIZE_MATCH' ))) and cs.code_id = size
						) pc on pc.goods_no = o.goods_no and pc.color_nm = substring_index(o.goods_opt, '^', 1) and replace(pc.size_nm, ' ', '') = replace(substring_index(o.goods_opt, '^', -1), ' ', '')
					where (o.store_cd is null or o.store_cd = 'HEAD_OFFICE')
						and o.clm_state in (-30,1,90,0)
						$where
					group by o.ord_opt_no
				) a
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

	// 온라인주문접수
	public function receipt(Request $request)
	{
		$user = [
			'id'	=> Auth('head')->user()->id,
			'name'	=> Auth('head')->user()->name
		];
		$ord_state = 20; // 출고처리중
		$onoff_type = 'on'; // 온라인/오프라인 타입 (온라인주문접수 메뉴에서는 온라인으로 고정)
		$rel_order = $request->input('rel_order', '');
		$rows = $request->input('data', []);
		$failed_rows = [];

		try {
            DB::beginTransaction();

			$order = new Order($user);

			// 신규형식 출고차수 설정
			$rel_order = date('ymd') . "-" . $rel_order; // 신규형식 (000000-온라인01)

			// 구형식 출고차수 설정
			$dlv_series_no = date('YmdH'); // 구형식 (0000000000)
			$sql = "
				select
					dlv_series_no
				from order_dlv_series
				where dlv_day >= date_format(date_sub(now(),interval 1 day),'%Y%m%d')
					and dlv_series_nm = '$dlv_series_no'
				order by dlv_series_no desc limit 0,1
			";
			$series_row	= DB::selectOne($sql);
			if ($series_row) {
				$dlv_series_no = $series_row->dlv_series_no;
			} else {
				$dlv_series_no = DB::table('order_dlv_series')->insertGetId([
					'dlv_series_nm'	=> $dlv_series_no,
					'dlv_day'		=> date('Ymd'),
					'regi_date'		=> now()
				]);
			}

			// 온라인주문접수 등록
			$or_cd = DB::table('order_receipt')->insertGetId([
				'type' => $onoff_type,
				'rel_order' => $rel_order,
				'req_rt' => now(),
				'req_id' => $user['id'],
			]);

			foreach ($rows as $row) {
				$o_store_cd = '';
				$store_info = DB::table('store')->select('store_cd', 'store_nm')->where('sale_place_match_yn', 'Y')->where('com_id', $row['sale_place'] ?? '')->first();
				if ($store_info == null || !isset($row['ord_no']) || !isset($row['ord_opt_no'])) {
					array_push($failed_rows, $row['ord_no']);
					continue;
				}
				$order->SetOrdOptNo($row['ord_opt_no'], $row['ord_no']);

				// 재고검사
				$stock_check = false;
				if ($row['dlv_place_type'] === 'storage') {
					$sql = DB::table('product_stock_storage')->where('storage_cd', $row['dlv_place_cd'])->where('prd_cd', $row['prd_cd']);
					if ($sql->count() > 0) $stock_check = $sql->value('wqty') >= $row['qty'];
				}
				if ($row['dlv_place_type'] === 'store') {
					$sql = DB::table('product_stock_store')->where('store_cd', $row['dlv_place_cd'])->where('prd_cd', $row['prd_cd']);
					if ($sql->count() > 0) $stock_check = $sql->value('wqty') >= $row['qty'];
				}

				if ($stock_check) {
					// 출고처리중 처리
					$state_log = [
						'ord_no' => $row['ord_no'],
						'ord_opt_no' => $row['ord_opt_no'],
						'ord_state' => $ord_state,
						'comment' => "배송출고요청(온라인주문접수)",
						'admin_id' => $user['id'],
						'admin_nm' => $user['name']
					];
					$order->AddStateLog($state_log);
					$order->DlvProc($dlv_series_no, $ord_state, $row['prd_cd'] ?? '');

					// 온라인주문접수 상품리스트 등록
					DB::table('order_receipt_product')->insert([
						'or_cd' => $or_cd,
						'ord_opt_no' => $row['ord_opt_no'],
						'prd_cd' => $row['prd_cd'],
						'qty' => $row['qty'],
						'state' => $ord_state,
						'dlv_location_type' => strtoupper($row['dlv_place_type'] ?? ''),
						'dlv_location_cd' => $row['dlv_place_cd'],
						'comment' => $row['comment'] ?? '',
						'rt' => now(),
					]);

					// 재고처리
					$this->update_stock($user, $row, $ord_state);
				} else {
					array_push($failed_rows, $row['ord_no']);
					continue;
				}
			}

			DB::commit();
			$code = 200;
			$msg = "온라인주문이 정상적으로 접수되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}
		$rel_orders = $this->_get_rel_orders();
		return response()->json(['code' => $code, 'msg' => $msg, 'failed_rows' => $failed_rows, 'rel_orders' => $rel_orders], $code);
	}

	// 주문접수 시 보유재고 차감처리
	private function update_stock($user, $row, $ord_state)
	{
		$prd_cd = $row['prd_cd'] ?? '';
		$goods_no = $row['goods_no'] ?? '';
		$goods_opt = $row['goods_opt'] ?? '';
		$ord_price = $row['price'] ?? '';
		$ord_opt_no = $row['ord_opt_no'] ?? '';
		$ord_qty = $row['qty'] ?? 0;
		$wonga = $row['wonga'] ?? 0;
		$location_type = strtoupper($row['dlv_place_type'] ?? '');
		$location_cd = $row['dlv_place_cd'] ?? '';

		// 보유재고 차감
		if ($location_type === 'STORE') {
			DB::table('product_stock_store')
				->where('prd_cd', '=', $prd_cd)
				->where('store_cd', '=', $location_cd)
				->update([
					'wqty' => DB::raw('wqty - ' . $ord_qty),
					'ut' => now(),
				]);
			DB::table('product_stock')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'qty_wonga'	=> DB::raw('qty_wonga - ' . ($ord_qty * $wonga)),
					'out_qty' => DB::raw('out_qty + ' . $ord_qty),
					'qty' => DB::raw('qty - ' . $ord_qty),
					'ut' => now(),
				]);
		} else if ($location_type === 'STORAGE') {
			DB::table('product_stock_storage')
				->where('prd_cd', '=', $prd_cd)
				->where('storage_cd', '=', $location_cd)
				->update([
					'wqty' => DB::raw('wqty - ' . $ord_qty),
					'ut' => now(),
				]);
			DB::table('product_stock')
				->where('prd_cd', '=', $prd_cd)
				->update([
					'qty_wonga'	=> DB::raw('qty_wonga - ' . ($ord_qty * $wonga)),
					'out_qty' => DB::raw('out_qty + ' . $ord_qty),
					'qty' => DB::raw('qty - ' . $ord_qty),
					'wqty' => DB::raw('wqty - ' . $ord_qty),
					'ut' => now(),
				]);
		}

		// 재고이력 등록
		// type : 창고 -> (온라인매장으로)출고 / 매장 -> (온라인매장으로)RT출고
		// 재고를 받는 온라인매장의 재고이력처리는 ord03controller에서 진행
		DB::table('product_stock_hst')
			->insert([
				'goods_no' => $goods_no,
				'prd_cd' => $prd_cd,
				'goods_opt' => $goods_opt,
				'location_cd' => $location_cd,
				'location_type' => $location_type,
				'type' => ($location_type === 'STORE' ? PRODUCT_STOCK_TYPE_STORE_RT : PRODUCT_STOCK_TYPE_STORAGE_OUT), // 재고분류 : 매장RT출고(15) / 창고출고(17)
				'price' => $ord_price,
				'wonga' => $wonga,
				'qty' => $ord_qty * -1,
				'stock_state_date' => date('Ymd'),
				'ord_opt_no' => $ord_opt_no,
				'comment' => ($location_type === 'STORE' ? '매장RT' : '창고') . '출고(온라인배송)',
				'rt' => now(),
				'admin_id' => $user['id'],
				'admin_nm' => $user['name'],
			]);
	}

	// 출고구분변경
	public function update_ord_kind(Request $request)
	{
		$ord_kind = $request->input('ord_kind', '');
		$ord_opt_nos = $request->input('data', []);
		$ord_opt_nos = implode(', ', $ord_opt_nos);

		try {
            DB::beginTransaction();

			$sql = "
				update order_opt
					set ord_kind = '$ord_kind'
				where ord_opt_no in ($ord_opt_nos)
			";
			DB::update($sql);

			$sql = "
				update order_opt_wonga
					set ord_kind = '$ord_kind'
				where ord_opt_no in ($ord_opt_nos)
			";
			DB::update($sql);

			DB::commit();
			$code = 200;
			$msg = "출고구분변경이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(['code' => $code, 'msg' => $msg], $code);
	}

	/**
     * 전화번호 숫자에 '-' 넣어서 반환
     * - Parameters: $tel(전화번호)
     * - Returns: String
    */
    private function __replaceTel($tel)
    {
        $tel = trim($tel);
        if (strpos($tel, '-') === false) {
            $len = strlen($tel);
            if ($len == 9) {
                $patterns = array("/(\d{2})(\d{3})(\d{4})/");
                $replace = array("\\1-\\2-\\3");
                $tel = preg_replace($patterns, $replace, $tel);
            } else if ($len == 10) {
                if (substr($tel, 0, 2) == "02") {
                    $patterns = array("/(\d{2})(\d{4})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                } else {
                    $patterns = array("/(\d{3})(\d{3})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                }
            } else if ($len == 11) {
                if (substr($tel, 0, 4) == "0505") {
                    $patterns = array("/(\d{4})(\d{3})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                } else {
                    $patterns = array("/(\d{3})(\d{4})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                }
            } else if ($len == 12) {
                $patterns = array("/(\d{4})(\d{4})(\d{4})/");
                $replace = array("\\1-\\2-\\3");
                $tel = preg_replace($patterns, $replace, $tel);
            }
            return $tel;
        } else {
            return $tel;
        }
    }
}
