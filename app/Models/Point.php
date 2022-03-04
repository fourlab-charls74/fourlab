<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use Exception;

class Point
{

	private $conn;
	private $user;

	private $point_kinds = array(
		"MEMBER" 		=> 1	// 회원가입
		,"ORDER" 		=> 2	// 주문
		,"GIFT" 		=> 3	// 상품권 사용
		,"CHANGE" 		=> 5	// 교환
		,"REFUND"		=> 6	// 환불
		,"CANCEL" 		=> 7	// 주문취소
		,"OVER_PAID"	=> 8	// 초과입금
		,"ALARM"		=> 9	// 재입고알리미 신청 , 미사용
		,"RECOMMAND"	=> 10	// 추천아이디
		,"REVIEW"		=> 11	// 상품후기작성
		,"ETC"			=> 12	// 기타
		,"EVENT"		=> 13	// 이벤트
	);

	private $point_kinds_str = array(
		"MEMBER" 	=> "회원가입",
		"ORDER" 	=> "주문",
		"CHANGE" 	=> "교환",
		"REFUND"	=> "환불",
		"CANCEL" 	=> "취소",
		"OVER_PAID"	=> "초과",
	);

	private $point_states = array(
		"USE" 	=> "사용"
		,"SAVE" => "적립"
	);

	private $point_msgs = array(
		"MEMBER"		=> "회원가입 축하"
		,"ORDER"		=> "주문결제로 인한 적립"
		,"GIFT"			=> "상품권 사용으로 인한 적립" 		// 미사용
		,"OVER_PAID"	=> "초과입금으로 인한 적립"			// 초과입금
		,"ALARM"		=> "재입고알리미 신청 인한 적립" 	// 미사용

		,"PAY_POINT"	=> "결제시 포인트 사용"				// 포인트 결제
		,"CHANGE"		=> "교환으로 인한 적립금 반환" 		// 교환
		,"REFUND"		=> "환불로 인한 적립금 반환"		// 환불
		,"CANCEL"		=> "주문취소로 인한 적립금 환원"	// 주문취소
		,"PAY"			=> "포인트 결제"					// 포인트 결제
		,"DEL_USER"		=> "회원탈퇴로 인한 포인트 차감"	// 탈퇴 시 포인트 차감
	);

	private $user_id;
	private $ord_no;
    private $ord_opt_no;	// 상품별 포인트 지급 변경 : 2009-01-06

	function __construct($user = [], $user_id = "" ){
		$this->user = $user;
		if($user_id != ""){
			$this->SetUserId($user_id);
		}
	}
	/*
		Function: SetUserId
		회원 아이디 설정

		Parameters:
			$user_id - 회원 아이디
	*/
	public function SetUserId( $user_id ){
		$this->user_id = $user_id;
	}

	/*
		Function: SetOrdNo
		주문 번호 설정

		Parameters:
			$ord_no - 주문 번호

		SeeAlso:
			<SetUserId>
	*/
	public function SetOrdNo( $ord_no ){

		$this->ord_no =  $ord_no;

		$sql = "
			select user_id
			from order_mst
			where ord_no = '$ord_no'
        ";

        $row = DB::selectOne($sql);

		if(!empty($row->user_id)){
			$user_id = $row->user_id;

			$this->SetUserId($user_id);
		}
    }

	/*
		Function: SetOrdOptNo
		회원 아이디 설정

		Parameters:
			$ord_opt_no - 주문일련번호
	*/
	public function SetOrdOptNo( $ord_opt_no ){
		$this->ord_opt_no = $ord_opt_no;
    }

	/*
		Function: Cancel
		주문으로 사용한 회원의 포인트 환원

		Parameters:
			$ord_no - 주문 번호
	*/
	public function Cancel( $point ){

		if(empty($this->ord_no)) throw new Exception("Do SetOrdNo() method!!");
		if(empty($this->user_id)) throw new Exception("Do SetUserId() or SetOrdNo() method!!");

		$this->__Plus($point, "CANCEL", "CANCEL");
    }

	/*
		Function: __Plus
		포인트 지급

		Parameters:
			$point - 지급 포인트
			$msg - 메시지 구분 ( $this->point_msgs 참조 )
			$kind - 포인트 종류
	*/
	private function __Plus( $point, $msg, $kind, $expire_day = "" ){
        if(empty($this->user_id) || $this->user_id == "비회원") return false;

        if($point > 0){

            $id = $this->user["id"];
            $name = $this->user["name"];

            $point_status = "Y";

            if($kind == "ORDER") $point_status = "N";

            $state = $this->point_states["SAVE"];
            $msg = (empty($this->point_msgs[$msg])) ? $msg : $this->point_msgs[$msg];
            $kind = (empty($this->point_kinds[$kind])) ? $kind : $this->point_kinds[$kind];

            $cnt = 0;
            if( $kind == "ORDER" || $kind == "2" ){
                $cnt = DB::table('point_list')
                    ->where("user_id",$this->user_id)
                    ->where("ord_no",$this->ord_no)
                    ->where("ord_opt_no",$this->ord_opt_no)
                    ->count();
            }

            if( $cnt == 0 ){
                $expire_yn = '';
                if(strlen($expire_day) == 8){
                    $expire_yn = "N";
                } else {
                    $expire_day = "";
                }

                DB::table('point_list')->insert([
                    'user_id' => $this->user_id,
                    'ord_no' => $this->ord_no,
                    'ord_opt_no' => $this->ord_opt_no,
                    'point_nm' => $msg,
                    'point' => $point,
                    'admin_id' => $id,
                    'admin_nm' => $name,
                    'point_st' => $state,
                    'point_kind' => $kind,
                    'point_status' => $point_status,
                    //'expire_day' => $expire_day,		사용안함
                    //'expire_yn' => $expire_yn,		사용안함
                    'regi_date' => DB::raw('now()'),
                    'point_date' => DB::raw('now()')
                ]);
            }

            if($point_status == "Y"){
                DB::table('member')
                    ->where("user_id",$this->user_id)
                    ->update([
                        'point' => DB::raw("point + $point")
                    ]);
            }
            return true;
        }
    }

