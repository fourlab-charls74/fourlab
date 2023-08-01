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
use Carbon\Carbon;
use PDO;
use PhpParser\Node\Stmt\Continue_;

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

		$sdate = $request->input('sdate', Carbon::now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));

		$state = $request->input("state");

		$prd_ord_no	= $request->input("prd_ord_no");
		$prd_nm	= $request->input("prd_nm");
		$prd_cd = $request->input("prd_cd_sub");

		$com_id	= $request->input("com_cd");
		$com_nm	= $request->input("com_nm");

		$user_nm_state = $request->input("user_nm_state");
		$user_nm = $request->input("user_nm");

		$ord_field = $request->input("ord_field", 'p1.req_rt');
		$ord = $request->input('ord','desc');
		
		if ($ord_field == 'prd_ord_date') {
			$orderby = sprintf("order by p2.%s %s", $ord_field, $ord);
		} else {
			$orderby = sprintf("order by p1.%s %s", $ord_field, $ord);
		}

		$where = "";
		$where1 = "";
		$having = "";
		if ($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach ($prd_cd as $cd) {
				$where .= " or p1.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}

		if ($user_nm_state != '' && $user_nm != '') {
			if ($user_nm_state == 'req_id') {
				$having = "having req_nm = '$user_nm'";
			} else if ($user_nm_state == 'prc_id') {
				$having = "having prc_nm = '$user_nm'";
			} else if ($user_nm_state == 'fin_id') {
				$having = "having fin_nm = '$user_nm'";
			}
		}

		if ($state != "") $where .= " and p1.state = '" . Lib::quote($state) . "'";
		if ($prd_ord_no != "") $where .= " and p1.prd_ord_no = '" . Lib::quote($prd_ord_no) . "'";
		if ($prd_nm != "") $where .= " and p1.prd_nm like '%" . Lib::quote($prd_nm) . "%' ";
		// if ($com_id != "") $where .= " and p1.com_id = '" . Lib::quote($com_id) . "'";
		if ($com_nm != "") $where1 .= " and cp.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		// if ($user_nm != "") $where .= " and m.name like '%" . Lib::quote($user_nm) . "%' ";

		$page_size	= $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ($page == 1) {
			$sql = /** @lang text */
				"
				select count(*) as total
				from sproduct_stock_order_product p1
					inner join sproduct_stock_order p2 on p1.prd_ord_no = p2.prd_ord_no
					left outer join product p3 on p1.prd_cd = p3.prd_cd
					left outer join product_code p4 on p3.prd_cd = p4.prd_cd
					left outer join product_image i on p1.prd_cd = i.prd_cd
					inner join company cp on p1.com_id = cp.com_id
					left outer join `code` c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and c1.code_id = p4.color
					left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_UNIT' and c3.code_id = p3.unit
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
				p1.idx as idx,
				p2.rt as reg_date,
				p2.prd_ord_date as prd_ord_date,
				p1.prd_ord_no as prd_ord_no,
				p1.state as state,
				cp.com_nm as sup_com_nm,
				p1.prd_cd as prd_cd,
				i.img_url as img,
				p1.prd_nm as prd_nm,
				c1.code_val as color,
				(
                    select s.size_nm from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size,
				c3.code_val as unit,
				ifnull(p1.qty, 0) as qty,
				ifnull(p1.price, 0) as price,
				ifnull(p1.wonga, 0) as wonga,
				p1.qty * p1.price as amount,
				p1.req_rt as rt,
				p1.ut as ut,
				m.name as user_nm,
				p1.req_id as req_id,
				(select name from mgr_user where id = p1.req_id) as req_nm, 
				p1.req_rt as req_rt,
				p1.prc_id as prc_id,
				(select name from mgr_user where id = p1.prc_id) as prc_nm, 
				p1.prc_rt as prc_rt,
				p1.fin_id as fin_id,
				(select name from mgr_user where id = p1.fin_id) as fin_nm, 
				p1.fin_rt as fin_rt
			from sproduct_stock_order_product p1
				inner join sproduct_stock_order p2 on p1.prd_ord_no = p2.prd_ord_no
				left outer join product p3 on p1.prd_cd = p3.prd_cd
				left outer join product_code p4 on p3.prd_cd = p4.prd_cd
				left outer join product_image i on p1.prd_cd = i.prd_cd
				inner join company cp on p1.com_id = cp.com_id
				left outer join `code` c1 on c1.code_kind_cd = 'PRD_CD_COLOR' and c1.code_id = p4.color
				left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_UNIT' and c3.code_id = p3.unit
				left outer join mgr_user m on p2.admin_id = m.id
			where 1=1 and p2.rt >= :sdate and p2.rt < date_add(:edate, interval 1 day)
			$where $where1
			$having
			$orderby
			$limit
		";
		$result = DB::select($sql, ['sdate' => $sdate, 'edate' => $edate]);
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
		$prd_ord_nos = $request->input("prd_ord_nos");
		$prd_cds = $request->input("prd_cds");
		$qties = $request->input("qties");
		$code = 200;
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$date = Carbon::now()->timezone('Asia/Seoul')->format('Y-m-d');
		$stock_state_date = str_replace("-", "", $date); 
		$now = now();

		if (count($prd_ord_nos) > 0) {
			try {
				for ($i = 0; $i < count($prd_ord_nos); $i++) {
					$prd_ord_no = $prd_ord_nos[$i];
					$prd_cd = $prd_cds[$i];
					$change_qty = $qties[$i];

					/**
					 * 변경 전 상태 가져오기
					 */
					$sql = "
						select state from sproduct_stock_order_product
						where prd_cd = :prd_cd and prd_ord_no = :prd_ord_no
					";
					$row = DB::selectOne($sql, ['prd_cd' => $prd_cd, 'prd_ord_no' => $prd_ord_no]);
					$prev_state = $row->state;

					/**
					 * 입고/반품 상태에 따른 수량변경
					 */
					switch ($state) {
						case $prev_state: // 이전상태와 동일한 경우 수량변경 없음
							break;
						case "20": // 입고처리중
							$sql = "
								select in_qty, qty, wqty from product_stock
								where prd_cd = :prd_cd
							";
							$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

							$prc_sql = "
								update sproduct_stock_order_product set prc_id = '$admin_id' , prc_rt = '$now' where prd_ord_no = '$prd_ord_no'
							";
							DB::update($prc_sql);

							if ($row != null) {
								$in_qty = $row->in_qty + $change_qty;
								$qty = $row->qty + $change_qty;
								$wqty = $row->wqty + $change_qty;

								$sql = "
									update product_stock set in_qty = :in_qty, qty = :qty, wqty = :wqty where prd_cd = :prd_cd
								";
								DB::update($sql, ['in_qty' => $in_qty, 'qty' => $qty, 'wqty' => $wqty, 'prd_cd' => $prd_cd]);
							}

							$sql = "
								select wqty from product_stock_storage
								where prd_cd = :prd_cd
							";
							$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

							if ($row != null) {
								$wqty = $row->wqty + $change_qty;
								$sql = "
									update product_stock_storage set wqty = :wqty where prd_cd = :prd_cd
								";
								DB::update($sql, ['wqty' => $wqty, 'prd_cd' => $prd_cd]);
							}
							break;
						case "30": // 입고완료
							if ($prev_state == "20") { // 기존에 입고가 처리중인 경우 storage의 qty도 입고수량만큼 증가.
								$sql = "
									select qty from product_stock_storage
									where prd_cd = :prd_cd
								";
								$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

							$fin_sql = "
								update sproduct_stock_order_product set fin_id = '$admin_id' , fin_rt = '$now' where prd_ord_no = '$prd_ord_no'
							";
							DB::update($fin_sql);

								$res = "
									select 
										a.goods_no as goods_no,
										a.goods_opt as goods_opt,
										b.price as price,
										b.wonga as wonga
									from product_code a
										inner join product b on a.prd_cd = b.prd_cd
									where a.prd_cd = '$prd_cd'
								";
								$r = DB::selectOne($res);

								if ($row != null) {
									$qty = $row->qty + $change_qty;

									$sql = "
										update product_stock_storage set qty = :qty where prd_cd = :prd_cd
									";
									DB::update($sql, ['qty' => $qty, 'prd_cd' => $prd_cd]);

									$query = "
										insert into 
										product_stock_hst(goods_no, prd_cd, goods_opt, location_type, type, price, wonga, qty, stock_state_date, comment, rt, admin_id, admin_nm) 
										values('$r->goods_no', '$prd_cd', '$r->goods_opt', 'STORAGE', '1', '$r->price', '$r->wonga', '$change_qty', '$stock_state_date', '창고입고(원부자재)', '$now', '$admin_id', '$admin_nm')
									";

									DB::insert($query);
								}
								break;
							} else { // 기존 상태가 입고처리중이 아닌 경우 입고처리 후 증가.
								$sql = "
									select in_qty, qty, wqty from product_stock
									where prd_cd = :prd_cd
								";
								$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

								if ($row != null) {
									$in_qty = $row->in_qty + $change_qty;
									$qty = $row->qty + $change_qty;
									$wqty = $row->wqty + $change_qty;
									
									$sql = "
										update product_stock set in_qty = :in_qty, qty = :qty, wqty = :wqty where prd_cd = :prd_cd
									";
									DB::update($sql, ['in_qty' => $in_qty, 'qty' => $qty, 'wqty' => $wqty, 'prd_cd' => $prd_cd]);
								}

								$prc_sql = "
									update sproduct_stock_order_product set prc_id = '$admin_id' , prc_rt = '$now' where prd_ord_no = '$prd_ord_no'
								";
								DB::update($prc_sql);

								$fin_sql = "
									update sproduct_stock_order_product set fin_id = '$admin_id' , fin_rt = '$now' where prd_ord_no = '$prd_ord_no'
								";
								DB::update($fin_sql);

								$sql = "
									select qty, wqty from product_stock_storage
									where prd_cd = :prd_cd
								";
								$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

								if ($row != null) {
									$qty = $row->qty + $change_qty;
									$wqty = $row->wqty + $change_qty;
									$sql = "
										update product_stock_storage set qty = :qty, wqty = :wqty where prd_cd = :prd_cd
									";
									DB::update($sql, ['qty' => $qty, 'wqty' => $wqty, 'prd_cd' => $prd_cd]);
								
								}
								break;
							}
						case "-20": // 반품처리중
							$sql = "
								select out_qty, qty, wqty from product_stock
								where prd_cd = :prd_cd
							";
							$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

							if ($row != null) {
								$out_qty = $row->out_qty + $change_qty;
								$qty = $row->qty - $change_qty;
								$wqty = $row->wqty - $change_qty;
								$sql = "
									update product_stock set out_qty = :out_qty, qty = :qty, wqty = :wqty where prd_cd = :prd_cd
								";
								DB::update($sql, ['out_qty' => $out_qty, 'qty' => $qty, 'wqty' => $wqty, 'prd_cd' => $prd_cd]);
							}

							$prc_sql = "
								update sproduct_stock_order_product set prc_id = '$admin_id' , prc_rt = '$now' where prd_ord_no = '$prd_ord_no'
							";
							DB::update($prc_sql);

							$sql = "
								select wqty from product_stock_storage
								where prd_cd = :prd_cd
							";
							$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

							if ($row != null) {
								$wqty = $row->wqty - $change_qty;
								$sql = "
									update product_stock_storage set wqty = :wqty where prd_cd = :prd_cd
								";
								DB::update($sql, ['wqty' => $wqty, 'prd_cd' => $prd_cd]);
							}
							break;
						case "-30": // 반품완료
							if ($prev_state == "-20") { // 기존에 반품이 처리중인 경우 storage의 qty도 입고수량만큼 감소.
								$sql = "
									select qty from product_stock_storage
									where prd_cd = :prd_cd
								";
								$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

								$prc_sql = "
									update sproduct_stock_order_product set fin_id = '$admin_id' , fin_rt = '$now' where prd_ord_no = '$prd_ord_no'
								";
								DB::update($prc_sql);
								
								$res = "
									select 
										a.goods_no as goods_no,
										a.goods_opt as goods_opt,
										b.price as price,
										b.wonga as wonga
									from product_code a
										inner join product b on a.prd_cd = b.prd_cd
									where a.prd_cd = '$prd_cd'
								";

								$r = DB::selectOne($res);

								if ($row != null) {
									$qty = $row->qty - $change_qty;

									$sql = "
										update product_stock_storage set qty = :qty where prd_cd = :prd_cd
									";
									DB::update($sql, ['qty' => $qty, 'prd_cd' => $prd_cd]);

									$query = "
										insert into 
										product_stock_hst(goods_no, prd_cd, goods_opt, location_type, type, price, wonga, qty, stock_state_date, comment, rt, admin_id, admin_nm) 
										values('$r->goods_no', '$prd_cd', '$r->goods_opt', 'STORAGE', '9', '$r->price', '$r->wonga', '-$change_qty', '$stock_state_date', '창고반품(원부자재)', '$now', '$admin_id', '$admin_nm')
									";
									DB::insert($query);
								}
								break;
							} else { // 기존 상태가 반품처리중이 아닌 경우 반품처리 후 감소.
								$sql = "
									select out_qty, qty, wqty from product_stock
									where prd_cd = :prd_cd
								";
								$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

								$res = "
									select 
										a.goods_no as goods_no,
										a.goods_opt as goods_opt,
										b.price as price,
										b.wonga as wonga
									from product_code a
										inner join product b on a.prd_cd = b.prd_cd
									where a.prd_cd = '$prd_cd'
								";

								$r = DB::selectOne($res);

								if ($row != null) {
									$out_qty = $row->out_qty + $change_qty;
									$qty = $row->qty - $change_qty;
									$wqty = $row->wqty - $change_qty;
									$sql = "
										update product_stock set out_qty = :out_qty, qty = :qty, wqty = :wqty where prd_cd = :prd_cd
									";
									DB::update($sql, ['out_qty' => $out_qty, 'qty' => $qty, 'wqty' => $wqty, 'prd_cd' => $prd_cd]);
								}

								$prc_sql = "
									update sproduct_stock_order_product set prc_id = '$admin_id' , prc_rt = '$now' where prd_ord_no = '$prd_ord_no'
								";
								DB::update($prc_sql);

								$fin_sql = "
									update sproduct_stock_order_product set fin_id = '$admin_id' , fin_rt = '$now' where prd_ord_no = '$prd_ord_no'
								";
								DB::update($fin_sql);

								$sql = "
									select qty, wqty from product_stock_storage
									where prd_cd = :prd_cd
								";
								$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

								if ($row != null) {
									$qty = $row->qty - $change_qty;
									$wqty = $row->wqty - $change_qty;
									$sql = "
										update product_stock_storage set qty = :qty, wqty = :wqty where prd_cd = :prd_cd
									";
									DB::update($sql, ['qty' => $qty, 'wqty' => $wqty, 'prd_cd' => $prd_cd]);

									$query = "
										insert into 
										product_stock_hst(goods_no, prd_cd, goods_opt, location_type, type, price, wonga, qty, stock_state_date, comment, rt, admin_id, admin_nm) 
										values('$r->goods_no', '$prd_cd', '$r->goods_opt', 'STORAGE', '9', '$r->price', '$r->wonga', '-$change_qty', '$stock_state_date', '창고반품(원부자재)', '$now', '$admin_id', '$admin_nm')
									";
									DB::insert($query);
								}
								break;
							}
						default:
							throw new Exception("unallowed state");
							break;
					}

					/**
					 * 입고/반품 상태변경
					 */
					$sql = "
						update sproduct_stock_order_product 
						set state = :state, ut = now()
						where prd_ord_no = :prd_ord_no and prd_cd = :prd_cd
					";
					DB::update($sql, ['state' => $state, 'prd_ord_no' => $prd_ord_no, 'prd_cd' => $prd_cd]);
				}
				$code = 200;
				$msg = '성공';
				DB::commit();
			} catch (Exception $e) {
				$msg = $e->getMessage();
				$code = 500;
				DB::rollback();
			}
		}
		return response()->json(['code' => $code, 'msg' => $msg]);
    }

    public function delete(Request $request) {

		$prd_ord_nos = $request->input("prd_ord_nos");
		$prd_cds = $request->input("prd_cds");
		$code = 200;
		if (count($prd_ord_nos) > 0) {
			for ($i = 0; $i < count($prd_ord_nos); $i++) {
				$prd_ord_no = $prd_ord_nos[$i];
				$prd_cd = $prd_cds[$i];
				try {
					DB::beginTransaction();

					/**
					 * slave 테이블 - 상품코드에 해당되는 입고/반품번호 삭제
					 */
					$sql = "
						delete p1 from sproduct_stock_order_product p1
						where p1.prd_ord_no = :prd_ord_no and p1.prd_cd = :prd_cd and p1.state in ('10', '-10')
					";
					DB::delete($sql, ['prd_ord_no' => $prd_ord_no, 'prd_cd' => $prd_cd]);

					/**
					 * slave 테이블에서 입고/반품번호와 일치하는 데이터가 전부 삭제된 경우 master도 삭제
					 */
					$sql = "
						select count(*) as cnt from sproduct_stock_order_product
						where prd_ord_no = :prd_ord_no
					";
					$result = DB::selectOne($sql, ['prd_ord_no' => $prd_ord_no]);
					if ($result->cnt == 0) {
						$sql = "
							delete from sproduct_stock_order
							where prd_ord_no = :prd_ord_no
						";
						DB::delete($sql, ['prd_ord_no' => $prd_ord_no]);
					}

					$code = 200;
					DB::commit();
				} catch (Exception $e) {
					$code = 500;
					DB::rollback();
				}
			}
		}
		return response()->json(['code' => $code]);
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
		$prd_cd = $request->input("prd_cd_sub");
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
				$where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}

		if ($type != "") $where .= " and pc.brand = '" . Lib::quote($type) . "'";
		if ($prd_nm != "") $where .= " and p.prd_nm like '%" . Lib::quote($prd_nm) . "%' ";
		// if ($com_id != "") $where .= " and p.com_id = '" . Lib::quote($com_id) . "'";
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
				i.img_url as img,
				c7.code_val as color,
				(
                    select s.size_nm from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size,
				c9.code_val as unit,
				ifnull(pss.wqty, 0) as stock_qty,
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
				left outer join code c9 on c9.code_kind_cd = 'PRD_CD_UNIT' and c9.code_id = p.unit
			where p.use_yn = 'Y' and p.type <> 'N'
				$where
			$orderby
			$limit
		";

		$rows = DB::select($query);

		return response()->json([
			"code"	=> 200,
			"com_nm" => $com_nm,
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
		$admin_nm = Auth('head')->user()->name;

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
					DB::table('sproduct_stock_order')->updateOrInsert(
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
					DB::table('sproduct_stock_order_product')->updateOrInsert(
						[
							'prd_ord_no' => $invoice_no,
							'prd_cd' => $prd_cd,
							'com_id' => $sup_com_id
						],
						[
							'state' => $state,
							'prd_nm' => $prd_nm,
							'qty' => $qty,
							'price' => $price,
							'wonga' => $wonga,
							'req_rt' => now(),
							'req_id' => $admin_id,
							'ut' => now(),
						]
					);

					$code = 201;

				} else if ($state == -10) { // 구분이 반품인 경우 반품대기 처리

					$kind = "out";

					/**
					 * 원부자재 상품 입고 master
					 */
					if ($row['stock_qty'] >= $qty ) {
						DB::table('sproduct_stock_order')->updateOrInsert(
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
						DB::table('sproduct_stock_order_product')->updateOrInsert(
							[
								'prd_ord_no' => $invoice_no,
								'prd_cd' => $prd_cd,
								'com_id' => $sup_com_id
							],
							[
								'state' => $state,
								'prd_nm' => $prd_nm,
								'qty' => $qty,
								'price' => $price,
								'wonga' => $wonga,
								'req_rt' => now(),
								'req_id' => $admin_id,
								'ut' => now(),
							]
						);

						$code = 201;
						
					} else {
						$code = 202;
					}
					

				}
				
			}
			DB::commit();
			return response()->json(['message' => 'created', 'code' => $code]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 500);
		}
    }

	//저장
	public function save(Request $request) {

		$rows = $request->input('rows');
		$com_id = $request->input('com_id');
		$sdate = $request->input('sdate');
		$type = $request->input('type');
		$invoice_no = $request->input('invoice_no');
		$prd_ord_date = $request->input('sdate', date("Y-m-d"));
		$prd_ord_date = str_replace("-", "", $prd_ord_date);
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		try {
			DB::beginTransaction();

			foreach($rows as $r) {
				$prd_cd = $r['prd_cd'];
				$prd_nm = $r['goods_nm'];
				$com_id = $r['com_id'];
				$qty = $r['qty'];
				$price = $r['price'];
				$wonga = $r['wonga'];


				if ($type == 10) { //입고

					$kind = "in";

					DB::table('sproduct_stock_order')->updateOrInsert(
						['prd_ord_no' => $invoice_no],
						[
							'kind' => $kind,
							'prd_ord_date' => $prd_ord_date,
							'prd_ord_type' => '',
							'com_id' => $com_id, // 일단은 송장 내용중 가장 최근의 공급업체로 반영되고 있음
							'state' => 10,
							'rt' => now(),
							'ut' => now(),
							'admin_id' => $admin_id
						]
					);

					/**
					 * 원부자재 상품 입고 slave
					 */
					DB::table('sproduct_stock_order_product')->updateOrInsert(
						[
							'prd_ord_no' => $invoice_no,
							'prd_cd' => $prd_cd,
							'com_id' => $com_id
						],
						[
							'state' => 10,
							'prd_nm' => $prd_nm,
							'qty' => $qty,
							'price' => $price,
							'wonga' => $wonga,
							'req_rt' => now(),
							'req_id' => $admin_id,
							'ut' => now(),
						]
					);

					$code = 200;

				} else { //반품

					$kind = "out";

					/**
					 * 원부자재 상품 입고 master
					 */
					if ($r['sg_qty'] >= $qty ) {
						DB::table('sproduct_stock_order')->updateOrInsert(
							['prd_ord_no' => $invoice_no],
							[
								'kind' => $kind,
								'prd_ord_date' => $prd_ord_date,
								'prd_ord_type' => '',
								'com_id' => $com_id, // 송장 내용중 가장 최근의 공급업체로 반영되고 있음
								'state' => -10,
								'rt' => now(),
								'ut' => now(),
								'admin_id' => $admin_id
							]
						);
						/**
						 * 원부자재 상품 입고 slave
						 */
						DB::table('sproduct_stock_order_product')->updateOrInsert(
							[
								'prd_ord_no' => $invoice_no,
								'prd_cd' => $prd_cd,
								'com_id' => $com_id
							],
							[
								'state' => -10,
								'prd_nm' => $prd_nm,
								'qty' => $qty,
								'price' => $price,
								'wonga' => $wonga,
								'req_rt' => now(),
								'req_id' => $admin_id,
								'ut' => now(),
							]
						);

						$code = 201;
						
					} else {
						$code = 202;
					}

				}
			}

			DB::commit();
			return response()->json(['message' => '성공', 'code' => $code]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 500);
		}
    }

	public function getInvoiceNo($com_id) {
		$prefix_invoice_no = sprintf("%s_%s_A", $com_id, date("ymd"));
		$sql = "
			select ifnull(max(prd_ord_no),0) as invoice_no from sproduct_stock_order
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

	// public function changeInput(Request $request) {

	// 	$com_nm = $request->input('com_nm', '');

	// 	try {
	// 		DB::beginTransaction();

	// 		$sql = "
	// 			select 
	// 				*
	// 			from company
	// 			where com_nm = '$com_nm'
	// 			limit 1
	// 		";

	// 		$result = DB::select($sql);
    //         DB::commit();
    //         $code = 200;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $code = 500;
    //     }

    //     return response()->json(["code" => $code, "result" => $result]);

	// }

}