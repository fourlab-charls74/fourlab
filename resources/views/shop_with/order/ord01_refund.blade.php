@extends('shop_with.layouts.layout-nav')
@section('title','환불')
@section('content')

<script>
// 기본 배송비 / 배송비 면제 금액
var g_dlv_fee		= {{ $g_dlv_fee }};
var g_dlv_add_fee	= {{ $g_dlv_add_fee }};
var g_free_dlv_fee_limit	= {{ $g_free_dlv_fee_limit }};

var prds	= new Object;

function SetRefundAccount(refund_amt)
{
	var ff	= document.f1;

	if( ff.isrefund_bank.value == "N" )
	{
		if( (ff.pay_type.value  & 2)== 2 )
		{
			if( unComma(ff.pay_amt.value) == refund_amt )
			{
				ff.refund_bank.value	= '카드취소';
				ff.refund_account.value	= '카드취소';
			}
			else
			{
				ff.refund_bank.value	= '카드부분취소';
				ff.refund_account.value	= '카드부분취소';
			}
		}
		else if( (ff.pay_type.value  & 16) == 16 )
		{
			if( unComma(ff.pay_amt.value) == refund_amt )
			{
				ff.refund_account.value	= '계좌이체취소';
			}
			else
			{
				ff.refund_account.value	= '계좌이체부분취소';
			}
		}
	}
}

function GetDlvAmt(ord_opt_no)
{
	var dlv_amt	= g_dlv_fee;

	if( prds[ord_opt_no]["com_dlv_policy"] == "C" )
	{
		dlv_amt	= prds[ord_opt_no]["com_dlv_amt"];
	}

	/*
	if( document.f1.IS_DLV_ADD.checked )
	{
		dlv_amt += g_dlv_add_fee;
	}
	*/

	return dlv_amt;
}

function ActOpt(opt)
{
	var ff	= document.f1;
	var order_opt_no	= opt.value;

	ff['DLV_TYPE_' + order_opt_no].disabled		= !opt.checked;
	ff['DLV_AMT_' + order_opt_no].disabled		= !opt.checked;
	ff['DLV_RET_AMT_' + order_opt_no].disabled	= !opt.checked;
	ff['DLV_ADD_AMT_' + order_opt_no].disabled	= !opt.checked;
	ff['DLV_ENC_AMT_' + order_opt_no].disabled	= !opt.checked;
	ff['DLV_PAY_AMT_' + order_opt_no].disabled	= !opt.checked;
}

function CalcDlvRetAmt(obj)
{
	var ff		= document.f1;
	var name	= obj.name;
	var ord_opt_no	= name.substr(name.lastIndexOf("_")+1);

	if( obj.value == "B" )
	{
		ff["DLV_RET_AMT_" + ord_opt_no].value = Comma(GetDlvAmt(ord_opt_no));
	}
	else
	{
		ff["DLV_RET_AMT_" + ord_opt_no].value = 0;
	}

	CalcDlvAddAmt(ord_opt_no);
	Calc();
}

function CalcDlvAddAmt(ord_opt_no)
{
	var ff	= document.f1;
	var dlv_add_amt	= 0;

	if( ff["prds"] && prds[ord_opt_no]["dlv_grp_amt"] == 0 )
	{
		// 그룹의 남은 건 중 선택된 추가배송비가 없다면
		// 그룹의 남은 (판매가격 * 수량 - 쿠폰) 금액이 g_free_dlv_fee_limit 크다면 0 아니면 배송비
		if( ff["prds"].length )
		{
			var opts  = ff["prds"];
		}
		else
		{
			var opts	= new Array();
			opts.push(ff["prds"]);
		}

		var price_amt	= 0;
		var add_amt		= 0;

		for( var i = 0; i < opts.length; i++ )
		{
			if( prds[ord_opt_no]["com_id"] == prds[opts[i].value]["com_id"] )
			{
				// 판매금액
				if( opts[i].checked != true )
				{
					price_amt += ( prds[opts[i].value]["price"] * prds[opts[i].value]["qty"] - prds[opts[i].value]["coupon_amt"] );
				}
				else {
				}

				// 추가배송비
				if( ord_opt_no != opts[i].value )
				{
					add_amt += unComma(ff["DLV_ADD_AMT_" + opts[i].value].value);
				}
			}
		}

		if( add_amt > 0 || price_amt >= g_free_dlv_fee_limit )
		{
		}
		else {
			dlv_add_amt = GetDlvAmt(ord_opt_no);
		}


	} else {
	}

	ff["DLV_ADD_AMT_" + ord_opt_no].value = dlv_add_amt;

	//CalcDlvEncAmt(ord_opt_no);
}

function CalcDlvEncAmt(ord_opt_no)
{
	var ff	= document.f1;
	var dlv_enc_amt	= unComma(ff["DLV_RET_AMT_" + ord_opt_no].value) + unComma(ff["DLV_ADD_AMT_" + ord_opt_no].value);

	var obj	= ff["DLV_ENC_AMT_" + ord_opt_no];
}

