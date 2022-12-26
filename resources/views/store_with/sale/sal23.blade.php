@extends('store_with.layouts.layout')
@section('title','본사수불집계표')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">본사수불집계표</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
        <span>/ 영업관리</span>
		<span>/ 본사수불집계표</span>
	</div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch(['#store_no'])">검색조건 초기화</a>
                    <a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>조회기간</label>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
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
                            <label for="prd_cd">상품옵션 범위검색</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 ac-style-no sch-prdcd-range" readonly style="background-color: #fff;">
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
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                        <option value="5000">5000</option>
                                        <option value="-1">전체</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
										<option value="pc.rt">등록일</option>
										<option value="pc.prd_cd">상품코드</option>
										<option value="pc.goods_no">상품번호</option>
										<option value="g.goods_nm">상품명</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-secondary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-primary primary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc">
                                    <input type="radio" name="ord" id="sort_asc" value="asc" checked="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
        </div>
    </form>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<style>
    .ag-row-level-1 {background-color: #edf4fd !important;}
</style>

<script language="javascript">
    const pinnedRowData = [{ prd_cd: '합계' }];
    let AlignCenter = {"text-align": "center"};

    let columns = [
        {field: "prd_cd_p", headerName: "코드일련", rowGroup: true, hide: true},
        {headerName: '코드일련', showRowGroup: 'prd_cd_p', pinned: "left", cellRenderer: 'agGroupCellRenderer', minWidth: 150},
        {field: "prd_cd", headerName: "상품코드", pinned: "left", width: 130, cellStyle: AlignCenter},
        {field: "goods_no", headerName: "상품번호", pinned: "left", width: 70, cellStyle: AlignCenter, aggFunc: "first"},
        {field: "goods_nm", headerName: "상품명", width: 150, aggFunc: "first"},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150, aggFunc: "first"},
        {field: "color", headerName: "컬러", width: 55, cellStyle: AlignCenter},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: AlignCenter},
        {field: "goods_opt", headerName: "옵션", width: 120},
        {field: "tag_price", headerName: "TAG가", width: 60, type: "currencyType", aggFunc: "first"},
        {field: "price", headerName: "판매가", width: 60, type: "currencyType", aggFunc: "first"},
        {field: "wonga", headerName: "원가", width: 60, type: "currencyType", aggFunc: "first"},
        {
            headerName: "이전재고",
            children: [
                {field: "prev_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "prev_tag_price", headerName: "TAG가 합계", width: 90, type: "currencyType"},
                {field: "prev_price", headerName: "판매가 합계", width: 90, type: "currencyType"},
                {field: "prev_wonga", headerName: "원가 합계", width: 90, type: "currencyType"},
            ]
        },
        {
            headerName: "기간입고",
            children: [
                {field: "stock_in_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "stock_in_tag_price", headerName: "TAG가 합계", width: 90, type: "currencyType"},
                {field: "stock_in_price", headerName: "판매가 합계", width: 90, type: "currencyType"},
                {field: "stock_in_wonga", headerName: "원가 합계", width: 90, type: "currencyType"},
            ]
        },
        {
            headerName: "기간반품",
            children: [
                {field: "stock_return_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "stock_return_tag_price", headerName: "TAG가 합계", width: 90, type: "currencyType"},
                {field: "stock_return_price", headerName: "판매가 합계", width: 90, type: "currencyType"},
                {field: "stock_return_wonga", headerName: "원가 합계", width: 90, type: "currencyType"},
            ]
        },
        {
            headerName: "판매",
            children: [
                {field: "sale_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "sale_tag_price", headerName: "TAG가 합계", width: 90, type: "currencyType"},
                {field: "sale_price", headerName: "판매가 합계", width: 90, type: "currencyType"},
                {field: "sale_wonga", headerName: "원가 합계", width: 90, type: "currencyType"},
            ]
        },
        {
            headerName: "LOSS",
            children: [
                {field: "loss_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "loss_tag_price", headerName: "TAG가 합계", width: 90, type: "currencyType"},
                {field: "loss_price", headerName: "판매가 합계", width: 90, type: "currencyType"},
                {field: "loss_wonga", headerName: "원가 합계", width: 90, type: "currencyType"},
            ]
        },
        {
            headerName: "기간재고",
            children: [
                {field: "term_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "term_tag_price", headerName: "TAG가 합계", width: 90, type: "currencyType"},
                {field: "term_price", headerName: "판매가 합계", width: 90, type: "currencyType"},
                {field: "term_wonga", headerName: "원가 합계", width: 90, type: "currencyType"},
            ]
        },
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
			groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
        });

        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal23/search', data, 1, function(d) {
            // let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            // let total_data = d.head.total_data;
			// if(pinnedRow && total_data != '') {
			// 	gx.gridOptions.api.setPinnedTopRowData([
			// 		{ ...pinnedRow.data, ...total_data }
			// 	]);
			// }
        });
	}
</script>
@stop
