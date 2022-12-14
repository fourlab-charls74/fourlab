<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

// 2022-07-20 오프라인 stock 모델 추가
class S_Stock 
{
    public $user;
    private $stock_no;        // 재고번호
    private $prd_cd;          // 상품코드
    private $goods_no;        // 상품번호
    private $goods_opt;        // 옵션
    private $loc = '';        // 위치

    function __construct($user = [])
    {
        $this->user = $user;
    }

    public function SetPrdCd($prd_cd) 
    {
        $this->prd_cd = $prd_cd;
    }

    public function SetLoc($loc)
    {
        $this->loc = $loc;
    }

    private function __SetStockNo($stock_no)
    {
        $this->stock_no = $stock_no;
    }

    public function Minus($stock)
    {
        // Parameters
        $type = $stock['type'];                      // 입,출고 구분
        $etc = $stock['etc'];                        // 사유
        $goods_no = $stock['goods_no'];              // 상품번호
        $prd_cd = $stock['prd_cd'];                  // 상품 코드
        $goods_opt = $stock['goods_opt'];            // 옵션
        $qty = $stock['qty'];                        // 보유재고
        $ord_no		= @$stock["ord_no"];		     // 주문번호
		$ord_opt_no	= @$stock["ord_opt_no"];		 // 주문일련번호

        // Property Set
        $this->SetPrdCd($prd_cd);
        $this->SetGoodsOpt($goods_no, $goods_opt);

        // Variables
        $is_order = ($ord_opt_no != "") ? true : false;    // 주문 설정
        $ord_date = "";
        $ord_price = "";

        // 상품 정보 얻기
        $sql = "
			select com_id, com_type, goods_type, is_unlimited
			from goods
			where goods_no = '$goods_no'
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

            $stocks = $this->MinusStockQty($goods_no, $prd_cd, $goods_opt, $qty, $type, "", $etc, $com_id, $ord_no, $ord_opt_no);
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
            $this->MinusQty($goods_no, $prd_cd, $goods_opt, $qty);
        }

