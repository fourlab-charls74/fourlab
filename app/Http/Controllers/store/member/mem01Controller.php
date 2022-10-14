<?php

namespace App\Http\Controllers\store\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use App\Models\Point;
use Exception;

class mem01Controller extends Controller
{
	public function index($type='') {
		$values = [
			'mail'		=> SLib::getCodes('S_MAIL'),
			'mobile'	=> SLib::getCodes('G_MOBILE'),
			'sex'		=> SLib::getCodes('G_SEX_TYPE'),
			'age'		=> SLib::getCodes('G_AGE'),
			'yn'		=> SLib::getCodes('G_YN'),
			'auth_type'	=> SLib::getCodes('G_AUTH_TYPE'),
			'type'		=> $type,
			'today'		=> date("md"),
			'layout'	=> $type ? 'head_skote.layouts.master-without-nav' : 'head_skote.layouts.app',
		];

		$sql = " select group_no as id, group_nm as val from user_group order by group_no ";
		$values['groups'] = DB::select($sql);

		$sql = "select com_id as code_id,com_nm as code_val from company where com_type = '4' and site_yn = 'Y'";
		$values['sites'] = DB::select($sql);

		return view( Config::get('shop.store.view') . '/member/mem01', $values);
	}

	public function search(Request $req) {
		$cond = $this->get_condition($req);

		$where = $cond[0];
		$order_by = $cond[1];
		$join = $cond[2];

		$page = $req->input("page", 1);
		$page_size	= $req->input("limit", 100);

		if ($page < 1 or $page == "") $page = 1;

		$total = 0;
		$page_cnt = 0;

		if ($page == 1) {
			$sql = "
				select
					count(*) as cnt
				from
					member a
					$join
					-- left outer join code d ON d.code_kind_cd = 'G_SEX_TYPE' AND a.sex = d.code_id
					-- left outer join member_stat e ON a.user_id = e.user_id
				where 1=1 and out_yn <> 'I' $where
			";
			$row = DB::selectOne($sql);
			$total = $row->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($total - 1)/$page_size) + 1;
			$startno = ($page - 1) * $page_size;
		} else {
			$startno = ($page - 1) * $page_size;
		}

		$sql = $this->get_user_sql($where, $order_by, $join, $startno, $page_size);

