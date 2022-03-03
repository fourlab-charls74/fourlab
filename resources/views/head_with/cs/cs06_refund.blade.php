@extends('head_with.layouts.layout-nav')
@section('title','환불')
@section('content')
<div class="container-fluid show_layout py-3">

	<div class="d-sm-flex align-items-center justify-content-between mb-2">
		<h1 class="h3 mb-0 text-gray-800">환불 - {{ $ord_no }}</h1>
		<div>
			<a href="#" id="search_sbtn" onclick="Search();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>

	
	<form name="detail">
	<input type="hidden" name="cmd" value="refundcmd">
	<input type="hidden" name="ord_no" value="{{ $ord_no }}">
	<input type="hidden" name="ord_opt_no" value="{{ $ord_opt_no }}">
	<input type="hidden" name="refund_no" value="{{ $refund_no }}">
	<input type="hidden" name="ord_amt" value="{{ $ord_amt }}">
	<input type="hidden" name="pay_amt" value="{{ $pay_amt }}">
	<input type="hidden" name="refund_amt" value="{{ $refund_amt }}">
	<input type="hidden" name="pay_type" value="{{ $pay_type }}">
	<input type="hidden" name="pay_type_nm" value="{{ $pay_name }}">
	<input type="hidden" name="tno" value="{{ $tno }}">

	<!-- 결제  정보 -->
    <div class="card_wrap mb-3">
        <div class="card shadow search_cum_form">
            <div class="card-header mb-0">
                <h5 class="m-0 font-weight-bold">결제 정보</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>주문금액</th>
                                <th>배송비</th>
                                <th>할인(쿠폰포함)</th>
                                <th>포인트</th>
                                <th>결제수수료</th>
                                <th>결제방법</th>
                                <th>거래번호</th>
                                <th>결제금액</th>
                                <th>환불금액</th>
                                <th>잔액</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
									<td>{{ $ord_amt }}</td>
									<td>{{ $pay_baesong }}</td>
									<td>{{ $dc_amt }}</td>
									<td>{{ $pay_point }}</td>
									<td>{{ $pay_fee }}</td>
									<td>{{ $pay_name }}</td>
									<td>{{ $tno }}</td>
									<td>{{ $pay_amt }}</td>

									<td>
										<input type="text" name="refunded_amt" value="{{ $refunded_amt }}" 
											class="input" style="text-align:right;width:70px;" onKeyPress="currency(this);" onKeyup="com(this);CalBalAmt();">
									</td>
									<td>
										<input type="text" name="bal_amt" value="{{ $bal_amt }}" 
											class="input" style="text-align:right;width:70px;" readonly>
									</td>

                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- 결제 정보 끝 -->


	<!-- 주문정보 -->
    <div class="card_wrap mb-3">
        <div class="card shadow search_cum_form">
            <div class="card-header mb-0">
                <h5 class="m-0 font-weight-bold">주문정보
				@if ($tno == "" && $p_ord_opt_no != "")
					<span style="margin:0;color:red;float:left;">* 부모주문의 결제정보를 확인해 주십시오.</span>
				@endif
				</h5>
            </div>
            <div class="card-body">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold"></h6>
						</div>
						<div class="fr_box flax_box">
							<input type="checkbox" name="is_dlv_add" value="Y" @if ($is_dlv_add == "Y")checked @endif>도서,산간 추가배송비
						</div>
					</div>
				</div>
