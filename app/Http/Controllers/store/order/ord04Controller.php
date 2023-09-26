<?php

namespace App\Http\Controllers\store\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/** 온라인교환환불재고처리 */
class ord04Controller extends Controller
{
	private $product_stock_type = [
		'sale_refund' => 2, // 주문 마이너스 (매장)
		'return' => 11, // 반품 (매장/창고)
	];

	public function index()
	{
		$user_group = Auth('head')->user()->logistics_group_yn;

		$sdate = Carbon::now()->sub(2, 'week')->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");

		$sale_places_sql = "select com_id as id, com_nm as val from company where com_type = '4' and use_yn = 'Y' order by com_nm";
		$sale_places = DB::select($sale_places_sql);

		$clm_state = [
			(object)['code_id' => 40, 'code_val' => '교환요청'],
			(object)['code_id' => 41, 'code_val' => '환불요청'],
		];

		$values = [
			'user_group' => $user_group, // 창고그룹사용자
			'sdate' => $sdate,
			'edate' => $edate,
			'clm_states' => $clm_state, // 클레임상태
			'sale_places' => $sale_places, // 판매처
			'stat_pay_types' => SLib::getCodes('G_PAY_TYPE'), // 결제방법
			'items' => SLib::getItems(), // 품목
			// 'ord_states'        => SLib::getordStates(), // 주문상태
			// 'clm_states'     	=> SLib::getCodes('G_CLM_STATE'), // 클레임상태
			// 'sale_kinds'        => SLib::getUsedSaleKinds(), // 판매유형
		];
		return view(Config::get('shop.store.view') . '/order/ord04', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));

		$ord_no = $request->input('ord_no', '');
		// $ord_state = $request->input('ord_state', ''); // 주문상태
		// $pay_stat = $request->input('pay_stat', ''); // 입금상태
		$clm_state = $request->input('clm_state', ''); // 클레임상태
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
		$stock_check_yn = $request->input('stock_check_yn', 'N');

		$ord_field = $request->input('ord_field', 'o.ord_date');
		$ord = $request->input('ord', 'desc');
		$page = $request->input('page', 1);
		$limit = $request->input('limit', 100);

		/** 검색조건 필터링 */
		$where = "";

