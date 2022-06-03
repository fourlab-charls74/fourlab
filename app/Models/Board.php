<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;

class Board
{
    private $board_id;
	private $upload_path;
    private $upload_dir;
    
    // 본문 얻기
	public function GetContents( $b_no ){

		$sql = "
			select
				b_no, board_id, subject, content, user_id, user_nm, email
				, is_notice, is_secret, is_html, hit, ip
				, comment_cnt, date_format(regi_date,'%Y.%m.%d %H:%i:%s') as regi_date
				, loc, gidx, step, reserve1, reserve2, reserve3
			from board
			where b_no = '$b_no'
		";
		$result = DB::selectOne($sql);

		return $result;
    }
    
    // 설정값 얻기
	public function GetConfig( $board_id ){

		$sql = "
			select
				board_id, board_nm, rights, rights_write, rights_comment, functions, display_writer, header_html, footer_html
				, cfg_reserve1, cfg_reserve2, cfg_reserve3, board_type, display_comment_writer
			from board_config
			where board_id = '$board_id'
		";
		$row = DB::selectOne($sql);
        $is_secret = "0";
        if(($row->functions & 8) == 8){
            $is_secret = 1;
        }else{
            $is_secret = 0;
        }

        $config = [
            "board_id"  => $row->board_id,
            "board_nm"  => $row->board_nm,
            "rights"    => $row->rights,
            "rights_write" => $row->rights_write,
            "rights_comment" => $row->rights_comment,
            "functions" => $row->functions,
            "display_writer" => $row->display_writer,
            "header_html" => stripslashes($row->header_html),
            "footer_html" => stripslashes($row->footer_html),
            "cfg_reserve1" => $row->cfg_reserve1,
            "cfg_reserve2" => $row->cfg_reserve2,
            "cfg_reserve2" => $row->cfg_reserve3,
            "cfg_reserve3" => $row->board_type,
            "board_type" => $row->board_type,
            "display_comment_writer" => $row->display_comment_writer,
            "is_secret" => $is_secret
        ];

        
		return $config;
    }
    
    // 첨부 파일 얻기
	function GetFiles( $b_no )
	{
		$files = array();

		$sql = "
			select f_no, b_no, file_nm, file_size, file_type
			from board_file
			where b_no = '$b_no'
		";
		$rows = DB::select($sql);
		foreach($rows as $row){
			$file_nm = $row->file_nm;
			$row->file_nm_enc = rawurlencode($file_nm);
			$files[] = $row;
		}
		return $files;
    }
    

    // 관련글
	function GetRelateContents( $board_id, $b_no )
	{
		$relate_contents = array();

		$sql = "
			select a.b_no, a.subject, a.hit, a.user_id, a.user_nm, a.regi_date, a.step, a.comment_cnt
			from board a
			where
				a.board_id = '$board_id'
				and a.gidx = (
					select gidx from board where b_no = '$b_no'
				)
			order by a.loc
        ";
		$result = DB::select($sql);

		// 관련글이 없는 경우
		if(count($result)<=1 ){
			return $relate_contents;
		}

		// 관련글이 있는 경우
		foreach ($result as $rows) {

			$step = $rows->step;
			$subject= $rows->subject;
			$comment_cnt = $rows->comment_cnt;

			$step_space = "";
			if($step > 0){
				for($k=1;$k<=$step;$k++){ $step_space .= "  "; }
				$step_space .= "┗ ";
			}
			$rows->subject = $step_space . $subject;

			$relate_contents[] = $rows;

		}
		return $relate_contents;
    }
    
    // 댓글
	function GetComments( $b_no )
	{
		$sql = "
			select * from board_comment
			where b_no = '$b_no'
			order by regi_date desc
		";
		$result = DB::select($sql);
		return $result;
    }
    

