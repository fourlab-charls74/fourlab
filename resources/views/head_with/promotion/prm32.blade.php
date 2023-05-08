@extends('head_with.layouts.layout')
@section('title','검색 바로가기')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">검색 바로가기</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 검색 바로가기</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="javascript:;" onclick="openSchDetail()" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 키워드 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">키워드</label>
                            <div class="flax_box">
                                <input type="text" name="kwd" id="kwd" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>

                    <!-- 검색 상품수 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">사용여부</label>
                            <div class="flax_box">
                                <select name="use_yn" class="form-control form-control-sm">
                                    <option value="">선택</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
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
    const columns = [
        {field:"idx" , headerName:"번호", cellStyle:{'text-align' : 'center'}},
        {field:"kwd",headerName:"검색어",width:130, type:"SearchDetailType", cellStyle:{'text-align' : 'center'}},
        {field:"url" , headerName:"URL", width:250},
        {field:"disp_yn",headerName:"검색창출력", cellStyle:{'text-align' : 'center'}},
        {field:"pv" , headerName:"검색횟수", type:"currencyType"},
        {field:"st" , headerName:"최근검색일시", width:130},
        {field:"use_yn", headerName:"사용여부", cellStyle:{'text-align' : 'center'}},
        {field:"rt", headerName:"등록일시", width:130, cellStyle:{'text-align' : 'center'}},
        {field:"ut" , headerName:"수정일시", width:130, cellStyle:{'text-align' : 'center'}},
        { width: "auto" }
    ];
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm32/search', data, 1);
    }
</script>
@stop
