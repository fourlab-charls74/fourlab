<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk30Controller extends Controller
{
    //
    public function index() {

        $values = [
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
            'items'         => SLib::getItems(),
			'sites'			=> SLib::getSalePlaces('Y'),
        ];
        return view( Config::get('shop.head.view') . '/stock/stk30',$values);
    }

    public function search(Request $request)
    {
		$join	= "";
        $page	= $request->input('page', 1);
        if( $page < 1 or $page == "" )	$page = 1;
        $limit	= $request->input('limit', 100);

		$s_goods_type	= $request->input("s_goods_type");		// 상품 구분
		$s_goods_stat	= $request->input("s_goods_stat");		// 상품 상태
		$s_ex_trash		= $request->input("s_ex_trash");		// 휴지통 제외
		$s_style_no		= $request->input("s_style_no");		// 스타일넘버
		$s_goods_no		= $request->input("s_goods_no");		// 상품번호
		$s_com_type		= $request->input("s_com_type");		// 업체구분
		//$s_com_nm		= $request->input("s_com_nm");			// 업체명
		$s_com_id		= $request->input("s_com_id");			// 업체아이디
		$s_opt_kind_cd	= $request->input("s_opt_kind_cd");		// 품목
		//$S_BRAND_NM		= $request->input("S_BRAND_NM");	// 브랜드명
		$s_brand_cd		= $request->input("s_brand_cd");		// 브랜드
		$s_site			= $request->input("s_site");			// 판매사이트
		$s_match		= $request->input("s_match");			// 매칭여부

        $limit			= $request->input("limit",100);
        $ord			= $request->input('ord','desc');
        $ord_field		= $request->input('ord_field','g.goods_no');
        $orderby		= sprintf("order by %s %s", $ord_field, $ord);

		//$S_GOODS_NM	= $request->input("S_GOODS_NM");		// 상품명
		//$LIMIT		= $request->input("LIMIT");				// 출력수
		//$S_GOODS		= $request->input("S_GOODS");			// 상품선택
		//$S_EX_SITE	= $request->input("EX_SITE");

		$where = "";
		if( $s_goods_type != "" )	$where .= " and g.goods_type = '" . Lib::quote($s_goods_type) . "' ";
		if( $s_goods_stat != "" )	$where .= " and g.sale_stat_cl = '" . Lib::quote($s_goods_stat) . "' ";
		if( $s_ex_trash == "Y" )	$where .= " and g.sale_stat_cl > 0 ";
		if( $s_style_no	!= "" )		$where .= " and g.style_no like '" . Lib::quote($s_style_no) . "%' ";

		if( $s_goods_no != "" )
		{
			$goods_nos	= explode(",",$s_goods_no);
			if( count($goods_nos) > 1 )
			{
				if( count($goods_nos) > 50 )	array_splice($goods_nos,50);
				$in_goods_nos	= join(",",$goods_nos);
				$where	.= " and g.goods_no in ( $in_goods_nos ) ";
			}
			else
			{
				$where .= " and g.goods_no = '" . Lib::quote($s_goods_no) . "' ";
			}
		}

		if( $s_com_type != "" )		$where .= " and g.com_type = '" . Lib::quote($s_com_type) . "' ";
		if( $s_com_id != "" )		$where .= " and g.com_id = '" . Lib::quote($s_com_id) . "' ";
		if( $s_opt_kind_cd != "" )	$where .= " and g.opt_kind_cd = '" . Lib::quote($s_opt_kind_cd) . "' ";
		if( $s_brand_cd != "" )		$where .= " and g.brand = '" . Lib::quote($s_brand_cd) . "' ";

		if( $s_site != "" )
		{
			$join	.= " inner join goods_site s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub and s.site = '" . Lib::quote($s_site) . "' ";
		}

		if( $s_match != "" )
		{
			if( $s_match == "Y" )	$where .= " and x.goods_no is not null";
			else					$where .= " and x.goods_no is null";
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
				$join
				inner join goods_summary gs on g.goods_no = gs.goods_no and g.goods_sub = gs.goods_sub
				left outer join goods_xmd x on gs.goods_no = x.goods_no and gs.goods_sub = x.goods_sub and gs.goods_opt = x.goods_opt
				where 1=1 
                    $where
			";
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		$query	= "
			select
				'' as state, opt.opt_kind_nm, brand.brand_nm,g.goods_no,g.style_no,
				stat.code_val as sale_stat_cl_val,
				g.goods_nm,gs.goods_opt, gs.good_qty as qty, x.cd
			from goods g
			$join
			inner join goods_summary gs on g.goods_no = gs.goods_no and g.goods_sub = gs.goods_sub
			left outer join goods_xmd x on gs.goods_no = x.goods_no and gs.goods_sub = x.goods_sub and gs.goods_opt = x.goods_opt
			left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
			left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
			left outer join brand brand on brand.brand = g.brand
			where 1=1 $where
            $orderby
			$limit
		";

        $result = DB::select($query);

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
                "total"		=> $total,
                "page"		=> $page,
                "page_cnt"	=> $page_cnt,
                "page_total"=> count($result)
            ),
            "body" => $result
        ]);

	}

    public function show()
	{

		$values = [
        ];

		return view( Config::get('shop.head.view') . '/stock/stk30_show',$values);
    }

	public function upload(Request $request)
	{

        if ( 0 < $_FILES['file']['error'] ) {
            echo json_encode(array(
                "code" => 500,
                "errmsg" => 'Error: ' . $_FILES['file']['error']
            ));
        }
        else {
			//$file = sprintf("data/stk30/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/stk30/%s", $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
            echo json_encode(array(
                "code" => 200,
                "file" => $file
            ));
        }

	}

	public function update(Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

        $datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}

		DB::beginTransaction();



		for( $i = 0; $i < count($datas); $i++ )
		{
			$data	= (array)$datas[$i];

			$cd			= $data["xmd_code"];
			$goods_no	= $data["goods_no"];
			$goods_opt	= $data["goods_opt"];

			$query	= " select count(*) as cnt from goods_xmd_imp2 where cd = :cd ";
			$rows	= DB::selectOne($query, ['cd' => $cd]);

			if( $rows->cnt == 0 )
			{
				$sql	= "
					insert into goods_xmd_imp2( cd,goods_no,goods_opt )
					values (  '$cd','$goods_no','$goods_opt' )
				";
				DB::insert($sql);
			}
		}

		$sql	= " update goods_xmd_imp2 set goods_opt = replace(goods_opt,' ^ ','^') ";
		DB::update( $sql);

		$sql	= " update goods_xmd_imp2 set goods_opt = replace(goods_opt,'\r','') where goods_opt like '%\r%'; ";
		DB::update( $sql);

		$sql	= "
			delete from goods_summary 
			where goods_no in (
				select goods_no from goods_xmd_imp2 group by goods_no
			)
		";
		DB::delete($sql);

		$sql	= "
			insert into goods_summary ( goods_no,goods_sub,opt_name,goods_opt,opt_price,opt_memo,good_qty,wqty,soldout_yn,use_yn, seq,rt,ut,bad_qty,last_date )
			select 
				a.goods_no, 0 as goods_sub,
				if( instr(goods_opt,'^') > 0,'컬러^사이즈','사이즈') as opt_name, 
				a.goods_opt,0 as opt_price,
				REVERSE(SUBSTR(REVERSE(a.cd),3,2)) AS opt_memo,
				0 as good_qty, 0 as wqty,'N' AS soldout_yn,'Y' AS use_yn, 0 AS seq,
				NOW() AS rt, NOW() AS ut, 0 AS bad_qty, DATE_FORMAT(NOW(),'%Y-%m-%d') AS last_date
			from ( select goods_no,goods_opt,max(cd) as cd from goods_xmd_imp2 group by goods_no,goods_opt )  a inner join goods g on a.goods_no = g.goods_no
		";
		DB::insert($sql);

		$sql	= "
			delete o.* FROM goods_option o 
			INNER JOIN (SELECT goods_no FROM goods_xmd_imp2 GROUP BY goods_no ) b ON o.goods_no = b.goods_no 
			WHERE o.type = 'basic' AND NAME IN ('사이즈','컬러^사이즈','컬러')
		";
		DB::delete($sql);

		$sql	= "
			insert into goods_option ( goods_no,goods_sub,type,name,required_yn,use_yn,seq,option_no,rt,ut )
			select 
				goods_no, goods_sub,'basic' as type,'사이즈' as name, 'Y' as required_yn, 
				'Y' as use_yn, 0 as seq, 0 as option_no, now() as rt, now() as ut
			from goods_summary 
			where goods_no in ( select goods_no from goods_xmd_imp2 group by goods_no ) and opt_name = '사이즈'
			group by goods_no, goods_sub
		";
		DB::insert($sql);

		$sql	= "
			insert into goods_option ( goods_no,goods_sub,type,name,required_yn,use_yn,seq,option_no,rt,ut )
			select 
				s.goods_no, s.goods_sub,'basic' as type,'사이즈' as name, 'Y' as required_yn, 
				'Y' as use_yn, 0 as seq, 0 as option_no, now() as rt, now() as ut
			from (
				select goods_no from goods_xmd_imp2 group by goods_no
			) a inner join goods_summary s on a.goods_no = s.goods_no
			where s.goods_no in ( select goods_no from goods_xmd_imp2 group by goods_no )
				and opt_name = '사이즈' and ( select count(*) from goods_option where goods_no = a.goods_no ) = 0
			group by s.goods_no,s.opt_name
		";
		DB::insert($sql);

		$sql	= "
			insert into goods_option ( goods_no,goods_sub,type,name,required_yn,use_yn,seq,option_no,rt,ut )
			select * from (
				select 
					s.goods_no, s.goods_sub,'basic' as type,'컬러' as name, 'Y' as required_yn, 
					'Y' as use_yn, 0 as seq, 0 as option_no, now() as rt, now() as ut
				from (
					select goods_no from goods_xmd_imp2 group by goods_no
				) a inner join goods_summary s on a.goods_no = s.goods_no
				where opt_name = '컬러^사이즈' and ( select count(*) from goods_option where goods_no = a.goods_no ) = 0
				group by s.goods_no,s.opt_name
				union 
				select 
					s.goods_no, s.goods_sub,'basic' as type,'사이즈' as name, 'Y' as required_yn, 
					'Y' as use_yn, 0 as seq, 1 as option_no, now() as rt, now() as ut
				from (
					select goods_no from goods_xmd_imp2 group by goods_no
				) a inner join goods_summary s on a.goods_no = s.goods_no
				where opt_name = '컬러^사이즈' and ( select count(*) from goods_option where goods_no = a.goods_no ) = 0
				group by s.goods_no,s.opt_name
			) a order by goods_no, goods_sub, name desc
		";
		DB::insert($sql);

		$sql	= " delete from goods_xmd where cd in ( select cd from goods_xmd_imp2 ) ";
		DB::delete($sql);

		$sql	= " insert into goods_xmd select cd,goods_no,0 as goods_sub,goods_opt,now() as rt, now() as ut from goods_xmd_imp2 ";
		DB::insert($sql);



		DB::commit();



		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);
	}

}
