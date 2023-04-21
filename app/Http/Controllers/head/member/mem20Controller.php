<?php

namespace App\Http\Controllers\head\member;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;
use Carbon\Carbon;

use App\Models\SMS;
use Illuminate\Support\Facades\Mail;

class mem20Controller extends Controller
{
    public function index($type='', Request $req) {
        $conf = new Conf();

        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $ans_yn_items = SLib::getCodes("G_ANS_YN");
        $qna_types = SLib::getCodes("G_QNA_TYPE");
        $admin_open_yn_items = SLib::getCodes("G_ADMIN_OPEN_YN");
        $open_yn_items = SLib::getCodes("G_OPEN_YN");

        $edate = $req->input('date', date("Y-m-d"));

        $sdate = date('Y-m-d', strtotime(-7 .'days'));
        $sdate = $req->input('date', $sdate);

        $cfg_shop_name	= $conf->getConfigValue("shop","name");
        $cfg_sms		= $conf->getConfig("sms");
        $cfg_sms_yn		= $conf->getValue($cfg_sms,"sms_yn");
        $cfg_email_yn	= $conf->getValue("email","counsel_yn");


        /*
        $ans_yn_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_ANS_YN' order by no asc");
        $qna_types = DB::select("select code_id as id, case when 'kor' = 'kor' then code_val else code_val_eng end as val from code where  code_kind_cd = 'G_QNA_TYPE' and code_id <> 'K' and use_yn='Y'  order by code_seq");
        $admin_open_yn_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_ADMIN_OPEN_YN' order by no asc");
        $open_yn_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_OPEN_YN' order by no asc");
        */

        //$conf_sms_yn = DB::select("select value as val from conf where type='sms' and name='sms_yn' ");
        //$conf_email_counsel_yn = DB::select("select value as val from conf where type='email' and name='counsel_yn'");

        $values = [
            "admin_id" =>$id,
            "admin_nm" => $name,
            "admin_ans_items" => $ans_yn_items,
            "qna_types" => $qna_types,
            "admin_open_yn_items" => $admin_open_yn_items,
            "open_yn_items" => $open_yn_items,
            "edate" => $edate,
            "sdate" => $sdate,
            "sms_yn" => $cfg_sms_yn,
            "email_yn" => $cfg_email_yn,
            'layout' => $type ? 'head_skote.layouts.master-without-nav' : 'head_skote.layouts.app',
            'user_id' => $req->input('user_id', '')
        ];
        return view( Config::get('shop.head.view') . '/member/mem20',$values);
    }

