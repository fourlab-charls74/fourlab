@extends('store_with.layouts.layout-nav')
@section('title','정산 - 상세')
@section('content')

<style>
	.custm_btn{
		height:30px;
		font-size:14px !important;
		letter-spacing:2px;
		color:#FFFFFF !important;
		padding:0px 15px 0px 15px;
	}	
</style>

<div class="show_layout py-3">
	<form method="post" name="search">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="d-flex justify-content-between">
					<h1 class="card-header">
						<a href="#">정산 - 상세 </a>
					</h1>
					<div class="flax_box">
						<a href="#" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1">검색</a>
						<a href="#" @if( $acc_idx == '')onclick="Closed();"@endif class="btn btn-sm btn-outline-primary shadow-sm mr-1 closed_btn" @if( $acc_idx != '') disabled style="background-color:#DBDBDB;" @endif>마감추가</a>
						<a href="#" onclick="gridDownload();" class="btn btn-sm btn-outline-primary shadow-sm">자료받기</a>
					</div>
				</div>
				<div class="card-body mt-1">
					<div class="row_wrap">

						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="15%">
											<col width="35%">
											<col width="15%">
											<col width="35%">
										</colgroup>
										<tbody>
											<tr>
												<th>정산일자</th>
												<td>
													<div class="form-inline date-select-inbox">
														<div class="docs-datepicker form-inline-inner input_box">
															<div class="input-group">
																<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
																<div class="input-group-append">
																	<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
																		<i class="fa fa-calendar" aria-hidden="true"></i>
																	</button>
																</div>
															</div>
															<div class="docs-datepicker-container"></div>
														</div>
														<span class="text_line">~</span>
														<div class="docs-datepicker form-inline-inner input_box">
															<div class="input-group">
																<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
																<div class="input-group-append">
																	<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
																		<i class="fa fa-calendar" aria-hidden="true"></i>
																	</button>
																</div>
															</div>
															<div class="docs-datepicker-container"></div>
														</div>
													</div>
												</td>
												<th>업체</th>
												<td>
													<div class="form-inline inline_select_box">
														<div class="form-inline-inner input-box w-100">
															<div class="form-inline inline_btn_box">
																<input type="hidden" id="com_cd" name="com_cd" value="{{ $com_id }}">
																<input type="text" id="com_nm" name="com_nm" value="{{ $com_nm }}" class="form-control form-control-sm ac-company sch-company" style="width:100%;">
																<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
															</div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>주문상태</th>
												<td>
													<div class="flax_box">
														<select name='ord_state' class="form-control form-control-sm">
															<option value=''>전체</option>
															@foreach ($ord_states as $ord_state)
																<option value='{{ $ord_state->code_id }}'>
																	{{ $ord_state->code_val }}
																</option>
															@endforeach
														</select>
													</div>
												</td>
												<th>클레임상태</th>
												<td>
													<div class="flax_box">
														<select name='clm_state' class="form-control form-control-sm">
															<option value=''>전체</option>
															@foreach ($clm_states as $clm_state)
																<option value='{{ $clm_state->code_id }}'>
																	{{ $clm_state->code_val }}
																</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>결제방법</th>
												<td>
													<div class="form-inline">
														<div class="form-inline-inner" style="width:100%;">
															<div class="form-group flax_box">
																<div style="width:calc(100% - 65px);">
																	<select name="stat_pay_type" class="form-control form-control-sm mr-2" style="width:100%;">
																		<option value="">전체</option>
																		@foreach ($stat_pay_types as $stat_pay_type)
																			<option value='{{ $stat_pay_type->code_id }}'>
																				{{ $stat_pay_type->code_val }}
																			</option>
																		@endforeach
																	</select>
																</div>
																<div style="height:30px;margin-left:5px;">
																	<div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="복합결제 제외">
																		<input type="checkbox" class="custom-control-input" id="not_complex" name="not_complex" value="Y">
																		<label for="not_complex" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</td>
												<th>주문구분</th>
												<td>
													<div class="flax_box">
														<select name='ord_type' class="form-control form-control-sm">
															<option value=''>전체</option>
															@foreach ($ord_types as $ord_type)
																<option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					
					</div>
				</div>

			</div>

		</div>

		<!-- DataTales Example -->
		<div class="card shadow last-card pt-2 pt-sm-0">
			<div class="card-body">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="height:calc(100vh - 280px);width:100%;" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>

	</form>

</div>

<div class="card shadow">
	<div class="card-body">
		<div class="card-title">
			<h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
		</div>
		<ul class="mb-0">
			<li>매출금액 = 판매금액 - 클레임금액 - 할인금액 - 쿠폰금액(업체부담) + 배송비 + 기타정산액</li>
			<li>판매수수료 = 수수료지정 : 판매가격 * 수수료율, 공급가지정 : 판매가격 - 공급가액</li>
			<li>수수료 = 판매수수료 - 할인금액</li>
			<li>정산금액 = 매출금액 - 수수료</li>
			<li>쿠폰금액(본사부담) = 판매촉진비 수수료 매출 신고</li>
			<li>카드수수료 등 수수료 부담의 주체가 귀사에 있으므로 입점업체의 경우 매출 신고 시에 해당 매출금액에 대하여 현금성으로 신고</li>
		</ul>
	</div>
