<?php

namespace App\Http\Controllers\head\product;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prd10Controller extends Controller
{
    public function index() {
        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),

        ];
        return view( Config::get('shop.head.view') . '/product/prd10',$values);
    }

    public function search(Request $request){

        $cat_type = $request->input("cat_type",'DISPLAY');

        $query = /** @lang text */
            "
			select
				c.d_cat_nm
				, floor(length(c.d_cat_cd)/3) as level
				, ifnull(b.cnt, 0) as cnt
				, ifnull(b.30_cnt, 0) as 30_cnt
				, ifnull(b.40_cnt, 0) as 40_cnt
				, ifnull(b.display_cnt, 0) as display_cnt
				, if(ifnull(c.sort_opt, 'A') = 'A', '자동', '수동') as sort_opt
				, c.product_brand, c.product_gender, c.product_item, c.product_outlet_yn
				, 0 as product_match_cnt
				, ifnull(c.auth, 'A') as auth
				, c.use_yn
				, c.regi_date
				, c.upd_date
				, c.cat_type
				, c.d_cat_cd
				, c.full_nm
				, (
					select if(count(*) >0, length(c.d_cat_cd)+1, length(c.d_cat_cd))
					from category
					where cat_type = '$cat_type' and p_d_cat_cd = c.d_cat_cd limit 0,1
				) as mx_len
				, (select count(d_cat_cd) from category where cat_type = '$cat_type' and p_d_cat_cd = c.d_cat_cd) as child_cnt
			from category c
				left outer join (
					select
						c.cat_type, c.d_cat_cd,
						sum(if(g.sale_stat_cl = '30', 1, 0 )) as 30_cnt,
						sum(if(g.sale_stat_cl = '40', 1, 0)) as 40_cnt,
						sum(if(g.sale_stat_cl = '10' or g.sale_stat_cl = '20' or g.sale_stat_cl = '40', 1, 0)) as display_cnt,
						count(*) as cnt
					from category c
						inner join category_goods cg on c.cat_type = cg.cat_type and c.d_cat_cd = cg.d_cat_cd
						inner join goods g on cg.goods_no = g.goods_no and cg.goods_sub = g.goods_sub
					where c.cat_type = '$cat_type'
					group by c.cat_type, c.d_cat_cd
				) b on c.cat_type = b.cat_type and c.d_cat_cd = b.d_cat_cd
			where c.cat_type = '$cat_type' and c.use_yn = 'Y'
			order by c.seq, c.d_cat_cd
        ";
            //echo "<pre>$query</pre>";
        $result = DB::select($query);

		foreach ($result as $row) {
			
			if( $row->product_brand != '' || $row->product_gender != '' || $row->product_item != '' || $row->product_outlet_yn == 'Y' ){

				$brand_query 	= "";
				$gender_query 	= "";
				$item_query 	= "";
				$outlet_query	= "";
				
				if( $row->product_brand != '' ){
					$product_brand	= explode(',', $row->product_brand);
					$brand_query	= " and pc.brand in (";
					for($i = 0; $i < count($product_brand); $i++ ){
						if($i > 0) $brand_query	.= ",";
						$brand_query	.= "'" . $product_brand[$i] . "'";
					}
					$brand_query	.= ") ";
				}

				if( $row->product_gender != '' ){
					$product_gender	= explode(',', $row->product_gender);
					$gender_query	= " and pc.gender in (";
					for($i = 0; $i < count($product_gender); $i++ ){
						if($i > 0) $gender_query	.= ",";
						$gender_query	.= "'" . $product_gender[$i] . "'";
					}
					$gender_query	.= ") ";
				}

				if( $row->product_item != '' ){
					$product_item	= explode(',', $row->product_item);
					$item_query	= " and pc.item in (";
					for($i = 0; $i < count($product_item); $i++ ){
						if($i > 0) $item_query	.= ",";
						$item_query	.= "'" . $product_item[$i] . "'";
					}
					$item_query	.= ") ";
				}

				if( $row->product_outlet_yn == 'Y' ){
					$outlet_query	.= " and (p.tag_price - p.price)/p.tag_price >= 0.2 ";
				}else{
					$outlet_query	.= " and (p.tag_price - p.price)/p.tag_price <= 0.3 ";
				}
				
				$sql_product	= "
					select
						count(distinct pc.goods_no) as tot
					from product_code pc
					inner join product p on pc.prd_cd = p.prd_cd
					inner join product_stock_storage pss on pc.prd_cd = pss.prd_cd
					inner join goods g on pc.goods_no = g.goods_no
					where
						1 = 1
						$brand_query
						$gender_query
						$item_query
						$outlet_query
						and pss.wqty > 0
				";
				$row->product_match_cnt	= DB::selectOne($sql_product)->tot;
			}
		}
		
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
            ),
            "body" => $result
        ]);
    }

    public function show($cat_type, $d_cat_cd, Request $request)
    {
        $d_cat_nm = DB::table('category')->where('d_cat_cd', $d_cat_cd)->value('d_cat_nm');
        $values = [
            'cat_type' => $cat_type,
            'd_cat_cd' => $d_cat_cd,
            'd_cat_nm' => $d_cat_nm,
        ];
        return view(Config::get('shop.head.view') . '/product/prd10_seq', $values);
    }

    public function goods_search($d_cat_cd, Request $req)
    {
        $page = $req->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $limit = $req->input('limit',100);

        $cat_type	= $req->input('cat_type', 'DISPLAY');
        $goods_stat	= $req->input('goods_stat', '');
        $style_no = $req->input("style_no", "");
        $goods_no = $req->input("goods_no", "");
        $brand_nm = $req->input("brand_nm", "");
        $brand_cd = $req->input("brand_cd", "");
        $goods_nm = $req->input("goods_nm", "");
        $com_type = $req->input("com_type", "");
        $com_id = $req->input("com_id", "");

        $head_desc = $req->input("head_desc", "");
        $disp_yn = $req->input("disp_yn", "");

        $sale_yn = $req->input("sale_yn", "");
        $point_yn = $req->input("point_yn", "");
        $div_yn = $req->input("div_yn", "");

        $ord = $req->input('ord','desc');
        $ord_field = $req->input('ord_field','g.goods_no');

        $where = "";

        if ($goods_stat != "")		$where .= " and g.sale_stat_cl =  '" . Lib::quote($goods_stat) . "' ";
        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        //if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";

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

        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        if ($com_type != "") $where .= " and c.com_type   = '" . Lib::quote($com_type) . "'";
        if ($com_id != "")	$where .= " and c.com_id     = '" . Lib::quote($com_id) . "' ";
        if ($disp_yn != "")	$where .= " and cg.disp_yn     = '" . Lib::quote($disp_yn) . "' ";

        if ($sale_yn != "") $where .= " and g.before_sale_price is not null ";
        if ($point_yn != "") $where .= " and g.point_yn = 'Y' ";
        if ($div_yn != "") $where .= " and g.bae_yn = 'N' ";

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = /** @lang text */
                "
                select
                    count(*) as total
                from category_goods cg
                    inner join goods g on cg.goods_no = g.goods_no and cg.goods_sub = g.goods_sub
                    inner join company c on c.com_id = g.com_id
                    left outer join code cd on cd.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = cd.code_id
                    left outer join goods_stat gs on cg.goods_no = gs.goods_no and cg.goods_sub = gs.goods_sub
                where cg.cat_type = :cat_type and cg.d_cat_cd = :d_cat_cd
                    $where
			";
            $row = DB::selectone($query,["cat_type" => $cat_type,'d_cat_cd' => $d_cat_cd]);
            $total = $row->total;
            if($total > 0){
                $page_cnt=(int)(($total-1)/$page_size) + 1;
            }
        }

        $query = "
            select
                cg.cat_type, cg.d_cat_cd, cg.goods_no, cg.goods_sub, g.style_no,cg.disp_yn,
                replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list') as img,
                replace(g.img, '$cfg_img_size_real', 'a_160') as img_160,
                g.ad_desc, g.head_desc, g.ad_desc, g.goods_nm, cd.code_val as sale_stat_cl,
                g.price, g.before_sale_price, g.sale_price, g.sale_s_dt, g.sale_e_dt, ifnull(round((1-(g.price/g.before_sale_price))*100), 0) as sale_rate,
                g.point_yn, g.point,
                g.bae_yn, g.dlv_pay_type, g.baesong_price,
                ifnull((
                    select sum(good_qty)
                    from goods_summary
                    where goods_no = cg.goods_no and goods_sub = cg.goods_sub
                ), 0) as qty,
                ifnull(gs.sale_1d, 0) as sale,
                ifnull(gs.sale_3m, 0) as sale_3m,
                ifnull(gs.pv_1d, 0) as pv,
                ifnull(gs.pv_3m, 0) as pv_3m,
                ifnull(gs.review_1d, 0) as review,
                ifnull(gs.review_3m, 0) as review_3m,
                ifnull(gs.grade_1d, '0.0') as grade,
                ifnull(gs.grade_3m, '0.0') as grade_3m,
                ifnull(round(gs.qa_1d, 1), 0) as qa,
                ifnull(round(gs.qa_3m, 1), 0) as qa_3m,
                c.com_nm,
                g.reg_dm,g.new_product_type,g.new_product_day,
                ifnull(date_format(
                (select max(last_date) from goods_summary
                    where goods_no = g.goods_no and goods_sub = g.goods_sub and good_qty = 0
                        and last_date >= date_format(date_sub(now(),interval 7 day),'%Y%m%d')),'%Y%m%d'),'') as soldout_day
            from category_goods cg
                inner join goods g on cg.goods_no = g.goods_no and cg.goods_sub = g.goods_sub
                inner join company c on c.com_id = g.com_id
                left outer join code cd on cd.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = cd.code_id
                left outer join goods_stat gs on cg.goods_no = gs.goods_no and cg.goods_sub = gs.goods_sub
            where cg.cat_type = :cat_type and cg.d_cat_cd = :d_cat_cd $where
            order by $ord_field $ord
            $limit
        ";

        $result = DB::select($query,["cat_type" => $cat_type,"d_cat_cd" => $d_cat_cd]);

		foreach($result as $row){
            if($row->img != ""){
                $row->img = sprintf("%s%s",config("shop.image_svr"),$row->img);
            }
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

    public function goods_add($d_cat_cd,Request $request) {

        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $cat_type	= $request->input('cat_type', 'DISPLAY');
        $goods_nos = $request->input('goods_no');

        try {
            DB::transaction(function () use (&$result,$cat_type, $d_cat_cd, $id,$name, $goods_nos) {

                for($i=0;$i<count($goods_nos);$i++){
                    $cnt = DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->count();

                    if($cnt === 0){
                        $section_goods = [
                            'cat_type' => $cat_type,
                            'd_cat_cd' => $d_cat_cd,
                            'goods_no' => $goods_nos[$i],
                            'goods_sub' => 0,
                            'disp_yn' => 'Y',
                            'admin_id' => $id,
                            'admin_nm' => $name,
                            'regi_date' => DB::raw('now()'),
                            'seq' => 0,
                        ];
                        DB::table('category_goods')->insert($section_goods);

						// 상위카테고리에 해당 상품 함께 등록
						$p_d_cat_cd = $d_cat_cd;
						while($p_d_cat_cd = DB::table('category')->where('cat_type', $cat_type)->where('d_cat_cd', $p_d_cat_cd)->value('p_d_cat_cd')) {

							$section_goods = [
								'cat_type' => $cat_type,
								'd_cat_cd' => $p_d_cat_cd,
								'goods_no' => $goods_nos[$i],
								'goods_sub' => 0,
								'disp_yn' => 'Y',
								'admin_id' => $id,
								'admin_nm' => $name,
								'regi_date' => DB::raw('now()'),
								'seq' => 0,
							];
							DB::table('category_goods')->insert($section_goods);
						}
                    }
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function goods_del($d_cat_cd,Request $request) {

        $cat_type	= $request->input('cat_type');
        $goods_nos = $request->input('goods_no');

        try {
            DB::transaction(function () use (&$result, $cat_type, $d_cat_cd, $goods_nos) {
                for($i=0;$i<count($goods_nos);$i++){

                    DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->delete();

					// 하위카테고리의 해당 상품 모두 삭제
					DB::table('category_goods')
						->where('cat_type','=',$cat_type)
						->where('d_cat_cd','like', Lib::quote($d_cat_cd) . '%')
						->where('goods_no','=',$goods_nos[$i])
						->delete();

					// 상위카테고리의 해당 상품 모두 삭제
					$p_d_cat_cd = $d_cat_cd;
					while($p_d_cat_cd = DB::table('category')->where('cat_type', $cat_type)->where('d_cat_cd', $p_d_cat_cd)->value('p_d_cat_cd')) {

						DB::table('category_goods')
							->where('cat_type','=',$cat_type)
							->where('d_cat_cd','=',$p_d_cat_cd)
							->where('goods_no','=',$goods_nos[$i])
							->delete();
					}
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function goods_disp($d_cat_cd,Request $request) {

        $cat_type	= $request->input('cat_type');
        $goods_nos = $request->input('goods_no');
        $disp_yn = $request->input('disp_yn');

        try {
            DB::transaction(function () use (&$result, $cat_type, $d_cat_cd, $disp_yn,$goods_nos) {
                for($i=0;$i<count($goods_nos);$i++){

                    DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->update(['disp_yn' => $disp_yn]);
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function goods_seq($d_cat_cd, Request $request){

        $cat_type   = $request->input('cat_type', 'DISPLAY');
        $goods_nos  = $request->input('goods_no', []);

        try {
            DB::transaction(function () use (&$result, $cat_type, $d_cat_cd, $goods_nos) {
                for ($i = 0; $i < count($goods_nos); $i++) {
                    DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->update(['seq' => $i + 1]);
                }
            });

            $code   = 200;
            $msg    = "";
        } catch (Exception $e) {
            $code   = 500;
            $msg    = $e->getMessage();
        }

        return response()->json(['code' => $code,"msg" => $msg]);
    }

    /** 순서변경팝업 내 조회 */
    public function goods_search_seq($d_cat_cd, Request $request)
    {
		$cat_type = $request->input('cat_type', 'DISPLAY');
        $page_h = $request->input('page_h', 9);
        $page_v = $request->input('page_v', 5);
        $sort_yn = $request->input('sort_yn', 'N');
        $ord1 = $request->input('ord1', '');
        $ord2 = $request->input('ord2', '');
        $ord3 = $request->input('ord3', '');
        $sort1 = $request->input('sort1', 'desc');
        $sort2 = $request->input('sort2', 'desc');
        $sort3 = $request->input('sort3', 'desc');

        $orderby = "order by";
        if ($sort_yn === 'Y' && !($ord1 === null && $ord2 === null && $ord3 === null)) {
            if ($ord1 !== null) $orderby .= sprintf(" a.%s %s", $ord1, $sort1);;
            if ($ord2 !== null) $orderby .= sprintf(", a.%s %s", $ord2, $sort2);;
            if ($ord3 !== null) $orderby .= sprintf(", a.%s %s", $ord3, $sort3);;
        } else {
            $orderby .= " a.seq asc";
        }

        $sql = "
            select a.*
            from (
                select
                    cg.goods_no
                    , cg.goods_sub
                    , g.style_no
                    , g.goods_nm
                    , g.head_desc
                    , gs.code_val as sale_stat_cl
                    , g.price
                    , cg.disp_yn
                    , ifnull((
                    select sum(good_qty)
						from goods_summary
						where goods_no = cg.goods_no and goods_sub = cg.goods_sub
                    ), 0) as qty
                    , ifnull(gst.sale_1d, 0) as ord_qty
                    , replace(g.img, 'a_500', 's_50') as img
                    , g.reg_dm
                    , g.upd_dm
                    , cg.seq
                    , gseq.spoint
                    , :page_h as page_h
                    , :page_v as page_v
                from category_goods cg
                    inner join goods g on g.goods_no = cg.goods_no and g.goods_sub = cg.goods_sub
                    left outer join code gs on gs.code_kind_cd = 'G_GOODS_STAT' and gs.code_id = g.sale_stat_cl
                    left outer join goods_seq gseq on gseq.goods_no = cg.goods_no and gseq.goods_sub = cg.goods_sub
                    left outer join goods_stat gst on cg.goods_no = gst.goods_no and cg.goods_sub = gst.goods_sub
                where cg.cat_type = :cat_type and cg.d_cat_cd = :d_cat_cd
            ) a
            $orderby
        ";

        $result = DB::select($sql, [ 'cat_type' => $cat_type, 'd_cat_cd' => $d_cat_cd, 'page_h' => $page_h, 'page_v' => $page_v ]);

        return response()->json([
            'code' => 200,
            'msg' => '상품전시순서가 정상적으로 조회되었습니다.',
            'head' => [
                'total'         => count($result),
                'page'          => 1,
                'page_cnt'      => 1,
                'page_total'    => 1,
            ],
            'body' => $result,
        ]);
    }

	// 상품 조건별 매장 상품 메인
	public function store_goods($d_cat_cd, Request $request)
	{
		$values = [
			'd_cat_cd'	=> $d_cat_cd,
			'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
		];
		
		return view( Config::get('shop.head.view') . '/product/prd10_store_goods',$values);
	}
	
	// 상품 조건별 매장 상품 리스트
	public function store_goods_list(Request $request)
	{
		$d_cat_cd	= $request->input('d_cat_cd');
		
		$sql	= " 
			select 
				c.product_brand, c.product_gender, c.product_item, c.product_outlet_yn
			from category c
			where
				c.cat_type = 'DISPLAY'
			  	and c.use_yn = 'Y'
				and c.d_cat_cd = :d_cat_cd
			limit 1
		";
		$category_info	= DB::selectOne($sql,['d_cat_cd' => $d_cat_cd]);

		if( $category_info->product_brand != '' || $category_info->product_gender != '' || $category_info->product_item != '' || $category_info->product_outlet_yn == 'Y' ){
			$product_brand	= $category_info->product_brand;
			$product_gender	= $category_info->product_gender;
			$product_item	= $category_info->product_item;
			$product_outlet_yn	= $category_info->product_outlet_yn;

			$brand_query 	= "";
			$gender_query 	= "";
			$item_query 	= "";
			$outlet_query	= "";

			if( $product_brand != '' ){
				$product_brand	= explode(',', $product_brand);
				$brand_query	= " and pc.brand in (";
				for($i = 0; $i < count($product_brand); $i++ ){
					if($i > 0) $brand_query	.= ",";
					$brand_query	.= "'" . $product_brand[$i] . "'";
				}
				$brand_query	.= ") ";
			}

			if( $product_gender != '' ){
				$product_gender	= explode(',', $product_gender);
				$gender_query	= " and pc.gender in (";
				for($i = 0; $i < count($product_gender); $i++ ){
					if($i > 0) $gender_query	.= ",";
					$gender_query	.= "'" . $product_gender[$i] . "'";
				}
				$gender_query	.= ") ";
			}

			if( $product_item != '' ){
				$product_item	= explode(',', $product_item);
				$item_query	= " and pc.item in (";
				for($i = 0; $i < count($product_item); $i++ ){
					if($i > 0) $item_query	.= ",";
					$item_query	.= "'" . $product_item[$i] . "'";
				}
				$item_query	.= ") ";
			}
			
			if( $product_outlet_yn == 'Y' ){
				$outlet_query	= " and (p.tag_price - p.price)/p.tag_price >= 0.2 ";
			}else{
				$outlet_query	= " and (p.tag_price - p.price)/p.tag_price <= 0.3 ";
			}

			$sql_product	= "
					select
						distinct pc.goods_no, 
						g.style_no, g.goods_nm, g.head_desc, g.goods_sh, g.price, 
						cd.code_val as sale_stat_cl, 'N' as category_goods_yn
					from product_code pc
					inner join product p on pc.prd_cd = p.prd_cd
					inner join product_stock_storage pss on pc.prd_cd = pss.prd_cd
					inner join goods g on pc.goods_no = g.goods_no
                    left outer join code cd on cd.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = cd.code_id
					where
						1 = 1
						$brand_query
						$gender_query
						$item_query
						$outlet_query
						and pss.wqty > 0
				";
			$result	= DB::select($sql_product);

			foreach ($result as $row) {
				$sql_chk	= " select count(*) as tot from category_goods where cat_type = 'DISPLAY' and disp_yn = 'Y' and d_cat_cd = :d_cat_cd and goods_no = :goods_no";
				$tot	= DB::selectOne($sql_chk,['d_cat_cd' => $d_cat_cd, 'goods_no' => $row->goods_no])->tot;
				
				if( $tot > 0 )	$row->category_goods_yn = 'Y';
			}			
		}
		
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($result),
			),
			"body" => $result
		]);
	}
}
