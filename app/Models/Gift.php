<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class Gift
{
  public function getValue($data) {
    return [
      'name' => $data->gift_nm,
      'kind' => $data->gift_kind,
      'fr_date' => $data->fr_date,
      'to_date' => $data->to_date,
      'apply_amt' => $data->apply_amt,
      'gift_price' => $data->gift_price,
      'qty' => $data->qty,
      'unlimited_yn' => $data->unlimited_yn,
      'dp_soldout_yn' => $data->dp_soldout_yn,
      'img' => $data->img,
      'contents' => $data->contents,
      'memo' => $data->memo,
      'use_yn' => $data->use_yn,
      'apply_product' => $data->apply_product,
      'apply_com' => $data->apply_com,
      'apply_group' => $data->apply_group,
      'refund_yn' => $data->refund_yn,
      'admin_id' => $data->admin_id,
      'admin_nm' => $data->admin_nm,
      'rt' => now(),
      'ut' => now()
    ];
  }

    /** 사은품 정보 등록 */
	public function SetGiftInfo($data)
	{
        return DB::table('gift')->insertGetId($this->getValue($data));
    }

	/*
		Function: ModGiftInfo
		사은품 정보 수정

		Returns:
			$data - 사은품 정보 파라미터
	*/
	public function ModGiftInfo($data)
	{
    $values = $this->getValue($data);
    $columns = [];

    foreach($values as $key => $value) {
      if ($key === "rt") continue;

      if ($value !== "") {
		//$columns[] = [ $key => $value ];
		$columns[$key] = $value;
        continue;
      }

      if ($key === 'gift_price' && $value == "0") {
		//$columns[] = [ $key => $value ];
		$columns[$key] = $value;
      }

      if ($key === 'qcy' && $value == "0") {
		//$columns[] = [ $key => $value ];
		$columns[$key] = $value;
      }
	}

    DB::table('gift')
      ->where('no', $data->gift_no)
      ->update($columns);

    return $data->gift_no;
  }


	/*
		Function: DelGiftInfo
		사은품 정보 삭제

		Returns:
			$gift_no - 사은품 번호
	*/
	public function DelGiftInfo($gift_no)
	{
    DB::table('gift')
      ->where('no', $gift_no)
      ->delete();
  }

	/*
		Function: SetGoods
		사은품 적용상품 설정

		Returns:
			$gift_no - 사은품 번호
			$goods - 상품정보(배열)
	*/
	public function SetGoods($gift_no, $goods)
	{
        $user = Auth('head')->user();
		$admin_id = $user->com_id;
		$admin_nm = $user->name;

		if (is_array($goods))
		{
			$goods = collect($goods)->unique()->all();
			for ($i = 0; $i < sizeof($goods); $i++)
			{
				if (empty($goods[$i])) continue;
				list($goods_no, $goods_sub) = explode("|", $goods[$i]);

				$data = [
					'gift_no' => $gift_no,
					'goods_no' => $goods_no,
					'goods_sub' => $goods_sub,
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
					'rt' => now()
				];

				DB::table('gift_goods')->insert($data);
			}
		}
  }

	/*
		Function: SetExGoods
		사은품 제외상품 설정

		Returns:
			$gift_no - 사은품 번호
			$goods - 상품정보(배열)
	*/
	public function SetExGoods($gift_no, $goods)
	{
    	$user = Auth('head')->user();
		$admin_id = $user->com_id;
		$admin_nm = $user->name;

		if (is_array($goods))
		{
			$goods = collect($goods)->unique()->all();
			for ($i = 0; $i < sizeof($goods); $i++)
			{
				if (empty($goods[$i])) continue;
        		list($goods_no, $goods_sub) = explode("|", $goods[$i]);

			$data = [
				'gift_no' => $gift_no,
				'goods_no' => $goods_no,
				'goods_sub' => $goods_sub,
				'admin_id' => $admin_id,
				'admin_nm' => $admin_nm,
				'rt' => now()
			];

        DB::table('gift_ex_goods')->insert($data);
      }
		}
  }

	/*
		Function: DelGoods
		사은품 적용 상품 삭제

		Returns:
			$gift_no - 사은품 번호
	*/
	public function DelGoods($gift_no)
	{
        return DB::table('gift_goods')
            ->where('gift_no', $gift_no)
            ->delete();
    }

	/*
		Function: DelExGoods
		사은품 제외 상품 삭제

		Parameters:
			$gift_no - 사은품 번호

		Returns:

	*/
	public function DelExGoods($gift_no)
	{
        return DB::table('gift_ex_goods')
          ->where('gift_no', $gift_no)
          ->delete();
    }

	/*
		Function: GiveGift
		사은품 지급

		Parameters:
			$order_gift_no - 사은품 주문 일련번호

		Returns:

	*/
	public function GiveGift($order_gift_no)
	{
    return DB::table('order_gift')
             ->where('no', $order_gift_no)
             ->update([
               'give_yn' => 'Y',
               'give_date' => now()
             ]);
  }

	/*
		Function: RefundGift
		사은품 환불

		Parameters:
			$ord_no - 주문번호
			$ord_op_no - 주문일련번호

		Returns:

	*/
	public function Refund($ord_no, $ord_op_no)
	{
		// 재고 추가
		$sql = "
			select a.gift_no, b.unlimited_yn
			from order_gift a
				inner join gift b on a.gift_no = b.no
			where a.ord_no = '$ord_no' and a.refund_no = '$ord_op_no'
		";

		$jaegos = DB::select($sql);

		foreach($jaegos as $row) {
			if($row->unlimited_yn == "N"){
				$sql_qty = "
					update gift set
						qty = qty + 1
					where no = '$row->gift_no'
				";
			DB::update($sql_qty);
			}
		}
		// 환불 처리
		DB::table('order_gift')
		->where('ord_no', $ord_no)
		->where('refund_no', $ord_op_no)
		->update([
			'refund_yn' => 'Y',
			'refund_date' => now()
		]);
	}


	/*
		Function: SetRefundGiftAmt
		사은품 환불 금액 저장

		Parameters:
			$order_gift_no - 사은품 주문 일련번호
			$refund_no - 사은품 환불 번호
			$refund_amt - 사은품 환불 금액

		Returns:

	*/
	public function SetRefundGiftAmt($order_gift_no, $refund_no, $refund_amt)
	{
        DB::table('order_gift')
        ->where('no', $order_gift_no)
        ->update([
            'refund_no' => $refund_no,
            'refund_amt' => $refund_amt
        ]);
	}
}
