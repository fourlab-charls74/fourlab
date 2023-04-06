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
        $sdate = $mutable->sub(1, 'week')->format('Y-m-d');
		$items = SLib::getItems();
        $com_types = SLib::getCodes("G_COM_TYPE");
		$as_states = SLib::getCodes("AS_STATE");
		$values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
			'items' => $items,
		    'com_types' => $com_types,
		    'as_states' => $as_states
        ];
		return view(Config::get('shop.store.view') . '/standard/std11', $values);
	}

	public function view()
	{
        $mutable = now();
        $sdate = $mutable->sub(1, 'day')->format('Y-m-d');
		$items = SLib::getItems();
        $com_types = SLib::getCodes("G_COM_TYPE");
		$as_states = SLib::getCodes("AS_STATE");

		//컬러
		$color_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_color' order by code_id asc ";
		$colors = DB::select($color_sql);
		
		//사이즈
		$size_sql = "select code_id, code_val, code_val2 from code where code_kind_cd = 'prd_cd_size_match' order by code_id asc ";
		$sizes = DB::select($size_sql);

		$values = [
            'sdate' 	=> $sdate,
            'edate' 	=> date("Y-m-d"),
			'items' 	=> $items,
		    'com_types' => $com_types,
		    'as_states' => $as_states,
			'colors'	=> $colors,
			'sizes'		=> $sizes
        ];
		return view(Config::get('shop.store.view') . '/standard/std11_view', $values);
	}

	public function search(Request $request)
	{
		$date_type = $request->input('date_type');
		$sdate = $request->input('sdate');
		$edate = $request->input('edate');
		$where1 = $request->input('where1');
		$where2 = $request->input('where2');
		$store_cd = $request->input('store_no');
		$as_type = $request->input('as_type');

		$where = "";
		if ($date_type != '') $where .= "and $date_type >= '$sdate' and $date_type <= '$edate'";
		if ($where1 != '') $where .= "and $where1 like '%" . $where2 . "%'";
		if ($store_cd != '') $where .= "and a.store_cd = '$store_cd'";
		if ($as_type != '') $where .= "and a.as_type = '$as_type'";
		

		$query = /** @lang text */
            "select
				a.*
				, s.store_nm as store_nm
			from after_service2 a
				left outer join store s on s.store_cd = a.store_cd 
			where 1=1 
			$where
			order by a.idx desc
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

	public function showCreate()
	{
		$mutable = now();
        $sdate = $mutable->format('Y-m-d');
		$items = SLib::getItems();
        $com_types = SLib::getCodes("G_COM_TYPE");
		$as_states = SLib::getCodes("AS_STATE");
		$values = [
			'type' => 'create',
            'sdate' => $sdate,
			'items' => $items,
		    'com_types' => $com_types,
			'as_states' => $as_states
        ];
		return view(Config::get('shop.store.view') . '/standard/std11_show', $values);
	}

	public function showDetail($idx = "")
	{
		$row = DB::table('after_service2')->where("idx", "=", $idx)->first();
		$mobile = $row->mobile;
		$items = SLib::getItems();
		if ($mobile != "") $row->mobile = explode("-", $mobile);
		$as_states = SLib::getCodes("AS_STATE");

		//컬러
		$color_sql = "select code_id, code_val from code where code_kind_cd = 'prd_cd_color' order by code_id asc ";
		$colors = DB::select($color_sql);

		//사이즈
		$size_sql = "select code_id, code_val, code_val2 from code where code_kind_cd = 'prd_cd_size_match' order by code_id asc ";
		$sizes = DB::select($size_sql);


		$values = [ 
			'type' => 'detail',
			'idx' => $idx,
			'items' => $items,
			'row' => $row,
			'store' => DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', '=', $row->store_cd)->first(),
			'as_states' => $as_states,
			'colors'	=> $colors,
			'sizes'		=> $sizes
		];
		return view(Config::get('shop.store.view') . '/standard/std11_detail', $values);
	}

	public function create(Request $request)
	{
		$inputs = $request->all();
		$store_cd = $request->input("store_no");
		$inputs['mobile'] = $inputs['mobile'] ? implode("-", array_filter($inputs['mobile'])) : "";
		if($inputs['store_nm'] == '') {
			$store_nm = DB::table('store')
							->where('store_cd', $store_cd)
							->value('store_nm');
			$inputs['store_nm'] = $store_nm;
		}

		try {
			DB::transaction(function () use ($inputs) {
				DB::table(self::T)->insert($inputs);
			});

			//수선 알림 전송
			if ($inputs['as_state'] >= 30) {
				$content = '';
				switch ($inputs['as_state']) {
					case 30 : 
						$content = '수선 심의 중입니다.';
						break;
					case 40 : 
						$content = '수선 진행 중입니다.';
						break;
					case 50 : 
						$content = '수선이 완료됐습니다.';
						break;
					default : break;
				}

				DB::beginTransaction();

                $res = DB::table('msg_store')
                    ->insertGetId([
                        'msg_kind' => 'AS',
                        'sender_type' => 'H',
                        'sender_cd' => 'HEAD',
                        'reservation_yn' => 'N',
                        'content' => $content,
                        'rt' => now()
                    ]);
                
                DB::table('msg_store_detail')
                    ->insert([
                        'msg_cd' => $res,
                        'receiver_type' => 'S',
                        'receiver_cd' => $store_cd,
                        'check_yn' => 'N',
                        'rt' => now()
                    ]);
				
				DB::commit();
			}

			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			return response()->json(['code' => '500']);
		}
	}

	public function edit(Request $request)
	{
		$inputs = $request->all();
		$store_cd = $request->input("store_no");
		$inputs['mobile'] = $inputs['mobile'] ? implode("-", array_filter($inputs['mobile'])) : "";
		if($inputs['store_nm'] == '') {
			$store_nm = DB::table('store')
							->where('store_cd', $store_cd)
							->value('store_nm');
			$inputs['store_nm'] = $store_nm;
		}

		$ori_as_state = DB::table('after_service')
							->where('idx', $inputs['idx'])
							->value('as_state');

		try {
			DB::transaction(function () use ($inputs) {
				DB::table(self::T)->where('idx', $inputs['idx'])->update($inputs);
			});

			//수선 알림 전송
			if ($ori_as_state != $inputs['as_state'] && $inputs['as_state'] >= 30) {
				$content = '';
				switch ($inputs['as_state']) {
					case 30 : 
						$content = '수선 심의 중입니다.';
						break;
					case 40 : 
						$content = '수선 진행 중입니다.';
						break;
					case 50 : 
						$content = '수선이 완료됐습니다.';
						break;
					default : break;
				}

				DB::beginTransaction();

                $res = DB::table('msg_store')
                    ->insertGetId([
                        'msg_kind' => 'AS',
                        'sender_type' => 'H',
                        'sender_cd' => 'HEAD',
                        'reservation_yn' => 'N',
                        'content' => $content,
                        'rt' => now()
                    ]);
                
                DB::table('msg_store_detail')
                    ->insert([
                        'msg_cd' => $res,
                        'receiver_type' => 'S',
                        'receiver_cd' => $store_cd,
                        'check_yn' => 'N',
                        'rt' => now()
                    ]);
				
				DB::commit();
			}

			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			return response()->json(['code'	=> '500']);
		}
	}

	public function remove(Request $request)
	{
		$idx = $request->input('idx');
		try {
			DB::transaction(function () use ($idx) {
				DB::table(self::T)->where('idx', $idx)->delete();
			});
			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			return response()->json(['code'	=> '500']);
		}
	}

	public function batchEdit(Request $request)
	{
		$inputs = $request->all();
		try {
			DB::transaction(function () use ($inputs) {
				$type = $inputs['type'];
				$date = $inputs['date'];
				$data =	$inputs['data'];
				collect($data)->map(function ($row) use ($type, $date) {
					DB::table(self::T)->where('idx', $row['idx'])->update([$type => $date]);
				});
			});
			
			return response()->json(['code'	=> '200']);
		} catch (Exception $e) {
			// dd($e);
			return response()->json(['code'	=> '500']);
		}
	}

	public function save(Request $request) 
	{

		$data = $request->all();

		$mobile = $data['mobile'][0].'-'.$data['mobile'][1].'-'.$data['mobile'][2];

		if($data['as_type'] == '1') { //매장접수(A/S)
			$as_state = 10;
		} elseif ($data['as_type'] == '2') { //매장접수(불량)
			$as_state = 11;
		} elseif ($data['as_type'] == '3') { //매장접수(심의)
			$as_state = 12;
		}

		try {
			DB::beginTransaction();

			DB::table('after_service2')
				->insert([
					'receipt_date' => $data['edate'],
					'as_state' => $as_state,
					'store_cd' => $data['store_no'],
					'as_type' => $data['as_type'],
					'customer_no' => $data['customer_no']??'',
					'customer' => $data['customer']??'',
					'mobile' => $mobile,
					'zipcode' => $data['zipcode']??'',
					'addr1' => $data['addr1']??'',
					'addr2' => $data['addr2']??'',
					'prd_cd' => $data['prd_cd'],
					'goods_nm' => $data['goods_nm'],
					'color' => $data['color'],
					'size' => $data['size'],
					'qty' => $data['qty'],
					'is_free' => $data['is_free'],
					'as_amt' => $data['as_amt']??'',
					'content' => $data['content']??'',
					'rt' => now(),
					'ut' => now(),
				]);

			DB::commit();
			$code = 200;
			$msg = "수선등록이 완료되었습니다.";

		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);

	}

	public function change_state(Request $request) {
		/*
		 * 접수 구분
		 *  1 : 매장접수(A/S)
		 *  2 : 매장접수(불량)
		 *  3 : 매장접수(심의)
		 *  4 : 본사A/S접수/진행
		 *  5 : 본사A/S완료
		 *  6 : 본사불량
		 */

		/**
		 * 수선진행상태
		 *  10 : 수선요청
		 *  11 : 불량요청
		 *  12 : 본사심의요청
		 *  20 : 수선접수
		 *  30 : 수선진행
		 *  40 : 수선완료
		 *  50 : 불량
		 */

		$data = $request->all();
		$mobile = $data['mobile'][0].'-'.$data['mobile'][1].'-'.$data['mobile'][2];

		try {
			DB::beginTransaction();
			if ($data['as_type'] == '1') { // 매장접수(A/S)
				
				DB::table('after_service2')
					->where('idx', '=', $data['idx'])
					->update([
						'receipt_date' => $data['edate'],
						'as_state' => 10,
						'store_cd' => $data['store_no'],
						'as_type' => '1',
						'customer_no' => $data['customer_no']??'',
						'customer' => $data['customer']??'',
						'mobile' => $mobile,
						'zipcode' => $data['zipcode']??'',
						'addr1' => $data['addr1']??'',
						'addr2' => $data['addr2']??'',
						'prd_cd' => $data['prd_cd'],
						'goods_nm' => $data['goods_nm'],
						'color' => $data['color'],
						'size' => $data['size'],
						'qty' => $data['qty'],
						'is_free' => $data['is_free'],
						'as_amt' => $data['as_amt']??'',
						'content' => $data['content']??'',
						'h_receipt_date' => null,
						'end_date' => null,
						'err_date' => null,
						'h_content'	=> $data['h_content']??'',
						'ut' => now()
				]);

			} elseif ($data['as_type'] == '2') { //매장접수(불량)

				DB::table('after_service2')
					->where('idx', '=', $data['idx'])
					->update([
						'receipt_date' => $data['edate'],
						'as_state' => 11,
						'store_cd' => $data['store_no'],
						'as_type' => '2',
						'customer_no' => $data['customer_no']??'',
						'customer' => $data['customer']??'',
						'mobile' => $mobile,
						'zipcode' => $data['zipcode']??'',
						'addr1' => $data['addr1']??'',
						'addr2' => $data['addr2']??'',
						'prd_cd' => $data['prd_cd'],
						'goods_nm' => $data['goods_nm'],
						'color' => $data['color'],
						'size' => $data['size'],
						'qty' => $data['qty'],
						'is_free' => $data['is_free'],
						'as_amt' => $data['as_amt']??'',
						'content' => $data['content']??'',
						'h_receipt_date' => null,
						'end_date' => null,
						'err_date' => null,
						'h_content'	=> $data['h_content']??'',
						'ut' => now()
				]);

			} elseif ($data['as_type'] == '3') { //매장접수(심의)

				DB::table('after_service2')
					->where('idx', '=', $data['idx'])
					->update([
						'receipt_date' => $data['edate'],
						'as_state' => 12,
						'store_cd' => $data['store_no'],
						'as_type' => '3',
						'customer_no' => $data['customer_no']??'',
						'customer' => $data['customer']??'',
						'mobile' => $mobile,
						'zipcode' => $data['zipcode']??'',
						'addr1' => $data['addr1']??'',
						'addr2' => $data['addr2']??'',
						'prd_cd' => $data['prd_cd'],
						'goods_nm' => $data['goods_nm'],
						'color' => $data['color'],
						'size' => $data['size'],
						'qty' => $data['qty'],
						'is_free' => $data['is_free'],
						'as_amt' => $data['as_amt']??'',
						'content' => $data['content']??'',
						'h_receipt_date' => null,
						'end_date' => null,
						'err_date' => null,
						'h_content'	=> $data['h_content']??'',
						'ut' => now()
				]);

			} elseif ($data['as_type'] == '4') { // 본사 A/S 접수인 경우

				DB::table('after_service2')
					->where('idx', '=', $data['idx'])
					->update([
						'receipt_date' => $data['edate'],
						'as_state' => 30,
						'store_cd' => $data['store_no'],
						'as_type' => '4',
						'customer_no' => $data['customer_no']??'',
						'customer' => $data['customer']??'',
						'mobile' => $mobile,
						'zipcode' => $data['zipcode']??'',
						'addr1' => $data['addr1']??'',
						'addr2' => $data['addr2']??'',
						'prd_cd' => $data['prd_cd'],
						'goods_nm' => $data['goods_nm'],
						'color' => $data['color'],
						'size' => $data['size'],
						'qty' => $data['qty'],
						'is_free' => $data['is_free'],
						'as_amt' => $data['as_amt']??'',
						'content' => $data['content']??'',
						'h_receipt_date' => $data['h_receipt_date']??now(),
						'h_content'	=> $data['h_content']??'',
						'ut' => now()
				]);

			} elseif($data['as_type'] == '5') { //본사 A/S 완료인 경우

					DB::table('after_service2')
						->where('idx', '=', $data['idx'])
						->update([
							'receipt_date' => $data['edate'],
							'as_state' => 40,
							'store_cd' => $data['store_no'],
							'as_type' => '5',
							'customer_no' => $data['customer_no']??'',
							'customer' => $data['customer']??'',
							'mobile' => $mobile,
							'zipcode' => $data['zipcode']??'',
							'addr1' => $data['addr1']??'',
							'addr2' => $data['addr2']??'',
							'prd_cd' => $data['prd_cd'],
							'goods_nm' => $data['goods_nm'],
							'color' => $data['color'],
							'size' => $data['size'],
							'qty' => $data['qty'],
							'is_free' => $data['is_free'],
							'as_amt' => $data['as_amt']??'',
							'content' => $data['content']??'',
							'h_receipt_date' => $data['h_receipt_date']??now(),
							'end_date' => $data['end_date']??now(),
							'h_content'	=> $data['h_content']??'',
							'ut' => now()
					]);

			} elseif ($data['as_type'] == '6') { //본사불량인 경우

				DB::table('after_service2')
					->where('idx', '=', $data['idx'])
					->update([
						'receipt_date' => $data['edate'],
						'as_state' => 50,
						'store_cd' => $data['store_no'],
						'as_type' => '6',
						'customer_no' => $data['customer_no']??'',
						'customer' => $data['customer']??'',
						'mobile' => $mobile,
						'zipcode' => $data['zipcode']??'',
						'addr1' => $data['addr1']??'',
						'addr2' => $data['addr2']??'',
						'prd_cd' => $data['prd_cd'],
						'goods_nm' => $data['goods_nm'],
						'color' => $data['color'],
						'size' => $data['size'],
						'qty' => $data['qty'],
						'is_free' => $data['is_free'],
						'as_amt' => $data['as_amt']??'',
						'content' => $data['content']??'',
						'err_date' => $data['err_date']??now(),
						'h_content'	=> $data['h_content']??'',
						'ut' => now()
				]);

			}


			// if ($data['h_receipt_date'] != '') { //본사접수일이 입력되어있으면 접수구분이 자동으로 본사 A/S접수 진행으로 변경 수선진행상태를 수선진행으로 변경

			// 	DB::table('after_service2')
			// 		->where('idx', '=', $data['idx'])
			// 		->update([
			// 			'receipt_date' => $data['edate'],
			// 			'as_state' => 30,
			// 			'store_cd' => $data['store_no'],
			// 			'as_type' => '4',
			// 			'customer_no' => $data['customer_no'],
			// 			'customer' => $data['customer'],
			// 			'mobile' => $mobile,
			// 			'zipcode' => $data['zipcode'],
			// 			'addr1' => $data['addr1'],
			// 			'addr2' => $data['addr2'],
			// 			'prd_cd' => $data['prd_cd'],
			// 			'goods_nm' => $data['goods_nm'],
			// 			'color' => $data['color'],
			// 			'size' => $data['size'],
			// 			'qty' => $data['qty'],
			// 			'is_free' => $data['is_free'],
			// 			'as_amt' => $data['as_amt']??'',
			// 			'content' => $data['content']??'',
			// 			'h_receipt_date' => $data['h_receipt_date']??now(),
			// 			'h_content'	=> $data['h_content']??'',
			// 			'ut' => now()
			// 	]);
				
			// } elseif ($data['end_date'] != '') { //수선완료일이 빈값이 아니면 자동으로 접수구분이 본사A/S완료로 변경 수선진행상태를 수선완료로 변경

			// 		DB::table('after_service2')
			// 			->where('idx', '=', $data['idx'])
			// 			->update([
			// 				'receipt_date' => $data['edate'],
			// 				'as_state' => 40,
			// 				'store_cd' => $data['store_no'],
			// 				'as_type' => '5',
			// 				'customer_no' => $data['customer_no'],
			// 				'customer' => $data['customer'],
			// 				'mobile' => $mobile,
			// 				'zipcode' => $data['zipcode'],
			// 				'addr1' => $data['addr1'],
			// 				'addr2' => $data['addr2'],
			// 				'prd_cd' => $data['prd_cd'],
			// 				'goods_nm' => $data['goods_nm'],
			// 				'color' => $data['color'],
			// 				'size' => $data['size'],
			// 				'qty' => $data['qty'],
			// 				'is_free' => $data['is_free'],
			// 				'as_amt' => $data['as_amt']??'',
			// 				'content' => $data['content']??'',
			// 				'h_receipt_date' => $data['h_receipt_date']??$data['end_date'],
			// 				'end_date' => $data['end_date'],
			// 				'h_content'	=> $data['h_content']??'',
			// 				'ut' => now()
			// 		]);

			// } elseif ($data['err_date'] != '') { // 불량등록일이 빈값이 아닐때 접수구분을 본사불량으로 변경 수선진행상태를 본사 불량 처리

			// 	DB::table('after_service2')
			// 		->where('idx', '=', $data['idx'])
			// 		->update([
			// 			'receipt_date' => $data['edate'],
			// 			'as_state' => 50,
			// 			'store_cd' => $data['store_no'],
			// 			'as_type' => '6',
			// 			'customer_no' => $data['customer_no'],
			// 			'customer' => $data['customer'],
			// 			'mobile' => $mobile,
			// 			'zipcode' => $data['zipcode'],
			// 			'addr1' => $data['addr1'],
			// 			'addr2' => $data['addr2'],
			// 			'prd_cd' => $data['prd_cd'],
			// 			'goods_nm' => $data['goods_nm'],
			// 			'color' => $data['color'],
			// 			'size' => $data['size'],
			// 			'qty' => $data['qty'],
			// 			'is_free' => $data['is_free'],
			// 			'as_amt' => $data['as_amt']??'',
			// 			'content' => $data['content']??'',
			// 			'err_date' => $data['err_date'],
			// 			'h_content'	=> $data['h_content']??'',
			// 			'ut' => now()
			// 	]);

			// }

			DB::commit();
			$code = 200;
			$msg = "수선정보가 저장되었습니다.";

		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}


	//삭제
	public function delete(Request $request)
	{
		$idx = $request->input('idx');

		try {
			DB::beginTransaction();

			for ($i = 0; $i < count($idx); $i++){
				DB::table('after_service2')
					->where('idx', '=', $idx[$i])
					->delete();
			}

			DB::commit();
			$code = 200;
			$msg = "수선정보가 삭제되었습니다.";

		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

}
