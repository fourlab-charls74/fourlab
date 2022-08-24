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
		$f_data = collect($data)->map(function ($item, $i) {
			$item['seq'] = $i;
			return $item;
		})->groupBy('grade_cd')->map(function ($item) {
			return $item->sortByDesc('seq')->values();
		});
		try {
			DB::transaction(function () use (&$data, &$f_data) {
				foreach ($f_data as $group_nm => $group) {
					$grade_cd = $group_nm;
					$duplicated = count($group) > 1 ? true : false;
					if ($duplicated) {
						/**
						 * grade_cd가 중복된 경우 - 순서가 제일 아래인 행을 기준으로 종료일 변경
						 */
						$highest_seq_item = $group[0];
						$idx = Lib::quote($highest_seq_item['idx']);
						$grade_cd = Lib::quote($highest_seq_item['grade_cd']);
						$sdate = $highest_seq_item['sdate'];
						$edate = Carbon::parse($sdate)->subMonth()->format("Y-m");
						DB::table('store_grade')->where([['grade_cd', "=", $grade_cd], ['idx', "<>", $idx]])->update(['edate' => $edate]);
						DB::table('store_grade')->where([['grade_cd', "=", $grade_cd], ['idx', "=", $idx]])->update(['sdate' => $sdate, 'edate' => "9999-99"]);
					} else {
						/**
						 * grade_cd가 중복되지 않은 경우 - 등급이 있으면 update, 없는 경우 insert 처리
						 */
						$row = $group[0];
						$idx = Lib::quote($row['idx']);
						$edate = Carbon::parse($row['sdate'])->subMonth()->format("Y-m");
						$sql = "select count(*) as cnt from store_grade sg where sg.idx = '$idx'";
						$result = DB::selectOne($sql);
						/**
						 * save시 불필요한 컬럼 제거
						 */
						unset($row['idx']);
						unset($row['added']);
						unset($row['editable']);
						if ($result->cnt > 0) {
							$result = DB::selectOne($sql);
							DB::table('store_grade')->where('idx', "=", $idx)->update($row);
						} else {
							$row['seq'] = count($data) - 1;
							DB::table('store_grade')->insert($row);
						}
					}
				}
			});
			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			dd($e);
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

