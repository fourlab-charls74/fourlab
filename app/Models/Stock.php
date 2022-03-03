<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;

class Stock
{
    private $loc = '';
    private $opt_name = '';
    private $opt_price;
    private $opt_seq;
    private $user;

    function __construct($user){
        $this->user = $user;
    }

    function __SetStockNo( $stock_no ){
        $this->stock_no = $stock_no;
    }

    private function SetGoodsOpt( $goods_no,$goods_opt,$opt_price = "",$opt_name = "",$opt_seq = "" ){
        $this->goods_no		= $goods_no;
        $this->goods_opt	= $goods_opt;

        if($opt_price != "" ) $this->opt_price = $opt_price;
        if($opt_name != "" ) $this->opt_name = $opt_name;
        if($opt_seq != "") $this->opt_seq = $opt_seq;
    }

    public function SetQty($goods_no,$goods_opt,$qty){

        if($qty < 0) $qty = 0;
        return DB::table('goods_summary')
            ->where('goods_no','=',$goods_no)
            ->where('goods_opt','=',$goods_opt)
            ->update(
                [
                    'good_qty' => $qty,
                    'ut' => DB::raw('now()')
                ]
            );
    }

    public function isUnlimited($goods_no){
        return DB::table('goods')
            ->where('goods_no','=',$goods_no)->value('is_unlimited');
    }

    public function SetStockQty($goods_no,$goods_opt,$qty,$etc = ""){

        $query = "
			select g.com_id, g.wonga,s.wqty
			from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
			where s.goods_no = :goods_no and s.goods_sub = 0 
			and s.goods_opt = :goods_opt
		";
        //DB::setFetchMode(PDO::FETCH_ASSOC);
        $rows = DB::select($query,['goods_no' => $goods_no,
            'goods_opt' => $goods_opt
            ]
        );
        $result = false;
        if(count($rows) > 0){
            $row = $rows[0];
            $wqty = $row->wqty;
            if($qty > $wqty){
                $plus_qty = $qty - $wqty;
                DB::transaction(function () use (&$result,$goods_no,$goods_opt,$plus_qty,$etc) {
                    $this->PlusStockQty($goods_no, $goods_opt, $plus_qty, 9, "INV_ADJUST", $etc);
                });
            } else if($qty < $wqty){
                $minus_qty = $wqty - $qty;
                $this->MinusStockQty($goods_no,$goods_opt,$minus_qty,9,"",$etc);
            }
        }
    }

    public function Plus($goods_no,$goods_opt,$qty,$stock = array()){

        $row = (array)DB::table('goods')->select("com_id","com_type","goods_type","is_unlimited")
            ->where("goods_no","=",$goods_no)->first();

        $com_id   = $row["com_id"];
        $goods_type = $row["goods_type"];
        $is_unlimited = $row["is_unlimited"];

        if($goods_type == "S" || $goods_type == "I"){

            $type = Lib::getValue($stock, "type", "");
            $invoice_no = Lib::getValue($stock, "invoice_no", "");
            $etc = Lib::getValue($stock, "etc", "");
            $wonga = Lib::getValue($stock, "wonga", "");
            $etc = Lib::getValue($stock, "etc", "");
            $ord_no = Lib::getValue($stock, "ord_no", "");
            $ord_opt_no = Lib::getValue($stock, "ord_opt_no", "");
            $ord_state = Lib::getValue($stock, "ord_state", "");

            if($ord_opt_no == "" || ($ord_opt_no != "" && $ord_state >= 30)){
                $this->PlusStockQty($goods_no,$goods_opt,$qty,$type,
                    $invoice_no,$etc,$wonga,$com_id,$ord_no,$ord_opt_no);
            }
        }
        if($is_unlimited == "N"){
            $this->PlusQty($goods_no,$goods_opt,$qty);
        }
    }

