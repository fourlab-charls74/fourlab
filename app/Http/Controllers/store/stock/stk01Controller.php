<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk01Controller extends Controller
{
	public function index() 
	{

		$values = [
			'code_kinds'	=> [],
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'store_types'	=> SLib::getStoreTypes(),
			// 'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'			=> SLib::getItems(),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE')
		];

		return view( Config::get('shop.store.view') . '/stock/stk01',$values);
	}

	public function search(Request $request)
	{
		$store_type = $request->input('store_type');
		$prd_cd = $request->input('prd_cd', '');

        $goods_stat = $request->input("goods_stat");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $goods_nm_eng = $request->input("goods_nm_eng");

		$com_id		= $request->input("com_cd");
		$ext_store_qty = $request->input("ext_store_qty", "false");

        $page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$store_cds = $request->input('store_no') ?? [];
		$store_where = "";
		foreach ($store_cds as $key => $cd) {
			if ($key === 0) {
				$store_where .= "p.store_cd = '$cd'";
			} else {
				$store_where .= " or p.store_cd = '$cd'";
			}
		}
		if (count($store_cds) < 1) {
			$store_where = "1=1";
		}

		$where	= "";
		if ($store_type != "")	$where .= " and s.store_type = '" . $store_type . "' ";
        if ($ext_store_qty == 'true')
            $where .= " and (p.wqty != '' and p.wqty != '0')";
		
		if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd = '" . $cd . "' ";
			}
			$where .= ")";
		}
        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

		if ($com_id != "") $where .= " and g.com_id = '" . Lib::quote($com_id) . "'";

        if (is_array($goods_stat)) {
            if (count($goods_stat) == 1 && $goods_stat[0] != "") {
                $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
            } else if (count($goods_stat) > 1) {
                $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
            }
        } else if($goods_stat != "") {
            $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        }

        if ($goods_nos != "") {
            $goods_no = $goods_nos;
        }
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if ( $goods_no != "" ) {
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        $page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ( $page == 1 ) {
			$query = /** @lang text */
                "
				select count(*) as total
				from product_stock_store p
					left outer join goods g on p.goods_no = g.goods_no
					left outer join store s on p.store_cd = s.store_cd
				where 1=1 $where
			";
			$row = DB::selectOne($query);
			$total = $row->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$sql = /** @lang text */
            "
			select 
				p.goods_no, goods_type, prd_cd, g.opt_kind_cd, opt_kind_nm, b.brand_nm, style_no, 
				c.code_val as sale_stat_cl, ifnull( c2.code_val, 'N/A') as goods_type_nm,
				if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				)) as img, goods_nm, goods_nm_eng, goods_opt, p.store_cd, store_nm, store_type, qty, wqty, p.rt, p.ut,
				g.goods_sh, g.price
			from product_stock_store p 
				left outer join goods g on p.goods_no = g.goods_no
				left outer join store s on p.store_cd = s.store_cd
				left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
				left outer join `code` c on c.code_kind_cd = 'G_GOODS_STAT' and sale_stat_cl = c.code_id
				left outer join `code` c2 on c2.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = c2.code_id 
				left outer join brand b on b.brand = g.brand
			where 1=1 $where and ($store_where)
			$orderby 
			$limit
		";

		$rows = DB::select($sql);
		$rows = collect($rows)->map(function ($row) { // shop image_svr 적용
			if ($row->img != "") {
				$row->img = sprintf("%s%s",config("shop.image_svr"), $row->img);
			}
			return $row;
		})->all();

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($rows)
			),
			"body" => $rows
		]);

	}

	/** 재고팝업 */
	public function show($prd_cd)
	{
		$cfg_img_size_real = "a_500";
		$cfg_img_size_list = "a_500";

		$sql = "
			select
				p.prd_cd
				, p.goods_no
				, p.goods_opt
				, p.color
				, p.size
				, g.goods_nm
				, g.goods_nm_eng
				, g.price
				, g.wonga
				, g.goods_sh
				, g.style_no
				, g.com_id
				, g.com_nm
				, g.opt_kind_cd
				, o.opt_kind_nm
				, g.brand
				, b.brand_nm
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				)) as img
			from product_code p
				left outer join goods g on g.goods_no = p.goods_no
				left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
				left outer join brand b on b.brand = g.brand
			where p.prd_cd = :prd_cd
		";
		$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		$storages = DB::table("storage")
			->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')
			->where('use_yn', '=', 'Y')
			->orderByDesc('default_yn')
			->get();

		$values = [
			'sdate' => date('Y-m-d'),
			'edate' => date('Y-m-d'),
			'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
			'storages' => $storages, // 창고리스트
			'prd' => $row,
		];
		return view(Config::get('shop.store.view') . '/stock/stk01_show', $values);
	}

	/** 재고팝업 현황조회 */
	public function search_command(Request $request, $cmd)
	{
		switch ($cmd) {
			case 'storage':
				$response = $this->search_stock_storage($request);
				break;
			case 'store':
				$response = $this->search_stock_store($request);
				break;
			case 'store-detail':
				$response = $this->search_stock_store_detail($request);
				break;
            default:
                $message = 'Command not found';
                $response = response()->json(['code' => 0, 'msg' => $message], 404);
		};
		return $response;
	}

	public function search_stock_storage(Request $request)
	{
		$prd_cd = $request->input('prd_cd', '');
		$sdate = $request->input('sdate', date('Y-m-d'));
		$edate = $request->input('edate', date('Y-m-d'));

		$sql = "
			select p.prd_cd, s.storage_cd, s.storage_nm, ifnull(p.qty, 0) as qty, ifnull(p.wqty, 0) as wqty
			from storage s
				left outer join product_stock_storage p on p.storage_cd = s.storage_cd and p.prd_cd = :prd_cd
			where s.use_yn = 'Y'
		";
		$rows = DB::select($sql, ['prd_cd' => $prd_cd]);

		$sql = "
			select ifnull(sum(qty), 0) as qty, ifnull(sum(wqty), 0) as wqty
			from product_stock_storage
			where prd_cd = :prd_cd
		";
		$total = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		return response()->json([
			'code' => 200,
			'total' => $total,
			'data' => $rows,
		]);
	}

	public function search_stock_store(Request $request)
	{
		$prd_cd = $request->input('prd_cd', '');
		$sdate = $request->input('sdate', date('Y-m-d'));
		$edate = $request->input('edate', date('Y-m-d'));
		$store_type = $request->input('store_type', '');
		$store_cds = $request->input('store_no', []);

		$rows = [];
		if ($store_type != '' || $store_cds != '') {
			$where = "";
			if ($store_type != '') $where .= " and s.store_type = '$store_type' ";

			$store_where = "";
			foreach($store_cds as $key => $cd) {
				if ($key === 0) {
					$store_where .= "s.store_cd = '$cd'";
				} else {
					$store_where .= " or s.store_cd = '$cd'";
				}
			}
			if (count($store_cds) < 1) {
				$store_where = "1=1";
			}
			
			$sql = "
				select p.prd_cd, s.store_cd, s.store_nm, ifnull(p.qty, 0) as qty, ifnull(p.wqty, 0) as wqty
				from store s
					left outer join product_stock_store p on p.store_cd = s.store_cd and p.prd_cd = :prd_cd
				where ($store_where) $where
				order by p.wqty desc
			";
			$rows = DB::select($sql, ['prd_cd' => $prd_cd]);
		}

		$sql = "
			select ifnull(sum(qty), 0) as qty, ifnull(sum(wqty), 0) as wqty
			from product_stock_store
			where prd_cd = :prd_cd
		";
		$total = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		return response()->json([
			'code' => 200,
			'total' => $total,
			'data' => $rows,
		]);
	}

	public function search_stock_store_detail(Request $request)
	{
		$prd_cd = $request->input('prd_cd', '');
		$sdate = $request->input('sdate', date('Y-m-d'));
		$edate = $request->input('edate', date('Y-m-d'));
		$next_edate = date("Y-m-d", strtotime("+1 day", strtotime($edate)));
		$store_type = $request->input('store_type', '');
		$store_cds = $request->input('store_no', []);
		
		$rows = [];
		if ($prd_cd != '') {
			$where = " and p.prd_cd = '$prd_cd' ";
			if ($store_type) $where .= " and store.store_type = '$store_type' ";

			// store_where
			$store_where = "";
			foreach($store_cds as $key => $cd) {
				if ($key === 0) {
					$store_where .= "p.store_cd = '$cd'";
				} else {
					$store_where .= " or p.store_cd = '$cd'";
				}
			}
			if (count($store_cds) < 1) {
				$store_where = "1=1";
			}

			$sql = "
				select 
					p.store_cd, 
					store.store_nm,
					p.prd_cd, 
					(p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty,
					sum(ifnull(store_in.qty, 0)) as store_in_qty,
					sum(ifnull(store_return.qty, 0)) * -1 as store_return_qty,
					sum(ifnull(rt_in.qty, 0)) as rt_in_qty,
					sum(ifnull(rt_out.qty, 0)) * -1 as rt_out_qty,
					sum(ifnull(sale.qty, 0)) * -1 as sale_qty,
					sum(ifnull(loss.qty, 0)) * -1 as loss_qty,
					p.wqty - sum(ifnull(_next.qty, 0)) as term_qty,
					p.qty as qty,
					p.wqty as wqty
				from product_stock_store p
					inner join product_code pc on pc.prd_cd = p.prd_cd
					inner join store on store.store_cd = p.store_cd
					left outer join (
						select idx, prd_cd, location_cd, type, qty, stock_state_date
						from product_stock_hst
						where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
					) hst on hst.location_cd = p.store_cd and hst.prd_cd = p.prd_cd
					left outer join product_stock_hst store_in on store_in.idx = hst.idx and store_in.type = '1'
					left outer join product_stock_hst store_return on store_return.idx = hst.idx and store_return.type = '11'
					left outer join product_stock_hst rt_in on rt_in.idx = hst.idx and rt_in.type = '15' and rt_in.qty > 0
					left outer join product_stock_hst rt_out on rt_out.idx = hst.idx and rt_out.type = '15' and rt_out.qty < 0
					left outer join product_stock_hst sale on sale.idx = hst.idx and (sale.type = '2' or sale.type = '5' or sale.type = '6') -- 주문&교환&환불
					left outer join product_stock_hst loss on loss.idx = hst.idx and loss.type = '14'
					left outer join (
						select idx, prd_cd, location_cd, type, qty, stock_state_date
						from product_stock_hst
						where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
					) _next on _next.location_cd = p.store_cd and _next.prd_cd = p.prd_cd
				where ($store_where) $where
				group by p.store_cd, p.prd_cd
				order by p.store_cd
			";
			$rows = DB::select($sql);

			$sql = "
				select
					sum(prev_qty) as prev_qty,
					sum(store_in_qty) as store_in_qty,
					sum(store_return_qty) as store_return_qty,
					sum(rt_in_qty) as rt_in_qty,
					sum(rt_out_qty) as rt_out_qty,
					sum(sale_qty) as sale_qty,
					sum(loss_qty) as loss_qty,
					sum(term_qty) as term_qty
				from (
					select 
						p.store_cd, 
						store.store_nm,
						p.prd_cd, 
						(p.wqty - sum(ifnull(_next.qty, 0)) - sum(ifnull(hst.qty, 0))) as prev_qty,
						sum(ifnull(store_in.qty, 0)) as store_in_qty,
						sum(ifnull(store_return.qty, 0)) * -1 as store_return_qty,
						sum(ifnull(rt_in.qty, 0)) as rt_in_qty,
						sum(ifnull(rt_out.qty, 0)) * -1 as rt_out_qty,
						sum(ifnull(sale.qty, 0)) * -1 as sale_qty,
						sum(ifnull(loss.qty, 0)) * -1 as loss_qty,
						p.wqty - sum(ifnull(_next.qty, 0)) as term_qty,
						p.wqty as current_qty
					from product_stock_store p
						inner join product_code pc on pc.prd_cd = p.prd_cd
						inner join store on store.store_cd = p.store_cd
						left outer join (
							select idx, prd_cd, location_cd, type, qty, stock_state_date
							from product_stock_hst
							where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$sdate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= '$edate 23:59:59'
						) hst on hst.location_cd = p.store_cd and hst.prd_cd = p.prd_cd
						left outer join product_stock_hst store_in on store_in.idx = hst.idx and store_in.type = '1'
						left outer join product_stock_hst store_return on store_return.idx = hst.idx and store_return.type = '11'
						left outer join product_stock_hst rt_in on rt_in.idx = hst.idx and rt_in.type = '15' and rt_in.qty > 0
						left outer join product_stock_hst rt_out on rt_out.idx = hst.idx and rt_out.type = '15' and rt_out.qty < 0
						left outer join product_stock_hst sale on sale.idx = hst.idx and (sale.type = '2' or sale.type = '5' or sale.type = '6') -- 주문&교환&환불
						left outer join product_stock_hst loss on loss.idx = hst.idx and loss.type = '14'
						left outer join (
							select idx, prd_cd, location_cd, type, qty, stock_state_date
							from product_stock_hst
							where location_type = 'STORE' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') >= '$next_edate 00:00:00' and STR_TO_DATE(stock_state_date, '%Y%m%d%H%i%s') <= now()
						) _next on _next.location_cd = p.store_cd and _next.prd_cd = p.prd_cd
					where ($store_where) $where
					group by p.store_cd, p.prd_cd
					order by p.store_cd
				) c
			";
			$total_data = DB::selectOne($sql);
		}

		return response()->json([
			'code' => 200,
			'head' => [
				'total' => count($rows),
				'page' => 1,
				'page_cnt' => 1,
				'page_total' => 1,
                'total_data' => $total_data
			],
			'body' => $rows,
		]);
	}
}
