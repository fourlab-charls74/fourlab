<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use App\Models\C_PP_CLI;

class Pay
{

	var $pg;
	var $pgobj;
	//private $pg;
	//private $pgobj;

	//public function pay($pg = "kcp"){
	function __construct($pg = "kcp") {
		// 환경설정
		/*
		global $PG_NAME;
		global $PG_SITE_CD;
		global $PG_SITE_KEY;
		global $PG_HOME_DIR;
		global $PG_CONF_HOME_DIR;
		global $PG_CONF_PA_URL;
		*/

		/* 테스트 완료 후 변경 예정 ceduce */
		$PG_NAME			= "kcp";
		$PG_SITE_CD			= "N3748";
		$PG_SITE_KEY		= "076y8wJlSApOXBeZ0cVgF6u__";
		$PG_CONF_HOME_DIR	= "/data1/wwwroot/fjallraven/pay/kcp/payplus";
		$PG_CONF_PA_URL		= "paygw.kcp.co.kr";
		

		$this->pg = $PG_NAME;

		if($this->pg == "kcp"){

			setlocale(LC_CTYPE, 'ko_KR.utf-8');

			$this->pgobj = new kcppay();
			$pgid = "";

			$this->setconfig(
				$PG_CONF_HOME_DIR,
				$PG_CONF_PA_URL,
				$PG_SITE_CD,
				$PG_SITE_KEY
			);
		} else if($this->pg == "inicis"){

			$this->pgobj = new inipay();

			$this->setconfig(
				$PG_CONF_HOME_DIR,
				$PG_CONF_PA_URL,
				$PG_SITE_CD,
				$PG_SITE_KEY
			);
		}
	}

	public function setconfig($dir,$url,$site_cd,$site_key) {
		$this->pgobj->setconfig($dir,$url,$site_cd,$site_key);
	}

	public function mod($type,$no,$ordno,$ip,$desc,$amt = 0,$pamt = 0) {
		return $this->pgobj->mod($type,$no,$ordno,$ip,$desc,$amt,$pamt);
	}

	public function mod_escrow( $type, $tno, $ordno, $cust_ip, $mod_desc, $a_param ) {
		return $this->pgobj->mod_escrow($type, $tno, $ordno, $cust_ip, $mod_desc, $a_param);
	}


	/*

	   public Function: cancelstate

	   부분취소 가능여부.

	   Parameters:

			type - 결제구분.
			no - 거래번호.
			card - 카드사.

	   Returns:

		  -  1 : 취소가능
		  -  2 : 부분취소 가능
		  - -1 : 취소불가

	   See Also:

	*/
	public function cancelstate($type,$no,$card = "") {
		/*
		global $PG_ISSUBCANCEL;
		global $PG_SUBAPPDATE;
		*/

		/* 테스트 완료 후 변경 예정 ceduce */
		$PG_ISSUBCANCEL	= "Y";
		$PG_SUBAPPDATE	= "20080509";


		if(empty($no)) return -1;
		$appdate = substr($no,0,8);

		if(($type & 2) == 2){		// 카드

			if($PG_ISSUBCANCEL == "Y" && $appdate > 0){
				if($card != "외환카드") return 2;
			}

			return 1;

		} else if(($type & 16) == 16){	// 계좌이체


			$now = date("Ymd");
			$pmonth = Lib::calcDate($now,"1M");

			// 한달 이전 승인자료 이면 취소 불가
			if($appdate <= $pmonth) return -1;

			if($PG_ISSUBCANCEL == "Y"){

				// 당일결제건 부분취소 불가
				//if($now != $appdate) return 2;
				return 2;
			}

			return 1;

		} else {

			if($PG_ISSUBCANCEL == "Y"){
				return 2;
			}


		}

		return 1;


	}
}

class kcppay {

	var $home_dir = "/data1/wwwroot/fjallraven/pay/kcp/payplus";			// BIN 절대경로 입력
	var $log_level	= "3";			// 변경불가
	var $pa_url = "paygw.kcp.co.kr";				// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	var $pa_port = "8090";		// 포트번호 , 변경불가
	var $mode = 0;					// 변경불가

	var $site_cd	= "N3748";
	var $site_key	= "076y8wJlSApOXBeZ0cVgF6u__";

	var $types = array(
			"AP"		=> "",				// 승인
			"SL"		=> "",				// 매입
			"CL"		=> "STSC",		// 승인취소
			"RN"		=> "RN07",		// 부분취소(카드)
      		"SC"		=> "STPC",		// 부분취소(카카오)
			"ST"		=> "STPA"			// 부분취소(계좌이체)
		);

	public function setconfig($dir,$url,$site_cd,$site_key) {
		$this->home_dir = $dir;
		$this->pa_url = $url;
		$this->site_cd = $site_cd;
		$this->site_key = $site_key;
	}

