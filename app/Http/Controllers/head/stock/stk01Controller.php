<?php

namespace App\Http\Controllers\head\stock;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Jaego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;

class stk01Controller extends Controller
{
    public function index() {

        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
            'alter_reasons' => SLib::getCodes('G_JAEGO_REASON'),
        ];
        return view( Config::get('shop.head.view') . '/stock/stk01',$values);
    }

    public function search(Request $request){

        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $goods_stat	    = $request->input("goods_stat");
        $style_no		= $request->input("style_no");
        $goods_no		= $request->input("goods_no");
        $item       	= $request->input("item");
        $com_id         = $request->input("com_id");
        $brand_nm		= $request->input("brand_nm");
        $brand_cd		= $request->input("brand_cd");
        $goods_nm		= $request->input("goods_nm");
        $is_unlimited	= $request->input("is_unlimited");
        $wqty_h	        = $request->input("wqty_h");
        $wqty_l	    	= $request->input("wqty_l");
        $qty_h	    	= $request->input("qty_h");
        $qty_l	    	= $request->input("qty_l");
		$ord_field      = $request->input("ord_field","a.goods_no");
		$ord            = $request->input("ord","desc");

        $str_order_by   = "order by " . $ord_field . " " . $ord . ", a.goods_opt";

        $where = "";
        if( $style_no != "" )	    $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if( $com_id != "" )	    $where .= " and g.com_id = '" . Lib::quote($com_id) . "' ";
        //if( $goods_no != "" )		$where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
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


        if( $item != "" )	$where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if( $brand_cd != "" ){
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != ""){
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if( $goods_nm != "" )		$where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if( $is_unlimited != "" )	$where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";
        if( $wqty_l != "" )		    $where .= " and s.wqty >= '" . Lib::quote($wqty_l) . "' ";
        if( $wqty_h != "" )	        $where .= " and s.wqty <= '" . Lib::quote($wqty_h) . "' ";
        if( $qty_l != "" )		    $where .= " and s.good_qty >= '" . Lib::quote($qty_l) . "' ";
        if( $qty_h != "" )		    $where .= " and s.good_qty <= '" . Lib::quote($qty_h) . "' ";
        if( $goods_stat != "" )	    $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = /** @lang text */
                "
                select count(*) as total
                from goods g
                    inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
                where 1=1 $where
			";
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt=(int)(($total-1)/$page_size) + 1;
        }


		// if($limit == -1){
		// 	$limit = "";
		// } else {
		// 	$limit = " limit $startno, $page_size ";
		// }

        $query = /** @lang text */
            "
          select
            a.com_nm, o.opt_kind_nm, b.brand_nm, a.style_no,
            cd.code_val as goods_type_nm, cd2.code_val as is_unlimited_nm,
            a.goods_no, a.goods_sub, a.goods_nm,
            cd3.code_val as sale_stat_cl_nm,
            a.wonga,
            a.goods_opt, '1' as level,
            ifnull(if(a.is_unlimited = 'Y', '∞', a.good_qty), 0) as good_qty,
            ifnull(a.wqty,0) as wqty,
            ifnull(if(a.is_unlimited = 'Y', '-', a.good_qty), 0) as edit_good_qty,
            ifnull(a.wqty, 0) as edit_wqty,
            a.goods_no as goods_no_hd,a.goods_sub as goods_sub_hd,a.is_unlimited
          from (
            select
              g.goods_no, g.goods_sub, g.goods_nm, g.sale_stat_cl,g.wonga,
              g.com_id,c.com_nm, g.opt_kind_cd, g.brand, g.style_no, g.goods_type, g.is_unlimited,
              s.goods_opt,s.good_qty,s.wqty
            from goods g inner join goods_summary s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
              inner join company c on c.com_id = g.com_id
            where 1=1 $where
          ) a
          left outer join brand b on a.brand = b.brand
          inner join opt o on a.opt_kind_cd = o.opt_kind_cd and o.opt_id = 'K'
          inner join code cd on cd.code_kind_cd = 'G_GOODS_TYPE' and a.goods_type = cd.code_id
          inner join code cd2 on cd2.code_kind_cd = 'G_IS_UNLIMITED' and a.is_unlimited = cd2.code_id
          inner join code cd3 on cd3.code_kind_cd = 'G_GOODS_STAT' and a.sale_stat_cl = cd3.code_id
          $str_order_by
          $limit
        ";
        //echo "<pre>$query</pre>";
        $result = DB::select($query);

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

    public function update(Request $request){

        $reason = $request->input('reason');
        $data = $request->input('data');

        //$data = html_entity_decode($data);
        //$data = json_decode($data);

        try {

            $id = Auth('head')->user()->id;
            $name = Auth('head')->user()->name;
            $user = array(
                "id" => $id,
                "name" => $name
            );

            DB::transaction(function () use (&$result,$user,$reason,$data) {
                $jaego = new Jaego($user);
                for($i=0;$i<count($data);$i++){
                    $row = $data[$i];
                    $jaego->SetQty($row["goods_no"], 0, $row["goods_opt"],$row["qty"]);
                    if($jaego->isUnlimited($row["goods_no"]) == "N"){
                        $jaego->SetStockQty($row["goods_no"], 0, $row["goods_opt"],$row["wqty"],$reason);
                    }
                }
            });
            $code = 200;
        } catch(\Exception $e){
            $code = 500;
        }
        return response()->json([
           "code" => $code
        ]);
    }

    public function show($goods_no, Request $req) {

        $goods_opt = $req->input('goods_opt');

        $mutable = now();
        $sdate = $mutable->sub(3, 'month')->format('Y-m-d');

        return view(Config::get('shop.head.view') . '/stock/stk01_show',
            [
                'sdate' => $sdate,
                'edate' => date("Y-m-d"),
                'goods_no' => $goods_no,
                'goods_opt' => strtoupper($goods_opt),
                'jaego_types' => SLib::getCodes('G_JAEGO_TYPE'),
            ]
        );
    }

    /** 재고현황팝업 상세정보 조회 */
    public function show_search($goods_no, Request $req)
    {
        $conf = new Conf();

        $cfg_domain_img			= $conf->getConfigValue("shop","domain_img");
        if($cfg_domain_img == ""){
            $cfg_domain_img = $_SERVER["HTTP_HOST"];
        }
        $goods_img_url = sprintf("http://%s",$cfg_domain_img);

        $mutable = now();
        $sdate = $mutable->sub(3, 'month')->format('Y-m-d');

        $goods_opt = $req->input("goods_opt",'');
        $sdate = $req->input("sdate",$sdate);
        $edate = $req->input("edate",date("Y-m-d"));
        $io = $req->input("io",'');
        $jaego_type = $req->input("jaego_type",'');
        $invoice_no = $req->input("invoice_no",'');

        $limit      = $req->input("limit", 100);
        $page = $req->input("page",1);
        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($sdate != "") $where .= " and a.regi_date >= '". Lib::quote($sdate) ."' ";
        if ($edate != "") $where .= " and a.regi_date < DATE_ADD('". Lib::quote($edate) ."', INTERVAL 1 DAY) ";
        // if ($goods_opt != "") $where .= " and a.goods_opt >= '". Lib::quote($goods_opt) ."' ";
        if ($goods_opt != "" && $goods_opt != 'null') $where .= " and a.goods_opt = '". Lib::quote($goods_opt) ."' ";
        if ($io == "I") $where .= " and a.qty > 0 ";
        if ($io == "O") $where .= " and a.qty < 0 ";
        if ($jaego_type != "") $where .= " and a.type = '". Lib::quote($jaego_type) ."' ";
        if ($invoice_no != "") $where .= " and a.invoice_no = '". Lib::quote($invoice_no) ."' ";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;

        $total = 0;
        $page_cnt = 0;
        $goods_info = (object)[];
        $options = [];

        if ($page == 1) {

            $sql = "
                select
                    style_no, goods_nm, a.com_id, c.com_nm, a.opt_kind_cd,
                    o.opt_kind_nm, ifnull(r.brand_nm,'') as brand_nm, a.rep_cat_cd,
                    img, a.option_kind
                from goods a
                    inner join company c on a.com_id = c.com_id
                    left outer join opt o on a.opt_kind_cd = o.opt_kind_cd and opt_id = 'K'
                    left outer join brand r on a.brand = r.brand
                where a.goods_no = :goods_no
            ";
            $goods_info = (array)DB::selectone($sql,array("goods_no" => $goods_no));
            $img = str_replace("a_500","s_100",$goods_info["img"]);
            $goods_info["img"] = sprintf("%s%s",$goods_img_url,$img);

            $sql = "
                select no, type, name
                from goods_option
                where goods_no = :goods_no
            ";
            $options_basic = DB::select($sql,array("goods_no" => $goods_no));

            // 재고 수량 얻기
            $sql = /** @lang text */
                "
                select
                    a.goods_opt,good_qty, wqty
                from goods_summary a
                where goods_no = :goods_no and goods_sub = :goods_sub
                order by a.seq,a.goods_opt
            ";

            $row = DB::select($sql,array("goods_no" => $goods_no,"goods_sub" => 0));

            $i=0;
            $a_opt1=[];
            $a_opt2=[];

            while ($i<count($row)) {

//                $goods_opt = strtoupper(addslashes($row[$i]->goods_opt));
                $goods_opt = $row[$i]->goods_opt;
                $qty = $row[$i]->good_qty;
                $wqty = $row[$i]->wqty;
                $a_jaego_qty[$goods_opt] = $qty;
                $a_jaego_wqty[$goods_opt] = $wqty;

                if(count($options_basic) > 1){
                    $tmp = explode("^", $goods_opt);
                    if($tmp[0] != "" && !in_array($tmp[0], $a_opt1)){
                        $a_opt1[] = $tmp[0];
                    }
                    if($tmp[1] != "" && !in_array($tmp[1], $a_opt2)){
                        $a_opt2[] = $tmp[1];
                    }
                } else { // 싱글옵션
                    if(!in_array($goods_opt, $a_opt1)){
                        $a_opt1[] = $goods_opt;
                    }
                }
                $i++;
            }
            if(count($options_basic) > 1){
                for($i = 0; $i < count($a_opt1); $i++){
                    for($j = 0; $j < count($a_opt2); $j++){
                        $key = $a_opt1[$i] ."^". $a_opt2[$j];
                        if(! array_key_exists($key, $a_jaego_qty)){
                            $a_jaego_qty[$key] = "N/A";
                        }
                        if(! array_key_exists($key, $a_jaego_wqty)){
                            $a_jaego_wqty[$key] = "N/A";
                        }
                    }
                }
            }

            $a_opt=array($a_opt1,$a_opt2);

            $sql = /** @lang text */
                "
                select
                    upper(goods_opt) as goods_opt, sum(qty) as salecnt
                from order_opt
                where goods_no = :goods_no and goods_sub = :goods_sub
                    and ord_state in (30,40,50)
                    and ord_date >= :sdate and ord_date < date_add(:edate,interval 1 DAY)
                group by goods_opt
            ";

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "goods_no" => $goods_no,
                "goods_sub" => 0,
                "sdate" => $sdate,
                "edate" => $edate
            ]);

            $stock_sale = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stock_sale[$row["goods_opt"]] = $row["salecnt"];
            }

            $sql = "
                select count(*) as total
                from goods_history a
                  left outer join order_opt oo on a.ord_opt_no = oo.ord_opt_no and a.goods_no = oo.goods_no and a.goods_sub = oo.goods_sub
                  left outer join code cd on cd.code_kind_cd = 'G_JAEGO_TYPE' and cd.code_id = a.type
                  left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_KIND' and cd2.code_id = oo.ord_kind
                  left outer join code cd3 on cd3.code_kind_cd = 'G_ORD_STATE' and cd3.code_id = oo.ord_state
                  left outer join code cd4 on cd4.code_kind_cd = 'G_CLM_STATE' and cd4.code_id = oo.clm_state
                where a.goods_no = :goods_no $where
			";
            $row = DB::selectOne($sql, array("goods_no" => $goods_no));

            $total = $row->total;
            if($total > 0){
                $page_cnt = (int)(($total-1)/$page_size) + 1;
            }
        }

        $sql = /** @lang text */
            "
            select
              date_format(a.regi_date,'%Y.%m.%d') as regi_date, replace(a.goods_opt,'^','  :  ') as opt_val, if(a.qty<0,'출고','입고') as io_gubun
              , if(a.type=9,'재고조정',cd.code_val) as type, a.qty, a.ord_no, a.invoice_no
              , cd2.code_val as ord_kind, cd3.code_val as ord_state, cd4.code_val as clm_state
              , etc, a.admin_nm, 	a.ord_opt_no, a.wonga
            from goods_history a
              left outer join order_opt oo on a.ord_opt_no = oo.ord_opt_no and a.goods_no = oo.goods_no and a.goods_sub = oo.goods_sub
              left outer join code cd on cd.code_kind_cd = 'G_JAEGO_TYPE' and cd.code_id = a.type
              left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_KIND' and cd2.code_id = oo.ord_kind
              left outer join code cd3 on cd3.code_kind_cd = 'G_ORD_STATE' and cd3.code_id = oo.ord_state
              left outer join code cd4 on cd4.code_kind_cd = 'G_CLM_STATE' and cd4.code_id = oo.clm_state
            where a.goods_no = :goods_no $where
            order by a.history_no desc
            limit $startno,$page_size
        ";
        $rows = DB::select($sql, array("goods_no" => $goods_no));

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
//            "goods_opt" => strtoupper($goods_opt),
            "goods_opt" => $goods_opt,
            "info" => $goods_info,
            "options" => $a_opt,
            "qty" => $a_jaego_qty ?? [],
            "wqty" => $a_jaego_wqty ?? [],
            "sale" => $stock_sale,
            "body" => $rows
        ]);
    }
}
