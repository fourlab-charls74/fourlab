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

class sal22Controller extends Controller
{
    public function index()
	{

		$storages	= SLib::getStorage();

        $values = [
            'sdate' => now()->sub(1, 'month')->format('Y-m-d'),
            'edate' => date('Y-m-d'),
            'storage' => $storages
		];
        return view(Config::get('shop.store.view') . '/sale/sal22', $values);
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

		$sdate	= str_replace('-','', $sdate);
		$edate	= str_replace('-','', $edate);
		$next_edate	= str_replace('-','', $next_edate);

        $where = "";
        $hst_where = "";

        if ($storage_type != ''){
			$where .= " and storage.storage_cd = '$storage_type' ";
			$hst_where .= " and hst.location_cd = '$storage_type' ";
		}
		
        if ($prd_cds != '') {
			$prd_cd = explode(',', $prd_cds);
			$where .= " and (1!=1";
			$hst_where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or pss.prd_cd like '" . Lib::quote($cd) . "%' ";
				$hst_where .= " or hst.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
			$hst_where .= ")";
		} else {
			//$where .= " and p.prd_cd != ''";
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
				$hst_where .= " and pc.$opt in ($opt_join) ";
			}
		}

        // ordreby
        $ord = $request->input('ord', 'desc');
        $ord_field = $request->input('ord_field', 'pss.storage_cd');
        $orderby = sprintf("order by %s %s", $ord_field, $ord);
        if($ord_field == 'pss.prd_cd') $orderby .= ", pss.storage_cd";

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
                pss.storage_cd
                , storage.storage_nm
                , pss.prd_cd
                , pc.prd_cd_p
                , pc.color
                , pc.size
                , pss.goods_no
                , b.brand_nm 
                , g.style_no
                , p.prd_nm
                , g.goods_nm_eng
                , pc.goods_opt
                , p.tag_price as goods_sh
                , p.price
                , p.wonga