카운트
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
								<th rowspan="2">&nbsp;</th>
                                <th rowspan="2" style="text-align:center;">상태</th>
                                <th rowspan="2" style="text-align:center;">업체</th>
                                <th rowspan="2" style="width:15%; text-align:center;">상품명</th>
                                <th rowspan="2" style="text-align:center;">옵션</th>
                                <th rowspan="2" style="width:5%; text-align:center;">수량</th>
                                <th rowspan="2" style="text-align:center;">판매가</th>
                                <th rowspan="2" style="text-align:center;">할인</th>
                                <th rowspan="2" style="text-align:center;">결제수수료</th>
								<th rowspan="2" style="text-align:center;">배송비</th>
                                <th colspan="3" style="text-align:center;">환불배송비</th>
                                <th rowspan="2" style="text-align:center;">환불액</th>
                            </tr>
							<tr>
								<th style="text-align:center">-반품</th>
								<th style="text-align:center">-추가</th>
								<th style="text-align:center">+동봉</th>
							</tr>
                        </thead>
                        <tbody>
						
							@for($i=0; $i<count($prds); $i++)
							<tr>
								<td rowspan="2">
									@if ($prds[$i]['ord_state'] >= 5 && ($prds[$i]['refund_no'] == "0" || $prds[$i]['refund_no'] == $refund_no))
									<input type="checkbox" name="PRDS" value=""
										@if ($prds[$i]['refund_no'] != "0" && $prds[$i]['refund_no'] == $refund_no)checked @endif>
									@else
									&nbsp;
									@endif
								</td>
								<td rowspan="2">{{ $prds[$i]['state'] }}</td>
								<td rowspan="2" style="text-align:left">{{ $prds[$i]['com_nm'] }}</td>
								<td rowspan="2" style="text-align:left"><span title="{{ $prds[$i]['goods_nm'] }}">{{ $prds[$i]['goods_snm'] }}</a></td>
								<td rowspan="2">{{ $prds[$i]['opt_nm'] }}</td>
								<td rowspan="2" style="text-align:right">{{ $prds[$i]['qty'] }}</td>
								<td rowspan="2" style="text-align:right">{{ $prds[$i]['price'] }}</td>
								<td rowspan="2" style="text-align:right">{{ $prds[$i]['coupon_amt'] }}</td>
								<td rowspan="2" style="text-align:right">{{ $prds[$i]['pay_fee'] }}..</td>
								@if ($prds[$i]['dlv_grp_cnt'] != "")
								<td style="text-align:right" rowspan="{{ ($prds[$i]['dlv_grp_cnt']*2) }}">
									{{ $prds[$i]['dlv_amt'] }}
								</td>
								@endif
								<td colspan="3" style="text-align:left">
									@if ( $prds[$i]['clm_dlv_type'] == "B" )
									착불
									@elseif ( $prds[$i]['clm_dlv_type'] == "P" )
									선불
									@endif, 택배 : {{ $prds[$i]['clm_dlv_cm'] }}
								</td>
								<td rowspan="2" style="text-align:right">
									{{ $prds[$i]['ref_amt'] }}
								</td>
							</tr>
							<tr>
								<td style="text-align:right">{{ $prds[$i]['clm_dlv_ret_amt'] }}</td>
								<td style="text-align:right">{{ $prds[$i]['clm_dlv_add_amt'] }}</td>
								<td style="text-align:right">{{ $prds[$i]['clm_dlv_enc_amt'] }}</td>
							</tr>
							@endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- 주문정보 끝 -->

	<!-- 환불정보 시작 -->
	<div class="card_wrap mb-3">
		<div class="card shadow">
			<div class="card-header mb-0">
				<h5 class="m-0 font-weight-bold">환불정보
					@if($refund_no == "")
					<span>* 환불금액을 다시 정확하게 계산해 주십시오.</span>
					@endif
				</h5>
			</div>
			<div class="card-body">

				<div class="row_wrap">
					@if ($res_cd != "" && $res_cd != "0000")
						<tr>
							<td width="15%" class="hdx">결제오류</td>
							<td class="bd" colspan="3" style="color:red">결제오류({{ $res_cd }}) - {{ $res_msg }}&nbsp;</td>
						</tr>
					@endif

					<!-- 판매가/클레임상태 -->
					<div class="row form-inline no-gutters">
						<!-- 판매가 -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">판매가</label>
							<div class="input_box">
								{{ $refund_price }} 원
							</div>
						</div>
						<!-- 클레임상태 -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">클레임상태</label>
							<div class="input_box">
								{{ $refund_clm_state_nm }}
							</div>
						</div>
					</div>
				</div>
				
				<div class="row_wrap">
					<!-- 결제수수료/동봉액 -->
					<div class="row form-inline no-gutters">
						<!-- 결제수수료 -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">결제수수료</label>
							<div class="input_box">
								{{ $refund_pay_fee }} 원
							</div>
						</div>
						<!-- 동봉액 -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">동봉액</label>
							<div class="input_box">
								{{ $refund_dlv_enc_amt }} 원
							</div>
						</div>
					</div>
				</div>


				<div class="row_wrap">
					<!-- 배송비/환불 -->
					<div class="row form-inline no-gutters">
						<!-- 배송비  -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">배송비</label>
							<div class="input_box">
								{{ $refund_dlv_amt }} 원
							</div>
						</div>
						<!-- 환불 -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">환불</label>
							<div class="input_box">
								@if ($refund_clm_state == 51)
									<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm refund-btn">{{ $refund_amt }} 원 환불</a>
								@else
									{{ $refund_amt }} 원
								@endif
							</div>
						</div>
					</div>
				</div>


				<div class="row_wrap">
					<!-- 환불배송비/은행 -->
					<div class="row form-inline no-gutters">
						<!-- 환불배송비   -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">환불배송비</label>
							<div class="input_box">
								{{ $refund_dlv_ret_amt }} 원
							</div>
						</div>
						<!-- 은행 -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">은행</label>
							<div class="input_box">
								{{ $refund_bank }}
							</div>
						</div>
					</div>
				</div>


				<div class="row_wrap">
					<!-- 포인트/계좌 -->
					<div class="row form-inline no-gutters">
						<!-- 포인트 -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">포인트</label>
							<div class="input_box">
								{{ $pay_point }} 원
							</div>
						</div>
						<!-- 계좌 -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">계좌</label>
							<div class="input_box">
								{{ $refund_account }}
							</div>
						</div>
					</div>
				</div>


				<div class="row_wrap">
					<!-- 할인/예금주 -->
					<div class="row form-inline no-gutters">
						<!-- 할인     -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">할인</label>
							<div class="input_box">
								{{ $refund_coupon_amt }} 원
							</div>
						</div>
						<!-- 예금주  -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">예금주 </label>
							<div class="input_box">
								{{ $refund_nm }}
							</div>
						</div>
					</div>
				</div>


				<div class="row_wrap">
					<!-- 기타/메모 -->
					<div class="row form-inline no-gutters">
						<!-- 할인     -->
						<div class="inline-inner-box ty2" style="width:50%;">
							<label for="">기타 </label>
							<div class="input_box">
								{{ $refund_etc_amt }} 원
							</div>
						</div>
						<!-- 메모 -->
						<div class="inline-inner-box ty" style="width:50%;">
							<label for="">메모 </label>
							<div class="input_box">
							{{ $memo }}
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- 환불정보 시작 -->

	</form>

