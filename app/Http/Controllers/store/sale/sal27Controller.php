<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class  sal27Controller extends Controller
{
	public function index(Request $request)
	{
		$sdate = $request->input('sdate', now()->startOfMonth()->format("Y-m-d"));
		$edate = $request->input('edate', now()->format("Y-m-d"));

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/sale/sal27', $values);
	}

	public function search(Request $request)
	{
		ini_set('memory_limit', -1);
		
		$sdate = $request->input('sdate', now()->startOfMonth()->sub(2, 'month')->format("Y-m-d"));
		$edate = $request->input('edate', now()->format("Y-m-d"));
		$sdate_day = date('Ymd', strtotime($sdate));
		$edate_day = date('Ymd', strtotime($edate));
		// $sdate_time = date('Y-m-d 00:00:00', strtotime($sdate));
		$edate_time = date('Y-m-d 23:59:59', strtotime($edate));
		$today_day = date('Ymd');
		$next_edate_day = date('Ymd', strtotime($edate_day . '+1 day'));

		$store_channel = $request->input('store_channel', '');
		$store_channel_kind = $request->input('store_channel_kind', '');
		$store_cds = $request->input('store_no', []);
		$prd_cd = $request->input('prd_cd', '');
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$storage_cds = $request->input('storage_no', []);

		// $limit = $request->input('limit', 500);
		// $ord_field = $request->input('ord_field', 'pc.prd_cd');
		// $ord = $request->input('ord', 'desc');
		// $page = $request->input('page', 1);

		// 일자검색
		$date_where1 = " and ord_state_date <= '" . $edate_day . "'";
		$date_where2 = " and r_stock_state_date <= '" . $edate_day . "'";
		$date_where3 = " and replace(sr_date, '-', '') <= '" . $edate_day . "'";
		$date_where4 = " and prc_rt <= '" . $edate_time . "'";
		$date_where5 = " and h.stock_state_date >= '" . $next_edate_day . "' and h.stock_state_date <= '" . $today_day . "'";
		$date_where6 = " and aa.ord_state_date >= '" . $sdate_day . "' and aa.ord_state_date <= '" . $edate_day . "'";

		// 매장검색
		$store_where = "";
		$store_where2 = "";
		if ($store_channel != '') {
			$store_where .= " and s.store_channel = '" . Lib::quote($store_channel) . "'";
		}
		if ($store_channel_kind != '') {
			$store_where .= " and s.store_channel_kind = '" . Lib::quote($store_channel_kind) . "'";
		}
		if (count($store_cds) > 0) {
			$store_cds = join(',', array_map(function($s) { return "'" . Lib::quote($s) . "'"; }, $store_cds));
			$store_where .= " and s.store_cd in (" . $store_cds . ")";
			$store_where2 .= " and h.location_cd in (" . $store_cds . ")";
		}

		// 창고검색
		$storage_where1 = "";
		$storage_where2 = "";
		if (count($storage_cds) > 0) {
			$storage_cds = join(',', array_map(function($s) { return "'" . Lib::quote($s) . "'"; }, $storage_cds));
			$storage_where1 .= " and storage_cd in (" . $storage_cds . ")";
			$storage_where2 .= " and h.location_cd in (" . $storage_cds . ")";
		}

		// 바코드검색
		$product_where = "";
		// 바코드검색
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
				$product_where .= " and pc.prd_cd in ($prd_cds_str) ";
			} else {
				//$product_where .= " and pc.prd_cd = '" . Lib::quote($prd_cd) . "' ";
				$product_where .= " and pc.prd_cd like '" . Lib::quote($prd_cd) . "%' ";
			}
		}

		// 상품조건검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$opt_join = join(',', array_map(function($r) {return "'" . Lib::quote($r) . "'";}, $rows));
				$product_where .= " and pc." . $opt . " in (" . $opt_join . ")";
			}
		}

		// 월별판매내역검색
		$month_sale_sql = "";
		$month_sale_cols = [];
		$sdate_month = date('Ym01', strtotime($sdate));
		$edate_month = date('Ym01', strtotime($edate));
		$interval = date_diff(date_create($sdate_month), date_create($edate_month)->setTime(24,0,0));
		for ($i = 0; $i <= ($interval->m + ($interval->y * 12)); $i++) {
			$from = date('Ymd', strtotime($sdate_month . '+' . $i . ' month'));
			$to = date('Ymd', strtotime(date('Y-m', strtotime($from)) . '-' . date('t', strtotime($from))));
			if ($from < $sdate_day) $from = $sdate_day;
			if ($to > $edate_day) $to = $edate_day;

			$month_sale_sql .= "
				, (
					select ifnull(sum(ifnull(aa.qty, 0) * if(aa.ord_state = 30, 1, -1)), 0) as sale_qty 
					from order_opt_wonga aa
					inner join order_opt bb on aa.ord_opt_no = bb.ord_opt_no and bb.ord_state = '30'
					where aa.ord_state_date >= '" . $from . "' and aa.ord_state_date <= '" . $to . "'
						and if( aa.ord_state_date <= '20231109', bb.sale_kind is not null, 1=1)	-- 피엘라벤 초기화 특성
						and aa.ord_state in (30,60,61)
						and bb.prd_cd = pc.prd_cd
				) as sale_qty_" . $from
			;
			$month_sale_cols[] = [ 'key' => "sale_qty_" . $from, 'kor_nm' => date('Y년 m월', strtotime($from)) ];
		}

		$sql = "
			select a.*
				, c.code_val as item_nm
				, b.brand_nm
				, clr.code_val as color_nm
			    , com.com_nm
				, (a.term_in_qty * a.goods_sh) as term_in_goods_sh
				, (a.term_in_qty * a.price) as term_in_price
				, (a.term_in_qty * a.wonga) as term_in_wonga
				, (a.sale_qty * a.goods_sh) as sale_goods_sh
				-- , (a.sale_qty * a.sale_price) as sale_price
			    , a.sale_price as sale_price
				-- , (a.sale_qty * a.wonga) as sale_wonga
			    , a.sale_wonga as sale_wonga
				, (a.term_storage_qty * a.goods_sh) as term_storage_goods_sh
				, (a.term_storage_qty * a.price) as term_storage_price
				, (a.term_storage_qty * a.wonga) as term_storage_wonga
				, (a.term_store_qty * a.goods_sh) as term_store_goods_sh
				, (a.term_store_qty * a.price) as term_store_price
				, (a.term_store_qty * a.wonga) as term_store_wonga
				, (a.term_storage_qty + a.term_store_qty) as term_total_qty
				, ((a.term_storage_qty * a.goods_sh) + (a.term_store_qty * a.goods_sh)) as term_total_goods_sh
				, ((a.term_storage_qty * a.price) + (a.term_store_qty * a.price)) as term_total_price
				, ((a.term_storage_qty * a.wonga) + (a.term_store_qty * a.wonga)) as term_total_wonga
			 	, round(a.sale_qty / a.term_in_qty * 100) as sale_ratio
			    -- , round((1 - (a.sale_recv_price / (a.sale_qty * a.price))) * 100) as discount_ratio
			    , round((1 - (a.sale_recv_price / a.sale_price)) * 100) as discount_ratio
			from (
				select pc.*
					, ps.in_qty as total_in_qty
					, date_format((
						select min(ord_state_date)
						from order_opt_wonga
						where 1=1 
							and ord_state = 30
							and prd_cd = pc.prd_cd
					), '%Y-%m-%d') as first_sale_date
					, (
						select ifnull(sum(ifnull(aa.qty, 0) * if(aa.ord_state = 30, 1, -1)), 0) as sale_qty 
						from order_opt_wonga aa 
						inner join order_opt bb on aa.ord_opt_no = bb.ord_opt_no and bb.ord_state = '30'
						where 1=1 
							-- $date_where1
							and if( aa.ord_state_date <= '20231109', bb.sale_kind is not null, 1=1)	-- 피엘라벤 초기화 특성
							and aa.ord_state in (30,60,61)
							and bb.prd_cd = pc.prd_cd
					) as sale_qty
					, (
						select ifnull(sum(aa.price * aa.qty), 0) as sale_price 
						from order_opt_wonga aa
						inner join order_opt bb on aa.ord_opt_no = bb.ord_opt_no and bb.ord_state = '30'
						where 1=1 
							-- $date_where1
							and if( aa.ord_state_date <= '20231109', bb.sale_kind is not null, 1=1)	-- 피엘라벤 초기화 특성
							and aa.ord_state in (30,60,61)
							and bb.prd_cd = pc.prd_cd
					) as sale_price
					, (
						select ifnull(sum(aa.wonga * aa.qty), 0) as sale_wonga 
						from order_opt_wonga aa
						inner join order_opt bb on aa.ord_opt_no = bb.ord_opt_no and bb.ord_state = '30'
						where 1=1 
							-- $date_where1
							and if( aa.ord_state_date <= '20231109', bb.sale_kind is not null, 1=1)	-- 피엘라벤 초기화 특성
							and aa.ord_state in (30,60,61)
							and bb.prd_cd = pc.prd_cd
					) as sale_wonga
					, (
						select ifnull(sum(aa.recv_amt), 0) as price
						from order_opt_wonga aa
						inner join order_opt bb on aa.ord_opt_no = bb.ord_opt_no and bb.ord_state = '30'
						where 1=1
							-- $date_where1
							and if( aa.ord_state_date <= '20231109', bb.sale_kind is not null, 1=1)	-- 피엘라벤 초기화 특성
							and aa.ord_state in (30,60,61)
							and bb.prd_cd = pc.prd_cd
					) as sale_recv_price
				    $month_sale_sql
					, (
						select ifnull(sum(ifnull(aa.qty, 0) * if(aa.ord_state = 30, 1, -1)), 0) as sale_qty 
						from order_opt_wonga aa
						inner join order_opt bb on aa.ord_opt_no = bb.ord_opt_no and bb.ord_state = '30'
						where 1=1 
							$date_where6
							and if( aa.ord_state_date <= '20231109', bb.sale_kind is not null, 1=1)	-- 피엘라벤 초기화 특성
							and aa.ord_state in (30,60,61)
							and bb.prd_cd = pc.prd_cd
					) as term_sale_qty
					, (
						select ifnull(sum(qty), 0)
						from product_stock_hst h
						where prd_cd = pc.prd_cd
							$date_where2
							and type in (1,9) 
							-- and location_type = 'STORE'
							and if( h.stock_state_date <= '20231109', (h.location_type = 'STORE' or h.location_type = 'STORAGE') , h.location_type = 'STORAGE')
							$store_where2
					) as term_in_qty
					, ifnull(date_format(first_release_date, '%Y-%m-%d'), '') as first_release_date
					, ifnull(psr.qty, 0) as term_release_qty
					, ifnull(srp.qty, 0) as term_return_qty
					, (ifnull(psr.qty, 0) - ifnull(srp.qty, 0)) as term_out_qty
					, (
						ifnull((
							select sum(wqty) as wqty
							from product_stock_storage
							where 1=1 $storage_where1
								and prd_cd = pc.prd_cd
						), 0)
						- 
						ifnull((
							select sum(h.qty) as qty
							from product_stock_hst h
							where h.location_type = 'STORAGE' $storage_where2
								$date_where5
								and h.prd_cd = pc.prd_cd
						), 0)
					) as term_storage_qty
					, (
						ifnull((
							select sum(ss.wqty + ss.rqty) as wqty
							from product_stock_store ss
								inner join store s on s.store_cd = ss.store_cd
							where 1=1 $store_where
								and ss.prd_cd = pc.prd_cd
						), 0)
						- 
						ifnull((
							select sum(h.qty) as qty
							from product_stock_hst h
							inner join store s on s.store_cd = h.location_cd
							where h.location_type = 'STORE' $store_where2
								$date_where5
								and h.prd_cd = pc.prd_cd
						), 0)
					) as term_store_qty
				from (
					select pc.item, pc.brand, pc.prd_cd, pc.goods_no, pc.color, pc.goods_opt
						, pc.size
						-- , ifnull(pc.prd_cd_p, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt)) as prd_cd_p
				  		, pc.prd_cd_p
						, g.goods_nm, g.goods_nm_eng, p.tag_price as goods_sh, p.price as price, p.wonga as wonga, g.com_id
					from product_code pc
					inner join product p on pc.prd_cd = p.prd_cd
					inner join goods g on g.goods_no = pc.goods_no
					where 1=1 
						$product_where 
						and pc.goods_no <> 0
						and pc.type = 'N'
					order by pc.item, pc.brand, pc.prd_cd_p desc, pc.color, pc.prd_cd desc
				) pc
					inner join product_stock ps on ps.prd_cd = pc.prd_cd
