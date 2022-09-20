<?php

namespace App\Http\Controllers\store\standard;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class std02Controller extends Controller
{
	public function index() 
	{
	
		$values = [
			'store_types'	=> SLib::getCodes("STORE_TYPE"),	// 매장구분
			'store_kinds'	=> SLib::getCodes("STORE_KIND"),	// 매장종류
			'store_areas'	=> SLib::getCodes("STORE_AREA")		// 매장지역
		];

		return view( Config::get('shop.store.view') . '/standard/std02',$values);

	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$store_type	= $request->input("store_type");
		$store_kind	= $request->input("store_kind");
		$store_area	= $request->input("store_area");
		$store_nm	= $request->input("store_nm");
		$store_cd	= $request->input("store_cd");
		$use_yn		= $request->input("use_yn");			// 사용유무
		
		$limit		= $request->input("limit",100);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','a.rt');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);
		
		$where = "";
		if( $store_type != "" )	$where .= " and a.store_type = '$store_type' ";
		if( $store_kind != "" )	$where .= " and a.store_kind = '$store_kind' ";
		if( $store_area != "" )	$where .= " and a.store_area = '$store_area' ";
		if( $store_nm != "" )	$where .= " and ( a.store_nm like '%" . Lib::quote($store_nm) . "%' or a.store_nm_s like '%" . Lib::quote($store_nm) . "%' ) ";
		if( $store_cd != "" )	$where .= " and a.com_id = '" . Lib::quote($store_cd) . "' ";
		if( $use_yn != "" )		$where .= " and a.use_yn = '$use_yn' ";

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

		if( $page == 1 ){
			$query	= "
				select count(*) as total
				from store a
				where 1=1 $where
			";
			//$row = DB::select($query,['com_id' => $com_id]);
			$row		= DB::select($query);
			$total		= $row[0]->total;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		}

		$query	= "
			select
				a.*,
				c.code_val as store_type_nm,
				d.code_val as store_kind_nm,
				e.code_val as store_area_nm,
				sg.name as grade_nm
			from store a
			left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
			left outer join code d on d.code_kind_cd = 'store_kind' and d.code_id = a.store_kind
			left outer join code e on e.code_kind_cd = 'store_area' and e.code_id = a.store_area
			left outer join store_grade sg on a.grade_cd = sg.grade_cd
			where 1=1 
				and concat(sg.sdate, '-01 00:00:00') <= date_format(now(), '%Y-%m-%d 00:00:00') 
				and concat(sg.edate, '-31 23:59:59') >= date_format(now(), '%Y-%m-%d 00:00:00') 
				$where
			$orderby
			$limit
		";

		$result = DB::select($query);

		foreach($result as $row){/*
			$row->manager_deposit	= Lib::cm($row->manager_deposit);
			$row->manager_fee		= Lib::cm($row->manager_fee);
			$row->manager_sfee		= Lib::cm($row->manager_sfee);
			$row->deposit_cash		= Lib::cm($row->deposit_cash);
			$row->deposit_coll		= Lib::cm($row->deposit_coll);
			$row->interior_cost		= Lib::cm($row->interior_cost);
			$row->interior_burden	= Lib::cm($row->interior_burden);
			$row->fee				= Lib::cm($row->fee);
		*/}

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

	public function show($store_cd = '')
	{
		$store	= "";
		$store_img	= "";
		$grades = [];

		if($store_cd != '') {
			$sql = "
				select * from store
				where store_cd = :store_cd
			";

			$store = DB::selectOne($sql, ["store_cd" => $store_cd]);
		}

		if($store_cd != '') {
			$img_sql = "
					select * from store_img
					where store_cd = :store_cd
			";

			$store_img = DB::select($img_sql, ["store_cd" => $store_cd]);
		}

		// dd($store_img);

		$values = [
			"cmd"	=> $store_cd == '' ? "" : "update",
			"store"	=> $store,
			'store_img' => $store_img,
			'store_types' => SLib::getCodes("STORE_TYPE"),
			'store_kinds' => SLib::getCodes("STORE_KIND"),
			'store_areas' => SLib::getCodes("STORE_AREA"),
			'grades' => SLib::getValidStoreGrades(),
			'prioritys' => SLib::getCodes("PRIORITY")
		];

		return view( Config::get('shop.store.view') . '/standard/std02_show',$values);
	}

	// 매장코드 중복체크
	public function check_code($store_cd = '') 
	{
		$code	= 200;
		$msg	= "사용가능한 코드입니다.";

		$sql	= " select count(store_cd) as cnt from store where store_cd = :store_cd ";

		$cnt	= DB::selectOne($sql, ["store_cd" => $store_cd])->cnt;

		if( $cnt > 0 ){
			$code	= 409;
			$msg	= "이미 사용중인 코드입니다.";
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	
	// 매장 등록/수정
	public function update_store(Request $request){

		$id			= Auth('head')->user()->id;
		$code		= 200;
		$msg		= "매장정보가 정상적으로 반영되었습니다.";
		$store_cd 	= $request->input('store_cd');
		$image 		= $request->file('file');

		try {
			DB::beginTransaction();

			$where	= [
				'store_cd'	=> $request->input('store_cd')
			];

			$values	= [
				'store_nm'		=> $request->input('store_nm'),
				'store_nm_s'	=> $request->input('store_nm_s'),
				'store_type'	=> $request->input('store_type'),
				'store_kind'	=> $request->input('store_kind'),
				'store_area'	=> $request->input('store_area'),
				'grade_cd'		=> $request->input('grade_cd'),
				'zipcode'		=> $request->input('zipcode'),
				'addr1'			=> $request->input('addr1'),
				'addr2'			=> $request->input('addr2'),
				'phone'			=> $request->input('phone'),
				'fax'			=> $request->input('fax'),
				'mobile'		=> $request->input('mobile'),
				'manager_nm'	=> $request->input('manager_nm'),
				'manager_mobile'=> $request->input('manager_mobile'),
				'email'			=> $request->input('email'),
				'fee'			=> $request->input('fee'),
				'sale_fee'		=> $request->input('sale_fee'),
				'md_manage_yn'	=> $request->input('md_manage_yn'),
				'bank_no'		=> $request->input('bank_no'),
				'bank_nm'		=> $request->input('bank_nm'),
				'depositor'		=> $request->input('depositor'),
				'deposit_cash'	=> $request->input('deposit_cash'),
				'deposit_coll'	=> $request->input('deposit_coll'),
				'loss_rate'		=> $request->input('loss_rate'),
				'sdate'			=> $request->input('sdate'),
				'edate'			=> $request->input('edate'),
				'use_yn'		=> $request->input('use_yn'),
				'ipgo_yn'		=> $request->input('ipgo_yn'),
				'vat_yn'		=> $request->input('vat_yn'),
				'biz_no'		=> $request->input('biz_no'),
				'biz_nm'		=> $request->input('biz_nm'),
				'biz_ceo'		=> $request->input('biz_ceo'),
				'biz_zipcode'	=> $request->input('biz_zipcode'),
				'biz_addr1'		=> $request->input('biz_addr1'),
				'biz_addr2'		=> $request->input('biz_addr2'),
				'biz_uptae'		=> $request->input('biz_uptae'),
				'biz_upjong'	=> $request->input('biz_upjong'),
				'manage_type'	=> $request->input('manage_type'),
				'exp_manage_yn'	=> $request->input('exp_manage_yn'),
				'priority'		=> $request->input('priority'),
				'competitor_yn'	=> $request->input('competitor_yn'),
				'pos_yn'		=> $request->input('pos_yn'),
				'ostore_stock_yn'	=> $request->input('ostore_stock_yn'),
				'sale_dist_yn'	=> $request->input('sale_dist_yn'),
				'rt_yn'			=> $request->input('rt_yn'),
				'point_in_yn'	=> $request->input('point_in_yn'),
				'point_out_yn'	=> $request->input('point_out_yn'),
				'reg_date'		=> now(),
				'mod_date'		=> now(),
				'admin_id'		=> $id
			];

			DB::table('store')->updateOrInsert($where, $values);


			// 이미지 저장
			$base_path = "/images/std02";

			if (!Storage::disk('public')->exists($base_path)) {
				Storage::disk('public')->makeDirectory($base_path);
			}

			// if ($image != null &&  $image != "") {
			// 	foreach ($image as $key => $ig) {
			// 		$ig_cnt = $key + 1;
					
			// 		$file_name = sprintf("%s_%s.jpg", $store_cd, "$ig_cnt");
			// 		$save_file = sprintf("%s/%s", $base_path, $file_name);
			// 		Storage::disk('public')->putFileAs($base_path, $ig, $file_name);

			// 		$insert_values = [
			// 			'img_url' => $save_file,
			// 			'store_cd' => $store_cd,
			// 			'seq' => $ig_cnt,
			// 			'rt' => now(),
			// 			'admin_id' => $id
			// 		];
			// 		DB::table('store_img')->insert($insert_values);
			// 	}
			// }

			$sql = "
				select seq
				from store_img
				where store_cd = '$store_cd'
				order by seq desc
				limit 1
			";
			$res = DB::selectOne($sql);

			$last_seq = 0;

			if($res !== null) {
				$last_seq = $res->seq;
			}

			if ($image != null &&  $image != "") {
				foreach ($image as $ig) {
					$cnt = $last_seq + 1;
					
					$file_name = sprintf("%s_%s.jpg", $store_cd, "$cnt");
					$save_file = sprintf("%s/%s", $base_path, $file_name);
					Storage::disk('public')->putFileAs($base_path, $ig, $file_name);

					$insert_values = [
						'img_url' => $save_file,
						'store_cd' => $store_cd,
						'seq' => $cnt,
						'rt' => now(),
						'admin_id' => $id
					];
					DB::table('store_img')->insert($insert_values);
					$last_seq++;
				}
				
			}


			DB::commit();

			return response()->json(["code" => $code, "msg" => $msg, "store_cd" => $request->input('store_cd')]);

		} catch(Exception $e) {
			$msg = $e->getMessage();
			DB::rollback();
			return response()->json(["code" => '500', 'msg' => $msg]);
			// 에러가 발생했습니다. 잠시 후 다시시도 해주세요.
		}
	}

	public function del_img(Request $request)
	{
		$store_cd = $request->input('data_img');
		$seq = $request->input('seq');
		
		try {
            DB::beginTransaction();

			$sel_sql = "
				select img_url
				from store_img
				where store_cd = '$store_cd' and seq = $seq

			";
			$row = DB::selectOne($sel_sql);

            $sql = "
                delete 
                from store_img
                where store_cd = '$store_cd' and seq = $seq
            ";

            DB::delete($sql);
			

			ULib::deleteFile($row->img_url);

            DB::commit();
            $code = '200';
            $msg = "";
        } catch (Exception $e) {
            DB::rollBack();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
	}


	public function delete($com_id)
	{
		try {
			DB::transaction(function () use (&$result, $com_id) {
				DB::table('__tmp_store')->where('com_id', $com_id)->delete();
			});

			DB::transaction(function () use (&$result, $com_id) {
				DB::table('__tmp_store_info')->where('com_id', $com_id)->delete();
			});

			$code = 200;
		} catch (Exception $e) {
			$code = 500;
		}
		return response()->json(['code' => $code]);
	}

}
