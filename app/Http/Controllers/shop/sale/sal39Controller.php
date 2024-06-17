<?php

namespace App\Http\Controllers\shop\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class sal39Controller extends Controller
{
	public function index(Request $req)
	{
		$date = new DateTime($req->input('sdate', now()->startOfMonth()->sub(0, 'month')->format("Ym") . '01'));
		$sdate = $date->format('Y-m-d');
		$edate = $date->format('Y-m-t');

		$values = [
			'sdate' => $sdate,
			'edate' => $edate,
			'store_channel' => SLib::getStoreChannel(),
			'store_kind' => SLib::getStoreKind(),
		];
		return view(Config::get('shop.shop.view') . '/sale/sal39', $values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 1000);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','prd_cd1');

		$date_type	= $request->input('date_type', 'req');
		$sdate_org	= $request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd'));
		$edate_org	= $request->input('edate',date("Ymd"));

		$store_cd	= Auth('head')->user()->store_cd;
		$kind				= $request->input('kind');
		$type				= $request->input('type');
		$state				= $request->input('state');
		$prd_cd				= $request->input("prd_cd", "");
		$prd_cd_range_text	= $request->input("prd_cd_range", '');
		$style_no			= $request->input("style_no");
		$goods_no			= $request->input("goods_no");
		$goods_nos			= $request->input('goods_nos', '');       // 상품번호 textarea
		$goods_nm			= $request->input("goods_nm");
		$goods_nm_eng		= $request->input("goods_nm_eng");

		$orderby = sprintf("order by a.%s %s, a.kind, a.kind_nm ", $ord_field, $ord);

		$where			= "";
		$where_common	= "";
		$rt_out_where	= "";
		$rt_in_where	= "";
		$release_where	= "";
		$return_where	= "";

		$sdate	= $sdate_org . " 00:00:00";
		$edate	= $edate_org . " 23:59:59";

		if($date_type == 'req') {
			$date_val			= "psr.req_rt";
			$release_date_val	= "psr.req_rt";
			$return_date_val	= "sr.sr_date";
		}elseif($date_type == 'prc'){
			$date_val			= "psr.rec_rt";
			$release_date_val	= "psr.rec_rt";
			$return_date_val	= "sr.sr_date";
		}else{
			$date_val			= "ifnull(psr.fin_rt, '9999-99-99 99:99:99')";
			$release_date_val	= "ifnull(if(psr.state = '30', psr.prc_rt, psr.fin_rt), '9999-99-99 99:99:99')";
			$return_date_val	= "ifnull(sr.sr_fin_date,'9999-99-99')";
		}

		if($store_cd != ""){
			$rt_out_where	.= " and psr.dep_store_cd = '$store_cd' ";
			$rt_in_where	.= " and psr.store_cd = '$store_cd' ";
			$release_where	.= " and psr.store_cd = '$store_cd' ";
			$return_where	.= " and sr.store_cd = '$store_cd' ";
		}

		if($kind != ''){
			$where	.= " and a.kind = '" . Lib::quote($kind). "' ";
		}

		if($type != ''){
			$where	.= " and a.type = '" . Lib::quote($type). "' ";
		}

		if($state != ''){
			if($state == '30'){
				$rt_out_where	.= " and ( psr.state = '20' or psr.state = '30' ) ";
				$rt_in_where	.= " and ( psr.state = '20' or psr.state = '30' ) ";
				$release_where	.= " and ( psr.state = '20' or psr.state = '30' ) ";
				$return_where	.= " and sr.sr_state = '$state' ";
			}else{
				$rt_out_where	.= " and psr.state = '$state' ";
				$rt_in_where	.= " and psr.state = '$state' ";
				$release_where	.= " and psr.state = '$state' ";
				$return_where	.= " and sr.sr_state = '$state' ";
			}
		}

		if($prd_cd != "") {
			$prd_cd	= explode(',', $prd_cd);
			$where_sub			= " and (1!=1";
			$where_sub_return	= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where_sub			.= " or psr.prd_cd like '" . Lib::quote($cd) . "%' ";
				$where_sub_return	.= " or srp.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where_sub			.= ")";
			$where_sub_return	.= ")";

			$rt_out_where	.= $where_sub;
			$rt_in_where	.= $where_sub;
			$release_where	.= $where_sub;
			$return_where	.= $where_sub_return;
		}

