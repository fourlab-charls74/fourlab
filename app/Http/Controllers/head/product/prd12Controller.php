<?php

namespace App\Http\Controllers\head\product;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Conf;
use App\Models\Category;

class prd12Controller extends Controller
{
    public function index() {

        $conf = new Conf();
        $domain = $conf->getConfig("shop","domain");

        $values = [
            'plan_types' => SLib::getCodes('G_PLAN_TYPE'),
            'plan_kinds' => SLib::getCodes('G_PLAN_KIND'),
            'is_shows' => SLib::getCodes('IS_SHOW'),
            'domain' => $domain,
        ];

        return view( Config::get('shop.head.view') . '/product/prd12',$values);
    }

    public function create(){

        $plan = new \stdClass();
        $plan->plan_type = 1;
        $plan->plan_kind = 1;
        $plan->is_show = "Y";
        $plan->p_no = "";


        $times = array();
        for($i=0;$i<24;$i++){
            array_push($times, sprintf("%02d", $i));
        }

        $values = [
            'code' => "",
            'plan' => $plan,
            'is_shows' => SLib::getCodes('IS_SHOW'),
            'plan_types' => SLib::getCodes('G_PLAN_TYPE'),
            'plan_kinds' => SLib::getCodes('G_PLAN_KIND'),
            'times' => $times,
            'start_date' => date("Y-m-d")
        ];

        return view( Config::get('shop.head.view') . '/product/prd12_show',$values);
    }

    public function show($code) {

        $conf = new Conf();
        $domain = $conf->getConfig("shop","domain");

        $sql = /** @lang text */
            "
				select a.p_no, a.d_cat_cd, a.plan_type, a.plan_kind, a.title, a.plan_date_yn, a.start_date, a.end_date, a.url
					, a.plan_img, a.plan_top_img, a.plan_top_mobile_img
					, a.plan_preview_img, a.map, a.is_show, a.plan_show, a.promotion1, a.promotion2
					, a.disp_prd_yn, a.disp_prd_pc, a.disp_prd_mobile
					, a.keyword, a.skin
					, b.sale_yn, b.sale_kind, b.sale_amt, b.sale_ratio
					, ( select count(*) from category 
					        where cat_type = 'PLAN' and p_d_cat_cd = a.p_no) as folder_cnt 
				from planning a
					left outer join category b on a.p_no = b.d_cat_cd and b.cat_type = 'PLAN'
				where a.no = :code
            ";
        $plan = DB::selectOne($sql,array("code" => $code));

        $category = new \stdClass();
        $category->d_cat_cd = $plan->p_no;

        if($plan->folder_cnt > 0){

            $sql = /** @lang text */
                "
                select
                    c.d_cat_cd,c.tpl_kind,c.header_html,c.sale_yn,c.sale_amt,c.sale_ratio
                from category c
                where c.cat_type = 'PLAN' and c.p_d_cat_cd = :p_d_cat_cd
                order by c.seq, c.d_cat_cd limit 0,1
            ";
            $category = DB::selectone($sql,array("p_d_cat_cd" => $plan->p_no));
        }

        $user = [
            'id'	=> Auth('head')->user()->id,
            'name'	=> Auth('head')->user()->name
        ];
        $d_cat_cd = $plan->d_cat_cd;
        $category = new Category($user, "DISPLAY");
		$categories = array();
		if ($d_cat_cd != "") {
			$d_cat_cd = substr($d_cat_cd, 1);
			$d_cat_cds = explode("s", $d_cat_cd);
			for($i=0;$i<sizeof($d_cat_cds);$i++){
				if(!empty($d_cat_cds[$i])){
					$categories[$d_cat_cds[$i]] = $category->Location($d_cat_cds[$i]);
				}
			}
		}

        $times = array();
        for($i=0;$i<24;$i++){
            array_push($times, sprintf("%02d", $i));
        }

        $values = [
            'code' => $code,
            'plan' => $plan,
            'category' => $category,
            'd_category' => $categories,
            'is_shows' => SLib::getCodes('IS_SHOW'),
            'plan_types' => SLib::getCodes('G_PLAN_TYPE'),
            'plan_kinds' => SLib::getCodes('G_PLAN_KIND'),
            'times' => $times,
            'start_date' => date("Y-m-d"),
            'domain' => $domain,
        ];

        return view( Config::get('shop.head.view') . '/product/prd12_show',$values);
    }

