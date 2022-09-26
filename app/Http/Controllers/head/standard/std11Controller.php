<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use Exception;
class std11Controller extends Controller
{
    public function index() {

        $values = [
          'user_yn' => SLib::getCodes('G_USER_TYPE'),
          'types' => SLib::getCodes('G_AD_TYPE'),
          'states' => SLib::getCodes('IS_SHOW')
        ];
        return view( Config::get('shop.head.view') . '/standard/std11',$values);
    }

    public function show($no = '') {
        //$com_id = Auth('partner')->user()->com_id;
        
		if($no == "") {
			$name				= "";
			$use_yn				= "Y";
			$dc_rate			= 0;
			$dc_amt				= 0;
			$dc_range			= "A";
			$date_from			= " ";
			$date_to			= " ";
			$limit_margin_rate	= 0;
			$limit_coupon_yn	= "Y";
			$limit_point_yn		= "Y";
			$add_point_yn		= "Y";
			$add_point_rate		= 0;
			$add_point_amt		= 0;
		} else {
            $row = DB::selectOne("select * from ad_dc where no = '$no'");

			if (!empty($row->name)) {
				$name				= $row->name;
				$use_yn				= $row->use_yn;
				$dc_rate			= $row->dc_rate;
				$dc_amt				= $row->dc_amt;
				$dc_range			= $row->dc_range;
				$date_from			= $row->date_from;
				$date_to			= $row->date_to;
				$limit_margin_rate	= $row->limit_margin_rate;
				$limit_coupon_yn	= $row->limit_coupon_yn;
				$limit_point_yn		= $row->limit_point_yn;
				$add_point_yn		= $row->add_point_yn;
				$add_point_rate		= $row->add_point_rate;
				$add_point_amt		= $row->add_point_amt;
			}
        }
        
        $values = [
            'no' => $no,
            'name' => $name,
            'use_yn' => $use_yn,
            'dc_rate' => $dc_rate,
            'dc_amt' => $dc_amt,
            'dc_range' => $dc_range,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'limit_margin_rate' => $limit_margin_rate,
            'limit_coupon_yn' => $limit_coupon_yn,
            'limit_point_yn' => $limit_point_yn,
            'add_point_yn' => $add_point_yn,
            'add_point_rate' => $add_point_rate,
            'add_point_amt' => $add_point_amt
        ];

        return view( Config::get('shop.head.view') . '/standard/std11_show',$values);
    }

    public function showDCBrand($no) {
        $values = [
            'no' => $no,
            'com_types' => SLib::getCodes('G_COM_TYPE')
        ];
        return view( Config::get('shop.head.view') . '/standard/std11_dc_brand',$values);
    }

    public function showDCGoods($no) {
        $conf = new Conf();

		$site = $conf->getConfig("shop", "sale_place", "");

        $sql = 'select * from company where site_yn = "Y" and com_type=4';

        $values = [
            'no' => $no,
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_goods_type'),
            'com_types' => SLib::getCodes('G_COM_TYPE'),
            'sites' => DB::select($sql),
            'site' => $site
        ];
        return view( Config::get('shop.head.view') . '/standard/std11_dc_goods',$values);
    }

    public function search(Request $req){
		$name				= $req->input("name", "");
		$use_yn			    = $req->input("use_yn", "");
		$dc_range			= $req->input("dc_range", "");
		$limit_coupon_yn	= $req->input("limit_coupon_yn", "");
		$limit_point_yn	    = $req->input("limit_point_yn", "");
		$add_point_yn		= $req->input("add_point_yn", "");
        $where = "";

		if ($name != "")			$where .= " and a.name like '$name%'";
		if ($use_yn != "")			$where .= " and a.use_yn = '$use_yn'";
		if ($dc_range != "")		$where .= " and a.dc_range = '$dc_range'";
		if ($limit_coupon_yn != "")	$where .= " and a.limit_coupon_yn = '$limit_coupon_yn'";
		if ($limit_point_yn != "")	$where .= " and a.limit_point_yn = '$limit_point_yn'";
		if ($add_point_yn != "")	$where .= " and a.add_point_yn = '$add_point_yn'";

        if($dc_range == 'A') {
            $sql = "
                select *
                from ad_dc a
                order by a.no desc
            ";
        }else{
            $sql = "
			select
				a.name, a.use_yn, a.dc_range, dc_rate, dc_amt, date_from, date_to,
				limit_margin_rate, limit_coupon_yn, limit_point_yn, add_point_yn, add_point_rate, add_point_amt, admin_nm, rt, ut,
				a.no
			from ad_dc a
			where 1=1
				$where
			order by a.no desc
        ";
        }
		
        $rows = DB::select($sql);

        $collection = collect($rows);
        $filtered = $collection->reject(function ($obj) {
            return $obj->use_yn == "D";
        });
        $rows = $filtered->values()->all();

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }
    
