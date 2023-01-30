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
		if ($regist_number != "") $where .= " and cdr.regist_number like '%" . Lib::quote($regist_number) . "%' ";
		if ($mobile != "") $where .= " and cdr.mobile like '%" . Lib::quote($mobile) . "%' ";
		if ($email != "") $where .= " and cdr.email like '%" . Lib::quote($email) . "%' ";
		if ($name1 != "") $where .= " and cdr.name1 like '%" . Lib::quote($name1) . "%' ";
		if ($state != "") $where .= " and cdr.state = '$state' ";
		if ($s_dm_date != "") $where .= " and cdr.s_dm_date = '$s_dm_date' ";
		if ($e_dm_date != "") $where .= " and cdr.e_dm_date = '$e_dm_date' ";

		if ($page == 1) {
			$sql = "
				select 
					count(*) as cnt
				from classic_dm_reserve cdr
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
				cdr.regist_number, cdr.name1, cdr.name2, cdr.mobile, cdr.email, cdr.confirm_yn, cdr.confirm_dt, cdr.reg_dt, cdr.updt_dt
				, cc1.value1 as state_nm
				, cc2.value3 as s_dm_date_nm
				, cc3.value3 as s_dm_type_nm
				, cc4.value3 as e_dm_date_nm
				, cc5.value3 as e_dm_type_nm
			from classic_dm_reserve cdr
				inner join classic_code cc1 on cc1.kind = 'dm_state' and cdr.state = cc1.code
				inner join classic_code cc2 on cc2.kind = 's_dm_date' and cdr.s_dm_date = cc2.code
				inner join classic_code cc3 on cc3.kind = 'dm_type' and cdr.s_dm_type = cc3.code
				inner join classic_code cc4 on cc4.kind = 'e_dm_date' and cdr.e_dm_date = cc4.code
				inner join classic_code cc5 on cc5.kind = 'dm_type' and cdr.e_dm_type = cc5.code
			where 1=1 $where
			order by cdr.reg_dt
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
		//기존
		$cnt_query = "
			select
				cc.code, cc.value1
			from classic_code cc	
			where
				cc.kind = 'dm_type'
		";
		$cnts = DB::select($cnt_query);

		//new
		$s_dm_date = DB::table('classic_dm_reserve')
                        -> where('regist_number', $rg_no)
                        -> value('s_dm_date');

		$e_dm_date = DB::table('classic_dm_reserve')
                        -> where('regist_number', $rg_no)
                        -> value('e_dm_date');

		$dm_date_query = "
			select
				cd.dm_type, cd.dm_cnt, cd.reserve_cnt
				, b.value1
			from classic_dm cd
				inner join classic_code b on cd.dm_type = b.code
			where
				cd.dm_date = :dm_date
			order by cd.dm_type
		";

		$sdms = DB::select($dm_date_query, ['dm_date' => $s_dm_date]);

		$edms = DB::select($dm_date_query, ['dm_date' => $e_dm_date]);

		$reserve_query = "
			select 
				cdr.passwd, cdr.name1, cdr.name2, cdr.mobile, cdr.email, cdr.regist_number
				, cdr.state
				, cdr.s_dm_date, cdr.s_dm_type
				, cdr.e_dm_date, cdr.e_dm_type
			from classic_dm_reserve cdr
			where cdr.regist_number = '$rg_no'
		";

		$reserve_detail = DB::selectOne($reserve_query);

		$values = [
			'reserve' 	=> $reserve_detail,
			'cnts' 		=> $cnts,
			'sdms'		=> $sdms,
			'edms'		=> $edms,
		];

		return view( Config::get('shop.head.view') . '/classic/cls02_show', $values );
	}
	public function state_update(Request $request) 
	{
		
	}
}
