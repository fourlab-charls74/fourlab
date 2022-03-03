<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm32Controller extends Controller
{
    //
    public function index() {
        $values = [
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm32',$values);
    }

    public function show($idx="") {
        $values = [
            'idx' => "",
            'kwd' => "",
            'url' => "",
            'disp_yn' => "N",
            'use_yn' => "N"
        ];

        if ($idx != "") {

            $cmd = "editcmd";

            $sql = "
				select *
				from search_shortcut
				where
					idx = '$idx'
            ";
            $values = (array) DB::selectOne($sql);
        }
        // dd($values);
        return view( Config::get('shop.head.view') . '/promotion/prm32_show',$values);
    }

  public function search() {
      
        // 검색 파라미터
        $kwd	= Request("kwd");
        $use_yn	= Request("use_yn");

        // 조건절 설정
        $where = "";

        if($kwd != "" )     $where .= sprintf(" and kwd like '%s' ", "%$kwd%");
        if($use_yn != "" )  $where .= sprintf(" and use_yn = '%s'", $use_yn);

        $page = Request("page", 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = 100;

        if ($page == 1) {
            $sql = "
				select
					count(idx) as cnt
				from search_shortcut
				where 1 = 1 $where
			";
            $data_cnt = DB::selectOne($sql)->cnt;

            // 페이지 얻기
            $page_cnt=(int)(($data_cnt-1) / $page_size) + 1;
            if($page == 1){
                $startno = ($page-1) * $page_size;
            } else {
                $startno = ($page-1) * $page_size;
            }
            $arr_header = array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt
            );
        } else {
            $startno = ($page-1) * $page_size;
            $arr_header = [];
        }

        $sql = "
			select 
			    idx,kwd,url,disp_yn,pv,st,use_yn,rt,ut
			from search_shortcut a
			where
				1 = 1 $where
			order by a.idx desc
			limit $startno, $page_size
        ";

        $results = DB::select($sql);
        $arr_header['page_total'] = count($results);
        
        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $results,
            "sql" => $sql
        ]);
    }

    public function edit_search($idx='') {
        $kwd	    = Request("kwd");
        $url    	= Request("url");
        $disp_yn	= Request("disp_yn");
        $use_yn	    = Request("use_yn");

        try{
            // Start transaction
            DB::beginTransaction();

            $this->__set_disp_yn($disp_yn);

            DB::table('search_shortcut')
                ->where('idx',$idx)
                ->update([
                    'kwd' => $kwd,
                    'url' => $url,
                    'disp_yn' => $disp_yn,
                    'use_yn' => $use_yn,
                    'ut' => now()
                ]);

            // Finish transaction
            DB::commit();

            return response()->json(['idx' => $idx], 201);
        } catch(Exception $e) {
            DB::rollBack();

            return response()->json([
                "msg" => "작업도중 에러가 발생했습니다."
            ], 500);
        }
    }
    
    public function add_search() {
        $idx 		= Request("idx");
        $kwd	    = Request("kwd");
        $url    	= Request("url");
        $disp_yn	= Request("disp_yn");
        $use_yn	    = Request("use_yn");

        try{
            // Start transaction
            DB::beginTransaction();

            $this->__set_disp_yn($disp_yn);
            $idx = DB::table('search_shortcut')
                ->insertGetId([
                    'kwd' => $kwd,
                    'url' => $url,
                    'disp_yn' => $disp_yn,
                    'use_yn' => $use_yn,
                    'rt' => now(),
                    'ut' => now()
                ]);

            // Finish transaction
            DB::commit();

            return response()->json(['idx' => $idx], 201);
        } catch(Exception $e) {
            DB::rollBack();

            return response()->json([
                "msg" => "작업도중 에러가 발생했습니다."
            ], 500);
        }
    }

    private function __set_disp_yn($disp_yn) {
        if($disp_yn == "Y"){
            $sql = "
                update search_shortcut set disp_yn = 'N' where disp_yn = 'Y'
            ";
            DB::update($sql);
        }
    }
}