    /* 댓글 등록 */
	function AddComment($comment){

		$b_no		= $comment['b_no'];
		$content	= $comment['content'];
		$user_id	= $comment['user_id'];
		$user_nm	= $comment['user_nm'];
		$is_secret	= $comment['is_secret'];
		$ip			= $comment['ip'];

        $update_item = [
            "comment_cnt" => "comment_cnt + 1",
			"upd_date" => "now()"
        ];
        try {
            DB::table('board')
            ->where('b_no','=',$b_no)
            ->update($update_item);
            $result_code = 1;
        } catch(Exception $e){
            $result_code = 0;
        }

        if($result_code==1){
            $sql = "
                insert into board_comment (
                    b_no, content, user_id, user_nm, is_secret,ip, regi_date
                ) values (
                    '$b_no', '$content', '$user_id', '$user_nm', '$is_secret','$ip', now()
                )
            ";

            try {
                DB::insert($sql);
                $result_code = 1;
            } catch(Exception $e){
                $result_code = -1;
            };

        }
        return ($result_code==1) ? TRUE : FALSE;
    }
    
    function EditSecret( $comment ){
        $result_code = 0;
        $c_no = $comment["c_no"];

        $sql = "
            update board_comment set is_secret = (1-is_secret) where c_no = $c_no
        ";
        
        try {
            DB::update( $sql);
            $result_code = 1;
        } catch(Exception $e){
            $result_code = 0;
        }

        return $result_code;

    }


    // 댓글 삭제
	function DelComment( $comment ){
        $result_code = 0;
		$b_no = $comment["b_no"];
		$c_no = $comment["c_no"];

		$sql = "
			update board set comment_cnt = if(comment_cnt = 0, 0, comment_cnt - 1) where b_no = '$b_no'
		";
        //$this->conn->Execute($sql);
        try {
            DB::update( $sql);
            $result_code = 1;
        } catch(Exception $e){
            $result_code = 0;
        }

        if($result_code==1){
            $sql = "
                delete from board_comment where c_no = '$c_no'
            ";
            try {
                DB::table('board_comment')->where([
                   'c_no' => $c_no
               ])->delete();

               $result_code = 1;
           } catch(Exception $e){
               $result_code = -1;
           }
        }
        return $result_code;
    }
    
    // 게시판 구분 얻기
	function GetBoardIds(){

		$sql = "
			select board_id, board_nm
			from board_config
			where is_use = '1'
			order by board_nm
		";
		$result = DB::select($sql);
		return $result;
    }
    
    // 게시물 등록
	function AddContents( $contents ){

		$board_id	= $contents["board_id"];
		$content	= $contents["content"];
		$subject	= $contents["subject"];
		$user_id	= $contents["user_id"];
		$user_nm	= $contents["user_nm"];
		$email		= $contents["email"];
		$is_notice	= $contents["is_notice"];
		$is_secret	= $contents["is_secret"];
		$is_html	= $contents["is_html"];
		$is_show	= $contents["is_show"];
		$ip			= $contents["ip"];
		$reserve1	= $contents["reserve1"];
		$reserve2	= $contents["reserve2"];
		$reserve3	= $contents["reserve3"];

		// group 아이디 얻기
		$gidx = $this->GetGroupIdx($board_id);
		
		$values = [
            "board_id" => $board_id, 
            "subject" => $subject, 
            "content" => $content, 
            "user_id" => $user_id, 
            "user_nm" => $user_nm, 
            "email" => $email, 
            "is_notice" => $is_notice, 
            "is_secret" => $is_secret, 
            "is_html" =>$is_html, 
            "is_show" => $is_show, 
            "hit" => "0", 
            "comment_cnt" => "0", 
            "file_cnt" => "0", 
            "gidx" => $gidx, 
            "step" => "0", 
            "loc" => "0", 
            "ip"=> $ip,
            "reserve1" => $reserve1, 
            "reserve2" => $reserve2,
            "reserve3" => $reserve3,
			"regi_date" => now(), 
            "upd_date" => now()
        ];

        $b_no = DB::table("board")->insertGetId($values);
            
		return $b_no;
    }

