<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

const PRODUCT_STOCK_TYPE_STORE_RT = 15;

class stk22Controller extends Controller
{
    public function index()
    {
        $stores = DB::table('store')->where('use_yn', '=', 'Y')->select('store_cd', 'store_nm')->get();
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();

        $values = [
            'today'         => date("Y-m-d"),
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

        return view(Config::get('shop.shop.view') . '/stock/stk22', $values);
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

        // if($r['com_cd'] != null)
        //     $where .= " and g.com_id = '" . $r['com_cd'] . "'";
        // if($r['item'] != null)
        //     $where .= " and g.opt_kind_cd = '" . $r['item'] . "'";
        // if(isset($r['brand_cd']))
        //     $where .= " and g.brand = '" . $r['brand_cd'] . "'";
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
                , (
                    select s.size_cd from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size
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
        //로그인한 아이디의 매칭된 매장을 불러옴
        $user_store	= Auth('head')->user()->store_cd;
        $user_store_nm   = Auth('head')->user()->store_nm;

        $code = 200;
        $prd_cd = $request->input("prd_cd", '');
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
                '$user_store' as store_cd,
                '$user_store_nm' as store_nm,
				ifnull((select qty from product_stock_store where store_cd = '$user_store' and prd_cd = '$prd_cd'), 0) as send_qty,
				ifnull((select wqty from product_stock_store where store_cd = '$user_store' and prd_cd = '$prd_cd'), 0) as send_wqty
            from store s
                left outer join product_stock_store ps on s.store_cd = ps.store_cd and ps.prd_cd = '$prd_cd'
                left outer join product_stock_storage pss on pss.storage_cd = (select storage_cd from storage where default_yn = 'Y') and pss.prd_cd = '$prd_cd'
            where
                s.use_yn = 'Y'
                and s.rt_yn = 'Y'
                and if(s.sdate <= '$now_date' and date_format(date_add(date_format(s.sdate, '%Y-%m-%d'), interval 1 month), '%Y%m%d') >= '$now_date', s.open_month_stock_yn <> 'Y', 1=1)
            	$where
            
		";

        $result = DB::select($sql);

        foreach ($result as $r) {
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

    // 일반RT 등록
    public function request_rt(Request $request)
    {
        $code = 200;
        $msg = '';
        
        $state = 10;
        $rt_type = 'G';
        $admin_id = Auth('head')->user()->id;
		$user_store = Auth('head')->user()->store_cd;
        $data = $request->input("data", []);
		$sdate = date('Y-m-d').' 00:00:00';
		$edate = date('Y-m-d').' 23:59:59';

        try {
            DB::beginTransaction();
			
			/**
			 * 같은 매장이 동일상품을 여러 매장에 찔러보기식 요청하지 못하도록 제한하는 기능
			 * 같은 매장이 하루에 동일상품을 2번이상 요청하지 못하도록
			 */
			//이미 요청되어있는 상품 넣는 배열
			$dup_request = [];
			//RT요청하려는 상품 넣는 배열
			$dup_product = [];
			foreach ($data as $d) {
				$prd_cd = $d['prd_cd'];
				
				$sql = "
					select 
						count(prd_cd) as cnt
					from product_stock_rotation
					where prd_cd = :prd_cd and type = :rt_type and state = :rt_state and store_cd = :store_cd and del_yn = :del_yn and req_rt >= :sdate and req_rt <= :edate
				";
				
				$result = DB::selectOne($sql,['prd_cd' => $prd_cd, 'rt_type' => $rt_type, 'rt_state' => $state, 'store_cd' => $user_store, 'del_yn' => 'N', 'sdate' => $sdate, 'edate' => $edate]);
				
				if ($result->cnt > 2) {
					array_push($dup_request, $prd_cd);
				}
				array_push($dup_product, $prd_cd);
			}
			$dup_request = array_unique($dup_request);
			$dup_cnt = array_count_values($dup_product);
			
			//매장요청RT에서 RT등록 버튼을 클릭했을 때 바코드가 같은 상품이 2개보다 많을 때 해당 바코드를 넣는 배열
			$dup_barcode = [];
			foreach ($dup_cnt as $dup => $count) {
				if ($count > 2) {
					$dup_barcode[] = $dup;
				}
			}
			
			if (count($dup_barcode) > 0) {
				$code = 400;
				$message = "한번에 같은 상품을 2번보다 많이 요청하실 수 없습니다."."\n";
				foreach ($dup_barcode as $db) {
					$prd_cd = $db;
					$message .= "바코드 : {$prd_cd}". "\n";
				}
				$message = rtrim($message, ', ');
				throw new Exception($message);
			}
			
			if (count($dup_request) > 0) {
				$code = 400;
				$message = "이미 같은 상품이 2번 이상 RT요청되어 있습니다." ."\n". "금일은 더 이상 같은 상품을 RT요청하실 수 없습니다."."\n";
				foreach ($dup_request as $dr) {
					$message .= "바코드 : {$dr}" . "\n";
				}
				$message = rtrim($message, ', ');
				throw new Exception($message);
			}
			
			/**
			 *  동일매장 찔러보기식 요청 막는 기능 끝
			 */
			
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
                        'exp_dlv_day' => date('Ymd'),
                        'dep_store_cd' => $d['dep_store_cd'] ?? '',
                        'store_cd' => $d['store_cd'] ?? '',
                        'state' => $state,
                        'req_comment' => $d['comment'] ?? '',
                        'req_id' => $admin_id,
                        'req_rt' => now(),
                        'rt' => now(),
                    ]);

                //RT처리 알림 전송
                $res = DB::table('msg_store')
                    ->insertGetId([
                        'msg_kind' => 'RT',
                        'sender_type' => 'S',
                        'sender_cd' => $d['store_cd'] ?? '',
                        'reservation_yn' => 'N',
                        'content' => $d['store_nm'].'에서 매장요청RT를 요청 하였습니다.',
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

            $msg = "매장요청RT 등록이 정상적으로 완료되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            if ($code === 200) $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([ "code" => $code, "msg" => $msg ]);
    }
	public function change_store_channel(Request $request) {

		$store_channel = $request->input('store_channel');

		try {
			DB::beginTransaction();
			$sql = "
					select 
						store_kind_cd
						, store_kind
					from store_channel
					where store_channel_cd = '$store_channel' and dep = 2 and use_yn = 'Y' 
					order by seq asc
                ";
			$store_kind = DB::select($sql);

			DB::commit();
			$code = 200;
			$msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, 'store_kind' => $store_kind]);
	}

	public function change_store_channel_kind(Request $request) {

		$store_channel_kind = $request->input('store_channel_kind');

		try {
			DB::beginTransaction();
			$sql = "
					select
						store_cd
						, store_nm
					from store
					where store_channel_kind = '$store_channel_kind' and use_yn = 'Y'
                ";
			$stores = DB::select($sql);

			DB::commit();
			$code = 200;
			$msg = "";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, 'stores' => $stores]);
	}
}
