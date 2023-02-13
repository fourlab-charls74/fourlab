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

class sal23Controller extends Controller
{
	public function index()
	{
        // $values = [
        //     'sdate' => now()->sub(1, 'month')->format('Y-m-d'),
        //     'edate' => date('Y-m-d'),
		// ];
        // return view(Config::get('shop.shop.view') . '/sale/sal23', $values);

        /* shop 미사용 메뉴 메인페이지로 리다이렉트 */
        return redirect('/shop');
	}

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Ymd'));
        $edate = $request->input('edate', date('Ymd'));
        $sdate = str_replace('-', '', $sdate);
        $edate = str_replace('-', '', $edate);
        $next_edate = date("Ymd", strtotime("+1 day", strtotime($edate)));
        $now_date = date('Ymd');
		$prd_cd	= $request->input('prd_cd', ''); // 상품코드
		$prd_cd_range_text = $request->input('prd_cd_range', ''); // 상품옵션범위
        $ext_current_qty = $request->input('ext_current_qty', ''); // 현재재고 0 제외여부

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

        if ($ext_current_qty == 'true') $where .= " and ps.qty > 0 ";

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
                -- 기간입고
                , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) as stock_in_qty
                , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) * p.tag_price as stock_in_tag_price
                , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) * p.price as stock_in_price
                , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) * p.wonga as stock_in_wonga
                -- 기간반품
                , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) as stock_return_qty
                , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) * p.tag_price as stock_return_tag_price
                , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) * p.price as stock_return_price
                , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) * p.wonga as stock_return_wonga
                -- loss
                , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) as loss_qty
                , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) * p.tag_price as loss_tag_price
                , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) * p.price as loss_price
                , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) * p.wonga as loss_wonga
                -- 기간재고
                , (ps.qty 
                    - sum(if(_next.type = 1, ifnull(_next.qty, 0), 0)) 
                    - sum(if(_next.type = 9, ifnull(_next.qty, 0), 0)) 
                    - sum(if(_next.type = 14, ifnull(_next.qty, 0), 0))
                ) as term_qty
            from product_stock ps
                inner join product_code pc on pc.prd_cd = ps.prd_cd
                inner join product p on p.prd_cd = ps.prd_cd
                left outer join (		
                    select idx, qty, prd_cd, location_type, type
                    from product_stock_hst
                    where ((type in (1,9) and location_type = 'STORAGE') or (type = 14 and location_type = 'STORE'))
                        and stock_state_date >= '$sdate'
                        and stock_state_date <= '$edate'
                ) hst on hst.prd_cd = ps.prd_cd
                left outer join (
                    select idx, qty, prd_cd, location_type, type
                    from product_stock_hst
                    where ((type in (1,9) and location_type = 'STORAGE') or (type = 14 and location_type = 'STORE'))
                        and stock_state_date >= '$next_edate'
                        and stock_state_date <= '$now_date'
                ) _next on _next.prd_cd = ps.prd_cd
            where 1=1 $where
            group by ps.prd_cd
            $orderby
            $limit
        ";
        $rows = DB::select($sql);

        $sale_query = "
            select
                w.prd_cd
                , ifnull(sum(w.qty * if(w.ord_state = 30, -1, 1)), 0) as sale_qty
            from order_opt_wonga w
                inner join product_stock ps on ps.prd_cd = w.prd_cd
                inner join product p on p.prd_cd = w.prd_cd
                inner join product_code pc on pc.prd_cd = w.prd_cd
            where w.ord_state in (30,60,61) 
                and w.ord_state_date >= '$sdate'
                and w.ord_state_date <= '$edate'
                $where   
            group by w.prd_cd
            $orderby
        ";
        $sale = DB::select($sale_query);

        $next_sale_query = "
            select
                w.prd_cd
                , ifnull(sum(w.qty * if(w.ord_state = 30, 1, -1)), 0) as sale_qty
            from order_opt_wonga w
                inner join product_stock ps on ps.prd_cd = w.prd_cd
                inner join product p on p.prd_cd = w.prd_cd
                inner join product_code pc on pc.prd_cd = w.prd_cd
            where w.ord_state in (30,60,61) 
                and w.ord_state_date >= '$next_edate'
                and w.ord_state_date <= '$now_date'
                $where
            group by w.prd_cd
            $orderby
        ";
        $next_sale = DB::select($next_sale_query);

        $rows = array_map(function($row) use ($sale, $next_sale) {
            // 판매집계
            $cur_idx = array_search($row->prd_cd, array_column($sale, 'prd_cd'));
            $row->sale_qty = $sale[$cur_idx]->sale_qty ?? 0;
            $row->sale_tag_price = $row->sale_qty * $row->tag_price;
            $row->sale_price = $row->sale_qty * $row->price;
            $row->sale_wonga = $row->sale_qty * $row->wonga;
            // 기간재고 집계
            $cur_idx = array_search($row->prd_cd, array_column($next_sale, 'prd_cd'));
            $row->term_qty = ($row->term_qty * 1) + (($next_sale[$cur_idx]->sale_qty ?? 0) * 1);
            $row->term_tag_price = $row->term_qty * $row->tag_price;
            $row->term_price = $row->term_qty * $row->price;
            $row->term_wonga = $row->term_qty * $row->wonga;
            // 이전재고 집계
            $row->prev_qty = $row->term_qty - $row->loss_qty - $row->sale_qty - $row->stock_return_qty - $row->stock_in_qty;
            $row->prev_tag_price = $row->prev_qty * $row->tag_price;
            $row->prev_price = $row->prev_qty * $row->price;
            $row->prev_wonga = $row->prev_qty * $row->wonga;
            return $row;
        }, $rows);

        // pagination
        $total = 0;
        $total_data = '';
        $page_cnt = 0;
        if($page == 1) {
            $sql = "	
                select 
                    count(a.prd_cd) as total
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
                        -- 기간입고
                        , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) as stock_in_qty
                        , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) * p.tag_price as stock_in_tag_price
                        , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) * p.price as stock_in_price
                        , sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) * p.wonga as stock_in_wonga
                        -- 기간반품
                        , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) as stock_return_qty
                        , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) * p.tag_price as stock_return_tag_price
                        , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) * p.price as stock_return_price
                        , sum(if(hst.type = 9, ifnull(hst.qty, 0), 0)) * p.wonga as stock_return_wonga
                        -- loss
                        , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) as loss_qty
                        , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) * p.tag_price as loss_tag_price
                        , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) * p.price as loss_price
                        , sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) * p.wonga as loss_wonga
                        -- 기간재고
                        , (ps.qty 
                            - sum(if(_next.type = 1, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 9, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 14, ifnull(_next.qty, 0), 0))
                        ) as term_qty
                        , (ps.qty 
                            - sum(if(_next.type = 1, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 9, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 14, ifnull(_next.qty, 0), 0))
                        ) * p.tag_price as term_tag_price
                        , (ps.qty 
                            - sum(if(_next.type = 1, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 9, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 14, ifnull(_next.qty, 0), 0))
                        ) * p.price as term_price
                        , (ps.qty 
                            - sum(if(_next.type = 1, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 9, ifnull(_next.qty, 0), 0)) 
                            - sum(if(_next.type = 14, ifnull(_next.qty, 0), 0))
                        ) * p.wonga as term_wonga
                    from product_stock ps
                        inner join product_code pc on pc.prd_cd = ps.prd_cd
                        inner join product p on p.prd_cd = ps.prd_cd
                        left outer join (		
                            select idx, qty, prd_cd, location_type, type
                            from product_stock_hst
                            where ((type in (1,9) and location_type = 'STORAGE') or (type = 14 and location_type = 'STORE'))
                                and stock_state_date >= '$sdate'
                                and stock_state_date <= '$edate'
                        ) hst on hst.prd_cd = ps.prd_cd
                        left outer join (
                            select idx, qty, prd_cd, location_type, type
                            from product_stock_hst
                            where ((type in (1,9) and location_type = 'STORAGE') or (type = 14 and location_type = 'STORE'))
                                and stock_state_date >= '$next_edate'
                                and stock_state_date <= '$now_date'
                        ) _next on _next.prd_cd = ps.prd_cd
                    where 1=1 $where
                    group by ps.prd_cd
                ) a
            ";
            $total_row = DB::selectOne($sql);

            $sale_query = "
                select
                    sum(ifnull(w.qty, 0) * if(w.ord_state = 30, -1, 1)) as sale_qty
                    , sum(ifnull(w.qty, 0) * p.tag_price * if(w.ord_state = 30, -1, 1)) as sale_tag_price
                    , sum(ifnull(w.qty, 0) * p.price * if(w.ord_state = 30, -1, 1)) as sale_price
                    , sum(ifnull(w.qty, 0) * p.wonga * if(w.ord_state = 30, -1, 1)) as sale_wonga
                from order_opt_wonga w
                    inner join product_stock ps on ps.prd_cd = w.prd_cd
                    inner join product_code pc on pc.prd_cd = w.prd_cd
                    inner join product p on p.prd_cd = w.prd_cd
                where w.ord_state in (30,60,61) 
                    and w.ord_state_date >= '$sdate'
                    and w.ord_state_date <= '$edate'
                    $where
            ";
            $sale = DB::selectOne($sale_query);
            $total_row->sale_qty = $sale->sale_qty;
            $total_row->sale_tag_price = $sale->sale_tag_price;
            $total_row->sale_price = $sale->sale_price;
            $total_row->sale_wonga = $sale->sale_wonga;

            $term_query = "
                select
                    $total_row->term_qty + ifnull(sum(w.qty * if(w.ord_state = 30, 1, -1)), 0) as term_qty
                    , $total_row->term_tag_price + ifnull(sum(w.qty * p.tag_price * if(w.ord_state = 30, 1, -1)), 0) as term_tag_price
                    , $total_row->term_price + ifnull(sum(w.qty * p.price * if(w.ord_state = 30, 1, -1)), 0) as term_price
                    , $total_row->term_wonga + ifnull(sum(w.qty * p.wonga * if(w.ord_state = 30, 1, -1)), 0) as term_wonga
                from order_opt_wonga w
                    inner join product_stock ps on ps.prd_cd = w.prd_cd
                    inner join product_code pc on pc.prd_cd = w.prd_cd
                    inner join product p on p.prd_cd = w.prd_cd
                where w.ord_state in (30,60,61)
                    and w.ord_state_date >= '$next_edate'
                    and w.ord_state_date <= '$now_date'
                    $where
            ";
            $term = DB::selectOne($term_query);
            $total_row->term_qty = $term->term_qty;
            $total_row->term_tag_price = $term->term_tag_price;
            $total_row->term_price = $term->term_price;
            $total_row->term_wonga = $term->term_wonga;

            $total_row->prev_qty = $total_row->term_qty - $total_row->loss_qty - $total_row->sale_qty - $total_row->stock_return_qty - $total_row->stock_in_qty;
            $total_row->prev_tag_price = $total_row->term_tag_price - $total_row->loss_tag_price - $total_row->sale_tag_price - $total_row->stock_return_tag_price - $total_row->stock_in_tag_price;
            $total_row->prev_price = $total_row->term_price - $total_row->loss_price - $total_row->sale_price - $total_row->stock_return_price - $total_row->stock_in_price;
            $total_row->prev_wonga = $total_row->term_wonga - $total_row->loss_wonga - $total_row->sale_wonga - $total_row->stock_return_wonga - $total_row->stock_in_wonga;

            $total_data = $total_row;
            $total = $total_row->total;
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
