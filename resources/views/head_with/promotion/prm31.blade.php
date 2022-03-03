@extends('head_with.layouts.layout')
@section('title','검색 내역')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">검색 내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 검색 내역</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 검색일자 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">일자</label>
                            <div class="form-inline">
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

                    <!-- 검색어 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">검색어</label>
                            <div class="flax_box">
                                <input type="text" name="kwd" id="kwd" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <!-- 검색 상품수 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">검색 상품수</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm" name="sch_cnt_fr" value="">
                                    </div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm" name="sch_cnt_to" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">아이디</label>
                            <div class="flax_box">
                                <input type="text" name="user_id" id="user_id" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <!-- IP -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">IP</label>
                            <div class="flax_box">
                                <input type="text" name="ip" id="ip" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <!-- 검색어 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">자료/정렬순서</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="s.idx" selected>검색일시</option>
                                        <option value="s.kwd">검색어</option>
                                        <option value="s.ip">IP</option>
                                        <option value="s.sch_cnt">검색상품수</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
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
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box form-inline">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<script>
    const columns = [{
            field: "rt",
            headerName: "검색 일시",
            width: 130
        },
        {
            field: "kwd",
            headerName: "검색어",
            type: "SearchType",
            width: 130
        },
        {
            field: "qry",
            headerName: "검색 패턴",
            width: 130
        },
        {
            field: "synonym",
            headerName: "동의어",
            cellStyle: StyleEditCell,
            editable: true,
            width: 130,
            onCellValueChanged: function(p) {
                console.log(p);
                if (confirm("해당 내용으로 변경하시겠습니까?") === false) return;

                $.ajax({
                    async: true,
                    type: 'put',
                    url: '/head/promotion/prm30/synonym',
                    data: {
                        kwd: p.data.kwd,
                        synonym: p.newValue
                    },
                    success: function(data) {
                        alert("변경되었습니다.");
                        Search();
                    },
                    error: function(request, status, error) {
                        console.log("error")
                    }
                });
            }
        },
        {
            field: "pv_1m",
            headerName: "검색횟수(한달)"
        },
        {
            field: "sch_cnt",
            headerName: "검색 상품수"
        },
        {
            field: "ip",
            headerName: "IP",
            width: 100
        },
        {
            field: "vid",
            headerName: "방문자 식별번호"
        },
        {
            field: "user_id",
            headerName: "아이디"
        },
        {
            field: "d_cat_cd",
            headerName: "카테고리"
        },
        {
            field: "price_from",
            headerName: "상품가격(FROM)"
        },
        {
            field: "price_to",
            headerName: "상품가격(TO)"
        },
        {
            field: "item",
            headerName: "품목"
        },
        {
            field: "brand",
            headerName: "브랜드"
        },
        {
            field: "color",
            headerName: "색상"
        },
        {
            field: "sort",
            headerName: "정렬"
        },
    ];

    const pApp = new App('', {
        gridId: "#div-gd"
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    pApp.ResizeGrid(265);

    const Search = () => {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm31/search', data, 1);
    }

    Search();
</script>
@stop