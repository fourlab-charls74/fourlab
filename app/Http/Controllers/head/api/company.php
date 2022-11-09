<?php

namespace App\Http\Controllers\head\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class company extends Controller
{


    public function getlist(Request $req){
		$com_id = $req->input('com_id', '');
		$com_nm = $req->input('com_nm', '');
		$use_yn = $req->input('use_yn', '');
		$com_type = $req->input('com_type', '');
		$wonboo = $req->input('wonboo', '');


        $where = "";

		if ($com_type != "") $where .= " and a.com_type = '$com_type' ";
		if ($com_id != "")	 $where .= " and a.com_id = '$com_id' ";
		if ($com_nm != "")	 $where .= " and a.com_nm like '%$com_nm%' ";
        if ($use_yn != "")	 $where .= " and a.use_yn = '$use_yn' ";
        
		$sql = "
            /* admin : standard/pop_company.php (1) */
            select 
                cd.code_val as com_type_nm
                , a.com_id
                , ifnull(com_nm,'-') as com_nm
                , ifnull(ceo,'-') as ceo
                , ifnull(biz_num,'-') as biz_num
                , ifnull(md_nm,'-') as md_nm
                , a.com_type
                , pay_fee
                , baesong_kind
                , baesong_info
                , margin_type
                , ifnull(coupon_ratio,0) as coupon_ratio
                , ifnull(dlv_amt, 0) as dlv_amt
            from company a 
                inner join code cd on cd.code_kind_cd = 'G_COM_TYPE' and cd.code_id = a.com_type
            where a.use_yn = 'Y'
                $where
            order by a.com_type, a.com_nm
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }
}





