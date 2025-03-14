<?php

namespace App\Http\Controllers\shop\sale;

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
	public function index(Request $request) {

		// $sdate = Carbon::now()->sub(4, 'week')->format("Y-m-d");
		// $edate = Carbon::now()->format("Y-m-d");

		// $values = [
        //     'sdate'         => $sdate,
		// 	'edate'         => $edate,
		// 	'store_types'	=> SLib::getStoreTypes(), // 매장구분
		// 	'items'			=> SLib::getItems(), // 품목
		// ];
        // return view( Config::get('shop.shop.view') . '/sale/sal08', $values );
		
		/* shop 미사용 메뉴 메인페이지로 리다이렉트 */
        return redirect('/shop');
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
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.`$opt in` ($opt_join) ";
			}
		}
		if ($item != '') $where2 .= " and g.opt_kind_cd = '$item' ";

		$sql = "
			select
				a.store_cd, a.store_nm, a.store_type, st.code_val as store_type_nm
				, a.pr_code, prc.code_val as pr_code_nm
				, a.brand, b.brand_nm
				, sum(a.sale_amt) as sale_amt
				, sum(a.recv_amt) as recv_amt
				, sum(a.wonga_amt) as wonga_amt
				, sum(a.margin_amt) as margin_amt
				, (sum(a.margin_amt) / sum(a.sale_amt) * 100) as margin_rate
			from (
				select
					o.ord_opt_no
					, o.store_cd, s.store_nm, s.store_type
					, o.prd_cd, pc.brand, o.pr_code
					, (w.qty * w.price) as sale_amt
					, w.recv_amt
					, (w.qty * w.wonga) as wonga_amt
					, (w.qty * (w.price - w.wonga)) as margin_amt
					, o.ord_state, o.goods_no, o.ord_date, o.sale_kind
				from order_opt o
					inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
					inner join product_code pc on pc.prd_cd = o.prd_cd
					inner join store s on s.store_cd = o.store_cd
				where w.ord_state in ('30', '60', '61') and o.store_cd <> '' $where
			) a
				inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = a.store_type
				left outer join goods g on g.goods_no = a.goods_no
				left outer join brand b on b.br_cd = a.brand
				left outer join code prc on prc.code_kind_cd = 'PR_CODE' and prc.code_id = a.pr_code
			where 1=1 $where2
			group by a.store_cd, a.brand, a.pr_code
			order by a.store_cd, a.brand, prc.code_seq
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
