<?php

namespace App\Http\Controllers\partner\product;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Category;
use App\Models\Jaego;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;



class prd03Controller extends Controller
{
	public function index(Request $request)
	{
		$style_no	= $request->input('style_no');

		$values = [
			'style_no'		=> $style_no,
            'goods_stats' => SLib::getCodes('G_GOODS_STAT',["40" => "<>"]),
			'items'			=> SLib::getItems(),
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
		];
		return view(Config::get('shop.partner.view') . '/product/prd03', $values);
	}

    public function get($goods_no){
        return response()->json($this->_get($goods_no));
    }

    private function _get($goods_no){

        $query = /** @lang text */
            "
				select
					a.head_desc, a.goods_nm, a.goods_nm_eng, a.ad_desc, a.opt_kind_cd, a.goods_sub
					, a.brand, br.brand_nm, a.sale_stat_cl, a.style_no, a.goods_type, ifnull( type.code_val, 'N/A') as goods_type_nm
					, a.com_id, c.com_nm, c.com_type, c.pay_fee, a.make, a.org_nm
					, a.price, a.goods_sh, a.wonga, a.delv_area, a.dlv_pay_type, a.dlv_fee_cfg
					, a.bae_yn, a.baesong_price, a.baesong_kind, a.baesong_info
					, a.goods_location, a.point_cfg, a.point_yn, a.point, a.tax_yn, a.md_id
					, a.reg_dm, date_format(a.reg_dm,'%Y%m%d') as reg_dm_ymd	, a.upd_dm
					, a.rep_cat_cd, '' as rep_cat_nm, a.goods_cont, a.spec_desc, a.baesong_desc, a.opinion
					, replace(a.img,'a_500', 'a_500') as img,a.img_update
					, a.goods_no_org, c.margin_type
					, ifnull(a.sale_price,0) as sale_price, a.sale_s_dt, a.sale_e_dt, a.before_sale_price
					, (1 - (a.wonga) / ifnull(a.before_sale_price, a.price)) * 100 as before_sale_margin
					, (1 - (a.wonga) / ifnull(a.sale_price,1)) * 100 as sale_margin
					, a.option_kind
					, ifnull(cd.code_id,'ETC') as option_kind_type
					, a.is_unlimited
					, ifnull(a.is_option_use,'Y') as is_option_use
					, ifnull(a.related_cfg,'A') as related_cfg
					, restock_yn
					, a.new_product_type
					, a.new_product_day
					, '' as prf, ifnull(c.dlv_policy, 'S') as dlv_policy,a.class
				from goods a
					left join brand br on br.brand = a.brand
					left join company c on c.com_id = a.com_id
					left outer join code cd on cd.code_kind_cd = 'G_OPTION_KIND' and cd.code_id = a.option_kind
                                        left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and a.goods_type = type.code_id
				where a.goods_no = :goods_no
            ";
        $goods_info = DB::selectone($query,array("goods_no" => $goods_no));

        //상품 배송비 설정
        if( $goods_info->dlv_policy == "S" ){
            $conf	= new Conf();
            $cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
            $cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");

            if( $goods_info->price < $cfg_free_dlv_fee_limit )
                $goods_info->baesong_price	= $cfg_dlv_fee;
            else
                $goods_info->baesong_price	= 0;
        }

        // 총 재고 합계
        $qty = 0;
        $wqty = 0;

        $query = /** @lang text */
            "
            select
                sum(good_qty) as qty,
                sum(wqty) as wqty
            from goods_summary
            where goods_no = :goods_no
        ";
        $qty_info = DB::selectone($query,array("goods_no" => $goods_no));
        if($qty_info){
            $qty = $qty_info->qty;
            $wqty = $qty_info->wqty;
        }

        $goods_images[0] = $goods_info->img;

        $images = DB::table("goods_image")
            ->select("type", "img")
            ->where("goods_no","=",$goods_no)->get();

        foreach ($images as $image) {
            $goods_images[] = $image->img;
        }

        $user		= Auth('partner')->user();
        $category	= new Category($user, "DISPLAY");
        $rep_cat_nm	= substr( $category->Location( $goods_info->rep_cat_cd ), 0 );

        //대표카테고리명 업데이트
        $goods_info->rep_cat_nm	= $rep_cat_nm;

        $query = /** @lang text */
            "
                select a.d_cat_cd, a.seq, a.disp_yn, b.full_nm
                from category_goods a
                    inner join category b on b.cat_type = a.cat_type and b.d_cat_cd = a.d_cat_cd
                where a.goods_no = '$goods_no' and a.goods_sub = '0'
                    and a.cat_type = 'DISPLAY'
                order by a.d_cat_cd
            ";
        $displays = DB::select($query);

        $query = /** @lang text */
            "
                select a.d_cat_cd, a.seq, a.disp_yn, b.full_nm
                from category_goods a
                    inner join category b on b.cat_type = a.cat_type and b.d_cat_cd = a.d_cat_cd
                where a.goods_no = '$goods_no' and a.goods_sub = '0'
                    and a.cat_type = 'ITEM'
                order by a.d_cat_cd
            ";
        $items = DB::select($query);

        //상품 옵션 정보
        //1. 단일옵션, 2. 다중옵션(2단)
        $sql	= /** @lang text */
            " select count(*) as tot from goods_option where goods_no = :goods_no ";
        $row	= DB::selectOne($sql,['goods_no' => $goods_no]);
        $opt_kind_cnt	= $row->tot;

        //옵션 종류별
        if( $opt_kind_cnt == 1 ){
            //단일옵션
            $opt2	= array('opt2'=> 'one');
        }else if( $opt_kind_cnt == 2 ){
            //2중 멀티옵션
            $sql	= /** @lang text */
                " select distinct(substring_index(goods_opt,'^',-1)) as opt2 from goods_summary where goods_no = :goods_no and use_yn = 'Y' order by opt2 ";
            $opt2	= DB::select($sql,['goods_no' => $goods_no]);
        }else{
            $opt2	= array();
        }

        $sql	= /** @lang text */
            " 
                select opt_name,goods_opt,opt_price,good_qty as qty,wqty,soldout_yn
                from goods_summary 
                where goods_no = :goods_no 
                order by goods_opt
          ";
        $options	= DB::select($sql,['goods_no' => $goods_no]);

        $goods_info = array_merge((array)$goods_info,[
            'goods_no'			=> $goods_no,
            'goods_images'		=> $goods_images,
            'qty'				=> $qty,
            'wqty'				=> $wqty,
            'opt2'				=> $opt2,
            'options'           => $options,
            'displays'          => $displays,
            'items'             => $items
        ]);
        return $goods_info;
    }


