<?php

namespace App\Http\Controllers\store\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Order;
use App\Models\Point;
use App\Models\Claim;
use App\Models\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use PDO;

class ord05Controller extends Controller
{
	public function index(Request $request)
	{
		$sdate = now()->sub(1, 'week')->format('Y-m-d');
		$edate = date('Y-m-d');

		$values = [
			'sdate' 		=> $sdate,
			'edate' 		=> $edate,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
		return view(Config::get('shop.store.view') . '/order/ord05', $values);
	}
	
	public function search_command($ord_no = '', Request $request)
	{
		if ($ord_no === '') {
			return $this->search($request);
		} else {
			return $this->searchOrderOpts($ord_no, $request);
		}
	}

	/** 주문내역조회 */
	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->sub(3, 'month')->format('Ymd'));
		$edate = $request->input('edate', date('Ymd'));
		$is_not_use_date = false;
		$nud = $request->input('nud', ''); // 주문일자 검색여부 (On: 'on', Off: '')
		$store_channel = $request->input('store_channel', '');
		$store_channel_kind	= $request->input('store_channel_kind', '');
		$store_cd = $request->input('store_no', '');
		// $prd_cd = $request->input('prd_cd', '');
		// $prd_cd_range_text = $request->input("prd_cd_range", '');
		// $style_no = $request->input('style_no', '');
		// $goods_no = $request->input('goods_no', '');
		// $goods_nm = $request->input('goods_nm', '');
		$ord_no = $request->input('ord_no', '');
		$limit = $request->input('limit', 100);
		$ord = $request->input('ord', 'desc');
		$ord_field = $request->input('ord_field', 'o.ord_date');
		$page = $request->input('page', 1);
		if ($page < 1 or $page == '') $page = 1;

		$where = "";
		
		if ($ord_no != '') $where .= " and o.ord_no = '" . Lib::quote($ord_no) . "'";
		if (!$is_not_use_date && $nud === 'on') $where .= " and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'";
		// $where .= " and o.ord_kind != '10'";
		
		if ($store_channel != '') $where .= " and s.store_channel = '" . Lib::quote($store_channel) . "'";
		if ($store_channel_kind != '') $where .= " and s.store_channel_kind = '" . Lib::quote($store_channel_kind) . "'";
		if ($store_cd != '') $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";
		
		$orderby = sprintf("order by %s %s, o.ord_no desc, ow.ord_state desc", $ord_field, $ord);
		// $orderby = sprintf("order by %s %s", $ord_field, $ord);
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = sprintf("limit %s, %s", $startno, $page_size);
		
		$sql = "
			select left(a.ord_date, 10) as ord_date
			    , a.ord_no
			    , a.store_cd
			    , a.store_nm
				, concat(a.user_nm, '(', a.user_id, ')') as user_nm
				, if(a.ord_state = 30, a.qty, a.qty * -1) as qty
			    -- , a.qty
				, a.ord_amt
				, a.recv_amt
				, a.dlv_amt
				, pt.code_val as pay_type
				, ot.code_val as ord_type
			    , a.ord_state
				, if(a.ord_state = 30, '판매', if(a.ord_state = 60, '교환', '환불')) as ord_state_nm
			from (
				select o.ord_date
					, o.ord_no
					, o.store_cd
					, s.store_nm
					, o.user_id
					, o.user_nm
				    , sum(oo.qty) as qty
				 	-- , (select sum(qty) from order_opt where ord_no = o.ord_no) as qty
					, o.ord_amt
					, o.recv_amt
					, o.dlv_amt
					, o.ord_type
					, ow.ord_state
				from order_mst o
					inner join store s on s.store_cd = o.store_cd
					inner join order_opt oo on oo.ord_no = o.ord_no
                    inner join order_opt_wonga ow on ow.ord_opt_no = oo.ord_opt_no and ow.ord_state in (30,60,61)
				where 1=1 $where
				group by o.ord_no, ow.ord_state
				$orderby
				$limit
			) a
				left outer join payment p on p.ord_no = a.ord_no
				left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = p.pay_type
				left outer join code ot on ot.code_kind_cd = 'G_ORD_TYPE' and ot.code_id = a.ord_type
		";
		$rows = DB::select($sql);
		
		$total = 0;
		$page_cnt = 0;
		
