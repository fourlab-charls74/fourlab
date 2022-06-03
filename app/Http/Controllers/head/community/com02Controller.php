<?php

namespace App\Http\Controllers\head\community;


use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;
use App\Models\Board;

class com02Controller extends Controller
{
	public function index($type='', $board_id='') {
		$sql = "
			select a.board_id, a.board_nm
			from board_config a
			where is_use = 1
			order by a.board_nm 
		";

		$boards = DB::select($sql);

		$values = [
            'boards' => $boards,
            'board_id' => $board_id,
            'layout' => $type ? 'head_with.layouts.layout-nav' : 'head_with.layouts.layout'
		];
		return view( Config::get('shop.head.view') . '/community/com02',$values);
	}

	public function search(Request $request) {
        

        
        
		// 게시판 ID
        $board_id	= $request->input("board_id","");

        // 검색 파라미터
        $name		= $request->input("name");
        $id		= $request->input("id");
        $subject	= $request->input("subject");
        $content	= $request->input("content");

        // 페이징 파라미터
        $page		= $request->input("page",1);
        $limit		= $request->input("limit",100);
        $order_type	= $request->input("order_type");
        $ord		= $request->input("ord","desc");

		if ($page < 1 or $page == "") $page = 1;
        $page_size = $limit;

        if($board_id != "" && $page == 1) {
            $order = " a.is_notice desc,a.gidx $ord, a.loc asc ";
            if($order_type != "" ){
                $order = " a.is_notice desc,a.b_no $ord ";
            }
        } else {
            $order = " a.gidx $ord, a.loc asc ";
            if($order_type != "" ){
                $order = " a.b_no $ord ";
            }
        }

        // 검색 조건
        $where = "";
        if($board_id != "") {
            $where .= " and a.board_id = '$board_id' ";
        }
        if($id != "" ) $where .= sprintf(" and a.user_id = '%s' ", $id);
        if($name != "" ) $where .= sprintf(" and a.user_nm = '%s' ", $name);
        if($subject != "" ) $where .= sprintf(" and a.subject like '%s' ", "%" . $subject . "%");
        if($content != "" ) $where .= sprintf(" and a.content like '%s' ", "%" . $content . "%");

		$sql = "
			select count(*) as cnt
			from board a
				inner join board_config b on a.board_id = b.board_id
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
		$arr_header = array("data_cnt" => $data_cnt, "page_cnt" => $page_cnt);
		
		if($limit == -1){
            $limits = "";
        } else {
            $limits = " limit $startno, $page_size ";
        }

		// 게시물 얻기
        $sql = "
            select '' as chkbox, a.b_no, b.board_nm, a.subject, a.hit, a.comment_cnt, a.file_cnt, a.user_nm, a.user_id, a.regi_date, a.ip,
                if(a.is_notice = 1,'Y','N') as is_notice, if(a.is_secret = 1,'Y','N') as is_secret,
                a.board_id, a.step, if(a.point_yn = 'Y', concat(a.point,''), a.point_yn ) as points
            from board a
                inner join board_config b on a.board_id = b.board_id
            where 1=1 $where 
            order by $order
            $limits
		";
		$result = DB::select($sql);

		$datas = array();

		foreach($result as $rows){
            $step = $rows->step;
            $subject= $rows->subject;
            $step_space = "";

            if($step > 0){
                for($k=1;$k<=$step;$k++){ $step_space .= "  "; }
                $step_space .= "┗ ";
            }
            $rows->subject = $step_space . $subject;
			$datas[] = $rows;
		}
		

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => count($datas),
                "page_total" => $page_cnt,
                "subject" => utf8_decode($request->input("subject"))
            ),
            "body" => $datas
        ]);

	}

	public function Read($b_no = ''){
		// 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_code = $conf->getConfigValue("shop","code");
        $cfg_shop_name = $conf->getConfigValue("shop","name");
		$return_code = 1;

		// Parameters
		
		$contents		= array();
		$config			= array();
		$files			= array();
		$files_total	= 0;
		$comments		= array();
		$board_id		= "";
		$board_ids		= array();
        // 에러 처리
        if($b_no == ""){
            $return_code = 0;
        }else{
			// 게시판
			
			$board = new Board();

			// 게시물
			$contents = $board->GetContents($b_no);
			$board_id = $contents->board_id;

			// 게시판 설정
			$config = $board->GetConfig( $board_id );
			
			// 관련 글
			$relate_contents = $board->GetRelateContents( $board_id, $b_no  );
			$relate_total = count($relate_contents);

			// 첨부파일
			$files = $board->GetFiles( $b_no );
			$files_total = count($files);

			$brand_all = $this->brand_all($contents->reserve3,$contents->reserve1);

			// 댓글
			$comments = $board->GetComments( $b_no );


		}

		$values = [
			"b_no"				=> $b_no,
			"contents"			=> $contents,
			"config"			=> $config,
			"relate_contents"	=> $relate_contents,
			"relate_total"		=> $relate_total,
			"files"				=> $files,
			"files_total"		=> $files_total,
			"board_id"			=> $board_id,
			"brand_all"			=> $brand_all,
			"comments"			=> $comments,
			"return_code"		=> $return_code
		];
		//$values = [];
		return view( Config::get('shop.head.view') . '/community/com02_show',$values);
	}

	public function Detail(Request $request){
		// 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_code = $conf->getConfigValue("shop","code");
        $cfg_shop_name = $conf->getConfigValue("shop","name");

		// Parameters
		$b_no	= $request->input("b_no");
        $board_id	= $request->input("board_id");
        $cmd		= $request->input("cmd","add");

        // 게시판 설정
        $board = new Board();

        $baord_contents = array();
        $brand_all = array();

        $files = array();
        $files_total = 0;
        $files_size = 0;
		$return_code = 1;
		$config = array();
		$board_ids = array();

		if( $b_no == "" ){
            $brand_all = $this->brand_all("","");

            $baord_contents["subject"] = "";
            $baord_contents["content"] = "";
            $baord_contents["is_notice"] = "0";
            $baord_contents["is_secret"] = "0";
            $baord_contents["user_id"] = $cfg_shop_code; // $this->user["id"];
            $baord_contents["user_nm"] = $cfg_shop_name; //$this->user["name"];
            $baord_contents["gidx"] = 0;
            $baord_contents["step"] = 0;
            $baord_contents["loc"] = 0;
            $baord_contents["reserve1"] = "";
            $baord_contents["reserve2"] = "";
            $baord_contents["reserve3"] = "";

			$config = [
            "board_id"  => "",
            "board_nm"  => "",
            "rights"    => "",
            "rights_write" => "",
            "rights_comment" => "",
            "functions" => "",
            "display_writer" => "",
            "header_html" => "",
            "footer_html" => "",
            "cfg_reserve1" => "",
            "cfg_reserve2" => "",
            "cfg_reserve2" => "",
            "cfg_reserve3" => "",
            "board_type" => "",
            "display_comment_writer" => "",
            "is_secret" => ""
        ];

			

        }else{
			// 게시물 얻기
            $contents = $board->GetContents( $b_no );
	
            if( $cmd == "reply"){
                $baord_contents["subject"] = "".$contents->subject;
                $baord_contents["content"] = "";
                $baord_contents["is_notice"] = $contents->is_notice;
                $baord_contents["user_id"] = $cfg_shop_code; // $this->user["id"];
                $baord_contents["user_nm"] = $cfg_shop_name; //$this->user["name"];
                $baord_contents["gidx"] = $contents->gidx;
                $baord_contents["step"] = $contents->step + 1;
                $baord_contents["loc"] = $contents->loc;
                $baord_contents["reserve1"] = $contents->reserve1;
                $baord_contents["reserve2"] = $contents->reserve2;
                $baord_contents["reserve3"] = $contents->reserve3;
				$baord_contents["is_secret"] = $contents->is_secret;

                $brand_all = $this->brand_all($contents->reserve3,$contents->reserve1);

            } else{
                //$baord_contents = $contents;
				$baord_contents["subject"] = "".$contents->subject;
                $baord_contents["content"] = $contents->content;
                $baord_contents["is_notice"] = $contents->is_notice;
                $baord_contents["user_id"] = $cfg_shop_code; // $this->user["id"];
                $baord_contents["user_nm"] = $cfg_shop_name; //$this->user["name"];
                $baord_contents["gidx"] = $contents->gidx;
                $baord_contents["step"] = $contents->step + 1;
                $baord_contents["loc"] = $contents->loc;
                $baord_contents["reserve1"] = $contents->reserve1;
                $baord_contents["reserve2"] = $contents->reserve2;
                $baord_contents["reserve3"] = $contents->reserve3;
				$baord_contents["is_secret"] = $contents->is_secret;

                $brand_all = $this->brand_all($contents->reserve3,$contents->reserve1);

                // 첨부파일
                $files = $board->GetFiles( $b_no );
                $files_total = count($files);
            }

			for($i=0;$i<count($files);$i++){
				$files_size += $files[$i]->file_size;
			}

		}

		// 게시판 구분
		$board_ids	= $board->GetBoardIds();

		// 게시판 설정값 
		if($board_id != ""){
			$config		= $board->GetConfig( $board_id );
		}

		$values = [
			"b_no"				=> $b_no,
			"contents"			=> $baord_contents,
			"config"			=> $config,
			"files"				=> $files,
			"files_total"		=> $files_total,
			"files_size"		=> $files_size,
			"board_id"			=> $board_id,
			"board_ids"			=> $board_ids,
			"return_code"		=> $return_code,
			"type"				=> $cmd
		];

		return view( Config::get('shop.head.view') . '/community/com02_input',$values);

	}

	public function Save(Request $request){
		// 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_name	= $conf->getConfigValue("shop","name");
        $cfg_shop_code	= $conf->getConfigValue("shop","code");

        // 사용자
        $user_id = $cfg_shop_code;
        $user_nm = $cfg_shop_name;

		$return_code = 0;

        // 게시물
        $type			= $request->input("type");
        $b_no			= $request->input("b_no");
        $board_id		= $request->input("board_id");
        $subject		= $request->input("subject");
        $content		= $request->input("content");
        $email			= $request->input("email");
        $is_notice		= $request->input("is_notice","0");
        $is_secret		= $request->input("is_secret","0");
        $is_html		= $request->input("is_html","1");
        $is_show		= $request->input("is_show","1");
        $gidx			= $request->input("gidx");
        $step			= $request->input("step");
        $loc			= $request->input("loc");
        $brand			= $request->input("brand");
        $brand_nm		= $request->input("brand_nm");
        $reserve1		= $request->input("reserve1");
        $reserve2		= $request->input("reserve2");
        $reserve3		= $request->input("reserve3");

		$ip				= $_SERVER["REMOTE_ADDR"];

        if($brand != ""){
            $reserve3 = $brand;
        }

        if($brand_nm != ""){
            $reserve1 = $brand_nm;
        }

		
		// 게시판 설정
        $board = new Board();
        $board_config = $board->GetConfig($board_id);

		if($b_no == ""){

            $contents = array(
                "board_id"		=> $board_id
                , "subject"		=> $subject
                , "content"		=> $content
                , "user_id"		=> $user_id
                , "user_nm"		=> $user_nm
                , "email"		=> $email
                , "is_notice"	=> $is_notice
                , "is_secret"	=> $is_secret
                , "is_html"		=> $is_html
                , "is_show"		=> $is_show
                , "ip"			=> $ip
                , "reserve1"	=> $reserve1
                , "reserve2"	=> $reserve2
                , "reserve3"	=> $reserve3
            );

            $b_no = $board->AddContents( $contents );

            // 파일 업로드
//            if( $board_config["functions"] & 16 ){

            //echo ( $result ) ? $b_no:"0";

        }else if ( $type == "detail") {
			$contents = array(
                "board_id"		=> $board_id
                , "b_no"		=> $b_no
                , "subject"		=> $subject
                , "content"		=> $content
                , "user_id"		=> $user_id
                , "user_nm"		=> $user_nm
                , "email"		=> $email
                , "is_notice"	=> $is_notice
                , "is_secret"	=> $is_secret
                , "is_html"		=> $is_html
                , "is_show"		=> $is_show
                , "ip"			=> $ip
                , "reserve1"	=> $reserve1
                , "reserve2"	=> $reserve2
                , "reserve3"	=> $reserve3
            );

            $b_no = $board->EditContents( $contents );

		}else if ( $type == "reply") {
			$contents = array(
                "board_id"		=> $board_id
                , "subject"		=> $subject
                , "content"		=> $content
                , "user_id"		=> $user_id
                , "user_nm"		=> $user_nm
                , "email"		=> $email
				, "gidx"		=> $gidx
				, "loc"			=> $loc
				, "step"		=> $step
                , "is_notice"	=> $is_notice
                , "is_secret"	=> $is_secret
                , "is_html"		=> $is_html
                , "is_show"		=> $is_show
                , "ip"			=> $ip
                , "reserve1"	=> $reserve1
                , "reserve2"	=> $reserve2
                , "reserve3"	=> $reserve3
            );

			$b_no = $board->ReplyContents( $contents );
		}
		if($b_no>0){
			$return_code = 1;
		}else{
			$return_code = 0;
		}
		

		return response()->json([
            "code" => 200,
            "return_code" => $return_code,
			"b_no" => $b_no
        ]);
	}

	public function Del(Request $request){
        $b_no		= $request->input("b_no");
        $board_id	= $request->input("board_id");

		$return_code = 0;

        // 게시판
        $board = new Board();
        $return_code = $board->DelContents($b_no, $board_id);


        //echo ($result) ? "1":"0";
		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
    }

	public function AddComment(Request $request){
		$return_code = 0;
        // 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_name = $conf->getConfigValue("shop","name");
        $cfg_shop_code = $conf->getConfigValue("shop","code");

        // 사용자
        $user_id = $cfg_shop_code;
        $user_nm = $cfg_shop_name;
        $ip = $_SERVER["REMOTE_ADDR"];
		
		$b_no					= $request->input("b_no");
        $content				= $request->input("content");
        $is_secret				= $request->input("is_secret");
        $display_comment_writer	= $request->input("display_comment_writer");

        if($display_comment_writer == 2) {
            $id = Auth('head')->user()->id;
			$name = Auth('head')->user()->name;
        }


        // 게시판
        $board = new Board();
        $comment = array(
            "b_no" => $b_no,
            "content" => $content,
            "user_id" => $user_id,
            "user_nm" => $user_nm,
            "is_secret" => $is_secret,
            "ip" => $ip
        );
		
        $return_code = $board->AddComment($comment);

        return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
    }

	public function EditSecret($c_no = ''){
		$return_code = 0;
        // 게시판
        $board = new Board();
        $comment = array(
            "c_no" => $c_no
        );

		//print_r($comment);
        $return_code = $board->EditSecret($comment);


        return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
    }

	public function DelComment($c_no = '', Request $request){
        $b_no = $request->input("b_no");
		$return_code = 0;

        // 게시판
        $board = new Board();
        $comment = array(
            "b_no" => $b_no,
            "c_no" => $c_no
        );
		//print_r($comment);
        $return_code = $board->DelComment($comment);
		
		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
    }




	function brand_all($brand_nm, $brand){
		$value = "
			<input type=text class='inputah' name='BRAND_NM' id='BRAND_NM' value='$brand_nm' style='width:130px;' autocomplete='off'  onkeydown='o_ac.ack();' onkeyup='o_ac.ac(\"hidden\", \"BRAND_NM\", \"BRAND\");'
							onblur='o_ac.acb(\"hidden\", \"BRAND_NM\", \"BRAND\");'>
			<button class='btn' style='margin:0 2px;width=25px;' onclick=\"PopSearchBrand('');\">...</button><input type=text class='input' name='BRAND' id='BRAND' value='$brand' style='width:50px;' readonly>
		";
		return $value;
	}
}
  