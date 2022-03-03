<?php

namespace App\Http\Controllers\head\account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\SLib;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class acc01Controller extends Controller
{
    public function index()
    {
        $immutable = CarbonImmutable::now();
        $date_types = [
            '사용자' => 0,
            '금일' => $immutable->sub(0, 'day')->format('Y-m-d'),
            '어제' => $immutable->sub(1, 'day')->format('Y-m-d'),
            '최근1주' => $immutable->sub(1, 'week')->format('Y-m-d'),
            '최근2주' => $immutable->sub(2, 'week')->format('Y-m-d'),
            '최근1달' => $immutable->sub(1, 'month')->format('Y-m-d'),
            '금월' => $immutable->sub(0, 'month')->startOfMonth()->format('Y-m-d'),
            '전월' => [
                'start' => $immutable->sub(1, 'month')->startOfMonth()->format('Y-m-d'),
                'end' => $immutable->sub(1, 'month')->endOfMonth()->format('Y-m-d')
            ]
        ];
        $sdate	 = $immutable->sub(1, 'week')->format('Y-m-d');
        $values = [
            'date_types' => $date_types,
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'states' => ['5' => "입금", '10' => "출고요청", '30' => "출고완료"], // default: 30
            'com_types' => SLib::getCodes("G_COM_TYPE"),
            'sale_places' => SLib::getSalePlaces(),
            'ord_types' => SLib::getCodes("G_ORD_TYPE"),
            'ord_kinds' => SLib::getCodes("G_ORD_KIND"),
            'stat_pay_types' => SLib::getCodes("G_STAT_PAY_TYPE"),
            'items' => SLib::getItems()
        ];
        return view( Config::get('shop.head.view') . '/account/acc01', $values);
    }

