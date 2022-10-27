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

				//상품코드 존재 유무
				$sql	= " select count(*) as tot from product_code where prd_cd = :prd_cd ";








				// 비밀번호 암호화
				$conf = new Conf();
				$encrypt_mode = $conf->getConfigValue("shop", "encrypt_mode");
				$encrypt_key = "";
				if ($encrypt_mode == "mhash") {
					$encrypt_key = $conf->getConfigValue("shop", "encrypt_key");
				}

				$enc_pwd = Lib::get_enc_hash($user_pw, $encrypt_mode, $encrypt_key);

				//고객 등급 매치
				switch ($group_code) {
					case '02':
						$group_no = "15"; break;
					case '03':
						$group_no = "16"; break;
					case '04':
						$group_no = "17"; break;
					default:
						$group_no = "13";
				}

				//고객 성별 매치
				if( $sex == '남' )		$sex = "M";
				else if( $sex == '여' )	$sex = "F";
				else					$sex = "";


				$rmobile	= strrev($mobile);
				$email_chk	= "N";

				//매장코드 생성
				$store_nm_l	= strpos($store_nm, "(");
				if($store_nm_l){
					$store_nm_org	= substr($store_nm, 0, $store_nm_l);
				}else{
					$store_nm_org	= $store_nm;
				}

				$store_cd	= "";
				$sql	= " select store_cd from store where store_nm = :store_nm ";
				//$store	= DB::selectOne($sql, ['store_nm' => $store_nm_org]);
				$store	= DB::selectOne($sql, ['store_nm' => $store_nm]);

				if($store != null)	$store_cd	= $store->store_cd;

				//생년월일
				$mm	= "";
				$dd	= "";
				if( $birth_date != "" ){
					$birth_date	= explode("-", $birth_date);

					$mm	= $birth_date[0];
					$dd	= $birth_date[1];
				}

				$where	= [
					'user_id'	=> $user_id
				];

				$values	= [
					'user_pw'	=> $enc_pwd,
					'name'		=> $name,
					'sex'		=> $sex,
					'email'		=> $email,
					'email_chk'	=> $email_chk,
					'zip'		=> $zip,
					'addr'		=> $addr,
					'addr2'		=> $addr2,
					'phone'		=> $mobile,
					'mobile'	=> $mobile,
					'rmobile'	=> $rmobile,
					'regdate'	=> $regdate,
					'point'		=> $point,
					'yn'		=> 'Y',
					'mm'		=> $mm,
					'dd'		=> $dd,
					'out_yn'	=> 'N',
					'memo'		=> $memo,
					'pwd_reset_yn'	=> 'N',
					'auth_type'	=> 'A',
					'auth_yn'	=> 'N',
					'site'		=> 'HEAD_OFFICE',
					'type'		=> 'B',
					'store_nm'	=> $store_nm,
					'store_cd'	=> $store_cd
				];

				//회원처리
				DB::table('member')->updateOrInsert($where, $values);


				//적립금 처리
				if($point > 0){
					$point_values	= [
						'ord_no'		=> '',
						'ord_opt_no'	=> '',
						'point_nm'		=> '기존 시스템 포인트 등록',
						'point'			=> $point,
						'admin_id'		=> 'system',
						'admin_nm'		=> '시스템',
						'regi_date'		=> now(),
						'point_st'		=> '적립',
						'point_kind'	=> '12',
						'point_status'	=> 'Y',
						'point_date'	=> now()
					];
	
					DB::table('point_list')->updateOrInsert($where, $point_values);
				}


				//member_group 처리
				$group_values	= [
					'group_no'	=> $group_no,
					'rt'		=> now(),
					'ut'		=> now()
				];

				DB::table('user_group_member')->updateOrInsert($where, $group_values);


				//member_stat 처리
				$stat_values	= [
					'ord_cnt'	=> $ord_cnt,
					'ord_amt'	=> $ord_amt,
					'ord_date'	=> $last_ord_date,
					'rt'		=> now(),
					'ut'		=> now()
				];

				DB::table('member_stat')->updateOrInsert($where, $stat_values);
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