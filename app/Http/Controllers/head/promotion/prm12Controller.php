<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm12Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'year')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view( Config::get('shop.head.view') . '/promotion/prm12',$values);
    }

    public function search(Request $request)
    {
        $sdate      = $request->input('sdate',Carbon::now()->sub(1, 'year')->format('Ymd'));
        $edate      = $request->input('edate',date("Ymd"));
        $title      = $request->input('s_title');
        $evt_idx    = $request->input('s_evt_idx');
		$order_no	= $request->input('s_order_no');
        $evt_state	= $request->input('s_evt_state');
		$pay_state	= $request->input('s_pay_state');
		$pay_ok		= $request->input('s_pay_ok');
		$user_nm	= $request->input('s_user_nm');
		$mobile		= $request->input('s_mobile');


        $where = "";
        if( $title != "" )		$where .= " and b.title like '%" . Lib::quote($title) . "%' ";
		if( $evt_idx != "" )	$where .= " and a.evt_idx = '$evt_idx' ";
		if( $order_no != "" )	$where .= " and a.order_no = '$order_no' ";
		if( $evt_state != "" )	$where .= " and a.evt_state = '$evt_state' ";
		if( $pay_state != "" )	$where .= " and d.pay_state = '$pay_state' ";
		if( $pay_ok != "" )		$where .= " and a.evt_state >= '10' ";
		if( $user_nm != "" )	$where .= " and a.user_nm like '%" . Lib::quote($user_nm) . "%' ";
		if( $mobile != "" )		$where .= " and a.mobile = '$mobile' ";

		$query	= "
			select
				a.evt_idx, b.title, a.order_no,
				case
					when a.evt_state = '1' then '입금예정'
					when a.evt_state = '5' then '접수후보'
					when a.evt_state = '9' then '후보결제대기'
					when a.evt_state = '10' then '접수완료'
					when a.evt_state = '20' then '확정대기'
					when a.evt_state = '30' then '확정완료'
					when a.evt_state = '-10' then '결제오류'
					when a.evt_state = '-20' then '신청취소'
					else '-'
				end as evt_state_nm,
				if(d.pay_state = '1', '입금','미입금') as pay_stat,
				c.kind, d.amount,
				d.buyr_name, a.mobile, a.regdate,
				a.evt_state, a.idx, a.seq
			from evt_payment d
			inner join evt_member a on a.order_no = d.order_no and a.seq = '1'
			inner join evt_mst b on a.evt_idx = b.idx
			inner join evt_order c on a.order_no = c.order_no
			where
				1 = 1
				and ( a.regdate >= :sdate and a.regdate < date_add(:edate,interval 1 day))
				$where
			order by a.idx desc
		";
        $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show($order_no)
	{
		// 사용하지 않음~
		//$order_no	= $request->input('order_no');
		/*
        $values = [
            'order_no' => $order_no,
        ];

		return view( Config::get('shop.head.view') . '/promotion/prm12_show',$values);
		*/
    }

}
