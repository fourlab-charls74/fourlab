<?php

namespace App\Http\Controllers\head\member;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;

class mem22Controller extends Controller
{
	public function index() {
		$last_year = Carbon::now()->sub('1 year')->format('Y-m-d');
	  	$values = [
			  'last_year' => $last_year,
		];
	  	return view( Config::get('shop.head.view') . '/member/mem22',$values);
	}

	public function search(Request $request){
		// 설정 값 얻기
		$cfg_img_size_real = SLib::getCodesValue("G_IMG_SIZE","real");

		$style_no			= $request->input("style_no");
		$goods_no			= $request->input("goods_no");
		$goods_nm			= $request->input("goods_nm");
		$goods_est_from		= $request->input("goods_est_from");
		$goods_est_to		= $request->input("goods_est_to");
		$goods_title		= $request->input("goods_title");
		$name				= $request->input("name");
		$id					= $request->input("id");
		$best_yn			= $request->input("best_yn");
        $best_type			= $request->input("best_type");
		$point_yn			= $request->input("point_yn");
		$buy_yn				= $request->input("buy_yn");
		$use_yn				= $request->input("use_yn");
        //$prt_text			= $request->input("prt_text");

		$page 				= $request->input("page", 1);
		$page_size			= $request->input("limit", 100);

        if ($page < 1 or $page == "") $page = 1;

        $total		= 0;
        $page_cnt	= 0;

		$where = "";

		if ($style_no != "")			$where .= " and g.style_no = '$style_no' ";
		//if ($goods_no != "")			$where .= " and a.goods_no = '$goods_no' ";

		$goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and a.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and a.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

		if ($goods_nm != "")			$where .= " and g.goods_nm like '%$goods_nm%' ";
		if ($goods_est_from != "")      $where .= " and a.goods_est >= '$goods_est_from' ";
		if ($goods_est_to != "")		$where .= " and a.goods_est <= '$goods_est_to' ";
		if ($goods_title != "")			$where .= " and a.goods_title like '%$goods_title%' ";
		if ($name != "")				$where .= " and a.name like '%$name%' ";
		if ($id != "")					$where .= " and a.user_id like '%$id%' ";
		if ($best_yn != "")				$where .= " and a.best_yn = '$best_yn' ";
		if ($best_type != "")			$where .= " and a.best_type = '$best_type' ";
		if ($point_yn != "")			$where .= " and a.point_yn = '$point_yn' ";
		if ($buy_yn != "")				$where .= " and a.buy_yn = '$buy_yn' ";
		if ($use_yn != "")				$where .= " and a.use_yn = '$use_yn' ";

		if( $page == 1 ) 
		{
			$sql	= "
				select count(*) as cnt
				from goods_estimate a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				where 1=1 $where
			";
			//echo $sql;
			$row = DB::selectOne($sql);
			$total = $row->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($total - 1)/$page_size) + 1;
            $startno = ($page - 1) * $page_size;
			//$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page, "page_total"=>count);
		} else {
			$startno = ($page - 1) * $page_size;
			//$arr_header = null;
		}

		$sql	= "
			select
				a.no,
				replace(b.img,'$cfg_img_size_real', 's_50') as img_s_50,
				b.goods_nm,
				(case a.goods_est
					when '1' then '★☆☆☆☆'
					when '2' then '★★☆☆☆'
					when '3' then '★★★☆☆'
					when '4' then '★★★★☆'
					when '5' then '★★★★★'
				end) as estimate,
				a.goods_est,
				ifnull(a.best_yn, 'N') as best_yn,'' as best_type,
				ifnull(a.buy_yn, 'N') as buy_yn,
				a.goods_title,a.goods_text, 
				concat(a.name,'(',a.user_id,')') as name, a.user_id,
				ifnull(a.point_yn, 'N') as point_yn, a.point,
				a.use_yn, a.cnt, a.comment_cnt, a.image_cnt, a.regi_date,
				a.goods_no, a.goods_sub as goods_sub, b.style_no, c.estimate_no,
				o.ord_no,o.ord_opt_no,
				( select count(*) from goods_estimate where user_id = a.user_id ) as est_user_cnt,
				( select count(*) from goods_estimate where goods_no = a.goods_no ) as est_goods_cnt,
				( select min(no) from goods_estimate where user_id = a.user_id and ord_opt_no = a.ord_opt_no ) as min_no,
				( select ord_date from order_opt where ord_opt_no = a.ord_opt_no ) as ord_date
			from (
				select 
					no 
				from goods_estimate a
				left outer join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				where 1=1 $where
				order by no desc
				limit $startno, $page_size
			) e 
			inner join goods_estimate a on a.no = e.no
			inner join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
			left outer join goods_estimate_best c on a.no = c.estimate_no
			left outer join order_opt o on a.ord_opt_no = o.ord_opt_no
		";
		/*
		echo $sql;
		echo "<br>";
		*/

		$data_list = array();
		$list_no = $total - $startno;

		$result = DB::select($sql);
		
