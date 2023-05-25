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

class stk22Controller extends Controller
{
    public function index()
	{
        $sql = "
			select
				store_channel
				, store_channel_cd
				, use_yn
			from store_channel
			where dep = 1 and use_yn = 'Y'
		";

		$store_channel = DB::select($sql);

		$sql = "
			select
				store_kind
				, store_kind_cd
				, use_yn
			from store_channel
			where dep = 2 and use_yn = 'Y'
		";

		$store_kind = DB::select($sql);

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
            'store_channel'	=> $store_channel,
			'store_kind'	=> $store_kind
		];

        return view(Config::get('shop.store.view') . '/stock/stk22', $values);
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
                $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
                $opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
                $where .= " and pc.$opt $in_query ($opt_join) ";
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
                , pc.size
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
        $store_channel = $request->input('store_channel', '');
        $store_channel_kind = $request->input('store_channel_kind', '');
        $now_date = date('Ymd');
        $where = "";

        if($store_channel != '') $where .= " and s.store_channel = $store_channel";
        if($store_channel_kind != '') $where .= " and s.store_channel_kind = $store_channel_kind";

		$sql = "
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
		";

		$result = DB::select($sql);

        foreach ($result as $r) {
            $r->prd_cd = $prd_cd;
        }

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => count($result),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
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
        // $admin_nm = Auth('head')->user()->name;
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
			$document_number = DB::selectOne($sql)->document_number;

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
                        // 'exp_dlv_day' => date('Ymd'),
                        // 'rec_id' => $admin_id,
                        // 'rec_rt' => now(),
                        // 'prc_id' => $admin_id,
                        // 'prc_rt' => now(),
                        // 'ut' => now(),
                    ]);

                // // 보내는 매장
                // // product_stock_store -> 보유재고 차감
                // DB::table('product_stock_store')
                //     ->where('prd_cd', '=', $d['prd_cd'])
                //     ->where('store_cd', '=', $d['dep_store_cd'])
                //     ->update([
                //         'qty' => DB::raw('qty - ' . ($d['rt_qty'] ?? 0)),
                //         'wqty' => DB::raw('wqty - ' . ($d['rt_qty'] ?? 0)),
                //         'ut' => now(),
                //     ]);
                // // 재고이력 등록
                // DB::table('product_stock_hst')
                //     ->insert([
                //         'goods_no' => $d['goods_no'],
                //         'prd_cd' => $d['prd_cd'],
                //         'goods_opt' => $d['goods_opt'],
                //         'location_cd' => $d['dep_store_cd'],
                //         'location_type' => 'STORE',
                //         'type' => PRODUCT_STOCK_TYPE_STORE_RT, // 재고분류 : RT출고
                //         'price' => $d['price'],
                //         'wonga' => $d['wonga'],
                //         'qty' => ($d['rt_qty'] ?? 0) * -1,
                //         'stock_state_date' => date('Ymd'),
                //         'ord_opt_no' => '',
                //         'comment' => 'RT출고',
                //         'rt' => now(),
                //         'admin_id' => $admin_id,
                //         'admin_nm' => $admin_nm,
                //     ]);
                //
                // // 받는 매장
                // // product_stock_store -> 재고 존재여부 확인 후 보유재고 플러스
                // $store_stock_cnt =
                //     DB::table('product_stock_store')
                //         ->where('prd_cd', '=', $d['prd_cd'])
                //         ->where('store_cd', '=', $d['store_cd'])
                //         ->count();
                // if($store_stock_cnt < 1) {
                //     // 해당 매장에 상품 기존재고가 없을 경우
                //     DB::table('product_stock_store')
                //         ->insert([
                //             'goods_no' => $d['goods_no'] ?? 0,
                //             'prd_cd' => $d['prd_cd'],
                //             'store_cd' => $d['store_cd'],
                //             'qty' => 0,
                //             'wqty' => $d['rt_qty'] ?? 0,
                //             'goods_opt' => $d['goods_opt'],
                //             'use_yn' => 'Y',
                //             'rt' => now(),
                //         ]);
                // } else {
                //     // 해당 매장에 상품 기존재고가 이미 존재할 경우
                //     DB::table('product_stock_store')
                //         ->where('prd_cd', '=', $d['prd_cd'])
                //         ->where('store_cd', '=', $d['store_cd'])
                //         ->update([
                //             'wqty' => DB::raw('wqty + ' . ($d['rt_qty'] ?? 0)),
                //             'ut' => now(),
                //         ]);
                // }
                // // 재고이력 등록
                // DB::table('product_stock_hst')
                //     ->insert([
                //         'goods_no' => $d['goods_no'],
                //         'prd_cd' => $d['prd_cd'],
                //         'goods_opt' => $d['goods_opt'],
                //         'location_cd' => $d['store_cd'],
                //         'location_type' => 'STORE',
                //         'type' => PRODUCT_STOCK_TYPE_STORE_RT, // 재고분류 : RT입고
                //         'price' => $d['price'],
                //         'wonga' => $d['wonga'],
                //         'qty' => $d['rt_qty'] ?? 0,
                //         'stock_state_date' => date('Ymd'),
                //         'ord_opt_no' => '',
                //         'comment' => 'RT입고',
                //         'rt' => now(),
                //         'admin_id' => $admin_id,
                //         'admin_nm' => $admin_nm,
                //     ]);

                //RT처리 알림 전송
                $res = DB::table('msg_store')
                    ->insertGetId([
                        'msg_kind' => 'RT',
                        'sender_type' => 'S',
                        'sender_cd' => $d['dep_store_cd'] ?? '',
                        'reservation_yn' => 'N',
                        'content' => $d['dep_store_nm'].'에서 일반RT를 처리하였습니다.',
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

            $msg = "일반RT등록이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
            if ($code === 200) $code = 500;
			$msg = $e->getMessage();
		}

        return response()->json([ "code" => $code, "msg" => $msg ]);
    }
}