    public function save(Request $req){

        $com_id =  Auth('partner')->user()->com_id;
        $com_nm =  Auth('partner')->user()->com_nm;

        $code = 200;
	    $msg = "";
	    $product = $req->all();

	    unset($product["blank"]);
        unset($product["com_nm"]);
        unset($product["opt_kind_nm"]);
        unset($product["full_nm"]);
        unset($product["img_view"]);
        unset($product["coupon_price"]);
        unset($product["sale_rate"]);
        unset($product["qty"]);
        unset($product["wqty"]);
        unset($product["margin_rate"]);
        unset($product["margin_amt"]);
        unset($product["goods_type_nm"]);
        unset($product["com_type_d"]);

        $product["com_id"] = $com_id;

        if(!isset($product["sale_stat_cl"])){
            $product["goods_type"] = "P";
        }
        if(!isset($product["is_unlimited"])){
            $product["is_unlimited"] = "N";
        }
        if(!isset($product["tax_yn"])){
            $product["tax_yn"] = "Y";
        }
        if(!isset($product["baesong_info"])){
            $product["baesong_info"] = "1";
        }
        if(!isset($product["baesong_kind"])){
            $product["baesong_kind"] = "2";
        }
        if($product["sale_stat_cl"] == ""){
            $product["sale_stat_cl"] = 5;
        }

        $user = [
	      "id" => $com_id,
	      "name" => $com_nm,
        ];
	    $prd = new Product($user);
        $goods_no = $prd->Add2($product);

        return response()->json([
            "code" => $code,
            "msg" => $goods_no
        ]);
    }
}