	public function mod($type,$tno,$ordno,$cust_ip,$mod_desc,$mod_mny,$rem_mny) {

		$c_PayPlus = new C_PP_CLI();

		$tran_cd = "00200000";
		$mod_type = $this->types[$type];

		$c_PayPlus->mf_set_modx_data( "tno",        $tno            );				// KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );       // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );			// 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );      // 변경 사유

		if( $mod_type == "RN07" || $mod_type == "STPC")	// 카드부분취소
		{
			$c_PayPlus->mf_set_modx_data("mod_mny",     $mod_mny   );		// 부분 매입 취소 금액
			$c_PayPlus->mf_set_modx_data("rem_mny",     $rem_mny   );			// 부분매입취소 전 남은 금액
		}
		else if( $mod_type == "STPA")	// 계좌이체 부분취소
		{
			$c_PayPlus->mf_set_modx_data("amount",     $mod_mny   );			// 부분 매입 취소 금액
			$c_PayPlus->mf_set_modx_data("rem_mny",     $rem_mny   );			// 부분매입취소 전 남은 금액
		}

		$c_PayPlus->mf_do_tx(
					"",	  // traceno
					$this->home_dir,$this->site_cd,$this->site_key,
					$tran_cd, "",
					$this->pa_url,$this->pa_port,  "payplus_cli_slib",
					$ordno, $cust_ip,
					$this->log_level, 0, $this->mode
				);

		//$tno       = $c_PayPlus->mf_get_res_data( "tno" );
		//$amount    = $c_PayPlus->mf_get_res_data( "amount" );

		$res_cd    = $c_PayPlus->m_res_cd;
		$res_msg   = $c_PayPlus->m_res_msg;

		//printf("%s %s",$res_cd,$res_msg);

		return array($res_cd,$res_msg);

	}

	public function mod_escrow( $type, $tno, $ordno, $cust_ip, $mod_desc, $a_param ) {

		$c_PayPlus = new C_PP_CLI();

		$tran_cd = "00200000";
		$mod_type = $type;

		$c_PayPlus->mf_set_modx_data( "tno",        $tno            );	// KCP 원거래 거래번호
		$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type       );  // 원거래 변경 요청 종류
		$c_PayPlus->mf_set_modx_data( "mod_ip",     $cust_ip        );	// 변경 요청자 IP
		$c_PayPlus->mf_set_modx_data( "mod_desc",   $mod_desc       );  // 변경 사유

		if ($mod_type == "STE1")                                                // 상태변경 타입이 [배송요청]인 경우
		{
			$c_PayPlus->mf_set_modx_data( "deli_numb",   $a_param[ "deli_numb" ] );          // 운송장 번호
			$c_PayPlus->mf_set_modx_data( "deli_corp",   $a_param[ "deli_corp" ] );          // 택배 업체명
		}
		else if ($mod_type == "STE2" || $mod_type == "STE4")                    // 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
		{
			if ($acnt_yn == "Y") // 상태변경시 계좌이체, 가상계좌 여부
			{
				$c_PayPlus->mf_set_modx_data( "refund_account",   $a_param[ "refund_account" ] );      // 환불수취계좌번호
				$c_PayPlus->mf_set_modx_data( "refund_nm",        $a_param[ "refund_nm"      ] );      // 환불수취계좌주명
				$c_PayPlus->mf_set_modx_data( "bank_code",        $a_param[ "bank_code"      ] );      // 환불수취은행코드
			}
		}

		$c_PayPlus->mf_do_tx(
					$trace_no = ""
					, $this->home_dir
					, $this->site_cd
					, $this->site_key
					, $tran_cd
					, ""
					, $this->pa_url
					, $this->pa_port
					, "payplus_cli_slib"
					, $ordno
					, $cust_ip
					, $this->log_level
					, 0
					, $this->mode
				);

		//$tno       = $c_PayPlus->mf_get_res_data( "tno" );
		//$amount    = $c_PayPlus->mf_get_res_data( "amount" );

		$res_cd    = $c_PayPlus->m_res_cd;
		$res_msg   = $c_PayPlus->m_res_msg;

		//printf("%s %s",$res_cd,$res_msg);

		return array($res_cd,$res_msg);
	}
}

class inipay {
	var $home_dir = "";			// BIN 절대경로 입력
	var $log_level	= "3";		// 변경불가
	var $pa_url = "";			//
	var $pa_port = "";			// 포트번호 , 변경불가
	var $mode = 0;				// 변경불가

	var $site_cd;
	var $site_key;
	var $key_dir;
	var $log_dir;
	var $os;

	var $types = array(
			"AP"		=> "",				// 승인
			"SL"		=> "",				// 매입
			"CL"		=> "STSC",		// 승인취소
			"CC"		=> "STE2",		// 매입취소
			"RN"		=> "RN07",		// 부분취소(카드)
			"ST"		=> "STPA"			// 부분취소(계좌이체)
		);

	public function inipay(){
		$this->payplus = new INIpay41();
	}

	public function setconfig($dir,$url,$site_cd,$site_key) {

		$this->home_dir = $dir;
		$this->pa_url = $url;
		$this->site_cd = $site_cd;
		$this->site_key = $site_key;

		$this->payplus->m_inipayHome = $this->home_dir;
		$this->payplus->m_subPgIp = "203.238.3.10"; 		// 고정 (절대 수정 불가)

		/**************************************************************************************************
		 * m_keyPw 는 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
		 * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
		 * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
		 * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
		 **************************************************************************************************/
		$this->payplus->m_keyPw = $site_key; 			// 키패스워드(상점아이디에 따라 변경)
		$this->payplus->m_debug = "true"; 				// 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$this->payplus->m_mid = $this->site_cd; 		// 상점아이디
	}

	public function mod($type,$tno,$ordno,$cust_ip,$mod_desc,$mod_mny,$rem_mny) {

		$this->payplus->m_type = "cancel"; 				// 고정 (절대 수정 불가)
		$this->payplus->m_pgId = "INIpay";

		$this->payplus->m_tid = $tno;
		$this->payplus->m_cancelMsg = $mod_desc;
		$this->payplus->startAction();

		if($this->payplus->m_resultCode == "00"){
			$res_cd    = "000";
		} else {
			$res_cd    = $this->payplus->m_resultCode;
		}
		$res_msg   = $this->payplus->m_resultMsg;

		//printf("%s %s",$res_cd,$res_msg);

		return array($res_cd,$res_msg);
	}
}
