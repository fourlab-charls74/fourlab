<?php

namespace App\Http\Controllers\api\cafe24;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller\partner\product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use App\Models\Auth;
use Illuminate\Support\Facades\Storage;
use PDO;

class productController extends Controller
{
    private $user = null;

    public function __construct(Request $request)
    {
        $auth = new Auth($request->bearerToken());
        if(!$auth->isAuth()){
            echo json_encode(array(
                "error" => [
                    "code" => 401,
                    "message" => "Authorization is required.",
                    "more_info" => ""
                ]
            ));
            exit;
        } else {
            $this->user = $auth->getUser();
        }
    }

    function getItemList(Request $request){

        $use_yn	= $request->input("use_yn","Y");

        $where = "";
        if ($use_yn != "")	$where .= "and use_yn = '$use_yn' ";

        $sql = "
                    select
                        opt_kind_cd as code,opt_kind_nm as name
                    from
                        opt
                    where 1=1
                    $where
        ";
        $result = DB::select($sql);
        //echo "<pre>$sql</pre>";exit;

        return response()->json([
            "data" => $result
        ]);
    }


    function getBrandList(Request $request){

        $brand_nm	= $request->input("brand_nm");
        $use_yn	= $request->input("use_yn","Y");

        $where = "";
        if ($brand_nm != "")	$where .= "and brand_nm like '$brand_nm%' ";
        if ($use_yn != "")	$where .= "and use_yn = '$use_yn' ";

        $sql = "
                    select
                        brand as code,brand_nm as name
                    from
                        brand
                    where 1=1
                    $where
                    order by brand asc
        ";

        $result = DB::select($sql);
        //echo "<pre>$sql</pre>";exit;

        return response()->json([
            "data" => $result
        ]);
    }

    function getCategoryList($cat_type = 'DISPLAY',Request $request){

        $auth = new Auth($request->bearerToken());
        $user = $auth->getUser();
        if(!isset($user["id"])){
            return response()->json([ "error" => [
                "code" => 401,
                "message" => "Authorization is required.",
                "more_info" => ""
            ]
            ]);
        }
        //$cat_type	= $request->input("cat_type","DISPLAY");
        $use_yn	= $request->input("use_yn","Y");

        $where = "";
        if ($cat_type != "")	$where .= "and cat_type = '$cat_type' ";
        if ($use_yn != "")	$where .= "and use_yn = '$use_yn' ";

        $sql = /** @lang text */
            "
            SELECT a.d_cat_cd AS code, d_cat_nm AS name,
            CASE
                WHEN LENGTH(a.d_cat_cd) = 3 THEN 1
                WHEN LENGTH(a.d_cat_cd) = 6 THEN 2
                WHEN LENGTH(a.d_cat_cd) = 9 THEN 3
                WHEN LENGTH(a.d_cat_cd) = 12 THEN 4
            END AS depth,p_d_cat_cd AS parent_code
            FROM category a
            WHERE
            cat_type = :cat_type
            ORDER BY a.d_cat_cd
        ";

        $result = DB::select($sql,["cat_type" => $cat_type]);
        //echo "<pre>$sql</pre>";exit;

        return response()->json([
            "data" => $result
        ]);
    }

    function getItemCategoryList(Request $request){

        $auth = new Auth($request->bearerToken());
        $user = $auth->getUser();
        if(!isset($user["id"])){
            return response()->json([ "error" => [
                "code" => 401,
                "message" => "Authorization is required.",
                "more_info" => ""
            ]
            ]);
        }
        $use_yn	= $request->input("use_yn","Y");

        $where = "";
        if ($use_yn != "")	$where .= "and use_yn = '$use_yn' ";

        $sql = /** @lang text */
            "
            SELECT a.d_cat_cd AS code, full_nm AS name,
            CASE
                WHEN LENGTH(a.d_cat_cd) = 3 THEN 1
                WHEN LENGTH(a.d_cat_cd) = 6 THEN 2
                WHEN LENGTH(a.d_cat_cd) = 9 THEN 3
                WHEN LENGTH(a.d_cat_cd) = 12 THEN 4
            END AS depth,p_d_cat_cd AS parent_code
            FROM category a
            WHERE
            cat_type = 'ITEM'
            and ( select count(*) from category where cat_type = 'ITEM' and p_d_cat_cd = a.d_cat_cd and use_yn = 'Y' ) = 0
            ORDER BY a.d_cat_cd
        ";

        $result = DB::select($sql);
        //echo "<pre>$sql</pre>";exit;

        return response()->json([
            "data" => $result
        ]);
    }

    function getCarrierList(Request $request){

        $rows = SLib::getCodes('DELIVERY');
        //echo "<pre>$sql</pre>";exit;

        $cafe_rows = array();
        for($i=0;$i<count($rows);$i++) {
            $row = (array)$rows[$i];
            //print_r($row);
            $cafe_rows[] = [
                "code" => $row["code_id"],
                "name" => $row["code_val"],
            ];
        }

        return response()->json([
            "data" => $cafe_rows
        ]);
    }

