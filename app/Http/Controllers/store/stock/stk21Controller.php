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
                left outer join goods g on g.goods_no = pc.goods_no
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

    // 매장/창고별 상품재고 검색
    public function search_stock(Request $request)
    {
		$code = 200;
		$prd_cd = $request->input("prd_cd", '');
        $store_type = $request->input("store_type", '');
        $where = "";

        if($store_type != '') $where .= " and s.store_type = $store_type";

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
            where s.use_yn = 'Y' $where
		";

		$result = DB::select($sql);

        foreach($result as $r) 
        {
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

    // RT요청
    public function request_rt(Request $request)
    {
        $state = 10;
        $rt_type = 'R';
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

			foreach($data as $d) {
                DB::table('product_stock_rotation')
                    ->insert([
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
            }

			DB::commit();
            $code = 200;
            $msg = "RT요청이 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
