<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Matrix\Exception;

class stk36Controller extends Controller
{
	//
	public function index() {

		$com_types = SLib::getCodes("G_COM_TYPE");
		$goods_stats  = SLib::getCodes("G_GOODS_STAT");

		$values = [
			"com_types" => $com_types,
			'items' => SLib::getItems(),
			"goods_stats"	=> $goods_stats
		];

		return view( Config::get('shop.head.view') . '/stock/stk36',$values);
	}

	public function search(Request $request)
	{
		$qty_type		= $request->input("qty_type","goods");
		$qty_buffer_cnt	= $request->input("qty_buffer_cnt","");
		$exp0			= $request->input("exp0","N");
		$bizest_qty		= $request->input("bizest_qty");
		$s_goods_stat	= $request->input("s_goods_stat");
		$s_style_no		= $request->input("style_no");
		$s_goods_no		= $request->input("goods_no");
		$s_opt_kind_cd	= $request->input("s_opt_kind_cd");
		$s_goods_nm		= $request->input("goods_nm");

		$where	= "";
		$where_group	= "";

		if( $qty_buffer_cnt != "" )
		{
			if( $qty_buffer_cnt >= 0 )
			{
				if( $qty_type == "opt" )	$where	.= " and ( a.xmd_qty - a.bizest_qty ) >= $qty_buffer_cnt ";
				else						$where_group	.= " and ( aa.xmd_qty - aa.bizest_qty ) >= $qty_buffer_cnt ";
			}
			else
			{
				if( $qty_type == "opt" )	$where	.= " and ( a.xmd_qty - a.bizest_qty ) <= $qty_buffer_cnt ";
				else						$where_group	.= " and ( aa.xmd_qty - aa.bizest_qty ) <= $qty_buffer_cnt ";
			}
		}
		if( $exp0 == "Y" )			$where	.= " and a.xmd_qty > 0 ";
		if( $bizest_qty != "" )
		{
			if( $qty_type == "opt" )	$where	.= " and a.bizest_qty <= $bizest_qty ";
			else						$where_group	.= " and aa.bizest_qty <= $bizest_qty ";
		}
		if( $s_goods_stat != "" )	$where .= " and b.sale_stat_cl = '" . Lib::quote($s_goods_stat) . "' ";
		if( $s_style_no	!= "" )		$where .= " and b.style_no like '" . Lib::quote($s_style_no) . "%' ";
		if( $s_goods_no != "" )
		{
			$goods_nos	= explode(",",$s_goods_no);

			if( count($goods_nos) > 1 )
			{
				if( count($goods_nos) > 50 )	array_splice($goods_nos,50);
				$in_goods_nos	= join(",",$goods_nos);
				$where	.= " and a.goods_no in ( $in_goods_nos ) ";
			}
			else
			{
				$where .= " and a.goods_no = '" . Lib::quote($s_goods_no) . "' ";
			}
		}
		if( $s_opt_kind_cd != "" )	$where .= " and b.opt_kind_cd = '" . Lib::quote($s_opt_kind_cd) . "' ";
		if( $s_goods_nm != "" )		$where .= " and b.goods_nm like '%" . Lib::quote($s_goods_nm) . "%' ";

		if( $qty_type == "opt" )
		{
			$sql	= "
				select
					a.goods_no, b.head_desc, b.goods_nm, a.goods_opt, stat.code_val as sale_stat_cl_val, a.xmd_qty, a.bizest_qty, (a.xmd_qty - a.bizest_qty) as qty_term,
					( select count(*) from order_opt where goods_no = a.goods_no and goods_opt = a.goods_opt and ord_state = '30' and (now() - interval 3 month) <= ord_date ) as month_ord,
					( select count(*) from order_opt where goods_no = a.goods_no and goods_opt = a.goods_opt and ord_state = '30' ) as tot_ord,
					date_format(a.regdate,'%Y.%m.%d') as rt
				from goods_xmd_monitor a
				inner join goods b on a.goods_no = b.goods_no
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = stat.code_id
				where 1=1  $where
				order by a.goods_no desc
			";
		}
		else
		{
			$sql	= "
				select
					aa.goods_no, aa.head_desc, aa.goods_nm, aa.goods_opt, aa.sale_stat_cl_val, aa.xmd_qty, aa.bizest_qty, aa.qty_term,
					( select count(*) from order_opt where goods_no = aa.goods_no and ord_state = '30' and (now() - interval 3 month) <= ord_date ) as month_ord,
					( select count(*) from order_opt where goods_no = aa.goods_no and ord_state = '30' ) as tot_ord,
					aa.regdate as rt
				from
				(
					select
						a.goods_no, b.head_desc, b.goods_nm, '' as goods_opt, stat.code_val as sale_stat_cl_val, sum(a.xmd_qty) as xmd_qty, sum(a.bizest_qty) as bizest_qty, (sum(a.xmd_qty) - sum(a.bizest_qty)) as qty_term, date_format(a.regdate,'%Y.%m.%d') as regdate
					from goods_xmd_monitor a
					inner join goods b on a.goods_no = b.goods_no
					left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = stat.code_id
					where 1=1  $where
					group by a.goods_no
				) aa
				where
					1=1 $where_group
				order by aa.goods_no desc
			";
		}

		$result = DB::select($sql);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result)
			),
			"body" => $result
		]);

	}

	function SearchCompany(Request $request){

		$S_OPT_KIND_CD	= $request->input("item");	//품목
		$S_BRAND_NM		= $request->input("brand");	//브랜드명
		$S_BRAND_CD		= $request->input("brand_cd");	//브랜드코드
		$S_COM_TYPE		= $request->input("com_type");	//업체 타입
		$S_COM_NM		= $request->input("com_nm");		//업체명
		$S_COM_ID		= $request->input("com_id");		//업체코드
		$S_GOODS_STAT	= $request->input("goods_stat");	//상품상태
		$S_STYLE_NO		= $request->input("style_no");	//스타일번호
		$S_GOODS		= $request->input("goods_no");	//상품번호
		$S_GOODS_NM		= $request->input("goods_nm");	//상품명

		$where = "";

		if ($S_OPT_KIND_CD != "" )	$where .= " and g.opt_kind_cd = '$S_OPT_KIND_CD' ";
		if ($S_BRAND_CD != ""){
			$where .= " and g.brand = '$S_BRAND_CD' ";
		} else if ($S_BRAND_CD == "" && $S_BRAND_NM != ""){
			$where .= " and g.brand ='$S_BRAND_CD'";
		}
		if ($S_COM_TYPE != "" )		$where .= " and g.com_type = '$S_COM_TYPE' ";
		if ($S_COM_ID != "")		$where .= " and g.com_id = '$S_COM_ID' ";
		if( is_array($S_GOODS_STAT)) {
			if (count($S_GOODS_STAT) == 1 && $S_GOODS_STAT[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($S_GOODS_STAT[0]) . "' ";
			} else if (count($S_GOODS_STAT) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $S_GOODS_STAT) . ") ";
			}
		} else if($S_GOODS_STAT != ""){
			$where .= " and g.sale_stat_cl = '" . Lib::quote($S_GOODS_STAT) . "' ";
		}
		if ($S_STYLE_NO != "" )		$where .= " and g.style_no like '$S_STYLE_NO%' ";

		$goods_no = "";
		if($S_GOODS != ""){
			$goods_no = $S_GOODS;
		}

		$goods_no = preg_replace("/\s/",",",$goods_no);
		$goods_no = preg_replace("/\t/",",",$goods_no);
		$goods_no = preg_replace("/\n/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);

		if( $goods_no != "" ){
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				// if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos = join(",", array_filter($goods_nos, function($s) { return is_numeric($s); }));
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if (is_numeric($goods_no)) $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
				else $where .= " and 1!=1 ";
			}
		}

		if ($S_GOODS_NM != "" )		$where .= " and g.goods_nm like '%$S_GOODS_NM%' ";

		// 합계변수
		$sum_goods_cnt = 0;
		$sum_good_qty = 0;
		$sum_wqty = 0;
		$sum_wonga = 0;

		$rows = [];
		$data = [];

		try {
			$sql = "
                select
                    a.com_id,
                    a.com_nm,
                    a.com_type,
                    cd.code_val as com_type_nm,
                    a.goods_cnt,
                    a.qty, a.wqty, a.t_wonga
                from (
                    select
                        c.com_id,
                        c.com_nm,
                        c.com_type,
                        count(distinct(g.goods_no)) as goods_cnt,
                        sum(good_qty) as qty, sum(wqty) as wqty,
                        sum(t_wonga) as t_wonga
                     from (
                        select s.goods_no, s.goods_sub, s.good_qty, s.wqty , 0 as t_wonga
                        from goods_summary s
                        union all
                        select a.goods_no, a.goods_sub, 0 as good_qty, 0 as wqty , a.wonga * a.qty as t_wonga
                        from goods_good a
                        where qty > 0
                    ) a inner join goods g on g.goods_no = a.goods_no and g.goods_sub = a.goods_sub
                        inner join company c on c.com_id = g.com_id
                    where 1=1 $where
                    group by com_id
                ) a inner join code cd on cd.code_kind_cd = 'G_COM_TYPE' and a.com_type = cd.code_id
                order by a.com_id desc
            ";

			$result = DB::select($sql);

			foreach($result as $rs) {
				$sum_goods_cnt += $rs->goods_cnt;
				$sum_good_qty += $rs->qty;
				$sum_wqty += $rs->wqty;
				$sum_wonga += $rs->t_wonga;
				array_push($rows, $rs);
			}

			$sum_array = array(
				"com_id" => "",
				"com_nm" => "합계",
				"com_type" => "",
				"code_val" => "",
				"goods_cnt" => $sum_goods_cnt,
				"qty" => $sum_good_qty,
				"wqty" => $sum_wqty,
				"t_wonga" => $sum_wonga
			);

			array_push($data, $sum_array);

			for($i = 0; $i < count($rows); $i++){
				array_push($data, $rows[$i]);
			}

			return response()->json([
				"code"	=> 200,
				"head"	=> array(
					"total"		=> count($result)
				),
				"body" => $data
			]);

		} catch (Exception $e) {
			return response()->json([
				"code"	=> 200,
				"msg" => $e->getMessage()
			]);
		}

	}

	function SearchItemnBrand(Request $request){

		$S_OPT_KIND_CD	= $request->input("item");	//품목
		$S_BRAND_NM		= $request->input("brand");	//브랜드명
		$S_BRAND_CD		= $request->input("brand_cd");	//브랜드코드
		$S_COM_TYPE		= $request->input("com_type");	//업체 타입
		$S_COM_NM		= $request->input("com_nm");		//업체명
		$S_COM_ID	    = $request->input("com_id");		//업체코드
		$S_GOODS_STAT	= $request->input("goods_stat");	//상품상태
		$S_STYLE_NO		= $request->input("style_no");	//스타일번호
		$S_GOODS			= $request->input("goods_no");	//상품번호
		$S_GOODS_NM		= $request->input("goods_nm");	//상품명

		$where = "";

		if ($S_OPT_KIND_CD != "" )	$where .= " and g.opt_kind_cd = '$S_OPT_KIND_CD' ";
		if ($S_BRAND_CD != "" ){
			$where .= " and g.brand = '$S_BRAND_CD' ";
		} else if ($S_BRAND_CD == "" && $S_BRAND_NM != ""){
			$where .= " and g.brand ='$S_BRAND_CD'";
		}

		if ($S_COM_TYPE != "" )		$where .= " and g.com_type = '$S_COM_TYPE' ";
		if ($S_COM_ID != "" )		$where .= " and c.com_id = '$S_COM_ID' ";
		if( is_array($S_GOODS_STAT)) {
			if (count($S_GOODS_STAT) == 1 && $S_GOODS_STAT[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($S_GOODS_STAT[0]) . "' ";
			} else if (count($S_GOODS_STAT) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $S_GOODS_STAT) . ") ";
			}
		} else if($S_GOODS_STAT != ""){
			$where .= " and g.sale_stat_cl = '" . Lib::quote($S_GOODS_STAT) . "' ";
		}
		if ($S_STYLE_NO != "" )		$where .= " and g.style_no like '$S_STYLE_NO%' ";
		$goods_no = "";

		if($S_GOODS != ""){
			$goods_no = $S_GOODS;
		}

		$goods_no = preg_replace("/\s/",",",$goods_no);
		$goods_no = preg_replace("/\t/",",",$goods_no);
		$goods_no = preg_replace("/\n/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);

		if( $goods_no != "" ){
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				// if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos = join(",", array_filter($goods_nos, function($s) { return is_numeric($s); }));
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				if (is_numeric($goods_no)) $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
				else $where .= " and 1!=1 ";
			}
		}
		if ($S_GOODS_NM != "" )		$where .= " and g.goods_nm like '%$S_GOODS_NM%' ";

		$sum_goods_cnt = 0;
		$sum_good_qty = 0;
		$sum_wqty = 0;
		$sum_wonga = 0;

		try {

			$sql = "
			    select * from (
                    select
                        o.opt_kind_cd, o.opt_kind_nm, b.brand as brand ,b.brand_nm,
                        sum(goods_cnt) as goods_cnt,
                        ifnull(sum(qty), 0) as t_qty,
                        ifnull(sum(wqty), 0) as t_wqty,
                        ifnull(sum(t_wonga), 0) as t_wonga
                    from (
                        select
                            opt_kind_cd,g.brand,count(distinct(g.goods_no)) as goods_cnt,sum(good_qty) as qty, sum(wqty) as wqty, sum(t_wonga) as t_wonga
                         from (
                            select s.goods_no, s.goods_sub, s.good_qty, s.wqty , 0 as t_wonga
                            from goods_summary s
                            union all
                            select a.goods_no, a.goods_sub, 0 as good_qty, 0 as wqty , a.wonga * a.qty as t_wonga
                            from goods_good a
                            where qty > 0
                        ) a inner join goods g on g.goods_no = a.goods_no and g.goods_sub = a.goods_sub
                            inner join company c on c.com_id = g.com_id
                        where 1=1 $where
                        group by opt_kind_cd,brand
                    ) a inner join opt o on a.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
                        inner join brand b on a.brand = b.brand
                    group by a.opt_kind_cd,a.brand
                ) a order by opt_kind_cd, brand
            ";

			$result = DB::select($sql);

			foreach($result as $rs) {
				$sum_goods_cnt += $rs->goods_cnt;
				$sum_good_qty += $rs->t_qty;
				$sum_wqty += $rs->t_wqty;
				$sum_wonga += $rs->t_wonga;
			}

			$sum_array = array(
				"opt_kind_nm" => "합계",
				"brand_nm" => "합계",
				"goods_cnt" => $sum_goods_cnt,
				"t_qty" => $sum_good_qty,
				"t_wqty" => $sum_wqty,
				"t_wonga" => $sum_wonga
			);


			return response()->json([
				"code"	=> 200,
				"head"	=> array(
					"total"		=> count($result),
					"total_row" => $sum_array
				),
				"body" => $result
			]);

		} catch (Exception $e) {
			return response()->json([
				"code"	=> 200,
				"msg" => $e->getMessage()
			]);
		}
	}

	function SearchGoodsByCom (Request $request) {
		//검색 폼

		$S_OPT_KIND_CD	= $request->input("item");	//품목
		$S_BRAND_NM		= $request->input("brand");	//브랜드명
		$S_BRAND_CD		= $request->input("brand_cd");	//브랜드코드
		$S_COM_TYPE		= $request->input("com_type");	//업체 타입
		$S_COM_NM		= $request->input("com_nm");		//업체명
		$S_COM_ID		= $request->input("com_id");		//업체코드
		$S_GOODS_STAT	= $request->input("goods_stat");	//상품상태
		$S_STYLE_NO		= $request->input("style_no");	//스타일번호
		$S_GOODS		= $request->input("goods_no");	//상품번호
		$S_GOODS_NM		= $request->input("goods_nm");	//상품명
		$LIMIT			= $request->input("limit");			//출력수
		$ORD_FIELD		= $request->input("ord_field");		//정렬필드
		$ORD			= $request->input("ord");			//정렬

		//그리드 값
		$OPT_KIND_CD	= $request->input("opt_kind_cd");	//품목
		$BRAND			= $request->input("brand");		//브랜드명
		$COM_ID			= $request->input("com_id");	//브랜드명

		$where = "";

		if ($S_COM_TYPE != "" )		$where .= " and g.com_type = '$S_COM_TYPE' ";
		if ($COM_ID != ""){
			$where .= " and g.com_id = '$COM_ID' ";
		} else {
			if ($S_COM_ID != "" )	$where .= " and g.com_id = '$S_COM_ID' ";
		}
		if ($S_GOODS_NM != "" )		$where .= " and g.goods_nm like '%$S_GOODS_NM%' ";
		if ($S_GOODS != "" )		$where .= " and g.goods_no = '$S_GOODS' ";
		if ($S_STYLE_NO != "" )		$where .= " and g.style_no like '$S_STYLE_NO%' ";
		if($OPT_KIND_CD != "") {
			$where .= " and g.opt_kind_cd = '$OPT_KIND_CD' ";
		} else {
			if ($S_OPT_KIND_CD != "" )	$where .= " and g.opt_kind_cd = '$S_OPT_KIND_CD' ";
		}

		if ($S_BRAND_CD != "" ){
			$where .= " and g.brand = '$S_BRAND_CD' ";
		} else if ($S_BRAND_CD == "" && $S_BRAND_NM != ""){
			$where .= " and g.brand ='$S_BRAND_CD'";
		}

		if( is_array($S_GOODS_STAT)) {
			if (count($S_GOODS_STAT) == 1 && $S_GOODS_STAT[0] != "") {
				$where .= " and g.sale_stat_cl = '" . Lib::quote($S_GOODS_STAT[0]) . "' ";
			} else if (count($S_GOODS_STAT) > 1) {
				$where .= " and g.sale_stat_cl in (" . join(",", $S_GOODS_STAT) . ") ";
			}
		} else if($S_GOODS_STAT != ""){
			$where .= " and g.sale_stat_cl = '" . Lib::quote($S_GOODS_STAT) . "' ";
		}

		$page = $request->input("page",1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $LIMIT; //_PAGE_SIZE;


		try {

			$sql = "
                    select count(*) as cnt
                    from goods g
                        inner join company c on c.com_id = g.com_id
                        inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
                    where 1=1
                        $where
			    ";

			$total_cnt = DB::selectOne($sql);
			$data_cnt = $total_cnt->cnt;

			// 페이지 얻기
			$page_cnt = (int)(($data_cnt-1)/$page_size) + 1;


			$startno = ($page-1) * $page_size;
			$arr_header = array("total"=> $data_cnt, "page" => $page, "page_cnt"=>$page_cnt);;


			if($LIMIT == -1){
				$LIMIT = "";
			} else {
				$LIMIT = " limit $startno, $page_size ";
			}

			$columns = '';
			$inner_columns = '';

			$sql = "
                 select code_id from code
                 where code_kind_cd = 'G_STOCK_LOC' and code_id <> 'LOC' and use_yn = 'y'
                 order by code_seq
            ";
			$rs = DB::select($sql);

			foreach ($rs as $ele) {
				$columns .= sprintf("ifnull(a.qty_%s,0) as 'wqty_%s',\n",$ele->code_id, $ele->code_id);
				$inner_columns .= sprintf("sum(if(b.loc = '%s',qty,0)) as 'qty_%s',\n", $ele->code_id, $ele->code_id);
			}

			$sql = "
                select
                        c.com_nm, o.opt_kind_nm, b.brand_nm, g.style_no,
                        cd.code_val as goods_type_nm, cd2.code_val as is_unlimited_nm,
                        g.goods_no, g.goods_sub, g.goods_nm,
                        cd3.code_val as sale_stat_cl_nm,
                        g.wonga,
                        a.goods_opt,
                        ifnull(if(g.is_unlimited = 'Y', '∞', s.good_qty), 0) as good_qty,
                        ifnull(s.wqty,0) as wqty,
                        ifnull(s.wqty,0) - ifnull(a.qty,0) as wqty_LOC,
                        $columns
                        g.goods_location,
                        ifnull(if(g.is_unlimited = 'Y', '-', s.good_qty), 0) as edit_good_qty,
                        ifnull(s.wqty, 0) as edit_wqty,
                        g.is_unlimited, g.sale_stat_cl
                from (
                        select
                                a.goods_no, a.goods_sub, a.goods_opt,
                                $inner_columns
                                sum(qty) as qty
                        from (
                                select
                                        s.goods_no, s.goods_sub, s.goods_opt
                                from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
                                        inner join company c on c.com_id = g.com_id
                                where 1=1 $where
                                $LIMIT
                        ) a left outer join goods_location b on a.goods_no = b.goods_no
                                        and a.goods_sub = b.goods_sub and a.goods_opt = b.goods_opt
                        group by a.goods_no, a.goods_sub, a.goods_opt
                ) a
                inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
                inner join goods_summary s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
                inner join company c on c.com_id = g.com_id
                left outer join brand b on g.brand = b.brand
                inner join opt o on g.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
                inner join code cd on cd.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = cd.code_id
                left outer join code cd2 on cd2.code_kind_cd = 'G_IS_UNLIMITED' and g.is_unlimited = cd2.code_id
                inner join code cd3 on cd3.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = cd3.code_id
                order by $ORD_FIELD $ORD
            ";

			$list = DB::select($sql);

			return response()->json([
				"code"	=> 200,
				"head"	=> $arr_header,
				"body" => $list
			]);

		} catch (Exception $e) {

		}
	}
}
