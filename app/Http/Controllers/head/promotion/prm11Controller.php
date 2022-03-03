<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class prm11Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        //이벤트 리스트 정보
        $query	= "  select idx, title, substring(start_date, 1, 8) as start_date, substring(end_date, 1, 8) as end_date from evt_mst order by idx desc  ";
        $rows = DB::select($query);
        foreach($rows as $row) {
            $row->idx   = $row->idx;
            $row->title = $row->title . " (" . $row->start_date . " ~ " . $row->end_date . ")";
        }

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'evt'   => $rows
        ];
        return view( Config::get('shop.head.view') . '/promotion/prm11',$values);
    }

    public function search(Request $request){

        $sdate      = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate      = $request->input('edate',date("Ymd"));
        $subject    = $request->input('subject');
        $use_yn     = $request->input('use_yn');
		$content    = $request->input('content');
        $evt_idx    = $request->input('evt_idx');


        $where = "";
        if( $subject != "" )    $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
		if( $content != "" )    $where .= " and a.content like '%" . Lib::quote($content) . "%' ";
		if( $evt_idx != "" )    $where .= " and a.evt_idx = '$evt_idx' ";
        if( in_array($use_yn,array("Y","N"))) $where .= " and a.use_yn = '$use_yn' ";

		$query	= "
			select
				b.title, 
				-- concat('{{config('shop.image_svr')}}/images/fjallraven_event/notice/thumb/thumb_',a.idx,'_s_62.jpg') as img, 
				a.thumb_img as img,
				a.subject, a.admin_nm, a.regi_date, a.cnt, a.idx, concat('/images/fjallraven_event/notice/thumb/thumb_',a.idx,'_s_62.jpg') as thumb_img
			from evt_notice a
			inner join evt_mst b on a.evt_idx = b.idx
			where
				1 = 1
                and ( a.regi_date >= :sdate and a.regi_date < date_add(:edate,interval 1 day))
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

    public function create()
	{
		$name		= Auth('head')->user()->name;
		$email		= Auth('head')->user()->email;

		$idx		= "";
		$useyn		= "Y";
		$evt_idx	= "";
		$evt_nm		= "";
        $subject	= "";
		$comment	= "";
		$content	= "";
		$thumb_img	= "";

        return view( Config::get('shop.head.view') . '/promotion/prm11_show',
            [
                'idx'		=> $idx,
				'useyn'		=> $useyn,
				'name'		=> $name,
				'email'		=> $email,
				'evt_idx'	=> $evt_idx,
				'evt_nm'	=> $evt_nm,
				'subject'	=> $subject,
				'comment'	=> $comment,
				'content'	=> $content,
				'thumb_img'	=> $thumb_img
            ]
        );
    }

    public function save(Request $request)
	{
		$id			= Auth('head')->user()->id;

		$idx		= $request->input('idx');
		$name		= Lib::quote($request->input('name'));
		$email		= Lib::quote($request->input('email'));
		$use_yn		= $request->input('useyn');
		$evt_idx	= $request->input('evt_idx');
		$subject	= Lib::quote($request->input('subject'));
		$content	= Lib::quote($request->input('content'));
		$comment	= Lib::quote($request->input('comment'));

		//등록
		if( $idx == "" )
		{
			$notice_event = [
				'evt_idx'		=> $evt_idx,
				'subject'		=> $subject,
				'comment'		=> $comment,
				'content'		=> $content,
				'admin_id'		=> $id,
				'admin_nm'		=> $name,
				'admin_email'	=> $email,
				'use_yn'		=> $use_yn,
				'regi_date'		=> DB::raw('now()'),
				'modi_date'		=> DB::raw('now()')
			];

			$idx	= DB::table('evt_notice')->insertGetId($notice_event);

			$code = 200;
		}
		//수정
		else
		{
			$notice_event = [
				'evt_idx'		=> $evt_idx,
				'subject'		=> $subject,
				'comment'		=> $comment,
				'content'		=> $content,
				'admin_id'		=> $id,
				'admin_nm'		=> $name,
				'admin_email'	=> $email,
				'use_yn'		=> $use_yn,
				'modi_date'		=> DB::raw('now()')
			];

			try {
				DB::transaction(function () use (&$result, $idx,$notice_event) {
					DB::table('evt_notice')
						->where('idx','=',$idx)
						->update($notice_event);
				});
				$code = 200;
			} catch(Exception $e){
				$code = 500;
			}
		}

		//이미지 처리 시작
		$base_path	= "/images/event_notice_img/";

		$thumb_file = $request->file("file");

		if($thumb_file != null &&  $thumb_file != "")
		{
			/* 이미지를 저장할 경로 폴더가 없다면 생성 */
			if(!Storage::disk('public')->exists($base_path))
			{
				Storage::disk('public')->makeDirectory($base_path);
			}

			$file_ori_name	= $thumb_file->getClientOriginalName();

			$ext	= substr($file_ori_name, strrpos($file_ori_name, '.') + 1);

			$file_name	= sprintf("thumb_%s.".$ext , $idx);
			$save_file	= sprintf("%s%s", $base_path, $file_name);

			Storage::disk('public')->putFileAs($base_path, $thumb_file, $file_name);

			$sql	= " update evt_notice set thumb_img = '$save_file' where idx = '$idx' ";
			DB::update($sql);
		}
		//이미지 처리 종료

		echo json_encode(array(
			"code" => $code
		));

    }

    public function event_list()
	{

		$values = [
        ];

		return view( Config::get('shop.head.view') . '/promotion/prm11_event_show',$values);
    }

    public function event_search(Request $request)
	{
        $s_title	= $request->input('s_title');

		$where	= "";
        if( $s_title != "" )	$where .= " and title like '%" . Lib::quote($s_title) . "%' ";

		$sql = "
			select
				a.title, a.join_cnt, substring(a.start_date, 1, 8) as start_date, substring(a.end_date, 1, 8) as end_date, '보기' as view, '선택' as slct, a.idx
			from evt_mst a
			where
				1 = 1
				$where
			order by a.idx desc
		";
		$result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show($idx)
	{
		$query	= " 
			select
				a.evt_idx, a.subject, a.comment, a.content, a.admin_nm, a.admin_email, a.use_yn, a.thumb_img, a.pds, a.pds_nm, b.title, a.thumb_img
			from evt_notice a
			inner join evt_mst b on a.evt_idx = b.idx
			where a.idx = :idx 
		";
		$rows	= DB::selectOne($query, ['idx' => $idx]);

		$useyn		= $rows->use_yn;
		$name		= $rows->admin_nm;
		$email		= $rows->admin_email;
		$evt_idx	= $rows->evt_idx;
		$evt_nm		= $rows->title;
        $subject	= $rows->subject;
		$comment	= $rows->comment;
		$content	= $rows->content;
		$thumb_img	= $rows->thumb_img;

        return view( Config::get('shop.head.view') . '/promotion/prm11_show',
            [
                'idx'		=> $idx,
				'useyn'		=> $useyn,
				'name'		=> $name,
				'email'		=> $email,
				'evt_idx'	=> $evt_idx,
				'evt_nm'	=> $evt_nm,
				'subject'	=> $subject,
				'comment'	=> $comment,
				'content'	=> $content,
				'thumb_img'	=> $thumb_img
            ]
        );

	}

    public function update($no, Request $request){

        $subject = $request->input('subject');
        $content = $request->input('content');
        $use_yn = $request->input('use_yn');
        $main_yn = $request->input('main_yn','N');
        $notice_yn = $request->input('notice_yn','N');
        $popup_yn = $request->input('popup_yn','N');
        $popup_type = $request->input('popup_type');

        $notice_shop = [
            'subject' => $subject,
            'content' => $content,
            'admin_id' => 'smson',
            'admin_nm' => '손상모',
            'admin_email' => 'steve92son@gmail.com',
            'use_yn' => $use_yn,
            'main_yn' => $main_yn,
            'notice_yn' => $notice_yn,
            'popup_yn' => $popup_yn,
            'popup_type' => $popup_type,
            'd_cat_cds' => '',
            'disp_prd_yn' => '',
            'disp_prd_type' => '',
            'ut' => DB::raw('now()')
        ];

        try {
            DB::transaction(function () use (&$result,$no,$notice_shop) {
                DB::table('notice_shop')
                    ->where('ns_no','=',$no)
                    ->update($notice_shop);
            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }

        echo json_encode(array(
            "code" => $code
        ));
    }

    public function destroy(Request $request)
	{
		$idx	= $request->input('idx');

		if( $idx == "" )
		{
			$code	= "500";
		}

        try {
            DB::transaction(function () use (&$result,$idx) {
                DB::table('evt_notice')
                    ->where('idx','=',$idx)
                    ->delete();
            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }
        echo json_encode(array(
            "code" => $code
        ));
    }

}
