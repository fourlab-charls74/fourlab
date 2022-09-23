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
		];
        return view(Config::get('shop.store.view') . '/sale/sal21', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $store_type = $request->input('store_type', '');
        $store_cds = $request->input('store_no', []);
        $close_yn = $request->input('close_yn', 'N');
        $prd_cds = $request->input('prd_cd', '');
        
        $where = "";
        $store_where = "";

        if ($store_type != '') $where .= " and store.store_type = '$store_type' ";
        if ($prd_cds != '') {
			$prd_cd = explode(',', $prd_cds);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or o.prd_cd = '" . Lib::quote($cd) . "' ";
			}
			$where .= ")";
		} else {
			$where .= " and o.prd_cd != ''";
		}
        if ($close_yn == 'N') {
            $where .= " and store.use_yn = 'Y' and (store.edate = '' or store.edate is null or store.edate >= date_format(now(), '%Y%m%d')) ";
        } else if ($close_yn == 'Y') {
            $where .= " and (store.edate != '' and store.edate is not null and store.edate < date_format(now(), '%Y%m%d')) ";
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

        $sql = "
            select 
                o.store_cd,
                (select store_nm from store where store_cd = o.store_cd) as store_nm,
                o.prd_cd,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_sm,
                pc.color,
                pc.size,
                o.goods_no,
                b.brand_nm, 
                g.style_no,
                stat.code_val as sale_stat_cl, 
                g.goods_nm,
                o.goods_opt,
                g.goods_sh,
                g.price,
                g.wonga,
                -- 매장입고
				ifnull(store_in.qty, 0) as store_in_qty,
                (ifnull(store_in.qty, 0) * g.goods_sh) as store_in_sh,
                (ifnull(store_in.qty, 0) * g.price) as store_in_price,
                (ifnull(store_in.qty, 0) * g.wonga) as store_in_wonga,
                -- 매장반품
				ifnull(store_return.qty, 0) as store_return_qty,
                (ifnull(store_return.qty, 0) * g.goods_sh) as store_return_sh,
                (ifnull(store_return.qty, 0) * g.price) as store_return_price,
                (ifnull(store_return.qty, 0) * g.wonga) as store_return_wonga,
                -- 이동입고
				ifnull(rt_in.qty, 0) as rt_in_qty,
                (ifnull(rt_in.qty, 0) * g.goods_sh) as rt_in_sh,
                (ifnull(rt_in.qty, 0) * g.price) as rt_in_price,
                (ifnull(rt_in.qty, 0) * g.wonga) as rt_in_wonga,
                -- 이동출고
				ifnull(rt_out.qty, 0) as rt_out_qty,
                (ifnull(rt_out.qty, 0) * g.goods_sh) as rt_out_sh,
                (ifnull(rt_out.qty, 0) * g.price) as rt_out_price,
                (ifnull(rt_out.qty, 0) * g.wonga) as rt_out_wonga,
                -- 매장판매
				sum(ifnull(o.qty, 0)) as store_sale_qty,
                (sum(ifnull(o.qty, 0)) * g.goods_sh) as store_sale_sh,
                (sum(ifnull(o.qty, 0)) * g.price) as store_sale_price,
                (sum(ifnull(o.qty, 0)) * g.wonga) as store_sale_wonga,
                -- LOSS
				ifnull(loss.qty, 0) as loss_qty,
                (ifnull(loss.qty, 0) * g.goods_sh) as loss_sh,
                (ifnull(loss.qty, 0) * g.price) as loss_price,
                (ifnull(loss.qty, 0) * g.wonga) as loss_wonga
            from order_opt o
                inner join product_code pc on pc.prd_cd = o.prd_cd
                inner join store on store.store_cd = o.store_cd
                inner join goods g on g.goods_no = o.goods_no
                left outer join brand b on b.brand = g.brand
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join (
                    select prd_cd, location_cd, sum(qty) as qty from product_stock_hst 
                    where type = '1' and location_type = 'STORE'
                        and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
                    group by location_cd, prd_cd
                ) store_in on store_in.location_cd = o.store_cd and store_in.prd_cd = o.prd_cd
                left outer join (
					select prd_cd, location_cd, sum(qty) as qty from product_stock_hst 
					where type = '11' and location_type = 'STORE'
						and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '2022-09-20 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '2022-09-23 23:59:59'
					group by location_cd, prd_cd
				) store_return on store_return.location_cd = o.store_cd and store_return.prd_cd = o.prd_cd
                left outer join (
					select prd_cd, location_cd, sum(qty) as qty from product_stock_hst 
					where type = '15' and location_type = 'STORE' and qty > 0
						and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '2022-09-20 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '2022-09-23 23:59:59'
					group by location_cd, prd_cd
				) rt_in on rt_in.location_cd = o.store_cd and rt_in.prd_cd = o.prd_cd
				left outer join (
					select prd_cd, location_cd, sum(qty) as qty from product_stock_hst 
					where type = '15' and location_type = 'STORE' and qty < 0
						and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '2022-09-20 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '2022-09-23 23:59:59'
					group by location_cd, prd_cd
				) rt_out on rt_out.location_cd = o.store_cd and rt_out.prd_cd = o.prd_cd
                left outer join (
                    select prd_cd, location_cd, sum(qty) as qty from product_stock_hst 
                    where type = '14' and location_type = 'STORE'
                        and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
                    group by location_cd, prd_cd
                ) loss on loss.location_cd = o.store_cd and loss.prd_cd = o.prd_cd
            where 1=1
                and o.ord_state = 30
                and (o.clm_state = 90 or o.clm_state = -30 or o.clm_state = 0)
                and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'
                and ($store_where)
                $where
            group by o.store_cd, o.prd_cd
            order by o.store_cd
        ";
        $rows = DB::select($sql);
        
		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($rows),
                'page' => 1,
			],
			'body' => $rows,
		]);
    }
}
