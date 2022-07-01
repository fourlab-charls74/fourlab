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

class std02Controller extends Controller
{
	public function index() {

		$values = [
			'store_types'	=> SLib::getCodes("STORE_TYPE"),	// 매장구분
			'store_kinds'	=> SLib::getCodes("STORE_KIND")		// 매장종류
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
				c.code_val as com_type_nm,
				d.code_val as store_kind_nm
			from store a
			left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
			left outer join code d on d.code_kind_cd = 'store_kind' and d.code_id = a.store_kind
			where 1=1 $where
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

	public function show()
	{
		$values = [
			'cmd'			=> '',
			'store_types'	=> SLib::getCodes("STORE_TYPE"),
			'store_kinds'	=> SLib::getCodes("STORE_KIND"),
			'store_areas'	=> SLib::getCodes("STORE_AREA"),
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

	public function view($com_id)
	{

		//매장구분
		$sql		= " 
			select 
				* from __tmp_code 
			where 
			code_kind_cd = 'com_type' and use_yn = 'Y' order by code_seq 
		";
		$com_types	= DB::select($sql);

		//매장종류
		$sql		= " 
			select 
				* from __tmp_code 
			where 
			code_kind_cd = 'store_kind' and use_yn = 'Y' order by code_seq 
		";
		$store_kinds	= DB::select($sql);

		//출고우선순위
		$sql		= " 
			select 
			* from __tmp_code 
			where 
			code_kind_cd = 'priority' and use_yn = 'Y' order by code_seq 
		";
		$prioritys	= DB::select($sql);

		$sql	= "
			select
				a.*, b.*
			from __tmp_store a
			left outer join __tmp_store_info b on a.com_id = b.com_id
			where
				a.com_id = :com_id
		";
		$data	= DB::selectOne($sql, ['com_id' => $com_id]);

		$values = [
			'com_types'		=> $com_types,
			'store_kinds'	=> $store_kinds,
			'prioritys'		=> $prioritys,
			"com_id"		=> $com_id,
			'data'			=> $data
		];

		return view( Config::get('shop.head.view') . '/xmd/code/code02_view',$values);
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
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/xmd/code/code02/%s", $_FILES['file']['name']);
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
				$data		= (array)$datas[$i];
	
				$com_type			= "";
				$com_type_nm		= $data["com_type_nm"];
				$com_id				= $data["com_id"];
				$com_nm				= $data["com_nm"];
				$store_kind			= "";
				$store_kind_nm		= $data["store_kind_nm"];
				$phone				= $data["phone"];
				$mobile				= $data["mobile"];
				$fax				= $data["fax"];
				$zipcode			= $data["zipcode"];
				$addr				= $data["addr"];
				$sdate				= $data["sdate"];
				$edate				= $data["edate"];
				$manager_nm			= $data["manager_nm"];
				$manager_sdate		= $data["manager_sdate"];
				$manager_edate		= $data["manager_edate"];
				$manager_deposit	= Lib::uncm($data["manager_deposit"]);
				$manager_fee		= Lib::uncm($data["manager_fee"]);
				$manager_sfee		= Lib::uncm($data["manager_sfee"]);
				$deposit_cash		= Lib::uncm($data["deposit_cash"]);
				$deposit_coll		= Lib::uncm($data["deposit_coll"]);
				$interior_cost		= Lib::uncm($data["interior_cost"]);
				$interior_burden	= Lib::uncm($data["interior_burden"]);
				$fee				= Lib::uncm($data["fee"]);
				$sale_fee			= $data["sale_fee"];
				$use_yn				= ($data["use_yn"] == "T")?"Y":"N";

				if( $com_type_nm != "" ){
					$query		= " select code_id as com_type from __tmp_code where code_kind_cd = 'com_type' and use_yn = 'Y' and code_val = :com_type_nm ";
					$row		= DB::selectOne($query, ['com_type_nm' => $com_type_nm]);
					$com_type	= $row->com_type;
				}

				if( $store_kind_nm != "" ){
					$query		= " select code_id as store_kind from __tmp_code where code_kind_cd = 'store_kind' and use_yn = 'Y' and code_val = :store_kind_nm ";
					$row		= DB::selectOne($query, ['store_kind_nm' => $store_kind_nm]);
					$store_kind	= $row->store_kind;
				}
	
				$query	= " select count(*) as cnt from __tmp_store where com_id = :com_id ";
				$row	= DB::selectOne($query, ['com_id' => $com_id]);

				$sql_data	= [
					'com_id'			=> $com_id, 
					'com_nm'			=> $com_nm, 
					'com_type'			=> $com_type, 
					'store_kind'		=> $store_kind, 
					'phone'				=> $phone, 
					'mobile'			=> $mobile, 
					'fax'				=> $fax, 
					'zipcode'			=> $zipcode, 
					'addr'				=> $addr, 
					'sdate'				=> $sdate, 
					'edate'				=> $edate, 
					'manager_nm'		=> $manager_nm, 
					'manager_sdate'		=> $manager_sdate, 
					'manager_edate'		=> $manager_edate, 
					'manager_deposit'	=> $manager_deposit, 
					'manager_fee'		=> $manager_fee, 
					'manager_sfee'		=> $manager_sfee, 
					'deposit_cash'		=> $deposit_cash, 
					'deposit_coll'		=> $deposit_coll, 
					'interior_cost'		=> $interior_cost, 
					'interior_burden'	=> $interior_burden, 
					'fee'				=> $fee, 
					'sale_fee'			=> $sale_fee, 
					'use_yn'			=> $use_yn, 
					'admin_id'			=> $id, 
					'admin_nm'			=> $name
				];
	
				if( $row->cnt == 0 ){
					$sql	= "
						insert into __tmp_store( com_id, com_nm, com_type, store_kind, phone, mobile, fax, zipcode, addr, sdate, edate, manager_nm, manager_sdate, manager_edate, manager_deposit, manager_fee, manager_sfee, deposit_cash, deposit_coll, interior_cost, interior_burden, fee, sale_fee, use_yn, rt, admin_id, admin_nm )
						values ( :com_id, :com_nm, :com_type, :store_kind, :phone, :mobile, :fax, :zipcode, :addr, :sdate, :edate, :manager_nm, :manager_sdate, :manager_edate, :manager_deposit, :manager_fee, :manager_sfee, :deposit_cash, :deposit_coll, :interior_cost, :interior_burden, :fee, :sale_fee, :use_yn, now(), :admin_id, :admin_nm )
					";
					DB::insert($sql, $sql_data);
				}
				else{
					$sql	= "
						update __tmp_store set
							com_nm			= :com_nm, 
							com_type		= :com_type, 
							store_kind		= :store_kind, 
							phone			= :phone, 
							mobile			= :mobile, 
							fax				= :fax, 
							zipcode			= :zipcode, 
							addr			= :addr, 
							sdate			= :sdate, 
							edate			= :edate, 
							manager_nm		= :manager_nm, 
							manager_sdate	= :manager_sdate, 
							manager_edate	= :manager_edate, 
							manager_deposit	= :manager_deposit, 
							manager_fee		= :manager_fee, 
							manager_sfee	= :manager_sfee, 
							deposit_cash	= :deposit_cash, 
							deposit_coll	= :deposit_coll, 
							interior_cost	= :interior_cost, 
							interior_burden	= :interior_burden, 
							fee				= :fee, 
							sale_fee		= :sale_fee, 
							use_yn			= :use_yn,
							admin_id		= :admin_id,
							admin_nm		= :admin_nm,
							ut				= now()
						where
							com_id	= :com_id
					";
					DB::update($sql, $sql_data);
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

	public function store_update($com_id, Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$store_data	= [
			'com_id'			=> $com_id,
			'com_type'			=> $request->input('com_type'),
			'com_nm'			=> $request->input("com_nm"),
			'store_kind'		=> $request->input('store_kind_nm'),
			'phone'				=> $request->input("phone"),
			'mobile'			=> $request->input("mobile"),
			'fax'				=> $request->input("fax"),
			'zipcode'			=> $request->input("zipcode"),
			'addr'				=> $request->input("addr"),
			'sdate'				=> $request->input("sdate"),
			'edate'				=> $request->input("edate"),
			'manager_nm'		=> $request->input("manager_nm"),
			'manager_sdate'		=> $request->input("manager_sdate"),
			'manager_edate'		=> $request->input("manager_edate"),
			'manager_deposit'	=> $request->input("manager_deposit"),
			'manager_fee'		=> $request->input("manager_fee"),
			'manager_sfee'		=> $request->input("manager_sfee"),
			'deposit_cash'		=> $request->input("deposit_cash"),
			'deposit_coll'		=> $request->input("deposit_coll"),
			'interior_cost'		=> $request->input("interior_cost"),
			'interior_burden'	=> $request->input("interior_burden"),
			'fee'				=> $request->input("fee"),
			'sale_fee'			=> $request->input("sale_fee"),
			'use_yn'			=> $request->input("use_yn"),
			'admin_id'			=> $id, 
			'admin_nm'			=> $name,

			'biz_num'			=> $request->input("biz_num"),
			'biz_name'			=> $request->input("biz_name"),
			'biz_ceo'			=> $request->input("biz_ceo"),
			'biz_zipcode'		=> $request->input("biz_zipcode"),
			'biz_addr1'			=> $request->input("biz_addr1"),
			'biz_addr2'			=> $request->input("biz_addr2"),
			'biz_uptae'			=> $request->input("biz_uptae"),
			'biz_upjong'		=> $request->input("biz_upjong")
		];

		$store_info_data	= [
			'com_id'			=> $com_id,
			'manage_type'		=> $request->input("manage_type"),
			'exp_manage_yn'		=> $request->input("exp_manage_yn"),
			'priority'			=> $request->input("priority"),
			'ocompany_info_yn'	=> $request->input("ocompany_info_yn"),
			'pos_yn'			=> $request->input("pos_yn"),
			'ostore_stock_yn'	=> $request->input("ostore_stock_yn"),
			'sale_dist_yn'		=> $request->input("sale_dist_yn"),
			'rt_yn'				=> $request->input("rt_yn"),
			'rt_sdate'			=> $request->input("rt_sdate"),
			'point_in_yn'		=> $request->input("point_in_yn"),
			'point_out_yn'		=> $request->input("point_out_yn"),
			'unpaid_proc_type'	=> $request->input("unpaid_proc_type")
		];

        try 
		{
            DB::beginTransaction();

			$query	= " select count(*) as cnt from __tmp_store_info where com_id = :com_id ";
			$row	= DB::selectOne($query, ['com_id' => $com_id]);
	
			if( $row->cnt == 0 ){
				$sql	= "
					insert into __tmp_store_info( com_id, manage_type, exp_manage_yn, priority, ocompany_info_yn, pos_yn, ostore_stock_yn, sale_dist_yn, rt_yn, rt_sdate, point_in_yn, point_out_yn, unpaid_proc_type )
					values ( :com_id, :manage_type, :exp_manage_yn, :priority, :ocompany_info_yn, :pos_yn, :ostore_stock_yn, :sale_dist_yn, :rt_yn, :rt_sdate, :point_in_yn, :point_out_yn, :unpaid_proc_type )
				";
				DB::insert($sql, $store_info_data);
			}else{
				$sql	= "
					update __tmp_store_info set
						manage_type			= :manage_type, 
						exp_manage_yn		= :exp_manage_yn, 
						priority			= :priority, 
						ocompany_info_yn	= :ocompany_info_yn, 
						pos_yn				= :pos_yn, 
						ostore_stock_yn		= :ostore_stock_yn, 
						sale_dist_yn		= :sale_dist_yn, 
						rt_yn				= :rt_yn, 
						rt_sdate			= :rt_sdate, 
						point_in_yn			= :point_in_yn, 
						point_out_yn		= :point_out_yn, 
						unpaid_proc_type	= :unpaid_proc_type
					where
						com_id	= :com_id
				";
				DB::update($sql, $store_info_data);
			}
	
			$sql	= "
				update __tmp_store set
					com_nm			= :com_nm, 
					com_type		= :com_type, 
					store_kind		= :store_kind, 
					phone			= :phone, 
					mobile			= :mobile, 
					fax				= :fax, 
					zipcode			= :zipcode, 
					addr			= :addr, 
					sdate			= :sdate, 
					edate			= :edate, 
					manager_nm		= :manager_nm, 
					manager_sdate	= :manager_sdate, 
					manager_edate	= :manager_edate, 
					manager_deposit	= :manager_deposit, 
					manager_fee		= :manager_fee, 
					manager_sfee	= :manager_sfee, 
					deposit_cash	= :deposit_cash, 
					deposit_coll	= :deposit_coll, 
					interior_cost	= :interior_cost, 
					interior_burden	= :interior_burden, 
					fee				= :fee, 
					sale_fee		= :sale_fee, 
					use_yn			= :use_yn,
					admin_id		= :admin_id,
					admin_nm		= :admin_nm,
					ut				= now(),
	
					biz_num			= :biz_num,
					biz_name		= :biz_name,
					biz_ceo			= :biz_ceo,
					biz_zipcode		= :biz_zipcode,
					biz_addr1		= :biz_addr1,
					biz_addr2		= :biz_addr2,
					biz_uptae		= :biz_uptae,
					biz_upjong		= :biz_upjong
				where
					com_id	= :com_id
			";
			DB::update($sql, $store_data);
		
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
