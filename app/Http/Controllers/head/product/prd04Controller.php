<?php

namespace App\Http\Controllers\head\product;

use App\Components\Lib;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\Schema;

class prd04Controller extends Controller
{
    public function index() {
        $values = [
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
        ];
        return view( Config::get('shop.head.view') . '/product/prd04',$values);
    }

    public function search(Request $request) {

        $goods_type = $request->input("goods_type"); // 상품구분
        $goods_stat = $request->input("goods_stat"); // 상품상태
        $style_no = $request->input("style_no"); // 스타일넘버
        $goods_no = $request->input("goods_no"); // 상품번호

        $brand_cd = $request->input("brand_cd"); // 브랜드
        $head_desc = $request->input("head_desc"); // 상단홍보글
        $goods_nm = $request->input("goods_nm"); // 상품명
        
        $limit = $request->input("limit", 100);	// 출력수
        $ord_field = $request->input("ord_field"); // 정렬필드
        $ord = $request->input("ord"); // 정렬

        $sale_yn  = $request->input("sale_yn"); // 세일여부
        $coupon_yn = $request->input("coupon_yn"); // 쿠폰여부
        $sale_dt_yn = $request->input("sale_dt_yn"); // 타임세일여부
        $sale_type = $request->input("sale_type"); // 세일구분

        $where = "";
        if ( $goods_type != "" ) $where .= " and g.goods_type = '$goods_type' ";
		if ( $goods_stat != "" ) $where .= " and g.sale_stat_cl = '$goods_stat' ";

        if ( $style_no != "" ) {
            $style_nos = explode(",", $style_no);
            if (count($style_nos) > 1){
                if (count($style_nos) > 500) array_splice($style_nos, 500);
                $in_style_nos = "";
                for ($i=0; $i<count($style_nos); $i++){
                    if (isset($style_nos[$i]) && $style_nos[$i] != ""){
                        $in_style_nos .= ($in_style_nos == "") ? "'$style_nos[$i]'" : ",'$style_nos[$i]'";
                    }
                }
                if ($in_style_nos != "") {
                    $where .= " and g.style_no in ( $in_style_nos ) ";
                }
            } else {
                $where .= " and g.style_no like '$style_no%' ";
            }
        }

        if( $goods_no != "" ){
            $goods_nos = explode(",", $goods_no);
            if (count($goods_nos) > 1) {
                if (count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_no = join(",", $goods_nos);
                $where .= " and g.goods_no in ( $in_goods_no ) ";
            } else {
                $where .= " and g.goods_no = '$goods_no' ";
            }
        }

        if ( $brand_cd != "" ) $where .= " and g.brand = '$brand_cd' ";
        if ( $goods_nm != "" ) $where .= " and g.goods_nm like '%$goods_nm%' ";
        if ( $head_desc != "" )	$where .= " and g.head_desc like '%$head_desc%' ";

        if ( $sale_yn != "" )	$where .= " and g.sale_yn = '$sale_yn' ";
        if( $coupon_yn != "" ) $where .= " and gc.price > 0 ";
        if( $sale_dt_yn != "" ) $where .= " and g.sale_dt_yn = '$sale_dt_yn' ";
        if( $sale_type != "" ) $where .= " and g.sale_type = '$sale_type' ";


        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $limit;

        ##############################################################
        # 리스트 페이징 처리
        ##############################################################
        $data_cnt = 0;
        $page_cnt = 0;

        if ($page == 1) {

            $sql = "
				select count(*) as cnt
				from goods g 
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
				where 1=1 $where
			";

            $row = DB::selectOne($sql);
            $data_cnt = $row->cnt;

            // 페이지 얻기
            $page_cnt=(int)(($data_cnt-1)/$page_size) + 1;

            if ($page == 1) {
                $startno = ($page-1) * $page_size;
            } else {
                $startno = ($page-1) * $page_size;
            }

        } else {
            $startno = ($page - 1) * $page_size;
        }

        if ($limit == -1) $limit = "";
        else $limit = " limit $startno,$page_size ";

        $orderby = "";
        if ($ord_field != ""){
            $orderby = " order by $ord_field $ord ";
        }

        $sql = " 
			select
				'' as blank, g.goods_no,
				ifnull( type.code_val, 'N/A') as goods_type,
				opt.opt_kind_nm, brand.brand_nm, g.style_no, g.head_desc, '' as img_view,
                replace(g.img,'a_500', 's_62') as img,
				g.goods_nm, stat.code_val as sale_stat_cl_val,
				g.goods_sh,g.normal_price,g.price,
				g.sale_type,g.sale_yn,g.before_sale_price,g.sale_price,0 as sale_rate,
				g.sale_dt_yn,g.sale_s_dt,g.sale_e_dt,
				gc.coupon_price,g.wonga,0 as margin_amt,0 as margin_rate,
				ifnull(
					(select sum(good_qty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				  ) as qty,
				g.md_nm,
				g.reg_dm,g.upd_dm,
				g.goods_type as goods_type_cd
			from goods g
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join brand brand on brand.brand = g.brand
			where 1=1 $where
			$orderby
			$limit
		";

        $collection = collect(DB::select($sql));

        $rows = $collection->map(function ($row) {

            $normal_price = $row->normal_price;
            $price = $row->price;
            $wonga = $row->wonga;
            $sale_price = $row->sale_price;

            // 세일 가격
            if ( $row->sale_yn == "Y" && $sale_price > 0 ) {
                $sale_rate = ( ($normal_price - $sale_price) / $normal_price );
                $row->sale_rate = $sale_rate * 100;
            }

            // 마진율 계산
            if ($row->sale_dt_yn == "Y" && $sale_price > 0)
            {
                $margin_amt = $sale_price - $wonga; //마진액(판매이익) 추가
                $margin_rate = round((1 - $wonga / $sale_price)*100, 2);
            }

            else {
                $margin_amt = $price - $wonga; //마진액(판매이익) 추가
                $margin_rate = 0;

                if ($price > 0){
                    $margin_rate = round((1 - $wonga / $price)*100, 2);
                }
            }

            $row->margin_amt = $margin_amt;
            $row->margin_rate = $margin_rate;	// 세일율

            if ($row->img != "") { // 이미지 url
				$row->img = sprintf("%s%s",config("shop.image_svr"),$row->img);
			}

            return $row;
            
        })->all();

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => count($rows),
                "page_total" => $page_cnt
            ),
            "body" => $rows,
        ]);
    }