</div>



<script language="javascript">
	var columns = [
		{field: "num",			headerName: "#", type:'NumType', pinned: 'left'},
		{field: "type",			headerName: "구분",			width:80, pinned: 'left'},
		{field: "state_date",	headerName: "일자",			width:80, pinned: 'left'},
		{field: "ord_no",		headerName: "주문번호",		width:130, pinned: 'left'},
		{field: "ord_opt_no",	headerName: "일련번호",		width:80, type:'HeadOrdOptNoType', pinned: 'left'},
		{field: "multi_order",	headerName: "복수",			width:70, 
			cellRenderer: function(params){
				if( params.value == "Y" ){
					return '<a href="#" onclick="return openHeadOrderOpt(\'' + params.data.ord_opt_no +'\');">'+ params.value +'</a>';
				}
			},
			cellStyle: function(params){
				return params.value === 'Y' ? {"background-color": "yellow"} : {};
			},
			pinned: 'left'
		},
		{field: "coupon_nm",	headerName: "쿠폰",			width:70, pinned: 'left'},
		{field: "goods_nm",		headerName: "상품명",		width:150, type:'HeadGoodsNameType'},
		{field: "opt_nm",		headerName: "옵션",			width:70},
		{field: "style_no",		headerName: "스타일넘버",	width:90},
		{field: "opt_type",		headerName: "출고형태",		width:90},
		{field: "com_nm",		headerName: "판매처",		width:80},
		{field: "user_nm",		headerName: "주문자",		width:80},
		{field: "pay_type",		headerName: "결제방법",		width:90},
		{field: "tax_yn",		headerName: "과세",			width:70},
		{field: "qty",			headerName: "수량",			width:70, type: 'currencyType'},
		{field: "sale_amt",		headerName: "판매금액",		width:90, type: 'currencyType'},
		{field: "clm_amt",		headerName: "클레임금액",	width:90, type: 'currencyType'},
		{field: "dc_apply_amt",	headerName: "할인금액",		width:90, type: 'currencyType'},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "coupon_com_amt",
					headerName: "(업체부담)",
					width:95,
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "dlv_amt",		headerName: "배송비",		width:80, type: 'currencyType'},
		{field: "fee_etc_amt",	headerName: "기타정산액",	width:90, type: 'currencyType'},
		{
			headerName: '매출금액',
			children: [{
					field: "sale_net_taxation_amt",
					headerName: "과세",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_taxfree_amt",
					headerName: "비과세",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_amt",
					headerName: "소계",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
			]
		},
		{field: "tax_amt",	headerName: "부가세",	type: 'currencyType',	hide:true},
		{
			headerName: '본사수수료',
			children: [{
					field: "fee_ratio",
					headerName: "수수료율(%)",
					width:90,
					cellStyle:{"text-align":"right"},
					aggregation: true
				},
				{
					field: "fee",
					headerName: "판매수수료",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_dc_amt",
					headerName: "할인금액",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_net_amt",
					headerName: "소계",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
			]
		},
		{field: "acc_amt",		headerName: "정산금액",		width:90, type: 'currencyType'},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "fee_allot_amt",
					headerName: "(본사부담)",
					width:95,
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "ord_state",	headerName: "주문상태",		width:90},
		{field: "clm_state",	headerName: "클레임상태",	width:90},
		{field: "ord_date",		headerName: "주문일",		width:80},
		{field: "dlv_end_date",	headerName: "배송완료일",	width:90},
		{field: "clm_end_date",	headerName: "클레임완료일",	width:90},
		{field: "bigo",			headerName: "비고",			width:120},
		{field: "goods_no",		headerName: "상품코드1", hide:true},
		{field: "goods_sub",	headerName: "상품코드2", hide:true},
		{field: "acc_type",		headerName: "정산구분", hide:true},
		{field: "err_notice",	headerName: "에러공지", hide:true},
	];

</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(325);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            
        };
		gx = new HDGrid(gridDiv, columns, options);
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc02/show-search', data,- 1);
	}

	function gridDownload() {
		gx.Download("정산상세내역.csv");
	}

	function Closed() {
		if(confirm('해당 내용을 마감내역에 추가 하시겠습니까?')){

			$.ajax({
				async: false,
				type: 'put',
				url: '/store/account/acc02/show',
				data: {
					com_id : $('input[name="com_cd"]').val(),
					sdate : $('input[name="sdate"]').val(),
					edate : $('input[name="edate"]').val(),
				},
				success: function(data) {
					cbClosed(data);
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		}
	}

	function cbClosed(data){
		/*
		999 : 알수 없는 에러
		000 : 성공
		100 : 부정확한 요청입니다.
		110 : 마감처리된 내역
		200 : 자료등록시 오류
		*/

		var results	= {
			"000":"마감내역을 추가하였습니다.",
			"100":"부정확한 요청입니다.",
			"110":"이미 마감처리된 내역입니다.",
			"200":"자료 등록 시 오류가 발생하였습니다. 다시 처리해 주십시오.",
			"999":"마감내역을 추가하였습니다."
		}

		var ret	= data.code;

		if( ret == "000" ){
			alert('마감내역을 추가하였습니다.');
			location.reload();
			opener.Search();
		} else {
			alert(results[ret]);
		}
	}
</script>



@stop