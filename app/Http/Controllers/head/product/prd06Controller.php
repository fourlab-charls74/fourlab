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
use App\Models\Product;

class prd06Controller extends Controller
{
    public function index() {
        $values = [
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
        ];
        return view( Config::get('shop.head.view') . '/product/prd06', $values);
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

        $onesize_yn = $request->input("onesize_yn"); // 원사이즈 상품 여부
        $onesize_qty = $request->input("onesize_qty", "wqty"); // 원사이즈 상품 재고 
        $not_order = $request->input("not_order", ""); // 미주문 또는 특정 기간 동안 미주문 

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

        if ($onesize_yn == "Y") {
            $where .= " and ( select sum(if($onesize_qty > 0,1,0)) from goods_summary where goods_no = g.goods_no having count(*) > 1) = 1  ";
        }

        if ($not_order == 1) {
            $where .= " and ( select count(*) from goods_stat where goods_no = g.goods_no and sale_1m = 0 ) = 1 ";
        } elseif ($not_order == 3) {
            $where .= " and ( select count(*) from goods_stat where goods_no = g.goods_no and sale_3m = 0 ) = 1 ";
        } elseif ($not_order == 12) {
            $where .= " and ( select count(*) from goods_stat where goods_no = g.goods_no and sale_1y = 0 ) = 1 ";
        };

        if ( $brand_cd != "" ) $where .= " and g.brand = '$brand_cd' ";
        if ( $goods_nm != "" ) $where .= " and g.goods_nm like '%$goods_nm%' ";
        if ( $head_desc != "" )	$where .= " and g.head_desc like '%$head_desc%' ";

        if ( $sale_yn != "" )	$where .= " and g.sale_yn = '$sale_yn' ";
        if ( $coupon_yn != "" ) $where .= " and gc.price > 0 ";
        if ( $sale_dt_yn != "" ) $where .= " and g.sale_dt_yn = '$sale_dt_yn' ";

        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $limit;

        ##############################################################
        # 리스트 페이징 처리
        ##############################################################
        $data_cnt = 0;
        $page_cnt = 0;
        $goods_cnt = 0;

        if ($page == 1) {

            $sql = "
				select count(*) as cnt,count(distinct(g.goods_no)) as goods_cnt
				from goods g 
                    inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
                    left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub				
				where 1=1 $where
			";

            $row = DB::selectOne($sql);

            $data_cnt = $row->cnt;
            $goods_cnt = $row->goods_cnt;

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

        if ($limit == -1) {
			if ($page > 1) $limit = "limit 0";
			else $limit = "";
		} else $limit = " limit $startno, $page_size ";

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
                s.goods_opt,s.good_qty,s.wqty,
                ( select count(*) from goods_restock where goods_no = s.goods_no and goods_sub = s.goods_sub  ) as restock,
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
                inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
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

        /**
         * 조회한 데이터에서 필요한 정보들을 추가 또는 가공
         */
        $rows = $collection->map(function ($row, $index) {

            $row->index = $index;

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
        
        /**
         * 프론트에서 그룹별 색깔 구분 표시를 위해 행별로 boolean 값 추가
         */
        collect($rows)->reduce(function($carry, $item) use ($rows) {

            $index = $item->index;
            $goods_no = $item->goods_no;

            if ($index == 0) { // first row is green
                $rows[$index]->is_green = true;
                return ['is_green' => true, 'goods_no' => $goods_no]; // next
            }
            
            $is_green = $carry['is_green'];
            $prev_goods_no = $carry['goods_no'];

            if ($prev_goods_no != $goods_no) { // 이전 상품번호가 현재 row와 다른경우
                $is_green = !$carry['is_green']; // switch boolean 
            }

            $rows[$index]->is_green = $is_green;
            return ['is_green' => $is_green, 'goods_no' => $goods_no];

        }, ['is_green' => true]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "goods_cnt" => $goods_cnt,
                "page_cnt" => count($rows),
                "page_total" => $page_cnt
            ),
            "body" => $rows,
        ]);
    }

    public function saleOn(Request $request) {
        $sale_type = $request->input("sale_type", "event");
        $sale_rate = $request->input("sale_rate", 0);
        $data = $request->input("data");

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        if (is_array($data)) {
            $goods = new Product($user);
            try {
                DB::beginTransaction();
                collect($data)->unique()->map(function($item) use ($goods, $sale_type, $sale_rate) {
                    $goods_no = trim($item["goods_no"]);
                    if (!empty($goods_no) ) {
                        $goods->SetGoodsNo($goods_no);
                        $goods->Sale($sale_type, $sale_rate);
                    }
                });
                DB::commit();
                return response()->json(['code' => "1"], 200);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json(['code' => "0"], 200);
            }
        }
    }

    public function saleOff(Request $request) {
        $data = $request->input('data');
        if (is_array($data)) {
            $user = [
                'id' => Auth('head')->user()->id,
                'name' => Auth('head')->user()->name
            ];
            $goods = new Product($user);
            try {
                DB::beginTransaction();
                collect($data)->map(function($item) use ($goods) {
                    $goods_no = trim($item["goods_no"]);
                    if (!empty($goods_no)) {
                        $goods->SetGoodsNo($goods_no);
                        $goods->SaleOff();
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
