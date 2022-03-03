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
use App\Models\Product;

class prd03Controller extends Controller
{
<<<<<<< HEAD
    public function index() {
        $sql = "
            select group_no, group_nm, dc_ratio as ratio
            from user_group
            where is_wholesale = 'Y'
            order by dc_ratio asc
        ";
        $group_columns = DB::select($sql);
        
        $values = [
            'goods_types'   => SLib::getCodes('G_GOODS_TYPE'),
            'goods_stats'   => SLib::getCodes('G_GOODS_STAT'),
            'opt_kind_cds'  => SLib::getItems(),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'group_columns' => $group_columns,
        ];
        return view(Config::get('shop.head.view') . '/product/prd03', $values);
    }

    public function search(Request $request) {
        $rows = [];
        $page_cnt = 0;
        $goods_cnt = 0;
        $page = 1;

        $cfg_img_size_list = SLib::getCodesValue('G_IMG_SIZE', 'list');
		$cfg_img_size_real = SLib::getCodesValue('G_IMG_SIZE', 'real');

        $goods_stat = $request->input("goods_stat", ''); // 상품상태
        $style_no = $request->input("style_no", ''); // 스타일넘버
        $goods_no = $request->input("goods_no", ''); // 상품코드
        $goods_nm = $request->input("goods_nm", ''); // 상품명

        $goods_type = $request->input("goods_type", ''); // 상품구분
        $not_price = $request->input("not_price", 'N'); // 미설정상품 여부
        $ne_margin = $request->input("ne_margin", 'N'); // 기본마진과 다른 상품 여부
        $limit = $request->input("limit", 100);	// 출력수
        $ord_field = $request->input("ord_field", 'goods_no'); // 정렬필드
        $ord = $request->input("ord", 'desc'); // 정렬
        
        $opt_kind_cd = $request->input("opt_kind_cd", ''); // 품목
        $brand_cd = $request->input("brand_cd", ''); // 브랜드
        $com_id = $request->input("com_cd", ''); // 업체

        $page = $request->input("page", 1);
        if ($page < 1 or $page == "") $page = 1;
        $page_size = $limit;

        $price_cols = "";
=======
	public function index() {
		$sql = "
			select group_no, group_nm, dc_ratio as ratio
			from user_group
			where is_wholesale = 'Y'
			order by dc_ratio asc
		";
		$group_columns = DB::select($sql);
		
		$values = [
			'goods_types'   => SLib::getCodes('G_GOODS_TYPE'),
			'goods_stats'   => SLib::getCodes('G_GOODS_STAT'),
			'opt_kind_cds'  => SLib::getItems(),
			'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'group_columns' => $group_columns,
		];
		return view(Config::get('shop.head.view') . '/product/prd03', $values);
	}

	public function search(Request $request) {
		$rows = [];
		$page_cnt = 0;
		$goods_cnt = 0;
		$page = 1;

		$cfg_img_size_list = SLib::getCodesValue('G_IMG_SIZE', 'list');
		$cfg_img_size_real = SLib::getCodesValue('G_IMG_SIZE', 'real');

		$goods_stat = $request->input("goods_stat", ''); // 상품상태
		$style_no = $request->input("style_no", ''); // 스타일넘버
		$goods_no = $request->input("goods_no", ''); // 상품코드
		$goods_nm = $request->input("goods_nm", ''); // 상품명

		$goods_type = $request->input("goods_type", ''); // 상품구분
		$not_price = $request->input("not_price", 'N'); // 미설정상품 여부
		$ne_margin = $request->input("ne_margin", 'N'); // 기본마진과 다른 상품 여부
		$limit = $request->input("limit", 100);	// 출력수
		$ord_field = $request->input("ord_field", 'goods_no'); // 정렬필드
		$ord = $request->input("ord", 'desc'); // 정렬
		
		$opt_kind_cd = $request->input("opt_kind_cd", ''); // 품목
		$brand_cd = $request->input("brand_cd", ''); // 브랜드
		$com_id = $request->input("com_cd", ''); // 업체

		$page = $request->input("page", 1);
		if ($page < 1 or $page == "") $page = 1;
		$page_size = $limit;

		$price_cols = "";
>>>>>>> main
		$print_cols = "";
		$group_cnt = 0;
		$group_nos = [];

		$sql = "
			select group_no, dc_ratio as margin
			from user_group
			where is_wholesale = 'Y'
			order by dc_ratio asc
		";
		$rows = DB::select($sql);

<<<<<<< HEAD
        foreach($rows as $row) {
            $group_no = $row->group_no;
            $margin = $row->margin;
            
            array_push($group_nos, ['no' => $group_no, 'margin' => $margin]);

            $price_cols .= sprintf(" sum(if(p.group_no = %d,p.price,0)) as group_%d_price, \n",$group_no,$group_no);
=======
		foreach($rows as $row) {
			$group_no = $row->group_no;
			$margin = $row->margin;
			
			array_push($group_nos, ['no' => $group_no, 'margin' => $margin]);

			$price_cols .= sprintf(" sum(if(p.group_no = %d,p.price,0)) as group_%d_price, \n",$group_no,$group_no);
>>>>>>> main
			$price_cols .= sprintf(" sum(if(p.group_no = %d,round((p.price - g.wonga)/p.price*100),0)) as group_%d_ratio, \n",$group_no,$group_no);
			$price_cols .= sprintf(" sum(if(p.group_no = %d,round((g.price - p.price)/g.price*100),0)) as group_%d_dc_ratio, \n",$group_no,$group_no);
			$print_cols .= sprintf(" %s as group_%d, \n",$group_no,$group_no);
			$print_cols .= sprintf(" '%s' as group_%d_margin, \n",$margin,$group_no);
			$print_cols .= sprintf(" p.group_%d_price as group_%d_price, \n",$group_no,$group_no);
			$print_cols .= sprintf(" p.group_%d_ratio as group_%d_ratio, \n",$group_no,$group_no);
			$print_cols .= sprintf(" p.group_%d_dc_ratio as group_%d_dc_ratio, \n",$group_no,$group_no);

<<<<<<< HEAD
            $group_cnt++;
        }

        $where = "";
		$having = "";

        if($goods_stat != "")		$where .= " and g.sale_stat_cl = '$goods_stat' ";
=======
			$group_cnt++;
		}

		$where = "";
		$having = "";

		if($goods_stat != "")		$where .= " and g.sale_stat_cl = '$goods_stat' ";
>>>>>>> main
		if($style_no != "")			$where .= " and g.style_no like '$style_no%' ";
		if($goods_no != "")			$where .= " and g.goods_no = '$goods_no' ";
		if($goods_nm != "")			$where .= " and g.goods_nm like '%$goods_nm%' ";
		if($goods_type != "")		$where .= " and g.goods_type = '$goods_type' ";
		if($opt_kind_cd != "")		$where .= " and g.opt_kind_cd = '$opt_kind_cd' ";
		if($brand_cd != "")			$where .= " and g.brand ='$brand_cd'";
		if($com_id != "")			$where .= " and g.com_id = '$com_id' ";
<<<<<<< HEAD
        if ($not_price != ""){
=======
		if ($not_price != ""){
>>>>>>> main
			if($having == ""){
				$having .= " having ifnull(group_cnt,0)  < '$group_cnt' ";
			} else {
				$having .= " and ifnull(group_cnt,0)  < '$group_cnt' ";
			}
		}
		if ($ne_margin != ""){
			for($i=0;$i<count($group_nos);$i++){
				if($having == ""){
					$having = sprintf(" having group_%d_ratio <> %d ",$group_nos[$i]["no"],$group_nos[$i]["margin"]);
				} else {
					$having .= sprintf(" or group_%d_ratio <> %d ",$group_nos[$i]["no"],$group_nos[$i]["margin"]);
				}
			}
		}

<<<<<<< HEAD
        ##############################################################
        # 리스트 페이징 처리
        ##############################################################
        $data_cnt = 0;
        $page_cnt = 0;
        $goods_cnt = 0;

        if ($page == 1) {

            $sql = "
                select count(*) as cnt
                from (
                    select
                        g.goods_no, g.goods_sub,
                        $price_cols
                        count(*) as group_cnt
                    from goods g left outer join goods_price p
                        on g.goods_no = p.goods_no and g.goods_sub = p.goods_sub
                    where 1=1 $where
                    group by g.goods_no,g.goods_sub
                    $having
                ) p
			";

            $row = DB::selectOne($sql);

            $data_cnt = $row->cnt;

            // 페이지 얻기
            $page_cnt=(int)(($data_cnt-1)/$page_size) + 1;

            if ($page == 1) {
                $startno = ($page-1) * $page_size;
            } else {
                $startno = ($page-1) * $page_size;
            }

        } else {
            $startno = ($page - 1) * $page_size;
        }

        if ($limit == -1) $limit = "";
        else $limit = " limit $startno,$page_size ";

        $sql = "
            select
                '' as chk,
                a.goods_no,a.goods_sub, a.style_no,
                o.opt_kind_nm, ifnull( cd3.code_val, 'N/A') as goods_type, b.brand_nm, c.com_nm,
                '' as image, a.goods_nm,
                ifnull( (
                    select sum(good_qty) from goods_summary
                    where goods_no = a.goods_no and goods_sub = a.goods_sub
                ), 0 ) as qty,
                ifnull( (
                    select sum(wqty) from goods_summary
                    where goods_no = a.goods_no and goods_sub = a.goods_sub
                ), 0 ) as wqty,
                a.price, a.wonga, if(a.wonga > 0,round((a.price - a.wonga)/a.price*100,1),0) as margin,
                replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img,
                $print_cols
                '' as ed
            from goods a inner join (
                select
                    g.goods_no, g.goods_sub,
                    $price_cols
                    sum(if(p.price > 0,1,0)) as group_cnt
                from goods g left outer join goods_price p
                    on g.goods_no = p.goods_no and g.goods_sub = p.goods_sub
                where 1=1 $where
                    group by g.goods_no,g.goods_sub
                $having
                order by $ord_field $ord $limit
            ) p on a.goods_no = p.goods_no and a.goods_sub = p.goods_sub
            inner join brand b on b.brand = a.brand and b.brand_type = 'S'
            left outer join company c on a.com_id = c.com_id
            inner join opt o on o.opt_kind_cd = a.opt_kind_cd and o.opt_id = 'K'
            inner join code cd3 on cd3.code_kind_cd = 'G_GOODS_TYPE' and a.goods_type = cd3.code_id
        ";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $data_cnt,
                "page" => $page,
                "page_cnt" => count($rows),
                "page_total" => $page_cnt
            ),
            "body" => $rows,
        ]);
    }

    public function save_price(Request $request) {
        $code = 200;
        $msg = '';

        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        $rows = $request->input('data');

        $sql = "
            select group_no
            from user_group
            where is_wholesale = 'Y'
            order by dc_ratio asc
        ";
        $group_columns = DB::select($sql);

        try {
            DB::beginTransaction();

            foreach($rows as $row) {
                foreach($group_columns as $col) {
                    $goods_no = $row['goods_no'];
                    $goods_sub = $row['goods_sub'];
                    $group_no = $col->group_no;
                    $price = $row['group_' . $group_no . '_price'];

                    $sql = "
                        select count(*) as cnt
                        from goods_price
                        where goods_no = :goods_no
                            and goods_sub = :goods_sub
                            and group_no = :group_no
                    ";
                    $no_data = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'group_no' => $group_no]);
                    $no_data = $no_data->cnt === 0;

                    if($no_data) {
                        $sql = "
                            insert into goods_price (
                                goods_no, goods_sub, group_no, price, rt, ut, admin_id, admin_nm
                            ) values (
                                '$goods_no', '$goods_sub', '$group_no', '$price', now(), now(), '$id', '$name'
                            )
                        ";
                        DB::insert($sql);
                    } else {
                        $sql = "
                            update goods_price set
                                price = '$price' ,
                                ut = now(),
                                admin_id = '$id' ,
                                admin_nm = '$name'
                            where
                                goods_no = '$goods_no'
                                and goods_sub = '$goods_sub'
                                and group_no = '$group_no'
                        ";
                        DB::update($sql);
                    }
                }
            }

            DB::commit();
            $msg = '저장되었습니다.';
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = '에러가 발생했습니다. 잠시 후 다시 시도해주세요.';
        }

        return response()->json(["code" => $code, "message" => $msg], $code);
    }

    /*
    ***
    상품원가 관련
    ***
    */

    public function wonga_index(Request $request) {
        $goods_no = $request->goods_no;
        $goods_sub = $request->goods_sub;

        $sql = "
			select g.price, g.goods_nm, c.com_nm
			from goods g inner join company c on g.com_id = c.com_id
			where goods_no = $goods_no and goods_sub = $goods_sub
        ";
        $row = DB::selectOne($sql);
        $price = $row->price;
        $goods_nm = $row->goods_nm;
        $com_nm = $row->com_nm;

        $sql = "
            select
                round(sum(wonga*qty)/sum(qty)) as g_wonga, ifnull(sum(qty),0) as avail_qty, ifnull(sum(wonga*qty),0) as sum_wonga
            from goods_good
            where goods_no='$goods_no'
                and goods_sub='$goods_sub'
                and qty > 0
        ";
        $row = DB::selectOne($sql);
        $avg_wonga = $row->g_wonga ?: 0;
        $avail_qty = $row->avail_qty;
        $sum_wonga = $row->sum_wonga;
        
        $values = [
            'goods_no'  => $goods_no,
            'goods_sub' => $goods_sub,
            'goods_nm'  => $goods_nm,
            'com_nm'    => $com_nm,
            'price'     => $price,
            'avg_wonga' => $avg_wonga,
            'avail_qty' => $avail_qty,
            'sum_wonga' => $sum_wonga,
        ];

        return view(Config::get('shop.head.view') . '/product/prd03_wonga', $values);
    }

    public function wonga_search(Request $request) {
        $goods_no = $request->input('goods_no');
        $goods_sub = $request->input('goods_sub');
        $price = $request->input('price');

        $sql = "
            select
                a.invoice_no,
                date_format(a.regi_date,'%Y.%m.%d') as regi_date,
                c.com_nm as com_nm,
                a.wonga as h_wonga,a.qty,
                a.goods_opt,
                '' as margin
            from goods_good a
                inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
                inner join company c on g.com_id = c.com_id
            where
                a.goods_no = '$goods_no'
                and a.goods_sub = '$goods_sub'
                and a.qty > 0
            order by a.regi_date desc
        ";
        $rows = DB::select($sql);

        foreach($rows as $row) {
            $wonga = $row->h_wonga;
            $profit = $price - $wonga; // 판매이익
            $margin = $profit / $price * 100;
            $row->margin = number_format($margin, 2);
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows),
            ),
            "body" => $rows,
        ]);
    }
