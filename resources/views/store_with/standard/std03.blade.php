@extends('store_with.layouts.layout')
@section('title','창고관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">창고관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 코드관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 등록</a>
		            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="storage_cd">창고코드</label>
                            <div class="form-inline">
                                <input type="text" id="storage_cd" name="storage_cd" class="form-control form-control-sm w-100 search-enter" />
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="storage_nm">창고명</label>
                            <div class="form-inline">
                                <input type="text" id="storage_nm" name="storage_nm" class="form-control form-control-sm w-100 search-enter" />
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>창고사용</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_A" name="use_yn" value="" checked />
                                    <label class="custom-control-label" for="use_yn_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_Y" name="use_yn" value="Y" />
                                    <label class="custom-control-label" for="use_yn_Y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_N" name="use_yn" value="N" />
                                    <label class="custom-control-label" for="use_yn_N">N</label>
                                </div>
                            </div>
						</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>매장조회여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="stock_check_yn_A" name="stock_check_yn" value="" checked />
                                    <label class="custom-control-label" for="stock_check_yn_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="stock_check_yn_Y" name="stock_check_yn" value="Y" />
                                    <label class="custom-control-label" for="stock_check_yn_Y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="stock_check_yn_N" name="stock_check_yn" value="N" />
                                    <label class="custom-control-label" for="stock_check_yn_N">N</label>
                                </div>
                            </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box"></div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"}},
        {field: "storage_cd", headerName: "창고코드", pinned: "left", width: 100},
        {field: "storage_nm", headerName: "창고명", width: 200,
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='openPopup("${params.data.storage_cd}")'>${params.value}</a>`;
            }
        },
        {field: "phone", headerName: "전화번호", width: 120, cellStyle: {"text-align": "center"}},
        {field: "use_yn", headerName: "창고사용", cellStyle: {"text-align": "center"}},
        {field: "stock_check_yn", headerName: "매장조회여부", cellStyle: {"text-align": "center"}},
        {field: "default_yn", headerName: "대표창고",
            cellStyle: (params) => ({"text-align": "center", "background-color": params.value === "Y" ? "#FFACAC" : "none"}),
        },
        {field: "online_yn", headerName: "온라인창고",
            cellStyle: (params) => ({"text-align": "center", "background-color": params.value === "Y" ? "#FFACAC" : "none"}),
        },
        {field: "comment", headerName: "설명", width: 300},
        {width: "auto"}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    // 창고리스트 검색
    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/standard/std03/search", data, -1);
    }

    // 검색조건 초기화
    function formReset(id) {
        document[id].reset();
    }

    // 등록/상세 팝업창 오픈
    function openPopup(storage_cd = '') {
        const url = "/store/standard/std03/show/" + storage_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=900,height=640");
    }
</script>
@stop