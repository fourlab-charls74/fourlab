<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class std05Controller extends Controller
{
    public function index()
    {
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        $faq_type_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_FAQ_TYPE' order by no asc");

        $values = [
            "faq_types" => $faq_type_items
        ];
        return view(Config::get('shop.head.view') . '/standard/std05', $values);
    }

    public function search(Request $request)
    {
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $page = $request->input("page");
        $type        = Request("type");
        $show        = Request("show");
        $que        = Request("que");
        $ans        = Request("ans");
        $best_yn    = Request("best_yn");

        $where = "";

        if ($type != "") $where     .= " and a.type = '$type' ";
        if ($show != "") $where     .= " and a.show_yn = '$show' ";
        if ($que != "") $where      .= " and a.question like '%$que%' ";
        if ($ans != "") $where      .= " and a.answer like '%$ans%' ";
        if ($best_yn != "") $where  .= " and a.best_yn = '$best_yn' ";

        $page_size = 10;
        if ($page == 1) {
            $query = "
                select count(a.no) as total
                FROM faq a
                    inner join code b on b.code_kind_cd = 'G_FAQ_TYPE' and a.type = b.code_id
                where 1=1 $where
            ";
            //echo $query;
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $query = "
			SELECT
				case when 'kor' = 'kor' then b.code_val else b.code_val_eng end as type
				, a.question, a.admin_nm
				, date_format(a.regi_date, '%Y.%m.%d') as regi_date,show_yn, a.best_yn, a.no
			FROM faq a
				inner join code b on b.code_kind_cd = 'G_FAQ_TYPE' and a.type = b.code_id
			where 1=1 $where
			order by seq,no desc
        ";
        //echo $query;
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

    public function show($idx = '')
    {
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        if (empty($idx)) {
            $cmd = "add";
            $admin_nm = $name;
            $type = "";
            $question = "";
            $answer = "";
            $show_yn = "Y";
            $best_yn = "Y";
        } else {
            $cmd = "edit";

            $query = "
				select * from  faq where no = '$idx'
            ";
            $row = DB::select($query);
            if ($row[0]->no != "") {
                $type = $row[0]->type;
                $admin_nm = $row[0]->admin_nm;
                $question = $row[0]->question;
                $answer = $row[0]->answer;
                $show_yn = $row[0]->show_yn;
                $best_yn = $row[0]->best_yn;
            }
        }
        $faq_type_items = DB::select("select no, code_kind_cd as cd, code_id as id, code_val as val From code where code_kind_cd='G_FAQ_TYPE' order by no asc");

        $values = [
            'idx' => $idx,
            'cmd' => $cmd,
            'type' => $type,
            'admin_nm' => $admin_nm,
            'question' => $question,
            'answer' => $answer,
            'show_yn' => $show_yn,
            'best_yn' => $best_yn,
            "faq_types" => $faq_type_items
        ];

        return view(Config::get('shop.head.view') . '/standard/std05_show', $values);
    }

    public function store($idx = '', Request $req)
    {
        $id = Auth('head')->user()->id;
        // $name = Auth('head')->user()->name;
        $admin_nm = $req->admin_nm;
        $req_idx = $req->idx;
        $answer = $req->input('answer', '');
        //echo $req_idx;
        //$question = $req->input('question', '');
        //echo $question;

        if ($idx === '') {
            $cnt = DB::table('faq')->whereRaw("no = UPPER('$req->idx')")->get()->count();

            if ($cnt > 0) {
                return response()->json([
                    'msg' => '중복된 FAQ가 가 있습니다.'
                ], 500);
            }
        }

        $wheres = ['no' => DB::raw("UPPER('$req->idx')")];
        //updateOrInsert
        $values = [
            'type' => $req->input('type', ''),
            'question' => $req->input('question', ''),
            'answer' => $req->input('answer', ''),
            'admin_id' => $id,
            'admin_nm' => $admin_nm,
            'show_yn' => $req->input('show_yn', ''),
            'best_yn' => $req->input('best_yn', ''),
            'regi_date' => now()
        ];

        DB::table('faq')->updateOrInsert($wheres, $values);

        return response()->json(null, 204);
    }

    public function delete($idx = '', Request $req)
    {
        DB::table('faq')->where('no', $idx)->delete();

        return response()->json(null, 204);
    }
}
