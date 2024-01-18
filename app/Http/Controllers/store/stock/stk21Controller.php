<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use App\Models\Conf;

class stk21Controller extends Controller
{
    public function index()
	{
        $stores = DB::table('store')->where('use_yn', '=', 'Y')->select('store_cd', 'store_nm')->get();
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();

		$values = [
            'rel_orders'     => SLib::getCodes("REL_ORDER"), // 출고차수
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'style_no'		=> "", // 스타일넘버
            // 'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'), // 상품구분(2)
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
            'stores'        => $stores, // 매장리스트
            'storages'      => $storages, // 창고리스트
            'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

        return view(Config::get('shop.store.view') . '/stock/stk21', $values);
	}

    // 상품검색
    public function search_goods(Request $request)
    {
        $r = $request->all();

		$code = 200;
		$where = "";
        $orderby = "";
        $prd_cd_range_text = $request->input("prd_cd_range", '');

        // where
		if($r['prd_cd'] != null) {
            $prd_cd = explode(',', $r['prd_cd']);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
        }
        if($r['style_no'] != null)
            $where .= " and if(pc.goods_no <> '0', g.style_no, p.style_no) = '" . $r['style_no'] . "'";

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
        $ord_field = $r['ord_field'] ?? "pc.rt";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $r['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $r['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        // search goods
		$sql = "
            select
                pc.prd_cd
                , pc.goods_no
                , opt.opt_kind_nm
                , if(pc.goods_no <> '0', g.brand, pc.brand) as brand
                , b.brand_nm
                , if(pc.goods_no <> '0', g.style_no, p.style_no) as style_no
                , if(pc.goods_no <> '0', g.goods_nm, p.prd_nm) as goods_nm
                , if(pc.goods_no <> '0', g.goods_nm_eng, p.prd_nm) as goods_nm_eng
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.color
                , color.code_val as color_nm
                , ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
                , pc.goods_opt
                , if(pc.goods_no <> '0', g.goods_sh, p.tag_price) as goods_sh
                , if(pc.goods_no <> '0', g.price, p.price) as price
                , if(pc.goods_no <> '0', g.wonga, p.wonga) as wonga
            from product_code pc
                inner join product p on p.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                left outer join code color on color.code_kind_cd = 'PRD_CD_COLOR' and color.code_id = pc.color
            where pc.type = 'N' $where
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
                from product_code pc
                    inner join product p on p.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                where pc.type = 'N' $where
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

    // 매장/창고별 상품재고 검색
    public function search_stock(Request $request)
    {
		$code = 200;
		$prd_cd = $request->input("prd_cd", '');
        // $store_type = $request->input("store_type", '');
        $store_channel = $request->input('store_channel', '');
        $store_channel_kind = $request->input('store_channel_kind', '');
        $now_date = date('Ymd');
        $where = "";

        if($store_channel != '') $where .= " and s.store_channel = '$store_channel'";
        if($store_channel_kind != '') $where .= " and s.store_channel_kind = '$store_channel_kind'";

		$sql = "
            select
                s.store_cd as dep_store_cd,
                s.store_nm as dep_store_nm,
                ifnull(ps.qty, 0) as qty,
                ifnull(ps.wqty, 0) as wqty,
                ifnull(pss.qty, 0) as storage_qty,
                ifnull(pss.wqty, 0) as storage_wqty,
                sum(ifnull(o.qty, 0)) as total_dep_store_sale_qty
            from store s
                left outer join product_stock_store ps on s.store_cd = ps.store_cd and ps.prd_cd = '$prd_cd'
                left outer join product_stock_storage pss on pss.storage_cd = (select storage_cd from storage where default_yn = 'Y') and pss.prd_cd = '$prd_cd'
            	left outer join order_opt o on o.store_cd = s.store_cd and o.prd_cd = '$prd_cd' and o.ord_state in (30, 60, 61)
            where
                s.use_yn = 'Y'
                and if(s.sdate <= '$now_date' and date_format(date_add(date_format(s.sdate, '%Y-%m-%d'), interval 1 month), '%Y%m%d') >= '$now_date', s.open_month_stock_yn <> 'Y', 1=1)
                and s.store_stock_yn = 'Y'
                $where
            group by s.store_cd
            order by ps.qty desc
		";

		$result = DB::select($sql);

        foreach($result as $r)
        {
            $r->prd_cd = $prd_cd;
        }

        $sql = "
            select
                sum(a.qty) as qty
                , sum(a.wqty) as wqty
                , 0 as rt_qty
            from (
                select
                    s.store_cd as dep_store_cd,
                    s.store_nm as dep_store_nm,
                    ifnull(ps.qty, 0) as qty,
                    ifnull(ps.wqty, 0) as wqty,
                    ifnull(pss.qty, 0) as storage_qty,
                    ifnull(pss.wqty, 0) as storage_wqty
                from store s
                    left outer join product_stock_store ps on s.store_cd = ps.store_cd and ps.prd_cd = '$prd_cd'
                    left outer join product_stock_storage pss on pss.storage_cd = (select storage_cd from storage where default_yn = 'Y') and pss.prd_cd = '$prd_cd'
                where
                    s.use_yn = 'Y'
                    and if(s.sdate <= '$now_date' and date_format(date_add(date_format(s.sdate, '%Y-%m-%d'), interval 1 month), '%Y%m%d') >= '$now_date', s.open_month_stock_yn <> 'Y', 1=1)
                    and s.store_stock_yn = 'Y'
                    $where
            ) as a
        ";

    $row = DB::selectOne($sql);
    $total_data = $row;

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
                "total_data" => $total_data,
			],
			"body" => $result
		]);
    }
	
