<?php

namespace App\Http\Controllers\head\product;

use App\Components\Lib;
use App\Components\SLib;

use App\Http\Controllers\Controller;
use App\Models\Conf;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class prd07Controller extends Controller
{
    public function index(Request $request)
	{
		/**
		 * post 형식으로 window.open 하여 전달받은 goods_nos 있으면 뷰에 전달
		 */
		$goods_nos = $request->input('goods_nos', '');

		/**
		 * 설정 값 얻기
		 */
		$conf = new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery", "base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery", "free_delivery_amt");
		$cfg_point_ratio		= $conf->getConfigValue("point", "ratio", "0");

		$sql = "select id id, name val from mgr_user where md_yn = 'Y' and use_yn = 'Y' order by name";
		$md_names = DB::select($sql);

		$values = [
			'goods_nos' => $goods_nos,
			'items' => SLib::getItems(),
			'com_types' => SLib::getCodes('G_COM_TYPE'),
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
		return view(Config::get('shop.head.view') . '/product/prd07', $values);
	}

    /**
	 * 일괄수정 검색
	 */
	public function search(Request $request) {


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
}