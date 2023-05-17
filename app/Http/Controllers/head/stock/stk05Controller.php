<?php

namespace App\Http\Controllers\head\stock;
use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class stk05Controller extends Controller
{
    public function index() {
        $values = [
            'sdate' => date('Y-m-d', strtotime(-3 . 'days')),
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(), // 품목
            'com_types'	=> SLib::getCodes('G_COM_TYPE'), // 업체
            'kind' => ["입고" => "입고", "출고" => "출고"], // 구분
            'jaego_types' => SLib::getCodes('G_JAEGO_TYPE'), // 사유
			'goods_stats' => SLib::getCodes('G_GOODS_STAT'), // 상품 상태
            'locs' => SLib::getCodes('G_STOCK_LOC')
        ];
        return view( Config::get('shop.head.view') . '/stock/stk05', $values);
    }

    public function search(Request $request) {

        // 일자
        $sdate = $request->input("sdate");
        $edate = $request->input("edate");
        $sdate = str_replace('-', '', $sdate);
        $edate = str_replace('-', '', $edate);

        // 브랜드
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");

        // 업체 및 업체명
        $com_type = $request->input("com_type");
        $com_id = $request->input("com_cd");

        $item = $request->input("item"); // 품목
        $style_no = $request->input("style_no"); // 스타일 넘버
        $goods_no = $request->input("goods_no"); // 상품코드
        $goods_nm = $request->input("goods_nm"); // 상품명
        $kind = $request->input("kind"); // 구분 (출고, 입고)
        $jaego_type = $request->input("jaego_type"); // 사유 (재고타입)
        $goods_stat = $request->input("goods_stat"); // 상품 상태
        $loc = $request->input("loc"); // 지역

        // 페이지네이션 관련
        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input("limit", 100);
        $ord_field = $request->input("ord_field", "a.history_no");
        $ord = $request->input("ord", "desc");

        $where = "";

        if ( $sdate != "" ) $where .= " and a.stock_state_date >= '$sdate' ";
		if ( $edate != "" ) $where .= " and a.stock_state_date <= '$edate' ";
		
		if ( $brand_cd != "" ) {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }

        if ( $com_type != "" ) $where .= " and c.com_type = '" . Lib::quote($com_type) . "' ";
		if ( $com_id != "" ) $where .= " and g.com_id = '" . Lib::quote($com_id) . "' ";

        if ( $item != "" ) $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";

        $style_no = preg_replace("/\s/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);
        $style_no = preg_replace("/\t/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);
        $style_no = preg_replace("/\n/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);
        if ( $style_no != "" ) {
            $style_nos = explode(",",$style_no);
            if(count($style_nos) > 1){
                if(count($style_nos) > 500) array_splice($style_nos,500);
                $in_style_nos = "";
                for($i=0; $i<count($style_nos); $i++){
                    if(isset($style_nos[$i]) && $style_nos[$i] != ""){
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

        if ($goods_no != "") {
            $goods_no = $goods_no;
        }
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);
        if ( $goods_no != "" ) {
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                $where .= " and g.goods_no = '$goods_no' ";
            }
        }

		if ( $goods_nm != "" ) $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if ( $goods_stat != "" ) $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        if ( $loc != "" ) $where .= " and a.loc = '" . Lib::quote($loc) . "' ";
        if ( $jaego_type != "" ) $where .= " and a.type = '" . Lib::quote($jaego_type) . "' ";

		if ( $kind == '입고' ) {
			$where .= " and a.qty > 0";
		} else if ($kind == '출고') {
			$where .= " and a.qty < 0";
		}

        $page_size = $limit;
        $startno = ($page-1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        ##############################################################
        # 리스트 페이징 처리
        ##############################################################
        if ($page == 1) {
            $sql = "select
					count(*) as cnt
				from goods_history a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
					inner join company c on g.com_id = c.com_id
				where 1=1 $where
			";
            $row = DB::select($sql);
            $total = $row[0]->cnt;
            $page_cnt=(int)(($total-1)/$page_size) + 1;
        }

        if ($limit == -1) {
            $limit = "";
        } else $limit = " limit $startno,$page_size ";

        $orderby = "";
        if ($ord_field != ""){
            $orderby = " order by $ord_field $ord ";
        }

		$sql = "
			select
				date_format(a.regi_date, '%Y-%m-%d %H:%i') as date,
				if(a.qty < 0, '출고', '입고') as kind,
				if(a.type = 9, '재고조정', cd.code_val) as type,
				c.com_nm, o.opt_kind_nm, r.brand_nm, g.style_no, cd5.code_val as goods_type,
				g.goods_no, g.goods_sub, g.goods_nm,
				replace(a.goods_opt, '^', '  :  ') as goods_opt,
				a.qty, a.wonga,
				if(a.loc = '','기본',cd6.code_val) as loc, a.invoice_no,a.ord_no,
				etc, a.admin_nm, a.ord_opt_no
			from goods_history a
				inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				inner join company c on g.com_id = c.com_id
				left outer join opt o on g.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
				left outer join brand r on g.brand = r.brand
				left outer join order_opt oo on a.ord_opt_no = oo.ord_opt_no and a.goods_no = oo.goods_no and a.goods_sub = oo.goods_sub
				left outer join code cd on cd.code_kind_cd = 'G_JAEGO_TYPE' and cd.code_id = a.type
				left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_KIND' and cd2.code_id = oo.ord_kind
				left outer join code cd3 on cd3.code_kind_cd = 'G_ORD_STATE' and cd3.code_id = oo.ord_state
				left outer join code cd4 on cd4.code_kind_cd = 'G_CLM_STATE' and cd4.code_id = oo.clm_state
				left outer join code cd5 on g.goods_type = cd5.code_id and cd5.code_kind_cd = 'G_GOODS_TYPE'
				left outer join code cd6 on a.loc = cd6.code_id and cd6.code_kind_cd = 'G_STOCK_LOC'
			where 1=1 $where
            $orderby
			$limit
		";
        
        $collection = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($collection)
            ),
            "body" => $collection
        ]);
    }

}
