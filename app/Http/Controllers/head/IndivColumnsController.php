<?php

namespace App\Http\Controllers\head;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndivColumnsController extends Controller
{
    public function save(Request $req) {
        $user_id = Auth('head')->user()->id;
		$pid = $req->input('pid', '');
		$indiv_columns = $req->input('indiv_columns', '');
		
		try {
			
			DB::beginTransaction();
			
			$select_sql = "
				select 
					count(*) as cnt
				from 
					indivisualization_columns
				where
					user_id = '$user_id'
					and pid = '$pid'
			";
			
			$row = DB::selectOne($select_sql);
			
			if($row->cnt >= 1) {
				throw new \InvalidArgumentException('이미 개인화된 컬럼이 존재합니다. 초기화후 다시 시도해주세요');
			}
			
			$max_sql = "
				select
					ifnull(max(seq), 0) + 1 as next_seq
				from
				    indivisualization_columns
			";
			
			$max = DB::selectOne($max_sql);
			
			$sql = /** @lang text */
				"
				insert into indivisualization_columns(
					seq,
					user_id,
					pid, 
					indiv_columns
				) values (
					'$max->next_seq',
					'$user_id',
					'$pid',
					'$indiv_columns'
				)
			";

			$log_sql = /** @lang text */
				"
				insert into indivisualization_columns_log(
					type,
					user_id,
					pid, 
					indiv_columns,
					rt
				) values (
					'C',
					'$user_id',
					'$pid',
					'$indiv_columns',
					now()
				)
			";
				
			DB::insert($sql);
			DB::insert($log_sql);
			
			DB::commit();
		} catch (Exception $e) {
			return response()->json([
				"code" => 500 ,
				"message" => $e->getMessage()
			]);
		}

		return response()->json([
			"code" => 200 ,
			"message" => '성공'
		]); 
    }

	public function get(Request $req) {
		$user_id = Auth('head')->user()->id;
		$pid = $req->input('pid', '');

		$sql = /** @lang text */
			"
          	select 
          		indiv_columns
          	from 
          		indivisualization_columns
          	where
          		user_id = '$user_id'
          		and pid = '$pid'
        ";

		return response()->json([
			"code" => 200 ,
			"body" => DB::selectOne($sql)
		]);
	}

	public function init(Request $req) {
		$user_id = Auth('head')->user()->id;
		$pid = $req->input('pid', '');
		$type = $req->input('type', '');
		
		$where = "";
		
		if($type === '') {
			$where = " and user_id = '$user_id'";
		}
		
		try {
			DB::beginTransaction();
			
			$insert_sql = "
				insert into indivisualization_columns_log(
					type,
					user_id,
					pid,
					indiv_columns,
					rt                                    
				)
				select
					'D' as type,
					user_id,
					pid,
					indiv_columns,
					now()
				from 
					indivisualization_columns
				where
				    1 = 1
					$where
					and pid = '$pid'
			";
	
			$sql = /** @lang text */
				"
				delete 
				from 
					indivisualization_columns
				where
					1 = 1
					$where
					and pid = '$pid'
			";
				
			DB::insert($insert_sql);
			DB::delete($sql);
			
			DB::commit();
		} catch (exception $e) {
			return response()->json([
				"code" => 500 ,
				"message" => $e->getMessage()
			]);
		}
		
		return response()->json([
			"code" => 200 ,
			"message" => '성공'
		]);
	}
}