</div>


<script>
	function CalBalAmt(){
		var ff = document.detail;

		if(ff.refunded_amt.value == ""){
			ff.refunded_amt.value = 0;
		}
		var refunded_amt = unComma(ff.refunded_amt.value);
	//	var bal_amt = unComma(ff.PAY_AMT.value) - refunded_amt;
		var bal_amt = unComma(ff.pay_amt.value);

		ff.refunded_amt.value = Comma(refunded_amt);
		ff.bal_amt.value = Comma(bal_amt);
	}

	function SetRefund(){
		
		if(confirm('환불하시겠습니까?')){
			
			var ff = document.detail;

			if(ff.tno.value == ""){
				alert('거래번호가 없어 환불처리를 하실 수 없습니다.');
				return false;
			}
			CheckRealBalanceAmt();
		}
	}

	function CheckRealBalanceAmt() {
		var ff = document.detail;
		var ord_no = ff.ord_no.value;
		var tno = ff.tno.value;
		var pay_amt = ff.pay_amt.value;
		var bal_amt = ff.bal_amt.value;
		
		$.ajax({
            async: true,
            type: 'put',
            url: '/head/cs/cs06/balanceamt',
			data: {
				'ord_no' : ord_no,
				'tno' : tno,
				'pay_amt' : pay_amt,
				'bal_amt': bal_amt
			},
            success: function (data) {
				console.log(data);
				console.log(data);
				if(data.result_code == 1){
					alert("환불처리된 내역이 있어 잔액이 일치하지 않습니다. \n다시 환불처리해 주십시오.");
					document.location.reload();
				}else{
					console.log("submit!!!");
					refundcmd();
					//document.detail.submit();
				}
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                    console.log("error")
                    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

	}

	function refundcmd(){
		//var ff = document.detail;
		var ff = $("[name=detail]").serialize();
		
		console.log("refundCmd!!!");
		$.ajax({
            async: true,
            type: 'put',
            url: '/head/cs/cs06/refundcmd',
			data: ff,
            success: function (data) {
				//console.log(data);
				//console.log(data);
				if(data.result_code == 1){
					location.reload();
				}else{
					alert(data.res_cd +"\n"+ res_msg);
				}
            },
            complete:function(){
                    _grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error")
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

	}


	function Comma(num){
        var len, point, str; 
        
        num = num + ""; 
        point = num.length % 3 ;
        len = num.length; 
    
        str = num.substring(0, point); 
        while (point < len) { 
            if (str != "") str += ","; 
            str += num.substring(point, point + 3); 
            point += 3; 
        } 
        
        return str;
    }

	//콤마풀기
	function unComma(str) {
		str = String(str);
		return str.replace(/[^\d]+/g, '');
	}

	$(function(){
		$(".refund-btn").click(function(e){
			e.preventDefault();

			SetRefund();
		});
		
	});
</script>

@stop

