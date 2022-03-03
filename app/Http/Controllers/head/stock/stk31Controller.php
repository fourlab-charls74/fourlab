<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class stk31Controller extends Controller
{
    //
    public function index() {

        $values = [
            's_goods_code'	=> "",
            's_comment'		=> ""
        ];
        return view( Config::get('shop.head.view') . '/stock/stk31',$values);
    }

    public function search(Request $request)
    {
		$s_goods_code	= $request->input('s_goods_code');
		$s_comment		= $request->input('s_comment');


        $where	= "";
		if( $s_goods_code != "" )	$where .= " and goods_code = '$s_goods_code' ";
		if( $s_comment != "" )		$where .= " and comment like '%" . Lib::quote($s_comment) . "%' ";

		$query	= "
			select
				goods_code, bonsa_cnt, store_cnt, comment, '삭제' as del, idx
			from xmd_stock_file_except
			where 1=1  $where
		";
        $result = DB::select($query);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show()
	{

		$values = [
        ];

		return view( Config::get('shop.head.view') . '/stock/stk31_show',$values);
    }

	public function save(Request $request)
	{
		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$error_code		= "200";
		$result_code	= "";

        $goods_code	= $request->input('goods_code');
		$comment	= $request->input('comment');
		$bonsa_cnt	= $request->input('bonsa_cnt');
		$store_cnt	= $request->input('store_cnt');

		if( $goods_code == "" )
		{
			$error_code	= "400";
		}

		$sql	= "
			insert into xmd_stock_file_except( goods_code, bonsa_cnt, store_cnt, comment, admin_id, admin_nm, rt )
			values ( :goods_code, :bonsa_cnt, :store_cnt, :comment, :id, :name, now() )
		";
		$result = DB::select($sql, 
			[
				'goods_code'	=> $goods_code,
				'bonsa_cnt'		=> $bonsa_cnt,
				'store_cnt'		=> $store_cnt,
				'comment'		=> $comment,
				'id'			=> $id,
				'name'			=> $name
			]
		);

		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);

	}

	public function update(Request $request)
	{
		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$error_code		= "200";
		$result_code	= "";

        $datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

		DB::beginTransaction();
		
		for( $i = 0; $i < count($datas); $i++ )
		{
			$data	= (array)$datas[$i];

			$goods_code	= $data["goods_code"];
			$bonsa_cnt	= $data["bonsa_cnt"];
			$store_cnt	= $data["store_cnt"];
			$comment	= $data["comment"];
			$idx		= $data["idx"];

			$sql	= "
				update xmd_stock_file_except set
					goods_code	= '" . Lib::quote($goods_code) . "',
					bonsa_cnt	= '$bonsa_cnt',
					store_cnt	= '$store_cnt',
					comment		= '" . Lib::quote($comment) . "',
					admin_id	= '$id',
					admin_nm	= '$name',
					ut			= now()
				where
					idx = :idx
			";
			DB::update($sql, ['idx'	=> $idx]);
		}

		DB::commit();



		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);

	}

	public function delete(Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

        $idx	= $request->input('idx');

		if( $idx == "" )
		{
			$error_code	= "400";
		}

		$sql	= " delete from xmd_stock_file_except where idx = :idx ";
		DB::delete($sql, ['idx'	=> $idx]);

		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);

	}


}
