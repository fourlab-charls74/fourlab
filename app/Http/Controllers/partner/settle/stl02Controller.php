<?php

namespace App\Http\Controllers\partner\settle;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stl02Controller extends Controller
{
    // 정산내역
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(12, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
			'com_nms' => SLib::getCodes('com_nms'),
        ];
        return view( Config::get('shop.partner.view') . '/settle/stl02',$values);
	}

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;
        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

		$closed_state = $request->input("closed_state");

		$page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

		$where = "";

        if($closed_state != ""){
			$where .= " and a.closed_yn = '".$closed_state."' ";
		}

		$page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = /** @lang text */
                "
                select count(*) as total
                from account_closed a inner join company b on ( a.com_id = b.com_id )
				inner join code cd on cd.code_kind_cd = 'G_MARGIN_TYPE' and b.margin_type = cd.code_id
				left outer join tax t on a.tax_no = t.idx
				left outer join code tax_state on tax_state.code_kind_cd = 'G_TAX_STATE' and t.state = tax_state.code_id
			where
				a.sday >= '20110101' and a.sday <= '$edate' and a.eday >= '$sdate' $where
			order by
				a.acc_amt desc
				$limit
			";
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query,['com_id' => $com_id]);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $sql =
		"
		select
			a.closed_yn,concat_ws('~',a.sday,a.eday),
			b.com_nm,cd.code_val as margin_type,
			a.sale_amt,a.clm_amt,a.dc_amt,
			( a.coupon_amt - a.allot_amt ) as coupon_com_amt,
			a.dlv_amt, a.etc_amt,
			a.sale_net_taxation_amt, a.sale_net_taxfree_amt,a.sale_net_amt,a.tax_amt,
			a.fee, a.fee_dc_amt, a.fee_net, a.acc_amt, a.allot_amt,

			date_format(a.pay_day,'%Y%m%d'),
			ifnull(tax_state.code_val,'') as tax_state,
			a.idx,a.com_id,a.sday,a.eday
		from
			account_closed a inner join company b on ( a.com_id = b.com_id )
			inner join code cd on cd.code_kind_cd = 'G_MARGIN_TYPE' and b.margin_type = cd.code_id
			left outer join tax t on a.tax_no = t.idx
			left outer join code tax_state on tax_state.code_kind_cd = 'G_TAX_STATE' and t.state = tax_state.code_id
		where
			a.sday >= '20110101' and a.sday <= '$edate' and a.eday >= '$sdate' $where
		order by
			a.acc_amt desc
			$limit
		";

        //echo "<pre>$sql</pre>";exit;
		$result = DB::select($sql);
        //dd($result);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);
    }
}