    // 그룹 번호 얻기
	function GetGroupIdx($board_id){

		$sql = "
			select ifnull(max(gidx),0)+1 as max from board
		";
		$row = DB::selectOne($sql);
		$max_gidx = $row->max;
		return $max_gidx;
	}

	/* 수정 */
	function EditContents($contents){
		$code = 0;
		$board_id	= $contents["board_id"];
		$b_no		= $contents["b_no"];
		$subject	= $contents["subject"];
		$content	= $contents["content"];
		$user_id	= $contents["user_id"];
		$user_nm	= $contents["user_nm"];
		$email		= $contents["email"];
		$is_notice	= $contents["is_notice"];
		$is_secret	= $contents["is_secret"];
		$is_html	= $contents["is_html"];
		$is_show	= $contents["is_show"];
		$ip			= $contents["ip"];
		$reserve1	= $contents["reserve1"];
		$reserve2	= $contents["reserve2"];
		$reserve3	= $contents["reserve3"];

		$update_items = [
			"subject" => $subject,
			"content" => $content,
			"user_id" => $user_id,
			"user_nm" => $user_nm,
			"email" => $email,
			"is_notice" => $is_notice,
			"is_secret" => $is_secret,
			"is_html" => $is_html,
			"is_show" => $is_show,
			"ip" => $ip,
			"reserve1" => $reserve1,
			"reserve2" => $reserve2,
			"reserve3" => $reserve3,
			"upd_date" => now()
		];

		try {
			DB::table('board')
			->where('b_no','=', $b_no)
			->update($update_items);
			$code = 1;
		} catch(Exception $e){
			$code = 0;
		}

		if($code ==1 ){
			return $b_no;
		}else{
			return 0;
		}

	}


	function ReplyContents($contents){
		
		$board_id	= $contents["board_id"];
		$subject	= $contents["subject"];
		$content	= $contents["content"];
		$user_id	= $contents["user_id"];
		$user_nm	= $contents["user_nm"];
		$email		= $contents["email"];
		$gidx		= $contents["gidx"];
		$loc		= $contents["loc"];
		$step		= $contents["step"];
		$is_notice	= $contents["is_notice"];
		$is_secret	= $contents["is_secret"];
		$is_html	= $contents["is_html"];
		$is_show	= $contents["is_show"];
		$ip			= $contents["ip"];
		$reserve1	= $contents["reserve1"];
		$reserve2	= $contents["reserve2"];
		$reserve3	= $contents["reserve3"];

		// 로케이션 변경
		$sql = "
			update board set loc = loc + 1
			where gidx = '$gidx' and loc > '$loc'
		";
		try {
			DB::update($sql);
			$code = 1;
		} catch(Exception $e){
			$code = 0;
		}

		$loc_plus = $loc + 1;

		$sql = "
			insert into board (
				board_id, subject, content, user_id, user_nm, email, is_notice, is_secret, is_html, is_show
				, hit, comment_cnt, gidx, step, loc, ip, reserve1, reserve2, reserve3, regi_date, upd_date
			) values (
				'$board_id', '$subject', '$content', '$user_id', '$user_nm', '$email', '$is_notice', '$is_secret', '$is_html', '$is_show'
				, 0, 0,'$gidx', '$step', '$loc_plus', '$ip', '$reserve1', '$reserve2', '$reserve3', now(), now()
			)
		";

		$values = [
            "board_id" => $board_id, 
            "subject" => $subject, 
            "content" => $content, 
            "user_id" => $user_id, 
            "user_nm" => $user_nm, 
            "email" => $email, 
            "is_notice" => $is_notice, 
            "is_secret" => $is_secret, 
            "is_html" =>$is_html, 
            "is_show" => $is_show, 
            "hit" => "0", 
            "comment_cnt" => "0", 
            "file_cnt" => "0", 
            "gidx" => $gidx, 
            "step" => $step, 
            "loc" => $loc_plus, 
            "ip"=> $ip,
            "reserve1" => $reserve1, 
            "reserve2" => $reserve2,
            "reserve3" => $reserve3,
			"regi_date" => now(), 
            "upd_date" => now()
		];
		
		$b_no = DB::table("board")->insertGetId($values);

		
		return $b_no;
	}