    public function timeSaleOff(Request $request) {
        $data = $request->input('data');
        if (is_array($data)) {
            try {
                DB::beginTransaction();
                collect($data)->map(function($item) {
                    $goods_no = trim($item["goods_no"]);
                    if (!empty($goods_no)) {
                        $sql = "
                            SELECT a.goods_no, a.goods_sub, a.goods_type, b.com_type, a.normal_wonga, a.normal_price
                            FROM goods a
                                LEFT OUTER JOIN company b ON a.com_id = b.com_id
                            WHERE
                                a.goods_no = :goods_no
                                and a.sale_yn = 'Y' 
                                and a.sale_dt_yn = 'Y'
                        "; // and a.normal_price > a.price - 제외 (old.netpx는 정상가 판매가 같아도 적용되어서 제거하였음)
                        $row = DB::selectOne($sql, ["goods_no" => $goods_no]);
                        if ($row) {
                            $sql = "";
                            if ($row->goods_type == "P" && $row->com_type == "2" && $row->normal_wonga > 0) {
                                $sql = "
                                    UPDATE goods SET 
                                        price = normal_price, wonga = normal_wonga, sale_yn = 'N', sale_price = 0, 
                                        sale_dt_yn = 'N', sale_s_dt = '000000000000', sale_e_dt = '000000000000', limited_dc = 'N', limited_coupon = 'N'
                                    where goods_no = :goods_no
                                ";
                            } else {
                                $sql = "
                                    UPDATE goods SET
                                        price = normal_price, sale_yn = 'N', sale_price = 0, 
                                        sale_dt_yn = 'N', sale_s_dt = '000000000000', sale_e_dt = '000000000000', limited_dc = 'N', limited_coupon = 'N'
                                    WHERE goods_no = :goods_no
                                ";
                            }
                            DB::update($sql, ["goods_no" => $goods_no]);
                        }
                    }
                });
                DB::commit();
                return response()->json(["code" => "1"], 200);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json(["code" => "0"], 200);
            }
        }
    }
}