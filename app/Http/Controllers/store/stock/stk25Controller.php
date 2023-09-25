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
                    st.sale_per
                from sale_type_apply
                    inner join sale_type st on st.idx = sale_type_cd
                where apply_date = :apply_date and apply_yn = 'Y'
            ";
            $sale_types = DB::select($sql, ['apply_date' => $sale_month]);
        }

        // 매장별 할인율 조회
        if($store_cd != '') {
            $where .= " and o.store_cd = '$store_cd'";
        }
        if($sale_month != '') {
            $where .= " and ow.ord_state_date >= '$sale_month" . "01' and ow.ord_state_date <= '$sale_month" . "31'";
        }

        $sql = "
            select 
                o.ord_state as ord_state_cd,
                ord_state.code_val as ord_state,
                o.store_cd,
                o.ord_opt_no,
                o.ord_no,
                DATE_FORMAT(ow.ord_state_date, '%Y-%m-%d') as ord_date,
                o.sale_kind,
                c.code_val as sale_kind_nm,
                o.prd_cd,
                o.goods_no,
                op.opt_kind_nm,
                b.brand_nm,
                g.style_no,
                stat.code_val as sale_stat_cl,
                o.goods_nm,
                g.goods_nm_eng,
                o.goods_opt,
                pc.color,
                ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                (ow.qty * if(ow.ord_state = 61, -1, 1)) as qty,
                o.price,
                st.sale_per,
                cast((ow.price * st.sale_per / 100) AS signed integer) * ow.qty as dc_price,
                ow.recv_amt
            from order_opt_wonga ow
                inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
                inner join order_mst om on om.ord_no = o.ord_no
                inner join product_code pc on pc.prd_cd = o.prd_cd
                inner join code c on c.code_kind_cd = 'sale_kind' and c.code_id = o.sale_kind
                inner join goods g on g.goods_no = o.goods_no
                inner join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                inner join brand b on b.brand = g.brand
                inner join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join sale_type st on st.sale_kind = o.sale_kind
                left outer join code ord_state on (o.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
            where 1=1 
                and ow.ord_state = 30
                and (o.clm_state = 90 or o.clm_state = -30 or o.clm_state = 0)
                $where
            order by ow.ord_state_date
        ";
        // -- where (ow.ord_state = 30 or ow.ord_state = 61 or ow.ord_state = 60) $where
        $result = DB::select($sql);

        $sql = "
            select 
                sum(ow.price * ow.qty) as total_sale_amt,
                cast((sum(ow.recv_amt * if(ow.ord_state > 30, -1, 1)) * (ifnull(stas.apply_rate, 0) / 100)) as signed integer) as total_dc_amt,
                sum(cast((ow.price * st.sale_per / 100) AS signed integer) * ow.qty) as dc_price,
                (cast((sum(ow.recv_amt * if(ow.ord_state > 30, -1, 1)) * (ifnull(stas.apply_rate, 0) / 100)) as signed integer) - sum(cast((ow.price * st.sale_per / 100) AS signed integer) * ow.qty)) as left_dc_price,
                stas.apply_rate,
                sum(ow.recv_amt * if(ow.ord_state > 30, -1, 1)) as total_recv_amt -- 판매내역의 실결제금액
            from order_opt_wonga ow
                inner join order_opt o on o.ord_opt_no = ow.ord_opt_no
                inner join order_mst om on om.ord_no = o.ord_no
                inner join product_code pc on pc.prd_cd = o.prd_cd
                inner join code c on c.code_kind_cd = 'sale_kind' and c.code_id = o.sale_kind
                inner join goods g on g.goods_no = o.goods_no
                inner join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                -- inner join brand b on b.brand = g.brand
                inner join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join sale_type st on st.sale_kind = o.sale_kind
                left outer join sale_type_apply_store stas on stas.apply_date = '$sale_month' and stas.store_cd = '$store_cd'
            where 1=1 
                and ow.ord_state in (30, 60, 61)
                and (o.clm_state = 90 or o.clm_state = -30 or o.clm_state = 0)
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
