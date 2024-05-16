<?php

namespace App\Http\Controllers\store;

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

        $mutable2 = Carbon::now();
        $startdate = $mutable2->sub(1, 'week')->format('Ymd');
        $enddate = date("Ymd");

        $mutable3 = Carbon::now();
        $sdate2 = $mutable3->sub(1, 'month')->format('Y-m-d');
        $edate2 = date("Y-m-d");

		$mutable4 = Carbon::now();
		$sdate3 = $mutable4->sub(1, 'week')->format('Y-m-d');
		$edate3 = date("Y-m-d");

        $sql = "
            select
                date_format(a.sale_date,'%Y%m%d') as date,
                (t.recv_amt_30 + t.recv_amt_60 + t.recv_amt_61) as sum_recv_amt,
                (t.wonga_30 + t.wonga_60 + t.wonga_61) as sum_wonga,
                (t.point_amt_30 + t.point_amt_60 + t.point_amt_61) as sum_point_amt,
                (t.fee_amt_30 + t.fee_amt_60 + t.fee_amt_61 ) as sum_fee_amt,
            	(t.taxation_amt_30 + t.taxation_amt_60 + t.taxation_amt_61) as sum_taxation_amt
            from (
                select d as sale_date from mdate where d >='$startdate' and d <= '$enddate' order by sale_date desc
            ) a 
            left outer join (
                select
                    b.sale_date
                    , sum(if(ord_state = '10', ifnull(b.recv_amt, 0), 0)) as recv_amt_30
                    , sum(if(ord_state = '10', ifnull(b.point_amt, 0), 0)) as point_amt_30
                    , sum(if(ord_state = '10', ifnull(b.fee_amt,0), 0)) as fee_amt_30
                    , sum(if(ord_state = '10', ifnull(b.wonga,0), 0)) as wonga_30
              		, sum(if(ord_state = '10', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_30
                    
                    , sum(if(ord_state = 60, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_60
                    , sum(if(ord_state = 60, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_60
                    , sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
                    , sum(if(ord_state = 60, ifnull(b.wonga, 0), 0)) as wonga_60
              		, sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60
                                    
                    , sum(if(ord_state = 61, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_61
                    , sum(if(ord_state = 61, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_61
                    , sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
                    , sum(if(ord_state = 61, ifnull(b.wonga, 0), 0))  as wonga_61
            		, sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61
                from (
                    select
                        w.ord_state_date as sale_date, w.ord_state
                        , sum(w.recv_amt) as recv_amt
                        , sum(w.point_apply_amt) as point_amt
                        , sum(w.wonga * w.qty) as wonga
                        , sum(w.sales_com_fee) as fee_amt
            			, sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', w.recv_amt + w.point_apply_amt - w.sales_com_fee, 0)) as taxation_amt
                    from order_opt o
                        inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
            			inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                    where
                        w.ord_state_date >= '$startdate' and w.ord_state_date <= '$enddate'
                        and w.ord_state in ('10',60,61)
                        and o.ord_state >= '10'
                        and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )  
                            group by w.ord_state_date, w.ord_state
                    ) b group by b.sale_date
                ) t on a.sale_date = t.sale_date
        ";

        $rows = DB::select($sql);

        $result = collect($rows)->map(function ($row) {

			$sale_date				= $row->date;
			$sum_point				= $row->sum_point_amt;	//포인트 합계
			$sum_fee				= $row->sum_fee_amt;		//수수료 합계
			$sum_recv				= $row->sum_recv_amt;		//무통장 또는 카드 합계
			$sum_wonga				= (int)$row->sum_wonga;		//원가 합계
			$sum_taxation			= $row->sum_taxation_amt;
			$sum_taxation_no_vat	= round($sum_taxation/1.1);
			$vat 					= $sum_taxation - $sum_taxation_no_vat;
//			$sum_amt				= $sum_recv + $sum_point - $sum_fee;
			$sum_amt				= $sum_recv;


			$array = array(
				"date"			=> $sale_date,
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
                , (t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) as sum_recv_amt
                , (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) as sum_point_amt
                , (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_fee_amt
                ,(t.recv_amt_10 + t.recv_amt_60 + t.recv_amt_61 ) + (t.point_amt_10 + t.point_amt_60 + t.point_amt_61 ) - (t.fee_amt_10 + t.fee_amt_60 + t.fee_amt_61) as sum_amt
                , (t.wonga_10 + t.wonga_60 + t.wonga_61) as sum_wonga
            from ( select store_cd, store_nm from store where 1=1 ) a 
                inner join (
                    select
                        b.store_cd
                        , sum(if(b.ord_state = 10, ifnull(b.point_amt,0), 0)) as point_amt_10
                        , sum(if(b.ord_state = 10, ifnull(b.fee_amt,0), 0)) as fee_amt_10
                        , sum(if(b.ord_state = 10, ifnull(b.recv_amt,0), 0)) as recv_amt_10
                        , sum(if(b.ord_state = 10, ifnull(b.wonga,0), 0)) as wonga_10

                        , sum(if(b.ord_state = 60, ifnull(b.point_amt,0), 0)) * -1 as point_amt_60
                        , sum(if(b.ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
                        , sum(if(b.ord_state = 60, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_60
                        , sum(if(b.ord_state = 60, ifnull(b.wonga,0), 0)) as wonga_60

                        , sum(if(b.ord_state = 61, ifnull(b.point_amt,0), 0)) * -1 as point_amt_61
                        , sum(if(b.ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
                        , sum(if(b.ord_state = 61, ifnull(b.recv_amt,0), 0)) * -1 as recv_amt_61
                        , sum(if(b.ord_state = 61, ifnull(b.wonga,0), 0))  as wonga_61
                    from  (
                        select
                            s.store_cd
                            , w.ord_state
                            , sum(w.recv_amt) as recv_amt
                            , sum(w.point_apply_amt) as point_amt
                            , sum(w.wonga * w.qty) as wonga
                            , sum(w.sales_com_fee) as fee_amt
                        from
                            order_opt o
                            inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
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

        //주문금액
        $sql = "
            select
                p.prd_nm, p.style_no, pc.prd_cd_p,
                sum(oo.wonga * oo.qty) as wonga, sum(oo.recv_amt) as recv_amt
            from order_opt oo
            inner join product_code pc on oo.prd_cd = pc.prd_cd
            inner join product p on oo.prd_cd = p.prd_cd
            where
                oo.ord_state >= '30' and ( oo.clm_state = '' or oo.clm_state is null or oo.clm_state = '-30' or oo.clm_state = '90' )
                and oo.ord_date >= '$sdate2 00:00:00' and oo.ord_date <= '$edate2 23:59:59'
            group by pc.prd_cd
            order by sum(oo.recv_amt) desc
            limit 10
        ";

        $chart2Result = DB::select($sql);

        // 주문수량
        $sql = "
            select
                p.prd_nm, p.style_no, pc.prd_cd_p,
                sum(oo.qty) as qty
            from order_opt oo
            inner join product_code pc on oo.prd_cd = pc.prd_cd
            inner join product p on oo.prd_cd = p.prd_cd
            where
                oo.ord_state >= '30' and ( oo.clm_state = '' or oo.clm_state is null or oo.clm_state = '-30' or oo.clm_state = '90' )
                and oo.ord_date >= '$sdate2 00:00:00' and oo.ord_date <= '$edate2 23:59:59'
            group by pc.prd_cd
            order by sum(oo.qty) desc
            limit 10
        ";

        $chart3Result = DB::select($sql);

        $values = [
            'sdate' 		=> $sdate,
            'edate' 		=> $edate,
            'sdate2' 		=> $sdate2,
            'edate2' 		=> $edate2,
			'sdate3'		=> $sdate3,
			'edate3' 		=> $edate3,
            'result' 		=> $result,
            'pieResult' 	=> $piechart,
            'chart2Result' 	=> $chart2Result,
            'chart3Result' 	=> $chart3Result
            
        ];


        return view(Config::get('shop.store.view'). '/index',$values);
    }

    // 공지사항
    public function main() {

        // $mutable = Carbon::now();
        // $sdate = $mutable->sub(6, 'month')->format('Y-m-d');
        // $edate = date("Y-m-d");
		
		$notice_type = '01'; // 공지타입 (01: 매장공지사항 / 02: VMD게시글)

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
                if ((select date_add(s.rt, interval 10 day)) < now(), 'false', 'true') as check_new_notice,
                s.rt,
                c.code_val as store_type_nm,
                s.ut,
                (case when ifnull(char_length(s.attach_file_url), 0) > 0 then 'Y' else 'N' end ) as attach_file_yn
            from notice_store s 
                left outer join notice_store_detail d on s.ns_cd = d.ns_cd
                left outer join store a on a.store_cd = d.store_cd
                left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
            where s.store_notice_type = :notice_type
            group by s.ns_cd
            order by s.rt desc
            limit 0, 10
        ";
        $result = DB::select($sql, [ 'notice_type' => $notice_type ]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);

    }


    public function main_alarm () {
		$user_store = Auth('head')->user()->store_cd;
		$user_id = Auth('head')->user()->id;

        $sql = "
            select 
				 m.msg_cd,
				m.sender_cd,
				if(m.sender_type = 'S', s.store_nm, if(m.sender_type = 'H', mu.name,'')) as sender_nm,
				s.phone as mobile,
				m.content,
				md.rt,
				md.check_yn
			from msg_store_detail md
				left outer join msg_store m on m.msg_cd = md.msg_cd
				left outer join store s on s.store_cd = m.sender_cd
				left outer join mgr_user mu on mu.id = m.sender_cd
            where (md.receiver_cd = '$user_store' or md.receiver_cd = '$user_id')
            and (m.reservation_yn <> 'Y' or m.reservation_date <= now())
            group by md.msg_cd
            order by md.rt desc
            limit 0, 10
        
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
}
