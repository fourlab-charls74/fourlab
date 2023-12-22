<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;
use App\Models\SMS_youiwe;
use Exception;

class SMS
{

	// private $user;

	private $sms_id;
	private $sms_callback;

	private $msg_states = array(
		"1" => "전송 대기"
		,"2" => "결과 대기"
		,"3" => "발송 완료"
	);

	// public function __construct( $user ) {
	// 	$this->user = $user;
    // }

	/**
	 * 발송문구 치환
	 *
	 * @param String $msg 발송문구
	 * @param Array $array 치환값
	 * @return String 치환된 문구
	 */
	public function MsgReplace( $msg, $replace )
	{
		$patterns = array();
		$replacement = array();

		foreach ( $replace as $key => $val)
		{
			$patterns[] = sprintf("/\[%s\]/",$key);
			$replacement[] = sprintf("%s",$val);
		}
		$replace_msg = preg_replace($patterns, $replacement, $msg);

		return $replace_msg;
    }

	/**
	 * SMS발송
	 *
	 * @param String $msg 메시지
	 * @param String $phone 수신자전화번호
	 * @param String $name 수신자명
	 * @param String $callback 전송자전화번호
	 * @return boolean 결과
	 */
	function Send( $msg, $phone, $name, $callback = "" ) {

		// 알리고로 리다이렉트
		// 2023-06-13
		$this->SendAligoSMS( $phone, $msg, $name,$callback = '1566-8911' );
		exit;

		$tr_phone = str_replace("-","",$phone);

        $conf = new Conf();
		$cfg_sms_sender = $conf->getConfig("sms", "sms_sender", "infobank");

		if($callback == ""){
			$callback = $conf->getConfig("shop","phone");
		}

		if( $cfg_sms_sender == "infobank" )
		{
			// InfoBank
			$sql = "
				insert into imds.em_smt_tran (
					date_client_req, content, callback, service_type, broadcast_yn, msg_status,	recipient_num, tr_etc1, tr_etc2
				) values(
					sysdate(), '$msg', '$callback', '0', 'N', '1', '$tr_phone', '$phone', '$name'
				)
            ";

            DB::insert($sql);
			
		}else if( $cfg_sms_sender == "youiwe" ) {
            // 수행시간 확인
            $time_start = microtime(true);

            $C_WEB_HOME = $_SERVER["DOCUMENT_ROOT"];
            if ($C_WEB_HOME == "") {
                $C_WEB_HOME = "/data1/wwwroot/bizest_smart/html";
            }
            // include_once(public_path() . '\nusoap_youiwe.php');

            $snd_number = str_replace("-", "", $callback);                    // 보내는 사람 번호를 받음
            $rcv_number = str_replace("-", "", $phone);                        // 받는 사람 번호를 받음
            $sms_content = $msg;                        //전송 내용을 받음

            //******고객님 접속 정보************
            $sms_id = "netpx";            //고객님께서 부여 받으신 sms_id
            $sms_pwd = "maria40022!";       //고객님께서 부여 받으신 sms_pwd
            //**********************************
            $webService = "http://webservice.youiwe.co.kr/SMS.v.6/ServiceSMS.asmx?WSDL";

            $date_client_req = date("Y-m-d H:i:s");

            //SMS 객체 생성
            $sms = new SMS_youiwe($webService);
            // 즉시 전송으로 구성하실경우
            $result = $sms->SendSMS($sms_id, $sms_pwd, $snd_number, $rcv_number, $sms_content);// 5개의 인자로 함수를 호출합니다.

            //dd($result);

            // 수행시간 확인
            $time_end = microtime(true);
            $elapsed_time = $time_end - $time_start;

            $request_uri = $_SERVER["REQUEST_URI"];

            // youiwe
            $sql = "
				insert into imds.em_smt_log (
					date_client_req, content, callback, recipient_num, date_mt_sent, mt_report_code_ib, tr_etc1, tr_etc2, tr_etc3, tr_etc4
				) values (
					'$date_client_req', '$sms_content', '$callback', '$rcv_number', now(), '$result', '$phone', '$name', '$elapsed_time', '$request_uri'
				)
            ";

            DB::insert($sql);
        }
		
		return ( $result ) ? 1 : 0;
    }

	public function SendKakao( $template_code , $phone, $name, $sms_msg, $replace_data, $req_dt, $btnarr) {

		try {
            $conf = new Conf();
            $sender_key = $conf->getConfigValue("kakao","sender_key");

            $button_type = "";
            $button_info = "";

            $tr_phone = str_replace("-","",$phone);
            //$msg 는 알림톡 수신이 되지 않은 경우, 대신 발송될 sms 문구
            $sql = /** @lang text */
                "
                select content, sms_content
                from kakao_template
                where tmpcode = '$template_code'
            ";
            $row = DB::selectOne($sql);

            $content = $row->content;
            $sms_content = $row->sms_content;

            $kakao_msg = $this->MsgReplace( $content, $replace_data );
            $sms_content = $this->MsgReplace( $sms_content, $replace_data );
            if($sms_msg == ""){
                $sms_msg = $sms_content;
            }

            if(!empty($btnarr)){
                $button_type = $btnarr['BUTTON_TYPE'];
                $button_info = $btnarr['BUTTON_INFO'];
            }

            DB::table('kakaomsg.ata_mmt_tran')->insert([
                'date_client_req' => DB::raw('now()'),
                'subject' => '',
                'content' => $kakao_msg,
                'msg_status' => '1',
                'recipient_num' => $tr_phone,
                'msg_type' => '1008',
                'sender_key' => $sender_key,
                'template_code' => $template_code,
                'etc_text_1' => $sms_msg,
                'etc_text_2' => $name,
                'kko_btn_type' => $button_type,
                'kko_btn_info' => $button_info
            ]);
            return 1;
        } catch (Exception $e) {
		    echo $e->getMessage();
            return 0;
        }
	}

