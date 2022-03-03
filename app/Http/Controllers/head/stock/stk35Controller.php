<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk35Controller extends Controller
{
    //
    public function index() {

        $values = [
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'items'         => SLib::getItems(),
        ];
        return view( Config::get('shop.head.view') . '/stock/stk35',$values);
    }

    public function search(Request $request)
    {
		$qty_type		= $request->input("qty_type","goods");
		$qty_buffer_cnt	= $request->input("qty_buffer_cnt","");
		$exp0			= $request->input("exp0","N");
		$bizest_qty		= $request->input("bizest_qty");
		$s_goods_stat	= $request->input("s_goods_stat");
		$s_style_no		= $request->input("style_no");
		$s_goods_no		= $request->input("goods_no");
		$s_opt_kind_cd	= $request->input("s_opt_kind_cd");
		$s_goods_nm		= $request->input("goods_nm");

		$where	= "";
		$where_group	= "";

		if( $qty_buffer_cnt != "" )	
		{
			if( $qty_buffer_cnt >= 0 )
			{
				if( $qty_type == "opt" )	$where	.= " and ( a.xmd_qty - a.bizest_qty ) >= $qty_buffer_cnt ";
				else						$where_group	.= " and ( aa.xmd_qty - aa.bizest_qty ) >= $qty_buffer_cnt ";
			}
			else
			{
				if( $qty_type == "opt" )	$where	.= " and ( a.xmd_qty - a.bizest_qty ) <= $qty_buffer_cnt ";
				else						$where_group	.= " and ( aa.xmd_qty - aa.bizest_qty ) <= $qty_buffer_cnt ";
			}
		}
		if( $exp0 == "Y" )			$where	.= " and a.xmd_qty > 0 ";
		if( $bizest_qty != "" )
		{
			if( $qty_type == "opt" )	$where	.= " and a.bizest_qty <= $bizest_qty ";
			else						$where_group	.= " and aa.bizest_qty <= $bizest_qty ";
		}
		if( $s_goods_stat != "" )	$where .= " and b.sale_stat_cl = '" . Lib::quote($s_goods_stat) . "' ";
		if( $s_style_no	!= "" )		$where .= " and b.style_no like '" . Lib::quote($s_style_no) . "%' ";
		if( $s_goods_no != "" )
		{
			$goods_nos	= explode(",",$s_goods_no);

			if( count($goods_nos) > 1 )
			{
				if( count($goods_nos) > 50 )	array_splice($goods_nos,50);
				$in_goods_nos	= join(",",$goods_nos);
				$where	.= " and a.goods_no in ( $in_goods_nos ) ";
			} 
			else 
			{
				$where .= " and a.goods_no = '" . Lib::quote($s_goods_no) . "' ";
			}
		}
		if( $s_opt_kind_cd != "" )	$where .= " and b.opt_kind_cd = '" . Lib::quote($s_opt_kind_cd) . "' ";
		if( $s_goods_nm != "" )		$where .= " and b.goods_nm like '%" . Lib::quote($s_goods_nm) . "%' ";

		if( $qty_type == "opt" )
		{
			$sql	= "
				select
					a.goods_no, b.head_desc, b.goods_nm, a.goods_opt, stat.code_val as sale_stat_cl_val, a.xmd_qty, a.bizest_qty, (a.xmd_qty - a.bizest_qty) as qty_term,
					( select count(*) from order_opt where goods_no = a.goods_no and goods_opt = a.goods_opt and ord_state = '30' and (now() - interval 3 month) <= ord_date ) as month_ord,
					( select count(*) from order_opt where goods_no = a.goods_no and goods_opt = a.goods_opt and ord_state = '30' ) as tot_ord,
					date_format(a.regdate,'%Y.%m.%d') as rt
				from goods_xmd_monitor a
				inner join goods b on a.goods_no = b.goods_no
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = stat.code_id
				where 1=1  $where
				order by a.goods_no desc
			";
		}
		else
		{
			$sql	= "
				select
					aa.goods_no, aa.head_desc, aa.goods_nm, aa.goods_opt, aa.sale_stat_cl_val, aa.xmd_qty, aa.bizest_qty, aa.qty_term,
					( select count(*) from order_opt where goods_no = aa.goods_no and ord_state = '30' and (now() - interval 3 month) <= ord_date ) as month_ord,
					( select count(*) from order_opt where goods_no = aa.goods_no and ord_state = '30' ) as tot_ord,
					aa.regdate as rt
				from
				(
					select
						a.goods_no, b.head_desc, b.goods_nm, '' as goods_opt, stat.code_val as sale_stat_cl_val, sum(a.xmd_qty) as xmd_qty, sum(a.bizest_qty) as bizest_qty, (sum(a.xmd_qty) - sum(a.bizest_qty)) as qty_term, date_format(a.regdate,'%Y.%m.%d') as regdate
					from goods_xmd_monitor a
					inner join goods b on a.goods_no = b.goods_no
					left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = stat.code_id
					where 1=1   $where
					group by a.goods_no
				) aa
				where
					1=1 $where_group
				order by aa.goods_no desc
			";
		}

        $result = DB::select($sql);

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
                "total"		=> count($result)
            ),
            "body" => $result
        ]);

	}

    public function show()
	{

		$values = [
			'buffer_cnt'	=> "",
        ];

		return view( Config::get('shop.head.view') . '/stock/stk35_show',$values);
    }

	public function delete(Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

		$sql	= " delete from xmd_stock_file_monitor ";
		DB::delete($sql);

		// 기존 등록 로그 비활성 처리
		$sql	= " delete from goods_xmd_monitor ";
		DB::delete($sql);
		
		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);
	}

	public function upload(Request $request)
	{

        if ( 0 < $_FILES['file']['error'] ) {
            echo json_encode(array(
                "code" => 500,
                "errmsg" => 'Error: ' . $_FILES['file']['error']
            ));
        }
        else {
			//$file = sprintf("data/stk35/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/stk35/%s", $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
            echo json_encode(array(
                "code" => 200,
                "file" => $file
            ));
        }

	}

	public function update(Request $request)
	{
		set_time_limit(0);

		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
        $name	= Auth('head')->user()->name;

		$store_cd	= "bonsa";
		$buf_cnt	= $request->input('buffer_cnt','0');

        $datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}



		//DB::beginTransaction();



		// 예외 사항 array 생성
		$except	= array();
		$sql	= " select goods_code, bonsa_cnt, store_cnt from xmd_stock_file_except ";
		$rows = DB::select($sql);
		foreach($rows as $row)
		{
			$except[]	= array(
				"goods_code"	=> $row->goods_code,
				"bonsa_cnt"		=> $row->bonsa_cnt,
				"store_cnt"		=> $row->store_cnt
			);
		}

		$sql_insert	= " insert into xmd_stock_file_monitor( store_code, xmd_goods_code, color_code, size_code, xmd_goods_code_full, goods_nm, color_nm, price, wonga, qty ) values ";

		for( $i = 0; $i < count($datas); $i++ )
		{
			$data	= (array)$datas[$i];

			$xmd_goods_code	= $data['xmd_code'];
			$item_code		= $data['item_code'];
			$goods_nm		= $data['goods_nm'];
			$color_code		= $data['color_code'];
			$color_nm		= $data['color_nm'];
			$size_code		= $data['size_code'];
			$tag_price		= Lib::uncm($data['goods_sh']);
			$now_price		= Lib::uncm($data['price']);
			$wonga			= Lib::uncm($data['wonga']);
			$store_cnt		= Lib::uncm($data['store_qty']);
			$bonsa_cnt		= Lib::uncm($data['bonsa_qty']);
			$qty			= 0;

			$xmd_goods_code_full	= $xmd_goods_code . $color_code . $size_code;

			//기존 데이터 삭제 ( 최신 자료만 남김 )
			$sql	= " delete from xmd_stock_file_monitor where xmd_goods_code_full = '" . $xmd_goods_code_full . "' ";
			DB::delete($sql);

			$store_cnt	= ( (int)$store_cnt > (int)$buf_cnt ) ? (int)$store_cnt - (int)$buf_cnt : 0;

			//$qty	= $bonsa_cnt + $store_cnt;
			$qty	= (int)$store_cnt;

			if( $qty < 0 ) $qty = 0;

			//예외 사항 적용
			for( $j = 0; $j < count($except); $j++ )
			{
				// 1. 해당코드, 2. 칼라코드포함, 3. 사이즈코드 포함
				if( ( $except[$j]["goods_code"] == $xmd_goods_code ) || ( $except[$j]["goods_code"] == ($xmd_goods_code . $color_code) ) || ( $except[$j]["goods_code"] == ($xmd_goods_code . $color_code . $size_code) ) )
				{
					if( $except[$j]["store_cnt"] != "" )	$except_store_cnt	= $except[$j]["store_cnt"];
					else									$except_store_cnt	= (int)$store_cnt;

					$qty	= (int)$except_store_cnt;
				}
			}

			if( $i != 0 )	$sql_insert .= ",";
			$sql_insert	.= " ( '$store_cd', '$xmd_goods_code', '$color_code', '$size_code', '$xmd_goods_code_full', '".Lib::quote($goods_nm)."', '$color_nm', '$now_price', '$wonga', '$qty' ) ";
		}

		DB::insert($sql_insert);

		$sql	= " delete from goods_xmd_monitor ";
		DB::delete($sql);

		$sql	= "
			select
				b.goods_no, b.goods_opt, sum(a.qty) as xmd_qty, ( select good_qty from goods_summary where goods_no = b.goods_no and goods_opt = b.goods_opt ) as bizest_qty
			from xmd_stock_file_monitor a
			inner join goods_xmd b on a.xmd_goods_code_full = b.cd
			group by b.goods_no, b.goods_opt
		";
		$result = DB::select($sql);
        foreach($result as $row) 
		{
			$goods_no	= $row->goods_no;
			$goods_opt	= $row->goods_opt;
			$xmd_qty	= $row->xmd_qty;
			$bizest_qty	= $row->bizest_qty;

			$sql_sub	= "
				insert into goods_xmd_monitor( goods_no, goods_opt, xmd_qty, bizest_qty, regdate )
				values ( '$goods_no', '$goods_opt', '$xmd_qty', '$bizest_qty', now() )
			";
			DB::insert($sql_sub);
		}

		$result_code	= "데이터가 등록 되었습니다. - [$store_cd]";


		//DB::commit();

		
		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}
