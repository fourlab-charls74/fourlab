<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use Exception;

class Coupon
{
	/**
	 * Class construct
	 * @param $conn
	 * @param $user
	 * @return unknown_type
	 */
	function __construct($user=[])
	{
		$this->user = $user;
    }

	/**
	 * 쿠폰 기본 정보 입력
	 * @param $data
	 * @return Integer
	 */
	function setCouponInfo($data)
	{
		try{
			DB::beginTransaction();

			$id = DB::table('coupon')->insertGetId([
				'coupon_nm'			=> $data['coupon_nm'], 
				'coupon_type'		=> $data['coupon_type'], 
				'coupon_img'		=> $data['coupon_img'],
				'coupon_url'		=> $data['coupon_url'], 
				'coupon_pub_kind'	=> $data['coupon_pub_kind'], 
				'pub_fr_date'		=> $data['pub_fr_date'], 
				'pub_to_date'		=> $data['pub_to_date'], 
				'use_fr_date'		=> $data['use_fr_date'],
				'use_to_date'		=> $data['use_to_date'], 
				'pub_dup_yn'		=> $data['pub_dup_yn'], 
                'pub_type'			=>$data['coupon_type'] === "E" ? $data['pub_type'] : '',
                'pub_day' 			=>$data['coupon_type'] === "E" && $data['pub_type'] === 'D' ? $data['pub_day'] : ($data['coupon_type'] === "E" && $data['pub_type'] === 'W' ? $data['pub_dayofweek'] : ''),
				'serial_yn'			=> $data['serial_yn'],
				'serial_dup_yn'		=> $data['serial_dup_yn'], 
				'coupon_apply'		=> $data['coupon_apply'],
				'coupon_amt_kind'	=> $data['coupon_amt_kind'],
				'coupon_amt'		=> $data['coupon_amt'],
				'coupon_per'		=> $data['coupon_per'],
				'price_yn'			=> $data['price_yn'],
				'low_price'			=> $data['low_price'],
				'high_price'		=> $data['high_price'],
				'pub_cnt'			=> $data['pub_cnt'],
				'use_yn'			=> $data['use_yn'], 
				'admin_id'			=> $data['admin_id'],
				'regi_date'			=> now(),
				//'pub_time'			=> $data['pub_time'],

				// 20220318 모델 주석 수정
				'use_date_type'		=> $data['use_date_type'],
				'use_date_alarm_yn'	=> $data['use_date_alarm_yn'], 
				'use_date_alarm_day'	=> $data['use_date_alarm_day'], 
				'use_date'			=> $data['use_date']
			]);

			DB::commit();

			return $id;
		}catch(Exception $e){
			DB::rollback();
			return -1;
		}
	}

	/**
	 * 쿠폰 기본 정보 수정
	 * @param $data
	 * @return Boolean
	 */
	function modCouponInfo($data)
	{
        try{
            DB::beginTransaction();
    
            DB::table('coupon')->where('coupon_no', $data['coupon_no'])->update([
                'coupon_nm'			=>$data['coupon_nm'],
                'coupon_type'		=>$data['coupon_type'],
                'coupon_pub_kind'	=>$data['coupon_pub_kind'],
                'pub_fr_date'		=>$data['pub_fr_date'],
                'pub_to_date'		=>$data['pub_to_date'],
                'use_fr_date'		=>$data['use_fr_date'],
                'use_to_date'		=>$data['use_to_date'],
                'pub_dup_yn'		=>$data['pub_dup_yn'],
                'pub_type'			=>$data['coupon_type'] === "E" ? $data['pub_type'] : '',
                'pub_day' 			=>$data['coupon_type'] === "E" && $data['pub_type'] === 'D' ? $data['pub_day'] : ($data['coupon_type'] === "E" && $data['pub_type'] === 'W' ? $data['pub_dayofweek'] : ''),
                'serial_yn'			=>$data['serial_yn'],
                'serial_dup_yn'		=>$data['serial_dup_yn'],
                'coupon_apply'		=>$data['coupon_apply'],
                'coupon_amt_kind'	=>$data['coupon_amt_kind'],
                'coupon_amt'		=>$data['coupon_amt'],
                'coupon_per'		=>$data['coupon_per'],
                'price_yn'			=>$data['price_yn'],
                'low_price'			=>$data['low_price'],
                'high_price'		=>$data['high_price'],
                'coupon_img'		=>$data['coupon_img'],
                'coupon_url'		=>$data['coupon_url'],
                'pub_cnt'			=>$data['pub_cnt'],
                'use_yn'			=>$data['use_yn'],
                'admin_id'			=>$data['admin_id'],
                // 'pub_time'			=>$data['pub_time'],

				// 20220406 모델 주석 수정
				'use_date_type'		=> $data['use_date_type'],
				'use_date_alarm_yn'	=> $data['use_date_alarm_yn'], 
				'use_date_alarm_day'	=> $data['use_date_alarm_day'], 
				'use_date'			=> $data['use_date']
            ]);

            DB::commit();
            return $data['coupon_no'];
        }catch(Exception $e){
            DB::rollback();
            return false;
        }
    }
    
