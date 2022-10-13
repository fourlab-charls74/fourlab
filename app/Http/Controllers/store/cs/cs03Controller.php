<?php

namespace App\Http\Controllers\store\cs;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Stock;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;
use PDO;

class cs03Controller extends Controller
{
    public function index(Request $request) {
        $immutable = CarbonImmutable::now();
        $sdate = $immutable->sub(1, 'month')->format('Y-m-d');
        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'buy_order_states' => SLib::getCodes('G_BUY_ORDER_STATE'),
            'formula_types' => collect([">", "<", ">=", "<=", "=", "<>"]),
            'month3' => (int)date("m"),
            'month2' => (int)$immutable->sub(1, 'month')->format('m'),
            'month1' => (int)$immutable->sub(2, 'month')->format('m'),
        ];
        return view(Config::get('shop.store.view') . '/cs/cs03', $values);
    }

    public function search(Request $request) {
		/**
		 * 설정값 얻기
		 */
        $conf = new Conf();
        $cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
		$cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");
        $cfg_domain_img			= $conf->getConfigValue("shop","domain_img");
        if($cfg_domain_img != ""){
			$goods_img_url = sprintf("http://%s",$cfg_domain_img);
		} else {
			$goods_img_url = "";
		}

		/**
		 * inputs
		 */
        $sdate = str_replace('-','',$request->input("sdate"));
        $edate = str_replace('-','',$request->input("edate"));
		$state = $request->input("buy_order_state");
		$buy_ord_no = $request->input("buy_ord_no");
		$brand_nm = $request->input("brand_nm");
		$brand_cd = $request->input("brand_cd");
		$com_type = $request->input("com_type");
		$com_id = $request->input("com_cd");
		
		$style_no = $request->input("style_no");
		$goods_stat = $request->input("goods_stat");
		$ex_trash = $request->input("ex_trash");
		$ex_soldout = $request->input("ex_soldout");
		$goods_nm = $request->input("goods_nm");
		$formula_type = $request->input("formula_type");
		$formula_val = $request->input("formula_val");
        $ord_field = $request->input("ord_field");
		$ord = $request->input("ord");
		$limit = $request->input("limit");

		/**
		 * query 작성
		 */
		$where_a = " and buy_ord_date >= '$sdate' and buy_ord_date <= '$edate' ";
		$where_p = "";

		if( $goods_nm != "" ){
			$where_a .= " and g.goods_nm like '%$goods_nm%' ";
			$where_p .= " and g.goods_nm like '%$goods_nm%' ";
		}
		if( $com_type != "" ){
			$where_a .= " and c.com_type = '$com_type' ";
		}
		if( $com_id != "" ){
			$where_a .= " and g.com_id = '$com_id' ";
			$where_p .= " and g.com_id = '$com_id' ";
		}
		if( $buy_ord_no != "" ){
			$where_a .= " and p.buy_ord_no = '$buy_ord_no'";
		}
		if( $state != "" ){
			$where_a .= " and p.state = '$state'";
		}
		if( $goods_stat != "" ){
			$where_a .= " and g.sale_stat_cl = '$goods_stat'";
			$where_p .= " and g.sale_stat_cl = '$goods_stat'";
		}

		$style_no = preg_replace("/\s/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\t/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\n/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);

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
					$where_a .= " and g.style_no in ( $in_style_nos ) ";
					$where_p .= " and g.style_no in ( $in_style_nos ) ";
				}
			} else {
				$where_a .= " and g.style_no like '${style_no}%' ";
				$where_p .= " and g.style_no like '${style_no}%' ";
			}
		}

		if( $ex_trash == "Y" ){
			$where_a .= " and g.sale_stat_cl > 0 ";
			$where_p .= " and g.sale_stat_cl > 0 ";
		}

		if( $ex_soldout == "Y" ){
			$where_a .= " and a.qty > 0 ";
		}

		if( $brand_cd != "" ){
			$where_a .= " and g.brand = '$brand_cd' ";
			$where_p .= " and g.brand = '$brand_cd' ";
		} else if ($brand_cd == "" && $brand_nm != "") {
			$where_a .= " and g.brand ='$brand_cd'";
			$where_p .= " and g.brand ='$brand_cd'";
		}

		if( $formula_val != "" ) {
			$where_a .= "  and sale_qty $formula_type $formula_val ";
		}

		$price_cols = "";
		$print_cols = "";
		$group_cnt = 0;
		$group_nos = array();
		$sql = "
			select
				group_no,dc_ratio as margin
			from user_group
			where is_wholesale = 'Y'
			order by dc_ratio asc
		";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$group_no = $row->group_no;
			$margin = $row->margin;
			array_push($group_nos,array( "no" => $group_no, "margin" => $margin ));
			$price_cols .= sprintf(" sum(if(p.group_no = %d,p.price,0)) as group_%d_price, \n",$group_no,$group_no);
			$price_cols .= sprintf(" sum(if(p.group_no = %d,round((p.price - g.wonga)/p.price*100),0)) as group_%d_ratio, \n",$group_no,$group_no);
			$price_cols .= sprintf(" sum(if(p.group_no = %d,round((g.price - p.price)/g.price*100),0)) as group_%d_dc_ratio, \n",$group_no,$group_no);
			$print_cols .= sprintf(" %s as group_%d, \n",$group_no,$group_no);
			$print_cols .= sprintf(" '%s' as group_%d_margin, \n",$margin,$group_no);
			$print_cols .= sprintf(" p.group_%d_price as group_%d_price, \n",$group_no,$group_no);
			$print_cols .= sprintf(" p.group_%d_ratio as group_%d_ratio, \n",$group_no,$group_no);
			$print_cols .= sprintf(" p.group_%d_dc_ratio as group_%d_dc_ratio, \n",$group_no,$group_no);
			$group_cnt++;
		};

		$page = $request->input("page", 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		if($ord_field == "sale_qty"){
			$orderby = " order by sale_qty $ord ";
		} else if($ord_field == "now_qty"){
			$orderby = " order by a.qty $ord ";
		} else if($ord_field == "goods_no"){
			$orderby = " order by goods_no $ord,goods_opt ";
		} else if($ord_field == "expect_day"){
			$orderby = " order by expect_day $ord ";
		} else {
			$orderby = " order by buy_ord_prd_no $ord ";
		}

		$data_cnt = 0;
		$page_cnt = 0;
		$sum_buy_qty = 0;
		$sum_buy_cost = 0;
		// 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
		if ($page == 1) {
			$sql =
				"
				select
					count(*) as cnt,sum(p.qty) as qty, sum(p.buy_cost) as buy_cost
				from
					buy_order_product p inner join goods g on p.goods_no = g.goods_no and p.goods_sub = g.goods_sub
					inner join goods_summary s on p.goods_no = s.goods_no and p.goods_sub = s.goods_sub and p.opt = s.goods_opt
					inner join company c on g.com_id = c.com_id
					left outer join goods_stock a on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
					left outer join goods_sale_recent gsr
							on ( a.goods_no = gsr.goods_no and a.goods_sub = gsr.goods_sub and a.goods_opt = gsr.goods_opt )
				where
					1 = 1 $where_a
			";

			$result = DB::select($sql);
			$row = $result[0];
			$data_cnt = $row->cnt;
			$sum_buy_qty = $row->qty;
			$sum_buy_cost = round($row->buy_cost);

			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
			}
		} else {
			$startno = ($page-1) * $page_size;
		}
		
		$limit = " limit $startno,$page_size ";

		$sql = "
			select
				'' as chk,
				d.buy_ord_date,d.buy_ord_no,cd3.code_val as state,d.com_nm, d.opt_kind_cd,o.opt_kind_nm, b.brand_nm, d.style_no,d.org_nm,
				d.goods_no, d.goods_sub,
				'' as img_view, goods_nm,
				cd2.code_val as sale_stat_cl,d.goods_opt,
				ifnull(d.now_qty,0) as now_qty,
				d.buy_qty,d.buy_unit_cost,d.buy_cost,
				d.sale_qty1,d.sale_qty2,d.sale_qty3,d.sale_qty,
				round(d.sale_qty/30,2) as avg_qty,
				d.expect_day,
				d.max_wonga,d.avg_wonga,
				d.tot_wonga,
				date_format(d.last_input_date,'%Y%m%d') as last_input_date,
				d.price, 0 as margin_amt, 0 as margin_rate,
				$print_cols
				concat('$goods_img_url',replace(img,'$cfg_img_size_real','$cfg_img_size_list')) as img,
				d.buy_ord_prd_no
			from (
				select
					p.buy_ord_prd_no,p.buy_ord_date,p.buy_ord_no,p.state,g.goods_no, g.goods_sub, a.goods_opt,
					g.brand,c.com_nm, g.opt_kind_cd,g.style_no,g.org_nm,
					g.goods_nm as goods_nm,img,
					g.sale_stat_cl,
					s.wqty as now_qty, p.qty as buy_qty, p.buy_unit_cost,p.buy_cost,
					a.stock_qty as stock_qty,a.req_date as req_date,
					ifnull(a.maxwonga,g.wonga) as  max_wonga,
					if(ifnull(a.totalwonga,0) > 0,if(s.wqty > 0, a.totalwonga/s.wqty,0),g.wonga) as avg_wonga,
					g.price,g.goods_sh,
					a.totalwonga as tot_wonga,
					a.maxinputdate as last_input_date,
					ifnull(gsr.sale_qty1,0) as sale_qty1,
					ifnull(gsr.sale_qty2,0) as sale_qty2,
					ifnull(gsr.sale_qty3,0) as sale_qty3,
					ifnull(gsr.sale_qty,0) as sale_qty,
					if(a.qty = 0,0,ifnull(round(a.qty/round(sale_qty/30,2),2),999999.00)) as expect_day
				from buy_order_product p inner join goods g on p.goods_no = g.goods_no and p.goods_sub = g.goods_sub
					inner join goods_summary s on p.goods_no = s.goods_no and p.goods_sub = s.goods_sub and p.opt = s.goods_opt
					inner join company c on g.com_id = c.com_id
					left outer join goods_stock a on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
					left outer join goods_sale_recent gsr
							on ( a.goods_no = gsr.goods_no and a.goods_sub = gsr.goods_sub and a.goods_opt = gsr.goods_opt )
				where
					1 = 1 $where_a
				$orderby $limit
			) d left outer join (
					select
						g.goods_no, g.goods_sub,
						$price_cols
						sum(if(p.price > 0,1,0)) as group_cnt
					from goods g inner join goods_price p
						on g.goods_no = p.goods_no and g.goods_sub = p.goods_sub
					where 1=1 $where_p
					group by g.goods_no,g.goods_sub
				) p on d.goods_no = p.goods_no and d.goods_sub = p.goods_sub
				inner join brand b on d.brand = b.brand
				inner join opt o on d.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
				left outer join code cd2 on cd2.code_kind_cd = 'G_GOODS_STAT' and d.sale_stat_cl = cd2.code_id
				left outer join code cd3 on cd3.code_kind_cd = 'G_BUY_ORDER_STATE' and d.state = cd3.code_id
			";

		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$row->margin_amt = $row->price - $row->avg_wonga;	// 마진
			if( $row->price > 0 ) {
				$row->margin_rate = round(($row->margin_amt / $row->price)*100);	// 마진율
			}
			if( $row->expect_day == "999999.00" ) {
				$row->expect_day = "-";
			}
		}

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows,
			"sum_buy_info" => array(
				"sum_buy_qty"	=> $sum_buy_qty,
				"sum_buy_cost"	=> $sum_buy_cost
			)
        ]);
    }
  
    public function changeState(Request $request) {
		$state = $request->input("state");
		$buy_ord_prd_nos = $request->input("buy_ord_prd_nos");
		$status = 200;
		if(count($buy_ord_prd_nos) > 0){
			try {
				for($i=0;$i<count($buy_ord_prd_nos);$i++){
					$buy_ord_prd_no = trim($buy_ord_prd_nos[$i]);
					if($buy_ord_prd_no > 0){
						$sql = "
							update buy_order_product set state = '$state' where buy_ord_prd_no = :buy_ord_prd_no
						";
						DB::delete($sql, ['buy_ord_prd_no' => $buy_ord_prd_no]);
					}
				}
				DB::commit();
				$msg = "발주 상태가 변경되었습니다.";
			} catch(Exception $e) {
				DB::rollback();
				$status = 500;
				$msg = "발주 상태 변경 중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
			}
		}
		return response()->json(['code' => $status, 'msg' => $msg], $status);
    }

    public function delete(Request $request) {
		$buy_ord_prd_nos = $request->input("buy_ord_prd_nos");
		$status = 200;
		if(count($buy_ord_prd_nos) > 0){
			try {
				DB::beginTransaction();
				for($i=0;$i<count($buy_ord_prd_nos);$i++){
					$buy_ord_prd_no = trim($buy_ord_prd_nos[$i]);
					if($buy_ord_prd_no > 0){
						$sql = "
							delete from buy_order_product where buy_ord_prd_no = :buy_ord_prd_no and state < 30
						";
						DB::delete($sql, ['buy_ord_prd_no' => $buy_ord_prd_no]);
					}
				}
				DB::commit();
				$msg = "삭제되었습니다.";
			} catch(Exception $e) {
				DB::rollback();
				$status = 500;
				$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
			}
		}
		return response()->json(['code' => $status, 'msg' => $msg], $status);
    }

	public function showBuy(Request $request) {
		$immutable = CarbonImmutable::now();
        $sdate	= $immutable->sub(3, 'month')->format('Y-m-d');
        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			'items' => SLib::getItems(),
			'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'formula_types' => collect([">", "<", ">=", "<=", "=", "<>"]),
            'month3' => (int)date("m"),
            'month2' => (int)$immutable->sub(1, 'month')->format('m'),
            'month1' => (int)$immutable->sub(2, 'month')->format('m'),
        ];
        return view(Config::get('shop.store.view') . '/cs/cs03_show', $values);
	}

	public function searchBuy(Request $request) {

		/**
		 * 설정값 얻기
		 */
        $conf = new Conf();
        $cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
		$cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");
        $cfg_domain_img			= $conf->getConfigValue("shop","domain_img");
        if($cfg_domain_img != ""){
			$goods_img_url = sprintf("http://%s",$cfg_domain_img);
		} else {
			$goods_img_url = "";
		}

		/**
		 * inputs
		 */
		$opt_kind_cd = $request->input("item");
		$brand_nm = $request->input("brand_nm");
		$brand_cd = $request->input("brand_cd");
		$com_type = $request->input("com_type");
		$com_id = $request->input("com_id");
		$style_no = $request->input("style_no");
		$style_nos = $request->input("style_nos");
		$goods_stat = $request->input("goods_stat");
		$ex_trash = $request->input("ex_trash");
		$ex_soldout = $request->input("ex_soldout");
		$goods_nm = $request->input("goods_nm");
		$formula_type = $request->input("formula_type");
		$formula_val = $request->input("formula_val");
		$apply_avg_wonga = $request->input("apply_avg_wonga");
        $ord_field = $request->input("ord_field");
		$ord = $request->input("ord");
		$limit = $request->input("limit");

		/**
		 * query 작성
		 */
		$where = "";
		$where_a = "";
		$where_p = "";

		if( $goods_nm != "" ){
			$where_a .= " and g.goods_nm like '%$goods_nm%' ";
			$where_p .= " and g.goods_nm like '%$goods_nm%' ";
		}
		if( $com_type != "" ){
			$where_a .= " and c.com_type = '$com_type' ";
		}
		if( $com_id != "" ){
			$where_a .= " and g.com_id = '$com_id' ";
			$where_p .= " and g.com_id = '$com_id' ";
		}
		if( $opt_kind_cd != "" ){
			$where_a .= " and g.opt_kind_cd = '$opt_kind_cd'";
			$where_p .= " and g.opt_kind_cd = '$opt_kind_cd'";
		}
		if( $goods_stat != "" ){
			$where_a .= " and g.sale_stat_cl = '$goods_stat'";
			$where_p .= " and g.sale_stat_cl = '$goods_stat'";
		}
        if( $style_nos != "" ) {
			$style_no = $style_nos;
		}
		$style_no = preg_replace("/\s/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\t/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\n/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);

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
					$where_a .= " and g.style_no in ( $in_style_nos ) ";
					$where_p .= " and g.style_no in ( $in_style_nos ) ";
				}
			} else {
				$where_a .= " and g.style_no like '$style_no%' ";
				$where_p .= " and g.style_no like '$style_no%' ";
			}
		}

		if( $ex_trash == "Y" ){
			$where_a .= " and g.sale_stat_cl > 0 ";
			$where_p .= " and g.sale_stat_cl > 0 ";
		}

		if( $ex_soldout == "Y" ){
			$where_a .= " and a.qty > 0 ";
		}

		if( $brand_cd != "" ){
			$where_a .= " and g.brand = '$brand_cd' ";
			$where_p .= " and g.brand = '$brand_cd' ";
		} else if ($brand_cd == "" && $brand_nm != ""){
			$where_a .= " and g.brand ='$brand_cd'";
			$where_p .= " and g.brand ='$brand_cd'";
		}

		if( $formula_val != "" ) {
			$where_a .= "  and sale_qty $formula_type $formula_val ";
		}

		$price_cols = "";
		$print_cols = "";
		$group_cnt = 0;
		$group_nos = array();
		$sql = "
			select
				group_no,dc_ratio as margin
			from user_group
			where is_wholesale = 'Y'
			order by dc_ratio asc
		";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$group_no = $row->group_no;
			$margin = $row->margin;
			array_push($group_nos,array( "no" => $group_no, "margin" => $margin ));
			$price_cols .= sprintf(" sum(if(p.group_no = %d,p.price,0)) as group_%d_price, \n",$group_no,$group_no);
			$price_cols .= sprintf(" sum(if(p.group_no = %d,round((p.price - g.wonga)/p.price*100),0)) as group_%d_ratio, \n",$group_no,$group_no);
			$price_cols .= sprintf(" sum(if(p.group_no = %d,round((g.price - p.price)/g.price*100),0)) as group_%d_dc_ratio, \n",$group_no,$group_no);
			$print_cols .= sprintf(" %s as group_%d, \n",$group_no,$group_no);
			$print_cols .= sprintf(" '%s' as group_%d_margin, \n",$margin,$group_no);
			$print_cols .= sprintf(" p.group_%d_price as group_%d_price, \n",$group_no,$group_no);
			$print_cols .= sprintf(" p.group_%d_ratio as group_%d_ratio, \n",$group_no,$group_no);
			$print_cols .= sprintf(" p.group_%d_dc_ratio as group_%d_dc_ratio, \n",$group_no,$group_no);
			$group_cnt++;
		};

		$page = $request->input("page", 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		if($ord_field == "sale_qty"){
			$orderby = " order by sale_qty $ord ";
		} else if($ord_field == "now_qty"){
			$orderby = " order by a.qty $ord ";
		} else if($ord_field == "goods_no"){
			$orderby = " order by goods_no $ord, goods_opt ";
		} else if($ord_field == "req_date"){
			$orderby = " order by a.req_date $ord ";
		} else {
			$orderby = " order by expect_day $ord, goods_no ";
		}

		$data_cnt = 0;
		$page_cnt = 0;
		// 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
		if ($page == 1) {
			$sql =
				"
				select
					count(*) as cnt
				from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
					inner join company c on g.com_id = c.com_id
					left outer join goods_stock a on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
					left outer join goods_sale_recent gsr
							on ( a.goods_no = gsr.goods_no and a.goods_sub = gsr.goods_sub and a.goods_opt = gsr.goods_opt )
				where
					1 = 1 $where_a
			";

			$result = DB::select($sql);
			$row = $result[0];
			$data_cnt = $row->cnt;

			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
			}
		} else {
			$startno = ($page-1) * $page_size;
		}
		
		$limit = "";
		if($page_size < 9999){
			$limit = " limit $startno,$page_size ";
		}

		$sql = "
				select
					'' as chk, d.com_nm, d.opt_kind_cd,o.opt_kind_nm, b.brand_nm, d.style_no,d.org_nm,
					d.goods_no, d.goods_sub,
					'' as img_view, goods_nm,
					cd2.code_val as sale_stat_cl,d.goods_opt,
					d.wqty,
					( d.sale_qty1 + d.sale_qty2 + d.sale_qty3 ) / 3 - ifnull(d.wqty,0) as exp_buy_qty,
					0 as qty,
					if('$apply_avg_wonga' = 'Y',avg_wonga,0) as buy_unit_cost,
					0 as buy_cost,
					d.sale_qty1,d.sale_qty2,d.sale_qty3,d.sale_qty,
					round(d.sale_qty/30,2) as avg_qty,
					d.expect_day,
					d.max_wonga,
					d.avg_wonga,
					d.tot_wonga,
					date_format(d.last_input_date,'%Y%m%d') as last_input_date,
					d.price, 0 as margin_amt, 0 as margin_rate,
					$print_cols
					-- (d.price * d.wqty) as tot_sales,0 as tot_margin,
					concat('$goods_img_url',replace(img,'$cfg_img_size_real','$cfg_img_size_list')) as img
				from (
					select
						g.goods_no, g.goods_sub, s.goods_opt,
						g.brand,c.com_nm, g.opt_kind_cd,g.style_no,g.org_nm, g.goods_nm,img,
						g.sale_stat_cl,
						s.wqty,
						a.stock_qty as stock_qty,a.req_date as req_date,
						ifnull(a.maxwonga,g.wonga) as  max_wonga,
						if(ifnull(a.totalwonga,0) > 0,if(s.wqty > 0, a.totalwonga/s.wqty,0),g.wonga) as avg_wonga,
						g.price,g.goods_sh,
						ifnull(a.totalwonga,0) as tot_wonga,
						a.maxinputdate as last_input_date,
						ifnull(gsr.sale_qty1,0) as sale_qty1,
						ifnull(gsr.sale_qty2,0) as sale_qty2,
						ifnull(gsr.sale_qty3,0) as sale_qty3,
						ifnull(gsr.sale_qty,0) as sale_qty,
						if(a.qty = 0,0,ifnull(round(a.qty/round(sale_qty/30,2),2),999999.00)) as expect_day
					from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
						inner join company c on g.com_id = c.com_id
						left outer join goods_stock a on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
						left outer join goods_sale_recent gsr
								on ( a.goods_no = gsr.goods_no and a.goods_sub = gsr.goods_sub and a.goods_opt = gsr.goods_opt )
					where
						1 = 1 $where_a
					$orderby $limit
				) d left outer join (
						select
							g.goods_no, g.goods_sub,
							$price_cols
							sum(if(p.price > 0,1,0)) as group_cnt
						from goods g inner join goods_price p
							on g.goods_no = p.goods_no and g.goods_sub = p.goods_sub
						where 1=1 $where_p
						group by g.goods_no,g.goods_sub
					) p on d.goods_no = p.goods_no and d.goods_sub = p.goods_sub
					inner join brand b on d.brand = b.brand
					inner join opt o on d.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
					inner join code cd2 on ( cd2.code_kind_cd = 'G_GOODS_STAT' and d.sale_stat_cl = cd2.code_id )
			";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			if( $row->exp_buy_qty < 0 ) $row->exp_buy_qty = 0;
			$row->margin_amt = $row->price - $row->avg_wonga;	// 마진
			if( $row->price > 0 ) {
				$row->margin_rate = round(($row->margin_amt / $row->price)*100);	// 마진율
			}
			if( $row->expect_day == "999999.00" ) {
				$row->expect_day = "-";
			}
		}

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);
	}

	public function addBuy(Request $request) {
		$data = $request->input('data');
		$products = explode("\n", $data);
		$buy_ord_date = date("Ymd");
		$state = 10;
		$buy_ord_date = explode(" ", now());
		$buy_ord_date = str_replace('-','',$buy_ord_date[0]);
		$user_id = Auth('head')->user()->id;
		if(count($products) > 0){
			try {
				DB::beginTransaction();
				for($i=0;$i<count($products);$i++){
					$rows = explode("\t",$products[$i]);
					$goods_no = $rows[0];
					$goods_sub = $rows[1];
					$goods_opt = $rows[2];
					$qty = $rows[3];
					$buy_unit_cost = $rows[4];
					$opt_kind_nm = $rows[5];

					if($goods_no > 0){
						$qty = str_replace(",","",str_replace("\\","",trim($qty)));
						$buy_unit_cost = str_replace(",","",str_replace("\\","",trim($buy_unit_cost)));
						$buy_cost = $buy_unit_cost * $qty;

						$sql = "
							select style_no, com_id 
							from goods 
							where goods_no = '$goods_no' and goods_sub = '$goods_sub'
						";
						$row = DB::selectOne($sql);

						$style_no = $row->style_no;
						$com_id = $row->com_id;
						$buy_ord_no = sprintf("%s_%s",$com_id,$buy_ord_date);
							$sql = "
							insert into buy_order_product
							( buy_ord_no, com_id, item, style_no,goods_no,goods_sub,opt,qty,buy_unit_cost,buy_cost,state,buy_ord_date,rt,ut ) values
							( '$buy_ord_no', '$com_id','$opt_kind_nm', '$style_no','$goods_no','$goods_sub','$goods_opt','$qty','$buy_unit_cost','$buy_cost','$state','$buy_ord_date',now(),now())
						";
						DB::insert($sql);
						

						$buy_ord_no2 = sprintf("%s_%s",$com_id,$buy_ord_date);
						$sql2 = "
							insert into buy_order ( buy_ord_no, buy_ord_date, com_id, item, id, rt ) 
							values('$buy_ord_no2','$buy_ord_date', '$com_id', '$opt_kind_nm','$user_id', now() )
						";
						DB::insert($sql2);

					}		
				}
				DB::commit();
				return response()->json(['message' => 'created'], 201);
			} catch (Exception $e) {
				DB::rollBack();
				return response()->json(['message' => $e->getMessage()], 500);
			}
		}
    }

}