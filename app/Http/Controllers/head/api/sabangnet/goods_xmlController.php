<?php

namespace App\Http\Controllers\head\api\sabangnet;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;

class goods_xmlController extends Controller
{
    public function index()
    {

        $values = [
        ];
        return view(Config::get('shop.head.view') . '/api/sabangnet/goods_xml', $values);
    }

    public function good_view(Request $request)
    {
        set_time_limit(0);

		// 설정 값 얻기
        $conf	= new Conf();

		$cfg_free_dlv_amt		= $conf->getConfigValue("delivery","free_delivery_amt");
		$cfg_dlv_fee			= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_api_sabangnet_id	= $conf->getConfigValue("api","sabangnet_id");
		$cfg_api_sabangnet_key	= $conf->getConfigValue("api","sabangnet_key");

		$cfg_domain	= $conf->getConfigValue("shop","domain");
		$host	= (strpos($cfg_domain, 'https://') !== false) ? $cfg_domain : sprintf("https://%s", $cfg_domain);

		$goods_no	= $request->input("goods_no");
		$goods_sub	= $request->input("goods_sub",0);
		$price		= $request->input("price",0);

		// 대용량 옵션 group_concat 리턴값 확장(기본값:1024)
		$sql	= " set session group_concat_max_len=10240; ";
		DB::select($sql);

		// 상품분류 속성값
		$row_class = array();
		$sql	= "
			select
				g.class,
				g.material,
				ifnull((
					select group_concat(c.code_val separator ',') as color
					from goods_color gc
					inner join code c on gc.color = c.code_id and c.code_kind_cd = 'G_PRODUCTS_COLOR'
					where gc.goods_no  = g.goods_no and gc.goods_sub = g.goods_sub
				),'') as color,
				g.size, g.make, g.org_nm,
				g.import_yn, g.caution, g.qa_basis, g.pack_date, g.kind,
				concat( ifnull(c.as_name, ''), ' ', ifnull(c.as_tel, '')) as as_info, g.import_yn
			from goods g
				left outer join company c on c.com_id = g.com_id
			where goods_no = :goods_no and goods_sub = '0'
		";
		$row_class	= DB::selectOne($sql,["goods_no" => $goods_no]);

		// 상품상세 패턴 설정값
		$row_pattern	= array();
		$sql	= "
			select `value`
			from shop_sabangnet_config
			where type = 'pattern'
				and use_yn = 'Y'
		";
		$results = DB::select($sql);
		foreach ($results as $row) 
		{
			$row_pattern[]	= $row->value;
		}

		// 연동구분
		$prop_edit_yn	= "Y";
		$img_sql		= "
			g.img as 'IMG_PATH',	 -- 대표이미지
			g.img as 'IMG_PATH1',	 -- 부가이미지1
			g.img as 'IMG_PATH2',	 -- 부가이미지2
			replace(g.img,'500','600') as 'IMG_PATH3',	 -- 부가이미지3
			g.img as 'IMG_PATH4',	 -- 부가이미지4
			g.img as 'IMG_PATH5',	 -- 부가이미지5
			-- replace(g.img,'500','600') as 'IMG_PATH6',	 -- 부가이미지6

			'' as 'IMG_PATH6',	 -- 부가이미지6
			'' as 'IMG_PATH7',	 -- 부가이미지7
			'' as 'IMG_PATH8',	 -- 부가이미지8
			'' as 'IMG_PATH9',	 -- 부가이미지9
			'' as 'IMG_PATH10',	 -- 부가이미지10

			replace(g.img,'500','270') as 'IMG_PATH22',	 -- 부가이미지22
		";

		$data		= "";

		// 상품 연동 정보
		$sql = "
			select
				g.goods_nm as 'GOODS_NM', -- 상품명
				'' as 'GOODS_KEYWORD', -- 상품약어
				g.style_no as 'MODEL_NM', -- 모델명
				g.style_no as 'MODEL_NO', -- 모델No
				b.brand_nm as 'BRAND_NM', -- 브랜드명
				g.goods_no as 'COMPAYNY_GOODS_CD', -- 자체코드
				'' as 'GOODS_SEARCH', -- 사이트검색어
				if(g.goods_type='S','3','1') as 'GOODS_GUBUN', -- 상품구분
				substr(g.rep_cat_cd,1,3) as 'CLASS_CD1',	-- 대분류코드
				substr(g.rep_cat_cd,4,3) as 'CLASS_CD2',	-- 중분류코드
				substr(g.rep_cat_cd,7,3) as 'CLASS_CD3',	-- 소분류코드
				substr(g.rep_cat_cd,10,3) as 'CLASS_CD4',	-- 세분류코드
				'' as 'PARTNER_ID', -- 매입처ID
				'' as 'DPARTNER_ID', -- 물류처ID
				g.make as 'MAKER', -- 제조사
				g.org_nm as 'ORIGIN', -- 원산지
				g.pack_date as 'MAKE_YEAR', -- 생산년도
				'' as 'MAKE_DM', -- 제조일자(서적)
				'NULL' as 'GOODS_SEASON', -- 시즌
				'NULL' as 'SEX', -- 남여구분
				case g.sale_stat_cl
					when 40 then '2'
					when 30 then '3'
					when 20 then '3'
					when -10 then '4'
					else '1'
				end as 'STATUS', -- 상품상태
				'1' as 'DELIV_ABLE_REGION',	 -- 판매지역
				if(g.tax_yn = 'Y',1,2) as 'TAX_YN',	 -- 세금구분
				if(g.bae_yn = 'Y',if( if(m.dlv_policy = 'C',m.free_dlv_amt_limit,'$cfg_free_dlv_amt') <= g.price, '1','3' ),'3') as 'DELV_TYPE',
				if(g.baesong_price > if(m.dlv_policy = 'C',m.dlv_amt,'$cfg_dlv_fee'), baesong_price,if(m.dlv_policy = 'C',m.dlv_amt,'$cfg_dlv_fee') ) as 'DELV_COST' ,
				'' as 'BANPUM_AREA',	 -- 반품지구분
				g.wonga as 'GOODS_COST',	 -- 원가
				if('$price' > '0', '$price', if(ifnull(s.price,0) > 0, s.price, g.price)) as 'GOODS_PRICE',	 -- 판매가
				g.goods_sh as 'GOODS_CONSUMER_PRICE',	 -- TAG(소비자가)
				(
					select
						if(g.is_option_use = 'Y', group_concat(name separator ','), '단품')
					from goods_option
					where goods_no = g.goods_no and goods_sub = g.goods_sub and type = 'basic' and use_yn = 'Y'
					order by seq
				) as 'CHAR_1_NM',	 -- 옵션구분1
				(
					select
						if( g.is_option_use = 'Y',
							group_concat(concat(SUBSTRING_INDEX(goods_opt, '^', 1),'^^', if(is_unlimited = 'Y', 900, ifnull(good_qty,0)),'^^',ifnull(opt_price,0)) separator ','),
							concat(goods_opt,'^^', if(is_unlimited = 'Y', 900, good_qty))
						)
					from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
					order by seq
				) as 'CHAR_1_VAL',	 -- 옵션1(옵션항목)
				'' as 'CHAR_2_NM',	 -- 옵션구분2
				(
					select
						if(instr(goods_opt,'^') > 0, group_concat(SUBSTRING_INDEX(goods_opt, '^', -1) separator ','), '')
					from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
					order by seq
				) as 'CHAR_2_VAL',	 -- 옵션2(옵션항목)

				$img_sql	-- 상품연동인 경우에만 활성화

				g.goods_cont as 'GOODS_REMARKS',	 -- 상품상세설명
				'N' as 'STOCK_USE_YN',	 -- 재고관리사용여부
				'2' as 'OPT_TYPE',	 -- 옵션수정가능여부
				'$prop_edit_yn' as 'PROP_EDIT_YN',	 -- 상품 속성정보 수정여부
				replace(g.class,'c','0') as 'PROP1_CD',	 -- 상품 속성분류코드
				gc.item_001 as 'PROP_VAL1',	 -- 속성값1
				gc.item_002 as 'PROP_VAL2',	 -- 속성값2
				gc.item_003 as 'PROP_VAL3',	 -- 속성값3
				gc.item_004 as 'PROP_VAL4',	 -- 속성값4
				gc.item_005 as 'PROP_VAL5',	 -- 속성값5
				gc.item_006 as 'PROP_VAL6',	 -- 속성값6
				gc.item_007 as 'PROP_VAL7',	 -- 속성값7
				gc.item_008 as 'PROP_VAL8',	 -- 속성값8
				gc.item_009 as 'PROP_VAL9',	 -- 속성값9
				gc.item_010 as 'PROP_VAL10', -- 속성값10
				gc.item_011 as 'PROP_VAL11', -- 속성값11
				gc.item_012 as 'PROP_VAL12', -- 속성값12
				gc.item_013 as 'PROP_VAL13', -- 속성값13
				gc.item_014 as 'PROP_VAL14', -- 속성값14
				gc.item_015 as 'PROP_VAL15', -- 속성값15
				gc.item_016 as 'PROP_VAL16', -- 속성값16
				gc.item_017 as 'PROP_VAL17', -- 속성값17
				gc.item_018 as 'PROP_VAL18', -- 속성값18
				gc.item_019 as 'PROP_VAL19', -- 속성값19
				gc.item_020 as 'PROP_VAL20'	 -- 속성값20
			from goods g
				inner join company m on g.com_id = m.com_id
				left outer join brand b on g.brand = b.brand
				left outer join shop_sabangnet s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
				left outer join goods_class gc on g.goods_no = gc.goods_no and g.goods_sub = gc.goods_sub
			where g.goods_no = :goods_no and g.goods_sub = '0'
		";
		$results = DB::select($sql, ['goods_no' => $goods_no]);

		$img_patterns = array (
									"/src=\"(\/.+)\"/i",
									"/src='(\/.+)'/i",
									"/\\nsrc=/i"
								);
		$img_replace = array (
									"src=\"$host\\1\"",
									"src='$host\\1'",
									"\n src=",
								);


        //header("Content-Type: text/plain; charset=EUC-KR");
        $data	.= "<?xml version=\"1.0\" encoding=\"EUC-KR\"?>\n";
		$data	.= "<SABANG_GOODS_REGI>\n";
		$data	.= "<HEADER>\n";
		$data	.= "<SEND_COMPAYNY_ID>$cfg_api_sabangnet_id</SEND_COMPAYNY_ID>\n";
		$data	.= "<SEND_AUTH_KEY><![CDATA[$cfg_api_sabangnet_key]]></SEND_AUTH_KEY>\n";
		$data	.= "<SEND_DATE><![CDATA[" . date("Ymd") . "]]></SEND_DATE>\n";
		$data	.= "<SEND_GOODS_CD_RT>Y</SEND_GOODS_CD_RT>\n";
		$data	.= "</HEADER>\n";

		foreach($results as $row) {

			//$row->GOODS_NM	= sprintf("<![CDATA[%s]]>",Lib::RHtml($row->GOODS_NM));
			//$row->COMPAYNY_GOODS_CD	= sprintf("<![CDATA[%s]]>",$row->COMPAYNY_GOODS_CD);
			$row->GOODS_REMARKS	=  preg_replace($img_patterns, $img_replace, $row->GOODS_REMARKS);
			
			for( $i = 0; $i < count($row_pattern); $i++ )
			{
				$row->GOODS_REMARKS =  preg_replace($row_pattern[$i],"",$row->GOODS_REMARKS);
			}

			if( trim($row->MAKER ) == "" )	$row->MAKER = $row->BRAND_NM;
			
			$sql_img	= " select type, img from goods_image where goods_no = :goods_no ";
			$img_rows	= DB::select($sql_img, ['goods_no' => $goods_no]);

			//부가 이미지 작업
			foreach($img_rows as $img_row){
				if($img_row->type == 'b')	$row->IMG_PATH6 = $img_row->img;
				if($img_row->type == 'c')	$row->IMG_PATH7 = $img_row->img;
				if($img_row->type == 'd')	$row->IMG_PATH8 = $img_row->img;
				if($img_row->type == 'e')	$row->IMG_PATH9 = $img_row->img;
				if($img_row->type == 'f')	$row->IMG_PATH10 = $img_row->img;
			}

			$row->IMG_PATH	= sprintf("%s%s",$host,$row->IMG_PATH);
			$row->IMG_PATH1	= sprintf("%s%s",$host,$row->IMG_PATH1);
			$row->IMG_PATH2	= sprintf("%s%s",$host,$row->IMG_PATH2);
			$row->IMG_PATH3	= sprintf("%s%s",$host,$row->IMG_PATH3);
			$row->IMG_PATH4	= sprintf("%s%s",$host,$row->IMG_PATH4);
			$row->IMG_PATH5	= sprintf("%s%s",$host,$row->IMG_PATH5);

			if($row->IMG_PATH6 != '')	$row->IMG_PATH6 = sprintf("%s%s",$host,$row->IMG_PATH6);
			if($row->IMG_PATH7 != '')	$row->IMG_PATH7 = sprintf("%s%s",$host,$row->IMG_PATH7);
			if($row->IMG_PATH8 != '')	$row->IMG_PATH8 = sprintf("%s%s",$host,$row->IMG_PATH8);
			if($row->IMG_PATH9 != '')	$row->IMG_PATH9 = sprintf("%s%s",$host,$row->IMG_PATH9);
			if($row->IMG_PATH10 != '')	$row->IMG_PATH10 = sprintf("%s%s",$host,$row->IMG_PATH10);

			$row->IMG_PATH22	= sprintf("%s%s",$host,$row->IMG_PATH22);
		}

		if( strpos($row->CHAR_1_NM,',') !== false )
		{
			$a_opts	= explode(",",$row->CHAR_1_NM);
			$row->CHAR_1_NM	= $a_opts[0];
			$row->CHAR_2_NM	= $a_opts[1];
		}

		// APPLY CDATA
		$row->GOODS_NM		= sprintf("<![CDATA[%s]]>",Lib::RHtml($row->GOODS_NM));
		$row->GOODS_KEYWORD	= sprintf("<![CDATA[%s]]>",$row->GOODS_KEYWORD);
		$row->MODEL_NM		= sprintf("<![CDATA[%s]]>",$row->MODEL_NM);
		$row->MODEL_NO		= sprintf("<![CDATA[%s]]>",$row->MODEL_NO);
		$row->BRAND_NM		= sprintf("<![CDATA[%s]]>",$row->BRAND_NM);
		$row->COMPAYNY_GOODS_CD	= sprintf("<![CDATA[%s]]>",$row->COMPAYNY_GOODS_CD);
		$row->GOODS_SEARCH	= sprintf("<![CDATA[%s]]>",$row->GOODS_SEARCH);
		$row->CLASS_CD1		= sprintf("<![CDATA[%s]]>",$row->CLASS_CD1);
		$row->CLASS_CD2		= sprintf("<![CDATA[%s]]>",$row->CLASS_CD2);
		$row->CLASS_CD3		= sprintf("<![CDATA[%s]]>",$row->CLASS_CD3);
		$row->CLASS_CD4		= sprintf("<![CDATA[%s]]>",$row->CLASS_CD4);
		$row->PARTNER_ID	= sprintf("<![CDATA[%s]]>",$row->PARTNER_ID);
		$row->DPARTNER_ID	= sprintf("<![CDATA[%s]]>",$row->DPARTNER_ID);
		$row->MAKER			= sprintf("<![CDATA[%s]]>",$row->MAKER);
		$row->ORIGIN		= sprintf("<![CDATA[%s]]>",$row->ORIGIN);
		$row->MAKE_YEAR		= sprintf("<![CDATA[%s]]>",$row->MAKE_YEAR);
		$row->MAKE_DM		= sprintf("<![CDATA[%s]]>",$row->MAKE_DM);
//		$row->CHAR_1_NM		= sprintf("<![CDATA[%s]]>",$row->CHAR_1_NM);
//		$row->CHAR_1_VAL	= sprintf("<![CDATA[%s]]>",$row->CHAR_1_VAL);
//		$row->CHAR_2_NM		= sprintf("<![CDATA[%s]]>",$row->CHAR_2_NM);
//		$row->CHAR_2_VAL	= sprintf("<![CDATA[%s]]>",$row->CHAR_2_VAL);
		$row->CHAR_1_NM		= sprintf("<![CDATA[%s]]>",'');
		$row->CHAR_1_VAL	= sprintf("<![CDATA[%s]]>",'');
		$row->CHAR_2_NM		= sprintf("<![CDATA[%s]]>",'');
		$row->CHAR_2_VAL	= sprintf("<![CDATA[%s]]>",'');

		$row->IMG_PATH		= sprintf("<![CDATA[%s]]>",$row->IMG_PATH);
		$row->IMG_PATH1		= sprintf("<![CDATA[%s]]>",$row->IMG_PATH1);
		$row->IMG_PATH2		= sprintf("<![CDATA[%s]]>",$row->IMG_PATH2);
		$row->IMG_PATH3		= sprintf("<![CDATA[%s]]>",$row->IMG_PATH3);
		$row->IMG_PATH4		= sprintf("<![CDATA[%s]]>",$row->IMG_PATH4);
		$row->IMG_PATH5		= sprintf("<![CDATA[%s]]>",$row->IMG_PATH5);

		if($row->IMG_PATH6 != '')	$row->IMG_PATH6 = sprintf("<![CDATA[%s]]>",$row->IMG_PATH6);
		if($row->IMG_PATH7 != '')	$row->IMG_PATH7 = sprintf("<![CDATA[%s]]>",$row->IMG_PATH7);
		if($row->IMG_PATH8 != '')	$row->IMG_PATH8 = sprintf("<![CDATA[%s]]>",$row->IMG_PATH8);
		if($row->IMG_PATH9 != '')	$row->IMG_PATH9 = sprintf("<![CDATA[%s]]>",$row->IMG_PATH9);
		if($row->IMG_PATH10 != '')	$row->IMG_PATH10 = sprintf("<![CDATA[%s]]>",$row->IMG_PATH10);

		$row->IMG_PATH22	= sprintf("<![CDATA[%s]]>",$row->IMG_PATH22);

		$row->GOODS_REMARKS	= sprintf("<![CDATA[%s]]>",$row->GOODS_REMARKS);
		$row->STOCK_USE_YN	= sprintf("<![CDATA[%s]]>",$row->STOCK_USE_YN);
		$row->OPT_TYPE		= sprintf("<![CDATA[%s]]>",$row->OPT_TYPE);
		$row->PROP_VAL1		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL1);
		$row->PROP_VAL2		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL2);
		$row->PROP_VAL3		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL3);
		$row->PROP_VAL4		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL4);
		$row->PROP_VAL5		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL5);
		$row->PROP_VAL6		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL6);
		$row->PROP_VAL7		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL7);
		$row->PROP_VAL8		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL8);
		$row->PROP_VAL9		= sprintf("<![CDATA[%s]]>",$row->PROP_VAL9);
		$row->PROP_VAL10	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL10);
		$row->PROP_VAL11	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL11);
		$row->PROP_VAL12	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL12);
		$row->PROP_VAL13	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL13);
		$row->PROP_VAL14	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL14);
		$row->PROP_VAL15	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL15);
		$row->PROP_VAL16	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL16);
		$row->PROP_VAL17	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL17);
		$row->PROP_VAL18	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL18);
		$row->PROP_VAL19	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL19);
		$row->PROP_VAL20	= sprintf("<![CDATA[%s]]>",$row->PROP_VAL20);


		$data	.= $this->println("DATA",$row);

		$data	.= "</SABANG_GOODS_REGI>\n";

		return Response($data)->header('Content-type','text/plan;charset=euc-kr');
	}

    public function summary_view(Request $request)
    {
        set_time_limit(0);

		// 설정 값 얻기
        $conf	= new Conf();
		$cfg_api_sabangnet_id	= $conf->getConfigValue("api","sabangnet_id");
		$cfg_api_sabangnet_key	= $conf->getConfigValue("api","sabangnet_key");

		$goods_no	= $request->input("goods_no");
		$goods_sub	= $request->input("goods_sub",0);
		$price		= $request->input("price",0);
		$ok_qty		= $request->input("ok_qty",0);

		$data		= "";
		$where		= "";

		// XML Header
        //header("Content-Type: text/plain; charset=euc-kr");
        $data	.= "<?xml version=\"1.0\" encoding=\"EUC-KR\"?>\n";
		$data	.= "<SABANG_GOODS_REGI>\n";
		$data	.= "<HEADER>\n";
		$data	.= "<SEND_COMPAYNY_ID>$cfg_api_sabangnet_id</SEND_COMPAYNY_ID>\n";
		$data	.= "<SEND_AUTH_KEY><![CDATA[$cfg_api_sabangnet_key]]></SEND_AUTH_KEY>\n";
		$data	.= "<SEND_DATE><![CDATA[" . date("Ymd") . "]]></SEND_DATE>\n";
		$data	.= "<SEND_GOODS_CD_RT>Y</SEND_GOODS_CD_RT>\n";
		$data	.= "</HEADER>\n";

		$sql	= "
			select
				g.goods_nm as 'GOODS_NM',
				g.goods_no as 'COMPAYNY_GOODS_CD',
				case g.sale_stat_cl
					when 40 then '2'
					when 30 then '3'
					when 20 then '3'
					when -10 then '4'
					else '1'
				end as 'STATUS',
				g.wonga as 'GOODS_COST',
				-- g.price as 'GOODS_PRICE',
				if('$price' <> g.price, '$price', g.price) as 'GOODS_PRICE',	 -- 판매가
				g.goods_sh as 'GOODS_CONSUMER_PRICE',
				'' as 'SKU_INFO'
			from shop_sabangnet s
				left outer join goods g on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
				left outer join brand b on g.brand = b.brand
				inner join company m on g.com_id = m.com_id
			where g.goods_no = :goods_no and g.goods_sub = 0
		";

        $rows = DB::select($sql,['goods_no' => $goods_no]);
		
		
		foreach($rows as $row) {

			$row->GOODS_NM	= sprintf("<![CDATA[%s]]>",Lib::RHtml($row->GOODS_NM));
			$row->COMPAYNY_GOODS_CD	= sprintf("<![CDATA[%s]]>",$row->COMPAYNY_GOODS_CD);

		}
		
		if( $ok_qty > 0 ){
			$sql_sku = "
				select concat(cast(replace(goods_opt, '^',':') as char),'^^', if(good_qty >= $ok_qty, good_qty, 0),'^^',ifnull(opt_price,0)) as 'SKU_VALUE'
				from goods_summary where goods_no = :goods_no and goods_sub = 0 and use_yn = 'Y'
				order by seq
			";
		}else{
			$sql_sku = "
				select concat(cast(replace(goods_opt, '^',':') as char),'^^',good_qty,'^^',ifnull(opt_price,0)) as 'SKU_VALUE'
				from goods_summary where goods_no = :goods_no and goods_sub = 0 and use_yn = 'Y'
				order by seq
			";
		}

		// 상품 재고 정보
		$rs_sku = DB::select($sql_sku,['goods_no' => $goods_no]);
		$row->SKU_INFO	= $rs_sku;

		$data	.= $this->println("DATA",$row);

		$data	.="</SABANG_GOODS_REGI>\n";

		return Response($data)->header('Content-type','text/plan;charset=euc-kr');
	}

	/**
	 * Function : println
	 *	XML출력
	 */
	function println($type,$data)
	{
		$buffer = "";
		foreach($data as $key => $value)
		{
			if( is_array($value) )
			{
				$buffer	.= sprintf("<%s>\n",$key);

				foreach( $value as $k => $v)
				{
					foreach( $v as $kk => $vv)
					{
						$vv	= iconv("UTF-8","CP949",$vv);
						//$buffer .= $v->SKU_VALUE;
						$buffer .= sprintf("\t<%s>%s</%s>\n",$kk,$vv,$kk);
					}
				}

				$buffer	.= sprintf("</%s>\n",$key);
			}
			else
			{
                $value	= iconv("UTF-8","CP949",$value);
				$buffer	.= sprintf("<%s>%s</%s>\n",$key,$value,$key);
			}
		}
		$buffer	= sprintf("<%s>\n%s</%s>\n",$type,$buffer,$type);

		return $buffer;
	}

}