    public function search(Request $request)
    {
        $immutable = CarbonImmutable::now();
        $sdate =  str_replace("-", "", $request->input("sdate", $immutable->sub(1, 'week')->format('Y-m-d')));
        $edate =  str_replace("-", "", $request->input("edate", date("Ymd")));
        $state =  $request->input("state", 5);

        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");

        $ord_no =  $request->input("ord_no", "");
        $user_nm =  $request->input("user_nm");
        $com_type =  $request->input("com_type");
        $com_id =  $request->input("com_id");
        // $cat_cd =  $request->input("cat_cd");
        // $com_nm =  $request->input("com_nm");
        $sale_place =  $request->input("sale_place");
        $style_no =  $request->input("style_no");
        $goods_no =  $request->input("goods_no");
        $ord_sdate =  $request->input("ord_sdate");
        $ord_edate =  $request->input("ord_edate");
        $pay_sdate =  $request->input("pay_sdate");
        $pay_edate =  $request->input("pay_edate");
        $dlv_sdate =  $request->input("dlv_sdate");
        $dlv_edate =  $request->input("dlv_edate");
        $clm_sdate =  $request->input("clm_sdate");
        $clm_edate =  $request->input("clm_edate");
        $ord_type =  $request->input("ord_type");
        $ord_kind =  $request->input("ord_kind");
        $stat_pay_type =  $request->input("stat_pay_type");
        $item =  $request->input("item");
        $goods_nm =  $request->input("goods_nm");

        $clm_state_ex = $request->input("clm_state_ex", "N");
        $not_complex = $request->input("not_complex", "N");

        $where = "";
        $inner_where = "";
        $insql = "";

        if ($clm_state_ex != "Y") {
            $suffix = ",60,61";
            $inner_where .= " and w.ord_state in ( ${state}${suffix} ) ";
        } else {
            $inner_where .= " and w.ord_state in ( ${state} ) ";
        }

        if ($ord_no != "") $where .= " and o.ord_no = '${ord_no}' ";
        if ($user_nm != "")	$where .= " and m.user_nm = '${user_nm}' ";
        // if ($pay_nm != "") $where .= " and d.pay_nm like '$pay_nm%' ";

        if ($sale_place != "")	$where .= " and m.sale_place = '${sale_place}' ";
        if ($com_type != "")	$where .= " and e.com_type = '${com_type}' ";
        if ($com_id != "")	$where .= " and e.com_id = '${com_id}' ";

        if ($ord_kind != "")	$where .= " and o.ord_kind = '${ord_kind}' ";
        if ($ord_type != "")	$where .= " and o.ord_type = '${ord_type}' ";

        if ($ord_sdate != "")  $where .= " and o.ord_date >= '${ord_sdate}' ";
        if ($ord_edate != "")  $where .= " and o.ord_date < DATE_ADD('${ord_edate}', INTERVAL 1 DAY) ";
        if ($pay_sdate != "")  $where .= " and p.pay_date >= '${pay_sdate}' ";
        if ($pay_edate != "")  $where .= " and p.pay_date < DATE_ADD('${pay_edate}', INTERVAL 1 DAY) ";
        if ($dlv_sdate != "")  $where .= " and o.dlv_end_date >= ${dlv_sdate} ";
        if ($dlv_edate != "")  $where .= " and o.dlv_end_date < DATE_ADD(${dlv_edate}, INTERVAL 1 DAY) ";
        if ($clm_sdate != "")  $where .= " and c.end_date >= ${clm_sdate} ";
        if ($clm_edate != "")  $where .= " and c.end_date < DATE_ADD(${clm_edate}, INTERVAL 1 DAY) ";

        // 결제조건
        if ($stat_pay_type != "") {
            if ($not_complex == "Y") {
                $where .= " and p.pay_type = '${stat_pay_type}' ";
            }else {
                $where .= " and (( p.pay_type & ${stat_pay_type} ) = ${stat_pay_type}) ";
            }
        }

        if ($item != "")	$where .= " and g.opt_kind_cd = '$item' ";

        if ($brand_cd != "") {
            $where .= " and g.brand ='${brand_cd}'";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand ='${brand_cd}'";
        }

        if ($goods_nm != "")		$where .= " and o.goods_nm like '%${goods_nm}%'";
        if ($style_no != "")		$where .= " and g.style_no like '${style_no}%'";
        if ($goods_no != "")		$where .= " and o.goods_no = '${goods_no}' ";

        $sql = "
            select
                w.ord_state_date,
                o.ord_date,
                p.pay_date,
                o.dlv_end_date,
                c.end_date as clm_date,
                o.ord_no,
                w.ord_opt_no,
                m.user_nm,
                o.goods_no,
                o.goods_sub,
                o.goods_nm,
                ord_state.code_val as ord_state,
                ord_type.code_val as ord_type,
                ord_kind.code_val as ord_kind,
                pay_type.code_val as pay_nm,
                w.qty,
                w.price,
                w.amt,
                w.point_apply_amt,
                w.coupon_apply_amt,
                w.coupon_com_amt,
                ( w.coupon_apply_amt - w.coupon_com_amt ) as coupon_allot_amt,
                w.dlv_amt,
                w.dlv_ret_amt,
                ( w.ret_amt - w.dlv_ret_amt ) as ret_amt,
                f.com_nm as sale_place,
                com_type.code_val as com_type,
                e.com_nm,
                round(if(e.com_type = 1,100,w.fee),2) as fee,
                if(e.com_type = 1,w.amt,w.sale_amt) as sale_amt,
                ( w.amt - w.sale_amt + w.dlv_amt - w.dlv_ret_amt - coupon_com_amt ) as cal_acc_amt
            from ( select
                        w.ord_opt_no,
                        w.com_id,
                        w.ord_state_date,
                        w.ord_state,
                        w.qty,
                        w.price,
                        w.price * w.qty as amt,
                        if(w.ord_state = 60 or w.ord_state = 61, -1, 1 ) * w.recv_amt as recv_amt,
                        if(w.ord_state = 60 or w.ord_state = 61, -1, 1 ) * w.point_apply_amt as point_apply_amt,
                        if(w.ord_state = 60 or w.ord_state = 61, -1, 1 ) * w.coupon_apply_amt as coupon_apply_amt,
                        round(if(ifnull(w.com_coupon_ratio, 0) > 1,ifnull(w.com_coupon_ratio, 0) / 100, ifnull(w.com_coupon_ratio, 0)) *
                            if(w.ord_state = 60 or w.ord_state = 61, -1, 1 ) * ifnull(w.coupon_apply_amt, 0)) as coupon_com_amt,
                        if(w.ord_state = 60 or w.ord_state = 61, 0, w.dlv_amt ) as dlv_amt,
                        if(w.ord_state = 60 or w.ord_state = 61 ,
                            ( -1 * ifnull(w.dlv_ret_amt,0) + -1 * ifnull(w.dlv_add_amt,0) + ifnull(w.dlv_enc_amt,0) + ifnull(w.dlv_pay_amt,0)),0) as dlv_ret_amt,
                        if(w.ord_state = 60 or w.ord_state = 61, -1, 0 ) * w.recv_amt as ret_amt,
                        w.wonga,
                        ( w.price - w.wonga ) / w.price  * 100 as fee,
                        ( w.price - w.wonga ) * qty as sale_amt
                    from order_opt_wonga w
                    where w.ord_state_date >= '${sdate}' and w.ord_state_date <= '${edate}' $inner_where
                    order by ord_state_date,ord_opt_no
                ) w inner join order_opt o on w.ord_opt_no = o.ord_opt_no and o.ord_state >= '${state}'
                    inner join order_mst m on o.ord_no = m.ord_no
                    left outer join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                    left outer join payment p on o.ord_no = p.ord_no
                    left outer join claim c on o.ord_opt_no = c.ord_opt_no
                    left outer join company e on w.com_id = e.com_id
                    left outer join company f on o.sale_place = f.com_id
                    left outer join code ord_type on ord_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = ord_type.code_id
                    left outer join code ord_kind on ord_kind.code_kind_cd = 'G_ORD_KIND' and o.ord_kind = ord_kind.code_id
                    left outer join code ord_state on ord_state.code_kind_cd IN ( 'G_ORD_STATE', 'G_CLM_STATE') and w.ord_state = ord_state.code_id
                    left outer join code pay_type on pay_type.code_kind_cd = 'G_PAY_TYPE' and p.pay_type = pay_type.code_id
                    left outer join code com_type on com_type.code_kind_cd = 'G_COM_TYPE' and e.com_type = com_type.code_id
                    $insql
            where 1 = 1 and o.ord_state >= '${state}' $where
        ";

        $rows = DB::select($sql);

        /**
         * 조회한 데이터에서 필요한 정보들을 추가 또는 가공
         */
        $rows = collect($rows)->map(function($row, $index) {
            $row->index = $index;
            return $row;
        })->all();

        /**
         * 프론트에서 그룹별 색깔 구분 표시를 위해 행별로 boolean 값 추가
         */
        collect($rows)->reduce(function($carry, $item) use ($rows) {

            $no = $item->ord_no;
            $index = $item->index;
            $is_green = $carry['is_green'];

            if ($index == 0) {
                $rows[$index]->is_group = false; // 첫번째 is_group 속성은 false - 추후 판별
                $rows[$index]->is_green = true; // 첫번째 is_green 속성은 true으로 고정 (그룹핑에 상관없이)
                return [ 'no' => $no, 'is_green' => $is_green ];
            }

            if ($index > 0) {
                // 그룹핑이 인접한 경우 색깔 스위칭
                $prev_no = $carry['no'];
                if ( $no == $prev_no ) {
                    $rows[$index-1]->is_group = true;
                    $rows[$index]->is_group = true;
                    $rows[$index]->is_green = $rows[$index-1]->is_green;
                } else {
                    $rows[$index]->is_group = false;
                    $is_green = !$is_green;
                    $rows[$index]->is_green = $is_green;
                }
            }

            return [ 'no' => $no, 'is_green' => $is_green];

        }, ['is_green' => true]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);


    }
}
