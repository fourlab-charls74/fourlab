<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\S_Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // (창고)출고

class stk16Controller extends Controller
{
    private $rel_states = [
        '10' => '요청',
        '20' => '접수',
        '30' => '출고',
        '40' => '매장입고',
        '-10' => '거부',
    ];

    public function index()
    {
        $values = [
            'sdate' => now()->sub(1, 'week')->format('Y-m-d'),
            'edate' => date("Y-m-d"),
            'rel_orders' => SLib::getCodes("REL_ORDER"), // 출고차수
            'rel_types' => SLib::getCodes("REL_TYPE"), // 출고구분
            'rel_states' => $this->rel_states, // 출고상태
            'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
            'types' => SLib::getCodes("PRD_MATERIAL_TYPE"), // 원부자재 구분
            'opts' => SLib::getCodes("PRD_MATERIAL_OPT"), // 원부자재 품목
            'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
        ];

        return view(Config::get('shop.store.view') . '/stock/stk16', $values);
    }

    public function search(Request $request)
    {
        $req = $request->all();

        $code = 200;
        $where = "";
        $orderby = "";

        // where
        $sdate = str_replace("-", "", Lib::quote($req['sdate']) ?? now()->sub(1, 'week')->format('Ymd'));
        $edate = str_replace("-", "", Lib::quote($req['edate']) ?? date("Ymd"));

        if ($req['date_type'] != "") {
            if ($req['date_type'] == "req_rt") {
                $where .= " 
                    and cast(psr.req_rt as date) >= '$sdate'
                    and cast(psr.req_rt as date) <= '$edate'
                ";
            } else if ($req['date_type'] == "dlv_day") {
                $where .= " 
                    and cast(psr.exp_dlv_day as date) >= '$sdate'
                    and cast(psr.exp_dlv_day as date) <= '$edate'
                ";
            }
        }

        if ($req['type'] != "") $where .= " and pc.brand = '" . Lib::quote($req['type']) . "'";
        if ($req['opt'] != "") $where .= " and pc.opt = '" . Lib::quote($req['opt']) . "'";
        if ($req['prd_nm'] != "") $where .= " and p.prd_nm like '________%" . Lib::quote($req['prd_nm']) . "%' ";

        if ($req['rel_order'] != null)
            $where .= " and psr.rel_order like '%" . Lib::quote($req['rel_order']) . "'";
        if ($req['rel_type'] != null)
            $where .= " and psr.type = '" . Lib::quote($req['rel_type']) . "'";
        if ($req['state'] != null)
            $where .= " and psr.state = '" . Lib::quote($req['state']) . "'";
        if ($req['ext_done_state'] ?? '' != '')
            $where .= " and psr.state != '40'";
        if (isset($req['store_no']))
            $where .= " and s.store_cd = '" . Lib::quote($req['store_no']) . "'";
        if ($req['prd_cd_sub'] != null) {
            $prd_cd = explode(',', $req['prd_cd_sub']);
            $where .= " and (1!=1";
            foreach ($prd_cd as $cd) {
                $where .= " or psr.prd_cd like '" . Lib::quote($cd) . "%' ";
            }
            $where .= ")";
        }
        if ($req['store_channel'] != '') $where .= "and s.store_channel ='" . Lib::quote($req['store_channel']). "'";
        if ($req['store_channel_kind'] ?? '' != '') $where .= "and s.store_channel_kind ='" . Lib::quote($req['store_channel_kind']). "'";

        // ordreby
        $ord = $req['ord'] ?? 'desc';
        $ord_field = $req['ord_field'] ?? "psr.req_rt";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $req['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $req['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        // search
        $sql = "
            select
                psr.idx,
                cast(if(psr.state < 30, psr.exp_dlv_day, psr.prc_rt) as date) as dlv_day,
                psr.prd_cd, 
                psr.prd_cd,
                i.img_url as img,
                p.prd_cd as prd_cd,
                p.prd_nm as prd_nm,
                c.code_val as type_nm,
                c2.code_val as opt,
                c3.code_val as color,
                size.size_nm as size,
                c5.code_val as unit,
                c6.code_val as rel_type,
                ifnull(p.price, 0) as price,
                ifnull(p.wonga, 0) as wonga,
                psr.qty,
                ifnull(psr.rec_qty, psr.qty) as rec_qty,
                ifnull(psr.prc_qty, psr.qty) as prc_qty,
                psr.store_cd,
                s.store_nm, 
                psr.storage_cd,
                sg.storage_nm, 
                psr.state, 
                cast(psr.exp_dlv_day as date) as exp_dlv_day, 
                -- psr.rel_order,
                c7.code_val3 as rel_order, 
                psr.comment,
                psr.req_comment,
                psr.req_id, 
				ifnull((select name from mgr_user where id = psr.req_id), '') as req_nm,
                psr.req_rt, 
                psr.rec_id, 
                ifnull((select name from mgr_user where id = psr.rec_id), '') as rec_nm,
                psr.rec_rt, 
                psr.prc_id, 
                ifnull((select name from mgr_user where id = psr.prc_id), '') as prc_nm,
                psr.prc_rt, 
                psr.fin_id, 
                ifnull((select name from mgr_user where id = psr.fin_id), '') as fin_nm,
                psr.fin_rt
            from sproduct_stock_release psr
                inner join product p on psr.prd_cd = p.prd_cd
                inner join product_code pc on p.prd_cd = pc.prd_cd
                left outer join product_image i on i.prd_cd = pc.prd_cd
                left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
                -- left outer join `code` c4 on c4.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c4.code_id = pc.size
                left outer join size size on size.size_cd = pc.size and size_kind_cd = 'PRD_CD_SIZE_UNISEX'
                left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
                left outer join `code` c6 on c6.code_kind_cd = 'REL_TYPE' and c6.code_id = psr.type
                left outer join `code` c7 on c7.code_kind_cd = 'REL_ORDER' and c7.code_id = psr.rel_order
                left outer join store s on s.store_cd = psr.store_cd
                left outer join storage sg on sg.storage_cd = psr.storage_cd
            where 1=1 $where
            $orderby
            $limit
		";
        $result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if ($page == 1) {
            $sql = "
                select count(*) as total
                from sproduct_stock_release psr
                    inner join product p on psr.prd_cd = p.prd_cd
                    inner join product_code pc on p.prd_cd = pc.prd_cd
                    left outer join product_image i on i.prd_cd = pc.prd_cd
                    left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                    left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                    left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
                    -- left outer join `code` c4 on c4.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c4.code_id = pc.size
                    left outer join size size on size.size_cd = pc.size and size_kind_cd = 'PRD_CD_SIZE_UNISEX'
                    left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
                    left outer join `code` c6 on c6.code_kind_cd = 'REL_TYPE' and c6.code_id = psr.type
                    left outer join `code` c7 on c7.code_kind_cd = 'REL_ORDER' and c7.code_id = psr.rel_order
                    left outer join store s on s.store_cd = psr.store_cd
                    left outer join storage sg on sg.storage_cd = psr.storage_cd
                where 1=1 $where
                order by psr.rt
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            "code" => $code,
            "head" => [
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ],
            "body" => $result
        ]);
    }
	
