<?php

namespace App\Http\Controllers\partner\product;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Category;
use App\Models\Product;
use App\Models\Jaego;
use App\Models\Option;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use PDO;
class prd01Controller extends Controller
{
	private $sale_types = [
		'event' => 'Event',
		'onesize' => 'Onesize',
		'clearance' => 'Clearance',
		'refurbished' => 'Refurbished',
		'newmember' => 'Newmember',
	];

	public function index(Request $request)
	{
		$conf = new Conf();

		$domain		= $conf->getConfigValue("shop", "domain");
		$style_no	= $request->input('style_no');

		$values = [
			'domain'		=> $domain,
			'style_no'		=> $style_no,
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
		];
		return view(Config::get('shop.partner.view') . '/product/prd01', $values);
	}

    public function index_choice()
    {
        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
        ];
        return view(Config::get('shop.partner.view') . '/product/prd01_choice', $values);
    }

	public function edit_index(Request $request)
	{
		/**
		 * post 형식으로 window.open 하여 전달받은 goods_nos 있으면 뷰에 전달
		 */
		$goods_nos = $request->input('goods_nos', '');

		/**
		 * 설정 값 얻기
		 */
		$conf = new Conf();
		$cfg_dlv_fee = $conf->getConfigValue("delivery", "base_delivery_fee");
		$cfg_free_dlv_fee_limit	= $conf->getConfigValue("delivery", "free_delivery_amt");
		$cfg_point_ratio = $conf->getConfigValue("point", "ratio", "0");

		$sql = "select id id, name val from mgr_user where md_yn = 'Y' and use_yn = 'Y' order by name";
		$md_names = DB::select($sql);

		$values = [
			'goods_nos' => $goods_nos,
			'items' => SLib::getItems(),
			'goods_stats' => SLib::getCodes("G_GOODS_STAT"),
			'md_names' => $md_names,
			'baesong_infos' => SLib::getCodes("G_BAESONG_INFO"),
			'baesong_kinds' => SLib::getCodes("G_BAESONG_KIND"),
			'dlv_pay_types' => SLib::getCodes("G_DLV_PAY_TYPE"),
			'dlv_fee_yn' => ['' => "==유료/무료==", 'Y' => "유료", 'N' => "무료"],
			'point_yn' => ['' => "==지급여부==", 'Y' => "지급함", 'N' => "지급안함"],
			'point_unit' => [''=> "단위", 'W' => "원", 'P' => "%"],
			'dlv_due_types' => SLib::getCodes("G_DLV_DUE_TYPE"),
			'dlv_fee' => Lib::cm($cfg_dlv_fee),
			'free_dlv_fee_limit' => Lib::cm($cfg_free_dlv_fee_limit),
			'order_point_ratio'	=> $cfg_point_ratio,
			'tax_yn' => ['' => "==과세 구분==", 'Y' => "과세", 'N' => "면세"]
		];
		return view(Config::get('shop.partner.view') . '/product/prd01_edit', $values);
	}

    public function search(Request $request)
    {
        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $goods_stat = $request->input("goods_stat");
		$except_trash = $request->input("except_trash");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
		$goods_nm_eng = $request->input("goods_nm_eng");
        $cat_type = $request->input("cat_type");
        $cat_cd = $request->input("cat_cd");
        $is_unlimited = $request->input("is_unlimited");

		$org_nm = $request->input("org_nm", "");
		$make = $request->input("make", "");
		$sdate = $request->input("sdate", "");
		$edate = $request->input("edate", "");
		$is_option_use = $request->input("is_option_use", "");
		$goods_location = $request->input("goods_location", "");

		$com_id = Auth('partner')->user()->com_id;

        $head_desc = $request->input("head_desc");
		$ad_desc = $request->input("ad_desc");

        $limit = $request->input("limit",100);
        $ord = $request->input('ord','desc');
        $ord_field = $request->input('ord_field','g.goods_no');
		$type = $request->input("type");
		$goods_type = $request->input("goods_type");

		$sale_yn	= $request->input("sale_yn");
		$coupon_yn	= $request->input("coupon_yn");
		$sale_type	= $request->input("sale_type");

        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $where = "";
        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";
        if ($is_unlimited != "") $where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";

		// if ($com_id != "") $where .= " and g.com_id = '" . Lib::quote($com_id) . "'";

        if($cat_cd != ""){
            if($cat_type === "DISPLAY"){
                $where .= " and g.rep_cat_cd = '". Lib::quote($cat_cd) . "' ";
            } else if($cat_type === "ITEM"){
                $where .= " and ( select count(*) from category_goods where cat_type = 'ITEM' and d_cat_cd = '". Lib::quote($cat_cd) . "' and goods_no = g.goods_no ) > 0 ";
            }
        }

        if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
		if ($ad_desc != "") $where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

        if ($is_unlimited != "") $where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";

		if( is_array($goods_stat)) {
			if (count($goods_stat) == 1 && $goods_stat[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
            } else if (count($goods_stat) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
            }
        } else if($goods_stat != ""){
			$where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        }

        if($goods_nos        != ""){
            $goods_no = $goods_nos;
        }
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
             }
        }

		if ($type != "") $where .= " and g.type = '" . Lib::quote($type) . "' ";
		if ($goods_type != "") $where .= " and g.goods_type = '" . Lib::quote($goods_type) . "' ";

		if( $sale_yn != "" )	$where .= " and g.sale_yn = '$sale_yn' ";
		if( $coupon_yn != "" )	$where .= " and gc.price > 0 ";
		if( $sale_type != "" )	$where .= " and g.sale_type = '" . Lib::quote($sale_type) . "' ";
		if( $except_trash == "Y") $where .= "and g.sale_stat_cl > -90";
		
		if ($org_nm != '') $where .= " and g.org_nm = '" . Lib::quote($org_nm) . "' ";
		if ($make != '') $where .= " and g.make = '" . Lib::quote($make) . "' ";
		if ($sdate != '') $where .= " and g.reg_dm >= '" . Lib::quote($sdate) . "' ";
		if ($edate != '') $where .= " and g.reg_dm <= '" . Lib::quote($edate) . " 23:59:59' ";
		if ($is_option_use != '') $where .= " and g.is_option_use = '" . Lib::quote($is_option_use) . "' ";
		if ($goods_location != '') $where .= " and g.goods_location = '" . Lib::quote($goods_location) . "' ";

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = /** @lang text */
                "
                select count(*) as total
                from goods g
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
                where 1=1
                    and g.com_id = :com_id
                    $where
			";
            $row = DB::select($query,['com_id' => $com_id]);
            // $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = /** @lang text */
            "
			select
				'' as blank
				, g.goods_no , g.goods_sub
				, ifnull( type.code_val, 'N/A') as goods_type
				, com.com_id, com.com_nm
				, opt.opt_kind_nm
				, brand.brand_nm
				, cat.full_nm
				, g.style_no
				, g.head_desc
				, '' as img_view
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				  )) as img
				, g.goods_nm
				, g.goods_nm_eng
				, g.ad_desc
				, stat.code_val as sale_stat_cl
				, g.before_sale_price
				, g.price
				, gc.coupon_price
				, '' as sale_rate
				, g.sale_s_dt
				, g.sale_e_dt
				, ifnull(
					(select sum(good_qty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				  ) as qty
				 , ifnull(
					(select sum(wqty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				  ) as wqty
				, g.wonga
				, g.goods_sh
				, (100/(g.price/(g.price-g.wonga))) as margin_rate
				, (g.price-g.wonga) as margin_amt
				, g.md_nm
				, bi.code_val as baesong_info
				, bk.code_val as baesong_kind
				, dpt.code_val as dlv_pay_type
				, g.baesong_price
				, g.point
				, g.org_nm
				, g.goods_memo
				, g.make
				, g.type
				, g.reg_dm
				, g.upd_dm
				, g.goods_location
				, g.sale_price
				, g.goods_type as goods_type_cd
				, com.com_type as com_type_d
				,g.sale_type,g.sale_yn,g.before_sale_price,g.sale_price,0 as sale_rate,
				g.sale_dt_yn,g.sale_s_dt,g.sale_e_dt

			from goods g
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = g.com_id
				left outer join brand brand on brand.brand = g.brand
				left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
				left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
				left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
			where 1 = 1 and g.com_id = '$com_id'
                $where
            $orderby
			$limit
		";
        // dd($query);
        //$result = DB::select($query,['com_id' => $com_id]);
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if($row["img"] != ""){
				$row["img"] = sprintf("%s%s",config("shop.image_svr"),$row["img"]);
			}
			$result[] = $row;
        }

        //echo "<pre>$query</pre>";
        //dd(array_keys ((array)$result[0]));
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);
    }

	/**
	 * 일괄수정 검색
	 */
	public function edit_search(Request $request) {


		$goods_nos = explode(",", $request->input("goods_nos", ""));

		if (count($goods_nos) > 0) {

			/**
			 * 설정 값 얻기
			 */
			$conf = new Conf();
			$cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
			$cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");
			$cfg_point_rate		= $conf->getConfigValue("point","ratio", "0");

			$sql_goods = "";

			$sql_goods = collect($goods_nos)->reduce(function($carry, $item) {
				$sql = $carry;
				list($goods_no, $goods_sub) = explode("_", $item);
				if ($sql == "") {
					$sql = " select $goods_no as goods_no, $goods_sub as goods_sub \n";
				} else {
					$sql .= " union select $goods_no as goods_no, $goods_sub as goods_sub \n";
				}
				return $sql;
			}, $sql_goods);

			$sql = "
				select
					'' as blank, '' as ret, g.goods_no, g.goods_sub, ifnull( type.code_val, 'N/A') as goods_type_nm, g.style_no,
					opt.opt_kind_nm, brand.brand_nm, c.full_nm as rep_cat_nm, stat.code_val as sale_stat_nm,
					'' as img, g.head_desc, g.goods_nm, g.goods_nm_eng, g.ad_desc,
					g.normal_price, g.price, '' as sale_rate, ifnull(g.sale_price, g.price) as sale_price, '' as margin_rate, ifnull(g.normal_wonga, g.wonga) as normal_wonga, g.wonga, ifnull(g.sale_wonga, g.wonga) as sale_wonga,
					'' as ed_normal_price, '' as ed_price, '' as ed_sale_rate, '' as ed_sale_price, '' as ed_margin_rate, '' as ed_normal_wonga, '' as ed_wonga, '' as ed_sale_wonga,
					ifnull(g.sale_yn, 'N') as sale_yn, ifnull(g.sale_dt_yn, 'N') as sale_dt_yn, g.sale_s_dt, g.sale_e_dt,
					bi.code_val as baesong_info_nm, bk.code_val as baesong_kind_nm,
					dpt.code_val as dlv_pay_type_nm, g.dlv_fee_cfg, g.bae_yn,g.baesong_price,
					g.dlv_due_type, g.dlv_due_period, g.dlv_due_day, g.dlv_due_memo,
					g.point_cfg, g.point_yn, g.point, g.point_unit,
					round(if(g.point_cfg = 'G', if(g.point_yn = 'Y', if(g.point_unit = 'P', g.price * g.point / 100, g.point), 0), g.price * $cfg_point_rate / 100)) as point_amt,
					g.org_nm, g.md_nm,
					replace(g.goods_cont, '\t', '') as goods_cont,
					g.make,
					replace(g.spec_desc, '\t', '') as spec_desc,
					replace(g.baesong_desc, '\t', '') as baesong_desc,
					replace(g.opinion, '\t', '') as opinion,
					if(g.restock_yn = '', 'N', ifnull(g.restock_yn, 'N')) as restock_yn,
					if(g.tax_yn = '', 'N', ifnull(g.tax_yn, 'N')) as tax_yn,
					g.goods_location,
					(
						select group_concat(tag separator ',')
						from goods_tags
						where goods_no = g.goods_no and goods_sub = g.goods_sub
					) as tags,
					g.new_product_type, g.new_product_day,
					ifnull((
						select group_concat(c.code_val_eng separator ',') as color
						from goods_color gc
							inner join code c on gc.color = c.code_id and c.code_kind_cd = 'G_PRODUCTS_COLOR'
						where gc.goods_no  = g.goods_no and gc.goods_sub = g.goods_sub
					),'') as color,
					g.opt_kind_cd, g.brand, g.rep_cat_cd, g.sale_stat_cl,
					g.baesong_info, g.baesong_kind, g.dlv_pay_type,g.md_id,
					if( g.goods_type = 'S', (
						select round(avg(wonga)) as wonga
						from goods_good
						where goods_no = g.goods_no and goods_sub = g.goods_sub and qty > 0
					), g.wonga) as buy_cost,
					'N' as chg_d_cat,
					g.com_type,
					if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
						select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
						from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
					)) as goods_img,
					ifnull(cp.margin_type, '') as margin_type,
					ifnull(cp.pay_fee, '') as pay_fee,
					g.goods_type, g.limited_qty_yn, g.limited_min_qty, g.limited_max_qty, g.limited_total_qty_yn, g.member_buy_yn
				from
					( $sql_goods ) a inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
					left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
					left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd AND opt.opt_id = 'K'
					left outer join brand brand ON brand.brand = g.brand
					left outer join category c ON c.d_cat_cd = g.rep_cat_cd AND c.cat_type  = 'DISPLAY'
					left outer join company cp ON cp.com_id = g.com_id
					left outer join code stat ON stat.code_kind_cd = 'G_GOODS_STAT' AND g.sale_stat_cl = stat.code_id
					left outer join code bk ON bk.code_kind_cd = 'G_BAESONG_KIND' AND bk.code_id = g.baesong_kind
					left outer join code bi ON bi.code_kind_cd = 'G_BAESONG_INFO' AND bi.code_id = g.baesong_info
					left outer join code dpt ON dpt.code_kind_cd = 'G_DLV_PAY_TYPE' AND dpt.code_id = g.dlv_pay_type
			";

			$array = DB::select($sql);

			$rows = collect($array)->map(function($row, $index) {

				$row->index = $index;
				$normal_price = $row->normal_price;
				$price = $row->price;
				$sale_price = $row->sale_price;
				$wonga = $row->wonga;

				if ($row->goods_type == "S") {
					$buy_cost = $row->buy_cost;
					if ($buy_cost == "") $buy_cost = 0;
					if ($buy_cost == 0 ) {
						$wonga = 0;
					} else {
						$wonga = $buy_cost;	// 원가 vat 포함된금액
					}
				}

				// 세일율 계산
				$sale_rate = 0;
				if ($normal_price > 0 && $sale_price > 0) {
					$sale_rate = round((1-$sale_price/$normal_price)*100);
				}
				$row->sale_rate = $sale_rate;

				// 마진율 계산
				$margin_amt = $price - $wonga;	 			// 마진액(판매이익) 추가(vat 포함된금액)
				if ( $price == 0 ) $price = 1; 				// 판매가 0 이면 1 로 강제 변경 (Cannot Division 방지)
				$margin_rate = round($margin_amt * 100 / $price, 2);

				$row->wonga = $wonga;
				$row->margin_rate = $margin_rate;

				// 초기 수정가격 = 현재가격
				$row->ed_normal_price = $normal_price;
				$row->ed_price = $row->price;
				$row->ed_sale_rate = $sale_rate;
				$row->ed_sale_price = $sale_price;
				$row->ed_margin_rate = $margin_rate;
				$row->ed_normal_wonga = $row->normal_wonga;
				$row->ed_wonga = $wonga;
				$row->ed_sale_wonga = $row->sale_wonga;

				return $row;

			});

			return response()->json([
				"code" => 200,
				"head" => array(
					"total" => count($rows)
				),
				"body" => $rows
			]);
		}
	}

	public function edit_save(Request $request) {

		/**
		 * 설정 값 얻기
		 */
		$conf = new Conf();
		$cfg_free_delivery_amt = $conf->getConfigValue("delivery", "free_delivery_amt");
		$cfg_base_delivery_fee = $conf->getConfigValue("delivery", "base_delivery_fee");

		$user = array(
            "id" => Auth('partner')->user()->id,
            "name" => Auth('partner')->user()->name
        );

		$row = $request->input("row", []);
		if (count(array_keys($row)) > 0) {

			$goods_no 		= Lib::Rq($row['goods_no']);
			$goods_sub 		= Lib::Rq($row['goods_sub']);
			$goods_nm 		= Lib::Rq($row['goods_nm']);
			$goods_nm_eng 		= Lib::Rq($row['goods_nm_eng']);
			$head_desc 		= Lib::Rq($row['head_desc']);
			$ad_desc 		= Lib::Rq($row['ad_desc']);

			$normal_price		= Lib::Rq($row['normal_price']);
			$price			= Lib::Rq($row['price']);
			$sale_price		= Lib::Rq($row['sale_price']);
			$wonga			= Lib::Rq($row['wonga']);
			$margin_rate		= Lib::Rq($row['margin_rate']);

			$ed_normal_price	= Lib::Rq($row['ed_normal_price']);
			$ed_price		= Lib::Rq($row['ed_price']);
			$ed_sale_price		= Lib::Rq($row['ed_sale_price']);
			$ed_margin_rate		= Lib::Rq($row['ed_margin_rate']);
			$ed_normal_wonga	= Lib::Rq($row['ed_normal_wonga']);
			$ed_wonga		= Lib::Rq($row['ed_wonga']);
			$ed_sale_wonga		= Lib::Rq($row['ed_sale_wonga']);

			$sale_yn		= Lib::Rq($row['sale_yn']);
			$sale_dt_yn		= Lib::Rq($row['sale_dt_yn']);
			$sale_s_dt		= Lib::Rq($row['sale_s_dt']);
			$sale_e_dt		= Lib::Rq($row['sale_e_dt']);

			$baesong_price 		= Lib::Rq($row['baesong_price']);
			$org_nm 		= Lib::Rq($row['org_nm']);
			$md_nm 			= Lib::Rq($row['md_nm']);
			$goods_cont 		= Lib::Rq($row['goods_cont']);
			$make 			= Lib::Rq($row['make']);
			$spec_desc 		= Lib::Rq($row['spec_desc']);
			$baesong_desc	 	= Lib::Rq($row['baesong_desc']);
			$opinion 		= Lib::Rq($row['opinion']);
			$restock_yn 		= Lib::Rq($row['restock_yn']);
			$opt_kind_cd 		= Lib::Rq($row['opt_kind_cd']);
			$brand 			= Lib::Rq($row['brand']);
			$rep_cat_cd 		= Lib::Rq($row['rep_cat_cd']);
			$sale_stat_cl 		= Lib::Rq($row['sale_stat_cl']);
			$baesong_info 		= Lib::Rq($row['baesong_info']);
			$baesong_kind 		= Lib::Rq($row['baesong_kind']);
			$dlv_pay_type 		= Lib::Rq($row['dlv_pay_type']);
			$dlv_fee_cfg 		= Lib::Rq($row['dlv_fee_cfg']);
			$dlv_fee_yn 		= Arr::exists($row, 'dlv_fee_yn') ? Lib::Rq($row['dlv_fee_yn']) : 'N';
			$point_cfg 		= Lib::Rq($row['point_cfg']);
			$point_yn 		= Lib::Rq($row['point_yn']);
			$point_unit		= Lib::Rq($row['point_unit']);
			$point 			= Lib::Rq($row['point']);
			$md_id 			= Lib::Rq($row['md_id']);


			$com_type 		= Lib::Rq($row['com_type']);
			$goods_type 		= Lib::Rq($row['goods_type']);

			$dlv_due_type		= $row['dlv_due_type'];
			$dlv_due_period		= $row['dlv_due_period'];
			$dlv_due_day		= $row['dlv_due_day'];
			$dlv_due_memo		= Lib::Rq($row['dlv_due_memo']);

			$tax_yn			= Lib::Rq($row['tax_yn']);
			$goods_location		= Lib::Rq($row['goods_location']);
			$tags			= Lib::Rq($row['tags']);
			$new_product_type 	= Lib::Rq($row['new_product_type']);
			$new_product_day	= Lib::Rq($row['new_product_day']);
			$limited_qty_yn		= Lib::Rq($row['limited_qty_yn']);
			$limited_min_qty	= Lib::Rq($row['limited_min_qty']);
			$limited_max_qty	= Lib::Rq($row['limited_max_qty']);
			$limited_total_qty_yn	= Lib::Rq($row['limited_total_qty_yn']);
			$member_buy_yn		= Lib::Rq($row['member_buy_yn']);

			$price			= str_replace(",","",$price);

			// 쇼핑몰 정책에 의한 배송비
			if ($dlv_fee_cfg == "S") {
				$baesong_price = ($price < $cfg_free_delivery_amt) ? $cfg_base_delivery_fee : "0";
			}

			if ($com_type == 1) {		// 공급업체
				//$wonga = Rq($row["ed_wonga"]);
				$price = $ed_price;
				//$wonga = $wonga;
			} else {
				$price = $ed_price;
				//$wonga = round($ed_price * (1-($ed_margin_rate/100)));
				// VAT 별도 원가
				// $wonga = round($price * (1-($margin_rate/100)));
				$wonga = $ed_wonga;
			}

			$param = array(
				"goods_nm"		=> $goods_nm,
				"goods_nm_eng"		=> $goods_nm_eng,
				"opt_kind_cd"		=> $opt_kind_cd,
				"brand"			=> $brand,
				"rep_cat_cd"		=> $rep_cat_cd,
				"sale_stat_cl"		=> $sale_stat_cl,
				"head_desc"		=> $head_desc,
				"ad_desc"		=> $ad_desc,
				"price"			=> $price,
				"wonga"			=> $wonga,
				"baesong_info"		=> $baesong_info,
				"baesong_kind"		=> $baesong_kind,
				"dlv_pay_type"		=> $dlv_pay_type,
				"dlv_fee_cfg"		=> $dlv_fee_cfg,
				"baesong_price"		=> $baesong_price,
				"dlv_due_type"		=> $dlv_due_type,
				"dlv_due_period"	=> $dlv_due_period,
				"dlv_due_day"		=> $dlv_due_day,
				"dlv_due_memo"		=> $dlv_due_memo,
				"bae_yn"		=> $dlv_fee_yn,
				"point_cfg"		=> $point_cfg,
				"point_yn"		=> $point_yn,
				"point_unit"		=> $point_unit,
				"point"			=> $point,
				"md_id"			=> $md_id,
				"md_nm"			=> $md_nm,
				"goods_cont"		=> $goods_cont,
				"make"			=> $make,
				"org_nm"		=> $org_nm,
				"spec_desc"		=> $spec_desc,
				"baesong_desc"		=> $baesong_desc,
				"opinion"		=> $opinion,
				"restock_yn"		=> $restock_yn,
				"com_type"		=> $com_type,
				"goods_type"		=> $goods_type,
				"normal_price"		=> $ed_normal_price,
				"sale_price"		=> $ed_sale_price,
				"normal_wonga"		=> $ed_normal_wonga,
				"sale_wonga"		=> $ed_sale_wonga,
				"sale_yn"		=> $sale_yn,
				"sale_dt_yn"		=> $sale_dt_yn,
				"sale_s_dt"		=> $sale_s_dt,
				"sale_e_dt"		=> $sale_e_dt,

				"goods_location"	=> $goods_location,
				"tax_yn"		=> $tax_yn,
				"new_product_type"	=> $new_product_type,
				"new_product_day"	=> $new_product_day,
				"limited_qty_yn"	=> $limited_qty_yn,
				"limited_min_qty"	=> $limited_min_qty,
				"limited_max_qty"	=> $limited_max_qty,
				"limited_total_qty_yn"	=> $limited_total_qty_yn,
				"member_buy_yn"		=> $member_buy_yn
			);

			try {
				DB::beginTransaction();

				$goods = new Product($user);


				$test = DB::table('goods')->where('goods_no','=',$goods_no);

				// $goods->SetGoodsNo( $goods_no );
				$result = $goods->Edit( $goods_no, $param );

				// 전시카테고리에도 반영
				$category = new Category($user, "DISPLAY"); // user 설정 및 초기화
				$category->SetGoodsNoSub( $goods_no, $goods_sub ); // 상품번호 설정

				// 기존 카테고리 전시에서 삭제
				$sql = "
					select d_cat_cd from category_goods
					where goods_no = '${goods_no}' and goods_sub = '${goods_sub}' and cat_type = 'DISPLAY'
				";
				$result = DB::select($sql);

				collect($result)->map(function($item) use($category) {
					$_d_cat_cd = $item->d_cat_cd;
					$category->SetCode($_d_cat_cd);
					$category->DeleteGoodsCode();
				});

				// 새로운 카테고리 등록
				$cnt = strlen($rep_cat_cd) / 3;
				for ($k=0; $k < $cnt; $k++) {
					$cd = substr($rep_cat_cd, 0, ($cnt - $k) * 3);
					$category->SetCode($cd);
					$category->AddGoods();
				}

				if ( $tags != "" ) {
					$a_tags = explode(",", $tags);

					// 등록 전 삭제
					$sql = " delete from goods_tags where goods_no = :goods_no and goods_sub = :goods_sub ";
					$inputarr = array("goods_no" => $goods_no, "goods_sub" => $goods_sub);
					DB::delete($sql, $inputarr);

					for ( $i = 0; $i < count($a_tags); $i++) {
						$_tag = Lib::Rq(trim($a_tags[$i]));
						if ( $_tag == "" ) continue;

						// 태그 검색
						$sql = "
							select count(*) as cnt
							from goods_tags
							where goods_no = :goods_no and goods_sub = :goods_sub and tag = :tag
						";
						$selectarr = array(
							"goods_no" => $goods_no,
							"goods_sub" => $goods_sub,
							"tag" => $_tag
						);
						$row = DB::selectOne($sql, $selectarr);
						$cnt = $row->cnt;

						if ($cnt == 0) {
							$sql = "
								insert into goods_tags (
									goods_no, goods_sub, tag, admin_id, admin_nm, rt
								) values (
									:goods_no, :goods_sub, :tag, :id, :name, now()
								)
							";
							$inputarr = array(
								"goods_no" => $goods_no,
								"goods_sub" => $goods_sub,
								"tag" => $_tag,
								"id" => $user["id"],
								"name" => $user["name"]
							);
							DB::insert($sql, $inputarr);
						}
					}
				}

				DB::commit();

				return response()->json([
					"code" => 1,
					"message" => "success"
				]);

			} catch (Exception $e) {

				DB::rollBack();
				$code = 0;
				$message = $e->getMessage();
				if ($e->getCode() == -1) {
					$code = $e->getCode();
					$message = $e->getMessage();
				}

				return response()->json([
					"code" => $code,
					"message" => $message
				]);

			}

		}

	}

    public function show_in_qty($no) {
        return view(Config::get('shop.partner.view') . '/product/prd01_show_qty', [
            'goods_no' => $no,
            'goods_sub' => 0
        ]);
    }

    public function options($no, Request $req) {
        $sql = "  -- [".Auth('partner')->user()->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
            select a.goods_no, a.goods_sub, a.goods_opt, 0 as qty
             from goods_summary a
            where a.goods_no = '$no' and a.goods_sub = '$req->goods_sub'
              and a.use_yn = 'Y'
            order by a.seq
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function update_in_qty($no, Request $req) {
        // 설정 값 얻기
        $cfg_domain		= $this->get_config_value("shop","domain");

        $goods_no = $req->input('goods_no', '');
        $goods_sub = $req->input('goods_sub', '');
        $stock_date =  $req->input('stock_date', date('Ymd'));
        $invoice_no = $req->input('invoice_no', date('Ymd'));
		
		$options = $req->input('options', []);

        $user = array(
            "id" => Auth('partner')->user()->id,
            "name" => Auth('partner')->user()->name
        );

        try {
            DB::beginTransaction();
            //재고 클래스 호출
            $prd = new Product($user);
            $err_msg = "";
			
			forEach($options as $option) {
				$goods_no = $option['goods_no'];
				$goods_sub = $option['goods_sub'];
				$goods_opt = $option['goods_opt'];
				$qty = $option['qty'];
				
				$sql = "
					select opt_name
					from goods_summary
					where goods_no = '$goods_no'
						and goods_sub = '$goods_sub'
						and goods_opt = '$goods_opt'
				";
	
				$row = DB::selectOne($sql);
				$opt_name = $row->opt_name;

				$check = $prd->Plus( array(
					"type" => 1,
					"etc" => '',
					"qty" => $qty,
					"goods_no" => $goods_no,
					"goods_sub" => $goods_sub,
					"goods_opt" => $goods_opt,
					"invoice_no" => $invoice_no,
					"opt_name"	=>  $opt_name,
					"opt_price" => '',
					'wonga' => '',
					'ord_no' => '',
					'ord_opt_no' => '',
					'ord_state' => '',
					'opt_seq' => '',
					"wonga_apply_yn" => "N"
				));

				if(! $check) {
					throw new Exception("재고조정용 발주건 또는 송장번호가 존재하지 않습니다.\\n발주건은 공급처가 [$cfg_domain] 만 가능합니다.\\n");
				}
			}
            

            DB::commit();
            return response()->json(null, 201);
        } catch(Exception $e){
            DB::rollback();
            return response()->json(['msg' => $e->getMessage()], 500);
        }
    }

    public function update_qty(Request $req) {
        DB::table('goods_summary')
            ->where([
                'goods_no' => $req->goods_no,
                'goods_sub' => $req->goods_sub,
            ])
            ->update([
                'good_qty' => $req->qty
            ]);

        return response()->json(null, 201);
    }

    public function update_state(Request $request)
	{
        $goods_nos		= $request->input('goods_no');
        $chg_sale_stat	= $request->input('chg_sale_stat');

        $user	= array(
			"id"	=> Auth('partner')->user()->id,
			"name"	=> Auth('partner')->user()->name
			//"id" => Auth('partner')->user()->com_id,
			//"name" => Auth('partner')->user()->com_nm
        );

        $prd		= new Product($user);
        $category	= new Category($user, "DISPLAY");

        DB::beginTransaction();

        $success	= 0;
        $fail		= 0;

        for( $i = 0; $i < count($goods_nos); $i++ )
		{
            $goods_no	= $goods_nos[$i];
            $prd->SetGoodsNo($goods_no);

            if( $chg_sale_stat == 30 )
			{
                $category->SetSeq($goods_no, "bottom");
            }

            $updates	= $prd->UpdateState($chg_sale_stat);
            $success	+= $updates[0];
            $fail		+= $updates[1];
        }

        if( $fail === 0 )
		{
            $code	= 200;
            DB::commit();
        }
		else
		{
            $code	= 200;
            DB::rollBack();
        }

        return response()->json([
            "code"	=> $code,
            "head"	=> array(
                "success"	=> $success,
                "fail"		=> $fail
            )
        ]);
    }

	public function create()
	{
		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");

		$com_id = Auth('partner')->user()->com_id;
		$com_nm = Auth('partner')->user()->com_nm;
		$user		= Auth('partner')->user();
		$pay_fee    = $user->pay_fee;

		$opt	= ['opt1' => [], 'opt2' => []];

		$query	= "
			select a.class , a.class_nm
			from code_class a
			group by class, class_nm
		";

		$class_items = DB::select($query);

		$goods_info = new \stdClass();
        $goods_info->sale_stat_cl = '5';
        $goods_info->goods_type = 'P';
        $goods_info->baesong_info = '1';
        $goods_info->baesong_kind = '2';
        $goods_info->tax_yn = 'Y';
        $goods_info->pay_fee = $pay_fee;
        $goods_info->is_unlimited = 'N';
        $goods_info->is_option_use = 'Y';


		return view(Config::get('shop.partner.view') . '/product/prd01_show',
			[
				'com_nm'			=> $com_nm,
				'goods_no'		=> '',
				'goods_info'	=> $goods_info,
				'md_list'		=> SLib::getMDs(),
				'opt_cd_list'	=> SLib::getItems(),
				'com_info'		=> Auth('partner')->user(),
				'qty'			=> 0,
				'wqty'			=> 0,
				'coupon_list'	=> [],
				'planing'		=> [],
				'modify_history'=> [],
				'type'			=> 'create',
				'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
				'class_items'	=> $class_items,
				'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),

				'g_dlv_fee'		=> $cfg_dlv_fee,
				'g_free_dlv_fee_limit'	=> $cfg_free_dlv_fee_limit,
				'g_order_point_ratio'	=> $cfg_order_point_ratio,

				'opt'			=> $opt,
				'opt2'			=> array()
			]
		);
	}

    public function show($goods_no, Request $req)
    {
		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");

		$com_id = Auth('partner')->user()->com_id;
		$com_nm = Auth('partner')->user()->com_nm;

		$type = $req->input("type", '');
        //$type = $req->input('type', '');	// 단순 페이지 상태 생성:create, XXXXXXXXXXXXXX상품형식-일반/납품/기획:N(혹은 無값)/D/E

        $query = /** @lang text */
            "
            select a.class , a.class_nm
              from code_class a
             group by class, class_nm
        ";
        $class_items = DB::select($query);

		$conf_sql = /** @lang text */
			"
            select 
            	value, 
            	mvalue
            from conf
            where 
            	type = 'shop'
            	and name = 'domain'
        ";
			
		$conf_items = DB::selectOne($conf_sql);
		
		$values = $this->_get($goods_no);

		$values = array_merge($values,[
			'com_nm'			=> $com_nm,
            'opt_cd_list'		=> SLib::getItems(),
            'md_list'			=> SLib::getMDs(),
            'type'				=> $type,
            'goods_stats'		=> SLib::getCodes('G_GOODS_STAT'),
            'class_items'		=> $class_items,
            'goods_types'		=> SLib::getCodes('G_GOODS_TYPE'),
            'com_info'			=> (object)array("dlv_amt" => 0,"free_dlv_amt_limit" => 0),
            'g_dlv_fee'			=> $cfg_dlv_fee,
            'g_free_dlv_fee_limit'	=> $cfg_free_dlv_fee_limit,
            'g_order_point_ratio'	=> $cfg_order_point_ratio,
			'front_url'			=> $conf_items
        ]);

        return view(Config::get('shop.partner.view') . '/product/prd01_show',$values);
    }

    public function get($goods_no){
        return response()->json($this->_get($goods_no));
    }

    private function _get($goods_no){

        $query = /** @lang text */
            "
				select
					a.head_desc, a.goods_nm, a.goods_nm_eng, a.ad_desc, a.opt_kind_cd, a.goods_sub
					, a.brand, br.brand_nm, a.sale_stat_cl, a.style_no, a.goods_type, ifnull( type.code_val, 'N/A') as goods_type_nm
					, a.com_id, c.com_nm, c.com_type, c.pay_fee, a.make, a.org_nm, a.goods_memo
					, a.price, a.goods_sh, a.wonga, a.delv_area, a.dlv_pay_type, a.dlv_fee_cfg
					, a.bae_yn, a.baesong_price, a.baesong_kind, a.baesong_info
					, a.goods_location, a.point_cfg, a.point_yn, a.point, a.tax_yn, a.md_id
					, a.reg_dm, date_format(a.reg_dm,'%Y%m%d') as reg_dm_ymd	, a.upd_dm
					, a.rep_cat_cd, '' as rep_cat_nm, a.goods_cont, a.spec_desc, a.baesong_desc, a.opinion
					, replace(a.img,'a_500', 'a_500') as img
					, a.goods_no_org, c.margin_type
					, ifnull(a.sale_price,0) as sale_price, a.sale_s_dt, a.sale_e_dt, a.before_sale_price
					, (1 - (a.wonga) / ifnull(a.before_sale_price, a.price)) * 100 as before_sale_margin
					, (1 - (a.wonga) / ifnull(a.sale_price,1)) * 100 as sale_margin
					, a.option_kind
					, ifnull(cd.code_id,'ETC') as option_kind_type
					, a.is_unlimited
					, ifnull(a.is_option_use,'Y') as is_option_use
					, ifnull(a.related_cfg,'A') as related_cfg
					, restock_yn
					, a.new_product_type
					, a.new_product_day
					, (100/(a.price/(a.price-a.wonga))) as prf
					, ifnull(c.dlv_policy, 'S') as dlv_policy
					, a.sale_type, a.sale_yn, a.sale_wonga, a.normal_wonga, a.sale_dt_yn, ifnull(a.normal_price,0) as normal_price
					, a.class
				from goods a
					left join brand br on br.brand = a.brand
					left join company c on c.com_id = a.com_id
					left outer join code cd on cd.code_kind_cd = 'G_OPTION_KIND' and cd.code_id = a.option_kind
                                        left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and a.goods_type = type.code_id
				where a.goods_no = :goods_no
            ";

        $goods_info = DB::selectone($query,array("goods_no" => $goods_no));

        // 총 재고 합계
        $qty = 0;
        $wqty = 0;

        $query = /** @lang text */
            "
            select
                sum(good_qty) as qty,
                sum(wqty) as wqty
            from goods_summary
            where goods_no = :goods_no
        ";
        $qty_info = DB::selectone($query,array("goods_no" => $goods_no));
        if($qty_info){
            $qty = $qty_info->qty;
            $wqty = $qty_info->wqty;
        }

        $coupon_list		= $this->get_coupon_info($goods_no, $goods_info->price); // 쿠폰 리스트
        $modify_history		= $this->get_history_modify($goods_no);
        $goods_info->cat_nm	= $this->get_cat_nm($goods_info->rep_cat_cd);
        $goods_info->goods_related	= $this->get_goods_related_info($goods_no);
        $planing			= $this->get_planing_list($goods_no, $goods_info->goods_sub); // 전시상품 리스트

		$goods_info->img = $goods_info->img;
        $goods_images[0] = $goods_info->img;

        $images = DB::table("goods_image")
            ->select("type", "img")
            ->where("goods_no","=",$goods_no)->get();

        foreach ($images as $image) {
            $goods_images[] = $image->img;
        }

        $user		= Auth('partner')->user();
        $category	= new Category($user, "DISPLAY");
        $rep_cat_nm	= substr( $category->Location( $goods_info->rep_cat_cd ), 0 );

		//대표카테고리명 업데이트
		$goods_info->rep_cat_nm	= $rep_cat_nm;

        //상품 옵션 정보
        //1. 단일옵션, 2. 다중옵션(2단)
        $sql	= /** @lang text */
            " select count(*) as tot from goods_option where goods_no = :goods_no and type = 'basic' ";
        $row	= DB::selectOne($sql,['goods_no' => $goods_no]);
        $opt_kind_cnt	= $row->tot;
		$opt 			= ['opt1' => [], 'opt2' => []];
		$opt_kind_list	= [];

		$sql	= /** @lang text */
			" select distinct(substring_index(goods_opt, '^', :index)) as opt_nm from goods_summary where goods_no = :goods_no and use_yn = 'Y' order by opt_nm ";

		if($opt_kind_cnt > 0) {
			$opt['opt1'] = DB::select($sql,['goods_no' => $goods_no, 'index' => 1]);
			if($opt_kind_cnt == 2) $opt['opt2'] = DB::select($sql,['goods_no' => $goods_no, 'index' => -1]);
		}

		$sql	=
			"select type, name, required_yn, use_yn from goods_option where goods_no = :goods_no and use_yn = 'Y' and type='basic'";
		$opt_kind_list	= DB::select($sql, ['goods_no' => $goods_no]);

        $sql	= /** @lang text */
            "
                select opt_name,goods_opt,opt_price,good_qty as qty,wqty,soldout_yn
                from goods_summary
                where goods_no = :goods_no
                order by goods_opt
          ";
        $options	= DB::select($sql,['goods_no' => $goods_no]);

		//상품 배송비 설정
		if( $goods_info->dlv_policy == "S" ){
			$conf	= new Conf();
			$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
			$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");

			if( $goods_info->price < $cfg_free_dlv_fee_limit )
				$goods_info->baesong_price	= $cfg_dlv_fee;
			else
				$goods_info->baesong_price	= 0;
		}

		// 세일설정 관련
		$goods_info->sale_types = [];
		foreach(array_keys($this->sale_types) as $sale_type) {
			array_push($goods_info->sale_types, ['key' => $sale_type, 'value' => $this->sale_types[$sale_type]]);
		}
		$goods_info->sale_rate = 0;
		$n_pr = $goods_info->normal_price;
		$s_pr = $goods_info->sale_price;
		if($goods_info->sale_yn == 'Y' && $s_pr > 0) {
			$rate = ( ($n_pr - $s_pr) / $n_pr );
			$goods_info->sale_rate = round($rate * 100);
		}

        return  [
            'goods_no'			=> $goods_no,
            'goods_info'		=> $goods_info,
            'goods_images'		=> $goods_images,
            'qty'				=> $qty,
            'wqty'				=> $wqty,
            'coupon_list'		=> $coupon_list,
            'modify_history'	=> $modify_history,
            'planing'			=> $planing,
			'opt'				=> $opt,
            'options'           => $options,
			'opt_kind_list'		=> $opt_kind_list,
        ];
    }

	public function create_goods(Request $req) {
		$user	= Auth('partner')->user();
		$id		= Auth('partner')->user()->id;
		$name	= Auth('partner')->user()->name;

		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
		$cfg_domain_bizest			= $conf->getConfigValue("shop","domain_bizest");
		$cfg_domain					= $conf->getConfigValue("shop","domain");

		$dlv_fee_cfg	= $req->input('dlv_fee_cfg');
		$d_category		= $req->input('d_category_s');
		$u_category		= $req->input('u_category_s');
		$is_sub			= $req->input('is_sub', 0);		// 보조 상품 유무 ( 사용안함 )

		$goods_no		= 0;
		$goods_sub		= 0;

		$price			= str_replace(',', '', $req->input('price', 0));
		$wonga			= str_replace(',', '', $req->input('wonga', 0));

		$goods_type = $req->input('goods_type');

		$point_cfg	= $req->input('point_cfg','S');
		$point_yn	= $req->input('point_yn','Y');
		$point_unit	= $req->input('point_unit','W');
		$point		= $req->input('point', 0);

		$bae_yn = $req->input('bae_yn');
		$baesong_price = $req->input('baesong_price');
		$baesong_kind = $req->input('baesong_kind', '1');

		$is_option_use = $req->input('is_option_use', 'Y');
		$goods_qty = $req->input('goods_qty', 0);
		
		$goods_class = (array) json_decode($req->input('goods_class', '')) ?? [];
		
		try {
			DB::beginTransaction();

			if( $is_sub != 0 ){
				$goods_no	= $req->input('goods_no');
				$goods_sub	= DB::selectOne("
							select max(goods_sub) + 1 as goods_sub
							from goods
							where goods_no = $goods_no
						")->goods_sub;
			}else{
				$goods_no	= DB::selectOne("select max(goods_no) + 1 as goods_no from goods")->goods_no;
				$goods_sub	= 0;
			}

			//전시 카테고리
			if( $d_category != "" ){
				$d_category_arr	= explode(',',$d_category);
				foreach( $d_category_arr  as $key => $d_cat ){
					if( $key > 0 ) {
						$this->insert_category("DISPLAY", $d_cat, $goods_no, $goods_sub);
					}
				}
			}

			//용도 카테고리
			if($u_category != ""){
				$u_category_arr	= explode(',',$u_category);
				foreach( $u_category_arr  as $key => $u_cat ){
					if( $key > 0 ){
						$this->insert_category("ITEM", $u_cat, $goods_no, $goods_sub);
					}
				}
			}

			// 배송비 설정 - 쇼핑몰
			if( $dlv_fee_cfg == "S" ){
				$bae_yn	= "Y";
				$baesong_price	= $cfg_dlv_fee;
			}

			// 적립금 계산 - 쇼핑몰
			if( $point_cfg = "S" ){
				$point_yn	= "Y";
				$point		= $price * $cfg_order_point_ratio / 100;
			}

			//옵션관리 안함 상품의 수량 등록
			if( $is_option_use == "N" ) {
				// 기본 옵션 등록
				$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
					insert into goods_option (
						goods_no, goods_sub, type, name, required_yn, use_yn, seq, option_no, rt, ut
					) values (
						'$goods_no', '$goods_sub', 'basic', 'NONE', 'Y', 'Y', '0', null, now(), now()
					)
				";
				DB::insert($sql);

				// 기본 재고 등록
				$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
					insert into goods_summary (
						goods_no, goods_sub, opt_name, goods_opt, opt_price, good_qty, wqty,
						soldout_yn, use_yn, seq, rt, ut, bad_qty, last_date
					) values	(
						'$goods_no', '$goods_sub', 'NONE', 'none', '0', '$goods_qty', '$goods_qty',
						'N', 'Y', '0', now(), now(), 0, now()
					)
				";
				DB::insert($sql);

				if( $goods_type == "S" ){
					// 기본 매입상품 처리
					$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
						insert into goods_good (
						goods_no, goods_sub, goods_opt, opt_type, opt_price, wonga, qty, invoice_no, init_qty, regi_date
						) values (
						'$goods_no', '$goods_sub', 'none', null, 0, '$wonga', '$goods_qty', '', '$goods_qty', now()
						)
					";
					DB::insert($sql);
				}
			}
			
			//md 아이디, 네임 가져오기
			$com_id = Auth('partner')->user()->com_id;
			$md_sql = " 
					 select
					    md_id,
					    md_nm
					 from 
					     company
 					 where
 					     com_id = '$com_id'
					";
			
			$md_info = DB::selectOne($md_sql);
	
			$a_goods = array(
				"goods_no"			=> $goods_no,
				"goods_sub"			=> $goods_sub,
				"head_desc"			=> $req->input('head_desc', ''),
				"goods_nm"			=> $req->input('goods_nm', ''),
				"goods_nm_eng"		=> $req->input('goods_nm_eng', ''),
				"ad_desc"			=> $req->input('ad_desc', ''),
				"opt_kind_cd"		=> $req->input('opt_kind_cd', ''),
				"brand"				=> $req->input('brand_cd', ''),
				"sale_stat_cl"		=> $req->input('sale_stat_cl', ''),
				"style_no"			=> $req->input('style_no', ''),
				"goods_type"		=> $req->input('goods_type', ''),
				"com_id"			=> Auth('partner')->user()->com_id,
				"com_type"			=> 2,
				"com_nm"			=> Auth('partner')->user()->com_nm,
				"make"				=> $req->input('make', ''),
				"org_nm"			=> $req->input('org_nm', ''),
				"goods_memo"		=> $req->input('goods_memo', ''),
				"goods_sh"			=> str_replace(',','',$req->input('goods_sh')),
				"price"				=> $price,
				"wonga"				=> $wonga,
				"baesong_info"		=> $req->input('baesong_info', '1'),
				"dlv_pay_type"		=> $req->input('dlv_pay_type', 'P'),
				"dlv_fee_cfg"		=> $dlv_fee_cfg,
				"bae_yn"			=> $bae_yn,
				"baesong_price"		=> $baesong_price,
				"baesong_kind"		=> $baesong_kind,
				"goods_location"	=> $req->input('goods_location', ''),
				"point_cfg"			=> $point_cfg,
				"point_yn"			=> $point_yn,
				"point_unit"		=> $point_unit,
				"point"				=> $point,
				"tax_yn"			=> $req->input('tax_yn', 'Y'),
				"md_id"				=> $md_info->md_id !== null ? $md_info->md_id : Auth('partner')->user()->com_id , 
				"md_nm"				=> $md_info->md_nm !== null ? $md_info->md_nm : $name ,
				"is_unlimited"		=> $req->input('is_unlimited', 'N'),
				"is_option_use"		=> $is_option_use,
				"rep_cat_cd"		=> $req->input('rep_cat_cd',''),
				"goods_cont"		=> str_replace($cfg_domain, "", $req->input('goods_cont')),
				"spec_desc"			=> $req->input('spec_desc'),
				"baesong_desc"		=> $req->input('baesong_desc'),
				"admin_id"			=> $id,
				"admin_nm"			=> $name,
				"opinion"			=> $req->input('opinion'),
				"related_cfg"		=> 'A',
				"restock_yn"		=> $req->input('restock_yn', 'N'),
				"new_product_type"	=> $req->input('new_product_type', 'M'),
				"new_product_day"	=> $req->input('new_product_day', ''),
				"reg_dm"			=> DB::raw("now()")
			);

			$result = DB::table('goods')->insertGetId($a_goods, 'goods_no');

			// goods_type = P 처리 생략 ( 위탁업체 )
			// goods_wonga 테이블
			// 도매처리 생략
			// 상품 컬러 생략
            
            // 상품정보고시 내용 저장
            if (isset($goods_class['class_cd'])) {
                $values = [
                    'item_001' => $goods_class['item_001'] ?? '',
                    'item_002' => $goods_class['item_002'] ?? '',
                    'item_003' => $goods_class['item_003'] ?? '',
                    'item_004' => $goods_class['item_004'] ?? '',
                    'item_005' => $goods_class['item_005'] ?? '',
                    'item_006' => $goods_class['item_006'] ?? '',
                    'item_007' => $goods_class['item_007'] ?? '',
                    'item_008' => $goods_class['item_008'] ?? '',
                    'item_009' => $goods_class['item_009'] ?? '',
                    'item_010' => $goods_class['item_010'] ?? '',
                    'item_011' => $goods_class['item_011'] ?? '',
                    'item_012' => $goods_class['item_012'] ?? '',
                    'item_013' => $goods_class['item_013'] ?? '',
                    'item_014' => $goods_class['item_014'] ?? '',
                    'item_015' => $goods_class['item_015'] ?? '',
                    'item_016' => $goods_class['item_016'] ?? '',
                    'item_017' => $goods_class['item_017'] ?? '',
                    'item_018' => $goods_class['item_018'] ?? '',
                    'item_019' => $goods_class['item_019'] ?? '',
                    'item_020' => $goods_class['item_020'] ?? '',
                ];
                
                $class_cd = $goods_class['class_cd'] ?? '';
                if ($class_cd !== '') {
                    $values['class'] = $class_cd;
                
                    DB::table('goods')
                        ->where('goods_no', $goods_no)
                        ->where('goods_sub', $goods_sub)
                        ->update([ 'class' => $class_cd ]);
                }

                $where = [ 'goods_no' => $goods_no, 'goods_sub' => $goods_sub ];
                DB::table('goods_class')->updateOrInsert($where, $values);
            }

			DB::commit();
			return response()->json($goods_no, 201);
		} catch(Exception $e){
			DB::rollback();
			return response()->json(['msg' => $e->getMessage()], 500);
			//return response()->json(['msg' => "업로드 도중 에러가 발생했습니다. 잠시 후 다시시도 해주세요."], 500);
		}
	}

    public function goods_class(Request $req) {
        $where = '';

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $goods_no    = $req->input('goods_no', '');
        $goods_sub   = $req->input('goods_sub', '');
        $goods_class = $req->input('class', '');
        $page        = $req->input("page", -1);

		$com_id = Auth('partner')->user()->com_id;

        $page_size = 100;
        $startno = ($page-1) * $page_size;
        $sql_limit = "";

        if($page > -1) {
            $sql_limit = " limit $startno, $page_size";
        }

        if ($goods_no !== '')    $where .= " and g.goods_no = $goods_no ";
        if ($goods_sub !== '')   $where .= " and g.goods_sub = $goods_sub ";
        // if ($goods_class !== '') $where .= " and ifnull(g.class,'') = '$goods_class' ";

        $where .= " and g.com_id = '$com_id' ";

        $query = "
			select count(*) as total
			from goods g
			where 1=1
			$where
		";

        $row = DB::selectOne($query);
        $total = $row->total;
        $page_cnt=(int) (($total-1)/$page_size) + 1;

        DB::statement(DB::raw('SET @ROWNUM:=0'));

        $query = "
          select
              @ROWNUM = @ROWNUM + 1 AS rownum,
              '' as blank,
              ifnull( type.code_val, 'N/A') as goods_type,
              com.com_nm, opt.opt_kind_nm, brand.brand_nm, g.style_no, '' as img2,
              if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                  select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                  from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
              )) as img, g.goods_nm, stat.code_val as sale_stat_cl,
              g.goods_no, g.goods_sub, com.com_id,
              (select class_nm from code_class where class = g.class group by class, class_nm) as class,
              class.item_001, class.item_002, class.item_003, class.item_004, class.item_005,
              class.item_006, class.item_007, class.item_008, class.item_009, class.item_010,
              class.item_011, class.item_012, class.item_013, class.item_014, class.item_015,
              class.item_016, class.item_017, class.item_018, class.item_019, class.item_020,
			  g.class as class_cd
           from goods g
              left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
              left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
              left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
              left outer join company com on com.com_id = g.com_id
              left outer join brand brand on brand.brand = g.brand
              left outer join goods_class class on g.goods_no = class.goods_no and g.goods_sub = class.goods_sub and g.class = class.class
          where 1=1
              $where
          order by g.goods_no
          $sql_limit
      	";

		$result = DB::select($query);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			),
			"body" => $result
		]);
	}

	public function get_option_name($no, Request $req) {
		$sql	= "
			select 	b.code_val as type, a.name, a.required_yn, a.use_yn, a.no
			from goods_option a
				inner join code b on b.code_kind_cd = 'G_OPTION_TYPE' and b.code_id = a.type
			where a.goods_no = '$no'
				and a.goods_sub = '$req->goods_sub'
			order by a.type, a.seq
		";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result)
			),
			"body" => $result
		]);
	}

	public function get_option_stock(Request $request)
	{
		$type = $request->input("data.type", "기본");
		$goods_no = $request->input("data.goods_no");
		$option_no = $request->input("data.no");

		if ($type == "기본") {
			$sql = "select * from goods_summary where `goods_no` = :goods_no order by seq";
			$result = DB::select($sql, ['goods_no' => $goods_no]);
		} else if ($type == "추가") {
			$sql = "select * from `options` where `option_no` = :option_no order by seq";
			$result = DB::select($sql, ['option_no' => $option_no]);
		};

		return response()->json([ "result" => $result ]);
	}

	public function goods_class_update(Request $req) {

		try {
			DB::beginTransaction();

			$values = [
				'item_001' => $req->input('item_001', ''),
				'item_002' => $req->input('item_002', ''),
				'item_003' => $req->input('item_003', ''),
				'item_004' => $req->input('item_004', ''),
				'item_005' => $req->input('item_005', ''),
				'item_006' => $req->input('item_006', ''),
				'item_007' => $req->input('item_007', ''),
				'item_008' => $req->input('item_008', ''),
				'item_009' => $req->input('item_009', ''),
				'item_010' => $req->input('item_010', ''),
				'item_011' => $req->input('item_011', ''),
				'item_012' => $req->input('item_012', ''),
			];

			$class_cd = $req->input('class_cd', '');

			if ($class_cd !== '') {
				$values['class'] = $class_cd;

				DB::table('goods')
					->where('goods_no', $req->goods_no)
					->where('goods_sub', $req->goods_sub)
					->update(['class' => $class_cd]);
			}

			$where = [
				'goods_no' => $req->goods_no,
				'goods_sub' => $req->goods_sub
			];

			DB::table('goods_class')->updateOrInsert($where, $values);

			DB::commit();

			return response()->json(null, 201);
		} catch(Exception $e){
			DB::rollback();
			return response()->json(['msg' => "수정중 에러가 발생했습니다. 잠시 후 다시시도 해주세요."], 500);
		}
	}

	public function goods_class_delete(Request $req) {
		try {
			DB::beginTransaction();

			$sql	= " update goods set class = '' where goods_no = :goods_no and goods_sub = :goods_sub ";
			DB::update($sql, [
					'goods_no' => $req->goods_no,
					'goods_sub' => $req->goods_sub
				]
			);

			$sql	= " delete from goods_class where goods_no = :goods_no and goods_sub = :goods_sub ";
			DB::delete($sql, [
					'goods_no' => $req->goods_no,
					'goods_sub' => $req->goods_sub
				]
			);

			DB::commit();

			return response()->json(null, 201);
		} catch(Exception $e){
			DB::rollback();
			return response()->json(['msg' => "삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요."], 500);
		}
	}

	public function goods_class_opt_update(Request $req){
		$err_code	= "200";	//정상
		$msg		= "";

		$goods_class	= $req->input('goods_class');
		$goods_no		= $req->input('goods_no');
		$class			= "";

		$sql	= " select class from goods_class where goods_no = :goods_no and goods_sub = '0' ";
		$row	= DB::selectOne($sql, ["goods_no" => $goods_no]);

		if(!empty($row->class)){
			$class	= $row->class;
		}

		try {
			DB::beginTransaction();

			if( $class != $goods_class ){
				$sql	= " update goods set class = :class where goods_no = :goods_no and goods_sub = '0' ";
				DB::update($sql, ['class' => $goods_class, 'goods_no' => $goods_no]);

				$sql	= " delete from goods_class where goods_no = :goods_no and goods_sub = '0' ";
				DB::delete($sql, ['goods_no' => $goods_no]);
			}else{
				$err_code	= "300";
				$msg		= "변경할 품목을 선택해야 합니다.";
			}

			DB::commit();
		} catch(Exception $e){

			$err_code	= "500";
			$msg		= "시스템 에러입니다. 관리자에게 문의하세요.";

			DB::rollback();
		}

		return response()->json(['code' => $err_code, 'msg' => $msg]);
	}

	// 상품 수정
	public function update(request $request){

		// dd($request->all());

		$code_status = 200;
		$msg = '';

		$user	= Auth('partner')->user();
		$id		= Auth('partner')->user()->id;
		$name	= Auth('partner')->user()->name;

		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
		$cfg_domain_bizest			= $conf->getConfigValue("shop","domain_bizest");
		$cfg_domain					= $conf->getConfigValue("shop","domain");

		$goods_no			= $request->input('goods_no');
		$goods_sub			= $request->input('goods_sub');
		$head_desc			= $request->input('head_desc');
		$goods_nm			= $request->input('goods_nm');
		$goods_nm_eng		= $request->input('goods_nm_eng');
		$ad_desc			= $request->input('ad_desc');
		$brand				= $request->input('brand_cd');
		$sale_stat_cl		= $request->input('sale_stat_cl');
		$style_no			= $request->input('style_no');
		$goods_type			= $request->input('goods_type');

		$point_cfg			= $request->input('point_cfg','S');
		$point_yn			= $request->input('point_yn','Y');
		$point_unit			= $request->input('point_unit','W');
		$point				= $request->input('point', 0);

		$com_id				= Auth('partner')->user()->com_id;
		$com_nm				= Auth('partner')->user()->com_nm;
		$com_type			= $request->input('com_type');
		$opt_kind_cd		= $request->input('opt_kind_cd');
		$make				= $request->input('make');
		$org_nm				= $request->input('org_nm');
		$goods_memo			= $request->input('goods_memo');
		$price				= str_replace(',', '', $request->input('price', 0));
		$normal_price		= $price;
		$wonga				= str_replace(',', '', $request->input('wonga', 0));
		$margin				= $request->input('margin');
		$tax_yn				= $request->input('tax_yn');
		$restock_yn			= $request->input('restock_yn', 'N');
		$goods_sh			= str_replace(',', '', $request->input('goods_sh', 0));
		$baesong_info		= $request->input('baesong_info');
		$baesong_kind		= $request->input('baesong_kind');
		$dlv_pay_type		= $request->input('dlv_pay_type');
		$dlv_fee_cfg		= $request->input('dlv_fee_cfg');
		$bae_yn				= $request->input('bae_yn');
		$baesong_price		= str_replace(',', '', $request->input('baesong_price', 0));
		$goods_location		= $request->input('goods_location');
		$new_product_type	= $request->input('new_product_type');
		$new_product_day	= str_replace('-', '', $request->input('new_product_day'));
		$is_unlimited		= $request->input('is_unlimited');
		$is_option_use		= $request->input('is_option_use');
		//$qty = $request->input('qty');
		//$wqty = $request->input('wqty');
		$goods_cont			= Lib::Rq(str_replace($cfg_domain, "", $request->input('goods_cont')));

		$spec_desc			= $request->input('spec_desc');
		$baesong_desc		= $request->input('baesong_desc');
		$opinion			= $request->input('opinion');
		$related_cfg        = $request->input('related_cfg');
		$d_category			= $request->input('d_category_s');
		$u_category			= $request->input('u_category_s');
		$rep_cat_cd			= $request->input('rep_cat_cd');

		$sale_yn			= $request->input('sale_yn');
		$sale_type			= $request->input('sale_type');
		$sale_rate			= $request->input('sale_rate');
		$sale_price			= str_replace(',', '', $request->input('sale_price', 0));
		$sale_dt_yn			= $request->input('sale_dt_yn');
		$sale_s_dt			= $request->input('sale_s_dt') . ' ' . $request->input('sale_s_dt_tm') . ':00:00';
		$sale_e_dt			= $request->input('sale_e_dt') . ' ' . $request->input('sale_e_dt_tm') . ':00:00';

		try {
			DB::beginTransaction();

			//전시카테고리
			if( $d_category != "" ){
				$d_category_arr  = explode(',',$d_category);
				$i =0;
				$cat_type = "DISPLAY";

				//카테고리 전체 삭제
				$this->delete_category($cat_type, $goods_no);

				foreach( $d_category_arr  as $d_cat ){
					if( $i > 0 ){
						// 카테고리 등록
						$this->insert_category($cat_type, $d_cat, $goods_no, $goods_sub);
					}

					$i++;
				}
			}

			//용도카테고리
			if( $u_category != "" ){
				$u_category_arr  = explode(',',$u_category);
				$i =0;
				$cat_type = "ITEM";

				//카테고리 전체 삭제
				$this->delete_category($cat_type, $goods_no);

				foreach( $u_category_arr  as $u_cat ){
					if( $i > 0 ){
						// 카테고리 등록
						$this->insert_category($cat_type, $u_cat, $goods_no, $goods_sub);
					}

					$i++;
				}
			}

			// 배송비 설정 - 쇼핑몰
			if( $dlv_fee_cfg == "S" ){
				$bae_yn	= "Y";
				$baesong_price	= $cfg_dlv_fee;
			}

			// 적립금 계산 - 쇼핑몰
			if( $point_cfg = "S" ){
				$point_yn	= "Y";
				$point		= $price * $cfg_order_point_ratio / 100;
			}

			// 세일관리
			if($sale_yn !== "Y") {
				$sale_type = '';
				$sale_dt_yn = 'N';
				$sale_price = 0;
				$price = $normal_price;
				$sale_s_dt = '0000-00-00 00:00:00';
				$sale_e_dt = '0000-00-00 00:00:00';
			}
			$sale_set = '';
			$sale_set .= 'sale_yn = "'.$sale_yn.'",';
			$sale_set .= 'sale_type = "'.$sale_type.'",';
			$sale_set .= 'sale_price = "'.$sale_price.'",';
			$sale_set .= 'sale_dt_yn = "'.$sale_dt_yn.'",';
			$sale_set .= 'sale_s_dt = "'.$sale_s_dt.'",';
			$sale_set .= 'sale_e_dt = "'.$sale_e_dt.'",';

			$com_id = Auth('partner')->user()->com_id;
			$md_sql = " 
					 select
					     md_id,
					     md_nm
					 from 
					     company
 					 where
 					     com_id = '$com_id'
					";
			
			$md_info = DB::selectOne($md_sql);
			
			$md_id = $md_info->md_id !== null ? $md_info->md_id : Auth('partner')->user()->com_id;
			$md_nm = $md_info->md_nm !== null ? $md_info->md_nm : Auth('partner')->user()->name;
			
			$query	= /** @lang text */
				"
					update goods
						set
							head_desc			= '".$head_desc."',
							goods_nm			= '".$goods_nm."',
							goods_nm_eng		= '".$goods_nm_eng."',
							ad_desc				= '".$ad_desc."',
							brand				= '".$brand."',
							sale_stat_cl		= '".$sale_stat_cl."',
							style_no			= '".$style_no."',
							goods_type			= '".$goods_type."',
							com_id				= '".$com_id."',
							com_type			= '".$com_type."',
							make				= '".$make."',
							org_nm				= '".$org_nm."',
							goods_memo			= '".$goods_memo."',
							price				= '".$price."',
							tax_yn				= '".$tax_yn."',
							md_id				= '".$md_id."' , 
							md_nm				= '".$md_nm."',
							baesong_info		= '".$baesong_info."',
							baesong_kind		= '".$baesong_kind."',
							dlv_pay_type		= '".$dlv_pay_type."',
							dlv_fee_cfg			= '".$dlv_fee_cfg."',
							bae_yn				= '".$bae_yn."',
							baesong_price		= '".$baesong_price."',
							goods_location		= '".$goods_location."',
							point_cfg			= '".$point_cfg."',
							point_yn			= '".$point_yn."',
							point_unit			= '".$point_unit."',
							point				= '".$point."',
							rep_cat_cd			= '".$rep_cat_cd."',
							new_product_type	= '".$new_product_type."',
							new_product_day		= '".$new_product_day."',
							is_unlimited		= '".$is_unlimited."',
							is_option_use		= '".$is_option_use."',
							goods_cont			= '".$goods_cont."',
							spec_desc			= '".$spec_desc."',
							baesong_desc		= '".$baesong_desc."',
							opinion				= '".$opinion."',
							related_cfg			= '".$related_cfg."',
							opt_kind_cd			= '".$opt_kind_cd."',
							restock_yn			= '".$restock_yn."',
							goods_sh			= '".$goods_sh."',
							admin_id			= '".$id."',
							admin_nm			= '".$name."',
							$sale_set
							upd_dm				= NOW()
					where
						goods_no = '".$goods_no."'
					limit 1
			";

			$result = DB::update($query);

			//상품의 상태가 품절 및 품절(수동)일 경우 전시순서 변경 - 작업안함
			//도매그룹 판매가격 설정 - 작업안함
			//상품컬러 - 작업안함

			//상품 변경 로그 등록
			$sql	= "
				insert into goods_modify_hist (
					goods_no, goods_sub, style_no, upd_date, sale_stat_cl, price, margin, wonga
					, head_desc, memo, id, regi_date
				) values (
					'$goods_no', '$goods_sub', '$style_no', now(), '$sale_stat_cl', '$price', '$margin', '$wonga'
					, '$head_desc', '상품정보수정', '$id', now()
				)
			";
			DB::insert($sql);


			// 재고 수정
			/*
			$q_query = "
				update goods_summary
				set
					good_qty = '".$qty."',
					wqty = '".$wqty."'
				where
					goods_no = '".$goods_no."'
					limit 1
			";

			$q_result = DB::update($q_query);
			*/

			DB::commit();
			$msg = "저장되었습니다.";
		} catch(Exception $e){
			// dd($e);
			DB::rollback();
			$code_status = 500;
			$msg = "저장 중 에러가 발생했습니다. 잠시 후 다시 시도해주세요.";
		}

		return response()->json($goods_no, $code_status);
	}

	private function get_coupon_info($goods_no, $price)
	{
		$query = /** @lang text */
			"  select
					coupon_no, coupon_nm,
					date_format(use_fr_date,'%Y.%m.%d') as use_fr_date,
					date_format(use_to_date,'%Y.%m.%d') use_to_date,
					use_yn,
					CASE
						WHEN coupon_apply = 'AG' THEN '전체상품'
						WHEN coupon_apply = 'SC' THEN '대표카테고리'
						WHEN coupon_apply = 'SG' THEN '상품'
					END	as coupon_apply,
					'$price' as price
					, coupon_amt_kind, coupon_amt, coupon_per
					, if(coupon_amt_kind = 'W',coupon_amt,round(coupon_per/100 * $price)) as coupon_price
					, $price - if(coupon_amt_kind = 'W',coupon_amt,round(coupon_per/100 * $price)) as coupon_applied_price
				from coupon
				where coupon_no in (
					select a.coupon_no
					from coupon_cat a
						inner join category_goods b on a.d_cat_cd = b.d_cat_cd and b.cat_type = 'DISPLAY'
					where b.goods_no = '$goods_no'
					union
					select coupon_no
					from coupon_goods
					where goods_no = '$goods_no'
				) and use_yn = 'Y'
				order by coupon_no desc
		";
		$result = DB::select($query);

		return $result;

	}

	public function delete_coupon($goods_no, Request $request) // 해당 상품에 적용된 해당 쿠폰정보 삭제
	{
		$code_status = 200;
		$msg = "";

		$goods_sub = $request->input('goods_sub');
		$coupon_no = $request->input('coupon_no');

		try {
			DB::beginTransaction();

			$sql	= "
				delete from coupon_goods
				where goods_no = :goods_no and goods_sub = :goods_sub and coupon_no = :coupon_no;
			";

			DB::delete($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'coupon_no' => $coupon_no]);
			DB::commit();
			$msg = "삭제되었습니다.";
		} catch(Exception $e){
			DB::rollback();
			$code_status = 500;
			$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
		}

		return response()->json(['code' => $code_status, 'msg' => $msg], $code_status);
	}

	public function update_selected(Request $request) // 선택된 상품목록 수정사항 업데이트
	{
		$data = $request->input('data');
		$user = array(
			"id" => Auth('partner')->user()->id,
			"name" => Auth('partner')->user()->name
		);

		$code = 200;
		$msg = '';

		try {
			DB::transaction(function () use (&$result, $user, $data) {
				$prd = new Product($user);
				for($i=0;$i<count($data);$i++){
					$row = $data[$i];
					$prd->Edit( $row['goods_no'], $row );
				}
			});
			$msg = "변경사항이 저장되었습니다.";
		} catch(Exception $e){
			$code = 500;
			$msg = "변경사항 저장에 실패했습니다.";
		}

		return response()->json(["code" => $code, "msg" => $msg], $code);
	}

	private function get_history_modify($goods_no)
	{
		$query = "
			select
				date_format(a.upd_date,'%y.%m.%d %h:%i:%s') as upd_date,
				a.memo, a.head_desc, a.price, a.wonga, a.margin, a.id, b.name
			from goods_modify_hist a
				inner join mgr_user b on a.id = b.id
			where a.goods_no = $goods_no
			order by a.hist_no desc
		";

		$result = DB::select($query);

		return $result;
	}

	private function get_cat_nm($cat_code){

		$query = "
			select group_concat(d_cat_nm order by d_cat_cd  separator ' > ') as full_nm
			from category
			where cat_type = 'DISPLAY'
				and instr('$cat_code', d_cat_cd) = 1
		";
		$result = DB::select($query);

		return $result[0]->full_nm;

	}

	private function get_goods_related_info($goods_no){
		$query = "
			select
				replace(b.img,'a_500', 'a_55') as img,
				c.opt_kind_nm,
				d.brand_nm,
				b.goods_nm,
				e.code_val as goods_stat,
				b.price,
				a.r_goods_no,
				a.r_goods_sub
			from goods_related a
				inner join goods b on a.r_goods_no = b.goods_no and a.r_goods_sub = b.goods_sub
				inner join opt c on b.opt_kind_cd = c.opt_kind_cd and c.opt_id = 'K'
				inner join brand d on b.brand = d.brand
				inner join code e on e.code_kind_cd = 'G_GOODS_STAT' and e.code_id = b.sale_stat_cl
			where a.goods_no = '$goods_no'
		";

		$result = DB::select($query);

		return $result;
	}

	private function get_planing_list($goods_no, $goods_sub){ // 기획전 정보
		$ar_planning_list = array();

		$query = "
			select
				b.title, b.plan_show, b.plan_date_yn, b.start_date, b.end_date, b.no, a.d_cat_cd
			from category_goods a
				inner join planning b on a.cat_type = 'PLAN' and b.p_no = a.d_cat_cd
			where a.goods_no = '$goods_no' and a.goods_sub = '$goods_sub'
		";
		$result = DB::select($query);

		return $result;
	}

	public function delete_planing($goods_no, Request $request) { // 기획전에서 해당 상품 삭제

		$code_status = 200;
		$msg = "";

		$goods_sub = $request->input('goods_sub');
		$d_cat_cd = $request->input('d_cat_cd');

		try {
			DB::beginTransaction();

			$sql	= "
				delete from category_goods
				where goods_no = :goods_no AND goods_sub = :goods_sub AND d_cat_cd = :d_cat_cd;
			";
			DB::delete($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'd_cat_cd' => $d_cat_cd]);

			DB::commit();
			$msg = "삭제되었습니다.";
		} catch(Exception $e){
			DB::rollback();
			$code_status = 500;
			$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
		}

		return response()->json(['code' => $code_status, 'msg' => $msg], $code_status);

	}

	private function get_opt_cd_list(){
		$query = "select opt_kind_cd as 'name', opt_kind_nm as 'value' from opt where opt_id = 'K' and use_yn = 'Y' order by opt_seq";

		$result = DB::select($query);

		return $result;
	}

	private function get_md_list(){
		$query = "select id as name, concat(ifnull(name, ''),' (',id,')') as value from mgr_user where md_yn = 'Y' order by name";

		$result = DB::select($query);

		return $result;
	}

	private function get_com_info($com_id){

		$query = "
			select a.com_nm, a.com_id, a.com_type, a.margin_type, a.pay_fee, a.baesong_kind, a.baesong_info, a.md_nm, b.id as md_id,
				ifnull(a.dlv_policy,'S') as dlv_policy, a.dlv_amt, a.free_dlv_amt_limit
			from company a
				left outer join mgr_user b on a.md_nm = b.name and b.md_yn = 'Y'
			where a.com_id = '$com_id'
		";


		$result = DB::select($query);

		if($result[0]->dlv_policy == "S"){
			$fee_query = "
				select value, mvalue from conf where type = 'delivery' and name = 'base_delivery_fee'
			";

			$fee = DB::select($fee_query);

			$amt_query = "
				select value, mvalue from conf where type = 'delivery' and name = 'free_delivery_amt'
			";

			$amt = DB::select($amt_query);


			$result[0]->dlv_amt = $fee[0]->value;
			$result[0]->free_dlv_amt_limit = $amt[0]->value;
		}

		return $result[0];
	}


	function get_config_value( $type, $name , $default = "" ){


		$query = "
				select value, mvalue from conf where type = '$type' and name = '$name'
			";
		$result = DB::select($query);
		if(empty($result[0])){
			$val = $result[0]->value;
			$mval = $result[0]->mvalue;

			$value = $this->get_mvalue($val, $mval);
			return $value;
		} else {
			return $default;
		}

	}

	function get_mvalue($value, $mvalue)
	{
		if($this->mobile == "Y" && $mvalue != ""){
			return $mvalue;
		} else {
			return $value;
		}
	}

	function delete_category($cat_type, $goods_no){
		$query = "  delete from  category_goods
					where
						goods_no = '".$goods_no."'
						and cat_type ='".$cat_type."'
				";

		//echo($query);
		DB::select($query);
	}

	function insert_category($cat_type, $d_cat, $goods_no, $goods_sub){

		$id		= Auth('partner')->user()->id;
		$name	= Auth('partner')->user()->name;

		list($cat, $seq, $disp_yn) = explode("|", $d_cat);

		/*
		$str_arr = explode(" ",$d_cat);

		$cnt = count($str_arr);
		$d_cat_cd = $str_arr[($cnt-1)];

		$disp_yn = str_replace("]", "",str_replace("[", "", $str_arr[($cnt-2)]));
		$seq = str_replace(")", "",str_replace("(", "", $str_arr[($cnt-3)]));
		*/

		$where = [
			'cat_type'	=> $cat_type,
			'd_cat_cd'	=> $cat,
			'goods_no'	=> $goods_no,
			'goods_sub'	=> $goods_sub
		];

		$values = [
			'disp_yn'	=> $disp_yn,
			'regi_date'	=> now(),
			'seq'		=> $seq,
			'admin_id'	=> $id,
			'admin_nm'	=> $name
		];

		$row_cnt = DB::table('category_goods')
			->where($where)
			->count();

		if ($row_cnt > 0) return false;

		$data = array_merge($where, $values);

		DB::table('category_goods')
			->insert($data);

		return true;
	}

	/*
	***
	상품옵션관리 옵션 추가&삭제 / 옵션품목 추가&삭제&저장&입고
	***
	*/

	// 옵션구분 등록
	public function add_option_kind(Request $req, $goods_no) {

		$goods_sub = 0;
		$opt_type = $req->input("opt_type");
		$opt_type_nm = $req->input("opt_type_nm");
		$opt_required_yn = $req->input("opt_required_yn");
		$opt_use_yn = $req->input("opt_use_yn");
		$basic_count = $req->input("basic_count");

		$code = 200;
		$msg = '';

		$sql = "
			select
				count(*) as seq
			from goods_option
			where goods_no = ':goods_no' and goods_sub = ':goods_sub'
				and type = '${opt_type}' and name = '${opt_type_nm}'
		";

		$result = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_sub' =>  $goods_sub]);
		$seq = $result->seq;
		$seq = $seq + 1;

		try {
			$sql = "
				insert into goods_option (
					goods_no, goods_sub, type, name, required_yn, use_yn, seq, option_no, rt
				) values (
					'$goods_no', '$goods_sub', '$opt_type', '$opt_type_nm', '$opt_required_yn', '$opt_use_yn', '$seq', 0, now()
				)
			";
			DB::insert($sql);

			/**
			 * 2단 전환시 샘플 옵션 등록 (기존 옵션구분이 하나인 경우)
			 */
			if ($basic_count == 1) {
				$sql = "
					select goods_opt, opt_name, goods_no
					from goods_summary
					where goods_no = :goods_no and use_yn = 'Y'
					order by seq
				";
				$result = DB::select($sql, ['goods_no' => $goods_no]);

				foreach ($result as $idx => $item) {
					$opt_value = $item->goods_opt;
					$sql = "
						delete from goods_summary
						where goods_no = :goods_no and goods_opt like '%$opt_value%'
					";
					DB::delete($sql, ["goods_no" => $goods_no]);
				}

				/**
				 * 옵션관리 팝업 api의 저장 기능을 활용하여 2단 옵션의 초기 샘플 옵션품목 값 생성
				 * (saveBasicOptions 데이터 형식에 맞게 가공)
				 */
				$count = count($result);
				$arr = ['opt_list' => []];
				foreach ($result as $idx => $item) {
					$no = $idx + 1;
					$arr['opt_list'][$idx] = (array) $item;
				}
				foreach ($result as $idx => $item) {
					$no = $idx + 1;
					$idx = $idx + $count;
					$item->goods_opt = "옵션${no}";
					$item->opt_name = "${opt_type_nm}";
					$arr['opt_list'][$idx] = (array) $item;
				}

				/**
				 * 기존 단일옵션들이 반영된 2단옵션 초기 품목 저장
				 */
				$request = $req->merge($arr);
				$this->saveBasicOptions($request, $goods_no);
			}

			$msg = "옵션명이 등록되었습니다.";
		} catch(Exception $e){
			// dd($e);
			$code = 500;
			$msg = "등록 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요.";
		}

		return response()->json(['code' => $code, 'msg' => $msg, 'data' => $this->_get($goods_no)], $code);
	}

	// 옵션구분 삭제
	public function del_option_kind(Request $req, $goods_no) {
		$goods_sub = $req->input("goods_sub");
		$goods_type = $req->input("goods_type");
		$del_id_list = $req->input("del_id_list") != null ? explode(",", $req->input("del_id_list")) : [];
		$is_option_use = $req->input("is_option_use");

		$code = 200;
		$msg = '';

		try {
			DB::beginTransaction();

			foreach($del_id_list as $opt_no) {
				// 1. 기본옵션여부
				$sql = "
					select type
					from goods_option
					where no = :opt_no and goods_no = :goods_no and goods_sub = :goods_sub
				";

				$row = DB::selectOne($sql, ['opt_no' => $opt_no, 'goods_no' => $goods_no, 'goods_sub' => $goods_sub]);

				if($row->type === "basic") {
					// 1-1. 기본옵션인 경우
						// 2. 매입상품인 경우 goods_good 삭제
						if($goods_type === "S") {
							// * 삭제 전 재고차감 처리 작업필요 ??

							// goods_good 삭제
							$sql = "
								delete from goods_good
								where goods_no = :goods_no and goods_sub = :goods_sub
							";
							DB::delete($sql, ['goods_no' => $goods_no, 'goods_sub' =>  $goods_sub]);
						}

						// 3. 재고 삭제 (goods_summary)
						$sql = "
							delete from goods_summary
							where goods_no = :goods_no and goods_sub = :goods_sub
						";
						DB::delete($sql, ['goods_no' => $goods_no, 'goods_sub' =>  $goods_sub]);

				} else if($row->type === "extra") {
					// 1-2. 추가옵션인 경우
					$sql = "
						delete from options
						where option_no = :option_no
					";
					DB::delete($sql, ['option_no' => $opt_no]);
				}

				// 4. 옵션 삭제 (goods_option)
				$sql = "
					delete from goods_option
					where no = :no and goods_no = :goods_no and goods_sub = :goods_sub
				";
				DB::delete($sql, ['no' => $opt_no, 'goods_no' => $goods_no, 'goods_sub' => $goods_sub]);

			}


			// 5. 옵션 사용여부 설정
			if($is_option_use != null) {
				$sql = "
					update goods
					set is_option_use = '$is_option_use'
					where goods_no = '$goods_no'
					limit 1
				";
				DB::update($sql);

				$user	= Auth('partner')->user();
				$id		= Auth('partner')->user()->id;
				$name	= Auth('partner')->user()->name;

				if($is_option_use == 'N') {
					// 기본 옵션 등록
					$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
						insert into goods_option (
							goods_no, goods_sub, type, name, required_yn, use_yn, seq, option_no, rt, ut
						) values (
							'$goods_no', '$goods_sub', 'basic', 'NONE', 'Y', 'Y', '0', null, now(), now()
						)
					";
					DB::insert($sql);

					// 기본 재고 등록
					$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
						insert into goods_summary (
							goods_no, goods_sub, opt_name, goods_opt, opt_price, good_qty, wqty,
							soldout_yn, use_yn, seq, rt, ut, bad_qty, last_date
						) values	(
							'$goods_no', '$goods_sub', 'NONE', 'none', '0', '0', '0',
							'N', 'Y', '0', now(), now(), 0, now()
						)
					";
					DB::insert($sql);

					$sql = "
						select wonga
						from goods
						where goods_no = :goods_no
					";
					$row = DB::selectOne($sql,['goods_no' => $goods_no]);
					$wonga = $row->wonga;

					if( $goods_type == "S" ){
						// 기본 매입상품 처리
						$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
							insert into goods_good (
							goods_no, goods_sub, goods_opt, opt_type, opt_price, wonga, qty, invoice_no, init_qty, regi_date
							) values (
							'$goods_no', '$goods_sub', 'none', null, 0, '$wonga', '0', '', '0', now()
							)
						";
						DB::insert($sql);
					}
				}
			}

			DB::commit();
			$msg = "삭제되었습니다.";
		} catch(Exception $e){
			DB::rollback();
			$code = 500;
			 $msg = $e->getMessage();
			//$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시 시도해주세요.";
		}

		return response()->json(['code' => $code, 'msg' => $msg, 'data' => $this->_get($goods_no)], $code);
	}

	// 옵션 관리 - 옵션 조회 ( 기본 )
	public function getBasicOptions($goods_no) {

		$code = 200;
		$opt_kinds = [];
		$result = [];
		$is_single = $this->checkBasicOptKindIsSingle($goods_no);

		try {

			$sql = "
				select name
				from goods_option
				where goods_no = :goods_no and use_yn = 'Y' and type = 'basic'
				order by no
			";

			$opt_kinds = DB::select($sql, ['goods_no' => $goods_no]);

			if ($is_single) { // 기본 싱글 옵션인 경우

				$sql = "
					select goods_opt, opt_name, goods_no
					from goods_summary
					where goods_no = :goods_no and use_yn = 'Y'
					order by seq
				";

				$result = DB::select($sql, ['goods_no' => $goods_no]);

			} else { // 기본 2단 옵션인 경우

				$sql = "
					select distinct substring_index(goods_opt, '^', :index) as goods_opt, substring_index(opt_name, '^', :index2) as opt_name, goods_no
					from goods_summary
					where goods_no = :goods_no and use_yn = 'Y'
					order by seq
				";

				$result = array_merge(DB::select($sql, ['index' => 1, 'index2' => 1, 'goods_no' => $goods_no]), DB::select($sql, ['index' => -1, 'index2' => -1, 'goods_no' => $goods_no]));

			}

		} catch(Exception $e) {
			$code = 500;
		}

		return response()->json([
			"code" => $code,
			"head" => array(
				"total" => count($result),
				'opt_kinds' => $opt_kinds,
			),
			"body" => $result,
		]);
	}

	// 옵션품목 저장 ( 유형 - 기본 )
	public function saveBasicOptions(Request $request, $goods_no) {

		$code = 200;
		$msg = '';

		$collection = collect($request->input("opt_list", []));
		$grouped = $collection->mapToGroups(function($item, $key) {
			return [$item['opt_name'] => $item['goods_opt']];
		});

		$keys = $grouped->keys()->all();

		/**
		 * 전달받은 옵션구분 항목이 1개인 경우
		 */
		if (count($keys) == 1) {

			$opt_kind_names = [];
			$sql =
				" select `name` from goods_option where goods_no = :goods_no and `type` = 'basic' order by seq";
			$rows = DB::select($sql,['goods_no' => $goods_no]);
			foreach ($rows as $row) {
				$name = $row->name;
				array_push($opt_kind_names, $name);
			}

			/**
			 * 2단 옵션인데 하나의 옵션 구분만 선택하는 경우 더미데이터 생성
			 */
			if (count($opt_kind_names) == 2) {

				$opt_list = $request->input("opt_list", []);

				// 더이데이터 옵션 구분 이름 설정
				$opt_name = "";
				if ($opt_kind_names[0] == $opt_list[0]['opt_name']) {
					$opt_name = $opt_kind_names[1];
				} else if ($opt_kind_names[1] == $opt_list[0]['opt_name']) {
					$opt_name = $opt_kind_names[0];
				};

				// 더미데이터 옵션 추가
				$c_count = $collection->count();
				for ($i = 0; $i < $c_count; $i++) {
					$idx = $i + $c_count;
					$no = $i + 1;
					$opt_list[$idx] = ['opt_name' => $opt_name, 'goods_opt' => "옵션${no}"];
				}

				// 저장 준비
				$collection = collect($opt_list);
				$grouped = $collection->mapToGroups(function($item, $key) {
					return [$item['opt_name'] => $item['goods_opt']];
				});
				$opt_name = $opt_kind_names[0] . "^" . $opt_kind_names[1];

				$arr = $grouped->map(function($item) {
					$arr = $item->all();
					return $arr;
				})->all();

				$opt1 = $arr[$opt_kind_names[0]];
				$opt2 = $arr[$opt_kind_names[1]];

				$goods_opts = [];
				foreach ($opt1 as $opt1_val) {
					foreach ($opt2 as $opt2_val) {
						array_push($goods_opts, $opt1_val . "^" . $opt2_val);
					}
				}

			} else {

				/**
				 * 단일 옵션인 경우 저장 준비
				 */
				$opt_name = $keys[0];

				$arr = $grouped->map(function($item) {
					$arr = $item->all();
					return $arr;
				})->values()->all();

				$goods_opts = $arr[0];

			}

			/**
			 * 옵션 저장
			 */
			try {
				DB::beginTransaction();
				$sql = "
					delete from `goods_summary`
					where goods_no = :goods_no
					and opt_name = :opt_name
				";
				DB::delete($sql, ['goods_no' => $goods_no, 'opt_name' => $opt_name]);
				for ($i=0; $i<count($goods_opts); $i++) {
					$goods_opt = $goods_opts[$i];
					$sql = "
						insert into goods_summary
							(goods_no, goods_sub, opt_name, goods_opt, opt_price, soldout_yn, use_yn, good_qty, wqty, bad_qty, rt, ut, last_date, seq)
						values (:goods_no, :goods_sub, :opt_name, :goods_opt, 0, 'N', 'Y', 0, 0, 0, NOW(), NOW(), DATE_FORMAT(NOW(),'%Y-%m-%d'), $i)
					";
					DB::insert($sql, ['goods_no' => $goods_no, 'goods_sub' => 0, 'goods_opt' => $goods_opt, 'opt_name' => $opt_name]);
				}
				DB::commit();
				$msg = "저장되었습니다.";
			} catch (Exception $e) {
				// dd($e->getMessage());
				DB::rollBack();
				$code = 500;
				$msg = "저장중 에러가 발생했습니다. 잠시 후 다시 시도해주세요.";
			}
			return response()->json(['code' => $code, 'msg' => $msg], $code);

		} else if (count($keys) == 2) {

			/**
			 * 전달받은 옵션구분 항목이 2개인 경우
			 */
			$opt_kind_names = [];
			$sql =
				" select `name` from goods_option where goods_no = :goods_no and TYPE = 'basic' order by seq";
			$rows = DB::select($sql,['goods_no' => $goods_no]);
			foreach($rows as $row) {
				$name = $row->name;
				array_push($opt_kind_names, $name);
			}

			$opt_name = $opt_kind_names[0] . "^" . $opt_kind_names[1];

			$arr = $grouped->map(function($item) {
				$arr = $item->all();
				return $arr;
			})->all();

			$opt1 = $arr[$opt_kind_names[0]];
			$opt2 = $arr[$opt_kind_names[1]];

			$goods_opts = [];
			foreach ($opt1 as $opt1_val) {
				foreach ($opt2 as $opt2_val) {
					array_push($goods_opts, $opt1_val . "^" . $opt2_val);
				}
			}

			/**
			 * 옵션 저장
			 */
			try {
				DB::beginTransaction();
				$sql = "
					delete from `goods_summary`
					where goods_no = :goods_no
					and opt_name = :opt_name
				";
				DB::delete($sql, ['goods_no' => $goods_no, 'opt_name' => $opt_name]);
				for ($i=0; $i<count($goods_opts); $i++) {
					$goods_opt = $goods_opts[$i];
					$sql = "
						insert into goods_summary
							(goods_no, goods_sub, opt_name, goods_opt, opt_price, soldout_yn, use_yn, good_qty, wqty, bad_qty, rt, ut, last_date, seq)
						values (:goods_no, :goods_sub, :opt_name, :goods_opt, 0, 'N', 'Y', 0, 0, 0, NOW(), NOW(), DATE_FORMAT(NOW(),'%Y-%m-%d'), $i)
					";
					DB::insert($sql, ['goods_no' => $goods_no, 'goods_sub' => 0, 'goods_opt' => $goods_opt, 'opt_name' => $opt_name]);
				}
				DB::commit();
				$msg = "저장되었습니다.";
			} catch (Exception $e) {
				// dd($e->getMessage());
				DB::rollBack();
				$code = 500;
				$msg = "저장중 에러가 발생했습니다. 잠시 후 다시 시도해주세요.";
			}

			return response()->json(['code' => $code, 'msg' => $msg], $code);

		}

	}

	// 옵션 품목 삭제 ( 유형 - 기본 )
	public function deleteBasicOptions(Request $request, $goods_no)
	{
		$code = 200;
		$msg = '';
		$data = $request->input("del_opt_list", []);
		try {
			DB::beginTransaction();
			for ( $i = 0; $i < count($data); $i++ ) {
				$arr = $data[$i];
				$opt_value = $arr["goods_opt"];
				$sql = "
					delete from goods_summary
                	where goods_no = :goods_no and goods_opt like '%$opt_value%'
				";
				DB::delete($sql, ["goods_no" => $goods_no]);
			}
			DB::commit();
			$msg = "삭제되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시 시도해주세요.";
		}
		return response()->json(['code' => $code, 'msg' => $msg], $code);
	}

	public function getBasicOptsMatrix($goods_no)
	{
		$is_single = $this->checkBasicOptKindIsSingle($goods_no);

		$opt	= ['opt1' => [], 'opt2' => []];
		$sql	=
			" select count(*) as tot from goods_option where goods_no = :goods_no and `type` = 'basic' order by seq";
		$row	= DB::selectOne($sql,['goods_no' => $goods_no]);
		$opt_kind_cnt	= $row->tot;

		$opt_kind_names = [];
		$sql =
			" select `name` from goods_option where goods_no = :goods_no and `type` = 'basic' order by seq";
		$rows = DB::select($sql,['goods_no' => $goods_no]);
		foreach($rows as $row) {
			$name = $row->name;
			array_push($opt_kind_names, $name);
		}

		$sql =
			" select distinct(substring_index(goods_opt, '^', :index)) as opt_nm from goods_summary where goods_no = :goods_no and use_yn = 'Y' order by seq";

		if ($opt_kind_cnt > 0) {
			$opt['opt1'] = DB::select($sql,['goods_no' => $goods_no, 'index' => 1]);
			if ($opt_kind_cnt == 2) $opt['opt2'] = DB::select($sql,['goods_no' => $goods_no, 'index' => -1]);
		}

		return response()->json(['code' => 200, 'opt_matrix' => $opt, 'opt_kind_names' => $opt_kind_names, 'is_single' => $is_single]);
	}

	public function updateBasicOptsData(Request $request, $goods_no)
	{
		$data = $request->input("data", []);
		try {
			foreach ($data as $item) {
				$opt_price = $item["opt_price"];
				$opt_memo = $item["opt_memo"] ? $item["opt_memo"] : "";
				$good_qty = $item["good_qty"];
				$goods_opt = $item["goods_opt"];
				$sql = "
					update goods_summary set
					opt_price = :opt_price, opt_memo = :opt_memo,
					good_qty = :good_qty
					where goods_no = :goods_no
					and goods_sub = '0'
					and goods_opt = :goods_opt
				";
				DB::update($sql, [
					'opt_price' => $opt_price, 'opt_memo' => $opt_memo, 'good_qty' => $good_qty,
					'goods_no' => $goods_no, 'goods_opt' => $goods_opt
				]);
			}
			DB::commit();
			return response()->json(['code' => 200, 'msg' => "저장되었습니다."]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(['code' => 500, 'msg' => "저장중 에러가 발생했습니다. 잠시 후 다시 시도해주세요."]);
		}
	}

	public function checkBasicOptKindIsSingle($goods_no)
	{
		$is_single = false;
		$sql = "
			select name
			from goods_option
			where goods_no = :goods_no and use_yn = 'Y' and type = 'basic'
			order by no
		";
		$opt_kinds = DB::select($sql, ['goods_no' => $goods_no]);
		if (count($opt_kinds) < 2) $is_single = true;
		return $is_single;
	}

	// 옵션 관리 - 옵션 조회 ( 추가 )
	public function getExtraOptions(Request $request)
	{
		$option_no = $request->input('option_no');

		$sql = "
			select *
			from options
			where option_no = :option_no
			order by seq
		";
		$result = DB::select($sql, ['option_no' => $option_no]);

		return $result;
	}

	public function updateExtraOptsData(Request $request)
	{
		$code = 200;
		$data = $request->input("data", []);
		$opt_no = $request->input("opt_no");

		try {
			DB::transaction(function () use ($data, $opt_no) {

				$sql = "
					delete from `options`
					where option_no = :option_no
				";
				DB::delete($sql, ["option_no" => $opt_no]);

				for ( $i = 0; $i < count($data); $i++ ) {

					$arr = $data[$i];
					$option_no = $arr["option_no"];
					$name = $arr["name"];
					$option = isset($arr["option"]) ? $arr["option"] : "";

					$sql = "
						select *, count(*) as count
						from `options`
						where option_no = :option_no and name = :name and `option` = :option
					";
					$result = DB::selectOne($sql, ["option_no" => $option_no, 'name' => $name, 'option' => $option]);

					if ($result->count < 1) {
						$arr = $data[$i];
						DB::table("options")->insert([
							'option_no' => $option_no,
							'name' => $name,
							'option' => $option,
							'price' => $arr["price"],
							'qty' => $arr["qty"],
							'wqty' => $arr["wqty"],
							'soldout_yn' => $arr["soldout_yn"],
							'use_yn' => "Y",
							'seq' => $i,
							'rt' => DB::raw("now()"),
							'ut' => DB::raw("now()")
						]);
					}

				}
			});
		} catch(Exception $e){
			// dd($e->getMessage());
			$code = 500;
		}
		return response()->json(['code' => $code]);
	}

	public function stock($goods_no = 0)
	{
		$wonga = 0;
		$sql = "
			select wonga from goods
			where goods_no = :goods_no and goods_sub = '0'
		";
		$result = DB::select($sql, ['goods_no' => $goods_no]);
		if (count($result) > 0) $wonga = $result[0]->wonga;

		$values = $this->getBasicOptsMatrix($goods_no)->getOriginalContent();
		$values['sdate'] = date("Y-m-d");
		$values['goods_no'] = $goods_no;
		$values['invoice_no'] = date("Ymd");
		$values['wonga'] = Lib::cm($wonga);
		$values['locs'] = SLib::getCodes('G_STOCK_LOC')->all();

		// dd($values);

        return view( Config::get('shop.partner.view') . '/product/prd01_stock', $values);
	}

	public function stockIn(Request $request)
	{
		// 설정 값 얻기
		$conf = new Conf();
		$cfg_domain = $conf->getConfigValue("shop", "domain");

		$user = [
			'id' => Auth('partner')->user()->id,
			'name' => Auth('partner')->user()->name
		];

		$inputs = $request->all();

		try {
			DB::transaction(function () use (&$inputs, $user, $cfg_domain) {

				$goods_no = $inputs['goods_no'];
				$invoice_no = $inputs['invoice_no'];
				$wonga = $inputs['wonga'];
				$loc = $inputs['loc'];
				$data = $inputs['data'];

				$jaego = new Jaego($user); // 재고 클래스 호출
				$jaego->SetLoc($loc);

				foreach ($data as $row) {
					$qty = $row['qty'];
					$opt = $row['opt'];
					$sql = "
						select distinct opt_name from goods_summary where goods_no = :goods_no and goods_sub = '0'
					";
					$result = DB::selectOne($sql, ['goods_no' => $goods_no]);
					$opt_name = $result->opt_name;

					$check = $jaego->Plus(array(
						"type" => 1,
						"etc" => '',
						"qty" => $qty,
						"goods_no" => $goods_no,
						"goods_sub" => 0,
						"goods_opt" => $opt,
						"wonga" => $wonga,
						"invoice_no" => $invoice_no,
						"opt_name"	=>  $opt_name,
						"wonga_apply_yn" => "Y"
					));

					if (!$check) {
						return response()->json(['code'	=> '-1', 'cfg_domain' => $cfg_domain]);
					}
				}
			});
			return response()->json(['code'	=> '200', 'cfg_domain' => $cfg_domain]);
		} catch (\Exception $e) {
            // dd($e->getMessage());
			return response()->json(['code'	=> '500', 'cfg_domain' => $cfg_domain]);
		}
	}

	/*
    ***
    유사 상품 관리 관련
    ***
    */

	// 유사 상품 조회
	public function get_similar_goods(Request $request, $goods_no) {
		$goods_sub = $request->goods_sub;

		// 해당 상품의 유사그룹번호 조회
		$sql = "
			select ifnull(similar_no, 0) as similar_no
			from goods
			where goods_no = :goods_no and goods_sub = :goods_sub
		";
		$row = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub]);

		$sql = "
			select a.goods_no, a.goods_sub, a.similar_no, a.seq, a.admin_id, a.admin_nm, a.rt, b.goods_nm, b.make as brand, b.opt_kind_cd, b.img, b.price, c.com_nm, b.rep_cat_cd
			from goods_similar a
				left join goods b on a.goods_no = b.goods_no
				left outer join company c on c.com_id = b.com_id
			where a.similar_no = :similar_no
			order by a.seq asc
		";
		$rows = DB::select($sql, ['similar_no' => $row->similar_no]);

		foreach($rows as $row) {
			$cat_nm = $this->get_cat_nm($row->rep_cat_cd);
			$row->cat_nm = $cat_nm;
		}

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows),
            ),
            "body" => $rows
        ]);
	}

	// 유사 상품 등록
	public function save_similar_goods(Request $request, $no) {
		$code = 200;
		$msg = '';

		$admin_id = Auth('partner')->user()->id;
		$admin_nm = Auth('partner')->user()->name;
		$goods_sub = $request->goods_sub;
		$goods_no = $no;
		
		// 해당 상품 정보 조회
		$sql = "
			select ifnull(similar_no, 0) as similar_no, brand, com_id, opt_kind_cd, rep_cat_cd
			from goods
			where goods_no = :goods_no and goods_sub = :goods_sub
		";
		$row = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub]);

		$similar_no = $row->similar_no;
		$brand = $row->brand;
		$com_id = $row->com_id;
		$opt_kind_cd = $row->opt_kind_cd;
		$rep_cat_cd = $row->rep_cat_cd;
		$seq = 0;

		try {
		    DB::beginTransaction();

			// 그룹유무 검사
			if($similar_no === 0) {
				// 그룹이 없는 경우 -> 해당 상품번호를 그룹번호로 지정 후 유사 상품에 등록
				$similar_no = $goods_no;

				/*$sql = "
					insert into goods_similar (
						similar_no, goods_no, goods_sub, seq, admin_id, admin_nm, rt, ut
					) values (
						'$similar_no', '$goods_no', '$goods_sub', '$seq', '$admin_id', '$admin_nm', now(), now()
					)
				";
				DB::insert($sql);
				*/
				
				$sql = "
					update goods set
						similar_no = '$similar_no'
					where goods_no = '$goods_no' and goods_sub = '$goods_sub'
				";
				DB::update($sql);

				$seq++;
			} else {
				// 그룹이 있는 경우 -> 순서값 얹기
				$sql = "
					select ifnull(max(seq), 0)+1 as seq
					from goods_similar
					where similar_no = '$similar_no'
				";
				$row = DB::selectOne($sql);
				$seq = $row->seq;
			}

			// 유사상품 등록
			$add_goods = $request->input('add_goods', []);
			
			foreach($add_goods as $goods) {
				list($s_goods_no, $s_goods_sub) = explode("||", $goods);
				if($goods_no !== $s_goods_no) {
					
					$regist_prd_sql = "
						select brand, com_id, opt_kind_cd, rep_cat_cd
						from goods
						where goods_no = :goods_no and goods_sub = :goods_sub
					";

					$regist_prd_info = DB::selectOne($regist_prd_sql, [
						'goods_no' => $s_goods_no,
						'goods_sub' => $s_goods_sub,
					]);

					if($regist_prd_info->brand !== $brand) {
						throw new Exception("$s_goods_no 의 브랜드가 동일하지 않습니다.");
					}

					if($regist_prd_info->com_id !== $com_id) {
						throw new Exception("$s_goods_no 의 업체가 동일하지 않습니다.");
					}

					if($regist_prd_info->opt_kind_cd !== $opt_kind_cd) {
						throw new Exception("$s_goods_no 품목이 동일하지 않습니다.");
					}

					if($regist_prd_info->rep_cat_cd !== $rep_cat_cd) {
						throw new Exception("$s_goods_no 대표카테고리가 동일하지 않습니다.");
					}
					
					$sql = "
						select goods_no, goods_sub
						from goods
						where goods_no = :goods_no and goods_sub = :goods_sub
							and brand = :brand and com_id = :com_id and opt_kind_cd = :opt_kind_cd and rep_cat_cd = :rep_cat_cd
					";
					
					$row = DB::selectOne($sql, [
						'goods_no' => $s_goods_no,
						'goods_sub' => $s_goods_sub,
						'brand' => $brand,
						'com_id' => $com_id,
						'opt_kind_cd' => $opt_kind_cd,
						'rep_cat_cd' => $rep_cat_cd,
					]);

					if($row !== NULL) {
						// 이미 등록된 유사상품인지 조회
						$sql = "
							select count(*) as cnt
							from goods_similar
							where goods_no = :goods_no and goods_sub = :goods_sub
						";
						$row = DB::selectOne($sql, ['goods_no' => $s_goods_no, 'goods_sub' => $s_goods_sub]);
						$cnt = $row->cnt;

						if($cnt === 0) {
							// 기존 유사상품 데이터에 없을 경우 -> 추가
							$sql = "
								insert into goods_similar (
									similar_no, goods_no, goods_sub, seq, admin_id, admin_nm, rt, ut
								) values (
									'$similar_no', '$s_goods_no', '$s_goods_sub', '$seq', '$admin_id', '$admin_nm', now(), now()
								)
							";
							DB::insert($sql);
						} else {
							// 기존 유사상품 데이터에 있을 경우 -> 그룹번호 업데이트
							$sql = "
								update goods_similar set
									similar_no = '$similar_no',
									seq = '$seq',
									admin_id = '$admin_id',
									admin_nm = '$admin_nm',
									ut = now()
								where similar_no <> '$similar_no' and goods_no = '$s_goods_no' and goods_sub = '$s_goods_sub'
							";
							DB::update($sql);
						}

						$sql = "
							update goods set
								similar_no = '$similar_no'
							where goods_no = '$s_goods_no' and goods_sub = '$s_goods_sub'
						";
						DB::update($sql);

						$seq++;
					}
				}
			}

            DB::commit();
            $msg = '저장되었습니다.';
        } catch(Exception $e){
            DB::rollback();
            $code = 500;
			$msg = $e->getMessage();
        }

		return response()->json(['code' => $code, 'message' => $msg], $code);
	}

	// 유사 상품 삭제
	public function delete_similar_goods(Request $request, $goods_no) {
		$code = 200;
		$msg = '';

		$goods_sub = $request->goods_sub;
		$similar_no = 0;
		$chg_similar_no = 0;
		$chg_similar_yn = "N";

        try {
            DB::beginTransaction();

			$del_goods = $request->del_goods;

			foreach($del_goods as $goods) {
				list($s_goods_no, $s_goods_sub, $s_similar_no) = explode("||", $goods);
				$similar_no = $s_similar_no;

				if($s_similar_no === $s_goods_no) $chg_similar_yn = "Y";

				// goods_similar에서 해당 상품 삭제
				$sql = "
					delete
					from goods_similar
					where goods_no = :goods_no and similar_no = :similar_no and goods_sub = :goods_sub
				";
				DB::delete($sql, ['goods_no' => $s_goods_no, 'similar_no' => $s_similar_no, 'goods_sub' => $s_goods_sub]);

				// goods에서 상품의 유사 그룹 번호 삭제
				$sql = "
					update goods set
						similar_no = ''
					where goods_no = '$s_goods_no' and goods_sub = '$s_goods_sub'
				";
				DB::update($sql);
			}

			// 유사그룹 대표번호였던 상품의 유사 그룹번호가 삭제되면 -> 남아있는 상품 중 한 개를 유사그룹 번호로 지정
			if($similar_no > 0 && $chg_similar_yn === "Y") {
				$sql = "
					select goods_no
					from goods_similar
					where similar_no = :similar_no
					order by seq
					limit 0, 1
				";
				$row = DB::selectOne($sql, ['similar_no' => $similar_no]);

				if($row !== NULL) {
					$chg_similar_no = $row->goods_no;

					if($chg_similar_no > 0) {
						$sql = "
							update goods_similar gs
								inner join goods g on g.goods_no = gs.goods_no and g.goods_sub = gs.goods_sub set
									gs.similar_no = '$chg_similar_no',
									g.similar_no = '$chg_similar_no'
							where gs.similar_no = '$similar_no'
						";
						DB::update($sql);
					}
				}
			}

            DB::commit();
            $msg = '삭제되었습니다.';
        } catch(Exception $e){
            DB::rollback();
            $code = 500;
			$msg = '에러가 발생했습니다. 잠시 후 다시 시도해주세요.';
        }

		return response()->json(['code' => $code, 'message' => $msg], $code);
	}

	/*
	***
	판매처별 상품관리
	***
	*/

	// 판매처별 상품관리 화면 조회
	public function index_cont($goods_no) {
		$sql = "
			select goods_cont as cont, goods_sub
			from goods
			where goods_no = :goods_no
		";
		$row = DB::selectOne($sql, ['goods_no' => $goods_no]);
		$goods_sub = $row->goods_sub;
		$cont = $row->cont;

		$values = [
			'goods_no' => $goods_no,
			'goods_sub' => $goods_sub,
			'sale_places' => SLib::getSalePlaces(),
			'goods_cont' => $cont,
		];
		return view(Config::get('shop.partner.view') . '/product/prd01_cont', $values);
	}

	// 판매처별 상품설명 검색
	public function search_sale_place_cont(Request $request, $goods_no) {
		$code = 200;
		$msg = '';

		$goods_sub = $request->input('goods_sub');
		$sale_place = $request->input('sale_place');
		$row = '';

		try {
			$row = $this->get_cont_of_sale_place($goods_no, $goods_sub, $sale_place);
			$msg = '조회에 성공했습니다.';
        } catch(Exception $e){
			$code = 500;
            $msg = $e->getMessage();
        }

		return response()->json(['code' => $code, 'message' => $msg, 'data' => $row], $code);
	}

	// 판매처별 상품설명 저장
	public function save_sale_place_cont(Request $request, $goods_no) {
		$code = 200;
		$msg = '';

		$conf = new Conf();
		$cfg_domain = $conf->getConfigValue("shop","domain");

		$goods_sub = $request->input('goods_sub');
		$sale_place = $request->input('sale_place');
		$goods_cont = str_replace($cfg_domain, "", $request->input('goods_cont'));

        try {
            DB::beginTransaction();

			$row = $this->get_cont_of_sale_place($goods_no, $goods_sub, $sale_place);

			if($row !== NULL) {
				// 기존에 등록된 상품설명이 있을 경우
				$sql = "
					update goods_desc
						set goods_cont = '$goods_cont', ut = now()
					where goods_no = '$goods_no' and goods_sub = '$goods_sub' and sale_place = '$sale_place'
				";
				DB::update($sql);
			} else {
				// 기존에 등록된 상품설명이 없을 경우
				$sql = "
					insert into goods_desc (
						goods_no, goods_sub, sale_place, goods_cont, rt, ut
					) values (
						'$goods_no', '$goods_sub', '$sale_place', '$goods_cont', now(), now()
					)
				";
				DB::insert($sql);
			}

            DB::commit();
            $msg = '저장되었습니다.';
        } catch(Exception $e){
            DB::rollback();
            $code = 500;
			$msg = $e->getMessage();
        }

		return response()->json(['code' => $code, 'message' => $msg], $code);
	}

	private function get_cont_of_sale_place($goods_no, $goods_sub, $sale_place) {
		$sql = "
			select goods_no, sale_place, goods_cont
			from goods_desc
			where goods_no = :goods_no and goods_sub = :goods_sub and sale_place = :sale_place
		";
		$row = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'sale_place' => $sale_place]);

		return $row;
	}

	// 휴지통 상품 삭제
	public function cleanup_trash(Request $req){

        $user = [
            'id' => Auth('partner')->user()->id,
            'name' => Auth('partner')->user()->name
        ];

		$error_cnt	= 0;
		$datas = $req->input('datas', []);

        DB::beginTransaction();

        try {

			$goods = new Product($user);

			foreach($datas as $goods_no) {
				$goods->SetGoodsNo( $goods_no );
				$ret	= $goods->CleanUpTrash();

				if( $ret != true ){
					$error_cnt++;
				}
			}

            DB::commit();

            return response()->json(["data"=> $error_cnt]);

		}catch(Exception $e) {

			DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);

		}

	}

	function addRelatedGoods(Request $request) { // 관련 상품 설정

        $goods_no = $request->input("goods_no");
        $goods_sub = $request->input("goods_sub");
        $cross_yn = $request->input("cross_yn"); // 크로스 등록
        $related_cfg = $request->input("related_cfg"); // 관련상품 등록 설정
        $related_goods = $request->input("related_goods");
        $a_goods = explode(",", $related_goods);

        try {

            DB::beginTransaction();

            $id = Auth('partner')->user()->id;
            $name = Auth('partner')->user()->name;

            $user = array( "id" => $id, "name" => $name );

            // 상품 클래스 생성
            $goods = new Product( $user );
            $goods->SetGoodsNo($goods_no); // 현재 서비스에서 sub 번호 필요없으므로 이 메서드로 대체

            // 관련상품 설정 업데이트
            $goods->Edit( $goods_no , array("related_cfg" => $related_cfg) );

            if ( $related_cfg == "A") { // 자동 설정인 경우: 관련상품 삭제

                // 자동 설정 변경 부분은 상품 수정 시 실행해야 함.
                $sql = "DELETE
                    FROM goods_related
                    WHERE goods_no = :goods_no
                ";
                DB::delete($sql, ['goods_no' => $goods_no]);

            } else if ( $related_cfg == "G" ) { // 개별 상품 설정

                // 관련상품 등록
                for ( $i=0; $i < count($a_goods); $i++) {

                    list($r_goods_no, $r_goods_sub) = explode("|", $a_goods[$i]);

                    if ( $goods_no != $r_goods_no) {

                        $sql = "SELECT count(*) AS cnt FROM goods_related
                            WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub' AND r_goods_no = '$r_goods_no' AND r_goods_sub = '$r_goods_sub'
                        ";
                        $row = DB::selectOne($sql);
                        $count = $row->cnt;
                        if ($count == 0) {
                            $sql = "INSERT INTO goods_related (
                                    goods_no, goods_sub, r_goods_no, r_goods_sub, seq, rt, ut, admin_id, admin_nm
                                ) VALUES (
                                    :goods_no, :goods_sub, :r_goods_no, :r_goods_sub, '$i', NOW(), NOW(), '$id', '$name'
                                )
                            ";
                            DB::insert($sql, [
                                'goods_no' => $goods_no,
                                'goods_sub' => $goods_sub,
                                'r_goods_no' => $r_goods_no,
                                'r_goods_sub' => $r_goods_sub
                            ]);
                            $goods->SetGoodsNo($r_goods_no); // 현재 서비스에서 sub 번호 필요없으므로 이 메서드로 대체

                            // 관련상품 설정 업데이트
                            $goods->Edit( $goods_no , array("related_cfg" => $related_cfg) );
                        }
                    }

                }

                if ($cross_yn == "Y") { // 관련상품 크로스 등록

                    array_push( $a_goods, $goods_no."|".$goods_sub);
                    $a_cross = $a_goods;

                    for( $i = 0; $i < count($a_goods); $i++ ){
                        list($goods_no, $goods_sub) = explode("|", $a_goods[$i]);
                        for ( $j = 0; $j < count($a_cross); $j++ ) {
                            list($r_goods_no, $r_goods_sub) = explode("|", $a_cross[$j]);
                            if ( $goods_no != $r_goods_no) { // 등록여부 확인
                                $sql = "SELECT count(*) AS cnt FROM goods_related
                                    WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub' AND r_goods_no = '$r_goods_no' AND r_goods_sub = '$r_goods_sub' ";
                                $row = DB::selectOne($sql);
                                $count = $row->cnt;
                                if ( $count == 0 ) {
                                    $sql = "INSERT into goods_related (
                                            goods_no, goods_sub, r_goods_no, r_goods_sub, seq, rt, ut, admin_id, admin_nm
                                        ) values (
                                            :goods_no, :goods_sub, :r_goods_no, :r_goods_sub, '$i', NOW(), NOW(), '$id', '$name'
                                        )
                                    ";
                                    DB::insert($sql, [
                                        'goods_no' => $goods_no,
                                        'goods_sub' => $goods_sub,
                                        'r_goods_no' => $r_goods_no,
                                        'r_goods_sub' => $r_goods_sub
                                    ]);
                                    $goods->SetGoodsNo($r_goods_no, $r_goods_sub);

                                    // 관련상품 설정 업데이트
                                    $goods->Edit( $goods_no , array("related_cfg" => $related_cfg) );
                                }
                            }
                        }
                    }

                }
            }
            DB::commit();
            return 1;
        } catch (Exception $e) {
            // dd($e);
            DB::rollback();
            return 0;
        }

    }

    function delRelatedGood(Request $request) { // 관련 상품 삭제

        $goods_no = $request->input("goods_no");
        $goods_sub = $request->input("goods_sub");
        $r_goods_no = $request->input("r_goods_no");
        $r_goods_sub = $request->input("r_goods_sub");

        try {
            $sql = "DELETE FROM goods_related
                WHERE goods_no = :goods_no AND goods_sub = :goods_sub
                AND r_goods_no = :r_goods_no AND r_goods_sub = :r_goods_sub
            ";
            DB::delete($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'r_goods_no' => $r_goods_no, 'r_goods_sub' => $r_goods_sub]);
            DB::commit();
            return 1;
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            return 0;
        }

    }

	public function get_addinfo($goods_no){

	    $query = /** @lang text */
            "
            select
                a.upd_date, a.memo, a.head_desc, a.price, a.wonga, a.margin, a.id, b.name
            from goods_modify_hist a
                inner join mgr_user b on a.id = b.id
            where a.goods_no = :goods_no
            order by a.hist_no desc
        ";
        $modify_history = DB::select($query,['goods_no' => $goods_no]);

        $query = /** @lang text */
            "
			select
				a.goods_no,replace(b.img,'a_500', 'a_55') as img,
				c.opt_kind_nm,
				d.brand_nm,
				b.style_no,
				b.goods_nm,
				e.code_val as sale_stat_cl,
				b.price,
				a.r_goods_no,
				a.r_goods_sub
			from goods_related a
				inner join goods b on a.r_goods_no = b.goods_no and a.r_goods_sub = b.goods_sub
				inner join opt c on b.opt_kind_cd = c.opt_kind_cd and c.opt_id = 'K'
				inner join brand d on b.brand = d.brand
				inner join code e on e.code_kind_cd = 'G_GOODS_STAT' and e.code_id = b.sale_stat_cl
			where a.goods_no = :goods_no
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute(["goods_no" => $goods_no]);
        $goods_related = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $goods_related[] = $row;
        }

        return response()->json([
            'modify_history'        => $modify_history,
            'goods_related'         => $goods_related
        ]);
	}


}
