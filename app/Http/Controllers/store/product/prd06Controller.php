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
    public function index()
    {
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
        $store_buffer_kind  = $req->input('store_buffer_kind');
        
        $where = "";
        if ($price_apply_yn != "") $where .= " and price_apply_yn = '$price_apply_yn' ";
        if ($store_buffer_kind != "") $where .= " and store_buffer_kind = '$store_buffer_kind' ";

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

        $sql = "select *, count(*) as cnt from bizest_stock_conf";
        $result = DB::selectOne($sql);
        $cnt = $result->cnt;

        if($cnt == 0) {
            $values = [
                'id'                     => $id,
                'default'                => $default,
                'online'                 => $online,
                'idx'                    => '',
                'price_apply_yn'         => '',
                'default_storage_buffer' => '',
                'online_storage_buffer'  => '',
                'store_buffer_kind'      => '',
                'store_tot_buffer'       => '',
            ];
        
        //기존 config 데이터
        } else {
            $values = [
                'id'                     => $id,
                'default'                => $default,
                'online'                 => $online,
                'idx'                    => $result->idx,
                'price_apply_yn'         => $result->price_apply_yn,
                'default_storage_buffer' => $result->default_storage_buffer,
                'online_storage_buffer'  => $result->online_storage_buffer,
                'store_buffer_kind'      => $result->store_buffer_kind,
                'store_tot_buffer'       => $result->store_tot_buffer,
            ];
        }

        return view(Config::get('shop.store.view') . '/product/prd06_show', $values);
    }

    public function search_store()
    {
        $sql = "            
            select
                c.code_id, c.rt, c.ut, c.admin_id
            from code c
                left outer join bizest_stock_store b on b.store_cd = c.code_id
            where c.code_kind_cd = 'ONLINE_BUFFER_STORE' and c.use_yn = 'Y' and b.store_cd is NULL
            order by c.code_seq asc
        ";
        $rows = DB::select($sql);
        
        foreach ($rows as $row){
            DB::table('bizest_stock_store')->insert([
                'store_cd' => $row->code_id,
                'rt'       => $row->rt,
                'ut'       => $row->ut,
                'id'       => $row->admin_id
            ]);
        }
        
        $sql = "
            select
                c.code_id, s.store_nm, b.*
            from code c
                inner join store s on s.store_cd = c.code_id
                inner join bizest_stock_store b on b.store_cd = c.code_id
            where c.code_kind_cd = 'ONLINE_BUFFER_STORE' and c.use_yn = 'Y'
            order by c.code_seq asc
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

        $idx                        = $request->input('idx');
        $price_apply_yn             = $request->input('price_apply_yn');
        $default_storage_cd         = $request->input('default_storage_cd');
        $default_storage_nm         = $request->input('default_storage_nm');
        $default_storage_buffer     = $request->input('default_storage_buffer');
        $online_storage_cd          = $request->input('online_storage_cd');
        $online_storage_nm          = $request->input('online_storage_nm');
        $online_storage_buffer      = $request->input('online_storage_buffer');
        $store_buffer_kind          = $request->input('store_buffer_kind');
        $store_tot_buffer           = $request->input('store_tot_buffer');
        $store_data                 = json_decode($request->input('store_data'));
        $idArr                      = json_decode($request->input('idArr'));

        try {
			DB::beginTransaction();

            //기존 config 업데이트
            if($idx != '') {
                DB::table('bizest_stock_conf')
                    ->where('idx', '=', $idx)
                    ->update([
                        'default_storage_cd'       => $default_storage_cd,
                        'default_storage_buffer'   => $default_storage_buffer,
                        'online_storage_cd'        => $online_storage_cd,
                        'online_storage_buffer'    => $online_storage_buffer,
                        'store_buffer_kind'        => $store_buffer_kind,
                        'store_tot_buffer'         => $store_tot_buffer,
                        'price_apply_yn'           => $price_apply_yn,
                        'ut' => now(),
                        'id' => $admin_id
                ]);
            //config 신규등록
            } else {
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
            }

            //선택 매장
            if($store_data != '') {
                foreach($store_data as $row) {
                    $code_id 	    = $row->code_id;
                    $buffer_cnt 	= $row->buffer_cnt ?? null;
                    if($buffer_cnt == '') $buffer_cnt = null;

                    $sql = "select count(*) as count from bizest_stock_store where store_cd = :code_id";
                    $result	= DB::selectOne($sql, ['code_id' => $code_id]);

                    if ($result->count == 0) {
                        DB::table('bizest_stock_store')->insert([
                            'store_cd' => $code_id,
                            'store_use_yn' => 'Y',
                            'buffer_cnt' => $buffer_cnt,
                            'rt' => now(),
                            'ut' => now(),
                            'id' => $admin_id
                        ]);
                    } else {
                        DB::table('bizest_stock_store')
                        ->where('store_cd', '=', $code_id)
                        ->update([
                            'store_use_yn' => 'Y',
                            'buffer_cnt' => $buffer_cnt,
                            'ut' => now(),
                            'id' => $admin_id
                        ]);
                    }
                }
            }

            //미선택 매장
            if($idArr != '') {
                foreach($idArr as $code_id) {
                    $sql = "select count(*) as count from bizest_stock_store where store_cd = :code_id";
                    $result	= DB::selectOne($sql, ['code_id' => $code_id]);
                    if ($result->count == 0) {
                        DB::table('bizest_stock_store')->insert([
                            'store_cd' => $code_id,
                            'store_use_yn' => 'N',
                            'buffer_cnt' => null,
                            'rt' => now(),
                            'ut' => now(),
                            'id' => $admin_id
                        ]);
                    } else {
                        DB::table('bizest_stock_store')
                        ->where('store_cd', '=', $code_id)
                        ->update([
                            'store_use_yn' => 'N',
                            'buffer_cnt' => null,
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
            if($comment != null) $update .= "comment = '" . Lib::quote($comment) . "', ";

            $sql = "
                update bizest_stock_exp_product set
                    $update
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
			"code" => $code
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
			insert into bizest_stock_exp_product( prd_cd, storage_limit_qty, store_limit_qty, comment, id, rt )
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
	
	// 재고 동기화
	public function stock_batch()
	{
		$code	= "200";
		$msg	= "";
		$id		= Auth('head')->user()->id;
		
		try {

			//재고 Batch Conf 불러오기
			$sql    = "
				select
					default_storage_cd, ifnull(default_storage_buffer, 0) as default_storage_buffer,
					online_storage_cd, ifnull(online_storage_buffer, 0) as online_storage_buffer,
					store_buffer_kind, store_tot_buffer, price_apply_yn
				from bizest_stock_conf
				order by idx desc
				limit 1
			";
			$stock_conf	= DB::selectOne($sql);
	
			// 재고 등록 예외 일자 처리 시작
			$today	= date('Ymd');
			$sql	= " select count(*) as tot from bizest_stock_exp_date where exp_date = :exp_date ";
			$chk_date_cnt	= DB::selectOne($sql,['exp_date' => $today])->tot;
	
			if( $chk_date_cnt > 0 ){
				// 재고 등록일자별 작동 정지
				//return 1;
				//exit;
			}
			// 재고 등록 예외 일자 처리 종료
	
			//재고 매장 사용 수 구하기
			$sql	= " select count(*) as tot from bizest_stock_store where store_use_yn = 'Y' ";
			$stock_store_cnt	= DB::selectOne($sql)->tot;
	
			//0. 재고 로그 등록(초기화)
			$idx	= DB::table('bizest_stock_log')->insertGetId(
				[
					'price_apply_yn'    => $stock_conf->price_apply_yn,
					'store_buffer_kind' => $stock_conf->store_buffer_kind,
					'store_cnt'			=> $stock_store_cnt	,
					'match_y_cnt'		=> 0,
					'match_n_cnt'		=> 0,
					'id'				=> $id,
					'rt'				=> now(),
					'ut'				=> now()
				]
			);
	
			//1. 창고 재고 데이터 시작
			$sql    = "
				select
					pss.storage_cd, pss.prd_cd, pss.goods_no, pc.goods_opt, p.price, p.wonga, pc.prd_cd_p, pc.year,
					-- pss.wqty as qty
					if( pss.wqty > pss.qty, pss.qty, pss.wqty) as qty
				from product_stock_storage pss
				inner join product_code pc on pc.prd_cd = pss.prd_cd
				inner join product p on pss.prd_cd = p.prd_cd
				where
					pss.use_yn = 'Y'
					and ( pss.storage_cd = :default_storage_cd or pss.storage_cd = :online_storage_cd )
					-- and pss.wqty > 0
	
			";
			$rows = DB::select($sql, ['default_storage_cd' => $stock_conf->default_storage_cd, 'online_storage_cd' => $stock_conf->online_storage_cd]);
	
			$sql_insert	= " insert into bizest_stock_data( stock_log_cd, stock_cd, prd_cd, goods_no, goods_opt, price, wonga, qty ) values ";
	
			$cnt	= 0;
			foreach ($rows as $row) {

				if( $row->year == '24' ){

					/**/
					$sql_24chk	= " select prd_cd, prd_cd_p from product_stock_24_yn ";
					$row_chk	= DB::select($sql_24chk);

					$chk24		= 0;
					foreach ($row_chk as $row_24){
						if($row_24->prd_cd_p != ''){
							if( $row_24->prd_cd_p == $row->prd_cd_p )	$chk24 += 1;
						}else{
							if( $row_24->prd_cd == $row->prd_cd )		$chk24 += 1;
						}
					}
					
					if($chk24 > 0) continue;
					/**/
				}
				
				//대표창고 버퍼링 처리
				if( $row->storage_cd == $stock_conf->default_storage_cd && $stock_conf->default_storage_buffer != 0){
					$row->qty = $row->qty - $stock_conf->default_storage_buffer;
				}
	
				//온라인창고 버퍼링 처리
				if( $row->storage_cd == $stock_conf->online_storage_cd && $stock_conf->online_storage_buffer != 0){
					$row->qty = $row->qty - $stock_conf->online_storage_buffer;
					
					// 재고 감가 시작
					$sql_exp	= " select prd_cd, exp_cnt from product_stock_exp ";
					$row_exp	= DB::select($sql_exp);
					
					foreach( $row_exp as $exp ){
						if( $exp->prd_cd == $row->prd_cd ){
							$row->qty	= $row->qty - $exp->exp_cnt;
							
							if( $row->qty < 0 )	$row->qty = 0;
						}
					}
					// 재고 감가 종료
				}
	
				//임시
				//if( $row->storage_cd == $stock_conf->default_storage_cd ){
				//$row->qty	= floor($row->qty / 2);
				//}
	
				//if( $row->qty > 0){
				if( $cnt > 0 )	$sql_insert .= ",";
	
				$sql_insert	.= " ( '$idx', '$row->storage_cd', '$row->prd_cd', '$row->goods_no', '".Lib::quote($row->goods_opt)."', '$row->price', '$row->wonga', '$row->qty' ) ";
	
				$cnt++;
				//}
			}
	
			DB::insert($sql_insert);
			//1. 창고 재고 데이터 종료
	
			//2. 매장 재고 데이터 시작
			$store_cnt	= 0;
			$store_buffer	= array();
			$store_cds_arr	= "";
	
			$sql	= " select store_cd, buffer_cnt from bizest_stock_store where store_use_yn = 'Y' ";
			$rows	= DB::select($sql);
			foreach ($rows as $row) {
				$store_buffer[$row->store_cd]	= $row->buffer_cnt;
	
				if( $store_cnt > 0)	$store_cds_arr .= ",";
				$store_cds_arr	.= " '$row->store_cd' ";
	
				$store_cnt++;
			}
	
			if( $store_cnt > 0 ){
	
				$sql_insert	= " insert into bizest_stock_data( stock_log_cd, stock_cd, prd_cd, goods_no, goods_opt, price, wonga, qty ) values ";
	
				if( $stock_conf->store_buffer_kind == 'A'){
	
					// 매장 통합버퍼링
					$sql    = "
						select
							'store_all' as store_cd, pss.prd_cd, pss.goods_no, pc.goods_opt, max(p.price) as price, max(p.wonga) as wonga, sum(pss.wqty) as qty, pc.prd_cd_p, pc.year
						from product_stock_store pss
						inner join product_code pc on pc.prd_cd = pss.prd_cd
						inner join product p on pss.prd_cd = p.prd_cd
						where
							pss.use_yn = 'Y'
							and pss.store_cd in ($store_cds_arr)
							and pss.wqty > 0
						group by pss.prd_cd
	
					";
					$rows	= DB::select($sql);
	
					$cnt	= 0;
					foreach ($rows as $row) {

						/**/
						if( $row->year == '24' ){
							$sql_24chk	= " select prd_cd, prd_cd_p from product_stock_24_yn ";
							$row_chk	= DB::select($sql_24chk);

							$chk24		= 0;
							foreach ($row_chk as $row_24){
								if($row_24->prd_cd_p != ''){
									if( $row_24->prd_cd_p == $row->prd_cd_p )	$chk24 += 1;
								}else{
									if( $row_24->prd_cd == $row->prd_cd )		$chk24 += 1;
								}
							}

							if($chk24 > 0) continue;
						}
						/**/

						//매장 통합 버퍼링 처리
						if( $stock_conf->store_tot_buffer != 0){
							$row->qty = $row->qty - $stock_conf->store_tot_buffer;
						}
	
						if( $row->qty > 0 ){
							if( $cnt > 0 )	$sql_insert .= ",";
	
							$sql_insert	.= " ( '$idx', '$row->store_cd', '$row->prd_cd', '$row->goods_no', '".Lib::quote($row->goods_opt)."', '$row->price', '$row->wonga', '$row->qty' ) ";
	
							$cnt++;
						}
	
					}
	
				}else{
	
					// 매장 개별 버퍼링
					$sql    = "
						select
							pss.store_cd, pss.prd_cd, pss.goods_no, pc.goods_opt, p.price, p.wonga, pss.wqty as qty, pc.prd_cd_p, pc.year
						from product_stock_store pss
						inner join product_code pc on pc.prd_cd = pss.prd_cd
						inner join product p on pss.prd_cd = p.prd_cd
						where
							pss.use_yn = 'Y'
							and pss.store_cd in ($store_cds_arr)
							and pss.wqty > 0
	
					";
					$rows	= DB::select($sql);
	
					$cnt	= 0;
					foreach ($rows as $row) {

						if( $row->year == '24' ){
							$sql_24chk	= " select prd_cd, prd_cd_p from product_stock_24_yn ";
							$row_chk	= DB::select($sql_24chk);

							$chk24		= 0;
							foreach ($row_chk as $row_24){
								if($row_24->prd_cd_p != ''){
									if( $row_24->prd_cd_p == $row->prd_cd_p )	$chk24 += 1;
								}else{
									if( $row_24->prd_cd == $row->prd_cd )		$chk24 += 1;
								}
							}

							if($chk24 > 0) continue;
						}

						//매장 개별 버퍼링 처리
						if( $store_buffer[$row->store_cd] != 0){
							$row->qty = $row->qty - $store_buffer[$row->store_cd];
						}
	
						if( $row->qty > 0){
							if( $cnt > 0 )	$sql_insert .= ",";
	
							$sql_insert	.= " ( '$idx', '$row->store_cd', '$row->prd_cd', '$row->goods_no', '".Lib::quote($row->goods_opt)."', '$row->price', '$row->wonga', '$row->qty' ) ";
	
							$cnt++;
						}
	
					}
	
				}
	
				DB::insert($sql_insert);
	
			}
			//2. 매장 재고 데이터 종료
	
			//로그파일 업데이트
			$sql	= " select sum(if(goods_no = 0, 0, 1)) as match_y_cnt, sum(if(goods_no = 0, 1, 0)) as match_n_cnt from bizest_stock_data where stock_log_cd = :idx ";
			$stock_data	= DB::selectOne($sql,['idx' => $idx]);
	
			$sql	= "
				update bizest_stock_log set
					match_y_cnt = :match_y_cnt,
					match_n_cnt = :match_n_cnt,
					ut	= now()
				where
					idx = :idx
			";
			DB::update($sql,['match_y_cnt' => $stock_data->match_y_cnt, 'match_n_cnt' => $stock_data->match_n_cnt, 'idx' => $idx]);
	
			//기존 배치 작업 ↓
			$sql	= " DROP TABLE IF EXISTS _tmp_goods_xmd_stock ";
			DB::select($sql);
	
			$sql	= "
				create table _tmp_goods_xmd_stock
				select
					goods_no, goods_opt, sum(qty) as qty, max(price) as price, max(wonga) as cost
				from bizest_stock_data
				where
					goods_no <> '0'
					and stock_log_cd = :idx
				group by goods_no, goods_opt
			";
			DB::select($sql,['idx'	=> $idx]);
	
			//원가 적용
			$sql	= "
				update goods g inner join _tmp_goods_xmd_stock b on g.goods_no = b.goods_no
				set wonga = round(b.cost * 1.1)
			";
			//DB::update($sql);
	
			//가격 반영
			if( $stock_conf->price_apply_yn == 'Y' ){
	
				$sql	= " delete from _tmp_goods_xmd_stock_maxprice ";
				DB::delete($sql);
	
				$sql	= "
					insert into _tmp_goods_xmd_stock_maxprice
					select goods_no, price as max_price from _tmp_goods_xmd_stock
					group by goods_no
				";
				DB::insert($sql);
	
				$sql	= "
					update goods g inner join _tmp_goods_xmd_stock_maxprice b on g.goods_no = b.goods_no
					set g.price = b.max_price
				";
				//DB::update($sql); 잠시 막아놓음
			}
	
			//stock update
			$sql	= "
				update goods_summary s inner join _tmp_goods_xmd_stock b on s.goods_no = b.goods_no and s.goods_opt = b.goods_opt
				set good_qty = b.qty, wqty = b.qty, ut = now()
			";
			DB::update($sql);
	
			$sql	= "
				create table _tmp_goods_good
				select * from goods_good where opt_price is not null
			";
			DB::select($sql);
	
			$sql	= " truncate table goods_good ";
			DB::select($sql);
	
			//goods_good 데이터 생성
			$sql	= "
				insert into goods_good (goods_no,goods_sub,goods_opt,wonga,qty,invoice_no,init_qty,regi_date)
				select s.goods_no,s.goods_sub,s.goods_opt,g.wonga,s.good_qty,'',s.good_qty,now() from goods_summary s
					inner join goods g on g.goods_no = s.goods_no
			";
			DB::insert($sql);
	
			$sql	= "
				update goods_good a
					inner join _tmp_goods_good b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub and a.goods_opt = b.goods_opt
				set
					a.goods_opt = b.goods_opt
			";
			DB::update($sql);
	
			$sql	= " drop table _tmp_goods_good ";
			DB::select($sql);
	
			//재고 업로드 시 없는 상품 품절 처리
			$sql	= "
				UPDATE goods g INNER JOIN (
					SELECT
						goods_no
					FROM goods g WHERE sale_stat_cl = '40' AND ( SELECT IFNULL(SUM(good_qty),0) FROM goods_summary WHERE goods_no = g.goods_no ) = 0
				) a ON g.goods_no = a.goods_no
					SET sale_stat_cl = 30
				WHERE sale_stat_cl = '40'
			";
			DB::update($sql);
	
			//잠시 주석 처리
			// 품절수동일때 상품 상태 변경 안되게
			$sql	= "
				UPDATE goods g INNER JOIN (
					SELECT
						goods_no
					FROM goods g WHERE sale_stat_cl = '30' AND ( SELECT SUM(good_qty) FROM goods_summary WHERE goods_no = g.goods_no ) > 0
				) a ON g.goods_no = a.goods_no
					SET sale_stat_cl = 10
				WHERE sale_stat_cl = '30'
			";
			//DB::update($sql);
	
			//잠시 주석 처리
			// 품절수동일때 상품 상태 변경 안되게
			$sql	= "
				UPDATE goods g INNER JOIN (
					SELECT
						goods_no
					FROM goods g WHERE sale_stat_cl = '20' AND ( SELECT SUM(good_qty) FROM goods_summary WHERE goods_no = g.goods_no ) > 0
				) a ON g.goods_no = a.goods_no
					SET sale_stat_cl = 40
				WHERE sale_stat_cl = '20'
			";
			//DB::update($sql);
	
	
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([
			"code" => $code,
			"msg" => $msg
		]);

	}
}
