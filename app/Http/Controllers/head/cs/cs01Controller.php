<?php

namespace App\Http\Controllers\head\cs;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class cs01Controller extends Controller
{
	public function index() {
		$today = date("Y-m-d");
		$edate = $today;
		$sdate = date('Y-m-d', strtotime(-3 .'day'));
		
		$date_type = SLib::getCodes("G_DATE_TYPE");
		$com_types = SLib::getCodes("G_COM_TYPE");
		$refund_yn_item = SLib::getCodes("G_REFUND_YN");
		$srefund_item = SLib::getCodes("G_SREFUND");
		$clm_state_item = SLib::getCodes("G_CLM_STATE");
		$clm_type_item = SLib::getCodes("G_CLM_TYPE");
		$clm_reason_item = SLib::getCodes("G_CLM_REASON");
		$stat_pay_types = SLib::getCodes("G_STAT_PAY_TYPE");
		

		$query = "select
			opt_kind_cd id,
			case when 'kor' = 'kor' then
				concat('(',ifnull(opt_kind_cd, ''),') ',ifnull(opt_kind_nm, ''))
			else
				opt_kind_cd
			end as val
		from opt where opt_id = 'K' and use_yn = 'Y'
		order by  use_yn desc,opt_kind_nm ";
		$opt_kind_cd_items = DB::select($query);
		
		//print_r($date_type);
		$values = [
			'sdate'				=> $sdate,
			'edate'				=> $edate,
			'date_type_items'	=> $date_type,
			'com_types'			=> $com_types,
			'opt_kind_cd_items'	=> $opt_kind_cd_items,
			'refund_yn_item'	=> $refund_yn_item,
			'srefund_item'		=> $srefund_item,
			'clm_state_items'	=> $clm_state_item,
			'clm_type_item'		=> $clm_type_item,
			'clm_reason_item'	=> $clm_reason_item,
			'stat_pay_types'	=> $stat_pay_types
		];
		return view( Config::get('shop.head.view') . '/cs/cs01',$values);
	}

