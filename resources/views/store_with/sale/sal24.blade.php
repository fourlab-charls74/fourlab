@extends('store_with.layouts.layout')
@section('title','일별 매출 통계')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">일별 매출 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 경영관리</span>
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
                            <label for="formrow-firstname-input">판매기간</label>
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
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
									@foreach ($store_channel as $sc)
										<option value='{{ $sc->store_channel_cd }}' @if(@$p_store_channel === $sc->store_channel_cd) selected @endif>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" @if(@$p_store_kind == '') disabled @endif>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}' @if(@$p_store_kind === $sk->store_kind_cd) selected @endif>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품검색조건</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 sch-prdcd-range" readonly style="background-color: #fff;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>매장명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 27px;"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sell_type">판매유형</label>
                            <div class="flax_box">
                                <select id="sell_type" name="sell_type[]" class="form-control form-control-sm multi_select w-100" multiple>
                                    <option value=''>전체</option>
                                    @foreach ($sale_kinds as $sale_kind)
                                    <option value='{{ $sale_kind->code_id }}' @if(in_array($sale_kind->code_id, $sell_type_ids)) selected @endif>{{ $sale_kind->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="pr_code">판매처수수료</label>
                            <div class="flax_box">
                                <select id="pr_code" name="pr_code[]" class="form-control form-control-sm multi_select w-100" multiple>
                                    <option value=''>전체</option>
                                    @foreach ($pr_codes as $pr_code)
                                    <option value='{{ $pr_code->code_id }}' @if(in_array($pr_code->code_id, $pr_code_ids)) selected @endif>{{ $pr_code->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td" hidden>
                        <div class="form-group">
                            <label for="">온라인/오프라인</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="on_off_yn" id="on_off_all" value="" class="custom-control-input" @if($on_off_yn == '') checked @endif>
                                    <label class="custom-control-label" for="on_off_all" value="">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="on_off_yn" id="on_off_on" value="ON" class="custom-control-input" @if($on_off_yn == 'ON') checked @endif>
                                    <label class="custom-control-label" for="on_off_on" value="ON">온라인</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="on_off_yn" id="on_off_off" value="OFF" class="custom-control-input" @if($on_off_yn == 'OFF') checked @endif>
                                    <label class="custom-control-label" for="on_off_off" value="OFF">오프라인</label>
                                </div>
                            </div>
                        </div>
                    </div>
<!--					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-inputState">브랜드</label>
							<div class="form-inline inline_btn_box">
								<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
								<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 26px;"></i></a>
							</div>
						</div>
					</div>-->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id='goods_nm' name='goods_nm' value='{{ @$goods_nm }}'>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-4 inner-td" hidden>
						<div class="form-group">
							<label for="formrow-email-input">매출시점</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="ord_state" id="ord_state10" value="10" class="custom-control-input" @if($ord_state == '10') checked @endif>
									<label class="custom-control-label" for="ord_state10" value="10">출고요청</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="ord_state" id="ord_state30" value="30" class="custom-control-input" @if($ord_state == '30' or $ord_state == '') checked @endif>
									<label class="custom-control-label" for="ord_state30" value="30">출고완료</label>
								</div>
							</div>
						</div>
					</div>
                </div>
                <div class="search-area-ext d-none row">
<!--                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputCity">품목</label>
                            <div class="flax_box">
                                <select id='item' name='item' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($items as $t)
                                    <option value='{{ $t->cd }}' @if($item == $t->cd) selected @endif>{{ $t->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>-->
                    
<!--					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">온라인판매처</label>
							<div class="flax_box">
								<select name='sale_place' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($sale_places as $sale_place)
										<option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>-->
                </div>
                <div class="search-area-ext d-none row">
<!--                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>주문구분</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[0]" id="ord_type_5" value="5" @if(in_array('5', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_5">교환</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[1]" id="ord_type_4" value="4" @if(in_array('4', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_4">예약</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[2]" id="ord_type_3" value="3" @if(in_array('3', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_3">특별주문</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[3]" id="ord_type_13" value="13" @if(in_array('13', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_13">도매주문</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[4]" id="ord_type_12" value="12" @if(in_array('12', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_12">서비스</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[5]" id="ord_type_17" value="17" @if(in_array('17', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_17">기관납품</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[7]" id="ord_type_15" value="15" @if(count($ord_type) < 1 || in_array('15', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_15">정상</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[6]" id="ord_type_14" value="14" @if(count($ord_type) < 1 || in_array('14', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_14">수기</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ord_type[8]" id="ord_type_16" value="16" @if(count($ord_type) < 1 || in_array('16', $ord_type)) checked @endif>
                                    <label class="custom-control-label" for="ord_type_16">오픈마켓</label>
                                </div>
                            </div>
                        </div>
                    </div>-->
<!--                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">결제방법</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[0]" id="stat_pay_type_16" value="16" @if(in_array('16', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_16">계좌이체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[1]" id="stat_pay_type_32" value="32" @if(in_array('32', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_32">핸드폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[2]" id="stat_pay_type_1" value="1" @if(in_array('1', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_1">현금</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[3]" id="stat_pay_type_2" value="2" @if(in_array('2', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_2">카드</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[4]" id="stat_pay_type_4" value="4" @if(in_array('4', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_4">포인트</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[5]" id="stat_pay_type_8" value="8" @if(in_array('8', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_8">쿠폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[6]" id="stat_pay_type_64" value="64" @if(in_array('64', $stat_pay_type)) checked @endif>
                                    <label class="custom-control-label" for="stat_pay_type_64">가상계좌</label>
                                </div>
                            </div>
                        </div>
                    </div>-->
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
<div class="card shadow mb-1">
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
			<li>매출액 = 판매결제금액 - 교환결제금액 - 환불결제금액</li>
			<li>할인 = 현재가(판매시점) - 결제금액</li>
			<li>부가세 = 매출액 - ( 매출액 / 1.1 )</li>
            <li>매출원가 = 실제판매원가</li>
        </ul>
    </div>
</div>
<script src="https://unpkg.com/ag-charts-community@8.0.6/dist/ag-charts-community.min.js"></script>
<script language="javascript">
    const columns = [
		{headerName: "일자", field: "date", width: 100, cellClass: 'hd-grid-code', pinned: 'left', aggSum: "합계", aggAvg: "평균",
            cellRenderer:function(params) {
				if(params.value === '합계' || params.value === '평균') return params.value;
				let form_data = $('form[name="search"]').serialize();
				return `<a href="/store/order/ord01?${form_data}&date=${params.data.date || ''}" target="_blank">${params.value}</a>`;
            }
        },
        {headerName: '매출액구분',
            children: [
				{headerName: "수량", field: "sum_qty", type: 'numberType', aggregation: true},
                {headerName: "적립금", field: "sum_point", type: 'currencyType', aggregation: true},
                {headerName: "할인", field: "sum_dc", type: 'currencyType', aggregation: true, width: 80},
                {headerName: "쿠폰", field: "sum_coupon", type: 'currencyType', aggregation: true},
                // {headerName: "수수료", field: "sum_fee", type: 'currencyType', aggregation: true},
                {headerName: "결제금액", field: "sum_recv", type: 'currencyType', aggregation: true},
                // {headerName: "과세", field: "sum_taxation", type: 'currencyType', aggregation: true},
                // {headerName: "비과세", field: "sum_taxfree", type: 'currencyType', aggregation: true},
            ]
        },
        {headerName: "부가세", field: "vat", type: 'currencyType', aggregation: true, width:80},
        {headerName: "매출액(VAT별도)", field: "sum_amt", type: 'currencyType', aggregation: true},
        {headerName: "매출원가", field: "sum_wonga", type: 'currencyType', aggregation: true},
        {headerName: "마진율(%)", field: "margin", type: 'percentType', 
	        valueGetter: function(params) {
                if (params.data.date === "합계" || params.data.date === "평균") {
                    const data = params.data;
                    return (parseInt(data.sum_amt) - parseInt(data.sum_wonga)) / parseInt(data.sum_amt) * 100;
                }
                return params.data.margin;
            }
        },
        // {
        //     headerName: '매출이익',
        //     children: [{headerName: "세전", field: "margin1",type: 'currencyType', aggregation: true},
        //         {headerName: "세후", field: "margin2",type: 'currencyType', aggregation: true},
        //     ]
        // },
        // {
        //     headerName: '비용',
        //     children: [
        //         {headerName: "PG수수료", field: "exp_pg_fee",type: 'currencyType', aggregation: true},
        //         {headerName: "적립금", field: "exp_point",type: 'currencyType', aggregation: true},
        //         {headerName: "광고비", field: "exp_ad",type: 'currencyType', aggregation: true},
        //         {headerName: "소계", field: "exp_sum",type: 'currencyType', aggregation: true},
        //     ]
        // },
        // {headerName: "이익율(%)", field: "biz_margin", type: 'percentType', aggregation: true},
        // {
        //     headerName: '이익',
        //     children: [
        //         {headerName: "세전", field: "biz_profit", type: 'currencyType', aggregation: true, width:80},
        //         {headerName: "세후", field: "biz_profit_after", type: 'currencyType', aggregation: true, width:80},
        //     ]
        // },
        {headerName: '판매',
            children: [
				{headerName: "수량", field: "qty_30", type: 'numberType', aggregation: true},
                // {headerName: "적립금", field: "point_amt_30", type: 'currencyType', aggregation: true},
                // {headerName: "할인", field: "dc_amt_30", type: 'currencyType', aggregation: true},
                // {headerName: "쿠폰", field: "coupon_amt_30", type: 'currencyType', aggregation: true},
                // {headerName: "수수료", field: "fee_amt_30", type: 'currencyType', aggregation: true},
				{headerName: "결제금액(VAT별도)", field: "recv_amt_30", type: 'currencyType', aggregation: true},
				{headerName: "원가", field: "wonga_30", width:100, type: 'currencyType', aggregation: true},
            ]
        },
        {headerName: '교환',
            children: [
				{headerName: "수량", field: "qty_60", type: 'numberType', aggregation: true},
                // {headerName: "적립금", field: "point_amt_60", type: 'currencyType', aggregation: true},
                // {headerName: "할인", field: "dc_amt_60", type: 'currencyType', aggregation: true},
                // {headerName: "쿠폰", field: "coupon_amt_60", type: 'currencyType', aggregation: true},
                // {headerName: "수수료", field: "fee_amt_60", type: 'currencyType', aggregation: true},
                {headerName: "결제금액(VAT별도)", field: "recv_amt_60", type: 'currencyType', aggregation: true},
				{headerName: "원가", field: "wonga_60", width:100, type: 'currencyType', aggregation: true},
            ]
        },
        {
            headerName: '환불',
            children: [
				{headerName: "수량", field: "qty_61", type: 'numberType', aggregation: true},
                // {headerName: "적립금", field: "point_amt_61", type: 'currencyType', aggregation: true},
                // {headerName: "할인", field: "dc_amt_61", type: 'currencyType', aggregation: true},
                // {headerName: "쿠폰", field: "coupon_amt_61", type: 'currencyType', aggregation: true},
                // {headerName: "수수료", field: "fee_amt_61", type: 'currencyType', aggregation: true},
                {headerName: "결제금액(VAT별도)", field: "recv_amt_61", type: 'currencyType', aggregation: true},
				{headerName: "원가", field: "wonga_61", width:100, type: 'currencyType', aggregation: true},
            ]
        },
        {width: 0}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;
    let chart_data = null;

    $(document).ready(function() {
        pApp.ResizeGrid(300);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
			getRowStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return { 'background': '#eee' }
				}
			}
		});

        // 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
        load_store_channel();

		$("#date-tab").click(function() {
			$("#chart-type").val('date');
			drawCanvasByDate();
		});

		$("#yoil-tab").click(function() {
			$("#chart-type").val('yoil');
			drawCanvasByYoil();
		});

		initSearchTab();
		Search();
    });
	
	function initSearchTab() {
		let store_cd = "{{ @$store->store_cd }}";
		let store_nm = "{{ @$store->store_nm }}";
		let brand_cd = "{{ @$brand->brand }}";
		let brand_nm = "{{ @$brand->brand_nm }}";

		if (store_cd != '') {
			const option = new Option(store_nm, store_cd, true, true);
			$('#store_no').append(option).trigger('change');
		}
		
		if (brand_cd != '') {
			const option = new Option(brand_nm, brand_cd, true, true);
			$('#brand_cd').append(option).trigger('change');
		}
		
		let prd_cd_range = <?= json_encode(@$prd_cd_range) ?>;
		let prd_cd_range_nm = "{{ @$prd_cd_range_nm }}";		
		prd_cd_range = Object.keys(prd_cd_range).reduce((a, c) => {
			if (c.includes('_contain') || c === 'match') return a;
			return a + prd_cd_range[c].map(rg => '&' + c +'[]=' + rg).join('');
		}, '');
		
		$('#prd_cd_range').val(prd_cd_range);
		$('#prd_cd_range_nm').val(prd_cd_range_nm);
	}

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top", "avg": "top" });
        gx.Request('/store/sale/sal24/search', data, -1, function(data) {
            chart_data = data.body;
            drawCanvas();
        });
    }

    function drawCanvas() {
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
</script>

@stop
