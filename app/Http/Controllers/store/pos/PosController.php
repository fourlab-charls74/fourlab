<?php

namespace App\Http\Controllers\store\pos;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Models\Conf;
use App\Models\Order;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

// 테스트매장 -- 추후변경필요
const STORE_CD = 'L0025';

class PosController extends Controller
{
    public function index() 
    {
        $store_cd = STORE_CD;
        $today = date('Y-m-d');
        $sql = "
            select 
                s.idx as sale_type, s.sale_kind, s.sale_type_nm, 
                s.sale_apply, s.amt_kind, s.sale_amt, s.sale_per
            from sale_type_store ss
                inner join sale_type s on s.idx = ss.sale_type_cd
            where ss.store_cd = '$store_cd' and ss.use_yn = 'Y' and ss.sdate <= '$today 00:00:00' and ss.edate >= '$today 23:59:59'
            order by s.sale_kind
        ";
        $sale_types = DB::select($sql);

        $sql = "
            select 
                code_id as pr_code, 
                code_val as pr_code_nm
            from code
            where code_kind_cd = 'PR_CODE' and use_yn = 'Y'
            order by code_seq
        ";
        $pr_codes = DB::select($sql);

        $sql = "
            select store_cd, store_nm
            from store
            where store_cd = :store_cd
        ";
        $store = DB::selectOne($sql, ['store_cd' => $store_cd]);
        if ($store == null) $store = (object) ['store_cd' => $store_cd, 'store_nm' => ''];

        $values = [
            'today' => $today,
            'sale_types' => $sale_types,
            'pr_codes' => $pr_codes,
            'store' => $store,
        ];

        return view(Config::get('shop.store.view') . '/pos/pos', $values);
    }

    public function search_command(Request $request, $cmd)
    {
        switch ($cmd) {
			case 'analysis':
				$response = $this->search_analysis($request);
				break;
			case 'goods':
				$response = $this->search_goods($request);
				break;
			case 'ordno':
				$response = $this->get_ordno($request);
				break;
			case 'member':
				$response = $this->search_member($request);
				break;
			case 'order':
				$response = $this->search_order_history($request);
				break;
			case 'order-detail':
				$response = $this->search_order_detail($request);
				break;
            case 'waiting':
                $response = $this->search_waiting($request);
                break;
            default:
                $message = 'Command not found';
                $response = response()->json(['code' => 0, 'msg' => $message], 404);
		};
		return $response;
    }

    /** 매출분석 및 직전결제내역 조회 */
    public function search_analysis(Request $request)
    {
        $today = date("Y-m-d");
        $store_cd = STORE_CD;
        $sql = "
            select count(ord_no) as ord_cnt, sum(total_amt) as ord_amt, sum(total_qty) as ord_qty
            from (
                select o.ord_no, sum(o.ord_amt) as total_amt, sum(opt.qty) as total_qty
                from order_mst o
                    inner join order_opt opt on opt.ord_no = o.ord_no
                where o.store_cd = '$store_cd'
                    and o.ord_date >= '$today 00:00:00' and o.ord_date <= '$today 23:59:59'
                group by o.ord_no
            ) a
        ";
        $today_analysis = DB::selectOne($sql);

        $sql = "
            select ord_no, date_format(ord_date, '%H시 %i분') as ord_date, ord_amt, recv_amt, (point_amt * -1) as point_amt, (dc_amt * -1) as dc_amt
            from order_mst
            where store_cd = '$store_cd'
                and ord_date >= '$today 00:00:00' and ord_date <= '$today 23:59:59'
            order by ord_date desc
            limit 0,1
        ";
        $prev_analysis = DB::selectOne($sql);

        return response()->json([
            'code' => '200',
            'today_order' => $today_analysis,
            'prev_order' => $prev_analysis,
        ], 200);
    }

