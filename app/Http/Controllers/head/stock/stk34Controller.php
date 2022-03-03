<?php

namespace App\Http\Controllers\head\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class stk34Controller extends Controller
{
    //
    public function index() {

        $values = [
			's_goods_nm'	=> "",
            's_goods_nm_b'	=> "",
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT')
        ];
        return view( Config::get('shop.head.view') . '/stock/stk34',$values);
    }

    public function search(Request $request)
    {
        $s_goods_nm		= $request->input('s_goods_nm');
        $s_goods_nm_b	= $request->input('s_goods_nm_b');
        $s_sale_stat_cl	= $request->input('s_sale_stat_cl');

		$imp_idx	= "0";
		$where		= "";

		if( $s_goods_nm	!= "" )		$where .= " and aa.xmd_goods_nm like '" . Lib::quote($s_goods_nm) . "%' ";
		if( $s_goods_nm_b	!= "" )	$where .= " and aa.goods_nm like '" . Lib::quote($s_goods_nm_b) . "%' ";
		if( $s_sale_stat_cl != "" )	$where .= " and aa.sale_stat_cl = '$s_sale_stat_cl' ";

		$sql		= " select imp_idx from goods_xmd_stock order by imp_idx desc limit 1 ";
        $row		= DB::selectOne($sql);
        $imp_idx	= $row->imp_idx;
		
		$query	= "
			select
				aa.*
			from
			(
				select 
					'' as goods_no, a.goods_nm as xmd_goods_nm, '' as goods_nm, concat(b.color_code, b.size_code) as goods_opt, sum(a.qty) as qty, '' as good_qty, '' as sale_stat_cl, a.match_yn, '' as chk_cmt
				from goods_xmd_stock a 
				inner join xmd_stock_file b on a.cd = b.xmd_goods_code_full
				where 
					a.imp_idx = :imp_idx
					and a.match_yn = 'N'
				group by a.goods_nm, concat(b.color_code, b.size_code)

				union all

				select 
					b.goods_no, a.goods_nm as xmd_goods_nm, d.goods_nm as goods_nm, b.goods_opt, sum(a.qty) as qty, c.good_qty, 
					case
						when d.sale_stat_cl = '-10' then '판매중지'
						when d.sale_stat_cl = '-90' then '휴지통'
						when d.sale_stat_cl = '5' then '등록대기중'
						when d.sale_stat_cl = '10' then '판매대기중'
						when d.sale_stat_cl = '20' then '품절[수동]'
						when d.sale_stat_cl = '30' then '품절'
						when d.sale_stat_cl = '40' then '판매중'
						else ''
					end as sale_stat_cl,
					a.match_yn, '' as chk_cmt
				from goods_xmd_stock a 
				inner join goods_xmd b on a.cd = b.cd
				left outer join goods_summary c on c.goods_no = b.goods_no and c.goods_sub = '0' and c.goods_opt = b.goods_opt
				left outer join goods d on b.goods_no = d.goods_no and d.goods_sub = '0'
				where 
					a.imp_idx = :imp_idx2
				group by b.goods_no, b.goods_opt
			) aa
			where
				1 = 1
				$where
		";

        $result = DB::select($query,
			[
				'imp_idx'	=> $imp_idx,
				'imp_idx2'	=> $imp_idx
			]
		);

		foreach($result as $row) 
		{
			if( $row->match_yn == "N" )	$row->chk_cmt .= "[비매칭]";

			//예외 처리
			$row->xmd_goods_nm	= str_replace("(Regular)","(R)",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("(Long)","",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("(Short)","(S)",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("(A)","",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("Shirt M","Shirt",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("Windbreakder","Windbreaker",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("Keb Touring Trousers W","Keb Touring Padded Trousers W",$row->xmd_goods_nm);
			$row->xmd_goods_nm	= str_replace("Keb Touring Padded Trousers W","Keb Touring Trousers W",$row->xmd_goods_nm);

			if(substr($row->xmd_goods_nm, strlen($row->xmd_goods_nm) - 2, strlen($row->xmd_goods_nm)) == " M" )
				$row->xmd_goods_nm	= substr($row->xmd_goods_nm, 0, strlen($row->xmd_goods_nm) - 2);

			if(substr($row->xmd_goods_nm, strlen($row->xmd_goods_nm) - 5, strlen($row->xmd_goods_nm)) == " M(R)" )
				$row->xmd_goods_nm	= substr($row->xmd_goods_nm, 0, strlen($row->xmd_goods_nm) - 5);

			$row->goods_nm		= str_replace("4cm","4 cm",$row->goods_nm);
			$row->xmd_goods_nm	= str_replace("4cm","4 cm",$row->xmd_goods_nm);

			$row->goods_nm		= str_replace("3cm","3 cm",$row->goods_nm);
			$row->xmd_goods_nm	= str_replace("3cm","3 cm",$row->xmd_goods_nm);

			$row->goods_nm		= str_replace("(Short)","(S)",$row->goods_nm);
			$row->goods_nm		= str_replace(" (Regular)","(R)",$row->goods_nm);

			if( stristr($row->goods_nm,$row->xmd_goods_nm) == false )	
			{
				if(		$row->xmd_goods_nm == "Kiruna Lite Shirt LS W"				&& stristr($row->goods_nm,"(89840)") == true ){}
				elseif(	$row->xmd_goods_nm == "Kanken 15"							&& stristr($row->goods_nm,"(27172)") == true ){}
				elseif(	$row->xmd_goods_nm == "Kanken 17"							&& stristr($row->goods_nm,"(27173)") == true ){}
				elseif(	$row->xmd_goods_nm == "Singi Belt 2,5cm"					&& stristr($row->goods_nm,"(77280)") == true ){}
				elseif(	$row->xmd_goods_nm == "Bergtagen Longjohns M"				&& stristr($row->goods_nm,"(83991)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Touring Trousers"				&& stristr($row->goods_nm,"(81880)") == true ){}
				elseif(	$row->xmd_goods_nm == "Bergtagen Trousers W(S)"				&& stristr($row->goods_nm,"(89866S)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Lite Trousers M"					&& stristr($row->goods_nm,"(81870)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Lite Trousers M(R)"				&& stristr($row->goods_nm,"(81870R)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Wool Sweater M"					&& stristr($row->goods_nm,"(81876)") == true ){}
				elseif(	$row->xmd_goods_nm == "High Coast Stretch Trousers M(R)"	&& stristr($row->goods_nm,"(82281R)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Touring Trousers M"				&& stristr($row->goods_nm,"(82283)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Touring Trousers M(R)"			&& stristr($row->goods_nm,"(82283R)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Stretch Trousers M(R)"		&& stristr($row->goods_nm,"(82284R)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Pile Fleece M"				&& stristr($row->goods_nm,"(82993)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Re-Wool Shirt Jacket M"	&& stristr($row->goods_nm,"(82994)") == true ){}
				elseif(	$row->xmd_goods_nm == "Fjallslim Shirt LS M"				&& stristr($row->goods_nm,"(82995)") == true ){}
				elseif(	$row->xmd_goods_nm == "Expedition Down Lite Vest M"			&& stristr($row->goods_nm,"(84606)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Winter Jacket M"			&& stristr($row->goods_nm,"(87122)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Winter Parka M"			&& stristr($row->goods_nm,"(87124)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Down Liner Jacket M"		&& stristr($row->goods_nm,"(87126)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Lite Jacket M"					&& stristr($row->goods_nm,"(87200)") == true ){}
				elseif(	$row->xmd_goods_nm == "Keb Touring Jacket M"				&& stristr($row->goods_nm,"(87210)") == true ){}
				elseif(	$row->xmd_goods_nm == "Bergtagen Insulation Jacket M"		&& stristr($row->goods_nm,"(87300)") == true ){}
				elseif(	$row->xmd_goods_nm == "Varmland Woolterry Long Johns M"		&& stristr($row->goods_nm,"(90842)") == true ){}
				elseif(	$row->xmd_goods_nm == "Varmland Woolterry Half Zip M"		&& stristr($row->goods_nm,"(90838)") == true ){}
				elseif(	$row->xmd_goods_nm == "High Coast Merino Sweater M"			&& stristr($row->goods_nm,"(81862)") == true ){}
				elseif(	$row->xmd_goods_nm == "Greenland Re-Wool Sweater M"			&& stristr($row->goods_nm,"(81863)") == true ){}
				elseif(	$row->xmd_goods_nm == "Byron Pom Hat"						&& stristr($row->goods_nm,"(78002)") == true ){}
				else	
					$row->chk_cmt .= "[상품명 불일치]";
			}

			if( $row->qty != $row->good_qty )	
			{
				if( $row->good_qty == "" )	$row->chk_cmt .= "[재고요약 오류]";
				else						$row->chk_cmt .= "[수량 불일치]";
			}

			// 오류 정보 초기화 상품
			if( $row->goods_no == "106389" )	$row->chk_cmt = "";


		}

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
                "total"		=> count($result)
            ),
            "body" => $result
        ]);

	}

}