		foreach($result as $row){
			$row->goods_text	= htmlspecialchars($row->goods_text);
		/*
			array_push($data_list,
				array(
					"no" => $row->no,
					"list_no"		=> $list_no,
					"img_s_50"		=> $row->img_s_50,
					"goods_nm"		=> $row->goods_nm,
					"goods_nm"		=> $row->goods_nm,
					"estimate"		=> $row->estimate,
					"goods_est"		=> $row->goods_est,
					"best_yn"		=> $row->best_yn,
					"best_type"		=> $row->best_type,
					"buy_yn"		=> $row->buy_yn,
					"goods_title"	=> $row->goods_title,
					"goods_text"	=> htmlspecialchars($row->goods_text),
					"name"			=> $row->name,
					"user_id"		=> $row->user_id,
					"point_yn"		=> $row->point_yn,
					"point"			=> $row->point,
					"use_yn"		=> $row->use_yn,
					"cnt"			=> $row->cnt,
					"comment_cnt"	=> $row->comment_cnt,
					"image_cnt"		=> $row->image_cnt,
					"regi_date"		=> $row->regi_date,
					"goods_no"		=> $row->goods_no,
					"goods_sub"		=> $row->goods_sub,
					"style_no"		=> $row->style_no,
					"estimate_no"	=> $row->estimate_no,
					"ord_no"		=> $row->ord_no,
					"ord_opt_no"	=> $row->ord_opt_no,
					"est_user_cnt"	=> $row->est_user_cnt,
					"est_goods_cnt" => $row->est_goods_cnt,
					"min_no"		=> $row->min_no
				)
			);
			$list_no--;
		*/
		}

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => $page_size
			),
			"body" => $result
		]);

	}
	
	/*
		Function: ViewContents
		상품평 상세조회
	*/
	public function show($no = '', Request $request){
		// 설정 값 얻기
		$conf = new Conf();

		$cfg_img_size_detail	= SLib::getCodesValue("G_IMG_SIZE","detail");
		$cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

		$no			= request("no");
		$menu_id	= request("menu_id");
		$ipaddres	= $this->createAddress();

		$ord_no = "";
		$ord_opt_no = "";
		$group_nm = "";

		//상품평 상세 정보 얻기
		$sql = "
			select
				replace(b.img, '$cfg_img_size_real', '$cfg_img_size_detail') as goods_img
				, c.opt_kind_nm, d.brand_nm, b.goods_nm
				, a.cnt, a.goods_est, a.goods_title, a.buy_yn, a.best_yn, '' as best_type, a.use_yn, a.name, a.user_id, a.regi_date, a.goods_text
				, a.ipaddres as ipaddres, '$ipaddres' as input_ipaddres
				, a.goods_no, a.goods_sub
				, e.code_val as sale_stat_cl
				, ifnull((
					select sum(good_qty) from goods_summary
					where goods_no = b.goods_no and goods_sub = b.goods_sub and good_qty > 0
				 ),0) as good_qty
				, ifnull((
					select sum(wqty) from goods_summary
					where goods_no = b.goods_no and goods_sub = b.goods_sub and wqty > 0
				 ),0) as wqty,
                                 o.ord_no, o.ord_opt_no
			from goods_estimate a
				left outer join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
				left outer join opt c on b.opt_kind_cd = c.opt_kind_cd and c.opt_id = 'K'
				left outer join brand d on b.brand = d.brand
				left outer join code e on e.code_kind_cd = 'G_GOODS_STAT' and e.code_id = b.sale_stat_cl
                                left outer join order_opt o on o.ord_opt_no = a.ord_opt_no
			where a.no = '$no'
		";

		$row = DB::selectOne($sql);
		if($row){

			$goods_img	= $row->goods_img;
			$opt_kind_nm= $row->opt_kind_nm;
			$brand_nm	= $row->brand_nm;
			$goods_nm	= $row->goods_nm;
			$goods_no	= $row->goods_no;
			$goods_sub	= $row->goods_sub;
			$sale_stat_cl= $row->sale_stat_cl;
			$good_qty	= $row->good_qty;
			$wqty		= $row->wqty;
			$cnt		= $row->cnt;
			$goods_est	= $row->goods_est;
			$goods_title	= $row->goods_title;
			$buy_yn		= $row->buy_yn;
			$best_yn	= $row->best_yn;
            $best_type	= $row->best_type;
			$use_yn		= $row->use_yn;
			$name		= $row->name;
			$user_id	= $row->user_id;
			$regi_date	= $row->regi_date;
			$goods_text	= $row->goods_text;
			$ipaddres	= $row->ipaddres;
			$input_ipaddres	= $row->input_ipaddres;
			$ord_no         = $row->ord_no;
			$ord_opt_no     = $row->ord_opt_no;

			$goods_info = $this->GetGoodsInfo($goods_no, $goods_sub);
		}


		$user_group_nm = "";

        $sql = "
			select a.group_nm from user_group a
				inner join user_group_member b on b.group_no = a.group_no
			where user_id = '$user_id'
		";
        //$rs = $conn->Execute($sql);
		$row = DB::selectOne($sql);
        if($row){
            $user_group_nm = $row->group_nm;
        }

        $sql = "
			select qna_no, subject, ans_msg from qna_ans_type where  kind = 'review' AND use_yn = 'Y'
		";
        $templates = array();
        $result = DB::select($sql);
        $tpl_comment = "";
		
		foreach($result as $row){
			
			$templates[] = array(
				"nm" => $row->qna_no,
				"val" => $row->subject
				);
				/*
			array_push($templates, 
			);
			*/
			if($tpl_comment == "") $tpl_comment = $row->ans_msg;

			//$templates[$row->qna_no] = $row->subject;
		}

		$sql = "
			select *
			from goods_estimate_comment
			where est_no = '$no'
			order by cmt_no asc
		";

        $result = DB::select($sql);
		
		$domain = $conf->getConfig("shop","domain");

		$values = [
			"no"			=> $no
			, "goods_img"		=> $goods_img
			, "opt_kind_nm"		=> $opt_kind_nm
			, "brand_nm"		=> $brand_nm
			, "goods_nm"		=> $goods_nm
			, "goods_no"		=> $goods_no
			, "goods_sub"		=> $goods_sub
			, "sale_stat_cl"	=> $sale_stat_cl
			, "good_qty"		=> $good_qty
			, "wqty"		=> $wqty
			, "cnt"			=> $cnt
			, "goods_est"		=> $goods_est
			, "goods_title"		=> $goods_title
			, "buy_yn"		=> $buy_yn
			, "ord_no"		=> $ord_no
			, "ord_opt_no"		=> $ord_opt_no
			, "best_yn"		=> $best_yn
			, "best_yn"		=> $best_yn
			, "best_type"	=> $best_type
			, "use_yn"		=> $use_yn
			, "name"		=> $name
			, "user_id"		=> $user_id
			, "user_group_nm"	=> $user_group_nm
			, "regi_date"		=> $regi_date
			, "goods_text"		=> $goods_text
			, "ipaddres"		=> $ipaddres
			, "input_ipaddres"	=> $input_ipaddres

			, "goods_info"		=> $goods_info

			, "menu_id"		=> $menu_id

			, "templates"		=> $templates
			, "tpl_comment"		=> $tpl_comment
			, "comment_list"	=> $result
			, "domain"			=> $domain
		];

		return view( Config::get('shop.head.view') . '/member/mem22_show',$values);

	}
	
	public function GetTemplate($no = ''){
		$return_code = 0;

        $sql = "
			select qna_no, subject, ans_msg from qna_ans_type where  qna_no = $no
		";
       $row = DB::selectOne($sql);
	   $ans_msg = $row->ans_msg;
	   if($ans_msg !='' ){
		   	$return_code = 1;
	   }

		return response()->json([
            "code" => 200,
            "return_code" => $return_code,
			"ans_msg" => $ans_msg
        ]);

    }

	public function ChangeUseYn(Request $request) {
		$return_code = 0;
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$conf = new Conf();
		$user_arr = [
			'id'=>$id,
			'name' => $name 
		];


        $est_no	= $request->input("est_no");
        $use_yn	= $request->input("use_yn");

        $ip		= $_SERVER["REMOTE_ADDR"];

		$update_items = [
			"use_yn" => $use_yn
		];
		
		try {
			DB::table('goods_estimate')
			->where('no','=', $est_no)
			->update($update_items);

			$return_code	= 1;
		} 
		catch(Exception $e)
		{
			$return_code	= 0;
		}


		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);

    }

	public function ChangeBestYn(Request $request) {
		$return_code = 0;
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$conf = new Conf();
		$user_arr = [
			'id'=>$id,
			'name' => $name 
		];


        $est_no		= $request->input("est_no");
        $best_yn	= $request->input("best_yn");

        $ip			= $_SERVER["REMOTE_ADDR"];

		$update_items = [
			"best_yn" => $best_yn
		];
		
		try {
			DB::table('goods_estimate')
			->where('no','=', $est_no)
			->update($update_items);

			$return_code	= 1;
		} 
		catch(Exception $e)
		{
			$return_code	= 0;
		}


		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);

    }

	public function ChangeBestType(Request $request) {
		$return_code = 0;
		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;

		$conf = new Conf();
		$user_arr = [
			'id'=>$id,
			'name' => $name 
		];


        $est_no			= $request->input("est_no");
        $best_type		= $request->input("best_type");

        $ip				= $_SERVER["REMOTE_ADDR"];

        $sql = "
			select best_yn from goods_estimate where no = $est_no
		";
        
        $rows = DB::selectOne($sql);
        $best_yn = $rows->best_yn;

        if($best_yn == "Y"){
            $sql = "
				update goods_estimate set best_type = ? where no = ? and best_yn = 'Y'
			";

			$update_items = [
				"best_type" => $best_type
			];
			
			try {
                DB::table('goods_estimate')
                ->where('no','=', $est_no)
                ->update($update_items);
                $return_code = 1;
            } catch(Exception $e){
                $return_code = 0;
            }


		} else {
        	$return_code = "-1";
		}

		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);

    }
	
	public function delete(Request $request){
		$data			= $request->input("data");
		
		$del_result1 = array();
		$del_result2 = array();
		$del_result3 = array();
		$del_result4 = array();

		if(!is_array($data)){
			$data = (array)$data;
		}
		$return_code =  "1";

		for($i = 0; $i < count($data); $i++){
			//DB::table('faq')->where('no', $idx)->delete();
			/* 상품평 삭제*/
			
			try {
                DB::table('goods_estimate')
                ->where('no', $data[$i])
                ->delete();
                $del_result1[$i] = 1;
            } catch(Exception $e){
                $del_result1[$i] = 0;
            }


			/* 상품평 베스트 삭제 */
			
			try {
                DB::table('goods_estimate_best')
                ->where('estimate_no', $data[$i])
                ->delete();
                $del_result2[$i] = 1;
            } catch(Exception $e){
                $del_result2[$i] = 0;
            }

			/* 상품평 댓글 삭제 */
			
			try {
                DB::table('goods_estimate_comment')
                ->where('est_no', $data[$i])
                ->delete();
                $del_result3[$i] = 1;
            } catch(Exception $e){
                $del_result3[$i] = 0;
            }

			/* 상품평 이미지 삭제 */
			
			try {
                DB::table('goods_estimate_image')
                ->where('est_no', $data[$i])
                ->delete();
                $del_result4[$i] = 1;
            } catch(Exception $e){
                $del_result4[$i] = 0;
            }

			
		}

		if(in_array(0, $del_result1)){
			$return_code =  "0";
		}

		if(in_array(0, $del_result2)){
			$return_code =  "-1";
		}

		if(in_array(0, $del_result3)){
			$return_code =  "-2";
		}

		if(in_array(0, $del_result4)){
			$return_code =  "-4";
		}

		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);

	}

	public function ChangeUse(Request $request){
		$data		= $request->input("data");
		$use_yn	= $request->input("use_yn");
		$return_codes = array();
		$return_code = 1;

		for($i = 0; $i < count($data); $i++){
			$sql = "
				update goods_estimate set use_yn = '$use_yn' where no = '$data[$i]'
			";
			$update_items = [
				"use_yn" => $use_yn
				];

			try {
				DB::table('goods_estimate')
				->where('no','=', $data[$i])
				->update($update_items);
				$return_codes[$i] = 1;
			} catch(Exception $e){
				$return_codes[$i] = -1;
			}
			/*
			echo $sql;
			echo "<br>";
			*/
			//$conn->Execute($sql);
		}

		if(in_array(0, $return_codes)){
			$return_code = 0;
		}

		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
	}

	public function addComment(Request $request){
		$goods_no		= $request->input('goods_no');
		$goods_sub		= $request->input('goods_sub');
		$est_no			= $request->input("no");
		$comment		= $request->input("comment");
		$ip				= $_SERVER["REMOTE_ADDR"];

		$id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

		$return_code = 0;

		//댓글 insert
		$sql = "
			insert into goods_estimate_comment(
				est_no, user_nm, user_id, comment, show_yn, ip, rt
			)values(
				'$est_no', '$name', '$id', '$comment', 'Y', '$ip', now()
			)
		";
		
		try {
			DB::insert($sql);
			$return_code = 1;
		} catch(Exception $e){
			$return_code = 0;
		};

		$sql = "
			update goods_estimate set
				comment_cnt = comment_cnt + 1
			where no = '$est_no'
		";
		$update_items = [
			"comment_cnt" => "comment_cnt + 1"
			];

		try {
			DB::table('goods_estimate')
			->where('no','=', $est_no)
			->update($update_items);
			$return_code = 1;
		} catch(Exception $e){
			$return_code = -1;
		}
			
		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
	}


	public function DelComment($cmt_no = ''){
		$comment_del_code = 0;
		$sql = "
			select est_no from goods_estimate_comment
			where cmt_no = '$cmt_no'
		";
		$rs = DB::selectOne($sql);
		$est_no = $rs->est_no;

		$sql = "
			delete from goods_estimate_comment
			where cmt_no = '$cmt_no'
		";
		try {
			DB::table('goods_estimate_comment')
			->where('cmt_no', $cmt_no)
			->delete();
			$comment_del_code = 1;
		} catch(Exception $e){
			$comment_del_code = 0;
		}

		
		$sql = "
			update goods_estimate set
				comment_cnt = if(comment_cnt = 0, 0, comment_cnt - 1)
			where no = '$est_no'
		";
		try {
			DB::update($sql);
			$return_code = 1;
		} catch(Exception $e){
			$return_code = -1;
		}

		return response()->json([
            "code" => 200,
            "return_code" => $comment_del_code
        ]);

	}

	public function GetComment($no = ''){

		$sql = "
			select *
			from goods_estimate_comment
			where est_no = '$no'
			order by cmt_no asc
		";

        $result = DB::select($sql);
		$data = array();
		//echo "<br>";
		/*
        foreach($rs as $row){
			//array_push($data, )
			$data[] = $row;
            //$row["comment"] = nl2br($row->comment);
        }
		*/
		//print_r($data);

		return response()->json([
            "code" => 200,
			"result" => $result
        ]);
	}



	/*
		Function: GetGoodsInfo
		상품평 해당 상품의 정보 얻기
	*/

	function GetGoodsInfo($goods_no, $goods_sub){

		$data = array();

		$sql = "
			select count(*) as cnt, avg(goods_est) as goods_est_avg
			from goods_estimate
			where goods_no = '$goods_no' and goods_sub = '$goods_sub'
			group by goods_no, goods_sub
		";
		//$rs = $conn->Execute($sql);
		$row = DB::selectOne($sql);
		if($row){
			$data = array(
				"goods_est_cnt" => $row->cnt
				, "goods_est_avg" => round($row->goods_est_avg, 1)
			);
		}

		return $data;
	}


	function createAddress()
	{
		$ip[] = array("start"=>"210.181.1.xxx","end"=>"210.181.31.xxx");
		$ip[] = array("start"=>"211.39.224.xxx","end"=>"211.39.255.xxx");
		$ip[] = array("start"=>"211.45.64.xxx","end"=>"211.45.95.xxx");
		$ip[] = array("start"=>"211.56.128.xxx","end"=>"211.56.191.xxx");
		$ip[] = array("start"=>"211.61.128.xxx","end"=>"211.61.255.xxx");
		$ip[] = array("start"=>"211.111.0.xxx","end"=>"211.111.127.xxx");
		$ip[] = array("start"=>"211.175.0.xxx","end"=>"211.175.255.xxx");
		$ip[] = array("start"=>"211.183.0.xxx","end"=>"211.183.255.xxx");
		$ip[] = array("start"=>"211.242.0.xxx","end"=>"211.242.63.xxx");
		$ip[] = array("start"=>"211.242.64.xxx","end"=>"211.242.127.xxx");
		$ip[] = array("start"=>"211.242.128.xxx","end"=>"211.242.191.xxx");
		$ip[] = array("start"=>"61.96.0.xxx","end"=>"61.96.127.xxx");
		$ip[] = array("start"=>"211.249.0.xxx","end"=>"211.249.255.xxx");
		$ip[] = array("start"=>"61.96.128.xxx","end"=>"61.96.255.xxx");
		$ip[] = array("start"=>"61.103.0.xxx","end"=>"61.103.255.xxx");
		$ip[] = array("start"=>"61.107.0.xxx","end"=>"61.107.255.xxx");
		$ip[] = array("start"=>"220.64.0.xxx","end"=>"220.64.255.xxx");
		$ip[] = array("start"=>"211.242.192.xxx","end"=>"211.242.255.xxx");
		$ip[] = array("start"=>"211.247.128.xxx","end"=>"211.247.255.xxx");
		$ip[] = array("start"=>"59.150.0.xxx","end"=>"59.150.255.xxx");
		$ip[] = array("start"=>"210.181.0.xxx","end"=>"210.181.3.xxx");
		$ip[] = array("start"=>"210.181.4.xxx","end"=>"210.181.11.xxx");
		$ip[] = array("start"=>"210.181.12.xxx","end"=>"210.181.31.xxx");
		$ip[] = array("start"=>"211.39.224.xxx","end"=>"211.39.255.xxx");
		$ip[] = array("start"=>"211.45.64.xxx","end"=>"211.45.95.xxx");
		$ip[] = array("start"=>"211.56.128.xxx","end"=>"211.56.191.xxx");
		$ip[] = array("start"=>"211.61.128.xxx","end"=>"211.61.255.xxx");
		$ip[] = array("start"=>"211.111.0.xxx","end"=>"211.111.127.xxx");
		$ip[] = array("start"=>"211.175.0.xxx","end"=>"211.175.255.xxx");
		$ip[] = array("start"=>"211.183.0.xxx","end"=>"211.183.255.xxx");
		$ip[] = array("start"=>"211.242.0.xxx","end"=>"211.242.63.xxx");
		$ip[] = array("start"=>"211.242.64.xxx","end"=>"211.242.127.xxx");
		$ip[] = array("start"=>"211.242.128.xxx","end"=>"211.242.191.xxx");
		$ip[] = array("start"=>"61.96.0.xxx","end"=>"61.96.127.xxx");
		$ip[] = array("start"=>"211.249.0.xxx","end"=>"211.249.255.xxx");
		$ip[] = array("start"=>"61.96.128.xxx","end"=>"61.96.255.xxx");
		$ip[] = array("start"=>"61.103.0.xxx","end"=>"61.103.255.xxx");
		$ip[] = array("start"=>"61.107.0.xxx","end"=>"61.107.255.xxx");
		$ip[] = array("start"=>"220.64.0.xxx","end"=>"220.64.255.xxx");
		$ip[] = array("start"=>"211.242.192.xxx","end"=>"211.242.255.xxx");
		$ip[] = array("start"=>"211.247.128.xxx","end"=>"211.247.255.xxx");
		$ip[] = array("start"=>"59.150.0.xxx","end"=>"59.150.255.xxx");
		$ip[] = array("start"=>"211.115.192.xxx","end"=>"211.115.207.xxx");
		$ip[] = array("start"=>"211.115.208.xxx","end"=>"211.115.211.xxx");
		$ip[] = array("start"=>"211.115.212.xxx","end"=>"211.115.223.xxx");
		$ip[] = array("start"=>"211.189.160.xxx","end"=>"211.189.191.xxx");
		$ip[] = array("start"=>"211.239.0.xxx","end"=>"211.239.127.xxx");
		$ip[] = array("start"=>"211.239.128.xxx","end"=>"211.239.191.xxx");
		$ip[] = array("start"=>"61.100.0.xxx","end"=>"61.100.191.xxx");
		$ip[] = array("start"=>"211.239.192.xxx","end"=>"211.239.255.xxx");
		$ip[] = array("start"=>"61.250.64.xxx","end"=>"61.250.95.xxx");
		$ip[] = array("start"=>"61.250.96.xxx","end"=>"61.250.127.xxx");
		$ip[] = array("start"=>"211.236.224.xxx","end"=>"211.236.255.xxx");
		$ip[] = array("start"=>"61.97.64.xxx","end"=>"61.97.79.xxx");
		$ip[] = array("start"=>"61.252.160.xxx","end"=>"61.252.191.xxx");
		$ip[] = array("start"=>"210.98.64.xxx","end"=>"210.98.127.xxx");
		$ip[] = array("start"=>"210.112.0.xxx","end"=>"210.112.127.xxx");
		$ip[] = array("start"=>"210.116.0.xxx","end"=>"210.116.63.xxx");
		$ip[] = array("start"=>"210.116.128.xxx","end"=>"210.116.255.xxx");
		$ip[] = array("start"=>"210.121.0.xxx","end"=>"210.121.127.xxx");
		$ip[] = array("start"=>"210.127.128.xxx","end"=>"210.127.191.xxx");
		$ip[] = array("start"=>"210.127.64.xxx","end"=>"210.127.127.xxx");
		$ip[] = array("start"=>"210.126.128.xxx","end"=>"210.126.255.xxx");
		$ip[] = array("start"=>"210.122.0.xxx","end"=>"210.122.255.xxx");
		$ip[] = array("start"=>"210.109.0.xxx","end"=>"210.109.255.xxx");
		$ip[] = array("start"=>"210.103.128.xxx","end"=>"210.103.255.xxx");
		$ip[] = array("start"=>"203.255.128.xxx","end"=>"203.255.159.xxx");
		$ip[] = array("start"=>"203.255.112.xxx","end"=>"203.255.119.xxx");
		$ip[] = array("start"=>"203.248.0.xxx","end"=>"203.248.127.xxx");
		$ip[] = array("start"=>"203.243.128.xxx","end"=>"203.243.255.xxx");
		$ip[] = array("start"=>"211.116.192.xxx","end"=>"211.116.223.xxx");
		$ip[] = array("start"=>"211.43.160.xxx","end"=>"211.43.191.xxx");
		$ip[] = array("start"=>"203.239.0.xxx","end"=>"203.239.127.xxx");
		$ip[] = array("start"=>"203.238.0.xxx","end"=>"203.238.127.xxx");
		$ip[] = array("start"=>"203.236.128.xxx","end"=>"203.236.191.xxx");
		$ip[] = array("start"=>"203.235.0.xxx","end"=>"203.235.127.xxx");
		$ip[] = array("start"=>"203.231.0.xxx","end"=>"203.231.255.xxx");
		$ip[] = array("start"=>"203.227.0.xxx","end"=>"203.227.255.xxx");
		$ip[] = array("start"=>"61.97.96.xxx","end"=>"61.97.111.xxx");
		$ip[] = array("start"=>"211.36.192.xxx","end"=>"211.36.223.xxx");
		$ip[] = array("start"=>"211.39.160.xxx","end"=>"211.39.191.xxx");
		$ip[] = array("start"=>"211.56.192.xxx","end"=>"211.56.223.xxx");
		$ip[] = array("start"=>"211.111.128.xxx","end"=>"211.111.159.xxx");
		$ip[] = array("start"=>"211.116.176.xxx","end"=>"211.116.191.xxx");
		$ip[] = array("start"=>"211.238.0.xxx","end"=>"211.238.15.xxx");
		$ip[] = array("start"=>"211.255.224.xxx","end"=>"211.255.255.xxx");
		$ip[] = array("start"=>"203.235.128.xxx","end"=>"203.235.191.xxx");
		$ip[] = array("start"=>"61.109.128.xxx","end"=>"61.109.255.xxx");
		$ip[] = array("start"=>"210.94.0.xxx","end"=>"210.94.3.xxx");
		$ip[] = array("start"=>"210.94.4.xxx","end"=>"210.94.11.xxx");
		$ip[] = array("start"=>"210.180.96.xxx","end"=>"210.180.107.xxx");
		$ip[] = array("start"=>"210.94.12.xxx","end"=>"210.94.31.xxx");
		$ip[] = array("start"=>"210.180.108.xxx","end"=>"210.180.127.xxx");
		$ip[] = array("start"=>"210.217.160.xxx","end"=>"210.217.191.xxx");
		$ip[] = array("start"=>"210.220.64.xxx","end"=>"210.220.95.xxx");
		$ip[] = array("start"=>"210.220.160.xxx","end"=>"210.220.191.xxx");
		$ip[] = array("start"=>"210.205.0.xxx","end"=>"210.205.63.xxx");
		$ip[] = array("start"=>"211.37.0.xxx","end"=>"211.37.127.xxx");
		$ip[] = array("start"=>"211.41.96.xxx","end"=>"211.41.127.xxx");
		$ip[] = array("start"=>"211.44.0.xxx","end"=>"211.44.127.xxx");
		$ip[] = array("start"=>"211.44.128.xxx","end"=>"211.44.255.xxx");
		$ip[] = array("start"=>"211.58.0.xxx","end"=>"211.58.255.xxx");
		$ip[] = array("start"=>"211.108.0.xxx","end"=>"211.108.255.xxx");
		$ip[] = array("start"=>"211.117.0.xxx","end"=>"211.117.255.xxx");
		$ip[] = array("start"=>"211.176.0.xxx","end"=>"211.177.255.xxx");
		$ip[] = array("start"=>"211.178.0.xxx","end"=>"211.179.255.xxx");
		$ip[] = array("start"=>"211.200.0.xxx","end"=>"211.205.255.xxx");
		$ip[] = array("start"=>"211.206.0.xxx","end"=>"211.211.255.xxx");
		$ip[] = array("start"=>"211.212.0.xxx","end"=>"211.215.255.xxx");
		$ip[] = array("start"=>"218.48.0.xxx","end"=>"218.49.255.xxx");
		$ip[] = array("start"=>"218.50.0.xxx","end"=>"218.55.255.xxx");
		$ip[] = array("start"=>"218.232.0.xxx","end"=>"218.233.255.xxx");
		$ip[] = array("start"=>"219.240.0.xxx","end"=>"219.241.255.xxx");
		$ip[] = array("start"=>"218.234.0.xxx","end"=>"218.235.255.xxx");
		$ip[] = array("start"=>"218.236.0.xxx","end"=>"218.239.255.xxx");
		$ip[] = array("start"=>"218.38.0.xxx","end"=>"218.39.255.xxx");
		$ip[] = array("start"=>"219.248.0.xxx","end"=>"219.251.255.xxx");
		$ip[] = array("start"=>"221.138.0.xxx","end"=>"221.143.255.xxx");
		$ip[] = array("start"=>"219.254.0.xxx","end"=>"219.255.255.xxx");
		$ip[] = array("start"=>"222.232.0.xxx","end"=>"222.239.255.xxx");
		$ip[] = array("start"=>"203.251.192.xxx","end"=>"203.251.255.xxx");
		$ip[] = array("start"=>"203.240.128.xxx","end"=>"203.240.255.xxx");
		$ip[] = array("start"=>"203.228.128.xxx","end"=>"203.228.255.xxx");
		$ip[] = array("start"=>"210.127.192.xxx","end"=>"210.127.255.xxx");
		$ip[] = array("start"=>"210.118.128.xxx","end"=>"210.118.255.xxx");
		$ip[] = array("start"=>"210.114.128.xxx","end"=>"210.114.255.xxx");
		$ip[] = array("start"=>"210.111.0.xxx","end"=>"210.111.127.xxx");
		$ip[] = array("start"=>"210.101.0.xxx","end"=>"210.101.63.xxx");
		$ip[] = array("start"=>"202.30.128.xxx","end"=>"202.30.255.xxx");
		$ip[] = array("start"=>"211.61.64.xxx","end"=>"211.61.127.xxx");
		$ip[] = array("start"=>"211.113.128.xxx","end"=>"211.113.255.xxx");
		$ip[] = array("start"=>"211.190.0.xxx","end"=>"211.191.255.xxx");
		$ip[] = array("start"=>"61.248.0.xxx","end"=>"61.248.255.xxx");
		$ip[] = array("start"=>"61.110.128.xxx","end"=>"61.111.255.xxx");
		$ip[] = array("start"=>"61.249.0.xxx","end"=>"61.249.255.xxx");
		$ip[] = array("start"=>"61.110.0.xxx","end"=>"61.110.127.xxx");
		$ip[] = array("start"=>"210.117.0.xxx","end"=>"210.117.63.xxx");
		$ip[] = array("start"=>"210.117.64.xxx","end"=>"210.117.127.xxx");
		$ip[] = array("start"=>"210.94.64.xxx","end"=>"210.94.95.xxx");
		$ip[] = array("start"=>"210.94.96.xxx","end"=>"210.94.127.xxx");
		$ip[] = array("start"=>"210.181.96.xxx","end"=>"210.181.127.xxx");
		$ip[] = array("start"=>"210.181.64.xxx","end"=>"210.181.95.xxx");
		$ip[] = array("start"=>"210.219.128.xxx","end"=>"210.219.191.xxx");
		$ip[] = array("start"=>"210.218.128.xxx","end"=>"210.218.191.xxx");
		$ip[] = array("start"=>"210.221.0.xxx","end"=>"210.221.127.xxx");
		$ip[] = array("start"=>"210.205.128.xxx","end"=>"210.205.255.xxx");
		$ip[] = array("start"=>"211.33.0.xxx","end"=>"211.33.127.xxx");
		$ip[] = array("start"=>"211.49.0.xxx","end"=>"211.49.127.xxx");
		$ip[] = array("start"=>"211.49.128.xxx","end"=>"211.49.255.xxx");
		$ip[] = array("start"=>"211.52.128.xxx","end"=>"211.52.255.xxx");
		$ip[] = array("start"=>"211.59.0.xxx","end"=>"211.59.255.xxx");
		$ip[] = array("start"=>"211.110.0.xxx","end"=>"211.110.255.xxx");
		$ip[] = array("start"=>"211.109.0.xxx","end"=>"211.109.255.xxx");
		$ip[] = array("start"=>"211.187.0.xxx","end"=>"211.187.255.xxx");
		$ip[] = array("start"=>"211.186.0.xxx","end"=>"211.186.255.xxx");
		$ip[] = array("start"=>"211.244.0.xxx","end"=>"211.244.255.xxx");
		$ip[] = array("start"=>"211.245.0.xxx","end"=>"211.245.127.xxx");
		$ip[] = array("start"=>"211.243.0.xxx","end"=>"211.243.255.xxx");
		$ip[] = array("start"=>"211.245.128.xxx","end"=>"211.245.255.xxx");
		$ip[] = array("start"=>"61.254.0.xxx","end"=>"61.255.255.xxx");
		$ip[] = array("start"=>"61.98.0.xxx","end"=>"61.98.255.xxx");
		$ip[] = array("start"=>"61.99.0.xxx","end"=>"61.99.255.xxx");
		$ip[] = array("start"=>"61.101.0.xxx","end"=>"61.101.127.xxx");
		$ip[] = array("start"=>"61.101.128.xxx","end"=>"61.101.223.xxx");
		$ip[] = array("start"=>"203.245.0.xxx","end"=>"203.245.15.xxx");
		$ip[] = array("start"=>"203.245.16.xxx","end"=>"203.245.31.xxx");
		$ip[] = array("start"=>"203.245.32.xxx","end"=>"203.245.63.xxx");
		$ip[] = array("start"=>"210.114.0.xxx","end"=>"210.114.63.xxx");
		$ip[] = array("start"=>"210.180.64.xxx","end"=>"210.180.95.xxx");
		$ip[] = array("start"=>"210.220.128.xxx","end"=>"210.220.159.xxx");
		$ip[] = array("start"=>"211.39.192.xxx","end"=>"211.39.223.xxx");
		$ip[] = array("start"=>"211.37.128.xxx","end"=>"211.37.191.xxx");
		$ip[] = array("start"=>"211.41.64.xxx","end"=>"211.41.95.xxx");
		$ip[] = array("start"=>"211.45.128.xxx","end"=>"211.45.191.xxx");
		$ip[] = array("start"=>"211.47.0.xxx","end"=>"211.47.63.xxx");
		$ip[] = array("start"=>"211.42.128.xxx","end"=>"211.42.159.xxx");
		$ip[] = array("start"=>"211.56.64.xxx","end"=>"211.56.127.xxx");
		$ip[] = array("start"=>"211.56.0.xxx","end"=>"211.56.63.xxx");
		$ip[] = array("start"=>"211.62.0.xxx","end"=>"211.62.63.xxx");
		$ip[] = array("start"=>"211.113.0.xxx","end"=>"211.113.127.xxx");
		$ip[] = array("start"=>"211.188.0.xxx","end"=>"211.188.127.xxx");
		$ip[] = array("start"=>"211.41.128.xxx","end"=>"211.41.131.xxx");
		$ip[] = array("start"=>"211.41.132.xxx","end"=>"211.41.135.xxx");
		$ip[] = array("start"=>"211.41.136.xxx","end"=>"211.41.143.xxx");
		$ip[] = array("start"=>"211.41.144.xxx","end"=>"211.41.159.xxx");
		$ip[] = array("start"=>"211.189.224.xxx","end"=>"211.189.255.xxx");
		$ip[] = array("start"=>"211.237.96.xxx","end"=>"211.237.111.xxx");
		$ip[] = array("start"=>"61.251.224.xxx","end"=>"61.251.255.xxx");
		$ip[] = array("start"=>"61.102.128.xxx","end"=>"61.102.223.xxx");
		$ip[] = array("start"=>"61.102.224.xxx","end"=>"61.102.255.xxx");
		$ip[] = array("start"=>"61.251.192.xxx","end"=>"61.251.223.xxx");
		$ip[] = array("start"=>"203.229.64.xxx","end"=>"203.229.127.xxx");
		$ip[] = array("start"=>"203.229.0.xxx","end"=>"203.229.63.xxx");
		$ip[] = array("start"=>"61.97.32.xxx","end"=>"61.97.47.xxx");
		$ip[] = array("start"=>"61.97.48.xxx","end"=>"61.97.63.xxx");
		$ip[] = array("start"=>"211.172.144.xxx","end"=>"211.172.159.xxx");
		$ip[] = array("start"=>"211.36.128.xxx","end"=>"211.36.159.xxx");
		$ip[] = array("start"=>"211.232.192.xxx","end"=>"211.232.239.xxx");
		$ip[] = array("start"=>"211.116.64.xxx","end"=>"211.116.127.xxx");
		$ip[] = array("start"=>"211.232.240.xxx","end"=>"211.232.255.xxx");
		$ip[] = array("start"=>"211.255.208.xxx","end"=>"211.255.223.xxx");
		$ip[] = array("start"=>"211.237.160.xxx","end"=>"211.237.191.xxx");
		$ip[] = array("start"=>"211.36.160.xxx","end"=>"211.36.163.xxx");
		$ip[] = array("start"=>"211.36.164.xxx","end"=>"211.36.171.xxx");
		$ip[] = array("start"=>"211.36.172.xxx","end"=>"211.36.175.xxx");
		$ip[] = array("start"=>"211.36.176.xxx","end"=>"211.36.183.xxx");
		$ip[] = array("start"=>"211.36.184.xxx","end"=>"211.36.191.xxx");
		$ip[] = array("start"=>"211.237.224.xxx","end"=>"211.237.239.xxx");
		$ip[] = array("start"=>"211.237.112.xxx","end"=>"211.237.127.xxx");
		$ip[] = array("start"=>"61.252.96.xxx","end"=>"61.252.111.xxx");
		$ip[] = array("start"=>"61.252.112.xxx","end"=>"61.252.127.xxx");
		$ip[] = array("start"=>"211.172.0.xxx","end"=>"211.172.31.xxx");
		$ip[] = array("start"=>"211.255.160.xxx","end"=>"211.255.191.xxx");
		$ip[] = array("start"=>"210.106.192.xxx","end"=>"210.106.223.xxx");
		$ip[] = array("start"=>"61.97.224.xxx","end"=>"61.97.239.xxx");
		$ip[] = array("start"=>"211.111.224.xxx","end"=>"211.111.255.xxx");
		$ip[] = array("start"=>"211.112.96.xxx","end"=>"211.112.127.xxx");
		$ip[] = array("start"=>"210.97.160.xxx","end"=>"210.97.191.xxx");
		$ip[] = array("start"=>"203.81.128.xxx","end"=>"203.81.159.xxx");
		$ip[] = array("start"=>"61.97.192.xxx","end"=>"61.97.207.xxx");
		$ip[] = array("start"=>"61.102.0.xxx","end"=>"61.102.95.xxx");
		$ip[] = array("start"=>"210.106.32.xxx","end"=>"210.106.63.xxx");
		$ip[] = array("start"=>"61.97.208.xxx","end"=>"61.97.223.xxx");
		$ip[] = array("start"=>"61.102.96.xxx","end"=>"61.102.127.xxx");
		$ip[] = array("start"=>"61.106.80.xxx","end"=>"61.106.127.xxx");
		$ip[] = array("start"=>"210.106.0.xxx","end"=>"210.106.31.xxx");
		$ip[] = array("start"=>"218.37.128.xxx","end"=>"218.37.191.xxx");
		$ip[] = array("start"=>"211.235.32.xxx","end"=>"211.235.63.xxx");
		$ip[] = array("start"=>"211.112.64.xxx","end"=>"211.112.95.xxx");
		$ip[] = array("start"=>"211.238.64.xxx","end"=>"211.238.95.xxx");
		$ip[] = array("start"=>"210.111.160.xxx","end"=>"210.111.191.xxx");
		$ip[] = array("start"=>"211.47.96.xxx","end"=>"211.47.99.xxx");
		$ip[] = array("start"=>"211.47.100.xxx","end"=>"211.47.107.xxx");
		$ip[] = array("start"=>"211.47.108.xxx","end"=>"211.47.127.xxx");
		$ip[] = array("start"=>"211.47.80.xxx","end"=>"211.47.95.xxx");
		$ip[] = array("start"=>"211.237.240.xxx","end"=>"211.237.255.xxx");
		$ip[] = array("start"=>"211.41.192.xxx","end"=>"211.41.207.xxx");
		$ip[] = array("start"=>"211.172.208.xxx","end"=>"211.172.223.xxx");
		$ip[] = array("start"=>"211.237.208.xxx","end"=>"211.237.223.xxx");
		$ip[] = array("start"=>"211.41.208.xxx","end"=>"211.41.223.xxx");
		$ip[] = array("start"=>"61.106.64.xxx","end"=>"61.106.79.xxx");
		$ip[] = array("start"=>"211.41.224.xxx","end"=>"211.41.255.xxx");
		$ip[] = array("start"=>"211.174.96.xxx","end"=>"211.174.127.xxx");
		$ip[] = array("start"=>"211.255.128.xxx","end"=>"211.255.159.xxx");
		$ip[] = array("start"=>"211.233.128.xxx","end"=>"211.233.255.xxx");
		$ip[] = array("start"=>"211.115.32.xxx","end"=>"211.115.63.xxx");
		$ip[] = array("start"=>"211.172.128.xxx","end"=>"211.172.143.xxx");
		$ip[] = array("start"=>"211.236.192.xxx","end"=>"211.236.223.xxx");
		$ip[] = array("start"=>"211.189.192.xxx","end"=>"211.189.223.xxx");
		$ip[] = array("start"=>"211.173.160.xxx","end"=>"211.173.191.xxx");
		$ip[] = array("start"=>"211.236.128.xxx","end"=>"211.236.159.xxx");
		$ip[] = array("start"=>"211.174.0.xxx","end"=>"211.174.15.xxx");
		$ip[] = array("start"=>"211.172.64.xxx","end"=>"211.172.79.xxx");
		$ip[] = array("start"=>"211.172.32.xxx","end"=>"211.172.63.xxx");
		$ip[] = array("start"=>"211.173.128.xxx","end"=>"211.173.159.xxx");
		$ip[] = array("start"=>"211.115.224.xxx","end"=>"211.115.255.xxx");
		$ip[] = array("start"=>"61.252.192.xxx","end"=>"61.252.255.xxx");

		$cnt = count($ip);

		$range = $ip[rand(0,$cnt-1)];
		$start_range = $range["start"];
		$end_range = $range["end"];

		$start = explode(".",$start_range);
		$end = explode(".",$end_range);

		// 랜덤으로 IP 대역을 생성
		$rand_ip = rand($start[2],$end[2]);

		return $start[0].".".$start[1].".".$rand_ip.".000";
	}


}
