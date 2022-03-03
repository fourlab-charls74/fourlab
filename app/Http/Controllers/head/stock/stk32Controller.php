<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk32Controller extends Controller
{
    //
    public function index() {

		$bonsa_jeago_yn	= "N";

		$sql	= " select count(*) as tot from xmd_stock_file_log where use_yn = 'Y' and store_cd = 'bonsa' ";
		$row	= DB::selectOne($sql);

		if( $row->tot != "0" )
		{
			$bonsa_jeago_yn	= "Y";
		}

		$store_info	= [];

		// 재고 처리 매장정보
		$tot_store_jeago_yn	= "Y";
		$sql	= " select code_id, code_val from data_code where code_kind_cd = 'xmd_stock_conf' and use_yn = 'Y' order by code_seq ";
		$rows = DB::select($sql);

		foreach($rows as $row)
		{
			$store_cd	= $row->code_id;
			$store_nm	= $row->code_val;

			$store_jeago_yn	= "N";
			$sql_sub	= " select count(*) as tot from xmd_stock_file_log where use_yn = 'Y' and store_cd = '$store_cd' ";
			$row_sub	= DB::selectOne($sql_sub);

			if( $row_sub->tot != "0" )
			{
				$store_jeago_yn	= "Y";
			}

			if( $store_jeago_yn == "N" )	$tot_store_jeago_yn = "N";

			array_push($store_info, 
				array(
					"store_cd"	=> $store_cd, 
					"store_nm"	=> $store_nm,
					"store_jeago_yn"	=> $store_jeago_yn
				)
			);
		}

		if( $bonsa_jeago_yn == "Y" && $tot_store_jeago_yn == "Y" )	$excel_yn = "Y";
		else														$excel_yn = "N";

		$values = [
			'bonsa_jeago_yn'	=> $bonsa_jeago_yn,
			'store_info'		=> $store_info,
			'excel_yn'			=> $excel_yn,
        ];
		
        return view( Config::get('shop.head.view') . '/stock/stk32',$values);
    }

    public function search(Request $request)
    {
		$query	= "
			select
				a.xmd_goods_code_full, a.goods_nm, a.color_nm, a.price, a.wonga, sum(a.qty) as amt
			from xmd_stock_file a
			-- inner join __tmp_ceduce_sale_goods b on a.xmd_goods_code = b.xmd_code
			where 1=1
			-- and a.xmd_goods_code in ('F185UAC19AC','F185UAC20AC','F185UAC21AC','F185UAC22AC','F185UAC23AC','F185UAC24AC','F185UAC25AC','F185UAC26AC','F185UAC27AC')
			group by a.xmd_goods_code, a.color_code, a.size_code
		";

        $result = DB::select($query);

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
		$store_info	= [];

		// 재고 처리 매장정보
		$sql	= " select code_id, code_val from data_code where code_kind_cd = 'xmd_stock_conf' and use_yn = 'Y' order by code_seq ";
		$rows = DB::select($sql);

		foreach($rows as $row)
		{
			$store_cd	= $row->code_id;
			$store_nm	= $row->code_val;

			$store_jeago_yn	= "N";
			$sql_sub	= " select count(*) as tot from xmd_stock_file_log where use_yn = 'Y' and store_cd = '$store_cd' ";
			$row_sub	= DB::selectOne($sql_sub);

			if( $row_sub->tot != "0" )
			{
				$store_jeago_yn	= "Y";
			}

			if( $store_jeago_yn == "N" )	$tot_store_jeago_yn = "N";

			array_push($store_info, 
				array(
					"store_cd"	=> $store_cd, 
					"store_nm"	=> $store_nm,
					"store_jeago_yn"	=> $store_jeago_yn
				)
			);
		}

		$values = [
			'store_info'	=> $store_info
        ];

		return view( Config::get('shop.head.view') . '/stock/stk32_show',$values);
    }

	public function delete(Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

		$sql	= " delete from xmd_stock_file ";
		DB::delete($sql);

		// 기존 등록 로그 비활성 처리
		$sql	= " update xmd_stock_file_log set use_yn = 'N' where use_yn = 'Y' ";
		DB::update($sql);
		
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
			//$file = sprintf("data/stk32/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/stk32/%s", $_FILES['file']['name']);
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

		$id			= Auth('head')->user()->id;
		$name		= Auth('head')->user()->name;
		$buffer_cnt	= 0;	// 매장 버퍼링 재고
		$cnt		= 0;	// 데이터 카운트

		$error_code		= "200";
		$result_code	= "";

		$store_cd	= $request->input('store_cd');
        $datas		= $request->input('data');
		$datas		= json_decode($datas);

		if( $store_cd == "" || $datas == "" )
		{
			$error_code	= "400";
		}


//		DB::beginTransaction();

		// 기존 등록된 중복데이터 삭제
		$sql	= " 
			select cnt
			from xmd_stock_file_log
			where 
				use_yn = 'Y' 
				and store_cd = :store_cd 
		";
		$row = DB::selectOne($sql, ['store_cd'	=> $store_cd]);
		if(!empty($row->cnt)) 
		{
			$sql	= " delete from xmd_stock_file where store_code = :store_cd ";
			DB::delete($sql,['store_cd'	=> $store_cd]);
		}

		// 매장별 버퍼링 카운트 적용
		$sql	= " 
			select 
				code_val2 as buffer_cnt 
			from data_code
			where 
				code_kind_cd = 'xmd_stock_conf' and use_yn = 'Y'
				and code_id = :store_cd
		";
		$row = DB::selectOne($sql, ['store_cd'	=> $store_cd]);
		if(!empty($row->buffer_cnt)) 
		{
			$buffer_cnt	= $row->buffer_cnt;
		}

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

		$sql_insert	= " insert into xmd_stock_file( store_code, xmd_goods_code, color_code, size_code, xmd_goods_code_full, goods_nm, color_nm, price, wonga, qty ) values ";

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

			if( $store_cd == "bonsa" )	$qty = (int)$bonsa_cnt + (int)$store_cnt;
			else
			{
				// 매장 재고는 버퍼재고를 제외한 수량을 재고로 세팅
				$qty	= (int)$store_cnt + (int)$buffer_cnt;
			}

			if( $qty <= 0 )	$qty = 0;

			//예외 사항 적용
			for( $j = 0; $j < count($except); $j++ )
			{
				// 1. 해당코드, 2. 칼라코드포함, 3. 사이즈코드 포함
				if( ( $except[$j]["goods_code"] == $xmd_goods_code ) || ( $except[$j]["goods_code"] == ($xmd_goods_code . $color_code) ) || ( $except[$j]["goods_code"] == ($xmd_goods_code . $color_code . $size_code) ) )
				{
					if( $store_cd == "bonsa" && $except[$j]["bonsa_cnt"] != "" )		$qty = $except[$j]["bonsa_cnt"];
					elseif( $store_cd != "bonsa" && $except[$j]["store_cnt"] != "" )	$qty = $except[$j]["store_cnt"];
				}
			}

			if( $store_cd != "bonsa" && $qty == 0 ){
			}else{

				// 물류재고 전체 혹은 매장재고중 수량이 0 보다 큰 데이터 등록
/*				
				$sql	= " 
					insert into xmd_stock_file( store_code, xmd_goods_code, color_code, size_code, xmd_goods_code_full, goods_nm, color_nm, price, wonga, qty )
					values ( :store_cd, :xmd_goods_code, :color_code, :size_code, :xmd_goods_code_full, :goods_nm, :color_nm, :now_price, :wonga, :qty )
				";
				DB::insert($sql,
					[
						'store_cd'				=> $store_cd,
						'xmd_goods_code'		=> $xmd_goods_code,
						'color_code'			=> $color_code,
						'size_code'				=> $size_code,
						'xmd_goods_code_full'	=> $xmd_goods_code_full,
						'goods_nm'				=> Lib::quote($goods_nm),
						'color_nm'				=> $color_nm,
						'now_price'				=> $now_price,
						'wonga'					=> $wonga,
						'qty'					=> $qty
					]
				);
*/
				if( $cnt != 0 )	$sql_insert .= ",";
				$sql_insert	.= " ( '$store_cd', '$xmd_goods_code', '$color_code', '$size_code', '$xmd_goods_code_full', '".Lib::quote($goods_nm)."', '$color_nm', '$now_price', '$wonga', '$qty' ) ";


				$cnt++;
			}

		}

		DB::insert($sql_insert);

		// 기존 등록 로그 비활성 처리
		$sql	= " update xmd_stock_file_log set use_yn = 'N' where store_cd = :store_cd ";
		DB::update($sql,['store_cd'	=> $store_cd]);

		// 신규 등록 로그 생성
		$sql	= "	
			insert into xmd_stock_file_log( store_cd, cnt, admin_id, admin_nm, use_yn, rt ) 
			values ( :store_cd, :cnt, :id, :name, 'Y', now() )
		";
		DB::insert($sql,
			[
				'store_cd'	=> $store_cd,
				'cnt'		=> $cnt,
				'id'		=> $id,
				'name'		=> $name
			]
		);

		$result_code	= "데이터가 등록 되었습니다. - [$store_cd]";

//		DB::commit();

		
		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}
