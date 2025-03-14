<?php

namespace App\Http\Controllers\store\cs;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Exports\ExcelViewExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

use App\Models\Conf;

const PRODUCT_STOCK_TYPE_STORAGE_RETURN = 9; // 상품반품

class cs02Controller extends Controller
{
    public function index()
	{
        $sup_coms = DB::table("company")
			->where('use_yn', '=', 'Y')->where('com_type', '=', '1')
			->select('com_id', 'com_nm')->get();

		$values = [
            'sdate'         => now()->sub(1, 'week')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'storages'      => SLib::getStorage(),
            'sup_coms'      => $sup_coms, // 공급업체 리스트
            'return_reason' => SLib::getCodes("RETURN_REASON")
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
        $sgr_type           = $request->input("sgr_type", "");
        $sgr_state          = $request->input("sgr_state", "");
        $return_reason      = $request->input("return_reason","");
        
        // where
        $sdate = str_replace("-", "", $sdate);
        $edate = str_replace("-", "", $edate);
        $where .= "
            and cast(sgr.sgr_date as date) >= '$sdate' 
            and cast(sgr.sgr_date as date) <= '$edate'
        ";
        if($storage_cd != "")           $where .= " and sgr.storage_cd = '$storage_cd'";
        if($target_com_cd != "")        $where .= " and sgr.target_cd = '$target_com_cd'";
        if($sgr_type != "")             $where .= " and sgr.sgr_type = '" . $sgr_type . "'";
        if($sgr_state != "")            $where .= " and sgr.sgr_state = '" . $sgr_state . "'";
        if($return_reason != "")        $where .= " and sgr.return_reason = '" . $return_reason . "'";

        // ordreby
        $ord_field  = 'sgr.' . $request->input("ord_field", "sgr_cd");
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
                (select storage_nm from storage where storage_cd = sgr.storage_cd) as storage_nm,
                sgr.target_type,
                sgr.target_cd,
                (select com_nm from company where com_id = sgr.target_cd) as target_nm,
                (select sum(return_qty) from storage_return_product where sgr_cd = sgr.sgr_cd) as sgr_qty,
                (select sum(return_price * return_qty) from storage_return_product where sgr_cd = sgr.sgr_cd) as sgr_price,
                sgr.comment,
                sgr.return_reason,
                c.code_val as return_reason_nm
            from storage_return sgr
                left outer join code c on c.code_id = sgr.return_reason and code_kind_cd = 'RETURN_REASON'
            where sgr.target_type = 'C' $where
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
                where sgr.target_type = 'C' $where
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
        $admin_nm = Auth('head')->user()->name;
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
                    select sr.sgr_cd, sr.sgr_prd_cd, sr.prd_cd, sr.return_qty
                        , pc.goods_opt, pc.goods_no
                        , if(pc.goods_no = 0, p.price, g.price) as price
                        , if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
                    from storage_return_product sr
                        inner join product p on p.prd_cd = sr.prd_cd
                        inner join product_code pc on pc.prd_cd = sr.prd_cd
                        left outer join goods g on g.goods_no = pc.goods_no
                    where sr.sgr_cd = :sgr_cd
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

                    // 재고이력 등록
                    DB::table('product_stock_hst')
                        ->insert([
                            'goods_no' => $row->goods_no,
                            'prd_cd' => $row->prd_cd,
                            'goods_opt' => $row->goods_opt,
                            'location_cd' => $d['storage_cd'],
                            'location_type' => 'STORAGE',
                            'type' => PRODUCT_STOCK_TYPE_STORAGE_RETURN,
                            'price' => $row->price,
                            'wonga' => $row->wonga,
                            'qty' => ($row->return_qty ?? 0) * -1,
							'stock_state_date' => date('Ymd'),
							'r_stock_state_date' => date('Ymd'),
                            'ord_opt_no' => '',
							'storage_return_no'	=> $row->sgr_prd_cd,
                            'comment' => '상품반품',
                            'rt' => now(),
                            'admin_id' => $admin_id,
                            'admin_nm' => $admin_nm,
                        ]);
                    
                    if($d['target_type'] == 'C') {
                        // product_stock -> 재고 / 창고재고 / 입고수량 차감
                        $r_qty = $row->return_qty ?? 0;
                        DB::table('product_stock')
                            ->where('prd_cd', '=', $row->prd_cd)
                            ->update([
                                'qty' => DB::raw('qty - ' . $r_qty),
                                'wqty' => DB::raw('wqty - ' . $r_qty),
                                'in_qty' => DB::raw('in_qty - ' . $r_qty),
                                'qty_wonga' => DB::raw('qty_wonga - ' . ($r_qty * ($row->wonga ?? 0))),
                                'ut' => now(),
                            ]);
                    }
                }
            }