		if ($ord_no != '') $where .= " and o.ord_no like '" . $ord_no . "%' ";
		// if ($ord_state != '') $where .= " and o.ord_state = '" . $ord_state . "' ";
		// if ($pay_stat != '') $where .= " and p.pay_stat = '" . $pay_stat . "' ";
		if ($clm_state != '') $where .= " and o.clm_state = '" . $clm_state . "' ";
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
			$sale_kind_join = join(',', array_map(function ($s) {
				return "'$s'";
			}, $sale_kind));
			$where .= " and o.sale_kind in ($sale_kind_join) ";
		}
		if ($com_id != '') $where .= " and g.com_id = '" . $com_id . "' ";

		// 상품코드 검색
		if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '$cd%' ";
			}
			$where .= ") ";
		}

		if ($style_no != '') $where .= " and g.style_no like '" . $style_no . "%' ";
		if ($item != '') $where .= " and g.opt_kind_cd = '" . $item . "' ";
		if ($brand_cd != '') $where .= " and g.brand = '" . $brand_cd . "' ";
		if ($goods_nm != '') $where .= " and g.goods_nm like '%" . $goods_nm . "%' ";
		if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%" . $goods_nm_eng . "%' ";

		if ($stock_check_yn === 'N') $where .= " and (csc.state is null or csc.state <> 30)";
		else if ($stock_check_yn === 'Y') $where .= " and csc.state = 30";

		// 상품번호 검색
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

		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function ($r) {
					return "'$r'";
				}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// order by
		$orderby = sprintf("order by %s %s, pc.prd_cd asc", $ord_field, $ord);

		// pagination
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$sql = "
			select a.*
				, p.pay_stat, p.pay_date
				, os.code_val as ord_state_nm, cs.code_val as clm_state_nm
				, ok.code_val as ord_kind_nm, pt.code_val as pay_type_nm
				, bk.code_val as baesong_kind
			    , sp.com_nm as sale_place_nm, s.store_nm
			from (
				select o.ord_no, o.ord_opt_no, o.goods_no, o.prd_cd, o.goods_opt
					, o.wonga, o.price, o.qty, o.pay_type, o.dlv_amt, o.sale_place, o.store_cd
					, o.ord_state, o.clm_state, o.com_id, o.baesong_kind as dlv_baesong_kind, o.ord_date
					, o.ord_type, o.ord_kind, o.dlv_end_date, c.last_up_date
					, concat(ifnull(om.user_nm, ''), '(', ifnull(om.user_id, ''), ')') as user_nm, om.r_nm
					, g.goods_nm, g.goods_nm_eng, g.style_no, g.price as goods_price, g.goods_sh
					, ifnull(pc.prd_cd_p, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt)) as prd_cd_p, pc.color
					, ifnull((
						select s.size_cd from size s
						where s.size_kind_cd = pc.size_kind
						   and s.size_cd = pc.size
						   and use_yn = 'Y'
					),'') as size
					, if(csc.state = 30, 'Y', 'N') as stock_check_yn
					, csc.comment
				from order_opt o
					inner join order_mst om on om.ord_no = o.ord_no
				    inner join claim c on c.ord_opt_no = o.ord_opt_no
					inner join goods g on g.goods_no = o.goods_no
					inner join product_code pc on pc.prd_cd = o.prd_cd
					left outer join claim_stock_check csc on csc.ord_opt_no = o.ord_opt_no
				where o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'
					and o.clm_state in (40,41)
					$where
				$orderby
				$limit
			) a
				left outer join payment p on p.ord_no = a.ord_no
				left outer join code os on os.code_kind_cd = 'G_ORD_STATE' and os.code_id = a.ord_state
				left outer join code cs on cs.code_kind_cd = 'G_CLM_STATE' and cs.code_id = a.clm_state
				left outer join code ok on ok.code_kind_cd = 'G_ORD_KIND' and ok.code_id = a.ord_kind
			    left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = a.pay_type
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = a.dlv_baesong_kind
				left outer join company sp on sp.com_type = '4' and sp.use_yn = 'Y' and sp.com_id = a.sale_place
				left outer join store s on s.store_cd = a.store_cd
		";
		$result = DB::select($sql);

		// pagination
		$total = 0;
		$page_cnt = 0;
		if ($page == 1) {
			$sql = "
				select count(*) as total
				from order_opt o
					inner join order_mst om on om.ord_no = o.ord_no
				    inner join claim c on c.ord_opt_no = o.ord_opt_no
					inner join goods g on g.goods_no = o.goods_no
					inner join product_code pc on pc.prd_cd = o.prd_cd
					left outer join claim_stock_check csc on csc.ord_opt_no = o.ord_opt_no
				where o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'
					and o.clm_state in (40,41)
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
			],
			"body" => $result,
		]);
	}

	/** 클레임 재고 검수완료 */
	public function completeStockCheck(Request $request)
	{
		$state = 30; // 검수완료
		$rows = $request->input('data', []);
		$user = [
			'id' => Auth('head')->user()->id,
			'name' => Auth('head')->user()->name
		];

		// 온라인클레임으로 반품되는 창고는 온라인창고로 설정된 창고입니다. (2023-09-07)
		// 추후 온라인창고가 아닌 대표창고로 반품할 경우, 아래 'online_yn'을 'default_yn'으로 변경하십시오.
		$return_storage_cd = DB::table('storage')->where('online_yn', 'Y')->value('storage_cd');

		try {
			DB::beginTransaction();

			foreach ($rows as $row) {
				if (!isset($row['store_cd'])) continue;

				$prd_cd = $row['prd_cd'] ?? '';
				$qty = $row['qty'] ?? 0;

				// 1) product_stock_storage -> 반품창고 보유재고/실재고 증감처리
				DB::table('product_stock_storage')
					->where('prd_cd', $prd_cd)
					->where('storage_cd', $return_storage_cd)
					->update([
						'qty' => DB::raw('qty + ' . $qty),
						'wqty' => DB::raw('wqty + ' . $qty),
						'ut' => now(),
					]);

				// 2) product_stock -> 전체재고/창고재고 증감처리
				DB::table('product_stock')
					->where('prd_cd', $prd_cd)
					->update([
						'out_qty' => DB::raw('out_qty - ' . $qty),
						'qty' => DB::raw('qty + ' . $qty),
						'wqty' => DB::raw('wqty + ' . $qty),
						'qty_wonga' => DB::raw('wonga * qty'),
						'ut' => now(),
					]);

				// 3) product_stock_hst -> 판매매장 증감/차감 & 반품창고 증감 로그
				$this->insertProductStockHst($row, 'STORE', $row['store_cd'], $this->product_stock_type['sale_refund'], $qty, $user);
				$this->insertProductStockHst($row, 'STORE', $row['store_cd'], $this->product_stock_type['return'], ($qty * -1), $user);
				$this->insertProductStockHst($row, 'STORAGE', $return_storage_cd, $this->product_stock_type['return'], $qty, $user);

				// 4) claim_stock_check -> 검수내역 로그
				DB::table('claim_stock_check')->insert([
					'ord_opt_no' => $row['ord_opt_no'],
					'prd_cd' => $prd_cd,
					'qty' => $qty,
					'state' => $state,
					'store_cd' => $row['store_cd'],
					'storage_cd' => $return_storage_cd,
					'comment' => $row['comment'] ?? '',
					'rt' => now(),
					'ut' => now(),
					'admin_id' => $user['id'],
				]);
			}

			DB::commit();
			$code = 200;
			$msg = "온라인교환환불건이 정상적으로 검수완료되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json(['code' => $code, 'msg' => $msg], $code);
	}

	/** product_stock_hst 매장/창고 재고업데이트 로그 */
	private function insertProductStockHst($data, $location_type = 'STORE', $location_cd, $stock_type, $qty, $user)
	{
		DB::table('product_stock_hst')
			->insert([
				'goods_no' => $data['goods_no'],
				'prd_cd' => $data['prd_cd'],
				'goods_opt' => $data['goods_opt'],
				'location_cd' => $location_cd,
				'location_type' => $location_type,
				'type' => $stock_type,
				'price' => $data['price'],
				'wonga' => $data['wonga'],
				'qty' => $qty,
				'stock_state_date' => date('Ymd'),
				'ord_opt_no' => $data['ord_opt_no'],
				'comment' => '온라인교환환불 재고처리',
				'rt' => now(),
				'admin_id' => $user['id'],
				'admin_nm' => $user['name'],
			]);
	}
}
