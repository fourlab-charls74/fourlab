<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk02Controller extends Controller
{

	//
	public function index() {

        $mutable	= now();
        $sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

        $com_types	= [];
        $event_cds	= [];
        //판매유형
        $sell_types	= [];
        $code_kinds	= [];

		$values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
			'com_types'		=> $com_types,
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
		];

		return view( Config::get('shop.store.view') . '/stock/stk02',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$sdate		= $request->input('sdate',Carbon::now()->sub(2, 'year')->format('Ymd'));
		$edate		= $request->input('edate',date("Ymd"));
		$com_type	= $request->input('com_type');
		$com_nm		= $request->input('com_nm');
		$goods_code	= $request->input('goods_code');
		$event_cd	= $request->input('event_cd');
		$user_id	= $request->input('user_id');
		$sell_type	= $request->input('sell_type');

		$limit		= $request->input("limit",100);
		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','g.goods_no');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		if( $com_type != "" )	$where .= " and a.com_type = '" . $com_type . "' ";
		if( $com_nm != "" )		$where .= " and a.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if( $goods_code != "" )	$where .= " and a.goods_code like '" . Lib::quote($goods_code) . "%' ";
		if( $event_cd != "" )	$where .= " and a.event_cd = '" . $event_cd . "' ";
		if( $user_id != "" )	$where .= " and a.user_id = '" . Lib::quote($user_id) . "%' ";
		if( $sell_type != "" )	$where .= " and a.sell_type = '" . $sell_type . "' ";

		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

		if( $page == 1 ){
			$query	= "
				select count(*) as total
				from __tmp_order a
				where 1=1 
					and ( a.ord_date >= :sdate and a.ord_date < date_add(:edate,interval 1 day))
					$where
			";
			//$row = DB::select($query,['com_id' => $com_id]);
			$row		= DB::select($query, ['sdate' => $sdate,'edate' => $edate]);
			$total		= $row[0]->total;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		}

		$query	= "
			select
				a.*, 
				(100 - round(a.price/a.goods_sh * 100)) as sale_rate,
				(100 - round(a.ord_amt/a.goods_sh * 100)) as ord_sale_rate,
				( (100 - round(a.ord_amt/a.goods_sh * 100)) - (100 - round(a.price/a.goods_sh * 100)) ) as sale_gap,
				b.code_val as com_type_nm,
				c.code_val as opt_kind_nm,
				d.code_val as brand_nm,
				e.code_val as stat_pay_type_nm,
				f.code_val as sell_type_nm,
				g.code_val as event_kind_nm
			from __tmp_order a
			left outer join __tmp_code b on b.code_kind_cd = 'com_type' and b.code_id = a.com_type
			left outer join __tmp_code c on c.code_kind_cd = 'opt_kind_cd' and c.code_id = a.opt_kind
			left outer join __tmp_code d on d.code_kind_cd = 'brand' and d.code_id = a.brand
			left outer join __tmp_code e on e.code_kind_cd = 'stat_pay_type' and e.code_id = a.stat_pay_type
			left outer join __tmp_code f on f.code_kind_cd = 'sell_type' and f.code_id = a.sell_type
			left outer join __tmp_code g on g.code_kind_cd = 'event_cd' and g.code_id = a.event_cd
			where 1=1 
				and ( a.ord_date >= :sdate and a.ord_date < date_add(:edate,interval 1 day))
				$where
			$orderby
			$limit
		";

		$result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body" => $result
		]);

	}
}