    public function search(Request $req) {

		$plan_type	= $req->input('plan_type', '');
		$plan_kind	= $req->input('plan_kind', '');
		$title		= $req->input('title', '');
		$is_show	= $req->input('is_show', 'Y');
        $limit      = $req->input("limit", 100);

        $page = $req->input("page",1);
        if ($page < 1 or $page == "") $page = 1;

        $where = "";

		if ($plan_kind != "")	$where .= " and a.plan_kind = '" . Lib::quote($plan_kind) . "'";
		if ($plan_type != "")	$where .= " and a.plan_type = '" . Lib::quote($plan_type) . "'";
		if ($is_show != "")	$where .= " and a.is_show = '" . Lib::quote($is_show) . "'";
		if ($title != "")		$where .= " and a.title like '%" . Lib::quote($title) . "%' ";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            // 갯수 얻기
            $sql = /** @lang text */
                " 
				select count(*) as total
				from planning a
				where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $total = $row->total;
            if($total > 0){
                $page_cnt = (int)(($total-1)/$page_size) + 1;
            }
        }

		$sql = /** @lang text */
            "
			select '' as type, a.no,
				cd.code_val as plan_type, cd2.code_val as plan_kind, a.title, 
				a.plan_date_yn,a.start_date,a.end_date, a.disp_prd_yn,
                if(( select count(*) from category where cat_type = 'PLAN' and d_cat_cd like concat(a.p_no,'%')) > 1,'Y','N') as folder_yn, 

				if(	(select count(*) from category where cat_type = 'PLAN' and d_cat_cd like concat(a.p_no,'%')) <= 1,
					(select count(*) from category_goods where cat_type = 'PLAN' and d_cat_cd = a.p_no),
					(select count(*) from category_goods where cat_type = 'PLAN' and d_cat_cd like concat(a.p_no,'%'))
				) as cnt,

                a.p_cnt, '보기' as preview, if(a.is_show = 1,'Y','N') as is_show,
				a.admin_name , a.regi_date, a.upd_date,
				a.p_no, a.d_cat_cd, a.url as url
			from planning a
				left outer join code cd on cd.code_kind_cd = 'G_PLAN_TYPE' and cd.code_id = a.plan_type
				left outer join code cd2 on cd2.code_kind_cd = 'G_PLAN_KIND' and cd2.code_id = a.plan_kind
			where 1=1 $where
			order by no desc
			limit $startno,$page_size
        ";
        $rows = DB::select($sql);

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

