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

class ord20Controller extends Controller
{
    public function index(Request $req)
    {
        $p_ord_opt_no = $req->input("p_ord_opt_no","");

        $sql = /** @lang text */
            "
            select
                a.ord_no,a.ord_opt_no,a.goods_no,a.head_desc, a.goods_nm, replace(a.goods_opt, '^', ' : ') as opt_val
                , a.qty, a.price, a.point_amt, a.coupon_amt, a.recv_amt, a.dlv_amt
                , a.dc_amt, a.opt_amt, 0 as pay_fee
                , substr(IFNULL(a.head_desc, ''), 0, 12 ) as old_head_desc
                , substr(a.goods_nm, 0, 30) as old_goods_nm
                , a.dlv_amt + a.recv_amt + 0 as total_amt
                , a.qty * a.price as ord_amt
            from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
            where a.ord_opt_no = '$p_ord_opt_no'
        ";
        $p_ord_opt = DB::selectOne($sql);
        $ord_no = $p_ord_opt->ord_no;
        $ord_opt_no = $p_ord_opt->ord_opt_no;

        $p_ord_opt->addopts = [];
        if (!empty($p_ord_opt->ord_opt_no)) {
            // 추가 옵션 값 얻기
            $sql = /** @lang text */
                "
                select *
                from order_opt_addopt
                where ord_opt_no = '$p_ord_opt_no'
                order by no
            ";
            $p_ord_opt->addopts  = DB::select($sql);
        }

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

        $values = [
            'ord_no' => $ord_no,
            'ord_opt_no' => $ord_opt_no,
            'p_ord_opt_no' => $p_ord_opt_no,
            'p_ord_opt' => $p_ord_opt,
            'banks' => $banks,
            'pay_types' => SLib::getCodes("G_STAT_PAY_TYPE"),
            'ord_types' => SLib::getCodes('G_ORD_TYPE'),
            'sale_places' => SLib::getSalePlaces(),
            'dlv_cds' => SLib::getCodes('DELIVERY'),
        ];
        return view(Config::get('shop.head.view') . '/order/ord20', $values);
    }

