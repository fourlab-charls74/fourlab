@extends('store_with.layouts.layout')
@section('title','판매유형관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">판매유형관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 코드관리</span>
    </div>
</div>

<form method="get" name="search" onsubmit="return false;">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
		            <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
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
                                <input type="text" id="sale_type_nm" name="sale_type_nm" class="form-control form-control-sm w-100 search-enter" />
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>기준금액</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sale_apply_A" name="sale_apply" value="" checked />
                                    <label class="custom-control-label" for="sale_apply_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sale_apply_P" name="sale_apply" value="price" />
                                    <label class="custom-control-label" for="sale_apply_P">판매가</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sale_apply_T" name="sale_apply" value="tag" />
                                    <label class="custom-control-label" for="sale_apply_T">정상가</label>
                                </div>
                            </div>
						</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>사용여부</label>
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
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
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
        {field: "sale_kind", headerName: "판매구분코드", pinned: "left", width: 90, cellStyle: {"text-align": "center"}},
        {field: "sale_kind_nm", headerName: "판매구분", pinned: "left", width: 120},
        {field: "sale_type_nm", headerName: "판매유형명", width: 150,
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='openPopup("${params.data.sale_type_cd}")'>${params.value}</a>`;
            }
        },
        {field: "sale_apply", headerName: "기준금액", width: 70, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                    return params.value === "price" ? "판매가" : params.value === "tag" ? "Tag가" : "";
        }},
        {field: "amt_kind", headerName: "적용구분", width: 70, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                    return params.value === "per" ? "할인율" : params.value === "amt" ? "할인액" : "";
        }},
        {field: "sale_amt", headerName: "할인율/액", width: 100, cellStyle: {"text-align": "right"},
            cellRenderer: function(params) {
                return params.data.amt_kind === 'per' ? Number.parseFloat(params.data.sale_per).toFixed(2) : Comma(params.value);
        }},
        {field: "use_yn", headerName: "사용여부", cellStyle: {"text-align": "center"}},
        {field: "store_cnt", headerName: "적용매장수", type: "numberType", cellStyle: {"text-align": "center"}},
        {field: "", headerName: "", width: "auto"}
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

    // 판매유형 검색
    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/standard/std05/search", data, -1);
    }

    // 검색조건 초기화
    function formReset(id) {
        document[id].reset();
    }

    // 등록/상세 팝업창 오픈
    function openPopup(sale_type_cd = '') {
        const url = "/store/standard/std05/show/" + sale_type_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=900,height=1200");
    }
</script>
@stop