@extends('head_with.layouts.layout')
@section('title','월별 매출 통계')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">월별 매출 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 월별 매출 통계</span>
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
                                        <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
                                        <input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
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
                                    @foreach ($ord_types as $ord_type)
                                    <option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <label>주문구분</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[0]" id="ord_type_5" value="5" checked>
                                    <label class="custom-control-label" for="ord_type_5">교환</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[1]" id="ord_type_4" value="4" checked>
                                    <label class="custom-control-label" for="ord_type_4">예약</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[2]" id="ord_type_3" value="3" checked>
                                    <label class="custom-control-label" for="ord_type_3">특별주문</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[3]" id="ord_type_13" value="13" checked>
                                    <label class="custom-control-label" for="ord_type_13">도매주문</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[4]" id="ord_type_12" value="12" checked>
                                    <label class="custom-control-label" for="ord_type_12">서비스</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[5]" id="ord_type_17" value="17" checked>
                                    <label class="custom-control-label" for="ord_type_17">기관납품</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[6]" id="ord_type_14" value="14" checked>
                                    <label class="custom-control-label" for="ord_type_14">수기</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[7]" id="ord_type_15" value="15" checked>
                                    <label class="custom-control-label" for="ord_type_15">정상</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input ord_type" name="ord_type[8]" id="ord_type_16" value="16" checked>
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
                                    <input type="radio" name="ord_state" id="ord_state10" value="10" class="custom-control-input" checked />
                                    <label class="custom-control-label" for="ord_state10">출고요청</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="ord_state" id="ord_state30" value="30" class="custom-control-input" />
                                    <label class="custom-control-label" for="ord_state30">출고완료</label>
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
                                    @foreach ($items as $item)
                                    <option value='{{ $item->cd }}'>{{ $item->val }}</option>
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
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
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
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[0]" id="statCheck1" value="16">
                                    <label class="custom-control-label" for="statCheck1">계좌이체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[1]" id="statCheck2" value="32">
                                    <label class="custom-control-label" for="statCheck2">핸드폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[2]" id="statCheck3" value="1">
                                    <label class="custom-control-label" for="statCheck3">현금</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[3]" id="statCheck4" value="2">
                                    <label class="custom-control-label" for="statCheck4">카드</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[4]" id="statCheck5" value="4">
                                    <label class="custom-control-label" for="statCheck5">포인트</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[5]" id="statCheck6" value="8">
                                    <label class="custom-control-label" for="statCheck6">쿠폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input stat_pay_type" name="stat_pay_type[6]" id="statCheck7" value="64">
                                    <label class="custom-control-label" for="statCheck7">가상계좌</label>
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
				<a class="nav-link active" id="date-tab" data-toggle="tab" href="#home" role="tab" aria-controls="date" aria-selected="true">월별</a>
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
					<div class="fr_box flax_box">
						<div class="custom-control custom-checkbox form-check-box">
							<input type="checkbox" class="custom-control-input" name="view_chart" id="view_chart" checked>
							<label class="custom-control-label" for="view_chart">차트보기</label>
						</div>
					</div>
				</div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="width:100%;min-height:600px;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<div class="card shadow mb-0">
    <div class="card-body">
        <div class="card-title">
            <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
        </div>
        <ul class="mb-0">
            <li>매출액 = 과세 + 비과세</li>
            <li>매출원가 = 실제판매원가</li>
            <li>부가세 = 과세 - ( 과세 / 1.1 )</li>
            <li>세전 매출이익 = 매출액 - 매출원가</li>
            <li>세후 매출이익 = 매출액 - 매출원가 - 부가세 </li>
        </ul>
    </div>