	public function SendAligoSMS( $phone, $msg, $name,$callback = '1566-8911' )
	{
		$apikey		= "ytzme8l0zjqg17dej17phjm6cn6phugf";
		$userid		= "alpeninter";
		$sender		= $callback;
		$phone		= str_replace("-", "", $phone);

		/**************** 문자전송하기 예제 필독항목 ******************/
		// 동일내용의 문자내용을 다수에게 동시 전송하실 수 있습니다
		// 대량전송시에는 반드시 컴마분기하여 1천건씩 설정 후 이용하시기 바랍니다. (1건씩 반복하여 전송하시면 초당 10~20건정도 발송되며 컨텍팅이 지연될 수 있습니다.)
		// 전화번호별 내용이 각각 다른 문자를 다수에게 보내실 경우에는 send 가 아닌 send_mass(예제:curl_send_mass.html)를 이용하시기 바랍니다.

		/****************** 인증정보 시작 ******************/
		$sms_url		= "https://apis.aligo.in/send/";		// 전송요청 URL
		$sms['user_id']	= $userid;								// SMS 아이디
		$sms['key']		= $apikey;								//인증키
		/****************** 인증정보 끝 ********************/

		/****************** 전송정보 설정시작 ****************/
		$sms['msg']			= stripslashes(iconv("UTF-8", "UTF-8", $msg));					// 메세지 내용 : utf-8로 치환이 가능한 문자열만 사용하실 수 있습니다. (이모지 사용불가능)
		$sms['receiver']	= $phone;								// 수신번호
		$sms['destination']	= $phone . "|" . iconv("UTF-8", "UTF-8", $name);					// 수신인 %고객명% 치환
		$sms['sender']		= $sender;								// 발신번호
		$sms['rdate']		= "";									// 예약일자 - 20161004 : 2016-10-04일기준
		$sms['rtime']		= "";									// 예약시간 - 1930 : 오후 7시30분
		$sms['testmode_yn']	= "";									// Y 인경우 실제문자 전송X , 자동취소(환불) 처리
		$sms['title']		= "";									//  LMS, MMS 제목 (미입력시 본문중 44Byte 또는 엔터 구분자 첫라인)
		// $sms['image']	= '/tmp/pic_57f358af08cf7_sms_.jpg';	// MMS 이미지 파일 위치 (저장된 경로)
		$sms['msg_type']	= "LMS";								//  SMS, LMS, MMS등 메세지 타입을 지정
		// ※ msg_type 미지정시 글자수/그림유무가 판단되어 자동변환됩니다. 단, 개행문자/특수문자등이 2Byte로 처리되어 SMS 가 LMS로 처리될 가능성이 존재하므로 반드시 msg_type을 지정하여 사용하시기 바랍니다.
		/****************** 전송정보 설정끝 ***************/

		/*****/
		$host_info	= explode("/", $sms_url);
		$port		= $host_info[0] == 'https:' ? 443 : 80;

		$oCurl		= curl_init();
		curl_setopt($oCurl, CURLOPT_PORT, $port);
		curl_setopt($oCurl, CURLOPT_URL, $sms_url);
		curl_setopt($oCurl, CURLOPT_POST, 1);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$ret = curl_exec($oCurl);
		curl_close($oCurl);

		//echo $ret;
		$retArr = json_decode($ret); // 결과배열
		// print_r($retArr); // Response 출력 (연동작업시 확인용)

		//로그 데이터 저장
		$sql = "
			insert into aligo_log (
				apikey, userid, token, senderkey, tpl_code, sender, receiver, recvname, subject, message, button, regdate
			) values (
				:apikey, :userid, :token, :senderkey, :tpl_code, :sender, :receiver, :recvname, :subject, :message, :button, now()
			)
		";
		DB::insert($sql,
			[
				"apikey"	=> $apikey,
				"userid"	=> $userid,
				"token"		=> "",
				"senderkey"	=> "",
				"tpl_code"	=> "SMS",
				"sender"	=> $sender,
				"receiver"	=> $phone,
				"recvname"	=> $name,
				"subject"	=> "",
				"message"	=> $msg,
				"button"	=> ""
			]
		);

		if( $retArr->result_code == "1" )	return "1";
		else								return "0";

		/**** Response 항목 안내 ****/
		// result_code : 전송성공유무 (성공:1 / 실패: -100 부터 -999)
		// message : success (성공시) / reserved (예약성공시) / 그외 (실패상세사유가 포함됩니다)
		// msg_id : 메세지 고유ID = 고유값을 반드시 기록해 놓으셔야 sms_list API를 통해 전화번호별 성공/실패 유무를 확인하실 수 있습니다
		// error_cnt : 에러갯수 = receiver 에 포함된 전화번호중 문자전송이 실패한 갯수
		// success_cnt : 성공갯수 = 이동통신사에 전송요청된 갯수
		// msg_type : 전송된 메세지 타입 = SMS / LMS / MMS (보내신 타입과 다른경우 로그로 기록하여 확인하셔야 합니다)
		/**** Response 예문 끝 ****/
	}

}
