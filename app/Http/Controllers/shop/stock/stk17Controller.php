<?php

namespace App\Http\Controllers\shop\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk17Controller extends Controller
{
    public function index()
	{
		$values = [
            'today'         => date("Y-m-d"),
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
            'types'         => SLib::getCodes("PRD_MATERIAL_TYPE"), // 원부자재 구분
            'opts'          => SLib::getCodes("PRD_MATERIAL_OPT"), // 원부자재 품목
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'rel_orders'    => SLib::getCodes("REL_ORDER"), // 출고차수
		];

        return view(Config::get('shop.shop.view') . '/stock/stk17', $values);
	}

    public function search(Request $request)
    {
        $req = $request->all();

        $code = 200;
        $where = "";

        // where
        if ($req['prd_cd_sub'] != null) {
            $prd_cd = explode(',', $req['prd_cd_sub']);
            $where .= " and (1!=1";
            foreach ($prd_cd as $cd) {
                $where .= " or p.prd_cd like '%" . Lib::quote($cd) . "%' ";
            }
            $where .= ")";
        }
        if (($req['ext_storage_qty'] ?? 'false') == 'true') $where .= " and (pss.wqty != '' and pss.wqty != '0')";

        if ($req['type'] != "") $where .= " and pc.brand = '" . Lib::quote($req['type']) . "'";
        if ($req['opt'] != "") $where .= " and pc.opt = '" . Lib::quote($req['opt']) . "'";
		if ($req['prd_nm'] != "") $where .= " and p.prd_nm like '" . Lib::quote($req['prd_nm']) . "%' ";

        // orderby
        $ord = $req['ord'] ?? 'desc';
        $ord_field = $req['ord_field'] ?? "p.prd_cd";
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page = $req['page'] ?? 1;
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $req['limit'] ?? 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        // search
        $store_cd = Auth('head')->user()->store_cd;
        $store_cd = Lib::quote( $store_cd );
            
		$sql = /** @lang text */ "
            select
                c.code_val as type_nm,
                c2.code_val as opt,
                i.img_url as img,
                p.prd_cd as prd_cd,
                p.prd_nm as prd_nm,
                c3.code_val as color,
				size.size_nm as size,
                c5.code_val as unit,
                ifnull(p.price, 0) as price,
                ifnull(p.wonga, 0) as wonga,
                ifnull(pss.qty, 0) as storage_qty,
                ifnull(pss.wqty, 0) as storage_wqty,
                '0' as rel_qty,
                '0' as amount
            from product p
                inner join product_stock_storage pss on pss.prd_cd = p.prd_cd
                inner join product_code pc on p.prd_cd = pc.prd_cd
                left outer join product_image i on p.prd_cd = i.prd_cd
                inner join company cp on p.com_id = cp.com_id
                left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
                left outer join size size on size.size_cd = pc.size and size_kind_cd = 'PRD_CD_SIZE_UNISEX'
                left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
            where 
                pss.storage_cd = (select storage_cd from storage where default_yn = 'Y')  and p.type <> 'N'
                $where
            $orderby
            $limit
        ";
		$result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if ($page == 1) {
            $sql = "
                select
                    count(*) as total
                from product p
                    inner join product_stock_storage pss on p.prd_cd = pss.prd_cd
                    inner join product_code pc on p.prd_cd = pc.prd_cd
                    left outer join product_image i on p.prd_cd = i.prd_cd
					inner join company cp on p.com_id = cp.com_id
                    left outer join `code` c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
                    left outer join `code` c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
                left outer join `code` c3 on c3.code_kind_cd = 'PRD_CD_COLOR' and c3.code_id = pc.color
					left outer join size size on size.size_cd = pc.size and size_kind_cd = 'PRD_CD_SIZE_UNISEX'
					left outer join `code` c5 on c5.code_kind_cd = 'PRD_CD_UNIT' and c5.code_id = p.unit
                where 
                    pss.storage_cd = (select storage_cd from storage where default_yn = 'Y') and p.type <> 'N'
                $where
            ";
            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		return response()->json([
			"code" => $code,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result),
			],
			"body" => $result
		]);
    }

    // 요청분출고 요청
    public function request_release(Request $request) {
        $release_type = 'R';
        $state = 10;
        $admin_id = Auth('head')->user()->id;

        $store_cd = Auth('head')->user()->store_cd;
        $data = $request->input("products", []);
        // $exp_dlv_day = $request->input("exp_dlv_day", '');
        // $rel_order = $request->input("rel_order", '');
        
        $sql = "select storage_cd from storage where default_yn = 'Y'";
        $storage_cd = DB::selectOne($sql)->storage_cd;
		$rel_date = date("Ymd");

        try {
            DB::beginTransaction();

			$last_rel_no = DB::table('sproduct_stock_release')->orderByDesc('idx')->value('release_no');
			$seq = 1;
			if ($last_rel_no != null) {
				$seq = explode('_', $last_rel_no);
				$seq = ($seq[count($seq) - 1] * 1) + 1;
			}
			$release_no = $release_type . '_' . $rel_date . '_' . $seq;

			foreach($data as $row) {
                DB::table('sproduct_stock_release')
                    ->insert([
                        'type' => $release_type,
						'release_no' => $release_no,
                        'prd_cd' => $row['prd_cd'],
                        'price' => $row['price'],
                        'wonga' => $row['wonga'],
						'qty' => $row['rel_qty'] ?? 0, // 요청수량
						'rec_qty' => $row['rel_qty'] ?? 0, // 접수수량
						'prc_qty' => $row['rel_qty'] ?? 0, // 출고수량
                        'store_cd' => $store_cd,
                        'storage_cd' => $storage_cd,
                        'state' => $state,
                        'req_id' => $admin_id,
                        'req_comment' => $row['req_comment'] ?? '',
                        'req_rt' => now(),
                        'rt' => now(),
                    ]);
            }
            $code = 200;
			DB::commit();
		} catch (\Exception $e) {
            // $msg = $e->getMessage();
            $code = 500;
			DB::rollback();
		}
        return response()->json(["code" => $code]);
    }
}
