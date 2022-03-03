<?php

namespace App\Http\Controllers\head\promotion;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\Conf;
use App\Models\Gift;
use Facade\FlareClient\Stacktrace\File;

class prm06Controller extends Controller
{
	public function index() {
		$gift_kinds = SLib::getCodes("G_GIFT_KIND");
		  
		$values = [
			"gift_kinds" => $gift_kinds
		];

		return view( Config::get('shop.head.view') . '/promotion/prm06',$values);
	}


	public function search(Request $request){
		$gift_nm			= $request->input("gift_nm");
		//$gift_type		= request("gift_type");
		$gift_kind			= $request->input("gift_kind");

		$use_yn				= $request->input("use_yn");
		$style_no			= $request->input("style_no");
		$goods_no			= $request->input("goods_no");
		$goods_nm			= $request->input("goods_nm");

		$fr_apply_amt		= $request->input("fr_apply_amt");
		$to_apply_amt		= $request->input("to_apply_amt");
		$fr_apply_date		= $request->input("fr_apply_date");
		$to_apply_date		= $request->input("to_apply_date");
		$limit				= $request->input("limit");

		$where = "";
		$join = "";
		$join_where = "";

		if($gift_nm != "") $where .= "and a.name LIKE '%".$gift_nm."%'";
		//if($S_GIFT_TYPE != "") $where .= "and a.type='".$S_GIFT_TYPE."'";
		if($gift_kind != "") $where .= "and a.kind='".$gift_kind."'";
		if($use_yn != "") $where .= "and a.use_yn='".$use_yn."'";

		if($style_no != "" || $goods_no != "" || $goods_nm != "")
		{
			if ($style_no != "") $join_where .= " and g.style_no = '".$style_no."' ";
			if ($goods_no != "") $join_where .= " and g.goods_no = '".$goods_no."' ";
			if ($goods_nm != "") $join_where .= " and g.goods_nm LIKE '".$goods_nm."%' ";
			$join = "
				inner join (
					select
						distinct t.gift_no
					from (
						select gg.gift_no
						from gift_goods gg
							inner join goods g on gg.goods_no = g.goods_no and gg.goods_sub = g.goods_sub
						where 1=1 $join_where
					) as t
				) as b on a.no = b.gift_no
			";
		}

		if($fr_apply_amt != "")	$where .= " and a.apply_amt>='".$fr_apply_amt."' ";
		if($to_apply_amt != "")	$where .= " and a.apply_amt<='".$to_apply_amt."' ";
		if($fr_apply_date != "")	$where .= " and a.fr_date >= '$fr_apply_date' ";
		if($to_apply_date != "")	$where .= " and a.to_date < date_format(DATE_ADD('$to_apply_date', INTERVAL 1 DAY),'%Y%m%d') ";

		$page = request("page",1);
		if ($page < 1 or $page == "") $page = 1;

		$limit				= $request->input("limit",100);
		$ord_field			= $request->input("ord_field","a.no");
		$ord				= $request->input("ord","asc");

		$page_size = $limit;
		$str_order_by = sprintf(" order by %s %s ",$ord_field,$ord);

		$sql = "
			select
				count(*) as cnt
			from gift a
				$join
			where 1=1 $where
		";
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}
		$wheres = " where 1=1 ".$where;

		$sql = "
			select
				'' as chk, a.no, a.name, cd2.code_val as kind, a.fr_date, a.to_date,
				cd3.code_val as apply_product,
				(
					select count(*)
					from gift_ex_goods
					where gift_no = a.no
				) as ext_godos,
				ifnull(a.gift_price,0) as gift_price,a.apply_amt,
				if(a.unlimited_yn = 'Y', '무제한', a.qty) as qty,
				(
					select count(*)
					from order_gift
					where gift_no = a.no
				) as ord_qty, a.dp_soldout_yn,
				if(a.refund_yn = 'N', ' 환불안함', '환불함') as refund_yn,
				a.use_yn, a.admin_nm, a.rt, a.ut, a.memo
			from gift a
				$join
				left outer join code cd2 on cd2.code_kind_cd = 'G_GIFT_KIND' and cd2.code_id = a.kind
				left outer join code cd3 on cd3.code_kind_cd = 'G_GIFT_APPLY' and cd3.code_id = a.apply_product
			where 1=1 $where
			$str_order_by
			limit $startno, $page_size
		";


