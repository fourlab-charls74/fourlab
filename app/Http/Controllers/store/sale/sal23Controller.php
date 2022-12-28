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

class sal23Controller extends Controller
{
	public function index()
	{
        $values = [
            'sdate' => now()->sub(1, 'month')->format('Y-m-d'),
            'edate' => date('Y-m-d'),
		];
        return view(Config::get('shop.store.view') . '/sale/sal23', $values);
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date('Y-m-d'));
        $next_edate = date("Y-m-d", strtotime("+1 day", strtotime($edate)));
		$prd_cd	= $request->input('prd_cd', ''); // 상품코드
		$prd_cd_range_text = $request->input('prd_cd_range', ''); // 상품옵션범위

        /** 검색조건 필터링 */
        $where = "";

        // 상품코드 검색
        if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '$cd%' ";
			}
			$where .= ") ";
		}

		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
			}
		}

        /** 데이터 정렬 */
        $ord = $request->input('ord', 'desc');
        $ord_field = $request->input('ord_field', 'pc.rt');
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        /** 페이징처리 */
        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $request->input('limit', 500);
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sql = "
            select
                ps.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , p.prd_nm as goods_nm
                , p.prd_nm_eng as goods_nm_eng
                , pc.goods_no
                , pc.brand
                , pc.color
                , pc.size
                , pc.goods_opt
                , p.tag_price
                , p.price
                , p.wonga
                -- 이전재고
                , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty
                , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * p.tag_price as prev_tag_price
                , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * p.price as prev_price
                , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * p.wonga as prev_wonga
                -- 기간입고
                , sum(ifnull(stock_in.qty, 0)) as stock_in_qty
                , sum(ifnull(stock_in.qty, 0)) * p.tag_price as stock_in_tag_price
                , sum(ifnull(stock_in.qty, 0)) * p.price as stock_in_price
                , sum(ifnull(stock_in.qty, 0)) * p.wonga as stock_in_wonga
                -- 기간반품
                , sum(ifnull(stock_return.qty, 0)) * -1 as stock_return_qty
                , sum(ifnull(stock_return.qty, 0)) * p.tag_price * -1 as stock_return_tag_price
                , sum(ifnull(stock_return.qty, 0)) * p.price * -1 as stock_return_price
                , sum(ifnull(stock_return.qty, 0)) * p.wonga * -1 as stock_return_wonga
                -- loss
                , 0 as loss_qty
                , 0 as loss_tag_price
                , 0 as loss_price
                , 0 as loss_wonga
                -- 기간재고
                , (ps.qty - sum(ifnull(_next.qty, 0))) as term_qty
                , (ps.qty - sum(ifnull(_next.qty, 0))) * p.tag_price as term_tag_price
                , (ps.qty - sum(ifnull(_next.qty, 0))) * p.price as term_price
                , (ps.qty - sum(ifnull(_next.qty, 0))) * p.wonga as term_wonga
            from product_stock ps
                inner join product_code pc on pc.prd_cd = ps.prd_cd
                inner join product p on p.prd_cd = ps.prd_cd
                left outer join (		
                    select idx, qty, prd_cd
                    from product_stock_hst
                    where location_type = 'STORAGE'
                        and type not in (11,15,16,17)
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') >= '$sdate 00:00:00' 
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') <= '$edate 23:59:59'
                ) hst on hst.prd_cd = ps.prd_cd
                left outer join product_stock_hst stock_in on stock_in.idx = hst.idx and stock_in.type = 1
                left outer join product_stock_hst stock_return on stock_return.idx = hst.idx and stock_return.type = 9
                left outer join (
                    select idx, qty, prd_cd
                    from product_stock_hst
                    where location_type = 'STORAGE'
                        and type not in (11,15,16,17)
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') >= '$next_edate 00:00:00' 
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') <= now()
                ) _next on _next.prd_cd = ps.prd_cd
            where 1=1 $where
            group by ps.prd_cd
            $orderby
            $limit
        ";
        $rows = DB::select($sql);

        foreach ($rows as $row) {
            $sale_query = "
                select
                    sum(w.qty) * -1 as sale_qty
                    , sum(w.qty) * $row->tag_price * -1 as sale_tag_price
                    , sum(w.qty) * $row->price * -1 as sale_price
                    , sum(w.qty) * $row->wonga * -1 as sale_wonga
                from order_opt_wonga w
                    inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                where w.ord_state in (30,60,61) and o.prd_cd = '$row->prd_cd'
            ";
            $sale = DB::selectOne($sale_query);
            $row->sale_qty = $sale->sale_qty;
            $row->sale_tag_price = $sale->sale_tag_price;
            $row->sale_price = $sale->sale_price;
            $row->sale_wonga = $sale->sale_wonga;
        }
        
        // pagination
        $total = 0;
        $total_data = '';
        $page_cnt = 0;
        if($page == 1) {
            $sql = "	
                select 
                    count(a.prd_cd) as total
                    , sum(prev_qty) as prev_qty
                    , sum(prev_tag_price) as prev_tag_price
                    , sum(prev_price) as prev_price
                    , sum(prev_wonga) as prev_wonga
                    , sum(stock_in_qty) as stock_in_qty
                    , sum(stock_in_tag_price) as stock_in_tag_price
                    , sum(stock_in_price) as stock_in_price
                    , sum(stock_in_wonga) as stock_in_wonga
                    , sum(stock_return_qty) as stock_return_qty
                    , sum(stock_return_tag_price) as stock_return_tag_price
                    , sum(stock_return_price) as stock_return_price
                    , sum(stock_return_wonga) as stock_return_wonga
                    , sum(loss_qty) as loss_qty
                    , sum(loss_tag_price) as loss_tag_price
                    , sum(loss_price) as loss_price
                    , sum(loss_wonga) as loss_wonga
                    , sum(term_qty) as term_qty
                    , sum(term_tag_price) as term_tag_price
                    , sum(term_price) as term_price
                    , sum(term_wonga) as term_wonga
                from (
                    select
                        ps.prd_cd
                        , p.tag_price
                        , p.price
                        , p.wonga
                        -- 이전재고
                        , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty
                        , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * p.tag_price as prev_tag_price
                        , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * p.price as prev_price
                        , (ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) * p.wonga as prev_wonga
                        -- 기간입고
                        , sum(ifnull(stock_in.qty, 0)) as stock_in_qty
                        , sum(ifnull(stock_in.qty, 0)) * p.tag_price as stock_in_tag_price
                        , sum(ifnull(stock_in.qty, 0)) * p.price as stock_in_price
                        , sum(ifnull(stock_in.qty, 0)) * p.wonga as stock_in_wonga
                        -- 기간반품
                        , sum(ifnull(stock_return.qty, 0)) * -1 as stock_return_qty
                        , sum(ifnull(stock_return.qty, 0)) * p.tag_price * -1 as stock_return_tag_price
                        , sum(ifnull(stock_return.qty, 0)) * p.price * -1 as stock_return_price
                        , sum(ifnull(stock_return.qty, 0)) * p.wonga * -1 as stock_return_wonga
                        -- loss
                        , 0 as loss_qty
                        , 0 as loss_tag_price
                        , 0 as loss_price
                        , 0 as loss_wonga
                        -- 기간재고
                        , (ps.qty - sum(ifnull(_next.qty, 0))) as term_qty
                        , (ps.qty - sum(ifnull(_next.qty, 0))) * p.tag_price as term_tag_price
                        , (ps.qty - sum(ifnull(_next.qty, 0))) * p.price as term_price
                        , (ps.qty - sum(ifnull(_next.qty, 0))) * p.wonga as term_wonga
                    from product_stock ps
                        inner join product_code pc on pc.prd_cd = ps.prd_cd
                        inner join product p on p.prd_cd = ps.prd_cd
                        left outer join (		
                            select idx, qty, prd_cd
                            from product_stock_hst
                            where location_type = 'STORAGE' 
                                and type not in (11,15,16,17)
                                and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') >= '$sdate 00:00:00' 
                                and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') <= '$edate 23:59:59'
                        ) hst on hst.prd_cd = ps.prd_cd
                        left outer join product_stock_hst stock_in on stock_in.idx = hst.idx and stock_in.type = 1
                        left outer join product_stock_hst stock_return on stock_return.idx = hst.idx and stock_return.type = 9
                        left outer join (
                            select idx, qty, prd_cd
                            from product_stock_hst
                            where location_type = 'STORAGE'
                                and type not in (11,15,16,17)
                                and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') >= '$next_edate 00:00:00' 
                                and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') <= now()
                        ) _next on _next.prd_cd = ps.prd_cd
                    where 1=1 $where
                    group by ps.prd_cd
                ) a
            ";
            $row = DB::selectOne($sql);

            $sale_query = "
                select
                    sum(w.qty) * -1 as sale_qty
                    , sum(w.qty) * -1 * p.tag_price as sale_tag_price
                    , sum(w.qty) * -1 * p.price as sale_price
                    , sum(w.qty) * -1 * p.wonga as sale_wonga
                from order_opt_wonga w
                    inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                    inner join product_code pc on pc.prd_cd = o.prd_cd
                    inner join product p on p.prd_cd = o.prd_cd
                where w.ord_state in (30,60,61) $where
            ";
            $sale = DB::selectOne($sale_query);
            $row->sale_qty = $sale->sale_qty;
            $row->sale_tag_price = $sale->sale_tag_price;
            $row->sale_price = $sale->sale_price;
            $row->sale_wonga = $sale->sale_wonga;

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
