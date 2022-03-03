<?php

namespace App\Http\Controllers\partner\settle;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class stl01Controller extends Controller
{
    // 정산내역
    public function index() {
        $immutable = CarbonImmutable::now();
        $sdate	= $immutable->sub(2, 'year')->endOfYear()->format('Y-m-d');
		$edate	= $immutable->sub(1, 'year')->endOfYear()->format('Y-m-d');
        $values = [
            'sdate' => $sdate,
            'edate' => $edate,
        ];
        return view( Config::get('shop.partner.view') . '/settle/stl01', $values);
	}

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));


		$closed_state = $request->input("closed_state");

		$where = "";

        if($closed_state != ""){
			$where .= " and a.closed_yn = '".$closed_state."' ";
		}

		if($com_id != ""){
			$where .= " and a.com_id = '".$com_id."' ";
		}

		$tax_state = "미발행";
        $sql = "
			select
				concat_ws('~',date_format(a.sday,'%Y%m%d'),date_format(a.eday,'%Y%m%d')) as date,
				b.com_nm,cd.code_val as margin_type,
				a.sale_amt,a.clm_amt,a.coupon_amt, ( a.sale_amt + a.clm_amt - a.coupon_amt ) as sale_clm_cpn_amt,
				a.dlv_amt,a.allot_amt,a.etc_amt,a.sale_net_amt,a.fee,a.acc_amt,
				date_format(a.pay_day,'%Y%m%d') as pay_date,a.closed_yn,
				ifnull(tax_state.code_val,'$tax_state') as tax_state,
				a.idx,a.com_id,a.sday,a.eday
			from
				account_closed a inner join company b on ( a.com_id = b.com_id )
					inner join code cd on cd.code_kind_cd = 'G_MARGIN_TYPE' and b.margin_type = cd.code_id
					left outer join tax t on a.tax_no = t.idx
					left outer join code tax_state on tax_state.code_kind_cd = 'G_TAX_STATE' and t.state = tax_state.code_id
			where
				a.com_id = '$com_id' and a.sday <= '$edate' and a.eday >= '$sdate' $where
			order by
				a.sday desc
			";

        //echo "<pre>$sql</pre>";exit;
		$result = DB::select($sql);
        //dd($result);

        return response()->json([
                "code" => 200,
                "head" => array(
                    "total" => count($result)
                ),
                "body" => $result
            ]
        );
    }

    // 정산 상세 내역
    public function detail($idx) {
		//$result = $this->detail_search($idx);
		$acc_info = $this->get_acc_closed_info($idx);
		//dd($acc_info);

        $values = [
			'idx' => $idx,
			'acc_info' => $acc_info,
        ];
        return view( Config::get('shop.partner.view') . '/settle/stl01_detail',$values);
    }

    // 정산 상세 내역 검색
    public function detail_search(Request $request, $idx) {

		$page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $limit = $request->input('limit',100);

		$page_size = $limit;
        $startno = ($page-1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = /** @lang text */
                "
				select count(*) as total
				from
				account_closed closed
				inner join account_closed_list w on w.acc_idx = closed.idx
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst m on o.ord_no = m.ord_no
				inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				inner join payment p on m.ord_no = p.ord_no
				left outer join code cd on cd.code_kind_cd = 'G_ORD_STATE' and cd.code_id = o.ord_state
				left outer join code cd2 on cd2.code_kind_cd = 'G_CLM_STATE' and cd2.code_id = o.clm_state
				left outer join code opt_type on opt_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = opt_type.code_id
				left outer join code acc_type on acc_type.code_kind_cd = 'G_ACC_TYPE' and w.type = acc_type.code_id
				left outer join code pay_type on pay_type.code_kind_cd = 'G_PAY_TYPE' and p.pay_type = pay_type.code_id
				where closed.idx = '$idx'
				order by w.state_date
			";
            //echo "<pre>$query $com_id</pre>";
            $row = DB::select($query,["idx" => $idx]);
            //$row = DB::select($query);
            $total = $row[0]->total;
            if($total > 0){
                $page_cnt=(int)(($total-1)/$page_size) + 1;
            }
        }

        $sql = "
			select
				acc_type.code_val as type,w.state_date,o.ord_no
				, if((select count(*) from order_opt where ord_no = o.ord_no) > 1, 'Y','') as multi_order
				, if(o.coupon_no <>0,(select coupon_nm from coupon where coupon_no = o.coupon_no),'') as coupon_nm
				, g.goods_nm
				, replace(o.goods_opt,'^',' : ') as opt_nm
				, g.style_no
				, opt_type.code_val as opt_type, m.user_nm, pay_type.code_val as pay_type
				, w.qty as qty
				, w.sale_amt, w.clm_amt, w.coupon_amt, ( w.sale_amt + w.clm_amt - w.coupon_amt ) as sale_clm_cpn_amt
				, w.dlv_amt, w.allot_amt, w.etc_amt /* 기타정산액 처리 */
				, w.sale_net_amt
				, w.fee_ratio , w.fee
				, w.acc_amt
				, cd.code_val as ord_state ,cd2.code_val as clm_state
				, date_format(o.ord_date,'%Y%m%d') as ord_date
				, date_format(o.dlv_end_date,'%Y%m%d') as dlv_end_date
				, if(o.clm_state in (60,61), (select date_format(max(end_date),'%Y%m%d') clm_end_date from claim where ord_opt_no = o.ord_opt_no),'') as clm_end_date
				, w.bigo
				, o.ord_opt_no
				, g.goods_no ,g.goods_sub
			from
				account_closed closed
				inner join account_closed_list w on w.acc_idx = closed.idx
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst m on o.ord_no = m.ord_no
				inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				inner join payment p on m.ord_no = p.ord_no
				left outer join code cd on cd.code_kind_cd = 'G_ORD_STATE' and cd.code_id = o.ord_state
				left outer join code cd2 on cd2.code_kind_cd = 'G_CLM_STATE' and cd2.code_id = o.clm_state
				left outer join code opt_type on opt_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = opt_type.code_id
				left outer join code acc_type on acc_type.code_kind_cd = 'G_ACC_TYPE' and w.type = acc_type.code_id
				left outer join code pay_type on pay_type.code_kind_cd = 'G_PAY_TYPE' and p.pay_type = pay_type.code_id
			where closed.idx = '$idx'
			order by w.state_date
			limit $startno, $page_size
		";

        $result = DB::select($sql);

        return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
			),
			"body" => $result
			]
		);
	}

	private function get_acc_closed_info($idx) {// 정산내역 대상일자

        $sql = "
			select
				*
			from
				account_closed
			where idx = '$idx'
		";

        $result = DB::select($sql);

        return $result[0];
    }
}
