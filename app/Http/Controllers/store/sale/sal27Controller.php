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
        $date = $request->input('date', now()->format("Y-m"));

		$months = [];
		$sd = Carbon::parse($date);
        while($sd <= Carbon::parse($date)){
            $months[] = [ "val" => $sd->format("Ym"), "fmt" => $sd->format("Y-m") ];
            $sd->addMonth();
        }
		$values = [
            'date' => $date,
			'months' => $months,
		];
        return view( Config::get('shop.store.view') . '/sale/sal27', $values);
	}

	public function search(Request $request)
	{
        $store_cd = $request->input('store_cd');
        $prd_cd_range_text = $request->input("prd_cd_range", '');

        $where = "";

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
                pc.item as item
                , c.code_val as item_nm
                , b.brand_nm
                , pc.prd_cd 
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
            from product_code pc
            inner join product p on p.prd_cd = pc.prd_cd
            inner join code c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c1.code_id
            left outer join goods g on g.goods_no = pc.goods_no
            left outer join brand b on b.br_cd = pc.brand
            left outer join code c on c.code_kind_cd = 'PRD_CD_ITEM' and c.code_id = pc.item
            $where
        
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
