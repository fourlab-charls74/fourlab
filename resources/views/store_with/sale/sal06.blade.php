@extends('store_with.layouts.layout')
@section('title','매장상품별판매분석')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장상품별판매분석</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 경영관리</span>
		<span>/ 매장상품별판매분석</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="good_types">판매기간</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disabled>
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disabled>
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
										<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="">판매유형</label>
							<div class="flex_box">
								<select name='sale_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($sale_kinds as $sale_kind)
									<option value='{{ $sale_kind->code_id }}' @if (@$sale_kind_id == $sale_kind->code_id) selected @endif>{{ $sale_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="formrow-email-input">상품명</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_nm_eng">상품명(영문)</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="style_no">스타일넘버/바코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no">
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input-box" style="width:47%">
									<div class="form-inline-inner inline_btn_box">
										<input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
										<a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
                        </div>
					</div>
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
					</div>
					{{-- <div class="col-lg-4">
						<div class="form-group">
							<label for="sale_yn">매출여부</label>
							<div class="flex_box">
								<div class="form-inline form-radio-box">
									<div class="custom-control custom-radio">
										<input type="radio" name="sale_yn" id="sale_y" value="Y" class="custom-control-input" checked/>
										<label class="custom-control-label" for="sale_y">Y</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="sale_yn" id="sale_n" value="N" class="custom-control-input"/>
										<label class="custom-control-label" for="sale_n">N</label>
									</div>
								</div>
							</div>
						</div>
					</div> --}}
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="initSearch()"> -->
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
    var columns = [
        { headerName: "#", field: "num", type:'NumType', pinned:'left', aggSum:"합계", aggAvg:"평균", cellStyle: { 'text-align': "center" },
            cellRenderer: function (params) {
                if (params.node.rowPinned === 'top') {
                    return "합계";
                } else {
                    return parseInt(params.value) + 1;
                }
            }
        },
        { field: "store_cd", headerName: "매장코드", pinned:'left', width:70, cellStyle: { 'text-align': "center" } },
        { field: "store_type_nm", headerName: "매장구분", pinned:'left', width:90, cellStyle: { 'text-align': "center" } },
        { field: "store_nm", headerName: "매장명", pinned:'left', type: 'StoreNameType', width: 250 },
        // {field: "",	headerName: "TAG가"},
        { field: "sale_kind", headerName: "판매유형",
            children: [
				@foreach ($sale_kinds as $sale_kind)
					{ headerName: '{{ $sale_kind->code_val }}', field: 'sale_kind_{{ $sale_kind->code_id }}', type: 'currencyMinusColorType' },
				@endforeach
				{ headerName: "합계", field: "qty", type: 'currencyMinusColorType' }
            ]
        },
        { field: "sale_status", headerName: "판매현황",
            children: [
                { headerName: "단가", field: "wonga", type: 'numberType' },
                { headerName: "매출액", field: "amt", width: 90, type: 'currencyMinusColorType' },
                { headerName: "할인", field: "discount", width: 80, type: 'currencyMinusColorType' },
                { headerName: "결제금액", field: "recv_amt", type: 'currencyMinusColorType' }, // 판매금액 + 포인트 합친게 결제(주문) 금액.
                { headerName: "매장수수료", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType' }, // 0 처리
                { headerName: "중간관리수수료", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType' }, // 0 처리
            ]
        },
        { field: "sum_wonga", headerName: "원가", width: 80, type: 'currencyMinusColorType' },
        { field: "sales_profit", headerName: "매출이익", type: 'currencyMinusColorType' }, // 매출이익 = 결제금액 - 원가 합계금액
        { field: "profit_rate",	headerName: "이익율(%)", type:'percentType' }, // 매출이익 분의 매출액 = 이익율
        { headerName: "", field: "nvl", width: "auto" }
    ];

</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();
	});
	
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal06/search', data, -1);
	}
</script>
@stop
