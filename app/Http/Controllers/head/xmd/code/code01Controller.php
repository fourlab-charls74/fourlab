<?php

namespace App\Http\Controllers\head\xmd\code;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class code01Controller extends Controller
{
	//
	public function index() {

		$sql		= " select * from __tmp_code_kind order by code_kind_nm ";
		$code_kinds	= DB::select($sql);


		$values = [
			'code_kinds'	=> $code_kinds,
		];

		return view( Config::get('shop.head.view') . '/xmd/code/code01',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$code_kind_cd	= $request->input("code_kind_cd");		// 코드구분
		$code_id		= $request->input("code_id");			// 코드ID
		$code_val		= $request->input("code_val");			// 코드명
		$use_yn			= $request->input("use_yn");			// 사용유무

		$limit			= $request->input("limit",100);
		$ord			= $request->input('ord','desc');
		$ord_field		= $request->input('ord_field','g.goods_no');
		$orderby		= sprintf("order by a.code_kind_cd, %s %s", $ord_field, $ord);

		$where = "";
		if( $code_kind_cd != "" )	$where .= " and a.code_kind_cd = '" . Lib::quote($code_kind_cd) . "' ";
		if( $code_id != "" )		$where .= " and a.code_id = '" . Lib::quote($code_id) . "' ";
		if( $code_val != "" )		$where .= " and a.code_val like '%" . Lib::quote($code_val) . "%' ";
		if( $use_yn != "" )			$where .= " and a.use_yn = '" . Lib::quote($use_yn) . "' ";

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

		if( $page == 1 ){
			$query	= "
				select count(*) as total
				from __tmp_code a
				inner join __tmp_code_kind b on a.code_kind_cd = b.code_kind_cd
				where 1=1 
					$where
			";
			//$row = DB::select($query,['com_id' => $com_id]);
			$row		= DB::select($query);
			$total		= $row[0]->total;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		}

		$query	= "
			select
				b.code_kind_nm, a.*
			from __tmp_code a
			inner join __tmp_code_kind b on a.code_kind_cd = b.code_kind_cd
			where 1=1 $where
			$orderby
			$limit
		";

		$result = DB::select($query);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body" => $result
		]);

	}

	public function show()
	{

		$values = [
		];

		return view( Config::get('shop.head.view') . '/xmd/code/code01_show',$values);
	}

	public function upload(Request $request)
	{

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code01/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/xmd/code/code01/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update(Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

        try 
		{
            DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data	= (array)$datas[$i];
	
				$code_id		= $data["detail_code"];
				$code_val		= $data["detail_code_nm"];
				$use_yn			= $data["use_yn"];
				$code_kind_cd	= $data["code_kind"];
	
				$use_yn			= ($use_yn == "T")?"Y":"N";
	
				$query	= " select count(*) as cnt from __tmp_code where code_kind_cd = :code_kind_cd and code_id = :code_id ";
				$rows	= DB::selectOne($query, ['code_kind_cd' => $code_kind_cd, 'code_id' => $code_id]);
	
				if( $rows->cnt == 0 ){
					$sql	= "
						insert into __tmp_code( code_kind_cd, code_id, code_val, use_yn, admin_id, admin_nm, rt )
						values (  :code_kind_cd, :code_id, :code_val, :use_yn, :admin_id, :admin_nm, now() )
					";
					DB::insert($sql, ['code_kind_cd' => $code_kind_cd, 'code_id' => $code_id, 'code_val' => $code_val, 'use_yn' => $use_yn, 'admin_id' => $id, 'admin_nm' => $name]);
				}
				else{
					$sql	= "
						update __tmp_code set
							code_val	= :code_val,
							use_yn		= :use_yn,
							admin_id	= :admin_id,
							admin_nm	= :admin_nm,
							ut			= now()
						where
							code_kind_cd	= :code_kind_cd
							and code_id		= :code_id
					";
					DB::update($sql, ['code_kind_cd' => $code_kind_cd, 'code_id' => $code_id, 'code_val' => $code_val, 'use_yn' => $use_yn, 'admin_id' => $id, 'admin_nm' => $name]);
				}
			}
	
			DB::commit();
        }
		catch(Exception $e) 
		{
            DB::rollback();

			$result_code	= "500";
			$result_msg		= "데이터 등록/수정 오류";
		}



		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}
