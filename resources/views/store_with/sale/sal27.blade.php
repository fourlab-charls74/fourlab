@extends('store_with.layouts.layout')
@section('title','품번별종합분석현황')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">품번별종합분석현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 경영관리</span>
		<span>/ 품번별종합분석현황</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">일자 / 기간</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" id="date" name="date" value="{{ $date }}" autocomplete="off" disable>
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
												<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
                                <span class="text_line">/</span>
                                <select id="dlv_type" name='dlv_type' class="form-control form-control-sm" style="width: 47%;">
                                    <option value='1'>1개월</option>
                                    <option value='3'>3개월</option>
                                    <option value='6'>6개월</option>
                                    <option value='9'>9개월</option>
                                    <option value='12'>12개월</option>
                                   
                                </select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm w-100 ac-style-no search-enter">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>매장명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품옵션 범위검색</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">창고검색조건</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="g.goods_no">상품번호</option>
                                        <option value="g.goods_nm">상품명</option>
                                        <option value="pc.prd_cd">상품코드</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="formReset()">
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
<style>
    .hd-grid-red {
        color: red;
    }
</style>
<script type="text/javascript" charset="utf-8">

    const pinnedRowData = [{ brand: 'total' }];
    const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0);

	let columns = [
        { headerName: "#", field: "num", type:'NumType', pinned:'left', aggSum:"합계", cellStyle: { 'text-align': "center" },
            cellRenderer: function (params) {
                if (params.node.rowPinned === 'top') {
                    return "";
                } else {
                    return parseInt(params.value) + 1;
                }
            }
        },
        { field: "item", headerName: "품목", pinned:'left', width:50, cellStyle: { 'text-align': "center" },
			cellRenderer: function (params) {
					if (params.node.rowPinned === 'top') {
						return "합계";
					} else {
						return params.data.item
					}
				},
		},
        { field: "item_nm", headerName: "품목명", width:80, pinned:'left'},
        { field: "brand_nm", headerName: "브랜드명", width:90},
        { field: "prd_cd", headerName: "상품코드", width:120},
        { field: "style_no", headerName: "스타일넘버", width:80},
        { field: "goods_nm", headerName: "상품명", width:250},
        { field: "goods_nm_eng", headerName: "상품명(영문)", width:250},
        { field: "color", headerName: "컬러", width:50},
        { field: "color_nm", headerName: "컬러명", width:80},
        { field: "size", headerName: "사이즈", width:50},
        { field: "goods_opt", headerName: "옵션", width:100},
        { field: "tag_price", headerName: "Tag가", width:80, type: 'currencyType'},
        { field: "price", headerName: "현재가", width:80, type: 'currencyType'},
        { field: "wonga", headerName: "원가", width:80, type: 'currencyType'},
        { field: "order_amt", headerName: "발주량", width:100},
        {
            headerName: "입고",
            children: [
                {field: "order_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "order_tag_price", headerName: "TAG가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "order_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "order_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        {
            headerName: "출고",
            children: [
                {field: "release_first_date", headerName: "최초출고일", width: 100, aggFunc: sumValuesFunc},
                {field: "release", headerName: "출고", width: 60, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "return", headerName: "반품", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "total_release", headerName: "총출고", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        {
            headerName: "판매",
            children: [
                {field: "sale_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_tag_price", headerName: "TAG가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_recv_amt", headerName: "실판매가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sa_rate", headerName: "판매율", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_discount_rate", headerName: "할인율", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        {
            headerName: "재고",
            children: [
                {field: "storage_stock_qty", headerName: "창고", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "store_stock_qty", headerName: "매장", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "total_stock_qty", headerName: "총재고", width: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
       
        { field: "", headerName: "", width: "auto"}
    ];

	
	const pApp = new App('',{
		gridId:"#div-gd",
	});

	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		let options = {
			getRowStyle: (params) => params.node.rowPinned ? ({'font-weight': 'bold', 'background-color': '#eee', 'border': 'none'}) : false,
		}
		gx = new HDGrid(gridDiv, columns, options);
        Search();
	});

	function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ sum: "top" });
        gx.Request('/store/sale/sal27/search', data, -1);
	}

    // 검색조건 초기화
    const formReset = () => {
		document.search.reset();
	};

    //상품옵션 범위검색 인풋박스 클릭시 API 오픈
    function openApi() {
        document.getElementsByClassName('sch-prdcd-range')[0].click();
    }



</script>
@stop