	/* 글 삭제 */
	function DelContents( $b_no, $board_id ){
		$result_code = 0;
		// 업로드 디렉터리
		//$upload_file_path = $this->upload_path . $this->upload_dir;

		// 게시판 설정
		$board_config = $this->GetConfig($board_id);

		// 첨부파일 삭제
		/*
		if($board_config["functions"] & 16 ){
			$this->DelFile($b_no);
		}
		*/

		// 댓글 삭제
		if($board_config["functions"] & 4 ){
			$sql = "
				select c_no from board_comment where b_no = '$b_no'
			";
			//$rs = $this->conn->Execute($sql);
			$result = DB::select($sql);
			foreach ($result as $rows) {
				$c_no = $rows->c_no;
				$this->DelComment($c_no);
			}
		}

		// 글 삭제
		$sql = "
			delete from board where b_no = '$b_no'
		";
		try {
			DB::table('board')->where([
			   'b_no' => $b_no
		   ])->delete();

		   $result_code = 1;
	   } catch(Exception $e){
		   $result_code = 0;
	   }

		return $result_code;
	}
    

	// 게시판 설정 수정	
	function UpdConfig( $config ){

		$board_id = $config["board_id"];
		$board_nm = $config["board_nm"];
		$rights = $config["rights"];
		$g_rights_value = $config["g_rights_value"];
		$rights_view = $config["rights_view"];
		$g_rights_view_value = $config["g_rights_view_value"];
		$rights_write = $config["rights_write"];
		$g_rights_write_value = $config["g_rights_write_value"];
		$rights_comment = $config["rights_comment"];
		$g_rights_comment_value = $config["g_rights_comment_value"];
		$functions = $config["functions"];
		$display_writer = $config["display_writer"];
		$display_comment_writer = $config["display_comment_writer"];
		$display_column = $config["display_column"];
		$header_html = $config[ "header_html"];
		$footer_html = $config[ "footer_html"];
		$user_id = $config["user_id"];
		$user_nm = $config["user_nm"];
		$is_use = $config["is_use"];
		$cfg_reserve1 = $config["cfg_reserve1"];
		$cfg_reserve2 = $config["cfg_reserve2"];
		$cfg_reserve3 = $config["cfg_reserve3"];
		$row_cnt = $config["row_cnt"];
		$page_cnt = $config["page_cnt"];
		$board_type = $config["board_type"];

		$sql = "
			update board_config set
				board_nm = '$board_nm'
				, rights = '$rights'
				, rights_view = '$rights_view'
				, rights_write = '$rights_write'
				, rights_comment = '$rights_comment'
				, functions = '$functions'
				, display_writer = '$display_writer'
				, display_comment_writer = '$display_comment_writer'
				, display_column = '$display_column'
				, header_html = '$header_html'
				, footer_html = '$footer_html'
				, cfg_reserve1 = '$cfg_reserve1'
				, cfg_reserve2 = '$cfg_reserve2'
				, cfg_reserve3 = '$cfg_reserve3'
				, row_cnt = '$row_cnt'
				, page_cnt = '$page_cnt'
				, board_type = '$board_type'
				, user_id = '$user_id'
				, user_nm = '$user_nm'
				, upd_date = now()
				, is_use = '$is_use'
			where board_id = '$board_id'
		";

		DB::update($sql);

		$this->DelBoardAuth($board_id);
		$this->AddBoardAuth($board_id, $g_rights_value, "L");
		$this->AddBoardAuth($board_id, $g_rights_view_value, "R");
		$this->AddBoardAuth($board_id, $g_rights_write_value, "W");
		$this->AddBoardAuth($board_id, $g_rights_comment_value, "C");		
	}

