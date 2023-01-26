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
	public function index(Request $request) {
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
		$here = 'here';

		$values = [
			'here' => $here
		];
		return view( Config::get('shop.head.view') . '/classic/cls02',$values);
	}

	// public function search(Request $request){
	// 	$sdate			= $request->input('sdate').' 00:00:00';
	// 	$edate			= $request->input('edate').' 23:59:59';
	// 	$evtidx			= $request->input('evtTitle');
	// 	$subject		= $request->input('subject');
	// 	$content 		= $request->input('content');
    //     $use_yn 		= $request->input('use_yn');

	// 	$ord_field 		= $request->input("ord_field",'a.redi_date');
	// 	$ord 			= $request->input("ord",'asc');

	// 	$page			= $request->input("page",1);
	// 	$page_size 		= $request->input("limit",10);

	// 	if ($page < 1 or $page == "") $page = 1;

	// 	$data_cnt = 0;
    //     $page_cnt = 0;

	// 	$where = "";
	// 	if( $sdate != "" ) 		$where .= " and a.regi_date >= '$sdate' ";
	// 	if( $edate != "" ) 		$where .= " and a.regi_date < '$edate' ";
	// 	if( $evtidx != "" ) 	$where .= " and a.evt_idx = '$evtidx' ";
	// 	if ( $subject != "" ) 	$where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
	// 	if ( $content != "" ) 	$where .= " and a.content like '%" . Lib::quote($content) . "%' ";
	// 	if ( $use_yn != "" ) 	$where .= " and a.use_yn = '$use_yn' ";

	// 	if($page == 1){
	// 		$sql = "
	// 				select 
	// 					count(*) as cnt
	// 				from evt_notice a
	// 					inner join evt_mst b on a.evt_idx = b.idx
	// 				where 1=1 $where
	// 		";
	// 		$row = DB::selectOne($sql);
	// 		$data_cnt = $row->cnt;

	// 		// 페이지 얻기
	// 		$page_cnt=(int)(($data_cnt - 1)/$page_size) + 1;
	// 		$startno = ($page - 1) * $page_size;
	// 	}else{
	// 		$startno = ($page - 1) * $page_size;
	// 	}

	// 	$arr_header = array("total"=>$data_cnt, "page_cnt"=>$page_cnt, "page"=>$page);

	// 	$sql = "
	// 			select 
	// 				a.idx, b.title, a.thumb_img, a.subject, a.admin_nm, a.use_yn, a.regi_date, a.cnt
	// 			from evt_notice a
	// 				inner join evt_mst b on a.evt_idx = b.idx
	// 			where 1=1 $where
	// 			order by $ord_field $ord 
	// 			limit $startno, $page_size
	// 	";

	// 	$result = DB::select($sql);

	// 	return response()->json([
	// 		"code" => 200,
	// 		"head" => $arr_header,
	// 		"body" => $result
	// 	]);
	// }

	// public function create(Request $request){
	// 	if(isset($type)) {
			
	// 	} else {
	// 		$type = 'add';
	// 	}
    //     $name =  Auth('head')->user()->name;
    //     $email =  Auth('head')->user()->email;

    //     return view( Config::get('shop.head.view') . '/classic/cls01_show',
    //         ['name' => $name, 'email' => $email, 'type' => $type]
    //     );
    // }


	// public function event_list(Request $request){
	// 	return view( Config::get('shop.head.view') . '/classic/cls01_event_show');
	// }


	// public function event_search(Request $request){
	// 	$stitle			= $request->input("s_title");

	// 	$limit			= $request->input("limit",100);
	// 	$head_desc		= $request->input("head_desc");
	// 	$page			= $request->input("page",1);

	// 	$where = "";
	// 	if ( $stitle != "" ) $where .= " and a.title like '%" . Lib::quote($stitle) . "%' ";

	// 	if ($page < 1 or $page == "") $page = 1;
	// 	$page_size = $limit;

	// 	if($page == 1) {
	// 		$sql = "
	// 				select 
	// 					count(*) as cnt
	// 				from evt_mst a
	// 				where 1=1 $where
	// 		";

	// 		$row = DB::selectOne($sql);
	// 		$data_cnt = $row->cnt;

	// 		// 페이지 얻기
	// 		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;

	// 		if($page == 1){
	// 			$startno = ($page-1) * $page_size;
	// 		} else {
	// 			$startno = ($page-1) * $page_size;
	// 		}
	// 	}

	// 	$arr_header = array(
	// 		"total"		=>	$data_cnt,
	// 		"page_cnt"	=>	$page_cnt,
	// 		"page"		=>	$page,
	// 		"page_total"=>	$page_cnt
	// 	);

	// 	$sql = "
	// 			select 
	// 				a.idx, a.title, a.join_cnt, a.start_date, a.end_date
	// 			from evt_mst a
	// 			where 1=1 $where
	// 			order by idx desc
	// 	";

	// 	$result = DB::select($sql);

	// 	return response()->json([
	// 		"code" => 200,
	// 		"head" => $arr_header,
	// 		"body" => $result
	// 	]);
	// }

	// public function save (Request $request){
	// 	$type			= $request->input("type");
	// 	$adminId 		= Auth('head')->user()->id;
	// 	$name			= $request->input("name");
	// 	$email			= $request->input("email");
	// 	$useyn			= $request->input("useyn");
	// 	$evt_idx		= $request->input("evt_idx");
	// 	$evt_nm 		= $request->input("evt_nm");
	// 	$img_file 		= $request->file("title_file");
	// 	$subject		= $request->input("subject");
	// 	$comment		= $request->input("comment");
	// 	$content		= $request->input("content");
	// 	$idx            = $request->input("idx");

	// 	$base_path = "/images/fjallraven_event/notice/thumb";

	// 	/* 이미지를 저장할 경로 폴더가 없다면 생성 */
	// 	if(!Storage::disk('public')->exists($base_path)){
	// 		Storage::disk('public')->makeDirectory($base_path);
	// 	}

	// 	if($img_file != null &&  $img_file != ""){
	// 		$file_ori_name = $img_file->getClientOriginalName();
	// 		$ext = substr($file_ori_name, strrpos($file_ori_name, '.') + 1);
	// 		$file_name = sprintf("%s_%s.".$ext, time(), 'thumb');
	// 		$save_file = sprintf("%s/%s", $base_path, $file_name);
	// 		$title_img_url = $save_file;

	// 		Storage::disk('public')->putFileAs($base_path, $img_file, $file_name);
	// 	}

	// 	try {
	// 		DB::beginTransaction();
			
	// 		if(isset($title_img_url)){
	// 			$sql= "
	// 					insert into evt_notice (
	// 						evt_idx, subject, comment, content, admin_id, admin_nm, admin_email, use_yn, regi_date, thumb_img
	// 					) values (
	// 						'$evt_idx', '$subject', '$comment', '$content', '$adminId', '$name', '$email', '$useyn', now(), '$title_img_url'
	// 					)
	// 			";
	// 		} else {
	// 			$sql= "
	// 					insert into evt_notice (
	// 						evt_idx, subject, comment, content, admin_id, admin_nm, admin_email, use_yn, regi_date
	// 					) values (
	// 						'$evt_idx', '$subject', '$comment', '$content', '$adminId', '$name', '$email', '$useyn', now()
	// 					)
	// 			";
	// 		}
	
	// 		DB::insert($sql);
	// 		DB::commit();

	// 		$code = 200;
	// 		$msg = "add success";
	// 	} catch(Exception $e) {
	// 		DB::rollback();
	// 		$code = 500;
	// 		$msg = $e->getMessage();
	// 	}
	// 	return response()->json(['code' => $code, 'message' => $msg], $code);
    // }

	// public function update (Request $request, $idx){
	// 	$adminId 		= Auth('head')->user()->id;
	// 	$name			= $request->input("name");
	// 	$email			= $request->input("email");
	// 	$useyn			= $request->input("useyn");
	// 	$evt_idx		= $request->input("evt_idx");
	// 	$evt_nm 		= $request->input("evt_nm");
	// 	$img_file 		= $request->file("title_file");
	// 	$subject		= $request->input("subject");
	// 	$comment		= $request->input("comment");
	// 	$content		= $request->input("content");

	// 	$base_path = "/images/fjallraven_event/notice/thumb";

	// 	/* 이미지를 저장할 경로 폴더가 없다면 생성 */
	// 	if(!Storage::disk('public')->exists($base_path)){
	// 		Storage::disk('public')->makeDirectory($base_path);
	// 	}

	// 	if ( $img_file != null &&  $img_file != "" ){
	// 		$beforeImg = DB::table('evt_notice')
	// 						->where('idx', $idx)
	// 						->value('thumb_img');
			
	// 		if ( $beforeImg != null && $img_file != "" ) {
	// 			$imgPathArr = explode('/', $beforeImg);
	// 			$cnt = count($imgPathArr);
	// 			Storage::disk('public')->delete($base_path.'/'.$imgPathArr[$cnt-1]);
	// 		}
	// 		$file_ori_name = $img_file->getClientOriginalName();
	// 		$ext = substr($file_ori_name, strrpos($file_ori_name, '.') + 1);
	// 		$file_name = sprintf("%s_%s.".$ext, time(), 'thumb');
	// 		$save_file = sprintf("%s/%s", $base_path, $file_name);
	// 		$title_img_url = $save_file;

	// 		Storage::disk('public')->putFileAs($base_path, $img_file, $file_name);
	// 	}

	// 	try {
	// 		DB::beginTransaction();
	// 		if ( isset($title_img_url) ) {
	// 			$sql = "
	// 					update evt_notice set
	// 						admin_id='$adminId', evt_idx='$evt_idx', subject='$subject', comment='$comment', content='$content', admin_nm='$name', admin_email='$email', use_yn='$useyn', modi_date=now(), thumb_img='$title_img_url'
	// 					where idx = '$idx'
	// 			";
	// 		} else {
	// 			$sql = "
	// 					update evt_notice set
	// 						admin_id='$adminId', evt_idx='$evt_idx', subject='$subject', comment='$comment', content='$content', admin_nm='$name', admin_email='$email', use_yn='$useyn', modi_date=now()
	// 					where idx = '$idx'
	// 			";
	// 		}
	
	// 		DB::update($sql);
	// 		DB::commit();

	// 		$code = 200;
	// 		$msg = "update success";
	// 	} catch(Exception $e) {
	// 		DB::rollback();
	// 		$code = 500;
	// 		$msg = $e->getMessage();
	// 	}
	// 	return response()->json(['code' => $code, 'message' => $msg], $code);

	// }

	// public function show(Request $request, $idx) {
	// 	if(isset($type)) {
			
	// 	} else {
	// 		$type = 'edit';
	// 	}

	// 	$notice_query = "
	// 			select 
	// 				a.idx, a.admin_nm, a.admin_email, a.use_yn, a.evt_idx, b.title, a.subject, a.thumb_img, a.comment, a.content
	// 			from evt_notice a
	// 				inner join evt_mst b on a.evt_idx = b.idx
	// 			where a.idx = '$idx'
	// 	";

	// 	$evt_notice = DB::select($notice_query);

	// 	$values = [
	// 		'type'		 => $type,
	// 		'evt_notice' => $evt_notice
	// 	];
	// 	return view( Config::get('shop.head.view') . '/classic/cls01_show',$values);
	// }

	// public function delete($idx) {
	// 	$base_path = "/images/fjallraven_event/notice/thumb";
	// 	$beforeImg = DB::table('evt_notice')
	// 					->where('idx', $idx)
	// 					->value('thumb_img');
			
	// 	if ( $beforeImg != null && $beforeImg != "" ) {
	// 		$imgPathArr = explode('/', $beforeImg);
	// 		$cnt = count($imgPathArr);
	// 		Storage::disk('public')->delete($base_path.'/'.$imgPathArr[$cnt-1]);
	// 	}

	// 	try {
	// 		DB::table('evt_notice')->where('idx', $idx)->delete();
	// 		$code = 200;
	// 		$msg  = "delete success";
	// 	} catch (Exception $e) {
	// 		DB::rollback();
	// 		$code = 500;
	// 		$msg = $e->getMessage();
	// 	}
    //     return response()->json(['code' => $code, 'message' => $msg], $code);
    // }
}
