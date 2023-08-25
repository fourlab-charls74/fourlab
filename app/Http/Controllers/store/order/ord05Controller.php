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
		
		$orderby = sprintf("order by %s %s", $ord_field, $ord);
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = sprintf("limit %s, %s", $startno, $page_size);
		
		$sql = "
			select left(a.ord_date, 10) as ord_date
			    , a.ord_no
			    , a.store_cd
			    , a.store_nm
				, concat(a.user_nm, '(', a.user_id, ')') as user_nm
				, a.qty
				, a.ord_amt
				, a.recv_amt
				, a.dlv_amt
				, pt.code_val as pay_type
				, ot.code_val as ord_type
			from (
				select o.ord_date
					, o.ord_no
					, o.store_cd
					, s.store_nm
					, o.user_id
					, o.user_nm
					, (select sum(qty) from order_opt where ord_no = o.ord_no) as qty
					, o.ord_amt
					, o.recv_amt
					, o.dlv_amt
					, o.ord_type
				from order_mst o
					inner join store s on s.store_cd = o.store_cd
				where 1=1 $where
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
				select count(*) as total
				from order_mst o
					inner join store s on s.store_cd = o.store_cd
				where 1=1 $where
			";
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
	
	public function searchOrderOpts($ord_no, Request $request)
	{
		$sql = "
			select b.*
				, (b.price - b.sale_kind_amt) as sale_price
				, round((1 - ((b.price - b.sale_kind_amt) / b.goods_sh)) * 100) as dc_rate
				, (b.qty * (b.price - b.sale_kind_amt)) as ord_amt
			from (
				select a.*
					, sale_kind.code_val as sale_kind_nm
					, pr_code.code_val as pr_code_nm
					, memo.memo
					, if(st.amt_kind = 'per', round(a.price * st.sale_per / 100), st.sale_amt) as sale_kind_amt
				from (
					select o.ord_no, o.ord_opt_no, o.prd_cd, o.goods_no, g.style_no, g.goods_nm, g.goods_nm_eng
						, pc.prd_cd_p, pc.color, pc.size, o.goods_opt as opt_val, o.sale_kind, o.pr_code, o.recv_amt
						, o.qty, o.wonga, g.goods_sh, g.price as goods_price, o.price, o.store_cd
						, round((1 - (o.price / g.goods_sh)) * 100) as sale_dc_rate
					from order_opt o
						inner join product_code pc on pc.prd_cd = o.prd_cd
						inner join goods g on g.goods_no = o.goods_no
					where o.ord_no = :ord_no
				) a
					left outer join code sale_kind on (sale_kind.code_id = a.sale_kind and sale_kind.code_kind_cd = 'SALE_KIND')
					left outer join code pr_code on (pr_code.code_id = a.pr_code and pr_code.code_kind_cd = 'PR_CODE')
					left outer join sale_type st on st.sale_kind = a.sale_kind and st.use_yn = 'Y'
					left outer join order_opt_memo memo on memo.ord_opt_no = a.ord_opt_no
				order by a.ord_opt_no desc
			) b
		";
		$rows = DB::select($sql, [ 'ord_no' => $ord_no ]);

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
				'sale_kinds' => SLib::getUsedSaleKinds(),
			],
			"body" => $rows,
		]);
	}
}