    /** 상품검색 */
    public function search_goods(Request $request)
    {
        $store_cd = STORE_CD;
        $search_type = $request->input('search_type', 'prd_cd');
        $search_keyword = $request->input('search_keyword', '');

        $where = "";
        if ($search_keyword != '') {
            if ($search_type == 'prd_cd') {
                $where .= " and pc.prd_cd like '%$search_keyword%' ";
            } else if ($search_type == 'goods_nm') {
                $where .= " and g.goods_nm like '%$search_keyword%' ";
            }
        }

        $page = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;
        $limit = 100;

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "a_500";

        $sql = " 
            select 
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_sm
                , g.goods_no
                , g.goods_sub
                , g.goods_type as goods_type_cd
                , pc.goods_opt
                , pc.brand
                , pc.color
                , pc.size
                , g.goods_nm
                , g.price
                , g.price as ori_price
                , g.goods_sh
                , ps.wqty
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , '' as sale_type
                , '' as pr_code
                , '' as coupon_no
            from product_code pc
                inner join goods g on g.goods_no = pc.goods_no
                inner join product_stock_store ps on ps.prd_cd = pc.prd_cd and ps.store_cd = '$store_cd'
            where 1=1 $where
            order by (CASE WHEN pc.year = '99' THEN 0 ELSE 1 END) desc, pc.year desc
            $limit
        ";
        $rows = DB::select($sql);

        if ($page == 1) {
            $sql = "
                select count(*) as total
                from product_code pc
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join product_stock_store ps on ps.prd_cd = pc.prd_cd and ps.wqty > 0 and ps.store_cd = '$store_cd'
                where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            'code' => 200,
            'head' => [
                'total' => $total,
                'page' => $page,
                'page_cnt' => $page_cnt,
                'page_total' => count($rows),
            ],
            'body' => $rows
        ], 200);
    }

    /** 새로운 주문번호 조회 */
    public function get_ordno()
    {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name,
        ];
        $order = new Order($user, false);
        $ord_no = $order->GetNextOrdNo();
        // $ord_no = 'test-order-no';
        
        return response()->json(['code' => '200', 'ord_no' => $ord_no], 200);
    }

    /** 고객검색 */
    public function search_member(Request $request)
    {
        $store_cd = STORE_CD;
        $search_type = $request->input('search_type', 'user_nm');
        $search_keyword = $request->input('search_keyword', '');

        $where = "";
        if ($search_keyword != '') {
            if ($search_type == 'user_nm') {
                $where .= " and name like '%$search_keyword%' ";
            }
        }

        $page = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;
        $limit = 500;

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        $sql = " 
            select 
                user_id
                , name as user_nm
                , mobile
                , email
                , if(sex = 'F', '여', if(sex = 'M', '남', '-')) as gender
                , yyyy
                , mm
                , dd
                , point
                , addr
                , addr2
                , if(store_cd = '$store_cd', 'Y', 'N') as store_member
            from member
            where 1=1 $where
            order by 
                (case when store_cd = '$store_cd' then 1 else 2 end), 
                (case when ASCII(substring(user_nm, 1)) < 123 then 2 else 1 end), 
                user_nm, 
                user_id
            $limit
        ";
        $rows = DB::select($sql);

        if ($page == 1) {
            $sql = "
                select count(*) as total
                from member
                where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            'code' => 200,
            'head' => [
                'total' => $total,
                'page' => $page,
                'page_cnt' => $page_cnt,
                'page_total' => count($rows),
            ],
            'body' => $rows
        ], 200);
    }

    /** 고객등록 */
    public function add_member(Request $request)
    {
        $code = '200';
        $msg = '';
        $member = '';
        
        $data = (object) $request->all();
        $admin_id = Auth('head')->user()->id;
        $store_cd = STORE_CD;
        $store_nm = '';
        if ($store_cd != '') {
            $row = DB::table('store')->select('store_nm')->where('store_cd', '=', $store_cd)->first();
            if ($row != null) $store_nm = $row->store_nm;
        }

        // 연락처
        $mobile = object_get($data, 'mobile1', '') . '-' . object_get($data, 'mobile2', '') . '-' . object_get($data, 'mobile3', '');

        // 비밀번호 암호화
        $conf = new Conf();
        $encrypt_mode = $conf->getConfigValue("shop", "encrypt_mode");
        $encrypt_key = "";
        if ($encrypt_mode == "mhash") {
            $encrypt_key = $conf->getConfigValue("shop", "encrypt_key");
        }
        // 매장에서 고객등록 시 초기비밀번호는 휴대폰 뒷자리 + '*' 로 설정합니다. -> 추후 개인변경 필요
        $default_pw = object_get($data, 'mobile3', '') . '*';
        $enc_pwd = Lib::get_enc_hash($default_pw, $encrypt_mode, $encrypt_key);

        $user_id = object_get($data, 'user_id', '');

        try {
            DB::beginTransaction();

            $values = [
                'user_id' => $user_id,
                'user_pw' => $enc_pwd,
                'name' => object_get($data, 'name', ''),
                'sex' => object_get($data, 'sex', ''),
                'email' => object_get($data, 'email', ''),
                'email_chk' => 'Y',
                'zip' => object_get($data, 'zipcode', ''),
                'addr' => object_get($data, 'addr1', ''),
                'addr2' => object_get($data, 'addr2', ''),
                'phone' => $mobile,
                'mobile' => $mobile,
                'rmobile' => strrev($mobile),
                'regdate' => now(),
                'lastdate' => now(),
                'point' => 0,
                'ypoint' => 0,
                'yn' => 'Y',
                'mobile_chk' => 'Y',
                'yyyy_chk' => object_get($data, 'yyyy_chk', ''),
                'yyyy' => object_get($data, 'yyyy', '0000'),
                'mm' => sprintf("%02d", object_get($data, 'mm', '00')),
                'dd' => sprintf("%02d", object_get($data, 'dd', '00')),
                'out_yn' => 'N',
                'memo' => object_get($data, 'memo', ''),
                'pwd_reset_yn' => 'N',
                'auth_type' => 'A',
                'auth_yn' => 'Y',
                'auth_key' => $admin_id,
                'store_cd' => $store_cd,
                'store_nm' => $store_nm,
            ];

            DB::table('member')->insert($values);
            
            $msg = "고객정보가 정상적으로 등록되었습니다.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $code = '500';
            $msg = $e->getMessage();
        }

        if ($code == '200') {
            $sql = "
                select 
                    user_id, name as user_nm, sex, if(sex = 'F', '여', if(sex = 'M', '남', '-')) as gender, 
                    yyyy, mm, dd, phone, mobile, email, addr, addr2, point
                from member
                where user_id = '$user_id'
            ";
            $member = DB::selectOne($sql);
        }

        return response()->json(['code' => $code, 'msg' => $msg, 'user' => $member]);
    }

    /** 주문등록 (판매) */
    public function save(Request $req)
    {
        $code = '200';
        $msg = '';

        $conf = new Conf();
        $cfg_ratio = $conf->getConfigValue("point", "ratio");

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name,
        ];

        $ord_no = "";
        $p_ord_opt_no = $req->input("p_ord_opt_no", "");
        $ord_date = date('Y-m-d H:i:s');
        $ord_type = 15; // 출고형태 : 정상(15)
        $ord_kind = 20; // 출고구분 : 출고가능(20)
        $ord_state = $req->input("ord_state", ""); // 주문상태
        $store_cd = STORE_CD; // 주문매장
        $store_nm = '';
        if ($store_cd != '') {
            $row = DB::table('store')->select('store_nm')->where('store_cd', '=', $store_cd)->first();
            if ($row != null) $store_nm = $row->store_nm;
        }

        $cart = $req->input("cart"); // 상품정보

        $coupon_no = $req->input("coupon_no", "");
        $card_amt = $req->input("card_amt", 0); // 카드결제금액
        $cash_amt = $req->input("cash_amt", 0); // 현금결제금액
        $point_amt = $req->input("point_amt", 0); // 적립금결제금액
        $total_amt = array_reduce($cart, function($c, $i) {
            $c += $i['total'] * 1;
            return $c;
        }, 0);
        $memo = $req->input("memo", "");

        $pay_type = 0; // 결제방법
        if ($ord_state == '1') {
            $pay_type = 1; // 입금예정일 경우, 무통장결제 처리
        } else if ($cash_amt > 0) {
            if ($point_amt > 0) $pay_type = 5; // 무통장+적립금
            else $pay_type = 1; // 무통장
        } else {
            if ($card_amt > 0) {
                if ($point_amt > 0) $pay_type = 6; // 카드+적립금
                else $pay_type = 2; // 카드
            } else {
                $pay_type = 4; // 적립금
            }
        }

        $user_id = $req->input("user_id", ""); // 주문자 ID
        $user_nm = "비회원";
        $phone = "";
        $mobile = "";
        $email = "";
        if ($user_id != "") {
            $sql = "
                select user_id, name, phone, mobile, email
                from member
                where user_id = '$user_id'
            ";
            $row = DB::selectOne($sql);
            if($row != null) {
                $user_nm = $row->name;
                $phone = $row->phone;
                $mobile = $row->mobile;
                $email = $row->email;
            }
        }

        $give_point = "Y"; // 적립금지급 여부
        $group_apply = $req->input("group_apply", "");
        $dlv_apply = "N"; // 배송비적용 여부
        $add_dlv_fee = 0; // 추가배송비
        
        $ord_amt = 0;
        $recv_amt = 0;
        $coupon_amt = 0;
        $dc_amt = 0;
        $pay_fee = 0;
        $dlv_amt = 0;
        $fee_rate = 0;

        try {
            DB::beginTransaction();

            $c_admin_id = $user['id'];
            $c_admin_name = $user['name'];

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

            ################################
            #	재고 수량 확인
            ################################
            
            $order_opt = [];

            $a_point_amt = 0;

            for ($i = 0; $i < count($cart); $i++) {
                $goods_no = $cart[$i]['goods_no'] ?? '';
                $goods_sub = $cart[$i]['goods_sub'] ?? '';
                if(empty($goods_sub) || !is_numeric($goods_sub)) $goods_sub = 0;
                $goods_type = $cart[$i]['goods_type_cd'] ?? '';
                $goods_price = $cart[$i]['ori_price'] ?? 0;
                $point = $cart[$i]['point'] ?? 0;
                $prd_cd = $cart[$i]['prd_cd'] ?? '';
                $goods_opt = $cart[$i]['goods_opt'] ?? '';
                $qty = $cart[$i]['qty'] ?? 0; // 판매수량
                $sale_kind = $cart[$i]['sale_type'] ?? ''; // 판매유형
                $pr_code = $cart[$i]['pr_code'] ?? 'JS'; // 행사명
                $coupon_amt = 0;
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
                }
                if ($row != null) $product_stock = $row->wqty;

                if ($goods->is_unlimited == "Y") {
                    if ($product_stock < 1) {
                        $code = '-105';
                        throw new Exception("재고가 부족하여 판매 할 수 없습니다.");
                    }
                } else {
                    if ($qty > $product_stock) {
                        $code = '-105';
                        throw new Exception("재고가 부족하여 판매 할 수 없습니다.");
                    }
                }

                $com_rat = 0;

                $coupon_no = $cart[$i]["coupon_no"] ?? '';
                if ($coupon_no != '') {
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
                // $ord_opt_point_amt = Lib::getValue($cart[$i], "point_amt", 0);
                // $ord_opt_coupon_amt = Lib::getValue($cart[$i], "coupon_amt", 0);
                // $ord_opt_dc_amt = Lib::getValue($cart[$i], "dc_amt", 0);
                // $ord_opt_dlv_amt = Lib::getValue($cart[$i], "dlv_amt", 0);

                $a_ord_amt = $cart[$i]["total"] ?? 0;
                $a_recv_amt = $a_ord_amt;
                $ord_opt_dlv_amt = 0;
                $c_dc_amt = $goods_price - $cart[$i]['price'];

                $divided_point = round(($goods_price / $total_amt) * $point_amt, 0);
                if ($i >= count($cart) - 1) {
                    $divided_point = $point_amt - $a_point_amt;
                } else {
                    $a_point_amt += $divided_point;
                }

                array_push($order_opt, [
                        'goods_no' => $goods_no,
                        'goods_sub' => $goods_sub,
                        'ord_no' => $ord_no,
                        'ord_seq' => '0',
                        'head_desc' => $goods->head_desc,
                        'goods_nm' => $goods->goods_nm,
                        'goods_opt' => $goods_opt,
                        'qty' => $qty,
                        'wonga' => $goods->wonga,
                        'price' => $goods_price,
                        'dlv_amt' => $ord_opt_dlv_amt,
                        'pay_type' => $pay_type,
                        'point_amt' => $divided_point,
                        'coupon_amt' => 0,
                        'dc_amt' => $c_dc_amt,
                        'opt_amt' => $order_opt_amt,
                        'addopt_amt' => $order_addopt_amt,
                        'recv_amt' => $a_recv_amt - $divided_point,
                        'p_ord_opt_no' => $p_ord_opt_no,
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
                        'ord_date' => $ord_date,
                        'dlv_comment' => $memo,
                        'admin_id' => $c_admin_id,
                        'coupon_no' => $coupon_no,
                        'com_coupon_ratio' => $com_rat,
                        'sales_com_fee' => round($a_ord_amt * $fee_rate / 100, 2),
                        'out_ord_opt_no' => null,
                        'prd_cd' => $prd_cd,
                        'store_cd' => $store_cd,
                        'sale_kind' => $sale_kind,
                        'pr_code' => $pr_code,
                ]);
                $ord_amt += $order_opt[$i]["price"] * $order_opt[$i]["qty"];
                // $point_amt += 0;
                $coupon_amt += 0;
                $dc_amt += $c_dc_amt;
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
                'ord_date' =>$ord_date,
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
                'recv_amt' => $recv_amt,
                'ord_state' => $ord_state,
                'upd_date' => DB::raw('now()'),
                'dlv_end_date' => DB::raw('NULL'),
                'ord_type' => $ord_type,
                'ord_kind' => $ord_kind,
                'out_ord_no' => '0',
                'store_cd' => $store_cd,
                'sale_place' => $store_nm,
                'chk_dlv_fee' => DB::raw('NULL'),
                'admin_id' => $c_admin_id
            ]);
            
            $pay_stat = 0;
            $tno = '';
            $pay_amt = $recv_amt;

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

            DB::table('payment')->insert([
                "ord_no"		=> $ord_no,
                "pay_type" 		=> $pay_type,
                "pay_nm" 		=> $user_nm,
                "pay_amt" 		=> $pay_amt,
                "pay_stat" 		=> $pay_stat,
                "tno"           => $tno,
                "bank_inpnm" 	=> '',
                "bank_code" 	=> '',
                "bank_number" 	=> '',
                "card_msg"      => '',
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
                $is_sugi = false;
                $order->SetOrdOptNo($ord_opt_no);
                $order->CompleteOrderSugi($ord_opt_no, $ord_state, $is_store_order, $is_sugi);
    
                if ($ord_state == "10" || $ord_state == "30") {
    
                    // 상품배송완료인경우 상태 변경
                    if ($ord_state == "30") {
    
                        // 주문상태 로그
                        $state_log = array(
                            "ord_no" => $ord_no,
                            "ord_state" => "30",
                            "comment" => "매장판매",
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

                    // 적립금 차감
                    if ($point_amt > 0) {
                        $point->Admin($point_amt, "PAY", "ORDER", "사용");
                    }
                }
            }

            $msg = "주문이 정상적으로 등록되었습니다.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            if($code == '200') $code = '500';
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg,
            "ord_no" => $ord_no,
        ]);
    }

    /** 판매내역조회 */
    public function search_order_history(Request $request)
    {
        $store_cd = STORE_CD;

        $where = "";
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $where .= " and o.ord_date >= '$sdate 00:00:00' ";
        $where .= " and o.ord_date <= '$edate 23:59:59' ";

        $ord_field = "o.ord_date";
        $ord = $request->input('ord', 'desc');
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $page = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;
        $limit = $request->input('limit', 100);

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        $sql = "
            select 
                o.ord_no, o.ord_date, o.user_id, o.user_nm, o.phone, o.mobile, o.ord_amt, o.recv_amt,
                o.ord_state, o.ord_type, o.ord_kind, o.clm_type, pay.pay_type, pt.code_val as pay_type_nm
            from order_mst o
                inner join payment pay on pay.ord_no = o.ord_no
                inner join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = pay.pay_type
            where o.store_cd = :store_cd $where
            $orderby
            $limit
        ";
        $rows = DB::select($sql, ['store_cd' => $store_cd]);

        if ($page == 1) {
            $sql = "
                select count(*) as total
                from order_mst o
                    inner join payment pay on pay.ord_no = o.ord_no
                    inner join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = pay.pay_type
                where o.store_cd = :store_cd $where
			";
            $row = DB::selectOne($sql, ['store_cd' => $store_cd]);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            'code' => 200,
            'head' => [
                'total' => $total,
                'page' => $page,
                'page_cnt' => $page_cnt,
                'page_total' => count($rows),
            ],
            'body' => $rows
        ], 200);
    }

    /** 판매내역 상세조회 */
    public function search_order_detail(Request $request)
    {
        $ord_no = $request->input('ord_no', '');

        $sql = "
            select
                o.ord_no, o.ord_opt_no, o.ord_date, o.prd_cd, o.goods_nm, o.goods_opt, o.pay_type, 
                o.sale_kind, s.sale_type_nm as sale_type, s.amt_kind, if(s.amt_kind = 'per', round(o.price * o.qty / s.sale_per), s.sale_amt) as sale_amount,
                pt.code_val as pay_type_nm, o.dlv_comment, o.price, o.qty, o.point_amt, o.dc_amt, o.recv_amt,
                om.user_id, om.user_nm, om.phone, om.mobile, om.point_amt as total_point_amt, om.recv_amt as total_recv_amt
            from order_opt o
                inner join order_mst om on om.ord_no = o.ord_no
                left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = o.pay_type
                left outer join sale_type s on s.sale_kind = o.sale_kind
            where o.ord_no = :ord_no
        ";
        $rows = DB::select($sql, ['ord_no' => $ord_no]);

        return response()->json(['code' => '200', 'data' => $rows], 200);
    }

    /** 대기내역 조회 */
    public function search_waiting(Request $request)
    {
        $store_cd = STORE_CD;
        $sdate = now()->sub(1, 'month')->format('Y-m-d');
        $edate = date("Y-m-d");

        $sql = "
            select 
                o.ord_no, o.ord_date, o.user_nm, o.ord_amt, 
                o.recv_amt, o.ord_state, sum(opt.qty) as qty
            from order_mst o
                inner join order_opt opt on opt.ord_no = o.ord_no
            where o.store_cd = :store_cd 
                and o.ord_state = 1 
                and o.ord_date >= '$sdate 00:00:00' 
                and o.ord_date <= '$edate 23:59:59'
            group by o.ord_no
            order by o.ord_date desc
        ";
        $rows = DB::select($sql, ['store_cd' => $store_cd]);
        
        return response()->json([
            'code' => '200', 
            'head' => [
                'total' => count($rows),
                'page' => 1,
                'page_cnt' => 1,
                'page_total' => 1,
            ],
            'body' => $rows,
        ], 200);
    }

    /** 대기내역 삭제 */
    public function remove_waiting(Request $request)
    {
        $code = '200';
        $msg = '';
        $ord_no = $request->input('ord_no', '');
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name,
        ];

        try {
            $order = new Order($user);
            $success = $order->DeleteStoreOrder($ord_no);
            if ($success < 1) $code = '500';
        } catch(Exception $e) {
            $code = '500';
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg], 200);
    }
}

