<?php

namespace App\Http\Controllers\shop\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sal22Controller extends Controller
{
    public function index()
	{

        // $sql = "select * from storage";
        // $storage = DB::select($sql);

        // $values = [
        //     'sdate' => now()->sub(1, 'month')->format('Y-m-d'),
        //     'edate' => date('Y-m-d'),
        //     'storage' => $storage
		// ];
        // return view(Config::get('shop.shop.view') . '/sale/sal22', $values);

        /* shop 미사용 메뉴 메인페이지로 리다이렉트 */
        return redirect('/shop');
	}


    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $next_edate = date("Y-m-d", strtotime("+1 day", strtotime($edate)));
        $storage_type = $request->input('storage_type', '');
        $store_cds = $request->input('store_no', []);
        $prd_cds = $request->input('prd_cd', '');
        $prd_cd_range_text = $request->input("prd_cd_range", '');

        $where = "";
        $store_where = "";

        if ($storage_type != '') $where .= " and storage.storage_cd = '$storage_type' ";
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
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
			}
		}

        // ordreby
        $ord = $request->input('ord', 'desc');
        $ord_field = $request->input('ord_field', 'p.storage_cd');
        $orderby = sprintf("order by %s %s", $ord_field, $ord);
        if($ord_field == 'p.prd_cd') $orderby .= ", p.storage_cd";

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
                p.storage_cd
                , storage.storage_nm
                , p.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.color
                , pc.size
                , pc.goods_no
                , b.brand_nm 
                , g.style_no
                , pd.prd_nm
                , g.goods_nm_eng
                , pc.goods_opt
                , g.goods_sh
                , g.price
                , g.wonga

                -- 이전재고
                , (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty
                , (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.goods_sh as prev_sh
                , (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.price as prev_price
                , (p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * g.wonga as prev_wonga

                -- 생산입고
                , sum(ifnull(storage_in.qty, 0)) as storage_in_qty
                , sum(ifnull(storage_in.qty, 0)) * g.goods_sh as storage_in_sh
                , sum(ifnull(storage_in.qty, 0)) * g.price as storage_in_price
                , sum(ifnull(storage_in.qty, 0)) * g.wonga as storage_in_wonga

                -- 생산반품
                , sum(ifnull(storage_return.qty, 0)) * -1 as storage_return_qty
                , sum(ifnull(storage_return.qty, 0)) * -1 * g.goods_sh as storage_return_sh
                , sum(ifnull(storage_return.qty, 0)) * -1 * g.price as storage_return_price
                , sum(ifnull(storage_return.qty, 0)) * -1 * g.wonga as storage_return_wonga

                -- 이동입고
                , sum(ifnull(rt_in.qty, 0)) as rt_in_qty
                , sum(ifnull(rt_in.qty, 0)) * g.goods_sh as rt_in_sh
                , sum(ifnull(rt_in.qty, 0)) * g.price as rt_in_price
                , sum(ifnull(rt_in.qty, 0)) * g.wonga as rt_in_wonga

                -- 이동출고
                , sum(ifnull(rt_out.qty, 0)) * -1 as rt_out_qty
                , sum(ifnull(rt_out.qty, 0)) * -1 * g.goods_sh as rt_out_sh
                , sum(ifnull(rt_out.qty, 0)) * -1 * g.price as rt_out_price
                , sum(ifnull(rt_out.qty, 0)) * -1 * g.wonga as rt_out_wonga

                -- 매장출고
                , sum(ifnull(store_out.qty, 0)) * -1 as store_out_qty
                , sum(ifnull(store_out.qty, 0)) * -1 * g.goods_sh as store_out_sh
                , sum(ifnull(store_out.qty, 0)) * -1 * g.price as store_out_price
                , sum(ifnull(store_out.qty, 0)) * -1 * g.wonga as store_out_wonga
                
                -- 매장반품
                , sum(ifnull(store_return.qty, 0)) as store_return_qty
                , sum(ifnull(store_return.qty, 0)) * g.goods_sh as store_return_sh
                , sum(ifnull(store_return.qty, 0)) * g.price as store_return_price
                , sum(ifnull(store_return.qty, 0)) * g.wonga as store_return_wonga
                
                -- LOSS
                , sum(ifnull(loss.qty, 0)) * -1 as loss_qty
                , sum(ifnull(loss.qty, 0)) * -1 * g.goods_sh as loss_sh
                , sum(ifnull(loss.qty, 0)) * -1 * g.price as loss_price
                , sum(ifnull(loss.qty, 0)) * -1 * g.wonga as loss_wonga
                
                -- 기간재고
                , p.wqty - sum(ifnull(_next.qty, 0)) as term_qty
                , (p.wqty - sum(ifnull(_next.qty, 0))) * g.goods_sh as term_sh
                , (p.wqty - sum(ifnull(_next.qty, 0))) * g.price as term_price
                , (p.wqty - sum(ifnull(_next.qty, 0))) * g.wonga as term_wonga
                
            from product_stock_storage p
                inner join product_code pc on pc.prd_cd = p.prd_cd
                left outer join product pd on pd.prd_cd = p.prd_cd
                left outer join goods g on g.goods_no = p.goods_no
                inner join storage storage on storage.storage_cd = p.storage_cd
                left outer join brand b on b.br_cd = pc.brand
                left outer join (
                    select idx, prd_cd, location_cd, type, qty, stock_state_date
                    from product_stock_hst
                    where location_type = 'STORAGE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
                ) hst on hst.location_cd = p.storage_cd and hst.prd_cd = p.prd_cd
                left outer join product_stock_hst storage_in on storage_in.idx = hst.idx and storage_in.type = '1' -- 상품입고
                left outer join product_stock_hst storage_return on storage_return.idx = hst.idx and storage_return.type = '9' -- 상품반품
                left outer join product_stock_hst rt_in on rt_in.idx = hst.idx and rt_in.type = '16' and rt_in.qty > 0 -- 이동입고
                left outer join product_stock_hst rt_out on rt_out.idx = hst.idx and rt_out.type = '16' and rt_out.qty < 0 -- 이동출고
                left outer join product_stock_hst store_out on store_out.idx = hst.idx and store_out.type = '17' -- 매장출고
                left outer join product_stock_hst store_return on store_return.idx = hst.idx and store_return.type = '11' -- 매장반품
                left outer join product_stock_hst loss on loss.idx = hst.idx and loss.type = '14' -- LOSS
                left outer join (
                    select idx, prd_cd, location_cd, type, qty, stock_state_date
                    from product_stock_hst
                    where location_type = 'STORAGE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
                ) _next on _next.location_cd = p.storage_cd and _next.prd_cd = p.prd_cd
            where ($store_where)
                $where
            group by p.storage_cd, p.prd_cd
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
                    
                    sum(storage_in_qty) as storage_in_qty,
                    sum(storage_in_sh) as storage_in_sh,
                    sum(storage_in_price) as storage_in_price,
                    sum(storage_in_wonga) as storage_in_wonga,
                    
                    sum(storage_return_qty) as storage_return_qty,
                    sum(storage_return_sh) as storage_return_sh,
                    sum(storage_return_price) as storage_return_price,
                    sum(storage_return_wonga) as storage_return_wonga,
                    
                    sum(rt_in_qty) as rt_in_qty,
                    sum(rt_in_sh) as rt_in_sh,
                    sum(rt_in_price) as rt_in_price,
                    sum(rt_in_wonga) as rt_in_wonga,

                    sum(rt_out_qty) as rt_out_qty,
                    sum(rt_out_sh) as rt_out_sh,
                    sum(rt_out_price) as rt_out_price,
                    sum(rt_out_wonga) as rt_out_wonga,
                    
                    sum(store_out_qty) as store_out_qty,
                    sum(store_out_sh) as store_out_sh,
                    sum(store_out_price) as store_out_price,
                    sum(store_out_wonga) as store_out_wonga,
                    
                    sum(store_return_qty) as store_return_qty,
                    sum(store_return_sh) as store_return_sh,
                    sum(store_return_price) as store_return_price,
                    sum(store_return_wonga) as store_return_wonga,

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

                        -- 생산입고
                        ifnull(storage_in.qty, 0) as storage_in_qty,
                        ifnull(storage_in.qty, 0) * g.goods_sh as storage_in_sh,
                        ifnull(storage_in.qty, 0) * g.price as storage_in_price,
                        ifnull(storage_in.qty, 0) * g.wonga as storage_in_wonga,
                        
                        -- 생산반품
                        sum(ifnull(storage_return.qty, 0)) * -1 as storage_return_qty,
                        sum(ifnull(storage_return.qty, 0)) * -1 * g.goods_sh as storage_return_sh,
                        sum(ifnull(storage_return.qty, 0)) * -1 * g.price as storage_return_price,
                        sum(ifnull(storage_return.qty, 0)) * -1 * g.wonga as storage_return_wonga,
                        
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
                        
                        -- 매장출고
                        sum(ifnull(store_out.qty, 0)) * -1 as store_out_qty,
                        sum(ifnull(store_out.qty, 0)) * -1 * g.goods_sh as store_out_sh,
                        sum(ifnull(store_out.qty, 0)) * -1 * g.price as store_out_price,
                        sum(ifnull(store_out.qty, 0)) * -1 * g.wonga as store_out_wonga,
                        
                        -- 매장반품
                        sum(ifnull(store_return.qty, 0)) as store_return_qty,
                        sum(ifnull(store_return.qty, 0)) * g.goods_sh as store_return_sh,
                        sum(ifnull(store_return.qty, 0)) * g.price as store_return_price,
                        sum(ifnull(store_return.qty, 0)) * g.wonga as store_return_wonga,
                        
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
                    from product_stock_storage p
                        inner join product_code pc on pc.prd_cd = p.prd_cd
                        left outer join product pd on pd.prd_cd = p.prd_cd
                        left outer join goods g on g.goods_no = p.goods_no
                        inner join storage storage on storage.storage_cd = p.storage_cd
                        left outer join brand b on b.br_cd = pc.brand
                        left outer join (
                            select idx, prd_cd, location_cd, type, qty, stock_state_date
                            from product_stock_hst
                            where location_type = 'STORAGE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
                        ) hst on hst.location_cd = p.storage_cd and hst.prd_cd = p.prd_cd
                        left outer join product_stock_hst storage_in on storage_in.idx = hst.idx and storage_in.type = '1' -- 상품입고
                        left outer join product_stock_hst storage_return on storage_return.idx = hst.idx and storage_return.type = '9' -- 상품반품
                        left outer join product_stock_hst rt_in on rt_in.idx = hst.idx and rt_in.type = '16' and rt_in.qty > 0 -- 이동입고
                        left outer join product_stock_hst rt_out on rt_out.idx = hst.idx and rt_out.type = '16' and rt_out.qty < 0 -- 이동출고
                        left outer join product_stock_hst store_out on store_out.idx = hst.idx and store_out.type = '17' -- 매장출고
                        left outer join product_stock_hst store_return on store_return.idx = hst.idx and store_return.type = '11' -- 매장반품
                        left outer join product_stock_hst loss on loss.idx = hst.idx and loss.type = '14' -- LOSS
                        left outer join (
                            select idx, prd_cd, location_cd, type, qty, stock_state_date
                            from product_stock_hst
                            where location_type = 'STORAGE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
                        ) _next on _next.location_cd = p.storage_cd and _next.prd_cd = p.prd_cd
                    where ($store_where)
                        $where
                    group by p.storage_cd, p.prd_cd
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