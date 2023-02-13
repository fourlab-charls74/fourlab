<?php

namespace App\Http\Controllers\shop\sale;

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

	protected $tmp_ord_no;
	protected $updated_tmp_ord_row;

	//
	public function index() {

        // $mutable	= now();
        // $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		// //매장구분
		// $sql		= " 
		// 	select 
		// 	* from __tmp_code 
		// 	where 
		// 		code_kind_cd = 'com_type' and use_yn = 'Y' order by code_seq 
		// ";
		// $com_types	= DB::select($sql);

		// //행사구분
		// $sql	= "
		// 	select
		// 	* from __tmp_code
		// 	where
		// 		code_kind_cd = 'event_cd' and use_yn = 'Y' order by code_seq
		// ";
		// $event_cds	= DB::select($sql);

		// //판매유형
		// $sql	= "
		// 	select
		// 	* from __tmp_code
		// 	where
		// 		code_kind_cd = 'sell_type' and use_yn = 'Y' order by code_seq
		// ";
		// $sell_types	= DB::select($sql);



		// $sql		= " select * from __tmp_code_kind order by code_kind_nm ";
		// $code_kinds	= DB::select($sql);


		// $values = [
        //     'sdate'         => $sdate,
        //     'edate'         => date("Y-m-d"),
		// 	'com_types'		=> $com_types,
		// 	'event_cds'		=> $event_cds,
		// 	'sell_types'	=> $sell_types,
		// 	'code_kinds'	=> $code_kinds,
		// ];

		// return view( Config::get('shop.shop.view') . '/sale/sal01',$values);

		/* shop 미사용 메뉴 메인페이지로 리다이렉트 */
        return redirect('/shop');
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
			-- left outer join code g on g.code_kind_cd = 'PR_CODE' and g.code_id = a.event_cd
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
		$sale_kinds = SLib::getCodes("SALE_KIND");
		return view( Config::get('shop.shop.view') . '/sale/sal01_show',$values);
	}

	public function upload(Request $request)
	{
		$save_path = "data/store/sale/sal01/";
		if (!Storage::disk('public')->exists($save_path)) {
			Storage::disk('public')->makeDirectory($save_path);
		}
		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		} else {
			$file = sprintf("data/store/sale/sal01/%s", $_FILES['file']['name']);
			$tmp_name = $_FILES['file']['tmp_name'];
			move_uploaded_file($tmp_name, $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
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

		return $data;
	}

	/**
	 * 전체를 한번에 처리하는 방식
	 */
	public function update(Request $request)
	{
		ini_set('max_execution_time', '36000');
		ini_set('max_input_vars' , 700000);
		// set_time_limit(0);

		$orders = $request->input('data');

		$codes = [];
		foreach ($orders as $order) {
			$order = (array)$order;

			/**
			 * 기존 tmp order에 ord_no가 없는 경우 insert, 있는 경우 update 처리
			 */
			DB::beginTransaction();
			try {
				$saved_type = $this->saveTmpOrder($order);
			} catch (Exception $e) { // 임시 주문서 저장시 문제 발생한 경우 에러 처리
				DB::rollback();
				$code = -400;
				goto pass_saved_order;
			}

			if ($saved_type == "insert") {
				$code = $this->insertOrder($order);
			} else if ($saved_type == "update") {
				$code = $this->updateOrder($order);
				if ($code == -201) { // 주문 관련 테이블에 초기 데이터 값들이 존재하지 않을 경우 재삽입 처리
					$code = $this->insertOrder($order);
				}
			}

			($code == 200 || $code == 201) ? DB::commit() : DB::rollBack(); // 추가 또는 수정이 완료된 경우 commit하여 DB 반영
			pass_saved_order:
			array_push($codes, $code);
		}

		return response()->json(['codes' => $codes]);
	}

	/**
	 * 데이터 1개당 처리하는 방식
	 */
	// public function update(Request $request)
	// {
	// 	ini_set('max_execution_time', '600');
	// 	// set_time_limit(0);

	// 	$order = $request->input('data');
	// 	$order = (array)$order;

	// 	/**
	// 	 * 기존 tmp order에 ord_no가 없는 경우 insert, 있는 경우 update 처리
	// 	 */
	// 	DB::beginTransaction();
	// 	try {
	// 		$saved_type = $this->saveTmpOrder($order);
	// 	} catch (Exception $e) { // 임시 주문서 저장시 문제 발생한 경우 에러 처리
	// 		DB::rollback();
	// 		$code = -400;
	// 		return response()->json(['code'	=> $code]);
	// 	}
		
	// 	if ($saved_type == "insert") {
	// 		$code = $this->insertOrder($order);
	// 	} else if ($saved_type == "update") {
	// 		$code = $this->updateOrder($order);
	// 		if ($code == -201) { // 주문 관련 테이블에 초기 데이터 값들이 존재하지 않을 경우 재삽입 처리
	// 			$code = $this->insertOrder($order);
	// 		}
	// 	}

	// 	($code == 200 || $code == 201) ? DB::commit() : DB::rollBack(); // 추가 또는 수정이 완료된 경우 commit하여 DB 반영
	// 	return response()->json(['code'	=> $code]);
	// }

	/**
	 * 임시주문데이터 저장
	 */
	public function saveTmpOrder($order)
	{
		$saved_type = "";

		$ord_date		= $order["ord_date"];
		$com_type_nm	= $order["com_type_nm"];
		$com_type		= $this->getTmpCode('com_type', $com_type_nm);
		$com_id			= $order["store_cd"];
		$com_nm			= $order["store_nm"];
		$receipt_no		= $order["receipt_no"];
		$seq			= $order["seq"];
		$style_no		= $order["style_no"];
		$opt_kind_nm	= $order["opt_kind_nm"];
		$opt_kind		= $this->getTmpCode('opt_kind_cd', $opt_kind_nm);
		$brand_nm		= $order["brand_nm"];
		$brand			= $this->getTmpCode('brand', $brand_nm);
		$goods_code		= $order["goods_code"];
		$goods_nm		= $order["goods_nm"];
		$color			= $order["color"];
		$color_nm		= $order["color_nm"];
		$size			= $order["size"];
		$size_nm		= $order["size_nm"];
		$stat_pay_type_nm	= $order["pay_type"];
		$stat_pay_type	= $this->getTmpCode('stat_pay_type', $stat_pay_type_nm);
		$goods_sh		= Lib::uncm($order["goods_sh"]);
		$price			= Lib::uncm($order["price"]);
		$wonga			= Lib::uncm($order["wonga"]);
		$sell_type_nm	= $order["sale_kind"];
		$sell_type		= $this->getTmpCode('sell_type', $sell_type_nm);
		$ord_amt		= Lib::uncm($order["ord_amt"]);
		$qty			= Lib::uncm($order["qty"]);
		$recv_amt		= Lib::uncm($order["recv_amt"]);
		$act_amt		= Lib::uncm($order["act_amt"]);
		$event_kind_nm	= $order["pr_code_val"];
		$event_kind		= $this->getTmpCode('event_cd', $event_kind_nm);
		$pay_fee		= $order["pay_fee"];
		$store_pay_fee	= $order["store_pay_fee"];
		$user_id		= $order["user_id"];
		$ord_nm			= $order["ord_nm"];
		$ord_nm2		= $order["ord_nm2"];
		$comment		= $order["comment"];
		$barcode		= $order["barcode"];
		$admin_nm		= $order["admin_nm"];
		$reg_date		= $order["reg_date"];

		$ord_p_date		= str_replace("-","",$ord_date);
		$ord_p_seq		= ( strlen($seq) <= 2 ) ? sprintf('%02d', $seq) : $seq;

		$ord_no			= $com_id . $ord_p_date . $receipt_no . $ord_p_seq;

		if ( $com_id != "" ) {
			$query	= " select com_nm from __tmp_store where com_id = :com_id ";
			$row	= DB::selectOne($query, ['com_id' => $com_id]);

			if (!empty($row)) {
				$com_nm	= $row->com_nm;
			}
		}

		$sql_data = [
			'ord_no'			=> $ord_no,
			'ord_date'			=> $ord_date,
			'com_type'			=> $com_type,
			'com_id'			=> $com_id,
			'com_nm'			=> $com_nm,
			'receipt_no'		=> $receipt_no,
			'seq'				=> $seq,
			'style_no'			=> $style_no,
			'opt_kind'			=> $opt_kind,
			'brand'				=> $brand,
			'goods_code'		=> $goods_code,
			'goods_nm'			=> $goods_nm,
			'color'				=> $color,
			'color_nm'			=> $color_nm,
			'size'				=> $size,
			'size_nm'			=> $size_nm,
			'stat_pay_type'		=> $stat_pay_type,
			'goods_sh'			=> $goods_sh,
			'price'				=> $price,
			'wonga'				=> $wonga,
			'sell_type'			=> $sell_type,
			'ord_amt'			=> $ord_amt,
			'qty'				=> $qty,
			'recv_amt'			=> $recv_amt,
			'act_amt'			=> $act_amt,
			'event_kind'		=> $event_kind,
			'pay_fee'			=> $pay_fee,
			'store_pay_fee'		=> $store_pay_fee,
			'user_id'			=> $user_id,
			'ord_nm'			=> Lib::quote($ord_nm),
			'ord_nm2'			=> Lib::quote($ord_nm2),
			'comment'			=> Lib::quote($comment),
			'barcode'			=> $barcode,
			'admin_nm'			=> Lib::quote($admin_nm),
			'reg_date'			=> $reg_date
		];

		$query	= " select count(*) as cnt from __tmp_order where ord_no = :ord_no ";
		$rows	= DB::selectOne($query, ['ord_no' => $ord_no]);

		if ( $rows->cnt == 0 ) {
			$sql	= "
				insert into __tmp_order( ord_no, ord_date, com_type, com_id, com_nm, receipt_no, seq, style_no, opt_kind, brand, goods_code, goods_nm, color, color_nm, size, size_nm, stat_pay_type, goods_sh, price, wonga, sell_type, ord_amt, qty, recv_amt, act_amt, event_cd, pay_fee, store_pay_fee, user_id, ord_nm, ord_nm2, comment, barcode, admin_nm, reg_date )
				values ( :ord_no, :ord_date, :com_type, :com_id, :com_nm, :receipt_no, :seq, :style_no, :opt_kind, :brand, :goods_code, :goods_nm, :color, :color_nm, :size, :size_nm, :stat_pay_type, :goods_sh, :price, :wonga, :sell_type, :ord_amt, :qty, :recv_amt, :act_amt, :event_kind, :pay_fee, :store_pay_fee, :user_id, :ord_nm, :ord_nm2, :comment, :barcode, :admin_nm, :reg_date )
			";
			DB::insert($sql, $sql_data);
			$saved_type = "insert";
			$this->tmp_ord_no = $ord_no;
		} else {
			$sql	= "
				update __tmp_order set
					ord_date		= :ord_date,
					com_type		= :com_type,
					com_id			= :com_id,
					com_nm			= :com_nm,
					receipt_no		= :receipt_no,
					seq				= :seq,
					style_no		= :style_no,
					opt_kind		= :opt_kind,
					brand			= :brand,
					goods_code		= :goods_code,
					goods_nm		= :goods_nm,
					color			= :color,
					color_nm		= :color_nm,
					size			= :size,
					size_nm			= :size_nm,
					stat_pay_type	= :stat_pay_type,
					goods_sh		= :goods_sh,
					price			= :price,
					wonga			= :wonga,
					sell_type		= :sell_type,
					ord_amt			= :ord_amt,
					qty				= :qty,
					recv_amt		= :recv_amt,
					act_amt			= :act_amt,
					event_cd		= :event_kind,
					pay_fee			= :pay_fee,
					store_pay_fee	= :store_pay_fee,
					user_id			= :user_id,
					ord_nm			= :ord_nm,
					ord_nm2			= :ord_nm2,
					comment			= :comment,
					barcode			= :barcode,
					admin_nm		= :admin_nm,
					reg_date		= :reg_date
				where
					ord_no		= :ord_no
			";
			DB::update($sql, $sql_data);
			$saved_type = "update";
			$this->tmp_ord_no = $ord_no;
			$this->updated_tmp_ord_row = DB::table('__tmp_order')->where('ord_no', $ord_no)->get()->all()[0];
		}
		return $saved_type;
	}

	public function insertOrder($order)
	{
		$ord_no = "";
		$admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
		$code = 0;

		$tmp_ord_no = $this->tmp_ord_no;
		
		/**
		 * prd_cd 설정 (자사바코드 있는 경우 자사바코드 사용, 없는 경우 상품코드 + 칼라 + 사이즈로 상품코드 설정)
		 */
		$prd_cd = $order['barcode'] ? $order['barcode'] : $order['goods_code'] . $order['color'] . $order['size'];

		/**
		 * 매출구분 자료 변환 작업
		 */
		$pay_type = $order['pay_type'];
		$pay_type = 1;
		if (preg_match("/현금/", $pay_type)) {
			$pay_type = 1;
		} else if (preg_match("/카드/", $pay_type)) {
			$pay_type = 2;
		} else if (preg_match("/포인트/", $pay_type)) {
			$pay_type = 4;
		} else {
		}

		/**
		 * 판매유형 자료 변환 작업
		 */
		$sale_kind = $order['sale_kind'];
		$sale_kind = str_replace(" ", "", $sale_kind); // 공백 제거
		if (preg_match("/쿠폰/", $sale_kind)) {
			if (preg_match("/\d+%/i", $sale_kind, $matches)) {
				$percent = $matches[0];
				$sale_kind = "쿠폰판매(${percent})";
			}
		} else if (preg_match("/할인/", $sale_kind)) {
			if (preg_match("/\d+%/i", $sale_kind, $matches)) {
				$percent = $matches[0];
				$sale_kind = "${percent}할인";
			}
		}
		// else if (preg_match("/프로모션/", $sale_kind)) {
		// 	if (preg_match("/\d+%/i", $sale_kind, $matches)) {
		// 		$percent = $matches[0];
		// 		$sale_kind = "브랜드데이(${percent})";
		// 	}
		// }

		$sale_kind_id = "99"; // 조건에 없는 경우 기타로 처리
		$sale_kinds = SLib::getCodes("SALE_KIND")->all();
		for ($i=0; $i<count($sale_kinds); $i++) {

			$item = $sale_kinds[$i];
			$id = $item->code_id;
			$value = $item->code_val;
			$value = str_replace(" ", "", $value); // 기존 항목들 공백 제거하여 입력하는 판매유형과 비교

			if ($sale_kind == $value) {
				$sale_kind_id = $id;
				break;
			}
		}

		/**
		 * 행사구분 자료 변환 작업
		 */
		$pr_code = 'JS'; // 행사구분 기본 값: 정상
		$pr_code_val = $order['pr_code_val'];

		$sql = /** @lang text */
		"
			select * from `code` where code_kind_cd = 'pr_code'
		";
		$result = DB::select($sql);
		for ($i=0; $i<count($result); $i++) {
			$item = $result[$i];
			$id = $item->code_id;
			$value = $item->code_val;
			if ($pr_code_val == $value) {
				$pr_code = $id; break;
			}
		}

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

		// 임시방편 - 매칭이 안된 상품 0 처리
		if ($result) {
			$order['goods_no'] = @$result->goods_no;
		} else {
			$order['goods_no'] = 0;
		}

		/**
		 * 초기 값 설정
		 */
		$order['goods_sub'] = 0;
		$order['out_ord_no'] = 0;
		$order['dlv_amt'] = 0;
		$order['point_amt'] = 0;
		$order['coupon_amt'] = 0;

		// 영수증번호 / SEQ 전부 0 으로 처리
		$order['receipt_no'] = 0;
		$order['seq'] = 0;
		
		$order['com_rate'] = 0;
		$order['ord_kind'] = 20;
		$order['ord_type'] = 15; // 정상:15 (code 테이블 > code_kind_cd = 'G_ORD_TYPE')

		$order_states = [5, 10, 30];
		$order["ord_state"] = $order_states[2];

		$order["pay_stat"] = 1;
		$order['coupon_no'] = 0;
		$order['com_coupon_ratio'] = 0;
		$order['sales_com_fee'] = $order['pay_fee'];
		$store_cd = $order['store_cd'];
		
		$order['sale_place'] = $order['store_nm'];
		$order['user_nm'] = $order['ord_nm'] ? $order['ord_nm']: "비회원";

		$ord_date = $order["ord_date"];
		$ord_state_date = Carbon::parse($order["ord_date"])->format('Ymd');

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
		if (@$order["goods_no"] === "") {
			@$order["goods_no"] == "0"; // 상품번호 없는경우 일단 0 처리 - 임시
			// $code = "-101";
		} else if (@$order["goods_opt"] == "") {
			$code = "-102";
		} else if (@$order["qty"] == "") {
			$code = "-106";
		} else if (@$order["ord_amt"] == "") {
			$code = "-107";
		}

		if ($code === 0) {

			$sql = /** @lang text */
				"
					select
						a.head_desc, a.goods_nm, a.style_no, a.md_id, a.md_nm, b.com_nm, a.com_id,
						a.baesong_kind, a.baesong_price, b.pay_fee/100 as com_rate, a.price, a.com_type,
						a.is_unlimited
					from goods a left outer join company b on a.com_id  = b.com_id
					where a.goods_no = :goods_no 
				";
			$row = (array)DB::selectOne($sql, array("goods_no" => $order["goods_no"]));
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
			} else {
				// Lib::q($sql);
				return ["code" => "-210"];
			}

			$order["clm_state"] = 0;
			$ord_seq = 0;

			try {

				$orderClass = new Order([
					"id" => $admin_id,
					"name" => $admin_nm
				]);

				list($usec, $sec) = explode(" ", microtime());
				$f_ord_date = Carbon::parse($ord_date)->format("Ymd");
				$f_ord_date_2 = Carbon::parse($ord_date)->format("Y-m-d H:i:s");

				$ord_no_front = $f_ord_date != "" ? $f_ord_date . $store_cd : date("Ymd") . $store_cd; // 주문번호 앞 자리
				$ord_no_back = round($usec * 1000000, 0); // 주문번호 맨 뒤 6자리 숫자에 해당

                $ord_no = sprintf("%s%06d", $ord_no_front, $ord_no_back);
				$orderClass->SetOrdNo($ord_no);

				$order_mst = [
					"ord_no"		=> $ord_no,
					"store_cd"      => $store_cd,
					"ord_date"      => $ord_date,
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
					"sale_place" 	=> @$order["sale_place"],
					"out_ord_no" 	=> @$order["out_ord_no"],
					"upd_date"      => $ord_date,
					"dlv_end_date"  => $ord_date
				];
				DB::table('order_mst')->insert($order_mst);

				$payment = [
					"ord_no"		=> $ord_no,
					"pay_type" 		=> $pay_type,
					"pay_nm" 		=> $order["user_nm"],
					"pay_amt" 		=> $order["ord_amt"],
					"pay_stat" 		=> @$order["pay_stat"],
					"bank_inpnm" 	=> Lib::getValue($order, "bank_inpnm", ""),
					"bank_code" 	=> Lib::getValue($order, "bank_code", ""),
					"bank_number" 	=> Lib::getValue($order, "bank_number", ""),
					"ord_dm"        => $f_ord_date . "000000",
					"pay_date"		=> $f_ord_date_2,
					"upd_dm"        => $f_ord_date . "000000"
				];
				DB::table('payment')->insert($payment);
				
				$order_opt = [
					"store_cd"      => $store_cd,
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
					"pay_type"		=> $pay_type,
					"dlv_pay_type" 	=> @$order["dlv_pay_type"],
					"dlv_amt" 		=> $order["dlv_amt"],
					"dc_amt" 		=> $order["dc_amt"],
					"point_amt" 	=> 0,
					"coupon_amt" 	=> 0,
					"recv_amt" 		=> $order["recv_amt"],
					"md_id" 		=> $order["md_id"],
					"md_nm" 		=> $order["md_nm"],

					"sale_kind" 	=> $sale_kind_id,
					"sale_place" 	=> @$order["sale_place"],
					"ord_state" 	=> $order["ord_state"],
					"clm_state" 	=> $order["clm_state"],
					"com_id" 		=> $order["com_id"],
					"ord_kind" 		=> @$order["ord_kind"],
					"ord_type" 		=> @$order["ord_type"],
					"baesong_kind" 	=> $order["baesong_kind"],

					"dlv_comment" 	=> @$order["dlv_comment"],
					"dlv_end_date"  => $ord_date,
					"admin_id" 		=> $admin_id,
					"sales_com_fee" => @$order["sales_com_fee"],
					"ord_date"      => $ord_date,
					'prd_cd'        => $prd_cd,
					"pr_code"		=> $pr_code
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

				$orderClass->SetOrdOptNo($ord_opt_no);

				// 주문상태 30 이하의 5, 10, 30 이 전부 order_opt_wonga에 저장되도록 수정
				foreach ($order_states as $value) {
					$order_opt_wonga = array(
						"store_cd" => $store_cd,
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
						"ord_state" => $value,
						"ord_kind"	=> $order['ord_kind'],
						"ord_type" => $order['ord_type'],
						"coupon_no" => $order['coupon_no'],
						"com_coupon_ratio" => $order['com_coupon_ratio'],
						"sales_com_fee" => $order['sales_com_fee'],
						'ord_state_date' => $ord_state_date,
						'prd_cd' => $prd_cd,
						'store_cd' => $store_cd
					);
					$orderClass->__InsertOptWonga($order_opt_wonga);
				}

				// insert 후 __tmp_order의 out_ord_opt_no 업데이트
				DB::table('__tmp_order')->where('ord_no', $tmp_ord_no)->update(['out_ord_opt_no' => $ord_opt_no]);
				$code = 201;
			} catch (Exception $e) {
				$code = -410;
			}
			return $code;
		} else {
			return $code;
		}
	}

	public function updateOrder($order)
	{
		$admin_id = Auth('head')->user()->id;
		$code = 0;

		$updated_tmp_ord_row = $this->updated_tmp_ord_row;
		
		/**
		 * prd_cd 설정 (자사바코드 있는 경우 자사바코드 사용, 없는 경우 상품코드 + 칼라 + 사이즈로 상품코드 설정)
		 */
		$prd_cd = $order['barcode'] ? $order['barcode'] : $order['goods_code'] . $order['color'] . $order['size'];

		/**
		 * 매출구분 자료 변환 작업
		 */
		$pay_type = $order['pay_type'];
		$pay_type = 1;
		if (preg_match("/현금/", $pay_type)) {
			$pay_type = 1;
		} else if (preg_match("/카드/", $pay_type)) {
			$pay_type = 2;
		} else if (preg_match("/포인트/", $pay_type)) {
			$pay_type = 4;
		} else {
		}

		/**
		 * 판매유형 자료 변환 작업
		 */
		$sale_kind = $order['sale_kind'];
		$sale_kind = str_replace(" ", "", $sale_kind); // 공백 제거
		if (preg_match("/쿠폰/", $sale_kind)) {
			if (preg_match("/\d+%/i", $sale_kind, $matches)) {
				$percent = $matches[0];
				$sale_kind = "쿠폰판매(${percent})";
			}
		} else if (preg_match("/할인/", $sale_kind)) {
			if (preg_match("/\d+%/i", $sale_kind, $matches)) {
				$percent = $matches[0];
				$sale_kind = "${percent}할인";
			}
		}
		// else if (preg_match("/프로모션/", $sale_kind)) {
		// 	if (preg_match("/\d+%/i", $sale_kind, $matches)) {
		// 		$percent = $matches[0];
		// 		$sale_kind = "브랜드데이(${percent})";
		// 	}
		// }

		$sale_kind_id = "99"; // 조건에 없는 경우 기타로 처리
		$sale_kinds = SLib::getCodes("SALE_KIND")->all();
		for ($i=0; $i<count($sale_kinds); $i++) {

			$item = $sale_kinds[$i];
			$id = $item->code_id;
			$value = $item->code_val;
			$value = str_replace(" ", "", $value); // 기존 항목들 공백 제거하여 입력하는 판매유형과 비교

			if ($sale_kind == $value) {
				$sale_kind_id = $id;
				break;
			}
		}

		/**
		 * 행사구분 자료 변환 작업
		 */
		$pr_code = 'JS'; // 행사구분 기본 값: 정상
		$pr_code_val = $order['pr_code_val'];

		$sql = /** @lang text */
		"
			select * from `code` where code_kind_cd = 'pr_code'
		";
		$result = DB::select($sql);
		for ($i=0; $i<count($result); $i++) {
			$item = $result[$i];
			$id = $item->code_id;
			$value = $item->code_val;
			if ($pr_code_val == $value) {
				$pr_code = $id; break;
			}
		}

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

		// 임시방편 - 매칭이 안된 상품 0 처리
		if ($result) {
			$order['goods_no'] = @$result->goods_no;
		} else {
			$order['goods_no'] = 0;
		}

		/**
		 * 초기 값 설정
		 */
		$order['goods_sub'] = 0;
		$order['out_ord_no'] = '';
		$order['dlv_amt'] = 0;
		$order['point_amt'] = 0;
		$order['coupon_amt'] = 0;

		// 영수증번호 / SEQ 전부 0 으로 처리
		$order['receipt_no'] = 0;
		$order['seq'] = 0;
		
		$order['com_rate'] = 0;
		$order['ord_kind'] = 20;
		$order['ord_type'] = 15; // 정상:15 (code 테이블 > code_kind_cd = 'G_ORD_TYPE')

		$order_states = [5, 10, 30];
		$order["ord_state"] = $order_states[2];

		$order["pay_stat"] = 1;
		$order['coupon_no'] = 0;
		$order['com_coupon_ratio'] = 0;
		$order['sales_com_fee'] = $order['pay_fee'];
		$store_cd = $order['store_cd'];
		
		// $order['sale_place'] = $order['store_nm'];
		$order['sale_place'] = "HEAD_OFFICE";

		$order['user_nm'] = $order['ord_nm'] ? $order['ord_nm']: "비회원";

		$ord_date = $order["ord_date"];
		$ord_state_date = Carbon::parse($order["ord_date"])->format('Ymd');

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
		if (@$order["goods_no"] === "") {
			@$order["goods_no"] == "0"; // 상품번호 없는경우 일단 0 처리 - 임시
			// $code = "-101";
		} else if (@$order["goods_opt"] == "") {
			$code = "-102";
		} else if (@$order["qty"] == "") {
			$code = "-106";
		} else if (@$order["ord_amt"] == "") {
			$code = "-107";
		}

		if ($code === 0) {

			$sql = /** @lang text */
				"
					select
						a.head_desc, a.goods_nm, a.style_no, a.md_id, a.md_nm, b.com_nm, a.com_id,
						a.baesong_kind, a.baesong_price, b.pay_fee/100 as com_rate, a.price, a.com_type,
						a.is_unlimited
					from goods a left outer join company b on a.com_id  = b.com_id
					where a.goods_no = :goods_no 
				";
			$row = (array)DB::selectOne($sql, array("goods_no" => $order["goods_no"]));
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
			} else {
				// Lib::q($sql);
				return ["code" => "-210"];
			}

			$order["clm_state"] = 0;
			$ord_seq = 0;

			try {
				$ord_opt_no = $updated_tmp_ord_row->out_ord_opt_no;
				DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->update([
					"store_cd"      => $store_cd,
					"goods_no"		=> $order["goods_no"],
					"goods_sub" 	=> $order["goods_sub"],
					"ord_seq" 		=> $ord_seq,
					"head_desc" 	=> Lib::getValue($order, "head_desc", ""),
					"goods_nm" 		=> $order["goods_nm"],
					"goods_opt" 	=> $order["goods_opt"],
					"qty"			=> $order["qty"],
					"price" 		=> $order["price"],
					"wonga"			=> $order["wonga"],
					"pay_type"		=> $pay_type,
					"dlv_pay_type" 	=> @$order["dlv_pay_type"],
					"dlv_amt" 		=> $order["dlv_amt"],
					"dc_amt" 		=> $order["dc_amt"],
					"point_amt" 	=> 0,
					"coupon_amt" 	=> 0,
					"recv_amt" 		=> $order["recv_amt"],
					"md_id" 		=> $order["md_id"],
					"md_nm" 		=> $order["md_nm"],

					"sale_kind" 	=> $sale_kind_id,
					"sale_place" 	=> @$order["sale_place"],
					"ord_state" 	=> $order["ord_state"],
					"clm_state" 	=> $order["clm_state"],
					"com_id" 		=> $order["com_id"],
					"ord_kind" 		=> @$order["ord_kind"],
					"ord_type" 		=> @$order["ord_type"],
					"baesong_kind" 	=> $order["baesong_kind"],

					"dlv_comment" 	=> @$order["dlv_comment"],
					"admin_id" 		=> $admin_id,
					"sales_com_fee" => @$order["sales_com_fee"],
					"ord_date"      => $ord_date,
					"prd_cd"        => $prd_cd,
					"pr_code"		=> $pr_code
				]);
				$updated_row = DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->first();
				if ($updated_row == null) return $code = -201;


				$ord_no = $updated_row->ord_no;

				DB::table('order_mst')->where('ord_no', $ord_no)->update([
					"store_cd"      => $store_cd,
					"ord_date"      => $ord_date,
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
					"sale_place" 	=> @$order["sale_place"],
					"out_ord_no" 	=> @$order["out_ord_no"],
					"upd_date"      => $ord_date,
					"dlv_end_date"  => $ord_date
				]);

				$updated_row = DB::table('order_mst')->where('ord_no', $ord_no)->first();
				if ($updated_row == null) return $code = -201;

				$f_ord_date = Carbon::parse($ord_date)->format("YmdHis");
				$f_ord_date_2 = Carbon::parse($ord_date)->format("Y-m-d H:i:s");

				DB::table('payment')->where('ord_no', $ord_no)->update([
					"pay_type" 		=> $pay_type,
					"pay_nm" 		=> $order["user_nm"],
					"pay_amt" 		=> $order["ord_amt"],
					"pay_stat" 		=> @$order["pay_stat"],
					"bank_inpnm" 	=> Lib::getValue($order, "bank_inpnm", ""),
					"bank_code" 	=> Lib::getValue($order, "bank_code", ""),
					"bank_number" 	=> Lib::getValue($order, "bank_number", ""),
					"ord_dm"        => $f_ord_date . "000000",
					"pay_date"		=> $f_ord_date_2,
					"upd_dm"        => $f_ord_date . "000000"
				]);

				$updated_row = DB::table('payment')->where('ord_no', $ord_no)->first();
				if ($updated_row == null) return $code = -201;

				// 주문상태 30 이하의 5, 10, 30 이 전부 order_opt_wonga에 저장되도록 수정
				foreach ($order_states as $value) {
					$order_opt_wonga = array(
						"store_cd" => $store_cd,
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
						"ord_kind"	=> $order['ord_kind'],
						"ord_type" => $order['ord_type'],
						"coupon_no" => $order['coupon_no'],
						"com_coupon_ratio" => $order['com_coupon_ratio'],
						"sales_com_fee" => $order['sales_com_fee'],
						"ord_state_date" => $ord_state_date,
						"prd_cd" => $prd_cd,
						"store_cd" => $store_cd
					);
					DB::table('order_opt_wonga')->where([
						['ord_opt_no', '=', $ord_opt_no],
						['ord_state', '=', $value]
					])->update($order_opt_wonga);

					$updated_row = DB::table('order_opt_wonga')->where([
						['ord_opt_no', '=', $ord_opt_no],
						['ord_state', '=', $value]
					])->first();
					if ($updated_row == null) return $code = -201;
				}
				$code = 200;
			} catch (Exception $e) {
				$code = -420;
			}
			return $code;
		} else {
			return $code;
		}
	}
}