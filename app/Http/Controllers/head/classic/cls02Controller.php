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
		$state_sql = "
			select
				cc.code, cc.value1
			from classic_code cc
			where cc.kind = 'dm_state'
			order by cc.code
		";
		$states = DB::select($state_sql);

		$reserve_sql = "
			select 
				cdr.passwd, cdr.name1, cdr.name2, cdr.mobile, cdr.email, cdr.regist_number, cdr.state, cdr.s_dm_date, cdr.s_dm_type, cdr.e_dm_date, cdr.e_dm_type
				, cc.value1 as s_type_nm
				, ccd.value1 as e_type_nm
			from classic_dm_reserve cdr
				right outer join classic_code cc on cc.kind = 'dm_type' and cc.code = cdr.s_dm_type
				right outer join classic_code ccd on ccd.kind = 'dm_type' and ccd.code = cdr.e_dm_type
			where cdr.regist_number = :regist_number
		";
		$reserve = DB::selectOne($reserve_sql, ['regist_number' => $rg_no]);

		$dates_sql = "
			select 
				cc.code, cc.value3, cc.value1
			from classic_code cc
			where cc.kind in ('s_dm_date', 'e_dm_date')
		";
		$dates = DB::select($dates_sql);

		$types_sql = "
			select 
				cd.dm_type, cc.code, cc.value1, cd.dm_date, cd.dm_cnt, cd.reserve_cnt
			from classic_dm cd
				inner join classic_code cc on cc.kind='dm_type' and cc.code = cd.dm_type
		";
		$types = DB::select($types_sql);

		$dm_date_sql = "
			select 
				dm_date 
			from classic_dm
			group by dm_date
		";
		$dm_dates = DB::select($dm_date_sql);

		$dm_sql = "";
		foreach ($dm_dates as $dm_date) {
			$d = $dm_date->dm_date;
			$dm_sql .= " , sum(if(d.dm_date = '$d', d.reserve_cnt, 0)) as 'reserve_$d' ";
		}

		$sql = "
			select c.code as room_cd, c.value1 as room_nm, d.dm_date, d.dm_cnt
				$dm_sql
			from classic_code c
				left outer join classic_dm d on d.dm_type = c.code
			where c.kind = 'dm_type' and c.code <> '0'
			group by c.code
		";
		$dm_status = DB::select($sql);

		$values = [
			'states'		=> $states,
			'reserve' 		=> $reserve,
			'dates'			=> $dates,
			'types'			=> $types,
			'dm_dates'		=> $dm_dates,
			'dm_status'		=> $dm_status,
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

	public function update(Request $request)
	{
		$regist_number	= $request->input("regist_number");
		$passwd			= $request->input("passwd");
		$state			= $request->input("state");
		$name1			= $request->input("name1");
		$name2			= $request->input("name2");
		$mobile			= $request->input("mobile");
		$email			= $request->input("email");
		
		$s_dm_date 		= $request->input("s_dm_date");
		$s_dm_type 		= $request->input("s_dm_type");
		
		$e_dm_date 		= $request->input("e_dm_date");
		$e_dm_type 		= $request->input("e_dm_type");

		$prev_query	= "
			select 
				s_dm_date
				, s_dm_type
				, e_dm_date
				, e_dm_type
			from classic_dm_reserve 
			where regist_number = :regist_number
		";
		$prev = DB::selectOne($prev_query, ['regist_number' => $regist_number]);

		$prev_sdate = $prev->s_dm_date;
		$prev_stype = $prev->s_dm_type;
		$prev_edate = $prev->e_dm_date;
		$prev_etype = $prev->e_dm_type;

		try {
			DB::beginTransaction();

			$sql = "
				update classic_dm_reserve set 
					name1 = :name1, name2 = :name2, mobile = :mobile, email = :email, passwd = :passwd, state = :state, s_dm_date = :s_dm_date, s_dm_type = :s_dm_type, e_dm_date = :e_dm_date, e_dm_type = :e_dm_type, updt_dt = now()
				where regist_number = :regist_number
			";

			$dm_minus_sql = "
				update classic_dm set
					reserve_cnt = reserve_cnt - 1
				where dm_type = :dm_type and dm_date = :dm_date
			";

			$dm_plus_sql = "
				update classic_dm set
					reserve_cnt = reserve_cnt + 1
				where dm_type = :dm_type and dm_date = :dm_date
			";

			if($prev_stype != $s_dm_type || ($prev_sdate != $s_dm_date)&&($prev_stype == $s_dm_type)){
				DB::update($dm_minus_sql, ['dm_type' => $prev_stype, 'dm_date' => $prev_sdate]);
				DB::update($dm_plus_sql, ['dm_type' => $s_dm_type, 'dm_date' => $s_dm_date]);
			}

			if($prev_etype != $e_dm_type || ($prev_edate != $e_dm_date)&&($prev_etype == $e_dm_type)){
				DB::update($dm_minus_sql, ['dm_type' => $prev_etype, 'dm_date' => $prev_edate]);
				DB::update($dm_plus_sql, ['dm_type' => $e_dm_type, 'dm_date' => $e_dm_date]);
			}

			DB::update($sql, ['regist_number' => $regist_number, 'name1' => $name1, 'name2' => $name2, 'mobile' => $mobile, 'email' => $email, 'passwd' => $passwd, 'state' => $state, 's_dm_date' => $s_dm_date, 's_dm_type' => $s_dm_type, 'e_dm_date' => $e_dm_date, 'e_dm_type' => $e_dm_type]);
			DB::commit();

			$code = 200;
			$msg = "update success";
		} catch(Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json(['code' => $code, 'message' => $msg], $code);
	}
}
