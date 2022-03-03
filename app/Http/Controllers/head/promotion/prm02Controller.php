<?php

namespace App\Http\Controllers\head\promotion;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm02Controller extends Controller
{
  //
	public function index() {
		$mutable = Carbon::now();
		$sdate	= $mutable->sub(3, 'month')->format('Y-m-d');
		
		$pattypes = SLib::getCodes("PATTYPE");
		$patstates = SLib::getCodes("PATSTATE");

		$values = [
			'sdate' => $sdate,
			'edate' => date("Y-m-d"),
			'pattypes' => $pattypes,
			'patstates' => $patstates
		];

		return view( Config::get('shop.head.view') . '/promotion/prm02',$values);
	}

	public function search(Request $request){
		$sdate		= $request->input("sdate");
		$edate		= $request->input("edate");
		$comany_nm	= $request->input("comany_nm");
		$name		= $request->input("name");
		$type		= $request->input("type");
		$state		= $request->input("state");

		$where = "";
		if ($sdate != "") $where .= " and a.regi_date >= '$sdate' ";
		if ($edate != "") $where .= " and a.regi_date < date_add('$edate',interval 1 day) ";

		if ($comany_nm != "") $where .= " and a.company_nm like '%$comany_nm%' ";
		if ($name != "") $where .= " and a.name like '%$name%' ";
		if ($type != "") $where .= " and a.type = '$type' ";
		if ($state != "") $where .= " and a.state = '$state' ";
		$charset = "kor";

		$sql = "
			select 
				date_format(a.regi_date,'%Y.%m.%d') as regi_date,
				case when '$charset' = 'kor' then c.code_val else c.code_val_eng end as state,
				case when '$charset' = 'kor' then b.code_val else b.code_val_eng end as type,
				a.company_nm,a.name,email,a.phone,a.mobile,a.address,a.idx, url
			from cooperation a 
				inner join code b on b.code_kind_cd = 'PATTYPE' and a.type = b.code_id 
				inner join code c on c.code_kind_cd = 'PATSTATE' and a.state = c.code_id 
			where 1=1 $where
			order by idx desc
		";
		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
				"page" => -1,
				"page_cnt" => count($result),
				"page_total" => 1
			),
			"body" => $result
		]);

	}

	/*
		Function: Detail
		제휴,문의 상세내용
	*/

	function Detail($idx = ''){

		$sql = "
			select * from  cooperation where idx = '$idx'
		";

		$file_dir = "/data/partner/";

		$row = DB::selectOne($sql);
		if ($row) {
			$company_nm		= $row->company_nm;
			$name			= $row->name;
			$email			= $row->email;
			$phone			= $row->phone;
			$mobile			= $row->mobile;
			$address		= $row->address;
			$content		= nl2br($row->content);
			$ans			= $row->ans;
			$type			= $row->type;
			$state			= $row->state;
			$application	= $row->application;
			$file = $file_dir.rawurlencode($row->application);
			$code			= $row->category;
			/*
			$category = new CategoryMulti( $conn, "DISPLAY" );
			$category_nm = $category->Location( $code );
			*/
			$url			= $row->url;

			$sql = "select * from category where cat_type='DISPLAY' and d_cat_cd='$code'";
			$category = DB::selectOne($sql);
			$category_nm = "";
			if($category){
				$category_nm = $category->d_cat_nm;
			}
		}


		$pattypes = SLib::getCodes("PATTYPE");
		$patstates = SLib::getCodes("PATSTATE");

		//$smarty->display($view);
		$values = [
			"idx"			=> $idx,
			"company_nm"	=> $company_nm,
			"name"			=> $name,
			"email"			=> $email,
			"phone"			=> $phone,
			"mobile"		=> $mobile,
			"address"		=> $address,
			"content"		=> $content,
			"ans"			=> $ans,
			"type"			=> $type,
			"state"			=> $state,
			"application"	=> $application,
			"file"			=> $file,
			"category"		=> $category_nm,
			"url"			=> $url,
			"pattypes"		=> $pattypes,
			"patstates"		=> $patstates
		];
		return view( Config::get('shop.head.view') . '/promotion/prm02_show',$values);
	}

	/*
		Function: Edit
		제휴,문의 상태 변경
	*/

	public function store(Request $request){
		$idx	= request("idx");
		$type	= request("type");
		$state	= request("state");
		$ans	= request("ans");
		
		$return_code = 0;

		$sql = "
			update cooperation set
				type = '$type',
				state = '$state',
				ans = '$ans'
			where idx = '$idx'
		";
		/*
		echo $sql;
		echo "<br>";
		*/
		$update_item = [
			"type"	=> $type,
			"state"	=> $state,
			"ans"	=> $ans	
		];
		//print_r($update_item);
		
		try {
			DB::table('cooperation')
			->where('idx','=', $idx)
			->update($update_item);
			$return_code = 1;
		} catch(Exception $e){
			$return_code = 0;
		}

		return response()->json([
			"code" => 200,
			"return_code" => $return_code
		]);

	}

}
