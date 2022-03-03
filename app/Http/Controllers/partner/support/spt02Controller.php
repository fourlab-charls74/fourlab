<?php

namespace App\Http\Controllers\partner\support;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class spt02Controller extends Controller
{
    public function index() {
        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'year')->format('Y-m-d');
        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'qna_types' => SLib::getCodes('G_COM_QNA_TYPE'),
            'qna_states' => SLib::getCodes('G_QNA_STATE')
        ];
        return view( Config::get('shop.partner.view') . '/support/spt02',$values);
    }

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;
        
        $sdate = $request->input('sdate', Carbon::now()->sub(1, 'year')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $sdate = $sdate . " 00:00:00";
        $edate = $edate . " 23:59:59";
        
        $subject = $request->input('subject');
        $com_nm = $request->input('com_nm');
        $qna_type = $request->input('qna_type');
        $state = $request->input('state');

        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $where = "";
        if ($subject != "") $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
        if ($com_nm != "") $where .= " and a.com_nm like '%" . Lib::quote($com_nm) . "%' ";
        if ($qna_type != "") $where .= " and a.type = '$qna_type'";
        if ($state != "") $where .= " and a.state = '". Lib::quote($state). "' ";

        $query =
            "SELECT 
                b.code_val AS type, a.com_nm, a.com_id, com_id,a.subject, question_date, answer_date, c.code_val AS state, a.no
            FROM company_qa a
                INNER JOIN code b ON b.code_kind_cd = 'G_COM_QNA_TYPE' AND a.type = b.code_id
                INNER JOIN code c ON c.code_kind_cd = 'G_QNA_STATE' AND a.state = c.code_id
            WHERE a.com_id = :com_id AND a.question_date >= :sdate AND a.question_date <= :edate
                $where
            ORDER BY `no` DESC
        ";

        $result = DB::select($query, ["com_id" => $com_id, "sdate" => $sdate, "edate" => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
            ),
            "body" => $result
        ]);

    }

    public function create() {
        $list = new \stdClass();
        $list->subject = '';

        return view( Config::get('shop.partner.view') . '/support/spt02_create', [
            'idx' => '',
            'list' => $list,
            'qna_types' => SLib::getCodes('G_COM_QNA_TYPE')
        ]);
    }

    public function store(Request $request) {
        $user = Auth('partner')->user();
        return DB::transaction(function () use ($request, $user) {
            try {
                DB::table('company_qa')->insert([
                    "type" => $request->input('type'),
                    'com_id' => $user->com_id,
                    'com_nm' => $user->com_nm,
                    'subject' => $request->input('subject'),
                    'question' => $request->input('question'),
                    'admin_id' => $user->com_id,
                    'admin_nm' => $user->com_nm,
                    'state' => 0,
                    'question_date' => DB::raw('now()'),
                    'answer_date' => null,
                    'memo_cnt' => 0,
                    'memo_regi_date' => DB::raw('now()')
                ]);
                return response()->json(["result" => 1], 200);
            } catch (\Exception $e) {
                return response()->json(["result" => 0, "msg" => $e->getMessage()]);
            }
        });
    }

    public function show($idx) {
        $list = DB::table('company_qa')
            ->join('code',function($join) {
                $join->on('company_qa.type','=','code.code_id')->where("code.code_kind_cd","G_COM_QNA_TYPE");})
            ->join('code as cd2',function($join) {
                $join->on('company_qa.state','=','cd2.code_id')->where("cd2.code_kind_cd","G_QNA_STATE");})
            ->select('company_qa.*','code.code_val as type_nm','cd2.code_val as state_nm')
            ->where('company_qa.no', "=", $idx)->get();

        foreach ($list as $row) {
            $row->question = $this->html2str($row->question);
            $row->answer = $this->html2str($row->answer);
        }

        $memos = DB::table('company_qa_memo')->where("p_no", "=", $idx)->get();
        return view(Config::get('shop.partner.view') . '/support/spt02_show', [
            'idx' => $idx,
            'qna_states' => SLib::getCodes('G_QNA_STATE'),
            'list' => $list[0],
            'memos' => $memos
        ]);
    }

    public function removeQna(Request $request) {
        $user = Auth('partner')->user();
        return (
            DB::transaction(function () use ($request, $user) {
            $no = $request->input("no");
            $admin_id = $user->com_id;
            try {
                $sql = "
				    select state from company_qa where no = '$no' and admin_id = '$admin_id'
			    ";
                $row = DB::selectOne($sql);
                if ($row) {
                    $state = $row->state;
                    if ($state == 0) {
                        $sql = "
						    delete from company_qa_memo where p_no = :no and admin_id = :admin_id
					    ";
                        DB::delete($sql, ["no" => $no, "admin_id" => $admin_id]);

                        $sql = "
						    delete from company_qa where no = :no and admin_id = :admin_id
					    ";
                        DB::delete($sql, ["no" => $no, "admin_id" => $admin_id]);
                    }
                } else {
                    throw new Exception("row not found", -2);
                }
                return response()->json(["result" => 1], 200);
            } catch (\Exception $e) {
                $code = $e->getCode();
                if ($code == '-2') {
                    return response()->json(["result" => -2, "msg" => $e->getMessage()], 200);
                } else {
                    return response()->json(["result" => -1, "msg" => $e->getMessage()], 200);
                }
            }})
        );
    }

    public function saveReply(Request $request) {
        $user = Auth('partner')->user();
        return DB::transaction(function () use ($request, $user) {
            $reply = $request->input("reply");
            $no = $request->input("no");
            $admin_id = $user->com_id;
            $admin_nm = $user->com_nm;
            try {
                DB::table("company_qa_memo")->insert([
                    "p_no" => $no,
                    "memo" => $reply,
                    "admin_id" => $admin_id,
                    "admin_nm" => $admin_nm,
                    "regi_date" => DB::raw('now()')
                ]);

                DB::table("company_qa")->where('no', $no)->update([
                    "memo_cnt" => DB::raw('memo_cnt+1'),
                    "memo_regi_date" => DB::raw('now()')
                ]);
                return response()->json(["result" => 1], 200);
            } catch (\Exception $e) {
                return response()->json(["result" => 0, "msg" => $e->getMessage()]);
            }
        });
    }

    private function html2str($html){ // html 태그 제거
        $str = strip_tags(str_replace('&nbsp;', " ", preg_replace('/\<br(\s*)?\/?\>/i', "\n", $html)));

        return $str;
    }


}
