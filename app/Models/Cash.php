<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;

class Cash
{

	var $pg;
	var $pgobj;

	public function cash($pg = "kcp"){

		// 환경설정
		global $PG_OS;
		global $PG_NAME;
		global $PG_SITE_CD;
		global $PG_SITE_KEY;
		global $PG_HOME_DIR;
		global $PG_CONF_HOME_DIR;
		global $PG_CONF_PA_URL;

		// 현금영수증 관련 설정
		global $CASH_CONF_HOME_DIR;
		global $CASH_CONF_KEY_DIR;
		global $CASH_CONF_LOG_DIR;
		global $PG_CONF_USER_TYPE;

		$this->pg = $PG_NAME;

		if($this->pg == "kcp"){

			require_once $PG_HOME_DIR."/pp_cli_hub_lib.php";

			$this->pgobj = new kcpcash();
			$pgid = "";

			$this->setconfig(
				$PG_OS,
				$PG_CONF_HOME_DIR,
				$PG_CONF_PA_URL,
				$PG_SITE_CD,
				$PG_SITE_KEY,
				$CASH_CONF_HOME_DIR,
				$CASH_CONF_KEY_DIR,
				$CASH_CONF_LOG_DIR,
				$PG_CONF_USER_TYPE
			);

		} else if($this->pg == "inicis"){


		}

	}

	public function setconfig($ps_os,$dir,$url,$site_cd,$site_key,$cash_home_dir,$cash_key_dir,$cash_log_dir,$user_type) {
		$this->pgobj->setconfig($ps_os,$dir,$url,$site_cd,$site_key,$cash_home_dir,$cash_key_dir,$cash_log_dir,$user_type);
	}

	public function mod($conn, $param) {
		return $this->pgobj->mod($conn, $param);
	}
}

class kcpcash {

	var $ps_os = "";			// PG OS 환경
	var $home_dir = "";			// BIN 절대경로 입력
	var $log_level	= "3";		// 변경불가
	var $pa_url = "";			// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	var $pa_port = "8090";		// 포트번호 , 변경불가
	var $mode = 0;				// 변경불가

	// 현금영수증 관련 설정
	var $cash_home_dir = "";	// 현금영수증 BIN 절대경로 입력
	var $cash_key_dir = "";		// 현금영수증 KEY 절대경로 입력
	var $cash_log_dir = "";		// 현금영수증 LOG 절대경로 입력
	var $user_type = "PGNW";	// 현금영수증 PG 모드

	var $site_cd;
	var $site_key;

	public function setconfig($ps_os,$dir,$url,$site_cd,$site_key,$cash_home_dir,$cash_key_dir,$cash_log_dir,$user_type) {
		$this->pg_os = $ps_os;
		$this->home_dir = $dir;
		$this->pa_url = $url;
		$this->site_cd = $site_cd;
		$this->site_key = $site_key;
		$this->cash_home_dir = $cash_home_dir;
		$this->cash_key_dir = $cash_key_dir;
		$this->cash_log_dir = $cash_log_dir;
		$this->user_type = $user_type;
	}