	/**
	 * 쿠폰 기본 정보 삭제
	 * @param $data
	 * @return Boolean
	 */
	function delCouponInfo($coupon_no)
	{
        $sql = "DELETE FROM coupon WHERE coupon_no = '$coupon_no'";

        DB::delete($sql);
	}
	/**
	 * 쿠폰적용 카테고리 입력 - coupon_cat Table 입력
	 * @param $coupon_no
	 * @param $categories
	 */
	function setCategory($coupon_no, $categories)
	{
	}

	/**
	 * 쿠폰 적용 상품 입력 - coupon_goods Table 입력
	 * @param $coupon_no
	 * @param $goods
	 */
	function setGoods($coupon_no, $goods)
	{
		if (is_array($goods))
		{
			$query_str = "";
            $add_query = array();

            foreach($goods as $val) {
				if (empty($val)) continue;
				list($goods_no, $goods_sub) = explode("|", $val);
				$add_query[] = "('" . $coupon_no . "', '" . $goods_no . "', '" . $goods_sub . "')";
            }

			$query_str = @implode(", ", $add_query);

			if ($query_str != "")
			{
				$sql = "
					INSERT INTO coupon_goods
					(
						coupon_no, goods_no, goods_sub
					)
					VALUES
					" . $query_str . ";
                ";
                DB::insert($sql);
			}
		}
	}

	/**
	 * 쿠폰 미 적용 상품 입력 - coupon_goods_ex Table 입력
	 * @param $coupon_no
	 * @param $goods
	 */
	function setGoodsEx($coupon_no, $goods)
	{
		if (is_array($goods))
		{
			$query_str = "";
			$add_query = array();

			for ($i = 0; $i < sizeof($goods); $i++)
			{
				if (empty($goods[$i])) continue;
				list($goods_no, $goods_sub) = explode("|", $goods[$i]);
				$add_query[] = "('" . $coupon_no . "', '" . $goods_no . "', '" . $goods_sub . "')";
			}

			$query_str = @implode(", ", $add_query);
			if ($query_str != "")
			{
				$sql = "
					INSERT INTO coupon_goods_ex
					(
						coupon_no, goods_no, goods_sub
					)
					VALUES
					" . $query_str . "
                ";
                DB::insert($sql);
			}
		}
	}

	/**
	 * 업체 정산 데이터 입력 - coupon_company Table 입력
	 * @param $coupon_no
	 * @param $com_id
	 * @param $com_rat
	 */
	function setCompany($coupon_no, $com_id, $com_rat)
	{
		$com_id = explode("|", $com_id);
		$com_rat = explode("|", $com_rat);
		if (is_array($com_id) && is_array($com_rat))
		{
			for ($i = 0; $i < sizeof($com_id); $i++)
			{
				$add_query[] = "('" . $coupon_no . "', '" . $com_id[$i] . "', '" . $com_rat[$i] . "', CURRENT_TIMESTAMP())";
			}

			$query_str = @implode(", ", $add_query);
			$sql = "
				INSERT INTO coupon_company
				(
					coupon_no, com_id, com_rat, ut
				)
				VALUES
				" . $query_str . "
            ";
            DB::insert($sql);
		}
	}

	/**
	 * coupon_cat Table 삭제
	 * @param $coupon_no
	 */
	function delCategory($coupon_no){
	}

	/**
	 * coupon_goods Table 삭제
	 * @param $coupon_no
	 */
	function delGoods($coupon_no)
	{
		$sql = "
			DELETE FROM coupon_goods WHERE coupon_no = '$coupon_no'
        ";

        DB::delete($sql);
	}
	/**
	 * coupon_goods_ex Table 삭제
	 * @param $coupon_no
	 */
	function delGoodsEx($coupon_no)
	{
		$sql = "
			DELETE FROM coupon_goods_ex WHERE coupon_no = '$coupon_no'
		";
        DB::delete($sql);
	}
	/**
	 * coupon_no로 연동된 데이터 삭제
	 * @param $coupon_no
	 */
    function delLinkData($coupon_no) 
    {
        $this->delGoods($coupon_no);
        $this->delGoodsEx($coupon_no);
        $this->delCompany($coupon_no);
    }
	/**
	 * 업체 정산 관련 데이터 삭제 - goupon_company Table 삭제
	 * @param $coupon_no
	 */
	function delCompany($coupon_no)
	{
		$sql = "
			DELETE FROM coupon_company WHERE coupon_no = '$coupon_no'
		";
        DB::delete($sql);
	}

