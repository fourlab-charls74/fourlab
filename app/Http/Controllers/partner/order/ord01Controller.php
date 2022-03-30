<?php

namespace App\Http\Controllers\partner\order;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use \Exception;

class ord01Controller extends Controller
{
    public function index(Request $request) {

        $mutable = Carbon::now();
        $sdate = $request->input("sdate", $mutable->sub(7, 'day')->format('Y-m-d'));
        $edate = $request->input("edate", date("Y-m-d"));

        $style_no	= $request->input('style_no');

        $values = [
            'sdate' => $sdate,
            'edate' => $edate,
            'style_no' => $style_no,
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'sale_places' => SLib::getSalePlaces(),
            'ord_states' => SLib::getOrdStates(),
            'clm_states' => SLib::getClmStates(),
            'ord_kinds' => SLib::getCodes('G_ORD_KIND'),
            'ord_types' => SLib::getCodes('G_ORD_TYPE'),
            'dlv_kinds' => SLib::getCodes('G_BAESONG_KIND'),
        ];
        return view(Config::get('shop.partner.view') . '/order/ord01',$values);
    }

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;

        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $limit = $request->input('limit',100);

        $sdate = $request->input('sdate',Carbon::now()->sub(7, 'day')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $sdate = str_replace("-","",$sdate);
        $edate = str_replace("-","",$edate);

        $ord_no = $request->input('ord_no');
        $user_id = $request->input('user_id');
        $user_nm = $request->input('user_nm');
        $r_nm = $request->input('r_nm');
        $cols = $request->input('cols');
        $key = $request->input('key');
        $style_no = $request->input('style_no');
        $goods_no = $request->input('goods_no');
        $item = $request->input('item');
        $brand_cd = $request->input('brand_cd');
        $goods_nm = $request->input('goods_nm');
        $goods_type = $request->input('goods_type');
        $dlv_kind = $request->input('dlv_kind');
        $head_desc = $request->input('head_desc');
        $ord_state = $request->input('ord_state');
        $clm_state = $request->input('clm_state');
        $limit = $request->input('limit',100);
        $ord = $request->input('ord');
        $ord_field = $request->input('ord_field');
        $ord_type = $request->input('ord_type');
        $ord_kind = $request->input('ord_kind');


        $where = "";
        $is_not_use_date = false;

        if($request->input('s_nud',"Y") === "Y"
            && ($ord_no != "" || $user_id != "" || $user_nm != "" || $r_nm != ""
                || ( ($cols == "b.mobile" || $cols == "b.phone" || $cols == "b.r_mobile") && $key != ""))){
            $is_not_use_date = true;
        }

        if($is_not_use_date == false){
            $where .= " and a.ord_date >= '$sdate' ";
            $where .= " and a.ord_date < DATE_ADD('$edate', INTERVAL 1 DAY) ";
        }

        if($ord_no != "")	$where .= " and a.ord_no = '" . Lib::quote($ord_no) . "' ";
        if($user_nm != "")	$where .= " and b.user_nm = '" . Lib::quote($user_nm) . "' ";
        if($r_nm != "")		$where .= " and b.r_nm = '" . Lib::quote($r_nm) . "' ";
        if($user_id != "")	$where .= " and b.user_id = '" . Lib::quote($user_id) . "' ";

		$style_no = preg_replace("/\s/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\t/",",",$style_no);
		$style_no = preg_replace("/\n/",",",$style_no);

		$goods_no = preg_replace("/\s/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);
		$goods_no = preg_replace("/\t/",",",$goods_no);
		$goods_no = preg_replace("/\n/",",",$goods_no);

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
					$where .= " and c.style_no in ( $in_style_nos ) ";
				}
			} else {
				$where .= " and c.style_no like '$style_no%' ";
			}
		}

        if( $goods_no		!= "" ){
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and c.goods_no in ( $in_goods_nos ) ";
			} else {
				$where .= " and c.goods_no = '$goods_no' ";
			}
		}

        if($item != "")	    $where .= " and opt_kind_cd = '" . Lib::quote($item) . "' ";
        if($goods_nm != "")		$where .= " and c.goods_nm like '%" . Lib::quote($goods_nm) . "%'";
        if($goods_type != "")	$where .= " and c.goods_type = '" . Lib::quote($goods_type) . "' ";
        if($dlv_kind != "")	    $where .= " and c.baesong_kind = '" . Lib::quote($dlv_kind) . "' ";
        if($ord_kind != "")	$where .= " and a.ord_kind = '" . Lib::quote($ord_kind) . "' ";
        if($ord_type != "")	$where .= " and a.ord_type = '" . Lib::quote($ord_type) . "' ";

        if($head_desc != "")	$where .= " and c.head_desc = '" . Lib::quote($head_desc) . "' ";
        if($ord_state != "")	$where .= " and a.ord_state = '" . Lib::quote($ord_state) . "' ";
        if($clm_state == "90")$where .= " and a.clm_state = 0 ";
		else{
			if($clm_state != ""){
                $where .= " and a.clm_state = '" . Lib::quote($clm_state) . "' ";
			}
		}
        if($brand_cd != "")    $where .= " and c.brand = '" . Lib::quote($brand_cd) . "' ";
        if($cols != "" && $key != ""){
            if(in_array($cols,array("b.mobile","b.phone","b.r_phone","b.r_mobile"))){
                $key = $this->replacetel($key);
                if($cols == "b.mobile" || $cols == "b.phone" || $cols == "b.r_mobile"){
                    $where .= " and $cols = '$key' ";
                } else {
                    $where .= " and $cols like '$key%' ";
                }
            } else {
                $where .= " and $cols like '" . Lib::quote($key) . "%' ";
            }
        }

        $str_order_by = $ord_field." ".$ord;
        if($ord_field == "a.head_desc"){ // 상단 홍보글인경우, 상단홍보글, 상품명 순.
			$str_order_by = $ord_field." ".$ord." ,a.goods_nm ".$ord;
		}

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";
        $insql = "";
        $str_order_by = " a.ord_opt_no desc ";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = /** @lang text */
                "
					select count(*) as total
					from order_opt a
						inner join order_mst b on a.ord_no = b.ord_no
						inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub $insql
						left outer join payment d on b.ord_no = d.ord_no
						left outer join coupon f on ( a.coupon_no = f.coupon_no )
						left outer join company e on a.sale_place = e.com_id and e.com_type = '4'
						left outer join company i on a.com_id = i.com_id
						left outer join claim g on g.ord_opt_no = a.ord_opt_no
						left outer join order_opt_memo h on a.ord_opt_no = h.ord_opt_no
						left outer join order_track ot on a.ord_no = ot.ord_no
					where 1=1 
					and a.com_id = :com_id 
					$where
			";
            $row = DB::select($query,["com_id" => $com_id]);
            //$row = DB::select($query);
            $total = $row[0]->total;
            if($total > 0){
                $page_cnt=(int)(($total-1)/$page_size) + 1;
            }
        }

        $query = /** @lang text */
            "
            select
                '' as chkbox, a.ord_no, a.ord_opt_no, ord_state.code_val ord_state, clm_state.code_val clm_state, pay_stat.code_val as pay_stat,
                ifnull(gt.code_val,'N/A') as goods_type_nm, a.style_no, '' as img_view, a.goods_nm,
                replace(a.goods_opt, '^', ' : ') as opt_val, a.goods_addopt, a.qty, a.user_nm, a.r_nm, a.price,
                a.sale_amt, a.gift, a.dlv_amt, a.pay_fee, pay_type.code_val as pay_type, fintech,
                a.cash_apply_yn,
                a.cash_yn,
                ord_type.code_val as ord_type,
                ord_kind.code_val as ord_kind,
                a.sale_place, a.out_ord_no, a.com_nm,
                baesong_kind.code_val as baesong_kind,
                dlv_type.code_val as dlv_type,
                dlv_cd.code_val, a.dlv_no,
                a.state, a.memo,
                a.coupon_nm,
                a.mobile_yn, a.app_yn, a.browser,
                a.ord_date, a.pay_date, a.dlv_end_date,
                a.last_up_date, a.goods_no, a.goods_sub,
                concat('$goods_img_url',replace(a.img,'$cfg_img_size_real','$cfg_img_size_list')) as img,
                a.goods_type,
                '2' as depth,
                a.sms_name, a.sms_mobile
            from (
                select
                    b.ord_no, a.ord_opt_no, a.ord_state, d.pay_stat, c.goods_type, c.style_no, a.goods_nm,
                    a.goods_opt, a.qty, concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm, b.r_nm,
                    a.price, (a.coupon_amt+a.dc_amt) as sale_amt,
                    (
                        select group_concat(gf.name)
                        from order_gift og
                            inner join gift gf on og.gift_no = gf.no
                        where og.ord_no = a.ord_no and og.ord_opt_no = a.ord_opt_no
                    ) as gift,
                    a.dlv_amt, 0 as pay_fee, d.pay_type,'' as fintech,
                    a.ord_type, a.ord_kind, f.coupon_nm, a.dlv_cd, a.dlv_no,
                    a.clm_state, e.com_nm as sale_place, b.out_ord_no, i.com_nm,
                    c.baesong_kind as dlv_baesong_kind, b.ord_date, d.pay_date,
                    a.dlv_end_date, g.last_up_date, c.goods_no, c.goods_sub, c.img, c.com_type,
                    h.state, h.memo, b.user_nm as sms_name, b.mobile as sms_mobile,
                    b.mobile_yn, '' as app_yn, ifnull(ot.browser, '') as browser,
                    if(d.cash_apply_yn = 'Y', '신청', '') as cash_apply_yn,
                    if(d.cash_yn = 'Y', '발행', '') as cash_yn,
                    b.dlv_type,
                    if(ifnull(a.goods_addopt,'') = '',
                        (select ifnull(group_concat(if(addopt_amt>0,concat(addopt,'(+',addopt_amt,')'),addopt)),'')
                        from order_opt_addopt where ord_opt_no = a.ord_opt_no),
                        a.goods_addopt
                    ) as goods_addopt
                from order_opt a
                    inner join order_mst b on a.ord_no = b.ord_no
                    inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub $insql
                    left outer join payment d on b.ord_no = d.ord_no
                    left outer join coupon f on ( a.coupon_no = f.coupon_no )
                    left outer join company e on a.sale_place = e.com_id and e.com_type = '4'
                    left outer join company i on a.com_id = i.com_id
                    left outer join claim g on g.ord_opt_no = a.ord_opt_no
                    left outer join order_opt_memo h on a.ord_opt_no = h.ord_opt_no
                    left outer join order_track ot on a.ord_no = ot.ord_no
                where 1=1 
                and a.com_id = :com_id 
                $where
                order by $str_order_by
                limit $startno, $page_size 
            ) a
            left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
            left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
            left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
            left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
            left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
            left outer join code com_type on (a.com_type = com_type.code_id and com_type.code_kind_cd = 'G_COM_TYPE')
            left outer join code baesong_kind on (a.dlv_baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
            left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
            left outer join code dlv_cd on (a.dlv_cd = dlv_cd.code_id and dlv_cd.code_kind_cd = 'DELIVERY')
            left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
            left outer join code dlv_type on (a.dlv_type = dlv_type.code_id and dlv_type.code_kind_cd = 'G_DLV_TYPE')
        ";

        $rows = DB::select($query,["com_id" => $com_id]);

        $depth_no = "";
        foreach ($rows as $row) {
            $ord_no = $row->ord_no;

            if($depth_no == ""){
                $depth_no = $ord_no;
                $row->depth = "1";
            }

            if($ord_no != $depth_no){
                $row->depth = "1";
                $depth_no = $ord_no;
            }

            if ($row->img != "") { // 이미지 url
				$row->img = sprintf("%s%s",config("shop.image_svr"),$row->img);
			}
        }

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

    public function show($ord_no,$ord_opt_no = ""){

        $com_id = Auth('partner')->user()->com_id;

        $img_svr = config("shop.image_svr");
        $cfg_img_size_real = SLib::getCodesValue("G_IMG_SIZE","real");
        $cfg_img_size_list = SLib::getCodesValue("G_IMG_SIZE","list");

        if($ord_no != "" && $ord_opt_no == "") {
            $ord_opt_no = $this->get_ord_opt_no($ord_no, $com_id);
        }

        $order = $this->get_order_info($ord_opt_no, $com_id); //주문 정보 리턴
        //dd($order);
        $order["ord_no"] = $ord_no;
        $order["ord_opt_no"] = $ord_opt_no;
        $order["com_addr"] = sprintf("[%s] %s %s",$order["zip_code"],$order["addr1"],$order["addr2"]);
        $order["com_r_addr"] = sprintf("[%s] %s %s",$order["com_r_zip_code"],$order["com_r_addr1"],$order["com_r_addr2"]);

        $payment = $this->get_payment_info($ord_no); //결제 정보 리턴

        $query = /** @lang text */
            "
            select
                o.ord_no,o.ord_opt_no, ord_state, o.clm_state
                , if(ifnull(o.clm_state, 0) = 0
                    , (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = o.ord_state)
                    , (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = o.clm_state)
                 ) as state
                , o.ord_kind
                , ord_kind.code_val as ord_kind_nm
                , o.ord_type
                , ord_type.code_val as ord_type_nm
                , if(g.com_type = 1, g.com_type, o.com_id) as com_id
                , o.head_desc, o.goods_nm, g.goods_no, g.goods_sub, g.style_no
                , concat('$img_svr',replace(g.img,'$cfg_img_size_real','$cfg_img_size_list')) as img
                , o.goods_opt
                , replace(o.goods_opt,'^',' : ') as opt_val
                , o.qty,o.price
                , ifnull(
                    if( o.ord_state < 10, o.qty, (
                            select sum(qty) from order_opt_wonga where ord_opt_no = o.ord_opt_no and ord_state = 10
                        )
                    ), 0
                 ) as wqty
                , ifnull(
                    ( select sum(good_qty) from goods_summary
                        where goods_no = g.goods_no and goods_sub = g.goods_sub and goods_opt = o.goods_opt
                    ), 0
                 ) as jaego_qty
                , o.coupon_amt,o.dlv_amt
                , c.refund_amt, o.add_point
                , g.is_unlimited, g.goods_type
                , o.opt_amt, o.addopt_amt, o.dlv_comment
            from order_opt o
                inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
                inner join company cm on o.com_id = cm.com_id
                left outer join claim c on o.ord_opt_no = c.ord_opt_no
                left outer join code ord_type on ord_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = ord_type.code_id
                left outer join code ord_kind on ord_kind.code_kind_cd = 'G_ORD_KIND' and o.ord_kind = ord_kind.code_id
            where ord_no = :ord_no and cm.com_id = :com_id
            order by com_id, o.ord_opt_no desc
        ";
        $goods_info = DB::select($query,[
            "ord_no" => $ord_no,
            "com_id" => $com_id,
        ]);

        //dd($goods_info);
        $order_products = [];

        $pcom_id = "";
        $pcom_idx = 0;
        $com_cnt = 1;
        $com_dlv_amt = 0;

        foreach($goods_info as $row) { // 상품 수 만큼 실행
            $row = (array)$row;

            $_dlv_amt = $row["dlv_amt"];

            $row["addopts"] = $this->get_addopts_info($ord_opt_no); // 추가 옵션 정보

            $row["goods_nm_short"] = Lib::cutString($row["goods_nm"],28);

            $order_products[] = $row;

            // 업체별 배송비 처리
            if($pcom_id != $row["com_id"]){
                $idx = count($order_products)-1;
                $order_products[$pcom_idx]["com_cnt"] = $com_cnt;
                $order_products[$pcom_idx]["dlv_grp_amt"] = Lib::cm($com_dlv_amt);

                $com_cnt = 1;
                $com_dlv_amt = $_dlv_amt;
                $pcom_idx = $idx;

            } else {
                $com_cnt++;
                $com_dlv_amt += $_dlv_amt;
            }
            $pcom_id = $row["com_id"];
        }

        $claim = $this->get_claim($ord_opt_no); // 클레임 정보
        $claim_yn = "N";
        if($claim){
            $claim_yn = "Y";
        }

        $claim_msgs = $this->get_claim_msgs($ord_opt_no); // 클레임 메시지 정보
        //dd($claim_msgs[0]['cs_form']);

        $accounts = $this->get_accounts_etc_info($ord_opt_no); // 입점업체 기타 정산 정보
        //dd($accounts);
        return view( Config::get('shop.partner.view') . '/order/ord01_show', 
            [
                "ord_no" => $ord_no,
                "ord_opt_no" => $ord_opt_no,
                "order" => $order,
                "order_products" => $order_products,
                "payment" => $payment,
                "claim_yn" => $claim_yn,
                "claim" => $claim,
                "claim_msgs" => $claim_msgs,
                "accounts" => $accounts,
            ]);
    }

    public function claim_comments_store(Request $request){

        $ord_opt_no = $request->input('ord_opt_no');
        $memo = $request->input('memo');
        $cs_form = $request->input('cs_form','01');
        $req_claim_gubun = $request->input('req_claim_gubun','REFUND');

        $validator = $request->validate([
            'ord_opt_no' => 'required',
        ]);

        $user = Auth('partner')->user();
        $claim = new Claim([
            'id' => $user->com_id,
            'name' => $user->name
        ]);
        $claim->SetOrdOptNo($ord_opt_no);
        $claim->AddComments($memo,'partner',$cs_form,$req_claim_gubun);
        return response()->json([
            "code" => 200
        ]);
    }

    private function get_ord_opt_no($ord_no, $com_id){
        $query = /** @lang text */
            "
				select max(ord_opt_no) as ord_opt_no
				from order_opt
				where ord_no = :ord_no and com_id = :com_id
			";
        $rows = DB::select($query, ["ord_no" => $ord_no,"com_id" => $com_id]);
        return $rows[0]->ord_opt_no;
    }

    public function dlv($ord_no, $ord_opt_no, Request $req) {
        // 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_name			= $conf->getConfigValue("shop","name");
		$cfg_img_size_detail	= SLib::getCodesValue("G_IMG_SIZE","detail");
		$cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

        //주문정보
		$ordSql = "
            /* admin : order/ord01_dlv.php (1) */
            select a.r_nm, a.r_phone, a.r_mobile, a.r_zipcode, a.r_addr1, a.r_addr2, a.dlv_msg, b.dlv_no, b.dlv_cd
            from order_mst a
                inner join order_opt b on a.ord_no = b.ord_no
            where a.ord_no = '$ord_no'
        ";

		// 상품 택배 정보
		$dlvSql = "
            select
                o.ord_opt_no, ord_state, o.clm_state
                , if(ifnull(o.clm_state, 0) = 0
                    , (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = o.ord_state)
                    , (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = o.clm_state)
                ) as order_state
                , o.ord_kind
                , ord_kind.code_val as ord_kind_nm
                , o.ord_type
                , ord_type.code_val as ord_type_nm
                , if(g.com_type = 1, g.com_type, o.com_id) as com_id
                , if(g.com_type = 1, '$cfg_shop_name', cm.com_nm) as com_nm
                , o.head_desc, o.goods_nm, g.goods_no, g.goods_sub, g.style_no, replace(g.img,'$cfg_img_size_real','$cfg_img_size_detail') as img
                , o.goods_opt
                , replace(o.goods_opt,'^',' : ') as opt_val
                , o.qty,o.price
                , ifnull(
                    if( o.ord_state < 10, o.qty, (
                            select sum(qty) from order_opt_wonga where ord_opt_no = o.ord_opt_no and ord_state = 10
                        )
                    ), 0
                ) as wqty
                , ifnull(
                    ( select sum(good_qty) from goods_summary
                        where goods_no = g.goods_no and goods_sub = g.goods_sub and goods_opt = o.goods_opt
                    ), 0
                ) as jaego_qty
                , ifnull(
                    ( select sum(wqty) from goods_summary
                        where goods_no = g.goods_no and goods_sub = g.goods_sub and goods_opt = o.goods_opt
                    ), 0
                ) as stock_qty
                , g.is_unlimited, g.goods_type
                , o.dlv_cd, o.dlv_no, dlv.code_val as dlv_nm, dlv.code_val2 as dlv_homepage
            from order_opt o
                inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
                inner join company cm on o.com_id = cm.com_id
                -- left outer join claim c on o.ord_opt_no = c.ord_opt_no
                left outer join code ord_type on ord_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = ord_type.code_id
                left outer join code ord_kind on ord_kind.code_kind_cd = 'G_ORD_KIND' and o.ord_kind = ord_kind.code_id
                -- left outer join order_opt_memo om on o.ord_opt_no = om.ord_opt_no
                left outer join code dlv on dlv.code_kind_cd = 'DELIVERY' and o.dlv_cd = dlv.code_id
            where o.ord_no = '$ord_no' and g.goods_type <> 'O'
            order by com_id, o.ord_opt_no desc
        ";
        $rows = DB::select($dlvSql);
        $dlvs = [];
        foreach($rows as $row) {
			$_ord_opt_no	= $row->ord_opt_no;
			$is_unlimited	= $row->is_unlimited;		// 2008-09-25 추가

			$choice_class		= "";

			if($ord_opt_no == $_ord_opt_no){
				$choice_class = "choice";
			}

			// 추가 옵션
			$sql = "
				select addopt, addopt_amt, addopt_qty
				from order_opt_addopt
				where ord_opt_no = '$_ord_opt_no'
            ";
            $rows2 = DB::select($sql);

            $a_addopts = [];

            foreach($rows2 as $row2) {
                $a_addopts[] = $row2;
            }

			array_push($dlvs,
				array(
					"ord_no"			=> $ord_no,
					"ord_opt_no"		=> $_ord_opt_no,
					"ord_state_nm"		=> $row->order_state,
					"ord_state"			=> $row->ord_state,
					"ord_kind"			=> $row->ord_kind_nm,
					"ord_type"			=> ($row->ord_type == 0) ? "정상" : $row->ord_type_nm,
					"head_desc"			=> $row->head_desc,
					"goods_no"			=> $row->goods_no,
					"goods_sub"			=> $row->goods_sub,
					"goods_nm"			=> $row->goods_nm,
					"goods_snm"			=> mb_substr($row->goods_nm, 0, 28),
					"img"				=> $row->img,
					"com_nm"			=> $row->com_nm,
					"style_no"			=> $row->style_no,
					"opt_val"			=> $row->opt_val,
					"goods_opt"			=> $row->goods_opt,
					"price"				=> $row->price,
					"qty"				=> $row->qty,
					"wqty"				=> $row->wqty,
					"jaego_qty"			=> ( $is_unlimited == "Y" ) ? "∞" : $row->jaego_qty,
					"stock_qty"			=> $row->stock_qty,
					"addopts"			=> $a_addopts,
					"dlv_cd"			=> $row->dlv_cd,
					"dlv_no"			=> trim($row->dlv_no),
					"dlv_nm"			=> $row->dlv_nm,
					"dlv_homepage"		=> $row->dlv_homepage,
					"dlv_cds"			=> SLib::getCodes("DELIVERY"),
					"choice_class"		=> $choice_class
				)
			);
        }
        $values = [
            'ord_no' => $ord_no,
            'ord_opt_no' => $ord_opt_no,
            'ord' => DB::selectOne($ordSql),
            'dlvs' => $dlvs
        ];
        // dd($values);
        return view( Config::get('shop.partner.view') . '/order/ord01_dlv',$values);
    }

    public function dlv_info_save($ord_no, Request $req){
        try {
            $r_nm		= $req->input("r_nm", "");
            $r_phone	= $req->input("r_phone", "");
            $r_mobile	= $req->input("r_mobile", "");
            $r_zipcode	= $req->input("r_zipcode", "");
            $r_addr1	= $req->input("r_addr1", "");
            $r_addr2	= $req->input("r_addr2", "");
            $dlv_msg	= $req->input("dlv_msg", "");

            $ord_opt_nos = $req->input("ord_opt_nos", "");

            DB::beginTransaction();

            DB::table("order_mst")
                ->where("ord_no",$ord_no)
                ->update([
                    'r_nm' => $r_nm,
                    'r_phone' => $r_phone,
                    'r_mobile' => $r_mobile,
                    'r_zipcode' => $r_zipcode,
                    'r_addr1' => $r_addr1,
                    'r_addr2' => $r_addr2,
                    'dlv_msg' => $dlv_msg,
                    'upd_date' => DB::raw("now()")
                ]);

            // 택배사, 송장번호 수정
            for( $i = 0; $i < count($ord_opt_nos); $i++){

                $_ord_opt_no = $ord_opt_nos[$i];
                $_dlv_no = $req->input("dlv_no_". $_ord_opt_no );
                $_dlv_cd = $req->input("dlv_cd_". $_ord_opt_no );

                if( $_dlv_no != "" && $_dlv_cd != "" ){
                    DB::table("order_opt")
                        ->where("ord_opt_no",$_ord_opt_no)
                        ->update([
                            'dlv_cd' => $_dlv_cd,
                            'dlv_no' => $_dlv_no
                            ]);
                }
            }

            DB::commit();
            return true;
        }catch(Exception $e) {
            DB::rollback();
            return false;
        }

    }


    private function get_order_info($ord_opt_no, $com_id){
        $query = /** @lang text */
            "
            select
                date_format(b.ord_date,'%Y.%m.%d %H:%i:%s') ord_date, a.ord_kind, b.user_id, b.user_nm, b.phone, b.mobile, b.email,
                b.r_nm, b.r_phone, b.r_mobile, b.r_zipcode, b.r_addr1, b.r_addr2,
                b.dlv_msg, a.com_id, b.url, a.ord_state, ord_state.code_val ord_state_nm, a.dlv_no ,
                c.code_val dlv_cd,c.code_val2 dlv_homepage,
                date_format(b.dlv_end_date,'%Y.%m.%d %H:%i:%s') mst_dlv_end_date, d.com_nm sale_place, b.dlv_amt, b.add_dlv_fee, a.add_point,
                date_format(a.dlv_start_date,'%Y.%m.%d %H:%i:%s') dlv_start_date,
                date_format(a.dlv_proc_date,'%Y.%m.%d %H:%i:%s') dlv_proc_date,
                date_format(a.dlv_end_date,'%Y.%m.%d %H:%i:%s') dlv_end_date,
                date_format(b.upd_date,'%Y.%m.%d %H:%i:%s') upd_date, a.dlv_comment, a.p_ord_opt_no,
                company.com_type, com_type.code_val com_type_nm, company.com_nm, company.staff_nm1, company.last_login_date,
                company.staff_phone1, company.staff_hp1,
                company.zip_code, company.addr1, company.addr2,
                company.r_zip_code as com_r_zip_code, company.r_addr1 as com_r_addr1, company.r_addr2  as com_r_addr2,
                company.md_nm, a.price
            from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
                inner join company company on a.com_id = company.com_id
                left outer join code c on c.code_kind_cd = 'DELIVERY' and a.dlv_cd = c.code_id
                left outer join company d on a.sale_place = d.com_id and d.com_type= '4'
                left outer join code com_type on com_type.code_kind_cd = 'G_COM_TYPE' and company.com_type = com_type.code_id
                left outer join code ord_state on ord_state.code_kind_cd = 'G_ORD_STATE' and a.ord_state = ord_state.code_id
            where a.ord_opt_no = :ord_opt_no and a.com_id = :com_id
        ";
        //echo "<pre>$query $ord_opt_no $com_id</pre>";
        $order = (array)DB::selectone($query,[
            //"ord_no" => $ord_no,
            "ord_opt_no" => $ord_opt_no,
            "com_id" => $com_id
        ]);

        // parent order
        $p_ord_no = "";
        if($order["p_ord_opt_no"] > 0){
            $p_ord_no = DB::table("order_opt")->where("ord_opt_no",$order["p_ord_opt_no"])->value("ord_no");
        }
        $order["p_ord_no"] = $p_ord_no;

        // child order

        $order["c_ord_no"] = "";
        $order["c_ord_opt_no"] = "";
        $porder = (array)DB::table("order_opt")
                    ->where("p_ord_opt_no",$ord_opt_no)
                    ->select('ord_no','ord_opt_no')->first();
        if($porder){
            $order["c_ord_no"] = $porder["ord_no"];
            $order["c_ord_opt_no"] = $porder["ord_opt_no"];
        }

        return $order;
    }

    private function get_payment_info($ord_no){
        $query = /** @lang text */
            "
            select
                a.pay_type, pay_type.code_val pay_type_nm, a.pay_amt, a.pay_point, a.pay_nm
                , a.pay_stat, pay_stat.code_val pay_stat_nm, a.bank_inpnm, a.bank_code, a.bank_number
                , a.card_code, a.card_isscode
                , a.card_quota, a.card_appr_no
                , date_format(a.card_appr_dm,'%Y.%m.%d %H:%i:%s') card_appr_dm, a.card_tid, a.tno, a.card_msg
                , date_format(a.ord_dm,'%Y.%m.%d %H:%i:%s') ord_dm, date_format(a.upd_dm,'%Y.%m.%d %H:%i:%s') as pay_upd_dm
                , a.pay_ypoint, a.pay_baesong, a.card_name, a.nointf, a.ghost_use, a.escw_use, a.tno
                , a.st_cd, ifnull(a.coupon_amt,0) coupon_amt
                , cr.bank_code as cr_bank_code
            from payment a
                left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
                left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STATE')
                left outer join common_return cr on cr.order_no = a.ord_no
            where a.ord_no = :ord_no
        ";
        $rows = DB::select($query,[
            "ord_no" => $ord_no,
        ]);
        return (array)$rows[0];
    }

    private function get_addopts_info($ord_opt_no){
        $query = /** @lang text */
            "
                select addopt, addopt_amt, addopt_qty
                from order_opt_addopt
                where ord_opt_no = :ord_opt_no
            ";
        $rows = DB::select($query,["ord_opt_no" => $ord_opt_no]);
        return (array)$rows;
    }

    private function get_claim($ord_opt_no){

        $query = /** @lang text */
            "
            select
                clm_no, clm_state
                , ( select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = c.clm_state ) as clm_state_nm
                , ( select code_val from code where code_kind_cd = 'G_CLM_REASON' and code_id = c.clm_reason ) as clm_reason_nm
                , clm_reason, refund_yn, refund_amt, refund_bank, refund_account, refund_nm, memo
                , date_format(req_date,'%Y.%m.%d %H:%i:%s') as req_date
                , date_format(proc_date,'%Y.%m.%d %H:%i:%s') as proc_date
                , date_format(end_date,'%Y.%m.%d %H:%i:%s') as end_date
                , req_nm,proc_nm,end_nm,date_format(last_up_date,'%Y.%m.%d %H:%i:%s') as last_up_date
                , dlv_deduct
            from claim c
            where
                ord_opt_no = :ord_opt_no
        ";
        $rows = DB::selectone($query,[
            "ord_opt_no" => $ord_opt_no,
        ]);

        return $rows;
    }

    private function get_claim_msgs($ord_opt_no){
        $sql = /** @lang text */
            "
            select
                a.memo_no, a.admin_id, a.admin_nm
                , date_format(a.regi_date, '%y.%m.%d %H:%i:%s') as regi_date, a.memo
                , cd.code_val as cs_form
                , cd2.code_val as ord_state
                , if(cd3.code_id is not null,cd3.code_val,cd2.code_val) as clm_state
            from claim_memo a
                left outer join code cd on cd.code_kind_cd = 'CS_FORM2' and cd.code_id = a.cs_form
                left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_STATE' and cd2.code_id = a.ord_state
                left outer join code cd3 on cd3.code_kind_cd = 'G_CLM_STATE' and cd3.code_id = a.clm_state
            where a.ord_opt_no = :ord_opt_no
            order by a.regi_date asc
        ";

        $rows = DB::select($sql,[
            "ord_opt_no" => $ord_opt_no,
        ]);
        $claim_msgs = array();

        $claim_msgs = (array)$rows;

        return $claim_msgs;
    }

    private function get_accounts_etc_info($ord_opt_no){
        $accounts = array();
        $query = /** @lang text */
            "
            select
                etc_day, etc_amt, etc_memo, admin_nm, regi_date
            from account_etc where ord_opt_no = :ord_opt_no
        ";
        $accounts = DB::select($query,[
            "ord_opt_no" => $ord_opt_no,
        ]);
        return $accounts;
    }

    private function replaceTel($tel) {

        $tel = trim($tel);

        if(strpos($tel,"-") === false){

            $len = strlen($tel);

            if($len == 9){

                $patterns = array ("/(\d{2})(\d{3})(\d{4})/");
                $replace = array ("\\1-\\2-\\3");
                $tel =  preg_replace ($patterns, $replace,$tel);

            } else if($len == 10){

                if(substr($tel,0,2) == "02"){
                    $patterns = array ("/(\d{2})(\d{4})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                } else {
                    $patterns = array ("/(\d{3})(\d{3})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                }

            } else if($len == 11){

                if(substr($tel,0,4) == "0505"){
                    $patterns = array ("/(\d{4})(\d{3})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                } else {
                    $patterns = array ("/(\d{3})(\d{4})(\d{4})/");
                    $replace = array ("\\1-\\2-\\3");
                    $tel =  preg_replace ($patterns, $replace,$tel);
                }

            } else if($len == 12){

                $patterns = array ("/(\d{4})(\d{4})(\d{4})/");
                $replace = array ("\\1-\\2-\\3");
                $tel =  preg_replace ($patterns, $replace,$tel);
            }
            return $tel;

        } else {
            return $tel;
        }
    }

}
