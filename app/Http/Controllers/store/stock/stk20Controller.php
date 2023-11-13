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
use App\Exports\ExcelViewExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

const PRODUCT_STOCK_TYPE_STORE_RT = 15;

class stk20Controller extends Controller
{
    private $rt_states = [
        '10' => 'RT요청',
        '20' => 'RT접수',
        '30' => 'RT처리중',
        '40' => 'RT완료',
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
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
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
                psr.document_number,
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
		$total_data = 0;
        $page_cnt = 0;
        if($page == 1) {
//            $sql = "
//                select count(*) as total
//                from product_stock_rotation psr
//                    inner join goods g on g.goods_no = psr.goods_no
//                    left outer join product_code pc on pc.prd_cd = psr.prd_cd
//                where 1=1 and psr.del_yn = 'N' $where
//            ";
			
			$sql = "
				select
				    sum(t.qty) as qty
					, count(*) as total
				from (
					select
						psr.idx
						, psr.qty
					from product_stock_rotation psr
						inner join product_code pc on pc.prd_cd = psr.prd_cd
						inner join product p on p.prd_cd = psr.prd_cd
						left outer join goods g on g.goods_no = psr.goods_no
					where 1=1 and psr.del_yn = 'N' $where
					$orderby
				) t
			";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
			$total_data = $row;
        }

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result),
				"total_data" => $total_data,
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
		$store_info_arr = [];

        try {
            DB::beginTransaction();

			$msg_dep_store_cd = [];
			$dep_store_arr = [];
			$msg_rec_store_cd = [];
			$rec_store_cd = [];
			$store_cnt = [];
			$store = [];
			
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

				$dep_store_cd = $d['dep_store_cd'];
				$rec_store_cd = $d['store_cd'];
				
				array_push($msg_dep_store_cd, $dep_store_cd);
				array_push($msg_rec_store_cd, $rec_store_cd);
				
//			
				// $store_info_arr에 저장된 정보가 없으면 배열을 초기화
				if (!isset($store_info_arr[$dep_store_cd])) {
					$store_info_arr[$dep_store_cd] = [];
				}

				// 중복된 받는 매장을 방지하기 위해 배열에 추가
				if (!in_array($rec_store_cd, $store_info_arr[$dep_store_cd])) {
					$store_info_arr[$dep_store_cd][] = $rec_store_cd;
				}

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

                //RT접수 알림 전송
//                $content = $d['dep_store_nm'] . '에서 ';
//                if ($d['type'] == 'R') $content .= '요청RT를 접수하였습니다.';
//                else if ($d['type'] == 'G') $content .= '일반RT를 접수하였습니다.';
//
//                $res = DB::table('msg_store')
//                    ->insertGetId([
//                        'msg_kind' => 'RT',
//                        'sender_type' => 'S',
//                        'sender_cd' => $d['dep_store_cd'] ?? '',
//                        'reservation_yn' => 'N',
//                        'content' => $content,
//                        'rt' => now()
//                    ]);
//                
//                DB::table('msg_store_detail')
//                    ->insert([
//                        'msg_cd' => $res,
//                        'receiver_type' => 'S',
//                        'receiver_cd' => $d['store_cd'] ?? '',
//                        'check_yn' => 'N',
//                        'rt' => now()
//                    ]);
            }

			/**
			 * RT접수 시 리스트에서 여러매장을 선택 후 접수를 하면 RT가 많을 시 한 매장에 엄청난 알리미가 발송되는 문제를 막는 부분
			 * 매장 당 1개의 알리미로 몇 개의 RT가 접수되었는지 알리미를 발송하는 부분
			 */
			
			$msg_dep_store_cnt = array_count_values($msg_dep_store_cd);
			
			$store_dup_cnt = [];
			foreach ($msg_dep_store_cnt as $value => $count) {
				array_push($store_dup_cnt, $value . '^' . $count);
			}