	/**
	 * 쿠폰 기본 정보 - coupon Table
	 * @param $coupon_no
	 * @return Array
	 */
	function getCouponInfo($coupon_no)
	{
		$sql = "
			SELECT * FROM coupon WHERE coupon_no = '$coupon_no'
		";

		return DB::selectOne($sql);
	}

	/**
	 * 쿠폰 적용 카테고리 or 상품 리스트 - coupon_cat or coupon_goods
	 * @param $getApplyData
	 * @param $coupon_no
	 * @return
	 */
	function getApplyData($getApplyData, $coupon_no)
	{
		$user       = Auth('head')->user();
        $category   = new Category($user, "DISPLAY");
		switch ($getApplyData)
		{
			// 전체 상품
			case 'AG' :
				$data = null;
			break;
			// 일부 카테고리
			case 'SC' :
				$sql = "
					SELECT d_cat_cd FROM coupon_cat WHERE coupon_no = '" . $coupon_no . "'
                ";
                $rows = DB::select($sql);

                foreach($rows as $row) {
					$data[$row->d_cat_cd] = $category -> Location($row->d_cat_cd);
                }

			break;
			// 일부 상품
			case 'SG' :
				$sql = "
					SELECT
						a.*, b.goods_nm
					FROM
						coupon_goods AS a
						INNER JOIN goods AS b ON a.goods_no = b.goods_no AND a.goods_sub = b.goods_sub
					WHERE
						a.coupon_no = '" . $coupon_no . "'
                ";
                $rows = DB::select($sql);

                foreach($rows as $row) {
					$data[$row->goods_no . "|" . $row->goods_sub] = $row->goods_nm;
                }
			break;
		}

		return (isset($data)) ? $data : null;
	}

	/**
	 * 쿠폰 제외 상품 리스트 - coupon_goods_ex Table
	 * @param $coupon_no
	 * @return Array
	 */
	function getNoApplyData($coupon_no)
	{
		$sql = "
			SELECT
				a.*, b.goods_nm
			FROM
				coupon_goods_ex AS a
				INNER JOIN goods AS b ON a.goods_no = b.goods_no AND a.goods_sub = b.goods_sub
			WHERE
				a.coupon_no = '" . $coupon_no . "'
        ";
        $rows = DB::select($sql);

        foreach($rows as $row) {
			$data[$row->goods_no . "|" . $row->goods_sub] = $row->goods_nm;
        }

		return (isset($data)) ? $data : null;
	}

	/**
	 * 쿠폰 번호 생성
	 * @param $coupon_no
	 * @param $pub_cnt
	 */
	function MakeCouponSerialNumber($coupon_no, $pub_cnt)
	{
		$sql = "
			select count(*) as cnt from coupon_serial where coupon_no = '$coupon_no'
		";
		

		$cnt = DB::selectOne($sql)->cnt;
		if($cnt == 0){
			$random_id_length = 8;

			$admin_id = $this->user['id'];
			$tmp_serial = $this -> GenerateCouponCode($coupon_no, $pub_cnt);
			$serial = array_keys($tmp_serial);

			$sql = "
				insert into coupon_serial
				( coupon_no, serial, admin_id, ut, rt ) values
			";
			$query_str = "";

			$i = 1;
			foreach( $tmp_serial as $serial => $val ){
				$iterator = ( $query_str == "" ) ? "":",";
				$query_str .= $iterator ." ('$coupon_no','$serial', '$admin_id', NOW(), NOW()) ";
				if( $i % 10000 == 0 ){
					DB::insert($sql.$query_str);
					$query_str = "";
				}
				$i++;
			}
			if( $query_str != "" ){
				DB::insert($sql.$query_str);
			}

		}
	}

