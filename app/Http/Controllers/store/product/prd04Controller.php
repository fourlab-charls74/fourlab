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

class prd04Controller extends Controller
{

	public function index() 
	{

		$values = [
            'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
		];

		return view( Config::get('shop.store.view') . '/product/prd04',$values);
	}

	public function search(Request $request){
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$prd_cd		= $request->input("prd_cd", "");
		$goods_no	= $request->input("goods_no", "");
		$style_no	= $request->input("style_no");
		$goods_nm	= $request->input("goods_nm");
		$store_type	= $request->input("store_type", "");
		$store_no	= $request->input("store_no", "");
		$ext_store_qty	= $request->input("ext_store_qty", "");

		$ord		= $request->input('ord','desc');
		$ord_field	= $request->input('ord_field','g.goods_no');
		$orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where		= "";
		$in_store_sql	= "";
		$store_qty_sql	= "(ps.qty - ps.wqty)";

		if( $prd_cd != "" ){
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or pc.prd_cd = '" . Lib::quote($cd) . "' ";
			}
			$where .= ")";
		}

		$goods_no	= preg_replace("/\s/",",",$goods_no);
        $goods_no	= preg_replace("/\t/",",",$goods_no);
        $goods_no	= preg_replace("/\n/",",",$goods_no);
        $goods_no	= preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

		if( $style_no != "" )	$where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
		if( $goods_nm != "" ){
			$where .= " and ( g.goods_nm like '%" . Lib::quote($goods_nm) . "%' or p.prd_nm like '%" . Lib::quote($goods_nm) . "%' ) ";
		}
		if( $store_no != "" ){
			$in_store_sql	= " inner join product_stock_store pss on pc.prd_cd = pss.prd_cd ";

			$where	.= " and (1!=1";
			foreach($store_no as $store_cd) {
				$where .= " or pss.store_cd = '" . Lib::quote($store_cd) . "' ";
			}
			$where	.= ")";

			$store_qty_sql	= "pss.qty";
		}

		if( $store_no == "" && $store_type != "" ){
			$in_store_sql	= " inner join product_stock_store pss on pc.prd_cd = pss.prd_cd ";

			$sql	= " select store_cd from store where store_type = :store_type and use_yn = 'Y' ";
			$result = DB::select($sql,['store_type' => $store_type]);

			$where	.= " and (1!=1";
			foreach($result as $row){
				$where .= " or pss.store_cd = '" . Lib::quote($row->store_cd) . "' ";
			}
			$where	.= ")";

			$store_qty_sql	= "pss.qty";
		}

		if( $ext_store_qty == "Y" ){
			if( $store_no == "" )	$where .= " and (ps.qty - ps.wqty) > 0 ";
			else					$where .= " and pss.qty > 0 ";
		}


		$page_size	= $limit;
		$startno	= ($page - 1) * $page_size;
		$limit		= " limit $startno, $page_size ";

		$total		= 0;
		$page_cnt	= 0;

		if( $page == 1 ){
			$query	= /** @lang text */
			"
				select 
					count(*) as total
				from product_code pc
				inner join product_stock ps on pc.prd_cd = ps.prd_cd
				$in_store_sql
				left outer join product p on p.prd_cd = pc.prd_cd
				left outer join goods g on pc.goods_no = g.goods_no
				where 1=1 
					$where
			";
			$row	= DB::select($query);
			$total	= $row[0]->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$goods_img_url		= '';
		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$query	= /** @lang text */
		"
			select 
				pc.prd_cd
				, '' as prd_cd_p
				, if(pc.goods_no = 0, '', ps.goods_no) as goods_no
				, brand.brand_nm, g.style_no
				, '' as img_view
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				  )) as img
				, if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
				, pc.color, pc.size, pc.goods_opt
				, ps.wqty
				, $store_qty_sql as sqty
				, if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
				, if(pc.goods_no = 0, p.price, g.price) as price
				, if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
			from product_code pc
			inner join product_stock ps on pc.prd_cd = ps.prd_cd
			$in_store_sql
			left outer join product p on p.prd_cd = pc.prd_cd
			left outer join goods g on pc.goods_no = g.goods_no
			left outer join brand brand on brand.brand = g.brand
			where 
				pc.type = 'N'
				$where
			$orderby
			$limit
		";
		$pdo	= DB::connection()->getPdo();
		$stmt	= $pdo->prepare($query);
		$stmt->execute();
		$result	= [];
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row["img"] != ""){
				$row["img"] = sprintf("%s%s",config("shop.image_svr"), $row["img"]);
			}

			$chk_len	= strlen($row['prd_cd']) - strlen($row['color']) - strlen($row['size']);
			$row['prd_cd_p']	= substr($row['prd_cd'], 0, $chk_len);


			$result[] = $row;
		}

		return response()->json([
			"code"	=> 200,
			"head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
			),
			"body"	=> $result
		]);

	}

}