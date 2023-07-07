<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sal21Controller extends Controller
{
	public function index()
	{
        $values = [
            'sdate' => now()->sub(1, 'month')->format('Y-m-d'),
            'edate' => date('Y-m-d'),
            'store_types' => SLib::getStoreTypes(),
            'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
        return view(Config::get('shop.store.view') . '/sale/sal21', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $next_edate = date("Y-m-d", strtotime("+1 day", strtotime($edate)));
        $store_cds = $request->input('store_no', []);
        $close_yn = $request->input('close_yn', 'N');
        $prd_cds = $request->input('prd_cd', '');
        $prd_cd_range_text = $request->input("prd_cd_range", '');
        $ext_term_qty = $request->input('ext_term_qty', ''); // 기간재고 0 제외여부
        $store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

        $where = "";
        $store_where = "";

        if ($prd_cds != '') {
			$prd_cd = explode(',', $prd_cds);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		} else {
			$where .= " and p.prd_cd != ''";
		}
        if ($close_yn == 'N') {
            $where .= " and store.use_yn = 'Y' and (store.edate = '' or store.edate is null or store.edate >= date_format(now(), '%Y%m%d')) ";
        } else if ($close_yn == 'Y') {
            $where .= " and (store.edate != '' and store.edate is not null and store.edate < date_format(now(), '%Y%m%d')) ";
        }
        if ($store_channel != "") $where .= "and store.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and store.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

        // store_where
		foreach($store_cds as $key => $cd) {
			if ($key === 0) {
				$store_where .= "p.store_cd = '$cd'";
			} else {
				$store_where .= " or p.store_cd = '$cd'";
			}
		}
		if (count($store_cds) < 1) {
			$store_where = "1=1";
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

        // having
        $having = "";
        if ($ext_term_qty === 'true') $having .= " having 
        (sum(ifnull(store_in.qty, 0)) != 0) 
        or (sum(ifnull(store_return.qty, 0)) * -1 != 0) 
        or (sum(ifnull(rt_in.qty, 0)) != 0)
        or (sum(ifnull(rt_out.qty, 0)) * -1 != 0)
        or (sum(ifnull(sale.qty, 0)) * -1 != 0)
        ";
        
        // ordreby
        $ord = $request->input('ord', 'desc');
        $ord_field = $request->input('ord_field', 'p.store_cd');
        $orderby = sprintf("order by %s %s", $ord_field, $ord);
        if($ord_field == 'p.prd_cd') $orderby .= ", p.store_cd";

        // pagination
        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $request->input('limit', 500);
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        //전체 데이터 보기(임시)
        if($page_size == -1)    $limit = "";

        $sql = "
            select 
                p.store_cd, 
                store.store_nm,
                st.code_val as store_type_nm,
                p.prd_cd, 
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_sm,
                pc.color,
                pc.size,
                g.goods_no,
                b.brand_nm, 
                g.style_no,
                stat.code_val as sale_stat_cl, 
                g.goods_nm,
                g.goods_nm_eng,
                pc.goods_opt,
                g.goods_sh,
                g.price,
                g.wonga,
                -- 이전재고
                (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty,
                (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.goods_sh as prev_sh,
                (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.price as prev_price,
                (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.wonga as prev_wonga,
                -- 매장입고
                sum(ifnull(store_in.qty, 0)) as store_in_qty,
                sum(ifnull(store_in.qty, 0)) * g.goods_sh as store_in_sh,
                sum(ifnull(store_in.qty, 0)) * g.price as store_in_price,
                sum(ifnull(store_in.qty, 0)) * g.wonga as store_in_wonga,
                -- 매장반품
                sum(ifnull(store_return.qty, 0)) * -1 as store_return_qty,
                sum(ifnull(store_return.qty, 0)) * -1 * g.goods_sh as store_return_sh,
                sum(ifnull(store_return.qty, 0)) * -1 * g.price as store_return_price,
                sum(ifnull(store_return.qty, 0)) * -1 * g.wonga as store_return_wonga,
                -- 이동입고
                sum(ifnull(rt_in.qty, 0)) as rt_in_qty,
                sum(ifnull(rt_in.qty, 0)) * g.goods_sh as rt_in_sh,
                sum(ifnull(rt_in.qty, 0)) * g.price as rt_in_price,
                sum(ifnull(rt_in.qty, 0)) * g.wonga as rt_in_wonga,
                -- 이동출고
                sum(ifnull(rt_out.qty, 0)) * -1 as rt_out_qty,
                sum(ifnull(rt_out.qty, 0)) * -1 * g.goods_sh as rt_out_sh,
                sum(ifnull(rt_out.qty, 0)) * -1 * g.price as rt_out_price,
                sum(ifnull(rt_out.qty, 0)) * -1 * g.wonga as rt_out_wonga,
                -- 매장판매
                sum(ifnull(sale.qty, 0)) * -1 as sale_qty,
                sum(ifnull(sale.qty, 0)) * -1 * g.goods_sh as sale_sh,
                sum(ifnull(sale.qty, 0)) * -1 * g.price as sale_price,
                sum(ifnull(sale.qty, 0)) * -1 * g.wonga as sale_wonga,
                -- loss
                sum(ifnull(loss.qty, 0)) * -1 as loss_qty,
                sum(ifnull(loss.qty, 0)) * -1 * g.goods_sh as loss_sh,
                sum(ifnull(loss.qty, 0)) * -1 * g.price as loss_price,
                sum(ifnull(loss.qty, 0)) * -1 * g.wonga as loss_wonga,
                -- 기간재고
                p.wqty - sum(ifnull(_next.qty, 0)) as term_qty,
                (p.wqty - sum(ifnull(_next.qty, 0))) * g.goods_sh as term_sh,
                (p.wqty - sum(ifnull(_next.qty, 0))) * g.price as term_price,
                (p.wqty - sum(ifnull(_next.qty, 0))) * g.wonga as term_wonga,
                -- 현재재고
                p.wqty as current_qty
            from product_stock_store p
                inner join product_code pc on pc.prd_cd = p.prd_cd
                left outer join goods g on g.goods_no = p.goods_no
                inner join store on store.store_cd = p.store_cd
                inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = store.store_type
                left outer join brand b on b.brand = g.brand
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join (
                    select idx, prd_cd, location_cd, type, qty, stock_state_date
                    from product_stock_hst
                    where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
                ) hst on hst.location_cd = p.store_cd and hst.prd_cd = p.prd_cd
                left outer join product_stock_hst store_in on store_in.idx = hst.idx and store_in.type = '1'
                left outer join product_stock_hst store_return on store_return.idx = hst.idx and store_return.type = '11'
                left outer join product_stock_hst rt_in on rt_in.idx = hst.idx and rt_in.type = '15' and rt_in.qty > 0
                left outer join product_stock_hst rt_out on rt_out.idx = hst.idx and rt_out.type = '15' and rt_out.qty < 0
                left outer join product_stock_hst sale on sale.idx = hst.idx and (sale.type = '2' or sale.type = '5' or sale.type = '6') -- 주문&교환&환불
                left outer join product_stock_hst loss on loss.idx = hst.idx and loss.type = '14'
                left outer join (
                    select idx, prd_cd, location_cd, type, qty, stock_state_date
                    from product_stock_hst
                    where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
                ) _next on _next.location_cd = p.store_cd and _next.prd_cd = p.prd_cd
            where ($store_where)
                $where
            group by p.store_cd, p.prd_cd
            $having
            $orderby
            $limit
        ";
        $rows = DB::select($sql);

        // pagination
        $total = 0;
        $total_data = '';
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select 
                    count(c.prd_cd) as total, 
                    sum(goods_sh) as goods_sh,
                    sum(price) as price,
                    sum(wonga) as wonga,
                    sum(prev_qty) as prev_qty,
                    sum(prev_sh) as prev_sh,
                    sum(prev_price) as prev_price,
                    sum(prev_wonga) as prev_wonga,
                    sum(store_in_qty) as store_in_qty,
                    sum(store_in_sh) as store_in_sh,
                    sum(store_in_price) as store_in_price,
                    sum(store_in_wonga) as store_in_wonga,
                    sum(store_return_qty) as store_return_qty,
                    sum(store_return_sh) as store_return_sh,
                    sum(store_return_price) as store_return_price,
                    sum(store_return_wonga) as store_return_wonga,
                    sum(rt_in_qty) as rt_in_qty,
                    sum(rt_in_sh) as rt_in_sh,
                    sum(rt_in_price) as rt_in_price,
                    sum(rt_in_wonga) as rt_in_wonga,
                    sum(rt_out_qty) as rt_out_qty,
                    sum(rt_out_sh) as rt_out_sh,
                    sum(rt_out_price) as rt_out_price,
                    sum(rt_out_wonga) as rt_out_wonga,
                    sum(sale_qty) as sale_qty,
                    sum(sale_sh) as sale_sh,
                    sum(sale_price) as sale_price,
                    sum(sale_wonga) as sale_wonga,
                    sum(loss_qty) as loss_qty,
                    sum(loss_sh) as loss_sh,
                    sum(loss_price) as loss_price,
                    sum(loss_wonga) as loss_wonga,
                    sum(term_qty) as term_qty,
                    sum(term_sh) as term_sh,
                    sum(term_price) as term_price,
                    sum(term_wonga) as term_wonga
                from (
                    select 
                        p.prd_cd, 
                        g.goods_sh,
                        g.price,
                        g.wonga,
                        -- 이전재고
                        (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty,
                        (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.goods_sh as prev_sh,
                        (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.price as prev_price,
                        (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.wonga as prev_wonga,
                        -- 매장입고
                        sum(ifnull(store_in.qty, 0)) as store_in_qty,
                        sum(ifnull(store_in.qty, 0)) * g.goods_sh as store_in_sh,
                        sum(ifnull(store_in.qty, 0)) * g.price as store_in_price,
                        sum(ifnull(store_in.qty, 0)) * g.wonga as store_in_wonga,
                        -- 매장반품
                        sum(ifnull(store_return.qty, 0)) * -1 as store_return_qty,
                        sum(ifnull(store_return.qty, 0)) * -1 * g.goods_sh as store_return_sh,
                        sum(ifnull(store_return.qty, 0)) * -1 * g.price as store_return_price,
                        sum(ifnull(store_return.qty, 0)) * -1 * g.wonga as store_return_wonga,
                        -- 이동입고
                        sum(ifnull(rt_in.qty, 0)) as rt_in_qty,
                        sum(ifnull(rt_in.qty, 0)) * g.goods_sh as rt_in_sh,
                        sum(ifnull(rt_in.qty, 0)) * g.price as rt_in_price,
                        sum(ifnull(rt_in.qty, 0)) * g.wonga as rt_in_wonga,
                        -- 이동출고
                        sum(ifnull(rt_out.qty, 0)) * -1 as rt_out_qty,
                        sum(ifnull(rt_out.qty, 0)) * -1 * g.goods_sh as rt_out_sh,
                        sum(ifnull(rt_out.qty, 0)) * -1 * g.price as rt_out_price,
                        sum(ifnull(rt_out.qty, 0)) * -1 * g.wonga as rt_out_wonga,
                        -- 매장판매
                        sum(ifnull(sale.qty, 0)) * -1 as sale_qty,
                        sum(ifnull(sale.qty, 0)) * -1 * g.goods_sh as sale_sh,
                        sum(ifnull(sale.qty, 0)) * -1 * g.price as sale_price,
                        sum(ifnull(sale.qty, 0)) * -1 * g.wonga as sale_wonga,
                        -- loss
                        sum(ifnull(loss.qty, 0)) * -1 as loss_qty,
                        sum(ifnull(loss.qty, 0)) * -1 * g.goods_sh as loss_sh,
                        sum(ifnull(loss.qty, 0)) * -1 * g.price as loss_price,
                        sum(ifnull(loss.qty, 0)) * -1 * g.wonga as loss_wonga,
                        -- 기간재고
                        p.wqty - sum(ifnull(_next.qty, 0)) as term_qty,
                        (p.wqty - sum(ifnull(_next.qty, 0))) * g.goods_sh as term_sh,
                        (p.wqty - sum(ifnull(_next.qty, 0))) * g.price as term_price,
                        (p.wqty - sum(ifnull(_next.qty, 0))) * g.wonga as term_wonga,
                        p.wqty as current_qty -- 현재재고
                    from product_stock_store p
                        inner join product_code pc on pc.prd_cd = p.prd_cd
                        left outer join goods g on g.goods_no = p.goods_no
                        inner join store on store.store_cd = p.store_cd
                        left outer join (
                            select idx, prd_cd, location_cd, type, qty, stock_state_date
                            from product_stock_hst
                            where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
                        ) hst on hst.location_cd = p.store_cd and hst.prd_cd = p.prd_cd
                        left outer join product_stock_hst store_in on store_in.idx = hst.idx and store_in.type = '1'
                        left outer join product_stock_hst store_return on store_return.idx = hst.idx and store_return.type = '11'
                        left outer join product_stock_hst rt_in on rt_in.idx = hst.idx and rt_in.type = '15' and rt_in.qty > 0
                        left outer join product_stock_hst rt_out on rt_out.idx = hst.idx and rt_out.type = '15' and rt_out.qty < 0
                        left outer join product_stock_hst sale on sale.idx = hst.idx and (sale.type = '2' or sale.type = '5' or sale.type = '6') -- 주문&교환&환불
                        left outer join product_stock_hst loss on loss.idx = hst.idx and loss.type = '14'
                        left outer join (
                            select idx, prd_cd, location_cd, type, qty, stock_state_date
                            from product_stock_hst
                            where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
                        ) _next on _next.location_cd = p.store_cd and _next.prd_cd = p.prd_cd
                    where ($store_where)
                        $where
                    group by p.store_cd, p.prd_cd
                    $having
                ) as c
            ";

            $row = DB::selectOne($sql);
            $total_data = $row;
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }
        
		return response()->json([
			'code' => 200,
			'head' => [
				'total' => $total,
				'page' => $page,
				'page_cnt' => $page_cnt,
				'page_total' => count($rows),
                'total_data' => $total_data
			],
			'body' => $rows,
		]);
    }
}
