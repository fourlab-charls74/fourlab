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
use App\Models\Jaego;
use App\Models\Order;


class prd31Controller extends Controller
{
    //
    public function index(Request $request) 
	{
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'day')->format('Y-m-d');

		$sql = "
			select distinct(mall_name) as mall_name from shop_sabangnet_order
		";

		$sale_places = DB::select($sql);


        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'ord_states'    => SLib::getOrdStates(),
            'clm_states'     => SLib::getCodes('G_CLM_STATE'),
            'sale_places'   => $sale_places,
        ];
        return view( Config::get('shop.head.view') . '/product/prd31',$values);
    }

    public function search(Request $request)
    {
		$page	= $request->input('page', 1);
        if( $page < 1 or $page == "" ) $page = 1;
        $limit	= $request->input('limit', 100);

		$edate          = $request->input("edate", date("Ymd"));
		$sdate          = $request->input("sdate", now()->sub(3, 'day')->format('Ymd'));
		$s_goods_no		= $request->input("goods_no");
		$s_style_no		= $request->input("style_no");
		$s_sale_place	= $request->input("s_site");
		$sale_place		= $request->input('site');
		$s_state		= $request->input("s_state");
		$s_ord_no		= $request->input("ord_no");
		$s_ord_nm		= $request->input("ord_nm");
		$s_rcv_nm		= $request->input("r_nm");
		$s_goods_nm		= $request->input("goods_nm");
		$s_ord_state	= $request->input("ord_state");
		$s_clm_state	= $request->input("clm_state");

        $ord		= $request->input('ord','desc');
        $ord_field	= $request->input('ord_field','g.goods_no');

        $orderby	= sprintf("order by %s %s", $ord_field, $ord);

		$where	= "";

		if( $s_style_no != "" )		$where .= " and style_no like '" . Lib::quote($s_style_no) . "%' ";
		if( $s_goods_no != "" )		$where .= " and g.goods_no = '$s_goods_no' ";
		if( $s_state != "" )		$where .= " and s.ord_state = '$s_state' ";
		if( $s_sale_place != "" )	$where .= " and s.mall_name = '$s_sale_place' ";
		if( $s_ord_no != "" )		$where .= " and o.ord_no = '$s_ord_no' ";
		if( $s_ord_nm != "" )		$where .= " and s.order_name = '$s_ord_nm' ";
		if( $s_rcv_nm != "" )		$where .= " and s.receive = '$s_rcv_nm' ";
		if( $s_goods_nm != "" )		$where .= " and s.product_name like '%$s_goods_nm%' ";
		if( $sale_place != "" )		$where .= " and s.mall_name = '$sale_place' ";
		// if( $com_id != "") 			$where .= " and com.com_id = '$com_id'";


		if( $s_ord_state != "" )	$where .= " and o.ord_state = '$s_ord_state' ";
		if( $s_clm_state == "90" ){
			$where .= " and o.clm_state = 0 ";
		} else {
			// if( $s_clm_state != "" )	$where .= " and o.clm_state = '$S_CLM_STATE' ";
		}

        $page_size	= $limit;
        $startno	= ($page - 1) * $page_size;
        $limit	= " limit $startno, $page_size ";

        $total	= 0;
        $page_cnt	= 0;

        if( $page == 1 )
		{
			$query = "
				select count(*) as cnt
				from shop_sabangnet_order s
					left outer join order_opt o on s.ord_opt_no = o.ord_opt_no
					left outer join goods g on s.partner_product_id = g.goods_no
					left outer join code dlv_cd on  dlv_cd.code_kind_cd = 'DELIVERY' and o.dlv_cd = dlv_cd.code_id
					left outer join code ord_state on o.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE'
					left outer join code clm_state on o.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE'
					-- left outer join company com on com.com_nm = s.mall_name
				where 
					1 = 1
					and ( s.orderdate >= :sdate and s.orderdate < date_add(:edate,interval 1 day))
					$where
			";
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);
            $total = $row[0]->cnt;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

		$sql	= "
			select
				'' as chk, o.ord_no, o.ord_opt_no, s.sabangnet_order_id, s.mall_order_id, s.mall_name, s.baesong_status,
				s.order_product_id, s.sabangnet_product_id, s.partner_product_id, g.goods_no, g.style_no,
				s.product_name, sku,
				if(o.ord_opt_no > 0,o.goods_opt,s.opt) as opt,
				g.price as goods_price, s.sale_price,
				s.quantity, s.order_price,
				((s.sale_price-s.supply_price)*s.quantity) as sale_fee,
				round(((1-(s.supply_price/s.sale_price))*100),2) as sale_fee_ratio,
				s.order_name, s.order_tel, s.order_cel, s.order_email,
				s.receive, s.receive_tel, s.receive_cel, s.receive_zipcode, s.receive_addr,
				s.baesong_type, s.baesong_bi, s.delivery_msg,
				dlv_cd.code_val  as dlv_cd,o.dlv_no,
				s.ord_state as shop_state,
				ord_state.code_val as ord_state,
				clm_state.code_val as clm_state,
				s.orderdate,s.order_reg_date,
				-- com.com_id,
				s.admin_nm,
				s.rt,s.ut
			from shop_sabangnet_order s
				left outer join order_opt o on s.ord_opt_no = o.ord_opt_no
				left outer join goods g on s.partner_product_id = g.goods_no
				left outer join code dlv_cd on  dlv_cd.code_kind_cd = 'DELIVERY' and o.dlv_cd = dlv_cd.code_id
				left outer join code ord_state on o.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE'
				left outer join code clm_state on o.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE'
				-- left outer join company com on com.com_nm = s.mall_name
			where 
				1 = 1	
				and ( s.orderdate >= :sdate and s.orderdate < date_add(:edate,interval 1 day))
				$where
            $orderby
			$limit
		";




        $result = DB::select($sql, ['sdate' => $sdate,'edate' => $edate]);
        
		foreach($result as $row) 
		{
			//$price	= $row->price;
			if( $row->opt == "NONE" )
			{
				$row->opt	= $this->transOpt($row->mall_name, $row->sku);
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

	public function skuoption($goods_no, Request $request)
    {
		$goods_nm	= $request->input('goods_nm');
		$sku		= $request->input('sku');
		$id			= $request->input('id');

        $values = [
            'goods_no'	=> $goods_no,
			'goods_nm'	=> $goods_nm,
			'sku'		=> $sku,
			'id'		=> $id
        ];

		return view( Config::get('shop.head.view') . '/product/prd31_option',$values);
	}

	public function skuoption_search(Request $request)
    {
		$goods_no	= $request->input('goods_no');

		$sql	= "
			select
				goods_opt, good_qty, '선택' as psend
			from goods_summary
			where 
				goods_no = :goods_no
			order by goods_opt
		";
        $result = DB::select($sql, ['goods_no' => $goods_no]);

        return response()->json([
            "code"	=> 200,
            "head"	=> array(
				"total" => count($result)
            ),
            "body"	=> $result
        ]);

	}

	public function goodsopt_add(Request $request)
    {
		$result_code	= "200";
		$result_msg		= "";

		$admin_id		= Auth('head')->user()->id;
		$admin_nm		= Auth('head')->user()->name;

		$goods_no	= $request->input('goods_no');
		$goods_opt	= $request->input('goods_opt');
		$wonga		= 0;
		$stock_state_date	= date("Ymd");

        try 
		{
            DB::beginTransaction();

			$sql	= " select wonga from goods_good where goods_no = '$goods_no' and goods_opt = '$goods_opt' order by no desc limit 1 ";
			$row = DB::selectOne($sql);

			if(empty($row->wonga)) {
				//$result_code	= "-2";
				//$result_msg		= "상품 옵션 데이터를 찾을 수 없습니다.";
			}
			else
			{
				$wonga	= sprintf("%s",$row->wonga);
			}

			// goods_summary 상품수량 수정
			$sql	= " update goods_summary set good_qty = good_qty + 1, wqty = wqty + 1 where goods_no = '$goods_no' and goods_opt = '$goods_opt' ";
			DB::update($sql);

			// goods_good 상품수량 추가 등록
			$sql	= " 
				insert into goods_good(goods_no, goods_sub, goods_opt, wonga, qty, invoice_no, init_qty, regi_date)
				values ('$goods_no', '0', '$goods_opt', '$wonga', '1', 'INV_ADJUST', '1', now())
			";
			DB::insert($sql);

			// goods_history 상품히스토리 등록
			$sql	= "
				insert into goods_history(goods_no, goods_sub, goods_opt, wonga, type, stock_state, qty, etc, admin_id, admin_nm, regi_date, invoice_no, stock_state_date)
				values ('$goods_no', '0', '$goods_opt', '$wonga', '9', '1', '1', '실사', '$admin_id', '$admin_nm', now(), 'INV_ADJUST', '$stock_state_date')
			";
			DB::insert($sql);

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

	public function get_order(Request $request)
    {
		// 설정 값 얻기
        $conf	= new Conf();
		$cfg_domain_bizest	= $conf->getConfigValue("shop","domain_handle");
		//$cfg_domain_bizest	= "127.0.0.1:8000/head";
		$cfg_domain_bizest	.= "/head";

		$result_code	= "100";
		$result_msg		= "";

		$admin_id		= Auth('head')->user()->id;
		$admin_nm		= Auth('head')->user()->name;

		$s_sdate		= str_replace("-","",$request->input('s_sdate'));
		$s_edate		= str_replace("-","",$request->input('s_edate'));

		// XML 연동
		//$url	= sprintf("http://%s/api/sabangnet/order_xml.php?c=get_order&ORD_ST_DATE=%s&ORD_ED_DATE=%s",$cfg_domain_bizest,$s_sdate,$s_edate);
		$url	= sprintf("https://%s/api/sabangnet/order_xml/get_order?ORD_ST_DATE=%s&ORD_ED_DATE=%s",$cfg_domain_bizest,$s_sdate,$s_edate);
		//$url	= sprintf("http://r.sabangnet.co.kr/RTL_API/xml_order_info.html?xml_url=%s",urlencode($url));
		$url	= sprintf("https://sbadmin14.sabangnet.co.kr/RTL_API/xml_order_info.html?xml_url=%s",urlencode($url));

		// 몰별 옵션처리
		$sql	= " select mall_name, sale_place, sku from shop_sabangnet_mall ";
		$result = DB::select($sql);

		foreach($result as $row) 
		{
			$sale_places[$row->mall_name]	= array(
				"sale_place"	=> $row->sale_place,
				"sku"			=> $row->sku
			);
		}

		$response = Http::get($url);

		$result_code	= $response->status();

		if( $result_code == 200 )
		{
			$result_body	= iconv("CP949","UTF-8",$response->body());
			//$result_body	= $response->body();

			// XML 형식이 아닌 경우 simplexml_load_string 처리 시 e-warnning 에러 발생함
			// 따라서 아래의 libxml_use_internal_errors 를 설정함
			//@libxml_use_internal_errors(true);

			// 인코딩 변경 : ", "x" 등 EUC-KR 형식에서 지원하지 않는 문자인 경우 CP949로 변경 후 simplexml_load_string 실행 가능
			$patterns	= array("encoding=\"EUC-KR\"", "encoding='EUC-KR'", "encoding=\"euc-kr\"", "encoding='euc-kr'");
			$replaces	= array("encoding=\"UTF-8\"", "encoding='UTF-8'","encoding=\"UTF-8\"","encoding='UTF-8'");
			$result_body	= str_replace($patterns,$replaces,$result_body);

			//$result_code	= $result_body;
			//$result_code = $s_sdate;

			// CDATA 처리된 문자열의 인식을 위해 LIBXML_NOCDATA 옵션 적용
			$array	= @simplexml_load_string($result_body,null,LIBXML_NOCDATA);

			if($array)
			{
				$cnt	= count($array->DATA);
	
				$ord_state	= 9;
				$ord_no		= "";
				$ord_opt_no	= "";
	
				// DB 커넥션 체크
				try {
					$sql	= " select 1; ";
					DB::select($sql);
				} catch (Exception $exceptionOnReConnect) {
					DB::reconnect('mysql');
				}
	
				for( $i = 0; $i < $cnt; $i++ )
				{
					$ord_state	= 9;
	
					$sabangnet_order_id	= $array->DATA[$i]->IDX;
					$mall_order_id		= $array->DATA[$i]->ORDER_ID;
					$mall_name			= Lib::quote($array->DATA[$i]->MALL_ID);
					$baesong_status		= Lib::quote($array->DATA[$i]->ORDER_STATUS);
					$order_name			= Lib::quote($array->DATA[$i]->USER_NAME);
					$order_tel			= Lib::quote($array->DATA[$i]->USER_TEL);
					$order_cel			= Lib::quote($array->DATA[$i]->USER_CEL);
					$order_email		= Lib::quote($array->DATA[$i]->USER_EMAIL);
					$receive			= Lib::quote($array->DATA[$i]->RECEIVE_NAME);
					$receive_tel		= Lib::quote($array->DATA[$i]->RECEIVE_TEL);
					$receive_cel		= Lib::quote($array->DATA[$i]->RECEIVE_CEL);
					$receive_zipcode	= Lib::quote($array->DATA[$i]->RECEIVE_ZIPCODE);
					$receive_addr		= Lib::quote(str_replace("'","",$array->DATA[$i]->RECEIVE_ADDR));
					$baesong_type		= Lib::quote($array->DATA[$i]->DELIVERY_METHOD_STR);
					$baesong_bi			= Lib::quote($array->DATA[$i]->DELV_COST);
					$delivery_msg		= Lib::quote($array->DATA[$i]->DELV_MSG);
					$order_product_id	= Lib::quote($array->DATA[$i]->MALL_PRODUCT_ID);
					$shoplinker_product_id	= Lib::quote($array->DATA[$i]->PRODUCT_ID);
					$partner_product_id	= Lib::quote($array->DATA[$i]->COMPAYNY_GOODS_CD);
					$product_name		= Lib::quote($array->DATA[$i]->PRODUCT_NAME);
					$quantity			= Lib::quote($array->DATA[$i]->SALE_CNT);
					$order_price		= Lib::quote($array->DATA[$i]->TOTAL_COST);
					$sale_price			= Lib::quote($array->DATA[$i]->SALE_COST);
					$supply_price		= Lib::quote($array->DATA[$i]->MALL_WON_COST);
					$sku				= Lib::quote($array->DATA[$i]->SKU_VALUE);
					$orderdate			= Lib::quote($array->DATA[$i]->ORDER_DATE);
					
	
					//
					// 세트상품처리
					//
					$p_ea	= Lib::CheckInt(Lib::quote($array->DATA[$i]->P_EA));


					//

					if( $p_ea > $quantity )	$quantity = $p_ea;
	
					if( $order_name != "" && $receive != "" && $receive_tel != "" && $receive_zipcode != "" && $receive_addr != "" )	$ord_state = 10;
	

					// 인코딩 과정에서 주문자 데이터가 누락되면 수령자를 주문자에 넣어주기
					if ( $order_name == '') {
						$order_name = $receive;
					}

					$sql	= " select count(*) as cnt from shop_sabangnet_order where sabangnet_order_id = '$sabangnet_order_id' ";
					$row	= DB::selectOne($sql);

					if( $row->cnt == 0 )
					{
						$opt	= "NONE";

						if( $sku != "" )
						{
							$pattern	= isset($sale_places[$mall_name])? $sale_places[$mall_name]["sku"]:"";
							//echo "pattern : $pattern , $sku";
							if($pattern != "" && preg_match("/$pattern/i",$sku,$match)){
								$opt = str_replace('ㅡ','/',trim($match[1]));
							}
						}
	
						$sql	= "
							insert into shop_sabangnet_order (
								sabangnet_order_id, mall_order_id, mall_name, baesong_status,
								order_name, order_tel, order_cel, order_email,
								receive, receive_tel, receive_cel, receive_zipcode, receive_addr,
								baesong_type, baesong_bi, delivery_msg,
								order_product_id,
								sabangnet_product_id,
								partner_product_id,product_name,opt,
								quantity, order_price, sale_price, supply_price, sku,
								orderdate,order_reg_date,
								ord_state,ord_no,ord_opt_no,
								admin_id,admin_nm,
								rt,ut
							) values (
								'$sabangnet_order_id', '$mall_order_id', '$mall_name', '$baesong_status',
								'$order_name', '$order_tel', '$order_cel', '$order_email',
								'$receive', '$receive_tel', '$receive_cel', '$receive_zipcode', '$receive_addr',
								'$baesong_type', '$baesong_bi', '$delivery_msg',
								'$order_product_id',
								'$shoplinker_product_id',
								'$partner_product_id','$product_name','$opt',
								'$quantity', '$order_price', '$sale_price', '$supply_price', '$sku',
								'$orderdate',now(),
								'$ord_state','$ord_no','$ord_opt_no',
								'$admin_id','$admin_nm',now(),now()
							)
						";
						DB::insert($sql);
	
						// 주문 자동 매칭
						$this->MatchOrder($sabangnet_order_id);
					}
				}
			}
	
		}
		else 
		{
			$result_code	= "-1";
			$result_msg = "Http Error";
		}
		
		return response()->json([
			"result_code"	=> $result_code,
			"result_msg"	=> $result_msg
		]);

	}

	/**
	 * 사방넷 주문 매칭
	 *	- 주문 수집 전에 수기판매로 등록된 주문건이 있는 경우에 해당
	 */
	function MatchOrder($sabangnet_order_id)
	{
		$cnt	= 0;

		$sql	= "
			select mall_order_id,mall_name
			from shop_sabangnet_order
			where sabangnet_order_id = '$sabangnet_order_id' and ord_state <= 10 and ord_opt_no = ''
		";
		$row = DB::selectOne($sql);
		if( empty($row->mall_order_id) ) {
		}
		else
		{
			$mall_order_id	= $row->mall_order_id;
			$mall_name		= $row->mall_name;

			$sql	= "
				select count(*) as cnt
				from shop_sabangnet_order
				where mall_order_id = '$mall_order_id' and mall_name = '$mall_name'
			";
			$row = DB::selectOne($sql);
			if( $row->cnt == 1 )
			{
				$sql	= "
					select m.ord_no, max(ord_opt_no) as ord_opt_no, count(*) as cnt
					from order_mst m inner join order_opt o on m.ord_no = o.ord_no
					where m.out_ord_no = '$mall_order_id'
					group by m.ord_no
				";
				$row_sub = DB::selectOne($sql);

				if( empty($row_sub->cnt) ) {
				}
				else
				{
					if( $row_sub->cnt == 1 )
					{
						$ord_no		= $row_sub->ord_no;
						$ord_opt_no = $row_sub->ord_opt_no;
						$sql	= "
							update shop_sabangnet_order set
								ord_no		= '$ord_no',
								ord_opt_no	= '$ord_opt_no',
								ord_state	= 20,
								ut			= now()
							where sabangnet_order_id = '$sabangnet_order_id' and ord_state <= 10 and ord_opt_no = ''
						";
						DB::update($sql);

						$cnt++;
					}
				}
			}
		}

		return $cnt;
	}

	public function add_order(Request $request)
    {
        $user	= [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

		$result_code	= "100";
		$result_msg		= "";
		$result_no		= "0";

		$admin_id		= Auth('head')->user()->id;
		$admin_nm		= Auth('head')->user()->name;

		$sabangnet_order_id	= $request->input('order_id');
		$goods_no		= $request->input('goods_no');
		$goods_sub		= 0;
		$goods_opt		= trim($request->input('option'));
		$sales_com_fee	= $request->input('sale_fee');

		$baesong_types	= array("착불");
		$sale_places	= array();

		$sql	= " select mall_name, sale_place, sku from shop_sabangnet_mall ";
        $result	= DB::select($sql);
        
		foreach($result as $row) 
		{
			$sale_places[$row->mall_name] = array(
				"sale_place"=> $row->sale_place,
				"sku"		=> $row->sku
			);
		}

		$sql = "
			select
				s.mall_order_id, s.mall_name, s.baesong_status,
				s.order_name, s.order_tel, s.order_cel, s.order_email,
				s.receive, s.receive_tel, s.receive_cel, s.receive_zipcode, s.receive_addr,
				s.baesong_type, s.baesong_bi, s.delivery_msg,
				s.order_product_id,
				s.sabangnet_product_id,
				s.partner_product_id,s.product_name,sku,'' as opt,
				s.quantity, s.sale_price,s.order_price,
				s.orderdate,s.order_reg_date,
				s.ord_state,s.ord_no,s.ord_opt_no
			from shop_sabangnet_order s
			where s.sabangnet_order_id = '$sabangnet_order_id'
		";
		$row = DB::selectOne($sql);

		if(empty($row->mall_order_id)) {
			$result_code	= "103";
			$result_msg		= "사방넷 주문번호 오류";
		}
		else
		{
			try 
			{
				DB::beginTransaction();

				$ORD_NO			= "";
				$ORD_TYPE		= "16";		// 오픈마켓
				$ORD_KIND		= "20";		// 출고구분 : 20 입금, 30 보류
				$ORD_STATE		= "10";		// 마스터 주문상태 : 10 출고요청
				$PAY_TYPE		= "1";		// 결제수단 : 1 현금, 2 카드
				$PAY_STAT		= "1";		// 결제상태 : 0 미입금, 1 입금
				$BANK_INPNM		= "";			//str_replace("'","`",$col[24]); //"D&SHOP";
				$BANK_CODE		= "오픈마켓";
				$BANK_NUMBER	= "";
				$USER_ID		= "";
	
				$OUT_ORD_NO		= $row->mall_order_id;
	
				$ORD_DATE		= date("Ymdhis");
				$RECV_AMT		= $row->order_price;
	
				$USER_NM		= trim($row->order_name);
				$PHONE			= trim($row->order_tel);
				$MOBILE			= $row->order_cel;
				$EMAIL			= $row->order_email;
				$R_NM			= Lib::quote($row->receive);
				$R_PHONE		= $row->receive_tel;
				$R_MOBILE		= $row->receive_cel;
				$R_ZIPCODE		= $row->receive_zipcode;
				$R_ADDR1		= Lib::quote($row->receive_addr);
				$R_ADDR2		= "";
				$HEAD_DESC		= "";
				$GOODS_NM		= Lib::quote($row->product_name);
				$DLV_MSG		= Lib::quote($row->delivery_msg);
				$DLV_AMT		= $row->baesong_bi;
	
				$DLV_PAY_TYPE	= "P";
				if(in_array($row->baesong_type, $baesong_types)){
					$DLV_PAY_TYPE = "F";
				}
	
				$QTY			= $row->quantity;
				$PRICE			= $row->sale_price;
	
				// 유효성 검사 
				if( $row->order_name == "" )
				{
					$result_code	= "101";
					$result_msg		= "주문자명 없음";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}
				if( $row->receive == "" )
				{
					$result_code	= "102";
					$result_msg		= "수령자명 없음";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}
				if( $row->receive_zipcode == "" )
				{
					$result_code	= "103";
					$result_msg		= "수령자 우편번호 없음";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}
				if( $row->receive_addr == "" )
				{
					$result_code	= "103";
					$result_msg		= "수령자 주소 없음";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}
				if( isset($sale_places[$row->mall_name]))
				{
					$SALE_PLACE		= $sale_places[$row->mall_name]["sale_place"];
				} 
				else 
				{
					$result_code	= "110";
					$result_msg		= "판매처 정보 없음";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}
				if( $row->ord_state > 10 )
				{
					if( $row->ord_state == 20 ){
						$result_code	= "310";
						$result_msg		= "주문상태오류[1]";
					} else {
						$result_code	= "320";
						$result_msg		= "주문상태오류[2]";
					}

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}

				$sql = "
					select
						a.head_desc, a.goods_nm, a.style_no, a.md_id, a.md_nm, b.com_nm, a.com_id,
						a.baesong_kind, a.baesong_price, b.pay_fee/100 as com_rate, a.price, a.com_type,
						a.is_unlimited
					from goods a left outer join company b on a.com_id  = b.com_id
					where a.goods_no = '$goods_no' and a.goods_sub = '$goods_sub'
				";
				$row = DB::selectOne($sql);

				if(empty($row->goods_nm)) {
					$result_code	= "210";
					$result_msg		= "상품번호 부정확";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}
				else
				{
					$md_id			= $row->md_id;
					$md_nm			= Lib::quote($row->md_nm);
					$com_id			= $row->com_id;
					$baesong_kind	= $row->baesong_kind;
					$goods_price	= $row->price;
					$is_unlimited	= $row->is_unlimited;
	
					$GOODS_NM		= Lib::quote($row->goods_nm);
					$ORD_AMT		= $goods_price * $QTY;
				}

				// 옵션검사
				$jaego	= new Jaego( $user );
				if($jaego->IsOption($goods_no,$goods_sub,$goods_opt) == false)
				{
					$result_code	= "220";
					$result_msg		= "옵션 부정확";

					return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
					exit;					
				}

				if( $ORD_NO == "" )
				{
					// 주문 객체 생성
					$order	= new Order( $user, true );
					$ORD_NO	= $order->ord_no;
				}
				else
				{
					// 주문 객체 생성
					$order	= new Order( $user );
				}

				/* 재고 확인 */
				$ret	= 0;
				$is_stock	= true;
				$good_qty	= $jaego->GetQty($goods_no, $goods_sub, $goods_opt);

				if( $is_unlimited == "Y" )
				{
					if( $good_qty == 0 )
					{
						$is_stock	= false;
						$ret		= 110;
					}
				} 
				else 
				{
					if( $QTY > $good_qty )
					{
						$is_stock	= false;
						$ret		= 110;
					}
				}

				/*
				*	묶음 주문 여부 확인
				*/
				$ord_seq	= 0;
				$ord_state	= ($is_stock == true) ? "10" : "5";
				$clm_state	= ($is_stock == true) ? "0" : "0";								// 클레임 : 주문취소 상태
				$dlv_start_date	= ($ord_state == "10" ) ? "now()" : "null";

				$sql	= "
					select m.ord_no, m.user_nm, o.goods_no,o.goods_opt,o.out_ord_opt_no
					from order_mst m inner join order_opt o on m.ord_no = o.ord_no
					where m.out_ord_no = '$OUT_ORD_NO' and m.sale_place = '$SALE_PLACE'
				";
				$result = DB::select($sql);
        
				foreach($result as $row) 
				{
					$ORD_NO = $row->ord_no;		// 기존 존재 주문건이면 주문번호 가져옴

					if( $row->goods_no == $goods_no && $row->goods_opt == $goods_opt && $row->out_ord_opt_no == $sabangnet_order_id ) 
					{
						$result_code	= "230";
						$result_msg		= "이미 등록된 주문";
	
						return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
						exit;					
					}

					if( $row->user_nm != $USER_NM )
					{
						$result_code	= "240";
						$result_msg		= "묶음주문 주문명 불일치";
	
						return response()->json(["result_code"	=> $result_code, "result_msg"	=> $result_msg, "result_no"	=> $result_no]);
						exit;					
					}

					$ord_seq++;
				}

				if( $ord_seq == 0 )
				{
					$sql	= "
						insert into order_mst (
							ord_no,ord_date,user_id,user_nm,phone,mobile,email,ord_amt,recv_amt,point_amt,coupon_amt,dlv_amt,
							r_nm,r_zipcode,r_addr1,r_addr2,r_phone,r_mobile,dlv_msg,url,c_link,
							ord_state,upd_date,dlv_end_date,ord_type,ord_kind, sale_place, out_ord_no
						) values (
							'$ORD_NO',now(),'$USER_ID','$USER_NM','$PHONE','$MOBILE','$EMAIL','$ORD_AMT','$RECV_AMT',0,0,'$DLV_AMT',
							'$R_NM','$R_ZIPCODE','$R_ADDR1','$R_ADDR2','$R_PHONE','$R_MOBILE','$DLV_MSG',null,null,
							'$ORD_STATE',now(),null,'$ORD_TYPE','$ORD_KIND','$SALE_PLACE', '$OUT_ORD_NO'
						)
					";
					DB::insert($sql);

					$sql	= "
						insert into payment (
							ord_no,pay_type,pay_nm,pay_amt,pay_stat,bank_inpnm,bank_code,bank_number,
							card_code,card_isscode,card_quota,card_appr_no,card_appr_dm,card_tid,card_msg,
							ord_dm,upd_dm,pay_ypoint,pay_point,pay_baesong,card_name,nointf
						) values (
							'$ORD_NO','$PAY_TYPE','$USER_NM','$RECV_AMT','$PAY_STAT','$BANK_INPNM','$BANK_CODE','$BANK_NUMBER',
							null,null,null,null,null,null,null,
							date_format('$ORD_DATE','%Y%m%d%Hi%s%'),date_format(now(),'%Y%m%d%H%i%s'),0,0,0,null,null
						)
					";
					DB::insert($sql);
				}
				else
				{
					$sql	= "
						update order_mst set
							ord_amt = ord_amt + $ORD_AMT,
							recv_amt = recv_amt + $RECV_AMT,
							dlv_amt = dlv_amt + $DLV_AMT
						where ord_no = '$ORD_NO'
					";
					DB::update($sql);

					$sql	= " update payment set pay_amt = pay_amt + $RECV_AMT where ord_no = '$ORD_NO' ";
					DB::update($sql);
				}

				$ord_opt_no	= DB::table('order_opt')->insertGetId([
					'goods_no'			=> $goods_no,
					'goods_sub'			=> $goods_sub,
					'ord_no'			=> $ORD_NO,
					'ord_seq'			=> $ord_seq,
					'head_desc'			=> $HEAD_DESC, 
					'goods_nm'			=> $GOODS_NM, 
					'goods_opt'			=> $goods_opt, 
					'qty'				=> $QTY, 
					'price'				=> $PRICE, 
					'pay_type'			=> $PAY_TYPE, 
					'dlv_pay_type'		=> $DLV_PAY_TYPE, 
					'dlv_amt'			=> $DLV_AMT,
					'point_amt'			=> 0, 
					'coupon_amt'		=> 0, 
					'recv_amt'			=> $RECV_AMT, 
					'bundle_ord_opt_no'	=> null, 
					'p_ord_opt_no'		=> null, 
					'dlv_no'			=> null, 
					'dlv_cd'			=> null, 
					'md_id'				=> $md_id, 
					'md_nm'				=> $md_nm,
					'sale_place'		=> $SALE_PLACE, 
					'ord_state'			=> $ord_state, 
					'clm_state'			=> $clm_state, 
					'com_id'			=> $com_id, 
					'add_point'			=> 0, 
					'ord_kind'			=> $ORD_KIND, 
					'ord_type'			=> $ORD_TYPE, 
					'baesong_kind'		=> $baesong_kind,
					'dlv_start_date'	=> $dlv_start_date, 
					'dlv_proc_date'		=> null, 
					'dlv_end_date'		=> null, 
					'dlv_cancel_date'	=> null, 
					'ord_date'			=> now(), 
					'dlv_comment'		=> '', 
					'admin_id'			=> $admin_id, 
					'sales_com_fee'		=> $sales_com_fee, 
					'out_ord_opt_no'	=> $sabangnet_order_id
				]);

				if( $is_stock ) 
				{
					$order->SetOrdNo( $ORD_NO );

					/**
					 * 주문상태 로그
					 */
					$state_log	= array(
						"ord_no"		=> $ORD_NO,
						"ord_opt_no"	=> $ord_opt_no,
						"ord_state"		=> "10",
						"comment"		=> "수기판매일괄",
						"admin_id"		=> $admin_id,
						"admin_nm"		=> $admin_nm
					);
					$order->AddStateLog($state_log);

					// 재고 차감
					$order->CompleteOrderSugi($ord_opt_no, $ord_state);

				} 
				else 
				{
					$order->SetOrdNo( $ORD_NO );

					/**
					 * 주문상태 로그
					 */
					$state_log	= array(
						"ord_no"		=> $ORD_NO,
						"ord_opt_no"	=> $ord_opt_no,
						"ord_state"		=> "5",
						"comment"		=> "수기판매일괄(품절)",
						"admin_id"		=> $admin_id,
						"admin_nm"		=> $admin_nm
					);
					$order->AddStateLog($state_log);

					// 재고 없는 경우 주문상태 변경
					$order->OutOfScockAfterPaid();
				}
				
				$sql	= "
						update shop_sabangnet_order set
							partner_product_id	= '$goods_no',
							ord_no		= '$ORD_NO',
							ord_opt_no	= '$ord_opt_no',
							ord_state	= 20,
							ut			= now()
						where sabangnet_order_id = '$sabangnet_order_id'
				";
				DB::update($sql);

				$result_code	= "200";

				if( $ret == 110 )	$result_msg = sprintf("%s[품절]",$ORD_NO);
				else				$result_msg = sprintf("%s",$ORD_NO);

				$result_no		= $ord_opt_no;

				DB::commit();
			}
			catch(Exception $e) 
			{
				DB::rollback();

				$result_code	= "500";
				$result_msg		= "데이터 업데이트 오류";
			}
		}		



		return response()->json([
			"result_code"	=> $result_code,
			"result_msg"	=> $result_msg,
			"result_no"		=> $result_no
		]);
	}

	public function del_order(Request $request)
    {
		$error_code	= "200";
		$result_msg	= "";

        $datas	= $request->input('data');
		$datas	= json_decode($datas);

        try 
		{
            DB::beginTransaction();

			for( $i = 0; $i < count($datas); $i++ )
			{
				$data	= (array)$datas[$i];
				$sabangnet_order_id	= $data["sabangnet_order_id"];

				$sql	= " delete from shop_sabangnet_order where sabangnet_order_id = :sabangnet_order_id and ord_state <= 10 ";
				DB::delete($sql,['sabangnet_order_id' => $sabangnet_order_id]);
			}

			DB::commit();
        }
		catch(Exception $e) 
		{
            DB::rollback();

			$error_code	= "500";
			$result_msg		= "데이터 업데이트 오류";
		}

		return response()->json([
			"code"			=> $error_code,
			"result_msg"	=> $result_msg
		]);

	}
	public function dlv_order(Request $request)
    {
		// 설정 값 얻기
        $conf	= new Conf();
        //$cfg_domain_bizest	= $conf->getConfigValue("shop","domain_bizest");
        $cfg_domain_bizest	= $conf->getConfigValue("shop","domain_handle");
		//$cfg_domain_bizest	= "127.0.0.1:8000/head";
		$cfg_domain_bizest	.= "/head";

		$result_code	= "100";
		$result_msg		= "";

		$admin_id		= Auth('head')->user()->id;
		$admin_nm		= Auth('head')->user()->name;

		$sabangnet_order_id	= $request->input('order_id');

		// XML 연동
		//$url = sprintf("http://%s/api/sabangnet/delivery_xml.php?c=dlv_view&order_id=%s",$cfg_domain_bizest,$sabangnet_order_id);
		$url = sprintf("http://%s/api/sabangnet/delivery_xml/dlv_view?order_id=%s",$cfg_domain_bizest,$sabangnet_order_id);
		//$url = sprintf("http://r.sabangnet.co.kr/RTL_API/xml_order_invoice.html?xml_url=%s",urlencode($url));
		$url = sprintf("https://sbadmin14.sabangnet.co.kr/RTL_API/xml_order_invoice.html?xml_url=%s",urlencode($url));

		$response = Http::get($url);

		$result_code	= $response->status();

		if( $result_code == 200 )
		{
			$result_body	= iconv("CP949","UTF-8",$response->body());

			$sql = "
				update shop_sabangnet_order set
					ord_state = 30,
					ut = now()
				where sabangnet_order_id = '$sabangnet_order_id' and ord_state = 20
			";
			DB::update($sql);

		}
		else 
		{
			$result_code	= "-1";
			$result_msg = "Http Error";
		}
		
		return response()->json([
			"result_code"	=> $result_code,
			"result_msg"	=> $result_msg
		]);

	}

	// 2021-03-08 ceduce 몰별 옵션 자동 생성 작업
	// 피엘라벤 커스텀 작업
	function transOpt($shop_nm, $sku)
	{
		$trans_chk	= "N";
		$data		= $sku;

		// 1. 하프 클럽
		if( $shop_nm == "하프클럽(신)" )
		{
			//한국 옵션 변경
			/*
			$data	= str_replace("_ 한국사이즈XS", "(한국사이즈XS)", $data);
			$data	= str_replace("_ 한국사이즈 XS", "(한국사이즈 XS)", $data);
			$data	= str_replace("_ 한국사이즈S", "(한국사이즈S)", $data);
			$data	= str_replace("_ 한국사이즈 S", "(한국사이즈 S)", $data);
			$data	= str_replace("_ 한국사이즈M", "(한국사이즈M)", $data);
			$data	= str_replace("_ 한국사이즈 M", "(한국사이즈 M)", $data);
			$data	= str_replace("_ 한국사이즈L", "(한국사이즈L)", $data);
			$data	= str_replace("_ 한국사이즈 L", "(한국사이즈 L)", $data);
			$data	= str_replace("_ 한국사이즈XL", "(한국사이즈XL)", $data);
			$data	= str_replace("_ 한국사이즈 XL", "(한국사이즈 XL)", $data);

			$data	= str_replace("_ 25.26 inch", "(25~26 inch)", $data);
			*/
			if( strpos($data, " _ ") != "" )
			{
				$data	= str_replace(" _ ", " (", $data);
				$data	= $data . ")";

				$data	= str_replace("23.24", "23~24", $data);
				$data	= str_replace("25.26", "25~26", $data);
				$data	= str_replace("27.28", "27~28", $data);
				$data	= str_replace("29.30", "29~30", $data);
				$data	= str_replace("31.32", "31~32", $data);
				$data	= str_replace("33.34", "33~34", $data);
				$data	= str_replace("35.36", "35~36", $data);
			}

			//1-1 / => ^ 변경
			$data	= str_replace("/", "^", $data);
			//1-2 . => / 변경
			//$data	= str_replace(".", "/", $data);
		}



		// 2. CJMall
		if( $shop_nm == "CJOshopping (신)" )
		{
			//2-1 / => ^ 변경
			$data	= str_replace("/", "^", $data);
			//2-2 . => / 변경
			//$data	= str_replace(".", "/", $data);

			//$data	= "NONE";
		}



		// 3. AKMall
		if( $shop_nm == "AKmall(신)" )
		{
			//3-1 / => ^ 변경
			$data	= str_replace(":", "^", $data);
			//3-2 . => / 변경
			//$data	= str_replace(".", "/", $data);

			//$data	= "NONE";
		}



		// 4. 신세계몰(신)
		if( $shop_nm == "신세계몰(신)" )
		{
			//4-1 / => ^ 변경
			$data	= str_replace("/", "^", $data);
			//4-2 . => / 변경
			//$data	= str_replace(".", "/", $data);

			//$data	= "NONE";
		}



		// 5. 무신사
		if( $shop_nm == "무신사" )
		{
			//5-1 / => ^ 변경
			$data	= str_replace(" : ", "^", $data);
			//5-2 . => / 변경
			//$data	= str_replace(".", "/", $data);

			//$data	= "NONE";
		}



		// 6. 쿠팡
		if( $shop_nm == "쿠팡" )
		{
			$data	= "NONE";
		}



		// 7. LG패션
		if( $shop_nm == "LG패션" )
		{
			//7-1 None[XX]: => '' 변경
			$data	= str_replace("None[XX]:", "", $data);
			//7-1 / => ^ 변경
			$data	= str_replace(" / ", "^", $data);
			//7-2 None One Size => None^One Size 변경
			$data	= str_replace("None One Size", "None^One Size", $data);

			if( strpos($data, "^") == "" )	$data = "NONE";

			//$data	= "NONE";
		}



		// 8. 11번가
		if( $shop_nm == "11번가" )
		{
			//8-1 None[XX]: => '' 변경
			$data	= str_replace("컬러:사이즈:", "", $data);
			$data	= str_replace("-1개", "", $data);
			$data	= str_replace("-2개", "", $data);
			//8-2 / => ^ 변경
			$data	= str_replace(":", "^", $data);

			//$data	= "NONE";
		}



		// 9. WIZWID
		if( $shop_nm == "WIZWID" )
		{
			//9-1 / => ^ 변경
			$data	= str_replace(":", "^", $data);
			//5-2 . => / 변경
			//$data	= str_replace(".", "/", $data);

			//$data	= "NONE";
		}



		return $data;
	}

}
