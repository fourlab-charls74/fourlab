<?php

namespace App\Http\Controllers\shop\standard;

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
		return view(Config::get('shop.shop.view') . '/standard/std08', $values);
	}

	public function search(Request $request)
	{
		$grade_cd = $request->input("grade_cd", "");
		$grade_nm = $request->input("name", "");

		$where = "where 1=1";
		if ($grade_cd != "") $where .= " and sg.grade_cd like '%" . Lib::quote($grade_cd) . "%'";
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
		$f_data = collect($data)->map(function ($item, $i) {
			$item['seq'] = $i;
			return $item;
		})->groupBy('grade_cd')->map(function ($item) {
			return $item->sortByDesc('seq')->values();
		});
		try {
			DB::transaction(function () use (&$f_data) {
				foreach ($f_data as $group_key => $group) {
					$grade_cd = $group_key;
					$duplicated = count($group) > 1 ? true : false;

					if ($duplicated) { // 등급 코드(grade_cd)가 중복된 경우 - 복수 행

						foreach ($group as $row) {
							/**
							 * idx가 있으면 업데이트, 없으면 insert 처리
							 */
							$idx = Lib::quote($row['idx']);
							$where = ($idx != "") ? "sg.idx = $idx" : "1<>1";
							$sql = "select count(*) as cnt from store_grade sg where $where";
							$result = DB::selectOne($sql);

							// save시 ag-grid에서 전달받은 데이터의 불필요한 컬럼 제거
							unset($row['idx']);
							unset($row['added']);
							unset($row['editable']);

							if ($result->cnt > 0) {
								$row['edate'] = "9999-99";
								DB::table('store_grade')->where('idx', "=", $idx)->update($row);
							} else {
								$row['edate'] = "9999-99";
								DB::table('store_grade')->insert($row);
							}
						}

						/**
						 * 순서가 제일 아래인 행을 기준으로 종료일 업데이트 되도록 설정
						 */
						$highest_seq_item = $group[0];
						$highest_seq_sdate = $highest_seq_item['sdate'];
						$highest_seq_edate = Carbon::parse($highest_seq_sdate)->subMonth()->format("Y-m");
						$highest_seq = $highest_seq_item['seq'];

						// 등급 코드가 중복된 모든 행의 종료일을 가장 아래 행의 시작월의 전월로 업데이트
						DB::table('store_grade')->where([['grade_cd', "=", $grade_cd], ['seq', "<>", $highest_seq]])->update(['edate' => $highest_seq_edate]);

					} else { // 등급 코드(grade_cd)가 중복되지 않은 경우 - 단일 행

						/**
						 * idx가 동일한 등급이 있으면 update, 없는 경우 insert 처리
						 */
						$row = $group[0];
						$idx = Lib::quote($row['idx']);
						$where = ($idx != "") ? "sg.idx = $idx" : "1<>1";
						$sql = "select count(*) as cnt from store_grade sg where $where";
						$result = DB::selectOne($sql);

						// save시 ag-grid에서 전달받은 데이터의 불필요한 컬럼 제거
						unset($row['idx']); 
						unset($row['added']);
						unset($row['editable']);

						if ($result->cnt > 0) {
							$result = DB::selectOne($sql);
							DB::table('store_grade')->where('idx', "=", $idx)->update($row);
						} else {
							$row['edate'] = "9999-99";
							DB::table('store_grade')->insert($row);
						}
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

	public function choice_index(Request $request)
	{
		$grade_nm = $request->input('grade_nm', '');
		$values = [
			"grade_nm" => $grade_nm,
		];
		return view(Config::get('shop.shop.view') . '/standard/std08_choice', $values);
	}
}

