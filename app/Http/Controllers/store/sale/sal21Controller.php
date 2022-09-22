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
                ifnull(ps.qty, 0) as store_qty, 
                ifnull(ps.wqty, 0) as store_wqty,
                sum(ifnull(o.qty, 0)) as sale_cnt
            from order_opt o
                inner join product_code pc on pc.prd_cd = o.prd_cd
                inner join product_stock_storage pss on pss.prd_cd = o.prd_cd and pss.storage_cd = (select storage_cd from storage where default_yn = 'Y')
                inner join product_stock_store ps on ps.prd_cd = o.prd_cd and ps.store_cd = o.store_cd
                inner join store on store.store_cd = o.store_cd
                left outer join goods g on g.goods_no = o.goods_no
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
            where 1=1
                and o.ord_state = 30
                and (o.clm_state = 90 or o.clm_state = -30 or o.clm_state = 0)
                and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59'
                and ($store_where)
                and store.store_type = '08'
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
