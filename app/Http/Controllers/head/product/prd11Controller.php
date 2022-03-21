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
use Exception;

class prd11Controller extends Controller
{
    public function index() {

        $values = [
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
            'types' => SLib::getCodes('G_AD_TYPE'),
            'states' => SLib::getCodes('IS_SHOW')
        ];
        return view( Config::get('shop.head.view') . '/product/prd11',$values);
    }

    public function create(){

        $section = new \stdClass();
        $section->max_limit = 5;
        $section->soldout_ex_yn = 'Y';
        $section->sort = 'M';

        $values = [
            'code' => '',
            'section' => $section,
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
        ];

        return view( Config::get('shop.head.view') . '/product/prd11_show',$values);
    }

    public function show($code) {

        $sql = /** @lang text */
            "select 
                a.sec_no,a.sec_code,a.subject,a.max_limit,a.soldout_ex_yn,a.sort, a.regi_date, a.use_yn,
                a.admin_id,ifnull(b.name,'') as admin_nm
            from section a left outer join mgr_user b on a.admin_id = b.id
            where a.sec_no = :code";
        $section = DB::selectOne($sql,array("code" => $code));

        $values = [
            'code' => $code,
            'section' => $section,
            'section_types' => SLib::getCodes('G_SECTION_TYPE')
        ];

        return view( Config::get('shop.head.view') . '/product/prd11_show',$values);
    }

