<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Jaego;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class sal01Controller extends Controller
{

	protected $file;
	protected $tmp_name;

	//
	public function index() {

        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		//매장구분
		$sql		= " 
			select 
			* from __tmp_code 
			where 
				code_kind_cd = 'com_type' and use_yn = 'Y' order by code_seq 
		";
		$com_types	= DB::select($sql);

		//행사구분
		$sql	= "
			select
			* from __tmp_code
			where
				code_kind_cd = 'event_cd' and use_yn = 'Y' order by code_seq
		";
		$event_cds	= DB::select($sql);

		//판매유형
		$sql	= "
			select
			* from __tmp_code
			where
				code_kind_cd = 'sell_type' and use_yn = 'Y' order by code_seq
		";
		$sell_types	= DB::select($sql);



		$sql		= " select * from __tmp_code_kind order by code_kind_nm ";
		$code_kinds	= DB::select($sql);


		$values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,

			'code_kinds'	=> $code_kinds,
		];

		return view( Config::get('shop.store.view') . '/sale/sal01',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$sdate		= $request->input('sdate',Carbon::now()->sub(2, 'year')->format('Ymd'));
		$edate		= $request->input('edate',date("Ymd"));
		$com_type	= $request->input('com_type');
		$com_nm		= $request->input('com_nm');
		$goods_code	= $request->input('goods_code');
		$event_cd	= $request->input('event_cd');
		$user_id	= $request->input('user_id');
		$sell_type	= $request->input('sell_type');

		$limit		= $request->input("limit",100);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','g.goods_no');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		if( $com_type != "" )	$where .= " and a.com_type = '" . $com_type . "' ";
		if( $com_nm != "" )		$where .= " and a.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if( $goods_code != "" )	$where .= " and a.goods_code like '" . Lib::quote($goods_code) . "%' ";
		if( $event_cd != "" )	$where .= " and a.event_cd = '" . $event_cd . "' ";
		if( $user_id != "" )	$where .= " and a.user_id = '" . Lib::quote($user_id) . "%' ";
		if( $sell_type != "" )	$where .= " and a.sell_type = '" . $sell_type . "' ";

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

		if( $page == 1 ){
			$query	= "
				select count(*) as total
				from __tmp_order a
				where 1=1 
					and ( a.ord_date >= :sdate and a.ord_date < date_add(:edate,interval 1 day))
					$where
			";
			//$row = DB::select($query,['com_id' => $com_id]);
			$row		= DB::select($query, ['sdate' => $sdate,'edate' => $edate]);
			$total		= $row[0]->total;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		}

		$query	= "
			select
				a.*, 
				(100 - round(a.price/a.goods_sh * 100)) as sale_rate,
				(100 - round(a.ord_amt/a.goods_sh * 100)) as ord_sale_rate,
				( (100 - round(a.ord_amt/a.goods_sh * 100)) - (100 - round(a.price/a.goods_sh * 100)) ) as sale_gap,
				b.code_val as com_type_nm,
				c.code_val as opt_kind_nm,
				d.code_val as brand_nm,
				e.code_val as stat_pay_type_nm,
				f.code_val as sell_type_nm,
				g.code_val as event_kind_nm
			from __tmp_order a
			left outer join __tmp_code b on b.code_kind_cd = 'com_type' and b.code_id = a.com_type
			left outer join __tmp_code c on c.code_kind_cd = 'opt_kind_cd' and c.code_id = a.opt_kind
			left outer join __tmp_code d on d.code_kind_cd = 'brand' and d.code_id = a.brand
			left outer join __tmp_code e on e.code_kind_cd = 'stat_pay_type' and e.code_id = a.stat_pay_type
			left outer join __tmp_code f on f.code_kind_cd = 'sell_type' and f.code_id = a.sell_type
			left outer join __tmp_code g on g.code_kind_cd = 'event_cd' and g.code_id = a.event_cd
			where 1=1 
				and ( a.ord_date >= :sdate and a.ord_date < date_add(:edate,interval 1 day))
				$where
			$orderby
			$limit
		";

		$result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body" => $result
		]);

	}

	public function show()
	{
		$values = [];
		return view( Config::get('shop.store.view') . '/sale/sal01_show',$values);
	}

	public function upload(Request $request)
	{
		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			/**
			 * DB 저장이 끝나면 디렉토리 생성(없을 경우) 및 파일 저장
			 */
			$save_path = "data/store/sale/sal01/";

			if (!Storage::disk('public')->exists($save_path)) {
				Storage::disk('public')->makeDirectory($save_path);
			}

			$file = sprintf("data/store/sale/sal01/%s", $_FILES['file']['name']);
			$tmp_name = $_FILES['file']['tmp_name'];
			move_uploaded_file($tmp_name, $file);

			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}
	}

	public function update(Request $request)
	{
		ini_set('max_execution_time', '600');
		//set_time_limit(0);
		$order = $request->input('data');

		$admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
		$ord_type = 14;
        $ord_no = "";
        $code = 0;

		$order = (array)$order;

		/**
		 * prd_cd 설정 (자사바코드 있는 경우 자사바코드 사용, 없는 경우 상품코드 + 칼라 + 사이즈로 상품코드 설정)
		 */
		$prd_cd = $order['barcode'] ? $order['barcode'] : $order['goods_code'] . $order['color'] . $order['size'];

		/**
		 * goods_no 가져오기
		 */
		$sql = /** @lang text */
		"
			select goods_no
			from product_stock
			where prd_cd = :prd_cd
		";
		$result = DB::selectOne($sql, array("prd_cd" => $prd_cd));
		$order['goods_no'] = @$result->goods_no;

		/**
		 * 초기 값 설정
		 */
		$order['goods_sub'] = 0;
		$order['out_ord_no'] = 0;
		$order['dlv_amt'] = 0;
		$order['point_amt'] = 0;
		$order['coupon_amt'] = 0;
		
		$order['com_rate'] = 0;
		$order['ord_kind'] = 20;
		$order['ord_type'] = 12;
		$order["pay_stat"] = 1;
		$order['coupon_no'] = 0;
		$order['com_coupon_ratio'] = 0;
		$order['sales_com_fee'] = $order['pay_fee'];
		$store_cd = $order['com_id'];
		$order['sale_kind'] = sprintf('%02d', $order['sell_type_nm']);
		$order['sale_place'] = $order['com_nm'];
		$order['user_nm'] = $order['ord_nm'] ? $order['ord_nm']: "비회원";

		$order['qty'] = Lib::uncm($order['qty']);

		$order["ord_amt"] = Lib::uncm($order["ord_amt"]);
		$order["recv_amt"] = Lib::uncm($order["recv_amt"]);
		$order["price"] = Lib::uncm($order["price"]);
		$order["wonga"] = Lib::uncm($order["wonga"]);

		$order["ord_amt"] = @$order["ord_amt"] ? $order["ord_amt"] : $order['price'] * $order['qty']; // 주문금액(판매단가)은 없으면 판매가 * 수량 처리
		$order['dc_amt'] = $order['ord_amt'] - $order['recv_amt'] - $order['point_amt']; // 결제금액 = 주문금액 - 할인... - (쿠폰, 적립금) 등등.

		/**
		 * 옵션 처리
		 */
		$goods_opt = "";
		$color_nm = @$order['color_nm'];
		$size_nm = @$order['size_nm'];
		if ($color_nm && $size_nm) {
			$goods_opt = $color_nm . "^" . $size_nm;
		} else {
			if ($color_nm) {
				$goods_opt .= $color_nm;
			}
			if ($size_nm) {
				$goods_opt .= $size_nm;
			}
		}
		$order['goods_opt'] = $goods_opt;

		/**
		 * validation 추후 처리 할 것 (주문자명, 상품명, 상품코드, 매장명, 매장코드 등등... 아래는 샘플)
		 */
		$out_ord_no = @$order["out_ord_no"];
		if (@$order["goods_no"] == "") {
			$code = "-101";
		} else if (@$order["goods_opt"] == "") {
			$code = "-102";
		} else if (@$order["qty"] == "") {
			$code = "-106";
		} else if (@$order["ord_amt"] == "") {
			$code = "-107";
		}

		// else if (@$order["r_nm"] == "") {
		// 	$code = "-110";
		// } else if (@$order["r_zipcode"] == "") {
		// 	$code = "-111";
		// } else if (@$order["r_addr"] == "") {
		// 	$code = "-112";
		// }

		if ($code === 0) {
			$stock = new Jaego();
			// if ($stock->IsOption($order["goods_no"], 0, $order["goods_opt"]) == false) {
			// 	$code = "-220";
			// 	return ["code" => $code];
			// }

			// $sql = /** @lang text */
			// "
			// 	select goods_no, opt_id, ord_no, user_nm
			// 	from outbound_order
			// 	where sale_place = :sale_place and out_ord_no = :out_ord_no
			// ";
			// $rows = DB::select($sql, array("sale_place" => @$order["sale_place"], "out_ord_no" => $out_ord_no));
			// $ord_seq = 0;

			// if (count($rows) > 0) {
			// 	for ($i = 0; $i < count($rows); $i++) {
			// 		$out_order_row = (array)$rows[$i];
			// 		if (trim($out_order_row["goods_no"]) == $order["goods_no"] && trim($out_order_row["opt_id"]) == $order["goods_opt"]) {
			// 			return ["code" => "-310"];
			// 		} else {
			// 			$ord_no = $out_order_row["ord_no"];
			// 		}
			// 	}

			// 	$sql =
			// 		/** @lang text */
			// 	"
			// 		select user_nm from order_mst
			// 		where ord_no = :ord_no
			// 	";
			// 	$row = (array)DB::selectone($sql, array("ord_no" => $ord_no));
			// 	if ($row) {
			// 		if (trim($row["user_nm"]) != $order["user_nm"]) {	// 묶음주문인데 주문자명이 다른 경우 처리
			// 			return ["code" => "-320"];
			// 		}
			// 		$ord_seq++;
			// 	} else {
			// 		return ["code" => "-330"];
			// 	}
			// }

			$sql = /** @lang text */
				"
					select
						a.head_desc, a.goods_nm, a.style_no, a.md_id, a.md_nm, b.com_nm, a.com_id,
						a.baesong_kind, a.baesong_price, b.pay_fee/100 as com_rate, a.price, a.com_type,
						a.is_unlimited
					from goods a left outer join company b on a.com_id  = b.com_id
					where a.goods_no = :goods_no 
				";
			$row = (array)DB::selectone($sql, array("goods_no" => $order["goods_no"]));
			if ($row) {
				if (isset($order["goods_nm"]) || $order["goods_nm"] == "") {
					$order["goods_nm"]	= $row["goods_nm"];
				}
				$order["md_id"]		= $row["md_id"];
				$order["md_nm"]		= $row["md_nm"];
				$order["com_type"] 	= $row["com_type"];
				$order["com_id"]	= $row["com_id"];
				$order["com_nm"] 	= $row["com_nm"];
				$order["baesong_kind"] =  $row["baesong_kind"];
				$is_unlimited = $row["is_unlimited"];
			} else {
				return ["code" => "-210"];
			}

			/**
			 * 재고 확인
			 */
			// $is_stock = true;
			// $good_qty = $stock->GetQty($order["goods_no"], $order["goods_sub"], $order["goods_opt"]);

			// if ($is_unlimited == "Y") {
			// 	if ($good_qty == 0) {
			// 		$is_stock = false;
			// 	}
			// } else {
			// 	if ($order["qty"] > $good_qty) {
			// 		$is_stock = false;
			// 	}
			// }

			// 주문 상태
			// $order["ord_state"] = ($is_stock == true) ? "10" : "5";
			// $order["clm_state"] = ($is_stock == true) ? "0" : "0";	// 클레임 : 주문취소 상태

			$order["ord_state"] = 10;
			$order["clm_state"] = 0;
			$ord_seq = 0;
			// $is_stock = true;

			try {

				$orderClass = new Order([
					"id" => $admin_id,
					"name" => $admin_nm
				]);
				if ($ord_no === "") {
					$ord_no = $orderClass->GetNextOrdNo();
				}
				$orderClass->SetOrdNo($ord_no);

				if ($ord_seq == 0) {
					$order_mst = [
						"ord_no"		=> $ord_no,
						"store_cd"      => $store_cd,
						"ord_date"      => $order["ord_date"],
						"user_id" 		=> $order["user_id"],
						"user_nm" 		=> $order["user_nm"],
						"phone" 		=> Lib::getValue($order, "phone", ""),
						"mobile" 		=> Lib::getValue($order, "mobile", ""),
						"email" 	    => Lib::getValue($order, "email", ""),
						"ord_amt" 		=> $order["ord_amt"],
						"recv_amt"		=> $order["recv_amt"],
						"dc_amt"		=> $order['dc_amt'],
						"point_amt" 	=> 0,
						"coupon_amt"	=> 0,
						"dlv_amt" 		=> @$order["dlv_amt"],
						"r_nm" 			=> @$order["r_nm"],
						"r_zipcode" 	=> @$order["r_zipcode"],
						"r_addr1" 		=> @$order["r_addr1"],
						"r_addr2" 		=> @$order["r_addr2"],
						"r_phone" 		=> @$order["r_phone"],
						"r_mobile" 		=> @$order["r_mobile"],
						"dlv_msg" 		=> @$order["dlv_msg"],
						"ord_state" 	=> @$order["ord_state"],
						"ord_type" 		=> @$order["ord_type"],
						"ord_kind" 		=> @$order["ord_kind"],
						"sale_kind" 	=> @$order["sale_kind"],
						"sale_place" 	=> @$order["sale_place"],
						"out_ord_no" 	=> @$order["out_ord_no"],
						"upd_date"      => DB::raw('now()'),
						"dlv_end_date"  => DB::raw('now()')
					];
					DB::table('order_mst')->insert($order_mst);

					$payment = [
						"ord_no"		=> $ord_no,
						"pay_type" 		=> @$order["pay_type"],
						"pay_nm" 		=> $order["user_nm"],
						"pay_amt" 		=> $order["ord_amt"],
						"pay_stat" 		=> @$order["pay_stat"],
						"bank_inpnm" 	=> Lib::getValue($order, "bank_inpnm", ""),
						"bank_code" 	=> Lib::getValue($order, "bank_code", ""),
						"bank_number" 	=> Lib::getValue($order, "bank_number", ""),
						"ord_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
						"upd_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
					];
					DB::table('payment')->insert($payment);
				} else {
					DB::table('order_mst')
						->where('ord_no', '=', $ord_no)
						->update([
							'ord_amt' => DB::raw(sprintf("ord_amt + %d", $order["ord_amt"])),
							'recv_amt' => DB::raw(sprintf("recv_amt + %d", $order["ord_amt"])),
							'dlv_amt' => DB::raw(sprintf("dlv_amt + %d", $order["ord_amt"])),
						]);

					DB::table('payment')
						->where('ord_no', '=', $ord_no)
						->update([
							'pay_amt' => $order["ord_amt"]
						]);
				}

				$order_opt = [
					"goods_no"		=> $order["goods_no"],
					"goods_sub" 	=> $order["goods_sub"],
					"ord_no" 		=> $ord_no,
					"ord_seq" 		=> $ord_seq,
					"head_desc" 	=> Lib::getValue($order, "head_desc", ""),
					"goods_nm" 		=> $order["goods_nm"],
					"goods_opt" 	=> $order["goods_opt"],
					"qty"			=> $order["qty"],
					"price" 		=> $order["price"],
					"wonga"			=> $order["wonga"],
					"pay_type"		=> @$order["pay_type"],
					"dlv_pay_type" 	=> @$order["dlv_pay_type"],
					"dlv_amt" 		=> $order["dlv_amt"],
					"dc_amt" 		=> $order["dc_amt"],
					"point_amt" 	=> 0,
					"coupon_amt" 	=> 0,
					"recv_amt" 		=> $order["recv_amt"],
					"md_id" 		=> $order["md_id"],
					"md_nm" 		=> $order["md_nm"],

					"sale_place" 	=> @$order["sale_place"],
					"ord_state" 	=> $order["ord_state"],
					"clm_state" 	=> $order["clm_state"],
					"com_id" 		=> $order["com_id"],
					"ord_kind" 		=> @$order["ord_kind"],
					"ord_type" 		=> @$order["ord_type"],
					"baesong_kind" 	=> $order["baesong_kind"],

					//"dlv_state_date"=> ($order["ord_state"] == "10" ) ? DB::raw('now()') : DB::raw('NULL'),
					"dlv_comment" 	=> @$order["dlv_comment"],
					"admin_id" 		=> $admin_id,
					"sales_com_fee" => @$order["sales_com_fee"],
					"ord_date"      => $order["ord_date"],
					'prd_cd'        => $prd_cd
				];
				DB::table('order_opt')->insert($order_opt);
				$ord_opt_no = DB::getPdo()->lastInsertId();

				/**
				 * 주문상태 로그
				 */
				$state_log = array(
					"ord_no"		=> $ord_no,
					"ord_opt_no"	=> $ord_opt_no,
					"ord_state"		=> $order["ord_state"],
					"comment" 		=> "매장판매일보",
					"admin_id" => $admin_id,
					"admin_nm" => $admin_nm
				);
				$orderClass->AddStateLog($state_log);

				// 재고 차감 여기
				// $orderClass->CompleteOrderSugi($ord_opt_no, $order["ord_state"]);
				$orderClass->SetOrdOptNo($ord_opt_no);
				$order_opt_wonga = array(
					"goods_no" => $order['goods_no'],
					"goods_sub" => $order['goods_sub'],
					"goods_opt" => $order['goods_opt'],
					"qty" => $order['qty'],
					"wonga" => $order['wonga'],
					"price" => $order['price'],
					"dlv_amt" => @$order['dlv_amt'],
					"recv_amt" => $order['recv_amt'],
					"point_apply_amt" => $order['point_amt'],
					"coupon_apply_amt" => $order['coupon_amt'],
					"dc_apply_amt" => $order['dc_amt'],
					"pay_fee" => $order['pay_fee'],
					"com_id" => $order['com_id'],
					"com_rate" => $order['com_rate'],
					"ord_state" => $order['ord_state'] ? $order['ord_state'] : 10,
					"ord_kind"	=> $order['ord_kind'],
					"ord_type" => $order['ord_type'],
					"coupon_no" => $order['coupon_no'],
					"com_coupon_ratio" => $order['com_coupon_ratio'],
					"sales_com_fee" => $order['sales_com_fee'],
					'prd_cd' => $prd_cd
				);
				$orderClass->__InsertOptWonga($order_opt_wonga);

				// outbound_order 저장 /////////////////////////////////////////////

				// $out_order = array(
				// 	"sale_place"	=> @$order["sale_place"],
				// 	"out_ord_no" 	=> @$order["out_ord_no"],

				// 	"pay_date" 		=> @$order["pay_date"],
				// 	"goods_no" 		=> $order["goods_no"],
				// 	"goods_nm" 		=> $order["goods_nm"],
				// 	"opt1" 			=> $order["goods_opt"],
				// 	"qty" 			=> $order["qty"],
				// 	"price" 		=> $order["ord_amt"],

				// 	"r_nm" 			=> @$order["r_nm"],
				// 	"r_zipcode" 	=> @$order["r_zipcode"],
				// 	"r_addr1" 		=> @$order["r_addr1"],
				// 	"r_addr2" 		=> @$order["r_addr2"],
				// 	"r_phone" 		=> @$order["r_phone"],
				// 	"r_mobile" 		=> @$order["r_mobile"],
				// 	"dlv_msg" 		=> @$order["dlv_msg"],

				// 	"user_nm" 		=> $order["user_nm"],
				// 	"user_phone" 	=> Lib::getValue($order, "phone", ""),
				// 	"user_mobile" 	=> Lib::getValue($order, "email", ""),

				// 	"opt_id" 		=> $order["goods_opt"],
				// 	"ord_no" 		=> $ord_no,
				// 	"ord_opt_no" 	=> $ord_opt_no,
				// 	"sales_com_fee" => @$order["sales_com_fee"],
				// 	"dlv_amt" 		=> @$order["dlv_amt"],
				// );
				// DB::table('outbound_order')->insert($out_order);
				$code = 200;
			} catch (Exception $e) {
				// dd($e->getMessage());
			}
			return response()->json(['code'	=> $code]);
		} else {
			return response()->json(['code'	=> $code]);
		}
	}

	public function getTmpCode($item, $item_nm)
	{
		$data	= "";
		$query	= " select code_id from __tmp_code where code_kind_cd = :item and use_yn = 'Y' and code_val = :item_nm ";
		$row	= DB::selectOne($query, ['item' => $item, 'item_nm' => $item_nm]);

		if(!empty($row)) {
			$data	= $row->code_id;
		}

		return  $data;
	}

}