	// 매장의 특정상품재고 조회
	public function search_stock_for_store(Request $request)
	{
		$prd_cd = $request->input('prd_cd', '');
		$store_cd = $request->input('store_cd', '');
		
		$stock = DB::table('product_stock_store')->where('store_cd', $store_cd)->where('prd_cd', $prd_cd)->select('qty', 'wqty')->first();
		
		$sale_qty = DB::table('order_opt')->where('store_cd', $store_cd)->where('prd_cd', $prd_cd)->whereIn('ord_state', [30, 60, 61])->sum('qty');
		
		return response()->json([ 'code' => 200, 'stock' => $stock, 'total_store_sale_qty' => $sale_qty], 200);
	}

    // 요청RT 등록
    public function request_rt(Request $request)
    {
        $code = 200;
        $msg = '';
        
        $state = 10;
        $rt_type = 'R';
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();
            
            $prd_store_qtys = array_reduce($data, function ($a, $c) {
                $idx = $c['dep_store_cd'] . '^' . $c['prd_cd'];
                if (isset($a[$idx])) {
                    $a[$idx] += $c['rt_qty'];
                } else {
                    $a[$idx] = $c['rt_qty'];
                }
                return $a;
            }, []);
            
            $over_qtys = array_filter($prd_store_qtys, function ($val, $key) {
                list($dep_store_cd, $prd_cd) = explode('^', $key);
                $store_wqty = DB::table('product_stock_store')->where('store_cd', $dep_store_cd)->where('prd_cd', $prd_cd)->value('wqty');
                return $val > $store_wqty;
            }, ARRAY_FILTER_USE_BOTH);
            
            if (count($over_qtys) > 0) {
                $code = 400;
                throw new Exception('보내는 매장의 보유재고를 초과하여 RT를 요청할 수 없습니다.');
            }
			
			$sql = "select ifnull(document_number, 0) + 1 as document_number from product_stock_rotation order by document_number desc limit 1";
			$document_number = DB::selectOne($sql);
			if ($document_number === null) $document_number = 1;
			else $document_number = $document_number->document_number;

			foreach($data as $d) {
                DB::table('product_stock_rotation')
                    ->insert([
						'document_number' => $document_number,
                        'type' => $rt_type,
                        'goods_no' => $d['goods_no'] ?? 0,
                        'prd_cd' => $d['prd_cd'] ?? 0,
                        'goods_opt' => $d['goods_opt'] ?? '',
                        'qty' => $d['rt_qty'] ?? 0,
                        'dep_store_cd' => $d['dep_store_cd'] ?? '',
                        'store_cd' => $d['store_cd'] ?? '',
                        'state' => $state,
                        'req_comment' => $d['comment'] ?? '',
                        'req_id' => $admin_id,
                        'req_rt' => now(),
                        'rt' => now(),
                    ]);

                //RT요청 알림 전송
                $res = DB::table('msg_store')
                    ->insertGetId([
                        'msg_kind' => 'RT',
                        'sender_type' => 'H',
                        'sender_cd' => 'HEAD',
                        'reservation_yn' => 'N',
                        'content' => '본사요청RT가 있습니다.',
                        'rt' => now()
                    ]);

                DB::table('msg_store_detail')
                    ->insert([
                        'msg_cd' => $res,
                        'receiver_type' => 'S',
                        'receiver_cd' => $d['dep_store_cd'] ?? '',
                        'check_yn' => 'N',
                        'rt' => now()
                    ]);
            }

			DB::commit();
            
            $msg = "본사요청RT가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			if ($code === 200) $code = 500;
			$msg = $e->getMessage();
		}

