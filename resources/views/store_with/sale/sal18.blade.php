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
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch(['#store_no'])">검색조건 초기화</a> -->
                    <a href="javascript:void(0);" onclick="return Save();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
					<a href="javascript:void(0);" onclick="return moveToStk25()" class="btn btn-sm btn-outline-primary shadow-sm pl-2">매장별할인율적용조회</a>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>매장명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch()">검색조건 초기화</a> -->
            <a href="javascript:void(0);" onclick="return Save();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            <a href="javascript:void(0);" onclick="return moveToStk25()" class="btn btn-sm btn-outline-primary shadow-sm pl-2">매장별할인율적용조회</a>
        </div>
    </div>
</form>

<div class="row show_layout">
    <div class="col-lg-10 pr-1">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-0"><span id="select_store_nm"></span>매장목록</h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
                    <input type='text' class="form-control form-control-sm mr-1" name='batch_apply' value='15' style="width: 60px;">
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="applyRate('batch')">일괄적용</button>
                    <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2" onclick="applyRate('last-month')">전월적용</button>
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-store-list" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="card shadow-none mb-0">
            <div class="card-header mt-1 mb-2">
                <h5 class="m-0">판매유형목록</h5>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
        {field: "sale_type_cd", hide: true},
		{field: "chk", headerName: '적용', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 40},
        {field: "sale_kind", headerName: "판매구분", width: 60, cellStyle: {"text-align": "center"}},
        {field: "sale_type_nm", headerName: "판매유형명", width: 'auto'},
        {field: "apply_date", hide: true},
        {field: "apply_yn", hide: true},
    ];
    
    let store_list_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
		{field: "store_type", hide: true},
		{field: "store_channel", headerName: "판매채널", width: 80, cellStyle: {"text-align": "center"}},
		{field: "store_channel_kind", headerName: "매장구분", width: 80, cellStyle: {"text-align": "center"}},
		{field: "store_cd", headerName: "매장코드", width: 80, cellStyle: {"text-align": "center"}},
		{field: "store_nm", headerName: "매장명", width: 200,
            cellRenderer: function(params) {
                return `<a href="/store/stock/stk25?store_cd=${params.data.store_cd}&sale_month=${$("[name=sdate]").val()}">${params.value}</a>`;
            }
        },
		{field: "this_month_rate", headerName: "현월(%)", width: 60, type: "currencyType", editable: true, cellStyle: {"background-color": "#ffff99"}},
		{field: "last_month_rate", headerName: "전월(%)", width: 60, type: "currencyType"},
		{field: "last_year_rate", headerName: "전년(%)", width: 60, type: "currencyType"},
		{field: "comment", headerName: "메모", width: 300, editable: true, cellStyle: {"background-color": "#ffff99"}},
        {field: "apply_date", hide: true},
        {width: "auto"},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx, gx2;

    const pApp = new App('', { gridId: "#div-gd" });
    const pApp2 = new App('', { gridId: "#div-gd-store-list" });

    let original_sale_type_apply = [];
    let changed_sale_type_apply = [];

    $(document).ready(function() {
        // 판매유형목록
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onSelectionChanged: (e) => {
                e.api.forEachNode(node => node.data.apply_yn = node.selected ? 'Y' : 'N');
            }
        });

        // 매장목록
        pApp2.ResizeGrid(275);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, store_list_columns, {
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
                if (e.column.colId == "this_month_rate") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx2.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    }
                }
            }
        });

        // 최초검색
        Search();

        // 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
        load_store_channel();
    });

    // 매장목록 조회
    function Search() {
		let data = $('[name=search]').serialize();
		gx2.Request("/store/sale/sal18/search-store", data, -1, function(d) {
            SearchDetail();
        });
    }

	// 판매유형목록 조회
    // * 코드관리 > 판매유형관리 에 등록되지 않았거나, 사용여부가 "N"인 항목은 조회되지 않습니다.
	function SearchDetail() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/sale/sal18/search", data, -1, function(d) {
            original_sale_type_apply = JSON.parse(JSON.stringify(d.body));
            gx.gridOptions.api.forEachNode((node) => node.setSelected(node.data.apply_yn === 'Y'));
        });
	}

    // 일괄적용 & 전월적용
    function applyRate(type) {
        if(type === 'batch') {
            const per = $("[name=batch_apply]").val();
            gx2.gridOptions.api.forEachNode((node) => {
                node.data.this_month_rate = per;
                node.setSelected(true);
            });
        } else if(type === 'last-month') {
            gx2.gridOptions.api.forEachNode((node) => {
                node.data.this_month_rate = node.data.last_month_rate;
                node.setSelected(true);
            });
        }
        
        gx2.gridOptions.api.updateRowData({update: gx2.getRows()});
    }

    // 변경정보 저장
    function Save() {
        if(!confirm("변경내역을 저장하시겠습니까?")) return;

        changed_sale_type_apply = gx.getRows();
        changed_sale_type_apply = changed_sale_type_apply.filter((c,i) => c.apply_yn != original_sale_type_apply[i].apply_yn);

        axios({
            url: '/store/sale/sal18/save',
            method: 'post',
            data: {
                sale_types: changed_sale_type_apply,
                sale_type_stores: gx2.getSelectedRows(),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 매장별할인율적용조회 페이지로 이동
    function moveToStk25() {
        location.href = '/store/stock/stk25?sale_month=' + $("[name=sdate]").val();
    }
</script>
@stop
