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

        $event_cds	= [];
        //판매유형
        $sell_types	= [];
        $code_kinds	= [];

        $conf = new Conf();

        $domain		= $conf->getConfigValue("shop", "domain");


        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			// 'event_cds'		=> $event_cds,
			// 'code_kinds'	    => $code_kinds,
            'domain'		=> $domain,
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            // 'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'			=> SLib::getItems(),
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

        $prd_cd = $request->input("prd_cd", "");
        $goods_stat = $request->input("goods_stat");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $goods_nm_eng = $request->input("goods_nm_eng");

        $com_id = $request->input("com_cd");

        $head_desc = $request->input("head_desc");
        $ad_desc = $request->input("ad_desc");

        $limit = $request->input("limit",100);
        $ord = $request->input('ord','desc');
        $ord_field = $request->input('ord_field','g.goods_no');

        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $where = "";
        if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or s.prd_cd = '" . Lib::quote($cd) . "' ";
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

        if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        if ($ad_desc != "") $where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

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

    /*********************************************************************************/
    /******************************* 매장별 상품검색 관련 *****************************/
    /********************************************************************************/
    
    public function store_show($store_cd = '') 
    {
        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $event_cds	= [];
        //판매유형
        $sell_types	= [];
        $code_kinds	= [];

        $conf = new Conf();

        $domain		= $conf->getConfigValue("shop", "domain");

        // 매장정보
        $store = DB::table("store")->where('store_cd', '=', $store_cd)->select('store_cd', 'store_nm')->first();
        if($store == null) $store = ['store_cd' => '', 'store_nm' => ''];

        $values = [
            'store'         => $store,
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			// 'event_cds'		=> $event_cds,
			// 'code_kinds'	=> $code_kinds,
            'domain'		=> $domain,
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'items'			=> SLib::getItems(),
		];

		return view(Config::get('shop.store.view') . '/common/store_goods_search', $values);
    }

    public function store_search(Request $request) 
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

        $prd_cd		= $request->input("prd_cd", "");
        $com_id = $request->input("com_cd");

        $head_desc = $request->input("head_desc");
        $ad_desc = $request->input("ad_desc");

        $limit = $request->input("limit",100);
        $ord = $request->input('ord','desc');
        $ord_field = $request->input('ord_field','g.goods_no');

        $store_cd   = $request->input("store_cd", "");
        $ext_zero_qty = $request->input("ext_zero_qty", "");

        $orderby = sprintf("order by %s %s, s.prd_cd", $ord_field, $ord);

        $where = "";
        if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or s.prd_cd = '" . Lib::quote($cd) . "' ";
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

        if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        if ($ad_desc != "") $where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";


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

        $store_where = "";
        if ($store_cd != "") $store_where .= " and ps.store_cd = '$store_cd'";

        $having = "";
        if ($ext_zero_qty == "true") {
            if ($store_cd != "") {
                $having .= " and (sum(ps.wqty) != 0) ";
            } else {
                $where .= " and (s.wqty != 0) ";
            }
        }

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = "
                select count(c.prd_cd) as total
                from (
                    select s.prd_cd, count(s.prd_cd)
                    from goods g 
                        inner join product_stock s on g.goods_no = s.goods_no 
                        inner join product_stock_store ps on s.prd_cd = ps.prd_cd $store_where
                    where 1=1 $where
                    group by s.prd_cd
                    having 1=1 $having
                ) as c
			";
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = "
			select
                '' as blank
                , g.goods_no
                , g.goods_sub
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
                , s.wqty as wqty
                , sum(ps.qty) as store_qty
                , sum(ps.wqty) as store_wqty
			from goods g 
                inner join product_stock s on g.goods_no = s.goods_no
                inner join product_stock_store ps on s.prd_cd = ps.prd_cd $store_where
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = g.com_id
				left outer join brand brand on brand.brand = g.brand
				left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
				left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
				left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
			where 1=1 $where
            group by s.prd_cd
            having 1=1 $having
            $orderby
			$limit
		";

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

    /*********************************************************************************/
    /******************************* 창고별 상품검색 관련 *****************************/
    /********************************************************************************/
    
    public function storage_show($storage_cd) 
    {
        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $event_cds	= [];
        //판매유형
        $sell_types	= [];
        $code_kinds	= [];

        $conf = new Conf();

        $domain		= $conf->getConfigValue("shop", "domain");

        // 창고정보
        $storage = DB::table("storage")->where('storage_cd', '=', $storage_cd)->select('storage_cd', 'storage_nm')->get();
        if(count($storage) < 1) $storage = ['storage_cd' => '', 'storage_nm' => ''];

        $values = [
            'storage'       => $storage[0],
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			// 'event_cds'		=> $event_cds,
			// 'code_kinds'	=> $code_kinds,
            'domain'		=> $domain,
            'style_no'		=> "",
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'items'			=> SLib::getItems(),
		];

		return view(Config::get('shop.store.view') . '/common/storage_goods_search', $values);
    }

    public function storage_search(Request $request) 
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

        $prd_cd		= $request->input("prd_cd", "");
        $com_id = $request->input("com_cd");

        $head_desc = $request->input("head_desc");
        $ad_desc = $request->input("ad_desc");

        $limit = $request->input("limit",100);
        $ord = $request->input('ord','desc');
        $ord_field = $request->input('ord_field','g.goods_no');

        $storage_cd   = $request->input("storage_cd", "");

        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $where = "";
        if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd = '" . Lib::quote($cd) . "' ";
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

        if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        if ($ad_desc != "") $where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

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

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = /** @lang text */
                "
                select count(*) as total
                from product_stock_storage p
                    left outer join goods g on g.goods_no = p.goods_no
                    left outer join brand b on b.brand = g.brand
                    left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                    left outer join company com on com.com_id = g.com_id
                    left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
                    left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                    left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                    left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
                    left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
                    left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
                where p.storage_cd = '$storage_cd' $where
			";

            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = "
            select
                p.goods_no,
                g.goods_sub,
                ifnull(type.code_val, 'N/A') as goods_type,
                com.com_nm,
                p.prd_cd,
                op.opt_kind_nm,
                b.brand_nm as brand,
                cat.full_nm,
                g.style_no,
                g.head_desc,
                '' as img_view,
                if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img,
                stat.code_val as sale_stat_cl,
                g.goods_nm,
                g.goods_nm_eng,
                g.ad_desc,
                g.normal_price,
                g.price,
                g.wonga,
                (100/(g.price/(g.price-g.wonga))) as margin_rate,
                (g.price-g.wonga) as margin_amt,
                g.md_nm,
                bi.code_val as baesong_info,
                bk.code_val as baesong_kind,
                dpt.code_val as dlv_pay_type,
                g.baesong_price,
                g.point,
                g.org_nm,
                g.make,
                g.type,
                g.reg_dm,
                g.upd_dm,
                g.goods_location,
                g.sale_price,
                g.goods_sh ,
                g.goods_type as goods_type_cd,
                com.com_type as com_type_d,
                p.goods_opt,
                p.qty as storage_qty,
                p.wqty as storage_wqty,
                '' as rel_qty
            from product_stock_storage p
                left outer join goods g on g.goods_no = p.goods_no
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join company com on com.com_id = g.com_id
                left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
                left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
                left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
            where p.storage_cd = '$storage_cd' $where
            $orderby
			$limit
        ";

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

    /*********************************************************************************/
    /******************************** 상품코드 검색 관련 ******************************/
    /********************************************************************************/

    const Conds = [
        'brand' => 'BRAND',
        'year' => 'YEAR',
        'season' => 'SEASON',
        'gender' => 'GENDER',
        'item' => 'ITEM',
        'opt' => 'OPT'
    ];

    public function search_product_conditions()
    {
        $result = [];

        foreach(self::Conds as $key => $cond_cd)
        {
            $sql = "
                select code_id, code_val
                from code
                where code_kind_cd = 'PRD_CD_$cond_cd'
                order by code_seq
            ";

            if($key == 'brand') {
                $sql = "
                    select br_cd as code_id, brand_nm as code_val
                    from brand
                    where use_yn = 'Y'
                        and br_cd != ''
                    order by field(br_cd, 'F') desc, br_cd
                ";
            }
            $result[$key] = DB::select($sql);
        }

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => 1,
            ],
            "body" => $result
        ]);
    }

    public function search_prdcd(Request $request)
    {
        $prd_cd = $request->input('prd_cd', '');
        $goods_nm = $request->input('goods_nm', '');

        $brand = $request->input('brand', []);
        $brand_contain = $request->input('brand_contain', '');
        $year = $request->input('year', []);
        $year_contain = $request->input('year_contain', '');
        $season = $request->input('season', []);
        $season_contain = $request->input('season_contain', '');
        $gender = $request->input('gender', []);
        $gender_contain = $request->input('gender_contain', '');
        $items = $request->input('item', []);
        $items_contain = $request->input('item_contain', '');
        $opt = $request->input('opt', []);
        $opt_contain = $request->input('opt_contain', '');
        $match = $request->input('match');

        $page = $request->input('page', 1);
        $where = "";

        if($prd_cd != '') $where .= " and p.prd_cd like '%$prd_cd%'";
        if($goods_nm != '') $where .= " and p.prd_nm like '%$goods_nm%'";

        //상품 매칭
        if($match == 'false'){ 
        } else {
            $where .= "and pc.type = 'N'";
        } 
        //상품 매칭
        if($match == 'false') {
        } else {
            $where .= "and p.match_yn = 'N'";
        } 

        foreach(self::Conds as $key => $value)
        {
            if($key === 'item') $key = 'items';
            if(count(${ $key }) > 0)
            {
                $where .= ${ $key . '_contain' } == 'true' ? " and (1!=1" : " and (1=1";

                $col = $key === 'items' ? 'item' : $key;
                foreach(${ $key } as $item) {
                    if(${ $key . '_contain' } == 'true')
                        $where .= " or pc.$col = '$item'";
                    else
                        $where .= " and pc.$col != '$item'";
                }
                $where .= ")";
            }
        }

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;

        if ($match == 'false') {
            $sql = "
                select p.prd_cd, p.goods_no, g.goods_nm, p.goods_opt, p.color, p.size
                from product_code p
                    inner join goods g on g.goods_no = p.goods_no
                where 1=1 $where
            ";

        } else {
            // 상품매칭
            $sql = "
                select pc.prd_cd, p.prd_nm, pc.goods_no, pc.goods_opt, pc.color, pc.size, p.match_yn
                from product_code AS pc
                    inner join product AS p on pc.prd_cd = p.prd_cd
                where 1=1 $where
            ";
        }


        $result = DB::select($sql);

        // if ($page == 1) {
        //     $sql = "
        //         select count(*) as total
        //         from product_code p
        //             inner join goods g on g.goods_no = p.goods_no
        //         where 1=1 $where
        //     ";
        //     $row = DB::select($sql);
        //     $total = $row[0]->total;
        //     $page_cnt = (int)(($total - 1) / $page_size) + 1;
        // }

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
                // "page" => $page,
                // "page_cnt" => $page_cnt,
                // "page_total" => count($result)
            ],
            "body" => $result
        ]);
    }
}
