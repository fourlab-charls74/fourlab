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

class std08Controller extends Controller
{
	public function index()
	{
        $sdate = Carbon::now()->startOfMonth()->format("Y-m-d");
        $edate = Carbon::now()->format("Y-m-d");
		$values = [
            "sdate" => $sdate,
            "edate" => $edate,
			"store_types" => SLib::getCodes("STORE_TYPE"),
		];
		return view(Config::get('shop.store.view') . '/standard/std08', $values);
	}

	public function search(Request $request)
	{
		$grade_nm = $request->input("name", ""); 
		$use_yn = $request->input("use_yn", "");
		$where = "where 1=1";
		if ($use_yn != "") $where .= " and sg.use_yn = '$use_yn'";
		if ($grade_nm != "") $where .= " and sg.name like '%" . Lib::quote($grade_nm) . "%'";
		$sql = "
			select *
			from store_grade sg
			$where
			order by seq asc
		";
		$rows = DB::select($sql);
		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($rows),
				"page" => 1
			],
			"body" => $rows
		]);
	}

	public function save(Request $request)
	{
		$data = $request->input('data');
		try {
			DB::transaction(function () use ($data) {
				foreach ($data as $row) {
					/**
					 * 데이터 가공, 초기 값 설정 및 불필요한 프로퍼티 제거
					 */
					if (array_key_exists('sdate', $row) === false) $row['sdate'] = now()->format("Y-m-d");
					$grade_cd = Lib::quote($row['grade_cd']);
					$added = Lib::quote(@$row['added']);
					$idx = Lib::quote($row['idx']);
					unset($row['idx']);
					unset($row['added']);
					unset($row['editable']);

					/**
					 * 새로 추가되는 등급인 경우 등급코드가 중복인 항목들의 사용여부를 N으로 변경
					 */
					if ($added) {
						DB::table('store_grade')->where([['grade_cd', "=", $grade_cd], ['idx', "<>", $idx]])->update(['use_yn' => "N", 'edate' => now()->format("Y-m-d")]);
					}

					/**
					 * 등급이 있는 경우 업데이트 / 없는 경우 추가
					 */
					$sql = "select count(*) as cnt from store_grade sg where sg.idx = '$idx'";
					$result = DB::selectOne($sql);
					if ($result->cnt > 0) {
						$result = DB::selectOne($sql);
						DB::table('store_grade')->where('idx', "=", $idx)->update($row);
					} else {
						$row['seq'] = count($data) - 1;
						DB::table('store_grade')->insert($row);
					}
				}
			});
			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			// dd($e);
			return response()->json(['code' => '500']);
		}
		return response()->json([]);
	}

	public function remove(Request $request)
	{
		$data = $request->input('data');
		try {
			DB::transaction(function () use ($data) {
				foreach ($data as $row) {
					$idx = $row['idx'];
					DB::table('store_grade')->where('idx', $idx)->delete();
				}
			});
			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			return response()->json(['code'	=> '500']);
		}
	}
}

