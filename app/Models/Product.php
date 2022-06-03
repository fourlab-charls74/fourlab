<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;
use Exception;

class Product
{
	private $user;
	private $goods_no;
	private $goods_sub;
	private $goods_opt;
	private $com_id;
	private $com_type;
	private $goods_type;
	private $opt_price;
	private $opt_name;
	private $opt_seq;
	private $loc;

	public function __construct($user)
	{
		$this->user = $user;
	}

	/*
	  Function: __SetStockNo
	  재고 일련 번호 설정
	*/
	private function __SetStockNo($stock_no)
	{
		$this->stock_no = $stock_no;
	}

	/*
	  Function: __InsertHistory
	  재고 변동 내역 등록

	  Parameters:
		$data - array

	  Returns:
		$data_no - 재고 변동 내역 일련 번호
	*/
	private function __InsertHistory($data)
	{
		return DB::table('goods_history')->insertGetId([
			'goods_no'  => $this->goods_no,
			'goods_sub' => $this->goods_sub,
			'goods_opt' => $this->goods_opt,
			'wonga' => $data['wonga'],
			'type'  => $data['type'],
			'stock_state' => 1,
			'qty' => $data['qty'],
			'loc' => $this->loc,
			'etc' => $data['etc'],
			'ord_opt_no' => $data['ord_opt_no'],
			'invoice_no' => $data['invoice_no'],
			'admin_id' => $this->user['id'],
			'admin_nm' => $this->user['name'],
			'com_id' => $data['com_id'],
			'ord_no' => $data['ord_no'],
			'stock_state_date' => now(),
			'regi_date' => now()
		]);
	}
	/*
	  Function: __InsertGoodQty
	  재고 정보 등록

	  Parameters:
		$wonga - 상품 원가
		$qty - 등록 수량
		$invoice_no - 인보이스 번호
	*/
	private function __InsertGoodQty($wonga, $qty, $invoice_no)
	{
		try {
			DB::table('goods_good')->insert([
				'goods_no' => $this->goods_no,
				'goods_sub' => $this->goods_sub,
				'goods_opt' => $this->goods_opt,
				'wonga' => $wonga,
				'qty' => $qty,
				'invoice_no' => $invoice_no,
				'init_qty' => $qty,
				'regi_date' => now()
			]);

			return 1;
		} catch (Exception $e) {
			return 0;
		}
	}