=======
		##############################################################
		# 리스트 페이징 처리
		##############################################################
		$data_cnt = 0;
		$page_cnt = 0;
		$goods_cnt = 0;

		if ($page == 1) {

			$sql = "
				select count(*) as cnt
				from (
					select
						g.goods_no, g.goods_sub,
						$price_cols
						count(*) as group_cnt
					from goods g left outer join goods_price p
						on g.goods_no = p.goods_no and g.goods_sub = p.goods_sub
					where 1=1 $where
					group by g.goods_no,g.goods_sub
					$having
				) p
			";

			$row = DB::selectOne($sql);

			$data_cnt = $row->cnt;

			// 페이지 얻기
			$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;

			if ($page == 1) {
				$startno = ($page-1) * $page_size;
			} else {
				$startno = ($page-1) * $page_size;
			}

		} else {
			$startno = ($page - 1) * $page_size;
		}

		if ($limit == -1) $limit = "";
		else $limit = " limit $startno,$page_size ";

		$sql = "
			select
				'' as chk,
				a.goods_no,a.goods_sub, a.style_no,
				o.opt_kind_nm, ifnull( cd3.code_val, 'N/A') as goods_type, b.brand_nm, c.com_nm,
				'' as image, a.goods_nm,
				ifnull( (
					select sum(good_qty) from goods_summary
					where goods_no = a.goods_no and goods_sub = a.goods_sub
				), 0 ) as qty,
				ifnull( (
					select sum(wqty) from goods_summary
					where goods_no = a.goods_no and goods_sub = a.goods_sub
				), 0 ) as wqty,
				a.price, a.wonga, if(a.wonga > 0,round((a.price - a.wonga)/a.price*100,1),0) as margin,
				replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img,
				$print_cols
				'' as ed
			from goods a inner join (
				select
					g.goods_no, g.goods_sub,
					$price_cols
					sum(if(p.price > 0,1,0)) as group_cnt
				from goods g left outer join goods_price p
					on g.goods_no = p.goods_no and g.goods_sub = p.goods_sub
				where 1=1 $where
					group by g.goods_no,g.goods_sub
				$having
				order by $ord_field $ord $limit
			) p on a.goods_no = p.goods_no and a.goods_sub = p.goods_sub
			inner join brand b on b.brand = a.brand and b.brand_type = 'S'
			left outer join company c on a.com_id = c.com_id
			inner join opt o on o.opt_kind_cd = a.opt_kind_cd and o.opt_id = 'K'
			inner join code cd3 on cd3.code_kind_cd = 'G_GOODS_TYPE' and a.goods_type = cd3.code_id
		";

		$rows = DB::select($sql);

		foreach($rows as $row) {
			if ($row->img != "") { // 이미지 url
				$row->img = sprintf("%s%s",config("shop.image_svr"),$row->img);
			}
		}

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($rows),
				"page_total" => $page_cnt
			),
			"body" => $rows,
		]);
	}

	public function save_price(Request $request) {
		$code = 200;
		$msg = '';

		$id = Auth('head')->user()->id;
		$name = Auth('head')->user()->name;
		$rows = $request->input('data');

		$sql = "
			select group_no
			from user_group
			where is_wholesale = 'Y'
			order by dc_ratio asc
		";
		$group_columns = DB::select($sql);

		try {
			DB::beginTransaction();

			foreach($rows as $row) {
				foreach($group_columns as $col) {
					$goods_no = $row['goods_no'];
					$goods_sub = $row['goods_sub'];
					$group_no = $col->group_no;
					$price = $row['group_' . $group_no . '_price'];

					$sql = "
						select count(*) as cnt
						from goods_price
						where goods_no = :goods_no
							and goods_sub = :goods_sub
							and group_no = :group_no
					";
					$no_data = DB::selectOne($sql, ['goods_no' => $goods_no, 'goods_sub' => $goods_sub, 'group_no' => $group_no]);
					$no_data = $no_data->cnt === 0;

					if($no_data) {
						$sql = "
							insert into goods_price (
								goods_no, goods_sub, group_no, price, rt, ut, admin_id, admin_nm
							) values (
								'$goods_no', '$goods_sub', '$group_no', '$price', now(), now(), '$id', '$name'
							)
						";
						DB::insert($sql);
					} else {
						$sql = "
							update goods_price set
								price = '$price' ,
								ut = now(),
								admin_id = '$id' ,
								admin_nm = '$name'
							where
								goods_no = '$goods_no'
								and goods_sub = '$goods_sub'
								and group_no = '$group_no'
						";
						DB::update($sql);
					}
				}
			}

			DB::commit();
			$msg = '저장되었습니다.';
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = '에러가 발생했습니다. 잠시 후 다시 시도해주세요.';
		}

		return response()->json(["code" => $code, "message" => $msg], $code);
	}

	/*
	***
	상품원가 관련
	***
	*/

	public function wonga_index(Request $request) {
		$goods_no = $request->goods_no;
		$goods_sub = $request->goods_sub;

		$sql = "
			select g.price, g.goods_nm, c.com_nm
			from goods g inner join company c on g.com_id = c.com_id
			where goods_no = $goods_no and goods_sub = $goods_sub
		";
		$row = DB::selectOne($sql);
		$price = $row->price;
		$goods_nm = $row->goods_nm;
		$com_nm = $row->com_nm;

		$sql = "
			select
				round(sum(wonga*qty)/sum(qty)) as g_wonga, ifnull(sum(qty),0) as avail_qty, ifnull(sum(wonga*qty),0) as sum_wonga
			from goods_good
			where goods_no='$goods_no'
				and goods_sub='$goods_sub'
				and qty > 0
		";
		$row = DB::selectOne($sql);
		$avg_wonga = $row->g_wonga ?: 0;
		$avail_qty = $row->avail_qty;
		$sum_wonga = $row->sum_wonga;
		
		$values = [
			'goods_no'  => $goods_no,
			'goods_sub' => $goods_sub,
			'goods_nm'  => $goods_nm,
			'com_nm'    => $com_nm,
			'price'     => $price,
			'avg_wonga' => $avg_wonga,
			'avail_qty' => $avail_qty,
			'sum_wonga' => $sum_wonga,
		];

		return view(Config::get('shop.head.view') . '/product/prd03_wonga', $values);
	}

	public function wonga_search(Request $request) {
		$goods_no = $request->input('goods_no');
		$goods_sub = $request->input('goods_sub');
		$price = $request->input('price');

		$sql = "
			select
				a.invoice_no,
				date_format(a.regi_date,'%Y.%m.%d') as regi_date,
				c.com_nm as com_nm,
				a.wonga as h_wonga,a.qty,
				a.goods_opt,
				'' as margin
			from goods_good a
				inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				inner join company c on g.com_id = c.com_id
			where
				a.goods_no = '$goods_no'
				and a.goods_sub = '$goods_sub'
				and a.qty > 0
			order by a.regi_date desc
		";
		$rows = DB::select($sql);

		foreach($rows as $row) {
			$wonga = $row->h_wonga;
			$profit = $price - $wonga; // 판매이익
			$margin = $profit / $price * 100;
			$row->margin = number_format($margin, 2);
		}

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => count($rows),
			),
			"body" => $rows,
		]);
	}
>>>>>>> main
}