	/**
	 * 쿠폰코드 생성

		NNNN-NNNNNNNN
		- 쿠폰번호는 항상 유일하다.
		- 쿠폰번호는 0-9A-F 로
		- 쿠폰 0~4	: 쿠폰번호의 16진수의 역문자열
		- 쿠폰 5~N	: 랜덤
		- N는 1~N-1 의 총합의 16 나머지 값
	 *
	 * @param	 int		쿠폰번호
	 * @param	 int		쿠폰코드 생성개수
	 * @param	 int		쿠폰코드 길이 ( 기본값 : 12 )
	 * @access	 public
	 * @return	 array	쿠폰코드배열
	 *
	 */
	function GenerateCouponCode($prefix, $count, $len = 12)
	{
            $sprefix = substr(strtoupper(str_pad(dechex($prefix),4,"0",STR_PAD_RIGHT)),0,4);
            $checksum_prefix = 0;

            for ($i = 0; $i < 4; $i++)
            {
                    $checksum_prefix += hexdec(substr($sprefix, $i, 1));
            }

            $couponcodes = array();
            $cnt = 0;

            while($cnt < $count)
            {
                    $couponcode = "";
                    $checksum = $checksum_prefix;

                    for($i = 4; $i < $len-1 ; $i++)
                    {
                            $index = rand(0, 15);
                            $checksum += $index;
                            $couponcode .= dechex($index);
                    }
                    $couponcode = strtoupper(sprintf("%s%s%s", $sprefix, $couponcode, strtoupper(dechex($checksum%16))));

                    if(isset($couponcodes[$couponcode]) == false)
                    {
                            $couponcodes[$couponcode] = true;
                            $cnt++;
                    } else {
                            echo "$cnt FAIL";
                    }
            }
            return $couponcodes;
	}

	/**
	 * 관련 쿠폰 번호 삭제
	 * @param $coupon_no
	 */
	function delSerial($coupon_no)
	{
		$sql = "
			DELETE FROM
				coupon_serial
			WHERE
				coupon_no = '" . $coupon_no . "'
        ";

        DB::delete($sql);
	}

	function uptGoods($goods)
	{
		for($i = 0; $i < sizeof($goods); $i++)
		{
			if (empty($goods[$i])) continue;

			list($goods_no, $goods_sub) = explode("|", $goods[$i]);
			$sql = "call cal_goodsno_coupon('" . $goods_no . "', '" . $goods_sub . "')";

            DB::update($sql);
		}
			//exit;
	}

    /**
	 * CRM 쿠폰 일괄 지급
	 * @param $userID_data
     * @param $couponNO_data
	 */
	function couponAdd($user_ids, $coupon_no)
	{
        $user_cnt = count($user_ids);

        $sql = "
        	select * from coupon where coupon_no = '$coupon_no'
        ";
        $row = Db::selectOne($sql);

        if(!empty($row->coupon_no)){
        	if($row->pub_cnt > 0 && ($row->pub_cnt - $row->coupon_pub_cnt) < $user_cnt){
        		return -2;
        	}

			$use_to_date = '';
			if($row->use_date_type == 'P'){
				$use_date = $row->use_date;
				$use_to_date = date("Ymd",strtotime("+ $use_date days"));
			}

	        for($i=0; $i < $user_cnt; $i++){
                DB::table('coupon_member')->insert([
                    'user_id' => $user_ids[$i], 
                    'coupon_no' => $coupon_no, 
                    'down_date' => now(), 
                    'use_yn' => 'N', 
                    'use_to_date' => $use_to_date
                ]);

	            // 쿠폰 다운로드수 업데이트
	            $sql = "
	                update coupon set
	                    coupon_pub_cnt = coupon_pub_cnt + 1
	                where coupon_no = '$coupon_no'
                ";

                DB::update($sql);
	        }
	        return 1;
        } else {
        	return -1;
        }
	}

	/**
	* 오프라인 쿠폰 지급 시 에러상황별 코드 정의
	*/
	private function _get_coupon_error_msg($err)
	{
		$msgs = [
			'10' => '시리얼넘버에 해댱하는 쿠폰정보가 존재하지 않습니다.',
			'11' => '발행중지된 쿠폰입니다.',
			'12' => '중복발급이 불가능한 쿠폰입니다.',
			'13' => '발행된 쿠폰이 모두 발급완료된 쿠폰입니다.',
			'701' => '오프라인 쿠폰이 아닙니다.',
			'702' => '쿠폰의 지급기한이 아닙니다.',
			'703' => '이미 발급된 쿠폰입니다.',
		];
		return ['code' => -2, 'msg' => $msgs[$err] . ' 해당 쿠폰을 등록할 수 없습니다.', 'error_code' => $err];
	}

