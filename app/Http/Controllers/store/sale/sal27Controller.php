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
        
        $period = $request->input('period');
        $mutable = Carbon::now();
        $sdate	 = $mutable->sub(3, 'month')->format('Y-m');
        $edate = $request->input('date', now()->format("Y-m"));

		$months = [];
		$sd = Carbon::parse($sdate);
        while($sd <= Carbon::parse($edate)){
            $months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y년 m월") ];
            $sd->addMonth();
        }

		$values = [
            'date' => $edate,
			'months' => $months,
		];
        return view( Config::get('shop.store.view') . '/sale/sal27', $values);
	}

	public function search(Request $request)
	{
        $period = $request->input('period');
        $date = $request->input('date', now()->format("Y-m"));
        $prd_cd = $request->input('prd_cd');
        $store_cd = $request->input('store_cd');
        $prd_cd_range_text = $request->input("prd_cd_range", '');

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

        // 매장검색
        if ( $store_cd != "" ) {
            $where	.= " and (1!=1";
            foreach($store_cd as $store_cd) {
                $where .= " or o.store_cd = '$store_cd' ";

            }
            $where	.= ")";
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

        $sql = "
            select
                a.item, a.item_nm, a.brand_nm, a.br_cd, a.prd_cd, a.prd_cd_p, a.style_no, a.goods_nm, a.goods_nm_eng, a.color, a.size
                , sum(a.tag_price) as tag_price
                , sum(a.price) as price
                , sum(a.wonga) as wonga
                , sum(a.order_qty) as order_qty
                , sum(a.order_tag_price) as order_tag_price
                , sum(a.order_price) as order_price
                , sum(a.order_wonga) as order_wonga
                , sum(a.release_qty) as release_qty
                , sum(a.storage_stock_qty) as storage_stock_qty
                , sum(a.store_stock_qty) as store_stock_qty
                , sum(a.total_stock_qty) as total_stock_qty
            from (
                select 
                    pc.item as item
                    , c.code_val as item_nm
                    , b.brand_nm
                    , b.br_cd
                    , pc.prd_cd
                    , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                    , p.style_no
                    , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                    , g.goods_nm_eng
                    , pc.color
                    , pc.size
                    , pc.goods_opt
                    , c1.code_val as color_nm
                    , p.tag_price
                    , p.price
                    , p.wonga
                    , psop.qty as order_qty
                    , ( p.tag_price * psop.qty ) as order_tag_price
                    , ( p.price * psop.qty ) as order_price
                    , ( p.wonga * psop.qty ) as order_wonga
                    , hst.rt as release_first_date
                    , psr.qty as release_qty
                    , srp.return_qty as return_qty
                    , ( psr.qty - srp.return_qty) as total_release_qty
                    , pss2.wqty as storage_stock_qty
                    , pss.wqty as store_stock_qty
                    , pss2.wqty + pss.wqty as total_stock_qty
                from product_code pc
                    inner join product p on p.prd_cd = pc.prd_cd
                    inner join code c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c1.code_id
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join code c on c.code_kind_cd = 'PRD_CD_ITEM' and c.code_id = pc.item
                    left outer join (select
                                        sum(qty) as qty
                                        ,prd_cd
                                    from product_stock_order_product
                                    group by prd_cd
                                ) psop on psop.prd_cd = p.prd_cd
                    left outer join (select 
                                        h.prd_cd
                                        , h.rt 
                                    from product_stock_hst h
                                        inner join product_code pcd on pcd.prd_cd = h.prd_cd
                                    where h.prd_cd = pcd.prd_cd and h.type = 17 
                                    order by h.rt asc
                                ) hst on hst.prd_cd = pc.prd_cd
                    left outer join product_stock_release psr on psr.prd_cd = pc.prd_cd and psr.state >= 30
                    left outer join store_return_product srp on srp.prd_cd = pc.prd_cd
                    left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd
                    left outer join product_stock_storage pss2 on pss2.prd_cd = pc.prd_cd
                where 1=1 and p.style_no != ''
            $where
            ) a
            where 1=1
            group by a.item, a.brand_nm, a.prd_cd, a.prd_cd_p
            order by a.item
        
        ";


        $result = DB::select($sql);

		
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]
    );
		

	}

}
