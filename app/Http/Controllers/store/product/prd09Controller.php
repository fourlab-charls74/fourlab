<?php

namespace App\Http\Controllers\store\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class prd09Controller extends Controller
{

	public function index(Request $request)
	{
		$prd_cd_p	= $request->input('prd_cd_p', '');
		$sdate		= $request->input('date', date('Y-m-d'));
		if($sdate == '') $sdate = date('Y-m-d');
		$color		= $request->input('color', '');
		$size		= $request->input('size', '');


		$values = [
			'prd_cd_p' => $prd_cd_p,
			'sdate' => $sdate,
			'color' => $color ?? '',
			'size' => $size ?? '',
			// 'prd' => $rows[0] ?? '',
			'store_types' => SLib::getCodes("STORE_TYPE"), // 매장구분
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
		];

		return view(Config::get('shop.store.view') . '/product/prd09', $values);
	}


	/** search: 옵션별 재고현황 검색 */
	public function search(Request $request)
	{
		$sdate			= $request->input('sdate', date('Y-m-d'));
		$next_edate		= date("Ymd", strtotime("+1 day", strtotime($sdate)));
		$now_date		= date("Ymd");
		$prd_cd_p		= $request->input('prd_cd_p', '');
		$o_prd_cd_p		= $prd_cd_p;
		$color			= $request->input('color', '');
		$store_channel	= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");
		$store_no		= $request->input("store_no", "");

		if ($color != '') $prd_cd_p .= $color;

		$values = [];

		if ($prd_cd_p != '') {

			// get sizes
			$sql = "
				select
				    	s.size_cd
				from size s
				inner join product_code pc on pc.size = s.size_cd and pc.size_kind = s.size_kind_cd
				where s.size_cd = pc.size and use_yn = 'Y' and pc.prd_cd like '$prd_cd_p%'
				group by s.size_cd
				order by s.size_seq asc
			";
			$sizes = array_map(function($row) {return $row->size_cd;}, DB::select($sql));

			// get goods info
			$cfg_img_size_real = "a_500";
			$cfg_img_size_list = "a_500";

			$sql = "
				select
					pc.prd_cd_p
					, pc.prd_cd
					, pc.goods_no
					-- , g.goods_nm
					-- , g.goods_nm_eng
					-- , g.style_no
					, p.prd_nm as goods_nm
				    , p.prd_nm_eng as goods_nm_eng
				    , p.style_no
				    , format(p.tag_price, 0) as tag_price
				    , format(p.price, 0) as price
					, g.com_id
					, g.com_nm
					, g.brand
					, b.brand_nm
					, g.opt_kind_cd
					, o.opt_kind_nm
					, ifnull(g.style_no, (select style_no from product p where p.prd_cd = pc.prd_cd)) as style_no
					, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
						select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
						from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
					)) as img
				from product_code pc
				inner join product p on p.prd_cd = pc.prd_cd
					left outer join goods g on g.goods_no = pc.goods_no
					left outer join brand b on b.brand = g.brand
					left outer join opt o on g.opt_kind_cd = o.opt_kind_cd
				where pc.prd_cd like '$o_prd_cd_p%'
				group by prd_cd_p
				-- having prd_cd_p = :prd_cd_p
			";
			$prd = DB::selectOne($sql);
			
			if (!isset($prd) || $prd->goods_no == '0') {
				$sql = "
					select
						p.prd_cd, p.prd_nm as goods_nm, p.prd_nm_eng as goods_nm_eng, p.style_no, format(p.tag_price) as tag_price, format(p.price, 0) as price, p.type, p.com_id, c.com_nm
						, p.match_yn, p.use_yn, pc.brand, b.brand_nm
						, pc.prd_cd_p as prd_cd_p
					from product p
						inner join product_code pc on pc.prd_cd = p.prd_cd
						left outer join company c on c.com_id = p.com_id
						left outer join brand b on b.br_cd = pc.brand
					group by prd_cd_p
					having prd_cd_p = :prd_cd_p
				";
				$prd = DB::selectOne($sql, ['prd_cd_p' => $o_prd_cd_p]);
			}

			// get store stock
			$where = "";
			if ($store_channel != '') $where .= "and store_channel ='" . Lib::quote($store_channel). "'";
			if ($store_channel_kind ?? '' != '') $where .= "and store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

			$case_sql = "";
			$case_sum_sql = "";
			foreach ($sizes as $size) {
				$case_sql .= "
					, if(pc.size = '$size', (ps.qty
						- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
						- ifnull(w.qty, 0)
					), 0) as '" . str_replace('.', '', $size) . "_qty'
					, if(pc.size = '$size', (ps.wqty
						- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
						- ifnull(w.qty, 0)
					), 0) as '" . str_replace('.', '', $size) . "_wqty'
				";
				$case_sum_sql .= "
					, sum(a.`" . str_replace('.', '', $size) . "_qty`) as '" . str_replace('.', '', $size) . "_qty'
					, sum(a.`" . str_replace('.', '', $size) . "_wqty`) as '" . str_replace('.', '', $size) . "_wqty'
				";
			}

