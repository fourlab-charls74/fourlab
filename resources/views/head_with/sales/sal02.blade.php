@extends('head_with.layouts.layout')
@section('title','일별 매출 통계')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">일별 매출 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 일별 매출 통계</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">매출일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
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
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            {{-- <label for="name">주문구분</label>
                            <div class="flax_box">
                                <select name='ord_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($ord_types as $ord_type_o)
                                    <option value='{{ $ord_type_o->code_id }}' @if( $ord_type_o->code_id == $ord_type ) selected @endif>{{ $ord_type_o->code_val }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <label>주문구분</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[0]" id="ord_type_5" value="5" @if($ord_type == '' or in_array('5', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_5">교환</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[1]" id="ord_type_4" value="4" @if($ord_type == '' or in_array('4', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_4">예약</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[2]" id="ord_type_3" value="3" @if($ord_type == '' or in_array('3', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_3">특별주문</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[3]" id="ord_type_13" value="13" @if($ord_type == '' or in_array('13', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_13">도매주문</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[4]" id="ord_type_12" value="12" @if($ord_type == '' or in_array('12', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_12">서비스</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[5]" id="ord_type_17" value="17" @if($ord_type == '' or in_array('17', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_17">기관납품</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[6]" id="ord_type_14" value="14" @if($ord_type == '' or in_array('14', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_14">수기</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[7]" id="ord_type_15" value="15" @if($ord_type == '' or in_array('15', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_15">정상</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[8]" id="ord_type_16" value="16" @if($ord_type == '' or in_array('16', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_16">오픈마켓</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">매출시점</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="ord_state" id="ord_state10" value="10" class="custom-control-input" @if($ord_state == '10' or $ord_state == '') checked @endif>
                                    <label class="custom-control-label" for="ord_state10" value="10">출고요청</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="ord_state" id="ord_state30" value="30" class="custom-control-input" @if($ord_state == '30') checked @endif>
                                    <label class="custom-control-label" for="ord_state30" value="30">출고완료</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputCity">품목</label>
                            <div class="flax_box">
                                <select name='item' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($items as $t)
                                    <option value='{{ $t->cd }}' @if($item == $t->cd) selected @endif>{{ $t->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 26px;"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value='{{ $goods_nm }}'>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매유형</label>
							<div class="form-inline inline_select_box">
<!--								<div class="form-inline-inner input-box w-75">
									<div class="flax_box">
										<select name='sale_place' class="form-control form-control-sm" style="width: 95%">
											<option value=''>전체</option>
											@foreach ($sale_places as $sale_place)
												<option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
											@endforeach
										</select>
									</div>
								</div>-->
								<div class="form-inline-inner input-box w-25">
									<div class="form-inline form-check-box">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="mobile_yn" id="mobile_yn" value = "">
											<label class="custom-control-label" for="mobile_yn">모바일</label>
										</div>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="app_yn" id="app_yn" value = "">
											<label class="custom-control-label" for="app_yn">앱</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">업체</label>
							<div class="form-inline inline_select_box">
								<div class="form-inline-inner input-box w-25 pr-1">
									<select id="com_type" name="com_type" class="form-control form-control-sm w-100">
										<option value="">전체</option>
										@foreach ($com_types as $com_type)
											<option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-inline-inner input-box w-75">
									<div class="form-inline inline_btn_box">
										<input type="hidden" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%">
										<select id="com_cd" name="com_cd" class="form-control form-control-sm select2-company" style="width:100%;"></select>
										<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">결제방법</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[0]" id="stat_pay_type_16" value="16" @if($stat_pay_type != '' and in_array('16', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_16">계좌이체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[1]" id="stat_pay_type_32" value="32" @if($stat_pay_type != '' and in_array('32', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_32">핸드폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[2]" id="stat_pay_type_1" value="1" @if($stat_pay_type != '' and in_array('1', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_1">현금</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[3]" id="stat_pay_type_2" value="2" @if($stat_pay_type != '' and in_array('2', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_2">카드</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[4]" id="stat_pay_type_4" value="4" @if($stat_pay_type != '' and in_array('4', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_4">포인트</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[5]" id="stat_pay_type_8" value="8" @if($stat_pay_type != '' and in_array('8', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_8">쿠폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[6]" id="stat_pay_type_64" value="64" @if($stat_pay_type != '' and in_array('64', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_64">가상계좌</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- 차트 -->
<div class="card shadow mb-1" id="chart_area">
    <div class="card-body">
        <input type="hidden" id="chart-type" value="date">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="date-tab" data-toggle="tab" href="#home" role="tab" aria-controls="date" aria-selected="true">일별</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="yoil-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="yoil" aria-selected="false">요일별</a>
            </li>
        </ul>
        <div id="opt_chart" style="height: 100%; min-height:300px;"></div>
    </div>
</div>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" name="view_chart" id="view_chart" checked>
						<label class="custom-control-label" for="view_chart">차트보기</label>
					</div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="width:100%;min-height:600px;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="card-title">
            <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
        </div>
        <ul class="mb-0">
            <li>매출액 = 과세 + 비과세</li>
            <li>매출원가 = 실제판매원가</li>
            <li>부가세 = 과세 - ( 과세 / 1.1 )</li>
            <li>세전 매출이익 = 매출액 - 매출원가</li>
            <li>세후 매출이익 = 매출액 - 매출원가 - 부가세</li>
        </ul>
    </div>
</div>
<script src="https://unpkg.com/ag-charts-community@8.0.6/dist/ag-charts-community.min.js"></script>
<script language="javascript">
    const columns = [{
            headerName: "일자",
            field: "date",
            width: 100,
            cellClass: 'hd-grid-code',
            pinned: 'left',
            aggSum: "합계",
            aggAvg: "평균",
			cellRenderer: function(params) {
				if (params.value === '합계' || params.value === '평균') return params.value;
				let form_data = $('form[name="search"]').serialize();
				return `<a href="/head/order/ord01?${form_data}&date=${params.data.date || ''}" target="_blank">${params.value}</a>`;
			}
        },
        {
            headerName: '매출액구분',
            children: [{
                    headerName: "수량",
                    field: "sum_qty",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "sum_point",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "sum_dc",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "sum_coupon",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "sum_fee",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "sum_recv",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "과세",
                    field: "sum_taxation",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "비과세",
                    field: "sum_taxfree",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: "부가세",
            field: "vat",
            type: 'currencyType',
            aggregation: true
        },
        {
            headerName: "매출액",
            field: "sum_amt",
            type: 'currencyType',
            aggregation: true
        },
        {
            headerName: "매출원가",
            field: "sum_wonga",
            type: 'currencyType',
            aggregation: true
        },
        {
            headerName: "마진율(%)",
            field: "margin",
            type: 'percentType',
            valueGetter: function(params) {
                if (params.data.date === "합계" || params.data.date === "평균") {
                    const data = params.data;
                    return (1 - parseInt(data.sum_wonga) / (parseInt(data.sum_recv) + parseInt(data.sum_point) - parseInt(data.sum_fee))) * 100;
                }
                return params.data.margin;
            }
        },
        {
            headerName: '매출이익',
            children: [{
                    headerName: "세전",
                    field: "margin1",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "세후",
                    field: "margin2",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '비용',
            children: [
                {
                    headerName: "PG수수료",
                    field: "exp_pg_fee",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "exp_point",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "광고비",
                    field: "exp_ad",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "소계",
                    field: "exp_sum",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: "이익율(%)",
            field: "biz_margin",
            type: 'percentType',
            aggregation: true
        },
        {
            headerName: '이익',
            children: [
                {
                    headerName: "세전",
                    field: "biz_profit",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "세후",
                    field: "biz_profit_after",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '판매',
            children: [{
                    headerName: "수량",
                    field: "qty_30",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "point_amt_30",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "dc_amt_30",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "coupon_amt_30",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "fee_amt_30",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "recv_amt_30",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '교환',
            children: [{
                    headerName: "수량",
                    field: "qty_60",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "point_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "dc_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "coupon_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "fee_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "recv_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '환불',
            children: [{
                    headerName: "수량",
                    field: "qty_61",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "point_amt_61",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "dc_amt_61",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "coupon_amt_61",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "fee_amt_61",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "recv_amt_61",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            width: "auto"
        }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let chart_data = null;

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;
    $(document).ready(function() {
		let brand_cd = "{{ @$brand->brand }}";
		let brand_nm = "{{ @$brand->brand_nm }}";
		if (brand_cd != '') {
			const option = new Option(brand_nm, brand_cd, true, true);
			$('#brand_cd').append(option).trigger('change');
		}

        pApp.ResizeGrid(300);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            }
        };
        gx = new HDGrid(gridDiv, columns, options);
        Search();
    });

    function Search() {
	    if (!gx) return;
		if($('#mobile_yn').is(':checked')) {
			$('#mobile_yn').val('Y');
		}

		if($('#app_yn').is(':checked')) {
			$('#app_yn').val('Y');
		}
        let data = $('form[name="search"]').serialize();

        gx.Aggregation({
            "sum": "top",
            "avg": "top"
        });

        gx.Request('/head/sales/sal02/search', data, -1, function(data) {
            chart_data = data.body;
            drawCanvas();
        });
    }

    function drawCanvas() {
        // console.log($("#chart-type").val());
        switch ($("#chart-type").val()) {
            case "date":
                drawCanvasByDate();
                break;
            case "yoil":
                drawCanvasByYoil();
                break;
        }
    }

    function drawCanvasByDate() {
        $('#opt_chart').html('');

        let beforeRowMonth = null;

        chart_data.sort(function(a, b) {
            if (a.date < b.date) {
                return -1;
            }
            if (a.date > b.date) {
                return 1;
            }

            return 0;
        });

        chart_data.forEach(function(row) {
            if (beforeRowMonth !== row.month || beforeRowMonth === null) {
                // row.chart_x_str = row.month + "." + row.day;
				row.chart_x_str = row.day + "일";
                beforeRowMonth = row.month;
                return;
            }

            row.chart_x_str = row.day + '일';
        });

		const chart_options = {
			container: document.getElementById('opt_chart'),
			title: { text: "일별 매출 통계" },
			data: chart_data,
			theme: {
				palette: {
					fills: ['#556ee6', '#2797f6'],
					strokes: ['#556ee6', '#2797f6'],
				},
			},
			legend: { position: 'right' },
			series: [
				{ type: 'column', xKey: 'chart_x_str', yKey: 'sum_amt', yName: '매출액',
					tooltip: {
						renderer: (params) => ({ content: params.xValue + ': ' + Comma(params.yValue) + '원' })
					}
				},
				{ type: 'column', xKey: 'chart_x_str', yKey: 'sum_wonga', yName: '매출원가',
					tooltip: {
						renderer: (params) => ({ content: params.xValue + ': ' + Comma(params.yValue) + '원' })
					}
				},
			],
			axes: [
				{ type: 'category', position: 'bottom', },
				{ type: 'number', position: 'left',
					label: { formatter: (params) => Comma(params.value) },
					tick: { maxSpacing: 20 }
				},
			],
		};
        agCharts.AgChart.create(chart_options);
    }

    function drawCanvasByTime() {
        $('#opt_chart').html('차트를 생성할 수 없습니다.');
    }

    function drawCanvasByYoil() {
        $('#opt_chart').html('');

		let data = [
			{name: '일요일', sum_amt: 0, sum_wonga: 0, margin: 0},
			{name: '월요일', sum_amt: 0, sum_wonga: 0, margin: 0},
			{name: '화요일', sum_amt: 0, sum_wonga: 0, margin: 0},
			{name: '수요일', sum_amt: 0, sum_wonga: 0, margin: 0},
			{name: '목요일', sum_amt: 0, sum_wonga: 0, margin: 0},
			{name: '금요일', sum_amt: 0, sum_wonga: 0, margin: 0},
			{name: '토요일', sum_amt: 0, sum_wonga: 0, margin: 0}
		];

        chart_data.forEach(function(c_data) {
            data[c_data.yoil - 1].sum_amt += Number(c_data.sum_amt);
            data[c_data.yoil - 1].sum_wonga += Number(c_data.sum_wonga);
        });

		const chart_options = {
			container: document.getElementById('opt_chart'),
			title: { text: "요일별 매출 통계" },
			data: data,
			theme: {
				palette: {
					fills: ['#556ee6', '#2797f6'],
					strokes: ['#556ee6', '#2797f6'],
				},
			},
			legend: { position: 'right' },
			series: [
				{ type: 'column', xKey: 'name', yKey: 'sum_amt', yName: '매출액',
					tooltip: {
						renderer: (params) => ({ content: params.xValue + ': ' + Comma(params.yValue) + '원' })
					}
				},
				{ type: 'column', xKey: 'name', yKey: 'sum_wonga', yName: '매출원가',
					tooltip: {
						renderer: (params) => ({ content: params.xValue + ': ' + Comma(params.yValue) + '원' })
					}
				},
			],
			axes: [
				{ type: 'category', position: 'bottom' },
				{ type: 'number', position: 'left',
					label: { formatter: (params) => Comma(params.value) },
					tick: { maxSpacing: 20 }
				},
			],
		};
		agCharts.AgChart.create(chart_options);
    }
    $("#date-tab").click(function() {
        $("#chart-type").val('date');
        drawCanvasByDate();
    });

    $("#yoil-tab").click(function() {
        $("#chart-type").val('yoil');
        drawCanvasByYoil();
    });

	$('#view_chart').change(() => {
		$('#chart_area').toggle();
		drawCanvas();
	});
	
    @if( $pop_search == "Y" )
        Search();
    @endif
</script>

@stop