    public function search(Request $req) {

        
		$sec_code	= $req->input('sec_code', '');
		$goods_nm	= $req->input('goods_nm', '');
		$name		= $req->input('name', '');
        $use_yn	    = $req->input('use_yn', '');

        $where = "";
        $inner_where = "";

		if ($sec_code != "")	$where .= " and a.sec_code = '" . Lib::quote($sec_code) . "'";
		if ($goods_nm != ""){
            $inner_where .= " and g.goods_nm like '" . Lib::quote($goods_nm) . "%' ";
            $where .= " and cnt > 0 ";
        }
		if ($name != "")		$where .= " and a.subject like '%" . Lib::quote($name) . "%' ";
        if ($use_yn != "")	$where .= " and a.use_yn = '" . Lib::quote($use_yn) . "'";

		$sql = /** @lang text */
            "
			select
				case a.sec_code
					when 'category' then CONCAT(c.code_val, '_', a.comment)
					when 'category_best' then CONCAT(c.code_val, '_', a.comment)
					when 'brandshop' then CONCAT(c.code_val, '_', a.comment)
					else c.code_val
				end as type,
				a.sec_no,
				a.subject as name, a.max_limit, a.soldout_ex_yn, d.code_val as sort,
				ifnull(b.cnt, 0) as cnt,
				ifnull(b.40_cnt, 0) as 40_cnt,
				ifnull(b.30_cnt, 0) as 30_cnt,
				a.use_yn,
				a.regi_date as rt
			from section a
				left outer join (
					select
						a.sec_no
						, sum(a.cnt) as cnt
						, sum(if(a.sale_stat_cl = '40', a.cnt, 0)) as 40_cnt
						, sum(if(a.sale_stat_cl = '30', a.cnt, 0 )) as 30_cnt
					 from (
						select sg.sec_no, count(*) as cnt, g.sale_stat_cl
						from section_goods sg
							inner join goods g on sg.goods_no = g.goods_no and sg.goods_sub = g.goods_sub
							where 1=1 $inner_where
						group by g.sale_stat_cl, sg.sec_no
						) a
					group by a.sec_no
				) b on a.sec_no = b.sec_no
				left outer join code c on c.code_kind_cd = 'G_SECTION_TYPE' and a.sec_code = c.code_id
				left outer join code d on d.code_kind_cd = 'G_SECTION_SORT' and a.sort = d.code_id
			where 1=1 $where
			order by 30_cnt desc,a.sec_no desc
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

    public function store(Request $request) {

        $subject = $request->input('subject');
        $sec_code = $request->input('sec_code');
        $use_yn	= $request->input('section_use_yn', 'Y');
        $max_limit = $request->input('max_limit',5);
        $soldout_ex_yn = $request->input('soldout_ex_yn','N');
        $sort = $request->input('sort','M');

        $id = Auth::guard('head')->user()->id;

        $section = [
            'sec_code' => $sec_code,
            'subject' => $subject,
            'max_limit' => $max_limit,
            'soldout_ex_yn' => $soldout_ex_yn,
            'sort' => $sort,
            'comment' => '',
            'admin_id' => $id,
            'use_yn' => $use_yn,
            'regi_date' => DB::raw('now()')
        ];

        try {
            DB::transaction(function () use (&$result, $section) {
                DB::table('section')->insert($section);
            });
            $code = 200;
        } catch (Exception $e) {
            $code = 500;
        }

        return response()->json(['code' => $code]);
    }

    public function update($code, Request $request){

        $subject = $request->input('subject');
        $sec_code = $request->input('sec_code');
        $max_limit = $request->input('max_limit',5);
        $use_yn	= $request->input('section_use_yn', 'Y');
        $soldout_ex_yn = $request->input('soldout_ex_yn','N');
        $sort = $request->input('sort','M');

        $id = Auth::guard('head')->user()->id;

        $section = [
            'sec_code' => $sec_code,
            'subject' => $subject,
            'max_limit' => $max_limit,
            'soldout_ex_yn' => $soldout_ex_yn,
            'sort' => $sort,
            'comment' => '',
            'admin_id' => $id,
            'use_yn' => $use_yn,
            'regi_date' => DB::raw('now()')
        ];

        try {
            DB::transaction(function () use (&$result, $code,$section) {
                DB::table('section')
                    ->where('sec_no','=',$code)
                    ->update($section);
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
                DB::table('section')->where('sec_no', $code)->delete();
                DB::table('section_goods')->where('sec_no', $code)->delete();
            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function goods_search($code,Request $req) {

        $type		= $req->input('type', '');
        $user_yn	= $req->input('user_yn', '');
        $name		= $req->input('name', '');

        $where = "";

        if ($type != "")		$where .= " and a.type = '$type'";
        if ($user_yn != "")	    $where .= " and a.user_yn = '$user_yn'";
        if ($name != "")		$where .= " and a.subject like '" . Lib::quote($name) . "%' ";

        $limit = "";

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $sql = /** @lang text */
            "
			select
				'on;off' as blank
				, '' as blank_img, g.head_desc, g.goods_nm, g.ad_desc, c.code_val as sale_stat_cl, g.goods_sh, g.price
				, ifnull(round((g.before_sale_price - g.price) / g.before_sale_price * 100 ), 0) as sale_rate
				, date_format(g.sale_s_dt, '%Y%m%d') as sale_s_dt, date_format(g.sale_e_dt, '%Y%m%d') as sale_e_dt
				, ifnull((
					select sum(good_qty)
					from goods_summary
					where goods_no = sg.goods_no and goods_sub = sg.goods_sub
				), 0) as qty
				, g.reg_dm
				, g.goods_no, g.goods_sub
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods where goods_no = g.goods_no and goods_sub = g.goods_sub
				 )) as img
			from section_goods sg
				inner join goods g on sg.goods_no = g.goods_no and sg.goods_sub = g.goods_sub
				left outer join goods_stat gs on sg.goods_no = gs.goods_no and sg.goods_sub = gs.goods_sub
				left outer join code c on c.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = c.code_id
			where sg.sec_no = '$code'
				$where
			order by sg.seq
			$limit
        ";
            //echo "<pre>$sql</pre>";

        $rows = DB::select($sql);

		foreach($rows as $row){
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

        $goods_nos = $request->input('goods_no');


        try {
            DB::transaction(function () use (&$result, $code,$goods_nos) {

                for($i=0;$i<count($goods_nos);$i++){
                    $cnt = DB::table('section_goods')
                        ->where('sec_no','=',$code)
                        ->where('goods_no','=',$goods_nos[$i])
                        ->count();

                    if($cnt === 0){
                        $section_goods = [
                            'sec_no' => $code,
                            'goods_no' => $goods_nos[$i],
                            'goods_sub' => 0,
                            'seq' => 0,
                        ];
                        DB::table('section_goods')->insert($section_goods);
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

        $goods_nos = $request->input('goods_nos');

        try {
            DB::transaction(function () use (&$result, $code,$goods_nos) {
                for($i=0;$i<count($goods_nos);$i++){
                    DB::table('section_goods')
                        ->where('sec_no','=',$code)
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

        $goods_nos = $request->input('goods_nos');

        try {
            DB::transaction(function () use (&$result, $code,$goods_nos) {
                for($i=0;$i<count($goods_nos);$i++){
                    DB::table('section_goods')
                        ->where('sec_no','=',$code)
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
}
