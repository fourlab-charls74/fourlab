<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class stk25Controller extends Controller
{
	public function index(Request $request)
	{
        $sdate = $request->input("sale_month", Carbon::now()->format('Y-m'));
        $store_cd = $request->input("store_cd", '');
        $store = ['store_cd' => '', 'store_nm' => ''];

        if($store_cd != '') {
            $store = DB::table('store')->where('store_cd', '=', $store_cd)->select('store_cd', 'store_nm')->first();
        }

        $sql = "
            select 
                sale_type_cd, 
                st.sale_type_nm, 
                st.sale_apply, 
                st.amt_kind,
                st.sale_amt, 
                st.sale_per
            from sale_type_apply
                inner join sale_type st on st.idx = sale_type_cd
            where apply_date = :apply_date and apply_yn = 'Y'
        ";
        $sale_types = DB::select($sql, ['apply_date' => $sdate]);

		$values = [
            'store' => $store,
			'sdate' => $sdate,
            'sale_types' => $sale_types,
		];
        return view(Config::get('shop.store.view') . '/stock/stk25', $values);
	}

	public function search(Request $request)
	{	
        $store_cd = $request->input("store_no", "");
		$sale_month = str_replace('-', '', $request->input('sdate', ''));
        $sale_types = [];
        $where = "";

        // 할인적용기간별 적용된 할인적용구분 조회
        if($sale_month != '') {
            $sql = "
                select 
                    sale_type_cd, 
                    st.sale_type_nm, 
                    st.sale_apply, 
                    st.amt_kind,
                    st.sale_amt, 
                    st.sale_per,
                    st.sale_kind
                from sale_type_apply
                    inner join sale_type st on st.idx = sale_type_cd
                where apply_date = :apply_date and apply_yn = 'Y'
            ";
            $sale_types = DB::select($sql, ['apply_date' => $sale_month]);
        }
		
		$sale_kinds = array_column($sale_types, 'sale_kind');
		$sale_kind = implode(',', $sale_kinds);
		
		$dc_search = "";
		if ($sale_kind != "") {
			$dc_search = "sum(case when c.code_id in ($sale_kind) then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end)";
		} else {
			$dc_search = "'0'";
		}

        // 매장별 할인율 조회
        if($store_cd != '') {
            $where .= " and o.store_cd = '$store_cd'";
        }
        if($sale_month != '') {
            $where .= " and w.ord_state_date >= '$sale_month" . "01' and w.ord_state_date <= '$sale_month" . "31'";
        }

		$sql = "
            select
                a.ord_no,
                a.ord_opt_no,
                DATE_FORMAT(a.ord_state_date, '%Y-%m-%d') as ord_date,
                a.ord_state as ord_state_cd,
                a.ord_state,
                clm_state.code_val as clm_state,
                a.prd_cd,
			 	a.goods_no,	
                a.opt_kind_nm,
                a.brand_nm,
                a.style_no,
                a.goods_nm,
                a.goods_nm_eng,
                a.prd_cd_p,
                a.color,
                a.size,
                a.goods_opt,
			 	if(a.ord_state > 30, a.qty * -1, a.qty) as qty,
                (a.price * a.qty) as price,
                a.recv_amt,
            	(if(a.ord_state > 30, a.qty * -1, a.qty) * (a.price - a.sale_kind_amt)) * if(a.ord_state > 30, -1, 1) as ord_amt,
                a.sale_per,
                a.dc_price,
                sale_kind.code_val as sale_kind_nm
            from (
                select
                    om.ord_no,
                    o.ord_opt_no,
                    o.ord_state as opt_ord_state,
                    w.ord_state_date,
                    o.clm_state,
                    o.prd_cd,
                    g.goods_no,
                    g.style_no,
                    g.goods_nm_eng,
                    o.goods_nm,
                    o.goods_opt,
                    pc.color,
                    pc.size,
                    pc.prd_cd_p,
                    w.qty,
                    o.price,
                    (o.recv_amt * if(w.ord_state > 30, -1, 1)) as recv_amt,
                    st.sale_per,
                    o.sale_kind,
                    b.brand_nm,
                    ord_state.code_val as ord_state,
                    d.code_val as opt_kind_nm,
                    ifnull(if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt), 0) as sale_kind_amt,
                    (o.recv_amt * if(w.ord_state > 30, -1, 1)) as dc_price
                from order_opt_wonga w
                    inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                    inner join order_mst om on o.ord_no = om.ord_no
                    left outer join product_code pc on pc.prd_cd = o.prd_cd
                    inner join code c on c.code_kind_cd = 'sale_kind' and c.code_id = o.sale_kind
                    inner join goods g on o.goods_no = g.goods_no
					left outer join brand b on b.brand = g.brand
                    inner join code d on d.code_id = pc.item and d.code_kind_cd = 'prd_cd_item'
                    left outer join sale_type st on st.sale_kind = ifnull(o.sale_kind,'00')
					left outer join code ord_state on (o.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
                	left outer join sale_type_apply_store stas on stas.apply_date = '$sale_month' and stas.store_cd = '$store_cd'
                where 
                    w.ord_state in (30,60,61)  and o.ord_state = '30'
					and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
                    $where
            ) a
                left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
                left outer join code sale_kind on (sale_kind.code_id = a.sale_kind and sale_kind.code_kind_cd = 'SALE_KIND')
        ";
        $result = DB::select($sql);

		$sql = "
				select
					-- sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as total_recv_amt
	                sum(if(w.ord_state > 30, (o.recv_amt + w.point_apply_amt) * -1, (o.recv_amt + w.point_apply_amt))) as total_recv_amt
					, cast((sum(if(w.ord_state > 30, (o.recv_amt + w.point_apply_amt) * -1, (o.recv_amt + w.point_apply_amt))) * (ifnull(stas.apply_rate, 0) / 100)) as signed integer) as total_dc_amt
				    , stas.apply_rate
                    , $dc_search as dc_price
					, cast((sum(if(w.ord_state > 30, (o.recv_amt + w.point_apply_amt) * -1, (o.recv_amt + w.point_apply_amt))) * (ifnull(stas.apply_rate, 0) / 100)) as signed integer) - $dc_search as left_dc_price
                from order_opt_wonga w
                    inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                    inner join order_mst om on o.ord_no = om.ord_no
                    left outer join product_code pc on pc.prd_cd = o.prd_cd
                    inner join code c on c.code_kind_cd = 'sale_kind' and c.code_id = o.sale_kind
                    inner join goods g on o.goods_no = g.goods_no
					-- left outer join brand b on b.brand = g.brand
					-- inner join code d on d.code_id = pc.item and d.code_kind_cd = 'prd_cd_item'
                    -- left outer join sale_type st on st.sale_kind = ifnull(o.sale_kind,'00')
					-- left outer join code ord_state on (o.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
                	left outer join sale_type_apply_store stas on stas.apply_date = '$sale_month' and stas.store_cd = '$store_cd'
                where 
                    w.ord_state in (30,60,61)  and o.ord_state = '30'
					and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
                    $where
		";
		
        $row = DB::selectOne($sql);
		
		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($result),
                'sale_types' => $sale_types,
                'amts' => $row,
			],
			'body' => $result
		]);
	}
}
