<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm20Controller extends Controller
{
  //
	public function index() {
		$values = [
		];

		return view( Config::get('shop.head.view') . '/promotion/prm20',$values);
	}

	public function search(Request $request){
	  // 검색 파라미터
		$subject	= $request->input("subject");
		$use_yn		= $request->input("use_yn");
		$page		= $request->input("page", 1);

		// 조건절 설정
		$where = "";
		if($subject != "" ) $where .= sprintf(" and title like '%s' ","%$subject%");
		if($use_yn != "" ) $where .= sprintf(" and is_use = '%s'",$use_yn);

		if ($page < 1 or $page == "") $page = 1;
		$page_size = 100;

		$sql = "
			select
				count(idx) as cnt
			from event_attend
			where 1 = 1 $where
		";
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1) / $page_size) + 1;
		$startno = ($page-1) * $page_size;

		$sql = "
			select
				a.idx,a.title,a.start_dt,a.end_dt,attend_point_type,attend_point,first_attend_yn,first_attend_point,regular_attend_day,regular_attend_point,
				( select count(distinct(user_id)) from event_attend_member where event_idx = a.idx ) as attend_cnt,
				( select count(distinct(user_id)) from event_attend_member where event_idx = a.idx and attend = date_format(now(),'%Y%m%d') ) as attend_today_cnt,
				a.bet,
                a.support_point_yn,a.support_point_sday,a.support_point_eday,a.support_point,a.support_point_expireday,a.support_point_amt,
				( select sum(ifnull(support_point,0)) from event_attend_member where event_idx = a.idx and attend = date_format(now(),'%Y%m%d') ) as support_point_today_amt,                
				a.is_use,ut
			from event_attend a
			where
				1 = 1 $where
			order by a.idx desc
			limit $startno, $page_size
		";
		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($result),
				"page_total" => $page_cnt
			),
			"body" => $result
		]);

	}

	public function ViewMember($idx = '', Request $request){
        $edate = $request->input("edate");
        $sdate = $request->input("sdate");

        if($sdate == "" || $edate == ""){
            $sql = "
				select start_dt, end_dt from event_attend where idx = $idx
			";
            $row = DB::selectOne($sql);
            if($sdate == "") $sdate = $row->start_dt;
            if($edate == "") $edate = $row->end_dt;
        }

		$values = [

			"idx"	=> $idx,
			"sdate"	=> $sdate,
			"edate"	=> $edate
		];

		return view( Config::get('shop.head.view') . '/promotion/prm20_member',$values);
	}

	/*
		Function: MemberSearch
		 이벤트 출첵 참가자
	*/

	public function MemberSearch($idx = '', Request $request){
		$idx			= $request->input("idx");
		$userid			= $request->input("userid");
		$attd_cnt_from	= $request->input("attd_cnt_from");
		$attd_cnt_to	= $request->input("attd_cnt_to");
        $edate			= $request->input("edate");
        $sdate			= $request->input("sdate");
		$page			= $request->input("page", 1);

		// 조건절 설정
		$where = "";
		if ( $userid != "" ) $where .= sprintf(" and user_id = '%s' ",$userid);

		$having = "";
		if ( $attd_cnt_from != "" ) {
			$having .= sprintf(" having attend_cnt >= '%s' ",$attd_cnt_from);
		}
		if( $attd_cnt_to != "" ) {
			if($having == ""){
				$having .= sprintf(" having attend_cnt <= '%s' ",$attd_cnt_to);
			} else {
				$having .= sprintf(" and attend_cnt <= '%s' ",$attd_cnt_to);
			}
		}

        if ( $edate != "" ) {
            $where .= sprintf(" and attend <= '%s' ",$edate);
        }
        if ( $sdate != "" ) {
            $where .= sprintf(" and attend >= '%s' ",$sdate);
        }

		
		if ($page < 1 or $page == "") $page = 1;
		$page_size = 100;

		$sql = "
			select count(*) as cnt from (
				select
					user_id, count(*) as attend_cnt
				from
					event_attend_member
				where
					event_idx = $idx
					$where 
				group by user_id
				$having
			) a 
		";
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1) / $page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}

		//echo $page_cnt;

		$sql = "
			select
				user_id, count(*) as attend_cnt, sum(if(regular_attend_point > 0,1,0)) as regular_attend_cnt,
				sum(attend_point) as attend_point,sum(regular_attend_point) as regular_attend_point,
				max(attend) as attend,
				sum(support_point) as support_amt,
				is_winner
			from
				event_attend_member
			where
				event_idx = $idx
				$where
			group by user_id $having
			order by attend_cnt desc
			limit $startno, $page_size
		";

		
		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($result),
				"page_total" => $page_cnt
			),
			"body" => $result
		]);
	}

	/*
		Function: ViewAttend
		화면출력
	*/

	public function ViewAttend($idx = '', Request $request){
		$edate = $request->input("edate");
        $sdate = $request->input("sdate");
		$user_id = $request->input("user_id");


        if($sdate == "" || $edate == ""){
            $sql = "
				select start_dt, end_dt from event_attend where idx = $idx
			";
            $row = DB::selectOne($sql);
            if($sdate == "") $sdate = $row->start_dt;
            if($edate == "") $edate = $row->end_dt;
        }

		$values = [

			"idx"	=> $idx,
			"sdate"	=> $sdate,
			"edate"	=> $edate,
			"user_id"	=> $user_id
		];

		return view( Config::get('shop.head.view') . '/promotion/prm20_attend',$values);
	}

	public function AttendSearch($idx = '', Request $request){
		$idx			= $request->input("idx");
		$userid			= $request->input("userid");
		$attd_cnt_from	= $request->input("attd_cnt_from");
		$attd_cnt_to	= $request->input("attd_cnt_to");
        $edate			= $request->input("edate");
        $sdate			= $request->input("sdate");
		$page			= $request->input("page", 1);

		if($sdate == "" || $sdate == ""){
			$sql = "
				select start_dt, end_dt from event_attend where event_idx = ?
			";
			$inputarr = array(
				"idx" => (string)$idx,
			);
			$rs = $conn->execute($sql,$inputarr);
			$row = $rs->fields;
			if($sdate == "") $sdate= $row["start_dt"];
			if($sdate == "") $sdate = $row["end_dt"];
		}

		// 조건절 설정
		$where = "";
		if ( $userid != "" ) $where .= sprintf(" and user_id = '%s' ",$userid);
		if ( $edate != "" ) {
			$where .= sprintf(" and attend <= '%s' ",$edate);
		}
		if ( $sdate != "" ) {
			$where .= sprintf(" and attend >= '%s' ",$sdate);
		}

		if ($page < 1 or $page == "") $page = 1;
		$page_size = 100;
		
		$sql = "
			select
				count(*) as cnt
			from
				event_attend_member
			where
				event_idx = $idx
				$where 
		";
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1) / $page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}

		$sql = "
			select
				user_id,attend,is_winner,attend_point,attend_point_date,regular_attend_point,regular_attend_point_date,support_point,rt
			from
				event_attend_member
			where
				event_idx = $idx
				$where
			order by idx desc
			limit $startno, $page_size
		";
		/*
		echo $sql;
		echo "<br>";
		*/
		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($result),
				"page_total" => $page_cnt
			),
			"body" => $result
		]);

	}

	/*
		Function: SetWinner
		이벤트 담청자 지정
	*/

	public function SetWinner($user_id = '', Request $request){
		$idx			= $request->input("idx");
		$is_winner		= $request->input("is_winner");
		$return_code	= 0;

		if($is_winner == "Y"){
			$winner_change = "N";
		} else if($is_winner == "N"){
			$winner_change = "Y";
		}
		
		/*
		$sql = "
			update event_attend_member set
				is_winner = '$winner_change'
			where event_idx = $idx and user_id = '$user_id'
		";
		*/

		//echo $sql;

		$update_item = [
			"is_winner" => $winner_change
		];
		
		try {
			DB::table('event_attend_member')
			->where('event_idx','=', $idx)
				->where('user_id','=', $user_id)
			->update($update_item);
			$return_code = 1;
		} catch(Exception $e){
			$return_code = 0;
		}
		



		return response()->json([
			"code" => 200,
			"return_code" => $return_code
		]);
	}

	public function newEvent(){
		$cmd	= "addcmd";
		
		$evnet_info = new \stdClass();
        $evnet_info->idx = '';
        $evnet_info->title = '';
        $evnet_info->content = '';
		$evnet_info->start_dt = '';
        $evnet_info->end_dt = '';
		$evnet_info->is_use = '';
        $evnet_info->attend_point_type = '';
        $evnet_info->attend_point = '';
		$evnet_info->first_attend_yn = '';
		$evnet_info->first_attend_point = '';
		$evnet_info->regular_attend_day = '';
		$evnet_info->regular_attend_point = '';
		$evnet_info->bet = '';
		$evnet_info->support_point_yn = '';
		$evnet_info->support_point = '';
		$evnet_info->support_point_expireday = '';
		$evnet_info->support_point_sday = '';
		$evnet_info->support_point_eday = '';
		$evnet_info->support_point_amt = '';
		$evnet_info->stamp = '';

		$values = [
			'cmd'	=> $cmd,
			'evnet_info'	=> $evnet_info
		];

		return view( Config::get('shop.head.view') . '/promotion/prm20_show',$values);
	}

	public function Detail($idx = ''){		
		$bet = "1";
		$is_use = "N";

		if ($idx != "") {
			$cmd = "editcmd";

			$sql = "
				select
					idx,title, content, start_dt, end_dt, is_use, 
					attend_point_type, attend_point, first_attend_yn, first_attend_point,
					regular_attend_day,regular_attend_point,bet,
					support_point_yn,support_point,support_point_expireday,support_point_sday,support_point_eday,support_point_amt,
					stamp
				from event_attend
				where
					idx = $idx
			";
			$evnet_info = DB::selectOne($sql);
		}
		$values = [
			'cmd'	=> $cmd,
			'evnet_info'	=> $evnet_info
		];

		return view( Config::get('shop.head.view') . '/promotion/prm20_show',$values);
	}

	public function Save(Request $request){
		$idx 						= $request->input("idx");
		$subject					= $request->input("subject");
		$content					= $request->input("content");
		$sdate						= $request->input("sdate");
		$edate						= $request->input("edate");
		$is_use						= $request->input("use_yn");
		$attend_point_type			= $request->input("attend_point_type");
		$attend_point				= $request->input("attend_point");
        $first_attend_yn			= $request->input("first_attend_yn","N");
		$first_attend_point			= $request->input("first_attend_point");
		$regular_attend_day			= $request->input("regular_attend_day");
		$regular_attend_point		= $request->input("regular_attend_point");
		$bet						= $request->input("bet",1);

        $support_point_yn			= $request->input("support_point_yn","N");
        $support_point				= $request->input("support_point",0);
        $support_point_expireday	= $request->input("support_point_expireday",0);
        $support_point_sday			= $request->input("support_point_sday");
        $support_point_eday			= $request->input("support_point_eday");
        $support_point_amt			= $request->input("support_point_amt",0);
		$cmd						= $request->input("cmd");

		$return_code = 0;
		$responseText = "";
		
		$sdate = str_replace("-", "", $sdate);
		$edate = str_replace("-", "", $edate);

		$support_point_sday = str_replace("-", "", $support_point_sday);
		$support_point_eday = str_replace("-", "", $support_point_eday);

		$regular_attend_point = str_replace(",", "", $regular_attend_point);
		$first_attend_point = str_replace(",", "", $first_attend_point);
		$attend_point = str_replace(",", "", $attend_point);
		$support_point = str_replace(",", "", $support_point);
		$regular_attend_day = str_replace(",", "", $regular_attend_day);
		$support_point_amt = str_replace(",", "", $support_point_amt);
		
		if($cmd == "addcmd"){

			$sql = "
				insert into event_attend(
					title, content, start_dt, end_dt, is_use, attend_point_type, attend_point, first_attend_yn, first_attend_point, regular_attend_day, regular_attend_point, 
					support_point_yn,support_point,support_point_expireday,support_point_sday,support_point_eday,support_point_amt,
					bet, rt, stamp
				) values (
					'$subject', '$content', '$sdate', '$edate', '$is_use', '$attend_point_type', '$attend_point', '$first_attend_yn', '$first_attend_point', '$regular_attend_day', '$regular_attend_point', '$support_point_yn', '$support_point', '$support_point_expireday', '$support_point_sday', '$support_point_eday','$support_point_amt', '$bet',now(), ''
				)
			";
			
			try {
				DB::insert($sql);
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
				$responseText = "출첵이벤트 등록에 실패하였습니다.";
			};
			

		}else if($cmd == "editcmd"){
			$update_items = [
				"title"						=> $subject,
				"content"					=> $content,
				"start_dt"					=> $sdate,
				"end_dt"					=> $edate,
				"is_use"					=> $is_use,
				"attend_point_type"			=> $attend_point_type,
				"attend_point"				=> $attend_point,
				"first_attend_yn"			=> $first_attend_yn,
				"first_attend_point"		=> $first_attend_point,
				"regular_attend_day"		=> $regular_attend_day,
				"regular_attend_point"		=> $regular_attend_point,
				"bet"						=> $bet,
				"support_point_yn"			=> $support_point_yn,
				"support_point"				=> $support_point,
				"support_point_expireday"	=> $support_point_expireday,
				"support_point_sday"		=> $support_point_sday,
				"support_point_eday"		=> $support_point_eday,
				"support_point_amt"			=> $support_point_amt,
				"ut"						=> "now()"
			];
			
			
			try {
				DB::table('event_attend')
				->where('idx','=', $idx)
				->update($update_items);
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
				$responseText = "출첵이벤트 수정에 실패하였습니다.";
			}

		}

		return response()->json([
			"code" => 200,
			"return_code" => $return_code,
			"responseText"	=> $responseText
		]);
	}

	public function Del(Request $request){
		$idx	= $request->input("idx");
		$return_code = 0;
		$responseText = "";
		$sql = "
			select is_use from event_attend where idx = $idx
		";
		$row = DB::selectOne($sql);

		if($row->is_use == "N"){

			$sql = "
				delete from event_attend_member where event_idx = ?
			";
			try {
				DB::table('event_attend_member')
				->where('event_idx', $idx)
				->delete();
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
				//$responseText = "이벤트 참여회원 삭제 중 오류입니다.\n관리자에게 문의하세요.";
				$responseText = "삭제 시에 오류가 발생하였습니다. 다시 시도하여 주십시오.";
			}

			$sql = "
				delete from event_attend where idx = ?
			";
			try {
				DB::table('event_attend')
				->where('idx', $idx)
				->delete();
				$return_code = 1;
			} catch(Exception $e){
				$return_code = -1;
				//$responseText = "이벤트 삭제 중 오류입니다.\n관리자에게 문의하세요.";
				$responseText = "삭제 시에 오류가 발생하였습니다. 다시 시도하여 주십시오.";
			}

		} else {
			$return_code = -2;
			$responseText = "이벤트가 존재하지 않거나 사용여부를 '미사용' 으로 변경 후 삭제하여 주십시오..";
		}


		return response()->json([
			"code" => 200,
			"return_code" => $return_code,
			"responseText"	=> $responseText
		]);

	}
}