                -- 이전재고
                , (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) as prev_qty
                , (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.tag_price as prev_sh
                , (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.price as prev_price
                , (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.wonga as prev_wonga

                -- 생산입고
                , ifnull(hst.storage_in_qty, 0) as storage_in_qty
                , ifnull(hst.storage_in_qty, 0) * p.tag_price as storage_in_sh
                , ifnull(hst.storage_in_qty, 0) * p.price as storage_in_price
                , ifnull(hst.storage_in_qty, 0) * p.wonga as storage_in_wonga

                -- 생산반품
                , ifnull(storage_return_qty, 0) as storage_return_qty
                , ifnull(storage_return_qty, 0) * p.tag_price as storage_return_sh
                , ifnull(storage_return_qty, 0) * p.price as storage_return_price
                , ifnull(storage_return_qty, 0) * p.wonga as storage_return_wonga

                -- 이동입고
                , ifnull(rt_in_qty, 0) as rt_in_qty
                , ifnull(rt_in_qty, 0) * p.tag_price as rt_in_sh
                , ifnull(rt_in_qty, 0) * p.price as rt_in_price
                , ifnull(rt_in_qty, 0) * p.wonga as rt_in_wonga

                -- 이동출고
                , ifnull(rt_out_qty, 0) as rt_out_qty
                , ifnull(rt_out_qty, 0) * p.tag_price as rt_out_sh
                , ifnull(rt_out_qty, 0) * p.price as rt_out_price
                , ifnull(rt_out_qty, 0) * p.wonga as rt_out_wonga

                -- 매장출고
                , ifnull(store_out_qty, 0) as store_out_qty
                , ifnull(store_out_qty, 0) * p.tag_price as store_out_sh
                , ifnull(store_out_qty, 0) * p.price as store_out_price
                , ifnull(store_out_qty, 0) * p.wonga as store_out_wonga
                
                -- 매장반품
                , ifnull(store_return_qty, 0) as store_return_qty
                , ifnull(store_return_qty, 0) * p.tag_price as store_return_sh
                , ifnull(store_return_qty, 0) * p.price as store_return_price
                , ifnull(store_return_qty, 0) * p.wonga as store_return_wonga
                
                -- LOSS
                , ifnull(loss_qty, 0) as loss_qty
                , ifnull(loss_qty, 0) * p.tag_price as loss_sh
                , ifnull(loss_qty, 0) * p.price as loss_price
                , ifnull(loss_qty, 0) * p.wonga as loss_wonga
                
                -- 기간재고
                , pss.wqty - ifnull(hst.next_qty, 0) as term_qty
                , (pss.wqty - ifnull(hst.next_qty, 0)) * p.tag_price as term_sh
                , (pss.wqty - ifnull(hst.next_qty, 0)) * p.price as term_price
                , (pss.wqty - ifnull(hst.next_qty, 0)) * p.wonga as term_wonga
                
            from product_stock_storage pss
			left outer join (
				select
					hst.location_cd as storage_cd, pc.prd_cd_p, hst.prd_cd, 
					sum(if( hst.stock_state_date <= '$edate', hst.qty, 0)) as qty,
			
					-- 상품입고
					sum(if(hst.type = 1 and hst.stock_state_date <= '$edate', hst.qty, 0)) as storage_in_qty,
					-- 상품반품
					sum(if(hst.type = 9 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as storage_return_qty,
					-- 이동입고
					sum(if(hst.type = 16 and hst.qty > 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) as rt_in_qty,
					-- 이동출고
					sum(if(hst.type = 16 and hst.qty < 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as rt_out_qty,
					-- 매장출고
					sum(if(hst.type = 17 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as store_out_qty,
					-- 매장반품
					sum(if(hst.type = 11 and hst.stock_state_date <= '$edate', hst.qty, 0)) as store_return_qty,
					-- loss
					sum(if(hst.type = 14, hst.qty and hst.stock_state_date <= '$edate', 0)) * -1 as loss_qty,
					
					sum(if( hst.stock_state_date >= '$next_edate', hst.qty, 0)) as next_qty
				from product_stock_hst hst
				inner join product_code pc on hst.prd_cd = pc.prd_cd and pc.type = 'N' 
				inner join storage on storage.storage_cd = hst.location_cd
				where
					hst.location_type = 'STORAGE'
					$hst_where  	
					and hst.stock_state_date >= '$sdate'
				group by storage_cd, prd_cd_p, prd_cd
			) hst on pss.storage_cd = hst.storage_cd and pss.prd_cd = hst.prd_cd
			inner join product_code pc on pss.prd_cd = pc.prd_cd and pc.type = 'N'
			inner join product p on pss.prd_cd = p.prd_cd
			inner join storage storage on storage.storage_cd = pss.storage_cd
			left outer join goods g on g.goods_no = pss.goods_no
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
						pss.storage_cd
						, storage.storage_nm
						, pss.prd_cd
						, pc.prd_cd_p
						, pc.color
						, pc.size
						, pss.goods_no
						, b.brand_nm 
						, g.style_no
						, p.prd_nm
						, g.goods_nm_eng
						, pc.goods_opt
						, p.tag_price as goods_sh
						, p.price
						, p.wonga
		
						-- 이전재고
						, (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) as prev_qty
						, (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.tag_price as prev_sh
						, (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.price as prev_price
						, (pss.wqty - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.wonga as prev_wonga
		
						-- 생산입고
						, ifnull(hst.storage_in_qty, 0) as storage_in_qty
						, ifnull(hst.storage_in_qty, 0) * p.tag_price as storage_in_sh
						, ifnull(hst.storage_in_qty, 0) * p.price as storage_in_price
						, ifnull(hst.storage_in_qty, 0) * p.wonga as storage_in_wonga
		
						-- 생산반품
						, ifnull(storage_return_qty, 0) as storage_return_qty
						, ifnull(storage_return_qty, 0) * p.tag_price as storage_return_sh
						, ifnull(storage_return_qty, 0) * p.price as storage_return_price
						, ifnull(storage_return_qty, 0) * p.wonga as storage_return_wonga
		
						-- 이동입고
						, ifnull(rt_in_qty, 0) as rt_in_qty
						, ifnull(rt_in_qty, 0) * p.tag_price as rt_in_sh
						, ifnull(rt_in_qty, 0) * p.price as rt_in_price
						, ifnull(rt_in_qty, 0) * p.wonga as rt_in_wonga
		
						-- 이동출고
						, ifnull(rt_out_qty, 0) as rt_out_qty
						, ifnull(rt_out_qty, 0) * p.tag_price as rt_out_sh
						, ifnull(rt_out_qty, 0) * p.price as rt_out_price
						, ifnull(rt_out_qty, 0) * p.wonga as rt_out_wonga
		
						-- 매장출고
						, ifnull(store_out_qty, 0) as store_out_qty
						, ifnull(store_out_qty, 0) * p.tag_price as store_out_sh
						, ifnull(store_out_qty, 0) * p.price as store_out_price
						, ifnull(store_out_qty, 0) * p.wonga as store_out_wonga
						
						-- 매장반품
						, ifnull(store_return_qty, 0) as store_return_qty
						, ifnull(store_return_qty, 0) * p.tag_price as store_return_sh
						, ifnull(store_return_qty, 0) * p.price as store_return_price
						, ifnull(store_return_qty, 0) * p.wonga as store_return_wonga
						
						-- LOSS
						, ifnull(loss_qty, 0) as loss_qty
						, ifnull(loss_qty, 0) * p.tag_price as loss_sh
						, ifnull(loss_qty, 0) * p.price as loss_price
						, ifnull(loss_qty, 0) * p.wonga as loss_wonga
						
						-- 기간재고
						, pss.wqty - ifnull(hst.next_qty, 0) as term_qty
						, (pss.wqty - ifnull(hst.next_qty, 0)) * p.tag_price as term_sh
						, (pss.wqty - ifnull(hst.next_qty, 0)) * p.price as term_price
						, (pss.wqty - ifnull(hst.next_qty, 0)) * p.wonga as term_wonga
						
					from product_stock_storage pss
					left outer join (
						select
							hst.location_cd as storage_cd, pc.prd_cd_p, hst.prd_cd, 
							sum(if( hst.stock_state_date <= '$edate', hst.qty, 0)) as qty,
					
							-- 상품입고
							sum(if(hst.type = 1 and hst.stock_state_date <= '$edate', hst.qty, 0)) as storage_in_qty,
							-- 상품반품
							sum(if(hst.type = 9 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as storage_return_qty,
							-- 이동입고
							sum(if(hst.type = 16 and hst.qty > 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) as rt_in_qty,
							-- 이동출고
							sum(if(hst.type = 16 and hst.qty < 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as rt_out_qty,
							-- 매장출고
							sum(if(hst.type = 17 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as store_out_qty,
							-- 매장반품
							sum(if(hst.type = 11 and hst.stock_state_date <= '$edate', hst.qty, 0)) as store_return_qty,
							-- loss
							sum(if(hst.type = 14, hst.qty and hst.stock_state_date <= '$edate', 0)) * -1 as loss_qty,
							
							sum(if( hst.stock_state_date >= '$next_edate', hst.qty, 0)) as next_qty
						from product_stock_hst hst
						inner join product_code pc on hst.prd_cd = pc.prd_cd and pc.type = 'N' 
						inner join storage on storage.storage_cd = hst.location_cd
						where
							hst.location_type = 'STORAGE'
							$hst_where  	
							and hst.stock_state_date >= '$sdate'
						group by storage_cd, prd_cd_p, prd_cd
					) hst on pss.storage_cd = hst.storage_cd and pss.prd_cd = hst.prd_cd
					inner join product_code pc on pss.prd_cd = pc.prd_cd and pc.type = 'N'
					inner join product p on pss.prd_cd = p.prd_cd
					inner join storage storage on storage.storage_cd = pss.storage_cd
					left outer join goods g on g.goods_no = pss.goods_no
					left outer join brand b on b.br_cd = pc.brand
					where
						1 = 1
						$where

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