    public function PlusStockQty($goods_no,$goods_opt,$qty,$type,$invoice_no = "INV_ADJUST",$etc = "",$wonga = "",
                          $com_id = "", $ord_no = "",$ord_opt_no = ""){
        $where = "";
        if($wonga != "") $where .= " and wonga = '$wonga' ";

        $this->SetGoodsOpt($goods_no,$goods_opt);

        $query = "
            select no,wonga
            from goods_good
            where goods_no = :goods_no and goods_sub = 0
                and goods_opt = :goods_opt
                and invoice_no = :invoice_no $where
            order by no desc
            limit 0,1
        ";
        $goods_good = DB::select($query,['goods_no' => $goods_no,'goods_opt' => $goods_opt,"invoice_no" => $invoice_no]);
        if(count($goods_good) > 0){
            $row = $goods_good[0];
            $stock_no = $row->no;
            $wonga = $row->wonga;
            $this->__SetStockNo($stock_no);
            $this->__IncreaseGoodQty( $qty );
        }else{

            if($wonga == ""){
                $query = "
                    select wonga from goods
                    where goods_no = :goods_no and goods_sub = 0
                ";
                $rows = DB::select($query,['goods_no' => $goods_no]);
                if(count($rows) > 0){
                    $wonga = $rows[0]->wonga;
                } else {
                    return 0;
                }
            }
            $this->__InsertGoodQty($wonga, $qty, $invoice_no);
        }
        $this->__IncreaseSummaryQty( $qty );

//        if($this->loc != '' && $this->loc != 'LOC'){
//            $loc = $this->loc;
//            $sql = "
//					select qty
//					from goods_location
//					where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
//				";
//            $rs = $this->conn->Execute($sql);
//            if($rows = $rs->fields){
//                $sql = "
//						update goods_location set qty = qty + $qty,ut = now()
//						where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
//					";
//                $this->conn->Execute($sql);
//            } else {
//                $sql = "
//						insert into goods_location ( goods_no,goods_sub,goods_opt,loc, qty, rt, ut )
//						values ( '$goods_no','$goods_sub','$goods_opt','$loc', $qty,now(),now() )
//					";
//                $this->conn->Execute($sql);
//            }
//        }

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
        return $this->__InsertHistory( $history );

    }


    public function PlusQty($goods_no,$goods_opt,$qty){
        $affected =  DB::table('goods_summary')
            ->where('goods_no','=',$goods_no)
            ->where('goods_opt','=',$goods_opt)
            ->increment('good_qty',$qty,['ut' => DB::raw('now()')]);
    }

