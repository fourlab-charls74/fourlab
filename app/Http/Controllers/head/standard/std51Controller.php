<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

// 코드관리
class std51Controller extends Controller
{
    public function index()
    {
        $values = [];
        return view(Config::get("shop.head.view") . "/standard/std51", $values);
    }

	// 목록 조회
    public function search(Request $request)
	{
		// about page
		$page = $request->input("page", 1);
		if($page < 1 or $page == '') $page = 1;
		$page_size = $request->input("limit", 100);
		$total = 0;
		$page_cnt = 0;
		$start_no = 0;

		// about order
		$ord_field = $request->input("ord_field", "code_id");
		$ord = $request->input("ord", "asc");
		$order_by = sprintf(" order by %s %s ", $ord_field, $ord);
		
		// about field
		$code_kind_cd = $request->input("code_kind_cd");
		$code_id = $request->input("code_id");
		$code_val = $request->input("code_val");
		$use_yn = $request->input('use_yn');
		
		// about where if
		$where = '';
		if($code_kind_cd != '') $where .= " and code_kind_cd='$code_kind_cd' ";
		if($code_id != '') $where .= " and code_id='$code_id' ";
		if($code_val != '') $where .= " and (code_val like '%$code_val%' or code_val2 like '%$code_val%' or code_val3 like '%$code_val%' or code_val_eng like '%$code_val%') ";
		if($use_yn != '') $where .= " and use_yn='$use_yn' ";
		
		if($page == 1) {
			$sql = "
				select count(*) as cnt
				from code
				where 1=1 $where
			";
			$row = DB::selectOne($sql);
			$total = $row->cnt;
		}
		$start_no = ($page - 1) * $page_size;

		$query = "
			select 
				no, code_kind_cd, code_id, code_val, code_val2, code_val3, code_val_eng, use_yn, admin_id, admin_nm, rt, ut 
			from code 
			where 1=1 $where
			$order_by
			limit :start_no, :page_size
		";

		$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page, "page_total"=>1);
		$result = DB::select($query, array("start_no" => $start_no, "page_size" => $page_size));
		
		return response()->json([
			"code" => 200,
			"head" => $arr_header,
			"body" => $result
		]);
	}

	// 상세보기
	public function show($code_no, Request $request) {
		$query = "
			select
				no, code_kind_cd, code_id, code_val, code_val2, code_val3, code_val_eng, use_yn, admin_id, admin_nm, rt, ut
			from code
			where no=:code_no;
		";

		$selected_code = DB::selectOne($query, array("code_no" => $code_no));
		$values = [
			'type' => 'edit',
            'code' => $selected_code,
        ];
        return view(Config::get('shop.head.view') . '/standard/std51_show', $values);
	}

	// 등록창 띄우기
	public function create()
	{
		$values = [
			'type' => 'add',
		];
		return view(Config::get('shop.head.view') . "/standard/std51_show", $values);
	}

	// 등록
	public function insert(Request $req)
	{
		$admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
		
		$code_kind_cd = Request("code_kind_cd");
		$code_id = Request("code_id");
		$code_val = Request("code_val");
		$code_val2 = Request("code_val2");
		$code_val3 = Request("code_val3");
		$code_val_eng = Request("code_val_eng");
		$use_yn = Request("use_yn");

		$result_code = 200;
		$msg = '';

		$sql_confirm = "
			select count(*) as cnt 
			from code 
			where code_id=:code_id and code_kind_cd=:code_kind_cd
		";
		
        $sql= "
            insert into code (
                code_kind_cd, code_id, code_val, code_val2, code_val3, code_val_eng, use_yn, admin_id, admin_nm, rt
            ) values (
                :code_kind_cd, :code_id, :code_val, :code_val2, :code_val3, :code_val_eng, :use_yn, :admin_id, :admin_nm, now()
            )
        ";

		try{
            DB::beginTransaction();

			$row = DB::selectOne($sql_confirm, array("code_id" => $code_id, "code_kind_cd" => $code_kind_cd));
			$is_unique = $row->cnt;

			if($is_unique == 0) {
				DB::insert($sql, array(
					"code_kind_cd" => $code_kind_cd, 
					"code_id" => $code_id, 
					"code_val" => $code_val, 
					"code_val2" => $code_val2, 
					"code_val3" => $code_val3, 
					"code_val_eng" => $code_val_eng, 
					"use_yn" => $use_yn, 
					"admin_id" => $admin_id, 
					"admin_nm" => $admin_nm
				));
				DB::commit();
				$msg = '등록되었습니다.';
			} else {
				$result_code = 400;
				$msg = '중복된 코드ID가 존재합니다.';
			}
        }catch(Exception $e) {
			DB::rollback();
			
			$result_code = 500;
			$msg = $e->getMessage();
        }

		return response()->json(["code" => $result_code, "message" => $msg], $result_code);
	}

	// 수정
	public function update($code_no, Request $req)
	{
		$admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
		
		$code_kind_cd = Request("code_kind_cd");
		$code_id = Request("code_id");
		$code_val = Request("code_val");
		$code_val2 = Request("code_val2");
		$code_val3 = Request("code_val3");
		$code_val_eng = Request("code_val_eng");
		$use_yn = Request("use_yn");

		$result_code = 200;
		$msg = '';
		
        $sql= "
            update code set
				code_val=:code_val,
				code_val2=:code_val2,
				code_val3=:code_val3,
				code_val_eng=:code_val_eng,
				use_yn=:use_yn,
				ut= now()
			where no=$code_no
        ";

		try{
            DB::beginTransaction();

			DB::update($sql, array(
				"code_val" => $code_val, 
				"code_val2" => $code_val2, 
				"code_val3" => $code_val3, 
				"code_val_eng" => $code_val_eng, 
				"use_yn" => $use_yn, 
			));

			DB::commit();
			$msg = '수정되었습니다.';
        }catch(Exception $e) {
			DB::rollback();
			
			$result_code = 500;
			$msg = $e->getMessage();
        }

		return response()->json(["code" => $result_code, "message" => $msg], $result_code);
	}

	// 삭제
	public function delete($code_no)
	{
		$result_code = 200;
		$msg = '';

		$sql= "
			delete 
			from code
			where no=:code_no
		";

		try{
            DB::beginTransaction();

            DB::delete($sql, array("code_no" => $code_no));

            DB::commit();
			$msg = "삭제되었습니다.";
        }catch(Exception $e) {
            DB::rollback();

			$result_code = 500;
			$msg = $e->getMessage();
        }

		return response()->json(["code" => $result_code, "message" => $msg], $result_code);
	}
}