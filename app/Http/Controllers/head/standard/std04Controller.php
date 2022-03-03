<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class std04Controller extends Controller
{
    public function index() {
		$sites = DB::select("select com_id,com_nm from company where com_type = '4' and site_yn = 'Y'");
        $values = [
			"sites" => $sites
        ];
        return view( Config::get('shop.head.view') . '/standard/std04',$values);
    }

    public function search(Request $request){
        //조건에 관한 값들
		$s_cat_type	= $request->input("s_cat_type");
		$cat_name	= $request->input("cat_name");
		$use_yn	= $request->input("use_yn");
		$sort_opt	= $request->input("sort_opt");
		$cat_auth	= $request->input("cat_auth");
		$site = $request->input("site");
        
		$where = "";

		if($cat_name) $where .= "and c.d_cat_nm like '%$cat_name%'";
		if($use_yn) $where .= "and c.use_yn = '$use_yn'";
		if($sort_opt) $where .= "and c.sort_opt = '$sort_opt'";
		if($cat_auth) $where .= "and c.auth = '$cat_auth'";
		if($site != "") $where .= "and c.site = '$site'";

		//오늘 일자 얻기
		$today = date("Ymd");

		$query = "
			select c.d_cat_cd, floor(length(c.d_cat_cd)/3) as level, c.d_cat_nm,
				ifnull(b.40_cnt, 0) as 40_cnt,
				ifnull(b.30_cnt, 0) as 30_cnt,
				ifnull(b.cnt, 0) as cnt,
				ifnull(c.sort_opt, 'A') as sort_opt,
				ifnull(c.auth, 'A') as auth,
				ifnull(cs.dpv, 0) as dpv,
				ifnull(cs.wpv, 0) as wpv,
				ifnull(cs.mpv, 0) as mpv,
				c.use_yn,
                                d.com_nm,
				c.regi_date,
				c.upd_date,
				c.cat_type,
				c.full_nm,
				c.p_d_cat_cd
			from category c
				left outer join (
					  select
						a.cat_type
						, a.d_cat_cd
						, sum(if(a.sale_stat_cl = '30', a.cnt, 0 )) as 30_cnt
						, sum(if(a.sale_stat_cl = '40', a.cnt, 0)) as 40_cnt
						, sum(a.cnt) as cnt
					  from (
						select count(*) as cnt, g.sale_stat_cl, cg.cat_type, cg.d_cat_cd
						from category_goods cg
							inner join goods g on cg.goods_no = g.goods_no and cg.goods_sub = g.goods_sub
						where cg.cat_type = '$s_cat_type'
						group by g.sale_stat_cl, cg.cat_type, cg.d_cat_cd
						) a
					  group by a.cat_type, a.d_cat_cd
				) b on c.cat_type = b.cat_type and c.d_cat_cd = b.d_cat_cd
                                left outer join company d on c.site = d.com_id and d.site_yn = 'Y'
				left outer join category_stat cs on c.d_cat_cd = cs.cat_cd and cs.cat_type = '$s_cat_type' and cs.day = '$today'
			where c.cat_type = '$s_cat_type'
				$where
			order by c.seq, c.d_cat_cd
        ";
        //echo $query;
        $result = DB::select($query);
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
	
	public function detail(Request $request){
		$id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;
		
		$cfg_result	= DB::select("select value as val from conf where type='shop' and name='sale_place'");
		$cfg_site	= $cfg_result[0]->val;

		$charset	= 'kor';

		//파라미터
		$cat_type	= $request->input("cat_type");
		$p_d_cat_cd	= $request->input("p_d_cat_cd");
		$d_cat_cd	= $request->input("d_cat_cd");
		$site		= $request->input("site", $cfg_site);

		//초기값
		$cmd	= "addcmd";

		$cat_type_nm	= "";
		$d_cat_nm		= "";
		$use_yn			= "Y";
		$sort_opt		= "A";
		$header_html	= "";
		$auth			= "A";
		$admin_id		= $id;
		$admin_nm		= $name;
		$regi_date		= "";
		$upd_date		= "";
		$seq_cats		= "";
		$sort_out_of_stock	= "Y";
		$best_display_yn	= "";
		$site			= $cfg_site;

		$cnt	= 0;

		//부모 full_nm 얻기
		$p_full_nm	= "";

		$query	= "
			select full_nm
			from category
			where cat_type = '$cat_type' and d_cat_cd = '$p_d_cat_cd'
		";
		$rs = DB::select($query);
		if(count($rs)>0){
			$p_full_nm = $rs[0]->full_nm;
		}

		$query2 = " select com_id,com_nm from company where com_type = '4' and site_yn = 'Y' ";
		$sites = DB::select($query2);

		if($d_cat_cd != ""){
			$query2 = "
				select
					t.cat_type_nm, t.cat_type,
					c.d_cat_nm, c.sort_opt, c.use_yn, c.best_display_yn, c.header_html, c.full_nm, c.auth,
					c.p_d_cat_cd,
					c.admin_id, c.admin_nm,
					c.regi_date, c.upd_date,
					c.sort_out_of_stock,
					c.best_display_yn,
                                        c.site
				from category c
					inner join category_type t on t.cat_type = c.cat_type
				where c.cat_type = '$cat_type' and c.d_cat_cd ='$d_cat_cd'
				order by d_cat_cd
			";
			//echo $query2;
			$cat_rs = DB::select($query2);
			if(count($cat_rs)>0){
				$cat_type_nm	= $cat_rs[0]->cat_type_nm;
				$d_cat_nm	= $cat_rs[0]->d_cat_nm;
				$use_yn		= $cat_rs[0]->use_yn;
				$sort_opt	= $cat_rs[0]->sort_opt;
				$header_html	= $cat_rs[0]->header_html;
				$auth		= $cat_rs[0]->auth;
				$admin_id	= $cat_rs[0]->admin_id;
				$admin_nm	= $cat_rs[0]->admin_nm;
				$regi_date	= $cat_rs[0]->regi_date;
				$upd_date	= $cat_rs[0]->upd_date;
				$p_d_cat_cd	= $cat_rs[0]->p_d_cat_cd;
				$sort_out_of_stock	= $cat_rs[0]->sort_out_of_stock;
				$best_display_yn	= $cat_rs[0]->best_display_yn;
				$site			= $cat_rs[0]->site;

				$query3 = "
					select ifnull(concat(d_cat_cd,'|',ifnull(d_cat_nm, '')),'') as seq_cat
					from category
					where cat_type = '$cat_type' and p_d_cat_cd = '$p_d_cat_cd'
					order by seq
				";
				$cate_rows = DB::select($query3);
				$seq_cats = "";
				foreach($cate_rows as $row){
					if($seq_cats == ""){
						$seq_cats = $row->seq_cat;
					} else {
						$seq_cats .= sprintf("\t%s",$row->seq_cat);
					}

				}
				
			}

			//최하단 카테고리 여부
			$query4 = "
				select count(*) as cnt
				from category
				where cat_type = '$cat_type' and d_cat_cd like '$d_cat_cd%'
			";
			$cnd_row = DB::select($query4);
			$cnt = $cnd_row[0]->cnt;

			$cmd = "editcmd";


		}

		// 카테고리 코드 얻기
		if( $cmd == "addcmd" ){
			$d_cat_cd = $this->GetNextCode($cat_type, $p_d_cat_cd, $site);
			//echo $d_cat_cd;
		}
		

		$values	= [
			"cmd"			=> $cmd,
			"cat_type_nm"	=> $cat_type_nm,
			"cat_type"		=> $cat_type,
			"p_d_cat_cd"	=> $p_d_cat_cd,
			"p_full_nm"		=> $p_full_nm,
			"d_cat_cd"		=> $d_cat_cd,
			"site"			=> $site,
			"sites"			=> $sites,
			"admin_id"		=> $admin_id,
			"admin_nm"		=> $admin_nm,
			"regi_date"		=> $regi_date,
			"upd_date"		=> $upd_date,
			"sort_opt"		=> $sort_opt,
			"best_display_yn"	=> $best_display_yn,
			"sort_out_of_stock"	=> $sort_out_of_stock,
			"use_yn"		=> $use_yn,
			"d_cat_nm"		=>$d_cat_nm,
			"sub_cat_cnt"	=> $cnt,
			"header_html"	=> $header_html,
			"seq_cats"		=> $seq_cats,
			"auth"			=> $auth
		];
		
		return view( Config::get('shop.head.view') . '/standard/std04_show',$values);
	}

	public function GetMemberGroup(Request $request){
		$cat_type = $request->input("cat_type");
		$d_cat_cd = $request->input("d_cat_cd");
		// member group 얻기
		$query = " 
			select
				'' as blank
				, a.group_nm, a.dc_ratio, a.point_ratio, a.group_no, b.group_cd
			from user_group a
				left outer join category_group b on a.group_no = b.group_cd and b.cate_type = '$cat_type' and b.d_cat_cd = '$d_cat_cd'
		";
		//echo $query;
		$result = DB::select($query);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
                "page" => 0,
                "page_cnt" => 0,
                "page_total" => 1
            ),
            "body" => $result
        ]);
	}

	public function Command($catCd = "", Request $request){
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		//파라미터
		$site				= $request->input("site");
		$cat_type			= $request->input("cat_type");
		//$cat_type			= strtoupper($cat_type);
		$d_cat_cd			= $request->input("d_cat_cd");
		$chg_d_cat_cd		= $request->input("chg_d_cat_cd");
		$p_d_cat_cd			= substr($d_cat_cd, 0, strlen($d_cat_cd)-3);
		$d_cat_nm			= $request->input("d_cat_nm");
		$use_yn				= $request->input("use_yn");
		$sort_opt			= $request->input("sort_opt");
		$sort_out_of_stock	= $request->input("sort_out_of_stock");
		$best_display_yn	= $request->input("best_display_yn", "");
		$apply_yn			= $request->input("apply_yn");
		$header_html		= $request->input("header_html");
		$auth				= $request->input("auth");
		$is_cats			= $request->input("is_cats");
		$cats				= $request->input("cats");
		$member_group		= $request->input("member_group");
		$create_low			= $request->input("create_low");
		$cmd				= $request->input("cmd");

		$cate_result = 500;
		$cate_result2 = 500;
		$cate_group_result = 500;
		$result_code = 1;
		$cate_result2 = 500;
		//카테고리 클래스 생성

		if($cmd == "addcmd" || $cmd == "editcmd") { // 카테고리 추가
			
			// 전체 로케이션 얻기
			$query = "
				select group_concat(d_cat_nm order by d_cat_cd  separator ' > ') as full_nm
				from category
				where cat_type = '$cat_type' and instr('$p_d_cat_cd',d_cat_cd) = 1
			";
			$rs = DB::select($query);
			if($rs[0]->full_nm){
				$full_nm = $rs[0]->full_nm." > ".$d_cat_nm;
			}else{
				$full_nm = $d_cat_nm;
			}

			if($p_d_cat_cd == $d_cat_cd) $p_d_cat_cd = '';

			$sql_where = "";
			if($p_d_cat_cd != "") $sql_where = " and (p_d_cat_cd = '$p_d_cat_cd' or d_cat_cd = '$p_d_cat_cd')";
			
			$seq_rs = DB::select("select seq From category as c where 1=1 and c.cat_type='$cat_type' $sql_where order by seq desc limit 1");
			$seq  = $seq_rs[0]->seq;
			$seq = $seq+1;
			
			if($cmd == "addcmd"){
				//카테고리 추가
				
				$values = [
					"cat_type"		=> $cat_type, 
					"d_cat_cd"		=> $d_cat_cd, 
					"p_d_cat_cd"	=> $p_d_cat_cd, 
					"d_cat_nm"		=> $d_cat_nm, 
					"site"			=> $site, 
					"sort_opt"		=> $sort_opt, 
					"auth"			=> $auth, 
					"use_yn"		=> $use_yn, 
					"best_display_yn"	=> $best_display_yn, 
					"full_nm"		=> $full_nm, 
					"header_html"	=> $header_html, 
					"admin_id"		=> $id, 
					"admin_nm"		=> $name,
					"regi_date"		=> now(), 
					"upd_date"		=> now(), 
					"sort_out_of_stock"	=> $sort_out_of_stock, 
					"seq"			=> $seq
				];

				$values2 = [
					"cat_type"		=> 'BEST', 
					"d_cat_cd"		=> $d_cat_cd, 
					"p_d_cat_cd"	=> $p_d_cat_cd, 
					"d_cat_nm"		=> $d_cat_nm, 
					"site"			=> $site, 
					"sort_opt"		=> $sort_opt, 
					"auth"			=> $auth, 
					"use_yn"		=> $use_yn, 
					"best_display_yn"	=> $best_display_yn, 
					"full_nm"		=> $full_nm, 
					"header_html"	=> $header_html, 
					"admin_id"		=> $id, 
					"admin_nm"		=> $name,
					"regi_date"		=> now(), 
					"upd_date"		=> now(), 
					"sort_out_of_stock" => $sort_out_of_stock, 
					"seq"			=> $seq
				];

				if( $p_d_cat_cd != "" ){

					try {
							$query = "update category set seq = seq + 1 where cat_type ='$cat_type' and seq >= '$seq' ";
						/*
						echo $query;
						echo "<br>";
						*/
						DB::update($query);
						/*
						DB::table('category')
							->where('cat_type','=','UPPER($cat_type)')
							->where('seq','>=', $seq)
							->update($up_items);
							*/
						$goods_class_up_result = 200;
					} catch(Exception $e){
						$goods_class_up_result = 500;
					}
				}

				//권한 그룹 설정
				if( $member_group != "" ){

					$member_group_arr	= explode(",", $member_group);
					for( $i = 0; $i < count($member_group_arr); $i++){
						$insert_cate_group = "
							insert into category_group(
								cate_type, d_cat_cd, group_cd, rt, ut
							) values(
								'$cat_type', '$d_cat_cd', '$member_group_arr[$i]', now(), now()
							)
						";
						//$conn->Execute($sql);
						try {
							DB::insert($insert_cate_group);
							$cate_group_result = 200;
						} catch(Exception $e){
							$cate_group_result = 500;
						};
						//echo "cate_group_result". $cate_group_result;
					}
				}
				
			}else{
				
				$values = [
					"cat_type"		=> $cat_type, 
					"d_cat_cd"		=> $d_cat_cd, 
					"p_d_cat_cd"	=> $p_d_cat_cd, 
					"d_cat_nm"		=> $d_cat_nm, 
					"site"			=> $site, 
					"sort_opt"		=> $sort_opt, 
					"auth"			=> $auth, 
					"use_yn"		=> $use_yn, 
					"best_display_yn"	=> $best_display_yn, 
					"full_nm"		=> $full_nm, 
					"header_html"	=> $header_html, 
					"admin_id"		=> $id, 
					"admin_nm"		=> $name, 
					"upd_date"		=> now(), 
					"sort_out_of_stock" => $sort_out_of_stock
				];

				$values2 = [
					"cat_type"		=> 'BEST', 
					"d_cat_cd"		=> $d_cat_cd, 
					"p_d_cat_cd"	=> $p_d_cat_cd, 
					"d_cat_nm"		=> $d_cat_nm, 
					"site"			=> $site, 
					"sort_opt"		=> $sort_opt, 
					"auth"			=> $auth, 
					"use_yn"		=> $use_yn, 
					"best_display_yn"	=> $best_display_yn, 
					"full_nm"		=> $full_nm, 
					"header_html"	=> $header_html, 
					"admin_id"		=> $id, 
					"admin_nm"		=> $name, 
					"upd_date"		=> now(), 
					"sort_out_of_stock" => $sort_out_of_stock
				];

				//카테고리 순서변경
				if( $is_cats == "Y" ){
					$sql = "
						select
							min(seq) as min_seq,max(seq) as max_seq,count(*) as cnt
						from category
						where cat_type = '$cat_type' and p_d_cat_cd = '$p_d_cat_cd'
					";
					$cats_row = DB::select($sql);
					$new_min_seq = $cats_row[0]->min_seq;
					$new_max_seq = $cats_row[0]->max_seq;
					$cnt = $cats_row[0]->cnt;

					if($new_min_seq > 0) {

						$arr_cats = explode("\t", $cats);

						for($i = 0; $i < count($arr_cats); $i++) {
							$cat = $arr_cats[$i];
							$sql = "
								select seq from category
								where cat_type = '$cat_type' and d_cat_cd = '$cat'
							";
							//$rs = $conn->Execute($sql);
							$seq_row = DB::select($sql);
							$row = $seq_row[0];
							$min_seq = $row->seq;

							if($min_seq > 0 && $new_min_seq != $min_seq) {
								$update_items = [
									"seq" => $new_min_seq ."( seq -". $min_seq .")"
								];
								$sql = "
									update category set
										seq = $new_min_seq + ( seq - $min_seq )
									where cat_type = '$cat_type' and d_cat_cd like '$cat%'
								";
								DB::update($sql);
							}

							$sql = "
								select max(seq) as max_seq
								from category
								where cat_type = '$cat_type' and d_cat_cd like '$cat%'
							";
							$seq_row = DB::select($sql);
							$row = $seq_row[0];
							$new_min_seq = $row->max_seq + 1;
						}
					}
				}

				//권한 그룹 수정 (삭제 후 다시 저장하는 방식)
				//권한 그룹 삭제
				if($apply_yn == "Y"){	//하위카테고리 적용

					$sql = "
						delete
						from category_group
						where cate_type = '$cat_type' and d_cat_cd like '$d_cat_cd%'
					";
					//$conn->Execute($sql);
					DB::delete("delete from category_group where cate_type = '$cat_type' and d_cat_cd like '$d_cat_cd%'");

					//권한 그룹 설정
					if($member_group != ""){

						$sql = "
							select d_cat_cd
							from category
							where cat_type = '$cat_type' and d_cat_cd like '$d_cat_cd%'
						";
						$rows = DB::select($sql);
						foreach($rows as $rs){

							$d_cat_cd = $rs->d_cat_cd;

							$member_group_arr = explode(",", $member_group);
							for( $i = 0; $i < count($member_group_arr); $i++){
								$insert_auth = "
									insert into category_group(
										cate_type, d_cat_cd, group_cd, rt, ut
									) values(
										'$cat_type', '$d_cat_cd', '$member_group_arr[$i]', now(), now()
									)
								";
								//$conn->Execute($sql);
								try {
									DB::insert($insert_auth);
									$cate_group_result = 200;
								} catch(Exception $e){
									$cate_group_result = 500;
								};
							}
						}
					}

				} else {	//하위카테고리 적용 안함
					/*
					$sql="
						delete
						from category_group
						where cate_type = '$cat_type' and d_cat_cd = '$d_cat_cd'
					";
					*/
					DB::delete("delete from category_group where cate_type = '$cat_type' and d_cat_cd = '$d_cat_cd'");


					//권한 그룹 설정
					if($member_group != ""){

						$member_group_arr = explode(",", $member_group);
						for( $i = 0; $i < count($member_group_arr); $i++){
							$insert_auth = "
								insert into category_group(
									cate_type, d_cat_cd, group_cd, rt, ut
								) values(
									'$cat_type', '$d_cat_cd', '$member_group_arr[$i]', now(), now()
								)
							";
							/*
							echo "insert_auth : ". $insert_auth;
							echo "<br>";
							*/
							try {
								DB::insert($insert_auth);
								$cate_group_result = 200;
							} catch(Exception $e){
								$cate_group_result = 500;
							};
						}
					}
				}
				
			}

			$wheres = [ 'cat_type' => DB::raw("UPPER('$cat_type')"), 'd_cat_cd' => $d_cat_cd ];
			try {
				DB::table('category')->updateOrInsert($wheres, $values);
				$cate_result = 200;
			} catch(Exception $e){
				$cate_result = 500;
			};
			
			if($cat_type=="DISPLAY"){	//전시카테고리 추가시 베스트 카테고리 추가 / 수정
				//$values2 = 
				$wheres2 = [ 'cat_type' => DB::raw("UPPER('BEST')"), 'd_cat_cd' => $d_cat_cd ];
				try {
					DB::table('category')->updateOrInsert($wheres2, $values2);
					$cate_result2 = 200;
				} catch(Exception $e){
					$cate_result2 = 500;
				};
			}

			
			if($member_group != ""){
				if($cate_group_result == 200){
					$result_code = 1;
				}else{
					$result_code = 0;
				}
			}
			
			if($cate_result == 200){
				$result_code = 1;
			}else{
				$result_code = 0;
			}
			
			if($cat_type=="DISPLAY"){
				if($cate_result2 == 200){
					$result_code = 1;
				}else{
					$result_code = 0;
				}
			}
			
		}else if($cmd == "delcmd"){
			$cnt = 0;

			// 하위카테고리가 있을 경우 삭제 제한
			$query = "
				select count(*) as cnt
				from category
				where cat_type = '$cat_type' and d_cat_cd like '$d_cat_cd%'
			";
			$row = DB::select($query);

			$cnt  = $row[0]->cnt;

			if($cnt == 1){
				try {
					DB::delete("delete from category where cat_type = '$cat_type' and d_cat_cd like '$d_cat_cd%'");
					$cate_result = 200;
				} catch(Exception $e){
					$cate_result = 500;
				}

				if($cat_type=="DISPLAY"){
					try {
						DB::delete("delete from category where cat_type = 'BEST' and d_cat_cd like '$d_cat_cd%'");
						$cate_result2 = 200;
					} catch(Exception $e){
						$cate_result2 = 500;
					}
				}

				if($cate_result == 500){
					$result_code = 0;
				}else{
					$result_code = 1;
				}

				if($cat_type=="DISPLAY"){
					if($cate_result2 == 500){
						$result_code = 0;
					}else{
						$result_code = 1;
					}
				}

			}else if($cnt > 1) {
				$result_code = "100";
			}else {
				$result_code = "110";
			}
		}else if($cmd == "copy_category" || $cmd == "change_category"){
			$t_cat_cd = array();
			$t_goods_cd = "";
			$add_sql = "";
			$category_goods_reuslt = 0;
			$goods_result = 0;
			$category_reuslt = 0;
			$category_best_result = 0;
			$category_group_result = 0;

			if ($create_low)
			{
				$t_cat_cd[] = $d_cat_cd;
			}
			else
			{
				$query = "select d_cat_cd FROM category WHERE cat_type = '$cat_type' AND p_d_cat_cd = '$d_cat_cd'";
				//echo $query;
				$rows = DB::select($query);
				foreach($rows as $rs){
					$t_cat_cd[] = $rs->d_cat_cd;
				}
				$add_sql = " AND d_cat_cd != '$d_cat_cd'";
			}

			//print_r($t_cat_cd);

			// 카테고리 생성
			//echo "test";
			for ($i = 0; $i < sizeof($t_cat_cd); $i++)
			{
				$t_goods_cd = $this -> copyCategory($cat_type, $chg_d_cat_cd, $t_cat_cd[$i], $site);
				//echo $t_goods_cd;
			}
			if (sizeof($t_cat_cd) > 1) $t_goods_cd = substr($t_goods_cd, 0, -3);
			// 상품 복사
			$t_goods_cd = ($t_goods_cd) ? $t_goods_cd : $chg_d_cat_cd;
			$this -> copyGoods($cat_type, $t_goods_cd, $d_cat_cd);

			// 변경일 경우 처리
			if ($cmd == "change_category")
			{
				// 카테고리 상품 삭제
				$sql = "
					DELETE FROM category_goods WHERE cat_type = '$cat_type' AND d_cat_cd LIKE '$d_cat_cd%'
				";
				try {
					DB::delete($sql);
				   $category_goods_result = 200;
				} catch(Exception $e){
					$category_goods_result = 500;
				}

				// 대표 카테고리 변경
				$sql = "
					UPDATE goods SET rep_cat_cd = CONCAT('$t_goods_cd', SUBSTRING(rep_cat_cd, LENGTH('$d_cat_cd') + 1)) WHERE rep_cat_cd LIKE '$d_cat_cd%'
				";
				//debugSQL($sql);
				try {
					DB::update($sql);
				   $goods_result = 200;
				} catch(Exception $e){
					$goods_result = 500;
				}

				// 카테고리 삭제
				$sql = "
					DELETE FROM category WHERE cat_type = '$cat_type' AND d_cat_cd LIKE '$d_cat_cd%'$add_sql
				";
				//debugSQL($sql);
				try {
					DB::delete($sql);
				   $category_reuslt = 200;
				} catch(Exception $e){
					$category_reuslt = 500;
				}

				// 베스트카테고리 삭제
				if($cat_type == "DISPLAY"){
					$sql = "
						DELETE FROM category WHERE cat_type = 'BEST' AND d_cat_cd LIKE '$d_cat_cd%'$add_sql
					";
					//debugSQL($sql);
					try {
						DB::delete($sql);
					   $category_best_result = 200;
					} catch(Exception $e){
						$category_best_result = 500;
					}
				}

				//삭제된 카테고리의 권한 삭제
				$sql = "
					DELETE FROM category_group WHERE cate_type = '$cat_type' AND d_cat_cd LIKE '$d_cat_cd%'$add_sql
				";
				//debugSQL($sql);
				try {
					DB::delete($sql);
				   $category_group_result = 200;
				} catch(Exception $e){
					$category_group_result = 500;
				}

				if($category_goods_result == 500){
					$result_code = 0;
				}else if($goods_result == 500){
					$result_code = 0;
				}else if($category_reuslt == 500){
					$result_code = 0;
				}else if($category_best_result == 500){
					$result_code = 0;
				}else if($category_group_result == 500){
					$result_code = 0;
				}else{
					$result_code = 1;
				}
			}
		}

		//return response()->json(null, 204);
		return response()->json([
			"code" => 200,
			"result_code" => $result_code
        ]);
	}
	
	/*
		Function: copyGoods
		상품 복사
		Parameters:
			$CAT_TYPE - 카테고리 구분값(전시카테고리, 용도카테고리)
			$t_goods_cd - 상품의 카테고리 값
			$D_CAT_CD - 원본 카테고리 값
		See Also:
	*/
	public function copyGoods($cat_type, $t_goods_cd, $d_cat_cd)
	{
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$goods_result = 500;
		$goods_result_arr = [];
		$reulst_code = 0;

		// 상품 복사
		$sql = "
			INSERT IGNORE INTO category_goods
			(
				cat_type, d_cat_cd, goods_no, goods_sub, disp_yn, admin_id, admin_nm, regi_date, seq
			)
			SELECT
				'$cat_type', CONCAT('$t_goods_cd', SUBSTRING(d_cat_cd, LENGTH('$d_cat_cd') + 1)), goods_no, goods_sub, disp_yn, '$admin_id', '$admin_nm', now(), seq
			FROM
				category_goods
			WHERE
				cat_type = '$cat_type'
				AND d_cat_cd LIKE '$d_cat_cd%'
		";
		//ebugSQL($sql);
		//$conn -> Execute($sql);
		try {
			DB::insert($sql);
			$goods_result = 200;
		} catch(Exception $e){
			$goods_result = 500;
		}

		// 상위 카테고리에도 상품 추가해 줘야 하는 문제로 while문 처리
		while(strlen($t_goods_cd))
		{
			$t_goods_cd = substr($t_goods_cd, 0, -3);
			if (!$t_goods_cd) break;
			$sql = "
				INSERT IGNORE INTO category_goods
				(
					cat_type, d_cat_cd, goods_no, goods_sub, disp_yn, admin_id, admin_nm, regi_date, seq
				)
				SELECT
					'$cat_type', '$t_goods_cd', goods_no, goods_sub, disp_yn, '$admin_id', '$admin_nm', now(), seq
				FROM
					category_goods
				WHERE
					cat_type = '$cat_type'
					AND d_cat_cd LIKE '$d_cat_cd%'
					AND d_cat_cd != '$t_goods_cd'
				GROUP BY cat_type, goods_no, goods_sub
			";
			//debugSQL($sql);
			//$conn -> Execute($sql);
			try {
				DB::insert($sql);
				$goods_result_arr[] = 200;
			} catch(Exception $e){
				$goods_result_arr[] = 500;
			}
		}
		if($goods_result == 500){
			$reulst_code = 0;
		}else if(in_array(500, $goods_result_arr)){
			$reulst_code = 0;
		}else{
			$reulst_code = 1;
		}

		return $reulst_code;
	}

	/*
		Function: copyCategory
		상품 복사
		Parameters:
			$CAT_TYPE - 카테고리 구분값(전시카테고리, 용도카테고리)
			$CHG_D_CAT_CD - 변경될 카테고리 값
			$D_CAT_CD - 원본 카테고리 값
		See Also:
	*/
	public function copyCategory($cat_type, $chg_d_cat_cd, $d_cat_cd, $site)
	{
	
		$new_top_cat = $this -> GetNextCode($cat_type, $chg_d_cat_cd, $site);
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		$cate_result = 500;

		// 변경전 데이터 정보
		$sql = "
			SELECT COUNT(*) AS cnt FROM category WHERE cat_type = '$cat_type' AND d_cat_cd LIKE '$d_cat_cd%'
		";
		//debugSQL($sql);
		$rs = DB::select($sql);
		$p_cnt = $rs[0]->cnt;

		// 상위 카테고리 풀네임 / MAX seq 및  가져오기
		$p_full_nm = "";
		$sql = "
			SELECT
				full_nm,
				(
					SELECT
						IFNULL(MAX(cc.seq), 0)
					FROM
						category AS cc
					WHERE
						cc.cat_type = '$cat_type'
						AND cc.d_cat_cd LIKE '$chg_d_cat_cd%'
				)
				AS max_seq
			FROM
				category
			WHERE
				cat_type = '$cat_type'
				AND d_cat_cd = '$chg_d_cat_cd'
		";
		//debugSQL($sql);
		$rs = DB::select($sql);
		$p_full_nm = $rs[0]->full_nm;
		$p_max_seq = $rs[0]->max_seq;

		// 정렬 순위 조절
		$sql = "
			UPDATE category SET
				seq = seq + '$p_cnt'
			WHERE
				cat_type = '$cat_type'
				AND seq > '$p_max_seq'
		";
		//debugSQL($sql);
		//$conn -> Execute($sql);
		
		try {
			DB::update($sql);
			$cate_result = 200;
		} catch(Exception $e){
			$cate_result = 500;
		}

		// 신규 카테고리 생성
		$sql = "
			INSERT INTO category (cat_type, d_cat_cd,
				p_d_cat_cd,
				d_cat_nm, type, tpl_kind, sale_yn, sale_kind, sale_amt, sale_ratio,
				use_yn, best_display_yn, header_html, admin_id, admin_nm, regi_date, upd_date,
				full_nm, sort_opt, sort_out_of_stock, auth, site,
				seq
			)
			SELECT
				'$cat_type', CONCAT('$new_top_cat', SUBSTRING(d_cat_cd, LENGTH('$d_cat_cd') + 1)),
				SUBSTRING(
					CONCAT('$new_top_cat', SUBSTRING(d_cat_cd, LENGTH('$d_cat_cd') + 1)),
					1,
					LENGTH(CONCAT('$new_top_cat', SUBSTRING(d_cat_cd, LENGTH('$d_cat_cd') + 1))) -3
				),
				d_cat_nm, type, tpl_kind, sale_yn, sale_kind, sale_amt, sale_ratio,
				use_yn, best_display_yn, header_html, '$admin_id', '$admin_nm', now(), now(),
				CONCAT('$p_full_nm', ' > ', full_nm), sort_opt, sort_out_of_stock, auth, '$site',
				(
					'$p_max_seq' + (c.seq - (SELECT MIN(seq) FROM category WHERE cat_type = '$cat_type' AND d_cat_cd LIKE '$d_cat_cd')) + 1
				)
			FROM
				category AS c
			WHERE
				c.cat_type = '$cat_type'
				AND c.d_cat_cd LIKE '$d_cat_cd%'
		";
		//echo $sql;
		//debugSQL($sql);
		//$conn -> Execute($sql);
		
		try {
			DB::insert($sql);
			$cate_result = 200;
		} catch(Exception $e){
			$cate_result = 500;
		}
		

		return $new_top_cat;
	}

	/*
		Function: GetNextCode
		하위 카테고리 코드 얻기
		Parameters:
			$cat_type - 카테고리 구분값(전시카테고리, 용도카테고리)
			$p_d_cat_cd - 부모 카테고리 코드값

		See Also:
	*/
	public function GetNextCode($cat_type, $p_d_cat_cd, $site){
		$cat_cd_where	= "";
		//$d_cat_cd = $this->GetNextCode($cat_type, $p_d_cat_cd, $site);
		$cat_cd_where	= " cat_type='$cat_type'";

		//if($site != "")	$cat_cd_where .= " and site='$site'";
		if( $p_d_cat_cd != "" ){
			$cat_cd_where	.= " and d_cat_cd like '$p_d_cat_cd%' and p_d_cat_cd='$p_d_cat_cd'";
		}else{
			$cat_cd_where	.= " and p_d_cat_cd = '$p_d_cat_cd'";
		}
		
		$d_cat_cd_query	= "
			select 
				d_cat_cd
			From category as c
			Where 
				$cat_cd_where
			order by d_cat_cd desc
			limit 1
		";
		$cat_cd_rows	= DB::select($d_cat_cd_query);
		$cat_cd_cnt		= count($cat_cd_rows);
		
		if( $cat_cd_cnt > 0 )
		{
			$code_len	= strlen($cat_cd_rows[0]->d_cat_cd);
			$p_d_cat_cd	= substr($cat_cd_rows[0]->d_cat_cd, 0, strlen($cat_cd_rows[0]->d_cat_cd) - 3);

			if( $code_len > 3 ){
				$new_cat_cd	= substr($cat_cd_rows[0]->d_cat_cd, strlen($cat_cd_rows[0]->d_cat_cd) - 3, strlen($cat_cd_rows[0]->d_cat_cd) - 1) + 1;
			}else{
				$new_cat_cd	= $cat_cd_rows[0]->d_cat_cd + 1;
			}

			if( $new_cat_cd < 10 ){
				$d_cat_cd	= '00'.$new_cat_cd;
			}else if( $new_cat_cd < 100 ){
				$d_cat_cd	= '0'.$new_cat_cd;
			}else{
				$d_cat_cd = $new_cat_cd;
			}
			$d_cat_cd = $p_d_cat_cd . $d_cat_cd;
			//$new_cat_cd = $cat_cd_rows[0]->d_cat_cd+1;
		}else{
			$d_cat_cd	= $p_d_cat_cd ."001";
		}

		return $d_cat_cd;
	}


}