	public function mod($conn, $param) {

		setlocale(LC_CTYPE, 'ko_KR.UTF-8');

		/* ============================================================================== */
		/* =   01. 요청 정보 설정                                                       = */
		/* = -------------------------------------------------------------------------- = */
		$req_tx     = GetValue($param, "req_tx");							// 요청 종류
		$trad_time  = GetValue($param, "trad_time");						// 원거래 시각
		/* = -------------------------------------------------------------------------- = */
		$ordr_idxx  = GetValue($param, "ordr_idxx");						// 주문 번호
		$buyr_name  = GetValue($param, "buyr_name");						// 주문자 이름
		$buyr_tel1  = GetValue($param, "buyr_tel1");						// 주문자 전화번호
		$buyr_mail  = GetValue($param, "buyr_mail");						// 주문자 E-Mail
		$good_name  = GetValue($param, "good_name");						// 상품 정보
		$comment    = GetValue($param, "comment");							// 비고
		/* = -------------------------------------------------------------------------- = */
		$corp_type     = GetValue($param, "corp_type");						// 사업장 구분
		$corp_tax_type = GetValue($param, "corp_tax_type");					// 과세/면세 구분
		$corp_tax_no   = GetValue($param, "corp_tax_no");					// 발행 사업자 번호
		$corp_nm       = GetValue($param, "corp_nm");						// 상호
		$corp_owner_nm = GetValue($param, "corp_owner_nm");					// 대표자명
		$corp_addr     = GetValue($param, "corp_addr");						// 사업장 주소
		$corp_telno    = GetValue($param, "corp_telno");					// 사업장 대표 연락처
		/* = -------------------------------------------------------------------------- = */
		$tr_code    = GetValue($param, "tr_code");							// 발행용도
		$id_info    = GetValue($param, "id_info");							// 신분확인 ID
		$amt_tot    = GetValue($param, "amt_tot");							// 거래금액 총 합
		$amt_sup    = GetValue($param, "amt_sup");							// 공급가액
		$amt_svc    = GetValue($param, "amt_svc");							// 봉사료
		$amt_tax    = GetValue($param, "amt_tax");							// 부가가치세
		/* = -------------------------------------------------------------------------- = */
		$mod_type   = GetValue($param, "mod_type");							// 변경 타입
		$mod_value  = GetValue($param, "mod_value");						// 변경 요청 거래번호
		$mod_gubn   = GetValue($param, "mod_gubn");							// 변경 요청 거래번호 구분
		$mod_mny    = GetValue($param, "mod_mny");							// 변경 요청 금액
		$rem_mny    = GetValue($param, "rem_mny");							// 변경처리 이전 금액
		/* = -------------------------------------------------------------------------- = */
		$cust_ip    = getenv( "REMOTE_ADDR" );								// 요청 IP
		/* ============================================================================== */

		// 관리자 정보
		$admin_id = GetValue($param, "admin_id");
		$admin_nm = GetValue($param, "admin_nm");

		/* ============================================================================== */
		/* =   02. 인스턴스 생성 및 초기화                                              = */
		/* = -------------------------------------------------------------------------- = */
		$c_PayPlus  = new C_PAYPLUS_CLI;
		$c_PayPlus->mf_clear();
		/* ============================================================================== */


		/* ============================================================================== */
		/* =   03. 처리 요청 정보 설정, 실행                                            = */
		/* = -------------------------------------------------------------------------- = */
		$rcpt_data_set = "";
		$corp_data_set = "";
		/* = -------------------------------------------------------------------------- = */
		/* =   03-1. 승인 요청                                                          = */
		/* = -------------------------------------------------------------------------- = */
			// 업체 환경 정보
			if ( $req_tx == "pay" )
			{
				$tx_cd = "07010000"; // 현금영수증 등록 요청

				// 현금영수증 정보
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "user_type",      $this->user_type );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "trad_time",      $trad_time        );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "tr_code",        $tr_code          );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "id_info",        $id_info          );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tot",        $amt_tot          );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_sup",        $amt_sup          );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_svc",        $amt_svc          );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tax",        $amt_tax          );
				$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_type",       "PAXX"            ); // 선 결제 서비스 구분(PABK - 계좌이체, PAVC - 가상계좌, PAXX - 기타)
				//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_trade_no",   $pay_trade_no ); // 결제 거래번호(PABK, PAVC일 경우 필수)
				//$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_tx_id",      $pay_tx_id    ); // 가상계좌 입금통보 TX_ID(PAVC일 경우 필수)

				// 주문 정보
				$c_PayPlus->mf_set_ordr_data( "ordr_idxx",  $ordr_idxx );
				$c_PayPlus->mf_set_ordr_data( "good_name",  $good_name );
				$c_PayPlus->mf_set_ordr_data( "buyr_name",  $buyr_name );
				$c_PayPlus->mf_set_ordr_data( "buyr_tel1",  $buyr_tel1 );
				$c_PayPlus->mf_set_ordr_data( "buyr_mail",  $buyr_mail );
				$c_PayPlus->mf_set_ordr_data( "comment",    $comment   );

				// 가맹점 정보
				$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_type",       $corp_type     );

				if ( $corp_type == "1" ) // 입점몰인 경우 판매상점 DATA 전문 생성
				{
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_tax_type",   $corp_tax_type );
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_tax_no",     $corp_tax_no   );
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_sell_tax_no",$corp_tax_no   );
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_nm",         $corp_nm       );
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_owner_nm",   $corp_owner_nm );
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_addr",       $corp_addr     );
					$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_telno",      $corp_telno    );
				}

				$c_PayPlus->mf_set_ordr_data( "rcpt_data", $rcpt_data_set );
				$c_PayPlus->mf_set_ordr_data( "corp_data", $corp_data_set );
			}

		/* = -------------------------------------------------------------------------- = */
		/* =   03-2. 취소 요청                                                          = */
		/* = -------------------------------------------------------------------------- = */
			else if ( $req_tx == "mod" )
			{
				if ( $mod_type == "STSQ" )
				{
					$tx_cd = "07030000"; // 조회 요청
				}
				else
				{
					$tx_cd = "07020000"; // 취소 요청
				}

				$c_PayPlus->mf_set_modx_data( "mod_type",   $mod_type   );      // 원거래 변경 요청 종류
				$c_PayPlus->mf_set_modx_data( "mod_value",  $mod_value  );
				$c_PayPlus->mf_set_modx_data( "mod_gubn",   $mod_gubn   );
				$c_PayPlus->mf_set_modx_data( "trad_time",  $trad_time  );

				if ( $mod_type == "STPC" ) // 부분취소
				{
					$c_PayPlus->mf_set_modx_data( "mod_mny",  $mod_mny  );
					$c_PayPlus->mf_set_modx_data( "rem_mny",  $rem_mny  );
				}
			}
		/* ============================================================================== */


		/* ============================================================================== */
		/* =   03-3. 실행                                                               = */
		/* ------------------------------------------------------------------------------ */
			if ( strlen($tx_cd) > 0 )
			{

				if($this->pg_os == "WIN") {
					$c_PayPlus->mf_do_tx( "",                $this->cash_home_dir, $this->site_cd,
										  $this->site_key,                $tx_cd,           "",
										  $this->pa_url,    $this->pa_port,  "payplus_cli_slib",
										  $ordr_idxx,        $cust_ip,         $this->log_level,
										  "",                "0", $this->cash_key_dir, $this->cash_log_dir );
				} else {
					$c_PayPlus->mf_do_tx( "",                $this->cash_home_dir, $this->site_cd,
										  $this->site_key,                $tx_cd,           "",
										  $this->pa_url,    $this->pa_port,  "payplus_cli_slib",
										  $ordr_idxx,        $cust_ip,         $this->log_level,
										  "",                "0" );
				}
			}
			else
			{
				$c_PayPlus->m_res_cd  = "9562";
				$c_PayPlus->m_res_msg = "연동 오류";
			}
			$res_cd  = $c_PayPlus->m_res_cd;                      // 결과 코드
			$res_msg = $c_PayPlus->m_res_msg;                     // 결과 메시지

		/* ============================================================================== */


		/* ============================================================================== */
		/* =   04. 승인 결과 처리                                                       = */
		/* = -------------------------------------------------------------------------- = */
			if ( $req_tx == "pay" )
			{
				if ( $res_cd == "0000" )
				{
					$cash_no    = $c_PayPlus->mf_get_res_data( "cash_no"    );       // 현금영수증 거래번호
					$receipt_no = $c_PayPlus->mf_get_res_data( "receipt_no" );       // 현금영수증 승인번호
					$app_time   = $c_PayPlus->mf_get_res_data( "app_time"   );       // 승인시간(YYYYMMDDhhmmss)
					$reg_stat   = $c_PayPlus->mf_get_res_data( "reg_stat"   );       // 등록 상태 코드
					$reg_desc   = $c_PayPlus->mf_get_res_data( "reg_desc"   );       // 등록 상태 설명

		/* = -------------------------------------------------------------------------- = */
		/* =   04-1. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
		/* = -------------------------------------------------------------------------- = */
		/* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
		/* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
		/* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
		/* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"     = */
		/* =         로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
		/* =         값을 세팅하시면 됩니다.)                                           = */
		/* = -------------------------------------------------------------------------- = */
					$bSucc = "";             // DB 작업 실패일 경우 "false" 로 세팅

					$conn->StartTrans();

					// 현금영수증 발행 내역 등록
					$sql = "
						insert into cash_history (
							`cash_no`, `ord_no`, `receipt_no`, `app_time`, `reg_stat`, `reg_desc`, `id_info`, `amt_tot`, `admin_id`, `admin_nm`, `rt`, `ut`
						) values (
							'$cash_no', '$ordr_idxx', '$receipt_no', '$trad_time', '$reg_stat', '$reg_desc', '$id_info', '$amt_tot', '$admin_id', '$admin_nm', now(), now()
						)
					";
					$conn->Execute($sql);

					// 현금영수증 발행여부 등록
					$sql = "
						update payment set
							cash_yn = 'Y'
							, cash_date = now()
						where ord_no = '$ordr_idxx'
					";
					$conn->Execute($sql);

					if($conn->CompleteTrans()){
					} else {
						$bSucc = "false";
					}

		/* = -------------------------------------------------------------------------- = */
		/* =   04-2. DB 작업 실패일 경우 자동 승인 취소                                 = */
		/* = -------------------------------------------------------------------------- = */
					if ( $bSucc == "false" )
					{
						$c_PayPlus->mf_clear();

						$tx_cd = "07020000"; // 취소 요청

						$c_PayPlus->mf_set_modx_data( "mod_type",  "STSC"     );                    // 원거래 변경 요청 종류
						$c_PayPlus->mf_set_modx_data( "mod_value", $cash_no   );
						$c_PayPlus->mf_set_modx_data( "mod_gubn",  "MG01"     );
						$c_PayPlus->mf_set_modx_data( "trad_time", $trad_time );

						if($PG_OS == "WIN") {
							$c_PayPlus->mf_do_tx( "",                $this->cash_home_dir, $this->site_cd,
												  $this->site_key,                $tx_cd,           "",
												  $this->pa_url,    $this->pa_port,  "payplus_cli_slib",
												  $ordr_idxx,        $cust_ip,         $this->log_level,
												  "",                "0", $this->cash_key_dir, $this->cash_log_dir );
						} else {
							$c_PayPlus->mf_do_tx( "",                $this->cash_home_dir, $this->site_cd,
												  $this->site_key,                $tx_cd,           "",
												  $this->pa_url,    $this->pa_port,  "payplus_cli_slib",
												  $ordr_idxx,        $cust_ip,         $this->log_level,
												  "",                "0" );
						}

						$res_cd  = $c_PayPlus->m_res_cd;
						$res_msg = $c_PayPlus->m_res_msg;
					}

				}    // End of [res_cd = "0000"]

		/* = -------------------------------------------------------------------------- = */
		/* =   04-3. 등록 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
		/* = -------------------------------------------------------------------------- = */
				else
				{
				}
			}
		/* ============================================================================== */


		/* ============================================================================== */
		/* =   05. 변경 결과 처리                                                       = */
		/* = -------------------------------------------------------------------------- = */
			else if ( $req_tx == "mod" )
			{
				if ( $res_cd == "0000" )
				{
					$cash_no    = $c_PayPlus->mf_get_res_data( "cash_no"    );       // 현금영수증 거래번호
					$receipt_no = $c_PayPlus->mf_get_res_data( "receipt_no" );       // 현금영수증 승인번호
					$app_time   = $c_PayPlus->mf_get_res_data( "app_time"   );       // 승인시간(YYYYMMDDhhmmss)
					$reg_stat   = $c_PayPlus->mf_get_res_data( "reg_stat"   );       // 등록 상태 코드
					$reg_desc   = $c_PayPlus->mf_get_res_data( "reg_desc"   );       // 등록 상태 설명
					$ordr_idxx  = Request("ORD_NO");
					if($mod_type == "STSC"){	// 취소
						$amt_tot = $rem_mny;
						$sql = "
							insert into cash_history (
								`cash_no`, `ord_no`, `receipt_no`, `cash_stat`, `app_time`, `reg_stat`, `reg_desc`, `id_info`, `amt_tot`, `admin_id`, `admin_nm`, `rt`, `ut`
							) values (
								'$cash_no', '$ordr_idxx', '$receipt_no', '$mod_type', '$trad_time', '$reg_stat', '$reg_desc', '$id_info', '$amt_tot', '$admin_id', '$admin_nm', now(), now()
							)
						";
						$conn->Execute($sql);
					} else if($mod_type == "STPC"){			// 부분취소
						$amt_tot = (int)($rem_mny) - (int)($mod_mny);
						$sql = "
							insert into cash_history (
								`cash_no`, `ord_no`, `receipt_no`, `cash_stat`, `app_time`, `reg_stat`, `reg_desc`, `id_info`, `amt_tot`, `admin_id`, `admin_nm`, `rt`, `ut`
							) values (
								'$cash_no', '$ordr_idxx', '$receipt_no', '$mod_type', '$trad_time', '$reg_stat', '$reg_desc', '$id_info', '$amt_tot', '$admin_id', '$admin_nm', now(), now()
							)
						";
						$conn->Execute($sql);
					}
				}

		/* = -------------------------------------------------------------------------- = */
		/* =   05-1. 변경 실패를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
		/* = -------------------------------------------------------------------------- = */
				else
				{
				}
			}
		/* ============================================================================== */


		/* ============================================================================== */
		/* =   06. 인스턴스 CleanUp                                                     = */
		/* = -------------------------------------------------------------------------- = */
		$c_PayPlus->mf_clear();
		/* ============================================================================== */

		return array($res_cd,$res_msg);
	}

}