		if ($page == 1) {
			$sql = "
				select count(total) as total
				from (    
					select count(*) as total
					from order_mst o
						inner join store s on s.store_cd = o.store_cd
						inner join order_opt oo on oo.ord_no = o.ord_no
						inner join order_opt_wonga ow on ow.ord_opt_no = oo.ord_opt_no and ow.ord_state in (30,60,61)
					where 1=1 $where
					group by o.ord_no, ow.ord_state
				) a
			";
			// $sql = "
			// 	select count(*) as total
			// 	from order_mst o
			// 		inner join store s on s.store_cd = o.store_cd
			// 	where 1=1 $where
			// ";
			$total = DB::selectOne($sql)->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($rows),
			],
			"body" => $rows,
		]);
	}
	
	/** 특정주문의 상품내역조회 */
	public function searchOrderOpts($ord_no, Request $request)
	{
		$ord_state = $request->input('ord_state', 30);
		$first_of_the_current_month = now()->firstOfMonth()->format('Y-m-d 00:00:00');

		$sql = "
			select b.*
				, (b.price - b.sale_kind_amt) as sale_price
				, round((1 - ((b.price - b.sale_kind_amt) / b.goods_sh)) * 100) as dc_rate
				, (b.qty * (b.price - b.sale_kind_amt)) as ord_amt
				, if(b.ord_date >= :first_date and ((
				    select count(c.idx)
				    from store_account_closed_list c
				    where c.ord_opt_no = b.ord_opt_no
				) < 1), 'Y', 'N') as is_editable
			from (
				select a.*
					, st.sale_type_nm as sale_kind_nm
					, pr_code.code_val as pr_code_nm
					, memo.memo
					, ifnull(if(st.amt_kind = 'per', round(a.price * st.sale_per / 100), st.sale_amt), 0) as sale_kind_amt
					, clm.code_val as clm_state_nm
				from (
					select o.ord_no, o.ord_opt_no, o.ord_date, o.prd_cd, o.goods_no, g.style_no, g.goods_nm, g.goods_nm_eng
						, pc.prd_cd_p, pc.color, pc.size, o.goods_opt as opt_val, o.sale_kind as sale_kind_cd, o.pr_code as pr_code_cd
						, if(w.ord_state = 30, o.qty, o.qty * -1) as qty
					    -- , p.wonga
					    , o.wonga
					    -- , g.goods_sh
					    , p.tag_price as goods_sh
					    -- , g.price as goods_price
					    , p.price as goods_price
					    , o.price, o.store_cd
					    , if(w.ord_state = 30, o.recv_amt, o.recv_amt * -1) as recv_amt, w.ord_state, o.clm_state
					    , (o.point_amt * -1) as point_amt, (o.coupon_amt * -1) as coupon_amt
					 	-- , o.qty, o.wonga, g.goods_sh, g.price as goods_price, o.price, o.recv_amt, o.store_cd, o.clm_state
						, round((1 - (o.price / g.goods_sh)) * 100) as sale_dc_rate
					from order_opt o
					    inner join order_opt_wonga w on w.ord_opt_no = o.ord_opt_no and w.ord_state = :ord_state
						inner join product_code pc on pc.prd_cd = o.prd_cd
						inner join product p on p.prd_cd = o.prd_cd
						inner join goods g on g.goods_no = o.goods_no
					where o.ord_no = :ord_no 
						-- and if(o.clm_state >= 60, :ord_state2 >= 60, 1=1)
				) a
					left outer join sale_type st on st.sale_kind = a.sale_kind_cd
					left outer join code pr_code on (pr_code.code_id = a.pr_code_cd and pr_code.code_kind_cd = 'PR_CODE')
					left outer join order_opt_memo memo on memo.ord_opt_no = a.ord_opt_no
					left outer join code clm on clm.code_kind_cd = 'G_CLM_STATE' and clm.code_id = a.clm_state
				order by a.ord_opt_no desc
			) b
		";
		$rows = DB::select($sql, [ 'ord_no' => $ord_no, 'first_date' => $first_of_the_current_month, 'ord_state' => $ord_state ]);
		// $rows = DB::select($sql, [ 'ord_no' => $ord_no, 'first_date' => $first_of_the_current_month, 'ord_state' => $ord_state, 'ord_state2' => $ord_state ]);
		// $rows = DB::select($sql, [ 'ord_no' => $ord_no, 'first_date' => $first_of_the_current_month ]);

		$pr_codes = [];
		if (count($rows) > 0) {
			$date = date('Y-m-d');
			$sql = "
				select f.store_cd, f.pr_code, p.code_val as pr_code_nm, f.store_fee, f.sdate, f.edate, f.use_yn
				from store_fee f
					inner join code p on p.code_kind_cd = 'PR_CODE' and p.code_id = f.pr_code
				where f.store_cd = :store_cd and f.use_yn = 'Y' and f.sdate <= :date1 and f.edate >= :date2
				group by f.pr_code
			";
			$pr_codes = DB::select($sql, [ 'store_cd' => $rows[0]->store_cd, 'date1' => $date, 'date2' => $date ]);
		}

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
				"pr_codes" => $pr_codes,
				'sale_kinds' => SLib::getUsedSaleKinds('', 'Y'),
			],
			"body" => $rows,
		]);
	}
	
	/** 상품내역 수정 */
	public function update(Request $request)
	{
		$code = 200;
		$msg = "";
		$orders = $request->input('data', []);
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		try {
			DB::beginTransaction();
			
			$ord_no = '';
			$total_diff_qty = 0;
			$total_diff_ord_amt = 0;
			$total_diff_dc_amt = 0;
			$total_diff_recv_amt = 0;
			
			foreach ($orders as $order) {
				$ord_opt_no = $order['ord_opt_no'];
				$new_qty = $order['qty'] ?? 0;
				$new_sale_kind_cd = $order['sale_kind_cd'] ?? '';
				$prev_sale_price = $order['ori_sale_price'] ?? 0;
				$new_sale_price = $order['sale_price'] ?? 0;
				$new_pr_code_cd = $order['pr_code_cd'] ?? '';
				$new_memo = $order['memo'] ?? '';
				
				$prev_order_opt = DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->first();
				if ($prev_order_opt === null) throw new Exception("해당 주문건이 존재하지 않습니다.");
				if ($ord_no === '') $ord_no = $prev_order_opt->ord_no;

				$diff_qty = $prev_order_opt->qty - ($new_qty * 1);
				$total_diff_qty += $diff_qty;
				$total_diff_ord_amt += $diff_qty * $prev_order_opt->price;

				// 01. order_opt
				$values = [];
				if ($diff_qty !== 0) $values['qty'] = $new_qty;
				if ($prev_order_opt->sale_kind != $new_sale_kind_cd) $values['sale_kind'] = $new_sale_kind_cd;
				if ($prev_order_opt->pr_code != $new_pr_code_cd) $values['pr_code'] = $new_pr_code_cd;
				if ($diff_qty !== 0 || $prev_sale_price != $new_sale_price) {
					$values['dc_amt'] = ($prev_order_opt->price - $new_sale_price) * $new_qty;
					$values['recv_amt'] = ($prev_order_opt->price * $new_qty) - $prev_order_opt->coupon_amt - $prev_order_opt->point_amt - $values['dc_amt'];
					$total_diff_dc_amt += $values['dc_amt'] - ($prev_order_opt->dc_amt ?? 0);
					$total_diff_recv_amt += $values['recv_amt'] - ($prev_order_opt->recv_amt ?? 0);
				}
				if (count($values) > 0) {
					DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->update($values);
					
					$log_values = [
						'ord_no' => $ord_no,
						'ord_opt_no' => $ord_opt_no,
						'goods_no' => $prev_order_opt->goods_no,
						'prd_cd' => $prev_order_opt->prd_cd,
						'store_cd' => $prev_order_opt->store_cd,
						'ord_update_date' => date('Ymd'),
						'rt' => now(),
						'admin_id' => $admin_id,
						'admin_nm' => $admin_nm,
					];

					// 01-1. store_order_log (이전 내용 주문내역 수정이력)
					DB::table('store_order_log')->insert(array_merge($log_values, [
						'qty' => $prev_order_opt->qty * -1,
						'point_amt' => $prev_order_opt->point_amt * -1,
						'coupon_amt' => $prev_order_opt->coupon_amt * -1,
						'dc_amt' => ($prev_order_opt->dc_amt ?? 0) * -1,
						'recv_amt' => ($prev_order_opt->recv_amt ?? 0) * -1,
						'sale_kind' => $prev_order_opt->sale_kind,
						'pr_code' => $prev_order_opt->pr_code,
					]));

					// 01-2. store_order_log (업데이트된 내용 주문내역 수정이력)
					DB::table('store_order_log')->insert(array_merge($log_values, [
						'qty' => $new_qty,
						'point_amt' => $prev_order_opt->point_amt,
						'coupon_amt' => $prev_order_opt->coupon_amt,
						'dc_amt' => $values['dc_amt'] ?? $prev_order_opt->dc_amt,
						'recv_amt' => $values['recv_amt'] ?? $prev_order_opt->recv_amt,
						'sale_kind' => $new_sale_kind_cd,
						'pr_code' => $new_pr_code_cd,
					]));
				}
				
				// 02. order_opt_wonga
				$values = [];
				if ($diff_qty !== 0) $values['qty'] = $new_qty;
				if ($diff_qty !== 0 || $prev_sale_price != $new_sale_price) {
					$values['dc_apply_amt'] = ($prev_order_opt->price - $new_sale_price) * $new_qty;
					$values['recv_amt'] = ($prev_order_opt->price * $new_qty) - $prev_order_opt->coupon_amt - $prev_order_opt->point_amt - $values['dc_apply_amt'];
				}
				if (count($values) > 0) {
					DB::table('order_opt_wonga')->where('ord_opt_no', $ord_opt_no)->update($values);
				}
				
				// 03. order_opt_memo
				$where = [
					'ord_opt_no' => $ord_opt_no,
					'ord_no' => $ord_no,
				];
				$values = [
					'memo' => $new_memo,
					'ut' => now(),
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
				];
				DB::table('order_opt_memo')->updateOrInsert($where, $values);

				// 출고완료된 (주문으로 인해 재고처리가 완료된) 주문건의 재고 재처리
				$is_store_order = $prev_order_opt->store_cd != '';
				if ($prev_order_opt->ord_state >= 10 && $prev_order_opt->prd_cd != '') {
					// 04. product_stock
					if ($diff_qty !== 0) {
						$values = [];
						$values['qty'] = DB::raw('qty + ' . $diff_qty); // 전체재고 업데이트
						if (!$is_store_order) $values['wqty'] = DB::raw('wqty + ' . $diff_qty); // 창고재고 업데이트
						$values['out_qty'] = DB::raw('out_qty - ' . $diff_qty);
						$values['qty_wonga'] = DB::raw('qty * wonga');
						$values['ut'] = now();
						DB::table('product_stock')->where('prd_cd', $prev_order_opt->prd_cd)->update($values);
					}
					
					// 05. product_stock_store / product_stock_storage
					if ($diff_qty !== 0) {
						$values = [];
						if ($prev_order_opt->ord_state >= 30) $values['qty'] = DB::raw('qty + ' . $diff_qty);
						$values['wqty'] = DB::raw('wqty + ' . $diff_qty);
						$values['ut'] = now();

						if ($is_store_order) {
							DB::table('product_stock_store')->where('store_cd', $prev_order_opt->store_cd)->where('prd_cd', $prev_order_opt->prd_cd)->update($values);
						} else {
							DB::table('product_stock_storage')->where('storage_cd', DB::raw("(select storage_cd from storage where default_yn = 'Y')"))->where('prd_cd', $prev_order_opt->prd_cd)->update($values);
						}
					}
					
					// 06. product_stock_hst
					if ($diff_qty !== 0) {
						$values = [
							'goods_no' => $prev_order_opt->goods_no,
							'prd_cd' => $prev_order_opt->prd_cd,
							'goods_opt' => $prev_order_opt->goods_opt,
							'price' => $prev_order_opt->price,
							'type' => 2, // 재고분류 : 주문
							'wonga' => $prev_order_opt->wonga,
							'stock_state_date' => date('Ymd'),
							'ord_opt_no' => $ord_opt_no,
							'comment' => '주문내역수정',
							'rt' => now(),
							'admin_id' => $admin_id,
							'admin_nm' => $admin_nm,
						];

						if ($is_store_order) {
							$values['location_cd'] = $prev_order_opt->store_cd;
							$values['location_type'] = 'STORE';
						} else {
							$values['location_cd'] = DB::raw("(select storage_cd from storage where default_yn = 'Y')");
							$values['location_type'] = 'STORAGE';
						}

						// 이전로그 마이너스처리
						$values['qty'] = $prev_order_opt->qty;
						DB::table('product_stock_hst')->insert($values);

						// new 로그 등록
						$values['qty'] = $new_qty * -1;
						DB::table('product_stock_hst')->insert($values);
					}
				}
			}

			if ($total_diff_qty !== 0 || $total_diff_recv_amt !== 0) {
				// 07. order_mst
				$values = [];
				$values['ord_amt'] = DB::raw('ord_amt - ' . $total_diff_ord_amt);
				$values['dc_amt'] = DB::raw('dc_amt + ' . $total_diff_dc_amt);
				$values['recv_amt'] = DB::raw('recv_amt + ' . $total_diff_recv_amt);
				$values['upd_date'] = now();
				DB::table('order_mst')->where('ord_no', $ord_no)->update($values);

				// 08. payment
				$values = [];
				$values['dc_amt'] = DB::raw('dc_amt + ' . $total_diff_dc_amt);
				$values['pay_amt'] = DB::raw('pay_amt + ' . $total_diff_recv_amt);
				$values['upd_dm'] = date('YmdHis');
				DB::table('payment')->where('ord_no', $ord_no)->update($values);

				// 09. member_stat -> 프로시저로 설정되어 따로 처리가 필요없음
				// $user_id = DB::table('order_mst')->where('ord_no', $ord_no)->value('user_id');
				// if ($user_id != '') {
				// 	$values = [];
				// 	$values['ord_cnt'] = DB::raw('ord_cnt - ' . $total_diff_qty);
				// 	$values['ord_amt'] = DB::raw('ord_amt - ' . $total_diff_ord_amt);
				// 	$values['ut'] = now();
				// 	DB::table('member_stat')->where('user_id', $user_id)->update($values);
				// }
			}

			DB::commit();
			$msg = "주문내역이 정상적으로 저장되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json(['code' => $code, 'msg' => $msg], $code);
	}
}
