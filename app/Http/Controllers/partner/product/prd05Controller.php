<?php

namespace App\Http\Controllers\partner\product;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class prd05Controller extends Controller
{
    public function index() {
        $com_id = Auth('partner')->user()->com_id;
        $com_nm = Auth('partner')->user()->com_nm;

		$query = "
				select 
                    '' as class
                     , '미분류' as class_nm
                     , (select count(*) as cnt from goods where com_id = '$com_id' and ifnull(class,'') = '') as cnt
            union all
                select 
                    a.class
                     , a.class_nm
                     , (select count(*) from goods where com_id = '$com_id' and class = a.class) as cnt 
                from code_class a 
                group by class, class_nm
        ";
		$class_items = DB::select($query);
		if (count($class_items) > 0) {
			$to_class_items = $class_items;
		}

        $query = "select item as id, item_nm as val from code_class where class = '001' order by item+0";
        $rows = DB::select($query);

        $query = "select code_id as id, code_val_eng val from code where code_kind_cd = 'G_PRODUCTS_COLOR' and use_yn = 'Y' order by code_seq asc";
        $product_colors = DB::select($query);


        $values = [
            'goods_stats' => SLib::getCodes('G_GOODS_STAT'),
            'items' => SLib::getItems(),
            'goods_types' => SLib::getCodes('G_GOODS_TYPE'),
            'is_unlimiteds' => SLib::getCodes('G_IS_UNLIMITED'),
            'alter_reasons' => SLib::getCodes('G_JAEGO_REASON'),
            'class_items' => $class_items,
            'com_id' => $com_id,
            'com_nm' => $com_nm,
            'color_num' => 0,
            'product_colors' => $product_colors
        ];
        return view( Config::get('shop.partner.view') . '/product/prd05',$values);
    }

