<?php

namespace App\Http\Controllers\head\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class mem03Controller extends Controller
{
    public function index() {
        $values = [
            'types' => SLib::getCodes('G_USR_GROUP_TYPE')
        ];

        return view( Config::get('shop.head.view') . '/member/mem03',$values);
    }

    public function show($type='', $id='', Request $req) {
        $values = [
            'type' => $type,
            'types' => SLib::getCodes('G_USR_GROUP_TYPE'),
            'group' => null
        ];

        if ($id !== ''){
			$sql = "
				select a.*, ifnull(b.dc_ext_goods, 0) as dc_ext_goods, ifnull(c.user_cnt, 0) as user_cnt
				from user_group a
					left outer join (
						select group_no, count(*) as dc_ext_goods from user_group_ext_goods group by group_no
					) b on b.group_no = a.group_no
					left outer join (
						select gm.group_no , count(*) as user_cnt
						from user_group_member gm
							inner join member m on gm.user_id = m.user_id
						group by gm.group_no
					) c on c.group_no = a.group_no
				where a.group_no = '$id'
            ";

            $values['group'] = DB::selectOne($sql);
        }
        // dd($values);
        return view( Config::get('shop.head.view') . '/member/mem03_show', $values);
    }

    public function grade() {
        $values = [];

        $sql = " select group_no as id, group_nm as val from user_group where type = 'G' order by group_no ";
        $values['groups'] = DB::select($sql);

        return view( Config::get('shop.head.view') . '/member/mem03_grade', $values);
    }

    public function group_user($id = '') {
        $values = [
            'sex' => SLib::getCodes('G_SEX_TYPE'),
            'age' => SLib::getCodes('G_AGE'),
            'yn' => SLib::getCodes('G_YN'),
            'group_no' => $id
        ];

        $sql = " select group_no as id, group_nm as val from user_group order by group_no ";
        $values['groups'] = DB::select($sql);
        
		// 회원그룹명
		$sql = "select group_nm from user_group where group_no = '$id'";
        $values['group_nm'] = DB::selectOne($sql)->group_nm;

        return view( Config::get('shop.head.view') . '/member/mem03_group_user', $values);

    }

