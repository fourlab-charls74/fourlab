<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class acc06Controller extends Controller
{
    public function index(Request $request) {
        // $sdate = Carbon::now()->startOfMonth()->format("Y-m-d"); // 이번 달 기준
        // $edate = Carbon::now()->format("Y-m-d"); // 현재
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m-d"); // 저번 달 기준 - 테스트용
        $edate = Carbon::now()->subMonth()->lastOfMonth()->format("Y-m-d"); // 저번 달 마지막 날 - 테스트용

        $store_types = SLib::getStoreTypes();

        $sql = "select 
            code_id, code_val 
            from `code` 
            where code_kind_cd = 'pr_code'
            order by code_seq asc
        ";
        $pr_codes = DB::select($sql);

        $values = [
            'sdate'         => $sdate,
            'edate'         => $edate,
            'store_types'	=> $store_types,
            'pr_codes'      => $pr_codes
        ];
        return view( Config::get('shop.store.view') . '/account/acc06', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->format("Ymd"));
        $edate = $request->input('edate', date("Ymd"));

        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);

        $store_type = $request->input('store_type', "");
        $store_cd = $request->input('store_cd', "");

        /**
         * 검색조건 필터링
         */
        $where = "";
        if ($store_type) $where .= " and c.code_id = " . Lib::quote($store_type);
        if ($store_cd != "") $where .= " and s.store_cd like '" . Lib::quote($store_cd) . "%'";

        $sql = "select 
            code_id, code_val 
            from `code` 
            where code_kind_cd = 'pr_code'
            order by code_seq asc
        ";
        $pr_codes = DB::select($sql);


        // 행사코드별 매출구분
        $pr_codes_query = "";
        foreach ($pr_codes as $item) {
            $key = $item->code_id;
            $pr_codes_query .= "sum(if(m.pr_code = '$key', o.price * o.qty, 0)) as $key,";
        }

        /**
         * 금액 구간별 수수료율 계산 (주문금액 기준으로 정상가만 우선 적용하였음)
         * 특가, 용품 구분은 매출에서 기준이 어떠한 것인지 논의가 필요 - 매출 부분은 PR_CODE에 있는 값들로 구현하였음
         */
        $sql = /** @lang text */
            "
			select s.store_nm, c.code_val as store_type_nm, 
            if(a.ord_amt > sg.amt1, sg.amt1 * sg.fee1 / 100, greatest(a.ord_amt * sg.fee1, 0) / 100) as fee_amt_1,
            if(a.ord_amt - sg.amt1 > sg.amt2, sg.amt2 * sg.fee2 / 100, greatest(a.ord_amt - sg.amt1, 0) * sg.fee2 / 100) as fee_amt_2,
            if(a.ord_amt - sg.amt1 - sg.amt2 > sg.amt3, sg.amt3 * sg.fee3 / 100, greatest(a.ord_amt - sg.amt1 - sg.amt2, 0) * sg.fee3 / 100) as fee_amt_3,
            sg.*, a.*
			from store s left outer join (
				select
					m.store_cd,count(*) as cnt,
                    $pr_codes_query
                    sum(o.price*o.qty) as ord_amt
				from order_mst m
					inner join order_opt o on m.ord_no = o.ord_no
					inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
					inner join goods g on o.goods_no = g.goods_no
					left outer join store s on m.store_cd = s.store_cd
					left outer join brand b on g.brand = b.brand
					left outer join `code` c on c.code_kind_cd = 'g_goods_stat' and g.sale_stat_cl = c.code_id
					left outer join `code` c2 on c2.code_kind_cd = 'g_goods_type' and g.goods_type = c2.code_id
				where o.`ord_date` >= '$sdate' and o.ord_date <= '$edate'
					and m.store_cd <> ''
				group by m.store_cd
			) as a on s.store_cd = a.store_cd
				left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = s.store_type
                left outer join store_grade sg on sg.grade_cd = s.grade_cd
			where 1=1 $where
		";

        $result = DB::select($sql);

        return response()->json([
            'code'	=> 200,
            'head'	=> array(
                'total'	=> count($result)
            ),
            'body' => $result
        ]);

    }

}
