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

		return view( Config::get('shop.shop.view') . '/stock/stk01',$values);

		/* shop 미사용 메뉴 메인페이지로 리다이렉트 */
        // return redirect('/shop');
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
		$prd_cd_range_text = $request->input("prd_cd_range", '');

		$com_id		= $request->input("com_cd");
		$ext_store_qty = $request->input("ext_store_qty", "false");

        $page = $request->input('page', 1);
		if ( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 100);

		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field','p.goods_no');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		$store_cds = $request->input('store_no') ?? [];
		if (count($store_cds) > 0) {
			$where .= " and p.store_cd in (" . join(',', array_map(function($s) { return "'$s'"; }, $store_cds)) . ")";
		}

		if ($store_type != "")	$where .= " and s.store_type = '" . $store_type . "' ";
        if ($ext_store_qty == 'true')
            $where .= " and (p.wqty != '' and p.wqty != '0')";

		if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd like '" . $cd . "%' ";
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
					left outer join product_code pc on pc.prd_cd = p.prd_cd
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
				p.goods_no, goods_type, p.prd_cd, g.opt_kind_cd, opt_kind_nm, b.brand_nm, style_no,
				c.code_val as sale_stat_cl, ifnull( c2.code_val, 'N/A') as goods_type_nm,
				if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				)) as img, goods_nm, goods_nm_eng, p.goods_opt, p.store_cd, store_nm, store_type, qty, wqty, p.rt, p.ut,
				g.goods_sh, g.price
			from product_stock_store p
				left outer join product_code pc on pc.prd_cd = p.prd_cd
				left outer join goods g on p.goods_no = g.goods_no
				left outer join store s on p.store_cd = s.store_cd
				left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
				left outer join `code` c on c.code_kind_cd = 'G_GOODS_STAT' and sale_stat_cl = c.code_id
				left outer join `code` c2 on c2.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = c2.code_id
				left outer join brand b on b.brand = g.brand
			where 1=1 $where
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
	public function show($prd_cd, Request $request)
	{
		$user_store	= Auth('head')->user()->store_cd;

		$ostore_stock_yn = SLib::getStoreProp($user_store)->ostore_stock_yn;

		$sdate = $request->input("date", '');
		if($sdate == '') $sdate = date("Y-m-d");

		$cfg_img_size_real = "a_500";
		$cfg_img_size_list = "a_500";

        // language=TEXT
		$sql = "
			select
				p.prd_cd
				, p.goods_no
				, p.goods_opt
				, p.color as color_cd
				, c.code_val as color
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
				left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = p.color
			where p.prd_cd = :prd_cd
		";
		$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		if ($row->goods_no == '0') {
            // language=TEXT
			$sql = "
				select
					p.prd_cd, p.prd_nm as goods_nm, p.style_no, p.tag_price as goods_sh
					, p.price, p.wonga, p.type, p.com_id, c.com_nm, p.match_yn, p.use_yn
					, pc.brand, b.brand_nm
				from product p
					inner join product_code pc on pc.prd_cd = p.prd_cd
					left outer join company c on c.com_id = p.com_id
					left outer join brand b on b.br_cd = pc.brand
				where p.prd_cd = :prd_cd
			";
			$prd = DB::selectOne($sql, ['prd_cd' => $prd_cd]);
			$row->goods_nm = $prd->goods_nm;
			$row->style_no = $prd->style_no;
			$row->goods_sh = $prd->goods_sh;
			$row->wonga = $prd->wonga;
			$row->price = $prd->price;
			$row->com_id = $prd->com_id;
			$row->com_nm = $prd->com_nm;
			$row->brand = $prd->brand;
			$row->brand_nm = $prd->brand_nm;
		}

		$storages = DB::table("storage")
			->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')
			->where('use_yn', '=', 'Y')
			->where('default_yn', '=', 'Y')
			->get();

		$values = [
			'sdate' => $sdate,
			'edate' => $sdate,
			'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
			'storages' => $storages, // 창고리스트
			'prd' => $row,
			'user_store' => $user_store,
			'ostore_stock_yn' => $ostore_stock_yn
		];
		return view(Config::get('shop.shop.view') . '/stock/stk01_show', $values);
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

        // language=TEXT
		$sql = "
			select p.prd_cd, s.storage_cd, s.storage_nm, ifnull(p.qty, 0) as qty, ifnull(p.wqty, 0) as wqty
			from storage s
				left outer join product_stock_storage p on p.storage_cd = s.storage_cd and p.prd_cd = :prd_cd
			where s.use_yn = 'Y' and s.default_yn = 'Y'
		";
		$rows = DB::select($sql, ['prd_cd' => $prd_cd]);

        // language=TEXT
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
		$now_date = date('Ymd');

		$user_store_cd	= Auth('head')->user()->store_cd;
		$store = DB::table('store')->where('store_cd', $user_store_cd)->first();

		$rows = [];
		if (isset($store)) {
			$where = "";

			/**
			 * 매장의 "타매장재고조회"항목이 'Y'일 경우 => 같은 매장구분값을 가지는 매장들의 재고 조회
			 * 매장의 "타매장재고조회"항목이 'N'일 경우 => 해당매장재고만 조회
			 */
			if ($store->ostore_stock_yn === 'Y') $where .= " and s.store_type = '$store->store_type'";
			else $where .= " and s.store_cd = '$user_store_cd'";

            /**
             * "오픈후한달재고보기제외여부"항목이 'Y'인 모든 매장의 오픈달이 현재 해당될 때, 매장재고을 보여주지 않음
             */
            $where .= " and if(s.sdate <= '$now_date' and date_format(date_add(date_format(s.sdate, '%Y-%m-%d'), interval 1 month), '%Y%m%d') >= '$now_date', s.open_month_stock_yn <> 'Y', 1=1)";

            // language=TEXT
			$sql = "
				select p.prd_cd, s.store_cd, s.store_nm, ifnull(p.qty, 0) as qty, ifnull(p.wqty, 0) as wqty
				from store s
					left outer join product_stock_store p on p.store_cd = s.store_cd and p.prd_cd = :prd_cd
				where 1=1 $where
				order by p.wqty desc, s.store_cd
			";
			$rows = DB::select($sql, ['prd_cd' => $prd_cd]);
		}

		// $sql = "
		// 	select ifnull(sum(qty), 0) as qty, ifnull(sum(wqty), 0) as wqty
		// 	from product_stock_store
		// 	where prd_cd = :prd_cd
		// ";
		// $total = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		return response()->json([
			'code' => 200,
			'total' => [],
			'data' => $rows,
		]);
	}

	public function search_stock_store_detail(Request $request)
	{
		$prd_cd = $request->input('prd_cd', '');
		$sdate = $request->input('sdate', date('Y-m-d'));
		$edate = $request->input('edate', date('Y-m-d'));
		$next_edate = date("Ymd", strtotime("+1 day", strtotime($edate)));
		$now_date = date('Ymd');
        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);

		$user_store_cd	= Auth('head')->user()->store_cd;
		$rows = [];
		$total_data = [];

		if ($prd_cd == '') return response()->json([ 'code' => 500, 'head' => [ 'total' => 0 ], 'body' => $rows ]);

		$where = " and ps.prd_cd = '$prd_cd' ";
		if (isset($user_store_cd)) $where .= " and ps.store_cd = '" . Lib::quote($user_store_cd) . "'";

        // language=TEXT
		$sql = "
			select ps.store_cd, s.store_nm, ps.prd_cd, ps.qty, ps.wqty
				, sum(if(hst.type = 1, ifnull(hst.qty, 0), 0)) as store_in_qty -- 매장입고
				, sum(if(hst.type = 11, ifnull(hst.qty, 0), 0)) as store_return_qty -- 매장반품
				, sum(if(hst.type = 15 and hst.qty > 0, ifnull(hst.qty, 0), 0)) as rt_in_qty -- 이동입고
				, sum(if(hst.type = 15 and hst.qty < 0, ifnull(hst.qty, 0), 0)) as rt_out_qty -- 이동출고
				, sum(if(hst.type = 14, ifnull(hst.qty, 0), 0)) as loss_qty -- LOSS
				, ifnull(w.qty, 0) as sale_qty -- 판매재고
				, (ps.wqty
					- sum(if(_next.type in (1, 11, 14, 15), ifnull(_next.qty, 0), 0))
					- ifnull(w_next.qty, 0)
				) as term_qty -- 기간재고
				, (ps.wqty
					- sum(if(_next.type in (1, 11, 14, 15), ifnull(_next.qty, 0), 0))
					- ifnull(w_next.qty, 0)
					- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
					- ifnull(w.qty, 0)
				) as prev_qty -- 이전재고
			from product_stock_store ps
				inner join store s on s.store_cd = ps.store_cd
				left outer join (
					select prd_cd, location_cd, type, qty
					from product_stock_hst
					where stock_state_date >= '$sdate' and stock_state_date <= '$edate' and location_type = 'STORE'
				) hst on hst.prd_cd = ps.prd_cd and hst.location_cd = ps.store_cd
				left outer join (
					select prd_cd, location_cd, type, qty
					from product_stock_hst
					where stock_state_date >= '$next_edate' and stock_state_date <= '$now_date' and location_type = 'STORE'
				) _next on _next.prd_cd = ps.prd_cd and _next.location_cd = ps.store_cd
				left outer join (
					select prd_cd, store_cd, sum(qty * if(ord_state = 30, -1, 1)) as qty
					from order_opt_wonga
					where ord_state_date >= '$sdate' and ord_state_date <= '$edate' and ord_state in (30,60,61)
					group by prd_cd, store_cd
				) w on w.prd_cd = ps.prd_cd and w.store_cd = ps.store_cd
				left outer join (
					select prd_cd, store_cd, sum(qty * if(ord_state = 30, -1, 1)) as qty
					from order_opt_wonga
					where ord_state_date >= '$next_edate' and ord_state_date <= '$now_date' and ord_state in (30,60,61)
					group by prd_cd, store_cd
				) w_next on w_next.prd_cd = ps.prd_cd and w_next.store_cd = ps.store_cd
			where 1=1 $where
			group by ps.store_cd, ps.prd_cd
			order by ps.store_cd
		";
		$rows = DB::select($sql);

        // language=TEXT
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
			from ( $sql ) a
		";
		$total_data = DB::selectOne($sql);

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
