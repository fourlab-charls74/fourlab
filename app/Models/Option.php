<?php

namespace App\Models;

use Exception;
use App\Components\Lib;
use Illuminate\Support\Facades\DB;

class Option { // 20220421 - madforre 추가

	var $user;
	var $goods_no;
	var $goods_sub;
	var $goods_type;

    function __construct($user = [], $goods_no, $goods_sub)
    {
        $this->user = $user;
        $this->goods_no = $goods_no;
        $this->goods_sub = $goods_sub;
    }

	function setGoods( $goods_no, $goods_sub )
    {
		$this->goods_no = $goods_no;
		$this->goods_sub = $goods_sub;
		
		// 상품 구분 조회
		$sql = "SELECT goods_type
			FROM goods
			WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub'
		";
        $result = DB::selectOne($sql);
		$this->goods_type = $result->goods_type;
	}
	
	function addOptionName( $options )
    {
		$type				= Lib::getValue( $options, "type" );
		$name				= Lib::getValue( $options, "name" );
		$required_yn		= Lib::getValue( $options, "required_yn" );
		$use_yn				= Lib::getValue( $options, "use_yn" );
		$option_no			= Lib::getValue( $options, "option_no" );

        $goods_no = $this->goods_no;
        $goods_sub = $this->goods_sub;

		$sql = "SELECT count(*) AS seq 
			FROM goods_option 
			WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub'
			AND `type` = '$type' AND `name` = '$name'
		";
        $result = DB::selectOne($sql);
        $seq = $result->seq;
					
		$sql = "INSERT INTO goods_option (
				goods_no, goods_sub, `type`, `name`, required_yn, use_yn, seq, option_no, rt, ut
			) VALUES (
                :goods_no, :goods_sub, :type, :name, :required_yn, :use_yn, :seq, :option_no, NOW(), NOW()
			)
		";
        DB::insert($sql, [
            'goods_no' => $goods_no,
            'goods_sub' => $goods_sub,
            'type' => $type,
            'name' => $name,
            'required_yn' => $required_yn,
            'use_yn' => $use_yn,
            'seq' => $seq,
            'option_no' => $option_no
        ]);
	}

    /*
	 * 	function : modBasicOption
	 * 		기본 옵션 변경
	 */		
	function modBasicOption( $basic_options ) 
    {	
		$goods_no = $this->goods_no;
		$goods_sub = $this->goods_sub;
		$opt1 = Lib::getValue($basic_options, "opt1");
		$goods_opt = Lib::getValue($basic_options, "goods_opt");
		$opt_qty = Lib::getValue($basic_options, "opt_qty");
		$opt_price = Lib::getValue($basic_options, "opt_price");
		$opt_memo = Lib::getValue($basic_options, "opt_memo");
		
		// 옵션 수량 변경
		if ( $opt_qty != "" ) {

			// 옵션 등록여부 검사
			$sql = "SELECT count(*) AS cnt
				FROM goods_summary
				WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub'
				AND goods_opt = '$goods_opt'
			";
            $result = DB::selectOne($sql);
            $cnt = $result->cnt;
			
			if ( $cnt > 0 ) {

				// 옵션이 있는 경우 변경 수량 변경
				$sql = "UPDATE goods_summary SET
					good_qty = :opt_qty
					WHERE goods_no = :goods_no AND goods_sub = :goods_sub AND goods_opt = :goods_opt
				";
                DB::insert($sql, ['opt_qty' => $opt_qty, 'goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'goods_opt' => $goods_opt]);
				
			} else {

                // 옵션이 없는 경우 - 원래는 등록해야 하지만, 편의를 위해서 처리함 
                $name_cnt = 0;
                $opt_name = "";
                $sql = "SELECT `name`
					FROM goods_option
					WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub' AND `type` = 'basic'
				";

                $collection = collect(DB::select($sql));

                $a_name = [];
                $collection->map(function ($row, $i) use ($a_name, $name_cnt) {
                    $a_name[$i] = $row->name;
                    $name_cnt++;
                });
                
                if ( $name_cnt == 2 ) {
                    $opt_name = $a_name[0] . "^" . $a_name[1];
                } elseif ($name_cnt == 1) {
                    $opt_name = $a_name[0];
                }

                // 없는 경우에는 옵션을 등록해준다!!
                $sql = "INSERT INTO goods_summary (
						goods_no, goods_sub, opt_name, goods_opt, opt_price, opt_memo,
						good_qty, wqty, soldout_yn, use_yn, seq, rt, ut, bad_qty, last_date
					) VALUES (
						:goods_no, :goods_sub, :opt_name, :goods_opt, :opt_price, :opt_memo, :opt_qty,
						0, 'N', 'Y', '0', NOW(), NOW(), 0, NOW()
					)
				";
                DB::insert($sql, [
                    'goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'opt_name' => $opt_name, 'goods_opt' => $goods_opt, 
                    'opt_price' => $opt_price, 'opt_memo' => $opt_memo, 'opt_qty' => $opt_qty
                ]);
            }
        }

        // 옵션 가격 변경
        if ($opt_price != "") {
            $sql = "UPDATE goods_summary SET
				opt_price = :opt_price
				WHERE goods_no = :goods_no AND goods_sub = :goods_sub
				AND goods_opt LIKE '$opt1%'
			";
            DB::update($sql, [
                'opt_price' => $opt_price,
                'goods_no' => $goods_no,
                'goods_sub' => $goods_sub
            ]);
        }

        // 옵션 메모 변경
        if ($opt_memo != "") {
            //옵션 메모가 null일 경우에 대한 쿼리 처리
            if ($opt_memo != "null") {
                $opt_memo = "'$opt_memo'";
            }
            $sql = "UPDATE goods_summary SET
				opt_memo = :opt_memo
				WHERE goods_no = :goods_no AND goods_sub = :goods_sub
				AND goods_opt LIKE '$opt1%'
            ";
            DB::update($sql, [
                'opt_memo' => $opt_memo,
                'goods_no' => $goods_no,
                'goods_sub' => $goods_sub
            ]);
        }

	}

    /*
	 * 	function : delBasicOption
	 * 		기본 옵션 삭제
	 */	
	function delBasicOption( $basic_options ) 
    {
		$goods_no = $this->goods_no;
		$goods_type = $this->goods_type;
		$goods_opt = Lib::GetValue($basic_options, "goods_opt");

        $a_goods_opt = array();

		// 삭제할 옵션
		$sql = "
			select goods_opt
			from goods_good
			where goods_no = '$goods_no' and goods_opt like '%$goods_opt%'
		";

        $collection = collect(DB::select($sql));

        $collection->map(function($row, $i) use ($a_goods_opt) {
            $a_goods_opt[$i] = $row->goods_opt;
        });

		if ( $goods_type == "S" ) {
			$jaego = new Jaego($this->user);
			for ( $i = 0; $i < count($a_goods_opt); $i++ ) {
				$goods_opt = $a_goods_opt[$i];
				$jaego->SetStockQty($goods_no, $goods_opt, 0, "옵션삭제");
			}
		}

		for ( $i = 0; $i < count($a_goods_opt); $i++ ) {
			$goods_opt = $a_goods_opt[$i];

			$sql = "
				delete from goods_good
				where goods_no = :goods_no and goods_opt = :goods_opt
			";
            DB::delete($sql, ["goods_no" => $goods_no, "goods_opt" => $goods_opt]);

			$sql = "
				delete from goods_summary
                where goods_no = :goods_no and goods_opt = :goods_opt
			";
			DB::delete($sql, ["goods_no" => $goods_no, "goods_opt" => $goods_opt]);
		}
	}

}