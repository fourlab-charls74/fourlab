<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // 출고

class stk13Controller extends Controller
{
	public function index()
	{
		$sql = "
			select
				*
			from code
			where code_kind_cd = 'rel_order' and code_id like 'S_%'
		";
		$rel_order_res = DB::select($sql);

		$storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderBy('default_yn')->get();

		$values = [
			'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
			'edate'         => date("Y-m-d"),
			'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
			'style_no'		=> "", // 스타일넘버
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
			'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
			'items'			=> SLib::getItems(), // 품목
			// 'rel_orders'    => SLib::getCodes("REL_ORDER"), // 출고차수
			'storages'      => $storages, // 창고리스트
			'rel_order_res'	=> $rel_order_res, //판매분 출고차수
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view(Config::get('shop.store.view') . '/stock/stk13', $values);
	}

	public function search(Request $request)
	{
		$r = $request->all();

		$code = 200;
		$where = "";
		$store_where = "";
		$orderby = "";
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$store_channel = $request->input("store_channel",[]);
		$store_channel_kind = $request->input("store_channel_kind",[]);

		$sdate = $r['sdate'] ?? '';
		$edate = $r['edate'] ?? '';

		$store_cds = $r['store_no'] ?? [];

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				//  $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// store_where
		foreach($store_cds as $key => $cd) {
			if ($key === 0) {
				$store_where .= "o.store_cd = '$cd'";
			} else {
				$store_where .= " or o.store_cd = '$cd'";
			}
		}
		if (count($store_cds) < 1) {
			$store_where = "1=1";
		}

		// where
		// if($r['store_type'] != null)
		// 	$where .= " and store.store_type = '" . $r['store_type'] . "'";
		if($r['prd_cd'] != null) {
			$prd_cd = explode(',', $r['prd_cd']);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or o.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		} else {
			$where .= " and o.prd_cd != ''";
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
		if(($r['ext_storage_qty'] ?? 'false') == 'true')
			$where .= " and (pss.wqty != '' and pss.wqty != '0')";

//		if ($r['store_channel'] != '') $where .= "and store.store_channel ='" . Lib::quote($r['store_channel']). "'";
//		if ($r['store_channel_kind'] ?? '' != '') $where .= "and store.store_channel_kind ='" . Lib::quote($r['store_channel_kind']). "'";

		if (count($store_channel) > 0) {
			$store_channel = join("','", $store_channel);
			$where .= " and store.store_channel in ('$store_channel')";
		}

		if (count($store_channel_kind) > 0) {
			$store_channel_kind = join("','", $store_channel_kind);
			$where .= " and store.store_channel_kind in ('$store_channel_kind')";
		}

		// orderby
		$ord = $r['ord'] ?? 'desc';
		$ord_field = $r['ord_field'] ?? "store_cd";
		$ord_field = 'o.' . $ord_field;
		$orderby = sprintf("%s %s", $ord_field, $ord);

		// pagination
		$page = $r['page'] ?? 1;
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $r['limit'] ?? 1000;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";
		$total = 0;
		$page_cnt = 0;
		$total_data = 0;

		if ($page == 1) {
			$sql = "
			select
				count(a.total) as total
				, sum(a.storage_qty) as storage_qty
				, sum(a.storage_wqty) as storage_wqty
				, sum(a.store_qty) as store_qty
				, sum(a.store_wqty) as store_wqty
				, sum(a.sale_cnt) as sale_cnt
				, sum(a.total_sale_cnt) as total_sale_cnt
				, sum(a.rel_qty) as rel_qty
				, sum(a.rel_qty2) as rel_qty2
			from (
				select 
					o.store_cd,
					count(pc.prd_cd) as total,
					(select store_nm from store where store_cd = o.store_cd) as store_nm,
					o.prd_cd,
					concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_sm,
					pc.color,
					pc.size,
					o.goods_no,
					op.opt_kind_nm,
					b.brand_nm, 
					g.style_no,
					g.goods_nm,
					g.goods_nm_eng,
					o.goods_opt,
					ifnull(pss.qty, 0) as storage_qty,
					ifnull(pss.wqty, 0) as storage_wqty,
					ifnull(ps.qty, 0) as store_qty, 
					ifnull(ps.wqty, 0) as store_wqty,
					sum(ifnull(o.qty, 0)) as sale_cnt,
					(select sum(qty) from order_opt where (o.clm_state = 90 or o.clm_state = -30 or o.clm_state = 0) and prd_cd = o.prd_cd and store_cd = o.store_cd) as total_sale_cnt,
					DATE_FORMAT(DATE_ADD(NOW(), INTERVAL (ifnull(ROUND(ps.wqty * (TIMESTAMPDIFF(DAY, '$sdate 00:00:00', '$edate 23:59:59') / sum(o.qty))), 0)) DAY),'%Y-%m-%d') as exp_soldout_day,
					-- LEAST(if(sum(ifnull(o.qty, 0)) < 0, 0, sum(o.qty)), ifnull(pss.wqty, 0)) as rel_qty
					0 as rel_qty,
					0 as rel_qty2
				from order_opt o
					inner join product_code pc on pc.prd_cd = o.prd_cd
					inner join product_stock_storage pss on pss.prd_cd = o.prd_cd and pss.storage_cd = (select storage_cd from storage where default_yn = 'Y')
					inner join product_stock_store ps on ps.prd_cd = o.prd_cd and ps.store_cd = o.store_cd
					inner join store store on store.store_cd = o.store_cd
					left outer join goods g on g.goods_no = o.goods_no
					left outer join brand b on b.brand = g.brand
					left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
				where o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'
					and o.ord_state = 30 and o.clm_state in (90,-30,0)
					and ($store_where)
					and store.sale_dist_yn = 'Y'
					$where
				group by o.store_cd, o.prd_cd
				order by $orderby
				) a
			";
			$row	= DB::select($sql);
			$total	= $row[0]->total;
			$total_data = $row[0];
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		// search
		$sql = "
			select 
				o.store_cd,
				(select store_nm from store where store_cd = o.store_cd) as store_nm,
				o.prd_cd,
				concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_sm,
				pc.color,
				c.code_val as color_nm,
				-- ifnull((
				-- 	select s.size_cd from size s
				-- 	where s.size_kind_cd = pc.size_kind
				-- 	   and s.size_cd = pc.size
				-- 	   and use_yn = 'Y'
				-- ),'') as size,
				pc.size,
				o.goods_no,
				op.opt_kind_nm,
				b.brand_nm, 
				g.style_no,
				g.goods_nm,
				g.goods_nm_eng,
				ifnull(pss.qty, 0) as storage_qty,
				ifnull(pss.wqty, 0) as storage_wqty,
				ifnull(ps.qty, 0) as store_qty, 
				ifnull(ps.wqty, 0) as store_wqty,
				sum(ifnull(o.qty, 0)) as sale_cnt,
				(select sum(qty) from order_opt where (o.clm_state = 90 or o.clm_state = -30 or o.clm_state = 0) and prd_cd = o.prd_cd and store_cd = o.store_cd) as total_sale_cnt,
				DATE_FORMAT(DATE_ADD(NOW(), INTERVAL (ifnull(ROUND(ps.wqty * (TIMESTAMPDIFF(DAY, '$sdate 00:00:00', '$edate 23:59:59') / sum(o.qty))), 0)) DAY),'%Y-%m-%d') as exp_soldout_day,
				-- LEAST(if(sum(ifnull(o.qty, 0)) < 0, 0, sum(o.qty)), ifnull(pss.wqty, 0)) as rel_qty
				store.store_seq,
				store.store_seq_cd,
				store.store_seq_nm,
				0 as rel_qty,
				0 as rel_qty2
			from order_opt o
				inner join product_code pc on pc.prd_cd = o.prd_cd
				inner join product_stock_storage pss on pss.prd_cd = o.prd_cd and pss.storage_cd = (select storage_cd from storage where default_yn = 'Y')
				inner join product_stock_store ps on ps.prd_cd = o.prd_cd and ps.store_cd = o.store_cd
				inner join (
					select s.store_cd, s.store_channel, s.store_channel_kind, ifnull(c.code_seq, 999) as store_seq, c.code_id as store_seq_cd, c.code_val as store_seq_nm, s.sale_dist_yn
					from store s
						left outer join code c on c.code_kind_cd = 'PRIORITY' and c.code_id = s.priority
				) store on store.store_cd = o.store_cd
				left outer join goods g on g.goods_no = o.goods_no
				left outer join brand b on b.brand = g.brand
				left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
				left outer join code c on c.code_id = pc.color and c.code_kind_cd = 'PRD_CD_COLOR'
			where o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'
				and o.ord_state = 30 and o.clm_state in (90,-30,0)
				and ($store_where)
				and store.sale_dist_yn = 'Y'
				$where
			group by o.store_cd, o.prd_cd
			order by $orderby, store.store_seq asc
			$limit
		";
		$result = DB::select($sql);

		$releases = [];
		foreach ($result as $row) {
			if (!isset($releases[$row->prd_cd])) $releases[$row->prd_cd] = ['storage_wqty' => $row->storage_wqty];

			$releases[$row->prd_cd] = array_merge($releases[$row->prd_cd], [
				$row->store_cd => [
					'rel_qty' => 0,
					'sale_cnt' => ($row->sale_cnt * 1),
					'store_wqty' => $row->store_wqty,
					'store_seq' => $row->store_seq,
				]
			]);

			// 1순위 : 매장보유재고가 0인 경우
			if ($row->store_wqty < 1) {
				$rel_qty = ( $row->sale_cnt < 0 || $releases[$row->prd_cd]['storage_wqty'] < 0 ) ? 0 : ($row->sale_cnt * 1);
				$predicted_cnt = $releases[$row->prd_cd]['storage_wqty'] - $rel_qty;
				if ($predicted_cnt < 0 && $releases[$row->prd_cd]['storage_wqty'] > 0) $rel_qty = $releases[$row->prd_cd]['storage_wqty'];
				$releases[$row->prd_cd]['storage_wqty'] -= $rel_qty;
				$releases[$row->prd_cd][$row->store_cd]['rel_qty'] = $rel_qty;
			}
		}

		foreach ((array) $releases as $key => $value) {
			$sort_arr = [];
			$seq_sort_arr = [];
			foreach ((array) $releases[$key] as $k => $v) {
				if ($k !== 'storage_wqty') {
					$sort_arr[] = $v['sale_cnt'];
					$seq_sort_arr[] = $v['store_seq'];
				}
			}
			$data_arr = array_filter($releases[$key], function($n, $r) { return $r !== 'storage_wqty'; }, ARRAY_FILTER_USE_BOTH);
			array_multisort($seq_sort_arr, SORT_ASC, $sort_arr, SORT_DESC, $data_arr);
			$releases[$key] = array_merge(['storage_wqty' => $releases[$key]['storage_wqty']], $data_arr);

			foreach ((array) $releases[$key] as $k => $v) {
				if ($k !== 'storage_wqty' && $v['rel_qty'] < 1) {
					$rr = ( $v['sale_cnt'] < 0 || $releases[$key]['storage_wqty'] < 0 ) ? 0 : $v['sale_cnt'];
					$rel_qty = $rr;
					if ($v['store_wqty'] >= 5 && $v['store_wqty'] > ($v['sale_cnt'] * 2)) $rel_qty = 0;
					if ($v['sale_cnt'] >= 10) $rel_qty = $rr;

					$predicted_cnt = $releases[$key]['storage_wqty'] - $rel_qty;
					if ($predicted_cnt < 0 && $releases[$key]['storage_wqty'] > 0) $rel_qty = $releases[$key]['storage_wqty'];
					$releases[$key]['storage_wqty'] -= $rel_qty;
					$releases[$key][$k]['rel_qty'] = $rel_qty;
				}
				if ($k !== 'storage_wqty') $releases[$key][$k] = $releases[$key][$k]['rel_qty'];
			}
		}

		foreach ($result as $row) {
			$row->rel_qty = $releases[$row->prd_cd][$row->store_cd];
			$row->rel_qty2 = $releases[$row->prd_cd][$row->store_cd];
			$row->already_cnt = $row->storage_wqty - $releases[$row->prd_cd]['storage_wqty'];
		}

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result),
				"rel_products" => $releases,
				"total_data" => $total_data,
			],
			"body" => $result
		]);
	}

	// 판매분출고 요청 (요청과 동시에 접수완료 처리됩니다.)
	public function request_release(Request $request)
	{
		$r = $request->all();

		$release_type = 'S';
		$state = 20;
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$exp_dlv_day = $request->input("exp_dlv_day", '');
		$rel_order = $request->input("rel_order", '');
		$exp_day = str_replace("-", "", $exp_dlv_day);
		$exp_dlv_day_data = substr($exp_day,2,6);

		$data = $request->input("products", []);
		$failed_result = [];

		try {
			DB::beginTransaction();

			$storage_cd = DB::table('storage')->where('default_yn', '=', 'Y')->select('storage_cd')->get();
			$storage_cd = $storage_cd[0]->storage_cd;

			$sql = "select ifnull(document_number, 0) + 1 as document_number from product_stock_release order by document_number desc limit 1";
			$document_number = DB::selectOne($sql);
			if ($document_number === null) $document_number = 1;
			else $document_number = $document_number->document_number;

			foreach($data as $d) {
				$rel_qty2 = $d['rel_qty2'] ?? 0;
				if ($rel_qty2 < 1) continue;

				$store_cd = $d['store_cd'] ?? '';

				$sql = "
					select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
					from product_code pc
						inner join goods g on g.goods_no = pc.goods_no
					where prd_cd = :prd_cd
				";
				$prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
				if($prd == null) {
					array_push($failed_result, $d);
					continue;
				}

				// 창고재고수량 체크
				$current_wqty = DB::table('product_stock_storage')->where('storage_cd', $storage_cd)->where('prd_cd', $prd->prd_cd)->value('wqty');
				if ($current_wqty < $rel_qty2) {
					array_push($failed_result, $d);
					continue;
				}

				DB::table('product_stock_release')
					->insert([
						'document_number' => $document_number,
						'type' => $release_type,
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'qty' => $rel_qty2,
						'store_cd' => $store_cd,
						'storage_cd' => $storage_cd,
						'state' => $state,
						'exp_dlv_day' => str_replace("-", "", $exp_dlv_day_data),
						'rel_order' => $rel_order,
						'req_id' => $admin_id,
						'req_rt' => now(),
						'rec_id' => $admin_id,
						'rec_rt' => now(),
						'rt' => now(),
					]);

				$release_no	= DB::getPdo()->lastInsertId();

				// product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
				$store_stock_cnt =
					DB::table('product_stock_store')
						->where('store_cd', '=', $store_cd)
						->where('prd_cd', '=', $prd->prd_cd)
						->count();
				if($store_stock_cnt < 1) {
					// 해당 매장에 상품 기존재고가 없을 경우
					DB::table('product_stock_store')
						->insert([
							'goods_no' => $prd->goods_no,
							'prd_cd' => $prd->prd_cd,
							'store_cd' => $store_cd,
							'qty' => 0,
							'wqty' => $rel_qty2,
							'goods_opt' => $prd->goods_opt,
							'use_yn' => 'Y',
							'rt' => now(),
						]);
				} else {
					// 해당 매장에 상품 기존재고가 이미 존재할 경우
					DB::table('product_stock_store')
						->where('prd_cd', '=', $prd->prd_cd)
						->where('store_cd', '=', $store_cd)
						->update([
							'wqty' => DB::raw('wqty + ' . ($rel_qty2)),
							'ut' => now(),
						]);
				}

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'location_cd' => $store_cd,
						'location_type' => 'STORE',
						'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
						'price' => $prd->price,
						'wonga' => $prd->wonga,
						'qty' => $rel_qty2,
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'release_no'	=> $release_no,
						'comment' => '매장입고',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);

				// product_stock -> 창고보유재고 차감
				DB::table('product_stock')
					->where('prd_cd', '=', $prd->prd_cd)
					->update([
						'wqty' => DB::raw("wqty - $rel_qty2"),
						'ut' => now(),
					]);

				// product_stock_storage -> 보유재고 차감
				DB::table('product_stock_storage')
					->where('prd_cd', '=', $prd->prd_cd)
					->where('storage_cd', '=', $storage_cd)
					->update([
						'wqty' => DB::raw("wqty - $rel_qty2"),
						'ut' => now(),
					]);

				// 재고이력 등록
				DB::table('product_stock_hst')
					->insert([
						'goods_no' => $prd->goods_no,
						'prd_cd' => $prd->prd_cd,
						'goods_opt' => $prd->goods_opt,
						'location_cd' => $storage_cd,
						'location_type' => 'STORAGE',
						'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
						'price' => $prd->price,
						'wonga' => $prd->wonga,
						'qty' => $rel_qty2 * -1,
						'stock_state_date' => date('Ymd'),
						'ord_opt_no' => '',
						'release_no'	=> $release_no,
						'comment' => '창고출고',
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					]);
			}

			DB::commit();
			$code = 200;
			$msg = "판매분출고가 정상적으로 접수처리되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, "failed_list" => $failed_result]);
	}
}
