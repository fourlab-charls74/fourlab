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
		}
		else if( $cfg_sms_sender == "youiwe" ) {
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

}
