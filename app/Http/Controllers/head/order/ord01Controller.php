<?php

namespace App\Http\Controllers\head\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;
use App\Models\Product;
use App\Models\Order;
use App\Models\Claim;
use App\Models\Point;
use App\Models\Gift;
use App\Models\SMS;
use App\Models\Pay;
use Exception;

class ord01Controller extends Controller
{
    public function index(Request $req) {
        $mutable = now();
        $sdate	= $mutable->sub(1, 'week')->format('Y-m-d');

		$o = $req->input('o');
		$ismt= $req->input('ismt');

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'ord_states'    => SLib::getOrdStates(),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            'ord_kinds'     => SLib::getCodes('G_ORD_KIND'),
            'dlv_types'     => SLib::getCodes('G_DLV_TYPE'),
            'sale_places'   => SLib::getSalePlaces(),
            'items'         => SLib::getItems(),
            'goods_types'   => SLib::getCodes('G_GOODS_TYPE'),
            'clm_states'     => SLib::getCodes('G_CLM_STATE'),
            'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'),
			'o'				=> $o,
			'ismt'			=> $ismt
        ];

        return $o == 'pop'
            ? view( Config::get('shop.head.view') . '/order/ord01_pop', $values)
            : view( Config::get('shop.head.view') . '/order/ord01', $values);
    }

    public function show($ord_no, $ord_opt_no = '', Request $req) {

        $format     = $req->input('fmt','view');
        $refund_yn  = $req->input('refund_yn');

        if($ord_opt_no == ''){
            $sql = /** @lang text */
                "
				select max(ord_opt_no) as ord_opt_no
				from order_opt
				where ord_no = '$ord_no'
			";
            $row = DB::selectOne($sql);
            $ord_opt_no = $row->ord_opt_no;
        }

        $values = $this->_get($ord_no,$ord_opt_no);

        $sql = /** @lang text */
            "
            select code_val id, code_val val
            from code
            where code_kind_cd = 'G_JAEGO_REASON'
                and use_yn = 'Y'
            order by code_seq asc
        ";
        $values['jaego_reasons'] = DB::select($sql);

        $values = array_merge($values,[
            'cs_forms'		=> SLib::getCodes("CS_FORM2"),
            'clm_reasons'	=> SLib::getCodes("G_CLM_REASON"),
            'clm_states'	=> SLib::getCodes("G_CLM_STATE"),
            'dlv_cds'		=> SLib::getCodes("DELIVERY"),
			'refund_yn'		=> $refund_yn
        ]);
        
        if($format == "json"){
            return response()->json($values);
        } else {
            return view( Config::get('shop.head.view') . '/order/ord01_show',$values);
        }
    }

    public function get($ord_no, $ord_opt_no = '', Request $req) {
        $values = $this->_get($ord_no,$ord_opt_no);
        return response()->json($values);
    }

    private function _get($ord_no, $ord_opt_no = '') {

        // 설정 값 얻기
        $conf = new Conf();

        $cfg_shop_name			= $conf->getConfigValue("shop","name");
        $cfg_sms_yn				= $conf->getConfigValue("sms","sms_yn");
        $cfg_delivery_yn		= $conf->getConfigValue("sms","delivery_yn");
        $cfg_domain_bizest		= $conf->getConfigValue("shop","domain_bizest");
        $cfg_img_size_detail	= SLib::getCodesValue("G_IMG_SIZE","detail");
        $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");
        $cfg_bank_code			= SLib::getCodes("G_BANK_CODE");

        // 현금영수증 사용여부 설정값 얻기
        $cfg_cash_use_yn		= $conf->getConfigValue("shop","cash_use_yn", "N");
        $isfind = false;

        $values = [
            'ord_no' => $ord_no,
            'ord_opt_no' => $ord_opt_no,
        ];

        if($ord_opt_no == ''){
            $sql = /** @lang text */
                "
				select max(ord_opt_no) as ord_opt_no
				from order_opt
				where ord_no = '$ord_no'
			";
            $row = DB::selectOne($sql);
            $ord_opt_no = $row->ord_opt_no;
        }

        if ($ord_opt_no != "") {
            ###################################################################
            #	기본 주문정보
            ###################################################################
            $sql = " /* admin : order/ord01.php (1) */
                select
                    a.ord_no,date_format(b.ord_date,'%Y.%m.%d %H:%i:%s') ord_date, a.ord_kind, b.user_id, b.user_nm, b.phone, b.mobile, b.email
                    , b.r_nm, b.r_phone, b.r_mobile, b.r_zipcode, b.r_addr1, b.r_addr2
                    , b.dlv_msg, a.com_id, b.url, a.ord_state, ord_state.code_val ord_state_nm, a.ord_type, a.dlv_no /*송장번호*/
                    , c.code_val dlv_cd /*택배사*/,c.code_val2 dlv_homepage
                    , date_format(b.dlv_end_date,'%Y.%m.%d %H:%i:%s') mst_dlv_end_date, d.com_nm sale_place, b.sale_place sale_place_nm
                    , 0 as tax, b.dlv_amt, b.add_dlv_fee, a.add_point
                    , date_format(a.dlv_start_date,'%Y.%m.%d %H:%i:%s') dlv_start_date
                    , date_format(a.dlv_proc_date,'%Y.%m.%d %H:%i:%s') dlv_proc_date
                    , date_format(a.dlv_end_date,'%Y.%m.%d %H:%i:%s') dlv_end_date
                    , date_format(b.upd_date,'%Y.%m.%d %H:%i:%s') upd_date, a.dlv_comment, a.p_ord_opt_no
                    , company.com_type, com_type.code_val com_type_nm, company.com_nm, company.staff_nm1, company.last_login_date
                    , company.staff_phone1, company.staff_hp1
                    , company.zip_code, company.addr1, company.addr2
                    , company.r_zip_code as com_r_zip_code, company.r_addr1 as com_r_addr1, company.r_addr2  as com_r_addr2
                    , company.md_nm, company.memo as com_memo, a.price, a.dlv_pay_type
                    , m.memo as member_memo, 'Y' as taxpayer_yn,mu.name as seller
                    , ifnull(mbl.black_yn, 'N') as black_yn
                    , ifnull(mbl.black_reason, '') as black_reason
                from order_opt a
                    inner join order_mst b on a.ord_no = b.ord_no
                    left outer join code c on c.code_kind_cd = 'DELIVERY' and a.dlv_cd = c.code_id
                    left outer join company d on a.sale_place = d.com_id and d.com_type= '4'
                    left outer join company company on a.com_id = company.com_id
                    left outer join code com_type on com_type.code_kind_cd = 'G_COM_TYPE' and company.com_type = com_type.code_id
                    left outer join code ord_state on ord_state.code_kind_cd = 'G_ORD_STATE' and a.ord_state = ord_state.code_id
                    left outer join member m on b.user_id = m.user_id
                    left outer join member_black_list mbl on b.user_id = mbl.user_id
                    left outer join mgr_user mu on b.admin_id = mu.id
                where a.ord_opt_no = :ord_opt_no
            ";
            $isfind = true;
            $row = DB::selectOne($sql,array("ord_opt_no" => $ord_opt_no));

            $row->dlv_pay_type = $row->dlv_pay_type == "P" ? "선불" : "착불";
            $values['ord'] = $row;

            if($ord_no === "ord_no"){
                $ord_no = $row->ord_no;
                $values["ord_no"] = $ord_no;
            }
        }

        // 현금영수증 발행 내역
        if($cfg_cash_use_yn == "Y"){
            $sql = "
				select *
				from cash_history
				where ord_no = '$ord_no'
				order by rt desc
            ";
            $rows = DB::select($sql);
            $values['cash_history_cnt'] = count($rows);
            $values['cash_histories'] = $rows;
        }

        if ($isfind === false) return false;

        ###################################################################
        #	회원그룹 정보
        ###################################################################
        if (isset($values['ord']) && !empty($values['ord']->user_id) ) {
            $user_id = $values['ord']->user_id;

            $sql = /** @lang text */
                "
                select
                    a.group_no, b.group_nm
                from user_group_member a
                    inner join user_group b on a.group_no = b.group_no
                where a.user_id = '$user_id'
                order by b.dc_ratio desc, b.point_ratio desc
                limit 0,1
            ";
            $values['group'] = DB::selectOne($sql);
        }

        ###################################################################
        #	부모 주문건
        ###################################################################
        if(isset($values['ord']) && !empty($values['ord']->p_ord_opt_no)) {
            $p_ord_opt_no = $values['ord']->p_ord_opt_no;

            $sql = " /* admin : order/ord01_detail.php (2) */
                select ord_no from order_opt where ord_opt_no = '$p_ord_opt_no' order by ord_date desc
            ";
            $row = DB::selectOne($sql);

            if (!empty($row->ord_no)) $values['p_ord_no'] = $row->ord_no;
        }
        ###################################################################
        #	자식 주문건
        ###################################################################
        $sql = "select ord_no, ord_opt_no from order_opt where p_ord_opt_no = '$ord_opt_no'";

        $row = DB::selectOne($sql);

        if (!empty($row->ord_no)) {
            $values['c_ord_no'] = $row->ord_no;
            $values['c_ord_opt_no'] = $row->ord_opt_no;
        }

        ###################################################################
        #	자식 주문건
        ###################################################################
        $sql = /** @lang text */
            "
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
                , o.point_amt, o.coupon_amt, o.dc_amt, o.dlv_amt, o.recv_amt
                , c.refund_amt, o.add_point
                , g.is_unlimited, g.goods_type
                , o.opt_amt, o.addopt_amt, o.dlv_comment
                ,( select point_status from point_list where ord_no = o.ord_no and ord_opt_no = o.ord_opt_no and point > 0 order by no desc limit 0,1 ) as point_status
                , om.state, om.memo
                , o.dlv_cd, o.dlv_no, dlv.code_val as dlv_nm, dlv.code_val2 as dlv_homepage
                , '' as choice_class
            from order_opt o
                inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
                inner join company cm on o.com_id = cm.com_id
                left outer join claim c on o.ord_opt_no = c.ord_opt_no
                left outer join code ord_type on ord_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = ord_type.code_id
                left outer join code ord_kind on ord_kind.code_kind_cd = 'G_ORD_KIND' and o.ord_kind = ord_kind.code_id
                left outer join order_opt_memo om on o.ord_opt_no = om.ord_opt_no
                left outer join code dlv on dlv.code_kind_cd = 'DELIVERY' and o.dlv_cd = dlv.code_id
            where o.ord_no = '$ord_no' and g.goods_type <> 'O'
            order by com_id, o.ord_opt_no desc
        ";

        $rows = DB::select($sql);

        $sum_amt = 0;
        $sum_qty = 0;
        $sum_dlv_amt = 0;
        $sum_coupon_amt = 0;
        $sum_refund_amt = 0;
        $sum_add_point = 0;

        $sum_claim_amt = 0;			// 취소금액
        $sum_normal_amt = 0;		// 유효금액

        $pcom_id = "";
        $pcom_idx = 0;
        $com_cnt = 1;
        $com_dlv_amt = 0;
        $dlv_comment_cnt = 0;

        $goods_no = "";
        $goods_sub = "";
        $choice_goods_type = "";

        foreach($rows as $row) {
            if($ord_opt_no == $row->ord_opt_no){
                $values['goods_no'] = $row->goods_no;
                $values['goods_sub'] = $row->goods_sub;
                $choice_goods_type = $row->goods_type;
                $row->choice_class = "choice";
            }

            $sum_amt		+= $row->qty * $row->price;
            $sum_qty		+= $row->qty;
            $sum_dlv_amt	+= $row->dlv_amt;
            $sum_coupon_amt	+= $row->coupon_amt + $row->dc_amt;
            $sum_refund_amt	+= $row->refund_amt;
            $sum_add_point	+= $row->add_point;

            if( $row->clm_state == 0 ){	// 클레임이 없는 경우에만 금액 가산
                $sum_normal_amt += $row->qty * $row->price;
            } else {
                $sum_claim_amt += $row->qty * $row->price;
                //$sum_claim_amt += $row->recv_amt;		// 클레임 금액은 상품가격에서 적립금, 쿠폰, 할인을 제외한
            }

            $sql2 = /** @lang text */
                "
                select addopt, addopt_amt, addopt_qty
                from order_opt_addopt
                where ord_opt_no = '$row->ord_opt_no'
            ";

            $row->a_addopts = DB::select($sql2);
            if($row->dlv_comment != ""){
                $dlv_comment_cnt++;
            }

            // 업체별 배송비 처리
            if($pcom_id != $row->com_id){
                $row->dlv_grp_amt = $com_dlv_amt;
                $com_dlv_amt = $row->dlv_amt;
            } else {
                $com_dlv_amt += $row->dlv_amt;
            }

            $pcom_id = $row->com_id;
        }

        $values['ord_lists'] = $rows;

        ###################################################################
        #	결제정보
        ###################################################################
        $sql = " /* admin : order/ord01_detail.php (7) */
            select
                a.pay_type, pay_type.code_val pay_type_nm, '' as fintech, a.pay_amt, a.pay_point, a.pay_nm,
                a.pay_stat, pay_stat.code_val pay_stat_nm, a.bank_inpnm, a.bank_code, a.bank_number,
                a.card_code, a.card_isscode,
                a.card_quota, a.card_appr_no,
                date_format(a.card_appr_dm,'%Y.%m.%d %H:%i:%s') card_appr_dm, a.card_tid, a.tno, a.card_msg,
                date_format(a.ord_dm,'%Y.%m.%d %H:%i:%s') ord_dm, date_format(a.upd_dm,'%Y.%m.%d %H:%i:%s') upd_dm,
                a.pay_ypoint, a.pay_baesong, a.card_name, a.nointf, a.ghost_use, a.escw_use, a.tno,
                a.st_cd, ifnull(a.coupon_amt,0) coupon_amt,
                cr.bank_code as cr_bank_code,
                ifnull(a.dc_amt,0) as dc_amt,
                ifnull(a.cash_yn, 'N') as cash_yn, a.cash_date,
                ifnull(a.tax_yn, 'N') as tax_yn, a.tax_date,
                a.confirm_id, ma.name as confirm_nm, a.confirm_amt,
                0 as pay_fee
            from payment a
                left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
                left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STATE')
                left outer join common_return cr on cr.order_no = a.ord_no
                left outer join mgr_user ma on a.confirm_id = ma.id
            where a.ord_no = '$ord_no'
        ";

        $row = DB::selectOne($sql);

        if(!empty($row->fintech)){
            $row->pay_type_nm = sprintf("%s(%s)", $row->pay_type_nm, strtoupper($row->fintech));
        }

        if(isset($row->cr_bank_code) && isset($cfg_bank_code[$row->cr_bank_code])){
            $row->bank_code = $cfg_bank_code[$row->cr_bank_code];
        }

        $values['pay'] = $row;
        ###################################################################
        #	해외 배송 정보
        ###################################################################
        if($choice_goods_type == "O") {
            $sql = /** @lang text */
                "
                select c.code_val as local_ord_state_nm, a.local_state_date, a.comment, a.admin_nm, a.admin_id
                from order_oversea_state a
                    inner join code c on a.local_ord_state = c.code_id  and c.code_kind_cd = 'G_ORD_STATE'
                where ord_opt_no = '$ord_opt_no'
                order by ord_state_no
            ";

            $values['oversea_states'] = DB::select($sql);
        }

        ###################################################################
        #	주문상태 정보
        ###################################################################
        if (isset($values['ord']) && $values['ord']->ord_state > 1) {
            $sql = "
                select
                    a.p_ord_state
                    , b.code_val as p_ord_state_nm
                    , a.ord_state
                    , c.code_val as ord_state_nm
                    , a.admin_id
                    , a.admin_nm
                    , date_format(a.state_date,'%Y.%m.%d %H:%i:%s') as state_date
                    , a.comment
                from order_state a
                    inner join code b on a.p_ord_state = b.code_id and b.code_kind_cd = 'G_ORD_STATE'
                    inner join code c on a.ord_state = c.code_id and c.code_kind_cd = 'G_ORD_STATE'
                where a.ord_opt_no = '$ord_opt_no'
                order by state_date asc
            ";

            $values['state_logs'] = DB::select($sql);
        }

        ###################################################################
        #	클레임 정보
        ###################################################################
        $claimInfoSql = " /* admin : order/ord01_detail.php (9) */
            select
                clm_no, clm_state
                , clm_reason, refund_yn, refund_amt, refund_bank, refund_account, refund_nm, memo
                , date_format(req_date,'%Y.%m.%d %H:%i:%s') as req_date
                , date_format(proc_date,'%Y.%m.%d %H:%i:%s') as proc_date
                , date_format(end_date,'%Y.%m.%d %H:%i:%s') as end_date
                , req_nm,proc_nm,end_nm,date_format(last_up_date,'%Y.%m.%d %H:%i:%s') as last_up_date
                , dlv_deduct
            from claim
            where
                ord_opt_no = '$ord_opt_no'
        ";

        $values['claim_info'] = DB::selectOne($claimInfoSql);
        $values['clm_state'] = empty($values['claim_info']->clm_state) ? 0 : $values['claim_info']->clm_state;
        $values['ord_state'] = empty($values['claim_info']->ord_state) ? 0 : $values['claim_info']->ord_state;

        ###################################################################
        #	클레임 대상 리스트
        ###################################################################
        $array_claim = array();


        $sql = "
            select
                o.goods_no, o.goods_sub, o.goods_sub, g.goods_type,
                o.qty, o.price, o.goods_nm, o.clm_state,
                d.clm_det_no, d.clm_qty, d.jaego_yn, d.jaego_reason, d.stock_state,
                'Y' as stocked_yn,
                if(ifnull(o.clm_state, 0) = 0
                    , (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = o.ord_state)
                    , (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = o.clm_state)
                 ) as order_state
                 , o.ord_state, o.clm_state
            from order_opt o
                inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
                left outer join claim c on o.ord_opt_no = c.ord_opt_no
                left outer join claim_detail d on c.clm_no = d.clm_no
            where
                o.ord_opt_no = '$ord_opt_no'
        ";

        $values['order_opt'] = DB::selectOne($sql);

        ###################################################################
        #	클레임 내역 리스트 변경 : CS유형, 주문상태, 클레임상태 추가
        ###################################################################
        $sql = "
            select
                a.memo_no, a.admin_id, a.admin_nm
                , date_format(a.regi_date, '%y.%m.%d %H:%i:%s') as regi_date, a.memo
                , cd.code_val as cs_form
                , cd2.code_val as ord_state
                , if(cd3.code_id is not null,cd3.code_val,cd2.code_val) as clm_state
                , a.ord_opt_no
                , '' as alt
            from claim_memo a
                inner join order_opt b on a.ord_opt_no = b.ord_opt_no
                left outer join code cd on cd.code_kind_cd = 'CS_FORM2' and cd.code_id = a.cs_form
                left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_STATE' and cd2.code_id = a.ord_state
                left outer join code cd3 on cd3.code_kind_cd = 'G_CLM_STATE' and cd3.code_id = a.clm_state
            where b.ord_no = '$ord_no'
            order by a.regi_date asc
        ";
        $rows = DB::select($sql);

        foreach($rows as $idx => $row) {
            $row->memo = str_replace("\n","<br>",$row->memo);

            if( $ord_opt_no == $row->ord_opt_no ) {
                $choice_class	= "choice";
            }

            $row->alt = ($idx % 2 == 1) ? "alt" : "";
        }

        $values['claim_memos'] = $rows;

        if (isset($values['ord']) && $values['ord']->com_type == 2) {
            $sql = " /* admin : order/ord01_detail.php (12) */
                select
                     etc_day,etc_amt, etc_memo, regi_date, admin_nm, '' as alt
                from account_etc where ord_opt_no = '$ord_opt_no'
            ";

            $rows = DB::select($sql);

            foreach($rows as $idx => $row) {
                $row->alt = ($idx % 2 == 1) ? "alt" : "";
            }

            $values['account_etcs'] = $rows;
        }

        ###################################################################
        #	유입정보
        ###################################################################
        $sql = "
            select
                ifnull(t.vt,0) as vt, t.vc,datediff(t.rt,t.lvd) as vp, t.pageview, t.referer,
                e.code_val as type,	a.name, t.kw, t.point  , t.ad, o.referrer as track,
                t.browser, t.domain, t.agent, m.mobile_yn
            from order_track t
                left join ad a on a.ad= t.ad
                left outer join order_mst m on m.ord_no = t.ord_no
                left outer join order_opt o on o.ord_no = t.ord_no
                left outer join code e on e.code_id = a.type and e.code_kind_cd = 'G_AD_TYPE'
            where o.ord_opt_no = '$ord_opt_no'
        ";

        $row = DB::selectOne($sql);

        if (!empty($row->vt)){
            $row->visit_time = sprintf("%02d:%02d",floor($row->vt/60), $row->vt % 60);
        }

        $values['track'] = $row;

        ###################################################################
        #	사은품 정보
        ###################################################################
        $sql = "
            select a.no, a.ord_no, a.ord_opt_no,
                ifnull(a.give_yn, 'N') as give_yn,
                ifnull(a.give_date, '') as give_date,
                ifnull(a.refund_no, '0') as refund_no,
                ifnull(a.refund_yn, 'N') as refund_yn,
                ifnull(a.refund_date, '') as refund_date,
                a.admin_id, a.admin_nm, a.rt, a.ut,
                b.no as gift_no, b.name,
                ifnull(cd.code_val, '') as type_val,
                ifnull(cd2.code_val, '') as kind_val,
                b.type, b.kind, b.img, b.apply_amt,
                g.goods_no, g.goods_sub, g.goods_nm,
                '' as choice_class
            from order_gift a
                inner join gift b on a.gift_no = b.no
                inner join order_opt c on c.ord_opt_no = a.ord_opt_no
                inner join goods g on g.goods_no = c.goods_no and g.goods_sub = c.goods_sub
                left outer join code cd on cd.code_kind_cd = 'G_GIFT_TYPE' and cd.code_id = b.type
                left outer join code cd2 on cd2.code_kind_cd = 'G_GIFT_KIND' and cd2.code_id = b.kind
            where a.ord_no = '$ord_no'
            order by a.ord_opt_no desc
        ";
        $rows = DB::select($sql);

        foreach($rows as $row){
            if( $row->kind == "P" && $ord_opt_no == $row->ord_opt_no ) {
                $row->choice_class	= "choice";
            }

            $row->goods_snm = mb_substr($row->goods_nm, 0, 28);
        }

        $values['gifts'] = $rows;

        // dd($values);

        return $values;

        // dd(DB::select($claimInfoSql));
        // dd(SLib::getCodes("G_CLM_STATE"));
        // $values = [
        //     'ord' => DB::selectOne($initSql),
        //     'track' => DB::selectOne($trackSql),
        //     'pay' => DB::selectOne($paySql),
        //     'state_logs' => DB::select($stateSql),
        //     'claim_info' => $claim_info,
        //     'jaego_reasons' => DB::select($jaegoSql),
        //     'claim_memos' => DB::select($claimMemoSql),
        //     'order_opt' => DB::selectOne($orderOpt),
        //     'ord_lists' => DB::select($orderListSql),
        //     'clm_state' => empty($claim_info->clm_state) ? 0 : $claim_info->clm_state
        // ];
        
    }

    public function refund($ord_no, $ord_opt_no, Request $req) {
		// 설정 값 얻기
        $conf = new Conf();
		$cfg_shop_name				= $conf->getConfigValue("shop","name");
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
        $cfg_add_dlv_fee			= $conf->getConfigValue("delivery","add_delivery_fee");

        $IsGroupDlv = true;

		// 환불계좌 입력 여부
        $isrefund_bank = "Y";

		$refund_bank = "";
		$refund_account = "";
		$refund_depositor = "";
        $p_ord_opt_no = "";
        $refunded_amt = 0;
        $pgcancelstate = "";
		// 주문 건수
        $sql = "select count(*) as cnt from order_opt where ord_no = '$ord_no'";

        $row = DB::selectOne($sql);

		$ord_cnt = $row->cnt;

		// 주문 & 입금정보
		$ordSql = "
            select
                a.ord_state,a.ord_amt, a.add_dlv_fee,
                b.pay_type, b.pay_nm, b.pay_amt, b.pay_point, b.pay_baesong, b.coupon_amt, b.dc_amt, 0 as pay_fee,
                b.bank_inpnm, bank_code, bank_number, tno, card_name, c.code_val as pay_name,
                b.escw_use,
                b.refund_bank, b.refund_account, b.refund_depositor
            from order_mst a
                inner join payment b on a.ord_no = b.ord_no
                left outer join code c on c.code_kind_cd = 'G_PAY_TYPE' and b.pay_type = c.code_id
            where a.ord_no = '$ord_no'
        ";

        $ord = DB::selectOne($ordSql);

        if (!empty($ord->ord_state)) {
            $ord_state = $ord->ord_state;
            $ord_amt = $ord->ord_amt;
            $add_dlv_fee = $ord->add_dlv_fee;

            $pay_amt = $ord->pay_amt;
            $pay_point = $ord->pay_point;
            $pay_baesong = $ord->pay_baesong;
            $coupon_amt = $ord->coupon_amt;
            $dc_amt = $ord->dc_amt;
            $pay_fee = $ord->pay_fee;

            $pay_type = $ord->pay_type;
            $pay_name = $ord->pay_name;
            $pay_nm = $ord->pay_nm;
            $card_name = $ord->card_name;
            $tno = $ord->tno;

            $pg = new Pay();
            $pgcancelstate = $pg->cancelstate($ord->pay_type, $ord->tno, $ord->card_name);

            if(($pay_type & 2) == 2){	// 카드
                $isrefund_bank = "N";
                $refund_bank = "";
                $refund_account = "";
            } else if(($pay_type & 16) == 16){	// 계좌이체 : PG 거래번호를 기본값으로 출력
                if($pgcancelstate > 0){
                    $isrefund_bank = "N";
                }

                $refund_bank = $ord->bank_code;
            } else if(($pay_type & 1) == 1 || ($pay_type & 64) == 64){	// 무통장 OR 가상계좌(에스크로)
                $refund_bank = $ord->refund_bank;
                $refund_account = $ord->refund_account;
                $pay_nm = $ord->refund_depositor;
            }
        } else {
			// 주문 정보 또는 입금 정보가 없는 경우는 에러처리!!!
        }

		//
		//	환불정보는 공유
		//

		$refund_no = "";

		$sql = "
			select refund_no
			from claim
			where ord_opt_no = '$ord_opt_no'
        ";

        $row = DB::selectOne($sql);

		if(!empty($row->refund_no)) {
			$refund_no = $row->refund_no;
		}

		// 환불정보
		$refundSql = "
			select
				clm_state,refund_price,refund_dlv_amt, refund_dlv_ret_amt, refund_dlv_enc_amt, refund_dlv_pay_amt,
				refund_point_amt, refund_coupon_amt, refund_etc_amt, 0 as refund_pay_fee, refund_gift_amt, refund_amt,
				refund_bank, refund_account, refund_nm, '' as refund_pay_fee_yn
			from claim a
			where a.ord_opt_no = '$refund_no'
        ";

		// 환불금액
		if($tno != ""){
			$sql = "
				select sum(ifnull(c.refund_amt,0)) as refunded_amt
				from (
					select ord_no
					from payment where tno = '$tno' and ord_no <> ''
				) a inner join order_opt o on a.ord_no = o.ord_no
					inner join claim c on o.ord_opt_no = c.ord_opt_no
				where c.clm_state = 61
			";
		} else {
			$sql = "
				select
					sum(ifnull(d.refund_amt,0)) as refunded_amt
				from order_opt a inner join claim d on a.ord_opt_no = d.ord_opt_no
				where a.ord_no = '$ord_no'
			";
        }

        $row = DB::selectOne($sql);

        if (!empty($row->refunded_amt)) {
            $refunded_amt = $row->refunded_amt;
        }

		// 그룹 주문
        $sum_dlv_amt = 0;

		if($IsGroupDlv){
			$sql = "
				select
					if(g.com_type = 1, g.com_type, a.com_id) as com_id,
					count(*) as cnt,
					sum(a.dlv_amt) as dlv_amt,
					sum(ifnull(c.dlv_add_amt,0)) as dlv_add_amt
				from order_opt a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
						and g.goods_type <> 'O'
					left outer join claim c on a.ord_opt_no = c.ord_opt_no
				where a.ord_no = '$ord_no'
				group by if(g.com_type = 1, g.com_type, a.com_id)
			";
			$rows = DB::select($sql);

			$group_dlv = array();

			foreach ($rows as $row) {
				$group_dlv[$row->com_id]["cnt"] = $row->cnt;
				$group_dlv[$row->com_id]["dlv_amt"] = $row->dlv_amt;
				$group_dlv[$row->com_id]["dlv_add_amt"] = $row->dlv_add_amt;
				$sum_dlv_amt += $row->dlv_amt;
			}
		} else {

			$sql = "
				select
					count(*) as cnt,
					sum(a.dlv_amt) as dlv_amt,
					sum(ifnull(c.dlv_add_amt,0)) as dlv_add_amt
				from order_opt a
					inner join goods g
						on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub and g.goods_type <> 'O'
					left outer join claim c on a.ord_opt_no = c.ord_opt_no
				where a.ord_no = '$ord_no'
            ";
            $row = DB::selectOne($sql);

			$group_dlv = array();
			$group_dlv["1"]["cnt"] = $ord_cnt;
			$group_dlv["1"]["dlv_amt"] = $pay_baesong;
			$group_dlv["1"]["dlv_add_amt"] = $row->dlv_add_amt;
		}

		// 주문 상품
		$sql = "
            select
                a.ord_opt_no, a.p_ord_opt_no, a.ord_state, a.clm_state,
                if(ifnull(a.clm_state,0) = 0,
                    (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = a.ord_state),
                    (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = a.clm_state)
                ) as state,
                if(g.com_type = 1, g.com_type, a.com_id) as com_id,
                if(g.com_type = 1, '$cfg_shop_name',e.com_nm) as com_nm,
                a.goods_nm,
                replace(a.goods_opt, '^',':') as opt_nm,
                a.price, a.qty, a.price * a.qty as amt,
                a.coupon_amt , a.dc_amt, 0 as pay_fee,
                ifnull(a.dlv_amt, 0) as dlv_amt,
                ifnull(d.dlv_type, '') as clm_clm_dlv_type,
                ifnull(d.dlv_cm, '') as clm_dlv_cm,
                ifnull(d.dlv_amt, '') as clm_dlv_amt,
                ifnull(d.dlv_ret_amt, '') as clm_dlv_ret_amt,
                ifnull(d.dlv_add_amt, '') as clm_dlv_add_amt,
                ifnull(d.dlv_enc_amt, '') as clm_dlv_enc_amt,
                ifnull(d.dlv_pay_amt, '') as clm_dlv_pay_amt,
                ifnull(d.ref_amt, 0) as ref_amt,
                ifnull(d.refund_no, 0) as refund_no,
                ifnull(d.refund_amt, '') as refund_amt,
                g.goods_type,
                ifnull(e.dlv_policy,'S') as com_dlv_policy,
                ifnull(e.dlv_amt, 0) as com_dlv_amt,
                ifnull(e.free_dlv_amt_limit, 0) as com_free_dlv_amt_limit
            from order_opt a
                inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
                inner join company e on a.com_id = e.com_id
                left outer join coupon b on a.coupon_no = b.coupon_no
                left outer join claim d on a.ord_opt_no = d.ord_opt_no
            where a.ord_no = '$ord_no'
            order by com_id,ord_opt_no desc
        ";

        $rows = DB::select($sql);
		$prds = array();
        $pre_com_id = "";
        $s_prd = null;
        foreach($rows as $row) {
            $class = "";

			if($row->ord_opt_no == $ord_opt_no){
				$class ="choice";
                $p_ord_opt_no = $row->p_ord_opt_no;
                $s_prd = $row;
			}

			// 배송비 및 열수
			if($IsGroupDlv){
				$com_id = $row->com_id;

				if($sum_dlv_amt == 0 && $pay_baesong > 0){
					$dlv_amt = $pay_baesong;
				} else {
					//$dlv_amt = $row->dlv_amt;
					$dlv_amt = $group_dlv[$com_id]["dlv_amt"]; // 2008-07-18 : 그룹 배송 처
				}
				$dlv_grp_amt = $group_dlv[$com_id]["dlv_amt"];
				$dlv_grp_add_amt = $group_dlv[$com_id]["dlv_add_amt"];
				$dlv_grp_cnt = ($pre_com_id != $row->com_id)? $group_dlv[$com_id]["cnt"]:"";
			} else {
				$com_id = "1";
				$dlv_amt = $pay_baesong;
				$dlv_grp_amt = $group_dlv[$com_id]["dlv_amt"];
				$dlv_grp_add_amt = $group_dlv[$com_id]["dlv_add_amt"];
				$dlv_grp_cnt = $ord_cnt;
			}

			array_push($prds,
				array(
					"class"				=> $class,
					"refund_no"			=> $row->refund_no,
					"ord_opt_no"		=> $row->ord_opt_no,
					"ord_state"			=> $row->ord_state,
					"clm_state"	 		=> $row->clm_state,
					"state"				=> $row->state,
					"com_id"			=> $com_id,
					"com_nm"			=> $row->com_nm,
					"goods_nm"			=> $row->goods_nm,
					"goods_snm"			=> $row->goods_nm,
					"opt_nm"			=> $row->opt_nm,
					"price"				=> $row->price,
					"qty"				=> $row->qty,
					"amt"				=> $row->amt,
					"dc_amt"			=> $row->dc_amt,
					"refunded_amt"		=> $refunded_amt,
					"pay_fee"			=> $row->pay_fee,
					// "coupon_amt"		=> $row->coupon_amt+$row->dc_amt,
					"coupon_amt"		=> $row->coupon_amt,
					"dlv_amt"			=> $dlv_amt,
					"dlv_grp_cnt"		=> $dlv_grp_cnt,
					"dlv_grp_amt"		=> $dlv_grp_amt,
					"dlv_grp_add_amt"	=> $dlv_grp_add_amt,
					"clm_clm_dlv_type"		=> $row->clm_clm_dlv_type,
					"clm_dlv_cm"		=> $row->clm_dlv_cm,
					"clm_dlv_amt"		=> $row->clm_dlv_amt,
					"clm_dlv_ret_amt"	=> $row->clm_dlv_ret_amt,
					"clm_dlv_add_amt"	=> $row->clm_dlv_add_amt,
					"clm_dlv_enc_amt"	=> $row->clm_dlv_enc_amt,
					"clm_dlv_pay_amt"	=> $row->clm_dlv_pay_amt,
					"ref_amt"			=> $row->ref_amt,
					"goods_type"		=> $row->goods_type,
					"com_dlv_policy"	=> $row->com_dlv_policy,
					"com_dlv_amt"		=> $row->com_dlv_amt,
                    "com_dlv_amt_free_limit"	=> $row->com_free_dlv_amt_limit,
                    "refund_amt"        => $row->refund_amt
				)
			);

			$pre_com_id = $com_id;
        }

		###################################################################
		#	사은품 정보
        ###################################################################
		$array_gift = array();
		$sql = "
			select a.no, a.ord_no, a.ord_opt_no,
				ifnull(a.give_yn, 'N') as give_yn,
				ifnull(a.give_date, '') as give_date,
				ifnull(a.refund_no, '0') as refund_no,
				ifnull(a.refund_yn, 'N') as refund_yn,
				ifnull(a.refund_amt, '0') as refund_amt,
				ifnull(a.refund_date, '') as refund_date,
				a.admin_id, a.admin_nm, a.rt, a.ut,
				b.no as gift_no, b.name, b.type, b.kind, b.refund_yn as g_refund_yn,
				ifnull(cd.code_val, '') as type_val,
				ifnull(cd2.code_val, '') as kind_val,
				b.img, b.apply_amt, 0 as gift_price,
				g.goods_no, g.goods_sub, g.goods_nm
			from order_gift a
				inner join gift b on a.gift_no = b.no
				inner join order_opt c on c.ord_opt_no = a.ord_opt_no
				inner join goods g on g.goods_no = c.goods_no and g.goods_sub = c.goods_sub
				left outer join code cd on cd.code_kind_cd = 'G_GIFT_TYPE' and cd.code_id = b.type
				left outer join code cd2 on cd2.code_kind_cd = 'G_GIFT_KIND' and cd2.code_id = b.kind
			where a.ord_no = '$ord_no'
			order by b.kind desc, b.apply_amt desc, a.ord_opt_no desc
        ";

        $rows = DB::select($sql);

		foreach($rows as $row){
			$order_gift_no		= $row->no;
			$gift_no			= $row->gift_no;
			$gift_nm			= $row->name;
			$gift_type			= $row->type;
			$gift_type_val		= $row->type_val;
			$gift_kind			= $row->kind;
			$gift_kind_val		= $row->kind_val;
			$gift_img			= $row->img;
			$gift_apply_amt		= $row->apply_amt;
			$g_refund_yn		= $row->g_refund_yn;
			$gift_give_yn		= $row->give_yn;
			$gift_give_date		= $row->give_date;
			$gift_refund_no		= $row->refund_no;
			$gift_refund_yn		= $row->refund_yn;
			$gift_refund_amt	= $row->refund_amt;
			$gift_refund_date	= $row->refund_date;
			$gift_goods_no		= $row->goods_no;
			$gift_goods_sub		= $row->goods_sub;
			$gift_goods_nm		= $row->goods_nm;
			$gift_price			= $row->gift_price;

			$_ord_opt_no = $row->ord_opt_no;

			$choice_class = "";
			if( $gift_kind == "P" && $ord_opt_no == $_ord_opt_no ) {
				$choice_class	= "choice";
			}

			array_push($array_gift, array(
				"order_gift_no"	=> $order_gift_no,
				"gift_no"		=> $gift_no,
				"name"			=> $gift_nm,
				"type"			=> $gift_type,
				"type_val"		=> $gift_type_val,
				"kind"			=> $gift_kind,
				"kind_val"		=> $gift_kind_val,
				"img"			=> $gift_img,
				"apply_amt"		=> $gift_apply_amt,
				"g_refund_yn"	=> $g_refund_yn,
				"give_yn"		=> $gift_give_yn,
				"give_date"		=> $gift_give_date,
				"refund_no"		=> $gift_refund_no,
				"refund_yn"		=> $gift_refund_yn,
				"refund_amt"	=> $gift_refund_amt,
				"refund_date"	=> $gift_refund_date,
				"ord_opt_no"	=> $_ord_opt_no,
				"ord_no"		=> $ord_no,
				"goods_no"		=> $gift_goods_no,
				"goods_sub"		=> $gift_goods_sub,
				"goods_nm"		=> $gift_goods_nm,
				"goods_snm"		=> $gift_goods_nm,
				"choice_class"	=> $choice_class,
				"gift_price"	=> $gift_price
			));
        }

        $values = [
            "g_dlv_fee"             => Lib::CheckInt($cfg_dlv_fee),
            "g_dlv_add_fee"         => Lib::CheckInt($cfg_add_dlv_fee),
            "g_free_dlv_fee_limit"  => Lib::CheckInt($cfg_free_dlv_fee_limit),

            'ord_no'		=> $ord_no,
            'ord_opt_no'	=> $ord_opt_no,
            'refund_no'		=> $refund_no,
            'ord'			=> $ord,
            'refund'		=> DB::selectOne($refundSql),
            'group_dlv'		=> $group_dlv,
            'prds'			=> $prds,
            'gifts'			=> $array_gift,
            'p_ord_opt_no'	=> $p_ord_opt_no,
            'ord_cnt'		=> $ord_cnt,
            'refunded_amt'	=> $refunded_amt,
            's_prd'			=> $s_prd,
            'refund_bank'	=> $refund_bank,
            'refund_account'=> $refund_account,
            'pay_nm'		=> $pay_nm,
            'pgcancelstate'	=> $pgcancelstate,
            'isrefund_bank'	=> $isrefund_bank,
            'escw_show'		=> ( ($ord->escw_use == "O" || $ord->escw_use == "Y") && $ord->pay_amt >= 100000 ) ? "" : "none"
        ];

        // dd($values);
        return view( Config::get('shop.head.view') . '/order/ord01_refund',$values);
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
        return view( Config::get('shop.head.view') . '/order/ord01_dlv',$values);
    }

    public function receipt($ord_no, Request $req) {
		$pre_ord_no = $req->input("pre_ord_no", "");
		$order_mst = "order_mst";
		$order_opt = "order_opt";
		$payment = "payment";

		if($pre_ord_no != ""){
			$ord_no = $pre_ord_no;
			$order_mst = "pre_order_mst";
			$order_opt = "pre_order_opt";
			$payment = "pre_payment";
        }

		// 주문정보 얻기 - 실주문 / 수기판매(MD)
		$sql = "
            select
                a.user_id, a.user_nm, a.phone, a.mobile, a.email
                , a.r_nm, a.r_phone, a.r_mobile, a.r_zipcode, a.r_addr1, a.r_addr2, a.dlv_msg
                , c.com_nm sale_place, b.bank_inpnm, b.bank_code, b.bank_number
                , a.point_amt, a.coupon_amt, a.ord_amt, a.dlv_amt, a.recv_amt
                , cd.code_val as ord_type_nm
                , cd2.code_val as ord_kind_nm
                , cd3.code_val as pay_type_nm
                , cd4.code_val as ord_state_nm
            from $order_mst a
                left outer join $payment b on a.ord_no = b.ord_no
                left outer join company c on a.sale_place = c.com_id and c.com_type='4'
                left outer join code cd on cd.code_kind_cd = 'G_ORD_TYPE' and cd.code_id = a.ord_type
                inner join code cd2 on cd2.code_kind_cd = 'G_ORD_KIND'  and cd2.code_id = a.ord_kind
                left outer join code cd3 on cd3.code_kind_cd = 'G_PAY_TYPE'  and cd3.code_id = b.pay_type
                left outer join code cd4 on cd4.code_kind_cd = 'G_ORD_STATE'  and cd4.code_id = a.ord_state
            where a.ord_no = '$ord_no'
        ";
        $ord = DB::selectOne($sql);

        // 등록된 상품 얻기
        $sql = "
            select
                a.ord_opt_no,c.goods_nm, c.goods_no, c.goods_sub, a.goods_opt, c.style_no, c.goods_nm
                , a.qty, a.price, a.point_amt, a.coupon_amt,a.recv_amt, b.dlv_amt
                , replace(a.goods_opt, '^', ' : ') as opt_val
                , cd.code_val as ord_state, c.opt_kind_cd
                , a.price * a.qty as ord_amt
            from $order_opt a
                inner join $order_mst b on a.ord_no = b.ord_no
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                inner join code cd on cd.code_kind_cd = 'G_ORD_STATE' and cd.code_id = a.ord_state
            where a.ord_no = '$ord_no'
        ";

        $opts = DB::select($sql);
        $tot_ord_amt = 0;

        foreach($opts as $opt) {
            $tot_ord_amt += $opt->ord_amt;
        }

        $tot_recv_amt = $tot_ord_amt + $ord->dlv_amt - $ord->point_amt - $ord->coupon_amt;

        $values = [
            'ord_no' => $ord_no,
            'ord' => $ord,
            'opts' => $opts,
            'tot_ord_amt' => $tot_ord_amt,
            'tot_recv_amt' => $tot_recv_amt
        ];

        // dd($values);
        return view( Config::get('shop.head.view') . '/order/ord01_receipt',$values);
    }

    public function search($cmd, Request $req) {
        // 설정 값 얻기
        $conf = new Conf();

        $cfg_img_size_list		= SLib::getCodesValue('G_IMG_SIZE', 'list');
		$cfg_img_size_real		= SLib::getCodesValue('G_IMG_SIZE', 'list');
        $cfg_domain_img			= $conf->getConfigValue("shop","domain_img");

		// if($cfg_domain_img == ""){
		// 	$cfg_domain_img = $_SERVER["HTTP_HOST"];
        // }
		// $goods_img_url = sprintf("http://%s",$cfg_domain_img);
        $goods_img_url = '';

		$page       = $req->input("page",1);
		$page_size  = $req->input("limit", 100);

		if ($page < 1 or $page == "") $page = 1;

		$edate          = $req->input("edate", date("Ymd"));
		$sdate          = $req->input("sdate", now()->sub(3, 'month')->format('Ymd'));
		$ord_no         = $req->input("ord_no", "");
		$user_nm        = $req->input("user_nm", "");
		$user_id        = $req->input("user_id", "");
		$goods_no       = $req->input("goods_no", "");
		$style_no       = $req->input("style_no", "");
		$r_nm           = $req->input("r_nm", "");
		$bank_inpnm     = $req->input("bank_inpnm", "");
		$stat_pay_type  = $req->input("stat_pay_type", "");
		$ord_state      = $req->input("ord_state", "");
		$clm_state      = $req->input("clm_state", "");
		$clm_stock_check = $req->input("clm_stock_check", "");
		$dlv_no         = $req->input("dlv_no", "");
		$sale_place     = $req->input("sale_place", "");
		$com_type       = $req->input("com_type", "");
		$com_id         = $req->input("com_id", "");
		$out_ord_no     = $req->input("out_ord_no", "");
		$cols           = $req->input("cols", "");
		$baesong_kind   = $req->input("baesong_kind", "");
		$ord_type       = $req->input("ord_type", "");
		$ord_kind       = $req->input("ord_kind", "");
		$goods_type     = $req->input("goods_type", "");
		$brand_cd       = $req->input("s_brand_cd", "");
		$brand_nm       = $req->input("brand_nm", "");
		$goods_nm       = $req->input("goods_nm", "");
		$head_desc      = $req->input("head_desc", "");
		$limit          = $req->input("limit", 100);
		$ord_field      = $req->input("ord_field","a.ord_no");
		$ord            = $req->input("ord","desc");
		$opt_kind_cd    = $req->input("item", "");
		$not_complex    = $req->input("not_complex", "");
		$baesong_info   = $req->input("baesong_info", "");
		$special_yn     = $req->input("special_yn", "");
		$key            = $req->input("key", "");
		$nud            = $req->input("s_nud", "Y");
		$pay_nm         = $req->input("pay_nm", "");
		$pay_stat       = $req->input("pay_stat", "");
		$goods          = $req->input("goods", "");		// 상품선택
		$mobile_yn      = $req->input("mobile_yn", "");	// 모바일 주문 여부
		$app_yn         = $req->input("app_yn", "");    // 앱 주문 여부
		$receipt        = $req->input("receipt", "N");	// 현금영수증 : N(미신청), R(신청), Y(발행)
		$dlv_type       = $req->input("dlv_type", "");	// 배송방식: D(택배), T(택배(당일배송)), G(직접수령)
		$pay_fee        = $req->input("pay_fee", "");	// 결제수수료 주문
        $fintech        = $req->input("fintech", "");	// 핀테크

        $str_order_by = $ord_field." ".$ord;

		if($ord_field == "a.head_desc"){ // 상단 홍보글인경우, 상단홍보글, 상품명 순.
			$str_order_by = $ord_field." ".$ord." ,a.goods_nm ".$ord;
		}

		$where = "";
		$insql = "";
		$is_not_use_date = false;

		/////////////////////////////////////////////////////////
		// 날짜검색 미 사용여부

		if($ord_no != ""){
			$is_not_use_date = true;
		} else if($user_id != ""){
			$is_not_use_date = true;
		} else if($user_nm != ""){
			$is_not_use_date = true;
		} else if(strlen($r_nm) >= 4){
			$is_not_use_date = true;
		} else if($cols == "b.mobile" && strlen($key) >= 8){
			$is_not_use_date = true;
		} else if($cols == "b.phone" && strlen($key) >= 8){
			$is_not_use_date = true;
		} else if($cols == "b.r_mobile" && strlen($key) >= 8){
			$is_not_use_date = true;
		}

		if($is_not_use_date == true && $nud == "Y"){
		} else {
			$where .= " and a.ord_date >= cast('$sdate' as date) ";
			$where .= " and a.ord_date < DATE_ADD('$edate', INTERVAL 1 DAY) ";
		}

		if($ord_no != "")		$where .= " and a.ord_no = '$ord_no' ";
		if($user_nm != "")	    $where .= " and b.user_nm = '$user_nm' ";
		if($r_nm != "")		    $where .= " and b.r_nm like '%$r_nm%' ";
		if($user_id != "")	    $where .= " and b.user_id = '$user_id' ";
		if($pay_nm != "")		$where .= " and d.pay_nm like '$pay_nm%' ";

		if($cols != "" && $key != ""){
			if(in_array($cols, array("b.mobile","b.phone","b.r_phone","b.r_mobile"))){
				$key = $this->__replaceTel($key);
				if($cols == "b.mobile" || $cols == "b.phone" || $cols == "b.r_mobile"){
                    $where .= " and $cols = '$key' ";
				} else {
					$where .= " and $cols like '$key%' ";
				}
			} else {
				if( $cols == "memo" ){
					$where = " and ( h.state like '%$key%' or h.memo like '%$key%' )";
				} else if($cols == "a.dlv_end_date"){
					$where = " and date_format($cols, '%Y%m%d') = $key";
				}else {
					$where .= " and $cols like '$key%' ";
				}
			}
		}

		if($sale_place != "")	$where .= " and a.sale_place = '$sale_place' ";
		if($com_type != "")	    $where .= " and c.com_type   = '$com_type' ";
		if($com_id != "")		$where .= " and c.com_id     = '$com_id' ";
		if($ord_kind != "")	    $where .= " and a.ord_kind   = '$ord_kind' ";
		if($ord_type != "") 	$where .= " and a.ord_type   = '$ord_type' ";
		if($bank_inpnm != "")	$where .= " and d.bank_inpnm = '$bank_inpnm' ";

		// 결제조건
		if($stat_pay_type != ""){
			if($not_complex == "Y"){
                $where .= " and a.pay_type = '$stat_pay_type' ";
			}else{
				$where .= " and (( a.pay_type & $stat_pay_type ) = $stat_pay_type) ";
			}
        }

        if($ord_state != "")	$where .= " and a.ord_state = '$ord_state' ";

        if($clm_state == "90")  $where .= " and a.clm_state = 0 ";

		else{
			if($clm_state != ""){
				$where .= " and a.clm_state = '$clm_state' ";
			}
        }

		if ($clm_stock_check == "0") $where .= " and csc.state is null ";
		else if ($clm_stock_check == "1") $where .= " and csc.state = 30 ";

		if($baesong_kind != "")	$where .= " and c.baesong_kind = '$baesong_kind' ";

		//2005.12.27 추가 지명근
		if ($opt_kind_cd != "")	$where .= " and OPT_KIND_CD = '$opt_kind_cd' ";


		if($brand_cd != ""){
			$where .= " and c.brand ='$brand_cd'";
		} else if ($brand_cd == "" && $brand_nm != ""){
			$where .= " and c.brand ='$brand_cd'";
		}

		if($goods_nm != "")     $where .= " and a.goods_nm like '%$goods_nm%'";
		if($style_no != "")     $where .= " and c.style_no like '$style_no%'";

		//if($goods_no != "")     $where .= " and c.goods_no = '$goods_no' ";

		$goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and c.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and c.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

		if($head_desc != "")    $where .= " and a.head_desc like '%$head_desc%' ";
		if($special_yn != "")   $where .= " and c.special_yn = '$special_yn' ";
		if($baesong_info != "") $where .= " and c.baesong_info = '$baesong_info' ";
		if($goods_type != "")   $where .= " and c.goods_type = '$goods_type' ";
		if($out_ord_no != "")   $where .= " and b.out_ord_no = '$out_ord_no' ";
		if($dlv_no != "")       $where .= " and a.dlv_no = '$dlv_no' ";
		if($pay_stat != "")     $where .= " and d.pay_stat = '$pay_stat' ";
		if($mobile_yn != "")    $where .= " and b.mobile_yn = '$mobile_yn' ";
		if($app_yn != "")	    $where .= " and b.app_yn = '$app_yn' ";
		if($pay_fee == "Y")     $where .= " and a.pay_fee > 0 ";
        if($fintech == "Y")     $where .= " and d.fintech <> '' ";


		// Cash Receipt Search
		if($receipt == "R"){	// 신청
			$where .= " and d.cash_apply_yn = 'Y' ";
		} elseif($receipt == "Y"){	// 발행
			$where .= " and d.cash_yn = 'Y' ";
        }

		// Delivery Type
		if($dlv_type != "")		$where .= " and b.dlv_type = '$dlv_type' ";

		if($goods != ""){			// 파일로 검색일 경우
			$goods = explode(",",$goods);
			for($i=0;$i<count($goods);$i++){
				if(empty($goods[$i])) continue;
				list($no,$sub) = explode("\|",$goods[$i]);
				if($insql == ""){
					$insql .= " select '$no' as no,'$sub' as sub ";
				} else {
					$insql .= " union select '$no' as no,'$sub' as sub  ";
				}
			}
			$insql = " inner join ( $insql ) sg on c.goods_no = sg.no and c.goods_sub = sg.sub ";
		}

		$id = Auth('head')->user()->id;
		$ip = $_SERVER["REMOTE_ADDR"];

        $total      = 0;
        $page_cnt   = 0;

		// 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
		if ($page == 1)
        {
			// 갯수 얻기
			$sql = " /* [$id][$ip] admin : order/ord01.php (1) */
				select
					count(*) total
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
				left outer join claim_stock_check csc on csc.ord_opt_no = a.ord_opt_no
				where 1=1 $where
			";

            
			$row = DB::selectOne($sql);
			$total = $row->total;

			$page_cnt   = (int)(($total-1)/$page_size) + 1;
			$startno    = ($page-1) * $page_size;

		} else {
			$startno = ($page-1) * $page_size;
			//$arr_header = null;
        }

       

		if($limit == -1){
			if ($page > 1) $limit = "limit 0";
			else $limit = "";
		} else {
			$limit = " limit $startno, $page_size ";
		}

		if($cmd == "list"){
			$sql = "
				select
					'' as chkbox, a.ord_no, a.ord_opt_no, ord_state.code_val ord_state_nm, a.ord_state , clm_state.code_val clm_state, pay_stat.code_val as pay_stat,
					ifnull(gt.code_val,'N/A') as goods_type_nm, a.style_no, '' as img_view, a.goods_nm,
					replace(a.goods_opt, '^', ' : ') as opt_val, a.goods_addopt, a.qty, a.user_nm, a.user_id, a.r_nm, a.price,
					a.sale_amt, a.gift, a.dlv_amt, 0 as pay_fee, pay_type.code_val as pay_type, fintech,
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
					a.sms_name, a.sms_mobile, a.head_desc, a.clm_stock_check_yn
				from (
					select
						b.ord_no, a.ord_opt_no, a.ord_state, d.pay_stat, c.goods_type, c.style_no, a.goods_nm,
						a.goods_opt, a.qty
                        -- , concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm
                        , ifnull(b.user_nm, '') as user_nm
                        , ifnull(b.user_id, '') as user_id
                        , b.r_nm,
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
                        if(csc.state = 30, 'Y', 'N') as clm_stock_check_yn,
                        a.head_desc,
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
						left outer join claim_stock_check csc on csc.ord_opt_no = a.ord_opt_no
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
				left outer join code dlv_type on (a.dlv_type = dlv_type.code_id and dlv_type.code_kind_cd = 'G_G_DLV_TYPE')
			";
		} else if($cmd == "popup"){ // 주문검색 쿼리

			$sql = " /* [$id][$ip] admin : order/ord01.php (2) */
				select  SQL_BUFFER_RESULT
					'선택' as choice
					, date_format(a.ord_date, '%Y.%m.%d %H:%i:%s') ord_date, a.ord_no, a.ord_seq, 'view' as view, '' as goods_img
					, a.goods_nm, a.style_no
					, replace(a.goods_opt, '^', ' : ') as opt_val
					, a.qty, a.user_nm, a.r_nm, a.price, pay_type.code_val pay_type
					, a.head_desc, a.coupon_nm
                    , ord_type.code_val ord_type, ord_type.code_id ord_type_cd
                    , ord_kind.code_val ord_kind, ord_kind.code_id ord_kind_cd
                    , ord_state.code_val ord_state, ord_state.code_id ord_state_cd
                    , clm_state.code_val clm_state
					, a.sale_place, com_type.code_val com_type, a.com_nm
					, baesong_kind.code_val baesong_kind, baesong_info.code_val baesong_info
					, date_format(a.upd_dm,'%Y.%m.%d %H:%i:%s') upd_dm
					, date_format(a.dlv_end_date,'%Y.%m.%d %H:%i:%s') dlv_end_date
					, date_format(a.upd_date,'%Y.%m.%d %H:%i:%s') upd_date
					, a.goods_no, a.goods_sub
					, concat('$goods_img_url',replace(a.img,'$cfg_img_size_real','$cfg_img_size_list')) as img, a.special_yn
					, if(a.jaego_out_qty <>'', if(a.jaego_out_qty <> a.qty, '1', '0'), 0) as odd, a.jaego_out_qty, a.qty as ord_qty,a.ord_opt_no
					, a.goods_type
					, '2' as depth
				from (
					select
						a.ord_opt_no,b.ord_date, b.ord_no, a.ord_seq, a.goods_nm, a.head_desc, a.qty
						, a.goods_opt, b.user_nm, b.r_nm, a.price, d.pay_type, c.img
						, case d.pay_type
							when '2' then d.card_name
							when '1' then d.bank_code
							when '6' then d.card_name
							else d.bank_code
						 end bank_code
						, a.ord_state, a.clm_state, i.com_nm, c.com_type, c.price-c.wonga as prf
						, case a.ord_state
							when '-20' then null
							else d.upd_dm
						 end upd_dm
						, a.dlv_end_date, b.upd_date, c.goods_no, c.goods_sub, a.ord_kind, c.baesong_kind, a.ord_type, c.style_no
						, e.com_nm sale_place,special_yn, c.baesong_info,f.coupon_nm
						, ifnull((select sum(qty) from order_opt_wonga where ord_opt_no = a.ord_opt_no and ord_state = '10' ),'') as jaego_out_qty
						, c.goods_type, b.out_ord_no
						, h.state, h.memo
						, if(ifnull(a.goods_addopt,'') = '',
							(select ifnull(group_concat(if(addopt_amt>0,concat(addopt,'(+',addopt_amt,')'),addopt)),'')
							from order_opt_addopt where ord_opt_no = a.ord_opt_no),
							a.goods_addopt
						) as goods_addopt
					from order_opt a
						inner join order_mst b on a.ord_no = b.ord_no
						left outer join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
						left outer join payment d on b.ord_no = d.ord_no
						left outer join coupon f on ( a.coupon_no = f.coupon_no )
						left outer join company e on a.sale_place = e.com_id and e.com_type = '4'
						left outer join company i on a.com_id = i.com_id
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
				left outer join code baesong_kind on (a.baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
				left outer join code baesong_info on (a.baesong_info = baesong_info.code_id and baesong_info.code_kind_cd = 'G_BAESONG_INFO')
				left outer join code goods_type on (a.goods_type = goods_type.code_id and goods_type.code_kind_cd = 'G_GOODS_TYPE')
			";
        }

        $depth_no = "";
        $rows = DB::select($sql);
        
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
        $arr_header = array(
            "total" => $total,
            "page" => $page,
            "page_cnt" => $page_cnt,
            "page_total" => count($rows)
        );

        // $arr_header['page_total'] = count($rows);

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $rows
        ]);
    }

    public function dlv_comment(Request $req) {
		$sql = " /* admin : order/ord01.php (44) */
			update order_opt set
				dlv_comment = '$req->comment'
			where ord_opt_no = '$req->ord_opt_no'
		";

        DB::update($sql);
    }

	public function order_goods($ord_no, $ord_opt_no){
		$sql	= "
			select
				d.goods_nm, replace(a.goods_opt, '^', ' : ') as opt_val
				, a.qty, a.ord_type, ord_type.code_val ord_type_nm, a.ord_kind
				, a.price
                -- , c.wonga
                , f.code_val, a.dlv_no,  b.upd_date, a.ord_state
			from order_opt a
				inner join order_mst b on a.ord_no = b.ord_no
				-- inner join order_opt_wonga c on a.ord_opt_no = c.ord_opt_no
				inner join goods d on a.goods_no = d.goods_no and a.goods_sub = d.goods_sub
				left outer join code f on a.dlv_cd = f.code_id and f.code_kind_cd = 'DELIVERY'
				left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
			where a.ord_opt_no = '$ord_opt_no'
		";

        $row = DB::selectOne($sql);
		$row->price	= Lib::cm($row->price);
		// $row->wonga	= Lib::cm($row->wonga);

		// 판매 구분
		$sql	= " select code_id id, code_val val from code where code_kind_cd = 'G_ORD_KIND' and code_id <> 'K' ";

		if( $row->ord_type == 0 ){
			// 정상판매
			$sql .= " and code_id < 20";
		}else{
			// 수기판매
			$sql .= " and code_id > 10";
		}

		$ord_kinds	= DB::select($sql);

		$values = [
			'ord_no'		=> $ord_no,
			'ord_opt_no'	=> $ord_opt_no,
			'ord_kinds'		=> $ord_kinds,
			'goods_list'	=> $row
		];

		return view( Config::get('shop.head.view') . '/order/ord01_goods',$values);
    }

	public function order_goods_save($ord_no, $ord_opt_no, Request $req){
		$ord_kind	= $req->input('ord_kind');

		try {
            DB::beginTransaction();

			// 판매구분 변경
			$sql	= "
				update order_opt set
					ord_kind = :ord_kind
				where ord_opt_no = :ord_opt_no
			";
			DB::update($sql, ['ord_kind' => $ord_kind, 'ord_opt_no'	=> $ord_opt_no]);

			// 최종 수정일 변경
			$sql = "
				update order_mst set
					upd_date = now()
				where ord_no = :ord_no
			";
			DB::update($sql, ['ord_no' => $ord_no]);

            DB::commit();
            return response()->json(null, 201);
        } catch(Exception $e){
            DB::rollback();
            return response()->json(['msg' => $e->getMessage()], 500);
        }
	}

    public function cancel_orders(Request $req) {
        $conf = new Conf();
        $cfg_return_yn	= $conf->getConfigValue("point","return_yn");

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        $claim = new Claim( $user );

        $datas = $req->input('datas', []);

        DB::beginTransaction();

        try {
				foreach($datas as $data) {


				//for( $i = 0; $i < count($data); $i++ )
				//{

                list($ord_no, $ord_opt_no) = explode('|', $data);

                $sql = "
                    select a.point_amt, b.user_id
                    from order_opt a
                        inner join order_mst b on a.ord_no = b.ord_no
                    where a.ord_opt_no = $ord_opt_no
                        and a.ord_state = 1
                ";

                $row = DB::selectOne($sql);

                $cancel_point = $row->point_amt;
                $user_id = $row->user_id;

                // 주문 취소
                $claim->SetOrdOptNo($ord_opt_no);
                $claim->ChangeClaimStateOrder(-10);

                if(empty($row->point_amt)) continue;

                if( $user_id != "" ){
                    if( isset($cfg_return_yn) ) {
                        if( $cfg_return_yn == "Y" ){
                            // 포인트 환원
                            $point = new Point($user, $user_id);
                            $point->SetOrdNo( $ord_no );
                            $point->Cancel( $cancel_point );
                        }
                    }
                }

                $sql = "select no from order_gift where ord_no = '$ord_no'";

                $gift = DB::selectOne($sql);

                if(!empty($gift->no)) {
                    $order_gift_no = $gift->no;

                    if($order_gift_no != ""){
						//$gift = new Gift( $user );
						$gift = new Gift();
                        $gift->SetRefundGiftAmt($order_gift_no, $ord_opt_no, '');
                        $gift->Refund( $ord_no, $ord_opt_no );
                    }
                }

                // 전체 취소된 주문건은 order_mst 상태 변경
                $sql = "
                    select count(*) as ord_cnt
                    from order_opt where ord_no = $ord_no and ord_state <> -10
                ";
                $ord = DB::selectOne($sql);
                $ord_cnt = $ord->ord_cnt;

                if( $ord_cnt == 0 ){
                    $sql = "
                        update order_mst set
                            ord_state = -10,
                            upd_date = now()
                        where ord_no = '$ord_no'
                            and ord_state = 1
                    ";
                    DB::update($sql);
                }
            }
            DB::commit();

            //return response()->json(null, 204);
            return response()->json(["data"=> "200"]);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function confirm_orders(Request $req) {
        $ord_opt_nos = $req->input('ord_opt_nos', []);
        DB::beginTransaction();

        try {
            foreach($ord_opt_nos as $ord_opt_no) {
				$sql = "
					update order_opt set
						ord_state = '50'
					where ord_opt_no = '$ord_opt_no'
                        and ord_state >= '30'
                        and ord_state < '50'
                ";

                DB::update($sql);
            }
            DB::commit();

            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function order_memo(Request $req) {

		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

        $ord_no = $req->input('ord_no', '');
        $ord_opt_no = $req->input('ord_opt_no', '');

        $state = $req->input('state', '');
        $memo = $req->input('memo', '');

        // Check Exists
        $sql = "
            select count(*) as cnt
            from order_opt_memo
            where ord_opt_no = '$ord_opt_no'
        ";
        $row = DB::selectOne($sql);

        if( $row->cnt == 0 ){
            $sql = "
                insert into order_opt_memo (
                    ord_opt_no, ord_no, state, memo, ut, admin_id, admin_nm
                ) values (
                    '$ord_opt_no', '$ord_no', '$state', '$memo', now(), '$admin_id', '$admin_nm'
                )
            ";
            DB::insert($sql);
        } else {
            $sql = "
                update order_opt_memo set
                    state = '$state',
                    memo = '$memo',
                    admin_id = '$admin_id',
                    admin_nm = '$admin_nm',
                    ut = now()
                where ord_opt_no = '$ord_opt_no'
            ";
            DB::update($sql);
        }

        return response()->json(null, 204);
    }

    public function claim_message_save(Request $req) {

		$ord_opt_no = $req->input("ord_opt_no", "");
		$clm_no     = $req->input("clm_no", "");
		$msg        = $req->input("msg", "");
		$ord_state  = $req->input("ord_state", "");
		$clm_state  = $req->input("clm_state", "");
		$cs_form    = $req->input("cs_form", "");

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        try {
            if(empty($clm_state)){
                $clm_state = $ord_state;
            }

            if(empty($clm_no)){
                $sql = "
                    select ifnull(max(clm_no),'') as clm_no
                    from claim
                    where ord_opt_no = '$ord_opt_no'
                ";
                $row = DB::selectOne($sql);
                $clm_no = $row->clm_no;
            }

            $param = array(
                "ord_state"=>$ord_state
                ,"clm_state"=>$clm_state
                ,"cs_form"=>$cs_form
                ,"memo"=>$msg
            );

            $claim = new Claim($user);

            $claim->SetOrdOptNo( $ord_opt_no );
            $claim->SetClmNo( $clm_no );

            $memo_no = $claim->InsertMessage( $param );

            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

	/*
		Function: ClaimSave
		클레임 임시저장/요청/처리완료 실행
	*/
	public function claim_save(Request $req) {

		$ord_no         = $req->input("ord_no", "");
		$ord_opt_no		= $req->input("ord_opt_no", "");
		$ord_opt_nos    = $req->input("ord_opt_nos", "");
		$a_ord_opt_no   = explode(",", $ord_opt_nos);

		$clm_state      = $req->input("clm_state", "");
		$clm_qty        = $req->input("clm_qty",0);
		$cmd            = $req->input("cmd", "");
		$clm_det_no		= $req->input("clm_det_no", "");
		$refund_yn      = $req->input("refund_yn", "");
		// 여러개 동시에 클레임 처리할 경우, 클레임 수량은 주문수량과 동일하게 처리
		if(count($a_ord_opt_no) > 1 && $clm_qty > 0){
			$clm_qty = 0;
		}

		$clm = array (
				"cur_clm_state"		=> $req->input("prev_clm_state", ""),
				"clm_state"			=> $clm_state,
				"clm_reason"		=> $req->input("clm_reason", ""),
				"clm_qty"			=> $clm_qty,
				"jaego_yn"			=> $req->input("jaego_yn", ""),
				"jaego_reason"		=> $req->input("jaego_reason", ""),
				"refund_yn"			=> $refund_yn,
				"refund_amt"		=> $req->input("refund_amt", ""),
				"refund_bank"		=> $req->input("refund_bank", ""),
				"refund_account"	=> $req->input("refund_account", ""),
				"refund_nm"			=> $req->input("refund_nm", ""),
				"dlv_deduct"		=> $req->input("dlv_deduct", ""),
				"update_field"		=> ""
		);

		$name = Auth('head')->user()->name;

		if( $clm_state == "-30" ){
			$this->__claim_order_save($a_ord_opt_no,$clm);
			return response()->json(null, 204);
		}

		if($cmd == "save"){
			//임시저장
			$clm["clm_state"] = "1";
			$this->__claim_order_save($a_ord_opt_no,$clm);
		} else if($cmd == "req"){
			//클레임요청
			$clm["update_field"] = ",req_date = now(),req_nm = '$name',proc_date = null, proc_nm = '', end_date = null, end_nm = '' ";
			$this->__claim_order_save($a_ord_opt_no,$clm);
		} else if($cmd == "proc"){
			//
			$clm["update_field"] = ",proc_date = now(), proc_nm = '$name', end_date = null, end_nm = ''";
			$this->__claim_order_save($a_ord_opt_no,$clm);
		} else if($cmd == "end"){
			//if($refund_yn != "y"){
                $clm["update_field"] = ",end_date = now(), end_nm = '$name'";
				$this->__claim_order_save($a_ord_opt_no,$clm);


			//} else {
			//	return response()->json(null, 401);
			//}
		}

		return response()->json(null, 201);
		//return response()->json($a_ord_opt_no, 201);
	}

    private function __claim_order_save($ord_opt_nos,$clm){
        try {
            DB::beginTransaction();

            $user = [
                'id' => Auth('head')->user()->id,
                'name' => Auth('head')->user()->name
            ];

            $claim = new Claim( $user );

            for( $i=0; $i<count($ord_opt_nos); $i++){
                $ord_opt_no = $ord_opt_nos[$i];
                $claim->SetOrdOptNo( $ord_opt_no );
                $clm_no = $this->__claim_update($ord_opt_no,$clm);

                if($clm_no == -1) continue;

                if($clm["clm_state"] != 1){
                    $claim->SetClmNo($clm_no);

                    // 교환 및 환불완료
                    if($clm["clm_state"] == 60 || $clm["clm_state"] == 61){
                        $sql = "
                            select o.ord_no, o.add_point
                            from order_opt o inner join claim c on o.ord_opt_no = c.ord_opt_no where o.ord_opt_no = $ord_opt_no
                        ";
                        $row = DB::selectOne($sql);
                        $ord_no = $row->ord_no;
                        $add_point = $row->add_point;

                        if($ord_no != ""){

                            // 매출내역 저장 (마이너스 매출), 재고조정
                            $claim->MinusSales( $clm["clm_state"], $ord_no, $ord_opt_no );

                            //echo "clm_no :$ord_no";
                            //DB::rollback();
                            //return;

                            // 환불, 교환 시 모두 적립금 차감 처리하도록 변경(2013-03-20)
                            $point = new Point($user);
                            $point->SetOrdNo( $ord_no );
                            $point->Refund( $ord_opt_no, $add_point,$clm["clm_state"] );

                            /*
                                    교환완료 시 자식 주문건의 상태를 입금완료 처리
                            */
                            if($clm["clm_state"] == "60") {
                                $claim->CompleteChange();
                            }

                            // 사은품 환불 처리
                            if($clm["clm_state"] == "61") {
								//$gift = new Gift($this->user);
								$gift = new Gift();
                                $gift->Refund( $ord_no, $ord_opt_no );
                            }
                        }
                    }
                }

				// order_opt 주문상태, 클레임상태 변경
				$claim->ChangeClaimStateOrder($clm["clm_state"]);

			}
            DB::commit();

            return true;
        }catch(Exception $e) {
            DB::rollback();
            return false;
        }
    }

    private function __claim_update($ord_opt_no,$clm){
        $sql = "
            select
                o.goods_no,o.goods_sub,o.qty,ifnull(c.clm_no,0) as clm_no,ifnull(o.clm_state,'0') as clm_state
            from order_opt o left outer join claim c on o.ord_opt_no = c.ord_opt_no where o.ord_opt_no = $ord_opt_no
        ";
        $row = DB::selectOne($sql);
        $clm_no = $row->clm_no;
        $clm_state = $row->clm_state;
        $qty = $row->qty;

        $user = [
            'id'	=> Auth('head')->user()->id,
            'name'	=> Auth('head')->user()->name
        ];

        $name = $user["name"];

        $claim = new Claim( $user );
        $claim->SetOrdOptNo( $ord_opt_no );
        $claim->SetClmNo($clm_no);

        if($clm["clm_state"] > 1 && ($clm_state != $clm["cur_clm_state"] || $clm_state >= $clm["clm_state"])){
            return false;
        }

        if($clm_no > 0){

            // 임시저장이면 현재 상태로 저장
            if($clm["clm_state"] == 1){
                if( $clm_state == "" or $clm_state == "0"){
				}else{
					$clm["clm_state"] = $clm_state;
				}
            }

            $param =  array(
                "clm_state"         => $clm["clm_state"],
                "clm_reason"        => $clm["clm_reason"],
                "refund_yn"         => $clm["refund_yn"],
                "dlv_deduct"        => $clm["dlv_deduct"],
                "refund_amt"        => $clm["refund_amt"],
                "refund_yn"         => $clm["refund_yn"],
                "refund_bank"       => $clm["refund_bank"],
                "refund_account"    => $clm["refund_account"],
                "refund_nm"         => $clm["refund_nm"],
                "update_field"      => $clm["update_field"]
            );

            $claim->UpdateClaim( $param );
        } else {
            $goods_no = $row->goods_no;
            $goods_sub = $row->goods_sub;

            $param = array(
                "clm_state"         => $clm["clm_state"],
                "clm_reason"        => $clm["clm_reason"],
                "refund_amt"        => $clm["refund_amt"],
                "refund_yn"         => $clm["refund_yn"],
                "refund_bank"       => $clm["refund_bank"],
                "refund_account"    => $clm["refund_account"],
                "refund_nm"         => $clm["refund_nm"],
                "memo"              => "",
				//"req_date"          => "now()",
                "req_date"          => date("Y-m-d H:i:s"),
                "req_nm"            => $name,
                "end_date"          => null,
                "end_nm"            => "",
                "goods_no"          => $goods_no,
                "goods_sub"         => $goods_sub
            );
            $clm_no = $claim->InsertClaim( $param );
        }

        $sql = "
            select clm_det_no from claim_detail where clm_no = '$clm_no'
        ";
        $row = DB::selectOne($sql);
        $clm_det_no = '';
        if (!empty($row->clm_det_no)) $clm_det_no = $row->clm_det_no;

        $claim = new Claim( $user );
        $claim->SetClmNo($clm_no);

        $clm_qty = $clm["clm_qty"];
        $jaego_yn = $clm["jaego_yn"];
        $jaego_reason = $clm["jaego_reason"];
        $stock_state = "1";

        if($clm_qty == 0 && $qty > 0){
            $clm_qty = $qty;
        }

        if($clm_det_no > 0){

            $param = array(
                    "clm_qty" => $clm_qty,
                    "jaego_yn" => $jaego_yn,
                    "jaego_reason" => $jaego_reason,
                    "stock_state" => $stock_state
            );
            $claim->SetClmDetNo( $clm_det_no );
            $claim->UpdateClaimDetail( $param );  //클레임 상세내역 내용 변경

        } else {
            $param = array(
                "clm_qty" => $clm_qty,
                "ord_wonga_no" => 0,
                "jaego_yn" => $jaego_yn,
                "jaego_reason" => $jaego_reason,
                "stock_state" => $stock_state
            );
            $claim->InsertClaimDetail( $param );
        }

        return $clm_no;
    }

	/*
		Function: OrderSave
		출고요청 상태변경
	*/
	public function order_save(Request $req) {

		$ord_no			= $req->input("ord_no", "");
		$ord_opt_no		= $req->input("ord_opt_no", "");
		$ord_opt_nos	= $req->input("ord_opt_nos", "");
		$a_ord_opt_no	= explode(",", $ord_opt_nos);

		$result_code	= "200";
		$msg			= "";

		try {
			DB::beginTransaction();

			$user = [
				'id'	=> Auth('head')->user()->id,
				'name'	=> Auth('head')->user()->name
			];

			$order = new Order( $user );
			$order->SetOrdNo( $ord_no );

			for( $i=0; $i<count($a_ord_opt_no); $i++){
				$ord_opt_no = $a_ord_opt_no[$i];

				if( $order->GetOrderState($ord_opt_no) == "5" ){
                    $result = $order->ProcOrder($ord_opt_no);
                    if($result == "-2"){
                        $result_code    = "400";
                    }
				}
			}
			DB::commit();

			//return true;
		}catch(Exception $e) {
			DB::rollback();
			$result_code    = "500";
            $msg = $e->getMessage();
		}

		return response()->json([
			"code"	=> $result_code,
			"msg"	=> $msg
		]);
	}

    // 주문상태 변경 (출고요청->출고처리중 / 출고처리중->출고완료)
    public function update_order_state(Request $req) {
        $code = 200;
        $msg = '';

        $user = [
			'id'	=> Auth('head')->user()->id,
			'name'	=> Auth('head')->user()->name
		];
        $order = new Order($user);

        $ord_state = $req->input('ord_state');
        $ord_opt_nos = $req->input('ord_opt_nos');
        $dlv_series_no = '';

        try {
            DB::beginTransaction();

            if($ord_state === '20') { // 출고처리중으로 변경

                $dlv_series_no = $req->input('dlv_series_no');

                $sql = "
                    select
                        dlv_series_no
                    from order_dlv_series
                    where dlv_day >= date_format(date_sub(now(),interval 1 day),'%Y%m%d')
                        and dlv_series_nm = :dlv_series_no
                    order by dlv_series_no desc limit 0,1
                ";
                $row = DB::selectOne($sql, ['dlv_series_no' => $dlv_series_no]);

                if($row === NULL) {
                    $dlv_series_no = DB::table('order_dlv_series')->insertGetId([
                        'dlv_series_nm'	=> $dlv_series_no,
                        'dlv_day'		=> date('Ymd'),
                        'regi_date'		=> now(),
                    ]);
                } else {
                    $dlv_series_no = $row->dlv_series_no;
                }

                $is_soldout = false;
    
                foreach($ord_opt_nos as $datas) {
                    if(trim($datas) == "") continue;
        
                    list($ord_no,$ord_opt_no) = explode("||", $datas);
                    $order->SetOrdOptNo($ord_opt_no,$ord_no);
        
                    if($order->CheckStockQty($ord_opt_no)) {
                        $state_log = array("ord_no" => $ord_no, "ord_state" => $ord_state, "comment" => "배송 출고요청", "admin_id" => $user['id'], "admin_nm" => $user['name']);
                        $order->AddStateLog($state_log);
                        $order->DlvProc($dlv_series_no, $ord_state);
                    } else {
                        $is_soldout = true;
                    }
                }
    
                if($is_soldout === true) {
                    $code = 400;
                    $msg = '품절된 제품이 포함되어있습니다.';
                } else {
                    $msg = '변경되었습니다.';
                }
            
            } else if($ord_state === '30') { // 출고완료로 변경

                $conf = new Conf();
                $cfg_shop_name		= $conf->getConfigValue("shop","name");
                $cfg_kakao_yn		= $conf->getConfigValue("kakao","kakao_yn");
                $cfg_sms			= $conf->getConfig("sms");
                $cfg_sms_yn			= $conf->getValue($cfg_sms,"sms_yn");
                $cfg_delivery_yn	= $conf->getValue($cfg_sms,"delivery_yn");
                $cfg_delivery_msg	= $conf->getValue($cfg_sms,"delivery_msg");
                $shop_phone =       $conf->getConfigValue("shop","phone");

                $dlv_no = $req->input("dlv_no", '');
                $dlv_cd = $req->input("dlv_cd", '');
                $send_sms_yn = $req->input("send_sms_yn", 'N');

                // 아래부터 출고완료처리 작업필요
                $dlv_nm = SLib::getCodesValue('DELIVERY',$dlv_cd);
                if($dlv_nm === "") $dlv_nm = $dlv_cd;

                foreach($ord_opt_nos as $datas) {
                    if(trim($datas) == "") continue;
        
                    list($ord_no,$ord_opt_no) = explode("||", $datas);
                    $order->SetOrdOptNo($ord_opt_no,$ord_no);
        
                    if($order->CheckState("30") === false) {
                        $msg = '선택하신 주문 중 이미 출고된 주문건이 있습니다. 검색 후 다시 처리해주세요. [주문일련번호: ' . $ord_opt_no . ']';
                        throw new Exception($msg);
                    }

                    /* 주문상태 로그 */
                    $state_log	= [
                        "ord_no"		=> $ord_no,
                        "ord_opt_no"	=> $ord_opt_no,
                        "ord_state"		=> "30",
                        "comment"		=> "배송 출고처리",
                        "admin_id"		=> $user['id'],
                        "admin_nm"		=> $user['name']
                    ];
                    $order->AddStateLog($state_log);
                    $order->DlvEnd($dlv_cd, $dlv_no);
                    $order->DlvLog($ord_state = 30);

                    /* 보유재고 차감 */
                    $sql = "
                        select qty, goods_no, goods_sub, goods_opt
                        from order_opt
                        where ord_opt_no = '$ord_opt_no'
                    ";
                    $opt	= DB::selectOne($sql);
                    $_qty	= $opt->qty;

                    $_goods_no	= $opt->goods_no;
                    $_goods_sub	= $opt->goods_sub;
                    $_goods_opt	= $opt->goods_opt;

                    $prd = new Product($user);

                    $stocks = $ret = $prd->Minus([
                        "type"			=> $type = 2,
                        "etc" 			=> $etc = "",
                        "qty" 			=> $_qty,
                        "goods_no"		=> $_goods_no,
                        "goods_sub"		=> $_goods_sub,
                        "goods_opt"		=> $_goods_opt,
                        "ord_no"		=> $ord_no,
                        "ord_opt_no"	=> $ord_opt_no
                    ]);

                    if(count($stocks) > 0) {

                        // 추가옵션에 대한 재고 차감
                        $sql = "
                            select addopt_idx, addopt_qty
                            from order_opt_addopt
                            where ord_opt_no = '$ord_opt_no'
                        ";
                        $rows = DB::select($sql);

                        foreach($rows as $row) {
                            $_addopt_idx	= $row->addopt_idx;
                            $_addopt_qty	= $row->addopt_qty;
    
                            $sql2 = "
                                update options set
                                    wqty = wqty - $_addopt_qty
                                where no = '$_addopt_idx'
                            ";
                            DB::update($sql2);
                        }

                        ///////////////////////////////////////

                        // 에스크로 결제여부 검사
                        $is_escrow = $order->IsEscrowOrder();

                        if( $is_escrow ) {
                            // 거래번호 얻기
                            $sql = "select tno from payment where ord_no = '$ord_no' ";
                            $row = DB::selectOne($sql);
                            $tno = $row->tno;
    
                            // Parameters
                            $ip = $_SERVER["REMOTE_ADDR"];
                            $memo = "배송 시작 요청";
                            $a_param = array( "deli_numb" => $dlv_no, "deli_corp" => $dlv_nm );
    
                            // 배송요청 시작
                            $pg	= new Pay();
                            list( $res_cd, $res_msg ) = $pg->mod_escrow("STE1", $tno, $ord_no, $ip, $memo, $a_param);
                            //$res_cd		= "9999";
                            //$res_msg    = "에스크로 확인 제외";
    
                            // 클레임 메모 등록
                            $param = array(
                                "ord_state"	=> 30,
                                "clm_state"	=> 30,
                                "cs_form"	=> 10,
                                "memo"		=> $msg = "[에스크로] 배송시작[ $dlv_nm ($dlv_no) ] - $res_msg [$res_cd]",
                            );
                            $claim	= new Claim($user);
                            $claim->SetOrdOptNo( $ord_opt_no );
                            $claim->SetClmNo("");
                            $memo_no = $claim->InsertMessage( $param );
                        }

                        ///////////////////////////////////////

                        // 사은품 지급
                        $gift = new Gift();
                        $msg_yn  = "N";

                        $sql = "
                            select no
                            from order_gift
                            where ord_no = '$ord_no' and ord_opt_no = '$ord_opt_no'
                        ";
                        $gifts = DB::select($sql);
    
                        foreach( $gifts as $g_row ) {
                            $order_gift_no	= $g_row->no;
                            if( $order_gift_no != "" ) $gift->GiveGift($order_gift_no);
                        }

                        if( $send_sms_yn != "N" ){
                            if( $cfg_sms_yn == "Y" && $cfg_delivery_yn == "Y" ) {
    
                                $sql = "
                                    select
                                        b.user_nm, b.mobile, a.goods_nm,
                                        ( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) as msg_cnt
                                    from order_opt a
                                         inner join order_mst b on a.ord_no = b.ord_no
                                    where ord_opt_no = '$ord_opt_no'
                                          and ( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) = 0
                                ";
                                $opt = DB::selectone($sql);
                                if ( !empty($opt->user_nm) ) {
                                    $user_nm	= $opt->user_nm;
                                    $mobile		= $opt->mobile;
                                    $goods_nm	= mb_substr($opt->goods_nm, 0, 10);
    
                                    $sms = new SMS( $user );
                                    $sms_msg = sprintf("[%s]%s..발송완료 %s(%s)",$cfg_shop_name, $goods_nm, $dlv_nm, $dlv_no);
    
                                    if($cfg_kakao_yn == "Y"){
                                        /*
                                        $template_code = "OrderCode6";
                                        $msgarr = array(
                                            "SHOP_NAME" => $cfg_shop_name,
                                            "GOODS_NAME" => $goods_nm,
                                            "DELIVERY_NAME" => $dlv_nm,
                                            "DELIVERY_NO" => $dlv_no,
                                            "USER_NAME"	=> $user_nm,
                                            "ORDER_NO"	=> $ord_no,
                                            "SHOP_URL"	=> 'http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo='.$dlv_no
                                        );
                                        $btnarr = array(
                                            "BUTTON_TYPE" => '1',
                                            "BUTTON_INFO" => '배송 조회하기^DS^http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo='.$dlv_no
                                        );
                                        $sms->SendKakao( $template_code, $mobile, $user_nm, $sms_msg, $msgarr, '', $btnarr);
                                        */
                                    } else {
                                        if($mobile != ""){
                                            //$sms->Send( $sms_msg, $mobile, $user_nm,$shop_phone);
                                            $sms->SendAligoSMS( $mobile, $sms_msg, $user_nm );
                                            $msg_yn  = "Y";
                                        }
                                    }
                                }
                            }
                        }

                        DB::table("delivery_import")
                            ->where("com_id",'HEAD')
                            ->where("admin_id",$user['id'])
                            ->where("ord_opt_no",$ord_opt_no)
                            ->update([
                                'dlv_yn' =>'Y',
                                'msg_yn' => $msg_yn,
                                'rt' => DB::raw("now()")
                            ]);

                    } else {
                        $msg = '선택하신 주문 중 이미 출고된 주문건이 있습니다. 검색 후 다시 처리해주세요. [주문일련번호: ' . $ord_opt_no . ']';
                        throw new Exception($msg);
                    }
                }
            }

            DB::commit();
            $msg = '출고완료 처리되었습니다.';
        } catch(Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code"	=> $code, "msg"	=> $msg], $code);
    }

    public function refund_save($ord_opt_no, Request $req)
    {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

		$refund_no  = $req->input("refund_no");

        if( empty($refund_no) ) $refund_no = $ord_opt_no;

		$opt_nos			= str_replace("%3D", "=", $req->input("opt_nos"));
		$opt_nos			= str_replace("%2C", ",", $opt_nos);
		$opt_nos			= explode(',', $opt_nos);
		$refund_price		= Lib::uncm($req->input("refund_price"));
		$refund_dlv_amt		= Lib::uncm($req->input("refund_dlv_amt"));
		$refund_dlv_ret_amt	= Lib::uncm($req->input("refund_dlv_ret_amt"));
		$refund_dlv_enc_amt	= Lib::uncm($req->input("refund_dlv_enc_amt"));
		$refund_dlv_pay_amt	= Lib::uncm($req->input("refund_dlv_pay_amt"));
		$refund_point_amt	= Lib::uncm($req->input("refund_point"));
		$refund_coupon_amt	= Lib::uncm($req->input("refund_coupon"));
		$refund_pay_fee		= Lib::uncm($req->input("refund_pay_fee"));
		$refund_pay_fee_yn	= $req->input("refund_pay_fee_yn", "N");
		$refund_etc_amt		= Lib::uncm($req->input("refund_etc"));

		$refund_bank	= $req->input("refund_bank");
		$refund_account	= $req->input("refund_account");
		$refund_nm		= $req->input("refund_nm");
		$refund_amt		= Lib::uncm($req->input("refund_amt"));

		//사은품 정보
		$order_gift_nos		= explode(',', $req->input("order_gift_nos"));
		$refund_gift_amt	= Lib::uncm($req->input("refund_gift"));

		//사은품 처리
		//
		//Gift Class
		$gift = new Gift();

		for( $i = 0; $i < count($order_gift_nos); $i++ )
		{
			if( isset($order_gift_nos[$i]) && $order_gift_nos[$i] != "" )
			 {
				list($order_gift_no, $value)	= explode('=', $order_gift_nos[$i]);

				if( $value == "Y" )
				{
					$gifts_ref_amt	= $req->input("gifts_ref_amt_$order_gift_no", 0);

					// 사은품 환불
					$gift->SetRefundGiftAmt($order_gift_no, $refund_no, $gifts_ref_amt);
				}
				else
				{
					// 사은품 환불취소
					$gift->SetRefundGiftAmt($order_gift_no, 0, 0);
				}
			}
		}

		for( $i = 0; $i < count($opt_nos); $i++ )
		{
			list($opt_no,$value)	= explode('=', $opt_nos[$i]);

			if( $value == "y" )
			{
				$dlv_type		= $req->input("DLV_TYPE_$opt_no");
				$dlv_cm			= $req->input("DLV_CM_$opt_no");
				$dlv_amt		= $req->input("DLV_AMT_$opt_no");
				$dlv_ret_amt	= $req->input("DLV_RET_AMT_$opt_no", 0);
				$dlv_add_amt	= $req->input("DLV_ADD_AMT_$opt_no", 0);
				$dlv_enc_amt	= $req->input("DLV_ENC_AMT_$opt_no", 0);
				$dlv_pay_amt	= $req->input("DLV_PAY_AMT_$opt_no", 0);
				$ref_amt		= $req->input("REF_AMT_$opt_no", 0);

				$sql	= "select clm_no,refund_no from claim where ord_opt_no = '$opt_no'";
                $row	= DB::selectOne($sql);

				if( !empty($row->clm_no) )
				{
					$clm_no	= $row->clm_no;

					if( $ord_opt_no == $opt_no )
					{
						$sql	= "
							update claim set
								dlv_type		= '$dlv_type',
								dlv_cm			= '$dlv_cm',
								dlv_amt			= '$dlv_amt',
								dlv_ret_amt		= '$dlv_ret_amt',
								dlv_add_amt		= '$dlv_add_amt',
								dlv_enc_amt		= '$dlv_enc_amt',
								dlv_pay_amt		= '$dlv_pay_amt',
								ref_amt			= '$ref_amt',
								refund_no		= '$refund_no',
								refund_yn		= 'y',
								refund_price	= '$refund_price',
								refund_dlv_amt	= '$refund_dlv_amt',
								refund_dlv_ret_amt	= '$refund_dlv_ret_amt',
								refund_dlv_enc_amt	= '$refund_dlv_enc_amt',
								refund_dlv_pay_amt	= '$refund_dlv_pay_amt',
								refund_point_amt	= '$refund_point_amt',
								refund_coupon_amt	= '$refund_coupon_amt',
								-- refund_pay_fee	= '$refund_pay_fee',
								refund_etc_amt	= '$refund_etc_amt',
								refund_gift_amt	= '$refund_gift_amt',
								refund_amt		= '$refund_amt',
								refund_bank		= '$refund_bank',
								refund_account	= '$refund_account',
								refund_nm		= '$refund_nm'
								-- refund_pay_fee_yn = '$refund_pay_fee_yn'
							where clm_no = '$clm_no'
						";
					}
					else
					{
						$sql	= "
							update claim set
								dlv_type	= '$dlv_type',
								dlv_cm		= '$dlv_cm',
								dlv_amt		= '$dlv_amt',
								dlv_ret_amt	= '$dlv_ret_amt',
								dlv_add_amt	= '$dlv_add_amt',
								dlv_enc_amt	= '$dlv_enc_amt',
								dlv_pay_amt	= '$dlv_pay_amt',
								ref_amt		= '$ref_amt',
								refund_no	= '$refund_no'
								-- refund_pay_fee_yn = '$refund_pay_fee_yn'
							where clm_no = '$clm_no'
						";
					}
                    DB::update($sql);

				}
				else
				{
					if( $ord_opt_no == $opt_no )
					{
                        /*
						$sql = "
							insert into claim (
								ord_opt_no, clm_state, dlv_type, dlv_cm, dlv_amt, dlv_ret_amt, dlv_add_amt, dlv_enc_amt, dlv_pay_amt, ref_amt,
								refund_no, refund_yn, refund_price,
								refund_dlv_amt, refund_dlv_ret_amt, refund_dlv_enc_amt, refund_dlv_pay_amt,
								refund_point_amt, refund_coupon_amt, refund_pay_fee, refund_etc_amt, refund_gift_amt, refund_amt,
								refund_bank, refund_account, refund_nm, refund_pay_fee_yn
							) values (
								'$opt_no', 1, '$dlv_type','$dlv_cm', '$dlv_amt', '$dlv_ret_amt', '$dlv_add_amt', '$dlv_enc_amt', '$dlv_pay_amt', '$ref_amt',
								'$refund_no', 'y', '$refund_price',
								'$refund_dlv_amt', '$refund_dlv_ret_amt', '$refund_dlv_enc_amt', '$refund_dlv_pay_amt',
								'$refund_point_amt', '$refund_coupon_amt', '$refund_pay_fee', '$refund_etc_amt', '$refund_gift_amt', '$refund_amt',
								'$refund_bank', '$refund_account', '$refund_nm', '$refund_pay_fee_yn'
							)
						";
                        */
						$sql	= "
							insert into claim (
								ord_opt_no, clm_state, dlv_type, dlv_cm, dlv_amt, dlv_ret_amt, dlv_add_amt, dlv_enc_amt, dlv_pay_amt, ref_amt,
								refund_no, refund_yn, refund_price,
								refund_dlv_amt, refund_dlv_ret_amt, refund_dlv_enc_amt, refund_dlv_pay_amt,
								refund_point_amt, refund_coupon_amt, refund_etc_amt, refund_gift_amt, refund_amt,
								refund_bank, refund_account, refund_nm
							) values (
								'$opt_no', 1, '$dlv_type','$dlv_cm', '$dlv_amt', '$dlv_ret_amt', '$dlv_add_amt', '$dlv_enc_amt', '$dlv_pay_amt', '$ref_amt',
								'$refund_no', 'y', '$refund_price',
								'$refund_dlv_amt', '$refund_dlv_ret_amt', '$refund_dlv_enc_amt', '$refund_dlv_pay_amt',
								'$refund_point_amt', '$refund_coupon_amt', '$refund_etc_amt', '$refund_gift_amt', '$refund_amt',
								'$refund_bank', '$refund_account', '$refund_nm'
							)
						";
					}
					else
					{
                        /*
						$sql = "
							insert into claim (
								ord_opt_no, clm_state,dlv_type, dlv_cm, dlv_amt, dlv_ret_amt, dlv_add_amt, dlv_enc_amt, dlv_pay_amt, ref_amt, refund_no, refund_pay_fee_yn
							) values (
								'$opt_no', 1,'$dlv_type','$dlv_cm','$dlv_amt', '$dlv_ret_amt', '$dlv_add_amt', '$dlv_enc_amt', '$dlv_pay_amt', '$ref_amt', '$refund_no', '$refund_pay_fee_yn'
							)
						";
                        */
						$sql	= "
							insert into claim (
								ord_opt_no, clm_state,dlv_type, dlv_cm, dlv_amt, dlv_ret_amt, dlv_add_amt, dlv_enc_amt, dlv_pay_amt, ref_amt, refund_no
							) values (
								'$opt_no', 1,'$dlv_type','$dlv_cm','$dlv_amt', '$dlv_ret_amt', '$dlv_add_amt', '$dlv_enc_amt', '$dlv_pay_amt', '$ref_amt', '$refund_no'
							)
						";
                    }
                    DB::insert($sql);
				}

			}
			else
			{
				$sql	= "
					update claim set
						dlv_type	= '',
						dlv_cm		= '',
						dlv_amt		= '0',
						dlv_ret_amt	= '0',
						dlv_add_amt	= '0',
						dlv_enc_amt	= '0',
						dlv_pay_amt	= '0',
						ref_amt		= '0',
						refund_no	= '0'
						-- refund_pay_fee_yn = 'N'
					where ord_opt_no = '$opt_no'
                ";

                DB::update($sql);
			}
		}

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

            // 배송정보 수정
            $sql = "
                update order_mst set
                    r_nm = '$r_nm',
                    r_phone = '$r_phone',
                    r_mobile = '$r_mobile',
                    r_zipcode = '$r_zipcode',
                    r_addr1 = '$r_addr1',
                    r_addr2 = '$r_addr2',
                    dlv_msg = '$dlv_msg',
                    upd_date = now()
                where ord_no = '$ord_no'
            ";
            DB::update($sql);

            // 택배사, 송장번호 수정
            for( $i = 0; $i < count($ord_opt_nos); $i++){

                $_ord_opt_no = $ord_opt_nos[$i];
                $_dlv_no = $req->input("dlv_no_". $_ord_opt_no );
                $_dlv_cd = $req->input("dlv_cd_". $_ord_opt_no );

                if( $_dlv_no != "" && $_dlv_cd != "" ){
                    $sql = " /* admin : order/ord01.php (3) */
                        update order_opt set
                            dlv_cd = '$_dlv_cd'
                            , dlv_no = '$_dlv_no'
                        where ord_opt_no = '$_ord_opt_no'
                    ";
                    DB::update($sql);
                }
            }

            DB::commit();
            return true;
        }catch(Exception $e) {
            DB::rollback();
            return false;
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
    private function __replaceTel($tel) {

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

    /*
    ***
    현금영수증 관련
    ***
    */
    public function show_cash(Request $req, $order_no, $order_opt_no) {
        $ord = $this->_get($order_no, $order_opt_no);
        $type = $req->done == '1' ? "result" : ($req->cash_no == '' ? 'create' : 'update');
        
        $values = [
            'type' => $type,
            'ord' => $type !== "result" ? $ord : '',
            'result' => $type === "result" ? [
                "msg" => "발행 구현중입니다.",
                "type" => $req->cash_no == '' ? 'create' : 'update'
            ] : [],
        ];

        return view(Config::get('shop.head.view') . '/order/ord01_cash', $values);
    }

    public function search_cash_receipt_list($ord_no) {
        $code = 200;

        $sql = "
            select
                if((cash_stat = 'STSC'), '취소', if((cash_stat = 'STPC'), '부분취소', '발행')) as cash_stat
                , cash_no, ord_no, receipt_no, user_id, user_nm, admin_id, admin_nm, app_time, reg_stat, reg_desc, rt, ut
            from cash_history
            where ord_no = :ord_no
            order by rt desc
        ";

        $result = DB::select($sql, ['ord_no' => $ord_no]);

        return response()->json([
            "code" => $code,
            "head" => ['total' => count($result)],
            "body" => $result
        ]);
    }

    public function set_cash_receipt(Request $req, $ord_no, $ord_opt_no) {
        $code = 200;
        $msg = '';

        // 현금영수증 발행 작업필요

        return response()->json(["code" => $code, "msg" => $msg], $code);
    }

    /*
    ***
    세금계산서 관련
    ***
    */
    public function show_tax(Request $req, $order_no, $order_opt_no) {
        $ord = $this->_get($order_no, $order_opt_no);
        $type = $req->tax_no == '' ? 'create' : 'update';
        
        $values = [
            'type' => $type,
            'ord' => $ord,
        ];

        return view(Config::get('shop.head.view') . '/order/ord01_tax', $values);
    }

    public function search_tax_receipt_list($ord_no) {
        $code = 200;

		$sql = "
			select
				if((tax_stat = 'C'), '취소', '발행') as tax_stat
				, tax_no, ord_no, user_id, user_nm, admin_id, admin_nm, format(amt_tot, 0) as amt_tot, date_format(rt, '%Y-%m-%d') as rt
			from payment_tax_history
			where ord_no = :ord_no
			order by tax_no desc
		";

        $result = DB::select($sql, ['ord_no' => $ord_no]);

        return response()->json([
            "code" => $code,
            "head" => ['total' => count($result)],
            "body" => $result
        ]);
    }

    public function set_tax_receipt(Request $req, $ord_no, $ord_opt_no) {
        $type = $req->type;
        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;


        // 세금계산서 취소 데이터


        $code = 200;
        $msg = '';

        try {
            DB::beginTransaction();
            
            if($type === "create") {            // 세금계산서 발행
                $amt_tot = str_replace(",", "", $req->input("amt_tot"));
                $amt_sup = str_replace(",", "", $req->input("amt_sup"));
                $amt_svc = str_replace(",", "", $req->input("amt_svc"));
                $amt_tax = str_replace(",", "", $req->input("amt_tax"));
                $user_id = $req->input("user_id");
                $user_nm = $req->input("user_nm");
                $comment = $req->input("comment");

                $sql = "
                    update payment set tax_yn = 'Y', tax_date = now()
                    where ord_no = :ord_no
                ";
                DB::update($sql, ['ord_no' => $ord_no]);

                $sql = "
                    insert into payment_tax_history (
                        ord_no, tax_stat, amt_tot, amt_sup, amt_svc, amt_tax, user_id, user_nm, admin_id, admin_nm, comment, rt
                    ) values (
                        '$ord_no', 'R', '$amt_tot', '$amt_sup', '$amt_svc', '$amt_tax', '$user_id', '$user_nm', '$admin_id', '$admin_nm', '$comment', now()
                    )
                ";
                DB::insert($sql);
                
            } else if($type === "update") {     // 세금계산서 취소

                $tax_no = $req->input("tax_no");

                $sql = "
                    update payment_tax_history set 
                        tax_stat = 'C',
                        admin_id = :admin_id,
                        admin_nm = :admin_nm
                    where tax_no = :tax_no
                ";
                DB::update($sql, ['admin_id' => $admin_id, 'admin_nm' => $admin_nm, 'tax_no' => $tax_no]);

                $sql = "
                    select tax_no 
                    from payment_tax_history 
                    where ord_no = :ord_no and tax_stat = 'R'
                ";
                $rows = DB::select($sql, ['ord_no' => $ord_no]);

                if(count($rows) < 1) {
                    $sql = "
                        update payment set tax_yn = 'N', tax_date = now()
                        where ord_no = :ord_no
                    ";
                    DB::update($sql, ['ord_no' => $ord_no]);
                }
            }

            DB::commit();
            $msg = "세금계산서 " . ($type === "create" ? "발행" : "취소") . "에 성공했습니다.";
        }catch(Exception $e) {
            DB::rollback();
            $msg = "에러가 발행했습니다. 다시 시도해주세요.";
        }

        return response()->json(["code" => $code, "msg" => $msg], $code);
    }

	public function add_account_etc(Request $request)
	{
		$code = 200;
		$msg = "";
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		$ord_no = $request->input('ord_no');
		$ord_opt_no = $request->input('ord_opt_no');
		$com_id = $request->input('com_id');
		$etc_day = $request->input('etc_day', '');
		$etc_day = str_replace('-', '', $etc_day);
		$etc_amt = $request->input('etc_amt', '');
		$etc_memo = $request->input('etc_memo', '');

		$rows = [];

		try {
			DB::beginTransaction();

			DB::table('account_etc')->insert([
				'etc_day'   => $etc_day,
				'com_id'    => $com_id,
				'ord_no'    => $ord_no,
				'ord_opt_no' => $ord_opt_no,
				'etc_amt'   => $etc_amt,
				'etc_memo'  => $etc_memo,
				'admin_id'  => $admin_id,
				'admin_nm'  => $admin_nm,
				'regi_date' => now(),
			]);

			$rows = DB::table('account_etc')->where('ord_opt_no', $ord_opt_no)->get();

			DB::commit();
			$msg = "입점업체 기타정산정보가 정상적으로 등록되었습니다.";
		} catch(Exception $e){
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([ 'code' => $code, 'msg' => $msg, 'account_etcs' => $rows ], $code);
	}
}
