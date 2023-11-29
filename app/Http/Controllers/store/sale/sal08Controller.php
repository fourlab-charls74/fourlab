<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/** 매장브랜드별매출분석 */
class sal08Controller extends Controller
{
	public function index(Request $request) 
	{
		$sdate = Carbon::now()->firstOfMonth()->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");

		$values = [
            'sdate'         => $sdate,
			'edate'         => $edate,
			'store_types'	=> SLib::getStoreTypes(), // 매장구분
			'items'			=> SLib::getItems(), // 품목
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
        return view( Config::get('shop.store.view') . '/sale/sal08', $values );
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', Carbon::now()->sub(1, 'week')->format("Ymd"));
		$edate = $request->input('edate', Carbon::now()->format("Ymd"));
		$sdate = str_replace('-', '', $sdate);
		$edate = str_replace('-', '', $edate);

		$store_no = $request->input('store_no', []); // 매장명 리스트
        $brand_cd = $request->input('brand_cd', ''); // 브랜드
		$goods_nm = $request->input('goods_nm', ''); // 상품명
		$goods_nm_eng = $request->input('goods_nm_eng', ''); // 상품명(영문)
		$prd_cd	= $request->input('prd_cd', ''); // 상품코드
		$prd_cd_range_text = $request->input('prd_cd_range', ''); // 상품옵션범위
		$item = $request->input('item', ''); // 품목
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		/** 검색조건 필터링 */
		$where = "";
		$where2 = "";
		
		$where .= " and w.ord_state_date >= '$sdate' and w.ord_state_date <= '$edate' ";
		if ($store_channel != "") $where .= " and s.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= " and s.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
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

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}
		if ($item != '') $where2 .= " and g.opt_kind_cd = '$item' ";
		
		$vat = 1.1;

		$sql = "
			select
				a.store_cd, a.store_nm
				, a.store_channel, a.store_channel_kind as store_kind
			    , concat(sc.store_channel, '/', sc.store_kind) as store_kind_nm
				, a.pr_code, prc.code_val as pr_code_nm
				, a.brand, b.brand_nm
				, sum((a.qty * (a.price - a.sale_kind_amt)) * if(a.w_ord_state > 30, -1, 1)) as sale_amt
			    , sum(a.recv_amt * if(a.ord_state > 30, -1, 1)) as recv_amt
				, sum(a.wonga_amt) as wonga_amt
				, sum(a.margin_amt) as margin_amt
				, (sum(a.margin_amt) / sum((a.qty * (a.price - a.sale_kind_amt)) * if(a.ord_state > 30, -1, 1)) * 100) as margin_rate
				-- , sum(if(a.ord_state = '10', ifnull(a.taxation_amt, 0), 0)) + sum(if(a.ord_state = '60', ifnull(a.taxation_amt, 0), 0)) + sum(if(a.ord_state = '61', ifnull(a.taxation_amt, 0), 0)) as taxation_amt
				-- , (sum(if(a.ord_state = '10', ifnull(a.taxation_amt, 0), 0)) + sum(if(a.ord_state = '60', ifnull(a.taxation_amt, 0), 0)) + sum(if(a.ord_state = '61', ifnull(a.taxation_amt, 0), 0))) / 1.1 as taxation_amt
			from (
				select
					o.ord_opt_no
					, o.store_cd, s.store_nm, s.store_channel, s.store_channel_kind, w.qty, o.price
					, o.prd_cd, pc.brand, o.pr_code
					, (w.qty * w.price ) as sale_amt
					, o.recv_amt
					-- , (w.qty * w.wonga / :vat2) as wonga_amt
				    , (w.qty * w.wonga) as wonga_amt
					-- , (w.qty * (w.price - w.wonga)) as margin_amt
				    -- , (o.recv_amt - (w.qty * w.wonga)) as margin_amt
					, if(w.ord_state = '30', (o.recv_amt - (w.qty * w.wonga)), (o.recv_amt - (w.qty * w.wonga * -1)) * -1) as margin_amt    
				     
					, o.ord_state
				    , w.ord_state as w_ord_state
				    , o.goods_no, o.ord_date, o.sale_kind
					, ifnull(if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt), 0) as sale_kind_amt
					-- , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', w.recv_amt + w.point_apply_amt - w.sales_com_fee, 0)) as taxation_amt
				from order_opt o
					inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
					inner join product_code pc on pc.prd_cd = o.prd_cd
					inner join store s on s.store_cd = o.store_cd
				    inner join goods g on o.goods_no = g.goods_no
					left outer join sale_type st on st.sale_kind = o.sale_kind
				where 
				    w.ord_state in (30,60,61) and o.ord_state = '30' and o.store_cd <> ''
					and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
				    $where
			) a
			    inner join store_channel sc on sc.store_channel_cd = a.store_channel and sc.store_kind_cd = a.store_channel_kind and sc.dep = 2 and sc.use_yn = 'Y'
				left outer join goods g on g.goods_no = a.goods_no
				left outer join brand b on b.br_cd = a.brand
				left outer join code prc on prc.code_kind_cd = 'PR_CODE' and prc.code_id = a.pr_code
			where 1=1 $where2
			group by a.store_cd, a.brand, a.pr_code
			order by a.store_channel, a.store_channel_kind, a.store_cd, a.brand, prc.code_seq
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
