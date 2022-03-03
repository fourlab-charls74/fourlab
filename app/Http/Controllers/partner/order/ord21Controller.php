<?php

namespace App\Http\Controllers\partner\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ord21Controller extends Controller
{
    //
    public function index(Request $request)
    {
        $mutable = Carbon::now();
        $sdate = $mutable->sub(14, 'day')->format('Y-m-d');
        $style_no	= $request->input('style_no');


        $com_id = Auth('partner')->user()->com_id;

        $values = [
            'com_id' => $com_id,
            'sdate' => $sdate,
            'style_no' => $style_no,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'sale_places' => SLib::getSalePlaces(),
            'ord_states' => SLib::getOrdStates(),
            'clm_states' => SLib::getClmStates(),
            'ord_kinds' => SLib::getCodes('G_ORD_KIND'),
            'ord_types' => SLib::getCodes('G_ORD_TYPE'),
            'dlv_kinds' => SLib::getCodes('G_BAESONG_KIND'),
            'dlv_types' => SLib::getCodes('G_DLV_TYPE')
        ];

        return view(Config::get('shop.partner.view') . '/order/ord21', $values);
    }

    public function search(Request $request)
    {
        //주문일자 검색일자, 검색 타입
        $date_type = $request->input('date_type');
        $sdate = $request->input('sdate', Carbon::now()->sub(7, 'day')->format('Ymd'));
        $edate = $request->input('edate', date("Ymd"));

        //주문상태, 배송방식
        $ord_state = $request->input('ord_state');
        $dlv_type = $request->input('dlv_type');

        //배송구분
        $dlv_kind = $request->input('dlv_kind');

        //주문자, 수령자 이름
        $user_nm = $request->input('user_nm');
        $r_nm = $request->input('r_nm');

        //스타일넘버
        $style_no = $request->input('style_no');

        //재고
        $wqty_low = $request->input('wqty_low');
        $wqty_high = $request->input('wqty_high');

        //주문번호
        $ord_no = $request->input('ord_no');

        //주문구분
        $ord_type = $request->input('ord_type');

        //출고구분
        $ord_kind = $request->input('ord_kind');

        //품목
        $item = $request->input('item');

        //브랜드
        $brand = $request->input('brand');

        //상품명
        $goods_nm = $request->input('goods_nm');
        //컴퍼니 아이디
        $com_id = Auth('partner')->user()->com_id;

        //페이징 처리
        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $limit = $request->input('limit',100);

        $where = "";
        $having = "";

        if ($com_id != "") $where .= " and c.com_id = '$com_id' ";
        if ($ord_state != "") $where .= " and a.ord_state    = '" . Lib::quote($ord_state) . "' ";
        if ($dlv_type != "") $where .= " and b.dlv_type     = '" . Lib::quote($dlv_type) . "'";
        if ($dlv_kind != "") $where .= " and c.baesong_kind = '" . Lib::quote($dlv_kind) . "' ";
        if ($user_nm != "") $where .= " and b.user_nm      = '" . Lib::quote($user_nm) . "' ";
        if ($r_nm != "") $where .= " and b.r_nm         = '" . Lib::quote($r_nm) . "' ";
        if ($style_no != "") $where .= " and c.style_no like '" . Lib::quote($style_no) . "%'";

        if ($wqty_high != "") $having .= " and qty <= '$wqty_high' ";
        if ($wqty_low != "") $having .= " and qty >= '$wqty_low' ";

        if ($ord_no != "") $where .= " and a.ord_no       = '" . Lib::quote($ord_no) . "' ";
        if ($ord_type != "") $where .= " and a.ord_type     = '" . Lib::quote($ord_type) . "'";
        if ($ord_kind != "") $where .= " and a.ord_kind     = '" . Lib::quote($ord_kind) . "' ";
        if ($item != "") $where .= " and opt_kind_cd    = '" . Lib::quote($item) . "' ";
        if ($brand != "") $where .= " and c.brand        = '" . Lib::quote($brand) . "' ";
        if ($goods_nm != "") $where .= " and a.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";

        // 날짜검색 미 사용여부
        $is_not_use_date = $this->get_is_not_use_date($request);

        if ($is_not_use_date == false) {
            $str_date = ' AND a.ord_date ';
            if ($date_type != '') {
                //당월
                $now_month = " LAST_DAY(CURDATE() - INTERVAL 1 month) + INTERVAL 1 DAY ";
                //전월
                $prev_month = " LAST_DAY(CURDATE() - INTERVAL 2 month) + INTERVAL 1 DAY ";

                switch ($date_type) {
                    // 당일
                    case 1 :
                        $where .= " $str_date = CURDATE() ";
                        break;
                    // 어제
                    case 2 :
                        $where .= " $str_date = CURDATE()-INTERVAL 1 DAY ";
                        break;
                    // 최근 1주(당일기준)
                    case 3 :
                        $where .= " $str_date  BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE() ";
                        break;
                    // 최근 2주(당일기준)
                    case 4 :
                        $where .= " $str_date  BETWEEN CURDATE()-INTERVAL 2 WEEK AND CURDATE() ";
                        break;
                    // 최근 1달(당일기준)
                    case 5 :
                        $where .= " $str_date  BETWEEN CURDATE()-INTERVAL 1 MONTH AND CURDATE() ";
                        break;
                    // 금월
                    case 6 :
                        $where .= " $str_date  >= $now_month";
                        break;
                    // 전월
                    case 7 :
                        $where .= " $str_date BETWEEN $prev_month AND $now_month ";
                        break;
                    //그외
                    default :
                        break;
                }
            } else {
                $where .= " $str_date BETWEEN '$sdate' AND DATE_ADD('$edate', INTERVAL 1 DAY) ";
            }
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
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                inner join payment d on b.ord_no = d.ord_no
                left join coupon g on g.coupon_no = a.coupon_no
                inner join company f on a.sale_place = f.com_id and f.com_type='4'
                inner join company e on a.com_id = e.com_id
                left outer join claim i on a.ord_opt_no = i.ord_opt_no
                left outer join order_opt_memo j on a.ord_opt_no = j.ord_opt_no
                where 1=1
                and ( a.clm_state = '0' or a.clm_state = '-30') $where
                having 1 = 1 $having
			";
            //echo "<pre>$query $com_id</pre>";
            $row = DB::select($query,["com_id" => $com_id]);
            //$row = DB::select($query);
            $total = $row[0]->total;
            if($total > 0){
                $page_cnt=(int)(($total-1)/$page_size) + 1;
            }
        }

        $query = "
            select
              '0' as chk, ord_type.code_val as ord_type_nm, ord_kind.code_val as ord_kind_nm,
              a.ord_no, a.ord_opt_no,
              ord_state.code_val as ord_state_nm, pay_stat.code_val as pay_stat_nm, a.pay_fee,
              dlv_type.code_val as dlv_type, clm_state.code_val as clm_state_nm,
              ifnull(gt.code_val,'N/A') as goods_type_nm, a.style_no, '' as img_col, a.goods_nm,
              if( a.goods_addopt = '', a.goods_opt, concat(a.goods_opt,' : ', a.goods_addopt)) as opt_val,
              a.sale_qty, a.qty, a.price, a.sale_amt, a.gift, a.dlv_amt, pay_type.code_val pay_type,
              a.user_nm, a.r_nm, a.dlv_msg, a.dlv_comment, a.proc_state, a.proc_memo, a.sale_place, a.out_ord_no, a.com_nm,
              baesong_kind.code_val baesong_kind,
              a.ord_date, a.pay_date,a.last_up_date,
              a.user_id, a.goods_no, a.goods_sub,
              a.cash_apply_yn, a.cash_yn, a.fintech, 
              '1' as depth, a.ord_no as ord, a.ord_state,a.clm_state,a.ord_kind, a.img
            from
            (
              select
                a.ord_kind, a.ord_type, a.ord_no, a.ord_opt_no,a.ord_state, d.pay_stat, a.clm_state,
                c.goods_type, c.style_no, a.goods_nm,
                replace(a.goods_opt,'^',' : ') as goods_opt,
                if( ifnull(a.goods_addopt,'') = '',( select ifnull(group_concat(concat(addopt,'(+',addopt_amt,')')),'') from order_opt_addopt where ord_opt_no = a.ord_opt_no ),a.goods_addopt) as goods_addopt,
                a.qty as sale_qty,
                ifnull( (
                  select sum(if(c.goods_type = 'P',good_qty,wqty)) from goods_summary
                  where goods_no = a.goods_no and goods_sub = a.goods_sub and goods_opt = a.goods_opt
                ), 0) as qty,
                a.price, (a.coupon_amt+a.dc_amt) as sale_amt,
                (
                  select group_concat(gf.name)
                  from order_gift og
                    inner join gift gf on og.gift_no = gf.no
                  where og.ord_no = a.ord_no and og.ord_opt_no = a.ord_opt_no
                ) as gift,
                a.dlv_amt,
                case d.pay_type
                  when '0' then d.card_name
                  when '1' then d.bank_code
                  when '4' then '-'
                else d.bank_code end bank_code,
                concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm, b.r_nm, b.dlv_msg, a.dlv_comment,
                f.com_nm sale_place, b.out_ord_no,e.com_nm,
                c.baesong_kind, b.ord_date, d.pay_date,
                ifnull(a.pay_fee, 0) as pay_fee,
                i.last_up_date, c.goods_no, c.goods_sub, b.user_id, d.pay_type, d.fintech, 
                j.state as proc_state, j.memo as proc_memo,
                replace(c.img,'$cfg_img_size_real','$cfg_img_size_list') as img,
                if(d.cash_apply_yn = 'Y', '신청', '') as cash_apply_yn,
                if(d.cash_yn = 'Y', '발행', '') as cash_yn,
                b.dlv_type
              from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                inner join payment d on b.ord_no = d.ord_no
                left join coupon g on g.coupon_no = a.coupon_no
                inner join company f on a.sale_place = f.com_id and f.com_type='4'
                inner join company e on a.com_id = e.com_id
                left outer join claim i on a.ord_opt_no = i.ord_opt_no
                left outer join order_opt_memo j on a.ord_opt_no = j.ord_opt_no
              where 1=1
                and ( a.clm_state = '0' or a.clm_state = '-30') $where
              having 1 = 1 $having
              limit $startno, $page_size
            ) a
            left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
            left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
            left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
            left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
            left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
            left outer join code baesong_kind on (a.baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
            left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
            left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
            left outer join code dlv_type on (a.dlv_type = dlv_type.code_id and dlv_type.code_kind_cd = 'G_DLV_TYPE')
            order by a.ord_opt_no desc
        ";
        //echo "<pre>$query</pre>";
        //dd($query);

        $rows = DB::select($query);

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

    private function get_is_not_use_date(Request $request)
    {
        if ($request->ord_no != "") {
            return true;
        }

        if ($request->user_id != "") {
            return true;
        }

        if ($request->user_nm != "") {
            return true;
        }

        if (strlen($request->r_nm) >= 4) {
            return true;
        }

        return false;
    }

    //dlv_series_no_update

    public function update_kind(Request $req)
    {
        $ord_opt_nos = implode(', ', $req->ord_opt_nos);
        $ord_kind = $req->ord_kind;

        $sql = "update order_opt
                 set ord_kind = '$ord_kind'
               where ord_opt_no in ($ord_opt_nos)";

        DB::update($sql);

        return true;
    }

    public function update_state(Request $req)
    {
        $com_id = Auth('partner')->user()->com_id;

        //* @method static int update(string $query, array $bindings = [])
        $ord_state = $req->ord_state;
        $ord_opt_nos = $req->ord_opt_nos;
        $dlv_series_no = $req->dlv_series_no;
        $chk_ord_no = $req->chk_ord_no;


        $sql = /** @lang text */
            "
        select
          dlv_series_no
        from order_dlv_series
        where dlv_day >= date_format(date_sub(now(),interval 1 day),'%Y%m%d')
            and dlv_series_nm = '$dlv_series_no'
        order by dlv_series_no desc limit 0,1
      ";

        $row = DB::selectOne($sql);

        if ($row) {
            $dlv_series_no = $row->dlv_series_no;
        } else {
            $dlv_series_no = DB::table('order_dlv_series')->insertGetId([
                'dlv_series_nm' => $dlv_series_no,
                'dlv_day' => date('Ymd'),
                'com_id' => $com_id,
                'regi_date' => now()
            ]);
        }

        $user = [
            'id'	=> $com_id,
            'name'	=> Auth('partner')->user()->com_nm
        ];

        //수정시작 ceduce 21-07-20
        $order = new Order($user);
        $is_soldout = false;

        //for( $i = 0; $i < count($ord_opt_nos); $i++ )
        foreach ($ord_opt_nos as $datas) {
            if (!is_array($datas)) continue;

            list($ord_no, $ord_opt_no) = $datas;

            $order->SetOrdOptNo($ord_opt_no, $ord_no);

            if ($chk_ord_no == "Y") {    // 묶음주문단위로 재고검사
                $ord_opt_no = 0;
            }

            if ($order->CheckStockQty($ord_opt_no)) {
                $state_log = array("ord_no" => $ord_no, "ord_state" => $ord_state, "comment" => "배송 출고요청", "admin_id" => $user['id'], "admin_nm" => $user['name']);
                $order->AddStateLog($state_log);
                $order->DlvProc($dlv_series_no, $ord_state);
            } else {
                $is_soldout = true;
            }
        }

        if ($is_soldout == true) {
            return 2;
        } else {
            return 1;
        }


        $sql = "update order_opt
                 set ord_state = '$ord_state',
                     dlv_series_no = '$dlv_series_no'                 
               where ord_opt_no in ($ord_opt_nos)";

        DB::update($sql);

        return true;
    }
}
