<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Conf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Exports\ExcelViewExport;
use App\Exports\ExcelSheetViewExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // 출고

class stk10Controller extends Controller
{
    private $rel_states = [
        '10' => '출고요청',
        '20' => '출고처리중',
        '30' => '출고완료',
        '40' => '매장입고',
        '-10' => '출고거부',
    ];

    public function index()
	{
        //로그인한 아이디의 매칭된 매장을 불러옴
        $user_store	= Auth('head')->user()->store_cd;

        $sql = "
            select
                *
            from code
            where code_kind_cd = 'rel_order' and code_id like 'R_%'
        ";
        $rel_order_res = DB::select($sql);

		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'rel_orders'     => SLib::getCodes("REL_ORDER"), // 출고차수
            'rel_order_res' => $rel_order_res,
            'rel_types'     => SLib::getCodes("REL_TYPE"), // 출고구분
            'rel_states'    => $this->rel_states, // 출고상태
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            // 'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'), // 상품구분(2)
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            // 'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
            'user_store'    => $user_store,
		];

        return view(Config::get('shop.shop.view') . '/stock/stk10', $values);
	}

    public function search(Request $request)
    {
		$r	= $request->all();
		
		//로그인한 아이디의 매칭된 매장을 불러옴
		$user_store	= Auth('head')->user()->store_cd;
		$code		= 200;
		$where		= " and psr.store_cd = '" . Lib::quote($user_store) . "' ";
		$orderby	= "";

        // where
        $sdate = str_replace("-", "", $r['sdate'] ?? now()->sub(1, 'week')->format('Ymd'));
        $edate = str_replace("-", "", $r['edate'] ?? date("Ymd"));
        $where .= "
            and cast(if(psr.state > 20, psr.prc_rt, if(psr.state > 10, psr.exp_dlv_day, psr.req_rt)) as date) >= '$sdate' 
            and cast(if(psr.state > 20, psr.prc_rt, if(psr.state > 10, psr.exp_dlv_day, psr.req_rt)) as date) <= '$edate'
        ";

        if($r['rel_order'] != null)
            $where .= " and psr.rel_order like '%" . $r['rel_order'] . "'";
		if($r['rel_type'] != null) 
			$where .= " and psr.type = '" . $r['rel_type'] . "'";
		if($r['state'] != null) 
			$where .= " and psr.state = '" . $r['state'] . "'";
        if($r['ext_done_state'] ?? '' != '')
            $where .= " and psr.state != '40'";
		// if(isset($r['store_no'])) 
		// 	$where .= " and s.store_cd = '" . $r['store_no'] . "'";
		if($r['prd_cd'] != null) {
            $prd_cd = explode(',', $r['prd_cd']);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or psr.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
        }
        // 상품옵션 범위검색
        $range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
        parse_str($r['prd_cd_range'] ?? '', $prd_cd_range);
        foreach ($range_opts as $opt) {
            $rows = $prd_cd_range[$opt] ?? [];
            if (count($rows) > 0) {
                // $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
                $opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
                $where .= " and pc.$opt in ($opt_join) ";
            }
        }
        if(isset($r['goods_stat'])) {
            $goods_stat = $r['goods_stat'];
            if(is_array($goods_stat)) {
                if (count($goods_stat) == 1 && $goods_stat[0] != "") {
                    $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
                } else if (count($goods_stat) > 1) {
                    $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
                }
            } else if($goods_stat != ""){
                $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
            }
        }
        
        $style_no = $r['style_no'];
        $style_nos = $request->input('style_nos', '');
        if($style_nos != '') $style_no = $style_nos;
        $style_no = preg_replace("/\s/",",",$style_no);
        $style_no = preg_replace("/\t/",",",$style_no);
        $style_no = preg_replace("/\n/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);

        if($style_no != ""){
            $style_nos = explode(",", $style_no);
            if(count($style_nos) > 1) {
                if(count($style_nos) > 500) array_splice($style_nos, 500);
                $in_style_nos = join(",", $style_nos);
                $where .= " and g.style_no in ( $in_style_nos ) ";
            } else {
                if ($style_no != "") $where .= " and g.style_no = '" . Lib::quote($style_no) . "' ";
            }
        }

        $goods_no = $r['goods_no'];
        $goods_nos = $request->input('goods_nos', '');
        if($goods_nos != '') $goods_no = $goods_nos;
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

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
		if($r['dc_num'] != null)
			$where	.= " and psr.document_number = '" . $r['dc_num'] . "' ";

        // ordreby
        $ord = $r['ord'] ?? 'desc';
        $ord_field = $r['ord_field'] ?? "psr.req_rt";
        if($ord_field == 'goods_no') $ord_field = 'g.' . $ord_field;
        else $ord_field = 'psr.' . $ord_field;
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
                psr.document_number,
                cast(if(psr.state < 30, psr.exp_dlv_day, psr.prc_rt) as date) as dlv_day,
                c.code_val as rel_type, 
                psr.goods_no, 
                g.style_no, 
                g.goods_nm, 
                g.goods_nm_eng,
                opt.opt_kind_nm,
                brand.brand_nm as brand,
                pc.color,
                d.code_val as color_nm,
                (
                    select s.size_cd from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size,
                psr.prd_cd,
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, 
                psr.qty,
                pss.wqty as storage_wqty,
                pss2.wqty as store_wqty,
                psr.store_cd,
                s.store_nm, 
                psr.storage_cd,
                sg.storage_nm, 
                psr.state, 
                -- cast(psr.exp_dlv_day as date) as exp_dlv_day, 
                psr.exp_dlv_day as exp_dlv_day_data,
                psr.prc_rt as last_release_date,
                psr.rel_order, 
                psr.req_comment,
                psr.storage_comment,
                psr.comment,
                psr.req_id,
                (select name from mgr_user where id = psr.req_id) as req_nm, 
                psr.req_rt, 
                psr.rec_id, 
                (select name from mgr_user where id = psr.rec_id) as rec_nm, 
                psr.rec_rt, 
                psr.prc_id, 
                (select name from mgr_user where id = psr.prc_id) as prc_nm, 
                psr.prc_rt, 
                psr.fin_id, 
                (select name from mgr_user where id = psr.fin_id) as fin_nm, 
                psr.fin_rt,
                if( psr.prc_rt > now(), 'N', 'Y' ) as fin_ok
            from product_stock_release psr
                inner join product_code pc on pc.prd_cd = psr.prd_cd
                inner join product_stock_storage pss on pss.prd_cd = psr.prd_cd and pss.storage_cd = psr.storage_cd
                left outer join product_stock_store pss2 on pss2.prd_cd = psr.prd_cd and pss2.store_cd = psr.store_cd
                left outer join goods g on g.goods_no = psr.goods_no
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                left outer join brand on brand.brand = g.brand
                left outer join code c on c.code_kind_cd = 'REL_TYPE' and c.code_id = psr.type
                left outer join store s on s.store_cd = psr.store_cd
                left outer join storage sg on sg.storage_cd = psr.storage_cd
                left outer join code d on d.code_id = pc.color and d.code_kind_cd = 'PRD_CD_COLOR'
            where 1=1 $where
            $orderby
            $limit
		";
		$result = DB::select($sql);

		// pagination
        $total = 0;
		$total_data = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                	, sum(psr.qty) as total_qty
                from product_stock_release psr
                    inner join product_code pc on pc.prd_cd = psr.prd_cd
                    left outer join goods g on g.goods_no = psr.goods_no
                    left outer join code c on c.code_kind_cd = 'REL_TYPE' and c.code_id = psr.type
                    left outer join store s on s.store_cd = psr.store_cd
                    left outer join storage sg on sg.storage_cd = psr.storage_cd
                where 1=1 $where
                order by psr.rt
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
			$total_data = $row->total_qty;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => $total,
				"total_data" => $total_data,
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
        $rel_order = $request->input("rel_order", '');

        $exp_day = str_replace("-", "", $exp_dlv_day);

        $exp_dlv_day_data = substr($exp_day,2,6);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['state'] != $ori_state) continue;

                $sql = "
                    select pc.prd_cd, pc.goods_no, pc.goods_opt, g.price, g.wonga
                    from product_code pc
                        inner join goods g on g.goods_no = pc.goods_no
                    where prd_cd = :prd_cd
                ";
                $prd = DB::selectOne($sql, ['prd_cd' => $d['prd_cd']]);
                if($prd == null) continue;

                DB::table('product_stock_release')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'qty' => $d['qty'] ?? 0,
                        'exp_dlv_day' => $exp_dlv_day_data,
                        'rel_order' => $exp_dlv_day_data . '-' . $rel_order,
                        'state' => $new_state,
                        'comment' => $d['comment'],
                        'req_comment' => $d['req_comment'],
                        'rec_id' => $admin_id,
                        'rec_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock -> 창고보유재고 차감
                DB::table('product_stock')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->update([
                        'wqty' => DB::raw('wqty - ' . ($d['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                // product_stock_storage -> 보유재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $prd->prd_cd)
                    ->where('storage_cd', '=', $d['storage_cd'])
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
                        'location_cd' => $d['storage_cd'],
                        'location_type' => 'STORAGE',
                        'type' => PRODUCT_STOCK_TYPE_STORAGE_OUT, // 재고분류 : (창고)출고
                        'price' => $prd->price,
                        'wonga' => $prd->wonga,
                        'qty' => ($d['qty'] ?? 0) * -1,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '창고출고',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);

                // product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
                $store_stock_cnt = 
                    DB::table('product_stock_store')
                        ->where('store_cd', '=', $d['store_cd'])
                        ->where('prd_cd', '=', $prd->prd_cd)
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
                        'type' => PRODUCT_STOCK_TYPE_STORE_IN, // 재고분류 : (매장)입고
                        'price' => $prd->price,
                        'wonga' => $prd->wonga,
                        'qty' => $d['qty'] ?? 0,
                        'stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
                        'comment' => '매장입고',
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

    // 출고 (20 -> 30)
    public function release(Request $request) 
    {          
        $ori_state = 20;
        $new_state = 30;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['state'] != $ori_state) continue;

                DB::table('product_stock_release')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'state' => $new_state,
                        'prc_id' => $admin_id,
                        'prc_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock_storage 창고 실재고 차감
                DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $d['prd_cd'])
                    ->where('storage_cd', '=', $d['storage_cd']) 
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

    // 매장입고 (30 -> 40)
    public function receive(Request $request)
    {
        $ori_state = 30;
        $new_state = 40;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                if($d['state'] != $ori_state) continue;

				$sql	= " select state from product_stock_release where idx = :idx ";
				$stock_release	= DB::selectOne($sql,['idx' => $d['idx']]);

				//출고상태 확인후 문제 있으면 패스
				if( $stock_release->state == $new_state ){
					throw new Exception("이미 입고완료된 정보입니다.");
				}
				
                DB::table('product_stock_release')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'state' => $new_state,
                        'fin_id' => $admin_id,
                        'fin_rt' => now(),
                        'ut' => now(),
                    ]);

                // product_stock_store 매장 실재고 플러스
                DB::table('product_stock_store')
                    ->where('prd_cd', '=', $d['prd_cd'])
                    ->where('store_cd', '=', $d['store_cd']) 
                    ->update([
                        'qty' => DB::raw('qty + ' . ($d['qty'] ?? 0)),
                        'ut' => now(),
                    ]);

				DB::table('product_stock_hst')
					->where('release_no', '=', $d['idx'])
					->where('location_type', '=', 'STORE')
					->update([
						//'r_stock_state_date' => DB::raw('stock_state_date'),
						'r_stock_state_date' => date('Ymd'),
						'ut'	=> now()
					]);

            }

			DB::commit();
            $code = 200;
            $msg = "매장입고처리가 정상적으로 완료되었습니다.";
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
                if($d['state'] != $ori_state) continue;

                DB::table('product_stock_release')
                    ->where('idx', '=', $d['idx'])
                    ->update([
                        'state' => $new_state,
                        'comment' => $d['comment'] ?? '',
                        'fin_id' => $admin_id,
                        'fin_rt' => now(),
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

	// 출고 거래명세서 출력
	public function download(Request $request)
	{
		$document_number = $request->input('document_number');
		$idx = $request->input('idx');
		$export = $this->_getDocumentFile($document_number, $idx);
		return Excel::download($export, '출고거래명세서_' . $document_number . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}

	/** 출고 거래명세서 일괄출력 (엑셀파일 형식) */
	public function downloadMulti(Request $request)
	{
		$data = $request->input('data', []);
		$data = array_reduce($data, function($a, $c) {
			$is_already = in_array($c['document_number'], array_map(function($item) { return $item['document_number']; }, $a));
			if (!$is_already) return array_merge($a, [$c]);
			return $a;
		}, []);
		
		$store_cd = Auth('head')->user()->store_cd;
		$store_nm = DB::table('store')->where('store_cd', $store_cd)->value('store_nm');

		$save_path = "data/shop/stk10/";
		$file_name = $store_nm . "_출고거래명세서_일괄출력_" . date('YmdHis') . '.xlsx';

		if (!Storage::disk('public')->exists($save_path)) {
			Storage::disk('public')->makeDirectory($save_path);
		}

		$exports = [];
		foreach ($data as $row) {
			$document_number = $row['document_number'] ?? '';
			$idx = $row['idx'] ?? '';
			$export = $this->_getDocumentFile($document_number, $idx);
			foreach ($export->sheets() as $sht) {
				$exports[] = $sht;
			}
		}

		Excel::store(new ExcelSheetViewExport($exports), sprintf("%s%s%s", "public/", $save_path, $file_name));
		return response()->json([ 'file_path' => sprintf("%s%s%s", "", $save_path, $file_name) ]);
	}

	public function _getDocumentFile($document_number, $idx) {
		$sql = "
			select p.prd_cd
			     , type.code_val as type_nm
			     , g.goods_nm
			     , pc.color
			     , (
                    select s.size_cd from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size
			     , p.qty
			     , g.price
			     , (g.price * p.qty) as total_price
			     , round(g.price / 1.1) as release_price
			     , round(g.price / 1.1 * p.qty) as total_release_price
				 , s.store_nm
			     , s.addr1
			     , s.addr2
				 , s.phone
				 , s.fax
			     , s.biz_no
			     , s.biz_ceo
			     , s.biz_uptae
			     , s.biz_upjong
				 , (select concat(addr1, ifnull(addr2, '')) from storage where storage_cd = p.storage_cd) as storage_addr
				 , (select concat(ifnull(ceo, ''), ' ', phone) from storage where storage_cd = p.storage_cd) as storage_manager
			from product_stock_release p
				inner join goods g on g.goods_no = p.goods_no
				inner join product_code pc on pc.prd_cd = p.prd_cd
				inner join store s on s.store_cd = p.store_cd
				inner join code type on type.code_kind_cd = 'REL_TYPE' and type.code_id = p.type
			where p.document_number = :document_number
				and p.store_cd = (select store_cd from product_stock_release where idx = :idx)
		";
		$rows = DB::select($sql, [ 'document_number' => $document_number, 'idx' => $idx ]);

		$data = [
			'one_sheet_count' => 38,
			'document_number' => sprintf('%04d', $document_number),
			'products' => $rows
		];

		if (count($rows) > 0) {
			$data['receipt_date']		= date('Y-m-d'); // 접수일자? 출고일자? 논의 후 수정필요
			$data['rel_type'] 			= $rows[0]->type_nm ?? '';
			$data['store_nm'] 			= $rows[0]->store_nm ?? '';
			$data['store_addr'] 		= ($rows[0]->addr1 ?? '') . ($rows[0]->addr2);
			$data['store_phone'] 		= $rows[0]->phone ?? '';
			$data['store_fax'] 			= $rows[0]->fax ?? '';
			$data['biz_no'] 			= $rows[0]->biz_no ?? '';
			$data['biz_ceo'] 			= $rows[0]->biz_ceo ?? '';
			$data['biz_uptae'] 			= $rows[0]->biz_uptae ?? '';
			$data['biz_upjong'] 		= $rows[0]->biz_upjong ?? '';
			$data['storage_addr'] 		= $rows[0]->storage_addr ?? '';
			$data['storage_manager'] 	= $rows[0]->storage_manager ?? '';

			$conf = new Conf();
			$company = $conf->getConfig('shop');
			$data['business_registration_number'] = $company['business_registration_number'];
			$data['company_name'] = $company['company_name'];
			$data['company_ceo_name'] = $company['company_ceo_name'];
			$data['company_address'] = $company['company_address'];

			/* 하단 정보는 값등록 후 수정이 필요합니다. */
			$data['company_uptae'] = '도소매';
			$data['company_upjong'] = '의류,신발,악세서리';
			$data['company_office_phone'] = '02) 332-0018';
			$data['company_fax'] = '';
			$data['company_bank_number'] = '국민은행 / 730637-04-005212 / (주) 알펜인터내셔널';
			/* 상단 정보는 값등록 후 수정이 필요합니다. */
		}

		$style = [
			'A1:AH52' => [
				'alignment' => [
					'vertical' => Alignment::VERTICAL_CENTER,
					'horizontal' => Alignment::HORIZONTAL_CENTER
				],
				'font' => [ 'size' => 22, 'name' => '굴림' ]
			],
			'A3:AH3' => [
				'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ],
				'font' => [ 'size' => 25 ]
			],
			'A4' => [ 'alignment' => [ 'textRotation' => true ] ],
			'R4' => [ 'alignment' => [ 'textRotation' => true ] ],
			'A4:AH52' => [
				'borders' => [
					'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ],
					'outline' => [ 'borderStyle' => Border::BORDER_THICK ],
				],
			],
			'M5:Q5' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'AD5:AH5' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'AC49:AH52' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'A9:AH9' => [ 'borders' => [ 'top' => [ 'borderStyle' => Border::BORDER_THICK ] ] ],
			'E5:E6' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'V5:V6' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'F10:F47' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'W10:AH48' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_RIGHT ] ],
			'B5:B8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'S5:S8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'J5:J8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'AA5:AA8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 4 ] ],
			'A48' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 70 ] ],
			'A49:A51' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 10 ] ],
			'V5' => [ 'font' => [ 'size' => 18 ] ],
			'V6' => [ 'font' => [ 'size' => 18 ] ],
			'Q5' => [ 'font' => [ 'size' => 16 ] ],
			'AH5' => [ 'font' => [ 'size' => 16 ] ],
			'B10:Q47' => [ 'font' => [ 'size' => 18 ] ],
			'Y10:AH47' => [ 'font' => [ 'size' => 19 ] ],
			'M2:V2' => [ 'borders' => [ 'bottom' => [ 'borderStyle' => Border::BORDER_THIN ] ] ],
			'K1' => [ 'font' => [ 'size' => 50, 'bold' => true ] ],
		];

		$view_url = Config::get('shop.store.view') . '/stock/stk10_document';
		$keys = [ 'list_key' => 'products', 'one_sheet_count' => $data['one_sheet_count'], 'cell_width' => 8, 'cell_height' => 48 ];
		$images = [[ 'title' => '인감도장', 'public_path' => '/img/stamp.png', 'cell' => 'P4', 'height' => 150 ]];

		return new ExcelViewExport($view_url, $data, $style, $images, $keys);
	}
}
