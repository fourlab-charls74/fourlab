<?php

namespace App\Http\Controllers\head\community;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use App\Models\Board;
use Exception;

class com01Controller extends Controller
{
    //
    public function index() {
        $values = [];
        return view( Config::get('shop.head.view') . '/community/com01',$values);
    }

    public function show($type, $id='', Request $req) {
      $values = ['type' => $type, 'board_id' => $id];

      $sql = "select group_no,group_nm from user_group order by group_no";
      $values['groups'] = DB::select($sql);
      
      $values['auth'] = array();
      $values['auth']["L"]["A"] = array();
      $values['auth']["R"]["A"] = array();
      $values['auth']["W"]["A"] = array();
      $values['auth']["C"]["A"] = array();

      $values['auth']["L"]["D"] = array();
      $values['auth']["R"]["D"] = array();
      $values['auth']["W"]["D"] = array();
      $values['auth']["C"]["D"] = array();
      
      if ($type === 'edit') {
          $sql = "
              select
                  a.board_id, a.board_nm, a.board_type, a.rights, a.rights_view, a.rights_write, a.rights_comment, a.functions, a.display_writer,
                  a.display_comment_writer, a.display_column, a.header_html, a.footer_html, a.cfg_reserve1, a.cfg_reserve2, a.cfg_reserve3, a.row_cnt, a.page_cnt,
                  a.user_id, a.user_nm, a.regi_date, a.upd_date, a.content_cnt, a.content_date, a.comment_cnt, a.comment_date,
                  a.is_use, b.type, b.access, g.group_no, g.group_nm
              from board_config a
                  left outer join board_auth b on a.board_id = b.board_id
                  left outer join user_group g on b.group_no = g.group_no
              where a.board_id = '$id'
              order by b.auth_no
          ";
          $values['board'] = DB::selectOne($sql);

          $sql = "
              select
                  b.type, g.group_no, g.group_nm,b.access
              from board_config a
                  inner join board_auth b on a.board_id = b.board_id
                  inner join user_group g on b.group_no = g.group_no
              where a.board_id = '$id'
          ";

          $rows = DB::select($sql);

          foreach($rows as $row) {
            $values['auth'][$row->type][$row->access][sprintf("%s|%s",$row->access,$row->group_no)] = $row->group_nm;
          }
      }
      return view( Config::get('shop.head.view') . '/community/com01_show',$values);
    }

    public function search() {
        $name	= Request("name");
        $use_yn	= Request("use_yn");

        // 조건절 설정
        $where = "";

        if($use_yn != "" ) $where .= sprintf(" and a.is_use = '%s' ", $use_yn);
        if($name != "" ) $where .= sprintf(" and a.board_nm like '%s' ","%$name%");

        $sql = "
          select a.board_id, a.board_nm,a.board_type,a.display_writer,a.display_comment_writer,
                a.rights, a.rights_view, a.rights_write, a.rights_comment,
            (select count(*) from board where a.board_id = board_id group by board_id) as content_cnt,
            (select max(regi_date) from board where a.board_id = board_id group by board_id) as content_date,
            (select count(*) from board_comment where a.board_id = board_id group by board_id ) as comment_cnt,
            (select max(regi_date) from board_comment where a.board_id = board_id group by board_id) as comment_date,
            if(a.is_use = 1,'Y','N') as is_use,
            regi_date,upd_date
          from board_config a
          where 1=1 $where
          order by content_date desc
        ";

        $result = DB::select($sql);

        return response()->json([
          "code" => 200,
          "head" => [
            'total' => count($result)
          ],
          "body" => $result
        ]);
    }

    public function id_chk($id='') {
        $sql = "
          select count(*) as cnt
          from board_config
          where board_id = '$id'
        ";

        return response()->json([
          'result' => DB::selectOne($sql)->cnt
        ], 201);
    }

    public function add_comunity($board_id = '', Request $req) {
        try{
            DB::beginTransaction();

            $sql = "select count(*) as cnt from board_config where board_id = '$board_id' ";
            $cnt = DB::selectOne($sql)->cnt;

            if ($cnt > 0) throw new Exception("중복된 아이디입니다.");

            $board = new Board();
    
            $config = $this->__getConfigValues($board_id);
    
            $board->AddConfig($config);

            DB::commit();
            return response()->json($board_id, 201);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function edit_comunity($board_id = '', Request $req) {
        try{
            DB::beginTransaction();

            $board = new Board();
    
            $config = $this->__getConfigValues($board_id);
    
            $board->UpdConfig($config);

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function __getSumFunctionsValue() {
        $functions = Request("functions", "1");

        if ($functions == "1") return 1;

        $datas = explode(',', $functions);
      
        $sum_functions = 1;

        foreach($datas as $val) {
            $sum_functions += $val;
        }

        return $sum_functions;
    }

    private function __getConfigValues($board_id) {
        $board_nm = Request("board_nm");
        $rights = Request("rights");
        $g_rights_value = Request("g_rights_value");
        $rights_view = Request("rights_view");
        $g_rights_view_value = Request("g_rights_view_value");
        $rights_write = Request("rights_write");
        $g_rights_write_value = Request("g_rights_write_value");
        $rights_comment = Request("rights_comment");
        $g_rights_comment_value = Request("g_rights_comment_value");
        $functions = explode(',', Request("functions", "1"));
        $display_writer = Request("display_writer");
        $display_comment_writer = Request("display_comment_writer");
        $header_html = Request("header_html");
        $footer_html = Request("footer_html");
        $display_column = Request("display_column");
        $cfg_reserve1 = Request("cfg_reserve1");
        $cfg_reserve2 = Request("cfg_reserve2");
        $cfg_reserve3 = Request("cfg_reserve3");
        $row_cnt = Request("row_cnt");
        $page_cnt = Request("page_cnt");
        $board_type = Request("board_type");
        $user_id = Auth('head')->user()->id;
        $user_nm = Auth('head')->user()->name;
        $is_use = Request("is_use",1);

        return array(
          "board_id" => $board_id
          , "board_nm" => $board_nm
          , "rights" => $rights
          , "g_rights_value" => $g_rights_value
          , "rights_view" => $rights_view
          , "g_rights_view_value" => $g_rights_view_value
          , "rights_write" => $rights_write
          , "g_rights_write_value" => $g_rights_write_value
          , "rights_comment" => $rights_comment
          , "g_rights_comment_value" => $g_rights_comment_value
          , "functions" => $this->__getSumFunctionsValue()
          , "display_writer" => $display_writer
          , "display_comment_writer" => $display_comment_writer
          , "display_column" => $display_column
          , "header_html" => $header_html
          , "footer_html" => $footer_html
          , "cfg_reserve1" => $cfg_reserve1
          , "cfg_reserve2" => $cfg_reserve2
          , "cfg_reserve3" => $cfg_reserve3
          , "row_cnt" => $row_cnt
          , "page_cnt" => $page_cnt
          , "board_type" => $board_type
          , "user_id" => $user_id
          , "user_nm" => $user_nm
          , "is_use" => $is_use
        );
    }
    public function del_comunity($board_id='') {
        try{
            DB::beginTransaction();
            $sql = "select is_use from board_config where board_id = '$board_id'";
            $row = DB::selectOne($sql);

            if($row->is_use == 0) {
                $board = new Board();
                $board->DelConfig( $board_id );
            } else {
              throw new Exception('게시판이 존재하지 않거나 사용여부를 "아니요"로 변경 후 삭제하여 주십시오.');
            }
      
            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
