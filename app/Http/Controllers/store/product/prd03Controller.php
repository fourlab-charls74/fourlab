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
			'store_types'	=> SLib::getCodes("STORE_TYPE"), // 매장구분
		];

		return view( Config::get('shop.store.view') . '/product/prd03',$values);
	}

	public function search(Request $request)
	{
		$page = $request->input('page', 1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit = $request->input('limit', 500);

		$type = $request->input("type");
		$prd_nm	= $request->input("prd_nm");
		$prd_cd = $request->input("prd_cd_sub");
		$com_id	= $request->input("com_cd");
		$com_nm	= $request->input("com_nm");
		$store_no	= $request->input("store_no", "");
		$ext_store_qty = $request->input("ext_store_qty", "");

		$limit = $request->input("limit", 100);
		$ord = $request->input('ord','desc');
		$ord_field = $request->input('ord_field', 'p.prd_cd');
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$where = "";
		$where2 = "";
		if ($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}
		if ($ext_store_qty == "Y") $where2 .= "and pss2.wqty > 0";
		if ($type != "") $where .= " and pc.brand = '" . Lib::quote($type) . "'";
		if ($prd_nm != "") $where .= " and p.prd_nm like '%" . Lib::quote($prd_nm) . "%' ";
		// if ($com_id != "") $where .= " and p.com_id = '" . Lib::quote($com_id) . "'";
		if ($com_nm != "") $where .= " and cp.com_nm like '%" . Lib::quote($com_nm) . "%' ";

		$in_store_sql	= "";
		if( $store_no != "" ){
			$in_store_sql	= " inner join product_stock_store pss3 on pc.prd_cd = pss3.prd_cd ";

			$where	.= " and (1!=1";
			foreach($store_no as $store_cd) {
				$where .= " or pss3.store_cd = '" . Lib::quote($store_cd) . "' ";
			}
			$where	.= ")";

			$store_qty_sql	= "pss.qty";
		}

		// $where3 = "";
		// if ($store_no != "") $where3 .= "and pss.store_cd = '$store_no'";

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
					$in_store_sql
				where p.use_yn = 'Y' and p.type <> 'N'
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
				i.img_url as img,
				p.prd_cd as prd_cd,
				c7.code_val as color,
				size.size_nm as size,
				p.prd_nm as prd_nm,
				p.tag_price as tag_price,
				p.price as price,
				p.wonga as wonga,
				ifnull(pss.wqty, 0) as stock_qty,
				ifnull(pss2.wqty, 0) as store_qty,
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
				left outer join product_image i on p.prd_cd = i.prd_cd
				left outer join product_stock_storage pss on p.prd_cd = pss.prd_cd
				left outer join product_stock_store pss2 on p.prd_cd = pss2.prd_cd
				inner join company cp on p.com_id = cp.com_id
				$in_store_sql
				left outer join code c on c.code_kind_cd = 'PRD_MATERIAL_TYPE' and c.code_id = pc.brand
				left outer join code c2 on c2.code_kind_cd = 'PRD_MATERIAL_OPT' and c2.code_id = pc.opt
				left outer join code c3 on c3.code_kind_cd = 'PRD_CD_YEAR' and c3.code_id = pc.year
				left outer join code c4 on c4.code_kind_cd = 'PRD_CD_SEASON' and c4.code_id = pc.season
				left outer join code c5 on c5.code_kind_cd = 'PRD_CD_GENDER' and c5.code_id = pc.gender
				left outer join code c6 on c6.code_kind_cd = 'PRD_CD_ITEM' and c6.code_id = pc.item
				left outer join code c7 on c7.code_kind_cd = 'PRD_CD_COLOR' and c7.code_id = pc.color
				-- left outer join code c8 on c8.code_kind_cd = 'PRD_CD_SIZE_MATCH' and c8.code_id = pc.size
				left outer join size size on size.size_cd = pc.size and size_kind_cd = 'PRD_CD_SIZE_UNISEX'
				left outer join code c9 on c9.code_kind_cd = 'PRD_CD_UNIT' and c9.code_id = p.unit
			where p.use_yn = 'Y' and p.type <> 'N' 
				$where
				$where2
			$orderby
			$limit
		";
		$pdo	= DB::connection()->getPdo();
		$stmt	= $pdo->prepare($query);
		$stmt->execute();
		$result	= [];
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
		$sup_coms = DB::table("company")->where('use_yn', '=', 'Y')->where('com_type', '=', '6')
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

				$brand	= $row['brand'];
				$type	= $row['type'];

				$season	= $row['season'];
				$gender	= $row['gender'];
				$item	= $row['item'];
				$seq	= $row['seq'];
				$opt	= $row['opt'];
				$color	= $row['color'];
				$size	= $row['size'];

				$tag_price = $row['tag_price'];
				$price	= $row['price'];
				$wonga	= $row['wonga'];

				$sup_com = $row['sup_com'];
				$unit	= $row['unit'];
				$year	= $row['year'];
				
				$prd_nm	= $row['prd_nm'];
				$prd_cd	= $row['prd_cd'];

				$prd_cd_p	= $brand . $year . $season . $gender . $item . $seq . $opt;

				$sql	= "select count(*) as count from product where prd_cd = :prd_cd";
				$result	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				if($result->count == 0) {

					DB::table('product')->insert([
						'prd_cd'	=> $prd_cd,
						'prd_nm'	=> $prd_nm,
						'type'		=> $type,
						'tag_price'	=> $tag_price,
						'price'		=> $price,
						'wonga'		=> $wonga,
						'com_id'	=> $sup_com,
						'unit'		=> $unit,
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $admin_id
					]);

					/**
					 * 원부자재 상품 이미지 저장 (단일 이미지)
					 */
					$wonboo_cd	= substr($prd_cd,0,2);

					$save_path = "";
					if($wonboo_cd == 'PR') {
						$save_path = "/images/s_goods/pr/";
					}else if($wonboo_cd == 'SM') {
						$save_path = "/images/s_goods/sm/";
					}

					$base64_src = $row['image'];
					
					$unique_img_name = $prd_cd . $seq;
					$img_name = strtolower($unique_img_name);
					$img_url = ULib::uploadBase64img($save_path, $base64_src, $img_name);
		
					DB::table('product_code')->insert([
						'prd_cd'	=> $prd_cd,
						'prd_cd_p'	=> $prd_cd_p,
						'seq'		=> $seq,
						'goods_no'	=> "",
						'brand'		=> $brand,
						'year'		=> $year,
						'season'	=> $season,
						'gender'	=> $gender,
						'item'		=> $item,
						'opt'		=> $opt,
						'color'		=> $color,
						'size'		=> $size,
						'type'		=> $type,
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $admin_id
					]);
					
					DB::table('product_image')->insert([
						'prd_cd' => $prd_cd,
						'seq' => $seq,
						'img_url' => $img_url,
						'rt' => now(),
						'ut' => now(),
						'admin_id'	=> $admin_id
					]);

					DB::table('product_stock')->insert([
						'prd_cd' => $prd_cd,
						'qty_wonga'	=> 0,
						'in_qty' => 0,
						'out_qty' => 0,
						'qty' => 0,
						'wqty' => 0,
						'barcode' => $prd_cd,
						'use_yn' => "Y",
						'rt' => now(),
						'ut' => now()
					]);

					DB::table('product_stock_storage')->insert([
						'prd_cd' => $prd_cd,
						'qty' => 0,
						'wqty' => 0,
						'storage_cd' => DB::raw("(select storage_cd from storage where default_yn = 'Y')"),
						'use_yn' => "Y",
						'rt' => now(),
						'ut' => now()
					]);

				} else {
					DB::rollback();
					return response()->json(["code" => -1, "prd_cd" => $prd_cd]);
				}
			}
			DB::commit();
			$code = 200;
		} catch (\Exception $e) {
			// dd($e);
			DB::rollback();
			$code = 500;
		}

		return response()->json(["code" => $code]);
	}

	public function showAndEdit($type, $product_code) {
		$sql = "
			select
				c.code_val as type_nm,
				c3.code_val as year,
				c4.code_val as season,
				c5.code_val as gender,
				c6.code_val as item,
				c2.code_val as opt,
				c7.code_val as color,
				c8.code_val as size,
				p.prd_nm as prd_nm,
				cp.com_nm as sup_com,
				p.price as price,
				p.wonga as wonga,
				c9.code_id as unit_id,
				i.img_url as img_url,
				p.prd_cd as prd_cd,
				p.ut as rt,
				p.ut as ut
			from product p
				inner join product_code pc on p.prd_cd = pc.prd_cd
				left outer join product_image i on p.prd_cd = i.prd_cd
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
			where p.prd_cd = :prd_cd
		";
		$values = (array)DB::selectOne($sql, ['prd_cd' => $product_code]);
		$values['units'] = SLib::getCodes("PRD_CD_UNIT");
		$values['method'] = $type;
		
		return view( Config::get('shop.store.view').'/product/prd03_edit' , $values);
	}

	public function edit(Request $request) {
		$admin_id = Auth('head')->user()->id;
        $data = $request->input();


		try {

			DB::beginTransaction();

			$prd_cd	= $data['prd_cd'];
			$prd_nm	= $data['prd_nm'];
			$price = $data['price'];
			$wonga = $data['wonga'];
			
			$unit = $data['unit'];
			$seq = $data['seq'];

			$img = $data['img'];

			/**
			 * 원부자재 상품 이미지 수정
			 */

			$i_prd_cd = substr($prd_cd,0,2);

			$save_path = "";
			if ($i_prd_cd == 'PR') {
				$save_path = "/images/s_goods/pr/";
			} else if ($i_prd_cd == 'SM') {
				$save_path = "/images/s_goods/sm/";
			}

			$base64_src = $data['image'];
			
			$unique_img_name = $prd_cd . $seq;
			$img_name = strtolower($unique_img_name);
			$img_url = "";

			/**
			 * 이미지 수정시 기존에 저장된 이미지가 있는지 확인
			 */
			$result = DB::table('product_image')->where([['prd_cd', '=', $prd_cd], ['seq', '=', $seq]])->first();

			if ($result == null) {
				/**
				 * 기존에 저장된 이미지가 없는 경우 이미지 관련 insert 처리
				 */
				if ($img == "img_not_null") {
					$img_url = ULib::uploadBase64img($save_path, $base64_src, $img_name);

					DB::table('product_image')->insert([
							'prd_cd' => $prd_cd,
							'seq' => $seq,
							'img_url' => $img_url,
							'rt' => now(),
							'ut' => now(),
							'admin_id'	=> $admin_id
						]);
				} else {
					DB::table('product_image')->insert([
						'prd_cd' => $prd_cd,
						'seq' => $seq,
						'rt' => now(),
						'ut' => now(),
						'admin_id'	=> $admin_id
					]);
				}

			} else {
				/**
				 * 기존에 저장된 이미지가 있는 경우 이미지 관련 update 처리
				 */
				$idx = $result->idx;
				$img_url = $result->img_url;

				if ($base64_src != "") { // 이미지를 수정한 경우에만 기존 이미지 삭제후 업데이트

					ULib::deleteFile($img_url);
					$img_url = ULib::uploadBase64img($save_path, $base64_src, $unique_img_name);

					DB::table('product_image')->where('idx', '=', $idx)->update([
						'img_url' => $img_url,
						'ut' => now(),
						'admin_id'	=> $admin_id
					]);

					DB::table('product_code')->where('prd_cd', '=', $prd_cd)->update([
						'ut' => now(),
						'admin_id'	=> $admin_id
					]);

				}
			}

			DB::table('product')->where('prd_cd', '=', $prd_cd)->update([
				'prd_nm' => $prd_nm,
				'price' => $price,
				'wonga' => $wonga,
				'unit' => $unit,
				'ut' => now(),
				'admin_id' => $admin_id
			]);

			$code = 200;
			DB::commit();
			
		} catch (\Exception $e) {
			$code = 500;
			DB::rollback();
		}

		return response()->json(["code" => $code]);
	}

	public function delSproduct(Request $request) {
		$admin_id = Auth('head')->user()->id;
		$product_code = $request->input('prd_cd');

		try {
			DB::beginTransaction();
			DB::table('product')->where('prd_cd', '=', $product_code)
			->update([
				'use_yn' => 'N',
				'ut' => now(),
				'admin_id' => $admin_id
			]);
            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
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
				brand = :type
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
		$admin_id = Auth('head')->user()->id;
		$prd_cd = $request->input('prd_cd');
		$seq = $request->input('seq');

		try {
			DB::beginTransaction();

			DB::table('product')->where('prd_cd', '=', $prd_cd)->update([
				'ut' => now(),
				'admin_id' => $admin_id
			]);

			DB::table('product_code')->where('prd_cd', '=', $prd_cd)->update([
				'ut' => now(),
				'admin_id'	=> $admin_id
			]);

			$result = DB::table('product_image')->where([['prd_cd', '=', $prd_cd], ['seq', '=', $seq]])->first();
			$idx = $result->idx;
			$img_url = $result->img_url;

			ULib::deleteFile($img_url);
			
			DB::table('product_image')->where('idx', '=', $idx)->delete();

            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code" => $code]);
	}

	//성별 변경 시 해당 성별의 사이즈 값 출력
	public function change_gender(Request $request)
	{
		$gender = $request->input('gender');

		$gen = '';
		if($gender == 'M') {
			$gen = 'MEN';
		} else if ($gender == 'U') {
			$gen = 'UNISEX';
		} else if ($gender == 'W') {
			$gen = 'WOMEN';
		} else if ($gender == 'K') {
			$gen = 'KIDS';
		}

		try {
			DB::beginTransaction();

			$sql = "
				select
					code_id, code_val
				from code
				where code_kind_cd = 'PRD_CD_SIZE_$gen'
				order by field(code_id, '10', '10.5', '99') desc,
				field(code_id, 'XL', 'XS', 'L2', 'X2') asc,
				code_id desc
			";

			$result = DB::select($sql);

            DB::commit();
            $code = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $code = 500;
        }

        return response()->json(["code" => $code, "result" => $result]);
	}
	
}
