<?php

namespace App\Http\Controllers\partner\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Components\SLib;

class prd10Controller extends Controller
{
    public function index(Request $request) {
        $query = "select code_id as id, code_val as val from code where code_kind_cd = 'G_GOODS_STAT' and code_id not in ('K','40') order by code_seq";
        $sale_stats = DB::select($query);

        $style_no	= $request->input('style_no');

        $opt_cd_list = $this->get_opt_cd_list();

        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
            'alter_reasons' => SLib::getCodes('G_JAEGO_REASON'),
            'sale_stats' => $sale_stats,
            'opt_cd_list' => $opt_cd_list,
            'style_no'		=> $style_no,
        ];
        return view( Config::get('shop.partner.view') . '/product/prd10',$values);
    }

    public function search(Request $request){
        $com_id = Auth('partner')->user()->com_id;
        $com_nm = Auth('partner')->user()->com_nm;
        $com_type = Auth('partner')->user()->com_type;

        $ord_field = $request->input('ord_field');
        $ord = $request->input('ord');
        $limit = ($request->input('limit')) ? $request->input('limit') : 500;
        $sdate = $request->input('sdate');
        $edate = $request->input('edate');
        $m_cat_cd = $request->input('m_cat_cd');
        $style_no = $request->input('style_no');
        $goods_no = $request->input('goods_no');
        $goods_sub = $request->input('goods_sub');
        $goods_nm = $request->input('goods_nm');
        $goods_stat = $request->input('goods_stat');
        $md_id = $request->input('md_id');
        $opt_kind_cd = $request->input('opt_kind_cd');
        $brand_cd = $request->input('brand_cd');
        $brand_nm = $request->input('brand_nm');
        $special_yn = $request->input('special_yn');
        $head_desc = $request->input('head_desc');
        $f_price = $request->input('f_price');
        $t_price = $request->input('t_price');
        $head_desc_yn = $request->input('head_desc_yn');
        $f_qty = $request->input('f_qty');
        $t_qty = $request->input('t_qty');
        $onesize = $request->input('onesize');
        $optcnt = $request->input('optcnt');
        $f_sqty = $request->input('f_sqty');
        $t_sqty = $request->input('t_sqty');
        $goods = $request->input('goods');

        $ad_desc = $request->input('ad_desc');
        $goods_type = $request->input('goods_type');
        $is_unlimited = $request->input('is_unlimited');

        $where = "";
		$having = "";
        $insql = "";

        $data_cnt = 0;
        $page_cnt = 0;

        if($goods != ""){
            $goods_arr = explode(",", $goods);

            for($i=0; $i<count($goods_arr); $i++){
                if(empty($goods_arr[$i])) continue;
                list($no, $sub) = explode("\|", $goods_arr[$i]);
                if($insql == ""){
                    $insql .= " select '$no' as no, '$sub' as sub ";
                }else{
                    $insql .= " union select '$no' as no, '$sub' as sub ";
                }
            }
            $insql = "inner join ($insql) sg on a.goods_no = sg.no and a.goods_sub = sg.sub ";
        }

        if($sdate != "") $where .= " and a.reg_dm >= $sdate ";
        if($edate != "") $where .= " and a.reg_dm < date_add($sdate, INTERVAL 1 DAY) ";
        if($m_cat_cd != "")		$where .= " and a.rep_cat_cd like '$m_cat_cd%' ";
		if($style_no != "")		$where .= " and a.style_no like '$style_no%' ";
		if($goods_no != "")		$where .= " and a.goods_no = '$goods_no' ";
		if($goods_sub !="")		$where .= " and a.goods_sub = '$goods_sub' ";
		if($goods_nm != "")		$where .= " and a.goods_nm like '%$goods_nm%' ";
		if($com_type != "")		$where .= " and a.com_type = '$com_type' ";
		if($com_id != "")			$where .= " and a.com_id = '$com_id' ";
		if($goods_stat != "")		$where .= " and a.sale_stat_cl = '$goods_stat' ";
		if($md_id != "")			$where .= " and a.md_id = '$md_id' ";
		if($opt_kind_cd != "")	$where .= " and a.opt_kind_cd = '$opt_kind_cd' ";
        if($brand_cd != "")		$where .= " and a.brand = '$brand_cd' ";
        if($special_yn != ""){
			if($special_yn == "N"){
				$where .= " and ifnull(a.special_yn,'') in ('$special_yn','') ";
			 } else {
				$where .= " and a.special_yn= '$special_yn' ";
			 }
		}
		if($head_desc != "")		$where .= " and a.head_desc like '%$head_desc%' ";
		if($f_price != "")		$where .= " and a.price >= '$f_price' ";
		if($t_price != "")		$where .= " and a.price <= '$t_price' ";

		if($head_desc_yn == "Y"){
			$where .= " and ifnull(a.head_desc,'') <> '' ";
		}else if($head_desc_yn == "N"){
			$where .= " and ifnull(a.head_desc,'') = '' ";
        }

        if($onesize == "Y"){

			$where .= "	and ( select count(*) from goods_summary  where goods_no = a.goods_no and good_qty > 0 ) = 1 ";

			if($onesize != "")	$where .= " and ( select count(goods_opt) from goods_summary  where goods_no = a.goods_no ) >= '$onesize' ";
			if($f_qty != "")	$where .= " and ( select sum(good_qty) from goods_summary where goods_no = a.goods_no ) >= '$f_qty' ";
			if($t_qty != "")	$where .= " and ( select sum(good_qty) from goods_summary where goods_no = a.goods_no ) <= '$t_qty' ";

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

		$page = $request->input('page');
		if ($page < 1 or $page == "") $page = 1;
        $page_size = $limit;

        if ($page == 1) {

			if(empty($where)){
                /* admin/prd/pop_goods.php (3) */
				$sql = " 
					select
						count(*) as cnt
					from goods
				";
			} else {
                /* admin/prd/pop_goods.php (3) */
				$sql = " 
					select
						count(*) as cnt
					from goods a
						inner join brand cd on cd.brand = a.brand
					where 1=1
						$where
				";
			}
			$row = DB::select($sql);
			$data_cnt = $row[0]->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
			}
			$arr_header = array("data_cnt"=>$data_cnt, "page_cnt"=>$page_cnt);

		} else {
			$startno = ($page-1) * $page_size;
			$arr_header = null;
        }

        if($limit == -1){
			$where_limit = "";
		} else {
			$where_limit = " limit $startno,$page_size ";
        }
        $result = "";

        $query = "
			select
				'on;off' as blank
				, a.goods_no
				, a.goods_sub
				, c.opt_kind_nm as opt_kind_nm
				, ifnull(cd.brand_nm,'N/A') as brand_nm
				, cd2.code_val as special_yn
				, a.style_no, '' as goods_img
				, if( ifnull(head_desc, '') = '' and 'Y' = '$onesize', (
					select replace(g.goods_opt,'^',' : ') as opt_val
					from goods_summary g
					where goods_no = a.goods_no and good_qty > 0
				   ), head_desc
				  ) as head_desc
				, a.goods_nm
				, d.com_nm
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
				, replace(a.img,'a_500', 's_62') as img_62
				, a.opt_kind_cd
				, a.brand
				, a.goods_nm_eng
				, a.com_id
				, a.goods_type
				, a.option_kind
			from goods a $insql
				left outer join opt c use index(kindcd_id) on c.opt_id = 'K' and a.opt_kind_cd = c.opt_kind_cd
				left outer join brand cd on cd.brand = a.brand
				left outer join company d on a.com_id = d.com_id
				left outer join code cd3 on cd3.code_kind_cd = 'G_GOODS_STAT' and cd3.code_id = a.sale_stat_cl
				left outer join code cd2 on cd2.code_kind_cd = 'G_SPECIAL_YN' and cd2.code_id = a.special_yn
			where 1=1
				$where
				$having
			order by $ord_field $ord
			$where_limit
        ";
        //echo $query;
        //echo "<br>";

        $result = DB::select($query);
        /*
        echo "limit : ". $limit;
        echo "<br>";
        echo "com_type : ". $com_type;
        echo "<br>";
        */
        //echo "page_size: ".$page_size;
        echo json_encode(array(
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result,
        ));
    }

    public function prd_search(request $request){
        $com_id = Auth('partner')->user()->com_id;
        $com_nm = Auth('partner')->user()->com_nm;

        $data = $request->input('data');
        $datas = explode(",", $data);

        $query = "select code_val from `code` where code_kind_cd='G_IMG_SIZE' and code_id='list' ";
        $img_size_row = DB::select($query);
        $cfg_img_size_list = $img_size_row[0]->code_val;

        $query = "select code_val from `code` where code_kind_cd='G_IMG_SIZE' and code_id='real' ";
        $img_size_row = DB::select($query);
        $cfg_img_size_real = $img_size_row[0]->code_val;

        $result = [];
        $total = count($datas);

        for($i=0; $i<count($datas); $i++){
            if(isset($datas[$i]) && $datas[$i] != ""){
                $goods_info = explode("|", $datas[$i]);
                $goods_no = $goods_info[0];
                $goods_sub = $goods_info[1];
                /*
                echo $goods_no;
                echo "<br>";
                */
                $wheres = " and g.goods_no = '$goods_no' and g.goods_sub = '$goods_sub' ";

                $query = "
					SELECT
						'' as blank, g.goods_no, g.goods_sub, g.style_no, opt.opt_kind_nm
						, brand.brand_nm, c.full_nm, stat.code_val as sale_stat
						, '' as img, g.head_desc, g.goods_nm, g.ad_desc
						, g.price, '' as margin_rate, g.wonga
						, g.price as mod_price, '' as mod_margin_rate, g.wonga as mod_wonga
						, bi.code_val as bae_info, bk.code_val as bae_kind
						, dpt.code_val as dlv_pay_type_nm, g.dlv_fee_cfg, g.bae_yn, g.baesong_price
						, g.org_nm, g.make, if(g.restock_yn = '', 'N', ifnull(g.restock_yn, 'N')) as restock_yn
						, g.goods_cont, g.spec_desc, g.baesong_desc, g.opinion
						, g.opt_kind_cd, g.brand, g.rep_cat_cd, g.sale_stat_cl
						, g.baesong_info, g.baesong_kind, g.dlv_pay_type
						, 'N' as category_yn
						, cp.com_type, g.goods_type
						, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
							select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
							from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
						  )) as img_s_62
					FROM
						goods g
						LEFT OUTER JOIN opt opt ON opt.opt_kind_cd = g.opt_kind_cd AND opt.opt_id = 'K'
						LEFT OUTER JOIN brand brand ON brand.brand = g.brand
						LEFT OUTER JOIN category c ON c.d_cat_cd = g.rep_cat_cd AND c.cat_type  = 'DISPLAY'
						LEFT OUTER JOIN company cp ON g.com_id = cp.com_id
						LEFT OUTER JOIN code stat ON stat.code_kind_cd = 'G_GOODS_STAT' AND g.sale_stat_cl = stat.code_id
						LEFT OUTER JOIN code bk ON bk.code_kind_cd = 'G_BAESONG_KIND' AND bk.code_id = g.baesong_kind
						LEFT OUTER JOIN code bi ON bi.code_kind_cd = 'G_BAESONG_INFO' AND bi.code_id = g.baesong_info
						LEFT OUTER JOIN code dpt ON dpt.code_kind_cd = 'G_DLV_PAY_TYPE' AND dpt.code_id = g.dlv_pay_type
					WHERE 1=1
						$wheres
                ";
                /*
                echo $query;
                echo "<br>";
                */
                $row = DB::select($query);
                if($row[0]){
                    $price = $row[0]->price;
                    $wonga = $row[0]->wonga;
                    if($wonga == "") $wonga = 0;

                    $margin_amt = $price - $wonga;
                    if( $price == 0) $price = 1;
                    $margin_rage = round(($margin_amt / $price *100), 2);
                    $row[0]->margin_rage = $margin_rage;
                }
                //echo $query;
                $result[] = $row[0];
            }
        }

        echo json_encode(array(
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => 1,
                "page_cnt" => 0,
                "page_total" => count($result)
            ),
            "body" => $result,
        ));

    }

    private function get_opt_cd_list(){
        $query = "select opt_kind_cd as 'name', opt_kind_nm as 'value' from opt where opt_id = 'K' and use_yn = 'Y' order by opt_seq";
  
        $result = DB::select($query);
  
        return $result;
    }

}
