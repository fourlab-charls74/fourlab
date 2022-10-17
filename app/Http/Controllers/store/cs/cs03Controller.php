<?php

namespace App\Http\Controllers\store\cs;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Stock;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;
use PDO;

class cs03Controller extends Controller
{
    public function index(Request $request) {
        $immutable = CarbonImmutable::now();
        $sdate = $immutable->sub(1, 'month')->format('Y-m-d');
        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'buy_order_states' => SLib::getCodes('G_BUY_ORDER_STATE'),
            'formula_types' => collect([">", "<", ">=", "<=", "=", "<>"]),
            'month3' => (int)date("m"),
            'month2' => (int)$immutable->sub(1, 'month')->format('m'),
            'month1' => (int)$immutable->sub(2, 'month')->format('m'),
        ];
        return view(Config::get('shop.store.view') . '/cs/cs03', $values);
    }

    public function search(Request $request) {
		$page = $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 500);

		$state = $request->input("state");

		$prd_ord_no	= $request->input("prd_ord_no");
		$prd_nm	= $request->input("prd_nm");
		$prd_cd = $request->input("prd_cd");

		$com_id	= $request->input("com_cd");
		$com_nm	= $request->input("com_nm");

		$user_nm = $request->input("user_nm");

		// $limit = $request->input("limit", 100);

		$where = "";
		if ($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach ($prd_cd as $cd) {
				$where .= " or p1.prd_cd = '" . Lib::quote($cd) . "' ";
			}
			$where .= ")";
		}
		
		if ($state != "") $where .= " and p2.state = '" . Lib::quote($state) . "'";
		if ($prd_ord_no != "") $where .= " and p1.prd_ord_no = '" . Lib::quote($prd_ord_no) . "'";
		if ($prd_nm != "") $where .= " and p1.prd_nm like '%" . Lib::quote($prd_nm) . "%' ";
		if ($com_id != "") $where .= " and p1.com_id = '" . Lib::quote($com_id) . "'";
		if ($com_nm != "") $where .= " and cp.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if ($user_nm != "") $where .= " and m.name like '%" . Lib::quote($user_nm) . "%' ";

		$page_size	= $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ($page == 1) {
			$sql = /** @lang text */
				"
				select count(*) as total
				from product_stock_order_product p1
					inner join product_stock_order p2 on p1.prd_ord_no = p2.prd_ord_no
					left outer join product p3 on p1.prd_cd = p3.prd_cd
					left outer join product_code p4 on p3.prd_cd = p4.prd_cd
					left outer join mgr_user m on p2.admin_id = m.id
				where 1=1 $where
			";
			$row = DB::select($sql);
			$total = $row[0]->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$sql = /** @lang text */
		"
			select  
				p2.prd_ord_date as prd_ord_date,
				p1.prd_ord_no as prd_ord_no,
				p2.state as state,
				cp.com_nm as sup_com_nm,
				p1.prd_cd as prd_cd,
				p1.prd_nm as prd_nm,
				c1.code_val as color,
				c2.code_val as size,
				c3.code_val as unit,
				ifnull(p1.qty, 0) as qty,
				ifnull(p1.price, 0) as price,
				ifnull(p1.wonga, 0) as wonga,
				p1.qty * p1.price as amount,
				p2.rt as rt,
				p2.ut as ut,
				m.name as user_nm
			from product_stock_order_product p1
				inner join product_stock_order p2 on p1.prd_ord_no = p2.prd_ord_no
				left outer join product p3 on p1.prd_cd = p3.prd_cd
				left outer join product_code p4 on p3.prd_cd = p4.prd_cd
				inner join company cp on p1.com_id = cp.com_id
				left outer join `code` c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and c1.code_id = p4.color
				left outer join `code` c2 on c2.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c2.code_id = p4.size
				left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_UNIT' and c3.code_id = p3.unit
				left outer join mgr_user m on p2.admin_id = m.id
			where 1=1 $where
			order by p2.prd_ord_date desc, p1.prd_ord_no desc, p1.prd_cd asc
			-- $limit
		";
		$result = DB::select($sql);
		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body"	=> $result
		]);
    }
  
    public function changeState(Request $request) {
		$state = $request->input("state");
		$prd_order_nos = $request->input("prd_order_nos");
		$status = 200;
		if(count($prd_order_nos) > 0){
			try {
				for($i=0;$i<count($prd_order_nos);$i++){
					$prd_order_no = trim($prd_order_nos[$i]);
					if($prd_order_no > 0){
						$sql = "
							update product_stock_order set state = '$state' where prd_order_no = :prd_order_no
						";
						DB::delete($sql, ['prd_order_no' => $prd_order_no]);
					}
				}
				DB::commit();
				$msg = "입고 상태가 변경되었습니다.";
			} catch(Exception $e) {
				DB::rollback();
				$status = 500;
				$msg = "입고 상태 변경 중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
			}
		}
		return response()->json(['code' => $status, 'msg' => $msg], $status);
    }

    public function delete(Request $request) {
		$prd_ord_nos = $request->input("prd_ord_nos");
		$status = 200;
		if(count($prd_ord_nos) > 0){
			try {
				DB::beginTransaction();
				for($i=0;$i<count($prd_ord_nos);$i++){
					$prd_ord_no = trim($prd_ord_nos[$i]);
					if($prd_ord_no > 0){
						$sql = "
							delete from product_stock_order where prd_ord_no = :prd_ord_no and state < 30
						";
						DB::delete($sql, ['prd_ord_no' => $prd_ord_no]);
					}
				}
				DB::commit();
				$msg = "삭제되었습니다.";
			} catch(Exception $e) {
				DB::rollback();
				$status = 500;
				$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
			}
		}
		return response()->json(['code' => $status, 'msg' => $msg], $status);
    }

	public function showBuy(Request $request) {
		$immutable = CarbonImmutable::now();
        $sdate	= $immutable->format('Y-m-d');
        $values = [
            'sdate' => $sdate
        ];
        return view(Config::get('shop.store.view') . '/cs/cs03_show', $values);
	}

	public function searchBuy(Request $request) {
		$page = $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 500);

		$type = $request->input("type");
		$prd_nm	= $request->input("prd_nm");
		$prd_cd = $request->input("prd_cd");
		$com_id	= $request->input("com_cd");
		$com_nm	= $request->input("com_nm");

		$limit = $request->input("limit", 100);
		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field', 'p.prd_cd');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where = "";
		if ($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach ($prd_cd as $cd) {
				$where .= " or p.prd_cd = '" . Lib::quote($cd) . "' ";
			}
			$where .= ")";
		}

		if ($type != "") $where .= " and pc.brand = '" . Lib::quote($type) . "'";
		if ($prd_nm != "") $where .= " and p.prd_nm like '%" . Lib::quote($prd_nm) . "%' ";
		if ($com_id != "") $where .= " and p.com_id = '" . Lib::quote($com_id) . "'";
		if ($com_nm != "") $where .= " and cp.com_nm like '%" . Lib::quote($com_nm) . "%' ";

		$page_size	= $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ($page == 1) {
			$query	= /** @lang text */
				"
				select count(*) as total from product p 
					inner join product_code pc on p.prd_cd = pc.prd_cd
					inner join product_image i on p.prd_cd = i.prd_cd
					inner join company cp on p.com_id = cp.com_id
				where p.use_yn = 'Y' 
					$where
			";
			$row	= DB::select($query);
			$total	= $row[0]->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$query = /** @lang text */
		"
			select
				cp.com_id as sup_com_id,
				cp.com_nm as sup_com_nm,
				p.prd_cd as prd_cd,
				p.prd_nm as prd_nm,
				pc.img_url as img,
				c7.code_val as color,
				c8.code_val as size,
				c9.code_val as unit,
				ifnull(ps.wqty, 0) as qty_1, 
				ifnull(pss.qty, 0) as qty_2,
				0 as in_qty,
				ifnull(p.price, 0) as price,
				ifnull(p.wonga, 0) as wonga,
				0 as amount,
				p.type as type
			from product p
				inner join product_code pc on p.prd_cd = pc.prd_cd
				left outer join product_image i on p.prd_cd = i.prd_cd
				inner join company cp on p.com_id = cp.com_id
				left outer join product_stock ps on p.prd_cd = ps.prd_cd
				left outer join product_stock_storage pss on p.prd_cd = pss.prd_cd
				left outer join code c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
				left outer join code c7 on c7.code_kind_cd = 'PRD_CD_COLOR' and c7.code_id = pc.color
				left outer join code c8 on c8.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c8.code_id = pc.size
				left outer join code c9 on c9.code_kind_cd = 'PRD_CD_UNIT' and c9.code_id = p.unit
			where p.use_yn = 'Y'
				$where
			$orderby
			$limit
		";

		$rows = DB::select($query);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($rows)
			),
			"body"	=> $rows
		]);
	}

	public function addBuy(Request $request) {

		$data = $request->input('rows');
		$invoice_no = $request->input('invoice_no');
		$state = $request->input('state');
		$prd_ord_date = $request->input('sdate', date("Y-m-d"));
		$prd_ord_date = str_replace("-", "", $prd_ord_date);
		$prd_ord_type = $request->input('prd_ord_type');
		$admin_id = Auth('head')->user()->id;

		try {

			DB::beginTransaction();

			foreach ($data as $row) {

				$sup_com_id = $row['sup_com_id'];
				$prd_cd = $row['prd_cd'];
				$prd_nm = $row['prd_nm'];
				
				$qty = $row['in_qty'];
				$price = $row['price'];
				$wonga = $row['wonga'];

				if ($state == 10) { // 구분이 입고인 경우 입고대기 처리

					$kind = "in";

					/**
					 * 원부자재 상품 입고 master
					 */
					DB::table('product_stock_order')->updateOrInsert(
						['prd_ord_no' => $invoice_no],
						[
							'kind' => $kind,
							'prd_ord_date' => $prd_ord_date,
							'prd_ord_type' => $prd_ord_type,
							'com_id' => $sup_com_id, // 일단은 송장 내용중 가장 최근의 공급업체로 반영되고 있음
							'state' => $state,
							'rt' => now(),
							'ut' => now(),
							'admin_id' => $admin_id
						]
					);

					/**
					 * 원부자재 상품 입고 slave
					 */
					DB::table('product_stock_order_product')->updateOrInsert(
						[
							'prd_ord_no' => $invoice_no,
							'prd_cd' => $prd_cd,
							'com_id' => $sup_com_id
						],
						[
							'prd_nm' => $prd_nm,
							'qty' => $qty,
							'price' => $price,
							'wonga' => $wonga,
							'rt' => now(),
							'ut' => now(),
							'admin_id' => $admin_id
						]
					);

				} else if ($state == -10) { // 구분이 반품인 경우 반품대기 처리

					$kind = "out";

					/**
					 * 원부자재 상품 입고 master
					 */
					DB::table('product_stock_order')->updateOrInsert(
						['prd_ord_no' => $invoice_no],
						[
							'kind' => $kind,
							'prd_ord_date' => $prd_ord_date,
							'prd_ord_type' => $prd_ord_type,
							'com_id' => $row['sup_com_id'], // 송장 내용중 가장 최근의 공급업체로 반영되고 있음
							'state' => $state,
							'rt' => now(),
							'ut' => now(),
							'admin_id' => $admin_id
						]
					);
					/**
					 * 원부자재 상품 입고 slave
					 */
					DB::table('product_stock_order_product')->updateOrInsert(
						[
							'prd_ord_no' => $invoice_no,
							'prd_cd' => $prd_cd,
							'com_id' => $sup_com_id
						],
						[
							'prd_nm' => $prd_nm,
							'qty' => $qty,
							'price' => $price,
							'wonga' => $wonga,
							'rt' => now(),
							'ut' => now(),
							'admin_id' => $admin_id
						]
					);

				}
				
			}
			DB::commit();
			return response()->json(['message' => 'created'], 201);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 500);
		}
    }

	public function getInvoiceNo($com_id) {
		$prefix_invoice_no = sprintf("%s_%s_A", $com_id, date("ymd"));
		$sql = "
			select ifnull(max(prd_ord_no),0) as invoice_no from product_stock_order
			where prd_ord_no like '$prefix_invoice_no%'
		";
		$row = DB::selectOne($sql);
		$max_invoice_no = $row->invoice_no;
		if ($max_invoice_no == "0") {
			$seq = 1;
		} else {
			$seq = str_replace($prefix_invoice_no, "", $max_invoice_no);
			$seq = $seq + 1;
		}
		$invoice_no = sprintf("%s%03d", $prefix_invoice_no, $seq);
		return $invoice_no;
	}

}


// /**
//  * 기존의 입고수량을 가져와서 추가하려는 입고수량과 합산
//  */
// $sql = "
// 	select qty from product_stock_order_product
// 	where prd_ord_no = :invoice_no and prd_cd = :prd_cd
// ";
// $result = DB::selectOne($sql, ['invoice_no' => $invoice_no, 'prd_cd' => $prd_cd]);
// if ($result != null) $qty = $row->qty + $row['in_qty'];