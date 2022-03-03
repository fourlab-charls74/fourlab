<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm01Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view( Config::get('shop.head.view') . '/promotion/prm01',$values);
    }

    public function search(Request $request){

        $sdate = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $subject = $request->input('subject');
        $use_yn = $request->input('use_yn');
		$content = $request->input('content');

        $main_yn = $request->input('main_yn');
        $notice_yn = $request->input('notice_yn');
        $popup_yn = $request->input('popup_yn');

        $where = "";
        if ($subject != "") $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
		if ($content != "") $where .= " and a.content like '%" . Lib::quote($content) . "%' ";
        if (in_array($use_yn,array("Y","N"))) $where .= " and a.use_yn = '$use_yn' ";

        if($main_yn != '' || $notice_yn != '' || $popup_yn != '') {
            if($main_yn == '') $main_yn = "N";
            if($notice_yn == '') $notice_yn = "N";
            if($popup_yn == '') $popup_yn = "N";
            $where .= " and a.main_yn = '$main_yn' ";
            $where .= " and a.notice_yn = '$notice_yn' ";
            $where .= " and a.popup_yn = '$popup_yn' ";
        }

        $query = /** @lang text */
            "
			select
				main_yn, notice_yn, '' as disp_prd_yn,subject,  a.cnt,
				a.admin_nm as name, a.use_yn, a.regi_date, a.ut, a.ns_no as idx
			from notice_shop a
			where ( notice_yn = 'Y' or ( a.regi_date >= :sdate and a.regi_date < date_add(:edate,interval 1 day))) $where
			order by a.notice_yn desc, a.ns_no desc
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

    public function create(){

        $user = new \stdClass();
        $user->admin_nm = Auth('head')->user()->name;
        $user->admin_email = Auth('head')->user()->email;
        $user->use_yn = 'Y';
        $user->main_yn = 'Y';
        $user->notice_yn = '';
        $user->popup_yn = '';
        $user->subject = '';
        $user->content = '';
        $user->popup_type = '';
        $user->disp_prd_yn = '';
        $user->disp_prd_type = '';

        return view( Config::get('shop.head.view') . '/promotion/prm01_show',
            ['no' => '','user' => $user]
        );
    }

    public function show($no){
        $user = DB::table('notice_shop')->where('ns_no',"=",$no)->get();
        return view( Config::get('shop.head.view') . '/promotion/prm01_show',
            ['no' => $no, 'user' => $user[0]]
        );
    }

    public function store(Request $request){
		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;
		$email	= "help@alpen-international.com";

        $admin_nm	    = $request->input('admin_nm');
        $admin_email	= $request->input('admin_email');
        $subject	    = $request->input('subject');
        $content	    = $request->input('content');
        $use_yn		    = $request->input('use_yn');
        $main_yn	    = $request->input('main_yn','N');
        $notice_yn	    = $request->input('notice_yn','N');
        $popup_yn	    = $request->input('popup_yn','N');
        $popup_type	    = $request->input('popup_type','C');
        $disp_prd_yn	= $request->input('disp_prd_yn','N');
        $disp_prd_type	= $request->input('disp_prd_type','P');

		$notice_shop = [
			'subject'		=> $subject,
			'content'		=> $content,
			'admin_id'		=> $id,
			'admin_nm'		=> $admin_nm,
			'admin_email'	=> $admin_email,
			'use_yn'		=> $use_yn,
			'main_yn'		=> $main_yn,
			'notice_yn'		=> $notice_yn,
			'popup_yn'		=> $popup_yn,
			'popup_type'	=> $popup_type,
            'disp_prd_yn'	=> $disp_prd_yn,
            'disp_prd_type'	=> $disp_prd_type,
			'd_cat_cds'		=> '',
			'cnt'			=> 0,
			'regi_date'		=> DB::raw('now()')
		];

        $code = 200;
        $msg = '저장되었습니다.';

		try {
			DB::transaction(function () use (&$result,$notice_shop) {
				DB::table('notice_shop')->insert($notice_shop);
			});
		} catch(Exception $e){
			$code = 500;
            $msg = $e->getMessage();
		}


		return response()->json([
			"code" => $code,
            "message" => $msg
		], $code);

    }

    public function update($no, Request $request){
		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;
		$email	= "help@alpen-international.com";

        $admin_nm	    = $request->input('admin_nm');
        $admin_email	= $request->input('admin_email');
        $subject	    = $request->input('subject');
        $content	    = $request->input('content');
        $use_yn		    = $request->input('use_yn');
        $main_yn	    = $request->input('main_yn','N');
        $notice_yn	    = $request->input('notice_yn','N');
        $popup_yn	    = $request->input('popup_yn','N');
        $popup_type	    = $request->input('popup_type','C');
        $disp_prd_yn	= $request->input('disp_prd_yn','N');
        $disp_prd_type	= $request->input('disp_prd_type','P');

        $notice_shop = [
			'subject'		=> $subject,
			'content'		=> $content,
			'admin_id'		=> $id,
			'admin_nm'		=> $admin_nm,
			'admin_email'	=> $admin_email,
			'use_yn'		=> $use_yn,
			'main_yn'		=> $main_yn,
			'notice_yn'		=> $notice_yn,
			'popup_yn'		=> $popup_yn,
			'popup_type'	=> $popup_type,
            'disp_prd_yn'	=> $disp_prd_yn,
            'disp_prd_type'	=> $disp_prd_type,
			'd_cat_cds'		=> '',
            'ut'            => now(),
        ];

        $code = 200;
        $msg = '변경사항이 저장되었습니다.';

        try {
            DB::transaction(function () use (&$result,$no,$notice_shop) {
                DB::table('notice_shop')
                    ->where('ns_no','=',$no)
                    ->update($notice_shop);
            });
        } catch(Exception $e){
            $code = 500;
            $msg = $e->getMessage();
        }

        echo json_encode(array(
            "code" => $code,
            "message" => $msg
        ));
    }

    public function destroy($no){

        $code = 200;
        $msg = '삭제되었습니다.';

        try {
            DB::transaction(function () use (&$result,$no) {
                DB::table('notice_shop')
                    ->where('ns_no','=',$no)
                    ->delete();
            });
        } catch(Exception $e){
            $code = 500;
            $msg = $e->getMessage();
        }
        echo json_encode(array(
            "code" => $code,
            "message" => $msg
        ));
    }

}