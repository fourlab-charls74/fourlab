<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

const PRODUCT_STOCK_TYPE_STORE_RT = 15;

class stk20Controller extends Controller
{
    private $rt_states = [
        '10' => '요청',
        '20' => '접수',
        '30' => '처리',
        '40' => '완료',
        '-10' => '거부',
    ];

    public function index()
	{
		$values = [
            'store_types' => SLib::getCodes("STORE_TYPE"),
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'rt_states'    => $this->rt_states, // RT상태
            'style_no'		=> "", // 스타일넘버
            // 'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'), // 상품구분(2)
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            // 'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/stock/stk20', $values);
	}

    public function search(Request $request)
    {
        $r = $request->all();

		$code = 200;
		$where = "";
        $orderby = "";
        $prd_cd_range_text = $request->input("prd_cd_range", '');
        
        // where
        $sdate = str_replace("-", "", $r['sdate'] ?? now()->sub(1, 'week')->format('Ymd'));
        $edate = str_replace("-", "", $r['edate'] ?? date("Ymd"));
        $rt_date_state = $r['rt_date_stat'] ?? 10;
        $date_state = "";
        if($rt_date_state == 10) $date_state = "req_rt";
        if($rt_date_state == 20) $date_state = "rec_rt";
        if($rt_date_state == 30) $date_state = "prc_rt";
        if($rt_date_state == 40) $date_state = "fin_rt";
        $where .= "
            and cast(psr.$date_state as date) >= '$sdate'
            and cast(psr.$date_state as date) <= '$edate'
        ";

		if($r['rt_type'] != null)
			$where .= " and psr.type = '" . $r['rt_type'] . "'";
		if(isset($r['send_store_no']))
			$where .= " and psr.dep_store_cd = '" . $r['send_store_no'] . "'";
		if(isset($r['store_no']))
			$where .= " and psr.store_cd = '" . $r['store_no'] . "'";
		if($r['rt_stat'] != null)
			$where .= " and psr.state = '" . $r['rt_stat'] . "'";
        if($r['ext_done_state'] ?? '' != '')
            $where .= " and psr.state != '40'";
		if($r['prd_cd'] != null) {
            $prd_cd = explode(',', $r['prd_cd']);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or psr.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
        }
        if($r['style_no'] != null) 
            $where .= " and g.style_no = '" . $r['style_no'] . "'";

        $goods_no = $r['goods_no'];
        $goods_nos = $request->input('goods_nos', '');
        if($goods_nos != '') $goods_no = $goods_nos;
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

         // 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
			}
		}

