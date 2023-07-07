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
	//
	public function index(Request $request)
	{
        $edate = $request->input('date', now()->format("Y-m"));

		$values = [
            'date' => $edate,
		];
        return view( Config::get('shop.store.view') . '/sale/sal27', $values);
	}

	public function search(Request $request)
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