    public function add_group_user($id='', Request $req) {
        try{
            DB::beginTransaction();

            $user_ids = explode(',', $req->input('user_ids', ''));

            foreach($user_ids as $user_id) {
				$sql = "
					select count(*) as cnt
					from user_group_member
					where group_no = '$id' and user_id = '$user_id'
                ";

                $cnt = DB::selectOne($sql)->cnt;

				if($cnt == "0") {
					$sql = "
						insert into user_group_member
						(user_id, group_no,rt,ut) values ('$user_id','$id',now(),now())
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

    public function move_group_user($old_id='', Request $req) {
        try{
            DB::beginTransaction();

            $user_ids = explode(',', $req->input('user_ids', ''));
            $change_id = $req->input('change_id', '');

            foreach($user_ids as $user_id) {
				$sql = "update user_group_member set group_no = '$change_id', rt=now(), ut=now() where group_no = '$old_id' and user_id = '$user_id'";

                DB::update($sql);
            }

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function del_group_user($id='', Request $req) {
        try{
            DB::beginTransaction();

            $user_ids = explode(',', $req->input('user_ids', ''));

            foreach($user_ids as $user_id) {
				$sql = "delete from user_group_member where group_no = '$id' and user_id = '$user_id'";

                DB::delete($sql);
            }

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function search_grade() {

		$cond_amt_from 	    = str_replace(",", "", Request("cond_amt_from"));
		$cond_amt_to 		= str_replace(",", "", Request("cond_amt_to"));
		$cond_cnt_from 	    = str_replace(",", "", Request("cond_cnt_from"));
		$cond_cnt_to 		= str_replace(",", "", Request("cond_cnt_to"));
		$in_group_nos		= Request("in_group_nos");
		$ex_group_nos		= Request("ex_group_nos");

		$type		= Request("type");
		
		$where = "";
		$having = "";

		if($cond_amt_from > 0){
			$where .= " and c.ord_amt >= '$cond_amt_from' ";
		}
		if($cond_amt_to > 0){
			$where .= " and c.ord_amt < '$cond_amt_to' ";
		}
		if($cond_cnt_from > 0){
			$where .= " and c.ord_cnt >= '$cond_cnt_from' ";
		}
		if($cond_cnt_to > 0){
			$where .= " and c.ord_cnt < '$cond_cnt_to' ";
		}

		if($in_group_nos != ""){
			$where .= " and ifnull(b.group_no,0) in ( $in_group_nos ) ";
		}

		if($type != ""){
			if($type == "on")	$where .= " and ( a.store_cd = '' or a.store_cd is null ) and ( a.store_nm = '' or a.store_nm is null ) ";
			if($type == "off")	$where .= " and a.store_cd <> '' ";
		}

		if($ex_group_nos != ""){
			$having = " having ( select count(*) from user_group_member where user_id = a.user_id and group_no in ( $ex_group_nos ) ) = 0 ";
		}

        $sql = "select group_concat(group_no) as group_nos from user_group where type = 'G'";

        $group_grade = DB::selectOne($sql)->group_nos;

		if($group_grade == "") $group_grade = "0";

		$sql = "
			select
				'' as blank,a.user_id, a.name,
				ifnull(b.group_no,0) as group_grade,
				( select group_concat(group_nm) from user_group
					where group_no in ( select group_no from user_group_member where user_id = a.user_id )) as group_nm,
				date_format(a.regdate,'%Y%m%d') regdate,
				c.ord_cnt, c.ord_amt,b.rt
			from member a
				left outer join user_group_member b on b.group_no in ( $group_grade ) and a.user_id = b.user_id
				left outer join member_stat c on a.user_id = c.user_id
			where 1 = 1 $where
			$having
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [
                'total' => count($result)
            ],
            "body" => $result
        ]);
    }

    public function search_group_user($group_no = '', Request $req) {

		$user_id		= Request("user_id");
		$name			= Request("name");
		$yn				= Request("yn");
		$sdate			= Request("sdate");
		$edate			= Request("edate");
		$sex			= Request("sex");
		$age			= Request("age");
		$last_sdate	    = Request("last_sdate");
		$last_edate	    = Request("last_edate");
		$limit			= Request("limit", 500);

		$cond_amt_from 	= str_replace(",", "", Request("cond_amt_from"));
		$cond_amt_to 	= str_replace(",", "", Request("cond_amt_to"));
		$cond_cnt_from 	= str_replace(",", "", Request("cond_cnt_from"));
		$cond_cnt_to 	= str_replace(",", "", Request("cond_cnt_to"));

		$order_sdate	= Request("order_sdate");
		$order_edate	= Request("order_edate");

		$type			= Request("type");
		
        $page = Request("page", 1);

		if ($page < 1 or $page == "") $page = 1;

		$where = "";
		if($user_id != "")$where .= " and b.user_id = '$user_id'";
		if($name != "")$where .= " and b.name = '$name'";
		if($yn != "")$where .= " and b.email_chk = '$yn'";
		if($sdate != "")$where .= " and b.regdate >= '$sdate' ";
		if($edate != "")$where .= " and b.regdate < date_add('$edate',interval 1 day) ";
		if($order_sdate != "")$where .= " and c.ord_date >= '$order_sdate' ";
		if($order_edate != "")$where .= " and c.ord_date < date_add('$order_edate',interval 1 day) ";
		if($sex != "")$where .= " and b.sex = '$sex'";
		if($age != ""){

			$year = date('Y');
			$age_10 = $year - 19;
			$age_20 = $year - 29;
			$age_30 = $year - 39;
			$age_40 = $year - 49;
			$age_50 = $year - 59;
			$age_60 = $year - 69;

			if($age == "10"){
				$where .= " and b.yyyy >= '$age_10'";
			} else if($age == "20"){
				$where .= " and b.yyyy >= '$age_20'";
				$where .= " and b.yyyy <= '$age_10'";
			} else if($age == "30"){
				$where .= " and b.yyyy >= '$age_30'";
				$where .= " and b.yyyy <= '$age_20'";
			} else if($age == "40"){
				$where .= " and b.yyyy >= '$age_40'";
				$where .= " and b.yyyy <= '$age_30'";
			} else if($age == "50"){
				$where .= " and b.yyyy >= '$age_50'";
				$where .= " and b.yyyy <= '$age_40'";
			} else if($age == "60"){
				$where .= " and b.yyyy <= '$age_50'";
			}
		}
		if($last_sdate != "")$where .= " and b.lastdate >= '$last_sdate' ";
		if($last_edate != "")$where .= " and b.lastdate < date_add('$last_edate',interval 1 day) ";

		if($cond_amt_from > 0){
			$where .= " and c.ord_amt >= '$cond_amt_from' ";
		}
		if($cond_amt_to > 0){
			$where .= " and c.ord_amt < '$cond_amt_to' ";
		}
		if($cond_cnt_from > 0){
			$where .= " and c.ord_cnt >= '$cond_cnt_from' ";
		}
		if($cond_cnt_to > 0){
			$where .= " and c.ord_cnt < '$cond_cnt_to' ";
        }

		if($type != ""){
			if($type == "on")	$where .= " and ( b.store_cd = '' or b.store_cd is null ) and ( b.store_nm = '' or b.store_nm is null ) ";
			if($type == "off")	$where .= " and b.store_cd <> '' ";
		}
        
        $id = Auth('head')->user()->id;
		$ip = $_SERVER["REMOTE_ADDR"];

		$page_size = $limit;

		// 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
		if ($page == 1) {
			// 갯수 얻기
			$sql = " /* [$id][$ip] admin : user/usr03.php (1) */
				select
					count(*) total
				from user_group_member a
					inner join member b on a.user_id = b.user_id
					left outer join member_stat c on a.user_id = c.user_id
				where a.group_no = '$group_no' $where
			";
			$row = DB::selectOne($sql);
			$data_cnt = $row->total;

			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
            }

			$arr_header = array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt
            );
		} else {
			$startno = ($page-1) * $page_size;
			$arr_header = array(
				"total" => 0,
				"page" => $page,
				"page_cnt" => 0
			);
        }

		if($limit == -1){
			if ($page > 1) $limit = "limit 0";
			else $limit = "";
		} else {
			$limit = " limit $startno, $page_size ";
		}

		$sql = "
			select
				'' as blank,b.user_id, b.name, d.code_val as sex, b.jumin1, b.mobile,
				date_format(b.regdate,'%Y%m%d') regdate, date_format(b.lastdate,'%Y%m%d') lastdate,
				b.visit_cnt, date_format(c.ord_date,'%Y%m%d') ord_date,
				c.ord_cnt, c.ord_amt,
				ifnull(e.est_cnt, 0) as est_cnt,
				b.point, b.email_chk,mobile_chk
			from user_group_member a
				inner join member b on a.user_id = b.user_id
				left outer join member_stat c on a.user_id = c.user_id
				left outer join code d on d.code_kind_cd = 'G_SEX_TYPE' and b.sex = d.code_id
				left outer join (
					select user_id, count(*) as est_cnt
					from goods_estimate
					group by user_id
				) e on e.user_id = a.user_id
			where a.group_no = '$group_no' $where
			$limit
        ";

        $result = DB::select($sql);
		$arr_header['page_total'] = count($result);

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $result
        ]);
    }

    public function ext_goods($id='') {
        $opt_kind_cd = Request("opt_kind_cd", "");
        $limit = Request("limit", 100);

		// 회원그룹명
		$sql = "select group_nm from user_group where group_no = '$id'";
        $row = DB::selectOne($sql);

        $values = [
            'group_no' => $id,
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'goods_states' => SLib::getCodes('G_GOODS_STAT'),
            'ord_kinds' => SLib::getCodes('G_ord_KIND'),
            'com_types' => SLib::getCodes('G_COM_TYPE'),
            'group_nm' => $row->group_nm,
            'items' => SLib::getItems(),
        ];

        return view( Config::get('shop.head.view') . '/member/mem03_ext_goods', $values);
    }

    public function search_ext_goods($group_no='', Request $req) {
        $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");
        $cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
        
		// 검색조건
		$goods_type		= Request("goods_type");	// 상품 구분
		$goods_stat		= Request("goods_stat");	// 상품 상태
		$style_no		= Request("style_no");		// 스타일넘버
		$goods_no		= Request("goods_no");		// 상품번호
		$com_type		= Request("com_type");		// 업체구분
		$com_id			= Request("com_cd");		// 업체아이디
		$opt_kind_cd	= Request("opt_kind_cd");	// 품목
		$brand_cd		= Request("brand_cd");		// 브랜드
		$rep_cat_cd		= Request("cat_cd");		// 대표카테고리
		$goods_nm		= Request("goods_nm");		// 상품명
		$limit			= Request("limit");			// 출력수
		$ord_field		= Request("ord_field");		// 정렬필드
        $ord			= Request("ord");			// 정렬

        $where = "";

		if( $goods_type != "" )		$where .= " and g.goods_type = '$goods_type' ";
		if( $goods_stat != "" )		$where .= " and g.sale_stat_cl = '$goods_stat' ";
		if( $style_no	!= "" )		$where .= " and g.style_no = '$style_no ' ";

		if( $goods_no != "" ){
			$goods_nos = explode(",", $goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 50) array_splice($goods_nos,50);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			} else {
				$where .= " and g.goods_no = '$goods_no' ";
			}
		}

		if( $com_type != "" )			$where .= " and g.com_type = '$com_type' ";
		if( $com_id != "" )			$where .= " and g.com_id = '$com_id' ";
		if( $opt_kind_cd != "" )		$where .= " and g.opt_kind_cd = '$opt_kind_cd' ";
		if( $brand_cd != "" )			$where .= " and g.brand = '$brand_cd' ";
		if( $rep_cat_cd != "" )		$where .= " and g.rep_cat_cd = '$rep_cat_cd' ";
		if( $goods_nm != "" )			$where .= " and g.goods_nm like '%$goods_nm%' ";

		$page = Request("page", 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		// 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
		if ($page == 1) {
			// 갯수 얻기
			$sql = "
				select
					count(*) total
				from user_group_ext_goods a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				where a.group_no = '$group_no' $where
			";
			$row = DB::selectOne($sql);
			$data_cnt = $row->total;

            $page_cnt=(int)(($data_cnt-1)/$page_size) + 1;

			if($page == 1){
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
            }

			$arr_header = array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => $page_cnt
            );
		} else {
			$startno = ($page-1) * $page_size;
			$arr_header = null;
		}
		if($limit == -1){
			if ($page > 1) $limit = "limit 0";
			else $limit = "";
		} else {
			$limit = " limit $startno, $page_size ";
		}

		$sql = "
			select
				'' as chkbox
				, g.goods_no , g.goods_sub
				, '' as img
				, if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
					select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
					from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
				  )) as img_url
				, g.goods_nm
				, ifnull( type.code_val, 'N/A') as goods_type_val
				, com.com_nm
				, opt.opt_kind_nm
				, brand.brand_nm
				, cat.full_nm
				, g.style_no
				, stat.code_val as sale_stat_cl_val
				, g.price
				, ifnull(
					(select sum(wqty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				) as wqty
				, ifnull(
					(select sum(good_qty) from goods_summary where goods_no = g.goods_no and goods_sub = g.goods_sub), 0
				) as qty
				, a.admin_id, a.admin_nm
				, a.rt
				, g.goods_type
				, com.com_type
				, g.sale_stat_cl
			from user_group_ext_goods a
				inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join company com on com.com_id = g.com_id
				left outer join brand brand on brand.brand = g.brand
				left outer join category cat on cat.d_cat_cd = g.rep_cat_cd and cat.cat_type = 'DISPLAY'
			where a.group_no = '$group_no' $where
			order by $ord_field $ord
			$limit
		";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $result
        ]);
    }

    public function add_ext_goods($id='', Request $req) {
		$goods	= Request('goods', '');
		$admin_id 			= Auth('head')->user()->id;
		$admin_nm			= Auth('head')->user()->name;

        $datas = explode(',', $goods);
        
        try{
            DB::beginTransaction();

            foreach($datas as $data) {
                list($goods_no, $goods_sub) = explode("|", $data);
                
				$sql = "
                    select count(*) as cnt
                    from user_group_ext_goods
                    where group_no = '$id' and goods_no = '$goods_no' and goods_sub = '$goods_sub'
                ";

                $row = DB::selectOne($sql);

                if ($row->cnt == "0") {
                    $sql = "
                        insert into user_group_ext_goods (
                            group_no, goods_no, goods_sub, admin_id, admin_nm, rt
                        ) values (
                            '$id', '$goods_no', '$goods_sub', '$admin_id', '$admin_nm', now()
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

    public function del_ext_goods($id='', Request $req) {
        try{
            DB::beginTransaction();

            $datas = explode(",", $req->goods);

            foreach($datas as $data) {
                list($goods_no, $goods_sub) = explode("|", $data);
                // 해당 상품 삭제
                $sql = "
                    delete
                    from user_group_ext_goods
                    where group_no = '$id' and goods_no = '$goods_no' and goods_sub = '$goods_sub'
                ";

                DB::delete($sql);
            }
            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function edit_grade($id='', Request $req) {
        try{
            DB::beginTransaction();

            $user_ids = explode(",", $req->input('user_ids'));
            
            $sql = "select group_concat(group_no) as group_nos from user_group where type = 'G'";

            $group_grade = DB::selectOne($sql)->group_nos;

            if($group_grade == "") $group_grade = "0";
        
            foreach($user_ids as $user_id) {
                $sql = "delete from user_group_member where user_id = '$user_id' and group_no in ( $group_grade )";
                DB::delete($sql);

                $sql = "insert into user_group_member ( group_no, user_id, rt, ut ) values ( '$id','$user_id',now(),now())";
                DB::insert($sql);
            }

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }

    public function search(Request $request) {
        $group_nm		= Request("group_nm");
        $group_type	    = Request("group_type");
        $user_id		= Request("user_id");
        $user_nm		= Request("user_nm");

        $where = "";

        if($group_nm != ""){
            $where .= " and a.group_nm like '$group_nm%' ";
        }

        if($group_type != ""){
            $where .= " and a.type = '$group_type' ";
        }

        if($user_id != ""){
            $where .= " and b.user_id like '$user_id%' ";
        }

        if( $user_nm != ""){
            $where .= " and m.name like '$user_nm%' ";
        }
        
        $sql = "
            select
                a.group_no, a.group_nm, ifnull(d.user_cnt,0) as user_cnt, ifnull(code_val,'') as user_group_type
                , a.dc_ratio, ifnull(e.dc_ext_goods,0) as dc_ext_goods
                , a.point_ratio, a.is_wholesale, a.is_point_use, a.is_point_save, a.is_coupon_use
                , a.rt, a.ut
            from user_group a
                left outer join user_group_member b on b.group_no = a.group_no
                left outer join member m on b.user_id = m.user_id
                left outer join code c on c.code_kind_cd = 'G_USR_GROUP_TYPE' and a.type = c.code_id
                left outer join (
                    select gm.group_no , count(*) as user_cnt
                    from user_group_member gm
                        inner join member m on gm.user_id = m.user_id
                    group by gm.group_no
                ) d on d.group_no = a.group_no
                left outer join (
                    select group_no, count(*) as dc_ext_goods from user_group_ext_goods group by group_no
                ) e on e.group_no = a.group_no
            where 1=1
                $where
            group by a.group_no
            order by a.group_no
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [ 'total' => count($result)],
            "body" => $result
        ]);
    }

    public function add_group(Request $req) {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        try{
            DB::beginTransaction();

            $id = DB::table('user_group')->insertGetId([
                'type'              => $req->type,
                'group_nm'          => $req->group_nm, 
                'cond_amt_from'     => Lib::uncm($req->cond_amt_from), 
                'cond_amt_to'       => Lib::uncm($req->cond_amt_to), 
                'cond_cnt_from'     => Lib::uncm($req->cond_cnt_from), 
                'cond_cnt_to'       => Lib::uncm($req->cond_cnt_to), 
                'cond_email'        => $req->cond_email,
                'renew_period'      => $req->renew_period, 
                'dc_limit_amt'      => Lib::uncm($req->dc_limit_amt), 
                'dc_ratio'          => Lib::uncm($req->dc_ratio), 
                'point_ratio'       => Lib::uncm($req->point_ratio), 
                'point_limit_amt'   => Lib::uncm($req->point_limit_amt), 
                'is_wholesale'      => $req->is_wholesale_yn, 
                'is_point_use'      => $req->is_point_use_yn, 
                'is_point_save'     => $req->is_point_save_yn, 
                'is_coupon_use'     => $req->is_coupon_use_yn,
                'icon'              => '', 
                'admin_id'          => $user['id'], 
                'admin_nm'          => $user['name'], 
                'rt'                => now(), 
                'ut'                => now()
            ]);
            
            if ($req->input('src', '') != '') {
                $iconPath = $this->__upload_img($id, $req);
            
                DB::table('user_group')->where('group_no', $id)->update([
                    'icon' => $iconPath
                ]);
            }

            DB::commit();
            return response()->json(['id' => $id], 201);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function edit_group($id='', Request $req) {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        try{
            DB::beginTransaction();

            $values = [
                'type' => $req->type,
                'group_nm' => $req->group_nm, 
                'cond_amt_from' => Lib::uncm($req->cond_amt_from), 
                'cond_amt_to' => Lib::uncm($req->cond_amt_to), 
                'cond_cnt_from' => Lib::uncm($req->cond_cnt_from), 
                'cond_cnt_to' => Lib::uncm($req->cond_cnt_to), 
                'cond_email' => $req->cond_email,
                'renew_period' => $req->renew_period, 
                'dc_limit_amt' => Lib::uncm($req->dc_limit_amt), 
                'dc_ratio' => Lib::uncm($req->dc_ratio), 
                'point_ratio' => Lib::uncm($req->point_ratio), 
                'point_limit_amt' => Lib::uncm($req->point_limit_amt), 
                'is_wholesale' => $req->is_wholesale_yn, 
                'is_point_use' => $req->is_point_use_yn, 
                'is_point_save' => $req->is_point_save_yn, 
                'is_coupon_use' => $req->is_coupon_use_yn,
                'admin_id' => $user['id'], 
                'admin_nm' => $user['name'], 
                'ut' => now()
            ];

            if ($req->input('src', '') != '') {
                $values['icon'] = $this->__upload_img($id, $req);
            }
            
            DB::table('user_group')->where('group_no', $id)->update($values);

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //그룹 삭제
    public function del_group($id='', Request $req) {
        try{
            DB::beginTransaction();

            // 할인율제외 상품 삭제
            DB::table('user_group_ext_goods')->where('group_no', $id)->delete();

            // 회원의 그룹 정보 삭제
            DB::table('user_group_member')->where('group_no', $id)->delete();

            // 그룹 삭제
            DB::table('user_group')->where('group_no', $id)->delete();

            DB::commit();

            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function group_value($group_no='') {
		$sql = "
			select
				cond_amt_from, cond_amt_to,cond_cnt_from,cond_cnt_to
			from user_group where group_no = '$group_no'
		";
        
        $row =  DB::selectOne($sql);
        return response()->json($row, 201);
    }

    private function __upload_img($id, Request $req) {
        // $save_path = sprintf("/data/head/user/group/icon/%s", $id);
        $save_path = sprintf("/images/user/group/icon/%s", $id); // 저장위치 관련 추가작업 필요
  
        $image = preg_replace('/data:image\/(.*?);base64,/', '', $req->src);
        
        $file_name = sprintf("%s.jpg", date('YmdHis'));
        $save_file = sprintf("%s/%s", $save_path, $file_name);

        /* 이미지를 저장할 경로 폴더가 없다면 생성 */
        if(!Storage::disk('public')->exists($save_path)){
            Storage::disk('public')->makeDirectory($save_path);
        }
  
        //저장
        Storage::disk('public')->put($save_file, base64_decode($image));
        
        return $save_file;
    }
}
