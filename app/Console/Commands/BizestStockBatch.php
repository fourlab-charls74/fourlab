<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use PDO;

class BizestStockBatch extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bizest:getPrdStock';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get product stock';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		// $sql    = "
		//     insert into _tmp_w(goods_no, price, wonga) values ('1234', '1000', '500')
		// ";
		// DB::insert($sql);

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
				'id'				=> 'admin',
				'rt'				=> now(),
				'ut'				=> now()
			]
		);

		//1. 창고 재고 데이터 시작
		$sql    = "
			select
				pss.storage_cd, pss.prd_cd, pss.goods_no, pc.goods_opt, p.price, p.wonga, pss.wqty as qty
			from product_stock_storage pss
			inner join product_code pc on pc.prd_cd = pss.prd_cd
			inner join product p on pss.prd_cd = p.prd_cd
			where
				pss.use_yn = 'Y'
				and ( pss.storage_cd = :default_storage_cd or pss.storage_cd = :online_storage_cd )
				and pss.wqty > 0

		";
		$rows = DB::select($sql, ['default_storage_cd' => $stock_conf->default_storage_cd, 'online_storage_cd' => $stock_conf->online_storage_cd]);

		$sql_insert	= " insert into bizest_stock_data( stock_log_cd, stock_cd, prd_cd, goods_no, goods_opt, price, wonga, qty ) values ";

		$cnt	= 0;
		foreach ($rows as $row) {
			//대표창고 버퍼링 처리
			if( $row->storage_cd == $stock_conf->default_storage_cd && $stock_conf->default_storage_buffer != 0){
				$row->qty = $row->qty - $stock_conf->default_storage_buffer;
			}

			//온라인창고 버퍼링 처리
			if( $row->storage_cd == $stock_conf->online_storage_cd && $stock_conf->online_storage_buffer != 0){
				$row->qty = $row->qty - $stock_conf->online_storage_buffer;
			}

			if( $row->qty > 0){
				if( $cnt > 0 )	$sql_insert .= ",";

				$sql_insert	.= " ( '$idx', '$row->storage_cd', '$row->prd_cd', '$row->goods_no', '".Lib::quote($row->goods_opt)."', '$row->price', '$row->wonga', '$row->qty' ) ";

				$cnt++;
			}
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
						'store_all' as store_cd, pss.prd_cd, pss.goods_no, pc.goods_opt, max(p.price) as price, max(p.wonga) as wonga, sum(pss.wqty) as qty
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
						pss.store_cd, pss.prd_cd, pss.goods_no, pc.goods_opt, p.price, p.wonga, pss.wqty as qty
					from product_stock_store pss
					inner join product_code pc on pc.prd_cd = pss.prd_cd
					inner join product p on pss.prd_cd = p.prd_cd
					where
						pss.use_yn = 'Y'
						and pss.store_cd in ($store_cds_arr)
						and pss.wqty > 0

				";
				$rows	= DB::select($sql);

				foreach ($rows as $row) {

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

		}

		DB::insert($sql_insert);
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
		DB::update($sql);

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
			DB::update($sql);
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



		return 0;
	}
}
