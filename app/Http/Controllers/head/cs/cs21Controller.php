<?php

namespace App\Http\Controllers\head\cs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cs21Controller extends Controller
{
    public function index() {
        $mutable = now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'com_types' => SLib::getCodes('G_COM_TYPE'),
            'cs_types' => SLib::getCodes('CS_FORM2')
        ];

        return view( Config::get('shop.head.view') . '/cs/cs21',$values);
    }

    public function search(Request $req){

		$sdate	    = $req->input("sdate", "");
		$edate	    = $req->input("edate", "");
		$goods_nm	= $req->input("goods_nm", "");
		$com_type	= $req->input("com_type", "");
		$com_id	    = $req->input("com_id", "");
		$com_nm	    = $req->input("com_nm", "");
		$cs_type	= $req->input("cs_type", "");
		$req_nm	    = $req->input("req_nm", "");
		$user_nm	= $req->input("user_nm", "");
		$ord_no	    = $req->input("ord_no", "");
		$state	    = $req->input("state", "");

		$where = "";

		// 조건절 설정
		if($goods_nm != "")	    $where .= " and oo.goods_nm like '%$goods_nm%'";
		if($req_nm != "")		$where .= " and a.name like '$req_nm%'";
		if($user_nm != "")		$where .= " and om.user_nm like '$user_nm%'";
		if($cs_type != "")		$where .= " and cs_form = '$cs_type' ";
		if($com_id != "")		$where .= " and oo.com_id = '$com_id' ";
		if($com_type != "")	    $where .= " and c.com_type = '$com_type'";

		if($sdate != "")		$where .= " and a.regi_date >= '$sdate' ";
		if($edate != "")		$where .= " and a.regi_date <= '$edate' ";
		if($ord_no != "")		$where .= " and oo.ord_no = '$ord_no' ";
		if($state != "")		$where .= " and a.state = '$state' ";

		$sql = "
			select
				a.memo_no, a.regi_date,
				cd.code_val as cs_form,
				cd2.code_val as state,
				oo.ord_no, oo.ord_opt_no,
				oo.head_desc, oo.goods_nm, oo.goods_no, oo.goods_sub, om.user_nm, a.name, cd3.code_val as state2,
				replace(replace(m.memo, '\n',''),'\t',' ') memo,
				a.cmc_no
			from claim_memo_client a
				inner join order_opt oo on oo.ord_opt_no = a.ord_opt_no
				inner join order_mst om on om.ord_no = a.ord_no
				inner join claim_memo m on m.memo_no = a.memo_no
				left outer join code cd on cd.code_kind_cd = 'CS_FORM2' and cd.code_id = m.cs_form
				left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_STATE' and cd2.code_id = oo.ord_state
				inner join code cd3 on cd3.code_kind_cd = 'G_CLM_REQ_STATE' and cd3.code_id = a.state
				inner join company c on c.com_id = oo.com_id
			where 1=1 $where
			order by a.regi_date desc, oo.ord_no, a.memo_no
        ";
        
        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows,
            'sql' => $sql
        ]);
    }
}