	public function search(Request $request){
		$date_type		= $request->input("date_type");
		$sdate			= $request->input("sdate");
		$edate			= $request->input("edate");
		$ord_no			= $request->input("ord_no");
		$req_nm			= $request->input("req_nm");
		$user_nm		= $request->input("user_nm");
		$pay_nm			= $request->input("pay_nm");
		$stat_pay_type	= $request->input("stat_pay_type");
		$not_complex	= $request->input("not_complex");
		$clm_type		= $request->input("clm_type");
		$clm_reason		= $request->input("clm_reason");
		$clm_state		= $request->input("clm_state");
		$com_type		= $request->input("com_type");
		$com_nm			= $request->input("com_nm");
		$com_id			= $request->input("com_id");
		$refund_nm		= $request->input("refund_nm");
		$refund_bank	= $request->input("refund_bank");
		$goods_nm		= $request->input("goods_nm");
		$refund_yn		= $request->input("refund_yn");
		$srefund		= $request->input("srefund");
		$opt_kind_cd	= $request->input("opt_kind_cd");
		$style_no		= $request->input("style_no");
		$limit			= $request->input("limit",100);
		$head_desc		= $request->input("head_desc");
		$page			= $request->input("page",1);

		$where = "";
		if($date_type == "10"){
			// 조건절 설정
			if( $sdate != "" ) $where .= " and a.req_date >= '$sdate' ";
			if( $edate != "" ) $where .= " and a.req_date < '$edate' ";
		}else if($date_type == "20" ){
			// 조건절 설정
			if( $sdate != "" ) $where .= " and a.proc_date >= '$sdate' ";
			if( $edate != "" ) $where .= " and a.proc_date < '$edate' ";
		}else if($date_type == "30" ){
			// 조건절 설정
			if( $sdate != "" ) $where .= " and a.end_date >= '$sdate' ";
			if( $edate != "" ) $where .= " and a.end_date < '$edate' ";
		}else{
			// 조건절 설정
			if( $sdate != "" ) $where .= " and (a.req_date >= '$sdate' or a.proc_date >= '$sdate' or a.end_date >= '$sdate') ";
			if( $edate != "" ) $where .= " and ((a.req_date < '$edate') or (a.proc_date < '$edate') or (a.end_date < '$edate'))";
		}

//		if( $CLM_NO != "" ) 		$where .= " and a.clm_no = '$CLM_NO' ";
		if( $req_nm != "" ) 		$where .= " and a.req_nm like '$req_nm%' ";
		if( $user_nm != "" ) 		$where .= " and f.user_nm like '$user_nm%' ";
		if( $pay_nm != "" ) 		$where .= " and e.pay_nm like '$pay_nm%' ";

		// 결제조건
		if($stat_pay_type != ""){
			if($not_complex == "Y"){
				$where .= " and e.pay_type = '$stat_pay_type' ";
			}else{
				$where .= " and (( e.pay_type & $stat_pay_type ) = $stat_pay_type) ";
			}
		}


		if( $clm_type != "" ) 	$where .= " and a.clm_type = '$clm_type' ";
		if( $clm_reason != "" ) 	$where .= " and a.clm_reason = '$clm_reason' ";
		if( $clm_state != "" ) 	$where .= " and b.clm_state = '$clm_state' ";
		if( $ord_no != "" ) 		$where .= " and b.ord_no = '$ord_no' ";
		// 2005.12.22 추가  이희천
		if( $refund_yn != "")		$where .= " and a.refund_yn = '$refund_yn' ";
		if( $refund_bank != "")	$where .= " and a.refund_bank like '$refund_bank%' ";
		if( $refund_nm != "")		$where .= " and a.refund_nm like '$refund_nm%' ";
		//추가 남동현 2006.08.12
		if( $srefund == "KH")		$where .= " and e.escw_use = 'Y' and ( e.st_cd = '' or e.st_cd is null )";
		if( $srefund == "BH")		$where .= " and ( e.escw_use != 'Y' or e.escw_use is null or e.st_cd != '' )  ";
		if( $goods_nm != "" )		$where .= " and b.goods_nm like '%$goods_nm%' ";
		if( $head_desc != "" )	$where .= " and b.head_desc like '%$head_desc%' ";
		if($com_id != "")			$where .= " and c.com_id = '$com_id'";
		if($com_type != "")		$where .= " and c.com_type = '$com_type'";

		if($style_no != "")		$where .= " and c.style_no like '$style_no%'";
		if ($opt_kind_cd != "")	$where .= " and c.opt_kind_cd = '$opt_kind_cd' ";


		
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		$sql = "
			select count(*) as cnt
			from claim a
				inner join order_opt b on a.ord_opt_no = b.ord_opt_no
				inner join goods c on b.goods_no = c.goods_no and b.goods_sub = c.goods_sub
				left outer join payment e on b.ord_no = e.ord_no
				inner join order_mst f on b.ord_no = f.ord_no
			where 1=1 $where
		";
		//echo $sql;
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}
		$arr_header = array("data_cnt"=>$data_cnt, "page_cnt"=>$page_cnt);

		
		$refund_company = "본사환불";
		$refund_KCP = "KCP환불";

		$sql = "
			select
				a.clm_no,'' as chkbox
				, b.ord_no
				, c.opt_kind_cd
				, c.style_no
				, b.head_desc
				, b.goods_nm
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
				, date_format(a.req_date,'%Y.%m.%d %H:%i:%s') as req_date
				, date_format(a.last_up_date,'%Y.%m.%d %H:%i:%s') as last_up_date
				, if ( ifnull(e.escw_use,'') <> 'Y' or e.st_cd <> '','$refund_company','$refund_KCP') as srefund
				, ca.code_val as ca
				, a.ord_opt_no
				, UCASE(ifnull(a.refund_yn,'')) as trefund_yn
				, a.clm_state, ifnull(e.escw_use,'') as tescw_use
				, ifnull(e.st_cd,'') as tst_cd
				, c.goods_no, c.goods_sub
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
			where 1=1 $where
			order by ord_no desc
			limit $startno,$page_size
		";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => $page_cnt
			),
			"body" => $result
		]);

	}

}
