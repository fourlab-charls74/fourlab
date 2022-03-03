<?php

namespace App\Http\Controllers\partner\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Components\SLib;

class prd07Controller extends Controller
{
    public function index(Request $req) {
        $goods_nos = $req->input('goods_nos', '');

        $opt_cd_list = $this->get_opt_cd_list();

        $values = [
            'goods_nos' => $goods_nos,
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'opt_cd_list' => $opt_cd_list
        ];
        return view( Config::get('shop.partner.view') . '/product/prd07',$values);
    }

    public function search(Request $req) {
      $goods = [];
      $goods_datas = explode(",", $req->input('goods_nos', ''));

      $cfg_img_size_list = 'a_50';
      $cfg_img_size_real = 'a_500';

      foreach($goods_datas as $datas) {
        $data = explode("_", $datas);
        $no = $data[0];
        $sub = $data[1];
        $wheres = "and goods_no = '$no' and goods_sub = '$sub'";

        $sql = "
          SELECT
            CONCAT(g.goods_no, '_' ,g.goods_sub) as id
            ,'' as blank, g.goods_no, g.goods_sub, g.style_no, opt.opt_kind_nm
            , brand.brand_nm, c.full_nm, stat.code_val as sale_stat_nm
            , '' as img, g.head_desc, g.goods_nm, g.ad_desc
            , g.price, '' as margin_rate, g.wonga
            , g.price as mod_price, '' as mod_margin_rate, g.wonga as mod_wonga
            , bi.code_val as bae_info, bk.code_val as bae_kind
            , dpt.code_val as dlv_pay_type_nm, g.dlv_fee_cfg, g.bae_yn, g.baesong_price
            , g.org_nm, g.make, if(g.restock_yn = '', 'N', ifnull(g.restock_yn, 'N')) as restock_yn
            , g.goods_cont, g.spec_desc, g.baesong_desc, g.opinion
            , g.opt_kind_cd, g.brand, g.rep_cat_cd, g.sale_stat_cl
            , g.baesong_info, g.baesong_kind, g.dlv_pay_type
            , 'N' as category_yn
            , cp.com_type, g.goods_type
            , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
              select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
              from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
              )) as img_s_62
          FROM
            goods g
            LEFT OUTER JOIN opt opt ON opt.opt_kind_cd = g.opt_kind_cd AND opt.opt_id = 'K'
            LEFT OUTER JOIN brand brand ON brand.brand = g.brand
            LEFT OUTER JOIN category c ON c.d_cat_cd = g.rep_cat_cd AND c.cat_type  = 'DISPLAY'
            LEFT OUTER JOIN company cp ON g.com_id = cp.com_id
            LEFT OUTER JOIN code stat ON stat.code_kind_cd = 'G_GOODS_STAT' AND g.sale_stat_cl = stat.code_id
            LEFT OUTER JOIN code bk ON bk.code_kind_cd = 'G_BAESONG_KIND' AND bk.code_id = g.baesong_kind
            LEFT OUTER JOIN code bi ON bi.code_kind_cd = 'G_BAESONG_INFO' AND bi.code_id = g.baesong_info
            LEFT OUTER JOIN code dpt ON dpt.code_kind_cd = 'G_DLV_PAY_TYPE' AND dpt.code_id = g.dlv_pay_type
          WHERE 1=1
            $wheres
        ";
        $result = DB::selectOne($sql);

        // 마진율 계산
        $price = $result->price;
        $wonga = $result->wonga;
        $margin_amt = $price - $wonga;
        $price == 0 && $price = 1;
        $margin_rate = round(($margin_amt / $price * 100), 2);

        $result->margin_rate = $margin_rate;
        $result->mod_margin_rate = $margin_rate;

        $goods[] = $result;
      }

      return response()->json([
        "code" => 200,
        "head" => array(
            "total" => count($goods)
        ),
        "body" => $goods
      ]);
    }

    public function update(Request $req){
        $user = Auth('partner')->user();
        $goods_no = $req->goods_no;

        $row = DB::table('goods')
                ->where("goods_no", $req->goods_no)
                ->where("goods_sub", $req->goods_sub)
                ->first();

        $product = array(
            "brand"			=> $req->brand_nm,
            "sale_stat_cl"	=> $req->sale_stat_cl,
            "goods_nm"		=> $req->goods_nm,
            "price"			=> $req->mod_price,
            "wonga"			=> $req->mod_wonga,
            "dlv_fee_cfg"	=> $req->dlv_fee_cfg,
            "bae_yn"		=> $req->bae_yn,
            "baesong_price"	=> $req->baesong_price,
            "org_nm"		=> $req->org_nm,
            "make"			=> $req->make,
            "goods_cont"	=> $req->goods_cont,
            "spec_desc"		=> $req->spec_desc,
            "baesong_desc"	=> $req->baesong_desc,
            "opinion"		=> $req->opinion,
            "restock_yn"	=> $req->restock_yn,
            "admin_id"		=> $user->com_id,
            "admin_nm"		=> $user->name,
            "opt_kind_cd" => $req->opt_kind_cd,
            "reg_dm"		=> now(),
            "upd_dm"		=> now()
        );

        try {
            DB::transaction(function () use ($product, $user, $goods_no) {
                $p_user = array(
                    "id" => $user->com_id,
                    "name" => $user->name
                );

                $prd = new Product($p_user);

                $prd->Edit($goods_no, $product);
                //$d_cat_cd = $row["rep_cat_cd"];
                //$u_cat_cd = $row["u_cat_cd"];

                // 카테고리 추가
                $stock = new Stock($user);
                //$stock->Plus();
                //옵션

            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }



    }
    
    private function get_opt_cd_list(){
      $query = "select opt_kind_cd as 'name', opt_kind_nm as 'value' from opt where opt_id = 'K' and use_yn = 'Y' order by opt_seq";

      $result = DB::select($query);

      return $result;
  }

}
