<?php

namespace App\Http\Controllers\partner\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm02Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view( Config::get('shop.partner.view') . '/promotion/prm02',$values);
    }

    public function search(Request $request){

        $sdate = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $subject = $request->input('subject');
        $use_yn = $request->input('use_yn');

        $where = "";
        if ($subject != "") $where .= " and a.subject like '%". Lib::quote($subject)."%' ";
        if ($use_yn != "") $where .= " and a.use_yn = '". Lib::quote($use_yn)."' ";

        $query = "
			select *
			from faq a
			order by a.no desc
		";
        $result = DB::select($query);
        echo json_encode($result);
    }

    public function create(){

        $user = new \stdClass();
        $user->use_yn = 'Y';
        $user->main_yn = '';
        $user->notice_yn = '';
        $user->popup_yn = '';
        $user->subject = '';
        $user->content = '';

        return view( Config::get('shop.partner.view') . '/promotion/prm01_show',
            ['no' => '','user' => $user]
        );
    }

    public function show($no){
        $user = DB::table('faq')->where('no',"=",$no)->get();
        return view( Config::get('shop.partner.view') . '/promotion/prm02_show',
            ['no' => $no, 'user' => $user[0]]
        );
    }

    public function store(Request $request){

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
            'cnt' => 0,
            'regi_date' => DB::raw('now()'),
            'ut' => DB::raw('now()')
        ];

        try {
            DB::transaction(function () use (&$result,$notice_shop) {
                DB::table('notice_shop')->insert($notice_shop);
            });
            $result = 1;
        } catch(Exception $e){
            $result = 0;
        }
        echo $result;
    }

    public function update($no, Request $request){

        $question = $request->input('question');
        $answer = $request->input('answer');

        $faq = [
            'question' => $question,
            'answer' => $answer
        ];

        try {
            DB::transaction(function () use (&$result,$no,$faq) {
                DB::table('faq')
                    ->where('no','=',$no)
                    ->update($faq);
            });
            $result = 1;
        } catch(Exception $e){
            $result = 0;
        }
        echo $result;
    }

    public function destroy($no){

        try {
            DB::transaction(function () use (&$result,$no) {
                DB::table('faq')
                    ->where('no','=',$no)
                    ->delete();
            });
            $result = 1;
        } catch(Exception $e){
            $result = 0;
        }
        echo $result;
    }

}
