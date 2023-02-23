<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function index() {

        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'week')->format('Y-m-d');
        $edate = date("Y-m-d");

        $startdate = $mutable->sub(1, 'week')->format('Ymd');
        $enddate = date("Ymd");

        $sql = "
        select
            date_format(a.sale_date,'%Y%m%d') as date,
            date_format(a.sale_date,'%m') as month,
            date_format(a.sale_date,'%d') as day,
            date_format(a.sale_date,'%a') as yoil_nm,
            DAYOFWEEK(a.sale_date) as yoil,
            t.*,
            (qty_30 + qty_60 + qty_61) as sum_qty,
            (t.recv_amt_30 + t.recv_amt_60 + t.recv_amt_61) as sum_recv_amt,
            (t.wonga_30 + t.wonga_60 + t.wonga_61) as sum_wonga,
            (t.point_amt_30 + t.point_amt_60 + t.point_amt_61) as sum_point_amt,
            (t.coupon_amt_30 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt,
            (t.fee_amt_30 + t.fee_amt_60 + t.fee_amt_61 ) as sum_fee_amt,
            (t.dc_amt_30 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt,
            (t.taxation_amt_30 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt,
            (t.tax_amt_30 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt,
            ifnull(p.pg_fee,0) as exp_pg_fee
        from (
        select d as sale_date from mdate where d >='$startdate' and d <= '$enddate' order by sale_date desc
        ) a left outer join (
            select
                b.sale_date
                , sum(if(ord_state = '10', ifnull(b.qty, 0), 0)) as qty_30
                , sum(if(ord_state = '10', ifnull(b.recv_amt, 0), 0)) as recv_amt_30
                , sum(if(ord_state = '10', ifnull(b.point_amt, 0), 0)) as point_amt_30
                , sum(if(ord_state = '10', ifnull(b.coupon_amt, 0), 0)) as coupon_amt_30
                , sum(if(ord_state = '10', ifnull(b.dc_amt, 0), 0)) as dc_amt_30
                , sum(if(ord_state = '10', ifnull(b.fee_amt,0), 0)) as fee_amt_30
                , sum(if(ord_state = '10', ifnull(b.wonga,0), 0)) as wonga_30
                , sum(if(ord_state = '10', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_30
                , sum(if(ord_state = '10', ifnull(b.tax_amt, 0), 0)) as tax_amt_30

                , sum(if(ord_state = 60, ifnull(b.qty, 0), 0)) * -1 as qty_60
                , sum(if(ord_state = 60, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_60
                , sum(if(ord_state = 60, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_60
                , sum(if(ord_state = 60, ifnull(b.coupon_amt, 0), 0)) * -1 as coupon_amt_60
                , sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
                , sum(if(ord_state = 60, ifnull(b.wonga, 0), 0)) as wonga_60
                , sum(if(ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_60
                , sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60
                , sum(if(ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60

                , sum(if(ord_state = 61, ifnull(b.qty, 0), 0)) * -1 as qty_61
                , sum(if(ord_state = 61, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_61
                , sum(if(ord_state = 61, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_61
                , sum(if(ord_state = 61, ifnull(b.coupon_amt, 0), 0)) * -1  as coupon_amt_61
                , sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
                , sum(if(ord_state = 61, ifnull(b.wonga, 0), 0))  as wonga_61
                , sum(if(ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_61
                , sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61
                , sum(if(ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61
            from (
            select
                w.ord_state_date as sale_date, w.ord_state
                , sum(w.qty)as qty
                , sum(w.recv_amt) as recv_amt
                , sum(w.point_apply_amt) as point_amt
                , sum(w.wonga * w.qty) as wonga
                , sum(w.coupon_apply_amt) as coupon_amt
                , sum(w.sales_com_fee) as fee_amt
                , sum(w.dc_apply_amt) as dc_amt
                , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', w.recv_amt + w.point_apply_amt - w.sales_com_fee, 0)) as taxation_amt
                , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11), 0)) as tax_amt
                , o.store_cd
                , o.sale_kind
                , o.pr_code
                , g.brand
            from order_opt o
                inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                left outer join company c on o.sale_place = c.com_id
            where
                w.ord_state_date >= '$startdate' and w.ord_state_date <= '$enddate'
                and w.ord_state in ('10',60,61)
                and o.ord_state >= '10'
                and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )  
                    group by w.ord_state_date, w.ord_state
                ) b group by b.sale_date
            ) t on a.sale_date = t.sale_date left outer join (
                select
                    ord_state_date,
                    sum(cal_pg_fee(a.ord_state,a.ord_state_date,p.pay_type,p.pay_amt,p.pay_date,a.refund_amt)) as pg_fee
                from (
                    select
                        o.ord_no,ord_state_date,
                        w.ord_state,
                        sum(if(clm.refund_yn = 'y',refund_amt,0)) as refund_amt
                    from order_opt o
                        inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                        inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                        left outer join company c on o.sale_place = c.com_id
                        left outer join claim clm on w.ord_opt_no = clm.ord_opt_no
                    where
                        w.ord_state_date >= '$startdate' and w.ord_state_date <= '$enddate'
                        and w.ord_state in ('10',60,61)
                        and o.ord_state >= '10'
                        and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )  
                        group by o.ord_no,w.ord_state,ord_state_date
                ) a inner join order_mst m on a.ord_no = m.ord_no
                inner join payment p on m.ord_no = p.ord_no
                where m.ord_type = 0 && m.sale_place = 'HEAD_OFFICE'  && p.tno <> ''
                group by a.ord_state_date
                ) p on a.sale_date = p.ord_state_date
        ";

        $rows = DB::select($sql);

        $result = collect($rows)->map(function ($row) {

			$sale_date		= $row->date;

			$sum_point		= $row->sum_point_amt;	//포인트 합계
			$sum_fee		= $row->sum_fee_amt;		//수수료 합계
			$sum_recv		= $row->sum_recv_amt;		//무통장 또는 카드 합계
			$sum_wonga		= (int)$row->sum_wonga;		//원가 합계
			$sum_amt		= $sum_recv + $sum_point - $sum_fee;


			$array = array(
				"date"			=> $sale_date,
				"month"			=> $row->month,
				"day"			=> $row->day,

				"sum_point"		=> ($sum_point) ? $sum_point:0,
				"sum_fee"	=> ($sum_fee) ? $sum_fee:0,
				"sum_recv"		=> ($sum_recv) ? $sum_recv:0,
				"sum_amt"		=> ($sum_amt) ? $sum_amt:0,
				"sum_wonga"		=> ($sum_wonga) ? $sum_wonga:0,
			);

			return $array;

		})->all();


        $sql = "
        select
            a.store_cd as store_cd, a.store_nm as store_nm
            , t.*
            , (qty_10 + qty_60 + qty_61) as sum_qty
            , (t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) as sum_recv_amt
            , (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) as sum_point_amt
            , (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_fee_amt
            ,(t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) + (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) - (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_amt
            , (t.wonga_10 + t.wonga_60 + t.wonga_61) as sum_wonga
            , (t.coupon_amt_10 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt
            , (t.dc_amt_10 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt
            , (t.taxation_amt_10 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt
            , (t.tax_amt_10 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt
            from ( select store_cd, store_nm from store where 1=1 ) a inner join (
                select
                    b.store_cd
                    , sum(if(b.ord_state = 10, ifnull(b.qty,0), 0)) as qty_10
                    , sum(if(b.ord_state = 10, ifnull(b.point_amt,0), 0)) as point_amt_10
                    , sum(if(b.ord_state = 10, ifnull(b.coupon_amt,0), 0)) as coupon_amt_10
                    , sum(if(b.ord_state = 10, ifnull(b.dc_amt, 0), 0)) as dc_amt_10
                    , sum(if(b.ord_state = 10, ifnull(b.fee_amt,0), 0)) as fee_amt_10
                    , sum(if(b.ord_state = 10, ifnull(b.recv_amt,0), 0)) as recv_amt_10
                    , sum(if(b.ord_state = 10, ifnull(b.wonga,0), 0)) as wonga_10
                    , sum(if(b.ord_state = 10, ifnull(b.taxation_amt, 0), 0)) as taxation_amt_10
                    , sum(if(b.ord_state = 10, ifnull(b.tax_amt, 0), 0)) as tax_amt_10

                    , sum(if(b.ord_state = 60, ifnull(b.qty,0), 0)) * -1 as qty_60
                    , sum(if(b.ord_state = 60, ifnull(b.point_amt,0), 0)) * -1 as point_amt_60
                    , sum(if(b.ord_state = 60, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_60
                    , sum(if(b.ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_60
                    , sum(if(b.ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
                    , sum(if(b.ord_state = 60, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_60
                    , sum(if(b.ord_state = 60, ifnull(b.wonga,0), 0)) as wonga_60
                    , sum(if(b.ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60
                    , sum(if(b.ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60

                    , sum(if(b.ord_state = 61, ifnull(b.qty,0), 0)) * -1 as qty_61
                    , sum(if(b.ord_state = 61, ifnull(b.point_amt,0), 0)) * -1 as point_amt_61
                    , sum(if(b.ord_state = 61, ifnull(b.coupon_amt,0), 0)) * -1 as coupon_amt_61
                    , sum(if(b.ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_61
                    , sum(if(b.ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
                    , sum(if(b.ord_state = 61, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_61
                    , sum(if(b.ord_state = 61, ifnull(b.wonga,0), 0))  as wonga_61
                    , sum(if(b.ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61
                    , sum(if(b.ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61
                from  (
                    select
                        s.store_cd
                        , w.ord_state
                        , sum(w.qty)as qty
                        , sum(w.recv_amt) as recv_amt
                        , sum(w.point_apply_amt) as point_amt
                        , sum(w.wonga * w.qty) as wonga
                        , sum(w.coupon_apply_amt) as coupon_amt
                        , sum(w.sales_com_fee) as fee_amt
                        , sum(w.dc_apply_amt) as dc_amt
                        , sum(if( ifnull(g.tax_yn,'Y') = 'Y',w.recv_amt + w.point_apply_amt - w.sales_com_fee,0)) as taxation_amt
                        , sum(if( ifnull(g.tax_yn,'Y') = 'Y',floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11),0)) as tax_amt
                        , o.sale_kind
                        , o.pr_code
                        , s.store_type
                        , s.store_nm
                    from
                        order_opt o
                        inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                        inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                        inner join company c on w.com_id = c.com_id
                        inner join store s on s.store_cd = o.store_cd
                    where
                        w.ord_state_date >= '$startdate' 
                        and w.ord_state_date <= '$enddate' and w.ord_state in (10,60,61)
                        and o.ord_state >= 10
                        and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )   
                        group by store_cd,ord_state
                ) b group by b.store_cd
                ) t on a.store_cd = t.store_cd 
                order by sum_amt desc
        ";

        $piechart = DB::select($sql);

        $values = [
            'sdate' => $sdate,
            'edate' => $edate,
            'result' => $result,
            'pieResult' => $piechart
            
        ];


        return view(Config::get('shop.shop.view'). '/index',$values);
    }

    // 공지사항
    public function main() {

        $mutable = Carbon::now();
        $sdate = $mutable->sub(6, 'month')->format('Y-m-d');
        $edate = date("Y-m-d");
        $user_store = Auth('head')->user()->store_cd;
        $user_store_nm = Auth('head')->user()->store_nm;

        $sql = "
            select 
                s.ns_cd,
                s.subject,
                s.content,
                s.admin_id,
                s.admin_nm,
                s.admin_email,
                s.cnt,
                s.all_store_yn,
                group_concat(a.store_nm separator ', ') as stores,
                s.rt,
                c.code_val as store_type_nm,
                s.ut
            from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store a on a.store_cd = d.store_cd
                left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
            where s.rt >= '$sdate' and s.rt < date_add( '$edate', interval 1 day)
            group by s.ns_cd
            having stores like '%$user_store_nm%'
            order by s.rt desc
            
        ";

        $result = DB::select($sql); 


        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);

    }


    public function main_alarm () {

        $mutable = Carbon::now();
        $sdate = $mutable->sub(1, 'month')->format('Y-m-d');
        $edate = date("Y-m-d");
        $user_store = Auth('head')->user()->store_cd;

        if ($user_store == 'L0025') {
            $receiver_type = 'H';
        } else {
            $receiver_type = 'S';
        }

        $sql = "
            select 
                m.msg_cd,
                m.sender_cd,
                s.store_nm as sender_nm,
                s.phone as mobile,
                m.content,
                md.rt,
                md.check_yn
            from msg_store_detail md
                left outer join msg_store m on m.msg_cd = md.msg_cd
                left outer join store s on s.store_cd = m.sender_cd
            where md.receiver_type = '$receiver_type' and md.receiver_cd = '$user_store'
            and m.rt >= '$sdate' and m.rt < date_add('$edate', interval 1 day) and m.del_yn = 'N'
            group by md.msg_cd
            limit 0, 5
        
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function chart() {

        $mutable = Carbon::now();
        $startdate = $mutable->sub(7, 'day')->format('Ymd');
        $enddate = date("Ymd");

        $sql = "
            select
                date_format(a.sale_date,'%Y%m%d') as date,
                date_format(a.sale_date,'%m') as month,
                date_format(a.sale_date,'%d') as day,
                date_format(a.sale_date,'%a') as yoil_nm,
                DAYOFWEEK(a.sale_date) as yoil,
                t.*,
                (qty_30 + qty_60 + qty_61) as sum_qty,
                (t.recv_amt_30 + t.recv_amt_60 + t.recv_amt_61) as sum_recv_amt,
                (t.wonga_30 + t.wonga_60 + t.wonga_61) as sum_wonga,
                (t.point_amt_30 + t.point_amt_60 + t.point_amt_61) as sum_point_amt,
                (t.coupon_amt_30 + t.coupon_amt_60 + t.coupon_amt_61) as sum_coupon_amt,
                (t.fee_amt_30 + t.fee_amt_60 + t.fee_amt_61 ) as sum_fee_amt,
                (t.dc_amt_30 + t.dc_amt_60 + t.dc_amt_61) as sum_dc_amt,
                (t.taxation_amt_30 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt,
                (t.tax_amt_30 + t.tax_amt_60 + t.tax_amt_61) as sum_tax_amt,
                ifnull(p.pg_fee,0) as exp_pg_fee
            from (
            select d as sale_date from mdate where d >='$startdate' and d <= '$enddate' order by sale_date desc
            ) a left outer join (
                select
                    b.sale_date
                    , sum(if(ord_state = '10', ifnull(b.qty, 0), 0)) as qty_30
                    , sum(if(ord_state = '10', ifnull(b.recv_amt, 0), 0)) as recv_amt_30
                    , sum(if(ord_state = '10', ifnull(b.point_amt, 0), 0)) as point_amt_30
                    , sum(if(ord_state = '10', ifnull(b.coupon_amt, 0), 0)) as coupon_amt_30
                    , sum(if(ord_state = '10', ifnull(b.dc_amt, 0), 0)) as dc_amt_30
                    , sum(if(ord_state = '10', ifnull(b.fee_amt,0), 0)) as fee_amt_30
                    , sum(if(ord_state = '10', ifnull(b.wonga,0), 0)) as wonga_30
                    , sum(if(ord_state = '10', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_30
                    , sum(if(ord_state = '10', ifnull(b.tax_amt, 0), 0)) as tax_amt_30

                    , sum(if(ord_state = 60, ifnull(b.qty, 0), 0)) * -1 as qty_60
                    , sum(if(ord_state = 60, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_60
                    , sum(if(ord_state = 60, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_60
                    , sum(if(ord_state = 60, ifnull(b.coupon_amt, 0), 0)) * -1 as coupon_amt_60
                    , sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
                    , sum(if(ord_state = 60, ifnull(b.wonga, 0), 0)) as wonga_60
                    , sum(if(ord_state = 60, ifnull(b.dc_amt, 0), 0)) * -1 as dc_amt_60
                    , sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60
                    , sum(if(ord_state = 60, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_60

                    , sum(if(ord_state = 61, ifnull(b.qty, 0), 0)) * -1 as qty_61
                    , sum(if(ord_state = 61, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_61
                    , sum(if(ord_state = 61, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_61
                    , sum(if(ord_state = 61, ifnull(b.coupon_amt, 0), 0)) * -1  as coupon_amt_61
                    , sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
                    , sum(if(ord_state = 61, ifnull(b.wonga, 0), 0))  as wonga_61
                    , sum(if(ord_state = 61, ifnull(b.dc_amt, 0), 0)) * -1  as dc_amt_61
                    , sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61
                    , sum(if(ord_state = 61, ifnull(b.tax_amt, 0), 0)) * -1 as tax_amt_61
                from (
                select
                    w.ord_state_date as sale_date, w.ord_state
                    , sum(w.qty)as qty
                    , sum(w.recv_amt) as recv_amt
                    , sum(w.point_apply_amt) as point_amt
                    , sum(w.wonga * w.qty) as wonga
                    , sum(w.coupon_apply_amt) as coupon_amt
                    , sum(w.sales_com_fee) as fee_amt
                    , sum(w.dc_apply_amt) as dc_amt
                    , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', w.recv_amt + w.point_apply_amt - w.sales_com_fee, 0)) as taxation_amt
                    , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', floor((w.recv_amt + w.point_apply_amt - w.sales_com_fee)/11), 0)) as tax_amt
                    , o.store_cd
                    , o.sale_kind
                    , o.pr_code
                    , g.brand
                from order_opt o
                    inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                    inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                    left outer join company c on o.sale_place = c.com_id
                where
                    w.ord_state_date >= '$startdate' and w.ord_state_date <= '$enddate'
                    and w.ord_state in ('10',60,61)
                    and o.ord_state >= '10'
                    and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )  
                        group by w.ord_state_date, w.ord_state
                    ) b group by b.sale_date
            ) t on a.sale_date = t.sale_date left outer join (
                select
                    ord_state_date,
                    sum(cal_pg_fee(a.ord_state,a.ord_state_date,p.pay_type,p.pay_amt,p.pay_date,a.refund_amt)) as pg_fee
                from (
                    select
                        o.ord_no,ord_state_date,
                        w.ord_state,
                        sum(if(clm.refund_yn = 'y',refund_amt,0)) as refund_amt
                    from order_opt o
                        inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                        inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                        left outer join company c on o.sale_place = c.com_id
                        left outer join claim clm on w.ord_opt_no = clm.ord_opt_no
                    where
                        w.ord_state_date >= '$startdate' and w.ord_state_date <= '$enddate'
                        and w.ord_state in ('10',60,61)
                        and o.ord_state >= '10'
                        and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )  
                        group by o.ord_no,w.ord_state,ord_state_date
                ) a inner join order_mst m on a.ord_no = m.ord_no
                inner join payment p on m.ord_no = p.ord_no
                where m.ord_type = 0 && m.sale_place = 'HEAD_OFFICE'  && p.tno <> ''
                group by a.ord_state_date
                ) p on a.sale_date = p.ord_state_date
        ";

        $rows = DB::select($sql);

        $result = collect($rows)->map(function ($row) {

			$sale_date		= $row->date;

			$sum_point		= $row->sum_point_amt;	//포인트 합계
			$sum_fee		= $row->sum_fee_amt;		//수수료 합계
			$sum_recv		= $row->sum_recv_amt;		//무통장 또는 카드 합계
			$sum_wonga		= (int)$row->sum_wonga;		//원가 합계
			$sum_amt		= $sum_recv + $sum_point - $sum_fee;

			$array = array(
				"date"			=> $sale_date,
				"month"			=> $row->month,
				"day"			=> $row->day,
				"sum_point"		=> ($sum_point) ? $sum_point:0,
				"sum_fee"	=> ($sum_fee) ? $sum_fee:0,
				"sum_recv"		=> ($sum_recv) ? $sum_recv:0,
				"sum_amt"		=> ($sum_amt) ? $sum_amt:0,
				"sum_wonga"		=> ($sum_wonga) ? $sum_wonga:0,
			);

			return $array;

		})->all();

        return response()->json([
            "code" => 200,
            "body" => $result
        ]);
    }
}

