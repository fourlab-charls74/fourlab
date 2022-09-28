@extends('store_with.layouts.layout')
@section('title','매장수불집계표')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장수불집계표</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
        <span>/ 영업관리</span>
		<span>/ 매장수불집계표</span>
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
                            <label for="store_type">매장구분</label>
                            <div class="flex_box">
                                <select name='store_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach (@$store_types as $store_type)
                                        <option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>매장</label>
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
                            <label>매장폐점여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="close_yn_N" name="close_yn" value="N" checked />
                                    <label class="custom-control-label" for="close_yn_N">폐점제외</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="close_yn_Y" name="close_yn" value="Y"/>
                                    <label class="custom-control-label" for="close_yn_Y">폐점만</label>
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
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                        <option value="5000">5000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="p.store_cd">매장코드</option>
                                        <option value="p.prd_cd">상품코드</option>
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

<script language="javascript">
    const pinnedRowData = [{ store_cd: '합계' }];

    let AlignCenter = {"text-align": "center"};
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: AlignCenter},
        {field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 60, cellStyle: AlignCenter},
        {field: "store_nm",	headerName: "매장명", pinned: 'left', width: 130},
        {field: "goods_no", headerName: "상품번호", pinned: 'left', width: 60, cellStyle: AlignCenter},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: AlignCenter},
        {field: "prd_cd_sm", headerName: "상품코드", pinned: 'left', width: 100, cellStyle: AlignCenter},
        {field: "color", headerName: "컬러", pinned: 'left', width: 50, cellStyle: AlignCenter},
        {field: "size", headerName: "사이즈", pinned: 'left', width: 50, cellStyle: AlignCenter},
        {field: "goods_nm", headerName: "상품명", width: 200},
        {field: "goods_opt", headerName: "옵션", width: 150},
        {field: "brand_nm", headerName: "브랜드", width: 60, cellStyle: AlignCenter},
        {field: "sale_stat_cl", headerName: "상품상태", width: 60, cellStyle: StyleGoodsState},
        {field: "goods_sh", headerName: "TAG가", width: 80, type: "currencyType"},
        {field: "price", headerName: "판매가", width: 80, type: "currencyType"},
        {field: "wonga", headerName: "원가", width: 80, type: "currencyType"},
        {
            headerName: "이전재고",
            children: [
                {field: "prev_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "prev_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "prev_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "prev_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "매장입고",
            children: [
                {field: "store_in_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "store_in_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "store_in_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "store_in_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "매장반품",
            children: [
                {field: "store_return_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "store_return_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "store_return_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "store_return_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "이동입고",
            children: [
                {field: "rt_in_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "rt_in_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "rt_in_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "rt_in_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "이동출고",
            children: [
                {field: "rt_out_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "rt_out_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "rt_out_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "rt_out_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "매장판매",
            children: [
                {field: "sale_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "sale_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "sale_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "sale_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "LOSS",
            children: [
                {field: "loss_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "loss_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "loss_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "loss_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
            ]
        },
        {
            headerName: "기간재고",
            children: [
                {field: "term_qty", headerName: "수량", width: 50, type: "currencyType"},
                {field: "term_sh", headerName: "TAG금액", width: 80, type: "currencyType"},
                {field: "term_price", headerName: "판매가금액", width: 80, type: "currencyType"},
                {field: "term_wonga", headerName: "원가금액", width: 80, type: "currencyType"},
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
        });

        Search();

        // 매장검색
        $( ".sch-store" ).on("click", function() {
            searchStore.Open(null, "multiple");
        });
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal21/search', data, 1, function(d) {
            let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            let total_data = d.head.total_data;
			if(pinnedRow && total_data != '') {
				gx.gridOptions.api.setPinnedTopRowData([
					{ ...pinnedRow.data, ...total_data }
				]);
			}
        });
	}
</script>
@stop
