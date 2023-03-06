<?php

namespace App\Http\Controllers\shop\api;

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
	public function show(Request $request)
	{
        $sdate		= now()->sub(1, 'week')->format('Y-m-d');
        $store_cd   = $request->input('store_cd', '');
        $storage_cd   = $request->input('storage_cd', '');

        $store = null;
        $storage = null;

        // $event_cds	= [];
        // $sell_types	= []; //판매유형
        // $code_kinds	= [];

        $conf = new Conf();
        $domain		= $conf->getConfigValue("shop", "domain");

        if ($store_cd != '') {
            if ($store_cd == 'ALL') $store = (object)['store_cd' => 'ALL', 'store_nm' => '전체 매장'];
            else $store = DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', $store_cd)->first();
        }
        if ($storage_cd != '') {
            if ($storage_cd == 'ALL') $storage = (object)['storage_cd' => 'ALL', 'storage_nm' => '전체 창고'];
            $storage = DB::table('storage')->select('storage_cd', 'storage_nm')->where('storage_cd', $storage_cd)->first();
        }

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'domain'		=> $domain,
            'style_no'		=> "",
            'store'         => $store,
            'storage'       => $storage,
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'items'			=> SLib::getItems(),
			// 'event_cds'		=> $event_cds,
			// 'code_kinds'	    => $code_kinds,
            // 'com_types'     => SLib::getCodes('G_COM_TYPE'),
		];

		return view(Config::get('shop.shop.view') . '/common/goods_search', $values);
	}

	public function file_search()
	{
		$values = [];
		return view(Config::get('shop.shop.view') . '/common/goods_file_search', $values);
	}

	public function search(Request $request)
	{
        $store_cd = $request->input('store_cd', '');
        $storage_cd = $request->input('storage_cd', '');
        $ext_zero_qty = $request->input("ext_zero_qty", '');

		$page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $prd_cd = $request->input("prd_cd", "");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $goods_nm_eng = $request->input("goods_nm_eng");
        $prd_cd_range_text = $request->input("prd_cd_range", '');
        
        $com_id = $request->input("com_cd");
        
        // $goods_stat = $request->input("goods_stat");
        // $head_desc = $request->input("head_desc");
        // $ad_desc = $request->input("ad_desc");

        $ord = $request->input('ord', 'desc');
        $ord_field = $request->input('ord_field', 'pc.rt');
        $orderby = sprintf("order by %s %s, pc.prd_cd", $ord_field, $ord);
        
        $where = "";
        if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '" . Lib::quote($cd) . "%' ";
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

        // if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        // if ($ad_desc != "") $where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

        // if( is_array($goods_stat)) {
        //     if (count($goods_stat) == 1 && $goods_stat[0] != "") {
        //         $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
        //     } else if (count($goods_stat) > 1) {
        //         $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
        //     }
        // } else if($goods_stat != ""){
        //     $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        // }

        if ($goods_nos != "") $goods_no = $goods_nos;
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
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
			}
		}

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

        $having = "";
        if ($ext_zero_qty == "true") {
            if ($store_cd != '' || $storage_cd != '') {
                $having .= " and (sum(ifnull(pss.wqty, 0)) != 0) ";
            }
        }

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sqls = '';
        if ($store_cd != '') $sqls = $this->_store_sql($store_cd, $where, $orderby, $limit, $having);
        else if ($storage_cd != '') $sqls = $this->_storage_sql($storage_cd, $where, $orderby, $limit, $having);
        else $sqls = $this->_normal_sql($where, $orderby, $limit);

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $row = DB::select($sqls->total_sql);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sqls->sql);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row["img"] != "") {
				$row["img"] = sprintf("%s%s", config("shop.image_svr"), $row["img"]);
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

    /** head의 goods.php > search 와 동일 */
    public function head_search(Request $req) {
		// 설정 값 얻기
		$cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
		$cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

        $cfg_img_size_list = "s_62";
        $cfg_img_size_real = "a_500";

		// 팝업 구분
		$limit = $req->input('limit', 100);
		$ord_field = $req->input('ord_field', 'a.goods_no');
		$ord = $req->input('ord', 'desc');

		// 변수 설정
		$sdate = $req->input('sdate', '');
        $edate = $req->input('edate', '');

		$m_cat_cd = $req->input('m_cat_cd', '');
		$style_no = $req->input('style_no', '');
		$goods_no = $req->input('goods_no', '');
		$goods_sub = $req->input('goods_sub', '');
		$goods_nm = $req->input('goods_nm', '');

		$com_type = $req->input('com_type', '');
		$com_nm = $req->input('com_nm', '');
		$com_id = $req->input('com_id', '');
		$goods_stat = $req->input('goods_stat', '');
		$md_id = $req->input('md_id', '');
		$opt_kind_cd = $req->input('opt_kind_cd', '');

		$brand_cd = $req->input('brand_cd', '');
		$brand_nm = $req->input('brand_nm', '');
		$special_yn = $req->input('special_yn', ''); // 2006.11.15 특별상품 추가
		$head_desc = $req->input('head_desc', '');

		$f_price = $req->input('f_price', '');
		$t_price = $req->input('t_price', '');
		$head_desc_yn = $req->input('head_desc_yn', '');

		$f_qty = $req->input('f_qty', '');
		$t_qty = $req->input('t_qty', '');
		$onesize = $req->input('onesize', '');
		$optcnt = $req->input('optcnt', '');

		$f_sqty = $req->input('f_sqty', '');
		$t_sqty = $req->input('t_sqty', '');

		$goods = $req->input('goods', '');

		//	2008-09-29 추가
		$ad_desc = $req->input('ad_desc', '');
		$goods_type = $req->input('goods_type', '');
		$is_unlimited = $req->input('is_unlimited', '');
		$site = $req->input('site', '');
		$ex_site = $req->input('ex_site', '');
		$cat_type = $req->input('cat_type', '');
		$rep_cat_cd   = $req->input('cat_cd', '');
		$not_d_cat_cd = $req->input('not_d_cat_cd', '');
		$style_nos = $req->input('style_nos', '');       // 스타일넘버 textarea
		$goods_nos = $req->input('goods_nos', '');       // 상품번호 textarea
        $sch_style_nos = $req->input('sch_style_nos', '');       // 스타일넘버 textarea
        $sch_goods_nos = $req->input('sch_goods_nos', '');       // 상품번호 textarea

		if($sch_style_nos        != ""){
            $style_no = $sch_style_nos;
        }
		if($style_nos != ""){
			$style_no = $style_nos;
		}
		$style_no = preg_replace("/\s/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\t/",",",$style_no);
		$style_no = preg_replace("/\n/",",",$style_no);

        if($sch_goods_nos        != ""){
            $goods_no = $sch_goods_nos;
        }
		if($goods_nos        != ""){
			$goods_no = $goods_nos;
		}
		$goods_no = preg_replace("/\s/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);
		$goods_no = preg_replace("/\t/",",",$goods_no);
		$goods_no = preg_replace("/\n/",",",$goods_no);


		$where = "";
		$having = "";
		$insql = "";
        $join  = "";

		if( $style_no != "" ) {
			$style_nos = explode(",",$style_no);
			if(count($style_nos) > 1){
				if(count($style_nos) > 500) array_splice($style_nos,500);
				$in_style_nos = "";
				for($i=0; $i<count($style_nos); $i++){
					if(isset($style_nos[$i]) && $style_nos[$i] != ""){
						$in_style_nos .= ($in_style_nos == "") ? "'$style_nos[$i]'" : ",'$style_nos[$i]'";
					}
				}
				if($in_style_nos != "") {
					$where .= " and a.style_no in ( $in_style_nos ) ";
				}
			} else {
				$where .= " and a.style_no like '$style_no%' ";
			}
		}

        if( $goods_no		!= "" ){
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and a.goods_no in ( $in_goods_nos ) ";
			} else {
				$where .= " and a.goods_no = '$goods_no' ";
			}
		}

		if($goods != ""){			// 파일로 검색일 경우
			$goods = explode(",",$goods);

			for($i=0;$i<count($goods);$i++){
				if(empty($goods[$i])) continue;
				list($no,$sub) = explode("\|",$goods[$i]);
				if($insql == ""){
					$insql .= " select '$no' as no,'$sub' as sub ";
				} else {
					$insql .= " union select '$no' as no,'$sub' as sub  ";
				}
			}
			$insql = " inner join ( $insql ) sg on a.goods_no = sg.no and a.goods_sub = sg.sub ";
		}

		if($sdate != "")		$where .= " and a.reg_dm >= $sdate ";
		if($edate != "")		$where .= " and a.reg_dm < date_add($edate, INTERVAL 1 DAY) ";
		if($m_cat_cd != "")	    $where .= " and a.rep_cat_cd like '$m_cat_cd%' ";
		if($goods_nm != "")	    $where .= " and a.goods_nm like '%$goods_nm%' ";
		if($com_type != "")	    $where .= " and a.com_type = '$com_type' ";
		if($com_id != "")		$where .= " and a.com_id = '$com_id' ";
		if($goods_stat != "")	$where .= " and a.sale_stat_cl = '$goods_stat' ";
		if($md_id != "")		$where .= " and a.md_id = '$md_id' ";
		if($opt_kind_cd != "")  $where .= " and a.opt_kind_cd = '$opt_kind_cd' ";
		if($brand_cd != "")	    $where .= " and a.brand = '$brand_cd' ";
		if($special_yn != ""){
			if($special_yn == "N"){
				$where .= " and ifnull(a.special_yn,'') in ('$special_yn','') ";
			 } else {
				$where .= " and a.special_yn= '$special_yn' ";
			 }
		}
		if($head_desc != "")	$where .= " and a.head_desc like '%$head_desc%' ";
		if($f_price != "")	$where .= " and a.price >= '$f_price' ";
		if($t_price != "")	$where .= " and a.price <= '$t_price' ";

		if($head_desc_yn == "Y"){
			$where .= " and ifnull(a.head_desc,'') <> '' ";
		}else if($head_desc_yn == "N"){
			$where .= " and ifnull(a.head_desc,'') = '' ";
		}

		if($onesize == "Y"){

			$where .= "
				and ( select count(*) from goods_summary  where goods_no = a.goods_no and good_qty > 0 ) = 1
			";

			if($optcnt != ""){
				$where .= "
					and ( select count(goods_opt) from goods_summary  where goods_no = a.goods_no ) >= '$optcnt'
				";
			}

			if($f_qty != ""){
				$where .= "
					and ( select sum(good_qty) from goods_summary where goods_no = a.goods_no ) >= '$f_qty'
				";
			}

			if($t_qty != ""){
				$where .= "
					and ( select sum(good_qty) from goods_summary where goods_no = a.goods_no ) <= '$t_qty'
				";
			}

		} else {
			if($f_qty != "")	$having .= " having  qty >= '$f_qty' ";
			if($t_qty != ""){
				if($having == ""){
					$having .= " having  qty <= '$t_qty' ";
				} else {
					$having .= " and qty <= '$t_qty' ";
				}
			}
		}


		if($f_sqty != ""){
			$where .= "
				and ifnull(( select sum(sale_qty) as sale_qty from goods_sale_recent
					where goods_no = a.goods_no ),0) >= '$f_sqty'
			";
		}

		if($t_sqty != ""){
			$where .= "
				and ifnull(( select sum(sale_qty) as sale_qty from goods_sale_recent
					where goods_no = a.goods_no ),0) <= '$t_sqty'
			";
		}

		if ($ad_desc != "")		$where .= " and ad_desc like '$ad_desc%' ";
		if ($goods_type != "")	$where .= " and goods_type = '$goods_type' ";
		if ($is_unlimited != "")	$where .= " and is_unlimited = '$is_unlimited' ";

		if($site != ""){
			if($ex_site == "Y"){
				$join .= " left outer join goods_site s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and s.site = '$site' ";
				$where .= " and ifnull(s.site,'') <> '$site' ";
			} else {
				$join .= " inner join goods_site s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and s.site = '$site' ";
			}
		}

               if( $not_d_cat_cd == "Y"){
			$where .= " and ( select count(*) from category_goods where cat_type = '$cat_type' and goods_no = a.goods_no and goods_sub = a.goods_sub ) = 0 ";
		} else {
			if( $rep_cat_cd != "" ){
				$where .= " and ( select count(*) from category_goods where cat_type = '$cat_type' and d_cat_cd = '$rep_cat_cd' and goods_no = a.goods_no and goods_sub = a.goods_sub ) > 0 ";
			}
		}

        $page = $req->input('page', 1);
		if ($page < 1 or $page == "") $page = 1;

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = /** @lang text */
                "
                select count(*) as total
                from goods a
                where 1=1 
                    $where
			";
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		$sql = /** @lang text */
            "
			select
				'1;0' as type
				, a.goods_no
				, a.goods_sub
				, ifnull(c.opt_kind_nm,'N/A') as opt_kind_nm
				, ifnull(cd.brand_nm,'N/A') as brand_nm
				, cd2.code_val as special_yn
				, a.style_no, '' as goods_img
				, if( ifnull(head_desc, '') = '' and 'Y' = '$onesize', 
					(
						select replace(g.goods_opt,'^',' : ') as opt_val 
						from goods_summary g
                        where goods_no = a.goods_no 
                          and good_qty > 0
					), head_desc
				 ) as head_desc
				, a.goods_nm
				, cm.com_nm
				, a.price
				, ifnull(
					(
						select sum(good_qty)
						from goods_summary 
						where goods_no = a.goods_no 
							and goods_sub = a.goods_sub
					),0
				 ) as qty
				, cd3.code_val as sale_stat_cl
				, DATE_FORMAT(a.reg_dm, '%Y.%m.%d') as regi_date
				, '선택' as choice
				, replace(a.img,'$cfg_img_size_real', '$cfg_img_size_list') as img
				, a.opt_kind_cd
				, a.brand
				, a.goods_nm_eng
				, a.com_id
				, a.goods_type
				, a.option_kind
                , a.com_type
                , a.reg_dm
			from goods a $insql $join
				left outer join opt c on c.opt_id = 'K' and a.opt_kind_cd = c.opt_kind_cd
				left outer join brand cd on cd.brand = a.brand
                left outer join company cm on a.com_id = cm.com_id
				left outer join code cd3 on cd3.code_kind_cd = 'G_GOODS_STAT' and cd3.code_id = a.sale_stat_cl
				left outer join code cd2 on cd2.code_kind_cd = 'G_SPECIAL_YN' and cd2.code_id = a.special_yn
			where 1=1
				$where
				$having
			order by $ord_field $ord
			$limit
        ";

        $rows = DB::select($sql);
        //echo "<pre>$sql</pre>";

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);

    }

    // 기본 상품검색 sql
    private function _normal_sql($where = '', $orderby = '', $limit = '')
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select pc.prd_cd
                from product_code pc
                    inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                where 1=1 $where
                group by pc.prd_cd
            ) a
        ";

        $sql = "
            select
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.goods_no
                , g.style_no
                , g.opt_kind_cd
                , opt.opt_kind_nm
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , g.goods_nm
                , g.goods_nm_eng
                , pc.brand as brand_cd
                , b.brand_nm as brand
                , pc.color, c.code_val as color_nm
                , pc.size
                , pc.goods_opt
                , (ps.qty - ps.wqty) as s_qty
                , ps.wqty as sg_qty
                , ps.qty as total_qty
                , g.goods_sh
                , g.price
                , g.wonga
                -- , ps.wonga
                , (100 / (g.price / (g.price - g.wonga))) as margin_rate
                , (g.price - g.wonga) as margin_amt
                , g.org_nm
                , g.com_id
                , com.com_nm
                , g.make
                , pc.rt as reg_dm
            from product_code pc
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                left outer join company com on com.com_id = g.com_id
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
            where 1=1 $where
            group by pc.prd_cd
            $orderby
            $limit
        ";
        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 매장별 상품검색 sql
    private function _store_sql($store_cd, $where = '', $orderby = '', $limit = '', $having = '')
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $store_where = "";
        if ($store_cd != '' && $store_cd != 'ALL') $store_where .= " and pss.store_cd = '$store_cd' ";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select pc.prd_cd
                from product_code pc
                    inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                    left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd $store_where
                where 1=1 $where
                group by pc.prd_cd
                having 1=1 $having
            ) a
        ";

        $sql = "
            select
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.goods_no
                , g.style_no
                , g.opt_kind_cd
                , opt.opt_kind_nm
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , g.goods_nm
                , g.goods_nm_eng
                , pc.brand as brand_cd
                , b.brand_nm as brand
                , pc.color, c.code_val as color_nm
                , pc.size
                , pc.goods_opt
                , sum(ifnull(pss.qty, 0)) as store_qty
                , sum(ifnull(pss.wqty, 0)) as store_wqty
                , (ps.qty - ps.wqty) as s_qty
                , ps.wqty as sg_qty
                , ps.qty as total_qty
                , g.goods_sh
                , g.price
                , g.wonga
                -- , ps.wonga
                , (100 / (g.price / (g.price - g.wonga))) as margin_rate
                , (g.price - g.wonga) as margin_amt
                , g.org_nm
                , g.com_id
                , com.com_nm
                , g.make
                , pc.rt as reg_dm
            from product_code pc
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd $store_where
                left outer join company com on com.com_id = g.com_id
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
            where 1=1 $where
            group by pc.prd_cd
            having 1=1 $having
            $orderby
            $limit
        ";
        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 창고별 상품검색 sql
    private function _storage_sql($storage_cd, $where = '', $orderby = '', $limit = '', $having = '')
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $storage_where = "";
        if ($storage_cd != '' && $storage_cd != 'ALL') $storage_where .= " and pss.storage_cd = '$storage_cd' ";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select pc.prd_cd
                from product_code pc
                    inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                    left outer join product_stock_storage pss on pss.prd_cd = pc.prd_cd $storage_where
                where 1=1 $where
                group by pc.prd_cd
                having 1=1 $having
            ) a
        ";

        $sql = "
            select
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.goods_no
                , g.style_no
                , g.opt_kind_cd
                , opt.opt_kind_nm
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , g.goods_nm
                , g.goods_nm_eng
                , pc.brand as brand_cd
                , b.brand_nm as brand
                , pc.color, c.code_val as color_nm
                , pc.size
                , pc.goods_opt
                , sum(ifnull(pss.qty, 0)) as storage_qty
                , sum(ifnull(pss.wqty, 0)) as storage_wqty
                , (ps.qty - ps.wqty) as s_qty
                , ps.wqty as sg_qty
                , ps.qty as total_qty
                , g.goods_sh
                , g.price
                , g.wonga
                -- , ps.wonga
                , (100 / (g.price / (g.price - g.wonga))) as margin_rate
                , (g.price - g.wonga) as margin_amt
                , g.org_nm
                , g.com_id
                , com.com_nm
                , g.make
                , pc.rt as reg_dm
            from product_code pc
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                left outer join product_stock_storage pss on pss.prd_cd = pc.prd_cd $storage_where
                left outer join company com on com.com_id = g.com_id
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
            where 1=1 $where
            group by pc.prd_cd
            having 1=1 $having
            $orderby
            $limit
        ";
        return (object)['total_sql' => $total_sql, 'sql' => $sql];
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
                    order by field(br_cd, 'F') desc, brand_nm asc
                ";
            }

            if($key == 'item') {
                $sql = "
                select 
                    code_id, code_val
                from code 
                where code_kind_cd = 'prd_cd_item'
                order by code_val asc
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

        $match_yn = $request->input('match_yn');


        $page = $request->input('page', 1);
        $where = "";

        if($prd_cd != '') $where .= " and pc.prd_cd like '%$prd_cd%'";

        if ($match == 'true') {
            if($goods_nm != '') $where .= " and p.prd_nm like '%$goods_nm%'";
        } else {
            if($goods_nm != '') $where .= " and g.goods_nm like '%$goods_nm%'";
        }

        //상품 매칭
        if($match != 'false') $where .= "and pc.type = 'N'";

        if($match != 'false') {
            if($match_yn == 'Y') 	$where .= " and p.match_yn = 'Y'";
		    if($match_yn == 'N') 	$where .= " and p.match_yn = 'N'";
        }

        if($match_yn == 'Y') 	$where .= " and p.match_yn = 'Y'";
		if($match_yn == 'N') 	$where .= " and p.match_yn = 'N'";


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


        $sql = "
            select 
                *
            from product_code
            where prd_cd = '$prd_cd'
        ";

        if ($match == 'false') {
            $sql = "
                select 
                pc.prd_cd
                , pc.goods_no
                , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                , concat(c.code_val, '^',d.code_val2) as goods_opt
                , concat(pc.brand,pc.year, pc.season, pc.gender
                , pc.item, pc.opt, pc.seq) as prd_cd1,
                pc.color, pc.size, p.match_yn
                from product_code pc
                    left outer join goods g on g.goods_no = pc.goods_no
                    inner join product p on pc.prd_cd = p.prd_cd
                    inner join code c on pc.color = c.code_id
			        inner join code d on pc.size = d.code_id
                where 1=1 and c.code_kind_cd = 'PRD_CD_COLOR' and d.code_kind_cd = 'PRD_CD_SIZE_MATCH'
                $where
            ";
        } else {
            // 상품매칭
            $sql = "
                select pc.prd_cd, p.prd_nm, pc.goods_no, pc.goods_opt, concat(pc.brand,pc.year, pc.season, pc.gender, pc.item, pc.opt, pc.seq) as prd_cd1,
                pc.color, pc.size, p.match_yn, pc.rt
                from product_code pc
                    inner join product p on pc.prd_cd = p.prd_cd
                where 1=1 $where
                order by pc.rt desc
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

    /** 코드일련으로 해당 상품의 컬러옵션 리스트 조회 */
    public function search_color(Request $request)
    {
        $prd_cd_p = $request->input('prd_cd_p', '');
        $colors = [];

        if ($prd_cd_p != '') {
            $sql = "
                select p.color, c.code_val as color_nm
                from product_code p
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = p.color
                where p.prd_cd like '$prd_cd_p%'
                group by p.color
            ";
            $colors = DB::select($sql);
        }

        return response()->json(['colors' => $colors]);
    }

    /** 코드일련 검색 */
    public function search_prdcd_p(Request $request)
    {
        $prd_cd_p = $request->input('prd_cd', '');
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

        $where = "";
        $having = "";

        if ($prd_cd_p != '') $having .= " and prd_cd_p like '$prd_cd_p%' ";
        if ($goods_nm != '') $having .= " and goods_nm like '%$goods_nm%' ";
        
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

        $sql = "
            select
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.prd_cd
                , pc.goods_no
                , ifnull(g.goods_nm, (select prd_nm from product p where p.prd_cd = pc.prd_cd)) as goods_nm
                , g.goods_nm_eng
                , ifnull(g.style_no, (select style_no from product p where p.prd_cd = pc.prd_cd)) as style_no
            from product_code pc
                left outer join goods g on g.goods_no = pc.goods_no
            where 1=1 $where
            group by prd_cd_p
            having 1=1 $having
            order by 
                field(pc.brand, 'F') desc,
                pc.year desc,
                prd_cd_p
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
            ],
            "body" => $result
        ]);
    }

    /*********************************************************************************/
    /****************************** 원부자재코드 검색 관련 ****************************/
    /********************************************************************************/

    const Conds_sub = [
        'brand' => 'BRAND',
        'year' => 'YEAR',
        'season' => 'SEASON',
        'gender' => 'GENDER',
        'item' => 'ITEM',
        'opt' => 'OPT'
    ];

    public function search_product_sub_conditions()
    {
        $result = [];

        foreach (self::Conds_sub as $key => $cond_cd) {
            $sql = "
                select code_id, code_val
                from code
                where code_kind_cd = 'PRD_CD_$cond_cd'
                order by code_seq
            ";

            if($key == 'brand') {
                $sql = "
                    select code_id, code_val
                    from code
                    where code_kind_cd = 'PRD_MATERIAL_TYPE'
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

    public function search_prdcd_sub(Request $request)
    {
        $prd_cd = $request->input('prd_cd_sub', '');
        $goods_nm = $request->input('goods_nm_sub', '');

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

        $page = $request->input('page', 1);
        $where = "";

        if($prd_cd != '') $where .= " and pc.prd_cd like '$prd_cd%'";
        if($goods_nm != '') $where .= " and pc.prd_nm like '%$goods_nm%'";

        foreach (self::Conds_sub as $key => $value) {
            if ($key === 'item') $key = 'items';
            if (count(${ $key }) > 0) {
                $where .= ${ $key . '_contain' } == 'true' ? " and (1!=1" : " and (1=1";

                $col = $key === 'items' ? 'item' : $key;
                foreach (${ $key } as $item) {
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

        $sql = "
            select 
                  pc.prd_cd
                , p.prd_nm
                , pc.goods_no
                , concat(c.code_val, '^',d.code_val2) as goods_opt
                , pc.color, pc.size
            from product_code pc 
                inner join product p on p.prd_cd = pc.prd_cd
                inner join code c on pc.color = c.code_id
                inner join code d on pc.size = d.code_id
            where 1=1 and pc.brand in('PR','SM') and c.code_kind_cd = 'PRD_CD_COLOR' and d.code_kind_cd = 'PRD_CD_SIZE_MATCH'
            $where
        ";
      
        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
              
            ],
            "body" => $result
        ]);
    }

    //판매유형 검색
    public function search_sell_type(Request $request)
    {
        $sell_nm= $request->input('sell_nm', '');

        $page = $request->input('page', 1);
        $where = "";

        if($sell_nm != '') $where .= " and code_val like '$sell_nm%'";

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'sale_kind'
        ";
      
        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
              
            ],
            "body" => $result
        ]);
    }


    //행사코드 검색
    public function search_prcode(Request $request)
    {
        $pr_code_nm= $request->input('pr_code_nm', '');

        $page = $request->input('page', 1);
        $where = "";

        if($pr_code_nm != '') $where .= " and code_val like '$pr_code_nm%'";

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'pr_code'
        ";
      
        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
              
            ],
            "body" => $result
        ]);
    }
}