/*
					left outer join (
						select prd_cd, sum(wqty) as wqty
						from product_stock_storage
						where 1=1 $storage_where1
						group by prd_cd
					) pssg on pssg.prd_cd = pc.prd_cd
					left outer join (
						select ss.prd_cd, sum(ss.wqty + ss.rqty) as wqty
						from product_stock_store ss
							inner join store s on s.store_cd = ss.store_cd
						where 1=1 $store_where
						group by ss.prd_cd
					) pss on pss.prd_cd = pc.prd_cd
*/
					left outer join (
						select r.prd_cd, sum(r.qty) as qty, min(r.prc_rt) as first_release_date
						from product_stock_release r
							inner join store s on s.store_cd = r.store_cd
							inner join (
								select idx
								from product_stock_release
								where 1=1 $storage_where1
									and state > 20
									$date_where4
							) rr on rr.idx = r.idx
						where 1=1 $store_where
						group by r.prd_cd
					) psr on psr.prd_cd = pc.prd_cd
					left outer join (
						select p.prd_cd, sum(if(r.sr_state = 40, ifnull(p.fixed_return_qty, 0), ifnull(p.return_p_qty, 0))) as qty
						from store_return_product p
							inner join (
								select r.sr_cd, r.sr_state, r.store_cd
								from store_return r
									inner join (
										select sr_cd
										from store_return
										where 1=1 $storage_where1 
											-- and sr_state > 10
											and sr_state = '40'
											$date_where3
									) rr on rr.sr_cd = r.sr_cd
							) r on r.sr_cd = p.sr_cd
							inner join store s on s.store_cd = r.store_cd
						where 1=1 $store_where
						group by p.prd_cd
					) srp on srp.prd_cd = pc.prd_cd
/*
					left outer join (
						select h.prd_cd, sum(h.qty) as qty
						from product_stock_hst h
						where h.location_type = 'STORAGE' $storage_where2
							$date_where5
						group by h.prd_cd
					) storage_hst on storage_hst.prd_cd = pc.prd_cd
					left outer join (
						select h.prd_cd, sum(h.qty) as qty
						from product_stock_hst h
							inner join store s on s.store_cd = h.location_cd
						where h.location_type = 'STORE' $store_where2
							$date_where5
						group by h.prd_cd
					) store_hst on store_hst.prd_cd = pc.prd_cd
*/
			) a
				left outer join code clr on clr.code_kind_cd = 'PRD_CD_COLOR' and clr.code_id = a.color
				left outer join code c on c.code_kind_cd = 'PRD_CD_ITEM' and c.code_id = a.item
				left outer join brand b on b.br_cd = a.brand
				left outer join company com on com.com_id = a.com_id
		";
		$rows = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($rows),
				'sale_month' => $month_sale_cols,
			],
			"body" => $rows
		]);
	}
}