	/**
	 * 오프라인 쿠폰 지급
	 * @param $userID_data
     * @param $serialNumber_data
	 */
	function offCouponAdd($user_id, $serial_num)
	{
		$today = now()->format("Ymd");
		
		/**
		 * COUPON 검사
		 */

		$cp_serial = DB::table('coupon_serial')->where('serial', $serial_num)->first();

		// 시리얼넘버에 해댱하는 쿠폰정보 존재여부 체크
		if ($cp_serial === null) {
			return $this->_get_coupon_error_msg('10');
		}

		$cp = DB::table('coupon')->where('coupon_no', $cp_serial->coupon_no)->first();

		// 발행중지된 쿠폰여부 체크
		if ($cp->use_yn === 'N') {
			return $this->_get_coupon_error_msg('11');
		}

		$cp_member = DB::table('coupon_member')->where('user_id', $user_id)->where('coupon_no', $cp->coupon_no);

		// 중복발급 불가능 시, 사용자가 해당 시리얼넘버 쿠폰을 발행받은적 있는지 체크
		if ($cp->pub_dup_yn !== 'Y' && $cp_member->count() > 0) {
			return $this->_get_coupon_error_msg('12');
		}
		// 쿠폰 지급횟수가 쿠폰 발행수를 초과했는지 체크
		if ($cp->pub_cnt > 0 && $cp->coupon_pub_cnt >= $cp->pub_cnt) {
			return $this->_get_coupon_error_msg('13');
		}
		// 오프라인쿠폰 여부 체크
		if ($cp->coupon_type !== 'F') {
			return $this->_get_coupon_error_msg('701');
		}
		// 쿠폰 지급기한 체크
		if ($cp->pub_fr_date > $today && $cp->pub_fr_date != '99999999') {
			return $this->_get_coupon_error_msg('702');
		}
		if ($cp->pub_to_date < $today && $cp->pub_to_date != '99999999') {
			return $this->_get_coupon_error_msg('702');
		}
		// 이미 발급된 쿠폰인지 체크
		if ($cp->pub_cnt > 0 && $cp->serial_dup_yn !== 'Y' && $cp_serial->use_yn === 'Y') {
			return $this->_get_coupon_error_msg('703'); // coupon_serial 테이블에서 오프라인쿠폰의 경우 발급 시 사용처리됨
		}

		/**
		 * COUPON 발급
		 */

		$use_to_date = '';
		if ($cp->use_date_type === 'S') {
			$use_to_date = $cp->use_to_date;
		} else if ($cp->use_date_type === 'P') {
			$use_to_date = now()->add($cp->use_date ?? 0, 'day')->format("Ymd");
		}

		// 1. coupon_member에 지급정보 추가
		$result = DB::table('coupon_member')->insert([
			'user_id' => $user_id,
			'coupon_no' => $cp->coupon_no,
			'down_date' => now(),
			'use_to_date' => $use_to_date,
			'serial' => $cp_serial->serial,
			'use_yn' => 'N',
			'rt' => now(),
		]);
		if ($result < 1) {
			return ['code' => -1, 'msg' => 'failed: insert coupon_member'];
		}

		// 2. coupon_serial에 지급정보 업데이트
		// coupon_serial 테이블에서 오프라인쿠폰의 경우 발급 시 사용처리함
		$result = DB::table('coupon_serial')->where('serial', $cp_serial->serial)->update([
			// 'pub_cnt' => DB::raw('ifnull(pub_cnt, 0) + 1'), // 사용안함
			// 'pub_date' => now(), // 사용안함
			// 'use_cnt' => DB::raw('ifnull(use_cnt, 0) + 1'), // 사용안함
			'use_yn' => 'Y',
			'use_date' => now(),
			'ut' => now(),
		]);
		if ($result < 1) {
			return ['code' => -1, 'msg' => 'failed: update coupon_serial'];
		}

		// 3. coupon 다운로드횟수 업데이트
		$result = DB::table('coupon')->where('coupon_no', $cp->coupon_no)
			->update([ 'coupon_pub_cnt' => DB::raw('ifnull(coupon_pub_cnt, 0) + 1') ]);
		if ($result < 1) {
			return ['code' => -1, 'msg' => 'failed: update coupon'];
		}

		// // 4. coupon_use_log_t 로그 등록
		// $result = DB::table('coupon_use_log_t')->insert([
		// 	'coupon_no' => $cp->coupon_no,
		// 	'user_id' => $user_id,
		// 	'ord_opt_no' => '0',
		// 	'ord_no' => '0',
		// 	'order_amt' => '0',
		// 	'coupon_amt' => '0',
		// 	'regi_date' => now(),
		// 	'use_gubun' => '1',
		// ]);
		// if ($result < 1) {
		// 	return ['code' => -1, 'msg' => 'failed: insert coupon_use_log_t'];
		// }
		
		return ['code' => 1, 'msg' => 'success'];
	}
}