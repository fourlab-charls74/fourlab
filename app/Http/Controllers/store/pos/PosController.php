<?php

namespace App\Http\Controllers\store\pos;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

// 테스트매장 -- 추후변경필요
const STORE_CD = 'L0025';

class PosController extends Controller
{
    public function index() 
    {
        return view(Config::get('shop.store.view') . '/pos/pos');
    }

    public function search_command(Request $request, $cmd)
    {
        switch ($cmd) {
			case 'goods':
				$response = $this->search_goods($request);
				break;
            default:
                $message = 'Command not found';
                $response = response()->json(['code' => 0, 'msg' => $message], 200);
		};
		return $response;
    }

    // 상품검색
    public function search_goods(Request $request)
    {
        $store_cd = STORE_CD;
        $search_type = $request->input('search_type', 'prd_cd');
        $search_keyword = $request->input('search_keyword', '');

        $where = "";
        if ($search_keyword != '') {
            if ($search_type == 'prd_cd') {
                $where .= " and pc.prd_cd like '%$search_keyword%' ";
            } else if ($search_type == 'goods_nm') {
                $where .= " and g.goods_nm like '%$search_keyword%' ";
            }
        }

        $page = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;
        $limit = 100;

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "a_500";

        $sql = " 
            select 
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_sm
                , g.goods_no
                , pc.goods_opt
                , pc.color
                , pc.size
                , g.goods_nm
                , g.price
                , g.goods_sh
                , ps.wqty
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , '' as sale_type
                , '' as pr_code
                , '' as coupon
            from product_code pc
                inner join goods g on g.goods_no = pc.goods_no
                inner join product_stock_store ps on ps.prd_cd = pc.prd_cd and ps.wqty > 0 and ps.store_cd = '$store_cd'
            where 1=1 $where
            order by (CASE WHEN pc.year = '99' THEN 0 ELSE 1 END) desc, pc.year desc
            $limit
        ";
        $rows = DB::select($sql);

        if ($page == 1) {
            $sql = "
                select count(*) as total
                from product_code pc
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join product_stock_store ps on ps.prd_cd = pc.prd_cd and ps.wqty > 0 and ps.store_cd = '$store_cd'
                where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            'code' => 200,
            'head' => [
                'total' => $total,
                'page' => $page,
                'page_cnt' => $page_cnt,
                'page_total' => count($rows),
            ],
            'body' => $rows
        ], 200);
    }
}

