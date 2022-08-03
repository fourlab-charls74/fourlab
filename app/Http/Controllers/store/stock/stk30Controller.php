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

use App\Models\Conf;

class stk30Controller extends Controller
{
    public function index()
	{
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();

		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'storages'      => $storages,
            'store_types'	=> SLib::getCodes("STORE_TYPE"),	// 매장구분
            'sr_states'	    => SLib::getCodes("SR_CODE"),	// 반품상태
            'sr_reasons'	=> SLib::getCodes("SR_REASON"),	// 반품사유
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/stock/stk30', $values);
	}

    public function search(Request $request)
    {
		$where = "";
        $orderby = "";

        $sdate      = $request->input("sdate", now()->sub(1, 'week')->format('Ymd'));
        $edate      = $request->input("edate", date("Ymd"));
        $sr_state   = $request->input("sr_state", "");
        $sr_reason  = $request->input("sr_reason", "");
        $storage_cd = $request->input("storage_cd", "");
        $store_type = $request->input("store_type", "");
        $store_nm   = $request->input("store_nm", "");
        $store_no   = $request->input("store_no", "");
        
        // where
        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);
        $where .= "
            and cast(sr.sr_date as date) >= '$sdate' 
            and cast(sr.sr_date as date) <= '$edate'
        ";
        if($sr_state != "")     $where .= " and sr.sr_state = '" . $sr_state . "'";
        if($sr_reason != "")    $where .= " and sr.sr_reason = '" . $sr_reason . "'";
        if($storage_cd != "")   $where .= " and sr.storage_cd = '" . $storage_cd . "'";
        if($store_type != "")   $where .= " and store.store_type = '" . $store_type . "'";
        if($store_no != "")     $where .= " and sr.store_cd = '" . $store_no . "'";

        // ordreby
        $ord_field  = $request->input("ord_field", "sr.sr_date");
        $ord        = $request->input("ord", "desc");
        $orderby    = sprintf("order by %s %s", $ord_field, $ord);
        
        // pagination
        $page       = $request->input("page", 1);
        $page_size  = $request->input("limit", 100);
        if ($page < 1 or $page == "") $page = 1;
        $startno    = ($page - 1) * $page_size;
        $limit      = " limit $startno, $page_size ";

        // search
		$sql = "
            select
                sr.sr_cd,
                sr.storage_cd,
                storage.storage_nm,
                sr.store_cd,
                store.store_nm,
                store.store_type,
                sc.code_val as store_type_nm,
                sr.sr_date,
                sr.sr_kind,
                sr.sr_state,
                c.code_val as sr_state_nm,
                sr.sr_reason,
                co.code_val as sr_reason_nm,
                sr.comment
            from storage_return sr
                inner join storage on storage.storage_cd = sr.storage_cd
                inner join store on store.store_cd = sr.store_cd
                inner join code c on c.code_kind_cd = 'SR_CODE' and c.code_id = sr.sr_state
                inner join code co on co.code_kind_cd = 'SR_REASON' and co.code_id = sr.sr_reason
                inner join code sc on sc.code_kind_cd = 'STORE_TYPE' and sc.code_id = store.store_type
            where 1=1 $where
            $orderby
            $limit
		";
		$result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                from storage_return sr
                    inner join storage on storage.storage_cd = sr.storage_cd
                    inner join store on store.store_cd = sr.store_cd
                    inner join code c on c.code_kind_cd = 'SR_CODE' and c.code_id = sr.sr_state
                    inner join code co on co.code_kind_cd = 'SR_REASON' and co.code_id = sr.sr_reason
                where 1=1 $where
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			],
			"body" => $result
		]);
    }

    // 창고반품 등록팝업 오픈
    public function show() 
    {
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();

        $values = [
            'sdate'         => date("Y-m-d"),
            'storages'      => $storages,
        ];
        return view(Config::get('shop.store.view') . '/stock/stk30_show', $values);
    }
}