	/** 현재 로그인정보 조회 */
	private function _getAdmin()
	{
		return [
			'id' => Auth('head')->user()->id,
			'name' => Auth('head')->user()->name,
		];
	}

	/** 단일 출고정보 조회 */
	private function _getReleaseInfo($idx)
	{
		return DB::table('sproduct_stock_release')->where('idx', $idx)->first();
	}
	
	
    // 접수 (10 -> 20)
    public function receipt(Request $request)
    {
		$code = 200;
		$msg = "";
		$failed_prd_cd = "";

        $ori_state = 10;
        $new_state = 20;
		$admin = $this->_getAdmin();

        $data = $request->input("data", []);
        $rel_order = $request->input("rel_order", '');
        $exp_dlv_day = $request->input("exp_dlv_day", '');
		$exp_dlv_day = str_replace("-", "", $exp_dlv_day);

        try {
            DB::beginTransaction();

			$stock = new S_Stock($admin);

            foreach ($data as $row) {
				$rel_idx = $row['idx'];
				$rel = $this->_getReleaseInfo($rel_idx);
				if ($rel->state != $ori_state) continue;

				$goods = DB::table('product_code')->select('goods_no', 'goods_opt')->where('prd_cd', $rel->prd_cd)->first();
				$rel->goods_no = $goods->goods_no;
				$rel->goods_opt = $goods->goods_opt;

				$qty = ($row['rec_qty'] ?? 0) * 1;

				// 0. 창고수량과 비교하여 접수수량이 더 많을 경우 에러처리
				$storage_qty = DB::table('product_stock_storage')->where([
					'storage_cd' => $rel->storage_cd,
					'prd_cd' => $rel->prd_cd,
				])->value('wqty');

                if ($qty > $storage_qty) {
					$code = -1;
					$failed_prd_cd = $rel->prd_cd;
					throw new Exception("창고수량보다 많은 수량을 출고접수처리할 수 없습니다.");
                }
                
				// 1. 출고테이블 접수처리
                DB::table('sproduct_stock_release')
                    ->where('idx', $rel_idx)
                    ->update([
                        'rec_qty' => $qty,
                        'prc_qty' => $qty,
                        'exp_dlv_day' => $exp_dlv_day,
                        'rel_order' => $rel_order,
                        'state' => $new_state,
                        'comment' => $row['comment'],
                        'rec_id' => $admin['id'],
                        'rec_rt' => now(),
                        'ut' => now(),
                    ]);

				// 2. 창고재고 차감
                // 2-1. product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', $rel->prd_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . $qty),
                        'ut' => now(),
                    ]);
				
                // 2-2. product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', $rel->prd_cd)
                    ->where('storage_cd', $rel->storage_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . $qty),
                        'ut' => now(),
                    ]);

                // 2-3. product_stock_hst -> 재고이력 등록
				$hst_values = (object) array_merge((array) $rel, [
					'location_type' => 'STORAGE',
					'location_cd' => $rel->storage_cd,
					'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT,
					'qty' => $qty * -1,
					'comment' => "창고출고",
				]);
				$stock->insertStockHistory($hst_values);
				
				// 3. 매장재고 증감
				// 3-1. product_stock_store -> 보유재고 증감
				$where = [ 'store_cd' => $rel->store_cd, 'prd_cd' => $rel->prd_cd ];
				$store_stock_collect = DB::table('product_stock_store')->where($where);
				if ($store_stock_collect->count() < 1) {
					DB::table('product_stock_store')->insert([
						'prd_cd' => $rel->prd_cd,
						'store_cd' => $rel->store_cd,
						'qty' => 0,
						'wqty' => $qty,
						'use_yn' => 'Y',
						'rt' => now(),
					]);
				} else {
					$store_stock_collect->update([
						'wqty' => DB::raw('wqty + ' . $qty),
						'ut' => now(),
					]);
				}

				// 3-2. product_stock_hst -> 재고이력 등록
				$hst_values = (object) array_merge((array) $rel, [
					'location_type' => 'STORE',
					'location_cd' => $rel->store_cd,
					'type' => PRODUCT_STOCK_TYPE_STORE_IN,
					'qty' => $qty,
					'comment' => "매장입고",
				]);
				$stock->insertStockHistory($hst_values);
            }

			$msg = "접수처리가 정상적으로 완료되었습니다.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
        }
        return response()->json([ 'code' => $code, 'msg' => $msg, 'prd_cd' => $failed_prd_cd ]);
    }

    // 출고 (20 -> 30)
    public function release(Request $request)
    {
		$code = 200;
		$msg = "";

        $ori_state = 20;
        $new_state = 30;
		$admin = $this->_getAdmin();

        $data = $request->input("data", []);
		$date = Carbon::now()->timezone('Asia/Seoul')->format('Y-m-d');
		$stock_state_date = str_replace("-", "", $date);

        try {
            DB::beginTransaction();

			$stock = new S_Stock($admin);

            foreach ($data as $row) {
				$rel_idx = $row['idx'];
				$rel = $this->_getReleaseInfo($rel_idx);
				if ($rel->state != $ori_state) continue;

				$goods = DB::table('product_code')->select('goods_no', 'goods_opt')->where('prd_cd', $rel->prd_cd)->first();
				$rel->goods_no = $goods->goods_no;
				$rel->goods_opt = $goods->goods_opt;

                $qty = ($row['prc_qty'] ?? 0) * 1;

				// 1. 출고테이블 출고처리
                DB::table('sproduct_stock_release')
					->where('idx', $rel_idx)
                    ->update([
						'prc_qty' => $qty,
                        'state' => $new_state,
                        'prc_id' => $admin['id'],
                        'prc_rt' => now(),
                        'ut' => now(),
                    ]);

				// 2. 창고재고 조정
				// 2-1. product_stock -> 창고보유재고 차감
				DB::table('product_stock')
					->where('prd_cd', $rel->prd_cd)
					->update([
						'wqty' => DB::raw('wqty + ' . $rel->rec_qty . ' - ' . $qty),
						'ut' => now(),
					]);

                // 2-2. product_stock_storage -> 창고실재고 차감 / 창고보유재고 조정
                DB::table('product_stock_storage')
					->where('prd_cd', $rel->prd_cd)
					->where('storage_cd', $rel->storage_cd)
                    ->update([
                        'qty' => DB::raw('qty - ' . $qty),
                        'wqty' => DB::raw('wqty + ' . $rel->rec_qty . ' - ' . $qty),
                        'ut' => now(),
                    ]);

				// 2-3. product_stock_hst -> 재고이력 등록
				if ($rel->rec_qty - $qty != 0) {
					$hst_values = (object) array_merge((array) $rel, [
						'location_type' => 'STORAGE',
						'location_cd' => $rel->storage_cd,
						'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT,
						'qty' => $rel->rec_qty - $qty,
						'comment' => "창고출고",
						'stock_state_date' => $stock_state_date
					]);
					$stock->insertStockHistory($hst_values);
				}

				// 3. 매장재고 조정
				// 3-1. product_stock_store -> 매장보유재고 조정
				DB::table('product_stock_store')
					->where('prd_cd', $rel->prd_cd)
					->where('store_cd', $rel->store_cd)
					->update([
						'wqty' => DB::raw('wqty - ' . $rel->rec_qty . ' + ' . $qty),
						'ut' => now(),
					]);

				// 3-2. product_stock_hst -> 재고이력 등록
				if ($rel->rec_qty - $qty != 0) {
					$hst_values = (object) array_merge((array) $rel, [
						'location_type' => 'STORE',
						'location_cd' => $rel->store_cd,
						'type' => PRODUCT_STOCK_TYPE_STORE_IN,
						'qty' => ($rel->rec_qty - $qty) * -1,
						'comment' => "매장입고",
						'stock_state_date' => $stock_state_date
					]);
					$stock->insertStockHistory($hst_values);
				}
            }

			$msg = "출고처리가 정상적으로 완료되었습니다.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
        }
        return response()->json([ 'code' => $code, 'msg' => $msg ]);
    }

    // 매장입고 (30 -> 40)
    public function receive(Request $request)
    {
		$code = 200;
		$msg = "";

        $ori_state = 30;
        $new_state = 40;
		$admin = $this->_getAdmin();
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

            foreach ($data as $row) {
				$rel_idx = $row['idx'];
				$rel = $this->_getReleaseInfo($rel_idx);
				if ($rel->state != $ori_state) continue;

				// 1. 출고테이블 매장입고처리
                DB::table('sproduct_stock_release')
					->where('idx', $rel_idx)
                    ->update([
                        'state' => $new_state,
                        'fin_id' => $admin['id'],
                        'fin_rt' => now(),
                        'ut' => now(),
                    ]);

                // 2. product_stock_store -> 매장실재고 증감
                DB::table('product_stock_store')
					->where('prd_cd', $rel->prd_cd)
					->where('store_cd', $rel->store_cd)
                    ->update([
                        'qty' => DB::raw('qty + ' . $rel->prc_qty),
                        'ut' => now(),
                    ]);
            }

			$msg = "매장입고처리가 정상적으로 완료되었습니다.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }
		return response()->json([ 'code' => $code, 'msg' => $msg ]);
    }

    // 거부 (10 -> -10)
    public function reject(Request $request)
    {
		$code = 200;
		$msg = "";

        $ori_state = 10;
        $new_state = -10;
		$admin = $this->_getAdmin();
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

            foreach ($data as $row) {
                if ($row['state'] != $ori_state) continue;
				
				// 1. 출고테이블 거부처리
                DB::table('sproduct_stock_release')
                    ->where('idx', $row['idx'])
                    ->update([
                        'state' => $new_state,
                        'comment' => $row['comment'] ?? '',
                        'fin_id' => $admin['id'],
                        'fin_rt' => now(),
                        'ut' => now(),
                    ]);
            }

			$msg = "거부처리가 정상적으로 완료되었습니다.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }
		return response()->json([ 'code' => $code, 'msg' => $msg ]);
    }

    //삭제
    public function del_release(Request $request) 
    {
		$code = 200;
		$msg = "";

		$ori_state = 20;
		$admin = $this->_getAdmin();
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			$stock = new S_Stock($admin);

			foreach ($data as $row) {
				$rel_idx = $row['idx'];
				$rel = $this->_getReleaseInfo($rel_idx);
				if ($rel->state != $ori_state) continue;

				$goods = DB::table('product_code')->select('goods_no', 'goods_opt')->where('prd_cd', $rel->prd_cd)->first();
				$rel->goods_no = $goods->goods_no;
				$rel->goods_opt = $goods->goods_opt;

				$qty = ($rel->rec_qty ?? 0) * 1;
				
				// 1. 창고재고 증감
				// 1-1. product_stock -> 창고보유재고 증감
				DB::table('product_stock')
					->where('prd_cd', $rel->prd_cd)
					->update([
						'wqty' => DB::raw('wqty + ' . $qty),
						'ut' => now(),
					]);

				// 1-2. product_stock_storage -> 창고보유재고 증감
				DB::table('product_stock_storage')
					->where('prd_cd', $rel->prd_cd)
					->where('storage_cd', $rel->storage_cd)
					->update([
						'wqty' => DB::raw('wqty + ' . $qty),
						'ut' => now(),
					]);

				// 1-3. product_stock_hst -> 재고이력 등록
				$hst_values = (object) array_merge((array) $rel, [
					'location_type' => 'STORAGE',
					'location_cd' => $rel->storage_cd,
					'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT,
					'qty' => $qty,
					'comment' => "창고출고삭제",
				]);
				$stock->insertStockHistory($hst_values);
				
				// 2. 매장재고 차감
				// 2-1. product_stock_store -> 매장보유재고 차감
				DB::table('product_stock_store')
					->where('prd_cd', $rel->prd_cd)
					->where('store_cd', $rel->store_cd)
					->update([
						'wqty' => DB::raw('wqty - ' . $qty),
						'ut' => now(),
					]);

				// 2-2. product_stock_hst -> 재고이력 등록
				$hst_values = (object) array_merge((array) $rel, [
					'location_type' => 'STORE',
					'location_cd' => $rel->store_cd,
					'type' => PRODUCT_STOCK_TYPE_STORE_IN,
					'qty' => $qty * -1,
					'comment' => "매장입고삭제",
				]);
				$stock->insertStockHistory($hst_values);

				// 3. 출고테이블 삭제처리
                DB::table('sproduct_stock_release')->where('idx', $rel_idx)->delete();
            }

            $msg = "출고건이 정상적으로 삭제되었습니다.";
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json([ 'code' => $code, 'msg' => $msg ]);
    }
}