        return $ret;
    }

    public function SetGoodsOpt($goods_no, $goods_opt, $opt_price = "", $opt_name = "", $opt_seq = "")
    {
        $this->goods_no = $goods_no;
        $this->goods_opt = $goods_opt;

        if ($opt_price != "") $this->opt_price = $opt_price;
        if ($opt_name != "") $this->opt_name = $opt_name;
        if ($opt_seq != "") $this->opt_seq = $opt_seq;
    }

    public function MinusStockQty($goods_no, $prd_cd, $goods_opt, $qty, $type, $invoice_no = "", $etc = "",
                                  $com_id = "", $ord_no = "", $ord_opt_no = "")
    {

        $where = "";
        if ($invoice_no != "") $where .= " and invoice_no = '$invoice_no' ";

        $stocks = array();

        if ($qty <= 0) return 0;
        try {

            $this->SetPrdCd($prd_cd);
            $this->SetGoodsOpt($goods_no, $goods_opt);

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
                  and goods_opt = '$goods_opt'
                  and qty > 0 $where
                $orderby
              ";
            $rows = DB::select($sql);

            if (count($rows) > 0) {
                // 차감할 보유재고가 없는 경우 온라인재고를 초기화하기 위해
                if ($plus_qty > 0 && $type == 9) {
                    $this->__DecreasePrdStockQty($plus_qty);
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
                                    where goods_no = s.goods_no and goods_opt = s.goods_opt
                                ),0) as qty
                            from product_stock s
                                where  s.goods_no = '$goods_no' 
                                and s.goods_opt = '$goods_opt'
                        ";

                        $prd_stock = DB::selectOne($sql);
                        $loc_qty = $prd_stock->qty;
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
                            where goods_no = s.goods_no and goods_opt = s.goods_opt
                          ),0) as qty
                        from product_stock s
                        where  s.goods_no = '$goods_no' and s.prd_cd = '$prd_cd' and s.goods_opt = '$goods_opt'
                      ";
                    $prd_stock = DB::selectOne($sql);
                    $loc_qty = $prd_stock->qty;

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
                                where goods_no = '$goods_no' and goods_opt = '$goods_opt' and loc = '$loc'
                              ";
                            $location = DB::selectOne($sql);

                            if ($location) {
                                $loc_qty = $location->qty;
                                $sql = /** @lang text */
                                    "
                                  update goods_location set qty = qty - if(qty >= $remain_qty,$remain_qty,qty) ,ut = now()
                                  where goods_no = '$goods_no' and goods_opt = '$goods_opt' and loc = '$loc'
                                ";
                                DB::update($sql);
                                $remain_qty = $remain_qty - $loc_qty;
                            }
                        }
                    }
                }

                $this->__DecreasePrdStockQty($stock_minus_qty);

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
                    $this->__DecreasePrdStockQty($plus_qty);
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

    private function __DecreasePrdStockQty($qty)
    {
        try {
            return DB::update(" 
                update product_stock set
                    wqty = if( (wqty - $qty) < 0, 0, (wqty - $qty))
                    , ut = now()
                where goods_no = '$this->goods_no'
                    and prd_cd = '$this->prd_cd'
                    and goods_opt = '$this->goods_opt'
            ");
        } catch (Exception $e) {
            return 0;
        }
    }

    private function __InsertHistory($data)
    {
        $user = $this->user;

        $sql = "select * from `storage` where default_yn = 'Y'";
        $result = DB::selectOne($sql);
        $storage_cd = $result == null ? '' : $result->storage_cd;

        return DB::table('product_stock_hst')->insertGetId([
            'goods_no' => $this->goods_no,
            'prd_cd' => $this->prd_cd,
            'goods_opt' => $this->goods_opt,
            'location_cd' => $storage_cd,
            'location_type' => 'STORAGE',
            'type' => 1, // 재고분류 : 입고(창고입고)
            'price' => $data["wonga"],
            'wonga' => $data["wonga"],
            'qty' => $data["qty"],
            'invoice_no' => $data['invoice_no'],
            'stock_state_date' => date('Ymd'),
            'com_id' => $data['com_id'],
            'ord_opt_no' => 0,
            'comment' => '창고입고',
            'rt' => now(),
            'admin_id' => $user['id'] ?? '',
            'admin_nm' => $user['name'] ?? '',
        ]);

        // return DB::table('goods_history')->insertGetId([
        //     'goods_no' => $this->goods_no,
        //     'goods_sub' => 0,
        //     'goods_opt' => $this->goods_opt,
        //     'wonga' => $data["wonga"],
        //     'type' => $data["type"],
        //     'stock_state' => 1,
        //     'qty' => $data["qty"],
        //     'loc' => $this->loc,
        //     'etc' => $data["etc"],
        //     'ord_opt_no' => $data["ord_opt_no"],
        //     'invoice_no' => $data["invoice_no"],
        //     'admin_id' => isset($user["id"])? $user["id"]:"",
        //     'admin_nm' => isset($user["name"])? $user["name"]:"",
        //     'com_id' => $data["com_id"],
        //     'ord_no' => $data["ord_no"],
        //     'stock_state_date' => date("Ymd"), // goods_history의 stock_state_date는 Ymd 형식으로 되어있음. now에서 Ymd로 수정 - 20220217
        //     'regi_date' => now()
        // ]);
    }

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

    private function __GetPartnerGoodsWonga($com_id, $price, $ord_date)
    {
        $sql = "
			select wonga
			from goods_wonga
			where goods_no = '$this->goods_no'
				and goods_sub ='0'
				-- and com_id = '$com_id'
				and price = '$price'
				and sdate <= '$ord_date'
				and edate >= '$ord_date'
			order by regi_date desc limit 0,1
        ";

        return DB::selectOne($sql)->wonga;
    }

    public function MinusQty($goods_no, $prd_cd, $goods_opt, $qty)
    {
        if ($qty <= 0) return 0;

        $sql = "
            update product_stock 
                set qty = qty - $qty 
                , ut = now()
            where goods_no = '$goods_no' 
                and prd_cd = '$prd_cd'
                and goods_opt = '$goods_opt'
                and qty >= '$qty'
            ";

        return DB::update($sql);
    }

    /*
        Function: Plus
            재고 처리( 증가 )

        Parameters:
            $type - 작업 구분
            $etc - 사유
            $qty - 차감 수량
            $goods_no - 상품 번호
            $prd_cd - 상품 코드
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
            - <__DecreasePrdStockQty>
            - <__InsertHistory>
    */
    public function Plus($stock)
    {
		$type		= $stock["type"];				// 입고 구분
		$qty		= $stock["qty"];				// 입고 수량
		$goods_no	= $stock["goods_no"];			// 상품 번호
        $prd_cd	    = $stock["prd_cd"];			    // 상품 코드
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
        $this->SetPrdCd($prd_cd);
        $this->SetGoodsOpt($goods_no, $goods_opt, $opt_price, $opt_name, $opt_seq);

        $sql = "
			select com_id, is_unlimited, goods_type
			from goods
			where goods_no = '$goods_no'
		";
        $rows = DB::selectOne($sql);
        $com_id = $rows->com_id;
        $is_unlimited = $rows->is_unlimited;
        $goods_type = $rows->goods_type;

        if ($goods_type == "S" || $goods_type == "I") {
            if ($ord_opt_no == "" || ($ord_opt_no != "" && $ord_state >= 30)) {
                $this->PlusStockQty($goods_no, $prd_cd, $goods_opt, $qty, $type,
                    $invoice_no, $etc, $wonga, $com_id, $ord_no, $ord_opt_no);

                // 입고완료시 입고처리
                $this->PlusInQty($goods_no, $prd_cd, $goods_opt, $qty);
            }
        }

        if ($is_unlimited == "N") {
            $this->PlusQty($goods_no, $prd_cd, $goods_opt, $qty);
        }

        //
        // 상품의 원가 업데이트
        //
        if ($wonga_apply_yn == "Y") {
            DB::table('goods')
                ->where('goods_no', $goods_no)
                ->update([
                    'wonga' => $wonga
                ]);
        }

        return true;
    }

    public function PlusStockQty($goods_no, $prd_cd, $goods_opt, $qty, $type, $invoice_no = "INV_ADJUST", $etc = "", $wonga = "",
                                 $com_id = "", $ord_no = "", $ord_opt_no = "")
    {
        $where = "";
        if ($wonga != "") $where .= " and wonga = '$wonga' ";

        $this->SetPrdCd($prd_cd);
        $this->SetGoodsOpt($goods_no, $goods_opt);

        $sql = " -- PlusStockQty.1 --
			select no
            from goods_good
            where goods_no = '$goods_no'
                and goods_opt = '$goods_opt'
                and invoice_no = '$invoice_no' $where
            order by no desc
            limit 0,1
        ";

        $row = DB::selectOne($sql);

        if ($row) {
            $stock_no = $row->no;
            $this->__SetStockNo($stock_no);
        } else {
            if ($wonga == "") {
                $sql = "  -- PlusStockQty.2 --
                    select wonga from goods
                    where goods_no = '$goods_no'
                ";

                $rows = DB::selectOne($sql);

                if ($rows) {
                    $wonga = $rows->wonga;
                } else {
                    return 0;
                }
            }
        }

        $this->__IncreasePrdStockQty($qty);

        $this->__IncreasePrdStockStorageQty($qty);

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

    private function __InsertGoodQty($wonga, $qty, $invoice_no)
    {
        try {
            DB::table('goods_good')->insert([
                'goods_no' => $this->goods_no,
                'goods_sub' => 0,
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

    private function __IncreasePrdStockQty($qty)
    {
        // wqty 증가 업데이트는 창고용 컬럼이므로 수정해야됨 20221027
        $affected_rows = DB::update("
            update product_stock set
                wqty = wqty + $qty
                , ut = now()
            where goods_no = '$this->goods_no' 
                and prd_cd = '$this->prd_cd'
                and goods_opt = '$this->goods_opt'
        ");
        if ($affected_rows > 0) return $affected_rows;
        try {
            DB::table('product_stock')->insert([
                'goods_no' => $this->goods_no,
                'prd_cd' => $this->prd_cd,
                'goods_opt' => $this->goods_opt,
                'qty' => '0', // qty는 입고시 등록되어야하므로 수정되어야함 20221027
                'wqty' => $qty,
                'use_yn' => 'Y',
                'rt' => now(),
                'ut' => now(),
            ]);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function __IncreasePrdStockStorageQty($qty)
    {

        $sql = "select * from `storage` where default_yn = 'Y'";
        $result = DB::selectOne($sql);
        $storage_cd = $result->storage_cd;

        $affected_rows = DB::update("
            update product_stock_storage set
                wqty = wqty + $qty,
                qty = qty + $qty,
                ut = now()
            where goods_no = '$this->goods_no'
                and prd_cd = '$this->prd_cd'
                and goods_opt = '$this->goods_opt'
                and storage_cd = '$storage_cd'
        ");
        if ($affected_rows > 0) return $affected_rows;
        try {
            DB::table('product_stock_storage')->insert([
                'goods_no' => $this->goods_no,
                'prd_cd' => $this->prd_cd,
                'goods_opt' => $this->goods_opt,
                'qty' => $qty,
                'wqty' => $qty,
                'use_yn' => 'Y',
                'rt' => now(),
                'ut' => now(),
                'storage_cd' => $storage_cd
            ]);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function PlusQty($goods_no, $prd_cd, $goods_opt, $qty)
    {
        $sql = "
            update product_stock 
                set qty = qty + $qty 
                    , ut = now()
            where goods_no = '$goods_no' 
                and prd_cd = '$prd_cd'
                and goods_opt = '$goods_opt'
        ";
        return DB::update($sql);
    }

    public function PlusInQty($goods_no, $prd_cd, $goods_opt, $qty)
    {
        $sql = "
            update product_stock 
                set in_qty = in_qty + $qty 
                    , ut = now()
            where goods_no = '$goods_no' 
                and prd_cd = '$prd_cd'
                and goods_opt = '$goods_opt'
        ";

        return DB::update($sql);
    }

    public function GetStockQty($goods_no, $prd_cd, $goods_opt)
    {
        $sql = "
			select
				ifnull(s.wqty,0) as wqty,
			from product_stock s
            where s.goods_no = '$goods_no' 
                and s.prd_cd = '$prd_cd'
                and s.goods_opt = '$goods_opt'
        ";

        $rows = DB::selectOne($sql);

        return $rows->wqty;
    }

    public function GetQty($goods_no, $prd_cd, $goods_opt)
    {
        $sql = "
			select
				ifnull(sum(s.qty),0) as goods_qty,
				max(g.is_unlimited) as is_unlimited
			from product_stock s
				inner join goods g on s.goods_no = g.goods_no
            where s.goods_no = '$goods_no' 
                and s.prd_cd = '$prd_cd'
                and s.goods_opt = '$goods_opt'
        ";

        $rows = DB::selectOne($sql);
        $goods_qty = $rows->goods_qty;
        $is_unlimited = $rows->is_unlimited;

        if ($is_unlimited == "Y" && $goods_qty > 0) {    // 무한재고상품
            $goods_qty = 99999999;
        }

        return $goods_qty;
    }

    public function SetQty($goods_no, $prd_cd, $goods_opt, $qty)
    {
        if ($qty < 0) $qty = 0;

        return DB::table('product_stock')
            ->where('goods_no', $goods_no)
            ->where('prd_cd', $prd_cd)
            ->where('goods_opt', $goods_opt)
            ->update(['qty' => $qty, 'ut' => now()]);
    }

    public function SetPrdStockQty($goods_no, $prd_cd, $goods_opt, $qty, $etc = "")
    {
        $sql = " -- SetPrdStockQty --
			select g.com_id, g.wonga, s.wqty
			from goods g inner join product_stock s on g.goods_no = s.goods_no
			where s.goods_no = '$goods_no' 
                and s.prd_cd = '$prd_cd'
                and s.goods_opt = '$goods_opt'
        ";

        $rows = DB::selectOne($sql);

        if (empty($rows)) return 0;

        $wqty = $rows->wqty;
        $affected_rows = 0;

        if ($qty > $wqty) {
            $plus_qty = $qty - $wqty;
            $affected_rows = $this->PlusStockQty($goods_no, $prd_cd, $goods_opt, $plus_qty, 9, "INV_ADJUST", $etc);
        } else if ($qty < $wqty) {
            $minus_qty = $wqty - $qty;
            $affected_rows = $this->MinusStockQty($goods_no, $prd_cd, $goods_opt, $minus_qty, 9, "", $etc);
        }

        return $affected_rows;
    }

    /*
        Function: IsOption
            옵션존재 여부 확인

        Parameters:
            $goods_no - 상품 번호
            $prd_cd - 상품 코드
            $goods_opt - 상품 옵션 코드

        Returns:
            bool
    */

    public function IsOption($goods_no, $prd_cd, $goods_opt)
    {
        $sql = /** @lang text */
        "
            select count(*) as cnt
            from product_stock
            where goods_no = :goods_no and prd_cd = :prd_cd and goods_opt = :goods_opt
        ";
        $rows = DB::selectOne($sql, array("goods_no" => $goods_no, "prd_cd" => $prd_cd, "goods_opt" => $goods_opt));
        return ($rows->cnt > 0) ? true : false;
    }

    public function CreateOption($goods_no, $prd_cd, $goods_opt, $qty = 0, $wqty = 0)
    {
        DB::table('product_stock')->insert([
            'goods_no' => $goods_no,
            'prd_cd' => $prd_cd,
            'goods_opt' => $goods_opt,
            'qty' => $qty,
            'wqty' => $wqty,
            'use_yn' => 'Y',
            'rt' => now(),
            'ut' => now(),
        ]);
    }

    /*
        Function: DeleteOption
            옵션 삭제 : 재고조정 후 해당 옵션 삭제.

        Parameters:
            $goods_no - 상품 번호
            $prd_cd - 상품 코드
            $goods_opt - 상품 옵션 코드
    */
    public function DeleteOption($goods_no, $prd_cd, $goods_opt)
    {
        DB::table('product_stock')
            ->where('goods_no', $goods_no)
            ->where('prd_cd', $prd_cd)
            ->where('goods_opt', $goods_opt)
            ->delete();
    }

    /*
        Function: IsTotalQty
            총 재고 수량 확인

        Parameters:
            $goods_no - 상품 번호
            $prd_cd - 상품 코드

        Returns:
            1 - 재고 있음
            0 - 재고 없음
    */
    public function IsTotalQty($goods_no, $prd_cd)
    {
        $sql = "
			select ifnull(sum(good_qty), 0) as tot_cnt
			from product_stock
			where goods_no = '$goods_no' and prd_cd = '$prd_cd'
        ";

        $row = DB::selectOne($sql);

        if ($row->tot_cnt > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}