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

class prd03Controller extends Controller
{

	public function index() 
	{

		$mutable	= now();
		$sdate		= $mutable->sub(1, 'week')->format('Y-m-d');

		$event_cds	= [];
		//판매유형
		$sell_types	= [];
		$code_kinds	= [];

		$conf = new Conf();
		$domain		= $conf->getConfigValue("shop", "domain");

		$values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
			'event_cds'		=> $event_cds,
			'sell_types'	=> $sell_types,
			'code_kinds'	=> $code_kinds,
			'domain'		=> $domain,
			'style_no'		=> "",
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'types' => SLib::getCodes("PRD_MATERIAL_TYPE"),
			// 'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
		];

		return view( Config::get('shop.store.view') . '/product/prd03',$values);
	}

	public function search(Request $request)
	{
		$page	= $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input('limit', 100);

		$type = $request->input("type");
		$prd_nm	= $request->input("prd_nm");
		$prd_cd = $request->input("prd_cd");
		$com_id	= $request->input("com_cd");

		$limit = $request->input("limit", 100);
		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field', 'p.prd_cd');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where = "";
		if ($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd = '" . Lib::quote($cd) . "' ";
			}
			$where .= ")";
		}

		if ($type != "") $where .= " and pc.brand = '" . Lib::quote($type) . "'";
		if ($prd_nm != "") $where .= " and p.prd_nm like '%" . Lib::quote($prd_nm) . "%' ";
		if ($com_id != "") $where .= " and p.com_id = '" . Lib::quote($com_id) . "'";

		$page_size	= $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$total = 0;
		$page_cnt = 0;

		if ($page == 1) {
			$query	= /** @lang text */
				"
				select count(*) as total from product p 
					inner join product_code pc on p.prd_cd = pc.prd_cd
					inner join product_image i on p.prd_cd = i.prd_cd
					inner join company cp on p.com_id = cp.com_id
				where 1=1 
					$where
			";
			$row	= DB::select($query);
			$total	= $row[0]->total;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		$query = /** @lang text */
		"
			select
				c.code_val as type_nm,
				c2.code_val as opt,
				pc.img_url as img,
				p.prd_cd as prd_cd,
				c7.code_val as color,
				c8.code_val as size,
				p.prd_nm as prd_nm,
				pc.seq as seq,
				c3.code_val as year,
				c4.code_val as season,
				c5.code_val as gender,
				c6.code_val as item,
				cp.com_nm as sup_com,
				c9.code_val as unit,
				i.rt as rt,
				i.ut as ut				
			from product p
				inner join product_code pc on p.prd_cd = pc.prd_cd
				inner join product_image i on p.prd_cd = i.prd_cd
				inner join company cp on p.com_id = cp.com_id
				left outer join code c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
				left outer join code c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
				left outer join code c3 on c3.code_kind_cd = 'PRD_CD_YEAR' and c3.code_id = pc.year
				left outer join code c4 on c4.code_kind_cd = 'PRD_CD_SEASON' and c4.code_id = pc.season
				left outer join code c5 on c5.code_kind_cd = 'PRD_CD_GENDER' and c5.code_id = pc.gender
				left outer join code c6 on c6.code_kind_cd = 'PRD_CD_ITEM' and c6.code_id = pc.item
				left outer join code c7 on c7.code_kind_cd = 'PRD_CD_COLOR' and c7.code_id = pc.color
				left outer join code c8 on c8.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c8.code_id = pc.size
				left outer join code c9 on c9.code_kind_cd = 'PRD_CD_UNIT' and c9.code_id = p.unit
			where 1 = 1
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
			// // 이미지 경로 적용
			// if($row["img"] != ""){
			// 	$row["img"] = sprintf("%s%s",config("shop.image_svr"), $row["img"]);
			// }
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

	public function showCreate(Request $request)
	{
		$sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '1')
			->select('com_id', 'com_nm')->get()->all(); // 공급업체 리스트
		$values = [
			'types' => SLib::getCodes("PRD_MATERIAL_TYPE"),
			'brand' => SLib::getCodes("PRD_CD_BRAND"),
			'years'	=> SLib::getCodes("PRD_CD_YEAR"),
			'seasons' => SLib::getCodes("PRD_CD_SEASON"),
			'genders' => SLib::getCodes("PRD_CD_GENDER"),
			'items'	=> SLib::getCodes("PRD_CD_ITEM"),
			'opts' => SLib::getCodes("PRD_MATERIAL_OPT"),
			'colors' => SLib::getCodes("PRD_CD_COLOR"),
			'sizes'	=> SLib::getCodes("PRD_CD_SIZE_MATCH"),
			'years'	=> SLib::getCodes("PRD_CD_YEAR"),
			'sup_coms' => $sup_coms,
			'units' => SLib::getCodes("PRD_CD_UNIT"),
			'images' => []
		];
		return view( Config::get('shop.store.view') . '/product/prd03_create',$values);
	}

	public function create(Request $request){
		$admin_id = Auth('head')->user()->id;
        $data = $request->input("data");

		try {

			DB::beginTransaction();

			foreach($data as $row) {

				$brand = $row['brand'];
				$type = $row['type'];

				$year = $row['year'];
				$season	= $row['season'];
				$gender	= $row['gender'];
				$item = $row['item'];
				$seq = $row['seq'];
				$opt = $row['opt'];
				$color = $row['color'];
				$size = $row['size'];

				$sup_com = $row['sup_com'];
				$unit = $row['unit'];
				$year = $row['year'];
				
				$prd_nm	= $row['prd_nm'];
				$prd_cd	= $row['prd_cd'];

				$goods_no = "";
				$goods_opt = "";

				$sql = "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				if ($result->count == 0) {

					DB::table('product')->insert(
						[
							'prd_cd' => $prd_cd,
							'prd_nm' => $prd_nm,
							'type' => $type,
							'com_id' => $sup_com,
							'unit' => $unit,
							'rt' => now(),
							'ut' => now(),
							'admin_id' => $admin_id
						]
					);

					/**
					 * 원부자재 상품 이미지 저장 (단일 이미지)
					 */
					$base64_src = $row['image'];
					$save_path = "/images/prd03";
					$img_url = ULib::uploadBase64img($save_path, $base64_src);
		
					DB::table('product_code')->insert(
						[
							'prd_cd' => $prd_cd,
							'seq' => $seq,
							'img_url' => $img_url,
							'goods_no' => $goods_no,
							'goods_opt'	=> $goods_opt,
							'brand' => $brand,
							'year' => $year,
							'season' => $season,
							'gender' => $gender,
							'item' => $item,
							'opt' => $opt,
							'color' => $color,
							'size' => $size,
							'type' => $type,
							'rt' => now(),
							'ut' => now(),
							'admin_id'	=> $admin_id
						]
					);
					
					DB::table('product_image')->insert(
						[
							'prd_cd' => $prd_cd,
							'seq' => $seq,
							'img_url' => $img_url,
							'rt' => now(),
							'ut' => now(),
							'admin_id'	=> $admin_id
						]
					);

				} else {
					DB::rollback();
					return response()->json(["code" => -1, "prd_cd" => $prd_cd]);
				}
			}
			DB::commit();
			$code = 200;
		} catch (\Exception $e) {
			DB::rollback();
			$code = 500;
			// $msg = $e->getMessage();
		}

		return response()->json(["code" => $code]);
	}

	public function getSeq(Request $request) {
		$type = $request->input('type');
		$year = $request->input('year');
		$season = $request->input('season');
		$item = $request->input('item');
		$opt = $request->input('opt');

		$sql = " 
			select ifnull(max(seq),'00') as seq 
			from product_code 
			where 
				type = :type
				and year = :year
				and season = :season
				and item = :item
				and opt = :opt
		";
		$result	= DB::selectOne($sql, ['type' => $type, 'year' => $year, 'season' => $season, 'item' => $item, 'opt' => $opt]);
		$seq = $result->seq + 1;
		if (strlen($seq) == "1") $seq = "0" . $seq;

		return response()->json(['seq' => $seq , 'code' => 200]);
	}

	public function delImg(Request $request)
	{
		$store_cd = $request->input('data_img');
		$seq = $request->input('seq');
		
		try {
            DB::beginTransaction();

			$sel_sql = "
				select img_url
				from store_img
				where store_cd = '$store_cd' and seq = $seq

			";
			$row = DB::selectOne($sel_sql);

            $sql = "
                delete 
                from store_img
                where store_cd = '$store_cd' and seq = $seq
            ";

            DB::delete($sql);
			

			ULib::deleteFile($row->img_url);

            DB::commit();
            $code = '200';
            $msg = "";
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
	}
	
}
