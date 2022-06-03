<?php

namespace App\Http\Controllers\head\community;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Board;

class com03Controller extends Controller
{
	//
	public function index($type='', $board_id='') {
		$sql = "
			select a.board_id, a.board_nm
			from board_config a
			where is_use = 1
			order by a.board_nm 
		";

		$boards = DB::select($sql);

		$values = [
			"boards"	=> $boards,
            'board_id' => $board_id,
            'layout' => $type ? 'head_with.layouts.layout-nav' : 'head_with.layouts.layout'
		];
		return view( Config::get('shop.head.view') . '/community/com03', $values);
	}

	public function search(Request $request){
		$board_id 	= $request->input("board_id", "");
        $subject 	= $request->input("subject", "");
        $content   	= $request->input("content", "");
        $user_id 	= $request->input("user_id", "");
        $name 	= $request->input("name", "");
        $page 		= $request->input("page", 1);
        $limit		= $request->input("limit",100);
		$order_type = $request->input("order_type");
		$ord 		= $request->input("ord");
		
		$data_cnt = 0;
        $page_cnt = 10;
        $data = array();

        $where = "";

        if($board_id != "" ) $where .= sprintf(" and a.board_id = '%s' ",$board_id);
        if($user_id != "" ) $where .= sprintf(" and c.user_id = '%s' ",$user_id);

        if($subject != "" ) $where .= sprintf(" and a.subject like '%s' ","%" . $subject . "%");
        if($content != "" ) $where .= sprintf(" and c.content like '%s' ","%" . $content . "%");

        $sql = "
			select count(*) as cnt
			from board a
				inner join board_comment c on a.b_no = c.b_no
			where 1=1 $where
		";
		//echo $sql;
        $row = DB::selectOne($sql);
        $data_cnt = $row->cnt;
		
		$page_size = $limit;
		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
		$startno = 0;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}
		$end   = $limit;

        if($name != "" ) $where .= sprintf(" and m.name = '%s' ",$name);

		$sql = "
            select 
              c.c_no,c.b_no,c.board_id,bc.board_nm,a.subject,c.content,c.user_id,m.name as user_nm,c.ip, if(c.is_secret = 1,'Y','N') as is_secret,c.regi_date
            from board a
                inner join board_config bc on a.board_id = bc.board_id
                inner join board_comment c on a.b_no = c.b_no
                inner join member m ON m.user_id = c.user_id
            where 1=1 $where
            order by c.c_no $ord
            limit $startno, $end
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

	public function EditSecret(Request $request){
        $data = $request->input("data");
        $use_yn = $request->input("use_yn");

        $code = 200;
        $msg = '';

        try {
            DB::beginTransaction();
            
            foreach($data as $c_no) {
                if($c_no != '') {
                    $sql = "
                        update board_comment 
                            set is_secret = $use_yn
                        where c_no = $c_no
                    ";
                    DB::update($sql);
                }
            }

            $msg = '비밀글여부가 정상적으로 변경되었습니다.';
			DB::commit();
		} catch (Exception $e) {
            DB::rollback();
            $cod = 500;
            $msg = '에러가 발생했습니다. 다시 시도해주세요.';
        }

		return response()->json([
            "code" => $code,
            "message" => $msg,
        ]);
    }


	public function DelComment(Request $request){

        $data = $request->input("data");
		$result_code = 0;

        //$c_nos = explode(",",$data);

        for($i=0;$i<count($data);$i++){

            $c_no = $data[$i];

            if($c_no != ""){
                $sql = "
                    select b_no from board_comment where c_no = '$c_no'
                ";
                $rows = DB::selectOne($sql);
                $b_no = $rows->b_no;

                $sql = "
                    update board set comment_cnt = if(comment_cnt = 0, 0, comment_cnt - 1) where b_no = '$b_no'
                ";
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
            }
        }

        return response()->json([
            "code" => 200,
            "return_code" => $result_code
        ]);
    }
}
