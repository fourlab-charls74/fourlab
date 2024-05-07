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

class sal51Controller extends Controller
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
		return view(Config::get('shop.store.view') . '/sale/sal51', $values);
	}

	public function search(Request $request)
	{
		ini_set('memory_limit', -1);

		$sdate              = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
		$edate              = $request->input('edate', date('Y-m-d'));
		$next_edate         = date("Y-m-d", strtotime("+1 day", strtotime($edate)));
		$store_cds          = $request->input('store_no', []);
		$close_yn           = $request->input('close_yn', 'N');
		$prd_cds            = $request->input('prd_cd', '');
		$prd_cd_range_text  = $request->input("prd_cd_range", '');
		$ext_term_qty       = $request->input('ext_term_qty', ''); // 기간재고 0 제외여부
		$store_channel	    = $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		$sdate	= str_replace('-','', $sdate);
		$edate	= str_replace('-','', $edate);
		$next_edate	= str_replace('-','', $next_edate);

		$where	= "";
		$hst_where	= "";

		if($prd_cds != '') {
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
		if ($close_yn == 'N') {
			$where .= " and store.use_yn = 'Y' and (store.edate = '' or store.edate is null or store.edate >= date_format(now(), '%Y%m%d')) ";
			$hst_where .= " and store.use_yn = 'Y' and (store.edate = '' or store.edate is null or store.edate >= date_format(now(), '%Y%m%d')) ";
		} else if ($close_yn == 'Y') {
			$where .= " and (store.edate != '' and store.edate is not null and store.edate < date_format(now(), '%Y%m%d')) ";
			$hst_where .= " and (store.edate != '' and store.edate is not null and store.edate < date_format(now(), '%Y%m%d')) ";
		}
		if ($store_channel != "") {
			$where .= "and store.store_channel ='" . Lib::quote($store_channel). "'";
			$hst_where .= "and store.store_channel ='" . Lib::quote($store_channel). "'";
		}
		if ($store_channel_kind != "") {
			$where .= "and store.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
			$hst_where .= "and store.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";
		}

		// store_where
		if(count($store_cds) > 0){
			$where	.= " and (";
			$hst_where	.= " and (";

			foreach($store_cds as $key => $cd) {
				if ($key === 0) {
					$where .= "pss.store_cd = '$cd'";
					$hst_where .= "hst.location_cd = '$cd'";
				} else {
					$where .= " or pss.store_cd = '$cd'";
					$hst_where .= " or hst.location_cd = '$cd'";
				}
			}

			$where	.= ")";
			$hst_where	.= ")";
		}

		//if (count($store_cds) < 1) {
		//	$store_where = "1=1";
		//}

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

		if ($ext_term_qty === 'true')
			$where .= "
			and ( 
				(store_in_qty != 0) 
				or (store_return_qty * -1 != 0) 
				or (rt_in_qty != 0)
				or (rt_out_qty * -1 != 0)
				or (sale_qty * -1 != 0)
			)
	        ";

		// ordreby
		$ord = $request->input('ord', 'desc');
		$ord_field = $request->input('ord_field', 'pss.store_cd');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);
		if($ord_field == 'pss.prd_cd') $orderby .= ", pss.store_cd";

		// pagination
		$page = $request->input('page', 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $request->input('limit', 500);
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		//전체 데이터 보기(임시)
		if($page_size == -1)    $limit = "";

		$sql	= "
			select
				pss.store_cd
				,store.store_nm
				,st.store_channel as store_type_nm
				,pss.prd_cd
				,pc.prd_cd_p as prd_cd_sm
				,pc.color
				,pc.size
				,pss.goods_no
				,b.brand_nm
				,g.style_no
				,g.goods_nm
				,g.goods_nm_eng
				,pc.goods_opt
				,p.tag_price as goods_sh
				,p.price
				,pw.wonga
				-- ,p.wonga
			
				-- 이전재고
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) as prev_qty
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.tag_price as prev_sh
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.price as prev_price
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * pw.wonga as prev_wonga
				-- ,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.wonga as prev_wonga
			
				-- 매장입고
				,ifnull(hst.store_in_qty, 0) as store_in_qty
				,ifnull(hst.store_in_qty, 0) * p.tag_price as store_in_sh
				,ifnull(hst.store_in_qty, 0) * p.price as store_in_price
				,ifnull(hst.store_in_qty, 0) * pw.wonga as store_in_wonga
				-- ,ifnull(hst.store_in_qty, 0) * p.wonga as store_in_wonga
			
				-- 매장반품
				,ifnull(hst.store_return_qty, 0) as store_return_qty
				,ifnull(hst.store_return_qty, 0) * p.tag_price as store_return_sh
				,ifnull(hst.store_return_qty, 0) * p.price as store_return_price
				,ifnull(hst.store_return_qty, 0) * pw.wonga as store_return_wonga
				-- ,ifnull(hst.store_return_qty, 0) * p.wonga as store_return_wonga
				
				-- 이동입고
				,ifnull(hst.rt_in_qty, 0) as rt_in_qty
				,ifnull(hst.rt_in_qty, 0) * p.tag_price as rt_in_sh
				,ifnull(hst.rt_in_qty, 0) * p.price as rt_in_price
				,ifnull(hst.rt_in_qty, 0) * pw.wonga as rt_in_wonga
				-- ,ifnull(hst.rt_in_qty, 0) * p.wonga as rt_in_wonga
			
				-- 이동출고
				,ifnull(hst.rt_out_qty, 0) as rt_out_qty
				,ifnull(hst.rt_out_qty, 0) * p.tag_price as rt_out_sh
				,ifnull(hst.rt_out_qty, 0) * p.price as rt_out_price
				,ifnull(hst.rt_out_qty, 0) * pw.wonga as rt_out_wonga
				-- ,ifnull(hst.rt_out_qty, 0) * p.wonga as rt_out_wonga
			
				-- 매장판매
				,ifnull(hst.sale_qty, 0) as sale_qty
				,ifnull(hst.sale_qty, 0) * p.tag_price as sale_sh
				,ifnull(hst.sale_qty, 0) * p.price as sale_price
				-- ,ifnull(hst.sale_wonga, 0) as sale_wonga
				,ifnull(hst.sale_qty, 0) * pw.wonga as sale_wonga
			
				-- loss
				,ifnull(hst.loss_qty, 0) as loss_qty
				,ifnull(hst.loss_qty, 0) * p.tag_price as loss_sh
				,ifnull(hst.loss_qty, 0) * p.price as loss_price
				,ifnull(hst.loss_qty, 0) * pw.wonga as loss_wonga
				-- ,ifnull(hst.loss_qty, 0) * p.wonga as loss_wonga
			
				-- 기간재고
				,(pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) as term_qty
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * p.tag_price as term_sh
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * p.price as term_price
				,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * pw.wonga as term_wonga
				-- ,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * p.wonga as term_wonga
			
				-- 현재재고
				,(pss.wqty + pss.rqty) as current_qty
			from product_stock_store pss
			left outer join (
				select
					hst.location_cd as store_cd, pc.prd_cd_p, hst.prd_cd, 
					sum(if( hst.stock_state_date <= '$edate', hst.qty, 0)) as qty,
			
					-- 매장입고
					sum(if(hst.type = 1 and hst.stock_state_date <= '$edate', hst.qty, 0)) as store_in_qty,
					-- 매장반품
					sum(if(hst.type = 11 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as store_return_qty,
					-- 이동입고
					sum(if(hst.type = 15 and hst.qty > 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) as rt_in_qty,
					-- 이동출고
					sum(if(hst.type = 15 and hst.qty < 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as rt_out_qty,
					-- 매장판매
					sum(if((hst.type = '2' or hst.type = '5' or hst.type = '6') and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as sale_qty,
				
					-- 매장판매 ( 원가 )
					-- sum(if((hst.type = '2' or hst.type = '5' or hst.type = '6') and hst.stock_state_date <= '$edate', hst.qty * oo.wonga, 0)) * -1 as sale_wonga,

					-- loss
					sum(if(hst.type = 14 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as loss_qty,
					
					sum(if( hst.stock_state_date >= '$next_edate', hst.qty, 0)) as next_qty
				from product_stock_hst hst
				inner join product_code pc on hst.prd_cd = pc.prd_cd and pc.type = 'N' 
				inner join store on store.store_cd = hst.location_cd
				-- left outer join order_opt oo on oo.ord_opt_no = hst.ord_opt_no
				where
					hst.location_type = 'STORE'
					$hst_where  	
					and hst.stock_state_date >= '$sdate'
				group by store_cd, prd_cd_p, prd_cd
			) hst on pss.store_cd = hst.store_cd and pss.prd_cd = hst.prd_cd
			inner join product_code pc on pss.prd_cd = pc.prd_cd and pc.type = 'N'
			inner join product_wonga pw on pc.prd_cd_p = pw.prd_cd_p
			inner join product p on pss.prd_cd = p.prd_cd
			inner join store store on store.store_cd = pss.store_cd
			left outer join goods g on g.goods_no = pss.goods_no
			left outer join brand b on b.br_cd = pc.brand
			inner join store_channel st on st.store_channel_cd = store.store_channel and st.store_kind_cd = store.store_channel_kind
			-- inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = store.store_type
			where
			    1=1
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
						pss.store_cd
						,store.store_nm
						,st.store_channel as store_type_nm
						,pss.prd_cd
						,pc.prd_cd_p as prd_cd_sm
						,pc.color
						,pc.size
						,pss.goods_no
						,b.brand_nm
						,g.style_no
						,g.goods_nm
						,g.goods_nm_eng
						,pc.goods_opt
						,p.tag_price as goods_sh
						,p.price
						,pw.wonga
						-- ,p.wonga
					
						-- 이전재고
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) as prev_qty
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.tag_price as prev_sh
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * p.price as prev_price
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) - ifnull(hst.qty, 0)) * pw.wonga as prev_wonga
					
						-- 매장입고
						,ifnull(hst.store_in_qty, 0) as store_in_qty
						,ifnull(hst.store_in_qty, 0) * p.tag_price as store_in_sh
						,ifnull(hst.store_in_qty, 0) * p.price as store_in_price
						,ifnull(hst.store_in_qty, 0) * pw.wonga as store_in_wonga
					
						-- 매장반품
						,ifnull(hst.store_return_qty, 0) as store_return_qty
						,ifnull(hst.store_return_qty, 0) * p.tag_price as store_return_sh
						,ifnull(hst.store_return_qty, 0) * p.price as store_return_price
						,ifnull(hst.store_return_qty, 0) * pw.wonga as store_return_wonga
						
						-- 이동입고
						,ifnull(hst.rt_in_qty, 0) as rt_in_qty
						,ifnull(hst.rt_in_qty, 0) * p.tag_price as rt_in_sh
						,ifnull(hst.rt_in_qty, 0) * p.price as rt_in_price
						,ifnull(hst.rt_in_qty, 0) * pw.wonga as rt_in_wonga
					
						-- 이동출고
						,ifnull(hst.rt_out_qty, 0) as rt_out_qty
						,ifnull(hst.rt_out_qty, 0) * p.tag_price as rt_out_sh
						,ifnull(hst.rt_out_qty, 0) * p.price as rt_out_price
						,ifnull(hst.rt_out_qty, 0) * pw.wonga as rt_out_wonga
					
						-- 매장판매
						,ifnull(hst.sale_qty, 0) as sale_qty
						,ifnull(hst.sale_qty, 0) * p.tag_price as sale_sh
						,ifnull(hst.sale_qty, 0) * p.price as sale_price
						-- ,ifnull(hst.sale_wonga, 0) as sale_wonga
						,ifnull(hst.sale_qty, 0) * pw.wonga as sale_wonga
					
						-- loss
						,ifnull(hst.loss_qty, 0) as loss_qty
						,ifnull(hst.loss_qty, 0) * p.tag_price as loss_sh
						,ifnull(hst.loss_qty, 0) * p.price as loss_price
						,ifnull(hst.loss_qty, 0) * pw.wonga as loss_wonga
					
						-- 기간재고
						,(pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0) as term_qty
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * p.tag_price as term_sh
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * p.price as term_price
						,((pss.wqty + pss.rqty) - ifnull(hst.next_qty, 0)) * pw.wonga as term_wonga
					
						-- 현재재고
						,(pss.wqty + pss.rqty) as current_qty
					from product_stock_store pss
					left outer join (
						select
							hst.location_cd as store_cd, pc.prd_cd_p, hst.prd_cd, 
							sum(if( hst.stock_state_date <= '$edate', hst.qty, 0)) as qty,
					
							-- 매장입고
							sum(if(hst.type = 1 and hst.stock_state_date <= '$edate', hst.qty, 0)) as store_in_qty,
							-- 매장반품
							sum(if(hst.type = 11 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as store_return_qty,
							-- 이동입고
							sum(if(hst.type = 15 and hst.qty > 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) as rt_in_qty,
							-- 이동출고
							sum(if(hst.type = 15 and hst.qty < 0 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as rt_out_qty,
							-- 매장판매
							sum(if((hst.type = '2' or hst.type = '5' or hst.type = '6') and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as sale_qty,
				
							-- 매장판매 ( 원가 )
							-- sum(if((hst.type = '2' or hst.type = '5' or hst.type = '6') and hst.stock_state_date <= '$edate', hst.qty * oo.wonga, 0)) * -1 as sale_wonga,

							-- loss
							sum(if(hst.type = 14 and hst.stock_state_date <= '$edate', hst.qty, 0)) * -1 as loss_qty,
							
							sum(if( hst.stock_state_date >= '$next_edate', hst.qty, 0)) as next_qty
						from product_stock_hst hst
						inner join product_code pc on hst.prd_cd = pc.prd_cd and pc.type = 'N' 
						inner join store on store.store_cd = hst.location_cd
						left outer join order_opt oo on oo.ord_opt_no = hst.ord_opt_no
						where
							hst.location_type = 'STORE'
							$hst_where  	
							and hst.stock_state_date >= '$sdate'
						group by store_cd, prd_cd_p, prd_cd
					) hst on pss.store_cd = hst.store_cd and pss.prd_cd = hst.prd_cd
					inner join product_code pc on pss.prd_cd = pc.prd_cd and pc.type = 'N'
					inner join product_wonga pw on pc.prd_cd_p = pw.prd_cd_p
					inner join product p on pss.prd_cd = p.prd_cd
					inner join store store on store.store_cd = pss.store_cd
					left outer join goods g on g.goods_no = pss.goods_no
					left outer join brand b on b.br_cd = pc.brand
					inner join store_channel st on st.store_channel_cd = store.store_channel and st.store_kind_cd = store.store_channel_kind
--					inner join code st on st.code_kind_cd = 'STORE_TYPE' and st.code_id = store.store_type
					where
					    1=1
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