    public function index2($cmd, $ord_no, $ord_opt_no, Request $req)
    {
        // 설정 값 얻기
        $conf = new Conf();
        $cfg_add_delivery_fee = $conf->getConfigValue("delivery", "add_delivery_fee");
        $cfg_base_delivery_fee = $conf->getConfigValue("delivery", "base_delivery_fee");
        $cfg_free_delivery_amt = $conf->getConfigValue("delivery", "free_delivery_amt");
        $cfg_wholesale_free_delivery_amt = $conf->getConfigValue("delivery", "wholesale_free_delivery_amt");

        $p_ord_opt_no = $req->input("p_ord_opt_no", "");
        $goods_no = $req->input("goods_no", "");
        $goods_sub = $req->input("goods_sub", 0);
        $ord_type = $req->input("ord_type", 0);
        $ord_kind = $req->input("ord_kind", "20");
        $ord_state = $req->input("ord_state", "");
        $sale_place = $req->input("sale_place", "C");

        $old_price = 0;
        $r_zip_code = "";
        $get_info_style = "";
        $coupon_no = "";
        $add_dlv_fee = 0;
        $goods_type = "";
        $com_dlv_policy = "S";

        // 회원 그룹 정보
        $group_no = "";
        $group_nm = "";
        $group_type = "";    // 적립금, 할인, 도매
        $group_ratio = 0;
        $group_dc_limit_amt = "";
        $group_dc_ratio = 0;
        $group_point_limit_amt = "";
        $group_point_ratio = 0;
        $is_wholesale = "N";
        $is_point_use = "";
        $wholesale_price = 0;
        $old_addopts = array();
        $group = null;

        $pays = (object)[];
        $old_ord_opt = (object)[];

        // 부모 주문 번호에 해당하는 정보
        if (empty($p_ord_opt_no)) exit("처리할수없는 요청입니다");

        #######################################################
        #	부모 주문 정보 : order_mst
        #######################################################
        $sql = /** @lang text */
            "
            select
                a.ord_kind, b.user_id, b.user_nm, b.phone, b.mobile, b.email ,
                b.r_nm, b.r_phone, b.r_mobile, b.r_zipcode, b.r_addr1, b.r_addr2, b.dlv_msg,
                a.p_ord_opt_no, a.ord_state, a.sale_place,
                c.goods_no, c.goods_sub, c.style_no, c.goods_nm, c.price as goods_price, c.goods_type,
                a.coupon_no, p.coupon_nm, b.add_dlv_fee, if(o.zip_code is null, 0, 1) as add_dlv_area,
                ifnull(a.dc_amt, 0) as dc_amt, b.r_jumin
            from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                left outer join order_add_dlv_area o on b.r_zipcode = o.zip_code
                left outer join coupon p on a.coupon_no = p.coupon_no
            where a.ord_opt_no = '$p_ord_opt_no'
        ";
        $ord_opt = DB::selectOne($sql);
        $user_id = "";

        if (isset($ord_opt->user_id)) {
            $user_id = $ord_opt->user_id;
            if ($user_id == "비회원") $get_info_style = "disabled";
            if ($ord_opt->add_dlv_area) $ord_opt->add_dlv_fee = $cfg_add_delivery_fee;
        }
        // 회원 그룹 정보
        if ($user_id != "비회원" && $user_id != "") {

            $sql = /** @lang text */
                "
                select a.group_no, b.group_nm,
                    b.dc_limit_amt, b.dc_ratio, b.point_limit_amt, b.point_ratio, b.is_wholesale, b.is_point_use
                from user_group_member a
                    inner join user_group b on a.group_no = b.group_no
                where a.user_id = '$user_id'
                    order by b.dc_ratio desc
                limit 0,1
            ";

            $group = DB::selectOne($sql);

            if (!empty($group->group_no)) {
                if ($group->is_wholesale == "Y") {
                    $group_type = "WS";
                    $group_ratio = $group->dc_ratio;

                    // 상품 도매가 얻기
                    $sql = /** @lang text */
                        "
                select price
                from goods_price
                where goods_no = '$ord_opt->goods_no'
                    and goods_sub = '$ord_opt->goods_sub'
                    and group_no = '$group->group_no'
                ";
                    $price = DB::selectOne($sql);
                    if (!empty($price->price)) $wholesale_price = $price;

                } else {
                    if ($group->dc_ratio > 0) {
                        $group_type = "DC";
                        $group_ratio = $group->dc_ratio;
                    }
                    if ($group->point_ratio > 0) {
                        $group_type = "PT";
                        $group_ratio = $group->point_ratio;
                    }
                }
            }

            #######################################################
            #	부모 결제 정보 : order_mst
            #######################################################
            $paySql = /** @lang text */
                "
                select
                    b.pay_type, b.bank_inpnm, b.bank_code, b.bank_number
                from order_opt a
                    inner join payment b on a.ord_no = b.ord_no
                where a.ord_opt_no = '$p_ord_opt_no'
            ";
            $pays = DB::selectOne($paySql);

            #######################################################
            #	이전 주문 상품 정보 : order_opt
            #######################################################
            $sql = /** @lang text */
                "
            select
                a.goods_no,a.head_desc, a.goods_nm, replace(a.goods_opt, '^', ' : ') as opt_val
                , a.qty, a.price, a.point_amt, a.coupon_amt, a.recv_amt, a.dlv_amt
                , a.dc_amt, a.opt_amt, 0 as pay_fee
                , substr(IFNULL(a.head_desc, ''), 0, 12 ) as old_head_desc
                , substr(a.goods_nm, 0, 30) as old_goods_nm
                , a.dlv_amt + a.recv_amt + 0 as total_amt
                , a.qty * a.price as ord_amt
            from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
            where a.ord_opt_no = '$p_ord_opt_no'
            ";
                //echo "<pre>$sql</pre>";
            $old_ord_opt = DB::selectOne($sql);

            if (!empty($old_ord_opt->goods_nm)) {
                // 추가 옵션 값 얻기
                $sql = /** @lang text */
                    "
                select *
                from order_opt_addopt
                where ord_opt_no = '$p_ord_opt_no'
                order by no
            ";

                $old_addopts = DB::select($sql);
            }
        }

        // 자식 주문 상품정보 얻기
        $goodsSql = /** @lang text */
            "
            select
            a.price, a.baesong_price, a.style_no, a.goods_nm, a.goods_type
            , ifnull(b.dlv_policy, 'S') as com_dlv_policy
            , ifnull(b.dlv_amt, 0) as com_dlv_amt
            , b.free_dlv_amt_limit as com_free_dlv_amt_limit
            , a.bae_yn
            , 1 as qty
            , 1 * a.price as ord_amt
            , a.price as old_price
            from goods a
            inner join company b on a.com_id = b.com_id
            where a.goods_no = '$goods_no' and a.goods_sub = '$goods_sub'
        ";

        $goods = DB::selectOne($goodsSql);

        // 배송비 계산
        if ($goods->com_dlv_policy == "C") {    // 업체 배송정책인 경우
            if ($goods->bae_yn == "Y") { // 배송비가 유료인 경우
                if ($goods->baesong_price == 0) {    // 기본 배송료
                    $goods->baesong_price = $goods->com_dlv_amt;
                } else {
                    if ($goods->baesong_price < $goods->com_dlv_amt) {  // 기본 배송료와 비교, 비싼 배송료 설정
                        $goods->baesong_price = $goods->com_dlv_amt;
                    }
                }
            }

            // 배송비 무료인 경우
            if ($goods->goods_type != "O") {
                if ($goods->price > $goods->com_free_dlv_amt_limit) {
                    $goods->baesong_price = 0;
                }
            }
        } else { // 쇼핑몰 정책
            if ($goods->bae_yn == "Y") { // 배송비가 유료인 경우
                if ($goods->baesong_price == 0) {    // 기본 배송료
                    $goods->baesong_price = $cfg_base_delivery_fee;
                } else {
                    if ($goods->baesong_price < $cfg_free_delivery_amt) {  // 기본 배송료와 비교, 비싼 배송료 설정
                        $goods->baesong_price = $cfg_base_delivery_fee;
                    }
                }
            }

            // 배송비 무료
            if ($goods->goods_type != "O") {
                if ($goods->price > $cfg_free_delivery_amt) {
                    $goods->baesong_price = 0;
                }
            }
        }

        // 주문액계
        $goods->recv_amt = $goods->ord_amt + $goods->baesong_price;

        if ($goods->price != $goods->old_price) {
            $goods->row_color = "#FF8E8E";
        } else {
            $goods->row_color = "#FFFFFF";
        }

        // 옵션 출력 처리
        $option_info = $this->__getOptionKind($goods_no, $goods_sub);
        $option_cnt = count($option_info);

        $a_combo = array();
        if ($goods_no != "" && $goods_sub != "") {
            $a_combo = $this->__getOptionCombo($goods_no, $goods_sub);
        }

        // 추가옵션 출력 처리
        $addopt_title = $this->__getAddoptTitle($goods_no, $goods_sub);
        $addopt = $this->_getAddopt($goods_no, $goods_sub);

        if ($sale_place == "C") $sale_place = "SPL_BONSA";
        // if($ord_state == "") $ord_state = 10;

        // 입금은행
        $bankSql = /** @lang text */
            "
            select concat(code_val,'_',ifnull(code_val2, '')) as 'name'
                , concat(code_val,' [',ifnull(code_val2, ''),']') as 'value'
                from code 
            where code_kind_cd ='BANK' 
                and code_id != 'K' 
                and use_yn = 'Y' 
            order by code_seq
        ";

        // 주문 상태
        $stateSql = /** @lang text */
            "
            select code_id id, code_val val
            from code
            where code_kind_cd = 'G_ORD_STATE' and code_id <> 'K' and code_id in ('1', '10','30')
            order by code_seq
        ";

        $pay_type_sql = /** @lang text */
            "
            select code_id as id, code_val as val
            from code
            where code_kind_cd = 'G_PAY_TYPE' and code_id <> 'K' and code_id in ('1','2','5','9','13','16','32','64')
            order by code_seq
        ";

        if ($sale_place == "C") {
            $salePlaceSql = /** @lang text */
                "select com_id as id, com_nm as val from company where com_type = '4' order by com_nm";
        } else {
            $salePlaceSql = /** @lang text */
                "
                select
                    com_id as id,
                    if(com_id = '',com_sale_type_nm, ifnull(com_nm, '')) as val
                from (
                    select code_id as com_sale_type,code_val as com_sale_type_nm,'' as com_id,'' as com_nm from code where code_kind_cd = 'G_COM_SALE_TYPE'
                    union all
                    select
                       '' as com_sale_type,b.code_val as com_sale_type_nm,c.com_id, c.com_nm
                    from company c inner join code b on b.code_kind_cd = 'G_COM_SALE_TYPE' and c.com_sale_type = b.code_id
                    where c.com_type = '4'
                ) a order by com_nm
            ";
        }
        $salePlaceSql = /** @lang text */
            "select com_id as id, com_nm as val from company where com_type = '4' order by com_nm";

        $values = [
            'ord_state' => $ord_state,
            'old_addopts' => $old_addopts,
            'wholesale_price' => $wholesale_price,
            'group' => $group,
            'goods' => $goods,
            'goods_no' => $goods_no,
            'goods_sub' => $goods_sub,
            'option_info' => $option_info,
            'sale_place' => $sale_place,
            'sale_placies' => DB::select($salePlaceSql),
            'ord_type' => $ord_type,
            'ord_types' => SLib::getCodes('G_ORD_TYPE'),
            'dlv_cds' => SLib::getCodes('DELIVERY'),
            'pay' => $pays,
            'banks' => DB::select($bankSql),
            'ord_states' => DB::select($stateSql),
            'pay_types' => DB::select($pay_type_sql),
            'ord_kind' => $ord_kind,
            'ord_kinds' => ['20' => '출고가능', '30' => '출고보류'],
            'g_free_dlv_fee_limit' => ($goods->com_dlv_policy == "C") ? $goods->com_free_dlv_amt_limit : $cfg_free_delivery_amt,
            'g_cfg_wholesale_free_delivery_amt' => $cfg_wholesale_free_delivery_amt,
            'g_dlv_fee' => ($goods->com_dlv_policy == "C") ? $goods->com_dlv_amt : $cfg_base_delivery_fee,
            'g_dlv_add_fee' => $cfg_add_delivery_fee,
            "group_type" => $group_type,
            "group_ratio" => $group_ratio,
            "cmd" => $cmd,
            "ord_no" => $ord_no,
            "ord_opt_no" => $ord_opt_no,
            "p_ord_opt_no" => $p_ord_opt_no,
            "ord" => $ord_opt,
            //이전 주문 내역
            "old_ord_opt" => $old_ord_opt,

            "goods_opts" => $a_combo,
            "option_cnt" => $option_cnt,
            "addopt_cnt" => count($addopt_title),
            "addopt_title" => $addopt_title,
            "addopts" => $addopt,
            "get_info_style" => $get_info_style
        ];

        return view(Config::get('shop.head.view') . '/order/ord20', $values);
    }

