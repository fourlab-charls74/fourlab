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
			$where .= " ) ";
		}
		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.`$opt $in_query` ($opt_join) ";
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
                , p.prd_nm
                , pc.color
                , pc.size
                , p.price
                , p.tag_price
                , p.wonga
                , ps.qty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0)) as prev_qty -- 이전재고
                , sum(ifnull(stock_in.qty, 0)) as stock_in_qty -- 기간입고
                , sum(ifnull(stock_return.qty, 0)) as stock_return_qty -- 기간출고
                , 0 as sale_qty -- 판매
                , 0 as loss_qty -- loss
                , ps.qty - sum(ifnull(_next.qty, 0)) as term_qty -- 기간재고
            from product_stock ps
                inner join product_code pc on pc.prd_cd = ps.prd_cd
                inner join product p on p.prd_cd = ps.prd_cd
                left outer join (		
                    select idx, qty, prd_cd
                    from product_stock_hst
                    where location_type = 'STORAGE' 
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') >= '2022-11-30 00:00:00' 
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') <= '2022-11-30 23:59:59'
                ) hst on hst.prd_cd = ps.prd_cd
                left outer join product_stock_hst stock_in on stock_in.idx = hst.idx and stock_in.type = 1
                left outer join product_stock_hst stock_return on stock_return.idx = hst.idx and stock_return.type = 9
                left outer join (
                    select idx, qty, prd_cd
                    from product_stock_hst
                    where location_type = 'STORAGE'
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') >= '2022-12-01 00:00:00' 
                        and date_format(stock_state_date, '%Y-%m-%d %H:%i:%s') <= '2022-12-26 23:59:59'
                ) _next on _next.prd_cd = ps.prd_cd
            group by ps.prd_cd;
        ";
        $rows = DB::select($sql);

        // pagination
        $total = 0;
        $total_data = '';
        $page_cnt = 0;
        if($page == 1) {
            $sql = "";
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
