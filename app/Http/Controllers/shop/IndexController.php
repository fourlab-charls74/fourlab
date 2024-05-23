<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\S_Release;
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

        $user_store = Auth('head')->user()->store_cd;

        $sql = "
        select
            date_format(a.sale_date,'%Y%m%d') as date,
            (t.recv_amt_30 + t.recv_amt_60 + t.recv_amt_61) as sum_recv_amt,
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
                , sum(if(ord_state = '10', ifnull(b.taxation_amt, 0), 0)) as taxation_amt_30

                , sum(if(ord_state = 60, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_60
                , sum(if(ord_state = 60, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_60
                , sum(if(ord_state = 60, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_60
                , sum(if(ord_state = 60, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_60

                , sum(if(ord_state = 61, ifnull(b.recv_amt, 0), 0)) * -1 as recv_amt_61
                , sum(if(ord_state = 61, ifnull(b.point_amt, 0), 0)) * -1 as point_amt_61
                , sum(if(ord_state = 61, ifnull(b.fee_amt,0), 0)) * 1 as fee_amt_61
                , sum(if(ord_state = 61, ifnull(b.taxation_amt, 0), 0)) * -1 as taxation_amt_61
            from (
                select
                    w.ord_state_date as sale_date, w.ord_state
                    , sum(w.recv_amt) as recv_amt
                    , sum(w.point_apply_amt) as point_amt
                    , sum(w.sales_com_fee) as fee_amt
                    , sum(if( if(ifnull(g.tax_yn,'')='','Y', g.tax_yn) = 'Y', w.recv_amt + w.point_apply_amt - w.sales_com_fee, 0)) as taxation_amt
                from order_opt o
                    inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
                    inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
                where
                    w.ord_state_date >= '$startdate' and w.ord_state_date <= '$enddate'
                    and o.store_cd = '$user_store'
                    and w.ord_state in ('10',60,61)
                    and o.ord_state >= '10'
                    and (  o.ord_type = '5'  or  o.ord_type = '4'  or  o.ord_type = '3'  or  o.ord_type = '13'  or  o.ord_type = '12'  or  o.ord_type = '17'  or  o.ord_type = '14'  or  o.ord_type = '15'  or o.ord_type = '0'  or  o.ord_type = '16'  )  
                        group by w.ord_state_date, w.ord_state
                    ) b group by b.sale_date
            ) t on a.sale_date = t.sale_date 
        ";

        //$rows = DB::select($sql);
/*
        $result = collect($rows)->map(function ($row) {

			$sale_date				= $row->date;
			$sum_point				= $row->sum_point_amt;	//포인트 합계
			$sum_fee				= $row->sum_fee_amt;		//수수료 합계
			$sum_recv				= $row->sum_recv_amt;		//무통장 또는 카드 합계
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
			);

			return $array;

		})->all();
*/

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

        //$piechart = DB::select($sql);

         //주문금액
         $sql = "
         select
             p.style_no as prd_nm, p.style_no, pc.prd_cd_p,
             sum(oo.wonga * oo.qty) as wonga, sum(oo.recv_amt) as recv_amt
         from order_opt oo
         inner join product_code pc on oo.prd_cd = pc.prd_cd
         inner join product p on oo.prd_cd = p.prd_cd
         where
             oo.ord_state >= '10' and ( oo.clm_state = '' or oo.clm_state is null or oo.clm_state = '-30' or oo.clm_state = '90' )
             and oo.ord_date >= date_add(now(), interval -1 month)
             and oo.store_cd = '$user_store'
         group by pc.prd_cd_p
         order by sum(oo.recv_amt) desc
         limit 10
     ";

     $chart2Result = DB::select($sql);

     // 주문수량
     $sql = "
         select
             p.style_no as prd_nm, p.style_no, pc.prd_cd_p,
             sum(oo.qty) as qty
         from order_opt oo
         inner join product_code pc on oo.prd_cd = pc.prd_cd
         inner join product p on oo.prd_cd = p.prd_cd
         where
             oo.ord_state >= '10' and ( oo.clm_state = '' or oo.clm_state is null or oo.clm_state = '-30' or oo.clm_state = '90' )
             and oo.ord_date >= date_add(now(), interval -1 month)
             and oo.store_cd = '$user_store'
         group by pc.prd_cd_p
         order by sum(oo.qty) desc
         limit 10
     ";

    $chart3Result = DB::select($sql);

	$user = [
		'id'	=> Auth('head')->user()->id,
		'name'	=> Auth('head')->user()->name
	];

	$store_cd	= Auth('head')->user()->store_cd;
	$release	= new S_Release($user);
	$expire_release_cnt	= $release->getExpireRelease('30', $store_cd);
	


		// 매인 좌측상단

		// 1. 매출현황
		$mutable4 = Carbon::now();

		$sec_sdate	= date('Ym');
		$sec_pdate = $mutable4->sub(1, 'year')->format('Ym');
		
		$sql	= "
			select
				a.qty, a.offline, a.online, a.recv_amt
				, concat(round((a.offline / a.recv_amt) * 100,0), ' : ', round((a.online / a.recv_amt) * 100,0)) as offline_online_rate
				, ifnull(p.amt,0) as proj_amt
				, round(a.recv_amt / ifnull(p.amt,0) * 100, 1) as progress_proj_amt
				, b.recv_amt as prev_recv_amt
				, round(a.recv_amt / b.recv_amt * 100, 1) as growth_rate
			from (
				select
					o.store_cd,
					sum(if(w.ord_state > 30, o.qty * -1, o.qty)) as qty, 
					sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as recv_amt,  
					if (s.online_only_yn = 'Y', sum(case when o.sale_kind <> 81 or o.sale_kind is null then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end), sum(case when o.sale_kind = 81 then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end)) as online,
					if (s.online_only_yn = 'Y', sum(case when o.sale_kind = 81 then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end), sum(case when o.sale_kind <> 81 or o.sale_kind is null then (o.recv_amt * if(w.ord_state > 30, -1, 1)) else 0 end))  as offline
				from order_opt_wonga w
				inner join order_opt o on w.ord_opt_no = o.ord_opt_no and o.ord_state = '30'
				inner join store s on s.store_cd = o.store_cd
				where 
					w.ord_state in(30, 60, 61) 
					and w.ord_state_date like '" . $sec_sdate . "%'
					and o.store_cd = :store_cd1
					and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
			) a
			left outer join store_sales_projection p on p.ym = '$sec_sdate' and a.store_cd = p.store_cd
			left outer join (
				select
					o.store_cd,
					sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as recv_amt
				from order_opt_wonga w
				inner join order_opt o on w.ord_opt_no = o.ord_opt_no and o.ord_state = '30'
				inner join store s on s.store_cd = o.store_cd
				where 
					w.ord_state in(30, 60, 61) 
					and w.ord_state_date like '" . $sec_pdate . "%'
					and o.store_cd = :store_cd2
					and if( w.ord_state_date <= '20231109', o.sale_kind is not null, 1=1)
			) b on a.store_cd = b.store_cd
		";
		$main_order	= DB::selectOne($sql, ['store_cd1' => $user_store, 'store_cd2' => $user_store]);

		// 2. 출고현황
		$mutable5 = Carbon::now();
		$sec_pdate = $mutable5->sub(1, 'month')->format('Y-m-d');
		
		$sql	= "
			select
				sum(if(psr.type = 'F' and psr.state < 40, psr.qty, 0)) as f_ing_qty
				, sum(if(psr.type = 'F' and psr.state = 40, psr.qty, 0)) as f_end_qty
				, sum(if(psr.type = 'S' and psr.state < 40, psr.qty, 0)) as s_ing_qty
				, sum(if(psr.type = 'S' and psr.state = 40, psr.qty, 0)) as s_end_qty
				, sum(if(psr.type = 'R' and psr.state < 40, psr.qty, 0)) as r_ing_qty
				, sum(if(psr.type = 'R' and psr.state = 40, psr.qty, 0)) as r_end_qty
				, sum(if(psr.state < 40, psr.qty, 0)) as t_ing_qty
				, sum(if(psr.state = 40, psr.qty, 0)) as t_end_qty
			from product_stock_release psr
			where
				psr.store_cd = :store_cd
				and psr.req_rt >= '" . $sec_pdate . " 00:00:00'
				and psr.type <> 'G'
		";
		$main_release	= DB::selectOne($sql, ['store_cd' => $user_store]);
		
		// 3. RT현황
		$sql	= "
			select
				sum(out_rt_cnt) as out_rt_cnt
				, sum(out_req_cnt) as out_req_cnt
				, ifnull(round(sum(out_req_cnt)/sum(out_rt_cnt)*100),'-') as out_req_ratio
				-- , (sum(out_rt_cnt) - sum(out_req_cnt)) as out_ing_cnt
				, (sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt)) as out_end_cnt
				, ifnull(round((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt))/(sum(out_rt_cnt) - sum(out_req_cnt))*100),'-') as out_end_ratio
				, sum(out_rej_cnt) as out_rej_cnt
				, ifnull(round(sum(out_rej_cnt)/sum(out_rt_cnt)*100),'-') as out_rej_ratio
				, sum(in_rt_cnt) as in_rt_cnt
				, sum(in_req_cnt) as in_req_cnt
				, ifnull(round(sum(in_req_cnt)/sum(in_rt_cnt)*100),'-') as in_req_ratio
				-- , (sum(in_rt_cnt) - sum(in_req_cnt)) as in_ing_cnt
				, (sum(in_rec_cnt) + sum(in_prc_cnt) + sum(in_fin_cnt)) as in_end_cnt
				, sum(in_fin_cnt) as in_fin_cnt
				, ifnull(round((sum(in_rec_cnt) + sum(in_prc_cnt) + sum(in_fin_cnt))/(sum(in_rt_cnt) - sum(in_req_cnt))*100),'-') as in_end_ratio
				, sum(in_rej_cnt) as in_rej_cnt
				, ifnull(round(sum(in_rej_cnt)/sum(in_rt_cnt)*100),'-') as in_rej_ratio
			
				, concat(
					' ',
					ifnull(round((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt))/((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt)) + sum(in_fin_cnt)) * 100), '-'), 
					' : ',
					ifnull(round(sum(in_fin_cnt)/((sum(out_rec_cnt) + sum(out_prc_cnt) + sum(out_fin_cnt)) + sum(in_fin_cnt)) * 100), '-')
				) as rt_ratio
			from
			(
				select
					psr.dep_store_cd as store_cd
					, 1 as out_rt_cnt
					, if(psr.state = '10', 1, 0) as out_req_cnt
					, if(psr.state = '20', 1, 0) as out_rec_cnt
					, if(psr.state = '30', 1, 0) as out_prc_cnt
					, if(psr.state = '40', 1, 0) as out_fin_cnt
					, if(psr.state = '-10', 1, 0) as out_rej_cnt
					, 0 as in_rt_cnt
					, 0 as in_req_cnt
					, 0 as in_rec_cnt
					, 0 as in_prc_cnt
					, 0 as in_fin_cnt
					, 0 as in_rej_cnt
				from product_stock_rotation psr
				where
					psr.rt >= '" . $sec_pdate . " 00:00:00'
					and psr.dep_store_cd = :store_cd1
			
				union all
			
				select
					psr.store_cd as store_cd
					, 0 as out_rt_cnt
					, 0 as out_req_cnt
					, 0 as out_rec_cnt
					, 0 as out_prc_cnt
					, 0 as out_fin_cnt
					, 0 as out_rej_cnt
					, 1 as in_rt_cnt
					, if(psr.state = '10', 1, 0) as in_req_cnt
					, if(psr.state = '20', 1, 0) as in_rec_cnt
					, if(psr.state = '30', 1, 0) as in_prc_cnt
					, if(psr.state = '40', 1, 0) as in_fin_cnt
					, if(psr.state = '-10', 1, 0) as in_rej_cnt
				from product_stock_rotation psr
				where
					psr.rt >= '" . $sec_pdate . " 00:00:00'
					and psr.store_cd = :store_cd2
			) rt
		";
		$main_rotation	= DB::selectOne($sql, ['store_cd1' => $user_store, 'store_cd2' => $user_store]);
		
		$values = [
			'main_order'	=> $main_order,
			'main_release'	=> $main_release,
			'main_rotation'	=> $main_rotation,
			'sec_sdate'		=> date('Y-m'),
			'sdate'			=> $sdate,
			'edate'			=> $edate,
			'sdate2'		=> $sdate2,
			'edate2'		=> $edate2,
			//'result'		=> $result,
			//'pieResult'	=> $piechart,
			'chart2Result'	=> $chart2Result,
			'chart3Result'	=> $chart3Result,
			'expire_release_cnt'	=> $expire_release_cnt
		];

        return view(Config::get('shop.shop.view'). '/index',$values);
    }

    // 공지사항
    public function main() 
	{
        // $mutable = Carbon::now();
        // $sdate = $mutable->sub(6, 'month')->format('Y-m-d');
        // $edate = date("Y-m-d");
        $user_store = Auth('head')->user()->store_cd;
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
            where s.store_notice_type = :notice_type and (s.all_store_yn = 'Y' or d.store_cd = :user_store)
            group by s.ns_cd
            order by s.rt desc
            limit 0, 10  
        ";
        $result = DB::select($sql, [ 'notice_type' => $notice_type, 'user_store' => $user_store ]); 


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
		$user_id = Auth('head')->user()->id;

        $sql = "
            select 
                m.msg_cd,
				m.sender_cd,
				if(m.sender_type = 'S', mu.name, if(m.sender_type = 'U', mu.name, if(m.sender_type = 'H', mu.name,''))) as sender_nm,
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
            and m.rt >= '$sdate' and m.rt < date_add('$edate', interval 1 day) and m.del_yn = 'N'
            group by md.msg_cd
            order by m.rt desc
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

