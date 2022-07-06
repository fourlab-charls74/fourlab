<?php

namespace App\Http\Controllers\head\xmd\store;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class store01Controller extends Controller
{

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

		return view( Config::get('shop.head.view') . '/xmd/store/store01',$values);
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

		$values = [
		];

		return view( Config::get('shop.head.view') . '/xmd/store/store01_show',$values);
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
			//$file = sprintf("data/store01/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/xmd/store/store01/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
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

		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

        //try 
		//{
           // DB::beginTransaction();

			$insert_cnt	= 0;
			$insert_sql	= "insert into __tmp_order( ord_no, ord_date, com_type, com_id, com_nm, receipt_no, seq, style_no, opt_kind, brand, goods_code, goods_nm, color, color_nm, size, size_nm, stat_pay_type, goods_sh, price, wonga, sell_type, ord_amt, qty, recv_amt, act_amt, event_cd, pay_fee, store_pay_fee, user_id, ord_nm, ord_nm2, comment, barcode, admin_nm, reg_date ) values ";

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data	= (array)$datas[$i];

				$ord_date		= $data["ord_date"];
				$com_type_nm	= $data["com_type_nm"];
				$com_type		= $this->getTmpCode('com_type', $com_type_nm);
				$com_id			= $data["com_id"];
				$com_nm			= "";
				$receipt_no		= $data["receipt_no"];
				$seq			= $data["seq"];
				$style_no		= $data["style_no"];
				$opt_kind_nm	= $data["opt_kind_nm"];
				$opt_kind		= $this->getTmpCode('opt_kind_cd', $opt_kind_nm);;
				$brand_nm		= $data["brand_nm"];
				$brand			= $this->getTmpCode('brand', $brand_nm);
				$goods_code		= $data["goods_code"];
				$goods_nm		= $data["goods_nm"];
				$color			= $data["color"];
				$color_nm		= $data["color_nm"];
				$size			= $data["size"];
				$size_nm		= $data["size_nm"];
				$stat_pay_type_nm	= $data["stat_pay_type_nm"];
				$stat_pay_type	= $this->getTmpCode('stat_pay_type', $stat_pay_type_nm);
				$goods_sh		= Lib::uncm($data["goods_sh"]);
				$price			= Lib::uncm($data["price"]);
				$wonga			= Lib::uncm($data["wonga"]);
				$sell_type_nm	= $data["sell_type_nm"];
				$sell_type		= $this->getTmpCode('sell_type', $sell_type_nm);
				$ord_amt		= Lib::uncm($data["ord_amt"]);
				$qty			= Lib::uncm($data["qty"]);
				$recv_amt		= Lib::uncm($data["recv_amt"]);
				$act_amt		= Lib::uncm($data["act_amt"]);
				$event_kind_nm	= $data["event_kind_nm"];
				$event_kind		= $this->getTmpCode('event_cd', $event_kind_nm);
				$pay_fee		= $data["pay_fee"];
				$store_pay_fee	= $data["store_pay_fee"];
				$user_id		= $data["user_id"];
				$ord_nm			= $data["ord_nm"];
				$ord_nm2		= $data["ord_nm2"];
				$comment		= $data["comment"];
				$barcode		= $data["barcode"];
				$admin_nm		= $data["admin_nm"];
				$reg_date		= $data["reg_date"];

				$ord_p_date		= str_replace("-","",$ord_date);
				$ord_p_seq		= ( strlen($seq) == 2 ) ? "0".$seq:$seq;

				$ord_no			= $com_id . $ord_p_date . $receipt_no . $ord_p_seq;
	
				if( $com_id != "" ){
					$query	= " select com_nm from __tmp_store where com_id = :com_id ";
					$row	= DB::selectOne($query, ['com_id' => $com_id]);

					if(!empty($row)){
						$com_nm	= $row->com_nm;
					}
				}

				$sql_data	= [
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
	
				if( $rows->cnt == 0 ){
					//if( $insert_cnt > 0 )	$insert_sql .= ",";

					//$insert_sql	.= " ( '$ord_no', '$ord_date', '$com_type', '$com_id', '$com_nm', '$receipt_no', '$seq', '$style_no', '$opt_kind', '$brand', '$goods_code', '$goods_nm', '$color', '$color_nm', '$size', '$size_nm', '$stat_pay_type', '$goods_sh', '$price', '$wonga', '$sell_type', '$ord_amt', '$qty', '$recv_amt', '$act_amt', '$event_kind', '$pay_fee', '$store_pay_fee', '$user_id', '$ord_nm', '$ord_nm2', '$comment', '$barcode', '$admin_nm', '$reg_date' ) ";

					//$insert_cnt++;

					$sql	= "
						insert into __tmp_order( ord_no, ord_date, com_type, com_id, com_nm, receipt_no, seq, style_no, opt_kind, brand, goods_code, goods_nm, color, color_nm, size, size_nm, stat_pay_type, goods_sh, price, wonga, sell_type, ord_amt, qty, recv_amt, act_amt, event_cd, pay_fee, store_pay_fee, user_id, ord_nm, ord_nm2, comment, barcode, admin_nm, reg_date )
						values ( :ord_no, :ord_date, :com_type, :com_id, :com_nm, :receipt_no, :seq, :style_no, :opt_kind, :brand, :goods_code, :goods_nm, :color, :color_nm, :size, :size_nm, :stat_pay_type, :goods_sh, :price, :wonga, :sell_type, :ord_amt, :qty, :recv_amt, :act_amt, :event_kind, :pay_fee, :store_pay_fee, :user_id, :ord_nm, :ord_nm2, :comment, :barcode, :admin_nm, :reg_date )
					";
					DB::insert($sql, $sql_data);
				}
				else{
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
				}
			}

			//if( $insert_cnt > 0 )	DB::insert($insert_sql);
	
			//DB::commit();
        //}
		//catch(Exception $e) 
		//{
        //    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}



		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
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