    public function search(Request $request){

        $com_id = Auth('partner')->user()->com_id;

        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

		$class          = $request->input("class");         // 분류
		$goods_type     = $request->input("goods_type");    // 상품구분
		$goods_stat     = $request->input("goods_stat");    // 상품 상태
		$style_no       = $request->input("style_no");      // 스타일 넘버
		$goods_no       = $request->input("goods_no");      // 상품번호
		$opt_kind_cd    = $request->input("item");          // 품목
		$brand_nm       = $request->input("brand_nm");      // 브랜드 이름
		$brand_cd       = $request->input("brand_cd");      // 브랜드
		$ad_desc		= $request->input("head_desc");		// 상/하단 홍보문구
		$goods_nm       = $request->input("goods_nm");      // 상품명
		$limit          = $request->input("limit", 100);	// 출력수
		$ord_field      = $request->input("ord_field");     // 정렬필드
		$ord            = $request->input("ord");           // 정렬
		$goods          = $request->input("goods");
		$oms_col        = explode(",", $request->input("omission_column")); // 미등록항목 컬럼 ex) 001,003,009

        $where = "";
        $insql = "";
        $order_field = "";
        $class_items_fields = "";

        if($goods_type != "")	$where .= " and g.goods_type = '$goods_type' ";
		if($goods_stat != "")	$where .= " and g.sale_stat_cl = '$goods_stat' ";
		if($style_no != "")	$where .= " and g.style_no like '$style_no%' ";
		if($goods_no != ""){
			// 상품코드 검색
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 50) array_splice($goods_nos,50);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			}else{
				$where .= " and g.goods_no = '$goods_no' ";
			}
		}

		if($goods != ""){
			// 파일로 검색일 경우
			$goods_arr = explode(",",$goods);
			for($i=0;$i<count($goods_arr);$i++){
				if(empty($goods_arr[$i])) continue;
				list($no,$sub) = explode("\|",$goods_arr[$i]);
				if($insql == ""){
					$insql .= " select '$no' as no,'$sub' as sub ";
				}else{
					$insql .= " union select '$no' as no,'$sub' as sub  ";
				}
			}
			$insql = " inner join ( $insql ) sg on g.goods_no = sg.no and g.goods_sub = sg.sub ";
        }

		if($com_id != "")			$where .= " and g.com_id = '$com_id' ";
		if($opt_kind_cd != "")		$where .= " and g.opt_kind_cd = '$opt_kind_cd' ";
		if($brand_cd != "")			$where .= " and g.brand = '$brand_cd' ";
		if($goods_nm != "")			$where .= " and g.goods_nm like '%$goods_nm%' ";
		if($ad_desc != "")			$where .= " and ( g.ad_desc like '%$ad_desc%' or g.head_desc like '%$ad_desc%' ) ";

        if($ord_field != "")    $order_field = "order by ". $ord_field;
        if($ord != "")  $order_field .= " ". $ord;

		if($class == ""){
			$where .= " and ifnull(g.class,'') = '' ";
		}else{
			$where .= " and g.class = '$class' ";
		}

		foreach($oms_col as $col) {
			if($col !== '') {
				$where .= " and (class.item_$col = '' or class.item_$col is null) ";
			}
		}

        /*
		for($i=1; $i<=20;$i++){
		}*/

        $sql_cols = "";

		if($class != ""){
			// 분류에 따른 검색 컬럼
			$sql = "
				select item as id, item_nm as val
				from code_class
				where class = '$class' order by item+0
			";
            $rows = DB::select($sql);


            $field_name = "K";
            foreach ($rows as $row) {
                $sql_cols .= sprintf(",class.item_%s",$row->id);
                /*
                $claim_reason = $menu->code_id;
                $claim_nm = $menu->code_val;

                $claim_menu .= " ,ifnull(sum(if(clm_reason = '$claim_reason',cnt,0)),0) as '$claim_nm' \n";
                $claim_no_str .= ",$claim_reason";
                */
                //$columns .= ", '". $field_name ."' : '". $row->val ."'";

                $field_name++;
            }

           $class_items_fields = $rows;

        }

        //echo 'page_size'. $page_size;
        $page_size = $limit;
        $startno = ($page-1) * $page_size;
        if($limit == -1) {
			if ($page > 1) $sql_limit = "limit 0";
			else $sql_limit = "";
		} else $sql_limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = "
                select count(*) as total
                from goods g $insql
                    left outer join goods_class class on g.goods_no = class.goods_no and g.goods_sub = class.goods_sub and g.class = class.class
                where 1=1 
                    $where
            ";
            //echo $query;
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt=(int)(($total-1)/$page_size) + 1;
        }

        $goods_img_url = '';
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = "
            select
                '' as blank,
                ifnull( type.code_val, 'N/A') as goods_type,
                com.com_nm, opt.opt_kind_nm, brand.brand_nm, g.style_no, '' as img2,
                if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img, g.goods_nm, stat.code_val as sale_stat_cl,
                g.goods_no, g.goods_sub, com.com_id,
                (select class_nm from code_class where class = g.class group by class, class_nm) as class
                $sql_cols
			from goods g $insql
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                left outer join company com on com.com_id = g.com_id
                left outer join brand brand on brand.brand = g.brand
                left outer join goods_class class on g.goods_no = class.goods_no and g.goods_sub = class.goods_sub and g.class = class.class
            where 1=1 
                $where
            $order_field
			$sql_limit
        ";
        //echo $query;
        //$result = DB::select($query,['com_id' => $com_id]);
        $result = DB::select($query);
        //echo "<pre>$query</pre>";
        //dd(array_keys ((array)$result[0]));

        $query = "select item, item_nm,field from code_class where class = '$class' order by item+0";
        $code_class_items = DB::select($query);


        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result,
            "code_items" => $code_class_items,
            "class" => $class,
        ]);
    }

    public function column_search(Request $request){
        $class          = $request->input("class");         // 분류
        // 분류별 미등록 항목 검색
        $columns = array();
        if($class != ""){
			// 분류에 따른 검색 컬럼
			$sql = "
				select item as id, item_nm as val
				from code_class
				where class = '$class' order by item+0
			";
            $rows = DB::select($sql);


            $field_name = "K";
            foreach ($rows as $row) {
                $columns[]=array('item_'.$row->id, $row->val);

                $field_name++;
            }

        }

        $query = "select item, item_nm,field from code_class where class = '$class' order by item+0";
        $code_class_items = DB::select($query);

        return response()->json([
            "code_items" => $code_class_items,
            "columns" => $columns,
        ]);

    }

    public function delete(Request $request){
        $data = $request->input("data");         // 선택된 데이타
        $a_data = explode("^EOL", $data);
        $result = 0;
        $goods_class = [
            'class' => ''
        ];
        for($i=0; $i<count($a_data); $i++){
            $sql_result1 = 0;
            $sql_result2 = 0;
            if(!empty($a_data[$i][0])){
                $val = explode(",", $a_data[$i]);
                $goods_no = $val[0];
                $goods_sub = $val[1];
                /*
                echo 'goods_no : '. $goods_no .', goods_sub: '. $goods_sub;
                echo "<br>";
                */
                $query = "delete from goods_class where goods_no = '$goods_no' and goods_sub = '$goods_sub'";
                $query = "update goods set class = '' where goods_no = '$goods_no' and goods_sub = '$goods_sub'";

                try {
                     DB::table('goods_class')
                        ->where('goods_no','=',$goods_no)
                        ->where('goods_sub','=',$goods_sub)
                        ->delete();
                    $sql_result1 = 1;
                } catch(Exception $e){
                    $sql_result1 = 0;
                }
                $result += $sql_result1;
                try {
                    DB::table('goods')
                        ->where('goods_no','=',$goods_no)
                        ->where('goods_sub','=',$goods_sub)
                        ->update($goods_class);
                    $sql_result2 = 1;
                } catch(Exception $e){
                    $sql_result2 = 0;
                }
                $result += $sql_result2;

            }
        }


        echo $result;
    }

    public function update(Request $request){
        $com_id = Auth('partner')->user()->com_id;
        $com_nm = Auth('partner')->user()->com_nm;

        $result = "";
        $data = $request->input("data");         // 선택된 데이타
        $class = $request->input("class");         // 선택된 데이타
        $to_class = $request->input("to_class");
        $goods = $request->input("data");
        $a_data = explode("^EOL", $data);
        $goods_arrs = json_decode($goods, true);
        $goods_cnt = count($goods_arrs);

        $goods_fields = [];
        $color_insert = [];
        $goods_class_in_result = 0;
        $goods_class_up_result = 0;
        $goods_up_result = 0;
        $goods_color_del_result = 0;
        $goods_color_in_result = 500;

		// 기존 코드에 품목 변경시 데이터 초기화 추가 및 버그 수정
		$class_changed = ($class == $to_class) ? false : true;
		$class = $to_class ? $to_class : "";

		$cc_query = "select * from code_class where class='$class'";
		$code_class_rows = DB::select($cc_query);

        //echo count($code_class_rows);

        $color_query = "select code_id as id, code_val_eng as val from code where code_kind_cd='G_PRODUCTS_COLOR' and use_yn='Y' order by code_seq asc";
        $color_rows = DB::select($color_query);

        //for($k=0; $k<$goods_cnt; $k++){
        foreach ($goods_arrs as $goods_arr) {
            $goods_opt_num = 0;
            $goods_opt_cnt = 0;
            $goods_class_sql = "";
            $goods_opt_arr = [];
            $insert = [];
            $update = [];

			$goods_no 		= $goods_arr['goods_no'];
			$goods_sub 		= $goods_arr['goods_sub'];
			$item_001		= (isset($goods_arr['item_001']) && !$class_changed) ? $goods_arr['item_001']:"";
			$item_002		= (isset($goods_arr['item_002']) && !$class_changed) ? $goods_arr['item_002']:"";
			$item_003		= (isset($goods_arr['item_003']) && !$class_changed) ? $goods_arr['item_003']:"";
			$item_004		= (isset($goods_arr['item_004']) && !$class_changed) ? $goods_arr['item_004']:"";
			$item_005		= (isset($goods_arr['item_005']) && !$class_changed) ? $goods_arr['item_005']:"";
			$item_006		= (isset($goods_arr['item_006']) && !$class_changed) ? $goods_arr['item_006']:"";
			$item_007		= (isset($goods_arr['item_007']) && !$class_changed) ? $goods_arr['item_007']:"";
			$item_008		= (isset($goods_arr['item_008']) && !$class_changed) ? $goods_arr['item_008']:"";
			$item_009		= (isset($goods_arr['item_009']) && !$class_changed) ? $goods_arr['item_009']:"";
			$item_010		= (isset($goods_arr['item_010']) && !$class_changed) ? $goods_arr['item_010']:"";
			$item_011		= (isset($goods_arr['item_011']) && !$class_changed) ? $goods_arr['item_011']:"";
			$item_012		= (isset($goods_arr['item_012']) && !$class_changed) ? $goods_arr['item_012']:"";
			$item_013		= (isset($goods_arr['item_013']) && !$class_changed) ? $goods_arr['item_013']:"";
			$item_014		= (isset($goods_arr['item_014']) && !$class_changed) ? $goods_arr['item_014']:"";
			$item_015		= (isset($goods_arr['item_015']) && !$class_changed) ? $goods_arr['item_015']:"";
			$item_016		= (isset($goods_arr['item_016']) && !$class_changed) ? $goods_arr['item_016']:"";
			$item_017		= (isset($goods_arr['item_017']) && !$class_changed) ? $goods_arr['item_017']:"";
			$item_018		= (isset($goods_arr['item_018']) && !$class_changed) ? $goods_arr['item_018']:"";
			$item_019		= (isset($goods_arr['item_019']) && !$class_changed) ? $goods_arr['item_019']:"";
			$item_020		= (isset($goods_arr['item_020']) && !$class_changed) ? $goods_arr['item_020']:"";


            $query = "select count(*) as cnt from goods_class where goods_no='$goods_no' and goods_sub='$goods_sub' ";
            $row = DB::select($query);
            $goods_class_cnt = $row[0]->cnt;

            if($goods_class_cnt==0){
                $insert = [
                    'goods_no' => $goods_no,
                    'goods_sub' => $goods_sub,
                    'class' => $class,
                    'item_001' => $item_001,
                    'item_002' => $item_002,
                    'item_003' => $item_003,
                    'item_004' => $item_004,
                    'item_005' => $item_005,
                    'item_006' => $item_006,
                    'item_007' => $item_007,
                    'item_008' => $item_008,
                    'item_009' => $item_009,
                    'item_010' => $item_010,
                    'item_011' => $item_011,
                    'item_012' => $item_012,
                    'item_013' => $item_013,
                    'item_014' => $item_014,
                    'item_015' => $item_015,
                    'item_016' => $item_016,
                    'item_017' => $item_017,
                    'item_018' => $item_018,
                    'item_019' => $item_019,
                    'item_020' => $item_020
                ];
                $insert_sql = "insert into goods_class set
                    goods_no = '$goods_no', 
                    goods_sub = '$goods_sub', 
                    class = '$class', 
                    item_001 = '$item_001', 
                    item_002 = '$item_002', 
                    item_003 = '$item_003', 
                    item_004 = '$item_004', 
                    item_005 = '$item_005', 
                    item_006 = '$item_006', 
                    item_007 = '$item_007', 
                    item_008 = '$item_008', 
                    item_009 = '$item_009', 
                    item_010 = '$item_010', 
                    item_011 = '$item_011', 
                    item_012 = '$item_012', 
                    item_013 = '$item_013', 
                    item_014 = '$item_014', 
                    item_015 = '$item_015', 
                    item_016 = '$item_016', 
                    item_017 = '$item_017', 
                    item_018 = '$item_018', 
                    item_019 = '$item_019', 
                    item_020 = '$item_020'
                ";


                if(!empty($goods_no)){

                    try {
                        DB::insert($insert_sql);
                        $goods_class_in_result = 200;
                    } catch(Exception $e){
                        $code = 500;
                        $goods_class_in_result = 500;
                    };
                }

            }else{
                $update = [
                    'class' => $class,
                    'item_001' => $item_001,
                    'item_002' => $item_002,
                    'item_003' => $item_003,
                    'item_004' => $item_004,
                    'item_005' => $item_005,
                    'item_006' => $item_006,
                    'item_007' => $item_007,
                    'item_008' => $item_008,
                    'item_009' => $item_009,
                    'item_010' => $item_010,
                    'item_011' => $item_011,
                    'item_012' => $item_012,
                    'item_013' => $item_013,
                    'item_014' => $item_014,
                    'item_015' => $item_015,
                    'item_016' => $item_016,
                    'item_017' => $item_017,
                    'item_018' => $item_018,
                    'item_019' => $item_019,
                    'item_020' => $item_020
                ];
                try {
                    DB::table('goods_class')
                        ->where('goods_no','=',$goods_no)
                        ->where('goods_sub','=',$goods_sub)
                        ->update($update);
                    $goods_class_up_result = 200;
                } catch(Exception $e){
                    $goods_class_up_result = 500;
                }
            }

            foreach ($goods_arr as $goods_info) {
                if($goods_opt_num>13){
                    //if($goods_opt_num == 11)
                    $goods_opt_arr[] = $goods_info;

                }

                $goods_opt_num++;

            }
            $goods_opt_cnt = count($goods_opt_arr);

            $k = 0;
            for($k=0; $k<count($code_class_rows); $k++){

                $item = $code_class_rows[$k]->item;
                $field = $code_class_rows[$k]->field;
                $num = $k;

                if($num<9) $num = '00'.($num+1);
                elseif($num<100) $num = '0'.($num+1);
                else $num = $num;

                if($item == $num && $field != ""){
                    if($field != "color" && $field != "as_info"){
                        if(@$goods_opt_arr[$k] != ""){
                        $goods_class_sql = $goods_class_sql .", ". $field. " = '". $goods_opt_arr[$k] ."'";
                        }
                    }
                }
                $goods_fields[$item] = $field;


            }

            $goods_up_sql = "update goods
                set  
                    class = '$class'
                    $goods_class_sql
            where goods_no='$goods_no' and goods_sub='$goods_sub'";


            try {
                DB::update($goods_up_sql);
                //$sql_result2 = 1;
                $goods_up_result = 200;
            } catch(Exception $e){
                //$sql_result2 = 0;
                $goods_up_result = 500;
            }

            $color_index = array_search("color", $goods_fields);
            $colors = "";
            //$a_color = "";
            $color_arr = [];

            if($color_index != ""){
                //$color_rows
                $color_field = sprintf("item_%s",$color_index);

                $color_arr = explode(",", $colors);
                $color_arr = array_unique($color_arr);

                try {
                    DB::table('goods_color')
                       ->where('goods_no','=',$goods_no)
                       ->where('goods_sub','=',$goods_sub)
                       ->delete();
                   //$sql_result1 = 1;
                   $goods_color_del_result = 200;
                } catch(Exception $e){
                   //$sql_result1 = 0;
                   $goods_color_del_result = 500;
                }

                for($j=0; $j < count($color_arr); $j++)
                {
                    $_color = trim(strtoupper($color_arr[$j]));
                    if($_color == "" ) continue;

                    if(in_array($_color, $color_rows)){

                        $_color_key = array_search($_color, $color_rows);

                        $color_insert = [
                            'goods_no' => $goods_no,
                            'goods_sub' => $goods_sub,
                            'color' => $_color_key,
                            'admin_id' => $com_id,
                            'admin_nm' => $com_nm,
                            'rt' => now(),
                            'ut' => now()
                        ];
                    }

                }
            }

        }


        if(!empty($color_insert)){
            try {
                DB::table('goods_color')->insert($color_insert);
                //$code = 200;
                $goods_color_in_result = 200;
            } catch(Exception $e){
                //$code = 500;
                $goods_color_in_result = 500;
            }
        }

        echo json_encode(array(
            "goods_class_in_result" => $goods_class_in_result,
            "goods_class_up_result" => $goods_class_up_result,
            "goods_up_result" => $goods_up_result,
            "goods_color_del_result" => $goods_color_del_result,
            "goods_color_in_result" => $goods_color_in_result,

        ));
    }

    public function load_excel(Request $request){
        //$file = $request->input("file");
        if ( 0 < $_FILES['file']['error'] ) {
            echo json_encode(array(
                "code" => 500,
                "errmsg" => 'Error: ' . $_FILES['file']['error']
            ));
        }
        else {
            $file = sprintf("data/%s", $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
            echo json_encode(array(
                "code" => 200,
                "file" => $file
            ));
        }
    }

    public function show_excel(Request $request){
        $data = $request->input("data");
        $class = $request->input("class");
        $goods_class_rows = explode("^EOL", $data);
        $goods_query = "";
        /*
        echo $data;
        echo "<br><br>";
        */
        //$result;

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";




        for($i=0; $i<count($goods_class_rows); $i++){
            $query = "";
            $val = explode("@@@",$goods_class_rows[$i]);
            $sql_cols = "";

            $goods_no		= (isset($val[0])) ? $val[0]:"";
            $goods_sub		= (isset($val[1])) ? $val[1]:"";
            //$com_id		= (isset($val[2])) ? Rq($val[2]):"";
            //$CLASS		= (isset($val[3])) ? Rq($val[3]):"";
            /*
            $item_001		= (isset($val[4])) ? $val[4]:"";
            $item_002		= (isset($val[5])) ? $val[5]:"";
            $item_003		= (isset($val[6])) ? $val[6]:"";
            $item_004		= (isset($val[7])) ? $val[7]:"";
            $item_005		= (isset($val[8])) ? $val[8]:"";
            $item_006		= (isset($val[9])) ? $val[9]:"";
            $item_007		= (isset($val[10])) ? $val[10]:"";
            $item_008		= (isset($val[11])) ? $val[11]:"";
            $item_009		= (isset($val[12])) ? $val[12]:"";
            $item_010		= (isset($val[13])) ? $val[13]:"";
            $item_011		= (isset($val[14])) ? $val[14]:"";
            $item_012		= (isset($val[15])) ? $val[15]:"";
            $item_013		= (isset($val[16])) ? $val[16]:"";
            $item_014		= (isset($val[17])) ? $val[17]:"";
            $item_015		= (isset($val[18])) ? $val[18]:"";
            $item_016		= (isset($val[19])) ? $val[19]:"";
            $item_017		= (isset($val[20])) ? $val[20]:"";
            $item_018		= (isset($val[21])) ? $val[21]:"";
            $item_019		= (isset($val[22])) ? $val[22]:"";
            $item_020		= (isset($val[23])) ? $val[23]:"";
            */
            if($goods_no != ""){
                for($j=3; $j<count($val); $j++){
                    $num = ($j-2);
                    if($num<10){
                        $num = '00'.$num;
                    }elseif($num<100){
                        $num = '0'.$num;
                    }
                    if($val[$j] != ""){
                        $sql_cols .= ", '". $val[$j] ."' as 'item_$num'";
                    }
                }

                $where = " and g.goods_no='". $goods_no ."' and g.goods_sub='$goods_sub'";

                $query = "
                select
                    '' as blank,
                    ifnull( type.code_val, 'N/A') as goods_type,
                    com.com_nm, opt.opt_kind_nm, brand.brand_nm, g.style_no, '' as img2,
                    if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                        select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                        from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                    )) as img, g.goods_nm, stat.code_val as sale_stat_cl,
                    g.goods_no, g.goods_sub, com.com_id,
                    (select class_nm from code_class where class = g.class group by class, class_nm) as class
                    $sql_cols
                from goods g 
                    left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                    left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                    left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                    left outer join company com on com.com_id = g.com_id
                    left outer join brand brand on brand.brand = g.brand
                    left outer join goods_class class on g.goods_no = class.goods_no and g.goods_sub = class.goods_sub and g.class = class.class
                where 1=1 
                    $where
                ";

                //
                //$result[$i] = $goods_result;
            }
        //$result = DB::select($query,['com_id' => $com_id]);
            if($query != ""){
                if($i>0){
                    $goods_query .= " union all ". $query;
                }else{
                    $goods_query = $query;
                }
            }


        }
        $result = DB::select($goods_query);
        /*
        echo $goods_query;
        echo "<br><br>";
        */
        echo json_encode(array(
            "result" => $result,

        ));

    }

    private function set_excel_download($filename_format) {
        $filename = sprintf($filename_format,date("YmdHis"));

        header("Content-type: application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");
        Header("Content-Transfer-Encoding: binary");
        Header("Pragma: no-cache");
        Header("Expires: 0");

    }


    public function down_excel(Request $request){
        $com_id = Auth('partner')->user()->com_id;

        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $class          = $request->input("class");         // 분류
        $goods_type     = $request->input("goods_type");    // 상품구분
        $goods_stat     = $request->input("goods_stat");    // 상품 상태
        $style_no       = $request->input("style_no");      // 스타일 넘버
        $goods_no       = $request->input("goods_no");      // 상품번호
        $opt_kind_cd    = $request->input("opt_kind_cd");   // 품목
        $brand_nm       = $request->input("brand_nm");      // 브랜드 이름
        $brand_cd       = $request->input("brand_cd");      // 브랜드
        $ad_desc        = $request->input("ad_desc");       // 상/하단 홍보문구
        $goods_nm       = $request->input("goods_nm");      // 상품명
        $ord_field      = $request->input("ord_field");     // 정렬필드
        $ord            = $request->input("ord");           // 정렬
        $goods          = $request->input("goods");

        /*
        echo "class : ". $class;
        echo "<br>";
        */
        $where = "";
        $insql = "";
        $order_field = "";
        $class_items_fields = "";

        if($goods_type != "")	$where .= " and g.goods_type = '$goods_type' ";
		if($goods_stat != "")	$where .= " and g.sale_stat_cl = '$goods_stat' ";
		if($style_no != "")	$where .= " and g.style_no like '$style_no%' ";
		if($goods_no != ""){
			// 상품코드 검색
			$goods_nos = explode(",",$goods_no);
			if(count($goods_nos) > 1){
				if(count($goods_nos) > 50) array_splice($goods_nos,50);
				$in_goods_nos = join(",",$goods_nos);
				$where .= " and g.goods_no in ( $in_goods_nos ) ";
			}else{
				$where .= " and g.goods_no = '$goods_no' ";
			}
		}

		if($goods != ""){
			// 파일로 검색일 경우
			$goods_arr = explode(",",$goods);
			for($i=0;$i<count($goods_arr);$i++){
				if(empty($goods_arr[$i])) continue;
				list($no,$sub) = explode("\|",$goods_arr[$i]);
				if($insql == ""){
					$insql .= " select '$no' as no,'$sub' as sub ";
				}else{
					$insql .= " union select '$no' as no,'$sub' as sub  ";
				}
			}
			$insql = " inner join ( $insql ) sg on g.goods_no = sg.no and g.goods_sub = sg.sub ";
        }

		if($com_id != "")				$where .= " and g.com_id = '$com_id' ";
		if($opt_kind_cd != "")		$where .= " and g.opt_kind_cd = '$opt_kind_cd' ";
		if($brand_cd != "")			$where .= " and g.brand = '$brand_cd' ";
		if($goods_nm != "")			$where .= " and g.goods_nm like '%$goods_nm%' ";
		if($ad_desc != "")			$where .= " and ( g.ad_desc like '%$ad_desc%' or g.head_desc like '%$ad_desc%' ) ";

        if($ord_field != "")    $order_field = "order by ". $ord_field;
        if($ord != "")  $order_field .= " ". $ord;

		if($class == ""){
			$where .= " and ifnull(g.class,'') = '' ";
		}else{
			$where .= " and g.class = '$class' ";
		}

        // 분류별 미등록 항목 검색


        /*
		for($i=1; $i<=20;$i++){
		}*/
        $title_fields = array();
        $columns_val = array();
        $title_fields[] = "상품구분";
        $columns_val[] = "goods_type";
        $title_fields[] = "업체";
        $columns_val[] = "com_nm";
        $title_fields[] = "품목";
        $columns_val[] = "opt_kind_nm";
        $title_fields[] = "브랜드";
        $columns_val[] = "brand_nm";
        $title_fields[] = "스타일넘버";
        $columns_val[] = "style_no";
        $title_fields[] = "이미지";
        $columns_val[] = "img";
        $title_fields[] = "상품명";
        $columns_val[] = "goods_nm";
        $title_fields[] = "상품상태";
        $columns_val[] = "sale_stat_cl";
        $title_fields[] = "상품번호";
        $columns_val[] = "goods_no";
        $columns_val[] = "goods_sub";
        $title_fields[] = "분류";
        $columns_val[] = "class";
        $sql_cols = "";
		if($class != ""){
			// 분류에 따른 검색 컬럼
			$sql = "
				select item as id, item_nm as val
				from code_class
				where class = '$class' order by item+0
			";
            $rows = DB::select($sql);


            foreach ($rows as $row) {
                $title_fields[] = $row->val;
                $columns_val[] = "item_".$row->id;
                $sql_cols .= sprintf(",class.item_%s",$row->id);
            }

           $class_items_fields = $rows;

        }

        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $query = "
            select
                '' as blank,
                ifnull( type.code_val, 'N/A') as goods_type,
                com.com_nm, opt.opt_kind_nm, brand.brand_nm, g.style_no, '' as img2,
                if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img, g.goods_nm, stat.code_val as sale_stat_cl,
                g.goods_no, g.goods_sub, com.com_id,
                (select class_nm from code_class where class = g.class group by class, class_nm) as class
                $sql_cols
			from goods g $insql
                left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
                left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
                left outer join company com on com.com_id = g.com_id
                left outer join brand brand on brand.brand = g.brand
                left outer join goods_class class on g.goods_no = class.goods_no and g.goods_sub = class.goods_sub and g.class = class.class
            where 1=1 
                $where
            $order_field
        ";
        //echo $query;
        //$result = DB::select($query,['com_id' => $com_id]);
        $result = DB::select($query);
        //echo count($header);

        //echo count($result);
        //echo count($columns_val);
        //echo count($title_fields);

        $this->set_excel_download("GoodsClassProperty_%s.xls");

        return view( Config::get('shop.partner.view') . '/product/prd05_excel',[
            'rows' => $result,
            'fields' => $columns_val,
            'headers' => $title_fields
        ]);

    }



	/*
		public function show($goods_no){
			return view( Config::get('shop.partner.view') . '/product/prd01_show',
				['goods_no' => $goods_no]
			);
		}
	*/

}
