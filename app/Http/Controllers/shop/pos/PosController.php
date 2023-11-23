<?php

namespace App\Http\Controllers\shop\pos;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use App\Models\Conf;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class PosController extends Controller
{
    public function index()
    {
        $store_cd = Auth::guard('head')->user()->store_cd;
        $today = date('Y-m-d');
		// 날짜형식 변경 시간 분 초 삭제
        $sql = "
            select
                s.idx as sale_type, s.sale_kind, s.sale_type_nm,
                s.sale_apply, s.amt_kind, s.sale_amt, s.sale_per
            from sale_type_store ss
                inner join sale_type s on s.idx = ss.sale_type_cd
            where ss.store_cd = '$store_cd' and ss.use_yn = 'Y' and ss.sdate <= '$today' and ss.edate >= '$today'
            order by s.sale_kind
        ";
		
        $sale_types = DB::select($sql);

		foreach ($sale_types as $key => $type) {
			$sql = " select brand from sale_type_brand where sale_type_cd = :idx and use_yn = 'Y' ";
			$brands = array_column(DB::select($sql, [ 'idx' => $type->sale_type ]), 'brand');
			$type->brands = join(',', $brands);
		}

        $sql = "
			select sf.pr_code, c.code_val as pr_code_nm
			from store_fee sf
				inner join code c on c.code_kind_cd = 'PR_CODE' and c.use_yn = 'Y' and c.code_id = sf.pr_code
			where sf.store_cd = :store_cd and sf.use_yn = 'Y'
			order by c.code_seq
        ";
        $pr_codes = DB::select($sql, [ 'store_cd' => $store_cd ]);

        $sql = "
            select store_cd, store_nm, point_out_yn
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
            'clm_reasons' => SLib::getCodes("G_CLM_REASON"),
        ];

        return view(Config::get('shop.shop.view') . '/pos/pos', $values);
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
			case 'order-by-ordno':
				$response = $this->search_order_by_ordno($request);
				break;
            case 'waiting':
                $response = $this->search_waiting($request);
                break;
            case 'member-coupon':
                $response = $this->get_member_coupon_list($request);
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
        $store_cd = Auth::guard('head')->user()->store_cd;
        $sql = "
            select count(ord_no) as ord_cnt, sum(ord_amt) as ord_amt, sum(pay_amt) as pay_amt, sum(total_qty) as ord_qty
            from (
                select o.ord_no, sum(o.ord_amt) as ord_amt, sum(o.recv_amt) as pay_amt, sum(opt.qty) as total_qty
                from order_mst o
                    inner join order_opt opt on opt.ord_no = o.ord_no
                where o.store_cd = '$store_cd'
                    and o.ord_state >= 30
                    and o.ord_date >= '$today 00:00:00'
                    and o.ord_date <= '$today 23:59:59'
                group by o.ord_no
            ) a
        ";
        $today_analysis = DB::selectOne($sql);

        $sql = "
            select ord_no, date_format(ord_date, '%H시 %i분') as ord_date
                , ord_amt, recv_amt, (point_amt * -1) as point_amt, (dc_amt * -1) as dc_amt, (coupon_amt * -1) as coupon_amt
            from order_mst
            where store_cd = '$store_cd'
                and ord_state >= 30
                and ord_date >= '$today 00:00:00'
                and ord_date <= '$today 23:59:59'
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
        $store_cd = Auth::guard('head')->user()->store_cd;
        $search_type = $request->input('search_type', 'prd_cd');
        $search_keyword = $request->input('search_keyword', '');

