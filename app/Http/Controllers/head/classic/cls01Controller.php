<?php

namespace App\Http\Controllers\head\classic;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class cls01Controller extends Controller
{
	public function index(Request $request) {
		$today = date("Y-m-d");
		$edate = $today;
		$sdate = date('Y-m-d', strtotime(-3 .'month'));

		$request_sdate = $request->input("sdate");
		if($request_sdate != '') {
			$sdate = date_format(date_create($request_sdate), "Y-m-d");
			$edate = date_format(date_create($request->input("edate")), "Y-m-d");
		}
		
		$mst_query = "
				select idx, title, start_date, end_date 
				from evt_mst 
				order by idx desc
		";

		$evt_mst = DB::select($mst_query);

		$values = [
			'sdate'				=> $sdate,
			'edate'				=> $edate,
			'evt_mst'			=> $evt_mst
		];
		return view( Config::get('shop.head.view') . '/classic/cls01',$values);
	}

	public function search(Request $request){
		$sdate			= $request->input('sdate').' 00:00:00';
		$edate			= $request->input('edate').' 23:59:59';
		$evtidx			= $request->input('evtTitle');
		$subject		= $request->input('subject');
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
		}

		$arr_header = array("total"=>$data_cnt, "page_cnt"=>$page_cnt, "page"=>$page, "page_total"=>$page_cnt );


		$sql = "
				select 
					a.idx, b.title, a.thumb_img, a.subject, a.admin_nm, a.use_yn, a.regi_date, a.cnt
				from evt_notice a
					inner join evt_mst b on a.evt_idx = b.idx
				where 1=1 $where
				order by regi_date desc
		";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => $arr_header,
			"body" => $result
		]);
	}


	public function create(Request $request){
		if(isset($type)) {
			
		} else {
			$type = 'add';
		}
        $name =  Auth('head')->user()->name;
        $email =  Auth('head')->user()->email;

        return view( Config::get('shop.head.view') . '/classic/cls01_show',
            ['name' => $name, 'email' => $email, 'type' => $type]
        );
    }


	public function event_list(Request $request){
		return view( Config::get('shop.head.view') . '/classic/cls01_event_show');
	}


	//이벤트 검색 (수정 잠시 중단)
	public function event_search(Request $request){
		$stitle			= $request->input("s_title");
		// dd($stitle);

		$limit			= $request->input("limit",100);
		$head_desc		= $request->input("head_desc");
		$page			= $request->input("page",1);

		$where = "";
		if ( $stitle != "" ) $where .= " and a.title like '%" . Lib::quote($stitle) . "%' ";


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
		}
		$arr_header = array("data_cnt"=>$data_cnt, "page_cnt"=>$page_cnt, 		"page" => $page, "page_total" => $page_cnt);

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
			"head" => $arr_header,
			"body" => $result
		]);
	}

	public function save (Request $request){
		$type			= $request->input("type");
		
		$name			= $request->input("name");
		$email			= $request->input("email");
		$useyn			= $request->input("useyn");
		$evt_idx		= $request->input("evt_idx");
		$evt_nm 		= $request->input("evt_nm");
		$img_file 		= $request->file("title_file");
		$subject		= $request->input("subject");
		$comment		= $request->input("comment");
		$content		= $request->input("content");
		$idx            = $request->input("idx");
		// dd($idx);


		//스토리지
		$base_path = "/images/fjallraven_event/notice/thumb";

		/* 이미지를 저장할 경로 폴더가 없다면 생성 */
		if(!Storage::disk('public')->exists($base_path)){
			Storage::disk('public')->makeDirectory($base_path);
		}

		if($img_file != null &&  $img_file != ""){
			$file_ori_name = $img_file->getClientOriginalName();
			$ext = substr($file_ori_name, strrpos($file_ori_name, '.') + 1);
			$file_name = sprintf("%s_%s.".$ext , $evt_nm, $subject.' 포스터');
			$save_file = sprintf("%s/%s", $base_path, $file_name);
			$title_img_url = $save_file;
			// dd($title_img_url);

			Storage::disk('public')->putFileAs($base_path, $img_file, $file_name);
		}

		try {
			DB::beginTransaction();
			if ( $type=='add' ) {
				$adminId 		= Auth('head')->user()->id;
				$sql= "
						insert into evt_notice (
							evt_idx, subject, comment, content, admin_id, admin_nm, admin_email, use_yn, regi_date, thumb_img
						) values (
							'$evt_idx', '$subject', '$comment', '$content', '$adminId', '$name', '$email', '$useyn', now(), '$title_img_url'
						)
				";
		
				DB::insert($sql);
			} else if ( $type=='edit' ) {
				
				$sql = "
						update evt_notice set
							evt_idx='$evt_idx', subject='$subject', comment='$comment', content='$content', admin_nm='$name', admin_email='$email', use_yn='useyn', modi_date=now(), thumb_img='$title_img_url'
						where idx = '$idx'
				";
        
				DB::update($sql);
			}
			
			// DB::commit();

			$code = 200;
			$msg = "등록 성공";
		} catch(Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json(['code' => $code, 'message' => $msg], $code);
    }

	public function show(Request $request, $idx) {
		if(isset($type)) {
			
		} else {
			$type = 'edit';
		}

		$notice_query = "
				select 
					a.idx, a.admin_nm, a.admin_email, a.use_yn, b.title, a.subject, a.thumb_img, a.comment, a.content
				from evt_notice a
					inner join evt_mst b on a.evt_idx = b.idx
				where a.idx = '$idx'
		";

		$evt_notice = DB::select($notice_query);
		// dd($evt_notice);

		$values = [
			'type'		 => $type,
			'evt_notice' => $evt_notice
		];
		return view( Config::get('shop.head.view') . '/classic/cls01_show',$values);
	}
}
