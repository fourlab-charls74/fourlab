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
                    <div class="col-lg-12 inner-td">
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

<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
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
        </ul>
    </div>
</div>
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
                let s_brand = $("[name=brand_cd]").val();
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

    $(document).ready(function() {
        pApp.ResizeGrid(300);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum": "top",
            "avg": "top"
        });
        gx.Request('/head/sales/sal03/search', data);
    }
</script>
@stop
