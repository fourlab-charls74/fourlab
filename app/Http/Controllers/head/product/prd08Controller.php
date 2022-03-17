<?php

namespace App\Http\Controllers\head\product;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class prd08Controller extends Controller
{
	//
	public function index()
	{
		$conf = new Conf();
		$sex_types  = SLib::getCodes("G_SEX_TYPE");
		$goods_stats  = SLib::getCodes("G_GOODS_STAT");

		$query = "select
			opt_kind_cd id,
			case when 'kor' = 'kor' then
				concat('(',ifnull(opt_kind_cd, ''),') ',ifnull(opt_kind_nm, ''))
			else
				opt_kind_cd
			end as val
		from opt where opt_id = 'K' and use_yn = 'Y'
		order by  use_yn desc,opt_kind_nm ";
		$opt_kind_cd_items = DB::select($query);

		$values = [
			"opt_kind_cd_items"	=> $opt_kind_cd_items,
			"sex_types"	=> $sex_types,
			"goods_stats"	=> $goods_stats
		];

		return view(Config::get('shop.head.view') . '/product/prd08', $values);
	}

	public function search(Request $request)
	{
		$conf = new Conf();
		$cfg_site   = $conf->getConfig("shop", "sale_place", "");
		$cfg_img_size_list	= SLib::getCodesValue("G_IMG_SIZE", "list");
		$cfg_img_size_real	= SLib::getCodesValue("G_IMG_SIZE", "real");

		$page = $request->input("page", 1);
		if ($page < 1 or $page == "") $page = 1;

		$date_type 		= $request->input("date_type");

		$opt_kind_cd	= $request->input("opt_kind_cd");
		$brand_cd 		= $request->input("brand_cd");
		$brand_nm 		= $request->input("brand_nm");
		$com_type 		= $request->input("com_type");
		$com_id 		= $request->input("com_id");
		$com_nm 		= $request->input("com_nm");
		$goods_stat		= $request->input("goods_stat");
		$rep_cat_cd		= $request->input("rep_cat_cd");
		$goods_nm 		= $request->input("goods_nm");
		$limit			= $request->input("limit", 100);

		if ($date_type != "") {
			if ($date_type == "1d") {
				$date = "1";
				$date_type_where = "_" . $date_type;
			} else if ($date_type == "1w") {
				$date = "7";
				$date_type_where = "_" . $date_type;
			} else if ($date_type == "2w") {
				$date = "14";
				$date_type_where = "_" . $date_type;
			} else if ($date_type == "1m") {
				$date = "30";
				$date_type_where = "_" . $date_type;
			} else if ($date_type == "3m") {
				$date = "90";
				$date_type_where = "_" . $date_type;
			} else if ($date_type == "1y") {
				$date = "365";
				$date_type_where = "_" . $date_type;
			}
		}

		$where = "";
		$page_size = $limit;

		if ($opt_kind_cd != "")	$where .= " and a.opt_kind_cd = '$opt_kind_cd'";
		if ($brand_cd != "")		$where .= " and a.brand = '$brand_cd'";
		if ($com_type != "")		$where .= " and a.com_type = '$com_type'";
		if ($goods_stat != "")		$where .= " and a.sale_stat_cl = '$goods_stat'";
		if ($rep_cat_cd != "")		$where .= " and a.rep_cat_cd = '$rep_cat_cd'";
		if ($goods_nm != "")		$where .= " and a.goods_nm like '$goods_nm%'";
		if ($com_id != "")			$where .= " and a.com_id = '$com_id'";

		$sql = "
			select
				count(*) total
			from  goods a
			inner join goods_site s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and s.site = '$cfg_site'
			inner join goods_rank b on b.goods_no = a.goods_no and b.goods_sub = a.goods_sub
			left outer join goods_stat c on c.goods_no = a.goods_no and c.goods_sub = a.goods_sub
			left outer join goods_rank_admin_point e on e.goods_no = a.goods_no and e.goods_sub = a.goods_sub
			left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and a.sale_stat_cl = stat.code_id
			where b.type = '$date_type' $where
			order by b.rank
		";
		
		$row = DB::selectOne($sql);
		$data_cnt = $row->total;

		$page_cnt = (int)(($data_cnt - 1) / $page_size) + 1;
		if ($page == 1) {
			$startno = ($page - 1) * $page_size;
		} else {
			$startno = ($page - 1) * $page_size;
		}

		if ($limit == -1) {
			$where_limit = "";
		} else {
			$where_limit = " limit $startno, $page_size ";
		}

		$clm = sprintf(", ifnull(c.clm%s,0) as clm_sum, ifnull(round(c.clm%s/%s),0) as clm_avg", $date_type_where, $date_type_where, $date);
		$review = sprintf(", concat(ifnull(c.review%s,0),' (',ifnull(c.grade%s,0),')') as review, concat(ifnull(round(c.review%s/%s),0),' (',ifnull(round(c.grade%s/%s),0),')') as review_avg", $date_type_where, $date_type_where, $date_type_where, $date, $date_type_where, $date);
		$qa = sprintf(", ifnull(c.qa%s,0) as qa, ifnull(round(c.qa%s/%s),0) as qa_avg", $date_type_where, $date_type_where, $date);

		$field = sprintf("%s%s%s", $clm, $review, $qa);

		$sql = "
			select
				'' as blank
				, '' as img_blank
				, a.goods_nm
				, stat.code_val
				, b.rank
				, b.variation
				, b.sale_point
				, e.admin_point
				, (b.sale_point + ifnull(e.admin_point, 0)) as pre_point
				, b.point
				$field
				, if(a.special_yn <> 'Y', replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list'), ( 
					select replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list') as img 
					from goods g where g.goods_no = b.goods_no and a.goods_sub = 0
				 )) as img
				 , b.goods_no
				, b.goods_sub
			from  goods a
                                inner join goods_site s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and s.site = '$cfg_site'
				inner join goods_rank b on b.goods_no = a.goods_no and b.goods_sub = a.goods_sub
				left outer join goods_stat c on c.goods_no = a.goods_no and c.goods_sub = a.goods_sub
				left outer join goods_rank_admin_point e on e.goods_no = a.goods_no and e.goods_sub = a.goods_sub
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and a.sale_stat_cl = stat.code_id
			where b.type = '$date_type' $where
			group by a.goods_no
			order by b.rank
			$where_limit
		";


