<?php

namespace App\Http\Controllers\partner\cs;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cs02Controller extends Controller
{
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(12, 'month')->format('Y-m-d');


        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
        ];
        return view( Config::get('shop.partner.view') . '/cs/cs02',$values);
    }

    public function search(Request $request){
      $com_id = Auth('partner')->user()->com_id;

        $sdate = $request->input('sdate',Carbon::now()->sub(1, 'week')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $style_no = $request->style_no;
        $show_yn = $request->show_yn;

        $goods_no = $request->goods_no;
        $goods_sub = $request->goods_sub;
        $goods_nm = $request->goods_nm;
        $column = $request->column;
        $keyword = $request->keyword;

        $answer_yn = $request->answer_yn;
        $item = $request->item; //opt_kind_cd

        $where = "";
        if($style_no != "")   $where .= " and g.style_no like '" . Lib::quote($style_no)."%'";
        if ($show_yn != "")   $where .= " and a.show_yn     = '" . Lib::quote($show_yn)."' ";
        if ($goods_no != "")  $where .= " and a.goods_no    = '" . Lib::quote($goods_no)."' ";
        if ($goods_sub != "") $where .= " and a.goods_sub   = '" . Lib::quote($goods_sub)."' ";
        if ($goods_nm != "")  $where .= " and g.goods_nm like '%". Lib::quote($goods_nm)."%' ";
        if ($keyword != "")   $where .= " and $column like    '%". Lib::quote($keyword)."%' ";
        if ($answer_yn != "") $where .= " and a.answer_yn   = '" . Lib::quote($answer_yn)."' ";
        if ($item != "")	    $where .= " and opt_kind_cd   = '" . Lib::quote($item) . "' ";

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = /** @lang text */
            " 
          select a.no, a.user_id, a.user_nm, a.subject, a.q_date, a.cnt, a.is_secret, a.answer_yn, g.goods_nm, g.goods_no, a_date, a.show_yn
               , a.admin_id, a.admin_nm
               , replace(img,'$cfg_img_size_real','$cfg_img_size_list') as img
            from goods_qa_new a
            left join goods g on g.goods_no = a.goods_no
           where g.com_id = :com_id and a.q_date >= :sdate and a.q_date < DATE_ADD(:edate, INTERVAL 1 DAY) 
           $where
           order by a.no desc
        ";
        $result = DB::select($query,[
            "com_id" => $com_id,
            "sdate" => $sdate,
            "edate" => $edate
        ]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show($idx){
        $query = " 
          select a.no, a.user_id, a.user_nm, a.subject, a.q_date, a.cnt, a.is_secret, a.answer_yn, g.goods_nm, g.goods_no, a_date, a.show_yn, ip
               , a.question, a.answer
               , a.admin_id, a.admin_nm
               , img
               , a.check_id, a.check_nm
            from goods_qa_new a
            left join goods g on g.goods_no = a.goods_no
           where 1=1
             and a.no = $idx
           order by q_date desc
        ";

        $list = DB::selectOne($query);
        
        $user = Auth('partner')->user();

        $list->question = $this->html2str($list->question);
        $list->answer = $this->html2str($list->answer);
        return view( Config::get('shop.partner.view') . '/cs/cs02_show',
            ['idx' => $idx, 'list' => $list, 'user_nm' => $user->com_nm, 'user_id' => $user->com_id]
        );
    }

    public function template(Request $request) {
        $keyword = $request->input('keyword', "");

        dd($keyword);
        
        $sql = 
            "SELECT `qna_no`, `subject`, `ans_msg` 
                FROM `qna_ans_type`
                WHERE `subject`LIKE '%${keyword}%' AND `use_yn` = 'Y'
            ";
            
        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [
                'total' => count($rows)
            ],
            "body" => $rows
        ]);
    }

    public function template_msg($no) {
        $result = DB::table('qna_ans_type')
                    ->select('qna_no', 'ans_msg')
                    ->where('qna_no', $no)
                    ->get();

        $result[0]->ans_msg = $this->html2str($result[0]->ans_msg);
        return $result;
    }

    private function html2str($html){ // html 태그 제거
        $str = strip_tags(str_replace('&nbsp;', " ", preg_replace('/\<br(\s*)?\/?\>/i', "\n", $html)));

        return $str;
    }

    public function check(Request $request) {

        $user = Auth('partner')->user();

        $id = $user->com_id;
        $name = $user->com_nm;

        $no = $request->input("no", "");
        $cmd = $request->input("cmd", "");

        if ($cmd && $no) {
            
            // 접수자 얻기
            $sql = "SELECT ifnull(check_id,'') as check_id, ifnull(check_nm,'') as check_nm
                FROM goods_qa_new
                WHERE `no` = '$no'
            ";
            $row = DB::selectOne($sql);

            $check_id = $row->check_id;
            $check_nm = $row->check_nm;

            // 프론트 답변자 버튼 체크시 result = 1 이면 성공, 0이면 에러
            $array = DB::transaction(function () use ($no, $cmd, $id, $name, $check_id, $check_nm) {

                try {

                    $check_msg = "command not found";

                    if ($cmd == "checkin") { // 접수
                        
                        if ($check_id != "") {
                            if ($id != $check_id) {
                                $check_msg = $this->_f("%1(%2) 님께서 접수하셨습니다.", $check_id, $check_nm);
                            } else {
                                $check_msg = "이미 접수하셨습니다";
                            }
                            return ["result" => 0, "check_msg" => $check_msg];
                        } else {
                            $sql = "
                                update goods_qa_new set
                                    check_id = :check_id
                                    ,check_nm = :check_nm
                                where no = :no
                            ";
                            DB::update($sql, [ "check_id" => $id, "check_nm" => $name, "no" => $no ]);
                            $check_msg = "done";
                            return ["result" => 1, "check_msg" => $check_msg];
                        }
                    } else if ($cmd == "checkout") { // 접수 해제

                        if ($id == $check_id) {

                            $data = [
                                'check_id' => DB::raw("null"),
                                'check_nm' => DB::raw("null")
                            ];

                            DB::table("goods_qa_new")
                                ->where("no", $no)
                                ->update($data);

                            $check_msg = "done";

                            return ["result" => 1, "check_msg" => $check_msg];

                        } else { // 접수자가 아닌 경우

                            $check_msg = $this->_f("%1 (%2) 님이 접수한 건입니다. 접수 해제 하실 수 없습니다.", $check_nm, $check_id);

                            return ["result" => 0, "check_msg" => $check_msg];

                        }

                    }
                    
                    return ["result" => 0, "check_msg" => $check_msg];
                     
                } catch (\Exception $e) { // update시 에러
                    // $check_msg = $e;
                    $check_msg = "update error";
                    return ["result" => 0, "check_msg" => $check_msg];
                }
            });

            return response()->json($array, 200);

        }

    }

    /*
        Function: _t
            language 변수에 설정된 문자열 출력

        Parameters:
            t - 문자열

        Returns:
            String
    */
    public function _t($t) {
        global $__text;
        if (isset($__text[$t]))
            return $__text[$t];
        return $t;
    }

    /*
        Function: _f
            language 변수에 설정된 문자열 출력

        Parameters:
            t - 문자열

        Returns:
            String
    */
    public function _f($t) {
        $t = $this->_t($t);
        if (func_num_args() <= 1)
            return $t;
        for ($i = 1; $i < func_num_args(); $i++) {
            $arg = func_get_arg($i);
            $t = str_replace('%' . $i, $arg, $t);
        }
        return $t;
    }

    public function update($idx, Request $request) {
        
        $user = Auth('partner')->user();
        $cmd = $request->input("cmd", "editcmd");

        if ($cmd == "change") {

            $show_yn = $request->input("show_yn");
            
			if ($show_yn == "Y") $show_change = "N";
			else if($show_yn == "N") $show_change = "Y";

            $data = [
                'show_yn' => $show_change
            ];

            return DB::transaction(function () use ($idx, $data) {
                try {
                    DB::table('goods_qa_new')->where('no', $idx)->update($data);
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            });

        } else if ($cmd == "editcmd") {

            $data = [
                'answer' => $request->answer,
                'answer_yn' => 'Y',
                'admin_id' => $user->com_id,
                'admin_nm' => $user->com_nm,
                'check_id' => $user->com_id,
                'check_nm' => $user->com_nm,
                'a_date' => now()
            ];
    
            try {
                DB::transaction(function () use (&$result, $idx, $data) {
                    DB::table('goods_qa_new')
                      ->where('no', $idx)
                      ->update($data);
                });
                $result = true;
            } catch(\Exception $e){
                $result = false;
            }
            
            return $result;

        }

    }

}