		$result = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => 1,
                "page_cnt" => count($result),
                "page_total" => 1
            ),
            "body" => $result
        ]);
	}

	public function create(){
		$conf = new Conf();
		$cfg_domain_bizest	= $conf->getConfigValue("shop","domain_bizest");
		$img_url = sprintf("//%s",$cfg_domain_bizest);

		$sql = "
			select group_no as id, group_nm as val
			from user_group order by group_no
		";
		$gift_group_nos = DB::select($sql);

		$sql = "
			select com_id, com_nm
			from company
			where com_id = 'SPL_BONSA' or com_type = '2'
			order by com_type
		";

		$apply_com = DB::select($sql);


		$gift_info = new \stdClass();
        $gift_info->gift_no = '';
        $gift_info->name = '';
        $gift_info->kind = '';
        $gift_info->fr_date = '';
		$gift_info->to_date = '';
        $gift_info->apply_amt = '';
        $gift_info->gift_price = '';
		$gift_info->qty = '';
		$gift_info->unlimited_yn = '';
		$gift_info->dp_soldout_yn = '';
		$gift_info->img = '';
		$gift_info->img_attr = '';
		$gift_info->contents = '';
		$gift_info->memo = '';
		$gift_info->apply_com = '';
		$gift_info->apply_group = '';
		$gift_info->refund_yn = '';
		$gift_info->use_yn = '';
		$gift_info->apply_product = '';
		$gift_info->gift_group_nos = '';
		$gift_info->rt = '';
		$gift_info->ut = '';

		$values = [
			'gift_group_nos'	=> $gift_group_nos,
			'apply_coms'		=> $apply_com,
			'gift_info'			=> $gift_info,
			'cmd'				=> 'addcmd',
			'gift_no'			=> '',
			'cfg_domain_bizest'	=> $cfg_domain_bizest
		];
		return view( Config::get('shop.head.view') . '/promotion/prm06_show',$values);
	}


	public function show($gift_no = ''){
		$conf = new Conf();
		$cfg_domain_bizest	= $conf->getConfigValue("shop","domain_bizest");
		$img_url = sprintf("//%s",$cfg_domain_bizest);
		$files = [];

		$sql = "
			select group_no as id, group_nm as val
			from user_group order by group_no
		";
		$gift_group_nos = DB::select($sql);

		$sql = "
			select com_id, com_nm
			from company
			where com_id = 'SPL_BONSA' or com_type = '2'
			order by com_type
		";

		$apply_com = DB::select($sql);

		$sql = "
				select name, kind, fr_date, to_date, apply_amt, gift_price, qty, unlimited_yn, dp_soldout_yn, img, contents, memo, use_yn, apply_product, apply_com, apply_group, refund_yn, admin_id, admin_nm, rt, ut
				from gift a
				where a.no = '$gift_no'
			";
		$gift_info = DB::selectOne($sql);

		$values = [
			'gift_group_nos'	=> $gift_group_nos,
			'apply_coms'		=> $apply_com,
			'gift_info'			=> $gift_info,
			'cmd'				=> 'editcmd',
			'gift_no'			=> $gift_no,
			'cfg_domain_bizest'	=> $cfg_domain_bizest
		];
		return view( Config::get('shop.head.view') . '/promotion/prm06_show', $values);
	}

	public function command(Request $request) {

		$cmd				= $request->input("cmd");
		$data				= $request->input("data");
		$gift_no			= $request->input("gift_no");
		$gift_name			= $request->input("name");
		//$gift_type		= $request->input("type");
		$gift_kind			= $request->input("kind");
		$fr_date			= $request->input("fr_date");
		$to_date			= $request->input("to_date");
		$apply_amt			= $request->input("apply_amt");
		$gift_price			= $request->input("gift_price");
		$qty				= $request->input("qty");
		$unlimited_yn		= $request->input("unlimited_yn", "N");
		$dp_soldout_yn		= $request->input("dp_soldout_yn", "N");
		
		$contents			= $request->input("contents");
		$memo				= $request->input("memo");
		$apply_com			= $request->input("apply_com");
		$refund_yn			= $request->input("refund_yn");
		$use_yn				= $request->input("use_yn");
		$apply_product		= $request->input("apply_product");
		$goods				= $request->input("goods");
		$ex_goods			= $request->input("ex_goods");
		$apply_group		= $request->input("in_group_nos");

		$fr_date = str_replace("-","", $fr_date);
		$to_date = str_replace("-","", $to_date);

		$return_code = 0;

		$gift_file	= $request->file("file");
		
		$base_path = "/images/gift";

		$id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

		/* 이미지를 저장할 경로 폴더가 없다면 생성 */
		
		if(!Storage::disk('public')->exists($base_path)){
			Storage::disk('public')->makeDirectory($base_path);
		}
		
		$file_path = "";
		if($gift_file != null &&  $gift_file != ""){
			$file_path = Storage::disk('public')->put($base_path, $gift_file);
		}

		//Gift Class
		$gift = new Gift();
		
		if($cmd == "addcmd"){
			/*
			$data = array(
				"gift_nm"			=> $gift_name,
				//"gift_type"			=> $gift_type,
				"gift_kind"			=> $gift_kind,
				"fr_date"			=> $fr_date,
				"to_date"			=> $to_date,
				"apply_amt"			=> $apply_amt,
				"gift_price"		=> $gift_price,
				"qty"				=> $qty,
				"unlimited_yn"		=> $unlimited_yn,
				"dp_soldout_yn"		=> $dp_soldout_yn,
				"img"				=> $img_url,
				"contents"			=> $contents,
				"memo"				=> $memo,
				"refund_yn"			=> $refund_yn,
				"use_yn"			=> $use_yn,
				"apply_product"		=> $apply_product,
				"apply_com"			=> $apply_com,
				"apply_group"		=> $apply_group,
				"admin_id"			=> $id,
				"admin_nm"			=> $name
			);
			*/
			$data = new \stdClass();
			$data->gift_nm			= $gift_name;
			//"gift_type"				=> $gift_type,
			$data->gift_kind		= $gift_kind;
			$data->fr_date			= $fr_date;
			$data->to_date			= $to_date;
			$data->apply_amt		= $apply_amt;
			$data->gift_price		= $gift_price;
			$data->qty				= $qty;
			$data->unlimited_yn		= $unlimited_yn;
			$data->dp_soldout_yn	= $dp_soldout_yn;
			$data->img				= $file_path;
			$data->contents			= $contents;
			$data->memo				= $memo;
			$data->refund_yn		= $refund_yn;
			$data->use_yn			= $use_yn;
			$data->apply_product	= $apply_product;
			$data->apply_com		= $apply_com;
			$data->apply_group		= $apply_group;
			$data->admin_id			= $id;
			$data->admin_nm			= $name;

			$gift_no = $gift->SetGiftInfo($data);
		}else if($cmd == "editcmd"){

			$data = new \stdClass();
			$data->gift_no			= $gift_no;
			$data->gift_nm			= $gift_name;
			//"gift_type"				=> $gift_type,
			$data->gift_kind		= $gift_kind;
			$data->fr_date			= $fr_date;
			$data->to_date			= $to_date;
			$data->apply_amt		= $apply_amt;
			$data->gift_price		= $gift_price;
			$data->qty				= $qty;
			$data->unlimited_yn		= $unlimited_yn;
			$data->dp_soldout_yn	= $dp_soldout_yn;
			$data->img				= $file_path;
			$data->contents			= $contents;
			$data->memo				= $memo;
			$data->refund_yn		= $refund_yn;
			$data->use_yn			= $use_yn;
			$data->apply_product	= $apply_product;
			$data->apply_com		= $apply_com;
			$data->apply_group		= $apply_group;
			$data->admin_id			= $id;
			$data->admin_nm			= $name;

			$gift_no = $gift->ModGiftInfo($data);

		}

		$goods = @explode("^", $goods);
		$ex_goods = @explode("^", $ex_goods);

		//기존에 등록되어 있는 상품 삭제 후 등록

		if($gift_no){
			try {
				$gift->DelGoods($gift_no);
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
			}

			if($return_code == 1){
				try {
					$gift->DelExGoods($gift_no);
					$return_code = 1;
				} catch(Exception $e){
					$return_code = -1;
				}

				if($return_code == 1){
					try {
						if($apply_product == "SG") {	// 일부상품일 경우
							$gift->SetGoods($gift_no, $goods);
						}else{
							$gift->SetExGoods($gift_no, $ex_goods);
						}
						$return_code = 1;
					} catch(Exception $e){
						$return_code = -2;
					}
				}
			}
		}
		
		return response()->json([
			"code" => $return_code,
			"gift_no"	=> $gift_no
		]);

	}

	public function delGift(Request $request){
		$date = $request->input('data');
		$return_codes = array();
		$return_code  = 0;

		//Gift Class
		$gift = new Gift();
		
		//print_r($date );

		for($i = 0; $i < count($date); $i++){
			if(isset($date[$i])) {
				$gift_no = $date[$i];
				// 사은품 관련 정보 삭제
				$return_codes[$i] = 0;
				try {
					$gift->DelGoods($gift_no);
					$return_codes[$i] = 1;
				}catch(Exception $e){
					$return_codes[$i] = 0;
				}
				
				if($return_codes[$i] == 1){
					try {
						$gift->DelExGoods($gift_no);
						$return_codes[$i] = 1;
					}catch(Exception $e){
						$return_codes[$i] = -1;
					}
				}

				if($return_codes[$i] == 1){
				// 사은품 기본정보 삭제
				
					try {
						$gift->DelGiftInfo($gift_no);
						$return_codes[$i] = 1;
					}catch(Exception $e){
						$return_codes[$i] = -2;
					}
				}

				if($return_codes[$i]<1)break;
			}
		}

		if(count($return_codes) == count($date)){
			$return_code = 1;
		}else{
			$return_code = 0;
		}
		return response()->json([
			"code" => $return_code
		]);

	}

	/*
		Function: getGoods
		사은품 적용 상품
	*/
	public function getGoods($gift_no = '', Request $request)
	{
		$gift_no = $request->input("gift_no");
		$page	= $request->input("page");

		$startNo = 0;
		$lmit = 50;
		if($page>1){
			$startNo = ($page-1)*50;
		}

		$sql = "select count(a.goods_no) as total
			from
				gift_goods as a
				inner join goods as b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
				left outer join code as c on code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = c.code_id
				left outer join brand as d on d.brand = b.brand
				left outer join company e on b.com_id = e.com_id
			where
				a.gift_no = '".$gift_no."'";
		$row = DB::selectOne($sql);
		$total = $row->total;

		$page_cnt=(int)(($total-1)/$lmit) + 1;
		
		$sql = "
			select
				'' as chk,
				a.goods_no, a.goods_sub, b.style_no, e.com_nm, d.brand_nm, b.goods_nm, c.code_val, a.rt, b.com_id
			from
				gift_goods as a
				inner join goods as b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
				left outer join code as c on code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = c.code_id
				left outer join brand as d on d.brand = b.brand
				left outer join company e on b.com_id = e.com_id
			where
				a.gift_no = '".$gift_no."'
			limit $startNo, $lmit
			
		";
		//debugSQL($sql);
		//$x2gate->select($conn, $sql, "xml");
		
		$result = DB::select($sql);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => count($result),
                "page_total" => $page_cnt
            ),
            "body" => $result
        ]);
		

	}

	/*
		Function: getExGoods
		사은품 제외 상품
	*/
	function getExGoods($gift_no, Request $request)
	{
		$gift_no = $request->input("gift_no");
		$page	= $request->input("page");
		
		$startNo = 0;
		$lmit = 50;
		if($page>1){
			$startNo = ($page-1)*50;
		}

		$sql = "
			select count(a.goods_no) as total
			from
				gift_ex_goods as a
				inner join goods as b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
				left outer join code as c on code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = c.code_id
				left outer join brand as d on d.brand = b.brand
				left outer join company e on b.com_id = e.com_id
			where
				a.gift_no = '".$gift_no."'
		";

		$row = DB::selectOne($sql);
		$total = $row->total;

		$page_cnt=(int)(($total-1)/$lmit) + 1;

		$sql = "
			select
				'' as chk,
				a.goods_no, a.goods_sub, b.style_no, e.com_nm, d.brand_nm, b.goods_nm, c.code_val, a.rt, b.com_id
			from
				gift_ex_goods as a
				inner join goods as b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
				left outer join code as c on code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = c.code_id
				left outer join brand as d on d.brand = b.brand
				left outer join company e on b.com_id = e.com_id
			where
				a.gift_no = '".$gift_no."'
		";
		//debugSQL($sql);
		$result = DB::select($sql);

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => count($result),
                "page_total" => $page_cnt
            ),
            "body" => $result
        ]);
	}

	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

}