		$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page);


		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => $arr_header,
			"body" => $result
		]);
	}

	private function get_user_sql($where, $order_by, $join, $startno = 0, $page_size = 0, $use_group = false) {

		$group_sql = "";

		if ($use_group) {
			$group_sql = "
				,(
					select group_concat(ug.group_nm order by ug.point_ratio desc separator ',')
					from user_group_member ugm
					inner join user_group ug on ug.group_no = ugm.group_no
					where ugm.user_id = a.user_id
				) as `group`
			";
		}

		$sql = "
			select
				'' as chkbox, a.user_id, a.name,
				d.code_val as sex, concat(ifnull(a.yyyy, ''),ifnull(a.mm, ''),ifnull(a.dd, '')) as birth_day,
				ifnull(a.jumin1, '') as jumin,
				a.phone, a.mobile, a.email, a.point,
				date_format(a.regdate,'%y%m%d') as regdate,
				a.lastdate as lastdate, a.visit_cnt,
				a.auth_type, f.code_val as auth_type_str, a.auth_yn,
				e.ord_date, e.ord_cnt, e.ord_amt,
				a.email_chk, a.mobile_chk, a.yn, ifnull(cm.com_nm,a.site) as site,
				a.name_chk, a.addr, a.zip
				${group_sql}
			from
				member a
				$join
				left outer join code d on d.code_kind_cd = 'G_SEX_TYPE' and a.sex = d.code_id
				left outer join member_stat e on a.user_id = e.user_id
				left outer join code f on f.code_kind_cd = 'G_AUTH_TYPE' and a.auth_type = f.code_id
				left outer join company cm on cm.com_type = 4 and a.site = cm.com_id
			where 1=1 and out_yn <> 'I'
				$where
				$order_by
			limit $startno, $page_size
		";

		return $sql;
	}

	private function get_condition(Request $req) {

		$user_id	= Request("user_ids");
		$name		= Request("name");
		$yn			= Request("yn");
		$jumin		= Request("jumin");
		$phone		= Request("phone");
		$mobile		= Request("mobile");
		$sex		= Request("sex");
		$age		= Request("age");
		$user_group	= Request("user_group");

		$sdate		= Request("sdate");
		$edate		= Request("edate");
		$last_sdate	= Request("last_sdate");
		$last_edate	= Request("last_edate");
		$ord_sdate	= Request("order_sdate");
		$ord_edate	= Request("order_edate");
		$mmdd		= Request("mmdd");
		$mail		= Request("mail");
		$mobile_chk	= Request("mobile_chk");

		$fr_ord_amt	= Lib::uncm(Request("cond_amt_from"));
		$to_ord_amt	= Lib::uncm(Request("cond_amt_to"));
		$fr_ord_cnt	= Lib::uncm(Request("cond_cnt_from"));
		$to_ord_cnt	= Lib::uncm(Request("cond_cnt_to"));

		$birth_sdate = Request("birth_sdate");
		$birth_edate = Request("birth_edate");

		// 인증
		$auth_type	= Request("auth_type");
		$auth_yn	= Request("auth_yn");
		$site 	    = Request("site");

		$mobile_yn	= Request("mobile_yn"); // 모바일 주문 여부
		$app_yn		= Request("app_yn"); // 앱 주문 여부

		$where		= "";

		// ag grid copy & paste 추가
		$user_id	= preg_replace("/\s/",",",$user_id);
		$user_id	= preg_replace("/,,/",",",$user_id);
		$user_id	= preg_replace("/\t/",",",$user_id);
		$user_id	= preg_replace("/\n/",",",$user_id);

		if($user_id !=""){
			$ids = explode(",",$user_id);
			if(count($ids) > 1){
				if(count($ids) > 300) array_splice($ids,300);
				for($i=0;$i<count($ids);$i++){
					$ids[$i] = sprintf("'%s'",$ids[$i]);
				}
				$in_ids = join(",",$ids);
				$where .= " and a.user_id in ( $in_ids ) ";
			} else {
				$where .= " and a.user_id = '$user_id' ";
			}
		}

		if($name != "")			$where .= " and a.name = '$name' ";

		if( $yn != "" ){
			$yn	= ( $yn == "Y" )?"Y":"";
			$where .= " and a.yn = '$yn' ";
		}

		if($jumin != "")		$where .= " and a.jumin1 = '$jumin' ";
		if($phone != "")		$where .= " and a.phone = '$phone' ";
		if($mobile != "")		$where .= " and a.mobile = '$mobile' ";
		if($sex != "")			$where .= " and a.sex = '$sex'";
		if($sdate != "")		$where .= " and a.regdate >= '$sdate' ";
		if($edate != "")		$where .= " and a.regdate < DATE_ADD('$edate', INTERVAL 1 DAY) ";
		if($last_sdate != "")	$where .= " and a.lastdate >= '$last_sdate' ";
		if($last_edate != "")	$where .= " and a.lastdate < DATE_ADD('$last_edate', INTERVAL 1 DAY) ";
		if($ord_sdate != "")	$where .= " and e.ord_date >= '$ord_sdate 00:00:00' ";
		if($ord_edate != "")	$where .= " and e.ord_date < DATE_ADD('$ord_edate 23:59:59', INTERVAL 1 DAY) ";

		if($mail != "")			$where .= " and a.email_chk = '$mail' ";
		if($mobile_chk != "")	$where .= " and a.mobile_chk = '$mobile_chk' ";

		if($fr_ord_amt != "")	$where .= " and e.ord_amt >= '$fr_ord_amt' ";
		if($to_ord_amt != "")	$where .= " and e.ord_amt < '$to_ord_amt' ";
		if($fr_ord_cnt != "")	$where .= " and e.ord_cnt >= '$fr_ord_cnt' ";
		if($to_ord_cnt != "")	$where .= " and e.ord_cnt < '$to_ord_cnt' ";
		if($birth_sdate != "")	$where .= " and a.yyyy >= '$birth_sdate' ";
		if($birth_edate != "")	$where .= " and a.yyyy <= $birth_edate";

		if($age != "") {
			if($age == '10')	$where .= " and  a.yyyy > YEAR(CURDATE())-7 and a.yyyy <= YEAR(CURDATE()) ";	// 초등학생미만
			if($age == '11')	$where .= " and  a.yyyy >= YEAR(CURDATE())-12 and a.yyyy <= YEAR(CURDATE())-7 ";	// 초등학생
			if($age == '12')	$where .= " and  a.yyyy >= YEAR(CURDATE())-15 and a.yyyy <= YEAR(CURDATE())-13 ";	// 중학생
			if($age == '13')	$where .= " and  a.yyyy >= YEAR(CURDATE())-18 and a.yyyy <= YEAR(CURDATE())-16 ";	// 고등학생
			if($age == '20')	$where .= " and  a.yyyy >= YEAR(CURDATE())-28 and a.yyyy <= YEAR(CURDATE())-19 ";	// 20대
			if($age == '30')	$where .= " and  a.yyyy >= YEAR(CURDATE())-38 and a.yyyy <= YEAR(CURDATE())-29 ";	// 30대
			if($age == '40')	$where .= " and  a.yyyy >= YEAR(CURDATE())-48 and a.yyyy <= YEAR(CURDATE())-39 ";	// 40대
			if($age == '50')	$where .= " and  a.yyyy >= YEAR(CURDATE())-58 and a.yyyy <= YEAR(CURDATE())-49 ";	// 50대
			if($age == '60')	$where .= " and  a.yyyy <= YEAR(CURDATE())-59 ";									// 60대이상
		}

		if($mmdd != ""){
			$mm = substr($mmdd,0,2);
			if($mm != ""){
				$where .= " and a.mm = '$mm' ";
			}
			$dd = substr($mmdd,2,2);
			if($dd != ""){
				$where .= " and a.dd = '$dd' ";
			}
		}

		if($auth_type != ""){
			$where .= " and a.auth_type = '$auth_type' ";
		}

		if($auth_yn != ""){
			$where .= " and a.auth_yn = '$auth_yn' ";
		}

		if($site != ""){
			$where .= " and a.site = '$site' ";
		}

		$join = "";
		if($user_group != ""){
			$join = " inner join user_group_member b on a.user_id = b.user_id ";
			$join .= " inner join user_group c on b.group_no = c.group_no and c.group_no in ($user_group) ";
		}

		$page = Request("page",1);
		if ($page < 1 or $page == "") $page = 1;

		$page_size	= Request("limit", 100);
		$ord_field	= Request("ord_field","a.user_id");
		$ord		= Request("ord","asc");

		$str_order_by = sprintf(" order by %s %s ",$ord_field,$ord);
		return [$where, $str_order_by, $join];
	}

	public function batch(){
		$values = [];

		return view( Config::get('shop.store.view') . '/member/mem01_batch', $values);
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
			$file = sprintf("data/store/mem01/%s", $_FILES['file']['name']);
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
	
				$user_id	= $data['user_code'];
				$name		= $data['user_nm'];
				$group_code	= $data['grade_code'];			//01:일반회원, 02:실버회원, 03:골드회원, 04:VIP회원
				$group_no	= "13";							//13:일반회원, 15:실버회원, 16:골드회원, 17:VIP회원
				$group_nm	= $data['group_nm'];
				$sex		= $data['sex'];
				$mobile		= $data['mobile'];
				$email		= $data['email'];
				$point		= Lib::uncm($data['point']);
				$ord_amt	= Lib::uncm($data['ord_amt']);
				$ord_cnt	= Lib::uncm($data['ord_cnt']);
				$last_ord_date	= $data['last_ord_date'] . " 00:00:00";
				$store_nm	= $data['store_nm'];
				$regdate	= $data['rt'];
				$birth_date	= $data['birth_date'];
				$zip		= $data['zip'];
				$addr		= $data['addr'];
				$addr2		= $data['addr2'];
				$memo		= $data['memo'];

				//고객 등급 매치
				switch ($group_code) {
					case '02':
						$group_no = "15"; break;
					case '03':
						$group_no = "16"; break;
					case '04':
						$group_no = "17"; break;
					default:
						$group_no = "13";
				}

				$rmobile	= strrev($mobile);
				$email_chk	= "N";

				//매장코드 생성


				//회원처리
				//적립금 처리
				//member_stat 처리



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

}