function Calc()
{
	var ff = document.f1;

	var refund_price	= 0;
	var refund_coupon	= 0;
	var refund_gift		= 0;
	var dlv_amt			= 0;
	var dlv_ret_amt		= 0;
	var dlv_add_amt		= 0;
	var dlv_enc_amt		= 0;
	var dlv_pay_amt		= 0;

	// 판매금액
	if(ff["prds"]){
		if(ff["prds"].length){
			var opts  = ff["prds"];
		} else {
			var opts = new Array();
			opts.push(ff["prds"]);
		}
		var opt_nos = "";

		for(var i=0;i<opts.length;i++){

			var order_opt_no = opts[i].value;

			if(opts[i].checked){

				var amt_opt = prds[order_opt_no].price * prds[order_opt_no].qty;

				// coupon_amt = dc_amt + coupon_amt
				var coupon_opt = prds[order_opt_no].coupon_amt;

				// 배송비
				//alert(getSelectBox(ff['DLV_AMT_' + order_opt_no]));
				//alert(ff['DLV_AMT_' + order_opt_no].value);
				var dlv_amt_opt = unComma(ff['DLV_AMT_' + order_opt_no].value);
				var dlv_ret_amt_opt = unComma(ff['DLV_RET_AMT_' + order_opt_no].value);
				var dlv_add_amt_opt = unComma(ff['DLV_ADD_AMT_' + order_opt_no].value);
				var dlv_enc_amt_opt = unComma(ff['DLV_ENC_AMT_' + order_opt_no].value);
				var dlv_pay_amt_opt = unComma(ff['DLV_PAY_AMT_' + order_opt_no].value);

				var ref_amt_opt = amt_opt - coupon_opt + dlv_amt_opt - dlv_ret_amt_opt - dlv_add_amt_opt + dlv_enc_amt_opt + dlv_pay_amt_opt;

				refund_price += amt_opt;
				refund_coupon += coupon_opt;
				dlv_amt += dlv_amt_opt;
				dlv_ret_amt += dlv_ret_amt_opt;
				dlv_add_amt += dlv_add_amt_opt;
				dlv_enc_amt += dlv_enc_amt_opt;
				dlv_pay_amt += dlv_pay_amt_opt;

				CalcDlvEncAmt(order_opt_no);	// 동봉금액
				ff['REF_AMT_' + order_opt_no].value	= Comma(ref_amt_opt);

			} else {

				// 값 표시
				//ff['DLV_AMT_' + order_opt_no].value		= 0;
				//ff['DLV_RET_AMT_' + order_opt_no].value	= 0;
				//ff['DLV_ADD_AMT_' + order_opt_no].value	= 0;
				//ff['DLV_ENC_AMT_' + order_opt_no].value	= 0;
				ff['REF_AMT_' + order_opt_no].value		= 0;

			}
		}
	}

	// 포인트
	//var refund_point = unComma(getRadioValue(ff.refund_point));
	var refund_point = unComma(ff.refund_point.value);

	// 기타 금액
	var refund_etc_gubun = getRadioValue(ff.refund_etc_gubun);
	var refund_etc = unComma(ff.refund_etc.value);
	if(refund_etc_gubun == 'p'){
		refund_etc = refund_etc * -1
	}

	// 사은품 금액
	if(ff["GIFTS"]){
		if(ff["GIFTS"].length){
			var gifts  = ff["GIFTS"];
		} else {
			var gifts = new Array();
			gifts.push(ff["GIFTS"]);
		}

		for(var i=0;i<gifts.length;i++){

			var order_gift_no = gifts[i].value;
			var gift_kind = ff['GIFTS_KIND_' + order_gift_no].value;
			var gift_apply_amt = parseInt(ff['GIFTS_APPLY_AMT_' + order_gift_no].value);

			if(gifts[i].checked){
				// 사은품 환불금액
				refund_gift += unComma(ff['GIFTS_REF_AMT_' + order_gift_no].value);
			} else {
				ff['GIFTS_REF_AMT_' + order_gift_no].value = Comma(ff['OLD_GIFTS_REF_AMT_' + order_gift_no].value);
				//ff['GIFTS_REF_AMT_' + order_gift_no].value = 0;
			}
		}
	}

	// 환불금액 계산
	var refund_amt = refund_price - refund_coupon + dlv_amt - dlv_ret_amt - dlv_add_amt + dlv_enc_amt + dlv_pay_amt - refund_point - refund_etc - refund_gift;

	// 값 대입
	ff.refund_dlv_amt.value		= Comma(dlv_amt);
	ff.refund_dlv_ret_amt.value	= Comma(dlv_ret_amt + dlv_add_amt);
	ff.refund_dlv_enc_amt.value	= Comma(dlv_enc_amt);
	ff.refund_dlv_pay_amt.value	= Comma(dlv_pay_amt);

	ff.refund_price.value	= Comma(refund_price);
	ff.refund_coupon.value	= Comma(refund_coupon);
	ff.refund_gift.value	= Comma(refund_gift);
	ff.refund_amt.value		= Comma(refund_amt);

	// 환불 계좌 처리
	if(refund_amt > 0){
		SetRefundAccount(refund_amt);
	}
}


function CheckGift(prd_obj, ord_opt_no)
{
	var ff	= document.f1;
	var total_pay_amt	= unComma(ff.pay_amt.value);
	var tmp_gift_apply_amt	= 0;

	// 사은품 금액
	// 사은품 쪽 오류 작업 수정해야함 21-07-15 ceduce
	if(ff["GIFTS"])
	{
		if( ff["GIFTS"].length )
		{
			var gifts	= ff["GIFTS"];
		}
		else
		{
			var gifts	= new Array();
			gifts.push(ff["GIFTS"]);
		}

		var tmp_gift_apply_amt	= 0;							//임시 사은품 구매가격
		var total_pay_amt		= unComma(ff.pay_amt.value);	// 결제금액
		var refund_amt			= unComma(ff.refund_amt.value) - unComma(ff.refund_gift.value);	// 사은품 금액을 뺀 환불금액

		for( var i = 0; i < gifts.length; i++ )
		{
			var order_gift_no	= gifts[i].value;
			var gift_kind		= ff['GIFTS_KIND_' + order_gift_no].value;
			var gift_apply_amt	= parseInt(ff['GIFTS_APPLY_AMT_' + order_gift_no].value);
			var gift_ord_opt_no	= parseInt(ff['GIFTS_ORD_OPT_NO_' + order_gift_no].value);

			// 환불될 사은품 자동 체크
			if( gift_kind == "W" )
			{	// 구매가격
				gift_apply_amt += tmp_gift_apply_amt;
				if( (total_pay_amt - refund_amt) < gift_apply_amt )
				{
					gifts[i].checked	= true;
				}
				else
				{
					gifts[i].checked	= false;
					tmp_gift_apply_amt	= gift_apply_amt;
				}
			}
			else if( gift_kind == "P" )
			{	// 상품별
				if( ord_opt_no == gift_ord_opt_no )
				{
					gifts[i].checked = prd_obj.checked;
				}
			}

			ActGift(gifts[i]);
		}
	}

	Calc();
}

