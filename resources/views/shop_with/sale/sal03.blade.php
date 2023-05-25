@extends('shop_with.layouts.layout')
@section('title','기간별 Best/Worst 판매현황')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">기간별 Best/Worst 판매현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 경영관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
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
                        </div>
					</div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='prd_cd' value=''>
                            </div>
                        </div>
                    </div>
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
				</div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
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
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="goods_nm_eng">상품명(영문)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="formrow-email-input">조회 기준</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="best_worst" value="B" id="best" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="best">Best</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="best_worst" value="W" id="worst" class="custom-control-input">
                                    <label class="custom-control-label" for="worst">Worst</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="formrow-email-input">보기</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="group_type_condition" value="color_and_size" id="color_and_size" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="color_and_size">컬러, 사이즈</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="group_type_condition" value="online_code" id="online_code" class="custom-control-input">
                                    <label class="custom-control-label" for="online_code">온라인코드</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">순위/구분</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="10">10위</option>
                                        <option value="20">20위</option>
                                        <option value="30">30위</option>
                                        <option value="50">50위</option>
                                        <option value="100">100위</option>
                                        <option value="200">200위</option>
                                        <option value="300">300위</option>
                                        <option value="400">400위</option>
                                        <option value="500">500위</option>
                                        <option value="1000">1000위</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value='ord_qty'>수량</option>
                                        {{--<option value='sale_rate'>기간판매율</option>--}}
                                        <option value='ord_amt'>기간판매금액</option>
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

    const pinnedRowData = [{ 
                        prd_cd : '합계' 
                        , "total_ord_amt" : 0 
                        , "total_ord_qty" : 0
                        , "in_sum_qty": 0 
                        , "ord_amt" : 0 
                        , "ord_qty" : 0
                        , "in_sum_amt" : 0
                        , "stock_qty" : 0
                        , "stock_wqty" : 0
                        , "ex_sum_qty" : 0
                    }];

	var columns = [
		{headerName: '#', pinned: 'left', type: 'NumType', width: 40, cellStyle: StyleLineHeight,
            cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
        },
		{field: "prd_cd", headerName: "상품코드", width: 120, pinned: "left", cellStyle: StyleLineHeight},
		{
            field: "goods_no",
            headerName: "상품번호",
            hide: true,
            width: 58,
            pinned: 'left',
            cellStyle: StyleLineHeight,
            cellRenderer: function (params) {
                if (params.value) {
                    return `<a href="{{config('shop.front_url')}}/app/product/detail/${params.value}" target="_blank">${params.value}</a>`
                }
            }
        },
        {field: "goods_no", headerName: "온라인코드", cellStyle: StyleLineHeight, width: 70},
		{field: "brand_nm", headerName: "브랜드", cellStyle: StyleLineHeight, width: 70},
        {field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: StyleLineHeight},
		{field: "img", headerName: "이미지", type: 'GoodsImageType', width: 50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}"},
        {field: "img", headerName: "이미지_url", hide: true},
		{field: "goods_nm", headerName: "상품명", cellStyle: {"line-height": "30px"}, width: 150,
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
                    return '<a href="#" onclick="return openShopProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
		{field: "goods_nm_eng", headerName: "상품명(영문)", width: 150, cellStyle: {"line-height": "30px"}},
		{field: "prd_cd_p", headerName: "코드일련", cellStyle: StyleLineHeight, width: 90},
		{field: "color", headerName: "컬러", cellStyle: StyleLineHeight, width: 55},
		{field: "size", headerName: "사이즈", cellStyle: StyleLineHeight, width: 55},
		{field: "goods_opt", headerName: "옵션", cellStyle: {"line-height": "30px"}, width: 130},
        {field: "in_warehouse",	headerName: "입고",
            children: [
                {headerName: "수량", field: "in_sum_qty", type: 'numberType', width: 60},
                {headerName: "금액", field: "in_sum_amt", type: 'currencyMinusColorType', width: 70},
                {headerName: "판매율(%)", field: "in_sale_rate", cellStyle:{'text-align': 'right'}, type: 'currencyMinusColorType', width: 70}
            ]
        },
        {field: "ex_warehouse",	headerName: "출고",
            children: [
                {headerName: "수량", field: "ex_sum_qty", type: 'numberType', width: 60},
                {headerName: "최초출고일", field: "ex_date", width: 70, cellStyle: StyleLineHeight},
            ]
        },
        {field: "total_sale", headerName: "총판매",
            children: [
                {headerName: "수량", field: "total_ord_qty", type: 'numberType', width: 60},
                {headerName: "금액", field: "total_ord_amt", type: 'currencyMinusColorType', width: 70},
                {headerName: "판매율(%)", field: "total_sale_rate", cellStyle:{'text-align': 'right'}, type: 'currencyMinusColorType', width: 70}
            ]
        },
        {field: "sale",	headerName: "기간판매",
            children: [
                {headerName: "수량", field: "ord_qty", type: 'numberType', width: 60},
                {headerName: "금액", field: "ord_amt", type: 'currencyMinusColorType', width: 70},
                {headerName: "판매율(%)", field: "sale_rate", cellStyle:{'text-align': 'right'}, type: 'currencyMinusColorType', width: 70}
            ]
        },
        {field: "stock_qty", headerName: "매장재고", type: 'numberType', width: 60},
        {field: "stock_wqty", headerName: "창고재고", type: 'numberType', width: 60},
        {width: "auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
       
		gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
                if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
            },
        });
		Search();
	});
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/shop/sale/sal03/search', data, -1, function(e) {
            let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            let total_data = e.head.total_data;

            if(pinnedRow && total_data != '') {
				gx.gridOptions.api.setPinnedTopRowData([
					{ ...pinnedRow.data, ...total_data }
				]);
			}
        });
	}

    //상품범위검색 input창 클릭시 자동으로 상품옵션 범위검색 API 오픈
    function openApi() {
        document.getElementsByClassName('sch-prdcd-range')[0].click();
    }

    function openShopProduct(prd_no){
        var url = '/shop/product/prd01/' + prd_no;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }
</script>
@stop
