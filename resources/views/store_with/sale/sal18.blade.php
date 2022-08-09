@extends('store_with.layouts.layout')
@section('title','월별할인유형적용관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">월별할인유형적용관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 영업관리</span>
        <span>/ 월별할인유형적용관리</span>
    </div>
</div>

<style>
    @media (max-width: 740px) {
        #div-gd {height: 130px !important;}
    }
</style>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch()">검색조건 초기화</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>할인적용기간</label>
                            <div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ @$sdate }}" autocomplete="off">
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
							<label for="sale_kind">판매구분</label>
                            <div class="form-inline">
                                <select id="sale_kind" name="sale_kind" class="form-control form-control-sm w-100">
                                    <option value="">전체</option>
                                    @foreach ($sale_kinds as $sale_kind)
                                    <option value="{{ $sale_kind->code_id }}">
                                        {{ $sale_kind->code_val }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="sale_type_nm">판매유형명</label>
                            <div class="form-inline">
                                <input type="text" id="sale_type_nm" name="sale_type_nm" class="form-control form-control-sm w-100" />
                            </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a>
        </div>
    </div>
</form>

<div class="row show_layout">
    <div class="col-lg-3 pr-1">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0">
                <h5 class="m-0">판매유형목록</h5>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-0"><span id="select_store_nm"></span>매장목록</h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
					<div class="d-flex">
						<select id="store_type" name="store_type" class="form-control form-control-sm mr-1" style="width: 110px;">
							<option value="">== 선택 ==</option>
							@foreach ($store_types as $store_type)
							<option value="{{ $store_type->code_id }}">
								{{ $store_type->code_val }}
							</option>
							@endforeach
						</select>
						<button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="SearchDetail()"><i class="fas fa-search fa-sm text-white-50"></i> 조회</button>
					</div>
                    {{-- <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateCompetitors()"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="downlaodExcel()"><i class="fas fa-download fa-sm text-white-50 mr-1"></i> 엑셀다운로드</button>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="resetCompetitors()">전체 초기화</button> --}}
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-store-list" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
		{field: "chk", headerName: '적용', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 40},
        {field: "sale_kind", headerName: "판매구분", width: 60, cellStyle: {"text-align": "center"}},
        {field: "sale_type_cd", hide: true},
        {field: "sale_type_nm", headerName: "판매유형명", width: 130,
            // cellRenderer: function(params) {
            //     return `<a href='javascript:void(0)' onclick='openPopup("${params.data.sale_type_cd}")'>${params.value}</a>`;
            // }
        },
        {width: "auto"},
    ];

    let store_list_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
		{field: "store_type", hide: true},
		{field: "store_type_nm", headerName: "매장구분", width: 80, cellStyle: {"text-align": "center"}},
		{field: "store_cd", headerName: "매장코드", width: 80, cellStyle: {"text-align": "center"}},
		{field: "store_nm", headerName: "매장명", width: 200},
		{field: "this_month", headerName: "현월(%)", width: 60, type: "currencyType", editable: true, cellStyle: {"background-color": "#ffff99"}},
		{field: "last_month", headerName: "전월(%)", width: 60, type: "currencyType"},
		{field: "last_year", headerName: "전년(%)", width: 60, type: "currencyType"},
		{field: "comment", headerName: "메모", width: 300, editable: true, cellStyle: {"background-color": "#ffff99"}},
        {width: "auto"},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx, gx2;

    const pApp = new App('', { gridId: "#div-gd" });
    const pApp2 = new App('', { gridId: "#div-gd-store-list" });

    $(document).ready(function() {
        // 판매유형목록
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
			onFirstDataRendered: (params) => {
				params.api.forEachNode((node) => node.setSelected(false)); // db생성 후 작업예정
			},
		});

        // 매장목록
        pApp2.ResizeGrid(275);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, store_list_columns, {
            onCellValueChanged: (e) => {
                // e.node.data.use_yn = 'Y';
                // gx2.gridOptions.api.updateRowData({update: [e.node.data]});
            }
        });

        // 최초검색
        Search();
    });

	// 판매유형목록 조회
    // * 코드관리 > 판매유형관리 에 등록되지 않았거나, 사용여부가 "N"인 항목은 조회되지 않습니다.
    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/sale/sal18/search", data, -1, function(d) {
			SearchDetail();
            // if(cur_store_cd === "" && d.body.length > 0) {
            //     SearchDetail(d.body[0].store_cd, d.body[0].store_nm);
            // }
        });
    }

	// 매장목록 조회
	function SearchDetail() {
		let data = $('[name=search]').serialize();
		data += "&store_type=" + $("#store_type").val();
		gx2.Request("/store/sale/sal18/search-store", data, -1);
	}
</script>
@stop
