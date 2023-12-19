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
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Ymd'));
        $edate = $request->input('edate', date('Ymd'));
        $next_edate = date("Ymd", strtotime("+1 day", strtotime($edate)));
        $now_date = date('Ymd');
		$prd_cd	= $request->input('prd_cd', ''); // 상품코드
		$prd_cd_range_text = $request->input('prd_cd_range', ''); // 상품옵션범위
        $ext_current_qty = $request->input('ext_current_qty', ''); // 현재재고 0 제외여부
        $storage_cd = $request->input('storage_no');

		$sdate	= str_replace('-','', $sdate);
		$edate	= str_replace('-','', $edate);
		$next_edate	= str_replace('-','', $next_edate);

        /** 검색조건 필터링 */
        $where = "";
		$hst_where	= "";

        // 상품코드 검색
        if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			$hst_where .= " and (1!=1";
			foreach ($prd_cd as $cd) {
				$where .= " or ps.prd_cd like '$cd%' ";
				$hst_where .= " or hst.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ") ";
			$hst_where .= ")";
		}

		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
				$hst_where .= " and pc.$opt in ($opt_join) ";
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

		//전체 데이터 보기(임시)
		if($page_size == -1)    $limit = "";

        $sql = "
            select 
                ps.prd_cd
                , pc.prd_cd_p
                , pc.color
                , pc.size
                , ps.goods_no
                , b.brand_nm 
                , g.style_no
                , p.prd_nm
                , g.goods_nm_eng
                , pc.goods_opt
                , p.tag_price as goods_sh
                , p.price
                , p.wonga

                -- 이전재고
                , (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) as prev_qty
                , (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.tag_price as prev_tag_price
                , (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.price as prev_price
                , (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.wonga as prev_wonga

                -- 상품입고
                , ifnull(hst.stock_in_qty, 0) as stock_in_qty
                , ifnull(hst.stock_in_qty, 0) * p.tag_price as stock_in_tag_price
                , ifnull(hst.stock_in_qty, 0) * p.price as stock_in_price
                , ifnull(hst.stock_in_qty, 0) * p.wonga as stock_in_wonga

                -- 기간반품
                , ifnull(stock_return_qty, 0) as stock_return_qty
                , ifnull(stock_return_qty, 0) * p.tag_price as stock_return_tag_price
                , ifnull(stock_return_qty, 0) * p.price as stock_return_price
                , ifnull(stock_return_qty, 0) * p.wonga as stock_return_wonga

				-- 매장판매
				,ifnull(hst.sale_qty, 0) as sale_qty
				,ifnull(hst.sale_qty, 0) * p.tag_price as sale_tag_price
				,ifnull(hst.sale_qty, 0) * p.price as sale_price
				,ifnull(hst.sale_qty, 0) * p.wonga as sale_wonga

                -- LOSS
                , ifnull(loss_qty, 0) as loss_qty
                , ifnull(loss_qty, 0) * p.tag_price as loss_tag_price
                , ifnull(loss_qty, 0) * p.price as loss_price
                , ifnull(loss_qty, 0) * p.wonga as loss_wonga
                
                -- 기간재고
                -- , ps.qty - ifnull(hst.next_qty, 0) as term_qty
                -- , (ps.qty - ifnull(hst.next_qty, 0)) * p.tag_price as term_tag_price
                -- , (ps.qty - ifnull(hst.next_qty, 0)) * p.price as term_price
                -- , (ps.qty - ifnull(hst.next_qty, 0)) * p.wonga as term_wonga
            	, (
            		(ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0))	-- 이전재고
            		+ ifnull(hst.stock_in_qty, 0)							-- 상품입고
            		- ifnull(stock_return_qty, 0)							-- 기간반품
            		- ifnull(hst.sale_qty, 0)								-- 매장판매
            		- ifnull(loss_qty, 0)									-- LOSS
            	) as term_qty
            	,( (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) + ifnull(hst.stock_in_qty, 0) - ifnull(stock_return_qty, 0) - ifnull(hst.sale_qty, 0) - ifnull(loss_qty, 0) ) * p.tag_price as term_tag_price
            	,( (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) + ifnull(hst.stock_in_qty, 0) - ifnull(stock_return_qty, 0) - ifnull(hst.sale_qty, 0) - ifnull(loss_qty, 0) ) * p.price as term_price
            	,( (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) + ifnull(hst.stock_in_qty, 0) - ifnull(stock_return_qty, 0) - ifnull(hst.sale_qty, 0) - ifnull(loss_qty, 0) ) * p.wonga as term_wonga
            
            from product_stock ps
			left outer join (
				select
					pc.prd_cd_p, hst.prd_cd, 
					sum(if( 
					    hst.stock_state_date <= '$edate'
					    , hst.qty, 0
					)) as qty,
			
					-- 상품입고
					sum(if(
						(
							hst.type = '1'
							and if( hst.stock_state_date <= '20231109', (hst.location_type = 'STORE' or hst.location_type = 'STORAGE') , hst.location_type = 'STORAGE')
							and hst.stock_state_date <= '$edate'
					    ), hst.qty, 0
					)) as stock_in_qty,

					-- 기간반품
					sum(if(hst.type = 9 and hst.location_type = 'STORAGE' and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as stock_return_qty,

					-- 매장판매
					sum(if((hst.type = '2' or hst.type = '5' or hst.type = '6') and location_type = 'STORE' and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as sale_qty,

					-- loss
					sum(if(hst.type = 14 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as loss_qty,
					
					sum(if( 
					    hst.stock_state_date >= '$next_edate'
					    , hst.qty, 0
					)) as next_qty
				from product_stock_hst hst
				inner join product_code pc on hst.prd_cd = pc.prd_cd and pc.type = 'N' 
				where
				    1=1
					$hst_where  	
					and hst.stock_state_date >= '$sdate'
				group by prd_cd_p, prd_cd
			) hst on ps.prd_cd = hst.prd_cd
			inner join product_code pc on ps.prd_cd = pc.prd_cd and pc.type = 'N'
			inner join product p on ps.prd_cd = p.prd_cd
			left outer join goods g on g.goods_no = ps.goods_no
			left outer join brand b on b.br_cd = pc.brand
            where
                1 = 1
                $where
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
                    count(a.prd_cd) as total
                    , sum(prev_qty) as prev_qty
                    , sum(prev_sh) as prev_tag_price
                    , sum(prev_price) as prev_price
                    , sum(prev_wonga) as prev_wonga
                    , sum(stock_in_qty) as stock_in_qty
                    , sum(stock_in_sh) as stock_in_tag_price
                    , sum(stock_in_price) as stock_in_price
                    , sum(stock_in_wonga) as stock_in_wonga
                    , sum(stock_return_qty) as stock_return_qty
                    , sum(stock_return_sh) as stock_return_tag_price
                    , sum(stock_return_price) as stock_return_price
                    , sum(stock_return_wonga) as stock_return_wonga
                    , sum(sale_qty) as sale_qty
                    , sum(sale_sh) as sale_tag_price
                    , sum(sale_price) as sale_price
                    , sum(sale_wonga) as sale_wonga
                    , sum(loss_qty) as loss_qty
                    , sum(loss_sh) as loss_tag_price
                    , sum(loss_price) as loss_price
                    , sum(loss_wonga) as loss_wonga
                    , sum(term_qty) as term_qty
                    , sum(term_sh) as term_tag_price
                    , sum(term_price) as term_price
                    , sum(term_wonga) as term_wonga
                from (
					select 
						ps.prd_cd
						, pc.prd_cd_p
						, pc.color
						, pc.size
						, ps.goods_no
						, b.brand_nm 
						, g.style_no
						, p.prd_nm
						, g.goods_nm_eng
						, pc.goods_opt
						, p.tag_price as goods_sh
						, p.price
						, p.wonga
		
						-- 이전재고
						, (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) as prev_qty
						, (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.tag_price as prev_sh
						, (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.price as prev_price
						, (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.wonga as prev_wonga
		
						-- 상품입고
						, ifnull(hst.stock_in_qty, 0) as stock_in_qty
						, ifnull(hst.stock_in_qty, 0) * p.tag_price as stock_in_sh
						, ifnull(hst.stock_in_qty, 0) * p.price as stock_in_price
						, ifnull(hst.stock_in_qty, 0) * p.wonga as stock_in_wonga
		
						-- 기간반품
						, ifnull(stock_return_qty, 0) as stock_return_qty
						, ifnull(stock_return_qty, 0) * p.tag_price as stock_return_sh
						, ifnull(stock_return_qty, 0) * p.price as stock_return_price
						, ifnull(stock_return_qty, 0) * p.wonga as stock_return_wonga
		
						-- 매장판매
						,ifnull(hst.sale_qty, 0) as sale_qty
						,ifnull(hst.sale_qty, 0) * p.tag_price as sale_sh
						,ifnull(hst.sale_qty, 0) * p.price as sale_price
						,ifnull(hst.sale_qty, 0) * p.wonga as sale_wonga
		
						-- LOSS
						, ifnull(loss_qty, 0) as loss_qty
						, ifnull(loss_qty, 0) * p.tag_price as loss_sh
						, ifnull(loss_qty, 0) * p.price as loss_price
						, ifnull(loss_qty, 0) * p.wonga as loss_wonga
						
						-- 기간재고
						-- , ps.qty - ifnull(hst.next_qty, 0) as term_qty
						-- , (ps.qty - ifnull(hst.next_qty, 0)) * p.tag_price as term_sh
						-- , (ps.qty - ifnull(hst.next_qty, 0)) * p.price as term_price
						-- , (ps.qty - ifnull(hst.next_qty, 0)) * p.wonga as term_wonga
						, (
							(ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0))	-- 이전재고
							+ ifnull(hst.stock_in_qty, 0)							-- 상품입고
							- ifnull(stock_return_qty, 0)							-- 기간반품
							- ifnull(hst.sale_qty, 0)								-- 매장판매
							- ifnull(loss_qty, 0)									-- LOSS
						) as term_qty
						,( (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) + ifnull(hst.stock_in_qty, 0) - ifnull(stock_return_qty, 0) - ifnull(hst.sale_qty, 0) - ifnull(loss_qty, 0) ) * p.tag_price as term_sh
						,( (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) + ifnull(hst.stock_in_qty, 0) - ifnull(stock_return_qty, 0) - ifnull(hst.sale_qty, 0) - ifnull(loss_qty, 0) ) * p.price as term_price
						,( (ps.qty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) + ifnull(hst.stock_in_qty, 0) - ifnull(stock_return_qty, 0) - ifnull(hst.sale_qty, 0) - ifnull(loss_qty, 0) ) * p.wonga as term_wonga
						
					from product_stock ps
					left outer join (
						select
							pc.prd_cd_p, hst.prd_cd, 
							sum(if( 
								hst.stock_state_date <= '$edate'
								, hst.qty, 0
							)) as qty,
					
							-- 상품입고
							sum(if(
								(
									hst.type = '1'
									and if( hst.stock_state_date <= '20231109', (hst.location_type = 'STORE' or hst.location_type = 'STORAGE') , hst.location_type = 'STORAGE')
									and hst.stock_state_date <= '$edate'
								), hst.qty, 0
							)) as stock_in_qty,
		
							-- 기간반품
							sum(if(hst.type = 9 and hst.location_type = 'STORAGE' and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as stock_return_qty,
		
							-- 매장판매
							sum(if((hst.type = '2' or hst.type = '5' or hst.type = '6') and location_type = 'STORE' and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as sale_qty,
		
							-- loss
							sum(if(hst.type = 14 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as loss_qty,
							
							sum(if( 
							    hst.stock_state_date >= '$next_edate'
							    , hst.qty, 0
							)) as next_qty
						from product_stock_hst hst
						inner join product_code pc on hst.prd_cd = pc.prd_cd and pc.type = 'N' 
						where
							1=1
							$hst_where  	
							and hst.stock_state_date >= '$sdate'
						group by prd_cd_p, prd_cd
					) hst on ps.prd_cd = hst.prd_cd
					inner join product_code pc on ps.prd_cd = pc.prd_cd and pc.type = 'N'
					inner join product p on ps.prd_cd = p.prd_cd
					left outer join goods g on g.goods_no = ps.goods_no
					left outer join brand b on b.br_cd = pc.brand
					where
						1 = 1
						$where
                ) a
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