    public function searchDCBrand(Request $req) {
		$no         = $req->input('no', '');
		$brand      = $req->input('brand', '');
		$brand_type	= $req->input('brand_type', '');
		$use_yn		= $req->input('use_yn', '');
        $best_yn	= $req->input('best', '');
        $com_nm     = $req->input('com_nm', '');

		$where = "";		
		
		if($brand)		$where .= " and (c.brand like '%$brand%' or c.brand_nm like '%$brand%' or c.brand_nm_eng like '%$brand%' )";
		if($brand_type)	$where .= " and c.brand_type = '$brand_type'";
		if($use_yn)		$where .= " and c.use_yn = '$use_yn'";
		if($best_yn)	$where .= " and c.best_yn = '$best_yn'";
        if($com_nm)     $where .= " and d.com_nm like '%$com_nm%' ";
		$sql = "
			select
				c.brand_type, b.brand , c.brand_nm_eng, c.brand_nm, bc.qty, d.com_nm,
				b.dc_rate, b.dc_amt, b.limit_margin_rate, b.admin_nm, b.ut
			from ad_dc a inner join ad_dc_brand b on a.no = b.dc_no
				inner join brand c on b.brand = c.brand 
				left outer join (
					select
						brand,com_id,
						count(*) as qty
					from goods
					where sale_stat_cl > 0
					group by brand,com_id
				) bc on c.brand = bc.brand
				left outer join company d on bc.com_id = d.com_id
			where a.no = '$no' $where
			group by b.brand
			order by c.brand_nm
		";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function searchDCGoods(Request $req) {
        // 설정 값 얻기
        $conf = new Conf();

        $cfg_img_size_list		= SLib::getCodesValue('G_IMG_SIZE', 'list');
		$cfg_img_size_real		= SLib::getCodesValue('G_IMG_SIZE', 'list');
		$cfg_domain_img			= $conf->getConfigValue("shop","domain_img");
        $cfg_order_point_ratio	= $conf->getConfigValue("point","ratio");

		if($cfg_domain_img == ""){
		    $cfg_domain_img		= $_SERVER["HTTP_HOST"];
        }

		$goods_img_url		= sprintf("http://%s",$cfg_domain_img);
		$img_size			= $req->input('img_size', '');
		if($img_size != ""){
		$cfg_img_size_list	= sprintf("_%s",$img_size);
		}
		
		$where = "";
		
		$no = $req->input("no", '');
		
		// 변수 설정
		$goods_type		= $req->input("goods_type");		// 상품 구분
		$goods_stat		= $req->input("goods_stat");		// 상품 상태
		$style_no		= $req->input("style_no");			// 스타일넘버
		$style_nos		= $req->input("style_no");			// 스타일넘버 textarea
		$goods_no		= $req->input("goods_no");			// 상품번호 input text
		$goods_nos		= $req->input("goods_nos");			// 상품번호 textarea
		$com_type		= $req->input("com_type");			// 업체구분
        $com_id			= $req->input("com_id");			// 업체아이디
        $opt_kind_cd	= $req->input("opt_kind_cd");		// 품목
        $brand_cd		= $req->input("brand_cd");			// 브랜드
		$goods_nm		= $req->input("goods_nm");			// 상품명		
        $com_nm         = $req->input('com_nm', '');

		if( $goods_type	!= "" )		$where .= " and c.goods_type = '$goods_type' ";
		if( $goods_stat	!= "" )		$where .= " and c.sale_stat_cl in ($goods_stat) ";
		if( $style_nos != "" ) {
			$style_no = $style_nos;
        }

		$style_no = preg_replace("/\s/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\t/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);
		$style_no = preg_replace("/\n/",",",$style_no);
		$style_no = preg_replace("/,,/",",",$style_no);

		if( $style_no != "" ) {
			$style_nos = explode(",",$style_no);
			if(count($style_nos) > 1){
				if(count($style_nos) > 500) array_splice($style_nos,500);
				$in_style_nos = "";
				for($i=0; $i<count($style_nos); $i++){
					if(isset($style_nos[$i]) && $style_nos[$i] != ""){
						$in_style_nos .= ($in_style_nos == "") ? "'$style_nos[$i]'" : ",'$style_nos[$i]'";
					}
				}
				if($in_style_nos != "") {
					$where .= " and c.style_no in ( $in_style_nos ) ";
				}
			} else {
				$where .= " and c.style_no like '$style_no%' ";
			}
		}

		if($goods_no == "" && $goods_nos != ""){
			$goods_no = $goods_nos;
		}

		$goods_no = preg_replace("/\s/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);
		$goods_no = preg_replace("/\t/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);
		$goods_no = preg_replace("/\n/",",",$goods_no);
		$goods_no = preg_replace("/,,/",",",$goods_no);

		if( $goods_no		!= "" ){
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 500) array_splice($goods_nos,500);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and c.goods_no in ( $in_goods_nos ) ";
			} else {
				$where .= " and c.goods_no = '$goods_no' ";
			}
		}
		
		if( $com_type   != "" )	$where .= " and c.com_type = '$com_type' ";
		if( $com_id		!= "" )	$where .= " and c.com_id = '$com_id' ";
        if( $brand_cd	!= "" )	$where .= " and c.brand = '$brand_cd'";
		if( $goods_nm	!= "" )	$where .= " and c.goods_nm like '%$goods_nm%' ";
        if( $com_nm    != "" )     $where .= " and com.com_nm like '%$com_nm%' ";

		//멤버등급의 할인율
        $ratio_sql = "select dc_ratio from user_group where group_no = '3'";

        $row = DB::selectOne($ratio_sql);
		$dc_ratio = 0;
        if($row != null) $dc_ratio = $row->dc_ratio;

        //wonga
		$sql = "
			select
				b.goods_no, b.goods_sub, ifnull( type.code_val, 'N/A') as goods_type,com.com_nm,opt.opt_kind_nm,
				d.brand_nm, c.style_no,
				' ' as img_view,if(c.img <> '',concat('$goods_img_url',replace(c.img,'$cfg_img_size_real','$cfg_img_size_list')),'') as img,
				c.goods_nm, stat.code_val as sale_stat_cl, c.price, c.wonga, round(c.price*(1- if(c.limited_dc='Y',0,$dc_ratio)/100)) as group_amt,
				ifnull(gc.coupon_dc_price, 0) as coupon_dc_price, if(c.prepoint_yn='N',0,c.point) as prepoint, '' as receive_amt,
				round(c.price*(1- b.dc_rate/100) - b.dc_amt) as ptn_dc_amt, 
				'' as group_diff_amt, '' as margin_diff_amt, 
				b.dc_rate, b.dc_amt, b.admin_nm, b.ut,
				c.goods_type as goods_type_cd, com.com_type as com_type_d
			from ad_dc a inner join ad_dc_goods b on a.no = b.dc_no
				inner join goods c on b.goods_no = c.goods_no
				inner join brand d on d.brand = c.brand
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and c.sale_stat_cl = stat.code_id
				left outer join goods_coupon gc on gc.goods_no = c.goods_no and gc.goods_sub = c.goods_sub
				left outer join opt opt on opt.opt_kind_cd = c.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = c.com_id
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and c.goods_type = type.code_id
			where a.no = '$no' $where
			order by b.rt desc
        ";

        $rows = DB::select($sql);

        foreach($rows as $row) {
			$row->receive_amt = $row->group_amt - $row->prepoint - $row->coupon_dc_price;
			$row->margin_diff_amt = $row->ptn_dc_amt - $row->wonga;
			$row->group_diff_amt = $row->ptn_dc_amt - $row->receive_amt;
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }
    
    public function searchDCExGoods(Request $req) {
		$no = $req->input('no', '');
        
		$sql = "
			select
				b.goods_no, b.goods_sub, d.brand_nm, c.goods_nm, b.admin_nm, b.ut
			from ad_dc a inner join ad_dc_ex_goods2 b on a.no = b.dc_no
				inner join goods c on b.goods_no = c.goods_no
				inner join brand d on d.brand = c.brand
			where a.no = '$no'
			order by c.brand_nm
        ";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function DCStore($no = '', Request $req) {
        $values = [
            'name' => $req->input("name", ''),
            'use_yn' => $req->input("use_yn", 'Y'),
            'dc_range' => $req->input("dc_range", ''),
            'dc_rate' => $req->input("dc_rate", '0'),
            'dc_amt' => $req->input("dc_amt", '0'),
            'date_from' => $req->input("date_from", ''),
            'date_to' => $req->input("date_to", ''),
            'limit_margin_rate' => $req->input("limit_margin_rate", '0'),
            'limit_coupon_yn' => $req->input("limit_coupon_yn", 'Y'),
            'limit_point_yn' => $req->input("limit_point_yn", 'Y'),
            'add_point_yn' => $req->input("add_point_yn", 'Y'),
            'add_point_rate' => $req->input("add_point_rate", '0'),
            'add_point_amt' => $req->input("add_point_amt", '0'),
            'admin_id' => Auth('head')->user()->id,
            'admin_nm' => Auth('head')->user()->name,
            'ut' => now()
        ];

        if (empty($no)) {
            $values['rt'] = now();
            $no = DB::table('ad_dc')->insertGetId($values);
            return response()->json($no, 201);
        } else {
            DB::table('ad_dc')->where('no', $no)->update($values);
            return response()->json(null, 204);
        }
    } 

    public function addDCBrand($no, Request $req) {
        $brand = $req->input('brand', '');
        
        $sql = "select * from  ad_dc where no = '$no'";
        
        $row = DB::selectOne($sql);

		if(!empty($row)) {
            try {
                DB::beginTransaction();

                $sql = "
                    select count(*) as cnt from ad_dc_brand
                    where dc_no = '$no' and brand = '$brand'
                ";
                
                $cntRow = DB::selectOne($sql);

                if($cntRow->cnt > 0) {
                    throw new Exception("이미 등록된 브랜드입니다.");
                }

                $values = [
                    'dc_no' => $no, 
                    'brand' => $brand, 
                    'dc_rate' => $row->dc_rate, 
                    'dc_amt' => $row->dc_amt, 
                    'limit_margin_rate' => $row->limit_margin_rate, 
                    'admin_id' => Auth('head')->user()->id, 
                    'admin_nm' => Auth('head')->user()->name, 
                    'rt' => now(), 
                    'ut' => now()
                ];

                DB::table('ad_dc_brand')->insert($values);

                DB::commit();

                return response()->json(null, 204);
            }catch(Exception $e) {
                DB::rollback();
                return response()->json(['message' => $e->getMessage()], 500);
            }
		}
    }

    public function deleteDCBrand($no, Request $req) {
        DB::table('ad_dc_brand')->where([
            'dc_no' => $no,
            'brand' => $req->input('brand', '')
        ])->delete();

        return response()->json(null, 204);
    }

    public function updateDCBrand($no, Request $req) {
        $wheres = [
            'dc_no' => $no,
            'brand' => $req->input('brand', '')
        ];

        $values = [
            'dc_rate' => $req->input('dc_rate', 0),
            'dc_amt' => $req->input('dc_amt', 0),
            'limit_margin_rate' => $req->input('limit_margin_rate', ''),
            'admin_id' => Auth('head')->user()->id, 
            'admin_nm' => Auth('head')->user()->name, 
            'ut' => now()
        ];

        DB::table('ad_dc_brand')
          ->where($wheres)
          ->update($values);

        return response()->json(null, 204);
    }

    public function addDCGoods($no, Request $req) {
        $goods_nos = explode(',', $req->input('goods_nos'));

        try {
            $sql = "select * from ad_dc where no = '$no'";
            $dc = DB::selectOne($sql);

            if (empty($dc->no)) throw new Exception("등록된 할인이 없습니다.");

            $dc_rate = $dc->dc_rate;
            $dc_amt = $dc->dc_amt;

            DB::beginTransaction();

            for($i=0; $i < count($goods_nos); $i++) {

                $a_goods_no = explode("|", $goods_nos[$i]);

                $goods_no = isset($a_goods_no[0]) ? $a_goods_no[0] : "";
                $goods_sub = isset($a_goods_no[1]) ? $a_goods_no[1] : "";

                if($goods_no > 0) {
                    $sql = "
                        select count(*) as cnt from ad_dc_goods
                        where dc_no = $no and goods_no = $goods_no
                    ";

                    $row = DB::selectOne($sql);
                    
                    if($row->cnt > 0) throw new Exception("이미 등록되어 있는 상품입니다.");

                    $admin_id = Auth('head')->user()->id;
                    $admin_nm = Auth('head')->user()->name;

                    $sql = "
                        insert into ad_dc_goods (
                            dc_no, goods_no, goods_sub, dc_rate, dc_amt, admin_id, admin_nm, rt, ut
                        ) values (
                            '$no', '$goods_no', '$goods_sub', '$dc_rate', '$dc_amt', '$admin_id', '$admin_nm', now(), now()
                        )
                    ";

                    DB::insert($sql);
                }
            }
            
            DB::commit();

            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteDCGoods($no, Request $req) {
		$goods_nos	= explode(",", $req->goods_nos);
        try {
            DB::beginTransaction();

            foreach($goods_nos as $val) {
                $a_goods_no = explode("|", $val);

                $goods_no = isset($a_goods_no[0]) ? $a_goods_no[0] : "";
                $goods_sub = isset($a_goods_no[1]) ? $a_goods_no[1] : "";

                $wheres = array("dc_no" => $no, "goods_no" => $goods_no, "goods_sub" => $goods_sub);
                
                DB::table('ad_dc_goods')->where($wheres)->delete();
            }

            DB::commit();

            return response()->json(null, 204);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    public function updateDCGoods($no, Request $req) {
		$goods_no		= $req->input('goods_no', '');
		$goods_sub		= $req->input('goods_sub', 0);
		$dc_rate		= $req->input('dc_rate', '');
        $dc_amt			= $req->input('dc_amt', '');

        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        try {
            DB::beginTransaction();

            $sql = "select * from ad_dc where no = '$no'";
            $dc = DB::selectOne($sql);

            if (empty($dc->no)) throw new Exception("등록되지 않은 할인입니다.");

            $wheres = array(
                "dc_no" => $no, 
                "goods_no" => $goods_no, 
                "goods_sub" => $goods_sub
            );

            $values = array(
                "dc_rate" => $dc_rate,
                "dc_amt" => $dc_amt,
                "admin_id" => $admin_id,
                "admin_nm" => $admin_nm,
                'ut' => now()
            );

            DB::table('ad_dc_goods')
              ->where($wheres)
              ->update($values);

            DB::commit();
            return response()->json(null, 204);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addDCExGoods($no, Request $req) {
		$goods_nos	= explode(",", $req->goods_nos);

        $admin_id = Auth('head')->user()->id;
        $admin_nm = Auth('head')->user()->name;

        try {
            DB::beginTransaction();
            $sql = "select * from ad_dc where no = '$no'";
            $dc = DB::selectOne($sql);

            if (empty($dc->no)) throw new Exception("등록되지 않은 할인입니다.");


			$dc_rate	= $dc->dc_rate;
            $dc_amt		= $dc->dc_amt;

            foreach($goods_nos as $val) {
				$a_goods_no = explode("|", $val);

				$goods_no = isset($a_goods_no[0]) ? $a_goods_no[0] : "";
				$goods_sub = isset($a_goods_no[1]) ? $a_goods_no[1] : "";

                if($goods_no === "") throw new Exception("상품을 선택해주세요.");
                
                $sql = "
                    select count(*) as cnt
                    from ad_dc_ex_goods2
                    where dc_no = $no 
                      and goods_no = $goods_no 
                      and goods_sub = $goods_sub
                ";

                $row = DB::selectOne($sql);

                if($row->cnt >  0) throw new Exception("이미 등록되어 있는 상품 있습니다.");

                $sql = "
                    insert into ad_dc_ex_goods2 (
                        dc_no, goods_no, goods_sub, admin_id, admin_nm, rt, ut
                    ) values (
                        '$no', '$goods_no', '$goods_sub', '$admin_id', '$admin_nm', now(), now()
                    )
                ";

                DB::insert($sql);                
            }

            DB::commit();
            return response()->json(null, 204);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteDCExGoods($no, Request $req) {
        $goods_nos	= explode(",", $req->goods_nos);

        try {
            DB::beginTransaction();

            foreach($goods_nos as $val) {
                $a_goods_no = explode("|", $val);

                $goods_no = isset($a_goods_no[0]) ? $a_goods_no[0] : "";
                $goods_sub = isset($a_goods_no[1]) ? $a_goods_no[1] : "";

                $wheres = array("dc_no" => $no, "goods_no" => $goods_no, "goods_sub" => $goods_sub);
                
                DB::table('ad_dc_ex_goods2')->where($wheres)->delete();
            }

            DB::commit();

            return response()->json(null, 204);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);

        }
    }
}
