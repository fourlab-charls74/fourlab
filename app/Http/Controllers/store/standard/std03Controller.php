<?php

namespace App\Http\Controllers\store\standard;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class std03Controller extends Controller
{
	public function index()
	{
		$values = [];
		return view(Config::get('shop.store.view') . '/standard/std03', $values);
	}

	public function search(Request $request)
	{
		$storage_cd = $request->input("storage_cd");
		$storage_nm = $request->input("storage_nm");
		$use_yn = $request->input("use_yn");
		$stock_check_yn = $request->input("stock_check_yn");

		$code = 200;
		$where = "";

		if($storage_cd != null) 
			$where .= " and storage_cd like '%$storage_cd%'";
		if($storage_nm != null) 
			$where .= " and (storage_nm like '%$storage_nm%' or storage_nm_s like '%$storage_nm%')";
		if($use_yn != null) 
			$where .= " and use_yn = '$use_yn'";
		if($stock_check_yn != null) 
			$where .= " and stock_check_yn = '$stock_check_yn'";

		$sql = "
			select storage_cd, storage_nm, phone, use_yn, stock_check_yn, default_yn, online_yn, comment
			from storage
			where 1=1 $where
			order by storage_cd
		";

		$rows = DB::select($sql);

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			],
			"body" => $rows
		]);
	}

	public function show($storage_cd = '') 
	{
		$storage = "";

		if($storage_cd != '') {
			$sql = "
				select storage_cd, storage_nm, storage_nm_s, zipcode, addr1, addr2, phone, fax, ceo, use_yn, loss_yn, stock_check_yn, default_yn, online_yn, comment, reg_date, mod_date, admin_id
				from storage
				where storage_cd = :storage_cd
			";

			$storage = DB::selectOne($sql, ["storage_cd" => $storage_cd]);
		}

		$is_exit_default_storage = DB::table('storage')->where('default_yn', '=', 'Y')->count();
		$is_exit_online_storage = DB::table('storage')->where('online_yn', '=', 'Y')->count();

		$values = [
			"cmd" => $storage_cd == '' ? "add" : "update",
			"storage" => $storage,
			"is_exit_default_storage" => $is_exit_default_storage > 0 ? 'true' : 'false',
			"is_exit_online_storage" => $is_exit_online_storage > 0 ? 'true' : 'false',
		];

		return view(Config::get('shop.store.view') . '/standard/std03_show', $values);
	}

	// 창고코드 중복체크
	public function dupcheck_storage($storage_cd = '') 
	{
		$code = 200;
		$msg = "사용가능한 코드입니다.";

		$sql = "
			select count(storage_cd) as cnt
			from storage
			where storage_cd = :storage_cd
		";

		$cnt = DB::selectOne($sql, ["storage_cd" => $storage_cd])->cnt;

		if($cnt > 0) {
			$code = 409;
			$msg = "이미 사용중인 코드입니다.";
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	// 창고등록
	public function add_storage(Request $request) 
	{
		$code = 200;
		$msg = "창고정보가 정상적으로 등록되었습니다.";

		$admin_id = Auth('head')->user()->id;
		$storage_cd = $request->input("storage_cd", "");
		$storage_nm = $request->input("storage_nm", "");
		// $storage_nm_s = $request->input("storage_nm_s", "");
		$zipcode = $request->input("zipcode", "");
		$addr1 = $request->input("addr1", "");
		$addr2 = $request->input("addr2", "");
		$phone = $request->input("phone", "");
		$fax = $request->input("fax", "");
		$ceo = $request->input("ceo", "");
		$use_yn = $request->input("use_yn", "Y");
		$loss_yn = $request->input("loss_yn", "Y");
		$stock_check_yn = $request->input("stock_check_yn", "Y");
		$default_yn = $request->input("default_yn", "N");
		$online_yn = $request->input("online_yn", "N");
		$comment = $request->input("comment", "");


		try {
            DB::beginTransaction();

			if($default_yn == 'Y') {
				DB::table('storage')->update(['default_yn' => 'N']);
			}
			if($online_yn == 'Y') {
				DB::table('storage')->update(['online_yn' => 'N']);
			}
			
			DB::table('storage')->insert([
				'storage_cd' => $storage_cd,
				'storage_nm' => $storage_nm,
				// 'storage_nm_s' => $storage_nm_s,
				'zipcode' => $zipcode,
				'addr1' => $addr1,
				'addr2' => $addr2,
				'phone' => $phone,
				'fax' => $fax,
				'ceo' => $ceo,
				'use_yn' => $use_yn,
				'loss_yn' => $loss_yn,
				'stock_check_yn' => $stock_check_yn,
				'default_yn' => $default_yn,
				'online_yn' => $online_yn,
				'comment' => $comment,
				'reg_date' => now(),
				'admin_id' => $admin_id,
			]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, "data" => ["storage_cd" => $storage_cd]]);
	}

	// 창고수정
	public function update_storage(Request $request) 
	{
		$code = 200;
		$msg = "창고정보가 정상적으로 수정되었습니다.";

		$admin_id = Auth('head')->user()->id;
		$storage_cd = $request->input("storage_cd", "");
		$storage_nm = $request->input("storage_nm", "");
		// $storage_nm_s = $request->input("storage_nm_s", "");
		$zipcode = $request->input("zipcode", "");
		$addr1 = $request->input("addr1", "");
		$addr2 = $request->input("addr2", "");
		$phone = $request->input("phone", "");
		$fax = $request->input("fax", "");
		$ceo = $request->input("ceo", "");
		$use_yn = $request->input("use_yn", "Y");
		$loss_yn = $request->input("loss_yn", "Y");
		$stock_check_yn = $request->input("stock_check_yn", "Y");
		$default_yn = $request->input("default_yn", "N");
		$online_yn = $request->input("online_yn", "N");
		$comment = $request->input("comment", "");

		try {
			DB::beginTransaction();

			if($default_yn == 'Y') {
				DB::table('storage')->update(['default_yn' => 'N']);
			}
			if($online_yn == 'Y') {
				DB::table('storage')->update(['online_yn' => 'N']);
			}

			DB::table('storage')
				->where("storage_cd", "=", $storage_cd)
				->update([
					'storage_cd' => $storage_cd,
					'storage_nm' => $storage_nm,
					// 'storage_nm_s' => $storage_nm_s,
					'zipcode' => $zipcode,
					'addr1' => $addr1,
					'addr2' => $addr2,
					'phone' => $phone,
					'fax' => $fax,
					'ceo' => $ceo,
					'use_yn' => $use_yn,
					'loss_yn' => $loss_yn,
					'stock_check_yn' => $stock_check_yn,
					'default_yn' => $default_yn,
					'online_yn' => $online_yn,
					'comment' => $comment,
					'mod_date' => now(),
					'admin_id' => $admin_id,
			]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	// 창고삭제
	public function delete_storage(Request $request, $storage_cd)
	{
		$code = 200;
		$msg = "창고정보가 정상적으로 삭제되었습니다.";

		try {
			DB::beginTransaction();
			
			DB::table('storage')
				->where("storage_cd", "=", $storage_cd)
				->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}
}