        $where = "";
        if ($search_keyword != '') {
            if ($search_type == 'prd_cd') {
                $where .= " and pc.prd_cd like '$search_keyword%' ";
            } else if ($search_type == 'goods_nm') {
                $where .= " and g.goods_nm like '%$search_keyword%' ";
            } else if ($search_type == 'style_no') {
                $where .= " and g.style_no like '$search_keyword%' ";
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

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "a_500";
		
		// product_code > plan_category 에 따른 상품조회
		// (01) 정상매장 	- 백화점매장 (store_kind = 01)
		// (02) 전매장 		- 모든매장
		// (03) 이월취급점 	- 모든매장 (이월상품취급매장 작업 이후, 아울렛매장 or 이월상품취급매장 으로 수정필요)
		// (04) 아울렛전용 	- 아울렛매장 (store_kind = 03)

        $sql = "
            select pc.prd_cd, pc.prd_cd_p as prd_cd_sm
                , pc.goods_no, g.goods_sub, g.goods_nm, g.goods_nm_eng, pc.goods_opt, g.style_no
                , g.goods_type as goods_type_cd, g.brand, p.price, p.price as ori_price, p.tag_price as goods_sh
                , (100 - round(p.price / p.tag_price * 100)) as dc_rate
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , ifnull((select wqty from product_stock_store where store_cd = :store_cd and prd_cd = pc.prd_cd), 0) as wqty
                , concat('[', pc.color, '] ', color.code_val) as color
                -- , (
				-- 	select s.size_cd from size s 
				-- 	where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX'))) 
				-- 		and s.size_cd = pc.size
				-- 		and use_yn = 'Y'
				-- ) as size
                , pc.size
                , '' as sale_type
                , '' as pr_code
                , '' as coupon_no
            from product_code pc
                inner join product p on p.prd_cd = pc.prd_cd
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code color on color.code_kind_cd = 'PRD_CD_COLOR' and color.code_id = pc.color
                -- left outer join (select prd_cd, store_cd from product_stock_release where type = 'F' and state >= 30 group by prd_cd) psr on psr.prd_cd = pc.prd_cd and psr.store_cd = ps.store_cd   -- 해당매장에 초도출고된적이 있는 상품만 검색가능하도록 설정
            where 1=1 $where
            	and if(pc.plan_category = '01', (select store_kind from store where store_cd = :store_cd2) = '01',
					if(pc.plan_category = '03', 1=1,
					if(pc.plan_category = '04', (select store_kind from store where store_cd = :store_cd3) = '03', 
				1=1)))
              -- and if(ps.wqty > 0, 1=1, psr.prd_cd is not null) 
            order by (case when pc.year = '99' then 0 else 1 end) desc
                , (case when pc.brand = 'F' then 0 else 1 end) asc
                , pc.prd_cd desc
            $limit
        ";
        $rows = DB::select($sql, [ 'store_cd' => $store_cd, 'store_cd2' => $store_cd, 'store_cd3' => $store_cd ]);

        if ($page == 1) {
            $sql = "
                select count(*) as total
                from product_code pc
					inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code color on color.code_kind_cd = 'PRD_CD_COLOR' and color.code_id = pc.color
                    -- inner join code size on size.code_kind_cd = if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', if(pc.gender = 'U', 'PRD_CD_SIZE_UNISEX', 'PRD_CD_SIZE_MATCH'))) and size.code_id = pc.size
                where 1=1 $where
					and if(pc.plan_category = '01', (select store_kind from store where store_cd = :store_cd2) = '01',
						if(pc.plan_category = '03', 1=1,
						if(pc.plan_category = '04', (select store_kind from store where store_cd = :store_cd3) = '03', 
					1=1)))
			";
            $row = DB::selectOne($sql, [ 'store_cd2' => $store_cd, 'store_cd3' => $store_cd ]);
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
        $store_cd = Auth::guard('head')->user()->store_cd;
        $search_type = $request->input('search_type', 'user_nm');
        $search_keyword = $request->input('search_keyword', '');

        $where = "";
        if ($search_keyword != '') {
            if ($search_type == 'user_nm') {
                $where .= " and name like '%$search_keyword%' ";
            }
            if ($search_type == 'phone') {
				$search_keyword = strrev(str_replace('-', '', $search_keyword));
                $where .= " and replace(rmobile, '-', '') like '$search_keyword%' ";
            }
        }

        $page = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;
        $limit = $request->input('limit', 500);

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
        $store_cd = Auth::guard('head')->user()->store_cd;
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
		if (object_get($data, 'id_mobile_same_yn', '') === 'Y') {
			$user_id = object_get($data, 'mobile1', '') . object_get($data, 'mobile2', '') . object_get($data, 'mobile3', '');
		}

        try {
            DB::beginTransaction();

            $values = [
                'user_id' => $user_id,
                'user_pw' => $enc_pwd,
                'name' => object_get($data, 'name', ''),
                'sex' => object_get($data, 'sex', ''),
                'email' => object_get($data, 'email', ''),
                'email_chk' => object_get($data, 'send_mail_yn', 'N'),
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
                'mobile_chk' => object_get($data, 'send_mobile_yn', 'N'),
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

    /** 회원 휴대폰 중복체크 */
	public function check_phone(Request $request) {
        $mobile1 = $request->input('mobile1', '');
        $mobile2 = $request->input('mobile2', '');
        $mobile3 = $request->input('mobile3', '');
        $mobile = $mobile1 . '-' . $mobile2 . '-' . $mobile3;
		$mobile_cnt = DB::table('member')->where('mobile', $mobile)->count();
		
		$user_id = $mobile1 . $mobile2 . $mobile3;
		$user_cnt = DB::table('member')->where('user_id', $user_id)->count();
		
		return response()->json([ 'mobile_cnt' => $mobile_cnt, 'user_cnt' => $user_cnt ]);
    }

    /** 주문등록 (판매 / 대기) */
    public function save(Request $req)
    {
        $code = '';
        $msg = '';

        $conf = new Conf();
        $cfg_ratio = $conf->getConfigValue('point', 'ratio');

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name,
        ];

        #####################################################
        #   SET VALUES
        #####################################################
        $ord_no = $req->input('ord_no', '') ?? '';
        $ord_state = $req->input('ord_state', ''); // 주문상태 (30: 출고완료 / 1: 입금예정)
        $card_amt = $req->input('card_amt', 0); // 카드결제금액
        $cash_amt = $req->input('cash_amt', 0); // 현금결제금액
        $point_amt = $req->input('point_amt', 0); // 포인트결제금액
        $member_id = $req->input('user_id', '') ?? ''; // 주문고객 아이디
        $memo = $req->input('memo', '') ?? ''; // 특이사항 메모
        $cart = $req->input('cart', []); // 상품목록
        $removed_cart = $req->input('removed_cart', []); // 삭제할 ord_opt_no 목록 (대기주문 판매처리 시 사용)
        $reservation_yn = $req->input('reservation_yn', 'N'); // 예약판매여부

        $is_new = $ord_no === '';
        $ord_date = date('Y-m-d H:i:s');
        $ord_type = 15; // 출고형태: 정상(15)
        if ($reservation_yn === 'Y') $ord_type = 4; // 예약(4)
        $ord_kind = 20; // 출고구분: 출고가능(20)
        $dlv_apply = 'N'; // 배송비적용여부
        $store_cd = Auth::guard('head')->user()->store_cd;
        $store = DB::table('store')->select('store_cd', 'store_nm', 'point_in_yn', 'point_ratio', 'point_out_yn')->where('store_cd', $store_cd)->first();

		// G_PAY_TYPE [ 무통장입금(1) / 카드(2) / 적립금(4) / 쿠폰(8) ]
		$pay_type = 0;
		$max_pay_type = 0;
		if ($ord_state === '1') $pay_type = 1;
		else {
			if ($cash_amt > 0) $pay_type += 1;
			if ($card_amt > 0) $pay_type += 2;
			if ($point_amt > 0) $pay_type += 4;
		}

        // set member
        $member = null;
        $member_nm = '비회원';
        $phone = $mobile = $email = '';
        if ($member_id !== '') {
            $member = DB::table('member')->select('user_id', 'name', 'phone', 'mobile', 'email')->where('user_id', $member_id)->first();
            if ($member !== null) {
                $member_nm = $member->name;
                $phone = $member->phone;
                $mobile = $member->mobile;
                $email = $member->email;
            }
        }

        try {
            DB::beginTransaction();
			
			////////////////
			//// 적립금 사용 매장이 맞는지 검토
			if($store->point_out_yn !== 'Y' && $point_amt > 0){
				$code = '-102';
				throw new Exception("포인트를 사용 할 수 없는 매장입니다.");
			}
			////////////////

            #####################################################
            #   회원그룹별 포인트 지급여부 및 지급율 조회
            #####################################################
            $point_flag = false; // 포인트지급여부
            $add_point_ratio = 0;

            if ($store !== null && $store->point_in_yn === 'Y' && $member !== null) {
                $point_flag = true;
                $sql = "
                    select um.group_no, u.point_ratio
                    from user_group_member um
                        inner join user_group u on u.group_no = um.group_no
                    where um.user_id = :user_id
                        order by u.point_ratio desc
                    limit 1
                ";
                $member_group = DB::selectOne($sql, ['user_id' => $member_id]);
                if ($member_group !== null) $add_point_ratio = $member_group->point_ratio;
            }

            #####################################################
            #   재고수량 체크 및 판매데이터 정렬
            #####################################################
            $order_opts = [];
            $ord_amt = 0; // 총 주문금액
			$ord_qty = 0; // 총 주문수량
            $a_ord_amt = array_reduce($cart, function($a, $c) { return $a + ($c['ori_price'] ?? 0 * $c['qty'] ?? 0); }, 0); // 대략적 주문금액
            $dc_amt = 0; // 총 할인금액 (판매유형에 따른 할인)
            $coupon_amt = 0; // 총 쿠폰할인금액
            $recv_amt = 0; // 총 실결제금액
            $used_point_amt = 0;

            for ($i = 0; $i < count($cart); $i++) {
                $item = $cart[$i];
                $prd_cd = $item['prd_cd'] ?? '';
                $goods_no = $item['goods_no'] ?? '';
                $goods_opt = $item['goods_opt'] ?? '';
                $qty = $item['qty'] ?? 0; // 판매수량
                $sale_kind = $item['sale_type'] ?? ''; // 판매유형
                $pr_code = $item['pr_code'] ?? ''; // 행사명
                $coupon_no = $item['c_no'] ?? ''; // 쿠폰아이디
				$item_pay_type = $pay_type; // 상품별 결제방법 (쿠폰사용여부 추가)

                $opt_ord_type = 15; // order_opt의 ord_type (정상:15 / 예약:4)

                $sql = "
                    select g.goods_no, g.goods_sub, g.goods_nm, g.com_id, g.com_type, c.com_nm, (c.pay_fee / 100) as com_rate
                        , g.head_desc, g.goods_type, g.baesong_kind, g.baesong_price, g.md_id, g.md_nm
                        , g.point, g.point_cfg, g.point_yn, g.point_unit, g.is_unlimited, g.wonga, g.price, g.goods_sh
                        , '' as margin_rate
                    from goods g
                        left outer join company c on c.com_id = g.com_id
                    where g.goods_no = :goods_no
                ";
                $goods = DB::selectOne($sql, ['goods_no' => $goods_no]);
				
				$sql = "
					select p.price, p.wonga, p.tag_price as goods_sh from product p where p.prd_cd = :prd_cd
				";
				$product = DB::selectOne($sql, [ 'prd_cd' => $prd_cd ]);

                $item_ord_amt = $product->price * $qty; // 해당상품 총 주문금액 (주문금액은 판매가기준)

                ######################### 재고수량 판매가능여부 체크 ############################
                $prd_wqty = DB::table('product_stock_store')->where('prd_cd', $prd_cd)->where('store_cd', $store_cd)->value('wqty');

                // 예약판매가 아닐 경우에만 재고부족 에러처리
                if (($goods->is_unlimited === 'Y' && $prd_wqty < 1) || $qty > $prd_wqty) {
                    if ($reservation_yn === 'Y') {
                        $opt_ord_type = 4; // order_opt의 ord_type (정상:15 / 예약:4)
                    } else {
                        if ($prd_wqty > 0) {
                            $code = '-104';
                            throw  new Exception("재고가 부족하여 판매할 수 없습니다. (재고 1개 이상 예약판매 불가)");
                        } else {
                            $code = '-105';
                            throw new Exception("재고가 부족하여 판매할 수 없습니다.");
                        }
                    }
                }

                ######################### 상품별 쿠폰금액 반영 ############################
                $com_ratio = 0; // 업체 쿠폰정산율
                $item_coupon_amt = 0; // 해당상품 총 쿠폰할인금액

                if ($coupon_no !== '') {
                    $com_ratio = DB::table('coupon_company')->where('coupon_no', $coupon_no)->where('com_id', $goods->com_id)->value('com_rat');

                    $sql = "
                        select a.*
                        from (
                            select cm.user_id, cm.use_to_date as to_date, cm.down_date, cm.coupon_no, c.coupon_nm, c.coupon_type
                                , if(c.use_date_type = 'S', c.use_fr_date, date_format(cm.down_date, '%Y%m%d')) as use_fr_date
                                , if(c.use_date_type = 'S', c.use_to_date, date_format(date_add(cm.down_date, interval c.use_date DAY), '%Y%m%d')) as use_to_date
                                , c.coupon_apply
                                , c.coupon_amt_kind, c.coupon_amt, c.coupon_per
                                , c.price_yn, c.low_price, c.high_price
                            from coupon_member cm
                                inner join coupon c on c.coupon_no = cm.coupon_no and c.use_yn = 'Y' and c.coupon_type <> 'O'
                            where cm.user_id = :user_id and cm.coupon_no = :coupon_no and cm.use_yn = 'N'
                            group by cm.coupon_no
                        ) a
                            where date_format(now(), '%Y%m%d') >= a.use_fr_date and date_format(now(), '%Y%m%d') <= a.use_to_date
                    ";
                    $cp = DB::selectOne($sql, ['user_id' => $member_id, 'coupon_no' => $coupon_no]);
					
					$goods_nos = DB::table('coupon_goods')->where('coupon_no', $coupon_no)->select('goods_no')->get()->toArray();
					$goods_nos = array_map(function ($no) { return $no->goods_no; }, $goods_nos);
					$ex_goods_nos = DB::table('coupon_goods_ex')->where('coupon_no', $coupon_no)->select('goods_no')->get()->toArray();
					$ex_goods_nos = array_map(function ($no) { return $no->goods_no; }, $ex_goods_nos);

                    // 해당 쿠폰의 사용기간 유효성 체크
                    if ($cp === null) {
                        $code = '-110';
                        throw new Exception("사용하신 쿠폰 중 현재 사용할 수 없는 쿠폰이 있습니다.");
                    }
                    // 해당 쿠폰의 최고가/최저가 부합 체크
                    if ($cp->price_yn === 'Y' && ($cp->low_price >= 0 && ($cp->low_price > $item_ord_amt) || ($cp->high_price > 0 && $cp->high_price < $item_ord_amt))) {
                        $code = '-111';
                        throw new Exception("[" . $cp->coupon_nm . "]은 주문금액이 최소 " . number_format($cp->low_price) . "원 / 최대 " . number_format($cp->high_price) . "원인 상품에만 적용할 수 있습니다.");
                    }
                    // 해당 쿠폰의 해당/제외 상품정보 부합 체크
                    if (
                        ($cp->coupon_apply === 'AG' && in_array($goods->goods_no, $ex_goods_nos))
                        || ($cp->coupon_apply === 'SG' && !in_array($goods->goods_no, $goods_nos))
                    ) {
                        $code = '-112';
                        throw new Exception("[" . $goods->goods_nm . "]상품에는 해당 쿠폰을 적용할 수 없습니다.");
                    }

                    // 쿠폰할인은 TAG가 기준입니다.
                    $item_coupon_amt = $cp->coupon_amt_kind === 'P'
                        ? round($product->goods_sh * $qty * ($cp->coupon_per ?? 0) / 100, 0)
                        : ($cp->coupon_amt ?? 0);
                }

				if ($item_coupon_amt > 0) $item_pay_type += 8; // 쿠폰결제(8)
				$max_pay_type = max($max_pay_type, $item_pay_type);

                ######################### 상품별 적립금 반영 ############################
                $item_point_amt = round($product->price / $a_ord_amt * $point_amt, 0); // 해당상품 적립금사용금액

                if ($i < count($cart) - 1) $used_point_amt += $item_point_amt;
                else $item_point_amt = $point_amt - $used_point_amt;

                ######################### 상품별 추가적립금 반영 ############################
				if($store !== null && $store->point_in_yn == 'Y' && $store->point_ratio > 0 ){
					$cfg_ratio	= $store->point_ratio;	// 매장별 포인트 적립률 적용
				}
				
                $add_group_point = $ord_opt_add_point = 0;

                if ($add_point_ratio > 0) $add_group_point = ($product->price * $add_point_ratio / 100) * $qty;
                if ($point_flag && $goods->point_yn === 'Y') {
                    if ($goods->point_cfg === 'G') {
                        if ($goods->point_unit === 'P') {
                            $ord_opt_add_point = round(($product->price * $goods->point / 100) * $qty, 0) + $add_group_point;
                        } else {
                            $ord_opt_add_point = ($goods->point * $qty) + $add_group_point;
                        }
                    } else {
                        $ord_opt_add_point = round(($product->price * $cfg_ratio / 100) * $qty, 0) + $add_group_point;
                    }
                }

                ######################### 상품별 판매할인금액 반영 ############################
                $item_dc_amt = 0; // 해당상품 총 할인금액

                $sk = DB::table('sale_type')->select('sale_apply', 'amt_kind', 'sale_amt', 'sale_per')
                    ->where('sale_kind', $sale_kind)->where('use_yn', 'Y')->first();
                if ($sk !== null) {
                    $item_dc_amt = $sk->amt_kind === 'per'
                    ? $sk->sale_apply === 'tag'
                        ? round($product->goods_sh * $qty * ($sk->sale_per ?? 0) / 100, 0)
                        : round($product->price * $qty * ($sk->sale_per ?? 0) / 100, 0)
                    : ($sk->sale_amt ?? 0);
                    if ($item_dc_amt < 0) $item_dc_amt = 0;
                }

                ######################### set order_opt values ############################
                // 매장판매에서는 배송비 제외하고 실결제금액 계산
                $item_recv_amt = $item_ord_amt - $item_coupon_amt - $item_dc_amt - $item_point_amt;

                $order_opt = [
                    'goods_no'      => $goods_no,
                    'goods_sub'     => $goods->goods_sub ?? 0,
                    'ord_seq'       => '0',
                    'head_desc'     => $goods->head_desc ?? '',
                    'goods_nm'      => $goods->goods_nm ?? '',
                    'goods_opt'     => $goods_opt,
                    'qty'           => $qty,
                    'wonga'         => $product->wonga,
                    'price'         => $product->price,
                    'dlv_amt'       => 0,
                    'pay_type'      => $item_pay_type,
                    'coupon_amt'    => $item_coupon_amt,
                    'dc_amt'        => $item_dc_amt,
                    'point_amt'     => $item_point_amt,
                    'recv_amt'      => $item_recv_amt,
                    'opt_amt'       => 0,
                    'addopt_amt'    => 0,
                    'p_ord_opt_no'  => '0',
                    'md_id'         => $goods->md_id ?? '',
                    'md_nm'         => $goods->md_nm ?? '',
                    'sale_place'    => '',
                    'ord_state'     => $ord_state,
                    'clm_state'     => '0',
                    'com_id'        => $goods->com_id ?? '',
                    'add_point'     => $ord_opt_add_point,
                    'ord_kind'      => $ord_kind,
                    'ord_type'      => $opt_ord_type,
                    'baesong_kind'  => $goods->baesong_kind,
                    'ord_date'      => $ord_date,
                    'dlv_comment'   => $memo,
                    'admin_id'      => $user['id'],
                    'coupon_no'     => $coupon_no,
                    'com_coupon_ratio'  => $com_ratio,
                    'sales_com_fee' => 0,
                    'out_ord_opt_no'    => null,
                    'prd_cd'        => $prd_cd,
                    'store_cd'      => $store_cd,
                    'sale_kind'     => $sale_kind,
                    'pr_code'       => $pr_code,
                ];
                array_push($order_opts, $order_opt);

                $ord_amt += $item_ord_amt;
                $ord_qty += $qty * 1;
                $coupon_amt += $item_coupon_amt;
                $dc_amt += $item_dc_amt;
                $recv_amt += $item_recv_amt;
            }

            #####################################################
            #   판매데이터 등록 (order_mst / order_opt / payment)
            #   재고처리
            #####################################################
            $order_mst = [
                'ord_date'      => $ord_date,
                'user_id'       => $member_id,
                'user_nm'       => $member_nm,
                'phone'         => $phone,
                'mobile'        => $mobile,
                'email'         => $email,
                'ord_amt'       => $ord_amt,
                'coupon_amt'    => $coupon_amt,
                'dc_amt'        => $dc_amt,
                'point_amt'     => $point_amt,
                'recv_amt'      => $recv_amt,
                'dlv_amt'       => 0,
                'add_dlv_fee'   => 0,
                'ord_state'     => $ord_state,
                'upd_date'      => now(),
                'dlv_end_date'  => DB::raw('NULL'),
                'ord_type'      => $ord_type,
                'ord_kind'      => $ord_kind,
                'out_ord_no'    => '0',
                'store_cd'      => $store_cd,
                'sale_place'    => $store !== null ? $store->store_nm : '',
                'chk_dlv_fee'   => DB::raw('NULL'),
                'admin_id'      => $user['id'],
            ];

            $payment = [
                "pay_type" 		=> $max_pay_type,
                "pay_nm" 		=> $member_nm,
                "pay_amt" 		=> $recv_amt,
                "pay_point"     => $point_amt,
                "pay_baesong"   => 0,
                "coupon_amt"    => $coupon_amt,
                "dc_amt"        => $dc_amt,
                "ord_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
                "upd_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
            ];

            $order = '';
            if ($is_new) {
                $order = new Order($user, true);
                $ord_no = $order->ord_no;

                DB::table('order_mst')->insert(array_merge($order_mst, [ 'ord_no' => $ord_no ]));
                DB::table('payment')->insert(array_merge($payment, [
                    "ord_no"		=> $ord_no,
                    "pay_stat" 		=> 0,
                    "bank_inpnm" 	=> '',
                    "bank_code" 	=> '',
                    "bank_number" 	=> '',
                    "card_msg"      => '',
                    "pay_ypoint"    => 0,
                ]));
            } else {
                $order = new Order($user, false);
                $order->SetOrdNo($ord_no);
                DB::table('order_mst')->where('ord_no', $ord_no)->update($order_mst);
                DB::table('payment')->where('ord_no', $ord_no)->update($payment);
            }

            // 주문대기건 판매처리 시, 삭제된 상품 처리
            for ($i = 0; $i < count($removed_cart); $i++) {
                DB::table("order_opt")->where("ord_opt_no", $removed_cart[$i])->delete();
            }

            for ($i = 0; $i < count($order_opts); $i++) {
                $order_opts[$i]['ord_no'] = $ord_no;
                $ord_opt_no = '';
                $o_ord_opt_no = $cart[$i]['ord_opt_no'] ?? '';

                if ($o_ord_opt_no === '') {
                    DB::table('order_opt')->insert($order_opts[$i]);
                    $ord_opt_no = DB::getPdo()->lastInsertId();
                } else {
                    DB::table('order_opt')->where('ord_opt_no', $o_ord_opt_no)->update($order_opts[$i]);
                    $ord_opt_no = $o_ord_opt_no;
                }

                ######################### 상품별 재고처리 ############################
                $is_store_order = true;
                $is_sugi = false;
                $order->SetOrdOptNo($ord_opt_no);
                $order->CompleteOrderSugi($ord_opt_no, $ord_state, $is_store_order, $is_sugi);

                if ($ord_state === '30') {

                    // 주문상태 로그반영
                    $state_log = [
                        'ord_no' => $ord_no,
                        'ord_state' => $ord_state,
                        'comment' => '매장판매',
                        'admin_id' => $user['id'],
                        'admin_nm' => $user['name'],
                    ];
                    $order->AddStateLog($state_log);

                    // order_opt_wonga 정산건 반영
                    $order->DlvLog($ord_state);
                }

                ######################### 사용한 쿠폰 처리 ############################
                $coupon_no = $cart[$i]['c_no'] ?? '';
                $c_idx = $cart[$i]['coupon_no'] ?? '';
                if ($coupon_no !== '') {
                    DB::table('coupon')->where('coupon_no', $coupon_no)->update([
                        'coupon_use_cnt' => DB::raw('coupon_use_cnt + 1'),
                        'coupon_order_cnt' => DB::raw('coupon_order_cnt + 1'),
                    ]);

                    DB::table('coupon_member')->where('idx', $c_idx)->update([
                        'ord_opt_no' => $ord_opt_no,
                        'use_date' => now(),
                        'use_yn' => 'Y',
                        'ut' => now(),
                    ]);

                    DB::table('coupon_use_log_t')->insert([
                        'coupon_no' => $coupon_no,
                        'user_id' => $member_id,
                        'ord_opt_no' => $ord_opt_no,
                        'ord_no' => $ord_no,
                        'order_amt' => $ord_amt,
                        'coupon_amt' => $coupon_amt,
                        'regi_date' => now(),
                        'use_gubun' => '1',
                    ]);
                }
            }

            #####################################################
            #   적립금 지급 & 차감
            #####################################################

            if ($ord_state !== '1' && $member_id !== '') {
                $point = new Point($user, $member_id);

                // 적립금 지급
                if ($point_flag) {
                    $point->SetOrdNo($ord_no);
                    $point->StoreOrder();
                }

                // 적립금 차감
                if ($point_amt > 0) {
                    $point->Admin($point_amt, "PAY", "ORDER", "사용");
                }
            }

            DB::commit();
            $code = '200';
            $msg = "주문이 정상적으로 등록되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            if($code === '') $code = '500';
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg, 'ord_no' => $ord_no], 200);
    }

    /** 판매내역조회 */
    public function search_order_history(Request $request)
    {
        $store_cd = Auth::guard('head')->user()->store_cd;

        $where = "";
        $sdate = $request->input('sdate', now()->sub(1, 'month')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $where .= " and o.ord_date >= '$sdate 00:00:00' ";
        $where .= " and o.ord_date <= '$edate 23:59:59' ";
		
		$keyword = $request->input('keyword', '');

		if ($keyword != '') {
			if (is_numeric($keyword)) {
				$where .= "and o.mobile like '%" . Lib::quote($keyword) . "' ";
			} else {
				$where .= " and o.user_nm = '" . Lib::quote($keyword) . "' ";
			}
		}

        $ord_field = "o.ord_date";
        $ord = $request->input('ord', 'desc');
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $page = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;
        $limit = $request->input('limit', 100);

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $ord_no = $request->input('ord_no', '');
        if ($ord_no != '') $where = " and o.ord_no = '$ord_no' ";

        $total = 0;
        $page_cnt = 0;

        $sql = "
            select
                o.ord_no, o.ord_date, o.user_id, o.user_nm, o.phone, o.mobile, o.ord_amt, o.recv_amt,
                o.ord_state, o.ord_type, o.ord_kind, o.clm_type, pay.pay_type, pt.code_val as pay_type_nm,
                oo.ord_opt_no, oo.clm_state
            from order_opt oo
                inner join order_mst o on o.ord_no = oo.ord_no
                inner join payment pay on pay.ord_no = oo.ord_no
                inner join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = pay.pay_type
            where o.store_cd = :store_cd and o.ord_state >= 30 $where
            $orderby
            $limit
        ";
        $rows = DB::select($sql, ['store_cd' => $store_cd]);

        if ($page == 1) {
            $sql = "
                select count(*) as total
				from order_opt oo
					inner join order_mst o on o.ord_no = oo.ord_no
					inner join payment pay on pay.ord_no = oo.ord_no
                    inner join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = pay.pay_type
                where o.store_cd = :store_cd and o.ord_state >= 30 $where
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

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "a_500";

        $sql = "
            select
                o.ord_no
                , o.ord_opt_no
                , o.ord_date
                , o.ord_type
                , o.prd_cd
                , o.goods_no
                , g.goods_sub
                , o.goods_nm
                , o.goods_opt
                , g.brand
                , p.price
                , p.price as ori_price
                , p.tag_price as goods_sh
                , o.pay_type
                , o.sale_kind
                , s.sale_type_nm
                , s.amt_kind
                , if(s.amt_kind = 'per', round(o.price * o.qty / s.sale_per), s.sale_amt) as sale_amount
                , pt.code_val as pay_type_nm
                , o.dlv_comment
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , o.qty
                , o.point_amt
                , o.dc_amt
                , o.coupon_amt
                , o.recv_amt
                , o.clm_state
                , if(o.clm_state < 60, '', ifnull((select ord_state_date from order_opt_wonga where ord_opt_no = o.ord_opt_no and ord_state = o.clm_state), '')) as clm_state_date
                , om.user_id
                , om.user_nm
                , om.mobile
                , m.email
                , if(m.sex = 'F', '여', if(m.sex = 'M', '남', '-')) as gender
                , m.yyyy
                , m.mm
                , m.dd
                , m.point
                , m.addr
                , m.addr2
                , om.coupon_amt as total_coupon_amt
                , om.dc_amt as total_dc_amt
                , om.point_amt as total_point_amt
                , om.recv_amt as total_recv_amt
                , om.ord_state
                , pc.color
                , pc.size
                , '' as sale_type
                , '' as pr_code
                , '' as coupon_no
            from order_opt o
                inner join order_mst om on om.ord_no = o.ord_no
                inner join product_code pc on pc.prd_cd = o.prd_cd
                inner join product p on p.prd_cd = o.prd_cd
                left outer join goods g on g.goods_no = o.goods_no
                left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = o.pay_type
                left outer join sale_type s on s.sale_kind = o.sale_kind
                left outer join member m on m.user_id = om.user_id
            where o.ord_no = :ord_no
            order by o.ord_opt_no
        ";
        $rows = DB::select($sql, ['ord_no' => $ord_no]);

        return response()->json(['code' => '200', 'data' => $rows], 200);
    }

    /** 주문번호로 판매내역 검색 */
    public function search_order_by_ordno(Request $request)
    {
        $code = 0;
        $ord_no = '';
        $keyword = $request->input('ord_no', '');
        $store_cd = Auth::guard('head')->user()->store_cd;

        $sql = "
            select ord_no
            from order_mst
            where ord_no = :ord_no and store_cd = :store_cd
        ";
        $ord = DB::selectOne($sql, ['ord_no' => $keyword, 'store_cd' => $store_cd]);

        if ($ord != null) {
            $code = 200;
            $ord_no = $ord->ord_no;
        } else {
            $code = 404;
        }

        return response()->json(['code' => $code, 'ord_no' => $ord_no]);
    }

    /** 대기내역 조회 */
    public function search_waiting(Request $request)
    {
        $store_cd = Auth::guard('head')->user()->store_cd;
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

    /** 오프라인 쿠폰 등록 */
    public function add_coupon(Request $request)
    {
        $code = 0;
        $msg = '';

        $user_id = $request->input('user_id', '');
        $serial_num = $request->input('serial_num', '');

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        try {
            DB::beginTransaction();

            $coupon = new Coupon($user);
            $result = $coupon->offCouponAdd($user_id, $serial_num);

            if ($result['code'] < 1) {
                if ($result['code'] < -1) $code = 400;
                throw new Exception($result['msg'] ?? '');
            }

            DB::commit();

            $code = 200;
            $msg = '오프라인쿠폰이 정상적으로 등록되었습니다.';
        } catch (Exception $e) {
            DB::rollback();

            if ($code < 1) $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg], 200);
    }

    /** 해당고객의 사용가능한 쿠폰목록 조회 */
    public function get_member_coupon_list(Request $request)
    {
        $user_id = $request->input('user_id', '');

        $sql = "
            select a.*
            from (
                select cm.user_id, cm.use_to_date as to_date, cm.down_date, cm.idx as coupon_no, cm.coupon_no as c_no, c.coupon_nm, c.coupon_type
                    , if(c.use_date_type = 'S', c.use_fr_date, date_format(cm.down_date, '%Y%m%d')) as use_fr_date
                    , if(c.use_date_type = 'S', c.use_to_date, date_format(date_add(cm.down_date, interval c.use_date DAY), '%Y%m%d')) as use_to_date
                    , c.coupon_apply, c.coupon_amt_kind, c.coupon_amt, c.coupon_per
                    , c.price_yn, c.low_price, c.high_price
                from coupon_member cm
                    inner join coupon c on c.coupon_no = cm.coupon_no and c.use_yn = 'Y' and c.coupon_type <> 'O'
                where cm.user_id = :user_id and cm.use_yn = 'N'
            ) a
                where date_format(now(), '%Y%m%d') >= a.use_fr_date and date_format(now(), '%Y%m%d') <= a.use_to_date
        ";
        $result = DB::select($sql, ['user_id' => $user_id]);
		
		foreach ($result as $row) {
			$goods_nos = DB::table('coupon_goods')->where('coupon_no', $row->c_no)->select('goods_no')->get()->toArray();
			$row->goods_nos = array_map(function ($no) { return $no->goods_no; }, $goods_nos);
			$ex_goods_nos = DB::table('coupon_goods_ex')->where('coupon_no', $row->c_no)->select('goods_no')->get()->toArray();
			$row->ex_goods_nos = array_map(function ($no) { return $no->goods_no; }, $ex_goods_nos);
		}

        return response()->json(['code' => '200', 'body' => $result], 200);
    }

    /** 예약판매상품 지급완료처리 (예약주문건 정상주문처리) */
    public function complete_reservation(Request $request)
    {
        $ord_no = $request->input('ord_no', '');
        $ord_opt_no = $request->input('ord_opt_no', '');
        $ord_type = 15; // 정상:15

        $code = 200;
        $msg = '';

        try {
            DB::beginTransaction();

            $order = DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->first();
            $stock_amt = DB::table('product_stock_store')->where('store_cd', $order->store_cd)->where('prd_cd', $order->prd_cd)->value('wqty');

            if (is_null($stock_amt) || $stock_amt < 0) {
                $code = 404;
                throw new Exception('매장 보유재고가 0개 이상일 때만 예약상품지급이 가능합니다. 해당상품의 현재 보유재고는 ' . ($stock_amt ?? '-') . '개 입니다.');
            }

            DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->update([ 'ord_type' => $ord_type ]);
            DB::table('order_opt_wonga')->where('ord_opt_no', $ord_opt_no)->update([ 'ord_type' => $ord_type ]);

            $reservation_ord_cnt = DB::table('order_opt')->where('ord_no', $ord_no)->where('ord_type', 4)->count();
            if ($reservation_ord_cnt < 1) {
                DB::table('order_mst')->where('ord_no', $ord_no)->update([ 'ord_type' => $ord_type ]);
            }

            DB::commit();
            $msg = '예약판매상품이 지급완료처리되었습니다.';
        } catch (Exception $e) {
            DB::rollback();
            if ($code === 200) $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg], 200);
    }
}