    public function store(Request $request) {

        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $p_no = $request->input("p_no","");
        $p_d_cat_cd = $p_no;

        $sql = /** @lang text */
            "
			select ifnull(max(right(d_cat_cd,3)),0)+1 as cat_cd
			from category
			where cat_type = :cat_type and p_d_cat_cd = :d_cat_cd
		";
        $row = DB::selectOne($sql,array("cat_type" => 'PLAN','d_cat_cd' => $p_no));
        $p_no = sprintf("%s%03d",$p_no,$row->cat_cd);

        $plan_type = $request->input('plan_type','1');
        $plan_kind = $request->input('plan_kind','1');
        $title = $request->input('subject');
        $plan_date_yn = $request->input('plan_date_yn','N');
        $start_date = $request->input('start_date');
        $start_time = $request->input('start_time');
        $end_date = $request->input('end_date');
        $end_time = $request->input('end_time');
        $url = $request->input('url','');
        $is_show = $request->input('is_show','N');
        $plan_show = $request->input('plan_show','');
        $promotion1 = $request->input('promotion1','');
        $promotion2 = $request->input('promotion2','');
        $map = $request->input('map');

        $disp_prd_yn = $request->input('disp_prd_yn', 'N');
        $disp_prd_pc = $request->input('disp_prd_pc');
        $disp_prd_mobile = $request->input('disp_prd_mobile');

        if($start_date != "" && $end_date != ""){
            $start_date = sprintf("%s%s", str_replace("-","",$start_date), $start_time);
            $end_date = sprintf("%s%s", str_replace("-","",$end_date), $end_time);
        }

        $plan = [
            'p_no' => $p_no,
            'd_cat_cd' => "",
            'plan_type' => $plan_type,
            'plan_kind' => $plan_kind,
            'title' => $title,
            'plan_date_yn' => $plan_date_yn,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'url' => $url,
            'map' => "",
            'is_show' => $is_show,
            'keyword' => "",
            'regi_date' => DB::raw('now()'),
            'upd_date' => DB::raw('now()'),
            'admin_id' => $id,
            'admin_name' => $name,
            'seq' => 0,
            'plan_show' => $plan_show,
            'promotion1' => $promotion1,
            'promotion2' => $promotion2,
            'map' => $map,
            'skin' => '',
            'disp_prd_yn' => $disp_prd_yn,
            'disp_prd_pc' => $disp_prd_pc,
            'disp_prd_mobile' => $disp_prd_mobile
        ];

        $plan_img_url = $request->input('plan_img_url','');
        $plan_top_img_url = $request->input('plan_top_img_url','');
        $plan_preview_img_url = $request->input('plan_preview_img_url','');
        $img = $this->image_save($p_no,$plan_img_url,$plan_top_img_url,$plan_preview_img_url);

        $plan["plan_img"] = $img["plan_img"];
        $plan["plan_top_img"] = $img["plan_top_img"];
        $plan["plan_preview_img"] = $img["plan_preview_img"];

        $category = [
            'cat_type' => 'PLAN',
            'd_cat_cd' => $p_no,
            'd_cat_nm' => $title,
            'p_d_cat_cd' => $p_d_cat_cd,
            'site' => 'HEAD_OFFICE',
            'type' => 'P',
            'tpl_kind' => 'A',
            'sale_yn' => '',
            'sale_kind' => '',
            'sale_amt' => '',
            'sale_ratio' => '',
            'use_yn' => 'Y',
            'header_html' => '',
            'admin_id' => $id,
            'admin_nm' => $name,
            'regi_date' => DB::raw('now()'),
            'upd_date' => DB::raw('now()'),
            'full_nm' => '',
            'seq' => ''
        ];

        try {
            DB::transaction(function () use (&$result, $plan,$category) {
                DB::table('category')->insert($category);
                DB::table('planning')->insert($plan);
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code,'msg' => $msg]);
    }

    public function update($code, Request $request){

        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $plan_type = $request->input('plan_type','1');
        $plan_kind = $request->input('plan_kind','1');
        $title = $request->input('subject');
        $plan_date_yn = $request->input('plan_date_yn','N');
        $start_date = $request->input('start_date');
        $start_time = $request->input('start_time');
        $end_date = $request->input('end_date');
        $end_time = $request->input('end_time');
        $url = $request->input('url','');
        $is_show = $request->input('is_show','N');
        $plan_show = $request->input('plan_show');
        $promotion1 = $request->input('promotion1');
        $promotion2 = $request->input('promotion2');
        $map = $request->input('map');

        $disp_prd_yn = $request->input('disp_prd_yn', 'N');
        $disp_prd_pc = $request->input('disp_prd_pc');
        $disp_prd_mobile = $request->input('disp_prd_mobile');

        if($start_date != "" && $end_date != ""){
            $start_date = sprintf("%s%s", str_replace("-","",$start_date), $start_time);
            $end_date = sprintf("%s%s", str_replace("-","",$end_date), $end_time);
        }

        $plan = [
            'plan_type' => $plan_type,
            'plan_kind' => $plan_kind,
            'title' => $title,
            'plan_date_yn' => $plan_date_yn,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'url' => $url,
            'is_show' => $is_show,
            'keyword' => "",
            'upd_date' => DB::raw('now()'),
            'admin_id' => $id,
            'admin_name' => $name,
            'plan_show' => $plan_show,
            'promotion1' => $promotion1,
            'promotion2' => $promotion2,
            'map' => $map,
            'skin' => '',
            'disp_prd_yn' => $disp_prd_yn,
            'disp_prd_pc' => $disp_prd_pc,
            'disp_prd_mobile' => $disp_prd_mobile
        ];

        $sql = /** @lang text */
            " 
				select p_no
				from planning a
				where no = :no
			";
        $row = DB::selectOne($sql,array("no" => $code));
        $p_no = $row->p_no;

        $plan_img_url = $request->input('plan_img_url','');
        $plan_top_img_url = $request->input('plan_top_img_url','');
        $plan_preview_img_url = $request->input('plan_preview_img_url','');
        $img = $this->image_save($p_no,$plan_img_url,$plan_top_img_url,$plan_preview_img_url);

        if($img["plan_img"] != "") $plan["plan_img"] = $img["plan_img"];
        if($img["plan_top_img"] != "") $plan["plan_top_img"] = $img["plan_top_img"];
        if($img["plan_preview_img"] != "") $plan["plan_preview_img"] = $img["plan_preview_img"];

        try {
            DB::transaction(function () use (&$result, $code,$plan) {
                DB::table('planning')
                    ->where('no','=',$code)
                    ->update($plan);
            });
            $code = 200;
        } catch (Exception $e) {
            $code = 500;
        }

        return response()->json(['code' => $code]);
    }

    public function delete($code, Request $req) {
        try {
            DB::transaction(function () use (&$result,$code) {
                DB::table('planning')->where('no', $code)->delete();
            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function goods_search($no,Request $req) {

        $d_cat_cd = $req->input('d_cat_cd','');

        if($d_cat_cd == ''){
            $sql = /** @lang text */
                " 
				select p_no
				from planning a
				where no = :no
			";
            $row = DB::selectOne($sql,array("no" => $no));
            $d_cat_cd = $row->p_no;
        }

        $sql = /** @lang text */
            "SELECT
                '' as type, a.goods_no, a.style_no, '' as img, a.head_desc, a.goods_nm, a.ad_desc, 
                cd.code_val as sale_stat_cl, a.goods_sh,a.price, c.com_nm, a.com_id, 
                ifnull(round((a.before_sale_price - a.price) / a.before_sale_price * 100 ), 0) as sale_rate, 
                date_format(a.sale_s_dt, '%Y%m%d') as sale_s_dt, date_format(a.sale_e_dt, '%Y%m%d') as sale_e_dt, 
                ifnull((
                    SELECT sum(good_qty)
					FROM goods_summary
					WHERE goods_no = a.goods_no and goods_sub = a.goods_sub
				), 0) as qty, a.reg_dm, a.goods_no, a.goods_sub, replace(a.img,'a_500', 's_62') as img
            FROM category_goods cg 
                inner join goods a on cg.cat_type = 'PLAN' and cg.d_cat_cd = :p_no and cg.goods_no = a.goods_no and cg.goods_sub = cg.goods_sub
                left outer join company c on a.com_id = c.com_id
                inner join code cd on cd.code_kind_cd = 'G_GOODS_STAT' and a.sale_stat_cl = cd.code_id
            order by cg.seq
        ";

        $rows = DB::select($sql,array("p_no" => $d_cat_cd));

		foreach ($rows as $row) {
            if($row->img != ""){
                $row->img = sprintf("%s%s",config("shop.image_svr"),$row->img);
            }
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function goods_add($code,Request $request) {

        $d_cat_cd = $request->input('d_cat_cd','');
        $goods_nos = $request->input('goods_no');

        try {
            DB::transaction(function () use (&$result, $code,$d_cat_cd,$goods_nos) {

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                if($d_cat_cd === ''){
                    $d_cat_cd = DB::table('planning')
                        ->where('no', $code)->value('p_no');
                }

                $cat_type = 'PLAN';

                for($i=0;$i<count($goods_nos);$i++){
                    $cnt = DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->count();

                    if($cnt === 0){
                        $category_good = [
                            'cat_type' => $cat_type,
                            'd_cat_cd' => $d_cat_cd,
                            'goods_no' => $goods_nos[$i],
                            'goods_sub' => 0,
                            'disp_yn' => 'Y',
                            'admin_id' => $id,
                            'admin_nm' => $name,
                            'regi_date' => DB::raw('now()'),
                            'seq' => 0,
                        ];
                        DB::table('category_goods')->insert($category_good);
                    }
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function goods_del($code,Request $request) {

        $d_cat_cd = $request->input('d_cat_cd','');
        $goods_nos = $request->input('goods_nos');

        try {
            DB::transaction(function () use (&$result, $code,$d_cat_cd,$goods_nos) {

                $cat_type = 'PLAN';
                if($d_cat_cd === ''){
                    $d_cat_cd = DB::table('planning')
                        ->where('no', $code)->value('p_no');
                }

                for($i=0;$i<count($goods_nos);$i++){
                    DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->delete();
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function goods_seq($code,Request $request) {

        $d_cat_cd = $request->input('d_cat_cd','');
        $goods_nos = $request->input('goods_nos');

        try {
            DB::transaction(function () use (&$result, $code,$d_cat_cd,$goods_nos) {

                $cat_type = 'PLAN';
                if($d_cat_cd === '') {
                    $d_cat_cd = DB::table('planning')
                        ->where('no', $code)->value('p_no');
                }

                for($i=0;$i<count($goods_nos);$i++){

                    DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$d_cat_cd)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->update(['seq' => $i+1]);
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function folder_search($no,Request $req) {

        $sql = /** @lang text */
            " 
				select p_no
				from planning a
				where no = :no
			";
        $row = DB::selectOne($sql,array("no" => $no));
        $p_no = $row->p_no;

        $sql = /** @lang text */
            "
            select
                c.d_cat_cd, c.d_cat_nm,c.use_yn,c.tpl_kind,
                ( select count(*) from category_goods cg where cat_type = c.cat_type and d_cat_cd = c.d_cat_cd ) as goods_cnt
            from category c
            where c.cat_type = 'PLAN' and c.p_d_cat_cd = :p_d_cat_cd
            order by c.seq, c.d_cat_cd
        ";
        //echo "<pre>$sql</pre>";

        $rows = DB::select($sql,array("p_d_cat_cd" => $p_no));

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function folder_save($code,Request $request) {

        $folders = json_decode($request->input('data'));

        try {
            DB::transaction(function () use (&$result, $code,$folders) {

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                $p_d_cat_cd = DB::table('planning')
                    ->where('no', $code)->value('p_no');

                $cat_type = 'PLAN';

                for($i=0;$i<count($folders);$i++){
                    $d_cat_cd = DB::table('category')
                        ->where('cat_type','=',$cat_type)
                        ->where('p_d_cat_cd','=',$p_d_cat_cd)
                        ->select(DB::raw('ifnull(max(right(d_cat_cd,3)),0)+1 as next_d_cat_cd'))
                        ->value('next_d_cat_cd');

                    $d_cat_cd = sprintf("%03d%03d", $p_d_cat_cd, $d_cat_cd);

                    $category = [
                        'cat_type' => $cat_type,
                        'd_cat_cd' => $d_cat_cd,
                        'd_cat_nm' => $folders[$i]->d_cat_nm,
                        'p_d_cat_cd' => $p_d_cat_cd,
                        'use_yn' => 'Y',
                        'admin_id' => $id,
                        'admin_nm' => $name,
                        'regi_date' => DB::raw('now()'),
                        'upd_date' => DB::raw('now()'),
                        'seq' => 0,
                    ];
                    DB::table('category')->insert($category);
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function folder_del($code,Request $request) {

        $folders = $request->input('folders');

        try {
            DB::transaction(function () use (&$result, $code,$folders) {

                $cat_type = 'PLAN';

                for($i=0;$i<count($folders);$i++){
                    DB::table('category')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$folders[$i])
                        ->delete();

                    DB::table('category_goods')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$folders[$i])
                        ->delete();

                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function folder_seq($code,Request $request) {

        $folders = $request->input('folders');

        try {
            DB::transaction(function () use (&$result, $code,$folders) {

                $cat_type = 'PLAN';

                for($i=0;$i<count($folders);$i++){
                    DB::table('category')
                        ->where('cat_type','=',$cat_type)
                        ->where('d_cat_cd','=',$folders[$i])
                        ->update(['seq' => $i+1]);
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function category_search($code,Request $request) {

        $d_cat_cd = $request->input('d_cat_cd');

        $sql = /** @lang text */
            "
            select
                c.d_cat_cd, c.tpl_kind,
                c.header_html,c.sale_yn,c.sale_amt,c.sale_kind
            from category c
            where c.cat_type = 'PLAN' and c.d_cat_cd = :d_cat_cd
        ";
        //echo "<pre>$sql</pre>";
        $rows = DB::selectone($sql,array("d_cat_cd" => $d_cat_cd));

        return response()->json([
            "code" => 200,
            "body" => $rows
        ]);
    }

    public function category_save($code,Request $request) {

        $d_cat_cd = $request->input('d_cat_cd');
        $tpl_kind = $request->input('tpl_kind');
        $header_html = $request->input('header_html');
        $sale_amt = $request->input('sale_amt');
        $sale_kind = $request->input('sale_kind','P');

        if($sale_amt > 0){
            $sale_yn = 'Y';
        } else {
            $sale_yn = 'N';
        }

        $cat_type = 'PLAN';

        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $category = [
            'tpl_kind' => $tpl_kind,
            'header_html' => $header_html,
            'sale_yn' => $sale_yn,
            'sale_amt' => $sale_amt,
            'sale_kind' => $sale_kind,
            'admin_id' => $id,
            'admin_nm' => $name,
            'upd_date' => DB::raw('now()')
        ];

        try {
            DB::transaction(function () use (&$result, $code,$cat_type,$d_cat_cd,$category) {
                DB::table('category')
                    ->where("cat_type",$cat_type)
                    ->where("d_cat_cd",$d_cat_cd)
                    ->update($category);
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function image_save($plan_no,$plan_img_url,$plan_top_img_url,$plan_preview_img_url){

        $base_path = "/images/plan_img";
        $img = [
            'plan_img' => "",
            'plan_top_img' => "",
            'plan_preview_img' => "",
        ];

        if($plan_no == ""){
            $plan_no = uniqid();
        }

        /* 이미지를 저장할 경로 폴더가 없다면 생성 */
        if(!Storage::disk('public')->exists($base_path)){
            //Storage::disk('public')->makeDirectory($save_path);
            Storage::disk('public')->makeDirectory($base_path);
        }

        if($plan_img_url != ""){
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $plan_img_url);
            preg_match('/data:image\/(.*?);base64,/', $plan_img_url, $matches, PREG_OFFSET_CAPTURE);
            $ext = $matches[1][0];

            $file_name = sprintf("%s.%s", sprintf("img_%s",$plan_no),$ext);
            $save_file = sprintf("%s/%s", $base_path, $file_name);

            Storage::disk('public')->put($save_file, base64_decode($image));
            $img["plan_img"] = $save_file;
        }

        if($plan_top_img_url != ""){
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $plan_top_img_url);
            preg_match('/data:image\/(.*?);base64,/', $plan_top_img_url, $matches, PREG_OFFSET_CAPTURE);
            $ext = $matches[1][0];

            $file_name = sprintf("%s.%s", sprintf("top_%s",$plan_no),$ext);
            $save_file = sprintf("%s/%s", $base_path, $file_name);

            Storage::disk('public')->put($save_file, base64_decode($image));
            $img["plan_top_img"] = $save_file;
        }

        if($plan_preview_img_url != ""){
            $image = preg_replace('/data:image\/(.*?);base64,/', '', $plan_preview_img_url);
            preg_match('/data:image\/(.*?);base64,/', $plan_preview_img_url, $matches, PREG_OFFSET_CAPTURE);
            $ext = $matches[1][0];

            $file_name = sprintf("%s.%s", sprintf("preview_%s",$plan_no),$ext);
            $save_file = sprintf("%s/%s", $base_path, $file_name);

            Storage::disk('public')->put($save_file, base64_decode($image));
            $img["plan_preview_img"] = $save_file;
        }

        return  $img;

    }

    
}
