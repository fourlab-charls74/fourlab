<?php

namespace App\Http\Controllers\head\classic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class cls02Controller extends Controller
{
	public function index(Request $request) 
	{
		// $today = date("Y-m-d");
		// $edate = $today;
		// $sdate = date('Y-m-d', strtotime(-3 .'month'));

		// $request_sdate = $request->input("sdate");
		// if($request_sdate != '') {
		// 	$sdate = date_format(date_create($request_sdate), "Y-m-d");
		// 	$edate = date_format(date_create($request->input("edate")), "Y-m-d");
		// }
		
		// $mst_query = "
		// 		select idx, title, start_date, end_date 
		// 		from evt_mst 
		// 		order by idx desc
		// ";

		// $evt_mst = DB::select($mst_query);
		// $here = 'here';

		// $values = [
		// 	'here' => $here
		// ];
		// return view( Config::get('shop.head.view') . '/classic/cls02',$values);
		return view( Config::get('shop.head.view') . '/classic/cls02');
	}

	public function search(Request $request)
	{
		$regist_number	= $request->input('regist_number');
		$mobile			= $request->input('mobile');
		$email			= $request->input('email');
		$name1			= $request->input('name1');
		$state 			= $request->input('state');
        $s_dm_date 		= $request->input('s_dm_date');
        $e_dm_date 		= $request->input('e_dm_date');

		$page			= $request->input("page",1);
		$page_size 		= $request->input("limit",100);

		if ($page < 1 or $page == "") $page = 1;

		$total = 0;
        $page_cnt = 0;

		$where = "";
		if ($regist_number != "") $where .= " and a.regist_number like '%" . Lib::quote($regist_number) . "%' ";
		if ($mobile != "") $where .= " and a.mobile like '%" . Lib::quote($mobile) . "%' ";
		if ($email != "") $where .= " and a.email like '%" . Lib::quote($email) . "%' ";
		if ($name1 != "") $where .= " and a.name1 like '%" . Lib::quote($name1) . "%' ";
		if ($state != "") $where .= " and a.state = '$state' ";
		if ($s_dm_date != "") $where .= " and a.s_dm_date = '$s_dm_date' ";
		if ($e_dm_date != "") $where .= " and a.e_dm_date = '$e_dm_date' ";

		if ($page == 1) {
			$sql = "
				select 
					count(*) as cnt
				from classic_dm_reserve a
				where 1=1 $where
			";
			$row = DB::selectOne($sql);
			$total = $row->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($total - 1)/$page_size) + 1;
			$startno = ($page - 1) * $page_size;
		} else {
			$startno = ($page - 1) * $page_size;
		}

		$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page);

		$sql = "
			select 
				a.regist_number, a.name1, a.name2, a.mobile, a.email, a.confirm_yn, a.confirm_dt, a.reg_dt, a.updt_dt
				, ba.value1 as state_nm
				, bb.value3 as s_dm_date_nm
				, bc.value3 as s_dm_type_nm
				, bd.value3 as e_dm_date_nm
				, be.value3 as e_dm_type_nm
			from classic_dm_reserve a
				left outer join classic_code ba on ba.kind = 'dm_state' and a.state = ba.code
				left outer join classic_code bb on bb.kind = 's_dm_date' and a.s_dm_date = bb.code
				left outer join classic_code bc on bc.kind = 'dm_type' and a.s_dm_type = bc.code
				left outer join classic_code bd on bd.kind = 'e_dm_date' and a.e_dm_date = bd.code
				left outer join classic_code be on be.kind = 'dm_type' and a.e_dm_type = be.code
			where 1=1 $where
			order by a.reg_dt
			limit $startno, $page_size
		";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => $arr_header,
			"body" => $result
		]);
	}

	//상세페이지 select box ( 예약 숫자 & 예약 가능 숫자 ) 작업 중
	public function show(Request $request, $rg_no) 
	{
		$cnt_query = "
			select
				a.code, a.value1
			from classic_code a
			where
				a.kind = 'dm_type'
		";
		$cnts = DB::select($cnt_query);

		$reserve_query = "
			select 
				a.passwd, a.name1, a.name2, a.mobile, a.email, a.regist_number
				, a.state
				, a.s_dm_date, a.s_dm_type
				, a.e_dm_date, a.e_dm_type
			from classic_dm_reserve a
			where a.regist_number = '$rg_no'
		";

		$reserve_detail = DB::selectOne($reserve_query);

		$values = [
			'reserve' 	=> $reserve_detail,
			'cnts' 		=> $cnts
		];

		return view( Config::get('shop.head.view') . '/classic/cls02_show', $values );
	}
}
