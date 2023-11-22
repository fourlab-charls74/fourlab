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
			'store_areas'	=> SLib::getCodes("STORE_AREA"),	// 매장지역
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view( Config::get('shop.store.view') . '/standard/std02',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$store_kind	= $request->input("store_kind");
		// $store_type	= $request->input("store_type");
		$store_area	= $request->input("store_area");
		$store_nm	= $request->input("store_nm");
		$store_cd	= $request->input("store_cd");
		$use_yn		= $request->input("use_yn");			// 사용유무
		
		$limit		= $request->input("limit",100);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','a.rt');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);
		
		$where = "";
		// if( $store_type != "" )	$where .= " and a.store_type = '$store_type' ";
		if( $store_kind != "" )	$where .= " and a.store_kind = '$store_kind' ";
		if( $store_area != "" )	$where .= " and a.store_area = '$store_area' ";
		if( $store_nm != "" )	$where .= " and ( a.store_nm like '%" . Lib::quote($store_nm) . "%' or a.store_nm_s like '%" . Lib::quote($store_nm) . "%' ) ";
		if( $store_cd != "" )	$where .= " and a.store_cd like '%" . Lib::quote($store_cd) . "%' ";
		if( $use_yn != "" )		$where .= " and a.use_yn = '$use_yn' ";
		if ($store_channel != "") $where .= "and a.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and a.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

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
				sc.store_channel as store_channel,
				sc2.store_kind as store_channel_kind,
				c.code_val as store_type_nm,
				d.code_val as store_kind_nm,
				e.code_val as store_area_nm,
				if(sg.name <> '', sg.name, a.grade_cd) as grade_nm
			from store a
			left outer join store_channel sc on sc.store_channel_cd = a.store_channel and dep = 1
			left outer join store_channel sc2 on sc2.store_kind_cd = a.store_channel_kind and sc2.dep = 2
			left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
			left outer join code d on d.code_kind_cd = 'store_kind' and d.code_id = a.store_kind
			left outer join code e on e.code_kind_cd = 'store_area' and e.code_id = a.store_area
			left outer join (
				select grade_cd, name, sdate, edate
				from store_grade
				where concat(sdate, '-01 00:00:00') <= date_format(now(), '%Y-%m-%d 00:00:00') 
					and concat(edate, '-31 23:59:59') >= date_format(now(), '%Y-%m-%d 00:00:00') 
			) sg on a.grade_cd = sg.grade_cd
			where 1=1
				$where
			$orderby
			$limit
		";

		$result = DB::select($query);

		foreach($result as $row){
			//주소 전체 나오게 처리
			$row->addr1	= $row->addr1 . ' ' . $row->addr2;
			/*
			$row->manager_deposit	= Lib::cm($row->manager_deposit);
			$row->manager_fee		= Lib::cm($row->manager_fee);
			$row->manager_sfee		= Lib::cm($row->manager_sfee);
			$row->deposit_cash		= Lib::cm($row->deposit_cash);
			$row->deposit_coll		= Lib::cm($row->deposit_coll);
			$row->interior_cost		= Lib::cm($row->interior_cost);
			$row->interior_burden	= Lib::cm($row->interior_burden);
			$row->fee				= Lib::cm($row->fee);
			*/
		}

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
		$map_key = "";
		$store_kind = "";
		$sel_store_kind = "";

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

			$map_key_sql = "
					select code_val
					from code
					where code_kind_cd = 'MAP_KEY'
			";

			$map_key = DB::selectOne($map_key_sql);


		//업체 셀렉트박스
		//매칭되어있는 매장은 출력되지않게해야함

			$match_sql = "
						select
							c.com_id
							, c.com_nm
							, s.com_id as s_match
						from company c
						left outer join store s on s.com_id = c.com_id and s.com_id != ''
						where c.use_yn = 'Y'
						order by c.com_nm asc
			";

			$store_match = DB::select($match_sql);

		// if ($store_cd != '') {
		// 	$select_sql = "
		// 			select 
		// 				com_id 
		// 			from store 
		// 			where com_id != ''
		// 	";

		// 	$select_match = DB::select($select_sql);

		// }

		$sql = "
			select
				store_channel
				, store_channel_cd
				, use_yn
			from store_channel
			where dep = 1 and use_yn = 'Y'
			order by seq
		";

		$store_channel = DB::select($sql);


		$sql = "
			select
				store_kind
				, store_kind_cd
				, use_yn
			from store_channel
			where dep = 2 and use_yn = 'Y'
		";

		$store_kind = DB::select($sql);

		if ($store_cd != '') {
			$sql = "
				select 
					store_channel
				from store
				where store_cd = '$store_cd'
			";

			$sc = DB::selectOne($sql);

			$sql = "
				select
					store_kind
					, store_kind_cd
					, use_yn
				from store_channel
				where dep = 2 and use_yn = 'Y' and store_channel_cd = '$sc->store_channel'

			";

			$sel_store_kind = DB::select($sql);
		} 
			
		$values = [
			"cmd"	=> $store_cd == '' ? "" : "update",
			"store"	=> $store,
			'store_img' => $store_img,
			'map_key' => $map_key,
			'store_types' => SLib::getCodes("STORE_TYPE"),
			'store_kinds' => SLib::getCodes("STORE_KIND"),
			'store_areas' => SLib::getCodes("STORE_AREA"),
			'grades' => SLib::getValidStoreGrades(),
			'prioritys' => SLib::getCodes("PRIORITY"),
			'store_match' => $store_match,
			'store_channel' => $store_channel,
			'store_kind' => $store_kind,
			'sel_store_kind' => $sel_store_kind
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
		$pre_store_cd = $request->input('pre_store_cd');
		$image 		= $request->file('file');
		// $y 			= $request->input('y');
		// $x 			= $request->input('x');
		// $map_code 	= $y.','.$x;

		$sale_place_match_yn = $request->input('sale_place_match_yn');
		if ($sale_place_match_yn == 'Y') {
			$com_id = $request->input('com_id');
		} else {
			$com_id = '';
		}

		$account_yn = $request->input('account_yn', 'N');
		if ($account_yn == 'Y') {
			$grade_cd = $request->input('grade_cd');
		} else {
			$grade_cd = null;
		}

		try {
			DB::beginTransaction();

			$where	= [
				'store_cd'	=> $pre_store_cd
			];

			$values	= [
				'store_cd' 				=> $request->input('store_cd'),
				'store_nm'				=> $request->input('store_nm'),
				'store_nm_s'			=> $request->input('store_nm_s')??'',
				// 'store_type'			=> $request->input('store_type'),
				'store_channel'			=> $request->input('store_channel'),
				'store_channel_kind'	=> $request->input('store_channel_kind'),
				'store_area'			=> $request->input('store_area'),
				'grade_cd'				=> $grade_cd,
				'zipcode'				=> $request->input('zipcode'),
				'addr1'					=> $request->input('addr1'),
				'addr2'					=> $request->input('addr2'),
				'phone'					=> $request->input('phone'),
				'fax'					=> $request->input('fax'),
				'mobile'				=> $request->input('mobile'),
				'manager_nm'			=> $request->input('manager_nm'),
				'manager_mobile'		=> $request->input('manager_mobile'),
				'email'					=> $request->input('email'),
				'fee'					=> $request->input('fee'),
				'sale_fee'				=> $request->input('sale_fee'),
				'md_manage_yn'			=> $request->input('md_manage_yn'),
				'bank_no'				=> $request->input('bank_no'),
				'bank_nm'				=> $request->input('bank_nm'),
				'depositor'				=> $request->input('depositor'),
				'cash'					=> $request->input('cash'),
				'warranty'				=> $request->input('warranty'),
				'deposit_coll'			=> $request->input('deposit_coll'),
				'etc_coll'				=> $request->input('etc_coll'),
				'deposit_cash'			=> $request->input('deposit_cash'),
				'loss_rate'				=> $request->input('loss_rate'),
				'sdate'					=> $request->input('sdate'),
				'edate'					=> $request->input('edate'),
				'use_yn'				=> $request->input('use_yn'),
				'ipgo_yn'				=> $request->input('ipgo_yn'),
				'vat_yn'				=> $request->input('vat_yn'),
				'biz_no'				=> $request->input('biz_no'),
				'biz_nm'				=> $request->input('biz_nm'),
				'biz_ceo'				=> $request->input('biz_ceo'),
				'biz_zipcode'			=> $request->input('biz_zipcode'),
				'biz_addr1'				=> $request->input('biz_addr1'),
				'biz_addr2'				=> $request->input('biz_addr2'),
				'biz_uptae'				=> $request->input('biz_uptae'),
				'biz_upjong'			=> $request->input('biz_upjong'),
				'manage_type'			=> $request->input('manage_type'),
				'exp_manage_yn'			=> $request->input('exp_manage_yn'),
				'priority'				=> $request->input('priority'),
				'competitor_yn'			=> $request->input('competitor_yn'),
				'pos_yn'				=> $request->input('pos_yn'),
				'ostore_stock_yn'		=> $request->input('ostore_stock_yn'),
				'store_stock_yn'		=> $request->input('store_stock_yn'),
				'sale_dist_yn'			=> $request->input('sale_dist_yn'),
				'rt_yn'					=> $request->input('rt_yn'),
				'point_in_yn'			=> $request->input('point_in_yn', 'N'),
				'point_ratio'			=> $request->input('point_ratio'),
				'point_out_yn'			=> $request->input('point_out_yn'),
				'com_id'				=> $com_id,
				'reg_date'				=> now(),
				'mod_date'				=> now(),
				'admin_id'				=> $id,
				'map_code'				=> $request->input('map_code'),
				'open_month_stock_yn'	=> $request->input('open_month_stock_yn'),
				'sale_place_match_yn' 	=> $sale_place_match_yn,
				'account_yn' 			=> $account_yn,				
			];
			
			DB::table('store')->updateOrInsert($where, $values);
			

			// 이미지 저장
			$base_path = "/images/std02";

			if (!Storage::disk('public')->exists($base_path)) {
				Storage::disk('public')->makeDirectory($base_path);
			}

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
					$imgSize = getimagesize($ig);
					$imageType = "";
					if ($imgSize[2] == 2) {
						$imageType = "jpg";
					}
					$imageType2 = $imgSize['mime'];

					if ($imageType == "jpg" && $imageType2 == 'image/jpeg') {
						$file_name = sprintf("%s_%s.jpg", $store_cd, "$cnt");
						$save_file = sprintf("%s/%s", $base_path, $file_name);

						$path = Storage::disk('public')->putFileAs($base_path, $ig, $file_name);

						if ($file_name != "") {
							$size = filesize($path);
							if ($size > 2048000) {
								return response()->json(["code" => 201]);
							}
						}

						$insert_values = [
							'img_url' => $save_file,
							'store_cd' => $store_cd,
							'seq' => $cnt,
							'rt' => now(),
							'admin_id' => $id
						];
						DB::table('store_img')->insert($insert_values);
						$last_seq++;

					} else {
						$msg = "매장이미지 중 'jpg'형식이 아닌 파일이 존재합니다.";
						return response()->json(["code" => 400, 'msg' => $msg]);
					}
				}
				
			}
			
			DB::commit();
			return response()->json(["code" => $code, "msg" => $msg, "store_cd" => $request->input('store_cd')]);
		} catch(Exception $e) {
			DB::rollback();
			$msg = $e->getMessage();
			
			return response()->json(["code" => 500, 'msg' => $msg]);
			
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

	public function charge($store_cd) {

		$sql = "
			select
				store_nm
				, store_cd
			from store
			where store_cd = '$store_cd'
		";

		$store = DB::selectOne($sql);

		$values = [
			'store_nm' => $store->store_nm,
			'store_cd' => $store->store_cd
		];

		return view( Config::get('shop.store.view') . '/standard/std02_charge',$values);

	}

	public function charge_search(Request $request) {
		$store_cd = $request->input("store_cd");

		$sql = "
			select 
				sf.idx, 
				cd.code_id as pr_code_cd, 
				cd.code_val as pr_code_nm, 
				s.store_cd, 
				sf.store_fee,
				s.grade_cd,
				sg.idx as grade_idx,
				sg.name as grade_nm,
				sf.sdate, 
				sf.edate, 
				sf.comment, 
				sf.use_yn
			from code cd
				inner join store s on s.store_cd = '$store_cd'
				left outer join store_fee sf
					on cd.code_id = sf.pr_code and sf.store_cd = s.store_cd and sf.idx in (select max(idx) from store_fee where store_cd = '$store_cd' group by pr_code)
				left outer join store_grade sg 
					on sg.grade_cd = s.grade_cd 
					and concat(sg.sdate, '-01 00:00:00') <= date_format(now(), '%Y-%m-%d 00:00:00') 
					and concat(sg.edate, '-31 23:59:59') >= date_format(now(), '%Y-%m-%d 00:00:00')			
			where cd.code_kind_cd = 'PR_CODE' and cd.use_yn = 'Y'
			order by cd.code_seq
		";

		$result = DB::select($sql);

		return response()->json([
			"head" => [
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1
			],
			"body" => $result
		]);


	}

	//판매채널 셀렉트값이 변경되면 해당 판매채널의 매장구분을 가져오는 코드
	public function change_store_channel(Request $request) {

		$store_channel = $request->input('store_channel');

		try {
			DB::beginTransaction();
			$sql = "
					select 
						store_kind_cd
						, store_kind
					from store_channel
					where store_channel_cd = '$store_channel' and dep = 2 and use_yn = 'Y' 
					order by seq asc
                ";
			$store_kind = DB::select($sql);

			DB::commit();
			$code = 200;
			$msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, 'store_kind' => $store_kind]);
	}


	//판매채널 셀렉트값이 변경되면 해당 판매채널의 매장구분을 가져오는 코드
	public function change_store_channel_multi(Request $request) {

		$store_channel = $request->input('store_channel');
		$store_kinds = [];

		try {
			DB::beginTransaction();
			foreach ($store_channel as $sc) {
				$sql = "
						select 
							store_kind_cd
							, store_kind
						from store_channel
						where store_channel_cd = '$sc' and dep = 2 and use_yn = 'Y' 
						order by seq asc
					";
				$store_kind = DB::select($sql);
				$store_kinds = array_merge($store_kinds, $store_kind);
			}
			DB::commit();
			$code = 200;
			$msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, 'store_kind' => $store_kind, 'store_kinds' => $store_kinds]);
	}

	public function change_store_channel_kind(Request $request) {

		$store_channel_kind = $request->input('store_channel_kind');

        try {
            DB::beginTransaction();
                $sql = "
					select
						store_cd
						, store_nm
					from store
					where store_channel_kind = '$store_channel_kind' and use_yn = 'Y'
                ";
            $stores = DB::select($sql);

			DB::commit();
            $code = 200;
            $msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg, 'stores' => $stores]);
	}

	public function create_store_cd(Request $request) {

		$store_code = $request->input('store_code');

        try {
            DB::beginTransaction();
                $sql = "
					select 
						count(*) as cnt
					from store
					where store_cd like '$store_code%'
					
                ";
            $cnt = DB::selectOne($sql);

			DB::commit();
            $code = 200;
            $msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg, "cnt" => $cnt->cnt]);
	}


}