		$result = DB::select($sql);
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result)
			),
			"body" => $result
		]);
	}

	public function Command(Request $request)
	{

		$cmd = $request->input("cmd");

		$return_code = 0;

		if ($cmd == "save_point") {

			$datas = $request->input("data");

			for ($i = 0; $i < count($datas); $i++) {
				list($goods_no, $goods_sub, $point)	= $datas[$i];

				$sql = "
					select count(*) as cnt from goods_rank_admin_point 
					where goods_no = '$goods_no' and goods_sub = '$goods_sub'
				";
				$row = DB::selectOne($sql);
				$cnt = $row->cnt;

				if ($cnt > 0) {
					$sql = "
						update goods_rank_admin_point set
							admin_point = '$point',
							ut = now()
						where goods_no = '$goods_no' and goods_sub = '$goods_sub'	
					";

					$update_items = [
						"admin_point" => $point,
						"ut" => "now()"
					];

					try {
						DB::update($sql);
						$return_code = 1;
					} catch (Exception $e) {
						$return_code = 0;
					}
				} else {
					$sql = "
						insert into goods_rank_admin_point (
							goods_no, goods_sub, admin_point, rt, ut
						) values (
							'$goods_no', '$goods_sub', '$point', now(), now()
						);
					";

					try {
						DB::insert($sql);
						$return_code = 1;
					} catch (Exception $e) {
						$return_code = 0;
					}
				}
			}
		}


		return response()->json([
			"code" => 200,
			"return_code" => $return_code
		]);
	}
}