			foreach ($store_dup_cnt as $sd) {
				$dep_store_cnt_data = explode('^', $sd);
				$dep_store_cd = $dep_store_cnt_data[0];
				$dep_store_msg_cnt = $dep_store_cnt_data[1];
				
				$res = DB::table('msg_store')
					->insertGetId([
						'msg_kind' => 'RT',
						'sender_type' => 'S',
						'sender_cd' => $dep_store_cd ?? '',
						'reservation_yn' => 'N',
						'content' => '매장요청RT가 '. $dep_store_msg_cnt .'건 접수되었습니다.',
						'rt' => now()
					]);

				$store_info = $store_info_arr[$dep_store_cd] ?? null;
				if ($store_info) {
					foreach ($store_info as $receiver_cd) {
						DB::table('msg_store_detail')
							->insert([
								'msg_cd' => $res,
								'receiver_type' => 'S',
								'receiver_cd' => $receiver_cd ?? '',
								'check_yn' => 'N',
								'rt' => now()
							]);
					}
				}
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

                //RT처리 알림 전송
                $content = $d['dep_store_nm'] . '에서 ';
                if ($d['type'] == 'R') $content .= '요청RT를 처리하였습니다.';
                else if ($d['type'] == 'G') $content .= '일반RT를 처리하였습니다.';

                $res = DB::table('msg_store')
                    ->insertGetId([
                        'msg_kind' => 'RT',
                        'sender_type' => 'S',
                        'sender_cd' => $d['dep_store_cd'] ?? '',
                        'reservation_yn' => 'N',
                        'content' => $content,
                        'rt' => now()
                    ]);
                
                DB::table('msg_store_detail')
                    ->insert([
                        'msg_cd' => $res,
                        'receiver_type' => 'S',
                        'receiver_cd' => $d['store_cd'] ?? '',
                        'check_yn' => 'N',
                        'rt' => now()
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
	
	// 전표출력
	public function download(Request $request)
	{
		$document_number = $request->input('document_number');
		$idx = $request->input('idx');
		
		$sql = "
			select p.prd_cd
			     , g.goods_nm
			     , pc.color
			     , ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				 ),'') as size
			     , p.qty
			     , p.rec_comment
			     , if(p.type = 'R', '요청RT', if(p.type = 'G', '일반RT', '')) as type
			     , (select store_nm from store where store_cd = p.dep_store_cd) as dep_store_nm
			     , (select store_nm from store where store_cd = p.store_cd) as store_nm
			     , p.prc_rt
			     , p.fin_rt
			from product_stock_rotation p
				inner join goods g on g.goods_no = p.goods_no
				inner join product_code pc on pc.prd_cd = p.prd_cd
			where p.document_number = :document_number
				and p.dep_store_cd = (select dep_store_cd from product_stock_rotation where idx = :idx)
				and p.store_cd = (select store_cd from product_stock_rotation where idx = :idx2)
		";
		$rows = DB::select($sql, [ 'document_number' => $document_number, 'idx' => $idx, 'idx2' => $idx ]);
		
		$data = [
			'document_number' => sprintf('%04d', $document_number),
			'products' => $rows
		];
		
		if (count($rows) > 0) {
			$data['type'] 			= $rows[0]->type ?? '';
			$data['dep_store_nm'] 	= $rows[0]->dep_store_nm ?? '';
			$data['store_nm'] 		= $rows[0]->store_nm ?? '';
			
			if (isset($rows[0]->prc_rt)) {
				$prc_rt = Carbon::parse($rows[0]->prc_rt);
				$data['prc_rt_yyyy'] 	= $prc_rt->format('Y');
				$data['prc_rt_mm']		= $prc_rt->format('m');
				$data['prc_rt_dd'] 		= $prc_rt->format('d');
			}

			if (isset($rows[0]->fin_rt)) {
				$fin_rt = Carbon::parse($rows[0]->fin_rt);
				$data['fin_rt_yyyy'] 	= $fin_rt->format('Y');
				$data['fin_rt_mm'] 		= $fin_rt->format('m');
				$data['fin_rt_dd'] 		= $fin_rt->format('d');
			}
		}
		
		$style = [
			'A6:Z35' => [ 'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ] ] ],
			'U1:Z4' => [ 'borders' => [ 
				'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ],
				'outline' => [ 'borderStyle' => Border::BORDER_MEDIUM ],
			] ],			
			'T6:Z7' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'A1:Z35' => [ 
				'alignment' => [ 
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					'vertical' => Alignment::VERTICAL_CENTER,
				],
				'font' => [ 'size' => 14 ]
			],
			'F9:F33' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'U9:U33' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'A36:A37' => [ 
				'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_RIGHT ],
				'font' => [ 'size' => 14 ]
			],
			'A6:A7' => [ 'font' => [ 'bold' => true ] ],
			'H6:H7' => [ 'font' => [ 'bold' => true ] ],
			'Q6:Q7' => [ 'font' => [ 'bold' => true ] ],
			'V6:V7' => [ 'font' => [ 'bold' => true ] ],
			'X6:X7' => [ 'font' => [ 'bold' => true ] ],
			'Z6:Z7' => [ 'font' => [ 'bold' => true ] ],
			'A8:Z8' => [ 'font' => [ 'bold' => true ] ],
			'A34:S34' => [ 'font' => [ 'bold' => true ] ],
			'G3' => [ 'font' => [ 'bold' => true, 'size' => 30 ] ],
		];

		$view_url = Config::get('shop.store.view') . '/stock/stk20_document';
		$keys = [ 'list_key' => 'products', 'one_sheet_count' => 25, 'cell_width' => 6, 'cell_height' => 33 ];
		
		return Excel::download(new ExcelViewExport($view_url, $data, $style, null, $keys), 'RT전표.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
}
