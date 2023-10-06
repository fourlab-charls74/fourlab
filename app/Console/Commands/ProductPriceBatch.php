<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductPriceBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bizest:updatePrdPrice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update product price';

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
		// 해당일자에 대한 예약 가격 정보 불러오기
		$chk_date	= date("Y-m-d");

		try {
			DB::beginTransaction();
			
			$sql_list = "
				select
					ppl.idx, pc.goods_no, pc.prd_cd, ppl.change_price, ppl.plan_category
				from product_price_list ppl
				inner join product_price pp on ppl.product_price_cd = pp.idx
				inner join product_code pc on pc.prd_cd = ppl.prd_cd
				where
					ppl.change_type = 'R'
					and ppl.apply_yn = 'N'
					and ppl.change_date = :change_date
			";
			$rows_list	= DB::select($sql_list,['change_date' => $chk_date]);
			foreach ($rows_list as $row_list) {

				$idx			= $row_list->idx;
				$goods_no		= $row_list->goods_no;
				$prd_cd			= $row_list->prd_cd;
				$change_price	= $row_list->change_price;
				$plan_category	= $row_list->plan_category;

				//goods 테이블 price 가격변경
				if( $goods_no != 0 ){
					DB::table('goods')
						->where('goods_no', '=', $goods_no)
						->update(['price' => $change_price]);

					//product 테이블 price 가격변경
					//DB::table('product')
					//	->where('prd_cd', '=', $prd_cd)
					//	->update(['price'=> $change_price]);

					//if($plan_category != '00'){
					//	DB::table('product_code')
					//		->where('prd_cd', '=', $prd_cd)
					//		->update(['plan_category' => $plan_category]);
					//}
				}

				$sql = " select prd_cd, prd_cd_p from product_code where prd_cd = :prd_cd ";
				$product_result = DB::select($sql,['prd_cd' => $prd_cd]);

				foreach ($product_result as $pr) {
					DB::table('product')
						->where('prd_cd', 'like', $pr->prd_cd_p . '%')
						->update([
							"price" 	=> $change_price,
							"ut" => now()
						]);

					if($plan_category != '00'){
						DB::table('product_code')
							->where('prd_cd', 'like', $pr->prd_cd_p . '%')
							->update(['plan_category' => $plan_category]);
					}
				}

				//product_price 테이블 가격적용 상태 변경
				DB::table('product_price_list')
					->where('idx', '=', $idx)
					->update(['apply_yn'=> 'Y','ut' => DB::raw('now()')]);
			}

			DB::commit();
			$code = 0;
			
		} catch (Exception $e) {
			DB::rollback();
			$code = $e->getMessage();
		}
		
        return $code;
    }
}