</script>

<div class="container-fluid show_layout py-3">
	<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
		<div>
			<h3 class="d-inline-flex">환불금액 계산</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 주문</span>
				<span>/ 환불금액 계산</span>
			</div>
		</div>
		<div>
			<a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
		</div>
	</div>
	<div class="card_wrap aco_card_wrap">

	<form name="f1">

		<input type="hidden" name="cmd"				value="save">
		<input type="hidden" name="ord_no"			value="{{ $ord_no }}">
		<input type="hidden" name="ord_opt_no"		value="{{ $ord_opt_no }}">
		<input type="hidden" name="refund_no"		value="{{ $refund_no }}">
		<input type="hidden" name="ord_amt"			value="{{ $ord->ord_amt }}">
		<input type="hidden" name="pay_amt"			value="{{ $ord->pay_amt }}">
		<input type="hidden" name="refunded_amt"		value="@if(!empty($refund->refunded_amt)){{ $refund->refunded_amt }}@endif">
		<input type="hidden" name="pay_baesong"		value="{{ $ord->pay_baesong }}">
		<input type="hidden" name="pay_point"		value="{{ $ord->pay_point }}">
		<input type="hidden" name="coupon_amt"		value="{{ $ord->coupon_amt }}">
		<input type="hidden" name="pay_type"		value="{{ $ord->pay_type }}">
		<input type="hidden" name="tno"				value="{{ $ord->tno }}">
		<input type="hidden" name="isrefund_bank"   value="{{ $isrefund_bank }}">
		<input type="hidden" name="pgcancelstate"	value="{{ $pgcancelstate }}">
		<input type="hidden" name="opt_nos"			value="">
		<input type="hidden" name="order_gift_nos"	value="">


		<div class="card shadow">
			<div class="card-header mb-0">
				<a href="#" class="m-0 font-weight-bold">결제정보</a>
			</div>
			<div class="card-body">
				<div class="row_wrap">
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table class="table table-bordered th_border_none">
									<thead>
										<tr>
											<th>주문금액</th>
											<th>배송비</th>
											<th>할인(쿠폰포함)</th>
											<th>포인트</th>
											<th>결제수수료</th>
											<th>결제방법</th>
											<th style="display:{{$escw_show}}">결제구분</th>
											<th>거래번호</th>
											<th>결제금액</th>
											<th>환불금액</th>
											<th>잔액</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="text-right">{{@number_format($ord->ord_amt)}}</td>
											<td class="text-right">{{@number_format($ord->pay_baesong)}}</td>
											<td class="text-right">{{@number_format($ord->dc_amt)}}</td>
											<td class="text-right">{{@number_format($ord->pay_point)}}</td>
											<td class="text-right">{{@number_format($ord->pay_fee)}}</td>
											<td>{{@$ord->pay_name}}</td>
											<td style="display:{{$escw_show}}" style="background-color:skyblue;">에스크로</td>
											<td>
												@if ($p_ord_opt_no != "" && $ord->tno == "" && (($ord->pay_type & 2) == 2 || $ord->pay_type & 16) == 16) )
													<a href="#" id="search_sbtn" class="btn btn-sm btn-primary shadow-sm">부모거래번호 복사</a>
												@else
													{{@$ord->tno}}
												@endif
											</td>
											<td class="text-right">{{@number_format($ord->pay_amt)}}</td>
											<td class="text-right">{{@number_format($refunded_amt)}}</td>
											<td class="text-right">{{number_format($ord->pay_amt - $refunded_amt)}}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card shadow">
			<div class="card-header mb-0">
				<a href="#" class="m-0 font-weight-bold">주문정보</a>
			</div>
			<div class="card-body">
				<div class="row_wrap">
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table class="table table-bordered th_border_none">
									<thead>
										<tr>
											<th rowspan="2"> </th>
											<th rowspan="2">상태</th>
											<th rowspan="2">업체</th>
											<th rowspan="2">상품명</th>
											<th rowspan="2">옵션</th>
											<th rowspan="2">수량</th>
											<th rowspan="2">판매가</th>
											<th rowspan="2">할인</th>
											<th rowspan="2">+결제수수료</th>
											<th rowspan="2">+배송비</th>
											<th colspan="4">환불배송비</th>
											<th rowspan="2">환불액</th>
										</tr>
										<tr>
											<th>-반품</th>
											<th>-추가</th>
											<th>+동봉</th>
											<th>+입금</th>
										</tr>
									</thead>
									<tbody>
										@if (count($prds) > 0)
											@foreach($prds as $prd)

												<script><!--

												var pd = new Object();
												pd["com_id"]			= "{{$prd['com_id']}}";
												pd["qty"]				= {{ str_replace(",", "", $prd['qty']) }};
												pd["price"]				= {{ str_replace(",", "", $prd['price']) }};
												pd["coupon_amt"]		= {{ str_replace(",", "", $prd['coupon_amt']) }};
												pd["dlv_amt"]			= {{ str_replace(",", "", $prd['dlv_amt']) }};
												pd["dlv_grp_cnt"]		= "{{ $prd['dlv_grp_cnt'] }}";
												pd["dlv_grp_amt"]		= {{ str_replace(",", "", $prd['dlv_grp_amt']) }};
												pd["dlv_grp_add_amt"]	= {{ str_replace(",", "", $prd['dlv_grp_add_amt']) }};
												pd["ref_amt"]			= {{ str_replace(",", "", $prd['ref_amt']) }};
												pd["com_dlv_policy"]	= "{{ $prd['com_dlv_policy'] }}";
												pd["com_dlv_amt"]		= {{ str_replace(",", "", $prd['com_dlv_amt']) }};
												prds["{{ $prd['ord_opt_no'] }}"] = pd;

												//--></script>

												<tr @if ($prd['ord_opt_no'] == $ord_opt_no) class="checked-goods" @endif id="prd-top-{{$prd['ord_opt_no']}}">
													<td rowspan="2">
													@if( $prd['ord_state'] >= 5 and ( $prd['refund_no'] == "0" or $prd['refund_no'] == $refund_no ) )
														<input
															type="checkbox"
															name="prds"
															@if( $prd['refund_no'] != "0" and $prd['refund_no'] == $refund_no )
																checked
															@endif
															@if( $prd['ord_opt_no'] == $ord_opt_no )
																readonly
															@endif
															value="{{$prd['ord_opt_no']}}"
															data-price="{{$prd['price']}}"

															onclick="ActOpt(this); Calc(); CheckGift(this, '{{ $prd['ord_opt_no'] }}');"
														/>
													@endif

													@if($prd['refund_no'] != 0)
														{{@$prd['refund_no']}}
													@endif
													</td>
													<td rowspan="2">{{$prd['state']}}</td>
													<td rowspan="2">{{$prd['com_nm']}}</td>
													<td rowspan="2">{{$prd['goods_nm']}}</td>
													<td rowspan="2">{{$prd['opt_nm']}}</td>
													<td rowspan="2">{{$prd['qty']}}</td>
													<td rowspan="2">{{number_format($prd['price'])}}</td>
													<td rowspan="2">{{number_format($prd['dc_amt'] + $prd['coupon_amt'])}}</td>
													<td rowspan="2">{{number_format($prd['pay_fee'])}}</td>

													@if( $prd['dlv_grp_cnt'] != "" )
													<td style="text-align:right" rowspan="{{ $prd['dlv_grp_cnt'] * 2 }}">
														{{ $prd['dlv_amt'] }}
														@if( $prd['dlv_amt'] == 0 )
															<input type="hidden" name="DLV_AMT_{{ $prd['ord_opt_no'] }}" value="0">
														@else
															<br>
															<select name="DLV_AMT_{{ $prd['ord_opt_no'] }}" onchange="Calc(); CheckGift();" class="form-control form-control-sm">
																<option value="0">0</option>
																<option value="{{ $prd['dlv_amt'] }}"
																	@if( $prd['ord_state'] <= 10 or $prd['dlv_amt'] == $prd['clm_dlv_amt'] )
																		selected
																	@endif
																>{{ $prd['dlv_amt'] }}</option>
															</select>
														@endif
													</td>
													@else
														<input type="hidden" name="DLV_AMT_{{ $prd['ord_opt_no'] }}" value="0">
													@endif

													<td colspan="4" style="padding:5px 10px 5px 10px;">
														<div style="display:flex; vertical-align:middle">
															<select
																name="DLV_TYPE_{{ $prd['ord_opt_no'] }}"
																data-opt-no="{{$prd['ord_opt_no']}}"
																class="form-control form-control-sm dlv-type"
																style="width:25%"
																onchange="return CalcDlvRetAmt(this);"
															>
																<option value="">선택</option>
																<option value="B">착불</option>
																<option value="P">선불</option>
															</select>
															<span class="mx-2" style="line-height:30px;">택배</span>
															<input type="text" name="DLV_CM_{{ $prd['ord_opt_no'] }}" value="{{$prd['clm_dlv_cm']}}" class="form-control form-control-sm">
														</div>
													</td>
													<td rowspan="2">
														<input
															type="text"
															name="REF_AMT_{{ $prd['ord_opt_no'] }}"
															class="form-control form-control-sm number-input"

															data-price="{{$prd['price']}}"
															data-dc="{{$prd['dc_amt']}}"
															data-coupon="{{$prd['coupon_amt']}}"
															data-qty="{{$prd['qty']}}"

															value="{{ $prd['ref_amt'] }}"
															disabled
														>
													</td>
												</tr>
												<tr @if ($prd['ord_opt_no'] == $ord_opt_no) class="checked-goods" @endif id="prd-bt-{{$prd['ord_opt_no']}}" >
													<td style="padding:5px 10px 5px 10px;">
														<input
															type="text"
															name="DLV_RET_AMT_{{ $prd['ord_opt_no'] }}"
															class="form-control form-control-sm number-input"
															value="{{ @number_format($prd['clm_dlv_ret_amt']) }}"
															onkeypress="currency(this);" onkeyup="com(this);Calc(); CheckGift();"
														>
													</td>
													<td style="padding:5px 10px 5px 10px;">
														<input
															type="text"
															name="DLV_ADD_AMT_{{ $prd['ord_opt_no'] }}"
															class="form-control form-control-sm number-input"
															value="{{ @number_format($prd['clm_dlv_add_amt']) }}"
															onkeypress="currency(this);" onkeyup="com(this);Calc(); CheckGift();"
														/>
													</td>
													<td style="padding:5px 10px 5px 10px;">
														<input
															type="text"
															name="DLV_ENC_AMT_{{ $prd['ord_opt_no'] }}"
															class="form-control form-control-sm number-input"
															value="{{ @number_format($prd['clm_dlv_enc_amt']) }}"
															onkeypress="currency(this);" onkeyup="com(this);Calc(); CheckGift();"
														/>
													</td>
													<td style="padding:5px 10px 5px 10px;">
														<input
															type="text"
															name="DLV_PAY_AMT_{{ $prd['ord_opt_no'] }}"
															class="form-control form-control-sm number-input"
															value="{{ @number_format($prd['clm_dlv_pay_amt']) }}"
															onkeypress="currency(this);" onkeyup="com(this);Calc(); CheckGift();"
														/>
													</td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		@if (count($gifts) > 0)
		<div class="card shadow">
			<div class="card-header mb-0">
				<a href="#" class="m-0 font-weight-bold">사은품 정보</a>
			</div>
			<div class="card-body">
				<div class="row_wrap">
					<div class="row">
						<div class="col-12">
							<div class="table-responsive">
								<table class="table table-bordered th_border_none">
									<thead>
										<tr>
											<th></th>
											<th>이미지</th>
											<th>사은품명</th>
											<th>분류</th>
											<th>증정구분</th>
											<th>지급여부</th>
											<th>환불여부</th>
											<th>환불일시</th>
											<th>사은품금액</th>
										</tr>
									</thead>
									<tbody>
										@foreach($gifts as $gift)
										<tr>
											<td>
												<input
													type="checkbox"
													name="gift"
													value="{{$gift['order_gift_no']}}"
													@if($gift['refund_no'] != "0" && $gift['refund_no'] == $refund_no) checked @endif
													data-kind="{{$gift['kind']}}"
													data-apply="{{$gift['apply_amt']}}"
													data-opt-no="{{$gift['ord_opt_no']}}"
												/>
												<br>
												{{@$gift['refund_no']}}
											</td>
											<td>
												<img src="{{config('shop.image_svr')}}/{{@$gift['img']}}" alt="img" style="width:50px">
											</td>
											<td>{{@$gift['name']}}</td>
											<td>{{@$gift['type_val']}}</td>
											<td>
												{{@$gift['kind_val']}}

												@if($gift['kind'] === 'W')
													({{number_format($gift['apply_amt'])}}이상)
												@elseif($gift['kind'] === 'P')
													(<a href="#">{{$gift['goods_snm']}}</a>)
												@endif
											</td>
											<td>{{@$gift['give_yn'] === 'Y' ? '지급' : '미지급'}}</td>
											<td>
												@if ($gift['refund_yn'] === 'Y')
													환불완료
												@elseif($gift['refund_no'] != 0)
													환불대기
												@else
													-
												@endif
											</td>
											<td>{{@$gift['refund_date']}}</td>
											<td>
												{{@$gift['gift_price']}}원<br>
												<input type="hidden" name="old_gifts_ref_amt_{{$gift['order_gift_no']}}" value="{{$gift['refund_amt']}}" />
												<input
													type="text"
													name="gifts_ref_amt_{{$gift['order_gift_no']}}"
													id="gifts-ref-amt-{{$gift['order_gift_no']}}"
													value="{{number_format($gift['refund_amt'])}}"
													class="input-num text-right"
													@if ($gift['g_refund_yn'] === 'N' || $gift['give_yn'] === 'N') disabled @endif
												/>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
		<div class="card shadow">
			<div class="card-header mb-0">
				<a href="#" class="m-0 font-weight-bold">환불 정보</a>
			</div>
			<div class="card-body">
				<div class="row_wrap">
					<div class="row">
						<div class="col-12">
							<div class="table-box-ty2 mobile">
								<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
									<colgroup>
										<col width="100px">
										<col width="40%">
										<col width="100px">
										<col width="40%">
									</colgroup>
									<tr>
										<th>판매가 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_price" class="form-control form-control-sm text-right" value="{{@number_format($refund->refund_price)}}" readonly style="background-color:#CCCCCC;"> </div>
												<span>원</span>
											</div>
										</td>
										<th>입금 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_dlv_pay_amt" class="form-control form-control-sm text-right" value="{{ @$refund->refund_dlv_pay_amt }}" readonly style="background-color:#CCCCCC;"> </div>
												<span>원</span>
											</div>
										</td>
									</tr>
									<tr>
										<th>배송비 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_dlv_amt" class="form-control form-control-sm text-right" value="{{ @$refund->refund_dlv_amt }}" readonly style="background-color:#CCCCCC;"> </div>
												<span>원</span>
											</div>
										</td>
										<th>동봉 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_dlv_enc_amt" class="form-control form-control-sm text-right" value="{{ @$refund->refund_dlv_enc_amt }}" readonly style="background-color:#CCCCCC;"> </div>
												<span>원</span>
											</div>
										</td>
									</tr>
									<tr>
										<th>환불배송비 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_dlv_ret_amt" class="form-control form-control-sm text-right" value="{{ @$refund->refund_dlv_ret_amt }}" readonly style="background-color:#CCCCCC;"> </div>
												<span>원</span>
											</div>
										</td>
										<th>환불 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_amt" id="refund-amt" class="form-control form-control-sm text-right" value="{{@number_format($refund->refund_amt)}}" readonly style="background-color:#CCCCCC;"> </div>
												<span class="mr-1">원</span>
												<a href="#" id="search_sbtn" class="btn btn-sm btn-secondary shadow-sm save-btn fs-12">환불액저장</a>
											</div>
										</td>
									</tr>
									<tr>
										<th>포인트 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<span>{{@$refund->refund_point_amt ? $refund->refund_point_amt : 0}}원 (포인트는 수기환원 해야 합니다.)</span>
												<input type="hidden" name="refund_point" value="@if( $ord->pay_type == 4 )0@else{{ $refund->refund_point_amt }}@endif">
											</div>
										</td>
										<th>결제 수수료 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" id="refund-pay-fee" class="form-control form-control-sm text-right" value="{{@number_format($refund->refund_pay_fee)}}" disabled> </div>
												<span class="mr-1">원</span>
												<div class="custom-control custom-checkbox form-check-box">
													<input type="checkbox" name="refund-pay-fee-yn" id="refund-pay-fee-yn" class="custom-control-input" value="Y" @if(@$refund->refund_pay_fee_yn != 'N') checked @endif />
													<label class="custom-control-label" for="refund-pay-fee-yn">환불</label>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th>할인 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_coupon" class="form-control form-control-sm text-right" value="{{@number_format($refund->refund_coupon_amt)}}" readonly style="background-color:#CCCCCC;"> </div>
												<span>원</span>
											</div>
										</td>
										<th>은행 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_bank" id="refund-bank" class="form-control form-control-sm" value="{{ @$refund->refund_bank }}" @if( $isrefund_bank == "N" ) readonly style="background-color:#CCCCCC;" @endif></div>
												<span class="mr-1"></span>
												<div class="custom-control custom-checkbox form-check-box">
													<input type="checkbox" name="is_refund_bank" id="is-refund-bank" class="custom-control-input" value="Y" />
													<label class="custom-control-label" for="is-refund-bank">계좌입력</label>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th>기타 ±</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_etc" class="form-control form-control-sm text-right" value="{{ @$refund->refund_etc_amt }}" onKeyPress="currency(this);" onKeyup="com(this);Calc(); CheckGift();"> </div>
												<span>원</span>
											</div>
											<div class="form-inline form-radio-box">
												<div class="custom-control custom-radio">
													<input type="radio" name="refund_etc_gubun" value="m" id="minus" class="custom-control-input"
														@if( @$refund->refund_etc_amt >= 0 )
															checked
														@endif
														onclick="Calc(); CheckGift();"
													/>
													<label class="custom-control-label" for="minus">차감</label>
												</div>
												<div class="custom-control custom-radio">
													<input type="radio" name="refund_etc_gubun" value="p" id="plus" class="custom-control-input"
														@if( @$refund->refund_etc_amt < 0 )
															checked
														@endif
														onclick="Calc(); CheckGift();"
													/>
													<label class="custom-control-label" for="plus">추가</label>
												</div>
											</div>
										</td>
										<th>계좌</th>
										<td>
											<div class="form-inline inline_input_box">
												<div class="form-inline inline_input_box">
													<input
														type="text"
														name="refund_account"
														id="refund-account"
														class="form-control form-control-sm"
														value="{{ @$refund->refund_account }}"
														@if( $isrefund_bank == "N" ) readonly style="background-color:#CCCCCC;" @endif
													/>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<th>사은품 +</th>
										<td>
											<div class="form-inline inline_input_box">
												<div><input type="text" name="refund_gift" class="form-control form-control-sm text-right" value="{{number_format(@$refund->refund_gift_amt)}}" readonly style="background-color:#CCCCCC;" /> </div>
												<span>원</span>
											</div>
										</td>
										<th>예금주</th>
										<td>
											<div class="form-inline inline_input_box">
												<input type="text" name="refund_nm" id="refund-nm" class="form-control form-control-sm" value="{{ $ord->pay_nm }}" @if( $isrefund_bank == "N" ) readonly style="background-color:#CCCCCC;" @endif>
											</div>
										</td>
									</tr>
									<tr>
										<th>메모</th>
										<td colspan="3">
											<div class="form-inline inline_input_box">
												@if(($ord->pay_type & 2) == 2 && $pgcancelstate != 2)
													<span style="color:red;">카드(부분취소불가)</span>
												@elseif(($ord->pay_type & 16) == 16 && $isrefund_bank == "Y")
													<span style="color:red;">계좌이체(환불계좌 필요)</span>
												@endif
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</form>

	</div>
