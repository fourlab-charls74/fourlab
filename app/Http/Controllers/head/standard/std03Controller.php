<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class std03Controller extends Controller
{
    public function index() {
		$id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        $values = [
			"admin_id" => $id,
			"admin_nm" => $name
		];
        return view( Config::get('shop.head.view') . '/standard/std03',$values);
    }

    public function search(Request $request){
        $cmd = $request->input("cmd");
        $brand = $request->input("brand");
		$brand_type = $request->input("brand_type");
		$best_yn = $request->input("best_yn");
		$use_yn = $request->input("use_yn");
		$memo = $request->input("memo");

		$where = "";

		//echo $brand_type;
		if($brand) $where .= "and (a.brand like '%$brand%' or a.brand_nm like '%$brand%' or a.brand_nm_eng like '%$brand%' )";
		if($brand_type) $where .= "and brand_type = '$brand_type'";
		if($use_yn) $where .= "and use_yn = '$use_yn'";
		if($best_yn) $where .= "and best_yn = '$best_yn'";
		if($memo) $where .= "and memo like '%$memo%'";

        //$choice = _t("선택");
        $choice = '';

		if($cmd == "popup_brand"){
			$query = "
				select
					brand_type ,best_yn,brand, brand_nm, brand_nm_eng, use_yn, comment, '$choice' as choice
				from brand a
				where 1=1 $where
				order by brand_type,brand_nm
			";
		} else {
			$query = "
				select
					a.brand_type ,a.best_yn,a.brand, a.brand_nm, ifnull(b.goods_cnt ,0) as goods_cnt, a.use_yn,
					a.regi_date, a.ut, a.overview, a.keyword
				from brand a
					left outer join(
						select brand, count(*) as goods_cnt
						from goods
						group by brand
					) b on a.brand = b.brand
				where 1=1
					$where
				order by brand_type,brand_nm
			";
        }

        $result = DB::select($query);
        //echo count($result);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1
            ),
            "body" => $result
        ]);
	}
	public function Command(Request $request){

		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$logo_img_url	= "";
		$brand_result	= 500;
		$goods_result	= 500;

		$cmd 			= $request->input("cmd");
		$brand			= $request->input("brand");
		$brand_nm 		= $request->input("brand_nm");
		$brand_nm_eng 	= $request->input("brand_nm_eng");
		$memo			= $request->input("memo");
		$overview 		= $request->input("overview");
		$keyword 		= $request->input("keyword");
		$brand_type 	= $request->input("brand_type");
		$use_yn 		= $request->input("use_yn");
		$best_yn 		= $request->input("best_yn");
		$brand_contents	= $request->input("brand_contents");
		$chg_brand		= $request->input("chg_brand");
		$brand_logo		= $request->input("brand_logo");


		$base_path = "/images/brand_logo";
		$save_path = sprintf("%s/%s", $base_path, $brand);
		$logo_img_url = $brand_logo;

		$brand_file = $request->file("brand_file");

		if( $request->input("c") == "edit" && $cmd == "" ){
			$cmd = "editcmd";
		}

		//return;
		/* 이미지를 저장할 경로 폴더가 없다면 생성 */

		if(!Storage::disk('public')->exists($base_path)){
			//Storage::disk('public')->makeDirectory($save_path);
			Storage::disk('public')->makeDirectory($base_path);
		}

		if($brand_file != null &&  $brand_file != ""){
			$file_ori_name = $brand_file->getClientOriginalName();

			$ext = substr($file_ori_name, strrpos($file_ori_name, '.') + 1);

			$file_name = sprintf("%s_%s.".$ext , $brand, $brand_nm_eng);
			//$save_file = sprintf("%s/%s", $save_path, $file_name);
			$save_file = sprintf("%s/%s", $base_path, $file_name);
			//$logo_img_url = sprintf("%s/%s", $brand, $file_name);
			$logo_img_url = $save_file;

			//$brand_file_info =  file_get_contents($brand_file);
			Storage::disk('public')->putFileAs($base_path, $brand_file, $file_name);
		}

		if($cmd == "addcmd"){
			//이미 등록된 브랜드가 있는지 검사
			$query1 = "
				select count(*) as cnt
				from brand
				where brand = '$brand'
			";

			$row = DB::select($query1);
            $cnt = $row[0]->cnt;
			if($cnt == 0){		// 등록된 코드가 없을때만 insert

				$insert_brand = "
					insert into brand(
						brand, brand_nm, brand_nm_eng, overview,memo, keyword, best_yn,use_yn, brand_contents, brand_logo, admin_id, admin_nm, regi_date, ut
					)values(
						'$brand', '$brand_nm', '$brand_nm_eng', '$overview', '$memo', '$keyword','$best_yn','$use_yn', '$brand_contents', '$logo_img_url', '$id', '$name', now(), now()
					)
				";

				try {
                    DB::insert($insert_brand);
                    $brand_result = 200;
                } catch(Exception $e){
                    $brand_result = 500;
                }
			}
		}else if( $cmd == "editcmd" ){
			$update_items	= [
				"brand_nm"		=> $brand_nm,
				"brand_nm_eng"	=> $brand_nm_eng,
<<<<<<< HEAD
				"overview"		=> $overview,
=======
				"overview"	=> $overview,
>>>>>>> main
				"memo"			=> $memo,
				"keyword"		=> $keyword,
				"brand_type"	=> $brand_type,
				"best_yn"		=> $best_yn,
				"use_yn"		=> $use_yn,
				"brand_contents"=> $brand_contents,
				"admin_id"		=> $id,
				"admin_nm"		=> $name,
				"brand_logo"	=> $logo_img_url,
				"ut"			=> now()
			];

			try {
				DB::table('brand')
				->where('brand','=', $brand)
				->update($update_items);
				//$code = 200;
				$brand_result = 200;
			} catch(Exception $e){
				//$code = 500;
				$brand_result = 500;
			}
		}else if($cmd == "delcmd"){
			//삭제할 해당 브랜드의 상품 브랜드를 none으로 변경

			$update_items = [
                "brand" => 'none'
            ];
            try {
                DB::table('goods')
                ->where('brand','=', $brand)
                ->update($update_items);
                //$code = 200;
                $goods_result = 200;
            } catch(Exception $e){
                //$code = 500;
                $goods_result = 500;
            }

			//브랜드 삭제
			$sql = "
				delete
				from brand
				where brand = '$brand'
			";
			try {
                DB::table('brand')
                   ->where('brand','=',$brand)
                   ->delete();
               $brand_result = 200;
            } catch(Exception $e){
               $brand_result = 500;
           	}
		}else if($cmd == "chg_brand"){		// 브랜드 변경

			//상품 브랜드를 변경할 브랜드으로 변경

			$update_items = [
                "brand" => $chg_brand
            ];
            try {
                DB::table('goods')
                ->where('brand','=', $brand)
                ->update($update_items);
                //$code = 200;
                $goods_result = 200;
            } catch(Exception $e){
                //$code = 500;
                $goods_result = 500;
            }

			//변경된 브랜드는 삭제 처리
			//브랜드 삭제
			try {
                DB::table('brand')
                   ->where('brand','=',$brand)
                   ->delete();
               $brand_result = 200;
           } catch(Exception $e){
               $brand_result = 500;
           }
		}

		return response()->json([
			"brand_result" => $brand_result,
			"goods_result" => $goods_result
        ]);
	}

	/*
		Function: CheckBrand
		브랜드 코드 중복확인
	*/
	public function CheckBrand(Request $request){
		$brand = $request->input("brand");
		$brand_check = 0;

		$query = "
			select
				count(*) as cnt
			from brand
			where brand = '$brand'
		";

		$row = DB::select($query);
        $cnt = $row[0]->cnt;
		if($cnt == 0){
			$brand_check = 1;
		}else{
			$brand_check = 0;
		}

		return response()->json([
            "code" => 200,
            "responseText" => $brand_check
        ]);
	}

	/*
		Function: View
		화면출력
	*/
	public function GetBrand($brand,Request $request){

		$query = "
			select
<<<<<<< HEAD
				brand, brand_nm, brand_nm_eng, a.overview, memo, a.keyword, a.best_yn,use_yn, brand_contents, ifnull(brand_logo,'') as brand_logo, admin_id, admin_nm, regi_date, ut, brand_type
=======
				brand, brand_nm, brand_nm_eng, a.overview, a.memo, a.keyword, a.best_yn,use_yn, brand_contents, ifnull(brand_logo,'') as brand_logo, admin_id, admin_nm, regi_date, ut, brand_type
>>>>>>> main
			from brand a
			where 1=1 and brand= :brand
		";
		$result = DB::select($query,array('brand' => $brand));

		return response()->json([
            "code" => 200,
			"body" => $result
        ]);
	}

	function GetBrandList(){
		// 등록된 브랜드 리스트 얻기
		$query2 = "
			select brand, brand_nm
			from brand
			order by brand_type,brand_nm
		";

		$brand_result = DB::select($query2);
		return response()->json([
            "code" => 200,
			"body" => $brand_result
        ]);
	}

	/*
		Function: GetBrandSummary
		브랜드 현황 얻기
	*/
	function GetBrandSummary(Request $request){

		$brand = $request->input("brand");

		// 브랜드 현황 얻기
		$query = "
			select sale_stat_cl, count(*) as goods_cnt
			from goods
			where brand = '$brand'
			group by sale_stat_cl
		";

		$result = DB::select($query);

		return response()->json([
            "code" => 200,
            "body" => $result
		]);

	}

}