</div>
<script src="https://unpkg.com/ag-charts-community@8.0.3/dist/ag-charts-community.min.js"></script>
<script language="javascript">
    var columns = [{
            headerName: "일자",
            field: "date",
            width: 120,
            cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value === '합계' || params.value === '평균') return params.value;
                let s_ord_type = $(".ord_type:checked").map(function() {return this.value;}).get().join(",");
                let s_ord_state = $('[name=ord_state]:checked').val();
                let s_item = $("[name=item]").val();
                let s_brand = $(".select2-brand").val()??'';
                let s_prd_nm = $("[name=goods_nm]").val();
                let s_stat_pay_type = $(".stat_pay_type:checked").map(function() {return this.value;}).get().join(",");
				return '<a href="/head/sales/sal02?sdate='+ params.data.date + '&ord_type=' + s_ord_type + '&ord_state='+ s_ord_state + '&item='+ s_item + '&brand='+ s_brand + '&goods_nm='+ s_prd_nm + '&stat_pay_type='+ s_stat_pay_type +'" target="_new">'+ params.value+'</a>';
			},
            pinned: 'left',
            aggSum: "합계",
            aggAvg: "평균"
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
                    field: "sum_point_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "sum_dc_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "sum_coupon_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "sum_fee_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "sum_recv_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "과세",
                    field: "sum_taxation_amt",
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
            headerName: "마진율",
            field: "margin",
            type: 'percentType',
            valueGetter: function(params) {
                if (params.data.date === "합계" || params.data.date === "평균") {
                    const data = params.data;
                    return (1 - parseInt(data.sum_wonga) / (parseInt(data.sum_recv_amt) + parseInt(data.sum_point_amt) - parseInt(data.sum_fee_amt))) * 100;
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
       /* {
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
        },*/
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
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
	let gx;

    $(document).ready(function() {
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
        let data = $('form[name="search"]').serialize();
		if($('#mobile_yn').is(':checked')) {
			$('#mobile_yn').val('Y');

		}

		if($('#app_yn').is(':checked')) {
			$('#app_yn').val('Y');

		}
		
        gx.Aggregation({
            "sum": "top",
            "avg": "top"
        });
		
		gx.Request('/head/sales/sal03/search', data, -1, function(data) {
			drawCanvasByMonth();
		});
    }


	function drawCanvasByMonth() {

		$.ajax({
			method: 'get',
			url: '/head/sales/sal03/chart-data/search',
			data: $('form[name="search"]').serialize(),
			success: function (res) {
				$('#opt_chart').html('');
				chart_data = res.chart_data;

				chart_data.forEach(function(element, index) {
					element.wonga = Number(element.wonga);
					element.swonga = Number(element.swonga);
					element.profit = Number(element.profit);
				});

				const chartOption = {
					container: document.getElementById('opt_chart'),
					autoSize: true,
					data: chart_data.sort(function(a, b) {
						if (a.month < b.month) {
							return -1;
						}
						if (a.month > b.month) {
							return 1;
						}

						return 0;
					}),
					theme: {
						palette: {
							fills: ['#c16068', '#a2bf8a', '#80a0c3'],
							strokes: ['#c16068', '#a2bf8a', '#80a0c3'],
						},
						overrides: {
							column: { series: { strokeWidth: 5 } },
							line: { series: { strokeWidth: 5, marker: { enabled: false } } },
						},
					},
					title: {
						text: '월별 매출 통계',
					},
					series: [
						{
							type: 'column',
							xKey: 'month',
							yKey: 'swonga',
							yName: '매출액',
						},
						{
							type: 'column',
							xKey: 'month',
							yKey: 'wonga',
							yName: '매출원가',
						},
						{
							type: 'line',
							xKey: 'month',
							yKey: 'profit',
							yName: '매출이익',
						},
					],
					legend: {
						item: {
							marker: {
								shape: 'square',
								strokeWidth: 0,
							},
						},
					},
					axes: [
						{ type: 'category', position: 'bottom' },
						{ type: 'number', position: 'left',
							label: { formatter: (params) => Comma(params.value) },
							tick: { maxSpacing: 30 }
						},
					],
				};

				var chart = agCharts.AgChart.create(chartOption);
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});
	}

	$('#view_chart').change(() => {
		$('#chart_area').toggle();
		//drawCanvasByMonth();
	});
</script>
@stop
