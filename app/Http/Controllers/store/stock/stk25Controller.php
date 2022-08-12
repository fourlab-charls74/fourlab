<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class stk25Controller extends Controller
{
	public function index(Request $request)
	{
        $sdate = Carbon::now()->format('Y-m');
        $store_cd = $request->input("store_cd", '');
        $store = ['store_cd' => '', 'store_nm' => ''];

        if($store_cd != '')
            $store = DB::table('store')->where('store_cd', '=', $store_cd)->select('store_cd', 'store_nm')->first();

        $sql = "
            select 
                sale_type_cd, 
                st.sale_type_nm, 
                st.sale_apply, 
                st.amt_kind,
                st.sale_amt, 
                st.sale_per
            from sale_type_apply
                inner join sale_type st on st.idx = sale_type_cd
            where apply_date = :apply_date and apply_yn = 'Y'
        ";
        $sale_types = DB::select($sql, ['apply_date' => $sdate]);

		$values = [
            'store' => $store,
			'sdate' => $sdate,
            'sale_types' => $sale_types,
		];
        return view(Config::get('shop.store.view') . '/stock/stk25', $values);
	}

	public function search(Request $request)
	{	
        $store_cd = $request->input("store_no", "");
		$sale_month = str_replace('-', '', $request->input('sdate', ''));
        $sale_types = [];
        $result = [];

        if($sale_month != '') {
            $sql = "
                select 
                    sale_type_cd, 
                    st.sale_type_nm, 
                    st.sale_apply, 
                    st.amt_kind,
                    st.sale_amt, 
                    st.sale_per
                from sale_type_apply
                    inner join sale_type st on st.idx = sale_type_cd
                where apply_date = :apply_date and apply_yn = 'Y'
            ";
            $sale_types = DB::select($sql, ['apply_date' => $sale_month]);
        }
		
        if($store_cd != '') {
            $sql = "
                select
                    s.idx as sale_type_cd,
                    s.sale_kind,
                    s.sale_type_nm,
                    '$sale_month' as apply_date, 
                    ifnull(sta.apply_yn, 'N') as apply_yn
                from sale_type s
                    left outer join sale_type_apply sta on sta.sale_type_cd = s.idx and sta.apply_date = '$sale_month'
                where s.use_yn = 'Y'
                order by s.sale_kind
            ";
            $result = DB::select($sql);
        }
		
		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($result),
                'sale_types' => $sale_types,
			],
			'body' => $result
		]);
	}
}
