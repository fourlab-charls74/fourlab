<?php

namespace App\Http\Controllers\head\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Jaego;
use App\Models\Order;
use App\Models\Point;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ord02Controller extends Controller
{
    public function index(Request $req)
    {

        $mutable = now();
        $sdate = $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'ord_states' => SLib::getordStates(),
            'com_types' => SLib::getCodes('G_COM_TYPE'),
            'ord_types' => SLib::getCodes('G_ord_TYPE'),
            'ord_kinds' => SLib::getCodes('G_ord_KIND'),
            'dlv_types' => SLib::getCodes('G_G_DLV_TYPE'),
            'sale_places' => SLib::getSalePlaces(),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'clm_states' => SLib::getCodes('G_CLM_STATE'),
            'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'),
        ];

        return view(Config::get('shop.head.view') . '/order/ord02', $values);
    }

    public function show($ord_no,$ord_opt_no = '')
    {
        $sql = /** @lang text */
            "
            select 
                concat(code_val,'_',ifnull(code_val2, '')) as 'name',
                concat(code_val,' [',ifnull(code_val2, ''),']') as 'value'
            from code 
            where code_kind_cd ='BANK' 
                and code_id != 'K' 
                and use_yn = 'Y' 
            order by code_seq        
        ";
        $banks = DB::select($sql);

        $conf = new Conf();

        $pay_types = DB::select("SELECT code_id, code_val FROM `code`
			WHERE code_kind_cd = 'G_PAY_TYPE' AND code_id <> 'K' AND code_id IN ('1','2','5','9','13','16','32','64')
			ORDER BY code_seq
        ");

        $values = [
            'ord_no' => $ord_no,
            'ord_opt_no' => $ord_opt_no,
            'banks' => $banks,
            'pay_types' => $pay_types,
            'ord_types' => SLib::getCodes('G_ORD_TYPE'),
            'sale_places' => SLib::getSalePlaces(),
            'dlv_cds' => SLib::getCodes('DELIVERY'),
            'dlv_fee' => array(
                'base_dlv_fee' => $conf->getConfigValue("delivery", "base_delivery_fee"), 
                'add_dlv_fee' => $conf->getConfigValue("delivery", "add_delivery_fee"), 
                'free_dlv_amt' => $conf->getConfigValue("delivery", "free_delivery_amt")
            ),
        ];

        return view(Config::get('shop.head.view') . '/order/ord02_show', $values);
    }

    function GetOptionKind($goods_no, $goods_sub)
    {
        $sql = "
        select name
        from goods_option
        where goods_no = '$goods_no'
          and goods_sub = '$goods_sub'
          and use_yn = 'Y'
          and type = 'basic'
        order by no
      ";

        return DB::select($sql);
    }

    function GetOptionCombo($goods_no, $goods_sub, $goods_opt = "", $depth = 1, $is_utf = false)
    {
        // 부모상품코드 설정
        $p_goods_no = $goods_no;
        $p_goods_sub = $goods_sub;

        $option = array();

        $option_names = $this->GetOptionKind($goods_no, $goods_sub);
        $is_multi_option = (count($option_names) > 1) ? "Y" : "N";

        // 옵션명
        $opt_name = "";
        if (count($option_names) == 2) {
            $opt1_name = isset($option_names[0]) ? $option_names[0] : "";
            $opt2_name = isset($option_names[1]) ? $option_names[1] : "";
            $opt_name = $opt1_name . "^" . $opt2_name;
        } elseif (count($option_names) == 1) {
            $opt_name = isset($option_names[0]) ? $option_names[0] : "";
        }

        $where = ($goods_opt != "" and $depth > 1) ? " and a.goods_opt like '" . $goods_opt . "^%' " : "";
        //$where .= ( $opt_name != "" ) ? " and a.opt_name = '$opt_name' ":"";

        $sql = "
        select
          a.goods_opt, a.opt_price,  a.good_qty
          , b.baesong_info, cd.code_val as baesong_info_str, b.bae_yn, b.baesong_price, b.is_unlimited
        from goods_summary a
          inner join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
          inner join code cd on cd.code_kind_cd = 'G_BAESONG_INFO' and cd.code_id = b.baesong_info
        where a.goods_no = '$goods_no'
          and a.goods_sub = '$goods_sub'
          and a.good_qty > 0
          $where
        order by a.seq
      ";
        $rows = DB::select($sql);
        foreach ($rows as $row) {
            $goods_opt = $row->goods_opt;
            $opt_price = ($depth == 1) ? $row->opt_price : 0;
            $good_qty = $row->good_qty;
            $is_unlimited = $row->is_unlimited;
            $baesong_info = $row->baesong_info;
            $bae_yn = $row->bae_yn;
            $baesong_price = $row->baesong_price;

            $tmp_txt = "";
            $tmp_val = "";

            $tmp = explode("\^", $goods_opt);
            if ($is_multi_option == "Y") {    // 멀티 옵션인 경우

                $tmp_txt = $tmp[$depth - 1];
                $tmp_val = $tmp[$depth - 1] . "|" . $goods_no . "|" . $goods_sub . "|" . $p_goods_no . "|" . $p_goods_sub . "|" . $tmp[$depth - 1] . "|" . $baesong_info . "|" . $bae_yn . "|" . $baesong_price;
                if ($depth == 2) {
                    $tmp_val = $tmp[$depth - 1];
                }
                if ($opt_price > 0) {
                    $tmp_txt .= " (+" . number_format($opt_price) . "원)";
                    $tmp_val .= "|$opt_price";
                }
            } else {    // 단일 옵션인 경우

                $tmp_txt = $goods_opt;
                $tmp_val = $goods_opt . "|" . $goods_no . "|" . $goods_sub . "|" . $p_goods_no . "|" . $p_goods_sub . "|" . $goods_opt . "|" . $baesong_info . "|" . $bae_yn . "|" . $baesong_price;
                if ($depth == 2) {
                    $tmp_val = $tmp[$depth - 1];
                }
                if ($opt_price > 0) {
                    $tmp_txt .= " (+" . number_format($opt_price) . "원)";
                    $tmp_val .= "|$opt_price";
                }
            }
            if ($tmp_txt != "") {
                $tmp_txt = ($is_utf) ? iconv("UTF-8", "UTF-8", $tmp_txt) : $tmp_txt;
                $tmp_val = ($is_utf) ? iconv("UTF-8", "UTF-8", $tmp_val) : $tmp_val;

                $check = 0;

                if (count($option) > 0) {
                    for ($i = 0; $i < count($option); $i++) {
                        if (in_array($tmp_val, $option[$i], true)) {
                            $check = 1;
                            break;
                        }
                    }

                    if ($check == 0) {
                        $option[] = array("val" => $tmp_val, "txt" => $tmp_txt);
                    }
                } else {
                    $option[] = array("val" => $tmp_val, "txt" => $tmp_txt);
                }
            }
        }
        return $option;
    }

    /*
        Function: GetOptionKind
        옵션 구분 얻기

        Parameters:
          $goods_no 	- 상품코드1
          $goods_sub	- 상품코드2

        Returns:
          $a_option_kind - 옵션구분 $a_option_kind : array( "color", "size" )
      */

    function GetAddoptTitle($goods_no, $goods_sub)
    {
        $a_addopt_title = array();
        $i = 1;
        $sql = "
        select name, required_yn
        from goods_option
        where goods_no = '$goods_no'
          and goods_sub = '$goods_sub'
          and type = 'extra'
          and use_yn = 'Y'
        order by seq, no
      ";
        $rows = DB::select($sql);
        foreach ($rows as $row) {
            $a_addopt_title = [
                "seq" => $i++,
                "name" => $row->name,
                "required_yn" => $row->required_yn
            ];
        }

        return $a_addopt_title;
    }

    /*
        Function: GetAddoptTitle
        추가 옵션 구분 얻기

        Parameters:
          $goods_no 	- 상품코드1
          $goods_sub	- 상품코드2

        Returns:
          $a_addopt_title - 추가 옵션 구분
      */

    function GetAddopt($goods_no, $goods_sub)
    {

        $a_addopt = array();
        $sql = "
        select no, name
        from goods_option
        where goods_no = '$goods_no'
          and goods_sub = '$goods_sub'
          and type = 'extra'
        order by no asc
      ";
        $rows = DB::select($sql);
        foreach ($rows as $row) {
            $no = $row->no;
            $name = $row->name;

            $sql = "
          select `option`, price, soldout_yn, no
          from options
          where option_no = '$no'
            and use_yn = 'Y'
            and qty > 0
          order by seq
        ";
            $rows2 = DB::select($sql);
            $a_opt = array();
            foreach ($rows2 as $row2) {
                $a_opt[] = array(
                    "option" => $row2->option, "price" => $row2->price, "soldout_yn" => $row2->soldout_yn, "no" => $row2->no, "goods_no" => $goods_no, "goods_sub" => $goods_sub
                );
            }
            $a_addopt[$name] = $a_opt;
        }
        return $a_addopt;
    }

    /*
        Function: GetAddopt
        추가 옵션 얻기

        Parameters:
          $goods_no 	- 상품코드1
          $goods_sub	- 상품코드2

        Returns:
          $a_addopt - 추가 옵션
      */

    public function search(Request $req)
    {
        // 설정 값 얻기
        $conf = new Conf();

        $cfg_img_size_list = SLib::getCodesValue('G_IMG_SIZE', 'list');
        $cfg_img_size_real = SLib::getCodesValue('G_IMG_SIZE', 'list');
        $cfg_domain_img = $conf->getConfigValue("shop", "domain_img");

        if ($cfg_domain_img == "") {
            $cfg_domain_img = $_SERVER["HTTP_HOST"];
        }

        $goods_img_url = sprintf("http://%s", $cfg_domain_img);

        $page = $req->input("page", 1);
        if ($page < 1 or $page == "") $page = 1;

        $edate = $req->input("edate", date("Ymd"));
        $sdate = $req->input("sdate", now()->sub(3, 'month')->format('Ymd'));
        $ord_no = $req->input("ord_no", "");
        $user_nm = $req->input("user_nm", "");
        $user_id = $req->input("user_id", "");
        $goods_no = $req->input("goods_no", "");
        $style_no = $req->input("style_no", "");
        $r_nm = $req->input("r_nm", "");
        $bank_inpnm = $req->input("bank_inpnm", "");
        $stat_pay_type = $req->input("stat_pay_type", "");
        $ord_state = $req->input("ord_state", "");
        $clm_state = $req->input("clm_state", "");
        $dlv_no = $req->input("dlv_no", "");
        $sale_place = $req->input("sale_place", "");
        $com_type = $req->input("com_type", "");
        $com_id = $req->input("com_id", "");
        $out_ord_no = $req->input("out_ord_no", "");
        $cols = $req->input("cols", "");
        $baesong_kind = $req->input("baesong_kind", "");
        $dlv_cm = $req->input("dlv_cm", "");
        $ord_type = $req->input("ord_type", "");
        $ord_kind = $req->input("ord_kind", "");
        $goods_type = $req->input("goods_type", "");
        $brand_cd = $req->input("brand_cd", "");
        $brand_nm = $req->input("brand_nm", "");
        $goods_nm = $req->input("goods_nm", "");
        $head_desc = $req->input("head_desc", "");
        $limit = $req->input("limit", 100);
        $ord_field = $req->input("ord_field", "a.ord_no");
        $ord = $req->input("ord", "desc");
        $opt_kind_cd = $req->input("opt_kind_cd", "");
        $not_complex = $req->input("not_complex", "");
        $baesong_info = $req->input("baesong_info", "");
        $special_yn = $req->input("special_yn", "");
        $key = $req->input("key", "");
        $nud = $req->input("nud", "N");
        $pay_nm = $req->input("pay_nm", "");
        $pay_stat = $req->input("pay_stat", "");
        $goods = $req->input("goods", "");    // 상품선택
        $mobile_yn = $req->input("mobile_yn", "");  // 모바일 주문 여부
        $app_yn = $req->input("app_yn", "");    // 앱 주문 여부
        $receipt = $req->input("receipt", "N");  // 현금영수증 : N(미신청), R(신청), Y(발행)
        $dlv_type = $req->input("dlv_type", "");  // 배송방식: D(택배), T(택배(당일배송)), G(직접수령)
        $pay_fee = $req->input("pay_fee", "");  // 결제수수료 주문
        $fintech = $req->input("fintech", "");  // 핀테크

        $str_order_by = $ord_field . " " . $ord;
        if ($ord_field == "a.head_desc") { // 상단 홍보글인경우, 상단홍보글, 상품명 순.
            $str_order_by = $ord_field . " " . $ord . " ,a.goods_nm " . $ord;
        }

        $where = " and a.ord_kind != '10' "; // 정상판매건이 아닌 경우에만 출력한다!
        $is_not_use_date = false;

        /////////////////////////////////////////////////////////
        // 날짜검색 미 사용여부

        if ($ord_no != "") {
            $is_not_use_date = true;
        } else if ($user_id != "") {
            $is_not_use_date = true;
        } else if ($user_nm != "") {
            $is_not_use_date = true;
        } else if (strlen($r_nm) >= 4) {
            $is_not_use_date = true;
        } else if ($cols == "b.mobile" && strlen($key) >= 8) {
            $is_not_use_date = true;
        } else if ($cols == "b.phone" && strlen($key) >= 8) {
            $is_not_use_date = true;
        } else if ($cols == "b.r_mobile" && strlen($key) >= 8) {
            $is_not_use_date = true;
        }

        if ($is_not_use_date == true && $nud == "Y") {
        } else {
            $where .= " and a.ord_date >= cast('$sdate' as date) ";
            $where .= " and a.ord_date < DATE_ADD('$edate', INTERVAL 1 DAY) ";
        }

        if ($ord_no != "") $where .= " and a.ord_no = '$ord_no' ";
        if ($user_nm != "") $where .= " and b.user_nm = '$user_nm' ";
        if ($r_nm != "") $where .= " and b.r_nm = '$r_nm' ";
        if ($user_id != "") $where .= " and b.user_id = '$user_id' ";
        if ($pay_nm != "") $where .= " and d.pay_nm like '$pay_nm%' ";

        if ($cols != "" && $key != "") {
            if (in_array($cols, array("b.mobile", "b.phone", "b.r_phone", "b.r_mobile"))) {
                $key = $this->__replaceTel($key);
                if ($cols == "b.mobile" || $cols == "b.phone" || $cols == "b.r_mobile") {
                    $where .= " and $cols = '$key' ";
                } else {
                    $where .= " and $cols like '$key%' ";
                }
            } else {
                if ($cols == "memo") {
                    $where = " and ( h.state like '%$key%' or h.memo like '%$key%' )";
                } else if ($cols == "a.dlv_end_date") {
                    $where = " and date_format($cols, '%Y%m%d') = $key";
                } else {
                    $where .= " and $cols like '$key%' ";
                }
            }
        }

        if ($sale_place != "") $where .= " and a.sale_place = '$sale_place' ";
        if ($com_type != "") $where .= " and c.com_type = '$com_type' ";
        if ($com_id != "") $where .= " and c.com_id = '$com_id' ";
        if ($ord_kind != "") $where .= " and a.ord_kind = '$ord_kind' ";
        if ($ord_type != "") $where .= " and a.ord_type = '$ord_type' ";
        if ($bank_inpnm != "") $where .= " and d.bank_inpnm = '$bank_inpnm' ";

        // 결제조건
        if ($stat_pay_type != "") {
            if ($not_complex == "Y") {
                $where .= " and a.pay_type = '$stat_pay_type' ";
            } else {
                $where .= " and (( a.pay_type & $stat_pay_type ) = $stat_pay_type) ";
            }
        }
        if ($ord_state != "") $where .= " and a.ord_state = '$ord_state' ";
        if ($clm_state == "90") $where .= " and a.clm_state = 0 ";
        else {
            if ($clm_state != "") {
                $where .= " and a.clm_state = '$clm_state' ";
            }
        }
        if ($dlv_cm != "") $where .= " and g.dlv_cm = '$dlv_cm' ";

        if ($bank_inpnm != "") $where .= " and d.bank_inpnm = '$bank_inpnm' ";

        if ($opt_kind_cd != "") $where .= " and OPT_KIND_CD = '$opt_kind_cd' ";

        if ($brand_cd != "") {
            $where .= " and c.brand ='$brand_cd'";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and c.brand ='$brand_cd'";
        }

        if ($goods_nm != "") $where .= " and a.goods_nm like '%$goods_nm%'";
        if ($style_no != "") $where .= " and c.style_no like '$style_no%'";
        //if($goods_no != "")		$where .= " and c.goods_no = '$goods_no' ";

        $goods_no = preg_replace("/\s/", ",", $goods_no);
        $goods_no = preg_replace("/\t/", ",", $goods_no);
        $goods_no = preg_replace("/\n/", ",", $goods_no);
        $goods_no = preg_replace("/,,/", ",", $goods_no);

        if ($goods_no != "") {
            $goods_nos = explode(",", $goods_no);
            if (count($goods_nos) > 1) {
                if (count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(",", $goods_nos);
                $where .= " and c.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and c.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        if ($head_desc != "") $where .= " and a.head_desc like '%$head_desc%' ";
        if ($special_yn != "") $where .= " and c.special_yn = '$special_yn' ";
        if ($baesong_info != "") $where .= " and c.baesong_info = '$baesong_info' ";
        if ($goods_type != "") $where .= " and c.goods_type = '$goods_type' ";
        if ($out_ord_no != "") $where .= " and b.out_ord_no = '$out_ord_no' ";
        if ($dlv_no != "") $where .= " and a.dlv_no = '$dlv_no' ";
        if ($pay_stat != "") $where .= " and d.pay_stat = '$pay_stat' ";

        $id = Auth('head')->user()->id;
        $ip = $_SERVER["REMOTE_ADDR"];

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

        // 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
        if ($page == 1) {
            // 갯수 얻기
            $sql = " /* [$id][$ip] admin : order/ord02Controller.php (1) */
          select
            count(*) total
          from order_opt a
            inner join order_mst b on a.ord_no = b.ord_no
            inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
            left outer join payment d on b.ord_no = d.ord_no
            inner join company e on a.sale_place = e.com_id and e.com_type = '4'
            left outer join order_opt_memo h on a.ord_opt_no = h.ord_opt_no
          where 1=1 $where
        ";
            $sql2 = " /* [$id][$ip] admin : order/ord02Controller.php (1) */
          select
            count(*) total
          from order_opt a
            inner join order_mst b on a.ord_no = b.ord_no
            inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
            left outer join payment d on b.ord_no = d.ord_no
            inner join company e on a.sale_place = e.com_id and e.com_type = '4'
            left outer join order_opt_memo h on a.ord_opt_no = h.ord_opt_no
          where 1=1 $where
        ";
            $total = DB::selectOne($sql)->total;

            $page_cnt = (int)(($total - 1) / $page_size) + 1;

			/*
            if ($page == 1) {
                $startno = ($page - 1) * $page_size;
            } else {
                $startno = ($page - 1) * $page_size;
            }
            $arr_header = array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt
            );
			*/
        } else {
			/*
            $startno = ($page - 1) * $page_size;
            $arr_header = null;
			*/
        }

        if ($limit == -1) {
            $limit = "";
        } else {
            $limit = " limit $startno, $page_size ";
        }
        $sql = "
				select
					'' as chkbox, a.ord_no, a.ord_opt_no, ord_state.code_val ord_state, clm_state.code_val clm_state, pay_stat.code_val as pay_stat
					, ifnull(gt.code_val,'N/A') as goods_type_nm, a.style_no, '' as img_view, a.goods_nm
					, replace(a.goods_opt, '^', ' : ') as opt_val, a.qty, a.user_nm, a.r_nm, a.goods_price, a.price
					, a.dlv_amt,a.sales_com_fee,pay_type.code_val pay_type
					, ord_type.code_val as ord_type
					, ord_kind.code_val as ord_kind
					, a.sale_place, a.out_ord_no, a.com_nm
					, baesong_kind.code_val as baesong_kind
					, dlv_cd.code_val as dlv_cm, a.dlv_no
					, a.state, a.memo
					, a.ord_date, a.pay_date, a.dlv_end_date
					, a.last_up_date, a.goods_no, a.goods_sub
					, replace(a.img,'$cfg_img_size_real','$cfg_img_size_list') as img
					, a.goods_type
					, '2' as depth
					, a.sms_name, a.sms_mobile
					, if(a.ord_state <= 10 and a.clm_state = 0 and ord_opt_cnt = 0, 'Y', 'N') as ord_del_yn
				from (
					select
						b.ord_no, a.ord_opt_no, a.ord_state, d.pay_stat, c.goods_type, c.style_no, c.goods_nm
						, a.goods_opt, a.qty, concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm, b.r_nm
						, c.price as goods_price, a.price, a.dlv_amt, a.sales_com_fee, d.pay_type
						, a.ord_type, a.ord_kind, a.dlv_cd, a.dlv_no
						, a.clm_state, e.com_nm as sale_place, b.out_ord_no, i.com_nm
						, c.baesong_kind as dlv_baesong_kind, b.ord_date, d.pay_date
						, a.dlv_end_date, g.last_up_date, c.goods_no, c.goods_sub, c.img, c.com_type
						, h.state, h.memo, b.user_nm as sms_name, b.mobile as sms_mobile
						, ( select count(*) from order_opt where ord_no = a.ord_no and ord_opt_no != a.ord_opt_no and (ord_state > 10 or clm_state > 0)) as ord_opt_cnt
					from order_opt a
						inner join order_mst b on a.ord_no = b.ord_no
						inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
						left outer join payment d on b.ord_no = d.ord_no
						left outer join company e on a.sale_place = e.com_id and e.com_type = '4'
						left outer join company i on c.com_id = i.com_id
						left outer join claim g on g.ord_opt_no = a.ord_opt_no
						left outer join order_opt_memo h on a.ord_opt_no = h.ord_opt_no
					where 1=1 $where
					order by $str_order_by
					$limit
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
      ";

        $depth_no = "";
        $rows = DB::select($sql);

        foreach ($rows as $row) {
            $ord_no = $row->ord_no;

            if ($depth_no == "") {
                $depth_no = $ord_no;
                $row->depth = "1";
            }

            if ($ord_no != $depth_no) {
                $row->depth = "1";
                $depth_no = $ord_no;
            }
        }

        //$arr_header['page_total'] = count($rows);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows,
            //"sql" => $sql2
        ]);
    }


    /*
        Function: GetOptionCombo
        옵션 콤보 출력 ( 1, 2 depth )
      */

    private function __replaceTel($tel)
    {

        $tel = trim($tel);

        if (strpos($tel, "-") === false) {

            $len = strlen($tel);

            if ($len == 9) {

                $patterns = array("/(\d{2})(\d{3})(\d{4})/");
                $replace = array("\\1-\\2-\\3");
                $tel = preg_replace($patterns, $replace, $tel);
            } else if ($len == 10) {

                if (substr($tel, 0, 2) == "02") {
                    $patterns = array("/(\d{2})(\d{4})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                } else {
                    $patterns = array("/(\d{3})(\d{3})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                }
            } else if ($len == 11) {

                if (substr($tel, 0, 4) == "0505") {
                    $patterns = array("/(\d{4})(\d{3})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                } else {
                    $patterns = array("/(\d{3})(\d{4})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                }
            } else if ($len == 12) {

                $patterns = array("/(\d{4})(\d{4})(\d{4})/");
                $replace = array("\\1-\\2-\\3");
                $tel = preg_replace($patterns, $replace, $tel);
            }
            return $tel;
        } else {
            return $tel;
        }
    }

    /*
          Function: ReplaceTel
          전화번호 숫자에 '-' 넣는 함수

      Parameters:

              $tel - 전화번호

          Returns:

              String
      */

    public function del_order()
    {
        $ord_nos = Request('ord_nos', []);
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        DB::beginTransaction();
        try {
            $order = new Order($user);
            foreach ($ord_nos as $ord_no) {
                $order->Delete($ord_no);
            }
            DB::commit();
            $code = "200";
            $msg = "";
        } catch(Exception $e){
            DB::rollBack();
            $code = "500";
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ],$code);
    }

    public function save(Request $req)
    {
        // 설정 값 얻기
        $conf = new Conf();
        $cfg_ratio = $conf->getConfigValue("point", "ratio");

        $ord_no = $req->input("ord_no", "");
        $p_ord_opt_no = $req->input("p_ord_opt_no", "");
        $ord_type = $req->input("ord_type", "");
        $ord_kind = $req->input("ord_kind", "");
        $ord_state = $req->input("ord_state", "");
        $sale_place = $req->input("sale_place", "");

        $cart = $req->input("cart");

        $ord_amt = 0;
        $recv_amt = 0;
        $point_amt = 0;
        $coupon_amt = 0;
        $dc_amt = 0;
        $pay_fee = 0;
        $dlv_amt = 0;
        $add_dlv_fee = 0;

        $coupon_no = $req->input("coupon_no", "");
        $pay_type = $req->input("pay_type", "");
        $bank_inpnm = $req->input("bank_inpnm", "");
        $bank_code = $req->input("bank_code", "");
        $bank_number = "";

        if ($bank_code != "") {
            list($bank_code, $bank_number) = explode("_", $bank_code);
        }

        $user_id = $req->input("user_id", "");
        $user_nm = $req->input("user_nm", "");
        $phone = $req->input("phone", "");
        $mobile = $req->input("mobile", "");
        $email = $req->input("email", "");
        $r_nm = $req->input("r_user_nm", "");
        $r_phone = $req->input("r_phone", "");
        $r_mobile = $req->input("r_mobile", "");

        $r_zip_code = $req->input("r_zip_code", "");
        $r_addr1 = $req->input("r_addr1", "");
        $r_addr2 = $req->input("r_addr2", "");
        $dlv_msg = $req->input("dlv_msg", "");

        $r_jumin = $req->input("r_jumin", "");
        $goods_type = $req->input("goods_type", "");
        $give_point = $req->input("give_point", "");
        $group_apply = $req->input("group_apply", "");
        $dlv_cd = $req->input("dlv_cd", "");    // 출고완료시 택배업체
        $dlv_no = $req->input("dlv_no", "");    // 출고완료시 송장번호

        // 추가옵션 및 추가옵션 가격
        //        $goods_addopt = $req->input("goods_addopt", "");
        //        $addopt_amt = $this->CheckInt($req->input("addopt_price", "", 0));
        //        $order_addopt_amt = $addopt_amt * $qty;

        //        if ($user_id == "") $user_id = null;
        //        if ($user_nm == "") $user_nm = null;
        //        if ($phone == "") $phone = null;
        //        if ($mobile == "") $mobile = null;
        //        if ($email == "") $email = null;
        //        if ($r_nm == "") $r_nm = null;
        //        if ($r_phone == "") $r_phone = null;
        //        if ($r_mobile == "") $r_mobile = null;
        //        if ($r_addr1 == "") $r_addr1 = null;
        //        if ($r_addr2 == "") $r_addr2 = null;
        //        if ($dlv_msg == "") $dlv_msg = null;
        //        if ($r_jumin == "") $r_jumin = null;

        try {
            DB::beginTransaction();
            ################################
            #	수기 주문번호 생성
            ################################
            $c_admin_id = Auth('head')->user()->id;
            $c_admin_name = Auth('head')->user()->name;
            $user = [
                'id' => $c_admin_id,
                'name' => $c_admin_name
            ];

            ################################
            # 포인트 지급
            ################################
            $point_flag = false;
            $add_point_ratio = 0;
            $add_point = 0;

            if ($give_point == "Y") {

                // 회원 여부 확인
                $sql = /** @lang text */
                    " select count(*) as cnt from member where user_id = :user_id ";
                $row = DB::selectOne($sql,array("user_id" => $user_id));
                if ($row->cnt == 1) {
                    // 적립금 지급
                    $point_flag = true;
                    if ($group_apply == "Y") {
                        // 회원 그룹 추가 포인트
                        $sql = /** @lang text */
                            "
                            select a.group_no, b.point_ratio
                            from user_group_member a
                                inner join user_group b on a.group_no = b.group_no
                            where a.user_id = :user_id
                                order by b.point_ratio desc
                            limit 0,1
                        ";
                        $group = DB::selectOne($sql,array("user_id" => $user_id));
                        if (!empty($group->point_ratio)) {
                            $add_point_ratio = $group->point_ratio;
                        }
                    }
                }
            }

            // 배송비 계산
            if ($goods_type == "O") {
            } else {
                if ($add_dlv_fee > 0) {
                    $dlv_amt = $dlv_amt - $add_dlv_fee;
                }
            }

            $order_opt = [];
            ################################
            #	재고 수량 확인
            ################################
            $jaego = new Jaego($user);

            for($i=0;$i<count($cart);$i++){

                $goods_no = $cart[$i]["goods_no"];
                $goods_sub = Lib::getValue($cart[$i],"goods_sub",0);
                if(empty($goods_sub) || !is_numeric($goods_sub)) $goods_sub = 0;
                //$goods_opt = Lib::getValue($cart[$i],"goods_opt","");
                $goods_opt = Lib::getValue($cart[$i],"opt_val","");

                $qty = $cart[$i]["qty"];
                $goods_price = $cart[$i]["price"] - $cart[$i]["dc_amt"] - $cart[$i]["coupon_amt"];
                $addopt_amt = Lib::getValue($cart[$i],"addopt_price",0);
                $order_addopt_amt = $addopt_amt * $qty;

                //옵션 가격
                $a_goods_opt = explode("|", $goods_opt);
                $opt_amt = isset($cart[$i]["opt_amt"]) ? $cart[$i]["opt_amt"] : 0;
                $order_opt_amt = $opt_amt * $qty;


                $sql = /** @lang text */
                    "
                    select a.goods_nm, a.head_desc, a.md_id, a.md_nm, b.com_nm, a.com_id, a.baesong_kind,
                        a.baesong_price,b.pay_fee/100 as com_rate, a.com_type, a.goods_type, a.is_unlimited,
                        a.point_cfg, a.point_yn, a.point_unit, a.price, a.point, a.wonga, '' as margin_rate
                    from goods a
                        left outer join company b on a.com_id  = b.com_id
                    where a.goods_no = :goods_no
                ";
                $goods = DB::selectOne($sql,array("goods_no" => $goods_no));
                // $goods->margin_rate	= round((1 - $goods->wonga / $goods->price)*100, 2);
                // 위탁상품인 경우,
                // 옵션가격이 있다면 수수료율에 맞춰 원가 재계산 > 정산 시 수수료율 보정
                if ($goods_type == "P" && ($opt_amt + $addopt_amt) > 0) {
                    $goods->wonga = ($goods_price + $opt_amt + $addopt_amt) * (1 - $goods->margin_rate / 100);
                }

                $good_qty = $jaego->Getqty($goods_no, $goods_sub, $goods_opt);
                if ($goods->is_unlimited == "Y") {
                    if ($good_qty == 0) {
                        throw new Exception("재고가 부족하여 수기판매 처리를 할 수 없습니다");
                    }
                } else {
                    if ($qty > $good_qty) {
                        throw new Exception("[$qty/$good_qty]  $goods_opt 재고가 부족하여 수기판매 처리를 할 수 없습니다");
                    }
                }

                $com_rat = 0;

                if(isset($cart[$i]["coupon_no"])){
                    $coupon_no = $cart[$i]["coupon_no"];
                    // 쿠폰정보 얻기
                    $sql = /** @lang text */
                        "
                        select com_rat from coupon_company
                        where coupon_no = :coupon_no and com_id = :com_id
                    ";
                    $coupon = DB::selectOne($sql,array("coupon_no" => $coupon_no,"com_id" => $goods->com_id));
                    if (!empty($coupon->com_rat)) {
                        $com_rat = $coupon->com_rat;
                    }
                }

                if($add_point_ratio > 0){
                    $add_group_point = ($goods_price * ($add_point_ratio / 100)) * $qty;
                } else {
                    $add_group_point = 0;
                }

                $ord_opt_add_point = 0;
                if ($point_flag) {
                    if ($goods->point_yn == "Y") {
                        if ($goods->point_cfg == "G") {
                            if ($goods->point_unit == "P") {
                                $ord_opt_add_point = round(($goods_price * $goods->point / 100) * $qty, 0) + $add_group_point;
                            } else {
                                //echo "($goods->point * $qty) + $add_group_point;";
                                $ord_opt_add_point = ($goods->point * $qty) + $add_group_point;
                            }
                        } else {
                            // 쇼핑몰 설정
                            //echo "round(($cfg_ratio / 100) * $qty, 0) + $add_group_point";
                            $ord_opt_add_point = round(($goods_price * $cfg_ratio / 100) * $qty, 0) + $add_group_point;
                        }
                    }
                }
                $add_point += $ord_opt_add_point;
                $ord_opt_point_amt = Lib::getValue($cart[$i],"point_amt",0);
                $ord_opt_coupon_amt = Lib::getValue($cart[$i],"coupon_amt",0);
                $ord_opt_dc_amt = Lib::getValue($cart[$i],"dc_amt",0);
                $ord_opt_dlv_amt = Lib::getValue($cart[$i],"dlv_amt",0);

                array_push($order_opt,[
                        'goods_no' => $goods_no,
                        'goods_sub' => $goods_sub,
                        'ord_no' => '',
                        'ord_seq' => '0',
                        'head_desc' => $goods->head_desc,
                        'goods_nm' => $goods->goods_nm,
                        'goods_opt' => $goods_opt,
                        'qty' => $qty,
                        'wonga' => $goods->wonga,
                        'price' => $cart[$i]["price"],
                        'dlv_amt' => $ord_opt_dlv_amt,
                        'pay_type' => $pay_type,
                        'point_amt' => $ord_opt_point_amt,
                        'coupon_amt' => $ord_opt_coupon_amt,
                        'dc_amt' => $ord_opt_dc_amt,
                        'opt_amt' => $order_opt_amt,
                        'addopt_amt' => $order_addopt_amt,
                        //'pay_fee' => $pay_fee,
                        'recv_amt' => $cart[$i]["recv_amt"],
                        'p_ord_opt_no' => $p_ord_opt_no,
                        'dlv_no' => $dlv_no,
                        'dlv_cd' => $dlv_cd,
                        'md_id' => $goods->md_id,
                        'md_nm' => $goods->md_nm,
                        'sale_place' => $sale_place,
                        'ord_state' => $ord_state,
                        'clm_state' => 0,
                        'com_id' => $goods->com_id,
                        'add_point' => $ord_opt_add_point,
                        'ord_kind' => $ord_kind,
                        'ord_type' => $ord_type,
                        'baesong_kind' => $goods->baesong_kind,
                        'dlv_start_date' => null,
                        'dlv_proc_date' => null,
                        'dlv_end_date' => null,
                        'dlv_cancel_date' => null,
                        'dlv_series_no' => null,
                        'ord_date' => DB::raw('now()'),
                        'dlv_comment' => null,
                        'admin_id' => $c_admin_id,
                        'coupon_no' => $coupon_no,
                        'com_coupon_ratio' => $com_rat
                ]);
                $ord_amt += $order_opt[$i]["price"] * $order_opt[$i]["qty"];
                $point_amt += $ord_opt_point_amt;
                $coupon_amt += $ord_opt_coupon_amt;
                $dc_amt += $ord_opt_dc_amt;
                $dlv_amt += $ord_opt_dlv_amt;
                $recv_amt += $order_opt[$i]["recv_amt"];
            }

            $order = new Order($user, true);
            $ord_no = $order->ord_no;

            DB::table('order_mst')->insert([
                'ord_no' => $ord_no,
                'ord_date' => DB::raw('now()'),
                'user_id' => $user_id,
                'user_nm' => $user_nm,
                'phone' => $phone,
                'mobile' => $mobile,
                'email' => $email,
                'ord_amt' => $ord_amt,
                'recv_amt' => $recv_amt + $dlv_amt,
                'point_amt' => $point_amt,
                'coupon_amt' => $coupon_amt,
                'dc_amt' => $dc_amt,
                'dlv_amt' => $dlv_amt,
                'add_dlv_fee' => $add_dlv_fee,
                //'pay_fee' => $pay_fee,
                'r_nm' => $r_nm,
                'r_zipcode' => $r_zip_code,
                'r_addr1' => $r_addr1,
                'r_addr2' => $r_addr2,
                'r_phone' => $r_phone,
                'r_mobile' => $r_mobile,
                'dlv_msg' => $dlv_msg,
                'ord_state' => $ord_state,
                'upd_date' => DB::raw('now()'),
                'dlv_end_date' => DB::raw('NULL'),
                'ord_type' => $ord_type,
                'ord_kind' => $ord_kind,
                'sale_place' => $sale_place,
                'chk_dlv_fee' => DB::raw('NULL'),
                'admin_id' => $c_admin_id,
                'r_jumin' => $r_jumin
            ]);

            $pay_stat = 0;
            $tno = '';
            $pay_amt = $recv_amt + $dlv_amt;

            if($p_ord_opt_no > 0){

                ##################################################
                #	부모 결제 정보 복사
                ##################################################
                $sql = /** @lang text */
                    "
                    select p.tno, p.pay_type, p.pay_stat, p.pay_amt
                    from order_opt o inner join payment p on o.ord_no = p.ord_no
                    where ord_opt_no = :ord_opt_no
                ";
                $row = DB::selectOne($sql,array("ord_opt_no" => $p_ord_opt_no));
                if (!empty($row->pay_type)) {
                    $ppay_type = $row->pay_type;
                    if ($row->tno != "" && (($pay_type & $ppay_type) == $pay_type || ($pay_type & $ppay_type) == $ppay_type)) {
                        $tno = $row->tno;
                        $pay_amt = $row->pay_amt;
                        $pay_stat = $row->pay_stat;
                    }
                }
            }

            $card_msg = "상품수기판매";
            DB::table('payment')->insert([
                "ord_no"		=> $ord_no,
                "pay_type" 		=> $pay_type,
                "pay_nm" 		=> $user_nm,
                "pay_amt" 		=> $pay_amt,
                "pay_stat" 		=> $pay_stat,
                "tno"           => $tno,
                "bank_inpnm" 	=> $bank_inpnm,
                "bank_code" 	=> $bank_code,
                "bank_number" 	=> $bank_number,
                "card_msg"      => $card_msg,
                "pay_ypoint"    => 0,
                "pay_point"     => $point_amt,
                "pay_baesong"   => $dlv_amt,
                "coupon_amt"    => $coupon_amt,
                "dc_amt"        => $dc_amt,
                //"pay_fee"       => $pay_fee,
                "ord_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
                "upd_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
            ]);


            for($i=0;$i<count($order_opt);$i++){

                $order_opt[$i]["ord_no"] = $ord_no;
                DB::table('order_opt')->insert($order_opt[$i]);
                $ord_opt_no = DB::getPdo()->lastInsertId();

                $goods_addopt = Lib::getValue($cart[$i],"goods_addopt","");
                $a_goods_addopts = explode("^", $goods_addopt);

                foreach($a_goods_addopts as $a_goods_addopt){
                    if(!empty($a_goods_addopt)){
                        list($addopt_value, $addopt_goods_no, $addopt_goods_sub, $a_addopt_amt, $addopt_idx) = explode("|", $a_goods_addopt);
                        $a_addopt_amt = $a_addopt_amt * $order_opt[$i]["qty"];
                        DB::table('order_opt_addopt')->insert([
                            "ord_opt_no" => $ord_opt_no,
                            "goods_no" => $order_opt[$i]["goods_no"],
                            "goods_sub" => $order_opt[$i]["goods_sub"],
                            "addopt_idx" => $addopt_idx,
                            "addopt" => $addopt_value,
                            "addopt_amt" => $a_addopt_amt,
                            "addopt_qty" => $order_opt[$i]["qty"],
                        ]);
                    }
                }

                #####################################################
                #	재고 처리
                #####################################################
                $order->SetOrdOptNo($ord_opt_no);
                $order->CompleteOrderSugi("", $ord_state);

                if ($ord_state == "10" || $ord_state == "30") {

                    // 상품배송완료인경우 상태 변경
                    if ($ord_state == "30") {
                        // 송장 정보 등록
                        $order->DlvEnd($dlv_cd, $dlv_no, "30");

                        // 주문상태 로그
                        $state_log = array(
                            "ord_no" => $ord_no,
                            "ord_state" => "30",
                            "comment" => "수기판매",
                            "admin_id" => $user["id"],
                            "admin_nm" => $user["name"]
                        );
                        $order->AddStateLog($state_log);

                        //	order_opt_wonga 정산건 반영
                        $order->DlvLog("30");

                        // 추가 옵션 온라인 및 보유 재고 처리
                        $sql_addopt = /** @lang text */
                            "
                            select addopt_idx, addopt_qty
                            from order_opt_addopt
                            where ord_opt_no = :ord_opt_no
                                and goods_no = :goods_no
                                and goods_sub = :goods_sub
                        ";
                        $rows = DB::select($sql_addopt,
                            array("ord_opt_no" => $ord_opt_no,"goods_no" => $order_opt[$i]["goods_no"],"goods_sub" => $order_opt[$i]["goods_sub"]));

                        foreach ($rows as $row) {
                            $addopt_qty = $row->addopt_qty;
                            DB::table('options')
                                ->where("no","=", $row->addopt_idx)
                                ->update([
                                "qty" => DB::raw("ifnull(qty, 0) - $addopt_qty"),
                                "wqty" => DB::raw("ifnull(wqty,0) - $addopt_qty"),
                            ]);
                        }
                    } else if ($ord_state == "10") { // 출고요청

                        if ($ord_kind != "30") { // 출고구분이 "보류"가 아닌경우 출고요청일 Update
                            //	주문상태 로그
                            $state_log = array(
                                "ord_no" => $ord_no,
                                "ord_state" => "10",
                                "comment" => "수기판매",
                                "admin_id" => $user["id"],
                                "admin_nm" => $user["name"]
                            );
                            $order->AddStateLog($state_log);
                        }

                        // 추가 옵션 온라인 및 보유 재고 처리
                        $sql_addopt = /** @lang text */
                            "
                            select addopt_idx, addopt_qty
                            from order_opt_addopt
                            where ord_opt_no = :ord_opt_no
                                and goods_no = :goods_no
                                and goods_sub = :goods_sub
                        ";
                        $rows = DB::select($sql_addopt,
                            array("ord_opt_no" => $ord_opt_no,"goods_no" => $order_opt[$i]["goods_no"],"goods_sub" => $order_opt[$i]["goods_sub"]));

                        foreach ($rows as $row) {
                            $addopt_qty = $row->addopt_qty;
                            DB::table('options')
                                ->where("no","=", $row->addopt_idx)
                                ->update([
                                    "qty" => DB::raw("ifnull(qty, 0) - $addopt_qty"),
                                    "wqty" => DB::raw("ifnull(wqty,0) - $addopt_qty"),
                                ]);
                        }
                    }
                }
            }


            #####################################################
            #	포인트 지급
            #####################################################
            if ($ord_state != "1") {
                if ($point_flag === true) {
                    $point = new Point($user,$user_id);
                    $point->SetOrdNo($ord_no);
                    $point->Order($add_point);
                }
            }
            DB::commit();
            return response()->json([
                    "code" => 200,
                    "ord_no" => $ord_no,
                    "msg" => ""
                ]);
        } catch (Exception $e) {
            DB::rollback();

            echo $e->getTraceAsString();

            return response()->json([
                "code" => 500,
                "msg" => sprintf("[%s %d] %s",$e->getFile(),$e->getLine(),$e->getMessage())
            ],500);
        }
    }

    private function CheckInt($val)
    {
        return is_numeric($val) ? $val : 0;
    }
}