		// 상품옵션 범위검색
		$where_sub	= "";
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows	= $prd_cd_range[$opt] ?? [];
			if(count($rows) > 0) {
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where_sub = " and pc.$opt in ($opt_join) ";

				$where_common	.= $where_sub;
			}
		}

		// 스타일넘버 다중검색
		$style_no	= preg_replace("/\s/",",",$style_no);
		$style_no	= preg_replace("/\t/",",",$style_no);
		$style_no	= preg_replace("/\n/",",",$style_no);
		$style_no	= preg_replace("/,,/",",",$style_no);

		if( $style_no != "" ){
			$where_sub	= "";
			$style_nos	= explode(",",$style_no);
			if(count($style_nos) > 1){
				if(count($style_nos) > 500) array_splice($style_nos,500);
				$in_style_nos = join(",",$style_nos);
				$where_sub = " and p.style_no in ( $in_style_nos ) ";
			} else {
				if ($style_no != "") $where_sub = " and p.style_no = '" . Lib::quote($style_no) . "' ";
			}

			$where_common	.= $where_sub;
		}

		// 온라인코드 다중검색
		if($goods_nos != ""){
			$goods_no	= $goods_nos;
		}
		$goods_no	= preg_replace("/\s/",",",$goods_no);
		$goods_no	= preg_replace("/\t/",",",$goods_no);
		$goods_no	= preg_replace("/\n/",",",$goods_no);
		$goods_no	= preg_replace("/,,/",",",$goods_no);

		if( $goods_no != "" ){
			$where_sub	= "";
			$goods_nos	= explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos	= join(",",$goods_nos);
				$where_sub	= " and pc.goods_no in ( $in_goods_nos ) ";
			} else {
				if ($goods_no != "") $where_sub	= " and pc.goods_no = '" . Lib::quote($goods_no) . "' ";
			}

			$where_common	.= $where_sub;
		}

		if($goods_nm != ""){
			$where_common	.= " and if(pc.goods_no = 0, p.prd_nm, g.goods_nm) like '%" . Lib::quote($goods_nm) . "%' ";
		}
		if($goods_nm_eng != ""){
			$where_common	.= " and if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) like '%" . Lib::quote($goods_nm_eng) . "%' ";
		}

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$total_data = 0;
		$page_cnt	= 0;

		if($page == 1) {
			$sql	= "
				select
					count(*) as total,
					sum(ifnull(a.qty, 0)) as total_qty
				from (
					-- RT 출고
					select
						'rt_out' as kind, 'RT출고' as kind_nm, 
						psr.type, if(psr.type = 'G','매장RT','본사RT') as type_nm,
						psr.state,
						case
							when psr.state = '10' then '요청'
							when psr.state = '20' or psr.state = '30' then '처리중'
							when psr.state = '40' then '완료'
							when psr.state = '-10' then '거부'
							else '-'
						end as state_nm,
						psr.document_number,
						date_format(psr.req_rt,'%Y-%m-%d') as req_rt,
						date_format(psr.fin_rt,'%Y-%m-%d') as fin_rt,
						psr.dep_store_cd, s1.store_nm as dep_store_nm,
						psr.store_cd as target_cd, s2.store_nm as target_nm,
						psr.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price,
						psr.qty * -1 as qty,
						if(psr.type = 'R', psr. req_comment, '') as comment
					from product_stock_rotation psr
					inner join product_code pc on pc.prd_cd = psr.prd_cd
					inner join product p on p.prd_cd = psr.prd_cd
					left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
					left outer join store s1 on s1.store_cd = psr.dep_store_cd
					left outer join store s2 on s2.store_cd = psr.store_cd
					where
						$date_val >= '$sdate' and $date_val <= '$edate'
						$rt_out_where
						$where_common
				
					union all
				
					-- RT 입고
					select
						'rt_in' as kind, 'RT입고' as kind_nm, 
						psr.type, if(psr.type = 'G','매장RT','본사RT') as type_nm,
						psr.state,
						case
							when psr.state = '10' then '요청'
							when psr.state = '20' or psr.state = '30' then '처리중'
							when psr.state = '40' then '완료'
							when psr.state = '-10' then '거부'
							else '-'
						end as state_nm,
						psr.document_number,
						date_format(psr.req_rt,'%Y-%m-%d') as req_rt,
						date_format(psr.fin_rt,'%Y-%m-%d') as fin_rt,
						psr.dep_store_cd, s1.store_nm as dep_store_nm,
						psr.store_cd as target_cd, s2.store_nm as target_nm,
						psr.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price,
						psr.qty,
						if(psr.type = 'R', psr. req_comment, '') as comment
					from product_stock_rotation psr
					inner join product_code pc on pc.prd_cd = psr.prd_cd
					inner join product p on p.prd_cd = psr.prd_cd
					left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
					left outer join store s1 on s1.store_cd = psr.dep_store_cd
					left outer join store s2 on s2.store_cd = psr.store_cd
					where
						$date_val >= '$sdate' and $date_val <= '$edate'
						$rt_in_where
						$where_common
				
					union all
						
					-- 물류 입고
					select
						'release' as kind, '물류입고' as kind_nm, 
						psr.type, 
						case
							when psr.type = 'F' then '초도'
							when psr.type = 'S' then '판매분'
							when psr.type = 'R' then '요청분'
							when psr.type = 'G' then '일반'
							when psr.type = 'SG' then '창고처리'
							else '-'
						end as type_nm, -- 초도/판매분/요청분/일반 : F/S/R/G
						psr.state,
						case
							when psr.state = '10' then '요청'
							when psr.state = '20' then '처리중'
							when psr.state = '30' or psr.state = '40' then '완료'
							when psr.state = '-10' then '거부'
							else '-'
						end as state_nm,
						psr.document_number,
						date_format(psr.req_rt,'%Y-%m-%d') as req_rt,
						date_format(psr.fin_rt,'%Y-%m-%d') as fin_rt,
						psr.store_cd, s1.store_nm as store_nm,
						psr.storage_cd as target_cd, s2.storage_nm as target_nm,
						psr.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price,
						psr.qty,
						psr. comment
					from product_stock_release psr
					inner join product_code pc on pc.prd_cd = psr.prd_cd
					inner join product p on p.prd_cd = psr.prd_cd
					left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
					left outer join store s1 on s1.store_cd = psr.store_cd
					left outer join storage s2 on s2.storage_cd = psr.storage_cd
					where
						$release_date_val >= '$sdate' and $release_date_val <= '$edate'
						$release_where
						$where_common
				
					union all
						
					-- 물류 반품
					select
						'return' as kind, '물류반품' as kind_nm, 
						sr.sr_reason as type,sr_reason.code_val as type_nm,
						sr.sr_state as state,
						case
							when sr.sr_state = '10' then '요청'
							when sr.sr_state = '30' then '처리중'
							when sr.sr_state = '40' then '완료'
							else '-'
						end as state_nm,
						srp.sr_cd as document_number,
						sr.sr_date as req_rt,
						sr.sr_fin_date as fin_rt,
						sr.store_cd, s1.store_nm as store_nm,
						sr.storage_cd as target_cd, s2.storage_nm as target_nm,
						srp.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price,
						case
							when sr.sr_state = '10' then srp.return_qty * -1
							when sr.sr_state = '30' then srp.return_p_qty * -1
							when sr.sr_state = '40' then srp.fixed_return_qty * -1
							else '0'
						end as qty,
						sr. comment
					from store_return_product srp
					inner join store_return sr on sr.sr_cd = srp.sr_cd
					inner join product_code pc on pc.prd_cd = srp.prd_cd
					inner join product p on p.prd_cd = srp.prd_cd
					left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
					left outer join code sr_reason on sr_reason.code_id = sr.sr_reason and sr_reason.code_kind_cd = 'SR_REASON'
					left outer join store s1 on s1.store_cd = sr.store_cd
					left outer join storage s2 on s2.storage_cd = sr.storage_cd
					where
						$return_date_val >= '$sdate_org' and $return_date_val <= '$edate_org'
						$return_where
						$where_common
				) a
				where
					1 = 1
					$where
			";
			$row		= DB::select($sql);
			$total		= $row[0]->total;
			$total_data = $row[0]->total_qty;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		}

		$sql	= "
			select
				a.*
			from (
				-- RT 출고
				select
					'rt_out' as kind, 'RT출고' as kind_nm, 
					psr.type, if(psr.type = 'G','매장RT','본사RT') as type_nm,
					psr.state,
					case
						when psr.state = '10' then '요청'
						when psr.state = '20' or psr.state = '30' then '처리중'
						when psr.state = '40' then '완료'
						when psr.state = '-10' then '거부'
						else '-'
					end as state_nm,
					psr.document_number,
					date_format(psr.req_rt,'%Y-%m-%d') as req_rt,
					date_format(psr.prc_rt,'%Y-%m-%d') as prc_rt,
					date_format(psr.fin_rt,'%Y-%m-%d') as fin_rt,
					psr.dep_store_cd, s1.store_nm as dep_store_nm,
					psr.store_cd as target_cd, s2.store_nm as target_nm,
					psr.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price, pc.goods_no,
					psr.qty * -1 as qty,
					if(psr.type = 'R', psr. req_comment, '') as comment
				from product_stock_rotation psr
				inner join product_code pc on pc.prd_cd = psr.prd_cd
				inner join product p on p.prd_cd = psr.prd_cd
				left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
				left outer join store s1 on s1.store_cd = psr.dep_store_cd
				left outer join store s2 on s2.store_cd = psr.store_cd
				where
					$date_val >= '$sdate' and $date_val <= '$edate'
					$rt_out_where
					$where_common
			
				union all
			
				-- RT 입고
				select
					'rt_in' as kind, 'RT입고' as kind_nm, 
					psr.type, if(psr.type = 'G','매장RT','본사RT') as type_nm,
					psr.state,
					case
						when psr.state = '10' then '요청'
						when psr.state = '20' or psr.state = '30' then '처리중'
						when psr.state = '40' then '완료'
						when psr.state = '-10' then '거부'
						else '-'
					end as state_nm,
					psr.document_number,
					date_format(psr.req_rt,'%Y-%m-%d') as req_rt,
					date_format(psr.prc_rt,'%Y-%m-%d') as prc_rt,
					date_format(psr.fin_rt,'%Y-%m-%d') as fin_rt,
					psr.dep_store_cd, s1.store_nm as dep_store_nm,
					psr.store_cd as target_cd, s2.store_nm as target_nm,
					psr.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price, pc.goods_no,
					psr.qty,
					if(psr.type = 'R', psr. req_comment, '') as comment
				from product_stock_rotation psr
				inner join product_code pc on pc.prd_cd = psr.prd_cd
				inner join product p on p.prd_cd = psr.prd_cd
				left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
				left outer join store s1 on s1.store_cd = psr.dep_store_cd
				left outer join store s2 on s2.store_cd = psr.store_cd
				where
					$date_val >= '$sdate' and $date_val <= '$edate'
					$rt_in_where
					$where_common
			
				union all
					
				-- 물류 입고
				select
					'release' as kind, '물류입고' as kind_nm, 
					psr.type, 
					case
						when psr.type = 'F' then '초도'
						when psr.type = 'S' then '판매분'
						when psr.type = 'R' then '요청분'
						when psr.type = 'G' then '일반'
						when psr.type = 'SG' then '창고처리'
						else '-'
					end as type_nm, -- 초도/판매분/요청분/일반 : F/S/R/G
					psr.state,
					case
						when psr.state = '10' then '요청'
						when psr.state = '20' then '처리중'
						when psr.state = '30' or psr.state = '40' then '완료'
						when psr.state = '-10' then '거부'
						else '-'
					end as state_nm,
					psr.document_number,
					date_format(psr.req_rt,'%Y-%m-%d') as req_rt,
					date_format(psr.rec_rt,'%Y-%m-%d') as prc_rt,
					date_format(if(psr.state = '30', psr.prc_rt, psr.fin_rt),'%Y-%m-%d') as fin_rt,
					psr.store_cd, s1.store_nm as store_nm,
					psr.storage_cd as target_cd, s2.storage_nm as target_nm,
					psr.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price, pc.goods_no,
					psr.qty,
					psr. comment
				from product_stock_release psr
				inner join product_code pc on pc.prd_cd = psr.prd_cd
				inner join product p on p.prd_cd = psr.prd_cd
				left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
				left outer join store s1 on s1.store_cd = psr.store_cd
				left outer join storage s2 on s2.storage_cd = psr.storage_cd
				where
					$release_date_val >= '$sdate' and $release_date_val <= '$edate'
					$release_where
					$where_common
			
				union all
					
				-- 물류 반품
				select
					'return' as kind, '물류반품' as kind_nm, 
					sr.sr_reason as type,sr_reason.code_val as type_nm,
					sr.sr_state as state,
					case
						when sr.sr_state = '10' then '요청'
						when sr.sr_state = '30' then '처리중'
						when sr.sr_state = '40' then '완료'
						else '-'
					end as state_nm,
					srp.sr_cd as document_number,
					sr.sr_date as req_rt,
					sr.sr_pro_date as prc_rt,
					sr.sr_fin_date as fin_rt,
					sr.store_cd, s1.store_nm as store_nm,
					sr.storage_cd as target_cd, s2.storage_nm as target_nm,
					srp.prd_cd, p.style_no, pc.prd_cd_p, p.prd_nm, pc.color, pc.size, p.tag_price, p.price, pc.goods_no,
					case
						when sr.sr_state = '10' then srp.return_qty * -1
						when sr.sr_state = '30' then srp.return_p_qty * -1
						when sr.sr_state = '40' then srp.fixed_return_qty * -1
						else '0'
					end as qty,
					sr. comment
				from store_return_product srp
				inner join store_return sr on sr.sr_cd = srp.sr_cd
				inner join product_code pc on pc.prd_cd = srp.prd_cd
				inner join product p on p.prd_cd = srp.prd_cd
				left outer join goods g on pc.goods_no = g.goods_no and g.goods_no <> 0
				left outer join code sr_reason on sr_reason.code_id = sr.sr_reason and sr_reason.code_kind_cd = 'SR_REASON'
				left outer join store s1 on s1.store_cd = sr.store_cd
				left outer join storage s2 on s2.storage_cd = sr.storage_cd
				where
					$return_date_val >= '$sdate_org' and $return_date_val <= '$edate_org'
					$return_where
					$where_common
			) a
			where
				1 = 1
				$where
			$orderby
			$limit
		";
		$result	= DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total"			=> $total,
				"total_data"	=> $total_data,
				"page"			=> $page,
				"page_cnt"		=> $page_cnt,
				"page_total"	=> count($result),
			),
			"body" => $result
		]);
	}

}
