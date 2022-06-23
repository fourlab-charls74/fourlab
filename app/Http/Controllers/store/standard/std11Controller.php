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

class std11Controller extends Controller
{

	const T = "after_service";

	public function index()
	{
        $mutable = now();
        $sdate = $mutable->sub(1, 'day')->format('Y-m-d');
        $com_types = SLib::getCodes("G_COM_TYPE");
		$values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
		    "com_types" => $com_types
        ];
		return view(Config::get('shop.store.view') . '/standard/std11', $values);
	}

	public function search(Request $request)
	{
		$sdate = $request->input('sdate', "");
		$edate = $request->input('edate', "");
		$store_no = $request->input('store_no', "");
		$store_nm = $request->input('store_nm', "");
		$item = $request->input('item', "");
		$as_type = $request->input('as_type', "");
		$where1 = $request->input('where1', "");
		$where2 = $request->input('where2', "");

		$where = "";
		if ($sdate) $where .= " and a.receipt_date >= '${sdate}'";
		if ($edate) $where .= " and a.receipt_date < date_add('${edate}', interval 1 day)";
		if ($store_no != "") $where .= " and a.store_no like '%" . Lib::quote($store_no) . "%'";
		if ($store_nm != "") $where .= " and a.store_nm like '%" . Lib::quote($store_nm) . "%'";
		if ($item != "") $where .= " and a.item like '%" . Lib::quote($item) . "%'";
		if ($as_type != "") $where .= "and a.as_type = '" . Lib::quote($as_type) . "'";
		if ($where1 != "") $where .= " and a.${where1} like '%" . Lib::quote($where2) . "%'";

		$query = /** @lang text */
            "select * from after_service as `a`
			where 1=1 $where
        ";

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

	public function createIndex(Request $request)
	{
		$mutable = now();
        $sdate = $mutable->format('Y-m-d');
        $com_types = SLib::getCodes("G_COM_TYPE");
		$values = [
            'sdate' => $sdate,
		    "com_types" => $com_types
        ];
		return view(Config::get('shop.store.view') . '/standard/std11_create', $values);
	}

	public function create(Request $request)
	{
		$inputs = $request->all();
		$inputs['mobile'] = $inputs['mobile'] ? implode("-", array_filter($inputs['mobile'])) : "";
		try {
			DB::transaction(function () use ($inputs) {
				DB::table(self::T)->insert($inputs);
			});
			return response()->json(["code"	=> "200", "msg"	=> "등록되었습니다."]);
		} catch (Exception $e) {
			return response()->json(["code"	=> "500", "msg"	=> "등록 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요."]);
		}
	}

	public function detail($idx = "")
	{
		$row = DB::table(self::T)->where("idx", "=", $idx)->first();
		$values = [ 'idx' => $idx, 'row' => $row ];
		return view(Config::get('shop.store.view') . '/standard/std11_detail', $values);
	}

	public function edit(Request $request)
	{
		$inputs = $request->all();
		$inputs['mobile'] = $inputs['mobile'] ? implode("-", array_filter($inputs['mobile'])) : "";
		try {
			DB::transaction(function () use ($inputs) {
				DB::table(self::T)->where('idx', $inputs['idx'])->update($inputs);
			});
			return response()->json(["code"	=> "200", "msg"	=> "수정되었습니다."]);
		} catch (Exception $e) {
			return response()->json(["code"	=> "500", "msg"	=> "수정 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요."]);
		}
	}

	public function remove(Request $request)
	{
		$idx = $request->input("idx");
		try {
			DB::transaction(function () use ($idx) {
				DB::table(self::T)->where('idx', $idx)->delete();
			});
			return response()->json(["code"	=> "200", "msg"	=> "삭제되었습니다."]);
		} catch (Exception $e) {
			return response()->json(["code"	=> "500", "msg"	=> "삭제 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요."]);
		}
	}

}
