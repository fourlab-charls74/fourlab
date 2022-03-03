<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class std20Controller extends Controller
{
    public function index() {
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        $tpl_kind_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_TPL_KIND' order by no asc");
        $qna_type_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_QNA_TYPE' order by no asc");
        $values = [
            "tpl_kind_items" => $tpl_kind_items,
            "qna_type_items" => $qna_type_items,
            "admin_id" => $admin_id,
            "admin_nm" => $admin_nm
        ];
        return view( Config::get('shop.head.view') . '/standard/std20',$values);
    }

    public function search(Request $request){
        $tpl_kind	= Request("tpl_kind");
		$use_yn	    = Request("use_yn");
		$qna_type	= Request("qna_type");
		$subject	= Request("subject");

		$where = "";
		$outerwhere = "";
		
		if ( $tpl_kind != "" )  $where      .= " and q.kind = '$tpl_kind' ";
		if ( $use_yn != "" ) $where         .= " and q.use_yn = '$use_yn' ";
		if ( $qna_type != "" ) $where       .= " and q.tplkind = '$qna_type' ";
		if ( $subject != "" ) $outerwhere   .= " where subject like '%$subject%' ";

		$query = "
			select * from (
				select cd.code_val as kind, ca.code_val as tplkind
					, if(ifnull(q.subject, '') = '', b.type_nm, subject) as subject
					, q.use_yn ,q.qna_no
				from qna_ans_type q
					left outer join qna_type b on q.qna_no = b.qna_no
					left outer join code ca on ca.code_kind_cd = 'G_QNA_TYPE' and ca.code_id = q.tplkind
					left outer join code cd on cd.code_kind_cd = 'G_TPL_KIND' and cd.code_id = q.kind
				where 1=1
					$where
			) a $outerwhere
			order by qna_no desc
        ";
        
        $result = DB::select($query);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1
            ),
            "body" => $result
        ]);
    }

    public function GetInfomation(Request $request){
        $qna_no = Request("qna_no");

		$query  = "
			select kind, tplkind
				, if(ifnull(q.subject, '') = '', b.type_nm, subject) as subject
				, q.ans_msg as content, q.use_yn, q.qna_no
				, q.admin_id, q.admin_nm, q.rt, q.ut
			from qna_ans_type q
				left outer join qna_type b on q.qna_no = b.qna_no
			where q.qna_no = '$qna_no'
        ";
        $result = DB::select($query);
        return response()->json([
            "code" => 200,
            "body" => $result
        ]);
    }

    public function Command(Request $request){
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $cmd = $request->input("cmd");
        $kind		= $request->input("tpl_kind_view");
		$qna_type	= $request->input("qna_type");
		$subject	= $request->input("subject");
		$content	= $request->input("content");
        $use_yn		= $request->input("use_yn");
        $qna_no     = $request->input("qna_no");

        $qna_result = 500;
        
        $cmd =  $cmd;
        if($cmd == "addcmd"){
            $insert_tamp = "
				insert into qna_ans_type(
					kind, tplkind, subject, ans_msg, admin_id, admin_nm, use_yn, rt, ut
				)values(
					'$kind','$qna_type','$subject','$content','$id', '$name', '$use_yn', now(), now()
				);
            ";
            
            try {
                DB::insert($insert_tamp);
                $qna_result = 200;
            } catch(Exception $e){
                $qna_result = 500;
            }
        }else if($cmd == "editcmd"){

            $update_items = [
                "kind" => $kind,
                "tplkind" => $qna_type,
                "subject" => $subject,
                "ans_msg" => $content,
                "admin_id" => $id,
                "admin_nm" => $name,
                "use_yn" => $use_yn,
                "ut" => now()
            ];
            try {
                DB::table('qna_ans_type')
                ->where('qna_no','=',$qna_no)
                ->update($update_items);
                //$code = 200;
                $qna_result = 200;
            } catch(Exception $e){
                //$code = 500;
                $qna_result = 500;
            }

        }else if($cmd == "delcmd"){
            try {
                DB::table('qna_ans_type')
                   ->where('qna_no','=',$qna_no)
                   ->delete();
               $qna_result = 200;
           } catch(Exception $e){
               $qna_result = 500;
           }

        }
        return response()->json([
            "qna_result" => $qna_result,
        ]);

    }
}
