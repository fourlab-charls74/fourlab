<?php

namespace App\Http\Controllers\head\product;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Conf;


class prd30Controller extends Controller
{
    //
    public function index(Request $request) 
	{

		$s_goods_stat	= $request->input("s_goods_stat","40");

		// 분류
		$sql	= "select class as code, class_nm as val from code_class group by class";
		$s_class	= DB::select($sql);

        $values = [
            'com_types'     => SLib::getCodes('G_COM_TYPE'),
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			's_goods_stat'	=> $s_goods_stat,
			's_class'		=> $s_class,
            'items'         => SLib::getItems(),
            'goods_types'   => SLib::getCodes('G_GOODS_TYPE'),
			's_api_yn'		=> $request->input('s_api_yn','N'),
            'sale_places'   => SLib::getSalePlaces(),
        ];
        return view( Config::get('shop.head.view') . '/product/prd30',$values);
    }

    public function search(Request $request)
    {
		$shop_status = array(
			"1" => "대기중",
			"2" => "공급중",
			"3" => "일시중지",
			"4" => "완전품절",
			"5" => "미사용",
			"6" => "삭제"
		);

		$page	= $request->input('page', 1);
        if( $page < 1 or $page == "" ) $page = 1;
        $limit	= $request->input('limit', 100);

		$s_goods_no		= $request->input("goods_no");
		$s_style_no		= $request->input("style_no");
		$s_goods_stat	= $request->input("s_goods_stat");
		$s_goods_nm		= $request->input("goods_nm");
		$s_brand_cd		= $request->input("brand_cd");
		$s_com_type		= $request->input("com_type");
		$s_com_id		= $request->input("s_com_id");
		$s_class		= $request->input("s_class");
		$s_opt_kind_cd	= $request->input("s_opt_kind_cd");
		$s_goods_type	= $request->input("s_goods_type");
		$s_api_yn		= $request->input("s_api_yn");
		$s_site			= $request->input("s_site");
		$s_cat_type		= $request->input("cat_type");
		$s_cat_cd		= $request->input("cat_cd");
		$s_product		= $request->input("s_product");
		$s_stock		= $request->input("s_stock");
		$s_desc			= $request->input("s_desc");

        $ord		= $request->input('ord','desc');
        $ord_field	= $request->input('ord_field','g.goods_no');

        $orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";
		$join	= "";

		$s_goods_no	= preg_replace("/\s/",",",$s_goods_no);
		$s_goods_no	= preg_replace("/\t/",",",$s_goods_no);
		$s_goods_no	= preg_replace("/\n/",",",$s_goods_no);
		$s_goods_no	= preg_replace("/,,/",",",$s_goods_no);

        if( $s_goods_no != "" )
		{
            $goods_nos	= explode(",",$s_goods_no);

            if( count($goods_nos) > 1 )
			{
                if( count($goods_nos) > 500 )	array_splice($goods_nos,500);
                $in_goods_nos	= join(",",$goods_nos);
                $where	.= " and g.goods_no in ( $in_goods_nos ) ";
            } 
			else 
			{
				if ($s_goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($s_goods_no) . "' ";
            }
        }

		
		//if( $s_goods_no != "" )		$where .= " and g.goods_no = '$s_goods_no' ";
		if( $s_style_no != "" )		$where .= " and style_no like '" . Lib::quote($s_style_no) . "%' ";
		if( $s_goods_stat != "" )	$where .= " and sale_stat_cl = '$s_goods_stat' ";
		if( $s_goods_nm != "" )		$where .= " and g.goods_nm like '%" . Lib::quote($s_goods_nm) . "%' ";
		if( $s_brand_cd != "" )		$where .= " and g.brand ='$s_brand_cd'";
		if( $s_com_type != "" )		$where .= " and com_type = '$s_com_type' ";
		if( $s_com_id != "" )		$where .= " and com_id = '$s_com_id' ";
		if( $s_class != "" )		$where .= " and g.class = '$s_class' ";
		if( $s_opt_kind_cd != "" )	$where .= " and opt_kind_cd = '$s_opt_kind_cd' ";
		if( $s_goods_type != "" )	$where .= " and goods_type = '$s_goods_type' ";
		if( $s_api_yn == "Y" )		$where .= " and l.shop_goods_no <> '' ";
		else if( $s_api_yn == "N" )	$where .= " and ( l.shop_goods_no is null or l.shop_goods_no = '') ";
		
		if( $s_site != "" )			$join .= " inner join goods_site s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub and s.site = '$s_site' ";

		if( $s_cat_cd != "" )		$where .= " and ( select count(*) from category_goods where cat_type = '$s_cat_type' and d_cat_cd = '$s_cat_cd' and goods_no = g.goods_no and goods_sub = g.goods_sub ) > 0 ";

		if( $s_stock == "NE" ) 
		{
			$where .= "
				and  (if(g.is_unlimited = 'Y',900,
				ifnull((
					select sum(good_qty) from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
				), 0)) <> l.qty
				or if(g.is_option_use = 'N','', concat((
					select
						group_concat(cast(name as char) separator ',')
					from goods_option
					where goods_no = g.goods_no and goods_sub = g.goods_sub and type = 'basic' and use_yn = 'Y'
					order by seq
				),'||',(
					select
						group_concat(concat(cast(goods_opt as char),'^^',good_qty,'^^',opt_price) separator ',')
					from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub  and use_yn = 'Y'
					order by seq
				))) <> l.option_qty )
			";
		}

		if( $s_product == "NE" )	$where .= " and g.upd_dm > l.ut ";
		if( $s_desc != "" )			$where .= " and (ad_desc like '" . Lib::quote($s_desc) . "%' or head_desc like '" . Lib::quote($s_desc) . "%') ";


        $page_size	= $limit;
        $startno	= ($page - 1) * $page_size;
        $limit	= " limit $startno, $page_size ";

        $total	= 0;
        $page_cnt	= 0;

        if( $page == 1 )
		{
            $query = /** @lang text */
			"
				select count(*) as cnt
				from goods g $join
						left outer join shop_sabangnet l on g.goods_no = l.goods_no and g.goods_sub = l.goods_sub
				where 1=1 $where
			";
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query);
            $total = $row[0]->cnt;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		$sql	= "
			select
				'on;off' as blank,
				g.goods_no, g.goods_sub, style_no,
				ifnull( cd3.code_val, 'N/A') as goods_type,'' as goods_img,
				head_desc, g.goods_nm,cd2.code_val as sale_stat_cl,
				if(g.is_unlimited = 'Y',900,
					ifnull((
						select sum(good_qty) from goods_summary
						where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
				), 0)) as qty,
				ifnull(g.price, 0) as price,
				ifnull(if( g.goods_type = 'S', (
					select round(avg(wonga)) as wonga
					from goods_good
					where goods_no = g.goods_no and goods_sub = g.goods_sub and qty > 0
       				), g.wonga
				),0) as wonga,
				'' as margin_rate,
				(select class_nm from code_class where class = g.class group by class) as class,
				date_format(upd_dm, '%y-%m-%d %H:%i:%s') as upd_dm,
				ifnull(l.shop_goods_no,'') as shop_goods_no,
				ifnull(l.status,'') as shop_status,
				ifnull(l.qty,'') as shop_qty,
				ifnull(l.price,'') as shop_price,
				ifnull(date_format(l.ut, '%y-%m-%d %H:%i:%s'),'') as shop_ut,
				ifnull(date_format(l.stock_ut, '%y-%m-%d %H:%i:%s'),'') as shop_stock_ut,
				ifnull(l.result_no,'') as shop_result_no,
				ifnull(l.result_msg,'') as shop_result_msg
			from goods g $join
				left outer join code cd on cd.code_kind_cd = 'G_SPECIAL_YN' and g.special_yn = cd.code_id
				left outer join code cd2 on cd2.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = cd2.code_id
				left outer join code cd3 on cd3.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = cd3.code_id
				left outer join shop_sabangnet l on g.goods_no = l.goods_no and g.goods_sub = l.goods_sub
			where 1=1 $where
            $orderby
			$limit
		";

        $result = DB::select($sql);
        
		foreach($result as $row) 
		{
			$price	= $row->price;
			if( $price == 0 )	$price = 1;	// 판매가 0 이면 1 로 강제 변경 (Cannot Division 방지)
			$wonga	= $row->wonga;
			if( $wonga == "" )	$wonga = 0;
			$margin_rate	= round((1 - $wonga / $price)*100, 2);
			$row->margin_rate	= $margin_rate;

			if( isset($shop_status[$row->shop_status]) )
			{
				$row->shop_status	= $shop_status[$row->shop_status];
			}

		}

        return response()->json([
            "code"	=> 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);

	}

	public function addshop_goods(Request $request)
	{
		// 설정 값 얻기
        $conf	= new Conf();
		$cfg_domain_bizest	= $conf->getConfigValue("shop","domain_bizest");
		//$cfg_domain_bizest	= "127.0.0.1:8000/head";
		$cfg_domain_bizest	.= "/head";

		$result_no	= "";
		$result_msg	= "";

		$goods_no	= $request->input("goods_no");
		$goods_sub	= $request->input("goods_sub");
		$shop_goods_no	= $request->input("shop_goods_no");
		$shop_price	= $request->input("shop_price");

		$sql	= "
			select
				g.goods_nm as 'product_name',
				case g.sale_stat_cl
					when 40 then '2'
					when 30 then '3'
					when 20 then '3'
					when -10 then '4'
					else '1'
				end as 'sale_status',
				g.price as sale_price,
				if(g.is_unlimited = 'Y',900,
					ifnull((
						select sum(good_qty) from goods_summary
						where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
				), 0)) as quantity,
				if(g.is_option_use = 'N','000','002')  as option_kind,
				if(g.is_option_use = 'N','', concat((
					select
						group_concat(cast(name as char) separator ',')
					from goods_option
					where goods_no = g.goods_no and goods_sub = g.goods_sub and type = 'basic' and use_yn = 'Y'
					order by seq
				),'||',(
					select
						group_concat(concat(cast(goods_opt as char),'^^',good_qty,'^^',opt_price) separator ',')
					from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub  and use_yn = 'Y'
					order by seq
				))) as opt_info,
				g.goods_no,g.goods_sub,
				ifnull(s.goods_no,-1) as s_goods_no,
				g.class
			from goods g
				inner join company m on g.com_id = m.com_id
				left outer join shop_sabangnet s on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
			where g.goods_no = '$goods_no' and g.goods_sub = '0' and g.sale_stat_cl = 40
		";
		$row = DB::selectOne($sql);

		if(empty($row->goods_no)) {
			$result_no	= "-2";
			$result_msg	= "상품을 찾을 수 없습니다.";
		}
		else
		{
			if( $shop_price > 0 )	$price	= $shop_price;
			else					$price	= $row->sale_price;

			$sale_stat_cl	= $row->sale_status;
			$qty		= $row->quantity;
			$option_qty	= $row->opt_info;
			$class		= $row->class;

			if( $row->s_goods_no == '-1' )
			{
				$sql_sub	= "
					insert into shop_sabangnet (
						goods_no, goods_sub, price, status, qty, option_qty, result_no, result_msg, admin_id, rt, ut
					) values (
						'$goods_no',0, '$price', '$sale_stat_cl', '$qty','$option_qty',0,'','api', now(), now()
					)
				";
				DB::insert($sql_sub);
			}

			//$url = sprintf("http://%s/api/sabangnet/goods_xml.php?c=goods_view&goods_no=%s&price=%s",$cfg_domain_bizest,$goods_no,$price);
			$url = sprintf("http://%s/api/sabangnet/goods_xml/good_view/?goods_no=%s&price=%s",$cfg_domain_bizest,$goods_no,$price);
			$url = sprintf("http://r.sabangnet.co.kr/RTL_API/xml_goods_info.html?xml_url=%s",urlencode($url));

			$response = Http::get($url);

			$result_no	= $response->status();
			

			if( $result_no == 200 )
			{
				//$result_body	= $this->toIconv($response->body());
				$result_body	= iconv("CP949","UTF-8",$response->body());

				try {
					$sql = "
						select 1;
					";
					DB::select($sql);
				} catch (Exception $exceptionOnReConnect) {
					DB::reconnect('mysql');
				}

				if(preg_match("/수정 성공 : (\w+)/i",$result_body,$match))
				{
					$shop_goods_no	= $match[1];
					$result_msg		= $match[0];

					$sql = "
						update shop_sabangnet set
							status		= '$sale_stat_cl',
							price		= '$shop_price',
							qty			= '$qty',
							option_qty	= '$option_qty',
							shop_goods_no	= '$shop_goods_no',
							result_no	= '200',
							result_msg	= '$result_msg',
							ut	= now()
						where goods_no	= '$goods_no' and goods_sub = '0'
					";
					DB::update($sql);

					$result_no	= "200";
					$result_msg = "상품수정 성공 : " . $shop_goods_no;
				} 
				else if(preg_match("/성공 : (\w+)/i",$result_body,$match))
				{
					$shop_goods_no	= $match[1];
					$result_msg		= $match[0];

					$sql	= "
						update shop_sabangnet set
							price = '$price',
							status = '$sale_stat_cl',
							qty = '$qty',
							option_qty = '$option_qty',
							shop_goods_no = '$shop_goods_no',
							result_no = '200',
							result_msg = '$result_msg',
							stock_ut = null,
							ut = now()
						where goods_no = '$goods_no' and goods_sub = '0'
					";
					DB::update($sql);

					$result_no	= "200";
					$result_msg = "상품등록 성공 : " . $shop_goods_no;
				} 
				else 
				{
					$err_msg	= $result_body;

					if( preg_match("/\-(.+)\//i",$err_msg,$match) )
					{
						$err_msg = $match[1];
					}
					$result_msg = sprintf("API  Error - %s",$err_msg);

					$sql	= "
						update shop_sabangnet set
							price = '$price',
							status = '$sale_stat_cl',
							qty = '$qty',
							option_qty = '$option_qty',
							shop_goods_no = '$shop_goods_no',
							result_no = '0',
							result_msg = '$result_msg',
							stock_ut = null,
							ut = now()
						where goods_no = '$goods_no' and goods_sub = '0'
					";
					DB::update($sql);

					$result_no	= "0";
				}

			} 
			else 
			{
				$result_no	= "-1";
				$result_msg = "Http Error";
			}

		}

		return response()->json([
			"result_no"		=> $result_no,
			"result_msg"	=> $result_msg
		]);
	}

	public function stockshop(Request $request)
	{
		// 설정 값 얻기
        $conf	= new Conf();
		$cfg_domain_bizest	= $conf->getConfigValue("shop","domain_bizest");
		//$cfg_domain_bizest	= "127.0.0.1:8000/head";
		$cfg_domain_bizest	.= "/head";

		$result_no	= "";
		$result_msg	= "";

		$goods_no	= $request->input("goods_no");
		$goods_sub	= $request->input("goods_sub");
		$shop_goods_no	= $request->input("shop_goods_no");
		$shop_price	= $request->input("shop_price");

		$sql	= "
			select
				g.goods_nm as 'product_name',
				case g.sale_stat_cl
					when 40 then '2'
					when 30 then '3'
					when 20 then '3'
					when -10 then '4'
					else '1'
				end as 'sale_status',
				g.price as sale_price,
				if(g.is_unlimited = 'Y',900,
					ifnull((
						select sum(good_qty) from goods_summary
						where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
				), 0)) as quantity,
				g.style_no as model,
				if(g.is_option_use = 'N','000','002')  as option_kind,
				if(g.is_option_use = 'N','', concat((
					select
						group_concat(cast(name as char) separator ',')
					from goods_option
					where goods_no = g.goods_no and goods_sub = g.goods_sub and type = 'basic' and use_yn = 'Y'
					order by seq
				),'||',(
					select
						group_concat(concat(cast(goods_opt as char),'^^',good_qty,'^^',opt_price) separator ',')
					from goods_summary
					where goods_no = g.goods_no and goods_sub = g.goods_sub and use_yn = 'Y'
					order by seq
				))) as opt_info,
				g.goods_no,g.goods_sub,
				g.sale_stat_cl,
				s.status,
				s.qty,
				s.option_qty,
				ifnull(s.stock_ut,0) as stock_ut, s.ut
			from shop_sabangnet s
				left outer join goods g on g.goods_no = s.goods_no and g.goods_sub = s.goods_sub
			where s.goods_no = '$goods_no' and s.goods_sub = '$goods_sub' and s.shop_goods_no = '$shop_goods_no'
		";
		$row = DB::selectOne($sql);

		if(empty($row->goods_no)) {
			$result_no	= "-2";
			$result_msg	= "상품을 찾을 수 없습니다.";
		}
		else
		{
			$sale_stat_cl	= $row->sale_status;
			$qty		= $row->quantity;
			$option_qty	= $row->opt_info;

			//$url = sprintf("http://%s/api/sabangnet/goods_xml.php?c=stock_view&goods_no=%s&price=%s",$cfg_domain_bizest,$goods_no,$shop_price);
			$url = sprintf("http://%s/api/sabangnet/goods_xml/summary_view/?goods_no=%s&price=%s",$cfg_domain_bizest,$goods_no,$shop_price);
			$url = sprintf("http://r.sabangnet.co.kr/RTL_API/xml_goods_info2.html?xml_url=%s",urlencode($url));

			$response = Http::get($url);

			$result_no	= $response->status();
			

			if( $result_no == 200 )
			{
				//$result_body	= $this->toIconv($response->body());
				$result_body	= iconv("CP949","UTF-8",$response->body());

				if(preg_match("/수정 성공 : (\w+)/i",$result_body,$match))
				{
					$shop_goods_no	= $match[1];
					$result_msg		= $match[0];

					try {
						$sql = "
							select 1;
						";
						DB::select($sql);
					} catch (Exception $exceptionOnReConnect) {
						DB::reconnect('mysql');
					}

					$sql = "
						update shop_sabangnet set
							status		= '$sale_stat_cl',
							price		= '$shop_price',
							qty			= '$qty',
							option_qty	= '$option_qty',
							shop_goods_no	= '$shop_goods_no',
							result_no	= '200',
							result_msg	= '$result_msg',
							stock_msg	= '$result_msg',
							stock_ut	= now()
						where goods_no	= '$goods_no' and goods_sub = '$goods_sub'
					";
					DB::update($sql);

					$result_no	= "200";
					$result_msg = "상품수정 성공 : " . $shop_goods_no;
				} 
				else 
				{
					$result_no	= "0";
					$result_msg = sprintf("API  Error - %s",$response->body());
				}

			} 
			else 
			{
				$result_no	= "-1";
				$result_msg = "Http Error";
			}

		}

		return response()->json([
			"result_no"		=> $result_no,
			"result_msg"	=> $result_msg
		]);
	}

	/*
	function toIconv($s){
		if($this->isUTF($s)){
			return $s;
		} else {
			return @iconv("CP949","UTF-8",$s);
		}
	}
	*/

	/*

		Function: isUTF
			문자열의 UTF8 Charset 확인

			CP949 contains four character sets
			+ *  <0x00-0x7f>			ASCII equivalent
			+ *  <0xa1-0xfd><0xa1-0xfe>    EUC instance of KS X 1001:2002
			+ *  <0x81-0xa0><0x41-0xfe>    Hangul syllables GAGG-JWAK
			+ *  <0xa1-0xc6><0x41-0xa0>    Hangul syllables JWAT-HIH (ends with C652)

			http://ko.wikipedia.org/wiki/%EC%BD%94%EB%93%9C_%ED%8E%98%EC%9D%B4%EC%A7%80_949

			isUTF 와 urlEncode 를 사용하지 않았을 경우
			HTML 코드 형식이 utf-8 일 경우 cp949 ( 완성형 ) 코드셋 - ?,갛등의 경우 처리하기가 어렵다.
			이와 같은 경우는 HTML charter set 를
			ks_c_5601-1987 으로 선언하고 AJAX 통신의 경우 WebServer Default Charterset 을 cp949로 선언하거나
			Header 값에 cp949 코드로 선언하여야 한다.

			Parameters:
				s - 문자열

			Returns:
				true or false

			History:
				- 2007-10-06 : ASCII Code 0-127까지의 코드는 제외 시킴 ( utf-8, cp949 코드에서도 동일하기에 )
				- 2007-10-09 : 모든 문자열이 UTF8인지 검사하는 코드에서 UTF8 코드가 하나라도 있으면 UTF8 로 Return 함
				- 2007-10-10 : [\xc0-\xdf][\x80-\xbf] 코드 검사 제외 - 디키즈등의 한글의 경우 일반 POST 일 경우에 해당함
				- 2007-10-24 : '코사마트' 의 경우 POST 일때 [\xe0-\xef][\x80-\xbf]{2} 패턴이 발견되어 문자열 전체의 UTF 검사방법 재 도입
									[\xc0-\xdf][\x80-\xbf] 는 삭제함, 영문과 숫자일때는 UTF로 보지 않음
				- 2008-01-17 : @iconv("UTF-8","UTF-8",$s) == $s 방식으로 변경
				- 2009-10-20 : UTF-8 문자열인 경우 UTF-8로 iconv 한 결과가 입력값과 같고,
								UTF-8 문자열인 경우도 CP949로 iconv 한 결과가 입력값과 같아서 문제 발생.
								UTF-8로 iconv한 결과로 구분하여 정확한 캐릭터셋을 확인해야 함.
				- 2009-10-29 : '카키'와 같은 글자가 POST 전송 시에 UTF-8 중 'non-overlong 2-byte' 코드로 구분되는 문제
							'빈'과 같은 글자를 AJAX 전송 시에 UTF-8 코드로 인식하는 문제 처리


	*/
	/*
	function isUTF( $s ) {
		$isutf = false;
		$isutf = false;
		$ishigh = preg_match( '/[\x80-\xff]/', $s);				// 영문 또는 숫자라면 iconv pass
		if($ishigh){
			//$error_handler = set_error_handler('None');
			if(@iconv("UTF-8","UTF-8",$s) == $s){
				/-
				$chk1 = preg_match('/[\x09\x0A\x0D\x20-\x7E]/',$s);
				$chk2 = preg_match('/[\xC2-\xDF][\x80-\xBF]/',$s);
				$chk3 = preg_match('/\xE0[\xA0-\xBF][\x80-\xBF]/',$s);
				$chk4 = preg_match('/[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}/',$s);
				$chk5 = preg_match('/\xED[\x80-\x9F][\x80-\xBF]/',$s);
				$chk6 = preg_match('/\xF0[\x90-\xBF][\x80-\xBF]{2}/',$s);
				$chk7 = preg_match('/[\xF1-\xF3][\x80-\xBF]{3}/',$s);
				$chk8 = preg_match('/\xF4[\x80-\x8F][\x80-\xBF]{2}/',$s);

				printf("ASCII Check : %d<br>\n",$chk1);
				printf("non-overlong 2-byte : %d<br>\n",$chk2);
				printf("excluding overlongs  : %d<br>\n",$chk3);
				printf("straight 3-byte  : %d<br>\n",$chk4);
				printf("excluding surrogates  : %d<br>\n",$chk5);
				printf("planes 1-3 : %d<br>\n",$chk6);
				printf("planes 4-15: %d<br>\n",$chk7);
				printf("plane 16 : %d<br>\n",$chk8);
				-/

				// '카키'와 같은 글자가 POST 전송 시에 UTF-8 중 'non-overlong 2-byte' 코드로 구분되는 문제
				// '빈'과 같은 글자를 AJAX 전송 시에 UTF-8 코드로 인식하는 문제 처리

				if(preg_match('/[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}/',$s)
					|| (@iconv("UTF-8","UTF-8",$s) != $s)){
					$isutf = true;
				}
			}
			//set_error_handler($error_handler);
		}
		return $isutf;
	}
	*/

    public function pattern(Request $request) 
	{
		$array_config = array();
		
		$sql	= " select * from shop_sabangnet_config ";
		$result = DB::select($sql);

		foreach($result as $row) 
		{
			$array_config[$row->type][$row->code] = array(
				"value"		=> $row->value,
				"use_yn"	=> $row->use_yn
			);
		}

        $values = [
            'shop_config'	=> $array_config,
        ];
        return view( Config::get('shop.head.view') . '/product/prd30_pattern',$values);
    }

    public function pattern_save(Request $request) 
	{
        $id		= Auth('head')->user()->id;
		$name	= Auth('head')->user()->name;

		$result_code	= "200";
		$result_msg		= "";

		$pattern_a		= $request->input("pattern_a");
		$pattern_style	= $request->input("pattern_style");
		$pattern_script	= $request->input("pattern_script");
		$pattern_iframe	= $request->input("pattern_iframe");

        try 
		{
            DB::beginTransaction();

			$sql	= "
				update shop_sabangnet_config set
					use_yn = 'N'
				where type = 'pattern'
			";
			DB::update($sql);

			if( isset($pattern_a) && $pattern_a == "Y" )
			{
				$sql	= "
					update shop_sabangnet_config set
						use_yn	= 'Y',
						admin_id	= :admin_id,
						admin_nm	= :admin_nm,
						ut		= now()
					where
						type = 'pattern' and code = 'a'
				";
				DB::update($sql, ['admin_id' => $id, 'admin_nm' => $name]);
			}

			if( isset($pattern_style) && $pattern_style == "Y" )
			{
				$sql	= "
					update shop_sabangnet_config set
						use_yn	= 'Y',
						admin_id	= :admin_id,
						admin_nm	= :admin_nm,
						ut		= now()
					where
						type = 'pattern' and code = 'style'
				";
				DB::update($sql, ['admin_id' => $id, 'admin_nm' => $name ]);
			}

			if( isset($pattern_script) && $pattern_script == "Y" )
			{
				$sql	= "
					update shop_sabangnet_config set
						use_yn	= 'Y',
						admin_id	= :admin_id,
						admin_nm	= :admin_nm,
						ut		= now()
					where
						type = 'pattern' and code = 'script'
				";
				DB::update($sql, ['admin_id' => $id, 'admin_nm' => $name]);
			}

			if( isset($pattern_iframe) && $pattern_iframe == "Y" )
			{
				$sql	= "
					update shop_sabangnet_config set
						use_yn	= 'Y',
						admin_id	= :admin_id, 
						admin_nm	= :admin_nm,
						ut		= now()
					where
						type = 'pattern' and code = 'iframe'
				";
				DB::update($sql, ['admin_id' => $id, 'admin_nm' => $name]);
			}

			DB::commit();
        }
		catch(Exception $e) 
		{
            DB::rollback();

			$result_code	= "500";
			$result_msg		= "데이터 업데이트 오류";
		}
		
		
		return response()->json([
			"result_code"	=> $result_code,
			"result_msg"	=> $result_msg
		]);
	}

    public function deleteshop_goods(Request $request) 
	{
		$result_code	= "200";
		$result_msg		= "";

		$datas	= $request->input('data');
		$datas	= json_decode($datas);

		if( $datas == "" )
		{
			$result_code	= "400";
			$result_msg		= "삭제하실 상품이 존재하지 않습니다.";
		}

        try 
		{
            DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data	= (array)$datas[$i];
	
				$goods_no	= $data["goods_no"];
	
				$sql	= "
					delete from shop_sabangnet
					where goods_no = :goods_no and goods_sub = '0'
				";
				DB::delete($sql, ['goods_no' => $goods_no]);
			}

			DB::commit();
        }
		catch(Exception $e) 
		{
            DB::rollback();

			$result_code	= "500";
			$result_msg		= "데이터 삭제 오류";
		}

		return response()->json([
			"code"	=> $result_code,
			"msg"	=> $result_msg
		]);
	}
}
