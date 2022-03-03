<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk33Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(2, 'month')->format('Y-m-d');

		$values = [
            'sdate'	=> $sdate,
            'edate'	=> date("Y-m-d"),
        ];
		
        return view( Config::get('shop.head.view') . '/stock/stk33',$values);
    }

    public function search(Request $request)
    {
		$page	= $request->input("page",1);
		if( $page < 1 or $page == "" )	$page = 1;
		$limit	= $request->input("limit", 100);

		$sdate	= $request->input('sdate',Carbon::now()->sub(2, 'month')->format('Ymd'));
        $edate	= $request->input('edate',date("Ymd"));

		$page_size	= $limit;
        $startno	= ($page - 1) * $page_size;
        $limit		= " limit $startno, $page_size ";

		$total		= 0;
        $page_cnt	= 0;

		// 2번째 페이지 이후로는 데이터 갯수를 얻는 로직을 실행하지 않는다.
		if( $page == 1 )
		{
			// 갯수 얻기
			$sql	= "
				select
					count(*) as total
				from goods_xmd_import
				where 
					( rt >= :sdate and rt < date_add(:edate,interval 1 day))
			";
			$row	= DB::selectOne($sql, ['sdate' => $sdate,'edate' => $edate]);
			$total	= $row->total;
			$page_cnt	= (int)(($total - 1) / $page_size) + 1;
		}

		$query	= "
			select
				idx, rt, cnt, match_cnt, non_match_cnt, state
			from goods_xmd_import
			where 
				( rt >= :sdate and rt < date_add(:edate,interval 1 day))
			order by idx desc
			$limit
		";

        $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
				"total"		=> $total,
				"page"		=> $page,
				"page_cnt"	=> $page_cnt,
				"page_total"=> count($result)
            ),
            "body"	=> $result
        ]);

	}

    public function show($idx, Request $request)
	{
		$kind	= $request->input("kind", "");
		
		$values = [
			'idx'	=> $idx,
			'kind'	=> $kind
        ];

		return view( Config::get('shop.head.view') . '/stock/stk33_show',$values);
    }

    public function detail_search(Request $request)
    {
		$idx	= $request->input("idx");
		$kind	= $request->input("kind");


		if( $kind == "non_match_cnt" )
		{
			$sql	= "
				SELECT a.* FROM goods_xmd_stock a LEFT OUTER JOIN goods_xmd b ON a.cd = b.cd 
				WHERE a.imp_idx = :idx AND b.cd IS NULL
			";
		}
		elseif( $kind == "match_cnt" )
		{
			$sql	= "
				SELECT a.* FROM goods_xmd_stock a INNER JOIN goods_xmd b ON a.cd = b.cd 
				WHERE a.imp_idx = :idx
			";
		}

        $result = DB::select($sql, ['idx' => $idx]);

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
				"total" => count($result)
            ),
            "body"	=> $result
        ]);

	}

    public function show_insert(Request $request)
	{
		$values = [
        ];

		return view( Config::get('shop.head.view') . '/stock/stk33_show_insert',$values);
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
			//$file = sprintf("data/stk33/%s", $_FILES['file']['name']);
			$file = sprintf("data/head/stk33/%s", $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $file);
            echo json_encode(array(
                "code" => 200,
                "file" => $file
            ));
        }

	}

	public function update(Request $request)
	{
		set_time_limit(0);

		$id			= Auth('head')->user()->id;
		$name		= Auth('head')->user()->name;
		$buffer_cnt	= 0;	// 매장 버퍼링 재고
		$cnt		= 0;	// 데이터 카운트

		$error_code		= "200";
		$result_code	= "";

        $datas		= $request->input('data');
		$datas		= json_decode($datas);

		if( $datas == "" )
		{
			$error_code	= "400";
		}


//		DB::beginTransaction();

		$sql	= " truncate table tmp_goods_stock_import ";
		DB::select($sql);

		$sql_insert	= " insert into tmp_goods_stock_import( cd,goods_nm,color,price,cost, qty ) values ";

		for( $i = 0; $i < count($datas); $i++ )
		{
			$data	= (array)$datas[$i];

			$cd			= $data['cd'];
			$goods_nm	= $data['goods_nm'];
			$color		= $data['color'];
			$price		= $data['price'];
			$cost		= $data['cost'];
			$qty		= $data['qty'];

/*			
			$sql	= " 
				insert into tmp_goods_stock_import( cd,goods_nm,color,price,cost, qty )
				 values (  :cd, :goods_nm, :color, :price, :cost, :qty )
			";
			DB::insert($sql,
				[
					'cd'		=> $cd,
					'goods_nm'	=> Lib::quote($goods_nm),
					'color'		=> $color,
					'price'		=> $price,
					'cost'		=> $cost,
					'qty'		=> $qty
				]
			);
*/

			if( $i != 0 )	$sql_insert .= ",";
			$sql_insert	.= " ( '$cd', '".Lib::quote($goods_nm)."', '$color', '$price', '$cost', '$qty' ) ";

		}

		DB::insert($sql_insert);

		$sql	= "
			update tmp_goods_stock_import set
				price = replace(price,',',''),
				cost = replace(cost,',',''),
				qty = replace(qty,',','')
		";
		DB::update($sql);

		$sql	= " insert into goods_xmd_import ( day,cnt,match_cnt,non_match_cnt,state,rt,ut ) values ( date_format(now(),'%Y%m%d'),0,0,0,0,now(),now()) ";
		$idx	= DB::table('goods_xmd_import')->insertGetId(
			[
				//'day'			=> date_format(now(), '%Y%m%d'),
				'day'			=> date("Ymd"),
				'cnt'			=> 0,
				'match_cnt'		=> 0,
				'non_match_cnt'	=> 0,
				'state'			=> 0,
				'rt'			=> now(),
				'ut'			=> now()
			]
		);
		//DB::insert($sql);
		//$idx = $conn->Insert_ID( );

		// 신규 등록 로그 생성
		$sql	= " insert into goods_xmd_stock SELECT :idx, cd, goods_nm,color,price,cost,qty,'N' FROM tmp_goods_stock_import ";
		DB::insert($sql,['idx'	=> $idx]);

		$sql	= "
				update goods_xmd_stock a left outer join goods_xmd b on a.cd = b.cd
					set a.match_yn = if( ifnull(b.cd,'') = '','N','Y')
				where imp_idx = :idx
		";
		DB::update($sql,['idx'	=> $idx]);

		$sql	= "
			create temporary table _tmp_xmd_stock
			select
				count(*) as cnt, sum(if(match_yn = 'Y',1,0)) as match_cnt, sum(if( match_yn = 'Y',0,1)) as non_match_cnt
			from goods_xmd_stock
			where imp_idx = :idx
		";
		DB::select($sql,['idx'	=> $idx]);

		$sql	= "
			update goods_xmd_import a inner join _tmp_xmd_stock b set
				a.cnt = b.cnt,
				a.match_cnt = b.match_cnt,
				a.non_match_cnt = b.non_match_cnt
			where idx = :idx
		";
		DB::update($sql,['idx'	=> $idx]);


		// 엑셀 업로드 구현 종료↑

		// 재고 업데이트 시작↓


		$sql		= " select max(imp_idx) as imp_idx from goods_xmd_stock ";
		$row		= DB::selectOne($sql);

		$imp_idx	= $row->imp_idx;

		//원가 업데이트
		$sql	= " DROP TABLE IF EXISTS _tmp_goods_xmd_stock ";
		DB::select($sql);

		$sql	= "
			CREATE TABLE _tmp_goods_xmd_stock
			SELECT 
				g.goods_no, g.goods_sub, a.goods_opt,
				SUM(x1.qty) AS qty,
				MAX(x1.price) AS price,
				MAX(x1.cost) AS cost
			FROM goods_xmd a INNER JOIN goods g ON a.goods_no = g.goods_no
				INNER JOIN goods_xmd_stock x1 ON x1.imp_idx = :imp_idx AND a.cd = x1.cd
			GROUP BY g.goods_no, g.goods_sub, a.goods_opt
		";
		DB::select($sql,['imp_idx'	=> $imp_idx]);

		$sql	= "
			UPDATE goods g INNER JOIN _tmp_goods_xmd_stock b ON g.goods_no = b.goods_no
			SET wonga = ROUND(b.cost * 1.1)
		";
		DB::update($sql);

		// 2019-06-20 상품가격 자동반영 ( max price 적용 )
		// 2019-07-24 재고있는 상품만 가격 반영
		// 2021-03-17 ceduce 최고가격 등록 프로세스 수정
		$sql	= " delete from _tmp_goods_xmd_stock_maxprice ";
		DB::delete($sql);
		
		$sql	= "
			insert into _tmp_goods_xmd_stock_maxprice
			select 
				goods_no, max(price) as max_price
			from _tmp_goods_xmd_stock 
			where 
				qty > 0
			group by goods_no
		";
		DB::insert($sql);

		$sql	= "
			UPDATE goods g INNER JOIN _tmp_goods_xmd_stock_maxprice b ON g.goods_no = b.goods_no
			SET g.price = b.max_price
		";
		DB::update($sql);

		//stock update
		$sql	= "
			UPDATE goods_summary s INNER JOIN _tmp_goods_xmd_stock b ON s.goods_no = b.goods_no AND s.goods_opt = b.goods_opt
			SET good_qty = b.qty, wqty = b.qty
		";
		DB::update($sql);

		$sql	= "
			CREATE TABLE _tmp_goods_good
			select * from goods_good where opt_price is not null
		";
		DB::select($sql);
		
		$sql	= " truncate table goods_good ";
		DB::select($sql);

		//goods_good 데이터 생성
		$sql	= "
			insert into goods_good (goods_no,goods_sub,goods_opt,wonga,qty,invoice_no,init_qty,regi_date)
			select s.goods_no,s.goods_sub,s.goods_opt,g.wonga,s.good_qty,'',s.good_qty,now() from goods_summary s
				inner join goods g on g.goods_no = s.goods_no
		";
		DB::insert($sql);
		
		$sql	= "
			update goods_good a
				inner join _tmp_goods_good b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub and a.goods_opt = b.goods_opt
			set
				a.goods_opt = b.goods_opt
		";
		DB::update($sql);

		$sql	= " drop table _tmp_goods_good ";
		DB::select($sql);

		//재고 업로드 시 없는 상품 품절 처리
		$sql	= "
			UPDATE goods g INNER JOIN (
				SELECT 
					goods_no
				FROM goods g WHERE sale_stat_cl = '40' AND ( SELECT IFNULL(SUM(good_qty),0) FROM goods_summary WHERE goods_no = g.goods_no ) = 0
			) a ON g.goods_no = a.goods_no 
				SET sale_stat_cl = 30
			WHERE sale_stat_cl = '40'
		";
		DB::update($sql);

		//잠시 주석 처리
		// 품절수동일때 상품 상태 변경 안되게
		$sql	= "
			UPDATE goods g INNER JOIN (
				SELECT 
					goods_no
				FROM goods g WHERE sale_stat_cl = '30' AND ( SELECT SUM(good_qty) FROM goods_summary WHERE goods_no = g.goods_no ) > 0
			) a ON g.goods_no = a.goods_no 
				SET sale_stat_cl = 10
			WHERE sale_stat_cl = '30'
		";
		//DB::update($sql);

		//잠시 주석 처리
		// 품절수동일때 상품 상태 변경 안되게
		$sql	= "
			UPDATE goods g INNER JOIN (
				SELECT 
					goods_no
				FROM goods g WHERE sale_stat_cl = '20' AND ( SELECT SUM(good_qty) FROM goods_summary WHERE goods_no = g.goods_no ) > 0
			) a ON g.goods_no = a.goods_no 
				SET sale_stat_cl = 40
			WHERE sale_stat_cl = '20'
		";
		//DB::update($sql);
		

		$result_code	= "재고가 등록 되었습니다.";

//		DB::commit();

		
		return response()->json([
			"code"			=> $error_code,
			"result_code"	=> $result_code
		]);
	}

}