	/*
	  Function: __IncreaseGoodQty
	  선택된 재고 수량 증가

	  Returns:
		$affected_row - 수정된 열 수
	*/
	private function __IncreaseGoodQty($qty)
	{
		try {
			return DB::update("
		  update goods_good set
			qty = qty + $qty
		  where no = '$this->stock_no'
		");
		} catch (Exception $e) {
			return 0;
		}
	}

	/*
	  Function: __DecreaseGoodQty
	  선택된 재고 수량 차감

	  Returns:
		$affected_row - 수정된 열 수
	*/
	private function __DecreaseGoodQty($qty)
	{
		if ($qty <= 0) return 0;

		try {
			return DB::update("
		  update goods_good set
			qty = qty - $qty
		  where no = '$this->stock_no'
		");
		} catch (Exception $e) {
			return 0;
		}
	}

	/*
	  Function: __DecreaseSummaryQty
	  재고 집계 정보 수량 차감

	  Parameters:
		$qty - 차감 수량

	  Returns:
		$affected_row - 수정된 행 수
	*/
	private function __DecreaseSummaryQty($qty)
	{
		try {
			return DB::update(" 
		  -- [" . $this->user["id"] . "] " . __FILE__ . " > " . __FUNCTION__ . " > " . __LINE__ . "
		  update goods_summary set
			wqty = if( (wqty - $qty) < 0, 0, (wqty - $qty))
			, last_date = now()
		  where goods_no = '$this->goods_no'
			and goods_sub = '$this->goods_sub'
			and goods_opt = '$this->goods_opt'
		");
		} catch (Exception $e) {
			return 0;
		}
	}

	/*
	  Function: __IncreaseSummaryQty
	  재고 집계 정보 수량 증가

	  Parameters:
		$qty - 증가 수량

	  Returns:
		$affected_row - 수정된 행 수
	*/
	private function __IncreaseSummaryQty($qty)
	{
		$affected_rows = DB::update("
		  update goods_summary set
			wqty = wqty + $qty
			, last_date = now()
		  where goods_no = '$this->goods_no'
			and goods_sub = '$this->goods_sub'
			and goods_opt = '$this->goods_opt'
		");

		if ($affected_rows > 0) return $affected_rows;

		try {
			DB::table('goods_summary')->insert([
				'goods_no' => $this->goods_no,
				'goods_sub' => $this->goods_sub,
				'opt_name' => $this->opt_name,
				'goods_opt' => $this->goods_opt,
				'opt_price' => $this->opt_price,
				'good_qty' => '0',
				'wqty' => $qty,
				'soldout_yn' => 'N',
				'use_yn' => 'Y',
				'seq' => $this->opt_seq,
				'rt' => now(),
				'ut' => now(),
				'bad_qty' => 0,
				'last_date' => now()
			]);

			return 1;
		} catch (Exception $e) {
			return 0;
		}
	}

	/*
	  Function: __GetPartnerGoodsWonga
	  입점업체 상품의 원가 내역 테이블에서 상품의 원가 얻기

	  Parameters:
		$com_id - 업체 아이디
		$price - 상품의 판매가격
		$ord_date - 주문 일자

	  Returns:
		$wonga - 원가
	*/
	private function __GetPartnerGoodsWonga($com_id, $price, $ord_date)
	{

		$sql = " -- [" . $this->user["id"] . "] " . __FILE__ . " > " . __FUNCTION__ . " > " . __LINE__ . "
		select wonga
		from goods_wonga
		where goods_no = '$this->goods_no'
		  and goods_sub ='$this->goods_sub'
		  -- and com_id = '$com_id'
		  and price = '$price'
		  and sdate <= '$ord_date'
		  and edate >= '$ord_date'
		order by regi_date desc limit 0,1
	  ";

	  	$row = DB::selectOne($sql);
		$wonga = $row == NULL ? 0 : $row->wonga;

		return $wonga;
	}

	function SetGoodsNo($goods_no)
	{

		$this->goods_no = $goods_no;

		$product = (array)DB::table('goods')->select("com_id", "com_type", "goods_type")
			->where("goods_no", "=", $goods_no)->first();

		$this->com_id   = $product["com_id"];
		$this->com_type = $product["com_type"];
		$this->goods_type = $product["goods_type"];
	}

	function GetNextGoodsNo($goods_no = "")
	{
		DB::table('goods_no')->insert([
			'rt' => DB::raw('now()'),
			'ut' => DB::raw('now()')
		]);
		return  DB::getPdo()->lastInsertId();
	}

	public function Add($product)
	{

		DB::table('goods')->insert($product);

		$goods_no = $product["goods_no"];

		if (isset($product["img"]) && $product["img"] != "") {
			DB::table('goods')
				->where('goods_no', '=', $goods_no)
				->update([
					'img_yn' => 'Y',
					'img_update' => DB::raw('now()')
				]);
		}

		// Property Setting
		$this->SetGoodsNo($goods_no);

		// 매입 상품이 아닌 경우 원가로그 생성
		if ($this->goods_type != "S") {

			$price = Lib::getValue($product, "price", 0);
			$wonga = Lib::getValue($product, "wonga", 0);
			$margin = 0;

			if ($price == 0 || $wonga > $price) {
			} else {
				$margin = sprintf("%01.2f", ((1 - $wonga / $price) * 100));
			}
			$this->CheckWongaLog($price, $wonga, $margin);
		}
	}

	/*
      Function: GetGoods
      상품 정보 얻기

      Parameters:

      Returns:
        goods_info - 상품 상세 내용
    */
	public function GetGoods()
	{
		$sql = "
          select * from goods where goods_no = '$this->goods_no' and goods_sub = '$this->goods_sub'
        ";
		$goods_info = DB::selectOne($sql);
		return $goods_info;
	}

	public function Add2($product)
	{

		try {
			DB::beginTransaction();

			$option_type = array();
			$option = array();
			if (isset($product["option_type"])) {
				$option_type = $product["option_type"];
				if (!is_array($option_type)) {
					$option_type = explode(",", $option_type);
				}
			}
			$is_option_use = (count($option_type) === 0) ? "N" : "Y";
			$product["is_option_use"] = $is_option_use;

			if (isset($product["option"])) {
				$option = $product["option"];
				if (!is_array($option)) {
					$tmp_option = explode(",", $option);
					$option = [];
					for ($i = 0; $i < count($tmp_option); $i++) {
						list($name, $qty) = explode(":", $tmp_option[$i]);
						$option[] = [
							"name" => $name,
							"qty" => $qty,
						];
					}
				}
			}

			unset($product["option_type"]);
			unset($product["option"]);

			$goods_no	= DB::selectOne("select max(goods_no) + 1 as goods_no from goods")->goods_no;
			$goods_sub	= 0;

			//옵션관리 안함 상품의 수량 등록
			if ($is_option_use == "N") {

				$qty = 0;
				if (isset($option[0]["qty"])) {
					$qty = $option[0]["qty"];
				}
				DB::table("goods_option")->insert([
					"goods_no" => $goods_no,
					"goods_sub" => 0,
					"type" => 'basic',
					"kind" => 'S',
					"name" => 'NONE',
					"required_yn" => 'Y',
					"use_yn" => 'Y',
					"seq" => 0,
					"rt" => DB::raw("now()"),
					"ut" => DB::raw("now()")
				]);

				DB::table("goods_summary")->insert([
					"goods_no" => $goods_no,
					"goods_sub" => 0,
					"goods_opt" => 'NONE',
					"opt_name" => 'NONE',
					"opt_price" => 0,
					"good_qty" => $qty,
					"wqty" => $qty,
					"soldout_yn" => 'Y',
					"use_yn" => 'Y',
					"seq" => 0,
					"rt" => DB::raw("now()"),
					"ut" => DB::raw("now()")
				]);
			} else {
				for ($i = 0; $i < count($option_type); $i++) {
					DB::table("goods_option")->insert([
						"goods_no" => $goods_no,
						"goods_sub" => 0,
						"type" => 'basic',
						"kind" => 'S',
						"name" => $option_type[$i],
						"required_yn" => 'Y',
						"use_yn" => 'Y',
						"seq" => 0,
						"rt" => DB::raw("now()"),
						"ut" => DB::raw("now()")
					]);
				}

				for ($i = 0; $i < count($option); $i++) {
					DB::table("goods_summary")->insert([
						"goods_no" => $goods_no,
						"goods_sub" => 0,
						"goods_opt" => $option[$i]["name"],
						"opt_name" => join("^", $option_type),
						"opt_price" => Lib::getValue($option[$i], "price", 0),
						"good_qty" => Lib::getValue($option[$i], "qty", 0),
						"wqty" => Lib::getValue($option[$i], "qty", 0),
						"soldout_yn" => 'Y',
						"use_yn" => 'Y',
						"seq" => 0,
						"rt" => DB::raw("now()"),
						"ut" => DB::raw("now()")
					]);
				}
			}

			array_merge($product, [
				"reg_dm" => DB::raw("now()"),
				"upd_dm" => DB::raw("now()")
			]);

			$product["goods_no"] = $goods_no;
			$product["goods_sub"] = $goods_sub;
			DB::table('goods')->insert($product);

			DB::commit();

			return $goods_no;
		} catch (Exception $e) {
			DB::rollback();
			return [
				"errno" => 1,
				"errmsg" => $e->getMessage()
			];
		}
	}

	public function Edit($goods_no, $product)
	{
		return DB::table('goods')->where('goods_no', '=', $goods_no)->update($product);
	}

	function CheckWongaLog($price, $wonga, $margin)
	{

		//  $ret_flag = false;

		//  // goods_wonga 에서 카운트 확인 : 당연히 있어야하는 값!!
		//  $sql = "
		//   select price, wonga from goods_wonga
		//   where goods_no = '$this->goods_no'
		//     and goods_sub = '$this->goods_sub'
		//     -- and com_id = '$this->com_id'
		//     and edate = '99999999'
		// ";
		//  $rs = $this->conn->Execute($sql);
		//  if(!$rs->EOF){
		//      $rows = $rs->fields;
		//      $_price = $rows["price"];
		//      $_wonga = $rows["wonga"];

		//      if($price != $_price  || $wonga != $_wonga ){

		//          $ret_flag = true;

		//          $this->__FinishWongaLog();
		//          $this->__StartWongaLog($price, $wonga, $margin);
		//      }
		//  }else{
		//      $ret_flag = true;
		//      $this->__StartWongaLog($price, $wonga, $margin);
		//  }
		//  return $ret_flag;
	}


	public function AddOption($type, $name, $required_yn = "Y", $use_yn = "Y", $option_no = "")
	{

		$seq = DB::table('goods_option')
			->where('goods_no', '=', $this->goods_no)
			->where('type', '=', $type)
			->where('name', '=', $name)->count();

		DB::table('goods_option')->insert([
			"goods_no" => $this->goods_no,
			"goods_sub" => 0,
			"type" => $type,
			"name" => $name,
			"required_yn" => $required_yn,
			"use_yn" => $use_yn,
			"seq" => $seq,
			"option_no" => $option_no,
			"rt" => DB::raw('now()'),
			"ut" => DB::raw('now()'),
		]);
	}


	public function AddOptionQty($goods_opt, $qty, $price = 0, $memo = "")
	{

		$cnt = DB::table('goods_summary')
			->where('goods_no', '=', $this->goods_no)
			->where('goods_sub', '=', 0)
			->where('goods_opt', '=', $goods_opt)->count();

		if ($cnt > 0) {

			DB::table('goods_summary')
				->where('goods_no', '=', $this->goods_no)
				->where('goods_sub', '=', 0)
				->where('goods_opt', '=', $goods_opt)
				->update([
					"good_qty" => $qty,
					"price" => $price,
					"memo" => $memo,
				]);
		} else {

			$options = (array)DB::table('goods_option')->select("name")
				->where("goods_no", "=", $this->goods_no)
				->where("goods_sub", "=", 0)
				->where("type", "=", 'basic')
				->get();

			if (count($options) > 1) {
				$opt_name = $options[0]["name"] . "^" . $options[1]["name"];
			} else {
				$opt_name = $options[0]["name"];
			}

			DB::table('goods_summary')->insert([
				"goods_no" => $this->goods_no,
				"goods_sub" => 0,
				"opt_name" => $opt_name,
				"goods_opt" => $goods_opt,
				"opt_price" => $price,
				"opt_memo" => $memo,
				"good_qty" => $qty,
				"wqty" => 0,
				"soldout_yn" => "N",
				"use_yn" => "Y",
				"seq" => 0,
				"rt" => DB::raw('now()'),
				"ut" => DB::raw('now()'),
				"bad_qty" => 0,
				"last_date" => DB::raw('now()')
			]);
		}
	}

	private function IsSalable()
	{
		$qty = DB::table('goods_summary')
			->where('goods_no', '=', $this->goods_no)
			->where('goods_sub', '=', 0)
			->sum('good_qty');

		return ($qty > 0) ? true : false;
	}

	public function UpdateState($state)
	{

		$success = 0;
		$fail = 0;

		if ($state == "40") {
			if ($this->IsSalable()) {
				DB::table('goods')
					->where("goods_no", "=", $this->goods_no)
					->where("goods_sub", "=", 0)
					->update([
						"sale_stat_cl" => $state
					]);
				$success++;
			} else {
				$fail++;
			}
		} else {
			DB::table('goods')
				->where("goods_no", "=", $this->goods_no)
				->where("goods_sub", "=", 0)
				->update([
					"sale_stat_cl" => $state
				]);
			$success++;
		}
		return array($success, $fail);
	}

	/*
	  Function: Minus
		재고 처리( 차감 )

	  Parameters:
		$type - 작업 구분
		$etc - 사유
		$qty - 차감 수량
		$goods_no - 상품 번호
		$goods_sub - 상품 번호 서브
		$goods_opt - 상품 옵션 코드
		$ord_no = "" - 주문 번호
		$ord_opt_no = "" - 주문 일련 번호

	  Returns:
		$ret - 매출 정보( array( "stock_qty", "stock_wonga", "invoice_no", "com_type" ))

	  Variables:
		- $type : 1 발주, 2 일반주문, 3 현장판매, 4 예약, 5 교환, 6 환불, 9 재고조정

	  Comment:
		주문 관련 재고 차감
		재고 조정 과련 재고 차감

	  See Also:
		- <__GetPartnerGoodsWonga>
	*/
	public function Minus($stock)
	{
		// Parameters
		$type = $stock['type'];				      // 입,출고 구분
		$etc = $stock['etc'];					      // 사유
		$goods_no = $stock['goods_no'];		  // 상품번호
		$goods_sub = $stock['goods_sub'];	  // 상품번호 하위
		$goods_opt = $stock['goods_opt'];	  // 옵션
		$qty = $stock['qty'];					      // 보유재고
		$ord_no = $stock['ord_no'];			    // 주문번호
		$ord_opt_no = $stock['ord_opt_no'];	// 주문일련번호

		// Property Set
		$this->SetGoodsOpt($goods_no, $goods_sub, $goods_opt);

		// Variables
		$is_order = ($ord_opt_no != "") ? true : false;	// 주문 설정
		$ord_date = "";
		$ord_price = "";

		// 상품 정보 얻기
		$sql = "
		select com_id, com_type, goods_type, is_unlimited
		from goods
		where goods_no = '$goods_no' and goods_sub = '$goods_sub'
	  ";
		$row = DB::selectOne($sql);
		$com_id = $row->com_id;
		$com_type = $row->com_type;
		$goods_type = $row->goods_type;
		$is_unlimited = $row->is_unlimited;

		$ret = array();
		$cnt = 0;

		$cal_qty = $qty;
		if ($goods_type == "S" || $goods_type == "I") { // 공급업체

			$stocks = $this->MinusStockQty($goods_no, $goods_sub, $goods_opt, $qty, $type, "", $etc, $com_id, $ord_no, $ord_opt_no);
			if ($stocks != 0) $ret = $stocks;
		} else { //공급업체 제외한 나머지 업체 재고 차감

			// ord_date 얻기
			if ($is_order) {
				$sql = "
			select date_format(ord_date,'%Y%m%d') as ord_date, price
			from order_opt
			where ord_opt_no = '$ord_opt_no'
		  ";

				$row = DB::selectOne($sql);
				$ord_date = $row->ord_date;
				$ord_price = $row->price;
			}
			
			// 주문 정보 있는 경우 해당 가격, 일자의 원가 얻기
			$ord_stock_wonga = ($is_order) ? $this->__GetPartnerGoodsWonga($com_id, $ord_price, $ord_date) : 0;

			// 매출 정보
			$ret[0] = array(
				"qty" => $qty,
				"wonga" => $ord_stock_wonga,
				"com_type" => $com_type
			);
		}

		if ($is_order == false && $is_unlimited == "N") {
			$this->MinusQty($goods_no, $goods_sub, $goods_opt, $qty);
		}

		return $ret;
	}

	public function SetGoodsOpt($goods_no, $goods_sub, $goods_opt, $opt_price = "", $opt_name = "", $opt_seq = "")
	{
		$this->goods_no		= $goods_no;
		$this->goods_sub	= $goods_sub;
		$this->goods_opt	= $goods_opt;

		if ($opt_price != "") $this->opt_price = $opt_price;
		if ($opt_name != "") $this->opt_name = $opt_name;
		if ($opt_seq != "") $this->opt_seq = $opt_seq;
	}

	public function MinusStockQty(
		$goods_no,
		$goods_sub,
		$goods_opt,
		$qty,
		$type,
		$invoice_no = "",
		$etc = "",
		$com_id = "",
		$ord_no = "",
		$ord_opt_no = ""
	) {

		$where = "";
		if ($invoice_no != "") $where .= " and invoice_no = '$invoice_no' ";

		$stocks = array();

		if ($qty <= 0) return 0;

		try {
			DB::beginTransaction();
			$this->SetGoodsOpt($goods_no, $goods_sub, $goods_opt);

			if ($type == 9) {
				$orderby = " order by no desc ";
			} else {
				$orderby = " order by no asc ";
			}

			$plus_qty = $qty;

			$sql = "  -- MinusStockQty.1 --
			select no,wonga,invoice_no,qty
			from goods_good
			where goods_no = '$goods_no'
			  and goods_sub = '$goods_sub'
			  and goods_opt = '$goods_opt'
			  and qty > 0 $where
			$orderby
		  ";
			$rows = DB::select($sql);

			if (!$rows) {
				// 차감할 보유재고가 없는 경우 온라인재고를 초기화하기 위해
				if ($plus_qty > 0 && $type == 9) {
					$this->__DecreaseSummaryQty($plus_qty);
					$history = array(
						"type" => $type,
						"qty" => -$plus_qty,
						"wonga" => 0,
						"invoice_no" => 'TAKE_STOCK',
						"etc" => $etc,
						"com_id" => '',
						"ord_no" => '',
						"ord_opt_no" => ''
					);
					$this->__InsertHistory($history);
				}
				$plus_qty = 0;
			}

			foreach ($rows as $row) {
				$stock_no			= $row->no;
				$stock_qty			= $row->qty;
				$stock_wonga		= $row->wonga;
				$stock_invoice_no	= $row->invoice_no;

				$this->__SetStockNo($stock_no);

				if ($plus_qty > $stock_qty) {
					$plus_qty = $plus_qty - $stock_qty;
					$stock_minus_qty = $stock_qty;
				} else {
					$stock_minus_qty = $plus_qty;
					$plus_qty = 0;
				}

				$this->__DecreaseGoodQty($stock_minus_qty);

				if ($this->loc != '') {
					if ($this->loc == 'LOC') {

						$sql = "
				  select s.wqty - ifnull((
						  select sum(qty) as qty from goods_location
						  where goods_no = s.goods_no and goods_sub = s.goods_sub and goods_opt = s.goods_opt
						),0) as qty
				  from goods_summary s
				  where  s.goods_no = '$goods_no' 
					and s.goods_sub = '$goods_sub' 
					and s.goods_opt = '$goods_opt'
				";

						$summary = DB::selectOne($sql);
						$loc_qty = $summary->qty;

						if ($stock_minus_qty > $loc_qty) {
							throw new Exception("출고가능한 재고가 없습니다.");
						}
					} else {
						$loc = $this->loc;
						$sql = "
				  select qty
					from goods_location
					where goods_no = '$goods_no' 
					  and goods_sub = '$goods_sub' 
					  and goods_opt = '$goods_opt' 
					  and loc = '$loc' 
					  and qty >= $stock_minus_qty
				";
						$location = DB::selectOne($sql);
						if ($location) {
							$sql = "
					update goods_location 
						set qty = qty - $stock_minus_qty
						  , ut = now()
					where goods_no = '$goods_no' 
					  and goods_sub = '$goods_sub' 
					  and goods_opt = '$goods_opt' 
					  and loc = '$loc'
				  ";
							DB::update($sql);
						} else {
							throw new Exception("출고가능한 재고가 없습니다.");
						}
					}
				} else {
					$sql = "
				select
				  s.wqty - ifnull((
					select sum(qty) as qty 
					  from goods_location
					 where goods_no = s.goods_no 
					   and goods_sub = s.goods_sub 
					   and goods_opt = s.goods_opt
				  ),0) as qty
				from goods_summary s
				where  s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
			  ";

					$summary = DB::selectOne($sql);
					$loc_qty = $summary->qty;

					//출고가능한 재고가 없는경우
					if ($stock_minus_qty > $loc_qty) {
						$remain_qty = $stock_minus_qty - $loc_qty;

						$sql = "
					select code_id from code
					where code_kind_cd = 'G_STOCK_LOC' and code_id <> 'LOC' and use_yn = 'y'
					order by code_seq
				";
						$codes = DB::select($sql);
						foreach ($codes as $code) {
							$loc = $code->code_id;

							$sql = "
					select qty
					from goods_location
					where goods_no = '$goods_no' 
					  and goods_sub = '$goods_sub' 
					  and goods_opt = '$goods_opt' 
					  and loc = '$loc'
				  ";

							$location = DB::selectOne($sql);

							if ($location->qty) {
								$loc_qty = $location->qty;
								$sql = "
					  update goods_location set qty = qty - if(qty >= $remain_qty,$remain_qty,qty) ,ut = now()
					  where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
					";
								DB::update($sql);
								$remain_qty = $remain_qty - $loc_qty;
							}
						}
					}
				}

				$this->__DecreaseSummaryQty($stock_minus_qty);

				$history = array(
					"type" => $type,
					"qty" => -$stock_minus_qty,
					"wonga" => $stock_wonga,
					"invoice_no" => $stock_invoice_no,
					"etc" => $etc,
					"com_id" => $com_id,
					"ord_no" => $ord_no,
					"ord_opt_no" => $ord_opt_no
				);
				$this->__InsertHistory($history);

				array_push(
					$stocks,
					array(
						"invoice_no" => $stock_invoice_no,
						"wonga" => $stock_wonga,
						"qty" => $stock_minus_qty,
					)
				);
			}

			if ($plus_qty == 0) {
				DB::commit();
				return $stocks;
			}

			if ($plus_qty > 0 && $type == 9) {
				$this->__DecreaseSummaryQty($plus_qty);
				$history = array(
					"type" => $type,
					"qty" => -$plus_qty,
					"wonga" => 0,
					"invoice_no" => 'TAKE_STOCK',
					"etc" => $etc,
					"com_id" => '',
					"ord_no" => '',
					"ord_opt_no" => ''
				);
				$this->__InsertHistory($history);
			}
			DB::commit();
			return 0;
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}

	public function MinusQty($goods_no, $goods_sub, $goods_opt, $qty)
	{
		if ($qty <= 0) return 0;

		return DB::table('goods_summary')
			->where('goods_no', $goods_no)
			->where('goods_sub', $goods_sub)
			->where('goods_opt', $goods_opt)
			->where('good_qty', '>=', $qty)
			->update([
				'good_qty' => DB::raw("good_qty - $qty"),
				'ut' => now()
			]);
	}

	/*
	  Function: Plus
		재고 처리( 증가 )

	  Parameters:
		$type - 작업 구분
		$etc - 사유
		$qty - 차감 수량
		$goods_no - 상품 번호
		$goods_sub - 상품 번호 서브
		$goods_opt - 상품 옵션 코드
		$wonga - 상품 원가
		$invoice_no - 송장 번호
		$ord_no = "" - 주문 번호
		$ord_opt_no = "" - 주문 일련 번호

	  Returns:
		boolean

	  Variables:
		- $type : 1 발주, 2 일반주문, 3 현장판매, 4 예약, 5 교환, 6 환불, 9 재고조정

	  Comment:
		클레임 처리 관련 차감

	  See Also:
		- <__DecreaseGoodQty>
		- <__DecreaseSummaryQty>
		- <__InsertHistory>
	*/
	public function Plus($stock)
	{
		// 입고 정보
		$type = $stock["type"];					      // 입고 구분
		$qty = $stock["qty"];						      // 입고 수량
		$goods_no = $stock["goods_no"];			  // 상품번호
		$goods_sub = $stock["goods_sub"];			// 상품번호하위
		$goods_opt = $stock["goods_opt"];			// 옵션
		$wonga = $stock["wonga"];					    // 원가
		$invoice_no = $stock["invoice_no"];		// 송장번호
		$ord_no = $stock["ord_no"];				    // 주문번호
		$ord_opt_no = $stock["ord_opt_no"];		// 주문일련번호
		$ord_state = $stock["ord_state"];			// 주문상태
		$opt_name = $stock["opt_name"];			  // 옵션명
		$opt_price = $stock["opt_price"];			// 옵션가격
		$etc = $stock["etc"];						      // 입고 사유
		$wonga_apply_yn = $stock["wonga_apply_yn"];	// 최근 원가 반영여부
		$opt_seq = $stock["opt_seq"];				  // 옵션 순서

		// Set Properties
		$this->SetGoodsOpt($goods_no, $goods_sub, $goods_opt, $opt_price, $opt_name, $opt_seq);

		$sql =
			/** @lang text */
			"
		select com_id, is_unlimited, goods_type
		from goods
		where goods_no = :goods_no
		  and goods_sub = :goods_sub
	  ";
		$row = DB::selectOne($sql, ["goods_no" => $goods_no, "goods_sub" => $goods_sub]);
		$com_id = $row->com_id;
		$is_unlimited = $row->is_unlimited;
		$goods_type = $row->goods_type;

		if ($goods_type == "S" || $goods_type == "I") {
			if ($ord_opt_no == "" || ($ord_opt_no != "" && $ord_state >= 30)) {
				$this->PlusStockQty(
					$goods_no,
					$goods_sub,
					$goods_opt,
					$qty,
					$type,
					$invoice_no,
					$etc,
					$wonga,
					$com_id,
					$ord_no,
					$ord_opt_no
				);
			}
		}

		if ($is_unlimited == "N") {
			$this->PlusQty($goods_no, $goods_sub, $goods_opt, $qty);
		}

		//
		// 상품의 원가 업데이트
		//
		if ($wonga_apply_yn == "Y") {
			DB::table("goods")
				->where("goods_no", $goods_no)
				->where("goods_sub", 0)
				->update([
					'wonga' => $wonga
				]);
		}

		return true;
	}

	public function PlusStockQty(
		$goods_no,
		$goods_sub,
		$goods_opt,
		$qty,
		$type,
		$invoice_no = "INV_ADJUST",
		$etc = "",
		$wonga = "",
		$com_id = "",
		$ord_no = "",
		$ord_opt_no = ""
	) {
		$where = "";
		if ($wonga != "") $where .= " and wonga = '$wonga' ";

		//if($qty > 0){

		$this->SetGoodsOpt($goods_no, $goods_sub, $goods_opt);

		$sql = " -- PlusStockQty.1 --
          select no
            from goods_good
           where goods_no = '$goods_no'
             and goods_sub = '$goods_sub'
             and goods_opt = '$goods_opt'
             and invoice_no = '$invoice_no' $where
           order by no desc
           limit 0,1
      ";

		$row = DB::select($sql);

		if ($row) {
			$stock_no = $row["no"];
			$this->__SetStockNo($stock_no);
			$this->__IncreaseGoodQty($qty);
		} else {
			if ($wonga == "") {
				$sql = "  -- PlusStockQty.2 --
            select wonga from goods
            where goods_no = '$goods_no' 
              and goods_sub = '$goods_sub'
          ";

				if ($rows = DB::selectOne($sql)) {
					$wonga = $rows->wonga;
				} else {
					return 0;
				}
			}
			$this->__InsertGoodQty($wonga, $qty, $invoice_no);
		}
		$this->__IncreaseSummaryQty($qty);

		if ($this->loc != '' && $this->loc != 'LOC') {
			$loc = $this->loc;
			$sql = "
          select qty
            from goods_location
           where goods_no = '$goods_no' 
             and goods_sub = '$goods_sub' 
             and goods_opt = '$goods_opt' 
             and loc = '$loc'
        ";
			if ($rows = DB::selectOne($sql)) {
				$sql = "
              update goods_location 
                set qty = qty + $qty
                  , ut = now()
              where goods_no = '$goods_no' 
                and goods_sub = '$goods_sub' 
                and goods_opt = '$goods_opt' 
                and loc = '$loc'
            ";

				DB::update($sql);
			} else {
				$sql = "
                insert into goods_location ( goods_no,goods_sub,goods_opt,loc, qty, rt, ut )
                values ( '$goods_no','$goods_sub','$goods_opt','$loc', $qty,now(),now() )
            ";

				DB::insert($sql);
			}
		}

		$history = array(
			"type" 	=> $type,
			"etc" 	=> $etc,
			"qty" 	=> $qty,
			"wonga" => $wonga,
			"invoice_no" => $invoice_no,
			"com_id" => $com_id,
			"ord_no" => $ord_no,
			"ord_opt_no" => $ord_opt_no
		);

		return $this->__InsertHistory($history);
	}

	public function PlusQty($goods_no, $goods_sub, $goods_opt, $qty)
	{
		$sql = "
          update goods_summary 
             set good_qty = good_qty + $qty 
               , ut = now()
           where goods_no = '$goods_no' 
             and goods_sub = '$goods_sub' 
             and goods_opt = '$goods_opt'
        ";

		return DB::update($sql);
	}

	public function Sale($sale_type, $sale_rate)
	{

		if ($sale_rate > 0) {
			// Sale
			$sql = "
          SELECT a.goods_no, a.goods_sub, a.goods_type, a.style_no,a.sale_stat_cl,a.head_desc,
              a.sale_yn, a.sale_dt_yn,a.price,a.wonga,b.com_type,IFNULL(a.sale_wonga,0) AS sale_wonga
          FROM goods a
            LEFT OUTER JOIN company b ON a.com_id = b.com_id
          WHERE
            goods_no = :goods_no and sale_dt_yn = 'N'
        ";
			$inputarr = array("goods_no"	=> (string)$this->goods_no);

			$row = DB::selectOne($sql, $inputarr);

			if ($row && $row->price > 0) {
				$price = $row->price;
				$sale_price = $price * (1 - $sale_rate / 100);
				$sql = "
            update goods set sale_yn = 'Y', normal_price = price, sale_type = :sale_type, price = :price, sale_price = :sale_price
            where
              goods_no = :goods_no
          ";
				$inputarr = array(
					"sale_type"	=> (string)$sale_type,
					"price"	=> (string)$sale_price,
					"sale_price"	=> (string)$sale_price,
					"goods_no"	=> (string)$this->goods_no
				);

				DB::update($sql, $inputarr);

				$margin = round(($sale_price - $row->wonga) / $sale_price * 100);

				// History
				$this->__AddModifyHistory(
					array(
						"style_no"		=> $row->style_no,
						"sale_stat_cl"	=> $row->sale_stat_cl,
						"price"		=> $sale_price,
						"margin"	=> $margin,
						"wonga"		=> $row->wonga,
						"head_desc"	=> $row->head_desc,
						"memo"		=> "세일"
					)
				);
			}
		} else {
			return false;
		}
	}

	public function SaleOff()
	{
		// Sale Off
		$sql = "
            SELECT a.goods_no, a.goods_sub, a.goods_type, a.style_no,a.sale_stat_cl,a.head_desc,
                a.sale_yn, a.sale_dt_yn,a.normal_price,a.price,a.wonga,b.com_type,IFNULL(a.sale_wonga,0) AS sale_wonga
            FROM goods a
            LEFT OUTER JOIN company b ON a.com_id = b.com_id
            WHERE
            goods_no = :goods_no and sale_dt_yn = 'N'
        ";
		$inputarr = array(
			"goods_no"	=> (string)$this->goods_no
		);
		$row = DB::selectOne($sql, $inputarr);
		if ($row) {
			$sql = "
            update goods set sale_yn = 'N', sale_type = '',price = normal_price, sale_price = 0
            where
                goods_no = :goods_no
            ";
			$inputarr = array(
				"goods_no"	=> (string)$this->goods_no
			);

			DB::update($sql, $inputarr);

			$price = $row->normal_price;
			$margin = 0;
			if ($price > 0) {
				$margin = round(($price - $row->wonga) / $price * 100);
			}

			// History
			$this->__AddModifyHistory(
				array(
					"style_no"		=> $row->style_no,
					"sale_stat_cl"	=> $row->sale_stat_cl,
					"price"		=> $price,
					"margin"	=> $margin,
					"wonga"		=> $row->wonga,
					"head_desc"	=> $row->head_desc,
					"memo"		=> "세일"
				)
			);
		}
	}

	/*
      Function: __AddModifyHistory
      상품 변경 로그 등록

      Parameters:
        $param - array
    */
	public function __AddModifyHistory($param)
	{

		$id = $this->user["id"];
		$style_no = $param["style_no"];
		$sale_stat_cl = $param["sale_stat_cl"];
		$price = $param["price"];
		$margin = $param["margin"];
		$wonga = $param["wonga"];
		$head_desc = $param["head_desc"];
		$memo = $param["memo"];

		$sql = "
            insert into goods_modify_hist (
            goods_no, goods_sub, style_no, upd_date, sale_stat_cl, price, margin, wonga
            , head_desc, memo, id, regi_date
            ) values (
            '$this->goods_no', '$this->goods_sub', '${style_no}', now(), '${sale_stat_cl}', '${price}', '${margin}', '${wonga}'
            , '${head_desc}', '${memo}', '${id}', now()
            )
        ";

		DB::insert($sql);
	}

	// 휴지통 상품 삭제
	// 이미지 삭제 제외 ceduce 22-02-10
	public function CleanUpTrash()
	{

		// 주문 이력 확인
		$sql  = "
            select count(*) as cnt
            from order_opt
            where 
            goods_no = '$this->goods_no' and goods_sub = '0'
        ";
		$row  = DB::selectOne($sql);

		$order_cnt  = $row->cnt;

		if ($order_cnt > 0) {
			return false;
		}

		// 관련 테이블 데이터 삭제
		$sql  = " delete from category_goods where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from coupon_goods where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_coupon where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from section_goods where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_customize where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		// 상품평
		$sql  = " select * from goods_estimate where goods_no = '$this->goods_no' and goods_sub = '0' ";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$no = $row->no;

			// 댓글 삭제
			$sql_cmt = " delete from goods_estimate_comment where est_no = '$no' ";
			DB::delete($sql_cmt);

			// 이미지 데이터 삭제
			$sql_cmt = " delete from goods_estimate_image where est_no = '$no' ";
			DB::delete($sql_cmt);
		}

		$sql  = " delete from goods_estimate where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		// 상품 문의
		$sql  = " select * from goods_qa_new where goods_no = '$this->goods_no' and goods_sub = '0' ";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$no = $row->no;

			// 댓글 삭제
			$sql_cmt = " delete from goods_qa_comment where qa_no = '$no' ";
			DB::delete($sql_cmt);
		}

		$sql = " delete from goods_qa_new where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_sale_recent where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_good where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_history where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_modify_hist where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_summary where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_option where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_price where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_rank_admin_point where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		// 관련상품 1
		$sql  = " delete from goods_related where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);
		// 관련상품 2
		$sql  = " delete from goods_related where r_goods_no = '$this->goods_no' and r_goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_search_fulltext where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_seq where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_stock where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_tag where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods_wonga where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		$sql  = " delete from goods where goods_no = '$this->goods_no' and goods_sub = '0' ";
		DB::delete($sql);

		return true;
	}
}