        return response()->json([ "code" => $code, "msg" => $msg ]);
    }

	
	/*
	 * 
	 * 매장RT 엑셀업로드 부분
	 * 
	 * */
	public function batch_show()
	{
		$sql = "
            select
                *
            from code
            where code_kind_cd = 'rel_order' and code_id not like 'R_%'
        ";
		$rel_order_res = DB::select($sql);

		$storages = DB::table("storage")
			->where('use_yn', '=', 'Y')
			->select('storage_cd', 'storage_nm as storage_nm', 'default_yn')
			->orderByRaw('CASE WHEN default_yn = "Y" THEN 0 ELSE 1 END')
			->orderByRaw('CASE WHEN online_yn = "Y" THEN 0 ELSE 1 END')
			->get();

		$values = [
			'today'         => date("Y-m-d"),
			'style_no'		=> "", // 스타일넘버
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
			'items'			=> SLib::getItems(), // 품목
			'storages'      => $storages, // 창고리스트
			'rel_order_res' => $rel_order_res
		];

		return view(Config::get('shop.store.view') . '/stock/stk21_batch', $values);
	}

	public function import_excel(Request $request)
	{
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();

				$save_path = "data/store/stk21/";
				$file_name = "${now}_${user_id}.${extension}";
				
				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}

				$file = sprintf("${save_path}%s", $file_name);
				
				move_uploaded_file($_FILES['file']['tmp_name'], $file);

				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}

	public function get_goods(Request $request)
	{
		$data = $request->input('data', []);
		$result = [];
		$fail_prd_cd = [];
		$success_prd_cd = [];
		$not_prd_cd = [];
		$not_match_prd_cd = [];
		$not_store_cd = [];
		$not_dep_store_cd = [];
		$msg = '';
		$code = 200;
		
		foreach ($data as $row) {
			$prd_cd = $row['prd_cd'];
			$store_cd = $row['store_cd'];
			$dep_store_cd = $row['dep_store_cd'];
			
			//바코드, 온라인코드 검색
			$sql = "
				select prd_cd, goods_no from product_code where prd_cd = :prd_cd
			";
			
			$search_true_prd_cd = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

			// 수령매장 검색
			$sql = "
				select store_cd from store where store_cd = :store_cd
			";

			$search_true_store_cd = DB::selectOne($sql,['store_cd' => $store_cd]);
			
			//보내는 매장 검색
			$sql = "
				select store_cd from store where store_cd = :dep_store_cd
			";

			$search_true_dep_store_cd = DB::selectOne($sql,['dep_store_cd' => $dep_store_cd]);
			
			if (empty($search_true_prd_cd)) {
				array_push($not_prd_cd, $prd_cd);
			}
			
			if ($search_true_prd_cd && isset($search_true_prd_cd->goods_no) && $search_true_prd_cd->goods_no == 0) {
				array_push($not_match_prd_cd, $prd_cd);
			}
			
			if (empty($search_true_store_cd)) {
				array_push($not_store_cd, $store_cd);	
			}
			
			if (empty($search_true_dep_store_cd)) {
				array_push($not_dep_store_cd, $dep_store_cd);	
			}
		}
		
		if (count($not_prd_cd) > 0) {
			$code = 400;
			$msg .= "존재하지않는 상품이 있습니다." . "\n". implode(', ', $not_prd_cd) . "\n";
		}
		
		if (count($not_match_prd_cd) > 0) {
			$code = 400;
			$msg .= "매칭이 되지않은 상품이 존재합니다." . "\n" . implode(', ' , $not_match_prd_cd) . "\n";
		}

		if (count($not_dep_store_cd) > 0) {
			$code = 400;
			$msg .= "존재하지않는 출고매장이 있습니다." . "\n". implode(', ', $not_dep_store_cd) . "\n";
		}
		
		if (count($not_store_cd) > 0) {
			$code = 400;
			$msg .= "존재하지않는 수령매장이 있습니다." . "\n". implode(', ', $not_store_cd) . "\n";
		}
		
		foreach($data as $key => $d)
		{
			$dep_store_cd = $d['dep_store_cd'];
			$store_cd = $d['store_cd'];
			$prd_cd = $d['prd_cd'];
			$qty = $d['qty'];
			$comment = $d['comment']??'';
			
			$sql = "
                select
                    g.goods_no
                    , opt.opt_kind_nm
                    , brand.brand_nm as brand
                    , g.style_no
                    , g.goods_nm
                    , g.goods_nm_eng
                    , g.goods_type as goods_type_cd
                    , com.com_type as com_type_d
                    , s.prd_cd
                    , pc.prd_cd_p as prd_cd_p
                    , pc.color
                    , pc.size
                    , s.goods_opt
                    , (select wqty from product_stock_store where store_cd = '$dep_store_cd' and prd_cd = s.prd_cd) as store_qty
                    , '$qty' as qty
                    , '$store_cd' as store_cd
                    , '$dep_store_cd' as dep_store_cd
                    , (select store_nm from store where store_cd = '$store_cd') as store_nm
                	, (select store_nm from store where store_cd = '$dep_store_cd') as dep_store_nm
                	, '$comment' as comment
                	, p.tag_price as tag_price
                	, p.price as price
                	, p.wonga as wonga
                from goods g
                	inner join product_stock s on g.goods_no = s.goods_no
                	inner join product p on p.prd_cd = s.prd_cd
                    left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
                    left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                    left outer join product_code pc on pc.prd_cd = s.prd_cd
                    left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                    left outer join company com on com.com_id = g.com_id
                    left outer join brand brand on brand.brand = g.brand
                where s.prd_cd = '$prd_cd'
            ";
			$row = DB::selectOne($sql);
			array_push($result, $row);
		}
		
		return response()->json([
			"code" => $code,
			"msg"  => $msg,
			"head" => [
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
			],
			"body" => $result
		]);
	}

	public function batch_request_rt(Request $request)
	{
		$code = 200;
		$msg = '';

		$state = 10;
		$rt_type = 'R';
		$admin_id = Auth('head')->user()->id;
		$data = $request->input("data", []);
		
		try {
			DB::beginTransaction();

			$prd_store_qtys = array_reduce($data, function ($a, $c) {
				$idx = $c['dep_store_cd'] . '^' . $c['prd_cd'];
				if (isset($a[$idx])) {
					$a[$idx] += $c['qty'];
				} else {
					$a[$idx] = $c['qty'];
				}
				return $a;
			}, []);
			
			$over_qtys = array_filter($prd_store_qtys, function ($val, $key) {
				list($dep_store_cd, $prd_cd) = explode('^', $key);
				$store_wqty = DB::table('product_stock_store')->where('store_cd', $dep_store_cd)->where('prd_cd', $prd_cd)->value('wqty');
				return $val > $store_wqty;
			}, ARRAY_FILTER_USE_BOTH);
			
			$keys = array_keys($over_qtys);
			$failed_data = [];
			foreach($keys as $k) {
				$explode_data = explode('^', $k);
				array_push($failed_data, $explode_data);
			}

			if (count($over_qtys) > 0) {
				$code = 400;
				$message = '보내는 매장의 보유재고를 초과하여 RT를 요청할 수 없습니다.'."\n";
				
				foreach ($failed_data as $fd) {
					$message .= "매장코드 : {$fd[0]}, 바코드 : {$fd[1]}" . "\n";
				}
				$message = rtrim($message, ', ');
				throw new Exception($message);
			}

			$sql = "select ifnull(document_number, 0) + 1 as document_number from product_stock_rotation order by document_number desc limit 1";
			$document_number = DB::selectOne($sql);
			if ($document_number === null) $document_number = 1;
			else $document_number = $document_number->document_number;

			$msg_dep_store_cd = [];	
			foreach($data as $d) {
				$dep_store_cd = $d['dep_store_cd'];
				array_push($msg_dep_store_cd, $dep_store_cd);
				
				DB::table('product_stock_rotation')
					->insert([
						'document_number' => $document_number,
						'type' => $rt_type,
						'goods_no' => $d['goods_no'] ?? 0,
						'prd_cd' => $d['prd_cd'] ?? 0,
						'goods_opt' => $d['goods_opt'] ?? '',
						'qty' => $d['qty'] ?? 0,
						'dep_store_cd' => $d['dep_store_cd'] ?? '',
						'store_cd' => $d['store_cd'] ?? '',
						'state' => $state,
						'req_comment' => $d['comment'] ?? '',
						'req_id' => $admin_id,
						'req_rt' => now(),
						'rt' => now(),
					]);

				//RT요청 알림 전송
//				$res = DB::table('msg_store')
//					->insertGetId([
//						'msg_kind' => 'RT',
//						'sender_type' => 'H',
//						'sender_cd' => $admin_id,
//						'reservation_yn' => 'N',
//						'content' => '본사요청RT가 있습니다.',
//						'rt' => now()
//					]);
//
//				DB::table('msg_store_detail')
//					->insert([
//						'msg_cd' => $res,
//						'receiver_type' => 'S',
//						'receiver_cd' => $d['dep_store_cd'] ?? '',
//						'check_yn' => 'N',
//						'rt' => now()
//					]);
			}
			
			/**
			 * RT일괄요청으로 요청 시 매장에 다량의 알리미가 발송되는 문제를 막는 부분
			 * 알리미 기능 전체를 수정해야하지만 일단 일괄등록부터 구현
			 * 알리미가 전송되고 요청RT가 처리되면 RT가 몇 건 남았는지도 보내줘야함
			 * 팀장님과 회의 후 진행해야할 거 같음
			*/
			$msg_dep_store_cnt = array_count_values($msg_dep_store_cd);
			
			$store_dup_cnt = [];
			foreach ($msg_dep_store_cnt as $value => $count) {
				array_push($store_dup_cnt, $value.'^'.$count);
			}
			
			foreach ($store_dup_cnt as $sd) {
				$dep_store_cnt_data = explode('^', $sd);
				$dep_store_cd = $dep_store_cnt_data[0];
				$dep_store_msg_cnt = $dep_store_cnt_data[1];
				
				$res = DB::table('msg_store')
					->insertGetId([
						'msg_kind' => 'RT',
						'sender_type' => 'H',
						'sender_cd' => $admin_id,
						'reservation_yn' => 'N',
						'content' => '본사요청RT이 '.$dep_store_msg_cnt.'건 요청되었습니다.',
						'rt' => now()
					]);

				DB::table('msg_store_detail')
					->insert([
						'msg_cd' => $res,
						'receiver_type' => 'S',
						'receiver_cd' => $dep_store_cd ?? '',
						'check_yn' => 'N',
						'rt' => now()
					]);
			}

			DB::commit();

			$msg = "본사요청RT가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			if ($code === 200) $code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([ "code" => $code, "msg" => $msg ]);
	}
}
