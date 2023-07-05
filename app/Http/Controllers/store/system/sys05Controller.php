<?php

namespace App\Http\Controllers\store\system;

use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class sys05Controller extends Controller
{
	protected $types = [
		"shop" => "매장",
		"storage" => "창고",
		"etc" => "기타"
	];

    public function index()
    {
        $values = [ 'types' => $this->types ];
        return view(Config::get('shop.store.view') . '/system/sys05', $values);
    }

	public function show(Request $request)
	{
		$type = $request->input('type', '');
		$name = $request->input('name', '');
		$idx = $request->input('idx', '');
		$conf = '';
		
		if ($type !== '') {
			$conf = DB::table('store_conf')->where([
				'type' => $type,
				'name' => $name,
				'idx' => $idx ?? '',
			])->first();
		}

		$values = [
			'cmd' => ($type == '' || $conf == null) ? 'add' : 'update',
			'conf' => $conf,
			'types' => $this->types,
		];
		return view(Config::get('shop.store.view') . '/system/sys05_show', $values);
	}

	/** 환경관리 조회 */
	public function search(Request $req)
	{
		$type = $req->input('type', '');
		$name = $req->input('name', '');
		$idx  = $req->input('idx', '');

		$where = "";

		if ($type != "")	$where .= " and type like '%" . Lib::quote($type) . "%' ";
		if ($name  != "")	$where .= " and name like '%" . Lib::quote($name) . "%' ";
		if ($idx  != "")	$where .= " and idx like '%" . Lib::quote($idx) . "%' ";

		$sql = " select * from store_conf where 1=1 $where ";
		$rows = DB::select($sql);

		foreach($rows as $row) {
			$row->type_nm = array_key_exists($row->type, $this->types) ? $this->types[$row->type] : '-';
		}

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => count($rows)
			],
			"body" => $rows
		]);
	}

	/** 환경관리 등록 */
	public function save(Request $request)
	{
		$code 		= 200;
		$msg 		= "";
		$type 		= $request->input('type');
		$name 		= $request->input('name');
		$idx 		= $request->input('idx','');
		$value 		= $request->input('value','');
		$mvalue 	= $request->input('mvalue','');
		$content 	= $request->input('content','');
		$desc 		= $request->input('desc','');
		$admin_id 	= Auth('head')->user()->id;
		$admin_nm 	= Auth('head')->user()->name;

		try {
			DB::beginTransaction();
			
			$already_cnt = DB::table('store_conf')->where([
				'type' => $type,
				'name' => $name,
				'idx' => $idx ?? '',
			])->count();
			if ($already_cnt > 0) {
				$code = 409;
				throw new Exception("구분/이름/이름(일련번호)이 중복된 값이 존재합니다.");
			}

			DB::table('store_conf')->insert([
				'type' 		=> $type,
				'name' 		=> $name,
				'idx' 		=> $idx ?? '',
				'value' 	=> $value,
				'mvalue' 	=> $mvalue,
				'content' 	=> $content,
				'desc'		=> $desc,
				'admin_id'	=> $admin_id,
				'admin_nm'	=> $admin_nm,
				'rt' 		=> DB::raw('now()'),
				'ut' 		=> DB::raw('now()'),
			]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			if ($code === 200) $code = 500;
			$msg = $e->getMessage();
		}
		return response()->json([ 'code' => $code, 'msg' => $msg ]);
	}

	/** 환경관리 수정 */
	public function update(Request $request)
	{
		$code 		= 200;
		$msg 		= "";
		$type 		= $request->input('type');
		$name 		= $request->input('name');
		$prev_idx 	= $request->input('prev_idx','');
		$idx 		= $request->input('idx','');
		$value 		= $request->input('value','');
		$mvalue 	= $request->input('mvalue','');
		$content 	= $request->input('content','');
		$desc 		= $request->input('desc','');
		$admin_id 	= Auth('head')->user()->id;
		$admin_nm 	= Auth('head')->user()->name;

		try {
			DB::beginTransaction();

			$already_cnt = DB::table('store_conf')->where([
				'type' => $type,
				'name' => $name,
				'idx' => $idx ?? '',
			])->count();
			if ($prev_idx !== $idx && $already_cnt > 0) {
				$code = 409;
				throw new Exception("구분/이름/이름(일련번호)이 중복된 값이 존재합니다.");
			}

			DB::table('store_conf')
				->where([
					'type' => $type,
					'name' => $name,
					'idx' => $prev_idx ?? '',
				])->update([
					'idx' 		=> $idx ?? '',
					'value' 	=> $value,
					'mvalue' 	=> $mvalue,
					'content' 	=> $content,
					'desc'		=> $desc,
					'admin_id'	=> $admin_id,
					'admin_nm'	=> $admin_nm,
					'ut' 		=> DB::raw('now()'),
				]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			if ($code === 200) $code = 500;
			$msg = $e->getMessage();
		}
		return response()->json([ 'code' => $code, 'msg' => $msg ]);
	}

	/** 환경관리 삭제 */
	public function remove(Request $request)
	{
		$code 		= 200;
		$msg 		= "";
		$type 		= $request->input('type');
		$name 		= $request->input('name');
		$idx 		= $request->input('idx','');

		try {
			DB::beginTransaction();

			DB::table('store_conf')
				->where([
					'type' => $type,
					'name' => $name,
					'idx' => $idx ?? '',
				])->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json([ 'code' => $code, 'msg' => $msg ]);
	}
}
