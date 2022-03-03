<?php

namespace App\Http\Controllers\head\partner;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class pat02Controller extends Controller
{
    //
    public function index()
    {
        $mutable = Carbon::now();
        $sdate = $mutable->sub(6, 'month')->format('Y-m-d');
        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'qna_types' => SLib::getCodes('G_COM_QNA_TYPE'),
            'qna_states' => SLib::getCodes('G_QNA_STATE')
        ];
        return view(Config::get('shop.head.view') . '/partner/pat02', $values);
    }

    public function search(Request $request)
    {
        $com_id = Auth('head')->user()->com_id;

        $sdate = $request->input('sdate', Carbon::now()->sub(6, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $subject = $request->input('subject');
        $com_nm = $request->input('com_nm');
        $qna_type = $request->input('qna_type');
        $state = $request->input('state');

        $where = "";
        if ($subject != "") $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
        if ($com_nm != "") $where .= " and a.com_nm like '%" . Lib::quote($com_nm) . "%' ";
        if ($qna_type != "") $where .= " and a.type = '$qna_type'";
        if ($state != "") $where .= " and a.state = '". Lib::quote($state). "' ";

        //$charset = Auth('head')->charset;

        $query = /** @lang text */
            "
            select 
                b.code_val as type, a.com_nm, a.com_id, com_id,a.subject, question_date,answer_date, c.code_val as state, a.no
            from company_qa a
                inner join code b on b.code_kind_cd = 'G_COM_QNA_TYPE' and a.type = b.code_id
                inner join code c on c.code_kind_cd = 'G_QNA_STATE' and a.state = c.code_id
            where a.question_date >= :sdate and a.question_date < date_add(:edate,interval 1 day)             
                $where
            order by no desc
            limit 0, 300
        ";
        /*
        echo $query;
        echo "<br>";
        */
        $result = DB::select($query,["sdate" => $sdate,"edate" => $edate]);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }


    public function show($idx)
    {
        $list = DB::table('company_qa')
            ->join('code',function($join) {
                $join->on('company_qa.type','=','code.code_id')->where("code_kind_cd","G_COM_QNA_TYPE");})
            ->select('company_qa.*','code.code_val as type_nm')
            ->where('company_qa.no', "=", $idx)->get();
        //dd($list);

        foreach ($list as $row) {
            $row->question = $this->html2str($row->question);
            $row->answer = $this->html2str($row->answer);
        }
        return view(Config::get('shop.head.view') . '/partner/pat02_show', [
                'idx' => $idx,
                'qna_states' => SLib::getCodes('G_QNA_STATE'),
                'list' => $list[0]
            ]);
    }

    private function html2str($html)
    { // html 태그 제거
        $str = strip_tags(str_replace('&nbsp;', " ", preg_replace('/\<br(\s*)?\/?\>/i', "\n", $html)));
        return $str;
    }

    public function update($idx, Request $request)
    {
        $answer = $request->input('answer');
        $state = $request->input('state');

        $qna = [
            'answer' => $answer,
            'state' => $state,
            'answer_date' => DB::raw('now()')
        ];

        try {
            DB::transaction(function () use (&$result, $idx, $qna) {
                DB::table('company_qa')
                    ->where('no', '=', $idx)
                    ->update($qna);
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

}
