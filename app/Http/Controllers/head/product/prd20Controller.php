<?php

namespace App\Http\Controllers\head\product;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use Exception;

class prd20Controller extends Controller
{
    public function index()
    {
        $code  = "naver_shopping";
        $edate = date("Y-m-d");
        $sdate = date('Y-m-d', strtotime(-7 . 'days'));

        $values = [
            'code'  => $code,
            "edate" => $edate,
            "sdate" => $sdate,
        ];
        return view(Config::get('shop.head.view') . '/product/prd20', $values);
    }

    public function search(Request $req)
    {

        $code    = "naver_shopping";
        $sdate    = $req->input("sdate", date('Y-m-d', strtotime(-7 . 'days')));
        $edate    = $req->input("edate", date("Y-m-d"));
        $type    = $req->input('type', '');
        $limit      = $req->input("limit", 100);

        $page = $req->input("page", 1);
        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($type != "")   $where .= " and type = '" . Lib::quote($type) . "' ";

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            // 갯수 얻기
            $sql =
                /** @lang text */
                " 
                select count(*) as total
                from shop_ad_goods_history
                where shop = :code 
                    and regi_date >= :sdate and regi_date < date_add(:edate,interval 1 day) $where
			";
            $row = DB::selectOne($sql, ['code' => $code, 'sdate' => $sdate, 'edate' => $edate]);
            $total = $row->total;
            if ($total > 0) {
                $page_cnt = (int)(($total - 1) / $page_size) + 1;
            }
        }

        $sql =
            /** @lang text */
            "
            select
				case type
				when 'a' then '전체상품'
				when 's' then '요약상품'
				when 'n' then '신규상품'
				end as type
				, date_format(regi_date,'%Y.%m.%d %H:%i:%s') as regi_date, qty,
				type as type_org
            from shop_ad_goods_history
            where shop = :code 
                and regi_date >= :sdate and regi_date < date_add(:edate,interval 1 day) $where
            order by regi_date desc
            limit $startno,$page_size
            ";

