<?php

namespace App\Http\Controllers\partner\cs;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cs72Controller extends Controller
{
    // 월별 클레임 통계 컨트롤러
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m');

        $md_ids = DB::table('mgr_user')->where("md_yn",'=','Y')->orderBy("name")->get();

        $sale_places = DB::table('company')->where("com_type",'=','4')->orderBy("com_nm")->get();

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m"),
            'md_ids' => $md_ids,
            'sale_places' => $sale_places,
        ];
        return view( Config::get('shop.partner.view') . '/cs/cs72',$values);
    }

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        if(strlen($sdate) == 6){
            $sdate = $sdate . "01";
        }
        if(strlen($edate) == 6){
            $edate = $edate . "31";
        }

        $goods_nm		= $request->input("goods_nm");
        $sale_place	= $request->input("sale_place");
        $type_e				= $request->input("type_e");
		$type_r				= $request->input("type_r");
        $type_c				= $request->input("type_c");
        $req_nm				= $request->input("req_nm");


        $where = "";
        $where .= " and g.com_id='". Lib::quote($com_id)."' ";
        $clm_where = "";

        if($type_e != ""){
			$clm_where .= " c.clm_state in ( 40,50,60 ) ";
		}

		if($type_r != ""){
			if($clm_where != "") $clm_where .= " or ";
			$clm_where .= " c.clm_state in ( 41,51,61 ) ";
		}

		if($type_c != ""){
			if($clm_where != "") $clm_where .= " or ";
			$clm_where .= " c.clm_state = -10 ";
		}

		if($clm_where != ""){
			$where .= " and ( $clm_where ) ";
		}

		if($goods_nm != ""){
		    $where .= " and g.goods_nm like '%". Lib::quote($goods_nm)."%' ";
		}

		if($sale_place != ""){
			$where .= " and o.sale_place = '". Lib::quote($sale_place)."' ";
		}

		if($req_nm != ""){
			$where .= " and c.req_nm like '". Lib::quote($req_nm)."%' ";
        }


        $csql = "
			select code_id,if('kor' = 'kor',code_val,code_val_eng) as code_val
			from code where code_kind_cd = 'G_CLM_REASON' and code_id <> 'K'
			order by code_seq
		";

        $menus = DB::select($csql);

        $claim_menu = "";
        $claim_no_str = "0";

		foreach ($menus as $menu) {

			$claim_reason = $menu->code_id;
			$claim_nm = $menu->code_val;

            $claim_menu .= " ,ifnull(sum(if(clm_reason = '$claim_reason',cnt,0)),0) as '$claim_nm' \n";
            $claim_no_str .= ",$claim_reason";

        }

        


        $sql = "
                select
                    a.d
                    ,ifnull(sum(if(clm_reason in($claim_no_str) ,cnt,0)),0) as '합계'
                    $claim_menu
                from
                    (select date_format(d,'%Y%m') as d
                        from mdate where d >='$sdate' and d <='$edate' group by date_format(d,'%Y%m')) a left outer join (
                        select
                            date_format(c.req_date,'%Y%m') as req_date,c.clm_reason,count(*) as cnt
                        from claim c inner join order_opt o on c.ord_opt_no = o.ord_opt_no
                                inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                        where req_date >= '$sdate' and req_date <= date_add('$edate',interval 1 day)
                            $where
                        group by date_format(req_date,'%Y%m'),clm_reason
                    ) c on a.d = c.req_date
                group by a.d
                order by a.d desc
            ";

        $result = DB::select($sql);
        //echo "<pre>$sql</pre>";exit;


        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

}
