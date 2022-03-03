<?php

namespace App\Http\Controllers\head\member;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class mem05Controller extends Controller
{
	public function index(Request $request) {
		$sdate	= $request->input("sdate");
		$edate	= $request->input("edate");

		$today = date("Y-m-d");
		$sdate = ($sdate == "") ? date('Y-m-d', strtotime(-1 .'month')) : $sdate;
		$edate = ($edate == "") ? $today : $edate;
		
		if($edate == ""){
			$edate = $today;
		}


		$point_st_items = SLib::getCodes("G_POINT_ST");
		$point_type_items = SLib::getCodes("G_POINT_TYPE");
		

		$values = [
			'edate' => $edate,
            'sdate' => $sdate,
			'point_st_items'	=> $point_st_items,
			'point_type_items'	=> $point_type_items
		];
		return view( Config::get('shop.head.view') . '/member/mem05',$values);
	}

	public function search(Request $request){
		$edate			= $request->input("edate");
		$sdate			= $request->input("sdate");
		$user_id		= $request->input("user_id");
		$point_st		= $request->input("point_st");
		$point_kind		= $request->input("point_kind");
		$ord_no			= $request->input("ord_no");
		$point_status	= $request->input("point_status");
		$point_nm		= $request->input("point_nm");

		$where = "";
		if($point_status != "") $where .= " and p.point_status = '$point_status' ";
		if($user_id !="") $where .= " and p.user_id = '$user_id' ";
		if($point_st !="") $where .= " and p.point_st = '$point_st' ";
		if($point_kind != "") $where .= " and p.point_kind = '$point_kind' ";
		if($point_nm != "") $where .= " and p.point_nm like '%$point_nm%' ";

		$page = request("page",1);
		if ($page < 1 or $page == "") $page = 1;

		$limit			= $request->input("limit",100);
		$ord_field		= $request->input("ord_field","p.point_date");
		$ord			= $request->input("ord","desc");

		$page_size = $limit;
		$str_order_by = sprintf(" order by %s %s ",$ord_field,$ord);

		$a_status = array("Y"=>"지급","N"=>"대기");

		// 갯수 얻기
		$sql = "
			select
				count(*) as total
			from point_list p
				inner join member m on p.user_id = m.user_id and m.out_yn = 'N'
			where
				p.point_date >= '$sdate'
				and p.point_date < DATE_ADD('$edate', INTERVAL 1 DAY)
				$where
		";
		//debugSQL($sql);
		//exit;
		$row = DB::selectOne($sql);
		$data_cnt = $row->total;

		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}

		if($limit == -1){
			$where_limit = "";
		} else {
			$where_limit = " limit $startno, $page_size ";
		}

		$sql = "
			select
				p.no, p.point_st, cd.code_val as point_kind, p.point_nm, p.point, p.user_id,  m.name,
				p.ord_no, p.ord_opt_no, p.point_status, p.point_date, p.admin_id
			from point_list p
				inner join member m on p.user_id = m.user_id and m.out_yn = 'N'
				inner join code cd on p.point_kind = cd.code_id and cd.code_kind_cd = 'G_POINT_TYPE'
			where
				p.point_date >= '$sdate'
				and p.point_date < DATE_ADD('$edate', INTERVAL 1 DAY)
				$where
			$str_order_by
			$where_limit
		";
		//$rs = $conn->Execute($sql);
		$result = DB::select($sql);
		$point_list = array();
		foreach($result as $row){
			array_push($point_list,
				array(
					"no"				=> $row->no,
					"point_st"			=> $row->point_st,
					"point_kind"		=> $row->point_kind,
					"point_nm"			=> $row->point_nm,
					"point"	 			=> $row->point,
					"user_id"			=> $row->user_id,
					"name"				=> $row->name,
					"ord_no"			=> $row->ord_no,
					"ord_opt_no"		=> $row->ord_opt_no,
					"point_status"		=> $a_status[$row->point_status],
					"point_date"		=> $row->point_date,
					"admin_id"			=> $row->admin_id,
				)
			);
			//$row["point_status"] = $a_status[$row["point_status"]];
		} // end while

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($point_list),
				"page_total" => $page_cnt
			),
			"body" => $point_list
		]);

	}
}
