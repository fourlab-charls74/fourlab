<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Conf;
use App\Models\Jaego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class prd06Controller extends Controller
{
    public function index() {
        $edate = date("Y-m-d");
        $sdate = date('Y-m-d', strtotime(-7 . 'days'));

        $values = [
            "edate" => $edate,
            "sdate" => $sdate,
        ];
        return view( Config::get('shop.store.view') . '/product/prd06',$values);
    }

    public function search(Request $req)
    {
        $sdate              = $req->input("sdate", date('Y-m-d', strtotime(-7 . 'days')));
        $edate              = $req->input("edate", date("Y-m-d"));
        $price_apply_yn     = $req->input('price_apply_yn');
        $store_buffer       = $req->input('store_buffer');
        
        $where = "";
        if ($price_apply_yn != "") $where .= " and price_apply_yn = '$price_apply_yn' ";
        if ($store_buffer != "") $where .= " and store_buffer = '$store_buffer' ";

        $limit   = $req->input("limit", 100);

        $page = $req->input("page", 1);
        if ($page < 1 or $page == "") $page = 1;

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            // 갯수 얻기
            $sql =
                /** @lang text */
                " 
                select 
                    count(*) as total
                from bizest_stock_log
                where rt >= :sdate and rt < date_add(:edate,interval 1 day) $where
			";
            $row = DB::selectOne($sql, ['sdate' => $sdate, 'edate' => $edate]);
            $total = $row->total;
            if ($total > 0) {
                $page_cnt = (int)(($total - 1) / $page_size) + 1;
            }
        }

        $sql =
            /** @lang text */
            "
            select
				rt, match_y_cnt, match_n_cnt, store_cnt, price_apply_yn, store_buffer_kind, id
            from bizest_stock_log
            where rt >= :sdate and rt < date_add(:edate,interval 1 day) $where
            order by rt desc
            limit $startno,$page_size
            ";

        $rows = DB::select($sql, ['sdate' => $sdate, 'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function create()
    {
        $id = Auth('head')->user()->id;
        $default = DB::table('storage')
                    ->where('default_yn', '=', 'Y')
                    ->select('storage_cd', 'storage_nm')
                    ->first();

        $online = DB::table('storage')
                    ->where('online_yn', '=', 'Y')
                    ->select('storage_cd', 'storage_nm')
                    ->first();

        $values = [
            'id' => $id,
            'default' => $default,
            'online' => $online,
        ];

        return view(Config::get('shop.store.view') . '/product/prd06_show', $values);
    }

    public function search_store()
    {
        $sql = "
            select
                code_val, code_id 
            from code 
            where code_kind_cd = 'ONLINE_BUFFER_STORE'
            order by code_seq asc
        ";

        $result = DB::select($sql);

        return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body"	=> $result
		]);
    }

    public function search_product()
    {
        $sql = "
            select 
                *, '삭제' as del
            from bizest_stock_exp_product
        ";
        $result = DB::select($sql);

        return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> count($result),
			),
			"body"	=> $result
		]);
    }

    public function save(Request $request)
    {
        $admin_id                   = Auth('head')->user()->id;

        $price_apply_yn             = $request->input('price_apply_yn');
        $default_storage_cd         = $request->input('default_storage_cd');
        $default_storage_nm         = $request->input('default_storage_nm');
        $default_storage_buffer     = $request->input('default_storage_buffer');
        $online_storage_cd          = $request->input('online_storage_cd');
        $online_storage_nm          = $request->input('online_storage_nm');
        $online_storage_buffer      = $request->input('online_storage_buffer');
        $store_buffer_kind          = $request->input('store_buffer_kind');
        $store_tot_buffer           = $request->input('store_buffer');
        $store_data                 = json_decode($request->input('store_data'));
        $idArr                      = json_decode($request->input('idArr'));

        if($store_buffer_kind == 'A'){
            $store_data       = '';
            $idArr            = '';
        }

        try {
			DB::beginTransaction();

            DB::table('bizest_stock_conf')->insert([
                'default_storage_cd'        => $default_storage_cd,
                'default_storage_buffer'    => $default_storage_buffer,
                'online_storage_cd'         => $online_storage_cd,
                'online_storage_buffer'     => $online_storage_buffer,
                'store_buffer_kind'         => $store_buffer_kind,
                'store_tot_buffer'          => $store_tot_buffer,
                'price_apply_yn'            => $price_apply_yn,
                'rt' => now(),
                'ut' => now(),
                'id' => $admin_id
            ]);
            
            if($store_data != '') {
                foreach($store_data as $row) {
                    $code_id 	    = $row->code_id;
                    $code_val 	    = $row->code_val;
                    $store_buffer 	= $row->store_buffer;
                    
                    $sql = "select count(*) as count from bizest_stock_store where store_cd = :code_id";
                    $result	= DB::selectOne($sql, ['code_id' => $code_id]);
                    
                    if ($result->count == 0) {
                        DB::table('bizest_stock_store')
                        ->insert([
                            'store_cd' => $code_id,
                            'store_use_yn' => 'Y',
                            'buffer_cnt' => $store_buffer,
                            'rt' => now(),
                            'id' => $admin_id
                        ]);
                    } else {
                        DB::table('bizest_stock_store')
                        ->where('store_cd', '=', $code_id)
                        ->update([
                            'store_use_yn' => 'Y',
                            'buffer_cnt' => $store_buffer,
                            'ut' => now(),
                            'id' => $admin_id
                        ]);
                    }
                }
            }

            if($idArr != '') {
                foreach($idArr as $code_id) {
                    $sql = "select count(*) as count from bizest_stock_store where store_cd = :code_id";
                    $result	= DB::selectOne($sql, ['code_id' => $code_id]);
                    if ($result->count != 0) {
                        DB::table('bizest_stock_store')
                        ->where('store_cd', '=', $code_id)
                        ->update([
                            'store_use_yn' => 'N',
                            'ut' => now(),
                            'id' => $admin_id
                        ]);
                    }
                }
            }
			
			DB::commit();
			$code = 200;
			$msg = "성공";
		} catch (\Exception $e) {
			DB::rollback();
			$msg = $e->getMessage();
			$code = 500;
		}

		return response()->json(["code" => $code, "msg" => $msg]);
        
    }

    public function prd_update(Request $request)
	{
		$id		= Auth('head')->user()->id;
		$code	= "200";
        $datas	= json_decode($request->input('data'));

		if ($datas == "") {
			$code	= "400";
		}

		DB::beginTransaction();
		
        foreach($datas as $data) {
            $prd_cd = $data->prd_cd;
            $comment = $data->comment;
            $storage_limit_qty = $data->storage_limit_qty;
            $store_limit_qty = $data->store_limit_qty;
            $update = "";
            if($storage_limit_qty != null) $update .= "storage_limit_qty = '$storage_limit_qty', ";
            if($store_limit_qty != null) $update .= "store_limit_qty = '$store_limit_qty', ";

            $sql = "
                update bizest_stock_exp_product set
                    $update
                    comment = '" . Lib::quote($comment) . "',
                    id = '$id',
                    ut = now()
                where
                    prd_cd = :prd_cd
            ";
            DB::update($sql, ['prd_cd' => $prd_cd]);
        }

		DB::commit();

		return response()->json([
			"code" => $code
		]);

	}

    public function prd_delete(Request $request)
	{
		$code   = "200";
        $prd_cd	= $request->input('prd_cd');

		if( $prd_cd == "" ){
			$code = "400";
		}

		$sql = "delete from bizest_stock_exp_product where prd_cd = :prd_cd";
		DB::delete($sql, ['prd_cd' => $prd_cd]);

		return response()->json([
			"code" => $code,
		]);

	}

    public function add_show(Request $request) 
    {
		return view( Config::get('shop.store.view') . '/product/prd06_add_show');
    }

    public function add_save(Request $request)
	{
		$id		= Auth('head')->user()->id;
		$code	= "200";

        $prd_cd	            = $request->input('prd_cd');
		$comment	        = $request->input('comment');
		$storage_limit_qty	= $request->input('storage_limit_qty');
		$store_limit_qty	= $request->input('store_limit_qty');

		if( $prd_cd == "" ) {
			$error_code	= "400";
		}

		$sql	= "
			insert into bizest_stock_exp_prodcut( prd_cd, storage_limit_qty, store_limit_qty, comment, id, rt )
			values ( :prd_cd, :storage_limit_qty, :store_limit_qty, :comment, :id, now() )
		";
		$result = DB::select($sql, 
			[
				'prd_cd'	        => $prd_cd,
				'storage_limit_qty'	=> $storage_limit_qty,
				'store_limit_qty'	=> $store_limit_qty,
				'comment'		    => $comment,
				'id'			    => $id
			]
		);

		return response()->json([
			"code" => $code
		]);

	}
}
