<?php

namespace App\Http\Controllers\store\stock;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Order;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class stk03Controller extends Controller
{
    public function index()
	{
		$values = [
            'sdate'         => now()->sub(3, 'month')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'style_no'      => '',
            'ord_states' => SLib::getordStates(), // 주문상태
            'clm_states' => SLib::getCodes('G_CLM_STATE'), // 클레임상태
            'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
            'ord_types' => SLib::getCodes('G_ord_TYPE'), // 주문구분
            'ord_kinds' => SLib::getCodes('G_ord_KIND'), // 출고구분
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
			'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/stock/stk03', $values);
	}

    public function search(Request $request)
    {
        $sdate          = $request->input('sdate', now()->sub(3, 'month')->format('Ymd'));
        $edate          = $request->input('edate', date('Ymd'));
        $nud            = $request->input('nud', ''); // 주문일자 검색여부
        $ord_no         = $request->input('ord_no', '');
        $store_no       = $request->input('store_no', '');
        $ord_state      = $request->input('ord_state', '');
        $pay_state      = $request->input('pay_stat', '');
        $clm_state      = $request->input('clm_state', '');
        $ord_type       = $request->input('ord_type', '');
        $ord_kind       = $request->input('ord_kind', '');
        $ord_info_key   = $request->input('ord_info_key', 'om.user_nm');
        $ord_info_value = $request->input('ord_info_value', '');
        $stat_pay_type  = $request->input('stat_pay_type', '');
        $not_complex    = $request->input('not_complex', 'N'); // 복합결제 제외
        $prd_cd         = $request->input('prd_cd', '');
        $style_no       = $request->input('style_no', '');
        $goods_no       = $request->input('goods_no', '');
        $goods_nm       = $request->input('goods_nm', '');
        $goods_stat     = $request->input('goods_stat', []);
        $item           = $request->input('item', '');
        $brand_cd       = $request->input('brand_cd', '');
        $goods_nm_eng   = $request->input('goods_nm_eng', '');
        $com_cd         = $request->input('com_cd', '');
        $com_nm         = $request->input('com_nm', '');
        $limit          = $request->input('limit', 100);
        $ord            = $request->input('ord', 'desc');
        $ord_field      = $request->input('ord_field', 'o.ord_date');
        $page           = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;

        // $mobile_yn      = $request->input('mobile_yn', '');  // 모바일 주문 여부
        // $app_yn         = $request->input('app_yn', '');    // 앱 주문 여부
        // $receipt        = $request->input('receipt', 'N');  // 현금영수증 : N(미신청), R(신청), Y(발행)
        // $dlv_type       = $request->input('dlv_type', '');  // 배송방식: D(택배), T(택배(당일배송)), G(직접수령)
        // $pay_fee        = $request->input('pay_fee', '');  // 결제수수료 주문
        // $fintech        = $request->input('fintech', '');  // 핀테크

        $where = "";
        $where .= " and o.ord_kind != '10' "; // 정상판매건이 아닌 경우에만 출력
        
        // 날짜검색 미사용 여부
        $is_not_use_date = false;
        // if (
        //     $ord_no != '' 
        //     || ($ord_info_key == 'om.user_id' && $ord_info_value != '')
        //     || ($ord_info_key == 'om.user_nm' && $ord_info_value != '')
        //     || ($ord_info_key == 'om.r_nm' && strlen($ord_info_value) >= 4)
        //     || ($ord_info_key == 'om.mobile' && strlen($ord_info_value) >= 8)
        //     || ($ord_info_key == 'om.phone' && strlen($ord_info_value) >= 8)
        //     || ($ord_info_key == 'om.r_mobile' && strlen($ord_info_value) >= 8)
        // ) {
        //     $is_not_use_date = true;
        // }
        if ($is_not_use_date == false && $nud == 'on') {
            $where .= " and o.ord_date >= '$sdate 00:00:00' ";
            $where .= " and o.ord_date <= '$edate 23:59:59' ";
        }
        if ($ord_no != '') $where .= " and o.ord_no = '$ord_no' ";
        if ($store_no != '') $where .= " and o.store_cd = '$store_no' ";
        if ($ord_state != '') $where .= " and o.ord_state = '$ord_state' ";
        if ($pay_state != '') $where .= " and pay.pay_stat = '$pay_state' ";
        // 클레임상태
        if ($clm_state == '90') {
            $where .= " and o.clm_state = '0' ";
        } else {
            if ($clm_state != '') {
                $where .= " and o.clm_state = '$clm_state' ";
            }
        }
        if ($ord_kind != '') $where .= " and o.ord_kind = '$ord_kind' ";
        if ($ord_type != '') $where .= " and o.ord_type = '$ord_type' ";
        // 주문정보검색
        if ($ord_info_value != '') {
            if (in_array($ord_info_key, ['om.mobile', 'om.phone', 'om.r_mobile', 'om.r_phone'])) {
                $val = $this->__replaceTel($ord_info_value);
                if (in_array($ord_info_key, ['om.mobile', 'om.phone', 'om.r_mobile'])) {
                    $where .= " and $ord_info_key = '$val' ";
                } else {
                    $where .= " and $ord_info_key like '$val%' ";
                }
            } else {
                if ($ord_info_key == 'memo') {
                    $where .= " and (m.state like '%$ord_info_value%' or m.memo like '%$ord_info_value%') ";
                } else if ($ord_info_key == 'o.dlv_end_date') {
                    $where .= " and date_format($ord_info_keyl, '%Y%m%d') = $ord_info_value ";
                } else if (in_array($ord_info_key, ['om.user_nm', 'om.user_id', 'om.r_nm', 'om.bank_inpnm'])) {
                    $where .= " and $ord_info_key = '$ord_info_value' ";
                } else {
                    $where .= " and $ord_info_key like '$ord_info_value%' ";
                }
            }
        }
        // 결제방법
        if ($stat_pay_type != '') {
            if ($not_complex == 'Y') {
                $where .= " and o.pay_type = '$stat_pay_type' ";
            } else {
                $where .= " and ((o.pay_type & $stat_pay_type) = $stat_pay_type) ";
            }
        }
        // 상품코드
        if ($prd_cd != '') {
            $prd_cd = preg_replace("/\s/", ",", $prd_cd);
            $prd_cd = preg_replace("/\t/", ",", $prd_cd);
            $prd_cd = preg_replace("/\n/", ",", $prd_cd);
            $prd_cd = preg_replace("/,,/", ",", $prd_cd);
            $prd_cds = explode(',', $prd_cd);
            if (count($prd_cds) > 1) {
                if (count($prd_cds) > 500) array_splice($prd_cds, 500);
                $in_prd_cds = join(',', $prd_cds);
                $where .= " and o.prd_cd in ($in_prd_cds) ";
            } else {
                $where .= " and o.prd_cd = '$prd_cd' ";
            }
        }
        if ($style_no != '') $where .= " and g.style_no like '$style_no%' ";
        if ($goods_no != '') {
            $goods_no = preg_replace("/\s/", ",", $goods_no);
            $goods_no = preg_replace("/\t/", ",", $goods_no);
            $goods_no = preg_replace("/\n/", ",", $goods_no);
            $goods_no = preg_replace("/,,/", ",", $goods_no);
            $goods_nos = explode(',', $goods_no);
            if (count($goods_nos) > 1) {
                if (count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(',', $goods_nos);
                $where .= " and g.goods_no in ($in_goods_nos) ";
            } else {
                $where .= " and g.goods_no = '$goods_no' ";
            }
        }
        if ($goods_nm != '') $where .= " and g.goods_nm like '%$goods_nm%' ";
        if (count($goods_stat) > 0) {
            if (count($goods_stat) == 1 && $goods_stat[0] != '') {
                $where .= " and g.sale_stat_cl = '" . $goods_stat[0] . "' ";
            } else {
                $in_goods_stats = join(',', $goods_stat);
                $where .= " and g.sale_stat_cl in ($in_goods_stats) ";
            }
        }
        if ($item != '') $where .= " and g.opt_kind_cd = '$item' ";
        if ($brand_cd != '') $where .= " and g.brand = '$brand_cd' ";
        if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%$goods_nm_eng%' ";
        if ($com_cd != '') $where .= " and g.com_id = '$com_cd' ";
        else if ($com_nm != '') $where .= " and g.com_nm = '$com_nm' ";

        // ordreby
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sql = "
            select
                a.ord_no,
                a.ord_opt_no,
                ord_state.code_val as ord_state,
                clm_state.code_val as clm_state,
                pay_stat.code_val as pay_stat,
                a.prd_cd,
                a.goods_no,
                a.style_no,
                a.goods_type,
                ifnull(gt.code_val, 'N/A') as goods_type_nm,
                a.goods_nm,
                replace(a.goods_opt, '^', ' : ') as opt_val,
                a.qty,
                concat(a.user_nm, ' (', a.user_id, ')') as user_nm,
                a.r_nm,
                a.sale_place,
                a.goods_price,
                a.price,
                a.dlv_amt,
                a.sales_com_fee,
                pay_type.code_val as pay_type,
                ord_type.code_val as ord_type,
                ord_kind.code_val as ord_kind,
                a.store_cd,
                baesong_kind.code_val as baesong_kind,
                a.dlv_no,
                dlv_cd.code_val as dlv_cm,
                a.state,
                a.memo,
                a.ord_date,
                a.pay_date,
                a.dlv_end_date,
                a.last_up_date,
                if(a.ord_state <= 10 and a.clm_state = 0 and ord_opt_cnt = 0, 'Y', 'N') as ord_del_yn,
                '2' as depth
            from (
                select
                    om.ord_no,
                    o.ord_opt_no,
                    o.ord_state,
                    o.clm_state,
                    pay.pay_stat,
                    p.prd_cd,
                    g.goods_no,
                    g.style_no,
                    g.goods_type,
                    g.goods_nm,
                    p.goods_opt,
                    o.qty,
                    om.user_id,
                    om.user_nm,
                    om.r_nm,
                    om.sale_place,
                    g.price as goods_price,
                    o.price,
                    o.dlv_amt,
                    o.sales_com_fee,
                    pay.pay_type,
                    o.ord_type,
                    o.ord_kind,
                    o.store_cd,
                    g.baesong_kind as dlv_baesong_kind,
                    o.dlv_no,
                    o.dlv_cd,
                    m.state,
                    m.memo,
                    o.ord_date,
                    pay.pay_date,
                    o.dlv_end_date,
                    c.last_up_date,
                    (select count(*) from order_opt where ord_no = o.ord_no and ord_opt_no != o.ord_opt_no and (ord_state > 10 or clm_state > 0)) as ord_opt_cnt
                from order_opt o
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join product_code p on o.prd_cd = p.prd_cd
                    inner join goods g on p.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                where 1=1 $where
                $orderby
                $limit
            ) a
                left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
                left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
                left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
                left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
                left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
                left outer join code baesong_kind on (a.dlv_baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
                left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
                left outer join code dlv_cd on (a.dlv_cd = dlv_cd.code_id and dlv_cd.code_kind_cd = 'DELIVERY')
                left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
        ";
        $result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                from order_opt o
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join product_code p on o.prd_cd = p.prd_cd
                    inner join goods g on p.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                where 1=1 $where
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ],
            "body" => $result,
        ]);
    }

    public function create()
    {
        $sql = "
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

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'G_PAY_TYPE'
                and code_id <> 'K'
                and code_id in ('1','2','5','9','13','16','32','64')
            order by code_seq
        ";
        $pay_types = DB::select($sql);

        $conf = new Conf();

        $values = [
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            'pay_types'     => $pay_types,
            'banks'         => $banks,
            'dlv_cds'       => SLib::getCodes('DELIVERY'),
            'dlv_fee'       => [
                'base_dlv_fee'  => $conf->getConfigValue('delivery', 'base_delivery_fee'), 
                'add_dlv_fee'   => $conf->getConfigValue('delivery', 'add_delivery_fee'), 
                'free_dlv_amt'  => $conf->getConfigValue('delivery', 'free_delivery_amt'),
            ],
        ];
        return view(Config::get('shop.store.view') . '/stock/stk03_show', $values);
    }

    // 수기판매 등록
    public function save(Request $req)
    {
        $conf = new Conf();
        $cfg_ratio = $conf->getConfigValue("point", "ratio");

        // $ord_no = $req->input("ord_no", "");
        $p_ord_opt_no = $req->input("p_ord_opt_no", "");
        $ord_type = $req->input("ord_type", ""); // 출고형태
        $ord_kind = $req->input("ord_kind", ""); // 출고구분
        $ord_state = $req->input("ord_state", ""); // 주문상태
        $store_cd = $req->input("store_no", ""); // 주문매장
        $store_nm = "본사"; // 주문매장명
        if ($store_cd != '') {
            $row = DB::table('store')->select('store_nm')->where('store_cd', '=', $store_cd)->first();
            if ($row != null) $store_nm = $row->store_nm;
        }

        $cart = $req->input("cart"); // 상품정보

        $ord_amt = 0;
        $recv_amt = 0;
        $point_amt = 0;
        $coupon_amt = 0;
        $dc_amt = 0;
        $pay_fee = 0;
        $dlv_amt = 0;
        $base_dlv_amt = $conf->getConfigValue('delivery', 'base_delivery_fee'); // 기본배송비
        $free_dlv_amt = $conf->getConfigValue('delivery', 'free_delivery_amt'); // 배송비무료 금액
        $dlv_apply = $req->input("dlv_apply", ""); // 배송비적용 여부
        $add_dlv_fee = $req->input("add_dlv_fee", 0); // 추가배송비
        if($add_dlv_fee == '') $add_dlv_fee = 0;

        $coupon_no = $req->input("coupon_no", "");
        $pay_type = $req->input("pay_type", ""); // 결제방법
        $bank_inpnm = $req->input("bank_inpnm", ""); // 입금자
        $bank_code = $req->input("bank_code", ""); // 입금은행
        $bank_number = ""; // 계좌번호
        if ($bank_code != "") {
            list($bank_code, $bank_number) = explode("_", $bank_code);
        }

        $user_id = $req->input("user_id", ""); // 주문자 ID
        $user_nm = $req->input("user_nm", ""); // 주문자 이름
        $phone = $req->input("phone", ""); // 주문자 전화
        $mobile = $req->input("mobile", ""); // 주문자 휴대전화
        $email = DB::table("member")->select("email")->where("user_id", "=", $user_id)->first(); // 주문자 이메일
        if($email != null) $email = $email->email;
        else $email = "";

        $r_nm = $req->input("r_user_nm", ""); // 수령자 이름
        $r_phone = $req->input("r_phone", ""); // 수령자 전화
        $r_mobile = $req->input("r_mobile", ""); // 수령자 휴대전화

        $r_zip_code = $req->input("r_zip_code", ""); // 수령 우편번호
        $r_addr1 = $req->input("r_addr1", ""); // 수령 주소1
        $r_addr2 = $req->input("r_addr2", ""); // 수령 주소2
        $dlv_msg = $req->input("dlv_msg", ""); // 출고메시지

        $give_point = $req->input("give_point", ""); // 적립금지급 여부
        $group_apply = $req->input("group_apply", "");
        $dlv_cd = $req->input("dlv_cd", ""); // 출고완료시 택배업체
        $dlv_no = $req->input("dlv_no", ""); // 출고완료시 송장번호

        $sale_kind = "01"; // 판매유형 (01: 일반판매)
        $pr_code = "JS"; // 행사구분 (JS: 정상)

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
                $sql = " select count(*) as cnt from member where user_id = :user_id ";
                $row = DB::selectOne($sql, ["user_id" => $user_id]);

                if ($row->cnt == 1) {
                    // 적립금 지급
                    $point_flag = true;
                    if ($group_apply == "Y") {
                        // 회원 그룹 추가 포인트
                        $sql = "
                            select a.group_no, b.point_ratio
                            from user_group_member a
                                inner join user_group b on a.group_no = b.group_no
                            where a.user_id = :user_id
                                order by b.point_ratio desc
                            limit 0,1
                        ";
                        $group = DB::selectOne($sql, ["user_id" => $user_id]);
                        if (!empty($group->point_ratio)) {
                            $add_point_ratio = $group->point_ratio;
                        }
                    }
                }
            }

            // 배송비 계산
            // if ($add_dlv_fee > 0) {
            //     $dlv_amt = $dlv_amt - $add_dlv_fee; //
            // }

            ################################
            #	재고 수량 확인
            ################################
            
            $order_opt = [];

            for ($i = 0; $i < count($cart); $i++) {
                $goods_no = $cart[$i]['goods_no'] ?? '';
                $goods_sub = $cart[$i]['goods_sub'] ?? '';
                if(empty($goods_sub) || !is_numeric($goods_sub)) $goods_sub = 0;
                $goods_type = $cart[$i]['goods_type_cd'] ?? '';
                $goods_price = ($cart[$i]['price'] ?? 0) - ($cart[$i][$dc_amt] ?? 0) - ($cart[$i]['coupon_amt'] ?? 0);
                $point = $cart[$i]['point'] ?? '';
                $com_type = $cart[$i]['com_type'] ?? '';
                $prd_cd = $cart[$i]['prd_cd'] ?? '';
                $goods_opt = $cart[$i]['goods_opt'] ?? '';
                $qty = $cart[$i]['qty'] ?? ''; // 판매수량
                $addopt_amt = $cart[$i]['addopt_amt'] ?? 0;
                $order_addopt_amt = $addopt_amt * $qty;

                // 옵션가격
                $a_goods_opt = explode("|", $goods_opt);
                $opt_amt = $cart[$i]["opt_amt"] ?? 0;
                $order_opt_amt = $opt_amt * $qty;

                $sql = "
                    select 
                        a.goods_nm, a.head_desc, a.md_id, a.md_nm, b.com_nm, a.com_id, a.baesong_kind,
                        a.baesong_price, b.pay_fee/100 as com_rate, a.com_type, a.goods_type, a.is_unlimited,
                        a.point_cfg, a.point_yn, a.point_unit, a.price, a.point, a.wonga, '' as margin_rate
                    from goods a
                        left outer join company b on a.com_id  = b.com_id
                    where a.goods_no = :goods_no
                ";
                $goods = DB::selectOne($sql, ["goods_no" => $goods_no]);

                // 위탁상품인 경우, 옵션가격이 있다면 수수료율에 맞춰 원가 재계산 > 정산 시 수수료율 보정
                if ($goods_type == "P" && ($opt_amt + $addopt_amt) > 0) {
                    $goods->wonga = ($goods_price + $opt_amt + $addopt_amt) * (1 - $goods->margin_rate / 100);
                }

                $product_stock = 0;
                if ($store_cd != '') {
                    $row = DB::table('product_stock_store')
                        ->select('wqty')->where('prd_cd', '=', $prd_cd)->where('store_cd', '=', $store_cd)
                        ->first();
                } else {
                    $sql = "
                        select wqty
                        from product_stock_storage
                        where prd_cd = '$prd_cd' and storage_cd = (select storage_cd from storage where default_yn = 'Y')
                    ";
                    $row = DB::selectOne($sql);
                }
                if ($row != null) $product_stock = $row->wqty;

                if ($goods->is_unlimited == "Y") {
                    if ($product_stock < 1) {
                        throw new Exception("재고가 부족하여 수기판매 처리를 할 수 없습니다.");
                    }
                } else {
                    if ($qty > $product_stock) {
                        throw new Exception("[상품코드 : $prd_cd] 재고가 부족하여 수기판매 처리를 할 수 없습니다.");
                    }
                }

                $com_rat = 0;

                if (isset($cart[$i]["coupon_no"])) {
                    $coupon_no = $cart[$i]["coupon_no"];
                    // 쿠폰정보 얻기
                    $sql = "
                        select com_rat 
                        from coupon_company
                        where coupon_no = :coupon_no and com_id = :com_id
                    ";
                    $coupon = DB::selectOne($sql, ["coupon_no" => $coupon_no, "com_id" => $goods->com_id]);
                    if (!empty($coupon->com_rat)) {
                        $com_rat = $coupon->com_rat;
                    }
                }
                
                $add_group_point = 0;
                if ($add_point_ratio > 0) {
                    $add_group_point = ($goods_price * ($add_point_ratio / 100)) * $qty;
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
                $ord_opt_point_amt = Lib::getValue($cart[$i], "point_amt", 0);
                $ord_opt_coupon_amt = Lib::getValue($cart[$i], "coupon_amt", 0);
                $ord_opt_dc_amt = Lib::getValue($cart[$i], "dc_amt", 0);
                $ord_opt_dlv_amt = Lib::getValue($cart[$i], "dlv_amt", 0);

                $a_ord_amt = $cart[$i]["ord_amt"] ?? 0;
                $a_recv_amt = $cart[$i]["recv_amt"] ?? ($a_ord_amt - $ord_opt_point_amt - $ord_opt_coupon_amt - $ord_opt_dc_amt);
                if ($dlv_apply == 'N' || $a_ord_amt >= $free_dlv_amt) {
                    $ord_opt_dlv_amt = 0;
                }

                array_push($order_opt, [
                        'goods_no' => $goods_no,
                        'goods_sub' => $goods_sub,
                        'ord_no' => '',
                        'ord_seq' => '0',
                        'head_desc' => $goods->head_desc,
                        'goods_nm' => $goods->goods_nm,
                        'goods_opt' => $goods_opt,
                        'qty' => $qty,
                        'wonga' => $goods->wonga,
                        'price' => $cart[$i]["price"] ?? 0,
                        'dlv_amt' => $ord_opt_dlv_amt,
                        'pay_type' => $pay_type,
                        'point_amt' => $ord_opt_point_amt,
                        'coupon_amt' => $ord_opt_coupon_amt,
                        'dc_amt' => $ord_opt_dc_amt,
                        'opt_amt' => $order_opt_amt,
                        'addopt_amt' => $order_addopt_amt,
                        'recv_amt' => $a_recv_amt,
                        'p_ord_opt_no' => $p_ord_opt_no,
                        'dlv_no' => $dlv_no,
                        'dlv_cd' => $dlv_cd,
                        'md_id' => $goods->md_id,
                        'md_nm' => $goods->md_nm,
                        'sale_place' => $store_nm,
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
                        'com_coupon_ratio' => $com_rat,
                        'prd_cd' => $prd_cd,
                        'store_cd' => $store_cd,
                        'sale_kind' => $sale_kind,
                        'pr_code' => $pr_code,
                ]);
                $ord_amt += $order_opt[$i]["price"] * $order_opt[$i]["qty"];
                $point_amt += $ord_opt_point_amt;
                $coupon_amt += $ord_opt_coupon_amt;
                $dc_amt += $ord_opt_dc_amt;
                // $dlv_amt += $ord_opt_dlv_amt;
                $recv_amt += $order_opt[$i]["recv_amt"];
            }

            $order = new Order($user, true);
            $ord_no = $order->ord_no;

            if ($dlv_apply == 'Y' && $ord_amt < $free_dlv_amt) {
                $dlv_amt = $base_dlv_amt;
            }

            DB::table('order_mst')->insert([
                'ord_no' => $ord_no,
                'ord_date' => DB::raw('now()'),
                'user_id' => $user_id,
                'user_nm' => $user_nm,
                'phone' => $phone,
                'mobile' => $mobile,
                'email' => $email,
                'ord_amt' => $ord_amt,
                'point_amt' => $point_amt,
                'coupon_amt' => $coupon_amt,
                'dc_amt' => $dc_amt,
                'dlv_amt' => $dlv_amt,
                'add_dlv_fee' => $add_dlv_fee,
                'recv_amt' => $recv_amt + $dlv_amt + $add_dlv_fee - $point_amt - $coupon_amt - $dc_amt,
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
                'store_cd' => $store_cd,
                'sale_place' => $store_nm,
                'chk_dlv_fee' => DB::raw('NULL'),
                'admin_id' => $c_admin_id
            ]);
            
            $pay_stat = 0;
            $tno = '';
            $pay_amt = $recv_amt + $dlv_amt + $add_dlv_fee - $point_amt - $coupon_amt - $dc_amt;

            ##################################################
            #	부모 결제 정보 복사
            ##################################################
            if ($p_ord_opt_no > 0) {
                $sql = /** @lang text */
                    "
                    select p.tno, p.pay_type, p.pay_stat, p.pay_amt
                    from order_opt o inner join payment p on o.ord_no = p.ord_no
                    where ord_opt_no = :ord_opt_no
                ";
                $row = DB::selectOne($sql, ["ord_opt_no" => $p_ord_opt_no]);
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

            for ($i = 0; $i < count($order_opt); $i++) {
                $order_opt[$i]["ord_no"] = $ord_no;
                DB::table('order_opt')->insert($order_opt[$i]);
                $ord_opt_no = DB::getPdo()->lastInsertId();

                $goods_addopt = Lib::getValue($cart[$i], "goods_addopt", "");
                $a_goods_addopts = explode("^", $goods_addopt);

                foreach ($a_goods_addopts as $a_goods_addopt) {
                    if (!empty($a_goods_addopt)) {
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
                $is_store_order = true;
                $order->SetOrdOptNo($ord_opt_no);
                $order->CompleteOrderSugi("", $ord_state, $is_store_order);

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
                        $sql_addopt = "
                            select addopt_idx, addopt_qty
                            from order_opt_addopt
                            where ord_opt_no = :ord_opt_no
                                and goods_no = :goods_no
                                and goods_sub = :goods_sub
                        ";
                        $rows = DB::select($sql_addopt, [
                            "ord_opt_no" => $ord_opt_no,
                            "goods_no" => $order_opt[$i]["goods_no"],
                            "goods_sub" => $order_opt[$i]["goods_sub"],
                        ]);

                        foreach ($rows as $row) {
                            $addopt_qty = $row->addopt_qty;
                            DB::table('options')
                                ->where("no","=", $row->addopt_idx)
                                ->update([
                                "qty" => DB::raw("ifnull(qty, 0) - $addopt_qty"),
                                "wqty" => DB::raw("ifnull(wqty, 0) - $addopt_qty"),
                            ]);
                        }
                    } else if ($ord_state == "10") { // 출고요청

                        if ($ord_kind != "30") { // 출고구분이 "보류"가 아닌경우 출고요청일 Update
                            //	주문상태 로그
                            $state_log = [
                                "ord_no" => $ord_no,
                                "ord_state" => "10",
                                "comment" => "수기판매",
                                "admin_id" => $user["id"],
                                "admin_nm" => $user["name"]
                            ];
                            $order->AddStateLog($state_log);
                        }

                        // 추가 옵션 온라인 및 보유 재고 처리
                        $sql_addopt = "
                            select addopt_idx, addopt_qty
                            from order_opt_addopt
                            where ord_opt_no = :ord_opt_no
                                and goods_no = :goods_no
                                and goods_sub = :goods_sub
                        ";
                        $rows = DB::select($sql_addopt, [
                            "ord_opt_no" => $ord_opt_no,
                            "goods_no" => $order_opt[$i]["goods_no"],
                            "goods_sub" => $order_opt[$i]["goods_sub"],
                        ]);

                        foreach ($rows as $row) {
                            $addopt_qty = $row->addopt_qty;
                            DB::table('options')
                                ->where("no","=", $row->addopt_idx)
                                ->update([
                                    "qty" => DB::raw("ifnull(qty, 0) - $addopt_qty"),
                                    "wqty" => DB::raw("ifnull(wqty, 0) - $addopt_qty"),
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
                    $point = new Point($user, $user_id);
                    $point->SetOrdNo($ord_no);
                    $point->Order($add_point);
                }
            }

            DB::commit();
            return response()->json([
                    "code" => '200',
                    "ord_no" => $ord_no,
                    "msg" => ""
                ]);
        } catch (Exception $e) {
            DB::rollback();
            // echo $e->getTraceAsString();

            return response()->json([
                    "code" => '500',
                    "msg" => sprintf("[%s %d] %s",$e->getFile(),$e->getLine(),$e->getMessage()),
                    "errer" => $e->getTraceAsString(),
                ], 500);
        }
    }

    /**
     * 전화번호 숫자에 '-' 넣어서 반환
     * - Parameters: $tel(전화번호)
     * - Returns: String
    */
    private function __replaceTel($tel)
    {
        $tel = trim($tel);
        if (strpos($tel, '-') === false) { 
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
}
