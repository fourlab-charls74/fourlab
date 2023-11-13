<?php

namespace App\Http\Controllers\store\sale;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Exports\ExcelViewExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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
		$values = $this->_get($request);
		$rows = $values['result'];
		$size_cols = $values['sizes'];

		return response()->json([
			"code" => 200,
			"head" => [
				"total"	=> count($rows),
				"page" => 1,
				"page_cnt" => 1,
				"page_total" => 1,
				"sizes"	=> $size_cols,
			],
			"body" => $rows,
		]);
	}
	
	public function _get(Request $request, $sort_id = '', $sort_type = '') 
	{
		$rel 			= $request->input('rel');
		$baebun_date 	= $request->input('baebun_date');
		$baebun_date	= Carbon::parse($baebun_date)->format('ymd');
		$baebun_type 	= $request->input("baebun_type", "");
		$ord_field		= $request->input('ord_field', 'store');

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

		// 해당사이즈 조회 (FREE + 해당사이즈)
		$sql = "
			select s.size_kind_cd, s.size_kind_nm, s.size_kind_nm as size_kind_nm_s, s.size_cd, s.size_seq
			from (
				select s.size_kind_cd, sk.size_kind_nm, s.size_cd, s.size_seq, sk.seq as size_kind_seq
				from size s
					inner join size_kind sk on sk.size_kind_cd = s.size_kind_cd
			) s
			where s.size_cd <> '99' and s.size_kind_cd in (
				select pc.size_kind
				from product_stock_release psr
					inner join product_code pc on pc.prd_cd = psr.prd_cd
				where 1=1 $where $where2
				group by pc.size_kind
			)
			order by s.size_kind_seq, s.size_seq
		";
		$free_size = DB::select(
			"select size_kind_cd, size_cd, size_seq from size where size_kind_cd = :size_kind_cd and size_cd = :size_cd",
			[ 'size_kind_cd' => 'FREE', 'size_cd' => '99' ],
		);
		$size_list = array_merge($free_size, DB::select($sql));

		$size_sql = "";
		$size_sum_sql = "";
		$size_group = [];

		foreach ($size_list as $size) {
			if ($size->size_kind_cd === 'FREE') {
				$size_sql .= ", if(pc.size = '$size->size_cd', psr.qty, 0) as '" . $size->size_kind_cd . "^" . $size->size_cd . "'";
				$size_group[$size->size_kind_cd] = [$size];
			} else {
				$size_sql .= ", if(pc.size_kind = '$size->size_kind_cd' and pc.size = '$size->size_cd', psr.qty, 0) as '" . $size->size_kind_cd . "^" . $size->size_cd . "'";
				if (is_array($size_group[$size->size_kind_cd] ?? '')) array_push($size_group[$size->size_kind_cd], $size);
				else $size_group[$size->size_kind_cd] = [$size];
			}
			$size_sum_sql .= ", sum(p.`" . $size->size_kind_cd . "^" . $size->size_cd . "`) as 'SIZE^" . $size->size_kind_cd . "^" . $size->size_cd . "'";
		}

		$max_group_length = max(array_map(function ($c) { return count($c); }, $size_group));
		$size_group = array_map(function ($c) use ($max_group_length) {
			return array_pad($c, $max_group_length, 0);
		}, $size_group);

		$size_cols = [];
		foreach ($size_group as $key => $value) {
			$cnt = 1;
			foreach ($value as $k => $val) {
				if ($key === 'FREE') {
					if ($k >= count($size_group) - 1) continue;
					if (!is_array($size_cols[0] ?? '')) $size_cols[0] = [$val];
					else array_push($size_cols[0], $val);
				} else {
					if (is_array($size_cols[$cnt] ?? '')) array_push($size_cols[$cnt], $val);
					else $size_cols[$cnt] = [$val];
				}
				$cnt++;
			}
		}

		$groupby = "";
		if ($ord_field === 'store') {
			$groupby .= "group by p.store_cd, p.prd_cd_p, p.color";
		} else if ($ord_field === 'product') {
			$groupby .= "group by p.prd_cd_p, p.color, p.store_cd";
		}

		$orderby = "";
		if ($sort_id !== '') {
			if ($ord_field === 'store') {
				if ($sort_id === 'store_cd') {
					$orderby = "order by store_cd " . $sort_type . ", prd_cd_p_color";
				} else {
					$orderby = "order by store_cd, " . $sort_id . " " . $sort_type . ", prd_cd_p_color";
				}
			} else if ($ord_field === 'product') {
				if ($sort_id === 'prd_cd_p_color') {
					$orderby = "order by prd_cd_p_color " . $sort_type . ", store_cd";
				} else if ($sort_id === 'prd_cd_p') {
					$orderby = "order by prd_cd_p " . $sort_type . ", color, store_cd";
				} else if ($sort_id === 'goods_nm') {
					$orderby = "order by goods_nm " . $sort_type . ", prd_cd_p_color, store_cd";
				} else {
					$orderby = "order by prd_cd_p_color, " . $sort_id . " " . $sort_type . ", store_cd";
				}
			}
		}

		$sql = "
			select a.*
			    , a.qty as qty_tot
				, type.code_val as baebun_type
			    , color.code_val as color_nm
				, s.store_nm
				, g.goods_nm
				, concat(a.prd_cd_p, a.color) as prd_cd_p_color
			from (
				select p.type, p.store_cd, p.prd_cd, p.prd_cd_p, p.goods_no, p.color, p.size_kind, p.size_kind_nm as size_kind_nm_p
					, sum(p.qty) as qty
					$size_sum_sql
				from (
					select psr.type, psr.store_cd, psr.prd_cd, psr.goods_no, psr.qty
						, if(pc.prd_cd_p <> '', pc.prd_cd_p, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt)) as prd_cd_p
						, pc.color, pc.size, pc.size_kind, sk.size_kind_nm
						$size_sql
					from product_stock_release psr
						inner join product_code pc on pc.prd_cd = psr.prd_cd
						inner join size_kind sk on sk.size_kind_cd = pc.size_kind
					where 1=1 $where $where2
				) p
					$groupby
			) a
				inner join store s on s.store_cd = a.store_cd
				inner join code type on type.code_kind_cd = 'REL_TYPE' and type.code_id = a.type
				left outer join code color on color.code_kind_cd = 'PRD_CD_COLOR' and color.code_id = a.color
				left outer join goods g on g.goods_no = a.goods_no
			$orderby
		";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$arr = array_filter((array) $row, function ($v, $k) use ($row) {
				return str_contains($k, 'FREE') || str_contains($k, $row->size_kind);
			}, ARRAY_FILTER_USE_BOTH);
			
			foreach ($size_cols as $index => $col) {
				if ($index < 1 || in_array($row->size_kind, array_column($col, 'size_kind_cd'))) {
					$key_object = array_values(array_filter($col, function ($c) use ($row) { 
						return (($c->size_cd ?? '') === '99') || (($c->size_kind_cd ?? '') === $row->size_kind); 
					}));
					$key_object = $key_object[0] ?? [];
					$row->{'SIZE_' . $index} = $arr['SIZE^' . ($key_object->size_kind_cd ?? '') . '^' . ($key_object->size_cd ?? '') ];
				}
			}
		}

		return [ 'result' => $rows, 'sizes' => $size_cols ];
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
            group by psr.exp_dlv_day, rel_order
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

	public function download(Request $request)
	{
		$rel 			= $request->input('rel');
		$baebun_date 	= $request->input('baebun_date');
		$baebun_date	= Carbon::parse($baebun_date)->format('ymd');
		$ord_field		= $request->input('ord_field', 'store');
		$columns		= $request->input('columns', '');
		$columns		= explode('^', $columns);
		
		$sort_id		= $request->input('sort_id', '');
		$sort_type		= $request->input('sort_type', '');

		$values = $this->_get($request, $sort_id, $sort_type);
		$rows = $values['result'];
		$size_cols = $values['sizes'];
		
		$headers = [];
		if ($ord_field === 'store') {
			$headers = [['배분구분', 'baebun_type'], ['매장코드', 'store_cd'], ['매장명', 'store_nm', 2], ['품번', 'prd_cd_p', 2], ['상품명', 'goods_nm', 5], ['컬러', 'color'], ['컬러명', 'color_nm', 2],['사이즈구분', 'size_kind_nm_p',3],['수량합계', 'qty_tot'], ['수량', 'qty']];
		} else if ($ord_field === 'product') {
			$headers = [['배분구분', 'baebun_type'], ['품번', 'prd_cd_p', 2], ['상품명', 'goods_nm', 5], ['컬러', 'color'], ['컬러명', 'color_nm', 2], ['매장코드', 'store_cd'], ['매장명', 'store_nm', 2], ['수량', 'qty']];
		}

		$headers = array_reduce($columns, function ($a, $c) use ($headers, $size_cols) {
			$idx = array_search($c, array_map(function ($header) { return $header[1]; }, $headers));
			if ($idx === false) {
				if ($c === 'size_kind_nm') {
					$size_kind = [];
					foreach ($size_cols[1] as $ss) {
						$size_kind[] = (object) [ 'size_cd' => $ss->size_kind_nm_s ];
					}
					$size_kind[] = $c;
					array_push($a, [$size_kind]);
				} else if (strpos($c, 'SIZE_') !== false) {
					$idx = explode('_', $c)[1];
					$size_cols[$idx][] = $c;
					array_push($a, [$size_cols[$idx]]);
				}
			} else {
				array_push($a, $headers[$idx]);
			}
			return $a;
		}, []);

		$group_key = $ord_field === 'store' ? 'store_cd' : 'prd_cd_p_color';
		$rows = array_reduce($rows, function ($a, $c) use ($group_key) {
			if (isset($a[$c->{$group_key}])) array_push($a[$c->{$group_key}], $c);
			else $a[$c->{$group_key}] = [$c];
			return $a;
		}, []);
		
		$total_rows = [];
		$rows = array_reduce($rows, function ($a, $c) use (&$total_rows) {
			$a = array_merge($a, $c);
			$sum_row = array_reduce($c, function ($aa, $cc) {
				$keys = array_filter(array_keys((array) $cc), function ($ft) { return str_contains($ft, 'SIZE_') || $ft === 'qty'; });
				foreach ($keys as $key) {
					$aa[$key] = ($aa[$key] ?? 0) + ($cc->{$key} ?? 0);
				}
				return $aa;
			}, []);
			$sum_row['sum'] = true;
			array_push($a, (object) array_merge((array) $c[0], $sum_row));
			array_push($total_rows, (object) $sum_row);
			return $a;
		}, []);
		$total_row = array_reduce($total_rows, function ($aa, $cc) {
			$keys = array_filter(array_keys((array) $cc), function ($ft) { return $ft !== 'sum'; });
			foreach ($keys as $key) {
				$aa[$key] = ($aa[$key] ?? 0) + ($cc->{$key} ?? 0);
			}
			return $aa;
		}, []);
		$total_row['total'] = true;
		$rows = array_merge([(object) $total_row], $rows);

		$data = [
			'one_sheet_count' => -1,
			'exp_dlv_date' => $baebun_date,
			'rel_order' => $rel,
			'groupby' => $ord_field,
			'group_key' => $group_key,
			'headers' => $headers,
			'list' => $rows
		];

		$view_url = Config::get('shop.store.view') . '/sale/sal29_excel';
		$keys = [ 'list_key' => 'list', 'one_sheet_count' => $data['one_sheet_count'], 'cell_width' => 8, 'cell_height' => 25, 'freeze_row' => 'A13', 'sheet_name' => '배분현황' ];

		return Excel::download(new ExcelViewExport($view_url, $data, [], null, $keys), '배분현황_' . $baebun_date . '_' . $rel . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
}
