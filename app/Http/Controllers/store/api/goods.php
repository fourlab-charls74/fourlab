<?php

namespace App\Http\Controllers\store\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Conf;
use Carbon\Carbon;
use PDO;

class goods extends Controller
{
	public function show()
	{
        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $com_types	= [];
        $event_cds	= [];
        //판매유형
        $sell_types	= [];
        $code_kinds	= [];

        $conf = new Conf();

        $domain		= $conf->getConfigValue("shop", "domain");


        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
            'domain'		=> $domain,
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'			=> SLib::getItems(),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
            'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
		];

		return view(Config::get('shop.store.view') . '/common/goods_search', $values);
	}

	public function file_search()
	{
		$values = [];
		return view(Config::get('shop.store.view') . '/common/goods_file_search', $values);
	}

	public function search(Request $request)
	{
		$page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $goods_stat = $request->input("goods_stat");
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

        $com_id = $request->input("com_cd");

        $head_desc = $request->input("head_desc");
        $ad_desc = $request->input("ad_desc");

        $is_unlimited = $request->input("is_unlimited");
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

        if ($com_id != "") $where .= " and g.com_id = '" . Lib::quote($com_id) . "'";

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

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = /** @lang text */
                "
                select count(*) as total
                from goods g inner join product_stock s on g.goods_no = s.goods_no 
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
                where 1=1 
                    -- g.com_id = :com_id 
                    $where
			";
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query);
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
				, com.com_nm
				, opt.opt_kind_nm
				, brand.brand_nm as brand
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
				, g.normal_price
				, g.price
				 , ifnull(
					(select sum(wqty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				  ) as wqty
				, g.wonga
				, (100/(g.price/(g.price-g.wonga))) as margin_rate
				, (g.price-g.wonga) as margin_amt
				, g.md_nm
				, bi.code_val as baesong_info
				, bk.code_val as baesong_kind
				, dpt.code_val as dlv_pay_type
				, g.baesong_price
				, g.point
				, g.org_nm
				, g.make
				, g.type
				, g.reg_dm
				, g.upd_dm
				, g.goods_location
				, g.sale_price
                , g.goods_sh 
				, g.goods_type as goods_type_cd
				, com.com_type as com_type_d
				, s.prd_cd , s.goods_opt
			from goods g inner join product_stock s on g.goods_no = s.goods_no
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
			where 1 = 1
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
}
