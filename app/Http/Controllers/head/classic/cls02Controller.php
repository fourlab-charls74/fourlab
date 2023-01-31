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

	public function show(Request $request, $rg_no) 
	{
		$state_query = "
			select
				cc.code, cc.value1
			from classic_code cc
			where cc.kind = 'dm_state'
			order by cc.code
		";
		$states = DB::select($state_query);

		$reserve_query = "
			select 
				cdr.passwd, cdr.name1, cdr.name2, cdr.mobile, cdr.email, cdr.regist_number, cdr.state, cdr.s_dm_date, cdr.s_dm_type, cdr.e_dm_date, cdr.e_dm_type
				, cc.value1 as s_type_nm
				, ccd.value1 as e_type_nm
			from classic_dm_reserve cdr
				right outer join classic_code cc on cc.kind = 'dm_type' and cc.code = cdr.s_dm_type
				right outer join classic_code ccd on ccd.kind = 'dm_type' and ccd.code = cdr.e_dm_type
			where cdr.regist_number = :rg_num
		";
		$reserve = DB::selectOne($reserve_query, ['rg_num' => $rg_no]);


		$date_query = "
			select 
				cd.dm_type, cc.value1, cd.dm_date, cd.dm_cnt, cd.reserve_cnt
			from classic_dm cd
				inner join classic_code cc on cc.kind='dm_type' and cc.code = cd.dm_type
		";
		$dms = DB::select($date_query);

		$rsv_date_query = "
			select cc.code, cc.value3
			from classic_code cc
			where cc.kind like '%_dm_date'
		";
		$rsv_date = DB::select($rsv_date_query);

		$room_status_query = "
			select 
				cd.dm_type, cd.dm_date, cd.dm_cnt, cd.reserve_cnt
				, cc.value3
				, ccd.value1
			from classic_dm cd
				inner join classic_code cc on cc.kind like '%_dm_date' and cd.dm_date = cc.code
				inner join classic_code ccd on ccd.kind = 'dm_type' and cd.dm_type = ccd.code
		";
		$room_status = DB::select($room_status_query);

		$values = [
			'states'		=> $states,
			'reserve' 		=> $reserve,
			'dms'			=> $dms,
			'rsv_date'		=> $rsv_date,
			'room_status'	=> $room_status
		];

		return view( Config::get('shop.head.view') . '/classic/cls02_show', $values );
	}

	public function state_update(Request $request) 
	{
		$error_code	= "200";
		$result_msg	= "";

		$datas	= $request->input('data');
		$datas	= json_decode($datas);
		$s_state = $request->input('s_state');

        try {
            DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data = (array)$datas[$i];
				$regist_number = $data["regist_number"];

				$sql = "
					update classic_dm_reserve set
						state = :s_state
					where regist_number = :regist_number
				";

				DB::update($sql, ['s_state' => $s_state, 'regist_number' => $regist_number]);
			}

			DB::commit();
        } catch(Exception $e) {
            DB::rollback();

			$error_code	= "500";
			$result_msg	= "데이터 업데이트 오류";
		}

		return response()->json([
			"code"			=> $error_code,
			"result_msg"	=> $result_msg
		]);
	}
}