    function MinusStockQty($goods_no, $goods_opt, $qty, $type, $invoice_no = "", $etc = "",
                           $com_id = "", $ord_no = "",$ord_opt_no = ""){

        $where = "";
        if($invoice_no != "") $where .= " and invoice_no = '$invoice_no' ";

        $stocks = array();

        if($qty > 0){

            $this->SetGoodsOpt($goods_no,$goods_opt);

            if($type == 9){
                $orderby = " order by no desc ";
            } else {
                $orderby = " order by no asc ";
            }
            $plus_qty = $qty;

            $query = "
				select no,wonga,invoice_no,qty
				from goods_good
				where goods_no = :goods_no
					and goods_sub = 0
					and goods_opt = :goods_opt
					and qty > 0 $where
				$orderby
			";
            $goods_good = DB::select($query,['goods_no' => $goods_no,'goods_opt' => $goods_opt]);

            if(count($goods_good) == 0){
                // 차감할 보유재고가 없는 경우 온라인재고를 초기화하기 위해
                if($plus_qty > 0 && $type == 9){
                    $this->__DecreaseSummaryQty( $plus_qty );
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

            for($i=0;$i<count($goods_good);$i++){
                if($plus_qty > 0){
                    $row = $goods_good[$i];
                    $stock_no			= $row->no;
                    $stock_qty			= $row->qty;
                    $stock_wonga		= $row->wonga;
                    $stock_invoice_no	= $row->invoice_no;

                    $this->__SetStockNo( $stock_no );

                    if($plus_qty > $stock_qty) {
                        $plus_qty = $plus_qty - $stock_qty;
                        $stock_minus_qty = $stock_qty;
                    } else {
                        $stock_minus_qty = $plus_qty;
                        $plus_qty = 0;
                    }

                    $this->__DecreaseGoodQty($stock_minus_qty);

//                if($this->loc != ''){
//                    if($this->loc == 'LOC'){
//
//                        $sql = "
//							select
//								s.wqty - ifnull((
//									select sum(qty) as qty from goods_location
//									where goods_no = s.goods_no and goods_sub = s.goods_sub and goods_opt = s.goods_opt
//								),0) as qty
//							from goods_summary s
//							where  s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
//						";
//                        $rs2 = $this->conn->Execute($sql);
//                        $rows = $rs2->fields;
//                        $loc_qty = $rows["qty"];
//                        if($stock_minus_qty > $loc_qty){
//                            return -1;
//                        }
//                    } else {
//                        $loc = $this->loc;
//                        $sql = "
//							select qty
//							from goods_location
//							where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc' and qty >= $stock_minus_qty
//						";
//                        //debugSQL($sql);exit;
//                        $rs2 = $this->conn->Execute($sql);
//                        if($rows = $rs2->fields){
//                            $sql = "
//								update goods_location set qty = qty - $stock_minus_qty ,ut = now()
//								where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
//							";
//                            $this->conn->Execute($sql);
//                        } else {
//                            return -1;
//                        }
//                    }
//                } else {
//
//                    $sql = "
//						select
//							s.wqty - ifnull((
//								select sum(qty) as qty from goods_location
//								where goods_no = s.goods_no and goods_sub = s.goods_sub and goods_opt = s.goods_opt
//							),0) as qty
//						from goods_summary s
//						where  s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.goods_opt = '$goods_opt'
//					";
//
//                    $rs2 = $this->conn->Execute($sql);
//                    $rows = $rs2->fields;
//                    $loc_qty = $rows["qty"];
//                    //echo "$stock_minus_qty > $loc_qty";
//                    //echo debugSQL($sql);
//
//                    if($stock_minus_qty > $loc_qty){
//                        $remain_qty = $stock_minus_qty - $loc_qty;
//                        //echo "$remain_qty = $stock_minus_qty - $loc_qty";
//
//                        $sql = "
//							 select code_id from code
//							 where code_kind_cd = 'G_STOCK_LOC' and code_id <> 'LOC' and use_yn = 'y'
//							 order by code_seq
//						";
//                        $rs2 = &$this->conn->Execute($sql);
//                        while ($remain_qty > 0 && !$rs2->EOF) {
//                            $rows = $rs2->fields;
//                            $loc = $rows["code_id"];
//
//                            $sql = "
//								select qty
//								from goods_location
//								where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
//							";
//                            $rs3 = $this->conn->Execute($sql);
//                            if($rows3 = $rs3->fields){
//
//                                $loc_qty = $rows3["qty"];
//                                $sql = "
//									update goods_location set qty = qty - if(qty >= $remain_qty,$remain_qty,qty) ,ut = now()
//									where goods_no = '$goods_no' and goods_sub = '$goods_sub' and goods_opt = '$goods_opt' and loc = '$loc'
//								";
//                                $this->conn->Execute($sql);
//                                $remain_qty = $remain_qty - $loc_qty;
//                            }
//
//                            $rs2->MoveNext();
//                        }
//                    }
//                }

                    $this->__DecreaseSummaryQty( $stock_minus_qty );

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
            }
            //debugSQL("$plus_qty end");exit;
            if($plus_qty == 0){
                //$this->__DecreaseSummaryQty( $qty );
                //$this->MinusQty($goods_no,$goods_sub,$goods_opt,$qty);
                return $stocks;
            } else {
                if($plus_qty > 0 && $type == 9){
                    $this->__DecreaseSummaryQty( $plus_qty );
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
        } else {
            return 0;
        }
    }


    function __InsertGoodQty( $wonga, $qty, $invoice_no ){

        $goods_good = [
            'goods_no' => $this->goods_no,
            'goods_sub' => 0,
            'goods_opt' => $this->goods_opt,
            'wonga' => $wonga,
            'qty' => $qty,
            'invoice_no' => $invoice_no,
            'init_qty' => $qty,
            'regi_date' => DB::raw('now()')
        ];
        return DB::table('goods_good')->insert($goods_good);

    }

    function __IncreaseGoodQty( $qty ){
        return DB::table('goods_good')
            ->where('no','=',$this->stock_no)
            ->increment('qty',$qty);
    }

    function __DecreaseGoodQty( $qty ){

        if($qty > 0){
            return DB::table('goods_good')
                ->where('no','=',$this->stock_no)
                ->decrement('qty',$qty);
        } else {
            return 0;
        }
    }

    function __IncreaseSummaryQty( $qty ){

        $affected =  DB::table('goods_summary')
            ->where('goods_no','=',$this->goods_no)
            ->where('goods_opt','=',$this->goods_opt)
            ->increment('wqty',$qty,['last_date' => DB::raw('now()')]);

        if($affected == 0){
            $goods_summary = [
                'goods_no' => $this->goods_no,
                'goods_sub' => 0,
                'goods_opt' => $this->goods_opt,
                'opt_name' => $this->opt_name,
                'opt_price' => $this->opt_price,
                'good_qty' => 0,
                'wqty' => $qty,
                'soldout_yn' => 'N',
                'use_yn' => 'Y',
                'seq' => $this->opt_seq,
                'rt' => DB::raw('now()'),
                'ut' => DB::raw('now()'),
                'bad_qty' => 0,
                'last_date' => DB::raw('now()'),
            ];
            DB::table('goods_summary')->insert($goods_summary);
        }
    }

    function __DecreaseSummaryQty( $qty ){

        $affected =  DB::table('goods_summary')
            ->where('goods_no','=',$this->goods_no)
            ->where('goods_opt','=',$this->goods_opt)
            ->update([
                    'wqty' => DB::raw("if( (wqty - $qty) < 0, 0, (wqty - $qty))"),
                    'last_date' => DB::raw('now()')
                ]);
        return $affected;

    }

    public function __InsertHistory( $history ){

        $id = $this->user["id"];
        $name = $this->user["name"];
        $stock_state = 1;

        $lib = new Lib();
        $type			= $lib->getValue($history,"type");
        $wonga			= $lib->getValue($history,"wonga",0);
        $qty			= $lib->getValue($history,"qty");
        $etc			= $lib->getValue($history,"etc");
        $invoice_no		= $lib->getValue($history,"invoice_no");
        $com_id			= $lib->getValue($history,"com_id");
        $ord_no			= $lib->getValue($history,"ord_no");
        $ord_opt_no	    = $lib->getValue($history,"ord_opt_no",0);
        if(empty($ord_opt_no)) $ord_opt_no = 0;

        $goods_history = [
            'goods_no' => $this->goods_no,
            'goods_sub' => 0,
            'goods_opt' => $this->goods_opt,
            'wonga' => $wonga,
            'type' => $type,
            'stock_state' => $stock_state,
            'qty' => $qty,
            'loc' => $this->loc,
            'ord_opt_no' => $ord_opt_no,
            'etc' => $etc,
            'admin_id' => $id,
            'admin_nm' => $name,
            'invoice_no' => $invoice_no,
            'stock_state_date' => DB::raw("date_format(now(),'%Y%m%d')"),
            'com_id' => $com_id,
            'ord_no' => $ord_no,
            'regi_date' => DB::raw('now()')
        ];

        DB::table('goods_history')->insert($goods_history);
        return DB::getPdo()->lastInsertId();;
    }
}
