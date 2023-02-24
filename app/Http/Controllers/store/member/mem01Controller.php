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
use Illuminate\Support\Facades\Storage;

//ini_set("upload_max_filesize", "50M");
//ini_set("post_max_size", "50M");
//ini_set("memory_limit", "128M");
//ini_set('max_execution_time', '60000');
//ini_set("max_input_time", 360000);

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
			$page_cnt = (int)(($total - 1)/$page_size) + 1;
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
				a.phone, a.mobile, a.email, a.point, a.store_nm,
				date_format(a.regdate,'%Y-%m-%d') as regdate,
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
		$store_no 	= Request("store_no");

		$sdate		= Request("sdate");
		$edate		= Request("edate");
		$last_sdate	= Request("last_sdate");
		$last_edate	= Request("last_edate");
		$ord_sdate	= Request("order_sdate");
		$ord_edate	= Request("order_edate");
		$mmdd		= Request("mmdd");
		$mail		= Request("mail");
		$mobile_chk	= Request("mobile_chk");
		$type		= Request("type");

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
		if($mobile != "")		$where .= " and a.mobile like '%$mobile%' ";
		if($sex != "")			$where .= " and a.sex = '$sex'";
		if($sdate != "")		$where .= " and a.regdate >= '$sdate' ";
		if($edate != "")		$where .= " and a.regdate < DATE_ADD('$edate', INTERVAL 1 DAY) ";
		if($last_sdate != "")	$where .= " and a.lastdate >= '$last_sdate' ";
		if($last_edate != "")	$where .= " and a.lastdate < DATE_ADD('$last_edate', INTERVAL 1 DAY) ";
		if($ord_sdate != "")	$where .= " and e.ord_date >= '$ord_sdate 00:00:00' ";
		if($ord_edate != "")	$where .= " and e.ord_date < DATE_ADD('$ord_edate 23:59:59', INTERVAL 1 DAY) ";
		if ( $store_no != "" ) {
			$where	.= " and (1!=1";
			foreach($store_no as $store_cd) {
				$where .= " or a.store_cd = '$store_cd' ";

			}
			$where	.= ")";
		}

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
		
		if($type != ""){
			$where .= " and a.type = '$type' ";
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
		$save_path = "data/store/member/mem01/";
		if (!Storage::disk('public')->exists($save_path)) {
			Storage::disk('public')->makeDirectory($save_path);
		}

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/member/mem01/%s", $_FILES['file']['name']);
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

        //try 
		//{
        //    DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data		= (array)$datas[$i];
	
				$user_id	= trim($data['user_code']);
				$user_pw	= "fjallraven";					//초기 비밀번호[???]
				$name		= trim($data['user_nm']);
				$group_code	= trim($data['grade_code']);			//01:일반회원, 02:실버회원, 03:골드회원, 04:VIP회원
				$group_no	= "13";							//13:일반회원, 15:실버회원, 16:골드회원, 17:VIP회원
				$group_nm	= trim($data['grade_nm']);
				$sex		= trim($data['sex']);
				$mobile		= trim($data['mobile']);
				$email		= trim($data['email']);
				$point		= Lib::uncm(trim($data['point']));
				$ord_amt	= Lib::uncm(trim($data['ord_amt']));
				$ord_cnt	= Lib::uncm(trim($data['ord_cnt']));
				$last_ord_date	= trim($data['last_ord_date']) . " 00:00:00";
				$store_nm	= trim($data['store_nm']);
				$regdate	= trim($data['rt']) . " 00:00:00";
				$birth_date	= trim($data['birth_date']);
				$zip		= trim($data['zip']);
				$addr		= trim($data['addr']);
				$addr2		= trim($data['addr2']);
				$memo		= trim($data['memo']);

				// 비밀번호 암호화
				$conf = new Conf();
				$encrypt_mode = $conf->getConfigValue("shop", "encrypt_mode");
				$encrypt_key = "";
				if ($encrypt_mode == "mhash") {
					$encrypt_key = $conf->getConfigValue("shop", "encrypt_key");
				}

				$enc_pwd = Lib::get_enc_hash($user_pw, $encrypt_mode, $encrypt_key);

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

				//고객 성별 매치
				if( $sex == '남' )		$sex = "M";
				else if( $sex == '여' )	$sex = "F";
				else					$sex = "";


				$rmobile	= strrev($mobile);
				$email_chk	= "N";

				//매장코드 생성
				$store_nm_l	= strpos($store_nm, "(");
				if($store_nm_l){
					$store_nm_org	= substr($store_nm, 0, $store_nm_l);
				}else{
					$store_nm_org	= $store_nm;
				}

				$store_cd	= "";
				$sql	= " select store_cd from store where store_nm = :store_nm ";
				//$store	= DB::selectOne($sql, ['store_nm' => $store_nm_org]);
				$store	= DB::selectOne($sql, ['store_nm' => $store_nm]);

				if($store != null)	$store_cd	= $store->store_cd;

				//생년월일
				$mm	= "";
				$dd	= "";
				if( $birth_date != "" ){
					$birth_date	= explode("-", $birth_date);

					$mm	= $birth_date[0];
					$dd	= $birth_date[1];
				}

				$where	= [
					'user_id'	=> $user_id
				];

				$values	= [
					'user_pw'	=> $enc_pwd,
					'name'		=> $name,
					'sex'		=> $sex,
					'email'		=> $email,
					'email_chk'	=> $email_chk,
					'zip'		=> $zip,
					'addr'		=> $addr,
					'addr2'		=> $addr2,
					'phone'		=> $mobile,
					'mobile'	=> $mobile,
					'rmobile'	=> $rmobile,
					'regdate'	=> $regdate,
					'point'		=> $point,
					'yn'		=> 'Y',
					'mm'		=> $mm,
					'dd'		=> $dd,
					'out_yn'	=> 'N',
					'memo'		=> $memo,
					'pwd_reset_yn'	=> 'N',
					'auth_type'	=> 'A',
					'auth_yn'	=> 'N',
					'site'		=> 'HEAD_OFFICE',
					'type'		=> 'B',
					'store_nm'	=> $store_nm,
					'store_cd'	=> $store_cd
				];

				//회원처리
				DB::table('member')->updateOrInsert($where, $values);


				//적립금 처리
				if($point > 0){
					$point_values	= [
						'ord_no'		=> '',
						'ord_opt_no'	=> '',
						'point_nm'		=> '기존 시스템 포인트 등록',
						'point'			=> $point,
						'admin_id'		=> 'system',
						'admin_nm'		=> '시스템',
						'regi_date'		=> now(),
						'point_st'		=> '적립',
						'point_kind'	=> '12',
						'point_status'	=> 'Y',
						'point_date'	=> now()
					];
	
					DB::table('point_list')->updateOrInsert($where, $point_values);
				}


				//member_group 처리
				$group_values	= [
					'group_no'	=> $group_no,
					'rt'		=> now(),
					'ut'		=> now()
				];

				DB::table('user_group_member')->updateOrInsert($where, $group_values);


				//member_stat 처리
				$stat_values	= [
					'ord_cnt'	=> $ord_cnt,
					'ord_amt'	=> $ord_amt,
					'ord_date'	=> $last_ord_date,
					'rt'		=> now(),
					'ut'		=> now()
				];

				DB::table('member_stat')->updateOrInsert($where, $stat_values);
			}
	
		//	DB::commit();
        //}
		//catch(Exception $e) 
		//{
        //    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}