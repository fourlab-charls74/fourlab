<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class sal29Controller extends Controller
{
	public function index()
	{
		$sdate = now()->sub(1, 'week')->format('Y-m-d');
		$edate = date("Y-m-d");
		
		$values = [
			"edate"	=> $edate,
			"sdate"	=> $sdate,
		];
		return view(Config::get('shop.store.view') . '/sale/sal29', $values);
	}
	
	public function search(Request $request)
	{
		$rel 			= $request->input('rel');
		$baebun_date 	= $request->input('baebun_date');
		$baebun_date	= Carbon::parse($baebun_date)->format('ymd');
		$baebun_type 	= $request->input("baebun_type", "");

		$where = "";
		$where2 = "";

		if ($rel != "") $where .= " and psr.rel_order = '" .Lib::quote($rel) . "'";
		if ($baebun_date != "") $where .= " and psr.exp_dlv_day ='$baebun_date'";

		if( $baebun_type != "" ){
			$baebun_type_where	= "";
			for( $i = 0; $i < 6; $i++ ){
				if( !empty($baebun_type[$i]) ){
					if( $baebun_type_where != "" )	$baebun_type_where	.= " or ";
					$baebun_type_where	.= " psr.type = '" . $baebun_type[$i] . "' ";
				}
			}

			if( $baebun_type_where != "" ){
				$where2	.= " and ( $baebun_type_where ) ";
			}
		} else {
			$where2	.= " and ( psr.type < 0 ) ";
		}
		
		$sql = "
			select 
				distinct psr.prd_cd
				, psr.qty
				, s.size_cd
				, psr.store_cd
				, s.size_seq
			from product_stock_release psr
				inner join product_code pc on pc.prd_cd = psr.prd_cd
				inner join size s on s.size_cd = pc.size
			where 1=1 $where
			order by s.size_seq, psr.store_cd
		";
		
		$size_val = DB::select($sql);
		
		
		$size_sql = "";
		for ($i=0; $i<count($size_val); $i++) {
			$prd_cd = $size_val[$i]->prd_cd;
			$store_cd = $size_val[$i]->store_cd;
			$qty = $size_val[$i]->qty;
			$size_cd = $size_val[$i]->size_cd;
			$size_seq = $size_val[$i]->size_seq;

			$size_sql .= ", if(psr.prd_cd = '$prd_cd' and psr.store_cd = '$store_cd', $qty, '') as `$size_cd`";
			
		}
		
		
		

		$limit	= 100;
		$page	= $request->input("page", 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;

		$total		= 0;
		$page_cnt	= 0;

		$total_row = [];
		if ($page == 1) {

			$sql = "
				select 
					count(prd_cd) as total
					, sum(a.qty) as total_qty
				from  (
					select
						d.code_val as baebun_type
						, storage.storage_cd
						, storage.storage_nm
						, store.store_cd
						, store.store_nm
						, psr.prd_cd as prd_cd
						, pc.color
						, c.code_val as color_nm
						, date_format(psr.exp_dlv_day,'%Y-%m-%d') as baebun_date
						, psr.rel_order as rel_baebun
						, concat(psr.exp_dlv_day, '_', psr.rel_order) as rel_order
						, psr.qty as qty
						, g.goods_nm
						, g.goods_no
					from product_stock_release psr
						inner join storage storage on storage.storage_cd = psr.storage_cd
						inner join store store on store.store_cd = psr.store_cd
						inner join product_code pc on pc.prd_cd = psr.prd_cd
						left outer join code c on c.code_id = pc.color and c.code_kind_cd = 'PRD_CD_COLOR'
						left outer join code d on d.code_id = psr.type and d.code_kind_cd = 'REL_TYPE'
						left outer join goods g on g.goods_no = psr.goods_no
					where 1=1 $where $where2
					order by store.store_cd desc, psr.prd_cd desc
				) as a

			";

			$row = DB::select($sql);
				$total	= $row[0]->total;
				$total_row = $row[0];
				$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}
	
		$sql = "
			select
				d.code_val as baebun_type
				, storage.storage_cd
				, storage.storage_nm
				, store.store_cd
				, store.store_nm
				, psr.prd_cd as prd_cd
				, pc.color
				, c.code_val as color_nm
			    , (
                    select 
						s.size_cd 
                    from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size
				, date_format(psr.exp_dlv_day,'%Y-%m-%d') as baebun_date
				, psr.rel_order as rel_baebun
				, concat(psr.exp_dlv_day, '_', psr.rel_order) as rel_order
				, psr.qty as qty
				, g.goods_nm
				, g.goods_no
				$size_sql
			from product_stock_release psr
				inner join storage storage on storage.storage_cd = psr.storage_cd
				inner join store store on store.store_cd = psr.store_cd
				inner join product_code pc on pc.prd_cd = psr.prd_cd
				left outer join code c on c.code_id = pc.color and c.code_kind_cd = 'PRD_CD_COLOR'
				left outer join code d on d.code_id = psr.type and d.code_kind_cd = 'REL_TYPE'
				left outer join goods g on g.goods_no = psr.goods_no
			where 1=1 $where $where2
			order by store.store_cd desc, psr.prd_cd desc
            ";
		
		$rows = DB::select($sql);
		
		
//		$sql = "
//			select 
//				distinct(s.size_cd)
//				, s.size_kind_cd
//			from size s
//				inner join product_code pc on pc.size = s.size_cd
//				left outer join product_stock_release psr on psr.prd_cd = pc.prd_cd
//			where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
//				and s.size_cd = pc.size
//				and use_yn = 'Y'
//			order by s.size_seq
//		";
		
		$sql = "
			select
				(
					select
						s.size_cd
					from size s
					where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
					and s.size_cd = pc.size
					and use_yn = 'Y'
				) as size
				, s.size_seq
			from product_stock_release psr
				inner join product_code pc on pc.prd_cd = psr.prd_cd
				inner join size s on s.size_cd = pc.size
			where 1=1 $where
			group by size
			order by s.size_seq asc
		";
		
		$sizes = DB::select($sql);
		
		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($rows),
				"total_row" => $total_row,
				"sizes"		=> $sizes,
			),
			"body" => $rows
		]);
		
	}

	public function searchBaebun(Request $request)
    {
        $sdate = $request->input('sdate');
        $edate = $request->input('edate');

        $where = "";

        if ($sdate != "") $where .= " and date_format(psr.exp_dlv_day,'%Y-%m-%d') >= '$sdate'";
        if ($edate != "") $where .= " and date_format(psr.exp_dlv_day,'%Y-%m-%d') <= '$edate'";

        $sql = "
            select
                storage.storage_cd
                , storage.storage_nm
                , store.store_cd
                , store.store_nm
                , date_format(psr.exp_dlv_day,'%Y-%m-%d') as baebun_date
                , psr.rel_order as rel_baebun
                , concat(psr.exp_dlv_day, '_', psr.rel_order) as rel_order
                , sum(psr.qty) as baebun_qty
                , psr.state as state
                , count(distinct store.store_cd) as store_cnt
                , '선택' as select_rows
            from product_stock_release psr
                inner join storage storage on storage.storage_cd = psr.storage_cd
                inner join store store on store.store_cd = psr.store_cd
            where 1=1 $where
            group by rel_order
            order by psr.exp_dlv_day desc, rel_baebun desc
        ";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }
}
