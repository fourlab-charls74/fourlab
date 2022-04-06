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
use Exception;



class prd01Controller extends Controller
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
		return view(Config::get('shop.partner.view') . '/product/prd01', $values);
	}

    public function index_choice()
    {
        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT',["40" => "<>"]),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
        ];
        return view(Config::get('shop.partner.view') . '/product/prd01_choice', $values);
    }

    public function search(Request $request)
    {
        $com_id =  Auth('partner')->user()->com_id;

        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $sdate = str_replace("-", "", $request->input("sdate", date('Y-m-d')));
        $edate = str_replace("-", "", $request->input("edate", date("Y-m-d")));

        $goods_stat = $request->input("goods_stat");
        $style_no = $request->input("style_no");
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $cat_type = $request->input("cat_type");
        $cat_cd = $request->input("cat_cd");
        $is_unlimited = $request->input("is_unlimited");
        $head_desc = $request->input("head_desc");
        $is_unlimited = $request->input("is_unlimited");
        $limit = $request->input("limit",100);
        $ord = $request->input('ord','desc');
        $ord_field = $request->input('ord_field','g.goods_no');
        $make = $request->input('make');
        $org_nm = $request->input('org_nm');
        $is_option_use = $request->input('is_option_use');
        $goods_location = $request->input('goods_location');

        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        $where = "";
        if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if ($is_unlimited != "") $where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";

        if($cat_cd != ""){
            if($cat_type === "DISPLAY"){
                $where .= " and g.rep_cat_cd = '". Lib::quote($cat_cd) . "' ";
            } else if($cat_type === "ITEM"){
                $where .= " and ( select count(*) from category_goods where cat_type = 'ITEM' and d_cat_cd = '". Lib::quote($cat_cd) . "' and goods_no = g.goods_no ) > 0 ";
            }
        }

        if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        if ($is_unlimited != "") $where .= " and g.is_unlimited = '" . Lib::quote($is_unlimited) . "' ";
        if ($goods_stat != "") $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";

        if( $make != "" )				$where .= " and g.make = '$make' ";
        if( $org_nm != "" )			$where .= " and g.org_nm = '$org_nm' ";
		if( $is_option_use != "" )	$where .= " and g.is_option_use = '$is_option_use' ";
		if( $goods_location != "" )	$where .= " and g.goods_location like '%$goods_location%' ";

		if ($sdate != "") $where .= " and g.reg_dm >= '$sdate' ";
		if ($edate != "") $where .= " and g.reg_dm < date_add('$edate',interval 1 day) ";

        if($goods_nos        != ""){
            $goods_no = $goods_nos;
        }
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

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

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $query = /** @lang text */
                "
                select count(*) as total
                from goods g 
                where 1=1 
                    and g.com_id = :com_id 
                    $where
			";
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query,['com_id' => $com_id]);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = /** @lang text */
            "
			select
				'' as blank
				, g.goods_no , g.goods_sub
				, ifnull( type.code_val, 'N/A') as goods_type
				, com.com_nm
				, opt.opt_kind_nm
				, brand.brand_nm
				, cat.full_nm
				, g.style_no
				, g.head_desc
				, '' as img_view
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				  )) as img
				, g.goods_nm
				, g.ad_desc
				, stat.code_val as sale_stat_cl
				, g.before_sale_price
				, g.price
				, gc.coupon_price
				, '' as sale_rate
				, g.sale_s_dt
				, g.sale_e_dt
				, ifnull(
					(select sum(good_qty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				  ) as qty
				 , ifnull(
					(select sum(wqty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				  ) as wqty
				, g.wonga
				, '' as margin_rate
				, '' as margin_amt
				, g.md_nm
				, bi.code_val as baesong_info
				, bk.code_val as baesong_kind
				, dpt.code_val as dlv_pay_type
				, g.baesong_price
				, g.point
				, g.org_nm
				, g.make
				, g.reg_dm
				, g.upd_dm
				, g.goods_location
				, g.sale_price
				, g.goods_type as goods_type_cd
				, com.com_type as com_type_d
			from goods g
				left outer join goods_coupon gc on gc.goods_no = g.goods_no and gc.goods_sub = g.goods_sub
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = g.com_id
				left outer join brand brand on brand.brand = g.brand
				left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = g.baesong_kind
				left outer join code bi on bi.code_kind_cd = 'G_BAESONG_INFO' and bi.code_id = g.baesong_info
				left outer join code dpt on dpt.code_kind_cd = 'G_DLV_PAY_TYPE' and dpt.code_id = g.dlv_pay_type
			where 1 = 1 and g.com_id = :com_id
                $where
            $orderby
			$limit
		";
        
        //$result = DB::select($query,['com_id' => $com_id]);

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute(['com_id' => $com_id]);
        $result = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if($row["img"] != ""){
                $row["img"] = sprintf("%s%s",config("shop.image_svr"),$row["img"]);
            }
            $result[] = $row;
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show_in_qty($no) {
        return view(Config::get('shop.partner.view') . '/product/prd01_show_qty', [
            'goods_no' => $no,
            'goods_sub' => 0
        ]);
    }

    public function options($no, Request $req) {
        $sql = "  -- [".Auth('partner')->user()->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
            select a.goods_no, a.goods_sub, a.goods_opt, 0 as qty 
             from goods_summary a
            where a.goods_no = '$no' and a.goods_sub = '$req->goods_sub'
              and a.use_yn = 'Y'
            order by a.seq
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function update_in_qty($goods_no, Request $req) {
        // 설정 값 얻기

        $goods_sub = $req->input('goods_sub', 0);
        $stock_date =  $req->input('stock_date', date('Ymd'));
        $invoice_no = $req->input('invoice_no', date('Ymd'));
        $options = $req->input('options');


        $user = array(
            "id" => Auth('partner')->user()->id,
            "name" => Auth('partner')->user()->com_nm
        );

        try {
            DB::beginTransaction();
            //재고 클래스 호출
            $prd = new Product($user);

            for($i=0;$i<count($options);$i++){

                $opt = $options[$i]["goods_opt"];
                $qty = $options[$i]["qty"];

                $opt_name = DB::table("goods_summary")
                    ->where("goods_no",$goods_no)
                    ->where("goods_sub",$goods_sub)
                    ->where("goods_opt",$opt)->value("opt_name");

                $check = $prd->Plus( array(
                    "type" => 1,
                    "etc" => '',
                    "qty" => $qty,
                    "goods_no" => $goods_no,
                    "goods_sub" => $goods_sub,
                    "goods_opt" => $opt,
                    "invoice_no" => $invoice_no,
                    "opt_name"	=>  $opt_name,
                    "opt_price" => '',
                    'wonga' => '',
                    'ord_no' => '',
                    'ord_opt_no' => '',
                    'ord_state' => '',
                    'opt_seq' => '',
                    "wonga_apply_yn" => "N"
                ));
                if(! $check) {
                    throw new Exception("재고조정용 발주건 또는 송장번호가 존재하지 않습니다.\\n발주건은 공급처만 가능합니다.\\n");
                }
            }

            DB::commit();
            return response()->json(null, 201);
        } catch(Exception $e){
            DB::rollback();
            return response()->json(['msg' => $e->getMessage()], 500);
        }
    }

    public function update_state(Request $request)
	{
        $goods_nos		= $request->input('goods_no');
        $chg_sale_stat	= $request->input('chg_sale_stat');

        $user	= array(
			"id"	=> Auth('partner')->user()->com_id,
			"name"	=> Auth('partner')->user()->com_nm
			//"id" => Auth('partner')->user()->com_id,
			//"name" => Auth('partner')->user()->com_nm
        );

        $prd		= new Product($user);
        $category	= new Category($user, "DISPLAY");

        DB::beginTransaction();

        $success	= 0;
        $fail		= 0;

        for( $i = 0; $i < count($goods_nos); $i++ )
		{
            $goods_no	= $goods_nos[$i];
            $prd->SetGoodsNo($goods_no);

            if( $chg_sale_stat == 30 )
			{
                $category->SetSeq($goods_no, "bottom");
            }

            $updates	= $prd->UpdateState($chg_sale_stat);
            $success	+= $updates[0];
            $fail		+= $updates[1];
        }

        if( $fail === 0 )
		{
            $code	= 200;
            DB::commit();
        }
		else
		{
            $code	= 200;
            DB::rollBack();
        }

        return response()->json([
            "code"	=> $code,
            "head"	=> array(
                "success"	=> $success,
                "fail"		=> $fail
            )
        ]);
    }

	public function create()
	{
		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
        $shop_domain = $conf->getConfigValue("shop", "domain");


        $user		= Auth('partner')->user();
        $com_id     = $user->com_id;
        $pay_fee    = $user->pay_fee;

        $goods = DB::table("goods")
            ->where("com_id",$com_id)
            ->where("sale_stat_cl",">=",5)
            ->orderBy("goods_no","desc")->first();


		$query = /** @lang text */
            "
			select a.class , a.class_nm
			from code_class a 
			group by class, class_nm
		";

		$class_items = DB::select($query);

        $goods_info = new \stdClass();
        $goods_info->sale_stat_cl = '5';
        $goods_info->goods_type = 'P';
        $goods_info->baesong_info = '1';
        $goods_info->baesong_kind = '2';
        $goods_info->tax_yn = 'Y';
        $goods_info->pay_fee = $pay_fee;
        $goods_info->is_unlimited = 'N';
        $goods_info->is_option_use = 'Y';


        if($goods){
            $goods_info->opt_kind_cd = $goods->opt_kind_cd;
            $goods_info->brand_cd = $goods->brand;
            $goods_info->brand_nm = $goods->brand_nm;
            // $goods_info->make = $goods->make;
            // $goods_info->org_nm = $goods->org_nm;
            $goods_info->md_id = $goods->md_id;
            $goods_info->rep_cat_cd = $goods->rep_cat_cd;
            $goods_info->cat_nm	= $this->get_cat_nm($goods_info->rep_cat_cd);
            $category	= new Category($user, "DISPLAY");
            $goods_info->rep_cat_nm	= substr( $category->Location( $goods_info->rep_cat_cd ), 0 );
        }

        $values = [
            'goods_no'		=> '',
            'goods_info'	=> $goods_info,
            'md_list'		=> SLib::getMDs(),
            'opt_cd_list'	=> SLib::getItems(),
            'com_info'		=> Auth('partner')->user(),
            'qty'			=> 0, 
            'wqty'			=> 0,
            'coupon_list'	=> [],
            'planing'		=> [],
            'modify_history'=> [],
            'type'			=> 'create',
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT',["40" => "<>"]),
            'class_items'	=> $class_items,
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),

            'g_dlv_fee'		=> $cfg_dlv_fee,
            'g_free_dlv_fee_limit'	=> $cfg_free_dlv_fee_limit,
            'g_order_point_ratio'	=> $cfg_order_point_ratio,
            'displays'          => [],
            'items'             => [],
            'shop_domain'       => $shop_domain,
            'opt2'			=> array()
        ];

        // dd($values);

		return view(Config::get('shop.partner.view') . '/product/prd01_show',
			$values
		);
	}

    public function show($goods_no, Request $req)
    {
		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
        $shop_domain = $conf->getConfigValue("shop", "domain");

        $type = $req->input('type', '');	// 단순 페이지 상태 생성:create, XXXXXXXXXXXXXX상품형식-일반/납품/기획:N(혹은 無값)/D/E

        $query = /** @lang text */
            "
            select a.class , a.class_nm
              from code_class a 
             group by class, class_nm
        ";
        $class_items = DB::select($query);

		$values = $this->_get($goods_no);

        $user	= array(
            "id"	=> Auth('partner')->user()->com_id,
            "name"	=> Auth('partner')->user()->com_nm
        );

        $goods_stats =  SLib::getCodes('G_GOODS_STAT',["40" => "<>"]);

//        $stock = new Jaego($user);
//        if($values["goods_info"]->sale_stat_cl === 30 && $stock->IsTotalQty($goods_no,0) > 0 && count($modify_hostory) >0){
//            $goods_stats =  SLib::getCodes('G_GOODS_STAT');
//        } else {
//        }

        $values = array_merge($values, [
            'opt_cd_list'		=> SLib::getItems(),
            'md_list'			=> SLib::getMDs(),
            'type'				=> $type,
            'goods_stats'		=> $goods_stats,
            'class_items'		=> $class_items,
            'goods_types'		=> SLib::getCodes('G_GOODS_TYPE'),
            'com_info'			=> (object)array("dlv_amt" => 0,"free_dlv_amt_limit" => 0),
            'g_dlv_fee'			=> $cfg_dlv_fee,
            'g_free_dlv_fee_limit'	=> $cfg_free_dlv_fee_limit,
            'g_order_point_ratio'	=> $cfg_order_point_ratio,
            'shop_domain'           => $shop_domain,
            'img_prefix'            => sprintf("%s",config("shop.image_svr"))
        ]);

        return view(Config::get('shop.partner.view') . '/product/prd01_show',$values);
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

        $coupon_list		= $this->get_coupon_info($goods_no, $goods_info->price); // 쿠폰 리스트
        $modify_history		= $this->get_history_modify($goods_no); // 상품 변경 내역
        $goods_info->cat_nm	= $this->get_cat_nm($goods_info->rep_cat_cd);
        $planing			= $this->get_planing_list($goods_no, $goods_info->goods_sub); // 전시상품 리스트

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

        return  [
            'goods_no'			=> $goods_no,
            'goods_info'		=> $goods_info,
            'goods_images'		=> $goods_images,
            'qty'				=> $qty,
            'wqty'				=> $wqty,
            'coupon_list'		=> $coupon_list,
            'modify_history'	=> $modify_history,
            'planing'			=> $planing,
            'opt2'				=> $opt2,
            'options'           => $options,
            'displays'          => $displays,
            'items'             => $items
        ];
    }

    public function get_addinfo($goods_no){

	    $query = /** @lang text */
            "
            select
                a.upd_date, a.memo, a.head_desc, a.price, a.wonga, a.margin, a.id, b.name
            from goods_modify_hist a
                inner join mgr_user b on a.id = b.id
            where a.goods_no = :goods_no
            order by a.hist_no desc
        ";
        $modify_history = DB::select($query,['goods_no' => $goods_no]);
        //$modify_hostory = array();

        $query = /** @lang text */
            "
			select
				a.goods_no,replace(b.img,'a_500', 'a_55') as img,
				c.opt_kind_nm,
				d.brand_nm,
				b.style_no,
				b.goods_nm,
				e.code_val as sale_stat_cl,
				b.price,
				a.r_goods_no,
				a.r_goods_sub
			from goods_related a
				inner join goods b on a.r_goods_no = b.goods_no and a.r_goods_sub = b.goods_sub
				inner join opt c on b.opt_kind_cd = c.opt_kind_cd and c.opt_id = 'K'
				inner join brand d on b.brand = d.brand
				inner join code e on e.code_kind_cd = 'G_GOODS_STAT' and e.code_id = b.sale_stat_cl
			where a.goods_no = :goods_no
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute(["goods_no" => $goods_no]);
        $goods_related = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if($row["img"] != ""){
                $row["img"] = sprintf("%s%s",config("shop.image_svr"),$row["img"]);
            }
            $goods_related[] = $row;
        }

        return response()->json([
            'modify_history'        => $modify_history,
            'goods_related'         => $goods_related
        ]);
	}

	public function create_goods(Request $req) {

		$user	= Auth('partner')->user();
        $com_id	= Auth('partner')->user()->com_id;
        $name	= Auth('partner')->user()->com_nm;
		$id		= $com_id;

		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
		$cfg_domain_bizest			= $conf->getConfigValue("shop","domain_bizest");
		$cfg_domain					= $conf->getConfigValue("shop","domain");

		$dlv_fee_cfg	= $req->input('dlv_fee_cfg');
		$d_category		= $req->input('d_category_s');
		$u_category		= $req->input('u_category_s');
		$is_sub			= $req->input('is_sub', 0);		// 보조 상품 유무 ( 사용안함 )

		$goods_no		= 0;
		$goods_sub		= 0;

		$price			= str_replace(',', '', $req->input('price', 0));
		$wonga			= str_replace(',', '', $req->input('wonga', 0));

		$goods_type = $req->input('goods_type');

		$point_cfg	= $req->input('point_cfg','S');
		$point_yn	= $req->input('point_yn','Y');
		$point_unit	= $req->input('point_unit','W');
		$point		= $req->input('point', 0);

		$bae_yn = $req->input('bae_yn');
		$baesong_price = $req->input('baesong_price');
		$baesong_kind = $req->input('baesong_kind', '1');

		$is_option_use = $req->input('is_option_use', 'Y');
		$goods_qty = $req->input('goods_qty', 0);

		// $good_qty =
		try {
			DB::beginTransaction();

			if( $is_sub != 0 ){
				$goods_no	= $req->input('goods_no');
				$goods_sub	= DB::selectOne("
							select max(goods_sub) + 1 as goods_sub 
							from goods
							where goods_no = $goods_no
						")->goods_sub;
			} else {
				$goods_no	= DB::selectOne("select max(goods_no) + 1 as goods_no from goods")->goods_no;
				$goods_sub	= 0;
			}

			//전시 카테고리
			if( $d_category != "" ){
				$d_category_arr	= explode(',',$d_category);
				foreach( $d_category_arr  as $key => $d_cat ){
					if( $key > 0 ) {
						$this->insert_category("DISPLAY", $d_cat, $goods_no, $goods_sub);
					}
				}
			}

			//용도 카테고리
			if($u_category != ""){
				$u_category_arr	= explode(',',$u_category);
				foreach( $u_category_arr  as $key => $u_cat ){
					if( $key > 0 ){
						$this->insert_category("ITEM", $u_cat, $goods_no, $goods_sub);
					}
				}
			}

			// 배송비 설정 - 쇼핑몰
			if( $dlv_fee_cfg == "S" ){
				$bae_yn	= "Y";
				$baesong_price	= $cfg_dlv_fee;
			}

			// 적립금 계산 - 쇼핑몰
			if( $point_cfg = "S" ){
				$point_yn	= "Y";
				$point		= $price * $cfg_order_point_ratio / 100;
			}

			//옵션관리 안함 상품의 수량 등록
			if( $is_option_use == "N" ) {
				// 기본 옵션 등록
				$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
					insert into goods_option (
						goods_no, goods_sub, type, name, required_yn, use_yn, seq, option_no, rt, ut
					) values (
						'$goods_no', '$goods_sub', 'basic', 'NONE', 'Y', 'Y', '0', null, now(), now()
					)
				";
				DB::insert($sql);

				// 기본 재고 등록
				$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
					insert into goods_summary (
						goods_no, goods_sub, opt_name, goods_opt, opt_price, good_qty, wqty,
						soldout_yn, use_yn, seq, rt, ut, bad_qty, last_date
					) values	(
						'$goods_no', '$goods_sub', 'NONE', 'none', '0', '$goods_qty', '$goods_qty',
						'N', 'Y', '0', now(), now(), 0, now()
					)
				";
				DB::insert($sql);

				if( $goods_type == "S" ){
					// 기본 매입상품 처리
					$sql = "  -- [".$user->com_id."] ". __FILE__ ." > ". __FUNCTION__ ." > ". __LINE__ ."
						insert into goods_good (
						goods_no, goods_sub, goods_opt, opt_type, opt_price, wonga, qty, invoice_no, init_qty, regi_date
						) values (
						'$goods_no', '$goods_sub', 'none', null, 0, '$wonga', '$goods_qty', '', '$goods_qty', now()
						)
					";
					DB::insert($sql);
				}
			}

			$a_goods = array(
				"goods_no"			=> $goods_no,
				"goods_sub"			=> $goods_sub,
				"head_desc"			=> $req->input('head_desc', ''),
				"goods_nm"			=> $req->input('goods_nm', ''),
				"goods_nm_eng"		=> $req->input('goods_nm_eng', ''),
				"ad_desc"			=> $req->input('ad_desc', ''),
				"opt_kind_cd"		=> $req->input('opt_kind_cd', ''),
				"brand"				=> $req->input('brand_cd', ''),
				"sale_stat_cl"		=> $req->input('sale_stat_cl', ''),
				"style_no"			=> $req->input('style_no', ''),
				"goods_type"		=> $req->input('goods_type', ''),
				"com_id"			=> $com_id,
				"com_type"			=> $req->input('com_type'),
				//"com_nm"			=> $req->input('com_nm',''),
				"make"				=> $req->input('make', ''),
				"org_nm"			=> $req->input('org_nm', ''),
				"goods_sh"			=> str_replace(',','',$req->input('goods_sh')),
				"price"				=> $price,
				"wonga"				=> $wonga,
				"baesong_info"		=> $req->input('baesong_info', '1'),
				"dlv_pay_type"		=> $req->input('dlv_pay_type', 'P'),
				"dlv_fee_cfg"		=> $dlv_fee_cfg,
				"bae_yn"			=> $bae_yn,
				"baesong_price"		=> $baesong_price,
				"baesong_kind"		=> $baesong_kind,
				"goods_location"	=> $req->input('goods_location', ''),
				"point_cfg"			=> $point_cfg,
				"point_yn"			=> $point_yn,
				"point_unit"		=> $point_unit,
				"point"				=> $point,
				"tax_yn"			=> $req->input('tax_yn', 'Y'),
				"md_id"				=> $req->input('md_id', ''),
				"md_nm"				=> $req->input('md_nm', ''),
				"is_unlimited"		=> $req->input('is_unlimited', 'N'),
				"is_option_use"		=> $is_option_use,
				"rep_cat_cd"		=> $req->input('rep_cat_cd',''),
				"goods_cont"		=> str_replace($cfg_domain, "", $req->input('goods_cont')),
				"spec_desc"			=> $req->input('spec_desc'),
				"baesong_desc"		=> $req->input('baesong_desc'),
				"admin_id"			=> $id,
				"admin_nm"			=> $name,
				"opinion"			=> $req->input('opinion'),
				"related_cfg"		=> 'A',
				"restock_yn"		=> $req->input('restock_yn', 'N'),
				"new_product_type"	=> $req->input('new_product_type', 'M'),
				"new_product_day"	=> $req->input('new_product_day', ''),
				"reg_dm"			=> DB::raw("now()")
			);

			$result = DB::table('goods')->insertGetId($a_goods, 'goods_no');

			// goods_type = P 처리 생략 ( 위탁업체 )
			// goods_wonga 테이블
			// 도매처리 생략
			// 상품 컬러 생략

			DB::commit();
			return response()->json($goods_no, 201);
		} catch(Exception $e){
			DB::rollback();
			return response()->json(['msg' => "업로드 도중 에러가 발생했습니다. 잠시 후 다시시도 해주세요."], 500);
		}
	}


    public function get_option_name($goods_no, Request $req) {
		$sql	= /** @lang text */
            "
			select 	a.type, a.name, a.required_yn, a.use_yn, a.no
			from goods_option a
				left outer join code b on b.code_kind_cd = 'G_OPTION_TYPE' and b.code_id = a.type
			where a.goods_no = '$goods_no'
			order by a.type, a.seq
		";
        $result = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }


    public function save_option_name($goods_no, Request $req) {

        try {
            DB::beginTransaction();

            $optionkinds = $req->input("optionkinds");
            for($i=0;$i<count($optionkinds);$i++){
                if($optionkinds[$i]["no"] > 0){

                    DB::table('goods_option')
                        ->where("goods_no", $goods_no)
                        ->where("no", $optionkinds[$i]["no"])
                        ->update([
                            "type" => $optionkinds[$i]["type"],
                            "kind" => "S",
                            "name" => $optionkinds[$i]["name"],
                            "required_yn" => $optionkinds[$i]["required_yn"],
                            "use_yn" => $optionkinds[$i]["use_yn"],
                            "ut" => DB::raw("now()"),
                        ]);
                } else {
                    DB::table('goods_option')
                        ->where("goods_no", $goods_no)
                        ->where("no", $optionkinds[$i]["no"])
                        ->insert([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "type" => $optionkinds[$i]["type"],
                            "kind" => "S",
                            "name" => $optionkinds[$i]["name"],
                            "required_yn" => $optionkinds[$i]["required_yn"],
                            "use_yn" => $optionkinds[$i]["use_yn"],
                            "seq" => 0,
                            "rt" => DB::raw("now()"),
                            "ut" => DB::raw("now()"),
                        ]);
                }
            }
            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function del_option_name($goods_no, Request $req) {

	    try {
            DB::beginTransaction();

            $optionkinds = $req->input("optionkinds");

            for($i=0;$i<count($optionkinds);$i++){
                if($optionkinds[$i]["no"] > 0){
                    DB::table('goods_option')
                        ->where("goods_no", $goods_no)
                        ->where("no", $optionkinds[$i]["no"])
                        ->delete();
                }
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function get_option($goods_no, Request $req) {

	    // 기본옵션
        $basic_options	= DB::table("goods_option")
                                ->where("goods_no",$goods_no)
                                ->where("type",'basic')
                                ->get();
        $options1 = [];
        $options2 = [];

        $sql = /** @lang text */
        " 
            select goods_opt, opt_price,opt_memo
            from goods_summary where goods_no = :goods_no and use_yn = 'Y' 
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["goods_no" => $goods_no]);
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $goods_opt = explode("^",$row["goods_opt"]);
            if(count($goods_opt) === 2){
                $options1[$goods_opt[0]] = $row;
                $options2[$goods_opt[1]] = $row;;
            } else {
                $options1[$goods_opt[0]] = $row;
            }
        }

        $data	= array();
        foreach($options1 as $option => $row){
            array_push($data,[
                'name' => $basic_options[0]->name,
                'option' => $option,
                'price' => $row["opt_price"],
                'memo' => $row["opt_memo"],
                'option_no' => $basic_options[0]->no,
                'no' => '1',
            ]);
        }
        foreach($options2 as $option => $row){
            array_push($data,[
                'name' => $basic_options[0]->name,
                'option' => $option,
                'price' => $row["opt_price"],
                'memo' => $row["opt_memo"],
                'option_no' => $basic_options[0]->no,
                'no' => '2',
            ]);
        }


        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($data)
            ),
            "body" => $data
        ]);
    }

    public function save_option($goods_no, Request $req) {

        try {
            DB::beginTransaction();

            $options = $req->input("options");

            $sql = /** @lang text */
                "
                select no,type,name 
                from goods_option where goods_no = :goods_no and use_yn = 'Y' 
            ";
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["goods_no" => $goods_no]);
            $basic_cnt = 0;
            $basic_options = [];
            $extra_options = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if($row["type"] === "basic"){
                    $row["idx"] = $basic_cnt;
                    $basic_options[$row["no"]] = $row;
                    $basic_cnt++;
                } else {
                    $extra_options[$row["no"]] = $row;
                }
            }

            //print_r($basic_options);
            //print_r($options);

            $options1 = [];
            $options2 = [];

            $sql = /** @lang text */
                " 
                select goods_opt, opt_price,opt_memo
                from goods_summary where goods_no = :goods_no and use_yn = 'Y' 
            ";
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["goods_no" => $goods_no]);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $goods_opt = explode("^",$row["goods_opt"]);
                if(count($goods_opt) === 2){
                    $options1[] = $goods_opt[0];
                    $options2[] = $goods_opt[1];
                } else {
                    $options1[] = $goods_opt[0];
                }
            }

            if($basic_cnt === 1) {
                for($i=0;$i<count($options);$i++){

                    //  echo $options[$i]["option"] ."\n";
                    //print_r($options1);
                    $option_no = $options[$i]["option_no"];

                    if(isset($basic_options[$option_no])){
                        if(in_array($options[$i]["option"],$options1)){
                            //echo "table update";
                            DB::table("goods_summary")
                                ->where("goods_no",$goods_no)->where("goods_sub",0)
                                ->where("goods_opt",$options[$i]["option"])
                                ->update([
                                    "opt_price" => $options[$i]["price"],
                                    "opt_memo" => $options[$i]["memo"],
                                    "ut" => DB::raw("now()")
                                ]);
                        } else {
                            //echo "table insert";
                            DB::table("goods_summary")
                                ->where("goods_no",$goods_no)->where("goods_sub",0)
                                ->where("goods_opt",$options[$i]["option"])
                                ->insert([
                                    "goods_no" => $goods_no,
                                    "goods_sub" => 0,
                                    "goods_opt" => $options[$i]["option"],
                                    "opt_name" => $options[$i]["name"],
                                    "opt_price" => $options[$i]["price"],
                                    "opt_memo" => $options[$i]["memo"],
                                    "good_qty" => 0,
                                    "wqty" => 0,
                                    "seq" => 0,
                                    "soldout_yn" => 'N',
                                    "use_yn" => 'Y',
                                    "rt" => DB::raw("now()"),
                                    "ut" => DB::raw("now()")
                                ]);
                        }
                    }
                }
            } else if($basic_cnt === 2){

                for($i=0;$i<count($options);$i++){

                    //  echo $options[$i]["option"] ."\n";
//                    print_r($options1);
                    $option_no = $options[$i]["option_no"];

                    if(isset($basic_options[$option_no])){

                        // 1차와 2차 판단
                        $idx = $basic_options[$option_no]["idx"];
                        if($idx === 0){
                            if(in_array($options[$i]["option"],$options1)){
                                //echo "table update";
                                DB::table("goods_summary")
                                    ->where("goods_no",$goods_no)->where("goods_sub",0)
                                    ->where("goods_opt", 'like',sprintf("%s^%%",$options[$i]["option"]))
                                    ->update([
                                        "opt_price" => $options[$i]["price"],
                                        "opt_memo" => $options[$i]["memo"],
                                        "ut" => DB::raw("now()")
                                    ]);
                            } else {
                                //echo "table insert";
                                $add_options = [];
                                foreach($options2 as $opt2 => $value){
                                    echo $options[$i]["option"];
                                    $opt = sprintf("%s^%s",$options[$i]["option"],$opt2);
                                    array_push($add_options,[
                                        "goods_no" => $goods_no,
                                        "goods_sub" => 0,
                                        "goods_opt" => $opt,
                                        "opt_name" => $options[$i]["name"],
                                        "opt_price" => $options[$i]["price"],
                                        "opt_memo" => $options[$i]["memo"],
                                        "good_qty" => 0,
                                        "wqty" => 0,
                                        "seq" => 0,
                                        "soldout_yn" => 'N',
                                        "use_yn" => 'Y',
                                        "rt" => DB::raw("now()"),
                                        "ut" => DB::raw("now()")
                                    ]);
                                }

                                DB::table("goods_summary")
                                    ->where("goods_no",$goods_no)->where("goods_sub",0)
                                    ->insert($add_options);
                                array_push($options1,$options[$i]["option"]);
                            }

                        } else if($idx === 1) {

                            if(in_array($options[$i]["option"],$options2)){
                                //echo "table update";
                                DB::table("goods_summary")
                                    ->where("goods_no",$goods_no)->where("goods_sub",0)
                                    ->where("goods_opt", 'like',sprintf("%%%^s",$options[$i]["option"]))
                                    ->update([
                                        "opt_price" => $options[$i]["price"],
                                        "opt_memo" => $options[$i]["memo"],
                                        "ut" => DB::raw("now()")
                                    ]);
                            } else {
                                //echo "table insert";
                                $add_options = [];
                                //print_r($options1);
                                foreach($options1 as $opt1 => $value){
                                    echo $options[$i]["option"];
                                    $opt = sprintf("%s^%s",$opt1,$options[$i]["option"]);
                                    echo "opt : $opt";
                                    array_push($add_options,[
                                        "goods_no" => $goods_no,
                                        "goods_sub" => 0,
                                        "goods_opt" => $opt,
                                        "opt_name" => $options[$i]["name"],
                                        "opt_price" => $options[$i]["price"],
                                        "opt_memo" => $options[$i]["memo"],
                                        "good_qty" => 0,
                                        "wqty" => 0,
                                        "seq" => 0,
                                        "soldout_yn" => 'N',
                                        "use_yn" => 'Y',
                                        "rt" => DB::raw("now()"),
                                        "ut" => DB::raw("now()")
                                    ]);
                                }
                                DB::table("goods_summary")
                                    ->where("goods_no",$goods_no)->where("goods_sub",0)
                                    ->insert($add_options);

                                array_push($options2,$options[$i]["option"]);

                            }

                        }
                    }
                }
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function del_option($goods_no, Request $req) {

        try {
            DB::beginTransaction();

            $options = $req->input("options");

            $sql = /** @lang text */
                "
                select no,type,name 
                from goods_option where goods_no = :goods_no and use_yn = 'Y' 
            ";
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["goods_no" => $goods_no]);
            $basic_cnt = 0;
            $basic_options = [];
            $extra_options = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if($row["type"] === "basic"){
                    $row["idx"] = $basic_cnt;
                    $basic_options[$row["no"]] = $row;
                    $basic_cnt++;
                } else {
                    $extra_options[$row["no"]] = $row;
                }
            }

            for($i=0;$i<count($options);$i++){
                if($options[$i]["no"] > 0){
                    // Basic option
                    if(isset($basic_options[$options[$i]["option_no"]])){
                        if($basic_cnt == 1){
                            DB::table('goods_summary')
                                ->where("goods_no", $goods_no)
                                ->where("goods_sub", 0)
                                ->where("goods_opt", $options[$i]["option"])
                                ->delete();
                        } else {
                            $idx = $basic_options[$options[$i]["option_no"]]["idx"];
                            if($idx === 0) {
                                DB::table('goods_summary')
                                    ->where("goods_no", $goods_no)
                                    ->where("goods_sub", 0)
                                    ->where("goods_opt", 'like',sprintf("%s^%%",$options[$i]["option"]))
                                    ->delete();
                            } else if($idx === 1){
                                DB::table('goods_summary')
                                    ->where("goods_no", $goods_no)
                                    ->where("goods_sub", 0)
                                    ->where("goods_opt", 'like',sprintf("%%%^s",$options[$i]["option"]))
                                    ->delete();
                            }
                        }
                    }
                }
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function get_stock($goods_no, Request $req) {

        $sql = /** @lang text */
            "
                select no,type,name from goods_option
                where goods_no = :goods_no
        ";
        $options_basic = DB::select($sql,array("goods_no" => $goods_no));

        // 재고 수량 얻기
        $sql = /** @lang text */
            "
            select
                a.goods_opt,good_qty, wqty
            from goods_summary a
            where goods_no = :goods_no and goods_sub = :goods_sub 
            order by a.seq,a.goods_opt
        ";
        $row = DB::select($sql,array("goods_no" => $goods_no,"goods_sub" => 0));

        $i=0;
        $a_opt1=[];
        $a_opt2=[];
        $a_jaego_qty = [];
        $a_jaego_wqty = [];

        while ($i<count($row)) {

            $goods_opt = strtoupper(addslashes($row[$i]->goods_opt));
            $qty = $row[$i]->good_qty;
            $wqty = $row[$i]->wqty;
            $a_jaego_qty[$goods_opt] = $qty;
            $a_jaego_wqty[$goods_opt] = $wqty;

            if(count($options_basic) > 1){
                $tmp = explode("^", $goods_opt);
                if($tmp[0] != "" && !in_array($tmp[0], $a_opt1)){
                    $a_opt1[] = $tmp[0];
                }
                if(isset($tmp[1]) && $tmp[1] != "" && !in_array($tmp[1], $a_opt2)){
                    $a_opt2[] = $tmp[1];
                }
            } else { // 싱글옵션
                if(!in_array($goods_opt, $a_opt1)){
                    $a_opt1[] = $goods_opt;
                }
            }
            $i++;
        }
        if(count($options_basic) > 1){
            for($i = 0; $i < count($a_opt1); $i++){
                for($j = 0; $j < count($a_opt2); $j++){
                    $key = $a_opt1[$i] ."^". $a_opt2[$j];
                    if(! array_key_exists($key, $a_jaego_qty)){
                        $a_jaego_qty[$key] = "N/A";
                    }
                    if(! array_key_exists($key, $a_jaego_wqty)){
                        $a_jaego_wqty[$key] = "N/A";
                    }
                }
            }
        }

        $a_opt=array($a_opt1,$a_opt2);

        $is_option_use = $req->input("is_option_use");
        if($is_option_use === "N"){
            if(!isset($a_jaego_qty["NONE"])){
                $a_opt = [
                    ['NONE'],[]
                ];
                $a_jaego_qty = ["NONE" => 0];
                $a_jaego_wqty = ["NONE" => 0];
            }
        }


        return response()->json([
            "code" => 200,
            "options" => $a_opt,
            "qty" => $a_jaego_qty,
            "wqty" => $a_jaego_wqty
        ]);
    }

    public function save_stock($goods_no, Request $req) {

        try {
            DB::beginTransaction();

            $is_option_use = $req->input("is_option_use");
            $stocks = $req->input("stocks");

            if($is_option_use === "N"){
                $cnt = DB::table("goods_option")
                    ->where("goods_no",$goods_no)->where("goods_sub",0)
                    ->where("type",'basic')
                    ->where("name",'NONE')->count();

                if($cnt === 0){
                    DB::table("goods_option")
                        ->where("goods_no",$goods_no)->where("goods_sub",0)
                        ->where("type",'basic')
                        ->delete();

                    DB::table("goods_summary")
                        ->where("goods_no",$goods_no)->where("goods_sub",0)
                        ->delete();

                    DB::table("goods_option")->insert([
                        "goods_no" => $goods_no,
                        "goods_sub" => 0,
                        "type" => 'basic',
                        "kind" => 'S',
                        "name" => 'NONE',
                        "required_yn" => 'Y',
                        "use_yn" => 'Y',
                        "seq" => 0,
                        "rt" => DB::raw("now()"),
                        "ut" => DB::raw("now()")
                    ]);

                    DB::table("goods_summary")->insert([
                        "goods_no" => $goods_no,
                        "goods_sub" => 0,
                        "goods_opt" => 'NONE',
                        "opt_name" => 'NONE',
                        "opt_price" => 0,
                        "good_qty" => 0,
                        "wqty" => 0,
                        "soldout_yn" => 'Y',
                        "use_yn" => 'Y',
                        "seq" => 0,
                        "rt" => DB::raw("now()"),
                        "ut" => DB::raw("now()")
                    ]);
                }
            }

            foreach($stocks as $opt => $qty){

                if(preg_match("/^opt_(.+)$/i", $opt,$m)){
                    $option = $m[1];
                    DB::table("goods_summary")
                        ->where("goods_no",$goods_no)->where("goods_sub",0)
                        ->where("goods_opt",$option)
                        ->update([
                            "good_qty" => $qty,
                            "ut" => DB::raw("now()")
                        ]);
                }
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function get_option_extra($goods_no, Request $req) {

        $sql = /** @lang text */
            " 
            select o.*
            from goods_option g inner join options o on g.no = o.option_no
            where g.goods_no = :goods_no and g.use_yn = 'Y' 
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["goods_no" => $goods_no]);
        $rows = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $rows[] = $row;
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function save_option_extra($goods_no, Request $req) {

        try {
            DB::beginTransaction();

            $optionextras = $req->input("optionextras");
            for($i=0;$i<count($optionextras);$i++){
                if($optionextras[$i]["no"] > 0){

                    DB::table('options')
                        ->where("option_no", $optionextras[$i]["option_no"])
                        ->where("no", $optionextras[$i]["no"])
                        ->update([
                            "name" => $optionextras[$i]["name"],
                            "price" => $optionextras[$i]["price"],
                            "qty" => $optionextras[$i]["qty"],
                            "wqty" => $optionextras[$i]["qty"],
                            "soldout_yn" => $optionextras[$i]["soldout_yn"],
                            "use_yn" => $optionextras[$i]["use_yn"],
                            "ut" => DB::raw("now()"),
                        ]);
                } else {
                    DB::table('options')
                        ->insert([
                            "option_no" => $optionextras[$i]["option_no"],
                            "name" => $optionextras[$i]["name"],
                            "option" => $optionextras[$i]["option"],
                            "price" => $optionextras[$i]["price"],
                            "qty" => $optionextras[$i]["qty"],
                            "wqty" => $optionextras[$i]["wqty"],
                            "soldout_yn" => 'Y',
                            "use_yn" => 'Y',
                            "seq" => 0,
                            "use_yn" => 'Y',
                            "rt" => DB::raw("now()"),
                            "ut" => DB::raw("now()"),
                        ]);
                }
            }
            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function del_option_extra($goods_no, Request $req) {

        try {
            DB::beginTransaction();

            $optionextras = $req->input("optionextras");

            for($i=0;$i<count($optionextras);$i++){
                if($optionextras[$i]["no"] > 0){
                    DB::table('options')
                        ->where("option_no", $optionextras[$i]["option_no"])
                        ->where("no", $optionextras[$i]["no"])
                        ->delete();
                }
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function goods_class(Request $req) {

        $goods_no    = $req->input('goods_no', '');
        $goods_sub   = $req->input('goods_sub', '');
        $goods_class   = $req->input('goods_class', '');

        $query = /** @lang text */
            "
          select
              0 as rownum, g.goods_no, g.goods_sub,g.class,
              class.item_001, class.item_002, class.item_003, class.item_004, class.item_005, 
              class.item_006, class.item_007, class.item_008, class.item_009, class.item_010, 
              class.item_011, class.item_012
           from goods g 
              inner join goods_class class on g.goods_no = class.goods_no and g.goods_sub = class.goods_sub and g.class = class.class
          where g.goods_no = :goods_no and g.goods_sub = :goods_sub 
      ";
        $goods_classes = (array)DB::selectone($query,['goods_no' => $goods_no,'goods_sub' => $goods_sub]);
        if($goods_classes && !empty($goods_classes["class"])){
            $goods_class = $goods_classes["class"];
        }

        $result = [];

        if($goods_class !== ""){
            $sql = /** @lang text */
                "
                select class,class_nm,item,item_nm from code_class where class = :class
            ";
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["class" => $goods_class]);
            $result = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $key = sprintf("item_%s",$row["item"]);
                $row["value"] = (isset($goods_classes[$key]))? $goods_classes[$key]:"";
                $result[] = $row;
            }
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
            ),
            "body" => $result
        ]);
    }


    public function goods_class_update(Request $req) {
		try {

            $classes = $req->input('classes', '');

            if(count($classes) > 0){

                DB::beginTransaction();

                $values = [];

                $class = $classes[0]["class"];
                $values['class'] = $class;

                for($i=0;$i<count($classes);$i++){
                    $key = sprintf("item_%s",$classes[$i]["item"]);
                    $values[$key] = $classes[$i]["value"];
                }

                DB::table('goods')
                    ->where('goods_no', $req->goods_no)
                    ->where('goods_sub', $req->goods_sub)
                    ->update(['class' => $class]);

                $where = [
                    'goods_no' => $req->goods_no,
                    'goods_sub' => $req->goods_sub
                ];

                DB::table('goods_class')->updateOrInsert($where, $values);
                DB::commit();

            }

			$code = "200";
			$msg = "";

		} catch(Exception $e){
			DB::rollback();
            $code = "500";
            $msg = $e->getMessage();
		}

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);

    }

	public function goods_class_delete(Request $req) {
		try {
			DB::beginTransaction();

            DB::table('goods')
                ->where('goods_no', $req->goods_no)
                ->where('goods_sub', $req->goods_sub)
                ->update(['class' => '']);

            DB::table('goods_class')
                ->where('goods_no', $req->goods_no)
                ->where('goods_sub', $req->goods_sub)
                ->delete();

			DB::commit();

            $code = "200";
            $msg = "";

        } catch(Exception $e){
			DB::rollback();
            $code = "500";
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);

    }

	public function update(request $request){

	    $user	= Auth('partner')->user();
		$id		= Auth('partner')->user()->com_id;
		$name	= Auth('partner')->user()->com_nm;

		$com_id = $id;
		$com_nm = $name;

		$conf	= new Conf();
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
		$cfg_domain_bizest			= $conf->getConfigValue("shop","domain_bizest");
		$cfg_domain					= $conf->getConfigValue("shop","domain");

		$goods_no			= $request->input('goods_no');
		$goods_sub			= $request->input('goods_sub');
		$head_desc			= $request->input('head_desc');
		$goods_nm			= $request->input('goods_nm');
		$goods_nm_eng		= $request->input('goods_nm_eng');
		$ad_desc			= $request->input('ad_desc');
		$brand				= $request->input('brand_cd');
		$sale_stat_cl		= $request->input('sale_stat_cl');
		$style_no			= $request->input('style_no');
		$goods_type			= $request->input('goods_type');

		$point_cfg			= $request->input('point_cfg','S');
		$point_yn			= $request->input('point_yn','Y');
		$point_unit			= $request->input('point_unit','W');
		$point				= $request->input('point', 0);

		$com_type			= $request->input('com_type');
		$opt_kind_cd		= $request->input('opt_kind_cd');
		$make				= $request->input('make');
		$org_nm				= $request->input('org_nm');
		$price				= str_replace(',', '', $request->input('price', 0));
		$wonga				= str_replace(',', '', $request->input('wonga', 0));
		$margin				= $request->input('margin');
		$tax_yn				= $request->input('tax_yn');
		$md_id				= $request->input('md_id');
		$md_nm				= $request->input('md_nm');
		$restock_yn			= $request->input('restock_yn', 'N');
		$goods_sh			= str_replace(',', '', $request->input('goods_sh', 0));
		$baesong_info		= $request->input('baesong_info');
		$baesong_kind		= $request->input('baesong_kind');
		$dlv_pay_type		= $request->input('dlv_pay_type');
		$dlv_fee_cfg		= $request->input('dlv_fee_cfg');
		$bae_yn				= $request->input('bae_yn');
		$baesong_price		= str_replace(',', '', $request->input('baesong_price', 0));
		$goods_location		= $request->input('goods_location');
		$new_product_type	= $request->input('new_product_type','M');
		$new_product_day	= str_replace('-', '', $request->input('new_product_day'));
		$is_unlimited		= $request->input('is_unlimited');
		$is_option_use		= $request->input('is_option_use');
		//$qty = $request->input('qty');
		//$wqty = $request->input('wqty');
		$goods_cont			= str_replace($cfg_domain, "", $request->input('goods_cont'));
		$spec_desc			= $request->input('spec_desc');
		$baesong_desc		= $request->input('baesong_desc');
		$opinion			= $request->input('opinion');

        $related_cfg        = $request->input('related_cfg');
		$d_category			= $request->input('d_category_s');
		$u_category			= $request->input('u_category_s');
		$rep_cat_cd			= $request->input('rep_cat_cd');

		//전시카테고리
        $d_category_arr  = explode(',',$d_category);
        $i =0;
        $cat_type = "DISPLAY";

        //카테고리 전체 삭제
        $this->delete_category($cat_type, $goods_no);

        foreach( $d_category_arr  as $d_cat ){
            if( $i > 0 ){
                // 카테고리 등록
                $this->insert_category($cat_type, $d_cat, $goods_no, $goods_sub);
            }
            $i++;
        }

		//용도카테고리
        $u_category_arr  = explode(',',$u_category);
        $i =0;
        $cat_type = "ITEM";

        //카테고리 전체 삭제
        $this->delete_category($cat_type, $goods_no);

        foreach( $u_category_arr  as $u_cat ){
            if( $i > 0 ){
                // 카테고리 등록
                $this->insert_category($cat_type, $u_cat, $goods_no, $goods_sub);
            }

            $i++;
        }

		// 배송비 설정 - 쇼핑몰
		if( $dlv_fee_cfg == "S" ){
			$bae_yn	= "Y";
			$baesong_price	= $cfg_dlv_fee;
		}

		// 적립금 계산 - 쇼핑몰
		if( $point_cfg = "S" ){
			$point_yn	= "Y";
			$point		= $price * $cfg_order_point_ratio / 100;
		}

		$query	= /** @lang text */
            "   
				update goods
					set 
						head_desc			= '".$head_desc."',
						goods_nm			= '".$goods_nm."',
						goods_nm_eng		= '".$goods_nm_eng."',
						ad_desc				= '".$ad_desc."',
						brand				= '".$brand."',
						sale_stat_cl		= '".$sale_stat_cl."',
						style_no			= '".$style_no."',
						goods_type			= '".$goods_type."',
						com_id				= '".$com_id."',
						com_type			= '".$com_type."',
						make				= '".$make."',
						org_nm				= '".$org_nm."',
						price				= '".$price."',
						wonga				= '".$wonga."',
						tax_yn				= '".$tax_yn."',
						md_id				= '".$md_id."',
						md_nm				= '".$md_nm."',
						baesong_info		= '".$baesong_info."',
						baesong_kind		= '".$baesong_kind."',
						dlv_pay_type		= '".$dlv_pay_type."',
						dlv_fee_cfg			= '".$dlv_fee_cfg."',
						bae_yn				= '".$bae_yn."',
						baesong_price		= '".$baesong_price."',
						goods_location		= '".$goods_location."',
						point_cfg			= '".$point_cfg."',
						point_yn			= '".$point_yn."',
						point_unit			= '".$point_unit."',
						point				= '".$point."',
						rep_cat_cd			= '".$rep_cat_cd."',
						new_product_type	= '".$new_product_type."',
						new_product_day		= '".$new_product_day."',
						is_unlimited		= '".$is_unlimited."',
						is_option_use		= '".$is_option_use."',
						goods_cont			= '".$goods_cont."',
						spec_desc			= '".$spec_desc."',
						baesong_desc		= '".$baesong_desc."',
						opinion				= '".$opinion."',
                        related_cfg			= '".$related_cfg."',
						opt_kind_cd			= '".$opt_kind_cd."',
						restock_yn			= '".$restock_yn."',
						goods_sh			= '".$goods_sh."',
						admin_id			= '".$id."',
						admin_nm			= '".$name."',
						upd_dm				= NOW()
				where 
					goods_no = '".$goods_no."'
		";

		$result = DB::update($query);

		//상품의 상태가 품절 및 품절(수동)일 경우 전시순서 변경 - 작업안함
		//도매그룹 판매가격 설정 - 작업안함
		//상품컬러 - 작업안함

		//상품 변경 로그 등록
		$sql	= "
			insert into goods_modify_hist (
				goods_no, goods_sub, style_no, upd_date, sale_stat_cl, price, margin, wonga
				, head_desc, memo, id, regi_date
			) values (
				'$goods_no', '$goods_sub', '$style_no', now(), '$sale_stat_cl', '$price', '$margin', '$wonga'
				, '$head_desc', '상품정보수정', '$id', now()
			)
		";
		DB::insert($sql);


		// 재고 수정
		/*
		$q_query = "
			update goods_summary
			set
				good_qty = '".$qty."',
				wqty = '".$wqty."'
			where
				goods_no = '".$goods_no."'
				limit 1
		";

		$q_result = DB::update($q_query);
		*/

		return response()->json($goods_no, 201);
	}

    private function get_coupon_info($goods_no, $price)
    {
        $query = /** @lang text */
            "  select
					coupon_no, coupon_nm,
					date_format(use_fr_date,'%Y.%m.%d') as use_fr_date,
					date_format(use_to_date,'%Y.%m.%d') use_to_date,
					use_yn,
					CASE
						WHEN coupon_apply = 'AG' THEN '전체상품'
						WHEN coupon_apply = 'SC' THEN '대표카테고리'
						WHEN coupon_apply = 'SG' THEN '상품'
					END	as coupon_apply,
					'$price' as price
					, coupon_amt_kind, coupon_amt, coupon_per
                    , if(coupon_amt_kind = 'W',coupon_amt,round(coupon_per/100 * $price)) as coupon_price
                    , $price - if(coupon_amt_kind = 'W',coupon_amt,round(coupon_per/100 * $price)) as coupon_applied_price
				from coupon
				where coupon_no in (
					select a.coupon_no
					from coupon_cat a
						inner join category_goods b on a.d_cat_cd = b.d_cat_cd and b.cat_type = 'DISPLAY'
					where b.goods_no = '$goods_no'
					union
					select coupon_no
					from coupon_goods
					where goods_no = '$goods_no'
				) and use_yn = 'Y'
				order by coupon_no desc
        ";
        $result = DB::select($query);

        return $result;

    }

    private function get_history_modify($goods_no)
	{
		$query = "
            select
                date_format(a.upd_date,'%y.%m.%d %h:%i:%s') as upd_date
                , a.memo, a.head_desc, a.price, a.wonga, a.margin, a.id, b.name
            from goods_modify_hist a
                inner join mgr_user b on a.id = b.id
            where a.goods_no = $goods_no
            order by a.hist_no desc
		";

		$result = DB::select($query);

		return $result;
	}

    private function get_cat_nm($cat_code){

        $query = "
            select group_concat(d_cat_nm order by d_cat_cd  separator ' > ') as full_nm
            from category
            where cat_type = 'DISPLAY'
                and instr('$cat_code', d_cat_cd) = 1
        ";
        $result = DB::select($query);

        return $result[0]->full_nm;

    }

    private function get_planing_list($goods_no, $goods_sub){ // 기획전 정보
        $ar_planning_list = array();

        $query = "
            select
                b.title, b.plan_show, b.plan_date_yn, b.start_date, b.end_date, b.no
            from category_goods a
                inner join planning b on a.cat_type = 'PLAN' and b.p_no = a.d_cat_cd
            where a.goods_no = '$goods_no' and a.goods_sub = '$goods_sub'
        ";
        $result = DB::select($query);

        return $result;
    }

    private function get_opt_cd_list(){
        $query = "select opt_kind_cd as 'name', opt_kind_nm as 'value' from opt where opt_id = 'K' and use_yn = 'Y' order by opt_seq";

        $result = DB::select($query);

        return $result;
    }

    private function get_md_list(){
        $query = "select id as name, concat(ifnull(name, ''),' (',id,')') as value from mgr_user where md_yn = 'Y' order by name";

        $result = DB::select($query);

        return $result;
    }

    private function get_com_info($com_id){

        $query = "
			select a.com_nm, a.com_id, a.com_type, a.margin_type, a.pay_fee, a.baesong_kind, a.baesong_info, a.md_nm, b.id as md_id,
				ifnull(a.dlv_policy,'S') as dlv_policy, a.dlv_amt, a.free_dlv_amt_limit
			from company a
				left outer join mgr_user b on a.md_nm = b.name and b.md_yn = 'Y'
			where a.com_id = '$com_id'
        ";


        $result = DB::select($query);

        if($result[0]->dlv_policy == "S"){
            $fee_query = "
				select value, mvalue from conf where type = 'delivery' and name = 'base_delivery_fee'
            ";

            $fee = DB::select($fee_query);

            $amt_query = "
				select value, mvalue from conf where type = 'delivery' and name = 'free_delivery_amt'
            ";

            $amt = DB::select($amt_query);


            $result[0]->dlv_amt = $fee[0]->value;
            $result[0]->free_dlv_amt_limit = $amt[0]->value;
        }

        return $result[0];
    }

    function delete_category($cat_type, $goods_no){
	    DB::table("category_goods")
            ->where("cat_type",$cat_type)
            ->where("goods_no",$goods_no)->delete();
    }

    function insert_category($cat_type, $d_cat, $goods_no, $goods_sub){

		$id		= Auth('partner')->user()->com_id;
		$name	= Auth('partner')->user()->com_nm;

		list($cat, $seq, $disp_yn) = explode("|", $d_cat);

/*
		$str_arr = explode(" ",$d_cat);

        $cnt = count($str_arr);
        $d_cat_cd = $str_arr[($cnt-1)];

        $disp_yn = str_replace("]", "",str_replace("[", "", $str_arr[($cnt-2)]));
        $seq = str_replace(")", "",str_replace("(", "", $str_arr[($cnt-3)]));
*/

        $where = [
            'cat_type'	=> $cat_type,
            'd_cat_cd'	=> $cat,
            'goods_no'	=> $goods_no,
            'goods_sub'	=> $goods_sub
        ];

        $values = [
            'disp_yn'	=> $disp_yn,
            'regi_date'	=> now(),
            'seq'		=> $seq,
			'admin_id'	=> $id,
			'admin_nm'	=> $name
        ];

        $row_cnt = DB::table('category_goods')
            ->where($where)
            ->count();

        if ($row_cnt > 0) return false;

        $data = array_merge($where, $values);

        DB::table('category_goods')
            ->insert($data);

        return true;
    }

    
    function addRelatedGoods(Request $request) { // 관련 상품 설정

        $goods_no = $request->input("goods_no");
        $goods_sub = $request->input("goods_sub");
        $cross_yn = $request->input("cross_yn"); // 크로스 등록
        $related_cfg = $request->input("related_cfg"); // 관련상품 등록 설정
        $related_goods = $request->input("related_goods");
        $a_goods = explode(",", $related_goods);

        try {

            DB::beginTransaction();

            $id = Auth('partner')->user()->com_id;
            $name = Auth('partner')->user()->com_nm;

            $user = array( "id" => $id, "name" => $name );

            // 상품 클래스 생성
            $goods = new Product( $user );
            $goods->SetGoodsNo($goods_no); // 현재 서비스에서 sub 번호 필요없으므로 이 메서드로 대체

            // 관련상품 설정 업데이트
            $goods->Edit( $goods_no , array("related_cfg" => $related_cfg) );

            if ( $related_cfg == "A") { // 자동 설정인 경우: 관련상품 삭제

                // 자동 설정 변경 부분은 상품 수정 시 실행해야 함.
                $sql = "DELETE
                    FROM goods_related
                    WHERE goods_no = :goods_no
                ";
                DB::delete($sql, ['goods_no' => $goods_no]);
                
            } else if ( $related_cfg == "G" ) { // 개별 상품 설정

                // 관련상품 등록
                for ( $i=0; $i < count($a_goods); $i++) {

                    list($r_goods_no, $r_goods_sub) = explode("|", $a_goods[$i]);

                    if ( $goods_no != $r_goods_no) {

                        $sql = "SELECT count(*) AS cnt FROM goods_related
                            WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub' AND r_goods_no = '$r_goods_no' AND r_goods_sub = '$r_goods_sub'
                        ";
                        $row = DB::selectOne($sql);
                        $count = $row->cnt;
                        if ($count == 0) {
                            $sql = "INSERT INTO goods_related (
                                    goods_no, goods_sub, r_goods_no, r_goods_sub, seq, rt, ut, admin_id, admin_nm
                                ) VALUES (
                                    :goods_no, :goods_sub, :r_goods_no, :r_goods_sub, '$i', NOW(), NOW(), '$id', '$name'
                                )
                            ";
                            DB::insert($sql, [
                                'goods_no' => $goods_no,
                                'goods_sub' => $goods_sub,
                                'r_goods_no' => $r_goods_no,
                                'r_goods_sub' => $r_goods_sub
                            ]);
                            $goods->SetGoodsNo($r_goods_no); // 현재 서비스에서 sub 번호 필요없으므로 이 메서드로 대체
                            
                            // 관련상품 설정 업데이트
                            $goods->Edit( $goods_no , array("related_cfg" => $related_cfg) );
                        }
                    }

                }

                if ($cross_yn == "Y") { // 관련상품 크로스 등록

                    array_push( $a_goods, $goods_no."|".$goods_sub);
                    $a_cross = $a_goods;

                    for( $i = 0; $i < count($a_goods); $i++ ){
                        list($goods_no, $goods_sub) = explode("|", $a_goods[$i]);
                        for ( $j = 0; $j < count($a_cross); $j++ ) { 
                            list($r_goods_no, $r_goods_sub) = explode("|", $a_cross[$j]);
                            if ( $goods_no != $r_goods_no) { // 등록여부 확인
                                $sql = "SELECT count(*) AS cnt FROM goods_related 
                                    WHERE goods_no = '$goods_no' AND goods_sub = '$goods_sub' AND r_goods_no = '$r_goods_no' AND r_goods_sub = '$r_goods_sub' ";
                                $row = DB::selectOne($sql);
                                $count = $row->cnt;
                                if ( $count == 0 ) {
                                    $sql = "INSERT into goods_related (
                                            goods_no, goods_sub, r_goods_no, r_goods_sub, seq, rt, ut, admin_id, admin_nm
                                        ) values (
                                            :goods_no, :goods_sub, :r_goods_no, :r_goods_sub, '$i', NOW(), NOW(), '$id', '$name'
                                        )
                                    ";
                                    DB::insert($sql, [
                                        'goods_no' => $goods_no,
                                        'goods_sub' => $goods_sub,
                                        'r_goods_no' => $r_goods_no,
                                        'r_goods_sub' => $r_goods_sub
                                    ]);
                                    $goods->SetGoodsNo($r_goods_no, $r_goods_sub);

                                    // 관련상품 설정 업데이트
                                    $goods->Edit( $goods_no , array("related_cfg" => $related_cfg) );
                                }
                            }
                        }
                    }

                }
            }
            DB::commit();
            return 1;
        } catch (Exception $e) {
            // dd($e);
            DB::rollback();
            return 0;
        }

    }

    function delRelatedGood(Request $request) { // 관련 상품 삭제

        $goods_no = $request->input("goods_no");
        $goods_sub = $request->input("goods_sub");
        $r_goods_no = $request->input("r_goods_no");
        $r_goods_sub = $request->input("r_goods_sub");
        
        try {
            $sql = "DELETE FROM goods_related
                WHERE goods_no = :goods_no AND goods_sub = :goods_sub
                AND r_goods_no = :r_goods_no AND r_goods_sub = :r_goods_sub
            ";
            DB::delete($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'r_goods_no' => $r_goods_no, 'r_goods_sub' => $r_goods_sub]);
            DB::commit();
            return 1;
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            return 0;
        }

    }

}