    private function __getOptionKind($goods_no, $goods_sub)
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

    /*
        Function: GetOptionKind
        옵션 구분 얻기

        Parameters:
            $goods_no 	- 상품코드1
            $goods_sub	- 상품코드2

        Returns:
            $a_option_kind - 옵션구분 $a_option_kind : array( "color", "size" )
    */

    private function __getOptionCombo($goods_no, $goods_sub, $goods_opt = "", $depth = 1, $is_utf = false)
    {
        // 부모상품코드 설정
        $p_goods_no = $goods_no;
        $p_goods_sub = $goods_sub;

        $option = array();

        $option_names = $this->__getOptionKind($goods_no, $goods_sub);
        $is_multi_option = (count($option_names) > 1) ? "Y" : "N";

        //print_r($option_names);

        // 옵션명
        $opt_name = "";
//		if( count($option_names) == 2 ){
//			$opt1_name = isset($option_names[0]) ? $option_names[0]:"";
//			$opt2_name = isset($option_names[1]) ? $option_names[1]:"";
//			$opt_name = $opt1_name."^".$opt2_name;
//		} elseif( count($option_names)== 1 ){
//			$opt_name = isset($option_names[0]) ? $option_names[0]:"";
//		}

        if (count($option_names) == 2) {
            $opt1_name = isset($option_names[0]) ? $option_names[0]->name : "";
            $opt2_name = isset($option_names[1]) ? $option_names[1]->name : "";
            $opt_name = $opt1_name . "^" . $opt2_name;
        } elseif (count($option_names) == 1) {
            $opt_name = isset($option_names[0]) ? $option_names[0] : "";
        }


        $where = ($goods_opt != "" && $depth > 1) ? " and a.goods_opt like '" . $goods_opt . "^%' " : "";
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
            $row->opt_price = ($depth == 1) ? $row->opt_price : 0;

            $tmp_txt = "";
            $tmp_val = "";

            if ($is_multi_option == "Y") {        // 멀티 옵션인 경우
                $tmp = explode("\^", $row->goods_opt);

                $tmp_txt = $tmp[$depth - 1];
                $tmp_val = $tmp[$depth - 1] . "|" . $goods_no . "|" . $goods_sub . "|" . $p_goods_no . "|" . $p_goods_sub . "|" . $tmp[$depth - 1] . "|" . $row->baesong_info . "|" . $row->bae_yn . "|" . $row->baesong_price;

                if ($depth == 2) {
                    $tmp_val = $tmp[$depth - 1];
                }

                if ($row->opt_price > 0) {
                    $tmp_txt .= " (+" . number_format($row->opt_price) . "원)";
                    $tmp_val .= "|$row->opt_price";
                }

            } else {        // 단일 옵션인 경우
                $tmp_txt = $row->goods_opt;
                $tmp_val = $row->goods_opt . "|" . $goods_no . "|" . $goods_sub . "|" . $p_goods_no . "|" . $p_goods_sub . "|" . $row->goods_opt . "|" . $row->baesong_info . "|" . $row->bae_yn . "|" . $row->baesong_price;
                if ($depth == 2) {
                    $tmp_val = $tmp[$depth - 1];
                }
                if ($row->opt_price > 0) {
                    $tmp_txt .= " (+" . number_format($row->opt_price) . "원)";
                    $tmp_val .= "|$row->opt_price";
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
        Function: GetOptionCombo
        옵션 콤보 출력 ( 1, 2 depth )
    */

    private function __getAddoptTitle($goods_no, $goods_sub)
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
            $name = $row->name;
            $required_yn = $row->required_yn;
            array_push($a_addopt_title, array("name" => $name, "seq" => $i, "required_yn" => $required_yn));
            $i++;
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

    private function _getAddopt($goods_no, $goods_sub)
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
                    "option" => $row2->option
                , "price" => $row2->price
                , "soldout_yn" => $row2->soldout_yn
                , "no" => $row2->no
                , "goods_no" => $goods_no
                , "goods_sub" => $goods_sub
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

    public function save2(Request $req)
    {
        // 설정 값 얻기
        $conf = new Conf();
        $cfg_ratio = $conf->getConfigValue("point", "ratio");

        $ord_opt_no = $req->input("ord_opt_no", "");
        $ord_no = $req->input("ord_no", "");
        $p_ord_opt_no = $req->input("p_ord_opt_no", "");
        $ord_type = $req->input("ord_type", "");
        $ord_kind = $req->input("ord_kind", "");
        $ord_state = $req->input("ord_state", "");
        $sale_place = $req->input("sale_place", "");
        $goods_no = $req->input("goods_no", "");
        $goods_sub = $req->input("goods_sub", "");
        $goods_opt = $req->input("goods_opt", "");
        $goods_price = $req->input("goods_price", "");    // 2013.06.12 : 상품판매가 > 적립금 지금의 기준가격
        $price = $req->input("price", "");
        $qty = $req->input("qty", "");
        $point_amt = $req->input("point_amt", "");
        $coupon_amt = $req->input("coupon_amt", "");
        $dc_amt = $req->input("dc_amt", "");
        $dlv_amt = $req->input("dlv_amt", "");
        $add_dlv_fee = $req->input("add_dlv_fee", "");
        $recv_amt = $req->input("recv_amt", "");
        $ord_amt = $req->input("ord_amt", "");
        $ord_state = $req->input("ord_state", "");
        $pay_type = $req->input("pay_type", "");
        $coupon_no = $req->input("coupon_no", "");
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
        $r_nm = $req->input("r_nm", "");
        $r_phone = $req->input("r_phone", "");
        $r_mobile = $req->input("r_mobile", "");

        $pay_fee = $req->input("pay_fee", "");
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


        $url = null;
        $partner_id = null;
        $partner_nm = null;
        $today = date("YmdHis");

        $price = $this->CheckInt($price);
        $qty = $this->CheckInt($qty);
        $point_amt = $this->CheckInt($point_amt);
        $coupon_amt = $this->CheckInt($coupon_amt);
        $dc_amt = $this->CheckInt($dc_amt);
        $pay_fee = $this->CheckInt($pay_fee);
        $dlv_amt = $this->CheckInt($dlv_amt);
        $add_dlv_fee = $this->CheckInt($add_dlv_fee);
        $recv_amt = $this->CheckInt($recv_amt);
        $ord_amt = $this->CheckInt($ord_amt);
        $cal_recv_amt = $recv_amt - $dlv_amt;        // recv_amt 에서 배송비 제거

        //옵션 가격
        $a_goods_opt = explode("|", $goods_opt);
        $opt_amt = isset($a_goods_opt[9]) ? CheckInt($a_goods_opt[9]) : 0;
        $order_opt_amt = $opt_amt * $qty;

        // 추가옵션 및 추가옵션 가격
        $goods_addopt = $req->input("goods_addopt", "");
        $addopt_amt = $this->CheckInt($req->input("addopt_price", "", 0));
        $order_addopt_amt = $addopt_amt * $qty;

        if ($user_id == "") $user_id = null;
        if ($user_nm == "") $user_nm = null;
        if ($phone == "") $phone = null;
        if ($mobile == "") $mobile = null;
        if ($email == "") $email = null;
        if ($r_nm == "") $r_nm = null;
        if ($r_phone == "") $r_phone = null;
        if ($r_mobile == "") $r_mobile = null;
        if ($r_addr1 == "") $r_addr1 = null;
        if ($r_addr2 == "") $r_addr2 = null;
        if ($dlv_msg == "") $dlv_msg = null;
        if ($r_jumin == "") $r_jumin = null;

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
            $order = new Order($user, true);
            $ord_no = $order->ord_no;

            ###############################################################################################
            #	수기판매 주문 등록
            ###############################################################################################
            // 상품정보 얻기
            $sql = /** @lang text */
                "
                select a.goods_nm, a.head_desc, a.md_id, a.md_nm, b.com_nm, a.com_id, a.baesong_kind,
                    a.baesong_price,b.pay_fee/100 com_rate, a.com_type, a.goods_type, a.is_unlimited,
                    a.point_cfg, a.point_yn, a.point_unit, a.price, a.point, a.wonga, '' as margin_rate
                from goods a
                    left outer join company b on a.com_id  = b.com_id
                where a.goods_no = '$goods_no'
                    and a.goods_sub = '$goods_sub'
            ";

            $goods = DB::selectOne($sql);

            // $goods->margin_rate	= round((1 - $goods->wonga / $goods->price)*100, 2);

            // 위탁상품인 경우,
            // 옵션가격이 있다면 수수료율에 맞춰 원가 재계산 > 정산 시 수수료율 보정
            if ($goods_type == "P" && ($opt_amt + $addopt_amt) > 0) {
                $goods->wonga = ($goods_price + $opt_amt + $addopt_amt) * (1 - $goods->margin_rate / 100);
            }

            ################################
            #	재고 수량 확인
            ################################
            $jaego = new Jaego($user);
            $good_qty = $jaego->Getqty($goods_no, $goods_sub, $a_goods_opt[0]);
            if ($goods->is_unlimited == "Y") {
                if ($good_qty == 0) {
                    throw new Exception("재고가 부족하여 수기판매 처리를 할 수 없습니다");
                }
            } else {
                if ($qty > $good_qty) {
                    throw new Exception("재고가 부족하여 수기판매 처리를 할 수 없습니다");
                }
            }

            // 쿠폰정보 얻기
            $sql = /** @lang text */
                "
                select com_rat from coupon_company
                where coupon_no = '$coupon_no' and com_id = '$goods->com_id'
            ";
            $coupon = DB::selectOne($sql);
            $com_rat = 0;

            if (!empty($coupon->com_rat)) {
                $com_rat = $row->com_rat;
            }

            ################################
            # 포인트 지급
            ################################
            $point_flag = false;
            $add_point = 0;
            $add_group_point = 0;

            if ($give_point == "Y") {
                // 회원 여부 확인
                $sql = /** @lang text */
                    " select count(*) as cnt from member where user_id = '$user_id' ";
                $row = DB::selectOne($sql);
                $id_cnt = $row->cnt;

                if ($id_cnt == 1) {
                    // 적립금 지급
                    $point_flag = true;

                    if ($group_apply == "Y") {
                        // 회원 그룹 추가 포인트
                        $sql = /** @lang text */
                            "
                            select a.group_no, b.point_ratio
                            from user_group_member a
                                inner join user_group b on a.group_no = b.group_no
                            where a.user_id = '$user_id'
                                order by b.point_ratio desc
                            limit 0,1
                        ";
                        $group = DB::selectOne($sql);
                        if (!empty($group->point_ratio)) {
                            $add_point_ratio = $group->point_ratio;
                            $add_group_point = ($goods_price * ($add_point_ratio / 100)) * $qty;
                        }
                    }
                }
            }

            if ($point_flag) {
                if ($goods->point_yn == "Y") {
                    if ($goods->point_cfg == "G") {
                        if ($goods->point_unit == "P") {
                            $add_point = round(($goods_price * $goods->point / 100) * $qty, 0) + $add_group_point;
                        } else {
                            $add_point = ($goods->point * $qty) + $add_group_point;
                        }
                    } else {
                        // 쇼핑몰 설정
                        $add_point = round(($cfg_ratio / 100) * $qty, 0) + $add_group_point;
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

            #########################################################
            #	Todo :
            #		수기판매할 경우의 배송비 처리 확인 필요함!!!
            #		수기화면 작업시에 order.OrderChange 적용 고려
            #	ins ORDER_MST
            #########################################################

            DB::table('order_mst')->insert([
                'ord_no' => $ord_no,
                'ord_date' => DB::raw('now()'),
                'user_id' => $user_id,
                'user_nm' => $user_nm,
                'phone' => $phone,
                'mobile' => $mobile,
                'email' => $email,
                'ord_amt' => $ord_amt,
                'recv_amt' => $recv_amt,
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

            // ins ORDER_OPT
            DB::table('order_opt')->insert([
                'goods_no' => $goods_no,
                'goods_sub' => $goods_sub,
                'ord_no' => $ord_no,
                'ord_seq' => '0',
                'head_desc' => $goods->head_desc,
                'goods_nm' => $goods->goods_nm,
                'goods_opt' => $a_goods_opt[0],
                'qty' => $qty,
                'wonga' => $goods->wonga,
                'price' => $price,
                'dlv_amt' => $dlv_amt,
                'pay_type' => $pay_type,
                'point_amt' => $point_amt,
                'coupon_amt' => $coupon_amt,
                'dc_amt' => $dc_amt,
                'opt_amt' => $order_opt_amt,
                'addopt_amt' => $order_addopt_amt,
                //'pay_fee' => $pay_fee,
                'recv_amt' => $cal_recv_amt,
                'p_ord_opt_no' => $p_ord_opt_no,
                'dlv_no' => $dlv_no,
                'dlv_cd' => $dlv_cd,
                'md_id' => $goods->md_id,
                'md_nm' => $goods->md_nm,
                'sale_place' => $sale_place,
                'ord_state' => $ord_state,
                'clm_state' => 0,
                'com_id' => $goods->com_id,
                'add_point' => $add_point,
                'ord_kind' => $ord_kind,
                'ord_type' => $ord_type,
                'baesong_kind' => $goods->baesong_kind,
                'dlv_start_date' => null,
                'dlv_proc_date' => null,
                'dlv_end_date' => null,
                'dlv_cancel_date' => null,
                'dlv_series_no' => null,
                'ord_date' => $today,
                'dlv_comment' => null,
                'admin_id' => $c_admin_id,
                'coupon_no' => $coupon_no,
                'com_coupon_ratio' => $com_rat
            ]);
            $ord_opt_no = DB::getPdo()->lastInsertId();

            // ins ORDER_OPT_ADDOPT
            $a_goods_addopt = explode("^", $goods_addopt);
            for ($i = 0; $i < count($a_goods_addopt); $i++) {
                if ($a_goods_addopt[0] != "") {
                    list($addopt_value, $addopt_goods_no, $addopt_goods_sub, $a_addopt_amt, $addopt_idx) = explode("|", $a_goods_addopt[$i]);

                    $a_addopt_amt = $a_addopt_amt * $qty;

                    $sql = /** @lang text */
                        "
                        insert into order_opt_addopt (
                            ord_opt_no, goods_no, goods_sub, addopt_idx, addopt, addopt_amt, addopt_qty
                        ) values (
                            '$ord_opt_no', '$addopt_goods_no', '$addopt_goods_sub', '$addopt_idx', '$addopt_value', '$a_addopt_amt', $qty
                        )
                    ";
                    DB::insert($sql);
                }
            }

            // ins PAYMENT
            $card_msg = "상품수기판매";
            $payment = [
                "ord_no"		=> $ord_no,
                "pay_type" 		=> $pay_type,
                "pay_nm" 		=> $user_nm,
                "pay_amt" 		=> $ord_amt,
                "pay_stat" 		=> 0,
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
            ];
            DB::table('payment')->insert($payment);


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
                        where ord_opt_no = '$ord_opt_no'
                            and goods_no = '$goods_no'
                            and goods_sub = '$goods_sub'
                    ";

                    $rows = DB::select($sql_addopt);
                    foreach ($rows as $row) {
                        $addopt_idx = $row->addopt_idx;
                        $addopt_qty = $row->addopt_qty;

                        $sql_addopt_upd = /** @lang text */
                            "
                            update options set
                                qty = ifnull(qty, 0) - $addopt_qty
                                , wqty = ifnull(wqty,0) - $addopt_qty
                            where no = '$addopt_idx'
                        ";
                        DB::update($sql_addopt_upd);
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
                        where ord_opt_no = '$ord_opt_no'
                            and goods_no = '$goods_no'
                            and goods_sub = '$goods_sub'
                    ";
                    $rows = DB::select($sql_addopt);
                    foreach ($rows as $row) {
                        $addopt_idx = $row->addopt_idx;
                        $addopt_qty = $row->addopt_qty;

                        $sql_addopt_upd = /** @lang text */
                            "
                            update options set
                                qty = ifnull(qty, 0) - $addopt_qty
                            where no = '$addopt_idx'
                        ";

                        DB::update($sql_addopt_upd);
                    }


                }

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
                    $ptno = $row->tno;
                    $ppay_type = $row->pay_type;
                    $ppay_stat = $row->pay_stat;
                    $ppay_amt = $row->pay_amt;

                    if ($ptno != "" && (($pay_type & $ppay_type) == $pay_type || ($pay_type & $ppay_type) == $ppay_type)) {

                        DB::table('payment')
                            ->where('ord_no','=',$ord_no)
                            ->update([
                                'tno' => $ptno,
                                'pay_stat' => $ppay_stat,
                                'pay_amt' => $ppay_amt
                            ]);
                    }
                }
            }


            #####################################################
            #	포인트 지급
            #####################################################
            if ($ord_state != "1") {
                if ($point_flag) {
                    $point = new Point($user);
                    $point->SetOrdNo($ord_no);
                    $point->Order($add_point);
                }
            }

            DB::commit();
            return response()->json(null, 204);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function CheckInt($val)
    {
        return is_numeric($val) ? $val : 0;
    }
}
