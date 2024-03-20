<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

// 상품출고 모델 추가
class S_Release
{
	public $user;

	function __construct($user = [])
	{
		$this->user = $user;
	}
	
	// 출고 상태 기간 만료 데이터 생성
	// $target_state	: 출고상태 
	// $store_cd		: 해당 매장
	// $data			: 기간만료 출고 자료 수
	public function getExpireRelease($target_state, $store_cd)
	{
		$sql	= " select count(*) as tot from product_stock_release where state = :state and store_cd = :store_cd and date_add(prc_rt, interval 1 month) < now() ";
		$data	= DB::selectOne($sql,['state' => $target_state, 'store_cd' => $store_cd])->tot;
		
		return $data;
	}
}
