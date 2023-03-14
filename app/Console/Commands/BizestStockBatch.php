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
                default_storage_cd, default_storage_buffer, online_storage_cd, online_storage_buffer, store_tot_buffer, price_apply_yn
            from bizest_stock_conf
            order by idx desc
            limit 1
        ";
		$stock_conf    = DB::selectOne($sql);


        // 기존 등록된 중복 데이터 삭제
		$sql	= " select cnt from xmd_stock_file_log where use_yn = 'Y' and store_cd = :store_cd ";
		$row    = DB::selectOne($sql, ['store_cd' => 'batch']);

		if(!empty($row->cnt)) 
		{
			$sql	= " delete from xmd_stock_file where store_code = :store_cd ";
			DB::delete($sql, ['store_cd' => 'batch']);
		}

        //1. 창고 재고 준비

        $sql    = "
            select
                pss.prd_cd, pss.goods_no, pc.goods_opt, p.price, pss.wqty
            from product_stock_storage pss
            inner join product_code pc on pc.prd_cd = psss.prd_cd
            inner join product p on pss.prd_cd = p.prd_cd
            where
                and pss.use_yn = 'Y'
                and ( pss.storage_cd = :default_storage_cd or pss.storage_cd = :online_storage_cd )

        ";
        $rows = DB::select($sql, ['default_storage_cd' => $stock_conf->default_storage_cd, 'online_storage_cd' => $stock_conf->online_storage_cnd]);

        foreach ($rows as $row) {

        }

        return 0;
    }
}