</div>



<script>
const ord_opt_no	= '{{$ord_opt_no}}';
const pay_type		= '{{$ord->pay_type}}';

$('#is-refund-bank').change(function(){
	$('#refund-bank, #refund-account, #refund-nm').attr('disabled', !this.checked);

	if(this.checked){
		const refund_amt = $('#refund-amt').val().replace(/[\,]/gi, '');

		if((pay_type  & 2)== 2){
			if(pay_amt == refund_amt){
				$('#refund-bank').val('카드취소');
				$('#refund-account').val('카드취소');
			} else {
				$('#refund-bank').val('카드부분취소');
				$('#refund-account').val('카드부분취소');
			}
		} else if((pay_type  & 16)== 16){
			if(pay_amt == refund_amt){
				$('#refund-account').val('계좌이체취소');
			} else {
				$('#refund-account').val('계좌이체부분취소');
			}
		}
	}
});

$('.save-btn').click(function(e){

	var ff	= document.f1;
	var msg	= "";

	var pay_amt		= unComma(ff.pay_amt.value);
	var refund_amt	= unComma(ff.refund_amt.value);

	// 부분취소 불가건
	if( (ff.pay_type.value & 2) == 2 && ff.pgcancelstate.value != 2 && pay_amt != refund_amt)
	{
		if(! ff.is_refund_bank.checked)
		{
			alert('부분취소가 불가능한 카드결제건입니다.\n\n환불금액을 저장하려면 \'계좌입력\' 항목을 체크하신 후\n\n은행, 계좌, 예금주를 입력하십시오.');
			return false;
		}
		else
		{
			// 부분취소 불가 카드 결제 건
			if( ff.refund_bank.value == "" || ff.refund_bank.value == "카드부분취소" )
			{
				alert("은행을 입력해 주십시오.");
				ff.refund_bank.select();
				ff.refund_bank.focus();
				return false
			}

			if( ff.refund_account.value == "" || ff.refund_account.value == "카드부분취소" )
			{
				alert("계좌를 입력해 주십시오.");
				ff.refund_account.select();
				ff.refund_account.focus();
				return false
			}

			if( ff.refund_nm.value == "" )
			{
				alert("예금주를 입력해 주십시오.");
				ff.refund_nm.focus();
				return false
			}

		}
	}

	if( ff.isrefund_bank.value == "Y" )
	{
		if( ff.refund_bank.value == "" )
		{
			alert("은행을 입력해 주십시오.");
			ff.refund_bank.focus();
			return false
		}

		if( ff.refund_account.value == "" )
		{
			alert("계좌를 입력해 주십시오.");
			ff.refund_account.focus();
			return false
		}
		if( ff.refund_nm.value == "" )
		{
			alert("예금주를 입력해 주십시오.");
			ff.refund_nm.focus();
			return false
		}
	}

	if( refund_amt == "0" )
	{
		if(! confirm("환불액이 0원 입니다.\n\n환불액을 정확히 입력하셨습니까?")){
			return false;
		}
	}

	if( ff["prds"].length )
	{
		var opts  = ff["prds"];
	}
	else
	{
		var opts = new Array();
		opts.push(ff["prds"]);
	}

	var opt_nos = "";

	for( var i = 0; i < opts.length; i++ )
	{
		var order_opt_no	= opts[i].value;

		if( opts[i].checked )
		{
			if( opt_nos != "" )
			{
				opt_nos += ",";
			}
			opt_nos += order_opt_no + "=y";
		}
		else
		{
			if( opt_nos != "" )
			{
				opt_nos += ",";
			}
			opt_nos += order_opt_no + "=n";
		}

	}

	ff.opt_nos.value = opt_nos;

	//사은품 정보 얻기
	if( ff["gifts"] )
	{
		if( ff["gifts"].length )
		{
			var gifts  = ff["gifts"];
		}
		else
		{
			var gifts	= new Array();
			gifts.push(ff["gifts"]);
		}

		var order_gift_nos	= "";

		for( var i = 0; i < gifts.length; i++ )
		{
			var order_gift_no	= gifts[i].value;

			if( gifts[i].checked )
			{
				if( order_gift_nos != "" )
				{
					order_gift_nos += ",";
				}
				order_gift_nos += order_gift_no + "=y";
			}
			else
			{
				if( order_gift_nos != "" )
				 {
					order_gift_nos += ",";
				}
				order_gift_nos += order_gift_no + "=n";
			}
		}

		ff.order_gift_nos.value = order_gift_nos;
	}

    $('[name=prds]').each(function(){
        const id = this.value;

		if (this.checked)
		{
			$(`input[name="DLV_RET_AMT_${id}"]`).val($(`input[name="DLV_RET_AMT_${id}"]`).val().replace(/[\,]/gi, ''));
			$(`input[name="DLV_ADD_AMT_${id}"]`).val($(`input[name="DLV_ADD_AMT_${id}"]`).val().replace(/[\,]/gi, ''));
			$(`input[name="DLV_ENC_AMT_${id}"]`).val($(`input[name="DLV_ENC_AMT_${id}"]`).val().replace(/[\,]/gi, ''));
			$(`input[name="DLV_PAY_AMT_${id}"]`).val($(`input[name="DLV_PAY_AMT_${id}"]`).val().replace(/[\,]/gi, ''));
			$(`input[name="REF_AMT_${id}"]`).val($(`input[name="REF_AMT_${id}"]`).val().replace(/[\,]/gi, ''));
		}
    });

    $('[name=gitfs]').each(function(){
		if (this.checked) {
			$(`#gifts-ref-amt-${this.value}`).val($(`#gifts-ref-amt-${this.value}`).val().replace(/[\,]/gi, ''));
		}
    });

	$('input[name="refund_price"]').val($('input[name="refund_price"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_dlv_amt"]').val($('input[name="refund_dlv_amt"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_dlv_ret_amt"]').val($('input[name="refund_dlv_ret_amt"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_dlv_enc_amt"]').val($('input[name="refund_dlv_enc_amt"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_dlv_pay_amt"]').val($('input[name="refund_dlv_pay_amt"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_point"]').val($('input[name="refund_point"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_coupon"]').val($('input[name="refund_coupon"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_etc"]').val($('input[name="refund_etc"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_amt"]').val($('input[name="refund_amt"]').val().replace(/[\,]/gi, ''));
	$('input[name="refund_gift"]').val($('input[name="refund_gift"]').val().replace(/[\,]/gi, ''));

	var frm = $('form[name="f1"]');

	//console.log(frm.serialize());
	//return;

	$.ajax({
		async: true,
		type: 'put',
		url: '/head/order/ord01/refund-save/' + ord_opt_no,
		data: frm.serialize(),
		success: function (data) {
			alert("저장되었습니다.");
			cbSave();
		},
		error: function(request, status, error) {
			console.log("error")
		}
	});

});


function cbSave()
{
	var ff	= document.f1;

	var refund_amt	= unComma(ff.refund_amt.value);

	if( refund_amt > 0 )
	{
		var refund_price	= ff.refund_price.value;

		var refund_dlv		= unComma(ff.refund_dlv_ret_amt.value) - unComma(ff.refund_dlv_enc_amt.value) - unComma(ff.refund_dlv_pay_amt.value) - unComma(ff.refund_dlv_amt.value);
		//var refund_point	= getRadioValue(ff.refund_point);
		var refund_point	= unComma(ff.refund_point.value);

		msg = "환불액 (" + ff.refund_amt.value + ") = ";
		msg += " 판매가 (" + refund_price + ") ";
		msg += " - 배송비 (" + Comma(refund_dlv) + ") ";
		msg += " - 포인트 (" + refund_point + ") ";
		msg += " - 쿠폰 (" + ff.refund_coupon.value + ") ";
		msg += " - 사은품 (" + ff.refund_gift.value + ") ";
		msg += " - 기타 (" + ff.refund_etc.value + ") ";

		opener.SetRefund(
			ff.refund_amt.value,
			ff.refund_bank.value,
			ff.refund_account.value,
			ff.refund_nm.value,
			msg
		);
	} else {
	}

	self.close();
}

</script>






{{--
<script>
const ord_opt_no = '{{$ord_opt_no}}';
const refund_no = '{{$refund_no}}';
const pay_type = '{{$ord->pay_type}}';
const pay_amt = '{{$ord->pay_amt}}';

const getRowInputsId = (id) => {
	return [
	   `#dlv-type-${id}`,
	   `#dlv-cm-${id}`,
	   `#dlv-ret-amt-${id}`,
	   `#dlv-add-amt-${id}`,
	   `#dlv-enc-amt-${id}`,
	   `#dlv-pay-amt-${id}`
	];
}

const goodsDisabled = (id, checked) => {
	getRowInputsId(id).forEach((target) => {
		$(target).attr('disabled', !checked);
	});
}

const totalRefundCalculate = () => {
	const total = {
		price : 0,
		rAmt : 0,
		aAmt : 0,
		eAmt : 0,
		pAmt : 0,
		refAmt : 0,
		gift_amt : 0
	};

	$('.goods-list tbody [type="checkbox"]:checked').each(function() {
		const id = this.value;

		total.price += $(this).attr('data-price') * 1;
		total.rAmt += $(`#dlv-ret-amt-${id}`).val().replace(/,/gi, '') * 1;
		total.aAmt += $(`#dlv-add-amt-${id}`).val().replace(/,/gi, '') * 1;
		total.eAmt += $(`#dlv-enc-amt-${id}`).val().replace(/,/gi, '') * 1;
		total.pAmt += $(`#dlv-pay-amt-${id}`).val().replace(/,/gi, '') * 1;
		total.refAmt += $(`#ref-amt-${id}`).val().replace(/,/gi, '') * 1;
	});

	$('[name=gitfs]:checked').each(function(){
		total.gift_amt += $(`#gifts-ref-amt-${this.value}`).val().replace(/,/gi, '') * 1
	});

	$('#refund-price').val(numberFormat(total.price));
	$('#refund-r-amt').val(numberFormat(total.rAmt));
	$('#refund-a-amt').val(numberFormat(total.aAmt));
	$('#refund-e-amt').val(numberFormat(total.eAmt));
	$('#refund-p-amt').val(numberFormat(total.pAmt));

	$('#refund-gift-amt').val(numberFormat(total.gift_amt));

	let etc = $('#refund-etc-amt').val().replace(/,/gi, '') * 1;

	const checked_id = $('[name=etc]:checked').attr('id');

	if (checked_id === 'minus') etc = etc * -1;

	$('#refund-amt').val(numberFormat(total.refAmt + etc));
}

const refundCalculate = (id) => {
	const target =$(`#ref-amt-${id}`);

	const price = (target.attr('data-price') * target.attr('data-qty')) - (target.attr('data-dc') + target.attr('data-coupon'));

	const rAmt = $(`#dlv-ret-amt-${id}`).val().replace(/,/gi, '') * 1;
	const aAmt = $(`#dlv-add-amt-${id}`).val().replace(/,/gi, '') * 1;
	const eAmt = $(`#dlv-enc-amt-${id}`).val().replace(/,/gi, '') * 1;
	const pAmt = $(`#dlv-pay-amt-${id}`).val().replace(/,/gi, '') * 1;

	let refAmt = (price + eAmt + pAmt) - (rAmt + aAmt);

	if (refAmt < 0) {
		refAmt = numberFormat(refAmt * -1);
		target.val('-'+refAmt);
	} else {
		target.val(numberFormat(refAmt));
	}

	totalRefundCalculate();
}

const validate = () => {
	const isRefundBank = $('#is-refund-bank:checked').val();

	if(isRefundBank == "Y" ) {
		if($('#refund-bank').val() == ""){
			alert("은행을 입력해 주십시오.");
			$('#refund-bank').focus();
			return false
		}

		if($('#refund-account').val() == ""){
			alert("계좌를 입력해 주십시오.");
			$('#refund-account').focus();
			return false
		}

		if($('#refund-nm').val() == ""){
			alert("예금주를 입력해 주십시오.");
			$('#refund-nm').focus();
			return false
		}
	}
	return true;
}

//초기에 check되어있지 않은 상품은 입력 불가하게 만듬
$('.goods-list tbody input[type="checkbox"]').each(function(){
	const id = this.value;
	const inputSelector = `#dlv-ret-amt-${id}, #dlv-add-amt-${id}, #dlv-enc-amt-${id}, #dlv-pay-amt-${id}`;

	$(inputSelector).keyup(function(){
		this.value = numberFormat(this.value.replace(/,/gi, ''));
		refundCalculate(this.id.split('-')[3]);
	});

	goodsDisabled(this.value, this.checked);
});

$('#refund-etc-amt').keyup(function(){
	this.value = numberFormat(this.value.replace(/,/gi, ''));
	totalRefundCalculate();
});

$('[name=etc]').change(totalRefundCalculate);

//check가 true일경우 입력가능
$('.goods-list tbody input[type="checkbox"]').change(function(){
	goodsDisabled(this.value, this.checked);
	refundCalculate(this.value);
});

$('.dlv-type').change(function(){
	const id = $(this).attr('data-opt-no');

	if (this.value === 'B')
		$(`#dlv-ret-amt-${id}`).val(numberFormat(2500));
	else
		$(`#dlv-ret-amt-${id}`).val(0);

	refundCalculate(id);
});

$('[name=gitfs]').change(function(){
	$(`#gifts-ref-amt-${this.value}`).attr('dsabled', !this.checked);
});

$(function(){
	refundCalculate(ord_opt_no);
});

</script>
--}}





<style>
.checked-goods td{
	background:yellow;
}
.number-input {
	text-align:right;
}
</style>
@stop
