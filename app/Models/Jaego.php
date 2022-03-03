<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class Jaego
{
    public $user;
    private $stock_no;        // 재고번호
    private $goods_no;        // 상품번호
    private $goods_sub;        // 상품번호 하위
    private $goods_opt;        // 옵션
    private $loc = '';        // 위치
    private $opt_price;        // 옵션가격
    private $opt_name;        // 옵션명
    private $opt_seq;        // 옵션 순서


    function __construct($user = [])
    {
        $this->user = $user;
    }

    /*
        Function: __SetStockNo
        재고 일련 번호 설정
    */

    public function SetLoc($loc)
    {
        $this->loc = $loc;
    }

    public function Minus($stock)
    {
        // Parameters
        $type = $stock->type;                      // 입,출고 구분
        $etc = $stock->etc;                          // 사유
        $goods_no = $stock->goods_no;          // 상품번호
        $goods_sub = $stock->goods_sub;      // 상품번호 하위
        $goods_opt = $stock->goods_opt;      // 옵션
        $qty = $stock->qty;                          // 보유재고
        $ord_no = $stock->ord_no;                // 주문번호
        $ord_opt_no = $stock->ord_opt_no;    // 주문일련번호

        // Property Set
        $this->SetGoodsOpt($goods_no, $goods_sub, $goods_opt);

        // Variables
        $is_order = ($ord_opt_no != "") ? true : false;    // 주문 설정
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

        } else { // 공급업체 제외한 나머지 업체 재고 차감

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
        $this->goods_no = $goods_no;
        $this->goods_sub = $goods_sub;
        $this->goods_opt = $goods_opt;

        if ($opt_price != "") $this->opt_price = $opt_price;
        if ($opt_name != "") $this->opt_name = $opt_name;
        if ($opt_seq != "") $this->opt_seq = $opt_seq;
    }

    /*
        Function: __InsertHistory
        재고 변동 내역 등록

        Parameters:
            $data - array

        Returns:
            $data_no - 재고 변동 내역 일련 번호
    */

    public function MinusStockQty($goods_no, $goods_sub, $goods_opt, $qty, $type, $invoice_no = "", $etc = "",
                                  $com_id = "", $ord_no = "", $ord_opt_no = "")
    {

        $where = "";
        if ($invoice_no != "") $where .= " and invoice_no = '$invoice_no' ";

        $stocks = array();

        if ($qty <= 0) return 0;
        try {
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

            if (count($rows) > 0) {
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
                $stock_no = $row->no;
                $stock_qty = $row->qty;
                $stock_wonga = $row->wonga;
                $stock_invoice_no = $row->invoice_no;

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

                        $sql = /** @lang text */
                            "
                          select s.wqty - ifnull((
                                  select sum(qty) as qty from goods_location
                                  where goods_no = s.goods_no and goods_sub = s.goods_sub and goods_opt = s.goods_opt
                                ),0) as qty
                          from goods_summary s
                          where  s.goods_no = '$goods_no' 
                            and s.goods_sub = '$goods_sub' 
                            and s.goods_opt = '$goods_opt'
                        ";

                        $summary = DB::select($sql);
                        $loc_qty = $summary->qty;
                        if ($stock_minus_qty > $loc_qty) {
                            return -1;
                        }
                    } else {
                        $loc = $this->loc;
                        $sql = /** @lang text */
                            "
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
                            $sql = /** @lang text */
                                "
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
                            return -1;
                        }
                    }
                } else {

                    $sql = /** @lang text */
                        "
                        select
                          s.wqty - ifnull((
                            select sum(qty) as qty from goods_location
                            where goods_no = s.goods_no and goods_sub = s.goods_sub and goods_opt = s.goods_opt
                          ),0) as qty
                        from goods_summary s
                        where  s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
                      ";
                    $summary = DB::selectOne($sql);
                    $loc_qty = $summary->qty;

                    if ($stock_minus_qty > $loc_qty) {
                        $remain_qty = $stock_minus_qty - $loc_qty;

                        $sql = /** @lang text */
                            "
                            select code_id from code
                            where code_kind_cd = 'G_STOCK_LOC' and code_id <> 'LOC' and use_yn = 'y'
                            order by code_seq
                        ";
                        $codes = &DB::select($sql);
                        foreach ($codes as $code) {
                            $loc = $code->code_id;

                            $sql = /** @lang text */
                                "
                                select qty
                                from goods_location
                                where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
                              ";
                            $location = DB::selectOne($sql);

                            if ($location) {
                                $loc_qty = $location->qty;
                                $sql = /** @lang text */
                                    "
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

                array_push($stocks,
                    array(
                        "invoice_no" => $stock_invoice_no,
                        "wonga" => $stock_wonga,
                        "qty" => $stock_minus_qty,
                    )
                );
            }

            if ($plus_qty == 0) {
                return $stocks;
            } else {
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
                return 0;
            }
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            //throw new Exception($e->getTraceAsString());
        }
    }

    /*
        Function: __InsertGoodQty
        재고 정보 등록

        Parameters:
            $wonga - 상품 원가
            $qty - 등록 수량
            $invoice_no - 인보이스 번호
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
        Function: __IncreaseGoodQty
        선택된 재고 수량 증가

        Returns:
            $affected_row - 수정된 열 수
    */

    private function __InsertHistory($data)
    {
        $user = $this->user;
        return DB::table('goods_history')->insertGetId([
            'goods_no' => $this->goods_no,
            'goods_sub' => $this->goods_sub,
            'goods_opt' => $this->goods_opt,
            'wonga' => $data["wonga"],
            'type' => $data["type"],
            'stock_state' => 1,
            'qty' => $data["qty"],
            'loc' => $this->loc,
            'etc' => $data["etc"],
            'ord_opt_no' => $data["ord_opt_no"],
            'invoice_no' => $data["invoice_no"],
            'admin_id' => isset($user["id"])? $user["id"]:"",
            'admin_nm' => isset($user["name"])? $user["name"]:"",
            'com_id' => $data["com_id"],
            'ord_no' => $data["ord_no"],
            'stock_state_date' => date("Ymd"), // goods_history의 stock_state_date는 Ymd 형식으로 되어있음. now에서 Ymd로 수정 - 20220217
            'regi_date' => now()
        ]);
    }

    /*
        Function: __DecreaseGoodQty
        선택된 재고 수량 차감

        Returns:
            $affected_row - 수정된 열 수
    */

    private function __SetStockNo($stock_no)
    {
        $this->stock_no = $stock_no;
    }

    /*
        Function: __DecreaseSummaryQty
        재고 집계 정보 수량 차감

        Parameters:
            $qty - 차감 수량

        Returns:
            $affected_row - 수정된 행 수
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
        Function: __IncreaseSummaryQty
        재고 집계 정보 수량 증가

        Parameters:
            $qty - 증가 수량

        Returns:
            $affected_row - 수정된 행 수
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

        return DB::selectOne($sql)->wonga;
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

    public function MinusQty($goods_no, $goods_sub, $goods_opt, $qty)
    {
        if ($qty <= 0) return 0;

        $sql = "
      update goods_summary 
         set good_qty = good_qty - $qty 
           , ut = now()
      where goods_no = '$goods_no' 
        and goods_sub = '$goods_sub' 
        and goods_opt = '$goods_opt'
        and good_qty >= '$qty'
    ";

        return DB::update($sql);
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

    public function Plus($stock)
    {
        // 입고 정보
        /*
        $type = $stock->type;                                // 입고 구분
        $qty = $stock->qty;                                    // 입고 수량
        $goods_no = $stock->goods_no;                    // 상품번호
        $goods_sub = $stock->goods_sub;                  // 상품번호하위
        $goods_opt = $stock->goods_opt;                  // 옵션
        $wonga = $stock->wonga;                              // 원가
        $invoice_no = $stock->invoice_no;              // 송장번호
        $ord_no = $stock->ord_no;                          // 주문번호
        $ord_opt_no = $stock->ord_opt_no;              // 주문일련번호
        $ord_state = $stock->ord_state;                  // 주문상태
        $opt_name = $stock->opt_name;                    // 옵션명
        $opt_price = $stock->opt_price;                  // 옵션가격
        $etc = $stock->etc;                                    // 입고 사유
        $wonga_apply_yn = $stock->wonga_apply_yn;    // 최근 원가 반영여부
        $opt_seq = $stock->opt_seq;                        // 옵션 순서
        */

		$type		= $stock["type"];				// 입고 구분
		$qty		= $stock["qty"];				// 입고 수량
		$goods_no	= $stock["goods_no"];			// 상품번호
		$goods_sub	= $stock["goods_sub"];			// 상품번호하위
		$goods_opt	= $stock["goods_opt"];			// 옵션
		$wonga		= $stock["wonga"];				// 원가
		$invoice_no	= $stock["invoice_no"];			// 송장번호

        // 입고완료시 코드에서 에러발생 2022-02-17 @처리
		$ord_no		= @$stock["ord_no"];				// 주문번호
		$ord_opt_no	= @$stock["ord_opt_no"];			// 주문일련번호


		$ord_state	= @$stock["ord_state"];			// 주문상태
		$opt_name	= @$stock["opt_name"];			// 옵션명
		$opt_price	= @$stock["opt_price"];			// 옵션가격
		$etc		= $stock["etc"];				// 입고 사유
		$wonga_apply_yn	= @$stock["wonga_apply_yn"];// 최근 원가 반영여부
		$opt_seq	= @$stock["opt_seq"];			// 옵션 순서

        $wonga_apply_yn = empty($wonga_apply_yn) ? 'N' : $wonga_apply_yn;

        // Set Properties
        $this->SetGoodsOpt($goods_no, $goods_sub, $goods_opt, $opt_price, $opt_name, $opt_seq);

        $sql = "
			select com_id, is_unlimited, goods_type
			from goods
			where goods_no = '$goods_no'
				and goods_sub = '$goods_sub'
		";
        $rows = DB::selectOne($sql);
        $com_id = $rows->com_id;
        $is_unlimited = $rows->is_unlimited;
        $goods_type = $rows->goods_type;

        if ($goods_type == "S" || $goods_type == "I") {
            if ($ord_opt_no == "" || ($ord_opt_no != "" && $ord_state >= 30)) {
                $this->PlusStockQty($goods_no, $goods_sub, $goods_opt, $qty, $type,
                    $invoice_no, $etc, $wonga, $com_id, $ord_no, $ord_opt_no);
            }
        }

        if ($is_unlimited == "N") {
            $this->PlusQty($goods_no, $goods_sub, $goods_opt, $qty);
        }

        //
        // 상품의 원가 업데이트
        //
        if ($wonga_apply_yn == "Y") {
            DB::table('goods')
                ->where('goods_no', $goods_no)
                ->where('goods_sub', $goods_sub)
                ->update([
                    'wonga' => $wonga
                ]);
        }

        return true;
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

    public function PlusStockQty($goods_no, $goods_sub, $goods_opt, $qty, $type, $invoice_no = "INV_ADJUST", $etc = "", $wonga = "",
                                 $com_id = "", $ord_no = "", $ord_opt_no = "")
    {
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

        $row = DB::selectOne($sql);

        if ($row) {
            $stock_no = $row->no;
            $this->__SetStockNo($stock_no);
            $this->__IncreaseGoodQty($qty);
        } else {
            if ($wonga == "") {
                $sql = "  -- PlusStockQty.2 --
						select wonga from goods
						where goods_no = '$goods_no' and goods_sub = '$goods_sub'
          ";

                $rows = DB::selectOne($sql);

                if ($rows) {
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

            $rows = DB::selectOne("
          select qty
            from goods_location
          where goods_no = '$goods_no' 
            and goods_sub = '$goods_sub' 
            and goods_opt = '$goods_opt' 
            and loc = '$loc'
        ");

            if ($rows) {
                DB::update("
            update goods_location 
              set qty = qty + $qty
                , ut = now()
            where goods_no = '$goods_no' 
              and goods_sub = '$goods_sub' 
              and goods_opt = '$goods_opt' 
              and loc = '$loc'
          ");
            } else {
                DB::table('goods_location')->insert([
                    'goods_no' => $goods_no,
                    'goods_sub' => $goods_sub,
                    'goods_opt' => $goods_opt,
                    'loc' => $loc,
                    'qty' => $qty,
                    'rt' => now(),
                    'ut' => now()
                ]);
            }
        }

        $history = array(
            "type" => $type,
            "etc" => $etc,
            "qty" => $qty,
            "wonga" => $wonga,
            "invoice_no" => $invoice_no,
            "com_id" => $com_id,
            "ord_no" => $ord_no,
            "ord_opt_no" => $ord_opt_no
        );

        return $this->__InsertHistory($history);
    }

    /*
        Function: GetStockQty
            상품 창고 재고 수량 얻기

        Parameters:
            $goods_no - 상품 번호
            $goods_sub = "" - 상품 번호 서브
            $goods_opt = "" - 상품 옵션 코드

        Returns:
            $good_qty - 상품 재고 수량
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
        Function: GetQty
            상품 재고 수량 얻기

        Parameters:
            $goods_no - 상품 번호
            $goods_sub = "" - 상품 번호 서브
            $goods_opt = "" - 상품 옵션 코드

        Returns:
            $good_qty - 상품 재고 수량
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

    public function GetStockQty($goods_no, $goods_sub, $goods_opt)
    {
        $sql = "
			select
				ifnull(s.wqty,0) as wqty,
			from goods_summary
			where s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
    ";

        $rows = DB::selectOne($sql);

        return $rows->wqty;
    }

    public function GetQty($goods_no, $goods_sub, $goods_opt)
    {
        $sql = "
			select
				ifnull(sum(s.good_qty),0) as goods_qty,
				max(g.is_unlimited) as is_unlimited
			from goods_summary s
				inner join goods g on s.goods_no = g.goods_no and s.goods_sub = g.goods_sub
			where s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
    ";

        $rows = DB::selectOne($sql);
        $goods_qty = $rows->goods_qty;
        $is_unlimited = $rows->is_unlimited;

        if ($is_unlimited == "Y" && $goods_qty > 0) {    // 무한재고상품
            $goods_qty = 99999999;
        }

        return $goods_qty;
    }

    public function SetQty($goods_no, $goods_sub, $goods_opt, $qty)
    {
        if ($qty < 0) $qty = 0;

        return DB::table('goods_summary')
            ->where('goods_no', $goods_no)
            ->where('goods_sub', $goods_sub)
            ->where('goods_opt', $goods_opt)
            ->update(['good_qty' => $qty, 'ut' => now()]);
    }

    public function SetStockQty($goods_no, $goods_sub, $goods_opt, $qty, $etc = "")
    {
        $sql = " -- SetStockQty --
			select g.com_id, g.wonga,s.wqty
			from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
			where s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
    ";

        $rows = DB::selectOne($sql);

        if (empty($rows)) return 0;

        $wqty = $rows->wqty;
        $affected_rows = 0;

        if ($qty > $wqty) {
            $plus_qty = $qty - $wqty;
            $affected_rows = $this->PlusStockQty($goods_no, $goods_sub, $goods_opt, $plus_qty, 9, "INV_ADJUST", $etc);
        } else if ($qty < $wqty) {
            $minus_qty = $wqty - $qty;
            $affected_rows = $this->MinusStockQty($goods_no, $goods_sub, $goods_opt, $minus_qty, 9, "", $etc);
        }

        return $affected_rows;
    }

    /*
        Function: IsOption
            옵션존재 여부 확인

        Parameters:
            $goods_no - 상품 번호
            $goods_sub - 상품 번호 서브
            $goods_opt - 상품 옵션 코드

        Returns:
            $good_qty - 상품 재고 수량
    */

    public function IsOption($goods_no, $goods_sub, $goods_opt)
    {
        $sql = /** @lang text */
            "
                select count(*) as cnt
                from goods_summary
                where goods_no = :goods_no and goods_sub = :goods_sub
                        and goods_opt = :goods_opt
        ";
        $rows = DB::selectOne($sql, array("goods_no" => $goods_no, "goods_sub" => $goods_sub, "goods_opt" => $goods_opt));
        return ($rows->cnt > 0) ? true : false;
    }

    public function CreateOption($goods_no, $goods_sub, $goods_opt, $opt_name, $qty = 0, $wqty = 0, $opt_price = 0)
    {
        DB::table('goods_summary')->insert([
            'goods_no' => $goods_no,
            'goods_sub' => $goods_sub,
            'opt_name' => $opt_name,
            'goods_opt' => $goods_opt,
            'opt_price' => $opt_price,
            'good_qty' => $qty,
            'wqty' => $wqty,
            'soldout_yn' => 'N',
            'use_yn' => 'Y',
            'seq' => 0,
            'rt' => now(),
            'ut' => now(),
            'bad_qty' => 0,
            'last_date' => now()
        ]);
    }

    /*
        Function: DeleteOption
            옵션 삭제 : 재고조정 후 해당 옵션 삭제.

        Parameters:
            $goods_no - 상품 번호
            $goods_sub - 상품 번호 서브
            $goods_opt - 상품 옵션 코드

        Returns:
            $good_qty - 상품 재고 수량
    */
    public function DeleteOption($goods_no, $goods_sub, $goods_opt)
    {
        DB::table('goods_summary')
            ->where('goods_no', $goods_no)
            ->where('goods_sub', $goods_sub)
            ->where('goods_opt', $goods_opt)
            ->delete();
    }

    /*
        Function: IsTotalQty
            총 재고 수량 확인

        Parameters:
            $goods_no - 상품 번호
            $goods_sub - 상품 번호 서브

        Returns:
            1 - 재고 있음
            0 - 재고 없음
    */
    public function IsTotalQty($goods_no, $goods_sub)
    {
        $sql = "
			select ifnull(sum(good_qty), 0) as tot_cnt
			from goods_summary
			where goods_no = '$goods_no' and goods_sub = '$goods_sub'
    ";

        $row = DB::selectOne($sql);

        if ($row->tot_cnt > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}
