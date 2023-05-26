<?php

namespace App\Http\Controllers\partner\cs;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cs01Controller extends Controller
{
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'day')->format('Y-m-d');

        $md_ids = DB::table('mgr_user')->where("md_yn",'=','Y')->orderBy("name")->get();

        $sale_places = DB::table('company')->where("com_type",'=','4')->orderBy("com_nm")->get();

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'md_ids' => $md_ids,
            'sale_places' => $sale_places
        ];
        return view( Config::get('shop.partner.view') . '/cs/cs01',$values);
    }

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;

        $sdate = $request->input('sdate',Carbon::now()->sub(7, 'day')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));

        /*
        echo "sdate : ". $sdate;
        echo "<br>";
        echo "edate : ". $edate;
        echo "<Br>";
        */
		
        $goods_nm		= $request->input("goods_nm");
        $date_type      = $request->input('date_type');
        $sale_place	    = $request->input("sale_place");
        $req_nm			= $request->input("req_nm");



        $user_nm		= $request->input("user_nm");
        $pay_nm			= $request->input("pay_nm");
        $stat_pay_type	= $request->input("stat_pay_type");
        $clm_type		= $request->input("clm_type");
        $clm_reason		= $request->input("clm_reason");
        $clm_state		= $request->input("clm_state");
        $ord_no			= $request->input("ord_no");
        $refund_yn		= $request->input("refund_yn");
        $refund_bank    = $request->input("refund_bank");

        $refund_nm      = $request->input("refund_nm");
        $head_desc      = $request->input("head_desc");
        $style_no       = $request->input("style_no");
        $limit          = $request->input("limit",100);

        $where = "";
        $clm_where = "";

        if($date_type == "10"){
			// 조건절 설정
			if( $sdate != "" ) $where .= " and a.req_date >= DATE_FORMAT('$sdate 00:00:00', '%Y-%m-%d %H:%i:%s') ";
			if( $edate != "" ) $where .= " and a.req_date <= DATE_FORMAT('$edate 23:59:59', '%Y-%m-%d %H:%i:%s') ";
		}else if($date_type == "20" ){
			// 조건절 설정
			if( $sdate != "" ) $where .= " and a.proc_date >= DATE_FORMAT('$sdate 00:00:00', '%Y-%m-%d %H:%i:%s') ";
			if( $edate != "" ) $where .= " and a.proc_date <= DATE_FORMAT('$edate 23:59:59', '%Y-%m-%d %H:%i:%s') ";
		}else if($date_type == "30" ){
			// 조건절 설정
			if( $sdate != "" ) $where .= " and a.end_date >= DATE_FORMAT('$sdate 00:00:00', '%Y-%m-%d %H:%i:%s') ";
			if( $edate != "" ) $where .= " and a.end_date <= DATE_FORMAT('$edate 23:59:59', '%Y-%m-%d %H:%i:%s') ";
		}else{
			// 조건절 설정
			if( $sdate != "" ) $where .= " and (a.req_date >= DATE_FORMAT('$sdate 00:00:00', '%Y-%m-%d %H:%i:%s') or a.proc_date >= DATE_FORMAT('$sdate 00:00:00', '%Y-%m-%d %H:%i:%s') or a.end_date >= DATE_FORMAT('$sdate 00:00:00', '%Y-%m-%d %H:%i:%s')) ";
			if( $edate != "" ) $where .= " and ((a.req_date <= DATE_FORMAT('$edate 23:59:59', '%Y-%m-%d %H:%i:%s')) or (a.proc_date <= DATE_FORMAT('$edate 23:59:59', '%Y-%m-%d %H:%i:%s')) or (a.end_date <=DATE_FORMAT('$edate 23:59:59', '%Y-%m-%d %H:%i:%s')))";
		}

		if( $user_nm != "" ) 		$where .= " and f.user_nm like '%". Lib::quote($user_nm)."%' ";
		if( $pay_nm != "" ) 		$where .= " and e.pay_nm like '%". Lib::quote($pay_nm)."%' ";

		if( $clm_type != "" ) 	$where .= " and a.clm_type = '$clm_type' ";
		if( $clm_reason != "" ) 	$where .= " and a.clm_reason = '$clm_reason' ";
		if( $clm_state != "" ) 	$where .= " and b.clm_state = '$clm_state' ";
		if( $ord_no != "" ) 		$where .= " and b.ord_no = '$ord_no' ";
		// 2005.12.22 추가  이희천
		if( $refund_yn != "")		$where .= " and a.refund_yn = '$refund_yn' ";
		if( $refund_bank != "")	$where .= " and a.refund_bank like '%". Lib::quote($refund_bank)."%' ";
		if( $refund_nm != "")		$where .= " and a.refund_nm like '%". Lib::quote($refund_nm)."%' ";
		//추가 남동현 2006.08.12
		if( $head_desc != "" )	$where .= " and b.head_desc like '%%". Lib::quote($head_desc)."%' ";
		if($style_no != "")		$where .= " and c.style_no like '%". Lib::quote($style_no)."%'";


		if($goods_nm != ""){
		    $where .= " and b.goods_nm like '%". Lib::quote($goods_nm)."%' ";
		}

		if($req_nm != ""){
			$where .= " and a.req_nm like '". Lib::quote($req_nm)."%' ";
        }

        $refund_company ="본사환불";
		$refund_KCP = "KCP환불";

        $sql = /** @lang text */
            "
            select
            '' as chkbox
            , b.ord_no
            , c.style_no
            , b.head_desc
            , b.goods_nm
            , b.goods_no
            , replace(b.goods_opt, '^', ' : ') as opt_val
            , cd.code_val as cd
            , a.memo
            , cb.code_val as cb
            , ce.code_val as ce
            , f.user_nm
            , f.user_id
            , f.mobile
            , cc.code_val as cc
            , if(a.refund_yn = 'Y', ifnull(g.pay_amt,cr.ipgm_mnyx),'') as pay_amt
            , a.refund_amt
            , a.refund_nm
            , a.refund_bank
            , a.refund_account
            , a.req_nm
            , a.ord_opt_no
            , date_format(a.req_date,'%Y.%m.%d %H:%i:%s') as req_date
            , date_format(a.last_up_date,'%Y.%m.%d %H:%i:%s') as last_up_date
            , if ( ifnull(e.escw_use,'') <> 'Y' or e.st_cd <> '','$refund_company','$refund_KCP') as srefund
            , ca.code_val as ca
        from claim a
            inner join order_opt b on a.ord_opt_no = b.ord_opt_no
            inner join goods c on b.goods_no = c.goods_no and b.goods_sub = c.goods_sub
            left outer join payment e on b.ord_no = e.ord_no
            inner join order_mst f on b.ord_no = f.ord_no
            left outer join code ca on ca.code_kind_cd = 'G_PAY_TYPE' and ca.code_id = b.pay_type
            left outer join code cb on cb.code_kind_cd = 'G_ORD_STATE' and cb.code_id = b.ord_state
            left outer join code cc on cc.code_kind_cd = 'G_REFUND_YN' and cc.code_id = a.refund_yn
            left outer join code cd on cd.code_kind_cd = 'G_CLM_REASON' and cd.code_id = a.clm_reason
            left outer join code ce on ce.code_kind_cd = 'G_CLM_STATE' and ce.code_id = b.clm_state
            left outer join payment g on b.ord_no = g.ord_no and g.pay_stat = '1'
            left outer join common_return cr on b.ord_no = cr.order_no
        where 1=1 and b.com_id = :com_id $where
        order by b.ord_opt_no desc
        limit 0,$limit
        ";

        $result = DB::select($sql,['com_id' => $com_id]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function popup(Request $request) {

        $sdate = $request->input("sdate", date("Y-m-d"));
        $edate = $request->input("edate", date("Y-m-d"));
        $edate = date('Y-m-d', strtotime( $edate . " +1 days"));
        
        $md_ids = DB::table('mgr_user')->where("md_yn",'=','Y')->orderBy("name")->get();
        $sale_places = DB::table('company')->where("com_type",'=','4')->orderBy("com_nm")->get();

        $values = [
            'sdate' => $sdate,
            'edate' => $edate,
            'md_ids' => $md_ids,
            'sale_places' => $sale_places,
        ];

        return view( Config::get('shop.partner.view') . '/cs/cs01_show',$values);

  }
}
