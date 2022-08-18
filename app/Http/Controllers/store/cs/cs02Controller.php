<?php

namespace App\Http\Controllers\store\cs;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;


class cs02Controller extends Controller
{
    public function index()
	{
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();
        $sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')->select('com_id', 'com_nm')->get();

		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'storages'      => $storages, // 창고 리스트
            'sup_coms'      => $sup_coms, // 공급업체 리스트
            'style_no'		=> "", // 스타일넘버
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
            'com_types'     => SLib::getCodes('G_COM_TYPE'), // 업체구분
            'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/cs/cs02', $values);
	}

    public function search(Request $request)
    {
		$where = "";
        $orderby = "";

        $sdate              = $request->input("sdate", now()->sub(1, 'week')->format('Ymd'));
        $edate              = $request->input("edate", date("Ymd"));
        $storage_cd         = $request->input("storage_cd", "");
        $target_com_cd      = $request->input("target_com_cd", "");
        $target_storage_cd  = $request->input("target_storage_cd", "");
        $sgr_type           = $request->input("sgr_type", "");
        $sgr_state          = $request->input("sgr_state", "");
        
        // where
        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);
        $where .= "
            and cast(sgr.sgr_date as date) >= '$sdate' 
            and cast(sgr.sgr_date as date) <= '$edate'
        ";
        if($storage_cd != "")           $where .= " and sgr.storage_cd = '$storage_cd'";
        if($target_com_cd != "")        $where .= " and sgr.target_type = 'C' and sgr.target_cd = '$target_com_cd'";
        if($target_storage_cd != "")    $where .= " and sgr.target_type = 'S' and sgr.target_cd = '$target_storage_cd'";
        if($sgr_type != "")             $where .= " and sgr.sgr_type = '" . $sgr_type . "'";
        if($sgr_state != "")            $where .= " and sgr.sgr_state = '" . $sgr_state . "'";

        // ordreby
        $ord_field  = 'sgr.' . $request->input("ord_field", "sgr_date");
        $ord        = $request->input("ord", "desc");
        $orderby    = sprintf("order by %s %s", $ord_field, $ord);
        
        // pagination
        $page       = $request->input("page", 1);
        $page_size  = $request->input("limit", 100);
        if ($page < 1 or $page == "") $page = 1;
        $startno    = ($page - 1) * $page_size;
        $limit      = " limit $startno, $page_size ";

        // search
		$sql = "
            select
                sgr.sgr_cd,
                sgr.sgr_date,
                sgr.sgr_type,
                if(sgr.sgr_type = 'G', '일반', if(sgr.sgr_type = 'B', '일괄', '-')) as sgr_type_nm,
                sgr.sgr_state,
                if(sgr.sgr_state = '10', '접수', if(sgr.sgr_state = '30', '완료', '-')) as sgr_state_nm,
                sgr.storage_cd,
                storage.storage_nm,
                sgr.target_type,
                sgr.target_cd,
                if(sgr.target_type = 'C', (select com_nm from company where com_id = sgr.target_cd), (select storage_nm from storage where storage_cd = sgr.target_cd)) as target_nm,
                (select sum(return_qty) from storage_return_product where sgr_cd = sgr.sgr_cd) as sgr_qty,
                (select sum(return_price * return_qty) from storage_return_product where sgr_cd = sgr.sgr_cd) as sgr_price,
                sgr.comment
            from storage_return sgr
                inner join storage on storage.storage_cd = sgr.storage_cd
            where 1=1 $where
            $orderby
            $limit
		";
		$result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                from storage_return sgr
                    inner join storage on storage.storage_cd = sgr.storage_cd
                where 1=1 $where
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			],
			"body" => $result
		]);
    }

    // 상품반품이동 완료처리
    public function update_return_state(Request $request)
    {
        $new_state = 30;
        $admin_id = Auth('head')->user()->id;
        $data = $request->input("data", []);

        try {
            DB::beginTransaction();

            foreach($data as $d) {
                DB::table('storage_return')
                    ->where('sgr_cd', '=', $d['sgr_cd'])
                    ->update([
                        'sgr_state' => $new_state,
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);
                
                $sql = "
                    select
                        sgr_cd, 
                        sgr_prd_cd,
                        prd_cd,
                        return_qty
                    from storage_return_product
                    where sgr_cd = :sgr_cd
                ";
                $rows = DB::select($sql, ["sgr_cd" => $d['sgr_cd']]);

                foreach($rows as $row) {
                    // 창고 재고 차감
                    DB::table('product_stock_storage')
                        ->where('prd_cd', '=', $row->prd_cd)
                        ->where('storage_cd', '=', $d['storage_cd']) 
                        ->update([
                            'qty' => DB::raw('qty - ' . ($row->return_qty ?? 0)),
                            'wqty' => DB::raw('wqty - ' . ($row->return_qty ?? 0)),
                            'ut' => now(),
                        ]);
                    
                    if($d['target_type'] == 'S') {
                        // 상품을 반품받은 창고 재고 플러스
                        DB::table('product_stock_storage')
                            ->where('prd_cd', '=', $row->prd_cd)
                            ->where('storage_cd', '=', $d['taret_cd'])
                            ->update([
                                'qty' => DB::raw('qty + ' . ($row->return_qty ?? 0)),
                                'wqty' => DB::raw('wqty + ' . ($row->return_qty ?? 0)),
                                'ut' => now(),
                            ]);
                    }
                }
            }

            DB::commit();
            $code = 200;
            $msg = "상품반품이동이 정상적으로 완료처리되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 창고반품 삭제
    public function del_return(Request $request)
    {
        $sgr_cds = $request->input("sgr_cds", []);

        try {
            DB::beginTransaction();

            foreach($sgr_cds as $sgr_cd) {
                DB::table('storage_return')
                    ->where('sgr_cd', '=', $sgr_cd)
                    ->delete();

                DB::table('storage_return_product')
                    ->where('sgr_cd', '=', $sgr_cd)
                    ->delete();
            }

            DB::commit();
            $code = 200;
            $msg = "삭제가 정상적으로 완료되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 상품반품이동 등록 & 상세팝업 오픈
    public function show($sgr_cd = '') 
    {
        $sgr = '';
        $new_sgr_cd = '';
        $storages = DB::table("storage")->where('use_yn', '=', 'Y')->select('storage_cd', 'storage_nm_s as storage_nm', 'default_yn')->orderByDesc('default_yn')->get();
        $companies = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')->select('com_id', 'com_nm')->get();

        if($sgr_cd != '') {
            $sql = "
                select
                    sgr.sgr_cd,
                    sgr.sgr_date,
                    sgr.sgr_type,
                    sgr.sgr_state,
                    sgr.storage_cd,
                    sgr.target_type,
                    sgr.target_cd,
                    if(sgr.target_type = 'C', (select com_nm from company where com_id = sgr.target_cd), (select storage_nm from storage where storage_cd = sgr.target_cd)) as target_nm,
                    sgr.comment
                from storage_return sgr
                where sgr_cd = :sgr_cd
            ";
            $sgr = DB::selectOne($sql, ['sgr_cd' => $sgr_cd]);
        } else {
            $sql = "
                select sgr_cd
                from storage_return
                order by sgr_cd desc
                limit 1
            ";
            $row = DB::selectOne($sql);
            if($row == null) $new_sgr_cd = 1;
            else $new_sgr_cd = $row->sgr_cd + 1;
        }

        $values = [
            "cmd" => $sgr == '' ? "add" : "update",
            'sdate'         => $sgr == '' ? date("Y-m-d") : $sgr->sgr_date,
            'storages'      => $storages,
            'companies'     => $companies,
            'sgr'           => $sgr,
            'new_sgr_cd'    => $new_sgr_cd,
        ];
        return view(Config::get('shop.store.view') . '/cs/cs02_show', $values);
    }

    // 기존에 반품등록된 상품목록 조회
    public function search_return_products(Request $request)
    {
        $sgr_cd = $request->input('sgr_cd', '');
        $sql = "
            select 
                @rownum := @rownum + 1 as count,
                srp.sgr_prd_cd, 
                srp.sgr_cd, 
                srp.prd_cd,
                pc.goods_no,
                ifnull(type.code_val, 'N/A') as goods_type,
                op.opt_kind_nm,
                b.brand_nm as brand, 
                g.style_no, 
                stat.code_val as sale_stat_cl, 
                g.goods_nm,
                pc.goods_opt,
                g.goods_sh,
                srp.price,
                srp.return_price, 
                ifnull(pss.wqty, 0) as storage_wqty, 
                srp.return_qty as qty,
                (srp.return_qty * srp.return_price) as total_return_price,
                true as isEditable
            from storage_return_product srp
                inner join product_code pc on pc.prd_cd = srp.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                left outer join storage_return sr on sr.sgr_cd = srp.sgr_cd
                left outer join product_stock_storage pss on pss.storage_cd = sr.storage_cd and pss.prd_cd = srp.prd_cd
                left outer join brand b on b.brand = g.brand
                left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id,
                (select @rownum :=0) as r
            where srp.sgr_cd = :sgr_cd
        ";
        $products = DB::select($sql, ['sgr_cd' => $sgr_cd]);

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($products),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
            ],
            "body" => $products
        ]);
    }

    // 상품반품이동 등록
    public function add_storage_return(Request $request)
    {
        $admin_id = Auth('head')->user()->id;
        $sgr_type = "G";
        $sgr_state = 10; // 일반등록 시 접수 상태로 등록
        $sgr_date = $request->input("sgr_date", date("Y-m-d"));
        $storage_cd = $request->input("storage_cd", "");
        $target_type = $request->input("target_type", "");
        $target_cd = $request->input("target_cd", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);

        try {
            DB::beginTransaction();

            if(count($products) < 1) {
                throw new Exception("반품등록할 상품을 선택해주세요.");
            }

            $sgr_cd = DB::table('storage_return')
                ->insertGetId([
                    'storage_cd' => $storage_cd,
                    'target_type' => $target_type,
                    'target_cd' => $target_cd,
                    'sgr_date' => $sgr_date,
                    'sgr_type' => $sgr_type,
                    'sgr_state' => $sgr_state,
                    'comment' => $comment,
                    'rt' => now(),
                    'admin_id' => $admin_id,
                ]);

            foreach($products as $product) {
                DB::table('storage_return_product')
                    ->insert([
                        'sgr_cd' => $sgr_cd,
                        'prd_cd' => $product['prd_cd'],
                        'price' => $product['price'], // 판매가
                        'return_price' => $product['return_price'], // 반품단가
                        'return_qty' => $product['return_qty'], // 반품수량
                        'rt' => now(),
                        'admin_id' => $admin_id,
                    ]);
            }

            DB::commit();
            $code = 200;
            $msg = "상품반품이동이 정상적으로 등록되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 상품반품이동 수정
    public function update_storage_return(Request $request)
    {
        $admin_id = Auth('head')->user()->id;
        $sgr_cd = $request->input("sgr_cd", "");
        $comment = $request->input("comment", "");
        $products = $request->input("products", []);

        try {
            DB::beginTransaction();

            DB::table('storage_return')
                ->where('sgr_cd', '=', $sgr_cd)
                ->update([
                    'comment' => $comment,
                    'ut' => now(),
                    'admin_id' => $admin_id,
                ]);

            foreach($products as $product) {
                DB::table('storage_return_product')
                    ->where('sgr_prd_cd', '=', $product['sgr_prd_cd'])
                    ->update([
                        'return_price' => $product['return_price'], // 반품단가
                        'return_qty' => $product['return_qty'], // 반품수량
                        'ut' => now(),
                        'admin_id' => $admin_id,
                    ]);
            }

            DB::commit();
            $code = 200;
            $msg = "상품반품이동이 정상적으로 수정되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(["code" => $code, "msg" => $msg]);
    }
}
