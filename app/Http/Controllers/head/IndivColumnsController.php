<?php

namespace App\Http\Controllers\head;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\RedisInstance;
use Predis\Collection\Iterator;

class IndivColumnsController extends Controller
{
    public function save(Request $req) {
        $user_id = Auth('head')->user()->id;
		$pid = $req->input('pid', '');
		$indiv_columns = $req->input('indiv_columns', '');
		
		try {
			
			/*$ip = env('');
			$port = '22';
			$url = $ip . ':' . $port;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			$health = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($health) {
				$json = json_encode(['health' => $health, 'status' => '1']);
				return $json;
			} else {
				$json = json_encode(['health' => $health, 'status' => '0']);
				return $json;
			}*/
			
			$redis = app(RedisInstance::class)->getInstance();
			
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
				$delete_sql = "
					delete 
					from 
						indivisualization_columns
					where
						user_id = '$user_id'
						and pid = '$pid'
				";
				
				DB::update($delete_sql);
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

			$redis->set('indiv_menu_list:'.$user_id.":".$pid, $indiv_columns);
		} catch (Exception $e) {
			return response()->json([
				"code" => 500 ,
				"message" => $e->getMessage()
			]);

			$redis->del('indiv_menu_list:'.$user_id.":".$pid);
		} catch (\RedisException $re) {
			return response()->json([
				"code" => 500 ,
				"message" => $re->getMessage()
			]);
			
			DB::rollBack();
		} finally {
			DB::commit();
		}

		return response()->json([
			"code" => 200 ,
			"message" => '성공'
		]); 
    }

	public function get(Request $req) {
		$user_id = Auth('head')->user()->id;
		$pid = $req->input('pid', '');
		
		try {
			$redis = app(RedisInstance::class)->getInstance();
			$redis_columns = $redis->get('indiv_menu_list:'.$user_id.":".$pid);
			$return_columns = null;
			
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

			if($redis_columns !== null) {
				$return_columns = ['indiv_columns' => $redis_columns];
			} else {
				$return_columns = DB::selectOne($sql);
				
				if($return_columns === null) {
					$return_columns = ['indiv_columns' => []];
				} else {
					$redis->set('indiv_menu_list:'.$user_id.":".$pid, $return_columns->indiv_columns);	
				}
			}
			
			return response()->json([
				"code" => 200 ,
				"body" => $return_columns
			]);
			
		} catch (\RedisException $re) {
			return response()->json([
				"code" => 500 ,
				"message" => $re->getMessage()
			]);
		}
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
			$redis = app(RedisInstance::class)->getInstance();
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

			if($type === 'E') {
				$iterator = null;
				while ($keys = $redis->scan($iterator, ['match' => "*:".strtoupper($pid), 'count' => 20])) {
					$iterator = $keys[0];
					foreach ($keys[1] as $key) {
						$redis->del($key);
					}
				}
			} else {
				$redis->del('indiv_menu_list:'.$user_id.":".$pid);
			}
			
		} catch (exception $e) {
			return response()->json([
				"code" => 500 ,
				"message" => $e->getMessage()
			]);
		}  catch (\RedisException $re) {
			return response()->json([
				"code" => 500 ,
				"message" => $re->getMessage()
			]);
			
			DB::rollBack();
		} finally {
			DB::commit();
		}
		
		return response()->json([
			"code" => 200 ,
			"message" => '성공'
		]);
	}
}
