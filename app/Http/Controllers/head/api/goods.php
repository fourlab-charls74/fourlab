<?php

namespace App\Http\Controllers\head\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Conf;
use Carbon\Carbon;

class goods extends Controller
{
    public function show() {
        $conf = new Conf();

		$site = $conf->getConfig("shop", "sale_place", "");

        $sql = 'select * from company where site_yn = "Y" and com_type=4';

        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_goods_type'),
            'com_types' => SLib::getCodes('G_COM_TYPE'),
            'sites' => DB::select($sql),
            'site' => $site
        ];

        return view( Config::get('shop.head.view') . '/common/goods_search',$values);
    }

    public function file_search() {
        $values = [];
        return view( Config::get('shop.head.view') . '/common/goods_file_search',$values);
    }

    public function search(Request $req) {

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
				$where .= " and ifnull(s.site,'') <> '$S_site' ";
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
            //$row = DB::select($query,['com_id' => $com_id]);
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
}
