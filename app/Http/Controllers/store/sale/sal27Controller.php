<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class sal27Controller extends Controller
{
	public function index(Request $request)
	{
		$sdate = $request->input('sdate', now()->startOfMonth()->sub(2, 'month')->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));

		$values = [
            'sdate' => $sdate,
            'edate' => $edate,
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];
        return view(Config::get('shop.store.view') . '/sale/sal27', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', now()->startOfMonth()->sub(2, 'month')->format("Y-m"));
		$edate = $request->input('edate', now()->format("Y-m"));
		$sdate_day = date('Ymd', strtotime($sdate . '-01'));
		$edate_day = date('Ymd', strtotime($edate . '-' . date('t', strtotime($edate . '-01'))));
		$sdate_time = date('Y-m-d 00:00:00', strtotime($sdate . '-01'));
		$edate_time = date('Y-m-d 23:59:59', strtotime($edate . '-' . date('t', strtotime($edate . '-01'))));
		$today_day = date('Ymd');
		$next_edate_day = date('Ymd', strtotime($edate_day . '+1 day'));

		$store_channel = $request->input('store_channel', '');
		$store_channel_kind = $request->input('store_channel_kind', '');
		$store_cds = $request->input('store_no', []);
		$prd_cd = $request->input('prd_cd', '');
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$storage_cds = $request->input('storage_no', []);
			
		// $limit = $request->input('limit', 500);
		// $ord_field = $request->input('ord_field', 'pc.prd_cd');
		// $ord = $request->input('ord', 'desc');
		// $page = $request->input('page', 1);
		
		// 일자검색
		$date_where1 = " and ord_state_date >= '" . $sdate_day . "' and ord_state_date <= '" . $edate_day . "'";
		$date_where2 = " and stock_state_date >= '" . $sdate_day . "' and stock_state_date <= '" . $edate_day . "'";
		$date_where3 = " and replace(sr_date, '-', '') >= '" . $sdate_day . "' and replace(sr_date, '-', '') <= '" . $edate_day . "'";
		$date_where4 = " and rec_rt >= '" . $sdate_time . "' and rec_rt <= '" . $edate_time . "'";
		$date_where5 = " and h.stock_state_date >= '" . $next_edate_day . "' and h.stock_state_date <= '" . $today_day . "'";

		// 매장검색
		$store_where = "";
		if ($store_channel != '') {
			$store_where .= " and s.store_channel = '" . Lib::quote($store_channel) . "'";
		}
		if ($store_channel_kind != '') {
			$store_where .= " and s.store_channel_kind = '" . Lib::quote($store_channel_kind) . "'";
		}
		if (count($store_cds) > 0) {
			$store_cds = join(',', array_map(function($s) { return "'" . Lib::quote($s) . "'"; }, $store_cds));
			$store_where .= " and s.store_cd in (" . $store_cds . ")";
		}
		
		// 창고검색
		$storage_where1 = "";
		$storage_where2 = "";
		if (count($storage_cds) > 0) {
			$storage_cds = join(',', array_map(function($s) { return "'" . Lib::quote($s) . "'"; }, $storage_cds));
			$storage_where1 .= " and storage_cd in (" . $storage_cds . ")";
			$storage_where2 .= " and h.location_cd in (" . $storage_cds . ")";
		}
		
		// 바코드검색
		$product_where = "";
		if ($prd_cd != '') {
			$product_where .= " and (1<>1";
			$product_where .= join('', array_map(
				function($s) { return " or pc.prd_cd like '" . Lib::quote($s) . "%'"; },
				explode(",", $prd_cd)
			));
			$product_where .= ") ";
		}

		// 상품조건검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$opt_join = join(',', array_map(function($r) {return "'" . Lib::quote($r) . "'";}, $rows));
				$product_where .= " and pc." . $opt . " in (" . $opt_join . ")";
			}
		}
		
		// 월별판매내역검색
		$month_sale_sql = "";
		$month_sale_cols = [];
		$interval = date_diff(date_create($sdate_day), date_create($next_edate_day));
		for ($i = 0; $i < $interval->m; $i++) {
			$from = date('Ymd', strtotime($sdate_time . '+' . $i . ' month'));
			$to = date('Ymd', strtotime(date('Y-m', strtotime($from)) . '-' . date('t', strtotime($from))));

			$month_sale_sql .= "
				, (
					select ifnull(sum(ifnull(qty, 0) * if(ord_state = 30, 1, -1)), 0) as sale_qty 
					from order_opt_wonga
					where ord_state_date >= '" . $from . "' and ord_state_date <= '" . $to . "'
						and ord_state in (30,60,61)
						and prd_cd = pc.prd_cd
				) as sale_qty_" . $from
			;
			$month_sale_cols[] = [ 'key' => "sale_qty_" . $from, 'kor_nm' => date('Y년 m월', strtotime($from)) ];
		}

		$sql = "
			select a.*
				, c.code_val as item_nm
				, b.brand_nm
				, clr.code_val as color_nm
			    , com.com_nm
				, (a.term_in_qty * a.goods_sh) as term_in_goods_sh
				, (a.term_in_qty * a.price) as term_in_price
				, (a.term_in_qty * a.wonga) as term_in_wonga
				, (a.sale_qty * a.goods_sh) as sale_goods_sh
				, (a.sale_qty * a.price) as sale_price
				, (a.sale_qty * a.wonga) as sale_wonga
				, (a.term_storage_qty * a.goods_sh) as term_storage_goods_sh
				, (a.term_storage_qty * a.price) as term_storage_price
				, (a.term_storage_qty * a.wonga) as term_storage_wonga
				, (a.term_store_qty * a.goods_sh) as term_store_goods_sh
				, (a.term_store_qty * a.price) as term_store_price
				, (a.term_store_qty * a.wonga) as term_store_wonga
				, (a.term_storage_qty + a.term_store_qty) as term_total_qty
				, ((a.term_storage_qty * a.goods_sh) + (a.term_store_qty * a.goods_sh)) as term_total_goods_sh
				, ((a.term_storage_qty * a.price) + (a.term_store_qty * a.price)) as term_total_price
				, ((a.term_storage_qty * a.wonga) + (a.term_store_qty * a.wonga)) as term_total_wonga
				, 0 as none
			from (
				select pc.*
					, ps.in_qty as total_in_qty
					, date_format((
						select min(ord_state_date)
						from order_opt_wonga
						where 1=1 
							and ord_state = 30
							and prd_cd = pc.prd_cd
					), '%Y-%m-%d') as first_sale_date
					, (
						select ifnull(sum(ifnull(qty, 0) * if(ord_state = 30, 1, -1)), 0) as sale_qty 
						from order_opt_wonga
						where 1=1 
							$date_where1
							and ord_state in (30,60,61)
							and prd_cd = pc.prd_cd
					) as sale_qty
					, (
						select ifnull(sum(recv_amt), 0) as price
						from order_opt_wonga
						where 1=1
							$date_where1
							and ord_state in (30,60,61)
							and prd_cd = pc.prd_cd
					) as sale_recv_price
				    $month_sale_sql
					, (
						select ifnull(sum(qty), 0)
						from product_stock_hst
						where prd_cd = pc.prd_cd
							$date_where2
							and type in (1,9) and location_type = 'STORAGE'
					) as term_in_qty
					, ifnull(date_format(first_release_date, '%Y-%m-%d'), '') as first_release_date
					, ifnull(psr.qty, 0) as term_release_qty
					, ifnull(srp.qty, 0) as term_return_qty
					, (ifnull(psr.qty, 0) - ifnull(srp.qty, 0)) as term_out_qty
					, (ifnull(pssg.wqty, 0) - ifnull(storage_hst.qty, 0)) as term_storage_qty
					, (ifnull(pss.wqty, 0) - ifnull(store_hst.qty, 0)) as term_store_qty
				from (
					select pc.item, pc.brand, pc.prd_cd, pc.goods_no, pc.color, pc.size, pc.goods_opt
						, ifnull(pc.prd_cd_p, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt)) as prd_cd_p
						, g.goods_nm, g.goods_nm_eng, g.goods_sh, g.price, g.wonga, g.com_id
					from product_code pc
						inner join goods g on g.goods_no = pc.goods_no
					where 1=1 
						$product_where 
						and pc.goods_no <> 0
					order by pc.item, pc.brand, pc.prd_cd_p desc, pc.color, pc.prd_cd desc
				) pc
					inner join product_stock ps on ps.prd_cd = pc.prd_cd
					left outer join (
						select prd_cd, sum(wqty) as wqty
						from product_stock_storage
						where 1=1 $storage_where1
						group by prd_cd
					) pssg on pssg.prd_cd = pc.prd_cd
					left outer join (
						select ss.prd_cd, sum(ss.wqty) as wqty
						from product_stock_store ss
							inner join store s on s.store_cd = ss.store_cd
						where 1=1 $store_where
						group by ss.prd_cd
					) pss on pss.prd_cd = pc.prd_cd
					left outer join (
						select r.prd_cd, sum(r.qty) as qty, min(r.rec_rt) as first_release_date
						from product_stock_release r
							inner join store s on s.store_cd = r.store_cd
							inner join (
								select idx
								from product_stock_release
								where 1=1 $storage_where1
									and state > 10
									$date_where4
							) rr on rr.idx = r.idx
						where 1=1 $store_where
						group by r.prd_cd
					) psr on psr.prd_cd = pc.prd_cd
					left outer join (
						select p.prd_cd, sum(if(r.sr_state = 40, ifnull(p.fixed_return_qty, 0), ifnull(p.return_p_qty, 0))) as qty
						from store_return_product p
							inner join (
								select r.sr_cd, r.sr_state, r.store_cd
								from store_return r
									inner join (
										select sr_cd
										from store_return
										where 1=1 $storage_where1 
											and sr_state > 10
											$date_where3
									) rr on rr.sr_cd = r.sr_cd
							) r on r.sr_cd = p.sr_cd
							inner join store s on s.store_cd = r.store_cd
						where 1=1 $store_where
						group by p.prd_cd
					) srp on srp.prd_cd = pc.prd_cd
					left outer join (
						select h.prd_cd, sum(h.qty) as qty
						from product_stock_hst h
						where h.location_type = 'STORAGE' $storage_where2
							$date_where5
						group by h.prd_cd
					) storage_hst on storage_hst.prd_cd = pc.prd_cd
					left outer join (
						select h.prd_cd, sum(h.qty) as qty
						from product_stock_hst h
							inner join store s on s.store_cd = h.location_cd
						where h.location_type = 'STORE' $store_where
							$date_where5
						group by h.prd_cd
					) store_hst on store_hst.prd_cd = pc.prd_cd
			) a
				inner join code clr on clr.code_kind_cd = 'PRD_CD_COLOR' and clr.code_id = a.color
				left outer join code c on c.code_kind_cd = 'PRD_CD_ITEM' and c.code_id = a.item
				left outer join brand b on b.br_cd = a.brand
				left outer join company com on com.com_id = a.com_id
		";
		$rows = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($rows),
				'sale_month' => $month_sale_cols,
			],
			"body" => $rows
		]);
	}

	public function search_back(Request $request)
	{
        $period = $request->input('period');
        $mutable = Carbon::now();
        $sdate	 = $mutable->sub($period, 'month')->format('Y-m');
        $edate = $request->input('date', now()->format("Y-m"));
        $prd_cd = $request->input('prd_cd');
        $store_cd = $request->input('store_cd');
        $storage_cd = $request->input('storage_no');

        $prd_cd_range_text = $request->input("prd_cd_range", '');

        $months = [];
		$sd = Carbon::parse($sdate);
        while($sd <= Carbon::parse($edate)){
            $months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y년 m월") ];
            $sd->addMonth();
        }

        $where = "";

        $period_data = "";

       // 상품코드 검색
       if ($prd_cd != '') {
        $prd_cd = explode(',', $prd_cd);
        $where .= " and (1<>1 ";
        foreach ($prd_cd as $cd) {
            $where .= " or pc.prd_cd like '$cd%' ";
        }
        $where .= ") ";
    }

        // 매장검색
        if ( $store_cd != "" ) {
            $where	.= " and (1!=1";
            foreach($store_cd as $store_cd) {
                $where .= " or o.store_cd = '$store_cd' ";

            }
            $where	.= ")";
        }
        
        // 창고검색
        if ( $storage_cd != "" ) {
            $where	.= " and (1!=1";
            foreach($storage_cd as $storage_cd) {
                $where .= " or o.store_cd = '$storage_cd' ";

            }
            $where	.= ")";
        }

        $period_data .= "
            left outer join (
                select
                    prd_cd,
                    qty,
                    rt
                from product_stock_hst
                where rt > '$sdate' and rt < '$edate' 
            ) psh on psh.prd_cd = pc.prd_cd
        ";

        

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

        $sql = "
            select
                a.item, a.item_nm, a.brand_nm, a.br_cd, a.prd_cd, a.prd_cd_p, a.goods_nm, a.goods_nm_eng, a.color, a.size, a.goods_no, a.color_nm, a.goods_opt, a.release_first_date
                , a.tag_price as tag_price
                , a.price as price
                , a.wonga as wonga
                , a.order_amt as order_amt
                , a.order_qty as order_qty
                , a.order_tag_price as order_tag_price
                , a.order_price as order_price
                , a.order_wonga as order_wonga
                , a.release_qty as release_qty
                , a.return_qty as return_qty
                , (a.release_qty - a.return_qty) as total_release_qty
                , a.storage_stock_qty as storage_stock_qty
                , a.store_stock_qty as store_stock_qty
                , (a.storage_stock_qty + a.store_stock_qty) as total_stock_qty



                -- , a.sale_qty
                -- , a.sale_wonga
                -- , a.sale_price
                -- , a.sale_recv_amt
                -- , a.sale_tag_price
                , a.202302
            from (
                select 
                    pc.item as item
                    , c.code_val as item_nm
                    , b.brand_nm
                    , b.br_cd
                    , pc.prd_cd
                    , pc.goods_no
                    , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                    , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                    , g.goods_nm_eng
                    , pc.color
                    , pc.size
                    , pc.goods_opt
                    , c1.code_val as color_nm
                    , p.tag_price
                    , p.price
                    , p.wonga
                    , psop2.qty as order_amt
                    
                    -- 입고
                    , psop.qty as order_qty
                    , ( p.tag_price * psop.qty ) as order_tag_price
                    , ( p.price * psop.qty ) as order_price
                    , ( p.wonga * psop.qty ) as order_wonga
                    
                    -- 출고
                    , DATE_FORMAT(hst.rt, '%Y-%m-%d') as release_first_date
                    , ifnull(psr.qty, 0) as release_qty
                    , ifnull(srp.return_qty, 0) as return_qty

                    -- 재고
                    , ifnull(pss.wqty, 0) as storage_stock_qty
                    , ifnull(pss2.wqty, 0) as store_stock_qty

                    -- 판매
                    -- , opt.qty as sale_qty
                    -- , opt.wonga as sale_wonga
                    -- , opt.price as sale_price
                    -- , opt.recv_amt as sale_recv_amt
                    -- , opt.tag_price as sale_tag_price

                    -- 기간재고
                    , if(psh.rt >= '2023-01' && psh.rt <= '2023-02',psh.qty,0) as '202302' 

                from product_code pc
                    inner join product p on p.prd_cd = pc.prd_cd
                    inner join code c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c1.code_id
                    left outer join product_stock_order_product psop on psop.prd_cd = pc.prd_cd and state >= 30
                    left outer join (
                                    select 
                                        qty
                                        , prd_cd
                                    from product_stock_order_product
                                    where state != -10
                    ) psop2 on psop2.prd_cd = pc.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join code c on c.code_kind_cd = 'PRD_CD_ITEM' and c.code_id = pc.item
                    left outer join (
                                    select 
                                        h.prd_cd
                                        , h.rt 
                                    from product_stock_hst h
                                        left outer join product_code pcd on pcd.prd_cd = h.prd_cd
                                    where h.type = 17 
                                    order by h.rt asc
                    ) hst on hst.prd_cd = pc.prd_cd
                    left outer join product_stock_release psr on psr.prd_cd = pc.prd_cd
                    left outer join store_return_product srp on srp.prd_cd = pc.prd_cd
                    left outer join product_stock_storage pss on pss.prd_cd = pc.prd_cd
                    left outer join product_stock_store pss2 on pss2.prd_cd = pc.prd_cd 
                    -- left outer join (
                    --     select
                    --         o.qty
                    --         , o.wonga
                    --         , o.price
                    --         , o.prd_cd
                    --         , o.recv_amt
                    --         , p.tag_price
                    --     from order_opt o
                    --         inner join product p on p.prd_cd = o.prd_cd
                    -- ) opt on opt.prd_cd = pc.prd_cd
                    left outer join (
                        select
                            prd_cd,
                            qty,
                            rt
                        from product_stock_hst
                        where rt > :sdate and rt < :edate
                    ) psh on psh.prd_cd = pc.prd_cd

                where 1=1 and pc.goods_no != 0 and pc.goods_no != ''
                $where
            ) a
            where 1=1
            group by a.item, a.brand_nm, a.prd_cd
            order by a.item
        ";

        $result = DB::select($sql,['sdate' => $sdate, 'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
                'months' => $months,
            ),
            "body" => $result
        ]
    );
		

	}

}