            DB::commit();
            $code = 200;
            $msg = "상품반품이 정상적으로 완료처리되었습니다.";
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
        $com_addr = '';
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
                    sgr.return_addr,
                    (select com_nm from company where com_id = sgr.target_cd) as target_nm,
                    sgr.comment,
                    sgr.return_reason
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

            $sql = "
                select
                    addr1
                    , addr2
                    , zip_code
                from company
                where use_yn = 'Y' and com_type = '1'
            ";

            $com_addr = DB::selectOne($sql);
        }

        $values = [
            "cmd" => $sgr == '' ? "add" : "update",
            'sdate'         => $sgr == '' ? date("Y-m-d") : $sgr->sgr_date,
            'storages'      => SLib::getStorage(),
            'companies'     => $companies,
            'sgr'           => $sgr,
            'new_sgr_cd'    => $new_sgr_cd,
            'com_addr'      => $com_addr,
            'return_reason' => SLib::getCodes('RETURN_REASON')
        ];
        return view(Config::get('shop.store.view') . '/cs/cs02_show', $values);
    }

    // 기존에 반품등록된 상품목록 조회
    public function search_return_products(Request $request)
    {
        $sgr_cd = $request->input('sgr_cd', '');
        $sql = "
            select a.*
                , if (a.goods_no = 0, a.opt, a.opt_kind_cd) as opt_kind_cd
                , if (a.goods_no = 0, c.code_val, opt.opt_kind_nm) as opt_kind_nm
            from (
                select 
                    @rownum := @rownum + 1 as count,
                    srp.sgr_prd_cd, 
                    srp.sgr_cd, 
                    srp.prd_cd,
                    pc.prd_cd_p,
                    pc.goods_no,
                    if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm,
                    if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng,
                    if(pc.goods_no = 0, p.style_no, g.style_no) as style_no,
                    if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh,
                    pc.color,
                    c.code_val as color_nm,
                    ifnull((
						select s.size_cd from size s
						where s.size_kind_cd = pc.size_kind
						   and s.size_cd = pc.size
						   and use_yn = 'Y'
					),'') as size,		
                    pc.opt,
                    g.opt_kind_cd,
                    pc.brand as brand_cd,
                    b.brand_nm as brand,
                    srp.price,
                    srp.return_price, 
                    ifnull(pss.wqty, 0) as storage_wqty, 
                    srp.return_qty as qty,
                    (srp.return_qty * srp.return_price) as total_return_price,
                    true as isEditable
                from storage_return_product srp
                    inner join product_code pc on pc.prd_cd = srp.prd_cd
                    inner join product p on p.prd_cd = srp.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join storage_return sr on sr.sgr_cd = srp.sgr_cd
                    left outer join product_stock_storage pss on pss.storage_cd = sr.storage_cd and pss.prd_cd = srp.prd_cd
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join code c on c.code_id = pc.color and code_kind_cd = 'PRD_CD_COLOR'
                , (select @rownum :=0) as r
                where srp.sgr_cd = :sgr_cd
            ) a
                left outer join opt opt on opt.opt_kind_cd = a.opt_kind_cd and opt.opt_id = 'K'
                left outer join code c on c.code_kind_cd = 'PRD_CD_OPT' and c.code_id = a.opt
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
        $admin_nm = Auth('head')->user()->name;
        $sgr_type = $request->input("sgr_type", "G");
        $sgr_state = $sgr_type == "G" ? 10 : 30;
        $sgr_date = $request->input("sgr_date", date("Y-m-d"));
        $storage_cd = $request->input("storage_cd", "");
        $target_type = 'C';
        $target_cd = $request->input("target_cd", "");
        $return_addr = $request->input("return_addr","");
        $comment = $request->input("comment", "");
        $return_reason = $request->input("return_reason","");
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
                    'return_addr' => $return_addr,
                    'comment' => $comment,
                    'return_reason' => $return_reason,
                    'rt' => now(),
                    'admin_id' => $admin_id,
                ]);

            foreach($products as $product) {
                $sql = "
                    select pc.prd_cd, pc.goods_opt, pc.goods_no
                        , if(pc.goods_no = 0, p.price, g.price) as price
                        , if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
                    from product p
                        inner join product_code pc on pc.prd_cd = p.prd_cd
                        left outer join goods g on g.goods_no = pc.goods_no
                    where p.prd_cd = :prd_cd
                ";
                $prd = DB::selectOne($sql, ['prd_cd' => $product['prd_cd']]);
                if($prd == null) continue;

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

				$storage_return_no	= DB::getPdo()->lastInsertId();

                if($sgr_type == 'B') // 일괄등록의 경우 등록 시 완료처리
                {
                    // 창고 재고 차감
                    DB::table('product_stock_storage')
                    ->where('prd_cd', '=', $product['prd_cd'])
                    ->where('storage_cd', '=', $storage_cd) 
                    ->update([
                        'qty' => DB::raw('qty - ' . ($product['return_qty'] ?? 0)),
                        'wqty' => DB::raw('wqty - ' . ($product['return_qty'] ?? 0)),
                        'ut' => now(),
                    ]);

                    // 재고이력 등록
                    DB::table('product_stock_hst')
                    ->insert([
                        'goods_no' => $prd->goods_no,
                        'prd_cd' => $prd->prd_cd,
                        'goods_opt' => $prd->goods_opt,
                        'location_cd' => $storage_cd,
                        'location_type' => 'STORAGE',
                        'type' => PRODUCT_STOCK_TYPE_STORAGE_RETURN,
                        'price' => $prd->price,
                        'wonga' => $prd->wonga,
                        'qty' => ($product['return_qty'] ?? 0) * -1,
						'stock_state_date' => date('Ymd'),
						'r_stock_state_date' => date('Ymd'),
                        'ord_opt_no' => '',
						'storage_return_no'	=> $storage_return_no,
                        'comment' => '상품반품',
                        'rt' => now(),
                        'admin_id' => $admin_id,
                        'admin_nm' => $admin_nm,
                    ]);
                    
                    if ($target_type == 'C') {
                        // product_stock -> 재고 / 창고재고 / 입고수량 차감
                        $r_qty = $product['return_qty'] ?? 0;
                        DB::table('product_stock')
                            ->where('prd_cd', '=', $product['prd_cd'])
                            ->update([
                                'qty' => DB::raw('qty - ' . $r_qty),
                                'wqty' => DB::raw('wqty - ' . $r_qty),
                                'in_qty' => DB::raw('in_qty - ' . $r_qty),
                                'qty_wonga' => DB::raw('qty_wonga - ' . ($r_qty * ($prd->wonga ?? 0))),
                                'ut' => now(),
                            ]);
                    }
                }
            }

            DB::commit();
            $code = 200;
            $msg = "상품반품이 정상적으로 등록되었습니다.";
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
        $return_addr = $request->input("return_addr", "");
        $return_reason = $request->input("return_reason");
        $products = $request->input("products", []);

        try {
            DB::beginTransaction();

            DB::table('storage_return')
                ->where('sgr_cd', '=', $sgr_cd)
                ->update([
                    'return_addr' => $return_addr,
                    'comment' => $comment,
                    'return_reason' => $return_reason,
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
            $msg = "상품반품이 정상적으로 수정되었습니다.";
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(["code" => $code, "msg" => $msg]);
    }

    // 일괄등록 show
    public function batch_show()
    {
        $values = [
			'return_reason' => SLib::getCodes('RETURN_REASON')
		];
        return view(Config::get('shop.store.view') . '/cs/cs02_batch', $values);
    }

	// Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환
	public function import_excel(Request $request) {
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();
	
				$save_path = "data/store/cs02/";
				$file_name = "${now}_${user_id}.${extension}";
				
				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}
	
				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);
	
				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}

    // 일괄등록 상품 개별 조회
    public function get_goods(Request $request) {
        $data = $request->input('data', []);
        $target_nm = '';
        $storage_cd = '';
        $storage_nm = '';
        $sgr_idx = '';
		$return_addr = '';
        $result = [];

        foreach($data as $key => $d)
        {
            if($key < 1) 
            {
                $is_company = true;
                $row = DB::table($is_company ? 'company' : 'storage')->where($is_company ? 'com_id' : 'storage_cd', '=', $d['target_cd'])->first();
                $target_nm = $is_company ? $row->com_nm ?? '' : $row->storage_nm ?? '';
				$return_addr = ($row->addr1 ?? '') . ' ' . ($row->addr2 ?? '');
                
                $storage_cd = $d['storage_cd'];
                $row = DB::table('storage')->where('storage_cd', '=', $storage_cd)->first();
                $storage_nm = $row->storage_nm ?? '';

                $sql = "
                    select sgr_cd
                    from storage_return
                    order by sgr_cd desc
                    limit 1
                ";
                $row = DB::selectOne($sql);
                if($row == null) $sgr_idx = 1;
                else $sgr_idx = $row->sgr_cd + 1;
            }

            $prd_cd = $d['prd_cd'];
            $return_price = $d['return_price'];
            $return_qty = $d['return_qty'];
            $count = $d['count'] ?? '';
            $sql = "
                select a.*, com.com_nm, com.com_type as com_type_d
                    , if (a.goods_no = 0, a.opt, a.opt_kind_cd) as opt_kind_cd
                    , if (a.goods_no = 0, c.code_val, opt.opt_kind_nm) as opt_kind_nm
                from (
                    select pc.prd_cd, pc.prd_cd_p, pc.color, code.code_val as color_nm, pc.goods_no, pc.goods_opt, p.style_no, pc.opt
                       , ifnull((
							select s.size_cd from size s
							where s.size_kind_cd = pc.size_kind
							   and s.size_cd = pc.size
							   and use_yn = 'Y'
						),'') as size
                        , pc.brand as brand_cd
                        , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                        , if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng
                        , if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
                        , if(pc.goods_no = 0, p.price, g.price) as price
                        , if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
                        , if(pc.goods_no = 0, p.com_id, g.com_id) as com_id
                        , g.goods_type as goods_type_cd
                        , ifnull(type.code_val, 'N/A') as goods_type
                        , g.opt_kind_cd
                        , b.brand_nm as brand
                        , ps.qty as storage_qty, ps.wqty as storage_wqty
                        , '$return_price' as return_price, '$return_qty' as qty
                        , true as isEditable
                        , '$count' as count
                        , ('$return_price' * '$return_qty') as total_return_price
                    from product_code pc
                        inner join product p on p.prd_cd = pc.prd_cd
                        inner join product_stock s on pc.prd_cd = s.prd_cd
                        left outer join product_stock_storage ps on s.prd_cd = ps.prd_cd and ps.storage_cd = '$storage_cd'
                        left outer join goods g on g.goods_no = pc.goods_no
                        left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                        left outer join brand b on b.br_cd = pc.brand
                        left outer join code code on code.code_id = pc.color and code.code_kind_cd = 'PRD_CD_COLOR'
                    where pc.prd_cd = '$prd_cd'
                    limit 1
                ) a
                    left outer join company com on com.com_id = a.com_id
                    left outer join opt opt on opt.opt_kind_cd = a.opt_kind_cd and opt.opt_id = 'K'
                    left outer join code c on c.code_kind_cd = 'PRD_CD_OPT' and c.code_id = a.opt
            ";
            $row = DB::selectOne($sql);
            array_push($result, $row);
        }

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
                "target_nm" => $target_nm,
                "storage_nm" => $storage_nm,
                "sgr_idx" => $sgr_idx,
				"return_addr" => $return_addr,
            ],
            "body" => $result
        ]);
    }

    // 상품반품명세서 출력
	public function download(Request $request)
	{
		$sgr_cd = $request->input('sgr_cd');

		$sql = "
            select
                sr.sgr_cd
                , sr.storage_cd
                , sr.target_cd
                , sr.target_type
                , sr.sgr_date
                , sr.sgr_type
                , sr.sgr_state
                , sr.return_addr
                , sr.return_reason
                , (srp.return_price * srp.return_qty) as total_price
                , srp.prd_cd
                , g.goods_nm
                , g.goods_nm_eng
                , pc.color
                , ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
                , srp.price
                , srp.return_price
                , srp.return_qty
                , s.storage_nm
                , s.addr1
                , s.addr2
                , s.phone
                , s.fax
                , s.ceo
                , (select concat(addr1, ifnull(addr2, '')) from storage where storage_cd = s.storage_cd) as storage_addr
            from storage_return sr
                inner join storage s on s.storage_cd = sr.storage_cd and s.use_yn = 'Y'
                left outer join storage_return_product srp on srp.sgr_cd = sr.sgr_cd
                inner join product_code pc on pc.prd_cd = srp.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
            where srp.sgr_cd = :sgr_cd

		";
		$rows = DB::select($sql, [ 'sgr_cd' => $sgr_cd]);

		$data = [
			'one_sheet_count' => 36,
			'sgr_cd' => sprintf('%04d', $sgr_cd),
			'products' => $rows
		];

		if (count($rows) > 0) {
			$data['receipt_date']		= date('Y-m-d'); // 접수일자? 출고일자? 논의 후 수정필요
			$data['storage_nm'] 		= $rows[0]->storage_nm ?? '';
			$data['storage_phone'] 		= $rows[0]->phone ?? '';
			$data['storage_fax'] 		= $rows[0]->fax ?? '';
			$data['storage_addr'] 		= $rows[0]->storage_addr ?? '';
			$data['return_price'] 	    = $rows[0]->return_price ?? '';
			$data['return_qty'] 	    = $rows[0]->return_qty ?? '';
			$data['storage_ceo'] 	    = $rows[0]->ceo ?? '';
			$data['storage_cd'] 	    = $rows[0]->storage_cd ?? '';

			$conf = new Conf();
			$company = $conf->getConfig('shop');
			$data['business_registration_number'] = $company['business_registration_number'];
			$data['company_name'] = $company['company_name'];
			$data['company_ceo_name'] = $company['company_ceo_name'];
			$data['company_address'] = $company['company_address'];
			
			/* 하단 정보는 값등록 후 수정이 필요합니다. */
			$data['company_uptae'] = '도소매';
			$data['company_upjong'] = '의류,신발,악세서리';
			$data['company_office_phone'] = '02) 332-0018';
			$data['company_fax'] = '';
			$data['company_bank_number'] = '국민은행 / 730637-04-005212 / (주) 알펜인터내셔널';
			/* 상단 정보는 값등록 후 수정이 필요합니다. */
		}

        $style = [
			'A1:AH50' => [
				'alignment' => [
					'vertical' => Alignment::VERTICAL_CENTER,
					'horizontal' => Alignment::HORIZONTAL_CENTER
				],
				'font' => [ 'size' => 22, 'name' => '굴림' ]
			],
			'A3:AH3' => [
				'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ],
				'font' => [ 'size' => 25 ]
			],
			'A4' => [ 'alignment' => [ 'textRotation' => true ] ],
			'R4' => [ 'alignment' => [ 'textRotation' => true ] ],
			'A4:AH50' => [
				'borders' => [
					'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ],
					'outline' => [ 'borderStyle' => Border::BORDER_THICK ],
				],
			],
			'M5:Q5' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'AD5:AH5' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'AC47:AH50' => [ 'borders' => [ 'inside' => [ 'borderStyle' => Border::BORDER_NONE ] ] ],
			'A9:AH9' => [ 'borders' => [ 'top' => [ 'borderStyle' => Border::BORDER_THICK ] ] ],
			'E5:E6' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'V5:V6' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'F10:F45' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_LEFT ] ],
			'W10:AH46' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_RIGHT ] ],
			'B5:B8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 1 ] ],
			'S5:S8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 1 ] ],
			'J5:J8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 1 ] ],
			'AA5:AA8' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 1 ] ],
			'A46' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 20 ] ],
			'A47:A49' => [ 'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_DISTRIBUTED, 'indent' => 2 ] ],
			'V5' => [ 'font' => [ 'size' => 22 ] ],
			'V6' => [ 'font' => [ 'size' => 22 ] ],
			'Q5' => [ 'font' => [ 'size' => 16 ] ],
			'AH5' => [ 'font' => [ 'size' => 16 ] ],
			'B10:Q45' => [ 'font' => [ 'size' => 18 ] ],
			'Y10:AH45' => [ 'font' => [ 'size' => 19 ] ],
			'M2:V2' => [ 'borders' => [ 'bottom' => [ 'borderStyle' => Border::BORDER_THIN ] ] ],
			'K1' => [ 'font' => [ 'size' => 50, 'bold' => true ] ],
		];

		$view_url = Config::get('shop.store.view') . '/cs/cs02_document';
		$keys = [ 'list_key' => 'products', 'one_sheet_count' => $data['one_sheet_count'], 'cell_width' => 8, 'cell_height' => 48 ];
		$images = [[ 'title' => '인감도장', 'public_path' => '/img/stamp_sample.png', 'cell' => 'P4', 'height' => 150 ]];

		return Excel::download(new ExcelViewExport($view_url, $data, $style, $images, $keys), '상품반품명세서_' . $sgr_cd . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
}