        if($goods_no != ""){
            $goods_nos = explode(",", $goods_no);
            if(count($goods_nos) > 1) {
                if(count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(",", $goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        if($r['com_cd'] != null) 
            $where .= " and g.com_id = '" . $r['com_cd'] . "'";
        if($r['item'] != null) 
            $where .= " and g.opt_kind_cd = '" . $r['item'] . "'";
        if(isset($r['brand_cd']))
            $where .= " and g.brand = '" . $r['brand_cd'] . "'";
        if($r['goods_nm'] != null) 
            $where .= " and g.goods_nm like '%" . $r['goods_nm'] . "%'";
        if($r['goods_nm_eng'] != null) 
            $where .= " and g.goods_nm_eng like '%" . $r['goods_nm_eng'] . "%'";

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "psr.req_rt";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        // search
		$sql = "
            select
                psr.idx,
                psr.type,
                psr.goods_no, 
                if(psr.goods_no > 0, g.style_no, p.style_no) as style_no,
                if(psr.goods_no > 0, g.goods_nm, p.prd_nm) as goods_nm,
                if(psr.goods_no > 0, g.goods_nm_eng, p.prd_nm_eng) as goods_nm_eng,
                psr.prd_cd, 
                pc.color,
                pc.size,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                if(psr.goods_no > 0, psr.goods_opt, pc.goods_opt) as goods_opt,
                if(psr.goods_no > 0, g.price, p.price) as price,
                if(psr.goods_no > 0, g.goods_sh, p.tag_price) as goods_sh,
                psr.qty,
                psr.dep_store_cd,
                (select store_nm from store where store_cd = psr.dep_store_cd) as dep_store_nm,
                psr.store_cd, 
                (select store_nm from store where store_cd = psr.store_cd) as store_nm,
                psr.state, 
                cast(psr.exp_dlv_day as date) as exp_dlv_day, 
                psr.req_comment,
                psr.rec_comment,
                psr.req_id, 
                psr.req_rt, 
                psr.rec_id, 
                psr.rec_rt, 
                psr.prc_id, 
                psr.prc_rt, 
                psr.fin_id, 
                psr.fin_rt
            from product_stock_rotation psr
                inner join product_code pc on pc.prd_cd = psr.prd_cd
                inner join product p on p.prd_cd = psr.prd_cd
                left outer join goods g on g.goods_no = psr.goods_no
            where 1=1 and psr.del_yn = 'N' $where
            $orderby
            $limit
		";
		$result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                from product_stock_rotation psr
                    inner join goods g on g.goods_no = psr.goods_no
                    left outer join product_code pc on pc.prd_cd = psr.prd_cd
                where 1=1 $where
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

    // 접수 (10 -> 20)
    public function receipt(Request $request) 
    {
        $ori_state = 10;
        $new_state = 20;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
        $data = $request->input("data", []);
        $exp_dlv_day = $request->input("exp_dlv_day", '');

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['idx'] == "") continue;
                if($d['state'] != $ori_state) continue;

                $sql = "
                    select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
                    from product_code pc
                        inner join goods g on g.goods_no = pc.goods_no
                    where prd_cd = :prd_cd
                ";
                $prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
                if($prd == null) continue;

                DB::table('product_stock_rotation')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'qty' => $d['qty'] ?? 0,
                        'exp_dlv_day' => str_replace("-", "", $exp_dlv_day),
                        'state' => $new_state,
                        'rec_comment' => $d['rec_comment'] ?? '',
                        'rec_id' => $admin_id,
                        'rec_rt' => now(),
                        'ut' => now(),
                    ]);

                // 보내는 매장
                // product_stock_store -> 보유재고 차감
                DB::table('product_stock_store')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->where('store_cd', '=', $d['dep_store_cd']) 
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($d['qty'] ?? 0)),
                        'ut' => now(),
                    ]);
                
                // 재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'goods_no' => $prd->goods_no,
                        'prd_cd' => $prd->prd_cd,
                        'goods_opt' => $prd->goods_opt,
                        'location_cd' => $d['dep_store_cd'],
                        'location_type' => 'STORE',
                        'type' => PRODUCT_STOCK_TYPE_STORE_RT, // 재고분류 : RT출고
                        'price' => $prd->price,
                        'wonga' => $prd->wonga,
                        'qty' => ($d['qty'] ?? 0) * -1,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => 'RT출고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);

                // 받는 매장
                // product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
                $store_stock_cnt = 
                    DB::table('product_stock_store')
                        ->where('prd_cd', '=', $prd->prd_cd)
                        ->where('store_cd', '=', $d['store_cd'])
                        ->count();
                if($store_stock_cnt < 1) {
                    // 해당 매장에 상품 기존재고가 없을 경우
                    DB::table('product_stock_store')
                        ->insert([
                            'goods_no' => $prd->goods_no,
                            'prd_cd' => $prd->prd_cd,
                            'store_cd' => $d['store_cd'],
                            'qty' => 0,
                            'wqty' => $d['qty'] ?? 0,
                            'goods_opt' => $prd->goods_opt,
                            'use_yn' => 'Y',
                            'rt' => now(),
                        ]);
                } else {
                    // 해당 매장에 상품 기존재고가 이미 존재할 경우
                    DB::table('product_stock_store')
                        ->where('prd_cd', '=', $prd->prd_cd)
                        ->where('store_cd', '=', $d['store_cd']) 
                        ->update([
                            'wqty' => DB::raw('wqty + ' . ($d['qty'] ?? 0)),
                            'ut' => now(),
                        ]);
                }

                // 재고이력 등록
                DB::table('product_stock_hst')
                    ->insert([
                        'goods_no' => $prd->goods_no,
                        'prd_cd' => $prd->prd_cd,
                        'goods_opt' => $prd->goods_opt,
                        'location_cd' => $d['store_cd'],
                        'location_type' => 'STORE',
                        'type' => PRODUCT_STOCK_TYPE_STORE_RT, // 재고분류 : RT입고
                        'price' => $prd->price,
                        'wonga' => $prd->wonga,
                        'qty' => $d['qty'] ?? 0,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => 'RT입고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "접수처리가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 보내는매장 출고 (처리) (20 -> 30)
    public function release(Request $request) 
    {          
        $ori_state = 20;
        $new_state = 30;
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['idx'] == "") continue;
                if($d['state'] != $ori_state) continue;

                DB::table('product_stock_rotation')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'state' => $new_state,
                        'prc_id' => $admin_id,
                        'prc_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock_store -> 보내는 매장 실재고 차감
                DB::table('product_stock_store')
                    ->where('prd_cd', '=', $d['prd_cd'])
                    ->where('store_cd', '=', $d['dep_store_cd']) 
                    ->update([
                        'qty' => DB::raw('qty - ' . ($d['qty'] ?? 0)),
                        'ut' => now(),
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "출고처리가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 받는매장 입고 (완료) (30 -> 40)
    public function receive(Request $request) 
    {
        $ori_state = 30;
        $new_state = 40;
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['idx'] == "") continue;
                if($d['state'] != $ori_state) continue;

                DB::table('product_stock_rotation')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'state' => $new_state,
                        'fin_id' => $admin_id,
                        'fin_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock_store 받는 매장 실재고 플러스
                DB::table('product_stock_store')
                    ->where('prd_cd', '=', $d['prd_cd'])
                    ->where('store_cd', '=', $d['store_cd']) 
                    ->update([
                        'qty' => DB::raw('qty + ' . ($d['qty'] ?? 0)),
                        'ut' => now(),
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "완료처리가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 거부 (10 -> -10)
    public function reject(Request $request) 
    {
        $ori_state = 10;
        $new_state = -10;
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['idx'] == "") continue;
                if($d['state'] != $ori_state) continue;

                DB::table('product_stock_rotation')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'state' => $new_state,
                        'rec_comment' => $d['rec_comment'] ?? '',
                        'ut' => now(),
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "거부처리가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 삭제
    public function remove(Request $request) 
    {
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['idx'] == "") continue;

                DB::table('product_stock_rotation')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'del_yn' => "Y",
                        'ut' => now(),
                    ]);
            }

			DB::commit();
            $code = 200;
            $msg = "삭제처리가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
