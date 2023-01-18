<?php

namespace App\Http\Controllers\head\classic;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cls01Controller extends Controller
{
	public function index(Request $req) {
		$today = date("Y-m-d");
		$edate = $today;
		$sdate = date('Y-m-d', strtotime(-3 .'month'));

		$req_sdate = $req->input("sdate");
		if($req_sdate != '') {
			$sdate = date_format(date_create($req_sdate), "Y-m-d");
			$edate = date_format(date_create($req->input("edate")), "Y-m-d");
		}
		
		$mst_query = "select idx, title, start_date, end_date from evt_mst order by idx desc";

		$evt_mst = DB::select($mst_query);

		$values = [
			'sdate'				=> $sdate,
			'edate'				=> $edate,
			'evt_mst'			=> $evt_mst
		];
		return view( Config::get('shop.head.view') . '/classic/cls01',$values);
	}

	public function search(Request $request){
		$sdate			= $request->input("sdate");
		$edate			= $request->input("edate");
		$evtidx			= $request->input('evtTitle');
		$subject		= $request->input("subject");
		$content 		= $request->input('content');
        $use_yn 		= $request->input('use_yn');

		$limit			= $request->input("limit",100);
		$head_desc		= $request->input("head_desc");
		$page			= $request->input("page",1);

		$where = "";
		if( $sdate != "" ) 		$where .= " and a.regi_date >= '$sdate' ";
		if( $edate != "" ) 		$where .= " and a.regi_date < '$edate' ";
		if( $evtidx != "" ) 	$where .= " and a.evt_idx = '$evtidx' ";
		if ( $subject != "" ) 	$where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
		if ( $content != "" ) 	$where .= " and a.content like '%" . Lib::quote($content) . "%' ";
		if ( $use_yn != "" ) 	$where .= " and a.use_yn = '$use_yn' ";


		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		if($page == 1) {
			$sql = "
					select 
						count(*) as cnt
					from evt_notice a
						inner join evt_mst b on a.evt_idx = b.idx
					where 1=1 $where
					";
			$row = DB::selectOne($sql);
			$data_cnt = $row->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
			}
			$arr_header = array("data_cnt"=>$data_cnt, "page_cnt"=>$page_cnt);
		}


		$sql = "
				select 
					b.title, a.thumb_img, a.subject, a.admin_nm, a.use_yn, a.regi_date, a.cnt
				from evt_notice a
					inner join evt_mst b on a.evt_idx = b.idx
				where 1=1 $where
				order by regi_date desc
			";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => $page_cnt
			),
			"body" => $result
		]);
	}


	//공지사항 추가 (수정 중)
	public function create(){

        $name =  Auth('head')->user()->name;
        $email =  Auth('head')->user()->email;

        $user = new \stdClass();
        $user->name = $name;
		$user->email = $email;

        return view( Config::get('shop.head.view') . '/classic/cls01_show',
            ['no' => '','user' => $user]
        );
    }


	public function event_list(){
		return view( Config::get('shop.head.view') . '/classic/cls01_event_show');
	}


	public function event_search(Request $request){
		$stitle			= $request->input("s_title");

		$limit			= $request->input("limit",100);
		$head_desc		= $request->input("head_desc");
		$page			= $request->input("page",1);

		$where = "";
		if ( $stitle != "" ) 	$where .= " and a.title like '%" . Lib::quote($subject) . "%' ";


		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		if($page == 1) {
			$sql = "
					select 
						count(*) as cnt
					from evt_notice a
						inner join evt_mst b on a.evt_idx = b.idx
					where 1=1 $where
					";
			$row = DB::selectOne($sql);
			$data_cnt = $row->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
			}
			$arr_header = array("data_cnt"=>$data_cnt, "page_cnt"=>$page_cnt);
		}

		$sql = "
				select 
					a.idx, a.title, a.join_cnt, a.start_date, a.end_date
				from evt_mst a
				where 1=1 $where
				order by idx desc
				";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => $page_cnt
			),
			"body" => $result
		]);
	}

}