	function DelBoardAuth($board_id = "") {
		$sql = "
			delete from board_auth
			where board_id = '$board_id'
		";
		DB::delete($sql);
	}

	function AddBoardAuth($board_id = "", $value = "", $type = "" ) {
		if ($value != "") {
			$datas = explode(",", $value);

			foreach($datas as $data) {
				list($access, $group_no) = explode("|", $data); 
				$sql = "
					insert into board_auth (
						board_id, type, group_no, access
					) values (
						'$board_id', '$type', '$group_no', '$access'
					)
				";
				DB::insert($sql);

			}
		}
	}
	// 게시판 설정 등록
	function AddConfig( $config ){
		$board_id = $config["board_id"];
		$board_nm = $config["board_nm"];
		$rights = $config["rights"];
		$g_rights_value = $config["g_rights_value"];
		$rights_view = $config["rights_view"];
		$g_rights_view_value = $config["g_rights_view_value"];
		$rights_write = $config["rights_write"];
		$g_rights_write_value = $config["g_rights_write_value"];
		$rights_comment = $config["rights_comment"];
		$g_rights_comment_value = $config["g_rights_comment_value"];
		$functions = $config["functions"];
		$display_writer = $config["display_writer"];
		$display_comment_writer = $config["display_comment_writer"];
		$display_column = $config["display_column"];
		$header_html = $config[ "header_html"];
		$footer_html = $config[ "footer_html"];
		$user_id = $config["user_id"];
		$user_nm = $config["user_nm"];
		$is_use = $config["is_use"];
		$cfg_reserve1 = $config["cfg_reserve1"];
		$cfg_reserve2 = $config["cfg_reserve2"];
		$cfg_reserve3 = $config["cfg_reserve3"];
		$row_cnt = $config["row_cnt"];
		$page_cnt = $config["page_cnt"];
		$board_type = $config["board_type"];	
		
		$sql = "
			insert into board_config (
				board_id, board_nm, rights, rights_view, rights_write, rights_comment, functions, display_writer, display_comment_writer, display_column
				, header_html, footer_html, cfg_reserve1, cfg_reserve2, cfg_reserve3, row_cnt, page_cnt, board_type
				, user_id, user_nm, regi_date, upd_date, content_cnt, content_date, comment_cnt, comment_date, is_use
			) values (
				'$board_id', '$board_nm', '$rights', '$rights_view', '$rights_write', '$rights_comment', '$functions', '$display_writer', '$display_comment_writer'
				, '$display_column', '$header_html', '$footer_html', '$cfg_reserve1', '$cfg_reserve2', '$cfg_reserve3', '$row_cnt', '$page_cnt', '$board_type'
				, '$user_id', '$user_nm', now(), now(), 0, null, 0, null, '$is_use'
			)
		";		

		DB::insert($sql);
				
		$this->AddBoardAuth($board_id, $g_rights_value, "L");
		$this->AddBoardAuth($board_id, $g_rights_view_value, "R");
		$this->AddBoardAuth($board_id, $g_rights_write_value, "W");
		$this->AddBoardAuth($board_id, $g_rights_comment_value, "C");
	}


	// 게시판 설정 삭제 - 모든 데이터 삭제
	function DelConfig( $board_id ){
		$sql = "delete from board_config where board_id = '$board_id' ";
		DB::delete($sql);

		$sql = "delete from board where board_id = '$board_id'";
		DB::delete($sql);

		$sql = "delete from board_file where board_id = '$board_id'";
		DB::delete($sql);

		$sql = "delete from board_comment where board_id = '$board_id'";
		DB::delete($sql);
		
		$this->DelBoardAuth($board_id);
	}
}
