<?php

namespace App\Http\Controllers\head\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class mem04Controller extends Controller
{
	public function index() {
		$today = date("Y-m-d");
        $edate = $today;
        $sdate = date('Y-m-d', strtotime(-1 .'month'));

		$sql = "
			select sum(m.point) as total_point
			from member m
			where m.out_yn = 'N'
		";
		$tot_point_row = DB::selectOne($sql);
		$total_point = $tot_point_row->total_point; 

		$values = [
			"edate" => $edate,
            "sdate" => $sdate,
			"total_point"	=> $total_point
		];
		return view( Config::get('shop.head.view') . '/member/mem04',$values);
	}

	public function search(Request $request){
		$edate = $request->input("edate");
		$sdate = $request->input("sdate");
		
		$edate = str_replace("-", "", $edate);
		$sdate = str_replace("-", "", $sdate);
		$array = array();

		$sql = "
			select
				a.point_date,
				sum(if(t.point_st = '적립' and t.point_kind = '1', t.spoint, 0)) as g_member, -- 회원가입
				sum(if(t.point_st = '적립' and t.point_kind = '2', t.spoint, 0)) as g_order, -- 주문
				sum(if(t.point_st = '적립' and t.point_kind = '3', t.spoint, 0)) as g_gift, -- 상품권
				sum(if(t.point_st = '적립' and t.point_kind = '5', t.spoint, 0)) as g_change, -- 교환 X
				sum(if(t.point_st = '적립' and t.point_kind = '6', t.spoint, 0)) as g_refund, -- 환불 X
				sum(if(t.point_st = '적립' and t.point_kind = '7', t.spoint, 0)) as g_cancel, -- 주문취소
				sum(if(t.point_st = '적립' and t.point_kind = '8', t.spoint, 0)) as g_overpay, -- 초과입금
				sum(if(t.point_st = '적립' and t.point_kind = '9', t.spoint, 0)) as g_restock,	-- 재입고알림
				sum(if(t.point_st = '적립' and t.point_kind = '10', t.spoint, 0)) as g_recommand, -- 추천아이디
				sum(if(t.point_st = '적립' and t.point_kind = '11', t.spoint, 0)) as g_review, -- 상품후기작성
				sum(if(t.point_st = '적립' and t.point_kind = '12', t.spoint, 0)) as g_etc, -- 기타
				sum(if(t.point_st = '적립' and t.point_kind = '13', t.spoint, 0)) as g_event, -- 이벤트
				sum(if(t.point_st = '적립', t.spoint, 0)) as g_sum, -- 적립계

				sum(if(t.point_st = '사용' and t.point_kind = '1', t.spoint, 0)) as t_member, -- 회원가입 X
				sum(if(t.point_st = '사용' and t.point_kind = '2', t.spoint, 0)) as t_order, -- 주문
				sum(if(t.point_st = '사용' and t.point_kind = '3', t.spoint, 0)) as t_gift, -- 상품권
				sum(if(t.point_st = '사용' and t.point_kind = '5', t.spoint, 0)) as t_change, -- 교환
				sum(if(t.point_st = '사용' and t.point_kind = '6', t.spoint, 0)) as t_refund, -- 환불
				sum(if(t.point_st = '사용' and t.point_kind = '7', t.spoint, 0)) as t_cancel, -- 주문취소 X
				sum(if(t.point_st = '사용' and t.point_kind = '8', t.spoint, 0)) as t_overpay, -- 초과입금
				sum(if(t.point_st = '사용' and t.point_kind = '10', t.spoint, 0)) as t_recommand, -- 추천아이디 X
				sum(if(t.point_st = '사용' and t.point_kind = '11', t.spoint, 0)) as t_review, -- 상품후기작성 X
				sum(if(t.point_st = '사용' and t.point_kind = '12', t.spoint, 0)) as t_etc, -- 기타
				sum(if(t.point_st = '사용' and t.point_kind = '13', t.spoint, 0)) as t_event,	-- 이벤트
				sum(if(t.point_st = '사용', t.spoint, 0)) as t_sum, -- 사용계
				sum(t.spoint) as gt_sum -- 합계
			from (
				select d as point_date from mdate where d >='$sdate' and d <= '$edate' order by point_date desc
			) a left outer join (

				select date_format(p.point_date,'%Y%m%d') as point_date, p.point_st, p.point_kind, sum(p.point) as spoint
				from point_list p
					inner join member m on m.user_id = p.user_id and m.out_yn = 'N'
				where
					p.point_date >= '$sdate'
					and p.point_date < DATE_ADD('$edate', INTERVAL 1 DAY)
					and p.point_status = 'Y'
				group by date_format(p.point_date,'%Y%m%d'), p.point_st, p.point_kind

			) t on a.point_date = t.point_date -- COLLATE utf8_general_ci
			group by a.point_date desc
		";

		$result = DB::select($sql);
		//print_r($result);
		//echo "<br>";
		
		foreach($result as $row){
			$point_date		= $row->point_date;
			$g_member		= $row->g_member; // -- 회원가입
			$g_order		= $row->g_order; // -- 주문
			$g_gift			= $row->g_gift; // -- 상품권
			$g_change		= $row->g_change; // -- 교환 X
			$g_refund		= $row->g_refund; // -- 환불 X
			$g_cancel		= $row->g_cancel; // -- 주문취소
			$g_overpay		= $row->g_overpay; // -- 초과입금
			$g_restock		= $row->g_restock; //	-- 재입고알림
			$g_recommand	= $row->g_recommand; // -- 추천아이디
			$g_review		= $row->g_review; // -- 상품후기작성
			$g_etc			= $row->g_etc; // -- 기타
			$g_event		= $row->g_event; // -- 이벤트
			$g_sum			= $row->g_sum; // -- 적립계

			$t_member		= $row->t_member; // -- 회원가입 X
			$t_order		= $row->t_order; // -- 주문
			$t_gift			= $row->t_gift; // -- 상품권
			$t_change		= $row->t_change; // -- 교환
			$t_refund		= $row->t_refund; // -- 환불
			$t_cancel		= $row->t_cancel; // -- 주문취소 X
			$t_overpay		= $row->t_overpay; // -- 초과입금
			$t_recommand	= $row->t_recommand; // -- 추천아이디 X
			$t_review		= $row->t_review; // -- 상품후기작성 X
			$t_etc			= $row->t_etc; // -- 기타
			$t_event		= $row->t_event; //	-- 이벤트
			$t_sum			= $row->t_sum; // -- 사용계

			$gt_sum			= $row->gt_sum; // -- 합계


			$array[] = array(
				"date" 	=> $point_date,
				"g_member" => $g_member,
				"g_order" => $g_order,
				"g_refund" => $g_refund,
				"g_change" => $g_change,
				"g_cancel" => $g_cancel,
				"g_review" => $g_review,
				"g_else" => $g_sum - ($g_member+$g_order+$g_refund+$g_change+$g_cancel+$g_review) ,
				"g_sum" => $g_sum,
				"t_order" => $t_order,
				"t_claim" => $t_refund + $t_change,
				"t_else" => $t_sum - ($t_order+$t_refund+$t_change),
				"t_sum" => $t_sum,
				"gt_sum" => $gt_sum
			);
		}
		
		


		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($array),
				"page" => 1,
				"page_cnt" => count($array),
				"page_total" => 1
			),
			"body" => $array
		]);

	}
}