    public function search(Request $request){
        $edate = $request->input("edate");
        $sdate = $request->input("sdate");

        $ans_yn = $request->input("ans_yn");
        $qna_type = $request->input("qna_type");
        $subject = $request->input("subject");
        $name = $request->input("name");
        $open_yn = $request->input("open_yn");
        $user_id = $request->input("user_id");
        $admin_nm = $request->input("admin_nm");
        $admin_open_yn = $request->input("admin_open_yn");
        $ans_nm = $request->input("ans_nm");
        $page = $request->input("page",1);

        /*
        $edate = str_replace("-", "", $edate);
        $sdate = str_replace("-", "", $sdate);
        */
        $edate = $edate." 23:59:59";
        $sdate = $sdate." 00:00:00";



        $where = "";
        if ($user_id !="" ) $where .= " and a.user_id = '$user_id'";
        if ($sdate != "") $where .= " and a.regi_date >= '$sdate' ";
        if ($edate != "") $where .= " and a.regi_date < '$edate'";
        if ($ans_yn != "") $where .= " and a.ans_yn = '$ans_yn' ";
        if ($subject != "") $where .= " and a.subject like '%$subject%' ";
        if ($qna_type != "") $where .= " and a.qna_type like '$qna_type%' ";
        if ($open_yn != "") $where .= " and a.open_yn = '$open_yn' ";
        if ($name != "") $where .= " and a.user_nm like '$name%' ";
        if ($admin_nm !="") $where .= " and a.admin_nm like '$admin_nm%' ";
        if ($admin_open_yn != "") $where .= " and a.admin_open_yn = '$admin_open_yn' ";
        if ($ans_nm != "") $where .= " and a.ans_nm = '$ans_nm' ";


        if ($page < 1 or $page == "") $page = 1;
        $page_size = 250;
        $query = "
            select count(*) as cnt
            from qna a
            where 1=1 $where
        ";
        $row = DB::select($query);
        $total = $row[0]->cnt;

        // 페이지 얻기
        $page_cnt=(int)(($total-1)/$page_size) + 1;

        if($page == 1){
            $startno = ($page-1) * $page_size;
        } else {
            $startno = ($page-1) * $page_size;
        }

        $arr_header = array("total"=>$total, "page_cnt"=>$page_cnt);

        $public = "공개";
        $private = "비공개";
        $ans_y = "완료";
        $ans_s = "대기";
        $ans_c = "불가";

        $query = "
        select
            '' as chkbox,
            a.idx, ca.code_val as type,
            a.subject, a.user_nm, a.user_id, date_format(a.regi_date,'%Y.%m.%d %H:%i:%s') as regi_date, a.admin_open_yn,
            (case a.open_yn when 'Y' then '$public' when 'N' then '$private' else '-' end) as open_state,
            (case a.admin_open_yn when 'Y' then '$public' when 'N' then '$private' else '-' end) as admin_open_state,
            (case a.ans_yn when 'Y' then '$ans_y' when 'C' then '$ans_c' else '$ans_s' end) as ans_state,
            a.ans_yn
        from qna a
            left outer join code ca on ca.code_kind_cd = 'G_QNA_TYPE' and ca.code_id = a.qna_type
        where 1=1 $where
        order by a.regi_date desc
        limit $startno, $page_size
        ";

        //echo $query;
        $result = DB::select($query);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);
    }

  public function show($idx = ''){
    //echo $query;
    $id = Auth('head')->user()->id;
    $name = Auth('head')->user()->name;
    //$idx = $request->input("idx");
    $query = "
        select
        a.subject, a.user_nm, a.user_id, a.open_yn, a.phone, a.mobile
        , ca.code_val as type_cd, a.regi_date, a.ord_no, a.admin_open_yn, a.question
        , a.ans_subject, a.ans_yn, a.answer
        , if(a.ans_yn = 'Y', a.ans_id, '$id') as ans_id
        , if(a.ans_yn = 'Y', a.ans_nm, '$name') as ans_nm
        , greatest(ifnull(a.ans_date, a.upd_date), a.upd_date) as repl_date
        , a.idx
        , a.email, a.pwd, a.check_id, a.check_nm
        , a.goods_no, a.goods_sub, g.goods_nm
        from qna a
        inner join code ca on ca.code_kind_cd = 'G_QNA_TYPE' and ca.code_id = a.qna_type
        left outer join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
        where a.idx = '$idx'
    ";
    $result = DB::select($query);
    return response()->json([
        "code" => 200,
        "body" => $result
    ]);
  }

  public function check($idx = '', Request $request){
    $id = Auth('head')->user()->id;
    $name = Auth('head')->user()->name;
    $cmd    = $request->input("cmd");
    $idx    = $request->input("idx");
    $code = 0;
    $qa_result = 500;
    //$cmd    = $request->input("cmd");
    $query = "
    select
        ifnull(check_id,'') as check_id, ifnull(check_nm,'') as check_nm
    from qna
    where idx = $idx
    ";
    $qna_rs = DB::select($query);
    $_check_id = $qna_rs[0]->check_id;
    $_check_nm = $qna_rs[0]->check_nm;

    if($cmd == "checkin"){ // 접수
        if($_check_id != "" && $_check_id != $id){
            $code = -1;
        }else{
            $update_items = [
                "check_id" => $id,
                "check_nm" => $name
            ];

            try {
                DB::table('qna')
                ->where('idx','=', $idx)
                ->update($update_items);
                $qa_result = 200;
            } catch(Exception $e){
                $qa_result = 500;
            }
            if($qa_result == 200){
                $code = 1;
            }else{
                $code = 0;
            }

        }
    }else if($cmd == "checkout"){
        $update_items = [
            "check_id" => '',
            "check_nm" => ''
        ];

        try {
            DB::table('qna')
            ->where('idx','=', $idx)
            ->update($update_items);
            $qa_result = 200;
        } catch(Exception $e){
            $qa_result = 500;
        }
        if($qa_result == 200){
            $code = 1;
        }else{
            $code = 0;
        }

    }

    return response()->json([
        "code" => 200,
        "qa_code" => $code
    ]);
  }

  public function save($idx = '', Request $request){
    $id = Auth('head')->user()->id;
    $name = Auth('head')->user()->name;

    $conf = new Conf();
    $cfg_shop_name = "";
    $cfg_shop_name = $conf->getConfigValue("shop","name");
    $cfg_kakao_yn	= $conf->getConfigValue("kakao","kakao_yn");
    $shop_tel = $conf->getConfigValue("shop","phone");
    $cfg_sms = $conf->getConfigValue("sms","qa_msg");

    $cfg_from_email = $conf->getConfig("shop","email");
    $host = $conf->getConfig("shop","domain");

    $qna_resuslt = 0;
    $sms_msg = "";


    $sms = new SMS([
        'admin_id' => $id,
        'admin_nm' => $name,
    ]);


    $ans_subject = $request->input("ans_subject");
    $answer = $request->input("answer");
    $c_ans_yn = $request->input("c_ans_yn", "Y");
    $s_aopen_yn = $request->input("a_open_yn");
    $ans_nm = $request->input("ans_nm",$name);

    //sms, email
    $user_name = $request->input("user_name");
    $user_mobile = $request->input("user_mobile");
    $user_email = $request->input("user_email");
    $sms_yn = $request->input("sms_yn");
    $email_yn = $request->input("email_yn");
    $qa_subject = $request->input("qa_subject");
    $qa_regi_date = $request->input("qa_regi_date");
    $user_question = $request->input("user_question");
	$check_id	= "";
	$check_nm	= "";

    $ans_values = [
        "ans_subject"	=> $ans_subject,
        "answer"		=> $answer,
        "ans_id"		=> $id,
        "ans_nm"		=> $ans_nm,
        "ans_yn"		=> $c_ans_yn,
        "admin_open_yn"	=> $s_aopen_yn,
        "check_id"		=> $check_id,
        "check_nm"		=> $check_nm,
        "ans_date"		=> now(),
        "upd_date"		=> now(),
    ];

    try {
        DB::table('qna')
        ->where('idx','=',$idx)
        ->update($ans_values);
        //$code = 200;
        $qna_resuslt = 1;
    } catch(Exception $e){
        //$code = 500;
        $qna_resuslt = 0;
    }

    /******************************************************
    * 테스트 위해 아래로 휴대폰 번호 임시 지정
    ******************************************************/
    //$user_mobile = "010-9877-2675";
    //$user_name = "테스트";

    if($sms_yn == "Y"){
        $msgarr = array(
            "SHOP_NAME" => $cfg_shop_name,
            "USER_NAME" => $user_name,
        );
        $sms_msg = $sms->MsgReplace($cfg_sms, $msgarr);

        if($user_mobile != ""){
            //$sms->Send($sms_msg, $user_mobile, $user_name, $shop_tel);
			$sms->SendAligoSMS( $user_mobile, $sms_msg, $user_name );
        }
    }else{


        //$user = array( 'email'=>'chieka@naver.com', 'name'=>'변지현' );
        //$data = array( 'detail'=>'Your awesome detail here', 'name' => "변지현" );

        ## 메일 발송
        /*
        $user = array( 'email' => 'chieka@naver.com', 'name' => '변지현' );
        $data = array( 'detail'=> 'Your awesome detail here 테스트!!!', 'name' => $user['name'] );
        //Mail::send('emails.welcome', $data, function($message) use ($user) {
            Mail::send('head_common.mail_all', $data, function($message) use ($user) {
            $message->from("help@netpx.co.kr", "넷피엑스");
            $message->to($user['email'], $user['name'])->subject('Welcome!');
        });
        */

    }
    $cfg_from_email = "";


    return response()->json([
        "code" => 200,
        "qa_code" => $qna_resuslt
    ]);
  }

  public function ChangeShow(Request $request){
    $qna_data = $request->input("data");
    $qna_resuslt_arr = 0;
    $qna_resuslt = 0;

    $query = "update qna set admin_open_yn = 'Y' where idx in ($qna_data) ";
    try {
        DB::update($query);
        //$code = 200;
        $qna_resuslt = 1;
    } catch(Exception $e){
        //$code = 500;
        $qna_resuslt = 0;
    }

    //echo "query : ".$query;
    return response()->json([
        "code" => 200,
        "qa_code" => $qna_resuslt
    ]);
  }

	public function GetImage($idx)
	{
		$sql	= " select a.img_url from qna_image a where a.qna_idx = :idx ";
        $result = DB::select($sql, ['idx' => $idx]);

        return response()->json([
            "code"	=> 200,
            "body"	=> $result
        ]);
	}

}