	/*
		Function: Refund
		환불 포인트 차감

		Parameters:
			$ord_opt_no - 주문옵션번호
			$point - 포인트
			$state - 클레임 상태

	*/
	public function Refund( $ord_opt_no, $point, $state ){

		$state_key = ($state == "61") ? "REFUND":"CHANGE";

		$sql = "
			select ord_opt_no
			from order_opt_wonga
			where ord_opt_no = '$ord_opt_no' and ord_state = '$state'
		";
        $row = DB::selectOne($sql);

        if(empty($row->ord_opt_no)) return false;

        $point_status = $this->GetPointStatus( $ord_opt_no );

        if($point_status !== "Y") return false;

        DB::beginTransaction();

        $this->SetOrdOptNo($ord_opt_no);
        $this->__Minus( $point, $state_key, $state_key );

        DB::commit();

        return true;
    }

	/*
		Function: GetPointStatus
		포인트 상태 리턴

		Parameters:
			$ord_opt_no - 주문 일련번호
	*/
	public function GetPointStatus( $ord_opt_no ){
		$point_status = "";

		$sql = "
			select point_status from point_list
			where user_id = '$this->user_id' and ord_opt_no = '$ord_opt_no'
        ";

        $row = DB::selectOne($sql);

		if(!empty($row->point_status)){
			$point_status = $row->point_status;
        }

		return $point_status;
    }

	/*
		Function: __Minus
		포인트 차감

		Parameters:
			$point - 차감 포인트
			$msg - 메시지 구분 ( $this->point_msgs 참조 )
			$kind - 포인트 종류
	*/
	private function __Minus( $point, $msg, $kind ){

        if(empty($this->user_id) || $this->user_id == "비회원") return false;
        if($point <= 0) return false;

        $id = $this->user["id"];
        $name = $this->user["name"];

        $state = $this->point_states["USE"];
        $msg = (empty($this->point_msgs[$msg])) ? $msg : $this->point_msgs[$msg];
        $kind = (empty($this->point_kinds[$kind])) ? $kind: $this->point_kinds[$kind];

        $sql = "
            insert into point_list (
                user_id, ord_no, ord_opt_no, point_nm, point, admin_id, admin_nm, regi_date, point_st, point_kind, point_status, point_date
            ) values (
                '$this->user_id', '$this->ord_no', '$this->ord_opt_no', '$msg', '-$point', '$id', '$name', now(), '$state','$kind', 'Y', now()
            )
        ";

        DB::insert($sql);

        $sql = "
            update member set
                point = if( ( point - $point ) < 0, 0, ( point - $point ))
            where user_id = '$this->user_id'
        ";

        DB::update($sql);
        return true;
    }

	/*
		Function: Order
		주문 포인트 지급

		Parameters:
			$point - 포인트
	*/
	public function Order( $point ){
        try {

            $sql = /** @lang text */
                "
                select a.ord_opt_no, a.add_point 
                from order_opt a
                    inner join order_mst b on a.ord_no = b.ord_no
                    inner join member c on b.user_id = c.user_id
                where a.ord_no = '$this->ord_no'
                    and a.add_point > 0
                    and b.user_id <> ''
            ";
            $rows = DB::select($sql);
            foreach($rows as $row){
                $ord_opt_no = $row->ord_opt_no;
                $add_point = $row->add_point;
                $this->ord_opt_no = $ord_opt_no;
                $this->__Plus( $add_point, "ORDER", "ORDER" );
            }
            return true;
        } catch(Exception $e) {
            return false;
        }
	}

	/*
		Function: Admin
		관리자 포인트 관리

		Parameters:
			$point - 포인트
			$msg - 메시지 구분 ( $this->point_msgs 참조 )
			$kind - 포인트 종류
			$state - 사용/적립 구분
	*/
	public function Admin( $point, $msg, $kind, $state, $expire_day = ""){
		try {
			DB::beginTransaction();

			if( $this->point_states["USE"] ==  $state ){
				$this->__Minus($point, $msg, $kind);
			}else if($this->point_states["SAVE"] == $state){
				$this->__Plus($point, $msg, $kind,$expire_day);
			}

			DB::commit();

			return true;
		} catch(Exception $e) {
            DB::rollBack();
            return false;
        }
	}

	/*
		Function: DeleteUser
		회원탈퇴 시 포인트 차감
	*/
	public function DeleteUser(){
		$point	= 0;
		$msg	= "DEL_USER";
		$state	= "ETC";

		try {
			DB::beginTransaction();

			// 현재 가용포인트 조회
			$sql = "
				select point 
				from member
				where user_id = '$this->user_id'
			";
			$row = DB::selectOne($sql);
			$point = $row->point;

			// 포인트 차감
			$this->__Minus( $point, $msg, $state );

			DB::commit();
			return true;
		} catch(Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