    function getOfficialList(Request $request){

        $query = /** @lang text */
            "
            select * from code_class order by class,item
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $data[] = [
                "parent_official_code" => (int)$row["class"],
                "parent_official_name" => $row["class_nm"],
                "official_code" => $row["item"],
                "official_name" => $row["item_nm"],
            ];
        }
        return response()->json([
            "data" => $data
        ]);
    }

    function getProduct($goods_no,Request $request){

        try {
            $good = (array)$this->_get($goods_no);

            $goods = (array)$good["goods_info"];
            $options = $good["options"];
            $option_qty = $good["option_qty"];
            $u_cat_cds = $good["items"];
            if(count($u_cat_cds) > 0){
                $u_cat_cd = $u_cat_cds[count($u_cat_cds)-1]->d_cat_cd;
            } else {
                $u_cat_cd = "";
            }
            $goods["u_cat_cd"] = $u_cat_cd;

            $cafe_status = [
                "W" => 5,
                "G" => 40,
                "T" => -10,
                "C" => -90,
            ];

            $cafe_goods_status = [
                "5" => "W",
                "7" => "W",
                "10" => "W",
                "20" => "T",
                "30" => "T",
                "40" => "G",
                "-10" => "T",
                "-90" => "C",
            ];

            $cafe_tax = [
                "T" => "Y",
                "F" => "N",
                "Z" => "",
            ];

            $goods_opt = array();
            $cafe_options = [];
            $cafe_items = [];
            if($goods["is_option_use"] == "Y" && count($options) > 0){
                if(count($options) === 1){
                    $opt = [];
                    for($i=0;$i<count($option_qty);$i++){
                        $cafe_items[] = [
                            "names" => [ $options[0]->name ],
                            "values" => [ $option_qty[$i]->goods_opt ],
                            "current_stock" => $option_qty[$i]->qty,
                            "selling_price" => $option_qty[$i]->opt_price,
                        ];
                        $opt[] = $option_qty[$i]->goods_opt;
                    }
                    $cafe_options[] = [
                        "name" => $options[0]->name,
                        "values" => array_values(array_unique($opt))
                    ];
                } else if(count($options) === 2){
                    $opt1 = [];
                    $opt2 = [];
                    for($i=0;$i<count($option_qty);$i++){
                        $opts = explode("^",$option_qty[$i]->goods_opt);
                        $opt1[] = $opts[0];
                        $opt2[] = $opts[1];
                        $cafe_items[] = [
                            "names" => [ $options[0]->name,$options[1]->name ],
                            "values" => [ $opts[0],$opts[1]],
                            "current_stock" => $option_qty[$i]->qty,
                            "selling_price" => $option_qty[$i]->opt_price,
                        ];
                    }

                    $cafe_options[] = [
                        "name" => $options[0]->name,
                        "values" => array_values(array_unique($opt1))
                    ];

                    $cafe_options[] = [
                        "name" => $options[1]->name,
                        "values" => array_values(array_unique($opt2))
                    ];
                }
            }

            if($goods["img"] != ""){
                if(config("shop.image_svr") == ""){
                    $host = $request->getHttpHost();
                    $protocol = ($request->secure())? "https://":"http://";
                    $img_svr = sprintf("%s%s",$protocol,$host);
                } else {
                    $img_svr = config("shop.image_svr");
                }
                $goods["img"] = sprintf("%s%s",$img_svr,$goods["img"]);
            }

            $tax_types = array_flip($cafe_tax);
            if(isset($tax_types[$goods["tax_yn"]])){
                $tax_type = $tax_types[$goods["tax_yn"]];
            } else {
                $tax_type = "Y";
            }

/*            $class = sprintf("%03d", $official_type);
            $goods_class = [
                "goods_no" => $goods_no,
                "goods_sub" => 0,
                "class" => $class
            ];
            foreach ($official as $key => $value) {
                $key = sprintf("item_%s", $key);
                $cnt = DB::table("code_class")->where([
                    "class" => $class, "item" => $key
                ])->count();
                if ($cnt === 0) {
                    $goods_class[$key] = $value;
                }
            }*/

            $cafe_product = [
                "market_product_code" => $goods_no,
                "product_name" => $goods["goods_nm"],
                "category_code" => $goods["rep_cat_cd"],
                "item_category" => $goods["u_cat_cd"],
                "selling_status" => $cafe_goods_status[$goods["sale_stat_cl"]],
                "custom_product_code" => $goods["style_no"],
                "description" => $goods["goods_cont"],
                "supply_price" => $goods["wonga"],
                "retail_price" => $goods["goods_sh"],
                "price" => $goods["price"],
                "shipping_fee" => $goods["baesong_price"],
                "quantity" => $goods["qty"],
                "detail_image" => $goods["img"],
                "tax_type" => $tax_type,
                "brand_code" => $goods["brand"],
                "brand_name" => $goods["brand_nm"],
                "manufacturer_name" => $goods["make"],
                "origin_place_value" => $goods["org_nm"],
                "item_code" => $goods["opt_kind_cd"],
                "item_name" => $goods["opt_kind_nm"],
                "options" => $cafe_options,
                "items" => $cafe_items,
            ];

            $class = (int)$goods["class"];
            if($class > 0){
                $cafe_product["official_type"] = $class;
                $goods_class = (array)DB::table("goods_class")->where("goods_no",$goods_no)->first();
                $code_classes = DB::table("code_class")->where("class",$goods["class"])->get();
                foreach ($code_classes as $code_class) {
                    if(empty($goods_class[sprintf("item_%s",$code_class->item)])){
                        $goods_class_value = "";
                    } else {
                        $goods_class_value = $goods_class[sprintf("item_%s",$code_class->item)];
                    }
                    $offical_class[$code_class->item] = $goods_class_value;
                }
                $cafe_product["official"] = $offical_class;
            }
            return response()->json([ "data" => $cafe_product]);
        } catch (\Exception $e){
            $errmsg = sprintf("%s %s line - %s",$e->getFile(),$e->getLine(),$e->getMessage());
            return response()->json([
                "error" => [
                    "code" => 500,
                    "message" => "Interal Error",
                    "more_info" => [$errmsg],
                ]
            ]);
        }
    }

    private function _get($goods_no){

        $query = /** @lang text */
            "
				select
					a.head_desc, a.goods_nm, a.goods_nm_eng, a.ad_desc, a.opt_kind_cd, opt.opt_kind_nm, a.goods_sub
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
                    , ifnull(
                    (select sum(good_qty) from goods_summary where goods_no = a.goods_no and goods_sub = a.goods_sub), 0
                    ) as qty
                    , ifnull(
                    (select sum(wqty) from goods_summary where goods_no = a.goods_no and goods_sub = a.goods_sub), 0
                    ) as wqty
					, ifnull(a.is_option_use,'Y') as is_option_use
					, ifnull(a.related_cfg,'A') as related_cfg
					, restock_yn
					, a.new_product_type
					, a.new_product_day
					, '' as prf, ifnull(c.dlv_policy, 'S') as dlv_policy,a.class
				from goods a
					left join brand br on br.brand = a.brand
					left join opt opt on opt.opt_id = 'K' and a.opt_kind_cd = opt.opt_kind_cd
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
                select type,kind,name,required_yn,use_yn
                from goods_option
                where goods_no = :goods_no
          ";
        $options	= DB::select($sql,['goods_no' => $goods_no]);

        $sql	= /** @lang text */
            "
                select opt_name,goods_opt,opt_price,good_qty as qty,wqty,soldout_yn
                from goods_summary
                where goods_no = :goods_no
                order by goods_opt
          ";
        $option_qty	= DB::select($sql,['goods_no' => $goods_no]);

        return  [
            'goods_no'			=> $goods_no,
            'goods_info'		=> $goods_info,
            'goods_images'		=> $goods_images,
            'qty'				=> $qty,
            'wqty'				=> $wqty,
            'coupon_list'		=> $coupon_list,
            'planing'			=> $planing,
            'opt2'				=> $opt2,
            'options'           => $options,
            'option_qty'        => $option_qty,
            'displays'          => $displays,
            'items'             => $items
        ];
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

    private function delete_category($cat_type, $goods_no){
        DB::table("category_goods")
            ->where("cat_type",$cat_type)
            ->where("goods_no",$goods_no)->delete();
    }

    private function insert_category($cat_type, $d_cat, $goods_no, $goods_sub){

        $com_id	= $this->user["id"];
        $name	= $this->user["name"];
        $id		= $com_id;

        $values = [
            'disp_yn'	=> "Y",
            'regi_date'	=> now(),
            'seq'		=> 0,
            'admin_id'	=> $id,
            'admin_nm'	=> $name
        ];

        while(strlen($d_cat) >= 3){
            $where = [
                'cat_type'	=> $cat_type,
                'd_cat_cd'	=> $d_cat,
                'goods_no'	=> $goods_no,
                'goods_sub'	=> $goods_sub
            ];

            $row_cnt = DB::table('category_goods')
                ->where($where)
                ->count();
            if($row_cnt === 0){
                $data = array_merge($where, $values);
                DB::table('category_goods')
                    ->insert($data);
            }
            $d_cat = substr($d_cat,0,strlen($d_cat)-3);
        }
        return true;
    }

    function register(Request $req){

        try {
            $com_id	= $this->user["id"];
            $name	= $this->user["name"];
            $id		= $com_id;

            $cafe_status = [
                "W" => 5,
                "G" => 40,
                "T" => -10,
                "C" => -90,
            ];

            $cafe_tax = [
                "T" => "Y",
                "F" => "N",
                "Z" => "",
            ];

            $options = $req->input("options");

            $is_option_use = (count($options) > 0)? "Y":"N";

            $sql = /** @lang text */
                "
                select md_id, md_nm,dlv_policy,dlv_amt,free_dlv_amt_limit from company c where com_id = :com_id
            ";
            $company = (array)DB::selectOne($sql,["com_id" => $com_id]);
            if(Lib::getValue($company,"dlv_policy","") === "C"){
                $dlv_amt = $req->input("shipping_fee");
            } else {
                $dlv_amt = $req->input("shipping_fee");
            }

            $req->merge([
                "goods_nm" => $req->input("product_name"),
                "head_desc" => "",
                "goods_nm_eng" => $req->input("product_name"),
                "ad_desc" => $req->input("product_tag"),
                "opt_kind_cd" => $req->input("item_code"),
                "brand" => $req->input("brand_code",'ETC'),
                "brand_nm" => $req->input("brand_name",$req->input("model_name",'기타')),
                "sale_stat_cl" => $cafe_status[$req->input("selling_status")],
                "style_no" => $req->input("custom_product_code"),
                "goods_type" => "P",
                "com_id" => $com_id,
                "com_type" => "2",
                "make" => $req->input("manufacturer_name"),
                "org_nm" => $req->input("origin_place_value"),
                "goods_sh" => $req->input("retail_price",$req->input("fixed_price")),
                "price" => $req->input("price"),
                "wonga" => $req->input("supply_price"),
                "baesong_info" => "1",  // 국내와 해외
                "dlv_pay_type" => "P",
                "dlv_fee_cfg" => Lib::getValue($company,"dlv_policy","S"),
                "bae_yn" => "Y",
                "baesong_price" => $dlv_amt,
                "baesong_kind" => "2",
                "point_cfg" => "S",
                "tax_yn" => $cafe_tax[$req->input("tax_type","Y")],
                "md_id" => Lib::getValue($company,"md_id",""),
                "md_nm" => Lib::getValue($company,"md_nm",""),
                "is_unlimited" => "N",
                "is_option_use" => $is_option_use,
                "rep_cat_cd" => $req->input("category_code"),
                "goods_cont" => $req->input("description"),
                "spec_desc" => "",
                "baesong_desc" => "",
                "opinion" => "",
                "related_cfg" => "A",
                "restock_yn" => "Y",
                "new_product_type" => "",
                "new_product_day" => $req->input("release_date"),
                "mall_prd_cd" => $req->input("ec_prd_code"),
                "options" => $req->input("options")
            ]);

            $conf	= new Conf();
            $cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
            $cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
            $cfg_order_point_ratio		= $conf->getConfigValue("point","ratio");
            $cfg_domain_bizest			= $conf->getConfigValue("shop","domain_bizest");
            $cfg_domain					= $conf->getConfigValue("shop","domain");

            $dlv_fee_cfg	= $req->input('dlv_fee_cfg');
            $d_category		= $req->input('d_cat_cd',$req->input("rep_cat_cd"));
            $u_category		= $req->input('item_category');

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
            $goods_qty = $req->input('quantity', 0);
            $official_type = $req->input('official_type');
            $official = $req->input('official');

            // $good_qty =
            try {
                DB::beginTransaction();

                do {
                    DB::table("goods_no")->insert([
                        "rt" => DB::raw("now()"),
                        "ut" => DB::raw("now()")
                    ]);
                    $goods_no = DB::getPdo()->lastInsertId();
                    $row = (array)DB::table("goods")->where("goods_no",$goods_no)->first();
                } while(isset($row["goods_no"]));

                //$goods_no	= DB::selectOne("select max(goods_no) + 1 as goods_no from goods")->goods_no;
                $goods_sub	= 0;

                $sale_stat_cl = $req->input('sale_stat_cl', '');
                if($sale_stat_cl == 40){
                    $sale_stat_cl = 10;
                }

                //전시 카테고리
                if( $d_category != "" ){
                    if(!is_array($d_category)){
                        $d_category = [ $d_category ];
                    }
                    foreach( $d_category  as $key => $d_cat ){
                        $this->insert_category("DISPLAY", $d_cat, $goods_no, $goods_sub);
                    }
                }

                //용도 카테고리
                if($u_category != ""){
                    if(!is_array($u_category)){
                        $u_category = [ $u_category ];
                    }
                    foreach( $u_category  as $key => $d_cat ){
                        $this->insert_category("ITEM", $d_cat, $goods_no, $goods_sub);
                    }
                }

                // 배송비 설정 - 쇼핑몰
                if( $dlv_fee_cfg == "S" ){
                    $bae_yn	= "Y";
                    $baesong_price	= $cfg_dlv_fee;
                }

                // 적립금 계산 - 쇼핑몰
                if( $point_cfg == "S" ){
                    $point_yn	= "Y";
                    $point		= $price * $cfg_order_point_ratio / 100;
                }

                //옵션관리 안함 상품의 수량 등록
                if( $is_option_use == "N" ) {
                    // 기본 옵션 등록
                    $sql = /** @lang text */
                        "
					insert into goods_option (
						goods_no, goods_sub, type, name, required_yn, use_yn, seq, option_no, rt, ut
					) values (
						'$goods_no', '$goods_sub', 'basic', 'NONE', 'Y', 'Y', '0', null, now(), now()
					)
				";
                    DB::insert($sql);

                    // 기본 재고 등록
                    $sql = /** @lang text */
                        "
					insert into goods_summary (
						goods_no, goods_sub, opt_name, goods_opt, opt_price, good_qty, wqty,
						soldout_yn, use_yn, seq, rt, ut, bad_qty, last_date
					) values	(
						'$goods_no', '$goods_sub', 'NONE', 'none', '0', '$goods_qty', '$goods_qty',
						'N', 'Y', '0', now(), now(), 0, now()
					)
				";
                    DB::insert($sql);
                } else {
                    $options = $req->input("options");
                    for($i=0;$i<count($options);$i++){

                        DB::table("goods_option")->insert([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "type" => 'basic',
                            "kind" => 'S',
                            "name" => $options[$i]["name"],
                            "required_yn" => 'Y',
                            "use_yn" => 'Y',
                            "seq" => 0,
                            "rt" => DB::raw("now()"),
                            "ut" => DB::raw("now()")
                        ]);
                    }

                    $items = $req->input("items");

                    $goods_option_qty = array();
                    $goods_option_price = array();

                    for($i=0;$i<count($items);$i++){
                        $goods_option_qty[join("^",$items[$i]["values"])] = $items[$i]["current_stock"];
                        $goods_option_price[join("^",$items[$i]["values"])] = $items[$i]["selling_price"];
                    }

                    if(count($options) == 1) {
                        for($i=0;$i<count($options[0]["values"]);$i++) {
                            $opt = $options[0]["values"][$i];
                            DB::table("goods_summary")->insert([
                                "goods_no" => $goods_no,
                                "goods_sub" => 0,
                                "goods_opt" => $opt,
                                "opt_name" => $options[0]["name"],
                                "opt_price" => Lib::getValue($goods_option_price,$opt,0),
                                "good_qty" => Lib::getValue($goods_option_qty,$opt,0),
                                "wqty" => Lib::getValue($goods_option_qty,$opt,0),
                                "soldout_yn" => 'Y',
                                "use_yn" => 'Y',
                                "seq" => 0,
                                "rt" => DB::raw("now()"),
                                "ut" => DB::raw("now()")
                            ]);
                        }
                    } else if(count($options) == 2){
                        for($i=0;$i<count($options[0]["values"]);$i++) {
                            for($j=0;$j<count($options[1]["values"]);$j++) {
                                $opt = sprintf("%s^%s",$options[0]["values"][$i],$options[1]["values"][$j]);
                                DB::table("goods_summary")->insert([
                                    "goods_no" => $goods_no,
                                    "goods_sub" => 0,
                                    "goods_opt" => $opt,
                                    "opt_name" => sprintf("%s^%s",$options[0]["name"],$options[1]["name"]),
                                    "opt_price" => Lib::getValue($goods_option_price,$opt,0),
                                    "good_qty" => Lib::getValue($goods_option_qty,$opt,0),
                                    "wqty" => Lib::getValue($goods_option_qty,$opt,0),
                                    "soldout_yn" => 'Y',
                                    "use_yn" => 'Y',
                                    "seq" => 0,
                                    "rt" => DB::raw("now()"),
                                    "ut" => DB::raw("now()")
                                ]);
                            }
                        }
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
                    "brand"				=> $req->input('brand_code', 'ETC'),
                    "brand_nm"			=> $req->input('brand_name', '기타'),
                    "sale_stat_cl"		=> $sale_stat_cl,
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
                    "class"             => ($official_type > 0)? sprintf("%03d",$official_type):"",
                    "track_cd"          => "cafe24api",
                    "mall_prd_cd"       => $req->input("mall_prd_cd"),
                    "reg_dm"			=> DB::raw("now()"),
                    "upd_dm"			=> DB::raw("now()")
                );

                $result = DB::table('goods')->insertGetId($a_goods, 'goods_no');

                // goods_type = P 처리 생략 ( 위탁업체 )
                // goods_wonga 테이블
                // 도매처리 생략
                // 상품 컬러 생략
                $img_url = $req->input("detail_image");
                $this->upload($img_url,$goods_no);

                if($official_type > 0){
                    $class = sprintf("%03d",$official_type);
                    $goods_class = [
                        "goods_no" => $goods_no,
                        "goods_sub" => 0,
                        "class" => $class
                    ];
                    foreach($official as $key => $value){
                        $key = sprintf("item_%s",$key);
                        $cnt = DB::table("code_class")->where([
                            "class" => $class, "item" => $key
                        ])->count();
                        if($cnt === 0){
                            $goods_class[$key] = $value;
                        }
                    }
                    DB::table("goods_class")->Insert($goods_class);
                }

                DB::commit();

                return response()->json([
                    "data" => [
                        "market_product_code" => $goods_no
                    ]
                ]);

            } catch(Exception $e){
                DB::rollback();
                return response()->json([ "error" => [
                    "code" => 500,
                    "message" => $e->getMessage(),
                    "more_info" => ""
                ]
                ]);
            }
        } catch(\Exception $e){
            $errmsg = sprintf("%s %s line - %s",$e->getFile(),$e->getLine(),$e->getMessage());
            return response()->json([
                "error" => [
                    "code" => 500,
                    "message" => "Interal Error",
                    "more_info" => [$errmsg],
                ]
            ]);
        }

    }

    function modify($goods_no,Request $request){

        try {
            $com_id	= $this->user["id"];
            $name	= $this->user["name"];
            $id		= $com_id;

            $cafe_status = [
                "W" => 5,
                "G" => 40,
                "T" => -10,
                "C" => -90,
            ];

            $cafe_tax = [
                "T" => "Y",
                "F" => "N",
                "Z" => "",
            ];

            $req = $request;

            $options = $req->input("options");

            $is_option_use = (count($options) > 0)? "Y":"N";

            $goods_sub			= $request->input('goods_sub',0);
            $goods_qty = $request->input('quantity', 0);
            $d_category		= $req->input('d_cat_cd',$req->input("rep_cat_cd"));
            $u_category		= $req->input('item_category');
            $official_type = $req->input('official_type');
            $official = $req->input('official');

            $sql = /** @lang text */
                "
                select md_id, md_nm,dlv_policy,dlv_amt,free_dlv_amt_limit from company c where com_id = :com_id
            ";
            $company = (array)DB::selectOne($sql,["com_id" => $com_id]);
            if(Lib::getValue($company,"dlv_policy","") === "C"){
                $dlv_amt = $req->input("shipping_fee");
            } else {
                $dlv_amt = $req->input("shipping_fee");
            }

            $edit_goods = [];
            if($req->has("product_name")) $edit_goods["goods_nm"] = $req->input("product_name");
            if($req->has("product_name")) $edit_goods["goods_nm_eng"] = $req->input("product_name");
            if($req->has("product_tag")) $edit_goods["ad_desc"] = $req->input("product_tag");
            if($req->has("item_code")) $edit_goods["opt_kind_cd"] = $req->input("item_code");
            if($req->has("brand_code")) $edit_goods["brand"] = $req->input("brand_code");
            if($req->has("brand_name")) $edit_goods["brand_nm"] = $req->input("brand_name");
            if($req->has("custom_product_code")) $edit_goods["style_no"] = $req->input("custom_product_code");
            if($req->has("manufacturer_name")) $edit_goods["make"] = $req->input("manufacturer_name");
            if($req->has("origin_place_value")) $edit_goods["org_nm"] = $req->input("origin_place_value");
            if($req->has("retail_price")) $edit_goods["goods_sh"] = $req->input("retail_price");
            if($req->has("price")) $edit_goods["price"] = $req->input("price");
            if($req->has("supply_price")) $edit_goods["wonga"] = $req->input("supply_price");
            if($req->has("shipping_fee")) $edit_goods["baesong_price"] = $dlv_amt;
            if($req->has("tax_type")) $edit_goods["tax_yn"] = $req->input("tax_type");
            if($req->has("category_code")) $edit_goods["rep_cat_cd"] = $req->input("category_code");
            if($req->has("description")) $edit_goods["goods_cont"] = $req->input("description");
            if($req->has("release_date")) $edit_goods["new_product_day"] = $req->input("release_date");
            if($req->has("ec_prd_code")) $edit_goods["mall_prd_cd"] = $req->input("ec_prd_code");


            $edit_goods["is_option_use"] = $is_option_use;
            $sale_stat_cl = $cafe_status[$req->input("selling_status")];
            if($sale_stat_cl !== 40){
                $edit_goods["sale_stat_cl"] =$sale_stat_cl;
            }
            if($official_type > 0) $edit_goods["class"] = sprintf("%03d",$official_type);

            $goods = (array)DB::table("goods")->where("goods_no",$goods_no)->first();
            $edit_goods["price"] = $goods["price"];
            if($goods["sale_stat_cl"] == 40){
                if(isset($edit_goods["price"])){
                    if($goods["price"] !== $edit_goods["price"]){
                        return response()->json([ "error" => [
                            "code" => 500,
                            "message" => "Can't edit the product price while it is on sale",
                            "more_info" => ""
                        ]
                        ]);
                    }
                } else {
                    $edit_goods["price"] = $goods["price"];
                }
            }

            //전시 카테고리
            if( $d_category != "" ){
                $this->delete_category("DISPLAY", $goods_no);
                if(!is_array($d_category)){
                    $d_category = [ $d_category ];
                }
                foreach( $d_category  as $key => $d_cat ){
                    $this->insert_category("DISPLAY", $d_cat, $goods_no, $goods_sub);
                }
            }

            //용도 카테고리
            if($u_category != ""){
                $this->delete_category("ITEM", $goods_no);
                if(!is_array($u_category)){
                    $u_category = [ $u_category ];
                }
                foreach( $u_category  as $key => $d_cat ){
                    $this->insert_category("ITEM", $d_cat, $goods_no, $goods_sub);
                }
            }

            //옵션관리 안함 상품의 수량 등록
            if( $is_option_use == "N" ) {

                $query = /** @lang text */
                    "
                        select name from goods_option where goods_no = :goods_no
                    ";

                $pdo = DB::connection()->getPdo();
                $stmt = $pdo->prepare($query);
                $stmt->execute(['goods_no' => $goods_no]);
                $db_opts = [];
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    $db_opts[] = strtoupper($row["name"]);
                }
                if(join("^",$db_opts) !== "NONE"){
                    DB::table('goods_option')
                        ->where([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "type" => 'basic'
                        ])->delete();

                    DB::table("goods_option")->insert([
                        "goods_no" => $goods_no,
                        "goods_sub" => 0,
                        "type" => 'basic',
                        "kind" => 'S',
                        "name" => "NONE",
                        "required_yn" => 'Y',
                        "use_yn" => 'Y',
                        "seq" => 0,
                        "rt" => DB::raw("now()"),
                        "ut" => DB::raw("now()")
                    ]);
                }

                $query = /** @lang text */
                    "
                        select goods_opt from goods_summary where goods_no = :goods_no
                    ";

                $pdo = DB::connection()->getPdo();
                $stmt = $pdo->prepare($query);
                $stmt->execute(['goods_no' => $goods_no]);
                $goods_opts = [];
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    $goods_opts[strtoupper($row["goods_opt"])] = 1;
                }

                if(isset($goods_opts["NONE"])){
                    DB::table("goods_summary")
                        ->where([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "goods_opt" => "NONE",
                        ])
                        ->update([
                            "opt_price" => 0,
                            "good_qty" => $goods_qty,
                            "wqty" => $goods_qty,
                            "ut" => DB::raw("now()")
                        ]);
                    unset($goods_opts["NONE"]);
                } else {
                    DB::table("goods_summary")->insert([
                        "goods_no" => $goods_no,
                        "goods_sub" => 0,
                        "goods_opt" => "NONE",
                        "opt_name" => 'NONE',
                        "opt_price" => 0,
                        "good_qty" => $goods_qty,
                        "wqty" => $goods_qty,
                        "soldout_yn" => 'Y',
                        "use_yn" => 'Y',
                        "seq" => 0,
                        "rt" => DB::raw("now()"),
                        "ut" => DB::raw("now()")
                    ]);
                }

                foreach($goods_opts as $goods_opt => $val){
                    DB::table("goods_summary")->where([
                        "goods_no" => $goods_no,
                        "goods_sub" => 0,
                        "goods_opt" => $goods_opt
                    ])->delete();
                }


            } else {
                $options = $req->input("options");
                $opts = [];
                for($i=0;$i<count($options);$i++) {
                    $opts[] = $options[$i]["name"];
                }

                $query = /** @lang text */
                    "
                        select name from goods_option where goods_no = :goods_no
                    ";

                $pdo = DB::connection()->getPdo();
                $stmt = $pdo->prepare($query);
                $stmt->execute(['goods_no' => $goods_no]);
                $db_opts = [];
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    $db_opts[] = $row["name"];
                }

                if(join("^",$opts) !== join("^",$db_opts)) {
                    DB::table('goods_option')
                        ->where([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "type" => 'basic'
                        ])->delete();

                    for($i=0;$i<count($options);$i++) {
                        DB::table("goods_option")->insert([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "type" => 'basic',
                            "kind" => 'S',
                            "name" => $opts[$i],
                            "required_yn" => 'Y',
                            "use_yn" => 'Y',
                            "seq" => 0,
                            "rt" => DB::raw("now()"),
                            "ut" => DB::raw("now()")
                        ]);
                    }
                }

                $items = $req->input("items");
                $goods_option_qty = array();
                $goods_option_price = array();

                for($i=0;$i<count($items);$i++){
                    $goods_option_qty[join("^",$items[$i]["values"])] = $items[$i]["current_stock"];
                    $goods_option_price[join("^",$items[$i]["values"])] = $items[$i]["selling_price"];
                }
                if(count($options) == 1) {

                    $query = /** @lang text */
                        "
                        select goods_opt from goods_summary where goods_no = :goods_no
                    ";

                    $pdo = DB::connection()->getPdo();
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['goods_no' => $goods_no]);
                    $goods_opts = [];
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        $goods_opts[$row["goods_opt"]] = 1;
                    }

                    for($i=0;$i<count($options[0]["values"]);$i++) {
                        $opt = $options[0]["values"][$i];
                        if(isset($goods_opts[$opt])){
                            DB::table("goods_summary")
                                ->where([
                                    "goods_no" => $goods_no,
                                    "goods_sub" => 0,
                                    "goods_opt" => $opt,
                                ])
                                ->update([
                                    "opt_price" => Lib::getValue($goods_option_price,$opt,0),
                                    "good_qty" => Lib::getValue($goods_option_qty,$opt,0),
                                    "wqty" => Lib::getValue($goods_option_qty,$opt,0),
                                    "ut" => DB::raw("now()")
                                ]);
                            unset($goods_opts[$opt]);
                        } else {
                            DB::table("goods_summary")->insert([
                                "goods_no" => $goods_no,
                                "goods_sub" => 0,
                                "goods_opt" => $opt,
                                "opt_name" => $options[0]["name"],
                                "opt_price" => Lib::getValue($goods_option_price,$opt,0),
                                "good_qty" => Lib::getValue($goods_option_qty,$opt,0),
                                "wqty" => Lib::getValue($goods_option_qty,$opt,0),
                                "soldout_yn" => 'Y',
                                "use_yn" => 'Y',
                                "seq" => 0,
                                "rt" => DB::raw("now()"),
                                "ut" => DB::raw("now()")
                            ]);
                        }
                    }

                    foreach($goods_opts as $goods_opt => $val){
                        DB::table("goods_summary")->where([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "goods_opt" => $goods_opt
                        ])->delete();
                    }

                } else if(count($options) == 2){

                    $query = /** @lang text */
                        "
                        select goods_opt from goods_summary where goods_no = :goods_no
                    ";

                    $pdo = DB::connection()->getPdo();
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['goods_no' => $goods_no]);
                    $goods_opts = [];
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        $goods_opts[$row["goods_opt"]] = 1;
                    }
                    for($i=0;$i<count($options[0]["values"]);$i++) {
                        for($j=0;$j<count($options[1]["values"]);$j++) {
                            $opt = sprintf("%s^%s",$options[0]["values"][$i],$options[1]["values"][$j]);
                            if(isset($goods_opts[$opt])){
                                DB::table("goods_summary")
                                    ->where([
                                        "goods_no" => $goods_no,
                                        "goods_sub" => 0,
                                        "goods_opt" => $opt,
                                    ])
                                    ->update([
                                        "opt_price" => Lib::getValue($goods_option_price,$opt,0),
                                        "good_qty" => Lib::getValue($goods_option_qty,$opt,0),
                                        "wqty" => Lib::getValue($goods_option_qty,$opt,0),
                                        "ut" => DB::raw("now()")
                                    ]);
                                unset($goods_opts[$opt]);
                            } else {
                                DB::table("goods_summary")->insert([
                                    "goods_no" => $goods_no,
                                    "goods_sub" => 0,
                                    "goods_opt" => $opt,
                                    "opt_name" => sprintf("%s^%s",$options[0]["name"],$options[1]["name"]),
                                    "opt_price" => Lib::getValue($goods_option_price,$opt,0),
                                    "good_qty" => Lib::getValue($goods_option_qty,$opt,0),
                                    "wqty" => Lib::getValue($goods_option_qty,$opt,0),
                                    "soldout_yn" => 'Y',
                                    "use_yn" => 'Y',
                                    "seq" => 0,
                                    "rt" => DB::raw("now()"),
                                    "ut" => DB::raw("now()")
                                ]);
                            }
                        }
                    }

                    foreach($goods_opts as $goods_opt => $val){
                        DB::table("goods_summary")->where([
                            "goods_no" => $goods_no,
                            "goods_sub" => 0,
                            "goods_opt" => $goods_opt
                        ])->delete();
                    }
                }
            }

            DB::table("goods")->where([
                "goods_no" => $goods_no
            ])->update($edit_goods);

            if($official_type > 0) {
                $class = sprintf("%03d", $official_type);
                $goods_class = [
                    "goods_no" => $goods_no,
                    "goods_sub" => 0,
                    "class" => $class
                ];
                foreach ($official as $key => $value) {
                    $key = sprintf("item_%s", $key);
                    $cnt = DB::table("code_class")->where([
                        "class" => $class, "item" => $key
                    ])->count();
                    if ($cnt === 0) {
                        $goods_class[$key] = $value;
                    }
                }
                DB::table("goods_class")->where("goods_no",$goods_no)->delete();
                DB::table("goods_class")->Insert($goods_class);
            }

            DB::table('goods_modify_hist')
                ->insert([
                    "goods_no" => $goods_no,
                    "goods_sub" => $goods_sub,
                    "style_no" => $edit_goods["style_no"],
                    "sale_stat_cl" => isset($edit_goods["sale_stat_cl"])? $edit_goods["sale_stat_cl"]:"",
                    "price" => $edit_goods["price"],
                    "wonga" => $edit_goods["wonga"],
                    "memo" => 'API 상품정보수정',
                    "id" => $id,
                    "upd_date" => DB::raw("now()"),
                    "regi_date" => DB::raw("now()"),
                ]);

            $img_url = $request->input("list_image");
            $this->upload($img_url,$goods_no);

            return response()->json([
                "data" => [
                    "market_product_code" => $goods_no
                ]
            ]);
        } catch(\Exception $e){
            $errmsg = sprintf("%s %s line - %s",$e->getFile(),$e->getLine(),$e->getMessage());
            return response()->json([
                "error" => [
                    "code" => 500,
                    "message" => "Interal Error",
                    "more_info" => [$errmsg],
                ]
            ]);
        }
    }

    //업로드 후 파일이 안보일 경우.
    //php artisan storage:link 실행 부탁드립니다.

    private function upload($url,$goods_no, $img_type = "a") {

        $user = Auth('partner')->user();

        $goods_sub = 0;
        $sizes = [50,62,70,100,129,55,120,160,180,270,280,320];
        $effect = [
            "quality" => 95,
            "amount" => 50,
            "radius" => 0.5,
            "threshold" => 0,
        ];
//        $image = preg_replace('/data:image\/(.*?);base64,/', '', $req->img);
//        preg_match('/data:image\/(.*?);base64,/', $req->img, $matches, PREG_OFFSET_CAPTURE);
        //print_r($matches);
//        $ext = $matches[1][0];

        $imageDownloaded = file_get_contents($url);
        if(!$imageDownloaded){
            return false;
        }
        if (!Storage::disk('local')->exists("tmp")) {
            Storage::disk('local')->makeDirectory("tmp");
        }
        $tmp_name = sprintf("/tmp/%s",uniqid("tmp_"));
        Storage::disk('local')->put($tmp_name, $imageDownloaded);
        $imageType = exif_imagetype(storage_path("/app/" . $tmp_name));
        Storage::disk('local')->delete($tmp_name);
        if($imageType !== false){
            $mimeType = explode('/', image_type_to_mime_type($imageType));
            $ext = $mimeType[1];
        } else {
            return false;
        }

        if($ext == "jpeg" || $ext == "jpg"){
            $ext = "jpg";
        } else if($ext == "png" || $ext == "gif") {
        }
        $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

        $sql = /** @lang text */
            "
            select date_format(reg_dm,'%Y%m%d') as reg_dm, img
            from goods
            where goods_no = :goods_no
            ";
        $goods = DB::selectOne($sql, array("goods_no" => $goods_no));
        $regdm = $goods->reg_dm;
        $save_path = sprintf("/images/goods_img/%s/%s", $regdm,$goods_no);
        $file_name = sprintf("%s_%s_%s.%s", $goods_no, $img_type, 500,$ext);
        $save_file = sprintf("%s/%s", $save_path, $file_name);

        try {

            /* 이미지를 저장할 경로 폴더가 없다면 생성 */
            if (!Storage::disk('public')->exists($save_path)) {
                Storage::disk('public')->makeDirectory($save_path);
            }

            //저장
            //Storage::disk('public')->put($save_file, base64_decode($image));
            Storage::disk('public')->put($save_file, $imageDownloaded);

            $src_file = public_path($save_file);

            if (file_exists($src_file)) {
                $img_info = getimagesize($src_file);

                $type = $img_info[2];
                if ($type == 1) {
                    $src_img = imagecreatefromgif($src_file);
                } else if ($type == 2) {
                    $src_img = imagecreatefromjpeg($src_file);
                } else if ($type == 3) {
                    $src_img = imagecreatefrompng($src_file);
                } else {
                    return false;
                }

                $dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type, 500));
                $this->resize($type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], 500);

                for ($i = 0; $i < count($sizes); $i++) {

                    // 임시수정 김용남 21-08-12
                    // 추가 이미지들은 img_type이 s임.
                    switch($sizes[$i]){
                        case '50':
                        case '62':
                        case '70':
                        case '100':
                        case '129':
                            $img_type_chk   = "s";
                            break;
                        default:
                            $img_type_chk   = "a";
                            break;
                    }

                    //$dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type, $sizes[$i]));
                    $dst_file = public_path(sprintf("%s/%s_%s_%s.jpg", $save_path, $goods_no, $img_type_chk, $sizes[$i]));
                    $this->resize($type, $effect, $src_img, $dst_file, $img_info[0], $img_info[1], $sizes[$i]);
                }

                DB::table('goods')
                    ->where('goods_no', $goods_no)
                    ->where('goods_sub', $goods_sub)
                    ->update([
                        'img' => $save_file,
                        'img_update' => now()
                    ]);
            }
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg]);
    }

    private function resize($type,$effect,$src_img,$dstFile,$sw,$sh,$dw,$dh = 0){

        if ($sw < $dw) {
            $dw = $sw;
            $dh = $sh;
        }

        if ($sw >= $dw) {
            $dh = ceil(($dw/$sw)*$sh);
        }

        $dst_img = imagecreatetruecolor($dw,$dh);
        imagecolorallocate($dst_img, 255, 255, 255);

        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0,$dw,$dh,$sw,$sh);

        $dst_img = $this->UnsharpMask($dst_img, 50, 0.5, 0);
        //$dst_img = WaterMark($dst_img);

        imageinterlace($dst_img);

        //echo $dstFile;

        if ($type == 1) {
            imagegif($dst_img,$dstFile);
        } else if ($type == 2) {
            imagejpeg($dst_img,$dstFile,$effect["quality"]);
        } else if ($type == 3) {
            imagepng($dst_img,$dstFile);
        }
        imagedestroy($dst_img);
    }

    /*
	Function: UnsharpMask
		UnsharpMask 처리

	Parameters:
		$img - 이미지 원본
		$amount - 적용 양 설정
		$radius - 화소 반경 설정
		$threshold - 화소 밀도 설정

	Returns:
		None

	New:
		- In version 2.1 (February 26 2007) Tom Bishop has done some important speed enhancements.
		- From version 2 (July 17 2006) the script uses the imageconvolution function in PHP
		version >= 5.1, which improves the performance considerably.


		Unsharp masking is a traditional darkroom technique that has proven very suitable for
		digital imaging. The principle of unsharp masking is to create a blurred copy of the image
		and compare it to the underlying original. The difference in colour values
		between the two images is greatest for the pixels near sharp edges. When this
		difference is subtracted from the original image, the edges will be
		accentuated.

		The Amount parameter simply says how much of the effect you want. 100 is 'normal'.
		Radius is the radius of the blurring circle of the mask. 'Threshold' is the least
		difference in colour values that is allowed between the original and the mask. In practice
		this means that low-contrast areas of the picture are left unrendered whereas edges
		are treated normally. This is good for pictures of e.g. skin or blue skies.

		Any suggenstions for improvement of the algorithm, expecially regarding the speed
		and the roundoff errors in the Gaussian blur process, are welcome.
    */
    private function UnsharpMask($img, $amount, $radius, $threshold)
    {

        ////////////////////////////////////////////////////////////////////////////////////////////////
        ////
        ////                  Unsharp Mask for PHP - version 2.1.1
        ////
        ////    Unsharp mask algorithm by Torstein Hønsi 2003-07.
        ////             thoensi_at_netcom_dot_no.
        ////               Please leave this notice.
        ////
        ///////////////////////////////////////////////////////////////////////////////////////////////

        // $img is an image that is already created within php using
        // imgcreatetruecolor. No url! $img must be a truecolor image.

        // Attempt to calibrate the parameters to Photoshop:
        if ($amount > 500) $amount = 500;
        $amount = $amount * 0.016;
        if ($radius > 50) $radius = 50;
        $radius = $radius * 2;
        if ($threshold > 255) $threshold = 255;

        $radius = abs(round($radius));     // Only integers make sense.
        if ($radius == 0) {
            return $img;
            imagedestroy($img);
        }
        $w = imagesx($img);
        $h = imagesy($img);
        $imgCanvas = imagecreatetruecolor($w, $h);
        $imgBlur = imagecreatetruecolor($w, $h);


        // Gaussian blur matrix:
        //
        //    1    2    1
        //    2    4    2
        //    1    2    1
        //
        //////////////////////////////////////////////////


        if (function_exists('imageconvolution')) { // PHP >= 5.1
            $matrix = array(
                array(1, 2, 1),
                array(2, 4, 2),
                array(1, 2, 1)
            );
            imagecopy($imgBlur, $img, 0, 0, 0, 0, $w, $h);
            imageconvolution($imgBlur, $matrix, 16, 0);
        } else {

            // Move copies of the image around one pixel at the time and merge them with weight
            // according to the matrix. The same matrix is simply repeated for higher radii.
            for ($i = 0; $i < $radius; $i++) {
                imagecopy($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
                imagecopymerge($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
                imagecopymerge($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
                imagecopy($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

                imagecopymerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333); // up
                imagecopymerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
            }
        }

        if ($threshold > 0) {
            // Calculate the difference between the blurred pixels and the original
            // and set the pixels
            for ($x = 0; $x < $w - 1; $x++) { // each row
                for ($y = 0; $y < $h; $y++) { // each pixel

                    $rgbOrig = ImageColorAt($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    // When the masked pixels differ less from the original
                    // than the threshold specifies, they are set to their original value.
                    $rNew = (abs($rOrig - $rBlur) >= $threshold)
                        ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
                        : $rOrig;
                    $gNew = (abs($gOrig - $gBlur) >= $threshold)
                        ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
                        : $gOrig;
                    $bNew = (abs($bOrig - $bBlur) >= $threshold)
                        ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
                        : $bOrig;


                    if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
                        $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
                        ImageSetPixel($img, $x, $y, $pixCol);
                    }
                }
            }
        } else {
            for ($x = 0; $x < $w; $x++) { // each row
                for ($y = 0; $y < $h; $y++) { // each pixel
                    $rgbOrig = ImageColorAt($img, $x, $y);
                    $rOrig = (($rgbOrig >> 16) & 0xFF);
                    $gOrig = (($rgbOrig >> 8) & 0xFF);
                    $bOrig = ($rgbOrig & 0xFF);

                    $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                    $rBlur = (($rgbBlur >> 16) & 0xFF);
                    $gBlur = (($rgbBlur >> 8) & 0xFF);
                    $bBlur = ($rgbBlur & 0xFF);

                    $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                    if ($rNew > 255) {
                        $rNew = 255;
                    } elseif ($rNew < 0) {
                        $rNew = 0;
                    }
                    $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                    if ($gNew > 255) {
                        $gNew = 255;
                    } elseif ($gNew < 0) {
                        $gNew = 0;
                    }
                    $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                    if ($bNew > 255) {
                        $bNew = 255;
                    } elseif ($bNew < 0) {
                        $bNew = 0;
                    }
                    $rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
                    ImageSetPixel($img, $x, $y, $rgbNew);
                }
            }
        }
        imagedestroy($imgCanvas);
        imagedestroy($imgBlur);

        return $img;
    }
}