			$store_where1	= "";
			$store_where2	= "";
			if( $store_no != "" ){
				
				$store_where1	.= " and ( 1<>1";
				$store_where2	.= " and ( 1<>1";
				foreach($store_no as $store_cd) {
					$store_where1 .= " or location_cd = '" . Lib::quote($store_cd) . "' ";
					$store_where2 .= " or ps.store_cd = '" . Lib::quote($store_cd) . "' ";
				}
				$store_where1	.= ")";
				$store_where2	.= ")";
			}
			
			$sql = "
				select a.store_cd, a.store_nm, a.prd_cd, a.color, c.code_val as color_nm
					$case_sum_sql
					, sum(a.qty) as qty
					, sum(a.wqty) as wqty
				from (
					select pc.color, ps.store_cd, s.store_nm, ps.prd_cd
						$case_sql
						, (ps.qty
							- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
							- ifnull(w.qty, 0)
						) as qty
						, (ps.wqty
							- sum(if(hst.type in (1, 11, 14, 15), ifnull(hst.qty, 0), 0))
							- ifnull(w.qty, 0)
						) as wqty
					from product_stock_store ps
						inner join product_code pc on pc.prd_cd = ps.prd_cd
						inner join store s on s.store_cd = ps.store_cd
						left outer join (
							select prd_cd, location_cd, type, qty
							from product_stock_hst
							where stock_state_date >= '$next_edate' and stock_state_date <= '$now_date' and location_type = 'STORE' $store_where1
						) hst on hst.prd_cd = ps.prd_cd and hst.location_cd = ps.store_cd
						left outer join (
							select prd_cd, store_cd, sum(qty * if(ord_state = 30, -1, 1)) as qty
							from order_opt_wonga
							where ord_state_date >= '$next_edate' and ord_state_date <= '$now_date' and ord_state in (30,60,61)
							group by prd_cd, store_cd
						) w on w.prd_cd = ps.prd_cd and w.store_cd = ps.store_cd
						left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = pc.color
					where ps.prd_cd like '$prd_cd_p%' $where $store_where2
					group by ps.store_cd, pc.prd_cd
					order by pc.color, s.store_nm
				) a
					left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = a.color
				group by a.store_cd, a.color
				order by sum(a.qty) desc
			";

			$store_rows = DB::select($sql);

			$case_sql = "";
			$case_sum_sql = "";
			foreach ($sizes as $size) {
				$case_sql .= "
					, if(pc.size = '$size', (ps.qty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))), 0) as '" . str_replace('.', '', $size) . "_qty'
					, if(pc.size = '$size', (ps.wqty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))), 0) as '" . str_replace('.', '', $size) . "_wqty'
				";
				$case_sum_sql .= "
					, sum(a.`" . str_replace('.', '', $size) . "_qty`) as '" . str_replace('.', '', $size) . "_qty'
					, sum(a.`" . str_replace('.', '', $size) . "_wqty`) as '" . str_replace('.', '', $size) . "_wqty'
				";
			}

			$sql = "
				select a.storage_cd, a.storage_nm, a.prd_cd, a.color, c.code_val as color_nm
					$case_sum_sql
					, sum(a.qty) as qty
					, sum(a.wqty) as wqty
				from (
					select pc.color, ps.storage_cd, s.storage_nm, ps.prd_cd
						$case_sql
						, (ps.qty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))) as qty
						, (ps.wqty - sum(if(hst.type in (1, 9, 11, 16, 17), ifnull(hst.qty, 0), 0))) as wqty
					from product_stock_storage ps
						inner join product_code pc on pc.prd_cd = ps.prd_cd
						inner join storage s on s.storage_cd = ps.storage_cd and s.use_yn = 'Y'
						left outer join (
							select prd_cd, location_cd, type, qty
							from product_stock_hst
							where stock_state_date >= '$next_edate' and stock_state_date <= '$now_date' and location_type = 'STORAGE'
						) hst on hst.prd_cd = ps.prd_cd and hst.location_cd = ps.storage_cd
						left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = pc.color
					where ps.prd_cd like '$prd_cd_p%' and ps.qty != 0 and ps.wqty != 0
					group by ps.storage_cd, pc.prd_cd
					order by pc.color, s.storage_nm
				) a
					left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = a.color
				group by a.storage_cd, a.color
				order by sum(a.qty) desc
			";

			$storage_rows = DB::select($sql);

			$values = [
				'sizes' => $sizes,
				'prd' => $prd,
				'stores' => $store_rows,
				'storages' => $storage_rows,
			];
		}

		return response()->json([ 'data' => $values ], 200);
	}
}
