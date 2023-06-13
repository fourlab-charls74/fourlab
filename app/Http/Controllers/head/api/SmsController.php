<?php

namespace App\Http\Controllers\head\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use App\Models\SMS;
use PDO;

class SmsController extends Controller
{
    public function index($type, Request $req){
        $conf = new Conf();

        $mutable = now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $s_phone = $req->input('phone', '');
        $s_name = $req->input('name', '');

        $ids = $req->input('ids', '');
        $users = [];
        if ($ids != '') {
            $ids = explode(',', $ids);
            $where = array_reduce($ids, function($a, $c) {
                $a .= " or user_id = '$c' ";
                return $a;
            }, '');
            $sql = "
                select user_id, name as s_name, phone as s_phone
                from member
                where 1<>1 $where
            ";
            $users = DB::select($sql);

            if (count($users) == 1) {
                $s_phone = $users[0]->s_phone;
                $s_name = $users[0]->s_name;
                $users = [];
            }
        }

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'name'          => $req->input('name', ''),
            'type'          => $type,
            'sms_yn'        => $conf->getConfigValue("sms","sms_yn"),
            'phone'         => $conf->getConfigValue("shop","phone"),
            's_phone'       => $s_phone,
            's_name'        => $s_name,
            'users'         => json_encode($users),
        ];

        return view( Config::get('shop.head.view') . "/common/sms_all", $values);
    }

    public function sendMsg(Request $req) {
		$data		= $req->data;
		$shop_tel	= $req->shop_tel;

        $conf = new Conf();
		$cfg_shop_name = $conf->getConfigValue("shop","name");

		$sms = new SMS([
            'admin_id' => Auth('head')->user()->id,
            'admin_nm' => Auth('head')->user()->name,
        ]);
        $name	= $data['name'];
        $mobile = $data['phone'];
        $msg	= $data['msg'];

        $msgarr = array(
            "shop_name" => $cfg_shop_name,
            "user_name" => $name,
        );

        $sms_msg = $sms->MsgReplace($msg, $msgarr);

        if($mobile != ""){
            //$sms->Send($sms_msg, $mobile, $name, $shop_tel);
			$sms->SendAligoSMS( $mobile, $sms_msg, $name );
			//$sms->Send( $sms_msg, $mobile, $name,$shop_tel);
        }
        return response()->json([
            "code" => "200"
        ]);
    }

    public function search(Request $req) {
		// 설정값 확인
        $conf = new Conf();
		$cfg_sms_sender = $conf->getConfig("sms", "sms_sender", "infobank");

		$sdate	= $req->input("sdate", date("Ymd"));
		$edate	= $req->input("edate", date("Ymd"));
		$phone	= $this->__replaceTel($req->input("phone"));
		$name	= $req->input("name");

        $page	= $req->input("page", 1);
		$page_size	= 500;

		if ($page < 1 or $page == "")	$page = 1;

        $total	= 0;
        $page_cnt	= 0;

		$where = "";

		if( $phone != "" )	$where .= " and a.receiver = '" . str_replace('-','',$phone) . "' ";
		if( $name != "" )	$where .= " and a.recvname = '$name' ";

		if( $page == 1 )
		{
			$sql	= "
				select
					count(*) as total
				from aligo_log a
				where a.regdate >= '$sdate' and  a.regdate < DATE_ADD('$edate', INTERVAL 1 DAY)
				$where
			";
			$row	= DB::selectOne($sql);
			$total	= $row->total;

			// 페이지 얻기
			$page_cnt	= (int)(( $total - 1) / $page_size ) + 1;
			$startno	= ( $page - 1 ) * $page_size;
		}
		else
		{
			$startno	= ( $page - 1 ) * $page_size;
		}

		$arr_header = array( "total" => $total, "page_cnt" => $page_cnt, "page" => $page );

		$sql = "
			select
				a.regdate, a.tpl_code, a.recvname, a.receiver, a.message
			from aligo_log a
			where 
				a.regdate >= '$sdate' and  a.regdate < DATE_ADD('$edate', INTERVAL 1 DAY)
				$where
			order by a.idx desc
			limit $startno, $page_size
		";

		$rows = DB::select($sql);

		foreach ($rows as $row)
		{
			if( $row->tpl_code != "SMS" )	$row->tpl_code = "카카오톡";
			else							$row->tpl_code = "SMS";
			/*
			if(isset($a_result_codes[$row->msg_rlt])){
				if($row->msg_rlt == "1" || $row->msg_rlt == "1000"){
					$row->msg_rlt = $a_result_codes[$row->msg_rlt];
				} else {
					$row->msg_rlt = sprintf("실패 - %s",$a_result_codes[$row->msg_rlt]);
				}
			} else {
				$row->msg_rlt = "실패";
			}
			*/
		}

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $rows
        ]);
    }

    /*
        Function: ReplaceTel
        전화번호 숫자에 '-' 넣는 함수

    Parameters:

            $tel - 전화번호

        Returns:

            String
    */
    private function __replaceTel($tel) {

        $tel = trim($tel);

        if(strpos($tel,"-") === false){

            $len = strlen($tel);

            if($len == 9){

                $patterns = array ("/(\d{2})(\d{3})(\d{4})/");
                $replace = array ("\\1-\\2-\\3");
                $tel =  preg_replace ($patterns, $replace,$tel);

            } else if($len == 10){

                if(substr($tel,0,2) == "02"){
                    $patterns = array ("/(\d{2})(\d{4})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                } else {
                    $patterns = array ("/(\d{3})(\d{3})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                }

            } else if($len == 11){

                if(substr($tel,0,4) == "0505"){
                    $patterns = array ("/(\d{4})(\d{3})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                } else {
                    $patterns = array ("/(\d{3})(\d{4})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                }

            } else if($len == 12){

                $patterns = array ("/(\d{4})(\d{4})(\d{4})/");
                $replace = array ("\\1-\\2-\\3");
                $tel =  preg_replace ($patterns, $replace,$tel);
            }
            return $tel;

        } else {
            return $tel;
        }
    }
}
