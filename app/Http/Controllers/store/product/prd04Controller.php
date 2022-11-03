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

	public function batch(){
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd04_batch', $values);
	}

	public function upload(Request $request)
	{

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/prd04/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update(Request $request)
	{


		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

        //try 
		//{
        //    DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data		= (array)$datas[$i];
	
				$storage_cd	= trim($data['storage_cd']);
				$prd_cd_p	= trim($data['prd_cd_p']);
				$prd_cd		= trim($data['prd_cd']);
				$prd_nm		= trim($data['prd_nm']);
				$brand_nm	= trim($data['brand_nm']);
				$style_no	= trim($data['style_no']);
				$color		= trim($data['color']);
				$size		= trim($data['size']);
				$qty		= Lib::uncm(trim($data['qty']));
				$wonga		= Lib::uncm(trim($data['wonga']));
				$tag_price	= Lib::uncm(trim($data['tag_price']));
				$price		= Lib::uncm(trim($data['price']));

				//창고 존재 유무 검토
				$sql		= " select count(*) as tot from storage where storage_cd = :storage_cd ";
				$storage	= DB::selectOne($sql, ['storage_cd' => $storage_cd]);

				if( $storage->tot == 0 ){
					$error_code		= "501";
					$result_code	= "창고정보가 존재하지 않습니다. [" . $storage_cd . "]";

					break;
				}

				//브랜드 존재 유무 검토
				$brand	= "";
				$sql	= " select br_cd from brand where brand_nm = :brand_nm ";
				$result = DB::select($sql,['brand_nm' => $brand_nm]);
				foreach($result as $row){
					$brand	= $row->br_cd;
				}
				if( $brand == "" ){
					$error_code		= "502";
					$result_code	= "브랜드정보가 존재하지 않습니다. [" . $prd_cd . "]";

					break;
				}

				//상품코드 존재 유무
				$sql		= " select count(*) as tot from product_code where prd_cd = :prd_cd ";
				$obj_prd_code	= DB::selectOne($sql, ['prd_cd' => $prd_cd]);

				if( $obj_prd_code->tot == 0 ){

					$where	= ['prd_cd'	=> $prd_cd];

					//product 등록/수정
					$values	= [
						'prd_nm'	=> $prd_nm,
						'style_no'	=> $style_no,
						'tag_price'	=> $tag_price,
						'price'		=> $price,
						'wonga'		=> $wonga,
						'type'		=> 'N',			//일반상품
						'com_id'	=> 'alpen',		//
						'unit'		=> '',
						'match_yn'	=> 'N',
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $id
					];
					DB::table('product')->updateOrInsert($where, $values);

					$year	= substr(str_replace($brand, "", $prd_cd), 0 ,2);
					$season	= substr(str_replace($brand, "", $prd_cd), 2 ,1);
					$gender	= substr(str_replace($brand, "", $prd_cd), 3 ,1);
					$item	= substr(str_replace($brand, "", $prd_cd), 4 ,2);
					$seq	= substr(str_replace($brand, "", $prd_cd), 6 ,2);
					$opt	= substr(str_replace($brand, "", $prd_cd), 8 ,2);

					//product_code 등록/수정
					$values	= [
						'prd_cd'	=> $prd_cd,
						'goods_no'	=> '',
						'goods_opt'	=> '',
						'brand'		=> $brand,
						'year'		=> $year,
						'season'	=> $season,
						'gender'	=> $gender,
						'item'		=> $item,
						'opt'		=> $opt,
						'seq'		=> $seq,
						'color'		=> $color,
						'size'		=> $size,
						'type'		=> 'N',			//일반상품
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $id
					];
					DB::table('product_code')->Insert($values);

				}

				//재고정보 처리
				$where	= ['prd_cd'	=> $prd_cd];

				$values	= [
					//'goods_no'	=> '',
					'wonga'		=> $wonga,
					'qty_wonga'	=> $qty * $wonga,
					'in_qty'	=> $qty,
					'out_qty'	=> '0',
					'qty'		=> $qty,
					'wqty'		=> $qty,
					//'goods_opt'	=> '',
					'barcode'	=> $prd_cd,
					'use_yn'	=> 'Y',
					'rt'		=> now(),
					'ut'		=> now()
				];
				DB::table('product_stock')->updateOrInsert($where, $values);

				//창고재고 정보 처리
				$where	= ['prd_cd'	=> $prd_cd, 'storage_cd' => $storage_cd];

				$values	= [
					'goods_no'	=> '',
					'qty'		=> $qty,
					'wqty'		=> $qty,
					'goods_opt'	=> '',
					'use_yn'	=> 'Y',
					'rt'		=> now(),
					'ut'		=> now()
				];
				DB::table('product_stock_storage')->updateOrInsert($where, $values);

			}
	
		//	DB::commit();
        //}
		//catch(Exception $e) 
		//{
        //    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

	public function batch_wonga(){
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd04_batch_wonga', $values);
	}

	public function upload_wonga(Request $request)
	{

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/prd04/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update_wonga(Request $request)
	{


		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

        //try 
		//{
        //    DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data		= (array)$datas[$i];
	
				$prd_cd_p	= trim($data['prd_cd_p']);
				$style_no	= trim($data['style_no']);
				$prd_nm		= trim($data['prd_nm']);
				$color		= trim($data['color']);
				$size		= trim($data['size']);
				$tag_price	= Lib::uncm(trim($data['tag_price']));
				$price		= Lib::uncm(trim($data['price']));
				$wonga		= Lib::uncm(trim($data['wonga']));
				$store_qty	= Lib::uncm(trim($data['store_qty']));
				$storage_qty	= Lib::uncm(trim($data['storage_qty']));
				$tot_qty	= Lib::uncm(trim($data['tot_qty']));

				$prd_cd		= $prd_cd_p . $color . $size;

				if( $store_qty != 0 || $storage_qty != 0 ){
					$sql	= " select count(*) as cnt from product_code where prd_cd = :prd_cd";
					$product_code	= DB::selectOne($sql,['prd_cd' => $prd_cd]);

					if( $product_code->cnt == 0 ){
						// 상품코드 정보가 없을시

						$brand	= "";
						$sql	= " select br_cd, length(br_cd) as chk_len from brand where use_yn = 'Y' and br_cd <>'' order by length(br_cd) asc ";
						$result = DB::select($sql);
						foreach($result as $row){
							if( substr($prd_cd, 0, $row->chk_len) == $row->br_cd ){
								$brand	= $row->br_cd;
							}
						}

						if( $brand == "" ){
							$error_code		= "501";
							$result_code	= "브랜드정보가 존재하지 않습니다. [" . $prd_cd . "]";
		
							break;
						}

						$year	= substr(str_replace($brand, "", $prd_cd), 0 ,2);
						$season	= substr(str_replace($brand, "", $prd_cd), 2 ,1);
						$gender	= substr(str_replace($brand, "", $prd_cd), 3 ,1);
						$item	= substr(str_replace($brand, "", $prd_cd), 4 ,2);
						$seq	= substr(str_replace($brand, "", $prd_cd), 6 ,2);
						$opt	= substr(str_replace($brand, "", $prd_cd), 8 ,2);

						//product_code 등록/수정
						$values	= [
							'prd_cd'	=> $prd_cd,
							'goods_no'	=> '',
							'goods_opt'	=> '',
							'brand'		=> $brand,
							'year'		=> $year,
							'season'	=> $season,
							'gender'	=> $gender,
							'item'		=> $item,
							'opt'		=> $opt,
							'seq'		=> $seq,
							'color'		=> $color,
							'size'		=> $size,
							'type'		=> 'N',			//일반상품
							'rt'		=> now(),
							'ut'		=> now(),
							'admin_id'	=> $id
						];
						DB::table('product_code')->Insert($values);
		
					}

					//product 등록/수정
					$where	= ['prd_cd'	=> $prd_cd];
					$values	= [
						'prd_nm'	=> $prd_nm,
						'style_no'	=> $style_no,
						'tag_price'	=> $tag_price,
						'price'		=> $price,
						'wonga'		=> $wonga,
						'type'		=> 'N',			//일반상품
						'com_id'	=> 'alpen',		//
						'unit'		=> '',
						'match_yn'	=> 'N',
						'rt'		=> now(),
						'ut'		=> now(),
						'admin_id'	=> $id
					];
					DB::table('product')->updateOrInsert($where, $values);

					//재고정보 처리
					$values	= [
						//'goods_no'	=> '',
						'wonga'		=> $wonga,
						'qty_wonga'	=> '0',
						'in_qty'	=> '0',
						'out_qty'	=> '0',
						'qty'		=> '0',
						'wqty'		=> '0',
						//'goods_opt'	=> '',
						'barcode'	=> $prd_cd,
						'use_yn'	=> 'Y',
						'rt'		=> now(),
						'ut'		=> now()
					];
					DB::table('product_stock')->updateOrInsert($where, $values);

				}else{
					//재고정보 초기화
					$where	= ['prd_cd'	=> $prd_cd];

					$values	= [
						'wonga'		=> $wonga,
						'in_qty'	=> '0',
						'out_qty'	=> '0',
						'qty'		=> '0',
						'wqty'		=> '0',
						'ut'		=> now()
					];
					DB::table('product_stock')
						->where($where)
						->update($values);
				}

			}
	
		//	DB::commit();
        //}
		//catch(Exception $e) 
		//{
        //    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

	public function batch_store(){
		$values = [];

		return view( Config::get('shop.store.view') . '/product/prd04_batch_store', $values);
	}

	public function upload_store(Request $request)
	{

		if ( 0 < $_FILES['file']['error'] ) {
			echo json_encode(array(
				"code" => 500,
				"errmsg" => 'Error: ' . $_FILES['file']['error']
			));
		}
		else {
			//$file = sprintf("data/code02/%s", $_FILES['file']['name']);
			$file = sprintf("data/store/prd04/%s", $_FILES['file']['name']);
			move_uploaded_file($_FILES['file']['tmp_name'], $file);
			echo json_encode(array(
				"code" => 200,
				"file" => $file
			));
		}

	}

	public function update_store(Request $request)
	{


		$error_code		= "200";
		$result_code	= "";

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

        //try 
		//{
        //    DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data		= (array)$datas[$i];
	
				$store_cd	= trim($data['store_cd']);
				$prd_cd_p	= trim($data['prd_cd_p']);
				$prd_nm		= trim($data['prd_nm']);
				$style_no	= trim($data['style_no']);
				$color		= trim($data['color']);
				$size		= trim($data['size']);
				$qty		= Lib::uncm(trim($data['qty']));
				$tag_price	= Lib::uncm(trim($data['tag_price']));
				$price		= Lib::uncm(trim($data['price']));
				$qty_wonga	= 0;
				$wonga		= 0;
				$in_qty		= 0;
				$org_qty	= 0;

				$prd_cd		= $prd_cd_p . $color . $size;

				//매장 존재 유무 검토
				$sql		= " select count(*) as tot from store where store_cd = :store_cd ";
				$store	= DB::selectOne($sql, ['store_cd' => $store_cd]);

				if( $store->tot == 0 ){
					$error_code		= "501";
					$result_code	= "매장정보가 존재하지 않습니다. [" . $store_cd . "]";

					break;
				}

				//상품코드 존재 유무
				$sql		= " select wonga, qty_wonga, in_qty, qty from product_stock where prd_cd = :prd_cd ";
				$result = DB::select($sql, ['prd_cd' => $prd_cd]);

				foreach($result as $row){
					$qty_wonga	= $row->qty_wonga;
					$wonga		= $row->wonga;

					if( $qty_wonga > 0 )	$wonga = $qty_wonga / $row->qty;

					$in_qty		= $row->in_qty;
					$org_qty	= $row->qty;
				}
		
				if( $wonga == 0 ){
					$error_code		= "502";
					$result_code	= "상품정보 혹은 원가정보가 존재하지 않습니다. [" . $prd_cd . "]";

					break;
				}

				//재고정보 처리
				$where	= ['prd_cd'	=> $prd_cd];

				$values	= [
					'wonga'		=> $wonga,
					'qty_wonga'	=> $qty_wonga + $qty * $wonga,
					'in_qty'	=> $in_qty + $qty,
					'qty'		=> $org_qty + $qty,
					'ut'		=> now()
				];
				DB::table('product_stock')
					->where($where)
					->update($values);
				//DB::table('product_stock')->update($where, $values);

				//매장재고 정보 처리
				$where	= ['prd_cd'	=> $prd_cd, 'store_cd' => $store_cd];

				$values	= [
					'goods_no'	=> '',
					'qty'		=> $qty,
					'wqty'		=> $qty,
					'goods_opt'	=> '',
					'use_yn'	=> 'Y',
					'rt'		=> now(),
					'ut'		=> now()
				];
				DB::table('product_stock_store')->updateOrInsert($where, $values);

			}
	
		//	DB::commit();
        //}
		//catch(Exception $e) 
		//{
        //    DB::rollback();

		//	$result_code	= "500";
		//	$result_msg		= "데이터 등록/수정 오류";
		//}

		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}