        $rows = DB::select($sql, ['code' => $code, 'sdate' => $sdate, 'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function show($code)
    {
        //1. 전체상품
        $sql =
            /** @lang text */
            "
            select * from shop_ad_goods
            where shop = :code
            and type = 'a';
         ";
        $shop_ad_goods_all = DB::selectOne($sql, ["code" => $code]);

        //2. 신규상품
        $sql =
            /** @lang text */
            "
            select * from shop_ad_goods
            where shop = :code
            and type = 'n';
         ";
        $shop_ad_goods_new = DB::selectOne($sql, ["code" => $code]);

        //3. 설정
        $sql =
            /** @lang text */
            "
            select * from shop_ad_goods
            where shop = :code
         ";
        $shop_ad_goods_set = DB::selectOne($sql, ["code" => $code]);

        // 미리보기 url 할당
        $conf = new Conf();
        $cfg_domain	= $conf->getConfigValue("shop","domain");
        $mall_id= $shop_ad_goods_all->mall_id;
        $_mall_id = ( $mall_id == "" ) ? $mall_id : "_".$mall_id;
        $goods_all_url	= sprintf("https://%s/api/naver/naver%s_all.txt", $cfg_domain, $_mall_id);
        $goods_new_url	= sprintf("https://%s/api/naver/naver%s_new.txt", $cfg_domain, $_mall_id);

        $values = [
            'code' => $code,
            'shop_ad_goods_set' => $shop_ad_goods_set,
            'shop_ad_goods_all' => $shop_ad_goods_all,
            'shop_ad_goods_new' => $shop_ad_goods_new,
            'goods_all_url' => $goods_all_url,
            'goods_new_url' => $goods_new_url,
            'all_except_some' => $this->allExceptSome($shop_ad_goods_all), // 전체 상품 중 제외된 정보 가져오기
            'new_except_some' => $this->newExceptSome($shop_ad_goods_new), // 신규 상품 중 제외된 정보 가져오기
            'ads' => $this->getAds($shop_ad_goods_all->ad) // 광고 목록 출력
        ];

        return view(Config::get('shop.head.view') . '/product/prd20_show', $values);
    }

    /**
     * 전체 상품 연동 제외 - 대표 카테고리, 업체, 브랜드, 상품 조회
     */
    public function allExceptSome($info_row) {

        $array_d_cat_cd = array();
		$array_brand 	= array();
		$array_com 		= array();
		$array_goods 	= array();

		$ex_d_cat_cd 		= $info_row->ex_d_cat_cd;
		$ex_goods 			= $info_row->ex_goods;
		$ex_brand 			= $info_row->ex_brand;
		$ex_com_id 			= $info_row->ex_com_id;
		
        $ex_d_cat_cds 		= explode("\t", $ex_d_cat_cd);
        $ex_goods_nosub 	= explode("\t", $ex_goods);
		$ex_brands 			= explode("\t", $ex_brand);
		$ex_com_ids 		= explode("\t", $ex_com_id);

        $goods_no 			= "";
		$goods_sub 			= "";

        // 제외 카테고리 조회
		for ($i = 0; $i < count($ex_d_cat_cds); $i++) {
			if ($ex_d_cat_cds[$i] != "") {
				$sql = "
					select d_cat_cd, full_nm from category where cat_type = 'DISPLAY' and d_cat_cd = '$ex_d_cat_cds[$i]'
				";
                $row = DB::selectOne($sql);
				$d_cat_cd 	= $row->d_cat_cd;
				$full_nm 	= $row->full_nm;
				$array_d_cat_cd[$d_cat_cd] = $full_nm;
			}
		}

        // 제외 업체 조회
        for ($i = 0; $i < count($ex_com_ids); $i++) {
			if ($ex_com_ids[$i] != "") {
				$sql = "
					select com_id, com_nm from company where com_id = '$ex_com_ids[$i]'
				";
				$row = DB::selectOne($sql);
				$com_id 	= $row->com_id;
				$com_nm 	= $row->com_nm;
				$array_com[$com_id] = $com_nm;
			}
		}

        // 제외 브랜드 조회
		for ($i = 0; $i < count($ex_brands); $i++) {
			if ($ex_brands[$i] != "") {
				$sql = "
					select brand, brand_nm from brand where brand = '$ex_brands[$i]'
				";
				$row = DB::selectOne($sql);
				$brand 		= $row->brand;
				$brand_nm 	= $row->brand_nm;
				$array_brand[$brand] = $brand_nm;
			}
		}

        // 제외 상품 조회
		for ($i = 0; $i < count($ex_goods_nosub); $i++) {
			if ($ex_goods_nosub[$i] != "") {
                $array = explode('|', $ex_goods_nosub[$i]);
                $goods_no = $array[0];
                $goods_sub = $array[1];
				$sql = "
					select goods_nm from goods where goods_no = '$goods_no' and goods_sub = '$goods_sub'
				";
				$row = DB::selectOne($sql);
                if ($row) {
                    $goods_nm	= $row->goods_nm;
				    $array_goods[$ex_goods_nosub[$i]] = $goods_nm;
                }
			}
		}

        return [
            'except_rep_category' => $array_d_cat_cd,
            'except_company' => $array_com,
            'except_brand' => $array_brand,
            'except_goods' => $array_goods
        ];
    }

    /**
     * 신규 상품 연동 제외 - 대표 카테고리, 업체, 브랜드, 상품 조회
     */
    public function newExceptSome($info_row) {

		$d_cat_cd 			= "";
		$goods_nm 			= "";
		$brand 				= "";
		$com_id 			= "";
		$goods_no 			= "";
		$goods_sub 			= "";
		$array_new_d_cat_cd = array();
		$array_new_brand 	= array();
		$array_new_com 		= array();
		$array_new_goods 	= array();

		$ex_new_d_cat_cd 		= $info_row->ex_d_cat_cd;
		$ex_new_goods 			= $info_row->ex_goods;
		$ex_new_brand 			= $info_row->ex_brand;
		$ex_new_com_id 			= $info_row->ex_com_id;

		$ex_new_d_cat_cds 		= explode("\t", $ex_new_d_cat_cd);
		$ex_new_goods_nosub 	= explode("\t", $ex_new_goods);
		$ex_new_brands 			= explode("\t", $ex_new_brand);
		$ex_new_com_ids 		= explode("\t", $ex_new_com_id);

		// 상품 연동 제외 카테고리 조회
		for ($i = 0; $i < count($ex_new_d_cat_cds); $i++) {
			if ($ex_new_d_cat_cds[$i] != "") {
				$sql = "
					select d_cat_cd, full_nm from category where cat_type = 'DISPLAY' and d_cat_cd = '$ex_new_d_cat_cds[$i]'
				";
                $row = DB::selectOne($sql);
				$d_cat_cd 	= $row->d_cat_cd;
				$full_nm 	= $row->full_nm;
				$array_new_d_cat_cd[$d_cat_cd] = $full_nm;
			}
		}

        // 상품 연동 제외 업체 조회
		for ($i = 0; $i < count($ex_new_com_ids); $i++) {
			if ($ex_new_com_ids[$i] != "") {
				$sql = "
					select com_id, com_nm from company where com_id = '$ex_new_com_ids[$i]'
				";
				$row = DB::selectOne($sql);
				$com_id = $row->com_id;
				$com_nm = $row->com_nm;
				$array_new_com[$com_id] = $com_nm;
			}
		}


		// 상품 연동 제외 브랜드 조회
		for ($i = 0; $i < count($ex_new_brands); $i++) {
			if ($ex_new_brands[$i] != "") {
				$sql = "
					select brand, brand_nm from brand where brand = '$ex_new_brands[$i]'
				";
                $row = DB::selectOne($sql);
				$brand 		= $row->brand;
				$brand_nm 	= $row->brand_nm;
				$array_new_brand[$brand] = $brand_nm;
			}
		}

        // 상품 연동 제외 상품 조회
		for ($i = 0; $i < count($ex_new_goods_nosub); $i++) {
			if ($ex_new_goods_nosub[$i] != "") {
                $array = explode('|', $ex_new_goods_nosub[$i]);
                $goods_no = $array[0];
                $goods_sub = $array[1];
				$sql = "
					select goods_nm from goods where goods_no = '$goods_no' and goods_sub = '$goods_sub'
				";
				$row = DB::selectOne($sql);
				$goods_nm 		= $row->goods_nm;
				$array_new_goods[$ex_new_goods_nosub[$i]] = $goods_nm;
			}
		}

        return [
            'except_rep_category' => $array_new_d_cat_cd,
            'except_company' => $array_new_com,
            'except_brand' => $array_new_brand,
            'except_goods' => $array_new_goods
        ];
    }

    public function getAds($ad)
    {
        // 광고 목록 가져오기
		$sql = "
            select
                ad as 'value', concat('[', cd.code_val,'_', c.code_val,'] ', name ) as 'name'
            from ad a
                inner join code c on a.type = c.code_id and c.code_kind_cd = 'G_AD_TYPE'
                inner join code cd on a.user_yn = cd.code_id and cd.code_kind_cd = 'G_USER_TYPE'
            where state = '1'
            order by cd.code_val, c.code_val desc,name
        ";

        $ads_list = DB::select($sql);

        $selected = "";

        // 저장된 광고 가져오기
        if ($ad != "") {
            $sql = "
                select
                    ad as 'value', concat('[', cd.code_val,'_', c.code_val,'] ', name ) as 'name'
                from ad a
                    inner join code c on a.type = c.code_id and c.code_kind_cd = 'G_AD_TYPE'
                    inner join code cd on a.user_yn = cd.code_id and cd.code_kind_cd = 'G_USER_TYPE'
                where state = '1' and a.ad in ('$ad')
                order by cd.code_val, c.code_val desc,name
            ";
            $selected = DB::selectOne($sql);
        }

        return [
            'list' => $ads_list,
            'selected' => $selected
        ];
    }

    public function store($code, Request $request)
    {
        $admin_id = Auth::guard('head')->user()->id;

        $ex_d_cat_type_all = $request->input('ex_d_cat_type_all');
        $ex_goods_type_all = $request->input('ex_goods_type_all');
        $ex_brand_type_all = $request->input('ex_brand_type_all');
        $ex_com_type_all = $request->input('ex_com_type_all');
        $price_from_all = $request->input('price_from_all');
        $price_to_all = $request->input('price_to_all');
        $qty_limit_all = $request->input('qty_limit_all');

        $ex_d_cat_cd = $request->input('ex_d_cat_cd');
        $ex_com_id = $request->input('ex_com_id');
        $ex_brand = $request->input('ex_brand');
        $ex_goods = $request->input('ex_goods');

        $shop_ad_goods_all = [
            'ex_d_cat_cd' => $ex_d_cat_cd,
            'ex_com_id' => $ex_com_id,
            'ex_brand' => $ex_brand,
            'ex_goods' => $ex_goods,
            'ex_d_cat_type' => $ex_d_cat_type_all,
            'ex_goods_type' => $ex_goods_type_all,
            'ex_brand_type' => $ex_brand_type_all,
            'ex_com_type' => $ex_com_type_all,
            'price_from' => $price_from_all,
            'price_to' => $price_to_all,
            'qty_limit' => $qty_limit_all,
            'admin_id' => $admin_id,
            'regi_date' => DB::raw('now()'),
            'upd_date' => DB::raw('now()'),
        ];

        $ex_d_cat_type_new = $request->input('ex_d_cat_type_new');
        $ex_goods_type_new = $request->input('ex_goods_type_new');
        $ex_brand_type_new = $request->input('ex_brand_type_new');
        $ex_com_type_new = $request->input('ex_com_type_new');
        $price_from_new = $request->input('price_from_new');
        $price_to_new = $request->input('price_to_new');
        $qty_limit_new = $request->input('qty_limit_new');
        $new_goods_date_new = $request->input('new_goods_date_new');

        $shop_ad_goods_new = [
            'ex_d_cat_type' => $ex_d_cat_type_new,
            'ex_goods_type' => $ex_goods_type_new,
            'ex_brand_type' => $ex_brand_type_new,
            'ex_com_type' => $ex_com_type_new,
            'price_from' => $price_from_new,
            'price_to' => $price_to_new,
            'qty_limit' => $qty_limit_new,
            'new_goods_date' => $new_goods_date_new,
            'admin_id' => $admin_id,
            'regi_date' => DB::raw('now()'),
            'upd_date' => DB::raw('now()'),
        ];

        $ad_set = $request->input('ad_set');
        $domain_set = $request->input('domain_set');
        $use_yn_set = $request->input('use_yn_set', 'Y');
        $card_desc_set = $request->input('card_desc_set');
        $head_desc_set = $request->input('head_desc_set');
        $tail_desc_set = $request->input('tail_desc_set');

        $shop_ad_goods_set = [
            'ad' => $ad_set,
            'domain' => $domain_set,
            'use_yn' => $use_yn_set,
            'admin_id' => $admin_id,
            'regi_date' => DB::raw('now()'),
            'upd_date' => DB::raw('now()'),
            'card_desc' => $card_desc_set,
            'head_desc' => $head_desc_set,
            'tail_desc' => $tail_desc_set,
        ];

        try {
            DB::transaction(function () use (&$result, $code, $shop_ad_goods_all, $shop_ad_goods_new, $shop_ad_goods_set) {
                $cnt = DB::table('shop_ad_goods')
                    ->where('shop', '=', $code)
                    ->where('type', '=', 'a')
                    ->count();

                if ($cnt > 0) {
                    DB::table('shop_ad_goods')
                        ->where('shop', '=', $code)
                        ->where('type', '=', 'a')
                        ->update($shop_ad_goods_all);
                } else {
                    DB::table('shop_ad_goods')->insert($shop_ad_goods_all);
                }

                $cnt = DB::table('shop_ad_goods')
                    ->where('shop', '=', $code)
                    ->where('type', '=', 'n')
                    ->count();

                if ($cnt > 0) {
                    DB::table('shop_ad_goods')
                        ->where('shop', '=', $code)
                        ->where('type', '=', 'n')
                        ->update($shop_ad_goods_new);
                } else {
                    DB::table('shop_ad_goods')->insert($shop_ad_goods_new);
                }

                $cnt = DB::table('shop_ad_goods')
                    ->where('shop', '=', $code)
                    ->count();

                if ($cnt > 0) {
                    DB::table('shop_ad_goods')
                        ->where('shop', '=', $code)
                        ->update($shop_ad_goods_set);
                } else {
                    DB::table('shop_ad_goods')->insert($shop_ad_goods_set);
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg]);
    }
}
