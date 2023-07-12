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
			
			$sql	= "
				select idx from product_price pp
				where
					pp.change_type = 'R'
					and pp.apply_yn = 'N'
					and pp.change_date = :change_date
			";
			$rows = DB::select($sql,['change_date' => $chk_date]);
			foreach ($rows as $row) {
				$product_price_cd	= $row->idx;
				
				$sql_list = "
					select
						pc.goods_no, pc.prd_cd, ppl.change_price
					from product_price_list ppl
					inner join product_code pc on pc.prd_cd = ppl.prd_cd
					where
					    ppl.product_price_cd = :product_price_cd
				";
				$rows_list	= DB::select($sql_list,['product_price_cd' => $product_price_cd]);
				foreach ($rows_list as $row_list) {

					$goods_no		= $row_list->goods_no;
					$prd_cd			= $row_list->prd_cd;
					$change_price	= $row_list->change_price;

					//goods 테이블 price 가격변경
					if( $goods_no != 0 ){
						DB::table('goods')
							->where('goods_no', '=', $goods_no)
							->update(['price' => $change_price]);

						//product 테이블 price 가격변경
						DB::table('product')
							->where('prd_cd', '=', $prd_cd)
							->update(['price'=> $change_price]);
					}

				}

				//product_price 테이블 가격적용 상태 변경
				DB::table('product_price')
					->where('idx', '=', $product_price_cd)
					->update(['apply_yn'=> 'Y','ut' => DB::raw('now()')]);

				echo "test";

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
